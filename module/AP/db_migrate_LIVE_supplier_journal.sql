-- ============================================================================
-- Kolom `supplier` di jurnal + pengisian OTOMATIS untuk SEMUA jalur input
-- Tanggal : 2026-09-04
-- ============================================================================
--
-- APA INI
--   Menambah kolom `supplier` ke tbl_list_journal dan tbl_list_journal_cancel,
--   lalu memasang TRIGGER yang mengisinya otomatis setiap kali baris jurnal
--   dibuat -- dari jalur MANA PUN.
--
-- CATATAN PENTING
--   Pengisian supplier SUDAH ditangani di KODE PHP (semua jalur input jurnal).
--   LANGKAH 3 (trigger) karena itu OPSIONAL - fungsinya cuma jaring pengaman
--   untuk jalur yang mungkin terlewat. Kalau ragu, JALANKAN LANGKAH 1, 2, dan 4
--   saja, lalu backfill. Trigger bisa dipasang belakangan kapan saja.
--
-- KENAPA DULU DIPERTIMBANGKAN TRIGGER
--   Ada 58 file PHP yang menulis ke tbl_list_journal. Menyunting satu per satu
--   pasti ada yang terlewat, dan kode baru nanti juga akan terlewat. Trigger
--   BEFORE INSERT menutup semuanya sekaligus, termasuk INSERT .. SELECT.
--
-- CATATAN PENTING soal CONVERT() di dalam trigger
--   tbl_list_journal.no_journal bertipe utf8mb4, sedangkan sebagian tabel
--   sumber masih latin1 (bpb.bpbno_int, memo_h.nm_memo, c_petty_cashout_h.no_pco)
--   atau utf8 (kontrabon_h.no_kbon). Tanpa CONVERT ke charset kolomnya,
--   MariaDB mengonversi sisi KOLOM sehingga INDEKS TIDAK TERPAKAI -- tiap baris
--   jurnal jadi memindai penuh 669rb baris bpb. Terukur: 92 ms/baris tanpa
--   CONVERT vs 0,44 ms/baris dengan CONVERT (sekitar 200x lebih cepat).
--   JANGAN menghapus CONVERT-nya.
--
-- Jalankan BERURUTAN. Sesudah selesai jalankan sekali:
--   /module/AP/backfill_journal_supplier.php   (mengisi data LAMA)
-- ============================================================================


-- ---------------------------------------------------------------------------
-- LANGKAH 1 - Tambah kolom. WAJIB di posisi sama pada KEDUA tabel, karena
--             banyak file cancel memakai INSERT .. SELECT * antar keduanya.
-- ---------------------------------------------------------------------------
ALTER TABLE tbl_list_journal        ADD COLUMN supplier VARCHAR(255) NULL DEFAULT NULL AFTER profit_center;
ALTER TABLE tbl_list_journal_cancel ADD COLUMN supplier VARCHAR(255) NULL DEFAULT NULL AFTER profit_center;

-- Harus melaporkan jumlah kolom yang SAMA untuk kedua tabel.
SELECT TABLE_NAME, COUNT(*) AS jml_kolom
FROM   information_schema.columns
WHERE  TABLE_SCHEMA = DATABASE()
  AND  TABLE_NAME IN ('tbl_list_journal','tbl_list_journal_cancel')
GROUP BY TABLE_NAME;


-- ---------------------------------------------------------------------------
-- LANGKAH 2 - Indeks yang dibutuhkan trigger (sisanya sudah ada).
--             Bila error "Duplicate key name", berarti sudah ada - abaikan.
-- ---------------------------------------------------------------------------
ALTER TABLE memo_h ADD INDEX idx_nm_memo (nm_memo);


-- ---------------------------------------------------------------------------
-- LANGKAH 3 (OPSIONAL) - Trigger pengisi supplier (sumber dicoba berurutan, berhenti
--             begitu ketemu).
-- ---------------------------------------------------------------------------
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


-- ---------------------------------------------------------------------------
-- LANGKAH 4 - Verifikasi trigger (hanya bila Langkah 3 dijalankan). Harus 2 baris.
-- ---------------------------------------------------------------------------
SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION
FROM   information_schema.triggers
WHERE  TRIGGER_SCHEMA = DATABASE() AND TRIGGER_NAME LIKE '%\_supplier';


-- ============================================================================
-- SESUDAH INI: jalankan sekali /module/AP/backfill_journal_supplier.php
-- untuk mengisi data jurnal LAMA (set-based, hanya baris yang masih kosong).
--
-- CARA MEMBATALKAN:
--   DROP TRIGGER IF EXISTS trg_tbl_list_journal_supplier;
--   DROP TRIGGER IF EXISTS trg_tbl_list_journal_cancel_supplier;
--   ALTER TABLE tbl_list_journal        DROP COLUMN supplier;
--   ALTER TABLE tbl_list_journal_cancel DROP COLUMN supplier;
-- ============================================================================
