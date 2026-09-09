<?php
// ============================================================================
// ajx_get_data_ppn.php — data preview tab "PPN Masukan" (baris staging milik
// user yang login). Bentuk barisnya dihitung oleh ppn_build_lines() — helper
// yang SAMA dipakai save_mj_ppn.php, jadi preview = hasil simpan.
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../../conn/conn.php';
require_once __DIR__ . '/ppn_masukan_lines.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

function ppn_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); }

$user = $_SESSION['username'] ?? 'system';
// Profit Center tidak lagi dikirim dari form — nilainya sudah ada di tiap baris
// staging (dibaca dari kolom terakhir file saat upload).
$res  = ppn_build_lines($conn2, $user, 'Temp');

if ($res === false) { ppn_out(['status' => 'error', 'message' => 'Failed to read uploaded data.']); exit; }

ppn_out([
    'status'     => 'success',
    'detail'     => $res['detail'],
    'grouped'    => $res['grouped'],
    'per_pc'     => $res['per_pc'],
    'lines'      => $res['lines'],
    'faktur'     => $res['faktur'],
    'tot_debit'  => $res['tot_debit'],
    'tot_credit' => $res['tot_credit'],
    'balance'    => $res['balance'],
]);
