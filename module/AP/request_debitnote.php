<?php include '../header.php';

// ============================================================================
// LIST REQUEST DEBIT NOTE — direstyle mengikuti skin app-skin.css / app-skin-form.css
// (pola yang sama dgn kontrabon_new.php / ppn_masukan_report.php).
// Logika backend (query, kolom tersembunyi, id tombol yg dipakai JS) TIDAK diubah
// — hanya markup & class yg dimodernkan.
// ============================================================================

$nama_supp = isset($_GET['nama_supp']) ? $_GET['nama_supp'] : 'ALL';
$status    = isset($_GET['status']) ? $_GET['status'] : 'ALL';
$startdate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$enddate   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$start_date = date('Y-m-d', strtotime($startdate));
$end_date   = date('Y-m-d', strtotime($enddate));

$isPost  = ($_SERVER['REQUEST_METHOD'] === 'POST');
$fSupp   = $isPost ? ($_POST['nama_supp'] ?? 'ALL') : 'ALL';
$fStatus = $isPost ? ($_POST['status'] ?? 'ALL') : 'ALL';
$fStart  = $isPost && !empty($_POST['start_date']) ? $_POST['start_date'] : date('d-m-Y');
$fEnd    = $isPost && !empty($_POST['end_date'])   ? $_POST['end_date']   : date('d-m-Y');

// Hak akses tombol Create (dicek di awal supaya tombolnya bisa ditaruh sebaris
// dgn Search/Reset/Export di kartu filter, bukan nyempil sendiri di bawah form).
$querys1 = mysqli_query($conn2, "select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Request Debitnote'");
$rs1  = mysqli_fetch_array($querys1);
$id1  = isset($rs1['id']) ? $rs1['id'] : 0;
$canCreate = ($id1 == '79');
?>

<!-- Skin UI bersama (kartu, tabel, badge, tombol, dropdown, tanggal, modal) -->
<link rel="stylesheet" href="../css/app-skin.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin.css'); ?>">
<style>
/* Halaman-spesifik: tombol pilih file di modal Upload Document. */
input[type=file]::file-selector-button{
  margin-right:14px; border:0; background:#1E3A8A; color:#fff;
  padding:9px 18px; border-radius:8px; cursor:pointer; font-weight:600; font-size:12.5px;
  transition:background .15s ease;
}
input[type=file]::file-selector-button:hover{ background:#15316f; }

/* Modal Documents — cukup lebar utk daftar dokumen + form upload terbaca
   nyaman (dulu 480px, kepencet/terlalu kecil), tapi tetap jauh lebih sempit
   drpd modal default skin (.app-modal default 1500px) krn isinya cuma list. */
#mymodal2 .modal-dialog{ max-width:600px; width:calc(100% - 2rem); }
#mymodal2 .modal-content{ border-radius:16px; overflow:hidden; }
#mymodal2 .modal-header{ padding:18px 24px; }
#mymodal2 .modal-header .modal-title{ font-size:15px; }
#mymodal2 .modal-body{ max-height:74vh; overflow:auto; padding:22px 24px 24px; }

/* Daftar dokumen — tiap dokumen jadi 1 "kartu" ringan, bukan cuma baris polos,
   supaya lebih jelas terpisah & enak dibaca saat dokumennya lebih dari satu. */
.rdn-doc-list{ max-height:320px; overflow:auto; margin:-2px -2px 4px; padding:2px; }
.rdn-doc-item{
  display:flex; align-items:center; justify-content:space-between; gap:12px;
  padding:13px 14px; margin-bottom:9px; border:1px solid #eef1f7; border-radius:12px;
  background:#f8fafc; transition:box-shadow .15s ease, border-color .15s ease;
}
.rdn-doc-item:last-child{ margin-bottom:0; }
.rdn-doc-item:hover{ border-color:#dbe4f3; box-shadow:0 3px 10px rgba(15,23,42,.06); }
.rdn-doc-info{ display:flex; align-items:center; gap:12px; min-width:0; }
.rdn-doc-ico{
  flex:0 0 auto; width:38px; height:38px; border-radius:10px; background:#fdecec;
  display:flex; align-items:center; justify-content:center;
}
.rdn-doc-ico i{ color:#dc2626; font-size:17px; }
.rdn-doc-name{ font-size:13px; font-weight:700; color:#1e293b; max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.rdn-doc-meta{ font-size:11px; color:#94a3b8; margin-top:2px; }
.rdn-doc-actions{ display:flex; gap:5px; flex:0 0 auto; }
.rdn-doc-actions .btn{ padding:6px 10px; border-radius:7px; }

/* Empty state — lebih besar & lega drpd .app-empty bawaan, cocok utk kotak modal ini. */
.rdn-doc-empty{ text-align:center; padding:34px 10px; color:#94a3b8; }
.rdn-doc-empty i{ display:block; font-size:40px; color:#dbe4f3; margin-bottom:12px; }
.rdn-doc-empty span{ font-size:13px; font-weight:600; }

/* Judul seksi "Add Document" */
.rdn-doc-addhead{
  display:flex; align-items:center; gap:8px; margin:18px 0 12px;
  font-size:11px; font-weight:700; color:#1e3a8a; text-transform:uppercase; letter-spacing:.5px;
}
.rdn-doc-addhead:before{ content:''; flex:0 0 auto; width:3px; height:14px; background:#1e3a8a; border-radius:2px; }

/* Kotak upload — dibuat spt dropzone (border putus-putus) supaya lebih menarik
   drpd input file polos, walau tetap input file bawaan (bukan drag-drop asli). */
.rdn-doc-upload{
  display:flex; align-items:center; gap:14px; padding:14px 16px; border:1.5px dashed #c7d4ea;
  border-radius:12px; background:#f8fafc;
}
.rdn-doc-upload input[type=file]{ border:0; background:transparent; padding:0; flex:1; min-width:0; font-size:12.5px; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">

  <!-- ===== Filter card ===== -->
  <div class="card app-card border-0">
    <div class="card-header app-card-header">
      <h5><i class="fa fa-file-text-o" aria-hidden="true"></i> LIST REQUEST DEBIT NOTE</h5>
    </div>
    <div class="card-body p-3">
      <form id="form-data" action="request_debitnote.php" method="post">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="app-flabel">Supplier</label>
            <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-live-search="true" data-size="5">
              <option value="ALL"<?= $fSupp === 'ALL' ? ' selected' : '' ?>>ALL</option>
              <?php
              $sp = mysqli_query($conn1, "SELECT DISTINCT Supplier sup FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
              while ($sp && $x = mysqli_fetch_assoc($sp)) {
                  $sel = ($x['sup'] === $fSupp) ? ' selected' : '';
                  echo '<option value="' . htmlspecialchars($x['sup']) . '"' . $sel . '>' . htmlspecialchars($x['sup']) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="app-flabel">Status</label>
            <select class="form-control selectpicker" name="status" id="status" data-live-search="true">
              <option value="ALL"<?= $fStatus === 'ALL' ? ' selected' : '' ?>>ALL</option>
              <?php
              $sqStat = mysqli_query($conn1, "select distinct(nama_status) nama_status from ir_status where status = 'dn' order by id ASC");
              while ($sqStat && $x = mysqli_fetch_assoc($sqStat)) {
                  $sel = ($x['nama_status'] === $fStatus) ? ' selected' : '';
                  echo '<option value="' . htmlspecialchars($x['nama_status']) . '"' . $sel . '>' . htmlspecialchars($x['nama_status']) . '</option>';
              }
              ?>
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
              <?php if ($canCreate) { ?>
              <button type="button" id="btncreatenew" class="app-btn app-btn-success app-btn-ctl"><i class="fa fa-plus-circle"></i> Create</button>
              <?php } ?>
              <!-- URL ekspor dibentuk di JS dari nilai filter saat itu, karena Search
                   tidak lagi reload halaman (tabelnya AJAX). -->
              <button type="button" id="btnExcel" class="app-btn app-btn-warning app-btn-ctl"><i class="fa fa-file-excel-o"></i> Excel</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Table card ===== -->
  <div class="card app-card border-0 mt-4">
    <div class="card-body p-4">
      <!-- .app-loading-wrap: area yg ditutup overlay loading saat tabel memuat data -->
      <div class="app-loading-wrap" id="rdnLoad">
        <div class="app-loading">
          <div class="app-loading-box">
            <div class="app-spinner"><span>NAG</span></div>
            <div class="app-loading-text">Loading data...</div>
          </div>
        </div>
        <div class="app-dt-scroll">
          <table id="datatable" class="table table-hover app-dt" style="width:100%">
            <thead>
              <tr>
                <th style="text-align:left;">No Request</th>
                <th style="text-align:center;">Request Date</th>
                <th style="text-align:left;">Supplier</th>
                <th style="text-align:right;">Total Amount</th>
                <th style="text-align:center;">Status</th>
                <th style="text-align:center;">No DN</th>
                <th style="text-align:left;">Create User</th>
                <th style="text-align:center;">Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- ===== Modal: Documents (upload + daftar dokumen) Request Debitnote =====
     Dulu 1 request cuma bisa punya 1 dokumen (tombol upload hilang begitu ada
     1 file). Sekarang modal ini menampilkan DAFTAR semua dokumen aktif + form
     upload di bawahnya — upload tidak menutup modal, jadi bisa dipakai
     berkali-kali berturut-turut. Lebar modal DIPERKECIL (lihat #mymodal2 di
     <style> atas) — modal upload sederhana ini tidak perlu selebar modal
     tabel/detail lain di halaman ini. -->
<div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog app-modal modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-pdf-o"></i> Documents &mdash; <span id="docModalNoReq"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <div class="rdn-doc-list" id="docListBody"></div>

        <form id="modal-form2" method="post" action="insert_doc_reqdn.php" enctype="multipart/form-data">
          <input type="hidden" class="form-control" name="txt_no_req" id="txt_no_req" value="">
          <input type="hidden" class="form-control" name="txt_user" id="txt_user" value="<?php echo $user; ?>">
          <div class="rdn-doc-addhead"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add Document</div>
          <div class="rdn-doc-upload">
            <input type="file" id="txtfile" name="txtfile" accept="application/pdf">
            <button type="submit" id="send2" name="send2" class="app-btn app-btn-primary app-btn-ctl"><i class="fa fa-cloud-upload"></i> Upload</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ===== Modal: viewer dokumen (PDF embed) ===== -->
<div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog app-modal modal-dialog-centered" role="document" style="max-width:760px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-pdf-o"></i> Document Request Debitnote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <div id="labelfile" name="labelfile" style="margin-bottom:10px;"></div>
        <div id="fileshow" name="fileshow"></div>
      </div>
    </div>
  </div>
</div>

<!-- ===== Modal: detail request (BPB / faktur) ===== -->
<div class="modal fade" id="mymodalftrdp" data-target="#mymodalftrdp" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog app-modal modal-dialog-centered" role="document" style="max-width:1240px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dtl_no"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <div class="app-mod-info">
          <div id="dtl_date"></div>
          <div id="dtl_supp"></div>
          <div id="dtl_status"></div>
          <div id="dtl_amount"></div>
          <div id="dtl_user"></div>
        </div>
        <div id="details" style="overflow-x:auto;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
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

    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }

    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>
<script>
$(document).ready(function () {

  $('.selectpicker').selectpicker();
  $('.tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });

  var esc = function (s) {
    return String(s === null || s === undefined ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  };
  var filterVal = function () {
    return {
      nama_supp:  $('select[name=nama_supp]').val() || 'ALL',
      status:     $('select[name=status]').val() || 'ALL',
      start_date: $('#start_date').val() || '',
      end_date:   $('#end_date').val() || ''
    };
  };

  // ---- DataTables AJAX -------------------------------------------------------
  // Data pengenal baris ditempel sbg data-* di <tr> lewat createdRow. Cara lama
  // (kolom tersembunyi lalu dibaca td:eq(8)/td:eq(9)) tidak bisa dipakai lagi:
  // kolom yg di-hide DataTables dihapus dari DOM.
  var tRdn = $('#datatable').DataTable({
    processing: true,
    autoWidth: false,
    order: [],
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100],
    ajax: {
      url: 'ajx_request_debitnote.php',
      type: 'POST',
      data: function (d) { $.extend(d, filterVal()); d.can_edit = <?= $canCreate ? 1 : 0 ?>; },
      dataSrc: 'data'
    },
    columns: [
      { data: 'no_req' },
      { data: 'tgl_req',      className: 'text-center' },
      { data: 'nama_supp' },
      { data: 'total_amount', className: 'text-right' },
      { data: 'status',       className: 'text-center' },
      { data: 'no_dn',        className: 'text-center' },
      { data: 'create_user' },
      { data: 'action',       className: 'text-center', orderable: false, searchable: false }
    ],
    createdRow: function (row, data) {
      $(row).attr({
        'data-no-req':  data._no_req,
        'data-tgl':     data._tgl,
        'data-supp':    data._supp,
        'data-status':  data._status,
        'data-user':    data._user,
        'data-amount':  data._amount,
        'data-no-dn':   data._no_dn
      });
    },
    drawCallback: function () { $('[data-toggle=tooltip]').tooltip(); },
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
  tRdn.on('processing.dt', function (e, settings, processing) {
    $('#rdnLoad').toggleClass('is-loading', processing);
  });

  // Search: muat ulang tabel saja, halaman TIDAK reload.
  $('#form-data').on('submit', function (e) { e.preventDefault(); tRdn.ajax.reload(); });

  // Excel: URL dibentuk dari nilai filter saat ini.
  $('#btnExcel').on('click', function () {
    var f = filterVal();
    window.open('ekspor_reqdn.php?nama_supp=' + encodeURIComponent(f.nama_supp) +
                '&status=' + encodeURIComponent(f.status) +
                '&start_date=' + encodeURIComponent(f.start_date) +
                '&end_date=' + encodeURIComponent(f.end_date), '_blank');
  });

  // ---- Aksi per baris --------------------------------------------------------
  // Didelegasikan dari <tbody> (bukan dari tiap <tr>) karena barisnya dibuat
  // ulang tiap kali tabel di-draw.
  var $body = $('#datatable tbody');

  // Klik nomor request -> modal detail
  $body.on('click', '.js-detail', function () {
    var $tr = $(this).closest('tr');
    var noReq = $tr.data('no-req');
    $('#dtl_no').text(noReq);
    $('#dtl_date').html('<b>Request Date</b><span>' + esc($tr.data('tgl')) + '</span>');
    $('#dtl_supp').html('<b>Supplier</b><span>' + esc($tr.data('supp')) + '</span>');
    // Warna badge status disamakan dgn yg di tabel (lihat ajx_request_debitnote.php).
    var st = String($tr.data('status') || ''), lc = st.toLowerCase();
    var bcls = (lc === 'cancel') ? 'cancel' : (lc === 'processed' ? 'done' : 'process');
    $('#dtl_status').html('<b>Status</b><span><span class="kb-badge ' + bcls + '">' + esc(st) + '</span></span>');
    $('#dtl_amount').html('<b>Total Amount</b><span>' + esc($tr.data('amount')) + '</span>');
    $('#dtl_user').html('<b>Created By</b><span>' + esc($tr.data('user')) + '</span>');
    $('#details').html('<div class="text-center" style="padding:34px 0;">' +
      '<div class="app-spinner" style="margin:0 auto;"><span>NAG</span></div>' +
      '<div class="app-loading-text" style="margin-top:12px;">Loading detail...</div></div>');
    $('#mymodalftrdp').modal('show');
    $.post('ajax_reqdn.php', { no_req: noReq })
      .done(function (html) { $('#details').html(html); })
      .fail(function () { $('#details').html('<div class="app-empty" style="padding:30px 0;"><i class="fa fa-exclamation-triangle"></i>Failed to load detail.</div>'); });
  });

  // ---- Documents modal (bisa upload berkali-kali per request) ---------------
  // Dulu ada 3 tombol terpisah (upload / preview / delete) dan upload cuma
  // muncul kalau BELUM ada dokumen sama sekali — begitu 1 file terupload,
  // tombol upload diganti preview+delete, jadi harus hapus dulu baru bisa
  // upload lagi. Sekarang SATU tombol "Documents" selalu ada, membuka modal
  // berisi daftar semua dokumen aktif request itu + form utk menambah lagi.
  function loadDocList(noReq) {
    $('#docListBody').html('<div class="text-center" style="padding:20px 0;color:#94a3b8;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $.post('ajx_req_dn_dok.php', { no_req: noReq })
      .done(function (res) {
        var rows = (res && res.data) || [];
        if (!rows.length) {
          $('#docListBody').html('<div class="rdn-doc-empty"><i class="fa fa-file-o"></i><span>No document uploaded yet</span></div>');
          return;
        }
        var html = '';
        rows.forEach(function (r) {
          html += '<div class="rdn-doc-item" data-id="' + r.id + '" data-file="' + esc(r.file_name) + '" data-file-as="' + esc(r.file_name_as) + '">' +
            '<div class="rdn-doc-info"><div class="rdn-doc-ico"><i class="fa fa-file-pdf-o"></i></div>' +
              '<div><div class="rdn-doc-name" title="' + esc(r.file_name_as) + '">' + esc(r.file_name_as) + '</div>' +
              '<div class="rdn-doc-meta">' + esc(r.created_by) + ' &middot; ' + esc(r.created_date) + '</div></div></div>' +
            '<div class="rdn-doc-actions">' +
              '<button type="button" class="btn btn-info btn-sm js-doc-preview" title="Preview"><i class="fa fa-eye"></i></button>' +
              '<a href="file_pdf/' + esc(r.file_name) + '" target="_blank" class="btn btn-warning btn-sm" title="Open in new tab"><i class="fa fa-external-link"></i></a>' +
              '<button type="button" class="btn btn-outline-danger btn-sm js-doc-del" title="Delete"><i class="fa fa-trash"></i></button>' +
            '</div></div>';
        });
        $('#docListBody').html(html);
      })
      .fail(function () {
        $('#docListBody').html('<div class="rdn-doc-empty"><i class="fa fa-exclamation-triangle"></i><span>Failed to load documents.</span></div>');
      });
  }

  // Buka modal Documents
  $body.on('click', '.js-manage-doc', function () {
    var noReq = $(this).closest('tr').data('no-req');
    $('#txt_no_req').val(noReq);
    $('#docModalNoReq').text(noReq);
    $('#txtfile').val('');
    loadDocList(noReq);
    $('#mymodal2').modal('show');
  });

  // Preview salah satu dokumen di daftar
  $('#docListBody').on('click', '.js-doc-preview', function () {
    var $it = $(this).closest('.rdn-doc-item');
    $('#labelfile').html('<b>' + esc($it.data('file-as')) + '</b>');
    $('#fileshow').html('<embed src="file_pdf/' + $it.data('file') +
      '" type="application/pdf" frameborder="0" width="100%" height="460px">');
    $('#mymodal3').modal('show');
  });

  // Hapus salah satu dokumen di daftar (baris lain milik request yg sama tidak ikut terhapus)
  $('#docListBody').on('click', '.js-doc-del', function () {
    var $it = $(this).closest('.rdn-doc-item'), id = $it.data('id'), noReq = $('#txt_no_req').val();
    if (!confirm('Remove this document?')) { return; }
    $.post('cancel_req_dn_dok.php', { id: id, cancel_user: '<?php echo $user ?>' })
      .always(function () { loadDocList(noReq); tRdn.ajax.reload(null, false); });
  });

  // Upload dokumen baru — AJAX (bukan submit form biasa) supaya modal TIDAK
  // ikut reload/tertutup, jadi bisa langsung upload dokumen berikutnya lagi
  // tanpa buka-tutup modal berulang kali.
  $('#modal-form2').on('submit', function (e) {
    e.preventDefault();
    if (!document.getElementById('txtfile').files.length) {
      Swal.fire({ icon: 'warning', title: 'Please choose a file to upload.' });
      return;
    }
    var noReq = $('#txt_no_req').val();
    var fd = new FormData(this);
    $('#send2').prop('disabled', true);
    $.ajax({
      type: 'POST', url: 'insert_doc_reqdn.php', data: fd,
      contentType: false, processData: false, dataType: 'json'
    }).done(function (res) {
      if (res && res.status === 'success') {
        $('#txtfile').val('');
        loadDocList(noReq);
        tRdn.ajax.reload(null, false);
      } else {
        Swal.fire({ icon: 'error', title: 'Upload failed', text: (res && res.message) || 'Unknown error' });
      }
    }).fail(function (xhr) {
      Swal.fire({ icon: 'error', title: 'Error', text: String((xhr && xhr.responseText) || (xhr && xhr.statusText) || xhr) });
    }).always(function () {
      $('#send2').prop('disabled', false);
    });
  });

  // Edit request (tambah/kurangi BPB — supplier tidak bisa diubah)
  $body.on('click', '.js-edit-req', function () {
    var noReq = $(this).closest('tr').data('no-req');
    location.href = 'edit_request_dn.php?no_req=' + encodeURIComponent(noReq);
  });

  // Cancel seluruh request
  $body.on('click', '.js-cancel-req', function () {
    var noReq = $(this).closest('tr').data('no-req');
    if (!confirm('Cancel request ' + noReq + '?')) { return; }
    $('#rdnLoad').addClass('is-loading');
    $.post('cancel_req_dn.php', { no_req: noReq, cancel_user: '<?php echo $user ?>' })
      .always(function () { $('#rdnLoad').removeClass('is-loading'); tRdn.ajax.reload(null, false); });
  });

  // Tombol Create (hanya dirender kalau user berhak)
  var btnCreateNew = document.getElementById('btncreatenew');
  if (btnCreateNew) {
    btnCreateNew.onclick = function () { location.href = "create_request_dn.php"; };
  }
  // Tombol Reset disembunyikan; handler dijaga null-check kalau nanti dimunculkan lagi.
  var btnReset = document.getElementById('reset');
  if (btnReset) {
    btnReset.onclick = function () { location.href = "request_debitnote.php"; };
  }
});
</script>

<script>
  function alert_cancel() {
    alert("Data Berhasil di Cancel");
    location.reload();
  }
  function alert_approve() {
    alert("Data Berhasil di Approve");
    location.reload();
  }
</script>
