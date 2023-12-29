<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_ro = $_POST['no_ro'];
$confirm_date = date("Y-m-d H:i:s");
$status = 'Cancel';
$update_user = $_POST['update_user'];
$update_date = date("Y-m-d H:i:s");

$sql = mysqli_query($conn2,"select * from bppb_new where no_ro = '$no_ro'");

if($no_ro == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$ro = $row['no_ro']; 
$query = mysqli_query($conn2,"update bppb_new set update_date = '$update_date', update_user= '$update_user', status= '$status' where no_ro= '$ro'");
header('Refresh:0; url=formapprovebppb.php');

$sqla = "update bppb set ap_inv = null where bpbno_ro='$no_ro'";
$querya = mysqli_query($conn1,$sqla);
}
}

if(!$query){
	die('Error: ' . mysqli_error());		
}

mysqli_close($conn2);

?>