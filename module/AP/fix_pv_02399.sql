-- ===========================================================================
-- PV-AP/REG/NAK/2026/09/02399 : baris GM/IN/2608/00060 tersimpan 4x lipat
--
-- Temuan: dari 25 baris BPB di PV ini, 24 baris COCOK PERSIS dengan sumbernya
-- di bpb_new. Hanya GM/IN/2608/00060 yang nilainya TEPAT 4x:
--
--     subtotal  118.689.236,40  seharusnya  29.672.309,10   (bpb_new: 1 baris,
--     tax        13.055.816,00  seharusnya   3.263.954,00    qty 892,25 x
--     pph         2.373.784,73  seharusnya     593.446,18    harga 33.255,60)
--     total     129.371.267,68  seharusnya  32.342.816,92
--
-- Karena header & jurnalnya dibangun dari angka itu, keduanya ikut dikoreksi
-- supaya dokumen tetap konsisten:
--   header : subtotal/tax/pph_idr/total/balance
--   jurnal : GR/IR per-BPB, PPN unbilled per-BPB, PPN billed grup faktur
--            04002600336143618, dan Utang Usaha
--
-- PPh dihitung 2% dari subtotal (sama dgn pph_code baris itu); hasilnya
-- 6.063.520,42 = tepat 2% dari subtotal header yang baru - cocok.
-- Dokumen ini TIDAK punya potongan maupun uang muka, jadi tidak ada komponen
-- lain yang perlu disesuaikan.
--
-- Semua UPDATE dijaga id + nilai lama, jadi aman dijalankan ulang (0 baris).
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

-- SEBELUM
SELECT 'jurnal' src, no_coa, ROUND(SUM(debit),2) debit, ROUND(SUM(credit),2) credit
FROM tbl_list_journal WHERE no_journal='PV-AP/REG/NAK/2026/09/02399' GROUP BY no_coa WITH ROLLUP;

-- 1) Baris detail
UPDATE kontrabon
SET subtotal = 29672309.10, tax = 3263954.00, pph_value = 593446.18, total = 32342816.92
WHERE id = 44704 AND no_kbon = 'PV-AP/REG/NAK/2026/09/02399' AND no_bpb = 'GM/IN/2608/00060'
  AND subtotal = 118689236.40;

-- 2) Header
UPDATE kontrabon_h
SET subtotal = 303176020.99, tax = 33349362.31, pph_idr = 6063520.42,
    total = 330461862.88, balance = 330461862.88
WHERE no_kbon = 'PV-AP/REG/NAK/2026/09/02399' AND subtotal = 392192948.29;

-- 3) Jurnal GR/IR baris BPB tsb (kolom double(16,4) - nilai lama 4 desimal)
UPDATE tbl_list_journal SET debit = 32936263.1010, debit_idr = 32936263.10
 WHERE id = 1077337 AND no_journal = 'PV-AP/REG/NAK/2026/09/02399'
   AND reff_doc = 'GM/IN/2608/00060' AND no_coa = '2.12.15' AND debit = 131745052.4040;

-- 4) Jurnal PPN unbilled baris BPB tsb
UPDATE tbl_list_journal SET credit = 3263954.0010, credit_idr = 3263954.00
 WHERE id = 1077338 AND no_journal = 'PV-AP/REG/NAK/2026/09/02399'
   AND reff_doc = 'GM/IN/2608/00060' AND no_coa = '1.52.07' AND credit = 13055816.0040;

-- 5) Jurnal PPN billed - grup faktur yang memuat BPB tsb
UPDATE tbl_list_journal SET debit = 18267149.1042, debit_idr = 18267149.10
 WHERE id = 1077357 AND no_journal = 'PV-AP/REG/NAK/2026/09/02399'
   AND no_coa = '1.52.04' AND faktur_pajak = '04002600336143618' AND debit = 28059011.1072;

-- 6) Jurnal Utang Usaha (= total + PPh, PPh dipotong saat pembayaran).
--    Nilainya disetel = GR/IR + PPN billed - PPN unbilled pada PRESISI PENUH
--    (4 desimal) supaya jurnalnya balance TEPAT 0. Nilai lama 435.334.172,6015
--    sudah menyimpan residu -0,0034 sejak dokumen dibuat; kalau residu itu
--    dipertahankan, total 2-desimalnya jadi terlihat beda 0,01.
UPDATE tbl_list_journal SET credit = 336525383.2951, credit_idr = 336525383.30
 WHERE id = 1077305 AND no_journal = 'PV-AP/REG/NAK/2026/09/02399'
   AND no_coa = '2.13.15' AND credit = 435334172.6015;

-- SESUDAH - jurnal harus tetap balance (selisih 0.00)
SELECT no_coa, ROUND(SUM(debit),2) debit, ROUND(SUM(credit),2) credit
FROM tbl_list_journal WHERE no_journal='PV-AP/REG/NAK/2026/09/02399' GROUP BY no_coa WITH ROLLUP;

-- Header vs Sigma detail (subtotal/tax/pph wajib sama; total boleh beda <= 0,05 krn pembulatan)
SELECT h.subtotal h_sub, d.sub d_sub, h.tax h_tax, d.tax d_tax,
       h.pph_idr h_pph, d.pph d_pph, h.total h_total, d.total d_total
FROM (SELECT subtotal, tax, pph_idr, total FROM kontrabon_h WHERE no_kbon='PV-AP/REG/NAK/2026/09/02399') h,
     (SELECT ROUND(SUM(subtotal),2) sub, ROUND(SUM(tax),2) tax, ROUND(SUM(pph_value),2) pph,
             ROUND(SUM(total),2) total FROM kontrabon WHERE no_kbon='PV-AP/REG/NAK/2026/09/02399') d;

-- Tiap baris wajib cocok dgn bpb_new (harus 0)
SELECT COUNT(*) baris_tidak_cocok FROM kontrabon k
JOIN (SELECT no_bpb, ROUND(SUM(qty*price),2) sub FROM bpb_new WHERE status<>'Cancel' GROUP BY no_bpb) b
  ON b.no_bpb = k.no_bpb
WHERE k.no_kbon='PV-AP/REG/NAK/2026/09/02399' AND ABS(k.subtotal - b.sub) > 0.01;
