<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');


$no_coa = trim($_POST['no_coa']);
$nama_coa = strtoupper($_POST['nama_coa']);
$nama_ctg1_h = $_POST['nama_ctg1_h'];
$nama_ctg2 = $_POST['nama_ctg2'];
$nama_ctg3_h = $_POST['nama_ctg3_h'];
$nama_ctg4_h = $_POST['nama_ctg4_h'];
$nama_ctg5 = $_POST['nama_ctg5'];
$txt_dirdebit = $_POST['txt_dirdebit'];
$txt_dircredit = $_POST['txt_dircredit'];
$txt_indirect = $_POST['txt_indirect'];
// Flag Bank In / Bank Out tanpa approval (bank_post_rule.php), default perlu approval.
$bankin_no_approve = ($_POST['bankin_no_approve'] ?? 'N') === 'Y' ? 'Y' : 'N';
$bankout_no_approve = ($_POST['bankout_no_approve'] ?? 'N') === 'Y' ? 'Y' : 'N';

/* Kelompok Cost Center yang boleh dipakai COA ini. Dibaca
   get_coa_wajib_cc.php (satu saja 'Y' -> COA wajib isi Cost Center) dan
   getCostCenter.php (menentukan daftar CC yang muncul). Sebelum 10 Nov
   2026 keempatnya hanya bisa diisi lewat SQL.

   DISARING KE 'Y'/'N' DI SINI, bukan dipercayakan ke kiriman layar:
   kolomnya dibaca dgn perbandingan tegas = 'Y' di banyak tempat, jadi
   nilai lain apa pun (kosong, '1', 'y') diam-diam berarti 'tidak'. */
function coaYN($v) { return (strtoupper(trim((string) $v)) === 'Y') ? 'Y' : 'N'; }
$support_gen_adm = coaYN($_POST['support_gen_adm'] ?? 'N');
$support_prod    = coaYN($_POST['support_prod'] ?? 'N');
$prod            = coaYN($_POST['prod'] ?? 'N');
$support_sell    = coaYN($_POST['support_sell'] ?? 'N');

/* Kode register Kas Kecil (P/K/N/B/C), hanya untuk COA 1.01.xx. Boleh
   kosong - sebagian besar COA memang tidak punya. */
$kode_cash = strtoupper(trim((string) ($_POST['kode_cash'] ?? '')));
$kode_cash = mysqli_real_escape_string($conn2, $kode_cash);
$status = "Active";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");

// echo $tgl_active;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $sob;
// echo "< -- >";
// echo $account;
// echo "< -- >";
// echo $bank;
// echo "< -- >";
// echo $curr;
// echo "< -- >";
// echo $pesan;
// echo "< -- >";
// echo $status;
// echo "< -- >";
// echo $create_user;
// echo "< -- >";
// echo $create_date;
// echo "< -- >";
// echo $create_user;
// echo "< -- >";
// echo $tgl_active2;
if ($no_coa == '') {
	$nocoa = "123";
echo $nocoa;
}
else{
	
$sqlx = mysqli_query($conn2,"select no_coa FROM mastercoa_v2 where no_coa = '$no_coa' ");
$rowx = mysqli_fetch_array($sqlx);
$nocoa = isset($rowx['no_coa']) ? $rowx['no_coa'] : NULL;
echo $nocoa;
}

if ($nocoa != '') {
	$query = 'select max(id) from mastercoa_v3';
	$execute = mysqli_query($conn2,$query);
	// echo "COA Already Exist";
}else{
$query = "INSERT INTO mastercoa_v3 (no_coa, nama_coa, id_ctg1, id_ctg2, id_ctg3, id_ctg4, id_ctg5, id_direct_debit, id_direct_credit, id_indirect, status) 
VALUES 
	('$no_coa', '$nama_coa', '$nama_ctg1_h', '$nama_ctg2', '$nama_ctg3_h', '$nama_ctg4_h', '$nama_ctg5', '$txt_dirdebit', '$txt_dircredit', '$txt_indirect', '$status')";

$execute = mysqli_query($conn2,$query);


$sql_ctg1 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg1 where id_ctg1 = '$nama_ctg1_h'");
$row_ctg1 = mysqli_fetch_array($sql_ctg1);
$ind_name1 = $row_ctg1['ind_name'];
$eng_name1 = $row_ctg1['eng_name'];

$sql_ctg2 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg2 where id_ctg2 = '$nama_ctg2'");
$row_ctg2 = mysqli_fetch_array($sql_ctg2);
$ind_name2 = $row_ctg2['ind_name'];
$eng_name2 = $row_ctg2['eng_name'];

$sql_ctg3 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg3 where id_ctg3 = '$nama_ctg3_h'");
$row_ctg3 = mysqli_fetch_array($sql_ctg3);
$ind_name3 = $row_ctg3['ind_name'];
$eng_name3 = $row_ctg3['eng_name'];

$sql_ctg4 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg4 where id_ctg4 = '$nama_ctg4_h'");
$row_ctg4 = mysqli_fetch_array($sql_ctg4);
$ind_name4 = $row_ctg4['ind_name'];
$eng_name4 = $row_ctg4['eng_name'];

$sql_ctg5 = mysqli_query($conn2,"select ind_name,eng_name from master_coa_ctg5 where id_ctg5 = '$nama_ctg5'");
$row_ctg5 = mysqli_fetch_array($sql_ctg5);
$ind_name5 = $row_ctg5['ind_name'];
$eng_name5 = $row_ctg5['eng_name'];

$sql_dirdebit = mysqli_query($conn2,"select ind_name,eng_name from tbl_master_cashflow where id = '$txt_dirdebit'");
$row_dirdebit = mysqli_fetch_array($sql_dirdebit);
$ind_name6 = isset($row_dirdebit['ind_name']) ? $row_dirdebit['ind_name'] : "NA";
$eng_name6 = isset($row_dirdebit['eng_name']) ? $row_dirdebit['eng_name'] : "NA";

$sql_dircredit = mysqli_query($conn2,"select ind_name,eng_name from tbl_master_cashflow where id = '$txt_dircredit'");
$row_dircredit = mysqli_fetch_array($sql_dircredit);
$ind_name7 = isset($row_dircredit['ind_name']) ? $row_dircredit['ind_name'] : "NA";
$eng_name7 = isset($row_dircredit['eng_name']) ? $row_dircredit['eng_name'] : "NA";

$sql_indirect = mysqli_query($conn2,"select ind_name,eng_name from tbl_master_cashflow where id = '$txt_indirect'");
$row_indirect = mysqli_fetch_array($sql_indirect);
$ind_name8 = isset($row_indirect['ind_name']) ? $row_indirect['ind_name'] : "NA";
$eng_name8 = isset($row_indirect['eng_name']) ? $row_indirect['eng_name'] : "NA";


$query2 = "INSERT INTO mastercoa_v2 (no_coa, nama_coa, ind_categori1, eng_categori1, ind_categori2, eng_categori2, ind_categori3, eng_categori3, ind_categori4, eng_categori4, ind_categori5, eng_categori5, ind_debit_direct, eng_debit_direct, ind_credit_direct, eng_credit_direct, ind_indirect, eng_indirect, status,id_ctg1, id_ctg2, id_ctg3, id_ctg4, id_ctg5, id_direct_debit, id_direct_credit, id_indirect, bankin_no_approve, bankout_no_approve, support_gen_adm, support_prod, prod, support_sell, kode_cash)
VALUES
	('$no_coa', '$nama_coa', '$ind_name1', '$eng_name1', '$ind_name2', '$eng_name2', '$ind_name3', '$eng_name3', '$ind_name4', '$eng_name4', '$ind_name5', '$eng_name5', '$ind_name6', '$eng_name6', '$ind_name7', '$eng_name7', '$ind_name8', '$eng_name8', 'Active', '$nama_ctg1_h', '$nama_ctg2', '$nama_ctg3_h', '$nama_ctg4_h', '$nama_ctg5', '$txt_dirdebit', '$txt_dircredit', '$txt_indirect', '$bankin_no_approve', '$bankout_no_approve', '$support_gen_adm', '$support_prod', '$prod', '$support_sell', '$kode_cash')";

$execute2 = mysqli_query($conn2,$query2);
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>