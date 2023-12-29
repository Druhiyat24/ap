<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">PAYABLE CARD STATEMENT</h2>
<div class="box">
    <div class="box header">

        
        <form id="form-data" action="kartuhutang.php" method="post">        
        <div class="form-row">
            <div class="col-md-12">
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
                $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC",$conn1);
                while ($row = mysql_fetch_array($sql)) {
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
            <label for="nama_supp"><b>No BPB</b></label>            
              <select class="form-control selectpicker" name="no_bpb" id="no_bpb" data-dropup-auto="false" data-live-search="true"  onchange="this.form.submit()">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $no_bpb ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $no_bpb= isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
                }                 
                $sql = mysqli_query($conn2,"select distinct(no_bpb) from bpb_new where supplier = '$nama_supp' order by no_bpb ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['no_bpb'];
                    if($row['no_bpb'] == $_POST['no_bpb']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>

            <div class="col-md-3 mb-3"> 
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
            placeholder="Tanggal Awal" onchange="this.form.submit()">
            </div>

            <div class="col-md-3 mb-3"> 
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
            placeholder="Tanggal Awal" onchange="this.form.submit()">
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
            <tr class="thead-dark">
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No BPB</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Supplier </th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;display: none;">No Kontrabon</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Tax (Pph)</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;display: none;">No Payment</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;width: 80px">Foreign Currency</th>
            <th  colspan="3" style="text-align: center;vertical-align: middle;width: 80px">Equivalent IDR</th>            
            <th style="text-align: center;vertical-align: middle;display: none;">Total</th>                                    
            <th colspan="2" style="text-align: center;vertical-align: middle;width: 130px;">Action</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                          
        </tr>
         <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;display: none;">No</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 80px">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Balance</th>
            <th style="text-align: center;vertical-align: middle;">Credit</th>
            <th style="text-align: center;vertical-align: middle;width: 80px">Debit</th>
            <th style="text-align: center;vertical-align: middle;">Balance</th> 
            <th style="text-align: center;vertical-align: middle;">Show</th> 
            <th style="text-align: center;vertical-align: middle;">Pdf File</th>                                   
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                          
        </tr>
    </thead>
   
    <tbody>
<?php
    $value = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $no_bpb = isset($_POST['no_bpb']) ? $_POST['no_bpb']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }
   if(empty($nama_supp) and empty($no_bpb) and empty($start_date) and empty($end_date)){
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where tgl_bpb = '$date_now' and cek >= 1 group by no_bpb  order by cek asc");
    }
    elseif ($nama_supp == 'ALL' and $no_bpb == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {            
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where cek >=1 group by no_bpb order by cek asc");
    }
    elseif ($nama_supp == 'ALL' and $no_bpb == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where tgl_bpb between '$start_date' and '$end_date' and cek >= 1 group by no_bpb  order by cek asc");
    }
    elseif ($nama_supp != 'ALL' and $no_bpb == 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where nama_supp = '$nama_supp' and cek >= 1 group by no_bpb  order by cek asc");
    }
    elseif ($nama_supp != 'ALL' and $no_bpb == 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where nama_supp = '$nama_supp' and tgl_bpb between '$start_date' and '$end_date' and cek >= 1 group by no_bpb  order by cek asc");
    }
    elseif ($nama_supp == 'ALL' and $no_bpb != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where no_bpb = '$no_bpb' and cek >= 1 group by no_bpb  order by cek asc");
    }
    elseif ($nama_supp == 'ALL' and $no_bpb != 'ALL' and !empty($start_date) and !empty($end_date)) {
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where no_bpb = '$no_bpb' and tgl_bpb between '$start_date' and '$end_date' and cek >= 1 group by no_bpb  order by cek asc");
    }
    elseif ($nama_supp != 'ALL' and $no_bpb != 'ALL' and $start_date == '1970-01-01' and $end_date == '1970-01-01') {
     $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where no_bpb = '$no_bpb' and nama_supp = '$nama_supp' and cek >= 1 group by no_bpb order by cek asc");
    }
    else{
    $sql = mysqli_query($conn2,"select no_bpb, tgl_bpb, no_po, tgl_po, nama_supp, no_kbon, tgl_kbon, MAX(pph) as pph, no_payment, curr, MAX(credit_usd) as credit_usd, SUM(debit_usd) as debit_usd, balance_usd, MAX(credit_idr) as credit_idr, SUM(debit_idr) as debit_idr, balance_idr, cek from detail where nama_supp = '$nama_supp' and no_bpb = '$no_bpb' and tgl_bpb between '$start_date' and '$end_date' and cek >= 1 group by no_bpb order by cek asc");
}
   while($row = mysqli_fetch_array($sql)){
    $credit = $row['credit_idr'];
    $debit = $row['debit_idr'];
    $credit1 = $row['credit_usd'];
    $debit2 = $row['debit_usd'];
    $pph = $row['pph'];
    $curr = $row['curr'];
    $pph1 = $pph / 14300;
    $balance = $credit - $debit - $pph;
    $balance1 = $credit1 - $debit2 - $pph1;

        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="display: none;" value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
            <td style="display: none;" value = "'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
            <td style="text-align:right;" value = "'.$row['pph'].'">'.number_format($row['pph'],2).'</td>
            <td style="display: none;" value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
            <td style="text-align:right;" value = "'.$row['credit_usd'].'">'.number_format($row['credit_usd'],2).'</td>
            <td style="text-align:right;" value = "'.$row['debit_usd'].'">'.number_format($row['debit_usd'],2).'</td>         
            <td style="text-align:right;" value = "'.$balance1.'">'.number_format($balance1,2).'</td>
            <td style="text-align:right;" value = "'.$row['credit_idr'].'">'.number_format($row['credit_idr'],2).'</td>
            <td style="text-align:right;" value = "'.$row['debit_idr'].'">'.number_format($row['debit_idr'],2).'</td>         
            <td style="text-align:right;" value = "'.$balance.'">'.number_format($balance,2).'</td>
             <td width="70px;">
                <button type="button" class="btn btn-outline-dark"><i class="fa fa-asterisk" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Show</i></button>                                     
            </td>';
            echo '<td width="100px;">';
            if($curr == 'IDR'){
                echo '<a href="pdf_kartuhutangidr.php?no_bpb='.$row['no_bpb'].'" target="_blank"><button type="button" class="btn btn-outline-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> ';
            }else{
                echo '<a href="pdf_kartuhutang.php?no_bpb='.$row['no_bpb'].'" target="_blank"><button type="button" class="btn btn-outline-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a> ';
            }                                  
            echo '</td>';
            echo '</tr>';
            
        // }
}?>
                                                         
</tbody>                    
</table>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodalbon" data-target="#mymodalbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
          <!-- <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
          <!-- <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                                          
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
    $('table tbody tr').on('click', 'td:eq(12)', function(){                
    $('#mymodalbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(3)').text();
    var supp = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(11)').text();
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
    var status = $(this).closest('tr').find('td:eq(9)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();                

    $.ajax({
    type : 'post',
    url : 'ajaxkartuhutang.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_kbon);
    $('#txt_tgl_kbon').html('BPB Date : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    // $('#txt_tgl_tempo').html('No PO: ' + tgl_tempo + '');
    // $('#txt_curr').html('PO Date : ' + curr + '');        
    // $('#txt_create_user').html('Create By : ' + create_user + '');
    // $('#txt_status').html('Status : ' + status + '');
    // $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    // $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    // $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                                     
});

</script>


<!-- <script type="text/javascript">
    document.getElementById('btnbpb').onclick = function () {
    location.href = "kartuhutang.php";
};
</script>
<script type="text/javascript">
    document.getElementById('btnkb').onclick = function () {
    location.href = "pcs2.php";
};
</script>
<script type="text/javascript">
    document.getElementById('btnlp').onclick = function () {
    location.href = "pcs.php";
};
</script> -->


<script type="text/javascript">
    document.getElementById('btndraft').onclick = function () {
    location.href = "draft_kb.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "kartuhutang.php";
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
