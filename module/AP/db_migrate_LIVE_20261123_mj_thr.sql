-- ===========================================================================
-- MEMORIAL JOURNAL - TYPE BARU "ACCRUAL RELIGIOUS HOLIDAY ALLOWANCE" (CMJ004)
-- Dijalankan di DATABASE ERP PRODUKSI: signalbit_erp @ 10.10.5.12
-- Tanggal siap: 23 November 2026
--
-- TIDAK ADA perubahan apa pun di database HRIS (hris_nag @ 10.10.5.111).
-- Jalur THR hanya MEMBACA thr_jurnals / employee_atribut / b_master_cc /
-- mastercoa_v2 di sana. Itulah sebabnya penahan jurnal ganda (log_jurnal_thr)
-- diletakkan di sisi ERP, bukan meniru log_jurnal_bpjs yang tinggal di HRIS.
-- ===========================================================================


-- 1. Memunculkan CMJ004 di dropdown TYPE pada tab "Journal From HRIS".
--    Barisnya SUDAH ADA sejak lama; yang kurang hanya penanda status_hris.
UPDATE master_category_mj
   SET status_hris = 'Y'
 WHERE id_cmj = 'CMJ004';


-- 2. Penahan jurnal ganda per periode THR.
--    periode = tanggal 1 bulan periode (mis. 2026-10-01 untuk Oktober 2026).
--    status  = 'POST' saat jurnal terbentuk, 'CANCEL' saat jurnal dibatalkan
--              (lihat cancel_memorialjournal.php & cancel_journal_all.php).
--    SENGAJA TIDAK UNIQUE pada periode: satu periode boleh punya beberapa
--    baris sepanjang yang lama sudah berstatus CANCEL - kalau dibuat unique,
--    periode yang jurnalnya pernah dibatalkan tidak akan bisa dijurnal lagi.
CREATE TABLE IF NOT EXISTS `log_jurnal_thr` (
  `id`           int(11)      NOT NULL AUTO_INCREMENT,
  `no_journal`   varchar(50)  DEFAULT NULL,
  `periode`      date         DEFAULT NULL,
  `status`       varchar(20)  DEFAULT NULL,
  `created_by`   varchar(100) DEFAULT NULL,
  `created_date` datetime     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `no_journal` (`no_journal`),
  KEY `periode_status` (`periode`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ===========================================================================
-- PEMERIKSAAN SESUDAH DIJALANKAN
-- ===========================================================================
-- Harus memberi 4 baris: CMJ001, CMJ003, CMJ004, CMJ020
-- SELECT id_cmj, nama_cmj FROM master_category_mj WHERE status_hris = 'Y';
--
-- Harus memberi tabel kosong, bukan "doesn't exist"
-- SELECT * FROM log_jurnal_thr;
--
-- CATATAN: periode THR yang SUDAH pernah dijurnal manual (Apr s/d Sep 2026
-- ada datanya di HRIS) tidak dikenali tabel ini. Kalau periode-periode itu
-- tidak boleh ditarik lagi lewat menu, isikan sendiri barisnya, mis.:
-- INSERT INTO log_jurnal_thr (no_journal, periode, status, created_by, created_date)
-- VALUES ('<no jurnal manualnya>', '2026-09-01', 'POST', 'migrasi', NOW());
-- ===========================================================================
