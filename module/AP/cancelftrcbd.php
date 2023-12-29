<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrcbd = $_POST['noftrcbd'];
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];

if(isset($noftrcbd)){
$sql = "update ftr_cbd set cancel_date = '$cancel_date', cancel_user = '$cancel_user', status = 'Cancel' where no_ftr_cbd = '$noftrcbd'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Cancel';
header('Refresh:0; url=ftrcbd.php');
}

mysqli_close($conn2);

?>