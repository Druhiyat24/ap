-- =====================================================================
-- HAK AKSES tombol CANCEL di AP > Report > Projection > History Projection
-- (diminta user 13 Nov 2026: "tombol cancel nya dipakaikan userrole saja")
--
-- Menambah SATU butir menurole: id 149 'AP History Projection - Cancel'.
--   - id 148 sudah dipesan 'Bank Projection Other'
--     (db_migrate_LIVE_20261111_cash_projection.sql), jadi pakai 149.
--   - Butir ini BUKAN menu navbar; hanya muncul sebagai toggle di menu
--     User Role. Namanya tidak cocok dgn pola LIKE di module/menu.php.
--
-- AKIBAT setelah kode baru naik: tombol Cancel HILANG untuk semua user
-- sampai admin menyalakan butir ini di User Role (atau jalankan BAGIAN 2).
-- Server (batal_history_projection.php) juga menolak user tanpa hak ini.
--
-- Aman diulang (idempoten).
-- =====================================================================


-- ======================= BAGIAN 1: daftar hak akses =======================
-- Periksa dulu: harus KOSONG (id 149 belum dipakai butir lain).
SELECT id, menu FROM menurole WHERE id = 149 AND menu <> 'AP History Projection - Cancel';

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 149, 'AP History Projection - Cancel', 'AP History Projection - Cancel', 'AP', '', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE id = 149
                     OR menu = 'AP History Projection - Cancel');

-- Harapan: satu baris id 149.
SELECT id, menu, display_name, menu_group, status, profit_center
FROM menurole WHERE menu = 'AP History Projection - Cancel';


-- ======================= BAGIAN 2 (PILIHAN): beri hak ke user tertentu =======================
-- Boleh dilewati - hak bisa dinyalakan satu per satu lewat menu User Role.
-- Kalau dipakai: ganti daftar username di bawah, lalu hapus tanda komentar.
-- Saat ini pemegang menu History Projection: dessy, gabby, indro, mandy1, willy.
--
-- INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
-- SELECT p.username, p.FullName, 'AP History Projection - Cancel', NOW(), 'migrasi 20261113'
-- FROM userpassword p
-- WHERE p.username IN ('indro')
--   AND NOT EXISTS (SELECT 1 FROM useraccess x
--                   WHERE x.username = p.username AND x.menu = 'AP History Projection - Cancel');
--
-- SELECT username FROM useraccess WHERE menu = 'AP History Projection - Cancel' ORDER BY username;


-- ======================= BATAL (kalau perlu dikembalikan) =======================
--   DELETE FROM useraccess WHERE menu = 'AP History Projection - Cancel';
--   DELETE FROM menurole   WHERE id = 149 AND menu = 'AP History Projection - Cancel';
