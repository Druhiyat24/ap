/* ============================================================================
   MIGRASI PRODUKSI - CASH FLOW PROJECTION REPORT (Daily)
   Tanggal   : 16 November 2026
   Basis data: signalbit_erp

   Isinya:
     1. ap_cf_bank_setting - minimum balance & limit per rekening, sekaligus
        SALINAN BEKU setelan tiap dokumen history
     2. ap_cf_opening / ap_cf_opening_log - saldo awal yang diketik tangan,
        PER TANGGAL, berikut riwayat siapa mengubah apa dan kapan
     3. ap_cf_history_h / ap_cf_history_det - potret Save History
     4. menurole id 151 + useraccess - hak akses menunya
     5. ap_cf_history_src - data sumber potret (lembar pendukung Excel)

   AMAN DIJALANKAN ULANG: tabelnya CREATE TABLE IF NOT EXISTS dan insert-nya
   berpagar NOT EXISTS, jadi menjalankan dua kali tidak menggandakan apa pun.

   CATATAN: laporan ini JUGA membaca ap_pvbiaya_plan_h/det. Kalau
   db_migrate_LIVE_20261116_pvbiaya_payment_date.sql belum dijalankan, baris
   biayanya nol dan laporannya memberi tahu - bukan gagal.
   ============================================================================ */


/* ---------------------------------------------------------------------------
   PERIKSA DULU - jalankan bagian ini lebih dulu, lihat hasilnya.
   --------------------------------------------------------------------------- */
SELECT 'tabel ap_cf_bank_setting', COUNT(*) FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_cf_bank_setting'
UNION ALL
SELECT 'tabel ap_cf_opening', COUNT(*) FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_cf_opening'
UNION ALL
SELECT 'menurole Bank Projection Cash Flow', COUNT(*) FROM menurole
 WHERE menu = 'Bank Projection Cash Flow'
UNION ALL
SELECT 'menurole id 151 dipakai menu lain', COUNT(*) FROM menurole
 WHERE id = 151 AND menu <> 'Bank Projection Cash Flow';


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


/* ---------------------------------------------------------------------------
   PERIKSA SESUDAHNYA - ketiganya harus sesuai kolom "harusnya".
   --------------------------------------------------------------------------- */
SELECT 'tabel ap_cf_bank_setting', COUNT(*), '1' FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_cf_bank_setting'
UNION ALL
SELECT 'tabel ap_cf_opening + log', COUNT(*), '2' FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name IN ('ap_cf_opening','ap_cf_opening_log')
UNION ALL
SELECT 'menurole Bank Projection Cash Flow', COUNT(*), '1' FROM menurole
 WHERE menu = 'Bank Projection Cash Flow' AND id = 151 AND menu_group = 'Bank'
UNION ALL
SELECT 'useraccess Bank Projection Cash Flow', COUNT(*), '1 atau lebih' FROM useraccess
 WHERE menu = 'Bank Projection Cash Flow'
UNION ALL
SELECT 'menurole Bank Projection History', COUNT(*), '1' FROM menurole
 WHERE menu = 'Bank Projection History' AND id = 152
UNION ALL
SELECT 'tabel ap_cf_history_src', COUNT(*), '1' FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'ap_cf_history_src';
