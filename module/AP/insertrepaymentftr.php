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
$no_coa = $_POST['no_coa'];
$no_coc = $_POST['no_coc'];
$reff_doc = $_POST['reff_doc'];
$reff_date = date("Y-m-d",strtotime($_POST['reff_date']));
$deskripsi = $_POST['deskripsi'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$prof_ctr = $_POST['prof_ctr'];

$profit_center = $_POST['profit_center'];
$coa_lp = $_POST['coa_lp'];


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

$t_nominal = $nominal * $rate;

if ($list_payment_id != '') {

	$sqlcoa_lp = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where no_coa = '$coa_lp' Limit 1");
	$rowcoa_lp = mysqli_fetch_array($sqlcoa_lp);
	$nama_coa_lp = $rowcoa_lp['nama_coa'];

	$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
	VALUES 
	('$payment_ftr_id', '$tgl_pelunasan', 'Payment', '$coa_lp', '$nama_coa_lp', '-', '-', '$list_payment_id', '$tgl_list_payment', '-', '-', '$valuta_ftr', '$rate', '$nominal', '0', '$t_nominal', '0', 'Draft', '-', '$create_user', '$create_date', '', '', '', '', '$profit_center')";

	$executess = mysqli_query($conn2,$queryss);

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
}


if ($debit == '' and $credit == '') {
	
}else{

	$sqlcoa_tamb = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where no_coa = '$no_coa' Limit 1");
	$rowcoa_tamb = mysqli_fetch_array($sqlcoa_tamb);
	$nama_coa_tamb = $rowcoa_tamb['nama_coa'];

	$sqlcoc_tamb = mysqli_query($conn1,"SELECT no_cc as code_combine,cc_name as cost_name from b_master_cc where no_cc = '$no_coc' Limit 1");
	$rowcoc_tamb = mysqli_fetch_array($sqlcoc_tamb);
	$nama_coc_tamb = $rowcoc_tamb['cost_name'];

	$t_debit = $debit * $rate;
	$t_credit = $credit * $rate;

	$query_tamb = "INSERT INTO payment_ftr_det (payment_ftr_id,no_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit, profit_center, status) 
	VALUES 
	('$payment_ftr_id', '$no_coa', '$no_coc', '$reff_doc', '$reff_date', '$deskripsi', '$debit', '$credit', '$prof_ctr', '$status')";

	$execute_tamb = mysqli_query($conn2,$query_tamb);

	$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
	VALUES 
	('$payment_ftr_id', '$tgl_pelunasan', 'Payment', '$no_coa', '$nama_coa_tamb', '$no_coc', '$nama_coc_tamb', '$reff_doc', '$reff_date', '-', '-', '$valuta_ftr', '$rate', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', '$prof_ctr')";

	$executess = mysqli_query($conn2,$queryss);
}


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