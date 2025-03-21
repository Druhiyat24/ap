<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];

$query = "delete from req_dn_po_detail_temp where created_by = '$create_user'";

$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>