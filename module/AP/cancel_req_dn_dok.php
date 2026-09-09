<?php
// ============================================================================
// Hapus (soft-delete) SATU dokumen lampiran req_dn_dok.
// Dulu di-target lewat no_req saja (update ... where no_req = ...) — itu cocok
// waktu cuma boleh 1 dokumen per request, tapi sekarang 1 request bisa punya
// banyak dokumen sekaligus, jadi harus di-target per baris (id) supaya menghapus
// satu dokumen tidak ikut menghapus dokumen lain milik request yang sama.
// ============================================================================
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

$id          = (int) ($_POST['id'] ?? 0);
$cancel_user = trim($_POST['cancel_user'] ?? '');

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid document id.']);
    exit;
}

$e = function ($v) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $v); };
$sql = "update req_dn_dok set cancel_date = '" . date('Y-m-d H:i:s') . "', cancel_by = '" . $e($cancel_user) . "', status = 'CANCEL' where id = " . $id;
$ok  = mysqli_query($conn2, $sql);

echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $ok ? '' : mysqli_error($conn2)]);
