<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon'] : '';
$approve_user = $_SESSION['username'] ?? '';
$second_approve_date = date("Y-m-d H:i:s");
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

if ($no_kbon === '') {
    echo 'Error: no_kbon kosong';
    exit;
}

if ($approve_user === '') {
    echo 'Error: Session tidak valid, silahkan login ulang';
    exit;
}

// $querys = mysqli_query($conn1, "select finance, ap_apprv_kb2 from userpassword where username = '" . mysqli_real_escape_string($conn1, $approve_user) . "'");
// $rs = mysqli_fetch_assoc($querys);
$fin = '1';
$app2 = '1';

if ($fin != '1' || $app2 != '1') {
    echo 'Error: Anda tidak memiliki akses untuk Second Approval';
    exit;
}

$no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

$sqlCheck = mysqli_query($conn2, "select status from kontrabon_h_dp where no_kbon = '$no_kbon_esc' limit 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: Payment Voucher DP tidak ditemukan';
    exit;
}

if ($rowCheck['status'] !== 'FIRST APPROVED') {
    echo 'Error: Status saat ini bukan FIRST APPROVED, tidak bisa dilakukan Second Approval';
    exit;
}

$update1 = mysqli_query($conn2, "update kontrabon_dp set status = 'SECOND APPROVED', confirm_user = '$approve_user_esc', confirm_date = '$second_approve_date' where no_kbon = '$no_kbon_esc'");
if (!$update1) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

$update2 = mysqli_query($conn2, "update kontrabon_h_dp set status = 'SECOND APPROVED', confirm_user = '$approve_user_esc', confirm_date = '$second_approve_date', second_approve_user = '$approve_user_esc', second_approve_date = '$second_approve_date' where no_kbon = '$no_kbon_esc'");
if (!$update2) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

echo 'Data Berhasil Di Second Approve';

mysqli_close($conn2);
?>
