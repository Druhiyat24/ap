<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$no_kbon = isset($_POST['no_kbon']) ? $_POST['no_kbon'] : '';
$approve_user = $_SESSION['username'] ?? '';
$first_approve_date = date("Y-m-d H:i:s");

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
$app = '1';


if ($fin != '1' || $app != '1') {
    echo 'Error: Anda tidak memiliki akses untuk First Approval';
    exit;
}

$no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

$sqlCheck = mysqli_query($conn2, "select status from kontrabon where no_kbon = '$no_kbon_esc' limit 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: No Kontrabon tidak ditemukan';
    exit;
}

if ($rowCheck['status'] !== 'draft') {
    echo 'Error: Status saat ini bukan draft, tidak bisa dilakukan First Approval';
    exit;
}

$update1 = mysqli_query($conn2, "update kontrabon set status = 'FIRST APPROVED', first_approve_by = '$approve_user_esc', first_approve_date = '$first_approve_date' where no_kbon = '$no_kbon_esc'");
if (!$update1) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

$update2 = mysqli_query($conn2, "update kontrabon_h set status = 'FIRST APPROVED', first_approve_user = '$approve_user_esc', first_approve_date = '$first_approve_date' where no_kbon = '$no_kbon_esc'");
if (!$update2) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

echo 'Data Berhasil Di First Approve';

mysqli_close($conn2);
?>
