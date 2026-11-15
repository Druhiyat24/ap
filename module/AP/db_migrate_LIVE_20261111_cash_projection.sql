/* ============================================================================
   MIGRASI PRODUKSI - MENU Cash > Projection > Other  (11 Nov 2026)

   Dijalankan SEKALI di basis data produksi (signalbit_erp) SEBELUM kode
   terbaru ditarik. Tanpa ini menunya tidak muncul dan halamannya galat
   karena tabelnya belum ada.

   Isinya tiga bagian:
     1. Tabel ap_cf_projection          - penyimpan ketiga jenis proyeksi
     2. menurole id 148                 - hak akses menunya
     3. useraccess                      - pemberian akses ke pemakai
     4. ap_cf_category                 - master kategori arus kas (Master CF)

   Seluruhnya ditulis supaya AMAN DIJALANKAN ULANG: tabelnya pakai
   CREATE TABLE IF NOT EXISTS, dua insert lainnya pakai WHERE NOT EXISTS.
   ============================================================================ */

/* ---------------------------------------------------------------------------
   1. TABEL
   SATU tabel untuk ketiga jenis, dibedakan kolom `jenis` - pola yang sama
   dgn Bank In (tbl_bankin_arcollection menampung AR Collection / Bank Out /
   None sekaligus). Kolom yang tidak dipakai suatu jenis dibiarkan NULL:

       BOOK TRANSFER : akun_source, akun_tujuan, pc_source, pc_tujuan,
                       intra_inter, amount_tujuan
       OTHER INFLOW  : nama + id_supplier (Customer Name), kategori
                       (Income Category), akun_tujuan (bank penerima), pc_tujuan
       PV EXPENSE    : nama + id_supplier (Supplier Name), kategori (Cost Category),
                       akun_tujuan (bank pembayar), pc_tujuan, tanggal
                       dibaca sbg Due Date

   Tujuh kolom jejak (status, created/cancel/approve) WAJIB ada sejak awal -
   diminta pemakai 11 Nov 2026. Approval belum dipakai; kolomnya sengaja
   sudah disiapkan supaya kalau nanti jadi, tidak perlu ubah struktur lagi.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_cf_projection` (
  `id`            INT(11)        NOT NULL AUTO_INCREMENT,
  `no_doc`        VARCHAR(50)    NOT NULL,
  `jenis`         VARCHAR(20)    NOT NULL COMMENT 'BOOK TRANSFER / OTHER INFLOW / PV EXPENSE',
  `tanggal`       DATE           DEFAULT NULL COMMENT 'Received Date, atau Due Date untuk PV Expense',
  `akun_source`   VARCHAR(150)   DEFAULT NULL,
  `akun_tujuan`   VARCHAR(150)   DEFAULT NULL COMMENT 'Destination / Bank',
  `pc_source`     VARCHAR(50)    DEFAULT NULL,
  `pc_tujuan`     VARCHAR(50)    DEFAULT NULL,
  `intra_inter`   VARCHAR(20)    DEFAULT NULL COMMENT 'Intra PC / Inter PC - Book Transfer saja',
  `nama`          VARCHAR(255)   DEFAULT NULL COMMENT 'Customer Name / Supplier Name',
  /* Id_Supplier dari mastersupplier - dipakai DUA jenis: PV Expense
     (supplier, tipe_sup 'S') dan Other Inflow (customer, tipe_sup 'C').
     Tabel masternya memang satu, yang membedakan cuma tipe_sup.
     Boleh NULL, karena kolom namanya tetap boleh diketik sendiri: di
     lembar Excel aslinya 163 dari 256 baris PV EXP isinya BUKAN supplier
     melainkan departemen (PURCHASING, MARKETING, ...) atau label periode
     gaji, dan Other Inflow memakai keterangan spt "Bunga".
     Kolom `nama` SELALU diisi: kalau dipilih dari master, isinya salinan
     nama saat itu supaya dokumen lama tidak ikut berubah kalau namanya
     diperbaiki di master. */
  `id_supplier`   BIGINT(20)     DEFAULT NULL,
  `kategori`      VARCHAR(255)   DEFAULT NULL COMMENT 'Income Category / Cost Category',
  `deskripsi`     VARCHAR(255)   DEFAULT NULL COMMENT 'Keterangan bebas, diketik pemakai',
  /* Disiapkan sejak awal, TAPI sampai 12 Nov 2026 belum dipakai: di laporan,
     kolom "Payment Voucher No" milik PV Expense memang diisi no_doc dokumen
     ini sendiri (EXP/...), bukan nomor PV lain. Dibiarkan ada supaya tidak
     perlu ubah struktur kalau nanti ternyata perlu. */
  `no_pv`         VARCHAR(50)    DEFAULT NULL,
  `curr`          VARCHAR(10)    DEFAULT NULL COMMENT 'mata uang akun SOURCE - satu-satunya yang terlihat pemakai',
  `amount`        DECIMAL(20,2)  NOT NULL DEFAULT 0.00,
  `rate`          DECIMAL(20,4)  NOT NULL DEFAULT 1.0000 COMMENT 'kurs PAJAK USD tgl dokumen; 1 kalau rupiah ke rupiah',
  /* Amount selalu dalam mata uang akun Source, lalu disimpan nilai rupiah
     dan nilai dolarnya - terlihat di layar sbg Equivalent IDR &
     Equivalent USD. Rupiah ke rupiah tidak punya sisi dolar: 0. */
  `amount_idr`    DECIMAL(20,2)  NOT NULL DEFAULT 0.00 COMMENT 'nilai rupiahnya - angka yang dipakai proyeksi',
  `amount_usd`    DECIMAL(20,2)  NOT NULL DEFAULT 0.00 COMMENT 'nilai dolarnya; 0 kalau rupiah ke rupiah',
  /* Nilai yang MENDARAT di akun tujuan = amount_idr atau amount_usd,
     tinggal lihat mata uang akun tujuan. Disimpan supaya saldo per akun
     bisa dihitung tanpa menebak ulang. */
  `curr_tujuan`   VARCHAR(10)    DEFAULT NULL,
  `amount_tujuan` DECIMAL(20,2)  DEFAULT NULL,
  `status`        VARCHAR(20)    NOT NULL DEFAULT 'Post' COMMENT 'Post / Cancel',
  `created_by`    VARCHAR(100)   DEFAULT NULL,
  `created_date`  DATETIME       DEFAULT NULL,
  `update_by`     VARCHAR(100)   DEFAULT NULL COMMENT 'pengubah terakhir lewat halaman Edit',
  `update_date`   DATETIME       DEFAULT NULL,
  `cancel_by`     VARCHAR(100)   DEFAULT NULL,
  `cancel_date`   DATETIME       DEFAULT NULL,
  `approve_by`    VARCHAR(100)   DEFAULT NULL,
  `approve_date`  DATETIME       DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_no_doc`  (`no_doc`),
  KEY `idx_jenis`   (`jenis`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_status`  (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* ---------------------------------------------------------------------------
   CATATAN 13 Nov 2026 - MENUNYA PINDAH DARI Cash KE Bank.
   Kunci hak aksesnya ikut berganti nama: 'Cash Projection Other' menjadi
   'Bank Projection Other', dan menu_group-nya 'Cash' menjadi 'Bank'.

   KALAU BERKAS INI SUDAH TERLANJUR DIJALANKAN dgn nama yang lama
   (mis. di basis data lokal), jalankan dua baris ini SEKALI supaya
   menunya muncul lagi - bukan menjalankan ulang seluruh berkas:

     UPDATE menurole   SET menu = 'Bank Projection Other', menu_group = 'Bank'
      WHERE menu = 'Cash Projection Other';
     UPDATE useraccess SET menu = 'Bank Projection Other'
      WHERE menu = 'Cash Projection Other';
   --------------------------------------------------------------------------- */

/* ---------------------------------------------------------------------------
   2. HAK AKSES MENU
   id 148 dipakai KARENA DIPANGGIL LANGSUNG di module/menu.php (pola yang
   sama dgn menu lain di berkas itu: id dibandingkan apa adanya). Kalau di
   produksi id 148 ternyata sudah terpakai menu lain, JANGAN dipaksa -
   ganti id di sini DAN di module/menu.php sekaligus.

   Periksa dulu:  SELECT * FROM menurole WHERE id = 148;
   --------------------------------------------------------------------------- */
INSERT INTO `menurole` (`id`, `menu`, `display_name`, `menu_group`, `status`, `profit_center`)
SELECT 148, 'Bank Projection Other', 'Projection - Other', 'Bank', 'Menu', 'NAG'
WHERE NOT EXISTS (SELECT 1 FROM `menurole` WHERE `menu` = 'Bank Projection Other');

/* ---------------------------------------------------------------------------
   3. PEMBERIAN AKSES
   Satu baris contoh untuk indro. Pemakai lain diberikan lewat layar
   User Access seperti biasa - TIDAK perlu SQL.
   --------------------------------------------------------------------------- */
INSERT INTO `useraccess` (`username`, `fullname`, `menu`, `create_date`, `create_user`)
SELECT 'indro', 'Indro', 'Bank Projection Other', NOW(), 'migrasi'
WHERE NOT EXISTS (
  SELECT 1 FROM `useraccess` WHERE `username` = 'indro' AND `menu` = 'Bank Projection Other'
);

/* ---------------------------------------------------------------------------
   4. MASTER KATEGORI ARUS KAS
   Isinya SALINAN lembar "Master CF" di berkas Excel aslinya - 12 kategori
   Cash In (dipakai tab Other Inflow) dan 42 kategori Cash Out (dipakai tab
   PV Expense). Dijadikan tabel supaya kategori baru cukup ditambah lewat
   data, tidak perlu menyentuh kode program.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `ap_cf_category` (
  `id`     INT(11)      NOT NULL AUTO_INCREMENT,
  `jenis`  VARCHAR(20)  NOT NULL COMMENT 'Cash In / Cash Out',
  `nama`   VARCHAR(255) NOT NULL,
  `urut`   INT(11)      NOT NULL DEFAULT 0,
  `status` VARCHAR(20)  NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jenis_nama` (`jenis`, `nama`),
  KEY `idx_jenis` (`jenis`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* INSERT IGNORE: aman dijalankan ulang, yang sudah ada dilewati. */
INSERT IGNORE INTO `ap_cf_category` (`jenis`, `nama`, `urut`) VALUES
  ('Cash Out', 'Pengeluaran Pembayaran Utang Usaha', 1),
  ('Cash In', 'Penerimaan Piutang Usaha (Existing)', 1),
  ('Cash Out', 'Pengeluaran Pembayaran Uang Muka Supplier', 2),
  ('Cash In', 'Penerimaan Piutang Usaha (Planning)', 2),
  ('Cash Out', 'Pengeluaran Pembayaran PPh', 3),
  ('Cash In', 'Penerimaan Piutang Lain-lain', 3),
  ('Cash Out', 'Pengeluaran Pembayaran PPN', 4),
  ('Cash In', 'Penerimaan Uang Muka Penjualan', 4),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Gaji Operator', 5),
  ('Cash In', 'Penerimaan Pendapatan Bunga Giro', 5),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Gaji Staff', 6),
  ('Cash In', 'Penerimaan Antar Bank (Dalam Satu PT)', 6),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya THR', 7),
  ('Cash In', 'Penerimaan Lain-lain', 7),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya BPJS TK', 8),
  ('Cash In', 'Setoran Modal', 8),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya BPJS KS', 9),
  ('Cash In', 'Penerimaan Pinjaman Bank', 9),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Listrik', 10),
  ('Cash In', 'Penerimaan Pinjaman Group', 10),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Telepon', 11),
  ('Cash In', 'Penerimaan Pinjaman Shareholder', 11),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Internet', 12),
  ('Cash In', 'Penerimaan Pinjaman Antar Divisi', 12),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Keperluan Pabrik', 13),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Perjalanan Dinas', 14),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Pemeliharaan Gedung', 15),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Pemeliharaan Mesin', 16),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Pemeliharaan Kendaraan', 17),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Pemeliharaan Peralatan', 18),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Sewa Tanah & Bangunan', 19),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Transportasi', 20),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya ATK', 21),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Export & Import', 22),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Jasa Profesional', 23),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Administrasi Bank', 24),
  ('Cash Out', 'Pengeluaran Pembayaran Biaya Provisi Bank', 25),
  ('Cash Out', 'Pengeluaran Antar Bank (Pemindahbukuan Internal)', 26),
  ('Cash Out', 'Pengeluaran Lain-lain', 27),
  ('Cash Out', 'Pembelian Tanah', 28),
  ('Cash Out', 'Pembelian Bangunan', 29),
  ('Cash Out', 'Pembelian Instalasi Pabrik', 30),
  ('Cash Out', 'Pembelian Mesin', 31),
  ('Cash Out', 'Pembelian Peralatan', 32),
  ('Cash Out', 'Pembelian Kendaraan', 33),
  ('Cash Out', 'Pembelian Perangkat Lunak', 34),
  ('Cash Out', 'Pelunasan Pinjaman Bank', 35),
  ('Cash Out', 'Pelunasan Pinjaman Non Bank', 36),
  ('Cash Out', 'Pelunasan Pinjaman Group', 37),
  ('Cash Out', 'Pelunasan Pinjaman Shareholder', 38),
  ('Cash Out', 'Pelunasan Pinjaman Antar Divisi', 39),
  ('Cash Out', 'Pembayaran Bunga Pinjaman Bank', 40),
  ('Cash Out', 'Pembayaran Bunga Pinjaman Group', 41),
  ('Cash Out', 'Pembayaran Bunga Pinjaman Shareholder', 42);

/* ---------------------------------------------------------------------------
   PEMERIKSAAN SESUDAH DIJALANKAN
   --------------------------------------------------------------------------- */
SELECT 'tabel'      AS bagian, COUNT(*) AS jml FROM information_schema.tables
  WHERE table_schema = DATABASE() AND table_name = 'ap_cf_projection'
UNION ALL
SELECT 'menurole'   AS bagian, COUNT(*) FROM menurole   WHERE menu = 'Bank Projection Other'
UNION ALL
SELECT 'useraccess' AS bagian, COUNT(*) FROM useraccess WHERE menu = 'Bank Projection Other'
UNION ALL
SELECT 'kategori Cash In'  AS bagian, COUNT(*) FROM ap_cf_category WHERE jenis = 'Cash In'
UNION ALL
SELECT 'kategori Cash Out' AS bagian, COUNT(*) FROM ap_cf_category WHERE jenis = 'Cash Out';
