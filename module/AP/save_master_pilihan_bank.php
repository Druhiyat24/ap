<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

$id             = intval($_POST['id'] ?? 0);
$katergori_bank = trim($_POST['katergori_bank'] ?? '');
$nama_bank      = trim($_POST['nama_bank']      ?? '');
$kode_bank      = trim($_POST['kode_bank']      ?? '');
$swift_code     = strtoupper(trim($_POST['swift_code'] ?? ''));

// ===== Validasi field wajib =====
if (!$katergori_bank || !$nama_bank) {
    echo json_encode(['status' => 'error', 'message' => 'Bank Category and Bank Name are required.']);
    exit;
}

// ===== Validasi duplikat: Nama Bank harus unik =====
$dup_sql = "SELECT id FROM master_pilihan_bank WHERE nama_bank = ?";
if ($id > 0) {
    $dup_sql .= " AND id != $id"; // abaikan record sendiri saat edit
}
$dup_stmt = mysqli_prepare($conn2, $dup_sql);
mysqli_stmt_bind_param($dup_stmt, 's', $nama_bank);
mysqli_stmt_execute($dup_stmt);
mysqli_stmt_store_result($dup_stmt);

if (mysqli_stmt_num_rows($dup_stmt) > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Bank <b>' . htmlspecialchars($nama_bank) . '</b> sudah terdaftar.'
    ]);
    exit;
}
mysqli_stmt_close($dup_stmt);

// ===== INSERT =====
if ($id === 0) {
    $stmt = mysqli_prepare($conn2,
        "INSERT INTO master_pilihan_bank (katergori_bank, nama_bank, kode_bank, swift_code, status)
         VALUES (?, ?, ?, ?, 'Y')"
    );
    mysqli_stmt_bind_param($stmt, 'ssss', $katergori_bank, $nama_bank, $kode_bank, $swift_code);

// ===== UPDATE =====
} else {
    $stmt = mysqli_prepare($conn2,
        "UPDATE master_pilihan_bank
         SET katergori_bank = ?, nama_bank = ?, kode_bank = ?, swift_code = ?
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssssi', $katergori_bank, $nama_bank, $kode_bank, $swift_code, $id);
}

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
