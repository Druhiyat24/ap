-- ===========================================================================
-- PV-AP/REG/NAG/2026/08/02088 : jurnal AP - Kontrabon TIDAK TERSIMPAN
--
-- Dokumen ini termasuk yang tersimpan DUA KALI (simpan-dobel, lihat
-- fix_potongan_ganda.sql). User melaporkan jurnalnya tidak ada di
-- tbl_list_journal. Skrip ini MEMBANGUN ULANG jurnal dari data PV-nya sendiri
-- (kontrabon_h, kontrabon, potongan, return_kb, kontrabon_ftr) dengan logika
-- yang SAMA dengan insertkbon_core.php + pv_ppn_group_faktur.php:
--
--   per BPB : Dr GR/IR              = subtotal + PPN BPB   (reff = no_bpb)
--             Cr 1.52.07 unbilled   = PPN BPB              (reff = no_bpb)
--   header  : Dr 1.52.04 billed     = PPN header, dipecah per nomor faktur
--             Cr Utang Usaha        = subtotal + beban + PPN - retur
--             Dr/Cr beban           = dari tabel potongan (kalau <> 0)
--             Dr Utang (DP)         = dp_value (kalau <> 0)
--   retur   : Cr GR/IR = total RO;  Dr 1.52.07 / Cr 1.52.04 = PPN RO
--   FTR     : Cr COA FTR = total FTR
--
-- COA GR/IR TIDAK tersimpan di data PV (aplikasi mencarinya dari isian form),
-- jadi diambil dari jurnal approve BPB masing-masing ('AP - BPB', sisi kredit
-- non-PPN) - itu memang akun yang dilunasi oleh PV ini. BPB yang jurnalnya
-- tidak ketemu memakai COA GR/IR terbanyak di PV ini.
--
-- Status & approve jurnal mengikuti status PV saat ini, sama seperti yang
-- dihasilkan aplikasi (dicek di 539 PV SECOND APPROVED data lokal, cocok semua):
--   draft / FIRST APPROVED -> jurnal 'Draft', approve_by & approve_date kosong
--                             (first_approve_kbon.php tidak menyentuh jurnal)
--   SECOND APPROVED        -> jurnal 'SECOND APPROVED', approve_by/approve_date
--                             = second_approve_user/second_approve_date header
--                             (confirm_date bertipe DATE - jamnya hilang)
-- second_approve_kbon.php menyamakan status jurnal lewat "UPDATE
-- tbl_list_journal ... WHERE no_journal = no_kbon" - karena jurnalnya tidak
-- ada, update itu dulu kena 0 baris tanpa error.
--
-- Nilai diambil dari angka TERSIMPAN (2 desimal). Aplikasi aslinya memakai
-- angka form yang belum dibulatkan, jadi dibanding PV normal bisa beda paling
-- banyak 0,01 per baris - total tetap balance.
--
-- PENGAMAN (langkah 4 menyimpan 0 baris kalau salah satu tidak terpenuhi):
--   a. PV tidak berstatus Cancel / Updated
--   b. belum ada SATU PUN baris jurnal untuk nomor ini (tipe apa saja)
--   c. ada baris BPB, tidak ada BPB / retur ganda
--   d. COA Utang, GR/IR, PPN billed & unbilled semuanya ketemu
--   e. draf jurnalnya BALANCE tepat 0,00
-- Draf dibangun dulu di TABEL SEMENTARA (tidak menyentuh data asli), baru
-- disalin ke tbl_list_journal kalau lolos. Aman dijalankan ulang: begitu
-- jurnalnya sudah tersimpan, pengaman (b) membuatnya menyimpan 0 baris.
--
-- CARA PAKAI
--   Langkah 0 : hanya MELIHAT. Jalankan dulu, periksa hasilnya.
--   Langkah 1-5 : jalankan SEKALIGUS dalam SATU sesi/eksekusi (memakai
--                 variabel @ dan tabel sementara yang hilang kalau sesi putus).
--
-- Nomor PV sengaja ditulis langsung (bukan variabel) di setiap perbandingan:
-- variabel @ membawa collation koneksi dan bisa memicu "Illegal mix of
-- collations" terhadap kolom no_journal / no_kbon yang collation-nya berbeda.
--
-- DIUJI (lokal signalbit_bk_sep26, 30 Sep 2026) dengan membangun ulang jurnal
-- 6 PV yang jurnalnya dibuat aplikasi lalu membandingkannya baris per baris:
--   02214 (FIRST APPROVED), 02143 (retur), 02121 (DP)  -> IDENTIK termasuk
--        status, approve_by & approve_date
--   02194 (beban)  -> identik, kecuali reff_doc PPN billed '' vs '-' (PV dibuat
--        sebelum PPN dipecah per faktur; skrip ikut logika sekarang)
--   02246, 02213   -> beda pembulatan <= 0,01 per baris, tetap balance
--   02112          -> aslinya selisih -0,06 akibat bug beban ">= 1" (sudah
--        diperbaiki 23 Sep); skrip ikut logika yang benar -> balance
-- Untuk 02088 SENDIRI belum dijalankan di mana pun.
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================


-- ###########################################################################
-- LANGKAH 0 - DIAGNOSA (hanya melihat, tidak mengubah apa pun)
-- ###########################################################################

-- 0a) Header PV. Normalnya 1 baris. Kalau 2 baris (simpan-dobel seperti
--     02150), skrip memakai baris id TERKECIL yang tidak Cancel/Updated.
SELECT id, no_kbon, status, unik_code, nama_supp, curr, rate, subtotal, tax,
       pph_idr, total, dp_value, no_faktur, no_coa, nama_coa, profit_center,
       create_user, create_date, confirm_user, confirm_date,
       second_approve_user, second_approve_date
FROM   kontrabon_h
WHERE  no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
ORDER BY id;

-- 0b) Baris BPB
SELECT id, no_bpb, tgl_bpb, curr, subtotal, tax, pph_value, total, no_faktur,
       status, create_user, create_date
FROM   kontrabon
WHERE  no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
ORDER BY id;

-- 0c) Header vs jumlah baris BPB (sub & tax wajib sama; jml_bpb = jml_bpb_unik)
SELECT h.subtotal h_sub, d.sub d_sub, h.tax h_tax, d.tax d_tax,
       d.jml_bpb, d.jml_bpb_unik
FROM  (SELECT subtotal, tax FROM kontrabon_h
       WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
         AND status NOT IN ('Cancel','Updated')
       ORDER BY id LIMIT 1) h,
      (SELECT ROUND(SUM(subtotal),2) sub, ROUND(SUM(tax),2) tax,
              COUNT(*) jml_bpb, COUNT(DISTINCT no_bpb) jml_bpb_unik
       FROM kontrabon
       WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
         AND status <> 'Cancel' AND no_bpb <> '') d;

-- 0d) Potongan / retur / FTR
SELECT * FROM potongan      WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088' ORDER BY id;
SELECT * FROM return_kb     WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088';
SELECT * FROM kontrabon_ftr WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088';

-- 0e) Jurnal yang SUDAH ada - harus KOSONG kalau memang tidak tersimpan.
--     LIKE ... '%' ikut menampilkan revisi (-REV_NN) kalau ada.
SELECT no_journal, type_journal, status, COUNT(*) baris,
       ROUND(SUM(debit_idr),2) dr_idr, ROUND(SUM(credit_idr),2) cr_idr
FROM   tbl_list_journal
WHERE  no_journal LIKE 'PV-AP/REG/NAG/2026/08/02088%'
GROUP BY no_journal, type_journal, status;

SELECT COUNT(*) baris_di_tabel_cancel
FROM   tbl_list_journal_cancel
WHERE  no_journal LIKE 'PV-AP/REG/NAG/2026/08/02088%';

-- 0f) COA GR/IR tiap BPB, diambil dari jurnal approve BPB-nya.
--     Semua BPB sebaiknya terisi, dan 1 BPB = 1 COA.
SELECT k.no_bpb, g.no_coa, g.nama_coa, g.baris
FROM   kontrabon k
LEFT JOIN (SELECT no_journal, no_coa, nama_coa, COUNT(*) baris
           FROM   tbl_list_journal
           WHERE  type_journal = 'AP - BPB' AND credit > 0
             AND  nama_coa NOT LIKE '%PPN%'
             AND  no_journal IN (SELECT no_bpb FROM kontrabon
                                 WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088')
           GROUP BY no_journal, no_coa, nama_coa) g
       ON g.no_journal = k.no_bpb
WHERE  k.no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
  AND  k.status <> 'Cancel' AND k.no_bpb <> ''
ORDER BY k.no_bpb;

-- 0g) Pembanding: COA yang dipakai 3 PV Regular terakhir dari supplier yang
--     sama. GR/IR, Utang & PPN di sini seharusnya SAMA dengan yang akan dipakai.
SELECT j.no_journal, j.no_coa, j.nama_coa,
       IF(SUM(j.debit_idr) >= SUM(j.credit_idr), 'Dr', 'Cr') sisi, COUNT(*) baris
FROM   tbl_list_journal j
JOIN  (SELECT no_kbon FROM kontrabon_h
       WHERE  nama_supp = (SELECT nama_supp FROM kontrabon_h
                           WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088' LIMIT 1)
         AND  no_kbon LIKE 'PV-AP/REG/%'
         AND  no_kbon <> 'PV-AP/REG/NAG/2026/08/02088'
         AND  status NOT IN ('Cancel','Updated')
       ORDER BY id DESC LIMIT 3) x
       ON x.no_kbon = j.no_journal
WHERE  j.type_journal = 'AP - Kontrabon'
GROUP BY j.no_journal, j.no_coa, j.nama_coa
ORDER BY j.no_journal DESC, sisi DESC, j.no_coa;

-- 0h) COA PPN dari master (normalnya 1.52.07 UNBILLED dan 1.52.04)
(SELECT 'PPN per-BPB (unbilled)' peran, no_coa, nama_coa
   FROM mastercoa_v2 WHERE inv_type LIKE '%PPN MASUKAN%' LIMIT 1)
UNION ALL
(SELECT 'PPN header (billed)', no_coa, nama_coa
   FROM mastercoa_v2 WHERE inv_type LIKE '%PPN KBN%' LIMIT 1);


-- ###########################################################################
-- LANGKAH 1 - SIAPKAN VARIABEL & PENGAMAN
-- (langkah 1 s/d 5 dijalankan SEKALIGUS)
-- ###########################################################################

SET @hid := (SELECT MIN(id) FROM kontrabon_h
             WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
               AND status NOT IN ('Cancel','Updated'));
SET @pid := (SELECT MIN(id) FROM potongan
             WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088');

-- COA GR/IR default = yang paling banyak dipakai jurnal BPB milik PV ini
SET @coa_grir := NULL, @nama_grir := NULL;
SELECT no_coa, nama_coa INTO @coa_grir, @nama_grir
FROM  (SELECT no_coa, nama_coa, COUNT(*) baris
       FROM   tbl_list_journal
       WHERE  type_journal = 'AP - BPB' AND credit > 0
         AND  nama_coa NOT LIKE '%PPN%'
         AND  no_journal IN (SELECT no_bpb FROM kontrabon
                             WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
                               AND status <> 'Cancel' AND no_bpb <> '')
       GROUP BY no_coa, nama_coa
       ORDER BY baris DESC LIMIT 1) t;

-- COA PPN, dicari persis seperti aplikasi
SET @coa_unb := NULL, @nama_unb := NULL;
SELECT no_coa, nama_coa INTO @coa_unb, @nama_unb
FROM   mastercoa_v2 WHERE inv_type LIKE '%PPN MASUKAN%' LIMIT 1;

SET @coa_bil := NULL, @nama_bil := NULL;
SELECT no_coa, nama_coa INTO @coa_bil, @nama_bil
FROM   mastercoa_v2 WHERE inv_type LIKE '%PPN KBN%' LIMIT 1;

SET @jurnal_ada := (SELECT COUNT(*) FROM tbl_list_journal
                    WHERE no_journal = 'PV-AP/REG/NAG/2026/08/02088');
SET @jml_bpb    := (SELECT COUNT(*) FROM kontrabon
                    WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
                      AND status <> 'Cancel' AND no_bpb <> '');
SET @bpb_ganda  := (SELECT COUNT(*) - COUNT(DISTINCT no_bpb) FROM kontrabon
                    WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
                      AND status <> 'Cancel' AND no_bpb <> '');
SET @ro_ganda   := (SELECT COUNT(*) - COUNT(DISTINCT no_bpbrtn) FROM return_kb
                    WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
                      AND status <> 'Cancel');
SET @coa_utang  := (SELECT TRIM(COALESCE(no_coa,'')) FROM kontrabon_h WHERE id = @hid);

SET @boleh := IF(@hid IS NOT NULL AND @jurnal_ada = 0 AND @jml_bpb > 0
                 AND @bpb_ganda = 0 AND @ro_ganda = 0
                 AND @coa_utang <> '' AND @coa_grir IS NOT NULL
                 AND @coa_unb IS NOT NULL AND @coa_bil IS NOT NULL, 1, 0);

-- boleh_lanjut HARUS 1. Kalau 0, lihat kolom lain untuk tahu sebabnya.
SELECT @boleh boleh_lanjut, @hid id_header, @jurnal_ada jurnal_sudah_ada,
       @jml_bpb jml_bpb, @bpb_ganda bpb_ganda, @ro_ganda ro_ganda,
       @coa_utang coa_utang, @coa_grir coa_grir, @nama_grir nama_grir,
       @coa_unb coa_ppn_unbilled, @coa_bil coa_ppn_billed;


-- ###########################################################################
-- LANGKAH 2 - BANGUN DRAF JURNAL DI TABEL SEMENTARA
-- ###########################################################################

DROP TEMPORARY TABLE IF EXISTS tmp_jurnal_02088;
CREATE TEMPORARY TABLE tmp_jurnal_02088 LIKE tbl_list_journal;

-- 2a) Beban dari tabel potongan (tanda + = debit, - = kredit;
--     potongan beli kebalikannya: + = kredit)
INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, h.create_date, 'AP - Kontrabon', b.no_coa, b.nama_coa,
       IF(h.profit_center = 'NAG', 'DEP24SUB001', 'DEPNK01SUB001'),
       IF(h.profit_center = 'NAG', 'MANAGEMENT FACTORY', 'KNITTING PRODUCTION'),
       '-', '', NULL, NULL, '-', '-', h.curr, h.rate,
       IF(b.nilai * b.arah > 0, ABS(b.nilai), 0),
       IF(b.nilai * b.arah < 0, ABS(b.nilai), 0),
       IF(b.nilai * b.arah > 0, ABS(b.nilai), 0) * h.rate,
       IF(b.nilai * b.arah < 0, ABS(b.nilai), 0) * h.rate,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', h.nama_supp), h.create_user, h.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
JOIN  (SELECT '8.52.02' no_coa, 'LABA / (RUGI) SELISIH KURS BELUM TEREALISASI' nama_coa,
              COALESCE(lr_kurs,0) + 0 nilai, 1 arah FROM potongan WHERE id = @pid
       UNION ALL SELECT '5.97.03', 'BEBAN SELISIH KUANTITAS',  COALESCE(s_qty,0)     + 0,  1 FROM potongan WHERE id = @pid
       UNION ALL SELECT '5.97.02', 'BEBAN SELISIH HARGA',      COALESCE(s_harga,0)   + 0,  1 FROM potongan WHERE id = @pid
       UNION ALL SELECT '5.97.99', 'BEBAN PABRIK LAINNYA',     COALESCE(materai,0)   + 0,  1 FROM potongan WHERE id = @pid
       UNION ALL SELECT '5.84.03', 'BEBAN EKSPEDISI ANGKUTAN', COALESCE(ekspedisi,0) + 0,  1 FROM potongan WHERE id = @pid
       UNION ALL SELECT '5.97.99', 'BEBAN PABRIK LAINNYA',     COALESCE(moq,0)       + 0,  1 FROM potongan WHERE id = @pid
       UNION ALL SELECT '5.97.02', 'BEBAN SELISIH HARGA',      COALESCE(pot_beli,0)  + 0, -1 FROM potongan WHERE id = @pid
      ) b ON b.nilai <> 0
WHERE  h.id = @hid AND @boleh = 1;

-- 2b) Utang Usaha (kredit) = subtotal + beban - pot. beli + PPN - retur
INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, h.create_date, 'AP - Kontrabon', h.no_coa, h.nama_coa, '-', '-',
       '-', '', NULL, NULL, '-', '-', h.curr, h.rate,
       0, u.ttl, 0, u.ttl * h.rate,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', h.nama_supp), h.create_user, h.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
JOIN  (SELECT (h2.subtotal + COALESCE(p.lr_kurs,0) + COALESCE(p.s_qty,0) + COALESCE(p.s_harga,0)
               + COALESCE(p.materai,0) + COALESCE(p.ekspedisi,0) + COALESCE(p.moq,0)
               - COALESCE(p.pot_beli,0)) + h2.tax - COALESCE(p.jml_return,0) AS ttl
       FROM   kontrabon_h h2
       LEFT JOIN potongan p ON p.id = @pid
       WHERE  h2.id = @hid) u
WHERE  h.id = @hid AND @boleh = 1;

-- 2c) Uang muka / DP (debit Utang), hanya kalau ada
INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, h.create_date, 'AP - Kontrabon', h.no_coa, h.nama_coa, '-', '-',
       '-', '', NULL, NULL, '-', '-', h.curr, h.rate,
       h.dp_value, 0, h.dp_value * h.rate, 0,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', h.nama_supp), h.create_user, h.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
WHERE  h.id = @hid AND @boleh = 1 AND COALESCE(h.dp_value, 0) <> 0;

-- 2d) Per BPB: GR/IR debit = subtotal + PPN
INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, k.create_date, 'AP - Kontrabon',
       COALESCE(g.no_coa, @coa_grir), COALESCE(g.nama_coa, @nama_grir), '-', '-',
       k.no_bpb, k.tgl_bpb, NULLIF(k.no_faktur, ''), NULL, '-', '-', k.curr, k.rt,
       k.subtotal + k.tax, 0, (k.subtotal + k.tax) * k.rt, 0,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', k.nama_supp), k.create_user, k.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
JOIN  (SELECT kk.*,
              IF(kk.curr = 'IDR', 1,
                 COALESCE((SELECT ROUND(m.rate,2) FROM masterrate m
                           WHERE m.tanggal = kk.tgl_bpb AND m.v_codecurr = 'PAJAK' LIMIT 1),
                          (SELECT rate FROM kontrabon_h WHERE id = @hid))) AS rt
       FROM   kontrabon kk
       WHERE  kk.no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
         AND  kk.status <> 'Cancel' AND kk.no_bpb <> '') k ON 1 = 1
LEFT JOIN (SELECT no_journal, MAX(no_coa) no_coa, MAX(nama_coa) nama_coa
           FROM   tbl_list_journal
           WHERE  type_journal = 'AP - BPB' AND credit > 0
             AND  nama_coa NOT LIKE '%PPN%'
             AND  no_journal IN (SELECT no_bpb FROM kontrabon
                                 WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088')
           GROUP BY no_journal) g
       ON g.no_journal = k.no_bpb
WHERE  h.id = @hid AND @boleh = 1;

-- 2e) Per BPB: PPN Masukan UNBILLED kredit = PPN BPB (kalau ada PPN)
INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, k.create_date, 'AP - Kontrabon', @coa_unb, @nama_unb, '-', '-',
       k.no_bpb, k.tgl_bpb, NULLIF(k.no_faktur, ''), NULL, '-', '-', k.curr, k.rt,
       0, k.tax, 0, k.tax * k.rt,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', k.nama_supp), k.create_user, k.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
JOIN  (SELECT kk.*,
              IF(kk.curr = 'IDR', 1,
                 COALESCE((SELECT ROUND(m.rate,2) FROM masterrate m
                           WHERE m.tanggal = kk.tgl_bpb AND m.v_codecurr = 'PAJAK' LIMIT 1),
                          (SELECT rate FROM kontrabon_h WHERE id = @hid))) AS rt
       FROM   kontrabon kk
       WHERE  kk.no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
         AND  kk.status <> 'Cancel' AND kk.no_bpb <> '') k ON 1 = 1
WHERE  h.id = @hid AND @boleh = 1 AND k.tax > 0;

-- 2f) Retur (RO): Cr GR/IR = total RO; kalau RO ber-PPN: Dr 1.52.07 / Cr 1.52.04
DROP TEMPORARY TABLE IF EXISTS tmp_ro_02088;
CREATE TEMPORARY TABLE tmp_ro_02088 AS
SELECT rb.no_bpbrtn, rb.total_ro + 0 AS total_ro, bp.tgl_bppb,
       COALESCE(bp.tax, 0) AS tax_pct, COALESCE(bp.tax_ro, 0) AS tax_ro,
       IF((SELECT curr FROM kontrabon_h WHERE id = @hid) = 'IDR', 1,
          COALESCE((SELECT ROUND(m.rate,2) FROM masterrate m
                    WHERE m.tanggal = bp.tgl_bppb AND m.v_codecurr = 'PAJAK' LIMIT 1),
                   (SELECT rate FROM kontrabon_h WHERE id = @hid))) AS rt
FROM   return_kb rb
LEFT JOIN (SELECT no_bppb, MAX(tgl_bppb) tgl_bppb, MAX(tax) tax,
                  ROUND(SUM((qty * price) * tax / 100), 2) tax_ro
           FROM   bppb_new
           WHERE  no_bppb IN (SELECT no_bpbrtn FROM return_kb
                              WHERE no_kbon = 'PV-AP/REG/NAG/2026/08/02088')
           GROUP BY no_bppb) bp
       ON bp.no_bppb = rb.no_bpbrtn
WHERE  rb.no_kbon = 'PV-AP/REG/NAG/2026/08/02088' AND rb.status <> 'Cancel';

INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, h.create_date, 'AP - Kontrabon', r.no_coa, r.nama_coa, '-', '-',
       r.no_bpbrtn, r.tgl_bppb, NULL, NULL, '-', '-', h.curr, r.rt,
       r.dr, r.cr, r.dr * r.rt, r.cr * r.rt,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', h.nama_supp), h.create_user, h.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
JOIN  (SELECT no_bpbrtn, tgl_bppb, rt, @coa_grir no_coa, @nama_grir nama_coa,
              0 dr, total_ro cr, 1 urut
       FROM   tmp_ro_02088
       UNION ALL
       SELECT no_bpbrtn, tgl_bppb, rt, '1.52.07', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN (UNBILLED)',
              tax_ro, 0, 2
       FROM   tmp_ro_02088 WHERE tax_pct > 0
       UNION ALL
       SELECT no_bpbrtn, tgl_bppb, rt, '1.52.04', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN',
              0, tax_ro, 3
       FROM   tmp_ro_02088 WHERE tax_pct > 0) r ON 1 = 1
WHERE  h.id = @hid AND @boleh = 1
ORDER BY r.no_bpbrtn, r.urut;

-- 2g) FTR (uang muka dari FTR), kredit COA FTR.
--     Status kontrabon_ftr SENGAJA tidak disaring: approvekbon.php mengubahnya
--     jadi 'Cancel' saat PV di-approve, padahal baris jurnalnya tetap ada.
INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, h.create_date, 'AP - Kontrabon', f.no_coa,
       COALESCE((SELECT nama_coa FROM mastercoa_v2 c WHERE c.no_coa = f.no_coa LIMIT 1), ''),
       '-', '-', f.no_ftr, '', NULL, NULL, '-', '-', f.curr, f.rt,
       0, f.total_ftr, 0, f.total_ftr * f.rt,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', f.nama_supp), f.created_by, h.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
JOIN  (SELECT ft.*,
              IF(ft.curr = 'IDR', 1,
                 COALESCE((SELECT ROUND(m.rate,2) FROM masterrate m
                           WHERE m.tanggal = ft.tgl_bankout AND m.v_codecurr = 'PAJAK'
                             AND m.curr = ft.curr LIMIT 1), 1)) AS rt
       FROM   kontrabon_ftr ft
       WHERE  ft.no_kbon = 'PV-AP/REG/NAG/2026/08/02088') f ON 1 = 1
WHERE  h.id = @hid AND @boleh = 1;

-- 2h) Tanggal faktur pajak per BPB (kolom kontrabon.tgl_faktur belum tentu ada
--     di semua server - dicek dulu, dilewati kalau tidak ada)
SET @ada_tgl := (SELECT COUNT(*) FROM information_schema.columns
                 WHERE table_schema = DATABASE() AND table_name = 'kontrabon'
                   AND column_name = 'tgl_faktur');
SET @q := IF(@ada_tgl > 0,
  'UPDATE tmp_jurnal_02088 j
     JOIN kontrabon k ON k.no_kbon = j.no_journal AND k.no_bpb = j.reff_doc
                     AND k.status <> ''Cancel''
      SET j.tgl_faktur_pajak = NULLIF(k.tgl_faktur, ''0000-00-00'')
    WHERE j.faktur_pajak IS NOT NULL AND j.faktur_pajak <> ''''',
  'SELECT ''kolom kontrabon.tgl_faktur tidak ada - dilewati'' AS info');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

-- 2i) PPN Masukan BILLED header, dipecah per nomor faktur
--     (sama dengan pv_group_ppn_billed_by_faktur): nilai tiap faktur = jumlah
--     PPN unbilled BPB dengan faktur itu; selisih pembulatan terhadap PPN header
--     ditempel ke faktur TERBESAR, jadi totalnya tepat = PPN header.
DROP TEMPORARY TABLE IF EXISTS tmp_ppn_02088;
CREATE TEMPORARY TABLE tmp_ppn_02088 AS
SELECT COALESCE(faktur_pajak, '') AS fp, MAX(tgl_faktur_pajak) AS tfp,
       MAX(curr) AS curr, MAX(rate) AS rate,
       SUM(credit) AS scr, SUM(credit_idr) AS scr_idr
FROM   tmp_jurnal_02088
WHERE  nama_coa LIKE '%UNBILLED%' AND credit_idr > 0
GROUP BY COALESCE(faktur_pajak, '');

SET @tax_h       := (SELECT tax        FROM kontrabon_h WHERE id = @hid);
SET @tax_h_idr   := (SELECT tax * rate FROM kontrabon_h WHERE id = @hid);
SET @sum_scr     := (SELECT COALESCE(SUM(scr), 0)     FROM tmp_ppn_02088);
SET @sum_scr_idr := (SELECT COALESCE(SUM(scr_idr), 0) FROM tmp_ppn_02088);
SET @fp_top      := (SELECT fp FROM tmp_ppn_02088 ORDER BY scr_idr DESC LIMIT 1);

INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, h.create_date, 'AP - Kontrabon', @coa_bil, @nama_bil, '-', '-',
       '-', '', g.fp, g.tfp, '-', '-',
       COALESCE(NULLIF(g.curr, ''), h.curr), IF(g.rate > 0, g.rate, h.rate),
       g.scr     + IF(g.fp = @fp_top, @tax_h     - @sum_scr,     0), 0,
       g.scr_idr + IF(g.fp = @fp_top, @tax_h_idr - @sum_scr_idr, 0), 0,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', h.nama_supp), h.create_user, h.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
JOIN   tmp_ppn_02088 g ON 1 = 1
WHERE  h.id = @hid AND @boleh = 1 AND h.tax >= 1
ORDER BY g.scr_idr DESC;

-- Cadangan: PPN header ada tapi tidak ada PPN per-BPB -> 1 baris konsolidasi
INSERT INTO tmp_jurnal_02088
       (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter,
        reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
        debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date,
        approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT h.no_kbon, h.create_date, 'AP - Kontrabon', @coa_bil, @nama_bil, '-', '-',
       h.no_faktur, '', NULL, NULL, '-', '-', h.curr, h.rate,
       h.tax, 0, h.tax * h.rate, 0,
       IF(h.status IN ('draft','FIRST APPROVED'), 'Draft', h.status),
       CONCAT('KONTRABON ', h.nama_supp), h.create_user, h.create_date,
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(NULLIF(h.second_approve_user, ''), h.confirm_user, '')),
       IF(h.status IN ('draft','FIRST APPROVED'), '', COALESCE(h.second_approve_date, h.confirm_date, '')),
       '', '', h.profit_center
FROM   kontrabon_h h
WHERE  h.id = @hid AND @boleh = 1 AND h.tax >= 1
  AND  (SELECT COUNT(*) FROM tmp_ppn_02088) = 0;

-- 2j) Kolom supplier (kalau kolomnya ada di server ini). Trigger BEFORE INSERT
--     di tbl_list_journal juga mengisinya; ini supaya tetap terisi walau
--     trigger-nya belum dipasang.
SET @ada_supp := (SELECT COUNT(*) FROM information_schema.columns
                  WHERE table_schema = DATABASE() AND table_name = 'tbl_list_journal'
                    AND column_name = 'supplier');
SET @q := IF(@ada_supp > 0,
  'UPDATE tmp_jurnal_02088 SET supplier = (SELECT nama_supp FROM kontrabon_h WHERE id = @hid)',
  'SELECT ''kolom supplier tidak ada - dilewati'' AS info');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;


-- ###########################################################################
-- LANGKAH 3 - PRATINJAU DRAF (belum ada yang tersimpan sampai langkah ini)
-- ###########################################################################

SELECT id, no_coa, nama_coa, reff_doc, faktur_pajak, curr, rate,
       ROUND(debit,2) debit, ROUND(credit,2) credit,
       ROUND(debit_idr,2) debit_idr, ROUND(credit_idr,2) credit_idr, status
FROM   tmp_jurnal_02088
ORDER BY id;

SET @selisih := (SELECT ROUND(SUM(debit_idr) - SUM(credit_idr), 2) FROM tmp_jurnal_02088);

-- selisih HARUS 0.00, kalau tidak langkah 4 menyimpan 0 baris
SELECT COUNT(*) baris, ROUND(SUM(debit_idr),2) total_debit_idr,
       ROUND(SUM(credit_idr),2) total_kredit_idr, @selisih selisih, @boleh boleh_lanjut
FROM   tmp_jurnal_02088;


-- ###########################################################################
-- LANGKAH 4 - SIMPAN KE tbl_list_journal (hanya kalau boleh_lanjut = 1 DAN
-- selisih = 0.00; selain itu menyimpan 0 baris). Satu perintah INSERT, jadi
-- tersimpan semua atau tidak sama sekali.
-- ###########################################################################

SET @cols := CONCAT(
  'no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, ',
  'reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, ',
  'debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, ',
  'approve_by, approve_date, cancel_by, cancel_date, profit_center',
  IF(@ada_supp > 0, ', supplier', ''));

SET @q := CONCAT('INSERT INTO tbl_list_journal (', @cols, ') SELECT ', @cols,
                 ' FROM tmp_jurnal_02088 ORDER BY id',
                 IF(@boleh = 1 AND @selisih = 0, '', ' LIMIT 0'));
PREPARE s FROM @q;
EXECUTE s;
SELECT ROW_COUNT() AS baris_tersimpan;   -- 0 = tidak lolos pengaman, tidak ada yang berubah
DEALLOCATE PREPARE s;


-- ###########################################################################
-- LANGKAH 5 - PERIKSA HASIL DI tbl_list_journal
-- ###########################################################################

SELECT no_coa, nama_coa, COUNT(*) baris,
       ROUND(SUM(debit_idr),2) debit_idr, ROUND(SUM(credit_idr),2) credit_idr
FROM   tbl_list_journal
WHERE  no_journal = 'PV-AP/REG/NAG/2026/08/02088'
GROUP BY no_coa, nama_coa
ORDER BY no_coa;

-- selisih WAJIB 0.00
SELECT COUNT(*) baris, ROUND(SUM(debit_idr),2) dr_idr, ROUND(SUM(credit_idr),2) cr_idr,
       ROUND(SUM(debit_idr) - SUM(credit_idr), 2) selisih
FROM   tbl_list_journal
WHERE  no_journal = 'PV-AP/REG/NAG/2026/08/02088';

-- Setiap BPB wajib punya baris GR/IR (harus 0)
SELECT COUNT(*) bpb_tanpa_jurnal
FROM   kontrabon k
WHERE  k.no_kbon = 'PV-AP/REG/NAG/2026/08/02088'
  AND  k.status <> 'Cancel' AND k.no_bpb <> ''
  AND  NOT EXISTS (SELECT 1 FROM tbl_list_journal j
                   WHERE j.no_journal = 'PV-AP/REG/NAG/2026/08/02088'
                     AND j.reff_doc = k.no_bpb AND j.debit > 0);

DROP TEMPORARY TABLE IF EXISTS tmp_jurnal_02088;
DROP TEMPORARY TABLE IF EXISTS tmp_ppn_02088;
DROP TEMPORARY TABLE IF EXISTS tmp_ro_02088;


-- ###########################################################################
-- BATALKAN (hanya kalau perlu, SEGERA setelah skrip ini dijalankan)
-- Aman karena sebelum perbaikan jumlah baris jurnal nomor ini = 0 (dijaga
-- pengaman). JANGAN dipakai lagi setelah PV ini diedit / di-approve ulang.
-- ###########################################################################
-- DELETE FROM tbl_list_journal
-- WHERE  no_journal = 'PV-AP/REG/NAG/2026/08/02088'
--   AND  type_journal = 'AP - Kontrabon';
