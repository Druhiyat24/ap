<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_num = $_POST['doc_num'];
$no_ca = $_POST['no_ca'];
$tgl_ca =  date("Y-m-d",strtotime($_POST['tgl_ca']));
$cc_name = $_POST['cc_name'];
$total = $_POST['total'];
$create_user = $_POST['create_user'];

// echo "< -- >";
// echo $no_kbon;

$query2 = "delete from c_realisasi_temp where no_rls = '$doc_num' ";
$execute2 = mysqli_query($conn2,$query2);

if(isset($no_ca)){
	$query = "INSERT INTO c_realisasi_temp (no_rls, no_ca	, tgl_ca, cc_name, total, create_user) 
VALUES 
	('$doc_num', '$no_ca', '$tgl_ca', '$cc_name', '$total', '$create_user')";

$execute = mysqli_query($conn2,$query);
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>