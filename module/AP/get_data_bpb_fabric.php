<?php
include '../../conn/conn.php';
 
$no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
// Pastikan sudah terhubung dengan database menggunakan $conn1

$sql = mysql_query("select id,no_ws,id_item,id_jo,desc_item,ROUND(qty_good,2) AS qty,unit,IF (price LIKE '%.%',TRIM(TRAILING '0' FROM TRIM(TRAILING '.' FROM FORMAT(price,4))),FORMAT(price,0)) AS price,TRIM(TRAILING '.' FROM TRIM(TRAILING '0' FROM FORMAT(price_update,4))) price_update FROM whs_inmaterial_fabric_det WHERE no_dok='$no_bpb'", $conn1);

// Ambil data dan simpan dalam array
$data = [];
while ($row = mysql_fetch_assoc($sql)) {
    $data[] = $row;
}

// Mengonversi data menjadi format JSON
echo json_encode($data);
?>
