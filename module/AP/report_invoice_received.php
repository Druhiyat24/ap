<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;
    }

    input {
        font-size: 14px;
    }

    #datatable.ir-modern thead.table-gradient th {
        background: linear-gradient(180deg, #2f6fea, #1E3A8A);
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        border-color: #1c3577;
        font-size: 12px;
        letter-spacing: .2px;
        padding: 10px 10px;
    }

    #datatable.ir-modern tbody td {
        font-size: 12.5px;
        vertical-align: middle;
        padding: 7px 10px;
        transition: background-color .12s ease;
    }

    #datatable.ir-modern tbody tr:hover td {
        background-color: #eef4ff;
    }

    #datatable.ir-modern td.ir-clickable {
        cursor: pointer;
        color: #1E3A8A;
        font-weight: 600;
        position: relative;
    }

    #datatable.ir-modern td.ir-clickable:hover {
        background-color: #dbe7ff !important;
        text-decoration: underline;
    }

    #datatable.ir-modern td.ir-clickable.ir-empty {
        color: #adb5bd;
        font-weight: 400;
        cursor: default;
        text-decoration: none;
    }

    #datatable.ir-modern td.ir-clickable.ir-empty:hover {
        background-color: transparent !important;
    }

    .ir-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .2px;
        white-space: nowrap;
    }

    .ir-badge-green { background: #dcfce7; color: #15803d; }
    .ir-badge-blue { background: #dbeafe; color: #1d4ed8; }
    .ir-badge-amber { background: #fef3c7; color: #b45309; }
    .ir-badge-gray { background: #e5e7eb; color: #4b5563; }

    div.dataTables_wrapper .dataTables_paginate {
        float: right;
        margin-top: 10px;
    }

    div.dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px !important;
        margin-left: 3px;
    }

    div.dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #1E3A8A !important;
        border-color: #1E3A8A !important;
        color: #fff !important;
    }

    div.dataTables_wrapper .dataTables_info {
        float: left;
        margin-top: 10px;
    }

    div.dataTables_wrapper .dataTables_filter input {
        border-radius: 6px;
    }

    div.dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
    }

    .ir-legend {
        font-size: 12.5px;
    }

    .ir-legend .card {
        border: 0;
        border-radius: 10px;
        background: #f8fafc;
        box-shadow: 0 1px 4px rgba(15, 23, 42, .06);
    }

    .ir-legend ul {
        padding-left: 18px;
        margin-bottom: 0;
    }

    #mymodalCellDetail .modal-content {
        border: 0;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 16px 42px rgba(15, 23, 42, .24);
    }

    #mymodalCellDetail .modal-header {
        background: linear-gradient(90deg, #191970, #1e90ff);
        border: 0;
        padding: 16px 20px;
    }

    #mymodalCellDetail .modal-title {
        font-size: 16px;
        font-weight: 700;
    }

    #mymodalCellDetail .modal-body {
        padding: 22px;
        background: #f8fafc;
    }

    .ir-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border: 1px solid #e5e9f2;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 10px;
    }

    .ir-detail-row .label {
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .ir-detail-row .value {
        color: #0f172a;
        font-size: 14px;
        font-weight: 700;
        text-align: right;
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
                <table id="datatable" class="ir-modern table table-striped table-bordered table-hover table-sm text-nowrap" style="width:100%">
                    <thead class="table-gradient">
                        <tr>
                            <th>No</th>
                            <th>Supplier Invoice No</th>
                            <th>No Kontrabon</th>
                            <th>No Reff</th>
                            <th>Supplier Name</th>
                            <th>Amount</th>
                            <th>Amount Add in PV</th>
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

<div class="modal fade" id="mymodalCellDetail" tabindex="-1" role="dialog" aria-labelledby="irCellDetailTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white">
                <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="irCellDetailTitle"><i class="fa fa-info-circle" aria-hidden="true"></i> <span id="irCellDetailStage"></span></h4>
            </div>
            <div class="modal-body">
                <div id="irCellDetailLoading" class="text-center text-muted py-3">
                    <i class="fa fa-spinner fa-spin"></i> Memuat data...
                </div>
                <div id="irCellDetailContent" style="display:none;">
                    <div class="ir-detail-row">
                        <span class="label">No Dokumen</span>
                        <span class="value" id="irCellDetailDoc">-</span>
                    </div>
                    <div class="ir-detail-row">
                        <span class="label">Tanggal</span>
                        <span class="value" id="irCellDetailTanggal">-</span>
                    </div>
                    <div class="ir-detail-row" id="irCellDetailOlehRow">
                        <span class="label" id="irCellDetailOlehLabel">Oleh</span>
                        <span class="value" id="irCellDetailOleh">-</span>
                    </div>
                </div>
                <div id="irCellDetailEmpty" class="text-center text-muted py-3" style="display:none;">
                    <i class="fa fa-exclamation-circle"></i> <span id="irCellDetailEmptyMsg">Data tidak ditemukan.</span>
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
    var IR_STAGE_LABELS = {
        no_invoice: 'Supplier Invoice (SI)',
        doc_number: 'Invoice Received (IR)',
        bpbdate: 'Barang Diterima (BPB Date)',
        dateinput: 'BPB Dibuat (BPB Create)',
        confirm_date: 'BPB Disetujui (BPB-A)',
        trf_date: 'BPB Transfer ke Accounting (BPB-R)',
        tfta_date: 'Finance to Accounting (FTA)',
        receive_acc_date: 'Accounting Receive from Finance (ARF)',
        tatp_date: 'Accounting to Purchasing (ATP)',
        receive_pch_date: 'Purchasing Receive from Accounting (PRA)',
        tptf_date: 'Purchasing to Finance (PTF)',
        receive_fin_date: 'Finance Receive from Purchasing (FRP)',
        tgl_kbon: 'Kontrabon (KB)',
        pvl_date: 'Payment Voucher List (PVL)',
        pvl_approve_date: 'PVL Approve',
        tgl_payment: 'Payment List (PL)',
        tgl_approve_lp: 'PL Approve (Second Approve)',
        bankout_date: 'Pembayaran (Bank Out / PCO / FTR)'
    };

    function irMakeClickableCell(stage) {
        return function (td, cellData) {
            $(td).addClass('ir-clickable').attr('data-stage', stage);
            if (!cellData || cellData === '-') {
                $(td).addClass('ir-empty');
            }
        };
    }

    function irStatusBadge(data) {
        if (!data) return '-';
        var cls = 'ir-badge-gray';
        var d = String(data).toLowerCase();
        if (d.indexOf('accepted') !== -1) cls = 'ir-badge-green';
        else if (d.indexOf('received') !== -1) cls = 'ir-badge-blue';
        else if (d.indexOf('reject') !== -1 || d.indexOf('cancel') !== -1) cls = 'ir-badge-amber';
        return '<span class="ir-badge ' + cls + '">' + data + '</span>';
    }

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
                { data: 'no_invoice', createdCell: irMakeClickableCell('no_invoice') },
                { data: 'doc_number', createdCell: irMakeClickableCell('doc_number') },
                { data: 'no_reff' },
                { data: 'nama_supp' },
                { data: 'amount' },
                { data: 'amount_add_pv' },
                { data: 'status', render: function (data) { return irStatusBadge(data); } },
                { data: 'tgl_invoice', createdCell: irMakeClickableCell('no_invoice') },
                { data: 'tgl_penerimaan', createdCell: irMakeClickableCell('doc_number') },
                { data: 'bpbdate', createdCell: irMakeClickableCell('bpbdate') },
                { data: 'dateinput', createdCell: irMakeClickableCell('dateinput') },
                { data: 'confirm_date', createdCell: irMakeClickableCell('confirm_date') },
                { data: 'trf_date', createdCell: irMakeClickableCell('trf_date') },
                { data: 'tfta_date', createdCell: irMakeClickableCell('tfta_date') },
                { data: 'receive_acc_date', createdCell: irMakeClickableCell('receive_acc_date') },
                { data: 'tatp_date', createdCell: irMakeClickableCell('tatp_date') },
                { data: 'receive_pch_date', createdCell: irMakeClickableCell('receive_pch_date') },
                { data: 'tptf_date', createdCell: irMakeClickableCell('tptf_date') },
                { data: 'receive_fin_date', createdCell: irMakeClickableCell('receive_fin_date') },
                { data: 'tgl_kbon', createdCell: irMakeClickableCell('tgl_kbon') },
                { data: 'pvl_date', createdCell: irMakeClickableCell('pvl_date') },
                { data: 'pvl_approve_date', createdCell: irMakeClickableCell('pvl_approve_date') },
                { data: 'tgl_payment', createdCell: irMakeClickableCell('tgl_payment') },
                { data: 'tgl_approve_lp', createdCell: irMakeClickableCell('tgl_approve_lp') },
                { data: 'bankout_date', createdCell: irMakeClickableCell('bankout_date') }
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

        $('#datatable tbody').on('click', 'td.ir-clickable', function () {
            if ($(this).hasClass('ir-empty')) return;

            var stage = $(this).data('stage');
            var rowData = window.irTable.row($(this).closest('tr')).data();
            if (!stage || !rowData) return;

            $('#irCellDetailStage').text(IR_STAGE_LABELS[stage] || stage);
            $('#irCellDetailContent').hide();
            $('#irCellDetailEmpty').hide();
            $('#irCellDetailLoading').show();
            $('#mymodalCellDetail').modal('show');

            $.ajax({
                type: 'POST',
                url: 'ajax_ir_cell_detail.php',
                dataType: 'json',
                data: {
                    stage: stage,
                    no_invoice: rowData.no_invoice,
                    doc_number: rowData.doc_number,
                    nama_supp: rowData.nama_supp
                },
                success: function (resp) {
                    $('#irCellDetailLoading').hide();
                    if (resp && resp.ok) {
                        $('#irCellDetailDoc').text(resp.no_dokumen || '-');
                        $('#irCellDetailTanggal').text(resp.tanggal || '-');
                        if (resp.label) {
                            $('#irCellDetailOlehLabel').text(resp.label);
                            $('#irCellDetailOleh').text(resp.oleh || '-');
                            $('#irCellDetailOlehRow').show();
                        } else {
                            $('#irCellDetailOlehRow').hide();
                        }
                        $('#irCellDetailContent').show();
                    } else {
                        $('#irCellDetailEmptyMsg').text((resp && resp.message) || 'Data tidak ditemukan.');
                        $('#irCellDetailEmpty').show();
                    }
                },
                error: function () {
                    $('#irCellDetailLoading').hide();
                    $('#irCellDetailEmptyMsg').text('Gagal memuat data.');
                    $('#irCellDetailEmpty').show();
                }
            });
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
