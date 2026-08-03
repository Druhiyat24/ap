<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;
    }

    input {
        font-size: 14px;
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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .kbon-action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    }

    .kbon-status-label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
        white-space: nowrap;
    }
</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0">
        <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
            <h5 class="mb-0"><i class="fa fa-money" aria-hidden="true"></i> LIST PETTY CASH IN</h5>
        </div>

        <div class="card-body p-3">
            <form id="form-data" action="petty-cashin.php" method="post">
                <div class="row g-3">
                 <div class="col-md-3">
                    <label for="nama_supp"><b>Refference</b></label>
                    <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" >
                        <option value="ALL" selected="true">ALL</option>
                        <?php
                        $nama_supp ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                        }
                        $sql = mysqli_query($conn1,"select ref_doc from master_forpay where ket = '5'");
                        while ($row = mysqli_fetch_array($sql)) {
                            $data = $row['ref_doc'];
                            if($row['ref_doc'] == $_POST['nama_supp']){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                        }?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="doc_Number"><b>Document Number</b></label>
                    <input type="text" class="form-control form-control-sm" name="doc_num" id="doc_num" style="font-size: 12px; text-align: left;"
                    value="<?php
                    $doc_num ='';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $doc_num = isset($_POST['doc_num']) ? $_POST['doc_num']: null;
                    }
                    if(!empty($_POST['doc_num'])) {
                     echo $_POST['doc_num'];
                 }
                 else{
                     echo '';
                 } ?>" autocomplete="off">
             </div>


             <div class="col-md-2">
                <label for="start_date"><b>From</b></label>
                <input type="text" style="font-size: 12px;" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
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
             } ?>"
             placeholder="Tanggal Awal" >
         </div>

         <div class="col-md-2">
            <label for="end_date"><b>To</b></label>
            <input type="text" style="font-size: 12px;" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
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
         } ?>"
         placeholder="Tanggal Awal" >
     </div>
     <div class="col-md-3 d-flex align-items-end">
        <button type="submit" id="submit" class="btn btn-info btn-sm me-2">
            <i class="fa fa-search" aria-hidden="true"></i> Search</button>
        
        <?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Cash'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '39'){
            echo '<button id="btncreate_new" type="button" class="btn btn-primary btn-sm ml-2"><i class="fa fa-plus-circle" aria-hidden="true"></i> Create</button>';
        }
        ?>

        <button type="button" id="btnExportExcel" class="btn btn-success btn-sm ml-2">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
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

            <table id="datatable" class="table table-striped table-bordered table-hover table-sm" role="grid" cellspacing="0" width="100%">
                <thead class="table-gradient">
                    <tr>
                        <th style="text-align: center;vertical-align: middle;">Document Number</th>
                        <th style="text-align: center;vertical-align: middle;">Date</th>
                        <th style="text-align: center;vertical-align: middle;">Refference</th>
                        <th style="text-align: center;vertical-align: middle;">Refference Document</th>
                        <th style="text-align: center;vertical-align: middle;">Other Document</th>
                        <th style="text-align: center;vertical-align: middle;">Account</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Status</th>
                        <th style="text-align: center;vertical-align: middle;width: 220px;">Action</th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>

        </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
                  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                     
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
              </div>
          </div>
      </div>
    <!-- /.modal-content 
  </div>
      /.modal-dialog 
  </div> -->         

</div><!-- body-row END -->
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
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
<script type="text/javascript">
    var datatable = $('#datatable').DataTable({
        ordering: false,
        processing: true,
        serverSide: false,
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,

        ajax: {
            url: 'ajx_petty_cashin.php',
            type: 'POST',
            data: function(d) {
                d.reference = $('#nama_supp').val();
                d.doc_num = $('#doc_num').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },

        columns: [
            { data: 'no_pci' },
            { data: 'tgl_pci' },
            { data: 'reff' },
            { data: 'reff_doc' },
            { data: 'oth_doc' },
            { data: 'nama_coa' },
            { data: 'amount' },
            { data: 'status' },
            { data: 'action', orderable: false },
        ],

        columnDefs: [
            { targets: [3, 4, 5], className: 'text-left' },
            { targets: [6], className: 'text-right' },
            { targets: [0, 1, 2, 7, 8], className: 'text-center' },
        ],
    });

    function dataTableReload() {
        datatable.ajax.reload();
    }

    $('#form-data').on('submit', function(e) {
        e.preventDefault();
        dataTableReload();
    });

    $("[data-toggle=tooltip]").tooltip();

    document.getElementById('btnExportExcel').onclick = function() {

        Swal.fire({
            title: 'Menyiapkan Excel...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        window.location.href = 'ekspor_petty_cash_in.php?reference=' + encodeURIComponent($('#nama_supp').val())
            + '&doc_num=' + encodeURIComponent($('#doc_num').val())
            + '&start_date=' + encodeURIComponent($('#start_date').val())
            + '&end_date=' + encodeURIComponent($('#end_date').val());

        setTimeout(function() {
            Swal.close();
        }, 1500);
    };
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });

    $(document).on("click", ".edit-none", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num); // sama dengan base64_encode di PHP

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_in_none.php?doc_num=" + encodedDocNum;
            }
        });
    });


    $(document).on("click", ".edit-settle", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num); // sama dengan base64_encode di PHP

        Swal.fire({
            title: "Are you sure?",
            text: "You are about to edit this document.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_in_settle.php?doc_num=" + encodedDocNum;
            }
        });
    });

    $(document).on("click", ".edit-cashout", function() {
        let doc_num = $(this).data("pettycash");
        let encodedDocNum = btoa(doc_num); // sama dengan base64_encode di PHP

        Swal.fire({
            title: "Are you sure?",
            text: "You will be redirected to the edit form.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "form_edit_pettycash_in_cashout.php?doc_num=" + encodedDocNum;
            }
        });
    });

    $(document).on("click", ".cancel-pci", function() {
        let doc_num = $(this).data("pettycash");
        let cancel_user = '<?php echo $user; ?>';

        Swal.fire({
            title: "Are you sure cancel " + doc_num + "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes, cancel it!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                type: 'POST',
                url: 'cancelpetcashin.php',
                data: { no_pci: doc_num, cancel_user: cancel_user },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message
                        }).then(() => {
                            dataTableReload();
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    console.log("ERROR AJAX:", xhr.responseText);
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        });
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#active", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'activebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Active");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
             alert(xhr);
         }
     });
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#deactive", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'deactivebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Deactive");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
             alert(xhr);
         }
     });
    });
</script>


<script type="text/javascript">
    $(document).on("click", ".btn-show-pci", function() {
        const data = datatable.row($(this).closest('tr')).data();
        if (!data) return;

        $('#mymodal').modal('show');

        $.ajax({
            type: 'post',
            url: 'ajaxpettyin.php',
            data: { 'no_ib': data.no_pci, 'refdoc': data.reff },
            success: function(html) {
                $('#details').html(html);
            }
        });

        $('#txt_bpb').html(data.no_pci);
        $('#txt_tglbpb').html('Date : ' + data.tgl_pci_raw);
        $('#txt_no_po').html('Refference : ' + data.reff);
        $('#txt_supp').html('Refference Document : ' + data.reff_doc);
        $('#txt_top').html('Other Document : ' + data.oth_doc);
        $('#txt_curr').html('Kas Account : ' + data.nama_coa);
        $('#txt_confirm').html('Currency : IDR');
        $('#txt_tgl_po').html('Description : ' + data.deskripsi);
    });
</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-petty-cashin.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('btncreate_new').onclick = function () {
        location.href = "create-pettycash-in.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "petty-cashin.php";
    };
</script>

<script>
    function alert_cancel() {
      alert("Master Bank Deactive");
      location.reload();
  }
  function alert_approve() {
      alert("Master Bank Active");
      location.reload();
  }
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>