<?php include '../header.php'; ?>
<?php
// ============================================================================
// STATUS INFORMATION - penelusuran satu dokumen dari terima barang sampai lunas.
//
// Cakupan dokumen: BPB (.../IN/... & .../RI/...) DAN BPPB (.../OUT/... & /RO/...).
// Query-nya dibangun di status_query.php, dipakai bareng dgn ekspor Excel.
//
// Tabelnya AJAX (ajx_status.php): dulu setiap Search me-reload seluruh halaman
// dan layar kosong menunggu query yang untuk filter tertentu bisa makan menitan.
// ============================================================================
$isPost  = ($_SERVER['REQUEST_METHOD'] === 'POST');
$fSupp   = $isPost ? ($_POST['nama_supp'] ?? 'ALL') : 'ALL';
$fFilter = $isPost ? ($_POST['filter'] ?? 'tgl_bpb') : 'tgl_bpb';
$fStart  = ($isPost && !empty($_POST['start_date'])) ? $_POST['start_date'] : date('d-m-Y');
$fEnd    = ($isPost && !empty($_POST['end_date']))   ? $_POST['end_date']   : date('d-m-Y');

$pilihanFilter = [
    'tgl_bpb'  => 'BPB Date',
    'tgl_kbon' => 'Kontrabon Date',
    'tgl_lp'   => 'List Payment Date',
    'tgl_pay'  => 'Payment Date',
];
?>

<!-- Skin UI bersama (kartu, tabel, badge, tombol, dropdown, tanggal, loading) -->
<link rel="stylesheet" href="../css/app-skin.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin.css'); ?>">
<style>
/* Tabel ini 18 kolom - hanya TABELNYA yang menggulir mendatar, sedangkan
   search/length/info/pagination tetap diam di luar area gulir. */
.app-dt-scroll{ overflow-x:auto; }
#mytable td, #mytable th{ white-space:nowrap; }

/* Penanda jenis dokumen - dua kelompok saja:
     IN  (biru)   = penerimaan barang, nomor .../IN/... & .../RI/...
     OUT (jingga) = pengeluaran/retur, nomor .../OUT/... & .../RO/...
   Rincian /RI vs /IN dan /RO vs /OUT tetap terbaca dari nomornya sendiri. */
.st-badge{
  display:inline-block; min-width:42px; text-align:center; padding:2px 9px;
  border-radius:20px; font-size:10.5px; font-weight:700; letter-spacing:.03em;
}
.st-badge.in{  background:#e9f0fd; color:#1d4ed8; }
.st-badge.out{ background:#fff3e6; color:#b45309; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">

  <!-- ===== Filter card ===== -->
  <div class="card app-card border-0">
    <div class="card-header app-card-header">
      <h5><i class="fa fa-info-circle" aria-hidden="true"></i> STATUS INFORMATION</h5>
    </div>
    <div class="card-body p-3">
      <form id="form-data" action="status.php" method="post">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="app-flabel">Supplier</label>
            <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" data-size="5">
              <option value="ALL"<?= $fSupp === 'ALL' ? ' selected' : '' ?>>ALL</option>
              <?php
              $sp = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
              while ($sp && $x = mysqli_fetch_assoc($sp)) {
                  $sel = ($x['Supplier'] === $fSupp) ? ' selected' : '';
                  echo '<option value="' . htmlspecialchars($x['Supplier']) . '"' . $sel . '>' . htmlspecialchars($x['Supplier']) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="app-flabel">Date Filter</label>
            <select class="form-control selectpicker" name="filter" id="filter" data-dropup-auto="false">
              <?php foreach ($pilihanFilter as $k => $label) {
                  $sel = ($k === $fFilter) ? ' selected' : '';
                  echo '<option value="' . $k . '"' . $sel . '>' . $label . '</option>';
              } ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="app-flabel">From</label>
            <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?= htmlspecialchars($fStart) ?>" placeholder="Start Date" autocomplete="off">
          </div>
          <div class="col-md-2">
            <label class="app-flabel">To</label>
            <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?= htmlspecialchars($fEnd) ?>" placeholder="End Date" autocomplete="off">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <div class="app-actions">
              <button type="submit" id="submit" class="app-btn app-btn-primary app-btn-ctl"><i class="fa fa-search"></i> Search</button>
              <button type="button" id="btnExcel" class="app-btn app-btn-success app-btn-ctl"><i class="fa fa-file-excel-o"></i> Excel</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Table card ===== -->
  <div class="card app-card border-0 mt-4">
    <div class="card-body p-4">
      <!-- .app-loading-wrap: area yg ditutup overlay loading saat tabel memuat -->
      <div class="app-loading-wrap" id="stLoad">
        <div class="app-loading">
          <div class="app-loading-box">
            <div class="app-spinner"><span>NAG</span></div>
            <div class="app-loading-text">Loading data...</div>
          </div>
        </div>
        <table id="mytable" class="table table-hover app-dt" style="width:100%">
          <thead>
            <tr>
              <th style="text-align:center;">Type</th>
              <th style="text-align:left;">Supplier</th>
              <th style="text-align:left;">No BPB / BPPB</th>
              <th style="text-align:center;">Doc Date</th>
              <th style="text-align:center;">Approved Date</th>
              <th style="text-align:center;">Verified Date</th>
              <th style="text-align:left;">No SJ</th>
              <th style="text-align:left;">No WS</th>
              <th style="text-align:left;">Style</th>
              <th style="text-align:left;">No Kontrabon</th>
              <th style="text-align:center;">Kontrabon Date</th>
              <th style="text-align:center;">Kontrabon Approved</th>
              <th style="text-align:left;">No List Payment</th>
              <th style="text-align:center;">List Payment Date</th>
              <th style="text-align:center;">LP Approved</th>
              <th style="text-align:center;">LP Closed</th>
              <th style="text-align:left;">No Payment</th>
              <th style="text-align:center;">Payment Date</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide');

  // Collapse/Expand icon
  $('#collapse-icon').addClass('fa-angle-double-left');

  // Collapse click
  $('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
  });

  function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }

    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
$(document).ready(function () {

  $('.selectpicker').selectpicker();
  $('.tanggal').datepicker({ format: "dd-mm-yyyy", startDate: "01-01-2022", autoclose: true });

  var filterVal = function () {
    return {
      nama_supp:  $('#nama_supp').val() || 'ALL',
      filter:     $('#filter').val() || 'tgl_bpb',
      start_date: $('#start_date').val() || '',
      end_date:   $('#end_date').val() || ''
    };
  };

  var badge = function (jenis) {
    var cls = jenis.toLowerCase();
    return '<span class="st-badge ' + cls + '">' + jenis + '</span>';
  };

  var tStatus = $('#mytable').DataTable({
    processing: true,
    autoWidth: false,
    order: [],
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100, 250],
    // Search/length/info/pagination DI LUAR area gulir; hanya tabel yg menggulir.
    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
         "<'app-dt-scroll't>" +
         "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
    ajax: {
      url: 'ajx_status.php',
      type: 'POST',
      data: function (d) { $.extend(d, filterVal()); },
      dataSrc: function (res) {
        if (res && res.error) {
          Swal.fire({ icon: 'error', title: 'Query error', text: res.error });
          return [];
        }
        return (res && res.data) || [];
      },
      // Query laporan ini bisa berjalan lama untuk filter tertentu; kalau putus
      // di tengah jalan, beri tahu daripada tabel diam tanpa penjelasan.
      error: function (xhr) {
        $('#stLoad').removeClass('is-loading');
        Swal.fire({
          icon: 'error',
          title: 'Gagal memuat data',
          text: 'Permintaan terputus (' + (xhr.statusText || xhr.status) + '). Coba persempit rentang tanggalnya.'
        });
      }
    },
    columns: [
      { data: 'jenis', className: 'text-center', render: function (d) { return badge(d); } },
      { data: 'nama_supp' },
      { data: 'no_bpb' },
      { data: 'tgl_bpb',      className: 'text-center' },
      { data: 'approve_bpb',  className: 'text-center' },
      { data: 'verif_date',   className: 'text-center' },
      { data: 'no_sj' },
      { data: 'no_ws' },
      { data: 'style' },
      { data: 'no_kbon' },
      { data: 'tgl_kbon',     className: 'text-center' },
      { data: 'approve_kbon', className: 'text-center' },
      { data: 'no_payment' },
      { data: 'tgl_payment',  className: 'text-center' },
      { data: 'approve_lp',   className: 'text-center' },
      { data: 'close_lp',     className: 'text-center' },
      { data: 'no_pelunasan' },
      { data: 'tgl_pelunasan',className: 'text-center' }
    ],
    language: {
      processing:  '<i class="fa fa-spinner fa-spin"></i> Loading...',
      emptyTable:  '<div class="app-empty"><i class="fa fa-inbox"></i>No data found</div>',
      zeroRecords: '<div class="app-empty"><i class="fa fa-search"></i>No matching records</div>',
      lengthMenu:  'Show _MENU_ entries',
      info:        'Showing _START_&ndash;_END_ of _TOTAL_ entries',
      infoEmpty:   'Showing 0 entries',
      paginate:    { previous: '&lsaquo; Prev', next: 'Next &rsaquo;' }
    }
  });

  // Overlay loading (skin .app-loading) mengikuti status processing DataTables.
  tStatus.on('processing.dt', function (e, settings, processing) {
    $('#stLoad').toggleClass('is-loading', processing);
  });

  // Search: muat ulang tabel saja, halaman TIDAK reload.
  $('#form-data').on('submit', function (e) { e.preventDefault(); tStatus.ajax.reload(); });

  // Excel: URL dibentuk dari nilai filter saat ini. Yang dikirim parameternya,
  // bukan teks SQL — ekspor_status.php membangun query yang sama sendiri.
  $('#btnExcel').on('click', function () {
    var f = filterVal();
    var label = $('#filter option:selected').text();
    window.open('ekspor_status.php?nama_supp=' + encodeURIComponent(f.nama_supp) +
                '&filter_key=' + encodeURIComponent(f.filter) +
                '&filter=' + encodeURIComponent(label) +
                '&start_date=' + encodeURIComponent(f.start_date) +
                '&end_date=' + encodeURIComponent(f.end_date), '_blank');
  });

  $('#reset').on('click', function () { location.href = 'status.php'; });
});
</script>

</body>

</html>
