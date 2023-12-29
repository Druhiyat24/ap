<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM EDIT MASTER CHART OF ACCOUNT</h2>
<div class="box">
    <div class="box header">

<div class="box footer">   
        <form id="form-simpan">
            </br> 

            </br>

            <div class="col-md-5 mb-2">

                  <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>COA Number </b></label>                  
                    <?php
            $no_coa = base64_decode($_GET['no_coa']);
            $sql = mysqli_query($conn2,"select DISTINCT no_coa from mastercoa_v2 where no_coa = '$no_coa'");
            $row = mysqli_fetch_array($sql);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control" id="no_coa" name="no_coa" value="'.$row['no_coa'].'">'
            ?>
                </div>
                </br>

                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>COA Name </b></label>                  
                    <?php
            $no_coa = base64_decode($_GET['no_coa']);
            $sql = mysqli_query($conn2,"select DISTINCT nama_coa from mastercoa_v2 where no_coa = '$no_coa'");
            $row = mysqli_fetch_array($sql);

            echo'<input type="text" style="font-size: 14px;text-transform:uppercase;" class="form-control" id="nama_coa" name="nama_coa" value="'.$row['nama_coa'].'">'
            ?>
                </div>
                </br>
    
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Category 1</b></label>                  
                     <?php
            $no_coa = base64_decode($_GET['no_coa']);
            $sql = mysqli_query($conn2,"select a.id_ctg1, b.ind_name from mastercoa_v2 a inner join master_coa_ctg1 b on b.id_ctg1 = a.id_ctg1 where a.no_coa = '$no_coa'");
            $row = mysqli_fetch_array($sql);

            echo'<input type="text" readonly style="font-size: 14px;text-transform:uppercase;" class="form-control" id="nama_ctg1" name="nama_ctg1" value="'.$row['ind_name'].'">
            <input type="hidden" readonly style="font-size: 14px;text-transform:uppercase;" class="form-control" id="nama_ctg1_h" name="nama_ctg1_h" value="'.$row['id_ctg1'].'">'
            ?> 
                </div>
                </br>
          
            <div class="input-group">
                <label for="accountid" class="col-form-label" style="width: 150px;" ><b>Category 2</b></label>
                <select class="form-control select2bs4" name="nama_ctg2" id="nama_ctg2" onchange='changeValuectg1(this.value)' required disabled>
            <?php 
            $no_coa = base64_decode($_GET['no_coa']);
            $sql1 = mysqli_query($conn2,"select c.no_coa,a.id_ctg2,a.ind_name as name2,CONCAT(a.id_ctg2,' - ',a.ind_name) as name,b.id_ctg1,b.ind_name from mastercoa_v2 c inner join master_coa_ctg2 a on a.id_ctg2 = c.id_ctg2 inner join master_coa_ctg1 b on b.id_ctg1 = a.id_ctg1 where c.no_coa = '$no_coa' group by c.id");
            $row1 = mysqli_fetch_array($sql1);  
            $id_ctg2 = $row1['id_ctg2'];
            $data = $row1['name2'];
            $ind_name = $row1['ind_name'];
            $id_ctg1 = $row1['id_ctg1'];  
            $isSelected = ' selected="selected"';                      
            if(!empty($no_coa)) {
                echo '<option name="nama_ctg2" value="'.$id_ctg2.'"'.$isSelected.'">'. $data .'</option>';    
                        $jsArray .= "prdName['" . $$id_ctg2 . "'] = {nama_ctg1:'" . addslashes($ind_name) . "',nama_ctg1_h:'".addslashes($id_ctg1)."'};\n";
            }
            else{
                echo '<option value="" disabled selected="true">Select Category 2</option>'; 
            }  ?>
                <?php 
                        $sqlacc = mysqli_query($conn1,"select a.id_ctg2,a.ind_name as name2,CONCAT(a.id_ctg2,' - ',a.ind_name) as name,b.id_ctg1,b.ind_name from master_coa_ctg2 a inner join master_coa_ctg1 b on b.id_ctg1 = a.id_ctg1 where a.id_ctg2 != '$id_ctg2' group by a.id");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['name2'];
                            $id_ctg2 = $row['id_ctg2'];
                            if($row['id_ctg2'] == $_POST['nama_ctg2']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="nama_ctg2" value="'.$id_ctg2.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray .= "prdName['" . $row['id_ctg2'] . "'] = {nama_ctg1:'" . addslashes($row['ind_name']) . "',nama_ctg1_h:'".addslashes($row['id_ctg1'])."'};\n";
                        }
                        ?>
                </select>  
            </div>
            </br>
            <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Category 3</b></label>
                    <?php
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql = mysqli_query($conn2,"select a.id_ctg3, b.ind_name from mastercoa_v2 a inner join master_coa_ctg3 b on b.id_ctg3 = a.id_ctg3 where a.no_coa = '$no_coa'");
                    $row = mysqli_fetch_array($sql);

                    echo'<input type="text" readonly style="font-size: 14px;text-transform:uppercase;" class="form-control" id="nama_ctg3" name="nama_ctg3" value="'.$row['ind_name'].'">
                    <input type="hidden" readonly style="font-size: 14px;text-transform:uppercase;" class="form-control" id="nama_ctg3_h" name="nama_ctg3_h" value="'.$row['id_ctg3'].'">'
                    ?>                   
                </div>
                </br>
            <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Category 4</b></label>                  
                    <?php
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql = mysqli_query($conn2,"select a.id_ctg4, b.ind_name from mastercoa_v2 a inner join master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 where a.no_coa = '$no_coa'");
                    $row = mysqli_fetch_array($sql);

                    echo'<input type="text" readonly style="font-size: 14px;text-transform:uppercase;" class="form-control" id="nama_ctg4" name="nama_ctg4" value="'.$row['ind_name'].'">
                    <input type="hidden" readonly style="font-size: 14px;text-transform:uppercase;" class="form-control" id="nama_ctg4_h" name="nama_ctg4_h" value="'.$row['id_ctg4'].'">'
                    ?>  
                </div>
                </br>
            <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Category 5</b></label>         
                    <select class="form-control select2bs4" name="nama_ctg5" id="nama_ctg5" onchange='changeValuectg34(this.value)' required disabled>
                    <?php
                     $no_coa = base64_decode($_GET['no_coa']); 
                        $sql2 = mysqli_query($conn2,"select id_ctg2 from mastercoa_v2  where no_coa = '$no_coa'");
                        $row2 = mysqli_fetch_array($sql2);
                        $idctg2 = $row2['id_ctg2'];

                         if(!empty($no_coa)) {
                                echo '';
                        }
                        else{
                                echo '<option value="" disabled selected="true">Select Category 5</option> '; 
                        }

                        $sqlacc = mysqli_query($conn1,"select a.ind_name name5,a.id_ctg5,CONCAT(a.id_ctg5,'.') as no_coa,CONCAT(a.id_ctg5,' - ',a.ind_name) as name,b.id_ctg3,b.ind_name as name3,c.id_ctg4,c.ind_name as name4 from master_coa_ctg5 a inner join master_coa_ctg3 b on b.id_ctg3 = a.id_ctg3 inner join master_coa_ctg4 c on c.id_ctg4 = a.id_ctg4 where a.id_ctg2 = '$idctg2' group by a.id");
                        $jsArray2 = "var prdName2 = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['name5'];
                            $id_ctg5 = $row['id_ctg5'];
                            if($row['id_ctg5'] == $_POST['nama_ctg5']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="nama_ctg2" value="'.$id_ctg5.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray2 .= "prdName2['" . $row['id_ctg5'] . "'] = {nama_ctg3:'" . addslashes($row['name3']) . "',nama_ctg3_h:'".addslashes($row['id_ctg3'])."',nama_ctg4:'" . addslashes($row['name4']) . "',nama_ctg4_h:'".addslashes($row['id_ctg4'])."'};\n";
                        }
                        ?> 
                </select>        
                </div>
                </br>
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b><u>CASH FLOW</u></b></label>                 
                </div>
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;padding-left: 15px;"><b>Direct Debit</b></label>          
                    <?php 
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql2 = mysqli_query($conn2,"select id_direct_debit from mastercoa_v2  where no_coa = '$no_coa'");
                        $row2 = mysqli_fetch_array($sql2);
                        $id_direct_debit = $row2['id_direct_debit'];

                    $sql1 = mysqli_query($conn2,"select id,ind_name from tbl_master_cashflow where id_group <= 3 and status = 'Active' and id = '$id_direct_debit'");
                    $row1 = mysqli_fetch_array($sql1);  
                    $data2 = isset($row1['ind_name']) ? $row1['ind_name'] : null;
                    $id2 = isset($row1['id']) ? $row1['id'] : null;
                    $isSelected = ' selected="selected"';   
                                      
                    if($id_direct_debit == '-' || $id_direct_debit == null) {
                        echo '<select style="background-color: gray;" class="form-control select2bs4" name="txt_dirdebit" id="txt_dirdebit"  disabled>
                        <option value="-" disabled selected="true">Select cash flow</option>'; 
                    }else{
                        echo '<select style="background-color: gray;" class="form-control select2bs4" name="txt_dirdebit" id="txt_dirdebit"  required>
                        <option value="'.$id2.'"'.$isSelected.'">'. $data2 .'</option>';
                    }  

                $txt_dirdebit ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $txt_dirdebit = isset($_POST['txt_dirdebit']) ? $_POST['txt_dirdebit']: null;
                }                 
                $sql = mysql_query("select id,ind_name from tbl_master_cashflow where id_group <= 3 and status = 'Active' and id != '$txt_dirdebit' ",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['ind_name'];
                    $id = $row['id'];
                    if($row['id'] == $_POST['txt_dirdebit']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                <div style="padding-left: 50px;" class="custom-control custom-checkbox col-sm-2">
                    <?php 
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql2 = mysqli_query($conn2,"select id_direct_debit from mastercoa_v2  where no_coa = '$no_coa'");
                        $row2 = mysqli_fetch_array($sql2);
                        $id_direct_debit = $row2['id_direct_debit'];

                    $sql1 = mysqli_query($conn2,"select id,ind_name from tbl_master_cashflow where id_group <= 3 and status = 'Active' and id = '$id_direct_debit'");
                    $row1 = mysqli_fetch_array($sql1);  
                    $data2 = isset($row1['ind_name']) ? $row1['ind_name'] : null;
                    $id2 = isset($row1['id']) ? $row1['id'] : null;
                    $isSelected = ' selected="selected"';   
                                      
                    if($id_direct_debit == '-' || $id_direct_debit == null) {
                        echo '<input class="custom-control-input" type="checkbox" id="check_dirdebit" name="check_dirdebit" onclick="Change_dirdebit()" checked>'; 
                    }else{
                        echo '<input class="custom-control-input" type="checkbox" id="check_dirdebit" name="check_dirdebit" onclick="Change_dirdebit()">';
                    }
                    ?>
                <label for="check_dirdebit" class="custom-control-label"><i class="fa fa-check" aria-hidden="true"></i></label>
                </div>        
                </div>
                </br>
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;padding-left: 15px;"><b>Direct Credit</b></label>                  
                    <?php
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql2 = mysqli_query($conn2,"select id_direct_credit from mastercoa_v2  where no_coa = '$no_coa'");
                        $row2 = mysqli_fetch_array($sql2);
                        $id_direct_credit = $row2['id_direct_credit'];

                    $sql1 = mysqli_query($conn2,"select id,ind_name from tbl_master_cashflow where id_group <= 3 and status = 'Active' and id = '$id_direct_credit'");
                    $row1 = mysqli_fetch_array($sql1);  
                    $data2 = isset($row1['ind_name']) ? $row1['ind_name'] : null;
                    $id2 = isset($row1['id']) ? $row1['id'] : null;
                    $isSelected = ' selected="selected"';   
                                      
                    if($id_direct_credit == '-' || $id_direct_credit == null) {
                        echo '<select style="background-color: gray;" class="form-control select2bs4" name="txt_dircredit" id="txt_dircredit"  disabled>
                        <option value="-" disabled selected="true">Select cash flow</option>'; 
                    }else{
                        echo '<select style="background-color: gray;" class="form-control select2bs4" name="txt_dircredit" id="txt_dircredit"  required>
                        <option value="'.$id2.'"'.$isSelected.'">'. $data2 .'</option>';
                    }

                $txt_dircredit ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $txt_dircredit = isset($_POST['txt_dircredit']) ? $_POST['txt_dircredit']: null;
                }                 
                $sql = mysql_query("select id,ind_name from tbl_master_cashflow where id_group <= 3 and status = 'Active' and id = '$id_direct_credit' ",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['ind_name'];
                    $id = $row['id'];
                    if($row['id'] == $_POST['txt_dircredit']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                <div style="padding-left: 50px;" class="custom-control custom-checkbox col-sm-2">
                    <?php
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql2 = mysqli_query($conn2,"select id_direct_credit from mastercoa_v2  where no_coa = '$no_coa'");
                        $row2 = mysqli_fetch_array($sql2);
                        $id_direct_credit = $row2['id_direct_credit'];

                    $sql1 = mysqli_query($conn2,"select id,ind_name from tbl_master_cashflow where id_group <= 3 and status = 'Active' and id = '$id_direct_credit'");
                    $row1 = mysqli_fetch_array($sql1);  
                    $data2 = isset($row1['ind_name']) ? $row1['ind_name'] : null;
                    $id2 = isset($row1['id']) ? $row1['id'] : null;
                    $isSelected = ' selected="selected"';   
                                      
                    if($id_direct_credit == '-' || $id_direct_credit == null) {
                        echo '<input class="custom-control-input" type="checkbox" id="check_dircredit" name="check_dircredit" onclick="Change_dircredit()" checked>'; 
                    }else{
                        echo '<input class="custom-control-input" type="checkbox" id="check_dircredit" name="check_dircredit" onclick="Change_dircredit()">';
                    }
                    ?>
                <label for="check_dircredit" class="custom-control-label"><i class="fa fa-check" aria-hidden="true"></i></label>
                </div>         
                </div>
                </br>                       
                                    
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;padding-left: 15px;"><b>Indirect</b></label>         
                    <?php
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql2 = mysqli_query($conn2,"select id_indirect from mastercoa_v2  where no_coa = '$no_coa'");
                        $row2 = mysqli_fetch_array($sql2);
                        $id_indirect = $row2['id_indirect'];

                    $sql1 = mysqli_query($conn2,"select id,ind_name from tbl_master_cashflow where id_group >= 4 and status = 'Active' and id = '$id_indirect'");
                    $row1 = mysqli_fetch_array($sql1);  
                    $data2 = isset($row1['ind_name']) ? $row1['ind_name'] : null;
                    $id2 = isset($row1['id']) ? $row1['id'] : null;
                    $isSelected = ' selected="selected"';   
                                      
                    if($id_indirect == '-' || $id_indirect == null) {
                        echo '<select style="background-color: gray;" class="form-control select2bs4" name="txt_indirect" id="txt_indirect"  disabled>
                        <option value="-" disabled selected="true">Select cash flow</option>'; 
                    }else{
                        echo '<select style="background-color: gray;" class="form-control select2bs4" name="txt_indirect" id="txt_indirect"  required>
                        <option value="'.$id2.'"'.$isSelected.'">'. $data2 .'</option>';
                    }

                $txt_indirect ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $txt_indirect = isset($_POST['txt_indirect']) ? $_POST['txt_indirect']: null;
                }                 
                $sql = mysql_query("select id,ind_name from tbl_master_cashflow where id_group >= 4 and status = 'Active' and id != '$id_indirect'",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['ind_name'];
                    $id = $row['id'];
                    if($row['id'] == $_POST['txt_indirect']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                <div style="padding-left: 50px;" class="custom-control custom-checkbox col-sm-2">
                 <?php
                    $no_coa = base64_decode($_GET['no_coa']);
                    $sql2 = mysqli_query($conn2,"select id_indirect from mastercoa_v2  where no_coa = '$no_coa'");
                        $row2 = mysqli_fetch_array($sql2);
                        $id_indirect = $row2['id_indirect'];

                    $sql1 = mysqli_query($conn2,"select id,ind_name from tbl_master_cashflow where id_group >= 4 and status = 'Active' and id = '$id_indirect'");
                    $row1 = mysqli_fetch_array($sql1);  
                    $data2 = isset($row1['ind_name']) ? $row1['ind_name'] : null;
                    $id2 = isset($row1['id']) ? $row1['id'] : null;
                    $isSelected = ' selected="selected"';   
                                      
                    if($id_indirect == '-' || $id_indirect == null) {
                        echo ' <input class="custom-control-input" type="checkbox" id="check_indirect" name="check_indirect" onclick="Change_indirect()" checked>'; 
                    }else{
                        echo ' <input class="custom-control-input" type="checkbox" id="check_indirect" name="check_indirect" onclick="Change_indirect()">';
                    }
                    ?>   
                <label for="check_indirect" class="custom-control-label"><i class="fa fa-check" aria-hidden="true"></i></label>
                </div>        
                </div>
                </br>
            </div>

           <div class="form-row col">
            <div class="col-md-3 mb-3">                               
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='master-coa.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalkbon" data-target="#mymodalkbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
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
<!--           <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
<!--           <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
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
    $('#mytable').dataTable();
    
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

<!--<script type="text/javascript"> 
    $("#mytable").on("click", "#delbutton", function() {
    var sub = $(this).closest('tr').find('td:eq(4)').attr('data-subtotal');
    var pajak = $(this).closest('tr').find('td:eq(5)').attr('data-tax');
    var total = $(this).closest('tr').find('td:eq(6)').attr('data-total');        
    var sub_val = document.getElementById("subtotal").value.replace(/[^0-9.]/g, '');
    var sub_tax = document.getElementById("pajak").value.replace(/[^0-9.]/g, '');
    var sub_total = document.getElementById("total").value.replace(/[^0-9.]/g, '');
    var min_sub = 0;
    var min_tax = 0;
    var min_total = 0;
    min_sub = sub_val - sub;
    min_tax = sub_tax - pajak;
    min_total = sub_total - total;
    $('#subtotal').val(formatMoney(min_sub));
    $('#pajak').val(formatMoney(min_tax));
    $('#total').val(formatMoney(min_total));                      
    $(this).closest("tr").remove();

});
</script>-->

<script type="text/javascript">
function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
  try {
    decimalCount = Math.abs(decimalCount);
    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

    const negativeSign = amount < 0 ? "-" : "";

    let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
    let j = (i.length > 3) ? i.length % 3 : 0;

    return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
  } catch (e) {
    console.log(e)
  }
};
//     $("input[type=checkbox]").change(function(){
//     var no_pay = '';
//     var tgl_pay = '';
//     var no_kbon = '';
//     var tgl_kbon = '';
//     var valuta = '';
//     var total = 0;
//     var dibayar = 0;
//     var sisa = 0;
//     var buka = '';

//     $("input[type=checkbox]:checked").each(function () {     
//     var select_rate = document.getElementById('rate');   
//     var pay = $(this).closest('tr').find('td:eq(1)').attr('dataout1');
//     var tglpay = $(this).closest('tr').find('td:eq(2)').attr('dataout2');
//     var kbon = $(this).closest('tr').find('td:eq(3)').attr('dataout3');
//     var tglkbon = $(this).closest('tr').find('td:eq(4)').attr('dataout4');
//     var val = $(this).closest('tr').find('td:eq(8)').attr('dataout6');
//     var ttl = parseFloat($(this).closest('tr').find('td:eq(6)').attr('dataout5'),10) || 0;
//     var dbayar = parseFloat($(this).closest('tr').find('td:eq(9)').attr('dataout7'),10) || 0; 
//     //  select_rate.prop('disabled', false);  
//     // buka = select_rate.prop('disabled', false);     
              
//     no_pay = pay;
//     tgl_pay = tglpay; 
//     no_kbon = kbon;
//     tgl_kbon = tglkbon;
//     valuta = val;
//     total = ttl;
//     dibayar += dbayar;

//     if(valuta == 'IDR'){
//             select_rate.disabled = true;
//         }else{
//             select_rate.disabled = false;
//         }
//     });
//     // $("#no_payment").val(no_pay);
//     // $("#tgl_payment").val(tgl_pay);
//     // $("#no_kbon").val(no_kbon);
//     // $("#tgl_kbon").val(tgl_kbon);
//     $("#valuta1").val(valuta);
//     $("#total").val(formatMoney(total));
//     $("#dibayar").val(formatMoney(dibayar));
//     $("#nominal_h").val(dibayar);
//     // $("#outstanding").val(formatMoney(sisa));
//     $("#nominal").val(formatMoney(dibayar));
//     // $("#select").val("1");                    
// });        
</script>

<script type="text/javascript">
    $("input[name=sob]").keyup(function(){
    var ttl_jml = '';
    var valu = '';
    $("input[type=text]").each(function () {         
    var sob = document.getElementById('sob').value;
    var urut = document.getElementById('no_urut').value;
    var bulan = document.getElementById('bulan').value;
    var tahun = document.getElementById('tahun').value;
    valu = 'MB'+'/'+sob+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;

    
    });
   $("#no_doc").val(valu);


    });
</script>

<script type="text/javascript">
    $("input[name=rate]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta1').value;
    valu = val;
    rat = rate;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#rate_h").val(formatMoney(rat));

    });
</script>

<script>
    function Change_dirdebit(){ 
    //
    ttl_jml = "-";
    // var id = document.getElementById('check_dirdebit').value; 
    // console.log(id);  
    if ($('[name="check_dirdebit"]').is(':checked')) { 
            $('#txt_dirdebit').prop('disabled', true);
             var id = $('select[name=nama_ctg2] option').filter(':selected').val();; 
        $.ajax({
            type: 'POST', 
            url: 'ubah_cf.php', 
            data: {'id':id},
            success: function(response) { 
                $('#txt_dirdebit').html(response); 
            }
        });         
  
    } else {        
             $('#txt_dirdebit').prop('disabled', false);
             
    }
}
</script>

<script>
    function Change_dircredit(){ 
    //
    ttl_jml = "-";  
    if ($('[name="check_dircredit"]').is(':checked')) {          
            $('#txt_dircredit').prop('disabled', true);
             var id = $('select[name=nama_ctg2] option').filter(':selected').val();; 
        $.ajax({
            type: 'POST', 
            url: 'ubah_cf.php', 
            data: {'id':id},
            success: function(response) { 
                $('#txt_dircredit').html(response); 
            }
        });
    } else {        
             $('#txt_dircredit').prop('disabled', false);
    }
}
</script>

<script>
    function Change_indirect(){ 
    //
    ttl_jml = "-";  
    if ($('[name="check_indirect"]').is(':checked')) {          
            $('#txt_indirect').prop('disabled', true);
             var id = $('select[name=nama_ctg2] option').filter(':selected').val();; 
        $.ajax({
            type: 'POST', 
            url: 'ubah_cf2.php', 
            data: {'id':id},
            success: function(response) { 
                $('#txt_indirect').html(response); 
            }
        });
    } else {        
             $('#txt_indirect').prop('disabled', false);
    }
}
</script>

<script type="text/javascript"> 
<?php echo $jsArray; ?>
function changeValuectg1(id){
    document.getElementById('nama_ctg1').value = prdName[id].nama_ctg1;
    document.getElementById('nama_ctg1_h').value = prdName[id].nama_ctg1_h;
    document.getElementById('nama_ctg3').value = "";
    document.getElementById('nama_ctg3_h').value = "";
    document.getElementById('nama_ctg4').value = "";
    document.getElementById('nama_ctg4_h').value = "";
    document.getElementById('no_coa').value = "";
};
</script>

<script type="text/javascript"> 
<?php echo $jsArray2; ?>
function changeValuectg34(id){
    document.getElementById('nama_ctg3').value = prdName2[id].nama_ctg3;
    document.getElementById('nama_ctg3_h').value = prdName2[id].nama_ctg3_h;
    document.getElementById('nama_ctg4').value = prdName2[id].nama_ctg4;
    document.getElementById('nama_ctg4_h').value = prdName2[id].nama_ctg4_h;
    // document.getElementById('no_coa').value = prdName2[id].no_coa;
};
</script>

<script type="text/javascript">
    $('#nama_ctg2').change(function() { 
        var id = $(this).val(); 
        $.ajax({
            type: 'POST', 
            url: 'ubah_ctg5_c.php', 
            data: {'id':id},
            success: function(response) { 
                $('#nama_ctg5').html(response); 
            }
        });
    });
 
</script>

<script type="text/javascript">
    $('#nama_ctg5').change(function() { 
        var id = $(this).val(); 
        $.ajax({
            type: 'POST', 
            url: 'ubah_nocoa.php', 
            data: {'id':id},
            success: function(response) { 
                $('#no_coa').val(response); 
                // alert(response);
            }
        });
    });
 
</script>

<script type="text/javascript">
// get all number fields
var numInputs = document.querySelectorAll('input[type="number"]');

// Loop through the collection and call addListener on each element
Array.prototype.forEach.call(numInputs, addListener); 


function addListener(elm,index){
  elm.setAttribute('min', 1);  // set the min attribute on each field
  
  elm.addEventListener('keypress', function(e){  // add listener to each field 
     var key = !isNaN(e.charCode) ? e.charCode : e.keyCode;
     str = String.fromCharCode(key); 
    if (str.localeCompare('-') === 0){
       event.preventDefault();
    }
    
  });
  
}
</script>


<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_coa = document.getElementById('no_coa').value;        
        var nama_coa = document.getElementById('nama_coa').value;
        var nama_ctg1_h = document.getElementById('nama_ctg1_h').value;        
        var nama_ctg2 = $('select[name=nama_ctg2] option').filter(':selected').val();
        var nama_ctg3_h = document.getElementById('nama_ctg3_h').value;        
        var nama_ctg4_h = document.getElementById('nama_ctg4_h').value;
        var nama_ctg5 = $('select[name=nama_ctg5] option').filter(':selected').val();
        var txt_dirdebit = $('select[name=txt_dirdebit] option').filter(':selected').val();
        var txt_dircredit = $('select[name=txt_dircredit] option').filter(':selected').val();
        var txt_indirect = $('select[name=txt_indirect] option').filter(':selected').val();
        var create_user = '<?php echo $user ?>';

 
        // if( no_coa != '' && nama_coa != '' && nama_ctg2 != '' && nama_ctg5 != '' && $('[name="check_dirdebit"]').is(':checked') && $('[name="check_dircredit"]').is(':checked') && $('[name="check_indirect"]').is(':checked')){  
        // $.ajax({
        //     type:'POST',
        //     url:'insertmastercoa.php',
        //     data: {'no_coa':no_coa, 'nama_coa':nama_coa, 'nama_ctg1_h':nama_ctg1_h,'nama_ctg2':nama_ctg2, 'nama_ctg3_h':nama_ctg3_h, 'nama_ctg4_h':nama_ctg4_h,'nama_ctg5':nama_ctg5, 'txt_dirdebit':txt_dirdebit, 'txt_dircredit':txt_dircredit, 'txt_indirect':txt_indirect, 'create_user':create_user},
        //     cache: 'false',
        //     close: function(e){
        //         e.preventDefault();
        //     },
        //     success: function(response){
        //         // alert(response);
        //         // alert(response);
        //         if (response == '') {
        //             alert("Successful Save");
        //             window.location = 'master-coa.php';
        //         }else{
        //             alert("COA Number Already Exist");
        //             document.getElementById('no_coa').focus();
        //         }
        //         },
        //     error: function (xhr, ajaxOptions, thrownError) {
        //         console.log(xhr);
        //         alert(xhr);
        //     }
        // });
        // }
                  
        if($('select[name=nama_ctg2] option').filter(':selected').val() == ''){
            document.getElementById('nama_ctg2').focus();
            alert("Please Select Category 2");
            return false;   
        }else if($('select[name=nama_ctg5] option').filter(':selected').val() == ''){
            document.getElementById('nama_ctg5').focus();
            alert("Please Select Category 5");
            return false;   
        } else if(document.getElementById('nama_coa').value == ''){
        document.getElementById('nama_coa').focus();
        alert("please input Coa name");
        } else if(document.getElementById('no_coa').value == ''){
        document.getElementById('no_coa').focus();
        alert("Coa Number Can't be empty");
        }else if($('[name="check_dirdebit"]').prop('checked') == false && $('select[name=txt_dirdebit] option').filter(':selected').val() == '-'){
        document.getElementById('txt_dirdebit').focus();
        alert("Direct Debit Can't be empty");
        }else if($('[name="check_dircredit"]').prop('checked') == false && $('select[name=txt_dircredit] option').filter(':selected').val() == '-'){
        document.getElementById('txt_dircredit').focus();
        alert("Direct Credit Can't be empty");
        }else if($('[name="check_indirect"]').prop('checked') == false && $('select[name=txt_indirect] option').filter(':selected').val() == '-'){
        document.getElementById('txt_indirect').focus();
        alert("Indirect Can't be empty");
        } else{
            $.ajax({
            type:'POST',
            url:'save_edit_mastercoa2.php',
            data: {'no_coa':no_coa, 'nama_coa':nama_coa, 'nama_ctg1_h':nama_ctg1_h,'nama_ctg2':nama_ctg2, 'nama_ctg3_h':nama_ctg3_h, 'nama_ctg4_h':nama_ctg4_h,'nama_ctg5':nama_ctg5, 'txt_dirdebit':txt_dirdebit, 'txt_dircredit':txt_dircredit, 'txt_indirect':txt_indirect, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                // alert(response);
                // alert(response);
                if (response == '') {
                    alert("Edit Successfuly");
                    window.location = 'master-coa.php';
                }else{
                    alert("error Edit!");
                }
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        }           
    });
</script>
<!-- if ($('[name="check_dircredit"]').is(':checked')) -->

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalkbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('dataout1');
    var tgl_kbon = $(this).closest('tr').find('td:eq(2)').text();
    var supp = $(this).closest('tr').find('td:eq(5)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(8)').text();
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(11)').attr('value');
    var status = $(this).closest('tr').find('td:eq(10)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();                

    $.ajax({
    type : 'post',
    url : 'ajaxf_kbon.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_kbon);
    $('#txt_tgl_kbon').html('List Payment Date : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Currency : ' + tgl_tempo + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                               
});

</script>
<script type="text/javascript"> 
<?php echo $jsArray; ?>
function changeValueACC(id){
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
};
</script>

<script type="text/javascript">
    $(document).ready(function(){
        // Format mata uang.
        $( '.uang' ).mask('0.000.000.000', {reverse: true});
    })
</script>

<!--<script>
    $(document).ready(){
        $('#mybpb').click(function){
            $('#mymodal').modal('show');
        }
    }
</script>-->
<!--<script>
$(document).ready(function() {   
    $("#send").click(function(e) {
        e.preventDefault();
        var datas= $(this).children("option:selected").val();
        $.ajax({
            type:"post",
            url:"cek.php",
            dataType: "json",
            data: {datas:datas},
            success: function(data){
                alert("Success: " + data);
            }
        });               
    });
</script>-->
<!--<script>
$(document).ready(function (){
    $("select.selectpicker").change(function(){
        var selectedbpb = $(this).children("option:selected").val();
        document.getElementById("bpbvalue").value = selectedbpb;             
    });
});
</script>-->
<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
