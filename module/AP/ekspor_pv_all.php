<?php
session_start();
include '../../conn/conn.php';
include 'pv_data_functions.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=payment_voucher.xls");

$user = $_SESSION['username'] ?? '';

$nama_supp   = isset($_GET['nama_supp']) ? $_GET['nama_supp'] : 'ALL';
$status      = isset($_GET['status']) ? $_GET['status'] : 'ALL';
$type_pv     = isset($_GET['type_pv']) ? $_GET['type_pv'] : 'ALL';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : 'tgl_kbon';
$start_date  = !empty($_GET['start_date']) ? date('Y-m-d', strtotime($_GET['start_date'])) : '';
$end_date    = !empty($_GET['end_date']) ? date('Y-m-d', strtotime($_GET['end_date'])) : '';

if (!in_array($filter_date, ['tgl_kbon', 'create_date', 'confirm_date'])) {
    $filter_date = 'tgl_kbon';
}

// Sama seperti ajx_payment-voucher-ap.php - pakai fungsi query yang sama
// (pv_data_functions.php) supaya hasil export selalu konsisten dengan apa
// yang tampil di listing on-screen, untuk semua 4 tipe Payment Voucher.
$querys = mysqli_query($conn1, "select Groupp, finance, ap_apprv_kb from userpassword where username = '" . mysqli_real_escape_string($conn1, $user) . "'");
$rs = mysqli_fetch_assoc($querys);
$group = $rs['Groupp'] ?? '';
$fin   = $rs['finance'] ?? '';
$app   = $rs['ap_apprv_kb'] ?? '';

$filters = [
    'nama_supp'   => $nama_supp,
    'status'      => $status,
    'filter_date' => $filter_date,
    'start_date'  => $start_date,
    'end_date'    => $end_date,
];

switch ($type_pv) {
    case 'Regular':
        $data = getDataRegular($conn1, $conn2, $filters, $fin, $app, $group);
        break;
    case 'Installment':
        $data = getDataInstallment($conn1, $conn2, $filters, $fin, $app, $group);
        break;
    case 'DP':
        $data = getDataDp($conn1, $conn2, $filters, $fin, $app, $group);
        break;
    case 'CBD':
        $data = getDataCbd($conn1, $conn2, $filters, $fin, $app, $group);
        break;
    case 'ALL':
    default:
        $data = array_merge(
            getDataRegular($conn1, $conn2, $filters, $fin, $app, $group),
            getDataInstallment($conn1, $conn2, $filters, $fin, $app, $group),
            getDataDp($conn1, $conn2, $filters, $fin, $app, $group),
            getDataCbd($conn1, $conn2, $filters, $fin, $app, $group)
        );
        usort($data, function ($a, $b) {
            return strtotime($b['tgl_kbon']) <=> strtotime($a['tgl_kbon']);
        });
        break;
}

$filter_date_label = [
    'tgl_kbon'     => 'PAYMENT VOUCHER DATE',
    'create_date'  => 'CREATED DATE',
    'confirm_date' => 'APPROVED DATE',
][$filter_date];

$period_label = ($start_date !== '' && $end_date !== '')
    ? date('d F Y', strtotime($start_date)) . ' - ' . date('d F Y', strtotime($end_date))
    : 'ALL';
?>
<html>
<head>
    <title>Export Data Ke Excel</title>
</head>
<body>
    <style type="text/css">
        body{ font-family: sans-serif; }
        table{ margin: 20px auto; border-collapse: collapse; }
        table th, table td{ border: 1px solid #3c3c3c; padding: 3px 8px; }
    </style>

    <h4>DATA PAYMENT VOUCHER<?php echo $type_pv !== 'ALL' ? ' - ' . strtoupper($type_pv) : ''; ?>
    <br/><?php echo $nama_supp === 'ALL' ? 'ALL SUPPLIER' : strtoupper($nama_supp); ?>
    <br/>PERIODE <?php echo strtoupper($period_label); ?></h4>

    FILTER DATE: <?php echo $filter_date_label; ?><br/>
    STATUS: <?php echo strtoupper($status); ?>

    <table style="width:100%;font-size:10px;" border="1">
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center; vertical-align: middle;">Type</th>
            <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">No Payment Voucher</th>
            <th style="text-align: center; vertical-align: middle;">Payment Voucher Date</th>
            <th style="text-align: center; vertical-align: middle;">Due Date</th>
            <th style="text-align: center; vertical-align: middle;">No Faktur</th>
            <th style="text-align: center; vertical-align: middle;">SubTotal</th>
            <th style="text-align: center; vertical-align: middle;">PPN</th>
            <th style="text-align: center; vertical-align: middle;">PPH</th>
            <th style="text-align: center; vertical-align: middle;">Total CBD/DP</th>
            <th style="text-align: center; vertical-align: middle;">Return</th>
            <th style="text-align: center; vertical-align: middle;">Potongan</th>
            <th style="text-align: center; vertical-align: middle;">Total Payment Voucher</th>
            <th style="text-align: center; vertical-align: middle;">Currency</th>
            <th style="text-align: center; vertical-align: middle;">Status</th>
            <th style="text-align: center; vertical-align: middle;">Create By</th>
            <th style="text-align: center; vertical-align: middle;">Create Date</th>
            <th style="text-align: center; vertical-align: middle;">Approved By</th>
            <th style="text-align: center; vertical-align: middle;">Approved Date</th>
        </tr>
        <?php $no = 1; foreach ($data as $row): ?>
        <tr style="font-size:12px;text-align:center;">
            <td><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($row['type']); ?></td>
            <td style="text-align:left;"><?php echo htmlspecialchars($row['nama_supp']); ?></td>
            <td><?php echo htmlspecialchars($row['no_kbon']); ?></td>
            <td><?php echo htmlspecialchars($row['tgl_kbon']); ?></td>
            <td><?php echo htmlspecialchars($row['tgl_tempo']); ?></td>
            <td><?php echo htmlspecialchars($row['no_faktur']); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($row['subtotal']); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($row['tax']); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($row['pph_value']); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($row['dp']); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($row['return']); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($row['potong']); ?></td>
            <td style="text-align:right;"><?php echo htmlspecialchars($row['total']); ?></td>
            <td><?php echo htmlspecialchars($row['curr']); ?></td>
            <td><?php echo htmlspecialchars($row['status_label'] ?? $row['status']); ?></td>
            <td><?php echo htmlspecialchars($row['create_user']); ?></td>
            <td><?php echo htmlspecialchars($row['create_date']); ?></td>
            <td><?php echo htmlspecialchars($row['confirm_user']); ?></td>
            <td><?php echo htmlspecialchars($row['confirm_date']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
