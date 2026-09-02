<?php include '../header.php' ?>
<?php
// ============================================================================
// KONTRABON REFERENCE NUMBER (master: ir_kontrabon_ref)
// Paper bon numbers purchased from suppliers - must be registered first (per
// book) before they can be used when creating a Kontrabon New. Two input
// methods: manual (one by one) and Excel upload (first column = number,
// parsed in the browser via SheetJS). Save/delete via AJAX.
// Keeps the app's signature blue form (card header navy->blue gradient); only
// the Excel upload area is modernised (drag & drop + template + file chip).
// UI text is full English (per user request).
// ============================================================================

$listRes = mysqli_query($conn2, "SELECT r.id, r.ref_number, r.status, r.keterangan, r.create_user, r.create_date,
    (SELECT h.doc_number FROM ir_kontrabon_h h WHERE h.no_reff = r.ref_number AND h.status <> 'Cancel' ORDER BY h.id DESC LIMIT 1) AS kontrabon_no
    FROM ir_kontrabon_ref r ORDER BY CAST(r.ref_number AS UNSIGNED) ASC, r.ref_number ASC");

$cntAvail = 0; $cntUsed = 0; $cntCancel = 0;
$cRes = mysqli_query($conn2, "SELECT status, COUNT(*) c FROM ir_kontrabon_ref GROUP BY status");
while ($cr = mysqli_fetch_assoc($cRes)) {
    if ($cr['status'] === 'Used') { $cntUsed = (int) $cr['c']; }
    elseif ($cr['status'] === 'Cancel') { $cntCancel = (int) $cr['c']; }
    else { $cntAvail += (int) $cr['c']; }
}
?>

<style>
/* Ciri khas app: card header gradient navy->biru (JANGAN diubah). */
.knr-card .card-header{ background: linear-gradient(90deg, #191970, #1e90ff); }
.knr-badge{ display:inline-block; padding:2px 10px; border-radius:12px; font-size:12px; font-weight:600; }
.knr-badge.avail{ background:#e6f7ec; color:#1f9d57; }
.knr-badge.used{ background:#fdecec; color:#dc2626; }
/* Dropzone (satu-satunya bagian yg dimodernkan) */
.knr-drop{ border:2px dashed #cbd5e1; border-radius:10px; padding:22px 16px; text-align:center; background:#f8fafc; cursor:pointer; transition:.18s; }
.knr-drop:hover{ border-color:#93c5fd; background:#f2f8ff; }
.knr-drop.drag{ border-color:#1e90ff; background:#eaf2ff; }
.knr-drop .dz-ic{ font-size:30px; color:#93c5fd; margin-bottom:6px; }
.knr-drop.drag .dz-ic{ color:#1e90ff; }
.knr-drop .dz-title{ font-weight:600; color:#334155; }
.knr-drop .dz-title .lnk{ color:#1e90ff; text-decoration:underline; }
.knr-drop .dz-sub{ font-size:12px; color:#94a3b8; margin-top:3px; }
.knr-chip{ display:inline-flex; align-items:center; gap:9px; margin-top:12px; background:#eef6ff; border:1px solid #d7e8ff; color:#1e40af; padding:7px 13px; border-radius:9px; font-size:13px; font-weight:600; }
.knr-chip .cnt{ background:#1e90ff; color:#fff; border-radius:20px; padding:1px 9px; font-size:11px; }
.knr-chip .x{ cursor:pointer; color:#64748b; } .knr-chip .x:hover{ color:#dc2626; }
/* Blue DataTable header (khas app) + badge Cancel */
.table-gradient th{ background:#1E3A8A !important; color:#fff !important; text-align:center; vertical-align:middle; white-space:nowrap; border-color:#1E3A8A !important; }
.knr-badge.cancel{ background:#f1f5f9; color:#64748b; }
#tblRef tbody td{ vertical-align:middle; }
#tblRef td, #tblRef th{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
#tblRef td.desc-cell{ white-space:normal; }
/* Tombol Cancel (merah, rapi) */
.btn-cancel{ display:inline-flex; align-items:center; gap:5px; background:#fff; color:#dc2626; border:1px solid #fca5a5; border-radius:8px; padding:4px 13px; font-size:12.5px; font-weight:600; cursor:pointer; transition:.15s; }
.btn-cancel:hover{ background:#dc2626; color:#fff; border-color:#dc2626; box-shadow:0 3px 8px rgba(220,38,38,.28); }
.btn-cancel:active{ transform:translateY(1px); }
#tblRef.dataTable thead .sorting:after, #tblRef.dataTable thead .sorting_asc:after, #tblRef.dataTable thead .sorting_desc:after{ color:#fff; }
</style>

<div class="container-fluid mt-4 p-4">
  <div class="card shadow border-0 knr-card">
    <div class="card-header text-white py-2 px-3">
      <h5 class="mb-0"><i class="fa fa-hashtag"></i> INVOICE RECEIVED REFERENCE NUMBER</h5>
    </div>
    <div class="card-body p-4">

      <!-- ===== Generate (self-printed Kontra Bon paper) ===== -->
      <div class="border rounded p-3 mb-3" style="background:#f8fafc;">
        <div class="d-flex align-items-end justify-content-between flex-wrap" style="gap:14px;">
          <div>
            <b><i class="fa fa-magic"></i> Generate Reference Number</b>
            <div class="text-muted" style="font-size:12px;">Running format <b>KB/NAG/<?= date('Y') ?>/00001</b> — auto-continue, for self-printed Kontra Bon paper.</div>
          </div>
          <div class="d-flex align-items-end flex-wrap" style="gap:10px;">
            <div>
              <label class="form-label mb-1" style="font-size:12px;">Quantity</label>
              <input type="number" min="1" max="500" class="form-control form-control-sm" id="g_qty" value="50" style="width:110px;">
            </div>
            <div>
              <label class="form-label mb-1" style="font-size:12px;">Description <span class="text-muted">(optional)</span></label>
              <input type="text" class="form-control form-control-sm" id="g_ket" placeholder="Description..." style="width:220px;" autocomplete="off">
            </div>
            <button type="button" class="btn btn-primary btn-sm" id="btnGenerate"><i class="fa fa-magic"></i> Generate</button>
          </div>
        </div>
      </div>

      <div class="row g-3">
        <!-- ===== Manual Input ===== -->
        <div class="col-md-5 mb-3">
          <div class="border rounded p-3 h-100">
            <b><i class="fa fa-pencil"></i> Manual Input</b>
            <div class="mt-3">
              <label class="form-label"><b>Reference Number</b></label>
              <input type="text" class="form-control" id="m_ref" placeholder="e.g. 005424" autocomplete="off">
            </div>
            <div class="mt-2">
              <label class="form-label">Description <span class="text-muted">(optional)</span></label>
              <input type="text" class="form-control" id="m_ket" placeholder="Description..." autocomplete="off">
            </div>
            <button type="button" class="btn btn-primary mt-3" id="btnAdd"><i class="fa fa-plus"></i> Add</button>
          </div>
        </div>

        <!-- ===== Excel Upload ===== -->
        <div class="col-md-7 mb-3">
          <div class="border rounded p-3 h-100">
            <div class="mb-2">
              <b><i class="fa fa-file-excel-o"></i> Excel Upload</b>
            </div>

            <div class="knr-drop" id="dropZone">
              <input type="file" id="fileExcel" accept=".xlsx,.xls,.csv" hidden>
              <div class="dz-ic"><i class="fa fa-cloud-upload"></i></div>
              <div class="dz-title">Drag &amp; drop your Excel here, or <span class="lnk">browse</span></div>
              <div class="dz-sub">.xlsx, .xls, or .csv — first column = reference number</div>
              <div id="fileChip" class="knr-chip" style="display:none;">
                <i class="fa fa-file-excel-o"></i>
                <span id="fileName"></span>
                <span id="fileCount" class="cnt"></span>
                <span class="x" id="fileRemove" title="Remove"><i class="fa fa-times"></i></span>
              </div>
            </div>

            <div class="text-muted mt-2" style="font-size:12px;">
              <b>Column 1</b> = reference number, <b>column 2</b> = description (optional, per row). A header row is skipped automatically.
              If a row has no description in the file, the field below is used. Tip: format the number column as <b>Text</b> in Excel so leading zeros are not lost.
            </div>

            <div class="mt-2">
              <label class="form-label">Description for all rows <span class="text-muted">(used when a row has no description in the file)</span></label>
              <input type="text" class="form-control" id="u_ket" placeholder="Description..." autocomplete="off">
            </div>
            <button type="button" class="btn btn-success mt-2" id="btnUpload" disabled><i class="fa fa-upload"></i> Upload &amp; Save</button>
            <button type="button" class="btn btn-secondary mt-2" id="btnTemplate"><i class="fa fa-download"></i> Template</button>
          </div>
        </div>
      </div>

      <hr>

      <div class="d-flex align-items-end mb-2 flex-wrap" style="gap:12px;">
        <b class="mb-1">Reference Number List</b>
        <span class="knr-badge avail mb-1">Available: <?= $cntAvail ?></span>
        <span class="knr-badge used mb-1">Used: <?= $cntUsed ?></span>
        <span class="knr-badge cancel mb-1">Cancel: <?= $cntCancel ?></span>
        <span style="flex:1;"></span>
        <!-- Print rentang lembar bon (KB/NAG/<tahun>) -->
        <div><label class="mb-1" style="font-size:11px;">Print From #</label><input type="number" min="1" id="p_from" class="form-control form-control-sm" style="width:95px;" placeholder="1"></div>
        <div><label class="mb-1" style="font-size:11px;">To #</label><input type="number" min="1" id="p_to" class="form-control form-control-sm" style="width:95px;" placeholder="50"></div>
        <button type="button" class="btn btn-outline-primary btn-sm mb-1" id="btnPrintRange"><i class="fa fa-print"></i> Print Bon <?= date('Y') ?></button>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover table-sm" id="tblRef" style="width:100%; table-layout:fixed;">
          <thead class="table-gradient">
            <tr>
              <th style="width:4%;">No</th>
              <th style="width:12%;">Reference Number</th>
              <th style="width:16%;">Invoice Received No</th>
              <th style="width:28%;">Description</th>
              <th style="width:10%;">Status</th>
              <th style="width:10%;">Created By</th>
              <th style="width:12%;">Created Date</th>
              <th style="width:8%;">Action</th>
            </tr>
          </thead>
          <tbody>
          <?php $no = 1; while ($r = mysqli_fetch_assoc($listRes)) { ?>
            <tr>
              <td class="text-center"><?= $no++ ?></td>
              <td><b><?= htmlspecialchars($r['ref_number']) ?></b></td>
              <td class="text-center"><?= !empty($r['kontrabon_no']) ? '<b>' . htmlspecialchars($r['kontrabon_no']) . '</b>' : '<span class="text-muted">-</span>' ?></td>
              <td class="desc-cell"><?= htmlspecialchars($r['keterangan'] ?? '') ?></td>
              <td class="text-center">
                <?php if ($r['status'] === 'Used') { ?>
                  <span class="knr-badge used">Used</span>
                <?php } elseif ($r['status'] === 'Cancel') { ?>
                  <span class="knr-badge cancel">Cancel</span>
                <?php } else { ?>
                  <span class="knr-badge avail">Available</span>
                <?php } ?>
              </td>
              <td class="text-center"><?= htmlspecialchars($r['create_user'] ?? '') ?></td>
              <td class="text-center"><?= !empty($r['create_date']) ? date('d-M-Y H:i', strtotime($r['create_date'])) : '-' ?></td>
              <td class="text-center">
                <?php if ($r['status'] === 'Available') { ?>
                  <button class="btn-cancel" data-id="<?= (int) $r['id'] ?>" data-ref="<?= htmlspecialchars($r['ref_number']) ?>" title="Cancel"><i class="fa fa-ban"></i> Cancel</button>
                <?php } else { ?>
                  <span class="text-muted">-</span>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<!-- Library dimuat per-halaman (header.php TIDAK memuatnya) - urut: jQuery dulu. -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script language="JavaScript" src="../css/4.1.1/xlsx.full.min.js"></script>
<script>
$(function () {
  if ($.fn && $.fn.DataTable) {
    try { $('#tblRef').DataTable({ order: [[1,'asc']], pageLength: 10, lengthMenu: [10,25,50,100], autoWidth: false }); } catch (e) {}
  }

  var ACTION = 'kontrabon_new_ref_action.php';
  var parsedItems = [];

  // ===== Generate running numbers (self-printed bon) =====
  $('#btnGenerate').on('click', function () {
    var qty = parseInt($('#g_qty').val(), 10) || 0;
    var ket = $('#g_ket').val() || '';
    if (qty < 1)   { Swal.fire({ icon: 'warning', title: 'Quantity?', text: 'Enter a quantity of at least 1.' }); return; }
    if (qty > 500) { Swal.fire({ icon: 'warning', title: 'Too many', text: 'Maximum 500 numbers per generate.' }); return; }
    Swal.fire({
      icon: 'question', title: 'Generate ' + qty + ' number(s)?',
      text: 'They will be created as Available and ready to print.',
      showCancelButton: true, confirmButtonText: 'Yes, generate', cancelButtonText: 'Cancel'
    }).then(function (r) {
      if (!r.isConfirmed) return;
      $.post(ACTION, { action: 'generate', qty: qty, keterangan: ket }, function (res) {
        if (res && res.ok) {
          Swal.fire({ icon: 'success', title: 'Generated', html: res.msg + '<br><br>Print these sheets now?',
            showCancelButton: true, confirmButtonText: '\u{1F5A8} Print', cancelButtonText: 'Later' }).then(function (rr) {
            if (rr.isConfirmed) { window.open('print_kontrabon_ref.php?year=' + res.year + '&from=' + res.from_num + '&to=' + res.to_num, '_blank'); }
            location.reload();
          });
        } else {
          Swal.fire({ icon: 'error', title: 'Failed', text: (res && res.msg) || 'Failed to generate.' });
        }
      }, 'json').fail(function () { Swal.fire({ icon: 'error', title: 'Error', text: 'Server error.' }); });
    });
  });

  // Print rentang lembar bon (KB/NAG/<tahun>/NNNNN)
  $('#btnPrintRange').on('click', function () {
    var f = parseInt($('#p_from').val(), 10) || 0;
    var t = parseInt($('#p_to').val(), 10) || 0;
    if (f < 1 || t < f) { Swal.fire({ icon: 'warning', title: 'Range?', text: 'Enter valid From/To numbers (To must be ≥ From).' }); return; }
    window.open('print_kontrabon_ref.php?year=<?= date('Y') ?>&from=' + f + '&to=' + t, '_blank');
  });

  /* ===== Manual input ===== */
  $('#btnAdd').on('click', function () {
    var ref = ($('#m_ref').val() || '').trim();
    var ket = ($('#m_ket').val() || '').trim();
    if (ref === '') { Swal.fire({icon:'warning', title:'Oops...', text:'Reference number is empty.'}); return; }
    $.post(ACTION, { action:'add', ref_number: ref, keterangan: ket }, function (res) {
      if (res.ok) {
        Swal.fire({icon:'success', title:'Success!', text: res.msg, timer:1200, showConfirmButton:false})
          .then(function(){ window.location.reload(); });
      } else { Swal.fire({icon:'error', title:'Failed', text: res.msg}); }
    }, 'json').fail(function(){ Swal.fire({icon:'error', title:'Error', text:'Failed to reach the server.'}); });
  });
  $('#m_ref').on('keypress', function(e){ if (e.which === 13) $('#btnAdd').click(); });

  /* ===== Download template (SheetJS -> Blob download; robust across versions) =====
     XLSX.writeFile bisa gagal diam-diam di sebagian versi/browser, jadi kita
     generate array buffer lalu download manual lewat <a download>. */
  $('#btnTemplate').on('click', function () {
    try {
      if (typeof XLSX === 'undefined') {
        Swal.fire({icon:'error', title:'Library not loaded', text:'Excel library (XLSX) is not loaded yet. Please reload the page.'});
        return;
      }
      var aoa = [['Reference Number','Description'], ['005424',''], ['005425',''], ['005426','']];
      var ws = XLSX.utils.aoa_to_sheet(aoa);
      ws['!cols'] = [{ wch: 24 }, { wch: 30 }];
      var wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, 'Template');
      var out = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
      var blob = new Blob([out], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url; a.download = 'kontrabon_reference_template.xlsx';
      document.body.appendChild(a); a.click();
      setTimeout(function(){ document.body.removeChild(a); URL.revokeObjectURL(url); }, 200);
    } catch (err) {
      Swal.fire({icon:'error', title:'Template error', text: String(err)});
    }
  });

  /* ===== Dropzone (click + drag & drop) ===== */
  var $dz = $('#dropZone'), fileInput = document.getElementById('fileExcel');
  $dz.on('click', function (e) { if (!$(e.target).closest('#fileChip').length) fileInput.click(); });
  $dz.on('dragover', function (e){ e.preventDefault(); $dz.addClass('drag'); });
  $dz.on('dragleave dragend', function (){ $dz.removeClass('drag'); });
  $dz.on('drop', function (e){
    e.preventDefault(); $dz.removeClass('drag');
    var files = e.originalEvent.dataTransfer.files;
    if (files && files.length) handleFile(files[0]);
  });
  $(fileInput).on('change', function(){ if (this.files.length) handleFile(this.files[0]); });
  $('#fileRemove').on('click', function(e){ e.stopPropagation(); resetFile(); });

  function resetFile(){ parsedItems = []; fileInput.value = ''; $('#fileChip').hide(); $('#btnUpload').prop('disabled', true); }

  function handleFile(file){
    var reader = new FileReader();
    reader.onload = function (e) {
      try {
        var data = new Uint8Array(e.target.result);
        var wb = XLSX.read(data, { type: 'array' });
        var ws = wb.Sheets[wb.SheetNames[0]];
        var rows = XLSX.utils.sheet_to_json(ws, { header: 1, raw: false, defval: '' });
        // col 1 = reference number, col 2 = description (optional, per row)
        if (rows.length && rows[0][0] != null && !/\d/.test(String(rows[0][0]))) { rows.shift(); } // drop header row
        parsedItems = rows.map(function (r) {
          return { n: (r && r[0] != null) ? String(r[0]).trim() : '', d: (r && r[1] != null) ? String(r[1]).trim() : '' };
        }).filter(function (x) { return x.n !== ''; });
      } catch (err) { Swal.fire({icon:'error', title:'Failed to read file', text: String(err)}); resetFile(); return; }
      if (parsedItems.length === 0) { Swal.fire({icon:'warning', title:'Oops...', text:'No number could be read from the file.'}); resetFile(); return; }
      $('#fileName').text(file.name);
      $('#fileCount').text(parsedItems.length + ' number' + (parsedItems.length>1?'s':''));
      $('#fileChip').css('display','inline-flex');
      $('#btnUpload').prop('disabled', false);
    };
    reader.readAsArrayBuffer(file);
  }

  /* ===== Upload (bulk) ===== */
  $('#btnUpload').on('click', function () {
    if (parsedItems.length === 0) { Swal.fire({icon:'warning', title:'Oops...', text:'Please choose an Excel file first.'}); return; }
    var ket = ($('#u_ket').val() || '').trim();
    Swal.fire({
      title: 'Upload ' + parsedItems.length + ' number(s)?',
      html: 'Numbers that are already registered will be skipped automatically.',
      icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Save', cancelButtonText: 'Cancel'
    }).then(function (result) {
      if (!result.isConfirmed) return;
      Swal.fire({ title:'Saving...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });
      $.post(ACTION, { action:'bulk', items: JSON.stringify(parsedItems), keterangan: ket }, function (res) {
        if (res.ok) { Swal.fire({icon:'success', title:'Done', text: res.msg}).then(function(){ window.location.reload(); }); }
        else { Swal.fire({icon:'error', title:'Failed', text: res.msg}); }
      }, 'json').fail(function(){ Swal.fire({icon:'error', title:'Error', text:'Failed to reach the server.'}); });
    });
  });

  /* ===== Cancel (Available only) - soft cancel: status -> Cancel, row kept ===== */
  $('#tblRef').on('click', '.btn-cancel', function () {
    var id = $(this).data('id'), ref = $(this).data('ref');
    Swal.fire({
      title: 'Cancel number ' + ref + '?',
      text: 'It will be marked as Cancel and can no longer be used.',
      icon: 'warning', showCancelButton: true,
      confirmButtonText: 'Yes, cancel it', cancelButtonText: 'No', confirmButtonColor: '#dc2626'
    }).then(function (result) {
      if (!result.isConfirmed) return;
      $.post(ACTION, { action:'cancel', id: id }, function (res) {
        if (res.ok) { Swal.fire({icon:'success', title:'Cancelled', text: res.msg, timer:1200, showConfirmButton:false}).then(function(){ window.location.reload(); }); }
        else { Swal.fire({icon:'error', title:'Failed', text: res.msg}); }
      }, 'json').fail(function(){ Swal.fire({icon:'error', title:'Error', text:'Failed to reach the server.'}); });
    });
  });
});
</script>
