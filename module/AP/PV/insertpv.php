<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$no_coa = $_POST['no_coa'];
$no_ref = $_POST['no_ref'];
$ref_date =  date("Y-m-d",strtotime($_POST['ref_date']));
$deskripsi = $_POST['deskripsi'];
$amount = $_POST['amount'];
$ded_add = $_POST['ded_add'];
$due_date =  date("Y-m-d",strtotime($_POST['due_date']));
$pph = $_POST['pph'];
$idtax = $_POST['idtax'];





// echo $doc_number;
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
$query = "INSERT INTO tbl_pv (no_pv,coa,reff_doc,reff_date,deskripsi,amount,due_date,ded_add,pph,id_pph) 
VALUES 
	('$doc_number', '$no_coa', '$no_ref', '$ref_date', '$deskripsi', '$amount', '$due_date', '$ded_add', '$pph','$idtax')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>