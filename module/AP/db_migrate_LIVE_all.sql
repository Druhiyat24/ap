-- ============================================================================
-- MIGRASI DATABASE UNTUK PRODUKSI (LIVE) — modul Invoice Received / Kontrabon New,
-- strip-guard faktur bppb_new, align tabel edit-PV, dan kolom amount_add_pv.
--
-- Cara pakai: jalankan sekali di DB live (mis. lewat phpMyAdmin > SQL, atau
--   mysql -u USER -p NAMADB < db_migrate_LIVE_all.sql
--
-- Idempoten memakai IF NOT EXISTS (aman diulang). Sintaks ini didukung MariaDB.
-- CATATAN: kalau server Anda MySQL (bukan MariaDB) dan MENOLAK
--   "ADD COLUMN IF NOT EXISTS", hapus ketiga kata "IF NOT EXISTS" pada baris ALTER
--   di Bagian 2 & 2b, lalu jalankan SEKALI saja (kolom belum ada di live).
-- CREATE TABLE IF NOT EXISTS didukung semua versi.
-- Tidak ada perubahan pada tabel keuangan lama (tbl_list_journal/kontrabon/kontrabon_h).
-- ============================================================================

-- ============================================================================
-- BAGIAN 1 — TABEL BARU (modul Invoice Received). Skema final (sudah lengkap).
-- ============================================================================

CREATE TABLE IF NOT EXISTS `ir_kontrabon_h` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_number` varchar(30) DEFAULT NULL,
  `kontrabon_date` date DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `unik_code` varchar(30) DEFAULT NULL,
  `no_reff` varchar(50) DEFAULT NULL,
  `nama_supp` varchar(150) DEFAULT NULL,
  `deskripsi` text,
  `total_amount` decimal(20,2) DEFAULT 0.00,
  `amount_add_pv` decimal(20,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'Draft',
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `cancel_user` varchar(100) DEFAULT NULL,
  `cancel_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_doc` (`doc_number`),
  KEY `idx_unik` (`unik_code`),
  KEY `idx_reff` (`no_reff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ir_kontrabon_inv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unik_code` varchar(30) DEFAULT NULL,
  `doc_number` varchar(30) DEFAULT NULL,
  `no_inv` varchar(100) DEFAULT NULL,
  `tgl_inv` date DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT 0.00,
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unik` (`unik_code`),
  KEY `idx_doc` (`doc_number`),
  KEY `idx_noinv` (`no_inv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ir_kontrabon_faktur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inv_id` int(11) DEFAULT NULL,
  `unik_code` varchar(30) DEFAULT NULL,
  `no_faktur` varchar(100) DEFAULT NULL,
  `tgl_faktur` date DEFAULT NULL,
  `nama_supplier` varchar(150) DEFAULT NULL,
  `npwp_supplier` varchar(40) DEFAULT NULL,
  `pembeli` varchar(150) DEFAULT NULL,
  `npwp_pembeli` varchar(40) DEFAULT NULL,
  `dpp` decimal(20,2) DEFAULT 0.00,
  `ppn` decimal(20,2) DEFAULT 0.00,
  `ppnbm` decimal(20,2) DEFAULT 0.00,
  `status_faktur` varchar(30) DEFAULT NULL,
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_inv` (`inv_id`),
  KEY `idx_unik` (`unik_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ir_kontrabon_bpb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faktur_id` int(11) DEFAULT NULL,
  `inv_id` int(11) DEFAULT NULL,
  `unik_code` varchar(30) DEFAULT NULL,
  `no_bpb` varchar(100) DEFAULT NULL,
  `no_po` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `tgl_bpb` date DEFAULT NULL,
  `total` decimal(20,2) DEFAULT 0.00,
  `dpp` decimal(20,2) DEFAULT 0.00,
  `ppn` decimal(20,2) DEFAULT 0.00,
  `curr` varchar(10) DEFAULT NULL,
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_faktur` (`faktur_id`),
  KEY `idx_inv` (`inv_id`),
  KEY `idx_unik` (`unik_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ir_kontrabon_ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_number` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Available',
  `keterangan` varchar(150) DEFAULT NULL,
  `create_user` varchar(100) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ref_number` (`ref_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ir_kontrabon_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_user` varchar(100) NOT NULL,
  `unik_code` varchar(30) DEFAULT NULL,
  `doc_number` varchar(30) DEFAULT NULL,
  `payload` mediumtext,
  `update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user` (`create_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ir_kontrabon_bpb_reserve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_bpb` varchar(100) NOT NULL,
  `create_user` varchar(100) DEFAULT NULL,
  `unik_code` varchar(30) DEFAULT NULL,
  `nama_supp` varchar(150) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bpb` (`no_bpb`),
  KEY `idx_user` (`create_user`),
  KEY `idx_unik` (`unik_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ============================================================================
-- BAGIAN 2 — KOLOM BARU pada tabel yang SUDAH ADA di live.
-- ============================================================================

-- 2a) bppb_new: 5 kolom strip-guard No/Tgl Invoice & Faktur retur.
ALTER TABLE `bppb_new` ADD COLUMN IF NOT EXISTS `upt_dok_inv`    VARCHAR(255) NULL AFTER `is_invoiced`;
ALTER TABLE `bppb_new` ADD COLUMN IF NOT EXISTS `upt_no_inv`     VARCHAR(255) NULL AFTER `upt_dok_inv`;
ALTER TABLE `bppb_new` ADD COLUMN IF NOT EXISTS `upt_tgl_inv`    DATE         NULL AFTER `upt_no_inv`;
ALTER TABLE `bppb_new` ADD COLUMN IF NOT EXISTS `upt_no_faktur`  VARCHAR(255) NULL AFTER `upt_tgl_inv`;
ALTER TABLE `bppb_new` ADD COLUMN IF NOT EXISTS `upt_tgl_faktur` DATE         NULL AFTER `upt_no_faktur`;

-- 2b) ap_edit_* : samakan skema dgn tabel live (agar INSERT ... SELECT * di edit PV tidak gagal).
ALTER TABLE `ap_edit_kontrabon_h` MODIFY COLUMN `ir_number` varchar(255) DEFAULT NULL;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `id_bank_account`     varchar(50)  DEFAULT NULL AFTER `ir_number`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `first_approve_user`  varchar(255) DEFAULT NULL AFTER `id_bank_account`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `first_approve_date`  timestamp    NULL DEFAULT NULL AFTER `first_approve_user`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `second_approve_user` varchar(255) DEFAULT NULL AFTER `first_approve_date`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `second_approve_date` timestamp    NULL DEFAULT NULL AFTER `second_approve_user`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `from_account`        varchar(100) DEFAULT NULL AFTER `second_approve_date`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `from_bank`           varchar(100) DEFAULT NULL AFTER `from_account`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `from_bank_curr`      varchar(10)  DEFAULT NULL AFTER `from_bank`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `status_pl`           varchar(50)  DEFAULT NULL AFTER `from_bank_curr`;
ALTER TABLE `ap_edit_kontrabon_h` ADD COLUMN IF NOT EXISTS `status_pvl`          varchar(50)  DEFAULT NULL AFTER `status_pl`;

ALTER TABLE `ap_edit_potongan` ADD COLUMN IF NOT EXISTS `potongan_ppn` decimal(16,2) DEFAULT NULL AFTER `jml_potong`;
ALTER TABLE `ap_edit_potongan` ADD COLUMN IF NOT EXISTS `potongan_pph` decimal(16,2) DEFAULT NULL AFTER `potongan_ppn`;

ALTER TABLE `ap_edit_kontrabon` ADD COLUMN IF NOT EXISTS `first_approve_by`    varchar(255) DEFAULT NULL AFTER `lp_inv`;
ALTER TABLE `ap_edit_kontrabon` ADD COLUMN IF NOT EXISTS `first_approve_date`  datetime     NULL DEFAULT NULL AFTER `first_approve_by`;
ALTER TABLE `ap_edit_kontrabon` ADD COLUMN IF NOT EXISTS `second_approve_by`   varchar(255) DEFAULT NULL AFTER `first_approve_date`;
ALTER TABLE `ap_edit_kontrabon` ADD COLUMN IF NOT EXISTS `second_approve_date` datetime     NULL DEFAULT NULL AFTER `second_approve_by`;

-- ============================================================================
-- BAGIAN 2c — PENGAMAN (hanya berlaku kalau tabel ir_kontrabon_h SUDAH ADA di live
-- dari deploy sebelumnya yang belum lengkap). Kalau tabel baru dibuat di Bagian 1,
-- kolom-kolom ini sudah ikut, dan baris ini akan di-skip (IF NOT EXISTS).
-- ============================================================================
ALTER TABLE `ir_kontrabon_h` ADD COLUMN IF NOT EXISTS `document_date` date NULL AFTER `kontrabon_date`;
ALTER TABLE `ir_kontrabon_h` ADD COLUMN IF NOT EXISTS `amount_add_pv` decimal(20,2) NOT NULL DEFAULT 0.00 AFTER `total_amount`;
ALTER TABLE `ir_kontrabon_h` ADD COLUMN IF NOT EXISTS `cancel_user` varchar(100) NULL;
ALTER TABLE `ir_kontrabon_h` ADD COLUMN IF NOT EXISTS `cancel_date` datetime NULL;

-- ============================================================================
-- SELESAI. (Perubahan DATA menu/hak-akses — "Invoice Received" & rename
-- "Document Handover" — TIDAK termasuk di sini karena bergantung id menurole di
-- live. Jalankan file idempoten: db_migrate_invoice_received_menu.php via browser,
-- atau minta versi SQL-nya secara terpisah.)
-- ============================================================================
