<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bankin = $_POST['no_bankin'];
$id_coa =  $_POST['id_coa'];
$no_reff = $_POST['no_reff'];
$reff_date =  date("Y-m-d",strtotime($_POST['reff_date']));
$deskripsi = $_POST['deskripsi'];
$t_debit = $_POST['t_debit'];
$t_credit = $_POST['t_credit'];


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

$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$id_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlx = mysqli_query($conn2,"select max(id) as id FROM tbl_bankin_arcollection ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(doc_num),date,create_by,create_date,curr from tbl_bankin_arcollection where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(doc_num)'];
 $date = $rownkb['date'];
 $create_by = $rownkb['create_by'];
 $create_date = $rownkb['create_date'];
 $curr = $rownkb['curr'];


 if ($curr == 'IDR') {
   $rates = '1';
   $debit = $t_debit;
   $credit = $t_credit;
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

$debit = $t_debit * $rates;
$credit = $t_credit * $rates;
}

if ($t_debit == '' and $t_credit == '') {
	
}else{
$query = "INSERT INTO b_bankin_none (no_bankin,id_coa,no_reff,reff_date,deskripsi,t_debit,t_credit) 
VALUES 
	('$kode', '$id_coa', '$no_reff', '$reff_date', '$deskripsi', '$t_debit', '$t_credit')";

$execute = mysqli_query($conn2,$query);

$queryss3 = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$date', 'Bank Keluar', '$id_coa', '$nama_coa', '-', '-', '$no_reff', '$reff_date', '-', '-', '$curr', '$rates', '$t_debit', '$t_credit', '$debit', '$credit', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '')";

$executess3 = mysqli_query($conn2,$queryss3);

}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>