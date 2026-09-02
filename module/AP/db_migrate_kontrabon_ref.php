<?php
// Migrasi: master Nomor Reff Kontrabon (nomor bon kertas yang dibeli dari
// supplier). Harus diinput/di-upload dulu (per buku) sebelum dipakai saat
// membuat Kontrabon New, supaya nomor bon tidak salah.
// ref_number UNIQUE -> tidak bisa didaftarkan dobel. status Available/Used
// (Used dipakai nanti saat nomornya terpakai di sebuah kontrabon).
// Idempoten. Jalankan sekali lewat browser.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

function tableExists($conn, $t)
{
    $r = mysqli_query($conn, "SHOW TABLES LIKE '" . mysqli_real_escape_string($conn, $t) . "'");
    return $r && mysqli_num_rows($r) > 0;
}

if (!tableExists($conn2, 'ir_kontrabon_ref')) {
    $ddl = "CREATE TABLE ir_kontrabon_ref (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        ref_number VARCHAR(50) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'Available',
        keterangan VARCHAR(150) NULL,
        create_user VARCHAR(100) NULL,
        create_date DATETIME NULL,
        UNIQUE KEY uq_ref_number (ref_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    echo mysqli_query($conn2, $ddl) ? "OK: tabel ir_kontrabon_ref dibuat\n" : "ERR: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: tabel ir_kontrabon_ref sudah ada\n";
}

echo "\nStruktur:\n";
$r = mysqli_query($conn2, "SHOW COLUMNS FROM ir_kontrabon_ref");
while ($x = mysqli_fetch_assoc($r)) {
    echo "  " . str_pad($x['Field'], 14) . " | " . str_pad($x['Type'], 14) . " | " . $x['Key'] . "\n";
}
mysqli_close($conn2);
