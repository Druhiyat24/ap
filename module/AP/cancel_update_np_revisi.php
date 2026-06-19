<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$no_dok = $_POST['no_dok'] ?? '';
$no_dok_esc = mysqli_real_escape_string($conn1, $no_dok);

if (empty($no_dok)) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data']);
    exit;
}

$check = mysqli_query($conn1, "SELECT status FROM update_np_revisi_h WHERE no_dok = '$no_dok_esc' LIMIT 1");
$header = mysqli_fetch_assoc($check);

if (!$header) {
    echo json_encode(['success' => false, 'message' => 'Data not found']);
    exit;
}

if ($header['status'] == 'Cancel') {
    echo json_encode(['success' => false, 'message' => 'Document already cancelled']);
    exit;
}

// Revert np_curr_rev / np_price_rev back to their pre-upload values for every row this document changed
$sqlItems = mysqli_query($conn1, "SELECT tipe, no_ref, no_barcode, curr_old, price_old FROM update_np_revisi_d WHERE no_dok = '$no_dok_esc' AND status = 'Success'");

while ($row = mysqli_fetch_assoc($sqlItems)) {
    $no_ref_esc     = mysqli_real_escape_string($conn1, $row['no_ref']);
    $no_barcode_esc = mysqli_real_escape_string($conn1, $row['no_barcode']);
    $curr_old_sql   = $row['curr_old'] !== null ? "'" . mysqli_real_escape_string($conn1, $row['curr_old']) . "'" : 'NULL';
    $price_old_sql  = $row['price_old'] !== null ? (float) $row['price_old'] : 'NULL';

    if ($row['tipe'] === 'OUT') {
        mysqli_query($conn1, "UPDATE whs_bppb_det SET np_curr_rev = $curr_old_sql, np_price_rev = $price_old_sql WHERE no_bppb = '$no_ref_esc' AND id_roll = '$no_barcode_esc'");
    } else {
        mysqli_query($conn1, "UPDATE whs_lokasi_inmaterial SET np_curr_rev = $curr_old_sql, np_price_rev = $price_old_sql WHERE no_dok = '$no_ref_esc' AND no_barcode = '$no_barcode_esc'");
    }
}

$update = mysqli_query($conn1, "UPDATE update_np_revisi_h SET status = 'Cancel' WHERE no_dok = '$no_dok_esc'");

if ($update) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update database']);
}
