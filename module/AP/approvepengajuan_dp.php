<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'];
$no_kbon = $_POST['no_kbon'];
$no_dp = $_POST['no_dp'];
$confirm_date = date("Y-m-d H:i:s");
$status1 = 'draft';
$status2 = 'Approved';
$status_int = 2;
$approve_user = $_POST['approve_user'];
$status_invoice = 'Waiting';

$sql = mysqli_query($conn2,"select * from pengajuan_kb_dp where id = '$id'");

if($no_kbon == ''){
	echo '';
}else{
$row= mysqli_fetch_assoc($sql);
$kbon = $row['no_kbon'];
$sql4 = mysqli_query($conn2,"select no_dp from kontrabon_dp where no_kbon = '$kbon'");
while($row4= mysqli_fetch_assoc($sql4)) {
$dp = $row4['no_dp'];

$sql = "update pengajuan_kb_dp set approved_user='$approve_user',tgl_approved='$confirm_date', status='$status2' where id='$id'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon_dp set cancel_user='$approve_user', cancel_date='$confirm_date', status='$status1', status_int='$status_int' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);

// $sql2 = "update ftr_dp set is_invoiced = '$status_invoice' where no_ftr_dp= '$dp'";
// $query2 = mysqli_query($conn2,$sql2);	


header('Refresh:0; url=pengajuankb.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>