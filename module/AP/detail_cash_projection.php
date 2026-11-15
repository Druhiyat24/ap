<?php
/* Rincian satu dokumen proyeksi - isi modal di cash-projection.php.
   Mengembalikan potongan HTML, bukan JSON, supaya layar tinggal menempel.
   Kolomnya mengikuti jenis dokumennya: Book Transfer beda dgn Other Inflow. */

include '../../conn/conn.php';

/* $_REQUEST, bukan $_GET: jQuery .load() dgn objek data mengirimnya
   sebagai POST. */
$no_doc = isset($_REQUEST['no_doc']) ? trim($_REQUEST['no_doc']) : '';
$doc    = mysqli_real_escape_string($conn1, $no_doc);

function dcp($v) {
    $v = trim((string) $v);
    return ($v === '' || $v === '0000-00-00') ? '-' : htmlspecialchars($v, ENT_QUOTES);
}
function dcp_tgl($t) {
    return ($t == '' || $t == '0000-00-00') ? '-' : date('d M Y', strtotime($t));
}
function dcp_waktu($t) {
    return ($t == '' || $t == '0000-00-00 00:00:00') ? '-' : date('d M Y H:i', strtotime($t));
}
function dcp_angka($n) { return number_format((float) $n, 2, ',', '.'); }

$baris = array();
$q = mysqli_query($conn1, "select * from ap_cf_projection where no_doc = '$doc' order by id");
while ($r = mysqli_fetch_assoc($q)) { $baris[] = $r; }

if (count($baris) == 0) {
    echo '<div class="alert alert-warning mb-0">Document not found.</div>';
    exit;
}

$h     = $baris[0];
$jenis = $h['jenis'];
$batal = (strcasecmp($h['status'], 'Cancel') === 0);
?>

<div class="cfp-detail-head">
  <div class="cfp-doc">
    <span>Document No</span>
    <b><?php echo dcp($h['no_doc']); ?></b>
  </div>
  <div>
    <span>Type</span>
    <b><?php echo dcp($jenis); ?></b>
  </div>
  <div>
    <span>Status</span>
    <b><span class="ftl-st <?php echo $batal ? 'is-cancel' : 'is-approved'; ?>"><?php echo dcp($h['status']); ?></span></b>
  </div>
  <div>
    <span>Created</span>
    <b><?php echo dcp($h['created_by']); ?></b>
    <i><?php echo dcp_waktu($h['created_date']); ?></i>
  </div>
  <div>
    <span>Updated</span>
    <b><?php echo dcp($h['update_by']); ?></b>
    <i><?php echo dcp_waktu($h['update_date']); ?></i>
  </div>
  <div>
    <span>Approved</span>
    <b><?php echo dcp($h['approve_by']); ?></b>
    <i><?php echo dcp_waktu($h['approve_date']); ?></i>
  </div>
  <div>
    <span>Cancelled</span>
    <b><?php echo dcp($h['cancel_by']); ?></b>
    <i><?php echo dcp_waktu($h['cancel_date']); ?></i>
  </div>
</div>

<div class="cfp-detail-tbl">
<table class="table cfp-grid mb-0">
  <thead>
    <tr>
      <th>#</th>
      <?php if ($jenis == 'BOOK TRANSFER') { ?>
        <th>Source</th><th>Destination</th>
      <?php } else { ?>
        <th><?php echo ($jenis == 'PV EXPENSE') ? 'Supplier Name' : 'Customer Name'; ?></th>
        <th><?php echo ($jenis == 'PV EXPENSE') ? 'Cost Category' : 'Income Category'; ?></th>
        <th>Bank</th>
      <?php } ?>
      <th><?php echo ($jenis == 'PV EXPENSE') ? 'Due Date' : 'Date'; ?></th><th>Curr</th>
      <th class="num">Amount</th><th class="num">Rate</th>
      <th class="num">Equivalent IDR</th>
      <?php /* Equivalent USD hanya berarti di Book Transfer - di dua jenis
               lain rekeningnya cuma satu, jadi nilainya sama dgn Amount. */
            if ($jenis == 'BOOK TRANSFER') { ?><th class="num">Equivalent USD</th><?php } ?>
      <th>Description</th>
      <?php if ($jenis == 'BOOK TRANSFER') { ?>
        <th>PC Source</th><th>PC Dest</th><th>Intra / Inter PC</th>
      <?php } else { ?>
        <th>PC</th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
  <?php
  $no = 1; $total_idr = 0; $total_usd = 0;
  foreach ($baris as $b) {
      $total_idr += (float) $b['amount_idr'];
      $total_usd += (float) $b['amount_usd'];

      echo '<tr><td>' . $no++ . '</td>';
      if ($jenis == 'BOOK TRANSFER') {
          echo '<td>' . dcp($b['akun_source']) . '</td><td>' . dcp($b['akun_tujuan']) . '</td>';
      } else {
          echo '<td>' . dcp($b['nama']) . '</td><td>' . dcp($b['kategori']) . '</td>'
             . '<td>' . dcp($b['akun_tujuan']) . '</td>';
      }
      echo '<td>' . dcp_tgl($b['tanggal']) . '</td>
            <td>' . dcp($b['curr']) . '</td>
            <td class="num">' . dcp_angka($b['amount']) . '</td>
            <td class="num">' . dcp_angka($b['rate']) . '</td>
            <td class="num">' . dcp_angka($b['amount_idr']) . '</td>';
      if ($jenis == 'BOOK TRANSFER') { echo '<td class="num">' . dcp_angka($b['amount_usd']) . '</td>'; }
      echo '<td>' . dcp($b['deskripsi']) . '</td>';

      if ($jenis == 'BOOK TRANSFER') {
          echo '<td>' . dcp($b['pc_source']) . '</td><td>' . dcp($b['pc_tujuan']) . '</td>'
             . '<td>' . dcp($b['intra_inter']) . '</td>';
      } else {
          echo '<td>' . dcp($b['pc_tujuan']) . '</td>';
      }
      echo '</tr>';
  }

  $kolom_kiri  = ($jenis == 'BOOK TRANSFER') ? 7 : 8;
  $kolom_kanan = ($jenis == 'BOOK TRANSFER') ? 4 : 2;
  ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="<?php echo $kolom_kiri; ?>">TOTAL <?php echo count($baris); ?> row(s)</td>
      <td class="num"><?php echo dcp_angka($total_idr); ?></td>
      <?php if ($jenis == 'BOOK TRANSFER') { ?>
        <td class="num"><?php echo dcp_angka($total_usd); ?></td>
      <?php } ?>
      <td colspan="<?php echo $kolom_kanan; ?>"></td>
    </tr>
  </tfoot>
</table>
</div>
