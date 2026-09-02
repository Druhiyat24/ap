<?php
// Migrasi ADITIF: detail tambahan BPB untuk Kontrabon New (dari bpb_new).
//   ir_kontrabon_bpb += no_po (pono), supplier, dpp, ppn
// (tgl_bpb, total, curr sudah ada). Idempoten.
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

addCol($conn2, 'ir_kontrabon_bpb', 'no_po',    "no_po VARCHAR(255) NULL AFTER no_bpb");
addCol($conn2, 'ir_kontrabon_bpb', 'supplier', "supplier VARCHAR(255) NULL AFTER no_po");
addCol($conn2, 'ir_kontrabon_bpb', 'dpp',      "dpp DECIMAL(20,2) NULL DEFAULT 0 AFTER total");
addCol($conn2, 'ir_kontrabon_bpb', 'ppn',      "ppn DECIMAL(20,2) NULL DEFAULT 0 AFTER dpp");

echo "\nStruktur ir_kontrabon_bpb:\n";
$r = mysqli_query($conn2, "SHOW COLUMNS FROM ir_kontrabon_bpb");
while ($x = mysqli_fetch_assoc($r)) echo "  " . str_pad($x['Field'], 13) . " | " . $x['Type'] . "\n";
mysqli_close($conn2);
