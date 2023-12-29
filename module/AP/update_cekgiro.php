<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pv = $_POST['no_pv'];
$cekgiro = $_POST['cekgiro'];
$cekdate =  date("Y-m-d",strtotime($_POST['cekdate']));
$update_user = $_POST['update_user'];
$update_date = date("Y-m-d H:i:s");

// echo "< -- >";
// echo $no_kbon;

if(isset($no_pv)){
	$query = "UPDATE tbl_pv_h SET no_cek = '$cekgiro', cek_date = '$cekdate', update_by = '$update_user', update_date = '$update_date' where no_pv = '$no_pv'";

$execute = mysqli_query($conn2,$query);
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>