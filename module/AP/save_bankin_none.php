<?php
include '../../conn/conn.php';
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

$ref_num   = $_POST['ref_num3'];
$doc_date  = date('Y-m-d',strtotime($_POST['tgl_active3']));
$supp      = $_POST['nama_supp3'];
$akun      = $_POST['account3'];
$bank      = $_POST['bank3'];
$kode_bank = $_POST['kode_bank3'];
$curr      = $_POST['currency3'];
$pc_bank   = $_POST['profit_center_bank3'];
$desc      = trim($_POST['pesan3'] ?? '');
$cash_flow = $_POST['cash_flow3'] ?? '';

if ($desc === '') {
    throw new Exception('Description tidak boleh kosong.');
}

if ($cash_flow === '') {
    throw new Exception('Cash Flow Category tidak boleh kosong.');
}
$cash_flow = (int) $cash_flow;

$amount = str_replace(',','',$_POST['amount_bank3']);
$rate   = str_replace(',','',$_POST['rate_bank3']);

if ($curr !== 'IDR' && (float) $rate === 1.0) {
    throw new Exception('Currency non IDR harus memiliki rate selain 1.');
}
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

$sqlcoa1 = q($conn2,"select no_coa,nama_coa from mastercoa_v2 where nama_coa like '%$akun%' and ind_categori2 = 'ASET'");
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
INSERT INTO tbl_bankin_arcollection
(
doc_num,date,ref_data,customer,akun,bank,curr,id_coa,id_cost_center, amount,rate,eqv_idr, outstanding,deskripsi, status,create_by,create_date, profit_center, id_cash_flow
)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$supp', '$akun', '$bank', '$curr', '-', '-', '$amount', '$rate', '$eqv', '$amount', '$desc', '$status', '$user', '$create_date', '$pc_bank', '$cash_flow')
");


q($conn2,"
INSERT INTO b_reportbank
(transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance,status,id_cash_flow)
VALUES
('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr','$amount', '0', '$amount', '$status', '$cash_flow')
");


q($conn2,"
INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES
('$doc_num', '$doc_date', '$ref_num', '$no_coa1', '$nama_coa1', '-', '-', '-', '', '-', '-', '$curr', '$rate', '$amount', '0', '$eqv', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_bank')
");


/* =========================
INSERT DETAIL TABLE (bulk)
========================= */

$coa    = $_POST['nomor_coa'] ?? [];
$pc     = $_POST['prof_ctr'] ?? [];
$cc     = $_POST['cost_ctr'] ?? [];
$buyer  = $_POST['buyer'] ?? [];
$no_ws  = $_POST['no_ws'] ?? [];
$curr_d = $_POST['currenc'] ?? [];
$ket    = $_POST['keterangan'] ?? [];
$debit  = $_POST['txt_amount'] ?? [];
$credit = $_POST['txt_credit'] ?? [];

$bankinValues = [];
$journalValues = [];

for ($i = 0; $i < count($coa); $i++) {

    $no_coa = $coa[$i];
    $pc_i   = $pc[$i];
    $cc_i   = $cc[$i];
    $buyer_i= $buyer[$i];
    $ws_i   = $no_ws[$i];
    $curr_i = $curr_d[$i];
    // Fallback di server agar tetap konsisten bila request tidak lewat UI.
    $ket_i  = trim($ket[$i] ?? '');
    if ($ket_i === '') {
        $ket_i = $desc;
    }

    if ($no_coa == '-' || $no_coa == '') continue;

    if ($pc_i == '-' || $pc_i == '') {
        throw new Exception('Baris ke-'.($i+1).': Profit Center wajib diisi.');
    }

    $d_debit  = (float) str_replace(',','',$debit[$i]);
    $d_credit = (float) str_replace(',','',$credit[$i]);

    if ($d_debit == 0 && $d_credit == 0) {
        throw new Exception('Baris ke-'.($i+1).': Debit/Credit harus diisi.');
    }

    if ($curr_i == 'IDR') {
        $rate_det = 1;
        $d_debit_idr = $d_debit;
        $d_credit_idr = $d_credit;
    } else {
        $rate_det = $rate;
        $d_debit_idr = $d_debit * $rate;
        $d_credit_idr = $d_credit * $rate;
    }

    $sqlcoa = q($conn2,"select nama_coa from mastercoa_v2 where no_coa = '".mysqli_real_escape_string($conn2,$no_coa)."'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'] ?? null;

    if (!$nama_coa) {
        throw new Exception('Baris ke-'.($i+1).': COA "'.$no_coa.'" tidak ditemukan.');
    }

    // COA tertentu wajib isi Cost Center (sama seperti aturan di sisi JS).
    $sqlWajibCc = q($conn2,"select no_coa from mastercoa_v2 where no_coa = '".mysqli_real_escape_string($conn2,$no_coa)."'
        and (support_gen_adm = 'Y' OR support_prod = 'Y' OR prod = 'Y' OR support_sell = 'Y')");
    if (mysqli_num_rows($sqlWajibCc) > 0 && (!$cc_i || $cc_i === '-' || $cc_i === '')) {
        throw new Exception('Baris ke-'.($i+1).': COA '.$no_coa.' wajib isi Cost Center.');
    }

    $nama_cc = null;
    if ($cc_i && $cc_i !== '-' && $cc_i !== '') {
        $sqlcc = q($conn2,"select cc_name from b_master_cc where no_cc = '".mysqli_real_escape_string($conn2,$cc_i)."'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = $rowcc['cc_name'] ?? null;
    }

    $noCoaEsc  = mysqli_real_escape_string($conn2, $no_coa);
    $ccEsc     = mysqli_real_escape_string($conn2, $cc_i ?: '-');
    $namaCcEsc = mysqli_real_escape_string($conn2, $nama_cc ?: '-');
    $buyerEsc  = mysqli_real_escape_string($conn2, $buyer_i);
    $wsEsc     = mysqli_real_escape_string($conn2, $ws_i);
    $currEsc   = mysqli_real_escape_string($conn2, $curr_i);
    $ketEsc    = mysqli_real_escape_string($conn2, $ket_i);
    $pcEsc     = mysqli_real_escape_string($conn2, $pc_i);
    $namaCoaEsc = mysqli_real_escape_string($conn2, $nama_coa);

    $bankinValues[] = "('$doc_num', '$noCoaEsc', '$ccEsc', '$buyerEsc', '$wsEsc', '$currEsc', '$d_debit', '$d_credit', '$ketEsc', '$pcEsc')";

    $journalValues[] = "('$doc_num', '$doc_date', '$ref_num', '$noCoaEsc', '$namaCoaEsc', '$ccEsc', '$namaCcEsc', '-', '', '$buyerEsc', '$wsEsc', '$currEsc', '$rate_det', '$d_debit', '$d_credit', '$d_debit_idr', '$d_credit_idr', 'Draft', '$ketEsc', '$user', '$create_date', '', '', '', '', '$pcEsc')";
}

if (empty($bankinValues)) {
    throw new Exception('Detail transaksi tidak boleh kosong.');
}

q($conn2, "
INSERT INTO tbl_bankin
(no_doc,id_coa,id_cost_center,buyer,no_ws,curr,t_debit,t_credit,keterangan, profit_center)
VALUES " . implode(',', $bankinValues)
);

q($conn2, "
INSERT INTO tbl_list_journal
(no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
VALUES " . implode(',', $journalValues)
);


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
