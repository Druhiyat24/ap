<?php
// MIGRASI: tambah kolom document_date ke ir_kontrabon_h.
//   kontrabon_date = Invoice Received Date (selalu Rabu).
//   document_date  = Document Date (tanggal yang diisi user). Idempotent.
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

$chk = mysqli_query($conn2, "SHOW COLUMNS FROM ir_kontrabon_h LIKE 'document_date'");
if ($chk && mysqli_num_rows($chk) > 0) {
    echo "SKIP document_date (sudah ada)\n";
} elseif (mysqli_query($conn2, "ALTER TABLE ir_kontrabon_h ADD COLUMN document_date DATE NULL AFTER kontrabon_date")) {
    echo "ADDED document_date\n";
} else {
    echo "ERR: " . mysqli_error($conn2) . "\n";
}
$r = mysqli_query($conn2, "SHOW COLUMNS FROM ir_kontrabon_h LIKE '%date%'");
while ($c = mysqli_fetch_assoc($r)) echo "  {$c['Field']} ({$c['Type']})\n";
