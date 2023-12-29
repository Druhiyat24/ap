<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pv = $_POST['no_pv'];
$status = 'Approved';
$approve_date = date("Y-m-d H:i:s");
$approve_user = $_POST['approve_user'];



$sql = "update tbl_pv_h set approve_by='$approve_user',approve_date='$approve_date', status='$status' where no_pv='$no_pv'";
$query = mysqli_query($conn2,$sql);


if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>