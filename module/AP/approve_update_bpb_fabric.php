<?php include '../header.php' ?>

<style type="text/css">
    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    #table-data {
        width: 100% !important;
    }

    #table-data th,
    #table-data td {
        vertical-align: top;
    }

    #table-data td:last-child {
        white-space: nowrap;
    }

    @media (min-width: 992px) {
        #mymodal .modal-dialog {
            max-width: 75%;
        }
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Header -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fa fa-check-circle" aria-hidden="true"></i> APPROVE UPDATE BPB FABRIC</h5>
</div>

<div class="card-body p-3">
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <i class="fa fa-info-circle mr-2" aria-hidden="true"></i>
        Number of requests pending approval: <b class="ml-1" id="pendingCount">0</b>
    </div>

    <button type="button" id="btnApprove" class="btn btn-success btn-sm">
        <i class="fa fa-check" aria-hidden="true"></i> Approve
    </button>
    <button type="button" id="btnCancel" class="btn btn-danger btn-sm ml-2">
        <i class="fa fa-times" aria-hidden="true"></i> Cancel
    </button>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div style="overflow-x:auto;">
          <table id="table-data"
           class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient">
            <tr>
                <th style="text-align: center;vertical-align: middle;width:36px;"><input type="checkbox" id="select_all"></th>
                <th style="text-align: center;vertical-align: middle;">No. Trans</th>
                <th style="text-align: center;vertical-align: middle;">Trans Date</th>
                <th style="text-align: center;vertical-align: middle;">Status</th>
                <th style="text-align: center;vertical-align: middle;">Description</th>
                <th style="text-align: center;vertical-align: middle;">Created By</th>
                <th style="text-align: center;vertical-align: middle;">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>
</div>
</div>


<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-3 mb-2"></div>
          <div id="txt_supp" class="col-md-3 mb-2"></div>
          <div id="txt_status" class="col-md-3 mb-2"></div>
          <div id="txt_created_by" class="col-md-3 mb-2"></div>
          <div id="txt_deskripsi" class="col-12 mb-2"></div>
          <div id="details" class="col-12 mt-2"></div>
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
  // Hide submenus
  $('#body-row .collapse').collapse('hide');

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left');

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }

    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>

<script type="text/javascript">

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (ch) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
    });
}

function formatMoney(amount, decimalCount = 2) {
    const val = parseFloat(amount);
    if (isNaN(val)) return '0.00';
    return val.toLocaleString('en-US', {
        minimumFractionDigits: decimalCount,
        maximumFractionDigits: decimalCount
    });
}

let datatable = $("#table-data").DataTable({
    ordering: false,
    processing: true,
    serverSide: false,
    pageLength: 10,
    searching: true,
    info: true,
    autoWidth: false,

    ajax: {
        url: 'ajx_approve_update_bpb_fabric.php',
        type: 'POST'
    },

    columns: [
        { data: 'checkbox', orderable: false },
        { data: 'no_pengajuan' },
        { data: 'tgl_pengajuan' },
        { data: 'status' },
        { data: 'deskripsi' },
        { data: 'created_by' },
        { data: 'action', orderable: false },
    ],

    columnDefs: [
        { targets: [0, 3, 6], className: 'text-center' },
        { targets: 6, width: '170px' }
    ],

    drawCallback: function () {
        $('#select_all').prop('checked', false);
        $('#pendingCount').text(this.api().data().count());
    },
});

function dataTableReload() {
    datatable.ajax.reload();
}

// Select all checkbox
$('#select_all').on('click', function () {
    const checked = this.checked;
    $('#table-data tbody .chk-pengajuan').prop('checked', checked);
});

function getSelectedPengajuan() {
    const selected = [];
    $('#table-data tbody .chk-pengajuan:checked').each(function () {
        selected.push(this.value);
    });
    return selected;
}

function prosesApproveCancel(action) {
    const selected = getSelectedPengajuan();

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Select at least 1 request!'
        });
        return;
    }

    const isApprove = action === 'approve';

    Swal.fire({
        title: isApprove ? 'Approve the selected requests?' : 'Cancel the selected requests?',
        text: selected.length + ' request(s) will have their status changed to ' + (isApprove ? 'Approved' : 'Cancel') + '.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isApprove ? '#28a745' : '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: isApprove ? 'Yes, Approve' : 'Yes, Cancel',
        cancelButtonText: 'Close'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: 'proses_approve_update_bpb_fabric.php',
            type: 'POST',
            data: { action: action, no_pengajuan: selected, approve_user: '<?php echo htmlspecialchars($user, ENT_QUOTES); ?>' },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    let text = res.updated + ' request(s) processed successfully' + (res.skipped > 0 ? ', ' + res.skipped + ' skipped' : '');
                    if (res.journal_entries > 0) {
                        text += ', ' + res.journal_entries + ' journal entr' + (res.journal_entries === 1 ? 'y' : 'ies') + ' booked';
                    }
                    if (res.journal_warnings && res.journal_warnings.length > 0) {
                        text += '\n\nWarning:\n' + res.journal_warnings.join('\n');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: text,
                        timer: res.journal_warnings && res.journal_warnings.length > 0 ? undefined : 2000,
                        showConfirmButton: res.journal_warnings && res.journal_warnings.length > 0
                    });
                    datatable.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed!', text: res.message || 'An error occurred' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process the requests' });
            }
        });
    });
}

$('#btnApprove').on('click', function () {
    prosesApproveCancel('approve');
});

$('#btnCancel').on('click', function () {
    prosesApproveCancel('cancel');
});

// View detail of an edit request
$('#table-data').on('click', '.btn-view-pengajuan', function () {
    const noPengajuan = this.dataset.no;

    $('#txt_bpb').text('Edit Request - ' + noPengajuan);
    $('#txt_tglbpb, #txt_supp, #txt_status, #txt_created_by, #txt_deskripsi').html('');
    $('#details').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#mymodal').modal('show');

    $.ajax({
        url: 'get_detail_update_bpb_fabric.php',
        type: 'GET',
        data: { no_pengajuan: noPengajuan },
        dataType: 'json',
        success: function (res) {
            const h = res.header;
            if (h) {
                $('#txt_tglbpb').html('<b>Transaction Date:</b> ' + escapeHtml(h.tgl_pengajuan));
                $('#txt_status').html('<b>Status:</b> ' + escapeHtml(h.status));
                const createdByText = h.created_by ? (h.created_by + ' (' + h.created_at + ')') : '-';
                $('#txt_created_by').html('<b>Created By:</b> ' + escapeHtml(createdByText));
                $('#txt_deskripsi').html('<b>Description:</b> ' + escapeHtml(h.deskripsi || '-'));
            }

            if (!res.items.length) {
                $('#details').html('<div class="text-center p-3 text-muted">No items found</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-bordered table-striped table-sm text-center">';
            html += '<thead class="table-gradient text-white"><tr>'
                + '<th>No BPB</th><th>BPB Date</th><th>Supplier</th><th>No WS</th><th>Item</th><th>Qty</th><th>Unit</th><th>Curr</th>'
                + '<th>Price (Old)</th><th>Price (New)</th><th>PPN % (Old)</th><th>PPN % (New)</th>'
                + '</tr></thead><tbody>';

            res.items.forEach(function (it) {
                html += '<tr>'
                    + '<td>' + escapeHtml(it.no_bpb) + '</td>'
                    + '<td>' + escapeHtml(it.tgl_bpb) + '</td>'
                    + '<td>' + escapeHtml(it.nama_supp || '-') + '</td>'
                    + '<td>' + escapeHtml(it.no_ws || '-') + '</td>'
                    + '<td class="text-left">' + escapeHtml(it.desc_item || it.id_item) + '</td>'
                    + '<td>' + formatMoney(it.qty) + '</td>'
                    + '<td>' + escapeHtml(it.unit || '-') + '</td>'
                    + '<td>' + escapeHtml(it.curr || '-') + '</td>'
                    + '<td>' + formatMoney(it.price_old, 4) + '</td>'
                    + '<td>' + formatMoney(it.price_new, 4) + '</td>'
                    + '<td>' + formatMoney(it.ppn_old) + '</td>'
                    + '<td>' + formatMoney(it.ppn_new) + '</td>'
                    + '</tr>';
            });

            html += '</tbody></table></div>';
            $('#details').html(html);
        },
        error: function () {
            $('#details').html('<div class="text-center p-3 text-danger">Failed to load detail</div>');
        }
    });
});

</script>

</body>

</html>
