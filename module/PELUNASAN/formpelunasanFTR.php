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
                 $sql = mysql_query("select max(payftr_id) from payment_ftr",$conn2);
                 $row = mysql_fetch_array($sql);
                 $kodeBarang = $row['max(payftr_id)'];
                 $urutan = (int) substr($kodeBarang, 15, 5);
                 $urutan++;
                 $bln = date("m");
                 $thn = date("y");
                 //PAY.FTR/0921-00001
                 $huruf = "PAY.FTR/$bln$thn-";
                 $kodeBarang = $huruf . sprintf("%05s", $urutan);

                 echo'<input type="text" readonly style="font-size: 12px;" class="form-control-plaintext" id="nopayftr" name="nopayftr" value="'.$kodeBarang.'">'
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
        <div class="col-md-5 mb-2">
        <div class="input-group">
            <div class="input-group">
                <label for="nama_supp">Tipe Pelunasan </label>  <label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <select class="form-control selectpicker" name="tipeftr" id="tipeftr" data-live-search="true">
                <option value="" disabled selected="true">Pilih Tipe Pelunasan</option>  
                <?php 
                        $sqftr = mysql_query("select distinct(namaftr) from mastertipeftr",$conn2);
                        while ($rowftr = mysql_fetch_array($sqftr)) {
                            $dataftr = $rowftr['namaftr'];
                            if($rowftr['namaftr'] == $_POST['tipeftr']){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option value="'.$dataftr.'"'.$isSelected.'">'. $dataftr .'</option>';    
                        }?>
                </select>       
            </div>
         
            <label for="nama_supp">Nama Supplier </label>  <label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
            <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-live-search="true">
            <option value="" disabled selected="true">Pilih Supplier</option>  
            <?php 
                    $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC",$conn1);
                    while ($row = mysql_fetch_array($sql)) {
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
</form>

<form id="form-simpan">

<div class="box body">
    <div class="row">    
     <div class="col-md-5">
        <div class="input-group">                   
            <label for="no_faktur">No. List Payment FTR</label><label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <br>            
            <select class="form-control selectpicker" name="nolpftr" id="nolpftr" data-live-search="true" onchange='changeValueFTR(this.value)' required>
            <option value="" disabled selected="true">Pilih List Payment FTR</option>  
            <?php 
                 
                    $dbtipe ="list_payment_dp";  
                                     
                    $sql = mysql_query("SELECT no_payment,tgl_payment,nama_supp,no_kbon,tgl_kbon,curr as valuta,amount
                    FROM $dbtipe
                    WHERE cancel_user IS null and nama_supp=$nama_supp",$conn2);
                   echo "
                   <script type='text/javascript'>
                   alert('$sql');
                   </script>";
                   
                        $jsArrayFTR = "var FTRName = new Array();\n";
                        while ($rowlp = mysql_fetch_array($sql)) 
                        {
                            $data = $rowlp['no_payment'];
                            if($rowlp['no_payment'] == $_POST['nolpftr']){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>'; 
                            $jsArrayFTR .= "FTRName['" . $rowlp['no_payment'] . "'] = {tglpayftr:'" . addslashes($rowlp['tgl_payment']) . "',nokbftr:'".addslashes($rowlp['no_kbon'])."',tglkbftr:'".addslashes($rowlp['tgl_kbon'])."',valftr:'".addslashes($rowlp['valuta'])."',subtotal:'".addslashes($rowlp['amount'])."'};\n";   
                        }
                   
                  ?>
            </select>       
        </div>   
        <div class="input-group">
                <label for="nama_supp">Tgl. List Payment </label>  <label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 12px;" class="form-control" id="tglpayftr" name="tglpayftr" readonly > 
            </div>
            <div class="input-group">
                <label for="nama_supp">No. Kontrabon FTR </label>  <label>  &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 12px;" class="form-control" id="nokbftr" name="nokbftr" readonly > 
            </div>
            <div class="input-group">
                <label for="nama_supp">Tgl. Kontrabon FTR</label>  <label>  &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 12px;" class="form-control" id="tglkbftr" name="tglkbftr" readonly > 
            </div>
            <div class="input-group">
                <label for="nama_supp">Valuta FTR </label>  <label>   &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <input type="text" style="font-size: 12px;" class="form-control" id="valftr" name="valftr" readonly > 
            </div>
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
          
            <div class="input-group">
                <label for="accountid">Account </label>  <label>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <select class="form-control" name="accountid" id="accountid" data-live-search="true" onchange='changeValueACC(this.value)' required >
                <option value="" disabled selected="true">Pilih Account</option>  
                <?php 
                        $sqlacc = mysql_query("select * from masterbank",$conn2);
                        $jsArray = "var prdName = new Array();\n";

                        while ($row = mysql_fetch_array($sqlacc)) {
                            $data = $row['no_rek'];
                            if($row['no_rek'] == $_POST['accountid']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="accountid" value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                            $jsArray .= "prdName['" . $row['no_rek'] . "'] = {nama_bank:'" . addslashes($row['nama_bank']) . "',valuta:'".addslashes($row['curr'])."',swiftcode:'".addslashes($row['v_swiftcode'])."'};\n";
                        }
                                                
                        ?>
                </select>
                   
            </div>
           
                <div class="input-group">
                    <label for="nama_supp">Bank </label>  <label>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
               
                <div class="input-group">
                    <label for="nama_supp">Valuta </label> <label>  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >         
                </div>
              
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
        var no_kbon_h = document.getElementById('nokontrabon').value;
        var tgl_kbon_h = document.getElementById('tanggal').value;
        var nama_supp_h = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur_h = document.getElementById('no_faktur').value;
        var supp_inv_h = document.getElementById('txt_inv').value;
        var tgl_inv_h = document.getElementById('txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('txt_tgltempo').value;        
        var curr_h = document.getElementById('matauang').value;
        var sub_h = document.getElementById('subtotal_h').value;
        var tax_h = document.getElementById('pajak_h').value;
        var pph_h = document.getElementById('pph_h').value;
        var total_h = document.getElementById('total_h').value;
        var create_user_h = '<?php echo $user; ?>';        
        $.ajax({
            type:'POST',
            url:'insertkbon_h.php',
            data: {'no_kbon_h':no_kbon_h, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'pph_h':pph_h, 'total_h':total_h},
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
        $("input[type=checkbox]:checked").each(function () {
        var no_kbon = document.getElementById('nokontrabon').value;        
        var tgl_kbon = document.getElementById('tanggal').value;
        var jurnal = document.getElementById('jurnal').value;
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur = document.getElementById('no_faktur').value;
        var supp_inv = document.getElementById('txt_inv').value;
        var tgl_inv = document.getElementById('txt_tglsi').value;
        var tgl_tempo = document.getElementById('txt_tgltempo').value;        
        var curr = document.getElementById('matauang').value;                               
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = '<?php echo $user; ?>';
        var ceklist = document.getElementById('select').value;          
        var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
        var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
        var total = parseFloat($(this).closest('tr').find('td:eq(7)').attr('data-total'),10) ||0;
        var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
        var idtax = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').attr('data-idtax');
        var sum_sub = 0;
        var sum_tax = 0;
        var sum_total = 0;
        var sum_pph = 0;
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;        
        sum_sub += price;
        sum_tax += tax;
        sum_pph += sum_sub * (pph / 100);        
        sum_total += total - sum_pph;
        $.ajax({
            type:'POST',
            url:'insertkbon.php',
            data: {'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'jurnal':jurnal, 'no_bpb':no_bpb, 'no_po':no_po,
            'nama_supp':nama_supp, 'tgl_bpb':tgl_bpb, 'no_faktur':no_faktur, 'supp_inv':supp_inv, 'tgl_inv':tgl_inv, 'tgl_tempo':tgl_tempo,
            'curr':curr, 'ceklist':ceklist, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_tax':sum_tax, 'sum_pph':sum_pph, 'sum_total':sum_total, 'start_date':start_date, 'end_date':end_date, 'pph':pph, 'idtax':idtax},
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
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data Berhasil Di Simpan");
        }else{
            alert("Silahkan Ceklist No BPB Dahulu");
        }
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
    $('#mymodalbpb').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(3)').text();
    var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(10)').attr('value');
    var top = $(this).closest('tr').find('td:eq(8)').attr('value');
    var curr = document.getElementById('matauang').value;
    var confirm = $(this).closest('tr').find('td:eq(9)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(10)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(12)').text();    

    $.ajax({
    type : 'post',
    url : 'ajaxbpb.php',
    data : {'no_bpb': no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_bpb);
    $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
    $('#txt_no_po').html('No PO : ' + no_po + '');
    $('#txt_supp2').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
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
    document.getElementById('tglpayftr').value = FTRName[id].tglpayftr;
    document.getElementById('nokbftr').value = FTRName[id].nokbftr;
    document.getElementById('tglkbftr').value = FTRName[id].tglkbftr;
    document.getElementById('valftr').value = FTRName[id].valftr;

    document.getElementById('subtotal').value = formatMoney(FTRName[id].subtotal);
    document.getElementById('valuta').value = FTRName[id].valftr;

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
