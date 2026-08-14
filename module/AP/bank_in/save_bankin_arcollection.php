<?php

include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

// Jalankan query dan lempar Exception kalau gagal, supaya rollback benar-benar
// terpicu saat ada query yang error (bukan cuma saat exception PHP biasa).
function q($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception(mysqli_error($conn));
    }
    return $result;
}

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
$pc        = $_POST['profit_center'] ?? '';
$pc_bank   = $_POST['profit_center_bank'];
$coa       = $_POST['coa'] ?? '';
$cost      = $_POST['cost'] ?? '-';
$desc      = trim($_POST['pesan'] ?? '');
$cash_flow = $_POST['cash_flow'] ?? '';

if ($akun === '') {
    throw new Exception('Account tidak boleh kosong.');
}

if ($coa === '') {
    throw new Exception('COA tidak boleh kosong.');
}

if ($pc === '') {
    throw new Exception('Profit Center tidak boleh kosong.');
}

if ($desc === '') {
    throw new Exception('Description tidak boleh kosong.');
}

if ($cash_flow === '') {
    throw new Exception('Cash Flow Category tidak boleh kosong.');
}
$cash_flow = (int) $cash_flow;

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

$sql = q($conn2,"
SELECT MAX(CAST(RIGHT(doc_num,5) AS UNSIGNED)) AS max_urut
FROM tbl_bankin_arcollection
WHERE doc_num LIKE '$prefix%'
FOR UPDATE
");

$row = mysqli_fetch_assoc($sql);
$maxHeader = (int) ($row['max_urut'] ?? 0);

// Nomor yang pernah dipakai tetap tercatat permanen di tbl_list_journal
// walau doc_num di header sudah berubah (akun diganti saat edit),
// jadi nomor lama tidak boleh dipakai ulang untuk dokumen baru.
$sqlJ = q($conn2,"
SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut
FROM tbl_list_journal
WHERE no_journal LIKE '$prefix%'
FOR UPDATE
");

$rowJ = mysqli_fetch_assoc($sqlJ);
$maxJournal = (int) ($rowJ['max_urut'] ?? 0);

$urutan = max($maxHeader, $maxJournal) + 1;


/* =========================
   DOC NUMBER BARU
========================= */

$doc_num = $prefix."/".sprintf("%05d",$urutan);

$sqlcoa1 = q($conn1,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
$rowcoa1 = mysqli_fetch_array($sqlcoa1);
$no_coa1 = $rowcoa1['no_coa'] ?? null;
$nama_coa1 = $rowcoa1['nama_coa'] ?? null;

if (!$no_coa1) {
    throw new Exception('COA Bank untuk akun "'.$akun.'" tidak ditemukan di mastercoa_v2.');
}

$sqlcoa = q($conn1,"select nama_coa from mastercoa_v2 where no_coa = '".mysqli_real_escape_string($conn1,$coa)."'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'] ?? null;

if (!$nama_coa) {
    throw new Exception('COA "'.$coa.'" tidak ditemukan.');
}

// COA tertentu wajib isi Cost Center (sama seperti aturan di sisi JS).
$sqlWajibCc = q($conn1,"select no_coa from mastercoa_v2 where no_coa = '".mysqli_real_escape_string($conn1,$coa)."'
    and (support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y')");
if (mysqli_num_rows($sqlWajibCc) > 0 && (!$cost || $cost === '-')) {
    throw new Exception('COA '.$coa.' wajib isi Cost Center.');
}

$nama_cc = null;
if ($cost && $cost !== '-') {
    $sqlcc = q($conn1,"select cc_name from b_master_cc where no_cc = '".mysqli_real_escape_string($conn1,$cost)."'");
    $rowcc = mysqli_fetch_array($sqlcc);
    $nama_cc = $rowcc['cc_name'] ?? null;
}
$nama_cc = $nama_cc ?: '-';


/* =========================
   INSERT BANK
========================= */

q($conn2,"
INSERT INTO tbl_bankin_arcollection
(
doc_num,date,ref_data,customer,akun,bank,curr,id_coa,id_cost_center, amount,rate,eqv_idr, outstanding,deskripsi, status,create_by,create_date, profit_center, id_cash_flow
)
VALUES
('$doc_num', '$doc_date', 'AR Collection', '$supp', '$akun', '$bank', '$curr', '$coa', '$cost', '$amount', '$rate', '$eqv', '$amount', '$desc', '$status', '$user', '$create_date', '$pc_bank', '$cash_flow')
");


q($conn2,"
INSERT INTO b_reportbank
(transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status,id_cash_flow)
VALUES
('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr','$amount', '0', '$amount', '$status', '$cash_flow')
");


/* ========================
   INSERT JOURNAL (bulk)
========================= */

$journalValues = [];

$journalValues[] = "('$doc_num', '$doc_date', 'AR Collection', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')";
$journalValues[] = "('$doc_num', '$doc_date', 'AR Collection', '$coa', '$nama_coa', '$cost', '$nama_cc', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc')";

if ($pc != $pc_bank) {
    $journalValues[] = "('$doc_num', '$doc_date', 'AR Collection', '2.21.01', 'UTANG ANTAR DIVISI', '-', '-', '-', '', '-', '-', '$curr', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')";
    $journalValues[] = "('$doc_num', '$doc_date', 'AR Collection', '1.91.01', 'PIUTANG ANTAR DIVISI', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc')";
}

q($conn2, "
INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES " . implode(',', $journalValues)
);


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
