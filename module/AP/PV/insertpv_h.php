<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pv = $_POST['no_pv'];
$pv_date =  date("Y-m-d",strtotime($_POST['pv_date']));
$nama_supp = $_POST['nama_supp'];
$sup_doc = $_POST['sup_doc'];
$ctb = $_POST['ctb'];
$pay_date =  date("Y-m-d",strtotime($_POST['pay_date']));
$pay_mth = $_POST['pay_mth'];
$curr = $_POST['curr'];
$forpay = $_POST['forpay'];
$frcc = $_POST['frcc'];
$tocc = $_POST['tocc'];
$ke = $_POST['ke'];
$dari = $_POST['dari'];
$no_cek = $_POST['no_cek'];
$cek_date = date("Y-m-d",strtotime($_POST['cek_date']));
$pesan = $_POST['pesan'];
$subtotal = $_POST['subtotal'];
$adjust = $_POST['adjust'];
$pph = $_POST['pph'];
$ppn = $_POST['ppn'];
$pilih_ppn = $_POST['pilih_ppn'];
$pilih_pph = $_POST['pilih_pph'];
$total = $_POST['total'];
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$status = "Draft";
$rat_pv = $_POST['rat_pv'];


// echo "< -- >";
// echo $no_kbon;
// echo "< -- >";
// echo $tgl_kbon;
// echo "< -- >";
// echo $valuta_ftr;
// echo "< -- >";
// echo $ttl_bayar;
// echo "< -- >";
// echo $cara_bayar;
// echo "< -- >";
// echo $account;
// echo "< -- >";
// echo $bank;
// echo "< -- >";


$query = "INSERT INTO tbl_pv_h (no_pv,pv_date,nama_supp,supp_doc,ctb,pay_date,pay_meth,curr,for_pay, frm_akun,to_akun,ke,dari,no_cek, cek_date,deskripsi,subtotal,adjust,pph,ppn,total,outstanding,per_ppn,per_pph,rate,create_by,create_date,status) 
VALUES 
	('$no_pv', '$pv_date', '$nama_supp', '$sup_doc', '$ctb', '$pay_date', '$pay_mth', '$curr', '$forpay', '$frcc', '$tocc', '$ke', '$dari', '$no_cek', '$cek_date', '$pesan', '$subtotal', '$adjust', '$pph', '$ppn', '$total', '$total', '$pilih_ppn', '$pilih_pph', '$rat_pv', '$create_user', '$create_date', '$status')";

$execute = mysqli_query($conn2,$query);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$query2 = "delete from supp_doc_temp";
	$execute2 = mysqli_query($conn2,$query2);
}

mysqli_close($conn2);
?>