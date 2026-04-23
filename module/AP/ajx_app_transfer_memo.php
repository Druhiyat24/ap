<?php
include "../../conn/conn.php";


$start_date = date("Y-m-d", strtotime($_POST['start_date']));
$end_date   = date("Y-m-d", strtotime($_POST['end_date']));
$search     = $_POST['search']['value'] ?? '';

$where = "";


if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            a.no_trans LIKE '%$search%' OR
            tgl_trans LIKE '%$search%' OR
            a.keterangan LIKE '%$search%'
        )
    ";
}


$sql = "select a.no_trans, tgl_trans, CONCAT(a.created_by,' (',a.created_at,')') create_user, a.status, a.id, upper(IFNULL(a.keterangan,b.keterangan)) keterangan from transfer_memo_exim_h a INNER JOIN transfer_memo_exim_det b on b.no_trans = a.no_trans where tgl_trans BETWEEN '$start_date' and '$end_date' and a.status = 'POST' and a.no_trans like '%TETF%' $where GROUP BY a.id";


$q = mysqli_query($conn2, $sql);

$data = [];
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = $r;
}

$sql_count = "SELECT COUNT(DISTINCT a.id) as total from (select a.no_trans, tgl_trans, CONCAT(a.created_by,' (',a.created_at,')') create_user, a.status, a.id, upper(IFNULL(a.keterangan,b.keterangan)) keterangan from transfer_memo_exim_h a INNER JOIN transfer_memo_exim_det b on b.no_trans = a.no_trans where tgl_trans BETWEEN '$start_date' and '$end_date' and a.status = 'POST' and a.no_trans like '%TETF%' $where GROUP BY a.id) a";

$q_count = mysqli_query($conn2, $sql_count);
$r_count = mysqli_fetch_assoc($q_count);
$total_pending = $r_count['total'];



echo json_encode([
    "data" => $data,
    "total_pending" => $total_pending
]);
