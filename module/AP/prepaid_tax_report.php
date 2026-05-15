<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-file-invoice"></i> PREPAID TAX REPORT</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="prepaid_tax_report.php" method="post">
    <div class="row g-3">
      <!-- Supplier -->
      <div class="col-md-3">
        <label for="profit_center" class="form-label"><b>Profit Center</b></label>
        <select class="form-control selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" selected="true">ALL</option>                                                
            <?php
            $profit_center ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: null;
            }                 
            $sql = mysqli_query($conn1,"select kode_pc, CONCAT(id_pc,' - ',nama_pc) nama_pc from master_pc");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['kode_pc'];
                $tampil = $row['nama_pc'];
                if($row['kode_pc'] == $_POST['profit_center']){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
        </select>
    </div>

    <!-- Supplier -->
      <div class="col-md-3">
        <label for="no_coa" class="form-label"><b>No Coa</b></label>
        <select class="form-control selectpicker" name="no_coa" id="no_coa" data-dropup-auto="false" data-live-search="true">
            <option value="" selected disabled>Please select No COA</option>                                                
            <?php
            $no_coa ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $no_coa = isset($_POST['no_coa']) ? $_POST['no_coa']: null;
            }                 
            $sql = mysqli_query($conn1,"select no_coa, CONCAT(no_coa,' - ',nama_coa) nama_coa from mastercoa_v2 where eng_categori4 = 'PREPAID TAX'");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['no_coa'];
                $tampil = $row['nama_coa'];
                if($row['no_coa'] == $_POST['no_coa']){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
        </select>
    </div>

    <!-- Start Date -->
    <div class="col-md-2
    ">
        <label for="start_date" class="form-label"><b>From</b></label>
        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
        value="<?php
        $start_date ='';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           $start_date = date("Y-m-d",strtotime($_POST['start_date']));
       }
       if(!empty($_POST['start_date'])) {
           echo $_POST['start_date'];
       }
       else{
           echo date("d-m-Y");
       } ?>" placeholder="Start Date" autocomplete="off">
   </div>

   <!-- End Date -->
   <div class="col-md-2
   ">
    <label for="end_date" class="form-label"><b>To</b></label>
    <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
    value="<?php
    $end_date ='';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $end_date = date("Y-m-d",strtotime($_POST['end_date']));
   }
   if(!empty($_POST['end_date'])) {
       echo $_POST['end_date'];
   }
   else{
       echo date("d-m-Y");
   } ?>"  placeholder="End Date" autocomplete="off">
</div>

<!-- Tombol -->
<div class="col-md-2 d-flex align-items-end">
  <button type="button" class="btn btn-info btn-sm me-2" onclick="dataTableReload()">
        <i class="fa fa-search"></i> Search
      </button>
<a id="btnExportPrepaidTax" target="_blank">
    <button type="button" class="btn btn-success btn-xs ml-2" style="margin-top: 30px;">
        <i class="fa fa-file-excel-o" aria-hidden="true" > Excel</i>
    </button>
</a>
  

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
                <th style="text-align: center;vertical-align: middle;">Date</th>
                <th style="text-align: center;vertical-align: middle;">Journal No</th>
                <th style="text-align: center;vertical-align: middle;">Supplier Inv No</th>
                <th style="text-align: center;vertical-align: middle;">Description</th>
                <th style="text-align: center;vertical-align: middle;">Supplier Name</th>
                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                <th style="text-align: center;vertical-align: middle;">Beginning Balance</th>
                <th style="text-align: center;vertical-align: middle;">Addition (Purchase)</th>
                <th style="text-align: center;vertical-align: middle;">Deduction (SI)</th>
                <th style="text-align: center;vertical-align: middle;">Deduction (GM)</th>
                <th style="text-align: center;vertical-align: middle;">Ending Balance</th>
                <th style="text-align: center;vertical-align: middle;">Remarks</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <tr>
            <th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th>
        </tr>
    </tfoot>
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

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-6 mb-2"></div>
          <div id="txt_no_po" class="col-md-6 mb-2"></div>
          <div id="txt_supp" class="col-md-6 mb-2"></div>
          <div id="txt_top" class="col-md-6 mb-2"></div>
          <div id="txt_curr" class="col-md-6 mb-2"></div>
          <div id="txt_confirm" class="col-md-6 mb-2"></div>
          <div id="txt_confirm2" class="col-md-6 mb-2"></div>
          <div id="txt_tgl_po" class="col-md-6 mb-2"></div>
          <div id="details" class="col-12 mt-2"></div>
      </div>
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
<script>
    $(document).ready(function() {
        $('#mytable').DataTable({
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: false 
        });

        $("[data-toggle=tooltip]").tooltip();
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2022",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
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
    serverSide: true,
    searching: true,
    info: true,
    autoWidth: false,
    scrollX: false,
    fixedColumns: {
        leftColumns: 5 // sampai kolom curr
      },
      paging: true,

      ajax: {
        url: 'ajx_prepaid_tax.php',
        type: 'POST',
        data: function (d) {
          d.profit_center   = $('#profit_center').val();
          d.no_coa          = $('#no_coa').val();
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
        }
      },

      columns: [
      { data: 'tgl_journal' },
      { data: 'no_journal' },
      { data: 'no_kbon' },
      { data: 'deskripsi' },
      { data: 'supplier' },
      { data: 'profit_center' },
      { data: 'saldo_awal' },
      { data: 'total_in' },
      { data: 'total_out' },
      { data: 'total_gm' },
      { data: 'saldo_akhir' },
      { data: 'remark' },
      ],

      columnDefs: [

            {
              targets: [6, 7],
              className: "text-right",
              render: function (data) {
                let val = parseFloat(data);
                if (isNaN(val)) return data;

                return val.toLocaleString('en-US', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
                });
              }
            },
            {
    targets: [8, 9, 10],
    className: "text-right",
    render: function (data) {
      let val = parseFloat(data);
      if (isNaN(val)) return data;

      let formatted = Math.abs(val).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });

      if (val < 0) {
        return '<span style="color:red;">(' + formatted + ')</span>';
      }

      return formatted;
    }
  }
            ],

            footerCallback: function () {
    let api = this.api();
    let json = api.ajax.json();

    if (!json || !json.footer) return;

    function fmtAccounting(val) {
        val = parseFloat(val) || 0;

        let formatted = Math.abs(val).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        if (val < 0) {
            return '<span style="color:red;">(' + formatted + ')</span>';
        }
        return formatted;
    }

    const map = {
        6: 'saldo_awal',
        7: 'total_in',
        8: 'total_out',
        9: 'total_gm',
        10: 'saldo_akhir',
    };

    Object.keys(map).forEach(function (colIdx) {
        let key = map[colIdx];
        let val = json.footer[key];

        $(api.column(colIdx).footer()).html(fmtAccounting(val));
    });

    $(api.column(0).footer()).html('<b>TOTAL</b>');
}

,

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

document.getElementById('btnExportPrepaidTax').addEventListener('click', function(e) {
  let start_date = toYmd(document.getElementById('start_date').value);
  let end_date = toYmd(document.getElementById('end_date').value);
  let profit_center = document.getElementById('profit_center').value;
  let no_coa = document.getElementById('no_coa').value;
  // alert(profit_center + '|' + no_coa + '|' + start_date + '|' + end_date);

  this.href = `ekspor_prepaid_tax.php?profit_center=${profit_center}&no_coa=${no_coa}&start_date=${start_date}&end_date=${end_date}`;
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "prepaid_tax_report.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "prepaid_tax_report.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
