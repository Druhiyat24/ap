<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

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

$doc_num_raw = $_POST['doc_num'] ?? '';
$date = date("Y-m-d", strtotime($_POST['date'] ?? ''));
$customer = mysqli_real_escape_string($conn2, $_POST['customer'] ?? '');
$profit_center = mysqli_real_escape_string($conn2, $_POST['profit_center'] ?? '');
$akun = mysqli_real_escape_string($conn2, $_POST['akun'] ?? '');
$amount = floatval($_POST['amount'] ?? 0);
$rate = floatval($_POST['rate'] ?? 0) ?: 1;
$deskripsi = mysqli_real_escape_string($conn2, $_POST['deskripsi'] ?? '');
$create_user = mysqli_real_escape_string($conn2, $_POST['create_user'] ?? 'system');
$create_date = date("Y-m-d H:i:s");
$total_nag = floatval($_POST['total_nag'] ?? 0);
$total_nak = floatval($_POST['total_nak'] ?? 0);

$details = json_decode($_POST['details'] ?? '', true) ?: [];

if (!$doc_num_raw) {
    echo "Invalid data";
    exit;
}

$doc_num = mysqli_real_escape_string($conn2, $doc_num_raw);

mysqli_begin_transaction($conn2);

try {

    // Dokumen ini dipakai bareng buat edit reff None, Advance, Settlement,
    // dan List Payment (lihat petty-cashout.php) - reff diambil dari data
    // tersimpan (bukan hardcode 'None') supaya tidak ketimpa jadi salah tipe.
    $sqlReff = dbExec($conn2, "SELECT status, reff FROM c_petty_cashout_h WHERE no_pco = '$doc_num' LIMIT 1 FOR UPDATE");
    $rowReff = mysqli_fetch_assoc($sqlReff);

    if (!$rowReff) {
        throw new Exception("Data Petty Cash Out tidak ditemukan");
    }

    if ($rowReff['status'] !== 'Draft') {
        throw new Exception("Data sudah bukan Draft, tidak bisa diedit");
    }

    $reff = $rowReff['reff'];

    $sqlx = dbExec($conn2, "select CONCAT( REPLACE(type_journal, 'Reverse ', ''), ' (Rev ', (SELECT COUNT(*)+1 FROM (select * from tbl_list_journal WHERE no_journal = '$doc_num' and type_journal like '%(Rev%' and type_journal not like '%Reverse%' GROUP BY type_journal) a), ')') AS type_journal from tbl_list_journal where no_journal = '$doc_num' limit 1");
    $rowx = mysqli_fetch_array($sqlx);
    $type_journal = !empty($rowx['type_journal']) ? $rowx['type_journal'] : ($reff . ' (Rev 1)');

    $sqlcoa = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$akun'");
    $rowcoa = mysqli_fetch_array($sqlcoa);
    $nama_coa = $rowcoa['nama_coa'];

    dbExec($conn2, "INSERT into tbl_list_journal (id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center) select '', no_journal, tgl_journal, CONCAT('Reverse ',type_journal) type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, faktur_pajak, tgl_faktur_pajak, buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr, 'Updated' status, keterangan, create_by, create_date, '$create_user' approve_by, CURRENT_TIMESTAMP() approve_date, cancel_by, cancel_date, created_at, updated_at, profit_center from tbl_list_journal where no_journal = '$doc_num' and status != 'Updated'");

    dbExec($conn2, "UPDATE tbl_list_journal set status = 'Updated' where no_journal = '$doc_num' and status = 'Draft'");

    dbExec($conn2, "UPDATE c_petty_cashout_h SET tgl_pco = '$date', nama_supp = '$customer', profit_center = '$profit_center', amount = '$amount', deskripsi = '$deskripsi' WHERE no_pco = '$doc_num'");

    dbExec($conn2, "UPDATE c_report_pettycash set transaksi_date = '$date', credit = '$amount', deskripsi = '$deskripsi' where no_doc = '$doc_num'");

    // Baris-baris tbl_list_journal / c_petty_cashout_none dikumpulkan dulu ke
    // array, baru di-insert sekali per tabel (bulk insert) di akhir.
    $journalRows = [];

    if ($total_nag != 0) {
        $journalRows[] = "('$doc_num', '$date', '$type_journal', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$total_nag', '0', '$total_nag', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', 'NAG')";
    }

    if ($total_nak != 0) {
        $journalRows[] = "('$doc_num', '$date', '$type_journal', '$akun', '$nama_coa', '-', '-', '-', '', '-', '-', 'IDR', '1', '0', '$total_nak', '0', '$total_nak', 'Draft', '$deskripsi', '$create_user', '$create_date', '', '', '', '', 'NAK')";
    }

    dbExec($conn2, "insert into c_petty_cashout_none_cancel (select * from c_petty_cashout_none where no_pco='$doc_num')");
    dbExec($conn2, "Delete from c_petty_cashout_none where no_pco='$doc_num'");

    $detRows = [];

    foreach ($details as $d) {
        $coa        = mysqli_real_escape_string($conn2, $d['coa'] ?? '');
        $prof_ctr   = mysqli_real_escape_string($conn2, $d['prof_ctr'] ?? '');
        $cost_ctr   = mysqli_real_escape_string($conn2, $d['cost_ctr'] ?? '');
        $buyer      = mysqli_real_escape_string($conn2, $d['buyer'] ?? '');
        $ws         = mysqli_real_escape_string($conn2, $d['ws'] ?? '');
        $currency   = mysqli_real_escape_string($conn2, $d['currency'] ?? 'IDR');
        $debit  = floatval($d['debit'] ?? 0);
        $credit = floatval($d['credit'] ?? 0);
        $ket        = mysqli_real_escape_string($conn2, !empty($d['keterangan']) ? $d['keterangan'] : $deskripsi);

        $sqlcoadet = dbExec($conn2, "select nama_coa from mastercoa_v2 where no_coa = '$coa'");
        $rowcoadet = mysqli_fetch_array($sqlcoadet);
        $nama_coa_det = isset($rowcoadet['nama_coa']) ? $rowcoadet['nama_coa'] : null;

        $sqlcc = dbExec($conn2, "select cc_name from b_master_cc where no_cc = '$cost_ctr'");
        $rowcc = mysqli_fetch_array($sqlcc);
        $nama_cc = isset($rowcc['cc_name']) ? $rowcc['cc_name'] : null;

        $t_debit = $debit * $rate;
        $t_credit = $credit * $rate;

        $detRows[] = "('$doc_num', '$date', '$reff', '$coa', '$prof_ctr', '$cost_ctr', '$buyer', '$ws', '$currency', '$debit', '$credit', '$ket')";

        $journalRows[] = "('$doc_num', '$date', '$type_journal', '$coa', '$nama_coa_det', '$cost_ctr', '$nama_cc', '', '', '$buyer', '$ws', '$currency', '1', '$debit', '$credit', '$t_debit', '$t_credit', 'Draft', '$ket', '$create_user', '$create_date', '', '', '', '', '$prof_ctr')";
    }

    if (!empty($detRows)) {
        dbExec($conn2, "
            INSERT INTO c_petty_cashout_none (no_pco,tgl_pco,reff_doc,no_coa, profit_center, no_costcntr,buyer,no_ws,curr,debit,credit,deskripsi)
            VALUES " . implode(', ', $detRows)
        );
    }

    if (!empty($journalRows)) {
        dbExec($conn2, "
            INSERT INTO tbl_list_journal (no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date, buyer, no_ws, curr, rate, debit, credit, debit_idr, credit_idr, status, keterangan, create_by, create_date, approve_by, approve_date, cancel_by, cancel_date, profit_center)
            VALUES " . implode(', ', $journalRows)
        );
    }

    mysqli_commit($conn2);

    echo "OK";

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo "Error: " . $e->getMessage();
}
