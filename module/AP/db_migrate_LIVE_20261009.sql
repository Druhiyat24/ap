-- ============================================================================
-- MIGRASI PRODUKSI - 9 Okt 2026
-- Gabungan dari empat berkas terpisah sebelumnya:
--   db_migrate_req_update_bpb.sql   (tabel Req_update_bpb_h / Req_update_bpb)
--   db_migrate_ubf_general.sql      (menurole Update BPB General)
--   db_migrate_approval_ftr.sql     (menurole Approval FTR CBD / DP)
--   db_migrate_log_ftr.sql          (tabel tbl_log_ftr)
--
-- WAJIB dijalankan SEBELUM pull kode baru, kalau tidak menunya error.
--
-- AMAN DIULANG. Semua perintah memakai IF NOT EXISTS / NOT EXISTS, jadi
-- menjalankannya dua kali tidak menduplikasi apa pun.
--
-- ----------------------------------------------------------------------------
-- KEADAAN PRODUKSI SAAT BERKAS INI DIBUAT (diperiksa 9 Okt 2026, read-only):
--   update_bpb_fabric_h  : 1 baris, kolom `jenis` BELUM ADA
--   update_bpb_fabric    : 3 baris
--   Req_update_bpb_h/_   : belum ada
--   tbl_log_ftr          : belum ada
--   menurole id terbesar : 138  (jadi 139/140/141/142 semuanya belum ada)
--   pemakai 'Update BPB Fabric'       : 3 (indro, nadia, willy)
--   pemakai 'Update BPB Accessories'  : 0
--   pemakai 'FTR' non-STAFF           : 12
--
-- KARENA ITU tabel baru dibuat dgn DDL EKSPLISIT, bukan "CREATE TABLE ... LIKE
-- update_bpb_fabric_h". Tabel lama di produksi tidak punya kolom `jenis`,
-- sehingga LIKE akan menghasilkan tabel tanpa kolom itu dan SELURUH kode baru
-- - yang menyaring per jenis - akan rusak tanpa pesan yang jelas.
-- ============================================================================


-- ============================================================================
-- BAGIAN 1 - Tabel pengajuan Update BPB
-- Nama barunya tidak lagi memuat kata "fabric", karena satu pasang tabel ini
-- dipakai bersama Fabric, Accessories, dan General. Yang membedakan kolom
-- `jenis`. Tabel LAMA TIDAK DIHAPUS - datanya disalin, bukan dipindah.
-- ============================================================================

CREATE TABLE IF NOT EXISTS Req_update_bpb_h (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    no_pengajuan  VARCHAR(30)  NOT NULL,
    tgl_pengajuan DATE         NOT NULL,
    nama_supp     VARCHAR(100)          DEFAULT NULL,
    deskripsi     VARCHAR(255)          DEFAULT NULL,
    status        VARCHAR(20)  NOT NULL,
    jenis         VARCHAR(20)  NOT NULL DEFAULT 'fabric',
    created_by    VARCHAR(50)           DEFAULT NULL,
    created_at    DATETIME              DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY no_pengajuan (no_pengajuan),
    KEY idx_jenis_status (jenis, status)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS Req_update_bpb (
    id           INT(11)       NOT NULL AUTO_INCREMENT,
    no_pengajuan VARCHAR(30)   NOT NULL,
    no_bpb       VARCHAR(50)   NOT NULL,
    tgl_bpb      DATE                   DEFAULT NULL,
    nama_supp    VARCHAR(100)           DEFAULT NULL,
    no_po        VARCHAR(50)            DEFAULT NULL,
    id_det       VARCHAR(50)            DEFAULT NULL,
    no_ws        VARCHAR(50)            DEFAULT NULL,
    id_jo        VARCHAR(50)            DEFAULT NULL,
    id_item      VARCHAR(50)            DEFAULT NULL,
    desc_item    VARCHAR(150)           DEFAULT NULL,
    qty          DECIMAL(15,2)          DEFAULT NULL,
    unit         VARCHAR(20)            DEFAULT NULL,
    curr         VARCHAR(10)            DEFAULT NULL,
    price_old    DECIMAL(18,4)          DEFAULT NULL,
    price_new    DECIMAL(18,4)          DEFAULT NULL,
    ppn_old      DECIMAL(8,2)           DEFAULT NULL,
    ppn_new      DECIMAL(8,2)           DEFAULT NULL,
    created_by   VARCHAR(50)            DEFAULT NULL,
    created_at   DATETIME               DEFAULT NULL,
    PRIMARY KEY (id),
    KEY no_pengajuan (no_pengajuan),
    KEY no_bpb (no_bpb)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Salin data lama. Kolomnya ditulis SATU PER SATU, tidak "SELECT *", supaya
-- tidak bergantung pada urutan maupun jumlah kolom tabel lama. `jenis` diisi
-- 'fabric' karena seluruh data lama di produksi memang Fabric - varian
-- Accessories belum pernah aktif di sana.
INSERT INTO Req_update_bpb_h
    (id, no_pengajuan, tgl_pengajuan, nama_supp, deskripsi, status, jenis, created_by, created_at)
SELECT h.id, h.no_pengajuan, h.tgl_pengajuan, h.nama_supp, h.deskripsi, h.status,
       'fabric', h.created_by, h.created_at
  FROM update_bpb_fabric_h h
 WHERE NOT EXISTS (SELECT 1 FROM (SELECT no_pengajuan FROM Req_update_bpb_h) x
                    WHERE x.no_pengajuan = h.no_pengajuan);

INSERT INTO Req_update_bpb
    (id, no_pengajuan, no_bpb, tgl_bpb, nama_supp, no_po, id_det, no_ws, id_jo, id_item,
     desc_item, qty, unit, curr, price_old, price_new, ppn_old, ppn_new, created_by, created_at)
SELECT d.id, d.no_pengajuan, d.no_bpb, d.tgl_bpb, d.nama_supp, d.no_po, d.id_det, d.no_ws,
       d.id_jo, d.id_item, d.desc_item, d.qty, d.unit, d.curr, d.price_old, d.price_new,
       d.ppn_old, d.ppn_new, d.created_by, d.created_at
  FROM update_bpb_fabric d
 WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM Req_update_bpb) x WHERE x.id = d.id);


-- ============================================================================
-- BAGIAN 2 - Jejak aktivitas FTR CBD & FTR DP
-- Bentuk kolomnya MENGIKUTI tbl_log_cash yang sudah dipakai modul Cash.
-- Satu tabel untuk kedua modul; yang membedakan ada di kolom `activitas`.
-- ============================================================================

CREATE TABLE IF NOT EXISTS tbl_log_ftr (
    id         INT(11)      NOT NULL AUTO_INCREMENT,
    nama_user  VARCHAR(100)          DEFAULT NULL,
    activitas  VARCHAR(100)          DEFAULT NULL,
    from_pc    VARCHAR(200)          DEFAULT NULL,
    log_date   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    doc_num    VARCHAR(200)          DEFAULT NULL,
    doc_date   DATE                  DEFAULT NULL,
    keterangan VARCHAR(255)          DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_doc  (doc_num),
    KEY idx_date (log_date)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ============================================================================
-- BAGIAN 3 - Menu baru
--   139 Update BPB Accessories   (belum pernah dibuat di produksi)
--   140 Update BPB General
--   141 Approval FTR CBD
--   142 Approval FTR DP
--
-- profit_center sengaja NULL. Query menu "BPB Garment" menyaring dgn
-- `profit_center != 'NAK'`, dan di SQL `NULL != 'NAK'` hasilnya NULL (bukan
-- TRUE), sehingga baris-baris ini tidak ikut tersaring ke sana. Kalau diisi
-- 'NAG', id-id ini akan muncul di GROUP_CONCAT menu BPB Garment.
-- ============================================================================

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 139, 'Update BPB Accessories', 'Update BPB Accessories', menu_group, 'Menu', NULL
  FROM menurole WHERE id = 112
   AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM menurole) x WHERE x.id = 139);

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 140, 'Update BPB General', 'Update BPB General', menu_group, 'Menu', NULL
  FROM menurole WHERE id = 112
   AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM menurole) x WHERE x.id = 140);

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 141, 'Approval FTR CBD', 'Approval FTR CBD', menu_group, 'Menu', NULL
  FROM menurole WHERE id = 4
   AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM menurole) x WHERE x.id = 141);

INSERT INTO menurole (id, menu, display_name, menu_group, status, profit_center)
SELECT 142, 'Approval FTR DP', 'Approval FTR DP', menu_group, 'Menu', NULL
  FROM menurole WHERE id = 4
   AND NOT EXISTS (SELECT 1 FROM (SELECT id FROM menurole) x WHERE x.id = 142);


-- ============================================================================
-- BAGIAN 4 - Hak akses
--
-- Update BPB Accessories & General diberikan ke pemegang 'Update BPB Fabric'
-- (3 orang: indro, nadia, willy). CATATAN: berkas lama memakai syarat
-- "yang punya Update BPB Accessories" - di produksi itu NOL orang, jadi
-- hasilnya tidak memberi akses ke siapa pun.
--
-- Approval FTR diberikan ke pemegang 'FTR' yang Groupp-nya BUKAN STAFF
-- (12 orang) - menyalin PERSIS syarat tombol Approve yang lama, jadi tidak
-- ada yang kehilangan maupun tiba-tiba mendapat kewenangan.
-- ============================================================================

INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT u.username, u.fullname, 'Update BPB Accessories', NOW(), 'migrasi'
  FROM useraccess u
 WHERE u.menu = 'Update BPB Fabric'
   AND NOT EXISTS (SELECT 1 FROM (SELECT username, menu FROM useraccess) v
                    WHERE v.username = u.username AND v.menu = 'Update BPB Accessories');

INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT u.username, u.fullname, 'Update BPB General', NOW(), 'migrasi'
  FROM useraccess u
 WHERE u.menu = 'Update BPB Fabric'
   AND NOT EXISTS (SELECT 1 FROM (SELECT username, menu FROM useraccess) v
                    WHERE v.username = u.username AND v.menu = 'Update BPB General');

INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT u.username, u.fullname, 'Approval FTR CBD', NOW(), 'migrasi'
  FROM useraccess u
 INNER JOIN userpassword p ON p.username = u.username
 WHERE u.menu = 'FTR' AND IFNULL(p.Groupp,'') <> 'STAFF'
   AND NOT EXISTS (SELECT 1 FROM (SELECT username, menu FROM useraccess) v
                    WHERE v.username = u.username AND v.menu = 'Approval FTR CBD');

INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
SELECT u.username, u.fullname, 'Approval FTR DP', NOW(), 'migrasi'
  FROM useraccess u
 INNER JOIN userpassword p ON p.username = u.username
 WHERE u.menu = 'FTR' AND IFNULL(p.Groupp,'') <> 'STAFF'
   AND NOT EXISTS (SELECT 1 FROM (SELECT username, menu FROM useraccess) v
                    WHERE v.username = u.username AND v.menu = 'Approval FTR DP');


-- ============================================================================
-- PEMERIKSAAN - jalankan sesudahnya. Kolom `hasil` harus "OK" semua.
-- ============================================================================

SELECT 'tabel Req_update_bpb_h' bagian,
       (SELECT COUNT(*) FROM update_bpb_fabric_h) lama,
       (SELECT COUNT(*) FROM Req_update_bpb_h) baru,
       IF((SELECT COUNT(*) FROM Req_update_bpb_h) >= (SELECT COUNT(*) FROM update_bpb_fabric_h),'OK','PERIKSA') hasil;

SELECT 'tabel Req_update_bpb' bagian,
       (SELECT COUNT(*) FROM update_bpb_fabric) lama,
       (SELECT COUNT(*) FROM Req_update_bpb) baru,
       IF((SELECT COUNT(*) FROM Req_update_bpb) >= (SELECT COUNT(*) FROM update_bpb_fabric),'OK','PERIKSA') hasil;

SELECT 'kolom jenis ada' bagian, '-' lama, COUNT(*) baru,
       IF(COUNT(*) = 1,'OK','PERIKSA') hasil
  FROM information_schema.columns
 WHERE table_schema = DATABASE() AND table_name = 'Req_update_bpb_h' AND column_name = 'jenis';

SELECT 'tabel tbl_log_ftr' bagian, '-' lama, COUNT(*) baru,
       IF(COUNT(*) = 1,'OK','PERIKSA') hasil
  FROM information_schema.tables
 WHERE table_schema = DATABASE() AND table_name = 'tbl_log_ftr';

SELECT 'menurole 139-142' bagian, '-' lama, COUNT(*) baru,
       IF(COUNT(*) = 4,'OK','PERIKSA') hasil
  FROM menurole WHERE id IN (139,140,141,142);

SELECT menu bagian, '-' lama, COUNT(*) baru, IF(COUNT(*) > 0,'OK','PERIKSA') hasil
  FROM useraccess
 WHERE menu IN ('Update BPB Accessories','Update BPB General','Approval FTR CBD','Approval FTR DP')
 GROUP BY menu;
