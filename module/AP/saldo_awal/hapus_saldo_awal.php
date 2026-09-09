<?php
// ============================================================================
// Hapus baris SALDO AWAL.
//   mode=row  + id      -> hapus SATU baris
//   mode=all  + as_of   -> hapus SEMUA baris pada tanggal saldo awal tsb
//   scope     = post (default, data tersimpan) | temp (draft hasil upload user)
//
// Save bersifat menambah, jadi menu ini yang dipakai untuk membetulkan kalau
// ada baris salah atau terlanjur dobel. Akses dibatasi (saldo_awal_guard.php).
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../../conn/conn.php';
require_once __DIR__ . '/saldo_awal_guard.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

function sa_del_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$user = $_SESSION['username'] ?? '';
ppn_sa_guard_json($user);

$mode  = ($_POST['mode'] ?? 'row') === 'all' ? 'all' : 'row';
$scope = ($_POST['scope'] ?? 'post') === 'temp' ? 'Temp' : 'Post';
$as_of = !empty($_POST['as_of']) ? date('Y-m-d', strtotime($_POST['as_of'])) : '2026-01-01';
$e     = function ($s) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $s); };

if ($mode === 'row') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) { sa_del_out(['status' => 'error', 'message' => 'Invalid row id.']); }
    $where = "id = $id";
} else {
    // draft: hanya milik user sendiri. tersimpan: seluruh baris pada as_of tsb.
    $where = ($scope === 'Temp')
        ? "status = 'Temp' AND create_by = '" . $e($user) . "'"
        : "status = 'Post' AND as_of = '" . $e($as_of) . "'";
}

$cek = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT COUNT(*) n FROM tbl_ppn_saldo_awal WHERE $where"));
$n   = (int) ($cek['n'] ?? 0);
if ($n === 0) { sa_del_out(['status' => 'error', 'message' => 'Nothing to delete.']); }

if (mysqli_query($conn2, "DELETE FROM tbl_ppn_saldo_awal WHERE $where") === false) {
    sa_del_out(['status' => 'error', 'message' => 'Failed to delete: ' . mysqli_error($conn2)]);
}
$hapus = mysqli_affected_rows($conn2);

$sum = mysqli_fetch_assoc(mysqli_query($conn2,
    "SELECT COUNT(*) n, COALESCE(SUM(amount_idr), 0) idr FROM tbl_ppn_saldo_awal
     WHERE status = 'Post' AND as_of = '" . $e($as_of) . "'"));

sa_del_out([
    'status'  => 'success',
    'hapus'   => $hapus,
    'total'   => (int) ($sum['n'] ?? 0),
    'tot_idr' => (float) ($sum['idr'] ?? 0),
]);
