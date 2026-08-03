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
	$create_user = $_POST['create_user'];
	$create_date = date("Y-m-d H:i:s");

	if ($customer === '') {
		throw new Exception('Supplier tidak boleh kosong.');
	}

	if (!$akun) {
		throw new Exception('Account tidak boleh kosong.');
	}

	if ((float) $amount == 0) {
		throw new Exception('Amount tidak boleh kosong.');
	}

	if ($deskripsi === '') {
		throw new Exception('Description tidak boleh kosong.');
	}

	if ($curr !== 'IDR' && ((float) $rate === 0.0 || (float) $rate === 1.0)) {
		throw new Exception('Currency non IDR harus memiliki rate diisi dan tidak boleh 1.');
	}

	$details = json_decode($_POST['details'], true);

	if (empty($details)) {
		throw new Exception('Detail transaksi tidak boleh kosong.');
	}

	$sqlOld = q($conn2, "select akun from b_bankout_h where no_bankout = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");
	$rowOld = mysqli_fetch_assoc($sqlOld);

	if (!$rowOld) {
		throw new Exception('Dokumen sudah diproses sebelumnya atau tidak ditemukan. Silakan muat ulang halaman.');
	}

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
		$prefix = "BK/".$kode_bank_acc."/".$pc_bank_acc."/".$bulan.$tahun;

		$prefixEsc = mysqli_real_escape_string($conn2, $prefix);

		$sqlNum = q($conn2, "
			SELECT MAX(CAST(RIGHT(no_bankout,5) AS UNSIGNED)) AS max_urut
			FROM b_bankout_h
			WHERE no_bankout LIKE '$prefixEsc%'
			FOR UPDATE
		");
		$rowNum = mysqli_fetch_assoc($sqlNum);
		$maxHeader = (int) ($rowNum['max_urut'] ?? 0);

		// Nomor yang pernah dipakai tetap tercatat permanen di tbl_list_journal
		// walau no_bankout di header sudah berubah (akun diganti saat edit
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

	/* =========================
	   NOMOR REVISI JURNAL
	========================= */

	if ($doc_num !== $old_doc_num) {
		// Nomor dokumen berubah (account diganti) - dokumen ini dianggap baru,
		// reset revisi jurnal jadi 'None' lagi (tidak melanjutkan Rev dari doc_num lama).
		$type_journal = 'None';
	} else {
		$sqlRev = q($conn1, "select type_journal from tbl_list_journal where no_journal = '".mysqli_real_escape_string($conn1, $old_doc_num)."'");
		$maxRev = 0;
		while ($rowRev = mysqli_fetch_assoc($sqlRev)) {
			if (preg_match('/\(Rev (\d+)\)/', $rowRev['type_journal'], $m)) {
				$maxRev = max($maxRev, (int) $m[1]);
			}
		}
		$type_journal = 'None (Rev '.($maxRev + 1).')';
	}

	$sqlcoa1 = q($conn1, "select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%".mysqli_real_escape_string($conn1, $akun)."%' and ind_categori2 = 'ASET'");
	$rowcoa1 = mysqli_fetch_array($sqlcoa1);
	$no_coa1 = $rowcoa1['no_coa'] ?? null;
	$nama_coa1 = $rowcoa1['nama_coa'] ?? null;

	if (!$no_coa1) {
		throw new Exception('COA Bank untuk akun "'.$akun.'" tidak ditemukan di mastercoa_v2.');
	}

	q($conn2, "INSERT into tbl_list_journal select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '".mysqli_real_escape_string($conn2, $create_user)."' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '".mysqli_real_escape_string($conn2, $old_doc_num)."' and status != 'Updated'");

	q($conn2, "UPDATE tbl_list_journal set status = 'Updated' where no_journal = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "UPDATE b_bankout_h SET no_bankout = '".mysqli_real_escape_string($conn2, $doc_num)."', bankout_date = '$date', nama_supp = '".mysqli_real_escape_string($conn2, $customer)."', akun = '".mysqli_real_escape_string($conn2, $akun)."', bank = '".mysqli_real_escape_string($conn2, $bank)."', curr = '".mysqli_real_escape_string($conn2, $curr)."', profit_center = '".mysqli_real_escape_string($conn2, $profit_center)."', amount = '$amount', outstanding = '$amount', rate = '$rate', eqv_idr = '$eqv_idr', deskripsi = '".mysqli_real_escape_string($conn2, $deskripsi)."' WHERE no_bankout = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "UPDATE b_reportbank set no_doc = '".mysqli_real_escape_string($conn2, $doc_num)."', transaksi_date = '$date', credit = '$amount', deskripsi = '".mysqli_real_escape_string($conn2, $deskripsi)."' where no_doc = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "insert into b_bankout_none_cancel (select * from b_bankout_none where no_bankout='".mysqli_real_escape_string($conn2, $old_doc_num)."')");
	q($conn2, "Delete from b_bankout_none where no_bankout='".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	$bankoutNoneValues = [];
	$journalValues = [];

	$journalValues[] = "('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '".mysqli_real_escape_string($conn2, $curr)."', '$rate', '0', '$amount', '0', '$eqv_idr', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '".mysqli_real_escape_string($conn2, $pc_bank_acc ?: $profit_center)."')";

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

		$bankoutNoneValues[] = "('$doc_num', '$date', 'None', '$coaEsc', '$ccEsc', '$buyerEsc', '$wsEsc', '$currencyEsc', '$debit', '$credit', '$ketEsc', '$profCtrEsc')";

		$journalValues[] = "('$doc_num', '$date', '$type_journal', '$coaEsc', '$namaCoaEsc', '$ccEsc', '$namaCcEsc', '-', '', '$buyerEsc', '$wsEsc', '$currencyEsc', '$rate', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$ketEsc', '$createUserEsc', '$create_date', '', '', '', '', '$profCtrEsc')";
	}

	if (empty($bankoutNoneValues)) {
		throw new Exception('Detail transaksi tidak boleh kosong.');
	}

	q($conn2, "INSERT INTO b_bankout_none (no_bankout,tgl_bankout,reff_doc,no_coa,no_costcntr,buyer,no_ws,curr,debit,credit,deskripsi, profit_center)
		VALUES " . implode(',', $bankoutNoneValues));

	q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
		VALUES " . implode(',', $journalValues));

	mysqli_commit($conn2);

	echo ($doc_num !== $old_doc_num) ? "OK|".$doc_num : "OK";

} catch (Exception $e) {

	mysqli_rollback($conn2);

	echo "Error: " . $e->getMessage();
}
