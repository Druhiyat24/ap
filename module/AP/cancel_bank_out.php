<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_bi        = $_POST['no_pay'] ?? '';
$cancel_user  = $_POST['update_user'] ?? '';
$cancel_date  = date("Y-m-d H:i:s");
$status       = 'Cancel';

if ($no_bi === '') {
    echo "Error: no_bankout kosong";
    exit;
}

$sql = "UPDATE b_bankout_h 
        SET cancel_by='$cancel_user', cancel_date='$cancel_date', status='$status' 
        WHERE no_bankout='$no_bi'";
$query = mysqli_query($conn2, $sql);

if (!$query) {
    echo "Error (b_bankout_h): " . mysqli_error($conn2);
    exit;
}

$sqlx = mysqli_query($conn1, "
    SELECT no_journal 
    FROM tbl_list_journal 
    WHERE no_journal='$no_bi' 
      AND type_journal LIKE '%Reverse%' 
      AND status='Draft'
    LIMIT 1
");
$rowx = mysqli_fetch_assoc($sqlx);
$filter_jurnal = $rowx['no_journal'] ?? null;

if ($filter_jurnal === null) {
    $jurnal_balik = mysqli_query($conn2, "
        INSERT INTO tbl_list_journal
        SELECT 
            '', 
            no_journal, 
            '$cancel_date' AS tgl_journal, 
            CONCAT('Reverse ', type_journal) AS type_journal,
            no_coa, nama_coa, no_costcenter, nama_costcenter, reff_doc, reff_date,
            buyer, no_ws, curr, rate, credit, debit, credit_idr, debit_idr,
            status, keterangan, create_by, create_date,
            '$cancel_user' AS approve_by,
            CURRENT_TIMESTAMP() AS approve_date,
            cancel_by, cancel_date, created_at, updated_at, profit_center
        FROM tbl_list_journal
        WHERE no_journal='$no_bi' AND status = 'Draft'
    ");
}

$sql2 = "INSERT INTO tbl_list_journal_cancel 
         SELECT * FROM tbl_list_journal WHERE no_journal='$no_bi'";
mysqli_query($conn2, $sql2);

mysqli_query($conn2, "UPDATE b_reportbank SET status='$status' WHERE no_doc='$no_bi'");
mysqli_query($conn2, "UPDATE b_bankout_det SET for_balance='0' WHERE no_bankout='$no_bi'");
mysqli_query($conn2, "UPDATE status SET no_pay='', tgl_pay='' WHERE no_pay='$no_bi'");
mysqli_query($conn2, "
    UPDATE tbl_pv_h 
    INNER JOIN b_bankout_det ON b_bankout_det.no_reff = tbl_pv_h.no_pv
    SET tbl_pv_h.outstanding = tbl_pv_h.total
    WHERE b_bankout_det.no_bankout = '$no_bi'
");
mysqli_query($conn2, "
    UPDATE list_payment 
    INNER JOIN b_bankout_det ON b_bankout_det.no_reff = list_payment.no_payment
    SET list_payment.status = 'Closed'
    WHERE b_bankout_det.no_bankout = '$no_bi'
");
mysqli_query($conn2, "
    UPDATE memo_h 
    SET no_bankout='', status='PAYMENT DRAFT' 
    WHERE no_bankout='$no_bi'
");

echo "OK";
mysqli_close($conn2);
exit;
?>
