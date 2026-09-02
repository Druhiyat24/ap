<?php
// Draft (staging) Kontrabon New per-user. Snapshot JSON supaya input scan tidak
// hilang saat refresh & bisa dilanjut di PC mana pun (draft ada di server, per
// create_user). Satu draft aktif per user.
//   action=save  : upsert draft (payload = seluruh isi form dalam JSON)
//   action=load  : ambil draft user (untuk ditawarkan "lanjutkan?")
//   action=clear : hapus draft user
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$user   = $_SESSION['username'] ?? '';
$action = $_POST['action'] ?? 'load';
$u = mysqli_real_escape_string($conn2, $user);
$now = date('Y-m-d H:i:s');

if ($user === '') { echo json_encode(['ok' => false, 'msg' => 'Not logged in.']); exit; }

if ($action === 'save') {
    $payload = $_POST['payload'] ?? '[]';
    $unik    = trim($_POST['unik_code'] ?? '');
    $doc     = trim($_POST['doc_number'] ?? '');
    $p = mysqli_real_escape_string($conn2, $payload);
    $un = mysqli_real_escape_string($conn2, $unik);
    $dc = mysqli_real_escape_string($conn2, $doc);
    $ok = mysqli_query($conn2, "INSERT INTO ir_kontrabon_temp (create_user, unik_code, doc_number, payload, update_date)
        VALUES ('$u', '$un', '$dc', '$p', '$now')
        ON DUPLICATE KEY UPDATE unik_code = VALUES(unik_code), doc_number = VALUES(doc_number), payload = VALUES(payload), update_date = VALUES(update_date)");
    echo json_encode($ok ? ['ok' => true] : ['ok' => false, 'msg' => mysqli_error($conn2)]);
    exit;
}

if ($action === 'clear') {
    mysqli_query($conn2, "DELETE FROM ir_kontrabon_temp WHERE create_user = '$u'");
    echo json_encode(['ok' => true]);
    exit;
}

// load
$r = mysqli_query($conn2, "SELECT unik_code, doc_number, payload, update_date FROM ir_kontrabon_temp WHERE create_user = '$u' LIMIT 1");
if ($r && mysqli_num_rows($r) > 0) {
    $row = mysqli_fetch_assoc($r);
    echo json_encode(['ok' => true, 'draft' => [
        'unik_code'   => $row['unik_code'],
        'doc_number'  => $row['doc_number'],
        'payload'     => json_decode($row['payload'], true),
        'update_date' => $row['update_date'],
    ]]);
} else {
    echo json_encode(['ok' => true, 'draft' => null]);
}
