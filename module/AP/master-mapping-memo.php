<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">MAPPING CATEGORY MEMO</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="master-mapping-memo.php" method="post">        
        <div class="form-row">
            <div class="col-md-4">
            <label for="nama_supp"><b>Category Name</b></label>            
              <select class="form-control selectpicker" name="nama_ctg" id="nama_ctg" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $nama_ctg ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_ctg = isset($_POST['nama_ctg']) ? $_POST['nama_ctg']: null;
                }                 
                $sql = mysqli_query($conn1,"select id_ctg,nm_ctg from master_memo_ctg");
                while ($row = mysqli_fetch_array($sql)) {
                    $isi = $row['id_ctg'];
                    $tampil = $row['nm_ctg'];
                    if($row['id_ctg'] == $_POST['nama_ctg']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$isi.'"'.$isSelected.'">'. $tampil .'</option>';    
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
                $sql = mysqli_query($conn1,"select DISTINCT ditagihkan status, if(ditagihkan = 'Y','Mapping','Not Mapping') stat_name from memo_mapping_v2 where ditagihkan != ''");
                while ($row = mysqli_fetch_array($sql)) {
                    $isi = $row['status'];
                    $tampil = $row['stat_name'];
                    if($row['status'] == $_POST['status']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$isi.'"'.$isSelected.'">'. $tampil .'</option>';    
                }?>
                </select>
                </div>

                <!-- <div class="col-md-4">
            <label for="nama_supp"><b>Sub Category Name</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
                </select>
                </div> -->

            <div class="input-group-append col">                                   
            <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 2;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <!-- <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button> -->

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
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create Bank'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '37'){
    // echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
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
            <th style="text-align: center;vertical-align: middle;width: 10%;">ID Category</th>
            <th style="text-align: center;vertical-align: middle;width: 25%;">Category Name</th>
            <th style="text-align: center;vertical-align: middle;width: 15%;">ID Sub Category</th>
            <th style="text-align: center;vertical-align: middle;width: 30%;">Sub Category Name</th>
            <th style="text-align: center;vertical-align: middle;width: 20%;">Action</th>
        </tr>
    </thead>
   
    <tbody>
    <?php
    $nama_ctg ='';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_ctg = isset($_POST['nama_ctg']) ? $_POST['nama_ctg']: null; 
    $status = isset($_POST['status']) ? $_POST['status']: null;               
    }
    if($nama_ctg == 'ALL' && $status == 'ALL'){
        $sql = mysqli_query($conn2,"select a.*,IF(id_sub_map is null,'N','Y') sts_map from (select a.id_ctg,nm_ctg,id_sub_ctg,upper(nm_sub_ctg) nm_sub_ctg from master_memo_ctg a inner join master_memo_subctg b on b.id_ctg = a.id_ctg order by id_ctg asc) a left join (select id_sub_ctg id_sub_map from memo_mapping_v2 GROUP BY id_sub_ctg) b on b.id_sub_map = a.id_sub_ctg");
    }elseif($nama_ctg == 'ALL' && $status != 'ALL'){
        $sql = mysqli_query($conn2,"select * from (select a.*,IF(id_sub_map is null,'N','Y') sts_map from (select a.id_ctg,nm_ctg,id_sub_ctg,upper(nm_sub_ctg) nm_sub_ctg from master_memo_ctg a inner join master_memo_subctg b on b.id_ctg = a.id_ctg order by id_ctg asc) a left join (select id_sub_ctg id_sub_map from memo_mapping_v2 GROUP BY id_sub_ctg) b on b.id_sub_map = a.id_sub_ctg) a where sts_map = '$status'");
    }elseif($nama_ctg == 'ALL' && $status != 'ALL'){
        $sql = mysqli_query($conn2,"select * from (select a.*,IF(id_sub_map is null,'N','Y') sts_map from (select a.id_ctg,nm_ctg,id_sub_ctg,upper(nm_sub_ctg) nm_sub_ctg from master_memo_ctg a inner join master_memo_subctg b on b.id_ctg = a.id_ctg where a.id_ctg = '$nama_ctg' order by id_ctg asc) a left join (select id_sub_ctg id_sub_map from memo_mapping_v2 GROUP BY id_sub_ctg) b on b.id_sub_map = a.id_sub_ctg) a");
    }else{
        $sql = mysqli_query($conn2,"select * from (select a.*,IF(id_sub_map is null,'N','Y') sts_map from (select a.id_ctg,nm_ctg,id_sub_ctg,upper(nm_sub_ctg) nm_sub_ctg from master_memo_ctg a inner join master_memo_subctg b on b.id_ctg = a.id_ctg where a.id_ctg = '$nama_ctg' order by id_ctg asc) a left join (select id_sub_ctg id_sub_map from memo_mapping_v2 GROUP BY id_sub_ctg) b on b.id_sub_map = a.id_sub_ctg) a where sts_map = '$status'");
    }
$trcol = '';
    while($row = mysqli_fetch_array($sql)){
        $idctg = $row['id_sub_ctg'];
        $query2 = mysqli_query($conn2,"select * from memo_mapping_v2 where id_sub_ctg = '$idctg' GROUP BY id_sub_ctg");

            $cek_data = mysqli_fetch_array($query2);
            $id_map = isset($cek_data['id']) ?  $cek_data['id'] : 0;

            if ($id_map != 0) {
              $trcol = '';
            } else {
              $trcol = 'class="bg-warning"';
            }

        echo '<tr style="font-size:12px;text-align:left;">
            <td '.$trcol.' value = "'.$row['id_ctg'].'">'.$row['id_ctg'].'</td>
            <td '.$trcol.' value = "'.$row['nm_ctg'].'">'.$row['nm_ctg'].'</td>
            <td '.$trcol.' value = "'.$row['id_sub_ctg'].'">'.$row['id_sub_ctg'].'</td>
            <td '.$trcol.' value = "'.$row['nm_sub_ctg'].'">'.$row['nm_sub_ctg'].'</td>';

            // $querys = mysqli_query($conn1,"select Groupp, finance, ap_apprv_lp from userpassword where username = '$user'");
            // $rs = mysqli_fetch_array($querys);
            // $group = $rs['Groupp'];
            // $fin = $rs['finance'];
            // $app = $rs['ap_apprv_lp'];

            echo '<td '.$trcol.' width="100px;" style="font-size:12px;text-align:center;">';
                echo '<button style="border-radius: 6px" type="button" class="btn-xs btn-info" onclick="showdetail('.$row['id_sub_ctg'].')"><i class="fa fa-info-circle" aria-hidden="true"></i> show</button>
                <a a href="edit-mapping-memo.php?id_sub='.base64_encode($row['id_sub_ctg']).'" target="_blank"><button style="border-radius: 6px" type="button" class="btn-xs btn-danger"><i class="fa fa-pencil-square" aria-hidden="true"> Edit</i></button></a>';                                                 
            echo '</td>';
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
        <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" style="color:white">Mapping Info</h4>
        </div>
        <div class="container">
        <div class="row">
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
<script type="text/javascript">
function showdetail(id_sub){
    var id_sub = id_sub;
    $('#mymodal').modal('show');
    
    $.ajax({
    type : 'post',
    url : 'ajaxmappingmemo.php',
    data : {'id_sub': id_sub},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    // $('#txt_nm_ctg').html('Category Name : ' + nm_ctg + '');
    // $('#txt_nm_subctg').html('Sub Category Name : ' + sub_ctg + '');
}
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>


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