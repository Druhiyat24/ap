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
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-exchange" aria-hidden="true"></i> TRANSFER LIST</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data">
                <div class="row g-3">

                    <div class="col-md-2">
                        <label for="filter" class="form-label"><b>Status</b></label>
                        <select class="form-control form-control-sm" id="filter">
                            <option value="ALL" selected>ALL</option>
                            <option value="Draft">Draft</option>
                            <option value="Cancel">Cancel</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-md-6 d-flex align-items-end mt-2">
                        <button type="submit" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <?php
                        $querysCreate = mysqli_query($conn2, "select useraccess.menu as menu, useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Transfer List'");
                        $rsCreate = mysqli_fetch_array($querysCreate);
                        $idCreate = isset($rsCreate['id']) ? $rsCreate['id'] : 0;

                        if ($idCreate == '131') {
                            echo '<button type="button" id="btncreate" class="btn btn-primary btn-sm ml-2">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i> Create New
                            </button>';
                        }
                        ?>
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
                            <th style="text-align: center;vertical-align: middle;">No Transfer List</th>
                            <th style="text-align: center;vertical-align: middle;">TL Date</th>
                            <th style="text-align: center;vertical-align: middle;">Tipe Mutasi</th>
                            <th style="text-align: center;vertical-align: middle;">Jenis Otorisasi</th>
                            <th style="text-align: center;vertical-align: middle;">Tanggal Efektif</th>
                            <th style="text-align: center;vertical-align: middle;">Jenis Biaya</th>
                            <th style="text-align: center;vertical-align: middle;">Jml PL</th>
                            <th style="text-align: center;vertical-align: middle;">Status</th>
                            <th style="text-align: center;vertical-align: middle;">Created By</th>
                            <th style="text-align: center;vertical-align: middle;width: 190px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modal-show" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_tl"></h4>
            </div>
            <div class="container">
                <div class="row">
                    <div id="txt_tl_date" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_tl_param" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;">
                        <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                            <thead>
                                <tr>
                                    <th style="width:16%;">No Payment List</th>
                                    <th style="width:12%;">PL Date</th>
                                    <th style="width:25%;">Keterangan</th>
                                    <th style="width:12%;">Status</th>
                                    <th style="width:15%;">Rekening Asal</th>
                                    <th style="width:8%;">Curr</th>
                                    <th style="width:12%;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="datatable_modal"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
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
            url: 'ajx_transfer-list.php',
            type: 'POST',
            data: function(d) {
                d.status     = $('#filter').val();
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
            }
        },

        columns: [
            { data: 'tl_number' },
            { data: 'tl_date' },
            { data: 'tipe_mutasi' },
            { data: 'jenis_otorisasi' },
            { data: 'tanggal_efektif' },
            { data: 'jenis_biaya' },
            { data: 'jml_pl' },
            { data: 'status' },
            { data: 'created_by' },
            { data: 'action', orderable: false },
        ],

        columnDefs: [
            { targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9], className: 'text-center' },
        ],
    });

    function dataTableReload() {
        datatable.ajax.reload();
    }

    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        dataTableReload();
    });
</script>

<script type="text/javascript">
    const btnCreate = document.getElementById('btncreate');
    if (btnCreate) {
        btnCreate.onclick = function() {
            location.href = "create-transfer-list.php";
        };
    }
</script>

<script type="text/javascript">
    $("#mytable").on("click", ".btn-show-tl", function() {
        const data = datatable.row($(this).closest('tr')).data();
        if (!data) return;

        var tl_number = data.tl_number;

        $.ajax({
            type: 'POST',
            url: 'get_data_transfer_list_det.php',
            data: { 'tl_number': tl_number },
            cache: false,
            success: function(html) {
                $('#datatable_modal').html(html);
            }
        });

        $('#txt_tl').html(tl_number);
        $('#txt_tl_date').html('<b>TL Date : </b>' + data.tl_date);
        $('#txt_tl_param').html(
            '<b>Tipe Mutasi : </b>' + data.tipe_mutasi +
            ' &nbsp;|&nbsp; <b>Jenis Otorisasi : </b>' + data.jenis_otorisasi +
            ' &nbsp;|&nbsp; <b>Tanggal Efektif : </b>' + data.tanggal_efektif +
            ' &nbsp;|&nbsp; <b>Jenis Biaya : </b>' + data.jenis_biaya
        );
        $('#modal-show').modal('show');
    });
</script>

<script type="text/javascript">
    $(document).on("click", ".btn-cancel-tl", function() {
        var tl_number = $(this).data("tl");

        Swal.fire({
            title: "Are you sure?",
            text: "Transfer List ini akan dibatalkan, dan Payment List di dalamnya akan tersedia lagi untuk Transfer List baru.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, cancel it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "cancel_transfer_list.php",
                    data: { tl_number: tl_number },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status === 'ok') {
                            Swal.fire({
                                icon: "success",
                                title: "Cancelled",
                                text: res.message
                            }).then(() => {
                                dataTableReload();
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: "error", title: "Error", text: "Failed to cancel Transfer List!" });
                    }
                });
            }
        });
    });
</script>

</body>

</html>
