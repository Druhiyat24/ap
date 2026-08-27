<?php
// Migrasi ADITIF (non-breaking) untuk merapikan tampilan menu di modal
// "Manage User Role" (userrole.php) TANPA mengubah key permission.
//
// Menambah 2 kolom baru di menurole:
//   - display_name : nama FORMAL yang ditampilkan di modal (kalau NULL, modal
//                    fallback ke kolom `menu` yang asli - jadi 122 menu lain
//                    tidak berubah).
//   - menu_group   : kategori/grup untuk warna-dot & legend di modal (kalau
//                    NULL, modal fallback ke deteksi awalan-nama yang lama).
//
// Key internal `menu` (dipakai useraccess + semua pengecekan hak akses) TIDAK
// disentuh sama sekali, jadi nol risiko ke permission & grant yang sudah ada.
// Idempoten: aman dijalankan berkali-kali. Jalankan sekali lewat browser.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

function columnExists($conn, $table, $col) {
    $col_esc = mysqli_real_escape_string($conn, $col);
    $r = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$col_esc'");
    return $r && mysqli_num_rows($r) > 0;
}

// 1) Tambah kolom kalau belum ada
if (!columnExists($conn2, 'menurole', 'display_name')) {
    $ok = mysqli_query($conn2, "ALTER TABLE menurole ADD COLUMN display_name VARCHAR(150) NULL AFTER menu");
    echo $ok ? "OK: kolom display_name ditambahkan\n" : "ERR display_name: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: kolom display_name sudah ada\n";
}
if (!columnExists($conn2, 'menurole', 'menu_group')) {
    $ok = mysqli_query($conn2, "ALTER TABLE menurole ADD COLUMN menu_group VARCHAR(60) NULL AFTER display_name");
    echo $ok ? "OK: kolom menu_group ditambahkan\n" : "ERR menu_group: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: kolom menu_group sudah ada\n";
}

// 2) Isi nama formal + grup untuk 3 role FS (key `menu` TIDAK diubah).
//    menu_group 'Accounting' -> ikut warna/ikon kategori Accounting di modal.
$rows = [
    'FS - ALL'                 => ['Financial Statement - All Access',          'Accounting'],
    'FS - Trial Balance Only'  => ['Financial Statement - Trial Balance Only',  'Accounting'],
    'FS - Copy Saldo'          => ['Financial Statement - Copy Saldo',          'Accounting'],
];
foreach ($rows as $menu => $vals) {
    $menu_esc  = mysqli_real_escape_string($conn2, $menu);
    $disp_esc  = mysqli_real_escape_string($conn2, $vals[0]);
    $group_esc = mysqli_real_escape_string($conn2, $vals[1]);
    $ok = mysqli_query($conn2, "UPDATE menurole SET display_name = '$disp_esc', menu_group = '$group_esc' WHERE menu = '$menu_esc'");
    echo $ok
        ? "OK: '$menu' -> display_name='{$vals[0]}', menu_group='{$vals[1]}' (rows=" . mysqli_affected_rows($conn2) . ")\n"
        : "ERR '$menu': " . mysqli_error($conn2) . "\n";
}

echo "\nSelesai. Verifikasi:\n";
$r = mysqli_query($conn2, "SELECT id, menu, display_name, menu_group FROM menurole WHERE menu LIKE 'FS -%' ORDER BY id");
while ($x = mysqli_fetch_assoc($r)) {
    echo "  id={$x['id']}  menu='{$x['menu']}'  display_name='{$x['display_name']}'  menu_group='{$x['menu_group']}'\n";
}
mysqli_close($conn2);
