<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrdp = $_POST['noftrdp'];
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];

if(isset($noftrdp)){
$sql = "update ftr_dp set cancel_date = '$cancel_date', cancel_user = '$cancel_user', status = 'Cancel' where no_ftr_dp = '$noftrdp'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Cancel';
header('Refresh:0; url=ftrdp.php');
}

mysqli_close($conn2);

?>