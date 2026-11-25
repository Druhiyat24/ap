-- ===========================================================================
-- GABUNGAN MIGRASI PRODUKSI - 25 November 2026
-- Dijalankan di DATABASE ERP PRODUKSI: signalbit_erp @ 10.10.5.12
--
-- SATU berkas, TIGA bagian. Menggantikan tiga berkas terpisah berikut; ketiga
-- berkas aslinya DIBIARKAN di repo sebagai riwayat, tapi JANGAN dijalankan
-- sendiri-sendiri lagi:
--   A  db_migrate_LIVE_20261123_mj_thr.sql        (Memorial Journal THR)
--   B  db_migrate_LIVE_20261124_forex_ap_coa.sql  (kolom forex_ap)
--   C  db_migrate_LIVE_20261124_forex_ar_coa.sql  (kolom forex_ar)
--
-- AMAN DIJALANKAN BERULANG. Tidak ada DROP, tidak ada DELETE. Penambahan
-- kolom dipagari information_schema, pembuatan tabel pakai IF NOT EXISTS, dan
-- pencentangan COA awal HANYA jalan kalau belum ada satu pun COA tercentang -
-- jadi kalau nanti Anda menambah/mengurangi centang dari menu Master COA,
-- menjalankan berkas ini lagi TIDAK akan menyetel ulang pilihan Anda.
--
-- KEADAAN PRODUKSI SAAT BERKAS INI DIBUAT (diperiksa langsung 25 Nov 2026):
--   - bankin_no_approve / bankout_no_approve  : SUDAH ada
--   - forex_ap                                : SUDAH ada, 51 COA tercentang
--                                               -> BAGIAN B akan melewati diri
--                                                  sendiri, itu wajar
--   - forex_ar                                : BELUM ada   -> BAGIAN C perlu
--   - master_category_mj.status_hris CMJ004   : masih NULL  -> BAGIAN A perlu
--   - tabel log_jurnal_thr                    : BELUM ada   -> BAGIAN A perlu
--   - jurnal FX-AP / FX-AR                    : belum ada satu pun
--
-- TIDAK ADA perubahan apa pun di database HRIS (hris_nag @ 10.10.5.111).
-- Jalur THR hanya MEMBACA thr_jurnals / employee_atribut di sana.
--
-- SESUDAH BERKAS INI JALAN, kode boleh di-deploy. Perhatikan urutannya:
-- insertmastercoa.php menyebut forex_ap DAN forex_ar di INSERT-nya, jadi
-- kalau kodenya naik duluan, MEMBUAT COA BARU akan gagal.
-- ===========================================================================


-- ===========================================================================
-- BAGIAN A - MEMORIAL JOURNAL, TYPE "ACCRUAL RELIGIOUS HOLIDAY ALLOWANCE"
-- ===========================================================================

-- A1. Memunculkan CMJ004 di dropdown TYPE pada tab "Journal From HRIS".
--     Barisnya SUDAH ADA sejak lama; yang kurang hanya penanda status_hris.
UPDATE `master_category_mj`
   SET `status_hris` = 'Y'
 WHERE `id_cmj` = 'CMJ004';


-- A2. Penahan jurnal ganda per periode THR.
--     periode = tanggal 1 bulan periode (mis. 2026-10-01 untuk Oktober 2026).
--     status  = 'POST' saat jurnal terbentuk, 'CANCEL' saat jurnal dibatalkan
--               (lihat cancel_memorialjournal.php & cancel_journal_all.php).
--     SENGAJA TIDAK UNIQUE pada periode: satu periode boleh punya beberapa
--     baris sepanjang yang lama sudah berstatus CANCEL - kalau dibuat unique,
--     periode yang jurnalnya pernah dibatalkan tidak akan bisa dijurnal lagi.
--
--     Diletakkan di ERP, BUKAN di HRIS seperti log_jurnal_bpjs, karena jalur
--     THR hanya boleh MEMBACA database HRIS.
CREATE TABLE IF NOT EXISTS `log_jurnal_thr` (
  `id`           int(11)      NOT NULL AUTO_INCREMENT,
  `no_journal`   varchar(50)  DEFAULT NULL,
  `periode`      date         DEFAULT NULL,
  `status`       varchar(20)  DEFAULT NULL,
  `created_by`   varchar(100) DEFAULT NULL,
  `created_date` datetime     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `no_journal` (`no_journal`),
  KEY `periode_status` (`periode`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ===========================================================================
-- BAGIAN B - KOLOM forex_ap (AUTO JURNAL SELISIH KURS AP / UTANG USAHA)
-- ===========================================================================
-- Polanya meniru bankin_no_approve / bankout_no_approve di tabel yang sama:
-- satu flag per COA, bukan tabel mapping terpisah - supaya menambah COA cukup
-- lewat dropdown di menu Master COA, tanpa SQL dan tanpa mengubah kode.

-- B1. Kolom penanda, hanya kalau belum ada.
SET @ada_ap := (SELECT COUNT(*) FROM information_schema.columns
                 WHERE table_schema = DATABASE()
                   AND table_name   = 'mastercoa_v2'
                   AND column_name  = 'forex_ap');
SET @sql := IF(@ada_ap = 0,
    'ALTER TABLE `mastercoa_v2` ADD COLUMN `forex_ap` char(1) NOT NULL DEFAULT ''N'' COMMENT ''Y = saldo valas COA utang ini ikut auto jurnal selisih kurs AP'' AFTER `bankout_no_approve`',
    'SELECT ''BAGIAN B1 dilewati - kolom forex_ap sudah ada'' AS keterangan');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;


-- B2. Centang awal = 51 COA yang dipakai file accounting
--     "Forex Gain or Loss - AP.xlsx" (sheet Forex AP - GR), apa adanya.
--     HANYA dijalankan kalau belum ada satu pun COA tercentang, supaya
--     menjalankan berkas ini lagi tidak menyetel ulang pilihan dari layar.
SET @sudah_ap := (SELECT COUNT(*) FROM `mastercoa_v2` WHERE `forex_ap` = 'Y');
SET @sql := IF(@sudah_ap = 0,
    'UPDATE `mastercoa_v2` SET `forex_ap` = ''Y'' WHERE `no_coa` IN (
       ''2.10.01'',''2.10.02'',''2.10.03'',''2.10.04'',''2.10.05'',''2.10.06'',''2.10.07'',
       ''2.10.11'',''2.10.12'',''2.10.13'',''2.10.14'',''2.10.99'',
       ''2.11.01'',''2.11.02'',''2.11.03'',''2.11.04'',''2.11.05'',''2.11.06'',''2.11.07'',
       ''2.11.11'',''2.11.12'',''2.11.13'',''2.11.14'',''2.11.15'',''2.11.99'',
       ''2.12.01'',''2.12.02'',''2.12.03'',''2.12.04'',''2.12.05'',''2.12.06'',''2.12.07'',
       ''2.12.11'',''2.12.12'',''2.12.13'',''2.12.14'',''2.12.15'',''2.12.99'',
       ''2.13.01'',''2.13.02'',''2.13.03'',''2.13.04'',''2.13.05'',''2.13.06'',''2.13.07'',
       ''2.13.11'',''2.13.12'',''2.13.13'',''2.13.14'',''2.13.15'',''2.13.99'')',
    'SELECT ''BAGIAN B2 dilewati - sudah ada COA ber-forex_ap=Y, pilihan yang ada tidak disentuh'' AS keterangan');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;


-- ===========================================================================
-- BAGIAN C - KOLOM forex_ar (AUTO JURNAL SELISIH KURS AR / PIUTANG USAHA)
-- ===========================================================================
-- Kembaran BAGIAN B untuk sisi piutang.

-- C1. Kolom penanda, hanya kalau belum ada.
--     Diletakkan sesudah forex_ap supaya urutan kolomnya rapi; kalau forex_ap
--     baru saja dibuat di B1, kolom itu sudah ada waktu baris ini jalan.
SET @ada_ar := (SELECT COUNT(*) FROM information_schema.columns
                 WHERE table_schema = DATABASE()
                   AND table_name   = 'mastercoa_v2'
                   AND column_name  = 'forex_ar');
SET @sql := IF(@ada_ar = 0,
    'ALTER TABLE `mastercoa_v2` ADD COLUMN `forex_ar` char(1) NOT NULL DEFAULT ''N'' COMMENT ''Y = saldo valas COA piutang ini ikut auto jurnal selisih kurs AR'' AFTER `forex_ap`',
    'SELECT ''BAGIAN C1 dilewati - kolom forex_ar sudah ada'' AS keterangan');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;


-- C2. Centang awal = 5 COA yang dipakai file accounting
--     "Forex Gain or Loss - AR.xlsx" (sheet Forex AR Calc), apa adanya.
--       1.30.01  PIUTANG USAHA PIHAK KETIGA - LOKAL
--       1.30.02  PIUTANG USAHA PIHAK KETIGA - EKSPOR
--       1.32.01  PIUTANG USAHA PIHAK BERELASI - LOKAL
--       1.32.02  PIUTANG USAHA PIHAK BERELASI - EKSPOR
--       1.34.01  PIUTANG LAIN-LAIN PIHAK KETIGA - LOKAL
SET @sudah_ar := (SELECT COUNT(*) FROM `mastercoa_v2` WHERE `forex_ar` = 'Y');
SET @sql := IF(@sudah_ar = 0,
    'UPDATE `mastercoa_v2` SET `forex_ar` = ''Y'' WHERE `no_coa` IN (
       ''1.30.01'',''1.30.02'',''1.32.01'',''1.32.02'',''1.34.01'')',
    'SELECT ''BAGIAN C2 dilewati - sudah ada COA ber-forex_ar=Y, pilihan yang ada tidak disentuh'' AS keterangan');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;


-- ===========================================================================
-- PEMERIKSAAN - ikut tercetak, tidak perlu diketik ulang
-- ===========================================================================
SELECT 'A1 CMJ004 muncul di Journal From HRIS' AS pemeriksaan,
       IF(COUNT(*) = 1, 'OK', 'GAGAL') AS hasil,
       CONCAT(COUNT(*), ' baris (harus 1)') AS rinci
  FROM `master_category_mj` WHERE `id_cmj` = 'CMJ004' AND `status_hris` = 'Y'
UNION ALL
SELECT 'A2 tabel log_jurnal_thr ada',
       IF(COUNT(*) = 1, 'OK', 'GAGAL'),
       CONCAT(COUNT(*), ' tabel (harus 1)')
  FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'log_jurnal_thr'
UNION ALL
SELECT 'B1 kolom mastercoa_v2.forex_ap ada',
       IF(COUNT(*) = 1, 'OK', 'GAGAL'),
       CONCAT(COUNT(*), ' kolom (harus 1)')
  FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'mastercoa_v2' AND column_name = 'forex_ap'
UNION ALL
SELECT 'B2 COA utang tercentang',
       IF(COUNT(*) > 0, 'OK', 'GAGAL'),
       CONCAT(COUNT(*), ' COA (pemasangan awal = 51)')
  FROM `mastercoa_v2` WHERE `forex_ap` = 'Y'
UNION ALL
SELECT 'C1 kolom mastercoa_v2.forex_ar ada',
       IF(COUNT(*) = 1, 'OK', 'GAGAL'),
       CONCAT(COUNT(*), ' kolom (harus 1)')
  FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'mastercoa_v2' AND column_name = 'forex_ar'
UNION ALL
SELECT 'C2 COA piutang tercentang',
       IF(COUNT(*) > 0, 'OK', 'GAGAL'),
       CONCAT(COUNT(*), ' COA (pemasangan awal = 5)')
  FROM `mastercoa_v2` WHERE `forex_ar` = 'Y';


-- ===========================================================================
-- CATATAN YANG PERLU DIBACA SEKALI
-- ===========================================================================
-- 1. PERIODE THR YANG SUDAH PERNAH DIJURNAL MANUAL (Apr s/d Sep 2026 ada
--    datanya di HRIS) tidak dikenali log_jurnal_thr yang baru dibuat ini,
--    jadi periode itu masih bisa ditarik lagi lewat menu. Kalau tidak boleh,
--    isikan sendiri barisnya, satu baris per periode, contoh:
--      INSERT INTO log_jurnal_thr (no_journal, periode, status, created_by, created_date)
--      VALUES ('<no jurnal manualnya>', '2026-09-01', 'POST', 'migrasi', NOW());
--
-- 2. COA UTANG AKTIF YANG SENGAJA TIDAK DICENTANG - tidak ada di file
--    accounting. Tinggal dicentang dari menu Master COA kalau accounting
--    setuju, TIDAK perlu SQL lagi:
--      2.10.15  GR/IR PIHAK KETIGA - MAKLOON DYEING  (tidak ada mutasi USD)
--      2.11.98  UTANG USAHA PIHAK KETIGA - BENANG KNITTING
--               ADA eksposur nyata: NAK 1.998.312,77 USD + NAG 82.491,07 USD
--
-- 3. COA PIUTANG YANG SENGAJA TIDAK DICENTANG, padahal bersaldo USD di buku
--    besar (per 20 Nov 2026). Semuanya bersaldo NEGATIF - ketiganya dipakai
--    sebagai akun penampung/kliring impor, bukan piutang dagang. Jangan
--    dicentang sebelum accounting memastikan itu memang eksposur valas:
--      1.34.04  FORWARDER    NAG   -446.921,20 ; NAK -128.112,38
--      1.34.05  BAHAN BAKU   NAG   -206.091,75 ; NAK     -143,11
--      1.34.99  LAIN-LAIN    NAG -1.262.713,87 ; NAK   +1.551,58
--
-- 4. DAFTAR COA YANG BELUM DICENTANG, untuk ditinjau sewaktu-waktu:
--      SELECT no_coa, nama_coa, forex_ap, forex_ar FROM mastercoa_v2
--       WHERE status = 'Active'
--         AND (no_coa LIKE '2.1%' OR no_coa LIKE '1.3%')
--         AND forex_ap = 'N' AND forex_ar = 'N'
--       ORDER BY no_coa;
-- ===========================================================================
