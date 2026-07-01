<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$no_kbon      = isset($_POST['no_kbon']) ? $_POST['no_kbon'] : '';
$approve_user = $_SESSION['username'] ?? '';
$approve_date = date("Y-m-d H:i:s");

if ($no_kbon === '') {
    echo 'Error: no_kbon kosong';
    exit;
}

if ($approve_user === '') {
    echo 'Error: Session tidak valid, silahkan login ulang';
    exit;
}

$no_kbon_esc      = mysqli_real_escape_string($conn2, $no_kbon);
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

$sqlCheck = mysqli_query($conn2, "SELECT status FROM ap_saldo_payment_voucher WHERE no_kbon = '$no_kbon_esc' LIMIT 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: Data Saldo Awal tidak ditemukan';
    exit;
}

if (strtolower($rowCheck['status']) !== 'draft') {
    echo 'Error: Status saat ini bukan draft, tidak bisa dilakukan First Approval';
    exit;
}

$update = mysqli_query($conn2, "UPDATE ap_saldo_payment_voucher
    SET status = 'FIRST APPROVED',
        first_approve_user = '$approve_user_esc',
        first_approve_date = '$approve_date'
    WHERE no_kbon = '$no_kbon_esc'");

if (!$update) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

echo 'Data Berhasil Di First Approve';

mysqli_close($conn2);
?>
