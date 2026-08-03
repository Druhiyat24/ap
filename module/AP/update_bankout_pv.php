<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
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

	if (!$akun) {
		throw new Exception('Account tidak boleh kosong.');
	}

	if ($deskripsi === '') {
		throw new Exception('Description tidak boleh kosong.');
	}

	if ($curr !== 'IDR' && ((float) $rate === 0.0 || (float) $rate === 1.0)) {
		throw new Exception('Currency non IDR harus memiliki rate diisi dan tidak boleh 1.');
	}

	$details = json_decode($_POST['details'], true) ?: [];
	$pv_rows = json_decode($_POST['pv_rows'] ?? '[]', true) ?: [];

	if (empty($pv_rows)) {
		throw new Exception('Pilih minimal 1 PV.');
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
		// reset revisi jurnal (tidak melanjutkan Rev dari doc_num lama).
		$type_journal = 'Payment Voucher';
	} else {
		$sqlRev = q($conn1, "select type_journal from tbl_list_journal where no_journal = '".mysqli_real_escape_string($conn1, $old_doc_num)."'");
		$maxRev = 0;
		while ($rowRev = mysqli_fetch_assoc($sqlRev)) {
			if (preg_match('/\(Rev (\d+)\)/', $rowRev['type_journal'], $m)) {
				$maxRev = max($maxRev, (int) $m[1]);
			}
		}
		$type_journal = 'Payment Voucher (Rev '.($maxRev + 1).')';
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

	q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
	VALUES
	('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '".mysqli_real_escape_string($conn2, $curr)."', '$rate', '0', '$amount', '0', '$eqv_idr', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '".mysqli_real_escape_string($conn2, $pc_bank_acc)."')");

	/* =========================
	   ADJUST DETAIL (bulk)
	========================= */

	q($conn2, "insert into b_bankout_adj_det_cancel (select * from b_bankout_adj_det where no_bankout='".mysqli_real_escape_string($conn2, $old_doc_num)."')");
	q($conn2, "Delete from b_bankout_adj_det where no_bankout='".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	$coaWajibCC = [];
	$sqlWajibCcAll = q($conn1, "select no_coa from mastercoa_v2 where support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y'");
	while ($rowWajib = mysqli_fetch_assoc($sqlWajibCcAll)) {
		$coaWajibCC[] = $rowWajib['no_coa'];
	}

	$adjValues = [];
	$adjJournalValues = [];

	foreach ($details as $i => $d) {

		$coa      = trim($d['coa'] ?? '');
		$prof_ctr = trim($d['prof_ctr'] ?? '');
		$cost_ctr = trim($d['cost_ctr'] ?? '') ?: '-';
		$refferensi = trim($d['refferensi'] ?? '') ?: '-';
		$tgl_refferensi = !empty($d['tgl_refferensi']) ? date('Y-m-d', strtotime($d['tgl_refferensi'])) : $date;
		$debit  = (float) ($d['debit'] ?? 0);
		$credit = (float) ($d['credit'] ?? 0);
		$ket    = trim($d['keterangan'] ?? '');

		if ($ket === '') {
			$ket = $deskripsi;
		}

		if (!$coa || $coa === '-') {
			throw new Exception('Adjust baris ke-'.($i + 1).': COA wajib diisi.');
		}

		if (!$prof_ctr || $prof_ctr === '-') {
			throw new Exception('Adjust baris ke-'.($i + 1).': Profit Center wajib diisi.');
		}

		if (in_array($coa, $coaWajibCC, true) && ($cost_ctr === '-' || $cost_ctr === '')) {
			throw new Exception('Adjust baris ke-'.($i + 1).': COA '.$coa.' wajib isi Cost Center.');
		}

		if ($debit == 0 && $credit == 0) {
			throw new Exception('Adjust baris ke-'.($i + 1).': Debit/Credit harus diisi.');
		}

		$coaEsc = mysqli_real_escape_string($conn2, $coa);
		$pcEsc = mysqli_real_escape_string($conn2, $prof_ctr);
		$ccEsc = mysqli_real_escape_string($conn2, $cost_ctr);
		$reffEsc = mysqli_real_escape_string($conn2, $refferensi);
		$ketEsc = mysqli_real_escape_string($conn2, $ket);

		$sqlcoa = q($conn1, "select nama_coa from mastercoa_v2 where no_coa = '$coaEsc'");
		$rowcoa = mysqli_fetch_array($sqlcoa);
		$nama_coa = $rowcoa['nama_coa'] ?? null;

		if (!$nama_coa) {
			throw new Exception('Adjust baris ke-'.($i + 1).': COA "'.$coa.'" tidak ditemukan.');
		}

		$nama_cc = null;
		if ($ccEsc !== '-' && $ccEsc !== '') {
			$sqlcc = q($conn1, "select cc_name from b_master_cc where no_cc = '$ccEsc'");
			$rowcc = mysqli_fetch_array($sqlcc);
			$nama_cc = $rowcc['cc_name'] ?? null;
		}
		$nama_cc = mysqli_real_escape_string($conn2, $nama_cc ?: '-');

		$adjValues[] = "('$doc_num', '$coaEsc', '$ccEsc', '$reffEsc', '$tgl_refferensi', '$ketEsc', '$debit', '$credit', '$pcEsc')";

		$adjJournalValues[] = "('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '$coaEsc', '$nama_coa', '$ccEsc', '$nama_cc', '$reffEsc', '$tgl_refferensi', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$ketEsc', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '$pcEsc')";
	}

	if (!empty($adjValues)) {
		q($conn2, "INSERT INTO b_bankout_adj_det (no_bankout,id_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit, profit_center)
			VALUES " . implode(',', $adjValues));

		q($conn2, "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
			VALUES " . implode(',', $adjJournalValues));
	}

	/* =========================
	   PV YANG TERKAIT: kembalikan outstanding PV lama, backup + hapus baris
	   lama, lalu insert ulang dari daftar final yang dikirim client (baris
	   lama yang dipertahankan + baris baru dari hasil pencarian PV) - dengan
	   perhitungan dpp/ppn/pph yang SAMA seperti create (save_pv.php), bukan
	   dari nilai yang tersimpan sebelumnya.
	========================= */

	q($conn2, "
		UPDATE tbl_pv_h
		INNER JOIN b_bankout_det ON b_bankout_det.no_reff = tbl_pv_h.no_pv
		SET tbl_pv_h.outstanding = tbl_pv_h.outstanding + b_bankout_det.total
		WHERE b_bankout_det.no_bankout = '".mysqli_real_escape_string($conn2, $old_doc_num)."'
		AND b_bankout_det.no_reff LIKE 'PV/NAG/%'
	");

	q($conn2, "
		UPDATE memo_h
		INNER JOIN b_bankout_det ON b_bankout_det.no_reff = memo_h.no_pv
		SET memo_h.no_bankout = '', memo_h.status = 'PAYMENT DRAFT'
		WHERE b_bankout_det.no_bankout = '".mysqli_real_escape_string($conn2, $old_doc_num)."'
		AND b_bankout_det.no_reff LIKE 'PV/NAG/%'
	");

	q($conn2, "CREATE TABLE IF NOT EXISTS b_bankout_det_cancel LIKE b_bankout_det");
	q($conn2, "INSERT INTO b_bankout_det_cancel SELECT * FROM b_bankout_det WHERE no_bankout = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");
	q($conn2, "DELETE FROM b_bankout_det WHERE no_bankout = '".mysqli_real_escape_string($conn2, $old_doc_num)."'");

	foreach ($pv_rows as $pv) {

		$no_pv  = mysqli_real_escape_string($conn2, $pv['no_pv'] ?? '');
		if ($no_pv === '') continue;

		$amountPv = (float) ($pv['amount'] ?? 0);
		$pc     = mysqli_real_escape_string($conn2, $pv['pc'] ?? '');
		$type_pv = !empty($pv['type_pv']) ? $pv['type_pv'] : 'Biaya';

		if ($amountPv <= 0) {
			throw new Exception('PV "'.$no_pv.'": Amount tidak boleh kosong.');
		}

		if ($type_pv === 'Biaya') {

			$sql_pv = q($conn2,"WITH
				total_pv as (select b.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr, SUM(b.amount - ded_add) subtotal, SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) ppn, SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100)) pph, (SUM(b.amount - ded_add) + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total, a.status, a.frm_akun, if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join b_masterbank c on c.bank_account = a.frm_akun INNER JOIN master_pc d on d.kode_pc = b.profit_center where a.no_pv = '$no_pv' and d.kode_pc = '$pc' group by a.no_pv, b.profit_center),

				total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_pv, sum(ppn) ppn_pv, sum(pph) pph_pv, sum(total) total_pv from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and a.type_pv = 'Biaya' and b.no_bankout != '".mysqli_real_escape_string($conn2, $old_doc_num)."' GROUP BY no_reff,a.profit_center)

				select a.profit_center, a.nama_pc, a.nama_supp, a.no_pv, a.pv_date,a.due_date, a.curr, (a.subtotal - COALESCE(b.dpp_pv,0)) subtotal, (a.ppn - COALESCE(b.ppn_pv,0)) ppn, (a.pph - COALESCE(b.pph_pv,0)) pph ,(a.total - COALESCE(b.total_pv,0)) total, a.status, a.frm_akun, a.bank_name, a.b_code, IFNULL(c.rate,1) rate from total_pv a LEFT JOIN total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = a.pv_date");
			$row_pv = mysqli_fetch_array($sql_pv);

			if (!$row_pv) {
				throw new Exception('Payment Voucher "'.$no_pv.'" tidak ditemukan.');
			}

			$pv_pc = mysqli_real_escape_string($conn2, $row_pv['profit_center']);
			$pv_number = mysqli_real_escape_string($conn2, $row_pv['no_pv']);
			$pv_date = $row_pv['pv_date'];
			$pv_duedate = $row_pv['due_date'];
			$pv_curr = mysqli_real_escape_string($conn2, $row_pv['curr']);
			$pv_sub = (float) $row_pv['subtotal'];
			$pv_ppn = (float) $row_pv['ppn'];
			$pv_pph = (float) $row_pv['pph'];
			$pv_total = (float) $row_pv['total'];
			$pv_rate = (float) $row_pv['rate'];

			q($conn2,"
				INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance, profit_center, type_pv)
				VALUES
				('$doc_num', '$pv_number', '$pv_date', '$pv_duedate', '$pv_sub', '$pv_ppn', '$pv_pph', '$pv_total', '$pv_curr', '$amountPv', '$rate', '$amountPv', '$pv_pc', 'Biaya')
				");

			q($conn2,"insert into tbl_list_journal select '' id, '$doc_num' no_journal, '$date' tgl_journal, type_journal, coa, nama_coa, no_cc, COALESCE(cc_name,'-') cc_name, no_pv no_reff, pv_date reff_date, buyer, no_ws, a.curr, IF(rate is null,1,rate) rate, debit, credit, round(debit * IF(rate is null,1,rate),4) debit_idr, round(credit * IF(rate is null,1,rate),4) credit_idr, 'Draft' status, deskripsi, '".mysqli_real_escape_string($conn2, $create_user)."' create_by, CURRENT_TIMESTAMP() create_date, '' approve_by, '' approve_date, '' cancel_by, '' cancel_date, CURRENT_TIMESTAMP() created_at, CURRENT_TIMESTAMP() updated_at, profit_center from
				(select a.id, '".mysqli_real_escape_string($conn2, $type_journal)."' type_journal, d.no_coa coa, d.nama_coa, a.no_cc, b.cc_name,h.no_pv, h.pv_date,  '-' buyer, '-' no_ws, h.curr, amount debit, ded_add credit, a.deskripsi, a.profit_center from tbl_pv a INNER JOIN tbl_pv_h h on h.no_pv = a.no_pv left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mastercoa_v2 d on d.no_coa = a.coa where a.no_pv = '$pv_number'
				UNION
				select a.id, '".mysqli_real_escape_string($conn2, $type_journal)."' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,h.no_pv, h.pv_date,  '-' buyer, '-' no_ws, h.curr, (ded_add * a.pph/100) debit, (amount * a.pph/100) credit, a.deskripsi, a.profit_center from tbl_pv a INNER JOIN tbl_pv_h h on h.no_pv = a.no_pv left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mtax d on d.idtax = a.id_pph where a.no_pv = '$pv_number'
				UNION
				select a.id, '".mysqli_real_escape_string($conn2, $type_journal)."' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,no_pv, pv_date,  '-' buyer, '-' no_ws, curr, (amount * ppn/100) debit, (ded_add * a.ppn/100) credit, deskripsi, a.profit_center from (select id,no_pv, pv_date, a.profit_center, coa, no_cc, reff_doc, reff_date, deskripsi, curr, ded_add, amount, pph, IF(per_ppn = 0,ppn,per_ppn) ppn, IF(per_ppn = 0,id_ppn,id_per_ppn) id_ppn from (select a.*,b.per_ppn, b.pv_date, b.curr, CASE WHEN b.per_ppn BETWEEN 1.09 AND 1.11 THEN 20 WHEN b.per_ppn = 11 THEN 1 WHEN b.per_ppn BETWEEN 1.19 AND 1.21 THEN 23 ELSE 0 END AS id_per_ppn from tbl_pv a INNER JOIN tbl_pv_h b on b.no_pv = a.no_pv where a.no_pv = '$pv_number' and a.profit_center = '$pv_pc') a) a left join b_master_cc b on b.no_cc = a.no_cc INNER JOIN mtax d on d.idtax = a.id_ppn
			) a LEFT JOIN (select tanggal, curr, rate from ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.pv_date and b.curr = a.curr order by a.id, credit asc");

			q($conn2, "Update tbl_pv_h set outstanding = GREATEST(outstanding - $amountPv, 0) where no_pv = '$pv_number'");
			q($conn2, "UPDATE memo_h set no_bankout = '$doc_num', status='PAID' where no_pv = '$pv_number'");

		} else {

			$no_kbon_esc = $no_pv;
			$filtersOne = [
				'nama_supp'   => 'ALL',
				'status'      => 'ALL',
				'filter_date' => 'tgl_kbon',
				'start_date'  => '',
				'end_date'    => '',
			];

			$pv_coa = '-';
			$pv_coa_nama = '-';
			$rowOne = null;

			if ($type_pv === 'Regular') {
				$filtersOne['no_kbon'] = $no_pv;
				$rows = getDataRegular($conn1, $conn2, $filtersOne, '1', '1', '');
				$rowOne = $rows[0] ?? null;
			} elseif ($type_pv === 'Installment') {
				$filtersOne['no_kbon_det'] = $no_pv;
				$rows = getDataInstallment($conn1, $conn2, $filtersOne, '1', '1', '');
				$rowOne = $rows[0] ?? null;
			} elseif ($type_pv === 'DP') {
				$filtersOne['no_kbon'] = $no_pv;
				$rows = getDataDp($conn1, $conn2, $filtersOne, '1', '1', '');
				$rowOne = $rows[0] ?? null;
			} elseif ($type_pv === 'CBD') {
				$filtersOne['no_kbon'] = $no_pv;
				$rows = getDataCbd($conn1, $conn2, $filtersOne, '1', '1', '');
				$rowOne = $rows[0] ?? null;
			} elseif ($type_pv === 'SaldoAwal') {
				$filtersOne['no_kbon'] = $no_pv;
				$rows = getDataSaldoAwal($conn2, $filtersOne);
				$rowOne = $rows[0] ?? null;
			}

			if ($rowOne) {
				$pv_coa = !empty($rowOne['no_coa']) ? $rowOne['no_coa'] : '-';
				$pv_coa_nama = !empty($rowOne['nama_coa']) ? $rowOne['nama_coa'] : '-';
			}

			if (!$rowOne) {
				throw new Exception("Data $type_pv untuk $no_pv tidak ditemukan");
			}

			$pv_pc = $rowOne['profit_center'];
			$pv_date = $rowOne['tgl_kbon_raw'];
			$pv_duedate = $rowOne['tgl_tempo_raw'];
			$pv_curr = $rowOne['curr'];
			$pv_sub = $rowOne['subtotal_raw'];
			$pv_ppn = $rowOne['tax_raw'];
			$pv_pph = $rowOne['pph_raw'];
			if ($pv_curr === 'IDR') {
				$pv_rate = 1;
			} else {
				$sqlPvRate = q($conn2, "SELECT rate FROM ap_masterrate WHERE v_codecurr = 'PAJAK' AND curr = '" . mysqli_real_escape_string($conn2, $pv_curr) . "' AND tanggal = '" . mysqli_real_escape_string($conn2, $date) . "' LIMIT 1");
				$rowPvRate = mysqli_fetch_assoc($sqlPvRate);
				$pv_rate = !empty($rowPvRate['rate']) ? (float)$rowPvRate['rate'] : (float)$rate;
			}

			$pv_pc_esc = mysqli_real_escape_string($conn2, $pv_pc);
			$pv_date_sql = !empty($pv_date) ? "'" . mysqli_real_escape_string($conn2, $pv_date) . "'" : 'NULL';
			$pv_duedate_sql = !empty($pv_duedate) ? "'" . mysqli_real_escape_string($conn2, $pv_duedate) . "'" : 'NULL';
			$pv_curr_esc = mysqli_real_escape_string($conn2, $pv_curr);
			$pv_coa_esc = mysqli_real_escape_string($conn2, $pv_coa);
			$pv_coa_nama_esc = mysqli_real_escape_string($conn2, $pv_coa_nama);
			$type_pv_esc = mysqli_real_escape_string($conn2, $type_pv);

			$bayar_pph = ($type_pv === 'Regular' || $type_pv === 'Installment')
				? min($pv_pph, $amountPv) : 0;
			$total_dppnya = $amountPv + $bayar_pph;
			$debit_idr = round($total_dppnya * $pv_rate, 4);

			q($conn2, "
				INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance, profit_center, type_pv)
				VALUES
				('$doc_num', '$no_kbon_esc', $pv_date_sql, $pv_duedate_sql, '$pv_sub', '$pv_ppn', '$pv_pph', '$amountPv', '$pv_curr_esc', '$amountPv', '$pv_rate', '$amountPv', '$pv_pc_esc', '$type_pv_esc')
				");

			q($conn2, "
				INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
				VALUES
				('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '$pv_coa_esc', '$pv_coa_nama_esc', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '$total_dppnya', '0', '$debit_idr', '0', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '$pv_pc_esc')
				");

			if ($bayar_pph > 0) {
				if ($type_pv === 'Installment') {
					$sqlpph = q($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(k.idtax) FROM kontrabon k INNER JOIN kontrabon_h_installment_detail d ON d.no_kbon = k.no_kbon WHERE d.no_kbon_det = '$no_kbon_esc')");
				} else {
					$sqlpph = q($conn2, "SELECT no_coa, nama_coa FROM mtax WHERE idtax = (SELECT MAX(idtax) FROM kontrabon WHERE no_kbon = '$no_kbon_esc')");
				}
				$rowpph = mysqli_fetch_assoc($sqlpph);
				$no_coa_pph  = mysqli_real_escape_string($conn2, $rowpph['no_coa'] ?? '');
				$nama_coa_pph = mysqli_real_escape_string($conn2, $rowpph['nama_coa'] ?? '');
				$pph_idr = round($bayar_pph * $pv_rate, 4);

				if (!empty($no_coa_pph)) {
					q($conn2, "
						INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
						VALUES
						('$doc_num', '$date', '".mysqli_real_escape_string($conn2, $type_journal)."', '$no_coa_pph', '$nama_coa_pph', '-', '-', '$no_kbon_esc', $pv_date_sql, '-', '-', '$pv_curr_esc', '$pv_rate', '0', '$bayar_pph', '0', '$pph_idr', 'Draft', '".mysqli_real_escape_string($conn2, $deskripsi)."', '".mysqli_real_escape_string($conn2, $create_user)."', '$create_date', '', '', '', '', '$pv_pc_esc')
						");
				}
			}
		}
	}

	mysqli_commit($conn2);

	echo ($doc_num !== $old_doc_num) ? "OK|".$doc_num : "OK";

} catch (Exception $e) {

	mysqli_rollback($conn2);

	echo "Error: " . $e->getMessage();
}
