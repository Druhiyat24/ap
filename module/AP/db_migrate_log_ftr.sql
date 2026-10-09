-- ============================================================================
-- tbl_log_ftr : jejak aktivitas FTR CBD & FTR DP  (9 Okt 2026)
--
-- Bentuk kolomnya MENGIKUTI tbl_log_cash yang sudah dipakai modul Cash, supaya
-- seragam dgn tabel log lain di aplikasi ini. Satu tabel untuk kedua modul -
-- yang membedakan ada di kolom `activitas` ("... FTR CBD" / "... FTR DP").
--
-- Diisi oleh: insertftrcbd/dp (Create), approveftrcbd/dp (Approve),
--             cancelftrcbd/dp (Cancel), update_ftrcbd/dp (Edit).
--
-- Dijalankan di lokal 9 Okt 2026. BELUM dijalankan di produksi.
-- ============================================================================

CREATE TABLE IF NOT EXISTS tbl_log_ftr (
    id          INT(11)      NOT NULL AUTO_INCREMENT,
    nama_user   VARCHAR(100)          DEFAULT NULL,
    activitas   VARCHAR(100)          DEFAULT NULL,
    from_pc     VARCHAR(200)          DEFAULT NULL,
    log_date    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    doc_num     VARCHAR(200)          DEFAULT NULL,
    doc_date    DATE                  DEFAULT NULL,
    keterangan  VARCHAR(255)          DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_doc  (doc_num),
    KEY idx_date (log_date)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

SELECT 'tbl_log_ftr' tabel, COUNT(*) baris FROM tbl_log_ftr;
