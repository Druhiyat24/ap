-- ===========================================================================
-- Koreksi PV-AP/REG/NAK/2026/09/02395 (Gunajaya Santosa,PT - NAK)
--
-- Temuan: subtotal / PPN / PPh di header SUDAH COCOK dengan 47 baris BPB-nya,
-- tetapi JURNALNYA tidak balance sebesar 1.707.770,25 (Dr 646.250.741,90 vs
-- Cr 647.958.512,15). Tiga komponen menyimpang dari pola PV sejenis:
--
--   komponen                    tersimpan        seharusnya         selisih
--   PPN masukan 1.52.04 (Dr)  58.209.752,48   58.274.332,27      -64.579,79
--   Utang 2.13.15 (Cr)       587.389.320,48  588.040.989,41     -651.668,93
--   Beban selisih 5.97.02     2.294.859,38 Cr         0,02 Dr  2.294.859,40
--
-- Pola "seharusnya" diambil dari 5 PV sejenis (supplier & COA sama, sama-sama
-- ber-PPh: 01959, 01960, 01961, 01962, 01963) - kelimanya balance dan memakai
-- konvensi: Utang dikredit sebesar (total + PPh) karena PPh baru dipotong saat
-- pembayaran, PPN masukan = PPN header dipecah per nomor faktur mengikuti PPN
-- unbilled-nya, dan Beban Selisih Harga hanya menampung residu pembulatan.
-- Tidak adanya baris jurnal PPh memang NORMAL di kelima PV itu.
--
-- GR/IR (2.12.15) dan PPN unbilled (1.52.07) TIDAK DISENTUH - keduanya sudah
-- cocok dengan baris per-BPB (hanya beda 0,02 karena pembulatan).
--
-- Semua UPDATE memakai id + nilai lama sebagai pengaman, jadi aman dijalankan
-- ulang: baris yang sudah benar berubah 0 baris.
--
-- STATUS: SUDAH DIJALANKAN DI PRODUKSI (28 Sep 2026) - diverifikasi ulang ke
-- server: jurnal 02395 kini balance (1.52.04 = 58.274.332,2733; Utang =
-- 588.040.989,41; 5.97.02 = 0,0115 Dr) dan Sigma kontrabon.total = 577.445.656,24.
-- Aman kalau terlanjur dijalankan lagi: semua UPDATE dijaga nilai lama, jadi
-- mengubah 0 baris. Dokumen ini SEKARANG berstatus Updated (digantikan
-- PV-AP/REG/NAK/2026/09/02395-REV_01) dan jurnalnya sudah berpasangan dgn
-- jurnal balik - JANGAN diedit manual lagi.
--
-- Dijalankan di: [x] lokal   [x] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

-- SEBELUM
SELECT no_coa, ROUND(SUM(debit),2) debit, ROUND(SUM(credit),2) credit
FROM tbl_list_journal WHERE no_journal = 'PV-AP/REG/NAK/2026/09/02395'
GROUP BY no_coa WITH ROLLUP;

-- 1) PPN Masukan (1.52.04): tiap faktur disamakan dgn PPN unbilled-nya,
--    residu pembulatan ditempel ke faktur TERBESAR (konvensi pv_ppn_group_faktur.php).
UPDATE tbl_list_journal SET debit = 28996756.85, debit_idr = 28996756.85
 WHERE id = 1075832 AND no_journal = 'PV-AP/REG/NAK/2026/09/02395' AND no_coa = '1.52.04' AND debit = 28744322.3402;   -- faktur 04002600300467257
-- faktur 04002600300467294: sudah benar (17,072,821.53), dilewati.
-- faktur 04002600300513756: sudah benar (5,513,169.82), dilewati.
UPDATE tbl_list_journal SET debit = 4672673.39, debit_idr = 4672673.39
 WHERE id = 1075835 AND no_journal = 'PV-AP/REG/NAK/2026/09/02395' AND no_coa = '1.52.04' AND debit = 4860528.1078;   -- faktur 04002600300513727
-- faktur 04002600300513755: sudah benar (2,018,910.68), dilewati.

-- 2) Utang Usaha (2.13.15) = total + PPh (PPh baru dipotong saat pembayaran).
UPDATE tbl_list_journal SET credit = 588040989.41, credit_idr = 588040989.41
 WHERE id = 1075736 AND no_journal = 'PV-AP/REG/NAK/2026/09/02395' AND no_coa = '2.13.15' AND credit = 587389320.4813;

-- 3) Beban Selisih Harga (5.97.02) = penyeimbang, tinggal residu pembulatan.
--    Dr lain 646,315,321.68 vs Cr lain 646,315,321.70 -> beban DEBIT 0.0115 (dihitung dari nilai PRESISI PENUH 4 desimal,
--    bukan dari angka yang sudah dibulatkan 2 desimal - kalau pakai 0,02 jurnalnya
--    malah meleset 0,01)
UPDATE tbl_list_journal SET debit = 0.0115, debit_idr = 0.0115, credit = 0.0000, credit_idr = 0.0000
 WHERE id = 1075735 AND no_journal = 'PV-AP/REG/NAK/2026/09/02395' AND no_coa = '5.97.02' AND credit = 2294859.3800;

-- 4) Baris detail yang total-nya tidak dikurangi PPh.
UPDATE kontrabon SET total = 1124983.01
 WHERE id = 44584 AND no_kbon = 'PV-AP/REG/NAK/2026/09/02395' AND no_bpb = 'GM/IN/2608/00003' AND total = 1145624.90;   -- 1,032,094.50 + 113,530.40 - 20,641.89
-- SESUDAH - selisih WAJIB 0.00
SELECT ROUND(SUM(debit),2) debit, ROUND(SUM(credit),2) credit,
       ROUND(SUM(debit) - SUM(credit),2) selisih
FROM tbl_list_journal WHERE no_journal = 'PV-AP/REG/NAK/2026/09/02395';

-- Cek baris detail: total = subtotal + tax - pph (toleransi pembulatan 0,01)
SELECT COUNT(*) baris_masih_meleset FROM kontrabon
WHERE no_kbon = 'PV-AP/REG/NAK/2026/09/02395'
  AND ABS(total - ROUND(subtotal + tax - pph_value, 2)) > 0.011;
