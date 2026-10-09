-- ============================================================================
-- PV-AP/REG/NAK/2026/09/02398-REV_01
-- Koreksi baris GM/IN/2608/00026 agar cocok dgn BPB-nya, TANPA mengubah
-- PPN, PPh, maupun Grand Total yang sudah di-FIRST APPROVED.
--
-- VERSI 2 (9 Okt 2026). Versi pertama GAGAL DIPAKAI: di sana ada
-- START TRANSACTION tapi COMMIT-nya saya biarkan terkomentar, jadi begitu
-- sesi ditutup semua perubahan ter-rollback dan data tidak bergerak sama
-- sekali. Berkas ini SUDAH BISA DIJALANKAN APA ADANYA - COMMIT aktif.
--
-- AMAN DIULANG: tiap UPDATE dijaga nilai lamanya di WHERE. Kalau sudah
-- pernah jalan, perintahnya mengenai 0 baris dan tidak merusak apa pun.
-- Kalau datanya ternyata tidak seperti yang diharapkan, perintahnya juga
-- tidak jalan - jadi tidak bisa salah sasaran.
--
-- DUDUK PERKARANYA
-- ----------------
-- BPB GM/IN/2608/00026 : qty 958,25 x harga 33.255,60 = 31.867.178,70
--   Jurnal BPB-nya (id 1049163-1049165) memakai angka itu:
--     Cr 2.12.15 GR/IR  35.372.568,36  = 31.867.178,70 + PPN 3.505.389,66
--   BPB ini TIDAK PERNAH dikoreksi harga (tidak ada pengajuan Update BPB,
--   tidak ada jurnal "(Rev n)").
--
-- Baris PV-nya hanya mencatat 31.794.016,38 - lebih rendah 73.162,32.
-- PENYEBAB (dikonfirmasi pemakai): QTY BERUBAH. Yang aktual sekarang 958,25
-- tapi yang terlanjur masuk kontrabon lebih kecil.
--   CATATAN ANGKA: nilai 31.794.016,38 setara qty 956,05 - jadi selisihnya
--   2,20 pcs, BUKAN 2 pcs. Kalau benar-benar 956,25, nilainya seharusnya
--   31.800.667,50 dan selisihnya 66.511,20. Patokan yang dipakai di sini
--   adalah qty AKTUAL 958,25, karena itu yang dipakai BPB dan jurnalnya,
--   dan hanya itu yang membuat GR/IR lunas tepat nol.
--
-- AKIBAT YANG BELUM KELIHATAN: GR/IR 2.12.15 utk BPB itu tidak pernah nol -
--   dikredit saat BPB  35.372.568,36
--   didebit saat PV    35.291.358,18
--   tersisa                81.210,18
-- Dari 25 BPB di PV ini, HANYA yang ini yang bersisa.
--
-- Potongan yang sudah ada (-119.777,67 / -13.175,54 / -2.395,55) BUKAN untuk
-- BPB ini: selisih PPN-nya melekat di faktur 04002600317454266 (15 BPB).
--
-- CARA MEMBETULKAN
-- ----------------
-- Baris dinaikkan ke nilai BPB, lalu kenaikannya diserap potongan supaya
-- header tidak bergerak. Header memakai rumus (dibuktikan dari datanya):
--     header.tax   = SUM(baris.tax)       + potongan_ppn
--     header.pph   = SUM(baris.pph_value) + potongan_pph
--     header.total = header.subtotal + header.tax - header.pph + jml_potong
--
-- YANG TIDAK DISENTUH, DAN ALASANNYA
-- ----------------------------------
--   kontrabon_h.tax / pph_idr / total / balance : memang harus tetap.
--   jurnal 2.13.15 Utang Usaha 305.806.222,12   : utangnya tidak berubah.
--   jurnal 1.52.04 PPN billed   30.305.121,11   : PPN header tidak berubah.
--   jurnal PV ASLI 02398                        : sudah dibalik penuh
--       (tiap "AP - Kontrabon" punya pasangan "Reverse"), netto NOL.
--   status FIRST APPROVED & first_approve_user  : tidak ada di perintah ini.
--   kartu_hutang (26 baris)                     : LIHAT CATATAN DI BAWAH.
-- ============================================================================

START TRANSACTION;

-- 1. baris PV disamakan dgn BPB-nya
UPDATE kontrabon
   SET subtotal  = 31867178.70,   -- dari 31.794.016,38  (+73.162,32)
       tax       =  3505389.66,   -- dari  3.497.341,80  (+ 8.047,86)  PPN 11%
       pph_value =   637343.57,   -- dari    635.880,33  (+ 1.463,24)  PPh 2%
       total     = 34735224.79    -- dari 34.655.477,85
 WHERE no_kbon  = 'PV-AP/REG/NAK/2026/09/02398-REV_01'
   AND no_bpb   = 'GM/IN/2608/00026'
   AND subtotal = 31794016.38;    -- penjaga: hanya kalau masih nilai lama

-- 2. potongan menyerap kenaikan itu, supaya PPN/PPh/Grand Total header TETAP
UPDATE potongan
   SET s_harga      =  -192939.99,  -- dari -119.777,67  (-73.162,32)
       jml_potong   =  -192939.99,
       potongan_ppn =   -21223.40,  -- dari  -13.175,54  (- 8.047,86)
       potongan_pph =    -3858.80   -- dari   -2.395,55  (- 1.463,25)
 WHERE no_kbon    = 'PV-AP/REG/NAK/2026/09/02398-REV_01'
   AND jml_potong = -119777.67;     -- penjaga

-- 3. header: HANYA subtotal yang berubah (memang itu yang dibetulkan).
UPDATE kontrabon_h
   SET subtotal = 275694041.00      -- dari 275.620.878,68 (+73.162,32)
 WHERE no_kbon  = 'PV-AP/REG/NAK/2026/09/02398-REV_01'
   AND subtotal = 275620878.68;     -- penjaga

-- 4. jurnal PV. Tiga baris yang bergerak, dikunci dgn id-nya sekaligus
--    nilai lamanya. rate = 1,0000 jadi kolom _idr nilainya sama.

--    4a. GR/IR naik supaya BPB-nya lunas tepat nol
UPDATE tbl_list_journal
   SET debit = 35372568.36, debit_idr = 35372568.36
 WHERE id = 1077248
   AND no_journal = 'PV-AP/REG/NAK/2026/09/02398-REV_01'
   AND no_coa = '2.12.15' AND reff_doc = 'GM/IN/2608/00026'
   AND debit  = 35291358.18;        -- penjaga

--    4b. PPN masukan unbilled ikut naik
UPDATE tbl_list_journal
   SET credit = 3505389.66, credit_idr = 3505389.66
 WHERE id = 1077249
   AND no_journal = 'PV-AP/REG/NAK/2026/09/02398-REV_01'
   AND no_coa = '1.52.07' AND reff_doc = 'GM/IN/2608/00026'
   AND credit = 3497341.80;         -- penjaga

--    4c. Beban Selisih Harga menyerap selisihnya
UPDATE tbl_list_journal
   SET credit = 192939.99, credit_idr = 192939.99
 WHERE id = 1077215
   AND no_journal = 'PV-AP/REG/NAK/2026/09/02398-REV_01'
   AND no_coa = '5.97.02'
   AND credit = 119777.67;          -- penjaga

COMMIT;

-- ============================================================================
-- PEMERIKSAAN - jalankan sesudahnya. Keempatnya harus "OK".
-- ============================================================================
SELECT 'header' bagian,
       FORMAT(subtotal,2) subtotal, FORMAT(tax,2) ppn,
       FORMAT(pph_idr,2) pph, FORMAT(total,2) grand_total,
       IF(ROUND(subtotal,2)=275694041.00 AND ROUND(tax,2)=30305121.11
          AND ROUND(pph_idr,2)=5510022.02 AND ROUND(total,2)=300296200.10,'OK','PERIKSA') hasil
  FROM kontrabon_h WHERE no_kbon = 'PV-AP/REG/NAK/2026/09/02398-REV_01';

SELECT 'jurnal balance' bagian,
       FORMAT(SUM(debit),2) debit, FORMAT(SUM(credit),2) credit,
       IF(ROUND(SUM(debit),2)=ROUND(SUM(credit),2),'OK','TIDAK BALANCE') hasil
  FROM tbl_list_journal WHERE no_journal = 'PV-AP/REG/NAK/2026/09/02398-REV_01';

SELECT 'sisa GR/IR 00026' bagian,
       FORMAT((SELECT SUM(credit)-SUM(debit) FROM tbl_list_journal
                WHERE no_journal='GM/IN/2608/00026' AND no_coa='2.12.15'
                  AND status IN ('Approved','POST'))
            - (SELECT subtotal+tax FROM kontrabon
                WHERE no_kbon='PV-AP/REG/NAK/2026/09/02398-REV_01'
                  AND no_bpb='GM/IN/2608/00026'), 2) sisa,
       IF(ABS((SELECT SUM(credit)-SUM(debit) FROM tbl_list_journal
                WHERE no_journal='GM/IN/2608/00026' AND no_coa='2.12.15'
                  AND status IN ('Approved','POST'))
            - (SELECT subtotal+tax FROM kontrabon
                WHERE no_kbon='PV-AP/REG/NAK/2026/09/02398-REV_01'
                  AND no_bpb='GM/IN/2608/00026')) < 0.01, 'OK','MASIH BERSISA') hasil;

SELECT 'baris 00026' bagian, FORMAT(subtotal,2) subtotal, FORMAT(tax,2) ppn,
       FORMAT(pph_value,2) pph, FORMAT(total,2) total,
       IF(ROUND(subtotal,2)=31867178.70,'OK','PERIKSA') hasil
  FROM kontrabon WHERE no_kbon='PV-AP/REG/NAK/2026/09/02398-REV_01'
                   AND no_bpb='GM/IN/2608/00026';

-- ============================================================================
-- CATATAN: kartu_hutang TIDAK diubah - SENGAJA.
-- 26 barisnya untuk PV ini berisi angka yang tidak nyambung ke mana pun:
--   curr = 0 dan rate = 17.824 padahal PV ini IDR;
--   baris BPB 00026 credit_idr = 36.111.424,00 (bukan 35.291.358,18);
--   beberapa BPB berbeda punya credit_idr yang sama persis 36.303.894,00;
--   jumlah seluruh baris 940.747.077,00 untuk PV senilai 300.296.200,10.
-- Jadi tabel itu memang sudah kacau sejak awal, bukan akibat koreksi ini, dan
-- tidak dipakai oleh cetakan PV. Mengubahnya hanya akan menukar satu angka
-- ngawur dgn angka ngawur lain. Perlu diperiksa terpisah.
-- ============================================================================
