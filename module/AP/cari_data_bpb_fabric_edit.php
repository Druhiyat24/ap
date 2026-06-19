<?php
include '../../conn/conn.php';
header('Content-Type: application/json');

$nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : 'ALL';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

$whereIn = "a.status != 'Cancel' AND a.no_po IS NOT NULL AND a.no_po != ''";
$whereOut = "h.status != 'Cancel' AND r.price IS NOT NULL AND r.price > 0";

if ($nama_supp !== 'ALL' && $nama_supp !== '') {
    $nama_supp_esc = mysqli_real_escape_string($conn1, $nama_supp);
    $whereIn .= " AND a.supplier = '$nama_supp_esc'";
    $whereOut .= " AND h.tujuan = '$nama_supp_esc'";
}

if (!empty($start_date) && !empty($end_date)) {
    $start_date_esc = mysqli_real_escape_string($conn1, $start_date);
    $end_date_esc = mysqli_real_escape_string($conn1, $end_date);
    $whereIn .= " AND a.tgl_dok BETWEEN '$start_date_esc' AND '$end_date_esc'";
    $whereOut .= " AND h.tgl_bppb BETWEEN '$start_date_esc' AND '$end_date_esc'";
}

$sql = mysqli_query($conn1, "SELECT t.no_dok, t.tgl_dok, t.supplier, t.no_po, MAX(t.curr) curr,
        ROUND(SUM(t.qty_good),2) qty,
        ROUND(SUM(t.qty_good * t.price),2) dpp,
        MAX(t.ppn_rate) ppn_rate,
        MIN(t.item_match) is_match
    FROM (
        SELECT a.no_dok, a.tgl_dok, a.supplier, IFNULL(a.no_po,'-') no_po,
            b.qty_good, IFNULL(b.price_update, b.price) price, b.curr,
            IFNULL(b.ppn, d.tax) ppn_rate,
            CASE
                WHEN pi.price IS NULL OR d.tax IS NULL THEN 0
                WHEN ABS(IFNULL(b.price_update, b.price) - pi.price) < 0.0001 AND ABS(IFNULL(b.ppn,d.tax) - d.tax) < 0.0001 THEN 1
                ELSE 0
            END item_match
        FROM whs_inmaterial_fabric a
        INNER JOIN whs_inmaterial_fabric_det b ON b.no_dok = a.no_dok
        LEFT JOIN po_header d ON d.pono = a.no_po
        LEFT JOIN masteritem pm ON pm.id_item = b.id_item
        LEFT JOIN po_item pi ON pi.id_po = d.id AND pi.id_jo = b.id_jo AND pi.id_gen = pm.id_gen AND pi.cancel = 'N'
        WHERE $whereIn

        UNION ALL

        SELECT h.no_bppb no_dok, h.tgl_bppb tgl_dok, h.tujuan supplier, IFNULL(r.no_po,'-') no_po,
            r.qty_out qty_good, r.price, r.curr,
            IFNULL(r.ppn, d2.tax) ppn_rate,
            CASE
                WHEN pi2.price IS NULL OR d2.tax IS NULL THEN 0
                WHEN ABS(r.price - pi2.price) < 0.0001 AND ABS(IFNULL(r.ppn,d2.tax) - d2.tax) < 0.0001 THEN 1
                ELSE 0
            END item_match
        FROM whs_bppb_h h
        INNER JOIN whs_bppb_ro r ON r.no_bppb = h.no_bppb
        LEFT JOIN po_header d2 ON d2.pono = r.no_po
        LEFT JOIN masteritem pm2 ON pm2.id_item = r.id_item
        LEFT JOIN po_item pi2 ON pi2.id_po = d2.id AND pi2.id_jo = r.id_jo AND pi2.id_gen = pm2.id_gen AND pi2.cancel = 'N'
        WHERE $whereOut
    ) t
    GROUP BY t.no_dok, t.tgl_dok, t.supplier, t.no_po
    ORDER BY t.tgl_dok DESC, t.no_dok DESC");

$data = [];
while ($row = mysqli_fetch_assoc($sql)) {
    $row['tgl_dok_fmt'] = !empty($row['tgl_dok']) ? date('d-M-Y', strtotime($row['tgl_dok'])) : '-';
    $dpp = (float) $row['dpp'];
    $ppn_rate = (float) $row['ppn_rate'];
    $row['ppn'] = round($dpp * $ppn_rate / 100, 2);
    $row['total'] = round($dpp + $row['ppn'], 2);
    $row['is_match'] = ((int) $row['is_match']) === 1;
    $data[] = $row;
}

echo json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
?>
