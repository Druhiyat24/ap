<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrcbd = $_POST['noftrcbd'];
$tglftrcbd = date("Y-m-d",strtotime($_POST['tglftrcbd']));
$nama_supp = $_POST['nama_supp'];
$no_pi = $_POST['no_pi'];
$curr = $_POST['curr'];
$create_date = date("Y-m-d H:i:s");
$status = 'draft';
$keterangan = $_POST['keterangan'];
$create_user = $_POST['create_user'];
$no_po = $_POST['no_po'];
$tgl_po = $_POST['tgl_po'];
$sum_sub = $_POST['sum_sub'];
$sum_tax = $_POST['sum_tax'];
$sum_total = $_POST['sum_total'];
$invoiced = 'Waiting';
$tambah = '0';

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
	
$query = "INSERT INTO ftr_cbd (no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, subtotal, tax, total, curr, keterangan, status, create_user, create_date, is_invoiced,biaya_tambahan) 
VALUES 
	('$noftrcbd', '$tglftrcbd', '$nama_supp', '$no_po', '$tgl_po', '$no_pi', '$sum_sub', '$sum_tax', '$sum_total', '$curr', '$keterangan', '$status', '$create_user', '$create_date', '$invoiced', '$tambah')";
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>