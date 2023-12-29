<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;

if(isset($no_bpb)){
$sql = "update bpb set is_invoiced = 'Waiting' where no_bpb = '$no_bpb'";
$execute = mysqli_query($conn2,$sql);	
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
	$query = mysqli_query($conn2,"delete from kontrabon where no_bpb = '$no_bpb'");
}

mysqli_close($conn2);

?>