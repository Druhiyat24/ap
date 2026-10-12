<?php
include "../../conn/conn.php";

/* ================= INPUT ================= */

$start_date = date("Y-m-d", strtotime($_POST['start_date']));
$end_date   = date("Y-m-d", strtotime($_POST['end_date']));
$coa_number   = $_POST['coa_number'];
$profit_center   = $_POST['profit_center'];
$search     = $_POST['search']['value'] ?? '';
$kata_filter = date("M_Y", strtotime($_POST['start_date']));

$where = "WHERE 1=1 ";

/* ================= SEARCH FILTER ================= */

if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            nama_pc LIKE '%$search%' OR
            reff_doc LIKE '%$search%' OR
            no_journal LIKE '%$search%' OR
            keterangan LIKE '%$search%' OR
            curr LIKE '%$search%'
        )
    ";
}

/* ================= MAIN QUERY =================
   Nilai IDR dibulatkan ke EMPAT desimal, bukan dua. Kolom debit/credit
   di tbl_list_journal bertipe double(16,4) dan memang sering berisi
   pecahan lebih halus - diperiksa ke produksi 12 Okt 2026, 9.674 dari
   195.356 baris tahun 2026 nilainya berbeda antara dibulatkan 2 dan 4.
   Dulu dipotong di SQL, jadi angka aslinya tidak pernah sampai ke layar
   maupun ke Excel, berapa pun tampilan yang dipakai di sisi JavaScript.
   Dampaknya ke saldo berjalan sangat kecil: selisih SELURUH tahun 2026
   hanya -0,1202 di debit dan +0,0585 di kredit; pada contoh COA 1.38.01
   September 2026 nettonya sama persis. Yang menampilkan 2 atau 4 angka
   sekarang glAngka() di general_ledger.php. */

if ($profit_center == 'ALL') {
    $sql = "SELECT * FROM (
    SELECT 
        0 as sort_key, '-' id,  '-' nama_pc, '-' reff_doc,  '-' no_journal,  NULL as tgl_journal, 'SALDO AWAL' keterangan,  0 credit_idr,  0 debit_idr,  SUM($kata_filter) saldo_akhir FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number'
    UNION ALL
    SELECT 
        1 as sort_key, id, profit_center nama_pc, reff_doc, no_journal, tgl_journal, keterangan, credit_idr, debit_idr, (SELECT IFNULL(SUM($kata_filter),0) FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number') + SUM(debit_idr - credit_idr) OVER (ORDER BY tgl_journal ASC, no_journal ASC, CASE WHEN debit_idr > 0 THEN 0 ELSE 1 END, id ASC) AS saldo_akhir

    FROM (SELECT a.id, a.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, IFNULL(NULLIF(reff_doc,''),'-') reff_doc,  no_journal, tgl_journal, a.keterangan, ROUND(credit * rate,4) credit_idr, ROUND(debit * rate,4) debit_idr FROM tbl_list_journal a INNER JOIN master_pc b ON b.kode_pc = a.profit_center WHERE no_coa = '$coa_number' AND tgl_journal BETWEEN '$start_date' and '$end_date' AND a.status != 'Cancel'
    ) q1
) a $where ORDER BY sort_key ASC, tgl_journal ASC, no_journal ASC, CASE WHEN debit_idr > 0 THEN 0 ELSE 1 END, id ASC";
}else{
    $sql = "SELECT * FROM (
    SELECT 
        0 as sort_key, '-' id,  '-' nama_pc, '-' reff_doc,  '-' no_journal,  NULL as tgl_journal, 'SALDO AWAL' keterangan,  0 credit_idr,  0 debit_idr,  SUM($kata_filter) saldo_akhir FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number' and profit_center = '$profit_center'
    UNION ALL
    SELECT 
        1 as sort_key, id, profit_center nama_pc, reff_doc, no_journal, tgl_journal, keterangan, credit_idr, debit_idr, (SELECT IFNULL(SUM($kata_filter),0) FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number') + SUM(debit_idr - credit_idr) OVER (ORDER BY tgl_journal ASC, no_journal ASC, CASE WHEN debit_idr > 0 THEN 0 ELSE 1 END, id ASC) AS saldo_akhir

    FROM (SELECT a.id, a.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, IFNULL(NULLIF(reff_doc,''),'-') reff_doc,  no_journal, tgl_journal, a.keterangan, ROUND(credit * rate,4) credit_idr, ROUND(debit * rate,4) debit_idr FROM tbl_list_journal a INNER JOIN master_pc b ON b.kode_pc = a.profit_center WHERE no_coa = '$coa_number' AND tgl_journal BETWEEN '$start_date' and '$end_date' AND a.status != 'Cancel' and a.profit_center = '$profit_center'
    ) q1
) a $where ORDER BY sort_key ASC, tgl_journal ASC, no_journal ASC, CASE WHEN debit_idr > 0 THEN 0 ELSE 1 END, id ASC";
}


/* ================= EXECUTE ================= */


$q = mysqli_query($conn2, $sql);

$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

/* ================= RESPONSE ================= */

echo json_encode([
    "data" => $data,
    "query" => $sql
]);
