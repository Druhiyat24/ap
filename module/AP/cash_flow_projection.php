<?php
/* ============================================================================
   CASH FLOW PROJECTION REPORT - layarnya.

   Angkanya SELURUHNYA dari ajx_cash_flow_projection.php; keterangan soal
   sumber data, rumus saldo awal dan simulasi pinjaman ada di berkas itu,
   tidak diulang di sini.

   Susunan barisnya mengikuti lembar "Cash Flow Daily" di
   D:\File Project\Cash Flow Projection Report.xlsx - kolom Descriptions &
   Total menempel di kiri, satu kolom per tanggal menggulir ke kanan.
   Tabelnya BUKAN DataTables: barisnya tetap (56 kategori + baris jumlah),
   yang berubah-ubah cuma kolom tanggalnya.
   ============================================================================ */
include '../header.php';
?>
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link rel="stylesheet" href="../css/app-cf-matrix.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-cf-matrix.css'); ?>">

<style>
/* Gaya matriksnya ada di ../css/app-cf-matrix.css - dipakai bersama layar
   riwayatnya supaya keduanya tidak bisa berbeda tampilan. */

/* Tombol Opening Balance ditaruh di kepala KARTU, bukan di baris saringan:
   baris saringan sudah pas satu baris dan tombol keempat akan membuatnya
   turun. Tempatnya juga lebih masuk akal - tombol itu menyunting baris
   pertama tabel di bawahnya. */
.cfd-cap-kanan { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }

/* DISEMBUNYIKAN 19 Nov 2026 (permintaan pemakai): modal saldo awal sudah
   muncul sendiri waktu Search ditekan, jadi tombolnya mubazir. Yang
   disembunyikan HANYA tampilannya - markup & seluruh kodenya sengaja
   dibiarkan utuh, supaya memunculkannya lagi cukup menghapus satu baris di
   bawah ini. Catatan: selama tersembunyi, modalnya hanya bisa dibuka lewat
   Search PERTAMA untuk sebuah tanggal From (lihat cfoPernah di bawah). */
#cfd-opening { display:none; }

/* Saringan Bank bercentang. DISALIN dari filter Account di
   report-cashflow-realisation.php (permintaan pemakai: &quot;persis seperti menu
   realisasi&quot;) - kalau di sana diubah, ubah juga di sini. Gaya toggle
   switch-nya sendiri aslinya dari panel Manage Role di userrole.php. */
/* Kartu saringan TIDAK boleh memotong isinya: daftar rekeningnya
   menggantung KELUAR dari kartu, sedangkan .ftl-card bawaannya
   overflow:hidden - dan itulah yang membulatkan kepala birunya. Jadi
   pemotongnya dimatikan DI KARTU INI SAJA, lalu kepala birunya dibulatkan
   sendiri; tanpa baris kedua, sudut atas kartunya kembali siku. 15px, bukan
   16px: itu radius di DALAM garis tepi 1px, persis seperti yang dipotong
   overflow:hidden dulu. */
.cfd-kartu-saring { overflow:visible; }
.cfd-kartu-saring .ftl-head { border-radius:15px 15px 0 0; }

#cfd-bank-menu { min-width:320px; max-height:320px; overflow-y:auto; }
.cfd-bank-btn { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cfd-acc-panel { display:grid; grid-template-columns:1fr; gap:8px; }
.cfd-acc-all-row { border-bottom:1px solid #e2e8f0; margin-bottom:8px; padding-bottom:8px; }
.cfd-acc-row { display:flex; align-items:center; justify-content:space-between; gap:8px;
    padding:8px 10px; font-size:12.5px; background:#fff; border:1px solid #e2e8f0;
    border-radius:8px; transition:box-shadow .15s ease, border-color .15s ease; }
.cfd-acc-row:hover { border-color:#93c5fd; box-shadow:0 2px 6px rgba(37,99,235,.15); }
.cfd-acc-label { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.cfd-acc-all-row .cfd-acc-label { font-weight:700; }

.toggle-switch { position:relative; display:inline-block; width:38px; height:21px; flex-shrink:0; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0;
    background-color:#cbd5e1; transition:background-color .2s ease; border-radius:21px; }
.toggle-slider:before { position:absolute; content:""; height:15px; width:15px; left:3px; bottom:3px;
    background-color:#fff; transition:transform .2s ease; border-radius:50%;
    box-shadow:0 1px 3px rgba(0,0,0,.35); }
.toggle-switch input:checked + .toggle-slider { background-color:#1E3A8A; }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(17px); }
.toggle-switch input:focus + .toggle-slider { box-shadow:0 0 0 3px rgba(30,58,138,.25); }

/* Kotak To yang dikunci (Weekly/Monthly): terlihat BERBEDA, bukan cuma
   tidak bisa diklik - kotak yang tampak biasa tapi tidak menanggapi
   terbaca sbg aplikasi rusak. */
#cfd-sampai.cfd-kunci { background:#f1f5f9; color:#475569; cursor:not-allowed; }

#modal-opening .cfo-tbl { width:100%; font-size:12px; }
#modal-opening .cfo-tbl th { background:#1E3A8A !important; color:#fff !important;
    font-size:10.5px; letter-spacing:.3px; text-transform:uppercase; padding:7px 9px; }
#modal-opening .cfo-tbl td { padding:5px 9px; border-bottom:1px solid #eef2f7; vertical-align:middle; }
#modal-opening .cfo-num { text-align:right; font-variant-numeric:tabular-nums; }
#modal-opening .cfo-tbl input { width:100%; border:1px solid #cbd5e1; border-radius:6px;
    padding:4px 7px; font-size:12px; }
#modal-opening .cfo-tbl input.cfo-nilai { text-align:right; font-variant-numeric:tabular-nums; }
#modal-opening .cfo-tbl input.cfo-isi { border-color:#2563eb; background:#eff6ff; }
#modal-opening tr.cfo-jum td { font-weight:700; background:#f1f5f9; border-top:2px solid #cbd5e1; }
#modal-opening .cfo-kecil { font-size:11px; color:#94a3b8; }
#modal-opening .cfo-log { max-height:230px; overflow:auto; border:1px solid #e2e8f0;
    border-radius:8px; margin-top:6px; }
#modal-opening .cfo-log table { width:100%; font-size:11.5px; }
#modal-opening .cfo-log th { position:sticky; top:0; background:#f1f5f9; color:#475569;
    font-size:10.5px; text-transform:uppercase; padding:6px 9px; }
#modal-opening .cfo-log td { padding:5px 9px; border-top:1px solid #eef2f7; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">

    <!-- cfd-kartu-saring: kartu ini TIDAK boleh memotong isinya, lihat
         keterangannya di blok style di atas. -->
    <div class="ftl-card cfd-kartu-saring">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
            <div>
                <h1>Cash Flow Projection Report</h1>
                <span class="ftl-crumb">Bank &rsaquo; Report &rsaquo; Projection &rsaquo; Projection Report</span>
            </div>
        </div><!-- /.ftl-head -->

        <div class="ftl-body">
            <form id="cfd-form" onsubmit="return false;">
                <div class="form-row">

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="cfd-pc" class="form-label"><b>Profit Center</b></label>
                        <select class="form-control app-select2" id="cfd-pc">
                            <option value="ALL" selected="selected">All PC</option>
                            <option value="NAG">Garment (NAG)</option>
                            <option value="NAK">Knitting (NAK)</option>
                        </select>
                    </div>

                    <!-- BANK BISA DICENTANG BEBERAPA (permintaan pemakai 19 Nov
                         2026: &quot;persis seperti menu realisasi&quot;). Bentuknya
                         ditiru dari filter Account di report-cashflow-realisation.php:
                         dropdown berisi kartu toggle switch, baris ALL di atas,
                         dan minimal satu rekening harus tetap menyala. Isinya
                         diisi JS dari daftar rekening yang sama dgn laporannya. -->
                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label class="form-label"><b>Bank</b></label>
                        <div class="dropdown">
                            <button type="button" class="form-control form-control-sm text-left dropdown-toggle cfd-bank-btn"
                                    id="cfd-bank-btn" data-toggle="dropdown" aria-expanded="false">All Banks</button>
                            <div class="dropdown-menu p-2" id="cfd-bank-menu">
                                <div class="cfd-acc-row cfd-acc-all-row">
                                    <span class="cfd-acc-label">ALL BANKS</span>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="cfd-acc-all" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="cfd-acc-panel" id="cfd-acc-panel"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Type: Daily dulu. Weekly & Monthly memakai TEMPLATE
                         LAIN (di Excel kolomnya per rekening bank, bukan per
                         minggu), jadi belum dibuka di sini. -->
                    <div class="col-12 col-sm-6 col-xl-1 mb-2">
                        <label for="cfd-type" class="form-label"><b>Type</b></label>
                        <select class="form-control app-select2" id="cfd-type">
                            <option value="daily" selected="selected">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="cfd-dari" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="cfd-dari"
                               value="<?php echo date('01-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="cfd-sampai" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="cfd-sampai"
                               value="<?php echo date('t-m-Y'); ?>" autocomplete="off">
                    </div>

                    <!-- Rate USD TIDAK diisi di sini (permintaan pemakai 16 Nov
                         2026). Mesinnya mengambil sendiri kurs PAJAK terakhir
                         yang tanggalnya <= tanggal To; angkanya tetap tercetak
                         di kepala laporan dan di Excel supaya bisa ditelusuri. -->

                    <!-- Lebar kolom disetel supaya SELURUH saringan + tombol muat
                         dalam SATU baris di layar lebar (permintaan pemakai
                         16 Nov 2026): 2+2+1+2+2+3 = 12. Di layar sempit tetap
                         turun sendiri lewat col-12/col-sm-6. -->
                    <div class="col-12 col-xl-3 mb-2 ftl-actions">
                        <button type="button" class="app-btn app-btn-primary app-btn-ctl app-btn-sm" id="cfd-cari">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <button type="button" class="app-btn app-btn-success app-btn-ctl app-btn-sm" id="cfd-xls">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                        </button>
                        <button type="button" class="app-btn app-btn-info app-btn-ctl app-btn-sm" id="cfd-simpan">
                            <i class="fa fa-save" aria-hidden="true"></i> Save History
                        </button>
                    </div>

                </div>
            </form>
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card -->

    <div class="cfd-wrap">
        <div class="cfd-cap">
            <b id="cfd-judul">Cash Flow Projection</b>
            <span class="cfd-cap-kanan">
                <span id="cfd-sub">Pick a type and a period, then click Search.</span>
                <button type="button" class="app-btn app-btn-light app-btn-sm" id="cfd-opening">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Opening Balance
                </button>
            </span>
        </div>
        <!-- Overlay spinner: menutupi AREA TABELNYA saja, bukan kepala kartu -
             judul & tombol Opening Balance tetap terbaca selagi menunggu.
             Markupnya sama persis dgn ap_projection_report.php. -->
        <div class="app-loading-wrap" id="cfdLoad">
            <div class="app-loading">
                <div class="app-loading-box">
                    <div class="app-spinner"></div>
                    <div class="app-loading-text" id="cfdLoadText">Building the projection...</div>
                </div>
            </div>

            <!-- DUA TAB untuk SEMUA type (permintaan pemakai 20 Nov 2026).
                 Isinya dua tata letak dari SATU jawaban yang sama, jadi
                 berpindah tab tidak memanggil server lagi. Disembunyikan
                 sampai ada hasilnya - pita tab di atas tabel kosong cuma
                 bikin bingung. -->
            <div class="cfd-tab-bar" id="cfd-tab-bar" style="display:none;">
                <button type="button" class="cfd-tab" data-tata="tgl">
                    <i class="fa fa-calendar" aria-hidden="true"></i> Report by Date
                </button>
                <button type="button" class="cfd-tab" data-tata="akun">
                    <i class="fa fa-university" aria-hidden="true"></i> Report by Bank
                </button>
            </div>

            <div class="cfd-scroll">
                <!-- Tabel lembar sumber: bentuknya kisi biasa, bukan matriks,
                     jadi tabelnya sendiri - kolom pertama matriks digayakan
                     sbg keterangan dan akan salah tempat di sini. -->
                <table class="cfd cfd-src" id="cfd-src" style="display:none;">
                    <thead></thead><tbody></tbody>
                </table>
                <table class="cfd" id="cfd-tbl">
                    <thead><tr><th>Descriptions</th><th class="cfd-num">Total</th></tr></thead>
                    <tbody><tr><td colspan="2" class="cfd-kosong">Pick a type and a period, then click Search.</td></tr></tbody>
                </table>
            </div>
        </div><!-- /.app-loading-wrap -->
    </div>

</div><!-- /MAIN -->

<!-- Saldo awal yang boleh diketik tangan. HANYA tanggal pertama - hari
     berikutnya adalah hitungan (saldo awal hari ini = saldo akhir kemarin),
     jadi tidak ada yang perlu diketik di sana. -->
<div class="modal fade" id="modal-opening" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-university" aria-hidden="true"></i>
            Opening Balance &mdash; <span id="cfo-tanggal"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body">
        <table class="cfo-tbl" id="cfo-tbl">
          <thead>
            <tr>
              <th>Account</th>
              <th style="width:52px;">Curr</th>
              <th class="cfo-num" style="width:170px;">System Balance (IDR)</th>
              <th class="cfo-num" style="width:180px;">Manual Balance (IDR)</th>
              <th style="width:240px;">Note</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <div style="margin-top:14px;">
          <b style="font-size:12px;">Change history for this date</b>
          <div class="cfo-log"><table><thead><tr>
            <th>When</th><th>By</th><th>Account</th><th>Action</th>
            <th class="cfo-num">From</th><th class="cfo-num">To</th><th>Note</th>
          </tr></thead><tbody id="cfo-log"></tbody></table></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="app-btn app-btn-dark app-btn-sm" data-dismiss="modal">Close</button>
        <button type="button" class="app-btn app-btn-primary app-btn-sm" id="cfo-simpan">
            <i class="fa fa-save" aria-hidden="true"></i> Save &amp; show report
        </button>
      </div>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
var CFD_HARI = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

function cfdAngka(v) {
    if (v === null || v === undefined || v === '') { return ''; }
    var n = Math.round(Number(v));
    if (!n) { return '<span class="cfd-nol">0</span>'; }
    var s = Math.abs(n).toLocaleString('en-US');
    /* Gaya akuntansi: angka minus ditulis DALAM KURUNG, bukan pakai tanda
       minus - tanda minus mudah terlewat di tabel sepadat ini. */
    return n < 0 ? '<span class="cfd-min">(' + s + ')</span>' : s;
}

function cfdAman(t) {
    return String(t === null || t === undefined ? '' : t)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

/* Kepala kolom tanggal; Sabtu & Minggu diberi latar beda tipis.
   kelas diisi saat kepala ini dipakai DI DALAM tbody (awal blok pinjaman) -
   di sana dua sel kirinya harus ikut menempel seperti baris biasa. */
/* DUA TATA LETAK, dua-duanya selalu ada: 'tgl' berkolom TANGGAL, 'akun'
   berkolom REKENING. Jawaban mesin disimpan utuh supaya berpindah tab tidak
   memanggil server lagi - angkanya pun dijamin dari hitungan yang sama. */
var CFD_KOLOM = [];     /* kepala kolom yang sedang dipakai */
var CFD_HASIL = null;   /* jawaban terakhir dari mesin */
var CFD_TATA  = 'tgl';  /* tab yang sedang terbuka: 'tgl' | 'akun' */

function cfdSiapkanKolom(res) {
    CFD_KOLOM = [];
    if (CFD_TATA === 'akun') {
        for (var i = 0; i < res.kolom.length; i++) {
            CFD_KOLOM.push({ judul: cfdAman(res.kolom[i].label), kelas: '' });
        }
        return;
    }
    for (var j = 0; j < res.tgl.length; j++) {
        var d = new Date(res.tgl[j] + 'T00:00:00');
        var akhirPekan = (d.getDay() === 0 || d.getDay() === 6) ? ' cfd-wknd' : '';
        CFD_KOLOM.push({
            judul: '<span class="cfd-dow">' + CFD_HARI[d.getDay()] + '</span>'
                 + ('0' + d.getDate()).slice(-2) + ' ' + d.toLocaleString('en-US', { month: 'short' }),
            kelas: akhirPekan
        });
    }
}

function cfdKepala() {
    var h = '<tr><th>Descriptions</th><th class="cfd-num">Total</th>';
    for (var i = 0; i < CFD_KOLOM.length; i++) {
        h += '<th class="cfd-num' + CFD_KOLOM[i].kelas + '">' + CFD_KOLOM[i].judul + '</th>';
    }
    return h + '</tr>';
}

function cfdIsi(baris) {
    var h = '', i, j;
    for (i = 0; i < baris.length; i++) {
        var b = baris[i];
        var label = (b.no ? '<span class="cfd-no">' + cfdAman(b.no) + '</span>' : '') + cfdAman(b.label);
        h += '<tr class="r-' + cfdAman(b.tipe) + '">'
           + '<th class="cfd-desc">' + label + '</th>'
           + '<td class="cfd-num cfd-tot">' + cfdAngka(b.tot) + '</td>';
        for (j = 0; j < CFD_KOLOM.length; j++) {
            h += '<td class="cfd-num' + CFD_KOLOM[j].kelas + '">' + cfdAngka(b.v[j]) + '</td>';
        }
        h += '</tr>';
    }
    return h;
}

/* Pengisi dropdown Bank. DI LINGKUP ATAS, sejajar cfdCari() - bukan di dalam
   $(function(){}): cfdCari() memanggil cfdPasangBank() dari jawaban AJAX-nya,
   dan fungsi yang didefinisikan di dalam blok ready TIDAK terlihat dari sana.
   Sempat begitu, dan akibatnya tiap Search melempar "cfdPasangBank is not
   defined" - tabelnya tetap tergambar (dipanggil lebih dulu) sehingga
   kesalahannya cuma kelihatan di console. */
function cfdPasangBank(daftar, pilih) {
    if (!daftar) { return; }
    /* pilih = larik kunci yang sebelumnya menyala. Yang sudah tidak ada di
       daftar baru (mis. sesudah Profit Center diganti) hilang sendiri;
       kalau tidak ada satu pun yang tersisa, semuanya dinyalakan - daftar
       kosong berarti laporan kosong tanpa sebab yang terlihat. */
    var dipilih = $.isArray(pilih) ? pilih : [];
    var punya = [];
    for (var i = 0; i < daftar.length; i++) { punya.push(String(daftar[i].kunci)); }
    dipilih = $.grep(dipilih, function (k) { return $.inArray(String(k), punya) >= 0; });
    var semua = (dipilih.length === 0);

    var h = '';
    for (var b = 0; b < daftar.length; b++) {
        var k = String(daftar[b].kunci);
        var nyala = semua || ($.inArray(k, dipilih) >= 0);
        h += '<div class="cfd-acc-row">'
           + '<span class="cfd-acc-label" title="' + cfdAman(daftar[b].label) + '">'
           + cfdAman(daftar[b].label) + '</span>'
           + '<label class="toggle-switch">'
           + '<input type="checkbox" class="cfd-acc-item" value="' + cfdAman(k) + '"'
           + (nyala ? ' checked' : '') + '>'
           + '<span class="toggle-slider"></span></label>'
           + '</div>';
    }
    $('#cfd-acc-panel').html(h);
    cfdBankSegarkan();
}

/* Kunci rekening yang menyala. SEMUA menyala -> larik kosong, artinya
   "semua rekening" - sama dgn yang dimengerti server. */
function cfdBankTerpilih() {
    var $item = $('.cfd-acc-item');
    var $on = $item.filter(':checked');
    if (!$item.length || $on.length === $item.length) { return []; }
    return $on.map(function () { return String(this.value); }).get();
}

/* Tulisan tombol + keadaan saklar ALL disamakan dgn isi daftarnya. */
function cfdBankSegarkan() {
    var $item = $('.cfd-acc-item');
    var $on = $item.filter(':checked');
    var tulisan;
    if (!$item.length || $on.length === $item.length) { tulisan = 'All Banks'; }
    else if ($on.length === 1) { tulisan = $on.first().closest('.cfd-acc-row').find('.cfd-acc-label').text(); }
    else { tulisan = $on.length + ' Banks Selected'; }
    $('#cfd-bank-btn').text(tulisan).attr('title', tulisan);

    var all = document.getElementById('cfd-acc-all');
    if (all) {
        all.checked = ($item.length > 0 && $on.length === $item.length);
        all.indeterminate = ($on.length > 0 && $on.length < $item.length);
    }
}

function cfdIsiBank(pilih) {
    $.post('ajx_cf_bank.php', { pc: $('#cfd-pc').val() }, null, 'json')
        .done(function (res) {
            if (!res || res.status !== 'success') { return; }
            cfdPasangBank(res.bank, pilih);
        });
}

/* Satu pintu untuk tirai "sedang diproses". Dipanggil dgn teks untuk
   menyalakan, dgn false untuk mematikan - jadi tidak ada lagi addClass di
   satu tempat dan removeClass yang kelupaan di tempat lain.

   PENTING: laporan ini bisa memakan PULUHAN DETIK kalau periodenya belum
   punya potret AR, karena mesin AR dijalankan ulang di server. Tanpa tirai
   ini layarnya terlihat diam dan pemakai menekan tombolnya berkali-kali. */
function cfdSibuk(teks) {
    if (teks === false) { $('#cfdLoad').removeClass('is-loading'); return; }
    $('#cfdLoadText').text(teks || 'Please wait...');
    $('#cfdLoad').addClass('is-loading');
}

/* Menggambar tabel dari jawaban yang SUDAH ADA - tidak memanggil server.
   Dipanggil sesudah pencarian dan tiap kali tab dipindah. */
/* Urutan tab lembar sumber - sama dgn urutan lembar di Excel. */
var CFD_SRC = ['AR_EXISTING', 'BOOK_TRANSFER', 'OTHER_INFLOW', 'PV_EXP', 'PV_AP'];

/* Satu sel lembar sumber menurut jenis kolomnya. */
function cfdSelSrc(nilai, jenis) {
    if (nilai === null || nilai === undefined || nilai === '') { return ''; }
    if (jenis === 'n') { return cfdAngka(nilai); }
    if (jenis === 'd') {
        var b = String(nilai).slice(0, 10).split('-');
        if (b.length !== 3) { return cfdAman(nilai); }
        var nama = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return b[2] + ' ' + nama[parseInt(b[1], 10) - 1] + ' ' + b[0];
    }
    return cfdAman(nilai);
}

/* Lembar sumber digambar APA ADANYA dari jawaban mesin - tidak ada hitungan
   di sini. Itu memang gunanya: yang terlihat di layar ini sama persis dgn
   yang akan tersimpan waktu Save History ditekan. */
function cfdGambarSrc(kode) {
    var res = CFD_HASIL;
    var def = (res.sumber_def || {})[kode];
    var baris = (res.sumber || {})[kode] || [];
    if (!def) { return; }

    var h = '<tr>';
    for (var i = 0; i < def.kolom.length; i++) {
        h += '<th class="' + (def.kolom[i][2] === 'n' ? 'cfd-num' : '') + '">'
           + cfdAman(def.kolom[i][0]) + '</th>';
    }
    $('#cfd-src thead').html(h + '</tr>');

    var isi = '';
    for (var r = 0; r < baris.length; r++) {
        isi += '<tr>';
        for (var c = 0; c < def.kolom.length; c++) {
            var k = def.kolom[c];
            isi += '<td class="' + (k[2] === 'n' ? 'cfd-num' : '') + '">'
                 + cfdSelSrc(baris[r][k[1]], k[2]) + '</td>';
        }
        isi += '</tr>';
    }
    if (!baris.length) {
        isi = '<tr><td colspan="' + def.kolom.length + '" class="cfd-kosong">'
            + 'No rows for this period.</td></tr>';
    }
    $('#cfd-src tbody').html(isi);
}

function cfdGambar() {
    var res = CFD_HASIL;
    if (!res) { return; }

    /* Tab lembar sumber: tabel lain, bukan matriksnya. */
    if ($.inArray(CFD_TATA, CFD_SRC) >= 0) {
        cfdGambarSrc(CFD_TATA);
        $('#cfd-tbl').hide();
        $('#cfd-src').show();
        $('#cfd-tab-bar').show().find('.cfd-tab')
            .removeClass('active').filter('[data-tata="' + CFD_TATA + '"]').addClass('active');
        $('.cfd-scroll').scrollLeft(0);
        return;
    }
    $('#cfd-src').hide();
    $('#cfd-tbl').show();

    var baris = (CFD_TATA === 'akun') ? res.baris_akun : res.baris_tgl;
    var pinj  = (CFD_TATA === 'akun') ? res.pinjaman_akun : res.pinjaman_tgl;
    /* Jawaban lama (mis. dari potret) cuma punya 'baris' - dipakai apa
       adanya supaya layar ini tidak kosong tanpa sebab. */
    if (!baris) { baris = res.baris; pinj = res.pinjaman; }

    cfdSiapkanKolom(res);
    var isi = cfdIsi(baris);
    if (pinj && pinj.length) {
        /* Satu tabel, bukan dua: blok pinjaman menyambung di bawah,
           dipisah SATU baris bergaris saja - kepalanya tidak diulang
           karena kolomnya sudah pasti sama dgn kepala di atas. */
        isi += '<tr class="r-spasi"><td colspan="' + (CFD_KOLOM.length + 2) + '"></td></tr>'
             + cfdIsi(pinj);
    }
    $('#cfd-tbl thead').html(cfdKepala());
    $('#cfd-tbl tbody').html(isi);

    $('#cfd-tab-bar').show().find('.cfd-tab')
        .removeClass('active').filter('[data-tata="' + CFD_TATA + '"]').addClass('active');
    /* Guliran dikembalikan ke kiri: dua tata letak itu lebarnya jauh
       berbeda, dan tabel yang tiba-tiba tergulir di tengah terbaca seperti
       data yang hilang. */
    $('.cfd-scroll').scrollLeft(0);
}

/* Tombol tab lembar sumber dibuat dari jawaban mesin, bukan ditulis mati:
   kalau suatu saat ada lembar baru, ia muncul sendiri di sini. Jumlah
   barisnya ikut dicetak supaya lembar kosong tidak terlihat seperti gagal
   memuat. */
function cfdPasangTabSrc(res) {
    $('#cfd-tab-bar .cfd-tab-src').remove();
    var def = res.sumber_def || {};
    var h = '';
    for (var i = 0; i < CFD_SRC.length; i++) {
        var k = CFD_SRC[i];
        if (!def[k]) { continue; }
        h += '<button type="button" class="cfd-tab cfd-tab-src" data-tata="' + k + '">'
           + '<i class="fa fa-list-ul" aria-hidden="true"></i> ' + cfdAman(def[k].judul)
           + ' <span class="cfd-tab-jml">' + def[k].jml + '</span></button>';
    }
    $('#cfd-tab-bar').append(h);
}

function cfdSaring() {
    return {
        start_date: $('#cfd-dari').val(),
        end_date:   $('#cfd-sampai').val(),
        pc:         $('#cfd-pc').val(),
        /* Beberapa rekening digabung dgn titik koma; kosong = semua. Server
           memecahnya lagi lewat cfpDaftarKunciBank(). */
        bank:       cfdBankTerpilih().join(';') || 'ALL',
        type:       $('#cfd-type').val(),
        /* Lembar sumbernya ikut diminta supaya pemakai bisa melihat data
           yang akan ikut tersimpan waktu Save History ditekan. */
        sumber:     '1'
    };
}

function cfdCari() {
    var kirim = cfdSaring();

    /* Judul memakai profit center yang dipilih, BUKAN nama perusahaan:
       halaman ini melayani Garment (NAG) dan Knitting (NAK), jadi nama
       perusahaan yang dipatok akan salah separuh waktu. Dipasang sebelum
       permintaan dikirim supaya judulnya tidak tertinggal lingkup lama
       kalau pencariannya gagal. */
    $('#cfd-judul').text('Cash Flow Projection \u2014 ' + $('#cfd-pc option:selected').text());
    /* Spinner-nya yang mengabarkan "sedang dihitung", jadi keterangan periode
       lama dikosongkan - kalau dibiarkan, yang terbaca keterangan hasil yang
       SEBELUMNYA, padahal angkanya sedang diganti. */
    $('#cfd-sub').text('');
    cfdSibuk('Building the projection...');
    $.post('ajx_cash_flow_projection.php', kirim, null, 'json')
        .done(function (res) {
            cfdSibuk(false);
            if (!res || res.error) {
                $('#cfd-sub').text('');
                Swal.fire({ icon: 'warning', title: 'Cannot build the report', text: (res && res.error) ? res.error : 'Unknown error' });
                return;
            }
            CFD_HASIL = res;
            cfdPasangTabSrc(res);
            /* Tab yang terbuka duluan ikut type-nya (res.mode), tapi
               keduanya sama-sama tersedia. */
            CFD_TATA = (res.mode === 'akun') ? 'akun' : 'tgl';
            cfdGambar();

            var i = res.info;
            $('#cfd-sub').text(i.periode + '  \u00b7  Rate ' + Number(i.rate).toLocaleString('en-US')
                               + '  \u00b7  ' + i.bank);   /* profit center sudah pindah ke judul */

            /* Catatan sumber & peringatan SENGAJA tidak ditampilkan di layar
               (permintaan pemakai 16 Nov 2026) - kalau ada yang tidak beres,
               diperbaiki dari belakang. Isinya tetap dikirim di JSON
               (info.ar_doc & info.peringatan) supaya masih bisa diperiksa. */

            /* Daftarnya disegarkan dari jawaban laporan juga - isinya sama
               (pustaka yang sama), ini sekadar menjaga kalau master berubah
               selagi halaman terbuka. Pilihan pemakai dipertahankan. */
            cfdPasangBank(res.bank, cfdBankTerpilih());
        })
        .fail(function (x) {
            cfdSibuk(false);
            $('#cfd-sub').text('');
            Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
        });
}

/* ----------------------------------------------------------------------
   PEMBATASAN RENTANG MENURUT TYPE (permintaan pemakai 19 Nov 2026):
       Weekly  -> From hari MINGGU, To hari SABTU
       Monthly -> From TANGGAL 1, To AKHIR BULAN
       Daily   -> bebas
   Satu minggu yang dimulai hari Rabu atau satu bulan yang dimulai tanggal
   7 tetap tampil rapi - tidak ada yang kelihatan salah - padahal kolomnya
   lalu tidak bisa dibandingkan dgn periode mana pun.

   DUA LAPIS, karena satu lapis saja selalu bisa dilewati:
   1. kalendernya dibatasi (hari yang tidak sah dimatikan; Monthly cuma
      memilih bulan), jadi yang MEMILIH tidak bisa salah;
   2. tanggalnya dirapikan sendiri sebelum dipakai, karena kotaknya bisa
      DIKETIK TANGAN dan isinya juga tertinggal dari type sebelumnya.
   ---------------------------------------------------------------------- */
function cfdKeTgl(t) {
    var b = String(t || '').split('-');
    if (b.length !== 3) { return null; }
    var d = new Date(Number(b[2]), Number(b[1]) - 1, Number(b[0]));
    return isNaN(d.getTime()) ? null : d;
}

function cfdTeksTgl(d) {
    function dua(n) { return (n < 10 ? '0' : '') + n; }
    return dua(d.getDate()) + '-' + dua(d.getMonth() + 1) + '-' + d.getFullYear();
}

/* WEEKLY = TEPAT SATU MINGGU (7 hari, Minggu s/d Sabtu) dan MONTHLY = TEPAT
   SATU BULAN (tanggal 1 s/d akhir bulan) - permintaan pemakai 19 Nov 2026.

   Pada kedua type itu To BUKAN pilihan: ia turunan dari From, jadi kotaknya
   dikunci (lihat cfdPasangKalender). Kotak yang nilainya ditentukan kode
   TAPI tetap bisa diketik adalah undangan masalah: yang diketik hilang lagi
   tanpa sebab yang terlihat. Dikunci, maksudnya langsung terbaca. */
function cfdRapikanTanggal() {
    var t = $('#cfd-type').val();
    var a = cfdKeTgl($('#cfd-dari').val());
    if (!a) { return; }
    var b;
    if (t === 'weekly') {
        a.setDate(a.getDate() - a.getDay());                  /* mundur ke Minggu */
        b = new Date(a.getFullYear(), a.getMonth(), a.getDate() + 6);
    } else if (t === 'monthly') {
        a.setDate(1);
        b = new Date(a.getFullYear(), a.getMonth() + 1, 0);   /* hari 0 = akhir bulan sebelumnya */
    } else {
        return;
    }
    $('#cfd-dari').val(cfdTeksTgl(a));
    $('#cfd-sampai').val(cfdTeksTgl(b));
}

/* Kalender dipasang ULANG tiap ganti type: pilihan daysOfWeekDisabled &
   minViewMode hanya terbaca waktu dipasang, jadi mengubahnya di tempat
   tidak berpengaruh. */
function cfdPasangKalender() {
    var t = $('#cfd-type').val();
    var kunciTo = (t === 'weekly' || t === 'monthly');

    $('#cfd-dari, #cfd-sampai').each(function () {
        var $i = $(this);
        var sisiTo = ($i.attr('id') === 'cfd-sampai');
        /* 'remove', BUKAN 'destroy': di bootstrap-datepicker versi yang
           dipakai aplikasi ini 'destroy' bukan perintah yang dikenal, jadi
           ia diam saja - lalu pemasangan berikutnya dianggap pemasangan
           ULANG pada widget lama dan SELURUH pilihan barunya diabaikan.
           Tidak ada pesan galat sama sekali; kalendernya cuma tidak pernah
           berubah. */
        if ($i.data('datepicker')) { $i.datepicker('remove'); }
        /* To yang dikunci TIDAK dipasangi kalender sama sekali: readonly
           saja tidak cukup, kalendernya tetap terbuka waktu kotaknya diklik
           dan tanggal yang dipilih di situ tidak akan ke mana-mana. */
        if (sisiTo && kunciTo) { return; }
        var s = { format: 'dd-mm-yyyy', autoclose: true };
        if (t === 'monthly') { s.minViewMode = 'months'; }
        if (t === 'weekly' && !sisiTo) { s.daysOfWeekDisabled = [1, 2, 3, 4, 5, 6]; }
        $i.datepicker(s);
    });

    $('#cfd-sampai').prop('readonly', kunciTo).toggleClass('cfd-kunci', kunciTo)
        .attr('title', kunciTo
            ? (t === 'weekly' ? 'One week: Sunday to Saturday. Pick the From date.'
                              : 'One month: the 1st to the end of the month. Pick the From date.')
            : '');
}

$(function () {
    cfdPasangKalender();
    $('.app-select2').select2({ width: '100%' });

    $('#cfd-type').on('change', function () { cfdPasangKalender(); cfdRapikanTanggal(); });
    /* Diketik tangan pun ikut dirapikan - begitu kotaknya ditinggalkan. To
       tidak perlu pemasang sendiri: pada type yang mengunci ia readonly,
       pada Daily ia memang bebas. */
    $('#cfd-dari').on('change', function () { cfdRapikanTanggal(); });

    $('#cfd-cari').on('click', function () {
        cfdRapikanTanggal();   /* lapis terakhir, sebelum apa pun dikirim */
        var s = cfdSaring();
        if (!s.start_date || !s.end_date) {
            Swal.fire({ icon: 'warning', title: 'Period required', text: 'Fill in both the From and To dates first.' });
            return;
        }
        if (cfoPernah[s.start_date]) { cfdCari(); return; }
        cfoBuka(true);
    });
    /* Daftar Bank mengikuti Profit Center SEJAK AWAL, tidak menunggu Search
       (permintaan pemakai 17 Nov 2026). Dipanggil lewat endpoint ringan yang
       cuma membaca master - menjalankan seluruh proyeksi hanya untuk mengisi
       dropdown berarti menunggu beberapa detik tiap kali PC disentuh. */
    /* Ganti Profit Center = daftar rekeningnya ikut berganti, jadi
       centangnya dimulai dari semua lagi. */
    $('#cfd-pc').on('change', function () { cfdIsiBank([]); });
    cfdIsiBank([]);

    /* Saklar ALL menyalakan/mematikan semuanya. Mematikan semua tidak
       diizinkan - laporan tanpa satu pun rekening kosong tanpa sebab yang
       terlihat - jadi satu rekening ditinggalkan menyala. */
    $('#cfd-acc-all').on('change', function () {
        var $item = $('.cfd-acc-item');
        $item.prop('checked', this.checked);
        if (!this.checked && $item.length) { $item.first().prop('checked', true); }
        cfdBankSegarkan();
    });

    /* Dipasang di WADAHNYA, bukan di tiap kotak: isinya digambar ulang tiap
       kali daftar rekening berganti, jadi pemasang yang menempel di kotak
       ikut terbuang diam-diam. */
    $('#cfd-acc-panel').on('change', '.cfd-acc-item', function () {
        if (!$('.cfd-acc-item:checked').length) { this.checked = true; return; }
        cfdBankSegarkan();
    });

    /* Klik di dalam dropdown tidak boleh menutupnya - kalau tertutup tiap
       klik, mencentang beberapa rekening jadi menyiksa. */
    $('#cfd-bank-menu').on('click', function (e) { e.stopPropagation(); });

    $('#cfd-tab-bar').on('click', '.cfd-tab', function () {
        var t = $(this).data('tata');
        if (t === CFD_TATA) { return; }
        CFD_TATA = t;
        cfdGambar();
    });

    $('#cfd-xls').on('click', function () {
        var s = cfdSaring(), q = [];
        for (var k in s) { if (s.hasOwnProperty(k)) { q.push(k + '=' + encodeURIComponent(s[k])); } }
        window.location = 'ekspor_cash_flow_projection.php?' + q.join('&');
    });

    /* Save History: membekukan laporan yang SEDANG tampil jadi satu dokumen.
       Angkanya dihitung ulang di server dgn saringan yang sama - layar tidak
       dipercaya mengirim angka. */
    $('#cfd-simpan').on('click', function () {
        var s = cfdSaring();
        Swal.fire({
            icon: 'question',
            title: 'Save this projection?',
            text: 'A snapshot will be stored for ' + $('#cfd-dari').val() + ' to ' + $('#cfd-sampai').val() + '.',
            showCancelButton: true,
            confirmButtonText: 'Save'
        }).then(function (p) {
            if (!p.isConfirmed) { return; }
            /* Spinner yang sama dipakai waktu menyimpan. Dulu hanya tombolnya
               yang dimatikan - padahal menyimpan bisa memakan puluhan detik
               kalau periode ini belum punya potret AR (mesin AR dijalankan
               lagi di server), dan selama itu layarnya terlihat DIAM. */
            cfdSibuk('Saving the snapshot...');
            var $t = $('#cfd-simpan').prop('disabled', true);
            $.post('simpan_cf_history.php', s, null, 'json')
                .done(function (res) {
                    if (!res || res.status !== 'success') {
                        Swal.fire({ icon: 'warning', title: 'Not saved', text: (res && res.message) ? res.message : 'Unknown error' });
                        return;
                    }
                    Swal.fire({ icon: 'success', title: 'Saved', text: 'Document ' + res.doc_number + ' (' + res.baris + ' rows).' });
                })
                .fail(function (x) {
                    Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
                })
                .always(function () { cfdSibuk(false); $t.prop('disabled', false); });
        });
    });

    /* ------------------------------------------------------------------
       SALDO AWAL - modal Opening Balance.
       Angka sistemnya datang dari MESIN LAPORAN, bukan dibaca dari tabel di
       layar, supaya tidak mungkin berbeda dgn baris OPENING di bawah.
       ------------------------------------------------------------------ */
    function cfoRupiah(v) {
        if (v === null || v === undefined || v === '') { return ''; }
        var n = Number(v);
        var s = Math.abs(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return n < 0 ? '(' + s + ')' : s;
    }

    /* Angka dari kotak isian. Pemisah ribuan dan spasi dibuang dgn cara yang
       SAMA PERSIS dgn server (simpan_cf_opening.php), supaya yang terbaca di
       layar dan yang tersimpan tidak pernah berbeda. Bukan angka -> null. */
    function cfoAngka(t) {
        t = String(t === null || t === undefined ? '' : t).replace(/[, ]/g, '');
        t = $.trim(t);
        if (t === '' || isNaN(t)) { return null; }
        return parseFloat(t);
    }

    /* TOTAL IN IDR kolom manual dihitung ULANG tiap ketikan (dilaporkan
       pemakai 19 Nov 2026: angkanya tidak berubah waktu diisi). Yang
       dijumlahkan NILAI YANG BERLAKU per rekening: angka manual kalau
       kotaknya terisi sah, selain itu saldo sistemnya - sama dgn arti
       'pakai' yang dikirim server waktu modal dibuka. */
    function cfoHitungTotal() {
        var t = 0;
        $('#cfo-tbl tbody .cfo-nilai').each(function () {
            var m = cfoAngka(this.value);
            /* REKENING PINJAMAN: yang dihitung HANYA angka yang diketik.
               Saldo sistemnya besarnya pinjaman yang masih berjalan, bukan
               uang yang bisa dipakai, jadi kotak kosong berarti 0 - bukan
               "ikut sistem" seperti rekening biasa. Aturan yang sama persis
               ada di cfpAwalKas() di mesin laporan. */
            if ($(this).attr('data-loan') === '1') { t += (m === null) ? 0 : m; return; }
            var sis = parseFloat($(this).attr('data-sistem'));
            if (isNaN(sis)) { sis = 0; }
            t += (m === null) ? sis : m;
        });
        $('#cfo-jum-pakai').text(cfoRupiah(t));
    }

    /* Siapa & kapan mengubah TIDAK lagi jadi kolom sendiri - riwayat
       lengkapnya sudah ada di tabel bawah, jadi kolom itu cuma mengulang. */
    function cfoBaris(a) {
        var terisi = (a.manual !== null && a.manual !== undefined);
        /* ISIAN BAWAANNYA = SALDO SISTEM (permintaan pemakai 19 Nov 2026),
           bukan kotak kosong - supaya yang mau mengubah satu rekening tidak
           perlu mengetik ulang angka yang sudah benar.

           Yang TIDAK berubah: artinya. Kotak yang isinya masih sama persis
           dgn saldo sistem TIDAK dianggap angka manual - waktu Save dikirim
           sbg kosong, jadi tidak ada baris ap_cf_opening yang lahir dan
           riwayatnya tidak penuh oleh rekening yang sebenarnya tidak
           disentuh. Lihat penjaganya di tombol Save. */
        /* REKENING PINJAMAN: isian bawaannya 0, BUKAN saldo sistem
           (permintaan pemakai 19 Nov 2026). Saldo sistemnya minus sebesar
           pinjaman yang masih berjalan, dan angka itu bukan uang yang bisa
           dipakai - dipakai sbg saldo awal, laporannya mulai dari minus.
           Saldo sistemnya tetap DITAMPILKAN di kolom sebelahnya, jadi yang
           mau memakainya tinggal mengetik ulang. */
        var pinjaman = (a.loan == 1);
        var bawaan = Number(a.sistem).toFixed(2);
        /* Isian bawaan rekening pinjaman 0, dan 0 itu SENGAJA dianggap angka
           manual beneran: data-sistem tetap saldo sistem yg minus, jadi
           penjaga "sama dgn sistem -> jangan simpan" di tombol Save tidak
           menelannya. Kalau data-sistem ikut dibuat 0, nilainya cuma hiasan -
           tersimpan tidak, dan diketik 0 pun tetap tidak tersimpan. */
        var isiBawaan = pinjaman ? '0.00' : bawaan;
        return '<tr>'
             + '<td>' + cfdAman(a.label) + '</td>'
             + '<td>' + cfdAman(a.curr) + '</td>'
             + '<td class="cfo-num">' + cfoRupiah(a.sistem) + '</td>'
             /* data-semula = nilai yang DILIHAT pemakai waktu modal dibuka.
                Dikirim balik saat Save supaya server bisa menolak kalau
                orang lain sudah mengubahnya - lihat simpan_cf_opening.php.
                SENGAJA tetap kosong kalau belum ada angka manual: isian
                bawaan di atas cuma bantuan mengetik, bukan angka tersimpan.
                data-sistem dipakai menghitung total & menandai baris yang
                benar-benar diubah. */
             /* Penanda biru juga untuk rekening pinjaman yang belum pernah
                disimpan: isian bawaannya 0 memang BERBEDA dari saldo sistem,
                dan penanda itu artinya persis "berbeda dari sistem". */
             + '<td><input type="text" class="cfo-nilai' + ((terisi || pinjaman) ? ' cfo-isi' : '') + '"'
             + ' data-akun="' + cfdAman(a.kunci) + '"'
             + ' data-loan="' + (pinjaman ? 1 : 0) + '"'
             + ' data-sistem="' + bawaan + '"'
             + ' data-semula="' + (terisi ? Number(a.manual).toFixed(2) : '') + '"'
             + ' value="' + (terisi ? Number(a.manual).toFixed(2) : isiBawaan) + '"></td>'
             + '<td><input type="text" class="cfo-ket" maxlength="200" value="'
             + cfdAman(a.keterangan || '') + '"></td>'
             + '</tr>';
    }

    /* From/To selalu angka. Baris lama yang terlanjur kosong masih mungkin
       ada, jadi tetap dijaga - ditampilkan sbg garis, bukan kata "system". */
    function cfoLogBaris(l) {
        var dari = (l.nilai_lama === null) ? '<span class="cfo-kecil">-</span>' : cfoRupiah(l.nilai_lama);
        var ke   = (l.nilai_baru === null) ? '<span class="cfo-kecil">-</span>' : cfoRupiah(l.nilai_baru);
        return '<tr><td>' + cfdAman(l.waktu) + '</td><td>' + cfdAman(l.user) + '</td>'
             + '<td>' + cfdAman(l.akun) + '</td><td>' + cfdAman(l.aksi) + '</td>'
             + '<td class="cfo-num">' + dari + '</td><td class="cfo-num">' + ke + '</td>'
             + '<td>' + cfdAman(l.keterangan || '') + '</td></tr>';
    }

    /* Alur (permintaan pemakai 17 Nov 2026):
       halaman dibuka KOSONG - tidak menghitung apa pun sampai Search ditekan.
       Search PERTAMA untuk satu tanggal From memunculkan modal saldo awal
       dulu, baru laporannya. Sesudah tanggal itu pernah dilihat, Search
       berikutnya langsung ke laporan - kalau tidak, modalnya jadi gangguan
       buat orang yang cuma mengulang periode yang sama. Tombol Opening
       Balance tetap ada untuk membukanya kapan saja. */
    var cfoPernah = {};     /* tanggal From yang modalnya sudah pernah tampil */
    var cfoLanjut = false;  /* tutup modal -> langsung jalankan laporan */
    var cfoBerubah = false; /* ada yang tersimpan -> laporan wajib dihitung ulang */

    function cfoBuka(lanjut) {
        var kirim = cfdSaring();
        cfoLanjut = !!lanjut;
        cfoBerubah = false;
        cfoPernah[kirim.start_date] = true;
        $('#cfo-tbl tbody').html('<tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Loading...</td></tr>');
        $('#cfo-log').html('');
        $('#modal-opening').modal('show');

        $.post('ajx_cf_opening.php', kirim, null, 'json')
            .done(function (res) {
                if (!res || res.status !== 'success') {
                    cfoGagal((res && res.message) ? res.message : 'Unknown error');
                    return;
                }
                $('#cfo-tanggal').text(res.tanggal);
                var isi = '';
                if (!res.akun.length) {
                    $('#cfo-tbl tbody').html('<tr><td colspan="5" style="padding:22px;text-align:center;color:#94a3b8;">'
                        + cfdAman(res.pesan || 'Nothing to set for this filter.') + '</td></tr>');
                    $('#cfo-log').html('');
                    return;
                }
                for (var i = 0; i < res.akun.length; i++) { isi += cfoBaris(res.akun[i]); }
                isi += '<tr class="cfo-jum"><td colspan="2">TOTAL IN IDR</td>'
                     + '<td class="cfo-num">' + cfoRupiah(res.jumlah.sistem) + '</td>'
                     + '<td class="cfo-num" id="cfo-jum-pakai">' + cfoRupiah(res.jumlah.pakai) + '</td>'
                     + '<td></td></tr>';
                $('#cfo-tbl tbody').html(isi);
                cfoHitungTotal();

                var log = '';
                for (var j = 0; j < res.log.length; j++) { log += cfoLogBaris(res.log[j]); }
                $('#cfo-log').html(log || '<tr><td colspan="7" class="cfo-kecil" style="padding:12px;">No manual change on this date yet.</td></tr>');
            })
            .fail(function (x) {
                cfoGagal((x.responseText || 'Request failed').slice(0, 300));
            });
    }

    /* Pesan gagal ditulis DI DALAM modal, modalnya TIDAK ditutup. Menutup
       modal yang animasi bukanya belum selesai membuatnya tersangkut terbuka
       dan kosong - terlihat seperti aplikasi menggantung padahal cuma gagal
       memuat. cfoLanjut dimatikan supaya menutupnya tidak langsung memicu
       laporan yang hampir pasti gagal dgn sebab yang sama. */
    function cfoGagal(pesan) {
        cfoLanjut = false;
        $('#cfo-tbl tbody').html('<tr><td colspan="5" style="padding:22px;text-align:center;color:#b91c1c;">'
            + cfdAman(pesan) + '</td></tr>');
        $('#cfo-log').html('');
    }

    $('#cfd-opening').on('click', function () { cfoBuka(false); });

    /* Laporannya dijalankan SESUDAH modalnya tertutup - baik lewat Save,
       Close, maupun tombol silang - supaya angkanya pasti yang terbaru. */
    $('#modal-opening').on('hidden.bs.modal', function () {
        if (cfoLanjut || cfoBerubah) { cfoLanjut = false; cfoBerubah = false; cfdCari(); }
    });

    $('#cfo-tbl').on('input', '.cfo-nilai', function () {
        /* Penanda biru = BERBEDA dari saldo sistem, bukan sekadar "ada
           isinya" - sejak kotaknya terisi bawaan, "ada isinya" tidak lagi
           membedakan apa pun. */
        var m = cfoAngka(this.value);
        var sis = parseFloat($(this).attr('data-sistem'));
        $(this).toggleClass('cfo-isi', m !== null && !isNaN(sis) && Math.abs(m - sis) >= 0.005);
        cfoHitungTotal();
    });

    $('#cfo-simpan').on('click', function () {
        var baris = [];
        $('#cfo-tbl tbody tr').each(function () {
            var $n = $(this).find('.cfo-nilai');
            if (!$n.length) { return; }

            /* Kotak yang isinya masih sama dgn saldo sistem DAN belum pernah
               punya angka manual dikirim sbg KOSONG - artinya "ikut hitungan
               sistem". Tanpa penjaga ini, isian bawaan yang baru ditambahkan
               akan membuat SETIAP rekening tersimpan sbg angka manual setiap
               kali tombol Save ditekan, dan riwayat perubahannya penuh oleh
               rekening yang sebenarnya tidak disentuh siapa pun. */
            var nilai  = $.trim($n.val());
            var semula = String($n.attr('data-semula') || '');
            var m      = cfoAngka(nilai);
            var sis    = parseFloat($n.attr('data-sistem'));
            if (semula === '' && m !== null && !isNaN(sis) && Math.abs(m - sis) < 0.005) {
                nilai = '';
            }

            baris.push({ akun: $n.data('akun'),
                         manual: nilai,
                         semula: semula,
                         keterangan: $.trim($(this).find('.cfo-ket').val() || '') });
        });
        if (!baris.length) { return; }

        var $t = $('#cfo-simpan').prop('disabled', true);
        $.post('simpan_cf_opening.php', { tanggal: $('#cfd-dari').val(),
                                          end_date: $('#cfd-sampai').val(),
                                          baris: JSON.stringify(baris) }, null, 'json')
            .done(function (res) {
                /* Orang lain mengubah saldo awal selagi modal ini terbuka.
                   TIDAK ADA yang tersimpan - modalnya dimuat ulang supaya
                   yang terlihat benar-benar keadaan terbaru. */
                if (res && res.status === 'conflict') {
                    var daftar = (res.bentrok || []).map(function (x) {
                        return cfdAman(x.akun) + (x.oleh ? ' (' + cfdAman(x.oleh) + ')' : '');
                    }).join('<br>');
                    Swal.fire({ icon: 'warning', title: 'Changed by someone else',
                                html: 'Nothing was saved. These accounts were changed while this window was open:<br><br>'
                                      + daftar + '<br><br>The list below has been reloaded.' });
                    cfoBuka(cfoLanjut);
                    return;
                }
                if (!res || res.status !== 'success') {
                    Swal.fire({ icon: 'warning', title: 'Not saved', text: (res && res.message) ? res.message : 'Unknown error' });
                    return;
                }
                cfoBerubah = true;
                /* Tidak ada kotak "Saved" (permintaan pemakai 18 Nov 2026):
                   modalnya tutup, lalu handler hidden-nya menjalankan
                   laporannya - tanda jadinya overlay spinner di area
                   tabelnya, tanpa perlu ditekan OK dulu. */
                $('#modal-opening').modal('hide');
            })
            .fail(function (x) {
                Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
            })
            .always(function () { $t.prop('disabled', false); });
    });

    /* SENGAJA tidak memanggil cfdCari() di sini: halaman dibuka kosong
       (permintaan pemakai 17 Nov 2026). Laporannya baru dihitung sesudah
       pemakai menentukan type, periode, dan saldo awalnya. */
});
</script>
