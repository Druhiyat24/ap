<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$kode_trans = $_POST['kode_trans'];
$doc_num = $_POST['doc_num'];
$status = "Y";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$unik_code = $_POST['unik_code'];
$ttl_amount = $_POST['ttl_amount'];



$sql = mysqli_query($conn2,"select max(substr(no_trans,15)) no_trans from ir_log_trans where kode_trans = '$kode_trans'");
$row = mysqli_fetch_array($sql);
$kodepay = $row['no_trans'];
$urutan = (int) substr($kodepay, 0, 5);
$urutan++;
$bln = date("m");
$thn = date("y");
$huruf = "$kode_trans/NAG/$bln$thn/";
$kode = $huruf . sprintf("%05s", $urutan);



$query = "INSERT INTO ir_log_trans (kode_trans,no_trans,status,created_by,created_date,unik_code) 
VALUES 
	('$kode_trans', '$kode', '$status', '$create_user', '$create_date', '$unik_code')";

$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	echo 'Data Saved Successfully With Document Number '; echo $kode;
}

mysqli_close($conn2);
?>