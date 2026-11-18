<?php
/* ============================================================================
   Rincian satu dokumen Update Payment Date PV Biaya.
   Dipanggil pvbiaya-payment-date.php lewat AJAX untuk mengisi modalnya.

   Baris yang sudah DIGANTIKAN (status 'N') IKUT DITAMPILKAN, ditandai
   "Replaced". Itu memang gunanya dokumen ini disimpan: rencana yang pernah
   dipakai tetap bisa dibaca, bukan hilang begitu digeser.
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
$doc_esc = mysqli_real_escape_string($conn2, $doc);

$q = mysqli_query($conn2, "SELECT no_pv, profit_center, plan_date, curr, amount, keterangan, status
        FROM ap_pvbiaya_plan_det WHERE doc_number = '$doc_esc' ORDER BY id ASC");
if (!$q) {
    echo json_encode(array('status' => 'error', 'message' => mysqli_error($conn2)));
    exit;
}

$data = array();
while ($r = mysqli_fetch_assoc($q)) {
    $data[] = array(
        'no_pv'         => htmlspecialchars($r['no_pv'], ENT_QUOTES),
        'profit_center' => htmlspecialchars((string) $r['profit_center'], ENT_QUOTES),
        'plan_date'     => $r['plan_date'] ? date('d-M-Y', strtotime($r['plan_date'])) : '-',
        'curr'          => htmlspecialchars((string) $r['curr'], ENT_QUOTES),
        'amount'        => number_format((float) $r['amount'], 2),
        'keterangan'    => htmlspecialchars((string) $r['keterangan'], ENT_QUOTES),
        'status'        => $r['status'],
    );
}

echo json_encode(array('status' => 'success', 'data' => $data));
