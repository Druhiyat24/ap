<?php
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$tl_number = isset($_POST['tl_number']) ? $_POST['tl_number'] : null;
$cancel_user = isset($_POST['cancel_user']) ? $_POST['cancel_user'] : ($_SESSION['username'] ?? '');

if (empty($tl_number)) {
    echo json_encode(['status' => 'error', 'message' => 'Transfer List tidak valid.']);
    exit;
}

$tl_number_esc = mysqli_real_escape_string($conn2, $tl_number);
$cancel_user_esc = mysqli_real_escape_string($conn2, $cancel_user);

$upd = mysqli_query($conn2, "UPDATE pv_transfer_list_h SET status = 'Cancel', cancel_by = '$cancel_user_esc', cancel_date = NOW()
    WHERE tl_number = '$tl_number_esc'");

if ($upd) {
    echo json_encode(['status' => 'ok', 'message' => 'Transfer List berhasil dibatalkan.']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
