<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM CASH ADVANCES</h2>
<div class="box">
    <div class="box header">

<div class="box footer">   
        <form id="form-simpan">
            </br> 

            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Document Number</b></label>
            <div class="col-md-4 mb-3">                              
                <?php
            $sql = mysqli_query($conn2,"select max(no_ca) from c_cash_advances");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_ca)'];
            $urutan = (int) substr($kodepay, 13, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("Y");
            $huruf = "FKB/$thn/$bln/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            ?>
            </div>
            </div>
            <div class="form-row col">
               <label for="total" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Date</b></label>
            <div class="col-md-4 mb-3">                              
                 <input type="text" style="font-size: 15px;" name="tgl_doc" id="tgl_doc" class="form-control tanggal" 
            value="<?php 
            $cost_name = isset($_POST['cost_name']) ? $_POST['cost_name']: null;            
            if(!empty($_POST['cost_name'])) {
                echo $_POST['tgl_doc'];
            }
            else{
                echo date("d-m-Y");
            } ?>" autocomplete='off'>
            </div>
            </div> 

            <div class="form-row col">
               <label for="total" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Cost Center</b></label>
            <div class="col-md-4 mb-3">                              
                 <select class="form-control selectpicker" name="cost_name" id="cost_name" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Cost Center</option>                                                 
                <?php
                $cost_name ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $cost_name = isset($_POST['cost_name']) ? $_POST['cost_name']: null;
                }                 
                $sql = mysql_query("select no_cc as code_combine,cc_name as cost_name from b_master_cc",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['cost_name'];
                     $data2 = $row['code_combine'];
                    if($row['cost_name'] == $_POST['cost_name']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data2.'"'.$isSelected.'">'. $data.'</option>';    
                }?>
                </select>
            </div>
            </div>    

            <div class="form-row col">
               <label for="total" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Request By</b></label>
            <div class="col-md-4 mb-3">                              
                 <input type="text" style="font-size: 15px;" name="req_by" id="req_by" class="form-control" autocomplete='off'>
            </div>
            </div>  

            <div class="form-row col">
               <label for="total" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Buyer</b></label>
            <div class="col-md-4 mb-3">                              
                 <input type="text" style="font-size: 15px;" name="buyer" id="buyer" class="form-control" autocomplete='off'>
            </div>
            </div> 

            <div class="form-row col">
               <label for="total" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Worksheet</b></label>
            <div class="col-md-4 mb-3">                              
                 <input type="text" style="font-size: 15px;" name="no_ws" id="no_ws" class="form-control" autocomplete='off'>
            </div>
            </div> 

            <div class="form-row col">
               <label for="total" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Amount</b></label>
            <div class="col-md-4 mb-3">                             
                 <input type="text" style="font-size: 15px;text-align: right;" name="amount" id="amount" class="form-control" autocomplete='off'>
            </div>
            </div> 

            <div class="form-row col">
               <label for="total" class="col-form-label" style="width: 150px;padding-left: 10px;"><b>Descriptions</b></label>
            <div class="col-md-4 mb-3">                             
                 <textarea style="font-size: 15px; text-align: left;" cols="30" rows="5" type="text" class="form-control " name="deskripsi" id="deskripsi" value="" placeholder="descriptions..." required></textarea>
                 <input type="hidden" style="font-size: 12px;" class="form-control" id="ambil_ip" name="ambil_ip" 
            value="<?php
                    echo gethostbyaddr($_SERVER['REMOTE_ADDR']); echo ' '; if($_SERVER['REMOTE_ADDR'] == '::1'){ echo 'LOCALHOST';}else{ echo $_SERVER['REMOTE_ADDR'];}
            ?>" >
            </div>
            </div>    
            </br>     


           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='cash-advances.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
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
    $("input[type=checkbox]").change(function(){
    var no_pay = '';
    var tgl_pay = '';
    var no_kbon = '';
    var tgl_kbon = '';
    var valuta = '';
    var total = 0;
    var dibayar = 0;
    var sisa = 0;
    var buka = '';

    $("input[type=checkbox]:checked").each(function () {     
    var select_rate = document.getElementById('rate');   
    var pay = $(this).closest('tr').find('td:eq(1)').attr('dataout1');
    var tglpay = $(this).closest('tr').find('td:eq(2)').attr('dataout2');
    var kbon = $(this).closest('tr').find('td:eq(3)').attr('dataout3');
    var tglkbon = $(this).closest('tr').find('td:eq(4)').attr('dataout4');
    var val = $(this).closest('tr').find('td:eq(8)').attr('dataout6');
    var ttl = parseFloat($(this).closest('tr').find('td:eq(6)').attr('dataout5'),10) || 0;
    var dbayar = parseFloat($(this).closest('tr').find('td:eq(9)').attr('dataout7'),10) || 0; 
    //  select_rate.prop('disabled', false);  
    // buka = select_rate.prop('disabled', false);     
              
    no_pay = pay;
    tgl_pay = tglpay; 
    no_kbon = kbon;
    tgl_kbon = tglkbon;
    valuta = val;
    total = ttl;
    dibayar += dbayar;

    if(valuta == 'IDR'){
            select_rate.disabled = true;
        }else{
            select_rate.disabled = false;
        }
    });
    // $("#no_payment").val(no_pay);
    // $("#tgl_payment").val(tgl_pay);
    // $("#no_kbon").val(no_kbon);
    // $("#tgl_kbon").val(tgl_kbon);
    $("#valuta1").val(valuta);
    $("#total").val(formatMoney(total));
    $("#dibayar").val(formatMoney(dibayar));
    $("#nominal_h").val(dibayar);
    // $("#outstanding").val(formatMoney(sisa));
    $("#nominal").val(formatMoney(dibayar));
    // $("#select").val("1");                    
});        
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
        var no_doc = document.getElementById('no_doc').value;
        var tgl_doc = document.getElementById('tgl_doc').value;        
        var no_cc =$('select[name=cost_name] option').filter(':selected').val();       
        var req_by = document.getElementById('req_by').value;
        var buyer = document.getElementById('buyer').value;        
        var no_ws = document.getElementById('no_ws').value;
        var amount = document.getElementById('amount').value;
        var deskripsi = document.getElementById('deskripsi').value;
        var ambil_ip = document.getElementById('ambil_ip').value;
        var create_user = '<?php echo $user ?>';

 
        if( no_cc != '-' && req_by != '' && amount >= 1){  
        $.ajax({
            type:'POST',
            url:'insert_cashadvances.php',
            data: {'no_doc':no_doc, 'tgl_doc':tgl_doc, 'no_cc':no_cc,'req_by':req_by, 'buyer':buyer, 'no_ws':no_ws,'amount':amount, 'deskripsi':deskripsi, 'ambil_ip':ambil_ip, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert(response);
                window.location = 'cash-advances.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        }
                  
        if($('select[name=cost_name] option').filter(':selected').val() == '-'){
        document.getElementById('cost_name').focus();
        alert("Please Select Cost Center");
        } else if(document.getElementById('req_by').value == ''){
        document.getElementById('req_by').focus();
        alert("Please Enter Requester");
        } else if(document.getElementById('amount').value == ''){
        document.getElementById('amount').focus();
        alert("Please Enter Amount");
        }else{
            alert("Successful Save");
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
