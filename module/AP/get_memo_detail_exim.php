<?php
// Refresh tabel detail memo + field Supplier/Charge to Buyer/Total Memo
// setelah pilih memo lewat modal "Select No Memo", tanpa reload halaman.
// Dipanggil oleh create-paymentvoucher-exim.php (handler #savememo).
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';

ob_start();
include 'inc_memo_detail_rows_exim.php';
$rows_html = ob_get_clean();

$sql = mysqli_query($conn2,"select DISTINCT ms.supplier supplier from memo_h a
    inner join mastersupplier ms on a.id_supplier = ms.id_supplier
    inner join mastersupplier mb on a.id_buyer = mb.id_supplier
    inner join memo_det mdet on mdet.id_h = a.id_h
    inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
    where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and mtemp.user = '$user' GROUP BY nm_memo order by a.id_h desc limit 1");
$row = mysqli_fetch_array($sql);
$nama_supp = isset($row['supplier']) ? $row['supplier'] : '';

$sql = mysqli_query($conn2,"select DISTINCT mb.supplier buyer from memo_h a
    inner join mastersupplier ms on a.id_supplier = ms.id_supplier
    inner join mastersupplier mb on a.id_buyer = mb.id_supplier
    inner join memo_det mdet on mdet.id_h = a.id_h
    inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
    where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and a.ditagihkan = 'Y' and mtemp.user = '$user' GROUP BY nm_memo order by a.id_h desc limit 1");
$row = mysqli_fetch_array($sql);
$ct_buyer = isset($row['buyer']) ? $row['buyer'] : '-';

$sql = mysqli_query($conn2,"select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user'");
$row = mysqli_fetch_array($sql);
$biaya = isset($row['biaya']) ? $row['biaya'] : 0;

header('Content-Type: application/json');
echo json_encode([
    'rows_html' => $rows_html,
    'nama_supp' => $nama_supp,
    'ct_buyer' => $ct_buyer,
    'biaya' => $biaya,
    'biaya_formatted' => number_format((float)$biaya, 2),
]);
