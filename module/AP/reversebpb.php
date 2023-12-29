<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bpb = $_POST['no_bpb'];
$confirm_date = date("Y-m-d H:i:s");
$status = 'Cancel';
$update_user = $_POST['update_user'];
$update_date = date("Y-m-d H:i:s");

$sql = mysqli_query($conn2,"select * from bpb_new where no_bpb = '$no_bpb'");

if($no_bpb == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$bpb = $row['no_bpb']; 
$query = mysqli_query($conn2,"update bpb_new set reverse_date = '$update_date', reverse_user= '$update_user', status= '$status' where no_bpb= '$bpb'");
header('Refresh:0; url=formreversebpb.php');

$sqla = "update bpb set ap_inv = null where bpbno_int='$no_bpb'";
$querya = mysqli_query($conn1,$sqla);
}
}

if(!$query){
	die('Error: ' . mysqli_error());		
}

mysqli_close($conn2);

?>