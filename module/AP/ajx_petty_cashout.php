<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$reference  = isset($_POST['reference']) ? trim($_POST['reference']) : 'ALL';
$doc_num    = isset($_POST['doc_num']) ? trim($_POST['doc_num']) : '';
$start_date = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : '';
$end_date   = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : '';

$conditions = [];
if ($reference !== '' && $reference !== 'ALL') {
    $conditions[] = "a.reff = '" . mysqli_real_escape_string($conn1, $reference) . "'";
}
if ($doc_num !== '') {
    $conditions[] = "a.no_pco like '%" . mysqli_real_escape_string($conn1, $doc_num) . "%'";
}
if ($start_date !== '' && $end_date !== '') {
    $conditions[] = "a.tgl_pco between '" . mysqli_real_escape_string($conn1, $start_date) . "' and '" . mysqli_real_escape_string($conn1, $end_date) . "'";
}

$where = count($conditions) ? ('where ' . implode(' and ', $conditions)) : '';

$sql = mysqli_query($conn1, "select a.no_pco, a.tgl_pco, a.reff, a.nama_supp, b.nama_coa, a.amount, a.deskripsi, a.status
    from c_petty_cashout_h a left join mastercoa_v2 b on b.no_coa = a.coa_akun $where group by a.no_pco order by a.tgl_pco desc, a.no_pco desc");

$data = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $status = $row['status'];
    $reff = $row['reff'];
    $noPco = $row['no_pco'];

    if (strcasecmp($status, 'Cancel') === 0) {
        $action = '<span class="text-danger font-weight-bold">Cancelled</span>';
        $statusLabel = '<span class="kbon-status-label" style="background:#fdecea;color:#b71c1c;"><i class="fa fa-ban"></i> Cancel</span>';
    } else {
        $btnShow = '<button type="button" class="btn btn-sm btn-outline-warning btn-show-pco" title="Show"><i class="fas fa-eye"></i> Show</button>';
        $btnPdf = '<a href="pdf_petty_cashout.php?no_pco=' . htmlspecialchars(rawurlencode($noPco)) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF"><i class="fa fa-file-pdf-o"></i> Pdf</a>';

        $btnEdit = '';
        $btnCancel = '';
        if (strcasecmp($status, 'Draft') === 0) {
            if ($reff === 'None' || $reff === 'Advance') {
                $btnEdit = '<button type="button" class="btn btn-sm btn-outline-primary edit-none" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            } elseif ($reff === 'Settlement') {
                $btnEdit = '<button type="button" class="btn btn-sm btn-outline-primary edit-settle" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            } elseif ($reff === 'List Payment') {
                $btnEdit = '<button type="button" class="btn btn-sm btn-outline-primary edit-lp" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            } elseif ($reff === 'Payment Voucher') {
                $btnEdit = '<button type="button" class="btn btn-sm btn-outline-primary edit-pv" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            }

            // Selama ini cancel cuma bisa dari menu approve-petty-cashout.php -
            // dokumen yang masih Draft sekarang bisa langsung di-cancel dari sini juga.
            $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger cancel-pco" data-pettycash="' . htmlspecialchars($noPco) . '" title="Cancel"><i class="fa fa-ban"></i> Cancel</button>';
        }

        $action = '<div class="kbon-action-buttons">' . $btnShow . $btnEdit . $btnCancel . $btnPdf . '</div>';
        $statusLabel = strcasecmp($status, 'Draft') === 0
            ? '<span class="kbon-status-label" style="background:#fff4e5;color:#b26a00;"><i class="fa fa-pencil"></i> Draft</span>'
            : '<span class="kbon-status-label" style="background:#eef2f9;color:#1e3a8a;"><i class="fa fa-check"></i> ' . htmlspecialchars($status) . '</span>';
    }

    $data[] = [
        'no_pco'       => $noPco,
        'tgl_pco'      => !empty($row['tgl_pco']) ? date('d-M-Y', strtotime($row['tgl_pco'])) : '-',
        'tgl_pco_raw'  => $row['tgl_pco'],
        'reff'         => $reff,
        'nama_supp'    => $row['nama_supp'],
        'nama_coa'     => $row['nama_coa'],
        'amount'       => number_format((float) $row['amount'], 2),
        'deskripsi'    => $row['deskripsi'],
        'status'       => $statusLabel,
        'status_raw'   => $status,
        'action'       => $action,
    ];
}

echo json_encode(['data' => $data]);
