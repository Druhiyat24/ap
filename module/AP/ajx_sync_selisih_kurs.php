<?php
include '../../conn/conn.php';
require __DIR__ . '/fxsk_functions.php';
require __DIR__ . '/fxap_functions.php';
require __DIR__ . '/fxar_functions.php';
header('Content-Type: application/json');

// Trigger MANUAL (tombol FX Revaluation di memorial-journal.php) - proses sama
// persis dengan sync_selisih_kurs_scheduler.php yang jalan tiap malam, dan
// mengerjakan TIGA hal yang sama berurutan:
//   1. BANK - saldo rekening USD      (fxsk_functions.php)
//   2. AP   - saldo utang usaha USD   (fxap_functions.php)
//   3. AR   - saldo piutang usaha USD (fxar_functions.php)
// Ditambahkan AP & AR pada 25 Nov 2026 - sebelumnya tombol ini hanya
// mengerjakan bank, padahal scheduler malamnya sudah bertiga.
//
// Tiap bagian dibungkus try/catch sendiri, jadi kalau AP atau AR bermasalah
// hasil bank yang sudah tersimpan tidak ikut batal. Dipanggil manual kapan
// saja aman diulang - idempotent.
//
// Hitungan AP mundur sampai Juli dan AR sampai Oktober, jadi sekali tekan bisa
// makan belasan detik; batas waktu PHP dilonggarkan supaya tidak terputus di
// tengah jalan.
@set_time_limit(600);
$user = isset($_POST['user']) ? trim($_POST['user']) : 'MANUAL SYNC';
$endDate = date('Y-m-d');

$summary = fxSkRunAllAccounts($conn1, $conn2, $endDate, $user);

$totalJurnal = 0;
$totalInsert = 0;
$totalUpdate = 0;
$errors = [];
$perAccount = [];

foreach ($summary as $acc) {
    if (isset($acc['error'])) {
        $errors[] = $acc['account'] . ': ' . $acc['error'];
        continue;
    }
    $count = count($acc['created']);
    $totalJurnal += $count;
    foreach ($acc['created'] as $c) {
        if ($c['action'] === 'insert') {
            $totalInsert++;
        } else {
            $totalUpdate++;
        }
    }
    $perAccount[] = [
        'account' => $acc['account'],
        'count'   => $count,
    ];
}


// ------------------------------------------------------------- AP & AR --
// Dua-duanya berbentuk sama: hitung seluruh rentang, lalu post dalam satu
// transaksi. Yang beda cuma mesinnya, jadi dirangkai lewat satu daftar.
$ringkas = [];

$bagian = [
    ['kode' => 'ap', 'nama' => 'AP', 'kolom' => 'forex_ap',
     'migrasi' => 'db_migrate_LIVE_20261124_forex_ap_coa.sql',
     'siap' => 'fxApSiap', 'hitung' => 'fxApHitung', 'post' => 'fxApPost', 'awal' => FXAP_MIN_DATE],
    ['kode' => 'ar', 'nama' => 'AR', 'kolom' => 'forex_ar',
     'migrasi' => 'db_migrate_LIVE_20261124_forex_ar_coa.sql',
     'siap' => 'fxArSiap', 'hitung' => 'fxArHitung', 'post' => 'fxArPost', 'awal' => FXAR_MIN_DATE],
];

foreach ($bagian as $b) {
    $hasilBagian = ['insert' => 0, 'update' => 0, 'removed' => 0, 'closed' => [], 'tanpa_coa' => []];
    try {
        if (!call_user_func($b['siap'], $conn1)) {
            // Kode sudah naik tapi migrasinya belum dijalankan - dikatakan
            // terang-terangan, jangan sampai terbaca sebagai "tidak ada selisih".
            throw new Exception('kolom mastercoa_v2.' . $b['kolom'] . ' belum ada - jalankan ' . $b['migrasi'] . ' dulu');
        }

        $hitung = call_user_func($b['hitung'], $conn1, $b['awal'], $endDate);

        if (!empty($hitung['dokumen'])) {
            $post = call_user_func($b['post'], $conn1, $conn2, $hitung, $user);
            if (!$post['ok']) {
                throw new Exception($post['message']);
            }
            foreach ($post['dokumen'] as $d) {
                if ($d['baris'] == 0) {
                    $hasilBagian['removed']++;        // dokumen basi, barisnya dihapus
                } elseif ($d['aksi'] === 'insert') {
                    $hasilBagian['insert']++;
                } else {
                    $hasilBagian['update']++;
                }
            }
        }

        // Tanggal yang ada selisihnya tapi periodenya sudah ditutup - harus
        // kelihatan, bukan hilang diam-diam.
        $tutup = [];
        foreach ($hitung['baris'] as $r) {
            if (!empty($r['periode_tutup'])) { $tutup[substr($r['tanggal'], 0, 7)] = true; }
        }
        ksort($tutup);
        $hasilBagian['closed'] = array_keys($tutup);

        // Khusus AR: faktur yang COA-nya tidak ketemu di jurnal.
        if (!empty($hitung['tanpa_coa'])) {
            foreach ($hitung['tanpa_coa'] as $pcx => $vx) {
                $hasilBagian['tanpa_coa'][] = $pcx . ' ' . number_format($vx, 2, ',', '.') . ' USD';
            }
        }

        $hasilBagian['ok'] = true;
    } catch (Throwable $e) {
        $hasilBagian['ok'] = false;
        $hasilBagian['message'] = $e->getMessage();
        $errors[] = $b['nama'] . ': ' . $e->getMessage();
    }
    $ringkas[$b['kode']] = $hasilBagian;
}

echo json_encode([
    'ok'           => empty($errors),
    'end_date'     => date('d-M-Y', strtotime($endDate)),
    'total_jurnal' => $totalJurnal,
    'total_insert' => $totalInsert,
    'total_update' => $totalUpdate,
    'per_account'  => $perAccount,
    'ap'           => $ringkas['ap'],
    'ar'           => $ringkas['ar'],
    'errors'       => $errors,
]);
