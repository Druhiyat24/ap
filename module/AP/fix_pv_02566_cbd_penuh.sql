-- ============================================================================
-- PERBAIKAN DATA - 16 Okt 2026  (CADANGAN, lihat "CARA YANG LEBIH BAIK" di bawah)
-- PV-AP/REG/NAG/2026/10/02566  ALVANON HK, LTD  (USD)
--
-- MASALAHNYA
--   CBD FTR/C/NAG/0826/00680 bernilai 3.080,00 sedangkan BPB GEN/IN/1026/03113
--   cuma 2.925,00. Saat PV dibuat, yang dipotong HANYA 2.925,00 dan selisih
--   155,00 dimasukkan sebagai penyesuaian MOQ. MOQ di sistem MENAMBAH utang
--   (lihat $ttl_kbon di insertkbon), jadi hasilnya:
--       Addition 2.925  -  Ded Advance 2.925  +  Ded Others 155  =  sisa 155
--   Padahal yang benar menurut pemakai:
--       Addition 2.925  -  Ded Advance 3.080  +  Ded Others 155  =  0
--
-- CARA YANG LEBIH BAIK - JANGAN PAKAI BERKAS INI KALAU MASIH BISA:
--   PV ini masih berstatus **draft**, dan form Edit PV membolehkan Amount CBD
--   diisi sampai SISA CBD-nya (3.080). Jadi cukup BUKA PV-nya lewat menu Edit,
--   ubah Amount CBD dari 2.925 menjadi 3.080, lalu simpan - jurnalnya dibangun
--   ulang oleh aplikasi sendiri, termasuk selisih kursnya. Itu jauh lebih aman
--   daripada menambal jurnal lewat SQL. Berkas ini hanya untuk keadaan di mana
--   PV-nya terlanjur tidak bisa diedit lagi.
--
-- JANGAN dijalankan sebelum dibaca. SEMUA angka di bawah diambil dari isi
-- jurnal yang sebenarnya (diperiksa 16 Okt 2026), bukan dikira-kira.
--
-- Kurs yang dipakai tiap baris (dari kolom rate pada jurnalnya):
--   2.11.99 UTANG              17.897
--   2.10.99 GR/IR              17.935
--   1.50.99 UANG MUKA          17.796
--   5.97.99 BEBAN PABRIK LAIN  17.897
-- ============================================================================

START TRANSACTION;

-- ----------------------------------------------------------------------------
-- SEBELUM - rekam dulu keadaannya
-- ----------------------------------------------------------------------------
SELECT 'SEBELUM jurnal' AS tahap, no_coa, nama_coa, debit, credit, debit_idr, credit_idr
FROM tbl_list_journal
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02566'
ORDER BY id;

SELECT 'SEBELUM ftr' AS tahap, no_ftr, total_ftr
FROM kontrabon_ftr WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02566';

SELECT 'SEBELUM potongan' AS tahap, moq, jml_potong
FROM potongan WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02566';

-- ----------------------------------------------------------------------------
-- 1. CBD yang dipotong: 2.925 -> 3.080 (seluruh nilai CBD-nya)
-- ----------------------------------------------------------------------------
UPDATE kontrabon_ftr
SET total_ftr = 3080.00
WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02566'
  AND no_ftr  = 'FTR/C/NAG/0826/00680'
  AND ROUND(total_ftr, 2) = 2925.00;
-- harap: 1 row affected

-- ----------------------------------------------------------------------------
-- 2. Jurnal UANG MUKA PEMBELIAN: kredit 2.925 -> 3.080
--    IDR = 3.080 x 17.796 = 54.811.680
-- ----------------------------------------------------------------------------
UPDATE tbl_list_journal
SET credit = 3080.00, credit_idr = 54811680.00
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02566'
  AND no_coa = '1.50.99'
  AND ROUND(credit, 2) = 2925.00;
-- harap: 1 row affected

-- ----------------------------------------------------------------------------
-- 3. Jurnal UTANG sisi DEBIT: 2.925 -> 3.080, supaya utangnya lunas
--    (sisi KREDIT utang memang sudah 3.080 = subtotal 2.925 + moq 155)
--    IDR = 3.080 x 17.897 = 55.122.760
-- ----------------------------------------------------------------------------
UPDATE tbl_list_journal
SET debit = 3080.00, debit_idr = 55122760.00
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02566'
  AND no_coa = '2.11.99'
  AND ROUND(debit, 2) = 2925.00;
-- harap: 1 row affected

-- ----------------------------------------------------------------------------
-- 4. Selisih kurs ikut berubah karena kurs tiap baris beda-beda.
--    Debit  : 2.774.035 (beban) + 55.122.760 (utang) + 52.459.875 (GR/IR)
--             = 110.356.670
--    Kredit : 55.122.760 (utang) + 54.811.680 (uang muka) = 109.934.440
--    Selisih: 422.230  -> kredit selisih kurs (sebelumnya 406.575)
-- ----------------------------------------------------------------------------
UPDATE tbl_list_journal
SET credit = 422230.00, credit_idr = 422230.00
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02566'
  AND no_coa = '8.52.02'
  AND ROUND(credit_idr, 2) = 406575.00;
-- harap: 1 row affected

-- ----------------------------------------------------------------------------
-- 5. jml_potong: 0 -> 155, supaya PDF Payment Voucher menulis
--    "Adjustment USD 155.00" dan bukan "( - 0.00 )".
--    Sejak perbaikan kode 16 Okt 2026 nilai ini dihitung server, jadi PV BARU
--    tidak akan mengalaminya lagi - ini khusus memperbaiki yang terlanjur.
-- ----------------------------------------------------------------------------
UPDATE potongan
SET jml_potong = 155.00
WHERE no_kbon = 'PV-AP/REG/NAG/2026/10/02566'
  AND ROUND(COALESCE(jml_potong, 0), 2) = 0.00
  AND ROUND(COALESCE(moq, 0), 2) = 155.00;
-- harap: 1 row affected

COMMIT;

-- ============================================================================
-- PEMERIKSAAN SESUDAH COMMIT
-- ============================================================================

-- (a) jurnalnya harus SEIMBANG di tiap mata uang
SELECT 'a. seimbang per mata uang' AS cek, curr,
       ROUND(SUM(debit), 2)      AS debit,
       ROUND(SUM(credit), 2)     AS credit,
       ROUND(SUM(debit_idr), 2)  AS debit_idr,
       ROUND(SUM(credit_idr), 2) AS credit_idr
FROM tbl_list_journal
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02566'
GROUP BY curr;
-- harus: USD debit = credit (6.160 = 6.160); total debit_idr = credit_idr

-- (b) persis seperti kolom laporan AP
SELECT 'b. laporan AP' AS cek,
       ROUND(SUM(CASE WHEN nama_coa LIKE '%GR/IR%'     THEN debit - credit ELSE 0 END), 2) AS addition,
       ROUND(SUM(CASE WHEN nama_coa LIKE '%UANG MUKA%' THEN debit - credit ELSE 0 END), 2) AS deduction_advance,
       ROUND(SUM(CASE WHEN nama_coa LIKE '%BEBAN%'     THEN debit - credit ELSE 0 END), 2) AS deduction_others
FROM tbl_list_journal
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02566';
-- harus: addition 2.925,00 / advance -3.080,00 / others 155,00
--        -> Ending Balance = 2925 - 3080 + 155 = 0

-- (c) utangnya benar-benar nol
SELECT 'c. sisa utang' AS cek,
       ROUND(SUM(debit - credit), 2) AS sisa_utang_usd
FROM tbl_list_journal
WHERE no_journal = 'PV-AP/REG/NAG/2026/10/02566' AND no_coa = '2.11.99';
-- harus: 0,00

-- (d) CBD-nya terpakai penuh
SELECT 'd. cbd' AS cek, f.no_ftr, f.total_ftr AS dipotong, c.total AS nilai_cbd,
       ROUND(c.total - f.total_ftr, 2) AS sisa
FROM kontrabon_ftr f
LEFT JOIN ftr_cbd c ON c.no_ftr_cbd = f.no_ftr
WHERE f.no_kbon = 'PV-AP/REG/NAG/2026/10/02566';
-- harus: dipotong 3.080, sisa 0
