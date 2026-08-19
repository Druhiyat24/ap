<?php
include "../../conn/conn.php";
session_start();
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ===== GET ONE (for Edit) =====
if ($action === 'get_one') {
    $id   = intval($_POST['id'] ?? 0);
    $stmt = mysqli_prepare($conn2, "SELECT * FROM master_pilihan_bank WHERE id = ?");
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

$filter_kategori = $_POST['filter_kategori'] ?? '';
$filter_status   = $_POST['filter_status']   ?? '';

$where = "WHERE 1=1";
if ($filter_kategori !== '') {
    $fk = mysqli_real_escape_string($conn2, $filter_kategori);
    $where .= " AND katergori_bank = '$fk'";
}
if ($filter_status !== '') {
    $fs = mysqli_real_escape_string($conn2, $filter_status);
    $where .= " AND status = '$fs'";
}
if ($search !== '') {
    $s = mysqli_real_escape_string($conn2, $search);
    $where .= " AND (nama_bank LIKE '%$s%' OR kode_bank LIKE '%$s%'
                  OR swift_code LIKE '%$s%' OR katergori_bank LIKE '%$s%')";
}

$total = (int)(mysqli_fetch_assoc(
    mysqli_query($conn2, "SELECT COUNT(*) AS c FROM master_pilihan_bank $where")
)['c'] ?? 0);

$order_col = intval($_POST['order'][0]['column'] ?? 0);
$order_dir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$cols      = ['id', 'katergori_bank', 'nama_bank', 'kode_bank', 'swift_code', 'status'];
$col_name  = $cols[$order_col] ?? 'nama_bank';

$q = mysqli_query($conn2,
    "SELECT * FROM master_pilihan_bank $where ORDER BY $col_name $order_dir LIMIT $start, $length"
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
