-- ============================================================================
-- tbl_ppn_masukan_upload — simpan DATA FULL hasil upload rekap Faktur Pajak
-- Masukan (e-Faktur/Coretax) untuk tab "PPN Masukan" di Memorial Journal.
--
-- Alur: upload -> baris masuk sini status='Temp' (per create_by) -> saat Save
-- ditandai status='Post' + diisi no_mj/mj_date (link ke GM & tbl_list_journal).
-- Idempoten: aman dijalankan ulang.
-- ============================================================================
CREATE TABLE IF NOT EXISTS tbl_ppn_masukan_upload (
  id              INT(11)       NOT NULL AUTO_INCREMENT,
  bulan           VARCHAR(20)   DEFAULT NULL COMMENT 'kolom Bulan di file, mis. 2601',
  jenis           VARCHAR(20)   DEFAULT NULL COMMENT 'kolom Jenis, mis. B3',
  nama_penjual    VARCHAR(255)  DEFAULT NULL COMMENT 'Nama Penjual BKP/Pemberi JKP',
  npwp            VARCHAR(50)   DEFAULT NULL COMMENT 'Nomor Identitas WP',
  no_faktur       VARCHAR(100)  DEFAULT NULL COMMENT 'No Faktur Pajak/Nota Retur',
  tgl_faktur      DATE          DEFAULT NULL,
  dpp             DECIMAL(20,2) NOT NULL DEFAULT 0.00 COMMENT 'Harga Jual/Penggantian/Nilai Impor/DPP',
  dpp_nilai_lain  DECIMAL(20,2) NOT NULL DEFAULT 0.00,
  ppn             DECIMAL(20,2) NOT NULL DEFAULT 0.00 COMMENT 'dipakai sbg nilai jurnal (bisa negatif utk retur)',
  ppnbm           DECIMAL(20,2) NOT NULL DEFAULT 0.00,
  faktur_diganti  VARCHAR(100)  DEFAULT NULL COMMENT 'Kode & No Seri Faktur yang Diganti/Diretur',
  no_mj           VARCHAR(200)  DEFAULT NULL COMMENT 'diisi saat Save (nomor GM)',
  mj_date         DATE          DEFAULT NULL,
  profit_center   VARCHAR(50)   DEFAULT NULL,
  status          VARCHAR(50)   NOT NULL DEFAULT 'Temp' COMMENT 'Temp | Post',
  create_by       VARCHAR(50)   DEFAULT NULL,
  create_date     DATETIME      DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_user_status (create_by, status),
  KEY idx_no_mj (no_mj),
  KEY idx_faktur (no_faktur)
) ENGINE=InnoDB;
