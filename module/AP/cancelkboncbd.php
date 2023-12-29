+<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$cancel_user = $_POST['cancel_user'];
$cancel_date = date("Y-m-d H:i:s");
$status_int = 1;

if(isset($no_kbon)){
$sql = "update ftr_cbd inner join kontrabon_cbd on kontrabon_cbd.no_cbd = ftr_cbd.no_ftr_cbd set ftr_cbd.is_invoiced = 'Waiting', kb_inv = null where kontrabon_cbd.no_kbon = '$no_kbon'";
$execute = mysqli_query($conn2,$sql);	
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
	$query = mysqli_query($conn2,"update kontrabon_cbd set status = 'Cancel', status_int= '$status_int', cancel_user = '$cancel_user', cancel_date = '$cancel_date'  where no_kbon = '$no_kbon'");
	$query2 = mysqli_query($conn2,"update kontrabon_h_cbd set status = 'Cancel', cancel_user = '$cancel_user', cancel_date = '$cancel_date'  where no_kbon = '$no_kbon'");
}

echo 'Data Berhasil Di Cancel';
header('Refresh:0; url=kontrabonftrcbd.php');

mysqli_close($conn2);

?>