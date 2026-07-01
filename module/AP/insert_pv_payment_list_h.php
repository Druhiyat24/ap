<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$pl_number = $_POST['pl_number'];
$pl_date = date('Y-m-d', strtotime($_POST['pl_date']));
$deskripsi = mysqli_real_escape_string($conn2, isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '');
$create_user = $_POST['create_user'];
$create_date = date('Y-m-d H:i:s');

// Payment List sekarang bisa multi Profit Center dalam satu pengajuan, jadi
// tidak ada lagi satu profit_center per header (kolomnya dibiarkan kosong).
// Keterangan juga sudah dipindah ke per-baris (pv_payment_list_det.deskripsi)
// dan tidak wajib lagi - header deskripsi cuma fallback kosong/legacy.
// PL dibuat dalam status 'Draft', menunggu First Approve & Second Approve.
$query = "INSERT INTO pv_payment_list_h (pl_number, pl_date, deskripsi, status, created_by, created_date)
VALUES
('$pl_number', '$pl_date', '$deskripsi', 'Draft', '$create_user', '$create_date')";
$execute = mysqli_query($conn2, $query);

if ($execute) {
    echo 'Data Saved Successfully With Payment List Number : ' . $pl_number;
} else {
    echo mysqli_error($conn2);
}

mysqli_close($conn2);
?>
