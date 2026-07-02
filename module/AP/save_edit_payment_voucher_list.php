<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');

$pl_number   = $_POST['pl_number'] ?? '';
$create_user = mysqli_real_escape_string($conn2, $_POST['create_user'] ?? '');
$removes     = json_decode($_POST['removes'] ?? '[]', true);
$adds        = json_decode($_POST['adds'] ?? '[]', true);

if (empty($pl_number)) {
    echo 'Error: pl_number kosong';
    exit;
}

$pl_esc = mysqli_real_escape_string($conn2, $pl_number);

// Verify PVL is still Draft
$check = mysqli_query($conn2, "SELECT status FROM pv_payment_voucher_list_h WHERE pl_number = '$pl_esc' LIMIT 1");
$row = mysqli_fetch_assoc($check);
if (!$row || $row['status'] !== 'Draft') {
    echo 'Error: Payment Voucher List bukan Draft, tidak bisa diedit';
    exit;
}

mysqli_begin_transaction($conn2);
try {
    $create_date = date('Y-m-d H:i:s');

    // Remove PVs
    foreach ($removes as $rm) {
        $rm_kbon = $rm['no_kbon'] ?? '';
        if (empty($rm_kbon)) continue;
        $rm_kbon_esc = mysqli_real_escape_string($conn2, $rm_kbon);
        if (!mysqli_query($conn2, "DELETE FROM pv_payment_voucher_list_det WHERE pl_number = '$pl_esc' AND no_kbon = '$rm_kbon_esc'")) {
            throw new Exception('Gagal menghapus PV ' . htmlspecialchars($rm_kbon) . ': ' . mysqli_error($conn2));
        }
    }

    // Add PVs
    foreach ($adds as $i => $row_data) {
        $type_pv       = mysqli_real_escape_string($conn2, $row_data['type_pv'] ?? '');
        $no_kbon       = mysqli_real_escape_string($conn2, $row_data['no_kbon'] ?? '');
        $tgl_raw       = $row_data['tgl_kbon'] ?? '';
        $tgl_kbon      = !empty($tgl_raw) ? "'" . date('Y-m-d', strtotime($tgl_raw)) . "'" : 'NULL';
        $nama_supp     = mysqli_real_escape_string($conn2, $row_data['nama_supp'] ?? '');
        $curr          = mysqli_real_escape_string($conn2, $row_data['curr'] ?? '');
        $total         = is_numeric($row_data['total'] ?? '') ? (float)$row_data['total'] : 0;
        $desk          = mysqli_real_escape_string($conn2, $row_data['deskripsi'] ?? '');
        $profit_center = mysqli_real_escape_string($conn2, $row_data['profit_center'] ?? '');

        $qd = "INSERT INTO pv_payment_voucher_list_det
               (pl_number, type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center, status, create_user, create_date)
               VALUES
               ('$pl_esc', '$type_pv', '$no_kbon', $tgl_kbon, '$nama_supp', '$curr', $total, '$desk', '$profit_center', 'Draft', '$create_user', '$create_date')";

        if (!mysqli_query($conn2, $qd)) {
            $label = htmlspecialchars($row_data['no_kbon'] ?? ('baris ke-' . ($i + 1)));
            throw new Exception('Gagal menambah PV ' . $label . ': ' . mysqli_error($conn2));
        }
    }

    mysqli_commit($conn2);

    // After commit: update status_pvl in source tables
    foreach ($removes as $rm) {
        if (!empty($rm['no_kbon'])) {
            updateStatusPvl($conn2, $rm['type_pv'] ?? '', $rm['no_kbon'], null);
        }
    }
    foreach ($adds as $row_data) {
        if (!empty($row_data['no_kbon'])) {
            updateStatusPvl($conn2, $row_data['type_pv'] ?? '', $row_data['no_kbon'], 'Draft');
        }
    }

    echo 'OK';

} catch (Exception $e) {
    mysqli_rollback($conn2);
    echo 'Error: ' . $e->getMessage();
}

mysqli_close($conn2);
