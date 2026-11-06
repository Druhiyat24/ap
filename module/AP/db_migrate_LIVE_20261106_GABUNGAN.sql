-- ============================================================================
-- AP - MIGRASI GABUNGAN, SEKALI JALAN.
-- Disusun 6 Nov 2026 atas permintaan pemakai: "query nya satukan saja biar
-- sekali jalan". Menggantikan lima berkas yang sebelumnya terpisah:
--     db_migrate_LIVE_20261104_payment_plan.sql
--     fix_payment_plan_awalan_ppu.sql
--     db_migrate_LIVE_20261105_payment_plan_awal.sql
--     db_migrate_LIVE_20261105_ap_projection.sql
--     db_migrate_LIVE_20261106_ap_projection_history.sql
-- Kelimanya boleh diabaikan sesudah berkas ini dijalankan.
--
-- JALANKAN DI PRODUKSI (10.10.5.12 / signalbit_erp).
--
-- ----------------------------------------------------------------------------
-- AMAN DIJALANKAN BERULANG, dan itu disengaja: tidak ada catatan pasti
-- bagian mana yang sudah pernah dijalankan di produksi, jadi seluruh isinya
-- dibuat tidak merusak kalau diulang.
--     CREATE TABLE IF NOT EXISTS  - tabel yang sudah ada dibiarkan apa adanya
--     INSERT IGNORE / NOT EXISTS  - baris yang sudah ada TIDAK ditimpa
--     UPDATE berpenyaring          - mengenai 0 baris kalau sudah beres
-- Yang TIDAK ada di sini: pemberian hak akses. Lihat BAGIAN 6.
--
-- TIDAK ADA satu pun DROP terhadap tabel milik aplikasi. Dua DROP yang ada
-- hanya mengenai tabel cadangan bak_pp_awalan_* buatan berkas ini sendiri.
-- ============================================================================


-- ============================================================================
-- BAGIAN 0 - POTRET SEBELUM. Jalankan dan SIMPAN hasilnya; kalau nanti ada
-- yang janggal, inilah pembandingnya.
-- ============================================================================
SELECT 'sebelum' AS saat,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_payment_plan_h')        AS ada_pp_h,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_payment_plan_det')      AS ada_pp_det,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_payment_plan_awal')     AS ada_pp_awal,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_h')  AS ada_prj_h,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_det') AS ada_prj_det,
  (SELECT COUNT(*) FROM menurole WHERE id IN (145, 146, 147))                    AS menurole_terpasang;

-- id 145/146/147 TIDAK BOLEH sudah dipakai menu lain. Kalau baris di bawah
-- memunculkan nama selain Payment Plan / AP Projection Report /
-- AP History Projection, BERHENTI dan beri tahu dulu.
SELECT id, menu, display_name FROM menurole WHERE id IN (145, 146, 147) ORDER BY id;


-- ============================================================================
-- BAGIAN 1 - TABEL PAYMENT PLAN
-- Rencana bayar per voucher. _h dan _det bertaut lewat NILAI doc_number,
-- bukan id - itu sebabnya perbaikan awalan di Bagian 2 harus menyentuh
-- keduanya dalam satu transaksi.
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ap_payment_plan_h` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_number` varchar(100) NOT NULL,
  `plan_date` date DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'POST',
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `cancel_by` varchar(100) DEFAULT NULL,
  `cancel_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_apph_doc` (`doc_number`),
  KEY `ix_apph_plan_date` (`plan_date`),
  KEY `ix_apph_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ap_payment_plan_det` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_number` varchar(100) NOT NULL,
  `no_kbon` varchar(255) NOT NULL,
  `plan_date` date DEFAULT NULL,
  `curr` varchar(50) DEFAULT NULL,
  `amount` decimal(16,2) DEFAULT 0.00,
  `keterangan` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `ix_appd_doc` (`doc_number`),
  KEY `ix_appd_kbon` (`no_kbon`),
  KEY `ix_appd_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================================
-- BAGIAN 2 - AWALAN NOMOR DOKUMEN: AP-PP/... -> PPU/...
-- Awalannya diganti atas permintaan pemakai 5 Nov 2026; kodenya sudah memakai
-- PPU. Bagian ini membereskan dokumen yang terlanjur bernomor lama.
-- Kalau tidak ada satu pun dokumen AP-PP, seluruh UPDATE mengenai 0 baris.
-- Hanya AWALANNYA yang berganti - AP-PP/1126/00001 menjadi PPU/1126/00001,
-- jadi urutan yang sudah dipakai tim AP tidak berubah.
-- ============================================================================
DROP TABLE IF EXISTS bak_pp_awalan_h;
DROP TABLE IF EXISTS bak_pp_awalan_det;

CREATE TABLE bak_pp_awalan_h AS
SELECT * FROM ap_payment_plan_h WHERE doc_number LIKE 'AP-PP/%';

CREATE TABLE bak_pp_awalan_det AS
SELECT * FROM ap_payment_plan_det WHERE doc_number LIKE 'AP-PP/%';

SELECT (SELECT COUNT(*) FROM bak_pp_awalan_h)   AS header_dicadangkan,
       (SELECT COUNT(*) FROM bak_pp_awalan_det) AS detail_dicadangkan;


-- ---------------------------------------------------------------------------
-- LANGKAH 2 - GANTI. Dua tabel dalam SATU transaksi.
--
-- DETAIL DIGANTI LEBIH DULU, memakai daftar nomor dari tabel header yang
-- MASIH memakai nama lama. Kalau headernya diganti duluan, tautan nilai itu
-- putus dan baris detailnya tidak bisa ditemukan lagi.
--
-- Baris yang nomor barunya sudah dipakai dokumen lain DILEWATI (NOT EXISTS),
-- jadi perintah ini tidak akan pernah membuat dua dokumen bernomor sama.
-- ---------------------------------------------------------------------------
START TRANSACTION;

UPDATE ap_payment_plan_det d
INNER JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
SET d.doc_number = CONCAT('PPU/', SUBSTRING(d.doc_number, 7))
WHERE d.doc_number LIKE 'AP-PP/%'
  AND NOT EXISTS (SELECT 1 FROM bak_pp_awalan_h b
                  WHERE b.doc_number = h.doc_number
                    AND EXISTS (SELECT 1 FROM ap_payment_plan_h x
                                WHERE x.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7))));

UPDATE ap_payment_plan_h h
SET h.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7))
WHERE h.doc_number LIKE 'AP-PP/%'
  AND NOT EXISTS (SELECT 1 FROM bak_pp_awalan_h b
                  WHERE b.doc_number = h.doc_number
                    AND EXISTS (SELECT 1 FROM ap_payment_plan_h x
                                WHERE x.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7))));

COMMIT;


-- ============================================================================
-- BAGIAN 3 - TABEL RENCANA AWAL (ap_payment_plan_awal)
-- Lapis KEDUA tanggal proyeksi: Kamis di pekan Minggu-Sabtu milik jatuh
-- temponya. Laporan proyeksi tetap jalan tanpa tabel ini (rumusnya dihitung
-- langsung), jadi bagian ini tidak mendesak - tapi dgn tabelnya ada, rencana
-- awal bisa DISETEL TANGAN per voucher dan setelan itu yang dipakai.
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ap_payment_plan_awal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_kbon` varchar(255) NOT NULL,
  `duedate` date DEFAULT NULL COMMENT 'jatuh tempo saat rencana awal dihitung',
  `plan_awal` date NOT NULL COMMENT 'Kamis di minggu Minggu sampai Sabtu milik duedate',
  `pay_dow` tinyint(2) NOT NULL DEFAULT 5 COMMENT 'DAYOFWEEK hari bayar, 5 = Kamis',
  `sumber` varchar(20) NOT NULL DEFAULT 'AUTO' COMMENT 'AUTO = dihitung sistem, MANUAL = disetel orang',
  `created_by` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ppa_no_kbon` (`no_kbon`),
  KEY `ix_ppa_plan_awal` (`plan_awal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Pengisian awal. INSERT IGNORE + kunci unik = baris yang sudah ada,
-- termasuk yang sudah disetel tangan, TIDAK ditimpa.
INSERT IGNORE INTO ap_payment_plan_awal
    (no_kbon, duedate, plan_awal, pay_dow, sumber, created_by, created_date)
SELECT k.no_kbon,
       k.tgl_tempo,
       DATE_ADD(k.tgl_tempo, INTERVAL (5 - DAYOFWEEK(k.tgl_tempo)) DAY),
       5, 'AUTO', 'MIGRASI', NOW()
FROM kontrabon_h k
WHERE k.status IN ('Approved','FIRST APPROVED','SECOND APPROVED')
  AND k.tgl_tempo IS NOT NULL AND k.tgl_tempo <> '0000-00-00';

-- Voucher saldo awal & cicilan juga ikut - laporan proyeksi memuat keduanya,
-- jadi rencana awalnya tidak boleh kosong.
INSERT IGNORE INTO ap_payment_plan_awal
    (no_kbon, duedate, plan_awal, pay_dow, sumber, created_by, created_date)
SELECT s.no_kbon,
       s.tgl_tempo,
       DATE_ADD(s.tgl_tempo, INTERVAL (5 - DAYOFWEEK(s.tgl_tempo)) DAY),
       5, 'AUTO', 'MIGRASI', NOW()
FROM ap_saldo_payment_voucher s
WHERE s.tgl_tempo IS NOT NULL AND s.tgl_tempo <> '0000-00-00';

INSERT IGNORE INTO ap_payment_plan_awal
    (no_kbon, duedate, plan_awal, pay_dow, sumber, created_by, created_date)
SELECT i.no_kbon_det,
       i.tgl_tempo,
       DATE_ADD(i.tgl_tempo, INTERVAL (5 - DAYOFWEEK(i.tgl_tempo)) DAY),
       5, 'AUTO', 'MIGRASI', NOW()
FROM kontrabon_h_installment_detail i
WHERE i.tgl_tempo IS NOT NULL AND i.tgl_tempo <> '0000-00-00';


-- ============================================================================
-- BAGIAN 4 - TABEL RIWAYAT LAPORAN PROYEKSI (tombol Save History)
-- Sengaja TIDAK menumpang tbl_history_projection_h/_det: dua tabel itu MILIK
-- AR dan sudah terpakai. AR & AP berbagi satu basis data, jadi menulis baris
-- AP ke sana akan memunculkannya di menu History Projection milik AR.
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ap_projection_history_h` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_number` varchar(25) NOT NULL,
  `periode_dari` date NOT NULL,
  `periode_sampai` date NOT NULL,
  `nama_supp` varchar(255) NOT NULL DEFAULT 'ALL',
  `type` varchar(10) NOT NULL DEFAULT 'daily',
  `per_tanggal` date DEFAULT NULL,
  `kurs` text DEFAULT NULL,
  `jml_baris` int(11) NOT NULL DEFAULT 0,
  `total_idr` decimal(20,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'ACTIVE',
  `created_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cancel_by` varchar(100) DEFAULT NULL,
  `cancel_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doc_number` (`doc_number`),
  KEY `periode` (`periode_dari`,`periode_sampai`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ap_projection_history_det` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `doc_number` varchar(25) NOT NULL,
  `no_urut` int(11) NOT NULL DEFAULT 0,
  `supplier` varchar(255) DEFAULT NULL,
  `no_kbon` varchar(255) DEFAULT NULL,
  `tgl_kbon` date DEFAULT NULL,
  `term` varchar(20) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `plan_date` date DEFAULT NULL,
  `basis` varchar(20) DEFAULT NULL,
  `relasi` varchar(50) DEFAULT NULL,
  `item_type` varchar(100) DEFAULT NULL,
  `flexibility` varchar(50) DEFAULT NULL,
  `curr` varchar(10) DEFAULT NULL,
  `invoice_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `rate` decimal(20,4) NOT NULL DEFAULT 1.0000,
  `tax_base` decimal(20,2) NOT NULL DEFAULT 0.00,
  `vat` decimal(20,2) NOT NULL DEFAULT 0.00,
  `total_invoice` decimal(20,2) NOT NULL DEFAULT 0.00,
  `pph` decimal(20,2) NOT NULL DEFAULT 0.00,
  `payment_amount` decimal(20,2) NOT NULL DEFAULT 0.00,
  `outstanding` decimal(20,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `doc_number` (`doc_number`),
  KEY `plan_date` (`plan_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ============================================================================
-- BAGIAN 5 - DAFTAR MENU (menurole)
-- Hanya MENDAFTARKAN butir menunya. Selama hak aksesnya belum diberikan
-- (Bagian 6), menu-menu ini TIDAK muncul untuk siapa pun - sudah dibuktikan
-- 6 Nov 2026 dgn membandingkan seluruh tautan menu tiap user sebelum/sesudah:
-- 0 butir hilang, 0 butir bertambah.
-- ============================================================================
INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 145, 'Update Payment Plan', 'Update Payment Plan', 'AP', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE id = 145);

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 146, 'AP Projection Report', 'AP Projection Report', 'AP', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE id = 146);

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 147, 'AP History Projection', 'AP History Projection', 'AP', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE id = 147);


-- ============================================================================
-- BAGIAN 6 - HAK AKSES.  *** TIDAK IKUT DIJALANKAN ***
--
-- Sengaja dibiarkan sebagai komentar. Begitu baris-baris ini dijalankan,
-- menunya LANGSUNG terlihat oleh yang diberi akses - jadi jalankan hanya
-- sesudah berkasnya ada di server dan Anda siap.
--
-- (a) UNTUK MENCOBA SENDIRI DULU - ganti 'indro' dgn username Anda:
--
--   INSERT INTO useraccess (username, menu)
--   SELECT * FROM (SELECT 'indro' AS u, 'Update Payment Plan' AS m) t
--   WHERE NOT EXISTS (SELECT 1 FROM useraccess x WHERE x.username='indro' AND x.menu='Update Payment Plan');
--   INSERT INTO useraccess (username, menu)
--   SELECT * FROM (SELECT 'indro', 'AP Projection Report') t
--   WHERE NOT EXISTS (SELECT 1 FROM useraccess x WHERE x.username='indro' AND x.menu='AP Projection Report');
--
-- (b) KALAU SUDAH YAKIN, BARU DIBUKA UNTUK SEMUA.
--     Update Payment Plan -> ke pemegang 'Payment Voucher AP' (merekalah yang
--     mengatur jadwal bayar). Projection Report -> ke pemegang 'Report'.
--     Keduanya TIDAK memberi menu kepada orang baru: yang kebagian hanya yang
--     hari ini sudah melihat menu acuannya.
--
--   INSERT INTO useraccess (username, menu)
--   SELECT DISTINCT u.username, 'Update Payment Plan' FROM useraccess u
--   WHERE u.menu = 'Payment Voucher AP'
--     AND NOT EXISTS (SELECT 1 FROM useraccess x
--                     WHERE x.username = u.username AND x.menu = 'Update Payment Plan');
--
--   INSERT INTO useraccess (username, menu)
--   SELECT DISTINCT u.username, 'AP Projection Report' FROM useraccess u
--   WHERE u.menu = 'Report'
--     AND NOT EXISTS (SELECT 1 FROM useraccess x
--                     WHERE x.username = u.username AND x.menu = 'AP Projection Report');
--
-- Butir 147 (History Projection) SENGAJA belum diberikan ke siapa pun:
-- halamannya belum dibuat.
-- ============================================================================


-- ============================================================================
-- BAGIAN 7 - PERIKSA HASILNYA
-- ============================================================================
SELECT 'sesudah' AS saat,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_payment_plan_h')        AS ada_pp_h,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_payment_plan_det')      AS ada_pp_det,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_payment_plan_awal')     AS ada_pp_awal,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_h')  AS ada_prj_h,
  (SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_det') AS ada_prj_det;
-- Harapan: kelimanya 1.

SELECT id, menu, display_name FROM menurole WHERE id IN (145,146,147) ORDER BY id;
-- Harapan: TIGA baris - Payment Plan, AP Projection Report, AP History Projection.

SELECT sumber, COUNT(*) AS jml, MIN(plan_awal) AS paling_awal, MAX(plan_awal) AS paling_akhir
FROM ap_payment_plan_awal GROUP BY sumber;

-- PENJAGA: rencana awal WAJIB jatuh hari Kamis. Harapan: 0 baris.
SELECT COUNT(*) AS rencana_awal_bukan_kamis
FROM ap_payment_plan_awal WHERE DAYOFWEEK(plan_awal) <> 5;

-- PENJAGA: tidak boleh ada dokumen Payment Plan berawalan lama.
SELECT COUNT(*) AS sisa_awalan_AP_PP FROM ap_payment_plan_h  WHERE doc_number LIKE 'AP-PP/%';
SELECT COUNT(*) AS sisa_awalan_AP_PP_det FROM ap_payment_plan_det WHERE doc_number LIKE 'AP-PP/%';

-- PENJAGA: tidak boleh ada baris detail yatim (kepalanya hilang).
SELECT COUNT(*) AS detail_yatim FROM ap_payment_plan_det d
WHERE NOT EXISTS (SELECT 1 FROM ap_payment_plan_h h WHERE h.doc_number = d.doc_number);

-- PENJAGA: belum ada yang diberi hak akses (kalau Bagian 6 belum dijalankan).
SELECT menu, COUNT(*) AS jml_user FROM useraccess
WHERE menu IN ('Update Payment Plan','AP Projection Report','AP History Projection')
GROUP BY menu;


-- ============================================================================
-- KALAU PERLU DIBATALKAN SELURUHNYA (urutannya penting):
--   DELETE FROM useraccess WHERE menu IN ('Update Payment Plan','AP Projection Report','AP History Projection');
--   DELETE FROM menurole   WHERE id IN (145,146,147);
--   DROP TABLE IF EXISTS ap_projection_history_det;
--   DROP TABLE IF EXISTS ap_projection_history_h;
--   DROP TABLE IF EXISTS ap_payment_plan_awal;
--   DROP TABLE IF EXISTS ap_payment_plan_det;
--   DROP TABLE IF EXISTS ap_payment_plan_h;
-- Nomor dokumen yang sudah diganti ke PPU TIDAK dikembalikan oleh baris di
-- atas; cadangannya ada di bak_pp_awalan_h / bak_pp_awalan_det.
-- ============================================================================
