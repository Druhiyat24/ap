/* ============================================================================
   MIGRASI PRODUKSI - MENU Cash > Projection > Other  (11 Nov 2026)

   Dijalankan SEKALI di basis data produksi (signalbit_erp) SEBELUM kode
   terbaru ditarik. Tanpa ini menunya tidak muncul dan halamannya galat
   karena tabelnya belum ada.

   Isinya tiga bagian:
     1. Tabel ap_cf_projection          - penyimpan ketiga jenis proyeksi
     2. menurole id 148                 - hak akses menunya
     3. useraccess                      - pemberian akses ke pemakai

   TIDAK ADA bagian master kategori. Kategori arus kasnya diambil dari
   master_cash_flow yang SUDAH ADA - lihat keterangan di bawah.

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
   4. MASTER KATEGORI ARUS KAS - DIBATALKAN (16 Nov 2026)

   Versi pertama berkas ini membuat tabel sendiri `ap_cf_category` berisi
   salinan 54 kategori dari lembar "Master CF" di Excel aslinya. ITU KELIRU
   dan sudah dibuang: tabel `master_cash_flow` yang sudah lama ada di
   aplikasi ini TERNYATA SUDAH MEMUAT SEMUANYA.

   Dicocokkan satu per satu: ke-54 kategori salinan itu ada semua di
   master_cash_flow, dan master_cash_flow malah punya 2 lagi yang tidak ada
   di salinan ("Pengakuan Keuntungan/Kerugian Selisih Kurs"). Jadi salinan
   itu bukan cuma mubazir - dia LEBIH MISKIN dari masternya.

   Yang lebih berbahaya: master_cash_flow punya menunya sendiri (Master Cash
   Flow) dan dipakai dropdown Bank In & Bank Out. Kalau halaman proyeksi
   memakai salinan, kategori yang ditambah atau dinonaktifkan lewat menu itu
   TIDAK akan pernah ikut ke sini - persis cacat yang dulu terjadi pada
   ind_categori4.

   Sisi kodenya sudah diubah: cfp_daftar_kategori() di
   module/AP/cash_projection/cfp_lib.php membaca master_cash_flow
   (type_cashflow + status='Y'), dan tidak ada lagi yang menyentuh
   ap_cf_category.

   TIDAK PERLU tindakan apa pun di produksi: tabel ap_cf_category memang
   BELUM PERNAH dibuat di sana (diperiksa 15 Nov 2026). Kalau di basis data
   lokal terlanjur ada, dia cuma menganggur dan boleh dibuang sendiri:
       DROP TABLE IF EXISTS `ap_cf_category`;
   --------------------------------------------------------------------------- */

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
/* Sumber kategorinya master_cash_flow yang sudah ada - tidak dibuat di sini,
   hanya dipastikan isinya memang ada sebelum menunya dipakai. */
SELECT 'kategori Cash In (master_cash_flow)'  AS bagian, COUNT(*) FROM master_cash_flow
  WHERE type_cashflow = 'Cash In'  AND status = 'Y'
UNION ALL
SELECT 'kategori Cash Out (master_cash_flow)' AS bagian, COUNT(*) FROM master_cash_flow
  WHERE type_cashflow = 'Cash Out' AND status = 'Y';
