<?php
include '../../conn/conn.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? $_GET['q'] : '';
$q = mysqli_real_escape_string($conn1, $q);

$sql = mysqli_query($conn1,
    "select kode_pc id, CONCAT(id_pc,' - ',nama_pc) text from master_pc where status = 'Active'"
);

$data = array();
while ($r = mysqli_fetch_assoc($sql)) {
    $data[] = $r;
}

echo json_encode($data);
?>