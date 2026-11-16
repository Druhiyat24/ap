<?php
/* Simpan baris Book Transfer. Dipanggil AJAX dari bt_form.php.
   Satu kali Save = SATU dokumen, nomornya BTR/<PC>/<mmyy>/<00001>,
   berapa pun barisnya. Semuanya masuk dalam satu transaksi: gagal satu,
   batal semua. */

session_start();
include '../../../conn/conn.php';
include 'cfp_lib.php';
header('Content-Type: application/json');

$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user == '') {
    echo json_encode(array('status' => 'gagal', 'pesan' => 'Session expired, please log in again.'));
    exit;
}

$baris = json_decode(isset($_POST['baris']) ? $_POST['baris'] : '[]', true);
if (!is_array($baris) || count($baris) == 0) {
    echo json_encode(array('status' => 'gagal', 'pesan' => 'No rows received.'));
    exit;
}

// daftar akun dibaca ulang di server - jangan percaya kiriman layar
$akun = cfp_daftar_akun($conn1);

mysqli_autocommit($conn2, false);
$no_doc = '';   // dibuat sekali, dipakai semua baris
$jml    = 0;
$aman   = true;
$pesan  = '';

foreach ($baris as $b) {
    $source = isset($b['source']) ? trim($b['source']) : '';
    $tujuan = isset($b['tujuan']) ? trim($b['tujuan']) : '';

    if (!isset($akun[$source]) || !isset($akun[$tujuan])) {
        $aman = false; $pesan = 'Unknown account: ' . $source . ' / ' . $tujuan;
        break;
    }
    if ($source == $tujuan) {
        $aman = false; $pesan = 'Source and Destination cannot be the same.';
        break;
    }

    $amount = (float) $b['amount'];
    if ($amount <= 0) {
        $aman = false; $pesan = 'Amount must be greater than 0.';
        break;
    }

    $tanggal   = date('Y-m-d', strtotime($b['tanggal']));
    $deskripsi = isset($b['deskripsi']) ? trim($b['deskripsi']) : '';
    $curr        = $akun[$source]['curr'];
    $curr_tujuan = $akun[$tujuan]['curr'];

    // Kurs USD diambil ulang di server, bukan dari layar.
    // Rupiah ke rupiah tidak memakai kurs sama sekali: rate 1.
    $kurs = cfp_kurs_pajak($conn1, $tanggal, 'USD');
    $rate = ($curr == 'IDR' && $curr_tujuan == 'IDR') ? 1 : $kurs['rate'];

    $hitung      = cfp_hitung_transfer($amount, $rate, $curr, $curr_tujuan);
    $pc_source   = $akun[$source]['nama_pc'];
    $pc_tujuan   = $akun[$tujuan]['nama_pc'];
    $intra_inter = cfp_intra_inter($akun[$source]['kode_pc'], $akun[$tujuan]['kode_pc']);

    // SATU KALI SAVE = SATU NOMOR, berapa pun barisnya (diminta pemakai).
    // PC & bulannya ikut baris pertama.
    if ($no_doc == '') {
        $no_doc = cfp_no_doc_berikut($conn2, 'BOOK TRANSFER', $akun[$source]['kode_pc'], $tanggal);
    }

    $sql = "insert into ap_cf_projection
            (no_doc, jenis, tanggal, akun_source, akun_tujuan, pc_source, pc_tujuan,
             intra_inter, deskripsi, curr, amount, rate, amount_idr, amount_usd,
             curr_tujuan, amount_tujuan,
             status, created_by, created_date)
            values ('" . mysqli_real_escape_string($conn2, $no_doc) . "',
                    'BOOK TRANSFER',
                    '" . mysqli_real_escape_string($conn2, $tanggal) . "',
                    '" . mysqli_real_escape_string($conn2, $source) . "',
                    '" . mysqli_real_escape_string($conn2, $tujuan) . "',
                    '" . mysqli_real_escape_string($conn2, $pc_source) . "',
                    '" . mysqli_real_escape_string($conn2, $pc_tujuan) . "',
                    '" . mysqli_real_escape_string($conn2, $intra_inter) . "',
                    '" . mysqli_real_escape_string($conn2, $deskripsi) . "',
                    '" . mysqli_real_escape_string($conn2, $curr) . "',
                    " . $amount . ", " . $rate . ",
                    " . $hitung['amount_idr'] . ", " . $hitung['amount_usd'] . ",
                    '" . mysqli_real_escape_string($conn2, $curr_tujuan) . "',
                    " . $hitung['amount_tujuan'] . ",
                    'Post',
                    '" . mysqli_real_escape_string($conn2, $user) . "', NOW())";

    if (!mysqli_query($conn2, $sql)) {
        $aman = false; $pesan = mysqli_error($conn2);
        break;
    }
    $jml++;
}

if ($aman) {
    mysqli_commit($conn2);
    echo json_encode(array('status' => 'ok', 'jml' => $jml, 'no_doc' => $no_doc));
} else {
    mysqli_rollback($conn2);
    echo json_encode(array('status' => 'gagal', 'pesan' => $pesan));
}
mysqli_autocommit($conn2, true);
