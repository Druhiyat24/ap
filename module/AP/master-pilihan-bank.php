<?php include '../header.php'; ?>

<style>
  .table-gradient th {
    background: #1E3A8A; color: #fff;
    text-align: center; vertical-align: middle; white-space: nowrap;
  }
  #tbl-pilbank td { vertical-align: middle; font-size: 13px; }
</style>

<div class="container-fluid mt-4 p-4">

  <!-- Card Header -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
         style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fa fa-university mr-1"></i> MASTER BANK CHOICE</h5>
    </div>
    <div class="card-body p-3">
      <div class="row align-items-end">

        <!-- Filter Category -->
        <div class="col-md-3">
          <label class="mb-1" style="font-size:12px; font-weight:600;">Bank Category</label>
          <select class="form-control form-control-sm selectpicker" id="filter-kategori"
                  data-dropup-auto="false">
            <option value="">All Categories</option>
            <?php
            $sql_kat = mysqli_query($conn2,
                "SELECT DISTINCT katergori_bank FROM master_pilihan_bank
                 WHERE katergori_bank IS NOT NULL AND TRIM(katergori_bank) != ''
                 ORDER BY katergori_bank ASC"
            );
            while ($k = mysqli_fetch_assoc($sql_kat)):
            ?>
              <option value="<?= htmlspecialchars($k['katergori_bank']) ?>">
                <?= htmlspecialchars($k['katergori_bank']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <!-- Filter Status -->
        <div class="col-md-2">
          <label class="mb-1" style="font-size:12px; font-weight:600;">Status</label>
          <select class="form-control form-control-sm selectpicker" id="filter-status"
                  data-dropup-auto="false">
            <option value="">All Status</option>
            <option value="Y">Active</option>
            <option value="N">Inactive</option>
          </select>
        </div>

        <!-- Buttons -->
        <div class="col-md-auto d-flex" style="gap:6px;">
          <button type="button" class="btn btn-info btn-sm" onclick="dtReload()">
            <i class="fa fa-search"></i> Search
          </button>
          <button type="button" class="btn btn-primary btn-sm" onclick="openCreate()">
            <i class="fa fa-plus"></i> Create
          </button>
          <a id="btn-export" href="ekspor_master_pilihan_bank.php" target="_blank">
            <button type="button" class="btn btn-success btn-sm">
              <i class="fa fa-file-excel-o"></i> Export Excel
            </button>
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Card Table -->
  <div class="card shadow border-0 mt-3">
    <div class="card-body p-3">
      <div class="table-responsive">
        <table id="tbl-pilbank"
               class="table table-striped table-bordered table-hover table-sm"
               style="width:100%">
          <thead class="table-gradient">
            <tr>
              <th>No</th>
              <th>Bank Category</th>
              <th>Bank Name</th>
              <th>Bank Code</th>
              <th>Swift Code</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- ===== MODAL CREATE / EDIT ===== -->
<div class="modal fade" id="modalPilBank" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header text-white py-2 px-3"
           style="background: linear-gradient(90deg,#191970,#1e90ff); border-radius:4px 4px 0 0;">
        <h5 class="modal-title mb-0" id="modal-pb-title">
          <i class="fa fa-plus-circle mr-1"></i> Create Bank
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"
                style="opacity:1;">&times;</button>
      </div>

      <div class="modal-body p-4">
        <input type="hidden" id="pb-id">

        <div class="form-group row mb-2">
          <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
            Bank Category <span class="text-danger">*</span>
          </label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm"
                   id="pb-kategori" list="pb-kategori-list" placeholder="e.g. BUMN, SWASTA, BPD"
                   maxlength="255">
            <datalist id="pb-kategori-list">
              <?php
              $sql_kat2 = mysqli_query($conn2,
                  "SELECT DISTINCT katergori_bank FROM master_pilihan_bank
                   WHERE katergori_bank IS NOT NULL AND TRIM(katergori_bank) != ''
                   ORDER BY katergori_bank ASC"
              );
              while ($k2 = mysqli_fetch_assoc($sql_kat2)):
              ?>
                <option value="<?= htmlspecialchars($k2['katergori_bank']) ?>">
              <?php endwhile; ?>
            </datalist>
          </div>
        </div>

        <div class="form-group row mb-2">
          <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
            Bank Name <span class="text-danger">*</span>
          </label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm"
                   id="pb-nama" placeholder="Bank name" maxlength="255">
          </div>
        </div>

        <div class="form-group row mb-2">
          <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
            Bank Code
          </label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm"
                   id="pb-kode" placeholder="e.g. 014" maxlength="255">
          </div>
        </div>

        <div class="form-group row mb-2">
          <label class="col-sm-4 col-form-label col-form-label-sm font-weight-bold">
            Swift Code
          </label>
          <div class="col-sm-8">
            <input type="text" class="form-control form-control-sm text-uppercase"
                   id="pb-swift" placeholder="e.g. BNINIDJA" maxlength="255">
          </div>
        </div>
      </div>

      <div class="modal-footer py-2" style="background:#f8f9fa;">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Cancel
        </button>
        <button type="button" class="btn btn-sm btn-primary" onclick="savePilBank()">
          <i class="fa fa-save"></i> Save
        </button>
      </div>

    </div>
  </div>
</div>

<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../css/4.1.1/datatables.min.js"></script>
<script src="../css/4.1.1/bootstrap-select.min.js"></script>
<script src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  // Sidebar collapse
  $('#body-row .collapse').collapse('hide');
  $('#collapse-icon').addClass('fa-angle-double-left');
  $('[data-toggle=sidebar-colapse]').click(function () { SidebarCollapse(); });
  function SidebarCollapse() {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
    var sep = $('.sidebar-separator-title');
    sep.hasClass('d-flex') ? sep.removeClass('d-flex') : sep.addClass('d-flex');
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }

  // SelectPicker init
  $(function () { $('.selectpicker').selectpicker(); });

  // ===== Search / reload =====
  function dtReload() {
    updateExportLink();
    dt.ajax.reload();
  }

  // ===== Update export link =====
  function updateExportLink() {
    var kategori = $('#filter-kategori').val();
    var status   = $('#filter-status').val();
    $('#btn-export').attr('href',
      'ekspor_master_pilihan_bank.php?filter_kategori=' + encodeURIComponent(kategori)
      + '&filter_status=' + encodeURIComponent(status)
    );
  }

  // Init export link
  $(function () { updateExportLink(); });

  // ===== DataTable =====
  var dt = $('#tbl-pilbank').DataTable({
    processing  : true,
    serverSide  : true,
    ordering    : true,
    searching   : true,
    autoWidth   : false,
    ajax: {
      url : 'ajax_master_pilihan_bank.php',
      type: 'POST',
      data: function (d) {
        d.filter_kategori = $('#filter-kategori').val();
        d.filter_status   = $('#filter-status').val();
      }
    },
    columns: [
      { data: 'rownum',         className: 'text-center', orderable: false },
      { data: 'katergori_bank'  },
      { data: 'nama_bank'       },
      { data: 'kode_bank',      className: 'text-center' },
      { data: 'swift_code',     className: 'text-center' },
      {
        data: 'status',
        className: 'text-center',
        render: function (d) {
          var cls = d === 'Y' ? 'success' : 'secondary';
          var txt = d === 'Y' ? 'Active' : 'Inactive';
          return '<span class="badge badge-' + cls + '">' + txt + '</span>';
        }
      },
      {
        data: null, orderable: false, searchable: false,
        className: 'text-center',
        render: function (d, t, row) {
          var edit = '<button class="btn btn-xs btn-warning mr-1" onclick="openEdit(' + row.id + ')">'
                   + '<i class="fa fa-pencil"></i> Edit</button>';
          if (row.status === 'Y') {
            return edit + '<button class="btn btn-xs btn-danger" onclick="doToggle(' + row.id + ',\'' + row.nama_bank.replace(/'/g, "\\'") + '\')">'
                 + '<i class="fa fa-ban"></i> Deactivate</button>';
          }
          return edit + '<button class="btn btn-xs btn-success" onclick="doToggle(' + row.id + ',\'' + row.nama_bank.replace(/'/g, "\\'") + '\')">'
               + '<i class="fa fa-check"></i> Activate</button>';
        }
      }
    ]
  });

  // ===== OPEN CREATE =====
  function openCreate() {
    $('#modal-pb-title').html('<i class="fa fa-plus-circle mr-1"></i> Create Bank');
    $('#pb-id').val('');
    $('#pb-kategori, #pb-nama, #pb-kode, #pb-swift').val('');
    $('#modalPilBank').modal('show');
  }

  // ===== OPEN EDIT =====
  function openEdit(id) {
    $.ajax({
      url: 'ajax_master_pilihan_bank.php',
      method: 'POST',
      data: { action: 'get_one', id: id },
      dataType: 'json',
      success: function (r) {
        if (!r) { Swal.fire('Error', 'Data not found.', 'error'); return; }
        $('#modal-pb-title').html('<i class="fa fa-pencil mr-1"></i> Edit Bank');
        $('#pb-id').val(r.id);
        $('#pb-kategori').val(r.katergori_bank);
        $('#pb-nama').val(r.nama_bank);
        $('#pb-kode').val(r.kode_bank);
        $('#pb-swift').val(r.swift_code);
        $('#modalPilBank').modal('show');
      },
      error: function () { Swal.fire('Error', 'Failed to fetch data.', 'error'); }
    });
  }

  // ===== SAVE (Create / Edit) =====
  function savePilBank() {
    var id        = $('#pb-id').val();
    var kategori  = $('#pb-kategori').val().trim();
    var nama      = $('#pb-nama').val().trim();
    var kode      = $('#pb-kode').val().trim();
    var swift     = $('#pb-swift').val().trim();

    if (!kategori || !nama) {
      Swal.fire('Notice', 'Bank Category and Bank Name are required.', 'warning');
      return;
    }

    $.ajax({
      url    : 'save_master_pilihan_bank.php',
      method : 'POST',
      data   : {
        id              : id,
        katergori_bank  : kategori,
        nama_bank       : nama,
        kode_bank       : kode,
        swift_code      : swift
      },
      dataType: 'json',
      success: function (r) {
        if (r.status === 'success') {
          $('#modalPilBank').modal('hide');
          Swal.fire({ icon: 'success', title: 'Success!',
                      text: id ? 'Data updated successfully.' : 'Data saved successfully.',
                      timer: 1800, showConfirmButton: false });
          dt.ajax.reload(null, false);
        } else {
          Swal.fire({ icon: 'error', title: 'Failed', html: r.message || 'An error occurred.' });
        }
      },
      error: function () { Swal.fire('Error', 'Failed to contact the server.', 'error'); }
    });
  }

  // ===== TOGGLE STATUS =====
  function doToggle(id, name) {
    Swal.fire({
      title: 'Confirmation',
      html : 'Change status for bank <b>' + name + '</b>?',
      icon : 'warning',
      showCancelButton   : true,
      confirmButtonColor : '#dc3545',
      cancelButtonColor  : '#6c757d',
      confirmButtonText  : '<i class="fa fa-check"></i> Yes, change it!',
      cancelButtonText   : 'No'
    }).then(function (res) {
      if (!res.isConfirmed) return;
      $.ajax({
        url    : 'toggle_master_pilihan_bank.php',
        method : 'POST',
        data   : { id: id },
        dataType: 'json',
        success: function (r) {
          if (r.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Status updated successfully.',
                        timer: 1500, showConfirmButton: false });
            dt.ajax.reload(null, false);
          } else {
            Swal.fire('Failed', r.message || 'An error occurred.', 'error');
          }
        },
        error: function () { Swal.fire('Error', 'Failed to contact the server.', 'error'); }
      });
    });
  }
</script>

</body>
</html>
