<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
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

#table-data {
    width: 100% !important;
}

#table-data th,
#table-data td {
    vertical-align: top;
}

#table-data th:nth-child(3),
#table-data td:nth-child(3),

#table-data th:nth-child(5),
#table-data td:nth-child(5),

#table-data th:nth-child(7),
#table-data td:nth-child(7),

#table-data th:nth-child(8),
#table-data td:nth-child(8),

#table-data th:nth-child(14),
#table-data td:nth-child(14),

#table-data th:nth-child(17),
#table-data td:nth-child(17),

#table-data th:nth-child(20),
#table-data td:nth-child(20),

#table-data th:nth-child(21),
#table-data td:nth-child(21),

#table-data th:nth-child(27),
#table-data td:nth-child(27)
{
    min-width: 220px !important;
    max-width: 320px !important;
    width: 220px !important;
    white-space: normal !important;
    word-break: break-word;
}


#table-data th:nth-child(2),
#table-data td:nth-child(2),

#table-data th:nth-child(4),
#table-data td:nth-child(4),

#table-data th:nth-child(11),
#table-data td:nth-child(11),

#table-data th:nth-child(13),
#table-data td:nth-child(13)
{
    min-width: 80px !important;
    max-width: 100px !important;
    width: 80px !important;
    white-space: normal !important;
    word-break: break-word;
}

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fa fa-cubes" aria-hidden="true"></i> ITEM GENERAL USAGE</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="item_general_usage.php
" method="post">
    <div class="row g-3">

         <div class="col-md-2">
        <label for="nama_supp" class="form-label"><b>Supplier</b></label>
        <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" selected="true">ALL</option>                                                
            <?php
            $nama_supp ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            }                 
            $sql = mysqli_query($conn1,"select id_supplier, supplier from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['id_supplier'];
                $tampil = $row['supplier'];
                if($row['id_supplier'] == $_POST['nama_supp']){
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
    <a id="btnExportExcel" target="_blank">
    <button type="button" class="btn btn-success ml-2" style="margin-top: 30px;">
        <i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i>
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
      <div style="overflow-x:auto;">
          <table id="table-data"
           class="table table-striped table-bordered table-hover table-sm" style="width:100%">
          <thead class="table-gradient">
            <tr>
                <th style="text-align: center;vertical-align: middle;">Trans #</th>
                <th style="text-align: center;vertical-align: middle;">Tgl. Trans</th>
                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                <th style="text-align: center;vertical-align: middle;">No Coa</th>
                <th style="text-align: center;vertical-align: middle;">Nama Coa</th>
                <th style="text-align: center;vertical-align: middle;">No Cost Center</th>
                <th style="text-align: center;vertical-align: middle;">Nama Cost Center</th>
                <th style="text-align: center;vertical-align: middle;">Inv #</th>
                <th style="text-align: center;vertical-align: middle;">Jenis Dok</th>
                <th style="text-align: center;vertical-align: middle;">Nomor Aju</th>
                <th style="text-align: center;vertical-align: middle;">Tgl Aju</th>
                <th style="text-align: center;vertical-align: middle;">Nomor Daftar</th>
                <th style="text-align: center;vertical-align: middle;">Tgl Daftar</th>
                <th style="text-align: center;vertical-align: middle;">Supplier</th>
                <th style="text-align: center;vertical-align: middle;">PO #</th>
                <th style="text-align: center;vertical-align: middle;">Type #</th>
                <th style="text-align: center;vertical-align: middle;">Inv/SJ #</th>
                <th style="text-align: center;vertical-align: middle;">Id Item</th>
                <th style="text-align: center;vertical-align: middle;">Kode Barang</th>
                <th style="text-align: center;vertical-align: middle;">Nama Barang</th>
                <th style="text-align: center;vertical-align: middle;">Kategori</th>
                <th style="text-align: center;vertical-align: middle;">Warna</th>
                <th style="text-align: center;vertical-align: middle;">Ukuran</th>
                <th style="text-align: center;vertical-align: middle;">Jumlah BPB</th>
                <th style="text-align: center;vertical-align: middle;">Satuan</th>
                <th style="text-align: center;vertical-align: middle;">Berat Bersih</th>
                <th style="text-align: center;vertical-align: middle;">Keterangan</th>
                <th style="text-align: center;vertical-align: middle;">Nama User</th>
                <th style="text-align: center;vertical-align: middle;">Approve By</th>
                <th style="text-align: center;vertical-align: middle;">WS #</th>
                <th style="text-align: center;vertical-align: middle;">Style #</th>
                <th style="text-align: center;vertical-align: middle;">Curr</th>
                <th style="text-align: center;vertical-align: middle;">Price</th>
                <th style="text-align: center;vertical-align: middle;">Jenis Trans</th>
                <th style="text-align: center;vertical-align: middle;">Reff No</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
</div>
</div>
</div>



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
    pageLength: 10,
    searching: true,
    info: true,
    autoWidth: false,

      ajax: {
        url: 'ajx_item_general_usage.php',
        type: 'POST',
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
          d.id_supplier        = $('#nama_supp').val();
        }
      },

      columns: [
      { data: 'bpbno' },
      { data: 'bpbdate' },
      { data: 'profit_center' },
      { data: 'no_coa' },
      { data: 'nama_coa' },
      { data: 'no_cc' },
      { data: 'cc_name' },
      { data: 'invno' },
      { data: 'jenis_dok' },
      { data: 'nomor_aju' },
      { data: 'tanggal_aju' },
      { data: 'bcno' },
      { data: 'bcdate' },
      { data: 'supplier' },
      { data: 'pono' },
      { data: 'tipe_com' },
      { data: 'invno' },
      { data: 'id_item' },
      { data: 'goods_code' },
      { data: 'itemdesc' },
      { data: 'description' },
      { data: 'color' },
      { data: 'size' },
      { data: 'qty' },
      { data: 'unit' },
      { data: 'berat_bersih' },
      { data: 'remark' },
      { data: 'username' },
      { data: 'confirm_by' },
      { data: 'reqno' },
      { data: 'whs_code' },
      { data: 'curr' },
      { data: 'price' },
      { data: 'jenis_trans' },
      { data: 'reffno' },
      ],

      columnDefs: [
            { targets: [2, 4, 6, 7, 13, 16, 19, 26], width: "320px" },
            {
    targets: [1, 10, 12],
    render: function(data) {

        if (
            data == null ||
            data == '' ||
            data == '-' ||
            data == '0000-00-00' ||
            data == '0000-00-00 00:00:00'
        ) {
            return '-';
        }

        const date = new Date(data);

        if (isNaN(date.getTime())) {
            return '-';
        }

        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
},
            {
              targets: [23, 32],
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



document.getElementById('btnExportExcel').addEventListener('click', function(e) {
  let start_date = toYmd(document.getElementById('start_date').value);
  let end_date = toYmd(document.getElementById('end_date').value);
  let nama_supp = document.getElementById('nama_supp').value;
  // alert(profit_center + '|' + no_coa + '|' + start_date + '|' + end_date);

  this.href = `ekspor_item_general_usage.php?id_supplier=${nama_supp}&start_date=${start_date}&end_date=${end_date}`;
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "item_general_usage.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "item_general_usage.php";
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
