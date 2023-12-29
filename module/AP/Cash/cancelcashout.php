<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_co = $_POST['no_co'];
$status = 'Cancel';
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];



$sql = "update c_cash_out set cancel_by='$cancel_user',cancel_date='$cancel_date', status='$status' where no_co='$no_co'";
$query = mysqli_query($conn2,$sql);

$sql2 = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_co')";
$query2 = mysqli_query($conn2,$sql2);


if(!$query2) {
	die('Error: ' . mysqli_error());	
}else{
	$sql3 = "Delete from tbl_list_journal where no_journal='$no_co'";
	$query3 = mysqli_query($conn2,$sql3);	
}

mysqli_close($conn2);

?>