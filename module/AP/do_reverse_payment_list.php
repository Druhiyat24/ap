<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$pl_number    = isset($_POST['pl_number']) ? trim($_POST['pl_number']) : '';
$reverse_user = $_SESSION['username'] ?? '';

if ($pl_number === '') {
    echo 'Error: pl_number kosong';
    exit;
}

if ($reverse_user === '') {
    echo 'Error: Session tidak valid, silahkan login ulang';
    exit;
}

$pl_number_esc = mysqli_real_escape_string($conn2, $pl_number);

// Cek status saat ini — hanya FIRST APPROVED atau SECOND APPROVED yang bisa di-reverse
$sqlCheck = mysqli_query($conn2, "SELECT status FROM pv_payment_list_h WHERE pl_number = '$pl_number_esc' LIMIT 1");
$rowCheck  = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: Payment List tidak ditemukan';
    exit;
}

if (!in_array($rowCheck['status'], ['FIRST APPROVED', 'SECOND APPROVED'])) {
    echo 'Error: Status saat ini "' . $rowCheck['status'] . '" tidak dapat di-reverse';
    exit;
}

// Reset header ke Draft, kosongkan kolom approval
$upd = mysqli_query($conn2, "UPDATE pv_payment_list_h
    SET status               = 'Draft',
        first_approve_user   = NULL,
        first_approve_date   = NULL,
        second_approve_user  = NULL,
        second_approve_date  = NULL
    WHERE pl_number = '$pl_number_esc'");

if (!$upd) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

// Reset status_pl di semua PV yang ada di dalam PL ini
$sqlDet = mysqli_query($conn2, "SELECT type_pv, no_kbon FROM pv_payment_list_det
    WHERE pl_number = '$pl_number_esc' AND status != 'Cancel'");

while ($rowDet = mysqli_fetch_assoc($sqlDet)) {
    updateStatusPl($conn2, $rowDet['type_pv'], $rowDet['no_kbon'], null);
}

echo 'Payment List ' . $pl_number . ' berhasil di-reverse ke Draft';

mysqli_close($conn2);
?>
