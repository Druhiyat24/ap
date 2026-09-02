<?php
// Cancel 1 Kontrabon (dari List). Dalam 1 TRANSAKSI:
//   1) kontrabon_new_h.status = 'Cancel' (+ cancel_user/date)
//   2) kembalikan kontrabon_new_ref.status = 'Available' untuk no_reff-nya
// Efek: reff bisa dipakai lagi, dan BPB kontrabon ini otomatis reusable karena
// query "already used" (check_bpb & save guard) sudah mengecualikan status Cancel.
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Jakarta'); // WIB

$user = $_SESSION['username'] ?? '';
$now  = date('Y-m-d H:i:s');
$doc  = trim($_POST['doc_number'] ?? '');
if ($doc === '') { echo json_encode(['ok' => false, 'msg' => 'Document number is empty.']); exit; }

$de = mysqli_real_escape_string($conn2, $doc);
$h  = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT id, unik_code, no_reff, status FROM ir_kontrabon_h WHERE doc_number = '$de' LIMIT 1"));
if (!$h) { echo json_encode(['ok' => false, 'msg' => 'Invoice Received not found.']); exit; }
if (($h['status'] ?? '') === 'Cancel') { echo json_encode(['ok' => false, 'msg' => 'Invoice Received is already cancelled.']); exit; }

// GUARD: sekali dokumen IR-nya sudah dipakai transaksi lain (transfer TFTA/TATP/TPTF
// atau sudah di-approve/di-proses divisi lain), kontrabon TIDAK boleh dibatalkan.
// Sinyal: status IR sudah bergerak dari 'Received', atau ada transfer aktif (non-Cancel).
$ihq = mysqli_query($conn2, "SELECT status FROM ir_invoice_supp_h WHERE doc_number = '$de' LIMIT 1");
$irStatus = ($ihq && ($ihr = mysqli_fetch_assoc($ihq))) ? ($ihr['status'] ?? null) : null;
if ($irStatus !== null && !in_array($irStatus, ['Received', 'Cancel'], true)) {
    echo json_encode(['ok' => false, 'msg' => "Cannot cancel: the Invoice Received document is already in process (status: $irStatus). It has been used in another transaction."]); exit;
}
$tcq = mysqli_query($conn2, "SELECT COUNT(*) c FROM ir_trans_invoice_supp WHERE doc_number = '$de' AND status <> 'Cancel'");
if ($tcq && ($tcr = mysqli_fetch_assoc($tcq)) && (int) $tcr['c'] > 0) {
    echo json_encode(['ok' => false, 'msg' => 'Cannot cancel: this document has already been used in another transaction (transfer/approval). Reverse that transaction first.']); exit;
}

$fail = null;
mysqli_begin_transaction($conn2);

$upd = mysqli_query($conn2, "UPDATE ir_kontrabon_h
    SET status = 'Cancel', cancel_user = '" . mysqli_real_escape_string($conn2, $user) . "', cancel_date = '$now'
    WHERE doc_number = '$de' AND status <> 'Cancel'");
if (!$upd || mysqli_affected_rows($conn2) === 0) $fail = 'Failed to cancel (maybe already cancelled).';

if (!$fail && !empty($h['no_reff'])) {
    $re = mysqli_real_escape_string($conn2, $h['no_reff']);
    if (!mysqli_query($conn2, "UPDATE ir_kontrabon_ref SET status = 'Available' WHERE ref_number = '$re' AND status = 'Used'"))
        $fail = 'Failed to release reference: ' . mysqli_error($conn2);
}

// Mirror ke IR: batalkan juga dokumen Invoice Received-nya.
if (!$fail) {
    if (!mysqli_query($conn2, "UPDATE ir_invoice_supp_h SET status = 'Cancel', cancel_ir_by = '" . mysqli_real_escape_string($conn2, $user) . "', cancel_ir_date = '$now' WHERE doc_number = '$de'"))
        $fail = 'IR cancel: ' . mysqli_error($conn2);
    if (!$fail) mysqli_query($conn2, "UPDATE ir_invoice_supp SET status = 'Cancel' WHERE doc_number = '$de'");
}

if ($fail) { mysqli_rollback($conn2); echo json_encode(['ok' => false, 'msg' => $fail]); exit; }
mysqli_commit($conn2);

// IR dibatalkan -> bersihkan info Invoice/Faktur yang tadinya di-set untuk BPB milik
// IR ini, di bpb_new (yg kita set saat save, dipandu upt_dok_inv = doc IR) DAN
// bpb_knitting, supaya tidak muncul lagi di laporan_pembelian.
$unik = mysqli_real_escape_string($conn2, $h['unik_code'] ?? '');
if ($unik !== '') {
    $bpbList = [];
    $bq = mysqli_query($conn2, "SELECT DISTINCT no_bpb FROM ir_kontrabon_bpb WHERE unik_code = '$unik' AND no_bpb <> ''");
    while ($bq && ($br = mysqli_fetch_assoc($bq))) $bpbList[] = "'" . mysqli_real_escape_string($conn2, $br['no_bpb']) . "'";
    if ($bpbList) {
        $inList = implode(',', $bpbList);
        // bpb_new: hanya yang di-set oleh IR ini (upt_dok_inv = doc IR).
        mysqli_query($conn2, "UPDATE bpb_new SET upt_dok_inv = NULL, upt_no_inv = NULL, upt_tgl_inv = NULL, upt_no_faktur = NULL, upt_tgl_faktur = NULL
            WHERE no_bpb IN ($inList) AND upt_dok_inv = '$de'");
        // bpb_knitting: bersihkan info invoice/faktur untuk BPB ini.
        mysqli_query($conn2, "UPDATE bpb_knitting SET no_invoice = NULL, tgl_invoice = NULL, no_faktur = NULL, tgl_faktur = NULL
            WHERE no_bpb IN ($inList)");
    }
}

echo json_encode([
    'ok'  => true,
    'msg' => "Invoice Received $doc cancelled. Reference '" . ($h['no_reff'] ?? '') . "' is available again and its BPB can be reused.",
]);
mysqli_close($conn2);
