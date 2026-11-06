<?php
/* ============================================================================
   Update Payment Plan - DAFTAR DOKUMEN.  Berkas ini BERDIRI SENDIRI
   (1 menu = 1 berkas). Ini yang ditunjuk menunya; formulirnya dibuka lewat
   tombol Create New (create-payment-plan.php), sama seperti List/Create
   Petty Cash Out.

   Bentuk dokumennya: satu nomor PPU/<bulan><tahun>/<urut> berisi banyak
   payment voucher dgn SATU tanggal rencana bayar. Dokumen yang salah
   DIBATALKAN, bukan ditimpa - jadi riwayat penjadwalan terbaca dari daftar
   ini sendiri.

   Tanggal rencana di dokumen inilah yang menggerakkan AP Payment Projection
   Report dan laporan realisasinya.

   SUSUNAN TAMPILANNYA MENGIKUTI petty-cashout.php (diminta pemakai,
   4 Nov 2026): DUA kartu - kartu saringan di atas, kartu tabel di bawah -
   tombol ber-kosakata .app-btn, tabel .ftl-tblwrap/.ftl-tbl berkepala
   .thead-dark, dan overlay .app-loading-wrap. Sebelumnya halaman ini memakai
   satu kartu dgn dua panel dan tombol Bootstrap biasa, jadi tampil lain dari
   menu-menu AP yang sudah jalan.

   MODAL RINCIANNYA TIDAK ikut kosakata .ftl- (diubah 5 Nov 2026): lihat
   keterangan panjang tepat di atas markup modalnya. Ringkasnya, aturan
   .ftl-modal mengharapkan susunan DOM yang berbeda dari yang dipakai di
   sini, jadi hampir semuanya tidak pernah kena dan kolom tabelnya tampak
   berdempetan. Sekarang memakai .app-modal / .app-mod-info / .app-mod-tbl,
   sama dgn modal rincian voucher di create-payment-plan.php.
   ============================================================================ */
include '../header.php';
?>

<!-- Tiap berkas CSS ditaut SENDIRI-SENDIRI dgn penanda versi dari filemtime,
     jadi tidak kena jebakan cache yang menimpa app-skin.css (yang meng-@import
     app-skin-form.css TANPA nomor versi). -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">

<!-- Kosakata .ftl- dipakai bersama daftar FTR CBD/DP, Petty Cash Out dan
     Update BPB - bentuk & warnanya sama dgn List Memorial Journal. Ditaut
     HANYA oleh halaman daftar, bukan dari header.php, supaya menu lain tidak
     ikut berubah. -->
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<style>
/* Hanya dua hal yang belum ada di app-skin-form.css. Sisanya diurus kosakata
   .app-modal / .app-mod-info / .app-mod-tbl. Namanya disamakan dgn
   .pv-rinc-judul / #pv-tunggu di create-payment-plan.php supaya kedua layar
   Payment Plan terbaca sebagai satu keluarga. */
.pp-rinc-judul { font-size: 11px; font-weight: 700; text-transform: uppercase;
                 letter-spacing: .5px; color: #64748b; margin: 4px 0 8px; }
.pp-rinc-judul i { color: #94a3b8; margin-right: 4px; }
.pp-tunggu { padding: 18px; text-align: center; color: #94a3b8; font-size: 12px; }

/* Nomor voucher di modal: ditegaskan sedikit supaya kolom paling kiri tetap
   jadi jangkar baca, tanpa dibuat setebal judul. */
#details .app-mod-tbl tbody td:first-child { font-weight: 600; color: #1e293b; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">

    <!-- Kartu saringan. Kepala kartunya (kotak ikon navy + judul + jejak
         menu) mengikuti List Petty Cash Out. -->
    <div class="ftl-card">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></span>
            <div>
                <h1>List Payment Plan</h1>
                <span class="ftl-crumb">AP &rsaquo; Payment Plan &rsaquo; Update Payment Plan</span>
            </div>
        </div><!-- /.ftl-head -->

        <div class="ftl-panel">
            <form id="form-data" action="list-payment-plan.php" method="post">
                <div class="form-row">

                    <!-- Saringan "Document Number" DIHAPUS (diminta pemakai,
                         5 Nov 2026): kotak Search bawaan DataTables di atas
                         tabel sudah mencari nomor dokumen, jadi isian ini
                         hanya mengerjakan hal yang sama dua kali.
                         ajx_list-payment-plan.php & ekspor_payment_plan.php
                         TETAP menerima parameter doc_num - tidak ada yang
                         dibongkar di sisi server, cuma tidak dikirim lagi
                         dari sini. -->
                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="status" class="form-label"><b>Status</b></label>
                        <select class="form-control app-select2" name="status" id="status">
                            <option value="ALL" selected="selected">ALL</option>
                            <option value="POST">POST</option>
                            <option value="CANCEL">CANCEL</option>
                        </select>
                    </div>

                    <!-- Saringan tanggalnya memakai TANGGAL RENCANA BAYAR,
                         bukan tanggal dokumen dibuat - yang dicari tim AP
                         biasanya "apa saja yang dijadwalkan bulan ini". -->
                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="start_date" class="form-label"><b>Plan Date From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" autocomplete="off"
                               value="<?php echo !empty($_POST['start_date']) ? htmlspecialchars($_POST['start_date'], ENT_QUOTES) : date('01 M Y'); ?>" placeholder="Start Date">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" autocomplete="off"
                               value="<?php echo !empty($_POST['end_date']) ? htmlspecialchars($_POST['end_date'], ENT_QUOTES) : date('t M Y'); ?>" placeholder="End Date">
                    </div>

                    <div class="col-12 col-xl-6 mb-2 ftl-actions">
                        <button type="submit" class="app-btn app-btn-primary app-btn-sm">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <!-- Hak aksesnya SATU untuk dua layar (menurole 145),
                             jadi siapa pun yang melihat menu ini boleh membuat
                             dokumennya - tidak ada penjaga terpisah seperti
                             'Create Cash' di Petty Cash Out. -->
                        <button id="btncreate_new" type="button" class="app-btn app-btn-success app-btn-sm">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i> Create New
                        </button>
                        <button type="button" id="btnExportExcel" class="app-btn app-btn-excel app-btn-sm">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                        </button>
                    </div>

                </div>
            </form>
        </div><!-- /.ftl-panel -->
    </div><!-- /.ftl-card: kartu saringan -->

    <div class="ftl-card mt-3">
        <div class="ftl-body">
            <!-- .app-loading-wrap: area yang ditutup overlay saat data
                 ditarik. Markup & kelasnya milik css/app-loading.css yang
                 dipakai bersama menu lain, jadi seragam antar halaman. -->
            <div class="app-loading-wrap" id="ppLoad">
              <div class="app-loading">
                <div class="app-loading-box">
                  <div class="app-spinner"></div>
                  <div class="app-loading-text">Loading data...</div>
                </div>
              </div>

              <!-- TANPA .table-responsive: pembungkus yang bisa digulir ke
                   samping membuat modul Responsive menyangka ruangnya selalu
                   cukup, jadi tanda "+" tidak pernah muncul. -->
              <div class="ftl-tblwrap">
                <table id="mytable" class="table ftl-tbl" role="grid" cellspacing="0" width="100%">
                    <thead>
                        <tr class="thead-dark">
                            <!-- Kolom tanda "+": pembuka baris rincian. -->
                            <th style="text-align: center;vertical-align: middle;"></th>
                            <th style="text-align: center;vertical-align: middle;">Document Number</th>
                            <th style="text-align: center;vertical-align: middle;">Payment Plan Date</th>
                            <!-- Kolom "Vouchers" DIHAPUS (diminta pemakai,
                                 5 Nov 2026). Jumlah vouchernya tetap ada di
                                 modal rincian, berikut daftar vouchernya -
                                 jadi tidak ada keterangan yang hilang. -->
                            <th style="text-align: center;vertical-align: middle;">Total Amount</th>
                            <th style="text-align: center;vertical-align: middle;">Description</th>
                            <th style="text-align: center;vertical-align: middle;">Status</th>
                            <th style="text-align: center;vertical-align: middle;">User Created</th>
                            <th style="text-align: center;vertical-align: middle;width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
              </div><!-- /.ftl-tblwrap -->
            </div><!-- /.app-loading-wrap -->
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card: kartu tabel -->
</div><!-- /.container-fluid -->

<!-- Modal rincian dokumen -->
<!-- .is-wide: varian modal rincian LEBAR di app-ftr-list.css - mengatur
     jarak kolom, mematikan penebalan .ftl-doc/.ftl-amt di dalam #details
     (modal ini cuma untuk dibaca), menyembunyikan kartu info yang kosong,
     dan menata ulang kartu-kartunya di layar sempit. -->
<!-- ============================================================================
     Modal rincian dokumen. DISUSUN ULANG 5 Nov 2026 (diminta pemakai).

     Bentuk lamanya <div class="container"><div class="row"><div class="modal-body col-6">
     - dan hampir SELURUH aturan perapian di app-ftr-list.css berpola
     `.ftl-modal.is-wide .modal-body > .row > [class*="col-"]`, yaitu
     .modal-body sebagai INDUK dari .row. Di sini .modal-body justru jadi
     ANAK, jadi aturan itu tidak pernah kena satu pun: tiap kotak info malah
     kebagian padding & latar milik .modal-body sendiri, dan tabel rinciannya
     tidak dapat perapian jarak selnya. Itu sebabnya kolom tampak berdempetan
     sampai terbaca menyatu ("16.513.500,00TES").

     Sekarang memakai kosakata .app-modal / .app-mod-info / .app-mod-tbl dari
     app-skin-form.css - berkas yang sudah ditaut halaman ini - yaitu kosakata
     yang SAMA dengan modal rincian voucher di create-payment-plan.php. Jadi
     dua layar Payment Plan berbicara satu bahasa, dan jarak selnya diatur
     aturan yang memang dibuat untuk tabel di dalam modal.
     ============================================================================ -->
<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="detail" aria-hidden="true">
    <div class="modal-dialog app-modal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <span id="txt_doc"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="fa fa-times"></span></button>
            </div>
            <div class="modal-body">
                <!-- Kepala dokumen: terisi dari baris yang sudah ada di tabel,
                     jadi tampil seketika. -->
                <div class="app-mod-info" id="pp-info"></div>

                <div class="pp-rinc-judul"><i class="fa fa-list" aria-hidden="true"></i> Payment vouchers</div>
                <div id="details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="app-btn app-btn-dark app-btn-sm" data-dismiss="modal">
                    <i class="fa fa-times" aria-hidden="true"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<!-- select2 untuk dropdown saringan. CSS-nya sudah dimuat header.php;
     skin tingginya dari app-skin-form.css, jadi sebaris dgn kotak tanggal. -->
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  /* Sama persis dgn create-payment-plan.php: panel pilihan ditempel ke <body>
     supaya tidak terpotong kartu, dan daftar pendek (Status, 3 pilihan) tidak
     diberi kotak cari - ambang 8 opsi. */
  $(function () {
    $('.app-select2').each(function () {
      var $s = $(this);
      var o = { width: '100%', dropdownParent: $('body') };
      if ($s.find('option').length < 8) { o.minimumResultsForSearch = Infinity; }
      $s.select2(o);
    });
  });
</script>

<script>
    // Hide submenus
    $('#body-row .collapse').collapse('hide');

    // Collapse/Expand icon
    $('#collapse-icon').addClass('fa-angle-double-left');

    // Collapse click
    $('[data-toggle=sidebar-colapse]').click(function() {
        SidebarCollapse();
    });

    function SidebarCollapse() {
        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

        var SeparatorTitle = $('.sidebar-separator-title');
        if (SeparatorTitle.hasClass('d-flex')) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
</script>

<script>
    $(document).ready(function() {
        /* "10 Oct 2026" - bentuk yang sama dgn formulir Create
           (diganti pemakai 5 Nov 2026). Bentuk ini juga yang dikirim ke
           ajx_list-payment-plan.php & ekspor_payment_plan.php; keduanya
           membacanya lewat strtotime(), yang tidak bisa salah menebak bulan
           kalau nama bulannya ditulis. */
        $('.tanggal').datepicker({
            format: "dd M yyyy",
            autoclose: true
        });
    });
</script>

<script type="text/javascript">
    var datatable = $('#mytable').DataTable({
        ordering: false,
        processing: true,
        serverSide: false,
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,

        // Kotak Search, nomor halaman & jumlah baris ikut diingat, jadi
        // kembali dari formulir Create mendarat di tampilan yang sama.
        // stateDuration -1 = sessionStorage (bukan localStorage), seumur tab.
        stateSave: true,
        stateDuration: -1,

        /* RESPONSIVE: kolom yang tidak muat DIKOLAPS jadi baris rincian yang
           dibuka lewat tanda "+", bukan dipaksa digulir ke samping - itu yang
           bikin tabel tak terbaca di HP. Modulnya sudah terbundel di
           datatables.min.js dan CSS-nya sudah dimuat header.php. */
        responsive: {
            details: { type: 'column', target: 0 }
        },

        ajax: {
            url: 'ajx_list-payment-plan.php',
            type: 'POST',
            data: function(d) {
                /* doc_num tidak dikirim lagi - isiannya dihapus dari
                   saringan. Endpoint-nya tetap menerima parameter itu. */
                d.status     = $('#status').val();
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
            },
            // Balasan yang membawa pesan galat SQL ditampilkan apa adanya -
            // tanpa ini tabelnya cuma tampak kosong tanpa sebab.
            dataSrc: function(res) {
                if (res.error) {
                    Swal.fire({ icon: 'error', title: 'Could not load the list', text: res.error });
                    return [];
                }
                return res.data || [];
            },
            // Tanpa ini overlay-nya menggantung terus kalau permintaannya
            // gagal, dan user tidak tahu apa yang terjadi.
            error: function(xhr) {
                $('#ppLoad').removeClass('is-loading');
                Swal.fire({
                    icon: 'error', title: 'Failed to load the list',
                    text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response')
                });
            }
        },

        /* responsivePriority: makin KECIL angkanya, makin lama kolom itu
           dipertahankan saat layar menyempit. Urutannya diambil dari cara
           orang membaca daftar ini: nomor dokumen & tombol aksi harus selalu
           ada, lalu Status, lalu tanggal rencana, baru sisanya. */
        columns: [
            { data: null, defaultContent: '', orderable: false, className: 'dtr-control', responsivePriority: 1 },
            { data: 'doc_number',  responsivePriority: 1 },
            { data: 'plan_date',   responsivePriority: 2 },
            { data: 'total',       responsivePriority: 4 },
            { data: 'keterangan',  responsivePriority: 7 },
            { data: 'status',      responsivePriority: 3 },
            { data: 'user_create', responsivePriority: 6 },
            { data: 'action', orderable: false, searchable: false, responsivePriority: 2 }
        ],

        /* Kelas "all" milik Responsive = kolom ini TIDAK PERNAH dikolaps,
           seberapa pun sempit layarnya. responsivePriority saja tidak cukup:
           itu hanya menentukan urutan dibuang. Tiga yang dikunci: nomor
           dokumen, Status, dan Action.

           SUSUNAN KOLOM sekarang 0..7 (kolom Vouchers dihapus, jadi nomor
           sesudahnya bergeser turun 1):
             0 "+"            1 Document Number   2 Payment Plan Date
             3 Total Amount   4 Description       5 Status
             6 User Created   7 Action */
        columnDefs: [
            { targets: [1],    className: 'text-left ftl-doc all' },
            { targets: [5],    className: 'text-center all' },
            { targets: [7],    className: 'text-center ftl-act-cell all' },
            { targets: [3],    className: 'text-right ftl-amt' },
            { targets: [4],    className: 'text-left' },
            { targets: [2, 6], className: 'text-center' }
        ],

        language: {
            emptyTable: 'No payment plan found for this filter.',
            zeroRecords: 'No payment plan matches your search.'
        }
    });

    /* Overlay mengikuti status processing DataTables. Kotak "Processing"
       bawaannya otomatis disembunyikan app-loading.css, jadi tidak muncul
       dua-duanya. */
    datatable.on('processing.dt', function(e, settings, processing) {
        $('#ppLoad').toggleClass('is-loading', processing);
    });

    function dataTableReload() {
        datatable.ajax.reload();
    }

    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        dataTableReload();
    });

    document.getElementById('btncreate_new').onclick = function() {
        location.href = "create-payment-plan.php";
    };

    document.getElementById('btnExportExcel').onclick = function() {

        Swal.fire({
            title: 'Menyiapkan Excel...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        /* doc_num tidak ikut dikirim - isiannya sudah tidak ada di saringan.
           Ekspornya mengikuti saringan yang terlihat di layar. */
        window.location.href = 'ekspor_payment_plan.php?status=' + encodeURIComponent($('#status').val())
            + '&start_date=' + encodeURIComponent($('#start_date').val())
            + '&end_date=' + encodeURIComponent($('#end_date').val());

        setTimeout(function() {
            Swal.close();
        }, 1500);
    };
</script>

<script type="text/javascript">
    /* Nilai dari server ditulis lewat .html(), jadi harus dilolosi dulu -
       catatan atau nama pemakai yang memuat < atau & tidak boleh merusak
       susunan modalnya. */
    function lolos(v) {
        return $('<div>').text(v === null || v === undefined ? '-' : v).html();
    }

    /* isiRingkas() / isiRingkasHtml() DIHAPUS: keduanya mengisi satu kotak
       .ftl-k/.ftl-v per panggilan, pola modal yang lama. Kepala modal
       sekarang dibangun sekaligus sbg kisi .app-mod-info di dalam
       bukaRincianPp(). Catatan yang perlu dibawa: data.status dan
       data.user_create SUDAH berupa HTML dari server - kalau dilolosi, yang
       tampil justru tag-nya. */

    /* Satu pintu untuk modal rincian - dipakai tombol View DAN klik baris,
       jadi tidak ada dua potongan kode yang harus dijaga agar seragam. */
    function bukaRincianPp(data) {
        if (!data) return;

        $('#mymodal').modal('show');
        $('#details').html('<div class="pp-tunggu"><i class="fa fa-spinner fa-spin"></i> Loading payment vouchers...</div>');

        $('#txt_doc').html(lolos(data.doc_number));

        /* Panel kepala dibangun sekali jadi, bukan diisi per kotak: susunannya
           kisi .app-mod-info, sama dgn modal rincian voucher di halaman
           Create. data.status & data.user_create sudah berupa HTML dari
           server (pil status + baris tanggal), jadi dipasang apa adanya;
           sisanya dilolos-kan. */
        var info = [
            ['Payment Plan Date', lolos(data.plan_date)],
            ['Status',            data.status || '-'],
            ['Vouchers',          lolos(data.jml)],
            ['Total Amount',      data.total || '-'],
            ['User Created',      data.user_create || '-'],
            ['Description',       lolos($('<div>').html(data.keterangan).text())]
        ];
        var hi = '';
        for (var n = 0; n < info.length; n++) {
            var nilai = info[n][1];
            if (nilai === '' || nilai === null || nilai === undefined) { nilai = '-'; }
            hi += '<div><b>' + info[n][0] + '</b><span>' + nilai + '</span></div>';
        }
        $('#pp-info').html(hi);

        $.ajax({
            url: 'ajx_detail-payment-plan.php',
            type: 'POST',
            dataType: 'json',
            data: { doc_number: data.doc_number }
        }).done(function(res) {
            var baris = res.data || [];
            if (!baris.length) {
                $('#details').html('<div class="pp-tunggu">No payment voucher in this document.</div>');
                return;
            }
            /* .app-mod-tbl = tabel di dalam .app-modal (app-skin-form.css):
               jarak sel, kepala biru muda, dan .num untuk angka rata kanan
               ber-tabular-nums. .nowrap dipasang di kolom yang TIDAK BOLEH
               dipatahkan - nomor voucher dan tanggal; hanya Supplier dan
               Description yang boleh membungkus, supaya nama panjang tidak
               memaksa tabelnya melebar. */
            var h = '<table class="app-mod-tbl">'
                  + '<thead><tr>'
                  + '<th>Payment Voucher No</th>'
                  + '<th>Supplier</th>'
                  + '<th>PV Date</th>'
                  + '<th>Due Date</th>'
                  + '<th>Payment Plan Date</th>'
                  + '<th>Curr</th>'
                  + '<th class="num">Amount</th>'
                  + '<th>Description</th>'
                  + '<th>Status</th>'
                  + '</tr></thead><tbody>';
            for (var i = 0; i < baris.length; i++) {
                var b = baris[i];
                h += '<tr>'
                   + '<td class="nowrap">' + b.no_kbon + '</td>'
                   + '<td>' + b.nama_supp + '</td>'
                   + '<td class="nowrap text-center">' + b.tgl_kbon + '</td>'
                   + '<td class="nowrap text-center">' + b.tgl_tempo + '</td>'
                   + '<td class="nowrap text-center">' + b.plan_date + '</td>'
                   + '<td class="text-center">' + b.curr + '</td>'
                   + '<td class="num">' + b.amount + '</td>'
                   + '<td>' + b.keterangan + '</td>'
                   + '<td class="text-center nowrap">' + b.status + '</td>'
                   + '</tr>';
            }
            h += '</tbody></table>';
            $('#details').html(h);
        }).fail(function() {
            $('#details').html('<div class="pp-tunggu" style="color:#b91c1c;">Could not load the payment vouchers.</div>');
        });
    }

    $(document).on("click", ".btn-view-doc", function() {
        bukaRincianPp(datatable.row($(this).closest('tr')).data());
    });

    /* Klik di mana saja pada baris ikut membuka modal - sama spt daftar FTR,
       Petty Cash Out dan Memorial Journal.

       PENJAGA: memblok teks untuk DISALIN tidak boleh ikut membuka modal.
       Dibedakan dari jarak geser tetikus antara mousedown & klik, dan dari
       ada-tidaknya teks yang tersorot. */
    var tekanX = 0, tekanY = 0;
    $(document).on('mousedown', '#mytable tbody tr', function(e) {
        tekanX = e.pageX; tekanY = e.pageY;
    });
    $(document).on('click', '#mytable tbody tr', function(e) {
        if ($(e.target).closest('button, a, input, select, .dtr-control').length) { return; }
        if (Math.abs(e.pageX - tekanX) > 4 || Math.abs(e.pageY - tekanY) > 4) { return; }
        var pilihan = window.getSelection ? String(window.getSelection()) : '';
        if (pilihan.length > 0) { return; }
        bukaRincianPp(datatable.row(this).data());
    });

    /* ---- batalkan dokumen ---- */
    $(document).on('click', '.btn-cancel-doc', function(e) {
        e.stopPropagation();
        var doc = $(this).data('doc');
        Swal.fire({
            icon: 'warning',
            title: 'Cancel this document?',
            html: 'Payment plan <b>' + doc + '</b> will no longer be used by the projection report.',
            input: 'text',
            inputPlaceholder: 'Reason (optional)',
            showCancelButton: true,
            confirmButtonText: 'Cancel document',
            cancelButtonText: 'Keep it'
        }).then(function(r) {
            if (!r.isConfirmed) { return; }
            Swal.fire({ title: 'Cancelling...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            $.ajax({
                url: 'cancel_payment_plan.php',
                type: 'POST',
                dataType: 'json',
                data: { doc_number: doc, alasan: r.value || '' }
            }).done(function(res) {
                if (res.status !== 'success') {
                    Swal.fire({ icon: 'error', title: 'Not cancelled', text: res.message || 'Unknown error' });
                    return;
                }
                Swal.fire({ icon: 'success', title: res.message, timer: 1600, showConfirmButton: false });
                dataTableReload();
            }).fail(function() {
                Swal.fire({ icon: 'error', title: 'Not cancelled', text: 'The server did not answer. Please try again.' });
            });
        });
    });
</script>
