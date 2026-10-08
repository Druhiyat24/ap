-- ===========================================================================
-- MIGRASI PRODUKSI : kolom profit_center untuk FTR CBD & FTR DP
--
-- JALANKAN SESUDAH db_migrate_LIVE_ftr_payment_method.sql.
-- Keduanya belum pernah dijalankan di produksi per 02 Okt 2026.
--
-- LATAR BELAKANG
-- Form Create FTR SUDAH punya pemilih Profit Center dan sifatnya wajib, tetapi
-- nilainya tidak pernah ikut tersimpan: insertftrcbd.php / insertftrdp.php
-- tidak pernah menerima field itu, dan tabelnya memang tidak punya kolomnya.
-- Di form, pilihan itu cuma dipakai dua hal yang sifatnya sementara:
--   1. menentukan sumber daftar PO (NAG -> MySQL, selainnya -> PostgreSQL)
--   2. dikirim ke modal Choose Supplier sebagai h_profit_center
-- Sesudah dokumen tersimpan, pilihannya hilang.
--
-- Itu baru terasa ketika FTR dibayar LANGSUNG dari Petty Cash Out (tanpa
-- PV-AP): jurnalnya butuh profit center, dan satu-satunya yang tersedia adalah
-- profit center akun kas yang membayar - padahal yang benar adalah profit
-- center dokumennya sendiri.
--
-- BARIS LAMA SENGAJA TIDAK DI-BACKFILL. Dibiarkan NULL karena profit center
-- dokumen lama memang tidak pernah tercatat di mana pun - menebaknya sekarang
-- berarti menulis data yang tidak pernah ada. Yang membacanya diminta mundur
-- ke profit center akun kas kalau kolom ini kosong, dan itu memang keadaan
-- terbaik yang bisa diketahui untuk dokumen lama.
--
-- Kolomnya NULL-able dan tanpa DEFAULT: nilai baru selalu datang dari form.
-- ===========================================================================

/* --------------------------------------------------------------------------
   LANGKAH 1 - PERIKSA DULU (jalankan sendiri, lihat hasilnya)
   -------------------------------------------------------------------------- */
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('ftr_cbd', 'ftr_dp')
  AND COLUMN_NAME = 'profit_center';
-- Kosong  = belum ada, lanjut ke LANGKAH 2.
-- Ada isi = sudah pernah dijalankan, JANGAN diulang.


/* --------------------------------------------------------------------------
   LANGKAH 2 - TAMBAH KOLOM
   Ditaruh sesudah `item_type` supaya berdekatan dgn kolom lain yang sama-sama
   datang dari isian header form, bukan terlempar ke ujung tabel.
   -------------------------------------------------------------------------- */
ALTER TABLE ftr_cbd
    ADD COLUMN profit_center VARCHAR(20) NULL AFTER item_type;

ALTER TABLE ftr_dp
    ADD COLUMN profit_center VARCHAR(20) NULL AFTER item_type;


/* --------------------------------------------------------------------------
   LANGKAH 3 - VERIFIKASI
   -------------------------------------------------------------------------- */
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, ORDINAL_POSITION
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('ftr_cbd', 'ftr_dp')
  AND COLUMN_NAME = 'profit_center';

SELECT 'ftr_cbd' AS tabel, COUNT(*) AS baris,
       SUM(profit_center IS NULL OR profit_center = '') AS belum_terisi
FROM ftr_cbd
UNION ALL
SELECT 'ftr_dp', COUNT(*),
       SUM(profit_center IS NULL OR profit_center = '')
FROM ftr_dp;
-- Sesaat setelah migrasi: belum_terisi = seluruh baris.


/* --------------------------------------------------------------------------
   PEMBATALAN (kalau perlu mundur)
   -------------------------------------------------------------------------- */
-- ALTER TABLE ftr_cbd DROP COLUMN profit_center;
-- ALTER TABLE ftr_dp  DROP COLUMN profit_center;
