<?php
include "../../conn/conn.php";

header('Content-Type: application/json');

$from     = isset($_POST['from'])     ? date("Y-m-d", strtotime($_POST['from']))     : date("Y-m-d");
$to       = isset($_POST['to'])       ? date("Y-m-d", strtotime($_POST['to']))       : date("Y-m-d");
$supplier = isset($_POST['supplier']) ? $_POST['supplier'] : 'ALL';

$where_supplier = '';
if ($supplier !== 'ALL' && $supplier !== '') {
    $supplier_esc   = mysqli_real_escape_string($conn2, $supplier);
    $where_supplier = "AND ms.Supplier = '$supplier_esc'";
}

$sql = "
    SELECT
        a.nm_memo,
        DATE_FORMAT(a.tgl_memo, '%d-%m-%Y') AS tgl_memo,
        a.kepada,
        ms.Supplier   AS supplier,
        a.jns_trans,
        a.jns_pengiriman,
        mb.Supplier   AS buyer,
        a.id_h,
        a.status_transfer
    FROM memo_h a
    INNER JOIN mastersupplier ms ON ms.id_supplier = a.id_supplier
    INNER JOIN mastersupplier mb ON mb.id_supplier = a.id_buyer
    WHERE a.tgl_memo BETWEEN '$from' AND '$to'
      AND a.status != 'CANCEL'
      AND ( a.status_transfer = 'A-TETF')
      $where_supplier
    ORDER BY a.id_h DESC
";

$q    = mysqli_query($conn2, $sql);
$data = [];

if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $data[] = $row;
    }
}

echo json_encode($data);
