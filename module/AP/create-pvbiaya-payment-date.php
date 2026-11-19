<?php
/* ============================================================================
   Update Payment Date PV Biaya - FORMULIRNYA.
   Datanya dari ajx_cari-pv-biaya.php, disimpan lewat
   simpan_pvbiaya_payment_date.php. Keterangan panjang soal KEBIJAKAN (apa itu
   "masih outstanding", kenapa ambangnya >= 1, kenapa kategori tidak bisa
   diubah) ada di kedua berkas itu, tidak diulang di sini.

   Bentuknya disalin dari create-payment-plan.php (Update Payment Plan AP) -
   kartu .pco-, bagian Header / Find / hasil, warna baris menurut Supplier
   Priority, tombol .app-btn. Dua hal yang beda:
     - kolom Cash Flow Category TEKS MATI, tidak bisa diubah dari sini;
     - Due Date & Payment Plan SEBELAHAN (permintaan pemakai) supaya
       pergeseran tanggalnya kelihatan sebaris.

   header.php tidak memuat satu pun pustaka JS, jadi halaman ini memuat
   sendiri yang dibutuhkannya.
   ============================================================================ */
include '../header.php';

/* Daftar supplier untuk saringan - hanya yang benar-benar punya PV Biaya,
   supaya dropdown-nya tidak sepanjang master supplier (ribuan baris). */
$supplierList = array();
$qSup = mysqli_query($conn2, "SELECT DISTINCT nama_supp FROM tbl_pv_h
        WHERE status = 'Approved'
          AND nama_supp IS NOT NULL AND nama_supp <> ''
        ORDER BY nama_supp ASC");
if ($qSup) { while ($r = mysqli_fetch_assoc($qSup)) { $supplierList[] = $r['nama_supp']; } }
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-pco-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-pco-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">

<style>
/* Tabelnya lebar (12 kolom), jadi yang digulir ke samping HANYA kotak di
   sekeliling tabelnya - bukan seluruh halaman. */
.pvb-scroll { overflow-x: auto; }
#table-pv { width: 100%; font-size: 12.5px; }
#table-pv thead th {
  white-space: nowrap; font-size: 11.5px; letter-spacing: .02em;
  text-transform: uppercase; vertical-align: middle;
}
#table-pv tbody td { vertical-align: middle; white-space: nowrap; }
#table-pv tbody td.wrap { white-space: normal; min-width: 190px; }
/* Penanda DP/CBD di bawah nomor dokumen - sengaja redup, tidak ikut menarik
   perhatian dari nomornya sendiri. */
#table-pv tbody td.doc small.jenis {
  display: inline-block; font-size: 10px; letter-spacing: .04em;
  color: #64748b; border: 1px solid #cbd5e1; border-radius: 3px;
  padding: 0 4px; margin-top: 2px;
}
#table-pv .num { text-align: right; font-variant-numeric: tabular-nums; }
#table-pv .doc { font-weight: 600; color: #1e3a8a; }
#table-pv .in-tgl { width: 120px; }
#table-pv .in-ket { min-width: 230px; }

/* ---------- Cash Flow Category: TEKS MATI, bukan isian ----------
   Boleh dilihat supaya ketahuan PV mana yang datanya belum lengkap, TAPI
   tidak bisa diubah dari sini: tbl_pv_h.id_cash_flow dipakai juga Bank Out &
   Cash Flow Realisation, jadi satu salah klik bisa memindahkan nominal ke
   baris laporan yang salah tanpa jejak. Diisinya di Create/Edit Payment
   Voucher. Yang kosong hanya DITANDAI - tanpa kalimat perintah di layar. */
#table-pv td.ktg { white-space: normal; min-width: 240px; max-width: 300px;
                   color: #475569; font-size: 12px; line-height: 1.35; }
.ktg-kosong { display: inline-block; padding: 1px 8px; border-radius: 999px;
              font-size: 10.5px; font-weight: 700; letter-spacing: .02em;
              color: #8a5a00; background: #ffeec2; }

/* ---------- Supplier Priority: warna baris (disalin dari create-payment-plan.php)
   Garis tebal di tepi kiri, bukan latar penuh - supaya tulisannya tetap
   gampang dibaca dan baris tercentang tetap kelihatan bedanya. ---------- */
#table-pv tbody tr                { --flx-bg: #ffffff; --flx-hov: #eef4ff; --flx-bar: transparent; }
#table-pv tbody tr.app-flx-green  { --flx-bg: #f1faf4; --flx-hov: #e2f3e8; --flx-bar: #34a853; }
#table-pv tbody tr.app-flx-yellow { --flx-bg: #fffdf2; --flx-hov: #fdf5e0; --flx-bar: #e0a800; }
#table-pv tbody tr.app-flx-red    { --flx-bg: #fdeeec; --flx-hov: #fadbd7; --flx-bar: #ea4335; }
#table-pv tbody tr > td       { background: var(--flx-bg); }
#table-pv tbody tr:hover > td { background: var(--flx-hov); }
#table-pv tbody tr > td:first-child { box-shadow: inset 4px 0 0 var(--flx-bar); }
#table-pv tbody tr.dipilih > td:first-child { box-shadow: inset 4px 0 0 var(--flx-bar), inset 7px 0 0 #2563eb; }

.app-flx-ket { float: right; font-size: 11px; font-weight: 500; color: #64748b;
               text-transform: none; letter-spacing: 0; }
.app-flx-ket > span { margin-left: 13px; white-space: nowrap; }
.app-flx-dot { display: inline-block; width: 12px; height: 12px; border-radius: 3px;
               margin-right: 5px; vertical-align: -2px; border: 1px solid #e2e8f0; }
.app-flx-dot.is-green  { background: #f1faf4; box-shadow: inset 4px 0 0 #34a853; }
.app-flx-dot.is-yellow { background: #fffdf2; box-shadow: inset 4px 0 0 #e0a800; }
.app-flx-dot.is-red    { background: #fdeeec; box-shadow: inset 4px 0 0 #ea4335; }
.app-flx-dot.is-kosong { background: #ffffff; }

/* Lewat tanggal tetap tampil supaya bisa digeser - cuma ditandai. */
#table-pv .telat { color: #b91c1c; font-weight: 600; }
/* Sudah punya rencana: nomor dokumennya ditulis kecil di bawah tanggalnya. */
.plan-asal { display: block; margin-top: 2px; font-size: 10px; color: #94a3b8; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="pco-card">

    <div class="pco-head">
      <span class="pco-head-icon"><i class="fas fa-calendar-plus"></i></span>
      <div>
        <h1>Form Update Payment Date</h1>
        <span class="pco-crumb">Bank &rsaquo; Payment Plan &rsaquo; Update Payment Date &rsaquo; Create</span>
      </div>
    </div><!-- /.pco-head -->

    <div class="pco-body">

      <!-- ---------------- kepala dokumen ---------------- -->
      <div class="pco-sec"><i class="fa fa-file-text-o" aria-hidden="true"></i> Header</div>
      <div class="form-row">
        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="plan_date" class="form-label"><b>Payment Plan Date</b></label>
          <input type="text" class="form-control form-control-sm tanggal" id="plan_date"
                 value="<?php echo date('d M Y'); ?>" placeholder="dd Mon yyyy" autocomplete="off">
        </div>
        <div class="col-12 col-sm-8 col-xl-6 mb-2">
          <label for="keterangan" class="form-label"><b>Description</b></label>
          <!-- Isi di sini mengalir ke tiap baris yang DICENTANG, termasuk baris
               yang baru dicentang sesudahnya - sama dgn Update Payment Plan AP. -->
          <input type="text" class="form-control form-control-sm" id="keterangan" maxlength="255"
                 placeholder="Optional - ticked rows follow this text">
        </div>
      </div>

      <!-- ---------------- saringan voucher ---------------- -->
      <div class="pco-sec"><i class="fa fa-search" aria-hidden="true"></i> Find payment vouchers</div>
      <div class="form-row">
        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="dasar" class="form-label"><b>Date Basis</b></label>
          <select class="form-control app-select2" id="dasar">
            <option value="tempo">Due Date</option>
            <option value="pv">PV Date</option>
          </select>
        </div>
        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="start_date" class="form-label"><b>From</b></label>
          <input type="text" class="form-control form-control-sm tanggal" id="start_date"
                 value="<?php echo date('d M Y'); ?>" placeholder="Start Date" autocomplete="off">
        </div>
        <div class="col-12 col-sm-6 col-xl-2 mb-2">
          <label for="end_date" class="form-label"><b>To</b></label>
          <input type="text" class="form-control form-control-sm tanggal" id="end_date"
                 value="<?php echo date('d M Y'); ?>" placeholder="End Date" autocomplete="off">
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-2">
          <label for="supplier" class="form-label"><b>Supplier</b></label>
          <select class="form-control app-select2" id="supplier">
            <option value="" selected="selected">All Suppliers</option>
            <?php foreach ($supplierList as $s) { ?>
            <option value="<?php echo htmlspecialchars($s, ENT_QUOTES); ?>"><?php echo htmlspecialchars($s, ENT_QUOTES); ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-2 pco-actions align-items-end">
          <button type="button" class="app-btn app-btn-primary app-btn-ctl app-btn-sm" id="btn-cari">
            <i class="fa fa-search" aria-hidden="true"></i> Search
          </button>
        </div>
      </div><!-- /.form-row -->

      <!-- ---------------- hasil ---------------- -->
      <div class="pco-sec"><i class="fa fa-list" aria-hidden="true"></i> Payment vouchers
        <span class="text-muted ml-2" id="app-info"
              style="text-transform:none;letter-spacing:0;font-weight:500;">Pick a period, then press Search.</span>
        <span class="app-flx-ket">Supplier priority
          <span><i class="app-flx-dot is-green"></i>Green</span>
          <span><i class="app-flx-dot is-yellow"></i>Yellow</span>
          <span><i class="app-flx-dot is-red"></i>Red</span>
          <span><i class="app-flx-dot is-kosong"></i>Not set</span>
        </span>
      </div>

      <div class="app-loading-wrap" id="pvbLoad">
        <div class="pvb-scroll">
          <table id="table-pv" class="table table-striped table-bordered table-hover table-sm">
            <thead class="table-gradient">
              <tr>
                <th style="text-align:center;"><input type="checkbox" id="pilih-semua" title="Tick every row"></th>
                <th style="text-align:center;">Payment Voucher No</th>
                <th style="text-align:center;">PV Date</th>
                <th style="text-align:center;">Supplier</th>
                <th style="text-align:center;">Due Date</th>
                <th style="text-align:center;">Payment Plan</th>
                <th style="text-align:center;">Cash Flow Category</th>
                <th style="text-align:center;">Bank</th>
                <th style="text-align:center;">PC</th>
                <th style="text-align:center;">Curr</th>
                <th style="text-align:center;">Outstanding</th>
                <th style="text-align:center;">Description</th>
              </tr>
            </thead>
            <tbody id="isi-tabel">
              <tr><td colspan="12" class="text-center text-muted" style="padding:18px;">Pick a period, then press Search.</td></tr>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="10" style="text-align:right;">Total ticked</th>
                <th class="num" id="total-dipilih">0,00</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div><!-- /.app-loading-wrap -->

      <div class="pco-actions mt-3">
        <button type="button" class="app-btn app-btn-success app-btn-sm" id="btn-simpan" disabled>
          <i class="fa fa-save" aria-hidden="true"></i> Save
        </button>
        <a href="pvbiaya-payment-date.php" class="app-btn app-btn-danger app-btn-sm">
          <i class="fa fa-times" aria-hidden="true"></i> Cancel
        </a>
      </div>

    </div><!-- /.pco-body -->
  </div><!-- /.pco-card -->
</div><!-- /.container-fluid -->

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
});

/* Angka dibentuk dgn pemisah Indonesia (1.234,56) - sama dgn layar AP. */
function uang(n) {
  n = parseFloat(n) || 0;
  return n.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function tglTampil(ymd) {
  if (!ymd || ymd === '0000-00-00') { return '-'; }
  var b = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var p = ymd.split('-');
  return p[2] + '-' + b[parseInt(p[1], 10) - 1] + '-' + p[0];
}
function tglIsian(ymd) {
  if (!ymd || ymd === '0000-00-00') { return ''; }
  var b = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var p = ymd.split('-');
  return p[2] + ' ' + b[parseInt(p[1], 10) - 1] + ' ' + p[0];
}
function lolos(t) {
  return $('<div>').text(t === null || t === undefined ? '' : t).html();
}

var hariIni = '<?php echo date("Y-m-d"); ?>';

/* ---------------------------------------------------------------------------
   TABELNYA DIPAGING 10 BARIS (permintaan pemakai 19 Nov 2026), dan itu
   membawa satu bahaya yang harus dijaga: DataTables MENCABUT baris halaman
   lain dari halaman web. Kalau tombol Save membaca $('#isi-tabel tr') spt
   dulu, yang terbaca cuma 10 baris yang sedang terlihat - centang dan isian
   di halaman lain HILANG tanpa pesan apa pun.

   Penangkalnya satu alat bantu: semuaBaris(). DataTables menyimpan SELURUH
   elemen barisnya (rows().nodes()), termasuk yang sedang tidak ditampilkan,
   dan isian <input> di dalamnya tetap memegang nilainya. Jadi setiap
   pekerjaan yang menyangkut SEMUA baris - menghitung total, centang semua,
   mengalirkan Description, dan menyimpan - WAJIB lewat semuaBaris(), bukan
   lewat pencarian di dalam #isi-tabel.
   --------------------------------------------------------------------------- */
var pvbTabel = null;

function semuaBaris() {
  /* Sebelum tabelnya dibangun (atau waktu hasilnya kosong), jatuh ke isi
     halaman apa adanya - jadi alat ini aman dipanggil kapan saja. */
  return pvbTabel ? $(pvbTabel.rows().nodes()) : $('#isi-tabel tr');
}

$('#btn-cari').on('click', function () {
  var $w = $('#pvbLoad').addClass('is-loading');
  $('#app-info').text('Searching...');

  $.ajax({
    type: 'POST',
    url: 'ajx_cari-pv-biaya.php',
    dataType: 'json',
    data: {
      dasar: $('#dasar').val(),
      start_date: $('#start_date').val(),
      end_date: $('#end_date').val(),
      supplier: $('#supplier').val()
    }
  }).done(function (r) {
    $w.removeClass('is-loading');
    if (!r || r.status !== 'success') {
      $('#app-info').text('');
      Swal.fire({ icon: 'error', title: 'Failed', text: (r && r.message) ? r.message : 'Could not read the data.' });
      return;
    }
    gambar(r.data);
    $('#app-info').text(r.jumlah + ' voucher' + (r.jumlah === 1 ? '' : 's') + ' found · cash flow category is read-only');
  }).fail(function (xhr) {
    $w.removeClass('is-loading');
    $('#app-info').text('');
    Swal.fire({ icon: 'error', title: 'Failed',
      text: 'HTTP ' + (xhr && xhr.status ? xhr.status : '?') + ' - ' + (xhr && xhr.responseText ? xhr.responseText.substring(0, 300) : 'no response') });
  });
});

function gambar(data) {
  /* DataTables lama DIBONGKAR dulu - kalau tidak, ia masih memegang elemen
     baris pencarian sebelumnya dan barisnya akan tercampur. */
  if (pvbTabel) { pvbTabel.destroy(); pvbTabel = null; }
  var $b = $('#isi-tabel').empty();
  if (!data.length) {
    $b.append('<tr><td colspan="12" class="text-center text-muted" style="padding:18px;">No outstanding payment voucher in that period.</td></tr>');
    hitung();
    return;
  }
  var warna = { GREEN: 'app-flx-green', YELLOW: 'app-flx-yellow', RED: 'app-flx-red' };

  $.each(data, function (i, d) {
    /* Tanggal rencana diisi lebih dulu dgn rencana yang BERLAKU kalau ada;
       kalau belum pernah dijadwalkan, dipakai due date-nya sendiri - itu
       tebakan yang paling masuk akal dan tetap bisa diganti. */
    var plan = d.plan_sekarang ? d.plan_sekarang : d.due_date;
    var telat = (d.due_date && d.due_date < hariIni) ? ' class="telat"' : '';
    var ktg = d.kategori
            ? lolos(d.kategori)
            : '<span class="ktg-kosong">not set</span>';
    var asal = d.plan_doc ? '<span class="plan-asal">' + lolos(d.plan_doc) + '</span>' : '';

    $b.append(
      '<tr class="' + (warna[d.prioritas] || '') + '"' +
        ' data-nopv="' + lolos(d.no_pv) + '" data-pc="' + lolos(d.profit_center) + '"' +
        ' data-jenis="' + lolos(d.jenis || 'Biaya') + '"' +
        ' data-sisa="' + d.sisa + '">' +
        '<td style="text-align:center;"><input type="checkbox" class="pilih"></td>' +
        /* Jenisnya ditulis KECIL di bawah nomornya, bukan jadi kolom sendiri -
           kolom baru akan menggeser lebar yang sudah disetel pemakai, dan
           nomornya sendiri sudah setengah menjelaskan (PV-AP/CBD/...). */
        '<td class="doc">' + lolos(d.no_pv) +
          ((d.jenis && d.jenis !== 'Biaya') ? '<br><small class="jenis">' + lolos(d.jenis) + '</small>' : '') + '</td>' +
        '<td>' + tglTampil(d.pv_date) + '</td>' +
        '<td class="wrap">' + lolos(d.supplier) + '</td>' +
        '<td' + telat + '>' + tglTampil(d.due_date) + '</td>' +
        '<td><input type="text" class="form-control form-control-sm tanggal in-tgl" value="' + tglIsian(plan) + '" autocomplete="off">' + asal + '</td>' +
        '<td class="ktg">' + ktg + '</td>' +
        '<td>' + lolos(d.bank) + '</td>' +
        '<td>' + lolos(d.profit_center) + '</td>' +
        '<td>' + lolos(d.curr) + '</td>' +
        '<td class="num">' + uang(d.sisa) + '</td>' +
        '<td class="wrap"><input type="text" class="form-control form-control-sm in-ket" maxlength="255" placeholder="follows header" value="' + lolos(d.plan_ket) + '"></td>' +
      '</tr>');
  });

  /* Datepicker dipasang SESUDAH barisnya ada - isian yang belum lahir tidak
     bisa diikat. Dipasang SEBELUM DataTables, selagi seluruh baris masih di
     halaman; sesudah dipaging, baris halaman lain tidak bisa dicari lagi
     lewat $('#isi-tabel ...'). Ikatannya menempel pada elemennya, jadi tetap
     hidup waktu barisnya dipasang kembali di halaman berikutnya. */
  $('#isi-tabel .tanggal').datepicker({ format: 'dd M yyyy', autoclose: true, todayHighlight: true });

  pvbTabel = $('#table-pv').DataTable({
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
    /* Urutannya dari server (jatuh tempo lalu nomor) dan itu yang diharapkan
       pemakai, jadi pengurutan kolom dimatikan - sekalian supaya kepala
       kolomnya tidak bergeser. */
    ordering: false,
    autoWidth: false,
    language: { emptyTable: 'No outstanding payment voucher in that period.' }
  });

  $('#pilih-semua').prop('checked', false);
  hitung();
}

/* Description kepala mengalir ke baris yang DICENTANG - termasuk baris yang
   baru dicentang sesudahnya. Yang centangnya dilepas dikosongkan lagi. */
function alirkanDeskripsi($tr) {
  var teks = $('#keterangan').val();
  var sasaran = $tr ? $tr : semuaBaris();
  sasaran.each(function () {
    var $t = $(this);
    if ($t.find('.pilih').prop('checked')) { $t.find('.in-ket').val(teks); }
  });
}
$('#keterangan').on('input', function () { alirkanDeskripsi(); });

$('#isi-tabel').on('change', '.pilih', function () {
  var $tr = $(this).closest('tr');
  $tr.toggleClass('dipilih', this.checked);
  if (this.checked) { alirkanDeskripsi($tr); }
  else { $tr.find('.in-ket').val(''); $('#pilih-semua').prop('checked', false); }
  hitung();
});

$('#pilih-semua').on('change', function () {
  var c = this.checked;
  /* SEMUA baris, bukan cuma yang terlihat - tulisannya memang "Tick every
     row", dan yang di halaman lain ikut tersimpan.

     PEKERJAANNYA DILAKUKAN LANGSUNG, bukan dgn trigger('change'). Penangan
     .pilih di atas dipasang sbg DELEGASI pada #isi-tabel, sedangkan baris
     halaman lain sedang TIDAK berada di dalam #isi-tabel - jadi penangan itu
     tidak akan pernah kebagian. Gejalanya halus dan sempat kejadian: 55 baris
     tercentang dan totalnya benar, tetapi Description cuma mengalir ke 10
     baris yang kebetulan terlihat. */
  semuaBaris().each(function () {
    var $t = $(this), cb = $t.find('.pilih')[0];
    if (!cb || cb.checked === c) { return; }
    cb.checked = c;
    $t.toggleClass('dipilih', c);
    if (c) { alirkanDeskripsi($t); } else { $t.find('.in-ket').val(''); }
  });
  hitung();
});

function hitung() {
  var t = 0, n = 0;
  semuaBaris().each(function () {
    if ($(this).find('.pilih').prop('checked')) { t += parseFloat($(this).data('sisa')) || 0; n++; }
  });
  $('#total-dipilih').text(uang(t));
  $('#btn-simpan').prop('disabled', n === 0 || $('#plan_date').val() === '');
}
$('#plan_date').on('change input', hitung);

/* ---------------------------------------------------------------------------
   Menyimpan. Tombolnya dikunci selama permintaan berjalan supaya tidak
   tertekan dua kali - satu klik ganda akan membuat DUA dokumen.
   --------------------------------------------------------------------------- */
$('#btn-simpan').on('click', function () {
  var baris = [];
  /* semuaBaris(): yang tercentang di halaman mana pun ikut tersimpan. */
  semuaBaris().each(function () {
    var $t = $(this);
    if (!$t.find('.pilih').prop('checked')) { return; }
    baris.push({
      no_pv: $t.data('nopv'),
      jenis: $t.data('jenis') || 'Biaya',
      profit_center: $t.data('pc'),
      plan_date: $t.find('.in-tgl').val(),
      keterangan: $t.find('.in-ket').val()
    });
  });
  if (!baris.length) {
    Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Tick at least one payment voucher first.' });
    return;
  }

  Swal.fire({
    icon: 'question', title: 'Are you sure?',
    text: 'Save the payment date for ' + baris.length + ' voucher' + (baris.length === 1 ? '' : 's') + '?',
    showCancelButton: true, confirmButtonText: 'Yes, save it!', cancelButtonText: 'Cancel'
  }).then(function (h) {
    if (!h.isConfirmed) { return; }

    $('#btn-simpan').prop('disabled', true);
    Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

    $.ajax({
      type: 'POST', url: 'simpan_pvbiaya_payment_date.php', dataType: 'json',
      data: {
        plan_date: $('#plan_date').val(),
        keterangan: $('#keterangan').val(),
        baris: JSON.stringify(baris)
      }
    }).done(function (r) {
      if (!r || r.status !== 'success') {
        $('#btn-simpan').prop('disabled', false);
        var ket = (r && r.message) ? r.message : 'Could not save.';
        if (r && r.ditolak && r.ditolak.length) { ket += '\n\nSkipped:\n- ' + r.ditolak.join('\n- '); }
        Swal.fire({ icon: 'error', title: 'Failed', text: ket });
        return;
      }
      var pesan = r.tersimpan + ' voucher' + (r.tersimpan === 1 ? '' : 's') + ' scheduled in ' + r.doc_number + '.';
      if (r.digantikan && r.digantikan.length) { pesan += '\n\nRescheduled:\n- ' + r.digantikan.join('\n- '); }
      if (r.ditolak && r.ditolak.length)       { pesan += '\n\nSkipped:\n- ' + r.ditolak.join('\n- '); }
      Swal.fire({ icon: 'success', title: 'Saved', text: pesan })
          .then(function () { window.location = 'pvbiaya-payment-date.php'; });
    }).fail(function (xhr) {
      $('#btn-simpan').prop('disabled', false);
      Swal.fire({ icon: 'error', title: 'Failed',
        text: 'HTTP ' + (xhr && xhr.status ? xhr.status : '?') + ' - ' + (xhr && xhr.responseText ? xhr.responseText.substring(0, 300) : 'no response') });
    });
  });
});
</script>

</body>
</html>
