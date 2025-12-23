<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_num = $_POST['doc_num'] ?? '';
$date = date("Y-m-d",strtotime($_POST['date'])) ?? '';
$ref_data = $_POST['ref_data'] ?? '';
$customer = $_POST['customer'] ?? '';
$profit_center = $_POST['profit_center'] ?? '';
$akun = $_POST['akun'] ?? '';
$curr = $_POST['curr'] ?? '';
$bank = $_POST['bank'] ?? '';
$amount = $_POST['amount'] ?? 0;
$rate = $_POST['rate'] ?? 0;
$eqv_idr = $_POST['eqv_idr'] ?? 0;
$deskripsi = $_POST['deskripsi'] ?? '';
$h_tot_debit = $_POST['h_tot_debit'] ?? 0;
$h_tot_credit = $_POST['h_tot_credit'] ?? 0;
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$h_tot_debit = $_POST['h_tot_debit']  ?? 0;
$h_tot_credit = $_POST['h_tot_credit'] ?? 0;
$total_nag = $_POST['total_nag'] ?? 0;
$total_nak = $_POST['total_nak'] ?? 0;

$details      = json_decode($_POST['details'], true);


if ($doc_num) {

	$sqlx = mysqli_query($conn1,"select CONCAT( REPLACE(type_journal, 'Reverse ', ''), ' (Rev ', (SELECT COUNT(*)+1 FROM (select * from tbl_list_journal WHERE no_journal = '$doc_num' and type_journal like '%(Rev%' and type_journal not like '%Reverse%' GROUP BY type_journal) a), ')') AS type_journal from tbl_list_journal where no_journal = '$doc_num' limit 1");
	$rowx = mysqli_fetch_array($sqlx);
	$type_journal = isset($rowx['type_journal']) ? $rowx['type_journal'] : null;


	$sqlcoa1 = mysqli_query($conn1,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
	$rowcoa1 = mysqli_fetch_array($sqlcoa1);
	$no_coa1 = $rowcoa1['no_coa'];
	$nama_coa1 = $rowcoa1['nama_coa'];

	$jurnal_balik = mysqli_query($conn2,"INSERT into tbl_list_journal select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$create_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and status != 'Updated'");	

	$jurnal_tambahan = mysqli_query($conn2,"INSERT into tbl_list_journal select '', no_journal, '$date' tgl_journal, '$type_journal' type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, 'Draft' status, '$deskripsi' keterangan, '$create_user' create_by, '$create_date' create_date, '' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and reff_doc like '%LP/%' and type_journal not like '%Reverse%' and status = 'Draft'");	

	$Update_journal = mysqli_query($conn2,"UPDATE tbl_list_journal set status = 'Updated' where no_journal = '$doc_num' and type_journal != '$type_journal' ");	

	$sql = "UPDATE b_bankout_h SET bankout_date = '$date', nama_supp = '$customer', profit_center = '$profit_center', amount = '$amount', outstanding = '$amount', rate = '$rate', eqv_idr = '$eqv_idr', deskripsi = '$deskripsi' WHERE no_bankout = '$doc_num'";

	$Update_report = mysqli_query($conn2,"UPDATE b_reportbank set transaksi_date = '$date', credit = '$amount', deskripsi = '$deskripsi' where no_doc = '$doc_num'");

		$query_nag = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$doc_num', '$date', '$type_journal', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv_idr', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', 'NAG')";

		$execute_nag = mysqli_query($conn2,$query_nag);
	


	$query_bk = mysqli_query($conn2,"insert into b_bankout_adj_det_cancel (select * from b_bankout_adj_det where no_bankout='$doc_num')");
	$query_delete = mysqli_query($conn2,"Delete from b_bankout_adj_det where no_bankout='$doc_num'");

	foreach ($details as $d) {
		$coa        = $d['coa'];
		$prof_ctr   = $d['prof_ctr'];
		$cost_ctr   = $d['cost_ctr'];
		$refferensi      = $d['refferensi'];
		$tgl_refferensi         = date("Y-m-d",strtotime($d['tgl_refferensi'])) ?? '';
		$debit  = floatval($d['debit'] ?? 0);
		$credit = floatval($d['credit'] ?? 0);
		$ket        = $d['keterangan'];

		$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$coa'");
		$rowcoa = mysqli_fetch_array($sqlcoa);
		$nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

		$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$cost_ctr'");
		$rowcc = mysqli_fetch_array($sqlcc);
		$nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

		$t_debit = $debit * $rate;
		$t_credit = $credit * $rate;

		$query_det = "INSERT INTO b_bankout_adj_det (no_bankout,id_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit, profit_center)
		VALUES 
		('$doc_num', '$coa', '$cost_ctr', '$refferensi', '$tgl_refferensi', '$ket', '$debit', '$credit', '$prof_ctr')";

		$execute_det = mysqli_query($conn2,$query_det);

		$sql_det = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
		VALUES 
		('$doc_num', '$date', '$type_journal', '$coa', '$nama_coa', '$cost_ctr', '$nama_cc', '$refferensi', '$tgl_refferensi', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$ket', '$create_user', '$create_date', '', '', '', '', '$prof_ctr')";

		$jurnal_det = mysqli_query($conn2,$sql_det);
	}

	if (mysqli_query($conn2, $sql)) {
		echo "OK";
	} else {
		echo "Error: " . mysqli_error($conn2);
	}
} else {
	echo "Invalid data";
}

?>