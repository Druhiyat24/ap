<?php
// ============================================================================
// Data untuk modal SET OPENING BALANCE.
//   mode=temp  (default) : baris hasil upload yg belum disimpan (status Temp)
//   mode=post            : saldo awal yang SUDAH tersimpan (status Post)
// Ikut mengembalikan ringkasan (jumlah baris + total per mata uang + total IDR).
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ob_start();

include '../../../conn/conn.php';
require_once __DIR__ . '/saldo_awal_guard.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
ini_set('max_execution_time', 0);
header('Content-Type: application/json; charset=utf-8');

function sa_get_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$user = $_SESSION['username'] ?? '';
ppn_sa_guard_json($user);

$mode  = ($_REQUEST['mode'] ?? 'temp') === 'post' ? 'post' : 'temp';
$as_of = !empty($_REQUEST['as_of']) ? date('Y-m-d', strtotime($_REQUEST['as_of'])) : '2026-01-01';
$e     = function ($s) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $s); };

if ($mode === 'temp') {
    $where = "status = 'Temp' AND create_by = '" . $e($user) . "'";
} else {
    $where = "status = 'Post' AND as_of = '" . $e($as_of) . "'";
}

$q = mysqli_query($conn2, "SELECT id, si_no, si_date, supplier, faktur_pajak, tgl_faktur_pajak,
        profit_center, curr, rate, amount_ocy, amount_idr, as_of, create_by, create_date
    FROM tbl_ppn_saldo_awal WHERE $where
    ORDER BY supplier, faktur_pajak, si_date, id");
if ($q === false) { sa_get_out(['status' => 'error', 'message' => mysqli_error($conn2)]); }

$rows = [];
$tot_idr = 0.0;
$per_curr = [];
while ($r = mysqli_fetch_assoc($q)) {
    $tot_idr += (float) $r['amount_idr'];
    $c = $r['curr'] !== '' ? $r['curr'] : 'IDR';
    if (!isset($per_curr[$c])) { $per_curr[$c] = 0.0; }
    $per_curr[$c] += (float) $r['amount_ocy'];
    $rows[] = $r;
}

// Info saldo awal yang sedang tersimpan (Post) — ditampilkan di modal.
$posted = mysqli_fetch_assoc(mysqli_query($conn2,
    "SELECT COUNT(*) n, COALESCE(SUM(amount_idr), 0) idr, MAX(create_by) by_user, MAX(create_date) at_time
     FROM tbl_ppn_saldo_awal WHERE status = 'Post' AND as_of = '" . $e($as_of) . "'"));

// Save bersifat UPSERT: item yang sudah ada akan DIPERBARUI, bukan ditambah lagi.
// Di sini dihitung berapa item draft yang sudah ada di saldo awal tersimpan,
// supaya bisa diberitahukan di layar konfirmasi ("n item akan diperbarui").
// Kunci item sama dgn di save: nomor faktur, kalau kosong pakai nomor dokumen.
$dupe = 0;
if ($mode === 'temp') {
    $d = mysqli_fetch_assoc(mysqli_query($conn2,
        "SELECT COUNT(*) n FROM (
            SELECT DISTINCT COALESCE(NULLIF(t.faktur_pajak, ''), t.si_no) k
            FROM tbl_ppn_saldo_awal t
            INNER JOIN tbl_ppn_saldo_awal p
                    ON p.status = 'Post' AND p.as_of = '" . $e($as_of) . "'
                   AND COALESCE(NULLIF(p.faktur_pajak, ''), p.si_no)
                     = COALESCE(NULLIF(t.faktur_pajak, ''), t.si_no)
            WHERE t.status = 'Temp' AND t.create_by = '" . $e($user) . "'
        ) x"));
    $dupe = (int) ($d['n'] ?? 0);
}

sa_get_out([
    'status'   => 'success',
    'mode'     => $mode,
    'as_of'    => $as_of,
    'rows'     => $rows,
    'total'    => count($rows),
    'tot_idr'  => $tot_idr,
    'per_curr' => $per_curr,
    'posted'   => $posted,
    'dupe'     => $dupe,
]);
