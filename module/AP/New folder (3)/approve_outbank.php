<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi = $_POST['no_bi'];
$status = 'Approved';
$approve_date = date("Y-m-d H:i:s");
$approve_user = $_POST['approve_user'];



$sql = "update b_bankout_h set approve_by='$approve_user',approve_date='$approve_date', status='$status' where no_bankout='$no_bi'";
$query = mysqli_query($conn2,$sql);


if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>