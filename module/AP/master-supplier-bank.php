<?php include '../header.php'; ?>

<style>
  .table-gradient th {
    background: #1E3A8A; color: #fff;
    text-align: center; vertical-align: middle; white-space: nowrap;
  }
  #tbl-supbank td { vertical-align: middle; font-size: 13px; }
</style>

<div class="container-fluid mt-4 p-4">

  <!-- Card Header -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
         style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fa fa-university mr-1"></i> MASTER BANK SUPPLIER - CUSTOMER</h5>
    </div>
    <div class="card-body p-3">
      <div class="row align-items-end">

        <!-- Filter Type -->
        <div class="col-md-2">
          <label class="mb-1" style="font-size:12px; font-weight:600;">Type</label>
          <select class="form-control form-control-sm selectpicker" id="filter-type"
                  data-dropup-auto="false" onchange="loadFilterName()">
            <option value="">All Type</option>
            <option value="S">Supplier</option>
            <option value="C">Customer</option>
          </select>
        </div>

        <!-- Filter Name -->
        <div class="col-md-3">
          <label class="mb-1" style="font-size:12px; font-weight:600;">Name</label>
          <select class="form-control form-control-sm selectpicker" id="filter-name"
                  data-live-search="true" data-dropup-auto="false" data-size="8">
            <option value="">All Name</option>
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
          <a id="btn-export" href="ekspor_master_supplier_bank.php" target="_blank">
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
        <table id="tbl-supbank"
               class="table table-striped table-bordered table-hover table-sm"
               style="width:100%">
          <thead class="table-gradient">
            <tr>
              <th>No</th>
              <th>Supplier / Customer</th>
              <th>Bank Currency</th>
              <th>Bank Name</th>
              <th>Bank Account</th>
              <th>Beneficiary Name</th>
              <th>Status</th>
              <th>Created By</th>
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
<div class="modal fade" id="modalSupBank" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header text-white py-2 px-3"
           style="background: linear-gradient(90deg,#191970,#1e90ff); border-radius:4px 4px 0 0;">
        <h5 class="modal-title mb-0" id="modal-sup-title">
          <i class="fa fa-plus-circle mr-1"></i> Create Supplier Bank
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"
                style="opacity:1;">&times;</button>
      </div>

      <div class="modal-body p-4">
        <input type="hidden" id="sb-id">

        <!-- Type: Supplier / Customer -->
        <div class="form-group row mb-2">
          <label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
            Type <span class="text-danger">*</span>
          </label>
          <div class="col-sm-9">
            <select class="form-control form-control-sm selectpicker" id="sb-type" onchange="loadNameByType()">
              <option value="">-- Select Type --</option>
              <option value="S">Supplier</option>
              <option value="C">Customer</option>
            </select>
          </div>
        </div>

        <!-- Name (diisi setelah pilih Type) -->
        <div class="form-group row mb-2">
          <label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
            Name <span class="text-danger">*</span>
          </label>
          <div class="col-sm-9">
            <select class="form-control form-control-sm selectpicker"
                    id="sb-supplier" data-live-search="true"
                    data-dropup-auto="false" data-size="8" disabled>
              <option value="">-- Select Type first --</option>
            </select>
          </div>
        </div>

        <div class="form-group row mb-2">
          <label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
            Bank Currency <span class="text-danger">*</span>
          </label>
          <div class="col-sm-9">
            <select class="form-control form-control-sm selectpicker"
                    id="sb-currency" data-live-search="true"
                    data-dropup-auto="false" data-size="8">
              <option value="">-- Select Currency --</option>
              <?php
              $sql_cur = mysqli_query($conn1,
                "SELECT nama_pilihan FROM whs_master_pilihan WHERE type_pilihan = 'currency' ORDER BY nama_pilihan ASC"
              );
              while ($cur = mysqli_fetch_assoc($sql_cur)):
              ?>
                <option value="<?= htmlspecialchars($cur['nama_pilihan']) ?>">
                  <?= htmlspecialchars($cur['nama_pilihan']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
        </div>

        <div class="form-group row mb-2">
          <label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
            Bank Name <span class="text-danger">*</span>
          </label>
          <div class="col-sm-9">
            <input type="text" class="form-control form-control-sm"
                   id="sb-bankname" placeholder="Bank name" maxlength="200">
          </div>
        </div>

        <div class="form-group row mb-2">
          <label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
            Bank Account <span class="text-danger">*</span>
          </label>
          <div class="col-sm-9">
            <input type="text" class="form-control form-control-sm"
                   id="sb-account" placeholder="Account number" maxlength="100">
          </div>
        </div>

        <div class="form-group row mb-2">
          <label class="col-sm-3 col-form-label col-form-label-sm font-weight-bold">
            Beneficiary Name <span class="text-danger">*</span>
          </label>
          <div class="col-sm-9">
            <input type="text" class="form-control form-control-sm"
                   id="sb-beneficiary" placeholder="Beneficiary name" maxlength="200">
          </div>
        </div>
      </div>

      <div class="modal-footer py-2" style="background:#f8f9fa;">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Batal
        </button>
        <button type="button" class="btn btn-sm btn-primary" onclick="saveSupBank()">
          <i class="fa fa-save"></i> Simpan
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

  // ===== Filter: load Name by Type =====
  function loadFilterName() {
    var type = $('#filter-type').val();
    var $sel = $('#filter-name');
    $sel.html('<option value="">All Name</option>').selectpicker('refresh');
    if (!type) return;

    $.ajax({
      url: 'ajax_master_supplier_bank.php', method: 'POST',
      data: { action: 'get_supplier_list', type: type },
      dataType: 'json',
      success: function (data) {
        var opts = '<option value="">All Name</option>';
        $.each(data, function (i, d) {
          opts += '<option value="' + d.id_supplier + '">' + d.Supplier + '</option>';
        });
        $sel.html(opts).selectpicker('refresh');
      }
    });
  }

  // ===== Search / reload =====
  function dtReload() {
    updateExportLink();
    dt.ajax.reload();
  }

  // ===== Update export link =====
  function updateExportLink() {
    var type     = $('#filter-type').val();
    var supplier = $('#filter-name').val();
    $('#btn-export').attr('href',
      'ekspor_master_supplier_bank.php?filter_type=' + encodeURIComponent(type)
      + '&filter_supplier=' + encodeURIComponent(supplier)
    );
  }

  // Init export link
  $(function () { updateExportLink(); });

  // ===== Load Name by Type =====
  function loadNameByType() {
    var type = $('#sb-type').val();
    var $sel = $('#sb-supplier');

    $sel.prop('disabled', true).html('<option value="">Loading...</option>');
    $sel.selectpicker('refresh');

    if (!type) {
      $sel.html('<option value="">-- Select Type first --</option>');
      $sel.selectpicker('refresh');
      return;
    }

    $.ajax({
      url     : 'ajax_master_supplier_bank.php',
      method  : 'POST',
      data    : { action: 'get_supplier_list', type: type },
      dataType: 'json',
      success : function (data) {
        var opts = '<option value="">-- Select Name --</option>';
        $.each(data, function (i, d) {
          opts += '<option value="' + d.id_supplier + '">' + d.Supplier + '</option>';
        });
        $sel.html(opts).prop('disabled', false);
        $sel.selectpicker('refresh');
      },
      error: function () {
        $sel.html('<option value="">Failed to load data</option>');
        $sel.selectpicker('refresh');
      }
    });
  }

  // ===== DataTable =====
  var dt = $('#tbl-supbank').DataTable({
    processing  : true,
    serverSide  : true,
    ordering    : true,
    searching   : true,
    autoWidth   : false,
    ajax: {
      url : 'ajax_master_supplier_bank.php',
      type: 'POST',
      data: function (d) {
        d.filter_type     = $('#filter-type').val();
        d.filter_supplier = $('#filter-name').val();
      }
    },
    columns: [
      { data: 'rownum',           className: 'text-center', orderable: false },
      { data: 'supplier_name'     },
      { data: 'bank_currency',    className: 'text-center' },
      { data: 'bank_name'         },
      { data: 'bank_account'      },
      { data: 'beneficiary_name'  },
      {
        data: 'status',
        className: 'text-center',
        render: function (d) {
          var cls = d === 'Active' ? 'success' : 'danger';
          return '<span class="badge badge-' + cls + '">' + d + '</span>';
        }
      },
      { data: 'created_by', className: 'text-center' },
      {
        data: null, orderable: false, searchable: false,
        className: 'text-center',
        render: function (d, t, row) {
          var edit = '<button class="btn btn-xs btn-warning mr-1" onclick="openEdit(' + row.id + ')">'
                   + '<i class="fa fa-pencil"></i> Edit</button>';
          var cancel = '<button class="btn btn-xs btn-danger" onclick="doCancel(' + row.id + ',\'' + row.supplier_name.replace(/'/g, "\\'") + '\')">'
                     + '<i class="fa fa-times"></i> Cancel</button>';
          if (row.status === 'Cancel') return '<span class="text-muted">-</span>';
          return edit + cancel;
        }
      }
    ]
  });

  // ===== OPEN CREATE =====
  function openCreate() {
    $('#modal-sup-title').html('<i class="fa fa-plus-circle mr-1"></i> Create Supplier Bank');
    $('#sb-id').val('');
    $('#sb-type').val('').selectpicker('refresh');
    $('#sb-supplier').html('<option value="">-- Select Type first --</option>')
                     .prop('disabled', true).selectpicker('refresh');
    $('#sb-currency').val('').selectpicker('refresh');
    $('#sb-bankname, #sb-account, #sb-beneficiary').val('');
    $('#modalSupBank').modal('show');
  }

  // ===== OPEN EDIT =====
  function openEdit(id) {
    $.ajax({
      url: 'ajax_master_supplier_bank.php',
      method: 'POST',
      data: { action: 'get_one', id: id },
      dataType: 'json',
      success: function (r) {
        if (!r) { Swal.fire('Error', 'Data tidak ditemukan.', 'error'); return; }
        $('#modal-sup-title').html('<i class="fa fa-pencil mr-1"></i> Edit Master Bank');
        $('#sb-id').val(r.id);
        $('#sb-type').val(r.tipe_sup).selectpicker('refresh');

        // Load name list dulu, baru set value
        $.ajax({
          url: 'ajax_master_supplier_bank.php', method: 'POST',
          data: { action: 'get_supplier_list', type: r.tipe_sup },
          dataType: 'json',
          success: function (data) {
            var opts = '<option value="">-- Select Name --</option>';
            $.each(data, function (i, d) {
              opts += '<option value="' + d.id_supplier + '">' + d.Supplier + '</option>';
            });
            $('#sb-supplier').html(opts).prop('disabled', false)
                             .val(r.id_supplier).selectpicker('refresh');
          }
        });

        $('#sb-currency').val(r.bank_currency).selectpicker('refresh');
        $('#sb-bankname').val(r.bank_name);
        $('#sb-account').val(r.bank_account);
        $('#sb-beneficiary').val(r.beneficiary_name);
        $('#modalSupBank').modal('show');
      },
      error: function () { Swal.fire('Error', 'Gagal mengambil data.', 'error'); }
    });
  }

  // ===== SAVE (Create / Edit) =====
  function saveSupBank() {
    var id          = $('#sb-id').val();
    var tipe_sup    = $('#sb-type').val();
    var id_supplier = $('#sb-supplier').val();
    var currency    = $('#sb-currency').val().trim();
    var bankname    = $('#sb-bankname').val().trim();
    var account     = $('#sb-account').val().trim();
    var beneficiary = $('#sb-beneficiary').val().trim();

    if (!tipe_sup) {
      Swal.fire('Perhatian', 'Pilih Type (Supplier / Customer) terlebih dahulu.', 'warning');
      return;
    }
    if (!id_supplier || !currency || !bankname || !account || !beneficiary) {
      Swal.fire('Perhatian', 'Semua field wajib diisi.', 'warning');
      return;
    }

    $.ajax({
      url    : 'save_master_supplier_bank.php',
      method : 'POST',
      data   : {
        id              : id,
        tipe_sup        : tipe_sup,
        id_supplier     : id_supplier,
        bank_currency   : currency,
        bank_name       : bankname,
        bank_account    : account,
        beneficiary_name: beneficiary
      },
      dataType: 'json',
      success: function (r) {
        if (r.status === 'success') {
          $('#modalSupBank').modal('hide');
          Swal.fire({ icon: 'success', title: 'Berhasil!',
                      text: id ? 'Data berhasil diupdate.' : 'Data berhasil disimpan.',
                      timer: 1800, showConfirmButton: false });
          dt.ajax.reload();
        } else {
          Swal.fire({ icon: 'error', title: 'Gagal', html: r.message || 'Terjadi kesalahan.' });
        }
      },
      error: function () { Swal.fire('Error', 'Gagal menghubungi server.', 'error'); }
    });
  }

  // ===== CANCEL =====
  function doCancel(id, name) {
    Swal.fire({
      title: 'Konfirmasi Cancel',
      html : 'Yakin ingin <b>cancel</b> data supplier bank:<br><b>' + name + '</b>?',
      icon : 'warning',
      showCancelButton   : true,
      confirmButtonColor : '#dc3545',
      cancelButtonColor  : '#6c757d',
      confirmButtonText  : '<i class="fa fa-check"></i> Ya, Cancel!',
      cancelButtonText   : 'Tidak'
    }).then(function (res) {
      if (!res.isConfirmed) return;
      $.ajax({
        url    : 'cancel_master_supplier_bank.php',
        method : 'POST',
        data   : { id: id },
        dataType: 'json',
        success: function (r) {
          if (r.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data berhasil di-cancel.',
                        timer: 1500, showConfirmButton: false });
            dt.ajax.reload();
          } else {
            Swal.fire('Gagal', r.message || 'Terjadi kesalahan.', 'error');
          }
        },
        error: function () { Swal.fire('Error', 'Gagal menghubungi server.', 'error'); }
      });
    });
  }
</script>

</body>
</html>
