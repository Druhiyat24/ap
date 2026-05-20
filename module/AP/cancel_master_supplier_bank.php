<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');

$id   = intval($_POST['id'] ?? 0);
$user = $_SESSION['username'] ?? 'system';
$now  = date('Y-m-d H:i:s');

if ($id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    exit;
}

$stmt = mysqli_prepare($conn2,
    "UPDATE master_supplier_bank
     SET status = 'Cancel', cancel_by = ?, cancel_date = ?
     WHERE id = ? AND status = 'Active'"
);
mysqli_stmt_bind_param($stmt, 'ssi', $user, $now, $id);
mysqli_stmt_execute($stmt);
$affected = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if ($affected > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan atau sudah di-cancel.']);
}
