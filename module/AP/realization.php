<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST REALIZATION</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="realization.php" method="post">        
        <div class="form-row">
           <div class="col-md-3">
            <label for="nama_supp"><b>Cost Center</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" >
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['cost_name'];
                     $data2 = $row['code_combine'];
                    if($row['code_combine'] == $_POST['nama_supp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data2.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>  

                <div class="col-md-3 mb-3">
            <label for="Worksheet"><b>Worksheet</b></label>   
            <input type="text" class="form-control" name="reff_doc" id="reff_doc" style="font-size: 12px; text-align: left;" 
            value="<?php
            $reff_doc ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $reff_doc = isset($_POST['reff_doc']) ? $_POST['reff_doc']: null;
            }
            if(!empty($_POST['reff_doc'])) {
               echo $_POST['reff_doc'];
            }
            else{
               echo '';
            } ?>"
             autocomplete="off">         
                </div>

                <div class="col-md-3">
            <label for="doc_Number"><b>Document Number</b></label>            
              <input type="text" class="form-control" name="doc_num" id="doc_num" style="font-size: 12px; text-align: left;" 
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
            placeholder="Tanggal Awal" >
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
            placeholder="Tanggal Awal" >
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
            <th style="text-align: center;vertical-align: middle;">Document Number</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">No Cash Advance</th>
            <th style="text-align: center;vertical-align: middle;">Value</th>
            <th style="text-align: center;vertical-align: middle;">Subtotal</th>
            <th style="text-align: center;vertical-align: middle;">Cash</th>                                                        
        </tr>
    </thead>
   
    <tbody>
    <?php
    $nama_supp ='';
    $doc_num = '';
    $reference = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reference = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $reff_doc = isset($_POST['reff_doc']) ? $_POST['reff_doc']: null; 
    $doc_num = isset($_POST['doc_num']) ? $_POST['doc_num']: null; 
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));               
    }
    if($reference == 'ALL' and empty($reff_doc) and empty($doc_num) and empty($start_date) and empty($end_date)){
     $where = "";
    }elseif($reference == 'ALL' and empty($reff_doc) and empty($doc_num) and !empty($start_date) and !empty($end_date)){
     $where = "where a.tgl_ca between '$start_date' and '$end_date'";
    }elseif($reference != 'ALL' and empty($reff_doc) and empty($doc_num)){
     $where = "where a.no_costcenter = '$reference' and a.tgl_ca between '$start_date' and '$end_date'";
    }elseif($reference == 'ALL' and !empty($reff_doc) and empty($doc_num)){
     $where = "where a.no_ws like '%$reff_doc%' and a.tgl_ca between '$start_date' and '$end_date'";
    }elseif($reference == 'ALL' and empty($reff_doc) and !empty($doc_num)){
     $where = "where a.no_ca like '%$doc_num%' and a.tgl_ca between '$start_date' and '$end_date'";
    }elseif($reference != 'ALL' and !empty($reff_doc) and empty($doc_num)){
     $where = "where a.no_costcenter = '$reference' and a.no_ws like '%$reff_doc%' and a.tgl_ca between '$start_date' and '$end_date'";
    }elseif($reference != 'ALL' and empty($reff_doc) and !empty($doc_num)){
     $where = "where a.no_costcenter = '$reference' and a.no_ca like '%$doc_num%' and a.tgl_ca between '$start_date' and '$end_date'";
    }elseif($reference == 'ALL' and !empty($reff_doc) and !empty($doc_num)){
     $where = "where a.no_ws like '%$reff_doc%' and a.no_ca like '%$doc_num%' and a.tgl_ca between '$start_date' and '$end_date'";
    }else{
     $where = "where a.no_costcenter = '$reference' and a.no_ca like '%$doc_num%' and a.no_ws like '%$reff_doc%' and a.tgl_ca between '$start_date' and '$end_date'";  
    }

    $sql = mysql_query("select a.no_ca, a.tgl_ca,b.cc_name,a.req_by,if(a.buyer = '','-',a.buyer) as buyer,if(a.no_ws = '','-',a.no_ws) as no_ws,a.amount from c_cash_advances a inner join b_master_cc b on b.no_cc = a.no_costcenter $where group by a.no_ca",$conn1);

    while($row = mysqli_fetch_array($sql)){
                   
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="width: 150px;" value = "'.$row['no_ca'].'">'.$row['no_ca'].'</td>
            <td style="width: 100px;" value = "'.$row['tgl_ca'].'">'.date("d-M-Y",strtotime($row['tgl_ca'])).'</td>
            <td style="width: 150px;" value = "'.$row['cc_name'].'">'.$row['cc_name'].'</td>
            <td style="width: 150px;" value = "'.$row['req_by'].'">'.$row['req_by'].'</td>
            <td style="width: 150px;" value = "'.$row['buyer'].'">'.$row['buyer'].'</td>
            <td style="width: 150px;" value = "'.$row['no_ws'].'">'.$row['no_ws'].'</td>
            <td style="width:50px; text-align : center;" value="'.$row['amount'].'">'.number_format($row['amount'],2).'</td>';
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
    location.href = "create-realization.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "realization.php";
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