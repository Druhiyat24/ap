<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
session_start();

$user = $_SESSION['username'] ?? '';

$status     = isset($_POST['status']) ? $_POST['status'] : 'ALL';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d', strtotime('-1 month'));
$end_date   = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');

$user_esc = mysqli_real_escape_string($conn2, $user);

$where = "pl_date BETWEEN '" . mysqli_real_escape_string($conn2, $start_date) . "' AND '" . mysqli_real_escape_string($conn2, $end_date) . "'";
if ($status !== 'ALL' && $status !== '') {
    $where .= " AND status = '" . mysqli_real_escape_string($conn2, $status) . "'";
}

$sql = mysqli_query($conn2, "select pl_number, pl_date, deskripsi, status, created_by, created_date from pv_payment_voucher_list_h where $where order by created_date desc");

$data = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $rowStatus = $row['status'];
    $plNumber = $row['pl_number'];

    $btnShow = '<button type="button" class="btn btn-sm btn-outline-warning btn-show-pl" data-pl="' . htmlspecialchars($plNumber) . '" title="Show"><i class="fas fa-eye"></i> Show</button>';
    $btnPdf = '<a href="pdf_payment_voucher_list.php?pl_number=' . htmlspecialchars(rawurlencode($plNumber)) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF"><i class="fa fa-file-pdf-o"></i> PDF</a>';
    $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-pl" data-pl="' . htmlspecialchars($plNumber) . '" title="Cancel"><i class="fas fa-times"></i> Cancel</button>';

    // Approve (satu tahap) dilakukan lewat halaman approval khusus
    // (approve-payment-voucher-list.php) - sama seperti pola Payment List.
    $action = $btnShow . $btnPdf;

    if (strcasecmp($rowStatus, 'Draft') === 0) {
        $action .= $btnCancel;
    }

    $action = '<div class="kbon-action-buttons">' . $action . '</div>';

    $data[] = [
        'pl_number'    => $plNumber,
        'pl_date'      => !empty($row['pl_date']) ? date('d-M-Y', strtotime($row['pl_date'])) : '-',
        'deskripsi'    => $row['deskripsi'],
        'status'       => $rowStatus,
        'created_by'   => $row['created_by'],
        'created_date' => !empty($row['created_date']) ? date('d-M-Y H:i:s', strtotime($row['created_date'])) : '-',
        'action'       => $action,
    ];
}

echo json_encode(['data' => $data]);
