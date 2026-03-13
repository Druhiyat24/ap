<?php
include "../../conn/conn.php";

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

$start_date = date("Y-m-d", strtotime($_POST['start_date']));
$end_date   = date("Y-m-d", strtotime($_POST['end_date']));
$search     = $_POST['search']['value'] ?? '';

$where = "AND 1=1 ";


if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            no_journal LIKE '%$search%' OR
            tgl_journal LIKE '%$search%' OR
            type_journal LIKE '%$search%' OR
            keterangan LIKE '%$search%' OR
            curr LIKE '%$search%'
        )
    ";
}


$sql = "select no_journal, tgl_journal, type_journal, curr, sum(debit) debit, sum(credit) credit, ROUND(sum(debit * rate),2) debit_idr, ROUND(sum(credit * rate),2) credit_idr, keterangan from tbl_list_journal where tgl_journal BETWEEN '$start_date' and '$end_date' $where GROUP BY no_journal ";

$sql_count = "SELECT COUNT(*) total FROM ($sql) x";
$q_count = mysqli_query($conn2, $sql_count);
$row = mysqli_fetch_assoc($q_count);
$recordsTotal = intval($row['total']);
$recordsFiltered = $recordsTotal;

/* ================= DATA + LIMIT ================= */
$sql_limit = $sql . " LIMIT $start, $length";
$q = mysqli_query($conn2, $sql_limit);

$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

/* ================= RESPONSE ================= */
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $recordsTotal,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
]);