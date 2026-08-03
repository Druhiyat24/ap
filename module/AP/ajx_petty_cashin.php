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
    $conditions[] = "a.no_pci like '%" . mysqli_real_escape_string($conn1, $doc_num) . "%'";
}
if ($start_date !== '' && $end_date !== '') {
    $conditions[] = "a.tgl_pci between '" . mysqli_real_escape_string($conn1, $start_date) . "' and '" . mysqli_real_escape_string($conn1, $end_date) . "'";
}

$where = count($conditions) ? ('where ' . implode(' and ', $conditions)) : '';

$sql = mysqli_query($conn1, "select a.no_pci, a.tgl_pci, a.reff, if(a.reff_doc = '','-',a.reff_doc) as reff_doc, if(a.oth_doc = '','-',a.oth_doc) as oth_doc, b.nama_coa, a.amount, a.deskripsi, a.status
    from c_petty_cashin_h a left join mastercoa_v2 b on b.no_coa = a.coa_akun $where group by a.no_pci order by a.tgl_pci desc, a.no_pci desc");

$data = [];

while ($row = mysqli_fetch_assoc($sql)) {
    $status = $row['status'];
    $reff = $row['reff'];
    $noPci = $row['no_pci'];

    $reffDoc = ($reff === 'None') ? $row['oth_doc'] : $row['reff_doc'];
    $othDoc = ($reff === 'None') ? '-' : $row['oth_doc'];

    if (strcasecmp($status, 'Cancel') === 0) {
        $action = '<span class="text-danger font-weight-bold">Cancelled</span>';
        $statusLabel = '<span class="kbon-status-label" style="background:#fdecea;color:#b71c1c;"><i class="fa fa-ban"></i> Cancel</span>';
    } else {
        $btnShow = '<button type="button" class="btn btn-sm btn-outline-warning btn-show-pci" title="Show"><i class="fas fa-eye"></i> Show</button>';
        $btnPdf = '<a href="pdf_petty_cashin.php?no_pci=' . htmlspecialchars(rawurlencode($noPci)) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF"><i class="fa fa-file-pdf-o"></i> Pdf</a>';

        $btnEdit = '';
        $btnCancel = '';
        if (strcasecmp($status, 'Draft') === 0) {
            if ($reff === 'None') {
                $btnEdit = '<button type="button" class="btn btn-sm btn-outline-primary edit-none" data-pettycash="' . htmlspecialchars($noPci) . '" title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            } elseif ($reff === 'Settlement') {
                $btnEdit = '<button type="button" class="btn btn-sm btn-outline-primary edit-settle" data-pettycash="' . htmlspecialchars($noPci) . '" title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            } elseif ($reff === 'Cash Out') {
                $btnEdit = '<button type="button" class="btn btn-sm btn-outline-primary edit-cashout" data-pettycash="' . htmlspecialchars($noPci) . '" title="Edit"><i class="fa fa-edit"></i> Edit</button>';
            }

            $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger cancel-pci" data-pettycash="' . htmlspecialchars($noPci) . '" title="Cancel"><i class="fa fa-ban"></i> Cancel</button>';
        }

        $action = '<div class="kbon-action-buttons">' . $btnShow . $btnEdit . $btnCancel . $btnPdf . '</div>';
        $statusLabel = strcasecmp($status, 'Draft') === 0
            ? '<span class="kbon-status-label" style="background:#fff4e5;color:#b26a00;"><i class="fa fa-pencil"></i> Draft</span>'
            : '<span class="kbon-status-label" style="background:#eef2f9;color:#1e3a8a;"><i class="fa fa-check"></i> ' . htmlspecialchars($status) . '</span>';
    }

    $data[] = [
        'no_pci'       => $noPci,
        'tgl_pci'      => !empty($row['tgl_pci']) ? date('d-M-Y', strtotime($row['tgl_pci'])) : '-',
        'tgl_pci_raw'  => $row['tgl_pci'],
        'reff'         => $reff,
        'reff_doc'     => $reffDoc,
        'oth_doc'      => $othDoc,
        'nama_coa'     => $row['nama_coa'],
        'amount'       => number_format((float) $row['amount'], 2),
        'deskripsi'    => $row['deskripsi'],
        'status'       => $statusLabel,
        'status_raw'   => $status,
        'action'       => $action,
    ];
}

echo json_encode(['data' => $data]);
