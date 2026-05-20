<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');


$action = $_POST['action'] ?? '';

// ===== GET SUPPLIER / CUSTOMER LIST by tipe_sup =====
if ($action === 'get_supplier_list') {
    $type  = $_POST['type'] ?? 'S';
    $where = $type === 'S' ? "tipe_sup = 'S'" : "tipe_sup != 'S'";
    $q     = mysqli_query($conn2,
        "SELECT id_supplier, Supplier FROM mastersupplier
         WHERE $where AND Supplier IS NOT NULL AND TRIM(Supplier) != ''
         ORDER BY Supplier ASC"
    );
    $rows = [];
    while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;
    echo json_encode($rows);
    exit;
}

// ===== GET ONE (for Edit) =====
if ($action === 'get_one') {
    $id   = intval($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn2, "SELECT * FROM master_supplier_bank WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    echo json_encode(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)));
    exit;
}

// ===== SERVER-SIDE DATATABLE =====
$draw   = intval($_POST['draw']   ?? 1);
$start  = intval($_POST['start']  ?? 0);
$length = intval($_POST['length'] ?? 10);
$search = $_POST['search']['value'] ?? '';

$filter_type     = $_POST['filter_type']     ?? '';
$filter_supplier = $_POST['filter_supplier'] ?? '';

$where = "WHERE 1=1";
if ($filter_type !== '') {
    $ft = mysqli_real_escape_string($conn2, $filter_type);
    $where .= " AND m.tipe_sup = '$ft'";
}
if ($filter_supplier !== '') {
    $fs = mysqli_real_escape_string($conn2, $filter_supplier);
    $where .= " AND m.id_supplier = '$fs'";
}
if ($search !== '') {
    $s = mysqli_real_escape_string($conn2, $search);
    $where .= " AND (m.id_supplier LIKE '%$s%' OR m.bank_name LIKE '%$s%'
                  OR m.bank_account LIKE '%$s%' OR m.beneficiary_name LIKE '%$s%'
                  OR m.bank_currency LIKE '%$s%')";
}

$total = (int)(mysqli_fetch_assoc(
    mysqli_query($conn2,
        "SELECT COUNT(*) AS c
         FROM master_supplier_bank m
         LEFT JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier
         $where"
    )
)['c'] ?? 0);

$order_col = intval($_POST['order'][0]['column'] ?? 0);
$order_dir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$cols      = ['m.id', 'ms.Supplier', 'm.bank_currency', 'm.bank_name',
               'm.bank_account', 'm.beneficiary_name', 'm.status', 'm.created_by'];
$col_name  = $cols[$order_col] ?? 'm.id';

$q = mysqli_query($conn2,
    "SELECT m.*, ms.Supplier AS supplier_name
     FROM master_supplier_bank m
     LEFT JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier
     $where ORDER BY $col_name $order_dir LIMIT $start, $length"
);

$data = [];
$no   = $start + 1;
while ($row = mysqli_fetch_assoc($q)) {
    $row['rownum'] = $no++;
    $data[] = $row;
}

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total,
    'recordsFiltered' => $total,
    'data'            => $data
]);
