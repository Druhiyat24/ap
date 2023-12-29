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

                 echo'<input type="text" readonly style="font-size: 12px;" class="form-control-plaintext" id="nokontrabon" name="nokontrabon" value="'.$kodeBarang.'">'
                 ?>
            </div>
            <div class="col-md-2">            
            <label for="tanggal">Tanggal Pelunasan : <i style="color: red;">*</i></label>          
            <input type="text" style="font-size: 12px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
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
                <select class="form-control selectpicker" name="tipe_pelunasan" id="tipe_pelunasan" data-live-search="true">

                    <option value="LPFTRCBD" isSelected>CBD</option>  
                    <option value="LPFTRDP">DP</option>  
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
      

       <div class="input-group">                   
            <label for="no_faktur">No. List Payment FTR  </label><label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <br>            
            <input type="text" style="font-size: 12px;" class="form-control" id="no_ftr" name="no_ftr" readonly >
            <input type="button" onclick="ceksupplier()" name="mylpftr" id="mynoftr" data-target="#mylpftr" data-toggle="modal" value="Pilih Data"> 

        </div>        
       
        <div class="modal fade" id="mylpftr" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
                        <h4 class="modal-title" id="myModalLabel">Pencarian data List Payment</h4>
                    </div>
                    <table id="simple-table" class="table table-no-bordered">
                
                        <tr><td>Supplier</td><td> : </td> 
                        <td><input type="text" style="font-size: 12px;" id="carinamasupp" name="carinamasupp" readonly ></td></tr>
                        <tr><td>Cari No LP FTR</td><td> : </td>
                        <td><input type="text" style="font-size: 12px;"id="cariNoFTR" name="cariNoFTR" ></td>
                        <td><button type="button" name="EnterCari" values="EnterCari" class="btn btn-default">Cari</button></td>

                    </tr>
                    </table>                      
                    <?php
                    if (isset($_POST['EnterCari'])) {
                         $xnamasupp = $_POST['carinamasupp'];
                         $xnolpftr = $_POST['cariNoFTR'];
                   
                    echo "Text 1 : $xnamasupp <br/>";
                    echo "Text 2 : $xnolpftr <br/>";
                    
                    }
                    ?>
                    <div class="modal-body">
                        <table id="lookup" class="table table-bordered table-hover table-striped">
                            <thead>						
                                <tr>
                                    <th style="width:10px;"><input type="checkbox" id="select_all"></th>
                                    <th>No. LP FTR</th>
                                    <th>Tgl. LP FTR</th>
                                    <th>No. KB FTR</th>
                                    <th>Tgl. KB FTR</th>
                                    <th>Valuta</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php

   
                            $queryv="SELECT no_payment,tgl_payment,nama_supp,no_kbon,tgl_kbon,curr as valuta,outstanding
                                    FROM list_payment
                                    WHERE cancel_user IS null 
                                    and nama_supp='$xnamasupp'
                                    ";

                               
                              $sqlv = mysql_query($queryv,$conn1);
                              while ($row = mysql_fetch_array($sqlv)) 
                              {
                                echo "<tr>
                                        <td style='width:10px;'><input type='checkbox' id='select' echo 'checked=checked';?'></td>
                                        <td style='text-align:center'>$row[no_payment]</td>
                                        <td style='text-align:center'>$row[tgl_payment]</td>
                                        <td style='text-align:center'>$row[no_kbon]</td>
                                        <td style='text-align:right'>$row[tgl_kbon]</td>
                                        <td style='text-align:right'>$row[valuta]</td>
                                        <td style='text-align:right'>".number_format($row['outstanding'],2)."</td>                                           
                                    </tr>";
                              }?>
   	
                            </tbody>
                        </table>  
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                        <tr>
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>
                            <th style="width:50px;">No. LP FTR</th>
                            <th style="width:50px;">Tgl. LP FTR</th>  
                            <th style="width:50px;">No. KB FTR</th>
                            <th style="width:50px;">Tgl. KB FTR</th>                                                     
                            <th style="width:100px;">Total Nominal</th>
                            <th style="width:100px;">Pembayaran</th>

                        </tr>
                    </thead>

            <tbody>
            <?php


            ?>
            </tbody>                    
            </table>                    
   <div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 100px;"><b>Total Bayar</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="subtotal_h" id="subtotal_h" value="">
            </div>
            </div>
            <div class="col-md-5 mb-2">
            <div class="input-group">
                <label for="carabayar">Cara Pembayaran </label>  <label>&nbsp;&nbsp;&nbsp;</label>                
                <select class="form-control selectpicker" name="carabayar" id="carabayar" data-live-search="true">
                    <option value="" disabled selected="true">Pilih Cara Pembayaran</option>  
                    <option value="TRANSFER">TRANSFER</option>  
                    <option value="TUNAI">TUNAI</option>                      
                    <option value="GIRO">GIRO</option>  
                    <option value="CEK">CEK</option>  
        
                </select>        
            </div>
           <br>
            <div class="input-group">
                <label for="accountid">Account </label>  <label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                <select class="form-control" name="accountid" id="accountid" data-live-search="true" onchange='changeValue(this.value)' required >
                <option value="" disabled selected="true">Pilih Account</option>  
                <?php 
                        $sqlacc = mysql_query("select * from masterbank",$conn2);
                        $jsArray = "var prdName = new Array();\n";
                        // while ($row = mysqli_fetch_array($sqlacc)) {  
                        // echo '<option name="norek"  value="' . $row['no_rek'] . '">' . $row['nama_bank'] . '</option>';  
                        // 
                        //  }

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
            <br>
                <div class="input-group">
                    <label for="nama_supp">Bank </label>  <label>  &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                
                    <input type="text" style="font-size: 12px;" class="form-control" id="nama_bank" name="nama_bank" readonly > 
                </div>
                <br>
                <div class="input-group">
                    <label for="nama_supp">Valuta </label>  <label>   &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                                 
                    <input type="text" style="font-size: 12px;" class="form-control" id="valuta" name="valuta" readonly >         
                </div>
                <br>

            <div class="input-group">
                <label for="nama_supp">Nominal </label>  <label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                                 
                <input type="text" style="font-size: 12px;" class="form-control" id="no_faktur" name="no_faktur" >
            </div>
            <br>
            <div class="input-group">
                <label for="nama_supp">Balance </label>  <label>  &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>                                 
                <input type="text" style="font-size: 12px;" class="form-control" id="balance" name="balance" >
            </div>

        </div>        
      

<div class="form-row col">
    <div class="col-md-3 mb-3">                              
        <button type="button" class="btn-primary" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Simpan</button>                
        <button type="button" class="btn-danger" name="batal" id="batal" onclick="location.href='kontrabon.php'"><span class="fa fa-times"></span> Batal</button>           
        </div>
        </div>                                    
        </form>
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
function changeValue(id){
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('swiftcode').value = prdName[id].swiftcode;
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
