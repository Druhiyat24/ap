<?php
// ============================================================================
// Upload file SALDO AWAL PPN Masukan -> staging tbl_ppn_saldo_awal (status Temp).
//
// Kolom file (urut, sesuai format-excel/format_saldo_awal_ppn.xls):
//   0 SI No | 1 SI Date | 2 Supplier | 3 No Faktur Pajak | 4 Tgl Faktur Pajak
//   5 Profit Center | 6 Currency | 7 Rate | 8 Amount OCY | 9 Eqv IDR
//
// Baris Temp milik user yang sama dihapus dulu, jadi upload ulang = ganti,
// bukan menumpuk. Semua di dalam satu transaksi (all-or-nothing).
// Akses dibatasi (lihat saldo_awal_guard.php).
// ============================================================================
ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');
ob_start();

require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;

include '../../../conn/conn.php';
require_once __DIR__ . '/saldo_awal_guard.php';
session_start();
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

function sa_out($a) { if (ob_get_level() > 0) { ob_end_clean(); } echo json_encode($a); exit; }

$user = $_SESSION['username'] ?? '';
ppn_sa_guard_json($user);

$as_of = !empty($_POST['as_of']) ? date('Y-m-d', strtotime($_POST['as_of'])) : '2026-01-01';

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    sa_out(['status' => 'error', 'message' => 'No file received. Please choose a file first.']);
}
$tmp = $_FILES['file']['tmp_name'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['xls', 'xlsx', 'csv'])) {
    sa_out(['status' => 'error', 'message' => 'Only .xls, .xlsx or .csv files are allowed.']);
}

// ---- angka: "1.055.000,00" / "(1,055,000.00)" / "1,055,000.00" -> float ----
function sa_num($v) {
    if ($v === null || $v === '') { return 0.0; }
    if (is_numeric($v)) { return (float) $v; }
    $s = trim((string) $v);
    $neg = (strpos($s, '(') !== false && strpos($s, ')') !== false) || strpos($s, '-') === 0;
    $s = preg_replace('/[^0-9,.\-]/', '', $s);
    $lastC = strrpos($s, ','); $lastD = strrpos($s, '.');
    if ($lastC !== false && $lastD !== false) {
        if ($lastC > $lastD) { $s = str_replace('.', '', $s); $s = str_replace(',', '.', $s); }
        else { $s = str_replace(',', '', $s); }
    } elseif ($lastC !== false) {
        $s = (strlen($s) - $lastC - 1) <= 2 ? str_replace(',', '.', $s) : str_replace(',', '', $s);
    }
    $s = str_replace('-', '', $s);
    $f = (float) $s;
    return $neg ? -$f : $f;
}

// ---- tanggal: serial Excel / ISO / dd-mm-yyyy -> Y-m-d ('' kalau kosong) ----
function sa_date($v) {
    if ($v === null || $v === '') { return ''; }
    if (is_numeric($v) && $v > 0) {
        try { return XlsDate::excelToDateTimeObject((float) $v)->format('Y-m-d'); } catch (Exception $e) { return ''; }
    }
    $s = trim((string) $v);
    if ($s === '' || $s === '-') { return ''; }
    $t = strtotime($s);
    return $t ? date('Y-m-d', $t) : '';
}

try {
    $sheet = IOFactory::load($tmp)->getActiveSheet();
    $rows  = $sheet->toArray(null, true, false, false);
} catch (Exception $e) {
    sa_out(['status' => 'error', 'message' => 'Failed to read the file: ' . $e->getMessage()]);
}
if (count($rows) < 2) { sa_out(['status' => 'error', 'message' => 'The file has no data rows.']); }

$e  = function ($s) use ($conn2) { return mysqli_real_escape_string($conn2, (string) $s); };
$eu = $e($user);
$ea = $e($as_of);
$now = date('Y-m-d H:i:s');

mysqli_query($conn2, "SET autocommit = 0");
mysqli_query($conn2, "START TRANSACTION");

if (mysqli_query($conn2, "DELETE FROM tbl_ppn_saldo_awal WHERE create_by = '$eu' AND status = 'Temp'") === false) {
    mysqli_query($conn2, "ROLLBACK");
    sa_out(['status' => 'error', 'message' => 'Failed to clear previous draft: ' . mysqli_error($conn2)]);
}

$head   = "INSERT INTO tbl_ppn_saldo_awal
    (si_no, si_date, supplier, faktur_pajak, tgl_faktur_pajak, profit_center, curr, rate,
     amount_ocy, amount_idr, as_of, status, create_by, create_date) VALUES ";
$vals   = [];
$n      = 0;
$skip   = 0;
$CHUNK  = 500;

$flush = function () use (&$vals, $head, $conn2) {
    if (!$vals) { return true; }
    $ok = mysqli_query($conn2, $head . implode(',', $vals));
    $vals = [];
    return $ok !== false;
};

for ($i = 1; $i < count($rows); $i++) {
    $r = $rows[$i];
    $si   = trim((string) ($r[0] ?? ''));
    $supp = trim((string) ($r[2] ?? ''));
    $fk   = trim((string) ($r[3] ?? ''));
    $ocy  = sa_num($r[8] ?? 0);
    $idr  = sa_num($r[9] ?? 0);
    // baris kosong / baris judul ulang -> lewati
    if ($si === '' && $fk === '' && $supp === '' && $ocy == 0 && $idr == 0) { $skip++; continue; }

    $rate = sa_num($r[7] ?? 0);
    if ($rate <= 0) { $rate = 1; }
    if ($idr == 0 && $ocy != 0) { $idr = $ocy * $rate; }   // Eqv IDR boleh dikosongkan
    $curr = strtoupper(trim((string) ($r[6] ?? ''))); if ($curr === '') { $curr = 'IDR'; }

    $sd  = sa_date($r[1] ?? '');
    $tfp = sa_date($r[4] ?? '');

    $vals[] = "('" . $e($si) . "', " . ($sd !== '' ? "'" . $e($sd) . "'" : 'NULL') . ", '" . $e($supp) . "',
        '" . $e($fk) . "', " . ($tfp !== '' ? "'" . $e($tfp) . "'" : 'NULL') . ", '" . $e(trim((string) ($r[5] ?? ''))) . "',
        '" . $e($curr) . "', " . (float) $rate . ", " . (float) $ocy . ", " . (float) $idr . ",
        '$ea', 'Temp', '$eu', '" . $e($now) . "')";
    $n++;
    if (count($vals) >= $CHUNK && !$flush()) {
        mysqli_query($conn2, "ROLLBACK");
        sa_out(['status' => 'error', 'message' => 'Failed to save row ' . ($i + 1) . ': ' . mysqli_error($conn2)]);
    }
}
if (!$flush()) {
    mysqli_query($conn2, "ROLLBACK");
    sa_out(['status' => 'error', 'message' => 'Failed to save data: ' . mysqli_error($conn2)]);
}
if ($n === 0) {
    mysqli_query($conn2, "ROLLBACK");
    sa_out(['status' => 'error', 'message' => 'No valid data row found in the file.']);
}
mysqli_query($conn2, "COMMIT");
mysqli_query($conn2, "SET autocommit = 1");

sa_out(['status' => 'success', 'baris' => $n, 'skip' => $skip, 'as_of' => $as_of]);
