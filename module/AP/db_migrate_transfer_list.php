<?php
// Jalankan sekali lewat browser:
// http://localhost/ap_dev/module/AP/db_migrate_transfer_list.php
include '../../conn/conn.php';

$tableH = "CREATE TABLE IF NOT EXISTS pv_transfer_list_h (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tl_number VARCHAR(50) NOT NULL UNIQUE,
    tl_date DATE NOT NULL,
    tipe_mutasi VARCHAR(20) NOT NULL,
    jenis_otorisasi VARCHAR(20) NOT NULL,
    tanggal_efektif DATE NOT NULL,
    jenis_biaya VARCHAR(10) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Draft',
    created_by VARCHAR(50),
    created_date DATETIME,
    cancel_by VARCHAR(50),
    cancel_date DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$tableDet = "CREATE TABLE IF NOT EXISTS pv_transfer_list_det (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tl_number VARCHAR(50) NOT NULL,
    pl_number VARCHAR(50) NOT NULL,
    KEY idx_tl_number (tl_number),
    KEY idx_pl_number (pl_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

foreach (['pv_transfer_list_h' => $tableH, 'pv_transfer_list_det' => $tableDet] as $name => $ddl) {
    if (mysqli_query($conn2, $ddl)) {
        echo "OK: table $name siap.<br>";
    } else {
        echo "Error table $name: " . mysqli_error($conn2) . "<br>";
    }
}

$items = [
    [130, 'Transfer List'],
    [131, 'Create Transfer List'],
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
