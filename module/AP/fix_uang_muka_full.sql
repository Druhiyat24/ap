-- ===========================================================================
-- Uang muka CBD ber-PPh: selaraskan PV penyelesai ke NILAI PENUH
--
-- Saat Bank Out, COA uang muka didebit sebesar nilai PENUH (mis. 3.825.000),
-- karena PPh 95.625 disetor ke kantor pajak ATAS NAMA SUPPLIER - jadi uang
-- muka ke supplier tetap sebesar nilai penuh. PV penyelesainya dulu hanya
-- mengkredit nilai netto (3.729.375), sehingga sisa sebesar PPh menggantung
-- di COA uang muka dan di Purchase Advance Report.
--
-- Kodenya sudah diperbaiki: daftar FTR di form PV AP (pv_regular.php,
-- pv_installment.php, edit_pv_regular_new.php) sekarang memakai
-- (total + pph), jadi PV BARU otomatis mengkredit penuh. Skrip ini hanya
-- membetulkan 3 dokumen lama.
--
-- Tiap PV disesuaikan pada TIGA tempat sekaligus supaya konsisten:
--   1. jurnal Dr Utang Usaha (offset uang muka)
--   2. jurnal Cr Uang Muka
--   3. kontrabon_ftr.total_ftr  <- WAJIB, kalau tidak, sisa PPh akan terus
--      muncul sebagai "masih bisa dipotong" di form PV AP.
-- Jurnal tetap balance karena (1) dan (2) diubah dengan nilai yang sama.
--
-- Semua UPDATE memakai id + nilai lama sebagai pengaman, jadi aman
-- dijalankan ulang (baris yang sudah benar berubah 0 baris).
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

-- SEBELUM - lihat kondisi ketiga PV
SELECT j.no_journal, j.no_coa, j.debit, j.credit, f.total_ftr
FROM tbl_list_journal j
LEFT JOIN kontrabon_ftr f ON f.no_kbon = j.no_journal
WHERE j.id IN (1063553, 1063555, 1063564, 1063566, 1063568, 1063570);

-- ───────────────────────────────────────────────────────────────────────────
-- PV-AP/REG/NAG/2026/09/02282   (uang muka penuh = 3,825,000.00)
UPDATE tbl_list_journal SET debit = 3825000.00, debit_idr = 3825000.00
 WHERE id = 1063553 AND no_journal = 'PV-AP/REG/NAG/2026/09/02282' AND no_coa = '2.11.99' AND debit = 3729375.0000;
UPDATE tbl_list_journal SET credit = 3825000.00, credit_idr = 3825000.00
 WHERE id = 1063555 AND no_journal = 'PV-AP/REG/NAG/2026/09/02282' AND no_coa = '1.49.99' AND credit = 3729375.0000;
UPDATE kontrabon_ftr SET total_ftr = 3825000.00
 WHERE id = 318 AND no_kbon = 'PV-AP/REG/NAG/2026/09/02282' AND no_ftr = 'FTR/C/NAG/0826/00675' AND total_ftr = 3729375;

-- ───────────────────────────────────────────────────────────────────────────
-- PV-AP/REG/NAG/2026/09/02283   (uang muka penuh = 3,825,000.00)
-- jurnal id 1063564 (2.11.99) sudah penuh, tidak perlu diubah.
-- jurnal id 1063566 (1.49.99) sudah penuh, tidak perlu diubah.
UPDATE kontrabon_ftr SET total_ftr = 3825000.00
 WHERE id = 319 AND no_kbon = 'PV-AP/REG/NAG/2026/09/02283' AND no_ftr = 'FTR/C/NAG/0826/00679' AND total_ftr = 3729375;

-- ───────────────────────────────────────────────────────────────────────────
-- PV-AP/REG/NAG/2026/09/02284   (uang muka penuh = 3,250,000.00)
UPDATE tbl_list_journal SET debit = 3250000.00, debit_idr = 3250000.00
 WHERE id = 1063568 AND no_journal = 'PV-AP/REG/NAG/2026/09/02284' AND no_coa = '2.11.99' AND debit = 3168750.0000;
UPDATE tbl_list_journal SET credit = 3250000.00, credit_idr = 3250000.00
 WHERE id = 1063570 AND no_journal = 'PV-AP/REG/NAG/2026/09/02284' AND no_coa = '1.49.99' AND credit = 3168750.0000;
UPDATE kontrabon_ftr SET total_ftr = 3250000.00
 WHERE id = 320 AND no_kbon = 'PV-AP/REG/NAG/2026/09/02284' AND no_ftr = 'FTR/C/NAG/0926/00684' AND total_ftr = 3168750;

-- SESUDAH - ketiga jurnal harus tetap balance (selisih 0)
SELECT no_journal, ROUND(SUM(debit) - SUM(credit), 2) AS selisih
FROM tbl_list_journal
WHERE no_journal IN ('PV-AP/REG/NAG/2026/09/02282','PV-AP/REG/NAG/2026/09/02283','PV-AP/REG/NAG/2026/09/02284')
  AND type_journal = 'AP - Kontrabon'
GROUP BY no_journal;
