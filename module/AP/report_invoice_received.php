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

    .ir-legend {
        font-size: 12.5px;
    }

    .ir-legend ul {
        padding-left: 18px;
        margin-bottom: 0;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-file-text-o" aria-hidden="true"></i> INVOICE RECEIVED REPORT</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="nama_supp" class="form-label"><b>Supplier</b></label>
                        <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-live-search="true" data-size="5">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            $sql = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['Supplier'];
                                echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
                            } ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label"><b>From</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label"><b>To</b></label>
                        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date" value="<?php echo date('d-m-Y'); ?>" autocomplete="off">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-info btn-sm me-2">
                            <i class="fa fa-search" aria-hidden="true"></i> Search
                        </button>
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
                <table id="datatable" class="table table-striped table-bordered table-hover table-sm text-nowrap" style="width:100%">
                    <thead class="table-gradient">
                        <tr>
                            <th>No</th>
                            <th>Supplier Invoice No</th>
                            <th>No Kontrabon</th>
                            <th>No Reff</th>
                            <th>Supplier Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>SI Date</th>
                            <th>IR Date</th>
                            <th>BPB Date</th>
                            <th>BPB Create</th>
                            <th>BPB-A Date</th>
                            <th>BPB-R Date</th>
                            <th>FTA Date</th>
                            <th>ARF Date</th>
                            <th>ATP Date</th>
                            <th>PRA Date</th>
                            <th>PTF Date</th>
                            <th>FRP Date</th>
                            <th>KB Date</th>
                            <th>PVL Date</th>
                            <th>PVL Approve Date</th>
                            <th>PL Date</th>
                            <th>PL Approve Date</th>
                            <th>PAY Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="row ml-1 mt-3 ir-legend">
                <div class="card col-md-3 p-2">
                    <ul>
                        <li>SI : Supplier Invoice</li>
                        <li>IR : Supplier Invoice Received</li>
                        <li>FTA : Finance To Accounting</li>
                        <li>ARF : Accounting Receive From Finance</li>
                    </ul>
                </div>
                <div class="card col-md-3 p-2">
                    <ul>
                        <li>ATP : Accounting To Purchasing</li>
                        <li>PRA : Purchasing Received From Accounting</li>
                        <li>PTF : Purchasing To Finance</li>
                        <li>FRP : Finance Received From Purchasing</li>
                    </ul>
                </div>
                <div class="card col-md-3 p-2">
                    <ul>
                        <li>BPB : Good Received In Warehouse</li>
                        <li>BPB-A : Good Received Approved</li>
                        <li>BPB-R : Good Received To Accounting</li>
                    </ul>
                </div>
                <div class="card col-md-3 p-2">
                    <ul>
                        <li>KB : Kontrabon</li>
                        <li>PVL : Payment Voucher List</li>
                        <li>PL : Payment List (First &amp; Second Approve)</li>
                        <li>PAY : Bank Out / Payment Date</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mymodalkbon" data-target="#mymodalkbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_kbon"></h4>
            </div>
            <div class="container">
                <div class="row">
                    <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                    <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script>
    // Hide submenus
    $('#body-row .collapse').collapse('hide');

    // Collapse/Expand icon
    $('#collapse-icon').addClass('fa-angle-double-left');

    // Collapse click
    $('[data-toggle=sidebar-colapse]').click(function () {
        SidebarCollapse();
    });

    function SidebarCollapse() {
        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

        // Treating d-flex/d-none on separators with title
        var SeparatorTitle = $('.sidebar-separator-title');
        if (SeparatorTitle.hasClass('d-flex')) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        // Collapse/Expand icon
        $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
    });
</script>

<script>
    $(function () {
        $('.selectpicker').selectpicker();
    });
</script>

<script>
    $(document).ready(function () {
        window.irTable = $('#datatable').DataTable({
            ordering: false,
            processing: true,
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            ajax: {
                url: 'ajx_report_invoice_received.php',
                type: 'POST',
                data: function (d) {
                    d.nama_supp = $('#nama_supp').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'no' },
                { data: 'no_invoice' },
                { data: 'doc_number' },
                { data: 'no_reff' },
                { data: 'nama_supp' },
                { data: 'amount' },
                { data: 'status' },
                { data: 'tgl_invoice' },
                { data: 'tgl_penerimaan' },
                { data: 'bpbdate' },
                { data: 'dateinput' },
                { data: 'confirm_date' },
                { data: 'trf_date' },
                { data: 'tfta_date' },
                { data: 'receive_acc_date' },
                { data: 'tatp_date' },
                { data: 'receive_pch_date' },
                { data: 'tptf_date' },
                { data: 'receive_fin_date' },
                { data: 'tgl_kbon' },
                { data: 'pvl_date' },
                { data: 'pvl_approve_date' },
                { data: 'tgl_payment' },
                { data: 'tgl_approve_lp' },
                { data: 'bankout_date' }
            ]
        });

        $('#form-data').on('submit', function (e) {
            e.preventDefault();
            window.irTable.ajax.reload();
        });

        $('#btnExportExcel').on('click', function () {
            window.location.href = 'ekspor_ir.php?nama_supp=' + encodeURIComponent($('#nama_supp').val())
                + '&start_date=' + encodeURIComponent($('#start_date').val())
                + '&end_date=' + encodeURIComponent($('#end_date').val());
        });

        $('#datatable tbody').on('click', 'tr', function () {
            var no_inv = $(this).find('td:eq(1)').text();
            var supp = $(this).find('td:eq(3)').text();

            $('#mymodalkbon').modal('show');

            $.ajax({
                type: 'post',
                url: 'ajax_reportinv.php',
                data: { 'no_inv': no_inv, 'supp': supp },
                success: function (data) {
                    $('#details').html(data);
                }
            });

            $('#txt_kbon').html(no_inv);
            $('#txt_nama_supp').html('Supplier : ' + supp + '');
        });
    });
</script>

<script>
    function alert_cancel() {
        alert("Data Berhasil di Cancel");
        location.reload();
    }
    function alert_approve() {
        alert("Data Berhasil di Approve");
        location.reload();
    }
</script>
