-- ============================================================================
-- MIGRASI DATA MENU & HAK AKSES UNTUK PRODUKSI (LIVE)
--   1) Rename menu "Document Handover - ..." (menurole id 66-74, 76, 77)
--   2) Menu baru: SJ (Document Handover - Accept SJ ...) + "Invoice Received"
--   3) Grant "Invoice Received" ke user lama (indro/willy/steven)
--
-- ASUMSI: id menurole 66-74,76,77 di live SAMA dengan lokal (menu lama bawaan app).
-- Rename useraccess memakai JEMBATAN by-id (JOIN ke menurole) -> TIDAK perlu tahu
-- nama menu lama, dan aman walau tiap id nama lamanya berbeda.
-- Idempoten (aman diulang). Untuk MariaDB (dan MySQL). Jalankan lewat phpMyAdmin/SQL.
--
-- URUTAN PENTING: jalankan Bagian 1 (useraccess) SEBELUM Bagian 2 (menurole),
--   karena Bagian 1 men-join saat menurole masih bernama lama.
-- ============================================================================

-- ============================================================================
-- BAGIAN 1 — rename useraccess mengikuti id menurole (bridge by-id).
-- Menyetel useraccess.menu ke nama BARU untuk semua user yang punya akses menu
-- id 66-74/76/77, apapun nama lamanya.
-- ============================================================================
UPDATE useraccess ua
JOIN menurole mr ON mr.menu = ua.menu
SET ua.menu = CASE mr.id
    WHEN 66 THEN 'Document Handover - Invoice'
    WHEN 67 THEN 'Document Handover - Create Invoice'
    WHEN 68 THEN 'Document Handover - Transfer Invoice Finance To Accounting'
    WHEN 69 THEN 'Document Handover - Accept Invoice Accounting'
    WHEN 70 THEN 'Document Handover - Transfer Invoice Accounting To Purchasing'
    WHEN 71 THEN 'Document Handover - Accept Invoice Purchasing'
    WHEN 72 THEN 'Document Handover - Transfer Invoice Purchasing To Finance'
    WHEN 73 THEN 'Document Handover - Accept Invoice Finance'
    WHEN 74 THEN 'Document Handover - Reverse Invoice'
    WHEN 76 THEN 'Document Handover - Accept BPB Warehouse To Accounting'
    WHEN 77 THEN 'Document Handover - BPB & SJ'
    ELSE ua.menu END
WHERE mr.id IN (66,67,68,69,70,71,72,73,74,76,77);

-- ============================================================================
-- BAGIAN 2 — rename menurole by id.
-- ============================================================================
UPDATE menurole SET menu = CASE id
    WHEN 66 THEN 'Document Handover - Invoice'
    WHEN 67 THEN 'Document Handover - Create Invoice'
    WHEN 68 THEN 'Document Handover - Transfer Invoice Finance To Accounting'
    WHEN 69 THEN 'Document Handover - Accept Invoice Accounting'
    WHEN 70 THEN 'Document Handover - Transfer Invoice Accounting To Purchasing'
    WHEN 71 THEN 'Document Handover - Accept Invoice Purchasing'
    WHEN 72 THEN 'Document Handover - Transfer Invoice Purchasing To Finance'
    WHEN 73 THEN 'Document Handover - Accept Invoice Finance'
    WHEN 74 THEN 'Document Handover - Reverse Invoice'
    WHEN 76 THEN 'Document Handover - Accept BPB Warehouse To Accounting'
    WHEN 77 THEN 'Document Handover - BPB & SJ'
    ELSE menu END
WHERE id IN (66,67,68,69,70,71,72,73,74,76,77);

-- ============================================================================
-- BAGIAN 3 — menu BARU: SJ approval placeholder (belum ada halaman/akses).
-- id dihitung MAX(id)+1 (bukan hardcode) supaya tak bentrok di live.
-- ============================================================================
INSERT INTO menurole (id, menu, menu_group, profit_center)
SELECT x.nid, 'Document Handover - Accept SJ Warehouse To Accounting', 'AP', 'NAG'
FROM (SELECT COALESCE(MAX(id),0)+1 AS nid FROM menurole) x
WHERE NOT EXISTS (SELECT 1 FROM menurole mr WHERE mr.menu = 'Document Handover - Accept SJ Warehouse To Accounting');

-- ============================================================================
-- BAGIAN 4 — menu BARU: "Invoice Received" (submenu Kontrabon New / IR baru).
-- ============================================================================
INSERT INTO menurole (id, menu, display_name, menu_group, profit_center)
SELECT x.nid, 'Invoice Received', 'Invoice Received', 'AP', 'NAG'
FROM (SELECT COALESCE(MAX(id),0)+1 AS nid FROM menurole) x
WHERE NOT EXISTS (SELECT 1 FROM menurole mr WHERE mr.menu = 'Invoice Received');

-- Grant "Invoice Received" ke user lama supaya akses tidak hilang.
INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT s.username,
       COALESCE((SELECT ua2.fullname FROM useraccess ua2 WHERE ua2.username = s.username AND ua2.fullname IS NOT NULL AND ua2.fullname <> '' LIMIT 1), s.username),
       'Invoice Received', NOW(), 'system'
FROM (SELECT 'indro' AS username UNION ALL SELECT 'willy' UNION ALL SELECT 'steven') s
WHERE NOT EXISTS (SELECT 1 FROM useraccess ua3 WHERE ua3.username = s.username AND ua3.menu = 'Invoice Received');

-- ============================================================================
-- SELESAI. Setelah ini, menu "Invoice Received" bisa di-assign ke user lain
-- lewat Manage User Role (userrole.php) tanpa ubah kode.
-- ============================================================================
