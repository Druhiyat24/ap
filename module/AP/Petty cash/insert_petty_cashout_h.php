<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pco = $_POST['no_pco'];
$tgl_pco =  date("Y-m-d",strtotime($_POST['tgl_pco']));
$reff = $_POST['reff'];
$nama_supp = $_POST['nama_supp'];
$akun = $_POST['akun'];
$curr = $_POST['curr'];
$amount = $_POST['amount'];
$deskripsi = $_POST['deskripsi'];
$status = "Draft";
$create_by = $_POST['create_by'];
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


$sqlnkb = mysqli_query($conn2,"select max(no_pco) from c_petty_cashout_h where coa_akun = '$akun'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(no_pco)'];
 $urutan = (int) substr($kodeBarang, 15, 5);
 $urutan++;
 $bln = date("m");
 $thn = date("Y");
 $huruf = $no_pco;
 $kode = $huruf ."/". sprintf("%05s", $urutan);

 $sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM c_report_pettycash where akun = '$akun'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from c_report_pettycash where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$balance = $rowy['balance'];
$balance2 = $balance - $amount;

$query = "INSERT INTO c_petty_cashout_h (no_pco,tgl_pco,reff,nama_supp,coa_akun,curr,amount,deskripsi,status, create_by,create_date) 
VALUES 
    ('$kode', '$tgl_pco', '$reff', '$nama_supp', '$akun', '$curr', '$amount','$deskripsi', '$status', '$create_by', '$create_date')";

    $queryss = "INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance) 
VALUES 
    ('$tgl_pco', '$kode', '$deskripsi', '$akun', '', '', '$curr','$amount', '0', '$balance2')";

$execute = mysqli_query($conn2,$query);
$executes = mysqli_query($conn2,$queryss);


if(!$execute){  
   die('Error: ' . mysqli_error()); 
}else{
echo 'Data Saved Successfully With No Petty Cash In '; echo $kode;
// if ($no_bk == '') {
    
// }else{
// $sql2 = "update b_bankout_h set stat_bi='Y' where no_bankout = '$no_bk'";
// $query2 = mysqli_query($conn2,$sql2);
// }
    
}

mysqli_close($conn2);
?>