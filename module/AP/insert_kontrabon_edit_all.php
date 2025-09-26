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
$sub_h      = $_POST['sub_h']      ?? 0;
$tax_h      = $_POST['tax_h']      ?? 0;
$dp_h       = $_POST['dp_h']       ?? 0;
$total_h    = $_POST['total_h']    ?? 0;
$balance    = $total_h;
$jml_return = $_POST['jml_return'] ?? 0;
$lr_kurs    = $_POST['lr_kurs']    ?? 0;
$s_qty      = $_POST['s_qty']      ?? 0;
$s_harga    = $_POST['s_harga']    ?? 0;
$materai    = $_POST['materai']    ?? 0;
$pot_beli   = $_POST['pot_beli']   ?? 0;
$ekspedisi  = $_POST['ekspedisi']  ?? 0;
$moq        = $_POST['moq']        ?? 0;
$jml_potong = $_POST['jml_potong'] ?? 0;
$status_invoice = 'Invoiced';
$mattype = $_POST['mattype'];
$matclass = $_POST['matclass'];
$n_code_category = $_POST['n_code_category'];
$cus_ctg = $_POST['cus_ctg'];
$profit_center = $_POST['profit_center'];




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

$ttl_kbon = (($sub_h + $lr_kurs1 + $s_qty1 + $s_harga1 + $materai1 + $ekspedisi1 + $moq1) - $pot_beli1) + $tax_h - $jml_return;

$sqlno = mysqli_query($conn1,"select CONCAT(
	'SI/APR/', '$profit_center', '/',
	DATE_FORMAT(CURRENT_DATE(), '%Y'), '/',
	DATE_FORMAT(CURRENT_DATE(), '%m'), '/',
	LPAD(
	CAST(SUBSTRING_INDEX('$no_kbon_h', '/', -1) AS UNSIGNED),
	5, '0'
	),
	'-REV_',
	LPAD(
	COALESCE(
	MAX(
	CAST(SUBSTRING_INDEX(no_kbon, '-REV_', -1) AS UNSIGNED)
	), 
	0
	) + 1,
	2, '0'
	)
	) AS new_nomor
	FROM kontrabon_h
	WHERE no_kbon LIKE CONCAT(SUBSTRING_INDEX('$no_kbon_h', '-REV_', 1), '%')");
$rowno = mysqli_fetch_array($sqlno);
$kode = isset($rowno['new_nomor']) ? $rowno['new_nomor'] : 0;

$queryss = "INSERT INTO potongan (no_kbon, tgl_kbon, nama_supp, jml_return, lr_kurs, s_qty, s_harga, materai, pot_beli, ekspedisi, moq, jml_potong, status)
VALUES 
('$kode','$tgl_kbon_h', '$nama_supp_h', '$jml_return', '$lr_kurs1', '$s_qty1', '$s_harga1', '$materai1', '$pot_beli1', '$ekspedisi1', '$moq1', '$jml_potong1', '$status')";
$executess = mysqli_query($conn2,$queryss);

if ($curr_h != 'IDR') {
	$sqlx = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$create_date' and v_codecurr = 'PAJAK'");
	$rowx = mysqli_fetch_array($sqlx);
	$h_rate = isset($rowx['rate']) ? $rowx['rate'] : 0;

	if($h_rate == 0){
		$sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = (select max(id) as id FROM masterrate where v_codecurr = 'PAJAK') and v_codecurr = 'PAJAK'");
		$rowy = mysqli_fetch_array($sqly);
		$rate = $rowy['rate'];
		$tglrate = $rowy['tanggal'];
	}else{
		$rate = $h_rate;
	}
}else{
	$rate = 1;
}

$idr_total_h 		= $ttl_kbon * $rate;
$idr_tax_h 			= $tax_h * $rate;
$idr_kurs 			= $lr_kurs1 * $rate;
$idr_qty 			= $s_qty1 * $rate;
$idr_harga 			= $s_harga1 * $rate;
$idr_materai 		= $materai1 * $rate;
$idr_pot_beli 		= $pot_beli1 * $rate;
$idr_ekspedisi 	= $ekspedisi1 * $rate;
$idr_moq 			= $moq1 * $rate;

$kata1 = "KONTRABON";

$keter = $kata1 ." ". $nama_supp_h;

if ($profit_center == 'NAG') {
	$no_cc = 'DEP24SUB001';
	$nama_cc = 'MANAGEMENT FACTORY';
}else{
	$no_cc = 'DEPNK01SUB001';
	$nama_cc = 'KNITTING PRODUCTION';
}

if ($lr_kurs1 == 0) {
	
}else{
	if ($lr_kurs1 >=1 ) {
		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '8.52.02', 'LABA / (RUGI) SELISIH KURS BELUM TEREALISASI', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$lr_kurs1', '0', '$idr_kurs', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '','$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}else{
		$lr_kurs2 =abs($lr_kurs1);
		$idr_kurs2 = $lr_kurs2 * $rate;

		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '8.52.02', 'LABA / (RUGI) SELISIH KURS BELUM TEREALISASI', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$lr_kurs2', '0', '$idr_kurs2', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}
}

//coa selisih qty
if ($s_qty1 == 0) {
	
}else{
	if ($s_qty1 >=1 ) {
		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.03', 'BEBAN SELISIH KUANTITAS', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$s_qty1', '0', '$idr_qty', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}else{
		$s_qty2 =abs($s_qty1);
		$idr_qty2 = $s_qty2 * $rate;

		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.03', 'BEBAN SELISIH KUANTITAS', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$s_qty2', '0', '$idr_qty2', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}
}

//coa selisih harga
if ($s_harga1 == 0) {
	
}else{
	if ($s_harga1 >=1 ) {
		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.02', 'BEBAN SELISIH HARGA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$s_harga1', '0', '$idr_harga', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}else{
		$s_harga2 =abs($s_harga1);
		$idr_harga2 = $s_harga2 * $rate;

		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.02', 'BEBAN SELISIH HARGA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$s_harga2', '0', '$idr_harga2', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}
}

//coa materai
if ($materai1 == 0) {
	
}else{
	if ($materai1 >=1 ) {
		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.99', 'BEBAN PABRIK LAINNYA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$materai1', '0', '$idr_materai', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}else{
		$materai2 =abs($materai1);
		$idr_materai2 = $materai2 * $rate;

		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.99', 'BEBAN PABRIK LAINNYA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$materai2', '0', '$idr_materai2', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}
}

//coa ekspedisi
if ($ekspedisi1 == 0) {
	
}else{
	if ($ekspedisi1 >=1 ) {
		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.84.03', 'BEBAN EKSPEDISI ANGKUTAN', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$ekspedisi1', '0', '$idr_ekspedisi', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}else{
		$ekspedisi2 =abs($ekspedisi1);
		$idr_ekspedisi2 = $ekspedisi2 * $rate;

		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.84.03', 'BEBAN EKSPEDISI ANGKUTAN', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$ekspedisi2', '0', '$idr_ekspedisi2', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}
}

//coa moq
if ($moq1 == 0) {
	
}else{
	if ($moq1 >=1 ) {
		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.99', 'BEBAN PABRIK LAINNYA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$moq1', '0', '$idr_moq', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}else{
		$moq2 =abs($moq1);
		$idr_moq2 = $moq2 * $rate;

		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.99', 'BEBAN PABRIK LAINNYA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$moq2', '0', '$idr_moq2', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}
}

//coa potongan beli
if ($pot_beli1 == 0) {
	
}else{
	if ($pot_beli1 >=1 ) {
		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.02', 'BEBAN SELISIH HARGA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$pot_beli1', '0', '$idr_pot_beli', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}else{
		$pot_beli2 =abs($pot_beli1);
		$idr_pot_beli2 = $pot_beli2 * $rate;

		$querykurs = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '5.97.02', 'BEBAN SELISIH HARGA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$pot_beli2', '0', '$idr_pot_beli2', '0', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

		$executekurs = mysqli_query($conn2,$querykurs);
	}
}


$sqlcoa = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where cus_ctg like '%$cus_ctg%' and mattype like '%$mattype%' and matclass like '%$matclass%' and n_code_category like '%$n_code_category%' and inv_type like '%kbn_credit%' Limit 1");
$rowcoa = mysqli_fetch_array($sqlcoa);
$no_coa_cre = $rowcoa['no_coa'];
$nama_coa_cre = $rowcoa['nama_coa'];

$queryjrnl = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
VALUES 
('$kode', '$create_date', 'AP - Kontrabon', '$no_coa_cre', '$nama_coa_cre', '-', '-', '-', '', '-', '-', '$curr_h', '$rate', '0', '$ttl_kbon', '0', '$idr_total_h', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

$executejrnl = mysqli_query($conn2,$queryjrnl);

$dp_h_idr = $dp_h * $rate;

if ($dp_h != 0) {

	$queryjrnl_dp = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
	VALUES 
	('$kode', '$create_date', 'AP - Kontrabon', '$no_coa_cre', '$nama_coa_cre', '-', '-', '-', '', '-', '-', '$curr_h', '$rate', '$dp_h', '0', '$dp_h_idr', '0', 'Draft', '$keter',  '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

	$executejrnl_dp = mysqli_query($conn2,$queryjrnl_dp);
}

if ($tax_h >= 1) {
	$sqlcoa3 = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where inv_type like '%PPN KBN%' Limit 1");
	$rowcoa3 = mysqli_fetch_array($sqlcoa3);
	$no_coa_ppn = $rowcoa3['no_coa'];
	$nama_coa_ppn = $rowcoa3['nama_coa'];


	$queryss4 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
	VALUES 
	('$kode', '$create_date', 'AP - Kontrabon', '$no_coa_ppn', '$nama_coa_ppn', '-', '-', '$no_faktur_h', '', '-', '-', '$curr_h', '$rate', '$tax_h', '0', '$idr_tax_h', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

	$executess4 = mysqli_query($conn2,$queryss4);
}


$pph_h1 = $pph_h * $rate;

if($curr_h == 'IDR'){

	$query = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code,no_coa,nama_coa, profit_center)
	VALUES 
	('$kode', '$tgl_kbon_h', '$no_po_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$pph_h', '1', '$total_h', '$dp_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date', '$tgl_kbon_s', '$unik_code', '$no_coa_cre', '$nama_coa_cre', '$profit_center')";
	$execute = mysqli_query($conn2,$query);

} else{

	$query = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, pph_fgn, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code,no_coa,nama_coa, profit_center)
	VALUES 
	('$kode', '$tgl_kbon_h', '$no_po_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$pph_h1',  '$rate', '$pph_h', '$total_h', '$dp_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date', '$tgl_kbon_s', '$unik_code', '$no_coa_cre', '$nama_coa_cre', '$profit_center')";
	$execute = mysqli_query($conn2,$query);

}

$query_log = "INSERT INTO ap_edit_kontrabon_log ( no_kbon, no_kbon_new, created_by, created_date)
VALUES 
('$no_kbon_h', '$kode', '$create_user_h', '$create_date')";
$execute_log = mysqli_query($conn2,$query_log);

$jurnal_balik = mysqli_query($conn2,"INSERT into tbl_list_journal select '', no_journal, '$create_date' tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, status, keterangan, create_by, create_date, '$create_user_h' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$no_kbon_h'");


$sqlcopy1 = mysqli_query($conn2,"insert into kontrabon select '', '$kode' no_kbon, '$tgl_kbon_h' tgl_kbon, id_jurnal, nama_supp, no_faktur, no_bpb, no_po, tgl_bpb, tgl_po, supp_inv, tgl_inv, subtotal, tax, total, balance, dp_value, curr, ceklist, tgl_tempo, idtax, pph_code, pph_value, post_date, update_date, 'draft' status, status_int, create_user, confirm_user, confirm_date, '$create_date' create_date, cancel_date, cancel_user, start_date, end_date, lp_inv from ap_edit_kontrabon where no_kbon = '$no_kbon_h'");

$sqlcopy2 = mysqli_query($conn2,"insert into kontrabon_ftr select '', '$kode' no_kbon, '$tgl_kbon_h' tgl_kbon, nama_supp, no_ftr, no_po, tgl_po, no_pi, curr, total_ftr, no_pv, no_bankout, tgl_bankout, no_coa, 'draft' status, created_by, '$create_date' created_date from ap_edit_kontrabon_ftr where no_kbon = '$no_kbon_h'");

$sqlcopy3 = mysqli_query($conn2,"insert into return_kb select '', '$kode' no_kbon, no_ro, no_bpbrtn, total_ro, 'draft' status from ap_edit_return_kb where no_kbon = '$no_kbon_h'");

$sqlcopy4 = mysqli_query($conn2,"insert into tbl_list_journal select '', '$kode' no_journal, '$create_date' tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, 'draft' status, keterangan, create_by, '$create_date' create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from ap_journal_temp where no_journal = '$no_kbon_h'");


$uptquery1 = mysqli_query($conn2,"update kontrabon set status = 'Updated' where no_kbon= '$no_kbon_h'");
$uptquery2 = mysqli_query($conn2,"update kontrabon_h set status = 'Updated' where no_kbon= '$no_kbon_h'");
$uptquery3 = mysqli_query($conn2,"update kontrabon_ftr set status = 'Updated' where no_kbon= '$no_kbon_h'");
$uptquery4 = mysqli_query($conn2,"update return_kb set status = 'Updated' where no_kbon= '$no_kbon_h'");
$uptquery5 = mysqli_query($conn2,"update potongan set status = 'Updated' where no_kbon= '$no_kbon_h'");

$delquery1 = mysqli_query($conn2,"delete from ap_edit_kontrabon where no_kbon= '$no_kbon_h'");
$delquery2 = mysqli_query($conn2,"delete from ap_edit_kontrabon_h where no_kbon= '$no_kbon_h'");
$delquery3 = mysqli_query($conn2,"delete from ap_edit_kontrabon_ftr where no_kbon= '$no_kbon_h'");
$delquery4 = mysqli_query($conn2,"delete from ap_edit_return_kb where no_kbon= '$no_kbon_h'");
$delquery5 = mysqli_query($conn2,"delete from ap_edit_potongan where no_kbon= '$no_kbon_h'");
$delquery6 = mysqli_query($conn2,"delete from ap_journal_temp where no_journal= '$no_kbon_h'");

$uptquery6 = mysqli_query($conn2,"update bpb_new a INNER JOIN kontrabon b ON b.no_bpb = a.no_bpb SET a.is_invoiced = 'Waiting' where b.no_kbon = '$no_kbon_h'");

$uptquery6 = mysqli_query($conn2,"update bpb_new a INNER JOIN kontrabon b ON b.no_bpb = a.no_bpb SET a.is_invoiced = 'Invoiced' where b.no_kbon = '$kode'");


if($execute){

	echo 'Data Saved Successfully With No Kontrabon '; echo $kode;

}else{
   // die('Error: ' . mysql_error());	
}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>