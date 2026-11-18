/* ============================================================================
   MIGRASI PRODUKSI - MENU Bank > Projection > Update Payment Date  (16 Nov 2026)
   Basis data: signalbit_erp @ 10.10.5.12

   DIJALANKAN SEKALI, SEBELUM kode terbaru ditarik ke server. Tanpa ini
   menunya tidak muncul dan halamannya galat karena tabelnya belum ada.

   APA INI
   -------
   PV Biaya (type_pv 'Biaya' di Bank Out) belum punya TANGGAL RENCANA BAYAR
   yang berdiri sendiri. Kolom tbl_pv_h.pay_date memang ada, tapi isinya
   dipasang saat PV dibuat dan dipakai dokumen itu sendiri - bukan rencana
   yang boleh digeser-geser. Menu ini menyimpan rencananya TERPISAH, persis
   pola Update Payment Plan milik AP (ap_payment_plan_h/det), supaya Cash
   Flow Projection nanti punya tanggal yang bisa diandalkan.

   TIDAK ADA satu pun kolom tbl_pv_h yang disentuh menu ini.

   Isinya tiga bagian:
     1. ap_pvbiaya_plan_h    - kepala dokumen rencana
     2. ap_pvbiaya_plan_det  - barisnya, satu per PV
     3. menurole id 150 + useraccess - hak akses menunya

   AMAN DIJALANKAN ULANG: tabelnya CREATE TABLE IF NOT EXISTS, insert-nya
   WHERE NOT EXISTS.
   ============================================================================ */


/* ============================================================================
   BAGIAN 0 - LIHAT DULU (tidak mengubah apa pun)
   ============================================================================ */
SELECT 'keadaan sebelum' AS bagian, @@hostname AS server, DATABASE() AS basis_data;

SELECT 'tabel ap_pvbiaya_plan_h'   AS yang_dicek, COUNT(*) AS sudah_ada
  FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_pvbiaya_plan_h'
UNION ALL
SELECT 'tabel ap_pvbiaya_plan_det', COUNT(*) FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_pvbiaya_plan_det'
UNION ALL
SELECT 'menurole Bank Projection Payment Date', COUNT(*) FROM menurole
 WHERE menu = 'Bank Projection Payment Date'
UNION ALL
SELECT 'menurole id 150 dipakai menu lain', COUNT(*) FROM menurole
 WHERE id = 150 AND menu <> 'Bank Projection Payment Date';


/* ---------------------------------------------------------------------------
   1. KEPALA DOKUMEN

   Satu kali Save = satu dokumen, berapa pun barisnya. Nomornya PPU-E/<mmyy>/
   <00001>, dibuat di server dgn GET_LOCK supaya dua orang yang menyimpan
   bersamaan tidak memakai nomor yang sama.

   plan_date di sini tanggal DOKUMEN sekaligus cadangan untuk baris yang
   tidak membawa tanggalnya sendiri. Yang dibaca laporan adalah tanggal PER
   BARIS di tabel det - sama seperti ap_payment_plan.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_pvbiaya_plan_h` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `doc_number`   VARCHAR(100) NOT NULL,
  `plan_date`    DATE         DEFAULT NULL COMMENT 'tanggal dokumen, cadangan utk baris tanpa tanggal',
  `keterangan`   VARCHAR(255) DEFAULT NULL,
  `status`       VARCHAR(20)  NOT NULL DEFAULT 'POST' COMMENT 'POST / CANCEL',
  `created_by`   VARCHAR(100) DEFAULT NULL,
  `created_date` DATETIME     DEFAULT NULL,
  `cancel_by`    VARCHAR(100) DEFAULT NULL,
  `cancel_date`  DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_doc` (`doc_number`),
  KEY `idx_status` (`status`),
  KEY `idx_plan_date` (`plan_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   2. BARIS DOKUMEN

   KUNCINYA (no_pv + profit_center), BUKAN no_pv saja. Bank Out memang
   mengelompokkan PV Biaya per profit center - satu PV bisa punya baris NAG
   dan NAK sekaligus dgn sisa masing-masing - jadi rencananya pun harus per
   pasangan itu. Kalau cuma no_pv, dua PC di satu PV akan saling memadamkan.

   status 'Y' = rencana yang BERLAKU, 'N' = sudah digantikan dokumen yang
   lebih baru atau dokumennya dibatalkan. Tanggal lamanya TIDAK dihapus -
   barisnya tetap ada sbg riwayat, yang berubah cuma penandanya.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_pvbiaya_plan_det` (
  `id`            BIGINT(20)    NOT NULL AUTO_INCREMENT,
  `doc_number`    VARCHAR(100)  NOT NULL,
  `no_pv`         VARCHAR(200)  NOT NULL,
  `profit_center` VARCHAR(50)   NOT NULL DEFAULT '',
  `plan_date`     DATE          DEFAULT NULL COMMENT 'tanggal rencana bayar - INI yang dibaca laporan',
  `curr`          VARCHAR(10)   DEFAULT NULL,
  `amount`        DECIMAL(20,2) NOT NULL DEFAULT 0.00 COMMENT 'sisa outstanding saat rencana dibuat',
  `keterangan`    VARCHAR(255)  DEFAULT NULL,
  `status`        CHAR(1)       NOT NULL DEFAULT 'Y' COMMENT 'Y = berlaku, N = digantikan/dibatalkan',
  PRIMARY KEY (`id`),
  KEY `idx_doc` (`doc_number`),
  KEY `idx_pv` (`no_pv`, `profit_center`),
  KEY `idx_plan_date` (`plan_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   3. HAK AKSES MENU

   id 150 dipakai KARENA DIPANGGIL LANGSUNG di module/menu.php (pola yang
   sama dgn menu lain di berkas itu). Diperiksa 16 Nov 2026: id terbesar di
   menurole produksi = 149 dan 150 KOSONG. Kalau ternyata sudah terpakai menu
   lain, JANGAN dipaksa - ganti id di sini DAN di module/menu.php sekaligus.

   Periksa dulu:  SELECT * FROM menurole WHERE id = 150;
   --------------------------------------------------------------------------- */
INSERT INTO `menurole` (`id`, `menu`, `display_name`, `menu_group`, `status`, `profit_center`)
SELECT 150, 'Bank Projection Payment Date', 'Payment Plan - Update Payment Date', 'Bank', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM `menurole` WHERE `menu` = 'Bank Projection Payment Date');

/* Satu baris contoh untuk indro. Pemakai lain diberikan lewat layar
   User Access seperti biasa - TIDAK perlu SQL. */
INSERT INTO `useraccess` (`username`, `fullname`, `menu`, `create_date`, `create_user`)
SELECT 'indro', 'Indro', 'Bank Projection Payment Date', NOW(), 'migrasi'
WHERE NOT EXISTS (
  SELECT 1 FROM `useraccess` WHERE `username` = 'indro' AND `menu` = 'Bank Projection Payment Date'
);


/* ============================================================================
   PEMERIKSAAN AKHIR - semua baris harus sesuai kolom harapan
   ============================================================================ */
SELECT 'tabel ap_pvbiaya_plan_h' AS yang_dicek, COUNT(*) AS hasil, '1 = ada' AS harapan
  FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_pvbiaya_plan_h'
UNION ALL
SELECT 'tabel ap_pvbiaya_plan_det', COUNT(*), '1 = ada' FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_pvbiaya_plan_det'
UNION ALL
SELECT 'menurole Bank Projection Payment Date', COUNT(*), '1' FROM menurole
 WHERE menu = 'Bank Projection Payment Date' AND id = 150 AND menu_group = 'Bank'
UNION ALL
SELECT 'useraccess Bank Projection Payment Date', COUNT(*), '1 atau lebih' FROM useraccess
 WHERE menu = 'Bank Projection Payment Date';
