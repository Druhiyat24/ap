<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$cancel_by = $_POST['cancel_user'];
$cancel_date = date("Y-m-d H:i:s");


if(isset($no_dok)){
$sql = "update ca_adjust_input set status = 'N', cancel_by = '$cancel_by', cancel_date = '$cancel_date'  where no_dok = '$no_dok'";
$execute = mysqli_query($conn2,$sql);	

}else{
	die('Error: ' . mysqli_error());		
}


echo 'Data Berhasil Di Cancel';
header('Refresh:0; url=kontrabon.php');

mysqli_close($conn2);

?>