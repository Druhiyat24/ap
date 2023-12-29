<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));
$referen = $_POST['referen'];
$nama_supp = $_POST['nama_supp'];
$akun = $_POST['akun'];
$bank = $_POST['bank'];
$curr = $_POST['curr'];
$coa = $_POST['coa'];
$cost = $_POST['cost'];
$nominal = $_POST['nominal'];
$rate = $_POST['rate'];
$eqv_idr = $_POST['eqv_idr'];
$pesan = $_POST['pesan'];
$status = "Draft";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");


// echo "< -- >";
// echo $no_kbon;
// echo "< -- >";
// echo $tgl_kbon;
// echo "< -- >";
// echo $valuta_ftr;
// echo "< -- >";
// echo $ttl_bayar;
// echo "< -- >";
// echo $cara_bayar;
// echo "< -- >";
// echo $account;
// echo "< -- >";
// echo $bank;
// echo "< -- >";


$query = "INSERT INTO ap_bankout_h (doc_number,doc_date,referensi,nama_supp,akun,bank,curr,coa,cost_center, nominal,rate,eqv_idr,outstanding,keterangan, status,create_by,create_date) 
VALUES 
	('$doc_number', '$doc_date', '$referen', '$nama_supp', '$akun', '$bank', '$curr', '$coa', '$cost', '$nominal', '$rate', '$eqv_idr', '$nominal', '$pesan', '$status', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>