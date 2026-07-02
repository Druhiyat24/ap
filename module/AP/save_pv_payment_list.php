<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');

$pl_date      = date('Y-m-d', strtotime($_POST['pl_date'] ?? 'now'));
$from_account = mysqli_real_escape_string($conn2, $_POST['from_account'] ?? '');
$deskripsi    = mysqli_real_escape_string($conn2, $_POST['deskripsi'] ?? '');
$create_user  = mysqli_real_escape_string($conn2, $_POST['create_user'] ?? '');
$create_date  = date('Y-m-d H:i:s');

$rows = isset($_POST['rows']) ? json_decode($_POST['rows'], true) : null;

if (empty($from_account)) { echo 'Error: from_account kosong'; exit; }
if (!is_array($rows) || count($rows) === 0) { echo 'Error: tidak ada baris dipilih'; exit; }

mysqli_begin_transaction($conn2);

try {
    // Generate nomor di dalam transaksi dengan FOR UPDATE agar aman jika banyak user save bersamaan
    $prefix     = 'PL-PV/' . date('my') . '/';
    $prefix_esc = mysqli_real_escape_string($conn2, $prefix);

    $sqlNum = mysqli_query($conn2,
        "SELECT COALESCE(MAX(CAST(RIGHT(pl_number, 5) AS UNSIGNED)), 0) + 1 AS next_seq
         FROM pv_payment_list_h
         WHERE pl_number LIKE '{$prefix_esc}%' FOR UPDATE"
    );
    $rowNum    = mysqli_fetch_assoc($sqlNum);
    $pl_number = $prefix . str_pad((int)$rowNum['next_seq'], 5, '0', STR_PAD_LEFT);
    $pl_esc    = mysqli_real_escape_string($conn2, $pl_number);

    // Insert header
    $qh = "INSERT INTO pv_payment_list_h (pl_number, pl_date, deskripsi, from_account, status, created_by, created_date)
            VALUES ('$pl_esc', '$pl_date', '$deskripsi', '$from_account', 'Draft', '$create_user', '$create_date')";
    if (!mysqli_query($conn2, $qh)) {
        throw new Exception('Gagal insert header: ' . mysqli_error($conn2));
    }

    // Insert setiap baris detail
    foreach ($rows as $i => $row) {
        $type_pv       = mysqli_real_escape_string($conn2, $row['type_pv'] ?? '');
        $no_kbon       = mysqli_real_escape_string($conn2, $row['no_kbon'] ?? '');
        $tgl_kbon      = !empty($row['tgl_kbon']) ? "'" . date('Y-m-d', strtotime($row['tgl_kbon'])) . "'" : 'NULL';
        $nama_supp     = mysqli_real_escape_string($conn2, $row['nama_supp'] ?? '');
        $curr          = mysqli_real_escape_string($conn2, $row['curr'] ?? '');
        $total         = is_numeric($row['total'] ?? '') ? (float)$row['total'] : 0;
        $desk_row      = mysqli_real_escape_string($conn2, $row['deskripsi'] ?? '');
        $profit_center = mysqli_real_escape_string($conn2, $row['profit_center'] ?? '');
        $row_user      = mysqli_real_escape_string($conn2, $row['create_user'] ?? $create_user);

        $qd = "INSERT INTO pv_payment_list_det
               (pl_number, type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center, status, create_user, create_date)
               VALUES
               ('$pl_esc', '$type_pv', '$no_kbon', $tgl_kbon, '$nama_supp', '$curr', '$total', '$desk_row', '$profit_center', 'Draft', '$row_user', '$create_date')";

        if (!mysqli_query($conn2, $qd)) {
            $label = htmlspecialchars($row['no_kbon'] ?? ('baris ke-' . ($i + 1)));
            throw new Exception('Gagal insert PV ' . $label . ': ' . mysqli_error($conn2));
        }
    }

    mysqli_commit($conn2);

    // Update status_pl untuk setiap PV setelah commit
    foreach ($rows as $row) {
        updateStatusPl($conn2, $row['type_pv'], $row['no_kbon'], 'Draft');
    }

    // Return nomor aktual agar client bisa tampilkan di Swal sukses
    echo 'OK:' . $pl_number;

} catch (Exception $e) {
    mysqli_rollback($conn2);
    echo 'Error: ' . $e->getMessage();
}

mysqli_close($conn2);
