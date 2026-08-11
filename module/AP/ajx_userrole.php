<?php
// AJAX endpoint untuk userrole.php - ringkasan menu per user (1 baris = 1 user,
// bukan 1 baris = 1 menu lagi), dipakai oleh DataTable supaya Search tidak
// reload seluruh halaman. userpassword, useraccess & menurole sama-sama ada
// di database yang sama (lihat conn/conn.php - $conn1/$conn2 connect ke db
// yang sama, cuma beda resource koneksi), jadi cukup query lewat $conn2 saja.
include '../../conn/conn.php';
header('Content-Type: application/json');

$nama_supp = $_POST['nama_supp'] ?? 'ALL';

$where = '';
if ($nama_supp !== '' && $nama_supp !== 'ALL') {
    $nama_supp_esc = mysqli_real_escape_string($conn2, $nama_supp);
    $where = "where u.username = '$nama_supp_esc'";
}

$sql = mysqli_query($conn2, "select u.username, u.FullName as fullname,
        group_concat(distinct a.menu order by a.menu separator '||') as menus,
        count(distinct a.menu) as menu_count
    from userpassword u
    left join useraccess a on a.username = u.username
    $where
    group by u.username, u.FullName
    having count(distinct a.menu) > 0
    order by u.username asc");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $menus = $row['menus'] ? explode('||', $row['menus']) : [];
    $data[] = [
        'username' => $row['username'],
        'fullname' => $row['fullname'],
        'menus' => $menus,
        'menu_count' => (int) $row['menu_count'],
    ];
}

echo json_encode(['data' => $data]);
