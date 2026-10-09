<?php
/* ============================================================================
   Update BPB - FABRIC.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen kain punya tabel kepala sendiri: whs_inmaterial_fabric (+_det)
   utk PENERIMAAN (GK/IN) dan whs_bppb_h/whs_bppb_ro utk RETUR (GK/RO).
   Keduanya dibedakan, karena jurnal GK/RO arahnya TERBALIK dari GK/IN.
   (Bandingkan Accessories & General: dokumennya langsung di `bpb` dan
   RI-nya tetap dihitung sbg penerimaan.)
   ============================================================================ */
include '../../conn/conn.php';
$jenis = 'fabric';
$jenis_esc = mysqli_real_escape_string($conn1, $jenis);
header('Content-Type: application/json');

$no_pengajuan = $_POST['no_pengajuan'] ?? '';
$no_pengajuan_esc = mysqli_real_escape_string($conn1, $no_pengajuan);

if (empty($no_pengajuan)) {
    echo json_encode(['success' => false, 'message' => 'Incomplete data']);
    exit;
}

$check = mysqli_query($conn1, "SELECT status FROM Req_update_bpb_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
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

$update = mysqli_query($conn1, "UPDATE Req_update_bpb_h SET status = 'Cancel' WHERE no_pengajuan = '$no_pengajuan_esc'");

if ($update) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update database']);
}
?>
