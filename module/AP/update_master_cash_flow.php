<?php
header('Content-Type: application/json');
include '../../conn/conn.php';

try {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $type_cashflow = isset($_POST['type_cashflow']) ? trim($_POST['type_cashflow']) : '';
    $nama_category = isset($_POST['nama_category']) ? trim($_POST['nama_category']) : '';
    $nama_subcategory = isset($_POST['nama_subcategory']) ? trim($_POST['nama_subcategory']) : '';
    $show_subcategory = isset($_POST['show_subcategory']) ? trim($_POST['show_subcategory']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    if ($id <= 0) {
        throw new Exception('ID tidak valid.');
    }
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

    $sql = "UPDATE master_cash_flow SET
        type_cashflow = '$type_cashflow_esc',
        nama_category = '$nama_category_esc',
        nama_subcategory = '$nama_subcategory_esc',
        show_subcategory = '$show_subcategory_esc',
        status = '$status'
        WHERE id = $id";
    if (!mysqli_query($conn2, $sql)) {
        throw new Exception(mysqli_error($conn2));
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
