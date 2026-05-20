<?php
include '../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try{

$ref_num   = $_POST['ref_num3'];
$doc_date  = date('Y-m-d',strtotime($_POST['tgl_active3']));
$supp      = $_POST['nama_supp3'];
$akun      = $_POST['account3'];
$bank      = $_POST['bank3'];
$kode_bank = $_POST['kode_bank3'];
$curr      = $_POST['currency3'];
$pc_bank   = $_POST['profit_center_bank3'];
$desc      = $_POST['pesan3'] ?? '-';

$amount = str_replace(',','',$_POST['amount_bank3']);
$rate   = str_replace(',','',$_POST['rate_bank3']);
$eqv    = str_replace(',','',$_POST['eqv_idr_bank3']);

$user = $_SESSION['username'] ?? 'system';

$bulan = date('m',strtotime($doc_date));
$tahun = date('y',strtotime($doc_date));
$status = "Draft";
$create_date = date("Y-m-d H:i:s");


/* =========================
   PREFIX DOC NUMBER
========================= */

$prefix = "BM/".$kode_bank."/".$pc_bank."/".$bulan.$tahun;


/* =========================
   AMBIL MAX URUTAN
========================= */

$sql = mysqli_query($conn2,"
SELECT MAX(CAST(RIGHT(doc_num,5) AS UNSIGNED)) AS max_urut
FROM tbl_bankin_arcollection
WHERE doc_num LIKE '$prefix%'
FOR UPDATE
");

$row = mysqli_fetch_assoc($sql);

$urutan = ($row['max_urut'] ?? 0) + 1;


/* =========================
   DOC NUMBER BARU
========================= */

$doc_num = $prefix."/".sprintf("%05d",$urutan);

$sqlcoa1 = mysqli_query($conn2,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
$rowcoa1 = mysqli_fetch_array($sqlcoa1);
$no_coa1 = $rowcoa1['no_coa'];
$nama_coa1 = $rowcoa1['nama_coa'];

/* =========================
INSERT HEADER
========================= */

mysqli_query($conn2,"
INSERT INTO tbl_bankin_arcollection
(
doc_num,date,ref_data,customer,akun,bank,curr,id_coa,id_cost_center, amount,rate,eqv_idr, outstanding,deskripsi, status,create_by,create_date, profit_center
)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$supp', '$akun', '$bank', '$curr', '-', '-', '$amount', '$rate', '$eqv', '$amount', '$desc', '$status', '$user', '$create_date', '$pc_bank')
");


mysqli_query($conn2,"
INSERT INTO b_reportbank
(transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status)
VALUES
('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr','$amount', '0', '$amount', '$status')
");


mysqli_query($conn2,"
INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')
");


/* =========================
INSERT DETAIL TABLE
========================= */

$coa    = $_POST['nomor_coa'];
$pc     = $_POST['prof_ctr'];
$cc     = $_POST['cost_ctr'];
$buyer  = $_POST['buyer'];
$no_ws  = $_POST['no_ws'];
$curr   = $_POST['currenc'];
$ket    = $_POST['keterangan'];
$debit  = $_POST['txt_amount'];
$credit = $_POST['txt_credit'];

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
   	$rate_det = $rate;
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
INSERT INTO tbl_bankin
(no_doc,id_coa,id_cost_center,buyer,no_ws,curr,t_debit,t_credit,keterangan, profit_center)
VALUES
('$doc_num', '$no_coa', '$cc_i', '$buyer_i', '$ws_i', '$curr_i', '$d_debit', '$d_credit', '$ket_i', '$pc_i')
");

mysqli_query($conn2,"
INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$no_coa', '$nama_coa', '$cc_i', '$nama_cc', '-', '', '$buyer_i', '$ws_i', '$curr_i', '$rate_det', '$d_debit', '$d_credit', '$d_debit_idr', '$d_credit_idr', 'Draft', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')
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
