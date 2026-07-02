<?php
include '../../conn/conn.php';
$items = [[126, 'Create Reverse PVL'], [127, 'Approval Reverse PVL']];
foreach ($items as [$id, $menu]) {
    $check = mysqli_query($conn2, "SELECT id FROM menurole WHERE id = $id");
    if (mysqli_num_rows($check) > 0) { echo "SKIP: id=$id ($menu) already exists.<br>"; continue; }
    $menu_esc = mysqli_real_escape_string($conn2, $menu);
    $ins = mysqli_query($conn2, "INSERT INTO menurole (id, menu) VALUES ($id, '$menu_esc')");
    echo $ins ? "OK: id=$id ($menu)<br>" : "Error id=$id: " . mysqli_error($conn2) . "<br>";
}
mysqli_close($conn2);
