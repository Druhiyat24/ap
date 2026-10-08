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

    /* Pil status: hanya ada tiga nilai di tabel ini - Draft, Approved,
       Cancel - dan ketiganya punya kelas sendiri di app-ftr-list.css.
       Dipetakan tegas, bukan lewat strtolower($status), supaya status
       bernama dua kata tidak pernah menghasilkan nama kelas rusak. */
    $kelasStatus = array('draft' => 'is-draft', 'approved' => 'is-approved', 'cancel' => 'is-cancel');
    $kunci = strtolower(trim($status));
    $statusLabel = '<span class="ftl-st ' . (isset($kelasStatus[$kunci]) ? $kelasStatus[$kunci] : '') . '">'
                 . htmlspecialchars($status, ENT_QUOTES) . '</span>';

    if (strcasecmp($status, 'Cancel') === 0) {
        $action = '<div class="ftl-act"><span class="ftl-badge"><i class="fa fa-ban" aria-hidden="true"></i> Cancelled</span></div>';
    } else {
        $btnShow = '<button type="button" class="ftl-mini is-show btn-show-pco" title="Show the document detail"><i class="fa fa-eye" aria-hidden="true"></i> Show</button>';
        $btnPdf = '<a href="pdf_petty_cashout.php?no_pco=' . htmlspecialchars(rawurlencode($noPco)) . '" target="_blank" class="ftl-mini is-pdf" title="Open the printable PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Pdf</a>';

        $btnEdit = '';
        $btnCancel = '';
        if (strcasecmp($status, 'Draft') === 0) {
            if ($reff === 'None' || $reff === 'Advance') {
                $btnEdit = '<button type="button" class="ftl-mini is-edit edit-none" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit this draft"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>';
            } elseif ($reff === 'Settlement') {
                $btnEdit = '<button type="button" class="ftl-mini is-edit edit-settle" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit this draft"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>';
            } elseif ($reff === 'List Payment') {
                $btnEdit = '<button type="button" class="ftl-mini is-edit edit-lp" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit this draft"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>';
            } elseif ($reff === 'FTR (CBD / DP)') {
                $btnEdit = '<button type="button" class="ftl-mini is-edit edit-ftr" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit this draft"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>';
            } elseif ($reff === 'Payment Voucher') {
                $btnEdit = '<button type="button" class="ftl-mini is-edit edit-pv" data-pettycash="' . htmlspecialchars($noPco) . '" title="Edit this draft"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>';
            }

            // Selama ini cancel cuma bisa dari menu approve-petty-cashout.php -
            // dokumen yang masih Draft sekarang bisa langsung di-cancel dari sini juga.
            $btnCancel = '<button type="button" class="ftl-mini is-cancel cancel-pco" data-pettycash="' . htmlspecialchars($noPco) . '" title="Cancel this draft"><i class="fa fa-trash" aria-hidden="true"></i> Cancel</button>';
        }

        $action = '<div class="ftl-act">' . $btnShow . $btnEdit . $btnCancel . $btnPdf . '</div>';
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
