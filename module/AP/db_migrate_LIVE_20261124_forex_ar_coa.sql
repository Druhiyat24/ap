-- ===========================================================================
-- AUTO JURNAL SELISIH KURS AR - MAPPING COA PIUTANG YANG DIREVALUASI
-- Dijalankan di DATABASE ERP PRODUKSI: signalbit_erp @ 10.10.5.12
-- Tanggal siap: 24 November 2026
--
-- Kembaran db_migrate_LIVE_20261124_forex_ap_coa.sql, untuk sisi PIUTANG.
-- Polanya sama: satu flag per COA di mastercoa_v2, bukan tabel mapping
-- terpisah, supaya menambah COA cukup lewat dropdown di menu Master COA.
--
-- BOLEH dijalankan tanpa menunggu yang AP, dan sebaliknya - keduanya hanya
-- menambah kolom sendiri-sendiri.
-- ===========================================================================


-- 1. Kolom penanda.
--    'Y' = saldo COA ini direvaluasi auto jurnal selisih kurs AR.
ALTER TABLE `mastercoa_v2`
  ADD COLUMN `forex_ar` char(1) NOT NULL DEFAULT 'N'
  COMMENT 'Y = saldo valas COA piutang ini ikut auto jurnal selisih kurs AR'
  AFTER `bankout_no_approve`;


-- 2. Isi awal = 5 COA yang dipakai file accounting
--    "Forex Gain or Loss - AR.xlsx" (sheet Forex AR Calc), apa adanya.
UPDATE `mastercoa_v2` SET `forex_ar` = 'Y' WHERE `no_coa` IN (
  '1.30.01',   -- PIUTANG USAHA PIHAK KETIGA - LOKAL
  '1.30.02',   -- PIUTANG USAHA PIHAK KETIGA - EKSPOR
  '1.32.01',   -- PIUTANG USAHA PIHAK BERELASI - LOKAL
  '1.32.02',   -- PIUTANG USAHA PIHAK BERELASI - EKSPOR
  '1.34.01'    -- PIUTANG LAIN-LAIN PIHAK KETIGA - LOKAL
);


-- ===========================================================================
-- PEMERIKSAAN SESUDAH DIJALANKAN
-- ===========================================================================
-- Harus memberi 5
-- SELECT COUNT(*) FROM mastercoa_v2 WHERE forex_ar = 'Y';
--
-- COA PIUTANG LAIN YANG SENGAJA DIBIARKAN 'N' karena tidak ada di file
-- accounting. Beberapa di antaranya PUNYA saldo USD di buku besar, jadi
-- selama 'N' saldo itu memang tidak pernah direvaluasi - sama persis dengan
-- keadaan sekarang di Excel. Tinggal dicentang di menu Master COA kalau
-- accounting setuju, tidak perlu SQL lagi. Saldo USD per 20 Nov 2026:
--   1.34.04  PIHAK KETIGA - FORWARDER    NAG -446.921,20 ; NAK -128.112,38
--   1.34.05  PIHAK KETIGA - BAHAN BAKU   NAG -206.091,75 ; NAK    -143,11
--   1.34.99  PIHAK KETIGA - LAIN-LAIN    NAG -1.262.713,87 ; NAK  1.551,58
-- Angkanya negatif - ketiganya dipakai sebagai akun penampung/kliring impor,
-- bukan piutang dagang. Jangan dicentang sebelum accounting memastikan
-- saldonya memang eksposur valas yang harus direvaluasi.
--
-- Daftar COA piutang aktif yang BELUM dicentang, untuk ditinjau sewaktu-waktu:
-- SELECT no_coa, nama_coa FROM mastercoa_v2
--  WHERE (no_coa LIKE '1.30.%' OR no_coa LIKE '1.31.%' OR no_coa LIKE '1.32.%'
--      OR no_coa LIKE '1.33.%' OR no_coa LIKE '1.34.%')
--    AND status = 'Active' AND forex_ar = 'N' ORDER BY no_coa;
-- ===========================================================================
