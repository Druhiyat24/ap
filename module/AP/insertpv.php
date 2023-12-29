<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$no_coa = $_POST['no_coa'];
$no_cc = $_POST['no_cc'];
$no_ref = $_POST['no_ref'];
$ref_date =  date("Y-m-d",strtotime($_POST['ref_date']));
$deskripsi = $_POST['deskripsi'];
$amount = $_POST['amount'];
$ded_add = $_POST['ded_add'];
$due_date =  date("Y-m-d",strtotime($_POST['due_date']));
$pph = $_POST['pph'];
$idtax = $_POST['idtax'];
$ppn = $_POST['ppn'];
$id_ppn = $_POST['id_ppn'];
$user = $_POST['user'];





echo $deskripsi;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;
// echo "< -- >";
// echo $deskripsi;
// echo "< -- >";
// echo $amount;
// echo "< -- >";
// echo $ded_add;
// echo "< -- >";
// echo $due_date;
// echo "< -- >";
// echo $pph;

if ($amount == '0' && $ded_add == '0') {
	
}else{
$query = "INSERT INTO tbl_pv (no_pv,coa,no_cc,reff_doc,reff_date,deskripsi,amount,due_date,ded_add,pph,id_pph,ppn,id_ppn) 
VALUES 
	('$doc_number', '$no_coa', '$no_cc', '$no_ref', '$ref_date', '$deskripsi', '$amount', '$due_date', '$ded_add', '$pph','$idtax', '$ppn','$id_ppn')";

$execute = mysqli_query($conn2,$query);

$sql = "update memo_h set status='PAYMENT', no_pv='$doc_number' where nm_memo='$no_ref'";
$query = mysqli_query($conn2,$sql);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{

$query2 = "delete from tbl_pv_memo_temp where no_memo = '$no_ref' and user = '$user' ";
$execute2 = mysqli_query($conn2,$query2);

$query2 = "UPDATE memo_h set no_pv = '$doc_number',status='PAYMENT DRAFT' where nm_memo = '$no_ref' ";
$execute2 = mysqli_query($conn2,$query2);
	
}

mysqli_close($conn2);
?>