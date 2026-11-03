-- ============================================================================
-- PERBAIKAN DATA: PV-AP/REG/NAG/2026/10/02590-REV_01
-- Potongan uang muka FTR/C/NAG/0926/00689 tidak ikut tersimpan.
--
-- DUDUK PERKARANYA
--   PV ini diedit 16 Okt 2026 dan ditambahi potongan uang muka CBD. Header-nya
--   SUDAH tercatat benar (kontrabon_h.dp_value = 1.485.000, total = 0), tetapi:
--     - kontrabon_ftr TIDAK punya satu baris pun  -> PDF membaca
--       SUM(total_ftr) = 0, jadi baris "CBD or DP" tertulis ( - 0.00 ) padahal
--       Grand Total sudah 0. Kotak ringkasannya jadi tidak masuk akal.
--     - jurnalnya hanya 2 baris (Cr Utang Usaha + Dr GR/IR). Dua kaki potongan
--       uang muka hilang sama sekali, sehingga Utang Usaha ikut terkredit penuh
--       1.485.000 seolah-olah tagihan ini masih harus dibayar, dan saldo uang
--       muka FTR/C/NAG/0926/00689 tidak pernah berkurang.
--
--   Penyebab sistemnya SUDAH diperbaiki 19 Okt 2026 (commit 5d22c459: blok FTR
--   dikeluarkan dari blok BPB di insertkbon_bulk_edit.php, kegagalan simpan
--   dibuat fatal, ditambah penjaga saldo jurnal). Produksi memakai berkas
--   deploy 23 Okt 2026, jadi sudah mengandung perbaikan itu. Berkas ini HANYA
--   membereskan satu dokumen yang terlanjur rusak sebelum perbaikan tsb.
--   Pemeriksaan seluruh 2026: hanya dokumen ini yang dp_value-nya > 0 tanpa
--   baris kontrabon_ftr.
--
-- NILAI YANG DIPAKAI
--   Diambil apa adanya dari query daftar FTR milik form edit PV
--   (edit_pv_regular_new.php), dijalankan ke produksi utk supplier
--   SINU PANCA KHARISMA - yaitu baris yang dulu dicentang user:
--     no_ftr      FTR/C/NAG/0926/00689     total     1.485.000 IDR
--     no_po       SMT/0926/056/04575       tgl_po    2026-09-14
--     no_pv       PV-AP/CBD/NAG/2026/09/00156
--     no_bankout  BK/BCA1979/NAG/0926/00175  tgl     2026-09-17
--     no_coa      1.49.11 UANG MUKA PEMBELIAN LOKAL - BAHAN BAKU / BAHAN PEMBANTU
--   no_pi sengaja dibiarkan kosong: cabang query yang memasok baris ini memang
--   mengirim NULL, dan dokumen yang tersimpan benar (mis. PV-AP/REG/NAG/2026/
--   11/02714) juga kosong di kolom itu. Jangan diisi supaya tidak beda sendiri.
--
-- BENTUK JURNAL YANG BENAR (dicontoh dari PV-AP/REG/NAG/2026/11/02714, dibuat
-- lewat jalur create dan tersimpan utuh) - empat baris:
--     Cr 2.11.02 Utang Usaha        1.485.000   <- sudah ada
--     Dr 2.11.02 Utang Usaha        1.485.000   <- DITAMBAHKAN berkas ini
--     Dr 2.10.02 GR/IR              1.485.000   <- sudah ada
--     Cr 1.49.11 Uang Muka          1.485.000   <- DITAMBAHKAN berkas ini
--
-- CARA PAKAI
--   Jalankan seluruh berkas sekali di basis data produksi. Aman diulang:
--   tiap INSERT dijaga NOT EXISTS, jadi menjalankan dua kali tidak menggandakan.
--   Bagian 1 dan 4 hanya menampilkan, tidak mengubah apa pun.
--
--   Diuji di basis data sementara dengan salinan baris produksi apa adanya:
--   sesudah dijalankan jurnalnya 4 baris dan seimbang (debit = kredit =
--   2.970.000), SUM(total_ftr) = 1.485.000, dan jalan kedua kali tidak
--   menambah baris apa pun.
-- ============================================================================

-- ---------------------------------------------------------------------------
-- 1) SEBELUM - tampilkan keadaan sekarang
-- ---------------------------------------------------------------------------
SELECT 'SEBELUM - header' AS bagian, no_kbon, subtotal, dp_value, total, status
FROM kontrabon_h
WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02590-REV_01';

SELECT 'SEBELUM - kontrabon_ftr' AS bagian, COUNT(*) AS jml_baris,
       COALESCE(SUM(total_ftr), 0) AS jml_total_ftr
FROM kontrabon_ftr
WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02590-REV_01';

SELECT 'SEBELUM - jurnal' AS bagian, COUNT(*) AS jml_baris,
       SUM(debit_idr) AS debit, SUM(credit_idr) AS kredit,
       ROUND(SUM(debit_idr) - SUM(credit_idr), 2) AS selisih
FROM tbl_list_journal
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02590-REV_01';

-- ---------------------------------------------------------------------------
-- 2) Baris penaut PV -> FTR yang hilang
-- ---------------------------------------------------------------------------
INSERT INTO kontrabon_ftr
    (no_kbon, tgl_kbon, nama_supp, no_ftr, no_po, tgl_po, no_pi, curr,
     total_ftr, no_pv, no_bankout, tgl_bankout, no_coa, status,
     created_by, created_date)
SELECT 'PV-AP/REG/NAG/2026/10/02590-REV_01', '2026-10-14', 'SINU PANCA KHARISMA',
       'FTR/C/NAG/0926/00689', 'SMT/0926/056/04575', '2026-09-14', '', 'IDR',
       1485000, 'PV-AP/CBD/NAG/2026/09/00156', 'BK/BCA1979/NAG/0926/00175',
       '2026-09-17', '1.49.11', 'draft', 'dessy', '2026-10-16 14:46:08'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM kontrabon_ftr
    WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02590-REV_01'
      AND no_ftr  = 'FTR/C/NAG/0926/00689'
);

-- ---------------------------------------------------------------------------
-- 3) Dua kaki jurnal yang hilang
--    Kolom disamakan persis dengan dua baris jurnal PV ini yang sudah ada
--    (status, keterangan, approve_by/date, profit_center, supplier).
-- ---------------------------------------------------------------------------

-- 3a) Debit Utang Usaha sebesar potongan uang muka
INSERT INTO tbl_list_journal
    (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
     no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws,
     curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan,
     create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,
     profit_center, supplier)
SELECT 'PV-AP/REG/NAG/2026/10/02590-REV_01', '2026-10-16', 'AP - Kontrabon',
       '2.11.02', 'UTANG USAHA PIHAK KETIGA - AKSESORIS',
       '-', '-', '-', '0000-00-00', '-', '-',
       'IDR', 1.0000, 1485000.0000, 0.0000, 1485000.00, 0.00,
       'SECOND APPROVED', 'KONTRABON SINU PANCA KHARISMA',
       'dessy', '2026-10-16 14:46:08', 'willy', '2026-10-21 09:43:14', '',
       '0000-00-00 00:00:00', 'NAG', 'SINU PANCA KHARISMA'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_list_journal
    WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02590-REV_01'
      AND no_coa     = '2.11.02'
      AND debit      > 0
);

-- 3b) Kredit Uang Muka Pembelian atas FTR-nya
INSERT INTO tbl_list_journal
    (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
     no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws,
     curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan,
     create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,
     profit_center, supplier)
SELECT 'PV-AP/REG/NAG/2026/10/02590-REV_01', '2026-10-16', 'AP - Kontrabon',
       '1.49.11', 'UANG MUKA PEMBELIAN LOKAL - BAHAN BAKU / BAHAN PEMBANTU',
       '-', '-', 'FTR/C/NAG/0926/00689', '0000-00-00', '-', '-',
       'IDR', 1.0000, 0.0000, 1485000.0000, 0.00, 1485000.00,
       'SECOND APPROVED', 'KONTRABON SINU PANCA KHARISMA',
       'dessy', '2026-10-16 14:46:08', 'willy', '2026-10-21 09:43:14', '',
       '0000-00-00 00:00:00', 'NAG', 'SINU PANCA KHARISMA'
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM tbl_list_journal
    WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02590-REV_01'
      AND no_coa     = '1.49.11'
);

-- ---------------------------------------------------------------------------
-- 4) SESUDAH - harus: kontrabon_ftr 1 baris / 1.485.000, jurnal 4 baris,
--    selisih 0, dan "CBD or DP" di PDF sudah 1.485.000.
-- ---------------------------------------------------------------------------
SELECT 'SESUDAH - kontrabon_ftr' AS bagian, COUNT(*) AS jml_baris,
       COALESCE(SUM(total_ftr), 0) AS jml_total_ftr
FROM kontrabon_ftr
WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02590-REV_01';

SELECT 'SESUDAH - jurnal' AS bagian, no_coa, nama_coa, reff_doc,
       debit_idr, credit_idr
FROM tbl_list_journal
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02590-REV_01'
ORDER BY id;

SELECT 'SESUDAH - saldo jurnal' AS bagian, COUNT(*) AS jml_baris,
       SUM(debit_idr) AS debit, SUM(credit_idr) AS kredit,
       ROUND(SUM(debit_idr) - SUM(credit_idr), 2) AS selisih
FROM tbl_list_journal
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02590-REV_01';

-- Persis query yang dipakai pdf_pv_regular.php untuk baris "CBD or DP".
SELECT 'SESUDAH - angka CBD or DP di PDF' AS bagian, SUM(total_ftr) AS total_ftr
FROM kontrabon_ftr
WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02590-REV_01'
GROUP BY no_kbon;
