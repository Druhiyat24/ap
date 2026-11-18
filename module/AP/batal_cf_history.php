<?php
/* ============================================================================
   Membatalkan satu potret Cash Flow Projection.

   Yang berubah HANYA penanda di kepala dokumen: status jadi CANCEL berikut
   siapa & kapan. BARIS RINCIANNYA TIDAK DIHAPUS - ini potret, dan potret
   yang dibatalkan masih harus bisa dibuka untuk menjawab "dulu angkanya
   berapa". Yang dibatalkan cuma pemakaiannya, bukan isinya.

   Dokumen yang SUDAH CANCEL ditolak, supaya cancel_by/cancel_date yang
   pertama tidak tertimpa klik kedua.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

$doc = trim(isset($_POST['doc_number']) ? (string) $_POST['doc_number'] : '');
if ($doc === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Document number is required.'));
    exit;
}
$docEsc = mysqli_real_escape_string($conn2, $doc);

$r = mysqli_fetch_assoc(mysqli_query($conn2,
    "select status from ap_cf_history_h where doc_number = '$docEsc' limit 1"));
if (!$r) {
    echo json_encode(array('status' => 'error', 'message' => 'Document not found.'));
    exit;
}
if (strtoupper($r['status']) === 'CANCEL') {
    echo json_encode(array('status' => 'error', 'message' => 'This document is already cancelled.'));
    exit;
}

$ok = mysqli_query($conn2, "update ap_cf_history_h
                               set status = 'CANCEL',
                                   cancel_by = '" . mysqli_real_escape_string($conn2, $user) . "',
                                   cancel_date = NOW()
                             where doc_number = '$docEsc' and status <> 'CANCEL'");
if (!$ok) {
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}

echo json_encode(array('status' => 'success', 'doc_number' => $doc));
