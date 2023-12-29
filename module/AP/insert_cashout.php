<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_co = $_POST['no_co'];
$tgl_co =  date("Y-m-d",strtotime($_POST['tgl_co']));
$type_co = $_POST['type_co'];
$dokumen = $_POST['dokumen'];
$no_coa = $_POST['no_coa'];
$no_costcenter = $_POST['no_costcenter'];
$buyer = $_POST['buyer'];
$ws = $_POST['ws'];
$req_by = $_POST['req_by'];
$curr = "IDR";
$amount = $_POST['amount'];
$deskrip = $_POST['deskrip'];
$pesan = $_POST['pesan'];
$status = "Draft";
$create_date = date("Y-m-d H:i:s");
$create_user = $_POST['create_user'];
$stat_pci = "N";





// echo $doc_number;
// echo "< -- >";
// echo $no_coa;
// echo "< -- >";
// echo $no_ref;
// echo "< -- >";
// echo $ref_date;

$sqlx = mysqli_query($conn2,"select max(id) as id FROM tbl_log_cash where doc_num like '%RCO%' ");
$rowx = mysqli_fetch_array($sqlx);
$maxid = $rowx['id'];

$sqlnkb = mysqli_query($conn2,"select max(doc_num) from tbl_log_cash where id = '$maxid'");
 $rownkb = mysqli_fetch_array($sqlnkb);
 $kode = $rownkb['max(doc_num)'];

 $sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$no_costcenter'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = $rowcc['cc_name'];


if($amount != ''){
$query = "INSERT INTO c_cash_out (no_co, tgl_co, type_co, dokumen, no_coa, no_costcenter, buyer, ws, req_by, curr, amount, deskrip, status, deskrip_global, create_date, create_by,stat_pci) 
VALUES 
	('$kode', '$tgl_co', '$type_co', '$dokumen', '$no_coa','$no_costcenter', '$buyer', '$ws', '$req_by', '$curr','$amount', '$deskrip', '$status', '$pesan', '$create_date', '$create_user', '$stat_pci')";

$execute = mysqli_query($conn2,$query);

$queryss = "INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date) 
VALUES 
   ('$kode', '$tgl_co', '$type_co', '$no_coa', '$nama_coa', '$no_costcenter', '$nama_cc', '$dokumen', '', '$buyer', '$ws', '$curr', '1', '$amount', '0', '$amount', '0', '$status', '$pesan', '$create_user', '$create_date', '', '', '', '')";

$executess = mysqli_query($conn2,$queryss);
}else{
	
}



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>