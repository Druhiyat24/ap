<?php
include "../../conn/conn.php";
session_start();

date_default_timezone_set('Asia/Jakarta');

$no_trans   = $_POST['no_trans'] ?? '';
$cancelIds  = $_POST['cancel_ids'] ?? [];
$user       = $_SESSION['username'] ?? 'system';
$now        = date("Y-m-d H:i:s");

if(empty($no_trans)){
    echo json_encode([
        'status' => 'error',
        'message' => 'No Trans kosong'
    ]);
    exit;
}

// 🔥 pastikan array
if(!is_array($cancelIds)){
    $cancelIds = [$cancelIds];
}

// 🔥 sanitize ID (hindari SQL injection)
$cancelIds = array_map('intval', $cancelIds);
$idList    = implode(',', $cancelIds);

// mulai transaksi
mysqli_begin_transaction($conn2);

try {

    if(!empty($cancelIds)){

        // 🔹 ambil nm_memo
        $getMemo = mysqli_query($conn2, "
            SELECT nm_memo 
            FROM memo_h 
            WHERE id_h IN ($idList)
        ");

        $memoList = [];
        while($row = mysqli_fetch_assoc($getMemo)){
            $memoList[] = "'" . mysqli_real_escape_string($conn2, $row['nm_memo']) . "'";
        }

        if(!empty($memoList)){
            $memoIn = implode(',', $memoList);

            // 🔹 update memo_h
            $updateMemo = "update memo_h a INNER JOIN transfer_memo_exim_det b ON b.nm_memo = a.nm_memo SET a.status_transfer = 'A-TETF', a.tfte_by = null, a.tfte_date = null where b.no_trans = '$no_trans'";
            mysqli_query($conn2, $updateMemo);

            // 🔹 update detail
            $updateDet = "
                UPDATE transfer_memo_exim_det
                SET status = 'N',
                    updated_at = '$now'
                WHERE no_trans = '$no_trans'
                AND nm_memo IN ($memoIn)
            ";
            mysqli_query($conn2, $updateDet);
        }
    }

    // 🔥 cek masih ada detail aktif?
    $cek = mysqli_query($conn2, "
        SELECT COUNT(*) as total 
        FROM transfer_memo_exim_det
        WHERE no_trans = '$no_trans'
        AND status = 'Y'
    ");
    $rowCek = mysqli_fetch_assoc($cek);

    if($rowCek['total'] == 0){
        // 🔹 update header hanya kalau semua sudah cancel
        $updateHeader = "
            UPDATE transfer_memo_exim_h
            SET status = 'CANCEL',
                cancel_by = '$user',
                cancel_date = '$now',
                updated_at = '$now'
            WHERE no_trans = '$no_trans'
        ";
        mysqli_query($conn2, $updateHeader);
    }

    mysqli_commit($conn2);

    echo json_encode([
        'status' => 'success',
        'message' => 'Semua data berhasil di-cancel'
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn2);
?>
