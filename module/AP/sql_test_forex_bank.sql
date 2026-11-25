-- =====================================================================
-- UJI MANUAL: REVALUASI SELISIH KURS BANK / KAS  (COA lawan 8.52.01)
-- =====================================================================
-- Query ini MENIRU persis fxSkCalcRevaluation() di fxsk_functions.php,
-- yang dijalankan tiap malam oleh sync_selisih_kurs_scheduler.php.
-- Gunanya cuma untuk MEMERIKSA angka dengan tangan - query ini TIDAK
-- menulis apa pun ke database, aman dijalankan kapan saja.
--
-- CARA PAKAI
--   1. Ganti 3 nilai di blok SET di bawah (akun, tanggal mulai, sampai).
--   2. Jalankan seluruh berkas dalam SATU koneksi.
--   3. Bandingkan kolom "selisih" dengan baris keluaran scheduler:
--        FX {akun} {tanggal} selisih={angka} -> {dokumen} (insert|update)
--
-- RUMUSNYA (lihat komentar besar di fxsk_functions.php):
--   saldo_usd(D)   = saldo awal bank + semua mutasi s/d D
--   nilai_pasar(D) = saldo_usd(D) x kurs HARIAN tanggal D
--   nilai_buku(D)  = nilai_pasar(D-1) + mutasi IDR tanggal D
--   selisih(D)     = nilai_pasar(D) - nilai_buku(D)
--
-- Karena nilai buku pembuka hari ini = nilai pasar kemarin (crystallize
-- harian), rumusnya cuma bergantung pada D dan D-1 - jadi tidak perlu
-- loop, cukup LAG(). Itu sebabnya query ini bisa satu tembakan.
--
-- CATATAN PENTING
--   * Mutasi IDR diambil dari JURNALNYA (tbl_list_journal baris COA bank
--     itu), BUKAN dari kurs master - karena pemakai bisa mengisi kurs
--     manual saat posting Bank Out. Kalau jurnalnya belum ada, dipakai
--     cadangan net x kurs PAJAK, sama seperti di PHP.
--   * Baris no_doc 'FX/%' DIKECUALIKAN - itu hasil jurnal tool ini
--     sendiri (curr IDR). Kalau ikut dihitung, angkanya akan dimakan
--     sendiri setiap kali dijalankan ulang.
--   * Kolom "periode" cuma penanda. Scheduler TIDAK menjurnal tanggal
--     yang periodenya Closed, tapi saldonya tetap jalan melewatinya.
--   * Per 24 Nov 2026 hanya ada 2 rekening USD (BCA 008-998-1982 NAG dan
--     BCA 008-751-5757 NAK), keduanya COA 1.10.xx alias BANK. BELUM ADA
--     akun KAS valas; begitu ada dan terdaftar di b_masterbank dengan
--     curr = 'USD' dan status = 'Active', akun itu otomatis ikut - query
--     ini maupun scheduler-nya tidak membedakan bank dan kas.
-- =====================================================================


-- ----------------------------------------------------------- parameter --
SET @akun   := '008-998-1982';   -- bank_account di b_masterbank
SET @mulai  := '2026-08-01';     -- = FXSK_MIN_DATE, batas bawah keras
SET @sampai := CURDATE();        -- scheduler selalu s/d HARI INI

SET @d0 := DATE_SUB(CAST(@mulai AS DATE), INTERVAL 1 DAY);   -- hari pembuka
SET @coa_bank := (SELECT id_coa FROM b_masterbank WHERE bank_account = @akun LIMIT 1);


-- -------------------------------- 1. daftar akun yang ikut direvaluasi --
-- Sama dengan fxSkGetUsdAccounts(). Jalankan ini dulu untuk tahu akun apa
-- saja yang diproses scheduler tiap malam.
SELECT bank_account, bank_name, b_code, id_coa, coa_name, profit_center_bank
  FROM b_masterbank
 WHERE status = 'Active' AND curr = 'USD'
 ORDER BY bank_account;


-- -------------------------------------- 2. perhitungan harian per akun --
WITH RECURSIVE

-- Setiap tanggal kalender, bukan cuma yang ada transaksinya: saldo bisa
-- berubah nilai murni karena kurs HARIAN bergerak.
kalender AS (
    SELECT CAST(@d0 AS DATE) AS tgl
    UNION ALL
    SELECT tgl + INTERVAL 1 DAY FROM kalender WHERE tgl < CAST(@sampai AS DATE)
),

-- Nilai IDR per dokumen menurut jurnalnya (kurs asli/manual yang dipakai
-- saat posting). Satu dokumen normalnya cuma punya 1 baris COA bank.
jurnal_bank AS (
    SELECT no_journal, SUM(debit_idr - credit_idr) AS idr
      FROM tbl_list_journal
     WHERE no_coa = @coa_bank AND status <> 'Cancel'
     GROUP BY no_journal
),

mutasi_baris AS (
    SELECT r.transaksi_date AS tgl,
           (r.debit - r.credit) AS net_usd,
           COALESCE(
               j.idr,
               (r.debit - r.credit) *
               (SELECT m.rate FROM masterrate m
                 WHERE m.v_codecurr = 'PAJAK' AND m.curr = 'USD'
                   AND m.tanggal <= r.transaksi_date
                 ORDER BY m.tanggal DESC LIMIT 1)
           ) AS net_idr
      FROM b_reportbank r
      LEFT JOIN jurnal_bank j ON j.no_journal = r.no_doc
     WHERE r.akun = @akun
       AND r.status <> 'Cancel'
       AND r.no_doc NOT LIKE 'FX/%'
       AND r.transaksi_date BETWEEN CAST(@d0 AS DATE) AND CAST(@sampai AS DATE)
),

mutasi AS (
    SELECT tgl, SUM(net_usd) AS net_usd, SUM(net_idr) AS net_idr
      FROM mutasi_baris GROUP BY tgl
),

-- Saldo USD pada hari pembuka: saldo awal bank + seluruh mutasi SEBELUM
-- hari pembuka. Mutasi yang jatuh PADA hari pembuka ditambahkan lewat
-- running total di bawah, jadi totalnya = semua mutasi < tanggal mulai.
awal AS (
    SELECT (SELECT COALESCE(SUM(amount), 0) FROM b_saldoawal_bank WHERE account = @akun)
         + (SELECT COALESCE(SUM(r.debit - r.credit), 0) FROM b_reportbank r
             WHERE r.akun = @akun AND r.status <> 'Cancel'
               AND r.no_doc NOT LIKE 'FX/%'
               AND r.transaksi_date < CAST(@d0 AS DATE)) AS usd0
),

jalan AS (
    SELECT k.tgl,
           COALESCE(mu.net_idr, 0) AS mutasi_idr,
           a.usd0 + SUM(COALESCE(mu.net_usd, 0)) OVER (ORDER BY k.tgl) AS saldo_usd,
           COALESCE((SELECT m.rate FROM masterrate m
                      WHERE m.v_codecurr = 'HARIAN' AND m.curr = 'USD'
                        AND m.tanggal <= k.tgl
                      ORDER BY m.tanggal DESC LIMIT 1), 1) AS kurs_harian
      FROM kalender k
      CROSS JOIN awal a
      LEFT JOIN mutasi mu ON mu.tgl = k.tgl
),

hitung AS (
    SELECT tgl, saldo_usd, kurs_harian, mutasi_idr,
           saldo_usd * kurs_harian AS pasar,
           LAG(saldo_usd * kurs_harian) OVER (ORDER BY tgl) AS pasar_kemarin
      FROM jalan
)

SELECT h.tgl                                              AS tanggal,
       ROUND(h.saldo_usd, 2)                              AS saldo_usd,
       h.kurs_harian,
       ROUND(h.mutasi_idr, 2)                             AS mutasi_idr,
       ROUND(h.pasar_kemarin + h.mutasi_idr, 2)           AS nilai_buku,
       ROUND(h.pasar, 2)                                  AS nilai_pasar,
       ROUND(h.pasar - (h.pasar_kemarin + h.mutasi_idr), 2) AS selisih,
       CASE WHEN cp.status_closing = 'Closed' THEN 'CLOSED - tidak dijurnal'
            ELSE 'Open' END                               AS periode,
       (SELECT b.no_doc FROM b_reportbank b
         WHERE b.akun = @akun AND b.transaksi_date = h.tgl
           AND b.no_doc LIKE 'FX/%' AND b.status <> 'Cancel' LIMIT 1) AS dokumen_ada
  FROM hitung h
  LEFT JOIN tbl_closing_periode cp
         ON cp.bulan = LPAD(MONTH(h.tgl), 2, '0') AND cp.tahun = YEAR(h.tgl)
 WHERE h.tgl >= CAST(@mulai AS DATE)
   AND ROUND(h.pasar - (h.pasar_kemarin + h.mutasi_idr), 2) <> 0
 ORDER BY h.tgl;


-- ------------------------------------- 3. jurnal yang sudah ada di buku --
-- Untuk membandingkan hasil hitungan di atas dengan yang benar-benar
-- tersimpan. Selisih kurs bank disimpan di b_reportbank + tbl_list_journal.
SELECT j.no_journal, j.tgl_journal, j.no_coa, j.nama_coa,
       j.debit_idr, j.credit_idr, j.profit_center, j.status, j.create_by
  FROM tbl_list_journal j
 WHERE j.no_journal LIKE 'FX/%'
   AND j.tgl_journal BETWEEN CAST(@mulai AS DATE) AND CAST(@sampai AS DATE)
 ORDER BY j.tgl_journal, j.no_journal, j.no_coa;


-- ------------------------------------------------ 4. pemeriksaan bantu --
-- (a) Dokumen bank yang punya LEBIH DARI SATU baris COA bank di jurnalnya.
--     Kalau ada isinya, angka "mutasi_idr" di atas bisa beda sedikit dari
--     PHP: PHP memakai baris TERAKHIR, query ini menjumlahkan.
SELECT no_journal, COUNT(*) AS jml_baris_coa_bank
  FROM tbl_list_journal
 WHERE no_coa = @coa_bank AND status <> 'Cancel'
 GROUP BY no_journal HAVING COUNT(*) > 1;

-- (b) Transaksi bank yang BELUM ada jurnalnya - nilai bukunya memakai
--     cadangan (net x kurs PAJAK), jadi wajar kalau beda dari GL.
SELECT r.transaksi_date, r.no_doc, r.debit, r.credit
  FROM b_reportbank r
  LEFT JOIN (SELECT DISTINCT no_journal FROM tbl_list_journal
              WHERE no_coa = @coa_bank AND status <> 'Cancel') j
         ON j.no_journal = r.no_doc
 WHERE r.akun = @akun AND r.status <> 'Cancel'
   AND r.no_doc NOT LIKE 'FX/%'
   AND r.transaksi_date BETWEEN CAST(@mulai AS DATE) AND CAST(@sampai AS DATE)
   AND j.no_journal IS NULL
 ORDER BY r.transaksi_date;

-- (c) Kurs yang dipakai: HARIAN untuk menilai saldo, PAJAK untuk cadangan.
SELECT tanggal, v_codecurr, rate
  FROM masterrate
 WHERE curr = 'USD' AND v_codecurr IN ('HARIAN', 'PAJAK')
   AND tanggal BETWEEN DATE_SUB(CAST(@mulai AS DATE), INTERVAL 10 DAY) AND CAST(@sampai AS DATE)
 ORDER BY tanggal, v_codecurr;
