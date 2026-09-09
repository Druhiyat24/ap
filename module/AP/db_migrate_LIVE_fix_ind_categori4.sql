-- ============================================================================
-- FIX: mastercoa_v2.ind_categori4 / eng_categori4 yang USANG
-- Tanggal   : 2026-09-04
-- Dampak    : SPL & SFP (FS1 + FS2, YTD + Monthly) dan ekspor Excel-nya
-- ============================================================================
--
-- MASALAH
--   mastercoa_v2.ind_categori4 / eng_categori4 adalah SALINAN nama kategori
--   dari master_coa_ctg4 (ind_name / eng_name). Salinan itu melenceng untuk
--   11 COA -- masih memakai nama kategori yang lama.
--
--   Laporan keuangan menjodohkan baris fs_kategori_laporan.sub_kategori ke
--   saldo COA lewat TEKS ind_categori4, SESUDAH data di-GROUP BY id_ctg4.
--   Karena satu id_ctg4 jadi punya 2 nama berbeda, label yang terpakai adalah
--   salah satu nilai acak dari grup -- dan yang kepilih justru yang usang,
--   sehingga SELURUH grup mendarat di baris laporan yang salah.
--
--   Akibat di SPL FS2 YTD (Juli 2026): 6 kategori tampil 0,00 dan nilainya
--   menempel ke kategori lain. Total LABA/(RUGI) SEBELUM PAJAK tetap benar
--   (tidak ada angka yang hilang), tapi RINCIANNYA salah baris:
--     Beban bunga      = bunga + selisih kurs + beban adm bank
--     Penjualan Aset T = penjualan AT + pendapatan lain-lain + pendapatan bunga
--
--   Yang usang (master_coa_ctg4 = acuan yang BENAR):
--     5.31.01, 5.31.02  ctg4=513  "JASA JAHIT EKSPOR"        -> "JASA JAHIT PAKAIAN JADI EKSPOR"
--     5.32.01, 5.32.02  ctg4=514  "JASA JAHIT LOKAL"         -> "JASA JAHIT PAKAIAN JADI LOKAL"
--     8.02.01           ctg4=812  "PENJUALAN ASET TETAP"     -> "DISPOSISI ASET TETAP"
--     8.03.01, 8.04.01  ctg4=813  "PENJUALAN ASET TETAP"     -> "PENDAPATAN LAIN-LAIN"
--     8.05.01           ctg4=814  "PENJUALAN ASET TETAP"     -> "PENDAPATAN BUNGA"
--     8.06.01           ctg4=815  "PENJUALAN ASET TETAP"     -> "PENDAPATAN SEWA"
--     8.51.01           ctg4=822  "BEBAN BUNGA"              -> "BEBAN ADMINISTRASI BANK"
--     8.52.01           ctg4=823  "BEBAN BUNGA"              -> "LABA / (RUGI) SELISIH KURS"
--
-- CATATAN
--   Hanya memperbaiki DATA MASTER, tidak menyentuh satu pun query laporan.
--   Tidak ada tabel transaksi/jurnal yang diubah -- angka tidak bergeser,
--   hanya pengelompokan barisnya yang jadi benar.
--   Jalankan BERURUTAN dari atas ke bawah.
-- ============================================================================


-- ---------------------------------------------------------------------------
-- LANGKAH 1 - Lihat dulu apa yang akan berubah (tidak mengubah apa pun).
--             Harus keluar TEPAT 11 baris.
-- ---------------------------------------------------------------------------
SELECT  g.id_ctg2,
        m.no_coa,
        m.nama_coa,
        m.id_ctg4,
        m.ind_categori4 AS ind_sekarang,
        g.ind_name      AS ind_seharusnya,
        m.eng_categori4 AS eng_sekarang,
        g.eng_name      AS eng_seharusnya
FROM       mastercoa_v2   m
INNER JOIN master_coa_ctg4 g ON g.id_ctg4 = m.id_ctg4
WHERE  m.ind_categori4 <> g.ind_name
    OR m.eng_categori4 <> g.eng_name
ORDER BY m.no_coa;


-- ---------------------------------------------------------------------------
-- LANGKAH 2 - Cadangkan baris yang akan diubah (supaya bisa dikembalikan).
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS bak_mastercoa_v2_ctg4_20260904;

CREATE TABLE bak_mastercoa_v2_ctg4_20260904 AS
SELECT  m.no_coa,
        m.id_ctg4,
        m.ind_categori4,
        m.eng_categori4,
        NOW() AS backup_at
FROM       mastercoa_v2   m
INNER JOIN master_coa_ctg4 g ON g.id_ctg4 = m.id_ctg4
WHERE  m.ind_categori4 <> g.ind_name
    OR m.eng_categori4 <> g.eng_name;

-- Pastikan isinya 11 baris sebelum lanjut.
SELECT COUNT(*) AS jml_dicadangkan FROM bak_mastercoa_v2_ctg4_20260904;


-- ---------------------------------------------------------------------------
-- LANGKAH 3 - Perbaiki: samakan salinan nama kategori dengan master_coa_ctg4.
--             Harus melaporkan 11 rows affected.
-- ---------------------------------------------------------------------------
UPDATE     mastercoa_v2   m
INNER JOIN master_coa_ctg4 g ON g.id_ctg4 = m.id_ctg4
SET m.ind_categori4 = g.ind_name,
    m.eng_categori4 = g.eng_name
WHERE  m.ind_categori4 <> g.ind_name
    OR m.eng_categori4 <> g.eng_name;


-- ---------------------------------------------------------------------------
-- LANGKAH 4 - Verifikasi. Kedua query di bawah harus mengembalikan 0.
-- ---------------------------------------------------------------------------

-- 4a. Tidak boleh ada lagi salinan nama yang melenceng.
SELECT COUNT(*) AS sisa_melenceng
FROM       mastercoa_v2   m
INNER JOIN master_coa_ctg4 g ON g.id_ctg4 = m.id_ctg4
WHERE  m.ind_categori4 <> g.ind_name
    OR m.eng_categori4 <> g.eng_name;

-- 4b. Tidak boleh ada lagi satu id_ctg4 yang punya >1 nama berbeda
--     (inilah yang bikin kategori saling menempel di laporan).
SELECT id_ctg4, COUNT(DISTINCT ind_categori4) AS jml_nama
FROM   mastercoa_v2
WHERE  id_ctg4 IS NOT NULL
GROUP BY id_ctg4
HAVING jml_nama > 1;


-- ============================================================================
-- CARA MENGEMBALIKAN (kalau perlu dibatalkan)
-- ============================================================================
-- UPDATE     mastercoa_v2 m
-- INNER JOIN bak_mastercoa_v2_ctg4_20260904 b ON b.no_coa = m.no_coa
-- SET m.ind_categori4 = b.ind_categori4,
--     m.eng_categori4 = b.eng_categori4;
--
-- Kalau sudah yakin tidak perlu dikembalikan, cadangannya boleh dihapus:
-- DROP TABLE bak_mastercoa_v2_ctg4_20260904;
