<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_reverse = $_POST['no_reverse'];
$no_dokumen = $_POST['no_dokumen'];
$tgl_dokumen = date("Y-m-d",strtotime($_POST['tgl_dokumen']));
$nama_supp = $_POST['nama_supp'];
$curr = $_POST['curr'];
$total = $_POST['total'];
$deskripsi_det = $_POST['deskripsi'];
$create_date = date("Y-m-d H:i:s");
	

$query = "INSERT INTO ap_reverse_det (rvs_number, doc_number, doc_date, nama_supp, curr, total, deskripsi, status) 
VALUES 
	('$no_reverse', '$no_dokumen', '$tgl_dokumen', '$nama_supp', '$curr', '$total', '$deskripsi_det', 'Y')";
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>