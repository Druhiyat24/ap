<?php
header('Content-Type: application/json');
include '../../conn/conn.php';

set_time_limit(0);

$result = mysqli_query($conn1, 'CALL whs_update_out_barcode()');

if ($result === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Update Barcode Out gagal: ' . mysqli_error($conn1)
    ]);
    exit;
}

// Bersihkan seluruh result set dari stored procedure sebelum koneksi dipakai lagi.
do {
    if ($result instanceof mysqli_result) {
        mysqli_free_result($result);
    }
} while (mysqli_more_results($conn1) && mysqli_next_result($conn1));

echo json_encode([
    'status' => 'ok',
    'message' => 'Barcode Out berhasil diperbarui.'
]);
