<?php
/* ============================================================================
   AP HISTORY PROJECTION - layarnya.

   SATU-SATUNYA yang mengubah data di sini TOMBOL CANCEL. Awalnya layar ini
   dibuat baca-saja ("ini kan hanya menampilkan"), lalu pemakai meminta
   Cancel menyusul di hari yang sama - mengikuti History Projection di
   ar_dev. Pembatalannya MENGARSIPKAN DULU ke ap_projection_history_*_cancel
   baru menghapus; lihat batal_history_projection.php.

   DUA KARTU: daftar dokumen di atas, dan rincian satu dokumen di bawah yang
   baru muncul sesudah sebuah dokumen dibuka.

   ANGKANYA TIDAK DIHITUNG ULANG. Seluruhnya dibaca dari
   ap_projection_history_det - itu potret, dan memang harus tetap
   memperlihatkan angka saat Save ditekan walau kurs atau rencana bayar sudah
   berubah sejak itu. Kisi kolom tanggalnya dibangun ulang dari periode
   dokumennya; lihat ajx_history_projection_detail.php.

   Susunan & kosakatanya mengikuti ap_projection_report.php.
   ============================================================================ */
include '../header.php';
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<style>
/* Tabel rincian LEBAR (18 kolom tetap + satu kolom per hari), jadi yang
   digulir ke samping hanya kotak di sekeliling tabelnya - bukan kartunya,
   supaya Search & paging DataTables tidak ikut bergeser. Pelajaran dari
   layar Projection Report: aturan perataan DataTables diikat ke
   div.dataTables_wrapper, jadi kendalinya TIDAK boleh dipindah keluar. */
#hpj-det-tbl_wrapper .hpj-scroll { overflow-x: auto; }
#hpj-det-tbl { width: 100%; }
#hpj-det-tbl th, #hpj-det-tbl td { white-space: nowrap; }
#hpj-det-tbl td.hpj-supp, #hpj-det-tbl th.hpj-supp { white-space: normal; min-width: 190px; }

/* Warna kepala tabel sama persis dgn Projection Report - satu arti, satu
   warna, di dua layar. !important WAJIB: app-ftr-list.css mengunci
   background-color/color/border pada .ftl-tbl thead th dgn !important, dan
   !important mengalahkan aturan biasa berapa pun spesifisitasnya. */
#hpj-det-tbl thead th {
  background-color: #1e3a5f !important;
  color: #f8fafc !important;
  font-weight: 600;
  font-size: 11px;
  letter-spacing: .4px;
  text-transform: uppercase;
  text-align: center;
  vertical-align: middle;
  border: 0 !important;
  border-right: 1px solid rgba(255, 255, 255, .10) !important;
  border-bottom: 2px solid #0f2942 !important;
}
#hpj-det-tbl thead th.hpj-grp-pay { background-color: #2c5282 !important; }
#hpj-det-tbl thead th.hpj-sub-pay { background-color: #3563a0 !important; }
#hpj-det-tbl thead th.hpj-grp-prj { background-color: #115e59 !important; }
#hpj-det-tbl thead th.hpj-sub-prj { background-color: #0f766e !important; }
/* Aturan :first-child milik DataTables Responsive memipihkan sel pertama.
   Halaman ini tidak memakai Responsive, dan kepalanya DUA TINGKAT - jadi
   aturan itu mengenai "No" di baris 1 DAN "Tax Base" di baris 2. */
#hpj-det-tbl thead th:first-child {
  width: auto; padding-left: 10px !important; padding-right: 10px !important;
}
#hpj-det-tbl tfoot th {
  background-color: #1e3a5f; color: #f8fafc; font-weight: 700; font-size: 11.5px;
  padding: 9px 10px; border: 0; border-top: 2px solid #2c5282;
  border-right: 1px solid rgba(255, 255, 255, .10);
  white-space: nowrap; font-variant-numeric: tabular-nums;
}
#hpj-det-tbl td.hpj-kosong { color: #cbd5e1; }
#hpj-det-tbl td.hpj-tgl-mulai { border-left: 2px solid #b9cdeb; }
#hpj-det-tbl thead th.hpj-tgl-mulai { border-left: 2px solid rgba(255, 255, 255, .35) !important; }

/* Latar baris menurut Flexibility, kosakata yang sama dgn Update Payment
   Plan & Projection Report. Ditaruh di <td> karena .table-striped mewarnai
   <tr> dan latar <td> menang atasnya. */
#hpj-det-tbl tbody tr                { --flx-bg: #ffffff; --flx-hov: #eef4ff; --flx-bar: transparent; }
#hpj-det-tbl tbody tr.hpj-flx-green  { --flx-bg: #f1faf4; --flx-hov: #e2f3e8; --flx-bar: #34a853; }
#hpj-det-tbl tbody tr.hpj-flx-yellow { --flx-bg: #fffdf2; --flx-hov: #fdf5e0; --flx-bar: #e0a800; }
#hpj-det-tbl tbody tr.hpj-flx-red    { --flx-bg: #fdeeec; --flx-hov: #fadbd7; --flx-bar: #ea4335; }
#hpj-det-tbl tbody tr > td       { background: var(--flx-bg); }
#hpj-det-tbl tbody tr:hover > td { background: var(--flx-hov); }
#hpj-det-tbl tbody tr > td:first-child { box-shadow: inset 4px 0 0 var(--flx-bar); }

/* Lencana Type, kosakata warna sama dgn History Projection di AR:
   weekly kuning, monthly hijau, daily biru. */
.hpj-badge { display: inline-block; padding: 2px 10px; border-radius: 20px;
             font-size: 11px; font-weight: 700; text-transform: lowercase; }
.hpj-badge.daily   { background: #dbeafe; color: #1e40af; }
.hpj-badge.weekly  { background: #fef3c7; color: #92400e; }
.hpj-badge.monthly { background: #d1fae5; color: #065f46; }
.hpj-badge.lain    { background: #e2e8f0; color: #334155; }
.hpj-aksi { display: inline-flex; gap: 6px; }

.hpj-warn { background: #fdecea; border: 1px solid #f5c2bd; color: #a3281c;
            border-radius: 8px; padding: 9px 12px; margin-bottom: 10px; font-size: 12.5px; }
.hpj-head-aksi { margin-left: auto; }
.hpj-info { display: flex; flex-wrap: wrap; gap: 6px 26px; margin-bottom: 12px; }
.hpj-info div { font-size: 12.5px; color: #475569; }
.hpj-info b { color: #0f172a; }
#hpj-belum { padding: 22px; text-align: center; color: #94a3b8; font-size: 12.5px; }
</style>

<div class="container-fluid mt-3 p-3">

    <div class="ftl-card">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
            <div>
                <h1>AP History Projection</h1>
                <span class="ftl-crumb">AP &rsaquo; Report &rsaquo; Projection &rsaquo; History Projection</span>
            </div>
        </div><!-- /.ftl-head -->

        <div class="ftl-body">
            <form id="hpj-form" onsubmit="return false;">
                <div class="row">
                    <!-- Type DI KIRI Created From (diminta pemakai 6 Nov
                         2026). Isinya sama dgn Type di layar Projection
                         Report, ditambah ALL supaya daftar bisa dilihat utuh
                         tanpa menyaring. -->
                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="hpj-type" class="form-label"><b>Type</b></label>
                        <select class="form-control form-control-sm" id="hpj-type">
                            <option value="all" selected="selected">All</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <!-- Saringannya tanggal DIBUATNYA dokumen, bukan periode
                         laporannya - sama dgn History Projection di AR. Yang
                         dicari orang biasanya "yang saya simpan kemarin". -->
                    <div class="col-12 col-sm-6 col-xl-3 mb-2">
                        <label for="hpj-dari" class="form-label"><b>Created From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="hpj-dari"
                               value="<?php echo date('01-m-Y'); ?>" autocomplete="off">
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 mb-2">
                        <label for="hpj-sampai" class="form-label"><b>Created To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="hpj-sampai"
                               value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>
                    <div class="col-12 col-xl-3 mb-2 ftl-actions">
                        <button type="button" class="app-btn app-btn-primary app-btn-ctl app-btn-sm" id="hpj-cari">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card -->

    <div class="ftl-card mt-3">
        <div class="ftl-body">
            <div class="app-loading-wrap" id="hpjLoad">
                <div class="app-loading">
                    <div class="app-loading-box">
                        <div class="app-spinner"></div>
                        <div class="app-loading-text">Loading...</div>
                    </div>
                </div>
                <table id="hpj-tbl" class="table ftl-tbl" cellspacing="0">
                    <thead><tr></tr></thead>
                    <tbody></tbody>
                </table>
            </div><!-- /.app-loading-wrap -->
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card -->

    <div class="ftl-card mt-3" id="hpj-kartu-det">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-table" aria-hidden="true"></i></span>
            <div>
                <h1 id="hpj-det-judul">Document detail</h1>
                <span class="ftl-crumb" id="hpj-det-sub">Open a document from the list above.</span>
            </div>
            <!-- Ekspor Excel dokumen yang SEDANG terbuka. Ditaruh di kepala
                 kartu, bukan di panel saringan di atas, karena yang diekspor
                 satu dokumen - bukan hasil pencarian. margin-left:auto yang
                 mendorongnya ke kanan; .ftl-head sudah flex & flex-wrap, jadi
                 di layar sempit tombolnya turun sendiri, tidak menindih judul.
                 Disembunyikan sampai ada dokumen yang dibuka - tombol yang
                 ditekan sebelum itu hanya menghasilkan berkas kosong. -->
            <div class="hpj-head-aksi" id="hpj-det-aksi" style="display:none;">
                <button type="button" class="app-btn app-btn-excel app-btn-ctl app-btn-sm" id="hpj-excel">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                </button>
            </div>
        </div>
        <div class="ftl-body">
            <div id="hpj-peringatan"></div>
            <div class="hpj-info" id="hpj-det-info"></div>
            <div class="app-loading-wrap" id="hpjDetLoad">
                <div class="app-loading">
                    <div class="app-loading-box">
                        <div class="app-spinner"></div>
                        <div class="app-loading-text">Loading the snapshot...</div>
                    </div>
                </div>
                <div id="hpj-belum">No document opened yet.</div>
                <table id="hpj-det-tbl" class="table ftl-tbl" cellspacing="0" style="display:none;">
                    <thead><tr></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div><!-- /.ftl-card -->

</div><!-- /.container-fluid -->

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<!-- Penjaga rentang From/To, dipakai bersama beberapa laporan AP.
     SESUDAH jQuery, bootstrap-datepicker dan SweetAlert. -->
<script language="JavaScript" src="app_rentang_tanggal.js"></script>

<script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide');
  $('#collapse-icon').addClass('fa-angle-double-left');
  $('[data-toggle=sidebar-colapse]').click(function () { SidebarCollapse(); });
  function SidebarCollapse () {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) { SeparatorTitle.removeClass('d-flex'); }
      else { SeparatorTitle.addClass('d-flex'); }
      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
$(document).ready(function () {

  $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
  /* Created To tidak boleh lebih kecil dari Created From. Kalendernya
     dikunci di sini; penjaga saat Search ada di cariDaftar(). */
  apRentangTanggal('hpj-dari', 'hpj-sampai');

  function lolos(v) {
    return $('<div>').text(v === null || v === undefined ? '' : v).html();
  }

  /* ================================================== DAFTAR DOKUMEN ===== */
  var tblDaftar = null;

  /* Susunannya mengikuti History Projection di ar_dev: periode dipecah jadi
     FROM dan TO, Type jadi lencana berwarna, jumlah baris disebut Total
     Voucher (padanan "Total Invoice" di AR), dan nilainya Total Payment
     Amount (padanan "Total Collection Amount").

     DUA BEDA yang disengaja:
     - Kolom SUPPLIER tetap ada walau AR tidak punya padanannya: potret AP
       bisa disaring per supplier, jadi tanpa kolom itu dua dokumen periode
       sama yang beda supplier tampak persis sama.
     - (6 Nov 2026, menyusul) Tombol REALIZATION sekarang ADA. Catatan di
       bawah ini ditulis waktu laporannya belum dibuat; begitu
       ap_realization_report.php jadi, tombolnya dipasang - dan memang HARUS
       dari sini, karena laporan itu tidak punya menu sendiri (acuannya satu
       dokumen proyeksi, bukan rentang tanggal). Catatan lamanya:
     - Tombol REALIZATION milik AR TIDAK dibuat: laporan Realization &
       Variance versi AP belum ada, jadi tombolnya tidak punya tujuan. */
  var KOLOM_DAFTAR = [
    { data: 'no',             title: 'No',              kelas: 'text-center' },
    { data: 'doc_number',     title: 'Doc Number',      kelas: 'text-left' },
    { data: 'periode_dari',   title: 'Periode From',    kelas: 'text-center' },
    { data: 'periode_sampai', title: 'Periode To',      kelas: 'text-center' },
    { data: 'type',           title: 'Type',            kelas: 'text-center' },
    { data: 'supplier',       title: 'Supplier',        kelas: 'text-left' },
    { data: 'baris',          title: 'Total Voucher',   kelas: 'text-right' },
    { data: 'total',          title: 'Total Payment Amount', kelas: 'text-right' },
    { data: 'created_by',     title: 'Saved By',        kelas: 'text-left' },
    { data: 'created_at',     title: 'Saved At',        kelas: 'text-center' },
    { data: null,             title: 'Action',          kelas: 'text-center' }
  ];

  function gambarDaftar(res) {
    if (tblDaftar) { tblDaftar.destroy(); tblDaftar = null; }
    $('#hpj-tbl thead').empty();
    $('#hpj-tbl tbody').empty();

    var th = '';
    for (var i = 0; i < KOLOM_DAFTAR.length; i++) {
      th += '<th>' + lolos(KOLOM_DAFTAR[i].title) + '</th>';
    }
    $('#hpj-tbl thead').html('<tr>' + th + '</tr>');

    var kolom = [], defs = [];
    for (var a = 0; a < KOLOM_DAFTAR.length; a++) {
      var kd = { data: KOLOM_DAFTAR[a].data };
      if (KOLOM_DAFTAR[a].title === 'Action') {
        kd.data = null;
        kd.orderable = false;
        kd.render = function (d, t, row) {
          return '<span class="hpj-aksi">'
               + '<button type="button" class="app-btn app-btn-primary app-btn-sm hpj-buka"'
               + ' data-doc="' + lolos(row.doc_number) + '">'
               + '<i class="fa fa-eye" aria-hidden="true"></i> View</button>'
               /* Realization membuka layar lain (laporan itu tidak punya
                  menu sendiri), jadi nomor dokumennya lewat querystring -
                  bukan dimuat ke dalam kartu rincian di bawah. */
               + '<button type="button" class="app-btn app-btn-success app-btn-sm hpj-realisasi"'
               + ' data-doc="' + lolos(row.doc_number) + '">'
               + '<i class="fa fa-balance-scale" aria-hidden="true"></i> Realization</button>'
               + '<button type="button" class="app-btn app-btn-danger app-btn-sm hpj-batal"'
               + ' data-doc="' + lolos(row.doc_number) + '">'
               + '<i class="fa fa-ban" aria-hidden="true"></i> Cancel</button>'
               + '</span>';
        };
      } else if (KOLOM_DAFTAR[a].data === 'type') {
        kd.render = function (d, t) {
          if (t !== 'display') { return d; }
          var k = (d || '').toString().toLowerCase();
          var sah = (k === 'daily' || k === 'weekly' || k === 'monthly') ? k : 'lain';
          return '<span class="hpj-badge ' + sah + '">' + lolos(d) + '</span>';
        };
      } else if (KOLOM_DAFTAR[a].data === 'baris') {
        /* Jumlah baris dihitung ulang dari rinciannya. Kalau tidak sama dgn
           yang tersimpan di kepala, ada rincian yang hilang - harus
           kelihatan, bukan ditutupi angka kepala. */
        kd.render = function (d, t, row) {
          if (t !== 'display') { return d; }
          if (row.utuh) { return lolos(d); }
          return '<span title="Header says ' + lolos(row.baris_kepala)
               + ' row(s) but ' + lolos(d) + ' were found" style="color:#a3281c;font-weight:700;">'
               + lolos(d) + ' / ' + lolos(row.baris_kepala) + '</span>';
        };
      }
      kolom.push(kd);
      defs.push({ targets: a, className: KOLOM_DAFTAR[a].kelas });
    }

    tblDaftar = $('#hpj-tbl').DataTable({
      data: res.data || [],
      columns: kolom,
      columnDefs: defs,
      ordering: false,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      autoWidth: false,
      language: {
        emptyTable: 'No saved projection in this period.',
        zeroRecords: 'No document matches your search.'
      }
    });
  }

  function cariDaftar() {
    /* Lapis kedua: isiannya teks biasa, jadi rentang terbalik masih bisa
       masuk lewat ketikan tangan atau tempelan clipboard. */
    if (!apRentangSah('hpj-dari', 'hpj-sampai')) { return; }
    $('#hpjLoad').addClass('is-loading');
    $.ajax({ url: 'ajx_history_projection_list.php', type: 'POST', dataType: 'json',
             data: { start_date: $('#hpj-dari').val(), end_date: $('#hpj-sampai').val(),
                     type: $('#hpj-type').val() } })
      .done(function (res) {
        $('#hpjLoad').removeClass('is-loading');
        if (res && res.error) {
          gambarDaftar({ data: [] });
          Swal.fire({ icon: 'warning', title: 'Could not load the list', text: res.error });
          return;
        }
        gambarDaftar(res || { data: [] });
      })
      .fail(function (xhr) {
        $('#hpjLoad').removeClass('is-loading');
        Swal.fire({ icon: 'error', title: 'Failed to load',
                    text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response') });
      });
  }

  /* ================================================== RINCIAN DOKUMEN ==== */
  var tblDet = null;
  /* Nomor dokumen yang SEDANG terbuka di kartu bawah - dipakai tombol Excel.
     Disimpan di peubah sendiri, BUKAN dibaca ulang dari teks judulnya:
     judulnya kembali jadi 'Document detail' waktu dokumennya dibatalkan, dan
     tombol yang membaca judul akan mengekspor dokumen bernama itu. */
  var docDibuka = '';

  var KOLOM_TETAP = [
    { data: 'no',             title: 'No',                   kelas: 'text-center' },
    { data: 'supplier',       title: 'Supplier',             kelas: 'text-left hpj-supp' },
    { data: 'no_kbon',        title: 'Payment Voucher No',   kelas: 'text-left' },
    { data: 'tgl_kbon',       title: 'PV Date',              kelas: 'text-center' },
    { data: 'term',           title: 'Payment Term',         kelas: 'text-center' },
    { data: 'duedate',        title: 'Due Date',             kelas: 'text-center' },
    { data: 'tgl_proyeksi',   title: 'Payment Plan Date',    kelas: 'text-center' },
    { data: 'relasi',         title: 'Relationship',         kelas: 'text-left' },
    { data: 'item_type',      title: 'Item Type',            kelas: 'text-left' },
    { data: 'flexibility',    title: 'Flexibility Category', kelas: 'text-center' },
    { data: 'curr',           title: 'Currency',             kelas: 'text-center' },
    { data: 'invoice_amount', title: 'Invoice Amount',       kelas: 'text-right' },
    { data: 'rate',           title: 'Rate',                 kelas: 'text-right' },
    { data: 'tax_base',       title: 'Tax Base',             kelas: 'text-right', grup: 'payable' },
    { data: 'vat',            title: 'VAT',                  kelas: 'text-right', grup: 'payable' },
    { data: 'total_invoice',  title: 'Total Invoice',        kelas: 'text-right', grup: 'payable' },
    { data: 'pph',            title: 'Income Tax Art 23',    kelas: 'text-right', grup: 'payable' },
    { data: 'payment_amount', title: 'Payment Amount',       kelas: 'text-right', grup: 'payable' }
  ];
  var GRUP_PAYABLE  = 'Payable Amount';
  var GRUP_PROYEKSI = 'Projected Cash Outflow for Accounts Payable Payment';

  function kodeFlex(v) {
    var k = (v || '').toString().trim().toUpperCase();
    return (k === 'GREEN' || k === 'YELLOW' || k === 'RED') ? k.toLowerCase() : '';
  }

  function bungkusGulir() {
    var t = $('#hpj-det-tbl');
    if (!t.parent().hasClass('hpj-scroll')) { t.wrap('<div class="hpj-scroll"></div>'); }
  }

  function gambarDetail(res) {
    if (tblDet) { tblDet.destroy(); tblDet = null; }
    if ($('#hpj-det-tbl').parent().hasClass('hpj-scroll')) { $('#hpj-det-tbl').unwrap(); }
    $('#hpj-det-tbl thead').empty();
    $('#hpj-det-tbl tfoot').remove();
    $('#hpj-det-tbl tbody').empty();

    var kolomTgl = res.kolom || [];
    var baris    = res.data  || [];
    var i        = res.info  || {};

    $('#hpj-det-judul').text(i.doc_number || 'Document detail');
    $('#hpj-det-sub').text((i.periode || '-') + '  ·  ' + (i.type || '-')
                           + '  ·  ' + (i.supplier || 'ALL'));

    docDibuka = i.doc_number || '';
    $('#hpj-det-aksi').toggle(docDibuka !== '');

    var kurs = '';
    if (i.kurs) {
      for (var m in i.kurs) {
        if (!Object.prototype.hasOwnProperty.call(i.kurs, m)) { continue; }
        kurs += (kurs ? ', ' : '') + m + ' ' + lolos(i.kurs[m].rate)
              + ' (' + lolos(i.kurs[m].tanggal) + ')';
      }
    }
    $('#hpj-det-info').html(
        '<div>Rows: <b>' + lolos(i.baris) + '</b></div>'
      + '<div>Total: <b>' + lolos(i.total) + '</b></div>'
      + '<div>Outstanding as of: <b>' + lolos(i.per_tanggal) + '</b></div>'
      + '<div>Saved by: <b>' + lolos(i.created_by) + '</b> on <b>' + lolos(i.created_at) + '</b></div>'
      + (kurs ? '<div>Rate used: <b>' + kurs + '</b></div>' : ''));

    /* Dua hal yang WAJIB diperingatkan, bukan didiamkan: potret yang
       jumlahnya tidak lagi cocok dgn yang tersimpan, dan baris yang tanggal
       rencananya di luar periode dokumennya (tidak punya kolom tempat
       berdiri, jadi hilang dari jumlah per kolom). */
    var warn = '';
    if (i.utuh === false) {
      warn += '<div class="hpj-warn"><b>This snapshot no longer adds up.</b> '
            + 'The header says ' + lolos(i.total_tersimpan) + ' over '
            + lolos(i.baris_tersimpan) + ' row(s), but the rows found total '
            + lolos(i.total) + ' over ' + lolos(i.baris) + '. Some detail rows are missing.</div>';
    }
    if (i.di_luar_periode && i.di_luar_periode.length) {
      warn += '<div class="hpj-warn"><b>Outside the period:</b> '
            + lolos(i.di_luar_periode.join(', '))
            + ' - these rows have no date column to sit in, so they are not in the column totals.</div>';
    }
    $('#hpj-peringatan').html(warn);

    if (!baris.length) {
      $('#hpj-det-tbl').hide();
      $('#hpj-belum').show().text('This document has no rows.');
      return;
    }
    $('#hpj-belum').hide();
    $('#hpj-det-tbl').show();

    /* ---- kepala DUA TINGKAT, sama dgn layar Projection Report ---- */
    var nPayable = 0;
    for (var p = 0; p < KOLOM_TETAP.length; p++) {
      if (KOLOM_TETAP[p].grup === 'payable') { nPayable++; }
    }
    var thAtas = '', thBawah = '', payableDitulis = false;
    for (var q = 0; q < KOLOM_TETAP.length; q++) {
      var kk = KOLOM_TETAP[q];
      if (kk.grup === 'payable') {
        if (!payableDitulis) {
          thAtas += '<th colspan="' + nPayable + '" class="hpj-grp-pay">' + GRUP_PAYABLE + '</th>';
          payableDitulis = true;
        }
        thBawah += '<th class="hpj-sub-pay">' + kk.title + '</th>';
      } else {
        thAtas += '<th rowspan="2">' + kk.title + '</th>';
      }
    }
    if (kolomTgl.length) {
      thAtas += '<th colspan="' + kolomTgl.length
              + '" class="hpj-tgl-mulai hpj-grp-prj">' + GRUP_PROYEKSI + '</th>';
      for (var j = 0; j < kolomTgl.length; j++) {
        thBawah += '<th class="hpj-sub-prj' + (j === 0 ? ' hpj-tgl-mulai' : '')
                 + '">' + lolos(kolomTgl[j].label) + '</th>';
      }
    }
    $('#hpj-det-tbl thead').html('<tr>' + thAtas + '</tr><tr>' + thBawah + '</tr>');

    if (kolomTgl.length) {
      var tf = '<tfoot><tr><th colspan="' + KOLOM_TETAP.length
             + '" style="text-align:right;">Projected Cash Outflow</th>';
      for (var m2 = 0; m2 < kolomTgl.length; m2++) {
        tf += '<th style="text-align:right;">' + lolos(kolomTgl[m2].total) + '</th>';
      }
      $('#hpj-det-tbl').append(tf + '</tr></tfoot>');
    }

    var kolom = [], defs = [];
    for (var a2 = 0; a2 < KOLOM_TETAP.length; a2++) {
      var kd2 = { data: KOLOM_TETAP[a2].data };
      if (KOLOM_TETAP[a2].data === 'tgl_proyeksi') {
        kd2.render = function (d, t, row) {
          if (t !== 'display') { return d; }
          return '<span title="' + lolos(row.basis || '') + '">' + lolos(d) + '</span>';
        };
      }
      kolom.push(kd2);
      defs.push({ targets: a2, className: KOLOM_TETAP[a2].kelas });
    }
    for (var b2 = 0; b2 < kolomTgl.length; b2++) {
      (function (kunci, idx) {
        kolom.push({
          data: kunci,
          render: function (d, t) {
            if (t !== 'display') { return d || ''; }
            return (d === '' || d === null || d === undefined)
                   ? '<span class="hpj-kosong">-</span>' : d;
          }
        });
        defs.push({ targets: KOLOM_TETAP.length + idx,
                    className: 'text-right' + (idx === 0 ? ' hpj-tgl-mulai' : '') });
      })(kolomTgl[b2].key, b2);
    }

    tblDet = $('#hpj-det-tbl').DataTable({
      data: baris,
      columns: kolom,
      columnDefs: defs,
      ordering: false,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
      autoWidth: false,
      createdRow: function (row, data) {
        var kode = kodeFlex(data.flexibility);
        if (kode !== '') { $(row).addClass('hpj-flx-' + kode); }
      },
      language: {
        emptyTable: 'This document has no rows.',
        zeroRecords: 'No row matches your search.'
      }
    });

    bungkusGulir();
  }

  function bukaDokumen(doc) {
    $('#hpjDetLoad').addClass('is-loading');
    $.ajax({ url: 'ajx_history_projection_detail.php', type: 'POST', dataType: 'json',
             data: { doc_number: doc } })
      .done(function (res) {
        $('#hpjDetLoad').removeClass('is-loading');
        if (res && res.error) {
          Swal.fire({ icon: 'warning', title: 'Could not open the document', text: res.error });
          return;
        }
        gambarDetail(res || {});
        $('html, body').animate({ scrollTop: $('#hpj-kartu-det').offset().top - 20 }, 250);
      })
      .fail(function (xhr) {
        $('#hpjDetLoad').removeClass('is-loading');
        Swal.fire({ icon: 'error', title: 'Failed to open',
                    text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response') });
      });
  }

  /* Tombolnya digambar ulang tiap halaman DataTables, jadi penangannya
     didelegasikan - bukan dipasang satu per satu ke tombol yang ada
     sekarang, yang akan mati begitu pindah halaman. */
  $('#hpj-tbl').on('click', '.hpj-buka', function () {
    bukaDokumen($(this).data('doc'));
  });

  /* ---- Export Excel dokumen yang sedang terbuka -------------------------
     Memakai window.location, bukan ajax: jawabannya berkas unduhan, bukan
     JSON. Nomor dokumennya di-encode karena memuat garis miring
     (AP-PPR/1126/00001) - tanpa itu potongan sesudah garis miring pertama
     dibaca sbg bagian alamat, bukan nilai.

     ekspor_history_projection.php membaca dokumen yang SAMA lewat endpoint
     yang sama dgn layar ini, jadi isinya tidak bisa berbeda dari yang
     terlihat - termasuk kurs dan rencana bayar yang dibekukan di potretnya. */
  $('#hpj-excel').on('click', function () {
    if (docDibuka === '') {
      Swal.fire({ icon: 'info', title: 'No document opened',
                  text: 'Open a document from the list above first.' });
      return;
    }
    Swal.fire({ title: 'Preparing Excel...', allowOutsideClick: false,
                didOpen: function () { Swal.showLoading(); } });
    window.location.href = 'ekspor_history_projection.php?doc_number='
                         + encodeURIComponent(docDibuka);
    /* Unduhan tidak memuat ulang halaman, jadi tidak ada peristiwa yang bisa
       ditunggu - kotaknya ditutup setelah jeda singkat, sama dgn layar
       Projection dan Payment Plan. */
    setTimeout(function () { Swal.close(); }, 1500);
  });

  /* ---- Realization: pindah ke laporan Realization & Variance ----
     Nomor dokumennya di-encode: nomornya memuat garis miring
     (AP-PPR/1026/00001), dan tanpa encodeURIComponent potongan sesudah
     garis miring pertama akan dibaca sbg bagian alamat, bukan nilai. */
  $('#hpj-tbl').on('click', '.hpj-realisasi', function () {
    window.location = 'ap_realization_report.php?doc_number='
                    + encodeURIComponent($(this).data('doc'));
  });

  /* ---- Cancel: satu-satunya yang mengubah data di layar ini ----
     Dimintai konfirmasi dgn menyebut nomor dokumennya, dan dikatakan apa
     akibatnya - dokumennya diarsipkan, bukan dilenyapkan. Pemakai yang
     mengira "cancel" berarti hilang selamanya akan ragu menekannya. */
  $('#hpj-tbl').on('click', '.hpj-batal', function () {
    var doc = $(this).data('doc');
    Swal.fire({
      icon: 'warning',
      title: 'Cancel this document?',
      html: '<b>' + lolos(doc) + '</b> will be moved to the cancel archive '
          + 'and will no longer appear in this list.<br>'
          + '<small>The snapshot itself is kept, not erased.</small>',
      showCancelButton: true,
      confirmButtonText: 'Yes, cancel it',
      cancelButtonText: 'Keep it',
      confirmButtonColor: '#b3312c'
    }).then(function (ya) {
      if (!ya.isConfirmed) { return; }
      $('#hpjLoad').addClass('is-loading');
      $.ajax({ url: 'batal_history_projection.php', type: 'POST', dataType: 'json',
               data: { doc_number: doc } })
        .done(function (res) {
          $('#hpjLoad').removeClass('is-loading');
          if (!res || res.status !== 'success') {
            Swal.fire({ icon: 'warning', title: 'Could not cancel',
                        text: (res && res.message) ? res.message : 'Unknown response.' });
            return;
          }
          /* Kalau dokumen yang dibatalkan itu yang sedang terbuka di kartu
             bawah, rinciannya ikut dikosongkan - membiarkannya terpampang
             membuat orang mengira dokumennya masih ada. */
          if ($('#hpj-det-judul').text() === doc) {
            if (tblDet) { tblDet.destroy(); tblDet = null; }
            $('#hpj-det-tbl').hide().find('thead,tbody').empty();
            $('#hpj-det-tbl tfoot').remove();
            $('#hpj-det-judul').text('Document detail');
            $('#hpj-det-sub').text('Open a document from the list above.');
            $('#hpj-det-info').empty();
            $('#hpj-peringatan').empty();
            $('#hpj-belum').show().text('No document opened yet.');
            docDibuka = '';
            $('#hpj-det-aksi').hide();
          }
          Swal.fire({ icon: 'success', title: 'Cancelled',
                      html: '<b>' + lolos(res.doc_number) + '</b> moved to the archive ('
                          + lolos(res.baris) + ' row(s)).' });
          cariDaftar();
        })
        .fail(function (xhr) {
          $('#hpjLoad').removeClass('is-loading');
          Swal.fire({ icon: 'error', title: 'Failed to cancel',
                      text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response') });
        });
    });
  });

  $('#hpj-cari').on('click', cariDaftar);
  $('#hpj-form').on('submit', function (e) { e.preventDefault(); cariDaftar(); });

  /* Daftarnya digambar kosong sejak halaman dibuka - kartunya tidak pernah
     kosong melompong, dan sudah berupa DataTable sungguhan. */
  gambarDaftar({ data: [] });
  cariDaftar();
});
</script>
