<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$id_ctg = $_POST['id_ctg'];
$nm_ctg = $_POST['nm_ctg'];
$id_sub_ctg = $_POST['id_sub_ctg'];
$nm_sub_ctg = $_POST['nm_sub_ctg'];
$jns_trans = $_POST['jns_trans'];
$ditagihkan = $_POST['ditagihkan'];
$id_item = $_POST['id_item'];
$no_coa = $_POST['no_coa'];
$no_cc = $_POST['no_cc'];
$no_coa_cre = "1.01.03";
$nama_coa_cre = "1.01.03";
$keterangan = '=SUB KATEGORI" "(VENDOR)", "(BUYER)", "JENIS TRANSAKSI", "INVOICE VENDOR"';

$sqlx = mysqli_query($conn1,"select no_coa,nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowx = mysqli_fetch_array($sqlx);
$nama_coa = $rowx['nama_coa'];

$sqly = mysqli_query($conn1,"select no_cc,cc_name from b_master_cc where status = 'Active' and no_cc = '$no_cc'");
$rowy = mysqli_fetch_array($sqly);
$cc_name = $rowy['cc_name'];

$sqlz = mysqli_query($conn1,"select id,item_name from master_memo_item where aktif = 'Y' and id = '$id_item'");
$rowz = mysqli_fetch_array($sqlz);
$item_name = $rowz['item_name'];

$query = "INSERT INTO memo_mapping_v2 (jns_trans,ditagihkan,id_ctg,nm_ctg,id_sub_ctg,nm_sub_ctg,id_item,item_name,no_coa,nama_coa,no_coa_cre,nama_coa_cre,id_cc,cc_name,keterangan,status) 
VALUES 
	('$jns_trans', '$ditagihkan', '$id_ctg', '$nm_ctg', '$id_sub_ctg', '$nm_sub_ctg','$id_item','$item_name','$no_coa','$nama_coa','$no_coa_cre','$nama_coa_cre','$no_cc','$cc_name','$keterangan','Y')";

$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	// echo 'Data Saved Successfully With No Cash In '; echo $kode;
}

mysqli_close($conn2);
?>