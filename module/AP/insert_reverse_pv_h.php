<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_reverse   = mysqli_real_escape_string($conn2, $_POST['no_reverse'] ?? '');
$reverse_date = !empty($_POST['reverse_date']) ? date('Y-m-d', strtotime($_POST['reverse_date'])) : date('Y-m-d');
$deskripsi    = mysqli_real_escape_string($conn2, $_POST['deskripsi'] ?? '');
$create_user  = mysqli_real_escape_string($conn2, $_POST['create_user'] ?? '');
$create_date  = date('Y-m-d H:i:s');

if (empty($no_reverse)) {
    echo 'Error: no_reverse is empty';
    exit;
}

$q = "INSERT INTO ap_reverse_h (rvs_number, rvs_date, type_doc, deskripsi, status, created_by, created_date)
      VALUES ('$no_reverse', '$reverse_date', 'PAYMENT VOUCHER', '$deskripsi', 'DRAFT', '$create_user', '$create_date')";
$r = mysqli_query($conn2, $q);
echo $r ? 'OK' : 'Error: ' . mysqli_error($conn2);
mysqli_close($conn2);
