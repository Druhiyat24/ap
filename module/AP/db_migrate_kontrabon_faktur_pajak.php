<?php
// Migrasi ADITIF: field Faktur Pajak (hasil scan barcode faktur) untuk Kontrabon New.
// Payload scan (dipisah '#'):
//   nama_supplier # npwp_supplier # pembeli # npwp_pembeli # no_faktur #
//   tgl_faktur # dpp # ppn # ppnbm # status
// no_faktur & tgl_faktur sudah ada; sisanya ditambahkan di sini. Idempoten.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

function colExists($conn, $table, $col)
{
    $c = mysqli_real_escape_string($conn, $col);
    $r = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$c'");
    return $r && mysqli_num_rows($r) > 0;
}
function addCol($conn, $table, $col, $ddl)
{
    if (colExists($conn, $table, $col)) { echo "SKIP: $table.$col sudah ada\n"; return; }
    $ok = mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN $ddl");
    echo $ok ? "OK: $table.$col ditambahkan\n" : "ERR $table.$col: " . mysqli_error($conn) . "\n";
}

addCol($conn2, 'ir_kontrabon_faktur', 'nama_supplier', "nama_supplier VARCHAR(150) NULL AFTER tgl_faktur");
addCol($conn2, 'ir_kontrabon_faktur', 'npwp_supplier', "npwp_supplier VARCHAR(40) NULL AFTER nama_supplier");
addCol($conn2, 'ir_kontrabon_faktur', 'pembeli',       "pembeli VARCHAR(150) NULL AFTER npwp_supplier");
addCol($conn2, 'ir_kontrabon_faktur', 'npwp_pembeli',  "npwp_pembeli VARCHAR(40) NULL AFTER pembeli");
addCol($conn2, 'ir_kontrabon_faktur', 'dpp',           "dpp DECIMAL(20,2) NULL DEFAULT 0 AFTER npwp_pembeli");
addCol($conn2, 'ir_kontrabon_faktur', 'ppn',           "ppn DECIMAL(20,2) NULL DEFAULT 0 AFTER dpp");
addCol($conn2, 'ir_kontrabon_faktur', 'ppnbm',         "ppnbm DECIMAL(20,2) NULL DEFAULT 0 AFTER ppn");
addCol($conn2, 'ir_kontrabon_faktur', 'status_faktur', "status_faktur VARCHAR(30) NULL AFTER ppnbm");

echo "\nStruktur ir_kontrabon_faktur:\n";
$r = mysqli_query($conn2, "SHOW COLUMNS FROM ir_kontrabon_faktur");
while ($x = mysqli_fetch_assoc($r)) echo "  " . str_pad($x['Field'], 15) . " | " . $x['Type'] . "\n";
mysqli_close($conn2);
