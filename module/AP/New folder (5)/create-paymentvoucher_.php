<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM PAYMENT VOUCHER</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>No Payment Voucher</b></label>
                <?php
            $sql = mysqli_query($conn2,"select max(no_payment) from list_payment");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_payment)'];
            $urutan = (int) substr($kodepay, 12, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "PV/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            ?>
        </div>

            <div class="col-md-3 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Payment Voucher Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tgl_active'])) {
                echo $_POST['tgl_active'];
            }
            else{
                echo date("d-m-Y");
            } ?>" autocomplete='off'>
            </div>

            
            <div class="col-md-4 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="Unrealize" disabled selected="true">Select Supplier</option>                                                 
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

        



                                        
    </div>
</br>

<div class="form-row">
            <!-- <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Description</b></label>
                <?php
            $no_doc = isset($_POST['no_doc']) ? $_POST['no_doc']: null;
            $sql = mysqli_query($conn1,"select group_concat(ket) as ket from tbl_ref where ref_doc = '$no_doc' ");
            $row = mysqli_fetch_array($sql);
            $value = isset($row['ket']) ? $row['ket'] : null;
            if (!empty($value)) {
            echo '<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="matauang" name="matauang" value="'.$value.'">';   
            } else {
            echo '<input type="text" readonly class="form-control-plaintext" id="matauang" name="matauang" value="">';
            }
            ?>  
        </div> -->

        <div class="col-md-2 mb-3">            
            <label for="total" class="col-form-label" style="width: 200px;"><b>For Payment</b></label>
                <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="btn3" id="btn3" data-target="#mymodal3" data-toggle="modal" value="Select"> 
            </div>

            <div class="col-md-2 mb-3">            
            <label for="total" class="col-form-label" style="width: 200px;"><b>Supporting Doc</b></label>
                <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="btn2" id="btn2" data-target="#mymodal2" data-toggle="modal" value="Select"> 
            </div>
            
          <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Purpose</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="<?php             
            if(!empty($_POST['pesan'])) {
                echo $_POST['pesan'];
            }
            else{
                echo '';
            } ?>" placeholder="Purpose..." ></textarea>
        </div>

        <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="<?php             
            if(!empty($_POST['pesan'])) {
                echo $_POST['pesan'];
            }
            else{
                echo '';
            } ?>" placeholder="Description..." ></textarea>
        </div>
                                
 </div>
</br>



<div class="form-row">
    <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose For Payment and Supporting Document</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form3" method="post">
                <div class="form-row">
                    <div class="col-6">
                        <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">Cek</th>
                            <th style="width:100px;display: none;">Supplier</th>
                            <th style="width:150px;">Supporting Doc</th>                                                    
                        </tr>
                    </thead>

            <tbody>
                    <?php

            $querys = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '1' ");


            while($row1 = mysqli_fetch_array($querys)){
                
                    echo '<tr>  
                    <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;display:none;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" disabled>
                            </td>
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>  
                    <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;display:none;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" >
                            </td> 
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" >
                            </td>                                                                                                 
                        </tr>';
                    ?> 
                </tbody>
            </table>
        </div>

        <div class="col-6">
            <div class="col-md-12 mb-3">            
            <label for="tanggal"><b>Ke</b></label>          
            <input type="text" style="font-size: 16px;text-align: center;" name="ke_berapa" id="ke_berapa" class="form-control" 
            value="">
            </div>
            <div class="col-md-12 mb-3">            
            <label for="tanggal"><b>Dari</b></label>          
            <input type="text" style="font-size: 16px;text-align: center;" name="dari_berapa" id="dari_berapa" class="form-control" 
            value="">
            </div>
            <div class="col-md-12 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Dari Account</b></label>            
              <select class="form-control selectpicker" name="dari_akun" id="dari_akun" data-dropup-auto="false" data-live-search="true">
                <option value="Unrealize" disabled selected="true">Select Account</option>                                                 
                <?php
                $dari_akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $dari_akun = isset($_POST['dari_akun']) ? $_POST['dari_akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select name from tbl_akun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['name'];
                    if($row['name'] == $_POST['dari_akun']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>

                <div class="col-md-12 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Ke Account</b></label>            
              <select class="form-control selectpicker" name="ke_akun" id="ke_akun" data-dropup-auto="false" data-live-search="true">
                <option value="Unrealize" disabled selected="true">Select Account</option>                                                 
                <?php
                $ke_akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ke_akun = isset($_POST['ke_akun']) ? $_POST['ke_akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select name from tbl_akun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['name'];
                    if($row['name'] == $_POST['ke_akun']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>
                <div class="col-md-12 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Keterangan</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="keter" id="keter" value="<?php             
            if(!empty($_POST['keter'])) {
                echo $_POST['keter'];
            }
            else{
                echo '';
            } ?>" placeholder="..." required></textarea>
        </div>

        <div class="col-md-12 mb-3">
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Supporting Document</b></label>
        <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">Cek</th>
                            <th style="width:150px;">Supporting Doc</th>                                                    
                        </tr>
                    </thead>

            <tbody>
                    <?php

            $querys = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '2' ");


            while($row1 = mysqli_fetch_array($querys)){
                
                    echo '<tr>  
                    <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>  
                    <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" >
                            </td>                                                                                                 
                        </tr>';
                    ?> 
                </tbody>
            </table> 
        </div>

            <div class="col-md-12 mb-3">
                <label for="pajak" class="col-form-label" style="width: 150px;"><b>Type</b></label>
                <select class="form-control selectpicker" name="carabayar" id="carabayar" data-live-search="true">
                    <option value="TUNAI">TUNAI</option>                       
                    <option value="Cek/Giro">Cek/Giro</option>  
        
                </select>        
            </div>

        </div>


              
    </div>
  
                <div class="modal-footer">
                    <button type="submit" id="send3" name="send3" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div>      

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose For Payment and Supporting Document</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <table id="doc_support" class="table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
        <tr><th class="text-center">Cek</th>
            <th class="text-center">Supporting Doc</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><input type="checkbox" id="select" name="select[]" value=""></td>
            <td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="PO/SO" disabled>
                </div>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" id="select" name="select[]" value=""></td>
            <td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="Tagihan" disabled>
                </div>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" id="select" name="select[]" value=""></td>
            <td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="LC" disabled>
                </div>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" id="select" name="select[]" value=""></td>
            <td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="" value="">
                </div>
            </td>
        </tr>
    </tbody>                  
            </table> 
              
    </div>
    <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
  
                <div class="modal-footer">
                    <button type="submit" id="send2" name="send2" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div>            
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
        <tr><th class="text-center">-</th>
            <th class="text-center">Account</th>
            <th class="text-center">To Account</th>
            <th class="text-center">Date</th>
            <th class="text-center">Currency</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Due date</th>
<!--             <th class="text-center" width="4"> Action </th>
 -->        </tr>
    </thead>
    <tbody id="tbody2">
        <tr>
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td style="width: 200px;">
                <div class="form-group">
                <select class="form-control" name="nomor_rek" id="nomor_rek" data-live-search="true"  required >
                <option value="" disabled selected="true">Select Account</option>  
                <?php 
                        $sqlacc = mysqli_query($conn1,"select nama_bank,curr,no_rek,RIGHT(no_rek,4) as kode from masterbank");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['no_rek'];
                            if($row['no_rek'] == $_POST['nomor_rek']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="nomor_rek" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            
                        }
                        ?>
                </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="">
                </div>
            </td>
            <td>
                <div class="form-group">
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php echo date("d-m-Y"); ?>" autocomplete='off'>
                </div>
            </td>
            <td>
                <div class="form-group">
                <input type="text" class="form-control" name="keterangan[]" placeholder="">
                </div>
            </td>
            <td>
                <div class="form-group">
                <input type="text" class="form-control" required="required" name="amount" placeholder="0.00">
                </div>
            </td>
            <td>
                <div class="form-group">
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php echo date("d-m-Y"); ?>" autocomplete='off'>
                </div>
            </td>
<!--             <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
 -->        </tr>
    </tbody>
<!--     <tfoot>
          <tr>
            <td colspan="8" align="center">
            <button type="button" class="btn btn-primary" onclick="addRow('tbody2')">Tambah Baris</button>
            <button type="button" class="btn btn-warning" onclick="InsertRow('tbody2')">Sisip Baris</button>
            <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')">Hapus Baris</button>
            </td>
          </tr>
    </tfoot>  -->                  
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="col-md-5 mb-1">
                </br>
             
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Amount</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate1" name="nomrate1" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Balance</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate2" name="nomrate2" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
            </div>
            </br>

            </div>
            
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='payment.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
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
    
   // JavaScript Document
function addRow(tableID) {
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);
    var colCount = table.rows[0].cells.length;
    for(var i=0; i<colCount; i++) {
        var newcell = row.insertCell(i);
        newcell.innerHTML = table.rows[0].cells[i].innerHTML;
        var child = newcell.children;
        for(var i2=0; i2<child.length; i2++) {
            var test = newcell.children[i2].tagName;
            switch(test) {
                case "INPUT":
                    if(newcell.children[i2].type=='checkbox'){
                        newcell.children[i2].value = "";
                        newcell.children[i2].checked[0] = true;

                    }else{
                        newcell.children[i2].value = "";
                    }
                break;
                case "SELECT":
                    newcell.children[i2].value = "";
                break;
                default:
                break;
            }
        }
    }
}
    
function deleteRow(tableID)
{
    try
         {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[7].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    if (rowCount <= 1)
                        {
                        alert("Tidak dapat menghapus semua baris.");
                        break;
                        }
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                    }
                }
            } catch(e)
    {
    alert(e);
    }
 }
 
 function InsertRow(tableID)
{
    try{
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[0].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    var newRow = table.insertRow(i+1);
                    var colCount = table.rows[0].cells.length;
                        for (h=0; h<colCount; h++){
                            var newCell = newRow.insertCell(h);
                            newCell.innerHTML = table.rows[0].cells[h].innerHTML;
                            var child = newCell.children;
                            for(var i2=0; i2<child.length; i2++) {
                                var test = newCell.children[i2].tagName;
                                switch(test) {
                                    case "INPUT":
                                        if(newCell.children[i2].type=='checkbox'){
                                            newCell.children[i2].value = "";
                                            newCell.children[i2].checked[0] = true;
                                        }else{
                                            newCell.children[i2].value = "";
                                        }
                                    break;
                                    case "SELECT":
                                        newCell.children[i2].value = "";
                                    break;
                                    default:
                                    break;
                                }
                            }
                        }
                    }
                    
                }
            } catch(e)
    {
    alert(e);
    }
 }
</script>

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
</script>
    

<script type="text/javascript">
    $("input[name=txt_amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var kb = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
    var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
    var select_amount = $(this).closest('tr').find('td:eq(6) input');                
    if(amount > balance){
        sum_kb += kb;
        select_amount.val(balance);
        sum_amount += balance;
        sum_total = sum_kb - sum_amount;
    }else{
    sum_kb += kb;
    sum_amount += amount;
    sum_total = sum_kb - sum_amount;        
    }   
    });
    $("#subtotal").val(formatMoney(sum_kb));
    $("#pajak").val(formatMoney(sum_amount));    
    $("#total").val(formatMoney(sum_total));
    });
</script>

<!-- -->

<script type="text/javascript">
    $("input[name=amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var amount = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;

    sum_amount += amount;
 
     
    });

    $("#nomrate1").val(formatMoney(sum_amount));    
    $("#nomrate2").val(formatMoney(sum_amount));    

    });
</script>


<script type="text/javascript"> 
<?php echo $jsArray; ?>
function changeValueACC(id){
    var select_rate = document.getElementById('rate');   
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('kode').value = prdName[id].kode;
    if (prdName[id].valuta == 'IDR') {
            select_rate.disabled = true;
        }else{
            select_rate.disabled = false;
        }
};
</script>

<script type="text/javascript">
    $("input[name=rate]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
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

<script type="text/javascript">
    $("input[name=nominal_h]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = ttl_h;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#nominal").val(formatMoney(rat));

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

<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_pv = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_pv = $(this).closest('tr').find('td:eq(1)').text();
    var pay_to = $(this).closest('tr').find('td:eq(10)').attr('value');
    var pay_date = $(this).closest('tr').find('td:eq(11)').attr('value');
    var pay_meth = $(this).closest('tr').find('td:eq(12)').attr('value');
    var f_akun = $(this).closest('tr').find('td:eq(13)').attr('value');
    var t_akun = $(this).closest('tr').find('td:eq(14)').attr('value');
    var cek_no = $(this).closest('tr').find('td:eq(8)').attr('value');
    var cek_date = $(this).closest('tr').find('td:eq(9)').attr('value');
    var pay_for = $(this).closest('tr').find('td:eq(15)').attr('value');

    $.ajax({
    type : 'post',
    url : 'ajax_pv.php',
    data : {'no_pv': no_pv},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_pv);
    $('#txt_tglbpb').html('PV Date : ' + tgl_pv + '');
    $('#txt_top').html('Payment Method : ' + pay_meth + '');
    $('#txt_no_po').html('Payment To : ' + pay_to + '');
    $('#txt_supp1').html('Payment For : ' + pay_for + '');
    $('#txt_supp').html('Payment Date : ' + pay_date + '');
    $('#txt_curr').html('From Account : ' + f_akun + '');        
    $('#txt_confirm').html('To Account : ' + t_akun + '');
    $('#txt_confirm1').html('Cheque No : ' + cek_no + '');
    $('#txt_confirm2').html('Cheque Date : ' + cek_date + '');                
});

</script> -->



<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var data = $(this).closest('tr').find('td:eq(1) input').val();
         
             
        $.ajax({
            type:'POST',
            url:'insertdoc_.php',
            data: {'doc_number':doc_number, 'data':data},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#details').html(data);
                // console.log(response);
                // $('#modal-form2').modal('toggle');
                // // $('#modal-form2').modal('hide');
                //  // alert("Data saved successfully");
                // window.location.reload(false);
                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
                return false; 
    });

    $("#form-data").on("click", "#btn3", function(){
        var doc_number = document.getElementById('no_doc').value;        
         
             
        $.ajax({
            type:'POST',
            url:'hapusdoc.php',
            data: {'doc_number':doc_number},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 // alert("Data saved successfully");
                // window.location.reload(false);
                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
 
    });


</script>

<script type="text/javascript">
    $("#modal-form3").on("click", "#send3", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var data = $(this).closest('tr').find('td:eq(1) input').val();
        var ke_berapa = document.getElementById('ke_berapa').value; 
        var dari_berapa = document.getElementById('dari_berapa').value; 
        var dari_akun = document.getElementById('dari_akun').value; 
        var ke_akun = document.getElementById('ke_akun').value; 
        var keter = document.getElementById('keter').value; 
        var supp_doc = $(this).closest('tr').find('td:eq(2) input').val();
         
             
        $.ajax({
            type:'POST',
            url:'insertforpay.php',
            data: {'doc_number':doc_number, 'data':data,'ke_berapa':ke_berapa, 'dari_berapa':dari_berapa,'dari_akun':dari_akun, 'ke_akun':ke_akun,'keter':keter,'supp_doc':supp_doc},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                 return false; 
            },
            success: function(response){
                // console.log(response);
                //  alert(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                window.location.reload(false);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
                 return false; 
        // return false; 
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var data = $(this).closest('tr').find('td:eq(1) input').val();
         
             
        $.ajax({
            type:'POST',
            url:'insertdoc.php',
            data: {'doc_number':doc_number, 'data':data},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 // alert("Data saved successfully");
                window.location.reload(false);
                return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
         return false; 
 
    });


</script>


<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var doc_number = document.getElementById('no_doc').value;        
        var doc_date = document.getElementById('tgl_active').value;
        var referen = $('select[name=ref_num] option').filter(':selected').val();    
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var akun = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var curr = document.getElementById('valuta').value; 
        var coa = document.getElementById('coa').value;        
        var cost = document.getElementById('cost').value;
        var nominal = document.getElementById('nominal_h').value; 
        var rate = document.getElementById('rate').value;        
        var eqv_idr = document.getElementById('nomrate_h').value;
        var pesan = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';

        
        $.ajax({
            type:'POST',
            url:'insertbankout_h.php',
            data: {'doc_number':doc_number, 'doc_date':doc_date, 'referen':referen, 'nama_supp':nama_supp, 'akun':akun, 'bank':bank, 'curr':curr, 'coa':coa, 'cost':cost, 'nominal':nominal, 'rate':rate, 'eqv_idr':eqv_idr, 'pesan':pesan, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                //  // alert(response);
                window.location = 'bank-out.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 
                        

        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
        var doc_date = document.getElementById('tgl_active').value;
        var referen = $('select[name=ref_num] option').filter(':selected').val();    
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var no_pay = $(this).closest('tr').find('td:eq(1)').attr('value');                               
        var pay_date = $(this).closest('tr').find('td:eq(2)').attr('value');
        var due_date = $(this).closest('tr').find('td:eq(3)').attr('value');
        var akun = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var curr = document.getElementById('valuta').value; 
        var coa = document.getElementById('coa').value;        
        var cost = document.getElementById('cost').value;
           
        $.ajax({
            type:'POST',
            url:'insertbankout.php',
            data: {'doc_number':doc_number, 'doc_date':doc_date, 'referen':referen, 'nama_supp':nama_supp, 'no_pay':no_pay, 'pay_date':pay_date, 'due_date':due_date, 'akun':akun, 'bank':bank, 'curr':curr, 'coa':coa, 'cost':cost},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                  // alert(response);
                
                window.location = 'bank-out.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    
        });                
       
            alert("data saved successfully");
    });
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>

<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalkbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(2)').text();
    var supp = $(this).closest('tr').find('td:eq(9)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(7)').text();
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(16)').attr('value');
    var status = $(this).closest('tr').find('td:eq(17)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(18)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(15)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(19)').text();                

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

</script> -->

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
