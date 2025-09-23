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
    <h5 class="mb-0"><i class="fas fa-thumbs-up"></i> APPROVAL REVERSE PAYMENT <span class="badge bg-danger ms-2">
        <i class="fas fa-bell"></i> 
        <?php
        $type_doc ='ALL';
        $where ='';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: 'ALL';            
        }

        if($type_doc == 'ALL'){
            $where = '';
        }else {
            $where ="AND type_doc = '$type_doc'";
        }
        
        $sql = mysqli_query($conn2,"select COUNT(id) jml from (select id from ap_reverse_h where rvs_number like '%SI/%' and status = 'DRAFT' $where) a");
        $row = mysqli_fetch_array($sql);
        $jml = $row['jml'];
        echo $jml;
        ?>

    </span></h5>
</div>

<div class="card-body p-3">
    <form id="form-data" action="form_approve_reverse_kontrabon.php"method="post">
        <div class="row g-3">
          <!-- Supplier -->
          <div class="col-md-3">
            <label for="profit_center"><b>Type Document</b></label>            
            <select class="form-control form-control-sm select2bs4" name="type_doc" id="type_doc" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $type_doc = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
                }                 
                if($type_doc == 'ALL'){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo $isSelected;
                ?>                
                >ALL</option>                                                    
                <option value="KONTRABON REGULAR" <?php
                $type_doc = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
                }                 
                if($type_doc == 'KONTRABON REGULAR'){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo $isSelected;
                ?>
                >KONTRABON REGULAR</option>

                <option value="KONTRABON CBD" <?php
                $type_doc = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
                }                 
                if($type_doc == 'KONTRABON CBD'){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo $isSelected;
                ?>
                >KONTRABON CBD</option>

                <option value="KONTRABON DP" <?php
                $type_doc = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: null;
                }                 
                if($type_doc == 'KONTRABON DP'){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo $isSelected;
                ?>
                >KONTRABON DP</option>

            </select>
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
              <th style="width: 15%;">No Reverse</th>
              <th style="width: 15%;">Reverse Date</th>
              <th style="width: 18%;">Type Document</th>
              <th style="width: 22%;">Descriptions</th>
              <th style="width: 12%;">Created By</th>
              <th style="width: 13%;">Created Date</th>
              <th style="width:5%; text-align: center;"><input type="checkbox" id="select_all"></th>
          </tr>
      </thead>
      <tbody>
        <?php
        function formatDateOrDash($date) {
            return (!empty($date) && $date != '0000-00-00')
            ? date("d-M-Y", strtotime($date))
            : '-';
        }
        $type_doc ='ALL';
        $where ='';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $type_doc = isset($_POST['type_doc']) ? $_POST['type_doc']: 'ALL';

        }

        if($type_doc == 'ALL'){
            $where ='';
        }else {
            $where ="AND type_doc = '$type_doc'";
        }
       

        $sql = mysqli_query($conn2,"select rvs_number, rvs_date, type_doc, deskripsi, status, created_by, created_date from ap_reverse_h where status = 'DRAFT' and rvs_number like '%SI/%' $where");

        while($row = mysqli_fetch_array($sql)){ 
                                           
          echo'<tr style="text-align:center;">';                   
          echo '<td value="'.$row['rvs_number'].'">'.$row['rvs_number'].'</td>';
          echo '<td value="'.$row['rvs_date'].'">'.formatDateOrDash($row['rvs_date']).'</td>';
          echo '<td value="'.$row['type_doc'].'">'.$row['type_doc'].'</td>';
          echo '<td style="width: 250px; text-align: left;" value="'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>';
          echo '<td value="'.$row['created_by'].'">'.$row['created_by'].'</td>';
          echo '<td value="'.$row['created_date'].'">'.$row['created_date'].'</td>';
          echo'<td style="width:10px; text-align: center;"><input type="checkbox" name="select[]" data-id="'.$row['rvs_number'].'"></td>                      
          </tr>';                

      } ?>
  </tbody>
</table>
</div>
<form id="form-simpan">
    <div class="mt-3">
        <button type="button" name="approve" id="approve" class="btn btn-success">
          <i class="fas fa-check-circle"></i> Approve
      </button>

      <button type="button" name="cancel" id="cancel" class="btn btn-danger ml-2">
          <i class="fas fa-times"></i> Cancel
      </button>
  </div>
</form>
</div>

</div>
</div>

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

<script type="text/javascript">
    // Select All
    $("#select_all").on("click", function(){
      $("input[name='selected_ids[]']").prop("checked", this.checked);
  });

    $("#form-simpan").on("click", "#approve", function(e){
    e.preventDefault();

    let selected = $("input[name='select[]']:checked");
    if(selected.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Please check at least one data before proceeding'
        });
        return;
    }

    let approve_user = '<?php echo $user ?>';
    let total = selected.length;
    let processed = 0;
    let errors = 0;

    selected.each(function () {
        let row = $(this).closest('tr');

        let data = {
            rvs_number  : row.find('td:eq(0)').attr('value'),
            approve_user: approve_user
        };

        $.ajax({
            type:'POST',
            url:'approve_reverse_kontrabon.php',
            data: data,
            success: function(response){
                console.log("Approved:", response);
            },
            error: function(xhr, ajaxOptions, thrownError){
                console.error(xhr.responseText);
                errors++;
            },
            complete: function(){
                processed++;
                if(processed === total){
                    if(errors === 0){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: total + ' reverse(s) approved successfully'
                        }).then(() => {
                            window.location = 'form_approve_reverse_kontrabon.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed!',
                            text: (total-errors) + ' approved successfully, ' + errors + ' failed'
                        });
                    }
                }
            }
        });
    });
});



$("#form-simpan").on("click", "#cancel", function(e){
    e.preventDefault();

    let selected = $("input[name='select[]']:checked");
    if(selected.length === 0){
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Please check at least one data before proceeding'
        });
        return;
    }

    let approve_user = '<?php echo $user ?>';
    let total = selected.length;
    let processed = 0;
    let errors = 0;

    selected.each(function () {
        let row = $(this).closest('tr');

        let data = {
            rvs_number  : row.find('td:eq(0)').attr('value'),
            approve_user: approve_user
        };

        $.ajax({
            type:'POST',
            url:'cancel_reverse_ap.php',
            data: data,
            success: function(response){
                console.log("Cancelled:", response);
            },
            error: function(xhr, ajaxOptions, thrownError){
                console.error(xhr.responseText);
                errors++;
            },
            complete: function(){
                processed++;
                if(processed === total){
                    if(errors === 0){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: total + ' reverse(s) Cancelled successfully'
                        }).then(() => {
                            window.location = 'form_approve_reverse_kontrabon.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed!',
                            text: (total-errors) + ' Cancelled successfully, ' + errors + ' failed'
                        });
                    }
                }
            }
        });
    });
});


</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
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
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
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
        location.href = "form_approve_reverse_kontrabon.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "form_approve_reverse_kontrabon.php";
    };
</script>


<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
