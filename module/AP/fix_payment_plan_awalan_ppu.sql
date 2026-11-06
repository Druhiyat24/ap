-- ===========================================================================
-- MENGGANTI AWALAN NOMOR DOKUMEN PAYMENT PLAN: AP-PP/... -> PPU/...
--
-- Awalan nomor diganti atas permintaan pemakai (5 Nov 2026). Kodenya sudah
-- memakai PPU (lihat PP_AWALAN di simpan_payment_plan.php); berkas ini
-- membereskan dokumen yang terlanjur dibuat dengan awalan lama.
--
-- AMAN DIJALANKAN WALAU TIDAK ADA APA-APA: kalau tidak ada satu pun dokumen
-- berawalan AP-PP, seluruh UPDATE di bawah mengenai 0 baris. Aman diulang
-- juga - sesudah diganti, tidak ada lagi yang cocok dengan 'AP-PP/%'.
--
-- Nomornya hanya berganti AWALAN. Bagian <bulan><tahun>/<urut> dibiarkan apa
-- adanya, jadi AP-PP/1126/00001 menjadi PPU/1126/00001 - urutan dokumen yang
-- sudah dipakai tim AP tidak berubah.
--
-- DUA TABEL harus ikut: ap_payment_plan_h DAN ap_payment_plan_det. Keduanya
-- bertaut lewat NILAI doc_number (bukan id), jadi kalau hanya satu yang
-- diganti, baris detailnya menjadi yatim dan dokumennya tampak kosong.
-- Karena itu semuanya dibungkus SATU transaksi.
--
-- Dibuat 5 Nov 2026. Sudah dijalankan di LOKAL. BELUM di produksi.
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- LANGKAH 0 - PERIKSA DULU. Jalankan sendiri dan lihat hasilnya.
-- ---------------------------------------------------------------------------

-- 0a. Apa saja yang akan diganti? Kolom "jadi" memperlihatkan nomor barunya.
SELECT h.doc_number                                   AS sekarang,
       CONCAT('PPU/', SUBSTRING(h.doc_number, 7))     AS jadi,
       h.plan_date, h.status, h.created_by,
       (SELECT COUNT(*) FROM ap_payment_plan_det d
        WHERE d.doc_number = h.doc_number)            AS baris_detail
FROM ap_payment_plan_h h
WHERE h.doc_number LIKE 'AP-PP/%'
ORDER BY h.doc_number;

-- 0b. PENJAGA TABRAKAN. Harapan: TIDAK ADA BARIS.
--     Kalau ada, berarti nomor PPU tujuannya sudah dipakai dokumen lain -
--     JANGAN lanjut, nomor itu harus diputuskan dulu mau diapakan.
SELECT h.doc_number AS mau_diganti,
       CONCAT('PPU/', SUBSTRING(h.doc_number, 7)) AS bentrok_dengan
FROM ap_payment_plan_h h
WHERE h.doc_number LIKE 'AP-PP/%'
  AND EXISTS (SELECT 1 FROM ap_payment_plan_h x
              WHERE x.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7)));

-- 0c. Baris detail yang induknya tidak ada (harus 0 sebelum DAN sesudah).
SELECT COUNT(*) AS detail_tanpa_induk
FROM ap_payment_plan_det d
LEFT JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
WHERE h.doc_number IS NULL;


-- ---------------------------------------------------------------------------
-- LANGKAH 1 - CADANGAN. Boleh dihapus setelah hasilnya cocok.
-- ---------------------------------------------------------------------------
DROP TABLE IF EXISTS bak_pp_awalan_h;
DROP TABLE IF EXISTS bak_pp_awalan_det;

CREATE TABLE bak_pp_awalan_h AS
SELECT * FROM ap_payment_plan_h WHERE doc_number LIKE 'AP-PP/%';

CREATE TABLE bak_pp_awalan_det AS
SELECT * FROM ap_payment_plan_det WHERE doc_number LIKE 'AP-PP/%';

SELECT (SELECT COUNT(*) FROM bak_pp_awalan_h)   AS header_dicadangkan,
       (SELECT COUNT(*) FROM bak_pp_awalan_det) AS detail_dicadangkan;


-- ---------------------------------------------------------------------------
-- LANGKAH 2 - GANTI. Dua tabel dalam SATU transaksi.
--
-- DETAIL DIGANTI LEBIH DULU, memakai daftar nomor dari tabel header yang
-- MASIH memakai nama lama. Kalau headernya diganti duluan, tautan nilai itu
-- putus dan baris detailnya tidak bisa ditemukan lagi.
--
-- Baris yang nomor barunya sudah dipakai dokumen lain DILEWATI (NOT EXISTS),
-- jadi perintah ini tidak akan pernah membuat dua dokumen bernomor sama.
-- ---------------------------------------------------------------------------
START TRANSACTION;

UPDATE ap_payment_plan_det d
INNER JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
SET d.doc_number = CONCAT('PPU/', SUBSTRING(d.doc_number, 7))
WHERE d.doc_number LIKE 'AP-PP/%'
  AND NOT EXISTS (SELECT 1 FROM bak_pp_awalan_h b
                  WHERE b.doc_number = h.doc_number
                    AND EXISTS (SELECT 1 FROM ap_payment_plan_h x
                                WHERE x.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7))));

UPDATE ap_payment_plan_h h
SET h.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7))
WHERE h.doc_number LIKE 'AP-PP/%'
  AND NOT EXISTS (SELECT 1 FROM bak_pp_awalan_h b
                  WHERE b.doc_number = h.doc_number
                    AND EXISTS (SELECT 1 FROM ap_payment_plan_h x
                                WHERE x.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7))));

COMMIT;


-- ---------------------------------------------------------------------------
-- LANGKAH 3 - PERIKSA HASILNYA.
-- ---------------------------------------------------------------------------

-- 3a. Harapan: sisa_ap_pp = 0, dan jumlah PPU = jumlah yang tadi dicadangkan.
SELECT (SELECT COUNT(*) FROM ap_payment_plan_h WHERE doc_number LIKE 'AP-PP/%') AS sisa_ap_pp,
       (SELECT COUNT(*) FROM ap_payment_plan_h WHERE doc_number LIKE 'PPU/%')   AS jml_ppu,
       (SELECT COUNT(*) FROM bak_pp_awalan_h)                                   AS tadinya;

-- 3b. PENJAGA UTAMA. Harapan: TIDAK ADA BARIS - tiap dokumen yang diganti
--     harus punya jumlah baris detail yang SAMA PERSIS seperti sebelumnya.
SELECT b.doc_number AS nomor_lama,
       CONCAT('PPU/', SUBSTRING(b.doc_number, 7)) AS nomor_baru,
       (SELECT COUNT(*) FROM bak_pp_awalan_det x WHERE x.doc_number = b.doc_number) AS detail_dulu,
       (SELECT COUNT(*) FROM ap_payment_plan_det y
        WHERE y.doc_number = CONCAT('PPU/', SUBSTRING(b.doc_number, 7)))            AS detail_kini
FROM bak_pp_awalan_h b
HAVING detail_dulu <> detail_kini;

-- 3c. Harapan: 0. Tidak boleh ada baris detail yang kehilangan induknya.
SELECT COUNT(*) AS detail_tanpa_induk
FROM ap_payment_plan_det d
LEFT JOIN ap_payment_plan_h h ON h.doc_number = d.doc_number
WHERE h.doc_number IS NULL;

-- 3d. Harapan: TIDAK ADA BARIS. Tidak boleh ada nomor dokumen kembar.
SELECT doc_number, COUNT(*) AS jml
FROM ap_payment_plan_h
GROUP BY doc_number HAVING jml > 1;


-- ---------------------------------------------------------------------------
-- KALAU PERLU DIKEMBALIKAN (selama belum ada perubahan lain):
--
--   START TRANSACTION;
--   UPDATE ap_payment_plan_det d
--   INNER JOIN bak_pp_awalan_det b
--           ON b.doc_number = CONCAT('PPU/', SUBSTRING(d.doc_number, 7))
--      SET d.doc_number = b.doc_number;
--   UPDATE ap_payment_plan_h h
--   INNER JOIN bak_pp_awalan_h b
--           ON b.doc_number = CONCAT('PPU/', SUBSTRING(h.doc_number, 7))
--      SET h.doc_number = b.doc_number;
--   COMMIT;
--
-- Setelah hasilnya cocok, cadangannya boleh dibuang:
--   DROP TABLE bak_pp_awalan_h; DROP TABLE bak_pp_awalan_det;
-- ---------------------------------------------------------------------------
