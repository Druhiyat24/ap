<?php
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

// parameter q
$q = isset($_GET['q']) ? $_GET['q'] : '';
$q = mysqli_real_escape_string($conn1, $q);

$sql = mysqli_query($conn1,
    "SELECT 
        no_coa AS id,
        CONCAT(no_coa,' ',nama_coa) AS text
     FROM mastercoa_sb
     WHERE no_coa LIKE '%$q%'
        OR nama_coa LIKE '%$q%'
     ORDER BY no_coa"
);

$data = array();
while ($r = mysqli_fetch_assoc($sql)) {
    $data[] = $r;
}

echo json_encode($data);
?>