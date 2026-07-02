<?php
include '../../conn/conn.php';
echo '<pre>';
echo "=== DB Migration: Reverse Payment Voucher ===\n\n";

// 1. Add type_pv column to ap_reverse_det if not exists
$res = mysqli_query($conn2, "SHOW COLUMNS FROM ap_reverse_det LIKE 'type_pv'");
if (mysqli_num_rows($res) === 0) {
    $a = mysqli_query($conn2, "ALTER TABLE ap_reverse_det ADD COLUMN type_pv VARCHAR(50) NULL AFTER doc_number");
    echo $a ? "OK: Added type_pv column to ap_reverse_det\n" : "Error adding type_pv: " . mysqli_error($conn2) . "\n";
} else {
    echo "SKIP: type_pv column already exists in ap_reverse_det\n";
}

// 2. Add menurole entries
$items = [
    [128, 'Create Reverse PV'],
    [129, 'Approval Reverse PV'],
];

foreach ($items as [$id, $menu]) {
    $check = mysqli_query($conn2, "SELECT id FROM menurole WHERE id=$id");
    if (mysqli_num_rows($check) > 0) {
        echo "SKIP: id=$id ($menu) already exists.\n";
        continue;
    }
    $menu_esc = mysqli_real_escape_string($conn2, $menu);
    $ins = mysqli_query($conn2, "INSERT INTO menurole (id, menu) VALUES ($id, '$menu_esc')");
    echo $ins ? "OK: id=$id ($menu) inserted.\n" : "Error id=$id: " . mysqli_error($conn2) . "\n";
}

echo "\n=== Migration complete ===\n";
echo '</pre>';
mysqli_close($conn2);
