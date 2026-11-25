-- ===========================================================================
-- AUTO JURNAL SELISIH KURS AP - MAPPING COA UTANG YANG DIREVALUASI
-- Dijalankan di DATABASE ERP PRODUKSI: signalbit_erp @ 10.10.5.12
-- Tanggal siap: 24 November 2026
--
-- Polanya meniru bankin_no_approve / bankout_no_approve yang sudah ada di
-- tabel yang sama: satu flag per COA, bukan tabel mapping terpisah.
-- Alasannya menu Master COA SUDAH punya form untuk kolom semacam ini
-- (master-coa.php / create-master-coa.php / edit-master-coa.php), jadi
-- menambah COA baru nanti cukup pilih dropdown di layar, tidak perlu SQL
-- dan tidak perlu mengubah kode.
--
-- YANG SENGAJA TIDAK DIPETAKAN DI SINI:
--   1. PROFIT CENTER. Diambil apa adanya dari tbl_list_journal.profit_center.
--      Template Excel mematok 2.10/2.11/2.13 = NAG dan 2.12 = NAK, dan itu
--      meleset: 2.10.11 bersaldo di NAG MAUPUN NAK, 2.12.01 ada mutasi NAG,
--      2.11.11 justru NAK. Kalau dipetakan, tiap kombinasi baru harus
--      didaftarkan lagi.
--   2. SUMBER SALDO AWAL (AP Report BPB vs PV). query_bpb2.php sudah
--      menyaring sendiri nama_coa like 'GR%' dan query_pv2.php memegang
--      UTANG USAHA - keduanya sudah mengeluarkan no_coa + profit_center.
--   3. COST CENTER lawan untuk 8.52.02. Sudah dicari fxSkCcSelisih() dari
--      master per profit center (NAG -> DEP06SUB001, NAK -> DEPNK09SUB001).
-- ===========================================================================


-- 1. Kolom penanda.
--    'Y' = saldo COA ini direvaluasi auto jurnal selisih kurs AP.
ALTER TABLE `mastercoa_v2`
  ADD COLUMN `forex_ap` char(1) NOT NULL DEFAULT 'N'
  COMMENT 'Y = saldo valas COA utang ini ikut auto jurnal selisih kurs AP'
  AFTER `bankout_no_approve`;


-- 2. Isi awal = 51 COA yang dipakai file accounting
--    "Forex Gain or Loss - AP.xlsx" (sheet Forex AP - GR), apa adanya.
UPDATE `mastercoa_v2` SET `forex_ap` = 'Y' WHERE `no_coa` IN (
  -- GR/IR PIHAK KETIGA (12)
  '2.10.01','2.10.02','2.10.03','2.10.04','2.10.05','2.10.06','2.10.07',
  '2.10.11','2.10.12','2.10.13','2.10.14','2.10.99',
  -- UTANG USAHA PIHAK KETIGA (13)
  '2.11.01','2.11.02','2.11.03','2.11.04','2.11.05','2.11.06','2.11.07',
  '2.11.11','2.11.12','2.11.13','2.11.14','2.11.15','2.11.99',
  -- GR/IR PIHAK BERELASI (13)
  '2.12.01','2.12.02','2.12.03','2.12.04','2.12.05','2.12.06','2.12.07',
  '2.12.11','2.12.12','2.12.13','2.12.14','2.12.15','2.12.99',
  -- UTANG USAHA PIHAK BERELASI (13)
  '2.13.01','2.13.02','2.13.03','2.13.04','2.13.05','2.13.06','2.13.07',
  '2.13.11','2.13.12','2.13.13','2.13.14','2.13.15','2.13.99'
);


-- ===========================================================================
-- PEMERIKSAAN SESUDAH DIJALANKAN
-- ===========================================================================
-- Harus memberi 51
-- SELECT COUNT(*) FROM mastercoa_v2 WHERE forex_ap = 'Y';
--
-- DUA COA UTANG AKTIF YANG SENGAJA DIBIARKAN 'N' karena memang tidak ada di
-- file accounting. Keduanya tinggal dicentang di menu Master COA kalau
-- accounting setuju - TIDAK perlu SQL lagi:
--   2.10.15  GR/IR PIHAK KETIGA - MAKLOON DYEING     (tidak ada mutasi USD)
--   2.11.98  UTANG USAHA PIHAK KETIGA - BENANG KNITTING
--            ADA eksposur nyata: NAK 1.998.312,77 USD + NAG 82.491,07 USD
--            (mutasi Jan s/d Okt 2026). Selama 'N', saldo itu tidak pernah
--            direvaluasi - sama seperti keadaan sekarang di Excel.
--
-- Daftar COA utang aktif yang BELUM dicentang, untuk ditinjau sewaktu-waktu:
-- SELECT no_coa, nama_coa FROM mastercoa_v2
--  WHERE (no_coa LIKE '2.10.%' OR no_coa LIKE '2.11.%'
--      OR no_coa LIKE '2.12.%' OR no_coa LIKE '2.13.%')
--    AND status = 'Active' AND forex_ap = 'N' ORDER BY no_coa;
-- ===========================================================================
