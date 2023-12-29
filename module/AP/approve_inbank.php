<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi = $_POST['no_bi'];
$status = 'Approved';
$approve_date = date("Y-m-d H:i:s");
$approve_user = $_POST['approve_user'];



$sql = "update tbl_bankin_arcollection set approve_by='$approve_user',approve_date='$approve_date', status='$status' where doc_num='$no_bi'";
$query = mysqli_query($conn2,$sql);

$sql2 = "update tbl_list_journal set approve_by='$approve_user',approve_date='$approve_date', status='$status' where no_journal='$no_bi'";
$query2 = mysqli_query($conn2,$sql2);


if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>