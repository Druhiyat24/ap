<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
/* ============================================================================
   Update BPB - GENERAL : rincian satu dokumen BPB (modal di form Create).
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen General ada LANGSUNG di tabel `bpb`; GEN/IN maupun GEN/RI
   diperlakukan sama (keduanya penerimaan secara jurnal).

   AWAS - sambungan ke PO memakai  pi.id_gen = bpb.id_item , BUKAN lewat
   masteritem.id_gen seperti Fabric/Accessories. masteritem.id_gen kosong
   untuk semua item GEN; po_item.id_gen berisi id_item-nya langsung.
   Kolom is_locked (harga & PPN sudah sama dgn PO) bergantung pada ini.
   ============================================================================ */

$no_bpb = isset($_GET['no_bpb']) ? $_GET['no_bpb'] : '';
$no_bpb_esc = mysqli_real_escape_string($conn1, $no_bpb);

$result = ['ppn' => 0, 'items' => []];

$sql = mysqli_query($conn1, "SELECT a.id_jo, tmpjo.kpno no_ws, c.id_item, c.itemdesc desc_item,
        SUM(a.qty - IFNULL(a.qty_reject,0)) qty, a.unit, a.price, a.curr, IFNULL(a.ppn,d.tax) ppn,
        d.tax po_ppn, po_match.po_price, po_match.po_curr
    FROM bpb a
    INNER JOIN masteritem c ON c.id_item = a.id_item
    LEFT JOIN po_header d ON IFNULL(d.pono,'') = IFNULL(a.pono,'')
    LEFT JOIN (
        SELECT po.pono, pi.id_jo, pi.id_gen id_item, pi.curr po_curr, pi.price po_price
        FROM po_header po
        INNER JOIN po_item pi ON pi.id_po = po.id
        WHERE pi.cancel = 'N'
    ) po_match ON IFNULL(po_match.pono,'') = IFNULL(a.pono,'') AND IFNULL(po_match.id_jo,0) = IFNULL(a.id_jo,0) AND IFNULL(po_match.id_item,0) = IFNULL(a.id_item,0)
    LEFT JOIN (SELECT id_jo, kpno, styleno FROM act_costing ac INNER JOIN so ON ac.id = so.id_cost INNER JOIN jo_det jod ON so.id = jod.id_so GROUP BY id_jo) tmpjo ON tmpjo.id_jo = a.id_jo
    WHERE a.bpbno_int = '$no_bpb_esc' AND IFNULL(a.cancel,'N') <> 'Y'
    GROUP BY a.id_jo, a.id_item");

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
