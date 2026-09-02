<?php
// ============================================================================
// Migrasi: menu "Invoice Received".
// Menggantikan submenu lama "Kontrabon New" yang dulu HANYA tampil untuk
// dev/tester lewat hardcode username (indro/willy/steven). Sekarang menu ini
// digating oleh menurole 'Invoice Received' -> bisa di-assign per user lewat
// Manage User Role (userrole.php membaca daftar menu langsung dari menurole).
//
// Langkah:
//   1) INSERT 1 baris menurole (menu='Invoice Received') kalau belum ada.
//      display_name & menu_group diisi supaya rapi di modal Manage User Role.
//   2) GRANT ke user lama (indro/willy/steven) supaya akses tidak hilang
//      setelah gate hardcode dihapus dari header/index.
//
// Menu ini SUBMENU biasa (bukan landing) -> status dibiarkan NULL, sama seperti
// 'Kontrabon' (id 6) & 'Payment Voucher AP' (id 135).
//
// Idempoten (boleh dijalankan berkali-kali). Jalankan sekali via browser:
//   http://localhost/ap_dev/module/AP/db_migrate_invoice_received_menu.php
// CATATAN: replay juga di server produksi saat deploy.
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

$MENU = 'Invoice Received';

// Cek keberadaan kolom (schema bisa beda antar environment).
function columnExists($conn, $table, $col) {
    $col_esc = mysqli_real_escape_string($conn, $col);
    $r = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$col_esc'");
    return $r && mysqli_num_rows($r) > 0;
}

$menu_esc = mysqli_real_escape_string($conn2, $MENU);

// ---- 1) menurole: insert kalau belum ada (guard by nama) ----
$chk = mysqli_query($conn2, "SELECT id FROM menurole WHERE menu = '$menu_esc'");
if ($chk && mysqli_num_rows($chk) > 0) {
    $row = mysqli_fetch_assoc($chk);
    echo "SKIP menurole: '$MENU' sudah ada (id=" . $row['id'] . ").\n";
} else {
    $r   = mysqli_query($conn2, "SELECT COALESCE(MAX(id),0)+1 AS nid FROM menurole");
    $nid = (int) mysqli_fetch_assoc($r)['nid'];

    // Susun kolom & nilai dinamis sesuai kolom yang tersedia.
    $cols = ['id', 'menu'];
    $vals = [$nid, "'$menu_esc'"];
    if (columnExists($conn2, 'menurole', 'display_name'))  { $cols[] = 'display_name';  $vals[] = "'$menu_esc'"; }
    if (columnExists($conn2, 'menurole', 'menu_group'))    { $cols[] = 'menu_group';    $vals[] = "'AP'"; }
    if (columnExists($conn2, 'menurole', 'profit_center')) { $cols[] = 'profit_center'; $vals[] = "'NAG'"; }

    $sql = "INSERT INTO menurole (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ")";
    if (mysqli_query($conn2, $sql)) {
        echo "OK menurole: '$MENU' ditambahkan (id=$nid).\n";
    } else {
        echo "ERROR menurole: " . mysqli_error($conn2) . "\n";
    }
}

// ---- 2) useraccess: grant ke user lama supaya akses tetap ada ----
$grantUsers  = ['indro', 'willy', 'steven'];
$create_user = 'system';
$now         = date('Y-m-d H:i:s');

foreach ($grantUsers as $u) {
    $u_esc = mysqli_real_escape_string($conn2, $u);

    $chkUa = mysqli_query($conn2, "SELECT id FROM useraccess WHERE username = '$u_esc' AND menu = '$menu_esc'");
    if ($chkUa && mysqli_num_rows($chkUa) > 0) {
        echo "SKIP useraccess: '$u' sudah punya '$MENU'.\n";
        continue;
    }

    // Ambil fullname dari row useraccess user tsb yang sudah ada (kalau ada).
    $fullname = $u;
    $fq = mysqli_query($conn2, "SELECT fullname FROM useraccess WHERE username = '$u_esc' AND fullname IS NOT NULL AND fullname <> '' LIMIT 1");
    if ($fq && mysqli_num_rows($fq) > 0) {
        $fr = mysqli_fetch_assoc($fq);
        if (!empty($fr['fullname'])) { $fullname = $fr['fullname']; }
    }
    $fullname_esc = mysqli_real_escape_string($conn2, $fullname);

    $ins = mysqli_query($conn2, "INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
        VALUES ('$u_esc', '$fullname_esc', '$menu_esc', '$now', '$create_user')");
    echo $ins ? "OK useraccess: '$u' diberi akses '$MENU'.\n"
              : "ERROR useraccess ($u): " . mysqli_error($conn2) . "\n";
}

echo "\nSelesai. Menu 'Invoice Received' siap. User lain bisa diberi akses lewat Manage User Role (userrole.php).\n";
mysqli_close($conn2);
