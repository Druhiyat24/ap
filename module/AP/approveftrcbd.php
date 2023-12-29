<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrcbd = $_POST['noftrcbd'];
$confirm_date = date("Y-m-d H:i:s");
$confirm_user = $_POST['cancel_user'];

if(isset($noftrcbd)){
$sql = "update ftr_cbd set confirm_date = '$confirm_date', confirm_user = '$confirm_user', status = 'Approved' where no_ftr_cbd = '$noftrcbd'";
$execute = mysqli_query($conn2,$sql);
header('Refresh:0; url=ftrcbd.php');
}else{
	die('Error: ' . mysqli_error());		
}

mysqli_close($conn2);

?>