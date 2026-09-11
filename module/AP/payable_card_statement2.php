<?php include '../header.php' ?>

<style type="text/css">
  label {
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    text-transform: uppercase;
    letter-spacing: .03em;
  }

  input, .selectpicker .btn {
    font-size: 14px;
  }

  .pcs2-card {
    border-radius: 14px;
    overflow: hidden;
    transition: box-shadow .25s ease;
  }

  .pcs2-card:hover {
    box-shadow: 0 .5rem 1.5rem rgba(25,25,112,0.12) !important;
  }

  .pcs2-header {
    background: linear-gradient(90deg, #191970, #1e90ff);
    position: relative;
    overflow: hidden;
  }

  .pcs2-header::after {
    content: "";
    position: absolute;
    right: -40px;
    top: -40px;
    width: 160px;
    height: 160px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
  }

  .pcs2-header h5 {
    font-weight: 700;
    letter-spacing: .02em;
  }

  .pcs2-header .badge {
    background: rgba(255,255,255,0.18);
    font-weight: 500;
    font-size: .75rem;
    padding: .35em .7em;
    border-radius: 20px;
  }

  .pcs2-card .card-body {
    background: #fafbfd;
  }

  .form-control, .selectpicker > .dropdown-toggle {
    border-radius: 8px;
    border: 1px solid #dde2ec;
  }

  .form-control:focus {
    border-color: #1e90ff;
    box-shadow: 0 0 0 .15rem rgba(30,144,255,.15);
  }

  /* select2 disamakan ukurannya dengan input tanggal (form-control-sm) */
  .select2-container .select2-selection--single {
    height: 31px;
    border-radius: 8px;
    border: 1px solid #dde2ec;
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 29px;
    font-size: 14px;
    padding-left: 12px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 29px;
  }

  #btnFilter {
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(23,162,184,.3);
  }

  #btnExportAll {
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(40,167,69,.3);
  }

  .tabcontent {
    display: none;
    animation: fadeEffect 0.3s;
  }

  @keyframes fadeEffect {
    from {opacity: 0; transform: translateY(4px);}
    to {opacity: 1; transform: translateY(0);}
  }

  .tab-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 10px;
    background: #eef1f7;
    border-radius: 12px;
    border: 1px solid #e2e6ee;
    margin-bottom: 1.25rem;
  }

  .tablinks {
    border: none;
    background: #ffffff;
    color: #555;
    padding: 9px 18px;
    font-size: 0.88rem;
    font-weight: 600;
    border-radius: 24px;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  }

  .tablinks i {
    margin-right: 6px;
    opacity: .85;
  }

  .tablinks:hover {
    background: #1e90ff;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(30,144,255,.25);
  }

  .tablinks.active {
    background: linear-gradient(135deg, #191970, #1e90ff);
    color: #fff;
    box-shadow: 0 4px 12px rgba(25,25,112,0.35);
  }

  table.dataTable th,
  table.dataTable td {
    white-space: nowrap;
    vertical-align: middle;
  }

  .dataTables_scrollHeadInner,
  .dataTables_scrollBody table {
    width: 100% !important;
  }

  /* ===== Tabel report (BPB dkk) - tema modern selaras header utama ===== */
  .pcs2-btn-excel {
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(40,167,69,.3);
  }

  .pcs2-table-wrap {
    border-radius: 10px;
    border: 1px solid #e7eaf0;
    overflow-x: auto;
    overflow-y: hidden;
  }

  /* ===== Freeze 5 kolom pertama (Nama Supplier..Currency) murni pakai CSS
     sticky di SATU tabel yang sama - tidak pakai plugin FixedColumns lagi
     karena sering meleset menyamakan lebar kolom antar head/body/foot.   ===== */
  /* Freeze kolom hanya untuk BPB dan Payment Voucher */
  #table-pcs-bpb thead tr:first-child th:nth-child(1),
  #table-pcs-bpb tbody td:nth-child(1),
  #table-pcs-bpb tfoot th:nth-child(1),
  #table-pcs-pv thead tr:first-child th:nth-child(1),
  #table-pcs-pv tbody td:nth-child(1),
  #table-pcs-pv tfoot th:nth-child(1) { position: sticky; left: 0px;   min-width: 170px; }

  #table-pcs-bpb thead tr:first-child th:nth-child(2),
  #table-pcs-bpb tbody td:nth-child(2),
  #table-pcs-bpb tfoot th:nth-child(2),
  #table-pcs-pv thead tr:first-child th:nth-child(2),
  #table-pcs-pv tbody td:nth-child(2),
  #table-pcs-pv tfoot th:nth-child(2) { position: sticky; left: 170px; min-width: 150px; }

  #table-pcs-bpb thead tr:first-child th:nth-child(3),
  #table-pcs-bpb tbody td:nth-child(3),
  #table-pcs-bpb tfoot th:nth-child(3),
  #table-pcs-pv thead tr:first-child th:nth-child(3),
  #table-pcs-pv tbody td:nth-child(3),
  #table-pcs-pv tfoot th:nth-child(3) { position: sticky; left: 320px; min-width: 95px; }

  #table-pcs-bpb thead tr:first-child th:nth-child(4),
  #table-pcs-bpb tbody td:nth-child(4),
  #table-pcs-bpb tfoot th:nth-child(4),
  #table-pcs-pv thead tr:first-child th:nth-child(4),
  #table-pcs-pv tbody td:nth-child(4),
  #table-pcs-pv tfoot th:nth-child(4) { position: sticky; left: 415px; min-width: 95px; }

  #table-pcs-bpb thead tr:first-child th:nth-child(5),
  #table-pcs-bpb tbody td:nth-child(5),
  #table-pcs-bpb tfoot th:nth-child(5),
  #table-pcs-pv thead tr:first-child th:nth-child(5),
  #table-pcs-pv tbody td:nth-child(5),
  #table-pcs-pv tfoot th:nth-child(5) { position: sticky; left: 510px; min-width: 70px; }

  #table-pcs-bpb thead th:nth-child(-n+5),
  #table-pcs-pv thead th:nth-child(-n+5) { z-index: 6; }

  #table-pcs-bpb tbody td:nth-child(-n+5),
  #table-pcs-pv tbody td:nth-child(-n+5) {
    z-index: 2;
    background-color: #fff;
    box-shadow: 2px 0 4px -2px rgba(0,0,0,.12);
  }

  #table-pcs-bpb tbody tr:nth-of-type(odd) td:nth-child(-n+5),
  #table-pcs-pv tbody tr:nth-of-type(odd) td:nth-child(-n+5) { background-color: #f7f8fb; }

  #table-pcs-bpb tbody tr:hover td:nth-child(-n+5),
  #table-pcs-pv tbody tr:hover td:nth-child(-n+5) { background-color: #eef6ff; }

  #table-pcs-bpb tfoot th:nth-child(-n+5),
  #table-pcs-pv tfoot th:nth-child(-n+5) {
    z-index: 5;
    box-shadow: 2px 0 4px -2px rgba(0,0,0,.12);
  }

  .pcs2-table {
    margin-bottom: 0;
    font-size: 12.5px;
  }

  .pcs2-table thead th {
    text-align: center;
    vertical-align: middle;
    color: #fff;
    border-color: rgba(255,255,255,.25) !important;
    font-weight: 600;
    letter-spacing: .01em;
  }

  /* Skema warna per grup disamakan konsepnya dengan menu sebelumnya
     (rose muda utk detail, peach utk item type, hijau utk aging, biru
     utk projection) tapi versi gradient modern, bukan flat pastel. */
.grp-head-default {
    background: linear-gradient(180deg, #2a2f78, #191970);
  }

  .grp-head-accent1 {
    background: linear-gradient(180deg, #3b4296, #2a2f78);
  }

  .grp-head-aging {
    background: linear-gradient(180deg, #17a589, #0e8174);
  }

  .grp-head-projection {
    background: linear-gradient(180deg, #2196f3, #1e7fd6);
  }

  .pcs2-table tbody tr:hover {
    background-color: #eef6ff !important;
  }

  .pcs2-table tfoot th {
    background: #f1f3f9;
    color: #191970;
    border-top: 2px solid #dde2ec;
  }

  .pcs2-table tfoot tr.pcs2-foot-all th {
    background: #191970;
    color: #fff;
  }

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card pcs2-card shadow border-0">
    <div class="card-header pcs2-header text-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap">
      <h5 class="mb-0"><i class="fas fa-swatchbook me-2"></i>PAYABLE CARD STATEMENT</h5>
      <span class="badge"><i class="far fa-calendar-alt me-1"></i> Jul 2026 - dst</span>
    </div>

  <div class="card-body p-4">
    <form id="form-data" action="payable_card_statement2.php" method="post">
      <div class="row g-3">
        <!-- Supplier -->
        <div class="col-md-3 mb-3">
          <label for="nama_supp"><i class="fa fa-building me-1"></i> Supplier</label>
          <select class="form-control form-control-sm" name="nama_supp" id="nama_supp" style="width:100%;">
            <option value="ALL"<?php echo (!isset($_POST['nama_supp']) || $_POST['nama_supp'] == 'ALL') ? ' selected="selected"' : ''; ?>>ALL</option>
            <?php
            $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC", $conn1);
            while ($row = mysql_fetch_array($sql)) {
              $data = $row['Supplier'];
              $isSelected = (isset($_POST['nama_supp']) && $_POST['nama_supp'] == $data) ? ' selected="selected"' : '';
              echo '<option value="'.$data.'"'.$isSelected.'>'. $data .'</option>';
            }
            ?>
          </select>
        </div>

        <!-- Start Date -->
        <div class="col-md-2">
          <label for="start_date" class="form-label"><i class="far fa-calendar-alt me-1"></i> From</label>
          <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
          value="<?php echo !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d'); ?>" placeholder="Start Date" autocomplete="off">
        </div>

        <!-- End Date -->
        <div class="col-md-2">
          <label for="end_date" class="form-label"><i class="far fa-calendar-alt me-1"></i> To</label>
          <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
          value="<?php echo !empty($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d'); ?>" placeholder="End Date" autocomplete="off">
        </div>

        <!-- Tombol -->
        <div class="col-md-3 d-flex align-items-end mb-3">
          <button id="btnFilter" type="button" class="btn btn-info btn-sm me-2" onclick="dataTableReload()">
            <i class="fa fa-search"></i> Search
          </button>
          <button id="btnExportAll"
            type="button"
            class="btn btn-success btn-xs ml-2">
            <i class="fa fa-file-excel-o"></i>
            <span id="btnText"> Excel</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Card Table -->
<div class="card pcs2-card shadow border-0 mt-4">
  <div class="card-body p-4">

    <div class="tab-container">
      <button class="tablinks active" onclick="openTab(event, 'pcs2_bpb')"><i class="fa fa-box"></i>BPB</button>
      <button class="tablinks" onclick="openTab(event, 'pcs2_payment_voucher')"><i class="fa fa-file-invoice-dollar"></i>Payment Voucher</button>
      <button class="tablinks" onclick="openTab(event, 'pcs2_summary_supplier')"><i class="fa fa-users"></i>Summary Supplier</button>
      <button class="tablinks" onclick="openTab(event, 'pcs2_type1')"><i class="fa fa-tag"></i>Item Type 1</button>
      <button class="tablinks" onclick="openTab(event, 'pcs2_type2')"><i class="fa fa-tags"></i>Item Type 2</button>
      <button class="tablinks" onclick="openTab(event, 'pcs2_summary_grup')"><i class="fa fa-chart-pie"></i>Summary</button>
    </div>

    <div id="pcs2_bpb" class="tabcontent">
      <?php include 'ap_report2/pcs2_bpb.php'; ?>
    </div>

    <div id="pcs2_payment_voucher" class="tabcontent">
      <?php include 'ap_report2/pcs2_payment_voucher.php'; ?>
    </div>

    <div id="pcs2_summary_supplier" class="tabcontent">
      <?php include 'ap_report2/pcs2_summary_supplier.php'; ?>
    </div>

    <div id="pcs2_type1" class="tabcontent">
      <?php include 'ap_report2/pcs2_type1.php'; ?>
    </div>

    <div id="pcs2_type2" class="tabcontent">
      <?php include 'ap_report2/pcs2_type2.php'; ?>
    </div>

    <div id="pcs2_summary_grup" class="tabcontent">
      <?php include 'ap_report2/pcs2_summary_grup.php'; ?>
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

<script>
  $(function() {
    $('#nama_supp').select2({
      width: '100%'
    });
  });
</script>

<script>
  // Default pageLength 10 untuk semua DataTable di halaman ini
  $.extend(true, $.fn.dataTable.defaults, {
    pageLength: 10
  });
</script>

<script>
  function openTab(evt, tabName) {
    let i, tabcontent, tablinks;

    // sembunyikan semua konten
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }

    // hapus active di semua tombol
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].classList.remove("active");
    }

    // tampilkan tab yang dipilih
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("active");
  }

  // buka default tab saat halaman load
  document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".tablinks.active").click();
  });
</script>

<script type="text/javascript">
  $(document).ready(function () {
    $('.tanggal').datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      startDate: "2026-07-01"
    });
  });
</script>

<script>
  function toYmd(dmy) {
    if (!dmy) return '';
    let p = dmy.split('-'); // [dd, mm, yyyy]
    return `${p[2]}-${p[1]}-${p[0]}`;
  }

  function loadProjectionHeader() {
    let endDate = $('#end_date').val();
    $.ajax({
      url: 'ap_report/get-projection-month.php',
      type: 'POST',
      data: { end_date: endDate },
      dataType: 'json',
      success: function (res) {
        const prefixes = ['','pv-','sum-supp-','type1-','type2-','sum-grup-'];
        for (let i = 0; i < 6; i++) {
          prefixes.forEach(p => $('#proj-month-' + p + (i+1)).text(res[i] ?? '-'));
        }
        [datatable,datatable2,datatable3,datatable4,datatable5,datatable6,datatable7].forEach(function(dt){
          if (typeof dt !== 'undefined') dt.columns.adjust();
        });
      }
    });
  }

  $(document).ready(function () {
    loadProjectionHeader();
  });

  let datatable = $("#table-pcs-bpb").DataTable({
    ordering: false,
    processing: true,
    serverSide: true,
    searching: true,
    info: true,
    autoWidth: false,
    scrollX: false,
    paging: true,

    ajax: {
      url: 'ap_report2/ajx_pcs_bpb2.php',
      type: 'POST',
      data: function (d) {
        d.start_date = $('#start_date').val();
        d.end_date   = $('#end_date').val();
        d.nama_supp  = $('#nama_supp').val();
      }
    },

    columns: [
      { data: 'supplier' },
      { data: 'no_bpb' },
      { data: 'tgl_bpb' },
      { data: 'duedate' },
      { data: 'curr' },
      { data: 'saldo_awal' },
      { data: 'in_bpb' },
      { data: 'reverse_bpb' },
      { data: 'ded_kontrabon' },
      { data: 'reverse_kontrabon' },
      { data: 'gm' },
      { data: 'saldo_akhir' },
      { data: 'rate' },
      { data: 'saldo_akhir_idr' },
      { data: 'no_coa' },
      { data: 'nama_coa' },
      { data: 'item_type1' },
      { data: 'item_type2' },
      { data: 'relasi' },
      { data: null, defaultContent: '' },
      { data: 'due_current' },
      { data: 'due_1_30' },
      { data: 'due_31_60' },
      { data: 'due_61_90' },
      { data: 'due_91_120' },
      { data: 'due_121_180' },
      { data: 'due_181_360' },
      { data: 'due_gt_360' },
      { data: 'total_due' },
      { data: null, defaultContent: '' },
      { data: 'pro_due' },
      { data: 'pro_due0' },
      { data: 'pro_due1' },
      { data: 'pro_due2' },
      { data: 'pro_due3' },
      { data: 'pro_due4' },
      { data: 'pro_due5' },
      { data: 'tot_produe' }
    ],

    columnDefs: [
      {
        targets: [7, 8, 10], // reverse_bpb & reverse_kontrabon
        className: "text-right",
        render: function (data, type) {
          let val = parseFloat(data);
          if (isNaN(val) || val === 0) return '0.00';

          let absVal = Math.abs(val);

          if (type === 'display') {
            return '<span style="color:red;">(' +
              absVal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              }) +
              ')</span>';
          }

          return -absVal;
        }
      },
      {
        targets: [5, 6,
          9, 11,
          13,
          20, 21, 22, 23, 24, 25, 26, 27, 28,
          30, 31, 32, 33, 34, 35, 36,
          37],
        className: "text-right",
        render: function (data) {
          let val = parseFloat(data);
          if (isNaN(val)) return data;

          return val.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
          });
        }
      }
    ],

    footerCallback: function () {
      let api  = this.api();
      let json = api.ajax.json();

      if (!json) return;

      let footer = $(api.table().footer());
      let rowIDR = footer.find('tr:eq(0)');
      let rowUSD = footer.find('tr:eq(1)');
      let rowALL = footer.find('tr:eq(2)');

      function fmt(val) {
        val = parseFloat(val) || 0;
        return val.toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      }

      const map = {
        5:  'saldo_awal',
        6:  'in_bpb',
        7:  'reverse_bpb',
        8:  'ded_kontrabon',
        9:  'reverse_kontrabon',
        10: 'gm',
        11: 'saldo_akhir',
        13: 'saldo_akhir_idr',
        20: 'due_current',
        21: 'due_1_30',
        22: 'due_31_60',
        23: 'due_61_90',
        24: 'due_91_120',
        25: 'due_121_180',
        26: 'due_181_360',
        27: 'due_gt_360',
        28: 'total_due',
        30: 'pro_due',
        31: 'pro_due0',
        32: 'pro_due1',
        33: 'pro_due2',
        34: 'pro_due3',
        35: 'pro_due4',
        36: 'pro_due5',
        37: 'tot_produe'
      };

      Object.keys(map).forEach(function (colIdx) {
        let key = map[colIdx];
        let val = json.footer_idr[key] || 0;
        rowIDR.find('th:eq(' + (colIdx) + ')').html(fmt(val));
      });

      Object.keys(map).forEach(function (colIdx) {
        let key = map[colIdx];
        let val = json.footer_usd[key] || 0;
        rowUSD.find('th:eq(' + (colIdx) + ')').html(fmt(val));
      });

      Object.keys(map).forEach(function (colIdx) {
        let key = map[colIdx];
        let val = json.footer_all[key] || 0;
        rowALL.find('th:eq(' + (colIdx) + ')').html(fmt(val));
      });

      // Catatan: tidak pakai colspan/display:none di sini - cukup
      // kosongkan isi sel kolom 1-4 (utk IDR/USD) atau 1-12 (utk
      // SUMMARY) supaya jumlah sel tetap 1:1 dengan kolom body, biar
      // selalu sejajar dengan header/data di atasnya.
      rowIDR.find('th:eq(0)')
        .html('<b>TOTAL IDR</b>')
        .css({ 'text-align': 'left', 'font-weight': 'bold' });

      rowUSD.find('th:eq(0)')
        .html('<b>TOTAL USD</b>')
        .css({ 'text-align': 'left', 'font-weight': 'bold' });

      rowALL.find('th:eq(0)')
        .html('<b>SUMMARY TOTAL</b>')
        .css({ 'text-align': 'left', 'font-weight': 'bold' });

      for (let i = 1; i <= 4; i++) {
        rowIDR.find('th:eq(' + i + ')').html('');
        rowUSD.find('th:eq(' + i + ')').html('');
      }

      for (let i = 1; i <= 12; i++) {
        rowALL.find('th:eq(' + i + ')').html('');
      }

      rowIDR.find('th:not(:eq(0))').css('text-align', 'right');
      rowUSD.find('th:not(:eq(0))').css('text-align', 'right');
      rowALL.find('th:not(:eq(0))').css('text-align', 'right');
    },

    initComplete: function () {
      this.api().columns.adjust();
    }
  });

  $('#table-pcs-bpb').on('draw.dt', function () {
    datatable.columns.adjust();
  });

  // ===== PAYMENT VOUCHER =====
  let datatable2 = $("#table-pcs-pv").DataTable({
    ordering: false,
    processing: true,
    serverSide: true,
    searching: true,
    info: true,
    autoWidth: false,
    scrollX: false,
    paging: true,

    ajax: {
      url: 'ap_report2/ajx_pcs_payment_voucher2.php',
      type: 'POST',
      data: function (d) {
        d.start_date = $('#start_date').val();
        d.end_date   = $('#end_date').val();
        d.nama_supp  = $('#nama_supp').val();
      }
    },

    columns: [
      { data: 'supplier' },
      { data: 'no_kbon' },
      { data: 'tgl_kbon' },
      { data: 'duedate' },
      { data: 'curr' },
      { data: 'saldo_awal' },
      { data: 'total_in' },
      { data: 'uang_muka' },
      { data: 'potongan' },
      { data: 'ded_bank' },
      { data: 'ded_cash' },
      { data: 'ded_nonbank' },
      { data: 'ded_gm' },
      { data: 'reverse_kontrabon' },
      { data: 'saldo_akhir' },
      { data: 'rate' },
      { data: 'saldo_akhir_idr' },
      { data: 'no_coa' },
      { data: 'nama_coa' },
      { data: 'item_type1' },
      { data: 'item_type2' },
      { data: 'relasi' },
      { data: null, defaultContent: '' },
      { data: 'due_current' },
      { data: 'due_1_30' },
      { data: 'due_31_60' },
      { data: 'due_61_90' },
      { data: 'due_91_120' },
      { data: 'due_121_180' },
      { data: 'due_181_360' },
      { data: 'due_gt_360' },
      { data: 'total_due' },
      { data: null, defaultContent: '' },
      { data: 'pro_due' },
      { data: 'pro_due0' },
      { data: 'pro_due1' },
      { data: 'pro_due2' },
      { data: 'pro_due3' },
      { data: 'pro_due4' },
      { data: 'pro_due5' },
      { data: 'tot_produe' }
    ],

    columnDefs: [
      {
        targets: [7, 8, 13],
        className: "text-right",
        render: function (data, type) {
          let val = parseFloat(data);
          if (isNaN(val) || val === 0) return '0.00';
          let absVal = Math.abs(val);
          if (type === 'display') {
            return '<span style="color:red;">(' +
              absVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
              ')</span>';
          }
          return -absVal;
        }
      },
      {
        targets: [5, 6, 9, 10, 11, 12, 14, 15, 16,
          23, 24, 25, 26, 27, 28, 29, 30, 31,
          33, 34, 35, 36, 37, 38, 39, 40],
        className: "text-right",
        render: function (data) {
          let val = parseFloat(data);
          if (isNaN(val)) return data;
          return val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
      }
    ],

    footerCallback: function () {
      let api  = this.api();
      let json = api.ajax.json();
      if (!json) return;

      let footer = $(api.table().footer());
      let rowIDR = footer.find('tr:eq(0)');
      let rowUSD = footer.find('tr:eq(1)');
      let rowALL = footer.find('tr:eq(2)');

      function fmt(val) {
        val = parseFloat(val) || 0;
        return val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      const map = {
        5: 'saldo_awal', 6: 'total_in', 7: 'uang_muka', 8: 'potongan',
        9: 'ded_bank', 10: 'ded_cash', 11: 'ded_nonbank',
        12: 'ded_gm', 13: 'reverse_kontrabon',
        14: 'saldo_akhir', 16: 'saldo_akhir_idr',
        23: 'due_current', 24: 'due_1_30', 25: 'due_31_60',
        26: 'due_61_90', 27: 'due_91_120', 28: 'due_121_180',
        29: 'due_181_360', 30: 'due_gt_360', 31: 'total_due',
        33: 'pro_due', 34: 'pro_due0', 35: 'pro_due1',
        36: 'pro_due2', 37: 'pro_due3', 38: 'pro_due4',
        39: 'pro_due5', 40: 'tot_produe'
      };

      Object.keys(map).forEach(function (i) {
        rowIDR.find('th:eq(' + i + ')').html(fmt(json.footer_idr[map[i]] || 0));
        rowUSD.find('th:eq(' + i + ')').html(fmt(json.footer_usd[map[i]] || 0));
        rowALL.find('th:eq(' + i + ')').html(fmt(json.footer_all[map[i]] || 0));
      });

      rowIDR.find('th:eq(0)').html('<b>TOTAL IDR</b>').css({'text-align':'left','font-weight':'bold'});
      rowUSD.find('th:eq(0)').html('<b>TOTAL USD</b>').css({'text-align':'left','font-weight':'bold'});
      rowALL.find('th:eq(0)').html('<b>SUMMARY TOTAL</b>').css({'text-align':'left','font-weight':'bold'});

      for (let i = 1; i <= 4;  i++) { rowIDR.find('th:eq('+i+')').html(''); rowUSD.find('th:eq('+i+')').html(''); }
      for (let i = 1; i <= 15; i++) { rowALL.find('th:eq('+i+')').html(''); }

      rowIDR.find('th:not(:eq(0))').css('text-align', 'right');
      rowUSD.find('th:not(:eq(0))').css('text-align', 'right');
      rowALL.find('th:not(:eq(0))').css('text-align', 'right');
    },

    initComplete: function () { this.api().columns.adjust(); }
  });

  $('#table-pcs-pv').on('draw.dt', function () {
    datatable2.columns.adjust();
  });

  // ===== SUMMARY SUPPLIER =====
  let datatable3 = $("#table-pcs-sum-supp").DataTable({
    ordering: false, processing: true, serverSide: true, searching: true,
    info: true, autoWidth: false, scrollX: false, paging: true,
    ajax: { url: 'ap_report2/ajx_pcs_sum_supp2.php', type: 'POST',
      data: function(d){ d.start_date=$('#start_date').val(); d.end_date=$('#end_date').val(); d.nama_supp=$('#nama_supp').val(); }
    },
    columns: [
      {data:'supplier'},{data:'curr'},{data:'saldo_awal'},{data:'addition'},
      {data:'reverse'},{data:'deduction_advance'},{data:'deduction_other'},
      {data:'ded_bank'},{data:'ded_cash'},{data:'ded_nonbank'},
      {data:'deduction_gm'},{data:'adjust'},
      {data:'saldo_akhir'},{data:'rate'},{data:'saldo_akhir_idr'},
      {data:null,defaultContent:''},
      {data:'due_current'},{data:'due_1_30'},{data:'due_31_60'},{data:'due_61_90'},
      {data:'due_91_120'},{data:'due_121_180'},{data:'due_181_360'},{data:'due_gt_360'},{data:'total_due'},
      {data:null,defaultContent:''},
      {data:'pro_due'},{data:'pro_due0'},{data:'pro_due1'},{data:'pro_due2'},
      {data:'pro_due3'},{data:'pro_due4'},{data:'pro_due5'},{data:'tot_produe'}
    ],
    columnDefs: [{
      targets: [2,3,4,5,6,7,8,9,10,11,12,13,14,16,17,18,19,20,21,22,23,24,26,27,28,29,30,31,32,33],
      className:"text-right",
      render: function(data){ let v=parseFloat(data); return isNaN(v)?data:v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    }],
    footerCallback: function(){
      let api=this.api(), json=api.ajax.json(); if(!json) return;
      let f=$(api.table().footer());
      let rI=f.find('tr:eq(0)'), rU=f.find('tr:eq(1)'), rA=f.find('tr:eq(2)');
      function fmt(v){ v=parseFloat(v)||0; return v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
      const map={2:'saldo_awal',3:'addition',4:'reverse',5:'deduction_advance',6:'deduction_other',7:'ded_bank',8:'ded_cash',9:'ded_nonbank',10:'deduction_gm',11:'adjust',12:'saldo_akhir',14:'saldo_akhir_idr',16:'due_current',17:'due_1_30',18:'due_31_60',19:'due_61_90',20:'due_91_120',21:'due_121_180',22:'due_181_360',23:'due_gt_360',24:'total_due',26:'pro_due',27:'pro_due0',28:'pro_due1',29:'pro_due2',30:'pro_due3',31:'pro_due4',32:'pro_due5',33:'tot_produe'};
      Object.keys(map).forEach(function(i){ rI.find('th:eq('+i+')').html(fmt(json.footer_idr[map[i]]||0)); rU.find('th:eq('+i+')').html(fmt(json.footer_usd[map[i]]||0)); rA.find('th:eq('+i+')').html(fmt(json.footer_all[map[i]]||0)); });
      rI.find('th:eq(0)').html('<b>TOTAL IDR</b>').css({'text-align':'left','font-weight':'bold'});
      rU.find('th:eq(0)').html('<b>TOTAL USD</b>').css({'text-align':'left','font-weight':'bold'});
      rA.find('th:eq(0)').html('<b>SUMMARY TOTAL</b>').css({'text-align':'left','font-weight':'bold'});
      for(let i=1;i<=1;i++){rI.find('th:eq('+i+')').html('');rU.find('th:eq('+i+')').html('');}
      for(let i=1;i<=13;i++){rA.find('th:eq('+i+')').html('');}
      rI.find('th:not(:eq(0))').css('text-align','right'); rU.find('th:not(:eq(0))').css('text-align','right'); rA.find('th:not(:eq(0))').css('text-align','right');
    },
    initComplete: function(){ this.api().columns.adjust(); }
  });
  $('#table-pcs-sum-supp').on('draw.dt', function(){ datatable3.columns.adjust(); });

  // ===== ITEM TYPE 1 =====
  let datatable4 = $("#table-pcs-type1").DataTable({
    ordering: false, processing: true, serverSide: true, searching: true,
    info: true, autoWidth: false, scrollX: false, paging: true,
    ajax: { url: 'ap_report2/ajx_pcs_type1_v2.php', type: 'POST',
      data: function(d){ d.start_date=$('#start_date').val(); d.end_date=$('#end_date').val(); d.nama_supp=$('#nama_supp').val(); }
    },
    columns: [
      {data:'supplier'},{data:'item_type1'},{data:'relasi'},
      {data:'saldo_akhir'},{data:'saldo_akhir_persen'},
      {data:null,defaultContent:''},
      {data:'due_current'},{data:'due_1_30'},{data:'due_31_60'},{data:'due_61_90'},
      {data:'due_91_120'},{data:'due_121_180'},{data:'due_181_360'},{data:'due_gt_360'},{data:'total_due'},
      {data:null,defaultContent:''},
      {data:'pro_due'},{data:'pro_due0'},{data:'pro_due1'},{data:'pro_due2'},
      {data:'pro_due3'},{data:'pro_due4'},{data:'pro_due5'},{data:'tot_produe'}
    ],
    columnDefs: [{
      targets: [3,6,7,8,9,10,11,12,13,14,16,17,18,19,20,21,22,23],
      className:"text-right",
      render: function(data){ let v=parseFloat(data); return isNaN(v)?data:v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    }],
    footerCallback: function(){
      let api=this.api(), json=api.ajax.json(); if(!json||!json.footer) return;
      function fmt(v){ v=parseFloat(v)||0; return v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
      const map={3:'saldo_akhir_idr',6:'due_current',7:'due_1_30',8:'due_31_60',9:'due_61_90',10:'due_91_120',11:'due_121_180',12:'due_181_360',13:'due_gt_360',14:'total_due',16:'pro_due',17:'pro_due0',18:'pro_due1',19:'pro_due2',20:'pro_due3',21:'pro_due4',22:'pro_due5',23:'tot_produe'};
      Object.keys(map).forEach(function(i){ $(api.column(i).footer()).html(fmt(json.footer[map[i]])); });
      $(api.column(0).footer()).html('<b>TOTAL</b>');
    },
    initComplete: function(){ this.api().columns.adjust(); }
  });
  $('#table-pcs-type1').on('draw.dt', function(){ datatable4.columns.adjust(); });

  // ===== ITEM TYPE 2 =====
  let datatable5 = $("#table-pcs-type2").DataTable({
    ordering: false, processing: true, serverSide: true, searching: true,
    info: true, autoWidth: false, scrollX: false, paging: true,
    ajax: { url: 'ap_report2/ajx_pcs_type2_v2.php', type: 'POST',
      data: function(d){ d.start_date=$('#start_date').val(); d.end_date=$('#end_date').val(); d.nama_supp=$('#nama_supp').val(); }
    },
    columns: [
      {data:'supplier'},{data:'item_type2'},{data:'relasi'},
      {data:'saldo_akhir'},{data:'saldo_akhir_persen'},
      {data:null,defaultContent:''},
      {data:'due_current'},{data:'due_1_30'},{data:'due_31_60'},{data:'due_61_90'},
      {data:'due_91_120'},{data:'due_121_180'},{data:'due_181_360'},{data:'due_gt_360'},{data:'total_due'},
      {data:null,defaultContent:''},
      {data:'pro_due'},{data:'pro_due0'},{data:'pro_due1'},{data:'pro_due2'},
      {data:'pro_due3'},{data:'pro_due4'},{data:'pro_due5'},{data:'tot_produe'}
    ],
    columnDefs: [{
      targets: [3,6,7,8,9,10,11,12,13,14,16,17,18,19,20,21,22,23],
      className:"text-right",
      render: function(data){ let v=parseFloat(data); return isNaN(v)?data:v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    }],
    footerCallback: function(){
      let api=this.api(), json=api.ajax.json(); if(!json||!json.footer) return;
      function fmt(v){ v=parseFloat(v)||0; return v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
      const map={3:'saldo_akhir_idr',6:'due_current',7:'due_1_30',8:'due_31_60',9:'due_61_90',10:'due_91_120',11:'due_121_180',12:'due_181_360',13:'due_gt_360',14:'total_due',16:'pro_due',17:'pro_due0',18:'pro_due1',19:'pro_due2',20:'pro_due3',21:'pro_due4',22:'pro_due5',23:'tot_produe'};
      Object.keys(map).forEach(function(i){ $(api.column(i).footer()).html(fmt(json.footer[map[i]])); });
      $(api.column(0).footer()).html('<b>TOTAL</b>');
    },
    initComplete: function(){ this.api().columns.adjust(); }
  });
  $('#table-pcs-type2').on('draw.dt', function(){ datatable5.columns.adjust(); });

  // ===== SUMMARY GROUP =====
  let datatable6 = $("#table-pcs-sum-grup").DataTable({
    ordering: false, processing: true, serverSide: false, searching: false,
    info: false, autoWidth: false, scrollX: false, paging: false,
    ajax: { url: 'ap_report2/ajx_pcs_sum_grup2.php', type: 'POST',
      data: function(d){ d.start_date=$('#start_date').val(); d.end_date=$('#end_date').val(); d.nama_supp=$('#nama_supp').val(); }
    },
    columns: [
      {data:'supplier'},{data:'curr'},{data:'saldo_akhir'},{data:'saldo_akhir_idr'},
      {data:null,defaultContent:''},
      {data:'due_current'},{data:'due_1_30'},{data:'due_31_60'},{data:'due_61_90'},
      {data:'due_91_120'},{data:'due_121_180'},{data:'due_181_360'},{data:'due_gt_360'},{data:'total_due'},
      {data:null,defaultContent:''},
      {data:'pro_due'},{data:'pro_due0'},{data:'pro_due1'},{data:'pro_due2'},
      {data:'pro_due3'},{data:'pro_due4'},{data:'pro_due5'},{data:'tot_produe'}
    ],
    columnDefs: [{
      targets: [2,3,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22],
      className:"text-right",
      render: function(data){ let v=parseFloat(data); return isNaN(v)?data:v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    }],
    footerCallback: function(){
      let api=this.api(), json=api.ajax.json(); if(!json) return;
      let f=$(api.table().footer());
      let rI=f.find('tr:eq(0)'), rU=f.find('tr:eq(1)'), rA=f.find('tr:eq(2)');
      function fmt(v){ v=parseFloat(v)||0; return v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
      const map={2:'saldo_akhir',3:'saldo_akhir_idr',5:'due_current',6:'due_1_30',7:'due_31_60',8:'due_61_90',9:'due_91_120',10:'due_121_180',11:'due_181_360',12:'due_gt_360',13:'total_due',15:'pro_due',16:'pro_due0',17:'pro_due1',18:'pro_due2',19:'pro_due3',20:'pro_due4',21:'pro_due5',22:'tot_produe'};
      Object.keys(map).forEach(function(i){ rI.find('th:eq('+i+')').html(fmt(json.footer_idr[map[i]]||0)); rU.find('th:eq('+i+')').html(fmt(json.footer_usd[map[i]]||0)); rA.find('th:eq('+i+')').html(fmt(json.footer_all[map[i]]||0)); });
      rI.find('th:eq(0)').html('<b>TOTAL IDR</b>').css({'text-align':'left','font-weight':'bold'});
      rU.find('th:eq(0)').html('<b>TOTAL USD</b>').css({'text-align':'left','font-weight':'bold'});
      rA.find('th:eq(0)').html('<b>SUMMARY GROUP</b>').css({'text-align':'left','font-weight':'bold'});
      for(let i=1;i<=1;i++){rI.find('th:eq('+i+')').html('');rU.find('th:eq('+i+')').html('');}
      for(let i=1;i<=2;i++){rA.find('th:eq('+i+')').html('');}
      rI.find('th:not(:eq(0))').css('text-align','right'); rU.find('th:not(:eq(0))').css('text-align','right'); rA.find('th:not(:eq(0))').css('text-align','right');
    },
    initComplete: function(){ this.api().columns.adjust(); }
  });
  $('#table-pcs-sum-grup').on('draw.dt', function(){ datatable6.columns.adjust(); });

  // ===== SUMMARY NON GROUP =====
  let datatable7 = $("#table-pcs-sum-non-grup").DataTable({
    ordering: false, processing: true, serverSide: false, searching: false,
    info: false, autoWidth: false, scrollX: false, paging: false,
    ajax: { url: 'ap_report2/ajx_pcs_sum_non_grup2.php', type: 'POST',
      data: function(d){ d.start_date=$('#start_date').val(); d.end_date=$('#end_date').val(); d.nama_supp=$('#nama_supp').val(); }
    },
    columns: [
      {data:'item_type2'},{data:'curr'},{data:'saldo_akhir'},{data:'saldo_akhir_idr'},
      {data:null,defaultContent:''},
      {data:'due_current'},{data:'due_1_30'},{data:'due_31_60'},{data:'due_61_90'},
      {data:'due_91_120'},{data:'due_121_180'},{data:'due_181_360'},{data:'due_gt_360'},{data:'total_due'},
      {data:null,defaultContent:''},
      {data:'pro_due'},{data:'pro_due0'},{data:'pro_due1'},{data:'pro_due2'},
      {data:'pro_due3'},{data:'pro_due4'},{data:'pro_due5'},{data:'tot_produe'}
    ],
    columnDefs: [{
      targets: [2,3,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22],
      className:"text-right",
      render: function(data){ let v=parseFloat(data); return isNaN(v)?data:v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
    }],
    footerCallback: function(){
      let api=this.api(), json=api.ajax.json(); if(!json) return;
      let f=$(api.table().footer());
      let rI=f.find('tr:eq(0)'), rU=f.find('tr:eq(1)'), rA=f.find('tr:eq(2)');
      let sI=f.find('tr:eq(4)'), sU=f.find('tr:eq(5)'), sA=f.find('tr:eq(6)');
      function fmt(v){ v=parseFloat(v)||0; return v.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
      const map={2:'saldo_akhir',3:'saldo_akhir_idr',5:'due_current',6:'due_1_30',7:'due_31_60',8:'due_61_90',9:'due_91_120',10:'due_121_180',11:'due_181_360',12:'due_gt_360',13:'total_due',15:'pro_due',16:'pro_due0',17:'pro_due1',18:'pro_due2',19:'pro_due3',20:'pro_due4',21:'pro_due5',22:'tot_produe'};
      Object.keys(map).forEach(function(i){
        rI.find('th:eq('+i+')').html(fmt(json.footer_idr[map[i]]||0));
        rU.find('th:eq('+i+')').html(fmt(json.footer_usd[map[i]]||0));
        rA.find('th:eq('+i+')').html(fmt(json.footer_all[map[i]]||0));
        sI.find('th:eq('+i+')').html(fmt(json.footer_sum_idr[map[i]]||0));
        sU.find('th:eq('+i+')').html(fmt(json.footer_sum_usd[map[i]]||0));
        sA.find('th:eq('+i+')').html(fmt(json.footer_sum_all[map[i]]||0));
      });
      rI.find('th:eq(0)').html('<b>TOTAL IDR</b>').css({'text-align':'left','font-weight':'bold'});
      rU.find('th:eq(0)').html('<b>TOTAL USD</b>').css({'text-align':'left','font-weight':'bold'});
      rA.find('th:eq(0)').html('<b>SUMMARY NON GROUP</b>').css({'text-align':'left','font-weight':'bold'});
      sI.find('th:eq(0)').html('<b>SUMMARY TOTAL IDR</b>').css({'text-align':'left','font-weight':'bold'});
      sU.find('th:eq(0)').html('<b>SUMMARY TOTAL USD</b>').css({'text-align':'left','font-weight':'bold'});
      sA.find('th:eq(0)').html('<b>SUMMARY TOTAL</b>').css({'text-align':'left','font-weight':'bold'});
      for(let i=1;i<=1;i++){rI.find('th:eq('+i+')').html('');rU.find('th:eq('+i+')').html('');sI.find('th:eq('+i+')').html('');sU.find('th:eq('+i+')').html('');}
      for(let i=1;i<=2;i++){rA.find('th:eq('+i+')').html('');sA.find('th:eq('+i+')').html('');}
      [rI,rU,rA,sI,sU,sA].forEach(function(r){ r.find('th:not(:eq(0))').css('text-align','right'); });
    },
    initComplete: function(){ this.api().columns.adjust(); }
  });
  $('#table-pcs-sum-non-grup').on('draw.dt', function(){ datatable7.columns.adjust(); });

  function dataTableReload() {
    loadProjectionHeader();
    datatable.ajax.reload(()=>{ datatable.columns.adjust(); });
    datatable2.ajax.reload(()=>{ datatable2.columns.adjust(); });
    datatable3.ajax.reload(()=>{ datatable3.columns.adjust(); });
    datatable4.ajax.reload(()=>{ datatable4.columns.adjust(); });
    datatable5.ajax.reload(()=>{ datatable5.columns.adjust(); });
    datatable6.ajax.reload(()=>{ datatable6.columns.adjust(); });
    datatable7.ajax.reload(()=>{ datatable7.columns.adjust(); });
  }

  // Jaga-jaga supaya form filter tidak pernah benar-benar submit/reload
  // halaman (mis. Enter di field tanggal) - Search harus selalu lewat AJAX
  // seperti di payable_card_statement.php.
  $('#form-data').on('submit', function (e) {
    e.preventDefault();
    dataTableReload();
  });
</script>

<script type="text/javascript">
  document.getElementById('btnExportExcel_bpb').addEventListener('click', function () {
    let sd = document.getElementById('start_date').value;
    let ed = document.getElementById('end_date').value;
    let supp = document.getElementById('nama_supp').value;

    this.href = `ap_report2/ekspor_pcs_bpb2.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
  });

  document.getElementById('btnExportExcel_pv').addEventListener('click', function () {
    let sd = document.getElementById('start_date').value;
    let ed = document.getElementById('end_date').value;
    let supp = document.getElementById('nama_supp').value;
    this.href = `ap_report2/ekspor_pcs_payment_voucher2.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
  });

  document.getElementById('btnExportExcel_sum_supp').addEventListener('click', function () {
    let sd = document.getElementById('start_date').value;
    let ed = document.getElementById('end_date').value;
    let supp = document.getElementById('nama_supp').value;
    this.href = `ap_report2/ekspor_pcs_sum_supp2.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
  });

  document.getElementById('btnExportExcel_type1').addEventListener('click', function () {
    let sd = document.getElementById('start_date').value;
    let ed = document.getElementById('end_date').value;
    let supp = document.getElementById('nama_supp').value;
    this.href = `ap_report2/ekspor_pcs_type1_v2.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
  });

  document.getElementById('btnExportExcel_type2').addEventListener('click', function () {
    let sd = document.getElementById('start_date').value;
    let ed = document.getElementById('end_date').value;
    let supp = document.getElementById('nama_supp').value;
    this.href = `ap_report2/ekspor_pcs_type2_v2.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
  });

  document.getElementById('btnExportExcel_sum_grup').addEventListener('click', function () {
    let sd = document.getElementById('start_date').value;
    let ed = document.getElementById('end_date').value;
    let supp = document.getElementById('nama_supp').value;
    this.href = `ap_report2/ekspor_pcs_sum_grup2.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;
  });

  // ===== Export All (multi-tab) =====
  document.getElementById('btnExportAll').addEventListener('click', async function () {
    let btn = this;
    let text = document.getElementById('btnText');
    let sd = document.getElementById('start_date').value;
    let ed = document.getElementById('end_date').value;
    let supp = document.getElementById('nama_supp').value;
    let url = `ap_report2/ekspor_pcs_all2.php?start_date=${sd}&end_date=${ed}&nama_supp=${supp}`;

    btn.style.opacity = '0.6';
    btn.style.pointerEvents = 'none';
    text.innerHTML = ' Loading...';

    Swal.fire({
      title: 'Exporting...',
      text: 'Sedang generate file Excel',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error('Gagal export');
      const blob = await response.blob();
      const link = document.createElement('a');
      link.href = window.URL.createObjectURL(blob);
      link.download = 'Payable_Card_Statement.xlsx';
      document.body.appendChild(link);
      link.click();
      link.remove();
      Swal.fire({ icon:'success', title:'Berhasil!', text:'File berhasil di export', timer:1500, showConfirmButton:false });
    } catch (error) {
      Swal.fire({ icon:'error', title:'Gagal!', text:error.message });
    }

    btn.style.opacity = '1';
    btn.style.pointerEvents = 'auto';
    text.innerHTML = ' Excel';
  });
</script>
