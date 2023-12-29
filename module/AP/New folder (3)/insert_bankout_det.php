<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankout = $_POST['no_bankout'];
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
// echo "< // >";
// echo $dpp;
// echo "< // >";
// echo $ppn;
// echo "< // >";
// echo $pph;
// echo "< // >";
// echo $total;
// echo "< // >";

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO b_bankout_adj_det (no_bankout,id_coa,reff_doc,reff_date,deskripsi,t_debit,t_credit) 
VALUES 
	('$no_bankout', '$no_coa', '$reff_doc', '$reff_date', '$deskripsi', '$debit', '$credit')";

$execute = mysqli_query($conn2,$query);
}

if ($no_pay == '') {
	
}else{
$query2 = "INSERT INTO b_bankout_det (no_bankout,no_reff,reff_date,due_date,dpp,ppn,pph,total,curr, eqv_idr) 
VALUES 
	('$no_bankout', '$no_pay', '$pay_date', '$due_date', '$dpp', '$ppn', '$pph', '$total', '$curr', '$total_idr')";

$execute2 = mysqli_query($conn2,$query2);


}

if(strpos($no_pay, 'PV/NAG/') !== false) {
	$sql = "Update tbl_pv_h set outstanding = '0' where no_pv = '$no_pay'";

	}else{
	$sql = "update list_payment set status_int = '$status_int' where no_payment= '$no_pay'";
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