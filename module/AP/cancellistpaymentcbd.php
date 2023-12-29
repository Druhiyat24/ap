<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_payment = $_POST['no_payment'];
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];
$amount = 0;
$status_int = 1;
$no_kbon = '';
// if($execute){
$sql1 = mysqli_query($conn2,"select list_payment_cbd.no_kbon as no_kbon, SUM(list_payment_cbd.amount) as amount, SUM(kontrabon_h_cbd.balance) as balance from list_payment_cbd inner join kontrabon_h_cbd on kontrabon_h_cbd.no_kbon = list_payment_cbd.no_kbon where no_payment = '$no_payment' group by kontrabon_h_cbd.no_kbon");
while($row = mysqli_fetch_array($sql1)){
$no_kbon = $row['no_kbon'];
$amount = $row['amount'];
$balance = $row['balance'];
$update_balance = $balance + $amount;
$sql2 = "update kontrabon_h_cbd set balance = '$update_balance' where no_kbon = '$no_kbon' ";
$exec = mysqli_query($conn2,$sql2);
}

// }

if($exec){
$sql = "update list_payment_cbd set cancel_date = '$cancel_date', cancel_user = '$cancel_user', status = 'Cancel', status_int = '$status_int' where no_payment = '$no_payment'";
$execute = mysqli_query($conn2,$sql);
header('Refresh:0; url=listpaymentcbd.php');
}else{
	die('Error: ' . mysqli_error());		
}

// echo $no_kbon;
// echo $amount;
// echo $balance;
// echo $update_balance;

mysqli_close($conn2);

?>