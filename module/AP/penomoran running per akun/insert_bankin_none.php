<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankin = $_POST['no_bankin'];
$id_coa =  $_POST['id_coa'];
$no_reff = $_POST['no_reff'];
$reff_date =  date("Y-m-d",strtotime($_POST['reff_date']));
$deskripsi = $_POST['deskripsi'];
$t_debit = $_POST['t_debit'];
$t_credit = $_POST['t_credit'];


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

$sqlx = mysqli_query($conn2,"select max(id) as id FROM tbl_bankin_arcollection ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(doc_num) from tbl_bankin_arcollection where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(doc_num)'];

if ($t_debit == '' and $t_credit == '') {
	
}else{
$query = "INSERT INTO b_bankin_none (no_bankin,id_coa,no_reff,reff_date,deskripsi,t_debit,t_credit) 
VALUES 
	('$kode', '$id_coa', '$no_reff', '$reff_date', '$deskripsi', '$t_debit', '$t_credit')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>