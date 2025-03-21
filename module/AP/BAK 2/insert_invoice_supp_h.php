<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_num = $_POST['doc_num'];
$tgl_penerimaan = date("Y-m-d",strtotime($_POST['tgl_penerimaan']));
$unik_code = $_POST['unik_code'];
$nama_supp = $_POST['nama_supp'];
$deskripsi = $_POST['deskripsi'];
$total_amount = $_POST['total_amount'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = "Received";



$sql = mysqli_query($conn2,"select max(doc_number) from ir_invoice_supp_h");
$row = mysqli_fetch_array($sql);
$kodepay = $row['max(doc_number)'];
$urutan = (int) substr($kodepay, 16, 5);
$urutan++;
$bln = date("m");
$thn = date("Y");
$huruf = "IR/NAG/$thn/$bln/";
$kode = $huruf . sprintf("%05s", $urutan);


$query = "INSERT INTO ir_invoice_supp_h (doc_number,tgl_penerimaan,nama_supp,total_amount,deskripsi,status,updated_at,created_by,created_date,unik_code) 
VALUES 
	('$kode', '$tgl_penerimaan', '$nama_supp', '$total_amount', '$deskripsi', '$status', '$create_date', '$create_user', '$create_date', '$unik_code')";

$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	echo 'Data Saved Successfully With Document Number '; echo $kode;
}

mysqli_close($conn2);
?>