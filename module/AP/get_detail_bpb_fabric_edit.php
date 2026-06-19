<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$no_bpb = isset($_GET['no_bpb']) ? $_GET['no_bpb'] : '';
$no_bpb_esc = mysqli_real_escape_string($conn1, $no_bpb);

$result = ['ppn' => 0, 'items' => []];

$headerCheck = mysqli_query($conn1, "SELECT 1 FROM whs_inmaterial_fabric WHERE no_dok = '$no_bpb_esc' LIMIT 1");
$isPenerimaan = $headerCheck && mysqli_num_rows($headerCheck) > 0;

if ($isPenerimaan) {
    $sql = mysqli_query($conn1, "SELECT b.id_jo, tmpjo.kpno no_ws, c.id_item, c.itemdesc desc_item,
            SUM(b.qty_good) qty, b.unit, IFNULL(b.price_update, b.price) price, b.curr, IFNULL(b.ppn,d.tax) ppn,
            d.tax po_ppn, po_match.po_price, po_match.po_curr
        FROM whs_inmaterial_fabric a
        INNER JOIN whs_inmaterial_fabric_det b ON b.no_dok = a.no_dok
        INNER JOIN masteritem c ON c.id_item = b.id_item
        LEFT JOIN po_header d ON IFNULL(d.pono,'') = IFNULL(a.no_po,'')
        LEFT JOIN (
            SELECT po.pono, pi.id_jo, pm.id_item, pi.curr po_curr, pi.price po_price
            FROM po_header po
            INNER JOIN po_item pi ON pi.id_po = po.id
            INNER JOIN masteritem pm ON pm.id_gen = pi.id_gen
            WHERE pi.cancel = 'N'
        ) po_match ON IFNULL(po_match.pono,'') = IFNULL(a.no_po,'') AND IFNULL(po_match.id_jo,0) = IFNULL(b.id_jo,0) AND IFNULL(po_match.id_item,0) = IFNULL(c.id_item,0)
        LEFT JOIN (SELECT id_jo, kpno, styleno FROM act_costing ac INNER JOIN so ON ac.id = so.id_cost INNER JOIN jo_det jod ON so.id = jod.id_so GROUP BY id_jo) tmpjo ON tmpjo.id_jo = b.id_jo
        WHERE a.no_dok = '$no_bpb_esc'
        GROUP BY b.id_jo, b.id_item");
} else {
    $sql = mysqli_query($conn1, "SELECT r.id_jo, tmpjo.kpno no_ws, c.id_item, c.itemdesc desc_item,
            SUM(r.qty_out) qty, r.satuan unit, r.price, r.curr, IFNULL(r.ppn,d.tax) ppn,
            d.tax po_ppn, po_match.po_price, po_match.po_curr
        FROM whs_bppb_h h
        INNER JOIN whs_bppb_ro r ON r.no_bppb = h.no_bppb
        INNER JOIN masteritem c ON c.id_item = r.id_item
        LEFT JOIN po_header d ON IFNULL(d.pono,'') = IFNULL(r.no_po,'')
        LEFT JOIN (
            SELECT po.pono, pi.id_jo, pm.id_item, pi.curr po_curr, pi.price po_price
            FROM po_header po
            INNER JOIN po_item pi ON pi.id_po = po.id
            INNER JOIN masteritem pm ON pm.id_gen = pi.id_gen
            WHERE pi.cancel = 'N'
        ) po_match ON IFNULL(po_match.pono,'') = IFNULL(r.no_po,'') AND IFNULL(po_match.id_jo,0) = IFNULL(r.id_jo,0) AND IFNULL(po_match.id_item,0) = IFNULL(c.id_item,0)
        LEFT JOIN (SELECT id_jo, kpno, styleno FROM act_costing ac INNER JOIN so ON ac.id = so.id_cost INNER JOIN jo_det jod ON so.id = jod.id_so GROUP BY id_jo) tmpjo ON tmpjo.id_jo = r.id_jo
        WHERE h.no_bppb = '$no_bpb_esc'
        GROUP BY r.id_jo, r.id_item");
}

$first = true;
while ($row = mysqli_fetch_assoc($sql)) {
    if ($first) {
        $result['ppn'] = $row['ppn'] !== null ? (float) $row['ppn'] : 0;
        $first = false;
    }
    $row['id'] = $row['id_jo'] . '-' . $row['id_item'];
    $row['qty'] = round((float) $row['qty'], 2);

    $priceMatches = $row['po_price'] !== null && abs((float) $row['price'] - (float) $row['po_price']) < 0.0001;
    $ppnMatches = $row['po_ppn'] !== null && abs((float) $row['ppn'] - (float) $row['po_ppn']) < 0.0001;
    $row['is_locked'] = $priceMatches && $ppnMatches;

    $result['items'][] = $row;
}

echo json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE);
?>
