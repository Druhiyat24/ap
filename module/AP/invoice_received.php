<?php include '../header.php' ?>

<!-- MAIN -->
<div class="col p-4">
    <h3 class="text-center">LIST INVOICE RECEIVED</h3>
    <div class="box">
        <div class="box header">
            <form id="form-data" action="invoice_received.php" method="post">
                <div class="form-row">
                    <div class="col-md-4 mb-2">
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
                            $sql = mysqli_query($conn1,"select distinct(nama_status) nama_status from ir_status where status = 'Y' order by id ASC");
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

                    <div class="col-md-3">
                        <label for="keterangan"><b>Description</b></label>            
                        <select class="form-control selectpicker" name="keterangan" id="keterangan" data-dropup-auto="false" data-live-search="true">
                            <option value="ALL" selected="true">ALL</option>                                                
                            <?php
                            $keterangan ='';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan']: null;
                            }                 
                            $sql = mysqli_query($conn1,"select distinct(nama_status) nama_status from ir_status where status = 'dec' order by id ASC");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['nama_status'];
                                if($row['nama_status'] == $_POST['keterangan']){
                                    $isSelected = ' selected="selected"';
                                }else{
                                    $isSelected = '';
                                }
                                echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            }?>                                                                                                             
                        </select>
                    </div>
                    <div class="col-md-3"></div>

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

                echo '<a target="_blank" href="ekspor_invoice.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success" style= "margin-top: 30px;margin-bottom:5px"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
                ?>
            </div>                                                            
        </div>
        <br>

    </div>
</form> 

<?php
$querys1 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Invoice Received'");
$rs1 = mysqli_fetch_array($querys1);
$id1 = isset($rs1['id']) ? $rs1['id'] : 0;

$querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Transfer Invoice Fin-Acc'");
$rs2 = mysqli_fetch_array($querys2);
$id2 = isset($rs2['id']) ? $rs2['id'] : 0;

$querys3 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Accept Invoice Acc'");
$rs3 = mysqli_fetch_array($querys3);
$id3 = isset($rs3['id']) ? $rs3['id'] : 0;

$querys4 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Transfer Invoice Acc-Pch'");
$rs4 = mysqli_fetch_array($querys4);
$id4 = isset($rs4['id']) ? $rs4['id'] : 0;

$querys5 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Accept Invoice Pch'");
$rs5 = mysqli_fetch_array($querys5);
$id5 = isset($rs5['id']) ? $rs5['id'] : 0;

$querys6 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Transfer Invoice Pch-Fin'");
$rs6 = mysqli_fetch_array($querys6);
$id6 = isset($rs6['id']) ? $rs6['id'] : 0;

$querys7 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Accept Invoice Fin'");
$rs7 = mysqli_fetch_array($querys7);
$id7 = isset($rs7['id']) ? $rs7['id'] : 0;

$querys8 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Reverse Invoice'");
$rs8 = mysqli_fetch_array($querys8);
$id8 = isset($rs8['id']) ? $rs8['id'] : 0;

$sql_acc = mysqli_query($conn2,"select COUNT(doc_number ) doc_number from ir_invoice_supp_h where status = 'Post Fin To Acc'");
$row_acc = mysqli_fetch_array($sql_acc);
$count_acc = $row_acc['doc_number'];

$sql_pch = mysqli_query($conn2,"select COUNT(doc_number ) doc_number from ir_invoice_supp_h where status = 'Post Acc To Pch'");
$row_pch = mysqli_fetch_array($sql_pch);
$count_pch = $row_pch['doc_number'];

$sql_fin = mysqli_query($conn2,"select COUNT(doc_number ) doc_number from ir_invoice_supp_h where status = 'Post Pch To Fin'");
$row_fin = mysqli_fetch_array($sql_fin);
$count_fin = $row_fin['doc_number'];

$sql_post1 = mysqli_query($conn2,"select COUNT(doc_number ) doc_number from ir_invoice_supp_h where status = 'Received'");
$row_post1 = mysqli_fetch_array($sql_post1);
$count_post1 = $row_post1['doc_number'];

$sql_post2 = mysqli_query($conn2,"select COUNT(doc_number ) doc_number from ir_invoice_supp_h where status IN ('Received','Accepted Acc')");
$row_post2 = mysqli_fetch_array($sql_post2);
$count_post2 = $row_post2['doc_number'];

$sql_post3 = mysqli_query($conn2,"select COUNT(doc_number ) doc_number from ir_invoice_supp_h where status = 'Accepted Pch'");
$row_post3 = mysqli_fetch_array($sql_post3);
$count_post3 = $row_post3['doc_number'];


if($id1 == '67'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs"><span class="fa fa-pencil-square-o"></span> Invoice received</button>';
}

if($id2 == '68'){
    echo '<button style="margin-left: 5px;" id="trf_fintoacc" type="button" class="btn-info btn-xs"><span class="fa fa-paper-plane"></span> Post Fin-Acc <span class="badge bg-danger text-white" style="font-size:10px">'.$count_post1.'</span></button>';
}

if($id3 == '69'){
    echo '<button style="margin-left: 5px;" id="appv_acc" type="button" class="btn-success btn-xs"><span class="fa fa-thumbs-up"></span> Accept Acc <span class="badge bg-danger text-white" style="font-size:10px">'.$count_acc.'</span></button>';
}

if($id4 == '70'){
    echo '<button style="margin-left: 5px;" id="trf_acctopch" type="button" class="btn-info btn-xs"><span class="fa fa-paper-plane"></span> Post Acc-Pch <span class="badge bg-danger text-white" style="font-size:10px">'.$count_post2.'</span></button>';
}

if($id5 == '71'){
    echo '<button style="margin-left: 5px;" id="appv_pch" type="button" class="btn-success btn-xs"><span class="fa fa-thumbs-up"></span> Accept Pch <span class="badge bg-danger text-white" style="font-size:10px">'.$count_pch.'</span></button>';
}

if($id6 == '72'){
    echo '<button style="margin-left: 5px;" id="trf_pchtofin" type="button" class="btn-info btn-xs"><span class="fa fa-paper-plane"></span> Post Pch-Fin <span class="badge bg-danger text-white" style="font-size:10px">'.$count_post3.'</span></button>';
}

if($id7 == '73'){
    echo '<button style="margin-left: 5px;" id="appv_fin" type="button" class="btn-success btn-xs"><span class="fa fa-thumbs-up"></span> Accept Fin <span class="badge bg-danger text-white" style="font-size:10px">'.$count_fin.'</span></button>';
}

if($id8 == '74'){
    echo '<button style="margin-left: 5px;" id="reverse_trf" type="button" class="btn-warning btn-xs"><span class="fa fa-undo"></span> Reverse</button>';
}

?>
</div>
<div class="box body">
    <div class="row">       
        <div class="col-md-12">

            <div class="table-responsive">
                <table id="datatable" class="table table-striped table-bordered text-nowrap" role="grid" cellspacing="0" width="100%">
                    <thead>
                        <tr class="thead-dark">
                            <th style="text-align: center;vertical-align: middle;width: 15%;">No Document</th>
                            <th style="text-align: center;vertical-align: middle;width: 10%;">Document Date</th>
                            <th style="text-align: center;vertical-align: middle;width: 15%;">Supplier</th> 
                            <th style="text-align: center;vertical-align: middle;width: 10%;">Total Amount</th>
                            <th style="text-align: center;vertical-align: middle;width: 10%;">Status</th>
                            <th style="text-align: center;vertical-align: middle;width: 15%;">Description</th>
                            <th style="text-align: center;vertical-align: middle;width: 15%;">Create User</th> 
                            <th style="text-align: center;vertical-align: middle;width: 10%;">Action</th>
                            <th hidden></th>                                         
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $nama_supp ='';
                        $keterangan ='';
                        $status = '';
                        $start_date ='';
                        $end_date ='';
                        $date_now = date("Y-m-d");                
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                            $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan']: null; 
                            $status = isset($_POST['status']) ? $_POST['status']: null;
                            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                            $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
                        }

                        if ($nama_supp == 'ALL' and $status == 'ALL' and $keterangan == 'ALL') {            
                           $where = "where tgl_penerimaan between '$start_date' and '$end_date'";
                       }
                       elseif ($nama_supp != 'ALL' and $status == 'ALL' and $keterangan == 'ALL') {
                           $where = "where nama_supp = '$nama_supp' and tgl_penerimaan between '$start_date' and '$end_date'";
                       }
                       elseif ($nama_supp == 'ALL' and $status != 'ALL' and $keterangan == 'ALL') {
                           $where = "where status = '$status' and tgl_penerimaan between '$start_date' and '$end_date'";
                       }
                       elseif ($nama_supp == 'ALL' and $status == 'ALL' and $keterangan != 'ALL') {
                           $where = "where keterangan = '$keterangan' and tgl_penerimaan between '$start_date' and '$end_date'";
                       } else{
                        $where = "where nama_supp = '$nama_supp' and status = '$status' and keterangan = '$keterangan' and tgl_penerimaan between '$start_date' and '$end_date'";
                    }

                    $sql = mysqli_query($conn2,"select * from (select 'IR' id,doc_number,tgl_penerimaan,nama_supp,total_amount, IF(status != 'Cancel','Received','Cancel')status, CONCAT(created_by,' (',created_date,')') create_user, 'Received Invoice' keterangan, COALESCE(no_reff,'-') no_reff from ir_invoice_supp_h
                        UNION
                        select id,a.no_trans,tgl_trans,nama_supp,amount,status,create_user,CASE
                        WHEN nama_trans = 'TFTA' and status like '%Approved%' THEN 'Accept From Finance To Accounting'
                        WHEN nama_trans = 'TFTA' and status = 'Post' THEN 'Transfer From Finance To Accounting'
                        WHEN nama_trans = 'TFTA' and status = 'Cancel' THEN 'Cancel From Finance To Accounting'
                        WHEN nama_trans = 'TATP' and status like '%Approved%' THEN 'Accept From Accounting To Purchasing'
                        WHEN nama_trans = 'TATP' and status = 'Post' THEN 'Transfer From Accounting To Purchasing'
                        WHEN nama_trans = 'TATP' and status = 'Cancel' THEN 'Cancel From Accounting To Purchasing'
                        WHEN nama_trans = 'TPTF' and status like '%Approved%' THEN 'Accept From Purchasing To Finance'
                        WHEN nama_trans = 'TPTF' and status = 'Post' THEN 'Transfer From Purchasing To Finance'
                        WHEN nama_trans = 'TPTF' and status = 'Cancel' THEN 'Cancel From Purchasing To Finance'
                        END as keterangan, '-' from (select 'TF' id,nama_trans,no_trans,tgl_trans,nama_supp,SUM(amount) amount,CONCAT(created_by,' (',created_date,')') create_user from ir_trans_invoice_supp GROUP BY no_trans) a inner join (select no_trans,CASE
                        WHEN s_post > 0 THEN 'Post'
                        WHEN s_cancel > 0 and s_approved = 0 THEN 'Cancel'
                        WHEN s_cancel = 0 and s_approved > 0 THEN 'Approved'
                        WHEN s_cancel > 0 and s_approved > 0 THEN 'Approved Partial'
                        END as status from (select a.no_trans,COALESCE(s_post,0) s_post,COALESCE(s_cancel,0) s_cancel, COALESCE(s_approved,0) s_approved from (select no_trans from ir_trans_invoice_supp GROUP BY no_trans) a left join
                        (select no_trans,COUNT(status) s_post from ir_trans_invoice_supp where status = 'Post' GROUP BY no_trans) b on b.no_trans = a.no_trans LEFT JOIN
                        (select no_trans,COUNT(status) s_cancel from ir_trans_invoice_supp where status = 'Cancel' GROUP BY no_trans) c on c.no_trans = a.no_trans LEFT JOIN
                        (select no_trans,COUNT(status) s_approved from ir_trans_invoice_supp where status = 'Approved' GROUP BY no_trans) d on d.no_trans = a.no_trans) a) b on b.no_trans = a.no_trans) a $where");


                    while($row = mysqli_fetch_array($sql)){
                        if (!empty($row)) {
                            $status = $row['status'];         
                            echo '<tr style="font-size: 12px;text-align:center;">
                            <td style="" value = "'.$row['doc_number'].'">'.$row['doc_number'].'</td>
                            <td style="" value = "'.$row['tgl_penerimaan'].'">'.date("d-M-Y",strtotime($row['tgl_penerimaan'])).'</td>
                            <td style=""value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style="text-align:right;" value = "'.$row['total_amount'].'">'.number_format($row['total_amount'],2).'</td>
                            <td style="" value = "'.$row['status'].'">'.$row['status'].'</td> 
                            <td style="" value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td> 
                            <td style="" value = "'.$row['create_user'].'">'.$row['create_user'].'</td>';
                            echo '<td>';
                            if($row['id'] == 'IR'){
                                echo '<a href="pdf_kontrabon_inv.php?doc_number='.$row['doc_number'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
                            }else{
                                echo '<a href="pdf_transf_inv.php?doc_number='.$row['doc_number'].'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Pdf</i></button></a>';
                            }                
                            echo '</td>';
                            echo'<td hidden value = "'.$row['no_reff'].'">'.$row['no_reff'].'</td>';
                            echo '</tr>';
                        }
                    }?>
                </tbody>                    
            </table>
        </div>

    </div>
</div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodalftrdp" data-target="#mymodalftrdp" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h5 class="modal-title" id="txt_dp"></h5>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tgl_dp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                  <div id="txt_no_reff" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> 
                  <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
              </div>
          </div>
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
        var noftrdp = $(this).closest('tr').find('td:eq(0)').attr('value');
        var cancel_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancelftrdp.php',
            data: {'noftrdp':noftrdp, 'cancel_user':cancel_user},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
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
        var no_document = $(this).closest('tr').find('td:eq(0)').attr('value'); 
        var tgl_doc = $(this).closest('tr').find('td:eq(1)').attr('value'); 
        var supp = $(this).closest('tr').find('td:eq(2)').attr('value'); 
        var no_reff = $(this).closest('tr').find('td:eq(8)').attr('value');                  

        $.ajax({
            type : 'post',
            url : 'ajax_suppinv.php',
            data : {'no_document': no_document},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_dp').html(no_document);
        $('#txt_tgl_dp').html('Receive Date : ' + tgl_doc + '');
        $('#txt_nama_supp').html('Supplier : ' + supp + ''); 
        $('#txt_no_reff').html('No Reff : ' + no_reff + '');                                    
    });

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create_invoice_received.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('trf_fintoacc').onclick = function () {
        location.href = "post_inv_fintoacc.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('appv_acc').onclick = function () {
        location.href = "form_approve_acc.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('trf_acctopch').onclick = function () {
        location.href = "post_inv_acctopch.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('appv_pch').onclick = function () {
        location.href = "form_approve_pch.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('trf_pchtofin').onclick = function () {
        location.href = "post_inv_pchtofin.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('appv_fin').onclick = function () {
        location.href = "form_approve_fin.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('reverse_trf').onclick = function () {
        location.href = "reverse_transfer_inv.php";
    };
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "invoice_received.php";
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
