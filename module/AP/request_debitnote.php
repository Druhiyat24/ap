<?php include '../header.php';
// $blnfrom=isset($_GET['blnfrom']) ? $_GET['blnfrom'] : (date("m") -1);
// $thnfrom=isset($_GET['thnfrom']) ? $_GET['thnfrom'] : date("y");
// $blnto=isset($_GET['blnto']) ? $_GET['blnto'] : date("m");
// $thnto=isset($_GET['thnto']) ? $_GET['thnto'] : date("y");
// $namabank=isset($_GET['namabank']) ? $_GET['namabank'] : 'ALL';
// $status_fil=isset($_GET['status']) ? $_GET['status'] : 'ALL';

$nama_supp = isset($_GET['nama_supp']) ? $_GET['nama_supp']: 'ALL';
$status = isset($_GET['status']) ? $_GET['status']: 'ALL';
$startdate = isset($_GET['start_date']) ? $_GET['start_date'] : date("Y-m-d");
$enddate = isset($_GET['end_date']) ? $_GET['end_date'] : date("Y-m-d");  
$start_date = date("Y-m-d",strtotime($startdate));
$end_date = date("Y-m-d",strtotime($enddate));

?>

<style type="text/css">
    input[type=file]::file-selector-button {
      margin-right: 20px;
      border: none;
      background: #084cdf;
      padding: 10px 20px;
      border-radius: 10px;
      color: #fff;
      cursor: pointer;
      transition: background .2s ease-in-out;
  }

  input[type=file]::file-selector-button:hover {
      background: #0d45a5;
  }
</style>
<!-- MAIN -->
<div class="col p-4">
    <h3 class="text-center">LIST REQUEST DEBIT NOTE</h3>
    <div class="box">
        <div class="box header">
            <form id="form-data" action="request_debitnote.php" method="post">
                <div class="form-row">
                    <div class="col-md-3">
                        <label for="nama_supp"><b>Supplier</b></label>            
                        <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" selected="true">ALL</option>                                                
                            <?php
                            $nama_supp ='';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                            }                 
                            $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['Supplier'];
                                if($row['Supplier'] == $_POST['nama_supp']){
                                    $isSelected = ' selected="selected"';
                                }else{
                                    $isSelected = '';
                                }
                                echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            }?>
                        </select>

                    </div>
                    <div class="col-md-2">
                        <label for="status"><b>Status</b></label>            
                        <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" selected="true">ALL</option>                                                
                            <?php
                            $status ='';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $status = isset($_POST['status']) ? $_POST['status']: null;
                            }                 
                            $sql = mysqli_query($conn1,"select distinct(nama_status) nama_status from ir_status where status = 'dn' order by id ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['nama_status'];
                                if($row['nama_status'] == $_POST['status']){
                                    $isSelected = ' selected="selected"';
                                }else{
                                    $isSelected = '';
                                }
                                echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            }?>                                                                                                             
                        </select>
                    </div>

                    <div class="col-md-2">         
                      <label for="start_date"><b>From</b></label>
                      <input type="text" class="form-control tanggal" id="start_date" name="start_date" 
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
                   placeholder="Start Date" autocomplete='off' style="height: 32px;font-size:13px;">   
               </div>

               <div class="col-md-2">
                 <label for="end_date"><b>To</b></label>        
                 <input type="text" class="form-control tanggal" id="end_date" name="end_date" 
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
               placeholder="Tanggal Akhir" style="height: 32px;font-size:13px;"> 
           </div>

           <div class="input-group-append col">                                   
            <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
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

            <?php
            $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
            $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;

            echo '<a target="_blank" href="ekspor_reqdn.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success" style= "margin-top: 30px;margin-bottom:5px"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
            ?>
        </div>                                                            
    </div>
    <br>

</div>
</form> 

<?php
$querys1 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Request Debitnote'");
$rs1 = mysqli_fetch_array($querys1);
$id1 = isset($rs1['id']) ? $rs1['id'] : 0;

if($id1 == '79'){
    echo '<button id="btncreatenew" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Create</button>';
}
        //<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Create</button>


?>

</div>
<div class="box body">
    <div class="row">       
        <div class="col-md-12">

            <div class="table-responsive">
                <form id="formdata2">    
                    <table id="datatable" class="table table-striped table-bordered text-nowrap" role="grid" cellspacing="0" width="100%">
                        <thead>
                            <tr class="thead-dark">
                                <th style="text-align: center;vertical-align: middle;width: 10%;">No Request</th>
                                <th style="text-align: center;vertical-align: middle;width: 10%;">Request Date</th>
                                <th style="text-align: center;vertical-align: middle;width: 10%;">Supplier</th> 
                                <th style="text-align: center;vertical-align: middle;width: 10%;">Total Amount</th>
                                <th style="text-align: center;vertical-align: middle;width: 10%;">Status</th>
                                <th style="text-align: center;vertical-align: middle;width: 10%;">No DN</th>
                                <th style="text-align: center;vertical-align: middle;width: 10%;">Create User</th>
                                <th style="text-align: center;vertical-align: middle;width: 10%;">Action</th>
                                <th style="display: none;">Pdf Rekening Koran</th>
                                <th style="display: none;">Pdf Rekening Koran</th>                                         
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $nama_supp ='';
                            $status = '';
                            $start_date ='';
                            $end_date ='';
                            $date_now = date("Y-m-d");                
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                                $status = isset($_POST['status']) ? $_POST['status']: null;
                                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                                $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
                            }

                            if ($nama_supp == 'ALL' and $status == 'ALL') {            
                             $where = "where tgl_req between '$start_date' and '$end_date'";
                         }
                         elseif ($nama_supp != 'ALL' and $status == 'ALL') {
                             $where = "where nama_supp = '$nama_supp' and tgl_req between '$start_date' and '$end_date'";
                         }
                         elseif ($nama_supp == 'ALL' and $status != 'ALL') {
                             $where = "where a.status = '$status' and tgl_req between '$start_date' and '$end_date'";
                         }
                         else{
                            $where = "where nama_supp = '$nama_supp' and a.status = '$status' and tgl_req between '$start_date' and '$end_date'";
                        }

                        $sql = mysqli_query($conn2,"select a.no_req,tgl_req,nama_supp,total_amount,deskripsi,a.status,CONCAT(a.created_by,' (',a.created_date,')') create_user,if(no_dn is null,'-',no_dn) no_dn,if(file_name is null,'-',file_name) file_name, file_name_as from req_dn_h a left join (select * from req_dn_dok where status is null GROUP BY no_req) b on b.no_req = a.no_req $where GROUP BY a.id");


                        while($row = mysqli_fetch_array($sql)){
                            if (!empty($row)) {
                                $file_name = $row['file_name'];        
                                $status = $row['status'];         
                                echo '<tr style="font-size: 12px;text-align:center;">
                                <td style="" value = "'.$row['no_req'].'">'.$row['no_req'].'</td>
                                <td style="" value = "'.$row['tgl_req'].'">'.date("d-M-Y",strtotime($row['tgl_req'])).'</td>
                                <td style=""value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                                <td style="text-align:right;" value = "'.$row['total_amount'].'">'.number_format($row['total_amount'],2).'</td>
                                <td style="" value = "'.$row['status'].'">'.$row['status'].'</td> 
                                <td style="" value = "'.$row['no_dn'].'">'.$row['no_dn'].'</td> 
                                <td style="" value = "'.$row['create_user'].'">'.$row['create_user'].'</td>';
                                echo '<td>';
                                if ($status == 'Cancel') {
                                   echo '-';
                               }elseif($status == 'Post'){
                                if ($file_name == '-') {
                                    echo '<button style="border-radius: 6px" type="button" class="btn-xs btn-warning" id="btnupdate" name="btnupdate"><i class="fa fa-cloud-upload" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button>
                                    <a href="pdf_req_dn.php?no_req='.$row['no_req'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>
                                    <a  style="margin-right: 5px" id="delete" href=""><button style="border-radius: 6px"  type="button" class="btn-xs btn-danger"><i style="color: white" class="fa fa-trash fa-lg" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Cancel</i></button</a>';
                                }else{
                                    echo'<a href="file_pdf/'.$row['file_name'].'" target="_blank" style="padding-left:2px;"><button style="border-radius: 6px" type="button" class="btn-xs btn-warning"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button></a>
                                    <a style="padding-right:2px;" id="delete_pdf" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-ban" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button></a>
                                    <button style="border-radius: 6px;padding-left:2px;" type="button" class="btn-xs btn-info" id="btnshow" name="btnshow"><i class="fa fa-eye" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button>
                                    <a href="pdf_req_dn.php?no_req='.$row['no_req'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>
                                    <a  style="margin-right: 5px" id="delete" href=""><button style="border-radius: 6px"  type="button" class="btn-xs btn-danger"><i style="color: white" class="fa fa-trash fa-lg" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Cancel</i></button</a>';
                                }

                            }else{
                                if ($file_name == '-') {
                                    echo '<button style="border-radius: 6px" type="button" class="btn-xs btn-warning" id="btnupdate" name="btnupdate"><i class="fa fa-cloud-upload" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button>
                                    <a href="pdf_req_dn.php?no_req='.$row['no_req'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
                                }else{
                                    echo'<a href="file_pdf/'.$row['file_name'].'" target="_blank" style="padding-left:2px;"><button style="border-radius: 6px" type="button" class="btn-xs btn-warning"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button></a>
                                    <a style="padding-right:2px;" id="delete_pdf" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-ban" aria-hidden="true" style="padding-right: 5px; padding-left: 5px;"></i></button></a>
                                    <button style="border-radius: 6px;padding-left:2px;" type="button" class="btn-xs btn-info" id="btnshow" name="btnshow"><i class="fa fa-eye" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"></i></button>
                                    <a href="pdf_req_dn.php?no_req='.$row['no_req'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
                                }
                                
                            }

                            echo '</td>
                            <td style="display: none;" value = "'.$row['file_name'].'">'.$row['file_name'].'</td>
                            <td style="display: none;" value = "'.$row['file_name_as'].'">'.$row['file_name_as'].'</td>';
                            echo '</tr>';
                        }
                    }?>
                </tbody>                    
            </table>
        </form>
    </div>

</div>
</div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="Heading">Upload Document Request Debitnote</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <form id="modal-form2" Method="Post" Action="insert_doc_reqdn.Php" Enctype="Multipart/Form-Data">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="nama_supp"><b>No Request</b></label> 
                                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_no_req" id="txt_no_req" value="">
                                <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_nama_supp" id="txt_nama_supp" value="">
                                <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_user" id="txt_user" value="<?php echo $user; ?>">
                                <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_status" id="txt_status" value="">
                                <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_start_date" id="txt_start_date" value="">
                                <input type="hidden" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_end_date" id="txt_end_date" value="">
                            </div>
        <!-- <div class="col-md-6 mb-3"> 
                <label for="nama_supp"><b>Bank Account</b></label> 
                <input type="text" readonly style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_akun" id="txt_akun" value="">
            </div> -->
        </div>
        <div class="form-row">
         <div class="col-md-12 mb-3"> 
            <label for="nama_supp"><b>Upload File</b></label> 
            <Input Type="File" class="form-control" id="txtfile" Name="txtfile" Accept="Application/Pdf">
        </div>
    </br>
    <div class="col-md-9">
    </div>
    <div class="col-md-3">
        <div class="modal-footer">
            <button type="submit" id="send2" name="send2" class="btn btn-success btn-lg" style="width: 100%;"><span class="fa fa-floppy-o"></span>
                Save
            </button>
        </div>
    </div>
</div>           
</form>
</div>
</div>
</div>
</div>
</div>
</div> 

<div class="form-row">
    <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="Heading">Document Request Debitnote</h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <div id="labelfile" name="labelfile"></div>
                    <div id="fileshow" name="fileshow"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<div class="modal fade" id="mymodalftrdp" data-target="#mymodalftrdp" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h5 class="modal-title" id="txt_dp"></h5>
            </div>
            <div class="container" style="overflow-x: auto;">
                <div class="row">
                  <div id="txt_tgl_dp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> 
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
              </div>
          </div>
      </div> 
      <!-- /.modal-content --> 
      <!--  </div> -->
      <!-- /.modal-dialog --> 
      <!--    </div> -->        

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
        // $('#mytdmodal').dataTable();

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
    $(function () {
        $("#formdata2").on("click", "#btnshow", function(){ 
            var namafile = $(this).closest('tr').find('td:eq(8)').attr('value'); 
            var labelfile = $(this).closest('tr').find('td:eq(9)').attr('value'); 
            var fileName = '<embed src="file_pdf/'+ namafile + '" type="application/pdf" toolbar="0" frameborder="0" width="100%" height="400px">';
         // var fileName = '<embed src="file_pdf/'+ namafile + '#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" toolbar="0" frameborder="0" width="100%" height="400px">';
            var label = '<label for="labelfile"><b>'+ labelfile + '</b></label>';
        // alert(fileName);
            $('#labelfile').html(label);
            $('#fileshow').html(fileName); 
            $('#mymodal3').modal('show');

        });
    });

</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#approve", function(){                 
        var noftrdp = $(this).closest('tr').find('td:eq(0)').attr('value');
        var confirm_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approveftrdp.php',
            data: {'noftrdp':noftrdp, 'confirm_user':confirm_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_req = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancel_req_dn.php',
            data: {'no_req':no_req, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                alert("Data Berhasil dicancel")
                window.location.reload();                                                            
            },
            error:  function (xhr, exc, ajaxOptions, thrownError) {
               alert(xhr.status);
               alert(exc);               
           }
       });
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#delete_pdf", function(){                 
        var no_req = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancel_req_dn_dok.php',
            data: {'no_req':no_req, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                alert("Dokumen Dihapus")
                window.location.reload();                                                            
            },
            error:  function (xhr, exc, ajaxOptions, thrownError) {
               alert(xhr.status);
               alert(exc);               
           }
       });
    });
</script>


<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
        $('#mymodalftrdp').modal('show');
        var no_req = $(this).closest('tr').find('td:eq(0)').attr('value'); 
        var tgl_doc = $(this).closest('tr').find('td:eq(1)').attr('value'); 
        var supp = $(this).closest('tr').find('td:eq(2)').attr('value');                  

        $.ajax({
            type : 'post',
            url : 'ajax_reqdn.php',
            data : {'no_req': no_req},
            success : function(data){
            $('#details').html(data); //menampilkan data ke dalam modal

    //         // Hapus DataTable jika sudah ada, lalu inisialisasi ulang
    //         if ($.fn.DataTable.isDataTable('#mytdmodal')) {
    //             $('#mytdmodal').DataTable().destroy();
    //         }

    //         $('#mytdmodal').DataTable({
    //     "autoWidth": true,  // Gunakan lebar sesuai konten
    //     "scrollX": true      // Aktifkan scroll horizontal jika lebar kurang
    // });

        }
    });         
        //make your ajax call populate items or what even you need
        $('#txt_dp').html(no_req);
        $('#txt_tgl_dp').html('Request Date : ' + tgl_doc + '');
        $('#txt_nama_supp').html('Supplier : ' + supp + '');                                    
    });

</script>

<script type="text/javascript">     
    $("#formdata2").on("click", "#btnupdate", function(){ 
        var no_req = $(this).closest('tr').find('td:eq(0)').attr('value');
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val(); 
        var status = $('select[name=status] option').filter(':selected').val(); 
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;

        $('#mymodal2').modal('show');
        $('#txt_no_req').val(no_req);
        $('#txt_nama_supp').val(nama_supp);
        $('#txt_status').val(status);
        $('#txt_start_date').val(start_date);
        $('#txt_end_date').val(end_date);

    });

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create_request_debitnote.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('btncreatenew').onclick = function () {
        location.href = "create_request_dn.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "request_debitnote.php";
    };
</script>


<script>
    function alert_cancel() {
      alert("Data Berhasil di Cancel");
      location.reload();
  }
  function alert_approve() {
      alert("Data Berhasil di Approve");
      location.reload();
  }
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
