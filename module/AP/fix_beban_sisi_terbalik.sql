-- ===========================================================================
-- Koreksi baris BEBAN yang terjurnal di SISI TERBALIK
--
-- Sebab: uji tanda beban header di insertkbon_*.php memakai ambang ">= 1",
-- padahal maksudnya "> 0". Nilai POSITIF di bawah 1 (selisih pembulatan
-- DPP+PPN vs total faktur, mis. 0,05) ikut masuk cabang negatif dan
-- dijurnal sebagai CREDIT, padahal seharusnya DEBIT.
--
-- Dampak per dokumen:
--   * jurnal PV tidak balance sebesar 2x nilai itu;
--   * di AP Report kolom Deduction Others (= SUM(debit - credit) baris BEBAN)
--     muncul MINUS padahal user mengisi plus, sehingga Ending Balance meleset
--     (mis. PV-AP/REG/NAK/2026/09/02362 jadi -0,02 padahal harusnya 0,00).
--
-- Kodenya sudah diperbaiki (>= 1 -> > 0) di 6 berkas, jadi dokumen BARU
-- tidak akan terkena lagi. Skrip ini hanya membetulkan 6 baris lama.
-- Ditelusuri ke seluruh tbl_list_journal + tbl_list_journal_cancel sejak
-- 1 Juli 2026: TEPAT 6 baris ini yang terdampak, tidak ada yang lain.
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

-- SEBELUM - harus keluar 6 baris, semuanya selisih != 0
SELECT j.no_journal, j.type_journal, j.no_coa, j.debit, j.credit,
       (SELECT ROUND(SUM(x.debit) - SUM(x.credit), 2) FROM tbl_list_journal x
         WHERE x.no_journal = j.no_journal AND x.type_journal = j.type_journal) AS selisih_jurnal
FROM tbl_list_journal j
WHERE j.id IN (1046810, 1065271, 1071279, 1071603, 1071643, 1071650);

-- Setiap UPDATE memakai id + nilai lama sebagai pengaman: kalau baris sudah
-- pernah dibetulkan, statement-nya mengubah 0 baris (aman dijalankan ulang).

-- PV-AP/REG/NAG/2026/08/02112 (AP - Kontrabon) : CREDIT 0.03  ->  DEBIT 0.03
UPDATE tbl_list_journal SET debit = 0.0300, credit = 0.0000, debit_idr = 0.03, credit_idr = 0.00
WHERE id = 1046810 AND no_journal = 'PV-AP/REG/NAG/2026/08/02112' AND no_coa = '5.97.02' AND debit = 0.0000 AND credit = 0.0300;

-- PV-AP/REG/NAG/2026/09/02304 (AP - Kontrabon) : CREDIT 0.04  ->  DEBIT 0.04
UPDATE tbl_list_journal SET debit = 0.0400, credit = 0.0000, debit_idr = 0.04, credit_idr = 0.00
WHERE id = 1065271 AND no_journal = 'PV-AP/REG/NAG/2026/09/02304' AND no_coa = '5.97.02' AND debit = 0.0000 AND credit = 0.0400;

-- PV-AP/REG/NAG/2026/09/02358 (AP - Kontrabon) : CREDIT 0.05  ->  DEBIT 0.05
UPDATE tbl_list_journal SET debit = 0.0500, credit = 0.0000, debit_idr = 0.05, credit_idr = 0.00
WHERE id = 1071279 AND no_journal = 'PV-AP/REG/NAG/2026/09/02358' AND no_coa = '5.97.02' AND debit = 0.0000 AND credit = 0.0500;

-- PV-AP/REG/NAK/2026/09/02362 (AP - Kontrabon) : CREDIT 0.01  ->  DEBIT 0.01
UPDATE tbl_list_journal SET debit = 0.0100, credit = 0.0000, debit_idr = 0.01, credit_idr = 0.00
WHERE id = 1071603 AND no_journal = 'PV-AP/REG/NAK/2026/09/02362' AND no_coa = '5.97.02' AND debit = 0.0000 AND credit = 0.0100;

-- PV-AP/REG/NAK/2026/09/02362 (Reverse AP - Kontrabon) : DEBIT 0.01  ->  CREDIT 0.01
UPDATE tbl_list_journal SET debit = 0.0000, credit = 0.0100, debit_idr = 0.00, credit_idr = 0.01
WHERE id = 1071643 AND no_journal = 'PV-AP/REG/NAK/2026/09/02362' AND no_coa = '5.97.02' AND debit = 0.0100 AND credit = 0.0000;

-- PV-AP/REG/NAK/2026/09/02362-REV_01 (AP - Kontrabon) : CREDIT 0.01  ->  DEBIT 0.01
UPDATE tbl_list_journal SET debit = 0.0100, credit = 0.0000, debit_idr = 0.01, credit_idr = 0.00
WHERE id = 1071650 AND no_journal = 'PV-AP/REG/NAK/2026/09/02362-REV_01' AND no_coa = '5.97.02' AND debit = 0.0000 AND credit = 0.0100;

-- SESUDAH - keenam baris harus selisih_jurnal = 0.00
SELECT j.no_journal, j.type_journal, j.no_coa, j.debit, j.credit,
       (SELECT ROUND(SUM(x.debit) - SUM(x.credit), 2) FROM tbl_list_journal x
         WHERE x.no_journal = j.no_journal AND x.type_journal = j.type_journal) AS selisih_jurnal
FROM tbl_list_journal j
WHERE j.id IN (1046810, 1065271, 1071279, 1071603, 1071643, 1071650);
