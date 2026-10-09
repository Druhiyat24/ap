<?php
/* ============================================================================
   Update BPB - ACCESSORIES.  Berkas ini BERDIRI SENDIRI (1 menu = 1 berkas).

   Dokumennya dibaca dari tabel `bpb` dgn bpbno_int LIKE 'GACC/%'. GACC/IN
   maupun GACC/RI ikut semua: diperiksa ke produksi 8 Okt 2026, jurnal
   GACC/RI SEARAH dgn penerimaan (Persediaan Aksesoris didebit, GR/IR
   Aksesoris dikredit, type 'AP - BPB'), jadi bukan retur akuntansi
   seperti GK/RO di Fabric.
   ============================================================================ */
include '../header.php';
?>
<?php /* Nilai tetap berkas ini. Tidak ada parameter di tautan - satu menu
   satu berkas. */
$jenis = 'accessories';
$label = 'Accessories';
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
      <span class="ftl-head-icon"><i class="fa fa-check-circle" aria-hidden="true"></i></span>
      <div>
        <h1>Approve Update BPB <?php echo htmlspecialchars($label); ?></h1>
        <span class="ftl-crumb">Cost Accounting &rsaquo; Update BPB &rsaquo; <?php echo htmlspecialchars($label); ?> &rsaquo; Approval</span>
      </div>
    </div><!-- /.ftl-head -->

    <div class="ftl-panel">
      <div class="aub-bar">
        <span class="aub-note">
          <i class="fa fa-info-circle" aria-hidden="true"></i>
          Requests pending approval: <b id="pendingCount">0</b>
        </span>
      </div>
    </div><!-- /.ftl-panel -->

    <div class="ftl-body">
      <div class="ftl-tblwrap">
        <table id="table-data" class="table ftl-tbl aub-tbl" style="width:100%">
          <thead>
            <tr class="thead-dark">
              <th style="width:36px;"><input type="checkbox" id="select_all"></th>
              <th style="width:168px;">No. Trans</th>
              <th style="width:110px;">Trans Date</th>
              <th style="width:104px;">Status</th>
              <th class="text-left">Description</th>
              <th style="width:210px;">Created By</th>
              <th style="width:104px;">Action</th>
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
      <span class="ub-foot-count"><b id="selectedCount">0</b> request(s) selected</span>
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
        url: 'ajx_approve_update_bpb_accessories.php',
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
        /* Nomor dokumen ditebalkan spt di halaman daftar; kolom teks
           panjang dibiarkan rata kiri supaya tidak melayang di tengah. */
        { targets: [0, 2, 3, 6], className: 'text-center' },
        { targets: [1], className: 'text-center ftl-doc' },
        { targets: [4, 5], className: 'text-left' }
    ],

    drawCallback: function () {
        $('#select_all').prop('checked', false);
        $('#pendingCount').text(this.api().data().count());
        /* Centang ikut hilang tiap tabel digambar ulang, jadi penghitungnya
           harus ikut dinolkan - kalau tidak, angkanya tertinggal. */
        $('#selectedCount').text($('#table-data tbody .chk-pengajuan:checked').length);
    },
});

function dataTableReload() {
    datatable.ajax.reload();
}

function perbaruiTercentang() {
    $('#selectedCount').text($('#table-data tbody .chk-pengajuan:checked').length);
}

// Select all checkbox
$('#select_all').on('click', function () {
    const checked = this.checked;
    $('#table-data tbody .chk-pengajuan').prop('checked', checked);
    perbaruiTercentang();
});
$('#table-data').on('change', '.chk-pengajuan', perbaruiTercentang);

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
            url: 'proses_approve_update_bpb_accessories.php',
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
        url: 'get_detail_update_bpb_accessories.php',
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

            /* Tabel rincian dijadikan DataTable spt modal di halaman daftar:
               punya pencarian, jumlah baris, dan penomoran halaman - sebuah
               pengajuan bisa memuat puluhan baris. Tiap sel diberi kelas
               perataannya sendiri; kalau tidak, angka rata kiri dan kolom
               pendek saling menempel. */
            let html = '<table id="table-detail-approve" class="table ftl-tbl" style="width:100%">';
            html += '<thead><tr class="thead-dark">'
                + '<th class="nw">No BPB</th><th class="nw">BPB Date</th><th class="text-left">Supplier</th>'
                + '<th class="nw">No WS</th><th class="text-left">Item</th>'
                + '<th class="text-right">Qty</th><th class="nw">Unit</th><th class="nw">Curr</th>'
                + '<th class="text-right">Price (Old)</th><th class="text-right">Price (New)</th>'
                + '<th class="text-right">PPN % (Old)</th><th class="text-right">PPN % (New)</th>'
                + '</tr></thead><tbody>';

            res.items.forEach(function (it) {
                html += '<tr>'
                    + '<td class="ftl-doc">' + escapeHtml(it.no_bpb) + '</td>'
                    + '<td class="nw">' + escapeHtml(it.tgl_bpb) + '</td>'
                    + '<td class="text-left">' + escapeHtml(it.nama_supp || '-') + '</td>'
                    + '<td class="nw">' + escapeHtml(it.no_ws || '-') + '</td>'
                    + '<td class="text-left ub-item">' + escapeHtml(it.desc_item || it.id_item) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.qty) + '</td>'
                    + '<td class="nw">' + escapeHtml(it.unit || '-') + '</td>'
                    + '<td class="nw">' + escapeHtml(it.curr || '-') + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.price_old, 4) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.price_new, 4) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.ppn_old) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.ppn_new) + '</td>'
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
                language: { emptyTable: 'No item in this request.' }
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
