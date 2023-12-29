<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bpb = $_POST['no_bpb'];
$tgl_bpb = date("Y-m-d",strtotime($_POST['tgl_bpb']));
$supplier = $_POST['supplier'];
$curr = $_POST['curr'];
$total = $_POST['total'];
$user = $_POST['user'];
$create_date = date("Y-m-d H:i:s");


if ($no_bpb != '') {
	$query = "INSERT INTO tbl_bpb_temp (no_bpb,tgl_bpb,nama_supp,curr,total,user_input,date_input) 
VALUES 
	('$no_bpb', '$tgl_bpb', '$supplier','$curr', '$total', '$user', '$create_date')";

$execute = mysqli_query($conn2,$query);  

}else{
	$query = 'select max(id) from master_coa_ctg1';
	$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{

	
}

mysqli_close($conn2);
?>