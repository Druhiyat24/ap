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
    <h5 class="mb-0"><i class="fas fa-thumbs-up"></i> APPROVAL TRANSFER MEMO
    <span id="parent_notif" class="ms-2 position-relative">
    <i class="fa fa-bell text-white" style="font-size:18px;"></i>

    <?php
    $q = mysqli_query($conn_li, "SELECT COUNT(*) as total FROM transfer_memo_exim_h WHERE status = 'POST' and no_trans like '%TETF%'");
    $d = mysqli_fetch_assoc($q);

    $total_pending = $d['total'];

    if($total_pending > 0): ?>
        <span id="badge_pending" 
      class="position-absolute top-0 start-100 translate-middle bg-danger text-white d-flex align-items-center justify-content-center"
      style="font-size:10px; width:18px; height:18px; border-radius:50%;">
            <?php echo $total_pending; ?>
        </span>
    <?php endif; ?>
</span>


</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="approve_transfer_memo.php" method="post">
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

#modalDetail table{
    font-size:14px;
}

#modalDetail table th{
    font-size:14px;
    font-weight:600;
}

#modalDetail table td{
    font-size:14px;
}

#modalDetail table input,
#modalDetail table select{
    font-size:14px;
    height:36px;
}
#modalDetail table th:nth-child(3),
#modalDetail table td:nth-child(3){
    width:220px;
    min-width:220px;
}

#modalDetail table th:nth-child(8),
#modalDetail table td:nth-child(8){
    width:50px;
    min-width:50px;
}

#modalDetail textarea{
    overflow:hidden;
    resize:none;
    width:100%;
}
#modalDetail .select2-container{
    width:100% !important;
    font-size:14px;
}

#modalDetail .select2-dropdown{
    width:auto !important;
    min-width:250px !important;
    max-width:420px !important;
}

#modalDetail .select2-results__options{
    max-height:220px !important;
    overflow-y:auto !important;
}

#modalDetail .select2-search--dropdown .select2-search__field{
    width:100% !important;
    font-size:14px;
}


</style>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:75%;width:75%;">
    <div class="modal-content">

      <div class="modal-header text-white" style="background-color: #1E3A8A;">
        <h5 class="modal-title">
          <i class="far fa-list-alt"></i> Detail Transfer Memo
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          &times;
        </button>
      </div>


      <div class="modal-body">

        <!-- HEADER -->
        <div class="row">
          <div class="col-6 mb-1">
            <strong>No Transfer</strong><br>
            <span id="d_no_trans">-</span>
          </div>

          <div class="col-6 mb-1">
            <strong>Transfer Date</strong><br>
            <span id="d_tgl_trans">-</span>
          </div>
        </div>

        <hr>

        <table class="table table-bordered table-striped table-sm" id="detailTable">
            <thead class="table-light">
                <tr>
                    <th>No Memo</th>
                    <th>Tgl Memo</th>
                    <th>Supplier</th>
                    <th>Jenis Transaksi</th>
                    <th>Jenis Pengiriman</th>
                    <th>Buyer</th>
                    <th>Description</th>
                    <th>check</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

      </div>
      <div class="modal-footer">
    <button type="button" class="btn btn-success" id="btnApprove">
        Approve
    </button>
    <button type="button" class="btn btn-danger" id="btnCancelAll">
        Cancel
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

  let detailDT = null;
let currentNoTrans = '';

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
        url: 'ajx_app_transfer_memo.php',
        type: 'POST',
        dataSrc: function (json) {

            console.log(json); // 🔥 debug dulu (penting)

            let badge = document.getElementById('badge_pending');
            let parent = document.getElementById('parent_notif');

            let total = parseInt(json.total_pending) || 0;

            if (total > 0) {
    if (!badge) {
        let span = document.createElement('span');
        span.id = 'badge_pending';

        span.className = 'position-absolute bg-danger text-white d-flex align-items-center justify-content-center';

        span.style.top = '-5px';
        span.style.right = '-10px';
        span.style.fontSize = '10px';
        span.style.width = '18px';
        span.style.height = '18px';
        span.style.borderRadius = '50%';

        span.innerText = total;

        if (parent) parent.appendChild(span);
    } else {
        badge.innerText = total;
    }
} else {
    if (badge) badge.remove();
}


            return json.data;
        },
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
        }
      },

      columns: [
      { data: 'no_trans' },
      { data: 'tgl_trans' },
      { data: 'keterangan' },
      { data: 'create_user' },
      { data: 'id' },
      ],

      columnDefs: [
        {
            targets: [0,1,2,3],
            render: function(data) {
                return data ? data : "-";
            }
        },
        {
            targets: [4],
            render: function(data, type, row) {
                return `
                <div class="d-flex gap-1 justify-content-center">
                    <a href="javascript:void(0)" 
                       class="btn btn-sm btn-info d-flex align-items-center gap-1"
                       onclick='showDetail("${row.id}")'>
                        <i class="fa-solid fa-circle-info"></i> Action
                    </a>
                </div>`;
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


function showDetail(id) {

    let url = "get_detail_transfer_memo.php?id=" + id;

    $.get(url, function(res){

        $("#d_no_trans").text(res?.header?.no_trans ?? '-');
        $("#d_tgl_trans").text(res?.header?.tgl_trans ?? '-');
        currentNoTrans = res?.header?.no_trans ?? '';


        // STATUS BADGE
        let status = (res?.header?.status ?? '-').toUpperCase();
        let badge = 'bg-secondary';
        if(status === 'APPROVED') badge = 'bg-success';
        else if(status === 'DRAFT') badge = 'bg-warning text-dark';
        else if(status === 'REJECTED') badge = 'bg-danger';

        $("#d_status").removeClass().addClass('badge '+badge).text(status);

        // reset datatable
        if (detailDT !== null) detailDT.clear().destroy();
        $("#detailTable tbody").html('');

        // isi rows
        let rows = '';
        res.detail.forEach((d,i)=>{
            rows += `
                <tr>
                    <td>${d.nm_memo ?? ''}</td>
                    <td>${d.tgl_memo ?? ''}</td>
                    <td>${d.supplier ?? ''}</td>
                    <td>${d.jns_trans ?? ''}</td>
                    <td>${d.jns_pengiriman ?? ''}</td>
                    <td>${d.buyer ?? ''}</td>
                    <td>${d.keterangan ?? ''}</td>
                    <td>
    <div class="d-flex justify-content-center align-items-center">
        <input type="checkbox" 
               class="form-check-input chk-detail" 
               value="${d.id_h}" 
               checked>
    </div>
</td>

                </tr>
            `;
        });
        $("#detailTable tbody").html(rows);

        // init datatable
        detailDT = $("#detailTable").DataTable({

            searching: true,
            paging: true,
            ordering: true,
            info: false,
            lengthChange: false,
            pageLength: 10,

            columnDefs:[
                
            ],
        });

        $("#modalDetail").modal('show');
    });
}


$('#btnApprove').on('click', function(){

    let approveIds = [];
    let cancelIds  = [];

    let table = $('#detailTable').DataTable(); // 🔥 FIX paging

    table.$('.chk-detail').each(function(){
        let id = $(this).val();

        if($(this).is(':checked')){
            approveIds.push(id);
        }else{
            cancelIds.push(id);
        }
    });

    let totalApprove = approveIds.length;
    let totalCancel  = cancelIds.length;

    if(totalApprove === 0 && totalCancel === 0){
        Swal.fire('Warning','Tidak ada data','warning');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Approve',
        html: `
            <b>Approve:</b> ${totalApprove} data<br>
            <b>Cancel:</b> ${totalCancel} data
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses',
        cancelButtonText: 'Batal'
    }).then((result) => {

        if(result.isConfirmed){

            $.ajax({
                url: "update_transfer_memo_approve.php",
                type: "POST",
                dataType: "json", // 🔥 WAJIB
                data: {
                    no_trans: currentNoTrans,
                    approve_ids: approveIds,
                    cancel_ids: cancelIds,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function(){
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Sedang menyimpan data',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(res){

                    if(res.status === 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil diproses'
                        });

                        $('#modalDetail').modal('hide');
                        dataTableReload();
                    }else{
                        Swal.fire('Gagal', res.message || 'Error', 'error');
                    }

                },
                error: function(xhr){
                    console.log(xhr.responseText); // 🔥 debug
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan'
                    });
                }
            });

        }

    });

});


$('#btnCancelAll').on('click', function(){

    let allIds = [];

    let table = $('#detailTable').DataTable(); // 🔥 FIX paging

    table.$('.chk-detail').each(function(){
        allIds.push($(this).val());
    });

    let totalData = allIds.length;

    if(totalData === 0){
        Swal.fire('Warning','Tidak ada data untuk dicancel','warning');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Cancel',
        html: `
            <b>No Transfer:</b> ${currentNoTrans}<br><br>
            Semua data (<b>${totalData}</b>) akan di <b>Cancel</b>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Cancel Semua',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33'
    }).then((result) => {

        if(result.isConfirmed){

            $.ajax({
                url: "update_transfer_memo_cancel.php",
                type: "POST",
                dataType: "json", // 🔥 WAJIB
                data: {
                    no_trans: currentNoTrans,
                    cancel_ids: allIds
                },
                beforeSend: function(){
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Sedang cancel semua data',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(res){

                    if(res.status === 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `Semua data (${totalData}) berhasil di-cancel`
                        });

                        $('#modalDetail').modal('hide');
                        dataTableReload();
                    }else{
                        Swal.fire('Gagal', res.message || 'Error', 'error');
                    }

                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat cancel'
                    });
                }
            });

        }

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

  this.href = `ekspor_approve_transfer_memo.php?status=${status}&start_date=${start_date}&end_date=${end_date}`;
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "approve_transfer_memo.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "approve_transfer_memo.php";
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
