<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_ci = $_POST['no_ci'];
$status = 'Cancel';
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];



$sql = "update c_cash_in set cancel_by='$cancel_user',cancel_date='$cancel_date', status='$status' where no_ci='$no_ci'";
$query = mysqli_query($conn2,$sql);

$sql2 = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_ci')";
$query2 = mysqli_query($conn2,$sql2);

$sql4 = "update c_report_pettycash set status='$status' where no_doc='$no_ci'";
$query4 = mysqli_query($conn2,$sql4);


if(!$query2) {
	die('Error: ' . mysqli_error());	
}else{
	$sql3 = "Delete from tbl_list_journal where no_journal='$no_ci'";
	$query3 = mysqli_query($conn2,$sql3);	
}

mysqli_close($conn2);

?>