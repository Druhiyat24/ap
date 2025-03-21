<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h3 class="text-center">LIST BPB TRANSFERRED</h3>
<div class="box">
    <div class="box header">
    <form id="form-data" action="bpb_received.php" method="post">
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
                $sql = mysqli_query($conn1,"select distinct(nama_status) nama_status from ir_status where status = 'bpb' order by id ASC");
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
                $sql = mysqli_query($conn1,"select distinct(nama_status) nama_status from ir_status where status = 'dec_bpb' order by id ASC");
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

<!--     <?php
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;

            echo '<a target="_blank" href="ekspor_invoice.php?nama_supp='.$nama_supp.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success" style= "margin-top: 30px;margin-bottom:5px"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        ?> -->
            </div>                                                            
    </div>
    <br>
            
        </div>
    </form> 

<?php


        $querys7 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Accept BPB Whs-Acc'");
        $rs7 = mysqli_fetch_array($querys7);
        $id7 = isset($rs7['id']) ? $rs7['id'] : 0;

        $sql_fin = mysqli_query($conn2,"select count(no_transfer) doc_number from (select *,CASE
    WHEN status like '%Approved%' THEN 'Accept From Warehouse To Accounting'
        WHEN status = 'Transfer' THEN 'Transfer From Warehouse To Accounting'
        WHEN status = 'Cancel' THEN 'Cancel From Warehouse To Accounting'
END as keterangan from (select no_transfer,tgl_transfer,no_bpb,tgl_bpb,nama_supp,no_po,curr,SUM(total) total,created_by,created_at,CONCAT(created_by,' (',created_at,')') create_user from ir_trans_bpb GROUP BY no_transfer) a inner join (select no_transfer no_trans,CASE
    WHEN s_post > 0 THEN 'Transfer'
        WHEN s_cancel > 0 and s_approved = 0 THEN 'Cancel'
        WHEN s_cancel = 0 and s_approved > 0 THEN 'Approved'
                WHEN s_cancel > 0 and s_approved > 0 THEN 'Approved Partial'
END as status from (select a.no_transfer,COALESCE(s_post,0) s_post,COALESCE(s_cancel,0) s_cancel, COALESCE(s_approved,0) s_approved from (select no_transfer from ir_trans_bpb GROUP BY no_transfer) a left join
(select no_transfer,COUNT(status) s_post from ir_trans_bpb where status = 'Transfer' GROUP BY no_transfer) b on b.no_transfer = a.no_transfer LEFT JOIN
(select no_transfer,COUNT(status) s_cancel from ir_trans_bpb where status = 'Cancel' GROUP BY no_transfer) c on c.no_transfer = a.no_transfer LEFT JOIN
(select no_transfer,COUNT(status) s_approved from ir_trans_bpb where status = 'Approved' GROUP BY no_transfer) d on d.no_transfer = a.no_transfer) a) b on b.no_trans = a.no_transfer) a where status = 'Transfer'");
        $row_fin = mysqli_fetch_array($sql_fin);
        $count_fin = $row_fin['doc_number'];

        if($id7 == '76'){
        echo '<button style="margin-left: 5px;" id="appv_bpb" type="button" class="btn-success btn-xs"><span class="fa fa-thumbs-up"></span> Accept BPB <span class="badge bg-danger text-white" style="font-size:10px">'.$count_fin.'</span></button>';
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
            <th style="text-align: center;vertical-align: middle;width: 18%;">No Transfer</th>
            <th style="text-align: center;vertical-align: middle;width: 12%;">Tgl Transfer</th>
            <th style="text-align: center;vertical-align: middle;width: 17%;">Supplier</th> 
            <th style="text-align: center;vertical-align: middle;width: 12%;">Total Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 17%;">Status</th>
            <th style="text-align: center;vertical-align: middle;width: 12%;">Keterangan</th>                      
            <th style="text-align: center;vertical-align: middle;width: 12%;">User Create</th>
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
     $where = "where tgl_transfer between '$start_date' and '$end_date'";
    }
    elseif ($nama_supp != 'ALL' and $status == 'ALL' and $keterangan == 'ALL') {
     $where = "where nama_supp = '$nama_supp' and tgl_transfer between '$start_date' and '$end_date'";
    }
    elseif ($nama_supp == 'ALL' and $status != 'ALL' and $keterangan == 'ALL') {
     $where = "where status = '$status' and tgl_transfer between '$start_date' and '$end_date'";
    }
    elseif ($nama_supp == 'ALL' and $status == 'ALL' and $keterangan != 'ALL') {
     $where = "where keterangan = '$keterangan' and tgl_transfer between '$start_date' and '$end_date'";
    } else{
        $where = "where nama_supp = '$nama_supp' and status = '$status' and keterangan = '$keterangan' and tgl_transfer between '$start_date' and '$end_date'";
    }

    $sql = mysqli_query($conn2,"select * from (select *,CASE
    WHEN status like '%Approved%' THEN 'Accept From Warehouse To Accounting'
        WHEN status = 'Transfer' THEN 'Transfer From Warehouse To Accounting'
        WHEN status = 'Cancel' THEN 'Cancel From Warehouse To Accounting'
END as keterangan from (select no_transfer,tgl_transfer,no_bpb,tgl_bpb,nama_supp,no_po,curr,SUM(total) total,created_by,created_at,CONCAT(created_by,' (',created_at,')') create_user from ir_trans_bpb GROUP BY no_transfer) a inner join (select no_transfer no_trans,CASE
    WHEN s_post > 0 THEN 'Transfer'
        WHEN s_cancel > 0 and s_approved = 0 THEN 'Cancel'
        WHEN s_cancel = 0 and s_approved > 0 THEN 'Approved'
                WHEN s_cancel > 0 and s_approved > 0 THEN 'Approved Partial'
END as status from (select a.no_transfer,COALESCE(s_post,0) s_post,COALESCE(s_cancel,0) s_cancel, COALESCE(s_approved,0) s_approved from (select no_transfer from ir_trans_bpb GROUP BY no_transfer) a left join
(select no_transfer,COUNT(status) s_post from ir_trans_bpb where status = 'Transfer' GROUP BY no_transfer) b on b.no_transfer = a.no_transfer LEFT JOIN
(select no_transfer,COUNT(status) s_cancel from ir_trans_bpb where status = 'Cancel' GROUP BY no_transfer) c on c.no_transfer = a.no_transfer LEFT JOIN
(select no_transfer,COUNT(status) s_approved from ir_trans_bpb where status = 'Approved' GROUP BY no_transfer) d on d.no_transfer = a.no_transfer) a) b on b.no_trans = a.no_transfer) a $where");


    while($row = mysqli_fetch_array($sql)){
    if (!empty($row)) {
    $status = $row['status'];         
        echo '<tr style="font-size: 12px;text-align:center;">
            <td style="" value = "'.$row['no_transfer'].'">'.$row['no_transfer'].'</td>
            <td style="" value = "'.$row['tgl_transfer'].'">'.date("d-M-Y",strtotime($row['tgl_transfer'])).'</td>
            <td style=""value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
            <td style="text-align:right;" value = "'.$row['total'].'">'.number_format($row['total'],2).'</td>
            <td style="" value = "'.$row['status'].'">'.$row['status'].'</td> 
            <td style="" value = "'.$row['keterangan'].'">'.$row['keterangan'].'</td> 
            <td style="" value = "'.$row['create_user'].'">'.$row['create_user'].'</td>';
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
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodalftrdp').modal('show');
    var no_document = $(this).closest('tr').find('td:eq(0)').attr('value'); 
    var tgl_doc = $(this).closest('tr').find('td:eq(1)').attr('value'); 
    var supp = $(this).closest('tr').find('td:eq(2)').attr('value');                  

    $.ajax({
    type : 'post',
    url : 'ajax_trfbpb.php',
    data : {'no_document': no_document},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_dp').html(no_document);
    $('#txt_tgl_dp').html('Receive Date : ' + tgl_doc + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');                                    
});

</script>


<script type="text/javascript">
    document.getElementById('appv_bpb').onclick = function () {
    location.href = "form_approve_bpb.php";
};
</script>
<!-- <script type="text/javascript">
    document.getElementById('reverse_trf').onclick = function () {
    location.href = "reverse_transfer_inv.php";
};
</script> -->
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "bpb_received.php";
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
