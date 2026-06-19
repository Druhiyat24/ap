<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$no_pengajuan = isset($_GET['no_pengajuan']) ? $_GET['no_pengajuan'] : '';
$no_pengajuan_esc = mysqli_real_escape_string($conn1, $no_pengajuan);

$header = null;
$h = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, nama_supp, deskripsi, status, created_by, created_at
    FROM update_bpb_fabric_h WHERE no_pengajuan = '$no_pengajuan_esc' LIMIT 1");
if ($row = mysqli_fetch_assoc($h)) {
    $row['tgl_pengajuan'] = !empty($row['tgl_pengajuan']) ? date('d-M-Y', strtotime($row['tgl_pengajuan'])) : '-';
    $row['created_at'] = !empty($row['created_at']) ? date('d-M-Y H:i:s', strtotime($row['created_at'])) : '-';
    $header = $row;
}

$items = [];
$sql = mysqli_query($conn1, "SELECT no_bpb, tgl_bpb, nama_supp, no_ws, id_item, desc_item, qty, unit, curr, price_old, price_new, ppn_old, ppn_new
    FROM update_bpb_fabric
    WHERE no_pengajuan = '$no_pengajuan_esc'
    ORDER BY no_bpb, no_ws, id_item");
while ($row = mysqli_fetch_assoc($sql)) {
    $row['tgl_bpb'] = !empty($row['tgl_bpb']) ? date('d-M-Y', strtotime($row['tgl_bpb'])) : '-';
    $items[] = $row;
}

echo json_encode(['header' => $header, 'items' => $items], JSON_INVALID_UTF8_SUBSTITUTE);
?>
