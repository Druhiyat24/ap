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

/* ================= MAIN QUERY ================= */

$sql = "SELECT * from (SELECT '$profit_center' nama_pc,'-' reff_doc, '-' no_journal, '-' tgl_journal, 'SALDO AWAL' keterangan, '-' credit_idr, '-' debit_idr, $kata_filter saldo_akhir FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number' and profit_center = '$profit_center'
UNION ALL
(SELECT profit_center nama_pc, reff_doc, q1.no_journal,q1.tgl_journal,q1.keterangan,q1.credit_idr,q1.debit_idr, (@runtot :=@runtot + q1.debit_idr - q1.credit_idr) AS saldo_akhir
FROM
   (select a.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, IFNULL(NULLIF(reff_doc,''),'-') reff_doc, no_journal,tgl_journal,a.keterangan,ROUND(credit * rate,2) credit_idr,ROUND(debit * rate,2) debit_idr from tbl_list_journal a INNER JOIN master_pc b on b.kode_pc = a.profit_center where no_coa = '$coa_number' and tgl_journal BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and profit_center = '$profit_center' order by tgl_journal,a.id ASC) AS q1 JOIN
     (SELECT @runtot:= IFNULL(( SELECT $kata_filter FROM fs_saldo_awal_tb WHERE no_coa = '$coa_number' and profit_center = '$profit_center'),0)) runtot ORDER BY tgl_journal ASC)) a ORDER BY tgl_journal ASC";

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
