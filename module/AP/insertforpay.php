<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$data = $_POST['data'];
$ke_berapa = $_POST['ke_berapa'];
$dari_berapa = $_POST['dari_berapa'];
$dari_akun = $_POST['dari_akun'];
$ke_akun = $_POST['ke_akun'];
$keter = $_POST['keter'];

echo $doc_number;
echo "< -- >";
echo $data;
echo "< -- >";
echo $ke_berapa;
echo "< -- >";
echo $dari_berapa;
echo "< -- >";
echo $dari_akun;
echo "< -- >";
echo $ke_akun;
echo "< -- >";
echo $keter;

if(isset($data)){
	$query = "INSERT INTO tbl_forpay (no_doc,for_pay,ke_berapa,dari_berapa, dari_akun,ke_akun,keterangan) 
VALUES 
	('$doc_number', '$data','$ke_berapa', '$dari_berapa','$dari_akun', '$ke_akun','$keter')";

$execute = mysqli_query($conn2,$query);
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>