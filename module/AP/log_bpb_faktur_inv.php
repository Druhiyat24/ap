<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];
$unik_code = $_POST['unik_code'];
$create_date = date("Y-m-d H:i:s");

 $sqlnkb = mysqli_query($conn2,"select max(SUBSTR(no_dok,14,5)) no_dok from bpb_faktur_inv where jenis = 'FAK'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $urutan = $rownkb['no_dok'];
 $urutan++;
 $bln = date("m");
 $thn = date("y");
 $huruf = "FAK/NAG/$bln$thn/";
 $kode = $huruf . sprintf("%05s", $urutan);
	
$query = "INSERT INTO log_bpb_faktur_inv (no_doc, created_by, unik_code, created_date) 
VALUES 
   ('$kode', '$create_user', '$unik_code', '$create_date')";
$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
}

mysqli_close($conn2);
?>