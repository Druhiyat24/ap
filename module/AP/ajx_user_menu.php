<?php
// AJAX endpoint untuk modal Manage Role di userrole.php - mengembalikan
// SEMUA menu (menurole) beserta status assigned/tidak untuk username
// tertentu, supaya bisa dirender sebagai satu daftar toggle switch
// (ceklis on/off), bukan dua panel assigned/available terpisah lagi.
include '../../conn/conn.php';
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';

if ($username === '') {
    echo json_encode(['menus' => [], 'fullname' => '']);
    exit;
}

$username_esc = mysqli_real_escape_string($conn2, $username);

$sqlFull = mysqli_query($conn2, "select FullName from userpassword where username = '$username_esc' limit 1");
$rowFull = mysqli_fetch_assoc($sqlFull);
$fullname = $rowFull['FullName'] ?? '';

$assignedSet = [];
$sqlAssigned = mysqli_query($conn2, "select menu from useraccess where username = '$username_esc'");
while ($row = mysqli_fetch_assoc($sqlAssigned)) {
    $assignedSet[$row['menu']] = true;
}

$menus = [];
// display_name (nama formal) & menu_group (grup utk warna/legend di modal) -
// kolom ADITIF, boleh NULL; front-end fallback ke `menu` / deteksi awalan-nama
// lama kalau kosong. Key `menu` tetap dipakai apa adanya utk simpan useraccess.
$sqlAll = mysqli_query($conn2, "select menu, display_name, menu_group from menurole where id != '35' order by id asc");
while ($row = mysqli_fetch_assoc($sqlAll)) {
    $menus[] = [
        'menu' => $row['menu'],
        'display_name' => $row['display_name'] ?? null,
        'menu_group' => $row['menu_group'] ?? null,
        'assigned' => isset($assignedSet[$row['menu']]),
    ];
}

echo json_encode(['menus' => $menus, 'fullname' => $fullname]);
