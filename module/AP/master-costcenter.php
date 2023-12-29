<?php include '../header.php' ?>
<style >
    .modal {
  text-align: center;
  padding: 0!important;
}

.modal:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px;
}

.modal-dialog {
  display: inline-table;
  width: 700px;
  text-align: left;
  vertical-align: middle;
}
</style>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">LIST COST CENTER</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="master-costcenter.php" method="post">        
        <div class="form-row">
           <div class="col-md-3">
            <label for="nama_type"><b>Departement</b></label>            
              <select style="background-color: gray;" class="form-control select2bs4" name="nama_ctg2" id="nama_ctg2" required>
                <option value="ALL">ALL</option>
                <?php
                $nama_ctg2 ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_ctg2 = isset($_POST['nama_ctg2']) ? $_POST['nama_ctg2']: null;
                }                 
                $sql = mysql_query("select id_cc,group1 from b_master_cc where id >= 100 GROUP BY id_cc",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['group1'];
                    $id_ctg2 = $row['id_cc'];
                    if($row['id_cc'] == $_POST['nama_ctg2']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_ctg2.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div> 

                <!-- <div class="col-md-3">
            <label for="nama_group"><b>Category 5</b></label>            
              <select style="background-color: gray;" class="form-control select2bs4" name="nama_ctg5" id="nama_ctg5" required>
                <option value="ALL">ALL</option>
                <?php
                $nama_ctg5 ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_ctg5 = isset($_POST['nama_ctg5']) ? $_POST['nama_ctg5']: null;
                }                 
                $sql = mysql_query("select id_ctg5,ind_name, CONCAT(id_ctg5,' - ',ind_name) as name from master_coa_ctg5 group by id",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['name'];
                    $id_ctg5 = $row['id_ctg5'];
                    if($row['id_ctg5'] == $_POST['nama_ctg5']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_ctg5.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div> -->

                <div class="col-md-3">
            <label for="nama_supp"><b>Status</b></label>            
              <select class="form-control select2bs4" name="Status" id="Status" data-dropup-auto="false" data-live-search="true" >
                <option value="ALL" <?php
                $Status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Status = isset($_POST['Status']) ? $_POST['Status']: null;
                }                 
                    if($Status == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>
                <option value="Active" <?php
                $Status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Status = isset($_POST['Status']) ? $_POST['Status']: null;
                }                 
                    if($Status == 'Active'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Active</option>
                <option value="Deactive" <?php
                $Status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $Status = isset($_POST['Status']) ? $_POST['Status']: null;
                }                 
                    if($Status == 'Deactive'){
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
            <button  type="submit" id="submit" value=" Search " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="reset" value=" Reset " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button>

    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_ctg2 = isset($_POST['nama_ctg2']) ? $_POST['nama_ctg2']: null; 
    $Status = isset($_POST['Status']) ? $_POST['Status']: null; 
    }
    if($nama_ctg2 == 'ALL' and $Status == 'ALL'){
     $where = "where id >= 100";
    }elseif($nama_ctg2 != 'ALL' and $Status == 'ALL'){
     $where = "where id >= 100 and id_cc = '$nama_ctg2'";
    }elseif($nama_ctg2 == 'ALL' and $Status != 'ALL'){
     $where = "where id >= 100 and id_cc = '$nama_ctg2' and status = '$Status'";
    }else{
     $where = "where id >= 100 and id_cc = '$nama_ctg2' and status = '$Status'";  
    }

        echo '<a target="_blank" href="ekspor_master_cc.php?nama_ctg2='.$nama_ctg2.' && Status='.$Status.' && where='.$where.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        
        ?>

            </div>                                                            
    </div>
<br/>

</div>
</form> 
<br/>

<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

    //     if($id == '9'){
    // echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
    //     }else{
    // echo '';
    // }
?>
    </div>             
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

  <form id="formdata2">               
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead style="font-size: 12px;">
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">No Cost Center</th>
            <th style="text-align: center;vertical-align: middle;">Cost Center Name</th>
            <th style="text-align: center;vertical-align: middle;">No Dept</th>
            <th style="text-align: center;vertical-align: middle;">Dept Name</th>
            <th style="text-align: center;vertical-align: middle;">Group 1</th>
            <th style="text-align: center;vertical-align: middle;">Group 2</th>
            <th style="text-align: center;vertical-align: middle;">Profit Center</th>
            <th style="text-align: center;vertical-align: middle;">status</th>
            <th style="text-align: center;vertical-align: middle;">Action</th>
        </tr>
    </thead>
   
    <tbody id="tbl_cf">
    <?php
    $nama_ctg2 ='';
    $nama_ctg5 = '';
    $Status = '';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_ctg2 = isset($_POST['nama_ctg2']) ? $_POST['nama_ctg2']: null; 
    $Status = isset($_POST['Status']) ? $_POST['Status']: null; 
    }
    if($nama_ctg2 == 'ALL' and $Status == 'ALL'){
     $where = "where id >= 100";
    }elseif($nama_ctg2 != 'ALL' and $Status == 'ALL'){
     $where = "where id >= 100 and id_cc = '$nama_ctg2'";
    }elseif($nama_ctg2 == 'ALL' and $Status != 'ALL'){
     $where = "where id >= 100 and id_cc = '$nama_ctg2' and status = '$Status'";
    }else{
     $where = "where id >= 100 and id_cc = '$nama_ctg2' and status = '$Status'";  
    }

    $sql = mysql_query("select id,no_cc,cc_name,id_cc,group1,group2,group21,profit_center,id_pc,status from b_master_cc $where",$conn1);

    while($row = mysqli_fetch_array($sql)){
          $status = $row['status']; 
                 
        echo '<tr style="font-size:11px;text-align:center;">
            <td value = "'.$row['no_cc'].'">'.$row['no_cc'].'</td>
            <td value = "'.$row['cc_name'].'">'.$row['cc_name'].'</td>
            <td value = "'.$row['id_cc'].'">'.$row['id_cc'].'</td>
            <td value = "'.$row['group1'].'">'.$row['group1'].'</td>
            <td value = "'.$row['group2'].'">'.$row['group2'].'</td>
            <td value = "'.$row['group21'].'">'.$row['group21'].'</td>
            <td value = "'.$row['profit_center'].'">'.$row['profit_center'].'</td>
            <td value = "'.$row['status'].'">'.$row['status'].'</td>
            ';

            $querys = mysqli_query($conn1,"select Groupp, finance, ap_apprv_lp from userpassword where username = '$user'");
            $rs = mysqli_fetch_array($querys);
            $group = $rs['Groupp'];
            $fin = $rs['finance'];
            $app = $rs['ap_apprv_lp'];

            echo '<td width="100px;">';
            if($status == 'Deactive' and $group != 'STAFF' and $fin == '1'){
                echo '<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-success"> Active </button>';
            }elseif($status == 'Deactive' and $fin == '1'){
                echo '-';
            }elseif($status == 'Active' and $group != 'STAFF' and $fin == '1'){
                echo '<button style="border-radius: 6px" type="button" id="btnupdate" name="btnupdate"  class="btn-xs btn-danger">Deactive</button>';                
            }elseif($status == 'Active' and $fin == '1') {
                echo '-';                
            } else {
                echo '';                
            }                                     
            echo '</td>';
            echo '</tr>';
}?>
</tbody>                    
</table>
</form>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:400px;" class="modal-dialog modal-md">
        <div style="height: 245px" class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading" style="text-align: center;"><b>Warning!!!</b></h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <div class="col-md-1 mb-3"> 
                    </div>
                <div class="col-md-10 mb-3"> 
                <p for="nama_supp" style="text-align: center;"><b>Password Authentication Required</b></p> 
                <input type="hidden" name="txt_pass" id="txt_pass" value="Cobaaja123">
                <input type="hidden" name="txt_type" id="txt_type" value="">
                <input type="hidden" name="txt_id" id="txt_id" value="">
                 <div class="input-group">
                        <input type="password" id="pass" name="pass" class="form-control">
                        <div class="input-group-append">
    
                            <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                            <span id="mybutton" onclick="change()" class="input-group-text">
    
                                <!-- icon mata bawaan bootstrap  -->
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                    <path fill-rule="evenodd"
                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                </svg>
                            </span>
                        </div>
                    </div>
        </div>
    </div>
                <div class="form-row">
                    <div class="col-md-8">
                    </div>
                <div class="col-md-4">
                <div class="modal-footer">
                    <button type="submit" id="send2" name="send2" class="btn btn-success btn-sm" style="width: 100%;"><span class="fa fa-thumbs-up"></span>
                        Save
                    </button>
                    </div>
                    </div>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>


<div class="form-row">
    <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:450px;" class="modal-dialog modal-md">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">FORM CREATE CASH FLOW</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form3" method="post">
                <div class="form-row">
                <div class="col-md-12 mb-3"> 
                <label for="nama_supp"><b>Type</b></label> 
                <select class="form-control" name="text_type" id="text_type" required>
                <option value="" disabled selected="true">Select Type</option> 
                <?php
                $text_type ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $text_type = isset($_POST['text_type']) ? $_POST['text_type']: null;
                }                 
                $sql = mysql_query("select DISTINCT type from tbl_master_group_cf",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['type'];
                    if($row['type'] == $_POST['text_type']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
        </div>
    </div>
    <div class="form-row">
                <div class="col-md-12 mb-3"> 
                <label for="nama_supp"><b>Group</b></label> 
                <select  class="form-control" name="txt_group" id="txt_group" required>
                    <option value="" disabled selected="true">Select Group</option>
                </select>
        </div>
    </div>
                <div class="form-row">
                 <div class="col-md-12 mb-3"> 
                <label for="nama_supp"><b>Indonesian Name</b></label> 
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_indname" id="txt_indname" autocomplete="off" 
            >
        </div>
        <div class="col-md-12 mb-3"> 
                <label for="nama_supp"><b>English Name</b></label> 
                <input type="text" style="font-size: 14px;font-weight: bold;" class="form-control" name="txt_engname" id="txt_engname" autocomplete="off" 
            >
        </div>   
            </br>
                    <div class="col-md-8">
                    </div>
                <div class="col-md-4">
                <div class="modal-footer">
                    <button type="submit" id="send3" name="send3" class="btn btn-success btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                    </div>
                    </div>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div>               

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
  <script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
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
      //Initialize Select2 Elements
      $('.select2').select2()
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
    });
  </script>


  <script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){ 
        var pass = document.getElementById('pass').value;
        var txt_pass = document.getElementById('txt_pass').value;
        var status = document.getElementById('txt_type').value;
        var id_cf = document.getElementById('txt_id').value;
        var update_user = '<?php echo $user; ?>';   
         
         if (pass == txt_pass) {    
        $.ajax({
            type:'POST',
            url:'update_costcenter.php',
            data: {'status':status, 'id_cf':id_cf, 'update_user':update_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                $('#tbl_cf').html(response);
                // console.log(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 // alert("Data saved successfully");
                window.location.reload(true);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 
        }  
        if(document.getElementById('pass').value == document.getElementById('txt_pass').value){
            alert("Data changed successfully");
            return false;   
        }else{
            alert("Incorrect Password");
        return false;           
        }

 
    });


</script>

 <script type="text/javascript">
    $("#modal-form3").on("click", "#send3", function(){ 
        var txt_group = $('select[name=txt_group] option').filter(':selected').val();
        var txt_indname = document.getElementById('txt_indname').value;
        var txt_engname = document.getElementById('txt_engname').value;
        var create_user = '<?php echo $user; ?>';   
         
         if (txt_group != '' && txt_indname != '' && txt_engname != '') {    
        $.ajax({
            type:'POST',
            url:'insert_cashflow.php',
            data: {'txt_group':txt_group, 'txt_indname':txt_indname, 'txt_engname':txt_engname, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                // $('#tbl_cf').html(response);
                alert(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 // alert("Data saved successfully");
                window.location.reload(true);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 
        }  
        if($('select[name=txt_group] option').filter(':selected').val() == ''){
            alert("Please Select Group");
            return false;   
        }else if(document.getElementById('txt_indname').value == ''){
            alert("Please Input Indonesian Name");
            return false;   
        }else if(document.getElementById('txt_engname').value == ''){
            alert("Please Input English Name");
            return false;   
        }else{
            alert("Data saved successfully");
            return false;   
        }

 
    });


</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<script type="text/javascript">     
    $("#formdata2").on("click", "#btnupdate", function(){ 
    var txt_type = $(this).closest('tr').find('td:eq(7)').attr('value'); 
    var txt_id = $(this).closest('tr').find('td:eq(0)').attr('value');           
    $('#mymodal2').modal('show');
    $('#txt_type').val(txt_type);
    $('#txt_id').val(txt_id);

});

</script>

<!-- <script type="text/javascript">     
    $("#btncreate").on("click", function(){
    var txt_type = "Active"; 
    var txt_id = 1;           
    $('#mymodal3').modal('show');
    $('#txt_type2').val(txt_type);
    $('#txt_id2').val(txt_id);

});

</script> -->

<script>
            // membuat fungsi change
            function change() {
    
                // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
                var x = document.getElementById('pass').type;
    
                //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
                if (x == 'password') {
    
                    //ubah form input password menjadi text
                    document.getElementById('pass').type = 'text';
                    
                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
                }
                else {
    
                    //ubah form input password menjadi text
                    document.getElementById('pass').type = 'password';
    
                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
                }
            }
        </script>

        <script>
            // membuat fungsi change
            function change2() {
    
                // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
                var x = document.getElementById('pass2').type;
    
                //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
                if (x == 'password') {
    
                    //ubah form input password menjadi text
                    document.getElementById('pass2').type = 'text';
                    
                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('mybutton2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
                }
                else {
    
                    //ubah form input password menjadi text
                    document.getElementById('pass2').type = 'password';
    
                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('mybutton2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
                }
            }
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

<script type="text/javascript">
    $('#nama_ctg2').change(function() { 
        var id = $(this).val(); 
        $.ajax({
            type: 'POST', 
            url: 'ubah_ctg5.php', 
            data: {'id':id},
            success: function(response) { 
                $('#nama_ctg5').html(response); 
            }
        });
    });
 
</script>

<script type="text/javascript">
    $('#text_type').change(function() { 
        var id = $(this).val(); 
        $.ajax({
            type: 'POST', 
            url: 'ubah_group2.php', 
            data: {'id':id},
            success: function(response) { 
                $('#txt_group').html(response); 
            }
        });
    });
 
</script>


<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
    var date = $(this).closest('tr').find('td:eq(1)').text();
    var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
    var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
    var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
    var curr = "IDR";

    $.ajax({
    type : 'post',
    url : 'ajax_cashin.php',
    data : {'no_ib': no_ib},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_ib);
    $('#txt_tglbpb').html('Date : ' + date + '');
    $('#txt_no_po').html('Refference : ' + reff + '');
    $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-master-costcenter.php";
};
</script>
<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "master-costcenter.php";
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