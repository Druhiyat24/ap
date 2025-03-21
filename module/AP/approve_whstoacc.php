<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$no_bpb = $_POST['no_bpb'];
$approve_user = $_POST['approve_user'];
$confirm_date = date("Y-m-d H:i:s");

if(isset($no_dok)){
$sql = "update ir_trans_bpb set status = 'Approved', approved_by = '$approve_user', approved_date = '$confirm_date' where no_transfer = '$no_dok' and no_bpb = '$no_bpb'";
$execute = mysqli_query($conn2,$sql);

}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
}

mysqli_close($conn2);

?>