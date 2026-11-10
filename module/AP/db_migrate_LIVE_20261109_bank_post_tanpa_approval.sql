-- ============================================================================
-- Bank In / Bank Out tanpa approval (status 'Post') - 9 Nov 2026
--
-- WAJIB DIJALANKAN SEBELUM KODE DI-PULL: insertmastercoa.php, menu Edit COA,
-- dan bank_post_rule.php membaca kedua kolom ini. Kalau kode lebih dulu,
-- Create COA gagal dan simpan Bank In/Out tipe None error.
--
-- Aman diulang: kolom hanya ditambah kalau belum ada. BAGIAN 2 menyetel ulang
-- flag 5 COA awal ke 'Y' - kalau sesudahnya ada yang diubah lewat menu Master
-- COA, jangan jalankan BAGIAN 2 lagi.
-- ============================================================================

-- BAGIAN 1. Kolom flag di mastercoa_v2 ('Y' = tidak perlu approval).
SET @ada := (SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'mastercoa_v2' AND column_name = 'bankin_no_approve');
SET @sql := IF(@ada = 0,
    'ALTER TABLE mastercoa_v2 ADD COLUMN bankin_no_approve CHAR(1) NOT NULL DEFAULT ''N'' COMMENT ''Y = Bank In tipe None dgn COA ini tidak perlu approval (Post)''',
    'SELECT ''bankin_no_approve sudah ada'' info');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @ada := (SELECT COUNT(*) FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = 'mastercoa_v2' AND column_name = 'bankout_no_approve');
SET @sql := IF(@ada = 0,
    'ALTER TABLE mastercoa_v2 ADD COLUMN bankout_no_approve CHAR(1) NOT NULL DEFAULT ''N'' COMMENT ''Y = Bank Out tipe None dgn COA ini tidak perlu approval (Post)''',
    'SELECT ''bankout_no_approve sudah ada'' info');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- BAGIAN 2. Daftar awal dari user (9 Nov 2026).
--   Bank Masuk  : 8.05.01 Pendapatan Jasa Giro, 8.05.02 Pendapatan Bunga Deposito
--   Bank Keluar : 8.50.01 Beban Bunga Bank, 8.51.01 Beban Administrasi Bank,
--                 8.51.03 Beban Administrasi Rekening
UPDATE mastercoa_v2 SET bankin_no_approve = 'Y' WHERE no_coa IN ('8.05.01', '8.05.02');
UPDATE mastercoa_v2 SET bankout_no_approve = 'Y' WHERE no_coa IN ('8.50.01', '8.51.01', '8.51.03');

-- BAGIAN 3. Periksa: harus keluar 5 baris (2 Bank In, 3 Bank Out).
SELECT no_coa, nama_coa, bankin_no_approve, bankout_no_approve
FROM mastercoa_v2
WHERE bankin_no_approve = 'Y' OR bankout_no_approve = 'Y'
ORDER BY no_coa;
