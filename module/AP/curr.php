<?php
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

// parameter pencarian
$q = isset($_GET['q']) ? $_GET['q'] : '';
$q = mysqli_real_escape_string($conn1, $q);

// query
$sql = mysqli_query($conn1,
    "SELECT curr FROM (
        SELECT 'IDR' AS curr
        UNION
        SELECT curr FROM masterrate GROUP BY curr
    ) a
    WHERE curr LIKE '%$q%' "
);

// output
$data = array();

while ($r = mysqli_fetch_assoc($sql)) {
    // Jika untuk Select2:
    $data[] = [
        "id"   => $r['curr'],
        "text" => $r['curr']
    ];
}

echo json_encode($data);
?>