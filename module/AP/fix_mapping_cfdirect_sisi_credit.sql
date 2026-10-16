-- ============================================================================
-- PERBAIKAN MASTER DATA - 16 Okt 2026
-- Cash Flow Direct: nilai pembayaran menggelembung / tidak masuk kategori
-- karena COA hanya dipetakan SATU SISI di mastercoa_v2.
--
-- JANGAN dijalankan sebelum dibaca. Ini mengubah MASTER (mastercoa_v2),
-- bukan transaksi - angka laporan ikut berubah begitu disimpan.
--
-- ----------------------------------------------------------------------------
-- CARA CF DIRECT MENGAMBIL ANGKANYA (ini yang ditanyakan)
--
--   tbl_list_journal.no_coa
--     -> mastercoa_v2.id_direct_debit   (dipakai untuk nilai DEBIT baris itu)
--     -> mastercoa_v2.id_direct_credit  (dipakai untuk nilai CREDIT baris itu)
--        -> tbl_master_cashflow.id -> .ind_name
--           -> dicocokkan LEWAT NAMA ke tb_master_pilihan.nama_pilihan
--              (status 'Y', type_pilihan 'Arus Kas dari Aktivitas Operasi')
--
--   Nilai yang tampil = SUM(credit pada COA ber-id_direct_credit)
--                     - SUM(debit  pada COA ber-id_direct_debit)
--
--   Jadi KEDUA SISI COA harus dipetakan ke kategori yang SAMA. Kalau hanya
--   sisi debit yang dipetakan, laporan menghitung BRUTO - sisi credit (koreksi,
--   pembalikan, versi jurnal yang digantikan) tidak ikut mengurangi.
--
-- ----------------------------------------------------------------------------
-- BUKTI (dihitung di salinan lokal, Jan-Sep 2026)
--
--   jurnal                       kategori        SEKARANG          SEHARUSNYA
--   BK/BCA1979/NAG/0626/00102    Pemasok Lain  -1.500.000.000    -500.000.000
--   KKK/N/2026/08/00004          Pemasok Lain       -112.000         -56.000
--   KKK/P/2026/07/00386          Pemasok Lain        -24.000          -8.000
--   KKK/P/2026/07/00409          Pemasok Lain        -24.000          -8.000
--   BK/BCA1982/NAG/0826/00019    Pemasok        -154.900.150,80  -79.009.272,80
--
--   Sesudah sisi credit dipetakan, kelimanya PERSIS sama dgn hitungan acuan
--   (hanya baris berstatus Approved, tanpa baris lama yang sudah digantikan).
--
--   Contoh paling jelas BK/BCA1982: COA 2.13.01 dalam SATU entri yang sama
--   di-debit 154.900.150,80 dan di-kredit 75.890.878,00; kas yang benar-benar
--   keluar 79.009.272,80 (= selisihnya, sama dgn kredit bank 1.10.02).
--   Tanpa pemetaan sisi credit, laporan memakai 154.900.150,80 yang bruto.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 0. LIHAT DULU (tidak mengubah apa pun)
-- ----------------------------------------------------------------------------
SELECT '0. sebelum' AS cek, no_coa, nama_coa, id_direct_debit, id_direct_credit
FROM mastercoa_v2
WHERE no_coa IN ('2.13.01', '2.51.41', '2.51.61')
ORDER BY no_coa;
-- harap: id_direct_debit terisi (2 / 3 / 3), id_direct_credit masih '-' atau kosong

SELECT '0b. kategori tujuan' AS cek, m.id, m.ind_name, p.nama_pilihan, p.type_pilihan, p.status
FROM tbl_master_cashflow m
LEFT JOIN tb_master_pilihan p ON p.nama_pilihan = m.ind_name AND p.status = 'Y'
WHERE m.id IN (2, 3);
-- harus: id 2 = PEMBAYARAN KEPADA PEMASOK, id 3 = PEMBAYARAN KEPADA PEMASOK
--        LAIN-LAIN, dan nama_pilihan TIDAK NULL (kalau NULL, namanya tidak
--        tersambung dan kategori itu tidak akan pernah muncul di laporan)

START TRANSACTION;

-- ----------------------------------------------------------------------------
-- 1. TIGA COA YANG TERBUKTI TERDAMPAK (ada nilai di KEDUA sisi)
-- ----------------------------------------------------------------------------
UPDATE mastercoa_v2 SET id_direct_credit = '2'
WHERE no_coa = '2.13.01' AND COALESCE(id_direct_credit, '-') IN ('-', '');
-- UTANG USAHA PIHAK BERELASI - KAIN -> PEMBAYARAN KEPADA PEMASOK

UPDATE mastercoa_v2 SET id_direct_credit = '3'
WHERE no_coa = '2.51.41' AND COALESCE(id_direct_credit, '-') IN ('-', '');
-- BIAYA YANG MASIH HARUS DIBAYAR - SEWA -> PEMBAYARAN KEPADA PEMASOK LAIN-LAIN

UPDATE mastercoa_v2 SET id_direct_credit = '3'
WHERE no_coa = '2.51.61' AND COALESCE(id_direct_credit, '-') IN ('-', '');
-- BIAYA YANG MASIH HARUS DIBAYAR - FOTOKOPI -> PEMBAYARAN KEPADA PEMASOK LAIN-LAIN

COMMIT;

-- ============================================================================
-- 2. SEMBILAN COA LAIN yang juga baru satu sisi dan terpakai di jurnal
--    kas/bank. SAAT INI BELUM merugikan karena di periode Jan-Sep 2026
--    nilainya hanya muncul di sisi debit - tapi begitu ada koreksi atau
--    pembalikan, gejalanya akan sama persis.
--    SENGAJA DIKOMENTARI: pasangan kategorinya perlu disetujui akuntansi
--    dulu, jangan diikutkan kalau belum yakin.
-- ============================================================================
-- UPDATE mastercoa_v2 SET id_direct_credit = id_direct_debit
-- WHERE no_coa IN ('2.52.03','2.13.15','2.13.07','2.13.99','1.52.03',
--                  '1.52.02','2.13.11','2.51.99','2.13.02')
--   AND COALESCE(id_direct_credit, '-') IN ('-', '');

-- ============================================================================
-- PEMERIKSAAN SESUDAH COMMIT
-- ============================================================================

-- (a) ketiganya sekarang dipetakan dua sisi ke kategori yang sama
SELECT 'a. sesudah' AS cek, no_coa, nama_coa, id_direct_debit, id_direct_credit,
       CASE WHEN id_direct_debit = id_direct_credit THEN 'OK' ELSE 'PERIKSA' END AS status
FROM mastercoa_v2
WHERE no_coa IN ('2.13.01', '2.51.41', '2.51.61')
ORDER BY no_coa;

-- (b) hasil kelima jurnal yang dilaporkan - harus sama dgn kolom SEHARUSNYA
SELECT 'b. hasil' AS cek, a.no_journal,
       ROUND(SUM(CASE WHEN kk.id IN (2,3) THEN ROUND(a.credit * a.rate, 2) ELSE 0 END)
           - SUM(CASE WHEN dd.id IN (2,3) THEN ROUND(a.debit  * a.rate, 2) ELSE 0 END), 2) AS nilai_kategori
FROM tbl_list_journal a
LEFT JOIN mastercoa_v2 b        ON b.no_coa = a.no_coa
LEFT JOIN tbl_master_cashflow dd ON dd.id = b.id_direct_debit
LEFT JOIN tbl_master_cashflow kk ON kk.id = b.id_direct_credit
WHERE a.no_journal IN ('BK/BCA1979/NAG/0626/00102', 'KKK/N/2026/08/00004',
                       'KKK/P/2026/07/00386', 'KKK/P/2026/07/00409',
                       'BK/BCA1982/NAG/0826/00019')
GROUP BY a.no_journal
ORDER BY a.no_journal;
-- harus: -500.000.000 / -56.000 / -8.000 / -8.000 / -79.009.272,80

-- (c) daftar SEMUA COA yang masih dipetakan satu sisi DAN terpakai di jurnal
--     kas/bank - untuk dibereskan berikutnya
SELECT 'c. masih satu sisi' AS cek, a.no_coa, MAX(b.nama_coa) AS nama_coa,
       MAX(b.id_direct_debit) AS debit, MAX(b.id_direct_credit) AS credit,
       ROUND(SUM(a.debit_idr), 0) AS total_debit,
       ROUND(SUM(a.credit_idr), 0) AS total_credit
FROM tbl_list_journal a
INNER JOIN mastercoa_v2 b ON b.no_coa = a.no_coa
WHERE a.tgl_journal >= '2026-01-01'
  AND (a.no_journal LIKE '%BM/%' OR a.no_journal LIKE '%BK/%' OR a.no_journal LIKE '%RCO/%'
       OR a.no_journal LIKE '%RCI/%' OR a.no_journal LIKE '%KKK/%' OR a.no_journal LIKE '%KKM/%')
  AND ((COALESCE(b.id_direct_debit, '-')  NOT IN ('-', '') AND COALESCE(b.id_direct_credit, '-') IN ('-', ''))
    OR (COALESCE(b.id_direct_credit, '-') NOT IN ('-', '') AND COALESCE(b.id_direct_debit, '-')  IN ('-', '')))
GROUP BY a.no_coa
ORDER BY GREATEST(SUM(a.debit_idr), SUM(a.credit_idr)) DESC;
