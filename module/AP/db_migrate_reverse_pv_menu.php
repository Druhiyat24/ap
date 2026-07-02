<?php
// Jalankan sekali lewat browser:
// http://localhost/ap_dev/module/AP/db_migrate_reverse_pv_menu.php
include '../../conn/conn.php';

$items = [
    [120, 'Reverse Payment Voucher'],
    [121, 'Reverse Payment Voucher List'],
    [122, 'Reverse Payment List'],
];

foreach ($items as [$id, $menu]) {
    $id_esc   = (int) $id;
    $menu_esc = mysqli_real_escape_string($conn2, $menu);

    $check = mysqli_query($conn2, "SELECT id FROM menurole WHERE id = $id_esc");
    if (mysqli_num_rows($check) > 0) {
        echo "SKIP: id=$id_esc ($menu) sudah ada.<br>";
        continue;
    }

    $ins = mysqli_query($conn2, "INSERT INTO menurole (id, menu) VALUES ($id_esc, '$menu_esc')");
    if ($ins) {
        echo "OK: id=$id_esc ($menu) berhasil ditambahkan.<br>";
    } else {
        echo "Error id=$id_esc: " . mysqli_error($conn2) . "<br>";
    }
}

mysqli_close($conn2);
?>
