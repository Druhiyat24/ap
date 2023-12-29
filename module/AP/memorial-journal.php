<?php include '../header.php' ?>
<style >
    .modal {
  text-align: center;
  padding: 0!important;
}

.modal:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px;
}

.modal-dialog {
  display: inline-table;
  width: 700px;
  text-align: left;
  vertical-align: middle;
}
</style>
    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST MEMORIAL JOURNAL</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="memorial-journal.php" method="post">        
        <div class="form-row">
           <div class="col-md-3">
            <label for="nama_supp"><b>Type</b></label>            
              <select class="form-control selectpicker" name="nama_type" id="nama_type" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $nama_type = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null;
                }                 
                    if($nama_type == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>                                 
                <?php
                $nama_type ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null;
                }                 
                $sql = mysqli_query($conn1,"select id_cmj,CONCAT(id_cmj,'-',nama_cmj) as type,nama_cmj from master_category_mj");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['id_cmj'];
                    $data2 = $row['nama_cmj'];
                    if($row['id_cmj'] == $_POST['nama_type']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                }?>
                </select>
                </div>  

                <div class="col-md-3">
            <label for="nama_supp"><b>Status</b></label>            
              <select class="form-control selectpicker" name="Status" id="Status" data-dropup-auto="false" data-live-search="true" >
                <option value="ALL" <?php
                $Status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Status = isset($_POST['Status']) ? $_POST['Status']: null;
                }                 
                    if($Status == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>
                <option value="Draft" <?php
                $Status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Status = isset($_POST['Status']) ? $_POST['Status']: null;
                }                 
                    if($Status == 'Draft'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Draft</option>
                <option value="Post" <?php
                $Status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Status = isset($_POST['Status']) ? $_POST['Status']: null;
                }                 
                    if($Status == 'Post'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Post</option>
                <option value="Cancel" <?php
                $Status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Status = isset($_POST['Status']) ? $_POST['Status']: null;
                }                 
                    if($Status == 'Cancel'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Cancel</option>
                </select>
                </div> 

            <div class="col-md-2 mb-3"> 
            <label for="start_date"><b>From</b></label>          
            <input type="text" style="font-size: 12px;" class="form-control tanggal" id="start_date" name="start_date" 
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
            placeholder="Tanggal Awal">
            </div>

            <div class="col-md-2 mb-3"> 
            <label for="end_date"><b>To</b></label>          
            <input type="text" style="font-size: 12px;" class="form-control tanggal" id="end_date" name="end_date" 
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
            placeholder="Tanggal Awal">
            </div>
            <div class="input-group-append col">                                   
            <button  type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button>

<!--     <?php
        $status = isset($_POST['status']) ? $_POST['status']: null;

        if($status == 'ALL'){
            echo '<a target="_blank" href="ekspor_lp_all.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($status == 'draft'){
            echo '<a target="_blank" href="ekspor_lp_draft.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($status == 'Approved'){
            echo '<a target="_blank" href="ekspor_lp_app.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($status == 'Cancel'){
            echo '<a target="_blank" href="ekspor_lp_cancel.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($status == 'Closed'){
            echo '<a target="_blank" href="ekspor_lp_closed.php?nama_supp='.$nama_supp.' && status='.$status.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }else{
            $filterr = ""; 
        }
        ?>  -->
            </div>                                                            
    </div>
<br/>
</div>
</form> 

<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Acct - Create Memorial Journal'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '54'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>&nbsp';
        }else{
    echo '';
    }




    $querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Acct - Upload Memorial Journal'");
        $rs2 = mysqli_fetch_array($querys2);
        $id2 = isset($rs2['id']) ? $rs2['id'] : 0;

        if($id2 == '55'){
    echo '<button id="btnupload" type="button" class="btn-success btn-xs" style="border-radius: 6%"><span class="fa fa-upload" aria-hidden="true"></span> Upload</button>';
        }else{
    echo '';
    }
?>
    </div>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

            
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">No Journal</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Type</th>
            <th style="text-align: center;vertical-align: middle;">curr</th>
            <th style="text-align: center;vertical-align: middle;">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Description</th>
            <th style="text-align: center;vertical-align: middle;">Action</th>
                                                        
        </tr>
    </thead>
   
    <tbody>
    <?php
    $nama_type ='';
    $Status = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null; 
    $Status = isset($_POST['Status']) ? $_POST['Status']: null; 
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));               
    }
    if(empty($nama_type) and empty($Status) and empty($start_date) and empty($end_date)){
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.mj_date = '$date_now' group by a.no_mj");
    }
    elseif ($nama_type == 'ALL' and $Status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj group by a.no_mj");
    }
    elseif ($nama_type == 'ALL' and $Status == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.mj_date between '$start_date' and '$end_date' group by a.no_mj");
    }
    elseif ($nama_type != 'ALL' and $Status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.id_cmj = '$nama_type' group by a.no_mj");
    }
    elseif ($nama_type != 'ALL' and $Status == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.id_cmj = '$nama_type' and a.mj_date between '$start_date' and '$end_date' group by a.no_mj");
    }
    elseif ($nama_type == 'ALL' and $Status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.status = '$Status' group by a.no_mj");
    }
    elseif ($nama_type == 'ALL' and $Status != 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.status = '$Status' and a.mj_date between '$start_date' and '$end_date' group by a.no_mj");
    }
    elseif ($nama_type != 'ALL' and $Status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.id_cmj = '$nama_type' and a.status = '$Status' group by a.no_mj");
    }
    else{
    $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,a.curr,sum(a.debit) debit,sum(a.credit) credit,a.keterangan,a.status from tbl_memorial_journal a left join master_category_mj b on b.id_cmj = a.id_cmj where a.id_cmj = '$nama_type' and a.status = '$Status' and a.mj_date between '$start_date' and '$end_date' group by a.no_mj");
}

    while($row = mysqli_fetch_array($sql)){
          $status = $row['status'];                 
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="" value = "'.$row['no_mj'].'">'.$row['no_mj'].'</td>
            <td style="" value = "'.$row['mj_date'].'">'.date("d-M-Y",strtotime($row['mj_date'])).'</td>
            <td style="" value = "'.$row['nama_cmj'].'">'.$row['nama_cmj'].'</td>
            <td style="" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style=" text-align : center;" value="'.$row['debit'].'">'.number_format($row['debit'],2).'</td>
            <td style=" text-align : center;" value="'.$row['credit'].'">'.number_format($row['credit'],2).'</td>
            <td style="" value = "'.$row['status'].'">'.$row['status'].'</td>
            <td style="" value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td>';

            $querys = mysqli_query($conn1,"select Groupp, finance, ap_apprv_lp from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $fin = $rs['finance'];
            $app = $rs['ap_apprv_lp'];

            echo '<td >';
            if($status == 'Draft' and $fin == '1'){
                echo '<a id="approve" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i class="fa fa-paper-plane"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;" onclick="alert_approve();"> Post</i></button></a>
                <a href="edit-memorial-journal.php?no_mj='.base64_encode($row['no_mj']).' "><button style="border-radius: 6px" type="button" class="btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Edit</i></button></a>                
                <a id="delete" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-trash"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;" onclick="alert_cancel();"> Cancel</i></button></a>';
            }elseif($status == 'Post' and $fin == '1'and $app != '1'){
                echo '<p style="font-size: 13px;margin-bottom: -1px"><i class="fa fa-paper-plane" style="padding-right: 3px; padding-left: 5px; color: green" ></i><b>Post</b></p>';
            }elseif($status == 'Post' and $fin == '1' and $app == '1'){
                echo '<a id="delete" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-trash"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;" onclick="alert_cancel();"> Cancel</i></button></a>
                ';
            }elseif($status == 'Cancel' and $fin == '1') {
                echo ' <p style="font-size: 13px;margin-bottom: -1px"><i class="fa fa-ban fa-lg" style="padding-right: 3px; padding-left: 5px; color: red" ></i><b>Canceled</b></p>';                    
            }else{
               echo '';                    
            }                                        
            echo '</td>';
            echo '</tr>';
}?>
</tbody>                    
</table>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:450px;" class="modal-dialog modal-md">
        <div style="height: 225px" class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading" style="text-align: center;"><b>UPLOAD</b></h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form method="post" enctype="multipart/form-data" action="proses_upload.php">
                                    Pilih File:
                                    <input class="form-control" name="fileexcel" type="file" required="required">
                                    <br>
                                    <button class="btn btn-sm btn-info" type="submit">Submit</button>
                                    <a target="_blank" href="format_upload_mj.xls"><button type="button" class="btn btn-warning "><i class="fa fa-file-excel-o" aria-hidden="true"> Format Upload</i></button></a>
                                </form>
        </div>
      </div>
    </div>
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
<!--           <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
  <!--         <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                    
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
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
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
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#approve", function(){                 
        var no_mj = $(this).closest('tr').find('td:eq(0)').attr('value');
        var post_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'post_memorialjournal.php',
            data: {'no_mj':no_mj, 'post_user':post_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                alert(data);
                window.location.reload();
                // alert(data);                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_mj = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';
        $.ajax({
            type:'POST',
            url:'cancel_memorialjournal.php',
            data: {'no_mj':no_mj, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();
                // alert(data);
                                                                            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
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
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_mj = $(this).closest('tr').find('td:eq(0)').attr('value');
    var date = $(this).closest('tr').find('td:eq(1)').text();
    var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
    var status = $(this).closest('tr').find('td:eq(6)').attr('value');


    $.ajax({
    type : 'post',
    url : 'ajax_mj.php',
    data : {'no_mj': no_mj},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_mj);
    $('#txt_tglbpb').html('Date : ' + date + '');
    $('#txt_no_po').html('Type : ' + reff + '');
    $('#txt_supp').html('Status : ' + status + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    // $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-memorial-journal.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnupload').onclick = function () {
    location.href = "upload-memorial-journal.php";
};
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "memorial-journal.php";
};
</script>

<!-- <script type="text/javascript">     
    document.getElementById('btnupload').onclick = function (){ 
    // var txt_type = $(this).closest('tr').find('td:eq(4)').attr('value'); 
    // var txt_id = $(this).closest('tr').find('td:eq(0)').attr('value');           
    $('#mymodal2').modal('show');
    // $('#txt_type').val(txt_type);
    // $('#txt_id').val(txt_id);

};

</script> -->

<script>
function alert_cancel() {
  alert("Memorial Journal Cancel successfully");
  location.reload();
}
function alert_approve() {
  alert("Memorial Journal Post successfully");
  location.reload();
}
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>