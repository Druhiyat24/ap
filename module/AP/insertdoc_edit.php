<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$unik_code = $_POST['unik_code'];
$data = $_POST['data'];

// echo "< -- >";
// echo $no_kbon;

$query2 = "delete from supp_doc_edit where unik_code != '$unik_code' ";
$execute2 = mysqli_query($conn2,$query2);

if(isset($data)){
	$query = "INSERT INTO supp_doc_edit (ref_doc,ket,unik_code) 
VALUES 
	('$doc_number', '$data', '$unik_code')";

$execute = mysqli_query($conn2,$query);
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>