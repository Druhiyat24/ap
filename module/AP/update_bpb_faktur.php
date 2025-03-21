<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bpb = $_POST['no_bpb'];
$no_inv = $_POST['no_inv'];
$tgl_inv =  date("Y-m-d",strtotime($_POST['tgl_inv']));
$no_faktur = $_POST['no_faktur'];
$tgl_faktur =  date("Y-m-d",strtotime($_POST['tgl_faktur']));
$user = $_POST['user'];
$update_date = date("Y-m-d H:i:s");

// echo "< -- >";
// echo $no_kbon;

if(isset($no_bpb)){
	$query = "UPDATE tbl_bpb_temp SET no_inv = '$no_inv', tgl_inv = '$tgl_inv', no_faktur = '$no_faktur', tgl_faktur = '$tgl_faktur' where no_bpb = '$no_bpb'";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>