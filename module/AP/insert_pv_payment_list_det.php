<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');

$create_date = date('Y-m-d H:i:s');

// Bulk mode: rows sent as JSON array
$rows = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : null;

if (!is_array($rows) || count($rows) === 0) {
    echo 'Error: no rows received';
    exit;
}

$errors = [];

foreach ($rows as $i => $row) {
    $pl_number     = mysqli_real_escape_string($conn2, $row['pl_number'] ?? '');
    $type_pv       = mysqli_real_escape_string($conn2, $row['type_pv'] ?? '');
    $no_kbon       = mysqli_real_escape_string($conn2, $row['no_kbon'] ?? '');
    $tgl_kbon      = !empty($row['tgl_kbon']) ? "'" . date('Y-m-d', strtotime($row['tgl_kbon'])) . "'" : 'NULL';
    $nama_supp     = mysqli_real_escape_string($conn2, $row['nama_supp'] ?? '');
    $curr          = mysqli_real_escape_string($conn2, $row['curr'] ?? '');
    $total         = is_numeric($row['total'] ?? '') ? (float)$row['total'] : 0;
    $deskripsi     = mysqli_real_escape_string($conn2, $row['deskripsi'] ?? '');
    $profit_center = mysqli_real_escape_string($conn2, $row['profit_center'] ?? '');
    $create_user   = mysqli_real_escape_string($conn2, $row['create_user'] ?? '');

    $query = "INSERT INTO pv_payment_list_det
        (pl_number, type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center, status, create_user, create_date)
        VALUES
        ('$pl_number', '$type_pv', '$no_kbon', $tgl_kbon, '$nama_supp', '$curr', '$total', '$deskripsi', '$profit_center', 'Draft', '$create_user', '$create_date')";

    $execute = mysqli_query($conn2, $query);

    if ($execute) {
        updateStatusPl($conn2, $row['type_pv'], $row['no_kbon'], 'Draft');
    } else {
        $errors[] = 'Row ' . ($i + 1) . ' (' . htmlspecialchars($row['no_kbon'] ?? '') . '): ' . mysqli_error($conn2);
    }
}

mysqli_close($conn2);

if (count($errors) > 0) {
    echo 'Error: ' . implode('; ', $errors);
} else {
    echo 'OK';
}
