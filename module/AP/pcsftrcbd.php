<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST PAYABLE CARD STATEMENT FTR CBD</h2>
<div class="box">
    <div class="box header">

        
        <form id="form-data" action="pcsbpb.php" method="post">        
        <div class="form-row">
            <div class="col-md-6">
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

            <!-- <div class="col-md-5">
            <label for="status"><b>Status</b></label>            
              <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" <?php
                // $status = '';
                // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // $status = isset($_POST['status']) ? $_POST['status']: null;
                // }                 
                //     if($status == 'ALL'){
                //         $isSelected = ' selected="selected"';
                //     }else{
                //         $isSelected = '';
                //     }
                //     echo $isSelected;
                // ?>                
                // >ALL</option>
                // <option value="Post" <?php
                // $status = '';
                // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // $status = isset($_POST['status']) ? $_POST['status']: null;
                // }                 
                //     if($status == 'Post'){
                //         $isSelected = ' selected="selected"';
                //     }else{
                //         $isSelected = '';
                //     }
                //     echo $isSelected;
                ?>
                >Post</option>
                <option value="Approved" <?php
                // $status = '';
                // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // $status = isset($_POST['status']) ? $_POST['status']: null;
                // }                 
                //     if($status == 'Approved'){
                //         $isSelected = ' selected="selected"';
                //     }else{
                //         $isSelected = '';
                //     }
                //     echo $isSelected;
                ?>
                >Approved</option>
                <option value="Cancel" <?php
                // $status = '';
                // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // $status = isset($_POST['status']) ? $_POST['status']: null;
                // }                 
                //     if($status == 'Cancel'){
                //         $isSelected = ' selected="selected"';
                //     }else{
                //         $isSelected = '';
                //     }
                //     echo $isSelected;
                ?>
                >Cancel</option>                                                                                                             
                </select>
                </div> -->
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
            placeholder="Tanggal Awal" onchange="this.form.submit()">
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
            placeholder="Tanggal Akhir" onchange="this.form.submit()">
            </div>
        </div>

            <div class="input-group-append col">                                   
            <input type="submit" id="submit" value="Search" style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;">
            <input type="button" id="reset" value="Reset" style="margin-top: 30px; margin-bottom: 5px;">
            </div>                                                            
    </div>
</div>
</form>


<?php
    //     $querys = mysqli_query($conn1,"select Groupp, finance from userpassword where username = '$user'");
    //     $rs = mysqli_fetch_array($querys);
    //     $group = $rs['Groupp'];
    //     $fin = $rs['finance'];

    //     if($fin == '0'){
    // echo '';
    //     }else{
    // echo '<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Create</button>';
    // // echo "<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    // // echo '<button id="btndraft" type="button" class="btn-warning btn-xs" hidden><span class="fa fa-bars"></span> List Draft</button>';
    // }
?>
    </div>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

            
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">No BPB</th>
            <th style="text-align: center;vertical-align: middle;width: 80px">BPB Date</th>
            <th style="text-align: center;vertical-align: middle;">NO PO</th>
            <th style="text-align: center;vertical-align: middle;width: 80px">PO Date</th>
            <th style="text-align: center;vertical-align: middle;width: 80px">END Date</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">Item Descriptions</th>            
            <th style="text-align: center;vertical-align: middle;display: none;">Total</th>
            <th style="text-align: center;vertical-align: middle;">SubTotal</th>
            <th style="text-align: center;vertical-align: middle;">TAX</th>                                    
            <th style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: center;vertical-align: middle;width: 130px;">Action</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                          
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
    // if(empty($nama_supp) and  empty($start_date) and empty($end_date)){
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon where tgl_kbon = '$date_now' group by no_kbon");
    // }
    // elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon where status = 'Approved'  group by no_kbon");
    // }
    // elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon where status = 'Approved' and tgl_kbon between '$start_date' and '$end_date' group by no_kbon");
    // }
    // elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon where status = 'Approved' and nama_supp = '$nama_supp' group by no_kbon");
    // }
    // elseif ($nama_supp != 'ALL' and !empty($start_date) and !empty($end_date)) {
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon where nama_supp = '$nama_supp' and status = 'Approved' and tgl_kbon between '$start_date' and '$end_date' group by no_kbon");
    // }
    // elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h 
    //     inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon 
    //     inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon 
    //     where status = 'Approved' group by no_kbon");
    // }
    // elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon where status = 'Approved' and tgl_kbon between '$start_date' and '$end_date' group by no_kbon");
    // }
    // elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    //  $sql = mysqli_query($conn2,"select kontrabon_h.no_kbon as no_kbon, kontrabon_h.tgl_kbon as tgl_kbon, kontrabon_h.nama_supp as nama_supp, kontrabon_h.total as total, kontrabon_h.balance as balance, SUM(list_payment.amount) as amount, kontrabon.curr as curr, kontrabon.create_user as create_user, kontrabon.status as status, kontrabon.tgl_tempo as tgl_tempo, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon.pph_code as pph_code from kontrabon_h inner join kontrabon on kontrabon_h.no_kbon=kontrabon.no_kbon inner join list_payment on kontrabon_h.no_kbon = list_payment.no_kbon where status = 'Approved' and nama_supp = '$nama_supp' group by no_kbon");
    // }
    // else{
   $sql = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, no_po, tgl_po, SUM(subtotal) as sub, SUM(tax) as tax, SUM(total) as total, supp as supplier, status, keterangan, create_user from ftr_cbd where supp = '$nama_supp' and tgl_ftr_cbd between '$start_date' and '$end_date' and is_invoiced = 'Waiting' and status = 'Approved' group by no_ftr_cbd");                                                     
            while($row = mysqli_fetch_array($sql)){
            $cbd = $row['no_ftr_cbd'];
            $querys = mysqli_query($conn2,"select no_cbd, no_po, status from kontrabon_cbd where no_cbd = '$cbd' and status != 'Cancel'");
            $rows = mysqli_fetch_array($querys);
            $n_cbd = isset($rows['no_cbd']);
            $stat = isset($rows['status']);                            
            $sub = $row['sub'];
            $tax = $row['tax'];
            $total = $row['total'];
            if($cbd == $n_cbd and $stat != 'Cancel'){
                echo '';
            }else{
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>
                            <td style="width:50px;" value="'.$row['no_ftr_cbd'].'">'.$row['no_ftr_cbd'].'</td>                                                    
                            <td style="width:50px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>                            
                            <td style="width:100px;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                            <td style="width:100px;">                            
                            <select name="combo_pph" id="combo_pph" disabled>
                            <option data-idtax="" value="0" selected="selected">Non PPH</option>
                            '.$persen.'                                                                                     
                            </select>                                                        
                            </td>                            
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['status'].'">'.$row['status'].'</td>
                            <td style="display: none;" value="'.$row['keterangan'].'">'.$row['keterangan'].'</td>
                            <td style="display: none;" value="'.$row['create_user'].'">'.$row['create_user'].'</td>
                            <td style="display: none;" value="'.$row['tgl_ftr_cbd'].'">'.date("d-M-Y",strtotime($row['tgl_ftr_cbd'])).'</td>                                                                                                                
                        </tr>';
                }        
                }                               
                    ?>
            <!-- // $querys = mysqli_query($conn1,"select Groupp, finance from userpassword where username = '$user'");
            // $rs = mysqli_fetch_array($querys);
            // $group = $rs['Groupp'];
            // $fin = $rs['finance'];

           // echo '<td width="100px;">';
           //  if($date_diff == 0){
           //      echo '<p style="font-size: 13px;margin-bottom: -1px;color: warning;background-color:yellow"><b>Last Days</b></p>';
           //  }elseif($date_diff >= 1){
           //     echo "<font style='background-color:red;  font-weight: bold; color:white'>"; echo $date_diff; echo " Days Late";
           //  }elseif($date_diff >= -1){
           //      echo "<font style='background-color:green;  font-weight: bold; color:white'>"; echo abs($date_diff); echo " Days Left";
           //  }                                   
           //  echo '</td>';
            // echo '<td width="70px;">';
            // if($date_diff >= 1){
            //     echo '<a href="pdf_pcss.php?nokontrabon='.$row['no_kbon'].'" target="_blank"><button type="button" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"></i></button></a>';
            // }else{
            //     echo " - ";
            // }                                        
            // echo '</td>';
            // echo '<td value = "'.$row['tgl_tempo'].'" style="display: none;">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
            // <td value = "'.$row['no_faktur'].'" style="display: none;">'.$row['no_faktur'].'</td>
            // <td value = "'.$row['supp_inv'].'" style="display: none;">'.$row['supp_inv'].'</td>
            // <td value = "'.$row['tgl_inv'].'" style="display: none;">'.date("d-M-Y",strtotime($row['tgl_inv'])).'</td>
            // <td value = "'.$row['pph_code'].'" style="display: none;">'.$row['pph_code'].'</td>  -->                                                            
</tbody>                    
</table>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodalbon" data-target="#mymodalbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_tempo" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                           
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>         
                                
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
    // $('#datatable').dataTable({
    //     "serverSide": true,
    //     "processing": true,
    //     "ajax": {
    //         url: "fetch_data_kontrabon.php",
    //         type: "post"
    //     }
    // });
    $('#datatable').dataTable();
    // $.fn.dataTable.ext.errMode = 'none';
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

<!-- <script type="text/javascript">
    $("table tbody tr").on("click", "#approve", function(){                 
        var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvekbon.php',
            data: {'no_kbon':no_kbon, 'approve_user':approve_user},
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
        var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';        

        $.ajax({
            type:'POST',
            url:'cancelkbon.php',
            data: {'no_kbon':no_kbon, 'cancel_user':cancel_user},
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
</script> -->

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodalbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(1)').text();
    var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(11)').text();
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
    var status = $(this).closest('tr').find('td:eq(9)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();                

    $.ajax({
    type : 'post',
    url : 'ajaxkbon.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_kbon);
    $('#txt_tgl_kbon').html('Tgl Kontrabon : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Tgl Jatuh Tempo : ' + tgl_tempo + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                                     
});

</script>


<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "formkontrabon.php";
};
</script>
<script type="text/javascript">
    document.getElementById('btndraft').onclick = function () {
    location.href = "draft_kb.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "pcsbpb.php";
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
