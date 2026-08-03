<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

function bankOutValue($key, $default = 'ALL') {
    return isset($_POST[$key]) && trim($_POST[$key]) !== '' ? trim($_POST[$key]) : $default;
}

function bankOutEscape($conn, $value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$namaSupp = bankOutValue('nama_supp');
$bank = bankOutValue('bank');
$akun = bankOutValue('akun');
$status = bankOutValue('status');
$startDate = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$endDate = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');

$conditions = ["bankout_date BETWEEN '" . mysqli_real_escape_string($conn1, $startDate) . "' AND '" . mysqli_real_escape_string($conn1, $endDate) . "'"];
foreach (['nama_supp' => $namaSupp, 'bank' => $bank, 'akun' => $akun, 'status' => $status] as $column => $value) {
    if ($value !== 'ALL') {
        $conditions[] = $column . " = '" . mysqli_real_escape_string($conn1, $value) . "'";
    }
}

$where = 'WHERE ' . implode(' AND ', $conditions);
$query = "SELECT a.no_bankout, a.bankout_date, a.nama_supp, a.curr, a.amount, a.outstanding,
    IF(a.reff_doc = 'Payment', 'List Payment', a.reff_doc) AS reff_doc, a.akun, a.bank, a.status,
    IF(a.deskripsi = '', '-', a.deskripsi) AS deskripsi, a.approve_date,
    COALESCE(d.file_name, '') AS file_name, COALESCE(d.file_name_as, '') AS file_name_as
    FROM b_bankout_h a
    LEFT JOIN (
        SELECT no_bankout, GROUP_CONCAT(file_name SEPARATOR '|') AS file_name,
        GROUP_CONCAT(file_name_as SEPARATOR '|') AS file_name_as
        FROM b_bankout_dok WHERE status IS NULL GROUP BY no_bankout
    ) d ON d.no_bankout = a.no_bankout
    $where ORDER BY a.bankout_date DESC, a.no_bankout DESC";

$result = mysqli_query($conn1, $query);
$data = [];
$totalAmount = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $noBankout = $row['no_bankout'];
    $files = array_values(array_filter(explode('|', $row['file_name'])));
    $fileLabels = explode('|', $row['file_name_as']);
    $safeNoBankout = bankOutEscape($conn1, $noBankout);
    $action = '';

    if (strcasecmp($row['status'], 'Cancel') === 0) {
        $action = '<span class="text-danger font-weight-bold">Cancelled</span>';
    } else {
        $action = '<div class="kbon-action-buttons">';
        $action .= '<button type="button" class="btn btn-sm btn-info btnupdate" data-bank="' . $safeNoBankout . '" title="Upload document"><i class="fa fa-cloud-upload"></i></button>';
        $action .= '<a href="pdf_bankout.php?no_bankout=' . rawurlencode($noBankout) . '" target="_blank" class="btn btn-sm btn-danger" title="PDF Bank Out"><i class="fa fa-file-pdf-o"></i></a>';
        if (!empty($files)) {
            $action .= '<button type="button" class="btn btn-sm btn-primary btnshow" data-files="' . bankOutEscape($conn1, $row['file_name']) . '" data-labels="' . bankOutEscape($conn1, $row['file_name_as']) . '" title="Show documents"><i class="fa fa-eye"></i></button>';
            $action .= '<div class="dropdown d-inline"><button class="btn btn-sm btn-danger dropdown-toggle" type="button" data-toggle="dropdown" title="Delete document"><i class="fa fa-trash"></i></button><div class="dropdown-menu">';
            foreach ($files as $index => $file) {
                $action .= '<a class="dropdown-item text-danger delete-pdf-item" href="#" data-noreq="' . $safeNoBankout . '" data-filename="' . bankOutEscape($conn1, $file) . '"><i class="fa fa-trash"></i> Delete Document ' . ($index + 1) . '</a>';
            }
            $action .= '</div></div>';
        }
        if (strcasecmp($row['status'], 'Draft') === 0) {
            $editClass = $row['reff_doc'] === 'None' ? 'edit-none' : ($row['reff_doc'] === 'Payment Voucher' ? 'edit-pv' : ($row['reff_doc'] === 'List Payment' ? 'edit-lp' : ''));
            if ($editClass !== '') {
                $action .= '<button type="button" class="btn btn-sm btn-warning ' . $editClass . '" data-bank="' . $safeNoBankout . '" title="Edit"><i class="fa fa-pencil-square-o"></i></button>';
            }
            $action .= '<button type="button" class="btn btn-sm btn-danger btn-cancel-bankout" data-bank="' . $safeNoBankout . '" title="Cancel"><i class="fa fa-ban"></i></button>';
        }
        $action .= '</div>';
    }

    $totalAmount += (float) $row['amount'];
    $data[] = [
        'no_bankout' => $noBankout,
        'nama_supp' => $row['nama_supp'],
        'date' => !empty($row['bankout_date']) ? date('d-M-Y', strtotime($row['bankout_date'])) : '-',
        'curr' => $row['curr'],
        'amount' => number_format((float) $row['amount'], 2),
        'status' => $row['status'],
        'approve_date' => !empty($row['approve_date']) ? date('d-M-Y', strtotime($row['approve_date'])) : '-',
        'action' => $action,
        'reff_doc' => $row['reff_doc'],
        'akun' => $row['akun'],
        'bank' => $row['bank'],
        'deskripsi' => $row['deskripsi'],
    ];
}

echo json_encode(['data' => $data, 'footer' => ['amount' => number_format($totalAmount, 2)]]);
