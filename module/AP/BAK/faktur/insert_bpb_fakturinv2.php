<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_dok = $_POST['no_dok'];
$tgl_dok = date("Y-m-d",strtotime($_POST['tgl_dok']));
$no_inv = $_POST['no_inv'];
$tgl_inv = date("Y-m-d",strtotime($_POST['tgl_inv']));
$no_faktur = $_POST['no_faktur'];
$tgl_faktur = date("Y-m-d",strtotime($_POST['tgl_faktur']));
$no_bpb = $_POST['no_bpb'];
$tgl_bpb = date("Y-m-d",strtotime($_POST['tgl_bpb']));
$supplier = $_POST['supplier'];
$create_user = $_POST['create_user'];
$status = 'POST';
$create_date = date("Y-m-d H:i:s");

// echo $noftrcbd;
// echo $tglftrcbd;
// echo $nama_supp;
// echo $no_pi;
// echo $curr;
// echo $create_date;
// echo $status;
// echo $create_user;
// echo $no_po;
// echo $tgl_po;
// echo $sum_sub;
// echo $sum_tax;
// echo $sum_total;

 $sqlnkb = mysqli_query($conn2,"select max(no_dok) from bpb_faktur_inv where jenis = 'FAK'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(no_dok)'];
 $urutan = (int) substr($kodeBarang, 14, 5);
 $urutan++;
 $bln = date("m");
 $thn = date("y");
 $huruf = "FAK/NAG/$bln$thn/";
 $kode = $huruf . sprintf("%05s", $urutan);
	
$query = "INSERT INTO bpb_faktur_inv (no_dok, tgl_dok, no_inv, tgl_inv, no_faktur, tgl_faktur, no_bpb, tgl_bpb, nama_supp, status, created_by, created_date, jenis) 
VALUES 
	('$kode', '$tgl_dok', '$no_inv', '$tgl_inv', '$no_faktur', '$tgl_faktur', '$no_bpb', '$tgl_bpb', '$supplier', '$status', '$create_user', '$create_date', 'FAK')";
$execute = mysqli_query($conn2,$query);

if ($no_bpb != '') {
	$sql = "update bpb_new set upt_dok_faktur='$kode', upt_no_faktur2='$no_faktur', upt_tgl_faktur2='$tgl_faktur' where no_bpb='$no_bpb'";
	$query = mysqli_query($conn2,$sql);
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql3 = "Delete from tbl_bpb_temp where user_input='$create_user'";
	$query3 = mysqli_query($conn2,$sql3); 
}

mysqli_close($conn2);
?>