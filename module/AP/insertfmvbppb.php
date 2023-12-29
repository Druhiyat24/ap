<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bppb = $_POST['no_bppb'];
$ceklist = $_POST['ceklist'];
$create_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'GMF';
$create_user = $_POST['create_user'];
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));
$invoiced = 'Waiting';
$confirm_date = '0000-00-00 00:00:00';
$no_ro1 = $_POST['no_ro'];
$no_bpb = $_POST['no_bpb'];
$confirm = $_POST['confirm_by'];

if(strpos($no_bppb, 'WIP/OUT') !== false) {
$sql = mysqli_query($conn1,"select DISTINCT bppb.id,bppb.bppbno_int as bppbno_int, bppb.bppbdate as bppbdate,bpb.pono as no_po, bppb.bpbno_ro as bpbno_ro, bppb.qty as qty, bppb.price as price, po_header.tax as tax, po_header.podate, bppb.unit as unit, bppb.confirm_by as confirm_by, bppb.curr as curr, masteritem.itemdesc, masteritem.color, masteritem.size, mastersupplier.Supplier as Supplier from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier INNER JOIN masteritem on masteritem.id_item = bppb.id_item right join bpb on bpb.id = bppb.id_bpb left JOIN po_header on po_header.pono = bpb.pono where bppb.cancel != 'Y' and bppb.confirm = 'Y' and bppbno_int = '$no_bppb' and bppb.ap_inv is null
");	
}else{
	$sql = mysqli_query($conn1,"select DISTINCT bppb.id,bppb.bppbno_int as bppbno_int, bppb.bppbdate as bppbdate,bpb.pono as no_po, bppb.bpbno_ro as bpbno_ro, bppb.qty as qty, bppb.price as price, po_header.tax as tax, po_header.podate, bppb.unit as unit, bppb.confirm_by as confirm_by, bppb.curr as curr, masteritem.itemdesc, masteritem.color, masteritem.size, mastersupplier.Supplier as Supplier from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier INNER JOIN masteritem on masteritem.id_item = bppb.id_item right join bpb on bpb.bpbno = bppb.bpbno_ro left JOIN po_header on po_header.pono = bpb.pono where bppb.cancel != 'Y' and bppb.confirm = 'Y' and bppbno_int = '$no_bppb' and bppb.ap_inv is null
");	

}

while($row= mysqli_fetch_assoc($sql)) { 
	$nama_supp = $row['Supplier'];
	$no_ro = $row['bpbno_ro'];
	$bppbno_int = $row['bppbno_int'];
	$curr = $row['curr'];
	$tgl_bppb = $row['bppbdate'];
	$item = $row['itemdesc'];
	$color = $row['color'];
	$size = $row['size'];
	$color = $row['color'];
	$pono = $row['no_po'];
	$tgl_po = $row['podate'];
	$tax = $row['tax'];
	$qty = $row['qty'];
	$uom = $row['unit'];
	$price = $row['price'];
	$confirm_by = $row['confirm_by'];
	$id_bppb = $row['id'];


$query = mysqli_query($conn2,"INSERT INTO bppb_new (no_bppb, no_ro, tgl_bppb, no_bpb, no_po, tgl_po, supplier, itemdesc, color, size, qty, uom, price, tax, curr, create_user, confirm1, confirm2, confirm_date, is_invoiced, status, create_date, update_date, update_user, ceklist, id_bppb) 
VALUES 
	('$bppbno_int', '$no_ro', '$tgl_bppb', '$no_bpb', '$pono', '$tgl_po', '$nama_supp', '$item', '$color', '$size', '$qty', '$uom', '$price', '$tax', '$curr', '$create_user', '$confirm_by', '','$confirm_date','$invoiced', '$status', '$create_date', '$update_date', '$create_user', '$ceklist', '$id_bppb') ");

if ($no_ro == null) {
$sqla = "update bppb set ap_inv='1' where bppbno_int='$bppbno_int'";
$querya = mysqli_query($conn1,$sqla);
}else{

$sqla = "update bppb set ap_inv='1' where bpbno_ro='$no_ro'";
$querya = mysqli_query($conn1,$sqla);
}

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