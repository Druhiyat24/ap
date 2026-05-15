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
    <h5 class="mb-0"><i class="fas fa-archive"></i> REPOST JOURNAL - BPB</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="repost-bpb.php" method="post">
    <div class="row g-3">

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
    <button type="button" class="btn btn-success btn-xs ml-2" style="margin-top: 30px;" onclick="RepostJurnal()">
        <i class="fas fa-paper-plane"></i> Repost
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
                <th style="text-align: center;vertical-align: middle;"><input type="checkbox" id="select_all"></th>
                <th style="text-align: center;vertical-align: middle;">Bpb Number</th>
                <th style="text-align: center;vertical-align: middle;">Bpb Date</th>
                <th style="text-align: center;vertical-align: middle;">Supplier</th>
                <th style="text-align: center;vertical-align: middle;">PO</th>
                <th style="text-align: center;vertical-align: middle;">Type PO</th>
                <th style="text-align: center;vertical-align: middle;">Curr</th>
                <th style="text-align: center;vertical-align: middle;">Total BPB</th>
                <th style="text-align: center;vertical-align: middle;">Total Journal</th>
                <th style="text-align: center;vertical-align: middle;">Different Total</th>
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

    $.ajax({
        url: 'get_min_date.php',
        type: 'GET',
        dataType: 'json',
        success: function(res){
          console.log(res.tgl_awal);

            $('.tanggal').datepicker({
                format: "dd-mm-yyyy",
                startDate : res.tgl_awal, // dari database
                autoclose:true
            });

        }
    });

});
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
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
        url: 'ajx_repost_bpb.php',
        type: 'POST',
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
        }
      },

      columns: [
      { data: 'bpbno_int' },
      { data: 'bpbno_int' },
      { data: 'bpbdate' },
      { data: 'supplier' },
      { data: 'pono' },
      { data: 'tipe_com' },
      { data: 'curr' },
      { data: 'total' },
      { data: 'total_jurnal' },
      { data: 'diff_total' },
      ],

      columnDefs: [
      {
            targets: 0,
            orderable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
                return `
                    <input type="checkbox" class="row-check" value="${row.bpbno_int}"> `;
            }
        },

            {
              targets: [7, 8, 9],
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

function RepostJurnal() {

    let selected = [];

    $('.row-check:checked').each(function () {
        selected.push($(this).val());
    });

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Pilih minimal 1 data!'
        });
        return;
    }

    console.log(selected);

    Swal.fire({
        title: 'Yakin repost jurnal?',
        text: "Jurnal lama akan dipindahkan ke tabel cancel!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Repost!',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Processing...',
                html: 'Sedang repost jurnal...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'proses_repost_bpb.php',
                type: 'POST',
                data: { bpb_list: selected },
                success: function (res) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res
                    });

                    datatable.ajax.reload();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan server'
                    });
                }
            });

        }

    });
}



document.getElementById('btnExportData').addEventListener('click', function(e) {
  let start_date = toYmd(document.getElementById('start_date').value);
  let end_date = toYmd(document.getElementById('end_date').value);
  let status = document.getElementById('status').value;
  // alert(profit_center + '|' + no_coa + '|' + start_date + '|' + end_date);

  this.href = `ekspor_repost-bpb.php?status=${status}&start_date=${start_date}&end_date=${end_date}`;
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "repost-bpb.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "repost-bpb.php";
    };
</script>
<script type="text/javascript">
  $("#select_all").click(function() {
    var c = this.checked;
    $(':checkbox').prop('checked', c);
  });  
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
