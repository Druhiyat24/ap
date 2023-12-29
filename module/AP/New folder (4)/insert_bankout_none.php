<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankout = $_POST['no_bankout'];
$tgl_bankout = date("Y-m-d",strtotime($_POST['tgl_bankout']));
$reff_doc = $_POST['reff_doc'];
$no_coa = $_POST['no_coa'];
$no_costcntr = $_POST['no_costcntr'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curr = $_POST['curr'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$deskripsi = $_POST['deskripsi'];


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

$sqlx = mysqli_query($conn2,"select max(id) as id FROM b_bankout_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_bankout) from b_bankout_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_bankout)'];

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO b_bankout_none (no_bankout,tgl_bankout,reff_doc,no_coa,no_costcntr,buyer,no_ws,curr,debit,credit,deskripsi) 
VALUES 
	('$kode', '$tgl_bankout', '$reff_doc', '$no_coa', '$no_costcntr', '$buyer', '$no_ws', '$curr', '$debit', '$credit', '$deskripsi')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>