<?php
include '../../conn/conn.php';
require_once __DIR__ . '/bpb_docinfo_guard.php';
ini_set('date.timezone', 'Asia/Jakarta');
ini_set('max_execution_time', 0);
ini_set('memory_limit', '4096M');

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


mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>