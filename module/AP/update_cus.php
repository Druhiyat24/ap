<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi = $_POST['no_bi'];
$customer = $_POST['customer'];
$update_user = $_POST['update_user'];
$update_date = date("Y-m-d H:i:s");

// echo "< -- >";
// echo $no_kbon;

if(isset($no_bi)){
	$query = "UPDATE tbl_bankin_arcollection SET customer = '$customer', update_by = '$update_user', update_date = '$update_date' where doc_num = '$no_bi'";

$execute = mysqli_query($conn2,$query);
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>