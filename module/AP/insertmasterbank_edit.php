<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$tgl_active =  date("Y-m-d",strtotime($_POST['tgl_active']));
$tgl_active2 =  date("Y-m-d H:i:s",strtotime($_POST['tgl_active']));
$no_doc = $_POST['no_doc'];
$sob = $_POST['sob'];
$account = $_POST['account'];
$bank = $_POST['bank'];
$curr = $_POST['curr'];
$pesan = $_POST['pesan'];
$maxid = $_POST['maxid'];
$cek_sb1 = $_POST['cek_sb1'];
$status = "Active";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");


$sql = "update b_masterbank set active_date = '$tgl_active', sob = '$sob', bank_account = '$account', bank_name = '$bank', curr = '$curr', deskripsi = '$pesan', jurnal_sb = '$cek_sb1' where doc_number = '$no_doc'";
$execute = mysqli_query($conn2,$sql);

$query2 = "INSERT INTO tbl_log (nama, activity, tanggal_input, doc_number) 
VALUES 
	('$create_user', 'Edit Bank', '$create_date', '$no_doc')";

$execute2 = mysqli_query($conn2,$query2);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>