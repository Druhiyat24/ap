/* ============================================================================
   REKAP PERUBAHAN DATABASE PRODUKSI - signalbit_erp (10.10.5.12)
   Dibuat 2026-10-08

   Berkas ini MERANGKUM seluruh perubahan database yang masih tertinggal di
   produksi. Isinya sudah DISARING: tiap butir diperiksa langsung ke produksi
   lebih dulu (hanya-baca), jadi yang ditulis di sini memang yang BELUM ada.

   SUDAH ADA di produksi, TIDAK perlu dijalankan lagi:
     - tabel update_bpb_fabric_h, update_bpb_fabric (+ kolom id_jo)
     - tabel tbl_ppn_masukan_upload, tbl_ppn_saldo_awal, master_supplier_bank
     - tabel keluarga ir_kontrabon_* (+ kolom ir_kontrabon_h.amount_add_pv)
     - kolom mastersupplier.to_account_manual
     - kolom tbl_list_journal.supplier & tbl_list_journal_cancel.supplier
     - kolom kontrabon_h.id_bank_account, master_project.live_date
     - penggantian nama menu Document Handover (menurole id 66-77) & id 137
     - menu Invoice Received (menurole id 138)
     - pembetulan mastercoa_v2.ind_categori4 (0 baris usang)

   TIDAK PERLU menu baru untuk halaman "Approve Update BPB Fabric": halaman itu
   memakai hak akses menurole id 112 ("Update BPB Fabric") yang sudah ada -
   lihat module/header.php, keduanya di dalam penjaga $id_update '112'.

   Versi produksi: MariaDB 10.11 - mendukung ADD COLUMN IF NOT EXISTS, jadi
   BAGIAN 1 aman diulang.

   CARA MENJALANKAN: pakai klien yang mengerti perintah DELIMITER (HeidiSQL,
   phpMyAdmin, atau mysql CLI). BAGIAN 2 memuat trigger.

   URUTAN: BAGIAN 0 dulu (hanya melihat), lalu 1, 2, 3. BAGIAN 4 JANGAN
   dijalankan sebelum dibaca - ada 12 dokumen yang perlu diputuskan dulu.
   ============================================================================ */


/* ============================================================================
   BAGIAN 0 - PERIKSA DULU (hanya membaca, tidak mengubah apa pun)
   ============================================================================ */

-- 0.1 Kolom ftr_cbd / ftr_dp yang akan ditambah. Harapan: 0 baris.
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, ORDINAL_POSITION
FROM   information_schema.COLUMNS
WHERE  TABLE_SCHEMA = DATABASE()
  AND  TABLE_NAME IN ('ftr_cbd', 'ftr_dp')
  AND  COLUMN_NAME IN ('payment_method', 'item_type', 'profit_center');

-- 0.2 Trigger pengisi kolom supplier. Harapan: 0 baris.
SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION
FROM   information_schema.TRIGGERS
WHERE  TRIGGER_SCHEMA = DATABASE()
  AND  TRIGGER_NAME IN ('trg_tbl_list_journal_supplier',
                        'trg_tbl_list_journal_cancel_supplier');

-- 0.3 Dokumen CBD/DP yang mata uang kepalanya beda dgn detailnya.
--     Harapan saat diperiksa 2026-10-08: 4 dokumen.
SELECT h.no_kbon, h.tgl_kbon, h.nama_supp, h.status,
       h.curr AS curr_header,
       GROUP_CONCAT(DISTINCT d.curr) AS curr_detail
FROM   kontrabon_h_cbd h
JOIN   kontrabon_cbd d ON d.no_kbon = h.no_kbon AND d.status <> 'Cancel'
WHERE  h.status <> 'Cancel'
GROUP  BY h.no_kbon, h.tgl_kbon, h.nama_supp, h.status, h.curr
HAVING curr_detail <> h.curr
ORDER  BY h.tgl_kbon DESC;

-- 0.4 PV yang punya LEBIH DARI SATU baris potongan.
--     Harapan saat diperiksa 2026-10-08: 12 dokumen.
SELECT no_kbon, COUNT(*) AS jml_baris
FROM   potongan
GROUP  BY no_kbon
HAVING COUNT(*) > 1
ORDER  BY no_kbon;


/* ============================================================================
   BAGIAN 1 - KOLOM BARU DI ftr_cbd & ftr_dp
   Dipakai form FTR CBD/DP: cara bayar, jenis barang, dan profit center yang
   diketik di kepala form. Semuanya NULL-able, jadi 760 baris ftr_cbd dan 32
   baris ftr_dp yang sudah ada TIDAK berubah isinya.
   Aman diulang (IF NOT EXISTS).
   ============================================================================ */

ALTER TABLE ftr_cbd
    ADD COLUMN IF NOT EXISTS payment_method VARCHAR(20) NULL AFTER curr,
    ADD COLUMN IF NOT EXISTS item_type      VARCHAR(50) NULL AFTER payment_method,
    ADD COLUMN IF NOT EXISTS profit_center  VARCHAR(20) NULL AFTER item_type;

ALTER TABLE ftr_dp
    ADD COLUMN IF NOT EXISTS payment_method VARCHAR(20) NULL AFTER curr,
    ADD COLUMN IF NOT EXISTS item_type      VARCHAR(50) NULL AFTER payment_method,
    ADD COLUMN IF NOT EXISTS profit_center  VARCHAR(20) NULL AFTER item_type;


/* ============================================================================
   BAGIAN 2 - TRIGGER PENGISI KOLOM supplier DI JURNAL
   Kolomnya sudah ada di produksi, tetapi pengisinya belum - jadi kolom itu
   masih kosong untuk baris jurnal baru. Trigger ini mengisinya otomatis saat
   baris jurnal disimpan.

   Disalin apa adanya dari db_migrate_LIVE_supplier_journal.sql.
   ============================================================================ */

DROP TRIGGER IF EXISTS trg_tbl_list_journal_supplier;
DROP TRIGGER IF EXISTS trg_tbl_list_journal_cancel_supplier;

DELIMITER $$

CREATE TRIGGER trg_tbl_list_journal_supplier
BEFORE INSERT ON tbl_list_journal
FOR EACH ROW
BEGIN
  DECLARE v_supp VARCHAR(255) DEFAULT NULL;

  -- PENGAMAN: kalau ada tabel sumber yang hilang/berganti nama, error-nya
  -- DIABAIKAN dan baris jurnal TETAP TERSIMPAN (supplier dibiarkan kosong).
  -- Tanpa handler ini, satu tabel sumber bermasalah = SELURUH posting jurnal
  -- berhenti. Untuk sistem keuangan itu terlalu berisiko.
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;

  IF (NEW.supplier IS NULL OR NEW.supplier = '')
     AND NEW.no_journal IS NOT NULL AND NEW.no_journal <> '' AND NEW.no_journal <> '-' THEN

    -- PV / Kontrabon
    SET v_supp = (SELECT nama_supp FROM kontrabon_h
                   WHERE no_kbon = CONVERT(NEW.no_journal USING utf8)
                     AND nama_supp IS NOT NULL AND nama_supp <> '' AND nama_supp <> '-' LIMIT 1);
    -- Bank Out (Payment Voucher / List Payment / None / Payment)
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT nama_supp FROM b_bankout_h
                     WHERE no_bankout = NEW.no_journal
                       AND nama_supp IS NOT NULL AND nama_supp <> '' AND nama_supp <> '-' LIMIT 1);
    END IF;
    -- Bank In (BM/..) - kolom customer menampung lawan transaksi
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT customer FROM tbl_bankin_arcollection
                     WHERE doc_num = NEW.no_journal
                       AND customer IS NOT NULL AND customer <> '' AND customer <> '-' LIMIT 1);
    END IF;
    -- Petty Cash Out (KKK/..)
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT nama_supp FROM c_petty_cashout_h
                     WHERE no_pco = CONVERT(NEW.no_journal USING latin1)
                       AND nama_supp IS NOT NULL AND nama_supp <> '' AND nama_supp <> '-' LIMIT 1);
    END IF;
    -- MEMO-EXIM
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT m.Supplier FROM memo_h h
                      JOIN mastersupplier m ON m.Id_Supplier = h.id_supplier
                     WHERE h.nm_memo = CONVERT(NEW.no_journal USING latin1)
                       AND m.Supplier <> '' AND m.Supplier <> '-' LIMIT 1);
    END IF;
    -- BPB masuk
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT m.Supplier FROM bpb b
                      JOIN mastersupplier m ON m.Id_Supplier = b.id_supplier
                     WHERE b.bpbno_int = CONVERT(NEW.no_journal USING latin1)
                       AND m.Supplier <> '' AND m.Supplier <> '-' LIMIT 1);
    END IF;
    -- BPB retur / RO
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT m.Supplier FROM bppb p
                      JOIN mastersupplier m ON m.Id_Supplier = p.id_supplier
                     WHERE p.bppbno_int = NEW.no_journal
                       AND m.Supplier <> '' AND m.Supplier <> '-' LIMIT 1);
    END IF;
    -- Cadangan terakhir
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT supplier FROM bpb_new
                     WHERE no_bpb = NEW.no_journal
                       AND supplier IS NOT NULL AND supplier <> '' AND supplier <> '-' LIMIT 1);
    END IF;

    IF v_supp IS NOT NULL THEN SET NEW.supplier = v_supp; END IF;
  END IF;
END$$

CREATE TRIGGER trg_tbl_list_journal_cancel_supplier
BEFORE INSERT ON tbl_list_journal_cancel
FOR EACH ROW
BEGIN
  DECLARE v_supp VARCHAR(255) DEFAULT NULL;

  -- PENGAMAN: kalau ada tabel sumber yang hilang/berganti nama, error-nya
  -- DIABAIKAN dan baris jurnal TETAP TERSIMPAN (supplier dibiarkan kosong).
  -- Tanpa handler ini, satu tabel sumber bermasalah = SELURUH posting jurnal
  -- berhenti. Untuk sistem keuangan itu terlalu berisiko.
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;

  IF (NEW.supplier IS NULL OR NEW.supplier = '')
     AND NEW.no_journal IS NOT NULL AND NEW.no_journal <> '' AND NEW.no_journal <> '-' THEN

    -- PV / Kontrabon
    SET v_supp = (SELECT nama_supp FROM kontrabon_h
                   WHERE no_kbon = CONVERT(NEW.no_journal USING utf8)
                     AND nama_supp IS NOT NULL AND nama_supp <> '' AND nama_supp <> '-' LIMIT 1);
    -- Bank Out (Payment Voucher / List Payment / None / Payment)
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT nama_supp FROM b_bankout_h
                     WHERE no_bankout = NEW.no_journal
                       AND nama_supp IS NOT NULL AND nama_supp <> '' AND nama_supp <> '-' LIMIT 1);
    END IF;
    -- Bank In (BM/..) - kolom customer menampung lawan transaksi
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT customer FROM tbl_bankin_arcollection
                     WHERE doc_num = NEW.no_journal
                       AND customer IS NOT NULL AND customer <> '' AND customer <> '-' LIMIT 1);
    END IF;
    -- Petty Cash Out (KKK/..)
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT nama_supp FROM c_petty_cashout_h
                     WHERE no_pco = CONVERT(NEW.no_journal USING latin1)
                       AND nama_supp IS NOT NULL AND nama_supp <> '' AND nama_supp <> '-' LIMIT 1);
    END IF;
    -- MEMO-EXIM
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT m.Supplier FROM memo_h h
                      JOIN mastersupplier m ON m.Id_Supplier = h.id_supplier
                     WHERE h.nm_memo = CONVERT(NEW.no_journal USING latin1)
                       AND m.Supplier <> '' AND m.Supplier <> '-' LIMIT 1);
    END IF;
    -- BPB masuk
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT m.Supplier FROM bpb b
                      JOIN mastersupplier m ON m.Id_Supplier = b.id_supplier
                     WHERE b.bpbno_int = CONVERT(NEW.no_journal USING latin1)
                       AND m.Supplier <> '' AND m.Supplier <> '-' LIMIT 1);
    END IF;
    -- BPB retur / RO
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT m.Supplier FROM bppb p
                      JOIN mastersupplier m ON m.Id_Supplier = p.id_supplier
                     WHERE p.bppbno_int = NEW.no_journal
                       AND m.Supplier <> '' AND m.Supplier <> '-' LIMIT 1);
    END IF;
    -- Cadangan terakhir
    IF v_supp IS NULL THEN
      SET v_supp = (SELECT supplier FROM bpb_new
                     WHERE no_bpb = NEW.no_journal
                       AND supplier IS NOT NULL AND supplier <> '' AND supplier <> '-' LIMIT 1);
    END IF;

    IF v_supp IS NOT NULL THEN SET NEW.supplier = v_supp; END IF;
  END IF;
END$$

DELIMITER ;


/* ============================================================================
   BAGIAN 3 - DATA: MATA UANG KEPALA CBD/DP IKUT DETAILNYA

   Kepala dokumen menyimpan mata uang yang berbeda dgn detailnya - inilah yang
   membuat PV-AP/CBD/NAG/2026/09/00169 tercetak USD padahal FTR-nya RMB.

   HANYA membetulkan dokumen yang SELURUH detailnya satu mata uang (ragam = 1).
   Dokumen bercampur mata uang sengaja TIDAK disentuh - itu harus diputuskan
   orang, bukan ditebak query.
   ============================================================================ */

UPDATE kontrabon_h_cbd h
JOIN (
    SELECT d.no_kbon,
           MIN(d.curr)            AS curr_benar,
           COUNT(DISTINCT d.curr) AS ragam
    FROM   kontrabon_cbd d
    WHERE  d.status <> 'Cancel'
    GROUP  BY d.no_kbon
) x ON x.no_kbon = h.no_kbon
SET    h.curr = x.curr_benar
WHERE  h.status <> 'Cancel'
  AND  x.ragam = 1
  AND  h.curr <> x.curr_benar;

-- Verifikasi BAGIAN 3. Harapan: 0 baris.
SELECT h.no_kbon, h.curr AS curr_header, GROUP_CONCAT(DISTINCT d.curr) AS curr_detail
FROM   kontrabon_h_cbd h
JOIN   kontrabon_cbd d ON d.no_kbon = h.no_kbon AND d.status <> 'Cancel'
WHERE  h.status <> 'Cancel'
GROUP  BY h.no_kbon, h.curr
HAVING curr_detail <> h.curr;

/* CATATAN LANJUTAN untuk PV-AP/CBD/NAG/2026/09/00169:
   mata uangnya jadi benar, TETAPI dokumen Bank Out yang terlanjur dibuat dari
   angka lama (BK/BCA1979/NAG/1026/00071, status Draft) TIDAK ikut terbetulkan
   oleh query di atas. Dokumen itu perlu di-Cancel lalu dibuat ulang lewat
   aplikasi supaya nilainya mengikuti mata uang yang benar. */


/* ============================================================================
   BAGIAN 4 - JANGAN DIJALANKAN LANGSUNG: POTONGAN GANDA

   12 PV punya lebih dari satu baris `potongan` (hasil simpan dobel). Selama
   baris gandanya masih ada, UNIQUE KEY di bawah PASTI DITOLAK.

   Membuang baris yang mana BUKAN keputusan query - nilainya bisa berbeda satu
   sama lain. Jalankan dulu 4.1 untuk melihat isinya, putuskan baris mana yang
   benar, baru hapus sisanya secara manual. Sesudah 4.1 bersih (0 baris), baru
   jalankan 4.2.
   ============================================================================ */

-- 4.1 Lihat isi tiap baris ganda, lalu putuskan mana yang dipertahankan.
SELECT p.*
FROM   potongan p
WHERE  p.no_kbon IN (SELECT no_kbon FROM potongan GROUP BY no_kbon HAVING COUNT(*) > 1)
ORDER  BY p.no_kbon, p.id;

-- 4.2 BARU dijalankan setelah 4.1 tidak lagi mengembalikan baris.
--     Ini pengaman yang sesungguhnya: menutup SEMUA jalur simpan dobel
--     sekaligus, termasuk skrip manual.
-- ALTER TABLE potongan ADD UNIQUE KEY uq_potongan_no_kbon (no_kbon);


/* ============================================================================
   BAGIAN 5 - VERIFIKASI AKHIR (hanya membaca)
   ============================================================================ */

-- 5.1 Enam kolom baru harus muncul. Harapan: 6 baris.
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, ORDINAL_POSITION
FROM   information_schema.COLUMNS
WHERE  TABLE_SCHEMA = DATABASE()
  AND  TABLE_NAME IN ('ftr_cbd', 'ftr_dp')
  AND  COLUMN_NAME IN ('payment_method', 'item_type', 'profit_center')
ORDER  BY TABLE_NAME, ORDINAL_POSITION;

-- 5.2 Dua trigger harus ada. Harapan: 2 baris.
SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION
FROM   information_schema.TRIGGERS
WHERE  TRIGGER_SCHEMA = DATABASE()
  AND  TRIGGER_NAME IN ('trg_tbl_list_journal_supplier',
                        'trg_tbl_list_journal_cancel_supplier');

-- 5.3 Mata uang kepala CBD/DP sudah sejalan. Harapan: 0 baris.
SELECT h.no_kbon, h.curr AS curr_header, GROUP_CONCAT(DISTINCT d.curr) AS curr_detail
FROM   kontrabon_h_cbd h
JOIN   kontrabon_cbd d ON d.no_kbon = h.no_kbon AND d.status <> 'Cancel'
WHERE  h.status <> 'Cancel'
GROUP  BY h.no_kbon, h.curr
HAVING curr_detail <> h.curr;
