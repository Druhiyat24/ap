-- ===========================================================================
-- PV-AP/REG/NAK/2026/09/02395-REV_01 : COA Utang Usaha KOSONG
--
-- Keluhan user: "Amount PV berbeda dengan Report AP".
-- Sebabnya: baris jurnal Utang Usaha dokumen ini (Cr 587.389.320,48) TIDAK
-- punya no_coa/nama_coa - kolomnya kosong. AP Report menyaring berdasarkan
-- COA utang, jadi nilai PV ini tidak pernah ikut terhitung di sana, padahal
-- nominalnya sendiri sudah benar (= total 576.805.729,12 + PPh 10.583.591,36).
--
-- Kosongnya berasal dari proses simpan revisi: COA utang dicari lewat
-- mastercoa_v2 (inv_type LIKE '%kbn_credit%' dgn filter cus_ctg/mattype/
-- matclass/n_code_category). Kalau lookup itu tidak dapat baris, kodenya
-- menulis string KOSONG tanpa memberi peringatan - lihat $no_coa_cre di
-- insertkbon_bulk_edit.php. Header kontrabon_h-nya ikut kosong.
--
-- Nilai yang benar diambil dari PV ASALNYA (PV-AP/REG/NAK/2026/09/02395):
--   2.13.15 - UTANG USAHA PIHAK BERELASI - MAKLOON DYEING
--
-- Ditelusuri ke seluruh jurnal AP - Kontrabon sejak 1 Juli 2026: HANYA
-- dokumen ini yang punya baris tanpa COA (1 dari 22 dokumen -REV_).
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

-- SEBELUM - kedua baris harus tampil dengan no_coa kosong
SELECT 'jurnal' AS sumber, id, no_coa, nama_coa, credit
FROM tbl_list_journal WHERE id = 1076832
UNION ALL
SELECT 'header', id, no_coa, nama_coa, total
FROM kontrabon_h WHERE no_kbon = 'PV-AP/REG/NAK/2026/09/02395-REV_01';

-- 1) Baris jurnal Utang Usaha
UPDATE tbl_list_journal
SET no_coa = '2.13.15', nama_coa = 'UTANG USAHA PIHAK BERELASI - MAKLOON DYEING'
WHERE id = 1076832
  AND no_journal = 'PV-AP/REG/NAK/2026/09/02395-REV_01'
  AND COALESCE(no_coa, '') = '';

-- 2) Header dokumen
UPDATE kontrabon_h
SET no_coa = '2.13.15', nama_coa = 'UTANG USAHA PIHAK BERELASI - MAKLOON DYEING'
WHERE no_kbon = 'PV-AP/REG/NAK/2026/09/02395-REV_01'
  AND COALESCE(no_coa, '') = '';

-- SESUDAH - keduanya harus terisi 2.13.15
SELECT 'jurnal' AS sumber, id, no_coa, nama_coa, credit
FROM tbl_list_journal WHERE id = 1076832
UNION ALL
SELECT 'header', id, no_coa, nama_coa, total
FROM kontrabon_h WHERE no_kbon = 'PV-AP/REG/NAK/2026/09/02395-REV_01';

-- Jurnal harus TETAP balance (selisih 0.00)
SELECT ROUND(SUM(debit),2) debit, ROUND(SUM(credit),2) credit,
       ROUND(SUM(debit)-SUM(credit),2) selisih
FROM tbl_list_journal WHERE no_journal = 'PV-AP/REG/NAK/2026/09/02395-REV_01';
