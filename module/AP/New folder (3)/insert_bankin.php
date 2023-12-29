<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$referen =  $_POST['referen'];
$nomor_coa = $_POST['nomor_coa'];
$cost_ctr = $_POST['cost_ctr'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curre = $_POST['curre'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$keterangan = $_POST['keterangan'];



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

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO tbl_bankin (no_doc,id_coa,id_cost_center,buyer,no_ws,curr,t_debit,t_credit,keterangan) 
VALUES 
	('$doc_number', '$nomor_coa', '$cost_ctr', '$buyer', '$no_ws', '$curre', '$debit', '$credit', '$keterangan')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>