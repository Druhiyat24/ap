<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_ci = $_POST['no_ci'];
$status = 'Approved';
$approve_date = date("Y-m-d H:i:s");
$approve_user = $_POST['approve_user'];



$sql = "update c_cash_in set approve_by='$approve_user',approve_date='$approve_date', status='$status' where no_ci='$no_ci'";
$query = mysqli_query($conn2,$sql);

$sql2 = "update tbl_list_journal set approve_by='$approve_user',approve_date='$approve_date', status='$status' where no_journal='$no_ci'";
$query2 = mysqli_query($conn2,$sql2);


if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>