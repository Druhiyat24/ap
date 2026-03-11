<?php
include '../../conn/conn.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? $_GET['q'] : '';
$q = mysqli_real_escape_string($conn1, $q);

$sql = mysqli_query($conn1,
    "SELECT 
        kpno AS id,
        CONCAT(kpno,' - ',styleno) AS text
     FROM act_costing where cost_date >= '2022-01-01'
     and kpno LIKE '%$q%'
     ORDER BY kpno"
);

$data = array();
while ($r = mysqli_fetch_assoc($sql)) {
    $data[] = $r;
}

echo json_encode($data);
?>