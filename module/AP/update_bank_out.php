<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi        = $_POST['no_pay'] ?? '';
$approve_user = $_POST['update_user'] ?? '';
$approve_date = date("Y-m-d H:i:s");
$status       = 'Approved';

if ($no_bi === '') {
    echo "Error: no_bankout kosong";
    exit;
}

// --- Query 1 ---
$sql = "UPDATE b_bankout_h 
        SET approve_by='$approve_user', approve_date='$approve_date', status='$status'
        WHERE no_bankout='$no_bi'";
$query = mysqli_query($conn2, $sql);

// --- Query 2 (non-blocking, abaikan error) ---
if ($query) {
    $sql2 = "UPDATE tbl_list_journal 
             SET approve_by='$approve_user', approve_date='$approve_date', status='$status'
             WHERE no_journal='$no_bi'";

    // Jalankan tanpa menunggu hasil (gunakan @ untuk abaikan error)
    @mysqli_query($conn2, $sql2);
}

if ($query) {
    echo "OK";
} else {
    echo "Error: " . mysqli_error($conn2);
}

mysqli_close($conn2);
exit;
?>
