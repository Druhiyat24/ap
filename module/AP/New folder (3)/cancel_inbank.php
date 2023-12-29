<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi = $_POST['no_bi'];
$status = 'Cancel';
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];



$sql = "update tbl_bankin_arcollection set cancel_by='$cancel_user',cancel_date='$cancel_date', status='$status' where doc_num='$no_bi'";
$query = mysqli_query($conn2,$sql);


if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>