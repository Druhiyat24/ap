-- =============================================================================
-- fix_saldo_barcode_F206086.sql
-- Basis data : signalbit_erp (produksi)
-- Tabel      : whs_saldo_awal_nilai_persediaan  (1 baris di-UPDATE)
-- Dibuat     : 4 November 2026
--
-- MASALAH
--   Pada periode 2026-10-01, saldo roll lot 05666/10/24 (BPB GK/RI/0125/00090,
--   id_item 44196, WS TSH/0924/102, qty 6,1) tersimpan dengan barcode
--   F206086, padahal barcode fisiknya F126124. Pada periode 2025-11-01 sampai
--   2026-09-01 barcode itu masih tertulis benar sebagai F126124.
--
-- SEBAB
--   Lihat ca_fabric_copy_saldo_barcode.php. Pemetaan barcode lama->baru dulu
--   hanya menyaring `whs_mut_lokasi.status = 'Y'` dan TIDAK memeriksa status
--   baris tujuannya di whs_lokasi_inmaterial. Mutasi PINDAH RAK yang sah
--   (MT/0826/00002, 3 Agu 2026, G.9.2.00 -> GB.C.15.2.00, status Y) bertemu
--   baris F206086 yang berasal dari mutasi DIBATALKAN (MT/0825/00028,
--   1 Agu 2025, status N), lalu barcode batal itu ikut terpakai.
--   Kodenya sudah diperbaiki 4 Nov 2026 (penyaring `b.status = 'Y'`).
--
-- KENAPA PERLU PERBAIKAN DATA TERPISAH
--   Perbaikan kode hanya mencegah kejadian baru; baris yang sudah terlanjur
--   tersimpan TIDAK membetulkan diri sendiri. Sudah diperiksa: dengan
--   pemetaan yang baru TIDAK ADA pemetaan yang bersumber dari F206086, jadi
--   Copy Saldo berikutnya akan membaca F206086 lalu menuliskannya lagi -
--   terbawa terus ke semua periode berikutnya kalau tidak dibetulkan sekarang.
--
-- SIFAT
--   Aman dijalankan berulang: jalan pertama 1 baris, jalan berikutnya 0 baris,
--   karena WHERE-nya mensyaratkan nilai lama yang sudah tidak ada lagi.
--   Dijaga NOT EXISTS supaya tidak mungkin membuat dua baris F126124 pada
--   periode yang sama.
--
-- BELUM BISA DIPASTIKAN SENDIRI - LANGKAH 1 WAJIB DIJALANKAN DULU
--   Total ada 173 pemetaan palsu semacam ini di tabel mutasi. Pada SALINAN
--   LOKAL (periode 2025-11-01 s/d 2026-08-01) hasil sapuannya NOL baris
--   terdampak. Tetapi periode 2026-09-01 dan 2026-10-01 TIDAK ADA di salinan
--   lokal, sedangkan justru di periode 2026-10-01 itulah kasus ini muncul.
--   Jadi: jalankan LANGKAH 1 lebih dulu. Kalau yang keluar hanya baris
--   F206086, lanjutkan ke langkah 2. Kalau ada barcode LAIN yang ikut
--   terdaftar, BERHENTI dan kabari - perbaikannya perlu diperluas dan
--   masing-masing perlu dipastikan dulu.
-- =============================================================================

-- ----------------------------------------------------------------------------
-- LANGKAH 1 - DETEKSI: baris tersimpan mana saja yang barcode-nya berasal dari
--   pemetaan palsu (baris tujuan di whs_lokasi_inmaterial berstatus N).
--
--   Himpunannya dimaterialkan ke tabel sementara BERINDEKS lebih dulu. Jangan
--   ditulis sebagai IN (subquery ber-GROUP BY): bentuk itu membuat MariaDB
--   memindai ulang 400 ribu baris untuk tiap baris saldo dan tidak selesai
--   dalam waktu wajar.
--   Tabel sementara hanya hidup di sesi ini dan tidak mengubah data apa pun.
-- ----------------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS map_lama;
DROP TEMPORARY TABLE IF EXISTS map_baru;
DROP TEMPORARY TABLE IF EXISTS map_palsu;

CREATE TEMPORARY TABLE map_lama AS
  SELECT idbpb_det, no_barcode
  FROM whs_mut_lokasi a
  INNER JOIN whs_lokasi_inmaterial b ON b.no_barcode_old = a.idbpb_det
  WHERE a.status = 'Y'
  GROUP BY no_barcode;

CREATE TEMPORARY TABLE map_baru AS
  SELECT idbpb_det, no_barcode
  FROM whs_mut_lokasi a
  INNER JOIN whs_lokasi_inmaterial b ON b.no_barcode_old = a.idbpb_det
  WHERE a.status = 'Y' AND b.status = 'Y'
  GROUP BY no_barcode;

ALTER TABLE map_lama ADD KEY no_barcode (no_barcode), ADD KEY idbpb_det (idbpb_det);
ALTER TABLE map_baru ADD KEY no_barcode (no_barcode), ADD KEY idbpb_det (idbpb_det);

CREATE TEMPORARY TABLE map_palsu AS
  SELECT l.idbpb_det AS barcode_asli, l.no_barcode AS barcode_palsu
  FROM map_lama l
  LEFT JOIN map_baru b
         ON b.idbpb_det = l.idbpb_det AND b.no_barcode = l.no_barcode
  WHERE b.no_barcode IS NULL;

ALTER TABLE map_palsu ADD KEY barcode_palsu (barcode_palsu);

-- Berapa banyak pemetaan palsu seluruhnya (diharapkan sekitar 173)
SELECT 'jumlah pemetaan palsu' AS keterangan, COUNT(*) AS jml FROM map_palsu;

-- INI YANG HARUS DIBACA: baris saldo mana saja yang terdampak
SELECT  s.tgl_periode,
        s.no_barcode AS tersimpan_sekarang,
        p.barcode_asli AS seharusnya,
        s.qty, s.id_item, s.no_lot, s.no_dok, s.ws
FROM whs_saldo_awal_nilai_persediaan s
INNER JOIN map_palsu p ON p.barcode_palsu = s.no_barcode
ORDER BY s.tgl_periode, s.no_barcode;

-- ----------------------------------------------------------------------------
-- LANGKAH 2 - PERBAIKAN (jalankan HANYA kalau langkah 1 cuma menampilkan
--   F206086 pada periode 2026-10-01).
--   Jalan pertama: 1 baris. Jalan berikutnya: 0 baris.
-- ----------------------------------------------------------------------------
UPDATE whs_saldo_awal_nilai_persediaan s
SET s.no_barcode = 'F126124'
WHERE s.tgl_periode = '2026-10-01'
  AND s.no_barcode = 'F206086'
  AND s.id_item    = '44196'
  AND s.no_dok     = 'GK/RI/0125/00090'
  -- Penjaga anti-ganda. Tabel sasaran HARUS dibungkus tabel turunan, kalau
  -- dirujuk langsung MySQL menolak dengan galat 1093.
  AND NOT EXISTS (
        SELECT 1 FROM (
            SELECT 1 FROM whs_saldo_awal_nilai_persediaan
            WHERE tgl_periode = '2026-10-01'
              AND no_barcode  = 'F126124'
            LIMIT 1
        ) AS sudah_ada
      );

-- ----------------------------------------------------------------------------
-- LANGKAH 3 - PERIKSA HASIL
--   Harus: satu baris F126124 pada 2026-10-01 dengan qty 6,1, dan TIDAK ADA
--   lagi baris F206086 pada periode itu.
-- ----------------------------------------------------------------------------
SELECT tgl_periode, no_barcode, qty, price, id_item, no_lot, no_dok, ws, style
FROM whs_saldo_awal_nilai_persediaan
WHERE no_barcode IN ('F126124','F206086')
ORDER BY tgl_periode, no_barcode;

-- Riwayat barcode ini harus jadi lurus F126124 dari 2025-11-01 sampai sekarang.

DROP TEMPORARY TABLE IF EXISTS map_palsu;
DROP TEMPORARY TABLE IF EXISTS map_baru;
DROP TEMPORARY TABLE IF EXISTS map_lama;

-- CATATAN: jangan menyelesaikan ini dengan menjalankan ulang Copy Saldo untuk
--   Sep 2026. Memang seluruh periode akan terbentuk benar, tetapi sekalian
--   menyerap data transaksi yang masuk SESUDAH jalan pertama, sehingga total
--   periode 2026-10-01 bisa bergeser - dan syaratnya total bulan lalu harus
--   tetap sama.
