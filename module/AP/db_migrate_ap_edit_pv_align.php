<?php
/**
 * Align the ap_edit_* scratch tables (used by the Payment Voucher edit flow) with
 * their live counterparts. copy_data_kontrabon.php copies a PV into these tables via
 * `INSERT ... SELECT *`, which REQUIRES identical column count/order. The live tables
 * gained newer columns (ir_number as varchar, id_bank_account, from_account/from_bank/
 * from_bank_curr, approval columns, potongan_ppn/pph) that the scratch tables never
 * received, so the copy failed silently and the edit form loaded empty.
 *
 * This migration is idempotent (checks each column before adding) and only touches the
 * three drifted scratch tables. ap_edit_kontrabon_ftr / ap_edit_return_kb already match.
 *
 * Run once via browser: /ap_dev/module/AP/db_migrate_ap_edit_pv_align.php
 */
include '../../conn/conn.php';
header('Content-Type: text/plain');

function col_exists($conn, $tbl, $col) {
    $c = mysqli_real_escape_string($conn, $col);
    $r = mysqli_query($conn, "SHOW COLUMNS FROM `$tbl` LIKE '$c'");
    return $r && mysqli_num_rows($r) > 0;
}
function col_type($conn, $tbl, $col) {
    $c = mysqli_real_escape_string($conn, $col);
    $r = mysqli_query($conn, "SHOW COLUMNS FROM `$tbl` LIKE '$c'");
    if ($r && ($row = mysqli_fetch_assoc($r))) return strtolower($row['Type']);
    return null;
}
function run($conn, $label, $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "  [DONE] $label\n";
    } else {
        echo "  [FAIL] $label -> " . mysqli_error($conn) . "\n";
    }
}

echo "=== ap_edit_kontrabon_h ===\n";
// ir_number must be varchar(255) to match live (was int). Widen if needed.
$t = col_type($conn2, 'ap_edit_kontrabon_h', 'ir_number');
if ($t !== null && strpos($t, 'varchar') === false) {
    run($conn2, "MODIFY ir_number -> varchar(255)",
        "ALTER TABLE ap_edit_kontrabon_h MODIFY COLUMN ir_number varchar(255) DEFAULT NULL");
} else {
    echo "  [SKIP] ir_number already varchar / absent\n";
}
// Add the trailing columns in the SAME ORDER as live kontrabon_h so SELECT * aligns.
$add_h = [
    ['id_bank_account',     "varchar(50)  DEFAULT NULL", 'ir_number'],
    ['first_approve_user',  "varchar(255) DEFAULT NULL", 'id_bank_account'],
    ['first_approve_date',  "timestamp    NULL DEFAULT NULL", 'first_approve_user'],
    ['second_approve_user', "varchar(255) DEFAULT NULL", 'first_approve_date'],
    ['second_approve_date', "timestamp    NULL DEFAULT NULL", 'second_approve_user'],
    ['from_account',        "varchar(100) DEFAULT NULL", 'second_approve_date'],
    ['from_bank',           "varchar(100) DEFAULT NULL", 'from_account'],
    ['from_bank_curr',      "varchar(10)  DEFAULT NULL", 'from_bank'],
    ['status_pl',           "varchar(50)  DEFAULT NULL", 'from_bank_curr'],
    ['status_pvl',          "varchar(50)  DEFAULT NULL", 'status_pl'],
];
foreach ($add_h as [$col, $def, $after]) {
    if (col_exists($conn2, 'ap_edit_kontrabon_h', $col)) { echo "  [SKIP] $col exists\n"; continue; }
    run($conn2, "ADD $col", "ALTER TABLE ap_edit_kontrabon_h ADD COLUMN `$col` $def AFTER `$after`");
}

echo "\n=== ap_edit_potongan ===\n";
$add_p = [
    ['potongan_ppn', "decimal(16,2) DEFAULT NULL", 'jml_potong'],
    ['potongan_pph', "decimal(16,2) DEFAULT NULL", 'potongan_ppn'],
];
foreach ($add_p as [$col, $def, $after]) {
    if (col_exists($conn2, 'ap_edit_potongan', $col)) { echo "  [SKIP] $col exists\n"; continue; }
    run($conn2, "ADD $col", "ALTER TABLE ap_edit_potongan ADD COLUMN `$col` $def AFTER `$after`");
}

echo "\n=== ap_edit_kontrabon ===\n";
$add_k = [
    ['first_approve_by',    "varchar(255) DEFAULT NULL", 'lp_inv'],
    ['first_approve_date',  "datetime     NULL DEFAULT NULL", 'first_approve_by'],
    ['second_approve_by',   "varchar(255) DEFAULT NULL", 'first_approve_date'],
    ['second_approve_date', "datetime     NULL DEFAULT NULL", 'second_approve_by'],
];
foreach ($add_k as [$col, $def, $after]) {
    if (col_exists($conn2, 'ap_edit_kontrabon', $col)) { echo "  [SKIP] $col exists\n"; continue; }
    run($conn2, "ADD $col", "ALTER TABLE ap_edit_kontrabon ADD COLUMN `$col` $def AFTER `$after`");
}

// Verify final column counts match live.
echo "\n=== verification (column counts) ===\n";
foreach ([['kontrabon_h','ap_edit_kontrabon_h'],['potongan','ap_edit_potongan'],['kontrabon','ap_edit_kontrabon']] as [$live,$edit]) {
    $lc = mysqli_num_rows(mysqli_query($conn2, "SHOW COLUMNS FROM `$live`"));
    $ec = mysqli_num_rows(mysqli_query($conn2, "SHOW COLUMNS FROM `$edit`"));
    echo "  $live=$lc  $edit=$ec  " . ($lc === $ec ? "[MATCH]" : "[STILL OFF]") . "\n";
}
echo "\nDone.\n";
