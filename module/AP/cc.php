<?php
include '../../conn/conn.php';

header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? $_GET['q'] : '';
$q = mysqli_real_escape_string($conn1, $q);

$sql = mysqli_query($conn1,
    "SELECT 
        no_cc AS id,
        CONCAT(no_cc,' - ',cc_name) AS text
     FROM b_master_cc
     WHERE no_cc LIKE '%$q%' and status ='Active'
        OR cc_name LIKE '%$q%' and status ='Active'
     ORDER BY no_cc"
);

$data = array();
while ($r = mysqli_fetch_assoc($sql)) {
    $data[] = $r;
}

echo json_encode($data);
?>