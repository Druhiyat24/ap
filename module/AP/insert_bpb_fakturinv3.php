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
$unik_code = $_POST['unik_code'];

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

 $sqlnkb = mysqli_query($conn2,"select no_doc from log_bpb_faktur_inv where unik_code = '$unik_code'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['no_doc'];
	
$query = "INSERT INTO bpb_faktur_inv (no_dok, tgl_dok, no_inv, tgl_inv, no_faktur, tgl_faktur, no_bpb, tgl_bpb, nama_supp, status, created_by, created_date, jenis) 
VALUES 
	('$kode', '$tgl_dok', '$no_inv', '$tgl_inv', '$no_faktur', '$tgl_faktur', '$no_bpb', '$tgl_bpb', '$supplier', '$status', '$create_user', '$create_date', 'INV')";
$execute = mysqli_query($conn2,$query);

$sql_temp_h = "insert into bpb_scan_faktur_h select * from bpb_scan_faktur_temp_h where created_by = '$create_user' GROUP BY kd_no_faktur";
	$query_temp_h = mysqli_query($conn2,$sql_temp_h);

$sql_temp = "insert into bpb_scan_faktur select * from bpb_scan_faktur_temp where created_by = '$create_user'";
	$query_temp = mysqli_query($conn2,$sql_temp);

if ($no_bpb != '') {
	$sql = "update bpb_new set upt_dok_inv='$kode',upt_no_inv='$no_inv', upt_tgl_inv='$tgl_inv',upt_no_faktur='$no_faktur', upt_tgl_faktur='$tgl_faktur' where no_bpb='$no_bpb'";
	$query = mysqli_query($conn2,$sql);
}

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql3 = "Delete from tbl_bpb_temp where user_input='$create_user'";
	$query3 = mysqli_query($conn2,$sql3); 
}

if($sql_temp_h){	
   $sql4 = "Delete from bpb_scan_faktur_temp_h where created_by='$create_user'";
	$query4 = mysqli_query($conn2,$sql4); 	
}

if($sql_temp){	
   $sql5 = "Delete from bpb_scan_faktur_temp where created_by='$create_user'";
	$query5 = mysqli_query($conn2,$sql5); 	
}

mysqli_close($conn2);
?>