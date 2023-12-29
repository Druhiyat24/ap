<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pco = $_POST['no_pco'];
$tgl_pco = date("Y-m-d",strtotime($_POST['tgl_pco']));
$no_coa = $_POST['no_coa'];
$reff_doc = $_POST['reff_doc'];
$reff_date = date("Y-m-d",strtotime($_POST['reff_date']));
$deskripsi = $_POST['deskripsi'];
$pesan = $_POST['pesan'];
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
$amount_lp = $_POST['amount_lp'];
$status_int = 5;
$total_idr = $_POST['total_idr'];
$rate = $total_idr / $total;
$amount_fgn = $amount_lp / $rate;


echo $no_coa;
echo "< // >";
echo $reff_doc;
echo "< // >";
echo $reff_date;
echo "< // >";
echo $deskripsi;
echo "< // >";
echo $debit;
echo "< // >";
echo $credit;


$sqlx = mysqli_query($conn2,"select max(id) as id FROM c_petty_cashout_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_pco),reff,create_date,create_by from c_petty_cashout_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_pco)'];
 $type_co = $rownkb['reff'];
 $create_date = $rownkb['create_date'];
 $create_by = $rownkb['create_by'];

$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO c_petty_cashout_adj_det (no_pco, tgl_pco,id_coa,reff_doc,reff_date,deskripsi,t_debit,t_credit) 
VALUES 
	('$kode', '$tgl_pco', '$no_coa', '$reff_doc', '$reff_date', '$deskripsi', '$debit', '$credit')";

$execute = mysqli_query($conn2,$query);


$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_pco', '$type_co', '$no_coa', '$nama_coa', '-', '-', '$reff_doc', '$reff_date', '-', '-', 'IDR', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);
}

if ($no_pay == '') {
	
}else{
$query2 = "INSERT INTO c_petty_cashout_det (no_pco, tgl_pco,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr,amount) 
VALUES 
	('$kode', '$tgl_pco', '$no_pay', '$pay_date', '$due_date', '$dpp', '$ppn', '$pph', '$total', '$curr', '$total_idr', '$amount_lp')";

$execute2 = mysqli_query($conn2,$query2);

$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_pco', '$type_co', '-', '-', '-', '-', '$no_pay', '$pay_date', '-', '-', '$curr', '$rate', '$amount_fgn', '0', '$amount_lp', '0', 'Draft', '$pesan', '$create_by', '$create_date', '', '', '', '')";

$executess2 = mysqli_query($conn2,$queryss2);

}

if(strpos($no_pay, 'PV/NAG/') !== false) {
	$sql = "Update tbl_pv_h set outstanding = '0' where no_pv = '$no_pay'";

	}else{
		if ($kodelp == 'REG') {
			$sql = "update list_payment set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		}elseif ($kodelp == 'CBD') {
			$sql = "update list_payment_cbd set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		}elseif ($kodelp == 'DP') {
			$sql = "update list_payment_dp set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		}else{

		}
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