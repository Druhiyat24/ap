<?php
include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try{

$ref_num   = $_POST['ref_num3'];
$doc_date  = date('Y-m-d',strtotime($_POST['tgl_active3']));
$pc_kas    = $_POST['profit_center_kas3'];
$akun      = $_POST['account3'];
$curr      = $_POST['currency3'];
$kode_kas  = $_POST['kode_kas3'];
$desc      = $_POST['pesan3'] ?? '-';
$nama_supp = $_POST['nama_supp3'] ?? '';

$amount = str_replace(',','',$_POST['amount_kas3']);

$user = $_SESSION['username'] ?? 'system';

$bulan = date('m',strtotime($doc_date));

$tahun = date('Y',strtotime($doc_date));
$status = "Draft";
$create_date = date("Y-m-d H:i:s");


/* =========================
   PREFIX DOC NUMBER
========================= */

$prefix = "KKK/".$kode_kas."/".$tahun."/".$bulan;

/* =========================
   AMBIL MAX URUTAN
========================= */

$sql = mysqli_query($conn2,"
SELECT MAX(CAST(RIGHT(no_pco,5) AS UNSIGNED)) AS max_urut
FROM c_petty_cashout_h
WHERE no_pco LIKE '$prefix%'
FOR UPDATE
");

$row = mysqli_fetch_assoc($sql);

$urutan = ($row['max_urut'] ?? 0) + 1;


/* =========================
   DOC NUMBER BARU
========================= */

$doc_num = $prefix."/".sprintf("%05d",$urutan);

$sqlcoa = mysqli_query($conn2,"select nama_coa from mastercoa_v2 where no_coa = '$akun'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

/* =========================
INSERT HEADER
========================= */


mysqli_query($conn2,"
INSERT INTO c_petty_cashout_h (no_pco,tgl_pco,reff,nama_supp,coa_akun,curr,amount,deskripsi,status, create_by,create_date, reff_doc)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$nama_supp', '$akun', '$curr', '$amount','$desc', '$status', '$user', '$create_date', '')
");


mysqli_query($conn2,"
INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance, status) 
VALUES
('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr', '0', '$amount', '$amount', '$status')
");



mysqli_query($conn2,"
INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$amount', '0', '$amount', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_kas')
");


/* =========================
INSERT DETAIL TABLE
========================= */

$coa    = $_POST['nomor_coa3'];
$pc     = $_POST['prof_ctr3'];
$cc     = $_POST['cost_ctr3'];
$buyer  = $_POST['buyer3'];
$no_ws  = $_POST['no_ws3'];
$curr   = $_POST['currenc3'];
$ket    = $_POST['keterangan3'];
$debit  = $_POST['txt_amount3'];
$credit = $_POST['txt_credit3'];

for($i=0;$i<count($coa);$i++){

	$no_coa = $coa[$i];
    $pc_i   = $pc[$i];
    $cc_i   = $cc[$i];
    $buyer_i= $buyer[$i];
    $ws_i   = $no_ws[$i];
    $curr_i = $curr[$i];
    $ket_i  = $ket[$i];



if($no_coa=='-' || $no_coa=='') continue;

$d_debit  = str_replace(',','',$debit[$i]);
$d_credit = str_replace(',','',$credit[$i]);

if ($curr_i == 'IDR') {
   	$rate_det = 1;
   	$d_debit_idr = $d_debit;
   	$d_credit_idr = $d_credit;
}else{
   	$rate_det = 1;
   	$d_debit_idr = $d_debit * $rate;
   	$d_credit_idr = $d_credit * $rate;
}

$sqlcoa = mysqli_query($conn2,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

$sqlcc = mysqli_query($conn2,"select cc_name from b_master_cc where no_cc = '$cc_i'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;


mysqli_query($conn2,"
INSERT INTO c_petty_cashout_none (no_pco,tgl_pco,reff_doc,no_coa, profit_center, no_costcntr,buyer,no_ws,curr,debit,credit,deskripsi)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$no_coa', '$pc_i', '$cc_i', '$buyer_i', '$ws_i', '$curr_i', '$d_debit', '$d_credit', '$ket_i')
");

mysqli_query($conn2,"
INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$no_coa', '$nama_coa', '$cc_i', '$nama_cc', '-', '', '$buyer_i', '$ws_i', '$curr_i', '1', '$d_debit', '$d_credit', '$d_debit', '$d_credit', 'Draft', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')
");


}


mysqli_commit($conn2);

echo json_encode([
"status"=>"success",
"message"=>"Data berhasil disimpan No : ".$doc_num
]);

}catch(Exception $e){

mysqli_rollback($conn2);

echo json_encode([
"status"=>"error",
"message"=>$e->getMessage()
]);

}
