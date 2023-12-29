<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pco = $_POST['no_pco'];
$tgl_pco =  date("Y-m-d",strtotime($_POST['tgl_pco']));
$bulan =  date("m",strtotime($_POST['tgl_pco']));
$tahun =  date("Y",strtotime($_POST['tgl_pco']));
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


$sqlnkb = mysqli_query($conn2,"select max(no_pco) from c_petty_cashout_h where coa_akun = '$akun' and YEAR(tgl_pco) = YEAR('$tgl_pco') AND MONTH(tgl_pco) = MONTH('$tgl_pco')");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(no_pco)'];
 $urutan = (int) substr($kodeBarang, 15, 5);
 $urutan++;
 $bln = $bulan;
 $thn = $tahun;
 $huruf = substr($no_pco,0,5);
 $kode = $huruf ."/". $thn."/".$bln ."/". sprintf("%05s", $urutan);

 $sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM c_report_pettycash where akun = '$akun'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from c_report_pettycash where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$balance = $rowy['balance'];
$balance2 = $balance - $amount;

$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$akun'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$query = "INSERT INTO c_petty_cashout_h (no_pco,tgl_pco,reff,nama_supp,coa_akun,curr,amount,deskripsi,status, create_by,create_date) 
VALUES 
    ('$kode', '$tgl_pco', '$reff', '$nama_supp', '$akun', '$curr', '$amount','$deskripsi', '$status', '$create_by', '$create_date')";

    $queryss = "INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance) 
VALUES 
    ('$tgl_pco', '$kode', '$deskripsi', '$akun', '', '', '$curr', '0','$amount', '$balance2')";

$execute = mysqli_query($conn2,$query);
$executes = mysqli_query($conn2,$queryss);

$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_pco', '$reff', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '')";

$executess2 = mysqli_query($conn2,$queryss2);


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