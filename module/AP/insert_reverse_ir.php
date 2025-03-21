<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_doc = $_POST['no_doc'];
$tgl_doc = date("Y-m-d",strtotime($_POST['tgl_doc']));
$keterangan = $_POST['keterangan'];
$no_trf = $_POST['no_trf'];
$tgl_trf = date("Y-m-d",strtotime($_POST['tgl_trf']));
$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$nama_supp = $_POST['nama_supp'];
$amount = $_POST['amount'];
$fil_kode = $_POST['fil_kode'];
$create_user = $_POST['create_user'];
$unik_code = $_POST['unik_code'];
$fil_trans = $_POST['fil_trans'];
$status = "Post";
$create_date = date("Y-m-d H:i:s");


$sql = mysqli_query($conn2,"select max(no_trans) doc_number from ir_log_trans where kode_trans = 'REIR' and unik_code = '$unik_code'");
$row = mysqli_fetch_array($sql);
$kode = $row['doc_number'];


$query = "INSERT INTO ir_reverse (no_reverse,tgl_reverse,keterangan,doc_number,tgl_doc,no_kbon,tgl_kbon,nama_supp,amount,created_by,created_date) 
VALUES 
	('$no_doc', '$tgl_doc', '$keterangan', '$no_trf', '$tgl_trf', '$no_kbon', '$tgl_kbon', '$nama_supp', '$amount', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);

if($fil_kode == 'IR'){

		$sql2 = "update ir_invoice_supp_h set status = 'Cancel', updated_at = '$create_date', cancel_ir_by = '$create_user', cancel_ir_date = '$create_date' where doc_number = '$no_kbon'";
		$execute2 = mysqli_query($conn2,$sql2);
		
	}elseif ($fil_kode == 'TFTA') {
		$sql2 = "update ir_invoice_supp_h set status = 'Received', updated_at = '$create_date', cancel_acc_by = '$create_user', cancel_acc_date = '$create_date',tfta_by = null, tfta_date = null, receive_acc_by = null, receive_acc_date = null where doc_number = '$no_kbon'";
		$execute2 = mysqli_query($conn2,$sql2);

		$sql3 = "update ir_trans_invoice_supp set status = 'Cancel', cancel_by = '$create_user', cancel_date = '$create_date' where no_trans = '$no_trf' and doc_number = '$no_kbon'";
		$execute3 = mysqli_query($conn2,$sql3);

	}elseif($fil_kode == 'TATP'){

		if ($fil_trans == '0') {
			$sql2 = "update ir_invoice_supp_h set status = 'Received', updated_at = '$create_date', cancel_pch_by = '$create_user', cancel_pch_date = '$create_date',tatp_by = null, tatp_date = null, receive_pch_by = null, receive_pch_date = null where doc_number = '$no_kbon'";
		}else{
			$sql2 = "update ir_invoice_supp_h set status = 'Accepted Acc', updated_at = '$create_date', cancel_pch_by = '$create_user', cancel_pch_date = '$create_date',tatp_by = null, tatp_date = null, receive_pch_by = null, receive_pch_date = null where doc_number = '$no_kbon'";
		}

		$execute2 = mysqli_query($conn2,$sql2);

		$sql3 = "update ir_trans_invoice_supp set status = 'Cancel', cancel_by = '$create_user', cancel_date = '$create_date' where no_trans = '$no_trf' and doc_number = '$no_kbon'";
		$execute3 = mysqli_query($conn2,$sql3);
		
	}elseif($fil_kode == 'TPTF'){
		$sql2 = "update ir_invoice_supp_h set status = 'Accepted Pch', updated_at = '$create_date', cancel_fin_by = '$create_user', cancel_fin_date = '$create_date', tptf_by = null, tptf_date = null, receive_fin_by = null, receive_fin_date = null where doc_number = '$no_kbon'";
		$execute2 = mysqli_query($conn2,$sql2);

		$sql3 = "update ir_trans_invoice_supp set status = 'Cancel', cancel_by = '$create_user', cancel_date = '$create_date' where no_trans = '$no_trf' and doc_number = '$no_kbon'";
		$execute3 = mysqli_query($conn2,$sql3);
	}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{

}

mysqli_close($conn2);
?>