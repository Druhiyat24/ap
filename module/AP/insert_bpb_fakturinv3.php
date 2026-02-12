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
$user = $_SESSION['username'];


// echo $sum_total;

 $sqlnkb = mysqli_query($conn2,"select no_doc from log_bpb_faktur_inv where unik_code = '$unik_code'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['no_doc'];
	
$query = "INSERT INTO bpb_faktur_inv select '','$kode', '$tgl_dok', no_inv, tgl_inv, no_faktur, tgl_faktur, no_bpb, tgl_bpb, nama_supp, '$status', '$create_user', '$create_date', 'INV', '', '' from tbl_bpb_temp where user_input = '$create_user' and (CHAR_LENGTH(no_inv) > 0 OR CHAR_LENGTH(no_faktur) > 0)";
$execute = mysqli_query($conn2,$query);

$sql_temp_h = "insert into bpb_scan_faktur_h select * from bpb_scan_faktur_temp_h where created_by = '$create_user' GROUP BY kd_no_faktur";
	$query_temp_h = mysqli_query($conn2,$sql_temp_h);

$sql_temp = "insert into bpb_scan_faktur select * from bpb_scan_faktur_temp where created_by = '$create_user'";
	$query_temp = mysqli_query($conn2,$sql_temp);

	$sql = "update bpb_new a
INNER JOIN (select no_inv, tgl_inv, no_faktur, tgl_faktur, no_bpb from tbl_bpb_temp where user_input = '$create_user' and (CHAR_LENGTH(no_inv) > 0 OR CHAR_LENGTH(no_faktur) > 0)) b ON b.no_bpb = a.no_bpb
SET a.upt_dok_inv='$kode',
		a.upt_no_inv = b.no_inv,
		a.upt_tgl_inv = b.tgl_inv,
		a.upt_no_faktur = b.no_faktur,
		a.upt_tgl_faktur = b.tgl_faktur";
	$query = mysqli_query($conn2,$sql);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
		$sql = "update bpb_new a
INNER JOIN (select no_inv, tgl_inv, no_faktur, tgl_faktur, no_bpb from tbl_bpb_temp where user_input = '$create_user' and (CHAR_LENGTH(no_inv) > 0 OR CHAR_LENGTH(no_faktur) > 0)) b ON b.no_bpb = a.no_bpb
SET a.upt_dok_inv='$kode',
		a.upt_no_inv = b.no_inv,
		a.upt_tgl_inv = b.tgl_inv,
		a.upt_no_faktur = b.no_faktur,
		a.upt_tgl_faktur = b.tgl_faktur";
	$queryupdate = mysqli_query($conn2,$sql);

	if($queryupdate){
	$sql3 = "Delete from tbl_bpb_temp where user_input='$create_user'";
	$query3 = mysqli_query($conn2,$sql3); 
}
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