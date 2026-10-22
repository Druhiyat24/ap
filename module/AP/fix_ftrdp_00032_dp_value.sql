-- ============================================================================
-- fix_ftrdp_00032_dp_value.sql
--
-- FTR/D/NAG/1026/00032 tersimpan dgn dp_value = 0 padahal DP-nya 50% dari
-- PO 143.735.000 (seharusnya 71.867.500), dan balance-nya masih penuh.
-- Akibatnya PV turunannya, PV-AP/DP/NAG/2026/10/00011, ber-Total 0 di daftar
-- Payment Voucher.
--
-- Sebabnya cacat di form FTR DP: saat pemakai mengisi kolom "DP Amount"
-- (bukan "DP %"), nilainya ditulis ke td:eq(67) - sel yang tidak ada -
-- sehingga atribut data-value yang justru DIBACA saat submit tidak pernah
-- terisi dan terkirim sebagai 0. Sudah diperbaiki di formftrdp.php &
-- edit_ftrdp.php, plus penjaga di insertftrdp.php & update_ftrdp.php yang
-- MENOLAK menyimpan DP Amount 0 selama DP %-nya terisi.
--
-- Nilai benar: 143.735.000 x 50% = 71.867.500; sisa 143.735.000 - 71.867.500
--              = 71.867.500.
-- Bentuk PV DP yang benar diambil dari 6 dokumen pembanding di produksi
-- (PV-AP/DP/NAG/2026/10/00008 s/d 00010 dll): pada header dp_value = total =
-- balance = nilai DP, pada detail dp_value = total = nilai DP.
--
-- AMAN DIULANG: tiap UPDATE dijaga nilai LAMA-nya, jadi sekali dijalankan
-- putaran berikutnya tidak mengubah baris mana pun.
--
-- PRASYARAT: PV-AP/DP/NAG/2026/10/00011 masih 'draft', belum berjurnal, dan
-- belum dibayar lewat Bank Out (diperiksa 22 Okt 2026). Kalau sudah berubah,
-- JANGAN dijalankan - periksa ulang dulu.
--
-- BELUM DIJALANKAN DI PRODUKSI.
-- ============================================================================

START TRANSACTION;

-- 1) dokumen FTR DP-nya sendiri
UPDATE ftr_dp
   SET dp_value = 71867500.00,
       balance  = 71867500.00
 WHERE no_ftr_dp = 'FTR/D/NAG/1026/00032'
   AND total     = 143735000.00
   AND dp        = 50.00
   AND dp_value  = 0.00;

-- 2) header PV DP turunannya
UPDATE kontrabon_h_dp
   SET dp_value = 71867500.00,
       total    = 71867500.00,
       balance  = 71867500.00
 WHERE no_kbon  = 'PV-AP/DP/NAG/2026/10/00011'
   AND subtotal = 143735000.00
   AND dp_value = 0.00
   AND status   = 'draft';

-- 3) detail PV DP turunannya
UPDATE kontrabon_dp
   SET dp_value = 71867500.00,
       total    = 71867500.00
 WHERE no_kbon  = 'PV-AP/DP/NAG/2026/10/00011'
   AND no_dp    = 'FTR/D/NAG/1026/00032'
   AND subtotal = 143735000.00
   AND dp_value = 0.00;

COMMIT;

-- ---------------------------------------------------------------------------
-- Pemeriksaan sesudah dijalankan - ketiganya harus menunjukkan 71.867.500
-- ---------------------------------------------------------------------------
SELECT 'ftr_dp' AS tabel, no_ftr_dp AS dokumen, total, dp, dp_value, balance
  FROM ftr_dp
 WHERE no_ftr_dp = 'FTR/D/NAG/1026/00032';

SELECT 'kontrabon_h_dp' AS tabel, no_kbon AS dokumen, subtotal, dp_value, total, balance, status
  FROM kontrabon_h_dp
 WHERE no_kbon = 'PV-AP/DP/NAG/2026/10/00011';

SELECT 'kontrabon_dp' AS tabel, no_kbon AS dokumen, no_dp, subtotal, dp_value, total
  FROM kontrabon_dp
 WHERE no_kbon = 'PV-AP/DP/NAG/2026/10/00011';
