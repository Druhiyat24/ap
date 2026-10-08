/* ============================================================================
   MIGRASI PRODUKSI - Update BPB varian ACCESSORIES
   signalbit_erp (10.10.5.12)

   Menu "Update BPB" kini punya lebih dari satu jenis barang. Berkas PHP-nya
   TIDAK disalin per jenis - satu set dipakai bersama, dibedakan parameter
   `jenis` (lihat module/AP/ubf_jenis.php). Karena itu tabel draftnya pun
   sama, cukup ditambah satu kolom pembeda.

   DUA LANGKAH SAJA:
     1. kolom `jenis` di update_bpb_fabric_h
     2. baris menurole untuk hak akses Accessories

   CATATAN: langkah 1 sebetulnya berjalan sendiri - form Create menjalankan
   ALTER ... ADD COLUMN IF NOT EXISTS saat pertama dibuka (pola yang sama
   dgn kolom id_jo). Tetap ditulis di sini supaya bisa dijalankan lebih dulu
   dan hasilnya terlihat, dan supaya rekap migrasi tetap lengkap.

   URUTAN: BAGIAN 0 dulu (hanya melihat), lalu 1, 2, lalu 3 untuk memastikan.
   ============================================================================ */


/* ============================================================================
   BAGIAN 0 - PERIKSA DULU (tidak mengubah apa pun)
   ============================================================================ */

-- 0.1 Kolom jenis sudah ada atau belum. Harapan sebelum migrasi: 0 baris.
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, IS_NULLABLE
FROM   information_schema.COLUMNS
WHERE  TABLE_SCHEMA = DATABASE()
  AND  TABLE_NAME = 'update_bpb_fabric_h'
  AND  COLUMN_NAME = 'jenis';

-- 0.2 Berapa pengajuan yang sudah ada (semuanya nanti jadi 'fabric').
SELECT COUNT(*) AS jml_pengajuan FROM update_bpb_fabric_h;

-- 0.3 Nomor menurole terbesar yang terpakai, dan apakah barisnya sudah ada.
SELECT MAX(id) AS id_terbesar FROM menurole;
SELECT id, menu, display_name, menu_group, status
FROM   menurole
WHERE  menu IN ('Update BPB Fabric', 'Update BPB Accessories');


/* ============================================================================
   BAGIAN 1 - KOLOM PEMBEDA JENIS

   Seluruh baris yang sudah ada otomatis jadi 'fabric' lewat DEFAULT, jadi
   daftar Fabric yang sekarang tidak berubah isinya. Aman diulang.
   ============================================================================ */

ALTER TABLE update_bpb_fabric_h
    ADD COLUMN IF NOT EXISTS jenis VARCHAR(20) NOT NULL DEFAULT 'fabric' AFTER status;

-- Jaring pengaman: kalau ada baris yang jenisnya terlanjur kosong.
UPDATE update_bpb_fabric_h SET jenis = 'fabric' WHERE jenis IS NULL OR jenis = '';


/* ============================================================================
   BAGIAN 2 - HAK AKSES MENU ACCESSORIES

   Fabric memakai menurole 'Update BPB Fabric' (id 112) yang SUDAH ADA -
   jangan disentuh. Accessories diberi barisnya sendiri supaya aksesnya bisa
   dipisah per pengguna.

   id-nya TIDAK dipatok angka tertentu: dibiarkan AUTO_INCREMENT supaya tidak
   bertabrakan kalau sementara ini ada menu lain yang ditambahkan. Menu di
   header.php mengenali Accessories dari NAMA menunya, bukan dari nomor id.
   ============================================================================ */

INSERT INTO menurole (menu, display_name, menu_group, status)
SELECT 'Update BPB Accessories', 'Update BPB Accessories', 'Cost Accounting', 'Menu'
WHERE NOT EXISTS (SELECT 1 FROM menurole WHERE menu = 'Update BPB Accessories');

/* Memberi akses ke pengguna yang SUDAH punya akses Update BPB Fabric.
   Kalau Anda ingin daftarnya berbeda, JANGAN jalankan baris ini - tambahkan
   sendiri lewat menu User Access. */
INSERT INTO useraccess (username, menu)
SELECT ua.username, 'Update BPB Accessories'
FROM   useraccess ua
WHERE  ua.menu = 'Update BPB Fabric'
  AND  NOT EXISTS (SELECT 1 FROM useraccess x
                   WHERE x.username = ua.username AND x.menu = 'Update BPB Accessories');


/* ============================================================================
   BAGIAN 3 - VERIFIKASI (hanya melihat)
   ============================================================================ */

-- 3.1 Kolom jenis harus muncul. Harapan: 1 baris, default 'fabric'.
SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT
FROM   information_schema.COLUMNS
WHERE  TABLE_SCHEMA = DATABASE()
  AND  TABLE_NAME = 'update_bpb_fabric_h'
  AND  COLUMN_NAME = 'jenis';

-- 3.2 Seluruh pengajuan lama harus berjenis 'fabric'. Harapan: 1 baris saja.
SELECT jenis, COUNT(*) AS jml FROM update_bpb_fabric_h GROUP BY jenis;

-- 3.3 Dua baris menurole harus ada.
SELECT id, menu, display_name, menu_group, status
FROM   menurole
WHERE  menu IN ('Update BPB Fabric', 'Update BPB Accessories')
ORDER  BY id;

-- 3.4 Siapa saja yang kini bisa membuka menu Accessories.
SELECT username, menu FROM useraccess
WHERE  menu IN ('Update BPB Fabric', 'Update BPB Accessories')
ORDER  BY username, menu;
