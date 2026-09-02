<?php
// Migrasi ADITIF: detail tanggal & total untuk Faktur/BPB Kontrabon New.
//   ir_kontrabon_faktur += tgl_faktur
//   ir_kontrabon_bpb    += tgl_bpb, total, curr
// Idempoten (skip kalau kolom sudah ada).
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

addCol($conn2, 'ir_kontrabon_faktur', 'tgl_faktur', "tgl_faktur DATE NULL AFTER no_faktur");
addCol($conn2, 'ir_kontrabon_bpb', 'tgl_bpb', "tgl_bpb DATE NULL AFTER no_bpb");
addCol($conn2, 'ir_kontrabon_bpb', 'total',   "total DECIMAL(20,2) NULL DEFAULT 0 AFTER tgl_bpb");
addCol($conn2, 'ir_kontrabon_bpb', 'curr',    "curr VARCHAR(10) NULL AFTER total");

echo "\nStruktur ir_kontrabon_bpb:\n";
$r = mysqli_query($conn2, "SHOW COLUMNS FROM ir_kontrabon_bpb");
while ($x = mysqli_fetch_assoc($r)) echo "  " . str_pad($x['Field'], 13) . " | " . $x['Type'] . "\n";
mysqli_close($conn2);
