<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_kbon = $_POST['no_kbon'];
$tgl_kbon = date("Y-m-d",strtotime($_POST['tgl_kbon']));
$jurnal = $_POST['jurnal'];
$nama_supp = $_POST['nama_supp'];
$no_faktur = $_POST['no_faktur'];
$supp_inv = $_POST['supp_inv'];
$tgl_inv = date("Y-m-d",strtotime($_POST['tgl_inv']));
$tgl_tempo = date("Y-m-d",strtotime($_POST['tgl_tempo']));
$pph = $_POST['pph'];
$idtax = $_POST['idtax'];
$curr = $_POST['curr'];
$ceklist = $_POST['ceklist'];
$create_date = date("Y-m-d H:i:s");
$post_date = date("Y-m-d H:i:s");
$update_date = date("Y-m-d H:i:s");
$status = 'draft';
$status_int = 2;
$create_user = $_POST['create_user'];
$no_cbd = $_POST['no_cbd'];
$no_po = $_POST['no_po'];
$tgl_po = $_POST['tgl_po'];
$sum_sub = $_POST['sum_sub'];
$sum_tax = $_POST['sum_tax'];
$sum_pph = $_POST['sum_pph'];
$sum_total = $_POST['sum_total'];
$status_invoice = 'Invoiced';
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));

if(isset($ceklist)){

	$sql = mysqli_query($conn1,"select no_po,tgl_po,no_pi,subtotal,tax,total,curr from ftr_cbd where no_ftr_cbd = '$no_cbd'");

while($row= mysqli_fetch_assoc($sql)) { 
	$f_no_po = $row['no_po'];
	$f_tgl_po = $row['tgl_po'];
	$f_subtotal = $row['subtotal'];	
	$f_tax = $row['tax'];
	$f_total = $row['total'];
	$f_curr = $row['curr'];

$query = "INSERT INTO kontrabon_cbd (no_kbon, tgl_kbon, id_jurnal, nama_supp, no_faktur, no_cbd, no_po, tgl_po, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, idtax, pph_code, pph_value, total, curr, ceklist, post_date, update_date, status, status_int, create_user, create_date, start_date, end_date) 
VALUES 
	('$no_kbon', '$tgl_kbon', '$jurnal', '$nama_supp', '$no_faktur', '$no_cbd', '$f_no_po', '$f_tgl_po', '$supp_inv', '$tgl_inv', '$tgl_tempo', '$f_subtotal', '$f_tax', '$idtax', '$pph', '$sum_pph', '$f_total', '$f_curr', '$ceklist', '$post_date', '$update_date', '$status', '$status_int', '$create_user', '$create_date', '$start_date', '$end_date')";
$execute = mysqli_query($conn2,$query);

}
}

if($execute){

$squery = mysqli_query($conn2,"update ftr_cbd set is_invoiced = '$status_invoice', kb_inv = '1' where no_ftr_cbd= '$no_cbd'");	

}

// echo $sum_sub;

//	echo 'Data Berhasil Di Simpan';
//}else{
//    die('Error: ' . mysql_error());	
//}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>