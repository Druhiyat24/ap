-- ============================================================================
-- AP PAYMENT PROJECTION REPORT - tabel riwayat (Save History).
-- Dijalankan di PRODUKSI (10.10.5.12 / signalbit_erp).
--
-- KENAPA TABEL SENDIRI, BUKAN MENUMPANG tbl_history_projection_h/_det:
-- dua tabel itu MILIK AR dan sudah terpakai (kolomnya no_invoice, customer,
-- duedate_update, collection_amount - semuanya istilah piutang). AR dan AP
-- berbagi satu basis data, jadi menulis baris AP ke sana akan muncul di menu
-- History Projection milik AR. Karena itu AP memakai pasangannya sendiri.
--
-- KOLOM TANGGAL PROYEKSI TIDAK DISIMPAN SATU-SATU. Jumlah kolomnya
-- berubah-ubah menurut periode, jadi yang disimpan `plan_date` (tanggal
-- proyeksi baris itu) dan `outstanding` (nilai yang jatuh di tanggal itu) -
-- dari dua kolom itu kisi tanggalnya bisa dibangun ulang persis, tanpa tabel
-- yang lebarnya ikut berubah.
--
-- Nilai-nilainya POTRET, sengaja disalin bukan dibaca ulang saat dokumen
-- dibuka: kurs, rencana bayar, dan master supplier semuanya bisa berubah
-- besok, sedangkan yang disimpan di sini adalah angka yang dilihat pemakai
-- saat menekan Save.
-- ============================================================================

CREATE TABLE IF NOT EXISTS ap_projection_history_h (
  id              INT(11)        NOT NULL AUTO_INCREMENT,
  doc_number      VARCHAR(25)    NOT NULL,
  periode_dari    DATE           NOT NULL,
  periode_sampai  DATE           NOT NULL,
  nama_supp       VARCHAR(255)   NOT NULL DEFAULT 'ALL',
  type            VARCHAR(10)    NOT NULL DEFAULT 'daily',
  -- patokan saldo: sehari sebelum periode (lihat butir 2 di kepala
  -- ajx_ap_projection_report.php)
  per_tanggal     DATE           NULL DEFAULT NULL,
  -- kurs yang benar-benar dipakai saat potret dibuat, dalam bentuk JSON
  -- ringkas {"USD":{"rate":"17,803.00","tanggal":"24 Sep 2026"}} - supaya
  -- angka rupiahnya bisa ditelusuri bertahun-tahun kemudian
  kurs            TEXT           NULL DEFAULT NULL,
  jml_baris       INT(11)        NOT NULL DEFAULT 0,
  total_idr       DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  status          VARCHAR(20)    NOT NULL DEFAULT 'ACTIVE',
  created_by      VARCHAR(100)   NOT NULL,
  created_at      TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cancel_by       VARCHAR(100)   NULL DEFAULT NULL,
  cancel_date     DATETIME       NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY doc_number (doc_number),
  KEY periode (periode_dari, periode_sampai)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS ap_projection_history_det (
  id              BIGINT(20)     NOT NULL AUTO_INCREMENT,
  doc_number      VARCHAR(25)    NOT NULL,
  no_urut         INT(11)        NOT NULL DEFAULT 0,
  supplier        VARCHAR(255)   NULL DEFAULT NULL,
  no_kbon         VARCHAR(255)   NULL DEFAULT NULL,
  tgl_kbon        DATE           NULL DEFAULT NULL,
  term            VARCHAR(20)    NULL DEFAULT NULL,
  duedate         DATE           NULL DEFAULT NULL,
  -- Payment Plan Date: tanggal yang BENAR-BENAR dipakai menaruh uangnya
  plan_date       DATE           NULL DEFAULT NULL,
  -- lapis mana yang dipakai: Updated plan / Initial plan / Due date
  basis           VARCHAR(20)    NULL DEFAULT NULL,
  relasi          VARCHAR(50)    NULL DEFAULT NULL,
  item_type       VARCHAR(100)   NULL DEFAULT NULL,
  flexibility     VARCHAR(50)    NULL DEFAULT NULL,
  curr            VARCHAR(10)    NULL DEFAULT NULL,
  invoice_amount  DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  rate            DECIMAL(20,4)  NOT NULL DEFAULT 1.0000,
  tax_base        DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  vat             DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  total_invoice   DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  pph             DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  payment_amount  DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  -- nilai yang jatuh di kolom tanggal plan_date (sisa utang, dalam rupiah)
  outstanding     DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id),
  KEY doc_number (doc_number),
  KEY plan_date (plan_date)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Periksa hasilnya
SELECT TABLE_NAME, TABLE_ROWS
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('ap_projection_history_h', 'ap_projection_history_det');

-- ----------------------------------------------------------------------------
-- MEMBATALKAN (kalau perlu):
--   DROP TABLE IF EXISTS ap_projection_history_det;
--   DROP TABLE IF EXISTS ap_projection_history_h;
-- ----------------------------------------------------------------------------
