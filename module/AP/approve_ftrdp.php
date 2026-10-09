<?php
/* ============================================================================
   Approve FTR DP - halaman persetujuan, TERPISAH dari daftarnya.
   Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Bentuknya SENGAJA dibuat sama dgn Approval Update BPB: ceklis per baris,
   tombol Approve/Cancel massal di kaki kartu, dan modal rincian. Versi
   pertama dibuat dgn menyalin halaman DAFTAR - ada kartu filter dan tombol
   Approve per baris - dan itu memang bukan yang diminta.

   Isinya HANYA dokumen berstatus draft; penyaringnya di ajx_approve_ftrdp.php.
   ============================================================================ */
include '../header.php';
?>


<!-- Kosakata .ftl- dipakai bersama halaman daftar Update BPB Fabric, FTR
     CBD/DP, dan Petty Cash Out - bentuknya sama dgn List Memorial Journal.
     Perabot khas halaman approve ini (.aub-) ada di app-ubf-form.css. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link rel="stylesheet" href="../css/app-ubf-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ubf-form.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fa fa-paper-plane" aria-hidden="true"></i></span>
      <div>
        <h1>Approve FTR DP</h1>
        <span class="ftl-crumb">AP &rsaquo; FTR &rsaquo; Approval &rsaquo; FTR DP</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">
      <div class="aub-bar">
        <span class="aub-note">
          <i class="fa fa-info-circle" aria-hidden="true"></i>
          FTR DP pending approval: <b id="pendingCount">0</b>
        </span>
      </div>
    </div><!-- /.ftl-panel -->

    <div class="ftl-body">
      <div class="ftl-tblwrap">
        <table id="table-data" class="table ftl-tbl aub-tbl" style="width:100%">
          <thead>
            <tr class="thead-dark">
              <th style="width:36px;"><input type="checkbox" id="select_all"></th>
              <th style="width:188px;">No FTR DP</th>
              <th style="width:110px;">FTR Date</th>
              <th class="text-left">Supplier</th>
              <th class="text-left">No PO</th>
              <th style="width:130px;">Total</th>
              <th style="width:64px;">Curr</th>
              <th style="width:92px;">Status</th>
              <th style="width:196px;">Created By</th>
              <th style="width:150px;">Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div><!-- /.ftl-body -->

    <!-- Tombolnya di kaki kartu, sejajar dgn form Update BPB Fabric.
         Penghitung di kirinya memberi tahu berapa baris yang tercentang -
         sebelumnya baru ketahuan sesudah tombolnya ditekan. -->
    <div class="ub-foot">
      <span class="ub-foot-count"><b id="selectedCount">0</b> FTR DP selected</span>
      <span class="ub-foot-sisi">
        <button type="button" id="btnCancel" class="app-btn app-btn-danger app-btn-sm">
          <i class="fa fa-times" aria-hidden="true"></i> Cancel
        </button>
        <button type="button" id="btnApprove" class="app-btn app-btn-success app-btn-sm">
          <i class="fa fa-check" aria-hidden="true"></i> Approve
        </button>
      </span>
    </div>

  </div><!-- /.ftl-card -->
</div><!-- /.container-fluid -->

<!-- Modal Detail. Bentuknya mengikuti modal rincian di halaman daftar
     (ftl-modal is-titlefirst is-wide) supaya satu keluarga. -->
<div class="modal fade ftl-modal is-titlefirst is-wide" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
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
        url: 'ajx_approve_ftrdp.php',
        type: 'POST'
    },

    columns: [
        { data: 'checkbox', orderable: false },
        { data: 'no_ftr' },
        { data: 'tgl_ftr' },
        { data: 'supp' },
        { data: 'no_po' },
        { data: 'total', render: $.fn.dataTable.render.text() },
        { data: 'curr' },
        { data: 'status' },
        { data: 'create_user' },
        { data: 'action', orderable: false },
    ],

    columnDefs: [
        /* Nomor dokumen ditebalkan spt di halaman daftar; kolom teks
           panjang dibiarkan rata kiri supaya tidak melayang di tengah. */
        { targets: [0, 2, 6, 9], className: 'text-center' },
        { targets: [1], className: 'text-center ftl-doc' },
        { targets: [5], className: 'text-right ftl-amt' },
        { targets: [7], className: 'text-center nw' },
        { targets: [3, 4, 8], className: 'text-left' }
    ],

    drawCallback: function () {
        $('#select_all').prop('checked', false);
        $('#pendingCount').text(this.api().data().count());
        /* Centang ikut hilang tiap tabel digambar ulang, jadi penghitungnya
           harus ikut dinolkan - kalau tidak, angkanya tertinggal. */
        $('#selectedCount').text($('#table-data tbody .chk-ftr:checked').length);
    },
});

function dataTableReload() {
    datatable.ajax.reload();
}

function perbaruiTercentang() {
    $('#selectedCount').text($('#table-data tbody .chk-ftr:checked').length);
}

// Select all checkbox
$('#select_all').on('click', function () {
    const checked = this.checked;
    $('#table-data tbody .chk-ftr').prop('checked', checked);
    perbaruiTercentang();
});
$('#table-data').on('change', '.chk-ftr', perbaruiTercentang);

function getSelectedFtr() {
    const selected = [];
    $('#table-data tbody .chk-ftr:checked').each(function () {
        selected.push(this.value);
    });
    return selected;
}

function prosesApproveCancel(action) {
    const selected = getSelectedFtr();

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Select at least 1 FTR DP!'
        });
        return;
    }

    const isApprove = action === 'approve';

    Swal.fire({
        title: isApprove ? 'Approve the selected FTR DP?' : 'Cancel the selected FTR DP?',
        text: selected.length + ' FTR DP will have their status changed to ' + (isApprove ? 'Approved' : 'Cancel') + '.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isApprove ? '#28a745' : '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: isApprove ? 'Yes, Approve' : 'Yes, Cancel',
        cancelButtonText: 'Close'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: 'proses_approve_ftrdp.php',
            type: 'POST',
            data: { action: action, no_ftr: selected, approve_user: '<?php echo htmlspecialchars($user, ENT_QUOTES); ?>' },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    /* FTR tidak memposting jurnal di titik ini - jurnalnya terbentuk
                       nanti lewat Kontra Bon - jadi tidak ada hitungan jurnal di sini. */
                    let text = res.updated + ' FTR DP processed successfully' + (res.skipped > 0 ? ', ' + res.skipped + ' skipped' : '');
                    if (res.warnings && res.warnings.length > 0) {
                        text += '\n\nNote:\n' + res.warnings.join('\n');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: text,
                        timer: res.warnings && res.warnings.length > 0 ? undefined : 2000,
                        showConfirmButton: res.warnings && res.warnings.length > 0
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
$('#table-data').on('click', '.btn-view-ftr', function () {
    const noFtr = this.dataset.no;

    $('#txt_bpb').text('FTR DP - ' + noFtr);
    $('#txt_tglbpb, #txt_supp, #txt_status, #txt_created_by, #txt_deskripsi').html('');
    $('#details').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#mymodal').modal('show');

    $.ajax({
        url: 'get_detail_ftrdp.php',
        type: 'GET',
        data: { no_ftr: noFtr },
        dataType: 'json',
        success: function (res) {
            const h = res.header;
            if (h) {
                $('#txt_tglbpb').html('<b>FTR Date:</b> ' + escapeHtml(h.tgl_ftr));
                $('#txt_supp').html('<b>Supplier:</b> ' + escapeHtml(h.supp || '-'));
                $('#txt_status').html('<b>Status:</b> ' + escapeHtml(h.status));
                const createdByText = h.create_user ? (h.create_user + ' (' + h.create_date + ')') : '-';
                $('#txt_created_by').html('<b>Created By:</b> ' + escapeHtml(createdByText));
                $('#txt_deskripsi').html('<b>Payment Date:</b> ' + escapeHtml(h.tgl_bayar)
                    + ' &nbsp;&nbsp; <b>SubTotal:</b> ' + escapeHtml(h.subtotal)
                    + ' &nbsp;&nbsp; <b>Tax:</b> ' + escapeHtml(h.tax)
                    + ' &nbsp;&nbsp; <b>Total:</b> ' + escapeHtml(h.total) + ' ' + escapeHtml(h.curr || ''));
            }

            if (!res.items.length) {
                $('#details').html('<div class="text-center p-3 text-muted">No items found</div>');
                return;
            }

            /* Tabel rincian dijadikan DataTable spt modal di halaman daftar:
               punya pencarian, jumlah baris, dan penomoran halaman - sebuah
               satu FTR bisa memuat banyak baris PO. Tiap sel diberi kelas
               perataannya sendiri; kalau tidak, angka rata kiri dan kolom
               pendek saling menempel. */
            let html = '<table id="table-detail-approve" class="table ftl-tbl" style="width:100%">';
            html += '<thead><tr class="thead-dark">'
                + '<th class="nw">No PO</th><th class="nw">PO Date</th><th class="text-left">No PI</th>'
                + '<th class="nw">Curr</th><th class="text-right">SubTotal</th>'
                + '<th class="text-right">Tax</th><th class="text-right">Total</th>'
                + '</tr></thead><tbody>';

            res.items.forEach(function (it) {
                html += '<tr>'
                    + '<td class="ftl-doc">' + escapeHtml(it.no_po || '-') + '</td>'
                    + '<td class="nw">' + escapeHtml(it.tgl_po) + '</td>'
                    + '<td class="text-left">' + escapeHtml(it.no_pi || '-') + '</td>'
                    + '<td class="nw">' + escapeHtml(it.curr || '-') + '</td>'
                    + '<td class="text-right ftl-amt">' + escapeHtml(it.subtotal) + '</td>'
                    + '<td class="text-right ftl-amt">' + escapeHtml(it.tax) + '</td>'
                    + '<td class="text-right ftl-amt">' + escapeHtml(it.total) + '</td>'
                    + '</tr>';
            });

            html += '</tbody></table>';
            /* Instance lama dibuang dulu: modal ini dibuka berulang kali untuk
               dokumen berbeda, dan DataTables menolak diinisialisasi dua kali
               pada id yang sama. */
            if ($.fn.DataTable.isDataTable('#table-detail-approve')) {
                $('#table-detail-approve').DataTable().destroy();
            }
            $('#details').html(html);
            $('#table-detail-approve').DataTable({
                ordering: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                language: { emptyTable: 'No PO line in this FTR.' }
            });
        },
        error: function () {
            $('#details').html('<div class="text-center p-3 text-danger">Failed to load detail</div>');
        }
    });
});

</script>

</body>

</html>
