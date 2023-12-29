<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_num = $_POST['doc_num'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));
$no_ca = $_POST['no_ca'];
$total_ca = $_POST['total_ca'];
$subtotal = $_POST['subtotal'];
$total_cash = $_POST['total_cash'];
$curr = "IDR";
$status = "Draft";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$from_ip = $_POST['from_ip'];
$aktivitas = "Create Realization";





// echo $doc_number;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;


if($total_ca != ''){
$query = "INSERT INTO c_realisasi_h (no_rls, tgl_rls, no_ca, total_ca, subtotal, total_cash, curr, status, create_by, create_date) 
VALUES 
	('$doc_num', '$doc_date', '$no_ca', '$total_ca', '$subtotal','$total_cash', '$curr', '$status', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);


$queryss = "INSERT INTO tbl_log_cash (nama_user,activitas,from_pc,log_date,doc_num,doc_date) 
VALUES 
	('$create_user', '$aktivitas', '$from_ip', '$create_date', '$doc_num', '$doc_date')";

$executess = mysqli_query($conn2,$queryss);
}else{
	
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>