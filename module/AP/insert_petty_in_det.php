<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pci = $_POST['no_pci'];
$tgl_pci =  date("Y-m-d",strtotime($_POST['tgl_pci']));
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

 $sqlco = mysqli_query($conn1,"select tgl_co, no_coa, no_costcenter,buyer, ws from c_cash_out where no_co = '$dokumen'");
$rowco = mysqli_fetch_array($sqlco);
$tgl_co = $rowco['tgl_co'];
$no_coa = $rowco['no_coa'];
$no_costcenter = $rowco['no_costcenter'];
$buyer = $rowco['buyer'];
$ws = $rowco['ws'];

 $sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$no_costcenter'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];

if ($t_debit == '' and $t_credit == '') {
	
}else{
$query = "INSERT INTO c_petty_cashin_det (no_pci,tgl_pci,no_coa,reff_doc,reff_date,deskripsi,debit,credit) 
VALUES 
	('$kode', '$tgl_pci', '$id_coa', '$no_reff', '$reff_date', '$deskripsi', '$t_debit', '$t_credit')";

$execute = mysqli_query($conn2,$query);


$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_pci', '$type_ci', '$no_coa', '$nama_coa', '$no_costcenter', '$nama_cc', '$dokumen', '', '$buyer', '$ws', 'IDR', '1', '$t_debit', '$t_credit', '$t_debit', '$t_credit', 'Draft', '$deskripsi', '$create_by', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);
}


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>