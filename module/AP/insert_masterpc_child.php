<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$text_parentpc = $_POST['text_parentpc'];
$txt_nochild = $_POST['txt_nochild'];
$txt_namechild = $_POST['txt_namechild'];
$txt_deschild = $_POST['txt_deschild'];
$status = "Active";
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];





// echo $text_parentpc;
// echo "< -- >";
// echo $txt_nochild;
// echo "< -- >";
// echo $txt_namechild;
// echo "< -- >";
// echo $txt_deschild;


$query = "INSERT INTO master_pc_child (id_pc,id_pc_child,nama_pc,deskripsi,status) 
VALUES 
	('$text_parentpc', '$txt_nochild', '$txt_namechild', '$txt_deschild', '$status')";

$execute = mysqli_query($conn2,$query);



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>