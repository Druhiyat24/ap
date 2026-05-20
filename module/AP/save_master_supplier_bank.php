<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

$id              = intval($_POST['id']              ?? 0);
$id_supplier     = trim($_POST['id_supplier']       ?? '');
$tipe_sup        = trim($_POST['tipe_sup']          ?? '');
$bank_currency   = strtoupper(trim($_POST['bank_currency']   ?? ''));
$bank_name       = trim($_POST['bank_name']         ?? '');
$bank_account    = trim($_POST['bank_account']      ?? '');
$beneficiary     = trim($_POST['beneficiary_name']  ?? '');

// ===== Validasi field wajib =====
if (!$id_supplier || !$tipe_sup || !$bank_currency || !$bank_name || !$bank_account || !$beneficiary) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
    exit;
}

// ===== Validasi duplikat: id_supplier + bank_account harus unik =====
$dup_sql   = "SELECT id FROM master_supplier_bank
              WHERE id_supplier = ? AND bank_account = ? AND status = 'Active'";
if ($id > 0) {
    $dup_sql .= " AND id != $id"; // abaikan record sendiri saat edit
}
$dup_stmt = mysqli_prepare($conn2, $dup_sql);
mysqli_stmt_bind_param($dup_stmt, 'ss', $id_supplier, $bank_account);
mysqli_stmt_execute($dup_stmt);
mysqli_stmt_store_result($dup_stmt);

if (mysqli_stmt_num_rows($dup_stmt) > 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Bank account <b>' . htmlspecialchars($bank_account) . '</b> sudah terdaftar untuk supplier/customer ini.'
    ]);
    exit;
}

$user = $_SESSION['username'] ?? 'system';
$now  = date('Y-m-d H:i:s');

// ===== INSERT =====
if ($id === 0) {
    $stmt = mysqli_prepare($conn2,
        "INSERT INTO master_supplier_bank
            (id_supplier, tipe_sup, bank_currency, bank_name, bank_account,
             beneficiary_name, status, created_by, created_date)
         VALUES (?, ?, ?, ?, ?, ?, 'Active', ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, 'ssssssss',
        $id_supplier, $tipe_sup, $bank_currency, $bank_name,
        $bank_account, $beneficiary, $user, $now
    );

// ===== UPDATE =====
} else {
    $stmt = mysqli_prepare($conn2,
        "UPDATE master_supplier_bank
         SET id_supplier = ?, tipe_sup = ?, bank_currency = ?,
             bank_name = ?, bank_account = ?, beneficiary_name = ?
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'ssssssi',
        $id_supplier, $tipe_sup, $bank_currency, $bank_name,
        $bank_account, $beneficiary, $id
    );
}

$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($dup_stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn2)]);
}
