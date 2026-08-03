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

    .total-box {
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        padding: 15px;
        background: #fafafa;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-university" aria-hidden="true"></i> INCOMING BANK</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="nama_supp" class="form-label"><b>Source</b></label>
                        <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <option value="Unrealize">Unrealize</option>
                            <?php
                            $sql = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['Supplier'];
                                echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="bank" class="form-label"><b>Bank</b></label>
                        <select class="form-control selectpicker" name="bank" id="bank" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "select distinct(Upper(bank_name)) as nama_bank from b_masterbank order by bank_name ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['nama_bank'];
                                echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="akun" class="form-label"><b>Account</b></label>
                        <select class="form-control selectpicker" name="akun" id="akun" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "select distinct(bank_account) as no_rek from b_masterbank order by no_rek ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['no_rek'];
                                echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label"><b>Status</b></label>
                        <select class="form-control selectpicker" name="status" id="status" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <option value="Draft">Draft</option>
                            <option value="Approved">Approved</option>
                            <option value="Cancel">Cancel</option>
                        </select>
                    </div>

                    <div class="col-md-3"></div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-md-8 d-flex align-items-end mt-2">
                        <button type="submit" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
                        <?php
                        $querys = mysqli_query($conn2, "select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Bank'");
                        $rs = mysqli_fetch_array($querys);
                        $id = isset($rs['id']) ? $rs['id'] : 0;
                        if ($id == '37') {
                            echo '<button id="btncreate_new" type="button" class="btn btn-primary btn-sm ml-2"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create</button>';
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
                            <th style="text-align: center;vertical-align: middle;">No Bank In</th>
                            <th style="text-align: center;vertical-align: middle;">Source</th>
                            <th style="text-align: center;vertical-align: middle;">Date</th>
                            <th style="text-align: center;vertical-align: middle;">Currency</th>
                            <th style="text-align: center;vertical-align: middle;">Amount</th>
                            <th style="text-align: center;vertical-align: middle;">Outstanding</th>
                            <th style="text-align: center;vertical-align: middle;">Status</th>
                            <th style="text-align: center;vertical-align: middle;">Approve Date</th>
                            <th style="text-align: center;vertical-align: middle;width: 190px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mt-2" style="font-size:13px">
                <b>Total Amount:</b> <span id="total_amount">0.00</span>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <b>Total Outstanding:</b> <span id="total_outstanding">0.00</span>
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
                    <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Customer -->
<div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="Heading">Update Customer</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form id="modal-form2" method="post">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="txt_pv"><b>No Incoming Bank</b></label>
                                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_pv" id="txt_pv" value="">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="txt_cstr"><b>Customer</b></label>
                                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_cstr" id="txt_cstr" value="">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="supp_update"><b>New Customer</b></label>
                                <select class="form-control selectpicker" name="supp_update" id="supp_update" data-live-search="true" data-size="5">
                                    <?php
                                    $sql = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC");
                                    while ($row = mysqli_fetch_array($sql)) {
                                        $data = $row['Supplier'];
                                        echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="modal-footer">
                                    <button type="submit" id="send2" name="send2" class="btn btn-success" style="width: 100%;"><span class="fa fa-check"></span> Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
            url: 'ajx_bank-in.php',
            type: 'POST',
            data: function(d) {
                d.nama_supp  = $('#nama_supp').val();
                d.bank       = $('#bank').val();
                d.akun       = $('#akun').val();
                d.status     = $('#status').val();
                d.start_date = $('#start_date').val();
                d.end_date   = $('#end_date').val();
            },
            dataSrc: function(json) {
                $('#total_amount').text(json.footer.amount);
                $('#total_outstanding').text(json.footer.outstanding);
                return json.data;
            }
        },

        columns: [
            { data: 'doc_num' },
            { data: 'customer' },
            { data: 'date' },
            { data: 'curr' },
            { data: 'amount' },
            { data: 'outstanding' },
            { data: 'status' },
            { data: 'approve_date' },
            { data: 'action', orderable: false },
        ],

        columnDefs: [
            { targets: [1], className: 'text-left' },
            { targets: [4, 5], className: 'text-right' },
            { targets: [0, 2, 3, 6, 7, 8], className: 'text-center' },
        ],
    });

    function dataTableReload() {
        datatable.ajax.reload();
    }

    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        dataTableReload();
    });

    <?php if ($id == '37') { ?>
    document.getElementById('btncreate_new').onclick = function() {
        location.href = "create-in-bank.php";
    };
    <?php } ?>

    document.getElementById('btnExportExcel').onclick = function() {
        window.location.href = 'ekspor_bank_in.php?nama_supp=' + encodeURIComponent($('#nama_supp').val())
            + '&bank=' + encodeURIComponent($('#bank').val())
            + '&akun=' + encodeURIComponent($('#akun').val())
            + '&status=' + encodeURIComponent($('#status').val())
            + '&start_date=' + encodeURIComponent($('#start_date').val())
            + '&end_date=' + encodeURIComponent($('#end_date').val());
    };
</script>

<script type="text/javascript">
    function editWithClosingCheck(url, closing) {
        if (closing === 'Open') {
            Swal.fire({
                title: "Are you sure?",
                text: "You are about to edit this document.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, edit it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Sorry!",
                text: "The Bank period has already been closed.",
                confirmButtonText: "OK"
            });
        }
    }

    $(document).on("click", ".edit-ar-collection", function() {
        let doc_num = $(this).data("bank");
        let closing = $(this).data("closing");
        editWithClosingCheck("form_edit_bank_in.php?doc_num=" + btoa(doc_num), closing);
    });

    $(document).on("click", ".edit-none", function() {
        let doc_num = $(this).data("bank");
        let closing = $(this).data("closing");
        editWithClosingCheck("form_edit_bank_in_none.php?doc_num=" + btoa(doc_num), closing);
    });

    $(document).on("click", ".edit-bk", function() {
        let doc_num = $(this).data("bank");
        let closing = $(this).data("closing");
        editWithClosingCheck("form_edit_bank_in_bk.php?doc_num=" + btoa(doc_num), closing);
    });

    $(document).on("click", ".btn-cancel-bankin", function() {
        let doc_num = $(this).data("bank");
        let cancel_user = '<?php echo $user; ?>';

        Swal.fire({
            title: "Yakin ingin cancel dokumen ini?",
            text: doc_num + " akan dibatalkan dan tidak bisa diedit lagi.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Cancel",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'cancel_inbank.php',
                    data: { no_bi: doc_num, cancel_user: cancel_user },
                    success: function() {
                        Swal.fire('Dibatalkan!', 'Dokumen berhasil dibatalkan.', 'success');
                        datatable.ajax.reload(null, false);
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
    $("#mytable").on("click", "tbody tr", function(e) {
        if ($(e.target).closest('button, a').length) return;

        const data = datatable.row(this).data();
        if (!data) return;

        $('#mymodal').modal('show');

        $.ajax({
            type: 'post',
            url: 'ajaxbankin.php',
            data: { 'no_ib': data.doc_num, 'refdoc': data.ref_data },
            success: function(html) {
                $('#details').html(html);
            }
        });

        $('#txt_bpb').html(data.doc_num);
        $('#txt_tglbpb').html('Customer : ' + data.customer);
        $('#txt_no_po').html('Reff Doc : ' + data.ref_data);
        $('#txt_supp').html('Account : ' + data.akun);
        $('#txt_top').html('Bank : ' + data.bank);
        $('#txt_curr').html('Currency : ' + data.curr);
        $('#txt_confirm').html('Status : ' + data.status);
        $('#txt_tgl_po').html('Description : ' + (data.deskripsi || '-'));
    });
</script>

<script type="text/javascript">
    $("#modal-form2").on("submit", function(e) {
        e.preventDefault();

        var no_bi = document.getElementById('txt_pv').value;
        var customer = $('select[name=supp_update] option').filter(':selected').val();
        var update_user = '<?php echo $user; ?>';

        $.ajax({
            type: 'POST',
            url: 'update_cus.php',
            data: { 'no_bi': no_bi, 'customer': customer, 'update_user': update_user },
            cache: false,
            success: function(response) {
                console.log(response);
                $('#mymodal2').modal('hide');
                dataTableReload();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire('Error', 'Gagal update customer', 'error');
            }
        });
    });
</script>

</body>

</html>
