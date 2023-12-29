<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pv = $_POST['no_pv'];
$status = 'Cancel';
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];



$sql = "update tbl_pv_h set cancel_by='$cancel_user',cancel_date='$cancel_date', status='$status' where no_pv='$no_pv'";
$query = mysqli_query($conn2,$sql);


if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>