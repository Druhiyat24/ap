<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;
    }

    input {
        font-size: 14px;
    }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    div.dataTables_wrapper .dataTables_paginate {
        float: right;
        margin-top: 10px;
    }

    div.dataTables_wrapper .dataTables_info {
        float: left;
        margin-top: 10px;
    }

    .kbon-action-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 5px;
    }

    .kbon-action-buttons .btn {
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 11px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .kbon-action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    }

    .modal {
        text-align: center;
        padding: 0 !important;
    }

    .modal:before {
        content: '';
        display: inline-block;
        height: 100%;
        vertical-align: middle;
        margin-right: -4px;
    }

    .modal-dialog {
        display: inline-table;
        width: 700px;
        text-align: left;
        vertical-align: middle;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-book" aria-hidden="true"></i> LIST MEMORIAL JOURNAL</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="nama_type" class="form-label"><b>Type</b></label>
                        <select class="form-control selectpicker" name="nama_type" id="nama_type" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "select id_cmj, nama_cmj from master_category_mj order by id_cmj");
                            while ($row = mysqli_fetch_array($sql)) {
                                echo '<option value="' . htmlspecialchars($row['id_cmj']) . '">' . htmlspecialchars($row['nama_cmj']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label"><b>Status</b></label>
                        <select class="form-control selectpicker" name="status" id="status" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <option value="Draft">Draft</option>
                            <option value="Post">Post</option>
                            <option value="Cancel">Cancel</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?php echo date('d-m-Y'); ?>" placeholder="Tanggal Awal" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?php echo date('d-m-Y'); ?>" placeholder="Tanggal Akhir" autocomplete="off">
                    </div>

                    <div class="col-md-3 d-flex align-items-end mt-2">
                        <button type="submit" class="btn btn-info btn-sm mr-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <?php
                        $querys = mysqli_query($conn2, "select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Acct - Create Memorial Journal'");
                        $rs = mysqli_fetch_array($querys);
                        $id = isset($rs['id']) ? $rs['id'] : 0;
                        if ($id == '54') {
                            echo '<button id="btncreate_new" type="button" class="btn btn-primary btn-sm mr-2"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create</button>';
                        }
                        ?>
                        <button id="btnverifikasi" type="button" class="btn btn-warning btn-sm mr-2">
                            <i class="fa fa-paper-plane" aria-hidden="true"></i> Verifikasi
                        </button>
                        <button id="btnsync_fx" type="button" class="btn btn-danger btn-sm">
                            <i class="fa fa-refresh" aria-hidden="true"></i> Selisih Kurs
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Table -->
    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="mytable" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
                    <thead class="table-gradient">
                        <tr>
                            <th style="text-align: center;vertical-align: middle;width: 13%;">No Journal</th>
                            <th style="text-align: center;vertical-align: middle;width: 8%;">Date</th>
                            <th style="text-align: center;vertical-align: middle;width: 11%;">Type</th>
                            <th style="text-align: center;vertical-align: middle;width: 5%;">Curr</th>
                            <th style="text-align: center;vertical-align: middle;width: 9%;">Debit</th>
                            <th style="text-align: center;vertical-align: middle;width: 9%;">Credit</th>
                            <th style="text-align: center;vertical-align: middle;width: 6%;">Status</th>
                            <th style="text-align: center;vertical-align: middle;width: 29%;">Description</th>
                            <th style="text-align: center;vertical-align: middle;width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
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
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
    $('#body-row .collapse').collapse('hide');
    $('#collapse-icon').addClass('fa-angle-double-left');
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

<script type="text/javascript">
    $(document).ready(function() {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
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

        ajax: {
            url: 'ajx_memorial-journal.php',
            type: 'POST',
            data: function(d) {
                d.nama_type  = $('#nama_type').val();
                d.status     = $('#status').val();
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
                d.user       = '<?php echo $user; ?>';
            },
            dataSrc: 'data'
        },

        columns: [
            { data: 'no_mj' },
            { data: 'mj_date' },
            { data: 'nama_cmj' },
            { data: 'curr' },
            { data: 'debit' },
            { data: 'credit' },
            { data: 'status' },
            { data: 'keterangan' },
            { data: 'action', orderable: false },
        ],

        columnDefs: [
            { targets: [0, 1, 2, 3, 7], className: 'text-left' },
            { targets: [4, 5], className: 'text-right' },
            { targets: [6, 8], className: 'text-center' },
        ],
    });

    function dataTableReload() {
        datatable.ajax.reload(null, false);
    }

    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        dataTableReload();
    });

    document.getElementById('btnverifikasi').onclick = function() {
        location.href = "formverifikasimj.php";
    };

    <?php if ($id == '54') { ?>
    document.getElementById('btncreate_new').onclick = function() {
        location.href = "create_memorial_journal.php";
    };
    <?php } ?>
</script>

<script type="text/javascript">
    $(document).on("click", ".btn-approve-mj", function() {
        let no_mj = $(this).data("no");
        let post_user = '<?php echo $user; ?>';

        Swal.fire({
            title: "Post journal ini?",
            text: no_mj + " akan diposting.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Post",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'post_memorialjournal.php',
                    data: { no_mj: no_mj, post_user: post_user },
                    success: function() {
                        Swal.fire('Berhasil!', 'Memorial Journal berhasil diposting.', 'success');
                        dataTableReload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal menghubungi server: ' + xhr.status, 'error');
                    }
                });
            }
        });
    });

    $(document).on("click", ".btn-cancel-mj", function() {
        let no_mj = $(this).data("no");
        let cancel_user = '<?php echo $user; ?>';

        Swal.fire({
            title: "Yakin ingin cancel journal ini?",
            text: no_mj + " akan dibatalkan dan tidak bisa diedit lagi.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Cancel",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'cancel_memorialjournal.php',
                    data: { no_mj: no_mj, cancel_user: cancel_user },
                    success: function() {
                        Swal.fire('Dibatalkan!', 'Memorial Journal berhasil dibatalkan.', 'success');
                        dataTableReload();
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal menghubungi server: ' + xhr.status, 'error');
                    }
                });
            }
        });
    });
</script>

<script type="text/javascript">
    document.getElementById('btnsync_fx').onclick = function() {
        Swal.fire({
            title: "Sync Jurnal Selisih Kurs?",
            text: "Ini akan menghitung ulang dan memposting (insert/update) jurnal selisih kurs untuk semua akun USD s.d. hari ini. Lanjutkan?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Sync",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: "Memproses...",
                text: "Mohon tunggu, sedang sync jurnal selisih kurs.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                type: 'POST',
                url: 'ajx_sync_selisih_kurs.php',
                data: { user: '<?php echo $user; ?>' },
                dataType: 'json',
                success: function(res) {
                    if (!res.ok) {
                        Swal.fire('Sebagian Gagal', (res.errors || []).join('\n') || 'Terjadi kesalahan.', 'error');
                        dataTableReload();
                        return;
                    }

                    var detail = (res.per_account || []).map(function(a) {
                        return a.account + ': ' + a.count + ' jurnal';
                    }).join('<br>');

                    Swal.fire({
                        icon: 'success',
                        title: 'Sync Selesai',
                        html: 'Sampai dengan <b>' + res.end_date + '</b><br>' +
                            'Total <b>' + res.total_jurnal + '</b> jurnal diproses ' +
                            '(' + res.total_insert + ' baru, ' + res.total_update + ' update)' +
                            (detail ? '<br><br>' + detail : '')
                    });
                    dataTableReload();
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal menghubungi server: ' + xhr.status, 'error');
                }
            });
        });
    };
</script>

<script type="text/javascript">
    $("#mytable").on("click", "tbody tr", function(e) {
        if ($(e.target).closest('button, a').length) return;

        const data = datatable.row(this).data();
        if (!data) return;

        $('#mymodal').modal('show');

        $.ajax({
            type: 'post',
            url: 'ajax_mj.php',
            data: { 'no_mj': data.no_mj },
            success: function(html) {
                $('#details').html(html);
            }
        });

        $('#txt_bpb').html(data.no_mj);
        $('#txt_tglbpb').html('Date : ' + data.mj_date);
        $('#txt_no_po').html('Type : ' + data.nama_cmj);
        $('#txt_supp').html('Status : ' + data.status);
    });
</script>

</body>

</html>
