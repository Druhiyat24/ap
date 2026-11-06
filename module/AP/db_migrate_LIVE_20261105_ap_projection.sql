-- ===========================================================================
-- MIGRASI - MENU AP > REPORT > PROJECTION
--
-- Menambah DUA butir menurole:
--   146  AP Projection Report   -> module/AP/ap_projection_report.php
--   147  AP History Projection  -> module/AP/ap_history_projection.php
--
-- Susunannya meniru sidebar ar_dev: satu induk "Projection" di dalam Report,
-- berisi "Projection Report" dan "History Projection".
--
-- HAK AKSESNYA DIBERIKAN HANYA UNTUK 146. Butir 147 sengaja dibuat lebih
-- dulu tetapi BELUM diberikan ke siapa pun, karena halamannya belum ada -
-- menu.php menampilkan tiap turunan secara terpisah, jadi "History
-- Projection" tetap tersembunyi sampai hak aksesnya diberikan nanti. Dengan
-- begitu tidak ada butir menu yang menunjuk ke halaman yang belum jadi.
--
-- WAJIB DIJALANKAN SEBELUM KODE DI-PULL. Kalau kodenya lebih dulu, menunya
-- tidak muncul untuk siapa pun (tidak merusak apa-apa, hanya tidak terlihat).
--
-- AMAN DIULANG: semua INSERT berpagar NOT EXISTS.
--
-- Dibuat 5 Nov 2026. BELUM DIJALANKAN DI PRODUKSI.
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- BAGIAN 0 - PERIKSA DULU. id 146/147 harus BELUM terpakai.
-- Di salinan lokal id tertinggi 145 (Update Payment Plan). Kalau di produksi
-- ternyata sudah ada yang memakai 146/147, BERHENTI dan ganti angkanya di
-- SELURUH berkas ini DAN di module/menu.php.
-- ---------------------------------------------------------------------------
SELECT id, menu, display_name, menu_group, status, profit_center
FROM menurole
WHERE id IN (146, 147)
   OR menu IN ('AP Projection Report', 'AP History Projection');

-- Harapan: memperlihatkan id tertinggi saat ini, dan tidak ada 146/147.
SELECT MAX(id) AS id_tertinggi FROM menurole;


-- ---------------------------------------------------------------------------
-- BAGIAN 1 - BUTIR MENU.
-- menu_group 'AP' supaya duduk sekelompok dgn menu AP lain di layar
-- pengaturan hak akses. profit_center 'NAG' mengikuti Update Payment Plan.
-- ---------------------------------------------------------------------------
INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 146, 'AP Projection Report', 'AP Projection Report', 'AP', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE id = 146
                     OR menu = 'AP Projection Report');

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 147, 'AP History Projection', 'AP History Projection', 'AP', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE id = 147
                     OR menu = 'AP History Projection');

-- Harapan: dua baris, id 146 dan 147.
SELECT id, menu, menu_group, status, profit_center
FROM menurole WHERE id IN (146, 147) ORDER BY id;


-- ---------------------------------------------------------------------------
-- BAGIAN 2 - HAK AKSES.  *** JANGAN DIJALANKAN DULU ***
--
-- Bagian 1 di atas aman dijalankan kapan saja: ia hanya mendaftarkan butir
-- menunya, dan selama belum ada yang diberi hak akses, menunya TIDAK muncul
-- untuk siapa pun.
--
-- Bagian 2 inilah yang memunculkan menunya. JALANKAN HANYA SESUDAH
-- module/AP/ap_projection_report.php ada di server - kalau tidak, akan ada
-- butir menu yang terlihat tetapi mengarah ke halaman yang belum ada.
--
-- Diberikan kepada SETIAP pemegang menu 'Report' - merekalah yang sudah
-- melihat AP > Report hari ini, jadi tidak ada orang baru yang tiba-tiba
-- kebagian menu laporan. (Update Payment Plan dulu memakai patokan
-- 'Payment Voucher AP'; untuk laporan, patokannya 'Report'.)
-- ---------------------------------------------------------------------------
INSERT INTO useraccess (username, menu)
SELECT DISTINCT u.username, 'AP Projection Report'
FROM useraccess u
WHERE u.menu = 'Report'
  AND NOT EXISTS (SELECT 1 FROM useraccess x
                  WHERE x.username = u.username AND x.menu = 'AP Projection Report');

-- Harapan: jumlah pemegang 'AP Projection Report' = jumlah pemegang 'Report'.
SELECT
  (SELECT COUNT(DISTINCT username) FROM useraccess WHERE menu = 'Report')               AS pemegang_report,
  (SELECT COUNT(DISTINCT username) FROM useraccess WHERE menu = 'AP Projection Report') AS pemegang_projection,
  (SELECT COUNT(DISTINCT username) FROM useraccess WHERE menu = 'AP History Projection') AS pemegang_history;

-- Siapa saja yang kebagian.
SELECT username FROM useraccess WHERE menu = 'AP Projection Report' ORDER BY username;


-- ---------------------------------------------------------------------------
-- KALAU PERLU DIBATALKAN:
--   DELETE FROM useraccess WHERE menu IN ('AP Projection Report','AP History Projection');
--   DELETE FROM menurole   WHERE id IN (146, 147);
--
-- NANTI, saat halaman History Projection sudah jadi, hak aksesnya diberikan
-- dengan perintah yang sama persis seperti Bagian 2, cukup ganti nama
-- menunya jadi 'AP History Projection'.
-- ---------------------------------------------------------------------------
