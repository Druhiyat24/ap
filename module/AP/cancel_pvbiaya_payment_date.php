<?php
/* ============================================================================
   Membatalkan satu dokumen Update Payment Date PV Biaya.
   Dipanggil pvbiaya-payment-date.php lewat AJAX.

   Yang terjadi: kepala dokumen jadi CANCEL, dan SELURUH barisnya dipadamkan
   (status 'N'). Akibatnya voucher di dalamnya kembali dianggap belum punya
   rencana, jadi bisa dijadwalkan ulang lewat dokumen baru - sama dgn
   perilaku pembatalan di Payment Plan AP.

   BARISNYA TIDAK DIHAPUS. Tanggal yang pernah dipakai tetap tersimpan supaya
   dokumennya masih bisa dibaca sbg riwayat; yang berubah cuma penandanya.

   Dokumen yang SUDAH CANCEL ditolak - supaya cancel_by/cancel_date yang
   pertama tidak tertimpa oleh klik kedua.
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
$doc_esc  = mysqli_real_escape_string($conn2, $doc);
$user_esc = mysqli_real_escape_string($conn2, $user);

$cek = mysqli_query($conn2, "SELECT status FROM ap_pvbiaya_plan_h WHERE doc_number = '$doc_esc' LIMIT 1");
if (!$cek || !mysqli_num_rows($cek)) {
    echo json_encode(array('status' => 'error', 'message' => 'Document not found.'));
    exit;
}
$h = mysqli_fetch_assoc($cek);
if (strtoupper($h['status']) === 'CANCEL') {
    echo json_encode(array('status' => 'error', 'message' => 'This document has already been cancelled.'));
    exit;
}

mysqli_query($conn2, 'START TRANSACTION');

$ok = mysqli_query($conn2, "UPDATE ap_pvbiaya_plan_h
        SET status = 'CANCEL', cancel_by = '$user_esc', cancel_date = NOW()
        WHERE doc_number = '$doc_esc' AND status <> 'CANCEL'");
if (!$ok) {
    mysqli_query($conn2, 'ROLLBACK');
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}

$ok = mysqli_query($conn2, "UPDATE ap_pvbiaya_plan_det SET status = 'N'
        WHERE doc_number = '$doc_esc'");
if (!$ok) {
    mysqli_query($conn2, 'ROLLBACK');
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}

mysqli_query($conn2, 'COMMIT');
echo json_encode(array('status' => 'success', 'doc_number' => $doc));
