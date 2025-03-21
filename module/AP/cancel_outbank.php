<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi = $_POST['no_bi'];
$status = 'Cancel';
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];



$sql = "update b_bankout_h set cancel_by='$cancel_user',cancel_date='$cancel_date', status='$status' where no_bankout='$no_bi'";
$query = mysqli_query($conn2,$sql);

$sql2 = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_bi')";
$query2 = mysqli_query($conn2,$sql2);

$sql4 = "update b_reportbank set status='$status' where no_doc='$no_bi'";
$query4 = mysqli_query($conn2,$sql4);

$sql5 = "update status set no_pay ='', tgl_pay = '' where no_pay = '$no_bi'";
$query5 = mysqli_query($conn2,$sql5);

$sql6 = "update tbl_pv_h INNER JOIN b_bankout_det ON b_bankout_det.no_reff = tbl_pv_h.no_pv
SET tbl_pv_h.outstanding = tbl_pv_h.total
where b_bankout_det.no_bankout = '$no_bi' ";
$query6 = mysqli_query($conn2,$sql6);


$query_uptmm = "UPDATE memo_h set no_bankout = '',status='PAYMENT DRAFT' where no_bankout = '$no_bi' ";
$execute_uptmm = mysqli_query($conn2,$query_uptmm);


if(!$query) {
	die('Error: ' . mysqli_error());	
}else{
	$sql3 = "Delete from tbl_list_journal where no_journal='$no_bi'";
	$query3 = mysqli_query($conn2,$sql3);
}

mysqli_close($conn2);

?>