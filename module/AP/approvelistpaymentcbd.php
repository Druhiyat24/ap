<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_payment = $_POST['no_payment'];
$confirm_date = date("Y-m-d H:i:s");
$approve_user = $_POST['approve_user'];
$status_int = 4;

$sql11 = mysqli_query($conn2,"select no_kbon, no_payment, amount, outstanding from list_payment_cbd where no_payment = '$no_payment'");
while($row = mysqli_fetch_array($sql11)){
$no_pay = $row['no_payment'];
$no_bon = $row['no_kbon'];
$amount = $row['amount'];
$balance = $row['outstanding'];
$update_balance = $balance - $amount;
$sql2 = "update list_payment_cbd set outstanding = '$update_balance' where no_payment = '$no_pay' and no_kbon = '$no_bon' ";
$exec = mysqli_query($conn2,$sql2);
}

if(isset($no_payment)){
$sql = "update list_payment_cbd set confirm_date = '$confirm_date', confirm_user = '$approve_user', status = 'Approved', status_int = '$status_int' where no_payment = '$no_payment'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
header('Refresh:0; url=listpaymentcbd.php');
}

mysqli_close($conn2);

?>