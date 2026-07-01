<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();

$pl_number = isset($_POST['pl_number']) ? $_POST['pl_number'] : '';
$approve_user = $_SESSION['username'] ?? '';
$approve_date = date('Y-m-d H:i:s');

if ($pl_number === '') {
    echo 'Error: pl_number kosong';
    exit;
}

if ($approve_user === '') {
    echo 'Error: Session tidak valid, silahkan login ulang';
    exit;
}

$pl_number_esc = mysqli_real_escape_string($conn2, $pl_number);
$approve_user_esc = mysqli_real_escape_string($conn2, $approve_user);

$querysSecond = mysqli_query($conn2, "select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$approve_user_esc' and useraccess.menu = 'Approval Payment List Second'");
$rsSecond = mysqli_fetch_assoc($querysSecond);
$idSecond = isset($rsSecond['id']) ? $rsSecond['id'] : 0;

if ($idSecond != '118') {
    echo 'Error: Anda tidak memiliki akses untuk Second Approve Payment List';
    exit;
}

$sqlCheck = mysqli_query($conn2, "select status from pv_payment_list_h where pl_number = '$pl_number_esc' limit 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: Payment List tidak ditemukan';
    exit;
}

if ($rowCheck['status'] !== 'FIRST APPROVED') {
    echo 'Error: Status saat ini bukan FIRST APPROVED, tidak bisa dilakukan Second Approve';
    exit;
}

$update = mysqli_query($conn2, "update pv_payment_list_h set status = 'SECOND APPROVED', second_approve_user = '$approve_user_esc', second_approve_date = '$approve_date' where pl_number = '$pl_number_esc'");

if (!$update) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

$sqlDet = mysqli_query($conn2, "select type_pv, no_kbon from pv_payment_list_det where pl_number = '$pl_number_esc' and status != 'Cancel'");
while ($rowDet = mysqli_fetch_assoc($sqlDet)) {
    updateStatusPl($conn2, $rowDet['type_pv'], $rowDet['no_kbon'], 'SECOND APPROVED');
}

echo 'Payment List Berhasil Di Second Approve';

mysqli_close($conn2);
?>
