<?php
include "../../conn/conn.php";

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);

$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$search = $_POST['search']['value'] ?? '';
$where = "";


if ($search != '') {
    $search = mysqli_real_escape_string($conn2, $search);
    $where .= "
        AND (
            a.no_trans LIKE '%$search%' OR
            a.status LIKE '%$search%' OR
            a.created_by LIKE '%$search%' OR
            a.keterangan LIKE '%$search%' OR
            b.keterangan LIKE '%$search%'
        )
    ";
}


$sql = "select a.no_trans, tgl_trans, CONCAT(a.created_by,' (',a.created_at,')') create_user, a.status, a.id, upper(IFNULL(a.keterangan,b.keterangan)) keterangan from transfer_memo_exim_h a INNER JOIN transfer_memo_exim_det b on b.no_trans = a.no_trans where tgl_trans BETWEEN '$start_date' and '$end_date' and (a.no_trans like '%TFTE%') $where GROUP BY a.id
";

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