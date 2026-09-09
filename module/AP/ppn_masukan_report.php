<?php include '../header.php' ?>
<?php
// ============================================================================
// SUB LEDGER - PPN MASUKAN (report)
// Kerangka halaman: styling meniru menu IR (kontrabon_new) lewat app-skin.css.
// Filter: Supplier + From/To. Header tabel bertingkat sudah disiapkan; ISI QUERY
// & loop baris di bagian <tbody> (lihat placeholder "TODO").
// ============================================================================

// Batas mundur filter From/To. Saldo yang lebih tua dari ini TIDAK boleh masuk lewat
// jurnal; nanti harus lewat menu UPLOAD SALDO AWAL (belum dibuat).
$PPN_MIN_DATE = '2026-01-01';

// Menu Set Opening Balance dibatasi user tertentu (daftar di saldo_awal_guard.php).
require_once __DIR__ . '/saldo_awal/saldo_awal_guard.php';
$bolehSaldoAwal = ppn_sa_can($user ?? ($_SESSION['username'] ?? ''));

$isPost  = ($_SERVER['REQUEST_METHOD'] === 'POST');
$fSupp   = $isPost ? ($_POST['nama_supp'] ?? 'ALL') : 'ALL';
// Pertama masuk: From & To sama-sama HARI INI.
$fStart  = ($isPost && !empty($_POST['start_date'])) ? date('Y-m-d', strtotime($_POST['start_date'])) : date('Y-m-d');
$fEnd    = ($isPost && !empty($_POST['end_date']))   ? date('Y-m-d', strtotime($_POST['end_date']))   : date('Y-m-d');
if ($fStart < $PPN_MIN_DATE) { $fStart = $PPN_MIN_DATE; }
if ($fEnd   < $PPN_MIN_DATE) { $fEnd   = $PPN_MIN_DATE; }
?>

<!-- Skin UI bersama (kartu, tabel, badge, tombol, dropdown, datepicker, loading) -->
<link rel="stylesheet" href="../css/app-skin.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin.css'); ?>">
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<style>
/* ============================================================================
   Tabel sub ledger — 29 kolom, 6 grup bertingkat.
     - Tiap sel PUNYA latar warna grup + garis pembatas, supaya kolom mana milik
       grup mana langsung kelihatan walau digeser jauh ke kanan.
     - Header dua tingkat: baris grup lebih gelap, sub-header lebih terang, plus
       garis warna grup di bawah judulnya.
     - Kolom Eqv IDR (angka yang dibaca) ditebalkan; kolom Currency yang isinya
       berulang "IDR" dikecilkan & diabukan supaya tidak ikut menarik perhatian.
   Peta kolom (1-based):
     1 SI No | 2 SI Date | 3 Supplier | 4 No Faktur | 5 Profit Center
     6-8 Beginning | 9-11 Addition | 12-16 Deduction | 17-21 Reclassification
     22-26 Adjustment | 27-29 Ending
   ============================================================================ */
#tblPpn{ font-size:12px; border-collapse:separate; border-spacing:0; }

/* ---- Header dua tingkat: baris grup lebih gelap, sub-header lebih terang ---- */
#tblPpn thead th{ text-align:center; font-weight:600; border:1px solid rgba(255,255,255,.42) !important; }
#tblPpn thead tr:first-child th{
  background:#1E3A8A; color:#fff; padding:10px 12px; font-size:12px; letter-spacing:.02em;
}
#tblPpn thead tr:last-child th{
  background:#33509f; color:#e2ebff; padding:7px 10px;
  font-size:10px; text-transform:uppercase; letter-spacing:.06em; font-weight:600;
}
/* Garis warna penanda grup di bawah judul grup */
#tblPpn thead tr:first-child th.grp{ border-bottom:3px solid transparent !important; }
#tblPpn thead tr:first-child th:nth-child(6) { border-bottom-color:#94a3b8 !important; } /* Beginning */
#tblPpn thead tr:first-child th:nth-child(7) { border-bottom-color:#22c07f !important; } /* Addition */
#tblPpn thead tr:first-child th:nth-child(8) { border-bottom-color:#f4635f !important; } /* Deduction */
#tblPpn thead tr:first-child th:nth-child(9) { border-bottom-color:#6366f1 !important; } /* Reclassification */
#tblPpn thead tr:first-child th:nth-child(10){ border-bottom-color:#f0a413 !important; } /* Adjustment */
#tblPpn thead tr:first-child th:nth-child(11){ border-bottom-color:#38bdf8 !important; } /* Ending */

/* ---- Baris data ---- */
#tblPpn tbody td{
  white-space:nowrap; text-align:center; padding:7px 10px; color:#334155;
  border-right:1px solid #d7dfea !important; border-bottom:1px solid #e3e9f2 !important;
}
#tblPpn tbody td:last-child{ border-right:0 !important; }
#tblPpn tbody td.num{ text-align:right; font-variant-numeric:tabular-nums; }
#tblPpn tbody td.txtleft{ text-align:left; }

/* Kolom Currency (6, 9, 14, 19, 24, 27) — isinya berulang, dibuat kalem */
#tblPpn tbody td:nth-child(6), #tblPpn tbody td:nth-child(9),
#tblPpn tbody td:nth-child(14), #tblPpn tbody td:nth-child(19),
#tblPpn tbody td:nth-child(24), #tblPpn tbody td:nth-child(27){ color:#64748b; font-size:11.5px; font-weight:600; }

/* Kolom Eqv IDR (8, 11, 16, 21, 26, 29) — angka utama, ditebalkan */
#tblPpn tbody td:nth-child(8), #tblPpn tbody td:nth-child(11),
#tblPpn tbody td:nth-child(16), #tblPpn tbody td:nth-child(21),
#tblPpn tbody td:nth-child(26), #tblPpn tbody td:nth-child(29){ font-weight:700; color:#0f172a; }

/* Garis pemisah lebih tegas di UJUNG tiap grup */
#tblPpn tbody td:nth-child(8),  #tblPpn tbody td:nth-child(11),
#tblPpn tbody td:nth-child(16), #tblPpn tbody td:nth-child(21),
#tblPpn tbody td:nth-child(26){ border-right:1px solid #aebbcd !important; }

/* Hanya TABEL yg scroll horizontal; Search/length/info/pagination di luar scroll */
.app-dt-scroll{ overflow-x:auto; }

/* FREEZE 5 kolom pertama (SI No .. Profit Center) saat scroll horizontal.
   Offset 'left' tiap kolom diset via JS (ppnFreeze) sesuai lebar aktual. */
#tblPpn thead tr:first-child th:nth-child(-n+5),
#tblPpn tbody td:nth-child(-n+5){ position:sticky; z-index:2; }
#tblPpn tbody td:nth-child(-n+5){ background:#fff; }
#tblPpn tbody tr:hover td:nth-child(-n+5){ background:#f5f8ff; }
#tblPpn thead tr:first-child th:nth-child(-n+5){ background:#1E3A8A; z-index:3; }
/* Penanda batas area freeze di kolom ke-5 (Profit Center) */
#tblPpn thead tr:first-child th:nth-child(5),
#tblPpn tbody td:nth-child(5),
#tblPpn tfoot th:nth-child(5){ box-shadow:4px 0 8px -5px rgba(15,23,42,.35); }

/* Warna latar per GRUP pada sel data (biar tiap grup mudah dibedakan).
   Kolom 6-8 Beginning Balance | 9-11 Addition | 12-16 Deduction |
   17-21 Reclassification | 22-26 Adjustment | 27-29 Ending Balance. */
#tblPpn tbody td:nth-child(n+6):nth-child(-n+8)  { background:#f1f5f9; } /* Beginning Balance */
#tblPpn tbody td:nth-child(n+9):nth-child(-n+11) { background:#ecfdf3; } /* Addition (in)  */
#tblPpn tbody td:nth-child(n+12):nth-child(-n+16){ background:#fef2f2; } /* Deduction (out) */
#tblPpn tbody td:nth-child(n+17):nth-child(-n+21){ background:#eef2ff; } /* Reclassification */
#tblPpn tbody td:nth-child(n+22):nth-child(-n+26){ background:#fffbeb; } /* Adjustment */
#tblPpn tbody td:nth-child(n+27):nth-child(-n+29){ background:#f0f9ff; } /* Ending Balance */
/* Hover: seluruh baris ikut menyala, tetap terasa grupnya (warna dipertegas) */
#tblPpn tbody tr:hover td{ filter:brightness(.975); }

/* ===== Footer GRAND TOTAL (semua halaman, bukan cuma halaman aktif) ===== */
#tblPpn tfoot th{
  background:#eef2f7; border-top:2px solid #1E3A8A !important; border-right:1px solid #cbd5e1;
  border-bottom:0; padding:10px 10px; font-size:12px; font-weight:700; color:#0f172a;
  white-space:nowrap; text-align:center;
}
#tblPpn tfoot th:last-child{ border-right:0; }
#tblPpn tfoot th.num{ text-align:right; font-variant-numeric:tabular-nums; }
/* 5 kolom pertama ikut ter-freeze seperti thead/tbody */
#tblPpn tfoot th:nth-child(-n+5){ position:sticky; z-index:2; background:#e2e8f0; text-align:left; }
/* warna grup senada tbody, sedikit lebih pekat biar terbaca sebagai total */
#tblPpn tfoot th:nth-child(n+6):nth-child(-n+8)  { background:#e2e8f0; }
#tblPpn tfoot th:nth-child(n+9):nth-child(-n+11) { background:#d7f5e4; }
#tblPpn tfoot th:nth-child(n+12):nth-child(-n+16){ background:#fde2e2; }
#tblPpn tfoot th:nth-child(n+17):nth-child(-n+21){ background:#dfe5fb; }
#tblPpn tfoot th:nth-child(n+22):nth-child(-n+26){ background:#fdf0cf; }
#tblPpn tfoot th:nth-child(n+27):nth-child(-n+29){ background:#d8eefc; }
#tblPpn tfoot th:nth-child(29){ color:#1E3A8A; }
#tblPpn tfoot .ftot-label{ color:#1e3a8a; letter-spacing:.3px; }
#tblPpn tfoot .ftot-sub{ font-weight:600; color:#94a3b8; font-size:11px; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">

  <!-- ===== Filter card ===== -->
  <div class="card app-card border-0 app-anim">
    <div class="card-header app-card-header d-flex align-items-center justify-content-between">
      <h5 class="mb-0"><i class="fa fa-book" aria-hidden="true"></i> SUB LEDGER &mdash; PPN MASUKAN</h5>
      <?php if ($bolehSaldoAwal) { ?>
      <!-- .app-btn-ghost: tombol kaca utk header gelap, tidak lagi kotak putih menempel -->
      <button type="button" id="btnSaldoAwal" class="app-btn app-btn-ghost app-btn-sm"
              title="Upload beginning balance">
        <i class="fa fa-upload"></i> Beginning Balance
      </button>
      <?php } ?>
    </div>
    <div class="card-body p-3">
      <form id="form-filter" method="post" action="ppn_masukan_report.php">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="app-flabel">Supplier</label>
            <select class="form-control selectpicker" name="nama_supp" data-live-search="true" data-size="5">
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
            <label class="app-flabel">From</label>
            <input type="text" class="form-control form-control-sm tanggal" name="start_date" value="<?= date('d M Y', strtotime($fStart)) ?>" autocomplete="off">
          </div>
          <div class="col-md-2">
            <label class="app-flabel">To</label>
            <input type="text" class="form-control form-control-sm tanggal" name="end_date" value="<?= date('d M Y', strtotime($fEnd)) ?>" autocomplete="off">
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <!-- Disamakan dgn sistem tombol .app-btn spt di menu lain (dulu btn-info/btn-success bawaan) -->
            <div class="app-actions">
              <button type="submit" class="app-btn app-btn-primary app-btn-ctl"><i class="fa fa-search"></i> Search</button>
              <button type="button" id="btnExport" class="app-btn app-btn-success app-btn-ctl"><i class="fa fa-file-excel-o"></i> Export</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ===== Table card ===== -->
  <div class="card app-card border-0 mt-4 app-anim app-anim-2">
    <div class="card-body p-4">
      <!-- .app-loading-wrap: area yg ditutup overlay loading saat Search (skin: app-skin-form.css) -->
      <div class="app-loading-wrap" id="ppnLoad">
        <div class="app-loading">
          <div class="app-loading-box">
            <div class="app-spinner"><span>NAG</span></div>
            <div class="app-loading-text">Loading data...</div>
          </div>
        </div>
        <table id="tblPpn" class="table table-hover app-dt" style="width:100%">
          <thead>
            <tr>
              <th rowspan="2">SI No</th>
              <th rowspan="2">SI Date</th>
              <th rowspan="2" style="text-align:left;">Supplier</th>
              <th rowspan="2">No Faktur Pajak</th>
              <th rowspan="2">Profit Center</th>
              <th class="grp" colspan="3">Beginning Balance</th>
              <th class="grp" colspan="3">Addition</th>
              <th class="grp" colspan="5">Deduction</th>
              <th class="grp" colspan="5">Reclassification</th>
              <th class="grp" colspan="5">Adjustment</th>
              <th class="grp" colspan="3">Ending Balance</th>
            </tr>
            <tr>
              <!-- Beginning Balance -->
              <th>Currency</th><th>Amount OCY</th><th>Eqv IDR</th>
              <!-- Addition -->
              <th>Currency</th><th>Amount OCY</th><th>Eqv IDR</th>
              <!-- Deduction -->
              <th>Doc No</th><th>Date</th><th>Currency</th><th>Amount OCY</th><th>Eqv IDR</th>
              <!-- Reclassification -->
              <th>Doc No</th><th>Date</th><th>Currency</th><th>Amount OCY</th><th>Eqv IDR</th>
              <!-- Adjustment -->
              <th>Doc No</th><th>Date</th><th>Currency</th><th>Amount OCY</th><th>Eqv IDR</th>
              <!-- Ending Balance -->
              <th>Currency</th><th>Amount OCY</th><th>Eqv IDR</th>
            </tr>
          </thead>
          <tbody><!-- diisi via DataTables AJAX (ajx_ppn_masukan.php) --></tbody>
          <!-- GRAND TOTAL: dihitung dari SELURUH baris hasil filter (lintas halaman),
               lihat footerCallback di bawah. Urutan 29 sel = urutan kolom header. -->
          <tfoot>
            <tr>
              <th class="ftot-label">GRAND TOTAL</th>
              <th class="ftot-sub" id="ppnFootCount"></th>
              <th></th><th></th><th></th>
              <th class="ppn-fcurr"></th><th class="num"></th><th class="num"></th>
              <th class="ppn-fcurr"></th><th class="num"></th><th class="num"></th>
              <th></th><th></th><th class="ppn-fcurr"></th><th class="num"></th><th class="num"></th>
              <th></th><th></th><th class="ppn-fcurr"></th><th class="num"></th><th class="num"></th>
              <th></th><th></th><th class="ppn-fcurr"></th><th class="num"></th><th class="num"></th>
              <th class="ppn-fcurr"></th><th class="num"></th><th class="num"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ===== Modal detail dokumen (dipakai saat Doc No di kolom Deduction diklik) =====
     Skin .app-modal ada di ../css/app-skin-form.css supaya bisa dipakai halaman lain. -->
<div class="modal fade" id="ppnDocModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog app-modal modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-text-o"></i> <span id="ppnDocTitle">Document Detail</span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body" id="ppnDocBody"></div>
      <div class="modal-footer">
        <button type="button" class="app-btn app-btn-light app-btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
      </div>
    </div>
  </div>
</div>

<?php if ($bolehSaldoAwal) { ?>
<!-- ===== Modal SET OPENING BALANCE (upload saldo awal) — lebar 80% =====
     Hanya dirender untuk user yang berhak (lihat saldo_awal/saldo_awal_guard.php).
     Endpoint-nya juga dijaga daftar user yang sama, jadi tidak bisa ditembus
     dengan memanggil URL-nya langsung. -->
<div class="modal fade" id="saModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog app-modal app-modal-80 modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-upload"></i> Beginning Balance</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">

        <!-- Ringkasan saldo awal yang sedang aktif + bar upload -->
        <div class="row g-3 align-items-end" style="margin-bottom:16px;">
          <div class="col-lg-7">
            <div class="app-mod-info" id="saPosted" style="margin-bottom:0;"></div>
          </div>
          <div class="col-lg-5">
            <label class="app-flabel">Upload file</label>
            <div class="app-actions">
              <a href="format-excel/format_saldo_awal_ppn.xls" class="app-btn app-btn-dark app-btn-sm">
                <i class="fa fa-download"></i> Template</a>
              <label class="app-btn app-btn-light app-btn-sm" style="margin:0;">
                <i class="fa fa-folder-open-o"></i> <span id="saFileName">Choose file...</span>
                <input type="file" id="saFile" accept=".xls,.xlsx,.csv" style="display:none;">
              </label>
              <button type="button" id="saBtnUpload" class="app-btn app-btn-success app-btn-sm">
                <i class="fa fa-upload"></i> Upload</button>
            </div>
          </div>
        </div>

        <div class="app-mod-sec no-line">
          <span>Preview</span> <span id="saCount" class="ftot-sub"></span>
          <span style="flex:1;"></span>
          <span class="app-search"><i class="fa fa-search"></i>
            <input type="text" id="saSearch" placeholder="Search supplier / invoice no / doc no..." autocomplete="off"></span>
        </div>
        <div id="saPreview"></div>

      </div>
      <div class="modal-footer">
        <button type="button" id="saBtnClear" class="app-btn app-btn-danger app-btn-sm mr-auto"><i class="fa fa-trash"></i> Clear All</button>
        <button type="button" class="app-btn app-btn-light app-btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        <button type="button" id="saBtnSave" class="app-btn app-btn-primary app-btn-sm"><i class="fa fa-save"></i> Save Beginning Balance</button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<!-- Library per-halaman (header.php TIDAK memuatnya). jQuery dulu. -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script>
var tPpn;
$(function () {
  $('.selectpicker').selectpicker();
  // startDate: filter tidak boleh mundur melewati batas (lihat $PPN_MIN_DATE di atas).
  $('.tanggal').datepicker({ format:'dd M yyyy', autoclose:true, language:'en',
      startDate:'<?php echo date('d M Y', strtotime($PPN_MIN_DATE)); ?>' })
    .on('show', function () {
      var w = $(this).outerWidth();
      setTimeout(function () { $('.datepicker-dropdown:visible').outerWidth(w); }, 0);
    });

  // ===== DataTables AJAX =====
  // Endpoint (ajx_ppn_masukan.php) balikan JSON: { "data": [ [ ...31 kolom... ], ... ] }
  // (array per baris, urutan kolom sesuai header). Kolom nominal otomatis rata kanan.
  var numCols = [6, 7, 9, 10, 14, 15, 19, 20, 24, 25, 27, 28]; // Amount OCY & Eqv IDR
  tPpn = $('#tblPpn').DataTable({
    ordering: false,
    processing: true,
    autoWidth: false,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100, 200],
    // Layout: length+search (atas) & info+pagination (bawah) DI LUAR area scroll;
    // hanya tabel yg dibungkus .app-dt-scroll yang scroll horizontal.
    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
         "<'app-dt-scroll't>" +
         "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
    ajax: {
      url: 'ajx_ppn_masukan.php',
      type: 'POST',
      data: function (d) {
        d.nama_supp  = $('[name=nama_supp]').val() || 'ALL';
        d.start_date = $('[name=start_date]').val() || '';
        d.end_date   = $('[name=end_date]').val() || '';
      },
      dataSrc: 'data'
    },
    columnDefs: [
      { targets: numCols, className: 'num' },
      { targets: [0, 2], className: 'txtleft' } // SI No & Supplier rata kiri
    ],
    // GRAND TOTAL di <tfoot>: memakai rows({search:'applied'}) -> menjumlah SEMUA
    // baris hasil filter, TIDAK terpatok halaman aktif.
    footerCallback: function () {
      var api  = this.api();
      var rows = api.rows({ search: 'applied' }).data();
      var num  = function (v) {
        v = (v === null || v === undefined) ? '' : String(v).replace(/,/g, '').trim();
        return v === '' ? 0 : (parseFloat(v) || 0);
      };
      var fmt  = function (v) { return v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
      var sumCols = [6, 7, 9, 10, 14, 15, 19, 20, 24, 25, 27, 28];
      var ocyCols = [6, 9, 14, 19, 24, 27];             // Amount OCY -> hanya sah kalau 1 mata uang
      var currCols = [5, 8, 13, 18, 23, 26];            // sel Currency di footer
      var tot = {}, i, c, currs = {};
      for (i = 0; i < sumCols.length; i++) { tot[sumCols[i]] = 0; }
      for (i = 0; i < rows.length; i++) {
        for (c = 0; c < sumCols.length; c++) { tot[sumCols[c]] += num(rows[i][sumCols[c]]); }
        if (rows[i][26]) { currs[rows[i][26]] = 1; }
      }
      var ck = Object.keys(currs);
      var $f = $('#tblPpn tfoot th');
      for (i = 0; i < sumCols.length; i++) {
        c = sumCols[i];
        if (ocyCols.indexOf(c) >= 0 && ck.length > 1) {
          $f.eq(c).text('\u2014').attr('title', 'Mixed currency: ' + ck.join(', '));
        } else {
          $f.eq(c).text(fmt(tot[c])).removeAttr('title');
        }
      }
      for (i = 0; i < currCols.length; i++) { $f.eq(currCols[i]).text(ck.length === 1 ? ck[0] : ''); }
      $('#ppnFootCount').text(rows.length.toLocaleString('en-US') + ' rows');
    },
    language: {
      processing:  '<i class="fa fa-spinner fa-spin"></i> Loading...',
      emptyTable:  '<div class="app-empty"><i class="fa fa-inbox"></i>Data not found</div>',
      zeroRecords: '<div class="app-empty"><i class="fa fa-search"></i>No matching records</div>',
      lengthMenu:  'Show _MENU_ entries',
      info:        'Showing _START_&ndash;_END_ of _TOTAL_ entries',
      infoEmpty:   'Showing 0 entries',
      paginate:    { previous: '&lsaquo; Prev', next: 'Next &rsaquo;' }
    }
  });

  // Overlay loading (skin .app-loading) mengikuti status processing DataTables.
  tPpn.on('processing.dt', function (e, settings, processing) {
    $('#ppnLoad').toggleClass('is-loading', processing);
  });

  // Freeze 5 kolom pertama: set offset 'left' tiap kolom sesuai lebar aktual.
  function ppnFreeze() {
    var $hdr = $('#tblPpn thead tr').first().children('th').slice(0, 5);
    if (!$hdr.length) return;
    var left = 0, offs = [];
    $hdr.each(function () { offs.push(left); left += $(this).outerWidth(); });
    $hdr.each(function (i) { $(this).css('left', offs[i] + 'px'); });
    $('#tblPpn tbody tr').each(function () {
      $(this).children('td').slice(0, 5).each(function (i) { $(this).css('left', offs[i] + 'px'); });
    });
    $('#tblPpn tfoot tr').each(function () {
      $(this).children('th').slice(0, 5).each(function (i) { $(this).css('left', offs[i] + 'px'); });
    });
  }
  tPpn.on('draw.dt', ppnFreeze);
  $(window).on('resize', function () { ppnFreeze(); });

  // Search -> reload tabel (bukan reload halaman)
  $('#form-filter').on('submit', function (e) { e.preventDefault(); tPpn.ajax.reload(); });

  // ===== Export Excel =====
  // Berkas diambil lewat fetch() sebagai Blob, BARU disodorkan ke user sbg unduhan.
  // Cara lama (submit ke <iframe> + cookie penanda) tidak bisa diandalkan: browser
  // memperlakukan respons attachment sbg unduhan, iframe-nya tidak pernah memicu
  // 'load', dan cookie-nya tidak selalu terbaca — akibatnya loading menggantung
  // padahal berkasnya sudah turun. Dengan fetch, selesainya TEPAT saat blob diterima.
  $('#btnExport').on('click', function () {
    var jml = tPpn ? tPpn.rows({ search: 'applied' }).data().length : 0;
    if (!jml) {
      Swal.fire({ icon: 'info', title: 'Nothing to export', text: 'There is no row in the current result. Adjust the filter, then try again.' });
      return;
    }
    Swal.fire({
      title: 'Export to Excel?',
      html: '<div style="text-align:center;font-size:14px;color:#334155;line-height:1.6">' +
            esc($('[name=start_date]').val()) + ' &nbsp;&rarr;&nbsp; ' + esc($('[name=end_date]').val()) + '</div>',
      icon: 'question', showCancelButton: true,
      confirmButtonText: '<i class="fa fa-file-excel-o"></i> Download',
      cancelButtonText: 'Cancel', confirmButtonColor: '#10b981'
    }).then(function (r) {
      if (r.isConfirmed) { ppnMulaiEkspor(jml); }
    });
  });

  function ppnMulaiEkspor(jml) {
    var mulai = new Date().getTime();
    Swal.fire({
      title: 'Preparing your Excel file',
      html: '<div class="app-spinner" style="margin:6px auto 14px;"><span>NAG</span></div>' +
            '<div style="font-size:12.5px;color:#475569;line-height:1.7">' +
            'Building <b>' + jml.toLocaleString('en-US') + '</b> rows &times; 29 columns.<br>' +
            '<span id="ppnDlTick" style="color:#94a3b8;font-size:11.5px;">starting...</span></div>',
      allowOutsideClick: false, allowEscapeKey: false, showConfirmButton: false
    });
    $('#ppnLoad').addClass('is-loading');

    var tick = setInterval(function () {
      $('#ppnDlTick').text(Math.round((new Date().getTime() - mulai) / 1000) + 's elapsed - please keep this tab open');
    }, 1000);

    var beres = function () { clearInterval(tick); $('#ppnLoad').removeClass('is-loading'); };

    var body = new URLSearchParams({
      nama_supp:  $('[name=nama_supp]').val() || 'ALL',
      start_date: $('[name=start_date]').val() || '',
      end_date:   $('[name=end_date]').val() || ''
    });

    fetch('ekspor_ppn_masukan.php', {
      method: 'POST', credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    })
      .then(function (res) {
        if (!res.ok) { throw new Error('HTTP ' + res.status); }
        // Nama berkas diambil dari header supaya sama dgn yang ditentukan server.
        var cd = res.headers.get('Content-Disposition') || '', nm = '';
        var m = cd.match(/filename="?([^"]+)"?/);
        if (m) { nm = m[1]; }
        return res.blob().then(function (b) { return { blob: b, nama: nm || 'SubLedger_PPN_Masukan.xls' }; });
      })
      .then(function (f) {
        var url = URL.createObjectURL(f.blob);
        var a = document.createElement('a');
        a.href = url; a.download = f.nama;
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
        beres();
        Swal.fire({
          icon: 'success', title: 'Excel file ready',
          html: '<div style="font-size:13px;line-height:1.7">' +
                jml.toLocaleString('en-US') + ' rows &bull; ' +
                (f.blob.size / 1048576).toFixed(2) + ' MB &bull; ' +
                Math.max(1, Math.round((new Date().getTime() - mulai) / 1000)) + 's<br>' +
                '<span style="color:#64748b;font-size:12px">' + esc(f.nama) + '</span></div>',
          timer: 4000, timerProgressBar: true
        });
      })
      .catch(function (e) {
        beres();
        Swal.fire({ icon: 'error', title: 'Export failed', text: String(e && e.message ? e.message : e) });
      });
  }
});

  // ===== Klik Doc No (kolom Deduction) -> modal detail per baris jurnal =====
  var esc = function (s) {
    return String(s === null || s === undefined ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  };
  var nf = function (v) {
    v = parseFloat(v); if (isNaN(v)) { v = 0; }
    return v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };
  var dmy = function (s) {
    if (!s || s === '0000-00-00') { return '-'; }
    var p = String(s).substr(0, 10).split('-');
    if (p.length !== 3) { return s; }
    var m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return p[2] + '-' + (m[parseInt(p[1], 10) - 1] || p[1]) + '-' + p[0];
  };
  var info = function (label, val) {
    return '<div><b>' + esc(label) + '</b><span>' + (val === '' || val === null || val === undefined ? '-' : esc(val)) + '</span></div>';
  };

  $('#tblPpn').on('click', 'a.ppn-doc', function () {
    var doc = $(this).data('doc') || '', fk = $(this).data('fk') || '';
    $('#ppnDocTitle').text(doc);
    $('#ppnDocBody').html('<div class="text-center" style="padding:38px 0;">' +
      '<div class="app-spinner" style="margin:0 auto;"><span>NAG</span></div>' +
      '<div class="app-loading-text" style="margin-top:12px;">Loading detail...</div></div>');
    $('#ppnDocModal').modal('show');

    $.post('ajx_ppn_masukan_detail.php', { no_journal: doc, faktur: fk }, null, 'json')
      .done(function (res) {
        if (!res || res.status !== 'success') {
          $('#ppnDocBody').html('<div class="app-empty" style="padding:30px 0;"><i class="fa fa-exclamation-triangle"></i>' +
            esc((res && res.message) ? res.message : 'Failed to load detail.') + '</div>');
          return;
        }
        var h = res.head, u = res.upload, t = res.total, html = '';

        html += '<div class="app-mod-info">' +
          info('Document No', h.no_journal) + info('Document Date', dmy(h.tgl_journal)) +
          info('Type', h.type_journal) + info('Tax Invoice No', h.faktur_pajak) +
          info('Supplier', h.supplier) + info('Profit Center', h.profit_center) +
          info('Status', h.status) + '</div>';

        if (u) {
          html += '<div class="app-mod-sec">Uploaded invoice data</div><div class="app-mod-info">' +
            info('Period', u.bulan) + info('Type', u.jenis) + info('Seller', u.nama_penjual) +
            info('NPWP', u.npwp) + info('Invoice Date', dmy(u.tgl_faktur)) +
            info('DPP', nf(u.dpp)) + info('DPP Other Value', nf(u.dpp_nilai_lain)) +
            info('VAT (PPN)', nf(u.ppn)) + info('PPnBM', nf(u.ppnbm)) +
            info('Replaced Invoice', u.faktur_diganti) + '</div>';
        }

        html += '<div class="app-mod-sec">Journal lines (' + res.lines.length + ')</div>' +
          '<div class="app-mod-scroll"><table class="app-mod-tbl coa-tbl"><thead><tr>' +
          '<th>COA</th><th>Cost Center</th><th>Reff Doc</th><th>Curr</th><th class="num">Rate</th>' +
          '<th class="num">Debit</th><th class="num">Credit</th>' +
          '<th class="num">Debit IDR</th><th class="num">Credit IDR</th><th>Description</th>' +
          '</tr></thead><tbody>';
        $.each(res.lines, function (i, r) {
          html += '<tr>' +
            '<td><b>' + esc(r.no_coa) + '</b><br><span style="color:#64748b;">' + esc(r.nama_coa) + '</span></td>' +
            '<td>' + esc(r.no_costcenter) + '</td>' +
            '<td class="nowrap">' + esc(r.reff_doc) + '</td>' +
            '<td class="nowrap">' + esc(r.curr) + '</td>' +
            '<td class="num">' + nf(r.rate) + '</td>' +
            '<td class="num">' + nf(r.debit) + '</td>' +
            '<td class="num">' + nf(r.credit) + '</td>' +
            '<td class="num">' + nf(r.debit_idr) + '</td>' +
            '<td class="num">' + nf(r.credit_idr) + '</td>' +
            '<td>' + esc(r.keterangan) + '</td></tr>';
        });
        html += '</tbody><tfoot><tr>' +
          '<th colspan="5">TOTAL</th>' +
          '<th class="num">' + nf(t.debit) + '</th><th class="num">' + nf(t.credit) + '</th>' +
          '<th class="num">' + nf(t.debit_idr) + '</th><th class="num">' + nf(t.credit_idr) + '</th>' +
          '<th></th></tr></tfoot></table></div>';

        $('#ppnDocBody').html(html);
      })
      .fail(function () {
        $('#ppnDocBody').html('<div class="app-empty" style="padding:30px 0;">' +
          '<i class="fa fa-exclamation-triangle"></i>Failed to contact the server.</div>');
      });
  });
<?php if ($bolehSaldoAwal) { ?>

  // ================= SET OPENING BALANCE (upload saldo awal) =================
  var SA_ASOF = '<?= $PPN_MIN_DATE ?>';
  var saDupe  = 0;
  var saMode  = 'post';   // 'post' = data tersimpan, 'temp' = draft hasil upload
  var saRows  = [];       // isi preview yg sedang dimuat (utk filter Search di klien)
  var saResMode = 'post';

  // Ringkasan saldo awal yang SEDANG tersimpan (Post)
  function saPosted(p) {
    var n = parseInt((p && p.n) || 0, 10);
    $('#saPosted').html(
      info('Currently saved', n ? (n.toLocaleString('en-US') + ' rows') : 'None yet') +
      info('Total Eqv IDR', n ? nf(p.idr) : '-') +
      info('Saved by', n ? p.by_user : '-') +
      info('Saved at', n ? String(p.at_time || '').substr(0, 16) : '-')
    );
  }

  // Tabel preview (dipakai utk data Post maupun hasil upload Temp)
  // Data preview disimpan di memori supaya kotak Search bisa menyaring tanpa
  // bolak-balik ke server. saRender() menyimpan, saDraw() menggambar hasil filter.
  function saRender(res) {
    saRows    = res.rows || [];
    saResMode = res.mode;
    $('#saSearch').val('');
    saDraw();
  }

  function saDraw() {
    var q = $.trim(($('#saSearch').val() || '')).toLowerCase();
    var rows = !q ? saRows : $.grep(saRows, function (r) {
      return ((r.si_no || '') + ' ' + (r.supplier || '') + ' ' + (r.faktur_pajak || '') + ' ' +
              (r.profit_center || '') + ' ' + (r.curr || '')).toLowerCase().indexOf(q) >= 0;
    });

    $('#saCount').text(!saRows.length ? '' :
      (q ? rows.length.toLocaleString('en-US') + ' of ' + saRows.length.toLocaleString('en-US') + ' rows'
         : saRows.length.toLocaleString('en-US') + ' rows') +
      (saResMode === 'temp' ? ' (uploaded, not saved yet)' : ' (saved)'));

    if (!saRows.length) {
      $('#saPreview').html('<div class="app-empty" style="padding:34px 0;"><i class="fa fa-inbox"></i>' +
        'No data yet. Download the template, fill it in, then upload.</div>');
      return;
    }
    if (!rows.length) {
      $('#saPreview').html('<div class="app-empty" style="padding:34px 0;"><i class="fa fa-search"></i>' +
        'No row matches "' + esc(q) + '".</div>');
      return;
    }

    var h = '<div class="app-mod-scroll h10"><table class="app-mod-tbl"><thead><tr>' +
      '<th>SI No</th><th>SI Date</th><th>Supplier</th><th>No Faktur Pajak</th><th>Tgl Faktur Pajak</th>' +
      '<th>Profit Center</th><th>Currency</th><th class="num">Rate</th>' +
      '<th class="num">Amount OCY</th><th class="num">Eqv IDR</th>' +
      '<th style="width:46px;"></th></tr></thead><tbody>';
    var tOcy = 0, tIdr = 0, curr = {};
    $.each(rows, function (i, r) {
      tOcy += parseFloat(r.amount_ocy) || 0;
      tIdr += parseFloat(r.amount_idr) || 0;
      if (r.curr) { curr[r.curr] = 1; }
      h += '<tr><td class="nowrap">' + esc(r.si_no) + '</td>' +
        '<td class="nowrap">' + dmy(r.si_date) + '</td>' +
        '<td>' + esc(r.supplier) + '</td>' +
        '<td class="nowrap">' + esc(r.faktur_pajak) + '</td>' +
        '<td class="nowrap">' + dmy(r.tgl_faktur_pajak) + '</td>' +
        '<td>' + esc(r.profit_center) + '</td>' +
        '<td class="nowrap">' + esc(r.curr) + '</td>' +
        '<td class="num">' + nf(r.rate) + '</td>' +
        '<td class="num">' + nf(r.amount_ocy) + '</td>' +
        '<td class="num">' + nf(r.amount_idr) + '</td>' +
        '<td class="num"><button type="button" class="app-btn app-btn-danger app-btn-sm sa-del"' +
          ' data-id="' + esc(r.id) + '" title="Delete this row"><i class="fa fa-trash"></i></button></td></tr>';
    });
    var ck = Object.keys(curr);
    h += '</tbody><tfoot><tr><th colspan="8">TOTAL' + (q ? ' (filtered)' : '') + '</th>' +
      '<th class="num">' + (ck.length > 1 ? '\u2014' : nf(tOcy)) + '</th>' +
      '<th class="num">' + nf(tIdr) + '</th><th></th></tr></tfoot></table></div>';
    $('#saPreview').html(h);
  }

  // Search: disaring di sisi klien, ditunda 180ms biar tidak menggambar tiap ketukan
  var saTmr = null;
  $('#saSearch').on('input', function () {
    clearTimeout(saTmr);
    saTmr = setTimeout(saDraw, 180);
  });

  function saLoad(mode) {
    saMode = mode;
    $('#saPreview').html('<div class="text-center" style="padding:34px 0;">' +
      '<div class="app-spinner" style="margin:0 auto;"><span>NAG</span></div>' +
      '<div class="app-loading-text" style="margin-top:12px;">Loading...</div></div>');
    $.get('saldo_awal/ajx_get_saldo_awal.php', { mode: mode, as_of: SA_ASOF }, null, 'json')
      .done(function (res) {
        if (!res || res.status !== 'success') {
          $('#saPreview').html('<div class="app-empty" style="padding:30px 0;"><i class="fa fa-lock"></i>' +
            esc((res && res.message) ? res.message : 'Failed to load data.') + '</div>');
          return;
        }
        saPosted(res.posted);
        saRender(res);
        saDupe = parseInt(res.dupe || 0, 10);   // faktur draft yg sdh ada di saldo tersimpan
      })
      .fail(function () {
        $('#saPreview').html('<div class="app-empty" style="padding:30px 0;">' +
          '<i class="fa fa-exclamation-triangle"></i>Failed to contact the server.</div>');
      });
  }

  $('#btnSaldoAwal').on('click', function () {
    $('#saFile').val(''); $('#saFileName').text('Choose file...');
    $('#saModal').modal('show');
    saLoad('post');
  });

  // ----- Hapus SATU baris -----
  $('#saPreview').on('click', '.sa-del', function () {
    var id = $(this).data('id');
    Swal.fire({
      icon: 'warning', title: 'Delete this row?',
      text: 'The row will be removed from the ' + (saMode === 'temp' ? 'uploaded draft' : 'saved beginning balance') + '.',
      showCancelButton: true, confirmButtonText: 'Yes, delete', cancelButtonText: 'Cancel',
      confirmButtonColor: '#e13c37'
    }).then(function (r) {
      if (!r.isConfirmed) { return; }
      $.post('saldo_awal/hapus_saldo_awal.php', { mode: 'row', id: id, as_of: SA_ASOF }, null, 'json')
        .done(function (res) {
          if (!res || res.status !== 'success') {
            Swal.fire({ icon: 'error', title: 'Delete failed', text: (res && res.message) ? res.message : 'Unknown error.' });
            return;
          }
          saLoad(saMode);
          tPpn.ajax.reload(null, false);
        })
        .fail(function () { Swal.fire({ icon: 'error', title: 'Delete failed', text: 'Failed to contact the server.' }); });
    });
  });

  // ----- Hapus SEMUA baris (draft atau data tersimpan, sesuai yg sedang dilihat) -----
  $('#saBtnClear').on('click', function () {
    var isTemp = (saMode === 'temp');
    Swal.fire({
      icon: 'warning',
      title: isTemp ? 'Clear uploaded draft?' : 'Clear all beginning balance?',
      html: isTemp
        ? 'All rows from your upload will be discarded (nothing saved is affected).'
        : 'All beginning balance rows as of <b><?= date('d M Y', strtotime($PPN_MIN_DATE)) ?></b> ' +
          'will be deleted.<br>The Beginning Balance column in this report will drop to the journal-only value.',
      showCancelButton: true, confirmButtonText: 'Yes, clear', cancelButtonText: 'Cancel',
      confirmButtonColor: '#e13c37'
    }).then(function (r) {
      if (!r.isConfirmed) { return; }
      $.post('saldo_awal/hapus_saldo_awal.php',
             { mode: 'all', scope: isTemp ? 'temp' : 'post', as_of: SA_ASOF }, null, 'json')
        .done(function (res) {
          if (!res || res.status !== 'success') {
            Swal.fire({ icon: 'error', title: 'Clear failed', text: (res && res.message) ? res.message : 'Unknown error.' });
            return;
          }
          Swal.fire({ icon: 'success', title: 'Cleared',
            text: res.hapus.toLocaleString('en-US') + ' rows deleted.' });
          saLoad('post');
          tPpn.ajax.reload(null, false);
        })
        .fail(function () { Swal.fire({ icon: 'error', title: 'Clear failed', text: 'Failed to contact the server.' }); });
    });
  });

  $('#saFile').on('change', function () {
    var f = this.files && this.files[0];
    $('#saFileName').text(f ? f.name : 'Choose file...');
  });

  $('#saBtnUpload').on('click', function () {
    var f = $('#saFile')[0].files[0];
    if (!f) { Swal.fire({ icon: 'warning', title: 'No file selected', text: 'Please choose a file first.' }); return; }
    var fd = new FormData();
    fd.append('file', f);
    fd.append('as_of', SA_ASOF);
    var $b = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
    $.ajax({ url: 'saldo_awal/proses_upload_saldo_awal.php', type: 'POST', data: fd,
             processData: false, contentType: false, dataType: 'json' })
      .done(function (res) {
        if (!res || res.status !== 'success') {
          Swal.fire({ icon: 'error', title: 'Upload failed',
            text: (res && res.message) ? res.message : 'Unknown error.' });
          return;
        }
        Swal.fire({ icon: 'success', title: 'File uploaded',
          text: res.baris.toLocaleString('en-US') + ' rows read' +
                (res.skip ? ' (' + res.skip + ' empty rows skipped)' : '') +
                '. Check the preview, then click Save Beginning Balance.' });
        saLoad('temp');
      })
      .fail(function () { Swal.fire({ icon: 'error', title: 'Upload failed', text: 'Failed to contact the server.' }); })
      .always(function () { $b.prop('disabled', false).html('<i class="fa fa-upload"></i> Upload'); });
  });

  $('#saBtnSave').on('click', function () {
    var $b = $(this);
    Swal.fire({
      icon: 'question',
      title: 'Save beginning balance?',
      html: 'Beginning balance as of <b><?= date('d M Y', strtotime($PPN_MIN_DATE)) ?></b> will be updated:' +
            '<br>&bull; items already saved are <b>updated</b> (not added again)' +
            '<br>&bull; new items are added' +
            '<br>&bull; saved items that are not in this file are left as they are' +
            (saDupe ? '<br><br><span style="color:#1e3a8a;"><b>' + saDupe +
                      '</b> item(s) in this file already exist and will be <b>updated</b>.</span>' : ''),
      showCancelButton: true, confirmButtonText: 'Yes, save', cancelButtonText: 'Cancel'
    }).then(function (r) {
      if (!r.isConfirmed) { return; }
      $b.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
      $.post('saldo_awal/save_saldo_awal.php', { as_of: SA_ASOF }, null, 'json')
        .done(function (res) {
          if (!res || res.status !== 'success') {
            Swal.fire({ icon: 'error', title: 'Save failed',
              text: (res && res.message) ? res.message : 'Unknown error.' });
            return;
          }
          Swal.fire({ icon: 'success', title: 'Beginning balance saved',
            html: res.baris.toLocaleString('en-US') + ' rows from the file saved' +
                  (res.update ? ' (' + res.update.toLocaleString('en-US') + ' updated, ' +
                                (res.baris - res.update).toLocaleString('en-US') + ' new)' : '') +
                  '.<br>Now <b>' + (res.total || 0).toLocaleString('en-US') + ' rows</b>, ' +
                  'total Eqv IDR <b>' + nf(res.tot_idr) + '</b>' });
          $('#saModal').modal('hide');
          tPpn.ajax.reload();
        })
        .fail(function () { Swal.fire({ icon: 'error', title: 'Save failed', text: 'Failed to contact the server.' }); })
        .always(function () { $b.prop('disabled', false).html('<i class="fa fa-save"></i> Save Beginning Balance'); });
    });
  });
<?php } ?>
</script>
