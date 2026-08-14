<?php
include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

function q($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception(mysqli_error($conn));
    }
    return $result;
}

mysqli_begin_transaction($conn2);

try{

$ref_num   = $_POST['ref_num3'] ?? '';
$doc_date  = date('Y-m-d',strtotime($_POST['tgl_active3']));
$supp      = $_POST['nama_supp3'] ?? '';
$akun      = $_POST['account3'] ?? '';
$bank      = $_POST['bank3'] ?? '';
$kode_bank = $_POST['kode_bank3'] ?? '';
$curr      = $_POST['currency3'] ?? '';
$pc_bank   = $_POST['profit_center_bank3'] ?? '';
$desc      = trim($_POST['pesan3'] ?? '');
$cash_flow = $_POST['cash_flow3'] ?? '';

if ($supp === '') {
    throw new Exception('Supplier tidak boleh kosong.');
}

if ($akun === '') {
    throw new Exception('Account tidak boleh kosong.');
}

if ($desc === '') {
    throw new Exception('Description tidak boleh kosong.');
}

if ($cash_flow === '') {
    throw new Exception('Cash Flow Category tidak boleh kosong.');
}
$cash_flow = (int) $cash_flow;

$amount = str_replace(',','',$_POST['amount_bank3']);
$rate   = str_replace(',','',$_POST['rate_bank3']);
$eqv    = str_replace(',','',$_POST['eqv_idr_bank3']);

if ((float) $amount <= 0) {
    throw new Exception('Amount tidak boleh kosong.');
}

$user = $_SESSION['username'] ?? 'system';

$bulan = date('m',strtotime($doc_date));
$tahun = date('y',strtotime($doc_date));
$status = "Draft";
$create_date = date("Y-m-d H:i:s");

$refNumEsc = mysqli_real_escape_string($conn2, $ref_num);
$suppEsc = mysqli_real_escape_string($conn2, $supp);
$akunEsc = mysqli_real_escape_string($conn2, $akun);
$bankEsc = mysqli_real_escape_string($conn2, $bank);
$currEsc = mysqli_real_escape_string($conn2, $curr);
$pcBankEsc = mysqli_real_escape_string($conn2, $pc_bank);
$descEsc = mysqli_real_escape_string($conn2, $desc);
$userEsc = mysqli_real_escape_string($conn2, $user);

/* =========================
   PREFIX DOC NUMBER
========================= */

$prefix = "BK/".$kode_bank."/".$pc_bank."/".$bulan.$tahun;


/* =========================
   AMBIL MAX URUTAN
========================= */

$prefixEsc = mysqli_real_escape_string($conn2, $prefix);

$sql = q($conn2,"
SELECT MAX(CAST(RIGHT(no_bankout,5) AS UNSIGNED)) AS max_urut
FROM b_bankout_h
WHERE no_bankout LIKE '$prefixEsc%'
FOR UPDATE
");

$row = mysqli_fetch_assoc($sql);
$maxHeader = (int) ($row['max_urut'] ?? 0);

// Nomor yang pernah dipakai tetap tercatat permanen di tbl_list_journal
// walau no_bankout di header sudah berubah (akun diganti saat edit),
// jadi nomor lama tidak boleh dipakai ulang untuk dokumen baru.
$sqlJ = q($conn2,"
SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut
FROM tbl_list_journal
WHERE no_journal LIKE '$prefixEsc%'
FOR UPDATE
");

$rowJ = mysqli_fetch_assoc($sqlJ);
$maxJournal = (int) ($rowJ['max_urut'] ?? 0);

$urutan = max($maxHeader, $maxJournal) + 1;


/* =========================
   DOC NUMBER BARU
========================= */

$doc_num = $prefix."/".sprintf("%05d",$urutan);

$sqlcoa1 = q($conn2,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akunEsc%' and ind_categori2 = 'ASET'");
$rowcoa1 = mysqli_fetch_array($sqlcoa1);
$no_coa1 = $rowcoa1['no_coa'] ?? null;
$nama_coa1 = $rowcoa1['nama_coa'] ?? null;

if (!$no_coa1) {
    throw new Exception('COA Bank untuk akun "'.$akun.'" tidak ditemukan di mastercoa_v2.');
}

/* =========================
INSERT HEADER
========================= */

q($conn2,"
INSERT INTO b_bankout_h
(
no_bankout, bankout_date, reff_doc, nama_supp, akun, bank, curr, amount, outstanding, rate, eqv_idr, deskripsi, status, create_by, create_date, stat_bi, id_cash_flow
)
VALUES
('$doc_num', '$doc_date', '$refNumEsc', '$suppEsc', '$akunEsc', '$bankEsc', '$currEsc', '$amount', '$amount', '$rate', '$eqv', '$descEsc', '$status', '$userEsc', '$create_date', 'N', '$cash_flow')
");


q($conn2,"
INSERT INTO b_reportbank
(transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status,id_cash_flow)
VALUES
('$doc_date', '$doc_num', '$descEsc', '$akunEsc', '', '', '$currEsc', '0', '$amount', '$amount', '$status', '$cash_flow')
");

/* =========================
   COA WAJIB CC (sama seperti get_coa_wajib_cc.php)
========================= */

$coaWajibCC = [];
$sqlWajibCcAll = q($conn1, "select no_coa from mastercoa_v2 where support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y'");
while ($rowWajib = mysqli_fetch_assoc($sqlWajibCcAll)) {
    $coaWajibCC[] = $rowWajib['no_coa'];
}

$journalValues = [];
$bankoutNoneValues = [];

$journalValues[] = "('$doc_num', '$doc_date', '$refNumEsc', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$currEsc', '$rate', '0', '$amount', '0', '$eqv', 'Draft', '$descEsc', '$userEsc', '$create_date', '', '', '', '', '$pcBankEsc')";

/* =========================
INSERT DETAIL TABLE (bulk)
========================= */

$coa    = $_POST['nomor_coa'] ?? [];
$pc     = $_POST['prof_ctr'] ?? [];
$cc     = $_POST['cost_ctr'] ?? [];
$buyer  = $_POST['buyer'] ?? [];
$no_ws  = $_POST['no_ws'] ?? [];
$currDet= $_POST['currenc'] ?? [];
$ket    = $_POST['keterangan'] ?? [];
$debit  = $_POST['txt_amount'] ?? [];
$credit = $_POST['txt_credit'] ?? [];

for($i=0;$i<count($coa);$i++){

    $no_coa = trim($coa[$i]);
    $pc_i   = trim($pc[$i] ?? '');
    $cc_i   = trim($cc[$i] ?? '') ?: '-';
    $buyer_i= $buyer[$i] ?? '';
    $ws_i   = $no_ws[$i] ?? '';
    $curr_i = $currDet[$i] ?? 'IDR';
    $ket_i  = trim($ket[$i] ?? '');

    if ($ket_i === '') {
        $ket_i = $desc;
    }

    $d_debit  = (float) str_replace(',','',$debit[$i]);
    $d_credit = (float) str_replace(',','',$credit[$i]);

    // Baris benar-benar kosong (belum diisi sama sekali) - lewati saja.
    if(($no_coa=='-' || $no_coa=='') && $d_debit == 0 && $d_credit == 0) continue;

    if ($no_coa === '-' || $no_coa === '') {
        throw new Exception('Baris ke-'.($i+1).': COA wajib diisi.');
    }

    if ($pc_i === '' || $pc_i === '-') {
        throw new Exception('Baris ke-'.($i+1).': Profit Center wajib diisi.');
    }

    if ($d_debit == 0 && $d_credit == 0) {
        throw new Exception('Baris ke-'.($i+1).': Debit/Credit harus diisi.');
    }

    if ($curr_i == 'IDR') {
        $rate_det = 1;
        $d_debit_idr = $d_debit;
        $d_credit_idr = $d_credit;
    }else{
        $rate_det = $rate;
        $d_debit_idr = $d_debit * $rate;
        $d_credit_idr = $d_credit * $rate;
    }

    $noCoaEsc = mysqli_real_escape_string($conn2, $no_coa);

    $sqlcoa = q($conn2,"select nama_coa from mastercoa_v2 where no_coa = '$noCoaEsc'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'] ?? null;

    if (!$nama_coa) {
        throw new Exception('Baris ke-'.($i+1).': COA "'.$no_coa.'" tidak ditemukan.');
    }

    if (in_array($no_coa, $coaWajibCC, true) && ($cc_i === '-' || $cc_i === '')) {
        throw new Exception('Baris ke-'.($i+1).': COA '.$no_coa.' wajib isi Cost Center.');
    }

    $ccEsc = mysqli_real_escape_string($conn2, $cc_i);

    $nama_cc = null;
    if ($ccEsc !== '-' && $ccEsc !== '') {
        $sqlcc = q($conn2,"select cc_name from b_master_cc where no_cc = '$ccEsc'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = $rowcc['cc_name'] ?? null;
    }
    $nama_cc = mysqli_real_escape_string($conn2, $nama_cc ?: '-');

    $pcEsc = mysqli_real_escape_string($conn2, $pc_i);
    $buyerEsc = mysqli_real_escape_string($conn2, $buyer_i);
    $wsEsc = mysqli_real_escape_string($conn2, $ws_i);
    $currDetEsc = mysqli_real_escape_string($conn2, $curr_i);
    $ketEsc = mysqli_real_escape_string($conn2, $ket_i);
    $namaCoaEsc = mysqli_real_escape_string($conn2, $nama_coa);

    $bankoutNoneValues[] = "('$doc_num', '$doc_date', '$refNumEsc', '$noCoaEsc', '$ccEsc', '$buyerEsc', '$wsEsc', '$currDetEsc', '$d_debit', '$d_credit', '$ketEsc', '$pcEsc')";

    $journalValues[] = "('$doc_num', '$doc_date', '$refNumEsc', '$noCoaEsc', '$namaCoaEsc', '$ccEsc', '$nama_cc', '-', '', '$buyerEsc', '$wsEsc', '$currDetEsc', '$rate_det', '$d_debit', '$d_credit', '$d_debit_idr', '$d_credit_idr', 'Draft', '$ketEsc', '$userEsc', '$create_date', '', '', '', '', '$pcEsc')";

}

if (empty($bankoutNoneValues)) {
    throw new Exception('Detail transaksi tidak boleh kosong.');
}

q($conn2, "INSERT INTO b_bankout_none
(no_bankout,tgl_bankout,reff_doc,no_coa,no_costcntr,buyer,no_ws,curr,debit,credit,deskripsi, profit_center)
VALUES " . implode(',', $bankoutNoneValues));

q($conn2, "INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES " . implode(',', $journalValues));

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
