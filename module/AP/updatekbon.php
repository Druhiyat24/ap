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
$status = 'Post';
$create_user = $_POST['create_user'];
$no_bpb = $_POST['no_bpb'];
$no_po = $_POST['no_po'];
$tgl_bpb = $_POST['tgl_bpb'];
$sum_sub = $_POST['sum_sub'];
$sum_tax = $_POST['sum_tax'];
$sum_pph = $_POST['sum_pph'];
$sum_total = $_POST['sum_total'];
$status_invoice = 'Invoiced';
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date']));


$sql = "delete from kontrabon where no_kbon = '$no_kbon'";
$exec1 = mysqli_query($conn2,$sql);

if($exec1){
$sql1 = "update bpb set is_invoiced = 'Waiting' where no_bpb= '$no_bpb'";
$exec2 = mysqli_query($conn2,$sql1);
}

if($exec2){	
$query = "INSERT INTO kontrabon (no_kbon, tgl_kbon, id_jurnal, nama_supp, no_faktur, no_bpb, no_po, tgl_bpb, supp_inv, tgl_inv, tgl_tempo, subtotal, tax, idtax, pph_code, pph_value, total, curr, ceklist, post_date, update_date, status, create_user, create_date, start_date, end_date) 
VALUES 
	('$no_kbon', '$tgl_kbon', '$jurnal', '$nama_supp', '$no_faktur', '$no_bpb', '$no_po', '$tgl_bpb', '$supp_inv', '$tgl_inv', '$tgl_tempo', '$sum_sub', '$sum_tax', '$idtax', '$pph', '$sum_pph', '$sum_total', '$curr', '$ceklist', '$post_date', '$update_date', '$status', '$create_user', '$create_date', '$start_date', '$end_date')";
$execute = mysqli_query($conn2,$query);
}

if($execute){

$squery = mysqli_query($conn2,"update bpb set is_invoiced = '$status_invoice' where no_bpb= '$no_bpb'");	

}
//	echo 'Data Berhasil Di Simpan';
//}else{
//    die('Error: ' . mysql_error());	
//}
mysqli_close($conn2);
//$execute = mysql_query($query,$conn2);
?>