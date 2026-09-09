-- ============================================================================
-- Tabel SALDO AWAL PPN MASUKAN (sub ledger 1.52.04).
--
-- Report ppn_masukan_report.php hanya membaca jurnal mulai 2026-01-01. Saldo
-- yang lebih tua dari itu TIDAK boleh diambil dari jurnal, tapi harus diupload
-- ke tabel ini (menu "Set Opening Balance" di halaman report).
--
-- status : Temp = hasil upload yang masih di-preview (per user)
--          Post = sudah disimpan & dipakai report sebagai Beginning Balance
-- as_of  : tanggal posisi saldo awal (dipakai 2026-01-01)
--
-- JALANKAN JUGA DI PRODUKSI sebelum menu ini dipakai di sana.
-- ============================================================================
CREATE TABLE IF NOT EXISTS `tbl_ppn_saldo_awal` (
  `id`               int(11)        NOT NULL AUTO_INCREMENT,
  `si_no`            varchar(200)   DEFAULT NULL,
  `si_date`          date           DEFAULT NULL,
  `supplier`         varchar(255)   DEFAULT NULL,
  `faktur_pajak`     varchar(50)    DEFAULT NULL,
  `tgl_faktur_pajak` date           DEFAULT NULL,
  `profit_center`    varchar(255)   DEFAULT NULL,
  `curr`             varchar(20)    DEFAULT NULL,
  `rate`             double(16,4)   DEFAULT 1.0000,
  `amount_ocy`       double(16,4)   DEFAULT 0.0000,
  `amount_idr`       double(16,2)   DEFAULT 0.00,
  `as_of`            date           DEFAULT NULL,
  `status`           varchar(20)    DEFAULT 'Temp',
  `create_by`        varchar(255)   DEFAULT NULL,
  `create_date`      timestamp      NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faktur_pajak` (`faktur_pajak`),
  KEY `status` (`status`),
  KEY `as_of` (`as_of`),
  KEY `create_by` (`create_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
