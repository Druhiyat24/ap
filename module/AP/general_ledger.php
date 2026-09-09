<?php include '../header.php' ?>

<!-- Skin UI bersama (kartu, tabel, badge, tombol, dropdown, tanggal, loading) -->
<link rel="stylesheet" href="../css/app-skin.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin.css'); ?>">
<style>
/* Halaman-spesifik: tabel GL pakai scrollX/scrollY bawaan DataTables (bukan
   .app-dt-scroll) supaya area tabel tetap terbatas tingginya (350px) spt semula. */
#table-data td.text-right, #table-data th.text-right{ text-align:right; font-variant-numeric:tabular-nums; }
/* No Journal jadi link supaya jelas bisa diklik utk lihat detail jurnalnya. */
a.gl-doc-link{ color:#1d4ed8; font-weight:600; text-decoration:none; }
a.gl-doc-link:hover{ text-decoration:underline; }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card app-card border-0">
    <div class="card-header app-card-header">
      <h5><i class="fas fa-file-alt"></i> GENERAL LEDGER</h5>
    </div>

    <div class="card-body p-3">
      <form id="form-data" action="general_ledger.php" method="post">
        <div class="row g-3">
          <!-- COA -->
          <div class="col-md-3">
            <label class="app-flabel">No COA</label>
            <select class="form-control select2" name="coa_number" id="coa_number" data-live-search="true">
                <option value="">Select Coa Number</option>
                <?php
                $coa_number = $_POST['coa_number'] ?? '';
                $sql = mysqli_query($conn1, "select DISTINCT no_coa, nama_coa, CONCAT(no_coa,' - ',nama_coa) as coa from mastercoa_v2");
                while ($row = mysqli_fetch_array($sql)) {
                    $selected = ($row['no_coa'] == $coa_number) ? 'selected' : '';
                    echo "<option value='" . $row['no_coa'] . "' " . $selected . ">" . $row['coa'] . "</option>";
                }
                ?>
            </select>
          </div>

          <div class="col-md-2">
            <label class="app-flabel">Profit Center</label>
            <select class="form-control select2" name="profit_center" id="profit_center" data-live-search="true">
                <option value="ALL">ALL</option>
                <?php
                $profit_center = $_POST['profit_center'] ?? '';
                $sql = mysqli_query($conn1, "select kode_pc, CONCAT(id_pc,' - ',nama_pc) nama_pc from master_pc where status = 'Active'");
                while ($row = mysqli_fetch_array($sql)) {
                    $selected = ($row['kode_pc'] == $profit_center) ? 'selected' : '';
                    echo "<option value='" . $row['kode_pc'] . "' " . $selected . ">" . $row['nama_pc'] . "</option>";
                }
                ?>
            </select>
          </div>

          <div class="col-md-2">
            <label class="app-flabel">From</label>
            <input type="text" name="start_date" id="start_date" class="form-control form-control-sm tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
          </div>
          <div class="col-md-2">
            <label class="app-flabel">To</label>
            <input type="text" name="end_date" id="end_date" class="form-control form-control-sm tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
          </div>

          <div class="col-md-3 d-flex align-items-end">
            <div class="app-actions">
              <button type="button" class="app-btn app-btn-primary app-btn-ctl" onclick="dataTableReload()"><i class="fa fa-search"></i> Search</button>
              <button type="button" class="app-btn app-btn-success app-btn-ctl" onclick="ExportGL()"><i class="fa fa-file-excel"></i> Excel</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Card Table -->
  <div class="card app-card border-0 mt-4">
    <div class="card-body p-4">
      <!-- .app-loading-wrap: area yg ditutup overlay loading saat tabel memuat data -->
      <div class="app-loading-wrap" id="glLoad">
        <div class="app-loading">
          <div class="app-loading-box">
            <div class="app-spinner"><span>NAG</span></div>
            <div class="app-loading-text">Loading data...</div>
          </div>
        </div>
        <table id="table-data" class="table table-hover app-dt nowrap" style="width:100%">
          <thead>
            <tr>
              <th>No Journal</th>
              <th>Date</th>
              <th>Profit Center</th>
              <th>Reff Document</th>
              <th>Descriptions</th>
              <th>Debit</th>
              <th>Credit</th>
              <th>Saldo</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ===== Modal detail jurnal (dipakai saat No Journal diklik) — pola sama
     dgn popup detail dokumen di ppn_masukan_report.php. ===== -->
<div class="modal fade" id="glDocModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog app-modal modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-file-text-o"></i> <span id="glDocTitle">Journal Detail</span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="fa fa-times"></span></button>
      </div>
      <div class="modal-body" id="glDocBody"></div>
      <div class="modal-footer">
        <button type="button" class="app-btn app-btn-light app-btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
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
<script language="JavaScript" src="../css/4.1.1/xlsx.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/html2pdf.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/exceljs.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/FileSaver.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min"></script>

<script>
    // Hide submenus
    $('#body-row .collapse').collapse('hide');

    // Collapse/Expand icon
    $('#collapse-icon').addClass('fa-angle-double-left');

    // Collapse click
    $('[data-toggle=sidebar-colapse]').click(function() {
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

  <script>
    $(function() {
      $('.select2').select2({
        width: '100%'
      });

      $('.selectpicker').selectpicker();
    });
  </script>



<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script type="text/javascript">
  let datatable = $("#table-data").DataTable({
    ordering: false,
    processing: true,
    serverSide: false,
    searching: true,
    info: true,
    autoWidth: false,

    scrollX: true,
    scrollY: "350px",
    scrollCollapse: true,
    paging: false,

      ajax: {
        url: 'ajx_get_data_gl.php',
        type: 'POST',
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
          d.coa_number      = $('#coa_number').val();
          d.profit_center   = $('#profit_center').val();

        },

        dataSrc: function (json) {

        // 🔥 tampilkan query ke console
        console.log("SQL QUERY:");
        console.log(json.query);

        return json.data;
        }
      },

      columns: [
      { data: 'no_journal' },
      { data: 'tgl_journal' },
      { data: 'nama_pc' },
      { data: 'reff_doc' },
      { data: 'keterangan' },
      { data: 'debit_idr' },
      { data: 'credit_idr' },
      { data: 'saldo_akhir' },
      ],

      columnDefs: [
      {
              targets: [5, 6, 7],
              className: "text-right",
              render: function (data) {
                let val = parseFloat(data);
                if (isNaN(val)) return data;

                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 4,
                  maximumFractionDigits: 4
                });
              }
            },
            {
              // No Journal jadi link (kecuali baris SALDO AWAL yg isinya '-')
              // supaya bisa diklik utk lihat detail jurnalnya, spt di PPN Masukan.
              targets: 0,
              render: function (data, type) {
                if (type !== 'display') { return data; }
                if (!data || data === '-') { return data; }
                var s = String(data);
                var escd = s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                return '<a href="javascript:void(0)" class="gl-doc-link" data-doc="' + escd + '">' + escd + '</a>';
              }
            }
            ],

      language: {
        processing:  '<i class="fa fa-spinner fa-spin"></i> Loading...',
        emptyTable:  '<div class="app-empty"><i class="fa fa-inbox"></i>No data found</div>',
        zeroRecords: '<div class="app-empty"><i class="fa fa-search"></i>No matching records</div>',
        info:        'Showing _START_&ndash;_END_ of _TOTAL_ entries',
        infoEmpty:   'Showing 0 entries'
      },

initComplete: function () {
  this.api().columns.adjust();
}
});

// Overlay loading (skin .app-loading) mengikuti status processing DataTables.
datatable.on('processing.dt', function (e, settings, processing) {
  $('#glLoad').toggleClass('is-loading', processing);
});

$("[data-toggle=tooltip]").tooltip();

function dataTableReload() {
  datatable.ajax.reload(()=>{
    datatable.columns.adjust();
  });
}

// ===== Klik No Journal -> modal detail jurnal (pola sama dgn PPN Masukan
// Sub Ledger's document detail popup) =====
var glEsc = function (s) {
  return String(s === null || s === undefined ? '' : s)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
};
var glNf = function (v) {
  v = parseFloat(v); if (isNaN(v)) { v = 0; }
  return v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};
var glDmy = function (s) {
  if (!s || s === '0000-00-00') { return '-'; }
  var p = String(s).substr(0, 10).split('-');
  if (p.length !== 3) { return s; }
  var m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  return p[2] + '-' + (m[parseInt(p[1], 10) - 1] || p[1]) + '-' + p[0];
};
var glInfo = function (label, val) {
  return '<div><b>' + glEsc(label) + '</b><span>' + (val === '' || val === null || val === undefined ? '-' : glEsc(val)) + '</span></div>';
};

$('#table-data').on('click', 'a.gl-doc-link', function () {
  var doc = $(this).data('doc') || '';
  $('#glDocTitle').text(doc);
  $('#glDocBody').html('<div class="text-center" style="padding:38px 0;">' +
    '<div class="app-spinner" style="margin:0 auto;"><span>NAG</span></div>' +
    '<div class="app-loading-text" style="margin-top:12px;">Loading detail...</div></div>');
  $('#glDocModal').modal('show');

  $.post('ajx_gl_detail.php', { no_journal: doc }, null, 'json')
    .done(function (res) {
      if (!res || res.status !== 'success') {
        $('#glDocBody').html('<div class="app-empty" style="padding:30px 0;"><i class="fa fa-exclamation-triangle"></i>' +
          glEsc((res && res.message) ? res.message : 'Failed to load detail.') + '</div>');
        return;
      }
      var h = res.head, t = res.total, html = '';

      html += '<div class="app-mod-info">' +
        glInfo('Document No', h.no_journal) + glInfo('Document Date', glDmy(h.tgl_journal)) +
        glInfo('Type', h.type_journal) + glInfo('Tax Invoice No', h.faktur_pajak) +
        glInfo('Supplier', h.supplier) + glInfo('Profit Center', h.profit_center) +
        glInfo('Status', h.status) + '</div>';

      html += '<div class="app-mod-sec">Journal lines (' + res.lines.length + ')</div>' +
        '<div class="app-mod-scroll"><table class="app-mod-tbl coa-tbl"><thead><tr>' +
        '<th>COA</th><th>Cost Center</th><th>Reff Doc</th><th>Curr</th><th class="num">Rate</th>' +
        '<th class="num">Debit</th><th class="num">Credit</th>' +
        '<th class="num">Debit IDR</th><th class="num">Credit IDR</th><th>Description</th>' +
        '</tr></thead><tbody>';
      $.each(res.lines, function (i, r) {
        html += '<tr>' +
          '<td><b>' + glEsc(r.no_coa) + '</b><br><span style="color:#64748b;">' + glEsc(r.nama_coa) + '</span></td>' +
          '<td>' + glEsc(r.no_costcenter) + '</td>' +
          '<td class="nowrap">' + glEsc(r.reff_doc) + '</td>' +
          '<td class="nowrap">' + glEsc(r.curr) + '</td>' +
          '<td class="num">' + glNf(r.rate) + '</td>' +
          '<td class="num">' + glNf(r.debit) + '</td>' +
          '<td class="num">' + glNf(r.credit) + '</td>' +
          '<td class="num">' + glNf(r.debit_idr) + '</td>' +
          '<td class="num">' + glNf(r.credit_idr) + '</td>' +
          '<td>' + glEsc(r.keterangan) + '</td></tr>';
      });
      html += '</tbody><tfoot><tr>' +
        '<th colspan="5">TOTAL</th>' +
        '<th class="num">' + glNf(t.debit) + '</th><th class="num">' + glNf(t.credit) + '</th>' +
        '<th class="num">' + glNf(t.debit_idr) + '</th><th class="num">' + glNf(t.credit_idr) + '</th>' +
        '<th></th></tr></tfoot></table></div>';

      $('#glDocBody').html(html);
    })
    .fail(function () {
      $('#glDocBody').html('<div class="app-empty" style="padding:30px 0;">' +
        '<i class="fa fa-exclamation-triangle"></i>Failed to contact the server.</div>');
    });
});


function ExportGL() {

    let start_date    = $('#start_date').val();
    let end_date      = $('#end_date').val();
    let coa_number    = $('#coa_number').val();
    let profit_center = $('#profit_center').val();

    // VALIDASI
    if (coa_number == '') {
        Swal.fire('Warning', 'COA wajib dipilih', 'warning');
        return;
    }

    if (profit_center == '' || profit_center == '-') {
        Swal.fire('Warning', 'Profit Center wajib dipilih', 'warning');
        return;
    }

    if (start_date == '' || end_date == '') {
        Swal.fire('Warning', 'Tanggal wajib diisi', 'warning');
        return;
    }

    Swal.fire({
        title: 'Export Excel?',
        text: "Data akan di download",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Export!',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (result.isConfirmed) {

            // LOADING
            Swal.fire({
                title: 'Processing...',
                text: 'Sedang generate Excel',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 🔥 REDIRECT KE PHP EXPORT
            window.open(
                "export_gl.php?start_date=" + start_date +
                "&end_date=" + end_date +
                "&coa_number=" + coa_number +
                "&profit_center=" + profit_center,
                "_blank"
            );

            setTimeout(() => {
                Swal.close();
            }, 1500);
        }
    });
}

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "general_ledger.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "general_ledger.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
