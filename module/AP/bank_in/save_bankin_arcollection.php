<?php

include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

mysqli_begin_transaction($conn2);

try{

/* =========================
   POST DATA
========================= */

$ref_num   = $_POST['ref_num'];
$doc_date  = date('Y-m-d',strtotime($_POST['tgl_active']));
$supp      = $_POST['nama_supp'];
$akun      = $_POST['account'];
$bank      = $_POST['bank'];
$kode_bank = $_POST['kode_bank'];
$curr      = $_POST['currency'];
$pc        = $_POST['profit_center'];
$pc_bank   = $_POST['profit_center_bank'];
$coa       = $_POST['coa'];
$cost      = $_POST['cost'] ?? '-';
$desc      = $_POST['pesan'] ?? '-';

$amount = str_replace(',','',$_POST['amount_bank']);
$rate   = str_replace(',','',$_POST['rate_bank']);
$eqv    = str_replace(',','',$_POST['eqv_idr_bank']);

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

$sqlcoa1 = mysqli_query($conn1,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
$rowcoa1 = mysqli_fetch_array($sqlcoa1);
$no_coa1 = $rowcoa1['no_coa'];
$nama_coa1 = $rowcoa1['nama_coa'];

$sqlcoa = mysqli_query($conn1,"select nama_coa from mastercoa_v2 where no_coa = '$coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

$sqlcc = mysqli_query($conn1,"select cc_name from b_master_cc where no_cc = '$cost'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;


/* =========================
   INSERT BANK
========================= */

mysqli_query($conn2,"
INSERT INTO tbl_bankin_arcollection
(
doc_num,date,ref_data,customer,akun,bank,curr,id_coa,id_cost_center, amount,rate,eqv_idr, outstanding,deskripsi, status,create_by,create_date, profit_center
)
VALUES
('$doc_num', '$doc_date', 'AR Collection', '$supp', '$akun', '$bank', '$curr', '$coa', '$cost', '$amount', '$rate', '$eqv', '$amount', '$desc', '$status', '$user', '$create_date', '$pc_bank')
");


mysqli_query($conn2,"
INSERT INTO b_reportbank
(transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status)
VALUES
('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr','$amount', '0', '$amount', '$status')
");


/* ========================
   INSERT JOURNAL
========================= */

mysqli_query($conn2,"
INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES
('$doc_num', '$doc_date', 'AR Collection', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')
");

mysqli_query($conn2,"
INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES
('$doc_num', '$doc_date', 'AR Collection', '$coa', '$nama_coa', '$cost', '$nama_cc', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc')
");

if ($pc != $pc_bank) {
    mysqli_query($conn2,"
  INSERT INTO tbl_list_journal
  (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
  VALUES
  ('$doc_num', '$doc_date', 'AR Collection', '2.21.01', 'UTANG ANTAR DIVISI', '-', '-', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')
  ");

  mysqli_query($conn2,"
  INSERT INTO tbl_list_journal
  (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
  VALUES
  ('$doc_num', '$doc_date', 'AR Collection', '1.91.01', 'PIUTANG ANTAR DIVISI', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc')
  ");
}




/* =========================
   COMMIT
========================= */

mysqli_commit($conn2);

echo json_encode([
    "status"=>"ok",
    "doc_num"=>$doc_num
]);

}catch(Exception $e){

mysqli_rollback($conn2);

echo json_encode([
    "status"=>"fail",
    "msg"=>$e->getMessage()
]);

}

?>

