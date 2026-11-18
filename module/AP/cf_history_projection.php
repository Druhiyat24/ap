<?php
/* ============================================================================
   HISTORY PROJECTION (Cash Flow) - membaca potret yang disimpan Save History
   di layar Projection Report.

   DUA KARTU: daftar dokumen di atas, dan isi satu dokumen di bawah yang baru
   muncul sesudah sebuah dokumen dibuka.

   ANGKANYA TIDAK DIHITUNG ULANG. Seluruhnya dibaca dari ap_cf_history_det -
   itu memang inti potret: harus tetap memperlihatkan angka saat Save
   ditekan, walau kurs, rencana bayar, atau saldo awalnya sudah berubah
   sejak itu. Lihat ajx_cf_history_detail.php.

   Tabel matriksnya memakai gaya yang SAMA dgn layar proyeksinya
   (../css/app-cf-matrix.css), bukan salinan - dua tabel itu harus terlihat
   sama persis.

   header.php tidak memuat satu pun pustaka JS, jadi halaman ini memuat
   sendiri yang dibutuhkannya.
   ============================================================================ */
include '../header.php';
?>

<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<style>
/* Kotak cari, pemilih jumlah baris dan tombol halaman SUDAH didandani
   app-ftr-list.css lewat .ftl-tblwrap - jangan ditulis ulang di sini.

   Yang dikecualikan cuma WARNA KEPALANYA: halaman ini biru tua sejak awal
   dan tidak diminta berubah, sedangkan .ftl-tbl thead th bawaannya biru
   muda. !important WAJIB - aturan bawaannya juga !important, jadi tanpa itu
   aturan di sini kalah tanpa pesan kesalahan apa pun. */
#cfh-daftar thead th {
    background-color:#1E3A8A !important; color:#fff !important;
    border-bottom:2px solid #16306e !important;
}
.cfh-num { text-align:right; font-variant-numeric:tabular-nums; }
.cfh-doc { color:#1d4ed8; font-weight:600; cursor:pointer; white-space:nowrap; }
.cfh-doc:hover { text-decoration:underline; }
.cfh-lencana { display:inline-block; padding:2px 9px; border-radius:999px;
    font-size:10.5px; font-weight:700; letter-spacing:.3px; }
.cfh-aktif { background:#dcfce7; color:#166534; }
.cfh-batal { background:#fee2e2; color:#991b1b; }
/* Pembungkus tombol aksi - jarak antar tombol, sama dgn AP History
   Projection (.hpj-aksi). */
.cfh-aksi { display:inline-flex; gap:6px; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">

    <div class="ftl-card">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
            <div>
                <h1>History Projection</h1>
                <span class="ftl-crumb">Bank &rsaquo; Report &rsaquo; Projection &rsaquo; History Projection</span>
            </div>
        </div>

        <div class="ftl-body">
            <form onsubmit="return false;">
                <div class="row">

                    <div class="col-12 col-sm-6 col-xl-3 mb-2">
                        <label for="cfh-dari" class="form-label"><b>Saved From</b></label>
                        <input type="text" class="form-control tanggal" id="cfh-dari"
                               value="<?php echo date('01-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3 mb-2">
                        <label for="cfh-sampai" class="form-label"><b>Saved To</b></label>
                        <input type="text" class="form-control tanggal" id="cfh-sampai"
                               value="<?php echo date('t-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="cfh-status" class="form-label"><b>Status</b></label>
                        <select class="form-control app-select2" id="cfh-status">
                            <option value="ALL" selected="selected">All</option>
                            <option value="ACTIVE">Active</option>
                            <option value="CANCEL">Cancel</option>
                        </select>
                    </div>

                    <div class="col-12 col-xl-4 mb-2 ftl-actions">
                        <button type="button" class="app-btn app-btn-primary app-btn-ctl app-btn-sm" id="cfh-cari">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="ftl-card" style="margin-top:16px;">
        <div class="ftl-body">
            <div class="ftl-tblwrap">
                <table id="cfh-daftar" class="table ftl-tbl" cellspacing="0">
                    <thead><tr></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /MAIN -->

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
function cfhAman(t) {
    return String(t === null || t === undefined ? '' : t)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

/* Susunan kolom daftar - satu tempat, dipakai kepala tabel maupun
   DataTable-nya. */
var CFH_KOLOM = [
    { data: 'doc_number',  title: 'Document No',   kelas: 'text-left' },
    { data: 'periode',     title: 'Period',        kelas: 'text-left' },
    { data: 'type',        title: 'Type',          kelas: 'text-center' },
    { data: 'pc',          title: 'Profit Center', kelas: 'text-left' },
    { data: 'bank',        title: 'Bank',          kelas: 'text-left' },
    { data: 'rate',        title: 'Rate',          kelas: 'cfh-num' },
    { data: 'jml_baris',   title: 'Rows',          kelas: 'cfh-num' },
    { data: 'created_by',  title: 'Created By',    kelas: 'text-left' },
    { data: 'created',     title: 'Created Date',  kelas: 'text-left' },
    { data: 'status',      title: 'Status',        kelas: 'text-center' },
    { data: null,          title: 'Action',        kelas: 'text-center' }
];

var cfhTbl = null;

function cfhGambar(data) {
    /* DataTable lama DIBONGKAR dulu. Tanpa destroy(), pemanggilan kedua
       cuma mengembalikan tabel yang lama dan datanya tidak berganti. */
    if (cfhTbl) { cfhTbl.destroy(); cfhTbl = null; }
    $('#cfh-daftar thead').empty();
    $('#cfh-daftar tbody').empty();

    var th = '', i;
    for (i = 0; i < CFH_KOLOM.length; i++) { th += '<th>' + cfhAman(CFH_KOLOM[i].title) + '</th>'; }
    $('#cfh-daftar thead').html('<tr>' + th + '</tr>');

    var kolom = [], defs = [];
    for (i = 0; i < CFH_KOLOM.length; i++) {
        var kd = { data: CFH_KOLOM[i].data };
        if (CFH_KOLOM[i].title === 'Action') {
            kd.orderable = false;
            kd.render = function (d, t, row) {
                var batal = (row.status === 'CANCEL');
                return '<span class="cfh-aksi">'
                     + '<button type="button" class="app-btn app-btn-primary app-btn-sm cfh-lihat" data-doc="'
                     + cfhAman(row.doc_number) + '"><i class="fa fa-eye" aria-hidden="true"></i> View</button>'
                     /* Realisasi: laporan yang sudah ada, dibuka dgn membawa
                        NOMOR POTRETNYA saja - periode & daftar akunnya diambil
                        laporan itu dari potret yang sama. Ekspor Excel-nya ada
                        DI DALAM laporan itu, tidak lagi di baris ini. */
                     + '<button type="button" class="app-btn app-btn-success app-btn-sm cfh-real" data-doc="'
                     + cfhAman(row.doc_number) + '"><i class="fa fa-balance-scale" aria-hidden="true"></i> Realization</button>'
                     /* Tombol cancel TIDAK boleh berkelas cfh-batal: nama itu
                        sudah dipakai lencana status. */
                     + (batal ? '' : '<button type="button" class="app-btn app-btn-danger app-btn-sm cfh-cancel" data-doc="'
                        + cfhAman(row.doc_number) + '"><i class="fa fa-ban" aria-hidden="true"></i> Cancel</button>')
                     + '</span>';
            };
        } else if (CFH_KOLOM[i].data === 'doc_number') {
            kd.render = function (d, t) {
                if (t !== 'display') { return d; }
                return '<span class="cfh-doc" data-doc="' + cfhAman(d) + '">' + cfhAman(d) + '</span>';
            };
        } else if (CFH_KOLOM[i].data === 'status') {
            kd.render = function (d, t) {
                if (t !== 'display') { return d; }
                var batal = (d === 'CANCEL');
                return '<span class="cfh-lencana ' + (batal ? 'cfh-batal' : 'cfh-aktif') + '">'
                     + (batal ? 'Cancel' : 'Active') + '</span>';
            };
        } else if (CFH_KOLOM[i].data === 'rate' || CFH_KOLOM[i].data === 'jml_baris') {
            kd.render = function (d, t) {
                /* Pengurutan memakai ANGKANYA, bukan teks berkoma - kalau
                   tidak, 9 akan terurut sesudah 10.000. */
                return (t === 'display') ? Number(d).toLocaleString('en-US') : Number(d);
            };
        }
        kolom.push(kd);
        defs.push({ targets: i, className: CFH_KOLOM[i].kelas });
    }

    cfhTbl = $('#cfh-daftar').DataTable({
        data: data,
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

function cfhDaftar() {
    $.post('ajx_cf_history_list.php', {
        start_date: $('#cfh-dari').val(),
        end_date:   $('#cfh-sampai').val(),
        status:     $('#cfh-status').val()
    }, null, 'json')
        .done(function (res) {
            if (!res || res.status !== 'success') {
                Swal.fire({ icon: 'warning', title: 'Cannot load', text: (res && res.message) ? res.message : 'Unknown error' });
                cfhGambar([]);
                return;
            }
            cfhGambar(res.data || []);
        })
        .fail(function (x) {
            cfhGambar([]);
            Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
        });
}



/* Rincian dibuka di HALAMAN SENDIRI, bukan ditempel di bawah daftar
   (permintaan pemakai 17 Nov 2026). Tabelnya setinggi laporan aslinya, jadi
   kalau ditempel di bawah harus digulir jauh dulu baru kelihatan. Polanya
   sama dgn AP History Projection -> ap_realization_report.php. */
function cfhBuka(doc) {
    window.location = 'cf_history_detail.php?doc_number=' + encodeURIComponent(doc);
}

$(function () {
    $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
    $('.app-select2').select2({ width: '100%' });

    $('#cfh-cari').on('click', cfhDaftar);

    /* ---- Realisasi ----
       SATU parameter saja, nomor potretnya; periode & daftar akunnya dibaca
       laporan itu dari potret yang sama (cfrp_header()). Jadi layarnya tidak
       perlu saringan tanggal/akun lagi, dan tidak ada jalan untuk membandingkan
       potret ini dgn rentang yang lain.

       Halaman yang sama, bukan tab baru - laporannya punya tombol Back ke sini,
       persis AP History Projection -> ap_realization_report.php. */
    $('#cfh-daftar').on('click', '.cfh-real', function () {
        window.location = 'report-cashflow-realisation.php?doc_number='
                        + encodeURIComponent($(this).data('doc'));
    });

    $('#cfh-daftar').on('click', '.cfh-doc, .cfh-lihat', function () { cfhBuka($(this).data('doc')); });

    $('#cfh-daftar').on('click', '.cfh-cancel', function () {
        var doc = $(this).data('doc');
        Swal.fire({
            icon: 'question',
            title: 'Cancel this snapshot?',
            html: 'Document <b>' + cfhAman(doc) + '</b> will be marked as cancelled.<br>'
                + 'Its figures are kept and can still be opened.',
            showCancelButton: true,
            confirmButtonText: 'Yes, cancel it'
        }).then(function (p) {
            if (!p.isConfirmed) { return; }
            $.post('batal_cf_history.php', { doc_number: doc }, null, 'json')
                .done(function (res) {
                    if (!res || res.status !== 'success') {
                        Swal.fire({ icon: 'warning', title: 'Not cancelled', text: (res && res.message) ? res.message : 'Unknown error' });
                        return;
                    }
                    Swal.fire({ icon: 'success', title: 'Cancelled', text: doc });
                    cfhDaftar();
                })
                .fail(function (x) {
                    Swal.fire({ icon: 'error', title: 'Request failed', text: (x.responseText || '').slice(0, 300) });
                });
        });
    });

    cfhDaftar();
});
</script>
