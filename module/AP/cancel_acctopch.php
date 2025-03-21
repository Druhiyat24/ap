<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$no_kbon = $_POST['no_kbon'];
$filter = $_POST['filter'];
$approve_user = $_POST['approve_user'];
$confirm_date = date("Y-m-d H:i:s");

if(isset($no_dok)){
	if ($filter == '0') {
		$sql = "update ir_invoice_supp_h set status = 'Received', updated_at = '$confirm_date', cancel_pch_by = '$approve_user', cancel_pch_date = '$confirm_date',tatp_by = null, tatp_date = null where doc_number = '$no_kbon'";
	}else{
		$sql = "update ir_invoice_supp_h set status = 'Accepted Acc', updated_at = '$confirm_date', cancel_pch_by = '$approve_user', cancel_pch_date = '$confirm_date',tatp_by = null, tatp_date = null where doc_number = '$no_kbon'";
	}

	$execute = mysqli_query($conn2,$sql);

$sql2 = "update ir_trans_invoice_supp set status = 'Cancel', cancel_by = '$approve_user', cancel_date = '$confirm_date' where no_trans = '$no_dok' and doc_number = '$no_kbon'";
$execute2 = mysqli_query($conn2,$sql2);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Cancel';
}

mysqli_close($conn2);

?>