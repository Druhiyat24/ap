<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">STATUS INFORMATION</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="status.php" method="post">        
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

                <div class="col-md-4">
            <label for="status"><b>Date Filter</b></label>            
              <select class="form-control selectpicker" name="filter" id="filter" data-dropup-auto="false" data-live-search="true">
                <option value="tgl_bpb" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
                }                 
                    if($status == 'tgl_bpb'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >BPB Date</option>
                <option value="tgl_kbon" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
                }                 
                    if($status == 'tgl_kbon'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Kontrabon Date</option>
                <option value="tgl_lp" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
                }                 
                    if($status == 'tgl_lp'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >List Payment Date</option> 
                <option value="tgl_pay" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['filter']) ? $_POST['filter']: null;
                }                 
                    if($status == 'tgl_pay'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Payment Date</option>                                                                                                            
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
               echo date("d-m-Y");
            } ?>" 
            placeholder="Start Date" autocomplete='off' >
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
            placeholder="Tanggal Akhir" >
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
        $filter = isset($_POST['filter']) ? $_POST['filter']: null;
//         tgl_bpb
// tgl_kbon
// tgl_lp
// tgl_pay
        if($filter == 'tgl_pay'){
            $filterr = "Payment Date";
            echo '<a target="_blank" href="ekspor_status.php?nama_supp='.$nama_supp.' && filter='.$filterr.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($filter == 'tgl_lp'){
            $filterr = "List Payment Date";
            echo '<a target="_blank" href="ekspor_status_lp.php?nama_supp='.$nama_supp.' && filter='.$filterr.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($filter == 'tgl_kbon'){
            $filterr = "Kontrabon Date"; 
            echo '<a target="_blank" href="ekspor_status_kbon.php?nama_supp='.$nama_supp.' && filter='.$filterr.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($filter == 'tgl_bpb'){
            $filterr = "BPB Date";
            echo '<a target="_blank" href="ekspor_status_bpb.php?nama_supp='.$nama_supp.' && filter='.$filterr.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }else{
            $filterr = ""; 
        }
        ?>          
            </div>                                                             
    </div>
</br>
</div>
</form>
</form>

            
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">No BPB</th>
            <th style="text-align: center; vertical-align: middle;">BPB Date</th>
            <th style="text-align: center; vertical-align: middle;">No Kontrabon</th>
            <th style="text-align: center; vertical-align: middle;">Kontrabon Date</th>
            <th style="text-align: center; vertical-align: middle;">No List Payment</th>
            <th style="text-align: center; vertical-align: middle;">List Payment Date</th>                         
            <th style="text-align: center; vertical-align: middle;">No Payment</th>                                    
            <th style="text-align: center; vertical-align: middle;">Payment Date</th>                      
        </tr>
    </thead>
    
<tbody>
    <?php
    $nama_supp ='';
    $status = '';
    $filter = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $status = isset($_POST['status']) ? $_POST['status']: null;
    $filter = isset($_POST['filter']) ? $_POST['filter']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }

//    if(empty($nama_supp) and empty($status) and empty($start_date) and empty($end_date)){
//    $kondisi = '';
//    }
//    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//    $kondisi = '';
//    }
//    elseif ($nama_supp == 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
//    $kondisi = 'where tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }
//    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//    $kondisi = 'where supplier = '.$nama_supp.'';
//    }
//    elseif ($nama_supp != 'ALL' and $status == 'ALL' and !empty($start_date) and !empty($end_date)) {
//    $kondisi = 'where supplier = '.$nama_supp.' and tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }
//    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
//    $kondisi = 'where status = '.$status.'';
//    }
//    elseif ($nama_supp == 'ALL' and $status != 'ALL' and !empty($start_date) and !empty($end_date)) {
//    $kondisi = 'where status = '.$status.' and tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }
//    else{
//    $kondisi = 'where supplier = '.$nama_supp.' and status = '.$status.' and tgl_bpb between '.$start_date.' and '.$end_date.'';
//    }

//    echo $kondisi;
    if ($filter == 'tgl_bpb') {
        if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_bpb = '$date_now' group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_bpb between '$start_date' and '$end_date' group by no_bpb");
    }    
    elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' group by no_bpb");
    }
    else{
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_bpb between '$start_date' and '$end_date' group by no_bpb");
    }

    }elseif ($filter == 'tgl_kbon') {
        if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_kbon = '$date_now' group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_kbon between '$start_date' and '$end_date' group by no_bpb");
    }    
    elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' group by no_bpb");
    }
    else{
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_kbon between '$start_date' and '$end_date' group by no_bpb");
    }

}elseif ($filter == 'tgl_lp') {
        if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_lp = '$date_now' group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_lp between '$start_date' and '$end_date' group by no_bpb");
    }    
    elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' group by no_bpb");
    }
    else{
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_lp between '$start_date' and '$end_date' group by no_bpb");
    }

    }else{
        if(empty($nama_supp) and empty($start_date) and empty($end_date)){
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_pay = '$date_now' group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status group by no_bpb");
    }
    elseif ($nama_supp == 'ALL' and !empty($start_date) and !empty($end_date)) {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where tgl_pay between '$start_date' and '$end_date' group by no_bpb");
    }    
    elseif ($nama_supp != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' group by no_bpb");
    }
    else{
    $sql = mysqli_query($conn2,"select id, supp, no_bpb, tgl_bpb, no_kbon, tgl_kbon, no_lp, tgl_lp, no_pay, tgl_pay from status where supp = '$nama_supp' and tgl_pay between '$start_date' and '$end_date' group by no_bpb");
    }
    }   


    while($row = mysqli_fetch_array($sql)){
    $tgl_kb = isset($row['tgl_kbon']) ? $row['tgl_kbon'] :null;
    $tgl_lp = isset($row['tgl_lp']) ? $row['tgl_lp'] :null;
    $tgl_pay = isset($row['tgl_pay']) ? $row['tgl_pay'] :null;

    if ($tgl_kb != '') {
        $tgl_kbon = date("d-M-Y",strtotime($row['tgl_kbon']));
        $kbon = $row['no_kbon'];
    }else{
        $tgl_kbon = '-';
        $kbon = '-';
    }

    if ($tgl_lp != '') {
        $tgl_lipa = date("d-M-Y",strtotime($row['tgl_lp']));
        $lipa = $row['no_lp'];
    }else{
        $tgl_lipa = '-';
        $lipa = '-';
    }

    if ($tgl_pay != '') {
        $tgl_payment = date("d-M-Y",strtotime($row['tgl_pay']));
        $payment = $row['no_pay'];
    }else{
        $tgl_payment = '-';
        $payment = '-';
    }

    if (!empty($row)) {       
        echo'<tr style="font-size: 12px; text-align: center;">
            <td style="width: 250px;" value="'.$row['supp'].'">'.$row['supp'].'</td>
            <td value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td style="" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
            <td value="'.$kbon.'">'.$kbon.'</td>
            <td style="" value="'.$tgl_kbon.'">'.$tgl_kbon.'</td>
            <td value="'.$lipa.'">'.$lipa.'</td>
            <td style="" value="'.$tgl_lipa.'">'.$tgl_lipa.'</td>
            <td value="'.$payment.'">'.$payment.'</td>
            <td style="" value="'.$tgl_payment.'">'.$tgl_payment.'</td>';

        echo '</tr>';
    }
                        
}?>
</tbody>                    
</table>

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
          <div id="txt_confirm2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                               
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div> 

    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>
  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
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
    location.href = "status.php";
};
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "status.php";
};
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
