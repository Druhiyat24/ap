<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_doc = $_POST['no_doc'];
$tgl_doc =  date("Y-m-d",strtotime($_POST['tgl_doc']));
$supplier = $_POST['supplier'];
$no_po = $_POST['no_po'];
$no_ws = $_POST['no_ws'];
$id_jo = $_POST['id_jo'];
$id_item = $_POST['id_item'];
$qty = $_POST['qty'];
$unit = $_POST['unit'];
$price = $_POST['price'];
$deskripsi = $_POST['deskripsi'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");





// echo $doc_number;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;


if($qty != ''){
$query = "INSERT INTO ca_adjust_input (no_dok, tgl_periode, supplier, no_po, no_ws, id_jo, id_item, qty, unit, price, deskripsi, status, create_by, create_date) 
VALUES 
	('$no_doc', '$tgl_doc', '$supplier', '$no_po', '$no_ws','$id_jo', '$id_item', '$qty', '$unit', '$price','$deskripsi', 'Y', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);

$executess = mysqli_query($conn2,$queryss);
}else{
	
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>