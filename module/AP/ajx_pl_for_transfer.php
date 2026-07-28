<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d', strtotime('-1 month'));
$end_date   = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');

$where = "h.status IN ('Draft','FIRST APPROVED')
    AND DATE(h.pl_date) BETWEEN '" . mysqli_real_escape_string($conn2, $start_date) . "' AND '" . mysqli_real_escape_string($conn2, $end_date) . "'
    AND h.pl_number NOT IN (
        SELECT d.pl_number FROM pv_transfer_list_det d
        INNER JOIN pv_transfer_list_h th ON th.tl_number = d.tl_number
        WHERE th.status != 'Cancel'
    )";

$sql = mysqli_query($conn2, "SELECT h.pl_number, h.pl_date, h.deskripsi, h.status, h.from_account
    FROM pv_payment_list_h h
    WHERE $where
    ORDER BY h.pl_number ASC");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $data[] = [
        'pl_number' => $row['pl_number'],
        'pl_date'   => !empty($row['pl_date']) ? date('d-M-Y', strtotime($row['pl_date'])) : '-',
        'deskripsi' => $row['deskripsi'],
        'status'    => $row['status'],
        'from_account' => $row['from_account'],
    ];
}

echo json_encode(['data' => $data]);
