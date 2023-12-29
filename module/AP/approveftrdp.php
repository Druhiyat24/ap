<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrdp = $_POST['noftrdp'];
$confirm_date = date("Y-m-d H:i:s");
$confirm_user = $_POST['cancel_user'];

if(isset($noftrdp)){
$sql = "update ftr_dp set confirm_date = '$confirm_date', confirm_user = '$confirm_user', status = 'Approved' where no_ftr_dp = '$noftrdp'";
$execute = mysqli_query($conn2,$sql);
header('Refresh:0; url=ftrdp.php');
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
}

mysqli_close($conn2);

?>