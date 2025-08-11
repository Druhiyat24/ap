<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$approve_date = date("Y-m-d H:i:s");
$approve_user = $_POST['approve_user'];


$sql_del = "Delete from tbl_list_journal_cancel where no_journal='$no_dok'";
$query_del = mysqli_query($conn2,$sql_del);

if($query_del) {
	$sql = "insert into tbl_list_journal_cancel (select * from tbl_list_journal where no_journal='$no_dok')";
	$query = mysqli_query($conn2,$sql);

	if($query) {
		$sql2 = "Delete from tbl_list_journal where no_journal='$no_dok'";
		$query2 = mysqli_query($conn2,$sql2);

		if($query2) {
			$sql3 = "insert into tbl_list_journal
			(select '' id, a.no_bankout, a.bankout_date, 'Payment Voucher' type_journal, id_coa no_coa, coa_name, '-' no_cc, '-' nama_cc, '-' reff_doc, '' reff_date, '-' buyer, '-' no_ws, b.curr, if(a.rate = 0,1,a.rate) rate, '0' debit, amount credit, '0' debit_idr, eqv_idr credit_idr, a.status, a.deskripsi, a.create_by, a.create_date, a.approve_by, a.approve_date, '' cancel_by, '' cancel_date, ''	created_at, '' updated_at, ''	profit_center from b_bankout_h a INNER JOIN b_masterbank b on b.bank_account = a.akun where a.no_bankout = '$no_dok')
			UNION
			(select '' id, no_bankout, bankout_date, type_journal, coa, nama_coa, no_cc, COALESCE(cc_name,'-') cc_name, no_reff, reff_date, buyer, no_ws, curr_pv, IF(rate is null,1,rate) rate, debit, credit, round(debit * IF(rate is null,1,rate),4) debit_idr, round(credit * IF(rate is null,1,rate),4) credit_idr, status, deskripsi, create_by, create_date, approve_by, approve_date, '' cancel_by, '' cancel_date, ''	created_at, '' updated_at, profit_center from 
			(select a.id, no_bankout, bankout_date, 'Payment Voucher' type_journal, d.no_coa coa, d.nama_coa, a.no_cc, b.cc_name,ob.no_reff, ob.reff_date,  '-' buyer, '-' no_ws, curr_pv, amount debit, '0' credit, ob.status, deskripsi, ob.create_by, ob.create_date, ob.approve_by, ob.approve_date, ob.profit_center from tbl_pv a left join b_master_cc b on b.no_cc = a.no_cc inner join (select b.no_bankout, b.bankout_date, c.curr curr_bk, b.status, b.create_by, b.create_date, b.approve_by, b.approve_date, a.no_reff, a.reff_date, a.curr curr_pv, a.profit_center FROM b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout INNER JOIN b_masterbank c on c.bank_account = b.akun where a.no_bankout = '$no_dok' GROUP BY no_reff) ob on ob.no_reff = a.no_pv INNER JOIN mastercoa_v2 d on d.no_coa = a.coa
			UNION
			select a.id, no_bankout, bankout_date, 'Payment Voucher' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,ob.no_reff, ob.reff_date,  '-' buyer, '-' no_ws, curr_pv, '0' debit, (amount * pph/100) credit, ob.status, deskripsi, ob.create_by, ob.create_date, ob.approve_by, ob.approve_date, ob.profit_center from tbl_pv a left join b_master_cc b on b.no_cc = a.no_cc inner join (select b.no_bankout, b.bankout_date, c.curr curr_bk, b.status, b.create_by, b.create_date, b.approve_by, b.approve_date, a.no_reff, a.reff_date, a.curr curr_pv, a.profit_center FROM b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout INNER JOIN b_masterbank c on c.bank_account = b.akun where a.no_bankout = '$no_dok' GROUP BY no_reff) ob on ob.no_reff = a.no_pv INNER JOIN mtax d on d.idtax = a.id_pph
			UNION
			select a.id, no_bankout, bankout_date, 'Payment Voucher' type_journal, d.no_coa, d.nama_coa, a.no_cc, b.cc_name,ob.no_reff, ob.reff_date,  '-' buyer, '-' no_ws, curr_pv, (amount * ppn/100) debit, '0' credit, ob.status, deskripsi, ob.create_by, ob.create_date, ob.approve_by, ob.approve_date, ob.profit_center from (select id,no_pv, coa, no_cc, reff_doc, reff_date, deskripsi, amount, pph, IF(per_ppn = 0,ppn,per_ppn) ppn, IF(per_ppn = 0,id_ppn,id_per_ppn) id_ppn from (select a.*,b.per_ppn, CASE WHEN b.per_ppn BETWEEN 1.09 AND 1.11 THEN 20 WHEN b.per_ppn = 11 THEN 1 WHEN b.per_ppn BETWEEN 1.19 AND 1.21 THEN 23 ELSE 0 END AS id_per_ppn from tbl_pv a INNER JOIN tbl_pv_h b on b.no_pv = a.no_pv) a) a left join b_master_cc b on b.no_cc = a.no_cc inner join (select b.no_bankout, b.bankout_date, c.curr curr_bk, b.status, b.create_by, b.create_date, b.approve_by, b.approve_date, a.no_reff, a.reff_date, a.curr curr_pv, a.profit_center FROM b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout INNER JOIN b_masterbank c on c.bank_account = b.akun where a.no_bankout = '$no_dok' GROUP BY no_reff) ob on ob.no_reff = a.no_pv INNER JOIN mtax d on d.idtax = a.id_ppn
			) a LEFT JOIN (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal) b on b.tanggal = a.bankout_date and b.curr = a.curr_pv order by a.id, credit asc)
			UNION
			(select '' i, b.no_bankout, bankout_date, 'Payment Voucher' type_journal, c.no_coa, c.nama_coa, d.no_cc, d.cc_name, a.reff_doc, a.reff_date, '-' buyer, '-' no_ws, 'IDR' curr, '1' rate, t_debit, t_credit, t_debit, t_credit, b.status, a.deskripsi, b.create_by, b.create_date, b.approve_by, b.approve_date, '' cancel_by, '' cancel_date, ''	created_at, '' updated_at, a.profit_center from b_bankout_adj_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout INNER JOIN mastercoa_v2 c on c.no_coa = a.id_coa LEFT JOIN b_master_cc d on d.no_cc = a.no_cc where a.no_bankout = '$no_dok')";
			$query3 = mysqli_query($conn2,$sql3);
		}
	}
}


if (!$query3) {
	die('Error: ' . mysqli_error($conn2));
}

mysqli_close($conn2);

?>