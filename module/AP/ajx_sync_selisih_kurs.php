<?php
include '../../conn/conn.php';
require __DIR__ . '/fxsk_functions.php';
header('Content-Type: application/json');

// Trigger MANUAL (tombol Sync di memorial-journal.php) - proses sama persis
// dengan sync_selisih_kurs_scheduler.php (fxSkRunAllAccounts, s.d. HARI INI,
// semua akun USD aktif, insert-or-update per tanggal) - file terpisah,
// sengaja TIDAK digabung ke jurnal_meals.php (jadwal/concern beda). Dipanggil
// manual kapan saja aman diulang - idempotent.
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

echo json_encode([
    'ok'           => empty($errors),
    'end_date'     => date('d-M-Y', strtotime($endDate)),
    'total_jurnal' => $totalJurnal,
    'total_insert' => $totalInsert,
    'total_update' => $totalUpdate,
    'per_account'  => $perAccount,
    'errors'       => $errors,
]);
