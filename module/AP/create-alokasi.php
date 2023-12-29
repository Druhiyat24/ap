<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM ALOKASI</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-6 container bg-light">
            <div class="col-md-9 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Doc Number</b></label>
                <?php
            $sql = mysqli_query($conn2,"select max(doc_number) from ap_bankout_h");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(doc_number)'];
            $urutan = (int) substr($kodepay,17,5);
            $urutan ++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "BK//NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;text-transform:uppercase" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_urut" name="no_urut" value="'.sprintf("%05s", $urutan).'" hidden>
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="bulan" name="bulan" value="'.$bln.'" hidden>
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="tahun" name="tahun" value="'.$thn.'" hidden>'
            ?>
        </div>

            <div class="col-md-9 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php echo date("d-m-Y"); ?>" autocomplete='off'>
            </div>

            <div class="col-md-9 mb-3">            
            <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Reference</b></label>            
              <select class="form-control selectpicker" name="ref_num" id="ref_num" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="None" selected="true">None</option>                                                 
                <?php
                $ref_num ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                }                 
                
                   $sql = mysqli_query($conn1,"select ref_doc from tbl_ref where ket = 'bankout'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['ref_doc'];
                    if($row['ref_doc'] == $_POST['ref_num']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
            </div> 
            <div class="col-md-9">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="Unrealize" selected="true">Unrealize</option>                                                 
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
                <div class="col-6 container bg-light">
            <div class="col-md-9 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Doc Number</b></label>
                <?php
            $sql = mysqli_query($conn2,"select max(doc_number) from ap_bankout_h");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(doc_number)'];
            $urutan = (int) substr($kodepay,17,5);
            $urutan ++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "BK//NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;text-transform:uppercase" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_urut" name="no_urut" value="'.sprintf("%05s", $urutan).'" hidden>
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="bulan" name="bulan" value="'.$bln.'" hidden>
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="tahun" name="tahun" value="'.$thn.'" hidden>'
            ?>
        </div>

            <div class="col-md-9 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php echo date("d-m-Y"); ?>" autocomplete='off'>
            </div>

            <div class="col-md-9 mb-3">            
            <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Reference</b></label>            
              <select class="form-control selectpicker" name="ref_num" id="ref_num" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="None" selected="true">None</option>                                                 
                <?php
                $ref_num ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                }                 
                
                   $sql = mysqli_query($conn1,"select ref_doc from tbl_ref where ket = 'bankout'");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['ref_doc'];
                    if($row['ref_doc'] == $_POST['ref_num']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
            </div> 
            <div class="col-md-9">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="Unrealize" selected="true">Unrealize</option>                                                 
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
                <div class="col-12 container bg-light">

        <div style="padding-top: 30px; padding-left: 10px;">
            <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Select">  
    </div>
</div>



                                        
    </div>
</br>

    <div class="form-row">

<!--            <div class="col-md-3 mb-3">            
            <label for="txt_pph"><b>PPh (%) </b></label>            
            <input type="text" style="font-size: 14px;" class="form-control" id="txt_pph" name="txt_pph" 
            value="<?php 
            //if(!empty($_POST['txt_pph'])){
            //    echo $_POST['txt_pph'];
            //}else{
            //    echo '';
            //}
            ?>">
            </div>-->                        


    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Payment</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
                <div class="form-row">
                 <div class="col-md-4">
            <label for="nama_supp"><b>Supplier</b></label>            
              <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?>">
        </div>
         <div class="col-md-4" style="padding-left: 150px;">

            <label for="nama_supp"><b>Reference</b></label>            
              <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
                echo $ref_num; 
            ?>">
        </div>
    </div>

                <label><b>Reference Date</b></label>
                <div class="input-group-append">           
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="start_date" name="start_date" 
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
                placeholder="Tanggal Awal">

                <label class="col-md-1" for="end_date"><b>-</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="end_date" name="end_date" 
                value="<?php
                $end_date ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $end_date = date("Y-m-d",strtotime($_POST['start_date']));
                }
                if(!empty($_POST['end_date'])) {
                    echo $_POST['end_date'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Akhir">
                </div>  
                <div class="modal-footer">
                    <button type="submit" id="send" name="send" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>


        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>
    </div>                  
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>
                            <th style="width:50px;">No Payment</th>
                            <th style="width:100px;">Payment Date</th>                                                            
                            <th style="width:100px;">Due Date</th>
                        </tr>
                    </thead>

            <tbody>
            <?php
            $start_date ='';
            $end_date ='';
            $sub = '';
            $tax = '';
            $total = '';            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $ref_num = isset($_POST['ref_num']) ? $_POST['ref_num']: null;
            }

            if ($ref_num == 'Payment') {
                $sql = mysqli_query($conn2,"select * from (
select no_payment, tgl_payment, tgl_tempo from list_payment where status = 'Closed' and tgl_payment BETWEEN '$start_date' and '$end_date' and nama_supp = '$nama_supp' group by no_payment
union
select no_pay, tgl_pay, due_date from saldo_awal  where status = 'Closed' and tgl_pay BETWEEN '$start_date' and '$end_date' and supplier = '$nama_supp' group by no_pay) as b left join 

(select no_lp from ap_bankout where status != 'Cancel') as  c on c.no_lp = b.no_payment where c.no_lp is null");
            }else{
                '';
            }

            while($row = mysqli_fetch_array($sql)){
                        
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
                            <td style="width:100px;" value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
                 
                        </tr>';
                      }                  
                    ?>
            </tbody>                    
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="col-md-5 mb-1">
                </br>
             <div class="input-group">
                <label for="accountid" class="col-form-label" style="width: 150px;" ><b>Account </b></label>  
                <select class="form-control" name="accountid" id="accountid" data-live-search="true" onchange='changeValueACC(this.value)' required >
                <option value="" disabled selected="true">Select Account</option>  
                <?php 
                        $sqlacc = mysqli_query($conn1,"select nama_bank,curr,no_rek,RIGHT(no_rek,4) as kode from masterbank");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['no_rek'];
                            if($row['no_rek'] == $_POST['accountid']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray .= "prdName['" . $row['no_rek'] . "'] = {nama_bank:'" . addslashes($row['nama_bank']) . "',kode:'" . addslashes($row['kode']) . "',valuta:'".addslashes($row['curr'])."'};\n";
                        }
                        ?>
                </select>
                   
            </div>
            </br>
           
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Bank </b></label>                  
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
                </br>
               
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Currency </b></label>         
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >  
                    <input type="text" style="font-size: 12px;" class="form-control" id="kode" name="kode" readonly hidden >         
                </div>
                </br>
                <div class="input-group">
            <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>COA</b></label>            
              <select class="form-control selectpicker" name="coa" id="coa" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select COA</option>                                           
                <?php
                $akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                $coa = isset($_POST['coa']) ? $_POST['coa']: null;
                }                 
                $sql = mysqli_query($conn1,"select id_coa,concat(id_coa,' ', coa_name) as coa from tbl_coa_detail");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['coa'];
                    $id_coa = $row['id_coa'];
                    if($row['coa'] == $_POST['coa']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_coa.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>
            </br>

            <div class="input-group">
            <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Cost Center</b></label>            
              <select class="form-control selectpicker" name="cost" id="cost" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select Cost Center</option>                                                 
                <?php
                $akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                $cost = isset($_POST['cost']) ? $_POST['cost']: null;
                }                 
                $sql = mysqli_query($conn1,"select code_combine,cost_name from tbl_cost_center");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['cost_name'];
                    $code_combine = $row['code_combine'];
                    if($row['cost_name'] == $_POST['cost']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$code_combine.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Amount </b></label>
               <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal_h" name="nominal_h" placeholder="input nominal..." >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" placeholder="0.00" readonly>
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Rate </b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate" name="rate" placeholder="input rate here..." >
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="rate_h" name="rate_h" placeholder="0.00" readonly="">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Equivalent IDR</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nomrate" name="nomrate" placeholder="0.00" readonly>
                 <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
            </div>
            </br>

            <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
                    <textarea style="font-size: 15px; text-align: left;" cols="30" rows="5" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>         
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

<script type="text/javascript">
   $('#accountid').change(function(){
    var ttl_jml = '';
    var valu = '';
    $("input[type=text]").each(function () {         
    var urut = document.getElementById('no_urut').value;
    var bulan = document.getElementById('bulan').value;
    var nbank = document.getElementById('nama_bank').value;
    var kode = document.getElementById('kode').value;
    var tahun = document.getElementById('tahun').value;

    if(nbank == 'BANK CENTRAL ASIA'){

    valu = 'BK'+'/'+'BCA'+kode+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;
}else{
    valu = 'BK'+'/'+'BNI'+kode+'/'+'NAG'+'/'+bulan+tahun+'/'+urut;
}

    
    });
   $("#no_doc").val(valu);


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
    $("#form-simpan").on("click", "#simpan", function(){
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
        var nominal = document.getElementById('nominal_h').value; 
        var rate = document.getElementById('rate').value;        
        var eqv_idr = document.getElementById('nomrate_h').value;
        var pesan = document.getElementById('pesan').value;
        var create_user = '<?php echo $user; ?>';         
             
        $.ajax({
            type:'POST',
            url:'insertbankout.php',
            data: {'doc_number':doc_number, 'doc_date':doc_date, 'referen':referen, 'nama_supp':nama_supp, 'no_pay':no_pay, 'pay_date':pay_date, 'due_date':due_date, 'akun':akun, 'bank':bank, 'curr':curr, 'coa':coa, 'cost':cost, 'nominal':nominal, 'rate':rate, 'eqv_idr':eqv_idr, 'pesan':pesan},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                 // alert("Data saved successfully");
                window.location = 'bank-out.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please check the Kontrabon number");            
        } else{
            alert("data saved successfully");
        }                       
    });
</script>
 -->
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

<script type="text/javascript">     
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
