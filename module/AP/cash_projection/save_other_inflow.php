<?php
/* Simpan baris Other Inflow. Dipanggil AJAX dari oi_form.php.
   Satu kali Save = SATU dokumen, nomornya OIN/<PC>/<mmyy>/<00001>,
   berapa pun barisnya. Semuanya dalam satu transaksi: gagal satu,
   batal semua. Polanya sama persis dgn save_book_transfer.php. */

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

// bank & kategori dibaca ulang di server - jangan percaya kiriman layar
$bank     = cfp_daftar_akun($conn1);
$kategori = cfp_daftar_kategori($conn1, 'Cash In');

mysqli_autocommit($conn2, false);
/* SATU TANGGAL = SATU NOMOR (permintaan pemakai 19 Nov 2026). Dulu satu
   kali Save selalu satu nomor, berapa pun tanggalnya - dan sejak barisnya
   bisa diunggah dari Excel, satu berkas gampang memuat banyak tanggal.
   Dokumen bertanggal campur tidak bisa dibaca di laporan harian, jadi
   nomornya dipecah per tanggal. Yang diketik tangan hampir selalu satu
   tanggal, jadi perilakunya tidak berubah bagi mereka. */
/* Jenis laporannya datang dari tombol Create yang dipakai - lihat
   cfp_type_report(). Kiriman yang kosong jatuh ke WEEKLY, bukan
   ditolak: yang penting barisnya tidak pernah lahir tanpa jenis. */
$type_report = cfp_type_report(isset($_POST['type_report']) ? $_POST['type_report'] : '');

$doc = array();   // tanggal => nomor dokumen
$jml    = 0;
$aman   = true;
$pesan  = '';

foreach ($baris as $b) {
    $akun = isset($b['bank']) ? trim($b['bank']) : '';
    $nama = isset($b['nama']) ? trim($b['nama']) : '';
    $ktg  = isset($b['kategori']) ? trim($b['kategori']) : '';

    // Customer boleh dipilih dari master (id terisi) atau diketik bebas
    // (id kosong - mis. "Bunga" yang memang bukan nama pelanggan). Kalau
    // id-nya ada, namanya dibaca ULANG dari master; teks layar diabaikan.
    $id_cus = isset($b['id_supplier']) ? trim((string) $b['id_supplier']) : '';
    if ($id_cus !== '') {
        $nama_master = cfp_nama_master($conn1, $id_cus);
        if ($nama_master === '') {
            $aman = false; $pesan = 'Unknown customer id: ' . $id_cus;
            break;
        }
        $nama = $nama_master;
    }

    if (!isset($bank[$akun])) {
        $aman = false; $pesan = 'Unknown bank or cash account: ' . $akun;
        break;
    }
    if ($nama == '') {
        $aman = false; $pesan = 'Customer Name is required.';
        break;
    }
    if (!in_array($ktg, $kategori)) {
        $aman = false; $pesan = 'Unknown income category: ' . $ktg;
        break;
    }

    $amount = (float) $b['amount'];
    if ($amount <= 0) {
        $aman = false; $pesan = 'Amount must be greater than 0.';
        break;
    }

    $tanggal   = date('Y-m-d', strtotime($b['tanggal']));
    $deskripsi = isset($b['deskripsi']) ? trim($b['deskripsi']) : '';
    $curr      = $bank[$akun]['curr'];

    // uang masuk ke akun rupiah tidak menyentuh kurs
    $kurs = cfp_kurs_pajak($conn1, $tanggal, 'USD');
    $rate = ($curr == 'IDR') ? 1 : $kurs['rate'];

    $amount_idr = ($curr == 'IDR') ? $amount : $amount * $rate;
    $amount_usd = ($curr == 'IDR') ? 0 : $amount;

    if (!isset($doc[$tanggal])) {
        $doc[$tanggal] = cfp_no_doc_berikut($conn2, 'OTHER INFLOW', $bank[$akun]['kode_pc'], $tanggal);
    }
    $no_doc = $doc[$tanggal];

    $sql = "insert into ap_cf_projection
            (no_doc, jenis, type_report, tanggal, akun_tujuan, pc_tujuan, nama_supplier, id_supplier, kategori, deskripsi,
             curr, amount, rate, amount_idr, amount_usd, curr_tujuan, amount_tujuan,
             status, created_by, created_date)
            values ('" . mysqli_real_escape_string($conn2, $no_doc) . "',
                    'OTHER INFLOW',
                    '$type_report',
                    '" . mysqli_real_escape_string($conn2, $tanggal) . "',
                    '" . mysqli_real_escape_string($conn2, $akun) . "',
                    '" . mysqli_real_escape_string($conn2, $bank[$akun]['nama_pc']) . "',
                    '" . mysqli_real_escape_string($conn2, $nama) . "',
                    " . ($id_cus !== '' ? (int) $id_cus : 'NULL') . ",
                    '" . mysqli_real_escape_string($conn2, $ktg) . "',
                    '" . mysqli_real_escape_string($conn2, $deskripsi) . "',
                    '" . mysqli_real_escape_string($conn2, $curr) . "',
                    " . $amount . ", " . $rate . ",
                    " . round($amount_idr, 2) . ", " . round($amount_usd, 2) . ",
                    '" . mysqli_real_escape_string($conn2, $curr) . "',
                    " . $amount . ",
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
    /* no_doc tetap dikirim sbg SATU teks supaya layar lama tidak rusak;
       no_doc_list dipakai layar baru untuk menampilkannya satu per satu. */
    $daftar = array_values($doc);
    echo json_encode(array('status' => 'ok', 'jml' => $jml,
                           'no_doc' => implode(', ', $daftar),
                           'no_doc_list' => $daftar));
} else {
    mysqli_rollback($conn2);
    echo json_encode(array('status' => 'gagal', 'pesan' => $pesan));
}
mysqli_autocommit($conn2, true);
