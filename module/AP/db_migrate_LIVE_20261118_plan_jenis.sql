/* ============================================================================
   MIGRASI PRODUKSI - KOLOM 'jenis' DI ap_pvbiaya_plan_det
   Tanggal   : 18 November 2026
   Basis data: signalbit_erp @ 10.10.5.12

   KENAPA
   ------
   Menu Update Payment Date sekarang bisa menjadwalkan BUKAN HANYA PV Biaya,
   tetapi juga DP dan CBD (permintaan pemakai 18 Nov 2026: "sama seperti bank
   out bisa tarik nomor FTR CBD dan DP yang statusnya sudah approve dan masih
   outstanding").

   Ketiganya disimpan di tabel rencana yang sama, tapi keterangan pendamping-
   nya ada di tabel yang BERBEDA:
       Biaya -> tbl_pv_h        (rekening pembayar + kategori arus kas)
       DP    -> kontrabon_h_dp  (rekening pembayar; kategori tetap Uang Muka)
       CBD   -> kontrabon_h_cbd
   Tanpa kolom penanda, laporan tidak tahu harus mencari ke tabel yang mana.

   KALAU BELUM DIJALANKAN, APLIKASINYA TETAP HIDUP - kodenya menanyakan dulu
   apakah kolom ini ada (SHOW COLUMNS), dan kalau belum ada ia bekerja persis
   seperti sebelum DP/CBD ada. Yang terjadi: rencana DP/CBD tidak bisa
   diselesaikan ke rekening & kategorinya, jadi jatuh ke baris "Pengeluaran
   Lain-lain" di Projection Report. Sudah diuji dgn kolomnya dibuang.

   AMAN DIJALANKAN ULANG: kalau kolomnya sudah ada, tidak ada yang berubah.
   TIDAK ADA baris yang dihapus atau diubah nilainya, selain mengisi penanda
   untuk baris lama - yang semuanya PV Biaya.
   ============================================================================ */

SELECT 'sebelum' AS bagian, @@hostname AS server, DATABASE() AS basis_data;

SELECT COUNT(*) AS kolom_jenis_sudah_ada, '0' AS harapan_sebelum_dijalankan
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME   = 'ap_pvbiaya_plan_det'
   AND COLUMN_NAME  = 'jenis';


/* ----------------------------------------------------------------------------
   Kolomnya ditambahkan HANYA kalau belum ada. ALTER TABLE tidak punya
   "IF NOT EXISTS" untuk kolom di MySQL 5.x, jadi dipagari pernyataan siap
   pakai - dgn begitu berkas ini tetap aman dijalankan dua kali.
   ---------------------------------------------------------------------------- */
SET @ada = (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = 'ap_pvbiaya_plan_det'
               AND COLUMN_NAME  = 'jenis');

SET @sql = IF(@ada = 0,
    "ALTER TABLE `ap_pvbiaya_plan_det`
       ADD COLUMN `jenis` varchar(10) NOT NULL DEFAULT 'Biaya' AFTER `no_pv`",
    "SELECT 'kolom jenis sudah ada - tidak ada yang diubah' AS catatan");
PREPARE j FROM @sql;
EXECUTE j;
DEALLOCATE PREPARE j;


/* ----------------------------------------------------------------------------
   Baris LAMA semuanya PV Biaya - menu ini baru bisa menjadwalkan DP/CBD hari
   ini. Nilai bawaannya sudah 'Biaya', tapi diisi ulang di sini supaya baris
   yang (entah bagaimana) kosong tidak tertinggal.
   ---------------------------------------------------------------------------- */
UPDATE `ap_pvbiaya_plan_det` SET `jenis` = 'Biaya'
 WHERE `jenis` IS NULL OR `jenis` = '';


/* ============================================================================
   PEMERIKSAAN AKHIR
   ============================================================================ */
SELECT 'kolom jenis ada' AS yang_dicek, COUNT(*) AS hasil, '1' AS harapan
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_pvbiaya_plan_det'
   AND COLUMN_NAME = 'jenis'
UNION ALL
SELECT 'baris rencana tanpa penanda', COUNT(*), '0'
  FROM `ap_pvbiaya_plan_det` WHERE `jenis` IS NULL OR `jenis` = '';

SELECT `jenis`, COUNT(*) AS baris FROM `ap_pvbiaya_plan_det` GROUP BY `jenis`;
