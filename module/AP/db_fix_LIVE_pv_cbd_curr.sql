-- ============================================================================
-- PERBAIKAN MATA UANG HEADER PV-AP CBD
--
-- Masalah: halaman pv_cbd.php mengisi mata uang PV dari tebakan
--     select curr from ftr_cbd where supp = '<supplier>'
-- tanpa penyaring nomor FTR dan tanpa ORDER BY, jadi yang terpakai adalah baris
-- mana saja milik supplier itu. Untuk supplier yang pernah bertransaksi dgn
-- lebih dari satu mata uang, nilainya salah.
--
-- Baris DETAIL (kontrabon_cbd.curr) selalu benar karena diisi per FTR, jadi
-- detail dipakai sbg acuan untuk membetulkan header.
--
-- Kodenya sudah diperbaiki (mata uang diambil dari FTR yang dicentang), jadi
-- dokumen baru tidak akan kena lagi. Skrip ini hanya membetulkan yang lama.
--
-- Terdampak per 02 Okt 2026: 4 dokumen dari 688 PV-AP CBD non-Cancel.
-- PV-AP DP: tidak ada yang terdampak.
-- ============================================================================

-- ---------------------------------------------------------------- SEBELUM ---
SELECT h.no_kbon, h.tgl_kbon, h.nama_supp, h.total, h.status,
       h.curr AS curr_header,
       GROUP_CONCAT(DISTINCT d.curr) AS curr_detail
FROM   kontrabon_h_cbd h
JOIN   kontrabon_cbd d ON d.no_kbon = h.no_kbon AND d.status <> 'Cancel'
WHERE  h.status <> 'Cancel'
GROUP  BY h.no_kbon, h.tgl_kbon, h.nama_supp, h.total, h.status, h.curr
HAVING curr_detail <> h.curr
ORDER  BY h.tgl_kbon DESC;

-- ------------------------------------------------------------- PERBAIKAN ---
-- Hanya dokumen yang detailnya SATU mata uang yang disentuh. Kalau suatu saat
-- ada dokumen dgn detail campur, dia sengaja dilewati - harus diperiksa orang,
-- bukan ditebak skrip.
UPDATE kontrabon_h_cbd h
JOIN (
    SELECT d.no_kbon,
           MIN(d.curr)              AS curr_benar,
           COUNT(DISTINCT d.curr)   AS ragam
    FROM   kontrabon_cbd d
    WHERE  d.status <> 'Cancel'
    GROUP  BY d.no_kbon
) x ON x.no_kbon = h.no_kbon
SET    h.curr = x.curr_benar
WHERE  h.status <> 'Cancel'
  AND  x.ragam = 1
  AND  h.curr <> x.curr_benar;

-- --------------------------------------------------------------- SESUDAH ---
-- Harus mengembalikan 0 baris.
SELECT h.no_kbon, h.curr AS curr_header, GROUP_CONCAT(DISTINCT d.curr) AS curr_detail
FROM   kontrabon_h_cbd h
JOIN   kontrabon_cbd d ON d.no_kbon = h.no_kbon AND d.status <> 'Cancel'
WHERE  h.status <> 'Cancel'
GROUP  BY h.no_kbon, h.curr
HAVING curr_detail <> h.curr;

-- ============================================================================
-- SISA YANG TIDAK DIKERJAKAN SKRIP INI - PERLU TINDAKAN ORANG
--
-- PV-AP/CBD/NAG/2026/09/00169 sudah ditarik ke bank out
-- BK/BCA1979/NAG/1026/00071 (status Draft, dibuat gabby 02 Okt 2026):
--
--   b_bankout_det : curr USD, rates 17863.00, total 49527.00
--   b_bankout_h   : IDR 133.886.835,00   (= 49.527 x +-2.703, kurs RMB)
--   tbl_list_journal (Draft): 1.50.99 debit 49.527,00 USD @17.863
--                             = Rp 884.700.801,00
--
-- Jadi di dalam satu dokumen bank out, detailnya memakai kurs USD sedangkan
-- headernya memakai rupiah hasil kurs RMB - selisihnya +-Rp 750 juta. Kalau
-- bank out ini di-approve apa adanya, jurnalnya masuk dgn nilai itu.
--
-- Nilai-nilai ini TIDAK ditambal lewat SQL: kursnya harus dipilih lagi oleh
-- yang berwenang, bukan diisi angka karangan. Langkahnya:
--   1. jalankan UPDATE di atas (header PV jadi RMB),
--   2. Cancel / hapus bank out Draft BK/BCA1979/NAG/1026/00071,
--   3. buat ulang bank out-nya dari PV yang sudah benar, supaya kurs & jurnal
--      terbentuk dari alur biasa.
--
-- Tiga dokumen lainnya (SI/CBD/2026/01/00008, SI/CBD/2025/12/00255,
-- SI/CBD/2025/09/00149) belum punya bank out dan belum punya jurnal, jadi
-- UPDATE di atas sudah menyelesaikannya.
-- ============================================================================
