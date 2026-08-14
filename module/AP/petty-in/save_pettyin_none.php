<?php
include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

// Jalankan query mutating (INSERT/UPDATE/DELETE) dan lempar Exception kalau
// gagal, supaya transaksi ke-rollback dengan benar.
function dbExec($conn, $sql)
{
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        throw new Exception('DB Error: ' . mysqli_error($conn));
    }
    return $result;
}

mysqli_begin_transaction($conn2);

try{

$ref_num   = $_POST['ref_num3'];
$doc_date  = date('Y-m-d',strtotime($_POST['tgl_active3']));
$pc_kas    = $_POST['profit_center_kas3'];
$akun      = mysqli_real_escape_string($conn2, $_POST['account3']);
$curr      = mysqli_real_escape_string($conn2, $_POST['currency3']);
$kode_kas  = $_POST['kode_kas3'];
$desc      = mysqli_real_escape_string($conn2, $_POST['pesan3'] ?? '-');
$cash_flow = $_POST['cash_flow3'] ?? '';

if ($cash_flow === '') {
    throw new Exception('Cash Flow Category tidak boleh kosong.');
}
$cash_flow = (int) $cash_flow;

$amount = str_replace(',','',$_POST['amount_kas3']);

$user = $_SESSION['username'] ?? 'system';

$bulan = date('m',strtotime($doc_date));

$tahun = date('Y',strtotime($doc_date));
$status = "Draft";
$create_date = date("Y-m-d H:i:s");


/* =========================
   PREFIX DOC NUMBER
========================= */

$prefix = "KKM/".$kode_kas."/".$tahun."/".$bulan;

/* =========================
   AMBIL MAX URUTAN
========================= */

$sql = dbExec($conn2,"
SELECT MAX(CAST(RIGHT(no_pci,5) AS UNSIGNED)) AS max_urut
FROM c_petty_cashin_h
WHERE no_pci LIKE '$prefix%'
FOR UPDATE
");

$sqlJ = dbExec($conn2,"
SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut
FROM tbl_list_journal
WHERE no_journal LIKE '$prefix%'
FOR UPDATE
");

$row = mysqli_fetch_assoc($sql);
$maxHeader = (int) ($row['max_urut'] ?? 0);

$rowJ = mysqli_fetch_assoc($sqlJ);
$maxJournal = (int) ($rowJ['max_urut'] ?? 0);

$urutan = max($maxHeader, $maxJournal) + 1;


/* =========================
   DOC NUMBER BARU
========================= */

$doc_num = $prefix."/".sprintf("%05d",$urutan);

$sqlcoa = dbExec($conn2,"select nama_coa from mastercoa_v2 where no_coa = '$akun'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

/* =========================
INSERT HEADER
========================= */


dbExec($conn2,"
INSERT INTO c_petty_cashin_h (no_pci,tgl_pci,reff,reff_doc,oth_doc,coa_akun,curr,amount,status, create_by,create_date,deskripsi,id_cash_flow)
VALUES
('$doc_num', '$doc_date', '$ref_num', '-', '-', '$akun', '$curr', '$amount', '$status', '$user', '$create_date', '$desc', '$cash_flow')
");


dbExec($conn2,"
INSERT INTO c_report_pettycash (transaksi_date,no_doc,deskripsi,akun,categori,cf_categori,curr,debit,credit, balance, status,id_cash_flow)
VALUES
('$doc_date', '$doc_num', '$desc', '$akun', '', '', '$curr','$amount', '0', '$amount', '$status', '$cash_flow')
");


/* =========================
INSERT DETAIL TABLE
========================= */
// Baris-baris c_petty_cashin_none / tbl_list_journal dikumpulkan dulu ke
// array, baru di-insert sekali per tabel (bulk insert) di akhir.

$coa    = $_POST['nomor_coa'];
$pc     = $_POST['prof_ctr'];
$cc     = $_POST['cost_ctr'];
$buyer  = $_POST['buyer'];
$no_ws  = $_POST['no_ws'];
$curr   = $_POST['currenc'];
$ket    = $_POST['keterangan'];
$debit  = $_POST['txt_amount'];
$credit = $_POST['txt_credit'];

$detRows     = [];
$journalRows = ["('$doc_num', '$doc_date', '$ref_num', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '$amount', '0', '$amount', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_kas')"];

for($i=0;$i<count($coa);$i++){

	$no_coa = mysqli_real_escape_string($conn2, $coa[$i]);
    $pc_i   = mysqli_real_escape_string($conn2, $pc[$i]);
    $cc_i   = mysqli_real_escape_string($conn2, $cc[$i]);
    $buyer_i= mysqli_real_escape_string($conn2, $buyer[$i]);
    $ws_i   = mysqli_real_escape_string($conn2, $no_ws[$i]);
    $curr_i = mysqli_real_escape_string($conn2, $curr[$i]);
    $ket_i  = mysqli_real_escape_string($conn2, !empty($ket[$i]) ? $ket[$i] : $desc);



if($coa[$i]=='-' || $coa[$i]=='') continue;

$d_debit  = str_replace(',','',$debit[$i]);
$d_credit = str_replace(',','',$credit[$i]);

$sqlcoa = dbExec($conn2,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

$sqlcc = dbExec($conn2,"select cc_name from b_master_cc where no_cc = '$cc_i'");
$rowcc = mysqli_fetch_array($sqlcc);
$nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

$detRows[] = "('$doc_num', '$doc_date', '$no_coa', '$pc_i', '$cc_i', '$buyer_i', '$ws_i', '$curr_i', '$d_debit', '$d_credit', '$ket_i')";

$journalRows[] = "('$doc_num', '$doc_date', '$ref_num', '$no_coa', '$nama_coa', '$cc_i', '$nama_cc', '-', '', '$buyer_i', '$ws_i', '$curr_i', '1', '$d_debit', '$d_credit', '$d_debit', '$d_credit', 'Draft', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')";

}

if (!empty($detRows)) {
    dbExec($conn2, "
        INSERT INTO c_petty_cashin_none (no_pci,tgl_pci,no_coa, profit_center, no_costcenter,buyer,no_ws,curr,debit,credit,deskripsi)
        VALUES " . implode(', ', $detRows)
    );
}

dbExec($conn2, "
    INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
    VALUES " . implode(', ', $journalRows)
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
