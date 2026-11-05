-- ===========================================================================
-- PERBAIKAN DATA SATU DOKUMEN - BK/BCA1979/NAG/1026/00276
--
-- Masalahnya: bank out ini DIBATALKAN 21 Okt 2026, tapi 4 baris jurnalnya
-- masih hidup di tbl_list_journal dengan status 'Approved' dan tanpa jurnal
-- balik. General Ledger hanya menyingkirkan baris berstatus 'Cancel'
-- (ajx_get_data_gl.php), jadi dokumen yang sudah dibatalkan ini tetap
-- terhitung penuh di buku besar.
--
-- Sebabnya ada di cancel_bank_out.php: jurnal balik hanya dibuat untuk baris
-- berstatus 'Draft'. Dokumen ini sudah di-approve (oleh steven, 16 Okt)
-- sebelum dibatalkan (oleh gabby, 21 Okt), jadi status barisnya 'Approved'
-- dan SELECT-nya tidak kena satu baris pun. KODENYA SUDAH DIPERBAIKI -
-- berkas ini hanya membereskan dokumen yang terlanjur.
--
-- Pemeriksaan menyeluruh ke produksi (5 Nov 2026): dari SELURUH bank out
-- berstatus Cancel, HANYA dokumen ini yang masih menggeser saldo akun.
-- Tiga dokumen lain yang barisnya juga masih hidup
-- (BK/BCA5858/NAG/0326/00012, .../1025/00015, .../1025/00017) ternyata sudah
-- memuat baris kebalikannya sendiri - sisa bersih tiap akunnya NOL, jadi
-- hanya terlihat di daftar GL, tidak menggeser saldo. Tidak disentuh.
--
-- Cara kerjanya: MENAMBAH jurnal balik, BUKAN menghapus baris aslinya.
-- Baris asli sudah berstatus Approved dan sudah terarsip di
-- tbl_list_journal_cancel; menghapusnya akan menghilangkan jejak dokumen yang
-- pernah di-approve. Ini juga pola yang dipakai cancelkbon.php.
--
-- AMAN DIULANG: langkah 2 memakai NOT EXISTS, jadi menjalankannya dua kali
-- tidak menggandakan apa pun.
--
-- Dibuat 5 Nov 2026. BELUM DIJALANKAN DI PRODUKSI.
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- LANGKAH 0 - PERIKSA DULU. Jalankan ini sendiri dan lihat hasilnya sebelum
-- menjalankan langkah 2. Kalau hasilnya tidak seperti yang tertulis di
-- bawah, BERHENTI - berarti keadaannya sudah berubah sejak diperiksa.
-- ---------------------------------------------------------------------------

-- 0a. Kepala dokumennya harus sudah Cancel.
--     Harapan: 1 baris, status = Cancel, cancel_by = gabby,
--              cancel_date = 2026-10-21 10:05:08
SELECT no_bankout, bankout_date, nama_supp, curr, amount, status,
       approve_by, approve_date, cancel_by, cancel_date
FROM b_bankout_h
WHERE no_bankout = 'BK/BCA1979/NAG/1026/00276';

-- 0b. Baris jurnalnya.
--     Harapan: 4 baris, semuanya status 'Approved', type_journal
--              'Payment Voucher', dan TIDAK ADA satu pun yang 'Reverse ...'
--              1.10.01 BCA 008-997-1979                  credit 124.866,00
--              7.52.01 BEBAN TRANSPORTASI                debit  126.000,00
--              1.52.04 PPN MASUKAN                       debit    1.386,00
--              2.52.05 PPH PASAL 23                      credit   2.520,00
SELECT id, tgl_journal, type_journal, no_coa, nama_coa,
       debit, credit, rate, curr, status, profit_center
FROM tbl_list_journal
WHERE no_journal = 'BK/BCA1979/NAG/1026/00276'
ORDER BY id;

-- 0c. Sisa bersih per akun SEBELUM diperbaiki.
--     Harapan: 4 baris, tidak ada yang nol - itulah yang salah.
SELECT no_coa, nama_coa,
       ROUND(SUM(ROUND(debit * rate, 2) - ROUND(credit * rate, 2)), 2) AS sisa_dr_min_cr
FROM tbl_list_journal
WHERE no_journal = 'BK/BCA1979/NAG/1026/00276'
GROUP BY no_coa, nama_coa
ORDER BY no_coa;


-- ---------------------------------------------------------------------------
-- LANGKAH 1 - CADANGAN. Menyimpan rupa baris jurnalnya sebelum disentuh.
-- Tabel ini hanya untuk jaga-jaga; boleh dihapus setelah hasilnya cocok.
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS bak_fix_bankout_00276;
CREATE TABLE bak_fix_bankout_00276 AS
SELECT * FROM tbl_list_journal
WHERE no_journal = 'BK/BCA1979/NAG/1026/00276';

-- Harapan: 4
SELECT COUNT(*) AS baris_dicadangkan FROM bak_fix_bankout_00276;


-- ---------------------------------------------------------------------------
-- LANGKAH 2 - MEMBUAT JURNAL BALIKNYA.
--
-- Nilainya DIAMBIL DARI BARIS ASLINYA (debit dan credit ditukar), bukan
-- diketik ulang - jadi tidak ada peluang salah ketik angka.
--
-- tgl_journal diisi TANGGAL PEMBATALAN (2026-10-21), bukan tanggal jurnal
-- aslinya. Itu yang dihasilkan cancel_bank_out.php untuk pembatalan
-- berikutnya, jadi dokumen lama dan baru berbentuk sama. Keduanya di
-- Oktober 2026, jadi buku besar bulan itu tetap bersih.
--
-- status disalin dari baris aslinya ('Approved') supaya jurnal baliknya ikut
-- terhitung di GL - kalau tidak, saldonya tidak akan kembali.
-- ---------------------------------------------------------------------------
INSERT INTO tbl_list_journal
    (no_journal, tgl_journal, type_journal, no_coa, nama_coa,
     no_costcenter, nama_costcenter, reff_doc, reff_date,
     faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate,
     debit, credit, debit_idr, credit_idr,
     status, keterangan, create_by, create_date,
     approve_by, approve_date, cancel_by, cancel_date,
     created_at, updated_at, profit_center, supplier)
SELECT
    j.no_journal,
    '2026-10-21'                        AS tgl_journal,
    CONCAT('Reverse ', j.type_journal)  AS type_journal,
    j.no_coa, j.nama_coa,
    j.no_costcenter, j.nama_costcenter, j.reff_doc, j.reff_date,
    j.faktur_pajak, j.tgl_faktur_pajak, j.buyer, j.no_ws, j.curr, j.rate,
    j.credit     AS debit,          -- ditukar
    j.debit      AS credit,         -- ditukar
    j.credit_idr AS debit_idr,      -- ditukar
    j.debit_idr  AS credit_idr,     -- ditukar
    j.status, j.keterangan, j.create_by, j.create_date,
    'gabby'               AS approve_by,     -- sama dgn cancel_by dokumennya
    '2026-10-21 10:05:08' AS approve_date,   -- sama dgn cancel_date dokumennya
    j.cancel_by, j.cancel_date, j.created_at, j.updated_at,
    j.profit_center, j.supplier
FROM tbl_list_journal j
WHERE j.no_journal = 'BK/BCA1979/NAG/1026/00276'
  AND j.type_journal NOT LIKE '%Reverse%'
  AND NOT EXISTS (
        /* Sudah pernah dibuatkan jurnal balik? Jangan digandakan. */
        SELECT 1 FROM tbl_list_journal x
        WHERE x.no_journal = 'BK/BCA1979/NAG/1026/00276'
          AND x.type_journal LIKE '%Reverse%'
  );


-- ---------------------------------------------------------------------------
-- LANGKAH 3 - PERIKSA HASILNYA.
-- ---------------------------------------------------------------------------

-- 3a. Harus jadi 8 baris: 4 asli + 4 balik.
--     Harapan: baris_asli = 4, baris_balik = 4
SELECT SUM(type_journal NOT LIKE '%Reverse%') AS baris_asli,
       SUM(type_journal LIKE '%Reverse%')     AS baris_balik,
       COUNT(*)                                AS total
FROM tbl_list_journal
WHERE no_journal = 'BK/BCA1979/NAG/1026/00276';

-- 3b. INI PENJAGA UTAMANYA.
--     Harapan: TIDAK ADA BARIS SAMA SEKALI - artinya dokumen ini sudah tidak
--     menggeser saldo akun mana pun. Kalau masih ada baris yang keluar,
--     perbaikannya belum benar.
SELECT no_coa, nama_coa,
       ROUND(SUM(ROUND(debit * rate, 2) - ROUND(credit * rate, 2)), 2) AS sisa_dr_min_cr
FROM tbl_list_journal
WHERE no_journal = 'BK/BCA1979/NAG/1026/00276'
GROUP BY no_coa, nama_coa
HAVING sisa_dr_min_cr <> 0;

-- 3c. Penyapuan ulang: SELURUH bank out Cancel yang masih menggeser saldo.
--     Harapan: TIDAK ADA BARIS SAMA SEKALI.
SELECT no_journal, COUNT(*) AS akun_bersisa
FROM (
    SELECT j.no_journal, j.no_coa,
           ROUND(SUM(ROUND(j.debit * j.rate, 2) - ROUND(j.credit * j.rate, 2)), 2) AS sisa
    FROM tbl_list_journal j
    INNER JOIN b_bankout_h h ON h.no_bankout = j.no_journal
    WHERE h.status = 'Cancel'
    GROUP BY j.no_journal, j.no_coa
    HAVING sisa <> 0
) t
GROUP BY no_journal;


-- ---------------------------------------------------------------------------
-- KALAU PERLU DIBATALKAN (hanya selama belum ada perubahan lain):
--
--   DELETE FROM tbl_list_journal
--   WHERE no_journal = 'BK/BCA1979/NAG/1026/00276'
--     AND type_journal LIKE '%Reverse%';
--
-- Setelah hasilnya cocok, cadangannya boleh dibuang:
--   DROP TABLE bak_fix_bankout_00276;
-- ---------------------------------------------------------------------------
