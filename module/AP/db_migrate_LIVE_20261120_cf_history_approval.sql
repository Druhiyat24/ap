/* ============================================================================
   HISTORY PROJECTION - ALUR POST & DUA TINGKAT APPROVAL
   Dijalankan di produksi (signalbit_erp). Aman diulang.

   Urutan statusnya:
     DRAFT      -> baru disimpan                         teks "Draft"
     POSTED     -> sesudah Post                          teks "Waiting for First Approval"
     APPROVED1  -> sesudah First Approval                teks "Waiting for Second Approval"
     APPROVED2  -> sesudah Second Approval               teks "Second Approval"

   Kolom `status` yang sudah ada TIDAK disentuh - isinya ACTIVE/CANCEL, yaitu
   apakah dokumennya dibatalkan, bukan sudah sampai mana approvalnya. Dua hal
   yang berbeda tidak boleh berbagi satu kolom: sekali digabung, membatalkan
   dokumen akan menghapus jejak siapa yang pernah menyetujuinya.

   SELURUH DOKUMEN LAMA JADI DRAFT. Itu memang keadaannya: alur approval baru
   ada hari ini, jadi belum ada satu pun yang pernah di-post.
   ============================================================================ */

/* ---------------------------------------------------------------- 1. kolom */
SET @ada := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME   = 'ap_cf_history_h'
                AND COLUMN_NAME  = 'appr_status');

SET @sql := IF(@ada = 0,
    'ALTER TABLE ap_cf_history_h
       ADD COLUMN appr_status VARCHAR(20) NOT NULL DEFAULT ''DRAFT'' AFTER status,
       ADD COLUMN post_by     VARCHAR(100) NULL AFTER appr_status,
       ADD COLUMN post_date   DATETIME     NULL AFTER post_by,
       ADD COLUMN appr1_by    VARCHAR(100) NULL AFTER post_date,
       ADD COLUMN appr1_date  DATETIME     NULL AFTER appr1_by,
       ADD COLUMN appr2_by    VARCHAR(100) NULL AFTER appr1_date,
       ADD COLUMN appr2_date  DATETIME     NULL AFTER appr2_by,
       ADD INDEX idx_appr_status (appr_status)',
    'SELECT ''kolom approval sudah ada - dilewati'' AS info');
PREPARE p FROM @sql; EXECUTE p; DEALLOCATE PREPARE p;

UPDATE ap_cf_history_h
   SET appr_status = 'DRAFT'
 WHERE appr_status IS NULL OR appr_status = '';

/* ------------------------------------------------- 2. tiga hak akses baru
   status = 'Akses', BUKAN 'Menu'. menu.php punya satu query yang menarik
   SELURUH baris ber-status 'Menu' milik pemakai untuk menggambar menunya;
   kalau ketiga hak ini ikut ber-status 'Menu', memberikannya ke seseorang
   akan mengubah daftar menu orang itu - padahal ketiganya cuma tombol di
   dalam satu layar. */
INSERT INTO menurole (menu, display_name, menu_group, status, profit_center)
SELECT * FROM (SELECT 'Bank Projection History Post' AS a,
                      'History Projection - Post' AS b,
                      'Bank' AS c, 'Akses' AS d, 'NAG' AS e) t
 WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE menu = 'Bank Projection History Post');

INSERT INTO menurole (menu, display_name, menu_group, status, profit_center)
SELECT * FROM (SELECT 'Bank Projection History Approve 1' AS a,
                      'History Projection - First Approval' AS b,
                      'Bank' AS c, 'Akses' AS d, 'NAG' AS e) t
 WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE menu = 'Bank Projection History Approve 1');

INSERT INTO menurole (menu, display_name, menu_group, status, profit_center)
SELECT * FROM (SELECT 'Bank Projection History Approve 2' AS a,
                      'History Projection - Second Approval' AS b,
                      'Bank' AS c, 'Akses' AS d, 'NAG' AS e) t
 WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE menu = 'Bank Projection History Approve 2');

/* ------------------------------------------------------------- 3. periksa */
SELECT appr_status, COUNT(*) AS jml FROM ap_cf_history_h GROUP BY appr_status;
SELECT id, menu, display_name, status FROM menurole
 WHERE menu LIKE 'Bank Projection History%' ORDER BY id;
