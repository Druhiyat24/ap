<?php
// Endpoint save untuk modal Manage Role di userrole.php - menerima daftar
// LENGKAP menu yang tercentang (bukan cuma yang baru ditambah), lalu
// menghitung selisihnya sendiri terhadap useraccess yang sudah ada: menu baru
// yang tercentang -> insert, menu lama yang sekarang tidak tercentang ->
// delete. Semua dalam SATU transaksi supaya tambah & kurang tersimpan
// sekaligus (dulu formuserrole.php cuma bisa nambah, per checkbox, request
// AJAX terpisah-pisah dan tidak atomic).
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$fullname = $_POST['fullname'] ?? '';
$menus = isset($_POST['menus']) ? json_decode($_POST['menus'], true) : [];
$create_user = $_POST['create_user'] ?? '';
$create_date = date('Y-m-d H:i:s');

if ($username === '' || !is_array($menus)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Username tidak valid.']);
    exit;
}

$username_esc = mysqli_real_escape_string($conn2, $username);
$fullname_esc = mysqli_real_escape_string($conn2, $fullname);
$create_user_esc = mysqli_real_escape_string($conn2, $create_user);

mysqli_begin_transaction($conn2);

try {
    $existing = [];
    $sqlExisting = mysqli_query($conn2, "select menu from useraccess where username = '$username_esc'");
    if ($sqlExisting === false) {
        throw new Exception(mysqli_error($conn2));
    }
    while ($row = mysqli_fetch_assoc($sqlExisting)) {
        $existing[$row['menu']] = true;
    }

    $checked = array_flip($menus);

    $toAdd = array_diff_key($checked, $existing);
    $toRemove = array_diff_key($existing, $checked);

    foreach (array_keys($toAdd) as $menu) {
        $menu_esc = mysqli_real_escape_string($conn2, $menu);
        $result = mysqli_query($conn2, "INSERT INTO useraccess (username, fullname, menu, create_date, create_user)
            VALUES ('$username_esc', '$fullname_esc', '$menu_esc', '$create_date', '$create_user_esc')");
        if (!$result) {
            throw new Exception(mysqli_error($conn2));
        }
    }

    foreach (array_keys($toRemove) as $menu) {
        $menu_esc = mysqli_real_escape_string($conn2, $menu);
        $result = mysqli_query($conn2, "DELETE FROM useraccess WHERE username = '$username_esc' AND menu = '$menu_esc'");
        if (!$result) {
            throw new Exception(mysqli_error($conn2));
        }
    }

    mysqli_commit($conn2);
    echo json_encode([
        'success' => true,
        'added' => count($toAdd),
        'removed' => count($toRemove),
    ]);
} catch (Exception $e) {
    mysqli_rollback($conn2);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

mysqli_close($conn2);
