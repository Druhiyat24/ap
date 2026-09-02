<?php
// Lepas reservasi BPB Kontrabon New.
//   action=one : lepas 1 BPB (dipanggil saat user hapus BPB dari draft)
//   action=all : lepas semua BPB milik user (dipanggil saat draft dibatalkan/new)
// Hanya boleh melepas reservasi milik user sendiri (create_user = user login).
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json; charset=utf-8');

$user   = $_SESSION['username'] ?? '';
$action = $_POST['action'] ?? 'one';
$no_bpb = trim($_POST['no_bpb'] ?? '');
$u = mysqli_real_escape_string($conn2, $user);

if ($action === 'all') {
    mysqli_query($conn2, "DELETE FROM ir_kontrabon_bpb_reserve WHERE create_user = '$u'");
    echo json_encode(['ok' => true, 'released' => mysqli_affected_rows($conn2)]);
    exit;
}

if ($no_bpb === '') { echo json_encode(['ok' => false, 'msg' => 'BPB is empty.']); exit; }
$b = mysqli_real_escape_string($conn2, $no_bpb);
mysqli_query($conn2, "DELETE FROM ir_kontrabon_bpb_reserve WHERE no_bpb = '$b' AND create_user = '$u'");
echo json_encode(['ok' => true, 'released' => mysqli_affected_rows($conn2)]);
