<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pci = $_POST['no_pci'];
$tgl_pci =  date("Y-m-d",strtotime($_POST['tgl_pci']));
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

$sqlx = mysqli_query($conn2,"select max(id) as id FROM c_petty_cashin_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_pci) from c_petty_cashin_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_pci)'];

if ($t_debit == '' and $t_credit == '') {
	
}else{
$query = "INSERT INTO c_petty_cashin_det (no_pci,tgl_pci,no_coa,reff_doc,reff_date,deskripsi,debit,credit) 
VALUES 
	('$kode', '$tgl_pci', '$id_coa', '$no_reff', '$reff_date', '$deskripsi', '$t_debit', '$t_credit')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>