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
// $sqlb = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate , tanggal  FROM masterrate where tanggal = '$tgl_pci' and v_codecurr = 'PAJAK'");
// $rowb = mysqli_fetch_array($sqlb);
// $rate = isset($rowb['rate']) ? $rowb['rate'] : 0;

// if ($curre == 'IDR') {
// $rates = 1;
// }else{
// if ($rate == '0') {
// $sqla = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
// $rowa = mysqli_fetch_array($sqla);
// $maxida = $rowa['id'];

// $sqlc = mysqli_query($conn2,"select IF(rate like ',',ROUND(rate,2),rate) as rate , tanggal  FROM masterrate where id = '$maxida' and v_codecurr = 'PAJAK'");
// $rowc = mysqli_fetch_array($sqlc);
// $rates = $rowc['rate'];

// }else{
// $rates = $rate;
// }
// }

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


if ($debit == '' and $credit == '') {
	
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


if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>