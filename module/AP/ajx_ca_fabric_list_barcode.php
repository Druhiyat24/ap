<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

// Server-side DataTable: dataset ini ratusan ribu baris (332rb+ no_barcode),
// jangan pernah diambil semua ke browser - selalu LIMIT/OFFSET + filter tanggal di query.
// Sumber data sekarang view vw_whs_master_barcode (gantiin join manual whs_inmaterial_fabric +
// whs_lokasi_inmaterial + masteritem + act_costing/so/jo_det) - view-nya sudah nanganin
// filter status/supplier di dalam definisinya sendiri.
$draw   = intval($_POST['draw']   ?? 1);
$start  = intval($_POST['start']  ?? 0);
$length = intval($_POST['length'] ?? 10);
$search = trim($_POST['search']['value'] ?? '');

$start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
$end_date   = !empty($_POST['end_date'])   ? $_POST['end_date']   : date('Y-m-d');
$start_date = mysqli_real_escape_string($conn1, $start_date);
$end_date   = mysqli_real_escape_string($conn1, $end_date);

$where = "WHERE tgl_dok BETWEEN '$start_date' AND '$end_date'";

if ($search !== '') {
    $s = mysqli_real_escape_string($conn1, $search);
    $where .= " AND (no_dok LIKE '%$s%' OR no_barcode LIKE '%$s%' OR supplier LIKE '%$s%'
        OR no_po LIKE '%$s%' OR itemdesc LIKE '%$s%' OR kpno LIKE '%$s%' OR styleno LIKE '%$s%')";
}

$fromWhere = "FROM vw_whs_master_barcode $where";

// Urutan harus sama persis dengan urutan kolom "columns" di ca_fabric_list_barcode.php
// (index 0 = rownum, tidak sortable, jadi tidak masuk array ini).
$cols = ['no_dok', 'tgl_dok', 'supplier', 'no_po', 'no_barcode', 'id_item',
         'itemdesc', 'id_jo', 'kpno', 'styleno', 'np_tgl_in', 'np_curr', 'np_price', 'rate', 'np_price_idr'];

$order_col = intval($_POST['order'][0]['column'] ?? 1);
$order_dir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$order_by  = $cols[$order_col - 1] ?? 'tgl_dok';

$totalRow = mysqli_fetch_assoc(mysqli_query($conn1, "SELECT COUNT(*) c $fromWhere"));
$total = (int)($totalRow['c'] ?? 0);

$sql = "SELECT no_dok, tgl_dok, supplier, IFNULL(no_po,'-') no_po, no_barcode, id_jo, id_item,
        itemdesc, kpno, styleno, np_curr, np_tgl_in, np_price, rate, np_price_idr
        $fromWhere
        ORDER BY $order_by $order_dir
        LIMIT $start, $length";

$q = mysqli_query($conn1, $sql);
if (!$q) {
    echo json_encode(['draw' => $draw, 'error' => mysqli_error($conn1), 'data' => []]);
    exit;
}

$data = [];
$no = $start + 1;
while ($row = mysqli_fetch_assoc($q)) {
    $row['rownum']    = $no++;
    $row['tgl_dok']   = !empty($row['tgl_dok']) ? date('d-M-Y', strtotime($row['tgl_dok'])) : '-';
    $row['np_tgl_in'] = !empty($row['np_tgl_in']) ? date('d-M-Y', strtotime($row['np_tgl_in'])) : '-';
    $row['np_price']     = number_format((float)$row['np_price'], 4);
    $row['rate']         = number_format((float)$row['rate'], 2);
    $row['np_price_idr'] = number_format((float)$row['np_price_idr'], 4);
    $data[] = $row;
}

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total,
    'recordsFiltered' => $total,
    'data'            => $data
]);
