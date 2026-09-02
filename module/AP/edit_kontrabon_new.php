<?php include '../header.php' ?>
<?php
// ============================================================================
// EDIT KONTRABON (Kontrabon New). Sama seperti Create tapi: data di-prefill dari
// DB, Supplier & No Reff TERKUNCI (readonly), tanpa draft/staging/reservasi.
// Bisa tambah/edit/hapus invoice-faktur-BPB. Simpan -> kontrabon_new_update.php
// (transaksi: hapus detail lama -> insert baru). Keunikan BPB & invoice
// mengecualikan kontrabon ini sendiri (exclude_doc). Bootstrap 4.5 -> ml-/mr-.
// ============================================================================

$doc = $_GET['doc'] ?? '';
$de  = mysqli_real_escape_string($conn2, $doc);
$h   = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT * FROM ir_kontrabon_h WHERE doc_number = '$de' LIMIT 1"));
if (!$h) { echo '<div class="alert alert-danger m-4">Invoice Received not found. <a href="kontrabon_new.php">Back to list</a></div>'; return; }
if (($h['status'] ?? '') === 'Cancel') { echo '<div class="alert alert-warning m-4">Cancelled invoice received cannot be edited. <a href="kontrabon_new.php">Back to list</a></div>'; return; }
$unik = $h['unik_code'];
$ue   = mysqli_real_escape_string($conn2, $unik);

// Bangun payload (invoice -> faktur -> bpb) untuk prefill.
$payload = ['invoices' => []];
$invs = mysqli_query($conn2, "SELECT id, no_inv, tgl_inv, amount FROM ir_kontrabon_inv WHERE unik_code = '$ue' ORDER BY id");
while ($iv = mysqli_fetch_assoc($invs)) {
    $invObj = [
        'no_inv'  => $iv['no_inv'],
        'tgl_inv' => !empty($iv['tgl_inv']) ? date('d-m-Y', strtotime($iv['tgl_inv'])) : '',
        'amount'  => (float) $iv['amount'],
        'fakturs' => [],
    ];
    $faks = mysqli_query($conn2, "SELECT * FROM ir_kontrabon_faktur WHERE inv_id = " . (int) $iv['id'] . " ORDER BY id");
    while ($fk = mysqli_fetch_assoc($faks)) {
        $fakObj = [
            'no_faktur'     => $fk['no_faktur'],
            'tgl_faktur'    => !empty($fk['tgl_faktur']) ? date('d-m-Y', strtotime($fk['tgl_faktur'])) : '',
            'nama_supplier' => $fk['nama_supplier'],
            'npwp_supplier' => $fk['npwp_supplier'],
            'pembeli'       => $fk['pembeli'],
            'npwp_pembeli'  => $fk['npwp_pembeli'],
            'dpp'           => (float) $fk['dpp'],
            'ppn'           => (float) $fk['ppn'],
            'ppnbm'         => (float) $fk['ppnbm'],
            'status'        => $fk['status_faktur'],
            'bpbs'          => [],
        ];
        $bpbs = mysqli_query($conn2, "SELECT * FROM ir_kontrabon_bpb WHERE faktur_id = " . (int) $fk['id'] . " ORDER BY id");
        while ($bp = mysqli_fetch_assoc($bpbs)) {
            $fakObj['bpbs'][] = [
                'no_bpb'   => $bp['no_bpb'],
                'tgl_bpb'  => !empty($bp['tgl_bpb']) ? date('Y-m-d', strtotime($bp['tgl_bpb'])) : '',
                'no_po'    => $bp['no_po'],
                'supplier' => $bp['supplier'],
                'dpp'      => (float) $bp['dpp'],
                'ppn'      => (float) $bp['ppn'],
                'total'    => (float) $bp['total'],
                'curr'     => $bp['curr'],
            ];
        }
        $invObj['fakturs'][] = $fakObj;
    }
    $payload['invoices'][] = $invObj;
}
?>

<style>
.kb-card .card-header{ background: linear-gradient(90deg, #191970, #1e90ff); }
.table-gradient th{ background:#1E3A8A; color:#fff; text-align:center; vertical-align:middle; white-space:nowrap; }
#tblInv > tbody > tr > td{ vertical-align:middle; }
#tblInv input{ font-size:13px; }
.kb-total{ font-size:15px; font-weight:700; text-align:right; }
.btn-xs{ padding:2px 8px; font-size:11px; line-height:1.4; border-radius:6px; }
.inv-detail > td{ background:#eef4ff; padding:14px 16px; }
.fb-scan{ display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.fb-scan-lbl{ font-size:12px; font-weight:700; color:#1e3a8a; white-space:nowrap; }
.fb-scan input.scan-faktur, .fb-scan input.scan-bpb, .fb-scan input.scan-fak-update{ max-width:340px; border-color:#93c5fd; }
.retur-row{ background:#fef2f2 !important; }
.retur-row .text-right{ color:#b91c1c; font-weight:600; }
.fb-scan input:focus{ border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
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
#tblInv td{ vertical-align:middle; height:46px; }
#tblInv td.inv-noc{ padding-left:14px; }
.inv-noc i{ color:#1e3a8a; margin-right:8px; }
.inv-noc .inv-num{ font-weight:700; color:#0f172a; letter-spacing:.02em; }
.inv-amtc{ padding-right:14px !important; font-weight:600; color:#0f172a; }
.toggle-detail{ border-radius:50%; width:26px; height:26px; padding:0; line-height:1; }
.fb-count{ font-size:11px; font-weight:600; }
</style>

<div class="container-fluid mt-4 p-4">
  <div class="card shadow border-0 kb-card">
    <div class="card-header text-white py-2 px-3">
      <h5 class="mb-0"><i class="fa fa-pencil-square-o"></i> EDIT INVOICE RECEIVED</h5>
    </div>
    <div class="card-body p-4">

      <!-- ===== Header ===== -->
      <div class="row g-3">
        <div class="col-md-3 mb-2">
          <label class="form-label"><b>Document Number</b></label>
          <input type="text" readonly class="form-control" id="no_doc" value="<?= htmlspecialchars($h['doc_number']) ?>" style="background:#eef2f7;">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label"><b>Actual Document Receiving Date</b></label>
          <input type="text" class="form-control tanggal" id="tgl_doc" value="<?= !empty($h['document_date']) ? date('d-m-Y', strtotime($h['document_date'])) : (!empty($h['kontrabon_date']) ? date('d-m-Y', strtotime($h['kontrabon_date'])) : date('d-m-Y')) ?>" autocomplete="off">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label"><b>Invoice Received Date</b> <span class="text-muted" style="font-size:11px;">(auto)</span></label>
          <input type="text" class="form-control" id="tgl_received" value="<?= !empty($h['kontrabon_date']) ? date('d-m-Y', strtotime($h['kontrabon_date'])) : '' ?>" readonly style="background:#eef2f7;" autocomplete="off" title="Otomatis dari Actual Document Receiving Date — tidak bisa diubah">
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label"><b>No Reff</b> <span class="text-muted" style="font-size:11px;">(locked)</span></label>
          <input type="text" readonly class="form-control" value="<?= htmlspecialchars($h['no_reff'] ?? '') ?>" style="background:#eef2f7;">
        </div>
        <div class="col-md-3 mb-2">
        </div>
        <div class="col-md-3 mb-2">
          <label class="form-label"><b>Supplier</b> <span class="text-muted" style="font-size:11px;">(locked)</span></label>
          <input type="text" readonly class="form-control" id="nama_supp" value="<?= htmlspecialchars($h['nama_supp'] ?? '') ?>" style="background:#eef2f7;">
        </div>
        <div class="col-md-6 mb-2">
          <label class="form-label"><b>Descriptions</b></label>
          <textarea class="form-control" id="pesan" rows="1" placeholder="descriptions..."><?= htmlspecialchars($h['deskripsi'] ?? '') ?></textarea>
        </div>
      </div>

      <hr>

      <!-- ===== Add Invoice ===== -->
      <div class="inv-input">
        <div class="row g-2 align-items-end">
          <div class="col-md-3 mb-2">
            <label class="form-label mb-1"><b>No Invoice</b></label>
            <input type="text" class="form-control form-control-sm" id="new_inv_no" placeholder="No Invoice" autocomplete="off">
          </div>
          <div class="col-md-2 mb-2">
            <label class="form-label mb-1"><b>Invoice Date</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="new_inv_date" placeholder="dd-mm-yyyy" autocomplete="off">
          </div>
          <div class="col-md-2 mb-2">
            <label class="form-label mb-1"><b>Amount</b></label>
            <input type="text" class="form-control form-control-sm text-right" id="new_inv_amt" placeholder="0" autocomplete="off">
          </div>
          <div class="col-md-1 mb-2">
            <button type="button" class="btn btn-primary btn-sm" id="btnAddRow"><i class="fa fa-plus"></i> Add Invoice</button>
          </div>
        </div>
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
      <button type="button" class="btn btn-success" id="btnSave"><i class="fa fa-floppy-o"></i> Update</button>
      <button type="button" class="btn btn-danger" onclick="location.href='kontrabon_new.php'"><i class="fa fa-angle-double-left"></i> Back</button>

    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script>
var DOC = <?= json_encode($h['doc_number']) ?>;
var PAYLOAD = <?= json_encode($payload) ?>;
$(function () {
  function bindDate($s){ ($s || $(document)).find('.tanggal').datepicker({ format:'dd-mm-yyyy', autoclose:true }); }
  bindDate();

  // ===== Invoice Received Date SELALU hari RABU (snap ke Rabu pertama pada/-sesudah tgl) =====
  function snapToWednesday(dmy){
    var p = String(dmy || '').split('-'); if (p.length !== 3) return dmy;
    var d = new Date(+p[2], +p[1] - 1, +p[0]); if (isNaN(d.getTime())) return dmy;
    var add = (3 - d.getDay() + 7) % 7;
    if (add) d.setDate(d.getDate() + add);
    return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
  }
  // Invoice Received Date (readonly) = Rabu dari Document Date. Update tiap Document Date berubah.
  function updateReceivedDate(){ $('#tgl_received').val(snapToWednesday($('#tgl_doc').val())); }
  $('#tgl_doc').on('changeDate change', function(){ updateReceivedDate(); });
  updateReceivedDate();
  function money(a){ a = (parseFloat(a) || 0).toFixed(2); return a.replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
  function esc(s){ return String(s == null ? '' : s).replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }
  var MON = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  function fmtDate(s){ if(!s) return '-'; var p=String(s).split('-'); if(p.length!==3) return s; return p[2]+'-'+MON[(+p[1]-1)]+'-'+p[0]; }
  function fmtDateDMY(s){ if(!s) return '-'; var p=String(s).split('-'); if(p.length!==3) return s; return p[0]+'-'+(MON[(+p[1]-1)]||p[1])+'-'+p[2]; }

  /* ---------- builders (sama dengan create) ---------- */
  // d-m-Y (atau Y-m-d) -> Y-m-d untuk <input type="date">. Kosong bila tak valid.
  function toYMD(s){
    s = String(s || '').trim(); if (!s) return '';
    var m = s.match(/^(\d{1,2})-(\d{1,2})-(\d{4})$/);
    if (m) return m[3] + '-' + ('0'+m[1]).slice(-2) + '-' + ('0'+m[2]).slice(-2);
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
    return '';
  }
  // Meta faktur (Supplier/NPWP/DPP/PPN) — dipakai fakturHtml & saat update via scan.
  function fakturMetaHtml(f){
    f = f || {};
    return '<div><span class="fm-lbl">No Faktur</span><span class="fm-val fm-no-faktur">' + (f.no_faktur ? esc(f.no_faktur) : '-') + '</span></div>' +
           '<div><span class="fm-lbl">Tgl Faktur</span><span class="fm-val fm-tgl-faktur">' + fmtDateDMY(f.tgl_faktur) + '</span></div>' +
           '<div><span class="fm-lbl">Supplier</span><span class="fm-val" title="' + esc(f.nama_supplier || '-') + '">' + (f.nama_supplier ? esc(f.nama_supplier) : '-') + '</span></div>' +
           '<div><span class="fm-lbl">NPWP Supplier</span><span class="fm-val">' + (f.npwp_supplier ? esc(f.npwp_supplier) : '-') + '</span></div>' +
           '<div><span class="fm-lbl">DPP</span><span class="fm-val num">' + money(f.dpp) + '</span></div>' +
           '<div><span class="fm-lbl">PPN</span><span class="fm-val num">' + money(f.ppn) + '</span></div>';
  }
  // Parse hasil scan faktur (format QR 10 field dipisah '#', atau hanya no faktur).
  function parseFakturScan(raw){
    raw = String(raw || '').trim(); if (!raw) return null;
    var p = raw.split('#'), f;
    if (p.length >= 10) { f = { nama_supplier:p[0].trim(), npwp_supplier:p[1].trim(), pembeli:p[2].trim(), npwp_pembeli:p[3].trim(), no_faktur:p[4].trim(), tgl_faktur:p[5].trim(), dpp:idr(p[6]), ppn:idr(p[7]), ppnbm:idr(p[8]), status:p[9].trim() }; f.full = true; }
    else { f = { no_faktur: raw, tgl_faktur:'', dpp:0, ppn:0, ppnbm:0 }; f.full = false; }
    return f.no_faktur ? f : null;
  }
  // Isi ulang field 1 blok faktur dari data scan. QR LENGKAP (f.full) menimpa semua
  // field; scan nomor saja hanya mengganti No Faktur (+Tgl bila ada), Supplier/DPP/PPN
  // TIDAK diubah supaya tidak terhapus.
  function applyFakturToBlock($item, f){
    $item.find('.fak-no').val(f.no_faktur);
    $item.find('.fm-no-faktur').text(f.no_faktur || '-');
    if (f.tgl_faktur) {
      $item.find('.fak-date').val(f.tgl_faktur);
      $item.find('.fm-tgl-faktur').text(fmtDateDMY(f.tgl_faktur));
    }
    if (f.full) {
      $item.find('.fak-supp').val(f.nama_supplier || '');
      $item.find('.fak-npwp-supp').val(f.npwp_supplier || '');
      $item.find('.fak-pembeli').val(f.pembeli || '');
      $item.find('.fak-npwp-pembeli').val(f.npwp_pembeli || '');
      $item.find('.fak-dpp').val(parseFloat(f.dpp) || 0);
      $item.find('.fak-ppn').val(parseFloat(f.ppn) || 0);
      $item.find('.fak-ppnbm').val(parseFloat(f.ppnbm) || 0);
      if (typeof f.status !== 'undefined') $item.find('.fak-status').val(f.status || '');
      $item.find('.faktur-meta').html(fakturMetaHtml(f));
    }
  }
  function fakturHtml(f){
    f = f || {};
    var st = String(f.status || '').toUpperCase();
    var stCls = st === 'APPROVED' ? 'badge-success' : (st ? 'badge-secondary' : '');
    return '<div class="faktur-item">' +
      '<div class="faktur-head">' +
        '<span class="faktur-title"><i class="fa fa-file-text-o"></i> Faktur</span>' +
        '<span class="fb-scan-lbl ml-3" style="white-space:nowrap;"><i class="fa fa-barcode"></i> Scan update</span>' +
        '<input type="text" class="scan-fak-update form-control form-control-sm ml-2" placeholder="Scan faktur to update this, then Enter" style="max-width:300px;border-color:#f59e0b;" title="Scan untuk memperbarui faktur ini">' +
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
      '<div class="faktur-meta">' + fakturMetaHtml(f) + '</div>' +
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
  function calcTotal(){
    var tot = 0;
    $('#invBody .inv-amt').each(function(){ tot += parseFloat(($(this).val()||'').replace(/,/g,'')) || 0; });
    $('#total_value_h').val(tot.toFixed(2));
    $('#total_value').val(money(tot));
  }
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
          no_faktur: nf, tgl_faktur: ($fi.find('.fak-date').val() || '').trim(),
          nama_supplier: $fi.find('.fak-supp').val() || '', npwp_supplier: $fi.find('.fak-npwp-supp').val() || '',
          pembeli: $fi.find('.fak-pembeli').val() || '', npwp_pembeli: $fi.find('.fak-npwp-pembeli').val() || '',
          dpp: parseFloat($fi.find('.fak-dpp').val()) || 0, ppn: parseFloat($fi.find('.fak-ppn').val()) || 0,
          ppnbm: parseFloat($fi.find('.fak-ppnbm').val()) || 0, status: $fi.find('.fak-status').val() || '', bpbs: bpbs
        });
      });
      invoices.push({ no_inv: ($row.find('.inv-no').val() || '').trim(), tgl_inv: ($row.find('.inv-date').val() || '').trim(), amount: parseFloat(($row.find('.inv-amt').val() || '').replace(/,/g,'')) || 0, fakturs: fakturs });
    });
    return invoices;
  }

  /* ---------- Scan Faktur ---------- */
  $('#invBody').on('keydown', '.scan-faktur', function (e) {
    if (e.which !== 13) return; e.preventDefault();
    var raw = ($(this).val() || '').trim(); if (raw === '') return;
    var p = raw.split('#'), f;
    if (p.length >= 10) f = { nama_supplier:p[0].trim(), npwp_supplier:p[1].trim(), pembeli:p[2].trim(), npwp_pembeli:p[3].trim(), no_faktur:p[4].trim(), tgl_faktur:p[5].trim(), dpp:idr(p[6]), ppn:idr(p[7]), ppnbm:idr(p[8]), status:p[9].trim() };
    else f = { no_faktur: raw.trim(), tgl_faktur:'', dpp:0, ppn:0, ppnbm:0 };
    if (!f.no_faktur) { Swal.fire({icon:'error', title:'Invalid faktur', text:'Could not read the faktur number.'}); $(this).val(''); return; }
    var $detail = $(this).closest('.inv-detail');
    // Faktur strip ("-") = penanda "tanpa faktur", boleh dipakai berkali-kali.
    var isStrip = /^[-\s]*$/.test(f.no_faktur);
    var dup = false;
    if (!isStrip) { $('#invBody .fak-no').each(function(){ if (($(this).val()||'') === f.no_faktur) dup = true; }); }
    if (dup) { Swal.fire({icon:'info', title:'Already added', text:'Faktur ' + f.no_faktur + ' is already used in this invoice received (cannot be scanned twice, even in another invoice).'}); $(this).val(''); return; }
    var $f = $(fakturHtml(f));
    $detail.find('.faktur-list').append($f);
    updateCount($detail); $(this).val(''); $f.find('.scan-bpb').focus();
  });

  /* ---------- Scan UPDATE faktur (perbarui blok faktur yang SUDAH ada) ---------- */
  $('#invBody').on('keydown', '.scan-fak-update', function (e) {
    if (e.which !== 13) return; e.preventDefault();
    var f = parseFakturScan($(this).val());
    if (!f) { Swal.fire({icon:'error', title:'Invalid faktur', text:'Could not read the faktur number.'}); $(this).val(''); return; }
    var $item = $(this).closest('.faktur-item');
    // No Faktur baru tidak boleh bentrok dgn faktur LAIN di invoice yang sama -
    // kecuali strip ("-") yang memang penanda "tanpa faktur", boleh dobel.
    var isStrip = /^[-\s]*$/.test(f.no_faktur);
    var dup = false;
    if (!isStrip) { $('#invBody .faktur-item').not($item).find('.fak-no').each(function(){ if (($(this).val()||'') === f.no_faktur) dup = true; }); }
    if (dup) { Swal.fire({icon:'info', title:'Duplicate', text:'Faktur ' + f.no_faktur + ' is already used in this invoice received (cannot be scanned twice, even in another invoice).'}); $(this).val(''); return; }
    applyFakturToBlock($item, f);
    $(this).val('');
    Swal.fire({ toast:true, position:'top-end', icon:'success', title:'Faktur updated', showConfirmButton:false, timer:1400 });
  });
  // idr helper (dipakai di atas via hoisting)
  function idr(s){ s=String(s==null?'':s).trim(); if(s==='') return 0; return parseFloat(s.replace(/\./g,'').replace(/,/g,'.'))||0; }

  /* ---------- Scan BPB (exclude_doc + no_reserve; cek dobel global) ---------- */
  $('#invBody').on('keydown', '.scan-bpb', function (e) {
    if (e.which !== 13) return; e.preventDefault();
    var $inp = $(this); var v = ($inp.val() || '').trim(); if (v === '') return;
    var supp = $('#nama_supp').val();
    var $item = $inp.closest('.faktur-item');
    var gdup = false; $('#invBody .bpb-row').each(function(){ if (($(this).data('no')||'') == v) gdup = true; });
    if (gdup) { Swal.fire({icon:'info', title:'Already used', text:'BPB ' + v + ' is already in this invoice received.'}); $inp.val(''); return; }
    $inp.prop('disabled', true);
    $.post('kontrabon_new_check_bpb.php', { no_bpb: v, supplier: supp, exclude_doc: DOC, no_reserve: 1 }, function (res) {
      $inp.prop('disabled', false);
      if (res.ok) { $item.find('.bpb-list').append($(bpbRowHtml(res))); refreshFaktur($item); $inp.val('').focus(); }
      else { Swal.fire({icon:'error', title:'BPB rejected', text: res.msg}); $inp.val('').focus(); }
    }, 'json').fail(function(){ $inp.prop('disabled', false); Swal.fire({icon:'error', title:'Error', text:'Failed to validate BPB.'}); });
  });

  /* ---------- Add Invoice (cek server exclude_doc) ---------- */
  $('#btnAddRow').on('click', function(){
    var no  = ($('#new_inv_no').val() || '').trim();
    var dt  = ($('#new_inv_date').val() || '').trim();
    var amt = parseFloat(($('#new_inv_amt').val() || '').replace(/,/g,'')) || 0;
    if (no === '') { Swal.fire({icon:'warning', title:'No Invoice required', text:'Enter the invoice number first.'}); $('#new_inv_no').focus(); return; }
    if (amt <= 0) { Swal.fire({icon:'warning', title:'Amount required', text:'Amount must be greater than 0.'}); $('#new_inv_amt').focus(); return; }
    var dup = false; $('#invBody .inv-no').each(function(){ if (($(this).val()||'') === no) dup = true; });
    if (dup) { Swal.fire({icon:'info', title:'Already added', text:'Invoice ' + no + ' is already in the list.'}); return; }
    var $btn = $(this).prop('disabled', true);
    $.post('kontrabon_new_check_inv.php', { no_inv: no, supplier: $('#nama_supp').val(), exclude_doc: DOC }, function (res) {
      $btn.prop('disabled', false);
      if (!res.ok) { Swal.fire({icon:'error', title:'Invoice already used', text: res.msg}); $('#new_inv_no').focus(); return; }
      addRow({ no_inv:no, tgl_inv:dt, amount:amt });
      $('#new_inv_no').val(''); $('#new_inv_date').val(''); $('#new_inv_amt').val('');
      calcTotal(); $('#new_inv_no').focus();
    }, 'json').fail(function(){ $btn.prop('disabled', false); Swal.fire({icon:'error', title:'Error', text:'Failed to validate invoice.'}); });
  });

  /* ---------- toggle / delete / show ---------- */
  $('#invBody').on('click', '.toggle-detail', function(){
    var $row = $(this).closest('.inv-row'), $detail = $row.next('.inv-detail');
    var open = $detail.is(':visible'); $detail.toggle(!open);
    $(this).find('i').toggleClass('fa-plus fa-minus');
    if (!open) $detail.find('.scan-faktur').focus();
  });
  $('#invBody').on('click', '.del-faktur', function(){
    var $detail = $(this).closest('.inv-detail'); $(this).closest('.faktur-item').remove(); updateCount($detail);
  });
  $('#invBody').on('click', '.del-bpb', function(){
    var $item = $(this).closest('.faktur-item'); $(this).closest('.bpb-row').remove(); refreshFaktur($item);
  });
  $('#invBody').on('click', '.del-inv', function(){
    var $row = $(this).closest('.inv-row'), $detail = $row.next('.inv-detail');
    $detail.remove(); $row.remove(); updateInvEmpty(); calcTotal();
  });
  $('#invBody').on('click', '.show-bpb', function(){
    var $r = $(this).closest('.bpb-row'); var no = String($r.data('no'));
    $('#bpbDetailTitle').text(no);
    $('#bpbDetailBody').html('<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#bpbDetailModal').modal('show');
    $.post('kontrabon_new_bpb_show.php', { no_bpb: no, supplier: $('#nama_supp').val() }, function (html) {
      $('#bpbDetailBody').html(html);
    }).fail(function () { $('#bpbDetailBody').html('<div class="text-danger p-3">Failed to load BPB detail.</div>'); });
  });
  $('#new_inv_date, #new_inv_amt').on('keydown', function(e){ if (e.which === 13) { e.preventDefault(); $('#btnAddRow').click(); } });

  /* ---------- Prefill dari DB ---------- */
  (PAYLOAD.invoices || []).forEach(function (iv) {
    var $pair = addRow({ no_inv: iv.no_inv, tgl_inv: iv.tgl_inv, amount: iv.amount });
    var $row = $pair.filter('.inv-row'); var $detail = $row.next('.inv-detail');
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

  /* ---------- Update ---------- */
  $('#btnSave').on('click', function () {
    var invoices = [], bad = [];
    collectInvoices().forEach(function (iv, i) {
      if (iv.no_inv === '' && iv.amount === 0) return;
      if (iv.no_inv === '') bad.push('Invoice ' + (i+1) + ': No Invoice is empty.');
      if (iv.amount <= 0)   bad.push('Invoice ' + (i+1) + ': Amount must be greater than 0.');
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
      title: 'Update this Invoice Received?', html: '<b>' + DOC + '</b><br><small class="text-muted">' + invoices.length + ' invoice(s) · ' + nFak + ' faktur · ' + nBpb + ' BPB</small>',
      icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Update', cancelButtonText: 'Cancel'
    }).then(function (result) { if (result.isConfirmed) sendUpdate(invoices); });
  });

  function sendUpdate(invoices){
    Swal.fire({ title:'Updating...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });
    $.post('kontrabon_new_update.php', {
      doc_number: DOC, document_date: $('#tgl_doc').val(), kontrabon_date: $('#tgl_received').val(), pesan: $('#pesan').val(),
      total_amount: $('#total_value_h').val(), invoices: JSON.stringify(invoices)
    }, function (res) {
      if (res.ok) { Swal.fire({icon:'success', title:'Updated', text: res.msg}).then(function(){ window.location = 'kontrabon_new.php'; }); }
      else {
        Swal.fire({ icon:'error', title:'Update failed', html: (res.msg || 'Unknown error') + '<br><small class="text-muted">Nothing was changed (rolled back). You can try again.</small>',
          showCancelButton:true, confirmButtonText:'Try again', cancelButtonText:'Close', allowOutsideClick:false })
          .then(function (r) { if (r.isConfirmed) sendUpdate(invoices); });
      }
    }, 'json').fail(function(){
      Swal.fire({ icon:'error', title:'Connection error', html:'Could not reach the server. You can try again.',
        showCancelButton:true, confirmButtonText:'Try again', cancelButtonText:'Close', allowOutsideClick:false })
        .then(function (r) { if (r.isConfirmed) sendUpdate(invoices); });
    });
  }
});
</script>

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
