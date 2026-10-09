-- ============================================================================
-- Req_update_bpb_h / Req_update_bpb  (8 Okt 2026)
--
-- Mengganti nama tabel fitur "Update BPB" supaya penamaannya rapi dan tidak
-- lagi mengandung kata "fabric" - padahal tabelnya dipakai bersama Fabric,
-- Accessories, dan General.
--
--   update_bpb_fabric_h  ->  Req_update_bpb_h
--   update_bpb_fabric    ->  Req_update_bpb
--
-- Kolomnya SAMA PERSIS, hanya namanya yang berubah. Kolom `jenis` tetap
-- dipakai: ketiga menu berbagi satu pasang tabel, jadi daftar Request
-- disaring lewat kolom itu.
--
-- Tabel LAMA TIDAK DIHAPUS. Datanya disalin, bukan dipindah - kalau ada
-- masalah, tinggal arahkan kode kembali ke tabel lama tanpa kehilangan apa
-- pun. Baris DROP ada di paling bawah, sengaja dikomentari.
--
-- Dijalankan di lokal 8 Okt 2026. BELUM dijalankan di produksi.
-- ============================================================================

CREATE TABLE IF NOT EXISTS Req_update_bpb_h LIKE update_bpb_fabric_h;
CREATE TABLE IF NOT EXISTS Req_update_bpb   LIKE update_bpb_fabric;

-- Salin data yang sudah ada. INSERT IGNORE + no_pengajuan UNIQUE membuat
-- perintah ini aman diulang (idempoten).
INSERT IGNORE INTO Req_update_bpb_h SELECT * FROM update_bpb_fabric_h;
INSERT IGNORE INTO Req_update_bpb   SELECT * FROM update_bpb_fabric;

-- Pemeriksaan: jumlah baris harus sama.
SELECT 'Req_update_bpb_h' tabel, (SELECT COUNT(*) FROM update_bpb_fabric_h) lama,
       (SELECT COUNT(*) FROM Req_update_bpb_h) baru
UNION ALL
SELECT 'Req_update_bpb',   (SELECT COUNT(*) FROM update_bpb_fabric),
       (SELECT COUNT(*) FROM Req_update_bpb);

-- Dijalankan NANTI, hanya setelah fitur berjalan normal beberapa waktu:
-- DROP TABLE update_bpb_fabric_h;
-- DROP TABLE update_bpb_fabric;
