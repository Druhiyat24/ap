<?php
include "../../conn/conn.php";
session_start();

header('Content-Type: application/json');
date_default_timezone_set('Asia/Jakarta');

$tgl_transfer = isset($_POST['tgl_transfer']) ? date("Y-m-d", strtotime($_POST['tgl_transfer'])) : date("Y-m-d");
$keterangan   = $_POST['keterangan']  ?? '';
$unik_code    = $_POST['unik_code']   ?? '';
$items        = json_decode($_POST['items'] ?? '[]', true) ?? [];
$user         = $_SESSION['username'] ?? 'system';
$now          = date("Y-m-d H:i:s");

if (empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'Tidak ada item yang dipilih.']);
    exit;
}

// ===== Generate TFTE number =====
$kode_dok = 'TFTE';
$sql_num  = mysqli_query($conn2,
    "SELECT MAX(SUBSTR(no_trans, 15)) AS last_num
     FROM transfer_memo_exim_h
     WHERE no_trans LIKE '%$kode_dok%'"
);
$row_num  = mysqli_fetch_assoc($sql_num);
$last_num = (int)$row_num['last_num'];
$no_trans = $kode_dok . '/NAG/' . date('my') . '/' . sprintf('%05d', $last_num + 1);

// ===== Transaction =====
mysqli_begin_transaction($conn2);

try {

    // Insert header
    $stmt_h = mysqli_prepare($conn2,
        "INSERT INTO transfer_memo_exim_h
            (no_trans, tgl_trans, keterangan, status, created_by, created_at, updated_at, unik_code)
         VALUES (?, ?, ?, 'POST', ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt_h, 'sssssss',
        $no_trans, $tgl_transfer, $keterangan, $user, $now, $now, $unik_code
    );
    if (!mysqli_stmt_execute($stmt_h)) {
        throw new Exception('Gagal insert header: ' . mysqli_stmt_error($stmt_h));
    }

    // Insert detail + update memo_h per item
    foreach ($items as $item) {
        $nm_memo       = $item['nm_memo']       ?? '';
        $item_ket      = $item['keterangan']    ?? '';
        $status_before = $item['status_before'] ?? '';

        $stmt_d = mysqli_prepare($conn2,
            "INSERT INTO transfer_memo_exim_det
                (no_trans, nm_memo, keterangan, status, created_by, created_at, updated_at)
             VALUES (?, ?, ?, 'Y', ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt_d, 'ssssss',
            $no_trans, $nm_memo, $item_ket, $user, $now, $now
        );
        if (!mysqli_stmt_execute($stmt_d)) {
            throw new Exception('Gagal insert detail (' . $nm_memo . '): ' . mysqli_stmt_error($stmt_d));
        }

        // Update status_transfer di memo_h
        $stmt_u = mysqli_prepare($conn2,
            "UPDATE memo_h
             SET status_transfer = 'TFTE',
                 tfte_by         = ?,
                 tfte_date       = ?
             WHERE nm_memo = ?"
        );
        mysqli_stmt_bind_param($stmt_u, 'sss', $user, $now, $nm_memo);
        mysqli_stmt_execute($stmt_u);
    }

    mysqli_commit($conn2);

    echo json_encode(['status' => 'success', 'no_trans' => $no_trans]);

} catch (Exception $e) {
    mysqli_rollback($conn2);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

mysqli_close($conn2);
