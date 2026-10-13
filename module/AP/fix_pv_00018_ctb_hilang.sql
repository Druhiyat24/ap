-- ============================================================================
-- PERBAIKAN DATA - 13 Okt 2026
-- PV/NAG/0926/00018 : "Charge To Buyer" hilang jadi "-"
--
-- JANGAN dijalankan sebelum dibaca. Satu dokumen, satu kolom.
--
-- ----------------------------------------------------------------------------
-- SEBABNYA
-- Halaman edit PV EXIM (edit-paymentvoucher-exim.php) mengisi field readonly
-- "Charge To Buyer" dari hasil TERKAAN: ia mencari buyer di tbl_pv_memo_temp
-- milik user yang sedang login. Itu alur CREATE (pilih memo dulu, buyer
-- menyusul). Saat halaman EDIT dibuka, tabel sementara itu kosong, sehingga
-- field tampil "-" lalu ikut tersimpan kembali sebagai "-".
-- Sudah diperbaiki di kode: field itu sekarang memakai nilai yang TERSIMPAN
-- di dokumen, dan hanya jatuh ke terkaan memo kalau dokumennya belum punya.
--
-- KEADAAN PRODUKSI (dibaca read-only 13 Okt 2026):
--   tbl_pv_h      id 8864  ctb = '-'                                status Approved
--   tbl_edit_pv_h id 8739  ctb = 'TEEJAY MAURITIUS PRIVATE LIMITED' (snapshot pra-edit)
--   update_by/update_date : gabby, 2026-09-02 10:40:18
--   tbl_log_edit_pv id 1605 : gabby, 2026-09-02 10:40:18  (edit yang sama)
--   'TEEJAY MAURITIUS PRIVATE LIMITED' ADA di mastersupplier dgn tipe_sup='C'
--
-- DAMPAKNYA TERBATAS: kolom ctb hanya dibaca pdf_payvoucher.php dan
-- ekspor_payment_voucher.php. TIDAK dipakai penjurnalan, jadi tidak ada
-- jurnal/saldo yang perlu diubah - ini murni perbaikan tampilan dokumen.
--
-- CAKUPAN: hanya 1 dokumen. Dari 73 PV yang pernah diedit lewat halaman ini,
-- hanya dokumen inilah yang BISA DIBUKTIKAN kehilangan ctb, sebab
-- tbl_edit_pv_h menyimpan SATU snapshot per PV (yang terakhir saja) - kalau
-- ada kehilangan yang lebih lama, buktinya sudah tertimpa.
-- ============================================================================

START TRANSACTION;

-- Sebelum: lihat nilainya dulu
SELECT 'SEBELUM' AS tahap, id, no_pv, pv_form_type, ctb, status, update_by, update_date
FROM tbl_pv_h
WHERE no_pv = 'PV/NAG/0926/00018';

-- Perbaikan. Dijaga nilai lamanya ('-') dan id-nya, jadi:
--   * aman diulang  : jalan kedua kali mengubah 0 baris
--   * aman salah klik: kalau isinya sudah bukan '-', tidak ada yang tersentuh
UPDATE tbl_pv_h
SET ctb = 'TEEJAY MAURITIUS PRIVATE LIMITED'
WHERE id = 8864
  AND no_pv = 'PV/NAG/0926/00018'
  AND ctb = '-';
-- harus: 1 row affected (putaran kedua: 0)

-- Sesudah: pastikan hasilnya
SELECT 'SESUDAH' AS tahap, id, no_pv, pv_form_type, ctb, status, update_by, update_date
FROM tbl_pv_h
WHERE no_pv = 'PV/NAG/0926/00018';
-- harus: ctb = 'TEEJAY MAURITIUS PRIVATE LIMITED'

COMMIT;

-- ============================================================================
-- PEMERIKSAAN SESUDAH COMMIT
-- ============================================================================

-- (a) cocok lagi dgn snapshot pra-edit
SELECT 'a. cocok dgn snapshot' AS cek, h.ctb AS ctb_sekarang, e.ctb AS ctb_snapshot,
       IF(h.ctb = e.ctb, 'COCOK', 'MASIH BEDA') AS hasil
FROM tbl_pv_h h
INNER JOIN tbl_edit_pv_h e ON e.no_pv = h.no_pv
WHERE h.no_pv = 'PV/NAG/0926/00018';
-- harus: COCOK

-- (b) tidak ada dokumen lain yang ikut tersentuh
SELECT 'b. dokumen tersentuh' AS cek, COUNT(*) AS jumlah
FROM tbl_pv_h
WHERE ctb = 'TEEJAY MAURITIUS PRIVATE LIMITED';
-- bandingkan dgn hitungan sebelum dijalankan; harus naik tepat 1

-- (c) apakah masih ada kehilangan ctb lain yang bisa dibuktikan
SELECT 'c. sisa kehilangan' AS cek, h.no_pv, h.pv_form_type, h.ctb AS sekarang, e.ctb AS snapshot
FROM tbl_pv_h h
INNER JOIN tbl_edit_pv_h e ON e.no_pv = h.no_pv
WHERE h.update_by IS NOT NULL AND h.update_by <> ''
  AND (h.ctb IS NULL OR h.ctb = '' OR h.ctb = '-')
  AND e.ctb IS NOT NULL AND e.ctb <> '' AND e.ctb <> '-';
-- harus: 0 baris
