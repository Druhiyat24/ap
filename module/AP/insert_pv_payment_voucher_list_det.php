<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
ini_set('date.timezone', 'Asia/Jakarta');

$pl_number = $_POST['pl_number'];
$type_pv = $_POST['type_pv'];
$no_kbon = $_POST['no_kbon'];
$tgl_kbon = !empty($_POST['tgl_kbon']) ? "'" . date('Y-m-d', strtotime($_POST['tgl_kbon'])) . "'" : 'NULL';
$nama_supp = $_POST['nama_supp'];
$curr = $_POST['curr'];
$total = isset($_POST['total']) && $_POST['total'] !== '' ? $_POST['total'] : 0;
$deskripsi = mysqli_real_escape_string($conn2, isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '');
$profit_center = mysqli_real_escape_string($conn2, isset($_POST['profit_center']) ? $_POST['profit_center'] : '');
$create_user = $_POST['create_user'];
$create_date = date('Y-m-d H:i:s');

$query = "INSERT INTO pv_payment_voucher_list_det (pl_number, type_pv, no_kbon, tgl_kbon, nama_supp, curr, total, deskripsi, profit_center, status, create_user, create_date)
VALUES
('$pl_number', '$type_pv', '$no_kbon', $tgl_kbon, '$nama_supp', '$curr', '$total', '$deskripsi', '$profit_center', 'Draft', '$create_user', '$create_date')";
$execute = mysqli_query($conn2, $query);

if ($execute) {
    // PV ini sekarang ikut sebuah Payment Voucher List yang masih Draft - tandai di table sumbernya.
    updateStatusPvl($conn2, $type_pv, $no_kbon, 'Draft');
    echo 'OK';
} else {
    echo mysqli_error($conn2);
}

mysqli_close($conn2);
?>
