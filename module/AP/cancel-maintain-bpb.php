<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$no_bpb = $_POST['no_bpb'];
$approve_user = $_POST['approve_user'];
$confirm_date = date("Y-m-d H:i:s");

if(isset($no_bpb)){
	$sql = "update maintain_bpb_h set status = 'CANCEL', cancel_by = '$approve_user', cancel_date = '$confirm_date' where no_maintain = '$no_dok'";
	$execute = mysqli_query($conn2,$sql);

	$sql2 = "update maintain_bpb_det set status = 'N' where no_maintain = '$no_dok'";
	$execute2 = mysqli_query($conn2,$sql2);

	$sql3 = "update bpb a INNER JOIN maintain_bpb_det b ON b.no_bpb = a.bpbno_int SET a.status_maintain = null where b.no_maintain = '$no_dok'";
	$execute3 = mysqli_query($conn2,$sql3);


}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
	echo 'Data Berhasil Di Cancel';
}

mysqli_close($conn2);

?>