<?php
// Standalone scheduler entry point for the FX (selisih kurs) revaluation
// journal ONLY - intentionally kept separate from jurnal_meals.php so the
// two concerns can be scheduled/monitored independently.
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

echo "Done. Through $endDate: $totalInsert inserted, $totalUpdate updated.\n";

mysqli_close($conn);
