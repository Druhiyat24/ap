/* ============================================================================
   GABUNGAN SQL PRODUKSI - 19 November 2026
   Basis data: signalbit_erp @ 10.10.5.12

   SATU BERKAS, TIGA BAGIAN. Berkas-berkas aslinya dibiarkan di repo sebagai
   riwayat, tetapi JANGAN dijalankan sendiri-sendiri lagi - cukup yang ini.

       BAGIAN A  kolom `jenis` di ap_pvbiaya_plan_det   -> BELUM dijalankan
       BAGIAN B  kategori arus kas PV EXIM November      -> SUDAH dijalankan
       BAGIAN C  jurnal dobel PAY/LP//1126/0000x         -> SUDAH dijalankan

   KEADAAN PRODUKSI SUDAH DIPERIKSA (baca-saja, 19 Nov 2026 pagi):
     - kolom `jenis` BELUM ada                                   -> perlu
     - 38 PV EXIM November sudah berkategori 34, 0 yang kosong   -> beres
     - sb_list_journal sudah bersih dari 'PAY/LP//%'             -> beres
     - tabel bak20261118_pv_exim_cash_flow & bak20261118_sb_journal_paylp
       dua-duanya SUDAH ADA, jadi kedua perbaikan itu memang sudah jalan.

   BAGIAN B & C TETAP DISERTAKAN, tetapi berpagar: kalau datanya memang sudah
   benar, keduanya TIDAK menyentuh satu baris pun. Gunanya kalau basis data
   dikembalikan dari cadangan lama - supaya tidak perlu mencari berkas
   lamanya lagi.

   AMAN DIJALANKAN ULANG. Tidak ada satu pun DROP TABLE. Satu-satunya
   perubahan struktur adalah menambah SATU kolom di Bagian A.
   ============================================================================ */

SELECT 'sebelum' AS bagian, @@hostname AS server, DATABASE() AS basis_data, NOW() AS waktu;


/* ============================================================================
   BAGIAN A - KOLOM `jenis` DI ap_pvbiaya_plan_det   (INI YANG BELUM JALAN)

   KENAPA. Menu Update Payment Date sekarang menjadwalkan bukan hanya PV
   Biaya, tetapi juga DP, CBD, dan FTR CBD/DP yang cara bayarnya Cash.
   Ketiganya disimpan di tabel rencana yang sama, tetapi keterangan
   pendampingnya ada di tabel yang BERBEDA:

       Biaya             -> tbl_pv_h        (rekening pembayar + kategori)
       DP                -> kontrabon_h_dp
       CBD               -> kontrabon_h_cbd
       FTR-CBD / FTR-DP  -> ftr_cbd / ftr_dp  (dibayar dari kas kecil)

   Tanpa kolom penanda, laporan tidak tahu harus mencari ke tabel yang mana.

   KALAU BELUM DIJALANKAN APLIKASINYA TETAP HIDUP - kodenya menanyakan dulu
   apakah kolom ini ada (SHOW COLUMNS). Yang terjadi: rencana DP/CBD/FTR tidak
   bisa diselesaikan ke rekening & kategorinya, jadi jatuh ke baris
   "Pengeluaran Lain-lain" di Projection Report. Sudah diuji dgn kolomnya
   sengaja dibuang.
   ============================================================================ */

SELECT 'A. sebelum' AS bagian, COUNT(*) AS kolom_jenis_ada, '0 = belum' AS harapan
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME   = 'ap_pvbiaya_plan_det'
   AND COLUMN_NAME  = 'jenis';

/* ALTER TABLE tidak punya "IF NOT EXISTS" untuk kolom di MySQL 5.x, jadi
   dipagari pernyataan siap pakai - dgn begitu berkas ini tetap aman
   dijalankan dua kali. */
SET @ada = (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = 'ap_pvbiaya_plan_det'
               AND COLUMN_NAME  = 'jenis');

SET @sql = IF(@ada = 0,
    "ALTER TABLE `ap_pvbiaya_plan_det`
       ADD COLUMN `jenis` varchar(10) NOT NULL DEFAULT 'Biaya' AFTER `no_pv`",
    "SELECT 'kolom jenis sudah ada - tidak ada yang diubah' AS catatan");
PREPARE j FROM @sql;
EXECUTE j;
DEALLOCATE PREPARE j;

/* Baris LAMA semuanya PV Biaya - menu ini baru bisa menjadwalkan DP/CBD/FTR
   sesudah perubahan 18-19 Nov 2026. Nilai bawaannya sudah 'Biaya', tetapi
   diisi ulang di sini supaya baris yang (entah bagaimana) kosong tidak
   tertinggal. */
UPDATE `ap_pvbiaya_plan_det` SET `jenis` = 'Biaya'
 WHERE `jenis` IS NULL OR `jenis` = '';


/* ============================================================================
   BAGIAN B - KATEGORI ARUS KAS PV EXIM NOVEMBER 2026   (sudah jalan)

   Seluruh 249 PV EXIM dulu tidak punya kategori arus kas, karena form EXIM
   tidak punya pilihan kategori sama sekali dan INSERT-nya memang tidak
   menyebut kolom id_cash_flow. Akibatnya di Projection Report uangnya jatuh
   ke baris "Pengeluaran Lain-lain", bukan "Pengeluaran Pembayaran Biaya
   Export & Import". Kodenya sudah diperbaiki (create + edit), jadi PV EXIM
   BARU tidak akan kosong lagi.

   Yang diperbaiki HANYA bulan berjalan - pv_date 1 s/d 30 November 2026,
   38 PV. Yang Agustus s/d Oktober (211 PV) DIBIARKAN atas keputusan pemakai,
   karena sudah terbayar dan laporan realisasinya memakai kategori yang
   tersimpan di dokumen Bank Out-nya sendiri.
   ============================================================================ */

SET @cf_exim = (SELECT id FROM master_cash_flow
                 WHERE show_subcategory = 'EXPORT & IMPORT'
                   AND type_cashflow = 'Cash Out'
                   AND status = 'Y'
                 ORDER BY id ASC LIMIT 1);

SELECT 'B. sebelum' AS bagian, @cf_exim AS id_kategori, '34' AS harapan,
       (SELECT COUNT(*) FROM tbl_pv_h
         WHERE pv_form_type = 'EXIM'
           AND pv_date BETWEEN '2026-11-01' AND '2026-11-30'
           AND (id_cash_flow IS NULL OR id_cash_flow = 0)) AS masih_kosong,
       '0 kalau sudah jalan' AS catatan;

CREATE TABLE IF NOT EXISTS `bak20261118_pv_exim_cash_flow` (
  `no_pv` varchar(50) NOT NULL,
  `id_cash_flow_lama` int(11) DEFAULT NULL,
  `disalin` datetime DEFAULT NULL,
  PRIMARY KEY (`no_pv`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `bak20261118_pv_exim_cash_flow` (no_pv, id_cash_flow_lama, disalin)
SELECT h.no_pv, h.id_cash_flow, NOW()
  FROM `tbl_pv_h` h
 WHERE h.pv_form_type = 'EXIM'
   AND h.pv_date BETWEEN '2026-11-01' AND '2026-11-30'
   AND (h.id_cash_flow IS NULL OR h.id_cash_flow = 0)
   AND NOT EXISTS (SELECT 1 FROM `bak20261118_pv_exim_cash_flow` b WHERE b.no_pv = h.no_pv);

/* Syarat @cf_exim IS NOT NULL penting: kalau baris masternya tidak ketemu,
   UPDATE ini tidak menyentuh satu baris pun - BUKAN mengisi NULL, yang justru
   cacat yang sedang diperbaiki. */
UPDATE `tbl_pv_h`
   SET id_cash_flow = @cf_exim
 WHERE @cf_exim IS NOT NULL
   AND pv_form_type = 'EXIM'
   AND pv_date BETWEEN '2026-11-01' AND '2026-11-30'
   AND (id_cash_flow IS NULL OR id_cash_flow = 0);


/* ============================================================================
   BAGIAN C - JURNAL DOBEL PAY/LP//1126/0000x   (sudah jalan)

   18 Nov 2026 pagi, empat dokumen Payment tersimpan bernomor cacat
   PAY/LP//1126/00007 s/d 00010 - ruas profit center-nya kosong. Nomornya
   kemudian dibetulkan langsung di basis data, TETAPI di sb_list_journal
   tertinggal DUA-DUANYA: 8 baris nomor lama DAN 8 baris nomor baru, isi
   persis sama - jadi keempat pembayaran terhitung DUA KALI.

   Kodenya sudah diperbaiki di tiga lapis, termasuk penjaga server yang
   MENOLAK menyimpan tanpa profit center, jadi nomor cacat tidak bisa lahir
   lagi.

   Baris lama DIHAPUS, bukan diganti namanya - kembarannya yang bernomor benar
   sudah ada. Yang dihapus HANYA yang benar-benar punya kembaran sama persis
   (nomor, COA, debit, kredit, detik pembuatan). Baris lama yang tidak punya
   kembaran DIBIARKAN dan akan terlihat di pemeriksaan akhir - lebih baik
   tertinggal dan kelihatan daripada hilang diam-diam.
   ============================================================================ */

SELECT 'C. sebelum' AS bagian,
       (SELECT COUNT(*) FROM sb_list_journal WHERE no_journal LIKE 'PAY/LP//%') AS baris_cacat,
       '0 kalau sudah jalan' AS harapan;

CREATE TABLE IF NOT EXISTS `bak20261118_sb_journal_paylp` LIKE `sb_list_journal`;

INSERT INTO `bak20261118_sb_journal_paylp`
SELECT * FROM `sb_list_journal` j
 WHERE j.no_journal LIKE 'PAY/LP//1126/%'
   AND NOT EXISTS (SELECT 1 FROM `bak20261118_sb_journal_paylp` b WHERE b.id = j.id);

DELETE lama
  FROM `sb_list_journal` lama
  JOIN `sb_list_journal` baru
    ON baru.no_journal = REPLACE(lama.no_journal, 'PAY/LP//', 'PAY/LP/NAG/')
   AND baru.no_coa      = lama.no_coa
   AND baru.debit_idr   = lama.debit_idr
   AND baru.credit_idr  = lama.credit_idr
   AND baru.create_date = lama.create_date
 WHERE lama.no_journal LIKE 'PAY/LP//1126/%';


/* ============================================================================
   PEMERIKSAAN AKHIR - kolom "hasil" harus sama dgn kolom "harapan"
   ============================================================================ */
SELECT 'A. kolom jenis ada' AS yang_dicek, COUNT(*) AS hasil, '1' AS harapan
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ap_pvbiaya_plan_det'
   AND COLUMN_NAME = 'jenis'
UNION ALL
SELECT 'A. baris rencana tanpa penanda', COUNT(*), '0'
  FROM `ap_pvbiaya_plan_det` WHERE `jenis` IS NULL OR `jenis` = ''
UNION ALL
SELECT 'B. EXIM November tanpa kategori', COUNT(*), '0'
  FROM tbl_pv_h WHERE pv_form_type = 'EXIM'
   AND pv_date BETWEEN '2026-11-01' AND '2026-11-30'
   AND (id_cash_flow IS NULL OR id_cash_flow = 0)
UNION ALL
SELECT 'B. EXIM November berkategori 34', COUNT(*), '38'
  FROM tbl_pv_h WHERE pv_form_type = 'EXIM'
   AND pv_date BETWEEN '2026-11-01' AND '2026-11-30' AND id_cash_flow = @cf_exim
UNION ALL
SELECT 'B. EXIM bulan lain - HARUS tetap kosong', COUNT(*), '211'
  FROM tbl_pv_h WHERE pv_form_type = 'EXIM'
   AND pv_date NOT BETWEEN '2026-11-01' AND '2026-11-30'
   AND (id_cash_flow IS NULL OR id_cash_flow = 0)
UNION ALL
SELECT 'B. PV Regular tanpa kategori - tidak disentuh', COUNT(*), '31'
  FROM tbl_pv_h WHERE pv_form_type = 'Regular' AND (id_cash_flow IS NULL OR id_cash_flow = 0)
UNION ALL
SELECT 'C. baris jurnal bernomor cacat tersisa', COUNT(*), '0'
  FROM sb_list_journal WHERE no_journal LIKE 'PAY/LP//%'
UNION ALL
SELECT 'C. baris jurnal bernomor benar (4 dokumen)', COUNT(*), '8'
  FROM sb_list_journal
 WHERE no_journal IN ('PAY/LP/NAG/1126/00007','PAY/LP/NAG/1126/00008',
                      'PAY/LP/NAG/1126/00009','PAY/LP/NAG/1126/00010');

/* Isi kolom penanda sesudah Bagian A - semuanya harus 'Biaya' untuk sekarang;
   DP/CBD/FTR-CBD/FTR-DP baru muncul sesudah ada yang dijadwalkan. */
SELECT `jenis`, COUNT(*) AS baris FROM `ap_pvbiaya_plan_det` GROUP BY `jenis`;
