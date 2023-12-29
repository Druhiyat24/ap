<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pco = $_POST['no_pco'];
$tgl_pco = date("Y-m-d",strtotime($_POST['tgl_pco']));
$no_coa = $_POST['no_coa'];
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
$amount_lp = $_POST['amount_lp'];
$status_int = 5;
$total_idr = $_POST['total_idr'];



// echo $no_bankout;
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

$sqlx = mysqli_query($conn2,"select max(id) as id FROM c_petty_cashout_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_pco) from c_petty_cashout_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_pco)'];

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO c_petty_cashout_adj_det (no_pco, tgl_pco,id_coa,reff_doc,reff_date,deskripsi,t_debit,t_credit) 
VALUES 
	('$kode', '$tgl_pco', '$no_coa', '$reff_doc', '$reff_date', '$deskripsi', '$debit', '$credit')";

$execute = mysqli_query($conn2,$query);
}

if ($no_pay == '') {
	
}else{
$query2 = "INSERT INTO c_petty_cashout_det (no_pco, tgl_pco,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr,amount) 
VALUES 
	('$kode', '$tgl_pco', '$no_pay', '$pay_date', '$due_date', '$dpp', '$ppn', '$pph', '$total', '$curr', '$total_idr', '$amount_lp')";

$execute2 = mysqli_query($conn2,$query2);


}

if(strpos($no_pay, 'PV/NAG/') !== false) {
	$sql = "Update tbl_pv_h set outstanding = '0' where no_pv = '$no_pay'";

	}else{
		// if ($kodelp == 'REG') {
		// 	$sql = "update list_payment set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		// }elseif ($kodelp == 'CBD') {
		// 	$sql = "update list_payment_cbd set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		// }elseif ($kodelp == 'DP') {
		// 	$sql = "update list_payment_dp set status_int = '$status_int', status = 'Paid' where no_payment= '$no_pay'";
		// }else{

		// }
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