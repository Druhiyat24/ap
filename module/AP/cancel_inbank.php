<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi = $_POST['no_bi'];
$status = 'Cancel';
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];



$sql = "update tbl_bankin_arcollection set cancel_by='$cancel_user',cancel_date='$cancel_date', status='$status' where doc_num='$no_bi'";
$query = mysqli_query($conn2,$sql);

$sql2 = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_bi')";
$query2 = mysqli_query($conn2,$sql2);

$sql4 = "update b_reportbank set status='$status' where no_doc='$no_bi'";
$query4 = mysqli_query($conn2,$sql4);

if(!$query2) {
	die('Error: ' . mysqli_error());	
}else{
	$sql3 = "Delete from tbl_list_journal where no_journal='$no_bi'";
	$query3 = mysqli_query($conn2,$sql3);
}

mysqli_close($conn2);

?>