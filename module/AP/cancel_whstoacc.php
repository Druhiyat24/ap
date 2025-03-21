<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$no_bpb = $_POST['no_bpb'];
$approve_user = $_POST['approve_user'];
$confirm_date = date("Y-m-d H:i:s");

if(isset($no_dok)){
$sql = "update ir_trans_bpb set status = 'Cancel', cancel_by = '$approve_user', cancel_date = '$confirm_date' where no_transfer = '$no_dok' and no_bpb = '$no_bpb'";
$execute = mysqli_query($conn2,$sql);

$sql2 = "update bpb a INNER JOIN ir_trans_bpb b ON b.no_bpb = a.bpbno_int SET a.stat_trf = null where b.no_transfer = '$no_transfer'";
$execute2 = mysqli_query($conn2,$sql2);

}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Cancel';
}

mysqli_close($conn2);

?>