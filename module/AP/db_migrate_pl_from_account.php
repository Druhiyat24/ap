<?php
// Jalankan file ini sekali untuk menambah kolom from_account ke pv_payment_list_h.
// Akses lewat browser: http://localhost/ap_dev/module/AP/db_migrate_pl_from_account.php
include '../../conn/conn.php';

$sql = "ALTER TABLE pv_payment_list_h ADD COLUMN IF NOT EXISTS from_account VARCHAR(100) NULL AFTER deskripsi";
if (mysqli_query($conn2, $sql)) {
    echo 'OK: Kolom from_account berhasil ditambahkan ke pv_payment_list_h (atau sudah ada).';
} else {
    echo 'Error: ' . mysqli_error($conn2);
}
mysqli_close($conn2);
?>
