<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$txt_noparent = $_POST['txt_noparent'];
$txt_nameparent = $_POST['txt_nameparent'];
$txt_desparent = $_POST['txt_desparent'];
$status = "Active";
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];





// echo $txt_group;
// echo "< -- >";
// echo $txt_indname;
// echo "< -- >";
// echo $txt_engname;
// echo "< -- >";
// echo $status;


$query = "INSERT INTO master_pc (id_pc,nama_pc,keterangan,status) 
VALUES 
	('$txt_noparent', '$txt_nameparent', '$txt_desparent', '$status')";

$execute = mysqli_query($conn2,$query);



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>