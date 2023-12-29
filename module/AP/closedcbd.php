<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pay = $_POST['no_pay'];
$confirm_date = date("Y-m-d H:i:s");
$update_user = $_POST['update_user'];


if($no_pay == ''){
	echo '';
}else{

$sql = "update list_payment_cbd set status = 'Closed', closed_date = '$confirm_date', closed_by = '$update_user' where no_payment = '$no_pay' and status = 'Approved'";
$query = mysqli_query($conn2,$sql);

header('Refresh:0; url=formclosing-paycbd.php');
}


if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>