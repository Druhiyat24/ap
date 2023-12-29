<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));
$referen = $_POST['referen'];
$nama_supp = $_POST['nama_supp'];
$akun = $_POST['akun'];
$bank = $_POST['bank'];
$curr = $_POST['curr'];
$coa = $_POST['coa'];
$cost = $_POST['cost'];
$nominal = $_POST['nominal'];
$rate = $_POST['rate'];
$eqv_idr = $_POST['eqv_idr'];
$pesan = $_POST['pesan'];
$status = "Draft";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");
$no_bk = $_POST['no_bk'];


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


$sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM b_reportbank where akun = '$akun'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from b_reportbank where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$balance = $rowy['balance'];
$balance2 = $balance + $nominal;

$query = "INSERT INTO tbl_bankin_arcollection (doc_num,date,ref_data,customer,akun,bank,curr,id_coa,id_cost_center, amount,rate,eqv_idr, outstanding,deskripsi, status,create_by,create_date) 
VALUES 
	('$doc_number', '$doc_date', '$referen', '$nama_supp', '$akun', '$bank', '$curr', '$coa', '$cost', '$nominal', '$rate', '$eqv_idr', '$nominal', '$pesan', '$status', '$create_user', '$create_date')";

	$queryss = "INSERT INTO b_reportbank (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance) 
VALUES 
	('$doc_date', '$doc_number', '$pesan', '$akun', '', '', '$curr','$nominal', '0', '$balance2')";

$execute = mysqli_query($conn2,$query);
$executes = mysqli_query($conn2,$queryss);


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	$sql2 = "update b_bankout_h set stat_bi='Y' where no_bankout = '$no_bk'";
$query2 = mysqli_query($conn2,$sql2);
	
}

mysqli_close($conn2);
?>