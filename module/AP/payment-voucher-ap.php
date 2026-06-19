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

#table-data td:last-child {
    white-space: nowrap;
}

@media (min-width: 992px) {
    #mymodal .modal-dialog {
        max-width: 75%;
    }
}

.select2-container .select2-selection--single {
    height: calc(1.5em + 0.5rem + 2px);
    font-size: 0.875rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: calc(1.5em + 0.5rem);
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 0.5rem + 2px);
}

.kbon-action-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 5px;
}

.kbon-action-buttons .btn {
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 11px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    transition: all 0.2s ease;
    white-space: nowrap;
}

.kbon-action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.25);
}

.kbon-status-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 20px;
}

.kbon-status-label.kbon-status-canceled {
    background: #fdecea;
    color: #c0392b;
}

.kbon-status-label.kbon-status-updated {
    background: #e3fcef;
    color: #1e8449;
}

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fa fa-cubes" aria-hidden="true"></i> PAYMENT VOUCHER</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="payment-voucher-ap.php
" method="post">
    <div class="row g-3">

        <div class="col-md-3">
            <label for="nama_supp"><b>Supplier</b></label>            
            <select class="form-control form-control-sm select2" name="nama_supp" id="nama_supp" style="width: 100%;">
                <?php
                $nama_supp = 'ALL';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : 'ALL';
                }

                $isSelected = ($nama_supp == 'ALL') ? ' selected="selected"' : '';
                echo '<option value="ALL"' . $isSelected . '>ALL</option>';

                $sql = mysqli_query($conn1, "SELECT DISTINCT(Supplier) FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    $isSelected = ($data == $nama_supp) ? ' selected="selected"' : '';
                    echo '<option value="' . $data . '"' . $isSelected . '>' . $data . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="col-md-1">
            <label for="status"><b>Status</b></label>
            <select class="form-control form-control-sm select2" name="status" id="status" style="width: 100%;">
                <?php
                $status = 'ALL';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $status = isset($_POST['status']) ? $_POST['status'] : 'ALL';
                }

                $isSelected = ($status == 'ALL') ? ' selected="selected"' : '';
                echo '<option value="ALL"' . $isSelected . '>ALL</option>';

                $sql = mysqli_query($conn1, "select nama_pilihan from whs_master_pilihan where type_pilihan = 'status_kontrabon' and status = 'Active'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['nama_pilihan'];
                    $isSelected = ($data == $status) ? ' selected="selected"' : '';
                    echo '<option value="' . $data . '"' . $isSelected . '>' . $data . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="col-md-2">
            <label for="type_pv"><b>Type</b></label>
            <?php
            $type_pv = 'ALL';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $type_pv = isset($_POST['type_pv']) ? $_POST['type_pv'] : 'ALL';
            }
            $types = ['ALL', 'Regular', 'Installment', 'DP', 'CBD'];
            ?>
            <select class="form-control form-control-sm select2" name="type_pv" id="type_pv" style="width: 100%;">
                <?php foreach ($types as $t): ?>
                <option value="<?= $t ?>" <?= ($t == $type_pv) ? 'selected="selected"' : '' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label for="filter_date"><b>Filter Date</b></label>
            <?php
            $filter_date = 'tgl_kbon';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $filter_date = isset($_POST['filter_date']) ? $_POST['filter_date'] : 'tgl_kbon';
            }
            ?>
            <select class="form-control form-control-sm select2" name="filter_date" id="filter_date" style="width: 100%;">
                <option value="tgl_kbon" <?= ($filter_date == 'tgl_kbon') ? 'selected="selected"' : '' ?>>Payment Voucher Date</option>
                <option value="create_date" <?= ($filter_date == 'create_date') ? 'selected="selected"' : '' ?>>Created Date</option>
                <option value="confirm_date" <?= ($filter_date == 'confirm_date') ? 'selected="selected"' : '' ?>>Approved Date</option>
            </select>
        </div>

        <div class="col-md-4"></div>

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
<div class="col-md-3 d-flex align-items-end">
  <button type="button" class="btn btn-info btn-sm me-2" onclick="dataTableReload()">
        <i class="fa fa-search"></i> Search
      </button>
    <?php
    $querys = mysqli_query($conn2, "select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Kontrabon'");
    $rs = mysqli_fetch_array($querys);
    $id = isset($rs['id']) ? $rs['id'] : 0;

    if ($id == '7') {
        echo '<button type="button" id="btnCreateNew" class="btn btn-primary btn-sm ml-2" onclick="location.href=\'create-payment-voucher-ap.php\'">
        <i class="fa fa-plus-circle" aria-hidden="true"></i> Create New
        </button>';
    }
    ?>
    <a id="btnExportExcel" target="_blank">
    <button type="button" class="btn btn-success btn-sm ml-2">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </button>
</a>


</div>

</div>
<div class="row g-3">
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
                <th style="text-align: center;vertical-align: middle;">No Payment Voucher</th>
                <th style="text-align: center;vertical-align: middle;">Payment Voucher Date</th>
                <th style="text-align: center;vertical-align: middle;">KB Date (Sys)</th>
                <th style="text-align: center;vertical-align: middle;">Supplier</th>
                <th style="text-align: center;vertical-align: middle;">SubTotal</th>
                <th style="text-align: center;vertical-align: middle;">Tax (PPn)</th>
                <th style="text-align: center;vertical-align: middle;">Tax (PPh)</th>
                <th style="text-align: center;vertical-align: middle;">Total CBD/DP</th>
                <th style="text-align: center;vertical-align: middle;">Return</th>
                <th style="text-align: center;vertical-align: middle;">Potongan</th>
                <th style="text-align: center;vertical-align: middle;">Total KB</th>
                <th style="text-align: center;vertical-align: middle;">Currency</th>
                <th style="text-align: center;vertical-align: middle;width: 190px;">Action</th>
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
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-3 mb-2"></div>
          <div id="txt_supp" class="col-md-3 mb-2"></div>
          <div id="txt_duedate" class="col-md-3 mb-2"></div>
          <div id="txt_curr" class="col-md-3 mb-2"></div>
          <div id="txt_created_by" class="col-md-3 mb-2"></div>
          <div id="txt_status" class="col-md-3 mb-2"></div>
          <div id="txt_nofaktur" class="col-md-3 mb-2"></div>
          <div id="txt_suppinv" class="col-md-3 mb-2"></div>
          <div id="txt_tglinv" class="col-md-3 mb-2"></div>
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
        $("[data-toggle=tooltip]").tooltip();

        $('#nama_supp').select2({
            width: '100%'
        });

        $('#status').select2({
            width: '100%'
        });

        $('#type_pv').select2({
            width: '100%'
        });

        $('#filter_date').select2({
            width: '100%'
        });
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
        url: 'ajx_payment-voucher-ap.php',
        type: 'POST',
        data: function (d) {
          d.nama_supp   = $('#nama_supp').val();
          d.status      = $('#status').val();
          d.type_pv     = $('#type_pv').val();
          d.filter_date = $('#filter_date').val();
          d.start_date  = $('#start_date').val();
          d.end_date    = $('#end_date').val();
        }
      },

      columns: [
      { data: 'no_kbon' },
      { data: 'tgl_kbon' },
      { data: 'tgl_kbon2' },
      { data: 'nama_supp' },
      { data: 'subtotal' },
      { data: 'tax' },
      { data: 'pph_value' },
      { data: 'dp' },
      { data: 'return' },
      { data: 'potong' },
      { data: 'total' },
      { data: 'curr' },
      { data: 'action', orderable: false },
      ],

      columnDefs: [
          { targets: [4, 5, 6, 7, 8, 9, 10], className: 'text-right' },
          { targets: [0, 1, 2, 3, 11, 12], className: 'text-center' },
          { targets: 12, width: '190px' }
            ],

});

$("[data-toggle=tooltip]").tooltip();

function dataTableReload() {
  datatable.ajax.reload(()=>{
    datatable.columns.adjust();
  });
}

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (ch) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
    });
}

function formatMoney(amount, decimalCount = 2) {
    const val = parseFloat(amount);
    if (isNaN(val)) return '0.00';
    return val.toLocaleString('en-US', {
        minimumFractionDigits: decimalCount,
        maximumFractionDigits: decimalCount
    });
}

// Klik baris (kolom No Kontra Bon) untuk lihat detail
$('#table-data').on('click', 'tbody tr td:first-child', function () {
    const data = datatable.row($(this).closest('tr')).data();
    if (!data) return;

    $('#mymodal').modal('show');
    $('#txt_bpb').text(data.no_kbon);
    $('#txt_tglbpb').html('Payment Voucher Date : ' + escapeHtml(data.tgl_kbon));
    $('#txt_supp').html('Supplier : ' + escapeHtml(data.nama_supp));
    $('#txt_duedate').html('Due Date : ' + escapeHtml(data.tgl_tempo));
    $('#txt_curr').html('Currency : ' + escapeHtml(data.curr));
    $('#txt_created_by').html('Create By : ' + escapeHtml(data.create_user));
    $('#txt_status').html('Status : ' + escapeHtml(data.status));
    $('#txt_nofaktur').html('No Faktur : ' + escapeHtml(data.no_faktur));
    $('#txt_suppinv').html('No Supplier Invoice : ' + escapeHtml(data.supp_inv));
    $('#txt_tglinv').html('Supplier Invoice Date : ' + escapeHtml(data.tgl_inv));
    $('#details').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i></div>');

    $.ajax({
        url: 'ajaxkbon.php',
        type: 'POST',
        data: { no_kbon: data.no_kbon },
        success: function (html) {
            $('#details').html(html);
        },
        error: function () {
            $('#details').html('<div class="text-center p-3 text-danger">Failed to load detail</div>');
        }
    });
});

// Approve kontra bon
$('#table-data').on('click', '.btn-approve-kbon', function () {
    const btn = this;

    Swal.fire({
        title: 'Approve this payment voucher?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Approve!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            type: 'POST',
            url: 'approvekbon.php',
            data: {
                no_kbon: btn.dataset.no_kbon,
                approve_user: '<?php echo $user ?>',
                tgl_kbon: btn.dataset.tgl_kbon,
                supp: btn.dataset.supp,
                pph: btn.dataset.pph,
                curr: btn.dataset.curr,
                tgl_bpb: btn.dataset.tgl_bpb,
                no_po: btn.dataset.no_po,
                no_bpb: btn.dataset.no_bpb,
                tgl_po: btn.dataset.tgl_po,
                total: btn.dataset.total
            },
            success: function () {
                Swal.fire({ icon: 'success', title: 'Successfully Approved', timer: 1500, showConfirmButton: false });
                datatable.ajax.reload();
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });
});

// Cancel kontra bon
$('#table-data').on('click', '.btn-cancel-kbon', function () {
    const noKbon = this.dataset.no_kbon;

    Swal.fire({
        title: 'Cancel this payment voucher?',
        text: noKbon + ' will have its status changed to Cancel.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, Cancel!',
        cancelButtonText: 'Close'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            type: 'POST',
            url: 'cancelkbon.php',
            data: { no_kbon: noKbon, cancel_user: '<?php echo $user ?>' },
            success: function () {
                Swal.fire({ icon: 'success', title: 'Successfully Cancelled', timer: 1500, showConfirmButton: false });
                datatable.ajax.reload();
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });
});

// Edit kontra bon (draft)
$('#table-data').on('click', '.btn-edit-kbon', function () {
    const noKbon = this.dataset.no_kbon;
    const closing = this.dataset.closing;
    const encodedNoKbon = btoa(noKbon);

    if (closing !== 'Open') {
        Swal.fire({ icon: 'error', title: 'Sorry!', text: 'The payment voucher period has already been closed.' });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to edit this document.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, edit it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            type: 'POST',
            url: 'copy_data_kontrabon.php',
            data: { no_kbon: noKbon, status: 'Updating' },
            success: function (res) {
                if (res.trim() === 'OK') {
                    localStorage.removeItem('profit_center');
                    window.location.href = 'form_edit_kontrabon.php?no_kbon=' + encodedNoKbon;
                } else {
                    Swal.fire('Error', res, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
    });
});

document.getElementById('btnExportExcel').addEventListener('click', function(e) {
  const where = [];
  if ($('#nama_supp').val() !== 'ALL') where.push("and a.nama_supp = '" + $('#nama_supp').val() + "'");
  if ($('#status').val() !== 'ALL') where.push("and a.status = '" + $('#status').val() + "'");

  const params = new URLSearchParams({
      nama_supp: $('#nama_supp').val(),
      status: $('#status').val(),
      start_date: toYmd($('#start_date').val()),
      end_date: toYmd($('#end_date').val()),
      tgl_filter: 'a.' + $('#filter_date').val(),
      where: where.join(' ')
  });

  this.href = 'ekspor_kb_all.php?' + params.toString();
});

</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
