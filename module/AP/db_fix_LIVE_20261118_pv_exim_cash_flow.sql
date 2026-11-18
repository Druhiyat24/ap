/* ============================================================================
   PERBAIKAN DATA PRODUKSI - KATEGORI ARUS KAS PV EXIM (NOVEMBER 2026 SAJA)
   Tanggal   : 18 November 2026
   Basis data: signalbit_erp @ 10.10.5.12

   APA YANG TERJADI
   ----------------
   SELURUH 249 PV EXIM tidak punya kategori arus kas (tbl_pv_h.id_cash_flow
   NULL) - tanpa satu pun kecuali. Dilaporkan pemakai lewat PV/NAG/1126/00085.

   Sebabnya di kode, bukan kelalaian pengisian: form EXIM tidak punya pilihan
   kategori sama sekali. "For Payment"-nya kotak READONLY bertuliskan
   "Export - Import", dan INSERT di save_pv_exim.php memang tidak menyebut
   kolom id_cash_flow. Bandingkan dgn form Regular: di sana "Payment For"
   adalah dropdown isi master_cash_flow dan idnya ikut tersimpan.

   AKIBATNYA: di Projection Report, PV tanpa kategori jatuh ke baris 99
   "Pengeluaran Lain-lain" - bukan ke baris "Pengeluaran Pembayaran Biaya
   Export & Import". Uangnya tidak hilang, tapi masuk baris yang salah.

   KODENYA SUDAH DIPERBAIKI (18 Nov 2026): save_pv_exim.php dan
   update_pv_exim.php mengisi kolom itu lewat pv_exim_cash_flow.php, jadi PV
   EXIM BARU tidak akan kosong lagi, dan PV lama ikut terbetulkan begitu
   halaman editnya disimpan.

   YANG DIPERBAIKI BERKAS INI
   --------------------------
   HANYA PV EXIM BULAN BERJALAN - pv_date 1 s/d 30 November 2026, 38 PV,
   senilai Rp 917.593.841. Diputuskan pemakai 18 Nov 2026: yang Agustus
   s/d Oktober (211 PV) DIBIARKAN APA ADANYA, karena sudah terbayar dan
   laporan realisasinya memakai kategori yang tersimpan di dokumen Bank
   Out-nya sendiri, bukan kategori di PV ini.

   SEMUANYA DIISI "EXPORT & IMPORT" (master_cash_flow id 34), sesuai
   keputusan pemakai. CATATAN YANG PERLU DIKETAHUI: 9 dari 38 PV itu
   suppliernya instansi pajak/bea cukai (KPPBC TMP A BANDUNG 7, KANTOR
   PAJAK 2), dan waktu dibayar lewat Bank Out orang memilih kategori 16
   "Pengeluaran Pembayaran PPN" untuk semuanya. Jadi untuk kesembilan PV itu
   kategori di PV (34) akan BERBEDA dgn kategori di Bank Out-nya (16):
   proyeksi menaruhnya di baris Export & Import, realisasi di baris PPN.
   Itu pilihan yang sudah disadari, bukan kelalaian - dicatat di sini supaya
   tidak membingungkan waktu laporan variance dibaca.

   TIDAK MENYENTUH tabel lain. Dokumen Bank Out / Bank In / Petty Cash
   menyimpan SALINAN kategorinya sendiri waktu diposting, jadi tidak ikut
   berubah dan memang tidak boleh diubah dari sini.

   AMAN DIJALANKAN ULANG: putaran kedua tidak menemukan baris kosong lagi.
   SALINAN NILAI LAMANYA disimpan di bak20261118_pv_exim_cash_flow.
   ============================================================================ */


/* ============================================================================
   BAGIAN 0 - LIHAT DULU (tidak mengubah apa pun)
   ============================================================================ */
SELECT 'keadaan sebelum' AS bagian, @@hostname AS server, DATABASE() AS basis_data;

SELECT CASE WHEN pv_date BETWEEN '2026-11-01' AND '2026-11-30'
            THEN 'EXIM November 2026 (DIPERBAIKI)'
            ELSE 'EXIM bulan lain (DIBIARKAN)' END AS kelompok,
       COUNT(*) AS pv,
       SUM(id_cash_flow IS NULL OR id_cash_flow = 0) AS tanpa_kategori,
       ROUND(SUM(total)) AS nilai
  FROM tbl_pv_h
 WHERE pv_form_type = 'EXIM'
 GROUP BY kelompok;

/* Kategori tujuannya dicari dari master - harus ketemu id 34. */
SET @cf_exim = (SELECT id FROM master_cash_flow
                 WHERE show_subcategory = 'EXPORT & IMPORT'
                   AND type_cashflow = 'Cash Out'
                   AND status = 'Y'
                 ORDER BY id ASC LIMIT 1);

SELECT @cf_exim AS id_kategori_yang_dipakai, '34' AS harapan,
       (SELECT nama_subcategory FROM master_cash_flow WHERE id = @cf_exim) AS namanya;


/* ============================================================================
   BAGIAN 1 - SALIN DULU nilai lamanya

   Isinya cuma no_pv + nilai lama, karena yang berubah cuma satu kolom. Kalau
   ternyata keliru, nilainya bisa dikembalikan dari tabel ini.
   ============================================================================ */
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


/* ============================================================================
   BAGIAN 2 - ISI KATEGORINYA (November 2026 saja)

   Syarat @cf_exim IS NOT NULL itu penting: kalau baris masternya tidak ketemu,
   UPDATE ini tidak menyentuh satu baris pun - BUKAN mengisi NULL, yang justru
   cacat yang sedang diperbaiki.
   ============================================================================ */
UPDATE `tbl_pv_h`
   SET id_cash_flow = @cf_exim
 WHERE @cf_exim IS NOT NULL
   AND pv_form_type = 'EXIM'
   AND pv_date BETWEEN '2026-11-01' AND '2026-11-30'
   AND (id_cash_flow IS NULL OR id_cash_flow = 0);


/* ============================================================================
   PEMERIKSAAN AKHIR - kolom "hasil" harus sama dgn kolom "harapan"
   ============================================================================ */
SELECT 'EXIM November tanpa kategori' AS yang_dicek, COUNT(*) AS hasil, '0' AS harapan
  FROM tbl_pv_h WHERE pv_form_type = 'EXIM'
   AND pv_date BETWEEN '2026-11-01' AND '2026-11-30'
   AND (id_cash_flow IS NULL OR id_cash_flow = 0)
UNION ALL
SELECT 'EXIM November berkategori Export & Import', COUNT(*), '38'
  FROM tbl_pv_h WHERE pv_form_type = 'EXIM'
   AND pv_date BETWEEN '2026-11-01' AND '2026-11-30' AND id_cash_flow = @cf_exim
UNION ALL
SELECT 'EXIM bulan lain - HARUS TETAP kosong', COUNT(*), '211'
  FROM tbl_pv_h WHERE pv_form_type = 'EXIM'
   AND pv_date NOT BETWEEN '2026-11-01' AND '2026-11-30'
   AND (id_cash_flow IS NULL OR id_cash_flow = 0)
UNION ALL
SELECT 'salinan tersimpan', COUNT(*), '38' FROM bak20261118_pv_exim_cash_flow;

/* PV Regular TIDAK IKUT DISENTUH - jumlah yang tanpa kategori harus tetap 31
   (semuanya 4-14 Agu 2026, pekan pertama sesudah kolomnya dibuat). Labelnya
   bermacam-macam, jadi harus dicocokkan satu per satu, bukan diisi satu nilai
   spt EXIM. Pemeriksaan ini ada supaya ketahuan kalau ada yang tersenggol. */
SELECT 'PV Regular tanpa kategori (tidak disentuh)' AS yang_dicek,
       COUNT(*) AS hasil, '31' AS harapan
  FROM tbl_pv_h WHERE pv_form_type = 'Regular' AND (id_cash_flow IS NULL OR id_cash_flow = 0);

/* Rincian ke-38 PV yang diubah, supaya hasilnya bisa dilihat langsung. */
SELECT no_pv, pv_date, nama_supp, id_cash_flow, ROUND(total) AS nilai
  FROM tbl_pv_h
 WHERE pv_form_type = 'EXIM' AND pv_date BETWEEN '2026-11-01' AND '2026-11-30'
 ORDER BY pv_date ASC, no_pv ASC;
