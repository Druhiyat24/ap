<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$no_pengajuan = $_POST['no_pengajuan'] ?? '';
$no_pengajuan_esc = mysqli_real_escape_string($conn1, $no_pengajuan);

if (empty($no_pengajuan)) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data']);
    exit;
}

$check = mysqli_query($conn1, "SELECT status FROM update_bpb_fabric_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
$row = mysqli_fetch_assoc($check);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Data not found']);
    exit;
}

if ($row['status'] == 'Cancel') {
    echo json_encode(['success' => false, 'message' => 'Request already cancelled']);
    exit;
}

if ($row['status'] == 'Approved') {
    echo json_encode(['success' => false, 'message' => 'Request already approved, cannot be cancelled']);
    exit;
}

$update = mysqli_query($conn1, "UPDATE update_bpb_fabric_h SET status = 'Cancel' WHERE no_pengajuan = '$no_pengajuan_esc'");

if ($update) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update database']);
}
?>
