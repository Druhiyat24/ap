/* ============================================================================
   PERIKSA SAJA - TIDAK ADA UBAHAN DATA. Seluruhnya SELECT.
   Dibuat 10 Nov 2026 menyusul laporan pemakai: di Invoice Received
   (create_kontrabon_new.php) BPB yang sudah Cancel/Reverse masih ikut tertarik
   sehingga nilainya jadi dobel.

   SEBABNYA (sudah diperbaiki di kode):
   bpb_new menyimpan satu baris per item, dan MEMBATALKAN sebuah BPB TIDAK
   menghapus barisnya - baris lama ditandai status='Cancel' lalu BPB dibuat
   ulang dgn nomor yang SAMA. kontrabon_new_check_bpb.php menjumlahkan
   SUM(qty*price) per no_bpb TANPA menyaring status, jadi kedua rombongan
   baris ikut terjumlah.

   Perbaikan kodenya menambahkan "AND status != 'Cancel'" di:
     - kontrabon_new_check_bpb.php  (pencarian BPB + fallback RETUR bppb_new)
     - kontrabon_new_bpb_show.php   (jendela Show)
   Kolom status ber-collation utf8mb4_general_ci, jadi 'CANCEL' ikut tersaring.

   DIUKUR DI SALINAN LOKAL signalbit_bk_sep26 (9 Nov 2026):
     - 447 nomor BPB memuat baris Cancel SEKALIGUS baris hidup
     - kelebihan seluruhnya Rp 898.974.556,92
     - contoh GEN/IN/0726/02353: hidup 323.500, dibatalkan 988.500,
       endpoint mengembalikan 1.312.000
     - BPB tanpa baris Cancel: 0 yang angkanya berubah - perbaikan ini tidak
       menyentuh apa pun yang sudah benar
     - ir_kontrabon_bpb: 0 baris meleset. TAPI salinan lokal itu per September
       sedangkan menunya hidup sesudahnya, jadi BAGIAN 2 di bawah WAJIB
       dijalankan di produksi.

   Jalankan di produksi lalu kirimkan hasilnya.
   ============================================================================ */

/* ---------------------------------------------------------------- BAGIAN 1
   Seberapa luas di produksi: BPB yang memuat baris Cancel sekaligus hidup.
   Kolom "kelebihan" = yang selama ini ikut terjumlah padahal tidak boleh.
   -------------------------------------------------------------------------- */
SELECT '=== BAGIAN 1 - rekap BPB bercampur ===' AS bagian;

SELECT COUNT(*)                AS jml_bpb_terdampak,
       ROUND(SUM(kelebihan),2) AS total_kelebihan
FROM (
  SELECT no_bpb,
         SUM((qty*price)+((qty*price)*(tax/100)))
       - SUM(IF(status <> 'Cancel', (qty*price)+((qty*price)*(tax/100)), 0)) AS kelebihan
    FROM bpb_new
   GROUP BY no_bpb
  HAVING SUM(status <> 'Cancel') > 0
     AND SUM(status =  'Cancel') > 0
) t;

SELECT '=== BAGIAN 1b - 25 terbesar ===' AS bagian;

SELECT s.no_bpb, s.supplier, s.pc,
       ROUND(s.total_lama, 2)                 AS ditarik_sekarang,
       ROUND(s.total_baru, 2)                 AS seharusnya,
       ROUND(s.total_lama - s.total_baru, 2)  AS kelebihan,
       s.baris_cancel, s.baris_hidup
FROM (
  SELECT no_bpb,
         MAX(supplier)       AS supplier,
         MAX(profit_center)  AS pc,
         SUM((qty*price)+((qty*price)*(tax/100)))                                AS total_lama,
         SUM(IF(status <> 'Cancel', (qty*price)+((qty*price)*(tax/100)), 0))     AS total_baru,
         SUM(status =  'Cancel')                                                 AS baris_cancel,
         SUM(status <> 'Cancel')                                                 AS baris_hidup
    FROM bpb_new
   GROUP BY no_bpb
  HAVING baris_cancel > 0 AND baris_hidup > 0
) s
ORDER BY kelebihan DESC
LIMIT 25;

/* ---------------------------------------------------------------- BAGIAN 2
   YANG PALING PENTING: apakah sudah ADA dokumen Invoice Received TERSIMPAN
   yang terlanjur memakai angka dobel itu? Perbaikan kode hanya mencegah
   kejadian baru - baris yang sudah tersimpan tidak sembuh sendiri.

   Kalau keluarannya kosong, tidak ada dokumen yang perlu diperbaiki.
   -------------------------------------------------------------------------- */
SELECT '=== BAGIAN 2 - dokumen IR tersimpan yang angkanya meleset ===' AS bagian;

SELECT k.no_bpb,
       h.doc_number,
       h.status                                   AS status_ir,
       h.nama_supp,
       k.create_date,
       ROUND(k.total, 2)                          AS total_tersimpan,
       ROUND(b.total_hidup, 2)                    AS total_seharusnya,
       ROUND(k.total - b.total_hidup, 2)          AS selisih
  FROM ir_kontrabon_bpb k
  LEFT JOIN ir_kontrabon_h h ON h.unik_code = k.unik_code
  JOIN (
        SELECT no_bpb,
               SUM(IF(status <> 'Cancel', (qty*price)+((qty*price)*(tax/100)), 0)) AS total_hidup,
               SUM(status = 'Cancel')                                              AS ada_cancel
          FROM bpb_new
         GROUP BY no_bpb
       ) b ON b.no_bpb = k.no_bpb
 WHERE b.ada_cancel > 0
   AND ABS(k.total - b.total_hidup) > 0.05
 ORDER BY ABS(k.total - b.total_hidup) DESC;

SELECT '=== BAGIAN 2b - rekapnya ===' AS bagian;

SELECT COUNT(*)                                   AS baris_ir_meleset,
       COUNT(DISTINCT h.doc_number)               AS jml_dokumen,
       ROUND(SUM(ABS(k.total - b.total_hidup)),2) AS jumlah_selisih
  FROM ir_kontrabon_bpb k
  LEFT JOIN ir_kontrabon_h h ON h.unik_code = k.unik_code
  JOIN (
        SELECT no_bpb,
               SUM(IF(status <> 'Cancel', (qty*price)+((qty*price)*(tax/100)), 0)) AS total_hidup,
               SUM(status = 'Cancel')                                              AS ada_cancel
          FROM bpb_new
         GROUP BY no_bpb
       ) b ON b.no_bpb = k.no_bpb
 WHERE b.ada_cancel > 0 AND ABS(k.total - b.total_hidup) > 0.05;

/* ---------------------------------------------------------------- BAGIAN 3
   Sisi RETUR. bppb_new juga punya kolom status dan juga punya baris Cancel
   (di salinan lokal: 14 baris Cancel, 5 nomor RO bercampur), jadi jalur
   fallback retur di endpoint yang sama kena penyakit serupa.
   -------------------------------------------------------------------------- */
SELECT '=== BAGIAN 3 - RO/BPPB bercampur ===' AS bagian;

SELECT s.no_ro, s.supplier,
       ROUND(s.total_lama, 2)                AS ditarik_sekarang,
       ROUND(s.total_baru, 2)                AS seharusnya,
       ROUND(s.total_lama - s.total_baru, 2) AS kelebihan
FROM (
  SELECT no_ro,
         MAX(supplier) AS supplier,
         SUM((qty*price)+((qty*price)*(tax/100)))                            AS total_lama,
         SUM(IF(status <> 'Cancel', (qty*price)+((qty*price)*(tax/100)), 0)) AS total_baru
    FROM bppb_new
   WHERE no_ro IS NOT NULL AND no_ro <> ''
   GROUP BY no_ro
  HAVING SUM(status =  'Cancel') > 0
     AND SUM(status <> 'Cancel') > 0
) s
ORDER BY kelebihan DESC
LIMIT 25;

/* ---------------------------------------------------------------- BAGIAN 4
   BPB yang SELURUH barisnya Cancel. Sesudah perbaikan, nomor-nomor ini tidak
   lagi bisa di-scan - dan sekarang pesannya berbunyi "has been cancelled or
   reversed", bukan lagi "belongs to another supplier" yang menyesatkan.
   Di salinan lokal ada 35 nomor.
   -------------------------------------------------------------------------- */
SELECT '=== BAGIAN 4 - BPB yang seluruhnya Cancel ===' AS bagian;

SELECT COUNT(*) AS jml_bpb_seluruhnya_cancel FROM (
  SELECT no_bpb FROM bpb_new GROUP BY no_bpb HAVING SUM(status <> 'Cancel') = 0
) t;
