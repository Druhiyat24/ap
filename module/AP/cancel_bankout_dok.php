<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankout = $_POST['no_bankout'];
$file_name = $_POST['file_name'];
$cancel_date = date("Y-m-d H:i:s");
$cancel_user = $_POST['cancel_user'];

if(isset($file_name)){
$sql = "update b_bankout_dok set cancel_date = '$cancel_date', cancel_by = '$cancel_user', status = 'CANCEL' where no_bankout = '$no_bankout' and file_name = '$file_name'";
$execute = mysqli_query($conn2,$sql);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Cancel';
}

mysqli_close($conn2);

?>