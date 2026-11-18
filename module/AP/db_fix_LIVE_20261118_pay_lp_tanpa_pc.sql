/* ============================================================================
   PERBAIKAN DATA PRODUKSI - JURNAL DOBEL dari dokumen PAY/LP//1126/0000x
   Tanggal   : 18 November 2026
   Basis data: signalbit_erp @ 10.10.5.12

   APA YANG TERJADI
   ----------------
   18 Nov 2026 pagi, empat dokumen Payment (menu AP > Payment, jalur PV
   Direct) tersimpan dgn nomor CACAT - ruas profit center-nya kosong:

       PAY/LP//1126/00007 .. PAY/LP//1126/00010      (seharusnya .../NAG/...)

   Sebabnya di layar: dropdown Profit Center kehilangan nilainya sesudah
   pencarian supplier, jadi yang terkirim ke server string kosong. Kodenya
   sudah diperbaiki (tiga tempat, lihat menu-docs entri Payment), dan sejak
   itu server MENOLAK menyimpan tanpa profit center - jadi nomor cacat tidak
   bisa lahir lagi.

   Sesudah itu nomornya DIBETULKAN langsung di basis data oleh orang lain.
   Hasil pemeriksaan 18 Nov 2026 sore:

       payment_ftr          sudah benar  (PAY/LP/NAG/1126/0000x)
       payment_ftr_det      sudah benar
       tbl_list_journal     sudah benar  (8 baris)
       sb_list_journal      ADA DUA-DUANYA - 8 baris nomor lama DAN 8 baris
                            nomor baru, isi persis sama

   YANG DIPERBAIKI BERKAS INI: 8 baris sisa di sb_list_journal itu. Selama
   masih ada, keempat pembayaran ini TERHITUNG DUA KALI di laporan yang
   membaca sb_list_journal - total Rp 9.966.964 di sisi debit maupun kredit.
   (Nilainya berimbang, jadi neracanya tidak miring; yang salah besarannya.)

   TIDAK ADA tabel lain yang perlu disentuh. Seluruh kolom yang mungkin
   memuat nomor dokumen (reff_doc / no_journal / payment_ftr_id / no_payment
   / no_doc / no_kbon / keterangan / deskripsi) di SELURUH tabel sudah
   disapu: hanya sb_list_journal yang menyisakan nomor lama.

   CARA KERJANYA: baris lama DIHAPUS, bukan diganti namanya - kembarannya yang
   bernomor benar sudah ada. Yang dihapus HANYA yang benar-benar punya kembaran
   sama persis (nomor, COA, debit, kredit, tanggal buat). Baris lama yang entah
   bagaimana TIDAK punya kembaran akan DIBIARKAN, dan akan terlihat di
   pemeriksaan akhir - lebih baik tertinggal dan kelihatan daripada hilang
   diam-diam.

   AMAN DIJALANKAN ULANG: putaran kedua tidak menemukan apa-apa lagi.
   SALINANNYA DISIMPAN di tabel bak20261118_sb_journal_paylp sebelum dihapus.
   ============================================================================ */


/* ============================================================================
   BAGIAN 0 - LIHAT DULU (tidak mengubah apa pun)

   Harapannya: 4 baris "LAMA (cacat)" @ 2 baris, dan 4 baris "BARU" @ 2 baris
   dengan jumlah yang sama persis.
   ============================================================================ */
SELECT 'keadaan sebelum' AS bagian, @@hostname AS server, DATABASE() AS basis_data;

SELECT CASE WHEN no_journal LIKE 'PAY/LP//%' THEN 'LAMA (cacat)' ELSE 'BARU' END AS versi,
       no_journal, COUNT(*) AS baris,
       SUM(debit_idr) AS debit, SUM(credit_idr) AS kredit, MIN(status) AS status
  FROM sb_list_journal
 WHERE no_journal LIKE 'PAY/LP//1126/%'
    OR no_journal IN ('PAY/LP/NAG/1126/00007','PAY/LP/NAG/1126/00008',
                      'PAY/LP/NAG/1126/00009','PAY/LP/NAG/1126/00010')
 GROUP BY versi, no_journal
 ORDER BY no_journal, versi;

/* Tabel lain - semuanya harus 0. */
SELECT 'tbl_list_journal masih cacat' AS yang_dicek, COUNT(*) AS jml, '0' AS harapan
  FROM tbl_list_journal WHERE no_journal LIKE 'PAY/LP//%'
UNION ALL
SELECT 'payment_ftr masih cacat', COUNT(*), '0' FROM payment_ftr WHERE payment_ftr_id LIKE 'PAY/LP//%'
UNION ALL
SELECT 'payment_ftr_det masih cacat', COUNT(*), '0' FROM payment_ftr_det WHERE payment_ftr_id LIKE 'PAY/LP//%';


/* ============================================================================
   BAGIAN 1 - SALIN DULU yang akan dihapus

   Dipisah dari penghapusannya supaya kalau ternyata ada yang keliru, isinya
   bisa dikembalikan dari tabel ini - bukan dari backup semalam.
   ============================================================================ */
CREATE TABLE IF NOT EXISTS `bak20261118_sb_journal_paylp` LIKE `sb_list_journal`;

INSERT INTO `bak20261118_sb_journal_paylp`
SELECT * FROM `sb_list_journal` j
 WHERE j.no_journal LIKE 'PAY/LP//1126/%'
   AND NOT EXISTS (SELECT 1 FROM `bak20261118_sb_journal_paylp` b WHERE b.id = j.id);


/* ============================================================================
   BAGIAN 2 - HAPUS baris bernomor lama yang kembarannya sudah ada

   Kembaran = nomor yang sama sesudah '//' diganti '/NAG/', COA sama, debit
   sama, kredit sama, dan dibuat pada detik yang sama. Lima-limanya harus
   cocok; kalau salah satu tidak, barisnya tidak disentuh.
   ============================================================================ */
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
SELECT 'baris bernomor cacat tersisa' AS yang_dicek, COUNT(*) AS hasil, '0' AS harapan
  FROM sb_list_journal WHERE no_journal LIKE 'PAY/LP//%'
UNION ALL
SELECT 'baris bernomor benar (4 dokumen)', COUNT(*), '8' FROM sb_list_journal
 WHERE no_journal IN ('PAY/LP/NAG/1126/00007','PAY/LP/NAG/1126/00008',
                      'PAY/LP/NAG/1126/00009','PAY/LP/NAG/1126/00010')
UNION ALL
SELECT 'salinan tersimpan', COUNT(*), '8' FROM bak20261118_sb_journal_paylp;

/* Tiap dokumen harus tinggal 2 baris dan tetap berimbang. */
SELECT no_journal, COUNT(*) AS baris, SUM(debit_idr) AS debit, SUM(credit_idr) AS kredit,
       SUM(debit_idr) - SUM(credit_idr) AS selisih
  FROM sb_list_journal
 WHERE no_journal IN ('PAY/LP/NAG/1126/00007','PAY/LP/NAG/1126/00008',
                      'PAY/LP/NAG/1126/00009','PAY/LP/NAG/1126/00010')
 GROUP BY no_journal ORDER BY no_journal;
