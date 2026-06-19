<?php include '../header.php' ?>

<style type="text/css">
    label { font-size: 14px; }
    input, textarea, select { font-size: 14px; }

    .table-gradient th {
        background: #1E3A8A;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .card.shadow {
        border-radius: 12px;
        transition: box-shadow .2s ease-in-out;
    }
    .card.shadow:hover {
        box-shadow: 0 .75rem 1.5rem rgba(30,144,255,.15) !important;
    }
    .card.shadow > .card-header:first-child {
        border-radius: 12px 12px 0 0;
    }
    .card.shadow > .card-body:last-child {
        border-radius: 0 0 12px 12px;
    }

    #table-data {
        width: 100% !important;
    }

    @media (min-width: 992px) {
        #modalDetail .modal-dialog {
            max-width: 80%;
        }
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">

  <!-- Upload Card -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fa fa-upload" aria-hidden="true"></i> UPDATE FABRIC BARCODE</h5>
    </div>
    <div class="card-body p-3">
      <div class="row g-3 align-items-end">
        <div class="col-md-4 mb-2">
          <label class="form-label"><b>File Excel</b></label>
          <input type="file" class="form-control form-control-sm" id="fileUpload" accept=".xlsx,.xls">
        </div>
        <div class="col-md-8 mb-2">
          <a href="template_update_np_revisi.php">
            <button type="button" class="btn btn-warning btn-sm mr-2">
              <i class="fa fa-file-excel-o" aria-hidden="true"></i> Download Template
            </button>
          </a>
          <button type="button" id="btnUpload" class="btn btn-primary btn-sm">
            <i class="fa fa-upload" aria-hidden="true"></i> Upload
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- History Card -->
  <div class="card shadow border-0 mt-4">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h6 class="mb-0"><i class="fas fa-history" aria-hidden="true"></i> HISTORY UPDATE</h6>
    </div>
    <div class="card-body p-4">
      <div style="overflow-x:auto;">
        <table id="table-data" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient">
            <tr>
              <th style="text-align: center;vertical-align: middle;">No Dokumen</th>
              <th style="text-align: center;vertical-align: middle;">Tanggal</th>
              <th style="text-align: center;vertical-align: middle;">Total Baris</th>
              <th style="text-align: center;vertical-align: middle;">Sukses</th>
              <th style="text-align: center;vertical-align: middle;">Gagal</th>
              <th style="text-align: center;vertical-align: middle;">Status</th>
              <th style="text-align: center;vertical-align: middle;">Created By</th>
              <th style="text-align: center;vertical-align: middle;">Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="modal-title" id="modalDetailLabel"><i class="fas fa-list"></i> Detail - <span id="txt_no_dok"></span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row mb-2">
          <div id="txt_tgl_dok" class="col-md-3 mb-2"></div>
          <div id="txt_total_row" class="col-md-3 mb-2"></div>
          <div id="txt_total_success" class="col-md-3 mb-2"></div>
          <div id="txt_total_failed" class="col-md-3 mb-2"></div>
          <div id="txt_status" class="col-md-3 mb-2"></div>
          <div id="txt_created_by" class="col-md-9 mb-2"></div>
        </div>
        <div id="details" class="table-responsive"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) {
          SeparatorTitle.removeClass('d-flex');
      } else {
          SeparatorTitle.addClass('d-flex');
      }

      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

<script>
  function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, function (ch) {
          return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
      });
  }

  function formatMoney(amount, decimalCount = 2) {
      const val = parseFloat(amount);
      if (isNaN(val)) return '-';
      return val.toLocaleString('en-US', {
          minimumFractionDigits: 0,
          maximumFractionDigits: decimalCount
      });
  }

  let datatable;

  $(document).ready(function () {
      datatable = $('#table-data').DataTable({
          ordering: false,
          processing: true,
          serverSide: false,
          pageLength: 10,
          searching: true,
          info: true,
          autoWidth: false,
          ajax: {
              url: 'ajx_update_np_revisi.php',
              type: 'POST'
          },
          columns: [
              { data: 'no_dok' },
              { data: 'tgl_dok' },
              { data: 'total_row' },
              { data: 'total_success' },
              { data: 'total_failed' },
              { data: 'status' },
              { data: 'created_by' },
              { data: 'action', orderable: false }
          ],
          columnDefs: [
              { targets: [2, 3, 4, 5, 7], className: 'text-center' }
          ]
      });

      // Upload
      $('#btnUpload').on('click', function () {
          const fileInput = document.getElementById('fileUpload');

          if (!fileInput.files.length) {
              Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Pilih file Excel terlebih dahulu' });
              return;
          }

          const formData = new FormData();
          formData.append('file', fileInput.files[0]);

          Swal.fire({
              title: 'Processing...',
              html: 'Sedang memproses file...',
              allowOutsideClick: false,
              didOpen: () => { Swal.showLoading(); }
          });

          $.ajax({
              type: 'POST',
              url: 'proses_upload_np_revisi.php',
              data: formData,
              processData: false,
              contentType: false,
              dataType: 'json',
              success: function (res) {
                  if (res.success) {
                      Swal.fire({
                          icon: 'success',
                          title: 'Berhasil!',
                          html: 'No Dokumen: <b>' + escapeHtml(res.no_dok) + '</b><br>'
                              + 'Total Baris: ' + res.total_row + '<br>'
                              + 'Sukses: ' + res.total_success + '<br>'
                              + 'Gagal: ' + res.total_failed
                      });
                      fileInput.value = '';
                      datatable.ajax.reload();
                  } else {
                      Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message || 'Terjadi kesalahan' });
                  }
              },
              error: function () {
                  Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengupload file' });
              }
          });
      });

      // View detail
      $('#table-data').on('click', '.btn-view-detail', function () {
          const noDok = this.dataset.no;

          $('#txt_no_dok').text(noDok);
          $('#txt_tgl_dok, #txt_total_row, #txt_total_success, #txt_total_failed, #txt_status, #txt_created_by').html('');
          $('#details').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i></div>');
          $('#modalDetail').modal('show');

          $.ajax({
              url: 'get_detail_update_np_revisi.php',
              type: 'GET',
              data: { no_dok: noDok },
              dataType: 'json',
              success: function (res) {
                  const h = res.header;
                  if (h) {
                      $('#txt_tgl_dok').html('<b>Tanggal:</b> ' + escapeHtml(h.tgl_dok));
                      $('#txt_total_row').html('<b>Total Baris:</b> ' + h.total_row);
                      $('#txt_total_success').html('<b>Sukses:</b> ' + h.total_success);
                      $('#txt_total_failed').html('<b>Gagal:</b> ' + h.total_failed);
                      const statusBadge = h.status === 'Cancel'
                          ? '<span class="badge badge-secondary p-2">Cancel</span>'
                          : '<span class="badge badge-success p-2">Updated</span>';
                      $('#txt_status').html('<b>Status:</b> ' + statusBadge);
                      const createdByText = h.created_by ? (h.created_by + ' (' + h.created_at + ')') : '-';
                      $('#txt_created_by').html('<b>Created By:</b> ' + escapeHtml(createdByText));
                  }

                  if (!res.items.length) {
                      $('#details').html('<div class="text-center p-3 text-muted">No items found</div>');
                      return;
                  }

                  let html = '<table class="table table-bordered table-striped table-sm text-center">';
                  html += '<thead class="table-gradient text-white"><tr>'
                      + '<th>No</th><th>Tipe</th><th>No Dokumen</th><th>No Barcode</th>'
                      + '<th>Curr Lama</th><th>Curr Baru</th><th>Price Lama</th><th>Price Baru</th>'
                      + '<th>Status</th><th>Keterangan</th>'
                      + '</tr></thead><tbody>';

                  res.items.forEach(function (it, idx) {
                      const statusBadge = it.status === 'Success'
                          ? '<span class="badge badge-success p-2">Success</span>'
                          : '<span class="badge badge-danger p-2">Failed</span>';

                      html += '<tr>'
                          + '<td>' + (idx + 1) + '</td>'
                          + '<td>' + escapeHtml(it.tipe) + '</td>'
                          + '<td>' + escapeHtml(it.no_ref || '-') + '</td>'
                          + '<td>' + escapeHtml(it.no_barcode || '-') + '</td>'
                          + '<td>' + escapeHtml(it.curr_old || '-') + '</td>'
                          + '<td>' + escapeHtml(it.curr_new || '-') + '</td>'
                          + '<td class="text-right">' + formatMoney(it.price_old, 4) + '</td>'
                          + '<td class="text-right">' + formatMoney(it.price_new, 4) + '</td>'
                          + '<td>' + statusBadge + '</td>'
                          + '<td class="text-left">' + escapeHtml(it.keterangan || '-') + '</td>'
                          + '</tr>';
                  });

                  html += '</tbody></table>';
                  $('#details').html(html);
              },
              error: function () {
                  $('#details').html('<div class="text-center p-3 text-danger">Failed to load detail</div>');
              }
          });
      });

      // Cancel a document (revert its Revisi Barcode changes)
      $('#table-data').on('click', '.btn-cancel', function () {
          const noDok = this.dataset.no;

          Swal.fire({
              title: 'Cancel ' + noDok + '?',
              text: 'Nilai Revisi Barcode yang diubah oleh dokumen ini akan dikembalikan ke nilai sebelumnya.',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#f0ad4e',
              cancelButtonColor: '#aaa',
              confirmButtonText: 'Yes, Cancel'
          }).then((result) => {
              if (!result.isConfirmed) return;

              $.ajax({
                  type: 'POST',
                  url: 'cancel_update_np_revisi.php',
                  data: { no_dok: noDok },
                  dataType: 'json',
                  success: function (res) {
                      if (res.success) {
                          Swal.fire({ icon: 'success', title: 'Dibatalkan', timer: 1500, showConfirmButton: false });
                          datatable.ajax.reload();
                      } else {
                          Swal.fire({ icon: 'error', title: 'Gagal!', text: res.message || 'Terjadi kesalahan' });
                      }
                  },
                  error: function () {
                      Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal membatalkan dokumen' });
                  }
              });
          });
      });
  });
</script>

</body>

</html>
