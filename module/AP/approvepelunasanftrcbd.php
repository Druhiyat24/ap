<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$payment_ftr_id = $_POST['payment_ftr_id'];
// $confirm_date = date("Y-m-d H:i:s");
// $approve_user = $_POST['approve_user'];

if(isset($payment_ftr_id)){
$sql = "update payment_ftrcbd set status = 'Approved' where payment_ftr_id = '$payment_ftr_id'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
header('Refresh:0; url=pelunasanftrcbd.php');
}

mysqli_close($conn2);

?>