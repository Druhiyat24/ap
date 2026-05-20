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
    <h5 class="mb-0"><i class="fas fa-file-contract"></i> TRANSFER MEMO</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="rekonsiliasi_jurnal_bpb.php" method="post">
    <div class="row g-3">
<!-- 
      <div class="col-md-2">
        <label for="status" class="form-label"><b>Status</b></label>
        <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" selected="true">ALL</option>                                                
            <?php
            $status ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
            }                 
            $sql = mysqli_query($conn1,"select nama_pilihan from whs_master_pilihan where type_pilihan  = 'status_bpb'");
            while ($row = mysqli_fetch_array($sql)) {
                $data = $row['nama_pilihan'];
                $tampil = $row['nama_pilihan'];
                if($row['nama_pilihan'] == $_POST['status']){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo '<option value="'.$data.'"'.$isSelected.'">'. $tampil .'</option>';    
            }?>
        </select>
    </div>
 -->
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
<!-- <a id="btnExportData" target="_blank">
    <button type="button" class="btn btn-success btn-xs ml-2" style="margin-top: 30px;">
        <i class="fa fa-file-excel-o" aria-hidden="true"> Excel</i>
    </button>
</a> -->
<a href="create_transfer_memo.php">
    <button type="button" class="btn btn-primary btn-xs ml-2" style="margin-top: 30px;">
        <i class="fa fa-plus"></i> Create
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
                <th style="text-align: center;vertical-align: middle;">No Transfer</th>
                <th style="text-align: center;vertical-align: middle;">Tgl Transfer</th>
                <th style="text-align: center;vertical-align: middle;">Keterangan</th>
                <th style="text-align: center;vertical-align: middle;">User Create</th>
                <th style="text-align: center;vertical-align: middle;">Status</th>
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

<!-- Modal Detail Transfer Memo -->
<div class="modal fade" id="modalDetailTransfer" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow">

      <div class="modal-header text-white py-2 px-3"
           style="background: linear-gradient(90deg,#191970,#1e90ff); border-radius:4px 4px 0 0;">
        <h5 class="modal-title mb-0">
          <i class="fa fa-file-text-o mr-1"></i> Detail Transfer Memo
          <span id="modal-label-notrans" class="ml-2"
                style="font-size:13px; opacity:.8; font-weight:400;"></span>
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"
                style="opacity:1;">&times;</button>
      </div>

      <div class="modal-body p-4" id="modal-body-transfer" style="min-height:200px;">
        <div class="text-center py-5" id="tm-loading">
          <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
          <p class="mt-2 text-muted">Memuat data...</p>
        </div>
      </div>

      <div class="modal-footer py-2" style="background:#f8f9fa;">
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Tutup
        </button>
        <a href="#" id="btn-modal-print" target="_blank" class="btn btn-sm btn-success">
          <i class="fa fa-print"></i> Print PDF
        </a>
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
        url: 'ajax_transfer_memo.php',
        type: 'POST',
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
        }
      },

      columns: [
        { data: 'no_trans'    },
        { data: 'tgl_trans'   },
        { data: 'keterangan'  },
        { data: 'create_user' },
        {
          data: 'status',
          render: function (data) {
            var map = { APPROVED: 'success', CANCEL: 'danger', DRAFT: 'warning' };
            var cls = map[data] || 'secondary';
            return '<span class="badge badge-' + cls + '">' + (data || '-') + '</span>';
          }
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            var btnShow = '<button type="button" class="btn btn-xs btn-info btn-show-detail mr-1" '
              + 'data-id="' + row.id + '" data-notrans="' + row.no_trans + '" title="Show Detail">'
              + '<i class="fa fa-eye"></i> Show</button>';

            var btnPrint = '<a href="pdf_transfer_memo.php?doc_number=' + row.no_trans + '" target="_blank">'
              + '<button type="button" class="btn btn-xs btn-success mr-1" title="Print PDF">'
              + '<i class="fa fa-print"></i> Print</button></a>';

            var btnCancel = '<button type="button" class="btn btn-xs btn-danger btn-cancel-trf" '
              + 'data-id="' + row.id + '" data-notrans="' + row.no_trans + '" title="Cancel">'
              + '<i class="fa fa-times"></i> Cancel</button>';

            if (row.status === 'CANCEL')   return btnShow;
            if (row.status === 'APPROVED') return btnShow + btnPrint;
            return btnShow + btnPrint + btnCancel;
          }
        },
      ],

      columnDefs: [
        { targets: [0,1,2,3,4,5], className: 'text-center align-middle' },
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


</script>

<!-- ===== HANDLER SHOW DETAIL ===== -->
<script>
$(document).on('click', '.btn-show-detail', function () {
  var id      = $(this).data('id');
  var notrans = $(this).data('notrans');

  $('#modal-label-notrans').text('— ' + notrans);
  $('#btn-modal-print').attr('href', 'pdf_transfer_memo.php?doc_number=' + notrans);
  $('#modal-body-transfer').html(
    '<div class="text-center py-5">' +
    '<i class="fa fa-spinner fa-spin fa-2x text-primary"></i>' +
    '<p class="mt-2 text-muted">Memuat data...</p></div>'
  );
  $('#modalDetailTransfer').modal('show');

  $.ajax({
    url: 'get_detail_transfer_memo.php',
    method: 'GET',
    data: { id: id },
    dataType: 'json',
    success: function (res) {
      var h    = res.header;
      var rows = res.detail || [];

      if (!h) {
        $('#modal-body-transfer').html(
          '<div class="alert alert-warning">Data tidak ditemukan.</div>'
        );
        return;
      }

      /* badge status */
      var sc = { APPROVED:'success', CANCEL:'danger', DRAFT:'warning' }[h.status] || 'secondary';

      var html =
        '<div class="row mb-3">' +
          '<div class="col-md-6">' +
            '<table class="table table-sm table-borderless" style="font-size:13px;">' +
              '<tr><th class="text-muted" style="width:130px;">No Transfer</th>' +
                  '<td>: <b>' + (h.no_trans || '-') + '</b></td></tr>' +
              '<tr><th class="text-muted">Tgl Transfer</th>' +
                  '<td>: ' + (h.tgl_trans || '-') + '</td></tr>' +
              '<tr><th class="text-muted">Keterangan</th>' +
                  '<td>: ' + (h.keterangan || '-') + '</td></tr>' +
              '<tr><th class="text-muted">Status</th>' +
                  '<td>: <span class="badge badge-' + sc + '">' + (h.status || '-') + '</span></td></tr>' +
            '</table>' +
          '</div>' +
          '<div class="col-md-6">' +
            '<table class="table table-sm table-borderless" style="font-size:13px;">' +
              '<tr><th class="text-muted" style="width:130px;">Created By</th>' +
                  '<td>: ' + (h.created_by || '-') + '</td></tr>' +
              '<tr><th class="text-muted">Created At</th>' +
                  '<td>: ' + (h.created_at || '-') + '</td></tr>' +
            '</table>' +
          '</div>' +
        '</div>' +
        '<hr class="my-2">' +
        '<div class="table-responsive">' +
          '<table class="table table-bordered table-striped table-sm" style="font-size:12px;">' +
            '<thead>' +
              '<tr style="background:#1E3A8A; color:#fff;">' +
                '<th style="width:35px; text-align:center;">#</th>' +
                '<th>No Memo</th>' +
                '<th>Tgl Memo</th>' +
                '<th>Supplier</th>' +
                '<th>Buyer</th>' +
                '<th>Jns Transaksi</th>' +
                '<th>Jns Pengiriman</th>' +
                '<th>Keterangan</th>' +
              '</tr>' +
            '</thead>' +
            '<tbody>';

      if (rows.length > 0) {
        $.each(rows, function (i, d) {
          html +=
            '<tr>' +
              '<td class="text-center">' + (i + 1) + '</td>' +
              '<td>' + (d.nm_memo         || '-') + '</td>' +
              '<td>' + (d.tgl_memo         || '-') + '</td>' +
              '<td>' + (d.supplier         || '-') + '</td>' +
              '<td>' + (d.buyer            || '-') + '</td>' +
              '<td>' + (d.jns_trans        || '-') + '</td>' +
              '<td>' + (d.jns_pengiriman   || '-') + '</td>' +
              '<td>' + (d.keterangan       || '-') + '</td>' +
            '</tr>';
        });
      } else {
        html += '<tr><td colspan="8" class="text-center text-muted">Tidak ada data detail</td></tr>';
      }

      html += '</tbody></table></div>';
      $('#modal-body-transfer').html(html);
    },
    error: function () {
      $('#modal-body-transfer').html(
        '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Gagal memuat data. Silakan coba lagi.</div>'
      );
    }
  });
});

/* ===== HANDLER CANCEL ===== */
$(document).on('click', '.btn-cancel-trf', function () {
  var id      = $(this).data('id');
  var notrans = $(this).data('notrans');

  Swal.fire({
    title: 'Konfirmasi Cancel',
    html : 'Yakin ingin <b>cancel</b> Transfer Memo<br><b>' + notrans + '</b>?<br>' +
           '<small class="text-muted">Semua item dalam memo ini akan dibatalkan.</small>',
    icon : 'warning',
    showCancelButton   : true,
    confirmButtonColor : '#dc3545',
    cancelButtonColor  : '#6c757d',
    confirmButtonText  : '<i class="fa fa-check"></i> Ya, Cancel!',
    cancelButtonText   : 'Tidak'
  }).then(function (result) {
    if (!result.isConfirmed) return;

    Swal.fire({ title: 'Memproses...', allowOutsideClick: false,
                didOpen: function () { Swal.showLoading(); } });

    /* ambil id_h dari detail, lalu kirim cancel */
    $.ajax({
      url: 'get_detail_transfer_memo.php',
      method: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function (res) {
        var cancelIds = (res.detail || []).map(function (d) { return d.id_h; });

        $.ajax({
          url: 'update_transfer_memo_cancel.php',
          method: 'POST',
          data: { no_trans: notrans, cancel_ids: cancelIds },
          dataType: 'json',
          success: function (r) {
            if (r.status === 'success') {
              Swal.fire({
                icon: 'success', title: 'Berhasil',
                text: 'Transfer Memo ' + notrans + ' telah di-cancel.',
                timer: 2000, showConfirmButton: false
              }).then(function () { datatable.ajax.reload(); });
            } else {
              Swal.fire('Gagal', r.message || 'Terjadi kesalahan.', 'error');
            }
          },
          error: function () {
            Swal.fire('Error', 'Gagal menghubungi server. Silakan coba lagi.', 'error');
          }
        });
      },
      error: function () {
        Swal.fire('Error', 'Gagal mengambil data detail.', 'error');
      }
    });
  });
});
</script>

</body>

</html>
