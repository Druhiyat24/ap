<?php
include "../../conn/conn.php";
session_start();

header('Content-Type: application/json');

$no_trans   = $_POST['no_trans'] ?? '';
$approveIds = $_POST['approve_ids'] ?? [];
$cancelIds  = $_POST['cancel_ids'] ?? [];

$user = $_SESSION['username'];
$now  = date("Y-m-d H:i:s");

mysqli_begin_transaction($conn2);

try {

    // ================= APPROVE =================
    if (!empty($approveIds)) {

        $ids = implode(",", array_map('intval', $approveIds));

        $queryApprove = "
            UPDATE memo_h
            SET 
                status_transfer = 'A-TETF',
                app_tetf_by = '$user',
                app_tetf_date = '$now'
            WHERE id_h IN ($ids)
        ";

        mysqli_query($conn2, $queryApprove);
    }

    // ================= CANCEL =================
    if (!empty($cancelIds)) {

        $idsCancel = implode(",", array_map('intval', $cancelIds));

        // ambil nm_memo
        $getMemo = "
            SELECT nm_memo 
            FROM memo_h 
            WHERE id_h IN ($idsCancel)
        ";

        $resMemo = mysqli_query($conn2, $getMemo);

        $cancelMemo = [];
        while ($row = mysqli_fetch_assoc($resMemo)) {
            $cancelMemo[] = "'" . mysqli_real_escape_string($conn2, $row['nm_memo']) . "'";
        }

        // update memo_h
        $queryCancelMemo = "
            UPDATE memo_h
            SET 
                status_transfer = status_to_finance,
                tetf_by = NULL,
                tetf_date = NULL,
                status_to_finance = NULL
            WHERE id_h IN ($idsCancel)
        ";

        mysqli_query($conn2, $queryCancelMemo);

        // update detail
        if (!empty($cancelMemo)) {

            $memoList = implode(",", $cancelMemo);

            $queryDetail = "
                UPDATE transfer_memo_exim_det
                SET 
                    status = 'N',
                    updated_at = '$now'
                WHERE no_trans = '$no_trans'
                AND nm_memo IN ($memoList)
            ";

            mysqli_query($conn2, $queryDetail);
        }
    }

    // ================= HEADER =================
    $queryHeader = "
        UPDATE transfer_memo_exim_h
        SET 
            status = 'APPROVED',
            approved_by = '$user',
            approved_date = '$now',
            updated_at = '$now'
        WHERE no_trans = '$no_trans'
    ";

    mysqli_query($conn2, $queryHeader);

    mysqli_commit($conn2);

    echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil diproses'
    ]);

} catch (Exception $e) {

    mysqli_rollback($conn2);

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
