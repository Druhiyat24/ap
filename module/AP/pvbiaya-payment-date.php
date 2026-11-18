<?php
/* ============================================================================
   Update Payment Date PV Biaya - DAFTAR dokumennya.
   Pasangan create-pvbiaya-payment-date.php. Bentuknya mengikuti
   list-payment-plan.php milik AP: kartu saringan di atas, kartu tabel di
   bawah, kosakata .ftl- dan .app-btn.

   Satu dokumen = satu kali Save, berapa pun vouchernya. Jumlah voucher &
   nilainya dihitung dari baris yang MASIH BERLAKU (status 'Y') - baris yang
   sudah digantikan dokumen lebih baru tidak ikut, supaya angka di daftar ini
   tidak menggandakan voucher yang rencananya pernah digeser.

   header.php tidak memuat satu pun pustaka JS, jadi halaman ini memuat
   sendiri yang dibutuhkannya.
   ============================================================================ */
include '../header.php';

$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== ''
            ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-01');
$end_date   = isset($_POST['end_date']) && $_POST['end_date'] !== ''
            ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-t');
$f_status   = isset($_POST['status']) ? trim($_POST['status']) : 'ALL';
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link href="../css/4.1.1/datatables.min.css" rel="stylesheet">

<style>
  #mytable td.num { text-align: right; font-variant-numeric: tabular-nums; }
  .st-post   { background: #e8f5ec; color: #1e7a3c; }
  .st-cancel { background: #fdeeec; color: #b91c1c; }
  .ftl-st { display: inline-block; padding: 2px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 700; letter-spacing: .02em; }
  /* Rincian barisnya dibuka di modal, bukan halaman baru - sama dgn AP. */
  #isi-rincian td { font-size: 12.5px; }
</style>

<div class="container-fluid mt-3 p-3">

  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
      <div>
        <h1>List Update Payment Date</h1>
        <span class="ftl-crumb">Bank &rsaquo; Payment Plan &rsaquo; Update Payment Date</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">
      <form method="post" action="pvbiaya-payment-date.php">
        <div class="form-row">
          <div class="col-12 col-sm-6 col-xl-2 mb-2">
            <label class="form-label"><b>From</b></label>
            <input type="text" class="form-control form-control-sm tanggal" name="start_date"
                   value="<?php echo date('d M Y', strtotime($start_date)); ?>" autocomplete="off">
          </div>
          <div class="col-12 col-sm-6 col-xl-2 mb-2">
            <label class="form-label"><b>To</b></label>
            <input type="text" class="form-control form-control-sm tanggal" name="end_date"
                   value="<?php echo date('d M Y', strtotime($end_date)); ?>" autocomplete="off">
          </div>
          <div class="col-12 col-sm-6 col-xl-2 mb-2">
            <label class="form-label"><b>Status</b></label>
            <select class="form-control app-select2" name="status">
              <option value="ALL"    <?php if ($f_status === 'ALL')    echo 'selected'; ?>>All</option>
              <option value="POST"   <?php if ($f_status === 'POST')   echo 'selected'; ?>>Post</option>
              <option value="CANCEL" <?php if ($f_status === 'CANCEL') echo 'selected'; ?>>Cancel</option>
            </select>
          </div>
          <div class="col-12 col-xl-6 mb-2 ftl-actions align-items-end">
            <button type="submit" class="app-btn app-btn-primary app-btn-sm">
              <i class="fa fa-search" aria-hidden="true"></i> Search
            </button>
            <a href="create-pvbiaya-payment-date.php" class="app-btn app-btn-success app-btn-sm">
              <i class="fa fa-plus" aria-hidden="true"></i> Create
            </a>
          </div>
        </div>
      </form>
    </div><!-- /.ftl-panel -->
  </div><!-- /.ftl-card: kartu saringan -->

  <div class="ftl-card mt-3">
    <div class="ftl-body">
      <div class="ftl-tblwrap">
        <table id="mytable" class="table ftl-tbl" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th style="text-align:center;vertical-align:middle;">Document Number</th>
              <th style="text-align:center;vertical-align:middle;">Payment Plan Date</th>
              <th style="text-align:center;vertical-align:middle;">Vouchers</th>
              <th style="text-align:center;vertical-align:middle;">Total Amount</th>
              <th style="text-align:center;vertical-align:middle;">Description</th>
              <th style="text-align:center;vertical-align:middle;">Created By</th>
              <th style="text-align:center;vertical-align:middle;">Created Date</th>
              <th style="text-align:center;vertical-align:middle;">Status</th>
              <th style="text-align:center;vertical-align:middle;">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $w = array("h.plan_date BETWEEN '" . mysqli_real_escape_string($conn2, $start_date) . "'
                      AND '" . mysqli_real_escape_string($conn2, $end_date) . "'");
          if ($f_status !== 'ALL') {
              $w[] = "h.status = '" . mysqli_real_escape_string($conn2, $f_status) . "'";
          }
          /* Jumlah & nilai dihitung dari baris 'Y' saja - lihat catatan di
             kepala berkas. COALESCE supaya dokumen yang seluruh barisnya
             sudah digantikan tetap tampil dgn angka 0, bukan hilang. */
          $sql = "SELECT h.id, h.doc_number, h.plan_date, h.keterangan, h.status,
                         h.created_by, h.created_date, h.cancel_by, h.cancel_date,
                         COALESCE(d.jml, 0) jml, COALESCE(d.total, 0) total
                    FROM ap_pvbiaya_plan_h h
                    LEFT JOIN (SELECT doc_number, COUNT(*) jml, SUM(amount) total
                                 FROM ap_pvbiaya_plan_det WHERE status = 'Y'
                                GROUP BY doc_number) d ON d.doc_number = h.doc_number
                   WHERE " . implode(' AND ', $w) . "
                   ORDER BY h.id DESC";
          $q = mysqli_query($conn2, $sql);
          if ($q) {
              while ($r = mysqli_fetch_assoc($q)) {
                  $batal = (strtoupper($r['status']) === 'CANCEL');
                  echo '<tr>'
                     . '<td class="ftl-doc">' . htmlspecialchars($r['doc_number']) . '</td>'
                     . '<td>' . ($r['plan_date'] ? date('d-M-Y', strtotime($r['plan_date'])) : '-') . '</td>'
                     . '<td style="text-align:center;">' . (int) $r['jml'] . '</td>'
                     . '<td class="num">' . number_format($r['total'], 2) . '</td>'
                     . '<td>' . htmlspecialchars((string) $r['keterangan']) . '</td>'
                     . '<td>' . htmlspecialchars((string) $r['created_by']) . '</td>'
                     . '<td>' . ($r['created_date'] ? date('d-M-Y H:i', strtotime($r['created_date'])) : '-') . '</td>'
                     . '<td style="text-align:center;"><span class="ftl-st ' . ($batal ? 'st-cancel' : 'st-post') . '">'
                     . ($batal ? 'Cancel' : 'Post') . '</span></td>'
                     . '<td style="text-align:center;white-space:nowrap;">'
                     . '<button type="button" class="app-btn app-btn-dark app-btn-sm btn-lihat" data-doc="'
                     . htmlspecialchars($r['doc_number'], ENT_QUOTES) . '" title="Detail"><i class="fa fa-eye"></i></button> '
                     . ($batal ? '' : '<button type="button" class="app-btn app-btn-danger app-btn-sm btn-batal" data-doc="'
                        . htmlspecialchars($r['doc_number'], ENT_QUOTES) . '" title="Cancel"><i class="fa fa-ban"></i></button>')
                     . '</td>'
                     . '</tr>';
              }
          }
          ?>
          </tbody>
        </table>
      </div><!-- /.ftl-tblwrap -->
    </div><!-- /.ftl-body -->
  </div><!-- /.ftl-card: kartu tabel -->
</div><!-- /.container-fluid -->

<!-- Rincian satu dokumen -->
<div class="modal fade" id="modal-rincian" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span id="judul-rincian"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm table-bordered" style="width:100%;">
          <thead class="table-gradient">
            <tr>
              <th style="text-align:center;">Payment Voucher No</th>
              <th style="text-align:center;">PC</th>
              <th style="text-align:center;">Payment Plan</th>
              <th style="text-align:center;">Curr</th>
              <th style="text-align:center;">Amount</th>
              <th style="text-align:center;">Description</th>
              <th style="text-align:center;">Status</th>
            </tr>
          </thead>
          <tbody id="isi-rincian"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="app-btn app-btn-dark app-btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
$(function () {
  $('.app-select2').select2({ width: '100%' });
  $('.tanggal').datepicker({ format: 'dd M yyyy', autoclose: true, todayHighlight: true });
  $('#mytable').DataTable({
    order: [[0, 'desc']],
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
    columnDefs: [{ targets: [8], orderable: false, searchable: false }],
    language: {
      search: 'Search:',
      emptyTable: 'No document in that period.',
      zeroRecords: 'No document matches your search.'
    }
  });
});

$('#mytable').on('click', '.btn-lihat', function () {
  var doc = $(this).data('doc');
  $('#judul-rincian').text(doc);
  $('#isi-rincian').html('<tr><td colspan="7" class="text-center text-muted" style="padding:14px;">Loading...</td></tr>');
  $('#modal-rincian').modal('show');

  $.ajax({ type: 'POST', url: 'ajx_detail-pvbiaya-payment-date.php', dataType: 'json', data: { doc_number: doc } })
   .done(function (r) {
      if (!r || r.status !== 'success') {
        $('#isi-rincian').html('<tr><td colspan="7" class="text-center" style="color:#b91c1c;padding:14px;">'
          + ((r && r.message) ? r.message : 'Could not read the detail.') + '</td></tr>');
        return;
      }
      if (!r.data.length) {
        $('#isi-rincian').html('<tr><td colspan="7" class="text-center text-muted" style="padding:14px;">No line in this document.</td></tr>');
        return;
      }
      var h = '';
      $.each(r.data, function (i, d) {
        h += '<tr>'
           + '<td>' + d.no_pv + '</td>'
           + '<td style="text-align:center;">' + d.profit_center + '</td>'
           + '<td style="text-align:center;">' + d.plan_date + '</td>'
           + '<td style="text-align:center;">' + d.curr + '</td>'
           + '<td style="text-align:right;">' + d.amount + '</td>'
           + '<td>' + d.keterangan + '</td>'
           + '<td style="text-align:center;">' + (d.status === 'Y'
               ? '<span class="ftl-st st-post">Active</span>'
               : '<span class="ftl-st st-cancel">Replaced</span>') + '</td>'
           + '</tr>';
      });
      $('#isi-rincian').html(h);
   })
   .fail(function (xhr) {
      $('#isi-rincian').html('<tr><td colspan="7" class="text-center" style="color:#b91c1c;padding:14px;">HTTP '
        + (xhr && xhr.status ? xhr.status : '?') + '</td></tr>');
   });
});

$('#mytable').on('click', '.btn-batal', function () {
  var doc = $(this).data('doc');
  Swal.fire({
    icon: 'warning', title: 'Are you sure?',
    text: 'Cancel ' + doc + '? Every voucher in it goes back to having no payment date.',
    showCancelButton: true, confirmButtonText: 'Yes, cancel it!', cancelButtonText: 'No'
  }).then(function (h) {
    if (!h.isConfirmed) { return; }
    Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
    $.ajax({ type: 'POST', url: 'cancel_pvbiaya_payment_date.php', dataType: 'json', data: { doc_number: doc } })
     .done(function (r) {
        if (!r || r.status !== 'success') {
          Swal.fire({ icon: 'error', title: 'Failed', text: (r && r.message) ? r.message : 'Could not cancel.' });
          return;
        }
        Swal.fire({ icon: 'success', title: 'Cancelled', text: doc + ' has been cancelled.' })
            .then(function () { window.location.reload(); });
     })
     .fail(function (xhr) {
        Swal.fire({ icon: 'error', title: 'Failed',
          text: 'HTTP ' + (xhr && xhr.status ? xhr.status : '?') });
     });
  });
});
</script>

</body>
</html>
