<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM TRANSFER REQUEST (CBD)</h2>
<div class="box">
    <div class="box header">


        <form id="form-data" action="ftrcbd.php" method="post">        
        <div class="form-row">
            <div class="col-md-12">
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

            <div class="col-md-5">
            <label for="status"><b>Status</b></label>            
              <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>
                <option value="draft" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'draft'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Draft</option>
                <option value="Approved" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Approved'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Approved</option>
                <option value="Cancel" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Cancel'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Cancel</option>                                                                                                             
                </select>
                </div>
        <div class="form-row">
            <div class="col-md-6"> 
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
               echo '';
            } ?>" 
            placeholder="Start Date" autocomplete='off'>
            </div>

            <div class="col-md-6 mb-1">
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
            placeholder="Tanggal Akhir">
            </div>
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
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 5px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button>
            </div>                                                            
    </div>
</div>
</form>        
<?php
         $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create FTR'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '5'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Create</button>';
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
            <th style="text-align: center;vertical-align: middle;">No FTR CBD</th>
            <th style="text-align: center;vertical-align: middle;width: 100px">FTR CBD Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>            
            <th style="text-align: center;vertical-align: middle;">No PO</th>
            <th style="text-align: center;vertical-align: middle;">SubTotal</th>
            <th style="text-align: center;vertical-align: middle;">Tax</th>           
            <th style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: center;vertical-align: middle;">Currency</th>
            <th style="text-align: center;vertical-align: middle;">Create By</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Keterangan</th>                                                           
            <th style="text-align: center;vertical-align: middle;width: 170px">Action</th>

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
    if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd where tgl_ftr_cbd = '$date_now' group by no_ftr_cbd");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd group by no_ftr_cbd");
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd where tgl_ftr_cbd between '$start_date' and '$end_date' group by no_ftr_cbd");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd where supp = '$nama_supp' group by no_ftr_cbd");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd where supp = '$nama_supp' and tgl_ftr_cbd between '$start_date' and '$end_date' group by no_ftr_cbd");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd where status = '$status' group by no_ftr_cbd");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd where status = '$status' and tgl_ftr_cbd between '$start_date' and '$end_date' group by no_ftr_cbd");
    }
    elseif ($nama_supp != 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal) as subtotal, SUM(tax) as tax, SUM(total) as total, curr, create_user, status, keterangan from ftr_cbd where status = '$status' and supp = '$nama_supp' group by no_ftr_cbd");
    }
    else{
    $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, supp, SUM(subtotal + biaya_tambahan) as subtotal, SUM(tax) as tax, SUM(total + biaya_tambahan) as total, curr, create_user, status, keterangan from ftr_cbd where status = '$status' and supp = '$nama_supp' and tgl_ftr_cbd between '$start_date' and '$end_date' group by no_ftr_cbd");
    }

    while($row = mysqli_fetch_array($sql)){
    if (!empty($row)) {
    $status = $row['status'];         
        echo '<tr style="font-size: 12px;text-align: center;">
            <td style="width: 100px;" value = "'.$row['no_ftr_cbd'].'">'.$row['no_ftr_cbd'].'</td>
            <td style="width: 100px;" value = "'.$row['tgl_ftr_cbd'].'">'.date("d-M-Y",strtotime($row['tgl_ftr_cbd'])).'</td>
            <td style="width: 250px;"value = "'.$row['supp'].'">'.$row['supp'].'</td>
            <td value = "'.$row['no_po'].'">'.$row['no_po'].'</td>            
            <td style="text-align: right;" value = "'.$row['subtotal'].'">'.number_format($row['subtotal'],2).'</td>
            <td style="text-align: right;" value = "'.$row['tax'].'">'.number_format($row['tax'],2).'</td>            
            <td style="text-align: right;" value = "'.$row['total'].'">'.number_format($row['total'],2).'</td>
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td value = "'.$row['create_user'].'">'.$row['create_user'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
            <td style = "display: none;" value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td>';

            $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $pur = $rs['purchasing'];
            $app_po = $rs['approve_po'];

            echo '<td width="150px;">';
            if($status == 'Approved' and $group != 'STAFF' and $pur == '1' ){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
                <a style="margin-right: 5=px" href="pdf_ftrcbd.php?noftrcbd='.$row['no_ftr_cbd'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'Approved' and $group == 'STAFF' and $pur == '1'){
                echo '<a id="approve" href=""><i class="fa fa-paper-plane" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>                
                <a id="delete" href=""><i class="fa fa-trash" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>
                <a style="margin-right: 5=px" href="pdf_ftrcbd.php?noftrcbd='.$row['no_ftr_cbd'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'draft' and $group != 'STAFF' and $pur == '1'){
                echo '<a id="approve" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i style="color: white" class="fa fa-paper-plane fa-lg" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Approve</i></button</a>                
                <a  style="margin-right: 5px" id="delete" href=""><button style="border-radius: 6px"  type="button" class="btn-xs btn-danger"><i style="color: white" class="fa fa-trash fa-lg" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Cancel</i></button</a>
                <a href="pdf_ftrcbd.php?noftrcbd='.$row['no_ftr_cbd'].'" target="_blank"><i class="fa fa-print" style="padding-right: 10px; padding-left: 5px;" hidden></i></a>';                
            }elseif($status == 'draft' and $group == 'STAFF' and $pur == '1') {
                echo ' <a style="margin-right: 5=px" href="pdf_ftrcbd.php?noftrcbd='.$row['no_ftr_cbd'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
            }elseif($status == 'Cancel' and $group != 'STAFF' and $pur == '1' ) {
                echo ' <p style="font-size: 13px;margin-bottom: -1px"><i class="fa fa-ban fa-lg" style="padding-right: 3px; padding-left: 5px; color: red" ></i><b>Canceled</b></p>';
            }elseif($status == 'Cancel' and $group == 'STAFF' and $pur == '1') {
                echo ' <p style="font-size: 13px;margin-bottom: -1px"><i class="fa fa-ban fa-lg" style="padding-right: 3px; padding-left: 5px; color: red" ></i><b>Canceled</b></p>';
            }                 
            echo '</td>';
            echo '</tr>';
        }
}?>
</tbody>                    
</table>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodalftrcbd" data-target="#mymodalftrcbd" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_cbd"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_cbd" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>       
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_keterangan" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                                               
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
        var noftrcbd = $(this).closest('tr').find('td:eq(0)').attr('value');
        var confirm_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approveftrcbd.php',
            data: {'noftrcbd':noftrcbd, 'confirm_user':confirm_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){
                console.log(data);
                alert('Data Berhasil Di Approve');
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
        var noftrcbd = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancelftrcbd.php',
            data: {'noftrcbd':noftrcbd, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                alert("Data Berhasil di Cancel");
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
    $('#mymodalftrcbd').modal('show');
    var noftrcbd = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_cbd = $(this).closest('tr').find('td:eq(1)').text();
    var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
    var status = $(this).closest('tr').find('td:eq(9)').attr('value');
    var keterangan = $(this).closest('tr').find('td:eq(10)').attr('value');                    

    $.ajax({
    type : 'post',
    url : 'ajaxcbd.php',
    data : {'noftrcbd': noftrcbd},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_cbd').html(noftrcbd);
    $('#txt_tgl_cbd').html('Tgl FTR CBD : ' + tgl_cbd + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_keterangan').html('Keterangan : ' + keterangan + '');                                        
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "formftrcbd.php";
};
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "ftrcbd.php";
};
</script>

<!--
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
-->

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
