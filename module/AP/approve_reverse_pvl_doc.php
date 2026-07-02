<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');
session_start();
$rvs_number   = $_POST['rvs_number'] ?? '';
$approve_user = $_POST['approve_user'] ?? ($_SESSION['username'] ?? '');
$approve_date = date('Y-m-d H:i:s');
if (empty($rvs_number)) { echo 'Error: rvs_number is empty'; exit; }
$rvs_esc  = mysqli_real_escape_string($conn2, $rvs_number);
$user_esc = mysqli_real_escape_string($conn2, $approve_user);
$sqlCheck = mysqli_query($conn2, "SELECT status FROM ap_reverse_h WHERE rvs_number = '$rvs_esc' LIMIT 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);
if (!$rowCheck || $rowCheck['status'] !== 'DRAFT') { echo 'Error: Status is not DRAFT or document not found'; exit; }
$upd = mysqli_query($conn2, "UPDATE ap_reverse_h SET status='APPROVED', approve_by='$user_esc', approve_date='$approve_date' WHERE rvs_number='$rvs_esc'");
if (!$upd) { echo 'Error: ' . mysqli_error($conn2); exit; }
$sqlDet = mysqli_query($conn2, "SELECT doc_number FROM ap_reverse_det WHERE rvs_number = '$rvs_esc' AND status = 'Y'");
while ($rowDet = mysqli_fetch_assoc($sqlDet)) {
    $pvl_esc = mysqli_real_escape_string($conn2, $rowDet['doc_number']);
    mysqli_query($conn2, "UPDATE pv_payment_voucher_list_h SET status='Draft', approve_user=NULL, approve_date=NULL WHERE pl_number='$pvl_esc'");
    $sqlPvDet = mysqli_query($conn2, "SELECT type_pv, no_kbon FROM pv_payment_voucher_list_det WHERE pl_number='$pvl_esc' AND status != 'Cancel'");
    while ($rowPv = mysqli_fetch_assoc($sqlPvDet)) {
        updateStatusPvl($conn2, $rowPv['type_pv'], $rowPv['no_kbon'], null);
    }
}
echo 'OK';
mysqli_close($conn2);
