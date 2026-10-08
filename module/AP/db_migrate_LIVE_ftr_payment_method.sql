-- ===========================================================================
-- MIGRASI PRODUKSI : kolom payment_method + item_type untuk FTR CBD (dan FTR DP)
--
-- Dipakai oleh penambahan input "Payment Method" (Transfer / Cash) di
-- formftrcbd.php. Mengacu Project #79:
--   "Penambahan input Payment Method pada menu FTR CBD dan FTR DP. Jika
--    Payment Method yang dipilih adalah Cash, maka kolom tanda tangan (Sign)
--    ditambahkan menjadi 2 bagian, yaitu Cashier dan Received By"
--
-- Diperiksa ke produksi (signalbit_erp @ servernag, 01 Okt 2026):
--   ftr_cbd : 755 baris, 24 kolom, TIDAK ada kolom payment/method
--   ftr_dp  :  29 baris, 24 kolom, TIDAK ada kolom payment/method
--
-- BARIS LAMA SENGAJA TIDAK DI-BACKFILL. Dibiarkan NULL, bukan diisi
-- 'Transfer', karena cara bayar dokumen lama tidak pernah tercatat - menebaknya
-- sekarang berarti menulis data yang tidak pernah ada. Yang membaca kolom ini
-- (mis. tab Petty Cash Out yang menyaring Payment Method = Cash) cukup
-- memperlakukan NULL sebagai "tidak diketahui", dan itu memang keadaannya.
--
-- ITEM TYPE ikut ditambah di sini. Alasannya: untuk FTR ber-Payment Method
-- Cash ada kemungkinan PV-AP CBD-nya TIDAK pernah dibuat, padahal jenis item
-- itu biasanya baru direkam di langkah PV - jadi kalau tidak dicatat di FTR,
-- informasinya hilang sama sekali. Pilihannya dibaca dari sumber yang SAMA
-- dgn menu PV-AP CBD/DP: pv_mapping_jurnal_dp WHERE status = 'Y' (Accessories,
-- Fabric, Fixed Asset, Others, Service, Sparepart - diperiksa sama persis di
-- produksi maupun lokal), sehingga tidak ada daftar kembar yang bisa melenceng.
--
-- Kolomnya NULL-able dan tanpa DEFAULT: nilai baru selalu datang dari form,
-- jadi DEFAULT hanya akan menyamarkan baris yang gagal mengirim nilainya.
-- ===========================================================================

/* --------------------------------------------------------------------------
   LANGKAH 1 - PERIKSA DULU (jalankan sendiri, lihat hasilnya)
   -------------------------------------------------------------------------- */
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('ftr_cbd', 'ftr_dp')
  AND COLUMN_NAME IN ('payment_method', 'item_type');
-- Kosong  = belum ada, lanjut ke LANGKAH 2.
-- Ada isi = sudah pernah dijalankan, JANGAN diulang.


/* --------------------------------------------------------------------------
   LANGKAH 2 - TAMBAH KOLOM
   Ditaruh sesudah `curr` supaya berdekatan dgn kolom nilai & mata uang,
   bukan terlempar ke ujung tabel.
   -------------------------------------------------------------------------- */
ALTER TABLE ftr_cbd
    ADD COLUMN payment_method VARCHAR(20) NULL AFTER curr,
    ADD COLUMN item_type      VARCHAR(50) NULL AFTER payment_method;

-- FTR DP belum dikerjakan formnya, tetapi kolomnya disiapkan sekalian supaya
-- kedua tabel seragam dan tab Petty Cash Out nanti bisa membaca keduanya
-- dengan query yang sama.
ALTER TABLE ftr_dp
    ADD COLUMN payment_method VARCHAR(20) NULL AFTER curr,
    ADD COLUMN item_type      VARCHAR(50) NULL AFTER payment_method;


/* --------------------------------------------------------------------------
   LANGKAH 3 - VERIFIKASI
   -------------------------------------------------------------------------- */
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, ORDINAL_POSITION
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('ftr_cbd', 'ftr_dp')
  AND COLUMN_NAME IN ('payment_method', 'item_type');

SELECT 'ftr_cbd' AS tabel, COUNT(*) AS baris,
       SUM(payment_method IS NULL)        AS pm_belum_terisi,
       SUM(payment_method = 'Transfer')   AS transfer,
       SUM(payment_method = 'Cash')       AS cash,
       SUM(item_type IS NULL)             AS it_belum_terisi
FROM ftr_cbd
UNION ALL
SELECT 'ftr_dp', COUNT(*),
       SUM(payment_method IS NULL),
       SUM(payment_method = 'Transfer'),
       SUM(payment_method = 'Cash'),
       SUM(item_type IS NULL)
FROM ftr_dp;
-- Sesaat setelah migrasi: belum_terisi = seluruh baris, transfer = 0, cash = 0.


/* --------------------------------------------------------------------------
   PEMBATALAN (kalau perlu mundur)
   -------------------------------------------------------------------------- */
-- ALTER TABLE ftr_cbd DROP COLUMN payment_method, DROP COLUMN item_type;
-- ALTER TABLE ftr_dp  DROP COLUMN payment_method, DROP COLUMN item_type;
