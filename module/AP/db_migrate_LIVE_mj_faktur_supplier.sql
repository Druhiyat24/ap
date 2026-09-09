-- ============================================================================
-- MJ Upload: kolom No Faktur / Tgl Faktur / Supplier
-- ============================================================================
-- KEPUTUSAN: ketiga nilai ini HANYA disimpan di tbl_list_journal (yang kolomnya
-- sudah ada). tbl_memorial_journal SENGAJA TIDAK diberi kolom ini.
--
-- Yang ditambahkan di sini CUMA tabel STAGING tbl_memorial_journal_temp.
-- Itu bukan penyimpanan, melainkan tempat singgah: baris hasil upload menunggu
-- di sana antara tahap "upload" dan tahap "save", dan tabel detail di layar
-- membacanya dari sana. Isinya dihapus otomatis setelah save
-- (DELETE FROM tbl_memorial_journal_temp WHERE create_by = ...).
-- Tanpa kolom ini, nilai dari template tidak punya jalan sampai ke
-- tbl_list_journal.
--
-- KONSEKUENSI YANG SUDAH DIKETAHUI DAN DITERIMA:
--   Form EDIT MJ memuat baris detailnya DARI tbl_memorial_journal
--   (edit-memorial-journal.php baris 425), lalu insert_memorial_journal_edit.php
--   menulis ulang tbl_list_journal dari isi form itu. Karena tbl_memorial_journal
--   tidak menyimpan ketiga nilai ini, MJ yang PERNAH DIEDIT akan kehilangan
--   No Faktur / Tgl Faktur / Supplier di tbl_list_journal - tanpa pesan error.
--   Data dari upload yang TIDAK pernah diedit tetap utuh.
--   Kalau kelak ingin tahan-edit tanpa menyimpan di tbl_memorial_journal, form
--   edit harus diubah agar membaca ketiga nilai itu dari tbl_list_journal.
--
-- Aman diulang: ADD COLUMN IF NOT EXISTS didukung MariaDB.
-- ============================================================================

ALTER TABLE tbl_memorial_journal_temp
  ADD COLUMN IF NOT EXISTS faktur_pajak     VARCHAR(100) NULL AFTER reff_date,
  ADD COLUMN IF NOT EXISTS tgl_faktur_pajak DATE         NULL AFTER faktur_pajak,
  ADD COLUMN IF NOT EXISTS supplier         VARCHAR(255) NULL AFTER tgl_faktur_pajak;

-- VERIFIKASI: harus muncul TEPAT 3 baris, semuanya milik
-- tbl_memorial_journal_temp. Kalau tbl_memorial_journal ikut muncul, berarti
-- ada migrasi lain yang terlanjur menambahkannya.
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('tbl_memorial_journal_temp','tbl_memorial_journal')
  AND COLUMN_NAME IN ('faktur_pajak','tgl_faktur_pajak','supplier')
ORDER BY TABLE_NAME, COLUMN_NAME;
