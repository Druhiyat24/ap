<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

function q($conn, $sql) {
	$result = mysqli_query($conn, $sql);
	if ($result === false) {
		throw new Exception(mysqli_error($conn));
	}
	return $result;
}

$old_doc_num = $_POST['doc_num'] ?? '';

if (!$old_doc_num) {
	echo "Invalid data";
	exit;
}

mysqli_begin_transaction($conn2);

try {

	$date = date("Y-m-d", strtotime($_POST['date']));
	$ref_data = $_POST['ref_data'] ?? '';
	$customer = $_POST['customer'] ?? '';
	$profit_center = $_POST['profit_center'] ?? '';
	$akun = $_POST['akun'] ?? '';
	$curr = $_POST['curr'] ?? '';
	$bank = $_POST['bank'] ?? '';
	$kode_bank_acc = $_POST['kode_bank_acc'] ?? '';
	$pc_bank_acc = $_POST['pc_bank_acc'] ?? '';
	$amount = $_POST['amount'] ?? 0;
	$rate = $_POST['rate'] ?? 0;
	$eqv_idr = $_POST['eqv_idr'] ?? 0;
	$deskripsi = trim($_POST['deskripsi'] ?? '');
	$cash_flow = $_POST['cash_flow'] ?? '';
	$h_tot_debit = $_POST['h_tot_debit'] ?? 0;
	$h_tot_credit = $_POST['h_tot_credit'] ?? 0;
	$create_user = $_POST['create_user'];
	$create_date = date("Y-m-d H:i:s");
	$total_nag = $_POST['total_nag'] ?? 0;
	$total_nak = $_POST['total_nak'] ?? 0;

	if ($deskripsi === '') {
		throw new Exception('Description tidak boleh kosong.');
	}

	if ($cash_flow === '') {
		throw new Exception('Cash Flow Category tidak boleh kosong.');
	}
	$cash_flow = (int) $cash_flow;

	if (!$akun) {
		throw new Exception('Account tidak boleh kosong.');
	}

	if ($curr !== 'IDR' && ((float) $rate === 0.0 || (float) $rate === 1.0)) {
		throw new Exception('Currency non IDR harus memiliki rate diisi dan tidak boleh 1.');
	}

	$details = json_decode($_POST['details'], true);

	if (empty($details)) {
		throw new Exception('Detail transaksi tidak boleh kosong.');
	}

	$sqlOld = q($conn2, "select akun from tbl_bankin_arcollection where doc_num = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");
	$rowOld = mysqli_fetch_assoc($sqlOld);
	$old_akun = $rowOld['akun'] ?? '';

	$doc_num = $old_doc_num;

	/* =========================
	   ACCOUNT BERUBAH -> GENERATE DOC NUMBER BARU
	========================= */

	if ($akun !== $old_akun) {

		if (!$kode_bank_acc || !$pc_bank_acc) {
			throw new Exception('Data bank untuk account "'.$akun.'" tidak ditemukan.');
		}

		$bulan = date('m', strtotime($date));
		$tahun = date('y', strtotime($date));
		$prefix = "BM/".$kode_bank_acc."/".$pc_bank_acc."/".$bulan.$tahun;

		$prefixEsc = mysqli_real_escape_string($conn2, $prefix);

		$sqlNum = q($conn2, "
			SELECT MAX(CAST(RIGHT(doc_num,5) AS UNSIGNED)) AS max_urut
			FROM tbl_bankin_arcollection
			WHERE doc_num LIKE '$prefixEsc%'
			FOR UPDATE
		");
		$rowNum = mysqli_fetch_assoc($sqlNum);
		$maxHeader = (int) ($rowNum['max_urut'] ?? 0);

		// Nomor yang pernah dipakai tetap tercatat permanen di tbl_list_journal
		// walau doc_num di header sudah berubah (akun diganti saat edit
		// sebelumnya), jadi nomor lama tidak boleh dipakai ulang.
		$sqlNumJ = q($conn2, "
			SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut
			FROM tbl_list_journal
			WHERE no_journal LIKE '$prefixEsc%'
			FOR UPDATE
		");
		$rowNumJ = mysqli_fetch_assoc($sqlNumJ);
		$maxJournal = (int) ($rowNumJ['max_urut'] ?? 0);

		$urutan = max($maxHeader, $maxJournal) + 1;

		$doc_num = $prefix."/".sprintf("%05d", $urutan);
	}

	if ($doc_num !== $old_doc_num) {
		// Nomor dokumen berubah (account diganti) - dokumen ini dianggap baru,
		// reset revisi jurnal (tidak melanjutkan Rev dari doc_num lama).
		$type_journal = $ref_data;
	} else {
		$sqlRev = q($conn1, "select type_journal from tbl_list_journal where no_journal = '".mysqli_real_escape_string($conn1, $old_doc_num)."'");
		$maxRev = 0;
		while ($rowRev = mysqli_fetch_assoc($sqlRev)) {
			if (preg_match('/\(Rev (\d+)\)/', $rowRev['type_journal'], $m)) {
				$maxRev = max($maxRev, (int) $m[1]);
			}
		}
		$type_journal = $ref_data.' (Rev '.($maxRev + 1).')';
	}

	$sqlcoa1 = q($conn1, "select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%".mysqli_real_escape_string($conn1, $akun)."%' and ind_categori2 = 'ASET'");
	$rowcoa1 = mysqli_fetch_array($sqlcoa1);
	$no_coa1 = $rowcoa1['no_coa'] ?? null;
	$nama_coa1 = $rowcoa1['nama_coa'] ?? null;

	if (!$no_coa1) {
		throw new Exception('COA Bank untuk akun "'.$akun.'" tidak ditemukan di mastercoa_v2.');
	}

	q($conn2, "INSERT into tbl_list_journal (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center) select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '".mysqli_real_escape_string($conn2, $create_user)."' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '".mysqli_real_escape_string($conn2, $old_doc_num)."' and status != 'Updated'");

	q($conn2, "UPDATE tbl_list_journal set status = 'Updated' where no_journal = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "UPDATE tbl_bankin_arcollection SET doc_num = '".mysqli_real_escape_string($conn2, $doc_num)."', date = '$date', customer = '".mysqli_real_escape_string($conn2, $customer)."', akun = '".mysqli_real_escape_string($conn2, $akun)."', bank = '".mysqli_real_escape_string($conn2, $bank)."', curr = '".mysqli_real_escape_string($conn2, $curr)."', profit_center = '".mysqli_real_escape_string($conn2, $profit_center)."', amount = '$amount', outstanding = '$amount', rate = '$rate', eqv_idr = '$eqv_idr', deskripsi = '".mysqli_real_escape_string($conn2, $deskripsi)."', id_cash_flow = '$cash_flow' WHERE doc_num = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "UPDATE b_reportbank set no_doc = '".mysqli_real_escape_string($conn2, $doc_num)."', transaksi_date = '$date', debit = '$amount', deskripsi = '".mysqli_real_escape_string($conn2, $deskripsi)."' where no_doc = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	if ($total_nag != 0) {
		$total_nag_idr = $total_nag * $rate;
		q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
			VALUES
			('$doc_num', '$date', '$type_journal', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$total_nag', '0', '$total_nag_idr', '0', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', 'NAG')");
	}

	if ($total_nak != 0) {
		$total_nak_idr = $total_nak * $rate;
		q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
			VALUES
			('$doc_num', '$date', '$type_journal', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$total_nak', '0', '$total_nak_idr', '0', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', 'NAK')");
	}

	q($conn2, "insert into tbl_bankin_cancel (select * from tbl_bankin where no_doc='".mysqli_real_escape_string($conn2, $old_doc_num)."')");
	q($conn2, "Delete from tbl_bankin where no_doc='".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	$bankinValues = [];
	$journalValues = [];

	foreach ($details as $i => $d) {

		$coa      = trim($d['coa'] ?? '');
		$prof_ctr = trim($d['prof_ctr'] ?? '');
		$cost_ctr = trim($d['cost_ctr'] ?? '') ?: '-';
		$buyer    = $d['buyer'] ?? '';
		$ws       = $d['ws'] ?? '';
		$currency = $d['currency'] ?? '';
		$debit    = (float) ($d['debit'] ?? 0);
		$credit   = (float) ($d['credit'] ?? 0);
		$ket      = trim($d['keterangan'] ?? '');

		if ($ket === '') {
			$ket = $deskripsi;
		}

		if (!$coa || $coa === '-') {
			throw new Exception('Baris ke-'.($i + 1).': COA wajib diisi.');
		}

		if (!$prof_ctr || $prof_ctr === '-') {
			throw new Exception('Baris ke-'.($i + 1).': Profit Center wajib diisi.');
		}

		if ($debit == 0 && $credit == 0) {
			throw new Exception('Baris ke-'.($i + 1).': Debit/Credit harus diisi.');
		}

		$sqlcoa = q($conn1, "select nama_coa from mastercoa_v2 where no_coa = '".mysqli_real_escape_string($conn1, $coa)."'");
		$rowcoa = mysqli_fetch_array($sqlcoa);
		$nama_coa = $rowcoa['nama_coa'] ?? null;

		if (!$nama_coa) {
			throw new Exception('Baris ke-'.($i + 1).': COA "'.$coa.'" tidak ditemukan.');
		}

		$sqlWajibCc = q($conn1, "select no_coa from mastercoa_v2 where no_coa = '".mysqli_real_escape_string($conn1, $coa)."'
			and (support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y')");
		if (mysqli_num_rows($sqlWajibCc) > 0 && ($cost_ctr === '-' || $cost_ctr === '')) {
			throw new Exception('Baris ke-'.($i + 1).': COA '.$coa.' wajib isi Cost Center.');
		}

		$nama_cc = null;
		if ($cost_ctr !== '-' && $cost_ctr !== '') {
			$sqlcc = q($conn1, "select cc_name from b_master_cc where no_cc = '".mysqli_real_escape_string($conn1, $cost_ctr)."'");
			$rowcc = mysqli_fetch_array($sqlcc);
			$nama_cc = $rowcc['cc_name'] ?? null;
		}

		$t_debit = $debit * $rate;
		$t_credit = $credit * $rate;

		$coaEsc      = mysqli_real_escape_string($conn2, $coa);
		$namaCoaEsc  = mysqli_real_escape_string($conn2, $nama_coa);
		$ccEsc       = mysqli_real_escape_string($conn2, $cost_ctr);
		$namaCcEsc   = mysqli_real_escape_string($conn2, $nama_cc ?: '-');
		$buyerEsc    = mysqli_real_escape_string($conn2, $buyer);
		$wsEsc       = mysqli_real_escape_string($conn2, $ws);
		$currencyEsc = mysqli_real_escape_string($conn2, $currency);
		$ketEsc      = mysqli_real_escape_string($conn2, $ket);
		$profCtrEsc  = mysqli_real_escape_string($conn2, $prof_ctr);
		$createUserEsc = mysqli_real_escape_string($conn2, $create_user);

		$bankinValues[] = "('$doc_num', '$coaEsc', '$ccEsc', '$buyerEsc', '$wsEsc', '$currencyEsc', '$debit', '$credit', '$ketEsc', '$profCtrEsc')";

		$journalValues[] = "('$doc_num', '$date', '$type_journal', '$coaEsc', '$namaCoaEsc', '$ccEsc', '$namaCcEsc', '-', '', '$buyerEsc', '$wsEsc', '$currencyEsc', '$rate', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$ketEsc', '$createUserEsc', '$create_date', '', '', '', '', '$profCtrEsc')";
	}

	if (empty($bankinValues)) {
		throw new Exception('Detail transaksi tidak boleh kosong.');
	}

	q($conn2, "INSERT INTO tbl_bankin (no_doc,id_coa,id_cost_center,buyer,no_ws,curr,t_debit,t_credit,keterangan, profit_center)
		VALUES " . implode(',', $bankinValues));

	q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
		VALUES " . implode(',', $journalValues));

	mysqli_commit($conn2);

	echo ($doc_num !== $old_doc_num) ? "OK|".$doc_num : "OK";

} catch (Exception $e) {

	mysqli_rollback($conn2);

	echo "Error: " . $e->getMessage();
}
