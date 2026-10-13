-- ============================================================================
-- PERBAIKAN DATA - 13 Okt 2026
-- Jurnal ber-profit-center NAK yang memakai COST CENTER MILIK NAG
--
-- RENTANG: tgl_journal / mj_date >= 2026-09-01 (1 September sampai sekarang).
--          AGUSTUS SENGAJA TIDAK IKUT, sesuai permintaan pemakai - masih ada
--          23 baris / 23 dokumen di Agustus 2026 yang dibiarkan apa adanya.
--
-- JANGAN dijalankan sebelum dibaca.
--
-- ----------------------------------------------------------------------------
-- APA YANG DIPERBAIKI
-- Yang diubah HANYA kolom cost center (no_costcenter + nama_costcenter).
-- Profit center dokumen TIDAK disentuh - profit center itu ditentukan oleh
-- dokumennya sendiri (bank/supplier), jadi yang salah adalah cost center-nya.
-- Nilai debit/kredit TIDAK ada yang berubah sama sekali.
--
-- PEMETAANNYA (nama & group2 sama persis, jadi tidak ada tafsiran):
--   DEP06SUB001  FINANCE, ACCOUNTING & TAX  ->  DEPNK09SUB001  (sama namanya)
--   DEP05SUB001  EXPORT & IMPORT            ->  DEPNK12SUB001  (sama namanya)
--   DEP11SUB001  MARKETING                  ->  DEPNK08SUB001  (sama namanya)
--
-- CAKUPAN (dibaca read-only dari produksi 13 Okt 2026):
--   tbl_list_journal     : 29 baris / 29 dokumen
--       25 FOREX GAIN OR LOSS  (COA 8.52.01, DEP06SUB001)
--        3 DEP05SUB001         (Payment Voucher 1, MEMO-EXIM 2)
--        1 DEP11SUB001         (Settlement, KKK/N/2026/10/00005)
--   tbl_memorial_journal : 25 baris  - cerminan jurnal FOREX yang sama.
--       WAJIB ikut diperbaiki, kalau tidak kedua tabel jadi berbeda isi.
--       Tabel ini TIDAK punya kolom nama_costcenter, jadi hanya no_costcenter.
--
--   Status barisnya: 25 Post, 3 Approved, 1 Draft. TIDAK ADA yang Cancel.
--
-- AMAN DIULANG: tiap UPDATE dijaga nilai LAMA-nya, jadi putaran kedua
-- mengubah 0 baris, dan kalau isinya sudah bukan cost center NAG itu lagi,
-- tidak ada yang tersentuh.
--
-- CATATAN TERPISAH - TIDAK dikerjakan di sini:
--   COA 2.18.02 (UTANG LAIN-LAIN - FORWARDER) keempat penanda grupnya 'N'.
--   Menurut aturan dropdown sendiri (cc.php), COA tanpa grup TIDAK punya cost
--   center yang sah - artinya 2 baris ber-COA 2.18.02 di bawah idealnya
--   dikosongkan ('-'), bukan sekadar dipindah ke cost center NAK. Itu
--   keputusan akuntansi, bukan sekadar perbaikan pasangan, jadi di sini
--   diperlakukan sama dgn yang lain (dipindah ke NAK) dan dilaporkan supaya
--   pemakai yang memutuskan.
-- ============================================================================

START TRANSACTION;

-- ----------------------------------------------------------------------------
-- SEBELUM - lihat dulu apa yang akan berubah
-- ----------------------------------------------------------------------------
SELECT 'SEBELUM' AS tahap, j.type_journal, j.no_coa, j.profit_center,
       j.no_costcenter, j.nama_costcenter, COUNT(*) AS baris,
       COUNT(DISTINCT j.no_journal) AS dokumen
FROM tbl_list_journal j
WHERE j.profit_center = 'NAK'
  AND j.tgl_journal >= '2026-09-01'
  AND j.no_costcenter IN ('DEP06SUB001', 'DEP05SUB001', 'DEP11SUB001')
GROUP BY j.type_journal, j.no_coa, j.profit_center, j.no_costcenter, j.nama_costcenter
ORDER BY baris DESC;
-- harap: 25 FOREX (DEP06SUB001) + 3 (DEP05SUB001) + 1 (DEP11SUB001) = 29 baris

SELECT 'SEBELUM mj' AS tahap, m.id_cmj, m.profit_center, m.no_costcenter,
       COUNT(*) AS baris
FROM tbl_memorial_journal m
WHERE m.profit_center = 'NAK'
  AND m.mj_date >= '2026-09-01'
  AND m.no_costcenter = 'DEP06SUB001'
GROUP BY m.id_cmj, m.profit_center, m.no_costcenter;
-- harap: CMJ021, 25 baris

-- ----------------------------------------------------------------------------
-- 1. tbl_list_journal : FINANCE, ACCOUNTING & TAX   (25 baris, FOREX)
-- ----------------------------------------------------------------------------
UPDATE tbl_list_journal
SET no_costcenter   = 'DEPNK09SUB001',
    nama_costcenter = 'FINANCE, ACCOUNTING & TAX'
WHERE profit_center  = 'NAK'
  AND no_costcenter  = 'DEP06SUB001'
  AND tgl_journal   >= '2026-09-01';
-- harap: 25 rows affected (putaran kedua: 0)

-- ----------------------------------------------------------------------------
-- 2. tbl_list_journal : EXPORT & IMPORT             (3 baris)
-- ----------------------------------------------------------------------------
UPDATE tbl_list_journal
SET no_costcenter   = 'DEPNK12SUB001',
    nama_costcenter = 'EXPORT & IMPORT'
WHERE profit_center  = 'NAK'
  AND no_costcenter  = 'DEP05SUB001'
  AND tgl_journal   >= '2026-09-01';
-- harap: 3 rows affected (putaran kedua: 0)

-- ----------------------------------------------------------------------------
-- 3. tbl_list_journal : MARKETING                   (1 baris)
-- ----------------------------------------------------------------------------
UPDATE tbl_list_journal
SET no_costcenter   = 'DEPNK08SUB001',
    nama_costcenter = 'MARKETING'
WHERE profit_center  = 'NAK'
  AND no_costcenter  = 'DEP11SUB001'
  AND tgl_journal   >= '2026-09-01';
-- harap: 1 row affected (putaran kedua: 0)

-- ----------------------------------------------------------------------------
-- 4. tbl_memorial_journal : cerminan jurnal FOREX   (25 baris)
--    Tabel ini tidak punya kolom nama_costcenter.
-- ----------------------------------------------------------------------------
UPDATE tbl_memorial_journal
SET no_costcenter  = 'DEPNK09SUB001'
WHERE profit_center = 'NAK'
  AND no_costcenter = 'DEP06SUB001'
  AND mj_date      >= '2026-09-01';
-- harap: 25 rows affected (putaran kedua: 0)

COMMIT;

-- ============================================================================
-- PEMERIKSAAN SESUDAH COMMIT
-- ============================================================================

-- (a) tidak ada lagi ketidakcocokan sejak 1 September
SELECT 'a. sisa tidak cocok (>= 1 Sep)' AS cek,
       j.type_journal, j.profit_center, j.no_costcenter, c.id_pc AS cc_milik,
       COUNT(*) AS baris
FROM tbl_list_journal j
INNER JOIN (SELECT no_cc, MAX(id_pc) id_pc FROM b_master_cc
            WHERE no_cc <> '' AND no_cc IS NOT NULL GROUP BY no_cc) c
        ON c.no_cc = j.no_costcenter
WHERE j.profit_center IN ('NAG', 'NAK')
  AND j.profit_center <> c.id_pc
  AND j.tgl_journal >= '2026-09-01'
GROUP BY j.type_journal, j.profit_center, j.no_costcenter, c.id_pc;
-- harus: 0 baris

-- (b) hasilnya sekarang
SELECT 'b. sesudah' AS cek, j.type_journal, j.no_costcenter, j.nama_costcenter,
       COUNT(*) AS baris
FROM tbl_list_journal j
WHERE j.profit_center = 'NAK'
  AND j.tgl_journal >= '2026-09-01'
  AND j.no_costcenter IN ('DEPNK09SUB001', 'DEPNK12SUB001', 'DEPNK08SUB001')
GROUP BY j.type_journal, j.no_costcenter, j.nama_costcenter
ORDER BY baris DESC;
-- harus: 25 + 3 + 1 = 29 baris, semuanya ber-cost-center DEPNK*

-- (c) tbl_memorial_journal ikut bersih
SELECT 'c. memorial journal' AS cek, m.id_cmj, m.no_costcenter, COUNT(*) AS baris
FROM tbl_memorial_journal m
WHERE m.profit_center = 'NAK' AND m.mj_date >= '2026-09-01'
GROUP BY m.id_cmj, m.no_costcenter;
-- harus: tidak ada lagi DEP06SUB001

-- (d) AGUSTUS memang TIDAK tersentuh (bukti rentangnya tepat)
SELECT 'd. Agustus dibiarkan' AS cek, COUNT(*) AS baris,
       COUNT(DISTINCT j.no_journal) AS dokumen
FROM tbl_list_journal j
INNER JOIN (SELECT no_cc, MAX(id_pc) id_pc FROM b_master_cc
            WHERE no_cc <> '' AND no_cc IS NOT NULL GROUP BY no_cc) c
        ON c.no_cc = j.no_costcenter
WHERE j.profit_center IN ('NAG', 'NAK')
  AND j.profit_center <> c.id_pc
  AND j.tgl_journal >= '2026-08-01' AND j.tgl_journal < '2026-09-01';
-- harus: TETAP 23 baris / 23 dokumen

-- (e) nilai jurnal tidak ada yang berubah - total debit/kredit periode ini
SELECT 'e. total nilai' AS cek,
       ROUND(SUM(j.debit_idr), 2) AS debit, ROUND(SUM(j.credit_idr), 2) AS kredit
FROM tbl_list_journal j
WHERE j.profit_center = 'NAK' AND j.tgl_journal >= '2026-09-01';
-- bandingkan dgn hasil yang sama SEBELUM dijalankan: harus PERSIS sama
