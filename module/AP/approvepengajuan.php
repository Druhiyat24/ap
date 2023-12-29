<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id = $_POST['id'];
$no_kbon = $_POST['no_kbon'];
$no_bpb = $_POST['no_bpb'];
$confirm_date = date("Y-m-d H:i:s");
$status1 = 'draft';
$status2 = 'Approved';
$status_int = 2;
$approve_user = $_POST['approve_user'];
$status_invoice = 'Waiting';

$sql = mysqli_query($conn2,"select * from pengajuan_kb where id = '$id'");

if($no_kbon == ''){
	echo '';
}else{
$row= mysqli_fetch_assoc($sql);
$kbon = $row['no_kbon'];
$sql4 = mysqli_query($conn2,"select no_bpb from kontrabon where no_kbon = '$kbon'");
while($row4= mysqli_fetch_assoc($sql4)) {
$bpb = $row4['no_bpb'];

$sql = "update pengajuan_kb set approved_user='$approve_user',tgl_approved='$confirm_date', status='$status2' where id='$id'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon set confirm_user='$approve_user', confirm_date='$confirm_date', status='$status1', status_int='$status_int' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);

// $query5 = mysqli_query($conn2,"update kontrabon_h set status = 'Cancel', cancel_user = '$cancel_user', cancel_date = '$cancel_date'  where no_kbon = '$no_kbon'");

// $sql2 = "update bpb_new set is_invoiced = '$status_invoice' where no_bpb= '$bpb'";
// $query2 = mysqli_query($conn2,$sql2);	

// $query4 = mysqli_query($conn2,"update bppb_new set is_invoiced = 'Waiting', no_kbon = null where no_kbon = '$no_kbon'");
// $sql111 = "update kartu_hutang set no_kbon='-' where no_kbon = '$no_kbon'";
// $query111 = mysqli_query($conn2,$sql111);


header('Refresh:0; url=pengajuankb.php');
}
}

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>