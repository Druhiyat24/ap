<?php
/* Ekspor Excel daftar Cash > Projection > Other.
   Saringannya sama dgn layar: jenis + rentang tanggal. */

include '../../conn/conn.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=cash-projection.xls");

$jenis      = isset($_GET['jenis']) && $_GET['jenis'] != '' ? $_GET['jenis'] : 'ALL';
$start_date = !empty($_GET['start_date']) ? date('Y-m-d', strtotime($_GET['start_date'])) : date('Y-m-01');
$end_date   = !empty($_GET['end_date'])   ? date('Y-m-d', strtotime($_GET['end_date']))   : date('Y-m-t');

/* Dokumen yang dipakai dipilih sama persis dgn layar daftarnya: yang
   RENTANG TANGGALNYA bersinggungan dgn saringan, lalu SELURUH barisnya
   diekspor. Kalau tanggalnya disaring per baris, isi Excel bisa beda dgn
   total yang terlihat di daftar. */
$tgl1 = mysqli_real_escape_string($conn1, $start_date);
$tgl2 = mysqli_real_escape_string($conn1, $end_date);

$saring_jenis = '';
if ($jenis != 'ALL') {
    $saring_jenis = "where jenis = '" . mysqli_real_escape_string($conn1, $jenis) . "'";
}

$where = "where no_doc in (select no_doc from ap_cf_projection $saring_jenis
                           group by no_doc
                           having min(tanggal) <= '$tgl2' and max(tanggal) >= '$tgl1')";

function ek($v) { return htmlspecialchars((string) $v, ENT_QUOTES); }
function ek_tgl($t)   { return ($t == '' || $t == '0000-00-00') ? '' : date('d M Y', strtotime($t)); }
function ek_waktu($t) { return ($t == '' || $t == '0000-00-00 00:00:00') ? '' : date('d M Y H:i', strtotime($t)); }
?>
<html>
<head><meta charset="utf-8"><title>Cash Flow Projection</title></head>
<body>
<center>
  <h4>CASH FLOW PROJECTION - OTHER</h4>
  <p><?php echo ek($jenis) . ' &nbsp;|&nbsp; ' . ek_tgl($start_date) . ' s/d ' . ek_tgl($end_date); ?></p>
</center>

<table border="1" style="width:100%;font-size:10px;">
  <tr>
    <th>No</th><th>No Doc</th><th>Type</th><th>Date</th>
    <th>Source</th><th>Destination / Bank</th><th>Customer / Supplier</th><th>Supplier / Customer ID</th><th>Category</th><th>Description</th>
    <th>Curr</th><th>Amount</th><th>Rate</th><th>Equivalent IDR</th><th>Equivalent USD</th>
    <th>PC Source</th><th>PC Destination</th><th>Intra / Inter PC</th>
    <th>Status</th><th>Created By</th><th>Created Date</th><th>Update By</th><th>Update Date</th>
    <th>Approve By</th><th>Approve Date</th><th>Cancel By</th><th>Cancel Date</th>
  </tr>
  <?php
  $no  = 1;
  $sql = mysqli_query($conn1, "select * from ap_cf_projection $where order by jenis asc, no_doc asc");
  while ($row = mysqli_fetch_assoc($sql)) {
      echo '<tr>
        <td>' . $no++ . '</td>
        <td>' . ek($row['no_doc']) . '</td>
        <td>' . ek($row['jenis']) . '</td>
        <td>' . ek_tgl($row['tanggal']) . '</td>
        <td>' . ek($row['akun_source']) . '</td>
        <td>' . ek($row['akun_tujuan']) . '</td>
        <td>' . ek($row['nama_supplier']) . '</td>
        <td>' . ek($row['id_supplier']) . '</td>
        <td>' . ek($row['kategori']) . '</td>
        <td>' . ek($row['deskripsi']) . '</td>
        <td>' . ek($row['curr']) . '</td>
        <td>' . ek($row['amount']) . '</td>
        <td>' . ek($row['rate']) . '</td>
        <td>' . ek($row['amount_idr']) . '</td>
        <td>' . ek($row['amount_usd']) . '</td>
        <td>' . ek($row['pc_source']) . '</td>
        <td>' . ek($row['pc_tujuan']) . '</td>
        <td>' . ek($row['intra_inter']) . '</td>
        <td>' . ek($row['status']) . '</td>
        <td>' . ek($row['created_by']) . '</td>
        <td>' . ek_waktu($row['created_date']) . '</td>
        <td>' . ek($row['update_by']) . '</td>
        <td>' . ek_waktu($row['update_date']) . '</td>
        <td>' . ek($row['approve_by']) . '</td>
        <td>' . ek_waktu($row['approve_date']) . '</td>
        <td>' . ek($row['cancel_by']) . '</td>
        <td>' . ek_waktu($row['cancel_date']) . '</td>
      </tr>';
  }
  ?>
</table>
</body>
</html>
