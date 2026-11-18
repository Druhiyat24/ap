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

    <div class="ftl-card">
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

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="cfd-bank" class="form-label"><b>Bank</b></label>
                        <select class="form-control app-select2" id="cfd-bank">
                            <option value="ALL" selected="selected">All Banks</option>
                        </select>
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
        <div class="cfd-scroll">
            <table class="cfd" id="cfd-tbl">
                <thead><tr><th>Descriptions</th><th class="cfd-num">Total</th></tr></thead>
                <tbody><tr><td colspan="2" class="cfd-kosong">Pick a type and a period, then click Search.</td></tr></tbody>
            </table>
        </div>
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
/* Dua tata letak: Daily berkolom TANGGAL, Weekly/Monthly berkolom REKENING.
   Yang menentukan res.mode dari mesin, bukan tebakan dari dropdown Type -
   supaya kepala tabel tidak pernah berbeda dari isi datanya. */
var CFD_KOLOM = [];     /* kepala kolom yang sedang dipakai */

function cfdSiapkanKolom(res) {
    CFD_KOLOM = [];
    if (res.mode === 'akun') {
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
    var opt = '<option value="ALL">All Banks</option>';
    for (var b = 0; b < daftar.length; b++) {
        opt += '<option value="' + cfdAman(daftar[b].kunci) + '">' + cfdAman(daftar[b].label) + '</option>';
    }
    $('#cfd-bank').html(opt).val(pilih || 'ALL');
    if (!$('#cfd-bank').val()) { $('#cfd-bank').val('ALL'); }
    $('#cfd-bank').trigger('change.select2');
}

function cfdIsiBank(pilih) {
    $.post('ajx_cf_bank.php', { pc: $('#cfd-pc').val() }, null, 'json')
        .done(function (res) {
            if (!res || res.status !== 'success') { return; }
            cfdPasangBank(res.bank, pilih);
        });
}

function cfdSaring() {
    return {
        start_date: $('#cfd-dari').val(),
        end_date:   $('#cfd-sampai').val(),
        pc:         $('#cfd-pc').val(),
        bank:       $('#cfd-bank').val(),
        type:       $('#cfd-type').val()
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
    $('#cfd-sub').text('Loading...');
    $.post('ajx_cash_flow_projection.php', kirim, null, 'json')
        .done(function (res) {
            if (!res || res.error) {
                $('#cfd-sub').text('');
                Swal.fire({ icon: 'warning', title: 'Cannot build the report', text: (res && res.error) ? res.error : 'Unknown error' });
                return;
            }
            cfdSiapkanKolom(res);
            var isi = cfdIsi(res.baris);
            if (res.pinjaman && res.pinjaman.length) {
                /* Satu tabel, bukan dua: blok pinjaman menyambung di bawah,
                   dipisah SATU baris bergaris saja - kepalanya tidak diulang
                   karena kolomnya sudah pasti sama dgn kepala di atas. */
                isi += '<tr class="r-spasi"><td colspan="' + (CFD_KOLOM.length + 2) + '"></td></tr>'
                     + cfdIsi(res.pinjaman);
            }
            $('#cfd-tbl thead').html(cfdKepala());
            $('#cfd-tbl tbody').html(isi);

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
            cfdPasangBank(res.bank, $('#cfd-bank').val());
        })
        .fail(function (x) {
            $('#cfd-sub').text('');
            Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
        });
}

$(function () {
    $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
    $('.app-select2').select2({ width: '100%' });

    $('#cfd-cari').on('click', function () {
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
    $('#cfd-pc').on('change', function () { cfdIsiBank('ALL'); });
    cfdIsiBank('ALL');

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
                .always(function () { $t.prop('disabled', false); });
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

    /* Siapa & kapan mengubah TIDAK lagi jadi kolom sendiri - riwayat
       lengkapnya sudah ada di tabel bawah, jadi kolom itu cuma mengulang. */
    function cfoBaris(a) {
        var terisi = (a.manual !== null && a.manual !== undefined);
        return '<tr>'
             + '<td>' + cfdAman(a.label) + '</td>'
             + '<td>' + cfdAman(a.curr) + '</td>'
             + '<td class="cfo-num">' + cfoRupiah(a.sistem) + '</td>'
             /* data-semula = nilai yang DILIHAT pemakai waktu modal dibuka.
                Dikirim balik saat Save supaya server bisa menolak kalau
                orang lain sudah mengubahnya - lihat simpan_cf_opening.php. */
             + '<td><input type="text" class="cfo-nilai' + (terisi ? ' cfo-isi' : '') + '"'
             + ' data-akun="' + cfdAman(a.kunci) + '"'
             + ' data-semula="' + (terisi ? Number(a.manual).toFixed(2) : '') + '"'
             + ' value="' + (terisi ? Number(a.manual).toFixed(2) : '') + '"></td>'
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
                     + '<td class="cfo-num">' + cfoRupiah(res.jumlah.pakai) + '</td>'
                     + '<td></td></tr>';
                $('#cfo-tbl tbody').html(isi);

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
        $(this).toggleClass('cfo-isi', $.trim(this.value) !== '');
    });

    $('#cfo-simpan').on('click', function () {
        var baris = [];
        $('#cfo-tbl tbody tr').each(function () {
            var $n = $(this).find('.cfo-nilai');
            if (!$n.length) { return; }
            baris.push({ akun: $n.data('akun'),
                         manual: $.trim($n.val()),
                         semula: String($n.attr('data-semula') || ''),
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
                $('#modal-opening').modal('hide');   /* handler hidden yang menjalankan laporannya */
                Swal.fire({ icon: 'success', title: 'Saved',
                            text: res.diubah + ' changed, ' + res.direset + ' back to system, '
                                + res.dilewati + ' unchanged.' });
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
