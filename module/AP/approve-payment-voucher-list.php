<?php include '../header.php' ?>

<style type="text/css">
    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    #table-approve-pl {
        width: 100% !important;
    }

    #table-approve-pl th,
    #table-approve-pl td {
        vertical-align: middle;
    }

    @media (min-width: 992px) {
        #mymodal .modal-dialog {
            max-width: 800px;
        }
    }

    #mymodal .modal-content {
        overflow: hidden;
    }

    #mymodal .modal-body {
        padding: 0.4rem 0.6rem;
    }

    #mymodal #mytable2 td,
    #mymodal #mytable2 th {
        padding: 4px 6px;
    }

    #mymodal #mytable2 td:not(.col-keterangan),
    #mymodal #mytable2 th:not(.col-keterangan) {
        white-space: nowrap;
    }

    .pl-approve-bar {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 2px solid #1E3A8A;
        padding: 12px 16px;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        border-radius: 0 0 10px 10px;
    }

    .btn-pl-approve {
        background: linear-gradient(90deg, #191970, #1e90ff);
        color: #fff;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        padding: 8px 22px;
    }

    .btn-pl-approve:hover {
        color: #fff;
    }

    .btn-pl-cancel {
        border-radius: 5px;
        font-weight: 600;
        padding: 8px 22px;
    }

    .btn-pl-approve:disabled,
    .btn-pl-cancel:disabled {
        opacity: 0.5;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Header -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3"
        style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0"><i class="fa fa-thumbs-up" aria-hidden="true"></i> APPROVAL - PAYMENT VOUCHER LIST</h5>
    </div>
    </div>

    <!-- Card Table -->
    <div class="card shadow border-0 mt-4">
        <div class="card-body p-4">
            <div style="overflow-x:auto;">
                <table id="table-approve-pl" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
                    <thead class="table-gradient">
                        <tr>
                            <th style="text-align: center;vertical-align: middle;width: 36px;"><input type="checkbox" id="chkSelectAll"></th>
                            <th style="text-align: center;vertical-align: middle;">No Payment Voucher List</th>
                            <th style="text-align: center;vertical-align: middle;">PL Date</th>
                            <th style="text-align: center;vertical-align: middle;">Keterangan</th>
                            <th style="text-align: center;vertical-align: middle;">Jumlah PV</th>
                            <th style="text-align: center;vertical-align: middle;">Created By</th>
                            <th style="text-align: center;vertical-align: middle;">Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="pl-approve-bar">
                <button type="button" class="btn btn-info btn-pl-approve" id="btnApproveSelected" disabled>
                    <i class="fa fa-thumbs-up"></i> Approve
                </button>
                <button type="button" class="btn btn-outline-danger btn-pl-cancel" id="btnCancelSelected" disabled>
                    <i class="fa fa-times-circle"></i> Cancel
                </button>
                <span id="selectedCount" class="text-muted">0 item dipilih</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white py-2" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_pl" style="font-size: 16px;"></h4>
            </div>
            <div id="txt_pl_date" style="font-size: 12px; padding: 0.4rem 0.6rem 0;"></div>
            <div id="txt_pl_desc" style="font-size: 12px; padding: 0.1rem 0.6rem 0.4rem;"></div>
            <div id="details" style="font-size: 12px; padding: 0 0.6rem 0.6rem; overflow-x: auto;">
                  <table id="mytable2" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%" style="font-size: 11.5px;text-align:left;">
                        <thead>
                            <tr>
                                <th style="text-align:center;">Type</th>
                                <th style="text-align:center;">No Payment Voucher</th>
                                <th style="text-align:center;">PV Date</th>
                                <th style="text-align:center;">Supplier</th>
                                <th style="text-align:center;">Curr</th>
                                <th style="text-align:center;">Total</th>
                                <th class="col-keterangan" style="text-align:center;width:18%;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody id="datatable_modal"></tbody>
                    </table>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
    $('#body-row .collapse').collapse('hide');
    $('#collapse-icon').addClass('fa-angle-double-left');
    $('[data-toggle=sidebar-colapse]').click(function() {
        SidebarCollapse();
    });

    function SidebarCollapse () {
        $('.menu-collapsed').toggleClass('d-none');
        $('.sidebar-submenu').toggleClass('d-none');
        $('.submenu-icon').toggleClass('d-none');
        $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

        var SeparatorTitle = $('.sidebar-separator-title');
        if ( SeparatorTitle.hasClass('d-flex') ) {
            SeparatorTitle.removeClass('d-flex');
        } else {
            SeparatorTitle.addClass('d-flex');
        }

        $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
    }
</script>

<script type="text/javascript">
    let approveTable = $('#table-approve-pl').DataTable({
        ordering: false,
        processing: true,
        serverSide: false,
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,

        ajax: {
            url: 'ajx_approve-payment-voucher-list.php',
            type: 'POST'
        },

        columns: [
            { data: 'select', orderable: false },
            { data: 'pl_number' },
            { data: 'pl_date' },
            { data: 'deskripsi' },
            { data: 'jumlah_pv' },
            { data: 'created_by' },
            { data: 'created_date' },
        ],

        columnDefs: [
            { targets: [3], className: 'text-left' },
            { targets: [0, 1, 2, 4, 5, 6], className: 'text-center' },
        ],

        drawCallback: function () {
            updateSelectedCount();
            $('#chkSelectAll').prop('checked', false);
        }
    });

    function reloadApproveTable() {
        approveTable.ajax.reload(function () {
            approveTable.columns.adjust();
        }, false);
    }

    function allCheckboxes() {
        return $($('#table-approve-pl').DataTable().rows().nodes()).find('.pl-approve-chk');
    }

    function updateSelectedCount() {
        const n = allCheckboxes().filter(':checked').length;
        $('#selectedCount').text(n + ' item dipilih');
        $('#btnApproveSelected').prop('disabled', n === 0);
        $('#btnCancelSelected').prop('disabled', n === 0);
    }

    $('#table-approve-pl').on('change', '.pl-approve-chk', function () {
        updateSelectedCount();
    });

    $('#chkSelectAll').on('change', function () {
        const checked = this.checked;
        allCheckboxes().prop('checked', checked);
        updateSelectedCount();
    });

    // Klik baris (kolom No Payment Voucher List) untuk lihat detail PV di dalamnya
    $('#table-approve-pl').on('click', 'tbody tr td:nth-child(2)', function () {
        const data = approveTable.row($(this).closest('tr')).data();
        if (!data) return;

        var pl_number = data.pl_number;

        $.ajax({
            type: 'POST',
            url: 'get_data_payment_voucher_list_det.php',
            data: { 'pl_number': pl_number },
            cache: false,
            success: function (html) {
                $('#datatable_modal').html(html);
            },
            error: function (xhr) {
                console.log(xhr);
            }
        });

        $('#txt_pl').html(pl_number);
        $('#txt_pl_date').html('<b>Payment Voucher List Date : </b>' + data.pl_date);
        $('#txt_pl_desc').html('<b>Keterangan : </b>' + data.deskripsi);
        $('#mymodal').modal('show');
    });

    // Bulk Approve - semua PVL terpilih dikirim ke approve_payment_voucher_list.php
    $('#btnApproveSelected').on('click', function () {
        const plNumbers = allCheckboxes().filter(':checked').map(function () {
            return this.dataset.pl;
        }).get();
        if (plNumbers.length === 0) return;

        Swal.fire({
            title: 'Approve ' + plNumbers.length + ' Payment Voucher List?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Approve!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $('#btnApproveSelected').prop('disabled', true);

            const requests = plNumbers.map(pl_number => $.ajax({
                type: 'POST',
                url: 'approve_payment_voucher_list.php',
                data: { pl_number: pl_number }
            }));

            Promise.allSettled(requests).then(results => {
                let success = 0;
                let errors = [];

                results.forEach((r, i) => {
                    const response = r.status === 'fulfilled' ? r.value : '';
                    if (String(response).indexOf('Error') === 0) {
                        errors.push(plNumbers[i] + ': ' + response);
                    } else if (r.status === 'fulfilled') {
                        success++;
                    } else {
                        errors.push(plNumbers[i] + ': Request failed');
                    }
                });

                let msg = success + ' Payment Voucher List Berhasil Di Approve';
                if (errors.length > 0) {
                    msg += '<br><br><b>Gagal:</b><br>' + errors.join('<br>');
                }

                Swal.fire({
                    icon: errors.length > 0 ? 'warning' : 'success',
                    title: 'Selesai',
                    html: msg
                });

                reloadApproveTable();
            });
        });
    });

    // Bulk Cancel - semua PVL terpilih dikirim ke cancel_payment_voucher_list.php
    $('#btnCancelSelected').on('click', function () {
        const plNumbers = allCheckboxes().filter(':checked').map(function () {
            return this.dataset.pl;
        }).get();
        if (plNumbers.length === 0) return;

        Swal.fire({
            title: 'Cancel ' + plNumbers.length + ' Payment Voucher List?',
            text: 'Status akan diubah menjadi Cancel.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel!',
            cancelButtonText: 'Close'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $('#btnCancelSelected').prop('disabled', true);

            const requests = plNumbers.map(pl_number => $.ajax({
                type: 'POST',
                url: 'cancel_payment_voucher_list.php',
                data: { pl_number: pl_number, cancel_user: '<?php echo $user ?>' }
            }));

            Promise.allSettled(requests).then(results => {
                let success = 0;
                let errors = [];

                results.forEach((r, i) => {
                    const response = r.status === 'fulfilled' ? r.value : '';
                    if (String(response).indexOf('Error') === 0) {
                        errors.push(plNumbers[i] + ': ' + response);
                    } else if (r.status === 'fulfilled') {
                        success++;
                    } else {
                        errors.push(plNumbers[i] + ': Request failed');
                    }
                });

                let msg = success + ' Payment Voucher List Berhasil Di Cancel';
                if (errors.length > 0) {
                    msg += '<br><br><b>Gagal:</b><br>' + errors.join('<br>');
                }

                Swal.fire({
                    icon: errors.length > 0 ? 'warning' : 'success',
                    title: 'Selesai',
                    html: msg
                });

                reloadApproveTable();
            });
        });
    });
</script>

</body>

</html>
