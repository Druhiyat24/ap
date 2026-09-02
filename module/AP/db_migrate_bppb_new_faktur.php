<?php
// ============================================================================
// MIGRASI: tambah kolom dokumen invoice/faktur ke bppb_new (untuk retur),
// analog dgn bpb_new. Idempotent — aman dijalankan ulang. Jalankan juga di server.
//   php via Apache: buka /ap_dev/module/AP/db_migrate_bppb_new_faktur.php
// ============================================================================
include '../../conn/conn.php';
header('Content-Type: text/plain; charset=utf-8');

$cols = [
    'upt_dok_inv'    => "ADD COLUMN upt_dok_inv VARCHAR(255) NULL AFTER is_invoiced",
    'upt_no_inv'     => "ADD COLUMN upt_no_inv VARCHAR(255) NULL AFTER upt_dok_inv",
    'upt_tgl_inv'    => "ADD COLUMN upt_tgl_inv DATE NULL AFTER upt_no_inv",
    'upt_no_faktur'  => "ADD COLUMN upt_no_faktur VARCHAR(255) NULL AFTER upt_tgl_inv",
    'upt_tgl_faktur' => "ADD COLUMN upt_tgl_faktur DATE NULL AFTER upt_no_faktur",
];
foreach ($cols as $name => $ddl) {
    $chk = mysqli_query($conn2, "SHOW COLUMNS FROM bppb_new LIKE '$name'");
    if ($chk && mysqli_num_rows($chk) > 0) { echo "SKIP  $name (sudah ada)\n"; continue; }
    if (mysqli_query($conn2, "ALTER TABLE bppb_new $ddl")) echo "ADDED $name\n";
    else echo "ERR   $name : " . mysqli_error($conn2) . "\n";
}
echo "\n== struktur upt_* bppb_new sekarang ==\n";
$r = mysqli_query($conn2, "SHOW COLUMNS FROM bppb_new LIKE 'upt_%'");
while ($c = mysqli_fetch_assoc($r)) echo "  {$c['Field']} ({$c['Type']})\n";
