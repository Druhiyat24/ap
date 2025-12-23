<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_num = $_POST['doc_num'] ?? '';
$date = date("Y-m-d",strtotime($_POST['date'])) ?? '';
$customer = $_POST['customer'] ?? '';
$profit_center = $_POST['profit_center'] ?? '';
$amount = $_POST['amount'] ?? 0;
$rate = $_POST['rate'] ?? 0;
$eqv_idr = $_POST['eqv_idr'] ?? 0;
$deskripsi = $_POST['deskripsi'] ?? '';
$akun = $_POST['akun'] ?? '';
$coa = $_POST['no_coa'] ?? '';
$curr = $_POST['curr'] ?? '';
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$cost = '';


if ($doc_num) {

	$sqlx = mysqli_query($conn1,"select CONCAT( REPLACE(type_journal, 'Reverse ', ''), ' (Rev ', (SELECT COUNT(*)+1 FROM (select * from tbl_list_journal WHERE no_journal = '$doc_num' and type_journal like '%(Rev%' and type_journal not like '%Reverse%' GROUP BY type_journal) a), ')') AS type_journal from tbl_list_journal where no_journal = '$doc_num' limit 1");
	$rowx = mysqli_fetch_array($sqlx);
	$type_journal = isset($rowx['type_journal']) ? $rowx['type_journal'] : null;


	$sqlcoa1 = mysqli_query($conn1,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
	$rowcoa1 = mysqli_fetch_array($sqlcoa1);
	$no_coa1 = $rowcoa1['no_coa'];
	$nama_coa1 = $rowcoa1['nama_coa'];

	$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$coa'");
	$rowcoa = mysqli_fetch_array($sqlcoa);
	$nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

	$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$cost'");
	$rowcc = mysqli_fetch_array($sqlcc);
	$nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

	$jurnal_balik = mysqli_query($conn2,"INSERT into tbl_list_journal select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$create_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and status != 'Updated'");	

	$Update_journal = mysqli_query($conn2,"UPDATE tbl_list_journal set status = 'Updated' where no_journal = '$doc_num'");	

	$sql = "UPDATE tbl_bankin_arcollection SET date = '$date', customer = '$customer', profit_center = '$profit_center', amount = '$amount', outstanding = '$amount', rate = '$rate', eqv_idr = '$eqv_idr', deskripsi = '$deskripsi' WHERE doc_num = '$doc_num'";

	$Update_report = mysqli_query($conn2,"UPDATE b_reportbank set transaksi_date = '$date', debit = '$amount', deskripsi = '$deskripsi' where no_doc = '$doc_num'");

	$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
   VALUES 
   ('$doc_num', '$date', '$type_journal', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv_idr', '0', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', '$profit_center')";

   $executess2 = mysqli_query($conn2,$queryss2);
$queryss3 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
   VALUES 
   ('$doc_num', '$date', '$type_journal', '$coa', '$nama_coa', '$cost', '$nama_cc', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv_idr', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', '$profit_center')";

   $executess3 = mysqli_query($conn2,$queryss3);

	if (mysqli_query($conn2, $sql)) {
		echo "OK";
	} else {
		echo "Error: " . mysqli_error($conn2);
	}
} else {
	echo "Invalid data";
}

?>