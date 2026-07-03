<?php
include '../../conn/conn.php';
include 'pv_data_functions.php';
header('Content-Type: application/json');
session_start();

$user = $_SESSION['username'] ?? '';

$nama_supp   = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : 'ALL';
$status      = isset($_POST['status']) ? $_POST['status'] : 'ALL';
$type_pv     = isset($_POST['type_pv']) ? $_POST['type_pv'] : 'Regular';
$filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : 'tgl_kbon';
$start_date  = !empty($_POST['start_date']) ? date('Y-m-d', strtotime($_POST['start_date'])) : '';
$end_date    = !empty($_POST['end_date']) ? date('Y-m-d', strtotime($_POST['end_date'])) : '';

if (!in_array($filter_date, ['tgl_kbon', 'create_date', 'confirm_date'])) {
    $filter_date = 'tgl_kbon';
}

// flag finance/approver yang menentukan tombol action mana yang berhak ditampilkan, sama seperti kontrabon.php
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
        usort($data, function($a, $b) {
            return strcmp($a['no_kbon'] ?? '', $b['no_kbon'] ?? '');
        });
        break;
}

echo json_encode(['data' => $data]);
