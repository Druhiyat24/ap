<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];
$jml_cek = $_POST['jml_cek'];
$create_date = date("Y-m-d H:i:s");


$query = mysqli_query($conn2,"INSERT INTO ap_log_verif (user_verif, jml_ceklis, tgl_verif) 
VALUES 
	('$create_user', '$jml_cek', '$create_date') ");

if(!$query){
    die('Error: ' . mysqli_error());	
}

//echo 'Data Berhasil Di Simpan';	

mysqli_close($conn2);

//if($query){
//	echo '<script type="text/javascript">alert("Data has been submitted");</script>';
//}else {
//	echo '<script type="text/javascript">alert("Error");</script>';
//}	
?>