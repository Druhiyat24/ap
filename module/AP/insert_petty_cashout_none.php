<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pco = $_POST['no_pco'];
$tgl_pco = date("Y-m-d",strtotime($_POST['tgl_pco']));
$reff_doc = $_POST['reff_doc'];
$no_coa = $_POST['no_coa'];
$no_costcntr = $_POST['no_costcntr'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curr = $_POST['curr'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$deskripsi = $_POST['deskripsi'];


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

$sqlx = mysqli_query($conn2,"select max(id) as id FROM c_petty_cashout_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_pco),reff,create_date,create_by from c_petty_cashout_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_pco)'];
 $type_co = $rownkb['reff'];
 $create_date = $rownkb['create_date'];
 $create_by = $rownkb['create_by'];

 $sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$no_costcntr'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];

if ($debit == '' and $credit == '') {
	
}else{
$query = "INSERT INTO c_petty_cashout_none (no_pco,tgl_pco,reff_doc,no_coa,no_costcntr,buyer,no_ws,curr,debit,credit,deskripsi) 
VALUES 
	('$kode', '$tgl_pco', '$reff_doc', '$no_coa', '$no_costcntr', '$buyer', '$no_ws', '$curr', '$debit', '$credit', '$deskripsi')";

$execute = mysqli_query($conn2,$query);

$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_pco', '$type_co', '$no_coa', '$nama_coa', '$no_costcntr', '$nama_cc', '-', '', '$buyer', '$no_ws', '$curr', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);

}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>