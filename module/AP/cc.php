<?php
include '../../conn/conn.php';

header('Content-Type: application/json; charset=utf-8');

$q  = isset($_GET['q'])  ? $_GET['q']  : '';
$pc = isset($_GET['pc']) ? $_GET['pc'] : '';
$q  = mysqli_real_escape_string($conn1, $q);
$pc = mysqli_real_escape_string($conn1, $pc);

// Cost Center dibatasi sesuai Profit Center yang sudah dipilih di baris
// (b_master_cc.id_pc -> master_pc.kode_pc). Kalau Profit Center belum
// dipilih, jangan tampilkan Cost Center apapun dulu - supaya user tidak
// bisa pilih kombinasi PC/CC yang salah.
if ($pc === '') {
    echo json_encode([]);
    exit;
}

$sql = mysqli_query($conn1,
    "SELECT
        no_cc AS id,
        CONCAT(no_cc,' - ',cc_name) AS text
     FROM b_master_cc
     WHERE id_pc = '$pc'
       AND status = 'Active'
       AND (no_cc LIKE '%$q%' OR cc_name LIKE '%$q%')
     ORDER BY no_cc"
);

$data = array();
while ($r = mysqli_fetch_assoc($sql)) {
    $data[] = $r;
}

echo json_encode($data);
?>