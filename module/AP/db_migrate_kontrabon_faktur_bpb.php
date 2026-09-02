<?php
// Migrasi: detail Faktur & BPB untuk Kontrabon New.
//   ir_kontrabon_faktur : 1 invoice (ir_kontrabon_inv) -> banyak faktur
//   ir_kontrabon_bpb    : 1 faktur -> banyak BPB
// Dihubungkan pakai id: faktur.inv_id -> kontrabon_new_inv.id,
// bpb.faktur_id -> kontrabon_new_faktur.id. Idempoten.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

function tableExists($conn, $t)
{
    $r = mysqli_query($conn, "SHOW TABLES LIKE '" . mysqli_real_escape_string($conn, $t) . "'");
    return $r && mysqli_num_rows($r) > 0;
}

if (!tableExists($conn2, 'ir_kontrabon_faktur')) {
    $ddl = "CREATE TABLE ir_kontrabon_faktur (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        inv_id      INT NULL,
        unik_code   VARCHAR(30) NULL,
        no_faktur   VARCHAR(100) NULL,
        create_user VARCHAR(100) NULL,
        create_date DATETIME NULL,
        KEY idx_inv (inv_id),
        KEY idx_unik (unik_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    echo mysqli_query($conn2, $ddl) ? "OK: ir_kontrabon_faktur dibuat\n" : "ERR faktur: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: ir_kontrabon_faktur sudah ada\n";
}

if (!tableExists($conn2, 'ir_kontrabon_bpb')) {
    $ddl = "CREATE TABLE ir_kontrabon_bpb (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        faktur_id   INT NULL,
        inv_id      INT NULL,
        unik_code   VARCHAR(30) NULL,
        no_bpb      VARCHAR(100) NULL,
        create_user VARCHAR(100) NULL,
        create_date DATETIME NULL,
        KEY idx_faktur (faktur_id),
        KEY idx_inv (inv_id),
        KEY idx_unik (unik_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    echo mysqli_query($conn2, $ddl) ? "OK: ir_kontrabon_bpb dibuat\n" : "ERR bpb: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: ir_kontrabon_bpb sudah ada\n";
}
mysqli_close($conn2);
