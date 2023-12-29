<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$payment_ftr_id = $_POST['payment_ftr_id'];
$tgl_pelunasan = date("Y-m-d",strtotime($_POST['tgl_pelunasan']));
$nama_supp = $_POST['nama_supp'];
$list_payment_id = $_POST['list_payment_id'];
$tgl_list_payment = date("Y-m-d",strtotime($_POST['tgl_list_payment']));
$no_kbon = $_POST['no_kbon'];
$tgl_kbon =  date("Y-m-d",strtotime($_POST['tgl_kbon']));
$valuta_ftr = $_POST['valuta_ftr'];
$ttl_bayar = $_POST['ttl_bayar'];
$cara_bayar = $_POST['cara_bayar'];
$account = $_POST['account'];
$bank = $_POST['bank'];
$valuta_bayar = $_POST['valuta_bayar'];
$nominal = $_POST['nominal'];
$rate = $_POST['rate'];
$nomrate = $_POST['nomrate'];
$ratebpb = $_POST['ratebpb'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 5;
$keterangan ='Paid';
$dibayar = $_POST['dibayar'];
$confirm_date2 = date("Y-m-d");
$cek = 3;
$nol = 0;


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
// echo $valuta_bayar;
// echo "< -- >";
// echo $nominal;
// echo "< -- >";
// echo $rate;
// echo "< -- >";
// echo $nomrate;
// echo "< -- >";
// // echo $create_user;
// // echo "< -- >";
// // echo $create_date;
// // echo "< -- >";
// // echo $status;
// // echo "< -- >";
// // echo $status_int;
// // echo "< -- >";
// // echo $keterangan;
// // echo "< -- >";
// // echo $dibayar;
// // echo "< -- >";
// // echo $confirm_date2;
// // echo "< -- >";
// // echo $cek;
// // echo "< -- >";
// // echo $nol;
// // echo "< -- >";
// echo $rate1;
// echo "< -- >";
// echo $nomrate1;
// echo "< -- >";
// // echo "< -- >";
$sqlx = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
$rowy = mysqli_fetch_array($sqly);
$rates = $rowy['rate'];
$tglrate = $rowy['tanggal'];

if ($rate == '') {
	$rate = 0;
}else{
	$rate = $rate;
}

if ($valuta_bayar != '') {
	$valuta_bayar1 = $valuta_bayar;
}else{
	$valuta_bayar1 = $valuta_ftr;
}

if ($nominal == '') {
	$nominal = 0;
}else{
	$nominal = $nominal;
}

if ($rate == '' || $rate == '1') {
	$rate1 = $rates;
	$nomrate1 = $nominal * $rate1;
	$nomrate2 = $nominal * $ratebpb;
	$skurs = $nomrate1 - $nomrate2;
	$kurs1 = abs($skurs);
}else{
	$rate1 = $rate;
	$nomrate1 = $nomrate;
	$nomrate2 = $nominal * $ratebpb;
	$skurs = $nomrate1 - $nomrate2;
	$kurs1 = abs($skurs);
}

if ($valuta_ftr == 'IDR') {	
$query = "INSERT INTO payment_ftr (payment_ftr_id, tgl_pelunasan, nama_supp, list_payment_id, tgl_list_payment, no_kbon, tgl_kbon, valuta_ftr, ttl_bayar, cara_bayar, account, bank, valuta_bayar, nominal, nominal_fgn, rate, status, keterangan, create_user, create_date) 
VALUES 
	('$payment_ftr_id', '$tgl_pelunasan', '$nama_supp', '$list_payment_id', '$tgl_list_payment', '$no_kbon', '$tgl_kbon', '$valuta_ftr', '$ttl_bayar', '$cara_bayar', '$account', '$bank', '$valuta_bayar1', '$nominal', '$nomrate', '$rate', '$status', '$keterangan', '$create_user', '$create_date')";

	$sql2 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('-', '-', '-', '-', '-', '$nama_supp', '$confirm_date2', '$list_payment_id', '$tgl_list_payment', '$valuta_ftr', 'Payment', '$nol', '$nol', '$nol', '$nominal', '$cek') ");

$execute = mysqli_query($conn2,$query);
}else{
	$query = "INSERT INTO payment_ftr (payment_ftr_id, tgl_pelunasan, nama_supp, list_payment_id, tgl_list_payment, no_kbon, tgl_kbon, valuta_ftr, ttl_bayar, cara_bayar, account, bank, valuta_bayar, nominal, nominal_fgn, rate, status, keterangan, create_user, create_date) 
VALUES 
	('$payment_ftr_id', '$tgl_pelunasan', '$nama_supp', '$list_payment_id', '$tgl_list_payment', '$no_kbon', '$tgl_kbon', '$valuta_ftr', '$ttl_bayar', '$cara_bayar', '$account', '$bank', '$valuta_bayar1', '$nomrate', '$nominal', '$rate', '$status', '$keterangan', '$create_user', '$create_date')";

	$sql2 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, rate, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('-', '-', '-', '-', '-', '$nama_supp', '$confirm_date2', '$list_payment_id', '$tgl_list_payment', '$valuta_ftr', 'Payment', '$rate1', '$nol', '$nominal', '$nol', '$nomrate1', '$cek') ");

	if ($skurs >= '0') {

		$sql3 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, rate, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('-', '-', '-', '-', '-', '$nama_supp', '$confirm_date2', '$list_payment_id', '$tgl_list_payment', '$valuta_ftr', 'Selisih Kurs', '$rate1', '$nol', '$nol', '$kurs1', '$nol', '$cek') ");

	}else{

	$sql3 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, rate, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('-', '-', '-', '-', '-', '$nama_supp', '$confirm_date2', '$list_payment_id', '$tgl_list_payment', '$valuta_ftr', 'Selisih Kurs', '$rate1', '$nol', '$nol', '$nol', '$kurs1', '$cek') ");
}

	$execute = mysqli_query($conn2,$query);
}

// echo $valuta_bayar1;
// echo "< -- >";
// echo $rate1;
// echo "< -- >";
// echo $nomrate2;
// echo "< -- >";
// echo $ratebpb;
// echo "< -- >";
// echo $skurs;
// echo "< -- >";
// echo $kurs1;

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	if(strpos($list_payment_id, 'LP/NAG/') !== false) {
	$sql = "update list_payment set status_int = '$status_int' where no_payment= '$list_payment_id'";
	$exec = mysqli_query($conn2,$sql);

	$sqlac = "update status set no_pay ='$payment_ftr_id', tgl_pay = '$tgl_pelunasan' where no_lp = '$list_payment_id'";
	$queryac = mysqli_query($conn2,$sqlac);
}else{
	$sql = "update saldo_awal set status_int = '$status_int' where no_pay= '$list_payment_id'";
	$exec = mysqli_query($conn2,$sql);

	$queryac = mysqli_query($conn2,"INSERT INTO status (supp, no_lp, tgl_lp, no_pay, tgl_pay) 
VALUES 
	('$nama_supp', '$list_payment_id', '$tgl_list_payment', '$payment_ftr_id', '$tgl_pelunasan') ");
}
}

mysqli_close($conn2);
?>