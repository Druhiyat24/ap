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
    <h5 class="mb-0"><i class="fa fa-sign-in" aria-hidden="true"></i> FABRIC TRANSACTION BARCODE OUT</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="ca_fabric_trx_out_barcode.php" method="post">
    <div class="row g-3">

        <!-- Start Date -->
        <div class="col-md-2">
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
       <div class="col-md-2">
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
   <div class="col-md-3 d-flex align-items-end">
      <button type="button" onclick="dataTableReload()" class="btn btn-info btn-sm me-2">
        <i class="fa fa-search"></i> Search
    </button>
    <button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
        <i class="fa fa-undo"></i> Reset
    </button>

    <a id="btnExportExcel" target="_blank">
    <button type="button" class="btn btn-success ml-2" style="margin-top: 30px;">
        <i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i>
    </button>
</a>    

</div>


    <?php
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

    // echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_ca_fabric_trx_out_barcode.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success ml-2" style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';

    ?>     

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="mytable" 
          class="table table-striped table-bordered table-hover table-sm nowrap" 
          style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
                        <th >No Trans</th>
                        <th >Tgl Trans</th>
                        <th >No Barcode</th>
                        <th >No Roll</th>
                        <th >No Lot</th>
                        <th >Lokasi</th>
                        <th >Id Item</th>
                        <th >Nama Barang</th>
                        <th >Warna</th>
                        <th >Ukuran</th>
                        <th >No Inv</th>
                        <th >Jenis Dok</th>
                        <th >Nomor Aju</th>
                        <th >Tgl Aju</th>
                        <th >Nomor Daftar</th>
                        <th >Tgl Daftar</th>
                        <th >Supplier</th>
                        <th >Jumlah</th>
                        <th >Satuan</th>
                        <th >Berat Bersih</th>
                        <th >Keterangan</th>
                        <th >Nama User</th>
                        <th >WS</th>
                        <th >Style</th>
                        <th >Curr</th>
                        <th >Price</th>
                        <th >Status</th>
                        <th >Nilai Barang / Unit Dalam Mata Uang Asal</th>
                        <th >Nilai Barang Dalam Mata Uang Asal</th>
                        <th >Rate</th>
                        <th >Nilai Barang Ekuivalen IDR</th>
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
    function toYmd(dmy) {
    if (!dmy) return '';
    let p = dmy.split('-'); // [dd, mm, yyyy]
    return `${p[2]}-${p[1]}-${p[0]}`;
}

         let datatable = $("#mytable").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            pageLength: 10,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: false,
        ajax: {
            url: 'http://localhost:8081/nds_wip/public/index.php/api/out-barcode-fabric/out-material/out-barcode-fabric',
            dataType: 'json',
            dataSrc: 'data',
            method: 'POST',
            data: function(d) {
                d.start_date = toYmd($('#start_date').val());
                d.end_date   = toYmd($('#end_date').val());

            },
        },
        columns: [{
                data: 'no_bppb'
            },
            {
                data: 'tgl_bppb'
            },
            {
                data: 'id_roll'
            },
            {
                data: 'no_roll'
            },
            {
                data: 'no_lot'
            },
            {
                data: 'no_rak'
            },
            {
                data: 'id_item'
            },
            {
                data: 'itemdesc'
            },
            {
                data: 'color'
            },
            {
                data: 'size'
            },
            {
                data: 'no_invoice'
            },
            {
                data: 'dok_bc'
            },
            {
                data: 'no_aju'
            },
            {
                data: 'tgl_aju'
            },
            {
                data: 'no_daftar'
            },
            {
                data: 'tgl_daftar'
            },
            {
                data: 'tujuan'
            },
            {
                data: 'qty_out'
            },
            {
                data: 'satuan'
            },
            {
                data: 'berat_bersih'
            },
            {
                data: 'catatan'
            },
            {
                data: 'username'
            },
            {
                data: 'kpno'
            },
            {
                data: 'styleno'
            },
            {
                data: 'np_curr'
            },
            {
                data: 'np_price'
            },
            {
                data: 'jenis_pengeluaran'
            },
            {
                data: 'price_unit'
            },
            {
                data: 'total'
            },
            {
                data: 'rate'
            },
            {
                data: 'total_idr'
            }

        ],
        columnDefs: [],
        initComplete: function() {
        this.api().columns.adjust();
    }
    });

         $('#mytable').on('draw.dt', function () {
    datatable.columns.adjust();
});


        $("[data-toggle=tooltip]").tooltip();

function dataTableReload() {
                datatable.ajax.reload(()=>{
                    datatable.columns.adjust();
                });
            }

document.getElementById('btnExportExcel').addEventListener('click', function(e) {
    let sd = toYmd(document.getElementById('start_date').value);
    let ed = toYmd(document.getElementById('end_date').value);

    // set dynamic href
    this.href = `ekspor_ca_fabric_trx_out_barcode.php?start_date=${sd}&end_date=${ed}`;
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
<!-- 
<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(6)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(11)').text();        

    $.ajax({
    type : 'post',
    url : 'ajaxbpb.php',
    data : {'no_bpb': no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_bpb);
    $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
    $('#txt_no_po').html('No PO : ' + no_po + '');
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "ca_fabric_trx_out_barcode.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "ca_fabric_trx_out_barcode.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
