<?php include '../header.php' ?>

<style type="text/css">
     label {
    font-size: 14px;
    ;
  }

  input {
    font-size: 14px;
    ;
  }


  .tabcontent {
    display: none;
    animation: fadeEffect 0.3s;
  }

  @keyframes fadeEffect {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  .tab-container {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px;
    background: #f4f6f9;
    border-radius: 10px;
    border: 1px solid #ddd;
  }

  .tablinks {
    border: none;
    background: #ffffff;
    color: #555;
    padding: 8px 14px;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }

  .tablinks:hover {
    background: #007bff;
    color: #fff;
    transform: translateY(-1px);
  }

  .tablinks.active {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: #fff;
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.35);
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

  .select2-container .select2-selection--single {
    height: calc(2.25rem + 2px);
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2.25rem;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(2.25rem + 2px);
  }

  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }

  div.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 10px;
  }

  div.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 10px;
  }

  #mytablenone .form-control {
    width: 100% !important;
  }

  #mytablenone .bootstrap-select {
    width: 100% !important;
  }

  .total-box{
    border:1px solid #dcdcdc;
    border-radius:6px;
    padding:15px;
    background:#fafafa;
}

.total-box h6{
    font-weight:bold;
    margin-bottom:15px;
    border-bottom:1px solid #ddd;
    padding-bottom:5px;
}

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-file-alt"></i> GENERAL LEDGER</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="general_ledger.php" method="post">
    <div class="row g-3">
      <!-- Supplier -->
      <div class="col-md-4">
        <label for="nama_type"><b>No COA</b></label>   
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
        <label for="nama_type"><b>Profit Center</b></label>   
            <select class="form-control select2" name="profit_center" id="profit_center" data-live-search="true">
                <option value="-">Select Profit Center</option>
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

    <!-- Spacer biar rata -->
    <div class="col-md-5"></div>

    <div class="col-md-2 mb-2">
        <label><b>From</b></label>
        <input type="text" name="start_date" id="start_date" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
    </div>
    <div class="col-md-2 mb-2">
        <label><b>To</b></label>
        <input type="text" name="end_date" id="end_date" class="form-control tanggal" value="<?php echo date("d-m-Y"); ?>" autocomplete="off">
    </div>

    <div class="col-md-2 mb-2 d-flex align-items-end">
        <button type="button" class="btn btn-info btn-sm mr-2" onclick="dataTableReload()">
            <i class="fa fa-search"></i> Search
        </button>

        <button type="button" class="btn btn-success btn-sm" onclick="ExportGL()">
            <i class="fa fa-file-excel"></i> Excel
        </button>
    </div>   

</div>

</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="table-data" 
          class="table table-striped table-bordered table-hover table-sm nowrap" >
          <thead class="table-gradient">
            <tr>
                <th style="text-align: center;vertical-align: middle;">No Journal</th>
                <th style="text-align: center;vertical-align: middle;">Date</th>
                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                <th style="text-align: center;vertical-align: middle;">Reff Document</th>
                <th style="text-align: center;vertical-align: middle;">Descriptions</th>
                <th style="text-align: center;vertical-align: middle;">Debit</th>
                <th style="text-align: center;vertical-align: middle;">Credit</th>
                <th style="text-align: center;vertical-align: middle;">Saldo</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>
</div>
</div>

<!-- CSS -->
<style>
  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
div.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 10px;
}
div.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 10px;
}


</style>


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
  function toYmd(dmy) {
    if (!dmy) return '';
    let p = dmy.split('-'); // [dd, mm, yyyy]
    return `${p[2]}-${p[1]}-${p[0]}`;
  }


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
            }
            ],

initComplete: function () {
  this.api().columns.adjust();
}
});


$('#table-pcs-bpb').on('draw.dt', function () {
  datatable.columns.adjust();
});

$("[data-toggle=tooltip]").tooltip();

function dataTableReload() {
  datatable.ajax.reload(()=>{
    datatable.columns.adjust();
  });
}


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
