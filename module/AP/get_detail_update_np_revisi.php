<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$no_dok = $_GET['no_dok'] ?? '';
$no_dok_esc = mysqli_real_escape_string($conn1, $no_dok);

$header = null;
$items = [];

if (!empty($no_dok)) {
    $sqlHeader = mysqli_query($conn1, "SELECT no_dok, tgl_dok, total_row, total_success, total_failed, status, file_name, created_by, created_at
        FROM update_np_revisi_h WHERE no_dok = '$no_dok_esc' LIMIT 1");
    $header = mysqli_fetch_assoc($sqlHeader);

    if ($header) {
        $header['tgl_dok']     = !empty($header['tgl_dok']) ? date('d-M-Y', strtotime($header['tgl_dok'])) : '-';
        $header['created_at']  = !empty($header['created_at']) ? date('d-M-Y H:i:s', strtotime($header['created_at'])) : '-';
    }

    $sqlItems = mysqli_query($conn1, "SELECT baris, tipe, no_ref, no_barcode, curr_old, curr_new, price_old, price_new, status, keterangan
        FROM update_np_revisi_d WHERE no_dok = '$no_dok_esc' ORDER BY baris ASC, id ASC");

    while ($row = mysqli_fetch_assoc($sqlItems)) {
        $items[] = $row;
    }
}

echo json_encode(['header' => $header, 'items' => $items], JSON_INVALID_UTF8_SUBSTITUTE);
