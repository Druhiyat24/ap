<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_req = $_POST['no_req'];
$tgl_req = date("Y-m-d",strtotime($_POST['tgl_req']));
$nama_supp = $_POST['nama_supp'];
$total_amount = $_POST['total_amount'];
$deskripsi = $_POST['deskripsi'];
$status = "Post";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$unik_code = $_POST['unik_code'];



$sql = mysqli_query($conn2,"select max(SUBSTR(no_req,15)) no_req from req_dn_h");
$row = mysqli_fetch_array($sql);
$kodepay = $row['no_req'];
$urutan = (int) substr($kodepay, 0, 5);
$urutan++;
$bln = date("m");
$thn = date("y");
$huruf = "RQDN/NAG/$bln$thn/";
$kode = $huruf . sprintf("%05s", $urutan);



$query = "INSERT INTO req_dn_h (no_req,tgl_req,nama_supp,total_amount,deskripsi,status,created_by,created_date,unik_code) 
VALUES 
	('$kode', '$tgl_req', '$nama_supp', '$total_amount', '$deskripsi', '$status', '$create_user', '$create_date', '$unik_code')";

$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	echo 'Data Saved Successfully With Document Number '; echo $kode;
}

mysqli_close($conn2);
?>