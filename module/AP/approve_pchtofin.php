<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$no_kbon = $_POST['no_kbon'];
$approve_user = $_POST['approve_user'];
$confirm_date = date("Y-m-d H:i:s");

if(isset($no_dok)){
$sql = "update ir_invoice_supp_h set status = 'Accepted Fin', updated_at = '$confirm_date', receive_fin_by = '$approve_user', receive_fin_date = '$confirm_date' where doc_number = '$no_kbon'";
$execute = mysqli_query($conn2,$sql);

$sql2 = "update ir_trans_invoice_supp set status = 'Approved', approved_by = '$approve_user', approved_date = '$confirm_date' where no_trans = '$no_dok' and doc_number = '$no_kbon'";
$execute2 = mysqli_query($conn2,$sql2);
}else{
	die('Error: ' . mysqli_error());		
}

if($execute){
echo 'Data Berhasil Di Approve';
}

mysqli_close($conn2);

?>