-- ===========================================================================
-- MIGRASI - TABEL RENCANA BAYAR AWAL  (ap_payment_plan_awal)
--
-- Menyimpan PAYMENT PLAN AWAL setiap payment voucher, yaitu tanggal bayar
-- bawaan yang dihitung dari jatuh temponya. Menu Update Payment Plan (PPU)
-- yang kemudian MEMPERBARUI rencana itu; tabel ini hanya titik berangkatnya.
--
-- ATURANNYA (dari pemakai, 5 Nov 2026):
--   "payment plan didapat dari hari KAMIS per tanggal di minggu MINGGU-SABTU
--    due date"
-- Jadi: ambil minggu tempat due date berada, dihitung MINGGU sampai SABTU,
-- lalu rencana awalnya = hari KAMIS di minggu itu.
--
--   due Minggu  -> Kamis 4 hari sesudahnya
--   due Kamis   -> hari itu juga
--   due Jumat   -> Kamis 1 hari SEBELUMNYA
--   due Sabtu   -> Kamis 2 hari SEBELUMNYA
--
-- TANGGALNYA DIBIARKAN APA ADANYA WALAU SUDAH LEWAT. Ditegaskan pemakai:
-- "set dulu tetep kamis aslinya, nah di sini kan peran menu PPU ini buat
-- update plan itu". Jadi JANGAN didorong ke Kamis terdekat - tunggakan
-- memang tampil dgn tanggal lamanya sampai tim AP menjadwalkan ulang.
--
-- Rumus MySQL-nya: DAYOFWEEK() memberi 1=Minggu s/d 7=Sabtu, dan Kamis = 5,
-- sehingga Kamis di minggu yang sama = duedate + (5 - DAYOFWEEK(duedate))
-- hari. Nilai itu NEGATIF untuk Jumat & Sabtu, yang memang maunya begitu.
--
-- AMAN DIULANG: tabelnya dibuat dgn IF NOT EXISTS, dan pengisiannya memakai
-- kunci unik no_kbon + INSERT IGNORE, jadi baris yang sudah ada tidak
-- ditimpa. Itu penting: begitu tim AP mengubah rencananya lewat PPU,
-- menjalankan ulang berkas ini TIDAK boleh menariknya kembali ke Kamis.
--
-- Dibuat 5 Nov 2026. BELUM DIJALANKAN DI PRODUKSI.
-- ===========================================================================

-- ---------------------------------------------------------------------------
-- BAGIAN 0 - PERIKSA DULU
-- ---------------------------------------------------------------------------
SELECT COUNT(*) AS tabel_sudah_ada
FROM information_schema.tables
WHERE table_schema = DATABASE() AND table_name = 'ap_payment_plan_awal';

-- Contoh perhitungannya, sekalian membuktikan rumusnya benar untuk KETUJUH
-- hari. Harapan: kolom nama_hari_rencana semuanya 'Thursday'.
SELECT d AS contoh_duedate,
       DAYNAME(d) AS nama_hari_due,
       DATE_ADD(d, INTERVAL (5 - DAYOFWEEK(d)) DAY) AS rencana_awal,
       DAYNAME(DATE_ADD(d, INTERVAL (5 - DAYOFWEEK(d)) DAY)) AS nama_hari_rencana
FROM (SELECT '2026-11-01' d UNION ALL SELECT '2026-11-02' UNION ALL
      SELECT '2026-11-03'   UNION ALL SELECT '2026-11-04' UNION ALL
      SELECT '2026-11-05'   UNION ALL SELECT '2026-11-06' UNION ALL
      SELECT '2026-11-07') t;


-- ---------------------------------------------------------------------------
-- BAGIAN 1 - TABELNYA
--
-- no_kbon dibuat UNIK: satu voucher hanya punya SATU rencana awal. Kunci itu
-- sekaligus yang membuat pengisian ulang di Bagian 2 aman.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ap_payment_plan_awal (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    no_kbon       VARCHAR(255) NOT NULL,
    duedate       DATE         NULL     COMMENT 'jatuh tempo saat rencana awal dihitung',
    plan_awal     DATE         NOT NULL COMMENT 'Kamis di minggu Minggu sampai Sabtu milik duedate',
    pay_dow       TINYINT(2)   NOT NULL DEFAULT 5 COMMENT 'DAYOFWEEK hari bayar, 5 = Kamis',
    sumber        VARCHAR(20)  NOT NULL DEFAULT 'AUTO' COMMENT 'AUTO = dihitung sistem, MANUAL = disetel orang',
    created_by    VARCHAR(100) NULL,
    created_date  DATETIME     NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_ppa_no_kbon (no_kbon),
    KEY ix_ppa_plan_awal (plan_awal)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ---------------------------------------------------------------------------
-- BAGIAN 2 - ISI AWALNYA
--
-- Diisi untuk voucher yang SUDAH DISETUJUI dan punya jatuh tempo. Sengaja
-- TIDAK disaring "masih outstanding": layar Create memang hanya menampilkan
-- yang outstanding, tapi kalau tabel ini ikut disaring, voucher yang baru
-- jadi outstanding belakangan tidak punya rencana awal sama sekali.
--
-- INSERT IGNORE + kunci unik = baris yang sudah ada TIDAK ditimpa.
-- ---------------------------------------------------------------------------
INSERT IGNORE INTO ap_payment_plan_awal
    (no_kbon, duedate, plan_awal, pay_dow, sumber, created_by, created_date)
SELECT k.no_kbon,
       k.tgl_tempo,
       DATE_ADD(k.tgl_tempo, INTERVAL (5 - DAYOFWEEK(k.tgl_tempo)) DAY),
       5, 'AUTO', 'MIGRASI', NOW()
FROM kontrabon_h k
WHERE k.status IN ('Approved','FIRST APPROVED','SECOND APPROVED')
  AND k.tgl_tempo IS NOT NULL AND k.tgl_tempo <> '0000-00-00';

-- Voucher saldo awal & cicilan juga ikut - laporan proyeksi memuat keduanya,
-- jadi rencana awalnya tidak boleh kosong.
INSERT IGNORE INTO ap_payment_plan_awal
    (no_kbon, duedate, plan_awal, pay_dow, sumber, created_by, created_date)
SELECT s.no_kbon,
       s.tgl_tempo,
       DATE_ADD(s.tgl_tempo, INTERVAL (5 - DAYOFWEEK(s.tgl_tempo)) DAY),
       5, 'AUTO', 'MIGRASI', NOW()
FROM ap_saldo_payment_voucher s
WHERE s.tgl_tempo IS NOT NULL AND s.tgl_tempo <> '0000-00-00';

INSERT IGNORE INTO ap_payment_plan_awal
    (no_kbon, duedate, plan_awal, pay_dow, sumber, created_by, created_date)
SELECT i.no_kbon_det,
       i.tgl_tempo,
       DATE_ADD(i.tgl_tempo, INTERVAL (5 - DAYOFWEEK(i.tgl_tempo)) DAY),
       5, 'AUTO', 'MIGRASI', NOW()
FROM kontrabon_h_installment_detail i
WHERE i.tgl_tempo IS NOT NULL AND i.tgl_tempo <> '0000-00-00';


-- ---------------------------------------------------------------------------
-- BAGIAN 3 - PERIKSA HASILNYA
-- ---------------------------------------------------------------------------

-- 3a. Berapa baris, dan dari mana.
SELECT sumber, COUNT(*) AS jml, MIN(plan_awal) AS paling_awal, MAX(plan_awal) AS paling_akhir
FROM ap_payment_plan_awal GROUP BY sumber;

-- 3b. PENJAGA UTAMA. Harapan: TIDAK ADA BARIS.
--     Setiap plan_awal WAJIB hari Kamis, dan WAJIB berada di minggu
--     Minggu-Sabtu yang sama dgn duedate-nya.
SELECT no_kbon, duedate, DAYNAME(duedate) AS hari_due,
       plan_awal, DAYNAME(plan_awal) AS hari_rencana
FROM ap_payment_plan_awal
WHERE DAYOFWEEK(plan_awal) <> 5
   OR DATE_SUB(duedate,   INTERVAL DAYOFWEEK(duedate) - 1 DAY)
   <> DATE_SUB(plan_awal, INTERVAL DAYOFWEEK(plan_awal) - 1 DAY);

-- 3c. Harapan: TIDAK ADA BARIS. Tidak boleh ada voucher kembar.
SELECT no_kbon, COUNT(*) n FROM ap_payment_plan_awal GROUP BY no_kbon HAVING n > 1;

-- 3d. Voucher disetujui yang BELUM punya rencana awal (mis. jatuh temponya
--     kosong). Boleh ada, tapi perlu diketahui jumlahnya.
SELECT COUNT(*) AS approved_tanpa_rencana_awal
FROM kontrabon_h k
LEFT JOIN ap_payment_plan_awal a ON a.no_kbon = k.no_kbon
WHERE k.status IN ('Approved','FIRST APPROVED','SECOND APPROVED')
  AND a.no_kbon IS NULL;


-- ---------------------------------------------------------------------------
-- KALAU PERLU DIBATALKAN:
--   DROP TABLE ap_payment_plan_awal;
--
-- MENAMBAH voucher baru belakangan: jalankan ulang Bagian 2 saja. Berkat
-- INSERT IGNORE, rencana yang sudah diperbarui lewat menu PPU TIDAK akan
-- tertimpa kembali ke hari Kamis.
-- ---------------------------------------------------------------------------
