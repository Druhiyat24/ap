<?php include '../header.php' ?>
<?php
// ============================================================================
// LIST KONTRABON (Kontrabon New) - daftar + filter + Create, mengikuti pola
// menu list app (contoh: bank-out.php): card filter terpisah dari card tabel,
// baris tombol Search + Create. Styling BIRU khas app. UI full English.
// Bootstrap 4.5 -> pakai ml-/mr- (bukan ms-/me-).
// ============================================================================

$isPost  = ($_SERVER['REQUEST_METHOD'] === 'POST');
$fSupp   = $isPost ? ($_POST['nama_supp'] ?? 'ALL') : 'ALL';
$fStatus = $isPost ? ($_POST['status'] ?? 'ALL') : 'ALL';
$fStart  = ($isPost && !empty($_POST['start_date'])) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$fEnd    = ($isPost && !empty($_POST['end_date']))   ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');

// Status yang ditampilkan = MIRROR dari sisi Invoice Received (ir_invoice_supp_h):
// Received -> Post Fin To Acc -> Accepted Acc -> Post Acc To Pch -> Accepted Pch -> Post Pch To Fin -> ...
// Kalau kontrabon sudah Cancel, tampil Cancel. Effective status = COALESCE(ir, kontrabon).
// Data tabel di-load via AJAX (ajx_kontrabon_new.php); di sini cukup opsi dropdown Status.
$eff = "COALESCE(ih.status, kh.status)";

// Opsi dropdown Status dibangun dinamis dari status efektif yang benar-benar ada.
$stOpts = ['ALL'];
$sq = mysqli_query($conn2, "SELECT DISTINCT $eff st FROM ir_kontrabon_h kh
    LEFT JOIN ir_invoice_supp_h ih ON ih.doc_number = kh.doc_number ORDER BY st");
while ($sq && ($sx = mysqli_fetch_assoc($sq))) { if (!empty($sx['st'])) $stOpts[] = $sx['st']; }
?>

<!-- Skin UI bersama (kartu, tabel, badge, tombol, chrome DataTables) -->
<link rel="stylesheet" href="../css/app-skin.css">
<style>
/* Halaman-spesifik: hanya gaya modal detail. Sisanya di app-skin.css. */
/* ---- KB detail modal ---- */
#kbDetailModal .modal-dialog{ max-width:1500px; width:94vw; }
#kbDetailModal .modal-content{ border:0; border-radius:16px; overflow:hidden; box-shadow:0 24px 70px rgba(15,23,42,.28); }
#kbDetailModal .modal-header{ border:0; padding:16px 24px; align-items:center; }
#kbDetailModal .modal-title{ font-weight:700; font-size:19px; letter-spacing:.02em; }
#kbDetailModal .modal-header .close{ opacity:.9; text-shadow:none; font-size:22px; }
#kbDetailModal .modal-body{ padding:20px 24px 24px; max-height:74vh; overflow-y:auto; }
.kbd-info{ display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:2px 26px; background:#f8fafc; border:1px solid #e8edf5; border-radius:12px; padding:12px 20px; margin-bottom:14px; }
.kbd-info > div{ padding:7px 0; min-width:0; }
.kbd-info .kbd-desc-row{ grid-column:1 / -1; border-top:1px solid #e8edf5; margin-top:6px; padding-top:11px; }
.kbd-info .kbd-desc-row .dval{ display:block; font-size:13.5px; font-weight:500; color:#334155; }
@media (max-width:768px){ .kbd-info{ grid-template-columns:repeat(2, minmax(0,1fr)); } }
.kbd-info .lbl, .fm-lbl{ display:block; font-size:10px; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; margin-bottom:1px; }
.kbd-info .val{ display:block; font-size:13.5px; font-weight:600; color:#0f172a; }
.kbd-desc{ font-size:13px; color:#334155; margin-bottom:16px; }
.kbd-desc .lbl{ font-size:10px; text-transform:uppercase; color:#94a3b8; letter-spacing:.05em; margin-right:4px; }
.kbd-inv{ border:1px solid #dbe4f3; border-radius:10px; padding:12px 14px; margin-bottom:14px; }
.kbd-inv-head{ display:flex; flex-wrap:wrap; align-items:center; gap:8px 16px; margin-bottom:10px; }
.kbd-inv-title{ font-size:14px; color:#0f172a; } .kbd-inv-title b{ color:#1e3a8a; }
.kbd-chip{ font-size:12px; background:#eef4ff; color:#1e3a8a; border-radius:20px; padding:3px 12px; font-weight:600; }
.kbd-fak{ background:#fff; border:1px solid #e6ebf3; border-left:3px solid #2563eb; border-radius:8px; padding:10px 12px; margin:8px 0; }
.kbd-fak-head{ font-size:13px; color:#0f172a; margin-bottom:8px; } .kbd-fak-head b{ color:#1e3a8a; }
.faktur-meta{ display:grid; grid-template-columns:repeat(auto-fit, minmax(150px,1fr)); gap:8px 24px; background:#f8fafc; border:1px solid #e6ebf3; border-radius:8px; padding:10px 14px; margin-bottom:10px; }
.faktur-meta > div{ display:flex; flex-direction:column; min-width:0; }
.fm-val{ font-size:13px; font-weight:600; color:#0f172a; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.kbd-bpb-table{ width:100%; border-collapse:separate; border-spacing:0; font-size:12px; }
.kbd-bpb-table thead th{ background:#1E3A8A; color:#fff; font-weight:600; padding:7px 9px; text-align:center; white-space:nowrap; }
.kbd-bpb-table tbody td{ padding:7px 9px; border-bottom:1px solid #eef2f7; }
.kbd-bpb-table tbody tr:hover{ background:#f8fafc; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">

  <!-- ===== Filter card ===== -->
  <div class="card app-card border-0">
    <div class="card-header app-card-header">
      <h5><i class="fa fa-list-alt" aria-hidden="true"></i> LIST INVOICE RECEIVED</h5>
    </div>
    <div class="card-body p-3">
      <form id="form-filter" method="post" action="kontrabon_new.php">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="app-flabel">Supplier</label>
            <select class="form-control selectpicker" name="nama_supp" data-live-search="true" data-size="5">
              <option value="ALL"<?= $fSupp === 'ALL' ? ' selected' : '' ?>>ALL</option>
              <?php
              $sp = mysqli_query($conn1, "SELECT DISTINCT Supplier sup FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
              while ($x = mysqli_fetch_assoc($sp)) {
                  $sel = ($x['sup'] === $fSupp) ? ' selected' : '';
                  echo '<option value="' . htmlspecialchars($x['sup']) . '"' . $sel . '>' . htmlspecialchars($x['sup']) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="app-flabel">Status</label>
            <select class="form-control selectpicker" name="status" data-size="8" data-live-search="true">
              <?php foreach ($stOpts as $st) {
                  $sel = ($st === $fStatus) ? ' selected' : '';
                  echo '<option value="' . htmlspecialchars($st) . '"' . $sel . '>' . htmlspecialchars($st) . '</option>';
              } ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="app-flabel">From</label>
            <input type="text" class="form-control form-control-sm tanggal" name="start_date" value="<?= date('d M Y', strtotime($fStart)) ?>" autocomplete="off">
          </div>
          <div class="col-md-2">
            <label class="app-flabel">To</label>
            <input type="text" class="form-control form-control-sm tanggal" name="end_date" value="<?= date('d M Y', strtotime($fEnd)) ?>" autocomplete="off">
          </div>
          <div class="col-md-3 d-flex align-items-end mt-2">
            <button type="submit" class="btn btn-info btn-sm btn-round"><i class="fa fa-search"></i> Search</button>
            <a href="create_kontrabon_new.php" class="btn btn-primary btn-sm btn-round ml-2"><i class="fa fa-plus-circle"></i> Create</a>
            <button type="button" id="btnExport" class="btn btn-success btn-sm btn-round ml-2"><i class="fa fa-file-excel-o"></i> Export</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Table card ===== -->
  <div class="card app-card border-0 mt-4">
    <div class="card-body p-4">
      <div class="app-dt-wrap">
        <!-- Loading overlay: spinner dalam kartu, menutup HANYA area tabel (ukuran di-set via JS) -->
        <div id="kbSpin" class="app-dt-overlay" style="display:none">
          <div class="app-spin-box" role="status" aria-label="Loading">
            <span class="app-spinner"></span>
            <span class="app-spin-txt">Loading&hellip;</span>
          </div>
        </div>
        <table id="tblKb" class="table table-hover app-dt" style="width:100%">
          <thead class="table-gradient">
            <tr>
              <th style="width:50px; text-align:center;">No</th>
              <th style="text-align:left;">Document Number</th>
              <th style="width:175px; text-align:center;">Actual Document Receiving Date</th>
              <th style="width:130px; text-align:center;">Invoice Received Date</th>
              <th style="width:110px; text-align:center;">No Reff</th>
              <th style="text-align:left; min-width:230px;">Supplier</th>
              <th style="width:150px; text-align:right;">Total Amount</th>
              <th style="width:120px; text-align:right;">Add in PV</th>
              <th style="width:150px; text-align:right;">Grand Total</th>
              <th style="width:100px; text-align:center;">Status</th>
              <th style="text-align:left;">Created By</th>
              <th style="width:140px; text-align:center;">Created Date</th>
              <th style="width:250px; text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody><!-- diisi via AJAX (DataTables) --></tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ===== KB detail modal ===== -->
<div class="modal fade" id="kbDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h4 class="modal-title mb-0" id="kbDetailTitle"></h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body" id="kbDetailBody"></div>
    </div>
  </div>
</div>

<!-- Library dimuat per-halaman (header.php TIDAK memuatnya). jQuery dulu. -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script>
var kbTable;
$(function () {
  $('.selectpicker').selectpicker();
  $('.tanggal').datepicker({ format:'dd M yyyy', autoclose:true, language:'en' })
    // Samakan lebar kalender popup dengan lebar input pemicunya.
    .on('show', function () {
      var w = $(this).outerWidth();
      setTimeout(function () { $('.datepicker-dropdown:visible').outerWidth(w); }, 0);
    });

  // ===== DataTables AJAX (pola app: endpoint balikin {data, footer}) =====
  kbTable = $('#tblKb').DataTable({
    ordering: false,
    processing: false,
    autoWidth: false,
    // Responsive: di layar kecil/Android kolom yg tak muat kolaps ke baris "+"
    // (detail), sementara No Document / Status / Action tetap tampil (priority 1-2).
    responsive: true,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    // Teks chrome + empty/loading state (styling-nya di app-skin.css)
    language: {
      processing:  '<i class="fa fa-spinner fa-spin"></i> Loading...',
      emptyTable:  '<div class="app-empty"><i class="fa fa-inbox"></i>No data found</div>',
      zeroRecords: '<div class="app-empty"><i class="fa fa-search"></i>No matching records</div>',
      lengthMenu:  'Show _MENU_ entries',
      info:        'Showing _START_&ndash;_END_ of _TOTAL_ entries',
      infoEmpty:   'Showing 0 entries',
      infoFiltered:'(filtered from _MAX_)',
      search:      'Search:',
      paginate:    { previous: '&lsaquo; Prev', next: 'Next &rsaquo;' }
    },
    ajax: {
      url: 'ajx_kontrabon_new.php',
      type: 'POST',
      data: function (d) {
        d.nama_supp  = $('[name=nama_supp]').val() || 'ALL';
        d.status     = $('[name=status]').val() || 'ALL';
        d.start_date = $('[name=start_date]').val() || '';
        d.end_date   = $('[name=end_date]').val() || '';
      },
      dataSrc: 'data'
    },
    // responsivePriority kecil = makin dipertahankan saat layar menyempit.
    // No Document (1), Action (1), Status (2) tetap tampil; sisanya kolaps duluan.
    columns: [
      { data: null, orderable: false, className: 'text-center', responsivePriority: 1,
        render: function (d, t, r, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
      { data: 'doc_number', responsivePriority: 1 },
      { data: 'document_date', className: 'text-center' },
      { data: 'kontrabon_date', className: 'text-center' },
      { data: 'no_reff', className: 'text-center' },
      { data: 'nama_supp' },
      { data: 'total_amount', className: 'text-right' },
      { data: 'amount_add_pv', className: 'text-right' },
      { data: 'grand_total', className: 'text-right' },
      { data: 'status', className: 'text-center', responsivePriority: 2 },
      { data: 'create_user' },
      { data: 'create_date', className: 'text-center' },
      { data: 'action', className: 'text-center', orderable: false, responsivePriority: 2 }
    ]
  });

  // Loading overlay (spinner) — ukur ke posisi & ukuran tabel supaya HANYA area
  // tabel yang tertutup (bukan filter/pagination), tampil saat request, sembunyi
  // saat data selesai digambar.
  function kbShowSpin() {
    var $t = $('#tblKb'); if (!$t.length) return;
    var pos = $t.position() || { top: 0, left: 0 };
    $('#kbSpin').css({
      top:    pos.top,
      left:   pos.left,
      width:  $t.outerWidth() || '100%',
      height: Math.max($t.outerHeight() || 0, 180)
    }).css('display', 'flex');
  }
  kbTable.on('preXhr.dt', function () { kbShowSpin(); });
  kbTable.on('draw.dt',   function () { $('#kbSpin').hide(); });
  kbShowSpin(); // tampilkan saat load pertama (draw pertama akan menyembunyikan)

  // Search -> reload tabel (bukan reload halaman)
  $('#form-filter').on('submit', function (e) { e.preventDefault(); kbTable.ajax.reload(); });

  // Export list ke Excel (ikut filter aktif di form)
  $('#btnExport').on('click', function () {
    var p = $.param({
      nama_supp:  $('[name=nama_supp]').val() || 'ALL',
      status:     $('[name=status]').val() || 'ALL',
      start_date: $('[name=start_date]').val() || '',
      end_date:   $('[name=end_date]').val() || ''
    });
    window.location = 'ekspor_kontrabon.php?' + p;
  });

  // Show detail Kontrabon
  $('#tblKb').on('click', '.show-kb', function(){
    var doc = $(this).data('doc');
    $('#kbDetailTitle').text(doc);
    $('#kbDetailBody').html('<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#kbDetailModal').modal('show');
    $.post('kontrabon_new_detail.php', { doc_number: doc }, function (html) {
      $('#kbDetailBody').html(html);
    }).fail(function () { $('#kbDetailBody').html('<div class="text-danger p-3">Failed to load detail.</div>'); });
  });

  // Cancel Kontrabon -> reff jadi Available lagi & BPB bisa dipakai ulang
  $('#tblKb').on('click', '.cancel-kb', function(){
    var doc = $(this).data('doc');
    Swal.fire({
      icon:'warning', title:'Cancel this Invoice Received?',
      html:'<b>' + doc + '</b> will be cancelled.<br><small class="text-muted">Its reference number becomes available again and its invoices / faktur / BPB can be re-input.</small>',
      showCancelButton:true, confirmButtonText:'Yes, cancel it', cancelButtonText:'No', confirmButtonColor:'#dc2626'
    }).then(function (r) {
      if (!r.isConfirmed) return;
      Swal.fire({ title:'Cancelling...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });
      $.post('kontrabon_new_cancel.php', { doc_number: doc }, function (res) {
        if (res.ok) { Swal.fire({icon:'success', title:'Cancelled', text: res.msg}).then(function(){ kbTable.ajax.reload(null, false); }); }
        else { Swal.fire({icon:'error', title:'Failed', text: res.msg}); }
      }, 'json').fail(function(){ Swal.fire({icon:'error', title:'Error', text:'Failed to reach the server.'}); });
    });
  });
});
</script>
