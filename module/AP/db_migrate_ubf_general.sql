-- ============================================================================
-- Update BPB - GENERAL : pendaftaran menu & hak akses  (8 Okt 2026)
--
-- Jenis ketiga setelah Fabric (menurole 112) dan Accessories (139).
-- Dokumennya dibaca dari tabel `bpb` dgn bpbno_int LIKE 'GEN/%'.
--
-- CATATAN profit_center: dibiarkan NULL, SAMA seperti baris Accessories (139).
-- Itu bukan kelalaian - query menu "BPB Garment" menyaring dgn
-- `profit_center != 'NAK'`, dan di SQL `NULL != 'NAK'` hasilnya NULL (bukan
-- TRUE), sehingga baris ini tidak ikut tersaring ke sana. Kalau diisi 'NAG',
-- id 140 akan muncul di GROUP_CONCAT menu BPB Garment.
--
-- Dijalankan di lokal 8 Okt 2026. BELUM dijalankan di produksi.
-- ============================================================================

-- 1. baris menurole. id 140 ditulis eksplisit supaya sama di lokal & produksi.
INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 140, 'Update BPB General', 'Update BPB General', menu_group, 'Menu', NULL
FROM menurole WHERE id = 139
  AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM menurole) x WHERE x.id = 140);

-- 2. hak akses: diberikan ke pemakai yang SUDAH punya Update BPB Accessories,
--    supaya tidak ada yang kelewat dan tidak ada yang kebanyakan.
INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT u.username, u.fullname, 'Update BPB General', NOW(), 'migrasi'
FROM useraccess u
WHERE u.menu = 'Update BPB Accessories'
  AND NOT EXISTS (
      SELECT 1 FROM (SELECT username, menu FROM useraccess) v
      WHERE v.username = u.username AND v.menu = 'Update BPB General');

-- 3. pemeriksaan
SELECT id, menu, display_name, status, profit_center FROM menurole WHERE menu LIKE 'Update BPB%' ORDER BY id;
SELECT menu, GROUP_CONCAT(username ORDER BY username) pemakai
FROM useraccess WHERE menu LIKE 'Update BPB%' GROUP BY menu;
