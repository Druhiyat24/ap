-- =====================================================================
-- PERBAIKAN: PPh PV tersimpan dgn idtax = PERSENTASE (bug form edit PV, s/d 13 Nov 2026)
--
-- Gejala : Bank Out yang membayar PV ber-PPh menjurnal PPh ke akun salah,
--          mis. PPh 23 (2%) masuk 2.52.03 (PPN) bukan 2.52.05.
--          Contoh: BK/BCA5858/NAK/1126/00007 - PV-AP/REG/NAK/2026/09/02395-REV_01.
-- Sebab  : form edit PV mengisi kontrabon.idtax dgn pph_code (persentase PPh).
--          2 (= 2%) kebetulan idtax "PPN Penjualan" (akun 2.52.03).
--          Kode sudah dibetulkan 13 Nov 2026; berkas ini membetulkan DATA yang terlanjur rusak.
--
-- CARA PAKAI (produksi):
--   Jalankan dalam SATU sesi/tab (tabel sementara hanya hidup di sesi itu).
--   1. Jalankan BAGIAN 1 -> periksa dua daftar yang keluar.
--      Baris "CEK MANUAL" = PV asal tidak ditemukan, idtax ditebak dari persentase:
--      pastikan jenis PPh-nya benar sebelum lanjut.
--   2. Kalau sudah sesuai, jalankan BAGIAN 2.
--   Jurnal yang diubah sebaiknya berada di periode yang belum closing (tbl_closing_periode).
-- =====================================================================


-- ======================= BAGIAN 1: CEK (hanya SELECT) =======================

-- 1a. Baris kontrabon ber-PPh yang idtax-nya BUKAN pajak PPH, dan idtax yang benar:
--     utamakan dari PV asal (nomor tanpa -REV_xx / revisi lain) utk BPB yang sama.
DROP TEMPORARY TABLE IF EXISTS tmp_pph_benar;
CREATE TEMPORARY TABLE tmp_pph_benar AS
SELECT k.id, k.no_kbon, k.no_bpb, k.status, k.idtax AS idtax_salah, ms.no_coa AS coa_salah,
       k.pph_code, k.pph_value,
       (SELECT k2.idtax FROM kontrabon k2
          INNER JOIN mtax m2 ON m2.idtax = k2.idtax AND m2.category_tax = 'PPH'
         WHERE k2.no_bpb = k.no_bpb AND k2.no_kbon <> k.no_kbon
           AND k2.no_kbon LIKE CONCAT(SUBSTRING_INDEX(k.no_kbon, '-REV', 1), '%')
         ORDER BY k2.id DESC LIMIT 1) AS idtax_pv_asal,
       (SELECT MAX(m3.idtax) FROM mtax m3
         WHERE m3.category_tax = 'PPH' AND m3.percentage = CAST(k.pph_code AS DECIMAL(10,4))) AS idtax_persen
FROM kontrabon k
LEFT JOIN mtax ms ON ms.idtax = k.idtax
WHERE k.pph_value <> 0
  AND (ms.category_tax IS NULL OR ms.category_tax <> 'PPH');

ALTER TABLE tmp_pph_benar ADD idtax_benar INT NULL, ADD sumber VARCHAR(40) NULL;
UPDATE tmp_pph_benar
   SET idtax_benar = COALESCE(idtax_pv_asal, idtax_persen),
       sumber = IF(idtax_pv_asal IS NOT NULL, 'PV asal', 'persentase - CEK MANUAL');

SELECT t.no_kbon, t.no_bpb, t.status, t.idtax_salah, t.coa_salah, t.pph_code, t.pph_value,
       t.idtax_benar, mb.kriteria AS pajak_benar, mb.no_coa AS coa_benar, t.sumber
FROM tmp_pph_benar t
LEFT JOIN mtax mb ON mb.idtax = t.idtax_benar
ORDER BY t.sumber DESC, t.no_kbon, t.no_bpb;

-- 1b. Baris jurnal Bank Out (type 'Payment Voucher') yang PPh-nya masuk akun salah.
DROP TEMPORARY TABLE IF EXISTS tmp_jurnal_pph_salah;
CREATE TEMPORARY TABLE tmp_jurnal_pph_salah AS
SELECT j.id, j.no_journal, j.tgl_journal, j.reff_doc, j.status,
       j.no_coa AS coa_salah, j.nama_coa AS nama_salah, j.credit, j.credit_idr,
       mb.no_coa AS coa_benar, mb.nama_coa AS nama_benar
FROM tbl_list_journal j
INNER JOIN (SELECT no_kbon, MAX(coa_salah) coa_salah, MAX(idtax_benar) idtax_benar
              FROM tmp_pph_benar GROUP BY no_kbon) t ON t.no_kbon = j.reff_doc
INNER JOIN mtax mb ON mb.idtax = t.idtax_benar
WHERE j.type_journal = 'Payment Voucher'
  AND j.no_coa = t.coa_salah
  AND j.credit <> 0;

SELECT * FROM tmp_jurnal_pph_salah ORDER BY no_journal, id;


-- ======================= BAGIAN 2: PERBAIKAN =======================
-- Cadangan dulu (CREATE TABLE langsung tersimpan, di luar transaksi).
CREATE TABLE bak20261113_kontrabon_idtax AS
SELECT k.* FROM kontrabon k INNER JOIN tmp_pph_benar t ON t.id = k.id AND t.no_kbon = k.no_kbon;

CREATE TABLE bak20261113_jurnal_pph AS
SELECT j.* FROM tbl_list_journal j INNER JOIN tmp_jurnal_pph_salah t ON t.id = j.id;

START TRANSACTION;

UPDATE kontrabon k
INNER JOIN tmp_pph_benar t ON t.id = k.id AND t.no_kbon = k.no_kbon
SET k.idtax = t.idtax_benar
WHERE t.idtax_benar IS NOT NULL;

UPDATE tbl_list_journal j
INNER JOIN tmp_jurnal_pph_salah t ON t.id = j.id
SET j.no_coa = t.coa_benar, j.nama_coa = t.nama_benar;

COMMIT;

-- Periksa ulang: dua daftar ini harus KOSONG.
SELECT COUNT(*) AS kontrabon_masih_salah FROM kontrabon k LEFT JOIN mtax m ON m.idtax = k.idtax
 WHERE k.pph_value <> 0 AND (m.category_tax IS NULL OR m.category_tax <> 'PPH');
SELECT COUNT(*) AS jurnal_masih_salah FROM tbl_list_journal j INNER JOIN tmp_jurnal_pph_salah t ON t.id = j.id
 WHERE j.no_coa <> t.coa_benar;
