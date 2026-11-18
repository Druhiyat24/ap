<?php
/* ============================================================================
   ISI satu potret Cash Flow Projection - HALAMAN SENDIRI.

   Dibuka dari cf_history_projection.php lewat window.location, pola yang
   sama dgn AP History Projection -> ap_realization_report.php. Dulu isinya
   ditempel di kartu bawah pada halaman daftar; tabelnya setinggi laporan
   aslinya, jadi harus digulir jauh ke bawah dulu baru kelihatan.

   ANGKANYA TIDAK DIHITUNG ULANG - dibaca dari ajx_cf_history_detail.php,
   yang membacanya dari ap_cf_history_det.

   header.php tidak memuat satu pun pustaka JS, jadi halaman ini memuat
   sendiri yang dibutuhkannya.
   ============================================================================ */
include '../header.php';

$cfdDoc = isset($_GET['doc_number']) ? trim((string) $_GET['doc_number']) : '';

?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link rel="stylesheet" href="../css/app-cf-matrix.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-cf-matrix.css'); ?>">
<link rel="stylesheet" href="../css/app-cf-exec.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-cf-exec.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">

    <div class="ftl-card">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
            <div>
                <h1>History Projection &mdash; <span id="cfv-doc"><?php echo htmlspecialchars($cfdDoc, ENT_QUOTES); ?></span></h1>
                <span class="ftl-crumb">Bank &rsaquo; Report &rsaquo; Projection &rsaquo; History Projection &rsaquo; Detail</span>
            </div>
        </div>

        <div class="ftl-body">
            <div class="ftl-actions" style="justify-content:flex-start;">
                <button type="button" class="app-btn app-btn-danger app-btn-ctl app-btn-sm" id="cfv-kembali">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to list
                </button>
                <!-- Excel mengunduh POTRETNYA, bukan menghitung ulang proyeksi:
                     ekspor_cf_history.php membaca ap_cf_history_det lewat
                     endpoint yang sama dgn layar ini. -->
                <button type="button" class="app-btn app-btn-success app-btn-ctl app-btn-sm" id="cfv-xls">
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                </button>
            </div>
        </div>
    </div>

    <!-- DUA TAB, SATU HALAMAN. Angka keduanya datang dari potret yang sama;
         Executive Summary cuma menyusun ulang angka yang sudah ada di tab
         Projection, bukan menghitung sendiri. -->
    <div class="cfe-tabs">
        <div class="cfe-tab on" data-tab="proj"><i class="fa fa-table" aria-hidden="true"></i> Projection</div>
        <div class="cfe-tab" data-tab="exec"><i class="fa fa-line-chart" aria-hidden="true"></i> Executive Summary</div>
    </div>

    <div class="cfd-wrap" id="cfv-panel-proj">
        <div class="cfd-cap">
            <b id="cfv-judul">Loading...</b>
            <span id="cfv-sub"></span>
        </div>
        <div class="cfd-scroll">
            <table class="cfd" id="cfv-tbl">
                <thead></thead>
                <tbody><tr><td class="cfd-kosong">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>

    <div class="cfe-wrap" id="cfv-panel-exec" style="display:none;">
        <div class="cfd-cap">
            <b id="cfv-exec-judul">Executive Summary</b>
            <span id="cfv-exec-sub"></span>
        </div>
        <div id="cfv-exec-isi"><div class="cfe-kosong-tbl">Loading...</div></div>
    </div>

</div><!-- /MAIN -->

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
var CFV_DOC = <?php echo json_encode($cfdDoc); ?>;

function cfvAman(t) {
    return String(t === null || t === undefined ? '' : t)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

/* Gaya akuntansi: minus DALAM KURUNG - sama dgn layar proyeksinya. */
function cfvAngka(v) {
    if (v === null || v === undefined || v === '') { return ''; }
    var n = Math.round(Number(v));
    if (!n) { return '<span class="cfd-nol">0</span>'; }
    var s = Math.abs(n).toLocaleString('en-US');
    return n < 0 ? '<span class="cfd-min">(' + s + ')</span>' : s;
}

function cfvKepala(kolom) {
    var h = '<tr><th>Descriptions</th><th class="cfd-num">Total</th>';
    for (var i = 0; i < kolom.length; i++) {
        var k = kolom[i];
        h += '<th class="cfd-num' + (k.akhir_pekan ? ' cfd-wknd' : '') + '">'
           + (k.hari ? '<span class="cfd-dow">' + cfvAman(k.hari) + '</span>' : '')
           + cfvAman(k.judul) + '</th>';
    }
    return h + '</tr>';
}

function cfvIsi(baris, kolom) {
    var h = '', i, j;
    for (i = 0; i < baris.length; i++) {
        var b = baris[i];
        var label = (b.no ? '<span class="cfd-no">' + cfvAman(b.no) + '</span>' : '') + cfvAman(b.label);
        h += '<tr class="r-' + cfvAman(b.tipe) + '">'
           + '<th class="cfd-desc">' + label + '</th>'
           + '<td class="cfd-num cfd-tot">' + cfvAngka(b.tot) + '</td>';
        for (j = 0; j < kolom.length; j++) {
            h += '<td class="cfd-num' + (kolom[j].akhir_pekan ? ' cfd-wknd' : '') + '">'
               + cfvAngka(b.v[j]) + '</td>';
        }
        h += '</tr>';
    }
    return h;
}

/* ---- Executive Summary ---------------------------------------------------
   Susunannya meniru lembar "Executive Summary" di berkas acuan. Angkanya
   sudah jadi dari server (cf_exec_lib.php) - di sini TIDAK ada hitungan,
   supaya tidak ada rumus yang hidup di dua tempat. */
function cfvTanggal(s) {
    if (!s) { return '-'; }
    var b = String(s).split('-');
    var nama = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return b.length === 3 ? (b[2] + ' ' + nama[parseInt(b[1], 10) - 1] + ' ' + b[0]) : s;
}

function cfvKartu(k) {
    var isi;
    if (k.jenis === 'tgl') {
        isi = (k.teks === 'No Shortfall Found') ? '<span class="aman">No Shortfall Found</span>'
            : (k.teks ? cfvTanggal(k.teks) : '<span class="kosong">&mdash;</span>');
    } else if (k.nilai === null) {
        isi = '<span class="kosong">&mdash;</span>';
    } else if (k.jenis === 'angka') {
        isi = Number(k.nilai).toLocaleString('en-US');
    } else {
        isi = cfvAngka(k.nilai);
    }
    return '<div class="cfe-k"><div class="lbl">' + cfvAman(k.label) + '</div>'
         + '<div class="val">' + isi + '</div>'
         + '<div class="sub">' + cfvAman(k.sub) + '</div></div>';
}

function cfvPenggerak(judul, daftar) {
    var h = '<div class="cfe-scroll"><table class="cfe-t"><thead><tr><th>' + cfvAman(judul)
          + '</th><th class="cfe-num">Amount</th></tr></thead><tbody>';
    if (!daftar.length) {
        h += '<tr><td colspan="2" class="cfe-kosong-tbl">No movement</td></tr>';
    }
    for (var i = 0; i < daftar.length; i++) {
        h += '<tr class="cfe-rank"><th><span class="no">' + (i + 1) + '</span>'
           + cfvAman(daftar[i].label) + '</th>'
           + '<td class="cfe-num">' + cfvAngka(daftar[i].nilai) + '</td></tr>';
    }
    return h + '</tbody></table></div>';
}

function cfvPekan(pekan) {
    var i, h = '<div class="cfe-scroll" style="padding:10px 16px 18px;">'
             + '<table class="cfe-t"><thead><tr><th>Week</th>';
    for (i = 0; i < pekan.length; i++) { h += '<th class="cfe-num">#' + pekan[i].no + '</th>'; }
    h += '</tr></thead><tbody>';

    var baris = [
        ['Start Date',      function (p) { return cfvTanggal(p.mulai); }, 'cfe-tgl'],
        ['End Date',        function (p) { return cfvTanggal(p.akhir); }, 'cfe-tgl'],
        ['Opening Balance', function (p) { return cfvAngka(p.buka); },    ''],
        ['Cash Inflows',    function (p) { return cfvAngka(p.masuk); },   ''],
        ['Cash Outflows',   function (p) { return cfvAngka(p.keluar); },  ''],
        ['Closing Balance', function (p) { return cfvAngka(p.tutup); },   'cfe-tebal'],
        ['Lowest Balance',  function (p) { return cfvAngka(p.rendah); },  '']
    ];
    var fasil = [
        ['&ndash; IDR',          function (p) { return cfvAngka(p.idr); },   ''],
        ['&ndash; USD (in IDR)', function (p) { return cfvAngka(p.usd); },   ''],
        ['Total Facility',       function (p) { return cfvAngka(p.total); }, 'cfe-tebal']
    ];

    function tulis(daftar) {
        var k, j, t = '';
        for (k = 0; k < daftar.length; k++) {
            t += '<tr class="' + daftar[k][2] + '"><th>' + daftar[k][0] + '</th>';
            for (j = 0; j < pekan.length; j++) {
                t += '<td class="cfe-num ' + daftar[k][2] + '">' + daftar[k][1](pekan[j]) + '</td>';
            }
            t += '</tr>';
        }
        return t;
    }

    h += tulis(baris);
    h += '<tr class="cfe-pisah"><th colspan="' + (pekan.length + 1) + '">Undrawn Credit Facility</th></tr>';
    h += tulis(fasil);
    return h + '</tbody></table></div>';
}

function cfvIsiExec(res) {
    var h = '<div class="cfe-kpi">', i;
    for (i = 0; i < res.kpi.length; i++) { h += cfvKartu(res.kpi[i]); }
    h += '</div>';

    h += '<div class="cfe-sec">Cash Flow Drivers</div><div class="cfe-dua">'
       + cfvPenggerak('Top Cash Inflows', res['in'])
       + cfvPenggerak('Top Cash Outflows', res.out) + '</div>';

    h += '<div class="cfe-sec">Weekly Liquidity Outlook</div>';
    /* Potret Weekly/Monthly berkolom REKENING, jadi tidak punya tanggal sama
       sekali - tabel mingguannya mustahil disusun. Ditulis terang-terangan,
       bukan ditinggal kosong spt kehilangan data. */
    h += res.pekan.length ? cfvPekan(res.pekan)
                          : '<div class="cfe-catatan"><i class="fa fa-info-circle"></i> '
                            + cfvAman(res.catatan || 'Available for Daily snapshots only.') + '</div>';
    $('#cfv-exec-isi').html(h);
}

var CFV_EXEC_DIMUAT = false;

function cfvMuatExec() {
    if (CFV_EXEC_DIMUAT || !CFV_DOC) { return; }
    CFV_EXEC_DIMUAT = true;
    $.post('ajx_cf_exec_summary.php', { doc_number: CFV_DOC }, null, 'json')
        .done(function (res) {
            if (!res || res.status !== 'success') {
                CFV_EXEC_DIMUAT = false;
                $('#cfv-exec-isi').html('<div class="cfe-kosong-tbl">'
                    + cfvAman((res && res.message) ? res.message : 'Unknown error') + '</div>');
                return;
            }
            var i = res.info || {};
            $('#cfv-exec-judul').text((i.doc_number || CFV_DOC) + '  \u2014  ' + (i.type || ''));
            $('#cfv-exec-sub').text($('#cfv-sub').text());
            cfvIsiExec(res);
        })
        .fail(function (x) {
            CFV_EXEC_DIMUAT = false;
            $('#cfv-exec-isi').html('<div class="cfe-kosong-tbl">Request failed</div>');
            Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
        });
}

$(function () {
    $('#cfv-kembali').on('click', function () { window.location = 'cf_history_projection.php'; });

    /* Tab: isinya DIMUAT SAAT DIBUKA, bukan sekaligus di awal - yang dicari
       orang biasanya tabel proyeksinya, jadi tidak perlu dua permintaan
       setiap kali halaman dibuka. */
    $('.cfe-tab').on('click', function () {
        var t = $(this).data('tab');
        $('.cfe-tab').removeClass('on');
        $(this).addClass('on');
        $('#cfv-panel-proj').toggle(t === 'proj');
        $('#cfv-panel-exec').toggle(t === 'exec');
        if (t === 'exec') { cfvMuatExec(); }
    });

    /* Nomor dokumen memuat garis miring (CF-PPR/1126/00001); tanpa
       encodeURIComponent potongan sesudah garis miring pertama akan dibaca
       sbg bagian alamat, bukan nilai. */
    $('#cfv-xls').on('click', function () {
        if (!CFV_DOC) { return; }
        window.location = 'ekspor_cf_history.php?doc_number=' + encodeURIComponent(CFV_DOC);
    });

    if (!CFV_DOC) {
        $('#cfv-judul').text('No document');
        $('#cfv-tbl tbody').html('<tr><td class="cfd-kosong">No document number was given.</td></tr>');
        return;
    }

    $.post('ajx_cf_history_detail.php', { doc_number: CFV_DOC }, null, 'json')
        .done(function (res) {
            if (!res || res.status !== 'success') {
                $('#cfv-judul').text(CFV_DOC);
                $('#cfv-sub').text('');
                $('#cfv-tbl tbody').html('<tr><td class="cfd-kosong">'
                    + cfvAman((res && res.message) ? res.message : 'Unknown error') + '</td></tr>');
                return;
            }
            var i = res.info;
            $('#cfv-judul').text(i.doc_number + '  —  ' + i.type);
            $('#cfv-sub').text(i.periode + '  ·  Rate ' + Number(i.rate).toLocaleString('en-US')
                               + '  ·  ' + i.pc + '  ·  ' + i.bank
                               + '  ·  saved by ' + i.created_by + ' ' + i.created
                               + (i.status === 'CANCEL' ? '  ·  CANCELLED' : ''));

            var isi = cfvIsi(res.baris, res.kolom);
            if (res.pinjaman && res.pinjaman.length) {
                isi += '<tr class="r-spasi"><td colspan="' + (res.kolom.length + 2) + '"></td></tr>'
                     + cfvIsi(res.pinjaman, res.kolom);
            }
            $('#cfv-tbl thead').html(cfvKepala(res.kolom));
            $('#cfv-tbl tbody').html(isi);
        })
        .fail(function (x) {
            $('#cfv-sub').text('');
            Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
        });
});
</script>
