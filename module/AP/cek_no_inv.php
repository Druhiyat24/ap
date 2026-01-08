<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_inv = $_POST['no_inv']; // array

$list = "'" . implode("','", $no_inv) . "'";

$q = mysqli_query($conn2,"
    SELECT no_invoice 
    FROM ir_invoice_supp
    WHERE no_invoice IN ($list)
");

$data = [];
while($r = mysqli_fetch_assoc($q)){
    $data[] = $r['no_invoice'];
}

echo json_encode($data);
?>