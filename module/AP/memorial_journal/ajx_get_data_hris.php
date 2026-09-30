<?php
include "../../../conn/conn.php";

/* ================= INPUT ================= */

$tgl_hris      = $_POST['hris_date'];
$mj_type2       = $_POST['mj_type2'];
$search         = $_POST['search']['value'] ?? '';

// SUMBER DATA HRIS. Sejak Sep 2026 HRIS memisahkan payroll jadi DUA tabel
// dengan struktur kolom yang identik:
//   jurnal       -> periode berjalan, mis. 26 Agu s/d 25 Sep (perilaku lama)
//   jurnal_akhir -> potongan akhir bulan, mis. 26 Agu s/d 31 Agu
// Nilai dari luar TIDAK pernah ikut ke dalam SQL apa adanya - dipetakan lewat
// daftar putih di bawah, sehingga nama tabel selalu salah satu dari dua itu.
$hris_source = ($_POST['hris_source'] ?? 'jurnal') === 'jurnal_akhir' ? 'jurnal_akhir' : 'jurnal';

// Hanya WAGES & SALARY (CMJ001) yang membaca tabel ini. Kategori lain (mis.
// BPJS) punya sumbernya sendiri dan tidak mengenal pemisahan akhir bulan.
$tbl_jurnal = ($mj_type2 === 'CMJ001') ? $hris_source : 'jurnal';

// TEKS PERIODE di keterangan jurnal. Untuk jurnal biasa cukup "SEPTEMBER 2026",
// tetapi jurnal akhir bulan hanya memuat tanggal 26 s/d akhir bulan - kalau
// ditulis "SEPTEMBER 2026" saja, keterangannya menyaru sebagai upah sebulan
// penuh. Jadi dijadikan "26 - 30 SEPTEMBER 2026", mengikuti bentuk yang selama
// ini dipakai pada jurnal akhir bulan yang dibuat manual. DAY(LAST_DAY(...))
// menyesuaikan sendiri untuk bulan 28/29/30/31 hari.
$teks_periode = ($tbl_jurnal === 'jurnal_akhir')
    ? "'26 - ',DAY(LAST_DAY('$tgl_hris')),' ',UPPER(DATE_FORMAT('$tgl_hris','%M')),' ',DATE_FORMAT('$tgl_hris','%Y')"
    : "UPPER(DATE_FORMAT('$tgl_hris','%M')),' ',DATE_FORMAT('$tgl_hris','%Y')";

// ============================================================================
// KENAPA SEMUA KOLOM UANG DI BAWAH DIBUNGKUS COALESCE(...,0)
//
// Di SQL, apa pun yang DITAMBAH NULL hasilnya NULL. Rumus baris UPAH
// menjumlahkan sebelas kolom sekaligus, jadi SATU kolom NULL saja membuat
// seluruh nilai debitnya NULL - barisnya tetap muncul di layar, tapi
// angkanya 0,00, dan jurnalnya jadi timpang tanpa pesan kesalahan apa pun.
//
// Itu yang terjadi pada periode 2026-09: HRIS berhenti mengisi
// potongan_insentif dan potongan_lembur (NULL di SELURUH 299 baris; di
// 2026-08 tidak ada satu pun yang NULL). Akibatnya kaki UPAH hilang
// Rp 12.283.830.061,15 - persis sebesar ketimpangan yang terlihat:
//   debit  1.246.090.106,15  (tinggal Tunjangan + Lembur)
//   credit 13.529.920.167,30
// Sisi kredit tidak ikut rusak karena tidak memakai kedua kolom itu.
//
// Untuk kolom akumulator seperti ini NULL dan 0 memang berarti sama, jadi
// COALESCE aman dipasang apa pun keadaan datanya. Kaki kredit memakai
// COALESCE(SUM(...),0) karena SUM atas kolom yang SEMUA barisnya NULL juga
// mengembalikan NULL.
//
// CATATAN: IF(potongan_insentif > 0, ...) tidak ikut diubah - kondisi NULL
// dianggap tidak terpenuhi, jadi hasilnya sudah 0 dan memang benar.
// ============================================================================
$where = "WHERE 1=1 ";


if ($mj_type2 == 'CMJ001') {
    $sql = "select no_journal,id_pc, profit_center, no_coa, nama_coa, no_cc, cc_name, reff_number, reff_date, buyer,ws,curr,debit, credit, deskripsi from (select a.no_journal,c.no_coa,c.nama_coa,a.kode_bagian no_cc,a.nama_bagian cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,(COALESCE(a.gaji,0) - COALESCE(a.potongan,0) + (COALESCE(a.gaji_neto,0) - (COALESCE(a.gaji,0) + COALESCE(a.tunjangan_karyawan_rupiah,0) + COALESCE(a.total_lembur_rupiah,0) + COALESCE(a.bonus,0) - COALESCE(a.piutang_karyawan,0) - COALESCE(a.piutang_bazzar,0) - COALESCE(a.bpjs_tk,0) - COALESCE(a.bpjs_ks,0) - COALESCE(a.potongan,0) - COALESCE(a.potongan_insentif,0) - COALESCE(a.potongan_lembur,0)))) debit, '0' credit, CONCAT('UPAH DEPT ',UPPER(a.nama_bagian),' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_gaji where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m') 
                                        UNION
                                        select a.no_journal,c.no_coa,c.nama_coa,a.kode_bagian no_cc,a.nama_bagian cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,COALESCE(a.tunjangan_karyawan_rupiah,0) debit, '0' credit, CONCAT('TUNJANGAN DEPT ',UPPER(a.nama_bagian),' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_tunj where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m')
                                        UNION
                                        select a.no_journal,c.no_coa,c.nama_coa,a.kode_bagian no_cc,a.nama_bagian cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,IF(potongan_insentif < 0,potongan_insentif,0) debit, IF(potongan_insentif > 0,potongan_insentif,0) credit, CONCAT(IF(potongan_insentif > 0,'POTONGAN TUNJANGAN DEPT ','TUNJANGAN DEPT '),UPPER(a.nama_bagian),' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_tunj where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m')
                                        UNION
                                        select a.no_journal,c.no_coa,c.nama_coa,a.kode_bagian no_cc,a.nama_bagian cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,COALESCE(a.total_lembur_rupiah,0) debit, '0' credit, CONCAT('LEMBUR DEPT ',UPPER(a.nama_bagian),' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_lembur where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m')
                                        UNION
                                        select a.no_journal,c.no_coa,c.nama_coa,a.kode_bagian no_cc,a.nama_bagian cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,IF(potongan_lembur < 0,potongan_lembur,0) debit, IF(potongan_lembur > 0,potongan_lembur,0) credit, CONCAT(IF(potongan_lembur > 0,'POTONGAN LEMBUR DEPT ','LEMBUR DEPT '),UPPER(a.nama_bagian),' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_lembur where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m')
                                        UNION
                                        select a.no_journal,c.no_coa,c.nama_coa,a.kode_bagian no_cc,a.nama_bagian cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,COALESCE(a.bonus,0) debit, '0' credit, CONCAT('BONUS DEPT ',UPPER(a.nama_bagian),' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_bonus where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m')
                                        UNION
                                        select a.no_journal, '1.34.51' no_coa, 'PIUTANG LAIN-LAIN PIHAK KETIGA - KARYAWAN' nama_coa,'-' no_cc,'-' cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,'0' debit, COALESCE(SUM(a.piutang_karyawan),0) credit, CONCAT('PINJAMAN KARYAWAN',' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m') GROUP BY b.id_pc
                                        UNION
                                        select a.no_journal, '1.34.51' no_coa, 'PIUTANG LAIN-LAIN PIHAK KETIGA - KARYAWAN' nama_coa,'-' no_cc,'-' cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,'0' debit, COALESCE(SUM(a.piutang_bazzar),0) credit, CONCAT('POTONGAN BAZZAR',' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m') GROUP BY b.id_pc
                                        UNION
                                        select a.no_journal, '2.51.31' no_coa, 'BIAYA YANG MASIH HARUS DIBAYAR - BPJS' nama_coa,'-' no_cc,'-' cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,'0' debit, COALESCE(SUM(a.bpjs_tk),0) credit, CONCAT('AKRUAL BPJS TK',' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m') GROUP BY b.id_pc
                                        UNION
                                        select a.no_journal, '2.51.31' no_coa, 'BIAYA YANG MASIH HARUS DIBAYAR - BPJS' nama_coa,'-' no_cc,'-' cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,'0' debit, COALESCE(SUM(a.bpjs_ks),0) credit, CONCAT('AKRUAL BPJS KS',' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m') GROUP BY b.id_pc
                                        UNION
                                        select a.no_journal, '2.51.21' no_coa, 'BIAYA YANG MASIH HARUS DIBAYAR - GAJI' nama_coa,'-' no_cc,'-' cc_name,'-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr,'0' debit, COALESCE(SUM(a.gaji_neto),0) credit, CONCAT('AKRUAL BEBAN UPAH',' ',$teks_periode) deskripsi, b.id_pc, b.profit_center from $tbl_jurnal a inner join b_master_cc b on b.no_cc = a.kode_bagian where periode_payroll = DATE_FORMAT('$tgl_hris','%Y-%m') GROUP BY b.id_pc) a where a.no_journal is null";
} elseif ($mj_type2 == 'CMJ003') {
    mysqli_query($conn3, "DROP TEMPORARY TABLE IF EXISTS tmp_data_jurnal_bpjs");
    mysqli_query($conn3, "CREATE TEMPORARY TABLE tmp_data_jurnal_bpjs
SELECT * FROM (
WITH
log_jurnal as (select DATE_FORMAT(tgl_journal, '%Y-%m') tgl_journal, no_journal from log_jurnal_bpjs where DATE_FORMAT(tgl_journal, '%Y-%m') = DATE_FORMAT('$tgl_hris', '%Y-%m') and status = 'POST' limit 1),

bpjs as (
SELECT 
    eb.uuid,
    eb.kode_bpjs,
    CONCAT(SUBSTR(eb.kode_bpjs,1,4),'-',SUBSTR(eb.kode_bpjs,5,2)) AS bpjs_kehadiran,
    ea.enroll_id,
    ea.nik,
    ea.employee_name,
    da.site_nirwana_id,
    da.site_nirwana_name,
    da.department_id,
    da.department_name,
    da.sub_dept_id,
    da.sub_dept_name,
    ea.join_date,
    ea.tanggal_resign,
    ea.status_aktif,
    ea.status_staff,

    -- ===============================
    -- STATUS BPJS
    -- ===============================
    ea.status_aktif_bpjs_tk,
    ea.tanggal_bpjs_ketenagakerjaan,
    ea.nomor_bpjs_ketenagakerjaan,
    ea.status_aktif_bpjs_ks,
    ea.tanggal_bpjs_kesehatan,
    ea.nomor_bpjs_kesehatan,

    -- ===============================
    -- DASAR PERHITUNGAN
    -- ===============================
    eb.dasar_pot_bpjs_rupiah,

    -- ===============================
    -- TOTAL IURAN
    -- ===============================
    (eb.bpjs_tk_jht_bruto_rupiah + eb.bpjs_tk_jht_neto_rupiah) AS bpjs_tk_jht_rupiah,
    (eb.bpjs_tk_jpn_bruto_rupiah + eb.bpjs_tk_jpn_neto_rupiah) AS bpjs_tk_jpn_rupiah,
    (eb.bpjs_ks_jkn_bruto_rupiah + eb.bpjs_ks_jkn_neto_rupiah) AS bpjs_ks_jkn_rupiah,

    (
        eb.bpjs_tk_jkk_bruto_rupiah +
        eb.bpjs_tk_jkm_bruto_rupiah +
        eb.bpjs_tk_jht_bruto_rupiah +
        eb.bpjs_tk_jht_neto_rupiah +
        eb.bpjs_tk_jpn_bruto_rupiah +
        eb.bpjs_tk_jpn_neto_rupiah +
        eb.bpjs_ks_jkn_bruto_rupiah +
        eb.bpjs_ks_jkn_neto_rupiah
    ) AS total_iuran,

    -- ===============================
    -- JKM
    -- ===============================
    eb.bpjs_tk_jkm_persen,
    eb.bpjs_tk_jkm_bruto_persen AS bpjs_tk_jkm_perusahaan_persen,
    eb.bpjs_tk_jkm_neto_persen AS bpjs_tk_jkm_karyawan_persen,
    (eb.bpjs_tk_jkm_bruto_rupiah + eb.bpjs_tk_jkm_neto_rupiah) AS bpjs_tk_jkm_rupiah,
    eb.bpjs_tk_jkm_bruto_rupiah AS bpjs_tk_jkm_perusahaan_rupiah,
    eb.bpjs_tk_jkm_neto_rupiah AS bpjs_tk_jkm_karyawan_rupiah,

    -- ===============================
    -- JKK
    -- ===============================
    eb.bpjs_tk_jkk_persen,
    eb.bpjs_tk_jkk_bruto_persen AS bpjs_tk_jkk_perusahaan_persen,
    eb.bpjs_tk_jkk_neto_persen AS bpjs_tk_jkk_karyawan_persen,
    (eb.bpjs_tk_jkk_bruto_rupiah + eb.bpjs_tk_jkk_neto_rupiah) AS bpjs_tk_jkk_rupiah,
    eb.bpjs_tk_jkk_bruto_rupiah AS bpjs_tk_jkk_perusahaan_rupiah,
    eb.bpjs_tk_jkk_neto_rupiah AS bpjs_tk_jkk_karyawan_rupiah,

    -- ===============================
    -- JHT
    -- ===============================
    eb.bpjs_tk_jht_persen,
    eb.bpjs_tk_jht_bruto_persen AS bpjs_tk_jht_perusahaan_persen,
    eb.bpjs_tk_jht_neto_persen AS bpjs_tk_jht_karyawan_persen,
    eb.bpjs_tk_jht_bruto_rupiah AS bpjs_tk_jht_perusahaan_rupiah,
    eb.bpjs_tk_jht_neto_rupiah AS bpjs_tk_jht_karyawan_rupiah,

    -- ===============================
    -- JPN
    -- ===============================
    eb.bpjs_tk_jpn_persen,
    eb.bpjs_tk_jpn_bruto_persen AS bpjs_tk_jpn_perusahaan_persen,
    eb.bpjs_tk_jpn_neto_persen AS bpjs_tk_jpn_karyawan_persen,
    eb.bpjs_tk_jpn_bruto_rupiah AS bpjs_tk_jpn_perusahaan_rupiah,
    eb.bpjs_tk_jpn_neto_rupiah AS bpjs_tk_jpn_karyawan_rupiah,

    -- ===============================
    -- JKN
    -- ===============================
    eb.bpjs_ks_jkn_persen,
    eb.bpjs_ks_jkn_bruto_persen AS bpjs_ks_jkn_perusahaan_persen,
    eb.bpjs_ks_jkn_neto_persen AS bpjs_ks_jkn_karyawan_persen,
    eb.bpjs_ks_jkn_bruto_rupiah AS bpjs_ks_jkn_perusahaan_rupiah,
    eb.bpjs_ks_jkn_neto_rupiah AS bpjs_ks_jkn_karyawan_rupiah,

    eb.operator,
    SUBSTR(eb.created_at,1,19) AS created_at,
    SUBSTR(eb.updated_at,1,19) AS updated_at,
    SUBSTR(eb.deleted_at,1,19) AS deleted_at

FROM employee_bpjs eb
INNER JOIN employee_atribut ea
    ON eb.enroll_id = ea.enroll_id

INNER JOIN rekap_perhitungan_payroll rp
    ON eb.enroll_id = rp.enroll_id
    AND eb.periode_kehadiran = rp.periode_kehadiran

LEFT JOIN department_all da
    ON rp.sub_dept_id = da.sub_dept_id

WHERE
    CONCAT(SUBSTR(eb.kode_bpjs,1,4),'-',SUBSTR(eb.kode_bpjs,5,2)) = DATE_FORMAT('$tgl_hris','%Y-%m') AND
        DATE_FORMAT(SUBSTRING_INDEX(rp.periode_kehadiran,' s/d ',-1),'%Y-%m') = DATE_FORMAT('$tgl_hris','%Y-%m') and LEFT(ea.nik,3) IN ('NAG','NAK')

ORDER BY
    ea.employee_name ASC
),

data_bpjs as (select * from bpjs a LEFT JOIN log_jurnal b on b.tgl_journal = a.bpjs_kehadiran where no_journal is null)

SELECT * FROM data_bpjs
) x where ( tanggal_resign IS NULL OR tanggal_resign >= CONCAT(DATE_FORMAT(DATE_SUB('$tgl_hris', INTERVAL 1 MONTH),'%Y-%m'),'-26'))");
    $sql = "WITH

bpjs_jkk as (select LAST_DAY(STR_TO_DATE(bpjs_kehadiran, '%Y-%m')) tgl_jurnal, status_staff, no_cc, cc_name, no_coa, nama_coa, id_pc, profit_center, ROUND(sum(bpjs_tk_jkk_perusahaan_rupiah),2) debit, 0 credit, CONCAT('BPJS TK - JKK (', status_staff, ') DEPT ', cc_name, ' ', UPPER(DATE_FORMAT('$tgl_hris', '%M %Y')), ' (', IF(id_pc = 'NAG','GARMENT','KNITTING'), ')') keterangan from tmp_data_jurnal_bpjs  a INNER JOIN b_master_cc b on b.no_cc = a.sub_dept_id INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_bpjs_jkk GROUP BY status_staff, no_cc, id_pc),

bpjs_jkm as (select LAST_DAY(STR_TO_DATE(bpjs_kehadiran, '%Y-%m')) tgl_jurnal, status_staff, no_cc, cc_name, no_coa, nama_coa, id_pc, profit_center, ROUND(sum(bpjs_tk_jkm_perusahaan_rupiah),2) debit, 0 credit, CONCAT('BPJS TK - JKM (', status_staff, ') DEPT ', cc_name, ' ', UPPER(DATE_FORMAT('$tgl_hris', '%M %Y')), ' (', IF(id_pc = 'NAG','GARMENT','KNITTING'), ')') keterangan from tmp_data_jurnal_bpjs  a INNER JOIN b_master_cc b on b.no_cc = a.sub_dept_id INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_bpjs_jkm GROUP BY status_staff, no_cc, id_pc),

bpjs_jht as (select LAST_DAY(STR_TO_DATE(bpjs_kehadiran, '%Y-%m')) tgl_jurnal, status_staff, no_cc, cc_name, no_coa, nama_coa, id_pc, profit_center, ROUND(sum(bpjs_tk_jht_perusahaan_rupiah),2) debit, 0 credit, CONCAT('BPJS TK - JHT (', status_staff, ') DEPT ', cc_name, ' ', UPPER(DATE_FORMAT('$tgl_hris', '%M %Y')), ' (', IF(id_pc = 'NAG','GARMENT','KNITTING'), ')') keterangan from tmp_data_jurnal_bpjs  a INNER JOIN b_master_cc b on b.no_cc = a.sub_dept_id INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_bpjs_jht GROUP BY status_staff, no_cc, id_pc),

bpjs_jpn as (select LAST_DAY(STR_TO_DATE(bpjs_kehadiran, '%Y-%m')) tgl_jurnal, status_staff, no_cc, cc_name, no_coa, nama_coa, id_pc, profit_center, ROUND(sum(bpjs_tk_jpn_perusahaan_rupiah),2) debit, 0 credit, CONCAT('BPJS TK - JPN (', status_staff, ') DEPT ', cc_name, ' ', UPPER(DATE_FORMAT('$tgl_hris', '%M %Y')), ' (', IF(id_pc = 'NAG','GARMENT','KNITTING'), ')') keterangan from tmp_data_jurnal_bpjs  a INNER JOIN b_master_cc b on b.no_cc = a.sub_dept_id INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_bpjs_jpn GROUP BY status_staff, no_cc, id_pc),

bpjs_ks as (select LAST_DAY(STR_TO_DATE(bpjs_kehadiran, '%Y-%m')) tgl_jurnal, status_staff, no_cc, cc_name, no_coa, nama_coa, id_pc, profit_center, ROUND(sum(bpjs_ks_jkn_perusahaan_rupiah),2) debit, 0 credit, CONCAT('BPJS KS (', status_staff, ') DEPT ', cc_name, ' ', UPPER(DATE_FORMAT('$tgl_hris', '%M %Y')), ' (', IF(id_pc = 'NAG','GARMENT','KNITTING'), ')') keterangan from tmp_data_jurnal_bpjs  a INNER JOIN b_master_cc b on b.no_cc = a.sub_dept_id INNER JOIN mastercoa_v2 c on c.no_coa = b.coa_bpjs_ks GROUP BY status_staff, no_cc, id_pc),

total_bpjs_tk as (select tgl_jurnal, status_staff, '-' no_cc, '-' cc_name, '2.51.31' no_coa, 'BIAYA YANG MASIH HARUS DIBAYAR - BPJS' nama_coa, id_pc, profit_center, 0 debit, ROUND(SUM(debit),2) credit, CONCAT('AKRUAL BPJS TK (', status_staff, ') ', UPPER(DATE_FORMAT('$tgl_hris', '%M %Y')), ' (', IF(id_pc = 'NAG','GARMENT','KNITTING'), ')') keterangan from (select * from bpjs_jkk
UNION ALL
select * from bpjs_jkm
UNION ALL
select * from bpjs_jht
UNION ALL
select * from bpjs_jpn) a GROUP BY status_staff, id_pc ORDER BY id_pc, status_staff ASC),

total_bpjs_ks as (select tgl_jurnal, status_staff, '-' no_cc, '-' cc_name, '2.51.31' no_coa, 'BIAYA YANG MASIH HARUS DIBAYAR - BPJS' nama_coa, id_pc, profit_center, 0 debit, ROUND(sum(debit),2) credit, CONCAT('AKRUAL BPJS KS (', status_staff, ') ', UPPER(DATE_FORMAT('$tgl_hris', '%M %Y')), ' (', IF(id_pc = 'NAG','GARMENT','KNITTING'), ')') keterangan from (select * from bpjs_ks) a GROUP BY status_staff, id_pc ORDER BY id_pc, status_staff ASC)

select '' no_journal, id_pc, profit_center, no_coa, nama_coa, no_cc, cc_name, '-' reff_number, '' reff_date, '-' buyer, '-' ws, 'IDR' curr, debit, credit, keterangan deskripsi from (select * from bpjs_jkk
UNION ALL
select * from bpjs_jkm
UNION ALL
select * from bpjs_jht
UNION ALL
select * from bpjs_jpn
UNION ALL
select * from bpjs_ks
UNION ALL
select * from total_bpjs_tk
UNION
select * from total_bpjs_ks) a";
} else {
    $sql = "";
}

if(!$sql){
    echo json_encode([
        "error" => "SQL kosong",
        "mj_type2" => $mj_type2
    ]);
    exit;
}


/* ================= EXECUTE ================= */

$q = mysqli_query($conn3, $sql);

if(!$q){
    echo json_encode([
        "error" => mysqli_error($conn3),
        "query" => $sql
    ]);
    exit;
}


$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

/* ================= RESPONSE ================= */

echo json_encode([
    "data" => $data
]);
