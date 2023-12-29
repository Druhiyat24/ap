<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">MASTER BANK</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="master-bank.php" method="post">        
        <div class="form-row">
            <div class="col-md-4">
            <label for="nama_supp"><b>Bank</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(bank_name) as nama_bank from b_masterbank  order by bank_name ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['nama_bank'];
                    if($row['nama_bank'] == $_POST['nama_supp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>

            <div class="col-md-3">
            <label for="status"><b>Currency</b></label>            
              <select class="form-control selectpicker" name="curren" id="curren" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['curren']) ? $_POST['curren']: null;
                }                 
                    if($status == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>
                <option value="IDR" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['curren']) ? $_POST['curren']: null;
                }                 
                    if($status == 'IDR'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >IDR</option>
                <option value="USD" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['USD']) ? $_POST['curren']: null;
                }                 
                    if($status == 'Approved'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >USD</option>                                                                                                             
                </select>
                </div>

                <div class="col-md-3">
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
                <option value="Active" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Active'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Active</option>
                <option value="Deactive" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Deactive'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Deactive</option>                                                                                                             
                </select>
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
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
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
            <th style="text-align: center;vertical-align: middle;">Doc Number</th>
            <th style="text-align: center;vertical-align: middle;">Bank</th>
            <th style="text-align: center;vertical-align: middle;">Account</th>
            <th style="text-align: center;vertical-align: middle;">Active Date</th>
            <th style="text-align: center;vertical-align: middle;">Deactive Date</th>
            <th style="text-align: center;vertical-align: middle;">Curreny</th>
            <th style="text-align: center;vertical-align: middle;">Descriptions</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">Action</th>
                                                        
        </tr>
    </thead>
   
    <tbody>
    <?php
    $nama_supp ='';
    $status = '';
    $curren = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $status = isset($_POST['status']) ? $_POST['status']: null; 
    $curren = isset($_POST['curren']) ? $_POST['curren']: null;               
    }
    if($nama_supp == 'ALL' and $status == 'ALL' and $curren == 'ALL'){
     $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank order by id asc");
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $curren == 'ALL') {            
     $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank where bank_name = '$nama_supp'order by id asc");
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $curren == 'ALL') {
     $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank where status = '$status'order by id asc");
    }elseif ($nama_supp == 'ALL' and $status == 'ALL' and $curren != 'ALL') {
     $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank where curr = '$curren'order by id asc");
    }elseif ($nama_supp != 'ALL' and $status != 'ALL' and $curren == 'ALL') {
     $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank where bank_name = '$nama_supp' and status = '$status'order by id asc");
    }elseif ($nama_supp != 'ALL' and $status == 'ALL' and $curren != 'ALL') {
     $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank where bank_name = '$nama_supp' and curr = '$curren'order by id asc");
    }elseif ($nama_supp == 'ALL' and $status != 'ALL' and $curren != 'ALL') {
     $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank where status = '$status' and curr = '$curren' order by id asc");
    }else{
    $sql = mysqli_query($conn2,"select DATE_FORMAT(date_active, '%Y-%m-%d') as active_date,DATE_FORMAT(non_active_date, '%Y-%m-%d') as nonactive_date,UPPER(doc_number) as doc_number,UPPER(sob) as sob,bank_account as account, bank_name as bank,curr, deskripsi, status, create_by, create_date FROM b_masterbank where bank_name = '$nama_supp' and status = '$status' and curr = '$curren'order by id asc");
}
    while($row = mysqli_fetch_array($sql)){
    if (!empty($row)) {
    $status = $row['status']; 
    $nonactivedate = $row['nonactive_date'];
            if ($nonactivedate == '' || $nonactivedate == '1970-01-01') { 
             $nonactive_date = '-';
            }else{
                $nonactive_date = date("d-M-Y",strtotime($row['nonactive_date'])); 
            }         
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="width: 150px;" value = "'.$row['doc_number'].'">'.$row['doc_number'].'</td>
            <td style="width: 150px;" value = "'.$row['bank'].'">'.$row['bank'].'</td>
            <td value = "'.$row['account'].'">'.$row['account'].'</td>
            <td style="width: 100px;" value = "'.$row['active_date'].'">'.date("d-M-Y",strtotime($row['active_date'])).'</td>
            <td style="width: 120px;" value = "'.$nonactive_date.'">'.$nonactive_date.'</td>
            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td  value = "'.$row['deskripsi'].'">'.$row['deskripsi'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>';

            $querys = mysqli_query($conn1,"select Groupp, finance, ap_apprv_lp from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $fin = $rs['finance'];
            $app = $rs['ap_apprv_lp'];

            echo '<td width="100px;">';
            if($status == 'Deactive' and $group != 'STAFF' and $fin == '1'){
                echo '<a id="active" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i onclick="alert_approve();"> Active</i></button></a>';
            }elseif($status == 'Deactive' and $fin == '1'){
                echo '-';
            }elseif($status == 'Active' and $group != 'STAFF' and $fin == '1'){
                echo '<a id="deactive" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i onclick="alert_cancel();"> Deactive</i></button></a>';                
            }elseif($status == 'Active' and $fin == '1') {
                echo '-';                
            } else {
                echo '';                
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

<div class="modal fade" id="mymodallistpayment" data-target="#mymodallistpayment" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_list_payment"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_list_payment" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_keterangan" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                                    
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


<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodallistpayment').modal('show');
    var no_payment = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_list_payment = $(this).closest('tr').find('td:eq(1)').text();
    var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(10)').text();
    var curr = $(this).closest('tr').find('td:eq(6)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(7)').attr('value');
    var status = $(this).closest('tr').find('td:eq(8)').attr('value');
    var top = $(this).closest('tr').find('td:eq(9)').attr('value');
    var keterangan = $(this).closest('tr').find('td:eq(16)').attr('value');               

    $.ajax({
    type : 'post',
    url : 'ajaxlistpayment.php',
    data : {'no_payment': no_payment},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_list_payment').html(no_payment);
    $('#txt_tgl_list_payment').html('Tgl List Payment : ' + tgl_list_payment + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_keterangan').html('Keterangan : ' + keterangan + '');                                        
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-master-bank.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "master-bank.php";
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