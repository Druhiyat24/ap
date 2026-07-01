<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$no_kbon     = isset($_POST['no_kbon']) ? $_POST['no_kbon'] : '';
$cancel_user = isset($_POST['cancel_user']) ? $_POST['cancel_user'] : ($_SESSION['username'] ?? '');

if ($no_kbon === '') {
    echo 'Error: no_kbon kosong';
    exit;
}

$no_kbon_esc     = mysqli_real_escape_string($conn2, $no_kbon);
$cancel_user_esc = mysqli_real_escape_string($conn2, $cancel_user);

$sqlCheck = mysqli_query($conn2, "SELECT status FROM ap_saldo_payment_voucher WHERE no_kbon = '$no_kbon_esc' LIMIT 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: Data Saldo Awal tidak ditemukan';
    exit;
}

if (strtoupper($rowCheck['status']) === 'SECOND APPROVED') {
    echo 'Error: Data sudah Second Approved, tidak bisa dibatalkan';
    exit;
}

$update = mysqli_query($conn2, "UPDATE ap_saldo_payment_voucher
    SET status = 'Cancel',
        cancel_user = '$cancel_user_esc',
        cancel_date = NOW()
    WHERE no_kbon = '$no_kbon_esc'");

if (!$update) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

echo 'Data Berhasil Di Cancel';

mysqli_close($conn2);
?>
