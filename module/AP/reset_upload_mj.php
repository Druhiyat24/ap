<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");


$sql = "DELETE from tbl_memorial_journal_temp where create_by = '$create_user'";
   $update = mysqli_query($conn2,$sql);



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	// echo 'Data Saved Successfully With No '; echo $no_mj;
   
}

mysqli_close($conn2);
?>