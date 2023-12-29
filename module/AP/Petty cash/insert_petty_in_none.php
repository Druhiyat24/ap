<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pci = $_POST['no_pci'];
$tgl_pci =  date("Y-m-d",strtotime($_POST['tgl_pci']));
$nomor_coa = $_POST['nomor_coa'];
$cost_ctr = $_POST['cost_ctr'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curre = $_POST['curre'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$keterangan = $_POST['keterangan'];



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
$sqlx = mysqli_query($conn2,"select max(id) as id FROM c_petty_cashin_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_pci) from c_petty_cashin_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_pci)'];


if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO c_petty_cashin_none (no_pci,tgl_pci,no_coa,no_costcenter,buyer,no_ws,curr,debit,credit,deskripsi) 
VALUES 
	('$kode', '$tgl_pci', '$nomor_coa', '$cost_ctr', '$buyer', '$no_ws', '$curre', '$debit', '$credit', '$keterangan')";

$execute = mysqli_query($conn2,$query);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>