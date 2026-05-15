<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_inv = $_POST['no_inv']; // array

$list = "'" . implode("','", $no_inv) . "'";

$q = mysqli_query($conn2,"
    SELECT a.no_invoice 
    FROM ir_invoice_supp a inner join ir_invoice_supp_h b on b.doc_number = a.doc_number
    WHERE a.no_invoice IN ($list) and a.status = 'Y' and b.status != 'cancel' and a.no_invoice != '-'
");

$data = [];
while($r = mysqli_fetch_assoc($q)){
    $data[] = $r['no_invoice'];
}

echo json_encode($data);
?>