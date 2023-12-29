<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_user = $_POST['create_user'];
$aktivitas = "Create Cash In";
$from_ip = $_POST['from_ip'];
$create_date = date("Y-m-d H:i:s");
$doc_num = $_POST['doc_num'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));





// echo $doc_number;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;



$query = "INSERT INTO tbl_log_cash (nama_user,activitas,from_pc,log_date,doc_num,doc_date) 
VALUES 
	('$create_user', '$aktivitas', '$from_ip', '$create_date', '$doc_num', '$doc_date')";

$execute = mysqli_query($conn2,$query);



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>