<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$sql = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, status, deskripsi, created_by, created_at
    FROM update_bpb_fabric_h
    WHERE status NOT IN ('Approved','Cancel')
    ORDER BY id DESC");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $row['tgl_pengajuan'] = !empty($row['tgl_pengajuan']) ? date('d-M-Y', strtotime($row['tgl_pengajuan'])) : '-';

    if (!empty($row['created_by'])) {
        $created_at_fmt = !empty($row['created_at']) ? date('d-M-Y H:i:s', strtotime($row['created_at'])) : '-';
        $row['created_by'] = $row['created_by'] . ' (' . $created_at_fmt . ')';
    }

    $statusRaw = $row['status'];

    $badge = 'secondary';
    if ($statusRaw == 'Waiting') $badge = 'warning';

    $row['status'] = '<span class="badge badge-' . $badge . ' p-2">' . htmlspecialchars($statusRaw) . '</span>';

    $row['checkbox'] = '<input type="checkbox" class="chk-pengajuan" value="' . htmlspecialchars($row['no_pengajuan']) . '">';

    $row['action'] = '<button type="button" class="btn btn-sm btn-primary btn-view-pengajuan" data-no="' . htmlspecialchars($row['no_pengajuan']) . '"><i class="fa fa-eye"></i> View</button>';

    $data[] = $row;
}

echo json_encode(['data' => $data], JSON_INVALID_UTF8_SUBSTITUTE);
?>
