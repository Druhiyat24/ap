<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

$current_user = $_SESSION['username'] ?? '';
if ($current_user !== 'indro') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access restricted.']);
    exit;
}

define('ROW_TOLERANCE', 0.5);

$no_journal = trim($_POST['no_journal'] ?? '');
if ($no_journal === '') {
    echo json_encode(['status' => 'error', 'message' => 'no_journal required.']);
    exit;
}

$stmt = mysqli_prepare($conn2, "
    SELECT id, no_journal, tgl_journal, type_journal, no_coa, nama_coa, no_costcenter,
           nama_costcenter, reff_doc, reff_date, curr, rate, debit, credit, debit_idr,
           credit_idr, status, keterangan, create_by, create_date
    FROM tbl_list_journal
    WHERE no_journal = ? AND status != 'Cancel'
    ORDER BY id ASC
");
mysqli_stmt_bind_param($stmt, 's', $no_journal);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$lines = [];
$sum_d = 0; $sum_c = 0; $sum_di = 0; $sum_ci = 0;
while ($row = mysqli_fetch_assoc($res)) {
    // IDR-denominated lines should always carry rate=1 (debit_idr == debit); a line
    // with curr=IDR but a non-1 rate stored is itself the defect, so the expected
    // value must ignore that stray rate rather than compound it.
    $expected_debit_idr  = $row['curr'] === 'IDR' ? round($row['debit'], 2)  : round($row['debit'] * $row['rate'], 2);
    $expected_credit_idr = $row['curr'] === 'IDR' ? round($row['credit'], 2) : round($row['credit'] * $row['rate'], 2);
    $row['expected_debit_idr']  = $expected_debit_idr;
    $row['expected_credit_idr'] = $expected_credit_idr;
    $row['debit_idr_mismatch']  = abs($expected_debit_idr - $row['debit_idr']) > ROW_TOLERANCE;
    $row['credit_idr_mismatch'] = abs($expected_credit_idr - $row['credit_idr']) > ROW_TOLERANCE;
    $sum_d  += $row['debit'];
    $sum_c  += $row['credit'];
    $sum_di += $row['debit_idr'];
    $sum_ci += $row['credit_idr'];
    $lines[] = $row;
}

echo json_encode([
    'status' => 'success',
    'no_journal' => $no_journal,
    'lines' => $lines,
    'totals' => [
        'debit' => $sum_d,
        'credit' => $sum_c,
        'debit_idr' => $sum_di,
        'credit_idr' => $sum_ci,
        'selisih_idr' => round($sum_di - $sum_ci, 2),
        'selisih_nat' => round($sum_d - $sum_c, 4),
    ],
]);
