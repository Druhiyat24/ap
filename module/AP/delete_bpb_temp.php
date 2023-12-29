<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$user = $_POST['user'];
$create_date = date("Y-m-d H:i:s");


if ($user != '') {
	$sql3 = "Delete from tbl_bpb_temp where user_input='$user'";
	$query3 = mysqli_query($conn2,$sql3); 

}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>