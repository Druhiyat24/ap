<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id_bpb = $_POST['id_bpb'];
$no_po = $_POST['no_po'];
$no_bpb = $_POST['no_bpb'];
$tgl_bpb =  date("Y-m-d H:i:s",strtotime($_POST['tgl_bpb']));
$id_jo = $_POST['id_jo'];
$id_item = $_POST['id_item'];
$itemdesc = $_POST['itemdesc'];
$qty = $_POST['qty'];
$qty_tagih = $_POST['qty_tagih'];
$price = $_POST['price'];
$price_tagih = $_POST['price_tagih'];
$unit = $_POST['unit'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");

$query = "INSERT INTO req_dn_po_detail_temp (id_bpb, no_po, no_bpb, tgl_bpb, id_jo, id_item, itemdesc, qty, qty_tagih, price, price_tagih, unit, created_by, created_date) 
VALUES 
	('$id_bpb', '$no_po', '$no_bpb', '$tgl_bpb', '$id_jo', '$id_item', '$itemdesc', '$qty', '$qty_tagih', '$price', '$price_tagih', '$unit', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>
