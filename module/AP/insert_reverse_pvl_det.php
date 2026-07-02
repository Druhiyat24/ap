<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
$no_reverse  = $_POST['no_reverse'];
$doc_number  = mysqli_real_escape_string($conn2, $_POST['doc_number']);
$doc_date    = !empty($_POST['doc_date']) ? "'" . date('Y-m-d', strtotime($_POST['doc_date'])) . "'" : 'NULL';
$deskripsi   = mysqli_real_escape_string($conn2, $_POST['deskripsi'] ?? '');
$create_user = mysqli_real_escape_string($conn2, $_POST['create_user'] ?? '');
$no_reverse_esc = mysqli_real_escape_string($conn2, $no_reverse);
$query = "INSERT INTO ap_reverse_det (rvs_number, doc_number, doc_date, nama_supp, curr, total, deskripsi, status)
          VALUES ('$no_reverse_esc', '$doc_number', $doc_date, '', '', 0, '$deskripsi', 'Y')";
$execute = mysqli_query($conn2, $query);
echo $execute ? 'OK' : 'Error: ' . mysqli_error($conn2);
mysqli_close($conn2);
