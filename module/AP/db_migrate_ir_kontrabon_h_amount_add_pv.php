<?php
// Migrasi idempoten: tambah kolom terpisah `amount_add_pv` di ir_kontrabon_h.
// Menampung NET nilai yang ditambahkan saat create Payment Voucher:
//   + BPB tambahan (tidak ada di IR asli)   ,  - RO/retur
// total_amount (invoice asli) TIDAK diubah. Grand total = total_amount + amount_add_pv.
// JALANKAN JUGA DI PRODUKSI.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

$chk = mysqli_query($conn2, "SELECT COLUMN_NAME FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ir_kontrabon_h' AND COLUMN_NAME = 'amount_add_pv'");
if ($chk && mysqli_num_rows($chk) > 0) {
    echo "SKIP: kolom amount_add_pv sudah ada.\n";
} else {
    $ok = mysqli_query($conn2, "ALTER TABLE ir_kontrabon_h
        ADD COLUMN amount_add_pv DECIMAL(20,2) NOT NULL DEFAULT 0.00 AFTER total_amount");
    echo $ok ? "OK: kolom amount_add_pv ditambahkan.\n" : ("GAGAL: " . mysqli_error($conn2) . "\n");
}
mysqli_close($conn2);
