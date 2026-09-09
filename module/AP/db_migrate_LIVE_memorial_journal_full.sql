-- ============================================================================
-- DEPLOY LIVE — Memorial Journal (Manual/HRIS/Upload/PPN Masukan) +
--               Sub Ledger PPN Masukan (ppn_masukan_report.php)
-- ============================================================================
-- Konsolidasi SEMUA perubahan skema database yang dibutuhkan fitur ini.
-- Idempoten menyeluruh: aman dijalankan berkali-kali, dan aman dijalankan
-- meski sebagian sudah pernah diterapkan sebagian (mis. kolom supplier di
-- tbl_list_journal kemungkinan sudah live dari migrasi terpisah sebelumnya).
--
-- CATATAN JUJUR: kolom faktur_pajak/tgl_faktur_pajak pada tbl_list_journal &
-- tbl_list_journal_cancel (Bagian 1a) SUDAH ADA di database lokal, tapi saya
-- TIDAK MENEMUKAN satu pun berkas migrasi tertulis yang menambahkannya --
-- beda dengan kolom "supplier" yang tercatat rapi di
-- db_migrate_LIVE_supplier_journal.sql. Kemungkinan ditambahkan langsung/manual
-- di awal sesi kerja sebelum konvensi berkas migrasi ini konsisten dipakai.
-- Disertakan di sini dengan IF NOT EXISTS supaya aman terlepas dari itu.
--
-- URUTAN MENJALANKAN: dari atas ke bawah, satu bagian tidak bergantung pada
-- bagian sesudahnya (aman dipenggal / dijalankan bertahap kalau perlu).
-- ============================================================================


-- ============================================================================
-- BAGIAN 1a. tbl_list_journal & tbl_list_journal_cancel
--            Kolom No Faktur / Tgl Faktur / Supplier pada JURNAL UMUM.
--            Dipakai oleh: SEMUA tab Memorial Journal (Manual/HRIS/Upload/PPN),
--            report-faktur-pajak.php, dan report sub ledger PPN Masukan.
-- ============================================================================
ALTER TABLE tbl_list_journal
  ADD COLUMN IF NOT EXISTS faktur_pajak     VARCHAR(50)  NULL AFTER reff_date,
  ADD COLUMN IF NOT EXISTS tgl_faktur_pajak DATE         NULL AFTER faktur_pajak,
  ADD COLUMN IF NOT EXISTS supplier         VARCHAR(255) NULL AFTER profit_center;

ALTER TABLE tbl_list_journal_cancel
  ADD COLUMN IF NOT EXISTS faktur_pajak     VARCHAR(50)  NULL AFTER reff_date,
  ADD COLUMN IF NOT EXISTS tgl_faktur_pajak DATE         NULL AFTER faktur_pajak,
  ADD COLUMN IF NOT EXISTS supplier         VARCHAR(255) NULL AFTER profit_center;


-- ============================================================================
-- BAGIAN 1b. tbl_memorial_journal_temp — tabel STAGING tab Upload Journal.
--            Bukan penyimpanan permanen: baris menunggu di sini antara tahap
--            "upload" dan tahap "save", lalu dihapus otomatis setelah save.
--            Tanpa kolom ini, No Faktur/Tgl Faktur/Supplier dari template
--            upload tidak punya jalan sampai ke tbl_list_journal.
--
-- KEPUTUSAN SADAR: tbl_memorial_journal (tabel utama, BUKAN _temp) SENGAJA
-- TIDAK diberi kolom ini. Konsekuensinya: form EDIT Memorial Journal membaca
-- detail dari tbl_memorial_journal, lalu menulis ulang tbl_list_journal dari
-- isi form itu — sehingga MJ yang PERNAH DIEDIT akan kehilangan ketiga nilai
-- ini di tbl_list_journal, tanpa pesan error. Data dari upload yang TIDAK
-- pernah diedit tetap utuh. (Kalau kelak berubah pikiran: tambahkan kolom yang
-- sama ke tbl_memorial_journal dan ubah edit-memorial-journal.php /
-- insert_memorial_journal_edit.php agar ikut membaca & menulis ulang.)
-- ============================================================================
ALTER TABLE tbl_memorial_journal_temp
  ADD COLUMN IF NOT EXISTS faktur_pajak     VARCHAR(100) NULL AFTER reff_date,
  ADD COLUMN IF NOT EXISTS tgl_faktur_pajak DATE         NULL AFTER faktur_pajak,
  ADD COLUMN IF NOT EXISTS supplier         VARCHAR(255) NULL AFTER tgl_faktur_pajak;


-- ============================================================================
-- BAGIAN 2. Kategori Type baru: VAT
--           Dipakai sebagai Type default tab "PPN Masukan" di
--           create_memorial_journal.php — jurnal PPN Masukan otomatis masuk
--           kategori ini, dan report sub ledger menyaring bucket DEDUCTION
--           dengan type_journal = 'VAT'.
--
-- Aman diulang: HAVING dievaluasi SETELAH agregasi, jadi baris hasil hanya
-- muncul kalau 'VAT' memang belum ada. (WHERE NOT EXISTS di sini SALAH:
-- filternya jalan sebelum agregasi, MAX jadi NULL, dan barisnya tetap masuk.)
-- id_cmj mengikuti pola yang ada: nomor CMJ terbesar + 1.
-- ============================================================================
INSERT INTO master_category_mj (id_cmj, nama_cmj, status_hris)
SELECT CONCAT('CMJ', LPAD(MAX(CAST(SUBSTRING(id_cmj, 4) AS UNSIGNED)) + 1, 3, '0')), 'VAT', NULL
FROM master_category_mj
HAVING SUM(nama_cmj = 'VAT') = 0;


-- ============================================================================
-- BAGIAN 3. Tabel baru: tbl_ppn_masukan_upload
--           Simpan DATA FULL hasil upload rekap Faktur Pajak Masukan
--           (e-Faktur/Coretax) untuk tab "PPN Masukan" di Memorial Journal.
--
-- Alur: upload -> baris masuk sini status='Temp' (per create_by) -> saat Save
-- ditandai status='Post' + diisi no_mj/mj_date (link ke GM & tbl_list_journal).
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


-- ============================================================================
-- BAGIAN 4. Tabel baru: tbl_ppn_saldo_awal
--           SALDO AWAL PPN MASUKAN (sub ledger 1.52.04).
--
-- Report ppn_masukan_report.php hanya membaca jurnal mulai 2026-01-01. Saldo
-- yang lebih tua dari itu TIDAK boleh diambil dari jurnal, tapi harus diupload
-- ke tabel ini (menu "Set Opening Balance" di halaman report).
--
-- status : Temp = hasil upload yang masih di-preview (per user)
--          Post = sudah disimpan & dipakai report sebagai Beginning Balance
-- as_of  : tanggal posisi saldo awal (dipakai 2026-01-01)
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


-- ============================================================================
-- BAGIAN 5 (OPSIONAL — jalankan belakangan, hanya relevan setelah fitur
--          benar-benar dipakai di produksi dan ada GM PPN Masukan yang lebih
--          tua dari kategori VAT dibuat).
--
-- BACKFILL: set Type = VAT untuk memorial journal LAMA yang berasal dari tab
-- PPN Masukan tapi tersimpan sebelum kategori VAT ada (Type-nya jadi 'OTHERS').
-- Report menyaring bucket DEDUCTION dengan type_journal = 'VAT' — tanpa
-- backfill ini, GM lama tidak lagi terhitung sebagai deduction dan saldonya
-- hilang dari report.
--
-- CAKUPAN: HANYA nomor GM yang memang tercatat di tbl_ppn_masukan_upload —
-- jadi tidak mungkin menyenggol memorial journal lain. Di deploy PERTAMA kali,
-- tbl_ppn_masukan_upload di produksi masih kosong sehingga bagian ini otomatis
-- tidak mengubah apa pun (aman dijalankan sekarang juga, hanya belum berguna).
-- ============================================================================
SET @vat := (SELECT id_cmj FROM master_category_mj WHERE UPPER(TRIM(nama_cmj)) = 'VAT' ORDER BY id_cmj LIMIT 1);

UPDATE tbl_list_journal l
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(l.no_journal USING utf8mb4)
SET l.type_journal = 'VAT'
WHERE l.type_journal <> 'VAT';

UPDATE tbl_memorial_journal m
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(m.no_mj USING utf8mb4)
SET m.id_cmj = @vat
WHERE @vat IS NOT NULL AND m.id_cmj <> @vat;

UPDATE sb_list_journal s
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(s.no_journal USING utf8mb4)
SET s.type_journal = 'VAT'
WHERE s.type_journal <> 'VAT';

UPDATE sb_memorial_journal s
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(s.no_mj USING utf8mb4)
SET s.id_cmj = @vat
WHERE @vat IS NOT NULL AND s.id_cmj <> @vat;


-- ============================================================================
-- VERIFIKASI — jalankan setelah Bagian 1-4 selesai. Harapan hasilnya:
--   * Baris pertama: 9 baris (3 kolom x 3 tabel jurnal umum), semuanya ADA.
--   * Baris kedua  : 1 baris, nama_cmj = VAT.
--   * Baris ketiga : 2 baris, kedua tabel PPN ADA.
-- ============================================================================
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('tbl_list_journal','tbl_list_journal_cancel','tbl_memorial_journal_temp')
  AND COLUMN_NAME IN ('faktur_pajak','tgl_faktur_pajak','supplier')
ORDER BY TABLE_NAME, COLUMN_NAME;

SELECT id_cmj, nama_cmj FROM master_category_mj WHERE nama_cmj = 'VAT';

SELECT TABLE_NAME FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('tbl_ppn_masukan_upload','tbl_ppn_saldo_awal');
