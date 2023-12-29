<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon_h = $_POST['no_kbon_h'];
$unik_code = $_POST['unik_code'];
$tgl_kbon_h = date("Y-m-d",strtotime($_POST['tgl_kbon_h']));
$tgl_kbon_s = date("Y-m-d",strtotime($_POST['tgl_kbon_s']));
$no_po_h = $_POST['no_po_h'];
$nama_supp_h = $_POST['nama_supp_h'];
$no_faktur_h = $_POST['no_faktur_h'];
$supp_inv_h = $_POST['supp_inv_h'];
$tgl_inv_h = date("Y-m-d",strtotime($_POST['tgl_inv_h']));
$tgl_tempo_h = date("Y-m-d",strtotime($_POST['tgl_tempo_h']));
$pph_h = $_POST['pph_h'];
$curr_h = $_POST['curr_h'];
$create_date = date("Y-m-d H:i:s");
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'draft';
$create_user_h = $_POST['create_user_h'];
$sub_h = $_POST['sub_h'];
$tax_h = $_POST['tax_h'];
$dp_h = $_POST['dp_h'];
$total_h = $_POST['total_h'];
$balance = $total_h;
$jml_return = $_POST['jml_return'];
$lr_kurs = $_POST['lr_kurs'];
$s_qty = $_POST['s_qty'];
$s_harga = $_POST['s_harga'];
$materai = $_POST['materai'];
$pot_beli = $_POST['pot_beli'];
$ekspedisi = $_POST['ekspedisi'];
$moq = $_POST['moq'];
$jml_potong = $_POST['jml_potong'];
$status_invoice = 'Invoiced';

if ($lr_kurs == '') {
	$lr_kurs1 = '0';
}else{
	$lr_kurs1 = $lr_kurs;
}

if ($s_qty == '') {
	$s_qty1 = '0';
}else{
	$s_qty1 = $s_qty;
}

if ($s_harga == '') {
	$s_harga1 = '0';
}else{
	$s_harga1 = $s_harga;
}

if ($materai == '') {
	$materai1 = '0';
}else{
	$materai1 = $materai;
}

if ($pot_beli == '') {
	$pot_beli1 = '0';
}else{
	$pot_beli1 = $pot_beli;
}

if ($ekspedisi == '') {
	$ekspedisi1 = '0';
}else{
	$ekspedisi1 = $ekspedisi;
}

if ($moq == '') {
	$moq1 = '0';
}else{
	$moq1 = $moq;
}

if ($jml_potong == '') {
	$jml_potong1 = '0';
}else{
	$jml_potong1 = $jml_potong;
}

$sqlnkb = mysqli_query($conn2,"select max(no_kbon) from kontrabon_h");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(no_kbon)'];
 $urutan = (int) substr($kodeBarang, 15, 5);
 $urutan++;
 $bln = date("m");
 $thn = date("Y");
 $huruf = "SI/APR/$thn/$bln/";
 $kode = $huruf . sprintf("%05s", $urutan);

$queryss = "INSERT INTO potongan (no_kbon, tgl_kbon, nama_supp, jml_return, lr_kurs, s_qty, s_harga, materai, pot_beli, ekspedisi, moq, jml_potong, status)
VALUES 
	('$kode','$tgl_kbon_h', '$nama_supp_h', '$jml_return', '$lr_kurs1', '$s_qty1', '$s_harga1', '$materai1', '$pot_beli1', '$ekspedisi1', '$moq1', '$jml_potong1', '$status')";
$executess = mysqli_query($conn2,$queryss);


$sqlx = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
$rowy = mysqli_fetch_array($sqly);
$rate = $rowy['rate'];
$tglrate = $rowy['tanggal'];

$pph_h1 = $pph_h * $rate;

if($curr_h == 'IDR'){

	$query = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code)
VALUES 
	('$kode', '$tgl_kbon_h', '$no_po_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$pph_h', '1', '$total_h', '$dp_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date', '$tgl_kbon_s', '$unik_code')";
$execute = mysqli_query($conn2,$query);

} else{

	$query = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, pph_fgn, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code)
VALUES 
	('$kode', '$tgl_kbon_h', '$no_po_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$pph_h1',  '$rate', '$pph_h', '$total_h', '$dp_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date', '$tgl_kbon_s', '$unik_code')";
$execute = mysqli_query($conn2,$query);

}
	

if($execute){

	echo 'Data Saved Successfully With No Kontrabon '; echo $kode;

}else{
   // die('Error: ' . mysql_error());	
}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>