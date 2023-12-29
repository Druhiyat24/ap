<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'];
$no_kbon = $_POST['no_kbon'];
$no_cbd = $_POST['no_cbd'];
$confirm_date = date("Y-m-d H:i:s");
$status1 = 'draft';
$status2 = 'Approved';
$status_int = 2;
$approve_user = $_POST['approve_user'];
$status_invoice = 'Waiting';

$sql = mysqli_query($conn2,"select * from pengajuan_kb_cbd where id = '$id'");

if($no_kbon == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$kbon = $row['no_kbon'];

$sql = "update pengajuan_kb_cbd set approved_user='$approve_user',tgl_approved='$confirm_date', status='$status2' where id='$id'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon_cbd set cancel_user='$approve_user', cancel_date='$confirm_date', status='$status1', status_int='$status_int' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);

// $sql2 = "update ftr_cbd set is_invoiced = '$status_invoice' where no_ftr_cbd= '$no_cbd'";
// $query2 = mysqli_query($conn2,$sql2);	


header('Refresh:0; url=pengajuankb_cbd.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>