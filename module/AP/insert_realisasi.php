<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_num = $_POST['doc_num'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));
$no_coa = $_POST['no_coa'];
$no_costcenter = $_POST['no_costcenter'];
$amount = $_POST['amount'];
$deskripsi = $_POST['deskripsi'];
$no_bpb = $_POST['no_bpb'];
$nama_supp = $_POST['nama_supp'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curr = "IDR";


// echo $no_ref;
// echo "< -- >";
// echo $ref_date;


if($amount != ''){
$query = "INSERT INTO c_realisasi (no_rls, tgl_rls, no_coa, no_costcenter, amount_rls, deskripsi, no_bpb, nama_supp, buyer, no_ws, curr) 
VALUES 
	('$doc_num', '$doc_date', '$no_coa', '$no_costcenter', '$amount','$deskripsi', '$no_bpb', '$nama_supp', '$buyer', '$no_ws','$curr')";

$execute = mysqli_query($conn2,$query);
}else{
	
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>