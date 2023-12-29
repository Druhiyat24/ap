<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pv = $_POST['no_pv'];
$txt_reverse = $_POST['txt_reverse'];
$update_user = $_POST['update_user'];
$update_date = date("Y-m-d H:i:s");
$status = 'Draft';
// echo "< -- >";
// echo $no_kbon;

if($no_pv != ''){
	$query = "UPDATE tbl_pv_h SET status = '$status', user_reverse = '$update_user', reverse_date = '$update_date', notes_reverse = '$txt_reverse' where no_pv = '$no_pv'";

$execute = mysqli_query($conn2,$query);
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>