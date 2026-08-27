<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

// Restricted page - only 'indro' may read this diagnostic data
$current_user = $_SESSION['username'] ?? '';
if ($current_user !== 'indro') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access restricted.']);
    exit;
}

// Rows whose stored debit_idr/credit_idr differ from debit*rate / credit*rate by more
// than this many rupiah are treated as a real conversion defect on that specific line.
// Journals whose total debit_idr/credit_idr mismatch is at or below MATERIALITY_THRESHOLD
// are pure multi-line rounding noise (native amounts carry 4 decimals, IDR only 2) and
// are excluded entirely - verified empirically: every confirmed rounding-only case tops
// out at Rp 0.97, every genuine defect starts at Rp 1.31+.
define('ROW_TOLERANCE', 0.5);
define('MATERIALITY_THRESHOLD', 1.0);

$start_date = trim($_POST['start_date'] ?? '');
$end_date   = trim($_POST['end_date'] ?? '');

$where = "status != 'Cancel'";
$params = [];
$types = '';
if ($start_date !== '' && $end_date !== '') {
    $where .= " AND tgl_journal BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
    $types .= 'ss';
}

$sql = "
    SELECT
        no_journal,
        MIN(tgl_journal) tgl_journal,
        GROUP_CONCAT(DISTINCT type_journal SEPARATOR ', ') types,
        GROUP_CONCAT(DISTINCT status SEPARATOR ', ') statuses,
        COUNT(*) line_count,
        COUNT(DISTINCT curr) ccnt,
        GROUP_CONCAT(DISTINCT curr SEPARATOR ',') currs,
        SUM(debit_idr) tot_debit_idr,
        SUM(credit_idr) tot_credit_idr,
        ROUND(SUM(debit_idr) - SUM(credit_idr), 2) selisih,
        SUM(debit) tot_debit_nat,
        SUM(credit) tot_credit_nat,
        SUM(CASE WHEN ABS(ROUND(IF(curr='IDR', debit, debit*rate),2) - debit_idr) > " . ROW_TOLERANCE . " THEN 1 ELSE 0 END) bad_debit_rows,
        SUM(CASE WHEN ABS(ROUND(IF(curr='IDR', credit, credit*rate),2) - credit_idr) > " . ROW_TOLERANCE . " THEN 1 ELSE 0 END) bad_credit_rows
    FROM tbl_list_journal
    WHERE $where
    GROUP BY no_journal
    HAVING ABS(ROUND(SUM(debit_idr) - SUM(credit_idr), 2)) > " . MATERIALITY_THRESHOLD . "
    ORDER BY ABS(ROUND(SUM(debit_idr) - SUM(credit_idr), 2)) DESC
";

if (count($params)) {
    $stmt = mysqli_prepare($conn2, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
} else {
    $res = mysqli_query($conn2, $sql);
}

if (!$res) {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
    exit;
}

$rows = [];
$needCurrencyCheck = [];
while ($r = mysqli_fetch_assoc($res)) {
    $rows[$r['no_journal']] = $r;
    if ($r['bad_debit_rows'] == 0 && $r['bad_credit_rows'] == 0) {
        $needCurrencyCheck[] = $r['no_journal'];
    }
}

// For journals with no single bad row, determine whether the native currency legs
// themselves fail to pair up (a genuinely missing/incomplete line) or whether native
// pairs up fine per currency but more than one rate was used for that currency
// (an inconsistent-rate defect that isn't localised to one row).
$incompleteSet = [];
if (count($needCurrencyCheck)) {
    $chunks = array_chunk($needCurrencyCheck, 500);
    foreach ($chunks as $chunk) {
        $placeholders = implode(',', array_fill(0, count($chunk), '?'));
        $csql = "
            SELECT no_journal FROM (
                SELECT no_journal, curr, SUM(debit) d, SUM(credit) c
                FROM tbl_list_journal
                WHERE status != 'Cancel' AND no_journal IN ($placeholders)
                GROUP BY no_journal, curr
            ) x
            GROUP BY no_journal
            HAVING SUM(ABS(d - c) > 0.01) > 0
        ";
        $cstmt = mysqli_prepare($conn2, $csql);
        $ctypes = str_repeat('s', count($chunk));
        mysqli_stmt_bind_param($cstmt, $ctypes, ...$chunk);
        mysqli_stmt_execute($cstmt);
        $cres = mysqli_stmt_get_result($cstmt);
        while ($crow = mysqli_fetch_assoc($cres)) {
            $incompleteSet[$crow['no_journal']] = true;
        }
    }
}

$out = [];
$summary = [
    'total' => 0,
    'total_selisih_abs' => 0,
    'rate_conversion' => 0,
    'incomplete' => 0,
    'rate_inconsistent' => 0,
];

foreach ($rows as $no => $r) {
    if ($r['bad_debit_rows'] > 0 || $r['bad_credit_rows'] > 0) {
        $diagnosis = 'salah_kurs_konversi';
        $diagnosis_label = 'Salah Kurs (Konversi Baris)';
    } elseif (isset($incompleteSet[$no])) {
        $diagnosis = 'tidak_lengkap';
        $diagnosis_label = 'Jurnal Tidak Lengkap';
    } else {
        $diagnosis = 'salah_kurs_rate';
        $diagnosis_label = 'Salah Kurs (Rate Tidak Konsisten)';
    }

    $r['diagnosis'] = $diagnosis;
    $r['diagnosis_label'] = $diagnosis_label;
    $out[] = $r;

    $summary['total']++;
    $summary['total_selisih_abs'] += abs($r['selisih']);
    if ($diagnosis === 'salah_kurs_konversi') $summary['rate_conversion']++;
    elseif ($diagnosis === 'tidak_lengkap') $summary['incomplete']++;
    else $summary['rate_inconsistent']++;
}

echo json_encode(['status' => 'success', 'summary' => $summary, 'data' => $out]);
