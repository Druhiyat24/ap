<?php
/* Query + action-button builder untuk listing Payment Voucher AP, dipakai
   bersama oleh ajx_payment-voucher-ap.php (DataTable di payment-voucher-ap.php)
   dan ekspor_pv_all.php (export Excel) - supaya keduanya selalu menampilkan
   data yang identik dan filter Type/Supplier/Status/Date-nya konsisten. */

/* ====================== TYPE: REGULAR ====================== */
/* Sumber data sama seperti kontrabon.php (tabel kontrabon + potongan + kontrabon_h) */

function getDataRegular($conn1, $conn2, $filters, $fin, $app, $group)
{
    $where = "a.no_kbon not like '%INS%' AND a.no_bpb != ''";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " AND a.nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    if ($filters['status'] !== 'ALL' && $filters['status'] !== '') {
        $where .= " AND a.status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";
    }

    if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
        $where .= " AND a." . $filters['filter_date'] . " BETWEEN '" . mysqli_real_escape_string($conn2, $filters['start_date']) . "' AND '" . mysqli_real_escape_string($conn2, $filters['end_date']) . "'";
    }

    if (!empty($filters['no_kbon'])) {
        $where .= " AND a.no_kbon = '" . mysqli_real_escape_string($conn2, $filters['no_kbon']) . "'";
    }

    $sql = mysqli_query($conn2, "select a.no_kbon, a.tgl_kbon, a.no_bpb, a.no_po, a.tgl_bpb, a.tgl_po, a.nama_supp,
        SUM(a.subtotal) as subtotal, SUM(a.tax) as tax, a.curr, a.create_user, a.status, a.tgl_tempo,
        a.no_faktur, a.supp_inv, a.tgl_inv, a.pph_code, SUM(a.pph_value) as pph_value, a.dp_value,
        d.tgl_kbon2, d.confirm_user, d.confirm_date, d.create_date, d.profit_center, b.jml_return, b.jml_potong,
        b.potongan_ppn, b.potongan_pph,
        d.status_pl, d.no_coa, d.nama_coa, d.rate, d.from_account, d.from_bank, d.from_bank_curr
        from kontrabon a
        inner join potongan b on b.no_kbon = a.no_kbon
        inner join kontrabon_h d on d.no_kbon = a.no_kbon
        where $where
        group by a.no_kbon
        order by a.tgl_kbon desc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];
        $tgl_kbon = $row['tgl_kbon'];

        $rows = mysqli_query($conn2, "select sum(total_ftr) total_ftr from kontrabon_ftr where no_kbon = '" . mysqli_real_escape_string($conn2, $kbonno) . "' group by no_kbon");
        $rowDp = mysqli_fetch_assoc($rows);
        $dp = $rowDp['total_ftr'] ?? 0;

        $queryClosing = mysqli_query($conn2, "select status_closing from tbl_closing_periode where '" . mysqli_real_escape_string($conn2, $tgl_kbon) . "' between tgl_awal and tgl_akhir");
        $rowClosing = mysqli_fetch_assoc($queryClosing);
        $status_closing = $rowClosing['status_closing'] ?? '';

        $sub = $row['subtotal'];
        $tax = $row['tax'] + $row['potongan_ppn'];
        $pph = $row['pph_value'] + $row['potongan_pph'];
        $return = $row['jml_return'];
        $potong = $row['jml_potong'];
        $total = $sub + $tax - ($pph + $dp + $return) + $potong;
        $rowStatus = $row['status'];

        $data[] = [
            'type'        => 'Regular',
            'profit_center' => $row['profit_center'],
            'no_kbon'     => $kbonno,
            'tgl_kbon'    => !empty($tgl_kbon) ? date('d-M-Y', strtotime($tgl_kbon)) : '-',
            'tgl_kbon_raw' => $tgl_kbon,
            'tgl_kbon2'   => !empty($row['tgl_kbon2']) ? date('d-M-Y', strtotime($row['tgl_kbon2'])) : '-',
            'nama_supp'   => $row['nama_supp'],
            'subtotal'    => number_format($sub, 2),
            'tax'         => number_format($tax, 2),
            'pph_value'   => '- ' . number_format($pph, 2),
            'dp'          => '- ' . number_format($dp, 2),
            'return'      => number_format($return, 2),
            'potong'      => number_format($potong, 2),
            'total'       => number_format($total, 2),
            'total_raw'   => $total,
            'subtotal_raw' => $sub,
            'tax_raw'     => $tax,
            'pph_raw'     => $pph,
            'curr'        => $row['curr'],
            'status'      => $rowStatus,
            'status_pl'   => $row['status_pl'],
            'no_coa'      => $row['no_coa'],
            'nama_coa'    => $row['nama_coa'],
            'rate'        => $row['rate'],
            'from_account' => $row['from_account'],
            'from_bank'   => $row['from_bank'],
            'from_bank_curr' => $row['from_bank_curr'],
            'tgl_tempo'   => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'],
            'create_user' => $row['create_user'],
            'create_date' => !empty($row['create_date']) ? date('Y-m-d H:i:s', strtotime($row['create_date'])) : '-',
            'confirm_user' => $row['confirm_user'],
            'confirm_date' => !empty($row['confirm_date']) && $row['confirm_date'] != '0000-00-00' ? date('Y-m-d', strtotime($row['confirm_date'])) : '-',
            'no_faktur'   => $row['no_faktur'],
            'supp_inv'    => $row['supp_inv'],
            'tgl_inv'     => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'action'      => buildActionRegular($kbonno, $rowStatus, $total, $row, $status_closing, $fin, $app),
        ];
    }

    return $data;
}

function buildActionRegular($kbonno, $rowStatus, $total, $row, $status_closing, $fin, $app)
{
    $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-kbon" data-no_kbon="' . htmlspecialchars($kbonno) . '" title="Cancel">'
        . '<i class="fa fa-times-circle"></i> Cancel</button>';

    $btnPdf = '<a href="pdf_pv_regular.php?nokontrabon=' . htmlspecialchars($kbonno) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF">'
        . '<i class="fa fa-file-pdf-o"></i> PDF</a>';

    $btnEdit = '<button type="button" class="btn btn-sm btn-outline-warning btn-edit-kbon" data-no_kbon="' . htmlspecialchars($kbonno) . '" data-closing="' . htmlspecialchars($status_closing) . '" title="Edit">'
        . '<i class="fa fa-pencil"></i> Edit</button>';

    $btnContinueEdit = '<a href="form_edit_kontrabon.php?no_kbon=' . base64_encode($kbonno) . '" class="btn btn-sm btn-outline-warning" title="Continue Editing">'
        . '<i class="fa fa-pencil"></i> Continue Editing</a>';

    if ($rowStatus == 'Approved' || $rowStatus == 'SECOND APPROVED') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
    } elseif ($rowStatus == 'FIRST APPROVED') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '<span class="kbon-status-label" style="background:#eef2f9;color:#1e3a8a;"><i class="fa fa-clock-o"></i> Waiting 2nd Approval</span></div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app == '1') {
        return '<div class="kbon-action-buttons">' . $btnCancel . $btnPdf . $btnEdit . '</div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app != '1') {
        return '<div class="kbon-action-buttons">' . $btnPdf . $btnEdit . '</div>';
    } elseif ($rowStatus == 'Cancel') {
        return '<span class="kbon-status-label kbon-status-canceled"><i class="fa fa-ban"></i> Canceled</span>';
    } elseif ($rowStatus == 'Updated') {
        return '<span class="kbon-status-label kbon-status-updated"><i class="fas fa-check-double"></i> Updated</span>';
    } elseif ($rowStatus == 'Updating') {
        return '<div class="kbon-action-buttons">' . $btnContinueEdit . '</div>';
    }

    return '';
}

/* ====================== TYPE: INSTALLMENT ====================== */
/* BPB/RO/FTR/jurnal Installment disimpan di kontrabon_h yang sama seperti
   Regular (tidak dipecah per cicilan) - hanya breakdown DPP/PPN/PPh/RO/FTR/
   Potongan/Total per cicilan yang dipecah di kontrabon_h_installment_detail.
   Listing ini menampilkan SATU BARIS PER CICILAN (nomor anak); nomor induk
   tidak ditampilkan sama sekali - tapi tombol Cancel tetap menyasar nomor
   induk supaya cancel salah satu cicilan otomatis cancel semua cicilan lain. */

function getDataInstallment($conn1, $conn2, $filters, $fin, $app, $group)
{
    $where = "h.no_kbon LIKE 'PV-AP/INS/%'";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " AND h.nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    if ($filters['status'] !== 'ALL' && $filters['status'] !== '') {
        $where .= " AND h.status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";
    }

    if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
        $where .= " AND h." . $filters['filter_date'] . " BETWEEN '" . mysqli_real_escape_string($conn2, $filters['start_date']) . "' AND '" . mysqli_real_escape_string($conn2, $filters['end_date']) . "'";
    }

    if (!empty($filters['no_kbon_det'])) {
        $where .= " AND d.no_kbon_det = '" . mysqli_real_escape_string($conn2, $filters['no_kbon_det']) . "'";
    }

    $sql = mysqli_query($conn2, "select d.no_kbon, d.no_kbon_det, d.cicilan_ke, d.total_cicilan, d.tgl_tempo, d.dpp, d.ppn, d.pph, d.ro, d.ftr, d.potongan, d.total, d.status_pl,
        h.tgl_kbon, h.nama_supp, h.curr, h.create_user, h.create_date, h.status, h.no_faktur, h.supp_inv, h.tgl_inv, h.confirm_user, h.confirm_date, h.profit_center,
        h.no_coa, h.nama_coa, h.rate, h.from_account, h.from_bank, h.from_bank_curr
        from kontrabon_h_installment_detail d
        inner join kontrabon_h h on h.no_kbon = d.no_kbon
        where $where
        order by h.tgl_kbon desc, d.no_kbon asc, d.cicilan_ke asc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];
        $kbonDet = $row['no_kbon_det'];
        $tgl_kbon = $row['tgl_kbon'];

        $queryClosing = mysqli_query($conn2, "select status_closing from tbl_closing_periode where '" . mysqli_real_escape_string($conn2, $tgl_kbon) . "' between tgl_awal and tgl_akhir");
        $rowClosing = mysqli_fetch_assoc($queryClosing);
        $status_closing = $rowClosing['status_closing'] ?? '';

        $rowStatus = $row['status'];

        $data[] = [
            'type'           => 'Installment',
            'profit_center'  => $row['profit_center'],
            'no_kbon'        => $kbonDet,
            'no_kbon_parent' => $kbonno,
            'tgl_kbon'    => !empty($tgl_kbon) ? date('d-M-Y', strtotime($tgl_kbon)) : '-',
            'tgl_kbon_raw' => $tgl_kbon,
            'tgl_kbon2'   => 'Installment ' . $row['cicilan_ke'] . '/' . $row['total_cicilan'],
            'nama_supp'   => $row['nama_supp'],
            'subtotal'    => number_format($row['dpp'], 2),
            'tax'         => number_format($row['ppn'], 2),
            'pph_value'   => '- ' . number_format($row['pph'], 2),
            'dp'          => '- ' . number_format($row['ftr'], 2),
            'return'      => number_format($row['ro'], 2),
            'potong'      => number_format($row['potongan'], 2),
            'total'       => number_format($row['total'], 2),
            'total_raw'   => $row['total'],
            'subtotal_raw' => $row['dpp'],
            'tax_raw'     => $row['ppn'],
            'pph_raw'     => $row['pph'],
            'curr'        => $row['curr'],
            'status'      => $rowStatus,
            'status_pl'   => $row['status_pl'],
            'no_coa'      => $row['no_coa'],
            'nama_coa'    => $row['nama_coa'],
            'rate'        => $row['rate'],
            'from_account' => $row['from_account'],
            'from_bank'   => $row['from_bank'],
            'from_bank_curr' => $row['from_bank_curr'],
            'tgl_tempo'   => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'],
            'create_user' => $row['create_user'],
            'create_date' => !empty($row['create_date']) ? date('Y-m-d H:i:s', strtotime($row['create_date'])) : '-',
            'confirm_user' => $row['confirm_user'],
            'confirm_date' => !empty($row['confirm_date']) && $row['confirm_date'] != '0000-00-00' ? date('Y-m-d', strtotime($row['confirm_date'])) : '-',
            'no_faktur'   => $row['no_faktur'],
            'supp_inv'    => $row['supp_inv'],
            'tgl_inv'     => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'action'      => buildActionInstallment($kbonno, $kbonDet, $rowStatus, $status_closing, $fin, $app),
        ];
    }

    return $data;
}

function buildActionInstallment($kbonno, $kbonDet, $rowStatus, $status_closing, $fin, $app)
{
    $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-kbon-installment" data-no_kbon="' . htmlspecialchars($kbonno) . '" title="Cancel">'
        . '<i class="fa fa-times-circle"></i> Cancel</button>';

    $btnPdf = '<a href="pdf_pv_installment.php?no_kbon_det=' . htmlspecialchars($kbonDet) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF">'
        . '<i class="fa fa-file-pdf-o"></i> PDF</a>';

    if ($rowStatus == 'Approved' || $rowStatus == 'SECOND APPROVED') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
    } elseif ($rowStatus == 'FIRST APPROVED') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '<span class="kbon-status-label" style="background:#eef2f9;color:#1e3a8a;"><i class="fa fa-clock-o"></i> Waiting 2nd Approval</span></div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app == '1') {
        return '<div class="kbon-action-buttons">' . $btnCancel . $btnPdf . '</div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app != '1') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
    } elseif ($rowStatus == 'Cancel') {
        return '<span class="kbon-status-label kbon-status-canceled"><i class="fa fa-ban"></i> Canceled</span>';
    }

    return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
}

/* ====================== TYPE: DP ====================== */
/* Sumber data: header kontrabon_h_dp sudah menyimpan subtotal/dp_value/total final */

function getDataDp($conn1, $conn2, $filters, $fin, $app, $group)
{
    $where = "1=1";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " AND nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    if ($filters['status'] !== 'ALL' && $filters['status'] !== '') {
        $where .= " AND status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";
    }

    if (!empty($filters['no_kbon'])) {
        $where .= " AND no_kbon = '" . mysqli_real_escape_string($conn2, $filters['no_kbon']) . "'";
    }

    if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
        $where .= " AND " . $filters['filter_date'] . " BETWEEN '" . mysqli_real_escape_string($conn2, $filters['start_date']) . "' AND '" . mysqli_real_escape_string($conn2, $filters['end_date']) . "'";
    }

    $sql = mysqli_query($conn2, "select no_kbon, tgl_kbon, nama_supp, subtotal, dp_value, pph, total, curr, status,
        create_user, create_date, confirm_user, confirm_date, no_faktur, supp_inv, tgl_inv, tgl_tempo, profit_center,
        status_pl, from_account, from_bank, from_bank_curr, item_type, no_coa, nama_coa
        from kontrabon_h_dp
        where $where
        order by tgl_kbon desc");


    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];
        $tgl_kbon = $row['tgl_kbon'];

        $queryClosing = mysqli_query($conn2, "select status_closing from tbl_closing_periode where '" . mysqli_real_escape_string($conn2, $tgl_kbon) . "' between tgl_awal and tgl_akhir");
        $rowClosing = mysqli_fetch_assoc($queryClosing);
        $status_closing = $rowClosing['status_closing'] ?? '';

        $rowStatus = $row['status'];

        $data[] = [
            'type'        => 'DP',
            'profit_center' => $row['profit_center'],
            'no_kbon'     => $kbonno,
            'tgl_kbon'    => !empty($tgl_kbon) ? date('d-M-Y', strtotime($tgl_kbon)) : '-',
            'tgl_kbon_raw' => $tgl_kbon,
            'tgl_kbon2'   => '-',
            'nama_supp'   => $row['nama_supp'],
            'subtotal'    => number_format($row['subtotal'], 2),
            'tax'         => '-',
            'pph_value'   => number_format($row['pph'], 2),
            'dp'          => number_format($row['dp_value'], 2),
            'return'      => '-',
            'potong'      => '-',
            'total'       => number_format($row['total'], 2),
            'total_raw'   => $row['total'],
            'subtotal_raw' => $row['subtotal'],
            'tax_raw'     => 0,
            'pph_raw'     => $row['pph'],
            'curr'        => $row['curr'],
            'status'      => $rowStatus,
            'status_pl'   => $row['status_pl'],
            'from_account' => $row['from_account'],
            'from_bank'   => $row['from_bank'],
            'from_bank_curr' => $row['from_bank_curr'],
            'item_type'   => $row['item_type'],
            'no_coa'      => $row['no_coa'],
            'nama_coa'    => $row['nama_coa'],
            'tgl_tempo'   => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'],
            'create_user' => $row['create_user'],
            'create_date' => !empty($row['create_date']) ? date('Y-m-d H:i:s', strtotime($row['create_date'])) : '-',
            'confirm_user' => $row['confirm_user'],
            'confirm_date' => !empty($row['confirm_date']) && $row['confirm_date'] != '0000-00-00' ? date('Y-m-d', strtotime($row['confirm_date'])) : '-',
            'no_faktur'   => $row['no_faktur'],
            'supp_inv'    => $row['supp_inv'],
            'tgl_inv'     => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'detail_url'  => 'ajaxkbondp.php',
            'action'      => buildActionDp($kbonno, $rowStatus, $status_closing, $fin, $app),
        ];
    }

    return $data;
}

function buildActionDp($kbonno, $rowStatus, $status_closing, $fin, $app)
{
    $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-kbon-dp" data-no_kbon="' . htmlspecialchars($kbonno) . '" title="Cancel">'
        . '<i class="fa fa-times-circle"></i> Cancel</button>';

    $btnPdf = '<a href="pdf_pv_dp.php?nokontrabon=' . htmlspecialchars($kbonno) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF">'
        . '<i class="fa fa-file-pdf-o"></i> PDF</a>';

    if ($rowStatus == 'Approved') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app == '1') {
        return '<div class="kbon-action-buttons">' . $btnCancel . $btnPdf . '</div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app != '1') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
    } elseif ($rowStatus == 'Cancel') {
        return '<span class="kbon-status-label kbon-status-canceled"><i class="fa fa-ban"></i> Canceled</span>';
    }

    return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
}

/* ====================== TYPE: CBD ====================== */
/* Sumber data: header kontrabon_h_cbd sudah menyimpan subtotal/tax/pph/total final */

function getDataCbd($conn1, $conn2, $filters, $fin, $app, $group)
{
    $where = "1=1";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " AND nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    if ($filters['status'] !== 'ALL' && $filters['status'] !== '') {
        $where .= " AND status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";
    }

    if (!empty($filters['no_kbon'])) {
        $where .= " AND no_kbon = '" . mysqli_real_escape_string($conn2, $filters['no_kbon']) . "'";
    }

    if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
        $where .= " AND " . $filters['filter_date'] . " BETWEEN '" . mysqli_real_escape_string($conn2, $filters['start_date']) . "' AND '" . mysqli_real_escape_string($conn2, $filters['end_date']) . "'";
    }

    $sql = mysqli_query($conn2, "select no_kbon, tgl_kbon, nama_supp, subtotal, tax, pph, total, curr, status,
        create_user, create_date, confirm_user, confirm_date, no_faktur, supp_inv, tgl_inv, tgl_tempo, profit_center,
        status_pl, from_account, from_bank, from_bank_curr, item_type, no_coa, nama_coa
        from kontrabon_h_cbd
        where $where
        order by tgl_kbon desc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];
        $tgl_kbon = $row['tgl_kbon'];

        $queryClosing = mysqli_query($conn2, "select status_closing from tbl_closing_periode where '" . mysqli_real_escape_string($conn2, $tgl_kbon) . "' between tgl_awal and tgl_akhir");
        $rowClosing = mysqli_fetch_assoc($queryClosing);
        $status_closing = $rowClosing['status_closing'] ?? '';

        $rowStatus = $row['status'];

        $data[] = [
            'type'        => 'CBD',
            'profit_center' => $row['profit_center'],
            'no_kbon'     => $kbonno,
            'tgl_kbon'    => !empty($tgl_kbon) ? date('d-M-Y', strtotime($tgl_kbon)) : '-',
            'tgl_kbon_raw' => $tgl_kbon,
            'tgl_kbon2'   => '-',
            'nama_supp'   => $row['nama_supp'],
            'subtotal'    => number_format($row['subtotal'], 2),
            'tax'         => number_format($row['tax'], 2),
            'pph_value'   => '- ' . number_format($row['pph'], 2),
            'dp'          => '-',
            'return'      => '-',
            'potong'      => '-',
            'total'       => number_format($row['total'], 2),
            'total_raw'   => $row['total'],
            'subtotal_raw' => $row['subtotal'],
            'tax_raw'     => $row['tax'],
            'pph_raw'     => $row['pph'],
            'curr'        => $row['curr'],
            'status'      => $rowStatus,
            'status_pl'   => $row['status_pl'],
            'from_account' => $row['from_account'],
            'from_bank'   => $row['from_bank'],
            'from_bank_curr' => $row['from_bank_curr'],
            'item_type'   => $row['item_type'],
            'no_coa'      => $row['no_coa'],
            'nama_coa'    => $row['nama_coa'],
            'tgl_tempo'   => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'],
            'create_user' => $row['create_user'],
            'create_date' => !empty($row['create_date']) ? date('Y-m-d H:i:s', strtotime($row['create_date'])) : '-',
            'confirm_user' => $row['confirm_user'],
            'confirm_date' => !empty($row['confirm_date']) && $row['confirm_date'] != '0000-00-00' ? date('Y-m-d', strtotime($row['confirm_date'])) : '-',
            'no_faktur'   => $row['no_faktur'],
            'supp_inv'    => $row['supp_inv'],
            'tgl_inv'     => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'detail_url'  => 'ajaxkboncbd.php',
            'action'      => buildActionCbd($kbonno, $rowStatus, $status_closing, $fin, $app),
        ];
    }

    return $data;
}

function buildActionCbd($kbonno, $rowStatus, $status_closing, $fin, $app)
{
    $btnCancel = '<button type="button" class="btn btn-sm btn-outline-danger btn-cancel-kbon-cbd" data-no_kbon="' . htmlspecialchars($kbonno) . '" title="Cancel">'
        . '<i class="fa fa-times-circle"></i> Cancel</button>';

    $btnPdf = '<a href="pdf_pv_cbd.php?nokontrabon=' . htmlspecialchars($kbonno) . '" target="_blank" class="btn btn-sm btn-outline-success" title="View PDF">'
        . '<i class="fa fa-file-pdf-o"></i> PDF</a>';

    if ($rowStatus == 'Approved') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app == '1') {
        return '<div class="kbon-action-buttons">' . $btnCancel . $btnPdf . '</div>';
    } elseif ($rowStatus == 'draft' && $fin == '1' && $app != '1') {
        return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
    } elseif ($rowStatus == 'Cancel') {
        return '<span class="kbon-status-label kbon-status-canceled"><i class="fa fa-ban"></i> Canceled</span>';
    }

    return '<div class="kbon-action-buttons">' . $btnPdf . '</div>';
}

/* ====================== TYPE: BIAYA ====================== */
/* Payment Voucher umum (non-kontrabon) - header tbl_pv_h, detail multi-baris
   tbl_pv (bisa multi Profit Center/COA per PV, makanya tidak punya satu
   profit_center per header seperti type lain). Approval cuma satu tahap -
   status final-nya 'Approved' (lihat approvepv.php), beda dari Regular/
   Installment/DP/CBD yang dua tahap (SECOND APPROVED). Bank tujuan disimpan
   sebagai nomor rekening langsung di tbl_pv_h.to_akun, dicocokkan ke
   master_supplier_bank.bank_account (bukan via id_bank_account).
*/

function getDataBiaya($conn2, $filters)
{
    $where = "1=1";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " AND a.nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    if ($filters['status'] !== 'ALL' && $filters['status'] !== '') {
        $where .= " AND a.status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";
    }

    if ($filters['start_date'] !== '' && $filters['end_date'] !== '') {
        $where .= " AND a.pv_date BETWEEN '" . mysqli_real_escape_string($conn2, $filters['start_date']) . "' AND '" . mysqli_real_escape_string($conn2, $filters['end_date']) . "'";
    }

    $sql = mysqli_query($conn2, "select a.no_pv, a.pv_date, max(b.due_date) as due_date, a.nama_supp, b.profit_center, a.curr, ROUND(SUM((b.amount + (b.amount *b.ppn/100) - (b.amount *b.pph/100)) - (b.ded_add + (b.ded_add *b.ppn/100) - (b.ded_add *b.pph/100))),2) total, a.status
        from tbl_pv_h a
        inner join tbl_pv b on b.no_pv = a.no_pv
        where $where
        group by a.no_pv, b.profit_center
        order by a.pv_date,a.no_pv, b.profit_center desc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $data[] = [
            'type'        => 'Biaya',
            'profit_center' => $row['profit_center'],
            'no_kbon'     => $row['no_pv'],
            'tgl_kbon'    => !empty($row['pv_date']) ? date('d-M-Y', strtotime($row['pv_date'])) : '-',
            'nama_supp'   => $row['nama_supp'],
            'curr'        => $row['curr'],
            'total'       => number_format($row['total'], 2),
            'status'      => $row['status'],
        ];
    }

    return $data;
}

/* ====================== TYPE: SALDO AWAL ====================== */
/* Opening-balance PV — sumber data ap_saldo_payment_voucher. Baris
   dikembalikan dalam format yang identik dengan getDataRegular() dll. supaya
   semua filter/loop di caller tidak perlu tahu asal-usulnya.
   status_pl di-coalesce ke status: entri dengan status='SECOND APPROVED' dan
   status_pl=NULL langsung lolos filter payment menu tanpa perlu lewat PL. */

function getDataSaldoAwal($conn2, $filters)
{
    $where = "1=1";

    if (!empty($filters['nama_supp']) && $filters['nama_supp'] !== 'ALL') {
        $where .= " AND nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    if (!empty($filters['no_kbon'])) {
        $where .= " AND no_kbon = '" . mysqli_real_escape_string($conn2, $filters['no_kbon']) . "'";
    }

    if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        $col = !empty($filters['filter_date']) ? $filters['filter_date'] : 'tgl_kbon';
        if (!in_array($col, ['tgl_kbon', 'tgl_tempo'])) $col = 'tgl_kbon';
        $where .= " AND $col BETWEEN '" . mysqli_real_escape_string($conn2, $filters['start_date']) . "' AND '" . mysqli_real_escape_string($conn2, $filters['end_date']) . "'";
    }

    $sql = mysqli_query($conn2, "SELECT * FROM ap_saldo_payment_voucher WHERE $where ORDER BY tgl_kbon DESC");

    $data = [];
    while ($row = mysqli_fetch_assoc($sql)) {
        $tgl = $row['tgl_kbon'];
        $data[] = [
            'type'           => 'SaldoAwal',
            'profit_center'  => $row['profit_center'],
            'no_kbon'        => $row['no_kbon'],
            'tgl_kbon'       => !empty($tgl) ? date('d-M-Y', strtotime($tgl)) : '-',
            'tgl_kbon_raw'   => $tgl,
            'tgl_kbon2'      => '[' . $row['type_pv'] . '] Saldo Awal',
            'nama_supp'      => $row['nama_supp'],
            'subtotal'       => number_format($row['subtotal'], 2),
            'tax'            => number_format($row['tax'], 2),
            'pph_value'      => '- ' . number_format($row['pph'], 2),
            'dp'             => '-',
            'return'         => '-',
            'potong'         => '-',
            'total'          => number_format($row['total'], 2),
            'total_raw'      => (float) $row['total'],
            'subtotal_raw'   => (float) $row['subtotal'],
            'tax_raw'        => (float) $row['tax'],
            'pph_raw'        => (float) $row['pph'],
            'curr'           => $row['curr'],
            'rate'           => !empty($row['rate']) ? (float) $row['rate'] : 1,
            'status'         => $row['status'],
            // status_pl NULL → fallback ke status, sehingga entri dengan
            // status='SECOND APPROVED' langsung muncul di payment search.
            'status_pl'      => $row['status_pl'] !== null ? $row['status_pl'] : $row['status'],
            'status_pvl'     => $row['status_pvl'],
            'no_coa'         => $row['no_coa'] ?? '-',
            'nama_coa'       => $row['nama_coa'] ?? '-',
            'from_account'   => $row['from_account'],
            'from_bank'      => $row['from_bank'],
            'from_bank_curr' => $row['from_bank_curr'],
            'tgl_tempo'      => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw'  => $row['tgl_tempo'],
            'create_user'    => $row['create_user'],
            'create_date'    => !empty($row['create_date']) ? date('Y-m-d H:i:s', strtotime($row['create_date'])) : '-',
            'confirm_user'   => '',
            'confirm_date'   => '-',
            'no_faktur'      => '',
            'supp_inv'       => '',
            'tgl_inv'        => '-',
        ];
    }
    return $data;
}

/* ====================== STATUS PL PROPAGATION ====================== */
/* Begitu sebuah PV masuk/keluar Payment List, status approval PL-nya
   ('Draft'/'FIRST APPROVED'/'SECOND APPROVED', atau NULL kalau PL-nya
   dibatalkan) ditulis juga ke kolom status_pl di table header sumbernya,
   supaya kelihatan langsung dari kontrabon_h/kontrabon_h_dp/kontrabon_h_cbd/
   tbl_pv_h/kontrabon_h_installment_detail tanpa perlu join ke
   pv_payment_list_det. $no_kbon di sini adalah nilai yang sama seperti yang
   disimpan di pv_payment_list_det.no_kbon - untuk Installment itu nomor
   anak/cicilan (no_kbon_det), bukan nomor induk. */

function updateStatusPl($conn2, $type_pv, $no_kbon, $statusPl)
{
    $no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);
    $statusPlSql = $statusPl === null ? 'NULL' : "'" . mysqli_real_escape_string($conn2, $statusPl) . "'";

    if ($type_pv === 'Biaya') {
        mysqli_query($conn2, "update tbl_pv_h set status_pl = $statusPlSql where no_pv = '$no_kbon_esc'");
        return;
    }

    if ($type_pv === 'SaldoAwal') {
        mysqli_query($conn2, "update ap_saldo_payment_voucher set status_pl = $statusPlSql where no_kbon = '$no_kbon_esc'");
        return;
    }

    if ($type_pv === 'Installment') {
        // Cicilan-nya sendiri (kontrabon_h_installment_detail) ditandai per baris -
        // PL bisa saja hanya berisi sebagian cicilan dari sebuah PV. Header
        // (kontrabon_h) tetap ditandai juga, sama seperti status SECOND APPROVED
        // dipakai bersama oleh semua cicilan.
        mysqli_query($conn2, "update kontrabon_h_installment_detail set status_pl = $statusPlSql where no_kbon_det = '$no_kbon_esc'");

        $sqlParent = mysqli_query($conn2, "select no_kbon from kontrabon_h_installment_detail where no_kbon_det = '$no_kbon_esc'");
        $rowParent = mysqli_fetch_assoc($sqlParent);
        $parentKbon = $rowParent['no_kbon'] ?? null;
        if (!empty($parentKbon)) {
            mysqli_query($conn2, "update kontrabon_h set status_pl = $statusPlSql where no_kbon = '" . mysqli_real_escape_string($conn2, $parentKbon) . "'");
        }
        return;
    }

    $table = $type_pv === 'DP' ? 'kontrabon_h_dp' : ($type_pv === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
    mysqli_query($conn2, "update $table set status_pl = $statusPlSql where no_kbon = '$no_kbon_esc'");
}

/* ====================== STATUS PVL PROPAGATION ====================== */
/* Sama persis seperti updateStatusPl() di atas, tapi untuk feature Payment
   Voucher List (single approval) - pakai kolom status_pvl yang terpisah
   supaya kedua "list" feature ini tidak saling bertabrakan statusnya di
   table sumber yang sama. */

function updateStatusPvl($conn2, $type_pv, $no_kbon, $statusPvl)
{
    $no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);
    $statusPvlSql = $statusPvl === null ? 'NULL' : "'" . mysqli_real_escape_string($conn2, $statusPvl) . "'";

    if ($type_pv === 'Biaya') {
        mysqli_query($conn2, "update tbl_pv_h set status_pvl = $statusPvlSql where no_pv = '$no_kbon_esc'");
        return;
    }

    if ($type_pv === 'SaldoAwal') {
        mysqli_query($conn2, "update ap_saldo_payment_voucher set status_pvl = $statusPvlSql where no_kbon = '$no_kbon_esc'");
        return;
    }

    if ($type_pv === 'Installment') {
        mysqli_query($conn2, "update kontrabon_h_installment_detail set status_pvl = $statusPvlSql where no_kbon_det = '$no_kbon_esc'");

        $sqlParent = mysqli_query($conn2, "select no_kbon from kontrabon_h_installment_detail where no_kbon_det = '$no_kbon_esc'");
        $rowParent = mysqli_fetch_assoc($sqlParent);
        $parentKbon = $rowParent['no_kbon'] ?? null;
        if (!empty($parentKbon)) {
            mysqli_query($conn2, "update kontrabon_h set status_pvl = $statusPvlSql where no_kbon = '" . mysqli_real_escape_string($conn2, $parentKbon) . "'");
        }
        return;
    }

    $table = $type_pv === 'DP' ? 'kontrabon_h_dp' : ($type_pv === 'CBD' ? 'kontrabon_h_cbd' : 'kontrabon_h');
    mysqli_query($conn2, "update $table set status_pvl = $statusPvlSql where no_kbon = '$no_kbon_esc'");
}

/* ====================== ALREADY-PAID (OUTSTANDING) ====================== */
/* Sudah ditarik sebagian/seluruhnya lewat Bank-Out ATAU Petty Cash Out (Payment
   Voucher) - dipakai supaya kontrabon yang sudah dibayar sebagian tidak bisa
   ditarik lebih dari sisanya, dari channel pembayaran manapun. Dihitung ulang
   di sini (bukan dipercaya dari nilai yang ditampilkan di layar) supaya tidak
   rawan terhadap data yang sudah berubah sejak halaman dibuka. */

function getAlreadyPaidFor($conn2, $type_pv, $no_kbon)
{
    $type_pv_esc = mysqli_real_escape_string($conn2, $type_pv);
    $no_kbon_esc = mysqli_real_escape_string($conn2, $no_kbon);

    $sqlBank = mysqli_query($conn2, "select sum(a.total) total_paid
        from b_bankout_det a
        inner join b_bankout_h b on b.no_bankout = a.no_bankout
        where b.status != 'Cancel' and a.type_pv = '$type_pv_esc' and a.no_reff = '$no_kbon_esc'");
    $rowBank = mysqli_fetch_assoc($sqlBank);
    $paidBank = !empty($rowBank['total_paid']) ? (float) $rowBank['total_paid'] : 0;

    $sqlCash = mysqli_query($conn2, "select sum(a.amount) total_paid
        from c_petty_cashout_det a
        inner join c_petty_cashout_h b on b.no_pco = a.no_pco
        where b.status != 'Cancel' and a.type_pv = '$type_pv_esc' and a.no_reff = '$no_kbon_esc'");
    $rowCash = mysqli_fetch_assoc($sqlCash);
    $paidCash = !empty($rowCash['total_paid']) ? (float) $rowCash['total_paid'] : 0;

    $sqlPay = mysqli_query($conn2, "select sum(nominal) total_paid
        from payment_ftr
        where status != 'Cancel' and type_pv = '$type_pv_esc' and no_kbon = '$no_kbon_esc'");
    $rowPay = mysqli_fetch_assoc($sqlPay);
    $paidPay = !empty($rowPay['total_paid']) ? (float) $rowPay['total_paid'] : 0;

    return $paidBank + $paidCash + $paidPay;
}
