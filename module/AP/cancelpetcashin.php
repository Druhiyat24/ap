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

header('Content-Type: application/json');

mysqli_begin_transaction($conn2);

try {

    $no_pci_raw = $_POST['no_pci'] ?? '';
    $cancel_user = mysqli_real_escape_string($conn2, $_POST['cancel_user'] ?? 'system');
    $cancel_date = date("Y-m-d H:i:s");
    $status = 'Cancel';

    if (!$no_pci_raw) {
        throw new Exception('Nomor dokumen tidak valid');
    }

    $no_pci = mysqli_real_escape_string($conn2, $no_pci_raw);

    $sqlChk = dbExec($conn2, "SELECT status FROM c_petty_cashin_h WHERE no_pci = '$no_pci' LIMIT 1 FOR UPDATE");
    $rowChk = mysqli_fetch_assoc($sqlChk);

    if (!$rowChk) {
        throw new Exception('Data Petty Cash In tidak ditemukan');
    }

    if (strcasecmp($rowChk['status'], 'Cancel') === 0) {
        throw new Exception('Data sudah berstatus Cancel');
    }

    dbExec($conn2, "UPDATE c_petty_cashin_h SET cancel_by='$cancel_user', cancel_date='$cancel_date', status='$status' WHERE no_pci='$no_pci'");

    dbExec($conn2, "INSERT INTO tbl_list_journal_cancel (SELECT * FROM tbl_list_journal WHERE no_journal='$no_pci')");

    dbExec($conn2, "UPDATE c_report_pettycash SET status='$status' WHERE no_doc='$no_pci'");

    dbExec($conn2, "DELETE FROM tbl_list_journal WHERE no_journal='$no_pci'");

    mysqli_commit($conn2);

    echo json_encode([
        'status'  => 'ok',
        'message' => 'Dokumen ' . $no_pci . ' berhasil di-cancel'
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
