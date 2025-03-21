<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

// $txt_idctg1 = trim($_POST['txt_idctg1']);
$kd_jns_trans = $_POST['kd_jns_trans'];
$fg_pengganti = $_POST['fg_pengganti'];
$no_faktur = $_POST['no_faktur'];
$tglfaktur = date("Y-m-d",strtotime($_POST['tglfaktur']));
$npwp_supp = $_POST['npwp_supp'];
$nama_supp = $_POST['nama_supp'];
$alamat_supp = $_POST['alamat_supp'];
$npwp_buyer = $_POST['npwp_buyer'];
$nama_buyer = $_POST['nama_buyer'];
$alamat_buyer = $_POST['alamat_buyer'];
$jml_dpp = $_POST['jml_dpp'];
$jml_ppn = $_POST['jml_ppn'];
$jml_ppn_bm = $_POST['jml_ppn_bm'];
$status_approve = $_POST['status_approve'];
$status_faktur = $_POST['status_faktur'];
$no_referensi = $_POST['no_referensi'];
$kd_no_faktur_h = $_POST['kd_no_faktur_h'];
$status = "Active";
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];



$d_query = "delete from bpb_scan_faktur_temp_h where kd_no_faktur = '$kd_no_faktur_h' and created_by = '$create_user'";
$d_execute = mysqli_query($conn2,$d_query);



$query = "INSERT INTO bpb_scan_faktur_temp_h (kd_jns_trans, fg_pengganti, no_faktur, tgl_faktur, npwp_supp, nama_supp, alamat_supp, npwp_buyer, nama_buyer, alamat_buyer, jml_dpp, jml_ppn, jml_ppn_bm, status_approve, status_faktur, no_referensi, kd_no_faktur,created_by,created_date) 
VALUES 
	('$kd_jns_trans', '$fg_pengganti', '$no_faktur', '$tglfaktur', '$npwp_supp', '$nama_supp', '$alamat_supp', '$npwp_buyer', '$nama_buyer', '$alamat_buyer', '$jml_dpp', '$jml_ppn', '$jml_ppn_bm', '$status_approve', '$status_faktur', '$no_referensi', '$kd_no_faktur_h', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>