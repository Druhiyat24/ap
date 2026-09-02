<?php include '../header.php' ?>
<?php
// ============================================================================
// CREATE KONTRABON (Kontrabon New).
// Header + tabel invoice. Tiap baris invoice bisa di-expand untuk mengisi
// Faktur & BPB via SCAN. 1 invoice -> banyak faktur (punya tgl_faktur) ->
// banyak BPB (tampil detail: No BPB / Tgl BPB / Total). BPB DIVALIDASI ke
// bpb_new: hanya untuk SUPPLIER terpilih (kontrabon_new_check_bpb.php), dan
// endpoint itu mengembalikan tgl_bpb + total. Save all-or-nothing (transaksi)
// + bulk insert BPB. Styling BIRU khas app. Bootstrap 4.5 -> ml-/mr-.
// ============================================================================

// Preview nomor: MELANJUT dari urutan Invoice Received (IR/NAG/YYYY/MM/NNNNN).
// (Nomor final di-generate ulang saat Save agar otoritatif.)
$rowMx = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT MAX(CAST(SUBSTRING_INDEX(doc_number, '/', -1) AS UNSIGNED)) mx FROM ir_invoice_supp_h"));
$urut  = ((int) ($rowMx['mx'] ?? 0)) + 1;
$docNumber = "IR/NAG/" . date('Y') . "/" . date('m') . "/" . str_pad($urut, 5, '0', STR_PAD_LEFT);
$unikCode  = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789'), 0, 25);
?>

<style>
.kb-card .card-header{ background: linear-gradient(90deg, #191970, #1e90ff); }
.table-gradient th{ background:#1E3A8A; color:#fff; text-align:center; vertical-align:middle; white-space:nowrap; }
/* Dropdown Supplier/No Reff (bootstrap-select) tampil DI ATAS tabel & Descriptions. */
.bootstrap-select .dropdown-menu{ z-index:1060 !important; }
.bootstrap-select.show{ z-index:1060; }
.bootstrap-select .dropdown-menu.inner{ z-index:auto !important; }
#tblInv > tbody > tr > td{ vertical-align:middle; }
#tblInv input{ font-size:13px; }
.kb-total{ font-size:15px; font-weight:700; text-align:right; }
.btn-xs{ padding:2px 8px; font-size:11px; line-height:1.4; border-radius:6px; }
.inv-detail > td{ background:#eef4ff; padding:14px 16px; }
.fb-scan{ display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.fb-scan-lbl{ font-size:12px; font-weight:700; color:#1e3a8a; white-space:nowrap; }
.fb-scan input.scan-faktur, .fb-scan input.scan-bpb{ max-width:340px; border-color:#93c5fd; }
.fb-scan input:focus{ border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
.retur-row{ background:#fef2f2 !important; }
.retur-row .text-right{ color:#b91c1c; font-weight:600; }
.fb-empty{ font-size:12px; font-style:italic; color:#94a3b8; }
.faktur-item{ background:#fff; border:1px solid #dbe4f3; border-left:3px solid #2563eb; border-radius:8px; padding:10px 12px; margin-bottom:10px; }
.faktur-head{ display:flex; align-items:center; gap:8px; margin-bottom:8px; flex-wrap:wrap; }
.faktur-title{ font-size:13px; color:#0f172a; } .faktur-title b{ color:#1e3a8a; }
.faktur-meta{ display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:10px 24px; margin:2px 0 10px; padding:10px 14px; background:#f8fafc; border:1px solid #e6ebf3; border-radius:8px; }
.faktur-meta > div{ display:flex; flex-direction:column; min-width:0; }
.fm-lbl{ font-size:10px; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8; margin-bottom:2px; }
.fm-val{ font-size:13px; font-weight:600; color:#0f172a; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.fm-val.num{ text-align:left; }
.bpb-table{ font-size:12px; margin-bottom:6px; }
.bpb-table thead th{ background:#f1f5f9; color:#334155; border-bottom:2px solid #e2e8f0; padding:5px 8px; }
.bpb-table tbody td{ vertical-align:middle; padding:5px 8px; }
.fak-subtotal{ color:#1e3a8a; }
.fb-count{ font-size:11px; }
.inv-input{ background:#eef4ff; border:1px solid #dbe4f3; border-radius:10px; padding:12px 14px 4px; }
.inv-input .form-label{ font-size:12px; color:#1e3a8a; }
#inv-gate-note{ margin-top:2px; }
#tblInv td{ vertical-align:middle; height:46px; }
#tblInv td.inv-noc{ padding-left:14px; }
.inv-noc i{ color:#1e3a8a; margin-right:8px; }
.inv-noc .inv-num{ font-weight:700; color:#0f172a; letter-spacing:.02em; }
.inv-amtc{ padding-right:14px !important; font-weight:600; color:#0f172a; }
.toggle-detail{ border-radius:50%; width:26px; height:26px; padding:0; line-height:1; }
.fb-count{ font-size:11px; font-weight:600; }
/* ---- BPB detail modal (modern) ---- */
#bpbDetailModal .modal-content{ border:0; border-radius:16px; overflow:hidden; box-shadow:0 24px 70px rgba(15,23,42,.28); }
#bpbDetailModal .modal-header{ border:0; padding:16px 24px; align-items:center; }
#bpbDetailModal .modal-title{ font-weight:700; letter-spacing:.02em; font-size:20px; }
#bpbDetailModal .modal-header .close{ opacity:.9; text-shadow:none; font-size:22px; }
#bpbDetailModal .modal-body{ padding:22px 24px 24px; }
.bpb-info{ display:grid; grid-template-columns:1fr 1fr; gap:0 28px; background:#f8fafc; border:1px solid #e8edf5; border-radius:12px; padding:12px 20px; margin-bottom:18px; }
.bpb-info > div{ padding:7px 0; }
.bpb-info .lbl{ display:block; font-size:10px; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; margin-bottom:1px; }
.bpb-info .val{ display:block; font-size:13.5px; font-weight:600; color:#0f172a; }
.bpb-detail-table{ width:100%; border-collapse:separate; border-spacing:0; font-size:12.5px; margin-bottom:18px; }
.bpb-detail-table thead th{ background:#1E3A8A; color:#fff; font-weight:600; padding:9px 12px; text-align:center; white-space:nowrap; }
.bpb-detail-table thead th:first-child{ border-top-left-radius:10px; }
.bpb-detail-table thead th:last-child{ border-top-right-radius:10px; }
.bpb-detail-table tbody td{ padding:9px 12px; border-bottom:1px solid #eef2f7; }
.bpb-detail-table tbody tr:last-child td{ border-bottom:0; }
.bpb-detail-table tbody tr:hover{ background:#f8fafc; }
.bpb-totals{ max-width:320px; margin-left:auto; padding:4px 4px 0; }
.bpb-totals .row-t{ display:flex; justify-content:space-between; align-items:baseline; padding:6px 0; font-size:13px; color:#64748b; }
.bpb-totals .row-t > span:last-child{ font-variant-numeric:tabular-nums; color:#334155; font-weight:600; }
.bpb-totals .row-t.total{ border-top:2px solid #e5eaf2; margin-top:6px; padding-top:12px; font-size:15px; color:#0f172a; font-weight:700; }
.bpb-totals .row-t.total .amt{ color:#1E3A8A; font-size:18px; font-weight:800; }
</style>

<div class="container-fluid mt-4 p-4">
  <div class="card shadow border-0 kb-card">
    <div class="card-header text-white py-2 px-3">
      <h5 class="mb-0"><i class="fa fa-file-text-o"></i> CREATE INVOICE RECEIVED</h5>
    </div>
    <div class="card-body p-4">

      <!-- ===== Header ===== -->
      <div class="row g-3">
        <div class="col-md-3 mb-2">
          <label class="form-label"><b>Document Number</b></label>
          <input type="text" readonly class="form-control" id="no_doc" value="<?= htmlspecialchars($docNumber) ?>" style="background:#eef2f7;">
          <input type="hidden" id="unik_code" value="<?= htmlspecialchars($unikCode) ?>">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label"><b>Actual Document Receiving Date</b></label>
          <input type="text" class="form-control tanggal" id="tgl_doc" value="<?= date('d-m-Y') ?>" autocomplete="off">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label"><b>Invoice Received Date</b> <span class="text-muted" style="font-size:11px;">(auto)</span></label>
          <input type="text" class="form-control" id="tgl_received" value="" readonly style="background:#eef2f7;" autocomplete="off" title="Otomatis dari Actual Document Receiving Date — tidak bisa diubah">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label"><b>No Reff</b> <span class="text-muted" style="font-size:11px;">(reference)</span></label>
          <select class="form-control selectpicker" id="no_reff" data-live-search="true" title="Select reference number">
            <?php
            $rf = mysqli_query($conn2, "SELECT ref_number FROM ir_kontrabon_ref WHERE status = 'Available' ORDER BY ref_number ASC");
            while ($x = mysqli_fetch_assoc($rf)) echo '<option value="' . htmlspecialchars($x['ref_number']) . '">' . htmlspecialchars($x['ref_number']) . '</option>';
            ?>
          </select>
        </div>
        <div class="col-md-3 mb-2">
        </div>
        <div class="col-md-5 mb-2">
          <label class="form-label"><b>Descriptions</b></label>
          <textarea class="form-control" id="pesan" rows="1" placeholder="descriptions..."></textarea>
        </div>
          <div class="col-md-4 mb-2">
          <label class="form-label"><b>Supplier</b></label>
          <select class="form-control selectpicker" id="nama_supp" data-live-search="true" title="Select Supplier">
            <?php
            $sp = mysqli_query($conn1, "SELECT DISTINCT Supplier sup FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
            while ($x = mysqli_fetch_assoc($sp)) echo '<option value="' . htmlspecialchars($x['sup']) . '">' . htmlspecialchars($x['sup']) . '</option>';
            ?>
          </select>
        </div>
      </div>

      <hr>

      <!-- ===== Add Invoice (single input; a row forms on Add) ===== -->
      <div class="inv-input">
        <div class="row g-2 align-items-end">
          <div class="col-md-3 mb-2">
            <label class="form-label mb-1"><b>No Invoice</b></label>
            <input type="text" class="form-control form-control-sm" id="new_inv_no" placeholder="No Invoice" autocomplete="off" disabled>
          </div>
          <div class="col-md-2 mb-2">
            <label class="form-label mb-1"><b>Invoice Date</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="new_inv_date" value="<?= date('d-m-Y') ?>" autocomplete="off" disabled>
          </div>
          <div class="col-md-2 mb-2">
            <label class="form-label mb-1"><b>Amount</b></label>
            <input type="text" class="form-control form-control-sm text-right" id="new_inv_amt" placeholder="0" autocomplete="off" disabled>
          </div>
          <div class="col-md-1 mb-2">
            <button type="button" class="btn btn-primary btn-sm" id="btnAddRow" disabled><i class="fa fa-plus"></i> Add Invoice</button>
          </div>
        </div>
        <div id="inv-gate-note" class="text-danger" style="font-size:12px;"><i class="fa fa-info-circle"></i> Please select a Supplier first before adding invoices.</div>
      </div>

      <!-- ===== Invoice list ===== -->
      <div class="table-responsive mt-3">
        <table class="table table-bordered table-hover table-sm" id="tblInv" style="width:100%; table-layout:fixed;">
          <thead class="table-gradient">
            <tr>
              <th style="width:4%;"></th>
              <th style="width:32%; text-align:left; padding-left:14px;">No Invoice</th>
              <th style="width:16%;">Invoice Date</th>
              <th style="width:20%; text-align:right; padding-right:14px;">Amount</th>
              <th style="width:18%;">Faktur / BPB</th>
              <th style="width:10%;">Del</th>
            </tr>
          </thead>
          <tbody id="invBody"></tbody>
        </table>
      </div>
      <div class="fb-empty" id="invEmpty" style="margin-bottom:12px;">No invoice yet — fill the form above and click Add Invoice.</div>

      <div class="row">
        <div class="col-md-12">
          <div class="d-flex align-items-center justify-content-end" style="gap:10px;">
            <label class="form-label mb-0"><b>Total Amount</b></label>
            <input type="text" class="form-control kb-total" id="total_value" value="0.00" readonly style="background:#eef2f7; max-width:220px;">
            <input type="hidden" id="total_value_h" value="0">
          </div>
        </div>
      </div>

      <hr>
      <button type="button" class="btn btn-success" id="btnSave"><i class="fa fa-floppy-o"></i> Save</button>
      <button type="button" class="btn btn-danger" onclick="location.href='kontrabon_new.php'"><i class="fa fa-angle-double-left"></i> Back</button>

    </div>
  </div>
</div>

<!-- ===== BPB detail modal ===== -->
<div class="modal fade" id="bpbDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h4 class="modal-title mb-0" id="bpbDetailTitle"></h4>
        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body" id="bpbDetailBody"></div>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script>
$(function () {
  $('.selectpicker').selectpicker({ size: 5 });
  function bindDate($s){ ($s || $(document)).find('.tanggal').datepicker({ format:'dd-mm-yyyy', autoclose:true }); }
  bindDate();

  // ===== Invoice Received Date SELALU hari RABU =====
  // Saat diisi/diganti -> snap ke Rabu pertama pada/-sesudah tanggal itu.
  // (Sudah Rabu -> tetap; lewat Rabu -> Rabu berikutnya.)
  function snapToWednesday(dmy){
    var p = String(dmy || '').split('-'); if (p.length !== 3) return dmy;
    var d = new Date(+p[2], +p[1] - 1, +p[0]); if (isNaN(d.getTime())) return dmy;
    var add = (3 - d.getDay() + 7) % 7;   // getDay: 0=Min..3=Rabu..6=Sab
    if (add) d.setDate(d.getDate() + add);
    return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
  }
  // Invoice Received Date (readonly) = Rabu dari Document Date. Update tiap Document Date berubah.
  function updateReceivedDate(){ $('#tgl_received').val(snapToWednesday($('#tgl_doc').val())); }
  $('#tgl_doc').on('changeDate change', function(){ updateReceivedDate(); });
  updateReceivedDate();   // set nilai awal saat load
  function money(a){ a = (parseFloat(a) || 0).toFixed(2); return a.replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
  function esc(s){ return String(s == null ? '' : s).replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
  var MON = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  function fmtDate(s){ if(!s) return '-'; var p=String(s).split('-'); if(p.length!==3) return s; return p[2]+'-'+MON[(+p[1]-1)]+'-'+p[0]; } // YYYY-MM-DD -> DD-Mon-YYYY
  function fmtDateDMY(s){ if(!s) return '-'; var p=String(s).split('-'); if(p.length!==3) return s; return p[0]+'-'+(MON[(+p[1]-1)]||p[1])+'-'+p[2]; } // DD-MM-YYYY -> DD-Mon-YYYY
  function idr(s){ s=String(s==null?'':s).trim(); if(s==='') return 0; return parseFloat(s.replace(/\./g,'').replace(/,/g,'.')) || 0; } // "97.822.450,00" -> 97822450
  function parseFaktur(raw){ // barcode faktur pajak dipisah '#'
    var p = String(raw).split('#');
    if (p.length >= 10) return {
      nama_supplier:p[0].trim(), npwp_supplier:p[1].trim(), pembeli:p[2].trim(), npwp_pembeli:p[3].trim(),
      no_faktur:p[4].trim(), tgl_faktur:p[5].trim(), dpp:idr(p[6]), ppn:idr(p[7]), ppnbm:idr(p[8]), status:p[9].trim()
    };
    return { nama_supplier:'', npwp_supplier:'', pembeli:'', npwp_pembeli:'', no_faktur:String(raw).trim(), tgl_faktur:'', dpp:0, ppn:0, ppnbm:0, status:'' };
  }

  /* ---------- HTML builders ---------- */
  function fakturHtml(f){
    f = f || {};
    var st = String(f.status || '').toUpperCase();
    var stCls = st === 'APPROVED' ? 'badge-success' : (st ? 'badge-secondary' : '');
    return '<div class="faktur-item">' +
      '<div class="faktur-head">' +
        '<span class="faktur-title"><i class="fa fa-file-text-o"></i> Faktur: <b>' + esc(f.no_faktur) + '</b></span>' +
        (st ? '<span class="badge ' + stCls + ' ml-2">' + esc(st) + '</span>' : '') +
        '<button type="button" class="btn btn-xs btn-outline-danger del-faktur ml-auto"><i class="fa fa-times"></i> Remove</button>' +
        '<input type="hidden" class="fak-no" value="' + esc(f.no_faktur) + '">' +
        '<input type="hidden" class="fak-date" value="' + esc(f.tgl_faktur || '') + '">' +
        '<input type="hidden" class="fak-supp" value="' + esc(f.nama_supplier || '') + '">' +
        '<input type="hidden" class="fak-npwp-supp" value="' + esc(f.npwp_supplier || '') + '">' +
        '<input type="hidden" class="fak-pembeli" value="' + esc(f.pembeli || '') + '">' +
        '<input type="hidden" class="fak-npwp-pembeli" value="' + esc(f.npwp_pembeli || '') + '">' +
        '<input type="hidden" class="fak-dpp" value="' + (parseFloat(f.dpp) || 0) + '">' +
        '<input type="hidden" class="fak-ppn" value="' + (parseFloat(f.ppn) || 0) + '">' +
        '<input type="hidden" class="fak-ppnbm" value="' + (parseFloat(f.ppnbm) || 0) + '">' +
        '<input type="hidden" class="fak-status" value="' + esc(f.status || '') + '">' +
      '</div>' +
      '<div class="faktur-meta">' +
        '<div><span class="fm-lbl">Supplier</span><span class="fm-val" title="' + esc(f.nama_supplier || '-') + '">' + (f.nama_supplier ? esc(f.nama_supplier) : '-') + '</span></div>' +
        '<div><span class="fm-lbl">NPWP Supplier</span><span class="fm-val">' + (f.npwp_supplier ? esc(f.npwp_supplier) : '-') + '</span></div>' +
        '<div><span class="fm-lbl">Tgl Faktur</span><span class="fm-val">' + fmtDateDMY(f.tgl_faktur) + '</span></div>' +
        '<div><span class="fm-lbl">DPP</span><span class="fm-val num">' + money(f.dpp) + '</span></div>' +
        '<div><span class="fm-lbl">PPN</span><span class="fm-val num">' + money(f.ppn) + '</span></div>' +
      '</div>' +
      '<div class="fb-scan"><span class="fb-scan-lbl"><i class="fa fa-barcode"></i> Scan BPB</span>' +
        '<input type="text" class="form-control form-control-sm scan-bpb" placeholder="Scan BPB for this supplier, then Enter"></div>' +
      '<div class="bpb-wrap" style="overflow-x:auto; display:none;">' +
      '<table class="table table-sm table-bordered bpb-table" style="min-width:820px; margin-bottom:6px;"><thead><tr>' +
        '<th style="width:34px;">#</th><th>No BPB</th><th style="width:110px;">Tgl BPB</th><th>No PO</th><th>Supplier</th>' +
        '<th style="width:130px;" class="text-right">DPP</th><th style="width:120px;" class="text-right">PPN</th><th style="width:150px;" class="text-right">Total</th><th style="width:60px;"></th>' +
      '</tr></thead><tbody class="bpb-list"></tbody></table></div>' +
      '<div class="fak-subtotal text-right" style="display:none;">Subtotal: <span class="sub-val">0.00</span></div>' +
    '</div>';
  }
  function bpbRowHtml(o){
    var cur = esc(o.curr||'');
    var ret = !!o.is_retur;   // RETUR (dari bppb_new) -> nilai negatif, ditandai.
    return '<tr class="bpb-row' + (ret ? ' retur-row' : '') + '" data-no="' + esc(o.no_bpb) + '" data-tgl="' + esc(o.tgl_bpb||'') + '"' +
        ' data-po="' + esc(o.no_po||'') + '" data-supp="' + esc(o.supplier||'') + '"' +
        ' data-dpp="' + (parseFloat(o.dpp)||0) + '" data-ppn="' + (parseFloat(o.ppn)||0) + '"' +
        ' data-total="' + (parseFloat(o.total)||0) + '" data-retur="' + (ret ? 1 : 0) + '" data-curr="' + cur + '">' +
      '<td class="idx text-center"></td>' +
      '<td>' + (ret
        ? '<i class="fa fa-undo text-danger"></i> <span class="badge badge-danger" style="font-size:9px;vertical-align:middle;">RETUR</span> '
        : '<i class="fa fa-cube text-primary"></i> ') + esc(o.no_bpb) + '</td>' +
      '<td class="text-center">' + fmtDate(o.tgl_bpb) + '</td>' +
      '<td>' + (o.no_po ? esc(o.no_po) : '-') + '</td>' +
      '<td>' + (o.supplier ? esc(o.supplier) : '-') + '</td>' +
      '<td class="text-right">' + money(o.dpp) + '</td>' +
      '<td class="text-right">' + money(o.ppn) + '</td>' +
      '<td class="text-right">' + money(o.total) + ' ' + cur + '</td>' +
      '<td class="text-center" style="white-space:nowrap;"><i class="fa fa-eye show-bpb" title="Show detail" style="cursor:pointer;color:#2563eb;margin-right:10px;"></i><i class="fa fa-times del-bpb" title="Remove" style="cursor:pointer;color:#dc2626;"></i></td>' +
    '</tr>';
  }
  function rowPairHtml(d){
    d = d || {};
    return '<tr class="inv-row">' +
      '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-primary toggle-detail" title="Faktur & BPB"><i class="fa fa-plus"></i></button></td>' +
      '<td class="inv-noc"><i class="fa fa-file-text-o"></i><span class="inv-num">' + esc(d.no_inv) + '</span><input type="hidden" class="inv-no" value="' + esc(d.no_inv) + '"></td>' +
      '<td class="text-center">' + fmtDateDMY(d.tgl_inv) + '<input type="hidden" class="inv-date" value="' + esc(d.tgl_inv || '') + '"></td>' +
      '<td class="text-right inv-amtc">' + money(d.amount) + '<input type="hidden" class="inv-amt" value="' + (parseFloat(d.amount) || 0) + '"></td>' +
      '<td class="text-center"><span class="badge badge-info fb-count">0 faktur</span></td>' +
      '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger del-inv"><i class="fa fa-trash"></i></button></td>' +
    '</tr>' +
    '<tr class="inv-detail" style="display:none;"><td colspan="6">' +
      '<div class="fb-scan"><span class="fb-scan-lbl"><i class="fa fa-barcode"></i> Scan Faktur</span>' +
        '<input type="text" class="form-control form-control-sm scan-faktur" placeholder="Scan or type Faktur, then Enter"></div>' +
      '<div class="faktur-list"></div>' +
      '<div class="fb-empty">No faktur yet — scan a faktur above.</div>' +
    '</td></tr>';
  }

  function updateCount($detail){
    var n = $detail.find('.faktur-item').length;
    $detail.prev('.inv-row').find('.fb-count').text(n + ' faktur');
    $detail.find('.fb-empty').toggle(n === 0);
  }
  function refreshFaktur($item){
    var $rows = $item.find('.bpb-row');
    $item.find('.bpb-wrap').toggle($rows.length > 0);
    $item.find('.fak-subtotal').toggle($rows.length > 0);
    var sub = 0, curr = '';
    $rows.each(function (i) { $(this).find('.idx').text(i + 1); sub += parseFloat($(this).data('total')) || 0; if (!curr) curr = $(this).data('curr'); });
    $item.find('.sub-val').text(money(sub) + (curr ? (' ' + curr) : ''));
  }
  function addRow(d){ var $pair = $(rowPairHtml(d)); $('#invBody').append($pair); updateInvEmpty(); return $pair; }
  function updateInvEmpty(){ $('#invEmpty').toggle($('#invBody .inv-row').length === 0); }
  function refreshInvInputState(){
    var has = !!$('#nama_supp').val();
    $('#new_inv_no, #new_inv_date, #new_inv_amt, #btnAddRow').prop('disabled', !has);
    $('#inv-gate-note').toggle(!has);
  }

  /* ---------- Reservasi BPB: lepas di server saat BPB dibuang ---------- */
  function releaseBpb(no){ if (no) $.post('kontrabon_new_release_bpb.php', { action:'one', no_bpb:String(no) }); }
  function releaseBpbIn($scope){ $scope.find('.bpb-row').each(function(){ releaseBpb($(this).data('no')); }); }

  /* ---------- Draft (staging) auto-save per user ---------- */
  var draftReady = false, draftTimer = null;
  function collectInvoices(){
    var invoices = [];
    $('#invBody .inv-row').each(function () {
      var $row = $(this);
      var fakturs = [];
      $row.next('.inv-detail').find('.faktur-item').each(function () {
        var $fi = $(this);
        var nf = ($fi.find('.fak-no').val() || '').trim();
        if (nf === '') return;
        var bpbs = [];
        $fi.find('.bpb-row').each(function () {
          bpbs.push({
            no_bpb: String($(this).data('no')), tgl_bpb: String($(this).data('tgl')||''),
            no_po: String($(this).data('po')||''), supplier: String($(this).data('supp')||''),
            dpp: parseFloat($(this).data('dpp'))||0, ppn: parseFloat($(this).data('ppn'))||0,
            total: parseFloat($(this).data('total'))||0, curr: String($(this).data('curr')||'')
          });
        });
        fakturs.push({
          no_faktur: nf,
          tgl_faktur: ($fi.find('.fak-date').val() || '').trim(),
          nama_supplier: $fi.find('.fak-supp').val() || '',
          npwp_supplier: $fi.find('.fak-npwp-supp').val() || '',
          pembeli: $fi.find('.fak-pembeli').val() || '',
          npwp_pembeli: $fi.find('.fak-npwp-pembeli').val() || '',
          dpp: parseFloat($fi.find('.fak-dpp').val()) || 0,
          ppn: parseFloat($fi.find('.fak-ppn').val()) || 0,
          ppnbm: parseFloat($fi.find('.fak-ppnbm').val()) || 0,
          status: $fi.find('.fak-status').val() || '',
          bpbs: bpbs
        });
      });
      invoices.push({ no_inv: ($row.find('.inv-no').val() || '').trim(), tgl_inv: ($row.find('.inv-date').val() || '').trim(), amount: parseFloat(($row.find('.inv-amt').val() || '').replace(/,/g,'')) || 0, fakturs: fakturs });
    });
    return invoices;
  }
  function serializeDraft(){
    return { document_date: $('#tgl_doc').val(), kontrabon_date: $('#tgl_received').val(), no_reff: $('#no_reff').val(), nama_supp: $('#nama_supp').val(), pesan: $('#pesan').val(), invoices: collectInvoices() };
  }
  function draftHasContent(d){
    if (d.nama_supp || d.no_reff) return true;
    return (d.invoices||[]).some(function(iv){ return (iv.no_inv||'') !== '' || (iv.amount>0) || (iv.fakturs||[]).length > 0; });
  }
  var DRAFT_KEY = 'kb_new_draft', savedOk = false;
  function draftPack(){ return { unik_code:$('#unik_code').val(), doc_number:$('#no_doc').val(), payload:serializeDraft() }; }
  function saveLocal(){ // INSTAN, anti-refresh (tidak tergantung sesi/jaringan)
    try {
      var dp = draftPack();
      if (draftHasContent(dp.payload)) { dp.ts = Date.now(); localStorage.setItem(DRAFT_KEY, JSON.stringify(dp)); }
      else localStorage.removeItem(DRAFT_KEY);
    } catch (e) {}
  }
  function pushDraft(){ // ke SERVER (resume lintas-PC)
    var dp = draftPack();
    if (!draftHasContent(dp.payload)) { $.post('kontrabon_new_temp_action.php', { action:'clear' }); return; }
    $.post('kontrabon_new_temp_action.php', { action:'save', unik_code:dp.unik_code, doc_number:dp.doc_number, payload:JSON.stringify(dp.payload) });
  }
  function clearDraftAll(){ try { localStorage.removeItem(DRAFT_KEY); } catch (e) {} $.post('kontrabon_new_temp_action.php', { action:'clear' }); }
  function scheduleDraftSave(){ if (!draftReady) return; saveLocal(); clearTimeout(draftTimer); draftTimer = setTimeout(pushDraft, 800); }
  // Flush final saat halaman ditutup/refresh supaya perubahan terakhir tidak hilang.
  $(window).on('beforeunload', function(){
    if (!draftReady || savedOk) return;
    saveLocal();
    try {
      var dp = draftPack();
      if (draftHasContent(dp.payload) && navigator.sendBeacon) {
        var fd = new FormData();
        fd.append('action','save'); fd.append('unik_code', dp.unik_code); fd.append('doc_number', dp.doc_number); fd.append('payload', JSON.stringify(dp.payload));
        navigator.sendBeacon('kontrabon_new_temp_action.php', fd);
      }
    } catch (e) {}
  });

  function restoreDraft(d){
    $('#invBody').empty();
    if (d.document_date) $('#tgl_doc').val(d.document_date);
    else if (d.kontrabon_date) $('#tgl_doc').val(d.kontrabon_date);
    updateReceivedDate();
    if (d.pesan) $('#pesan').val(d.pesan);
    if (d.no_reff)  $('#no_reff').selectpicker('val', d.no_reff);
    if (d.nama_supp) $('#nama_supp').selectpicker('val', d.nama_supp);
    (d.invoices||[]).forEach(function (iv) {
      var $pair = addRow({ no_inv: iv.no_inv, tgl_inv: iv.tgl_inv, amount: iv.amount });
      var $row = $pair.filter('.inv-row');
      var $detail = $row.next('.inv-detail');
      (iv.fakturs||[]).forEach(function (fk) {
        var $f = $(fakturHtml(fk));
        $detail.find('.faktur-list').append($f);
        (fk.bpbs||[]).forEach(function (bp) { $f.find('.bpb-list').append($(bpbRowHtml(bp))); });
        refreshFaktur($f);
      });
      if ((iv.fakturs||[]).length) { $detail.show(); $row.find('.toggle-detail i').removeClass('fa-plus').addClass('fa-minus'); }
      updateCount($detail);
    });
    updateInvEmpty(); calcTotal();
  }

  /* ---------- Scan Faktur ---------- */
  $('#invBody').on('keydown', '.scan-faktur', function (e) {
    if (e.which !== 13) return;
    e.preventDefault();
    var raw = ($(this).val() || '').trim(); if (raw === '') return;
    var f = parseFaktur(raw);
    if (!f.no_faktur) { Swal.fire({icon:'error', title:'Invalid faktur', text:'Could not read the faktur number from the scan.'}); $(this).val(''); return; }
    var $detail = $(this).closest('.inv-detail');
    var dup = false; $('#invBody .fak-no').each(function(){ if (($(this).val()||'') === f.no_faktur) dup = true; });
    if (dup) { Swal.fire({icon:'info', title:'Already added', text:'Faktur ' + f.no_faktur + ' is already used in this invoice received (cannot be scanned twice, even in another invoice).'}); $(this).val(''); return; }
    var $f = $(fakturHtml(f));
    $detail.find('.faktur-list').append($f);
    updateCount($detail);
    $(this).val('');
    $f.find('.scan-bpb').focus();
    scheduleDraftSave();
  });

  /* ---------- Scan BPB (validasi bpb_new per supplier + RESERVE di server) ---------- */
  $('#invBody').on('keydown', '.scan-bpb', function (e) {
    if (e.which !== 13) return;
    e.preventDefault();
    var $inp = $(this);
    var v = ($inp.val() || '').trim(); if (v === '') return;
    var supp = $('#nama_supp').val();
    if (!supp) { Swal.fire({icon:'warning', title:'Select supplier', text:'Please choose the Supplier first.'}); return; }
    var $item = $inp.closest('.faktur-item');
    var dup = false; $('#invBody .bpb-row').each(function(){ if (($(this).data('no')||'') == v) dup = true; });
    if (dup) { Swal.fire({icon:'info', title:'Already scanned', text:'BPB ' + v + ' is already used in this invoice received (cannot be scanned twice, even in another invoice).'}); $inp.val(''); return; }

    $inp.prop('disabled', true);
    $.post('kontrabon_new_check_bpb.php', { no_bpb: v, supplier: supp, unik_code: $('#unik_code').val() }, function (res) {
      $inp.prop('disabled', false);
      if (res.ok) {
        $item.find('.bpb-list').append($(bpbRowHtml(res)));
        refreshFaktur($item);
        $inp.val('').focus();
        scheduleDraftSave();
      } else {
        Swal.fire({icon:'error', title:'BPB rejected', text: res.msg});
        $inp.val('').focus();
      }
    }, 'json').fail(function(){ $inp.prop('disabled', false); Swal.fire({icon:'error', title:'Error', text:'Failed to validate BPB.'}); });
  });

  /* ---------- other events ---------- */
  $('#btnAddRow').on('click', function(){
    if (!$('#nama_supp').val()) { Swal.fire({icon:'warning', title:'Select supplier', text:'Please choose the Supplier first.'}); return; }
    var no  = ($('#new_inv_no').val() || '').trim();
    var dt  = ($('#new_inv_date').val() || '').trim();
    var amt = parseFloat(($('#new_inv_amt').val() || '').replace(/,/g,'')) || 0;
    if (no === '') { Swal.fire({icon:'warning', title:'No Invoice required', text:'Enter the invoice number first.'}); $('#new_inv_no').focus(); return; }
    if (amt < 0) { Swal.fire({icon:'warning', title:'Invalid amount', text:'Amount cannot be negative.'}); $('#new_inv_amt').focus(); return; }
    var dup = false; $('#invBody .inv-no').each(function(){ if (($(this).val()||'') === no) dup = true; });
    if (dup) { Swal.fire({icon:'info', title:'Already added', text:'Invoice ' + no + ' is already in the list.'}); return; }
    var supp = $('#nama_supp').val();
    var $btn = $(this).prop('disabled', true);
    // Cek server: No Invoice belum dipakai untuk supplier yang sama (kecuali kontrabon lama sudah Cancel)
    $.post('kontrabon_new_check_inv.php', { no_inv: no, supplier: supp }, function (res) {
      $btn.prop('disabled', false);
      if (!res.ok) { Swal.fire({icon:'error', title:'Invoice already used', text: res.msg}); $('#new_inv_no').focus(); return; }
      addRow({ no_inv:no, tgl_inv:dt, amount:amt });
      $('#new_inv_no').val(''); $('#new_inv_date').val(''); $('#new_inv_amt').val('');
      calcTotal(); saveLocal(); scheduleDraftSave();
      $('#new_inv_no').focus();
    }, 'json').fail(function(){ $btn.prop('disabled', false); Swal.fire({icon:'error', title:'Error', text:'Failed to validate invoice.'}); });
  });
  $('#invBody').on('click', '.toggle-detail', function(){
    var $row = $(this).closest('.inv-row'), $detail = $row.next('.inv-detail');
    var open = $detail.is(':visible');
    $detail.toggle(!open);
    $(this).find('i').toggleClass('fa-plus fa-minus');
    if (!open) $detail.find('.scan-faktur').focus();
  });
  $('#invBody').on('click', '.del-faktur', function(){
    var $detail = $(this).closest('.inv-detail'), $item = $(this).closest('.faktur-item');
    releaseBpbIn($item); $item.remove(); updateCount($detail); scheduleDraftSave();
  });
  $('#invBody').on('click', '.del-bpb', function(){
    var $item = $(this).closest('.faktur-item'), $r = $(this).closest('.bpb-row');
    releaseBpb($r.data('no')); $r.remove(); refreshFaktur($item); scheduleDraftSave();
  });
  $('#invBody').on('click', '.show-bpb', function(){
    var $r = $(this).closest('.bpb-row');
    var no = String($r.data('no'));
    var supp = $('#nama_supp').val() || String($r.data('supp') || '');
    $('#bpbDetailTitle').text(no);
    $('#bpbDetailBody').html('<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#bpbDetailModal').modal('show');
    $.post('kontrabon_new_bpb_show.php', { no_bpb: no, supplier: supp }, function (html) {
      $('#bpbDetailBody').html(html);
    }).fail(function () { $('#bpbDetailBody').html('<div class="text-danger p-3">Failed to load BPB detail.</div>'); });
  });
  $('#invBody').on('click', '.del-inv', function(){
    var $row = $(this).closest('.inv-row'), $detail = $row.next('.inv-detail');
    releaseBpbIn($detail); $detail.remove(); $row.remove(); updateInvEmpty(); calcTotal(); scheduleDraftSave();
  });
  $('#tgl_doc, #pesan').on('input change', scheduleDraftSave);
  $('#no_reff').on('change', scheduleDraftSave);
  $('#new_inv_date').on('keydown', function(e){ if (e.which === 13) { e.preventDefault(); $('#btnAddRow').click(); } });
  $('#new_inv_amt').on('keydown', function(e){ if (e.which === 13) { e.preventDefault(); $('#btnAddRow').click(); } });

  $('#nama_supp').on('change', function(){
    refreshInvInputState();
    var $rows = $('#invBody .bpb-row');
    if ($rows.length) {
      $rows.each(function(){ releaseBpb($(this).data('no')); });
      $rows.remove();
      $('#invBody .faktur-item').each(function(){ refreshFaktur($(this)); });
      Swal.fire({toast:true, position:'top-end', icon:'info', title:'Supplier changed — scanned BPBs cleared', showConfirmButton:false, timer:2200});
    }
    scheduleDraftSave();
  });

  function calcTotal(){
    var tot = 0;
    $('#invBody .inv-amt').each(function(){ tot += parseFloat(($(this).val()||'').replace(/,/g,'')) || 0; });
    $('#total_value_h').val(tot.toFixed(2));
    $('#total_value').val(money(tot));
  }

  /* ---------- Load draft: server (lintas-PC) dulu, fallback localStorage (anti-refresh) ---------- */
  function localDraft(){ try { var x = JSON.parse(localStorage.getItem(DRAFT_KEY) || 'null'); return (x && x.payload && draftHasContent(x.payload)) ? x : null; } catch (e) { return null; } }
  function startFresh(){ draftReady = true; refreshInvInputState(); updateInvEmpty(); }
  function offerResume(dp){
    Swal.fire({
      icon:'question', title:'Unsaved draft found',
      html:'You have an unsaved invoice received draft' + (dp.update_date ? ' <b>(' + dp.update_date + ')</b>' : '') + '.<br>Continue where you left off?',
      showCancelButton:true, confirmButtonText:'Yes, continue', cancelButtonText:'Discard & start new',
      allowOutsideClick:false
    }).then(function (r) {
      if (r.isConfirmed) {
        if (dp.unik_code)  $('#unik_code').val(dp.unik_code);
        if (dp.doc_number) $('#no_doc').val(dp.doc_number);
        restoreDraft(dp.payload);
      } else {
        clearDraftAll();
        $.post('kontrabon_new_release_bpb.php', { action:'all' });
      }
      draftReady = true;
      refreshInvInputState(); updateInvEmpty();
    });
  }
  $.post('kontrabon_new_temp_action.php', { action:'load' }, function (res) {
    if (res && res.ok && res.draft && res.draft.payload && draftHasContent(res.draft.payload)) {
      offerResume({ unik_code:res.draft.unik_code, doc_number:res.draft.doc_number, payload:res.draft.payload, update_date:res.draft.update_date });
    } else {
      var loc = localDraft();
      if (loc) offerResume(loc); else startFresh();
    }
  }, 'json').fail(function(){ var loc = localDraft(); if (loc) offerResume(loc); else startFresh(); });

  /* ---------- Save ---------- */
  $('#btnSave').on('click', function () {
    var reff = $('#no_reff').val(), supp = $('#nama_supp').val();
    if (!supp) { Swal.fire({icon:'warning', title:'Oops...', text:'Supplier is required.'}); return; }
    if (!reff) { Swal.fire({icon:'warning', title:'Oops...', text:'Reference number (No Reff) is required.'}); return; }

    var invoices = [], bad = [];
    collectInvoices().forEach(function (iv, i) {
      if (iv.no_inv === '' && iv.amount === 0) return;   // baris kosong -> lewati
      if (iv.no_inv === '') bad.push('Invoice ' + (i+1) + ': No Invoice is empty.');
      if (iv.amount < 0)    bad.push('Invoice ' + (i+1) + ': Amount cannot be negative.');
      // Total BPB (dari semua faktur) HARUS sama dengan Amount invoice. Kalau beda -> tidak boleh save.
      var bpbTotal = 0;
      (iv.fakturs || []).forEach(function (fk) { (fk.bpbs || []).forEach(function (bp) { bpbTotal += parseFloat(bp.total) || 0; }); });
      // (Dinonaktifkan atas permintaan) total BPB TIDAK wajib sama dengan Amount invoice — boleh beda.
      // if (Math.round(bpbTotal * 100) !== Math.round((iv.amount || 0) * 100)) {
      //   bad.push('Invoice ' + (i+1) + ' (' + (iv.no_inv || '-') + '): total BPB (' + money(bpbTotal) + ') must equal Amount (' + money(iv.amount) + ').');
      // }
      invoices.push(iv);
    });

    if (bad.length) { Swal.fire({icon:'error', title:'Check the invoices', html: bad.join('<br>')}); return; }
    if (invoices.length === 0) { Swal.fire({icon:'warning', title:'Oops...', text:'Add at least one invoice.'}); return; }

    var nFak = 0, nBpb = 0;
    invoices.forEach(function(iv){ (iv.fakturs||[]).forEach(function(fk){ nFak++; nBpb += (fk.bpbs||[]).length; }); });
    Swal.fire({
      title: 'Save this Invoice Received?',
      html: 'Reference <b>' + reff + '</b> will be marked as <b>Used</b>.<br>' +
            '<small class="text-muted">' + invoices.length + ' invoice(s) · ' + nFak + ' faktur · ' + nBpb + ' BPB</small>',
      icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Save', cancelButtonText: 'Cancel'
    }).then(function (result) {
      if (result.isConfirmed) sendSave(reff, supp, invoices);
    });
  });

  // Save di SERVER = 1 TRANSAKSI all-or-nothing + BULK insert BPB. Kalau ada 1
  // yang gagal -> server rollback total (tidak ada setengah save) & reference
  // tetap Available. Di sini gagal -> tawari user "Save again".
  function sendSave(reff, supp, invoices){
    Swal.fire({ title:'Saving...', html:'Saving ' + invoices.length + ' invoice(s)…', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });
    $.post('kontrabon_new_save.php', {
      doc_number: $('#no_doc').val(), unik_code: $('#unik_code').val(),
      document_date: $('#tgl_doc').val(), kontrabon_date: $('#tgl_received').val(), no_reff: reff, nama_supp: supp,
      pesan: $('#pesan').val(), total_amount: $('#total_value_h').val(),
      invoices: JSON.stringify(invoices)
    }, function (res) {
      if (res.ok) {
        savedOk = true; try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
        Swal.fire({icon:'success', title:'Saved', text: res.msg}).then(function(){ window.location = 'kontrabon_new.php'; });
      } else {
        Swal.fire({ icon:'error', title:'Save failed',
          html: (res.msg || 'Unknown error') + '<br><small class="text-muted">Nothing was saved (rolled back) &amp; the reference is still available. You can try again.</small>',
          showCancelButton:true, confirmButtonText:'Save again', cancelButtonText:'Close', allowOutsideClick:false })
          .then(function (r) { if (r.isConfirmed) sendSave(reff, supp, invoices); });
      }
    }, 'json').fail(function(){
      Swal.fire({ icon:'error', title:'Connection error',
        html: 'Could not reach the server.<br><small class="text-muted">Your draft is safe. You can try again.</small>',
        showCancelButton:true, confirmButtonText:'Save again', cancelButtonText:'Close', allowOutsideClick:false })
        .then(function (r) { if (r.isConfirmed) sendSave(reff, supp, invoices); });
    });
  }
});
</script>
