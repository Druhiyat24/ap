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
$oth_doc = $_POST['oth_doc'];
$total_nak = isset($_POST['total_nak']) ? $_POST['total_nak'] : 0;
$total_nag = isset($_POST['total_nag']) ? $_POST['total_nag'] : 0;
$h_profit_center = $_POST['h_profit_center'];




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

$query = "INSERT INTO c_petty_cashout_h (no_pco,tgl_pco,reff,nama_supp,coa_akun,curr,amount,deskripsi,status, create_by,create_date, reff_doc) 
VALUES 
('$kode', '$tgl_pco', '$reff', '$nama_supp', '$akun', '$curr', '$amount','$deskripsi', '$status', '$create_by', '$create_date', '$oth_doc')";

$queryss = "INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status) 
VALUES 
('$tgl_pco', '$kode', '$deskripsi', '$akun', '', '', '$curr', '0','$amount', '$balance2', '$status')";

$execute = mysqli_query($conn2,$query);
$executes = mysqli_query($conn2,$queryss);

if ($reff != 'List Payment') {

   if ($total_nag != 0) {
      $queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
      VALUES 
      ('$kode', '$tgl_pco', '$reff', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$total_nag', '0', '$total_nag', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '', 'NAG')";

      $executess2 = mysqli_query($conn2,$queryss2);
   }


   if ($total_nak != 0) {
      $queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
      VALUES 
      ('$kode', '$tgl_pco', '$reff', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$total_nak', '0', '$total_nak', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '', 'NAK')";

      $executess2 = mysqli_query($conn2,$queryss2);
   }
}else{

   $queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
      VALUES 
      ('$kode', '$tgl_pco', '$reff', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '', '$h_profit_center')";

      $executess2 = mysqli_query($conn2,$queryss2);
}


if(!$execute){  
   die('Error: ' . mysqli_error()); 
}else{
   $sql_upt = "update c_petty_cashout_h set settlement='Y' where no_pco = '$oth_doc'";
   $query_upt = mysqli_query($conn2,$sql_upt);
   echo 'Data Saved Successfully With No Petty Cash In '; echo $kode;

}

mysqli_close($conn2);
?>