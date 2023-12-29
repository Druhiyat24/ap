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

if(strpos($no_bpb, '/IN/') !== false) {
if(strpos($no_bpb, 'GEN/') !== false) {
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, bpb.kpno, bpb.curr, bpb.confirm_by, bpb.id_supplier from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item where bpb.confirm='Y' and status_retur = 'N' and po_header.app = 'A' and cancel = 'N' and bpb.bpbno_int = '$no_bpb'");	
}else{
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, act_costing.kpno, bpb.curr, bpb.confirm_by, bpb.id_supplier from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item INNER JOIN po_item on po_item.id = bpb.id_po_item INNER JOIN jo on jo.id = bpb.id_jo INNER JOIN jo_det on jo_det.id_jo = jo.id INNER JOIN so on so.id = jo_det.id_so INNER JOIN act_costing on act_costing.id = so.id_cost where bpb.confirm='Y' and po_header.app = 'A' and status_retur = 'N' and bpb.cancel = 'N' and bpb.bpbno_int = '$no_bpb'");
}}
else{
	if(strpos($no_bpb, 'GEN/') !== false) {
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, bpb.kpno, bpb.curr, bpb.confirm_by, bpb.id_supplier from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item where bpb.confirm='Y' and po_header.app = 'A' and bpb.bpbno_int = '$no_bpb'");	
}else{
$sql = mysqli_query($conn1,"select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.id_item, masteritem.itemdesc, masteritem.color, masteritem.size, IF(bpb.qty_reject IS NULL,(bpb.qty), (bpb.qty - bpb.qty_reject)) as qty, bpb.unit, bpb.price ,po_header.tax, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, act_costing.kpno, bpb.curr, bpb.confirm_by, bpb.id_supplier from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier INNER JOIN masteritem on masteritem.id_item = bpb.id_item INNER JOIN jo on jo.id = bpb.id_jo INNER JOIN jo_det on jo_det.id_jo = jo.id INNER JOIN so on so.id = jo_det.id_so INNER JOIN act_costing on act_costing.id = so.id_cost where bpb.confirm='Y' and po_header.app = 'A' and bpb.bpbno_int = '$no_bpb'");
}
}

while($row= mysqli_fetch_assoc($sql)) { 
	$nama_supp = $row['Supplier'];
	$no_po = $row['pono'];
	$ws = $row['kpno'];	
	$curr = $row['curr'];
	$tgl_bpb = $row['bpbdate'];
	$top = $row['jml_pterms'];
	$item = $row['itemdesc'];
	$color = $row['color'];
	$size = $row['size'];
	$qty = $row['qty'];
	$uom = $row['unit'];
	$price = $row['price'];
	$confirm1 = $row['confirm_by'];
	$tax = $row['tax'];
	$id_item = $row['id_item'];
	$id_supplier = $row['id_supplier'];


$query = mysqli_query($conn2,"INSERT INTO bpb_new (no_bpb, pono, ws, tgl_bpb, tgl_po, supplier, itemdesc, color, size, qty, uom, price, tax, curr, create_user, confirm1, confirm2, confirm_date, is_invoiced, status, top, pterms, create_date, update_date, update_user, ceklist, start_date, end_date, id_item, id_supplier) 
VALUES 
	('$no_bpb', '$no_po', '$ws', '$tgl_bpb', '$tgl_po', '$nama_supp', '$item', '$color', '$size', '$qty', '$uom', '$price', '$tax', '$curr', '$create_user', '$confirm1', '','$confirm_date','$invoiced', '$status', '$top', '$pterms', '$create_date', '$update_date', '$create_user', '$ceklist', '$start_date', '$end_date', '$id_item', '$id_supplier') ");

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