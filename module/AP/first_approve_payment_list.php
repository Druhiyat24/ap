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

$querysFirst = mysqli_query($conn2, "select menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$approve_user_esc' and useraccess.menu = 'Approval Payment List First'");
$rsFirst = mysqli_fetch_assoc($querysFirst);
$idFirst = isset($rsFirst['id']) ? $rsFirst['id'] : 0;

if ($idFirst != '117') {
    echo 'Error: Anda tidak memiliki akses untuk First Approve Payment List';
    exit;
}

$sqlCheck = mysqli_query($conn2, "select status from pv_payment_list_h where pl_number = '$pl_number_esc' limit 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);

if (!$rowCheck) {
    echo 'Error: Payment List tidak ditemukan';
    exit;
}

if ($rowCheck['status'] !== 'Draft') {
    echo 'Error: Status saat ini bukan Draft, tidak bisa dilakukan First Approve';
    exit;
}

$update = mysqli_query($conn2, "update pv_payment_list_h set status = 'FIRST APPROVED', first_approve_user = '$approve_user_esc', first_approve_date = '$approve_date' where pl_number = '$pl_number_esc'");

if (!$update) {
    echo 'Error: ' . mysqli_error($conn2);
    exit;
}

$sqlDet = mysqli_query($conn2, "select type_pv, no_kbon from pv_payment_list_det where pl_number = '$pl_number_esc' and status != 'Cancel'");
while ($rowDet = mysqli_fetch_assoc($sqlDet)) {
    updateStatusPl($conn2, $rowDet['type_pv'], $rowDet['no_kbon'], 'FIRST APPROVED');
}

echo 'Payment List Berhasil Di First Approve';

mysqli_close($conn2);
?>
