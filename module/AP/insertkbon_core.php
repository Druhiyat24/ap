<?php
// ============================================================================
// insertkbon_core.php — SHARED CORE simpan PV Regular (header + per-BPB).
// DIPAKAI HANYA oleh insertkbon_bulk.php (bulk 1-transaksi, all-or-nothing).
//
// PENTING (anti-drift): body kedua fungsi di bawah adalah SALINAN VERBATIM dari
//   - pv_reg_save_header() <- insertkbon_h_pv_regular.php
//   - pv_reg_save_bpb()    <- insertkbon.php
// File asli SENGAJA tidak diubah (dipakai banyak form kontrabon lain). Kalau ada
// perubahan logika/jurnal di file asli, SALIN JUGA ke sini (atau regen gen_core.php).
// Perbedaan yang disengaja vs asli: (1) tak ada include conn / mysqli_close,
// (2) IR-guard 'return' bukan echo+exit, (3) return ['ok'=>..] untuk deteksi rollback.
// ============================================================================
require_once __DIR__ . '/bpb_docinfo_guard.php';

if (!function_exists('pv_reg_save_header')) {
function pv_reg_save_header($conn1, $conn2) {

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
$potongan_ppn = isset($_POST['potongan_ppn']) && $_POST['potongan_ppn'] !== '' ? $_POST['potongan_ppn'] : 0;
$potongan_pph = isset($_POST['potongan_pph']) && $_POST['potongan_pph'] !== '' ? $_POST['potongan_pph'] : 0;
$status_invoice = 'Invoiced';
$mattype = $_POST['mattype'];
$matclass = $_POST['matclass'];
$n_code_category = $_POST['n_code_category'];
$cus_ctg = $_POST['cus_ctg'];
$profit_center = $_POST['profit_center'];
$ir_number = $_POST['ir_number'];
$bank_account_raw = isset($_POST['bank_account']) ? $_POST['bank_account'] : '';
$id_bank_account = ($bank_account_raw !== '' && $bank_account_raw !== '-') ? (int) $bank_account_raw : 'NULL';
$from_account = mysqli_real_escape_string($conn2, isset($_POST['from_account']) ? $_POST['from_account'] : '');
$from_bank = mysqli_real_escape_string($conn2, isset($_POST['from_bank']) ? $_POST['from_bank'] : '');
$from_bank_curr = mysqli_real_escape_string($conn2, isset($_POST['from_bank_curr']) ? $_POST['from_bank_curr'] : '');

// GUARD: 1 IR hanya boleh dipakai 1 PV aktif. Kalau IR ini sudah dipakai PV lain
// (kontrabon_h) yang belum Cancel -> tolak total (belum ada yang di-insert).
// IR baru bisa dipakai lagi setelah PV yang memakainya di-cancel.
if ($ir_number !== null && $ir_number !== '' && $ir_number !== '-') {
	$ire = mysqli_real_escape_string($conn2, $ir_number);
	$chkIr = mysqli_query($conn2, "SELECT no_kbon FROM kontrabon_h WHERE ir_number = '$ire' AND status <> 'Cancel' LIMIT 1");
	if ($chkIr && mysqli_num_rows($chkIr) > 0) {
		$uKb = mysqli_fetch_assoc($chkIr);
		return ['ok' => false, 'ir_used' => ($uKb['no_kbon'] ?? '')];
	}
}




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

if ($potongan_ppn == '') {
	$potongan_ppn1 = '0';
}else{
	$potongan_ppn1 = $potongan_ppn;
}

if ($potongan_pph == '') {
	$potongan_pph1 = '0';
}else{
	$potongan_pph1 = $potongan_pph;
}

$ttl_kbon = (($sub_h + $lr_kurs1 + $s_qty1 + $s_harga1 + $materai1 + $ekspedisi1 + $moq1) - $pot_beli1) + ($tax_h) - $jml_return;

$sqlno = mysqli_query($conn1,"select CONCAT(
	'PV-AP/REG/$profit_center/',
	DATE_FORMAT(CURRENT_DATE(), '%Y'), '/',
	DATE_FORMAT(CURRENT_DATE(), '%m'), '/',
	LPAD(
	COALESCE(MAX(CAST(RIGHT(no_kbon, 5) AS UNSIGNED)), 0) + 1,
	5, '0'
	)
) nomor from kontrabon_h WHERE YEAR(tgl_kbon) = YEAR ('$tgl_kbon_h')");
$rowno = mysqli_fetch_array($sqlno);
$kode = isset($rowno['nomor']) ? $rowno['nomor'] : 0;

$queryss = "INSERT INTO potongan (no_kbon, tgl_kbon, nama_supp, jml_return, lr_kurs, s_qty, s_harga, materai, pot_beli, ekspedisi, moq, jml_potong, potongan_ppn, potongan_pph, status)
VALUES
('$kode','$tgl_kbon_h', '$nama_supp_h', '$jml_return', '$lr_kurs1', '$s_qty1', '$s_harga1', '$materai1', '$pot_beli1', '$ekspedisi1', '$moq1', '$jml_potong1', '$potongan_ppn1', '$potongan_pph1', '$status')";
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
$idr_ekspedisi 		= $ekspedisi1 * $rate;
$idr_moq 			= $moq1 * $rate;
$idr_potongan_ppn 	= $potongan_ppn1 * $rate;

$kata1 = "KONTRABON";
// $supp = $nama_supp.toUpperCase();

$keter = $kata1 ." ". $nama_supp_h;

if ($profit_center == 'NAG') {
	$no_cc = 'DEP24SUB001';
	$nama_cc = 'MANAGEMENT FACTORY';
}else{
	$no_cc = 'DEPNK01SUB001';
	$nama_cc = 'KNITTING PRODUCTION';
}

//coa labarugi kurs
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

//coa koreksi ppn
// if ($potongan_ppn1 == 0) {

// }else{
// 	if ($potongan_ppn1 >= 1) {
// 		// positif = tambah PPN → debit
// 		$querypotppn = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
// 		VALUES
// 		('$kode', '$create_date', 'AP - Kontrabon', '5.97.02', 'BEBAN SELISIH HARGA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '$potongan_ppn1', '0', '$idr_potongan_ppn', '0', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

// 		$executepotppn = mysqli_query($conn2,$querypotppn);
// 	}else{
// 		// negatif = kurangi PPN → credit
// 		$potongan_ppn2 = abs($potongan_ppn1);
// 		$idr_potongan_ppn2 = $potongan_ppn2 * $rate;

// 		$querypotppn = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
// 		VALUES
// 		('$kode', '$create_date', 'AP - Kontrabon', '5.97.02', 'BEBAN SELISIH HARGA', '$no_cc', '$nama_cc', '-', '', '-', '-', '$curr_h', '$rate', '0', '$potongan_ppn2', '0', '$idr_potongan_ppn2', 'Draft', '$keter', '$create_user_h', '$create_date', '', '', '', '', '$profit_center')";

// 		$executepotppn = mysqli_query($conn2,$querypotppn);
// 	}
// }

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
}else{

}


// $sqlx = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
// $rowx = mysqli_fetch_array($sqlx);
// $maxid = $rowx['id'];

// $sqly = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
// $rowy = mysqli_fetch_array($sqly);
// $rate = $rowy['rate'];
// $tglrate = $rowy['tanggal'];

$pph_h1 = $pph_h * $rate;

if($curr_h == 'IDR'){

	$query = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code,no_coa,nama_coa, profit_center,ir_number, id_bank_account, from_account, from_bank, from_bank_curr)
	VALUES
	('$kode', '$tgl_kbon_h', '$no_po_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$pph_h', '1', '$total_h', '$dp_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date', '$tgl_kbon_s', '$unik_code', '$no_coa_cre', '$nama_coa_cre', '$profit_center', '$ir_number', $id_bank_account, '$from_account', '$from_bank', '$from_bank_curr')";
	$execute = mysqli_query($conn2,$query);

} else{

	$query = "INSERT INTO kontrabon_h ( no_kbon, tgl_kbon, no_po, nama_supp, no_faktur, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, pph_idr, rate, pph_fgn, total, dp_value, balance, curr, post_date, update_date, status, create_user, create_date, tgl_kbon2, unik_code,no_coa,nama_coa, profit_center, ir_number, id_bank_account, from_account, from_bank, from_bank_curr)
	VALUES
	('$kode', '$tgl_kbon_h', '$no_po_h', '$nama_supp_h', '$no_faktur_h', '$supp_inv_h', '$tgl_inv_h', '$tgl_tempo_h', '$sub_h', '$tax_h', '$pph_h1',  '$rate', '$pph_h', '$total_h', '$dp_h', '$balance', '$curr_h', '$post_date', '$update_date', '$status', '$create_user_h', '$create_date', '$tgl_kbon_s', '$unik_code', '$no_coa_cre', '$nama_coa_cre', '$profit_center', '$ir_number', $id_bank_account, '$from_account', '$from_bank', '$from_bank_curr')";
	$execute = mysqli_query($conn2,$query);

}


	return ['ok' => ($execute !== false), 'kode' => $kode];
}
}

if (!function_exists('pv_reg_save_bpb')) {
function pv_reg_save_bpb($conn1, $conn2) {

$no_kbon = $_POST['no_kbon'];
$unik_code = $_POST['unik_code'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$jurnal = $_POST['jurnal'];
$nama_supp = $_POST['nama_supp'];
$no_faktur = $_POST['no_faktur'];
$supp_inv = $_POST['supp_inv'];
$tgl_inv = date("Y-m-d",strtotime($_POST['tgl_inv']));
$tgl_tempo = date("Y-m-d",strtotime($_POST['tgl_tempo']));
$pph = $_POST['pph'];
$cash = $_POST['cash'];
$ttl_ro = $_POST['ttl_ro'];
$no_bppb = $_POST['no_bppb'];
$idtax = $_POST['idtax'];
$curr = $_POST['curr'];
$ceklist = $_POST['ceklist'];
$create_date = date("Y-m-d H:i:s");
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 2;
$create_user = $_POST['create_user'];
$no_bpb = $_POST['no_bpb'];
$no_po = $_POST['no_po'];
$no_ro = $_POST['no_ro'];
$tgl_bpb = date("Y-m-d",strtotime($_POST['tgl_bpb']));
$tgl_po = date("Y-m-d",strtotime($_POST['tgl_po']));
$sum_sub = $_POST['sum_sub'];
$sum_tax = $_POST['sum_tax'];
$sum_dp = $_POST['sum_dp'];
$sum_pph = $_POST['sum_pph'];
$sum_total = $sum_sub - $sum_pph + $sum_tax;
$sum_dpp = $sum_sub + $sum_tax;
$status_invoice = 'Invoiced';
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$status_update = 'Cancel';
$mattype = $_POST['mattype'];
$matclass = $_POST['matclass'];
$n_code_category = $_POST['n_code_category'];
$cus_ctg = $_POST['cus_ctg'];

$no_ftr = $_POST['no_ftr'];
$no_po_ftr = $_POST['no_po_ftr'];
$tgl_po_ftr = date("Y-m-d",strtotime($_POST['tgl_po_ftr']));
$no_pi_ftr = $_POST['no_pi_ftr'];
$ttl_ftr = $_POST['ttl_ftr'];
$curr_ftr = $_POST['curr_ftr'];
$kbon_ftr = $_POST['kbon_ftr'];
$tglkbon_ftr = date("Y-m-d",strtotime($_POST['tglkbon_ftr']));
$lp_ftr = $_POST['lp_ftr'];
$tgllp_ftr = date("Y-m-d",strtotime($_POST['tgllp_ftr']));
$pv_ftr = $_POST['pv_ftr'];
$bankout_ftr = $_POST['bankout_ftr'];
$bankoutdate_ftr = date("Y-m-d",strtotime($_POST['bankoutdate_ftr']));
$coa_ftr = $_POST['coa_ftr'];

$profit_center = $_POST['profit_center'];

// ============================================================================
// No Faktur & Tgl Faktur PER-BPB dari kontrabon baru (ir_kontrabon_faktur).
// Lama: no_faktur diambil dari field header (alur BPB baru selalu kosong) &
// tgl_faktur tidak ada sama sekali. Sekarang: ambil faktur milik BPB ini pada
// IR terpilih. Kalau ketemu -> pakai untuk kolom no_faktur (override header)
// dan tgl_faktur. Kalau tidak (mis. IR lama non-kontrabon) -> perilaku lama.
$ir_number  = isset($_POST['ir_number']) ? trim($_POST['ir_number']) : '';
$tgl_faktur = '';
$faktur_found = false;
$in_ir = false; // apakah no_bpb ini sudah termasuk set asli IR (ir_kontrabon_bpb)?
if ($ir_number !== '' && $ir_number !== '-' && $no_bpb !== '') {
	$ire = mysqli_real_escape_string($conn2, $ir_number);
	$nbe = mysqli_real_escape_string($conn2, $no_bpb);
	$chkIr = mysqli_query($conn2, "SELECT b.id FROM ir_kontrabon_bpb b
		JOIN ir_kontrabon_h h ON h.unik_code = b.unik_code
		WHERE h.doc_number = '$ire' AND b.no_bpb = '$nbe' AND h.status <> 'Cancel' LIMIT 1");
	$in_ir = ($chkIr && mysqli_num_rows($chkIr) > 0);
	$rf = mysqli_query($conn2, "SELECT f.no_faktur, f.tgl_faktur
		FROM ir_kontrabon_bpb b
		JOIN ir_kontrabon_h h ON h.unik_code = b.unik_code
		JOIN ir_kontrabon_faktur f ON f.id = b.faktur_id
		WHERE h.doc_number = '$ire' AND b.no_bpb = '$nbe' AND h.status <> 'Cancel' LIMIT 1");
	if ($rf && ($fr = mysqli_fetch_assoc($rf))) {
		if (!empty($fr['no_faktur']))  { $no_faktur  = $fr['no_faktur']; $faktur_found = true; }
		if (!empty($fr['tgl_faktur']) && $fr['tgl_faktur'] !== '0000-00-00') { $tgl_faktur = $fr['tgl_faktur']; $faktur_found = true; }
	}
}
// UI form PV mengirim No Faktur & Tgl Faktur per-BPB (auto-fill dari IR atau diisi
// manual, minimal '-'). Kalau ada -> prioritaskan di atas hasil lookup.
$no_faktur_in  = isset($_POST['no_faktur_in'])  ? trim($_POST['no_faktur_in'])  : '';
$tgl_faktur_in = isset($_POST['tgl_faktur_in']) ? trim($_POST['tgl_faktur_in']) : '';
if ($no_faktur_in !== '') { $no_faktur = $no_faktur_in; $faktur_found = true; }
if ($tgl_faktur_in !== '' && $tgl_faktur_in !== '-') {
	$ts = strtotime($tgl_faktur_in);
	if ($ts) { $tgl_faktur = date('Y-m-d', $ts); $faktur_found = true; }
} elseif ($tgl_faktur_in === '-') {
	$tgl_faktur = ''; // '-' bukan tanggal valid -> NULL di kolom DATE
}
// Fragmen untuk jurnal: faktur_pajak (varchar) & tgl_faktur_pajak (DATE; NULL bila kosong).
$fp_esc  = mysqli_real_escape_string($conn2, $no_faktur);
$tfp_sql = ($tgl_faktur !== '') ? "'" . mysqli_real_escape_string($conn2, $tgl_faktur) . "'" : "NULL";

// echo $no_ro;

$sql123 = mysqli_query($conn2,"select no_kbon, no_bpb from kontrabon where no_kbon = '$no_kbon' and no_bpb = '$no_bpb' and status != 'Cancel'");
$row123 = mysqli_fetch_array($sql123);
$dup_kbon = $row123['no_kbon'];
$dup_bpb = $row123['no_bpb'];

$sqlnkb = mysqli_query($conn2,"select no_kbon from kontrabon_h where unik_code = '$unik_code'");
$rownkb = mysqli_fetch_array($sqlnkb);
$kode = $rownkb['no_kbon'];


$kata1 = "KONTRABON";
// $supp = $nama_supp.toUpperCase();

$keter = $kata1 ." ". $nama_supp;


if ($dup_kbon != null && $dup_bpb == $no_bpb) {
	echo '';
}else{

	$sql1 = mysqli_query($conn2,"select no_kbon from kontrabon_dp where no_bpb = '$no_bpb'");
	while($row = mysqli_fetch_array($sql1)){
		$kbon = $row['no_kbon'];

		if($sum_dp != '0'){
			echo '';
		}else{
			$sql11 = "update ftr_dp set status='$status_update' where no_po='$no_po'";
			$query11 = mysqli_query($conn2,$sql11);

			$sql111 = "update kontrabon_dp set status='$status_update' where no_po='$no_po'";
			$query111 = mysqli_query($conn2,$sql111);

			$sql1111 = "update list_payment_dp set status='$status_update' where no_po='$no_po'";
			$query1111 = mysqli_query($conn2,$sql1111);

			$sql11111 = "update kontrabon_h_dp set status='$status_update' where no_kbon='$kbon'";
			$query11111 = mysqli_query($conn2,$sql11111);

		}
	}


	if($no_ftr != '' ){
		$query_ftr = "INSERT INTO kontrabon_ftr (no_kbon, tgl_kbon, nama_supp, no_ftr, no_po, tgl_po,no_pi, curr, total_ftr, no_pv, no_bankout, tgl_bankout, no_coa, status, created_by, created_date) 
		VALUES 
		('$kode', '$tgl_kbon', '$nama_supp', '$no_ftr', '$no_po_ftr', '$tgl_po_ftr', '$no_pi_ftr', '$curr_ftr', '$ttl_ftr', '$pv_ftr', '$bankout_ftr', '$bankoutdate_ftr', '$coa_ftr', '$status', '$create_user', '$create_date')";

		$execute_ftr = mysqli_query($conn2,$query_ftr);

		$sqlcoa_ftr = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where no_coa = '$coa_ftr' Limit 1");
		$rowcoa_ftr = mysqli_fetch_array($sqlcoa_ftr);
		$no_coa_ftr = $rowcoa_ftr['no_coa'];
		$nama_coa_ftr = $rowcoa_ftr['nama_coa'];

		if ($curr_ftr != 'IDR') {
			$sqlftr = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$bankoutdate_ftr' and v_codecurr = 'PAJAK' and curr = '$curr_ftr'");
			$rowftr = mysqli_fetch_array($sqlftr);
			$rate_ftr = isset($rowftr['rate']) ? $rowftr['rate'] : 1;
		}else{
			$rate_ftr = 1;
		}

		$ttl_ftr_idr = $ttl_ftr * $rate_ftr;

		$jurnal_ftr = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '$no_coa_ftr', '$nama_coa_ftr', '-', '-', '$no_ftr', '', '-', '-', '$curr_ftr', '$rate_ftr', '0', '$ttl_ftr', '0', '$ttl_ftr_idr', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

		$execute_jurnal_ftr = mysqli_query($conn2,$jurnal_ftr);
	}


	if(isset($ceklist) && $no_bpb != '' ){	
		$query = "INSERT INTO kontrabon (no_kbon, tgl_kbon, id_jurnal, nama_supp, no_faktur, no_bpb, no_po, tgl_bpb,tgl_po, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, idtax, pph_code, pph_value, total, dp_value, curr, ceklist, post_date, update_date, status, status_int, create_user, create_date, start_date, end_date) 
		VALUES 
		('$kode', '$tgl_kbon', '$jurnal', '$nama_supp', '$no_faktur', '$no_bpb', '$no_po', '$tgl_bpb', '$tgl_po', '$supp_inv', '$tgl_inv', '$tgl_tempo', '$sum_sub', '$sum_tax', '$idtax', '$pph', '$sum_pph', '$sum_total', '$sum_dp', '$curr', '$ceklist', '$post_date', '$update_date', '$status', '$status_int', '$create_user', '$create_date', '$start_date', '$end_date')";
		$execute = mysqli_query($conn2,$query);
		// Simpan Tgl Faktur per-BPB (kolom baru `tgl_faktur`). Aman: kalau kolom
		// belum ada, query gagal diam-diam tanpa menghentikan proses (no_faktur
		// tetap tersimpan lewat INSERT di atas).
		if ($tgl_faktur !== '') {
			@mysqli_query($conn2, "UPDATE kontrabon SET tgl_faktur = '" . mysqli_real_escape_string($conn2, $tgl_faktur) . "'
				WHERE no_kbon = '" . mysqli_real_escape_string($conn2, $kode) . "' AND no_bpb = '" . mysqli_real_escape_string($conn2, $no_bpb) . "'");
		}
		$squerys = mysqli_query($conn2,"update bppb_new set is_invoiced = '$status_invoice', no_kbon = '$no_kbon' where no_ro= '$no_ro'");

		if ($curr != 'IDR') {
			$sqlx = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$tgl_bpb' and v_codecurr = 'PAJAK'");
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

		$idr_sub = $sum_dpp * $rate;
		$idr_tax = $sum_tax * $rate;
		$idr_ttl_ro = $ttl_ro * $rate;

		$sqlcoa = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where cus_ctg like '%$cus_ctg%' and mattype like '%$mattype%' and matclass like '%$matclass%' and n_code_category like '%$n_code_category%' and inv_type like '%bpb_credit%' Limit 1");
		$rowcoa = mysqli_fetch_array($sqlcoa);
		$no_coa_deb = $rowcoa['no_coa'];
		$nama_coa_deb = $rowcoa['nama_coa'];

		$querykbon1 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '$no_coa_deb', '$nama_coa_deb', '-', '-','$no_bpb', '$tgl_bpb', '-', '-', '$curr', '$rate', '$sum_dpp', '0', '$idr_sub', '0', 'Draft', '$keter', '$create_user', '$create_date', '', '', '', '', '$profit_center')";

		$executekbon1 = mysqli_query($conn2,$querykbon1);

		if ($sum_tax > 0) {
			$sqlcoa3 = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where inv_type like '%PPN MASUKAN%' Limit 1");
			$rowcoa3 = mysqli_fetch_array($sqlcoa3);
			$no_coa_ppn = $rowcoa3['no_coa'];
			$nama_coa_ppn = $rowcoa3['nama_coa'];


			$queryss4 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
			VALUES 
			('$kode', '$create_date', 'AP - Kontrabon', '$no_coa_ppn', '$nama_coa_ppn', '-', '-', '$no_bpb', '$tgl_bpb', '-', '-', '$curr', '$rate', '0', '$sum_tax', '0', '$idr_tax', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

			$executess4 = mysqli_query($conn2,$queryss4);

		}else{

		}

		// Isi No Faktur Pajak & Tgl Faktur Pajak pada baris jurnal BPB ini
		// (GR/IR + PPN Masukan; keduanya reff_doc = no_bpb). Dulu NULL karena
		// jurnal AP - Kontrabon tak pernah mengisi kolom faktur_pajak/tgl_faktur_pajak.
		if ($faktur_found) {
			@mysqli_query($conn2, "UPDATE tbl_list_journal
				SET faktur_pajak = '$fp_esc', tgl_faktur_pajak = $tfp_sql
				WHERE no_journal = '" . mysqli_real_escape_string($conn2, $kode) . "'
				  AND reff_doc = '" . mysqli_real_escape_string($conn2, $no_bpb) . "'
				  AND type_journal = 'AP - Kontrabon'");
		}

		// SYNC BPB TAMBAHAN ke IR: kalau BPB ini diceklis manual (bukan bagian set asli
		// IR) tapi PV memilih IR tsb, tambahkan ke ir_kontrabon_faktur + ir_kontrabon_bpb
		// dengan keterangan "Added in Payment Voucher <no_pv>" supaya kelihatan saat di-
		// view di menu Kontrabon. NILAI INVOICE IR TIDAK DIUBAH (ir_kontrabon_h.total_amount
		// & mirror ir_invoice_supp_h dibiarkan).
		if ($execute && $ir_number !== '' && $ir_number !== '-' && !$in_ir && $no_bpb !== '') {
			$ket_esc = mysqli_real_escape_string($conn2, 'Added in Payment Voucher ' . $kode);
			$ire2 = mysqli_real_escape_string($conn2, $ir_number);
			$hrow = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT h.unik_code,
				(SELECT MIN(id) FROM ir_kontrabon_inv i WHERE i.unik_code = h.unik_code) inv_id
				FROM ir_kontrabon_h h WHERE h.doc_number = '$ire2' AND h.status <> 'Cancel' LIMIT 1"));
			if ($hrow && !empty($hrow['unik_code'])) {
				$ir_unik = mysqli_real_escape_string($conn2, $hrow['unik_code']);
				$ir_inv  = (int) ($hrow['inv_id'] ?? 0);
				$nf_esc  = mysqli_real_escape_string($conn2, $no_faktur);
				$tf_sql  = ($tgl_faktur !== '') ? "'" . mysqli_real_escape_string($conn2, $tgl_faktur) . "'" : "NULL";
				// Faktur: pakai yang sudah ada di IR ini (no_faktur sama) atau buat baru.
				$fid = 0;
				$frow = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT id FROM ir_kontrabon_faktur WHERE unik_code = '$ir_unik' AND no_faktur = '$nf_esc' LIMIT 1"));
				if ($frow) {
					$fid = (int) $frow['id'];
				} elseif (mysqli_query($conn2, "INSERT INTO ir_kontrabon_faktur
					(inv_id, unik_code, no_faktur, tgl_faktur, nama_supplier, dpp, ppn, ppnbm, status_faktur, create_user, create_date)
					VALUES ($ir_inv, '$ir_unik', '$nf_esc', $tf_sql, '" . mysqli_real_escape_string($conn2, $nama_supp) . "', '$sum_sub', '$sum_tax', 0, '', '$create_user', '$create_date')")) {
					$fid = (int) mysqli_insert_id($conn2);
					@mysqli_query($conn2, "UPDATE ir_kontrabon_faktur SET keterangan = '$ket_esc' WHERE id = $fid");
				}
				// BPB: tambahkan ke IR (nilai per-BPB saja; nilai invoice IR tidak berubah).
				if ($fid > 0) {
					$bpb_total = (float) $sum_sub + (float) $sum_tax;
					if (mysqli_query($conn2, "INSERT INTO ir_kontrabon_bpb
						(faktur_id, inv_id, unik_code, no_bpb, no_po, supplier, tgl_bpb, total, dpp, ppn, curr, create_user, create_date)
						VALUES ($fid, $ir_inv, '$ir_unik', '" . mysqli_real_escape_string($conn2, $no_bpb) . "', '" . mysqli_real_escape_string($conn2, $no_po) . "', '" . mysqli_real_escape_string($conn2, $nama_supp) . "', '$tgl_bpb', '$bpb_total', '$sum_sub', '$sum_tax', '$curr', '$create_user', '$create_date')")) {
						@mysqli_query($conn2, "UPDATE ir_kontrabon_bpb SET keterangan = '$ket_esc' WHERE id = " . (int) mysqli_insert_id($conn2));
					}
					// bpb_new juga (dipakai laporan_pembelian): No/Tgl Invoice dari invoice IR
					// (inv_id) + No/Tgl Faktur dari input PV, untuk BPB tambahan ini.
					$inv_no = ''; $inv_tgl_sql = 'NULL';
					if ($ir_inv > 0) {
						$irow = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT no_inv, tgl_inv FROM ir_kontrabon_inv WHERE id = $ir_inv LIMIT 1"));
						if ($irow) {
							$inv_no = $irow['no_inv'] ?? '';
							$inv_tgl_sql = (!empty($irow['tgl_inv']) && $irow['tgl_inv'] !== '0000-00-00') ? "'" . mysqli_real_escape_string($conn2, $irow['tgl_inv']) . "'" : 'NULL';
						}
					}
					$fak_tgl_sql = ($tgl_faktur !== '') ? "'" . mysqli_real_escape_string($conn2, $tgl_faktur) . "'" : 'NULL';
					// GUARD: jangan timpa No/Tgl Invoice/Faktur real yg sudah ada dgn strip "-".
					// Strip hanya mengisi kalau nilai lama masih kosong (lihat bpb_docinfo_guard.php).
					bpbnew_apply_docinfo($conn2, $no_bpb, $ir_number, $inv_no, $inv_tgl_sql, $no_faktur, $fak_tgl_sql);
					// Akumulasi nilai BPB tambahan (dpp+ppn) ke KOLOM TERPISAH di IR:
					// amount_add_pv (PLUS). total_amount (invoice asli) TIDAK diubah.
					// Grand total IR = total_amount + amount_add_pv.
					@mysqli_query($conn2, "UPDATE ir_kontrabon_h SET amount_add_pv = COALESCE(amount_add_pv,0) + " . (float) $bpb_total . " WHERE unik_code = '$ir_unik'");
				}
			}
		}

	}

	if($no_ro != ''){
		$queryess = "INSERT INTO return_kb (no_kbon, no_ro, no_bpbrtn, total_ro, status) 
		VALUES 
		('$kode', '$no_ro', '$no_bppb', '$ttl_ro', '$status')";
		$executeess = mysqli_query($conn2,$queryess);

		$squery_bppb = mysqli_query($conn2,"update bppb_new set no_kbon = '$kode' where no_bppb = '$no_bppb';");

		// RETUR: tulis No/Tgl Faktur (dari input retur) + No/Tgl Invoice (supp_inv PV)
		// ke bppb_new dgn strip-guard (jangan timpa nilai real dgn strip). Dibaca laporan.
		$ret_dok     = ($ir_number !== '' && $ir_number !== '-') ? $ir_number : $kode;
		$ret_inv_tgl = (!empty($_POST['tgl_inv']) && $supp_inv !== '' && $supp_inv !== '-')
			? "'" . mysqli_real_escape_string($conn2, $tgl_inv) . "'" : 'NULL';
		bppbnew_apply_docinfo($conn2, $no_bppb, $ret_dok, $supp_inv, $ret_inv_tgl, $no_faktur, $tfp_sql);

		// RO/retur MENGURANGI kolom terpisah amount_add_pv di IR (MINUS). ttl_ro positif
		// (magnitude) -> dikurangkan. Hanya kalau PV ini memang terkait sebuah IR.
		if ($ir_number !== '' && $ir_number !== '-') {
			@mysqli_query($conn2, "UPDATE ir_kontrabon_h SET amount_add_pv = COALESCE(amount_add_pv,0) - " . (float) $ttl_ro . " WHERE doc_number = '" . mysqli_real_escape_string($conn2, $ir_number) . "' AND status <> 'Cancel'");
		}

		if ($curr != 'IDR') {
			$sqlx = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$tgl_bpb' and v_codecurr = 'PAJAK'");
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

		$idr_sub = $sum_dpp * $rate;
		$idr_tax = $sum_tax * $rate;
		$idr_ttl_ro = $ttl_ro * $rate;

		$kata1 = "KONTRABON";
// $supp = $nama_supp.toUpperCase();

		$keter = $kata1 ." ". $nama_supp;

		$sqlbppb = mysqli_query($conn1,"select tgl_bppb,tax,round(sum((qty*price) * tax / 100),2) tax_ro from bppb_new where no_bppb = '$no_bppb' GROUP BY no_bppb");
		$rowbppb = mysqli_fetch_array($sqlbppb);
		$tgl_bppb = isset($rowbppb['tgl_bppb']) ? $rowbppb['tgl_bppb'] : 0;
		$val_tax_ro = isset($rowbppb['tax']) ? $rowbppb['tax'] : 0;
		$tax_ro = isset($rowbppb['tax_ro']) ? $rowbppb['tax_ro'] : 0;
		$idr_tax_ro = $tax_ro * $rate;



		$sqlcoa = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where cus_ctg like '%$cus_ctg%' and mattype like '%$mattype%' and matclass like '%$matclass%' and n_code_category like '%$n_code_category%' and inv_type like '%bpb_credit%' Limit 1");
		$rowcoa = mysqli_fetch_array($sqlcoa);
		$no_coa_deb = $rowcoa['no_coa'];
		$nama_coa_deb = $rowcoa['nama_coa'];


		$queryss5 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$kode', '$create_date', 'AP - Kontrabon', '$no_coa_deb', '$nama_coa_deb', '-', '-', '$no_bppb', '$tgl_bppb', '-', '-', '$curr', '$rate', '0', '$ttl_ro', '0', '$idr_ttl_ro', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

		$executess5 = mysqli_query($conn2,$queryss5);

		if ($val_tax_ro > 0) {

			$queryss6 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
			VALUES 
			('$kode', '$create_date', 'AP - Kontrabon', '1.52.07', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN (UNBILLED)', '-', '-', '$no_bppb', '$tgl_bppb', '-', '-', '$curr', '$rate', '$tax_ro', '0', '$idr_tax_ro', '0', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

			$executess6 = mysqli_query($conn2,$queryss6);

			$queryss7 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
			VALUES 
			('$kode', '$create_date', 'AP - Kontrabon', '1.52.04', 'PAJAK DIBAYAR DIMUKA PPN MASUKAN', '-', '-', '$no_bppb', '$tgl_bppb', '-', '-', '$curr', '$rate', '0', '$tax_ro', '0', '$idr_tax_ro', 'Draft', '$keter','$create_user', '$create_date', '', '', '', '', '$profit_center')";

			$executess7 = mysqli_query($conn2,$queryss7);

		}

		// No Faktur Pajak & Tgl Faktur Pajak utk baris jurnal RETUR (reff_doc = no_bppb).
		// Blok BPB (di atas) mengisi kolom faktur_pajak untuk reff_doc = no_bpb; baris
		// RETUR (queryss5/6/7) memakai reff_doc = no_bppb sehingga sebelumnya
		// faktur_pajak-nya tak pernah terisi -> No Faktur retur hilang di jurnal.
		if ($faktur_found) {
			@mysqli_query($conn2, "UPDATE tbl_list_journal
				SET faktur_pajak = '$fp_esc', tgl_faktur_pajak = $tfp_sql
				WHERE no_journal = '" . mysqli_real_escape_string($conn2, $kode) . "'
				  AND reff_doc = '" . mysqli_real_escape_string($conn2, $no_bppb) . "'
				  AND type_journal = 'AP - Kontrabon'");
		}

	}else{
		echo '';
	}

	if($execute){

		$squery = mysqli_query($conn2,"update bpb_new set is_invoiced = '$status_invoice' where no_bpb= '$no_bpb'");
		$squerysss = mysqli_query($conn2,"delete from kontrabon where no_bpb = '' and no_po = '' ");
		$sql2 = mysqli_query($conn2,"select no_po from list_payment_cbd where no_po = '$no_po'");
		$row = mysqli_fetch_array($sql2);
		$nopo = $row['no_po'];
		$update_amount = $cash - $sum_dp;
// $sql3 = "update kontrabon_h_cbd set amount_update = '$update_amount' where no_po = '$no_po' and status = 'Approved'";
// $exec = mysqli_query($conn2,$sql3);

		$sql1 = "update kartu_hutang set no_kbon='$no_kbon' where no_bpb = '$no_bpb' and no_po='$no_po'";
		$query1 = mysqli_query($conn2,$sql1);

		$sqlac = "update status set no_kbon='$no_kbon',tgl_kbon = '$tgl_kbon' where no_bpb = '$no_bpb'";
		$queryac = mysqli_query($conn2,$sqlac);
	}
}


	$ok = true;
	if (isset($execute)    && $execute    === false) $ok = false; // INSERT kontrabon (BPB)
	if (isset($executeess) && $executeess === false) $ok = false; // INSERT return_kb (RO)
	return ['ok' => $ok];
}
}
