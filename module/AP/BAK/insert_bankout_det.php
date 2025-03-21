<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankout = $_POST['no_bankout'];
$bankout_date = date("Y-m-d",strtotime($_POST['bankout_date']));
$no_coa = $_POST['no_coa'];
$no_coc = $_POST['no_coc'];
$reff_doc = $_POST['reff_doc'];
$reff_date = date("Y-m-d",strtotime($_POST['reff_date']));
$deskripsi = $_POST['deskripsi'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$no_pay = $_POST['no_pay'];
$pay_date = date("Y-m-d",strtotime($_POST['pay_date']));
$due_date = date("Y-m-d",strtotime($_POST['due_date']));
$dpp = $_POST['dpp'];
$ppn = $_POST['ppn'];
$pph = $_POST['pph'];
$total = $_POST['total'];
$curr = $_POST['curr'];
$kodelp = $_POST['kodelp'];
$rates = $_POST['rates'];
$amount_input = $_POST['amount_input'];
$rate_h = $_POST['rate_h'];
$curr_h = $_POST['curr_h'];
$pesan_ob = $_POST['pesan'];
$status_int = 5;
$total_idr = $_POST['total_idr'];
if ($curr_h == 'IDR' ) {
$balance = $amount_input/ $rates;
$pph2 = $pph/ $rates;
$balance2 = ($amount_input + $pph) / $rates;
$ratess = $rates;
$balanceidr = $balance;
$balanceidr2 = $balance2;
$pphidr2 = $pph2;
}else{
	$balance = $amount_input;
	$pph2 = $pph;
	$balance2 = ($amount_input + $pph);
	$ratess = $rate_h;
	$balanceidr = $balance * $ratess;
	$balanceidr2 = $balance2 * $ratess;
	$pphidr2 = $pph2 * $ratess;
}



echo $no_pay;
// echo "< // >";
// echo $no_coa;
// echo "< // >";
// echo $reff_doc;
// echo "< // >";
// echo $reff_date;
// echo "< // >";
// echo $deskripsi;
// echo "< // >";
// echo $debit;
// echo "< // >";
// echo $credit;
// echo "< // >";
// echo $no_pay;
// echo "< // >";
// echo $pay_date;
// echo "< // >";
// echo $due_date;



$sqlx = mysqli_query($conn2,"select max(id) as id FROM b_bankout_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_bankout),curr,reff_doc,create_by,create_date from b_bankout_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_bankout)'];
 $type_ob = $rownkb['reff_doc'];
 $curr_ob = $rownkb['curr'];
 $create_by = $rownkb['create_by'];
 $create_date = $rownkb['create_date'];


 if ($curr_ob == 'IDR') {
   $rates = '1';
   $t_debit = $debit;
   $t_credit = $credit;
}else{
   $sqlyzz = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$bankout_date' and v_codecurr = 'PAJAK'");
$rowyzz = mysqli_fetch_array($sqlyzz);
$rats = isset($rowyzz['rate']) ? $rowyzz['rate'] : 0;

if($rats != '0'){
   $rates = $rats;
}else{
$sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
$rowxss = mysqli_fetch_array($sqlxss);
$maxidss = $rowxss['id'];

$sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and v_codecurr = 'PAJAK'");
$rowyss = mysqli_fetch_array($sqlyss);
$rates = $rowyss['rate'];
}

$t_debit = $debit * $rates;
$t_credit = $credit * $rates;
}

 // $sqlnlp = mysqli_query($conn2,"select COALESCE(sum(for_balance),0) as ttl_bayar from b_bankout_det where no_reff = '$no_pay'");
 // $rownlp = mysqli_fetch_array($sqlnlp);
 // $ttl_bayar = $rownlp['ttl_bayar'];

 $forbalance = $total - $balance;

$sqlcoa_adj = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where no_coa = '$no_coa' Limit 1");
$rowcoa_adj = mysqli_fetch_array($sqlcoa_adj);
$nama_coa_adj = $rowcoa_adj['nama_coa'];

$sqlcoc_adj = mysqli_query($conn1,"SELECT no_cc as code_combine,cc_name as cost_name from b_master_cc where no_cc = '$no_coc' Limit 1");
$rowcoc_adj = mysqli_fetch_array($sqlcoc_adj);
$nama_coc_adj = $rowcoc_adj['cost_name'];

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO b_bankout_adj_det (no_bankout,id_coa,no_cc,reff_doc,reff_date,deskripsi,t_debit,t_credit) 
VALUES 
	('$kode', '$no_coa', '$no_coc', '$reff_doc', '$reff_date', '$deskripsi', '$debit', '$credit')";

$execute = mysqli_query($conn2,$query);

$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$bankout_date', '$type_ob', '$no_coa', '$nama_coa_adj', '$no_coc', '$nama_coc_adj', '$reff_doc', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);
}

if ($no_pay == '') {
	
}elseif(strpos($no_pay, 'PV/NAG/') !== false){

	$query2 = "INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance) 
VALUES 
	('$kode', '$no_pay', '$pay_date', '$due_date', '$dpp', '$ppn', '$pph', '$total', '$curr', '$total_idr', '$ratess', '$balance')";

$execute2 = mysqli_query($conn2,$query2);

	$sqlpv = mysqli_query($conn1, "select * from (select b.id,a.no_pv,a.pv_date,a.curr,b.coa,c.nama_coa,b.no_cc,IF(e.cc_name is null,'-',e.cc_name) cc_name,b.reff_doc,b.reff_date,b.deskripsi,b.amount total,((a.per_ppn/100) * b.amount) total_ppn,a.per_ppn,((b.pph/100) * b.amount) as total_pph, b.pph, (b.amount + ((a.per_ppn/100) * b.amount) - ((b.pph/100) * b.amount)) as total_,d.no_coa coa_pph,d.nama_coa coa_name_pph from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join mastercoa_v2 c on c.no_coa = b.coa left join mtax d on d.idtax = b.id_pph left join b_master_cc e on e.no_cc = b.no_cc where a.no_pv = '$no_pay') a left join
(select b.id id_,((b.ppn/100) * b.amount) as t_ppn, b.ppn,d.no_coa coa_ppn,d.nama_coa coa_name_ppn from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join mastercoa_v2 c on c.no_coa = b.coa left join mtax d on d.idtax = b.id_ppn left join b_master_cc e on e.no_cc = b.no_cc where a.no_pv = '$no_pay') b on b.id_ = a.id order by id asc");

	while ($row = mysqli_fetch_assoc($sqlpv)) {
		$pv_no_pv = $row['no_pv'];
		$pv_pv_date = $row['pv_date'];
		$pv_curr = $row['curr'];
		$pv_total = $row['total'];
		$pv_deskripsi = $row['deskripsi'];
		$pv_coa = $row['coa'];
		$pv_nama_coa = $row['nama_coa'];
		$pv_no_cc = $row['no_cc'];
		$pv_cc_name = $row['cc_name'];
		$pv_per_ppn = $row['ppn'];
		$pv_total_ppn = $row['t_ppn'];
		$pv_pph = $row['pph'];
		$pv_total_pph = $row['total_pph'];
		$pv_coa_pph = $row['coa_pph'];
		$pv_coa_name_pph = $row['coa_name_pph'];
		$pv_coa_ppn = $row['coa_ppn'];
		$pv_coa_name_ppn = $row['coa_name_ppn'];

		$pv_total_idr = $pv_total * $rates;
		$pv_ppn_idr = $pv_total_ppn * $rates;
		$pv_pph_idr = $pv_total_pph * $rates;
		

		$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date)
VALUES
('$kode', '$bankout_date', '$type_ob', '$pv_coa', '$pv_nama_coa', '$pv_no_cc', '$pv_cc_name', '$pv_no_pv', '$pv_pv_date', '-', '-', '$pv_curr', '$rates','$pv_total', '0', '$pv_total_idr', '0', 'Draft', '$pv_deskripsi', '$create_by', '$create_date', '', '', '', '')";

		$executess2 = mysqli_query($conn2, $queryss2);


	if ($pv_per_ppn > 0) {
	$sqlcoa3 = mysqli_query($conn1,"SELECT no_coa, nama_coa from mastercoa_v2 where inv_type like '%PPN KBN%' Limit 1");
$rowcoa3 = mysqli_fetch_array($sqlcoa3);
$no_coa_ppn = $rowcoa3['no_coa'];
$nama_coa_ppn = $rowcoa3['nama_coa'];


$querys_ppn = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$bankout_date', '$type_ob', '$pv_coa_ppn', '$pv_coa_name_ppn', '$pv_no_cc', '$pv_cc_name', '$pv_no_pv', '$pv_pv_date', '-', '-', '$curr', '$rates', '$pv_total_ppn', '0', '$pv_ppn_idr', '0', 'Draft', '$pv_deskripsi','$create_by', '$create_date', '', '', '', '')";

 $executes_ppn = mysqli_query($conn2,$querys_ppn);
}else{

}

if ($pv_pph > 0) {


$querys_ppn = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
    ('$kode', '$bankout_date', '$type_ob', '$pv_coa_pph', '$pv_coa_name_pph', '$pv_no_cc', '$pv_cc_name', '$pv_no_pv', '$pv_pv_date', '-', '-', '$curr', '$rates', '0', '$pv_total_pph', '0', '$pv_pph_idr', 'Draft', '$pv_deskripsi','$create_by', '$create_date', '', '', '', '')";

 $executes_ppn = mysqli_query($conn2,$querys_ppn);
}else{

}


	}

}else{
$query2 = "INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr, rates, for_balance) 
VALUES 
	('$kode', '$no_pay', '$pay_date', '$due_date', '$dpp', '$ppn', '$pph', '$total', '$curr', '$total_idr', '$ratess', '$balance')";

$execute2 = mysqli_query($conn2,$query2);

$sql1lp = mysqli_query($conn2,"select DISTINCT * from (select no_coa,nama_coa,sum(amount) amount,if(memo = '',null,memo) memo from list_payment where no_payment = '$no_pay' GROUP BY no_coa
union
select no_coa,nama_coa,sum(total) amount,if(keterangan = '',null,keterangan) keterangan from saldo_awal where no_pay = '$no_pay' GROUP BY no_coa) a");
while($rowlp = mysqli_fetch_array($sql1lp)){
$no_coalp = $rowlp['no_coa'];
$nama_coalp = $rowlp['nama_coa'];
$amountlp = $rowlp['amount'];
$memolp = $rowlp['memo'];
$lp_total_idr = $amountlp * $rates;


$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$bankout_date', '$type_ob', '$no_coalp', '$nama_coalp', '-', '-', '$no_pay', '$pay_date', '-', '-', '$curr', '$rates', '$balance2', '0', '$balanceidr2', '0', 'Draft', '$pesan_ob', '$create_by', '$create_date', '', '', '', '')";

$executess2 = mysqli_query($conn2,$queryss2);


if ($pph > 0) {

	$sqlpph = mysqli_query($conn1,"select no_coa,nama_coa from mtax where idtax = (select max(a.idtax) idtax from kontrabon a INNER JOIN list_payment b on b.no_kbon = a.no_kbon where b.no_payment = '$no_pay' group by a.no_kbon limit 1)");
$rowpph = mysqli_fetch_array($sqlpph);
$no_coa_pph = $rowpph['no_coa'];
$nama_coa_pph = $rowpph['nama_coa'];


$querys_pph = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
    ('$kode', '$bankout_date', '$type_ob', '$no_coa_pph', '$nama_coa_pph', '-', '-', '$no_pay', '$pay_date', '-', '-', '$curr', '$rates', '0', '$pph2', '0', '$pphidr2', 'Draft', '$pesan_ob','$create_by', '$create_date', '', '', '', '')";

 $executes_pph = mysqli_query($conn2,$querys_pph);
}else{

}

}

}

if(strpos($no_pay, 'PV/NAG/') !== false) {
	$sql = "Update tbl_pv_h set outstanding = '0' where no_pv = '$no_pay'";
	$query_uptmm = "UPDATE memo_h set no_bankout = '$kode',status='PAID' where no_pv = '$no_pay' ";
	$execute_uptmm = mysqli_query($conn2,$query_uptmm);

	}else{
		if ($kodelp == 'REG' && $forbalance == '0') {
			$sql = "update list_payment set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		}elseif ($kodelp == 'CBD' && $forbalance == '0') {
			$sql = "update list_payment_cbd set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		}elseif ($kodelp == 'DP' && $forbalance == '0') {
			$sql = "update list_payment_dp set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		}elseif ($kodelp == 'SAL' && $forbalance == '0') {
			$sql = "update saldo_awal set status_int = '$status_int', status = 'Paid' where no_pay = '$no_pay'";
		}else{

		}

		$sqlac = "update status set no_pay ='$kode', tgl_pay = '$bankout_date' where no_lp = '$no_pay'";
		$queryac = mysqli_query($conn2,$sqlac);
	}

	$sqlyss = mysqli_query($conn2,$sql);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}
if(!$execute2){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>