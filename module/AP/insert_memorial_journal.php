<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_mj = $_POST['no_mj'];
$mj_date =  date("Y-m-d",strtotime($_POST['mj_date']));
$tgl_hris =  date("Y-m",strtotime($_POST['tgl_hris']));
$bulan =  date("m",strtotime($_POST['mj_date']));
$tahun =  date("y",strtotime($_POST['mj_date']));
$id_cmj = $_POST['id_cmj'];
$no_coa = $_POST['no_coa'];
$no_costcenter = $_POST['no_costcenter'];
$no_reff =  $_POST['no_reff'];
$reff_date = date("Y-m-d",strtotime($_POST['reff_date']));
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curr = $_POST['curr'];
$rate = $_POST['rate'];
$debit = $_POST['debit'];
$credit =$_POST['credit'];
$debit_idr = $debit * $rate;
$credit_idr = $credit * $rate;
$keterangan = $_POST['keterangan'];
$status = "Post";
$create_user = $_POST['create_user'];
$create_date = date("Y-m-d H:i:s");





// echo $no_coa;
// echo "< -- >";
// echo $no_costcenter;
// echo "< -- >";
// echo $no_reff;
// echo "< -- >";
// echo $reff_date;
// echo "< -- >";
// echo $buyer;
// echo "< -- >";
// echo $no_ws;
// echo "< -- >";
// echo $curr;
// echo "< -- >";
// echo $rate;
// echo "< -- >";
// echo $debit;
// echo "< -- >";
// echo $credit;

// $sqlnkb = mysqli_query($conn2,"select max(no_mj) from tbl_memorial_journal where YEAR(mj_date) = YEAR('$mj_date') AND MONTH(mj_date) = MONTH('$mj_date')");
//  $rownkb = mysqli_fetch_array($sqlnkb);
//  $kodeBarang = $rownkb['max(no_mj)'];
//  $urutan = (int) substr($kodeBarang, 13, 5);
//  $urutan++;
//  $bln = $bulan;
//  $thn = $tahun;
//  $huruf = substr($no_mj,0,6);
//  $kode = $huruf ."/". $bln."".$thn ."/". sprintf("%05s", $urutan);


$sqlcmj = mysqli_query($conn1,"select nama_cmj from master_category_mj where id_cmj = '$id_cmj'");
$rowcmj = mysqli_fetch_array($sqlcmj);
$nama_cmj = $rowcmj['nama_cmj'];

$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$no_costcenter'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];

// $sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from c_report_pettycash where id = '$maxid'");
// $rowy = mysqli_fetch_array($sqly);
// $balance = $rowy['balance'];
// $balance2 = $balance + $total;

$query = "INSERT INTO tbl_memorial_journal (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date) 
VALUES 
	('$no_mj', '$mj_date', '$id_cmj', '$no_coa', '$no_costcenter', '$no_reff', '$reff_date', '$buyer', '$no_ws', '$curr', '$rate', '$debit', '$credit', '$debit_idr', '$credit_idr', '$keterangan', '$status', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);


$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$no_mj', '$mj_date', '$nama_cmj', '$no_coa', '$nama_coa', '$no_costcenter', '$nama_cc', '$no_reff', '$reff_date', '$buyer', '$no_ws', '$curr', '$rate', '$debit', '$credit', '$debit_idr', '$credit_idr', '$status', '$keterangan', '$create_user', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);


if ($id_cmj == 'CMJ001') {
	$sql_upt = "update jurnal set no_journal = '$no_mj' where periode_payroll = '$tgl_hris'";
	$execute_upt = mysqli_query($conn3,$sql_upt);
}




if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	// echo 'Data Saved Successfully With No '; echo $no_mj;
}

mysqli_close($conn2);
?>