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

    .kbon-status-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        white-space: nowrap;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-money" aria-hidden="true"></i> LIST PETTY CASH OUT</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data" action="petty-cashout.php" method="post">
                <div class="row g-3">

                    <div class="col-md-3">
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

                    <div class="col-md-2">
                        <label for="doc_num" class="form-label"><b>Document Number</b></label>
                        <input type="text" class="form-control form-control-sm" name="doc_num" id="doc_num" autocomplete="off"
                            value="<?php echo isset($_POST['doc_num']) ? htmlspecialchars($_POST['doc_num']) : ''; ?>">
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" autocomplete="off"
                            value="<?php echo !empty($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : date('d-m-Y'); ?>" placeholder="Start Date">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" autocomplete="off"
                            value="<?php echo !empty($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : date('d-m-Y'); ?>" placeholder="End Date">
                    </div>

                    <div class="col-md-3 d-flex align-items-end mt-2">
                        <button type="submit" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        
                        <?php
                        $querys = mysqli_query($conn2, "select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Cash'");
                        $rs = mysqli_fetch_array($querys);
                        $id = isset($rs['id']) ? $rs['id'] : 0;
                        if ($id == '39') {
                            echo '<button id="btncreate_new" type="button" class="btn btn-primary btn-sm ml-2"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create New</button>';
                        }
                        ?>
                        <button type="button" id="btnExportExcel" class="btn btn-success btn-sm ml-2">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
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

        ajax: {
            url: 'ajx_petty_cashout.php',
            type: 'POST',
            data: function(d) {
                d.reference = $('#reference').val();
                d.doc_num = $('#doc_num').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },

        columns: [
            { data: 'no_pco' },
            { data: 'tgl_pco' },
            { data: 'reff' },
            { data: 'nama_supp' },
            { data: 'nama_coa' },
            { data: 'amount' },
            { data: 'status' },
            { data: 'action', orderable: false },
        ],

        columnDefs: [
            { targets: [3, 4], className: 'text-left' },
            { targets: [5], className: 'text-right' },
            { targets: [0, 1, 2, 6, 7], className: 'text-center' },
        ],
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
    $(document).on("click", ".btn-show-pco", function() {
        const data = datatable.row($(this).closest('tr')).data();
        if (!data) return;

        $('#mymodal').modal('show');

        $.ajax({
            type: 'post',
            url: 'ajaxpettyout.php',
            data: { 'no_ob': data.no_pco, 'refdoc': data.reff },
            success: function(html) {
                $('#details').html(html);
            }
        });

        $('#txt_bpb').html(data.no_pco);
        $('#txt_tglbpb').html('Date : ' + data.tgl_pco_raw);
        $('#txt_no_po').html('Supplier : ' + data.nama_supp);
        $('#txt_supp').html('Refference : ' + data.reff);
        $('#txt_top').html('Kas Account : ' + data.nama_coa);
        $('#txt_tgl_po').html('Description : ' + data.deskripsi);
        $('#txt_status').html('Status : ' + data.status_raw);
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
