<?php
/* Cancel satu dokumen proyeksi. Dipanggil AJAX dari cash-projection.php.
   Dokumen yang sudah Cancel tidak bisa di-cancel lagi. */

session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$user   = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$no_doc = isset($_POST['no_doc']) ? trim($_POST['no_doc']) : '';

if ($user == '') {
    echo json_encode(array('status' => 'gagal', 'pesan' => 'Session expired, please log in again.'));
    exit;
}
if ($no_doc == '') {
    echo json_encode(array('status' => 'gagal', 'pesan' => 'Document number is empty.'));
    exit;
}

$doc = mysqli_real_escape_string($conn2, $no_doc);
$usr = mysqli_real_escape_string($conn2, $user);

$sql = "update ap_cf_projection
        set status = 'Cancel', cancel_by = '$usr', cancel_date = NOW()
        where no_doc = '$doc' and status <> 'Cancel'";

if (!mysqli_query($conn2, $sql)) {
    echo json_encode(array('status' => 'gagal', 'pesan' => mysqli_error($conn2)));
    exit;
}
if (mysqli_affected_rows($conn2) == 0) {
    echo json_encode(array('status' => 'gagal', 'pesan' => 'Document not found or already cancelled.'));
    exit;
}

echo json_encode(array('status' => 'ok'));
