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
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 5;
$keterangan ='Paid';
$dibayar = $_POST['dibayar'];

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
	$nomrate2 = $nominal * $rates;
	$skurs = $nomrate1 - $nomrate2;
	$kurs1 = abs($skurs);
}else{
	$rate1 = $rate;
	$nomrate1 = $nomrate;
	$nomrate2 = $nominal * $rates;
	$skurs = $nomrate1 - $nomrate2;
	$kurs1 = abs($skurs);
}



$sql11 = mysqli_query($conn2,"select list_payment_cbd.nama_supp as supp, list_payment_cbd.no_kbon as no_kbon, list_payment_cbd.tgl_kbon as tgl_kbon, list_payment_cbd.no_po as no_po, list_payment_cbd.tgl_po as tgl_po, list_payment_cbd.curr as curr, list_payment_cbd.no_payment as no_payment, list_payment_cbd.tgl_payment as tgl_payment, list_payment_cbd.amount as amount, kontrabon_cbd.no_faktur as no_faktur, kontrabon_cbd.supp_inv as supp_inv, kontrabon_cbd.tgl_inv as tgl_inv from list_payment_cbd INNER JOIN kontrabon_cbd on kontrabon_cbd.no_kbon = list_payment_cbd.no_kbon where list_payment_cbd.no_payment = '$list_payment_id' and list_payment_cbd.status_int = '4' and kontrabon_cbd.status = 'Approved'");
$row = mysqli_fetch_array($sql11);
$supp = $row['supp'];
$kbon = $row['no_kbon'];
$kbondate = $row['tgl_kbon'];
$no_po = $row['no_po'];
$tgl_po = $row['tgl_po'];
$no_pay = $row['no_payment'];
$tgl_pay = $row['tgl_payment'];
$faktur = $row['no_faktur'];
$supp_inv = $row['supp_inv'];
$tgl_inv = $row['tgl_inv'];
$amount = $row['amount'];
$curr = $row['curr'];
$confirm_date2 = date("Y-m-d");
$nol = 0;
$cek = 1;
$strip = '-';


if ($valuta_ftr == 'IDR') {	
$query = "INSERT INTO payment_ftrcbd (payment_ftr_id, tgl_pelunasan, nama_supp, list_payment_id, tgl_list_payment, no_kbon, tgl_kbon, valuta_ftr, ttl_bayar, cara_bayar, account, bank, valuta_bayar, nominal, nominal_fgn, rate, status, keterangan, create_user, create_date) 
VALUES 
	('$payment_ftr_id', '$tgl_pelunasan', '$nama_supp', '$list_payment_id', '$tgl_list_payment', '$no_kbon', '$tgl_kbon', '$valuta_ftr', '$ttl_bayar', '$cara_bayar', '$account', '$bank', '$valuta_bayar1', '$nominal', '$nomrate', '$rate', '$status', '$keterangan', '$create_user', '$create_date')";

	$sql2 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, tgl_po, no_kbon, tgl_kbon, supp_inv, tgl_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('$strip', '$no_po', '$tgl_po', '$kbon', '$kbondate', '$supp_inv', '$tgl_inv', '$faktur', '$supp', '$confirm_date2', '$no_pay', '$tgl_pay', '$curr', 'Payment CBD', '$nol', '$nol', '$nol', '$amount', '$cek') ");

$execute = mysqli_query($conn2,$query);
}else{
	$query = "INSERT INTO payment_ftrcbd (payment_ftr_id, tgl_pelunasan, nama_supp, list_payment_id, tgl_list_payment, no_kbon, tgl_kbon, valuta_ftr, ttl_bayar, cara_bayar, account, bank, valuta_bayar, nominal, nominal_fgn, rate, status, keterangan, create_user, create_date) 
VALUES 
	('$payment_ftr_id', '$tgl_pelunasan', '$nama_supp', '$list_payment_id', '$tgl_list_payment', '$no_kbon', '$tgl_kbon', '$valuta_ftr', '$ttl_bayar', '$cara_bayar', '$account', '$bank', '$valuta_bayar1', '$nomrate', '$nominal', '$rate', '$status', '$keterangan', '$create_user', '$create_date')";

	$sql2 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, tgl_po, no_kbon, tgl_kbon, supp_inv, tgl_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, rate, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('$strip', '$no_po', '$tgl_po', '$kbon', '$kbondate', '$supp_inv', '$tgl_inv', '$faktur', '$supp', '$confirm_date2', '$no_pay', '$tgl_pay', '$curr', 'Payment CBD', '$rate1', '$nol', '$amount', '$nol', '$nomrate1', '$cek') ");

	if ($skurs >= '0') {

		$sql3 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, rate, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('-', '-', '-', '-', '-', '$supp', '$confirm_date2', '$no_pay', '$tgl_pay', '$curr', 'Selisih Kurs', '$rate1', '$nol', '$nol', '$kurs1', '$nol', '$cek') ");

	}else{

	$sql3 = mysqli_query($conn2,"INSERT INTO kartu_hutang (no_bpb, no_po, no_kbon, supp_inv, no_faktur, nama_supp, create_date, no_payment, tgl_payment, curr, ket, rate, credit_usd, debit_usd, credit_idr, debit_idr, cek) 
VALUES 
	('-', '-', '-', '-', '-', '$supp', '$confirm_date2', '$no_pay', '$tgl_pay', '$curr', 'Selisih Kurs', '$rate1', '$nol', '$nol', '$nol', '$kurs1', '$cek') ");
}

	$execute = mysqli_query($conn2,$query);
}

// echo $rate1;
// echo "||";
// echo $nomrate1;
// echo "||";
// echo $nomrate2;
// echo "||";
// echo $skurs;
// echo "||";
// echo $kurs1;

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql = "update list_payment_cbd set status_int = '$status_int' where no_payment= '$list_payment_id'";
	$exec = mysqli_query($conn2,$sql);
}

mysqli_close($conn2);
?>