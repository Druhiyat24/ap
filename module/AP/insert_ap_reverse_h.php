<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_reverse = $_POST['no_reverse'];
$reverse_date = date("Y-m-d",strtotime($_POST['reverse_date']));
$type_doc = $_POST['type_doc'];
$deskripsi = $_POST['deskripsi'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
	
$query = "INSERT INTO ap_reverse_h (rvs_number, rvs_date, type_doc, deskripsi, status, created_by, created_date) 
VALUES 
	('$no_reverse', '$reverse_date', '$type_doc', '$deskripsi', 'DRAFT', '$create_user', '$create_date')";
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>