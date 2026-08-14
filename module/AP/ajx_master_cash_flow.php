<?php
header('Content-Type: application/json');
include '../../conn/conn.php';

$type_cashflow = isset($_REQUEST['type_cashflow']) ? $_REQUEST['type_cashflow'] : 'ALL';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 'ALL';

$conditions = [];
if ($type_cashflow !== '' && $type_cashflow !== 'ALL') {
    $conditions[] = "type_cashflow = '" . mysqli_real_escape_string($conn2, $type_cashflow) . "'";
}
if ($status !== '' && $status !== 'ALL') {
    $conditions[] = "status = '" . mysqli_real_escape_string($conn2, $status) . "'";
}
$where = count($conditions) > 0 ? 'where ' . implode(' and ', $conditions) : '';

$sql = mysqli_query($conn2, "select id, type_cashflow, nama_category, nama_subcategory, show_subcategory, status from master_cash_flow $where order by type_cashflow asc, nama_category asc, urutan asc");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $data[] = $row;
}

echo json_encode(['data' => $data]);
