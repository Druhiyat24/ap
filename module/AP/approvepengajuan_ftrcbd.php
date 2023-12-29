<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'];
$no_cbd = $_POST['no_cbd'];
$no_po = $_POST['no_po'];
$confirm_date = date("Y-m-d H:i:s");
$status1 = 'draft';
$status2 = 'Approved';
// $status_int = 1;
$approve_user = $_POST['approve_user'];


$sql = mysqli_query($conn2,"select * from pengajuan_ftrcbd where id = '$id'");

if($no_po == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$kbon = $row['no_po'];
$cbd = $row['no_cbd'];

$sql = "update pengajuan_ftrcbd set approved_user='$approve_user',tgl_approved='$confirm_date', status='$status2' where id='$id'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update ftr_cbd set cancel_user='$approve_user', cancel_date='$confirm_date', status='$status1' where no_po='$kbon' and no_ftr_cbd = '$cbd'";
$query1 = mysqli_query($conn2,$sql1);


header('Refresh:0; url=pengajuan_ftrcbd.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>