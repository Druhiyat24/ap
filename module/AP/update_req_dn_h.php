<?php
// ============================================================================
// Update header Request Debit Note dari edit_request_dn.php.
// BEDA dgn insert_req_dn_h.php (dipakai saat CREATE, selalu INSERT baris baru
// dgn no_req baru): endpoint ini UPDATE baris header yg sudah ada, no_req TETAP
// SAMA, dan SUPPLIER SENGAJA TIDAK ADA di daftar kolom yang di-update — supplier
// tidak boleh diubah lewat form Edit sama sekali.
//
// Guard "status = 'Post'" di WHERE brjaga2 kalau ada tab lama yg masih terbuka
// setelah status request berubah di tempat lain (mis. sudah diproses jadi Debit
// Note) — supaya tidak ada update yg lolos ke request yg semestinya sudah
// terkunci.
// ============================================================================
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

$no_req       = trim($_POST['no_req'] ?? '');
$unik_code    = trim($_POST['unik_code'] ?? '');
$tgl_req      = !empty($_POST['tgl_req']) ? date('Y-m-d', strtotime($_POST['tgl_req'])) : null;
$total_amount = $_POST['total_amount'] ?? 0;
$deskripsi    = $_POST['deskripsi'] ?? '';

if ($no_req === '' || $unik_code === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing no_req/unik_code.']);
    exit;
}

$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };

$sql = "update req_dn_h set tgl_req = '" . $e($tgl_req) . "', total_amount = '" . $e($total_amount) . "', deskripsi = '" . $e($deskripsi) . "'
    where no_req = '" . $e($no_req) . "' and unik_code = '" . $e($unik_code) . "' and status = 'Post'";
$ok = mysqli_query($conn2, $sql);

if (!$ok) {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
    exit;
}

if (mysqli_affected_rows($conn2) === 0) {
    // affected_rows 0 bisa berarti "tidak ada nilai yg benar2 berubah" (bukan
    // error) ATAU barisnya memang tidak ketemu/statusnya sudah bukan Post lagi
    // — cek dulu keberadaan & status barisnya sebelum menyimpulkan gagal.
    $chk = mysqli_query($conn2, "select status from req_dn_h where no_req = '" . $e($no_req) . "' and unik_code = '" . $e($unik_code) . "'");
    $row = $chk ? mysqli_fetch_assoc($chk) : null;
    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Request not found.']);
        exit;
    }
    if ($row['status'] !== 'Post') {
        echo json_encode(['status' => 'error', 'message' => 'This request can no longer be edited (status: ' . $row['status'] . ').']);
        exit;
    }
}

echo json_encode(['status' => 'success']);
