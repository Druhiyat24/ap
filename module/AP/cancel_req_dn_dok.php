<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_req = $_POST['no_req'];
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];

if(isset($no_req)){
$sql = "update req_dn_dok set cancel_date = '$cancel_date', cancel_by = '$cancel_user', status = 'CANCEL' where no_req = '$no_req'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Cancel';
}

mysqli_close($conn2);

?>