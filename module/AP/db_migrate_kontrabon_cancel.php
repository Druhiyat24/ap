<?php
// Migrasi ADITIF: audit Cancel untuk Kontrabon New.
//   ir_kontrabon_h += cancel_user, cancel_date
// Idempoten.
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

addCol($conn2, 'ir_kontrabon_h', 'cancel_user', "cancel_user VARCHAR(100) NULL");
addCol($conn2, 'ir_kontrabon_h', 'cancel_date', "cancel_date DATETIME NULL");
echo "done\n";
mysqli_close($conn2);
