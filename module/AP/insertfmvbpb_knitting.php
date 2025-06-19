<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bpb = $_POST['no_bpb'];
$no_bpb_asal = $_POST['no_bpb_asal'];
$ceklist = $_POST['ceklist'];
$create_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'GMF';
$create_user = $_POST['create_user'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$invoiced = 'Waiting';
$confirm_date = '0000-00-00 00:00:00';
$tgl_po = $_POST['tgl_po'];
$pterms = $_POST['pterms'];


$sql = mysqli_query($conn1,"select no_bpb, no_po, '-' no_ws, tgl_bpb, '' tgl_po, c.id_supplier, a.supplier, nama_barang, '-' color, '-' size, jumlah_bpb qty,satuan uom, price, round(ppn/dpp * 100,0) tax, curr, '-' create_user, 'ibrahim' confirm1, b.id_item from bpb_knitting a inner join masteritem b on b.goods_code = a.id_item INNER JOIN (select DISTINCT id_supplier, supplier from mastersupplier where tipe_sup = 'S' GROUP BY supplier) c on c.supplier = a.supplier where a.no_bpb = '$no_bpb'");	


while($row= mysqli_fetch_assoc($sql)) { 
	$nama_supp = $row['supplier'];
	$no_po = $row['no_po'];
	$ws = $row['no_ws'];	
	$curr = $row['curr'];
	$tgl_bpb = $row['tgl_bpb'];
	$top = $pterms;
	$item = $row['nama_barang'];
	$color = '-';
	$size = '-';
	$qty = $row['qty'];
	$uom = $row['uom'];
	$price = $row['price'];
	$confirm1 = $row['confirm1'];
	$tax = $row['tax'];
	$id_item = $row['id_item'];
	$id_supplier = $row['id_supplier'];


$query = mysqli_query($conn2,"INSERT INTO bpb_new (no_bpb, pono, ws, tgl_bpb, tgl_po, supplier, itemdesc, color, size, qty, uom, price, tax, curr, create_user, confirm1, confirm2, confirm_date, is_invoiced, status, top, pterms, create_date, update_date, update_user, ceklist, start_date, end_date, id_item, id_supplier, profit_center) 
VALUES 
	('$no_bpb', '$no_po', '$ws', '$tgl_bpb', '$tgl_po', '$nama_supp', '$item', '$color', '$size', '$qty', '$uom', '$price', '$tax', '$curr', '$create_user', '$confirm1', '','$confirm_date','$invoiced', '$status', '$top', '-', '$create_date', '$update_date', '$create_user', '$ceklist', '$start_date', '$end_date', '$id_item', '$id_supplier', 'NAK') ");

$query23 = mysqli_query($conn2,"INSERT INTO bpb_ri (bpb1, bpb2) 
VALUES 
	('$no_bpb', '$no_bpb_asal') ");

$sqla = "update bpb set ap_inv='1' where bpbno_int='$no_bpb'";
$querya = mysqli_query($conn1,$sqla);

if(!$query){
    die('Error: ' . mysqli_error());	
}
}
//echo 'Data Berhasil Di Simpan';	

mysqli_close($conn2);

//if($query){
//	echo '<script type="text/javascript">alert("Data has been submitted");</script>';
//}else {
//	echo '<script type="text/javascript">alert("Error");</script>';
//}	
?>