-- ============================================================================
-- MIGRASI PRODUKSI - 15 Okt 2026
-- Menu baru: Cash > Petty Cash Flow Realisation
--
-- Salinan menu "Report Cash Flow Realisation" (Bank > Report) yang daftar
-- akunnya HANYA akun KAS (Kas Besar & Kas Kecil), diletakkan di menu Cash.
-- Perhitungannya memakai berkas yang sama (cfr_functions.php), jadi angka
-- untuk akun yang sama pasti sama dgn laporan aslinya.
--
-- WAJIB dijalankan SEBELUM pull kode baru, kalau tidak menunya tidak muncul
-- untuk siapa pun (butirnya dijaga hak akses tersendiri, bukan hak akses
-- 'Cash').
--
-- AMAN DIULANG. Semua perintah memakai NOT EXISTS.
--
-- ----------------------------------------------------------------------------
-- CATATAN PENTING TENTANG NOMOR id
-- id dipatok 144 karena id terbesar yang DIKETAHUI saat ini 143
-- ('Update BPB WIP', dari db_migrate_LIVE_20261013_ubf_wip.sql yang BELUM
-- tentu sudah dijalankan di produksi). Isi produksi TIDAK bisa diperiksa dari
-- sesi pembuat berkas ini, jadi JALANKAN DULU bagian 0 di bawah untuk
-- memastikan 144 memang masih kosong. Kalau ternyata sudah terpakai, ganti
-- angka 144 di SELURUH berkas ini DAN di module/menu.php (cari "144").
-- ----------------------------------------------------------------------------

-- ----------------------------------------------------------------------------
-- 0. PERIKSA DULU (tidak mengubah apa pun)
-- ----------------------------------------------------------------------------
SELECT '0a. id terbesar di menurole' AS cek, MAX(id) AS id_terbesar FROM menurole;
-- harap: 142 atau 143. Kalau >= 144, JANGAN lanjut - ganti nomornya dulu.

SELECT '0b. apakah 144 sudah dipakai' AS cek, COUNT(*) AS baris FROM menurole WHERE id = 144;
-- harus: 0

SELECT '0c. apakah namanya sudah ada' AS cek, COUNT(*) AS baris
FROM menurole WHERE menu = 'Petty Cash Flow Realisation';
-- harus: 0

SELECT '0d. calon penerima (pemegang Bank)' AS cek, COUNT(*) AS pengguna
FROM useraccess WHERE menu = 'Bank';
-- di basis data lokal 15 Okt 2026: 20 pengguna

-- ============================================================================
-- MULAI PERUBAHAN
-- ============================================================================
START TRANSACTION;

-- ----------------------------------------------------------------------------
-- 1. menurole : hak akses menu baru
--    Bentuknya mengikuti baris 38 ('Cash') karena menunya memang ada di
--    kelompok Cash: menu_group 'Cash', profit_center 'NAG'. display_name
--    diisi (seperti baris-baris baru 138 dst) supaya tampil rapi di halaman
--    Manage Role.
-- ----------------------------------------------------------------------------
INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 144, 'Petty Cash Flow Realisation', 'Petty Cash Flow Realisation', 'Cash', '', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE menu = 'Petty Cash Flow Realisation')
  AND NOT EXISTS (SELECT 1 FROM menurole WHERE id = 144);

-- ----------------------------------------------------------------------------
-- 2. useraccess : diberikan ke PEMEGANG 'Bank'
--
--    ALASANNYA: pemegang 'Bank' SUDAH bisa membuka Report Cash Flow
--    Realisation yang asli, dan laporan asli itu sudah memuat SELURUH akun
--    kas yang sama (plus akun bank). Jadi pemberian ini TIDAK membuka data
--    baru untuk siapa pun - cuma menaruh laporan yang sama, terbatas akun
--    kas, di menu Cash. Kalau dasarnya dipakai pemegang 'Cash', ada 20
--    pengguna yang sebagian BELUM pernah bisa melihat laporan ini.
--
--    fullname & create_user disalin dari baris 'Bank' milik pengguna itu,
--    jadi tidak ada nama yang ditebak.
--
--    KALAU MAU DAFTAR PENERIMA YANG LAIN: jangan jalankan bagian 2 ini,
--    lalu pakai bagian 2b di bawah (daftar nama diisi sendiri), atau atur
--    lewat halaman Manage Role seperti biasa.
-- ----------------------------------------------------------------------------
INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT b.username, b.fullname, 'Petty Cash Flow Realisation', NOW(), b.create_user
FROM useraccess b
WHERE b.menu = 'Bank'
  AND NOT EXISTS (
        SELECT 1 FROM useraccess x
        WHERE x.username = b.username
          AND x.menu = 'Petty Cash Flow Realisation'
      );

-- ----------------------------------------------------------------------------
-- 2b. ALTERNATIF - daftar penerima ditentukan sendiri.
--     Hapus tanda komentar, isi daftar nama, dan JANGAN jalankan bagian 2.
-- ----------------------------------------------------------------------------
-- INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
-- SELECT u.username, MAX(u.fullname), 'Petty Cash Flow Realisation', NOW(), 'migrasi'
-- FROM useraccess u
-- WHERE u.username IN ('indro', 'willy')          -- <- isi di sini
--   AND NOT EXISTS (SELECT 1 FROM useraccess x
--                   WHERE x.username = u.username
--                     AND x.menu = 'Petty Cash Flow Realisation')
-- GROUP BY u.username;

COMMIT;

-- ============================================================================
-- PEMERIKSAAN SESUDAH COMMIT
-- ============================================================================

-- (a) baris menurole-nya ada dan nomornya benar
SELECT 'a. menurole' AS cek, id, menu, display_name, menu_group, profit_center
FROM menurole WHERE menu = 'Petty Cash Flow Realisation';
-- harus: 1 baris, id = 144

-- (b) berapa pengguna yang kebagian
SELECT 'b. penerima' AS cek, COUNT(*) AS pengguna
FROM useraccess WHERE menu = 'Petty Cash Flow Realisation';
-- harus: sama dgn hasil 0d (pemegang 'Bank')

-- (c) tidak ada yang dobel
SELECT 'c. dobel' AS cek, username, COUNT(*) AS baris
FROM useraccess WHERE menu = 'Petty Cash Flow Realisation'
GROUP BY username HAVING COUNT(*) > 1;
-- harus: 0 baris

-- (d) hak akses LAMA tidak ada yang tersentuh
SELECT 'd. hak akses lama' AS cek, menu, COUNT(*) AS pengguna
FROM useraccess WHERE menu IN ('Cash', 'Bank') GROUP BY menu;
-- harus: jumlahnya SAMA PERSIS dgn sebelum migrasi dijalankan
