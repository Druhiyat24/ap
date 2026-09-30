-- =============================================================================
-- fix_potongan_ganda.sql
--
-- MASALAH
-- Tabel `potongan` memuat total potongan TINGKAT DOKUMEN - satu baris per
-- no_kbon. Halaman Payment Voucher (payment-voucher-ap.php) mengambilnya lewat
-- INNER JOIN, jadi begitu satu dokumen punya DUA baris, seluruh nilai dokumen
-- itu terbaca GANDA di daftar (SubTotal, Tax PPn, Total CBD/DP, dst.) padahal
-- data aslinya benar. Dilaporkan user 30 Sep 2026 pada
-- PV-AP/REG/NAG/2026/08/02088.
--
-- PENYEBAB
-- Kolom no_kbon hanya punya index BIASA (bukan UNIQUE), jadi tidak ada yang
-- mencegah baris kedua. Dua kasus 2026 (02088 & 02150) isinya IDENTIK - itu
-- tanda satu dokumen tersimpan dua kali karena permintaan simpan terkirim
-- dobel, bukan dua potongan yang berbeda.
--
-- CAKUPAN saat diperiksa (produksi, 30 Sep 2026):
--   11.920 dokumen di tabel potongan
--       13 dokumen punya lebih dari satu baris
--       23 baris berlebih, terbanyak 6 baris untuk satu dokumen
--
-- CARA PAKAI: jalankan berurutan. Langkah 1 & 2 hanya MELIHAT, tidak mengubah.
-- =============================================================================


-- -----------------------------------------------------------------------------
-- 1) LIHAT DULU: dokumen mana saja yang punya baris ganda
-- -----------------------------------------------------------------------------
SELECT  no_kbon,
        COUNT(*)      AS jml_baris,
        MIN(id)       AS id_disimpan,
        GROUP_CONCAT(id ORDER BY id)                  AS semua_id,
        MIN(tgl_kbon) AS tgl_kbon,
        MIN(nama_supp) AS nama_supp,
        MIN(status)   AS status
FROM    potongan
GROUP BY no_kbon
HAVING  COUNT(*) > 1
ORDER BY MIN(tgl_kbon) DESC;


-- -----------------------------------------------------------------------------
-- 2) LIHAT DULU: apakah baris gandanya BENAR-BENAR kembar?
--
--    Kalau isi_berbeda = 1  -> seluruh barisnya identik, aman menyisakan satu.
--    Kalau isi_berbeda > 1  -> nilainya TIDAK sama, JANGAN dihapus otomatis;
--                              periksa manual dulu mana yang benar.
--    COALESCE dipakai karena CONCAT menghasilkan NULL kalau ada satu saja
--    kolom yang NULL - tanpa itu, baris lama yang kolomnya NULL akan terbaca
--    sebagai "0 isi berbeda" dan menyesatkan.
-- -----------------------------------------------------------------------------
SELECT  no_kbon,
        COUNT(*) AS jml_baris,
        COUNT(DISTINCT CONCAT_WS('|',
              COALESCE(jml_return,0),  COALESCE(lr_kurs,0),   COALESCE(s_qty,0),
              COALESCE(s_harga,0),     COALESCE(materai,0),   COALESCE(pot_beli,0),
              COALESCE(ekspedisi,0),   COALESCE(moq,0),       COALESCE(jml_potong,0),
              COALESCE(potongan_ppn,0), COALESCE(potongan_pph,0))) AS isi_berbeda
FROM    potongan
GROUP BY no_kbon
HAVING  COUNT(*) > 1
ORDER BY isi_berbeda DESC, MIN(tgl_kbon) DESC;


-- -----------------------------------------------------------------------------
-- 3) BERSIHKAN: sisakan baris dengan id TERKECIL untuk tiap dokumen
--
--    Dijalankan HANYA untuk dokumen yang langkah 2 menyatakan isi_berbeda = 1.
--    Kalau ada yang isi_berbeda > 1, keluarkan dulu no_kbon itu dari daftar
--    (tambahkan AND p.no_kbon NOT IN ('...')) dan tangani manual.
--
--    Id terkecil dipilih karena itu baris yang PERTAMA tersimpan - yang kedua
--    adalah hasil kiriman ulang.
-- -----------------------------------------------------------------------------
-- SELECT dulu untuk memastikan baris mana yang akan hilang:
SELECT  p.*
FROM    potongan p
JOIN   (SELECT no_kbon, MIN(id) AS id_simpan
        FROM   potongan
        GROUP BY no_kbon
        HAVING COUNT(*) > 1) d
       ON d.no_kbon = p.no_kbon
WHERE   p.id <> d.id_simpan
ORDER BY p.no_kbon, p.id;

-- Kalau daftar di atas sudah benar, baru jalankan penghapusannya:
-- DELETE p
-- FROM   potongan p
-- JOIN  (SELECT no_kbon, MIN(id) AS id_simpan
--        FROM   potongan
--        GROUP BY no_kbon
--        HAVING COUNT(*) > 1) d
--       ON d.no_kbon = p.no_kbon
-- WHERE  p.id <> d.id_simpan;


-- -----------------------------------------------------------------------------
-- 4) PENGAMAN TETAP: satu dokumen = satu baris, dipaksa oleh database
--
--    Ini pembatasan yang sesungguhnya. Penjaga di PHP (lihat insertkbon_core.php
--    dkk.) hanya menutup jalur yang sudah ketahuan; UNIQUE KEY menutup SEMUA
--    jalur sekaligus, termasuk jalur lama, skrip manual, dan jalur baru yang
--    belum ada.
--
--    JALANKAN SETELAH langkah 3 selesai - selama masih ada baris ganda,
--    perintah ini akan ditolak (Duplicate entry).
--
--    Sudah diperiksa AMAN terhadap alur Edit: baik insertkbon_bulk_edit.php
--    maupun insert_kontrabon_edit_all.php menandai dokumen lama 'Updated' lalu
--    menyisipkan baris dengan NOMOR BARU (revisi -REV_NN), tidak memakai ulang
--    no_kbon yang sama. Jadi tidak ada alur sah yang butuh dua baris.
-- -----------------------------------------------------------------------------
-- ALTER TABLE potongan
--   DROP INDEX no_kbon,
--   ADD UNIQUE KEY uq_potongan_no_kbon (no_kbon);


-- -----------------------------------------------------------------------------
-- 5) PERIKSA SETELAHNYA: harus tidak ada baris sama sekali
-- -----------------------------------------------------------------------------
SELECT no_kbon, COUNT(*) AS jml_baris
FROM   potongan
GROUP BY no_kbon
HAVING COUNT(*) > 1;
