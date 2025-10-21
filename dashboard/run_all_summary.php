<?php
function run_php_remote($url, $nama_file)
{
    echo "▶️ Menjalankan: $nama_file ...<br>";

    $start = microtime(true); // mulai waktu

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 180); // timeout 3 menit
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    $end = microtime(true); // selesai waktu
    $duration = round($end - $start, 2);

    if ($output === false || $httpcode !== 200) {
        echo "❌ Gagal: $nama_file";
        if ($err) echo " (Error: $err)";
        echo " ⏱️ Waktu: {$duration}s<br><br>";
    } else {
        echo "✅ Berhasil: $nama_file ⏱️ Waktu: {$duration}s<br><br>";
    }

    ob_flush();
    flush();
}

$base_url = 'http://localhost/ap/module/ap/';

run_php_remote($base_url . 'dashboard-bpb.php', 'dashboard-bpb.php');
run_php_remote($base_url . 'dashboard-kbon.php', 'dashboard-kbon.php');
run_php_remote($base_url . 'dashboard-lp.php', 'dashboard-lp.php');
run_php_remote($base_url . 'dashboard-purchase.php', 'dashboard-purchase.php');
run_php_remote($base_url . 'dashboard-retur.php', 'dashboard-retur.php');
?>
