<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h3 class="text-center">INVOICE RECEIVED REPORT</h3>
<div class="box">
    <div class="box header">

        <form id="form-data" action="report_invoice_received.php" method="post">        
        <div class="form-row">
            <div class="col-md-5 mb-1">
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
            placeholder="Start Date" autocomplete='off' style="height: 32px;font-size:13px;">
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
            placeholder="Tanggal Akhir" style="height: 32px;font-size:13px;">
            </div>
        </div>

            <div class="input-group-append col">                                   
            <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 10px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 10px;margin-right: 15px;border: 0;
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

            echo '<a target="_blank" href="ekspor_ir.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success" style= "margin-top: 30px;margin-bottom:10px"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        ?> 
            </div>                                                            
    </div>
</div>
</form> 

    </div>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">
<div class="d-flex justify-content-between mr-2 mb-1">
                    <div class="ml-auto">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text"  id="myInput" name="myInput" required autocomplete="off" placeholder="Search Data..." onkeyup="myFunction()">
                </div>
    <div class="tableFix table-responsive" style="height: 350px;">        
<table id="datatable" class="table table-striped table-bordered text-nowrap" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">                                                
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">Supplier Invoice No</th>
            <th style="text-align: center;vertical-align: middle;">No Kontrabon</th>
            <th style="text-align: center;vertical-align: middle;">No Reff</th>
            <th style="text-align: center;vertical-align: middle;">Supplier Name</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
            <th style="text-align: center;vertical-align: middle;">Status</th>
            <th style="text-align: center;vertical-align: middle;">SI Date</th>
            <th style="text-align: center;vertical-align: middle;">IR Date</th>
            <th style="text-align: center;vertical-align: middle;">BPB Date</th>
            <th style="text-align: center;vertical-align: middle;">BPB Create</th>
            <th style="text-align: center;vertical-align: middle;">BPB-A Date</th>
            <th style="text-align: center;vertical-align: middle;">BPB-R Date</th>
            <th style="text-align: center;vertical-align: middle;">FTA Date</th>
            <th style="text-align: center;vertical-align: middle;">ARF Date</th>
            <th style="text-align: center;vertical-align: middle;">ATP Date</th>
            <th style="text-align: center;vertical-align: middle;">PRA Date</th>
            <th style="text-align: center;vertical-align: middle;">PTF Date</th>
            <th style="text-align: center;vertical-align: middle;">FRP Date</th>
            <th style="text-align: center;vertical-align: middle;">KB Date</th>
            <th style="text-align: center;vertical-align: middle;">LP Date</th>
            <th style="text-align: center;vertical-align: middle;">LP Approve Date</th>
            <th style="text-align: center;vertical-align: middle;">PAY Date</th>
        </tr>
    </thead>
   
    <tbody>
    <?php
    $nama_supp ='';
    $start_date ='';
    $end_date ='';
    $no = 1;
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }
    if($nama_supp == 'ALL'){
     $sql = mysqli_query($conn2,"select * from (select no_invoice,COALESCE(no_reff,'-') no_reff,a.doc_number,b.nama_supp,a.amount,b.status,tgl_invoice,tgl_penerimaan,tfta_date,receive_acc_date,tatp_date,receive_pch_date,tptf_date,receive_fin_date from ir_invoice_supp a inner join ir_invoice_supp_h b on b.doc_number = a.doc_number where tgl_invoice between '$start_date' and '$end_date' group by a.id) a left join (select max(bpbdate) bpbdate,max(dateinput) dateinput,max(confirm_date) confirm_date,upt_no_inv,max(trf_date) trf_date,b.supplier from bpb a inner join mastersupplier b on b.id_supplier  = a.id_supplier where upt_no_inv is not null and upt_no_inv != '-' GROUP BY upt_no_inv,supplier) b on b.upt_no_inv = a.no_invoice and b.supplier = a.nama_supp left join
(select supplier supp,upt_no_inv,MAX(tgl_kbon) tgl_kbon,MAX(tgl_payment) tgl_payment,MAX(tgl_approve_lp) tgl_approve_lp,MAX(bankout_date) bankout_date from (select a.*,b.no_kbon,b.tgl_kbon,b.create_date created_kbon,c.no_payment,c.tgl_payment,c.create_date created_lp,DATE_FORMAT(c.confirm_date, '%Y-%m-%d') tgl_approve_lp, d.no_bankout, d.bankout_date  from (select no_bpb,supplier,upt_no_inv from bpb_new where upt_no_inv is not null and upt_no_inv != '-' GROUP BY no_bpb) a left join 
(select * from kontrabon where status != 'Cancel') b on b.no_bpb = a.no_bpb left join 
(select * from list_payment where status != 'Cancel') c on c.no_kbon = b.no_kbon left join 
(select * from (select a.no_bankout,bankout_date,no_reff from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where no_reff like '%LP/NAG%' and b.status != 'Cancel'
UNION
select a.no_pco,b.tgl_pco,no_reff from c_petty_cashout_det a inner join c_petty_cashout_h b on a.no_pco = b.no_pco where no_reff like '%LP/NAG%' and b.status != 'Cancel'
UNION
select payment_ftr_id,tgl_pelunasan,list_payment_id from payment_ftr where list_payment_id like '%LP/NAG%' and status != 'Cancel') a GROUP BY no_reff order by bankout_date desc) d on d.no_reff = c.no_payment GROUP BY no_bpb) a GROUP BY supplier,upt_no_inv) c on c.upt_no_inv = a.no_invoice and c.supp = a.nama_supp");
    }else{
    $sql = mysqli_query($conn2,"select * from ( select no_invoice,COALESCE(no_reff,'-') no_reff,a.doc_number,b.nama_supp,a.amount,b.status,tgl_invoice,tgl_penerimaan,tfta_date,receive_acc_date,tatp_date,receive_pch_date,tptf_date,receive_fin_date from ir_invoice_supp a inner join ir_invoice_supp_h b on b.doc_number = a.doc_number where b.nama_supp = '$nama_supp' and tgl_invoice between '$start_date' and '$end_date' group by a.id) a left join (select max(bpbdate) bpbdate,max(confirm_date) confirm_date,upt_no_inv,max(trf_date) trf_date,max(dateinput) dateinput,b.supplier from bpb a inner join mastersupplier b on b.id_supplier  = a.id_supplier where upt_no_inv is not null and upt_no_inv != '-' GROUP BY upt_no_inv,supplier) b on b.upt_no_inv = a.no_invoice and b.supplier = a.nama_supp left join
(select supplier supp,upt_no_inv,MAX(tgl_kbon) tgl_kbon,MAX(tgl_payment) tgl_payment,MAX(tgl_approve_lp) tgl_approve_lp,MAX(bankout_date) bankout_date from (select a.*,b.no_kbon,b.tgl_kbon,b.create_date created_kbon,c.no_payment,c.tgl_payment,c.create_date created_lp,DATE_FORMAT(c.confirm_date, '%Y-%m-%d') tgl_approve_lp, d.no_bankout, d.bankout_date  from (select no_bpb,supplier,upt_no_inv from bpb_new where upt_no_inv is not null and upt_no_inv != '-' GROUP BY no_bpb) a left join
(select * from kontrabon where status != 'Cancel') b on b.no_bpb = a.no_bpb left join 
(select * from list_payment where status != 'Cancel') c on c.no_kbon = b.no_kbon left join 
(select * from (select a.no_bankout,bankout_date,no_reff from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where no_reff like '%LP/NAG%' and b.status != 'Cancel'
UNION
select a.no_pco,b.tgl_pco,no_reff from c_petty_cashout_det a inner join c_petty_cashout_h b on a.no_pco = b.no_pco where no_reff like '%LP/NAG%' and b.status != 'Cancel'
UNION
select payment_ftr_id,tgl_pelunasan,list_payment_id from payment_ftr where list_payment_id like '%LP/NAG%' and status != 'Cancel') a GROUP BY no_reff order by bankout_date desc) d on d.no_reff = c.no_payment GROUP BY no_bpb) a GROUP BY supplier,upt_no_inv) c on c.upt_no_inv = a.no_invoice and c.supp = a.nama_supp");
}

    while($row = mysqli_fetch_array($sql)){
        if ($row['tfta_date'] == null || $row['tfta_date'] == '') { $tfta_date = '-'; }else{ $tfta_date = date("d-M-Y",strtotime($row['tfta_date']));}

        if ($row['receive_acc_date'] == null || $row['receive_acc_date'] == '') { $receive_acc_date = '-'; }else{ $receive_acc_date = date("d-M-Y",strtotime($row['receive_acc_date']));}

        if ($row['tatp_date'] == null || $row['tatp_date'] == '') { $tatp_date = '-'; }else{ $tatp_date = date("d-M-Y",strtotime($row['tatp_date']));}

        if ($row['receive_pch_date'] == null || $row['receive_pch_date'] == '') { $receive_pch_date = '-'; }else{ $receive_pch_date = date("d-M-Y",strtotime($row['receive_pch_date']));}

        if ($row['tptf_date'] == null || $row['tptf_date'] == '') { $tptf_date = '-'; }else{ $tptf_date = date("d-M-Y",strtotime($row['tptf_date']));}

        if ($row['receive_fin_date'] == null || $row['receive_fin_date'] == '') { $receive_fin_date = '-'; }else{ $receive_fin_date = date("d-M-Y",strtotime($row['receive_fin_date']));}

        if ($row['bpbdate'] == null || $row['bpbdate'] == '') { $bpbdate = '-'; }else{ $bpbdate = date("d-M-Y",strtotime($row['bpbdate']));}

        if ($row['confirm_date'] == null || $row['confirm_date'] == '') { $confirm_date = '-'; }else{ $confirm_date = date("d-M-Y",strtotime($row['confirm_date']));}

        if ($row['trf_date'] == null || $row['trf_date'] == '') { $trf_date = '-'; }else{ $trf_date = date("d-M-Y",strtotime($row['trf_date']));}
        
        if ($row['tgl_kbon'] == null || $row['tgl_kbon'] == '') { $tgl_kbon = '-'; }else{ $tgl_kbon = date("d-M-Y",strtotime($row['tgl_kbon']));}

        if ($row['tgl_payment'] == null || $row['tgl_payment'] == '') { $tgl_payment = '-'; }else{ $tgl_payment = date("d-M-Y",strtotime($row['tgl_payment']));}

        if ($row['bankout_date'] == null || $row['bankout_date'] == '') { $bankout_date = '-'; }else{ $bankout_date = date("d-M-Y",strtotime($row['bankout_date']));}

        if ($row['dateinput'] == null || $row['dateinput'] == '') { $dateinput = '-'; }else{ $dateinput = date("d-M-Y",strtotime($row['dateinput']));}

        if ($row['tgl_approve_lp'] == null || $row['tgl_approve_lp'] == '') { $tgl_approve_lp = '-'; }else{ $tgl_approve_lp = date("d-M-Y",strtotime($row['tgl_approve_lp']));}

    $status = $row['status'];         
        echo '<tr style="font-size:12px;text-align:center;">
            <td value = "">'.$no++.'.</td>
            <td value = "'.$row['no_invoice'].'">'.$row['no_invoice'].'</td>
            <td value = "'.$row['doc_number'].'">'.$row['doc_number'].'</td>
            <td value = "'.$row['no_reff'].'">'.$row['no_reff'].'</td>
            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td value = "'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
            <td value = "'.$row['tgl_invoice'].'">'.date("d-M-Y",strtotime($row['tgl_invoice'])).'</td>
            <td value = "'.$row['tgl_penerimaan'].'">'.date("d-M-Y",strtotime($row['tgl_penerimaan'])).'</td>
            <td value = "'.$bpbdate.'">'.$bpbdate.'</td>
            <td value = "'.$dateinput.'">'.$dateinput.'</td>
            <td value = "'. $confirm_date.'">'. $confirm_date.'</td>
            <td value = "'.$trf_date.'">'.$trf_date.'</td>
            <td value = "'.$tfta_date.'">'.$tfta_date.'</td>
            <td value = "'.$receive_acc_date.'">'.$receive_acc_date.'</td>
            <td value = "'.$tatp_date.'">'.$tatp_date.'</td>
            <td value = "'.$receive_pch_date.'">'.$receive_pch_date.'</td>
            <td value = "'.$tptf_date.'">'.$tptf_date.'</td>
            <td value = "'.$receive_fin_date.'">'.$receive_fin_date.'</td>
            <td value = "'.$tgl_kbon.'">'.$tgl_kbon.'</td>
            <td value = "'.$tgl_payment.'">'.$tgl_payment.'</td>
            <td value = "'.$tgl_approve_lp.'">'.$tgl_approve_lp.'</td>
            <td value = "'.$bankout_date.'">'.$bankout_date.'</td>
            </tr>';
}?>
</tbody>                    
</table>

</div>
<div class="form-row ml-1">
<div class="card col-md-4">
  <ul class="">
    <li class="">SI : Supplier Invoice</li>
    <li class="">IR : Supplier Invoice Received</li>
    <li class="">FTA : Finance To Accounting</li>
    <li class="">ARF : Accounting Receive From Finance</li>
  </ul>
</div>
<div class="card col-md-4">
  <ul class="">
    <li class="">ATP : Accounting To Purchasing</li>
    <li class="">PRA : Purchasing Received From Accounting</li>
    <li class="">PTF : Purchasing To Finance</li>
    <li class="">FRP : Finance Received From Purchasing</li>
  </ul>
</div>
<div class="card col-md-4">
  <ul class="">
    <li class="">BPB : Good Received In Warehouse</li>
    <li class="">BPB-A : Good Received Approved</li>
    <li class="">BPB-R : Good Received To Accounting</li>
  </ul>
</div>
</div>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodalkbon" data-target="#mymodalkbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
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
<!-- <script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

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
        var no_payment = $(this).closest('tr').find('td:eq(0)').attr('value');
        var tgl_payment = $(this).closest('tr').find('td:eq(1)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(2)').attr('value');
        var amount = $(this).closest('tr').find('td:eq(5)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(6)').attr('value');
        var no_kbon = $(this).closest('tr').find('td:eq(9)').attr('value');
        var tgl_kbon = $(this).closest('tr').find('td:eq(10)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(11)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(12)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(13)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(14)').attr('value');
        var pph = $(this).closest('tr').find('td:eq(15)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvelistreport_invoice_received.php',
            data: {'no_payment':no_payment, 'approve_user':approve_user, 'tgl_payment':tgl_payment, 'supp':supp, 'amount':amount, 'curr':curr, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'no_po':no_po, 'tgl_po':tgl_po, 'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb, 'pph':pph},
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
    $("table tbody tr").on("click", "#delete", function(){                 
        var no_payment = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';
        $.ajax({
            type:'POST',
            url:'cancellistreport_invoice_received.php',
            data: {'no_payment':no_payment, 'cancel_user':cancel_user},
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
    $('table tbody tr').on('click', 'td:eq(8)', function(){                
    $('#mymodalkbon').modal('show');
    var no_inv = $(this).closest('tr').find('td:eq(1)').attr('value'); 
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');                 

    $.ajax({
    type : 'post',
    url : 'ajax_reportinv.php',
    data : {'no_inv': no_inv,'supp': supp},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_inv);
    $('#txt_nama_supp').html('Supplier : ' + supp + '');                            
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "formreport_invoice_received.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "report_invoice_received.php";
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