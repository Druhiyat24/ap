<?php
include '../../../conn/conn.php';
session_start();

date_default_timezone_set('Asia/Jakarta');

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

$doc_num   = mysqli_real_escape_string($conn2, $_POST['doc_num']);
$doc_date  = date('Y-m-d',strtotime($_POST['tgl_active2']));
$pc_kas    = $_POST['profit_center_kas2'];
$akun      = mysqli_real_escape_string($conn2, $_POST['account2']);
$curr      = mysqli_real_escape_string($conn2, $_POST['currency2']);
$reff_number = mysqli_real_escape_string($conn2, $_POST['reff_number']);
$desc      = mysqli_real_escape_string($conn2, $_POST['pesan2'] ?? '-');
$cash_flow = $_POST['cash_flow2'] ?? '';

if ($cash_flow === '') {
    throw new Exception('Cash Flow Category tidak boleh kosong.');
}
$cash_flow = (int) $cash_flow;

$amount = str_replace(',','',$_POST['amount_kas2']);

$user = $_SESSION['username'] ?? 'system';

$create_date = date("Y-m-d H:i:s");

if (empty($doc_num)) {
    throw new Exception('Doc number tidak boleh kosong');
}

$sqlChk = dbExec($conn2, "SELECT status, coa_akun FROM c_petty_cashin_h WHERE no_pci = '$doc_num' AND reff = 'Settlement' LIMIT 1 FOR UPDATE");
$rowChk = mysqli_fetch_assoc($sqlChk);

if (!$rowChk) {
    throw new Exception('Data Petty Cash In tidak ditemukan');
}

if ($rowChk['status'] !== 'Draft') {
    throw new Exception('Data sudah bukan Draft, tidak bisa diedit');
}

$old_akun = $rowChk['coa_akun'];

$sqlcoa = dbExec($conn2,"select nama_coa from mastercoa_v2 where no_coa = '$akun'");
$rowcoa = mysqli_fetch_array($sqlcoa);
$nama_coa = $rowcoa['nama_coa'];

/* =========================
   REVISION LABEL
   Kalau account (kode kas) berubah sampai dokumen akan di-renumber di akhir,
   reset label jurnal ke 'Settlement' lagi (jangan lanjutkan Rev dari nomor lama).
========================= */

$sqlNewKodeEarly = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '$akun'");
$rowNewKodeEarly = mysqli_fetch_assoc($sqlNewKodeEarly);
$new_kode_cash_early = $rowNewKodeEarly['kode_cash'] ?? '';

$sqlOldKodeEarly = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '" . mysqli_real_escape_string($conn2, $old_akun) . "'");
$rowOldKodeEarly = mysqli_fetch_assoc($sqlOldKodeEarly);
$old_kode_cash_early = $rowOldKodeEarly['kode_cash'] ?? '';

$willRenumber = ($new_kode_cash_early !== '' && $new_kode_cash_early !== $old_kode_cash_early);

if ($willRenumber) {
    $type_journal = "Settlement";
} else {
    $sqlrev = dbExec($conn2,"
    SELECT COUNT(DISTINCT type_journal) AS rev_count
    FROM tbl_list_journal
    WHERE no_journal = '$doc_num' AND type_journal LIKE '%(Rev %' AND type_journal NOT LIKE 'Reverse %'
    ");
    $rowrev = mysqli_fetch_assoc($sqlrev);
    $nextRev = ($rowrev['rev_count'] ?? 0) + 1;
    $type_journal = "Settlement (Rev $nextRev)";
}

/* =========================
   REVERSE OLD JOURNAL ROWS
========================= */

dbExec($conn2,"
INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
SELECT no_journal, tgl_journal, CONCAT('Reverse ', type_journal), no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated', keterangan, create_by, create_date, '$user', '$create_date', cancel_by, cancel_date, profit_center
FROM tbl_list_journal
WHERE no_journal = '$doc_num' AND status != 'Updated'
");

dbExec($conn2,"UPDATE tbl_list_journal SET status = 'Updated' WHERE no_journal = '$doc_num' AND status != 'Updated' AND type_journal NOT LIKE 'Reverse %'");

/* =========================
   BACKUP + DELETE OLD DETAIL ROWS
========================= */

dbExec($conn2,"INSERT INTO c_petty_cashin_none_cancel SELECT * FROM c_petty_cashin_none WHERE no_pci = '$doc_num'");
dbExec($conn2,"DELETE FROM c_petty_cashin_none WHERE no_pci = '$doc_num'");

/* =========================
   UPDATE HEADER
========================= */

dbExec($conn2,"
UPDATE c_petty_cashin_h
SET tgl_pci = '$doc_date', coa_akun = '$akun', curr = '$curr', amount = '$amount', deskripsi = '$desc', id_cash_flow = '$cash_flow'
WHERE no_pci = '$doc_num'
");

dbExec($conn2,"
UPDATE c_report_pettycash
SET transaksi_date = '$doc_date', debit = '$amount', deskripsi = '$desc', id_cash_flow = '$cash_flow'
WHERE no_doc = '$doc_num'
");

/* =========================
   INSERT NEW DETAIL + JOURNAL ROWS
========================= */

$coa    = $_POST['nomor_coa2'];
$pc     = $_POST['prof_ctr2'];
$cc     = $_POST['cost_ctr2'];
$buyer  = $_POST['buyer2'];
$no_ws  = $_POST['no_ws2'];
$curr_d = $_POST['currenc2'];
$ket    = $_POST['keterangan2'];
$debit  = $_POST['txt_amount2'];
$credit = $_POST['txt_credit2'];

$detRows     = [];
$journalRows = ["('$doc_num', '$doc_date', '$type_journal', '$akun', '$nama_coa', '-', '-', '$reff_number', '', '-', '-', 'IDR', '1', '$amount', '0', '$amount', '0', 'Draft', '$desc', '$user', '$create_date', '', '', '', '', '$pc_kas')"];

for($i=0;$i<count($coa);$i++){

    $no_coa = mysqli_real_escape_string($conn2, $coa[$i]);
    $pc_i   = mysqli_real_escape_string($conn2, $pc[$i]);
    $cc_i   = mysqli_real_escape_string($conn2, $cc[$i]);
    $buyer_i= mysqli_real_escape_string($conn2, $buyer[$i]);
    $ws_i   = mysqli_real_escape_string($conn2, $no_ws[$i]);
    $curr_i = mysqli_real_escape_string($conn2, $curr_d[$i]);
    $ket_i  = mysqli_real_escape_string($conn2, !empty($ket[$i]) ? $ket[$i] : $desc);

    if($coa[$i]=='-' || $coa[$i]=='') continue;

    $d_debit  = str_replace(',','',$debit[$i]);
    $d_credit = str_replace(',','',$credit[$i]);

    $sqlcoa = dbExec($conn2,"select nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa_d = isset($rowcoa['nama_coa']) ? $rowcoa['nama_coa'] : null;

    $sqlcc = dbExec($conn2,"select cc_name from b_master_cc where no_cc = '$cc_i'");
    $rowcc = mysqli_fetch_array($sqlcc);
    $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

    $detRows[] = "('$doc_num', '$doc_date', '$no_coa', '$pc_i', '$cc_i', '$buyer_i', '$ws_i', '$curr_i', '$d_debit', '$d_credit', '$ket_i')";

    $journalRows[] = "('$doc_num', '$doc_date', '$type_journal', '$no_coa', '$nama_coa_d', '$cc_i', '$nama_cc', '$reff_number', '', '$buyer_i', '$ws_i', '$curr_i', '1', '$d_debit', '$d_credit', '$d_debit', '$d_credit', 'Draft', '$ket_i', '$user', '$create_date', '', '', '', '', '$pc_i')";

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

/* =========================
   RENUMBER JIKA ACCOUNT (KODE KAS) BERUBAH - no_pci mengandung kode kas
   sebagai bagian nomornya (KKM/{kode_kas}/{tahun}/{bulan}/{urutan}), jadi
   kalau akun diganti ke kas kecil lain, nomornya perlu di-generate ulang
   supaya kodenya tetap mencerminkan akun yang benar - baru di-rename di
   semua tabel sebagai langkah terakhir sebelum commit.
========================= */

if ($akun !== $old_akun) {

    $sqlNewKode = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '$akun'");
    $rowNewKode = mysqli_fetch_assoc($sqlNewKode);
    $new_kode_cash = $rowNewKode['kode_cash'] ?? '';

    $sqlOldKode = dbExec($conn2, "SELECT kode_cash FROM mastercoa_v2 WHERE no_coa = '" . mysqli_real_escape_string($conn2, $old_akun) . "'");
    $rowOldKode = mysqli_fetch_assoc($sqlOldKode);
    $old_kode_cash = $rowOldKode['kode_cash'] ?? '';

    if ($new_kode_cash !== '' && $new_kode_cash !== $old_kode_cash) {

        $bulanNew = date('m', strtotime($doc_date));
        $tahunNew = date('Y', strtotime($doc_date));
        $newPrefix = "KKM/" . $new_kode_cash . "/" . $tahunNew . "/" . $bulanNew;

        $sqlSeq = dbExec($conn2, "SELECT MAX(CAST(RIGHT(no_pci,5) AS UNSIGNED)) AS max_urut FROM c_petty_cashin_h WHERE no_pci LIKE '$newPrefix%' FOR UPDATE");
        $rowSeq = mysqli_fetch_assoc($sqlSeq);
        $maxHeader = (int) ($rowSeq['max_urut'] ?? 0);

        $sqlSeqJ = dbExec($conn2, "SELECT MAX(CAST(RIGHT(no_journal,5) AS UNSIGNED)) AS max_urut FROM tbl_list_journal WHERE no_journal LIKE '$newPrefix%' FOR UPDATE");
        $rowSeqJ = mysqli_fetch_assoc($sqlSeqJ);
        $maxJournal = (int) ($rowSeqJ['max_urut'] ?? 0);

        $urutan = max($maxHeader, $maxJournal) + 1;
        $new_doc_num = $newPrefix . "/" . sprintf("%05d", $urutan);
        $new_doc_num_esc = mysqli_real_escape_string($conn2, $new_doc_num);

        dbExec($conn2, "UPDATE c_petty_cashin_h SET no_pci = '$new_doc_num_esc' WHERE no_pci = '$doc_num'");
        dbExec($conn2, "UPDATE c_petty_cashin_none SET no_pci = '$new_doc_num_esc' WHERE no_pci = '$doc_num'");
        dbExec($conn2, "UPDATE c_report_pettycash SET no_doc = '$new_doc_num_esc' WHERE no_doc = '$doc_num'");
        dbExec($conn2, "UPDATE tbl_list_journal SET no_journal = '$new_doc_num_esc' WHERE no_journal = '$doc_num'");

        $doc_num = $new_doc_num;
    }
}

mysqli_commit($conn2);

echo json_encode([
"status"=>"success",
"message"=>"Data berhasil diupdate No : ".$doc_num
]);

}catch(Exception $e){

mysqli_rollback($conn2);

echo json_encode([
"status"=>"error",
"message"=>$e->getMessage()
]);

}
