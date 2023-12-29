<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_ro = $_POST['no_ro'];
$confirm_date = date("Y-m-d H:i:s");
$status = 'GMF-PCH';
$confirm2 = $_POST['approve_user'];
$update_user = $_POST['update_user'];

$sql = mysqli_query($conn2,"select no_ro, no_bppb, tgl_bppb, no_bpb, supplier,sum(qty * price) as ttl from bppb_new where no_ro = '$no_ro'");

if($no_ro == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$ro = $row['no_ro'];
$no_bppb = $row['no_bppb'];
$tgl_bppb = $row['tgl_bppb'];
$no_bpb = $row['no_bpb'];
$supplier = $row['supplier'];
$ttl = $row['ttl'];

$sql = "update bppb_new set confirm2='$confirm2', confirm_date='$confirm_date', update_user='$update_user', status='$status' where no_ro='$ro' and status = 'GMF'";
$query = mysqli_query($conn2,$sql);

$query = "INSERT INTO ttl_bppb (no_ro, no_bppb, bppbdate, no_bpb, supp, total) 
VALUES 
	('$ro', '$no_bppb', '$tgl_bppb', '$no_bpb', '$supplier', '$ttl')";
$execute = mysqli_query($conn2,$query);

$sqla = "update bppb set ap_inv='2' where bpbno_ro='$ro'";
$querya = mysqli_query($conn1,$sqla);
header('Refresh:0; url=formapprovebppb.php');
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