<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if ($id === 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
    exit;
}

$cur_stmt = mysqli_prepare($conn2, "SELECT status FROM master_pilihan_bank WHERE id = ?");
mysqli_stmt_bind_param($cur_stmt, 'i', $id);
mysqli_stmt_execute($cur_stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($cur_stmt));
mysqli_stmt_close($cur_stmt);

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    exit;
}

$new_status = $row['status'] === 'Y' ? 'N' : 'Y';

$stmt = mysqli_prepare($conn2, "UPDATE master_pilihan_bank SET status = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'si', $new_status, $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo json_encode(['status' => 'success', 'new_status' => $new_status]);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
