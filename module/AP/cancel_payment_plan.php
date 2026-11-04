<?php
/* ============================================================================
   Update Payment Plan - membatalkan satu dokumen. Pasangannya
   list-payment-plan.php.

   Dokumen TIDAK dihapus: kepalanya jadi CANCEL, isinya jadi status 'N'.
   Dgn begitu riwayat penjadwalan tetap terbaca, dan voucher di dalamnya
   kembali terhitung "belum dijadwalkan" - persis cara cancel_duedate_update()
   di AR.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

$doc = trim(isset($_POST['doc_number']) ? $_POST['doc_number'] : '');
if ($doc === '') {
    echo json_encode(array('status' => 'error', 'message' => 'No document selected.'));
    exit;
}
$doc_esc  = mysqli_real_escape_string($conn1, $doc);
$user_esc = mysqli_real_escape_string($conn1, $user);

$alasan = trim(isset($_POST['alasan']) ? (string) $_POST['alasan'] : '');

$cek = mysqli_query($conn1, "SELECT status, keterangan FROM ap_payment_plan_h
        WHERE doc_number = '$doc_esc' LIMIT 1");
if (!$cek || !mysqli_num_rows($cek)) {
    echo json_encode(array('status' => 'error', 'message' => 'Document not found.'));
    exit;
}
$h = mysqli_fetch_assoc($cek);
if (strtoupper((string) $h['status']) === 'CANCEL') {
    echo json_encode(array('status' => 'error', 'message' => 'This document was already cancelled.'));
    exit;
}

/* Alasannya disambung ke keterangan yang sudah ada, bukan menimpanya. */
$ket = (string) $h['keterangan'];
if ($alasan !== '') {
    $ket = ($ket === '' ? '' : $ket . ' | ') . 'Cancelled: ' . $alasan;
}
$ket_sql = ($ket === '') ? 'NULL' : "'" . mysqli_real_escape_string($conn1, $ket) . "'";

mysqli_query($conn1, 'START TRANSACTION');

$ok1 = mysqli_query($conn1, "UPDATE ap_payment_plan_h
        SET status = 'CANCEL', keterangan = $ket_sql,
            cancel_by = '$user_esc', cancel_date = NOW()
        WHERE doc_number = '$doc_esc'");
$ok2 = mysqli_query($conn1, "UPDATE ap_payment_plan_det
        SET status = 'N' WHERE doc_number = '$doc_esc'");

if (!$ok1 || !$ok2) {
    mysqli_query($conn1, 'ROLLBACK');
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn1)));
    exit;
}

mysqli_query($conn1, 'COMMIT');

echo json_encode(array(
    'status'  => 'success',
    'message' => 'Payment plan ' . $doc . ' cancelled.',
));
?>
