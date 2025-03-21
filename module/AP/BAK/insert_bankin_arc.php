<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$doc_date =  date("Y-m-d",strtotime($_POST['doc_date']));
$bulan =  date("m",strtotime($_POST['doc_date']));
$tahun =  date("y",strtotime($_POST['doc_date']));
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

$sqlnkb = mysqli_query($conn2,"select max(doc_num) from tbl_bankin_arcollection where akun = '$akun' and YEAR(date) = YEAR('$doc_date') AND MONTH(date) = MONTH('$doc_date')");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kodeBarang = $rownkb['max(doc_num)'];
 $urutan = (int) substr($kodeBarang, 21, 5);
 $urutan++;
 $bln = $bulan;
 $thn = $tahun;
 $huruf = substr($doc_number,0,14);
 $kode = $huruf ."/". $bln."".$thn ."/". sprintf("%05s", $urutan);

 $sqlcoa1 = mysqli_query($conn1,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
$rowcoa1 = mysqli_fetch_array($sqlcoa1);
$no_coa1 = $rowcoa1['no_coa'];
$nama_coa1 = $rowcoa1['nama_coa'];

 $sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$cost'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;


$sqlx = mysqli_query($conn1,"select if(max(id) is null,'0',max(id)) as id FROM b_reportbank where akun = '$akun'");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from b_reportbank where id = '$maxid'");
$rowy = mysqli_fetch_array($sqly);
$balance = $rowy['balance'];
$balance2 = $balance + $nominal;

$query = "INSERT INTO tbl_bankin_arcollection (doc_num,date,ref_data,customer,akun,bank,curr,id_coa,id_cost_center, amount,rate,eqv_idr, outstanding,deskripsi, status,create_by,create_date) 
VALUES 
	('$kode', '$doc_date', '$referen', '$nama_supp', '$akun', '$bank', '$curr', '$coa', '$cost', '$nominal', '$rate', '$eqv_idr', '$nominal', '$pesan', '$status', '$create_user', '$create_date')";

	$queryss = "INSERT INTO b_reportbank (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status) 
VALUES 
	('$doc_date', '$kode', '$pesan', '$akun', '', '', '$curr','$nominal', '0', '$balance2', '$status')";

$execute = mysqli_query($conn2,$query);
$executes = mysqli_query($conn2,$queryss);

if ($referen == 'Bank Keluar') {
   $sqlbk = mysqli_query($conn1,"select bankout_date from b_bankout_h where no_bankout = '$no_bk'");
   $rowbk = mysqli_fetch_array($sqlbk);
   $bk_date = isset($rowbk['bankout_date']) ? $rowbk['bankout_date'] : null;

  $queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$doc_date', '$referen', '$no_coa1', '$nama_coa1', '-', '-', '$no_bk', '$bk_date', '-', '-', '$curr', '$rate', '$nominal', '0', '$eqv_idr', '0', 'Draft', '$pesan', '$create_user', '$create_date', '', '', '', '')";

$executess2 = mysqli_query($conn2,$queryss2);
}else{
$queryss2 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$doc_date', '$referen', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$nominal', '0', '$eqv_idr', '0', 'Draft', '$pesan', '$create_user', '$create_date', '', '', '', '')";

$executess2 = mysqli_query($conn2,$queryss2);
}

if ($referen == 'AR Collection') {
   $queryss3 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$doc_date', '$referen', '$coa', '$nama_coa', '$cost', '$nama_cc', '-', '', '-', '-', '$curr', '$rate', '0', '$nominal', '0', '$eqv_idr', 'Draft', '$pesan', '$create_user', '$create_date', '', '', '', '')";

$executess3 = mysqli_query($conn2,$queryss3);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
echo 'Data Saved Successfully With No Incoming Bank '; echo $kode;
if ($no_bk == '') {
	
}else{
$sql2 = "update b_bankout_h set stat_bi='Y' where no_bankout = '$no_bk'";
$query2 = mysqli_query($conn2,$sql2);
}
	
}

mysqli_close($conn2);
?>