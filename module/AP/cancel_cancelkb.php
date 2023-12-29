<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$cancel_date = date("Y-m-d");
$status = 'Post';
$status_int = 2;
$cancel_user = $_POST['cancel_user'];

$sql = mysqli_query($conn2,"select * from cancel_kb where no_kbon = '$no_kbon'");

if($no_kbon == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$kbon = $row['no_kbon'];

$sql = "update cancel_kb set cancel_user='$cancel_user', tgl_cancel='$cancel_date' where no_kbon='$kbon'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon set status='$status', status_int='$status_int' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);
header('Refresh:0; url=calcelpengajuankb.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>