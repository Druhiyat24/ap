<?php
// Migrasi: tabel Kontrabon New (base) - header + invoice detail.
//   ir_kontrabon_h   : header kontrabon (1 dokumen)
//   ir_kontrabon_inv : invoice yang ditambahkan (1 header -> banyak invoice)
// Faktur & BPB (ir_kontrabon_faktur / ir_kontrabon_bpb) menyusul di iterasi
// berikutnya. Dihubungkan lewat unik_code (pola sama dgn ir_invoice_supp).
// Idempoten. Jalankan sekali lewat browser.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

function tableExists($conn, $t)
{
    $r = mysqli_query($conn, "SHOW TABLES LIKE '" . mysqli_real_escape_string($conn, $t) . "'");
    return $r && mysqli_num_rows($r) > 0;
}

if (!tableExists($conn2, 'ir_kontrabon_h')) {
    $ddl = "CREATE TABLE ir_kontrabon_h (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        doc_number     VARCHAR(30) NULL,
        kontrabon_date DATE NULL,
        unik_code      VARCHAR(30) NULL,
        no_reff        VARCHAR(50) NULL,
        nama_supp      VARCHAR(150) NULL,
        deskripsi      TEXT NULL,
        total_amount   DECIMAL(20,2) NULL DEFAULT 0,
        status         VARCHAR(20) NOT NULL DEFAULT 'Draft',
        create_user    VARCHAR(100) NULL,
        create_date    DATETIME NULL,
        KEY idx_doc (doc_number),
        KEY idx_unik (unik_code),
        KEY idx_reff (no_reff)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    echo mysqli_query($conn2, $ddl) ? "OK: ir_kontrabon_h dibuat\n" : "ERR h: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: ir_kontrabon_h sudah ada\n";
}

if (!tableExists($conn2, 'ir_kontrabon_inv')) {
    $ddl = "CREATE TABLE ir_kontrabon_inv (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        unik_code   VARCHAR(30) NULL,
        doc_number  VARCHAR(30) NULL,
        no_inv      VARCHAR(100) NULL,
        tgl_inv     DATE NULL,
        amount      DECIMAL(20,2) NULL DEFAULT 0,
        create_user VARCHAR(100) NULL,
        create_date DATETIME NULL,
        KEY idx_unik (unik_code),
        KEY idx_doc (doc_number),
        KEY idx_noinv (no_inv)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    echo mysqli_query($conn2, $ddl) ? "OK: ir_kontrabon_inv dibuat\n" : "ERR inv: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: ir_kontrabon_inv sudah ada\n";
}

echo "\nStruktur ir_kontrabon_h:\n";
$r = mysqli_query($conn2, "SHOW COLUMNS FROM ir_kontrabon_h");
while ($x = mysqli_fetch_assoc($r)) echo "  " . str_pad($x['Field'], 15) . " | " . $x['Type'] . "\n";
mysqli_close($conn2);
