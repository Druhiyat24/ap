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
    <h5 class="mb-0"><i class="fas fa-donate"></i> MASTER RATE</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="master-rate.php" method="post">
    <div class="row g-3">
      <!-- Supplier -->
      <div class="col-md-2">
        <label for="type_curr" class="form-label"><b>Type</b></label>
        <select class="form-control selectpicker" name="type_curr" id="type_curr" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" selected="true">ALL</option>                                                
            <?php
            $type_curr ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $type_curr = isset($_POST['type_curr']) ? $_POST['type_curr']: null;
            }                 
            $sql = mysqli_query($conn1,"select nama_pilihan from whs_master_pilihan where type_pilihan = 'master_rate' and status = 'Active'");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['nama_pilihan'];
                $tampil = $row['nama_pilihan'];
                if($row['nama_pilihan'] == $_POST['type_curr']){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
        </select>
    </div>

    <!-- Supplier -->
      <div class="col-md-1">
        <label for="curr" class="form-label"><b>Currency</b></label>
        <select class="form-control selectpicker" name="curr" id="curr" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" selected="true">ALL</option>                                                
            <?php
            $curr ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $curr = isset($_POST['curr']) ? $_POST['curr']: null;
            }                 
            $sql = mysqli_query($conn1,"select nama_pilihan from whs_master_pilihan where type_pilihan = 'currency' and status = 'Active' and nama_pilihan != 'IDR'");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['nama_pilihan'];
                $tampil = $row['nama_pilihan'];
                if($row['nama_pilihan'] == $_POST['curr']){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
        </select>
    </div>

<!-- Tombol -->
<div class="col-md-2 d-flex align-items-end">
      <button type="button" class="btn btn-info btn-sm me-2" onclick="dataTableReload()">
        <i class="fa fa-search"></i> Search
      </button>

      <button type="button" class="btn btn-success btn-sm ml-2" onclick="AddData()">
        <i class="fas fa-plus"></i> Add New
      </button>
<!-- <a id="btnExportPrepaidTax" target="_blank">
    <button type="button" class="btn btn-success btn-xs ml-2" style="margin-top: 30px;">
        <i class="fa fa-file-excel-o" aria-hidden="true" > Excel</i>
    </button>
</a> -->
  

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
                <th style="text-align: center;vertical-align: middle;">Type</th>
                <th style="text-align: center;vertical-align: middle;">Currency</th>
                <th style="text-align: center;vertical-align: middle;">Periode</th>
                <th style="text-align: center;vertical-align: middle;">Rate</th>
                <th style="text-align: center;vertical-align: middle;">Rate Jual</th>
                <th style="text-align: center;vertical-align: middle;">Rate Beli</th>
                <th style="text-align: center;vertical-align: middle;">Created By</th>
                <th style="text-align: center;vertical-align: middle;">Action</th>
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

<!-- Modal EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header text-white" style="background-color: #1E3A8A;">
        <h5 class="modal-title">
          <i class="fa fa-edit"></i> Edit Master Rate
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          &times;
        </button>
      </div>

      <div class="modal-body">

        <form id="formEdit">

          <input type="hidden" id="edit_id" name="id">

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Type</label>
              <input type="text" class="form-control form-control-sm" id="v_codecurr_edit" name="v_codecurr_edit" readonly required>
            </div>

          <div class="form-group col-md-6">
            <label>Currency</label>
            <select class="form-control selectpicker" name="edit_curr" id="edit_curr" data-dropup-auto="false" data-live-search="true">
            <?php               
            $sql = mysqli_query($conn1,"select nama_pilihan from whs_master_pilihan where type_pilihan = 'currency' and status = 'Active' and nama_pilihan != 'IDR'");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['nama_pilihan'];
                $tampil = $row['nama_pilihan'];
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
            </select>
          </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Periode Awal</label>
              <input type="text" class="form-control form-control-sm tgl_edit" id="edit_tgl_awal" onfocus="lastValidAwal=this.value" onchange="validasiPeriode()" required>
            </div>

            <div class="form-group col-md-6">
              <label>Periode Akhir</label>
              <input type="text" class="form-control form-control-sm tgl_edit" id="edit_tgl_akhir" onfocus="lastValidAkhir=this.value" onchange="validasiPeriode()" required>
            </div>
          </div>

          <div class="form-row">
          <div class="form-group col-md-4">
            <label>Rate</label>
            <input type="number" step="0.0001" class="form-control form-control-sm text-right" id="edit_rate" required>
          </div>

          <div class="form-group col-md-4">
            <label>Rate Jual</label>
            <input type="number" step="0.0001" class="form-control form-control-sm text-right" id="edit_rate_jual" required>
          </div>

          <div class="form-group col-md-4">
            <label>Rate Beli</label>
            <input type="number" step="0.0001" class="form-control form-control-sm text-right" id="edit_rate_beli" required>
          </div>
        </div>

        </form>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          Close
        </button>

        <button type="button" class="btn btn-success" id="btnSaveEdit" onclick="SaveEdit()">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </div>

    </div>
  </div>
</div>



<div class="modal fade" id="modalNew" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header text-white" style="background-color: #1E3A8A;">
        <h5 class="modal-title">
          <i class="fas fa-plus"></i> Add Master Rate
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          &times;
        </button>
      </div>

      <div class="modal-body">

        <form id="formNew">


          <div class="form-row">
            <div class="form-group col-md-6">
            <label>Currency</label>
            <select class="form-control selectpicker" name="v_codecurr_new" id="v_codecurr_new" data-dropup-auto="false" data-live-search="true">
            <?php               
            $sql = mysqli_query($conn1,"select nama_pilihan from whs_master_pilihan where type_pilihan = 'master_rate' and status = 'Active'");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['nama_pilihan'];
                $tampil = $row['nama_pilihan'];
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
            </select>
          </div>

          <div class="form-group col-md-6">
            <label>Currency</label>
            <select class="form-control selectpicker" name="new_curr" id="new_curr" data-dropup-auto="false" data-live-search="true">
            <?php               
            $sql = mysqli_query($conn1,"select nama_pilihan from whs_master_pilihan where type_pilihan = 'currency' and status = 'Active' and nama_pilihan != 'IDR'");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['nama_pilihan'];
                $tampil = $row['nama_pilihan'];
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
            </select>
          </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Periode Awal</label>
              <input type="text" class="form-control form-control-sm tgl_new" id="new_tgl_awal" value="<?= !empty($_POST['start_date'] ?? '') ? $_POST['start_date'] : date('Y-m-d'); ?>" onfocus="lastValidAwalNew=this.value" onchange="validasiPeriode()" required>
            </div>

            <div class="form-group col-md-6">
              <label>Periode Akhir</label>
              <input type="text" class="form-control form-control-sm tgl_new" id="new_tgl_akhir" value="<?= !empty($_POST['start_date'] ?? '') ? $_POST['start_date'] : date('Y-m-d'); ?>" onfocus="lastValidAkhirNew=this.value" onchange="validasiPeriode()" required>
            </div>
          </div>

          <div class="form-row">
          <div class="form-group col-md-4">
            <label>Rate</label>
            <input type="number" step="0.0001" class="form-control form-control-sm text-right" id="new_rate" required>
          </div>

          <div class="form-group col-md-4">
            <label>Rate Jual</label>
            <input type="number" step="0.0001" class="form-control form-control-sm text-right" id="new_rate_jual" required>
          </div>

          <div class="form-group col-md-4">
            <label>Rate Beli</label>
            <input type="number" step="0.0001" class="form-control form-control-sm text-right" id="new_rate_beli" required>
          </div>
        </div>

        </form>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          Close
        </button>

        <button type="button" class="btn btn-success" id="btnSaveNew" onclick="SaveNew()">
          <i class="fa fa-save"></i> Save Changes
        </button>
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


<script type="text/javascript">
    $(document).ready(function () {
        $('.tgl_edit').datepicker({
            format: "yyyy-mm-dd",
            startDate : "2025-01-01",
            autoclose:true
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tgl_new').datepicker({
            format: "yyyy-mm-dd",
            startDate : "2025-01-01",
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
        url: 'ajx_master_rate.php',
        type: 'POST',
        data: function (d) {
          d.type_curr   = $('#type_curr').val();
          d.curr          = $('#curr').val();
        }
      },

      columns: [
      { data: 'v_codecurr' },
      { data: 'curr' },
      { data: 'tanggal_input' },
      { data: 'rate' },
      { data: 'rate_jual' },
      { data: 'rate_beli' },
      { data: 'v_lastupdate' },
      { data: 'v_idgroup' },
      ],

      columnDefs: [
      { width: "100px", targets: 0 }, // Type
  { width: "80px",  targets: 1 }, // Currency
  { width: "220px", targets: 2 }, // Periode
  { width: "120px", targets: 3 }, // Rate
  { width: "120px", targets: 4 }, // Rate Jual
  { width: "120px", targets: 5 }, // Rate Beli
  { width: "140px", targets: 6 }, // Created By
  { width: "180px", targets: 7 }, // Action

            {
              targets: [3, 4, 5],
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
  targets: -1,
  orderable: false,
  className: "text-center",
  render: function (data, type, row) {

    return `
      <button class="btn btn-xs btn-primary"
        onclick="editData('${row.v_idgroup}')">
        <i class="fa fa-edit"></i> Update
      </button>

      <button class="btn btn-xs btn-danger"
        onclick="cancelData('${row.v_idgroup}')">
        <i class="fa fa-times"></i> Cancel
      </button>
    `;
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

document.getElementById('btnExportPrepaidTax').addEventListener('click', function(e) {
  let start_date = toYmd(document.getElementById('start_date').value);
  let end_date = toYmd(document.getElementById('end_date').value);
  let profit_center = document.getElementById('profit_center').value;
  let no_coa = document.getElementById('no_coa').value;
  // alert(profit_center + '|' + no_coa + '|' + start_date + '|' + end_date);

  this.href = `ekspor_prepaid_tax.php?profit_center=${profit_center}&no_coa=${no_coa}&start_date=${start_date}&end_date=${end_date}`;
});

function editData(id) {
  // alert(id);

  Swal.fire({
    title: 'Loading...',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  $.ajax({
    url: 'get_master_rate.php',
    type: 'POST',
    data: { id: id },
    dataType: 'json',
    success: function(res){

      Swal.close();

      if(res.status === "success"){

        let data = res.data;

        $('#edit_id').val(data.v_idgroup);
        $('#edit_curr').val(data.curr);
        $('#edit_curr').selectpicker('refresh');

        $('#edit_tgl_awal').val(data.tgl_awal);
        $('#edit_tgl_akhir').val(data.tgl_akhir);

        $('#edit_rate').val(data.rate);
        $('#edit_rate_jual').val(data.rate_jual);
        $('#edit_rate_beli').val(data.rate_beli);
        $('#v_codecurr_edit').val(data.v_codecurr);

        $('#modalEdit').modal('show');

      } else {

        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: res.message
        });

      }
    }
  });
}


function AddData() {

$('#modalNew').modal('show');
}


function validasiPeriode() {

    var awal  = $('#edit_tgl_awal').val();
    var akhir = $('#edit_tgl_akhir').val();

    if(awal !== '' && akhir !== '') {

        if(akhir < awal){

            Swal.fire({
                icon: 'warning',
                title: 'Periode Salah',
                text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal'
            });

            $('#edit_tgl_akhir').val(lastValidAkhir);
            $('#edit_tgl_awal').val(lastValidAwal);
            return false;
        }

        lastValidAkhir = akhir;
        lastValidAwal = awal;
    }

    return true;
}


function validasiPeriodeNew() {

    var awal  = $('#new_tgl_awal').val();
    var akhir = $('#new_tgl_akhir').val();

    if(awal !== '' && akhir !== '') {

        if(akhir < awal){

            Swal.fire({
                icon: 'warning',
                title: 'Periode Salah',
                text: 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal'
            });

            $('#new_tgl_akhir').val(lastValidAkhirNew);
            $('#new_tgl_awal').val(lastValidAwalNew);
            return false;
        }

        lastValidAkhirNew = akhir;
        lastValidAwalNew = awal;
    }

    return true;
}





function cancelData(id) {
  var username = '<?php echo $user; ?>';
alert(id);
  Swal.fire({
    title: 'Yakin?',
    text: "Data akan dicancel!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, Cancel!',
    cancelButtonText: 'Batal'
  }).then((result) => {

    if (result.isConfirmed) {

      Swal.fire({
        title: 'Processing...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      $.post('cancel_master_rate.php', { id:id, username:username }, function(res){

        Swal.fire({
          icon: 'success',
          title: 'Berhasil',
          text: 'Data berhasil dicancel',
          timer: 1500,
          showConfirmButton: false
        });

        datatable.ajax.reload(null, false);

      });

    }

  });
}

function SaveEdit() {
  // alert('test');

    if(!validasiPeriode()){
        return false;
    }

    var data = {
        id: $('#edit_id').val(),
        curr: $('#edit_curr').val(),
        tgl_awal: $('#edit_tgl_awal').val(),
        tgl_akhir: $('#edit_tgl_akhir').val(),
        rate: $('#edit_rate').val(),
        rate_jual: $('#edit_rate_jual').val(),
        rate_beli: $('#edit_rate_beli').val(),
        v_codecurr: $('#v_codecurr_edit').val(),
        create_user : '<?php echo $user; ?>'
    };

    console.log(data);

    $.ajax({
    url: 'update_master_rate.php',
    type: 'POST',
    data: data,
    dataType: 'json',
    beforeSend: function(){
        $('#btnSaveEdit').prop('disabled', true);
    },
    success: function(result){

        console.log(result);

        if(result && result.status === 'success'){

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: result.message
            }).then(function(){
                $('#modalEdit').modal('hide');
                datatable.ajax.reload(null, false);
            });

        }else{

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message || 'Terjadi kesalahan'
            });

        }
    },
    error: function(xhr){
        console.log(xhr.responseText);

        Swal.fire({
            icon: 'error',
            title: 'AJAX Error',
            text: 'Server tidak merespon dengan benar'
        });
    },
    complete: function(){
        $('#btnSaveEdit').prop('disabled', false);
    }
});


}




function SaveNew() {

    if(!validasiPeriodeNew()){
        return false;
    }

    var curr        = $('#new_curr').val();
    var tgl_awal    = $('#new_tgl_awal').val();
    var tgl_akhir   = $('#new_tgl_akhir').val();
    var rate        = $('#new_rate').val();
    var rate_jual   = $('#new_rate_jual').val();
    var rate_beli   = $('#new_rate_beli').val();
    var v_codecurr  = $('#v_codecurr_new').val();

    // =========================
    // VALIDASI WAJIB ISI
    // =========================
    if(!curr || !v_codecurr || !tgl_awal || !tgl_akhir || !rate){

        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Currency, Type, Periode dan Rate wajib diisi'
        });

        return false;
    }

    var data = {
        curr: curr,
        tgl_awal: tgl_awal,
        tgl_akhir: tgl_akhir,
        rate: rate,
        rate_jual: rate_jual,
        rate_beli: rate_beli,
        v_codecurr: v_codecurr,
        create_user : '<?php echo $user; ?>'
    };

    console.log(data);

    $.ajax({
        url: 'insert_master_rate.php',
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function(){
            $('#btnSaveNew').prop('disabled', true);
        },
        success: function(result){

            if(result && result.status === 'success'){

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.message
                }).then(function(){
                    $('#modalNew').modal('hide');
                    datatable.ajax.reload(null, false);
                });

            }else{

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message || 'Terjadi kesalahan'
                });

            }
        },
        error: function(xhr){
            console.log(xhr.responseText);

            Swal.fire({
                icon: 'error',
                title: 'AJAX Error',
                text: 'Server tidak merespon dengan benar'
            });
        },
        complete: function(){
            $('#btnSaveNew').prop('disabled', false);
        }
    });
}




</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "master-rate.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "master-rate.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
