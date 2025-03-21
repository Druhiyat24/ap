<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

// $txt_idctg1 = trim($_POST['txt_idctg1']);
$nama_item = $_POST['nama_item'];
$price = $_POST['price'];
$qty = $_POST['qty'];
$ttl_harga = $_POST['ttl_harga'];
$diskon = $_POST['diskon'];
$dpp = $_POST['dpp'];
$ppn = $_POST['ppn'];
$tarif_ppn_bm = $_POST['tarif_ppn_bm'];
$ppn_bm = $_POST['ppn_bm'];
$kd_no_faktur = $_POST['kd_no_faktur'];
$status = "Active";
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];



// echo $status;

$d_query2 = "delete from bpb_scan_faktur_temp where kd_no_faktur = '$kd_no_faktur' and created_by = '$create_user'";
$d_execute2 = mysqli_query($conn2,$d_query2);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>