<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$no_kbon_det = isset($_POST['no_kbon']) ? $_POST['no_kbon'] : '';
$approve_user = $_SESSION['username'] ?? '';
$second_approve_date = date("Y-m-d H:i:s");

if ($no_kbon_det === '') {
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

$no_kbon_det_esc = mysqli_real_escape_string($conn2, $no_kbon_det);
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

$sqlCheck = mysqli_query($conn2, "select no_kbon, status from kontrabon_h_installment_detail where no_kbon_det = '$no_kbon_det_esc' limit 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: Cicilan tidak ditemukan';
    exit;
}

if ($rowCheck['status'] !== 'FIRST APPROVED') {
    echo 'Error: Status saat ini bukan FIRST APPROVED, tidak bisa dilakukan Second Approval';
    exit;
}

$no_kbon = mysqli_real_escape_string($conn2, $rowCheck['no_kbon']);

$update1 = mysqli_query($conn2, "update kontrabon_h_installment_detail set status = 'SECOND APPROVED', second_approve_user = '$approve_user_esc', second_approve_date = '$second_approve_date' where no_kbon_det = '$no_kbon_det_esc'");
if (!$update1) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

// Sinkronkan status header (kontrabon_h) hanya kalau SEMUA cicilan pada Payment Voucher ini sudah SECOND APPROVED.
$sqlRemaining = mysqli_query($conn2, "select count(*) as sisa from kontrabon_h_installment_detail where no_kbon = '$no_kbon' and status != 'SECOND APPROVED'");
$rowRemaining = mysqli_fetch_assoc($sqlRemaining);
if ((int) $rowRemaining['sisa'] === 0) {
    mysqli_query($conn2, "update kontrabon_h set status = 'SECOND APPROVED', confirm_user = '$approve_user_esc', confirm_date = '$second_approve_date', second_approve_user = '$approve_user_esc', second_approve_date = '$second_approve_date' where no_kbon = '$no_kbon'");
}

echo 'Data Berhasil Di Second Approve';

mysqli_close($conn2);
?>
