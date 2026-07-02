<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_reverse = mysqli_real_escape_string($conn2, $_POST['no_reverse'] ?? '');
$doc_number = mysqli_real_escape_string($conn2, $_POST['doc_number'] ?? '');
$type_pv    = mysqli_real_escape_string($conn2, $_POST['type_pv'] ?? '');
$doc_date   = !empty($_POST['doc_date']) ? "'" . date('Y-m-d', strtotime($_POST['doc_date'])) . "'" : 'NULL';
$nama_supp  = mysqli_real_escape_string($conn2, $_POST['nama_supp'] ?? '');
$curr       = mysqli_real_escape_string($conn2, $_POST['curr'] ?? '');
$total      = is_numeric($_POST['total'] ?? '') ? (float)$_POST['total'] : 0;
$deskripsi  = mysqli_real_escape_string($conn2, $_POST['deskripsi'] ?? '');

if (empty($no_reverse) || empty($doc_number)) {
    echo 'Error: missing required fields';
    exit;
}

$q = "INSERT INTO ap_reverse_det (rvs_number, doc_number, type_pv, doc_date, nama_supp, curr, total, deskripsi, status)
      VALUES ('$no_reverse', '$doc_number', '$type_pv', $doc_date, '$nama_supp', '$curr', '$total', '$deskripsi', 'Y')";
$r = mysqli_query($conn2, $q);
echo $r ? 'OK' : 'Error: ' . mysqli_error($conn2);
mysqli_close($conn2);
