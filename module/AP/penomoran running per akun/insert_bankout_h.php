<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankout = $_POST['no_bankout'];
$bankout_date =  date("Y-m-d",strtotime($_POST['bankout_date']));
$reff_doc = $_POST['reff_doc'];
$nama_supp = $_POST['nama_supp'];
$akun = $_POST['akun'];
$bank = $_POST['bank'];
$curr = $_POST['curr'];
$amount = $_POST['amount'];
$rate = $_POST['rate'];
$eqv_idr = $_POST['eqv_idr'];
$deskripsi = $_POST['deskripsi'];
$status = "Draft";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");


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


$sqlnkb = mysqli_query($conn2,"select max(no_bankout) from b_bankout_h where akun = '$akun'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(no_bankout)'];
 $urutan = (int) substr($kodeBarang, 20, 5);
 $urutan++;
 $bln = date("m");
 $thn = date("Y");
 $huruf = $no_bankout;
 $kode = $huruf ."/". sprintf("%05s", $urutan);

$sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM b_reportbank where akun = '$akun'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from b_reportbank where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$balance = $rowy['balance'];
$balance2 = $balance - $amount;

$query = "INSERT INTO b_bankout_h (no_bankout,bankout_date,reff_doc,nama_supp,akun,bank,curr,amount, outstanding,rate, eqv_idr,deskripsi,status, create_by,create_date,stat_bi) 
VALUES 
	('$kode', '$bankout_date', '$reff_doc', '$nama_supp', '$akun', '$bank', '$curr', '$amount', '$amount', '$rate', '$eqv_idr', '$deskripsi', '$status', '$create_user', '$create_date', 'N')";

	$queryss = "INSERT INTO b_reportbank (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance) 
VALUES 
	('$bankout_date', '$kode', '$deskripsi', '$akun', '', '', '$curr', '0', '$amount', '$balance2')";

$executess = mysqli_query($conn2,$queryss);

$execute = mysqli_query($conn2,$query);


if($execute){	
	echo 'Data Saved Successfully With No Outgoing Bank '; echo $kode;
}else{
}

mysqli_close($conn2);
?>