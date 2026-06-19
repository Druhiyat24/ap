<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$sql = mysqli_query($conn1, "SELECT no_pengajuan, tgl_pengajuan, status, deskripsi, created_by, created_at
    FROM update_bpb_fabric_h
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
    if ($statusRaw == 'Approved') $badge = 'success';
    elseif ($statusRaw == 'Waiting') $badge = 'warning';
    elseif ($statusRaw == 'Cancel') $badge = 'danger';

    $row['status'] = '<span class="badge badge-' . $badge . ' p-2">' . htmlspecialchars($statusRaw) . '</span>';
    $row['action'] = '<button type="button" class="btn btn-sm btn-primary btn-view-pengajuan" data-no="' . htmlspecialchars($row['no_pengajuan']) . '"><i class="fa fa-eye"></i> View</button>'
        . ' <a href="pdf_update_bpb_fabric.php?no_pengajuan=' . urlencode($row['no_pengajuan']) . '" target="_blank" class="btn btn-sm btn-danger"><i class="fa fa-file-pdf-o"></i> PDF</a>';

    if ($statusRaw != 'Approved' && $statusRaw != 'Cancel') {
        $row['action'] .= ' <button type="button" class="btn btn-sm btn-warning btn-cancel-pengajuan" data-no="' . htmlspecialchars($row['no_pengajuan']) . '"><i class="fa fa-ban"></i> Cancel</button>';
    }

    $data[] = $row;
}

echo json_encode(['data' => $data], JSON_INVALID_UTF8_SUBSTITUTE);
?>
