<?php
include '../../conn/conn.php';

$tanggal = isset($_POST['tanggal']) ? date("Y-m-d", strtotime($_POST['tanggal'])) : null;

$sql = mysqli_query($conn1,"SELECT 
    CONCAT('PAY/LP/NAG/', DATE_FORMAT('$tanggal','%m%y'), '/', LPAD((COALESCE(MAX(SUBSTR(payment_ftr_id,17)),0)+1),5,0)) nomor,
    DATE_FORMAT('$tanggal','%d-%m-%Y') as tgl_format
    FROM payment_ftr");

$data = mysqli_fetch_assoc($sql);

$sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$tanggal' and v_codecurr = 'PAJAK'");
$rowy = mysqli_fetch_array($sqly);

// Kirim sebagai JSON agar mudah dipakai di JS
echo json_encode([
    "nomor" => $data['nomor'],
    "rate"   => $rowy['rate']
]);
?>
