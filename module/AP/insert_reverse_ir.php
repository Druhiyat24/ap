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

		// IR dari menu Invoice Received juga punya baris di ir_kontrabon_h. Penjaga
		// "already used" (invoice & BPB) membaca tabel itu, jadi harus ikut Cancel -
		// kalau tidak, daftar menampilkan Cancel tapi invoice & BPB-nya tetap terkunci.
		// Langkahnya sama dgn kontrabon_new_cancel.php.
		$doc_esc = mysqli_real_escape_string($conn2, $no_kbon);
		$user_esc = mysqli_real_escape_string($conn2, $create_user);
		$qkb = mysqli_query($conn2,"select unik_code, no_reff from ir_kontrabon_h where doc_number = '$doc_esc' and (status is null or status <> 'Cancel') limit 1");
		$kb = $qkb ? mysqli_fetch_assoc($qkb) : null;

		mysqli_begin_transaction($conn2);

		$sql2 = "update ir_invoice_supp_h set status = 'Cancel', updated_at = '$create_date', cancel_ir_by = '$create_user', cancel_ir_date = '$create_date' where doc_number = '$no_kbon'";
		$execute2 = mysqli_query($conn2,$sql2);

		if ($execute2 && $kb) {
			$execute2 = mysqli_query($conn2,"update ir_kontrabon_h set status = 'Cancel', cancel_user = '$user_esc', cancel_date = '$create_date' where doc_number = '$doc_esc'")
				&& mysqli_query($conn2,"update ir_invoice_supp set status = 'Cancel' where doc_number = '$doc_esc'");
			if ($execute2 && !empty($kb['no_reff'])) {
				$execute2 = mysqli_query($conn2,"update ir_kontrabon_ref set status = 'Available' where ref_number = '" . mysqli_real_escape_string($conn2, $kb['no_reff']) . "' and status = 'Used'");
			}
		}

		if (!$execute2) {
			$err = mysqli_error($conn2);
			mysqli_rollback($conn2);
			die('Error: ' . $err);
		}
		mysqli_commit($conn2);

		// Bersihkan info Invoice/Faktur BPB milik IR ini supaya tidak muncul lagi di laporan_pembelian.
		if ($kb && !empty($kb['unik_code'])) {
			$bpbList = array();
			$bq = mysqli_query($conn2,"select distinct no_bpb from ir_kontrabon_bpb where unik_code = '" . mysqli_real_escape_string($conn2, $kb['unik_code']) . "' and no_bpb <> ''");
			while ($bq && ($br = mysqli_fetch_assoc($bq))) { $bpbList[] = "'" . mysqli_real_escape_string($conn2, $br['no_bpb']) . "'"; }
			if ($bpbList) {
				$inList = implode(',', $bpbList);
				mysqli_query($conn2,"update bpb_new set upt_dok_inv = null, upt_no_inv = null, upt_tgl_inv = null, upt_no_faktur = null, upt_tgl_faktur = null where no_bpb in ($inList) and upt_dok_inv = '$doc_esc'");
				mysqli_query($conn2,"update bpb_knitting set no_invoice = null, tgl_invoice = null, no_faktur = null, tgl_faktur = null where no_bpb in ($inList)");
			}
		}

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