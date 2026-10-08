<?php include '../header.php' ?>

<!-- Tiap berkas CSS ditaut SENDIRI-SENDIRI dgn penanda versi dari
     filemtime, jadi tidak kena jebakan cache yang menimpa app-skin.css
     (yang meng-@import app-skin-form.css TANPA nomor versi). -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">

<!-- Kosakata .ftl- dipakai bersama daftar FTR CBD/DP - bentuk & warnanya
     sama dgn List Memorial Journal. Ditaut HANYA oleh halaman daftar,
     bukan dari header.php, supaya menu lain tidak ikut berubah. -->
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
    <!-- Kartu filter. Kepala kartunya (kotak ikon navy + judul + jejak
         menu) mengikuti List Memorial Journal: pita gradien selebar kartu
         diganti kotak ikon supaya warnanya jadi aksen, bukan latar. -->
    <div class="ftl-card">
        <div class="ftl-head">
            <span class="ftl-head-icon"><i class="fa fa-money" aria-hidden="true"></i></span>
            <div>
                <h1>List Petty Cash Out</h1>
                <span class="ftl-crumb">AP &rsaquo; Petty Cash Out</span>
            </div>
        </div><!-- /.ftl-head -->

        <div class="ftl-panel">
            <form id="form-data" action="petty-cashout.php" method="post">
                <div class="form-row">

                    <div class="col-12 col-sm-6 col-xl-3 mb-2">
                        <label for="reference" class="form-label"><b>Reference</b></label>
                        <select class="form-control selectpicker" name="reference" id="reference" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" selected="selected">ALL</option>
                            <?php
                            $reference = isset($_POST['reference']) ? $_POST['reference'] : null;
                            $sqlRef = mysqli_query($conn1, "select DISTINCT reff ref_doc from c_petty_cashout_h where reff != ''");
                            while ($rowRef = mysqli_fetch_array($sqlRef)) {
                                $data = $rowRef['ref_doc'];
                                $isSelected = ($data == $reference) ? ' selected="selected"' : '';
                                echo '<option value="' . htmlspecialchars($data) . '"' . $isSelected . '>' . htmlspecialchars($data) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="doc_num" class="form-label"><b>Document Number</b></label>
                        <input type="text" class="form-control form-control-sm" name="doc_num" id="doc_num" autocomplete="off"
                            value="<?php echo isset($_POST['doc_num']) ? htmlspecialchars($_POST['doc_num']) : ''; ?>">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" autocomplete="off"
                            value="<?php echo !empty($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : date('d-m-Y'); ?>" placeholder="Start Date">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" autocomplete="off"
                            value="<?php echo !empty($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : date('d-m-Y'); ?>" placeholder="End Date">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3 mb-2 ftl-actions">
                        <button type="submit" class="app-btn app-btn-primary app-btn-sm">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        
                        <?php
                        $querys = mysqli_query($conn2, "select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Cash'");
                        $rs = mysqli_fetch_array($querys);
                        $id = isset($rs['id']) ? $rs['id'] : 0;
                        if ($id == '39') {
                            echo '<button id="btncreate_new" type="button" class="app-btn app-btn-success app-btn-sm"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create New</button>';
                        }
                        ?>
                        <button type="button" id="btnExportExcel" class="app-btn app-btn-excel app-btn-sm">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                        </button>
                    </div>

                </div>
            </form>
        </div><!-- /.ftl-panel -->
    </div><!-- /.ftl-card: kartu filter -->

    <div class="ftl-card mt-3">
        <div class="ftl-body">
            <!-- .app-loading-wrap: area yang ditutup overlay saat data
                 ditarik. Markup & kelasnya milik css/app-loading.css yang
                 dipakai bersama menu lain, jadi seragam antar halaman. -->
            <div class="app-loading-wrap" id="pcoLoad">
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
                            <th style="text-align: center;vertical-align: middle;">Date</th>
                            <th style="text-align: center;vertical-align: middle;">Reference</th>
                            <th style="text-align: center;vertical-align: middle;">Supplier</th>
                            <th style="text-align: center;vertical-align: middle;">Account</th>
                            <th style="text-align: center;vertical-align: middle;">Amount</th>
                            <th style="text-align: center;vertical-align: middle;">Status</th>
                            <th style="text-align: center;vertical-align: middle;width: 220px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
              </div><!-- /.ftl-tblwrap -->
            </div><!-- /.app-loading-wrap -->
        </div><!-- /.ftl-body -->
    </div><!-- /.ftl-card: kartu tabel -->
</div><!-- /.container-fluid -->

<!-- Modal Detail -->
<div class="modal fade ftl-modal" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                    <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

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
    $(function() {
        $('.selectpicker').selectpicker();
    });

    $(document).ready(function() {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
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
        // kembali dari halaman edit mendarat di tampilan yang sama.
        // stateDuration -1 = sessionStorage (bukan localStorage), seumur
        // tab. Sama spt List Memorial Journal.
        stateSave: true,
        stateDuration: -1,

        /* RESPONSIVE: kolom yang tidak muat DIKOLAPS jadi baris rincian
           yang dibuka lewat tanda "+", bukan dipaksa digulir ke samping -
           itu yang bikin tabel tak terbaca di HP. Modulnya sudah terbundel
           di datatables.min.js dan CSS-nya sudah dimuat header.php
           (responsive.bootstrap4.min.css). */
        responsive: {
            details: { type: 'column', target: 0 }
        },

        ajax: {
            url: 'ajx_petty_cashout.php',
            type: 'POST',
            data: function(d) {
                d.reference = $('#reference').val();
                d.doc_num = $('#doc_num').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            // Tanpa ini overlay-nya menggantung terus kalau permintaannya
            // gagal, dan user tidak tahu apa yang terjadi.
            error: function(xhr) {
                $('#pcoLoad').removeClass('is-loading');
                Swal.fire({
                    icon: 'error', title: 'Failed to load the list',
                    text: 'HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response')
                });
            }
        },

        /* responsivePriority: makin KECIL angkanya, makin lama kolom itu
           dipertahankan saat layar menyempit. Urutannya diambil dari cara
           orang membaca daftar ini: nomor dokumen & tombol aksi harus
           selalu ada, lalu Status, lalu Amount, baru sisanya. */
        columns: [
            { data: null, defaultContent: '', orderable: false, className: 'dtr-control', responsivePriority: 1 },
            { data: 'no_pco',    responsivePriority: 1 },
            { data: 'tgl_pco',   responsivePriority: 6 },
            { data: 'reff',      responsivePriority: 5 },
            { data: 'nama_supp', responsivePriority: 7 },
            { data: 'nama_coa',  responsivePriority: 8 },
            { data: 'amount',    responsivePriority: 4 },
            { data: 'status',    responsivePriority: 3 },
            { data: 'action', orderable: false, searchable: false, responsivePriority: 2 },
        ],

        /* Kelas "all" milik Responsive = kolom ini TIDAK PERNAH dikolaps,
           seberapa pun sempit layarnya. responsivePriority saja tidak cukup:
           itu hanya menentukan urutan dibuang. Tiga yang dikunci: nomor
           dokumen, Status, dan Action.
           Nomor target bergeser +1 karena kolom "+" disisipkan di depan. */
        columnDefs: [
            { targets: [1],    className: 'text-left ftl-doc all' },
            { targets: [7],    className: 'text-center all' },
            { targets: [8],    className: 'text-center ftl-act-cell all' },
            { targets: [4, 5], className: 'text-left' },
            { targets: [6],    className: 'text-right ftl-amt' },
            { targets: [2, 3], className: 'text-center' },
        ],

        language: {
            emptyTable: 'No Petty Cash Out found for this filter.',
            zeroRecords: 'No Petty Cash Out matches your search.'
        }
    });

    /* Overlay mengikuti status processing DataTables. Kotak "Processing"
       bawaannya otomatis disembunyikan app-loading.css, jadi tidak muncul
       dua-duanya. */
    datatable.on('processing.dt', function(e, settings, processing) {
        $('#pcoLoad').toggleClass('is-loading', processing);
    });

    function dataTableReload() {
        datatable.ajax.reload();
    }

    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        dataTableReload();
    });

    <?php if ($id == '39') { ?>
    document.getElementById('btncreate_new').onclick = function() {
        location.href = "create-pettycash-out.php";
    };
    <?php } ?>

    document.getElementById('btnExportExcel').onclick = function() {

        Swal.fire({
            title: 'Menyiapkan Excel...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        window.location.href = 'ekspor_petty_cash_out.php?reference=' + encodeURIComponent($('#reference').val())
            + '&doc_num=' + encodeURIComponent($('#doc_num').val())
            + '&start_date=' + encodeURIComponent($('#start_date').val())
            + '&end_date=' + encodeURIComponent($('#end_date').val());

        setTimeout(function() {
            Swal.close();
        }, 1500);
    };
</script>

<script type="text/javascript">
    /* Nilai dari server ditulis lewat .html(), jadi harus dilolosi dulu -
       nama supplier atau deskripsi yang memuat < atau & tidak boleh
       merusak susunan modalnya. */
    function lolos(v) {
        return $('<div>').text(v === null || v === undefined ? '-' : v).html();
    }

    /* Satu keterangan ringkas = label kecil di atas, nilainya di bawah.
       Dulu ditulis satu kalimat ("Supplier : X") sehingga nilainya tidak
       pernah sejajar antar kolom karena panjang labelnya berbeda-beda. */
    function isiRingkas(id, label, nilai) {
        $(id).html('<span class="ftl-k">' + label + '</span>'
                 + '<span class="ftl-v">' + lolos(nilai) + '</span>');
    }

    /* Satu pintu untuk modal rincian - dipakai tombol Show DAN klik baris,
       jadi tidak ada dua potongan kode yang harus dijaga agar seragam. */
    function bukaRincianPco(data) {
        if (!data) return;

        $('#mymodal').modal('show');
        $('#details').html('');

        $.ajax({
            type: 'post',
            url: 'ajaxpettyout.php',
            data: { 'no_ob': data.no_pco, 'refdoc': data.reff },
            success: function(html) {
                $('#details').html(html);
            }
        });

        $('#txt_bpb').html(lolos(data.no_pco));
        isiRingkas('#txt_tglbpb', 'Date',        data.tgl_pco);
        isiRingkas('#txt_no_po',  'Supplier',    data.nama_supp);
        isiRingkas('#txt_supp',   'Reference',   data.reff);
        isiRingkas('#txt_top',    'Kas Account', data.nama_coa);
        isiRingkas('#txt_tgl_po', 'Description', data.deskripsi);
        isiRingkas('#txt_status', 'Status',      data.status_raw);
    }

    $(document).on("click", ".btn-show-pco", function() {
        bukaRincianPco(datatable.row($(this).closest('tr')).data());
    });

    /* Klik di mana saja pada baris ikut membuka modal - sama spt daftar FTR
       dan Memorial Journal.

       PENJAGA: memblok teks untuk DISALIN tidak boleh ikut membuka modal.
       Dibedakan dari jarak geser tetikus antara mousedown & klik, dan dari
       ada-tidaknya teks yang tersorot. */
    var tekanX = 0, tekanY = 0;
    $('#mytable tbody').on('mousedown', 'tr', function(e) {
        tekanX = e.clientX; tekanY = e.clientY;
    });
    $('#mytable tbody').on('click', 'tr', function(e) {
        if (Math.abs(e.clientX - tekanX) > 4 || Math.abs(e.clientY - tekanY) > 4) { return; }
        var tersorot = window.getSelection ? String(window.getSelection()) : '';
        if (tersorot.length > 0) { return; }
        // Tanda "+", sel tombol, dan baris rincian punya tugasnya sendiri.
        if ($(e.target).closest('td.dtr-control').length) { return; }
        if ($(e.target).closest('.ftl-act-cell').length) { return; }
        if ($(this).hasClass('child')) { return; }
        bukaRincianPco(datatable.row(this).data());
    });
</script>

<script type="text/javascript">
    $(document).on("click", ".edit-none", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_out_none.php?doc_num=" + encodedDocNum;
            }
        });
    });

    $(document).on("click", ".edit-settle", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_out_settle.php?doc_num=" + encodedDocNum;
            }
        });
    });

    $(document).on("click", ".edit-lp", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_out_lp.php?doc_num=" + encodedDocNum;
            }
        });
    });

    $(document).on("click", ".edit-ftr", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_out_ftr.php?doc_num=" + encodedDocNum;
            }
        });
    });

    $(document).on("click", ".edit-pv", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num);

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_out_pv.php?doc_num=" + encodedDocNum;
            }
        });
    });

    $(document).on("click", ".cancel-pco", function() {
        let doc_num = $(this).data("pettycash");
        let cancel_user = '<?php echo $user; ?>';

        Swal.fire({
            title: "Are you sure cancel " + doc_num + "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes, cancel it!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                type: 'POST',
                url: 'cancelpetcashout.php',
                data: { no_pci: doc_num, cancel_user: cancel_user },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message
                        }).then(() => {
                            dataTableReload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.log("ERROR AJAX:", xhr.responseText);
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        });
    });
</script>

</body>

</html>
