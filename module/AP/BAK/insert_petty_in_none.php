<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pci = $_POST['no_pci'];
$tgl_pci =  date("Y-m-d",strtotime($_POST['tgl_pci']));
$nomor_coa = $_POST['nomor_coa'];
$cost_ctr = $_POST['cost_ctr'];
$buyer = $_POST['buyer'];
$no_ws = $_POST['no_ws'];
$curre = $_POST['curre'];
$debit = $_POST['debit'];
$credit = $_POST['credit'];
$keterangan = $_POST['keterangan'];
$oth_doc = $_POST['oth_doc'];
$referen = $_POST['referen'];




$sqlx = mysqli_query($conn2,"select max(id) as id FROM c_petty_cashin_h ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(no_pci),reff,reff_doc,create_date,create_by from c_petty_cashin_h where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(no_pci)'];
 $type_ci = $rownkb['reff'];
 $dokumen = $rownkb['reff_doc'];
 $create_date = $rownkb['create_date'];
 $create_by = $rownkb['create_by'];


$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$nomor_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$cost_ctr'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];

$sqlpco = mysqli_query($conn1,"select no_pco, tgl_pco from c_petty_cashout_h where no_pco = '$oth_doc' GROUP BY no_pco");
$rowpco = mysqli_fetch_array($sqlpco);
$txtno_pco = $rowpco['no_pco'];
$txttgl_pco = $rowpco['tgl_pco'];


if ($debit == '' and $credit == '') {
	
}else{
   if ($referen == 'Settlement') {
      $query = "INSERT INTO c_petty_cashin_none (no_pci,tgl_pci,no_coa,no_costcenter,buyer,no_ws,curr,debit,credit,deskripsi) 
VALUES 
   ('$kode', '$tgl_pci', '$nomor_coa', '$cost_ctr', '$buyer', '$no_ws', '$curre', '$debit', '$credit', '$keterangan')";

$execute = mysqli_query($conn2,$query);

$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_pci', '$type_ci', '$nomor_coa', '$nama_coa', '$cost_ctr', '$nama_cc', '$txtno_pco', '$txttgl_pco', '$buyer', '$no_ws', '$curre', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$keterangan', '$create_by', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);
   }else{
      
$query = "INSERT INTO c_petty_cashin_none (no_pci,tgl_pci,no_coa,no_costcenter,buyer,no_ws,curr,debit,credit,deskripsi) 
VALUES 
	('$kode', '$tgl_pci', '$nomor_coa', '$cost_ctr', '$buyer', '$no_ws', '$curre', '$debit', '$credit', '$keterangan')";

$execute = mysqli_query($conn2,$query);

$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_pci', '$type_ci', '$nomor_coa', '$nama_coa', '$cost_ctr', '$nama_cc', '$dokumen', '', '$buyer', '$no_ws', '$curre', '1', '$debit', '$credit', '$debit', '$credit', 'Draft', '$keterangan', '$create_by', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);
   }
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>