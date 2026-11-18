<?php
/* ============================================================================
   Isi dropdown Bank di layar Cash Flow Projection, MENURUT PROFIT CENTER yang
   sedang dipilih - tanpa menunggu Search.

   Dulu daftar banknya hanya ikut jawaban laporan, jadi sebelum Search isinya
   cuma "All Banks" dan memilih Garment/Knitting tidak mengubah apa pun.

   SENGAJA TIDAK memanggil mesin laporan: yang dibutuhkan cuma daftar akun,
   dan menjalankan seluruh proyeksi untuk itu berarti menunggu beberapa detik
   setiap kali dropdown Profit Center disentuh. Daftarnya diambil dari
   cf_akun_lib.php - berkas yang SAMA yang dipakai mesin, supaya isi dropdown
   mustahil berbeda dari isi laporan.
   ============================================================================ */
session_start();
include '../../conn/conn.php';
require_once __DIR__ . '/cf_akun_lib.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Your session has ended. Please sign in again.'));
    exit;
}

$pc = strtoupper(trim(isset($_POST['pc']) ? $_POST['pc'] : 'ALL'));

$kasTanpaPc = array();
$akun = cf_daftar_akun($conn2, $kasTanpaPc);

$daftar = array();
foreach ($akun as $k => $a) {
    if (!cfpIkut($a, $pc, '')) { continue; }
    $daftar[] = array('kunci' => $a['kunci'], 'label' => $a['label'],
                      'curr' => $a['curr'], 'pc' => $a['pc'], 'loan' => $a['loan'] ? 1 : 0);
}

echo json_encode(array(
    'status' => 'success',
    'pc'     => $pc,
    'bank'   => $daftar,
), JSON_INVALID_UTF8_SUBSTITUTE);
