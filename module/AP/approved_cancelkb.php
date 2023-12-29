<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$confirm_date = date("Y-m-d");
$status = 'Cancel';
$status_int = 1;
$approve_user = $_POST['approve_user'];

$sql = mysqli_query($conn2,"select * from cancel_kb where no_kbon = '$no_kbon'");

if($no_kbon == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$kbon = $row['no_kbon'];

$sql = "update cancel_kb set approved_user='$approve_user', tgl_approved='$confirm_date' where no_kbon='$kbon'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon set confirm_user='$approve_user', confirm_date='$confirm_date',status='$status', status_int='$status_int' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);
header('Refresh:0; url=pengajuankb.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>