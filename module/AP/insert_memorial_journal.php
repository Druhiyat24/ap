<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$cek_sb1 = $_POST['cek_sb1'];
$no_mj_sb1 = $_POST['no_mj_sb1'];
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
$prof_ctr = $_POST['prof_ctr'];
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

if($no_coa != '' || $no_coa != null){


$sqlcmj = mysqli_query($conn1,"select nama_cmj from master_category_mj where id_cmj = '$id_cmj'");
$rowcmj = mysqli_fetch_array($sqlcmj);
$nama_cmj = $rowcmj['nama_cmj'];

$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$no_costcenter'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];

// $sqlz = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate , tanggal  FROM masterrate where tanggal = '$mj_date' and v_codecurr = 'PAJAK'");
// $rowz = mysqli_fetch_array($sqlz);
// $ratez = $rowz['rate'];
//    if ($ratez != '') {
//       $rates = $ratez;
//    }else{

//     	$sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
//       $rowx = mysqli_fetch_array($sqlx);
//       $maxid = $rowx['id'];

//       $sqly = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'PAJAK'");
//       $rowy = mysqli_fetch_array($sqly);
//       $rates = $rowy['rate']; 
//    }

// $sqly = mysqli_query($conn1,"select if(sum(balance) is null,'0',sum(balance)) as balance from c_report_pettycash where id = '$maxid'");
// $rowy = mysqli_fetch_array($sqly);
// $balance = $rowy['balance'];
// $balance2 = $balance + $total;

$query = "INSERT INTO tbl_memorial_journal (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, profit_center) 
VALUES 
	('$no_mj', '$mj_date', '$id_cmj', '$no_coa', '$no_costcenter', '$no_reff', '$reff_date', '$buyer', '$no_ws', '$curr', '$rate', '$debit', '$credit', '$debit_idr', '$credit_idr', '$keterangan', '$status', '$create_user', '$create_date', '$prof_ctr')";

$execute = mysqli_query($conn2,$query);


$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
VALUES 
   ('$no_mj', '$mj_date', '$nama_cmj', '$no_coa', '$nama_coa', '$no_costcenter', '$nama_cc', '$no_reff', '$reff_date', '$buyer', '$no_ws', '$curr', '$rate', '$debit', '$credit', '$debit_idr', '$credit_idr', '$status', '$keterangan', '$create_user', '$create_date', '', '', '', '', '$prof_ctr')";

$executess = mysqli_query($conn2,$queryss);

if ($cek_sb1 == '1' ) {

$query = "INSERT INTO sb_memorial_journal (no_mj, mj_date, id_cmj, no_coa, no_costcenter, no_reff, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, keterangan, status, create_by, create_date, asal_data, profit_center) 
VALUES 
	('$no_mj_sb1', '$mj_date', '$id_cmj', '$no_coa', '$no_costcenter', '$no_reff', '$reff_date', '$buyer', '$no_ws', '$curr', '$rate', '$debit', '$credit', '$debit_idr', '$credit_idr', '$keterangan', '$status', '$create_user', '$create_date', 'Input SB2', '$prof_ctr')";

$execute = mysqli_query($conn2,$query);


$queryss = "INSERT INTO sb_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center) 
VALUES 
   ('$no_mj_sb1', '$mj_date', '$nama_cmj', '$no_coa', '$nama_coa', '$no_costcenter', '$nama_cc', '$no_reff', '$reff_date', '$buyer', '$no_ws', '$curr', '$rate', '$debit', '$credit', '$debit_idr', '$credit_idr', '$status', '$keterangan', '$create_user', '$create_date', '', '', '', '', '$prof_ctr')";

$executess = mysqli_query($conn2,$queryss);

$sqlx = mysqli_query($conn2,"select no_mj FROM status_memorial_journal where no_mj = '$no_mj'");
$rowx = mysqli_fetch_array($sqlx);
$no_mj_cek = isset($rowx['no_mj']) ? $rowx['no_mj'] : '-';

if ($no_mj_cek == '-') {
$query_sb = "INSERT INTO status_memorial_journal (no_mj, mj_date, no_mj_sb, status, create_by, create_date) 
VALUES 
	('$no_mj', '$mj_date', '$no_mj_sb1', 'Post', '$create_user', '$create_date')";

$execute_sb = mysqli_query($conn2,$query_sb);
}

}else{
	
}


if ($id_cmj == 'CMJ001') {
	$sql_upt = "update jurnal set no_journal = '$no_mj' where periode_payroll = '$tgl_hris'";
	$execute_upt = mysqli_query($conn3,$sql_upt);
}




if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	// echo 'Data Saved Successfully With No '; echo $no_mj;
}
}

mysqli_close($conn2);
?>