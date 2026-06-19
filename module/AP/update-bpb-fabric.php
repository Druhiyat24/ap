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




</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fa fa-cubes" aria-hidden="true"></i> UPDATE BPB FABRIC</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="update-bpb-fabric.php
" method="post">
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
<div class="col-md-6 d-flex align-items-end">
  <button type="button" class="btn btn-info btn-sm me-2" onclick="dataTableReload()">
        <i class="fa fa-search"></i> Search
      </button>
    <button type="button" id="btnCreateNew" class="btn btn-primary btn-sm ml-2" onclick="location.href='form_update_bpb_fabric.php'">
        <i class="fa fa-plus-circle" aria-hidden="true"></i> Create New
    </button>
    <a id="btnExportExcel" target="_blank">
    <button type="button" class="btn btn-success btn-sm ml-2">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
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
                <th style="text-align: center;vertical-align: middle;">No. Trans</th>
                <th style="text-align: center;vertical-align: middle;">Trans Date</th>
                <th style="text-align: center;vertical-align: middle;">Status</th>
                <th style="text-align: center;vertical-align: middle;">Description</th>
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
          <div id="txt_status" class="col-md-3 mb-2"></div>
          <div id="txt_created_by" class="col-md-3 mb-2"></div>
          <div id="txt_deskripsi" class="col-12 mb-2"></div>
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
        url: 'ajx_update-bpb-fabric.php',
        type: 'POST',
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
        }
      },

      columns: [
      { data: 'no_pengajuan' },
      { data: 'tgl_pengajuan' },
      { data: 'status' },
      { data: 'deskripsi' },
      { data: 'created_by' },
      { data: 'action', orderable: false },
      ],

      columnDefs: [
          { targets: [2, 5], className: 'text-center' },
          { targets: 5, width: '240px' }
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

// View detail of an edit request
$('#table-data').on('click', '.btn-view-pengajuan', function () {
    const noPengajuan = this.dataset.no;

    $('#txt_bpb').text('Edit Request - ' + noPengajuan);
    $('#txt_tglbpb, #txt_supp, #txt_status, #txt_created_by, #txt_deskripsi').html('');
    $('#details').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#mymodal').modal('show');

    $.ajax({
        url: 'get_detail_update_bpb_fabric.php',
        type: 'GET',
        data: { no_pengajuan: noPengajuan },
        dataType: 'json',
        success: function (res) {
            const h = res.header;
            if (h) {
                $('#txt_tglbpb').html('<b>Transaction Date:</b> ' + escapeHtml(h.tgl_pengajuan));
                $('#txt_status').html('<b>Status:</b> ' + escapeHtml(h.status));
                const createdByText = h.created_by ? (h.created_by + ' (' + h.created_at + ')') : '-';
                $('#txt_created_by').html('<b>Created By:</b> ' + escapeHtml(createdByText));
                $('#txt_deskripsi').html('<b>Description:</b> ' + escapeHtml(h.deskripsi || '-'));
            }

            if (!res.items.length) {
                $('#details').html('<div class="text-center p-3 text-muted">No items found</div>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-bordered table-striped table-sm text-center">';
            html += '<thead class="table-gradient text-white"><tr>'
                + '<th>No BPB</th><th>BPB Date</th><th>Supplier</th><th>No WS</th><th>Item</th><th>Qty</th><th>Unit</th><th>Curr</th>'
                + '<th>Price (Old)</th><th>Price (New)</th><th>PPN % (Old)</th><th>PPN % (New)</th>'
                + '</tr></thead><tbody>';

            res.items.forEach(function (it) {
                html += '<tr>'
                    + '<td>' + escapeHtml(it.no_bpb) + '</td>'
                    + '<td>' + escapeHtml(it.tgl_bpb) + '</td>'
                    + '<td>' + escapeHtml(it.nama_supp || '-') + '</td>'
                    + '<td>' + escapeHtml(it.no_ws || '-') + '</td>'
                    + '<td class="text-left">' + escapeHtml(it.desc_item || it.id_item) + '</td>'
                    + '<td>' + formatMoney(it.qty) + '</td>'
                    + '<td>' + escapeHtml(it.unit || '-') + '</td>'
                    + '<td>' + escapeHtml(it.curr || '-') + '</td>'
                    + '<td>' + formatMoney(it.price_old, 4) + '</td>'
                    + '<td>' + formatMoney(it.price_new, 4) + '</td>'
                    + '<td>' + formatMoney(it.ppn_old) + '</td>'
                    + '<td>' + formatMoney(it.ppn_new) + '</td>'
                    + '</tr>';
            });

            html += '</tbody></table></div>';
            $('#details').html(html);
        },
        error: function () {
            $('#details').html('<div class="text-center p-3 text-danger">Failed to load detail</div>');
        }
    });
});

// Cancel an edit request
$('#table-data').on('click', '.btn-cancel-pengajuan', function () {
    const noPengajuan = this.dataset.no;

    Swal.fire({
        title: 'Cancel this request?',
        text: noPengajuan + ' will have its status changed to Cancel.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Yes, Cancel',
        cancelButtonText: 'Close'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: 'cancel_update_bpb_fabric.php',
            type: 'POST',
            data: { no_pengajuan: noPengajuan },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Request ' + noPengajuan + ' has been cancelled',
                        timer: 1800,
                        showConfirmButton: false
                    });
                    datatable.ajax.reload();
                } else {
                    Swal.fire({ icon: 'error', title: 'Failed!', text: res.message || 'An error occurred' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to cancel the request' });
            }
        });
    });
});

function RepostJurnal() {

    let selected = [];

    $('.row-check:checked').each(function () {
        selected.push($(this).val());
    });

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Select at least 1 record!'
        });
        return;
    }

    console.log(selected);

    Swal.fire({
        title: 'Repost journal?',
        text: "The old journal will be moved to the cancel table!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Repost!',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Processing...',
                html: 'Reposting journal...',
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
                        title: 'Success!',
                        text: res
                    });

                    datatable.ajax.reload();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'A server error occurred'
                    });
                }
            });

        }

    });
}



document.getElementById('btnExportExcel').addEventListener('click', function(e) {
  let start_date = toYmd(document.getElementById('start_date').value);
  let end_date = toYmd(document.getElementById('end_date').value);

  this.href = `ekspor_update-bpb-fabric.php?start_date=${start_date}&end_date=${end_date}`;
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "update-bpb-fabric.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "update-bpb-fabric.php";
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
