<?php
// Standalone scheduler entry point for the FX (selisih kurs) revaluation
// journals - intentionally kept separate from jurnal_meals.php so the
// two concerns can be scheduled/monitored independently.
//
// MENGERJAKAN TIGA HAL dalam satu panggilan, supaya cukup SATU jadwal di Task
// Scheduler (diminta 24 Nov 2026 - biar tidak perlu bikin jadwal baru):
//   1. Selisih kurs BANK - saldo rekening USD      (fxsk_functions.php)
//   2. Selisih kurs AP   - saldo utang usaha USD   (fxap_functions.php)
//   3. Selisih kurs AR   - saldo piutang usaha USD (fxar_functions.php)
// Bagian AP dan AR dijalankan SETELAH bank, masing-masing dibungkus
// try/catch sendiri, jadi kalau salah satu bermasalah hasil yang sudah
// tersimpan tidak ikut batal.
//
// Safe to run repeatedly (insert-or-update per date, never duplicates).
// Always processes THROUGH TODAY (not H-1) for every active USD bank account -
// intended to be scheduled late at night (e.g. 23:00) once the current day's
// data is effectively final, so there's no need to lag a day behind.
//
// How to trigger:
//  - HTTP: schedule a hit to this file's URL (e.g. via curl/Invoke-WebRequest
//    in Task Scheduler) - recommended on this server, since the local
//    php-cli's extensions don't match what Apache/mod_php uses.
//  - CLI: php sync_selisih_kurs_scheduler.php (fine if the production
//    server's php-cli has working mysqli, unlike this dev environment).

date_default_timezone_set('Asia/Jakarta');

$db_host = "10.10.5.12";
$db_user = "root";
$db_pass = "ERP@S19n4lB1t";
$db_name = "signalbit_erp";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Database connection failed\n");
}
mysqli_set_charset($conn, "utf8");

require __DIR__ . '/fxsk_functions.php';

$endDate = date('Y-m-d');
$summary = fxSkRunAllAccounts($conn, $conn, $endDate, 'SCHEDULER');

$totalInsert = 0;
$totalUpdate = 0;

foreach ($summary as $acc) {
    if (isset($acc['error'])) {
        echo "FX " . $acc['account'] . ": FAILED - " . $acc['error'] . "\n";
        continue;
    }
    if (empty($acc['created'])) {
        echo "FX " . $acc['account'] . ": no selisih through $endDate.\n";
        continue;
    }
    foreach ($acc['created'] as $c) {
        if ($c['action'] === 'insert') {
            $totalInsert++;
        } else {
            $totalUpdate++;
        }
        echo "FX " . $acc['account'] . " " . $c['date'] . " selisih=" . $c['selisih'] . " -> " . $c['doc'] . " (" . $c['action'] . ")\n";
    }
}

echo "Bank done. Through $endDate: $totalInsert inserted, $totalUpdate updated.\n";


// ------------------------------------------------------- selisih kurs AP --
// Revaluasi saldo UTANG USAHA USD - COA yang ditandai forex_ap = 'Y' di
// mastercoa_v2 (diatur dari menu Master COA, tidak dihardcode di sini).
//
// Sifatnya sama dengan bagian bank di atas: dihitung ulang dari FXAP_MIN_DATE
// sampai hari ini setiap malam, dokumen yang sudah ada DIPERBARUI (bukan
// digandakan), dan tanggal yang periodenya sudah Closed dilewati.

echo "\n";

try {
    require __DIR__ . '/fxap_functions.php';

    if (!fxApSiap($conn)) {
        // Kode sudah naik tapi migrasinya belum dijalankan - dikatakan
        // terang-terangan, jangan sampai terbaca sebagai "tidak ada selisih".
        throw new Exception("kolom mastercoa_v2.forex_ap belum ada - jalankan db_migrate_LIVE_20261124_forex_ap_coa.sql dulu");
    }

    $hasilAp = fxApHitung($conn, FXAP_MIN_DATE, $endDate);

    if (empty($hasilAp['dokumen'])) {
        echo "AP: no selisih through $endDate.\n";
    } else {
        $postAp = fxApPost($conn, $conn, $hasilAp, 'SCHEDULER');
        if (!$postAp['ok']) {
            echo "AP: FAILED - " . $postAp['message'] . "\n";
        } else {
            $apInsert = 0;
            $apUpdate = 0;
            $apHapus  = 0;
            foreach ($postAp['dokumen'] as $d) {
                if ($d['baris'] == 0) {
                    // Dokumen basi - baris lamanya dihapus, tidak ditulis ulang.
                    $apHapus++;
                    echo "AP " . $d['tanggal'] . " " . $d['pc'] . " -> " . $d['doc'] . " (removed)\n";
                    continue;
                }
                if ($d['aksi'] === 'insert') {
                    $apInsert++;
                } else {
                    $apUpdate++;
                }
                echo "AP " . $d['tanggal'] . " " . $d['pc'] . " net=" . $d['net'] . " -> " . $d['doc'] . " (" . $d['aksi'] . ")\n";
            }
            echo "AP done. Through $endDate: $apInsert inserted, $apUpdate updated, $apHapus removed.\n";
        }
    }

    // Tanggal yang ada selisihnya tapi periodenya sudah ditutup - dilaporkan
    // supaya ketahuan, bukan hilang diam-diam.
    $tutup = array();
    foreach ($hasilAp['baris'] as $b) {
        if (!empty($b['periode_tutup'])) { $tutup[substr($b['tanggal'], 0, 7)] = true; }
    }
    if ($tutup) {
        ksort($tutup);
        echo "AP WARNING: period closed, skipped - " . implode(', ', array_keys($tutup)) . "\n";
    }
} catch (Throwable $e) {
    echo "AP: FAILED - " . $e->getMessage() . "\n";
}

// ------------------------------------------------------- selisih kurs AR --
// Revaluasi saldo PIUTANG USAHA USD - COA yang ditandai forex_ar = 'Y' di
// mastercoa_v2 (diatur dari menu Master COA, tidak dihardcode di sini).
//
// Bentuknya sengaja dibuat sama persis dengan bagian AP di atas supaya
// keluaran lognya mudah dibaca berdampingan. Bedanya cuma di mesinnya:
// AR itu akun ASET (arah debit/kredit kebalikan AP), saldo awalnya dari
// Kartu AR, dan kursnya memakai HARIAN di tanggal akhir bulan.

echo "
";

try {
    require __DIR__ . '/fxar_functions.php';

    if (!fxArSiap($conn)) {
        // Kode sudah naik tapi migrasinya belum dijalankan - dikatakan
        // terang-terangan, jangan sampai terbaca sebagai "tidak ada selisih".
        throw new Exception("kolom mastercoa_v2.forex_ar belum ada - jalankan db_migrate_LIVE_20261124_forex_ar_coa.sql dulu");
    }

    $hasilAr = fxArHitung($conn, FXAR_MIN_DATE, $endDate);

    if (empty($hasilAr['dokumen'])) {
        echo "AR: no selisih through $endDate.
";
    } else {
        $postAr = fxArPost($conn, $conn, $hasilAr, 'SCHEDULER');
        if (!$postAr['ok']) {
            echo "AR: FAILED - " . $postAr['message'] . "
";
        } else {
            $arInsert = 0;
            $arUpdate = 0;
            $arHapus  = 0;
            foreach ($postAr['dokumen'] as $d) {
                if ($d['baris'] == 0) {
                    // Dokumen basi - baris lamanya dihapus, tidak ditulis ulang.
                    $arHapus++;
                    echo "AR " . $d['tanggal'] . " " . $d['pc'] . " -> " . $d['doc'] . " (removed)
";
                    continue;
                }
                if ($d['aksi'] === 'insert') {
                    $arInsert++;
                } else {
                    $arUpdate++;
                }
                echo "AR " . $d['tanggal'] . " " . $d['pc'] . " net=" . $d['net'] . " -> " . $d['doc'] . " (" . $d['aksi'] . ")
";
            }
            echo "AR done. Through $endDate: $arInsert inserted, $arUpdate updated, $arHapus removed.
";
        }
    }

    // Faktur yang COA-nya tidak ketemu di jurnal - saldonya tidak ikut
    // terhitung, jadi harus kelihatan di log.
    if (!empty($hasilAr['tanpa_coa'])) {
        $sisa = array();
        foreach ($hasilAr['tanpa_coa'] as $pcx => $vx) { $sisa[] = $pcx . ' ' . number_format($vx, 2, '.', '') . ' USD'; }
        echo "AR WARNING: faktur tanpa COA, tidak ikut saldo awal - " . implode(', ', $sisa) . "
";
    }

    // Tanggal yang ada selisihnya tapi periodenya sudah ditutup - dilaporkan
    // supaya ketahuan, bukan hilang diam-diam.
    $tutupAr = array();
    foreach ($hasilAr['baris'] as $b) {
        if (!empty($b['periode_tutup'])) { $tutupAr[substr($b['tanggal'], 0, 7)] = true; }
    }
    if ($tutupAr) {
        ksort($tutupAr);
        echo "AR WARNING: period closed, skipped - " . implode(', ', array_keys($tutupAr)) . "
";
    }
} catch (Throwable $e) {
    echo "AR: FAILED - " . $e->getMessage() . "
";
}

mysqli_close($conn);
