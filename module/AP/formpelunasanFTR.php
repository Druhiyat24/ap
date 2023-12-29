<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-10">
        <h2 class="text-center">PELUNASAN FTR</h2>
<div class="box">
    <div class="box header">   
       <form id="form-data" method="post">

        <div class="form-row">
            <div class="col-md-2">            
                <label for="nokontrabon">No Payment FTR :</label>            
                <?php
            $sql = mysqli_query($conn2,"select max(payment_ftr_id) from payment_ftr");
            $row = mysqli_fetch_array($sql);
            $kodeBarang = $row['max(payment_ftr_id)'];
            $urutan = (int) substr($kodeBarang, 15, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("Y");
            $huruf = "LP/FTR/NAG/$thn/$bln/";
            $kodeBarang = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="nokontrabon" name="nokontrabon" value="'.$kodeBarang.'">'
            ?>
            </div>
            <div class="col-md-2">            
            <label for="tanggal">Tanggal Pelunasan : <i style="color: red;">*</i></label>          
            <input type="text" style="font-size: 12px;" name="tglpayftr" id="tglpayftr" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tglpayftr'])) {
                echo $_POST['tglpayftr'];
            }
            else{
                echo date("d-M-Y");
            } ?>">
            </div>
        
        </div>
        <br>                 
        <div class="col-md-10 mb-2">
        <div class="input-group">
            <div class="input-group">
         
            <label for="nama_supp">Nama Supplier </label>  <label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
            <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-live-search="true">
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
        </div>    

    </div>
</div>
</form>

<form id="form-simpan">

<div class="box body">
    <div class="row">    
     <div class="col-md-5">
        <div class="input-group">                   
            <label for="no_faktur">No. List Payment FTR</label><label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <br>            
            <select class="form-control selectpicker" name="nolpftr" id="nolpftr" data-live-search="true" onchange='changeValueFTR(this.value)' required>
            <option value="" disabled selected="true">Pilih List Payment FTR</option>


             <?php 
                        $sqlacc = mysqli_query($conn2,"select * from list_payment");
                        $jsArrayFTR = "var FTRName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['no_payment'];
                            if($row['no_payment'] == $_POST['nolpftr']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="nolpftr" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArrayFTR .= "FTRName['" . $row['no_payment'] . "'] = {tgl_payment:'" . addslashes($row['tgl_payment']) . "'};\n";
                        }
                             ?>
            </select>       
</div>
        </br>  
        <div class="input-group">
                <label for="nama_supp">Tgl. List Payment </label>  <label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 12px;" class="form-control" id="tgl_payment" name="tgl_payment" readonly > 
            </div>
        </div> 
    </div>
        </br>
        <div class="row">
            <div class="col-md-5">
        <div class="input-group">                   
            <label for="no_faktur">No. Kontrabon FTR</label><label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <br>            
            <select class="form-control selectpicker" name="nokbon" id="nokbon" data-live-search="true" onchange='changeValueKBON(this.value)' required>
            <option value="" disabled selected="true">Pilih NO Kontrabon FTR</option>  
             <?php 
                        $sqlacc = mysqli_query($conn2,"select * from kontrabon");
                        $jsArrayKBON = "var KBNName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $data = $row['no_kbon'];
                            if($row['no_kbon'] == $_POST['nokbon']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="nokbon" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArrayKBON .= "KBNName['" . $row['no_kbon'] . "'] = {tglkbftr:'" . addslashes($row['tgl_kbon']) . "',valftr:'".addslashes($row['curr'])."',subtotal:'".addslashes($row['total'])."'};\n";
                        }
                             ?>
            </select>       
        </div> 
            </br>
            <div class="input-group">
                <label for="nama_supp">Tgl. Kontrabon FTR</label>  <label>  &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 12px;" class="form-control" id="tglkbftr" name="tglkbftr" readonly > 
            </div>
            </br>
            <div class="input-group">
                <label for="nama_supp">Valuta FTR </label>  <label>   &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 12px;" class="form-control" id="valftr" name="valftr" readonly > 
            </div>
            </br>
            <div class="input-group">
                <label for="subtotal"><b>Total Bayar</b></label><label>  &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                                                        
                <input type="text"style="font-size: 14px;text-align: right;" class="form-control" id="subtotal" name="subtotal"  value="" placeholder="0.00" readonly > 
              
            </div>
        </div>
     </div>
     </div>   
    </div>                  
    <div class="box footer">           
     
        <div class="col-md-5 mb-2">
            <div class="input-group">
                <label for="carabayar">Cara Pembayaran </label>  <label>&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                             
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
                <label for="accountid">Account </label>  <label>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
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
           
                <div class="input-group">
                    <label for="nama_supp">Bank </label>  <label>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
                </br>
               
                <div class="input-group">
                    <label for="nama_supp">Valuta </label> <label>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >         
                </div>
                </br>
              
            <div class="input-group">
                <label for="nama_supp">Nominal </label><label>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="no_faktur" name="no_faktur" >
            </div>
          
        </div>        
      
        <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" class="btn-primary" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Simpan</button>                
            <button type="button" class="btn-danger" name="batal" id="batal" onclick="location.href='formpelunasanFTR.php'"><span class="fa fa-times"></span> Batal</button>           
            </div>
            </div>                                    
       
    </div> 
</div>
</form>


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
        format: "dd-MM-yyyy",
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
    $("input[type=checkbox]").change(function(){
    var sum_sub = 0;
    var sum_tax = 0;
    var ceklist = 0;
    var sum_pph = 0;
    var sum_total = 0;
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);         
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
    var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]');
    select_pph.prop('disabled', false);        
    sum_sub += price;
    sum_tax += tax;
    sum_pph += price * (pph / 100);
    sum_total = sum_sub + sum_tax - sum_pph;     
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub);       
    $("#pajak").val(formatMoney(sum_tax));
    $("#pajak_h").val(sum_tax);    
    $("#pph").val(formatMoney(sum_pph));
    $("#pph_h").val(sum_pph);        
    $("#total").val(formatMoney(sum_total));
    $("#total_h").val(sum_total);    
    $("#select").val("1");                      
});        
</script>


<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_pay_ftr = document.getElementById('nokontrabon').value;
        var tgl_lunas = document.getElementById('tglpayftr').value;
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var no_list_pay = document.getElementById('nolpftr').value;
        var tgl_list_pay = document.getElementById('tgl_payment').value;
        var no_kbon = document.getElementById('nokbon').value;
        var tgl_kbon = document.getElementById('tglkbftr').value;        
        var valuta_kbon = document.getElementById('valftr').value;
        var total = document.getElementById('subtotal').value;
        var cr_bayar = document.getElementById('carabayar').value;
        var acc = document.getElementById('accountid').value;
        var bank = document.getElementById('nama_bank').value;
        var valuta = document.getElementById('valuta').value;
        var nominal = document.getElementById('no_faktur').value;        
        $.ajax({
            type:'POST',
            url:'insertrepaymentftr.php',
            data: {'no_pay_ftr':no_pay_ftr, 'tgl_lunas':tgl_lunas,'nama_supp':nama_supp, 'no_list_pay':no_list_pay, 'tgl_list_pay':tgl_list_pay, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'valuta_kbon':valuta_kbon, 'total':total, 'cr_bayar':cr_bayar, 'acc':acc, 'bank':bank, 'valuta':valuta, 'nominal':nominal},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                window.location = 'kontrabon.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });                          
          
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data Berhasil Di Simpan");
        }else{
            alert("gagal");
        }
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
<?php echo $jsArrayFTR; ?>
function changeValueFTR(id){
    document.getElementById('tgl_payment').value = FTRName[id].tgl_payment;

};
</script>

<script type="text/javascript"> 
<?php echo $jsArrayKBON; ?>
function changeValueKBON(id){
    document.getElementById('tglkbftr').value = KBNName[id].tglkbftr;
    document.getElementById('valftr').value = KBNName[id].valftr;

    document.getElementById('subtotal').value = formatMoney(KBNName[id].subtotal);
};
</script>

<script type="text/javascript"> 
function ceksupplier(){
    var namasp = document.getElementById("nama_supp").value ;
    if (namasp=="")
    {
        alert("Nama Supplier Masih Kosong");
        return false;
    }
    else
    {
        document.getElementById('carinamasupp').value = namasp;
        return true;
    }

    
};


</script>


</body>

</html>
