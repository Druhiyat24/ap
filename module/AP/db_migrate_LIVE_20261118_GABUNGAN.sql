/* ============================================================================
   MIGRASI PRODUKSI - GABUNGAN  (dirakit 18 November 2026)
   Basis data: signalbit_erp @ 10.10.5.12

   PAKAI BERKAS INI. Isinya dua berkas migrasi yang masih tertunda:
       db_migrate_LIVE_20261116_pvbiaya_payment_date.sql
       db_migrate_LIVE_20261116_cash_flow_report.sql
   Keduanya TIDAK perlu dijalankan lagi secara terpisah.

   DIJALANKAN SEKALI, SEBELUM kode terbaru ditarik ke server. Tanpa ini tiga
   menunya tidak muncul untuk siapa pun dan layarnya galat karena tabelnya
   belum ada.

   ISI BERKAS INI SUDAH DISARING. Produksi diperiksa langsung (baca-saja)
   18 Nov 2026 sebelum berkas ini dirakit:

     SUDAH ADA di produksi - TIDAK ikut dibawa ke sini:
       - tabel ap_cf_projection + menurole 148 'Bank Projection Other'
         (berkas db_migrate_LIVE_20261115_GABUNGAN.sql sudah jalan)
       - tabel project_erd / project_mockup / project_review / project_workflow
         (berkas db_migrate_LIVE_20261117_project_studio.sql sudah jalan)
       - menurole sampai id 149; id terbesar di produksi = 149

     BELUM ADA - inilah yang ada di bawah:
       BAGIAN A  ap_pvbiaya_plan_h / _det  + menurole 150
                 menu Bank > Projection > Update Payment Date
       BAGIAN B  ap_cf_bank_setting, ap_cf_opening, ap_cf_opening_log,
                 ap_cf_history_h, ap_cf_history_det, ap_cf_history_src
                 + menurole 151 & 152
                 menu Bank > Report > Projection Report & History Projection

   URUTANNYA SENGAJA: BAGIAN A duluan. Laporan Cash Flow di BAGIAN B membaca
   ap_pvbiaya_plan_h/_det; kalau tabel itu belum ada, baris biayanya nol dan
   laporannya memberi tahu - tidak gagal, tapi angkanya kurang.

   ID MENU 150/151/152 MASIH KOSONG di produksi (diperiksa 18 Nov 2026). Kalau
   sampai sudah terpakai menu lain waktu berkas ini dijalankan, JANGAN
   dipaksa - ganti angkanya di berkas ini DAN di module/menu.php sekaligus.

   AMAN DIJALANKAN ULANG: semua tabel CREATE TABLE IF NOT EXISTS, semua
   insert berpagar WHERE NOT EXISTS. Tidak ada satu pun DROP/ALTER/UPDATE di
   berkas ini - tidak ada data lama yang bisa tersentuh.

   SESUDAH DIJALANKAN: tandai "Mark as run on production" di ERD project
   90/106/107 (menu Project 3D).
   ============================================================================ */


/* ============================================================================
   BAGIAN 0 - LIHAT DULU (tidak mengubah apa pun)

   Jalankan bagian ini lebih dulu dan baca hasilnya. Semua angkanya HARUS 0;
   kalau ada yang 1, bagian itu sudah pernah jalan - berkas ini tetap aman
   diteruskan, tapi berarti ada yang sudah dikerjakan orang lain.
   ============================================================================ */
SELECT 'keadaan sebelum' AS bagian, @@hostname AS server, DATABASE() AS basis_data;

SELECT 'tabel ap_pvbiaya_plan_h + _det' AS yang_dicek, COUNT(*) AS sudah_ada, '0' AS harapan
  FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name IN ('ap_pvbiaya_plan_h','ap_pvbiaya_plan_det')
UNION ALL
SELECT 'tabel ap_cf_* (6 tabel)', COUNT(*), '0' FROM information_schema.tables
 WHERE table_schema = DATABASE()
   AND table_name IN ('ap_cf_bank_setting','ap_cf_opening','ap_cf_opening_log',
                      'ap_cf_history_h','ap_cf_history_det','ap_cf_history_src')
UNION ALL
SELECT 'menurole 3 menu baru', COUNT(*), '0' FROM menurole
 WHERE menu IN ('Bank Projection Payment Date','Bank Projection Cash Flow','Bank Projection History')
UNION ALL
SELECT 'id 150/151/152 dipakai menu LAIN', COUNT(*), '0' FROM menurole
 WHERE id IN (150,151,152)
   AND menu NOT IN ('Bank Projection Payment Date','Bank Projection Cash Flow','Bank Projection History');


/* ############################################################################
   BAGIAN A - MENU Bank > Projection > Update Payment Date

   PV Biaya (type_pv 'Biaya' di Bank Out) belum punya TANGGAL RENCANA BAYAR
   yang berdiri sendiri. Kolom tbl_pv_h.pay_date memang ada, tapi dipasang
   saat PV dibuat dan dipakai dokumen itu sendiri - bukan rencana yang boleh
   digeser-geser. Menu ini menyimpan rencananya TERPISAH, persis pola Update
   Payment Plan milik AP, supaya Cash Flow Projection punya tanggal yang bisa
   diandalkan. TIDAK ADA satu pun kolom tbl_pv_h yang disentuh.
   ############################################################################ */
/* ---------------------------------------------------------------------------
   1. KEPALA DOKUMEN

   Satu kali Save = satu dokumen, berapa pun barisnya. Nomornya PPU-E/<mmyy>/
   <00001>, dibuat di server dgn GET_LOCK supaya dua orang yang menyimpan
   bersamaan tidak memakai nomor yang sama.

   plan_date di sini tanggal DOKUMEN sekaligus cadangan untuk baris yang
   tidak membawa tanggalnya sendiri. Yang dibaca laporan adalah tanggal PER
   BARIS di tabel det - sama seperti ap_payment_plan.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_pvbiaya_plan_h` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `doc_number`   VARCHAR(100) NOT NULL,
  `plan_date`    DATE         DEFAULT NULL COMMENT 'tanggal dokumen, cadangan utk baris tanpa tanggal',
  `keterangan`   VARCHAR(255) DEFAULT NULL,
  `status`       VARCHAR(20)  NOT NULL DEFAULT 'POST' COMMENT 'POST / CANCEL',
  `created_by`   VARCHAR(100) DEFAULT NULL,
  `created_date` DATETIME     DEFAULT NULL,
  `cancel_by`    VARCHAR(100) DEFAULT NULL,
  `cancel_date`  DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_doc` (`doc_number`),
  KEY `idx_status` (`status`),
  KEY `idx_plan_date` (`plan_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   2. BARIS DOKUMEN

   KUNCINYA (no_pv + profit_center), BUKAN no_pv saja. Bank Out memang
   mengelompokkan PV Biaya per profit center - satu PV bisa punya baris NAG
   dan NAK sekaligus dgn sisa masing-masing - jadi rencananya pun harus per
   pasangan itu. Kalau cuma no_pv, dua PC di satu PV akan saling memadamkan.

   status 'Y' = rencana yang BERLAKU, 'N' = sudah digantikan dokumen yang
   lebih baru atau dokumennya dibatalkan. Tanggal lamanya TIDAK dihapus -
   barisnya tetap ada sbg riwayat, yang berubah cuma penandanya.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_pvbiaya_plan_det` (
  `id`            BIGINT(20)    NOT NULL AUTO_INCREMENT,
  `doc_number`    VARCHAR(100)  NOT NULL,
  `no_pv`         VARCHAR(200)  NOT NULL,
  `profit_center` VARCHAR(50)   NOT NULL DEFAULT '',
  `plan_date`     DATE          DEFAULT NULL COMMENT 'tanggal rencana bayar - INI yang dibaca laporan',
  `curr`          VARCHAR(10)   DEFAULT NULL,
  `amount`        DECIMAL(20,2) NOT NULL DEFAULT 0.00 COMMENT 'sisa outstanding saat rencana dibuat',
  `keterangan`    VARCHAR(255)  DEFAULT NULL,
  `status`        CHAR(1)       NOT NULL DEFAULT 'Y' COMMENT 'Y = berlaku, N = digantikan/dibatalkan',
  PRIMARY KEY (`id`),
  KEY `idx_doc` (`doc_number`),
  KEY `idx_pv` (`no_pv`, `profit_center`),
  KEY `idx_plan_date` (`plan_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   3. HAK AKSES MENU

   id 150 dipakai KARENA DIPANGGIL LANGSUNG di module/menu.php (pola yang
   sama dgn menu lain di berkas itu). Diperiksa 16 Nov 2026: id terbesar di
   menurole produksi = 149 dan 150 KOSONG. Kalau ternyata sudah terpakai menu
   lain, JANGAN dipaksa - ganti id di sini DAN di module/menu.php sekaligus.

   Periksa dulu:  SELECT * FROM menurole WHERE id = 150;
   --------------------------------------------------------------------------- */
INSERT INTO `menurole` (`id`, `menu`, `display_name`, `menu_group`, `status`, `profit_center`)
SELECT 150, 'Bank Projection Payment Date', 'Payment Plan - Update Payment Date', 'Bank', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM `menurole` WHERE `menu` = 'Bank Projection Payment Date');

/* Satu baris contoh untuk indro. Pemakai lain diberikan lewat layar
   User Access seperti biasa - TIDAK perlu SQL. */
INSERT INTO `useraccess` (`username`, `fullname`, `menu`, `create_date`, `create_user`)
SELECT 'indro', 'Indro', 'Bank Projection Payment Date', NOW(), 'migrasi'
WHERE NOT EXISTS (
  SELECT 1 FROM `useraccess` WHERE `username` = 'indro' AND `menu` = 'Bank Projection Payment Date'
);

/* ############################################################################
   BAGIAN B - MENU Bank > Report > Projection Report & History Projection

   Laporan Cash Flow Projection (Daily/Weekly/Monthly), potret Save History,
   dan data sumber potretnya.
   ############################################################################ */
/* ---------------------------------------------------------------------------
   1. SETELAN PER REKENING + SALINAN BEKU TIAP DOKUMEN

   min_balance dipakai simulasi pinjaman (saldo minimum yang harus disisakan
   di rekening); limit_amount menimpa b_masterbank.fac_limit kalau diisi.
   Di Excel semuanya masih 0.

   doc_number: KOSONG/NULL = setelan yang sedang berjalan. Terisi = SALINAN
   BEKU milik satu dokumen history, supaya laporan lama bisa dibuka lagi
   dengan setelan yang berlaku saat itu - pola yang sama dgn
   ap_projection_history_det. Baris beku itulah yang membawa saldo awal yang
   benar-benar dipakai dokumen tsb:
     saldo_tarik = hasil hitung sistem saat itu
     saldo_pakai = angka yang dipakai (sama dgn saldo_tarik kalau tidak
                   ada isian tangan)
     manual_by / manual_date = siapa mengetik angka tangannya dan kapan,
                   DISALIN dari ap_cf_opening - bukan nama orang yang menekan
                   Save. Tanpa dua kolom ini, dokumen beku tidak bisa
                   menjawab "angka ini dari mana".
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_cf_bank_setting` (
  `id`           INT(11)        NOT NULL AUTO_INCREMENT,
  `doc_number`   VARCHAR(50)    DEFAULT NULL COMMENT 'kosong = setelan berjalan',
  `akun`         VARCHAR(150)   NOT NULL     COMMENT 'nomor rekening / COA kas',
  `curr`         VARCHAR(10)    DEFAULT 'IDR',
  `saldo_tarik`  DECIMAL(20,2)  DEFAULT NULL COMMENT 'hasil hitung sistem',
  `saldo_pakai`  DECIMAL(20,2)  DEFAULT NULL COMMENT 'NULL = ikut saldo_tarik',
  `manual_by`    VARCHAR(100)   DEFAULT NULL COMMENT 'penulis angka tangan (salinan dari ap_cf_opening)',
  `manual_date`  DATETIME       DEFAULT NULL COMMENT 'kapan angka tangan itu ditulis',
  `min_balance`  DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  `limit_amount` DECIMAL(20,2)  DEFAULT NULL COMMENT 'NULL = ikut b_masterbank.fac_limit',
  `rate`         DECIMAL(20,4)  DEFAULT NULL,
  `keterangan`   VARCHAR(255)   DEFAULT NULL,
  `update_by`    VARCHAR(100)   DEFAULT NULL,
  `update_date`  DATETIME       DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_doc_akun` (`doc_number`, `akun`),
  KEY `idx_akun` (`akun`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   1b. SALDO AWAL YANG DIKETIK TANGAN - PER TANGGAL

   Saldo awal laporan ini DIHITUNG dari b_saldoawal_bank + b_reportbank
   (rumus yang sama dgn menu Report Bank). Diuji per 31 Okt 2026, sembilan
   dari sepuluh rekening sama persis dgn lembar Balance di Excel - yang satu
   (BNI 442-244-2000) beda Rp 635.159.500 karena di Excel memang dinaikkan
   dengan tangan, dan angka itu tidak ada di b_reportbank sama sekali. Itulah
   yang ditampung tabel ini.

   KOLOM `tanggal` ADALAH INTI TABEL INI, bukan hiasan. Saldo awal selalu
   melekat pada SATU tanggal ("saldo awal per 1 Nov"), jadi isian tangan
   HARUS bertanggal. Kalau tidak, angka yang diketik untuk November akan ikut
   terpakai waktu laporan dijalankan untuk Desember - dan salahnya tidak
   kentara, karena laporannya tetap tampil rapi. Mesin hanya memakai baris
   yang tanggalnya SAMA PERSIS dgn tanggal From laporan.

   Hanya tanggal PERTAMA yang bisa diketik. Hari-hari berikutnya adalah
   hitungan (saldo awal hari ini = saldo akhir kemarin), jadi tidak ada yang
   perlu - dan tidak boleh - diketik di sana.

   saldo_sistem disimpan sbg CATATAN: berapa hitungan sistem saat angka
   tangan itu ditulis. Dgn begitu koreksinya kelihatan sebagai koreksi
   (sistem sekian, dipakai sekian), bukan menimpa diam-diam.

   KEDUA KOLOM SALDO DISIMPAN DALAM RUPIAH, termasuk untuk rekening USD -
   itu yang diketik pemakai dan itu yang dibaca orang. Mesin laporan yang
   membaginya dgn kurs saat memakai. Sempat dicoba sebaliknya (disimpan
   dalam mata uang rekening, dibagi kurs waktu menyimpan) dan hasilnya
   melenceng: DECIMAL(20,2) memotong angka USD, lalu dikalikan kurs lagi
   sehingga angka yang dibaca kembali beda puluhan rupiah dari yang
   diketik. Dibagi di saat dipakai, pembulatannya tidak pernah tersimpan.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_cf_opening` (
  `id`           INT(11)        NOT NULL AUTO_INCREMENT,
  `tanggal`      DATE           NOT NULL COMMENT 'tanggal saldo awal - sama dgn tanggal From laporan',
  `akun`         VARCHAR(150)   NOT NULL COMMENT 'nomor rekening / COA kas',
  `curr`         VARCHAR(10)    DEFAULT 'IDR',
  `saldo_sistem` DECIMAL(20,2)  DEFAULT NULL COMMENT 'RUPIAH - hitungan sistem saat angka ini ditulis',
  `saldo_manual` DECIMAL(20,2)  NOT NULL     COMMENT 'RUPIAH - angka yang dipakai laporan',
  `keterangan`   VARCHAR(255)   DEFAULT NULL,
  `update_by`    VARCHAR(100)   DEFAULT NULL,
  `update_date`  DATETIME       DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_tgl_akun` (`tanggal`, `akun`),
  KEY `idx_tanggal` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   1c. RIWAYAT PERUBAHAN SALDO AWAL

   Satu baris per perubahan, TIDAK PERNAH dihapus atau ditimpa - tabel di
   atas cuma menyimpan keadaan TERAKHIR, jadi tanpa tabel ini pertanyaan
   "dulu angkanya berapa, siapa yang mengubah" tidak bisa dijawab.

   nilai_lama & nilai_baru SELALU BERISI ANGKA, tidak pernah dikosongkan:
   nilai_lama = yang berlaku sebelum perubahan (isian tangan sebelumnya,
   atau hitungan sistem kalau belum pernah diketik), nilai_baru = yang
   berlaku sesudahnya (angka tangan, atau hitungan sistem kalau aksinya
   RESET). Sempat dibuat NULL dan ditampilkan sbg kata "system"; itu
   memaksa pembacanya menebak angkanya berapa, dan hitungan sistem hari itu
   bisa saja sudah berubah waktu riwayatnya dibaca lagi. Kolom aksi
   (SET/RESET) yang membedakan maksudnya.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_cf_opening_log` (
  `id`           BIGINT(20)     NOT NULL AUTO_INCREMENT,
  `tanggal`      DATE           NOT NULL,
  `akun`         VARCHAR(150)   NOT NULL,
  `aksi`         VARCHAR(10)    NOT NULL DEFAULT 'SET' COMMENT 'SET / RESET',
  `nilai_lama`   DECIMAL(20,2)  DEFAULT NULL COMMENT 'RUPIAH - yang berlaku sebelum perubahan',
  `nilai_baru`   DECIMAL(20,2)  DEFAULT NULL COMMENT 'RUPIAH - yang berlaku sesudah perubahan',
  `saldo_sistem` DECIMAL(20,2)  DEFAULT NULL,
  `keterangan`   VARCHAR(255)   DEFAULT NULL,
  `user`         VARCHAR(100)   DEFAULT NULL,
  `waktu`        DATETIME       DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tgl_akun` (`tanggal`, `akun`),
  KEY `idx_waktu` (`waktu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   2. HISTORY - potret laporan yang dibekukan lewat tombol Save History

   Satu dokumen = satu kali Save, bernomor CF-PPR/MMYY/NNNNN. Yang disimpan
   HASIL JADINYA, bukan sumbernya: laporan ini dirakit dari enam sumber yang
   terus bergerak (rencana bayar AP, potret AR, PV biaya, book transfer,
   saldo bank, kurs), jadi menyimpan sumbernya saja berarti potretnya bisa
   berubah sendiri besok - yang justru dihindari.

   ap_cf_history_det menyimpan SATU BARIS per (baris laporan x kolom).
   Kolomnya tanggal (Daily) atau rekening (Weekly/Monthly) - dibedakan lewat
   dua kolom terpisah supaya tidak bisa tertukar:
     TOTAL          -> tanggal NULL, kolom NULL
     Daily          -> tanggal diisi, kolom NULL
     Weekly/Monthly -> tanggal NULL, kolom diisi kunci rekening
   Nilai NOL TIDAK disimpan - waktu dibaca lagi, baris yang tidak ada berarti
   nol. Itu memangkas sekitar 90% barisnya tanpa kehilangan apa pun.
   Kolom tanggal NULL = kolom TOTAL, dan baris TOTAL SELALU ditulis meski
   nilainya nol supaya susunan barisnya tetap utuh.

   Setelan rekening (saldo awal, min balance, limit, kurs) ikut dibekukan ke
   ap_cf_bank_setting dgn doc_number dokumen ini - itulah gunanya kolom
   doc_number di tabel tsb.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_cf_history_h` (
  `id`             INT(11)       NOT NULL AUTO_INCREMENT,
  `doc_number`     VARCHAR(50)   NOT NULL,
  `periode_dari`   DATE          NOT NULL,
  `periode_sampai` DATE          NOT NULL,
  `profit_center`  VARCHAR(20)   DEFAULT 'ALL',
  `bank`           VARCHAR(150)  DEFAULT 'ALL',
  `type`           VARCHAR(20)   DEFAULT 'daily',
  `rate`           DECIMAL(20,4) DEFAULT NULL,
  `ar_doc`         VARCHAR(50)   DEFAULT NULL COMMENT 'potret proyeksi AR yang dipakai',
  `jml_baris`      INT(11)       DEFAULT 0,
  `status`         VARCHAR(20)   NOT NULL DEFAULT 'ACTIVE',
  `created_by`     VARCHAR(100)  DEFAULT NULL,
  `created_date`   DATETIME      DEFAULT NULL,
  `cancel_by`      VARCHAR(100)  DEFAULT NULL,
  `cancel_date`    DATETIME      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_doc` (`doc_number`),
  KEY `idx_periode` (`periode_dari`, `periode_sampai`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ap_cf_history_det` (
  `id`         BIGINT(20)    NOT NULL AUTO_INCREMENT,
  `doc_number` VARCHAR(50)   NOT NULL,
  `blok`       VARCHAR(10)   NOT NULL DEFAULT 'report' COMMENT 'report / loan',
  `urut`       INT(11)       NOT NULL,
  `tipe`       VARCHAR(10)   DEFAULT NULL COMMENT 'sal/blok/grup/item/tot/tot2/net/pinj/sub',
  `no_urut`    VARCHAR(10)   DEFAULT NULL COMMENT 'nomor yang tampil di depan nama kategori',
  `label`      VARCHAR(200)  DEFAULT NULL,
  `tanggal`    DATE          DEFAULT NULL COMMENT 'Daily: tanggal kolomnya. NULL = TOTAL atau mode akun',
  `kolom`      VARCHAR(150)  DEFAULT NULL COMMENT 'Weekly/Monthly: NAMA rekening kolomnya. NULL = TOTAL atau mode tanggal',
  `kolom_urut` INT(11)       DEFAULT NULL COMMENT 'urutan kolom saat disimpan. NULL = kolom TOTAL',
  `nilai`      DECIMAL(20,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_doc` (`doc_number`, `blok`, `urut`),
  KEY `idx_doc_tgl` (`doc_number`, `tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   3. MENU

   menurole produksi = 150 terpakai Update Payment Date, 151 KOSONG. Kalau
   ternyata sudah terpakai menu lain, JANGAN dipaksa - ganti id di sini DAN
   di module/menu.php sekaligus.

   Periksa dulu:  SELECT * FROM menurole WHERE id = 151;
   --------------------------------------------------------------------------- */
INSERT INTO `menurole` (`id`, `menu`, `display_name`, `menu_group`, `status`, `profit_center`)
SELECT 151, 'Bank Projection Cash Flow', 'Projection Report', 'Bank', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM `menurole` WHERE `menu` = 'Bank Projection Cash Flow');

INSERT INTO `useraccess` (`username`, `fullname`, `menu`, `create_date`, `create_user`)
SELECT 'indro', 'indro', 'Bank Projection Cash Flow', NOW(), 'migrasi'
WHERE NOT EXISTS (
  SELECT 1 FROM `useraccess` WHERE `username` = 'indro' AND `menu` = 'Bank Projection Cash Flow'
);


/* ---------------------------------------------------------------------------
   4. MENU HISTORY - layar untuk MEMBACA potret yang disimpan Save History.

   Haknya TERPISAH dari laporannya (menurole 152): yang boleh melihat potret
   lama belum tentu yang boleh menjalankan proyeksinya, dan sebaliknya.

   Periksa dulu:  SELECT * FROM menurole WHERE id = 152;
   --------------------------------------------------------------------------- */
INSERT INTO `menurole` (`id`, `menu`, `display_name`, `menu_group`, `status`, `profit_center`)
SELECT 152, 'Bank Projection History', 'History Projection', 'Bank', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM `menurole` WHERE `menu` = 'Bank Projection History');

INSERT INTO `useraccess` (`username`, `fullname`, `menu`, `create_date`, `create_user`)
SELECT 'indro', 'indro', 'Bank Projection History', NOW(), 'migrasi'
WHERE NOT EXISTS (
  SELECT 1 FROM `useraccess` WHERE `username` = 'indro' AND `menu` = 'Bank Projection History'
);


/* ---------------------------------------------------------------------------
   5. ap_cf_history_src - DATA SUMBER potret (lembar pendukung di Excel)

   Berkas acuan Cash Flow Projection bukan cuma lembar angkanya: ada lembar
   rincian sumbernya (AR Existing, Book Transfer, Other Inflow, PV EXP,
   PV AP, Balance, Master CF). Rincian itu DIIKUTKAN KE DALAM POTRET, bukan
   dibangun ulang waktu Excel diunduh - potret harus tetap memperlihatkan
   keadaan saat Save ditekan, dan rincian yang dibangun ulang bisa sudah
   tidak cocok lagi dgn angka yang dibekukan.

   Satu baris = satu baris di salah satu lembar. Isinya disimpan sbg JSON di
   kolom `data`, bukan dipecah jadi puluhan kolom: tiap lembar susunannya
   beda-beda, dan susunan itu sudah ditentukan di cf_sumber_lib.php. JSON-nya
   selalu ASCII (karakter non-ASCII ditulis \uXXXX), jadi aman di latin1.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_cf_history_src` (
  `id`         BIGINT(20)  NOT NULL AUTO_INCREMENT,
  `doc_number` VARCHAR(50) NOT NULL,
  `jenis`      VARCHAR(20) NOT NULL COMMENT 'AR_EXISTING/BOOK_TRANSFER/OTHER_INFLOW/PV_EXP/PV_AP/BALANCE/MASTER_CF',
  `urut`       INT(11)     NOT NULL DEFAULT 0,
  `data`       TEXT        DEFAULT NULL COMMENT 'satu baris lembar, JSON',
  PRIMARY KEY (`id`),
  KEY `idx_doc` (`doc_number`, `jenis`, `urut`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* ============================================================================
   PEMERIKSAAN AKHIR - kolom "hasil" harus sama dgn kolom "harapan"
   ============================================================================ */
SELECT 'tabel ap_pvbiaya_plan_h + _det' AS yang_dicek, COUNT(*) AS hasil, '2' AS harapan
  FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name IN ('ap_pvbiaya_plan_h','ap_pvbiaya_plan_det')
UNION ALL
SELECT 'tabel ap_cf_* (6 tabel)', COUNT(*), '6' FROM information_schema.tables
 WHERE table_schema = DATABASE()
   AND table_name IN ('ap_cf_bank_setting','ap_cf_opening','ap_cf_opening_log',
                      'ap_cf_history_h','ap_cf_history_det','ap_cf_history_src')
UNION ALL
SELECT 'menurole 150 Update Payment Date', COUNT(*), '1' FROM menurole
 WHERE menu = 'Bank Projection Payment Date' AND id = 150 AND menu_group = 'Bank'
UNION ALL
SELECT 'menurole 151 Projection Report', COUNT(*), '1' FROM menurole
 WHERE menu = 'Bank Projection Cash Flow' AND id = 151 AND menu_group = 'Bank'
UNION ALL
SELECT 'menurole 152 History Projection', COUNT(*), '1' FROM menurole
 WHERE menu = 'Bank Projection History' AND id = 152 AND menu_group = 'Bank'
UNION ALL
SELECT 'useraccess 3 menu baru', COUNT(*), '3 atau lebih' FROM useraccess
 WHERE menu IN ('Bank Projection Payment Date','Bank Projection Cash Flow','Bank Projection History');

/* Daftar menu barunya, untuk dicocokkan dgn module/menu.php. */
SELECT id, menu, display_name, menu_group, status, profit_center
  FROM menurole WHERE id IN (150,151,152) ORDER BY id;
