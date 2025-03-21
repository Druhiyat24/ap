<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$unik_code = $_POST['unik_code'];
$no_po = $_POST['no_po'];
$item = $_POST['item'];
$qty = $_POST['qty'];
$price = $_POST['price'];
$attn = $_POST['attn'];
$seasons = $_POST['seasons'];
$no_reff = $_POST['no_reff'];
$id_bpb = $_POST['id_bpb'];
$no_bpb = $_POST['no_bpb'];
$tgl_bpb = $_POST['tgl_bpb'];
$id_jo = $_POST['id_jo'];
$id_item = $_POST['id_item'];
$id_item = $_POST['id_item'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = "Y";


$sql = mysqli_query($conn2,"select DISTINCT no_req from req_dn_h where unik_code = '$unik_code'");
$row = mysqli_fetch_array($sql);
$kode = $row['no_req'];


$query = "INSERT INTO req_dn (no_req,no_po,item,qty,price,attn,seasons,no_reff,id_bpb, tgl_bpb, no_bpb, id_jo,id_item,unit, created_by,created_date) 
VALUES 
	('$kode', '$no_po', '$item', '$qty', '$price', '$attn', '$seasons', '$no_reff', '$id_bpb', '$tgl_bpb', '$no_bpb', '$id_jo', '$id_item', '$unit', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);

if($execute){	
	$sql = "delete from req_dn_po_detail_temp where created_by = '$create_user'";
	$exec = mysqli_query($conn2,$sql);
}else{
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>