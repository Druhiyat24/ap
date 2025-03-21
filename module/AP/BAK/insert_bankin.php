<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$referen =  $_POST['referen'];
$nomor_coa = $_POST['nomor_coa'];
$cost_ctr = $_POST['cost_ctr'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curre = $_POST['curre'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$keterangan = $_POST['keterangan'];



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


$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$nomor_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$cost_ctr'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];


$sqlx = mysqli_query($conn2,"select max(id) as id FROM tbl_bankin_arcollection ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(doc_num),date,create_by,create_date from tbl_bankin_arcollection where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(doc_num)'];
 $date = $rownkb['date'];
 $create_by = $rownkb['create_by'];
 $create_date = $rownkb['create_date'];

 if ($curre == 'IDR') {
   $rates = '1';
   $t_debit = $debit;
   $t_credit = $credit;
}else{
   $sqlyzz = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where tanggal = '$date' and v_codecurr = 'PAJAK'");
$rowyzz = mysqli_fetch_array($sqlyzz);
$rats = isset($rowyzz['rate']) ? $rowyzz['rate'] : 0;

if($rats == '0'){
   $rates = $rats;
}else{
$sqlxss = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
$rowxss = mysqli_fetch_array($sqlxss);
$maxidss = $rowxss['id'];

$sqlyss = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss' and v_codecurr = 'PAJAK'");
$rowyss = mysqli_fetch_array($sqlyss);
$rates = $rowyss['rate'];
}

$t_debit = $debit * $rates;
$t_credit = $credit * $rates;
}

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO tbl_bankin (no_doc,id_coa,id_cost_center,buyer,no_ws,curr,t_debit,t_credit,keterangan) 
VALUES 
	('$kode', '$nomor_coa', '$cost_ctr', '$buyer', '$no_ws', '$curre', '$debit', '$credit', '$keterangan')";

$execute = mysqli_query($conn2,$query);

$queryss3 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$date', '$referen', '$nomor_coa', '$nama_coa', '$cost_ctr', '$nama_cc', '-', '', '$buyer', '$no_ws', '$curre', '$rates', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$keterangan', '$create_by', '$create_date', '', '', '', '')";

$executess3 = mysqli_query($conn2,$queryss3);

}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>