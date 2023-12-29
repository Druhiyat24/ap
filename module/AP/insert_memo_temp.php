<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_memo = $_POST['no_memo'];
$tgl_memo = date("Y-m-d",strtotime($_POST['tgl_memo']));
$jenis_transaksi = $_POST['jenis_transaksi'];
$supplier = $_POST['supplier'];
$biaya = $_POST['biaya'];
$user = $_POST['user'];





echo $no_memo;
echo "< -- >";
echo $tgl_memo;
echo "< -- >";
echo $jenis_transaksi;
echo "< -- >";
echo $supplier;
echo "< -- >";
echo $biaya;
echo "< -- >";
echo $user;

if ($no_memo != '') {
	$query = "INSERT INTO tbl_pv_memo_temp (no_memo,tgl_memo,jenis_transaksi,supplier,biaya,user) 
VALUES 
	('$no_memo', '$tgl_memo', '$jenis_transaksi','$supplier', '$biaya', '$user')";

$execute = mysqli_query($conn2,$query);  

}else{
	$query = 'select max(id) from master_coa_ctg1';
	$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>