<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_doc = $_POST['no_doc'];
$tgl_doc = date("Y-m-d",strtotime($_POST['tgl_doc']));
$keterangan = $_POST['keterangan'];
$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$nama_supp = $_POST['nama_supp'];
$amount = $_POST['amount'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = "Post";
$kode_trans = $_POST['kode_trans'];
$unik_code = $_POST['unik_code'];


$sql = mysqli_query($conn2,"select max(no_trans) doc_number from ir_log_trans where kode_trans = '$kode_trans' and unik_code = '$unik_code'");
$row = mysqli_fetch_array($sql);
$kode = $row['doc_number'];

$query = "INSERT INTO ir_trans_invoice_supp (nama_trans,no_trans,tgl_trans,keterangan,doc_number,tgl_doc,nama_supp,amount,status,created_by,created_date) 
VALUES 
	('$kode_trans', '$kode', '$tgl_doc', '$keterangan', '$no_kbon', '$tgl_kbon', '$nama_supp', '$amount', '$status', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	if ($kode_trans == 'TFTA') {
		$sql_upt = "update ir_invoice_supp_h set status = 'Post Fin To Acc', updated_at = '$create_date',tfta_by = '$create_user', tfta_date = '$tgl_doc', cancel_acc_by = null, cancel_acc_date = null where doc_number = '$no_kbon'";
	}elseif($kode_trans == 'TATP'){
		$sql_upt = "update ir_invoice_supp_h set status = 'Post Acc To Pch', updated_at = '$create_date',tatp_by = '$create_user', tatp_date = '$tgl_doc', cancel_pch_by = null, cancel_pch_date = null where doc_number = '$no_kbon'";
	}elseif($kode_trans == 'TPTF'){
		$sql_upt = "update ir_invoice_supp_h set status = 'Post Pch To Fin', updated_at = '$create_date',tptf_by = '$create_user', tptf_date = '$tgl_doc', cancel_fin_by = null, cancel_fin_date = null where doc_number = '$no_kbon'";
	}

	$execute_upt = mysqli_query($conn2,$sql_upt);
}

mysqli_close($conn2);
?>