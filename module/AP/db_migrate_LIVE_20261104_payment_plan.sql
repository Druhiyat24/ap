-- ============================================================================
-- MIGRASI PRODUKSI - 4 Nov 2026
-- Menu baru: AP > Payment Plan > Update Payment Plan
--
-- Menu ini tempat tim AP menetapkan TANGGAL RENCANA BAYAR payment voucher
-- (kontrabon). Bentuknya DOKUMEN, sama persis dgn Update Due Date di AR:
-- satu dokumen bernomor berisi banyak voucher, ada daftar dokumennya, dan
-- dokumen yang salah DIBATALKAN - bukan ditimpa. Dgn begitu riwayat
-- penjadwalan terbaca dari dokumennya sendiri, tidak perlu tabel log.
--
-- Tanggal rencana itulah yang menggerakkan dua laporan berikutnya:
--
--   - AP Payment Projection Report  : barisnya diambil dari voucher yang
--     tanggal rencana bayarnya jatuh di dalam periode laporan - BUKAN dari
--     tanggal voucher atau tanggal jatuh tempo. Sudah dicocokkan ke lembar
--     kerja tim AP (Okt 2026): 229 baris, tanggal rencana 3-26 Okt,
--     sedangkan tanggal vouchernya tersebar dari Agu 2025 sampai Sep 2026.
--   - AP Payment Realization & Variance Report : membandingkan tanggal &
--     nilai rencana itu dengan pembayaran yang betul-betul terjadi.
--
-- Tanpa kedua tabel ini laporan proyeksi tidak punya sisi "rencana".
--
-- WAJIB dijalankan SEBELUM pull kode baru. Kalau tidak: menunya tidak
-- muncul untuk siapa pun, dan layarnya gagal membaca tabel.
--
-- AMAN DIULANG. Semua perintah memakai IF NOT EXISTS / NOT EXISTS.
--
-- ----------------------------------------------------------------------------
-- CATATAN PENTING TENTANG NOMOR id
-- id dipatok 145 karena id terbesar yang DIKETAHUI saat ini 144
-- ('Petty Cash Flow Realisation', dari db_migrate_LIVE_20261015_petty_cfr.sql
-- yang BELUM tentu sudah dijalankan di produksi). Isi produksi TIDAK bisa
-- diperiksa dari sesi pembuat berkas ini, jadi JALANKAN DULU bagian 0 di
-- bawah. Kalau 145 ternyata sudah terpakai, ganti angka 145 di SELURUH
-- berkas ini DAN di module/menu.php (cari "145").
-- ----------------------------------------------------------------------------

-- ----------------------------------------------------------------------------
-- 0. PERIKSA DULU (tidak mengubah apa pun)
-- ----------------------------------------------------------------------------
SELECT '0a. id terbesar di menurole' AS cek, MAX(id) AS id_terbesar FROM menurole;
-- harap: 143 atau 144. Kalau >= 145, JANGAN lanjut - ganti nomornya dulu.

SELECT '0b. apakah 145 sudah dipakai' AS cek, COUNT(*) AS baris FROM menurole WHERE id = 145;
-- harus: 0

SELECT '0c. apakah namanya sudah ada' AS cek, COUNT(*) AS baris
FROM menurole WHERE menu = 'Update Payment Plan';
-- harus: 0

SELECT '0d. calon penerima (pemegang Payment Voucher AP)' AS cek, COUNT(*) AS pengguna
FROM useraccess WHERE menu = 'Payment Voucher AP';

SELECT '0e. apakah tabelnya sudah ada' AS cek, COUNT(*) AS baris
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name IN ('ap_payment_plan_h', 'ap_payment_plan_det');
-- harus: 0

-- ============================================================================
-- MULAI PERUBAHAN
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Kepala dokumen rencana bayar
--
-- Bentuknya mengikuti tbl_duedate_update_h di AR supaya alurnya seragam:
-- nomor dokumen AP-PP/<bulan><tahun>/<urut 5 digit>, satu tanggal rencana
-- untuk seluruh isinya, dan status POST / CANCEL.
--
-- Sengaja TABEL SENDIRI, bukan kolom baru di kontrabon_h: tabel itu dipakai
-- belasan layar yang sudah jalan di produksi, dan satu voucher bisa
-- dijadwalkan ulang berkali-kali.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ap_payment_plan_h (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    doc_number    VARCHAR(100) NOT NULL,
    plan_date     DATE                  DEFAULT NULL,
    keterangan    VARCHAR(255)          DEFAULT NULL,
    status        VARCHAR(20)           DEFAULT 'POST',
    created_by    VARCHAR(100)          DEFAULT NULL,
    created_date  DATETIME              DEFAULT NULL,
    cancel_by     VARCHAR(100)          DEFAULT NULL,
    cancel_date   DATETIME              DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_apph_doc (doc_number),
    KEY ix_apph_plan_date (plan_date),
    KEY ix_apph_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------------------------
-- 2. Isi dokumennya
--
-- status 'Y' = berlaku, 'N' = ikut dibatalkan bersama kepalanya. Laporan
-- proyeksi membaca baris 'Y' TERBARU per no_kbon, jadi penjadwalan ulang
-- cukup dgn membuat dokumen baru.
--
-- Yang disimpan hanya nomor voucher, tanggal rencana, dan nilainya saat
-- dijadwalkan. Relationship, Item Type dan Flexibility TIDAK ikut disalin -
-- ketiganya punya sumbernya sendiri dan dibaca langsung waktu laporan
-- dibuka:
--   Relationship & Item Type : mastercoa_v2.relasi / item_type1, lewat
--       kontrabon_h.no_coa - sumber yang sama dgn laporan AP (Payment
--       Voucher, ap_report2/query_pv2.php). Dicocokkan ke lembar kerja
--       Okt 2026: 93 dari 95 voucher yang ada di salinan lokal cocok untuk
--       DUA-DUANYA.
--   Flexibility Category     : mastersupplier.payment_rating.
-- Menyalinnya ke sini cuma akan membuat dua sumber yang bisa berbeda.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ap_payment_plan_det (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    doc_number    VARCHAR(100) NOT NULL,
    no_kbon       VARCHAR(255) NOT NULL,
    plan_date     DATE                  DEFAULT NULL,
    curr          VARCHAR(50)           DEFAULT NULL,
    amount        DECIMAL(16,2)         DEFAULT 0,
    keterangan    VARCHAR(255)          DEFAULT NULL,
    status        CHAR(1)               DEFAULT 'Y',
    PRIMARY KEY (id),
    KEY ix_appd_doc (doc_number),
    KEY ix_appd_kbon (no_kbon),
    KEY ix_appd_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------------------------
-- 3. Menunya
--
-- menu_group 'AP' dan profit_center 'NAG' mengikuti baris menu AP lainnya.
-- status diisi 'Menu' supaya ikut terbaca penyaring menurole.status = 'Menu'
-- yang dipakai beberapa blok di module/menu.php.
--
-- SATU hak akses untuk dua layar: daftarnya (list-payment-plan.php) dan
-- formulirnya (create-payment-plan.php) yang dibuka lewat tombol Create -
-- sama seperti List/Create Due Date Update di AR.
-- ----------------------------------------------------------------------------
INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 145, 'Update Payment Plan', 'Update Payment Plan', 'AP', 'Menu', 'NAG'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE id = 145 OR menu = 'Update Payment Plan');

-- ----------------------------------------------------------------------------
-- 4. Hak aksesnya
--
-- Diberikan ke pemegang 'Payment Voucher AP' - merekalah yang memegang
-- voucher yang mau dijadwalkan. Hak aksesnya SENDIRI (tidak menumpang
-- 'Payment Voucher AP'), supaya bisa dicabut atau diberikan ke orang lain
-- tanpa mengubah hak akses voucher itu sendiri.
-- ----------------------------------------------------------------------------
INSERT INTO useraccess (username, menu)
SELECT DISTINCT u.username, 'Update Payment Plan'
FROM useraccess u
WHERE u.menu = 'Payment Voucher AP'
  AND NOT EXISTS (
      SELECT 1 FROM useraccess x
      WHERE x.username = u.username AND x.menu = 'Update Payment Plan');

-- ============================================================================
-- 5. PERIKSA HASILNYA
-- ============================================================================
SELECT '5a. baris menurole' AS cek, id, menu, menu_group, status, profit_center
FROM menurole WHERE menu = 'Update Payment Plan';
-- harus: 1 baris, id 145

SELECT '5b. jumlah penerima hak akses' AS cek, COUNT(*) AS pengguna
FROM useraccess WHERE menu = 'Update Payment Plan';
-- harus sama dengan hasil 0d

SELECT '5c. tabelnya' AS cek, table_name, engine
FROM information_schema.tables
WHERE table_schema = DATABASE()
  AND table_name IN ('ap_payment_plan_h', 'ap_payment_plan_det');
-- harus: 2 baris, InnoDB

SELECT '5d. isi dokumen rencana' AS cek, COUNT(*) AS baris FROM ap_payment_plan_h;
-- 0 di awal - diisi lewat menu Update Payment Plan, bukan lewat SQL
