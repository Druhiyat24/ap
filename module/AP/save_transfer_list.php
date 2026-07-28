<?php
session_start();
include '../../conn/conn.php';
header('Content-Type: application/json');

$user = $_SESSION['username'] ?? '';

$tl_date         = date('Y-m-d');
$tipe_mutasi     = trim($_POST['tipe_mutasi'] ?? '');
$jenis_otorisasi = trim($_POST['jenis_otorisasi'] ?? '');
$tanggal_efektif = !empty($_POST['tanggal_efektif']) ? date('Y-m-d', strtotime($_POST['tanggal_efektif'])) : '';
$jenis_biaya     = trim($_POST['jenis_biaya'] ?? '');
$pl_numbers      = isset($_POST['pl_numbers']) && is_array($_POST['pl_numbers']) ? $_POST['pl_numbers'] : [];

if ($tipe_mutasi === '' || $jenis_otorisasi === '' || $tanggal_efektif === '' || $jenis_biaya === '') {
    echo json_encode(['status' => 'error', 'message' => 'Tipe Mutasi, Jenis Otorisasi, Tanggal Efektif, dan Jenis Biaya wajib diisi.']);
    exit;
}

if ($tanggal_efektif < date('Y-m-d')) {
    echo json_encode(['status' => 'error', 'message' => 'Tanggal Efektif tidak boleh mundur dari hari ini.']);
    exit;
}

if (empty($pl_numbers)) {
    echo json_encode(['status' => 'error', 'message' => 'Pilih minimal 1 Payment List.']);
    exit;
}

$plList = [];
foreach ($pl_numbers as $pl) {
    $plList[] = mysqli_real_escape_string($conn2, $pl);
}
$plListSql = "'" . implode("','", $plList) . "'";

// Pastikan tidak ada PL yang sudah kepakai atau tidak eligible
$checkResult = mysqli_query($conn2, "SELECT pl_number, status FROM pv_payment_list_h
    WHERE pl_number IN ($plListSql) AND status NOT IN ('Draft','FIRST APPROVED')");
if (mysqli_num_rows($checkResult) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ada Payment List yang statusnya tidak eligible (bukan Draft/First Approval). Silahkan refresh dan coba lagi.']);
    exit;
}

$checkUsed = mysqli_query($conn2, "SELECT d.pl_number FROM pv_transfer_list_det d
    INNER JOIN pv_transfer_list_h th ON th.tl_number = d.tl_number
    WHERE th.status != 'Cancel' AND d.pl_number IN ($plListSql)");
if (mysqli_num_rows($checkUsed) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ada Payment List yang sudah masuk Transfer List lain. Silahkan refresh dan coba lagi.']);
    exit;
}

$sqlNumber = mysqli_query($conn2, "SELECT CONCAT('TL/', DATE_FORMAT(CURRENT_DATE(), '%Y/%m'), '/',
    LPAD(COALESCE(MAX(CAST(RIGHT(tl_number, 5) AS UNSIGNED)), 0) + 1, 5, '0')) nomor
    FROM pv_transfer_list_h WHERE tl_number LIKE CONCAT('TL/', DATE_FORMAT(CURRENT_DATE(), '%Y/%m'), '/%')");
$rowNumber = mysqli_fetch_assoc($sqlNumber);
$tl_number = $rowNumber['nomor'];

mysqli_begin_transaction($conn2);

try {
    $tl_number_esc = mysqli_real_escape_string($conn2, $tl_number);
    $tipe_mutasi_esc = mysqli_real_escape_string($conn2, $tipe_mutasi);
    $jenis_otorisasi_esc = mysqli_real_escape_string($conn2, $jenis_otorisasi);
    $jenis_biaya_esc = mysqli_real_escape_string($conn2, $jenis_biaya);
    $user_esc = mysqli_real_escape_string($conn2, $user);

    $insH = mysqli_query($conn2, "INSERT INTO pv_transfer_list_h
        (tl_number, tl_date, tipe_mutasi, jenis_otorisasi, tanggal_efektif, jenis_biaya, status, created_by, created_date)
        VALUES ('$tl_number_esc', '$tl_date', '$tipe_mutasi_esc', '$jenis_otorisasi_esc', '$tanggal_efektif', '$jenis_biaya_esc', 'Draft', '$user_esc', NOW())");

    if (!$insH) throw new Exception(mysqli_error($conn2));

    foreach ($plList as $pl) {
        $insD = mysqli_query($conn2, "INSERT INTO pv_transfer_list_det (tl_number, pl_number) VALUES ('$tl_number_esc', '$pl')");
        if (!$insD) throw new Exception(mysqli_error($conn2));
    }

    mysqli_commit($conn2);
    echo json_encode(['status' => 'ok', 'tl_number' => $tl_number]);
} catch (Exception $e) {
    mysqli_rollback($conn2);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
