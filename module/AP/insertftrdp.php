<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrdp = $_POST['noftrdp'];
$tglftrdp = date("Y-m-d",strtotime($_POST['tglftrdp']));
$tgl_bayar = date("Y-m-d",strtotime($_POST['tgl_bayar']));
$nama_supp = $_POST['nama_supp'];
$no_pi = $_POST['no_pi'];
$curr = $_POST['curr'];
$create_date = date("Y-m-d H:i:s");
$status = 'draft';
$keterangan = $_POST['keterangan'];
$create_user = $_POST['create_user'];
$no_po = $_POST['no_po'];
$tgl_po = $_POST['tgl_po'];
$total = $_POST['total'];
$dp_code = $_POST['dp_code'];
$dp_value = $_POST['dp_value'];
$balance = $_POST['balance'];
$invoiced = 'Waiting';

// Payment Method (Transfer / Cash) - kolom BARU ftr_dp.payment_method.
// DITOLAK kalau di luar dua pilihan itu, bukan dipilihkan "Transfer" diam-diam:
// cara bayar menentukan perlakuan berikutnya (tanda tangan Cashier & Received
// By di cetakan, dan kemungkinan tidak dibuatkan PV-AP DP), jadi menebaknya di
// sini berarti mencatat keputusan yang tidak pernah diambil siapa pun.
$payment_method = isset($_POST['payment_method']) ? trim((string) $_POST['payment_method']) : '';
if ($payment_method !== 'Transfer' && $payment_method !== 'Cash') {
    http_response_code(400);
    die('Payment Method is required (Transfer / Cash).');
}

// Item Type - WAJIB, dan harus benar-benar ada di pv_mapping_jurnal_dp
// (sumber yang sama dgn dropdown-nya). Request yang dibuat sendiri tidak bisa
// menitipkan teks sembarangan ke kolom yang dipakai mengelompokkan laporan.
$item_type_in = isset($_POST['item_type']) ? trim((string) $_POST['item_type']) : '';
if ($item_type_in === '') {
    http_response_code(400);
    die('Item Type is required.');
}
$cekIt = mysqli_query($conn1, "select 1 from pv_mapping_jurnal_dp
    where status = 'Y' and item_type = '" . mysqli_real_escape_string($conn1, $item_type_in) . "' limit 1");
if (!$cekIt || mysqli_num_rows($cekIt) === 0) {
    http_response_code(400);
    die('Item Type is not recognised.');
}
$item_type_sql = "'" . mysqli_real_escape_string($conn2, $item_type_in) . "'";

// Profit Center - lihat keterangan yang sama di insertftrcbd.php.
$profit_center = isset($_POST['profit_center']) ? mysqli_real_escape_string($conn2, trim((string) $_POST['profit_center'])) : '';

// No PO kosong DITOLAK. Dulu $query hanya dirakit di dalam if ($no_po != ''),
// sehingga kiriman tanpa no_po membuat mysqli_query dipanggil dgn variabel
// yang belum pernah ada - galat fatal, bukan pesan yang bisa dibaca user.
if (trim((string) $no_po) === '') {
    http_response_code(400);
    die('PO number is missing.');
}

$query = "INSERT INTO ftr_dp (no_ftr_dp, tgl_ftr_dp, tgl_bayar, supp, no_po, tgl_po, no_pi, total, dp, dp_value, balance, curr, payment_method, item_type, profit_center, keterangan, status, is_invoiced, create_user, create_date)
VALUES
	('$noftrdp', '$tglftrdp', '$tgl_bayar', '$nama_supp', '$no_po', '$tgl_po', '$no_pi', '$total', '$dp_code', '$dp_value', '$balance', '$curr', '$payment_method', $item_type_sql, '$profit_center', '$keterangan', '$status', '$invoiced', '$create_user', '$create_date')";

$execute = mysqli_query($conn2,$query);

if(!$execute){
   http_response_code(500);
   die('Error: ' . mysqli_error($conn2));
}

mysqli_close($conn2);
