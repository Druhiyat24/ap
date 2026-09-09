-- ============================================================================
-- BACKFILL: set Type = VAT untuk memorial journal LAMA yang berasal dari tab
-- PPN Masukan tapi tersimpan sebelum kategori VAT ada (Type-nya jadi 'OTHERS').
--
-- LATAR: ppn_masukan_report.php kini menyaring bucket DEDUCTION dengan
--        type_journal = 'VAT'. Tanpa backfill ini, GM lama tidak lagi terhitung
--        sebagai deduction dan saldonya hilang dari report.
--
-- CAKUPAN: HANYA nomor GM yang memang tercatat di tbl_ppn_masukan_upload —
--          jadi tidak mungkin menyenggol memorial journal lain.
--
-- JALANKAN SEKALI; aman diulang (baris yang sudah VAT tidak ikut ter-update).
-- Jalankan juga di PRODUKSI kalau di sana ada GM PPN Masukan sebelum kategori
-- VAT dibuat.
-- ============================================================================
SET @vat := (SELECT id_cmj FROM master_category_mj WHERE UPPER(TRIM(nama_cmj)) = 'VAT' ORDER BY id_cmj LIMIT 1);

-- 1) Jurnal detail
UPDATE tbl_list_journal l
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(l.no_journal USING utf8mb4)
SET l.type_journal = 'VAT'
WHERE l.type_journal <> 'VAT';

-- 2) Header GM
UPDATE tbl_memorial_journal m
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(m.no_mj USING utf8mb4)
SET m.id_cmj = @vat
WHERE @vat IS NOT NULL AND m.id_cmj <> @vat;

-- 3) Cermin SB I (kalau jurnalnya dulu ikut di-include ke SB I)
UPDATE sb_list_journal s
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(s.no_journal USING utf8mb4)
SET s.type_journal = 'VAT'
WHERE s.type_journal <> 'VAT';

UPDATE sb_memorial_journal s
  INNER JOIN (SELECT DISTINCT no_mj FROM tbl_ppn_masukan_upload
              WHERE no_mj IS NOT NULL AND no_mj <> '') u
          ON CONVERT(u.no_mj USING utf8mb4) = CONVERT(s.no_mj USING utf8mb4)
SET s.id_cmj = @vat
WHERE @vat IS NOT NULL AND s.id_cmj <> @vat;
