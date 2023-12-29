<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_payment = $_POST['no_payment'];
$tgl_payment = date("Y-m-d",strtotime($_POST['tgl_payment']));
$supp = $_POST['supp'];
$amount = $_POST['amount'];
$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$no_po = $_POST['no_po'];
$tgl_po = date("Y-m-d",strtotime($_POST['tgl_po']));
$no_bpb = $_POST['no_bpb'];
$tgl_bpb = date("Y-m-d",strtotime($_POST['tgl_bpb']));
$curr = $_POST['curr'];
$pph = $_POST['pph'];
$confirm_date = date("Y-m-d H:i:s");
$confirm_date2 = date("Y-m-d");
$approve_user = $_POST['approve_user'];
$status_int = 4;

$sql11 = mysqli_query($conn2,"select no_kbon, no_payment, amount, outstanding from list_payment where no_payment = '$no_payment'");
while($row = mysqli_fetch_array($sql11)){
$no_pay = $row['no_payment'];
$no_bon = $row['no_kbon'];
$amount = $row['amount'];
$balance = $row['outstanding'];
$update_balance = $balance - $amount;
$sql2 = "update list_payment set outstanding = '$update_balance' where no_payment = '$no_pay' and no_kbon = '$no_bon' ";
$exec = mysqli_query($conn2,$sql2);

}

if(isset($no_payment)){
	if(strpos($no_payment, 'LP/NAG/') !== false) {
$sql = "update list_payment set confirm_date = '$confirm_date', confirm_user = '$approve_user', status = 'Approved',status_int = '$status_int' where no_payment = '$no_payment'";
$execute = mysqli_query($conn2,$sql);

$sqlac = "update status set no_lp='$no_payment', tgl_lp='$tgl_payment' where no_lp='$no_payment'";
$queryac = mysqli_query($conn2,$sqlac);
}else{
$sql = "update saldo_awal set confirm_date = '$confirm_date', confirm_user = '$approve_user', status = 'Approved',status_int = '$status_int', outstanding = '0' where no_pay = '$no_payment'";
$execute = mysqli_query($conn2,$sql);
}

}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
header('Refresh:0; url=payment.php');
}

mysqli_close($conn2);

?>