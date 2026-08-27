<?php
// Migrasi satu-kali: menambah 3 menurole baru untuk menu Financial Statement
// terpadu (financial_statement.php) yang menggantikan menu lama
// Trial Balance / Financial Statement (FS1) / Financial Statement 2 (FS2).
// Idempoten: dilewati kalau nama menu sudah ada. Pola sama dgn db_migrate_reverse_*.php
// (menurole = kolom id + menu), tapi id dihitung otomatis (MAX+1) supaya tidak
// bentrok. Jalankan sekali lewat browser, lalu boleh dihapus.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

$menus = [
    'FS - Trial Balance Only', // akses hanya tab Trial Balance
    'FS - ALL',                // akses seluruh tab (SFP/SPL/CF Direct/CF Indirect + FS1/FS2)
    'FS - Copy Saldo',         // izin tombol Copy Saldo
];

foreach ($menus as $menu) {
    $menu_esc = mysqli_real_escape_string($conn2, $menu);
    $chk = mysqli_query($conn2, "SELECT id FROM menurole WHERE menu = '$menu_esc'");
    if ($chk && mysqli_num_rows($chk) > 0) {
        $row = mysqli_fetch_assoc($chk);
        echo "SKIP: '$menu' sudah ada (id={$row['id']}).\n";
        continue;
    }
    $r = mysqli_query($conn2, "SELECT COALESCE(MAX(id),0)+1 AS nid FROM menurole");
    $nid = (int) mysqli_fetch_assoc($r)['nid'];
    $ins = mysqli_query($conn2, "INSERT INTO menurole (id, menu) VALUES ($nid, '$menu_esc')");
    echo $ins ? "OK: id=$nid ('$menu')\n" : "ERROR '$menu': " . mysqli_error($conn2) . "\n";
}

mysqli_close($conn2);
