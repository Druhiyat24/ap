-- ============================================================================
-- AP HISTORY PROJECTION - DUA TABEL ARSIP yang membuat tombol Cancel jalan.
-- JALANKAN DI PRODUKSI (10.10.5.12 / signalbit_erp).
--
-- KENAPA BERKAS INI ADA, padahal isinya sudah termuat di
-- db_migrate_LIVE_20261106_GABUNGAN.sql:
--   Yang pernah dijalankan di produksi adalah berkas yang LEBIH DULU,
--   db_migrate_LIVE_20261106_ap_projection_history.sql - dan berkas itu hanya
--   membuat dua tabel HIDUP, tanpa satu pun tabel *_cancel. Akibatnya menu
--   History Projection bisa menyimpan dokumen (AP-PPR/1026/00001 dan
--   AP-PPR/1126/00001 memang sudah ada), tapi tombol Cancel-nya ditolak:
--
--       "The archive table ap_projection_history_h_cancel does not exist yet."
--
--   Penolakan itu DISENGAJA, bukan kerusakan. batal_history_projection.php
--   memindahkan dokumen ke arsip DULU, baru menghapusnya dari tabel hidup;
--   tanpa arsipnya, Cancel akan menghapus tanpa jejak dan dokumen yang
--   dibatalkan tidak bisa ditelusuri lagi.
--
--   Menjalankan seluruh berkas GABUNGAN juga boleh dan aman (semuanya
--   CREATE IF NOT EXISTS / INSERT IGNORE), tapi berkas itu 20 KB dan
--   menyentuh menu, hak akses, dan penggantian awalan nomor dokumen. Yang
--   dibutuhkan sekarang hanya DUA tabel. Berkas ini persis itu.
--
-- AMAN DIULANG: dua-duanya CREATE TABLE IF NOT EXISTS, tidak ada DROP, tidak
-- ada ALTER, dan tidak ada satu pun baris data yang disentuh.
--
-- STRUKTURNYA DISALIN PERSIS dari tabel hidupnya, dan itu WAJIB - bukan
-- kerapian. batal_history_projection.php mengarsipkan dengan
--     INSERT IGNORE INTO ..._cancel SELECT * FROM ...
-- yang mencocokkan kolom MENURUT URUTAN, bukan menurut nama. Satu kolom yang
-- bergeser membuat tanggal masuk ke kolom angka tanpa satu pun pesan salah.
-- BAGIAN 3 di bawah memeriksa hal itu sesudah tabelnya dibuat.
-- ============================================================================


-- ============================================================================
-- BAGIAN 0 - POTRET SEBELUM. Jalankan dan simpan hasilnya.
-- Harapan: dua baris (tabel hidup ada), tabel _cancel belum ada.
-- ============================================================================
SELECT TABLE_NAME, TABLE_ROWS
  FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME LIKE 'ap_projection_history%'
 ORDER BY TABLE_NAME;

-- Dokumen yang sekarang hidup - inilah yang nanti bisa di-Cancel.
SELECT doc_number, periode_dari, periode_sampai, type, jml_baris,
       total_idr, status, created_by, created_at
  FROM ap_projection_history_h
 ORDER BY id;


-- ============================================================================
-- BAGIAN 1 - ARSIP KEPALA DOKUMEN
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ap_projection_history_h_cancel` (
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


-- ============================================================================
-- BAGIAN 2 - ARSIP RINCIAN DOKUMEN
-- ============================================================================
CREATE TABLE IF NOT EXISTS `ap_projection_history_det_cancel` (
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
-- BAGIAN 3 - PEMERIKSAAN. INI YANG PALING PENTING, jangan dilewati.
-- Keempat baris di bawah HARUS berbunyi OK. Kalau ada satu saja yang
-- 'TIDAK COCOK', JANGAN memakai tombol Cancel dan beri tahu dulu - arsipnya
-- akan terisi melenceng tanpa satu pun pesan salah.
-- ============================================================================

-- 3a. Keempat tabelnya ada.
SELECT CASE WHEN COUNT(*) = 4 THEN 'OK - 4 tabel ada'
            ELSE CONCAT('TIDAK COCOK - baru ', COUNT(*), ' dari 4 tabel') END AS cek_3a
  FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME IN ('ap_projection_history_h', 'ap_projection_history_det',
                      'ap_projection_history_h_cancel', 'ap_projection_history_det_cancel');

-- 3b. URUTAN KOLOM kepala dokumen sama persis antara tabel hidup & arsipnya.
--     GROUP_CONCAT dgn ORDER BY ORDINAL_POSITION = tanda tangan urutannya.
SELECT CASE WHEN hidup.tt = arsip.tt THEN 'OK - urutan kolom _h sama'
            ELSE CONCAT('TIDAK COCOK - hidup: ', hidup.tt, ' || arsip: ', arsip.tt) END AS cek_3b
  FROM (SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) tt
          FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_h') hidup,
       (SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) tt
          FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_h_cancel') arsip;

-- 3c. URUTAN KOLOM rincian dokumen sama persis.
SELECT CASE WHEN hidup.tt = arsip.tt THEN 'OK - urutan kolom _det sama'
            ELSE CONCAT('TIDAK COCOK - hidup: ', hidup.tt, ' || arsip: ', arsip.tt) END AS cek_3c
  FROM (SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) tt
          FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_det') hidup,
       (SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) tt
          FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_projection_history_det_cancel') arsip;

-- 3d. Tidak ada data yang hilang: dokumen hidup masih utuh, arsip masih kosong.
SELECT (SELECT COUNT(*) FROM ap_projection_history_h)        AS dok_hidup,
       (SELECT COUNT(*) FROM ap_projection_history_det)      AS baris_hidup,
       (SELECT COUNT(*) FROM ap_projection_history_h_cancel) AS dok_arsip,
       (SELECT COUNT(*) FROM ap_projection_history_det_cancel) AS baris_arsip,
       'arsip memang HARUS kosong sekarang' AS catatan;


-- ============================================================================
-- SESUDAH INI
-- ----------------------------------------------------------------------------
-- Tombol Cancel di menu AP History Projection langsung jalan - tidak perlu
-- menyentuh berkas PHP apa pun, karena penjaganya memang hanya memeriksa
-- ADA/TIDAKNYA tabel ini setiap kali ditekan.
--
-- Dokumen yang dibatalkan hilang dari daftar, tapi tetap tersimpan lengkap di
-- ap_projection_history_h_cancel / _det_cancel. Membacanya:
--     SELECT * FROM ap_projection_history_h_cancel ORDER BY cancel_date DESC;
-- ============================================================================
