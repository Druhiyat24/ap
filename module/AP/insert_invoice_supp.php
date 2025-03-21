<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$unik_code = $_POST['unik_code'];
$no_inv = $_POST['no_inv'];
$tgl_inv = date("Y-m-d",strtotime($_POST['tgl_inv']));
$amount = $_POST['amount'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = "Y";


$sql = mysqli_query($conn2,"select DISTINCT doc_number from ir_invoice_supp_h where unik_code = '$unik_code'");
$row = mysqli_fetch_array($sql);
$kode = $row['doc_number'];


$query = "INSERT INTO ir_invoice_supp (doc_number,no_invoice,tgl_invoice,amount,status,created_by,created_date) 
VALUES 
	('$kode', '$no_inv', '$tgl_inv', '$amount', '$status', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>