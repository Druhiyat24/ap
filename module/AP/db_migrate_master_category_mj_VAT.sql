-- ============================================================================
-- Tambah kategori Memorial Journal baru: VAT.
-- Dipakai sebagai Type default tab "PPN Masukan" di create_memorial_journal.php
-- (jurnal PPN Masukan otomatis masuk kategori ini).
--
-- Aman diulang: HAVING dievaluasi SETELAH agregasi, jadi baris hasil hanya
-- muncul kalau 'VAT' memang belum ada. (Pakai WHERE NOT EXISTS di sini SALAH:
-- filternya jalan sebelum agregasi, MAX jadi NULL, dan barisnya tetap masuk.)
-- id_cmj mengikuti pola yang ada: nomor CMJ terbesar + 1.
--
-- JALANKAN JUGA DI PRODUKSI.
-- ============================================================================
INSERT INTO master_category_mj (id_cmj, nama_cmj, status_hris)
SELECT CONCAT('CMJ', LPAD(MAX(CAST(SUBSTRING(id_cmj, 4) AS UNSIGNED)) + 1, 3, '0')), 'VAT', NULL
FROM master_category_mj
HAVING SUM(nama_cmj = 'VAT') = 0;
