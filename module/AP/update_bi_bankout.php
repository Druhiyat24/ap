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
	$cash_flow = $_POST['cash_flow'] ?? '';
	$create_user = $_POST['create_user'];
	$create_date = date("Y-m-d H:i:s");

	$no_bk = $_POST['no_bk'] ?? '';
	$reff_doc = $_POST['no_journal'] ?? '';
	$reff_date = !empty($_POST['tgl_journal']) ? date('Y-m-d', strtotime($_POST['tgl_journal'])) : $date;
	$coa = $_POST['no_coa'] ?? '-';
	$pc = $_POST['pc'] ?? '-';
	$cost = $_POST['no_cc'] ?? '-';
	$curr_reff = $_POST['curr_reff'] ?? $curr;
	$rate_reff = $_POST['rate_reff'] ?? 1;
	$total_reff = $_POST['total_reff'] ?? 0;
	$total_idr_reff = $_POST['total_idr_reff'] ?? 0;

	if (!$akun) {
		throw new Exception('Account tidak boleh kosong.');
	}

	if (!$no_bk || !$reff_doc) {
		throw new Exception('Bank Out tidak boleh kosong.');
	}

	if ((float) $amount == 0) {
		throw new Exception('Amount tidak boleh kosong.');
	}

	if ($curr !== 'IDR' && ((float) $rate === 0.0 || (float) $rate === 1.0)) {
		throw new Exception('Currency non IDR harus memiliki rate diisi dan tidak boleh 1.');
	}

	if ($deskripsi === '') {
		throw new Exception('Description tidak boleh kosong.');
	}

	if ($cash_flow === '') {
		throw new Exception('Cash Flow Category tidak boleh kosong.');
	}
	$cash_flow = (int) $cash_flow;

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

	/* =========================
	   NOMOR REVISI JURNAL
	========================= */

	if ($doc_num !== $old_doc_num) {
		// Nomor dokumen berubah (account diganti) - dokumen ini dianggap baru,
		// reset revisi jurnal (tidak melanjutkan Rev dari doc_num lama).
		$type_journal = 'Bank Keluar';
	} else {
		$sqlRev = q($conn1, "select type_journal from tbl_list_journal where no_journal = '".mysqli_real_escape_string($conn1, $old_doc_num)."'");
		$maxRev = 0;
		while ($rowRev = mysqli_fetch_assoc($sqlRev)) {
			if (preg_match('/\(Rev (\d+)\)/', $rowRev['type_journal'], $m)) {
				$maxRev = max($maxRev, (int) $m[1]);
			}
		}
		$type_journal = 'Bank Keluar (Rev '.($maxRev + 1).')';
	}

	$sqlcoa1 = q($conn1, "select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%".mysqli_real_escape_string($conn1, $akun)."%' and ind_categori2 = 'ASET'");
	$rowcoa1 = mysqli_fetch_array($sqlcoa1);
	$no_coa1 = $rowcoa1['no_coa'] ?? null;
	$nama_coa1 = $rowcoa1['nama_coa'] ?? null;

	if (!$no_coa1) {
		throw new Exception('COA Bank untuk akun "'.$akun.'" tidak ditemukan di mastercoa_v2.');
	}

	$sqlcoa = q($conn1, "select nama_coa from mastercoa_v2 where no_coa = '".mysqli_real_escape_string($conn1, $coa)."'");
	$rowcoa = mysqli_fetch_array($sqlcoa);
	$nama_coa = $rowcoa['nama_coa'] ?? '-';

	$selisih = $eqv_idr - $total_idr_reff;

	if ($selisih > 0) {
		$debit_reff = 0;
		$credit_reff = $selisih;
	} elseif ($selisih < 0) {
		$debit_reff = $selisih * -1;
		$credit_reff = 0;
	} else {
		$debit_reff = 0;
		$credit_reff = 0;
	}

	q($conn2, "INSERT into tbl_list_journal (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center) select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '".mysqli_real_escape_string($conn2, $create_user)."' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '".mysqli_real_escape_string($conn2, $old_doc_num)."' and status != 'Updated'");

	q($conn2, "UPDATE tbl_list_journal set status = 'Updated' where no_journal = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "UPDATE tbl_bankin_arcollection SET doc_num = '".mysqli_real_escape_string($conn2, $doc_num)."', date = '$date', customer = '".mysqli_real_escape_string($conn2, $customer)."', akun = '".mysqli_real_escape_string($conn2, $akun)."', bank = '".mysqli_real_escape_string($conn2, $bank)."', curr = '".mysqli_real_escape_string($conn2, $curr)."', profit_center = '".mysqli_real_escape_string($conn2, $profit_center)."', amount = '$amount', outstanding = '$amount', rate = '$rate', eqv_idr = '$eqv_idr', deskripsi = '".mysqli_real_escape_string($conn2, $deskripsi)."', id_cash_flow = '$cash_flow' WHERE doc_num = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "UPDATE b_reportbank set no_doc = '".mysqli_real_escape_string($conn2, $doc_num)."', transaksi_date = '$date', debit = '$amount', deskripsi = '".mysqli_real_escape_string($conn2, $deskripsi)."' where no_doc = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "insert into b_bankin_none_cancel (select * from b_bankin_none where no_bankin='".mysqli_real_escape_string($conn2, $old_doc_num)."')");
	q($conn2, "Delete from b_bankin_none where no_bankin='".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	q($conn2, "INSERT INTO b_bankin_none (no_bankin,id_coa,no_reff,reff_date,deskripsi,t_debit,t_credit,profit_center)
		VALUES ('$doc_num', '".mysqli_real_escape_string($conn2, $coa)."', '".mysqli_real_escape_string($conn2, $reff_doc)."', '$reff_date', '".mysqli_real_escape_string($conn2, $deskripsi)."', '0', '$total_reff', '".mysqli_real_escape_string($conn2, $pc)."')");

	/* =========================
	   INSERT JOURNAL (bulk)
	========================= */

	$journalValues = [];

	$journalValues[] = "('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '$no_coa1', '$nama_coa1', '-', '-', '".mysqli_real_escape_string($conn2, $reff_doc)."', '$reff_date', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv_idr', '0', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '$pc_bank_acc')";
	$journalValues[] = "('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '".mysqli_real_escape_string($conn2, $coa)."', '".mysqli_real_escape_string($conn2, $nama_coa)."', '-', '-', '".mysqli_real_escape_string($conn2, $reff_doc)."', '$reff_date', '-', '-', '".mysqli_real_escape_string($conn2, $curr_reff)."', '$rate_reff', '0', '$total_reff', '0', '$total_idr_reff', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '".mysqli_real_escape_string($conn2, $pc)."')";

	if ($selisih != 0) {
		$journalValues[] = "('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '8.52.01', 'LABA / (RUGI) SELISIH KURS', '-', '-', '".mysqli_real_escape_string($conn2, $reff_doc)."', '$reff_date', '-', '-', 'IDR', '1', '$debit_reff', '$credit_reff', '$debit_reff', '$credit_reff', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '".mysqli_real_escape_string($conn2, $pc)."')";
	}

	q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
		VALUES " . implode(',', $journalValues));

	mysqli_commit($conn2);

	echo ($doc_num !== $old_doc_num) ? "OK|".$doc_num : "OK";

} catch (Exception $e) {

	mysqli_rollback($conn2);

	echo "Error: " . $e->getMessage();
}
