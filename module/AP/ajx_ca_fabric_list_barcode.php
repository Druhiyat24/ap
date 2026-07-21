<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

// Server-side DataTable: dataset ini ratusan ribu baris (332rb+ no_barcode),
// jangan pernah diambil semua ke browser - selalu LIMIT/OFFSET + filter tanggal di query.
$draw   = intval($_POST['draw']   ?? 1);
$start  = intval($_POST['start']  ?? 0);
$length = intval($_POST['length'] ?? 10);
$search = trim($_POST['search']['value'] ?? '');

$start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
$end_date   = !empty($_POST['end_date'])   ? $_POST['end_date']   : date('Y-m-d');
$start_date = mysqli_real_escape_string($conn1, $start_date);
$end_date   = mysqli_real_escape_string($conn1, $end_date);

$where = "WHERE a.supplier NOT LIKE '%Production -%'
    AND a.status != 'Cancel'
    AND b.status = 'Y'
    AND a.tgl_dok BETWEEN '$start_date' AND '$end_date'";

if ($search !== '') {
    $s = mysqli_real_escape_string($conn1, $search);
    $where .= " AND (a.no_dok LIKE '%$s%' OR b.no_barcode LIKE '%$s%' OR a.supplier LIKE '%$s%'
        OR a.no_po LIKE '%$s%' OR mi.itemdesc LIKE '%$s%' OR tmpjo.kpno LIKE '%$s%' OR tmpjo.styleno LIKE '%$s%')";
}

$fromJoin = "FROM whs_inmaterial_fabric a
    INNER JOIN whs_lokasi_inmaterial b ON b.no_dok = a.no_dok
    INNER JOIN masteritem mi ON mi.id_item = b.id_item
    LEFT JOIN (
        SELECT id_jo, kpno, styleno
        FROM act_costing ac
        INNER JOIN so ON ac.id = so.id_cost
        INNER JOIN jo_det jod ON so.id = jod.id_so
        GROUP BY id_jo
    ) tmpjo ON tmpjo.id_jo = b.id_jo
    $where
    GROUP BY b.no_barcode";

// Urutan harus sama persis dengan urutan kolom "columns" di ca_fabric_list_barcode.php
// (index 0 = rownum, tidak sortable, jadi tidak masuk array ini).
$cols = ['a.no_dok', 'a.tgl_dok', 'a.supplier', 'no_po', 'b.no_barcode', 'b.id_item',
         'mi.itemdesc', 'b.id_jo', 'tmpjo.kpno', 'tmpjo.styleno', 'b.np_tgl_in', 'b.np_curr', 'b.np_price'];

$order_col = intval($_POST['order'][0]['column'] ?? 1);
$order_dir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$order_by  = $cols[$order_col - 1] ?? 'a.tgl_dok';

$totalRow = mysqli_fetch_assoc(mysqli_query($conn1,
    "SELECT COUNT(*) c FROM (SELECT b.no_barcode $fromJoin) t"
));
$total = (int)($totalRow['c'] ?? 0);

$sql = "SELECT a.no_dok, a.tgl_dok, a.supplier, IFNULL(a.no_po,'-') no_po, b.no_barcode, b.id_jo, b.id_item,
        mi.itemdesc, tmpjo.kpno, tmpjo.styleno, IFNULL(b.np_curr_rev, b.np_curr) np_curr, b.np_tgl_in, IFNULL(b.np_price_rev, b.np_price) np_price
        $fromJoin
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
    $row['np_price']  = number_format((float)$row['np_price'], 4);
    $data[] = $row;
}

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => $total,
    'recordsFiltered' => $total,
    'data'            => $data
]);
