-- ============================================================================
-- MIGRASI PRODUKSI - 13 Okt 2026
-- Menu baru: Update BPB > WIP  (barang dalam proses / makloon)
--
-- WAJIB dijalankan SEBELUM pull kode baru, kalau tidak menunya tidak muncul.
--
-- AMAN DIULANG. Semua perintah memakai NOT EXISTS, jadi menjalankannya dua
-- kali tidak menduplikasi apa pun.
--
-- ----------------------------------------------------------------------------
-- KEADAAN PRODUKSI SAAT BERKAS INI DIBUAT (diperiksa 13 Okt 2026, read-only):
--   menurole id terbesar            : 142  (jadi 143 masih kosong)
--   menurole 'Update BPB WIP'       : 0 baris
--   useraccess 'Update BPB WIP'     : 0 baris
--   useraccess 'Update BPB Fabric'  : 3 pengguna (indro, nadia, willy)
--   Req_update_bpb_h / Req_update_bpb : SUDAH ADA berikut kolom `jenis`
--     -> migrasi 9 Okt 2026 (db_migrate_LIVE_20261009.sql) sudah terpasang,
--        menurole 139-142 pun sudah ada. Berkas ini hanya menambah 143.
--
-- TIDAK ADA tabel baru di sini. Jenis WIP memakai tabel yang sama dgn tiga
-- jenis lain (Req_update_bpb_h / Req_update_bpb), dibedakan oleh kolom
-- `jenis` yang berisi 'wip'.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. menurole : hak akses menu baru
--    Bentuknya disamakan dgn baris 139/140 (Accessories & General):
--    menu_group 'Cost Accounting', status 'Menu', profit_center kosong.
--    id dipatok 143 supaya urut dgn tiga jenis lainnya (112/139/140/143) dan
--    tidak bergantung pada AUTO_INCREMENT yang bisa berbeda.
-- ----------------------------------------------------------------------------
INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 143, 'Update BPB WIP', 'Update BPB WIP', 'Cost Accounting', 'Menu', ''
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE menu = 'Update BPB WIP')
  AND NOT EXISTS (SELECT 1 FROM menurole WHERE id = 143);

-- ----------------------------------------------------------------------------
-- 2. useraccess : diberikan ke pemegang 'Update BPB Fabric'
--    Dasarnya Fabric, BUKAN Accessories/General, karena ketiganya dipegang
--    orang yang sama (3 pengguna) dan Fabric-lah menu tertua yang pasti ada.
--    fullname & create_user disalin dari baris Fabric milik pengguna itu,
--    jadi tidak ada nama yang ditebak.
-- ----------------------------------------------------------------------------
INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT f.username, f.fullname, 'Update BPB WIP', NOW(), f.create_user
FROM useraccess f
WHERE f.menu = 'Update BPB Fabric'
  AND NOT EXISTS (
        SELECT 1 FROM useraccess w
        WHERE w.username = f.username AND w.menu = 'Update BPB WIP');

-- ============================================================================
-- PEMERIKSAAN - jalankan dan cocokkan dgn kolom "harus"
-- ============================================================================

-- (a) baris menurole-nya ada, bentuknya sama dgn Accessories/General
SELECT 'a. menurole Update BPB*' AS cek, id, menu, display_name, menu_group, status, profit_center
FROM menurole
WHERE menu LIKE 'Update BPB%'
ORDER BY id;
-- harus: 4 baris -> 112 Fabric, 139 Accessories, 140 General, 143 WIP

-- (b) jumlah pemegang tiap jenis harus SAMA
SELECT 'b. pemegang per jenis' AS cek, menu, COUNT(*) AS pengguna
FROM useraccess
WHERE menu LIKE 'Update BPB%'
GROUP BY menu
ORDER BY menu;
-- harus: keempatnya 3 pengguna

-- (c) tidak ada pengguna Fabric yang terlewat
SELECT 'c. Fabric tanpa WIP' AS cek, f.username
FROM useraccess f
WHERE f.menu = 'Update BPB Fabric'
  AND NOT EXISTS (SELECT 1 FROM useraccess w
                  WHERE w.username = f.username AND w.menu = 'Update BPB WIP');
-- harus: 0 baris

-- (d) menu baru lolos syarat yang dipakai module/menu.php
--     (useraccess JOIN menurole, status harus 'Menu')
SELECT 'd. lolos syarat menu.php' AS cek, u.username
FROM useraccess u
INNER JOIN menurole m ON m.menu = u.menu
WHERE u.menu = 'Update BPB WIP' AND m.status = 'Menu'
ORDER BY u.username;
-- harus: 3 baris (indro, nadia, willy)

-- (e) kolom `jenis` memang ada - dipakai utk memisahkan pengajuan WIP
SELECT 'e. kolom jenis' AS cek, COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'Req_update_bpb_h'
  AND COLUMN_NAME = 'jenis';
-- harus: 1 baris

-- (f) belum ada pengajuan jenis wip (wajar, menunya baru)
SELECT 'f. pengajuan per jenis' AS cek, jenis, COUNT(*) AS pengajuan
FROM Req_update_bpb_h
GROUP BY jenis
ORDER BY jenis;
-- harus: belum ada baris berjenis 'wip'
