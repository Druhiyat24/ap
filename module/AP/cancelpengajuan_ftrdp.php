<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'];
$no_cbd = $_POST['no_cbd'];
$no_po = $_POST['no_po'];
$cancel_user = $_POST['cancel_user'];
$cancel_date = date("Y-m-d H:i:s");
$status1 = 'Approved';
$status2 = 'Cancel';
// $status_int = 2;




$sql = mysqli_query($conn2,"select * from pengajuan_ftrdp where id = '$id'");

if($no_po == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$kbon = $row['no_po'];
$payment = $row['no_cbd'];

$sql = "update pengajuan_ftrdp set cancel_user='$cancel_user', tgl_cancel='$cancel_date', status='$status2' where id='$id'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update ftr_dp set status='$status1' where no_ftr_dp = '$payment'";
$query1 = mysqli_query($conn2,$sql1);
header('Refresh:0; url=pengajuan_ftrdp.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

// if(isset($no_kbon)){
// $sql = "update bpb_new inner join kontrabon on kontrabon.no_bpb = bpb_new.no_bpb set bpb_new.is_invoiced = 'Waiting' where kontrabon.no_kbon = '$no_kbon'";
// $execute = mysqli_query($conn2,$sql);	
// }else{
// 	die('Error: ' . mysqli_error());		
// }

// if($execute){
// 	$query = mysqli_query($conn2,"update kontrabon set status = 'Cancel', cancel_user = '$cancel_user', cancel_date = '$cancel_date'  where no_kbon = '$no_kbon'");
// 	$query2 = mysqli_query($conn2,"update kontrabon_h set status = 'Cancel', cancel_user = '$cancel_user', cancel_date = '$cancel_date'  where no_kbon = '$no_kbon'");
// }

// echo 'Data Berhasil Di Cancel';
// header('Refresh:0; url=pengajuankb.php');

// mysqli_close($conn2);

?>
