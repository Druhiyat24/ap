/* ============================================================================
   PROJECTION OTHER - KOLOM type_report
   Dijalankan di produksi (signalbit_erp). Aman diulang.

   Alasannya: mulai 20 Nov 2026 entri Projection Other DIBEDAKAN per jenis
   laporan - Weekly dan Monthly punya tombol Create sendiri-sendiri (Daily
   masih dibicarakan). Tanpa kolom ini, entri kedua jenis itu bercampur dan
   tidak ada cara memisahkannya lagi di kemudian hari.

   SELURUH BARIS LAMA DIANGGAP WEEKLY: tombol Create yang sudah dipakai
   selama ini memang yang sekarang jadi "Create Weekly", jadi itulah arti
   sebenarnya dari data yang sudah ada - bukan tebakan.
   ============================================================================ */

SET @ada := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME   = 'ap_cf_projection'
                AND COLUMN_NAME  = 'type_report');

SET @sql := IF(@ada = 0,
    'ALTER TABLE ap_cf_projection
       ADD COLUMN type_report VARCHAR(10) NOT NULL DEFAULT ''WEEKLY'' AFTER jenis,
       ADD INDEX idx_type_report (type_report)',
    'SELECT ''kolom type_report sudah ada - dilewati'' AS info');
PREPARE p FROM @sql; EXECUTE p; DEALLOCATE PREPARE p;

/* Baris lama yang (karena apa pun) kosong ikut disetel WEEKLY. */
UPDATE ap_cf_projection
   SET type_report = 'WEEKLY'
 WHERE type_report IS NULL OR type_report = '';

SELECT type_report, COUNT(*) AS jml
  FROM ap_cf_projection
 GROUP BY type_report;
