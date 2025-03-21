<?php include '../header.php' ?>

<!-- MAIN -->
<div class="col p-4">
    <h2 class="text-center">FORM ADJUSMENT SUBCONTRACTOR</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="adjust-subcont.php" method="post">        
                <div class="form-row">
                   <div class="col-md-3">
                    <label for="nama_supp"><b>Supplier</b></label>            
                    <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                        <option value="ALL" <?php
                        $nama_supp = '';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $status = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                        }                 
                        if($nama_supp == 'ALL'){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';
                        }
                        echo $isSelected;
                        ?>                
                        >ALL</option>                                 
                        <?php
                        $nama_supp ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                        }                 
                        $sql = mysql_query("SELECT DISTINCT id_supplier, Supplier AS nama_supp FROM mastersupplier WHERE tipe_sup = 'S' ORDER BY Supplier ASC",$conn1);
                        while ($row = mysql_fetch_array($sql)) {
                            $data = $row['nama_supp'];
                            if($row['nama_supp'] == $_POST['nama_supp']){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                        }?>
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
            <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 15px;margin-right: 15px;border: 0;
            line-height: 1;
            padding: -2px 8px;
            font-size: 1rem;
            text-align: center;
            color: #fff;
            text-shadow: 1px 1px 1px #000;
            border-radius: 6px;
            background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 15px;margin-right: 15px;border: 0;
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
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Cash'");
$rs = mysqli_fetch_array($querys);
$id = isset($rs['id']) ? $rs['id'] : 0;

if($id == '39'){
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
                        <th style="text-align: center;vertical-align: middle;">No Document</th>
                        <th style="text-align: center;vertical-align: middle;">Date Periode</th>
                        <th style="text-align: center;vertical-align: middle;">Supplier</th>
                        <th style="text-align: center;vertical-align: middle;">No PO</th>
                        <th style="text-align: center;vertical-align: middle;">No WS</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Create by</th>
                        <th style="text-align: center;vertical-align: middle;">Action</th>

                    </tr>
                </thead>

                <tbody>
                    <?php
                    $nama_supp ='';
                    $start_date ='';
                    $end_date ='';
                    $date_now = date("Y-m-d");                
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
                        $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                        $end_date = date("Y-m-d",strtotime($_POST['end_date']));               
                    }
                    if($nama_supp == 'ALL'){
                     $where = "where tgl_periode between '$start_date' and '$end_date'";
                 }else{
                     $where = "where supplier = '$nama_supp' and tgl_periode between '$start_date' and '$end_date'";  
                 }

                 $sql = mysql_query("SELECT no_dok, tgl_periode, supplier, no_po, no_ws, ROUND(SUM(qty * price), 2) total, create_by, status FROM ca_adjust_input $where GROUP BY no_dok",$conn1);

                 while($row = mysqli_fetch_array($sql)){

                    echo '<tr style="font-size:12px;text-align:center;">
                    <td style="width: 150px;" value = "'.$row['no_dok'].'">'.$row['no_dok'].'</td>
                    <td style="width: 100px;" value = "'.$row['tgl_periode'].'">'.date("d-M-Y",strtotime($row['tgl_periode'])).'</td>
                    <td style="width: 150px;" value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
                    <td style="width: 150px;" value = "'.$row['no_po'].'">'.$row['no_po'].'</td>
                    <td style="width: 150px;" value = "'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                    <td style="width:50px; text-align : center;" value="'.$row['total'].'">'.number_format($row['total'],2).'</td><td style="width: 150px;" value = "'.$row['create_by'].'">'.$row['create_by'].'</td>';
                    if ($row['status'] == 'N') {
                        echo '<td style="text-align: center;"><p style="font-size: 13px;margin-bottom: -1px"><i class="fa fa-ban fa-lg" style="padding-right: 3px; padding-left: 5px; color: red" ></i><b>Canceled</b></p></td>';
                    }else{
                        echo '<td style="width:50px;text-align : center" ><a id="delete" href=""><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-trash" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;" onclick="alert_cancel();"> Cancel</i></button></a></td>';
                    }
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

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <!--  <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
                  <!--  <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
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
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_dok = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_by = '<?php echo $user ?>';        

        $.ajax({
            type:'POST',
            url:'calcel_adj_subcont.php',
            data: {'no_dok':no_dok, 'cancel_by':cancel_by},
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
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
        $('#mymodal').modal('show');
        var no_dok = $(this).closest('tr').find('td:eq(0)').attr('value');
        var date = $(this).closest('tr').find('td:eq(1)').text();
        var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
        var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
        var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
        var curr = "IDR";

        $.ajax({
            type : 'post',
            url : 'ajax_adj_subcont.php',
            data : {'no_dok': no_dok},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_dok);
        $('#txt_tglbpb').html('Periode : ' + date + '');
        // $('#txt_no_po').html('Refference : ' + reff + '');
        // $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-adjust-subcont.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "adjust-subcont.php";
    };
</script>

<script>
    function alert_cancel() {
      alert("Data Berhasil di Cancel");
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