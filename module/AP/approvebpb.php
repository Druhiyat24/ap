<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bpb = $_POST['no_bpb'];
$curr = $_POST['curr'];
$pono = $_POST['pono'];
$tgl_bpb =  date("Y-m-d",strtotime($_POST['tgl_bpb']));
$tgl_po = date("Y-m-d",strtotime($_POST['tgl_po']));
$supp = $_POST['supp'];
$total = $_POST['total'];
$confirm_date = date("Y-m-d H:i:s");
$confirm_date2 = date("Y-m-d");
$status = 'GMF-PCH';
$confirm2 = $_POST['approve_user'];
$update_user = $_POST['update_user'];
$strip = '-';
$nol = 0;
$cek = 2;



$sql = mysqli_query($conn2,"select * from bpb_new where no_bpb = '$no_bpb'");

if($no_bpb == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$bpb = $row['no_bpb'];
$id = $row['id'];

$sqlx = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
$rowy = mysqli_fetch_array($sqly);
$rate = $rowy['rate'];
$tglrate = $rowy['tanggal'];

$ttl_konversi = $total * $rate;


if ($curr == 'IDR') {
	$sql2 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, tgl_bpb, no_po, tgl_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, curr, ket, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('$no_bpb', '$tgl_bpb', '$pono', '$tgl_po', '$strip', '$strip', '$strip', '$supp', '$confirm_date2', '$strip', '$curr', 'Purchase', '$nol', '$nol', '$total', '$nol', '$cek') ");
}
else{
	$sql2 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, tgl_bpb, no_po, tgl_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, curr, ket, rate, tgl_rate, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('$no_bpb', '$tgl_bpb', '$pono', '$tgl_po', '$strip', '$strip', '$strip', '$supp', '$confirm_date2', '$strip', '$curr', 'Purchase', '$rate', '$tglrate',  '$total', '$nol', '$ttl_konversi', '$nol', '$cek') ");
}

$sql = "update bpb_new set confirm2='$confirm2', confirm_date='$confirm_date', update_user='$update_user', status='$status' where no_bpb='$bpb' and status = 'GMF'";
$query = mysqli_query($conn2,$sql);

$sql6 = mysqli_query($conn2,"INSERT INTO status (supp, no_bpb, tgl_bpb) 
VALUES 
	('$supp', '$no_bpb', '$tgl_bpb') ");

$sqla = "update bpb set ap_inv='2' where bpbno_int='$no_bpb'";
$querya = mysqli_query($conn1,$sqla);
header('Refresh:0; url=formapprovebpb.php');
}
}


// if ($curr == 'IDR') {
// 	$sql2 = mysqli_query($conn2,"INSERT INTO detail (no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, pph, no_payment, curr, credit_usd, debit_usd, balance_usd, credit_idr, debit_idr, balance_idr,cek) 
// VALUES 
// 	('$no_bpb', '$tgl_bpb', '$pono', '$tgl_po', '$supp', '$strip', '$strip', '$strip', '$curr', '$nol', '$nol', '$nol', '$total', '$nol', '$total', '$cek') ");
// }
// else{
// 	$sql2 = mysqli_query($conn2,"INSERT INTO detail (no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, pph, no_payment, curr, credit_usd, debit_usd, balance_usd, credit_idr, debit_idr, balance_idr, cek) 
// VALUES 
// 	('$no_bpb', '$tgl_bpb', '$pono', '$tgl_po', '$supp', '$strip', '$nol', '$strip', '$curr', '$total', '$nol', '$total', '$ttl_konversi', '$nol', '$ttl_konversi', '$cek') ");
// }

if(!$query) {
	die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);

?>