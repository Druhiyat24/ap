<?php include '../header.php'; ?>
<?php
// Generate preview No Transfer TFTE
$kode_dok = 'TFTE';
$sql_num  = mysqli_query($conn2, "SELECT MAX(SUBSTR(no_trans, 15)) AS last_num FROM transfer_memo_exim_h WHERE no_trans LIKE '%$kode_dok%'");
$row_num  = mysqli_fetch_assoc($sql_num);
$last_num = (int)$row_num['last_num'];
$preview_no = $kode_dok . '/NAG/' . date('my') . '/' . sprintf('%05d', $last_num + 1);

// Unique code untuk linking header-detail
$unik_code = bin2hex(random_bytes(16));

// Supplier list
$sql_sup   = mysqli_query($conn2, "SELECT DISTINCT Supplier FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
?>

<style>
  label { font-size: 13px; font-weight: 600; }

  /* Header table */
  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
    padding: 8px 10px;
  }

  /* Body row alignment */
  #tbl-memo td {
    vertical-align: middle;
    font-size: 13px;
    padding: 6px 10px;
    white-space: nowrap;
  }

  /* Checkbox column */
  #tbl-memo th:first-child,
  #tbl-memo td:first-child {
    text-align: center;
    width: 40px;
  }

  /* Description column – allow wrap */
  #tbl-memo td:last-child {
    white-space: normal;
    min-width: 160px;
  }

  .card-footer-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
  }
</style>

<div class="container-fluid mt-4 p-4">

  <!-- ===== CARD FORM ===== -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3"
         style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <h5 class="mb-0"><i class="fa fa-file-text-o mr-1"></i> FORM TRANSFER MEMO</h5>
    </div>

    <div class="card-body p-3">

      <!-- Row 1: No Transfer, Tgl Transfer, Supplier, Keterangan -->
      <div class="row mb-3">
        <div class="col-md-3">
          <label>No Transfer</label>
          <input type="text" class="form-control form-control-sm bg-light"
                 id="no_transfer" value="<?= $preview_no ?>" readonly>
          <input type="hidden" id="unik_code" value="<?= $unik_code ?>">
        </div>

        <div class="col-md-2">
          <label>Tgl Transfer</label>
          <div class="input-group input-group-sm">
            <input type="text" class="form-control tanggal"
                   id="tgl_transfer" value="<?= date('d-m-Y') ?>" autocomplete="off">
            <div class="input-group-append">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <label>Supplier</label>
          <select class="form-control selectpicker form-control-sm" id="supplier_filter" data-dropup-auto="false" data-live-search="true" data-size="5">
            <option value="ALL">ALL</option>
            <?php while ($s = mysqli_fetch_assoc($sql_sup)): ?>
              <option value="<?= htmlspecialchars($s['Supplier']) ?>">
                <?= htmlspecialchars($s['Supplier']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>

      <!-- Row 2: Search From, Search To, Search button -->
      <div class="row align-items-end">
         <div class="col-md-5">
          <label>Keterangan</label>
          <textarea class="form-control form-control-sm" id="keterangan"
                    rows="2" placeholder="Description..."></textarea>
        </div>
        <div class="col-md-2">
          <label>Search From:</label>
          <div class="input-group input-group-sm">
            <input type="text" class="form-control tanggal"
                   id="search_from" value="<?= date('d-m-Y') ?>" autocomplete="off">
            <div class="input-group-append">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <label>Search To:</label>
          <div class="input-group input-group-sm">
            <input type="text" class="form-control tanggal"
                   id="search_to" value="<?= date('d-m-Y') ?>" autocomplete="off">
            <div class="input-group-append">
              <span class="input-group-text"><i class="fa fa-calendar"></i></span>
            </div>
          </div>
        </div>

        <div class="col-md-2">
          <button type="button" class="btn btn-primary btn-sm" onclick="loadMemoData()">
            <i class="fa fa-search"></i> search
          </button>
        </div>
      </div>

    </div><!-- /card-body -->
  </div><!-- /card form -->

  <!-- ===== CARD TABLE ===== -->
  <div class="card shadow border-0 mt-3">
    <div class="card-body p-3">
      <div id="table-info" class="mb-2 text-muted small"></div>
      <div class="table-responsive">
        <table id="tbl-memo" class="table table-striped table-bordered table-hover table-sm nowrap" style="width:100%">
          <thead class="table-gradient">
            <tr>
              <th style="width:40px;">
                <input type="checkbox" id="chk-all" title="Pilih semua">
              </th>
              <th>No Memo</th>
              <th>Tgl Memo</th>
              <th>Kepada</th>
              <th>Supplier</th>
              <th>Jenis Transaksi</th>
              <th>Jenis Pengiriman</th>
              <th>Buyer</th>
              <th style="min-width:160px;">Descriptions</th>
            </tr>
          </thead>
          <tbody id="memo-tbody">
            <tr>
              <td colspan="9" class="text-center text-muted py-3">
                <i class="fa fa-info-circle mr-1"></i>
                Klik <b>search</b> untuk menampilkan data memo
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer-actions">
    <a href="transfer_memo.php" class="btn btn-sm btn-danger">
      <i class="fa fa-arrow-left mr-1"></i> Back
    </a>
    <button type="button" class="btn btn-sm btn-primary" onclick="saveData()">
      <i class="fa fa-save mr-1"></i> Save
    </button>
  </div>
    </div>
  </div>

  <!-- ===== FOOTER BUTTONS ===== -->
  <!-- <div class="card-footer-actions">
    <a href="transfer_memo.php" class="btn btn-sm btn-danger">
      <i class="fa fa-arrow-left mr-1"></i> Back
    </a>
    <button type="button" class="btn btn-sm btn-primary" onclick="saveData()">
      <i class="fa fa-save mr-1"></i> Save
    </button>
  </div> -->

</div><!-- /container -->


<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>

<script language="JavaScript" src="../css/4.1.1/xlsx.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/html2pdf.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/exceljs.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/FileSaver.min.js"></script>


<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min"></script>
<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>
<script>
  // ===== Sidebar collapse =====
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

  // ===== Datepicker =====
  $(document).ready(function () {
    $('.tanggal').datepicker({ format: 'dd-mm-yyyy', autoclose: true });
  });

  // ===== Check all — iterasi SEMUA baris DataTable lintas halaman =====
  $(document).on('change', '#chk-all', function () {
    var isChecked = this.checked;
    if (!dt) return;
    dt.rows().every(function () {
      $(this.node()).find('input[name="chk-memo"]').prop('checked', isChecked);
    });
    updateChkCount();
  });

  // ===== Per-baris checkbox — update state header + counter =====
  $(document).on('change', 'input[name="chk-memo"]', function () {
    updateChkAll();
    updateChkCount();
  });

  function updateChkAll() {
    if (!dt) return;
    var total   = dt.rows().count();
    var checked = 0;
    dt.rows().every(function () {
      if ($(this.node()).find('input[name="chk-memo"]').is(':checked')) checked++;
    });
    $('#chk-all').prop('indeterminate', checked > 0 && checked < total);
    $('#chk-all').prop('checked', total > 0 && checked === total);
  }

  function updateChkCount() {
    if (!dt) return;
    var checked = 0;
    dt.rows().every(function () {
      if ($(this.node()).find('input[name="chk-memo"]').is(':checked')) checked++;
    });
    var info = checked > 0 ? '<b>' + checked + '</b> memo dipilih' : 'Menampilkan ' + dt.rows().count() + ' data';
    $('#table-info').html(info);
  }

  // ===== Load memo data =====
  var dt;
  function loadMemoData() {
    var from     = $('#search_from').val();
    var to       = $('#search_to').val();
    var supplier = $('#supplier_filter').val();

    if (!from || !to) {
      Swal.fire('Perhatian', 'Tanggal From dan To harus diisi.', 'warning');
      return;
    }

    $('#memo-tbody').html(
      '<tr><td colspan="9" class="text-center py-3">' +
      '<i class="fa fa-spinner fa-spin fa-lg text-primary"></i> Memuat data...</td></tr>'
    );

    if (dt) { dt.destroy(); dt = null; }

    $.ajax({
      url: 'ajax_create_memo_list.php',
      method: 'POST',
      data: { from: from, to: to, supplier: supplier },
      dataType: 'json',
      success: function (data) {
        if (!data || data.length === 0) {
          $('#memo-tbody').html(
            '<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data tersedia</td></tr>'
          );
          $('#table-info').text('');
          return;
        }

        var html = '';
        $.each(data, function (i, d) {
          html += '<tr>' +
            '<td class="text-center align-middle">' +
              '<input type="checkbox" name="chk-memo"' +
              ' data-nm_memo="'       + escHtml(d.nm_memo)        + '"' +
              ' data-id_h="'          + escHtml(d.id_h)            + '"' +
              ' data-status_before="' + escHtml(d.status_transfer) + '">' +
            '</td>' +
            '<td class="align-middle">' + (d.nm_memo         || '-') + '</td>' +
            '<td class="align-middle text-center">' + (d.tgl_memo     || '-') + '</td>' +
            '<td class="align-middle">' + (d.kepada           || '-') + '</td>' +
            '<td class="align-middle">' + (d.supplier         || '-') + '</td>' +
            '<td class="align-middle text-center">' + (d.jns_trans    || '-') + '</td>' +
            '<td class="align-middle text-center">' + (d.jns_pengiriman || '-') + '</td>' +
            '<td class="align-middle">' + (d.buyer            || '-') + '</td>' +
            '<td class="align-middle">' +
              '<input type="text" class="form-control form-control-sm desc-input"' +
              ' placeholder="Description...">' +
            '</td>' +
          '</tr>';
        });
        $('#memo-tbody').html(html);
        $('#table-info').text('Menampilkan ' + data.length + ' data');

        dt = $('#tbl-memo').DataTable({
          paging   : true,
          searching: true,
          ordering : true,
          autoWidth: true,
          scrollX  : false,
          columnDefs: [
            { orderable: false,                          targets: [0, 8] },
            { className: 'text-center align-middle',    targets: [0, 2, 5, 6] },
            { className: 'align-middle',                targets: [1, 3, 4, 7, 8] }
          ]
        });
        dt.columns.adjust();

        // Update header checkbox state setiap kali halaman berpindah
        dt.on('draw', function () { updateChkAll(); });
      },
      error: function () {
        $('#memo-tbody').html(
          '<tr><td colspan="9" class="text-center text-danger py-3">' +
          '<i class="fa fa-exclamation-triangle mr-1"></i>Gagal memuat data.</td></tr>'
        );
      }
    });
  }

  // ===== Save — kumpulkan item dari SEMUA halaman DataTable =====
  function saveData() {
    if (!dt) {
      Swal.fire('Perhatian', 'Klik Search terlebih dahulu untuk memuat data.', 'info');
      return;
    }

    var items = [];
    dt.rows().every(function () {
      var node = this.node();
      var chk  = $(node).find('input[name="chk-memo"]');
      if (chk.is(':checked')) {
        items.push({
          nm_memo      : chk.data('nm_memo'),
          id_h         : chk.data('id_h'),
          status_before: chk.data('status_before'),
          keterangan   : $(node).find('.desc-input').val()
        });
      }
    });

    if (items.length === 0) {
      Swal.fire('Perhatian', 'Pilih minimal satu memo untuk ditransfer.', 'warning');
      return;
    }

    Swal.fire({
      title            : 'Konfirmasi Save',
      html             : 'Simpan <b>' + items.length + ' memo</b> ke Transfer Memo?',
      icon             : 'question',
      showCancelButton : true,
      confirmButtonColor: '#1e90ff',
      cancelButtonColor : '#6c757d',
      confirmButtonText: '<i class="fa fa-save"></i> Simpan',
      cancelButtonText : 'Batal'
    }).then(function (result) {
      if (!result.isConfirmed) return;

      Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false,
                  didOpen: function () { Swal.showLoading(); } });

      $.ajax({
        url     : 'save_transfer_memo.php',
        method  : 'POST',
        data    : {
          tgl_transfer: $('#tgl_transfer').val(),
          keterangan  : $('#keterangan').val(),
          unik_code   : $('#unik_code').val(),
          items       : JSON.stringify(items)
        },
        dataType: 'json',
        success: function (r) {
          if (r.status === 'success') {
            Swal.fire({
              icon : 'success',
              title: 'Berhasil!',
              html : 'Transfer Memo berhasil disimpan.<br><b>' + r.no_trans + '</b>',
              confirmButtonText: 'OK'
            }).then(function () { window.location.href = 'transfer_memo.php'; });
          } else {
            Swal.fire('Gagal', r.message || 'Terjadi kesalahan.', 'error');
          }
        },
        error: function () {
          Swal.fire('Error', 'Gagal menghubungi server. Silakan coba lagi.', 'error');
        }
      });
    });
  }

  function escHtml(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;');
  }
</script>

</body>
</html>
