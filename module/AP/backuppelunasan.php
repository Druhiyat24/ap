<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">PELUNASAN FTR</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="nopayment"><b> No. Pelunasan FTR</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(payment_ftr_id) from payment_ftr");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(payment_ftr_id)'];
            $urutan = (int) substr($kodepay,17,5);
            $urutan ++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "FTR/LP/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="nopayment" name="nopayment" value="'.$kodepay.'">'
            ?>
            </div>
            <div class="col-md-3 mb-3">            
            <label for="tanggal"><b>Tanggal Pelunasan <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

                    
                                        
    </div>

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

            <div class="col-md-9 mb-3">
            <label for="nama_supp"><b>Supplier</b></label>            
            <div class="input-group">
            <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?>">

    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Pilih Data</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">

            <form id="modal-form" method="post">
            <label for="nama_supp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Pilih Supplier</option>                
                <?php 
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

            <div class="input-group-append col">
            <input type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Pilih Data">
            <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
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
                        <tr>
                            <th style="width:10px;"><input type="radio" id="select_all"></th>
                            <th style="width:50px;">NO LP</th>
                            <th style="width:100px;">Tgl LP</th>                            
                            <th style="width:50px;">No KB</th>                            
                            <th style="width:100px;">Tgl KB</th>
                            <th style="width:100px;">Nama Supplier</th>
                            <th style="width:100px;">Total KB</th>                                                        
                            <th style="width:100px;">Valuta</th>
                            <th style="width:100px;">Sudah dibayar</th>                            
                        </tr>
                    </thead>

            <tbody>
            <?php
            $no_pay = '';
            $tgl_pay = '';
            $no_kbon = '';
            $tgl_kbon = '';
            $valuta= '';
            $total = 0;            

            $sql = mysqli_query($conn2,"select list_payment.no_payment as no_pay, list_payment.tgl_payment as tgl_pay,list_payment.no_kbon as no_kbon, list_payment.tgl_kbon as tgl_kbon,list_payment.nama_supp as supplier, list_payment.total_kbon as total, list_payment.curr as valuta, SUM(list_payment.amount) as dibayar, list_payment.outstanding as outstanding from list_payment  where list_payment.nama_supp = '$nama_supp' and list_payment.status = 'Approved' and total_kbon != 0 and outstanding !=0 group by no_kbon");


            while($row = mysqli_fetch_array($sql)){                      
                    echo '<tr>
                            <td style="width:10px;"><input type="radio" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" class="dt_out" style="width:100px;" dataout1="'.$row['no_pay'].'">'.$row['no_pay'].'</td>
                            <td style="width:100px;" class="dt_out" style="width:100px;" dataout2="'.$row['tgl_pay'].'">'.date("d-M-Y",strtotime($row['tgl_pay'])).'</td>
                            <td style="width:50px;" class="dt_out" style="width:100px;" dataout3="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
                            <td style="width:100px;" class="dt_out" style="width:100px;" dataout4="'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                            <td style="width:100px;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="width:50px;" class="dt_out" style="width:100px;" dataout5="'.$row['total'].'">'.$row['total'].'</td>
                            <td style="width:100px;" class="dt_out" style="width:100px;" dataout6="'.$row['valuta'].'">'.$row['valuta'].'</td>
                            <td style="width:100px;" class="dt_out" style="width:100px;" dataout7="'.$row['dibayar'].'">'.$row['dibayar'].'</td>
                                                                                 
                        </tr>';
                      }                  
                    ?>
            </tbody>                    
            </table>  

<div class="box footer">   
        <form id="form-simpan">
            </br> 

            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 150px;">No. List Payment FTR</b></label> 
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="no_payment" id="no_payment" value="" placeholder="-" style="font-size: 14px; text-align: left;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 150px;">Tgl. List Payment FTR</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="tgl_payment" id="tgl_payment" value="" placeholder="-" style="font-size: 14px; text-align: left;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 150px;">No. Kontrabon FTR</b></label> 
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="no_kbon" id="no_kbon" value="" placeholder="-" style="font-size: 14px; text-align: left;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 150px;">Tgl. Kontrabon FTR</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="tgl_kbon" id="tgl_kbon" value="-" placeholder="" style="font-size: 14px; text-align: left;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;">Valuta FTR</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="valuta1" id="valuta1" value="-" placeholder="" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>          
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;">Total Kontrabon</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;">Total Bayar</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="dibayar" id="dibayar" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 150px;">Sisa</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="outstanding" id="outstanding" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
            </br>

            <div class="col-md-5 mb-2">
            <div class="input-group">
                <label for="carabayar" class="col-form-label" style="width: 150px;">Cara Pembayaran </label>               
                <select class="form-control selectpicker" name="carabayar" id="carabayar" data-live-search="true">
                    <option value="" disabled selected="true">Pilih Cara Pembayaran</option>  
                    <option value="TRANSFER">TRANSFER</option>  
                    <option value="TUNAI">TUNAI</option>                      
                    <option value="GIRO">GIRO</option>  
                    <option value="CEK">CEK</option>  
        
                </select>        
            </div>
            </br>
          
            <div class="input-group">
                <label for="accountid" class="col-form-label" style="width: 150px;" >Account </label>  
                <select class="form-control" name="accountid" id="accountid" data-live-search="true" onchange='changeValueACC(this.value)' required >
                <option value="" disabled selected="true">Pilih Account</option>  
                <?php 
                        $sqlacc = mysqli_query($conn2,"select * from masterbank");
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['no_rek'];
                            if($row['no_rek'] == $_POST['accountid']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray .= "prdName['" . $row['no_rek'] . "'] = {nama_bank:'" . addslashes($row['nama_bank']) . "',valuta:'".addslashes($row['curr'])."'};\n";
                        }
                                                
                        ?>
                </select>
                   
            </div>
            </br>
           
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;">Bank </label>                  
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
                </br>
               
                <div class="input-group" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;">Valuta </label>         
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >         
                </div>
                </br>
              
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 150px;">Nominal </label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="nominal" name="nominal" >
            </div>
            </br>

            </div>

           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" class="btn-primary" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Simpan</button>                
            <button type="button" class="btn-danger" name="batal" id="batal" onclick="location.href='pelunasanftr.php'"><span class="fa fa-times"></span> Batal</button>           
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/igorescobar/jQuery-Mask-Plugin/1ef022ab/dist/jquery.mask.min.js"></script>


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
    $("input[type=radio]").change(function(){
    var no_pay = '';
    var tgl_pay = '';
    var no_kbon = '';
    var tgl_kbon = '';
    var valuta = '';
    var total = 0;
    var dibayar = 0;
    var sisa = 0;

    $("input[type=radio]:checked").each(function () {        
    var pay = $(this).closest('tr').find('td:eq(1)').attr('dataout1');
    var tglpay = $(this).closest('tr').find('td:eq(2)').attr('dataout2');
    var kbon = $(this).closest('tr').find('td:eq(3)').attr('dataout3');
    var tglkbon = $(this).closest('tr').find('td:eq(4)').attr('dataout4');
    var val = $(this).closest('tr').find('td:eq(7)').attr('dataout6');
    var ttl = parseFloat($(this).closest('tr').find('td:eq(6)').attr('dataout5'),10) || 0;
    var dbayar = parseFloat($(this).closest('tr').find('td:eq(8)').attr('dataout7'),10) || 0;    
              
    no_pay = pay;
    tgl_pay = tglpay; 
    no_kbon = kbon;
    tgl_kbon = tglkbon;
    valuta = val;
    total = ttl;
    dibayar = dbayar;
    sisa = total -dibayar; 
    });
    $("#no_payment").val(no_pay);
    $("#tgl_payment").val(tgl_pay);
    $("#no_kbon").val(no_kbon);
    $("#tgl_kbon").val(tgl_kbon);
    $("#valuta1").val(valuta);
    $("#total").val(formatMoney(total));
    $("#dibayar").val(formatMoney(dibayar));
    $("#outstanding").val(parseFloat(sisa));
    $("#ket").val(parseFloat(keter));
    // $("#select").val("1");                    
});        
</script>

<!-- <script type="text/javascript">
    $("input[name=txt_amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=radio]:checked").each(function () {        
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
</script> -->

<!-- <script type="text/javascript">
    $("#txt_dp").keyup(function(){
        var dp_value = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;
        dp_value = total_po * (dp / 100);
        $("#txt_dp_value").val(formatMoney(dp_value));
    });
</script> -->

<!-- <script type="text/javascript">
    $("#txt_dp_value").keyup(function(){
        var dp_code = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp_value = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        dp_code = (dp_value / total_po) * 100;
        $("#txt_dp").val(dp_code);
    });
</script> -->

<!--<script type="text/javascript">
    $("#form-data").on("click", "#send", function(){
        var datas= $('select[name=nama_supp] option').filter(':selected').val();
        var start_date= $('#start_date').attr('value');
        var end_date= $('#start_date').attr('value');
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'nama_supp':datas, 'start_date': start_date, 'end_date': end_date},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                alert(response);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });
    });
</script>-->

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
        $("input[type=radio]:checked").each(function () {
        var payment_ftr_id = document.getElementById('nopayment').value;        
        var tgl_pelunasan = document.getElementById('tanggal').value;        
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var list_payment_id = $(this).closest('tr').find('td:eq(1)').attr('dataout1');                               
        var tgl_list_payment = $(this).closest('tr').find('td:eq(2)').attr('dataout2');
        var no_kbon = $(this).closest('tr').find('td:eq(3)').attr('dataout3')
        var tgl_kbon = $(this).closest('tr').find('td:eq(4)').attr('dataout4');
        var valuta_ftr = $(this).closest('tr').find('td:eq(7)').attr('dataout6');        
        var ttl_bayar = parseFloat($(this).closest('tr').find('td:eq(6)').attr('dataout5'),10) ||0;
        var cara_bayar = document.getElementById('carabayar').value; 
        var account = document.getElementById('accountid').value;        
        var bank = document.getElementById('nama_bank').value;
        var valuta_bayar = document.getElementById('valuta').value;  
        var nominal = parseFloat(document.getElementById('nominal').value);
        var outstanding = parseFloat(document.getElementById('outstanding').value);
        var dibayar = parseFloat($(this).closest('tr').find('td:eq(8)').attr('dataout7'),10) || 0;

        // var top = $(this).closest('tr').find('td:eq(4)').attr('value');
        // var outstanding = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) ||0;
        // var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) ||0;
        // var tgl_tempo = $(this).closest('tr').find('td:eq(7)').attr('value');
        var sisa = 0;
        sisa += outstanding - nominal; 
        if(sisa < 1){    
        $.ajax({
            type:'POST',
            url:'insertrepaymentftr.php',
            data: {'payment_ftr_id':payment_ftr_id, 'tgl_pelunasan':tgl_pelunasan, 'nama_supp':nama_supp,'list_payment_id':list_payment_id, 'tgl_list_payment':tgl_list_payment, 'no_kbon':no_kbon,'tgl_kbon':tgl_kbon, 'valuta_ftr':valuta_ftr, 'ttl_bayar':ttl_bayar,'cara_bayar':cara_bayar, 'account':account, 'bank':bank, 'valuta_bayar':valuta_bayar, 'nominal':nominal, 'outstanding':outstanding, 'sisa':sisa, 'dibayar':dibayar},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                alert(response);
                window.location = 'pelunasanftr.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }
    else{
        // $(this).closest('tr').find('td:eq(6) input').focus();
        alert("Masukan Nominal yang sama dengan tagihan anda!, Jika ingin membayar dengan mencicil silahkan buka di menu List Payment");
    }
   
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Silahkan Ceklist No List Payment Dahulu");            
        }            
    });
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':radio').prop('checked', c);
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
    var create_user = $(this).closest('tr').find('td:eq(11)').attr('value');
    var status = $(this).closest('tr').find('td:eq(10)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(12)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(13)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(14)').text();                

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
