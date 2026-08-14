<?php
header('Content-Type: application/json');
include '../../conn/conn.php';

try {
    $type_cashflow = isset($_POST['type_cashflow']) ? trim($_POST['type_cashflow']) : '';
    $nama_category = isset($_POST['nama_category']) ? trim($_POST['nama_category']) : '';
    $nama_subcategory = isset($_POST['nama_subcategory']) ? trim($_POST['nama_subcategory']) : '';
    $show_subcategory = isset($_POST['show_subcategory']) ? trim($_POST['show_subcategory']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    if ($type_cashflow !== 'Cash In' && $type_cashflow !== 'Cash Out') {
        throw new Exception('Type tidak valid.');
    }
    if ($nama_category === '') {
        throw new Exception('Category tidak boleh kosong.');
    }
    if ($nama_subcategory === '') {
        throw new Exception('Subcategory tidak boleh kosong.');
    }
    if ($show_subcategory === '') {
        throw new Exception('Short Name tidak boleh kosong.');
    }
    if ($status !== 'Y' && $status !== 'N') {
        throw new Exception('Status tidak valid.');
    }

    $type_cashflow_esc = mysqli_real_escape_string($conn2, $type_cashflow);
    $nama_category_esc = mysqli_real_escape_string($conn2, $nama_category);
    $nama_subcategory_esc = mysqli_real_escape_string($conn2, $nama_subcategory);
    $show_subcategory_esc = mysqli_real_escape_string($conn2, $show_subcategory);

    $resId = mysqli_query($conn2, "SELECT COALESCE(MAX(id), 0) + 1 as next_id FROM master_cash_flow");
    $rowId = mysqli_fetch_assoc($resId);
    $newId = (int) $rowId['next_id'];

    $resUrutan = mysqli_query($conn2, "SELECT COALESCE(MAX(urutan), 0) + 1 as next_urutan FROM master_cash_flow WHERE type_cashflow = '$type_cashflow_esc' AND nama_category = '$nama_category_esc'");
    $rowUrutan = mysqli_fetch_assoc($resUrutan);
    $newUrutan = (int) $rowUrutan['next_urutan'];

    $sql = "INSERT INTO master_cash_flow (id, type_cashflow, nama_category, nama_subcategory, show_subcategory, urutan, status)
        VALUES ($newId, '$type_cashflow_esc', '$nama_category_esc', '$nama_subcategory_esc', '$show_subcategory_esc', $newUrutan, '$status')";
    if (!mysqli_query($conn2, $sql)) {
        throw new Exception(mysqli_error($conn2));
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
