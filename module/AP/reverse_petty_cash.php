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

    .select2-container {
      width: 100% !important;
      max-width: 100% !important;
      min-width: 100% !important;
  }

  .select2-container--bootstrap4 .select2-selection {
      height: calc(1.5em + .5rem + 2px) !important;  /* sama seperti form-control-sm */
      padding: .25rem .5rem !important;
      font-size: .875rem !important;  /* font kecil */
      line-height: 1.5 !important;
      border-radius: .2rem !important;
  }

  .select2-container--bootstrap4 .select2-selection__rendered {
      line-height: 1.5 !important;
      font-size: 12px !important;
  }

  .select2-container--bootstrap4 .select2-results__option {
      font-size: 12px !important;
      padding: 4px 8px; /* biar rapat */
  }

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-history"></i> REVERSE PETTY CASH</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="reverse_petty_cash.php" method="post">
    <div class="row g-3">
      <!-- Supplier -->
      <div class="col-md-4">
        <label for="profit_center"><b>Type Document <i style="color: red;">*</i></b></label>            
        <select class="form-control form-control-sm select2bs4" name="type_doc" id="type_doc" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" selected="true">ALL</option>                                                 
            <option value="PETTY CASH IN" <?php
            $type_doc = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
            }                 
            if($type_doc == 'PETTY CASH IN'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >PETTY CASH IN</option>

            <option value="PETTY CASH OUT" <?php
            $type_doc = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
            }                 
            if($type_doc == 'PETTY CASH OUT'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >PETTY CASH OUT</option>

        </select>
    </div>

    <!-- Filter -->
    <div class="col-md-2">
        <label for="filter" class="form-label"><b>Status</b></label>
        <select class="form-control select2bs4" name="filter" id="filter" data-dropup-auto="false" data-live-search="true">
            <option value="ALL" selected="true">ALL</option>
            <option value="DRAFT" <?php
            $status = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
            }                 
            if($status == 'DRAFT'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >DRAFT</option>
            <option value="APPROVED" <?php
            $status = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
            }                 
            if($status == 'APPROVED'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >APPROVED</option>
            <option value="CANCEL" <?php
            $status = '';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
            }                 
            if($status == 'CANCEL'){
                $isSelected = ' selected="selected"';
            }else{
                $isSelected = '';
            }
            echo $isSelected;
            ?>
            >CANCEL</option>                                                                                                            
        </select>
    </div>

    <!-- Spacer biar rata -->
    <div class="col-md-6"></div>

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
  <button type="submit" class="btn btn-info btn-sm me-2">
    <i class="fa fa-search"></i> Search
</button>
<!-- <button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
    <i class="fa fa-undo"></i> Reset
</button> -->

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Reverse Payment'");
$rs = mysqli_fetch_array($querys);
$id = isset($rs['id']) ? $rs['id'] : 0;

if($id == '96'){
    // echo '<button id="btncreate" type="button" class="btn btn-primary btn-sm ml-2"><span class="fa fa-pencil-square-o"></span> Create</button>';
}else{
    echo '';
}

echo '<button id="btncreate" type="button" class="btn btn-primary btn-sm ml-2"><span class="fa fa-pencil-square-o"></span> Create</button>';
?>

<?php
$type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null; 
$status = isset($_POST['filter']) ? $_POST['filter']: null;
$start_date = date("Y-m-d",strtotime($_POST['start_date']));
$end_date = date("Y-m-d",strtotime($_POST['end_date'])); 

if ($status == 'ALL' && $type_doc == 'ALL') {
    $where = '';
}elseif($status != 'ALL' && $type_doc == 'ALL'){
    $where = " AND a.status = '$status' ";
}elseif($status == 'ALL' && $type_doc != 'ALL'){
    $where = " AND a.type_doc = '$type_doc' ";
}else{
    $where = " AND a.status = '$status' AND a.type_doc = '$type_doc'";
}

$sql = "select a.rvs_number, rvs_date, type_doc, doc_number, doc_date, nama_supp, curr, total, IF(b.deskripsi is null OR b.deskripsi = '',a.deskripsi,b.deskripsi) deskripsi, a.status, a.created_by, a.created_date from ap_reverse_h a INNER JOIN ap_reverse_det b on b.rvs_number = a.rvs_number where rvs_date BETWEEN '$start_date' AND '$end_date' and b.status = 'Y' and a.rvs_number like '%PC/%' $where";


echo '<a target="_blank" href="ekspor_reverse_petty_cash.php?type_doc='.$type_doc.'&& status='.$status.'&& start_date='.$start_date.' && end_date='.$end_date.'&& query='.$sql.'"><button type="button" class="btn btn-success ml-2" style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
?>     

</div>

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
              <th>No Reverse</th>
              <th>Reverse Date</th>
              <th>Type Document</th>
              <th>Descriptions</th>
              <th>Status</th>
              <th>Created By</th>
              <th>Created Date</th>
              <th>Action</th>
          </tr>
      </thead>
      <tbody>
        <?php
        $type_doc ='';
        $status = '';
        $start_date ='';
        $end_date ='';
        $date_now = date("Y-m-d");                    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null; 
            $status = isset($_POST['filter']) ? $_POST['filter']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
        }

        if ($status == 'ALL' && $type_doc == 'ALL') {
            $where = '';
        }elseif($status != 'ALL' && $type_doc == 'ALL'){
            $where = " AND status = '$status' ";
        }elseif($status == 'ALL' && $type_doc != 'ALL'){
            $where = " AND type_doc = '$type_doc' ";
        }else{
            $where = " AND status = '$status' AND type_doc = '$type_doc'";
        }
        $sql = mysqli_query($conn2,"select rvs_number, rvs_date, type_doc, deskripsi, status, created_by, created_date from ap_reverse_h where rvs_date BETWEEN '$start_date' AND '$end_date' and rvs_number like '%PC/%' $where");


        function formatDateOrDash($date) {
            return (!empty($date) && $date != '0000-00-00')
            ? date("d-M-Y", strtotime($date))
            : '-';
        }

        function valueOrDash($value) {
            return !empty($value) ? $value : '-';
        }

        while ($row = mysqli_fetch_array($sql)) {

            echo '<tr style="font-size: 12px; text-align: center;">';
            echo '<td value="'.$row['rvs_number'].'">'.$row['rvs_number'].'</td>';
            echo '<td value="'.$row['rvs_date'].'">'.formatDateOrDash($row['rvs_date']).'</td>';
            echo '<td value="'.$row['type_doc'].'">'.$row['type_doc'].'</td>';
            echo '<td style="width: 250px; text-align: left;" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>';
            echo '<td value="'.$row['status'].'">'.$row['status'].'</td>';
            echo '<td value="'.$row['created_by'].'">'.$row['created_by'].'</td>';
            echo '<td value="'.$row['created_date'].'">'.$row['created_date'].'</td>';
            if($row['status'] == 'DRAFT'){
                echo '<td><a id="showdet" ><button style="border-radius: 6px" type="button" class="btn-xs btn-warning"><i class="fas fa-eye"aria-hidden="true"> Show</i></button></a>
                <a href="pdf_reverse_ap.php?rvs_number='.$row['rvs_number'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="far fa-file-pdf"> Pdf</i></button></a>
                <button style="border-radius: 6px" type="button" 
                class="btn-xs btn-danger btn-cancel" 
                data-rvs="'.$row['rvs_number'].'">
                <i class="fas fa-times"></i> Cancel
                </button>
                </td> ';
            }elseif($row['status'] == 'APPROVED'){
                echo '<td><a id="showdet" ><button style="border-radius: 6px" type="button" class="btn-xs btn-warning"><i class="fas fa-eye"aria-hidden="true"> Show</i></button></a>
                <a href="pdf_reverse_ap.php?rvs_number='.$row['rvs_number'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="far fa-file-pdf"> Pdf</i></button></a>
                </td> ';
            }else{
                echo '<td>-</td> ';
            }
            echo '</tr>';
        }
        ?>
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

<div class="modal fade" id="modal-show" data-target="#modal-show" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #2563EB;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_rvs"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_rvs_date" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_doc_type" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
                  <div id="txt_deskripsi" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>                                                    
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;">
                      <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                        <thead>
                            <tr>
                                <th style="width:20%;">No Document</th>
                                <th style="width:13%;">Document Date</th> 
                                <th style="width:19%;">Supplier</th>
                                <th style="width:10%;">Curr</th>
                                <th style="width:13%;">Total</th>  
                                <th style="width:25%;">Deskripsi</th>

                            </tr>
                        </thead>

                        <tbody id="datatable_modal">

                        </tbody>
                    </table> 
                </div>           
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
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>

 $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
    })
  });
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

<script>
$(document).on("click", ".btn-cancel", function () {
    let rvs_number = $(this).data("rvs");

    Swal.fire({
        title: "Are you sure?",
        text: "This document will be cancelled!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, cancel it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "cancel_reverse_ap.php",
                data: { rvs_number: rvs_number },
                success: function (res) {
                    Swal.fire({
                        icon: "success",
                        title: "Cancelled",
                        text: "Document has been cancelled successfully."
                    }).then(() => {
                        location.reload(); // refresh halaman
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to cancel document!"
                    });
                }
            });
        }
    });
});
</script>


<script type="text/javascript">
    $("table tbody tr").on("click", "#showdet", function(){                 
        var no_rvs = $(this).closest('tr').find('td:eq(0)').attr('value');
        var rvs_date = $(this).closest('tr').find('td:eq(1)').attr('value');
        var type_doc = $(this).closest('tr').find('td:eq(2)').attr('value');
        var deskripsi = $(this).closest('tr').find('td:eq(3)').attr('value');
        
        $.ajax({
            type:'POST',
            url:'get_data_reverse_ap.php',
            data: {'no_rvs':no_rvs},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#datatable_modal').html(data);

                // alert(data);  
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 

        $('#txt_rvs').html(no_rvs);
        $('#txt_rvs_date').html('<b>Reverse Date : </b>' + rvs_date + '');
        $('#txt_doc_type').html('<b>Type Document : </b>' + type_doc + '');
        $('#txt_deskripsi').html('<b>Descriptions : </b>' + deskripsi + '');
        $('#modal-show').modal('show');
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
        location.href = "form_reverse_petty_cash.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "reverse_petty_cash.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
