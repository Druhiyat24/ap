<?php
/* Simpan baris PV Expense. Dipanggil AJAX dari pv_form.php.
   Satu kali Save = SATU dokumen, nomornya EXP/<PC>/<mmyy>/<00001>,
   berapa pun barisnya. Nomor itu pula yang nanti tampil di kolom
   "Payment Voucher No" pada laporan proyeksi.
   Semuanya dalam satu transaksi: gagal satu, batal semua.
   Polanya sama persis dgn save_other_inflow.php. */

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

// bank & kategori dibaca ulang di server - jangan percaya kiriman layar.
// Kategorinya sisi Cash Out, beda dgn Other Inflow yang Cash In.
$bank     = cfp_daftar_bank($conn1);
$kategori = cfp_daftar_kategori($conn1, 'Cash Out');

mysqli_autocommit($conn2, false);
$no_doc = '';
$jml    = 0;
$aman   = true;
$pesan  = '';

foreach ($baris as $b) {
    $akun = isset($b['bank']) ? trim($b['bank']) : '';
    $nama = isset($b['nama']) ? trim($b['nama']) : '';
    $ktg  = isset($b['kategori']) ? trim($b['kategori']) : '';

    // Supplier boleh dipilih dari master (id terisi) atau diketik bebas
    // (id kosong - mis. baris departemen atau periode gaji). Kalau id-nya
    // ada, namanya dibaca ULANG dari master; teks dari layar diabaikan.
    $id_sup = isset($b['id_supplier']) ? trim((string) $b['id_supplier']) : '';
    if ($id_sup !== '') {
        $nama_master = cfp_nama_master($conn1, $id_sup);
        if ($nama_master === '') {
            $aman = false; $pesan = 'Unknown supplier id: ' . $id_sup;
            break;
        }
        $nama = $nama_master;
    }

    if (!isset($bank[$akun])) {
        $aman = false; $pesan = 'Unknown bank account: ' . $akun;
        break;
    }
    if ($nama == '') {
        $aman = false; $pesan = 'Supplier Name is required.';
        break;
    }
    if (!in_array($ktg, $kategori)) {
        $aman = false; $pesan = 'Unknown cost category: ' . $ktg;
        break;
    }

    $amount = (float) $b['amount'];
    if ($amount <= 0) {
        $aman = false; $pesan = 'Amount must be greater than 0.';
        break;
    }

    $tanggal   = date('Y-m-d', strtotime($b['tanggal']));   // Due Date
    $deskripsi = isset($b['deskripsi']) ? trim($b['deskripsi']) : '';
    $curr      = $bank[$akun]['curr'];

    // uang keluar dari akun rupiah tidak menyentuh kurs
    $kurs = cfp_kurs_pajak($conn1, $tanggal, 'USD');
    $rate = ($curr == 'IDR') ? 1 : $kurs['rate'];

    $amount_idr = ($curr == 'IDR') ? $amount : $amount * $rate;
    $amount_usd = ($curr == 'IDR') ? 0 : $amount;

    if ($no_doc == '') {
        $no_doc = cfp_no_doc_berikut($conn2, 'PV EXPENSE', $bank[$akun]['kode_pc'], $tanggal);
    }

    $sql = "insert into ap_cf_projection
            (no_doc, jenis, tanggal, akun_tujuan, pc_tujuan, nama_supplier, id_supplier, kategori, deskripsi,
             curr, amount, rate, amount_idr, amount_usd, curr_tujuan, amount_tujuan,
             status, created_by, created_date)
            values ('" . mysqli_real_escape_string($conn2, $no_doc) . "',
                    'PV EXPENSE',
                    '" . mysqli_real_escape_string($conn2, $tanggal) . "',
                    '" . mysqli_real_escape_string($conn2, $akun) . "',
                    '" . mysqli_real_escape_string($conn2, $bank[$akun]['nama_pc']) . "',
                    '" . mysqli_real_escape_string($conn2, $nama) . "',
                    " . ($id_sup !== '' ? (int) $id_sup : 'NULL') . ",
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
    echo json_encode(array('status' => 'ok', 'jml' => $jml, 'no_doc' => $no_doc));
} else {
    mysqli_rollback($conn2);
    echo json_encode(array('status' => 'gagal', 'pesan' => $pesan));
}
mysqli_autocommit($conn2, true);
