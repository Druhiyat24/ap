<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$supp = $_POST['supp'];
$curr = $_POST['curr'];
$no_po = $_POST['no_po'];
$total = $_POST['total'];
$tgl_po =date("Y-m-d",strtotime($_POST['tgl_po']));
$confirm_date = date("Y-m-d H:i:s");
$status = 'Approved';
$status_int = 4;
$approve_user = $_POST['approve_user'];
// $strip = '-';
// $nol = 0;
// $nilai = 14300;
// $ttl_konversi = $total * $nilai;
// $cek = 3;

$sql = mysqli_query($conn2,"select * from kontrabon_dp where no_kbon = '$no_kbon'");

if($no_kbon == ''){
	echo '';
}else{
while($row= mysqli_fetch_assoc($sql)) {
$kbon = $row['no_kbon'];

$sql = "update kontrabon_dp set confirm_user='$approve_user', confirm_date='$confirm_date', status='$status', status_int = '$status_int' where no_kbon='$kbon'";
$query = mysqli_query($conn2,$sql);

$sql1 = "update kontrabon_h_dp set confirm_user='$approve_user', confirm_date='$confirm_date', status='$status' where no_kbon='$kbon'";
$query1 = mysqli_query($conn2,$sql1);
header('Refresh:0; url=kontrabonftrdp.php');
}
}
// if ($curr == 'IDR') {
// 	$sql2 = mysqli_query($conn2,"INSERT INTO detail (no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, pph, no_payment, curr, credit_usd, debit_usd, balance_usd, credit_idr, debit_idr, balance_idr, cek) 
// VALUES 
// 	('$no_bpb', '$tgl_bpb', '$no_po', '$tgl_po', '$supp', '$no_kbon', '$tgl_kbon', '$nol', '$strip', '$curr', '$nol', '$nol', '$nol', '$total', '$nol', '$total', '$cek') ");
// }
// else{
// 	$sql2 = mysqli_query($conn2,"INSERT INTO detail (no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, pph, no_payment, curr, credit_usd, debit_usd, balance_usd, credit_idr, debit_idr, balance_idr, cek) 
// VALUES 
// 	('$no_bpb', '$tgl_bpb', '$no_po', '$tgl_po', '$supp', '$no_kbon', '$tgl_kbon', '$nol', '$strip', '$curr', '$total', '$nol', '$total', '$ttl_konversi', '$nol', '$ttl_konversi', '$cek') ");
// }

if(!$query) {
	die('Error: ' . mysqli_error());	
}
mysqli_close($conn2);

?>