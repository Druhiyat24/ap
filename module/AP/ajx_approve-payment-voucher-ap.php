<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
session_start();

$user = $_SESSION['username'] ?? '';

$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : 'ALL';
$stage = isset($_POST['stage']) && $_POST['stage'] === 'second' ? 'second' : 'first';
$type_pv = isset($_POST['type_pv']) ? $_POST['type_pv'] : 'ALL';

// $querys = mysqli_query($conn1, "select Groupp, finance, ap_apprv_kb, ap_apprv_kb2 from userpassword where username = '" . mysqli_real_escape_string($conn1, $user) . "'");
// $rs = mysqli_fetch_assoc($querys);
$fin  = 1;
$app  = 1;
$app2 = 1;

$statusFilter = $stage === 'second' ? 'FIRST APPROVED' : 'draft';
$canSelect = $stage === 'second' ? ($fin == '1' && $app2 == '1') : ($fin == '1' && $app == '1');

$filters = [
    'nama_supp' => $nama_supp,
    'status'    => $statusFilter,
];

switch ($type_pv) {
    case 'Regular':
        $data = getApprovalRegular($conn2, $filters, $canSelect);
        break;
    case 'Installment':
        $data = getApprovalInstallment($conn2, $filters, $canSelect);
        break;
    case 'DP':
        $data = getApprovalDp($conn2, $filters, $canSelect);
        break;
    case 'CBD':
        $data = getApprovalCbd($conn2, $filters, $canSelect);
        break;
    case 'Saldo Awal':
        $data = getApprovalSaldoAwal($conn2, $filters, $canSelect);
        break;
    case 'ALL':
    default:
        $data = array_merge(
            getApprovalRegular($conn2, $filters, $canSelect),
            getApprovalInstallment($conn2, $filters, $canSelect),
            getApprovalDp($conn2, $filters, $canSelect),
            getApprovalCbd($conn2, $filters, $canSelect),
            getApprovalSaldoAwal($conn2, $filters, $canSelect)
        );
        break;
}

echo json_encode(['data' => $data]);

function buildSelect($id, $type, $canSelect, $cancelId)
{
    if (!$canSelect) {
        return '<span class="badge-pv-waiting">No Access</span>';
    }
    return '<input type="checkbox" class="pv-approve-chk" data-no_kbon="' . htmlspecialchars($id) . '" data-type="' . htmlspecialchars($type) . '" data-cancel_id="' . htmlspecialchars($cancelId) . '">';
}

// Sama seperti form-closing-payreg.php: due date yang sudah lewat (<= hari ini) ditandai overdue.
function isOverdue($rawDueDate)
{
    return !empty($rawDueDate) && $rawDueDate <= date('Y-m-d');
}

// Due date yang belum lewat tapi tinggal <= 7 hari dari sekarang ditandai due_soon (kuning).
function isDueSoon($rawDueDate)
{
    if (empty($rawDueDate)) {
        return false;
    }
    $today = date('Y-m-d');
    $weekFromNow = date('Y-m-d', strtotime('+7 days'));
    return $rawDueDate > $today && $rawDueDate <= $weekFromNow;
}

/* ====================== TYPE: REGULAR ====================== */

function getApprovalRegular($conn2, $filters, $canSelect)
{
    // Baris Installment ikut tersimpan di tabel kontrabon juga (lihat cancelkboninstallment.php),
    // jadi harus dikecualikan disini supaya tidak ter-approve lewat jalur Regular -
    // yang cuma update kontrabon/kontrabon_h dan akan meninggalkan
    // kontrabon_h_installment_detail tetap draft.
    $where = "a.no_kbon NOT LIKE 'PV-AP/INS/%' and h.status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " and a.nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    $sql = mysqli_query($conn2, "select a.no_kbon, a.tgl_kbon, a.nama_supp,
        SUM(a.subtotal) as subtotal, SUM(a.tax) as tax, a.curr, a.status, SUM(a.pph_value) as pph_value, a.dp_value,
        a.tgl_tempo, a.create_user, a.no_faktur, a.supp_inv, a.tgl_inv,
        b.jml_return, b.jml_potong
        from kontrabon a
        inner join kontrabon_h h on h.no_kbon = a.no_kbon
        inner join potongan b on b.no_kbon = a.no_kbon
        where $where
        group by a.no_kbon
        order by a.tgl_kbon desc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];

        $sql45 = mysqli_query($conn2, "select (c.qty * c.price) * (d.tax / 100) as tax_return from kontrabon a left join bppb_new c on c.no_bpb = a.no_bpb left join bpb_new d on d.no_bpb = a.no_bpb where a.no_kbon = '" . mysqli_real_escape_string($conn2, $kbonno) . "' and a.no_bpb != '' group by a.no_kbon");
        $row45 = mysqli_fetch_assoc($sql45);
        $tax_return = $row45['tax_return'] ?? 0;

        $sub = $row['subtotal'];
        $tax = $row['tax'] + $tax_return;
        $pph = $row['pph_value'];
        $dp = $row['dp_value'];
        $return = $row['jml_return'] + $tax_return;
        $potong = $row['jml_potong'];
        $total = $sub + $tax - ($pph + $dp + $return) + $potong;

        $data[] = [
            'no_kbon'     => $kbonno,
            'type_label'  => 'Regular',
            'tgl_kbon'    => !empty($row['tgl_kbon']) ? date('d-M-Y', strtotime($row['tgl_kbon'])) : '-',
            'tgl_kbon2'   => 'Regular',
            'nama_supp'   => $row['nama_supp'],
            'subtotal'    => number_format($sub, 2),
            'tax'         => number_format($tax, 2),
            'pph_value'   => '- ' . number_format($pph, 2),
            'dp'          => '- ' . number_format($dp, 2),
            'return'      => number_format($return, 2),
            'potong'      => number_format($potong, 2),
            'total'       => number_format($total, 2),
            'curr'        => $row['curr'],
            'status'      => $row['status'],
            'tgl_tempo'     => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'] ?: '',
            'overdue'       => isOverdue($row['tgl_tempo']),
            'due_soon'      => isDueSoon($row['tgl_tempo']),
            'create_user' => $row['create_user'],
            'no_faktur'   => $row['no_faktur'],
            'supp_inv'    => $row['supp_inv'],
            'tgl_inv'     => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'select'      => buildSelect($kbonno, 'regular', $canSelect, $kbonno),
            'cancel_id'   => $kbonno,
        ];
    }

    return $data;
}

/* ====================== TYPE: INSTALLMENT ====================== */
/* Satu baris per cicilan (kontrabon_h_installment_detail). Status yang dipakai
   untuk filter & approve adalah status cicilan itu sendiri (d.status), bukan
   status header (h.status) - supaya tiap cicilan bisa diapprove sendiri-sendiri.
   Cancel tetap menyasar no_kbon INDUK (lihat cancelkboninstallment.php) karena
   cancel selalu membatalkan seluruh cicilan sekaligus. */

function getApprovalInstallment($conn2, $filters, $canSelect)
{
    $where = "h.no_kbon LIKE 'PV-AP/INS/%' and d.status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " and h.nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    $sql = mysqli_query($conn2, "select d.no_kbon, d.no_kbon_det, d.cicilan_ke, d.total_cicilan, d.tgl_tempo, d.dpp, d.ppn, d.pph, d.ro, d.ftr, d.potongan, d.total, d.status,
        h.tgl_kbon, h.nama_supp, h.curr, h.create_user, h.no_faktur, h.supp_inv, h.tgl_inv
        from kontrabon_h_installment_detail d
        inner join kontrabon_h h on h.no_kbon = d.no_kbon
        where $where
        order by h.tgl_kbon desc, d.no_kbon asc, d.cicilan_ke asc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $data[] = [
            'no_kbon'        => $row['no_kbon_det'],
            'no_kbon_parent' => $row['no_kbon'],
            'type_label'     => 'Installment ' . $row['cicilan_ke'] . '/' . $row['total_cicilan'],
            'tgl_kbon'       => !empty($row['tgl_kbon']) ? date('d-M-Y', strtotime($row['tgl_kbon'])) : '-',
            'tgl_kbon2'      => 'Installment ' . $row['cicilan_ke'] . '/' . $row['total_cicilan'],
            'nama_supp'      => $row['nama_supp'],
            'subtotal'       => number_format($row['dpp'], 2),
            'tax'            => number_format($row['ppn'], 2),
            'pph_value'      => '- ' . number_format($row['pph'], 2),
            'dp'             => '- ' . number_format($row['ftr'], 2),
            'return'         => number_format($row['ro'], 2),
            'potong'         => number_format($row['potongan'], 2),
            'total'          => number_format($row['total'], 2),
            'curr'           => $row['curr'],
            'status'         => $row['status'],
            'tgl_tempo'      => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw'  => $row['tgl_tempo'] ?: '',
            'overdue'        => isOverdue($row['tgl_tempo']),
            'due_soon'       => isDueSoon($row['tgl_tempo']),
            'create_user'    => $row['create_user'],
            'no_faktur'      => $row['no_faktur'],
            'supp_inv'       => $row['supp_inv'],
            'tgl_inv'        => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'select'         => buildSelect($row['no_kbon_det'], 'installment', $canSelect, $row['no_kbon']),
            'cancel_id'      => $row['no_kbon'],
        ];
    }

    return $data;
}

/* ====================== TYPE: DP ====================== */

function getApprovalDp($conn2, $filters, $canSelect)
{
    $where = "status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " and nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    $sql = mysqli_query($conn2, "select no_kbon, tgl_kbon, nama_supp, subtotal, dp_value, pph, total, curr, status,
        create_user, no_faktur, supp_inv, tgl_inv, tgl_tempo
        from kontrabon_h_dp
        where $where
        order by tgl_kbon desc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];

        $data[] = [
            'no_kbon'     => $kbonno,
            'type_label'  => 'DP',
            'tgl_kbon'    => !empty($row['tgl_kbon']) ? date('d-M-Y', strtotime($row['tgl_kbon'])) : '-',
            'tgl_kbon2'   => 'DP',
            'nama_supp'   => $row['nama_supp'],
            'subtotal'    => number_format($row['subtotal'], 2),
            'tax'         => '-',
            'pph_value'   => number_format($row['pph'], 2),
            'dp'          => number_format($row['dp_value'], 2),
            'return'      => '-',
            'potong'      => '-',
            'total'       => number_format($row['total'], 2),
            'curr'        => $row['curr'],
            'status'      => $row['status'],
            'tgl_tempo'     => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'] ?: '',
            'overdue'       => isOverdue($row['tgl_tempo']),
            'due_soon'      => isDueSoon($row['tgl_tempo']),
            'create_user' => $row['create_user'],
            'no_faktur'   => $row['no_faktur'],
            'supp_inv'    => $row['supp_inv'],
            'tgl_inv'     => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'detail_url'  => 'ajaxkbondp.php',
            'select'      => buildSelect($kbonno, 'dp', $canSelect, $kbonno),
            'cancel_id'   => $kbonno,
        ];
    }

    return $data;
}

/* ====================== TYPE: SALDO AWAL ====================== */

function getApprovalSaldoAwal($conn2, $filters, $canSelect)
{
    $where = "status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " and nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    $sql = mysqli_query($conn2, "SELECT no_kbon, tgl_kbon, nama_supp, tgl_tempo, curr,
        SUM(total) total, status
        FROM ap_saldo_payment_voucher
        WHERE $where
        GROUP BY no_kbon, tgl_kbon, nama_supp, tgl_tempo, curr, status
        ORDER BY tgl_kbon DESC");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];

        $data[] = [
            'no_kbon'       => $kbonno,
            'type_label'    => 'Saldo Awal',
            'tgl_kbon'      => !empty($row['tgl_kbon']) ? date('d-M-Y', strtotime($row['tgl_kbon'])) : '-',
            'tgl_kbon2'     => 'Saldo Awal',
            'nama_supp'     => $row['nama_supp'],
            'subtotal'      => number_format($row['total'], 2),
            'tax'           => '-',
            'pph_value'     => '-',
            'dp'            => '-',
            'return'        => '-',
            'potong'        => '-',
            'total'         => number_format($row['total'], 2),
            'curr'          => $row['curr'],
            'status'        => $row['status'],
            'tgl_tempo'     => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'] ?: '',
            'overdue'       => isOverdue($row['tgl_tempo']),
            'due_soon'      => isDueSoon($row['tgl_tempo']),
            'create_user'   => '-',
            'no_faktur'     => '-',
            'supp_inv'      => '-',
            'tgl_inv'       => '-',
            'select'        => buildSelect($kbonno, 'saldo_awal', $canSelect, $kbonno),
            'cancel_id'     => $kbonno,
        ];
    }

    return $data;
}

/* ====================== TYPE: CBD ====================== */

function getApprovalCbd($conn2, $filters, $canSelect)
{
    $where = "status = '" . mysqli_real_escape_string($conn2, $filters['status']) . "'";

    if ($filters['nama_supp'] !== 'ALL' && $filters['nama_supp'] !== '') {
        $where .= " and nama_supp = '" . mysqli_real_escape_string($conn2, $filters['nama_supp']) . "'";
    }

    $sql = mysqli_query($conn2, "select no_kbon, tgl_kbon, nama_supp, subtotal, tax, pph, total, curr, status,
        create_user, no_faktur, supp_inv, tgl_inv, tgl_tempo
        from kontrabon_h_cbd
        where $where
        order by tgl_kbon desc");

    $data = [];

    while ($row = mysqli_fetch_assoc($sql)) {
        $kbonno = $row['no_kbon'];

        $data[] = [
            'no_kbon'     => $kbonno,
            'type_label'  => 'CBD',
            'tgl_kbon'    => !empty($row['tgl_kbon']) ? date('d-M-Y', strtotime($row['tgl_kbon'])) : '-',
            'tgl_kbon2'   => 'CBD',
            'nama_supp'   => $row['nama_supp'],
            'subtotal'    => number_format($row['subtotal'], 2),
            'tax'         => number_format($row['tax'], 2),
            'pph_value'   => '- ' . number_format($row['pph'], 2),
            'dp'          => '-',
            'return'      => '-',
            'potong'      => '-',
            'total'       => number_format($row['total'], 2),
            'curr'        => $row['curr'],
            'status'      => $row['status'],
            'tgl_tempo'     => !empty($row['tgl_tempo']) ? date('d-M-Y', strtotime($row['tgl_tempo'])) : '-',
            'tgl_tempo_raw' => $row['tgl_tempo'] ?: '',
            'overdue'       => isOverdue($row['tgl_tempo']),
            'due_soon'      => isDueSoon($row['tgl_tempo']),
            'create_user' => $row['create_user'],
            'no_faktur'   => $row['no_faktur'],
            'supp_inv'    => $row['supp_inv'],
            'tgl_inv'     => !empty($row['tgl_inv']) ? date('d-M-Y', strtotime($row['tgl_inv'])) : '-',
            'detail_url'  => 'ajaxkboncbd.php',
            'select'      => buildSelect($kbonno, 'cbd', $canSelect, $kbonno),
            'cancel_id'   => $kbonno,
        ];
    }

    return $data;
}
