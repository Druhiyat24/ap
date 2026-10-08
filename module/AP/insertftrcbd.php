<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$noftrcbd = $_POST['noftrcbd'];
$tglftrcbd = date("Y-m-d",strtotime($_POST['tglftrcbd']));
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
$sum_sub = $_POST['sum_sub'];
$sum_tax = $_POST['sum_tax'];
$sum_total = $_POST['sum_total'];
$invoiced = 'Waiting';
// Payment Method (Transfer / Cash) - kolom BARU ftr_cbd.payment_method.
// Nilainya DIPETAKAN lewat daftar putih, bukan dipakai apa adanya: kiriman di
// luar dua pilihan itu jatuh ke Transfer, sehingga tidak ada nilai asing yang
// bisa masuk ke kolom yang nanti dipakai menyaring tab Petty Cash Out.
$payment_method = isset($_POST['payment_method']) ? trim((string) $_POST['payment_method']) : '';
if ($payment_method !== 'Transfer' && $payment_method !== 'Cash') {
    // DITOLAK, bukan dipilihkan "Transfer" diam-diam. Cara bayar menentukan
    // perlakuan berikutnya (tanda tangan di cetakan, dan kemungkinan tidak
    // dibuatkan PV-AP CBD), jadi menebaknya di sini berarti mencatat keputusan
    // yang tidak pernah diambil siapa pun.
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

// Profit Center: sampai 02 Okt 2026 pilihan ini tidak pernah ikut tersimpan -
// hanya dipakai sementara untuk menentukan sumber daftar PO. Sekarang direkam,
// karena pembayaran FTR langsung dari kas kecil membutuhkannya untuk jurnal.
$profit_center = isset($_POST['profit_center']) ? mysqli_real_escape_string($conn2, trim((string) $_POST['profit_center'])) : '';
$tambah = '0';

// echo $noftrcbd;
// echo $tglftrcbd;
// echo $nama_supp;
// echo $no_pi;
// echo $curr;
// echo $create_date;
// echo $status;
// echo $create_user;
// echo $no_po;
// echo $tgl_po;
// echo $sum_sub;
// echo $sum_tax;
// echo $sum_total;
	
$query = "INSERT INTO ftr_cbd (no_ftr_cbd, tgl_ftr_cbd, tgl_bayar, supp, no_po, tgl_po, no_pi, subtotal, tax, total, curr, payment_method, item_type, profit_center, keterangan, status, create_user, create_date, is_invoiced,biaya_tambahan) 
VALUES 
	('$noftrcbd', '$tglftrcbd', '$tgl_bayar', '$nama_supp', '$no_po', '$tgl_po', '$no_pi', '$sum_sub', '$sum_tax', '$sum_total', '$curr', '$payment_method', $item_type_sql, '$profit_center', '$keterangan', '$status', '$create_user', '$create_date', '$invoiced', '$tambah')";
$execute = mysqli_query($conn2,$query);

if(!$execute){	
   die('Error: ' . mysqli_error());	
}

mysqli_close($conn2);
?>