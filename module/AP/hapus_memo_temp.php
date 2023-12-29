<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$user = $_POST['user'];

// echo "< -- >";
// echo $no_kbon;

	$query = "delete from tbl_pv_memo_temp where user = '$user'";

$execute = mysqli_query($conn2,$query);




if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>