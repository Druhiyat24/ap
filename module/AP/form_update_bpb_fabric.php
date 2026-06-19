<?php include '../header.php' ?>

<?php
mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS update_bpb_fabric_h (
  id INT(11) NOT NULL AUTO_INCREMENT,
  no_pengajuan VARCHAR(30) NOT NULL,
  tgl_pengajuan DATE NOT NULL,
  nama_supp VARCHAR(100) DEFAULT NULL,
  deskripsi VARCHAR(255) DEFAULT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Draft',
  created_by VARCHAR(50) DEFAULT NULL,
  created_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_no_pengajuan (no_pengajuan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn1, "CREATE TABLE IF NOT EXISTS update_bpb_fabric (
  id INT(11) NOT NULL AUTO_INCREMENT,
  no_pengajuan VARCHAR(30) NOT NULL,
  no_bpb VARCHAR(50) NOT NULL,
  tgl_bpb DATE DEFAULT NULL,
  nama_supp VARCHAR(100) DEFAULT NULL,
  no_po VARCHAR(50) DEFAULT NULL,
  id_det VARCHAR(50) DEFAULT NULL,
  no_ws VARCHAR(50) DEFAULT NULL,
  id_jo VARCHAR(50) DEFAULT NULL,
  id_item VARCHAR(50) DEFAULT NULL,
  desc_item VARCHAR(150) DEFAULT NULL,
  qty DECIMAL(15,2) DEFAULT 0,
  unit VARCHAR(20) DEFAULT NULL,
  curr VARCHAR(10) DEFAULT NULL,
  price_old DECIMAL(18,4) DEFAULT 0,
  price_new DECIMAL(18,4) DEFAULT 0,
  ppn_old DECIMAL(8,2) DEFAULT 0,
  ppn_new DECIMAL(8,2) DEFAULT 0,
  created_by VARCHAR(50) DEFAULT NULL,
  created_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_no_pengajuan (no_pengajuan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn1, "ALTER TABLE update_bpb_fabric ADD COLUMN IF NOT EXISTS id_jo VARCHAR(50) DEFAULT NULL AFTER no_ws");

// Generate next transaction number: UPD/GK/MMYY/00001
$prefix = 'UPD/GK/' . date('my') . '/';
$cek = mysqli_query($conn1, "SELECT MAX(CAST(SUBSTRING(no_pengajuan,13) AS UNSIGNED)) mx FROM update_bpb_fabric_h WHERE no_pengajuan LIKE '$prefix%'");
$row_cek = mysqli_fetch_assoc($cek);
$next_no = (!empty($row_cek['mx'])) ? ((int) $row_cek['mx'] + 1) : 1;
$no_pengajuan = $prefix . str_pad($next_no, 5, '0', STR_PAD_LEFT);
?>

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

    #table-bpb tbody tr:hover,
    #table-selected tbody tr:hover {
        background-color: #f5faff;
    }

    #table-bpb tbody tr.bpb-row-ok {
        background-color: #d4edda;
    }
    #table-bpb tbody tr.bpb-row-diff {
        background-color: #f8d7da;
    }

    .btn-edit-items, .btn-remove-item, #btnSearchBpb, #btnSave {
        transition: transform .15s ease-in-out;
    }
    .btn-edit-items:hover, .btn-remove-item:hover, #btnSearchBpb:hover, #btnSave:hover {
        transform: translateY(-1px);
    }

    .price-new-input, .ppn-new-input {
        font-size: 13px;
        text-align: right;
    }

    .empty-placeholder {
        text-align: center;
        color: #888;
        padding: 24px 0;
    }

    @media (min-width: 992px) {
        #modalDetail .modal-dialog {
            max-width: 75%;
        }
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">

  <!-- Header Card -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fas fa-edit" aria-hidden="true"></i> EDIT REQUEST - BPB FABRIC</h5>
    </div>
    <div class="card-body p-3">
      <form id="form-header">
        <div class="row">
          <div class="col-md-2 mb-2">
            <label for="no_pengajuan"><b>Transaction No</b></label>
            <input type="text" class="form-control form-control-sm" id="no_pengajuan" value="<?= htmlspecialchars($no_pengajuan) ?>" readonly>
          </div>
          <div class="col-md-2 mb-2">
            <label for="tgl_pengajuan"><b>Transaction Date</b></label>
            <input type="text" class="form-control form-control-sm" id="tgl_pengajuan" value="<?= date('Y-m-d') ?>" readonly>
          </div>
          <div class="col-md-4 mb-2">
            <label for="deskripsi"><b>Description</b> <span class="text-danger">*</span></label>
            <textarea class="form-control form-control-sm" id="deskripsi" rows="1" placeholder="e.g. price correction for fabric receiving..." required></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2 mb-2">
            <label for="start_date"><b>BPB Date From</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="start_date" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
          </div>
          <div class="col-md-2 mb-2">
            <label for="end_date"><b>BPB Date To</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="end_date" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
          </div>
          <div class="col-md-3 mb-2">
            <label for="nama_supp"><b>Supplier</b></label>
            <select class="form-control form-control-sm selectpicker" id="nama_supp" data-dropup-auto="false" data-live-search="true">
              <option value="ALL" selected>ALL</option>
              <?php
              $sql = mysqli_query($conn1, "select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
              while ($row = mysqli_fetch_array($sql)) {
                  $data = $row['Supplier'];
                  echo '<option value="' . htmlspecialchars($data) . '">' . htmlspecialchars($data) . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="col-md-2 mb-2 d-flex align-items-end">
            <button type="button" id="btnSearchBpb" class="btn btn-info btn-sm">
              <i class="fa fa-search"></i> Search BPB
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- BPB List Card -->
  <div class="card shadow border-0 mt-4">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h6 class="mb-0"><i class="fas fa-list" aria-hidden="true"></i> BPB List</h6>
    </div>
    <div class="card-body p-3">
      <div class="table-responsive">
        <table id="table-bpb" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
              <th>No BPB</th>
              <th>BPB Date</th>
              <th>Supplier</th>
              <th>No PO</th>
              <th>Curr</th>
              <th>Qty</th>
              <th>DPP</th>
              <th>PPN</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Selected Items Card -->
  <div class="card shadow border-0 mt-4">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h6 class="mb-0"><i class="fas fa-pen-square" aria-hidden="true"></i> Items to Update</h6>
    </div>
    <div class="card-body p-3">
      <div class="table-responsive">
        <table id="table-selected" class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
              <th class="text-center">No BPB</th>
              <th class="text-center">No WS</th>
              <th class="text-left">Item</th>
              <th class="text-right">Qty</th>
              <th class="text-center">Unit</th>
              <th class="text-center">Curr</th>
              <th class="text-right">Price (Old)</th>
              <th class="text-right">Price (New)</th>
              <th class="text-right">PPN % (Old)</th>
              <th class="text-right">PPN % (New)</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr id="row-empty-selected"><td colspan="11" class="empty-placeholder">No items added yet</td></tr>
          </tbody>
        </table>
      </div>
      <div class="text-right mt-3">
        <button type="button" class="btn btn-danger btn-sm mr-2" onclick="location.href='update-bpb-fabric.php'">
          <i class="fa fa-angle-double-left"></i> Back
        </button>
        <button type="button" id="btnSave" class="btn btn-success btn-sm">
          <i class="fas fa-save"></i> Save Request
        </button>
      </div>
    </div>
  </div>

</div>

<!-- Modal: per WS / per item editor -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="modal-title" id="modalDetailLabel"><i class="fas fa-boxes"></i> Items - <span id="modalBpbLabel"></span></h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-hidden="true"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="table-modal-detail" class="table table-bordered table-striped table-sm">
            <thead class="table-gradient text-white">
              <tr>
                <th class="text-center" style="width:30px;"><input type="checkbox" id="checkAllModal"></th>
                <th class="text-center">No WS</th>
                <th class="text-left">Item</th>
                <th class="text-right">Qty</th>
                <th class="text-center">Unit</th>
                <th class="text-center">Curr</th>
                <th class="text-right">Price (Current)</th>
                <th class="text-right">New Price</th>
                <th class="text-right">PPN % (Current)</th>
                <th class="text-right">New PPN %</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="10" class="empty-placeholder"><i class="fas fa-spinner fa-spin"></i></td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="btnAddSelected" class="btn btn-primary">
          <i class="fa fa-plus"></i> Add Selected
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
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

  $(function() {
      $('.selectpicker').selectpicker();
  });
</script>

<script>
  function escapeHtml(str) {
      return String(str).replace(/[&<>"']/g, function (ch) {
          return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
      });
  }

  function formatMoney(amount, decimalCount = 2) {
      const val = parseFloat(amount);
      if (isNaN(val)) return '0';
      return val.toLocaleString('en-US', {
          minimumFractionDigits: 0,
          maximumFractionDigits: decimalCount
      });
  }

  function toYmd(dmy) {
      if (!dmy) return '';
      let p = dmy.split('-'); // [dd, mm, yyyy]
      return `${p[2]}-${p[1]}-${p[0]}`;
  }

  let bpbTable;

  $(document).ready(function () {
      $('.tanggal').datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true
      });

      bpbTable = $('#table-bpb').DataTable({
          paging: true,
          searching: true,
          info: true,
          ordering: false,
          autoWidth: false,
          columnDefs: [
              { targets: [4], className: 'text-center' },
              { targets: [5, 6, 7, 8], className: 'text-right' },
              { targets: [10], visible: false }
          ],
          createdRow: function (row, data) {
              $(row).addClass(data[10] === '1' ? 'bpb-row-ok' : 'bpb-row-diff');
          },
          language: { emptyTable: 'Click "Search BPB" to load data' }
      });

      $('#btnSearchBpb').on('click', function () {
          const nama_supp = $('#nama_supp').val();
          const start_date = toYmd($('#start_date').val());
          const end_date = toYmd($('#end_date').val());

          bpbTable.clear().draw();

          $.ajax({
              type: 'POST',
              url: 'cari_data_bpb_fabric_edit.php',
              data: { nama_supp: nama_supp, start_date: start_date, end_date: end_date },
              dataType: 'json',
              success: function (res) {
                  res.forEach(function (r) {
                      bpbTable.row.add([
                          escapeHtml(r.no_dok),
                          r.tgl_dok_fmt,
                          escapeHtml(r.supplier),
                          escapeHtml(r.no_po),
                          escapeHtml(r.curr || '-'),
                          formatMoney(r.qty),
                          formatMoney(r.dpp),
                          formatMoney(r.ppn),
                          formatMoney(r.total),
                          (r.is_match ? '' : '<small class="text-danger d-block mb-1"><i class="fas fa-exclamation-triangle"></i> Price/PPN differs from PO</small>')
                              + '<button type="button" class="btn btn-sm btn-outline-primary btn-edit-items" '
                              + 'data-no-bpb="' + escapeHtml(r.no_dok) + '" '
                              + 'data-tgl-bpb="' + r.tgl_dok_fmt + '" '
                              + 'data-supplier="' + escapeHtml(r.supplier) + '" '
                              + 'data-no-po="' + escapeHtml(r.no_po) + '">'
                              + '<i class="fas fa-pen"></i> Edit Items</button>',
                          r.is_match ? '1' : '0'
                      ]);
                  });
                  bpbTable.draw();
              },
              error: function () {
                  Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load BPB data' });
              }
          });
      });

      // Open detail modal
      $('#table-bpb tbody').on('click', '.btn-edit-items', function () {
          const noBpb    = this.dataset.noBpb;
          const tglBpb   = this.dataset.tglBpb;
          const supplier = this.dataset.supplier;
          const noPo     = this.dataset.noPo;
          // alert(noBpb);

          $('#modalDetail').data({ noBpb, tglBpb, supplier, noPo });
          $('#modalBpbLabel').text(noBpb + ' (' + tglBpb + ')');
          $('#table-modal-detail tbody').html('<tr><td colspan="10" class="empty-placeholder"><i class="fas fa-spinner fa-spin"></i></td></tr>');
          $('#modalDetail').modal('show');

          $.ajax({
              type: 'GET',
              url: 'get_detail_bpb_fabric_edit.php',
              data: { no_bpb: noBpb },
              dataType: 'json',
              success: function (res) {
                  const ppn = parseFloat(res.ppn) || 0;
                  const tbody = $('#table-modal-detail tbody');
                  tbody.empty();

                  if (!res.items.length) {
                      tbody.html('<tr><td colspan="10" class="empty-placeholder">No items found</td></tr>');
                      return;
                  }

                  res.items.forEach(function (item) {
                      const currentPrice = item.price;
                      const row = $('<tr>');
                      const isLocked = !!item.is_locked;

                      row.attr({
                          'data-id-det': item.id,
                          'data-no-ws': item.no_ws,
                          'data-id-jo': item.id_jo,
                          'data-id-item': item.id_item,
                          'data-desc-item': item.desc_item,
                          'data-qty': item.qty,
                          'data-unit': item.unit,
                          'data-curr': item.curr,
                          'data-price-old': currentPrice,
                          'data-ppn-old': ppn
                      });

                      if (isLocked) {
                          row.addClass('table-secondary').attr('title', 'Already matches PO - no edit needed');
                      }

                      const disabledAttr = isLocked ? ' disabled' : '';

                      row.html(
                          '<td class="text-center"><input type="checkbox" class="chk-modal-row"' + disabledAttr + '></td>'
                          + '<td class="text-center">' + escapeHtml(item.no_ws || '-') + '</td>'
                          + '<td class="text-left">' + escapeHtml(item.desc_item || item.id_item) + '</td>'
                          + '<td class="text-right">' + formatMoney(item.qty) + '</td>'
                          + '<td class="text-center">' + escapeHtml(item.unit || '-') + '</td>'
                          + '<td class="text-center">' + escapeHtml(item.curr || '-') + '</td>'
                          + '<td class="text-right">' + formatMoney(currentPrice, 2) + '</td>'
                          + '<td class="text-right"><input type="number" step="0.0001" class="form-control form-control-sm price-new-input" value="' + currentPrice + '"' + disabledAttr + '></td>'
                          + '<td class="text-right">' + formatMoney(ppn) + '</td>'
                          + '<td class="text-right"><select class="form-control form-control-sm ppn-new-input"' + disabledAttr + '>'
                              + '<option value="11"' + (ppn === 11 ? ' selected' : '') + '>11%</option>'
                              + '<option value="0"' + (ppn !== 11 ? ' selected' : '') + '>Non PPN</option>'
                              + '</select></td>'
                      );

                      tbody.append(row);
                  });
              },
              error: function () {
                  $('#table-modal-detail tbody').html('<tr><td colspan="10" class="empty-placeholder text-danger">Failed to load items</td></tr>');
              }
          });
      });

      // Check all rows in modal
      $('#checkAllModal').on('change', function () {
          $('#table-modal-detail tbody .chk-modal-row:not(:disabled)').prop('checked', this.checked);
      });

      // Add selected items from modal into the "Items to Update" table
      $('#btnAddSelected').on('click', function () {
          const modalData = $('#modalDetail').data();
          const checked = $('#table-modal-detail tbody .chk-modal-row:checked');

          if (checked.length === 0) {
              Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please select at least one item' });
              return;
          }

          checked.each(function () {
              const tr = $(this).closest('tr');
              const idDet = tr.data('id-det');

              const priceNew = parseFloat(tr.find('.price-new-input').val()) || 0;
              const ppnNew   = parseFloat(tr.find('.ppn-new-input').val()) || 0;

              const item = {
                  id_det: idDet,
                  no_bpb: modalData.noBpb,
                  tgl_bpb: modalData.tglBpb,
                  nama_supp: modalData.supplier,
                  no_po: modalData.noPo,
                  no_ws: tr.data('no-ws'),
                  id_jo: tr.data('id-jo'),
                  id_item: tr.data('id-item'),
                  desc_item: tr.data('desc-item'),
                  qty: tr.data('qty'),
                  unit: tr.data('unit'),
                  curr: tr.data('curr'),
                  price_old: tr.data('price-old'),
                  price_new: priceNew,
                  ppn_old: tr.data('ppn-old'),
                  ppn_new: ppnNew
              };

              addOrUpdateSelectedItem(item);
          });

          $('#modalDetail').modal('hide');
      });

      function addOrUpdateSelectedItem(item) {
          $('#row-empty-selected').remove();

          const rowKey = item.no_bpb + '|' + item.id_det;
          let existing = $('#table-selected tbody tr[data-row-key="' + rowKey + '"]');

          const rowHtml = ''
              + '<td class="text-center">' + escapeHtml(item.no_bpb) + '</td>'
              + '<td class="text-center">' + escapeHtml(item.no_ws || '-') + '</td>'
              + '<td class="text-left">' + escapeHtml(item.desc_item || item.id_item) + '</td>'
              + '<td class="text-right">' + formatMoney(item.qty) + '</td>'
              + '<td class="text-center">' + escapeHtml(item.unit || '-') + '</td>'
              + '<td class="text-center">' + escapeHtml(item.curr || '-') + '</td>'
              + '<td class="text-right">' + formatMoney(item.price_old, 4) + '</td>'
              + '<td class="text-right">' + formatMoney(item.price_new, 4) + '</td>'
              + '<td class="text-right">' + formatMoney(item.ppn_old) + '</td>'
              + '<td class="text-right">' + formatMoney(item.ppn_new) + '</td>'
              + '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"><i class="fas fa-trash"></i></button></td>';

          if (existing.length) {
              existing.attr('data-item', JSON.stringify(item)).html(rowHtml);
          } else {
              const tr = $('<tr>').attr({ 'data-row-key': rowKey, 'data-item': JSON.stringify(item) }).html(rowHtml);
              $('#table-selected tbody').append(tr);
          }
      }

      // Remove item from selected list
      $('#table-selected tbody').on('click', '.btn-remove-item', function () {
          $(this).closest('tr').remove();
          if ($('#table-selected tbody tr').length === 0) {
              $('#table-selected tbody').html('<tr id="row-empty-selected"><td colspan="10" class="empty-placeholder">No items added yet</td></tr>');
          }
      });

      // Save the edit request
      $('#btnSave').on('click', function () {
          const deskripsi = $('#deskripsi').val().trim();

          if (!deskripsi) {
              Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please fill in the Description' });
              $('#deskripsi').focus();
              return;
          }

          const items = [];
          $('#table-selected tbody tr[data-item]').each(function () {
              items.push(JSON.parse($(this).attr('data-item')));
          });

          if (items.length === 0) {
              Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please add at least one item to update' });
              return;
          }

          Swal.fire({
              title: 'Save this edit request?',
              text: 'This request will be saved as Draft.',
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#1e90ff',
              cancelButtonColor: '#aaa',
              confirmButtonText: 'Yes, Save'
          }).then((result) => {
              if (!result.isConfirmed) return;

              $.ajax({
                  type: 'POST',
                  url: 'insert_update_bpb_fabric.php',
                  data: {
                      no_pengajuan: $('#no_pengajuan').val(),
                      tgl_pengajuan: $('#tgl_pengajuan').val(),
                      deskripsi: $('#deskripsi').val(),
                      nama_supp: $('#nama_supp').val(),
                      created_by: '<?php echo $user ?>',
                      items: JSON.stringify(items)
                  },
                  dataType: 'json',
                  success: function (res) {
                      if (res.success) {
                          Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: 'Edit request ' + res.no_pengajuan + ' saved successfully',
                              timer: 1800,
                              showConfirmButton: false
                          }).then(() => {
                              window.location = 'update-bpb-fabric.php';
                          });
                      } else {
                          Swal.fire({ icon: 'error', title: 'Failed!', text: res.message || 'An error occurred' });
                      }
                  },
                  error: function () {
                      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save edit request' });
                  }
              });
          });
      });
  });
</script>

</body>

</html>
