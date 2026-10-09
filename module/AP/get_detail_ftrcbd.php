<?php
/* ============================================================================
   Rincian satu FTR CBD untuk modal di halaman persetujuan.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).
   ============================================================================ */
include '../../conn/conn.php';
header('Content-Type: application/json');

$no = isset($_GET['no_ftr']) ? $_GET['no_ftr'] : '';
$e  = mysqli_real_escape_string($conn2, $no);

$hasil = array('header' => null, 'items' => array());

$h = mysqli_query($conn2, "select no_ftr_cbd no_ftr, tgl_ftr_cbd tgl_ftr, tgl_bayar, supp,
        MAX(curr) curr, status, create_user, create_date,
        ROUND(SUM(subtotal),2) subtotal, ROUND(SUM(tax),2) tax, ROUND(SUM(total),2) total
    from ftr_cbd where no_ftr_cbd = '$e' group by no_ftr_cbd");
if ($h && ($x = mysqli_fetch_assoc($h))) {
    $x['tgl_ftr']     = !empty($x['tgl_ftr'])     ? date('d-M-Y', strtotime($x['tgl_ftr']))     : '-';
    $x['tgl_bayar']   = !empty($x['tgl_bayar'])   ? date('d-M-Y', strtotime($x['tgl_bayar']))   : '-';
    $x['create_date'] = !empty($x['create_date']) ? date('d-M-Y H:i:s', strtotime($x['create_date'])) : '-';
    $x['subtotal'] = number_format((float) $x['subtotal'], 2);
    $x['tax']      = number_format((float) $x['tax'], 2);
    $x['total']    = number_format((float) $x['total'], 2);
    $hasil['header'] = $x;
}

$r = mysqli_query($conn2, "select no_po, tgl_po, no_pi, curr,
        ROUND(SUM(subtotal),2) subtotal, ROUND(SUM(tax),2) tax, ROUND(SUM(total),2) total
    from ftr_cbd where no_ftr_cbd = '$e' group by no_po, tgl_po, no_pi, curr order by no_po");
while ($x = mysqli_fetch_assoc($r)) {
    $x['tgl_po']   = !empty($x['tgl_po']) && $x['tgl_po'] !== '0000-00-00' ? date('d-M-Y', strtotime($x['tgl_po'])) : '-';
    $x['subtotal'] = number_format((float) $x['subtotal'], 2);
    $x['tax']      = number_format((float) $x['tax'], 2);
    $x['total']    = number_format((float) $x['total'], 2);
    $hasil['items'][] = $x;
}

echo json_encode($hasil, JSON_INVALID_UTF8_SUBSTITUTE);
?>