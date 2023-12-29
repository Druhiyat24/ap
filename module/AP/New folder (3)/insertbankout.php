<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));
$referen = $_POST['referen'];
$nama_supp = $_POST['nama_supp'];
$no_pay = $_POST['no_pay'];
$pay_date =  date("Y-m-d",strtotime($_POST['pay_date']));
$due_date =  date("Y-m-d",strtotime($_POST['due_date']));
$akun = $_POST['akun'];
$bank = $_POST['bank'];
$curr = $_POST['curr'];
$coa = $_POST['coa'];
$cost = $_POST['cost'];
$status = "Draft";



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


$query = "INSERT INTO ap_bankout (doc_number,doc_date,referensi,nama_supp,no_lp,lp_date,due_date,akun,bank,curr,coa,cost_center,  status) 
VALUES 
	('$doc_number', '$doc_date', '$referen', '$nama_supp', '$no_pay', '$pay_date', '$due_date', '$akun', '$bank', '$curr', '$coa', '$cost', '$status')";

$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>