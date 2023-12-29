<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$payment_ftr_id = $_POST['payment_ftr_id'];
// $cancel_date = date("Y-m-d H:i:s");
// $cancel_user = $_POST['cancel_user'];
$amount = 0;
$no_kbon = '';
$balance=0;
// if($execute){
$sql1 = mysqli_query($conn2,"select payment_ftrcbd.no_kbon as no_kbon, payment_ftrcbd.list_payment_id as list_payment_id, SUM(payment_ftrcbd.nominal) as nominal, SUM(list_payment_cbd.outstanding) as outstanding from payment_ftrcbd inner join list_payment_cbd on list_payment_cbd.no_payment = payment_ftrcbd.list_payment_id where payment_ftr_id = '$payment_ftr_id' group by list_payment_cbd.no_kbon");
while($row = mysqli_fetch_array($sql1)){
$no_kbon = $row['no_kbon'];
$amount = $row['nominal'];
$balance = $row['outstanding'];
$update_balance = $balance + $amount;
$sql2 = "update list_payment_cbd set outstanding = '$update_balance' where no_kbon = '$no_kbon' ";
$exec = mysqli_query($conn2,$sql2);
}

// }

if($exec){
// $sql = "update list_payment set cancel_date = '$cancel_date', cancel_user = '$cancel_user', status = 'Cancel' where no_payment = '$no_payment'";
	$sql = "update payment_ftrcbd set status = 'Cancel' where payment_ftr_id = '$payment_ftr_id'";
$execute = mysqli_query($conn2,$sql);
header('Refresh:0; url=pelunasanftr_cbd.php');
}else{
	die('Error: ' . mysqli_error());		
}

// echo $no_kbon;
// echo "------";
// echo $amount;
// echo "------";
// echo $balance;
// echo "------";
// echo $update_balance;

mysqli_close($conn2);

?>