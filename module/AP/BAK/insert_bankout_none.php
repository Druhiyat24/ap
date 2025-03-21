<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankout = $_POST['no_bankout'];
$tgl_bankout = date("Y-m-d",strtotime($_POST['tgl_bankout']));
$reff_doc = $_POST['reff_doc'];
$no_coa = $_POST['no_coa'];
$no_costcntr = $_POST['no_costcntr'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curr = $_POST['curr'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$deskripsi = $_POST['deskripsi'];
$rate = $_POST['rate'];


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

$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$no_costcntr'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];

$sqlx = mysqli_query($conn2,"select max(id) as id FROM b_bankout_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_bankout),bankout_date,create_by,create_date from b_bankout_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_bankout)'];
 $date = $rownkb['bankout_date'];
 $create_by = $rownkb['create_by'];
 $create_date = $rownkb['create_date'];

 if ($curr == 'IDR') {
   $rates = '1';
   $t_debit = $debit;
   $t_credit = $credit;
}else{
//    $sqlyzz = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$date' and v_codecurr = 'PAJAK'");
// $rowyzz = mysqli_fetch_array($sqlyzz);
// $rats = isset($rowyzz['rate']) ? $rowyzz['rate'] : 0;

// if($rats == '0'){
//    $rates = $rate;
// }else{
// $sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
// $rowxss = mysqli_fetch_array($sqlxss);
// $maxidss = $rowxss['id'];

// $sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and v_codecurr = 'PAJAK'");
// $rowyss = mysqli_fetch_array($sqlyss);
$rates = $rate;
// }

$t_debit = $debit * $rates;
$t_credit = $credit * $rates;
}

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO b_bankout_none (no_bankout,tgl_bankout,reff_doc,no_coa,no_costcntr,buyer,no_ws,curr,debit,credit,deskripsi) 
VALUES 
	('$kode', '$tgl_bankout', '$reff_doc', '$no_coa', '$no_costcntr', '$buyer', '$no_ws', '$curr', '$debit', '$credit', '$deskripsi')";

$execute = mysqli_query($conn2,$query);

$queryss3 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$date', '$reff_doc', '$no_coa', '$nama_coa', '$no_costcntr', '$nama_cc', '-', '', '$buyer', '$no_ws', '$curr', '$rates', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '')";

$executess3 = mysqli_query($conn2,$queryss3);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>