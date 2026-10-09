-- ============================================================================
-- Approval FTR CBD & FTR DP : menu persetujuan terpisah  (9 Okt 2026)
--
-- Dulu tombol Approve menempel di halaman daftar FTR. Sekarang persetujuan
-- punya menunya sendiri (approve_ftrcbd.php / approve_ftrdp.php), seperti menu
-- Approval lain. Hak aksesnya DIPISAH per modul supaya penyetuju CBD dan DP
-- bisa orang yang berbeda.
--
-- profit_center dibiarkan NULL, mengikuti baris menurole baru lainnya.
--
-- Dijalankan di lokal 9 Okt 2026. BELUM dijalankan di produksi.
-- ============================================================================

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 141, 'Approval FTR CBD', 'Approval FTR CBD', menu_group, 'Menu', NULL
FROM menurole WHERE id = 4
  AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM menurole) x WHERE x.id = 141);

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 142, 'Approval FTR DP', 'Approval FTR DP', menu_group, 'Menu', NULL
FROM menurole WHERE id = 4
  AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM menurole) x WHERE x.id = 142);

-- Hak akses diberikan ke pemakai yang SELAMA INI memang bisa meng-approve FTR,
-- yaitu pemegang menu 'FTR' yang Groupp-nya BUKAN STAFF - persis syarat tombol
-- Approve yang lama. Jadi tidak ada yang kehilangan kewenangan, dan tidak ada
-- yang tiba-tiba mendapatkannya.
INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT u.username, u.fullname, 'Approval FTR CBD', NOW(), 'migrasi'
FROM useraccess u
INNER JOIN userpassword p ON p.username = u.username
WHERE u.menu = 'FTR' AND IFNULL(p.Groupp,'') <> 'STAFF'
  AND NOT EXISTS (SELECT 1 FROM (SELECT username, menu FROM useraccess) v
                  WHERE v.username = u.username AND v.menu = 'Approval FTR CBD');

INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT u.username, u.fullname, 'Approval FTR DP', NOW(), 'migrasi'
FROM useraccess u
INNER JOIN userpassword p ON p.username = u.username
WHERE u.menu = 'FTR' AND IFNULL(p.Groupp,'') <> 'STAFF'
  AND NOT EXISTS (SELECT 1 FROM (SELECT username, menu FROM useraccess) v
                  WHERE v.username = u.username AND v.menu = 'Approval FTR DP');

SELECT id, menu, display_name, status FROM menurole WHERE menu LIKE '%FTR%' ORDER BY id;
SELECT menu, COUNT(*) pemakai FROM useraccess WHERE menu LIKE '%FTR%' GROUP BY menu;
