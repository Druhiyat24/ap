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


$sqlx = mysqli_query($conn1,"select CONCAT( REPLACE(type_journal, 'Reverse ', ''), ' (Rev ', (SELECT COUNT(*)+1 FROM (select * from tbl_list_journal WHERE no_journal = '$doc_num' and type_journal like '%(Rev%' GROUP BY type_journal) a), ')') AS type_journal from tbl_list_journal where no_journal = '$doc_num' limit 1");
$rowx = mysqli_fetch_array($sqlx);
$filter_jurnal = isset($rowx['no_journal']) ? $rowx['no_journal'] : null;


if ($doc_num) {

	$jurnal_balik = mysqli_query($conn2,"INSERT into tbl_list_journal select '', no_journal, '$cancel_date' tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$cancel_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and status != 'Updated'");	

	$Update_journal = mysqli_query($conn2,"UPDATE tbl_list_journal set status = 'Updated' where no_journal = '$doc_num'");	

	$sql = "UPDATE tbl_bankin_arcollection SET date = '$date', customer = '$customer', profit_center = '$profit_center', amount = '$amount', rate = '$rate', eqv_idr = '$eqv_idr', deskripsi = '$deskripsi' WHERE doc_num = '$doc_num'";

	// $jurnal_balik = mysqli_query($conn2,"INSERT into tbl_list_journal select '', no_journal, '$cancel_date' tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$cancel_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, '$profit_center' profit_center from tbl_list_journal where no_journal = '$doc_num'");

	if (mysqli_query($conn2, $sql)) {
		echo "OK";
	} else {
		echo "Error: " . mysqli_error($conn2);
	}
} else {
	echo "Invalid data";
}

?>