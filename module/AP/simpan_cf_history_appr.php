<?php
/* ============================================================================
   HISTORY PROJECTION - MENJALANKAN Post / First Approval / Second Approval.

   Dipanggil cf_history_detail.php lewat AJAX, balasannya JSON.

   SELURUH SYARATNYA DIPERIKSA ULANG DI SINI, tidak cuma di layar: tombolnya
   memang disembunyikan kalau pemakainya tidak berhak, tetapi layar bisa
   dilewati. Yang diperiksa: pemakainya masih login, dokumennya ada dan belum
   dibatalkan, aksinya memang aksi yang BOLEH dari status sekarang, dan
   pemakainya punya hak untuk aksi itu.

   Perpindahannya ditulis dgn syarat appr_status LAMA ikut di WHERE - jadi
   dua orang yang menekan tombol yang sama dalam detik yang sama tidak bisa
   membuat dokumennya melompat dua langkah: yang kedua tidak mengubah baris
   apa pun dan diberi tahu keadaannya sudah berubah.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
require_once __DIR__ . '/cf_history_appr_lib.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

$doc  = trim(isset($_POST['doc_number']) ? (string) $_POST['doc_number'] : '');
$aksi = trim(isset($_POST['aksi']) ? (string) $_POST['aksi'] : '');
if ($doc === '' || $aksi === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Document number and action are required.'));
    exit;
}

$docEsc = mysqli_real_escape_string($conn2, $doc);
$h = mysqli_fetch_assoc(mysqli_query($conn2,
    "select doc_number, status, appr_status from ap_cf_history_h
      where doc_number = '$docEsc' limit 1"));
if (!$h) {
    echo json_encode(array('status' => 'error', 'message' => 'Document not found.'));
    exit;
}
if (strcasecmp((string) $h['status'], 'CANCEL') === 0) {
    echo json_encode(array('status' => 'error', 'message' => 'This document is cancelled.'));
    exit;
}

$st = cfa_status($h['appr_status']);
$l  = cfa_langkah();
$langkah = $l[$st];

if ($langkah['aksi'] === '' || $langkah['aksi'] !== $aksi) {
    echo json_encode(array('status' => 'error',
        'message' => 'This document is already ' . $langkah['teks'] . '.',
        'appr_status' => $st, 'teks' => $langkah['teks']));
    exit;
}
if (!cfa_punya_peran($conn2, $user, $langkah['peran'])) {
    echo json_encode(array('status' => 'error',
        'message' => 'You do not have the right to do this.'));
    exit;
}

list($kolOleh, $kolWaktu) = $langkah['kolom'];
$userEsc = mysqli_real_escape_string($conn2, $user);
$baru    = mysqli_real_escape_string($conn2, $langkah['berikut']);
$lama    = mysqli_real_escape_string($conn2, $st);

$ok = mysqli_query($conn2, "update ap_cf_history_h
                               set appr_status = '$baru',
                                   `$kolOleh`   = '$userEsc',
                                   `$kolWaktu`  = NOW()
                             where doc_number = '$docEsc'
                               and appr_status = '$lama'");
if (!$ok) {
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}
if (mysqli_affected_rows($conn2) < 1) {
    /* Statusnya sudah berubah di antara layar dibuka dan tombol ditekan. */
    $r = mysqli_fetch_assoc(mysqli_query($conn2,
        "select appr_status from ap_cf_history_h where doc_number = '$docEsc' limit 1"));
    $kini = cfa_status($r ? $r['appr_status'] : '');
    echo json_encode(array('status' => 'error',
        'message' => 'Someone else changed this document - it is now ' . cfa_teks($kini) . '.',
        'appr_status' => $kini, 'teks' => cfa_teks($kini)));
    exit;
}

echo json_encode(array('status' => 'success',
    'appr_status' => $langkah['berikut'],
    'teks'        => cfa_teks($langkah['berikut'])));
