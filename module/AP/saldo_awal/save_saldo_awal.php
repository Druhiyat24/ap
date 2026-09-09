<?php
// ============================================================================
// Simpan SALDO AWAL: baris Temp milik user -> Post, secara UPSERT.
//
//   - item yang SUDAH ada di saldo awal  -> DIPERBARUI (bukan ditambah lagi)
//   - item baru                          -> ditambahkan
//   - item lama yang tidak ada di file    -> DIBIARKAN (jadi bisa upload bertahap)
//
// Jadi mengupload file yang sama dua kali TIDAK menggandakan saldo; malah kalau
// sebelumnya sempat dobel, upload ulang merapikannya jadi satu baris.
//
// KUNCI ITEM: nomor faktur pajak; kalau kosong dipakai nomor dokumen (si_no).
// Sama dengan kunci yang dipakai report, supaya saldo awal & mutasi ketemu.
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ini_set('max_execution_time', 0);
ob_start();

include '../../../conn/conn.php';
require_once __DIR__ . '/saldo_awal_guard.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

function sa_save_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$user = $_SESSION['username'] ?? '';
ppn_sa_guard_json($user);

$as_of = !empty($_POST['as_of']) ? date('Y-m-d', strtotime($_POST['as_of'])) : '2026-01-01';
$e  = function ($s) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $s); };
$eu = $e($user);
$ea = $e($as_of);

$cek = mysqli_fetch_assoc(mysqli_query($conn2,
    "SELECT COUNT(*) n FROM tbl_ppn_saldo_awal WHERE status = 'Temp' AND create_by = '$eu'"));
if (!$cek || (int) $cek['n'] === 0) {
    sa_save_out(['status' => 'error', 'message' => 'There is no uploaded data to save.']);
}

mysqli_query($conn2, "SET autocommit = 0");
mysqli_query($conn2, "START TRANSACTION");

// 1) Buang baris Post yang ITEM-nya juga ada di draft -> efek "update", sekaligus
//    merapikan kalau sebelumnya sempat tersimpan dobel.
$joinKey = "COALESCE(NULLIF(t.faktur_pajak, ''), t.si_no) = COALESCE(NULLIF(p.faktur_pajak, ''), p.si_no)";
$cntUpd = mysqli_fetch_assoc(mysqli_query($conn2,
    "SELECT COUNT(*) n FROM tbl_ppn_saldo_awal p
     INNER JOIN tbl_ppn_saldo_awal t
             ON t.status = 'Temp' AND t.create_by = '$eu' AND $joinKey
     WHERE p.status = 'Post' AND p.as_of = '$ea'"));
if (mysqli_query($conn2,
    "DELETE p FROM tbl_ppn_saldo_awal p
     INNER JOIN tbl_ppn_saldo_awal t
             ON t.status = 'Temp' AND t.create_by = '$eu' AND $joinKey
     WHERE p.status = 'Post' AND p.as_of = '$ea'") === false) {
    mysqli_query($conn2, "ROLLBACK");
    sa_save_out(['status' => 'error', 'message' => 'Failed to update existing rows: ' . mysqli_error($conn2)]);
}
$ditimpa = mysqli_affected_rows($conn2);

// 2) Temp -> Post
if (mysqli_query($conn2, "UPDATE tbl_ppn_saldo_awal SET status = 'Post', as_of = '$ea',
        create_date = '" . $e(date('Y-m-d H:i:s')) . "'
     WHERE status = 'Temp' AND create_by = '$eu'") === false) {
    mysqli_query($conn2, "ROLLBACK");
    sa_save_out(['status' => 'error', 'message' => 'Failed to save: ' . mysqli_error($conn2)]);
}
$saved = mysqli_affected_rows($conn2);

mysqli_query($conn2, "COMMIT");
mysqli_query($conn2, "SET autocommit = 1");

$sum = mysqli_fetch_assoc(mysqli_query($conn2,
    "SELECT COUNT(*) n, COALESCE(SUM(amount_idr), 0) idr FROM tbl_ppn_saldo_awal
     WHERE status = 'Post' AND as_of = '$ea'"));

sa_save_out([
    'status'   => 'success',
    'baris'    => $saved,                          // total baris dari file yang disimpan
    'update'   => (int) ($cntUpd['n'] ?? 0),       // item lama yang diperbarui
    'ditimpa'  => $ditimpa,                        // baris lama yang digantikan (bisa > update kalau sempat dobel)
    'total'    => (int) ($sum['n'] ?? 0),          // total baris saldo awal sekarang
    'as_of'    => $as_of,
    'tot_idr'  => (float) ($sum['idr'] ?? 0),
]);
