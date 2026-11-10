-- ============================================================================
-- OTHER RECEIVABLE REPORT (COA 1.34.04) - kenapa Deduction (ALK) tidak sama
-- dengan jurnal alokasinya.  BERKAS INI HANYA BERISI SELECT. Tidak ada
-- INSERT/UPDATE/DELETE/ALTER satu pun - aman dijalankan di produksi kapan saja.
--
-- TEMUANNYA (dibuktikan di salinan lokal signalbit_bk_sep26, 9 Nov 2026):
--
-- 1. KURSNYA SAMA, BUKAN BEDA KURS. Kolom "KURS SL" di berkas Excel yang
--    dikirim itu hasil bagi (nilai subledger / nilai USD jurnal), jadi
--    selisih JUMLAH otomatis tampil seolah-olah selisih KURS. Kalau nilai
--    subledger dibagi kurs ALOKASI, hasilnya selalu angka USD bulat 2
--    desimal - artinya subledger memakai kurs alokasi yang sama persis:
--
--      MEMO/NAG/2607/01060  11.741.707,16 / 17.863 = 657,32 USD (jurnal 664,55)
--      MEMO/NAG/2608/01135   1.725.387,17 / 17.863 =  96,59 USD (jurnal  97,65)
--      MEMO/NAG/2608/01238  11.558.419,51 / 17.897 = 645,83 USD (jurnal 652,93)
--      MEMO/NAG/2609/01335  11.064.074,56 / 18.032 = 613,58 USD (jurnal 643,21)
--
--    Seluruh 14 baris itu selisihnya 56,30 USD = Rp 1.011.317,25, persis
--    angka di kepala berkas Excel-nya. Jadi yang beda JUMLAH USD-nya.
--
--    CATATAN CARA MEMBANDINGKAN: jurnal alokasi dibuat PER DN (satu baris
--    1.34.04 per nomor DN), sedangkan laporan ini menyajikan PER MEMO. Satu
--    DN bisa menanggung beberapa memo sekaligus, jadi sisi subledger-nya
--    adalah JUMLAH beberapa baris laporan. Contohnya DN/NAK/0726/0637
--    menanggung tiga memo - 01060 (294,49) + 01044 (271,37) + 01045 (91,46)
--    = 657,32 USD, dan 657,32 itulah yang disandingkan dengan jurnal 664,55.
--    Jadi memberi satu nama memo pada satu baris perbandingan bisa
--    menyesatkan; patokan yang benar nomor DN-nya. BAGIAN 2B di bawah
--    memecah tiap DN menurut memonya.
--
-- 2. SEBAB UTAMA: BARIS PPN MEMO SENGAJA DIBUANG DARI SUBLEDGER.
--    Query memo di other_receivable_report.php menyaring
--        b.nm_sub_ctg != 'VAT'
--    di DUA tempat: saat menyusun Beginning Balance/Addition (baris ~404) dan
--    saat menyusun Deduction (baris ~411). Debit Note dan jurnal alokasinya
--    TIDAK menyaring apa pun - yang dipotong nilai DN secara penuh, termasuk
--    PPN-nya. Jadi untuk tiap memo ber-PPN, Deduction (ALK) memang SELALU
--    lebih kecil daripada jurnal sebesar PPN-nya.
--    Tujuh dari 14 baris itu selisihnya sekitar 1,1% dari nilai subledger
--    (lima di antaranya tepat 1,10%) - PPN jasa forwarder, DPP nilai lain
--    10% x 11%. Satu baris lagi 4,83%, campuran tarif.
--
-- 3. SEBAB KEDUA (yang recehan): PEMBULATAN ALOKASI.
--    tbl_alokasi_detail.amount (yang dipakai jurnal) kadang beda 0,01-0,04 USD
--    dari jumlah baris tbl_debitnote_det (yang dipakai subledger). Itulah
--    baris yang selisihnya 178,63 dan 714,52 - persis 0,01 dan 0,04 USD
--    dikali kurs alokasinya. Penjaga di laporan hanya bereaksi kalau sisanya
--    LEBIH DARI 1 USD (sisa_dn > 1), jadi selisih serecehan ini lolos.
--
-- 4. SEBAB KETIGA, belum tentu kena ke-14 baris ini tapi ada di data lain:
--    baris tbl_debitnote_det yang nm_memo / id_memo_det-nya KOSONG. Baris itu
--    bagian dari DN dan ikut dijurnal, tapi tidak punya memo sehingga tidak
--    pernah muncul di subledger mana pun. Contoh nyata di salinan lokal:
--    DN/NAG/0726/0651, 1.407,58 USD dari 2.125,75 USD tidak bermemo.
--
-- KESIMPULAN: dua sisi ini memang BEDA UKURAN - subledger memo NETO PPN,
-- buku besar 1.34.04 BRUTO. Masing-masing konsisten di dalam dirinya sendiri
-- (Addition-nya pun neto PPN), jadi tiap memo tetap habis jadi nol. Yang
-- tidak bisa dilakukan adalah menyandingkan baris subledger dengan jurnal
-- begitu saja. Mau disamakan atau tidak itu keputusan akuntansi - sampai
-- berkas ini dibuat, BELUM ADA satu baris kode pun yang diubah.
-- ============================================================================


-- ============================================================================
-- BAGIAN 1 - 14 BARIS YANG DIKIRIM. Ini yang membuktikan angkanya.
--
-- Cara membacanya:
--   usd_dialokasi / usd_jurnal   = yang dipotong jurnal (dua-duanya harus sama)
--   usd_masuk_subledger          = yang dihitung kolom Deduction (ALK)
--   usd_vat_dibuang              = baris PPN memo yang disaring keluar
--   usd_tanpa_memo               = baris DN yang tidak punya memo sama sekali
--   selisih_rupiah               = harus sama dengan kolom SELISIH di Excel
-- ============================================================================
SELECT g.no_alk, g.tgl_alk, g.curr, g.rate AS kurs_alk, f.no_ref AS no_dn,
       ROUND(f.amount, 2)                                     AS usd_dialokasi,
       ROUND(IFNULL(j.usd_jurnal, 0), 2)                      AS usd_jurnal,
       ROUND(IFNULL(d.usd_masuk, 0), 2)                       AS usd_masuk_subledger,
       ROUND(IFNULL(d.usd_vat, 0), 2)                         AS usd_vat_dibuang,
       ROUND(IFNULL(d.usd_tanpa_memo, 0), 2)                  AS usd_tanpa_memo,
       ROUND(IFNULL(d.usd_cancel, 0), 2)                      AS usd_memo_cancel,
       ROUND(f.amount - IFNULL(d.usd_masuk, 0), 2)            AS selisih_usd,
       ROUND((f.amount - IFNULL(d.usd_masuk, 0)) * g.rate, 2) AS selisih_rupiah
  FROM tbl_alokasi g
 INNER JOIN tbl_alokasi_detail f ON f.no_alk = g.no_alk AND f.coa = '1.34.04'
  LEFT JOIN (SELECT reff_doc, SUM(credit - debit) AS usd_jurnal
               FROM tbl_list_journal
              WHERE no_coa = '1.34.04'
              GROUP BY reff_doc) j ON j.reff_doc = f.no_ref
  LEFT JOIN (SELECT c.no_dn,
                    SUM(CASE WHEN md.id IS NOT NULL AND md.cancel = 'N'
                              AND md.nm_sub_ctg <> 'VAT' THEN c.amount ELSE 0 END) AS usd_masuk,
                    SUM(CASE WHEN md.id IS NOT NULL AND md.cancel = 'N'
                              AND md.nm_sub_ctg  = 'VAT' THEN c.amount ELSE 0 END) AS usd_vat,
                    SUM(CASE WHEN md.id IS NOT NULL AND md.cancel <> 'N'
                             THEN c.amount ELSE 0 END)                             AS usd_cancel,
                    SUM(CASE WHEN md.id IS NULL THEN c.amount ELSE 0 END)          AS usd_tanpa_memo
               FROM tbl_debitnote_det c
               LEFT JOIN memo_det md ON md.id = c.id_memo_det
              GROUP BY c.no_dn) d ON d.no_dn = f.no_ref
 WHERE f.no_ref IN ('DN/NAK/0726/0637','DN/NAK/0826/0699','DN/NAK/0826/0697',
                    'DN/NAK/0926/0745','DN/NAG/0926/0738','DN/NAG/0926/0757',
                    'DN/NAG/0926/0741','DN/NAG/0926/0746','DN/NAG/0926/0794',
                    'DN/NAG/0926/0804','DN/NAG/0926/0805','DN/NAG/1026/0852',
                    'DN/NAG/1026/0853','DN/NAG/1026/0877')
   AND g.status <> 'Cancel'
 ORDER BY g.no_alk, f.no_ref;


-- ============================================================================
-- BAGIAN 2 - BARIS PPN-nya SENDIRI, supaya kelihatan angkanya datang dari mana.
-- Jumlah kolom usd_di_dn di sini = kolom usd_vat_dibuang di BAGIAN 1.
-- ============================================================================
SELECT c.no_dn, c.nm_memo, md.id AS id_memo_det, md.nm_ctg, md.nm_sub_ctg,
       md.biaya AS nilai_memo_idr,
       c.value  AS nilai_di_dn_idr,
       c.amount AS usd_di_dn
  FROM tbl_debitnote_det c
 INNER JOIN memo_det md ON md.id = c.id_memo_det
 WHERE md.nm_sub_ctg = 'VAT'
   AND md.cancel = 'N'
   AND c.no_dn IN ('DN/NAK/0726/0637','DN/NAK/0826/0697','DN/NAG/0926/0741',
                   'DN/NAG/0926/0746','DN/NAG/0926/0794','DN/NAG/0926/0804',
                   'DN/NAG/0926/0805','DN/NAG/1026/0852')
 ORDER BY c.no_dn, md.id;


-- ============================================================================
-- BAGIAN 2B - TIAP DN DIPECAH MENURUT MEMONYA. Ini yang memperlihatkan bahwa
-- satu DN bisa menanggung beberapa memo, dan bahwa jumlah kolom usd_non_vat
-- per DN persis sama dengan nilai Deduction (ALK) di laporan dibagi kurs
-- alokasinya. Baris "(tanpa memo)" adalah nilai DN yang tidak punya memo -
-- ikut dijurnal, tapi tidak pernah muncul di subledger mana pun.
-- ============================================================================
SELECT c.no_dn,
       IFNULL(NULLIF(TRIM(c.nm_memo), ''), '(tanpa memo)') AS memo,
       COUNT(*)                                            AS jml_baris,
       ROUND(SUM(c.amount), 2)                             AS usd_semua,
       ROUND(SUM(CASE WHEN md.id IS NOT NULL AND md.cancel = 'N'
                       AND md.nm_sub_ctg <> 'VAT' THEN c.amount ELSE 0 END), 2) AS usd_non_vat,
       ROUND(SUM(CASE WHEN md.nm_sub_ctg = 'VAT' AND md.cancel = 'N'
                      THEN c.amount ELSE 0 END), 2)        AS usd_vat
  FROM tbl_debitnote_det c
  LEFT JOIN memo_det md ON md.id = c.id_memo_det
 WHERE c.no_dn IN ('DN/NAK/0726/0637','DN/NAK/0826/0699','DN/NAK/0826/0697',
                   'DN/NAK/0926/0745','DN/NAG/0926/0738','DN/NAG/0926/0757',
                   'DN/NAG/0926/0741','DN/NAG/0926/0746','DN/NAG/0926/0794',
                   'DN/NAG/0926/0804','DN/NAG/0926/0805','DN/NAG/1026/0852',
                   'DN/NAG/1026/0853','DN/NAG/1026/0877')
 GROUP BY c.no_dn, memo WITH ROLLUP;


-- ============================================================================
-- BAGIAN 3 - PEMBULATAN ALOKASI (sebab selisih 178,63 dan 714,52).
-- Kalau total_det_dn <> amount_dialokasi tapi bedanya <= 1 USD, penjaga
-- sisa_dn > 1 di laporan TIDAK bereaksi dan selisihnya lolos diam-diam.
-- ============================================================================
SELECT f.no_alk, f.no_ref AS no_dn, g.rate AS kurs_alk,
       ROUND(f.total, 2)                      AS total_dn_di_alokasi,
       ROUND(f.amount, 2)                     AS amount_dialokasi,
       ROUND(dd.tot, 2)                       AS total_det_dn,
       ROUND(dd.tot - f.amount, 2)            AS beda_usd,
       ROUND((dd.tot - f.amount) * g.rate, 2) AS beda_rupiah
  FROM tbl_alokasi_detail f
 INNER JOIN tbl_alokasi g ON g.no_alk = f.no_alk
 INNER JOIN (SELECT no_dn, SUM(amount) AS tot FROM tbl_debitnote_det GROUP BY no_dn) dd
         ON dd.no_dn = f.no_ref
 WHERE f.coa = '1.34.04'
   AND g.status <> 'Cancel'
   AND ROUND(dd.tot - f.amount, 2) <> 0
   AND ABS(dd.tot - f.amount) <= 1
   AND g.tgl_alk >= '2026-09-01'
 ORDER BY g.tgl_alk, f.no_ref;


-- ============================================================================
-- BAGIAN 4 - SAPU MENYELURUH. Berapa besar gejalanya di SELURUH alokasi USD
-- tahun berjalan. Ganti rentang tanggalnya sesuai kebutuhan.
-- Alokasi IDR dikecualikan - kalau dicampur, jumlah USD-nya jadi tidak berarti.
-- ============================================================================
SELECT COUNT(*)                                            AS jml_baris_dn,
       SUM(CASE WHEN ROUND(f.amount - IFNULL(d.usd_masuk, 0), 2) <> 0
                THEN 1 ELSE 0 END)                         AS jml_dn_selisih,
       ROUND(SUM(IFNULL(d.usd_vat, 0)), 2)                 AS usd_vat_dibuang,
       ROUND(SUM(IFNULL(d.usd_tanpa_memo, 0)), 2)          AS usd_tanpa_memo,
       ROUND(SUM(f.amount - IFNULL(d.usd_masuk, 0)), 2)    AS selisih_usd,
       ROUND(SUM((f.amount - IFNULL(d.usd_masuk, 0)) * g.rate), 2) AS selisih_rupiah
  FROM tbl_alokasi g
 INNER JOIN tbl_alokasi_detail f ON f.no_alk = g.no_alk AND f.coa = '1.34.04'
  LEFT JOIN (SELECT c.no_dn,
                    SUM(CASE WHEN md.id IS NOT NULL AND md.cancel = 'N'
                              AND md.nm_sub_ctg <> 'VAT' THEN c.amount ELSE 0 END) AS usd_masuk,
                    SUM(CASE WHEN md.id IS NOT NULL AND md.cancel = 'N'
                              AND md.nm_sub_ctg  = 'VAT' THEN c.amount ELSE 0 END) AS usd_vat,
                    SUM(CASE WHEN md.id IS NULL THEN c.amount ELSE 0 END)          AS usd_tanpa_memo
               FROM tbl_debitnote_det c
               LEFT JOIN memo_det md ON md.id = c.id_memo_det
              GROUP BY c.no_dn) d ON d.no_dn = f.no_ref
 WHERE g.tgl_alk BETWEEN '2026-01-01' AND '2026-12-31'
   AND g.status <> 'Cancel'
   AND g.curr <> 'IDR';

-- ============================================================================
-- BAGIAN 5 - MEMBUKTIKAN ANGKA TOTALNYA. (ditambahkan 10 Nov 2026)
--
-- Pemakai mengirim lembar kerja berisi 14 baris dgn selisih Rp 1.011.317,25
-- dan menyimpulkan "kursnya beda". BAGIAN ini memecah angka itu menurut
-- SEBABNYA, supaya terlihat kursnya sama dan yang beda jumlah USD-nya.
--
-- SUDAH DICOCOKKAN KE ANGKA PEMAKAI (10 Nov 2026), seluruhnya pas:
--     PPN memo             56,24 USD  = Rp 1.010.245,47   (99,894%)
--     Pembulatan alokasi    0,06 USD  = Rp      1.071,78   ( 0,106%)
--     ------------------------------------------------------------
--     JUMLAH               56,30 USD  = Rp 1.011.317,25
--
-- BUKTI BAHWA KURSNYA SAMA, dan ini yang paling menentukan: selisih SETIAP
-- baris kalau dibagi kurs alokasinya sendiri menghasilkan angka USD bulat
-- DUA DESIMAL - 7,23 / 0,01 / 1,06 / 0,01 / 0,04 / 7,10 / 0,95 / 0,50 /
-- 5,45 / 4,32 / 29,63 - 14 dari 14 baris, tanpa sisa satu sen pun.
-- Kalau dua sisi itu benar-benar memakai kurs BERBEDA, selisihnya akan
-- berbentuk (jumlah USD x beda kurs), dan angka seperti itu hampir tidak
-- mungkin jatuh tepat pada USD dua desimal di kurs mana pun. Yang berbeda
-- hanya JUMLAH USD-nya; pengalinya satu dan sama.
--
-- Kolom "KURS SL" di lembar kerja itu bukan kurs yang tersimpan di mana pun:
-- itu hasil bagi (nilai subledger / jumlah USD jurnal) = kurs asli x
-- (1 - porsi PPN). Karena itu ia SELALU lebih kecil, tidak pernah lebih
-- besar - pertanda yang tidak akan muncul pada beda kurs yang sungguhan.
--
-- Isi SEBELUM menjalankan:
SET @dari := '2026-10-01';
SET @sampai := '2026-10-31';

SELECT '=== BAGIAN 5A - per memo ===' AS bagian;

SELECT x.nm_memo,
       x.no_alk,
       x.rate                                      AS kurs_alokasi,
       ROUND(x.usd_tanpa_vat, 2)                   AS usd_subledger,
       ROUND(x.usd_dgn_vat, 2)                     AS usd_jurnal,
       ROUND(x.usd_dgn_vat - x.usd_tanpa_vat, 2)   AS selisih_usd,
       ROUND((x.usd_dgn_vat - x.usd_tanpa_vat) * x.rate, 2) AS selisih_rp,
       CASE WHEN ROUND(x.usd_dgn_vat - x.usd_tanpa_vat, 2) = 0 THEN 'sama'
            WHEN ROUND(x.usd_dgn_vat - x.usd_tanpa_vat, 2) <= 0.05 THEN 'pembulatan alokasi'
            ELSE 'PPN memo' END                    AS sebab
FROM (
  SELECT c.nm_memo, g.no_alk, g.rate,
         SUM(IF(b.nm_sub_ctg <> 'VAT', c.amount, 0)) AS usd_tanpa_vat,
         SUM(c.amount)                               AS usd_dgn_vat
    FROM memo_h a
    INNER JOIN memo_det b            ON b.id_h = a.id_h
    INNER JOIN tbl_debitnote_det c   ON c.id_memo_det = b.id
    INNER JOIN tbl_debitnote_h e     ON e.no_dn = c.no_dn
    INNER JOIN tbl_alokasi_detail f  ON f.no_ref = e.no_dn
    INNER JOIN tbl_alokasi g         ON g.no_alk = f.no_alk
   WHERE g.tgl_alk BETWEEN @dari AND @sampai
     AND b.cancel = 'N' AND g.status <> 'Cancel' AND c.nm_memo IS NOT NULL
   GROUP BY c.nm_memo, g.no_alk, g.rate
) x
ORDER BY selisih_rp DESC;

SELECT '=== BAGIAN 5B - rekap per sebab, INI ANGKA YANG DICARI ===' AS bagian;

SELECT y.sebab,
       COUNT(*)                   AS jml_memo,
       ROUND(SUM(y.selisih_usd),2) AS total_usd,
       ROUND(SUM(y.selisih_rp),2)  AS total_rp
FROM (
  SELECT ROUND(x.usd_dgn_vat - x.usd_tanpa_vat, 2)                    AS selisih_usd,
         ROUND((x.usd_dgn_vat - x.usd_tanpa_vat) * x.rate, 2)         AS selisih_rp,
         CASE WHEN ROUND(x.usd_dgn_vat - x.usd_tanpa_vat, 2) = 0 THEN 'sama'
              WHEN ROUND(x.usd_dgn_vat - x.usd_tanpa_vat, 2) <= 0.05 THEN 'pembulatan alokasi'
              ELSE 'PPN memo' END                                     AS sebab
  FROM (
    SELECT c.nm_memo, g.rate,
           SUM(IF(b.nm_sub_ctg <> 'VAT', c.amount, 0)) AS usd_tanpa_vat,
           SUM(c.amount)                               AS usd_dgn_vat
      FROM memo_h a
      INNER JOIN memo_det b            ON b.id_h = a.id_h
      INNER JOIN tbl_debitnote_det c   ON c.id_memo_det = b.id
      INNER JOIN tbl_debitnote_h e     ON e.no_dn = c.no_dn
      INNER JOIN tbl_alokasi_detail f  ON f.no_ref = e.no_dn
      INNER JOIN tbl_alokasi g         ON g.no_alk = f.no_alk
     WHERE g.tgl_alk BETWEEN @dari AND @sampai
       AND b.cancel = 'N' AND g.status <> 'Cancel' AND c.nm_memo IS NOT NULL
     GROUP BY c.nm_memo, g.no_alk, g.rate
  ) x
) y
GROUP BY y.sebab WITH ROLLUP;
