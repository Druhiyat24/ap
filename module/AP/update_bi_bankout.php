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
$no_bk = $_POST['no_bk']  ?? '';

$detailData      = json_decode($_POST['detailData'], true);


if ($doc_num) {

	$sqlx = mysqli_query($conn1,"select CONCAT( REPLACE(type_journal, 'Reverse ', ''), ' (Rev ', (SELECT COUNT(*)+1 FROM (select * from tbl_list_journal WHERE no_journal = '$doc_num' and type_journal like '%(Rev%' and type_journal not like '%Reverse%' GROUP BY type_journal) a), ')') AS type_journal from tbl_list_journal where no_journal = '$doc_num' limit 1");
	$rowx = mysqli_fetch_array($sqlx);
	$type_journal = isset($rowx['type_journal']) ? $rowx['type_journal'] : null;


	$sqlcoa1 = mysqli_query($conn1,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
	$rowcoa1 = mysqli_fetch_array($sqlcoa1);
	$no_coa1 = $rowcoa1['no_coa'];
	$nama_coa1 = $rowcoa1['nama_coa'];

	$jurnal_balik = mysqli_query($conn2,"INSERT into tbl_list_journal select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$create_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and status != 'Updated'");	

	$Update_journal = mysqli_query($conn2,"UPDATE tbl_list_journal set status = 'Updated' where no_journal = '$doc_num'");	

	$sql = "UPDATE tbl_bankin_arcollection SET date = '$date', customer = '$customer', profit_center = '$profit_center', amount = '$amount', outstanding = '$amount', rate = '$rate', eqv_idr = '$eqv_idr', deskripsi = '$deskripsi' WHERE doc_num = '$doc_num'";

	$Update_report = mysqli_query($conn2,"UPDATE b_reportbank set transaksi_date = '$date', debit = '$amount', deskripsi = '$deskripsi' where no_doc = '$doc_num'");

	$sqlbk = mysqli_query($conn1,"select bankout_date from b_bankout_h where no_bankout = '$no_bk'");
	$rowbk = mysqli_fetch_array($sqlbk);
	$bk_date = isset($rowbk['bankout_date']) ? $rowbk['bankout_date'] : null;

	$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
	VALUES 
	('$doc_num', '$date', '$type_journal', '$no_coa1', '$nama_coa1', '-', '-', '$no_bk', '$bk_date', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv_idr', '0', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', '$profit_center')";
	$executess2 = mysqli_query($conn2,$queryss2);

	$query_bk = mysqli_query($conn2,"insert into b_bankin_none_cancel (select * from b_bankin_none where no_doc='$doc_num')");
	$query_delete = mysqli_query($conn2,"Delete from b_bankin_none where no_doc='$doc_num'");

	foreach ($detailData as $d) {
		$det_kode_pc = (!empty($d['det_kode_pc'])) ? $d['det_kode_pc'] : $profit_center;
		$det_no_coa   		= $d['det_no_coa'];
		$det_no_bankout   	= $d['det_no_bankout'];
		$det_bankout_date 	= date("Y-m-d",strtotime($d['det_bankout_date'])) ?? '';
		$det_deskripsi      = $d['det_deskripsi'];
		$det_debit  		= floatval($d['det_debit'] ?? 0);
		$det_credit 		= floatval($d['det_credit'] ?? 0);

		$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$det_no_coa'");
		$rowcoa = mysqli_fetch_array($sqlcoa);
		$nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

		if ($det_debit == 0 and $det_credit == 0) {

		}else{
			$query = "INSERT INTO b_bankin_none (no_bankin,id_coa,no_reff,reff_date,deskripsi,t_debit,t_credit,profit_center) 
			VALUES 
			('$doc_num', '$det_no_coa', '$det_no_bankout', '$det_bankout_date', '$det_deskripsi', '$det_debit', '$det_credit', '$det_kode_pc')";

			$execute = mysqli_query($conn2,$query);

			$queryss3 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date,profit_center) 
			VALUES 
			('$doc_num', '$date', '$type_journal', '$det_no_coa', '$nama_coa', '-', '-', '$det_no_bankout', '$det_bankout_date', '-', '-', 'IDR', '1', '$det_debit', '$det_credit', '$det_debit', '$det_credit', 'Draft', '$det_deskripsi', '$create_user', '$create_date', '', '', '', '', '$det_kode_pc')";

			$executess3 = mysqli_query($conn2,$queryss3);

		}
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