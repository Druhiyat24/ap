<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_doc = $_POST['no_doc'];
$tgl_doc =  date("Y-m-d",strtotime($_POST['tgl_doc']));
$no_cc = $_POST['no_cc'];
$req_by = $_POST['req_by'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curr = "IDR";
$amount = $_POST['amount'];
$deskripsi = $_POST['deskripsi'];
$status = "Draft";
$ambil_ip = $_POST['ambil_ip'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$aktivitas = "Create Cash Advances";





// echo $doc_number;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;


if($amount != ''){
$query = "INSERT INTO c_cash_advances (no_ca, tgl_ca, no_costcenter, req_by, buyer, no_ws, curr, amount, deskripsi, status, create_by, create_date) 
VALUES 
	('$no_doc', '$tgl_doc', '$no_cc', '$req_by', '$buyer','$no_ws', '$curr', '$amount', '$deskripsi', '$status','$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);


$queryss = "INSERT INTO tbl_log_cash (nama_user,activitas,from_pc,log_date,doc_num,doc_date) 
VALUES 
	('$create_user', '$aktivitas', '$ambil_ip', '$create_date', '$no_doc', '$tgl_doc')";

$executess = mysqli_query($conn2,$queryss);
}else{
	
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>