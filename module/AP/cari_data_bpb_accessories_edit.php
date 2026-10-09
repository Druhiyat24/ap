<?php
include '../../conn/conn.php';
header('Content-Type: application/json');
/* ============================================================================
   Update BPB - ACCESSORIES : daftar dokumen BPB utk dipilih di form Create.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumen aksesoris ada LANGSUNG di tabel `bpb` (tidak punya tabel kepala
   seperti whs_inmaterial_fabric pada Fabric). GACC/IN maupun GACC/RI ikut
   semua - diperiksa ke produksi 8 Okt 2026, jurnal GACC/RI SEARAH dgn
   penerimaan, jadi bukan retur akuntansi seperti GK/RO di Fabric.

   Sambungan ke PO lewat masteritem.id_gen (bukan id_item langsung seperti
   di General): utk item GACC kolom itu TERISI - diperiksa 8 Okt 2026, 9.446
   dari 9.446 baris dapat harga PO lewat jalur ini, sedangkan lewat id_item
   hasilnya 0.
   ============================================================================ */

$nama_supp  = isset($_POST['nama_supp'])  ? $_POST['nama_supp']  : 'ALL';
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date   = isset($_POST['end_date'])   ? $_POST['end_date']   : '';

$where = "a.bpbno_int LIKE 'GACC/%' AND IFNULL(a.cancel,'N') <> 'Y'"
       . " AND a.pono IS NOT NULL AND a.pono != '' AND a.price > 0";

if ($nama_supp !== 'ALL' && $nama_supp !== '') {
    $nama_supp_esc = mysqli_real_escape_string($conn1, $nama_supp);
    $where .= " AND ms.Supplier = '$nama_supp_esc'";
}

if (!empty($start_date) && !empty($end_date)) {
    $start_date_esc = mysqli_real_escape_string($conn1, $start_date);
    $end_date_esc   = mysqli_real_escape_string($conn1, $end_date);
    $where .= " AND a.bpbdate BETWEEN '$start_date_esc' AND '$end_date_esc'";
}

$sql = mysqli_query($conn1, "SELECT t.no_dok, t.tgl_dok, t.supplier, t.no_po, MAX(t.curr) curr,
        ROUND(SUM(t.qty_good),2) qty,
        ROUND(SUM(t.qty_good * t.price),2) dpp,
        MAX(t.ppn_rate) ppn_rate,
        MIN(t.item_match) is_match
    FROM (
        SELECT a.bpbno_int no_dok, a.bpbdate tgl_dok, ms.Supplier supplier, IFNULL(a.pono,'-') no_po,
            (a.qty - IFNULL(a.qty_reject,0)) qty_good, a.price, a.curr,
            IFNULL(a.ppn, d.tax) ppn_rate,
            CASE
                WHEN pi.price IS NULL OR d.tax IS NULL THEN 0
                WHEN ABS(a.price - pi.price) < 0.0001 AND ABS(IFNULL(a.ppn,d.tax) - d.tax) < 0.0001 THEN 1
                ELSE 0
            END item_match
        FROM bpb a
        INNER JOIN mastersupplier ms ON ms.Id_Supplier = a.id_supplier
        LEFT JOIN po_header d ON d.pono = a.pono
        LEFT JOIN masteritem pm ON pm.id_item = a.id_item
        LEFT JOIN po_item pi ON pi.id_po = d.id AND pi.id_jo = a.id_jo AND pi.id_gen = pm.id_gen AND pi.cancel = 'N'
        WHERE $where
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
