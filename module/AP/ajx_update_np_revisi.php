<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$sql = mysqli_query($conn1, "SELECT no_dok, tgl_dok, total_row, total_success, total_failed, status, created_by, created_at
    FROM update_np_revisi_h
    ORDER BY id DESC");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $row['tgl_dok'] = !empty($row['tgl_dok']) ? date('d-M-Y', strtotime($row['tgl_dok'])) : '-';

    if (!empty($row['created_by'])) {
        $created_at_fmt = !empty($row['created_at']) ? date('d-M-Y H:i:s', strtotime($row['created_at'])) : '-';
        $row['created_by'] = $row['created_by'] . ' (' . $created_at_fmt . ')';
    }

    $statusRaw = $row['status'];
    $badge = ($statusRaw == 'Cancel') ? 'secondary' : 'success';
    $row['status'] = '<span class="badge badge-' . $badge . ' p-2">' . htmlspecialchars($statusRaw) . '</span>';

    $no_dok_attr = htmlspecialchars($row['no_dok']);
    $row['action'] = '<button type="button" class="btn btn-sm btn-primary btn-view-detail" data-no="' . $no_dok_attr . '"><i class="fa fa-eye"></i> View Detail</button>'
        . ' <a href="export_update_np_revisi.php?no_dok=' . urlencode($row['no_dok']) . '" class="btn btn-sm btn-success"><i class="fa fa-file-excel-o"></i> Export</a>';

    if ($statusRaw != 'Cancel') {
        $row['action'] .= ' <button type="button" class="btn btn-sm btn-warning btn-cancel" data-no="' . $no_dok_attr . '"><i class="fa fa-ban"></i> Cancel</button>';
    }

    $data[] = $row;
}

echo json_encode(['data' => $data], JSON_INVALID_UTF8_SUBSTITUTE);
