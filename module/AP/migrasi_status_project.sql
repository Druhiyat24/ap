-- ===========================================================================
-- master_project: hapus status "On Hold", pecah "Done" jadi Done + Live
--
--   Done = pekerjaan SELESAI tetapi BELUM di-deploy ke produksi
--   Live = sudah jalan di produksi
--
-- Kode sudah tidak lagi menerima "On Hold" (lihat $valid_status di
-- save_project.php & quick_update_status.php). Baris lama yang masih
-- berstatus "On Hold" tidak akan muncul di kolom board mana pun, jadi harus
-- dipindahkan. Saat skrip ini dibuat, produksi TIDAK punya satu pun baris
-- "On Hold" (Done 34, Planned 5, On Progress 1) - langkah 1 hanya jaring
-- pengaman kalau ada yang menyusul.
--
-- Dijalankan di: [ ] lokal   [ ] produksi (signalbit_erp @ 10.10.5.12)
-- ===========================================================================

-- Langkah 0 - kolom BARU: tanggal go-live, dipisah dari tanggal selesai.
--   actual_date = tanggal DONE (pekerjaan selesai)  <- INI yang dipakai Export Excel
--   live_date   = tanggal mulai jalan di produksi
-- Dipisah supaya memindahkan kartu ke kolom Live TIDAK menimpa tanggal selesai.
ALTER TABLE master_project
    ADD COLUMN live_date DATE NULL DEFAULT NULL COMMENT "Tanggal go-live; tanggal selesai tetap di actual_date"
    AFTER actual_date;

-- Lihat kondisi sekarang
SELECT status, COUNT(*) AS jumlah FROM master_project GROUP BY status ORDER BY jumlah DESC;

-- Langkah 1 - sisa "On Hold" dikembalikan ke Planned (aman, boleh diulang)
UPDATE master_project SET status = 'Planned' WHERE status = 'On Hold';

-- ---------------------------------------------------------------------------
-- Langkah 2 - menandai mana yang SUDAH live.
--
-- Seluruh baris Done yang ada sekarang DIBIARKAN tetap "Done", jadi tidak ada
-- data yang berubah kalau langkah ini dilewati - kartu tinggal digeser ke
-- kolom Live lewat drag & drop di halaman Project.
--
-- Kalau lebih praktis menandai lewat SQL, pakai SALAH SATU di bawah ini.
-- ---------------------------------------------------------------------------

-- Pilihan A - tandai project tertentu saja (ganti id-nya):
-- UPDATE master_project SET status = 'Live' WHERE id IN (1, 2, 3);

-- Pilihan B - anggap semua yang selesai SEBELUM tanggal tertentu sudah live
-- (ganti tanggalnya):
-- UPDATE master_project SET status = 'Live'
-- WHERE status = 'Done' AND actual_date IS NOT NULL AND actual_date < '2026-09-01';

-- Pilihan C - anggap SEMUA yang Done sekarang sudah live, lalu yang belum
-- live digeser balik satu per satu lewat halaman Project:
-- UPDATE master_project SET status = 'Live' WHERE status = 'Done';

-- Verifikasi - tidak boleh ada lagi "On Hold"
SELECT status, COUNT(*) AS jumlah FROM master_project GROUP BY status ORDER BY jumlah DESC;
