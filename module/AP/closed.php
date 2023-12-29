<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pay = $_POST['no_pay'];
$confirm_date = date("Y-m-d H:i:s");
$update_user = $_POST['update_user'];


if($no_pay == ''){
	echo '';
}else{
	if(strpos($no_pay, 'LP/NAG/') !== false) {

$sql = "update list_payment set status = 'Closed', closed_date = '$confirm_date', closed_by = '$update_user' where no_payment = '$no_pay' and status = 'Approved'";
$query = mysqli_query($conn2,$sql);
}else{
$sql = "update saldo_awal set status = 'Closed', closed_date = '$confirm_date', closed_by = '$update_user' where no_pay = '$no_pay' and status = 'Approved'";
$query = mysqli_query($conn2,$sql);
}

header('Refresh:0; url=formapprovebpb.php');
}

// update list_payment inner join payment_ftr on payment_ftr.list_payment_id = list_payment.no_payment set list_payment.`status` = 'Closed', payment_ftr.keterangan = 'Closed', payment_ftr.closed_date ='$confirm_date', payment_ftr.closed_by = '$update_user' where payment_ftr.payment_ftr_id = '$no_pay

if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>