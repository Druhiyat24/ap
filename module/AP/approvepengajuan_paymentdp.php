<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'];
$no_payment = $_POST['no_payment'];
$no_kbon = $_POST['no_kbon'];
$confirm_date = date("Y-m-d H:i:s");
$status1 = 'draft';
$status2 = 'Approved';
$status_int = 2;
$approve_user = $_POST['approve_user'];
$amount = 0;

$sql11 = mysqli_query($conn2,"select list_payment_dp.no_kbon as no_kbon, SUM(list_payment_dp.amount) as amount, SUM(kontrabon_h_dp.balance) as balance from list_payment_dp inner join kontrabon_h_dp on kontrabon_h_dp.no_kbon = list_payment_dp.no_kbon where no_payment = '$no_payment' group by kontrabon_h_dp.no_kbon");
while($row = mysqli_fetch_array($sql11)){
$no_bon = $row['no_kbon'];
$amount = $row['amount'];
$balance = $row['balance'];
$update_balance = $balance + $amount;
// $sql2 = "update kontrabon_h_dp set balance = '$update_balance' where no_kbon = '$no_bon' ";
$exec = mysqli_query($conn2,$sql2);
}

$sql = mysqli_query($conn2,"select * from pengajuan_paymentdp where id = '$id'");

if($no_kbon == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$kbon = $row['no_kbon'];

$sql = "update pengajuan_paymentdp set approved_user='$approve_user',tgl_approved='$confirm_date', status='$status2' where id='$id'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update list_payment_dp set cancel_user='$approve_user', cancel_date='$confirm_date', status='$status1', status_int='$status_int' where no_payment = '$no_payment'";
$query1 = mysqli_query($conn2,$sql1);


header('Refresh:0; url=pengajuan_paymentdp.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>
