<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankout = $_POST['no_bankout'];
$bankout_date =  date("Y-m-d",strtotime($_POST['bankout_date']));
$bulan =  date("m",strtotime($_POST['bankout_date']));
$tahun =  date("y",strtotime($_POST['bankout_date']));
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


$sqlnkb = mysqli_query($conn2,"select max(no_bankout) from b_bankout_h where akun = '$akun' and YEAR(bankout_date) = YEAR('$bankout_date') AND MONTH(bankout_date) = MONTH('$bankout_date')");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(no_bankout)'];
 $urutan = (int) substr($kodeBarang, 21, 5);
 $urutan++;
 $bln = $bulan;
 $thn = $tahun;
 $huruf = substr($no_bankout,0,14);
 $kode = $huruf ."/". $bln."".$thn ."/". sprintf("%05s", $urutan);

$sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM b_reportbank where akun = '$akun'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from b_reportbank where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$balance = $rowy['balance'];
$balance2 = $balance - $amount;

 $sqlcoa1 = mysqli_query($conn1,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
$rowcoa1 = mysqli_fetch_array($sqlcoa1);
$no_coa1 = $rowcoa1['no_coa'];
$nama_coa1 = $rowcoa1['nama_coa'];

$query = "INSERT INTO b_bankout_h (no_bankout,bankout_date,reff_doc,nama_supp,akun,bank,curr,amount, outstanding,rate, eqv_idr,deskripsi,status, create_by,create_date,stat_bi) 
VALUES 
    ('$kode', '$bankout_date', '$reff_doc', '$nama_supp', '$akun', '$bank', '$curr', '$amount', '$amount', '$rate', '$eqv_idr', '$deskripsi', '$status', '$create_user', '$create_date', 'N')";

$queryss = "INSERT INTO b_reportbank (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status) 
VALUES 
    ('$bankout_date', '$kode', '$deskripsi', '$akun', '', '', '$curr', '0', '$amount', '$balance2', '$status')";

$executess = mysqli_query($conn2,$queryss);

$execute = mysqli_query($conn2,$query);


$queryss3 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$bankout_date', '$reff_doc', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv_idr', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '')";

$executess3 = mysqli_query($conn2,$queryss3);


if($execute){   
    echo 'Data Saved Successfully With No Outgoing Bank '; echo $kode;
}else{
}

mysqli_close($conn2);
?>