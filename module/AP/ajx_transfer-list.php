<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$status     = isset($_POST['status']) ? $_POST['status'] : 'ALL';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d', strtotime('-1 month'));
$end_date   = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : date('Y-m-d');

$where = "h.tl_date BETWEEN '" . mysqli_real_escape_string($conn2, $start_date) . "' AND '" . mysqli_real_escape_string($conn2, $end_date) . "'";
if ($status !== 'ALL' && $status !== '') {
    $where .= " AND h.status = '" . mysqli_real_escape_string($conn2, $status) . "'";
}

$sql = mysqli_query($conn2, "SELECT h.tl_number, h.tl_date, h.tipe_mutasi, h.jenis_otorisasi, h.tanggal_efektif, h.jenis_biaya,
        h.status, h.created_by, h.created_date, (SELECT COUNT(*) FROM pv_transfer_list_det d WHERE d.tl_number = h.tl_number) jml_pl
    FROM pv_transfer_list_h h
    WHERE $where
    ORDER BY h.created_date DESC");

$data = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $tlNumber = $row['tl_number'];
    $rowStatus = $row['status'];

    $btnShow = '<button type="button" class="btn btn-sm btn-outline-warning btn-show-tl" data-tl="' . htmlspecialchars($tlNumber) . '" title="Show"><i class="fas fa-eye"></i> Show</button>';
    $btnExcel = '<a href="export_transfer_list.php?tl_number=' . htmlspecialchars(rawurlencode($tlNumber)) . '" class="btn btn-sm btn-outline-success" title="Export Excel"><i class="fa fa-file-excel-o"></i> Excel</a>';
    $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-tl" data-tl="' . htmlspecialchars($tlNumber) . '" title="Cancel"><i class="fas fa-times"></i> Cancel</button>';

    if (strcasecmp($rowStatus, 'Cancel') === 0) {
        $action = '<span class="text-danger font-weight-bold">Cancelled</span>';
    } else {
        $action = '<div class="kbon-action-buttons">' . $btnShow . $btnExcel . $btnCancel . '</div>';
    }

    $data[] = [
        'tl_number'       => $tlNumber,
        'tl_date'         => !empty($row['tl_date']) ? date('d-M-Y', strtotime($row['tl_date'])) : '-',
        'tipe_mutasi'     => $row['tipe_mutasi'],
        'jenis_otorisasi' => $row['jenis_otorisasi'],
        'tanggal_efektif' => !empty($row['tanggal_efektif']) ? date('d-M-Y', strtotime($row['tanggal_efektif'])) : '-',
        'jenis_biaya'     => $row['jenis_biaya'],
        'jml_pl'          => $row['jml_pl'],
        'status'          => $rowStatus,
        'created_by'      => $row['created_by'],
        'created_date'    => !empty($row['created_date']) ? date('d-M-Y H:i:s', strtotime($row['created_date'])) : '-',
        'action'          => $action,
    ];
}

echo json_encode(['data' => $data]);
