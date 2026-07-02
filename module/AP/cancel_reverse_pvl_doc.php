<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
$rvs_number = $_POST['rvs_number'] ?? '';
if (empty($rvs_number)) { echo 'Error: rvs_number is empty'; exit; }
$rvs_esc = mysqli_real_escape_string($conn2, $rvs_number);
$sqlCheck = mysqli_query($conn2, "SELECT status FROM ap_reverse_h WHERE rvs_number = '$rvs_esc' LIMIT 1");
$rowCheck = mysqli_fetch_assoc($sqlCheck);
if (!$rowCheck) { echo 'Error: Document not found'; exit; }
if ($rowCheck['status'] !== 'DRAFT') { echo 'Error: Only DRAFT documents can be cancelled'; exit; }
$upd = mysqli_query($conn2, "UPDATE ap_reverse_h SET status='CANCEL' WHERE rvs_number='$rvs_esc'");
echo $upd ? 'OK' : 'Error: ' . mysqli_error($conn2);
mysqli_close($conn2);
