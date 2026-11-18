/* ============================================================================
   db_migrate_LIVE_20261117_project_studio.sql
   Menu Project (mode 3D): ruang DESIGN STUDIO (mockup) + DATA ROOM (ERD).

   JALANKAN DI: signalbit_erp (PRODUKSI). Menu Project selalu membaca produksi
   lewat $conn_live (lihat project.php), jadi tabelnya juga harus di sana.

   AMAN DIJALANKAN ULANG: semua CREATE TABLE IF NOT EXISTS. Tidak mengubah
   tabel lama dan tidak menyentuh data lain.

   Sebelum berkas ini dijalankan, menu Project tetap jalan: ruangannya tampil,
   Lucca memberi tahu bahwa tabelnya belum ada, dan semua tombol simpan menolak
   dengan pesan - bukan error.

   4 tabel:
     project_mockup   - satu baris per versi mockup (berkas .html disimpan utuh)
     project_erd      - satu baris per versi ERD (isi JSON dari editor ERD)
     project_review   - riwayat Submit / Approve / Request changes
     project_workflow - tanda "tidak perlu mockup / ERD" per project
   ============================================================================ */


/* ---------------------------------------------------------------------------
   0. PERIKSA DULU (tidak mengubah apa pun)
   --------------------------------------------------------------------------- */
SELECT @@hostname AS server, DATABASE() AS basis_data;

SELECT table_name AS tabel_yang_sudah_ada
  FROM information_schema.tables
 WHERE table_schema = DATABASE()
   AND table_name IN ('project_mockup', 'project_erd', 'project_review', 'project_workflow');


/* ---------------------------------------------------------------------------
   1. VERSI MOCKUP
      status: review   = baru diupload, menunggu persetujuan
              changes  = diminta revisi (versi berikutnya diupload sbg baris baru)
              approved = disetujui
      html MEDIUMBLOB: isi berkas disimpan apa adanya (tanpa konversi huruf),
      ditampilkan lagi di iframe sandbox.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `project_mockup` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `project_id`   INT(11)      NOT NULL     COMMENT 'master_project.id',
  `version`      INT(11)      NOT NULL     COMMENT '1, 2, 3 ... per project',
  `status`       VARCHAR(20)  NOT NULL DEFAULT 'review' COMMENT 'review / changes / approved',
  `note`         VARCHAR(255) DEFAULT NULL COMMENT 'catatan singkat versi ini',
  `file_name`    VARCHAR(150) DEFAULT NULL COMMENT 'nama berkas asal',
  `html`         MEDIUMBLOB   NOT NULL,
  `created_by`   VARCHAR(100) DEFAULT NULL,
  `created_date` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_version` (`project_id`, `version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   2. VERSI ERD
      status: draft    = masih disunting (hanya baris draft yang boleh diubah)
              review   = diajukan, menunggu persetujuan
              changes  = diminta revisi (suntingan berikutnya = versi baru)
              approved = disetujui
      sql_run_date: kapan SQL migrasi tabel barunya ditandai sudah dijalankan
      di produksi (Lucca mengingatkan selama masih kosong).
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `project_erd` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `project_id`   INT(11)      NOT NULL     COMMENT 'master_project.id',
  `version`      INT(11)      NOT NULL,
  `status`       VARCHAR(20)  NOT NULL DEFAULT 'draft' COMMENT 'draft / review / changes / approved',
  `erd_json`     MEDIUMTEXT   NOT NULL     COMMENT '{"tables":[...],"rels":[...]}',
  `sql_run_date` DATETIME     DEFAULT NULL,
  `sql_run_by`   VARCHAR(100) DEFAULT NULL,
  `created_by`   VARCHAR(100) DEFAULT NULL,
  `created_date` DATETIME     DEFAULT NULL,
  `updated_by`   VARCHAR(100) DEFAULT NULL,
  `updated_date` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_project_version` (`project_id`, `version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   3. RIWAYAT KEPUTUSAN (mockup & ERD)
      action: submitted / approved / changes
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `project_review` (
  `id`           INT(11)      NOT NULL AUTO_INCREMENT,
  `project_id`   INT(11)      NOT NULL,
  `kind`         VARCHAR(10)  NOT NULL     COMMENT 'mockup / erd',
  `ref_id`       INT(11)      NOT NULL     COMMENT 'project_mockup.id / project_erd.id',
  `version`      INT(11)      NOT NULL,
  `action`       VARCHAR(20)  NOT NULL     COMMENT 'submitted / approved / changes',
  `note`         TEXT,
  `created_by`   VARCHAR(100) DEFAULT NULL,
  `created_date` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_kind_ref` (`kind`, `ref_id`),
  KEY `idx_project` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   4. TANDA "TIDAK PERLU" PER PROJECT
      Project kecil yang tidak butuh mockup / tidak menambah tabel: ditandai di
      sini supaya dianggap beres (Coding tidak menunggu) & Lucca tidak menagih.
   --------------------------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS `project_workflow` (
  `project_id`   INT(11)      NOT NULL,
  `mockup_skip`  TINYINT(1)   NOT NULL DEFAULT 0,
  `erd_skip`     TINYINT(1)   NOT NULL DEFAULT 0,
  `updated_by`   VARCHAR(100) DEFAULT NULL,
  `updated_date` DATETIME     DEFAULT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* ---------------------------------------------------------------------------
   5. SESUDAH: harus muncul 4 baris
   --------------------------------------------------------------------------- */
SELECT table_name AS tabel_baru
  FROM information_schema.tables
 WHERE table_schema = DATABASE()
   AND table_name IN ('project_mockup', 'project_erd', 'project_review', 'project_workflow');
