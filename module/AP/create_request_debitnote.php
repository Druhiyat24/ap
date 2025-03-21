<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h3 class="text-center">FORM REQUEST DEBIT NOTE</h3>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>No Request</b></label>
                <?php
            $sql = mysqli_query($conn2,"select max(SUBSTR(no_req,15)) from req_dn_h");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_req)'];
            $urutan = (int) substr($kodepay, 15, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "RQDN/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            ?>
        </div>

            <div class="col-md-2 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_doc" id="tgl_doc" class="form-control tanggal" 
            value="<?php 
            $nama_type = isset($_POST['nama_type']) ? $_POST['nama_type']: null;            
            if(!empty($_POST['nama_type'])) {
                echo $_POST['tgl_doc'];
            }
            else{
                echo date("d-m-Y");
            } ?>" autocomplete='off' readonly>

            <input type="hidden" style="font-size: 15px;" name="unik_code" id="unik_code" class="form-control" 
            value="<?php 
            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789!@#$%^&*()?';
            $shuffle  = substr(str_shuffle($karakter), 0, 25);
            echo $shuffle; ?>" autocomplete='off' readonly>
            </div>

            <div class="col-md-3 mt-2">
                <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" selected="disabled">Select Supplier</option>                                                
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

            <div class="col-md-8 mb-3" >
                    <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
                    <textarea style="font-size: 15px; text-align: left;" cols="40" rows="2" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..." required></textarea>         
                </div>

            </div>

            <input type="hidden" style="font-size: 12px;" class="form-control" id="ambil_ip" name="ambil_ip" 
            value="<?php
                    echo gethostbyaddr($_SERVER['REMOTE_ADDR']); echo ' '; if($_SERVER['REMOTE_ADDR'] == '::1'){ echo 'LOCALHOST';}else{ echo $_SERVER['REMOTE_ADDR'];}
            ?>" >

                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat_pv" name="rat_pv" 
                value="<?php

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'HARIAN'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'HARIAN'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        ?>">

        



                                        
    </div>

               
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 10px;text-align:center;">
                    <thead>
        <tr><th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">-</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">No PO</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Item</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Qty</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Price</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Attn</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Seasons</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;">Reff</th>
            <th style="text-align: center;vertical-align: middle;font-size: 12px;width: 12%;"> Action </th>
        </tr>
    </thead>
    
    <tbody id="tbody2">
        <tr style="display: none;">
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
            <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
            <td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_qty" name="txt_qty"  oninput="modal_input_qty(value)" autocomplete = "off"></td>
            <td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td>
            <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
            <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
            <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td>
            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
        </tr>
    </tbody>
    <tfoot>
          <tr>
            <td colspan="10" align="center">
            <button type="button" class="btn btn-primary" onclick="addRow('tbody2')">Add Row</button>
            <button type="button" class="btn btn-warning" onclick="InsertRow('tbody2')">Interject Row</button>
            <button type="button" class="btn btn-danger" onclick="hapusbaris()">Delete Row</button>
            <!-- <input  style="margin-right: 15px;border: 0; line-height: 1; padding: 10px 20px; font-size: 1rem; text-align: center; color: #fff; text-shadow: 1px 1px 1px #000; border-radius: 6px; background-color: rgb(30, 144, 255);" id="add" type="button" value="(+) Add">  -->
            </td>
          </tr>
    </tfoot>                   
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
            <div class="col-md-4">
                </br>
      
            
            </br>
        <!--     <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 80px;"><b>Tax (11%)</b></label>
                <input type="checkbox" id="check_vat_baru" name="check_vat_baru" onclick="modal_input_vat_baru()">
            </div>
            </br> -->
             
            
            </div>
            <div class="col-md-4">

            </div>
            <div class="col-md-4">
                </br>
                <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total Amount</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="total_value" name="total_value" placeholder="0.00" readonly>
                 <input type="hidden" name="total_value_h" id="total_value_h" value="">
            </div>
            </br>
             
        </div>
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='request_debitnote.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
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
  <script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-multiselect.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
    <script language="JavaScript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.js"></script>

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
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
</script>


<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_coa').select2({
            dropdownAutoWidth : true
        });

        $('.select2_costcenter').select2({
            dropdownAutoWidth : true
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

<script>
    $(".select2").select2({
        theme: "bootstrap",
        placeholder: "Search"
} );
</script>


<script type="text/javascript">
    
   // JavaScript Document
function addRow(tableID) {
    var tableID = "tbody2";
 var table = document.getElementById(tableID);
 var rowCount = table.rows.length;
 var row = table.insertRow(rowCount);

$(function() {
    $('.selectpicker').selectpicker();
});
$(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
$(function() {
      //Initialize Select2 Elements
      var selectcoba = rowCount;
      $('.rowCount').select2({
         theme: 'bootstrap4'
      })
      //Initialize Select2 Elements
      $('.select2add').select2({
        theme: 'bootstrap4'
      })
    });
 $coa = '';
 var element1 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_qty" name="txt_qty"  oninput="modal_input_qty(value)" autocomplete = "off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';


 row.innerHTML = element1;    
    }
    //<td><select class="form-control selectpicker" name="supp" id="supp" data-live-search="true" data-width="250px" data-size="5"> <option value="-" > - </option><?php $sql = mysqli_query($conn1,"select distinct(Supplier) supp from mastersupplier where tipe_sup = 'S' order by Supplier ASC"); foreach ($sql as $coa) : ?> <option value="<?= $coa["supp"]; ?>"><?= $coa["supp"]; ?> </option><?php endforeach; ?></select></td>
    
function deleteRow()
{
    try
         {
        var table = document.getElementById("tbody2");
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[8].childNodes[0];
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
                var chkbox = row.cells[8].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
$(function() {
    $('.selectpicker').selectpicker();

});

$(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
        var element2 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_qty" name="txt_qty"  oninput="modal_input_qty(value)" autocomplete = "off"></td><td><input style="text-align: right;font-size: 12px;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';
        var newRow = table.insertRow(i+1);
        newRow.innerHTML = element2;
                    
                    }
                    
                }
            } catch(e)
    {
    alert(e);
    }
 }

 function hitungRow(){
    var table = document.getElementById("tbody2");
    var rowCount2 = table.rows.length;
    var tota = 0;
    var tot_price = 0;

          for(var i=0; i<rowCount2; i++){

    var price = parseFloat(document.getElementById("tbody2").rows[i].cells[6].children[0].value,10) || 0;

    tota += price;
    
    document.getElementsByName("total_value_h")[0].value = tota.toFixed(2);
    document.getElementsByName("total_value")[0].value = formatMoney(tota.toFixed(2));
}
     
}


async function hapusbaris(){
   await deleteRow()
   console.log("result");
   hitungRow();
}
</script>



<script type="text/javascript">
      function modal_input_qty(){ 

    var table = document.getElementById("tbody2");
    var tota = 0;
    var tota_pph = 0;
    var total_pph = 0;
    var tota_ppn = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var qty = document.getElementById("tbody2").rows[i].cells[3].children[0].value || 0;
    var price = document.getElementById("tbody2").rows[i].cells[4].children[0].value || 0;
    harga = parseFloat(qty) * parseFloat(price);
    tota += parseFloat(harga);


    document.getElementsByName("total_value_h")[0].value = tota.toFixed(2);
    document.getElementsByName("total_value")[0].value = formatMoney(tota.toFixed(2));

}
}

function modal_input_amt(){ 

    var table = document.getElementById("tbody2");
    var tota = 0;
    var tota_pph = 0;
    var total_pph = 0;
    var tota_ppn = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 1; i < (table.rows.length); i++) {

    var qty = document.getElementById("tbody2").rows[i].cells[3].children[0].value || 0;
    var price = document.getElementById("tbody2").rows[i].cells[4].children[0].value || 0;
    harga = parseFloat(qty) * parseFloat(price);
    tota += parseFloat(harga);


    document.getElementsByName("total_value_h")[0].value = tota.toFixed(2);
    document.getElementsByName("total_value")[0].value = formatMoney(tota.toFixed(2));

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
    

<!-- <script type="text/javascript">
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
</script> -->

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


<!-- <script type="text/javascript"> 
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
 -->
<!-- <script type="text/javascript">
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
</script> -->

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
    $("#modal-form3").on("click", "#send3", function(){
        var valu = '';
        $("input[type=radio]:checked").each(function () {
        var data = $(this).closest('tr').find('td:eq(1) input').val();
        valu = data;
        console.log(data);
         
             
                  
        });
        $("#txt_forpay").val(valu);
 
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
    $("#modal-form2").on("click", "#send2", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;
        var unik_code = document.getElementById('unik_code').value;        
        var data = $(this).closest('tr').find('td:eq(1) input').val();
         
             
        $.ajax({
            type:'POST',
            url:'insertdoc.php',
            data: {'doc_number':doc_number, 'unik_code':unik_code, 'data':data},
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
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
                // return false; 
 
    });


</script>

<!-- <script type="text/javascript">
    $("#form-data").on("click", "#btn2", function(){
        $("input[type=checkbox]:checked").each(function () {
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

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
    });


</script> -->

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        var no_req = document.getElementById('no_doc').value;  
        var tgl_req = document.getElementById('tgl_doc').value; 
        var unik_code = document.getElementById('unik_code').value;       
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var deskripsi = document.getElementById('pesan').value;
        var total_amount = document.getElementById('total_value_h').value;
        var create_user = '<?php echo $user; ?>';

        if (total_amount >= '1' && nama_supp != '') {
        $.ajax({
            type:'POST',
            url:'insert_req_dn_h.php',
            data: {'no_req':no_req, 'tgl_req':tgl_req, 'unik_code':unik_code, 'nama_supp':nama_supp, 'deskripsi':deskripsi, 'total_amount':total_amount, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                 $("input[type=checkbox]:checked").each(function () {
                    var unik_code = document.getElementById('unik_code').value;  
                    var no_po = $(this).closest('tr').find('td:eq(1) input').val();                               
                    var item = $(this).closest('tr').find('td:eq(2) input').val();
                    var qty = $(this).closest('tr').find('td:eq(3) input').val();
                    var price = $(this).closest('tr').find('td:eq(4) input').val();                               
                    var attn = $(this).closest('tr').find('td:eq(5) input').val();
                    var seasons = $(this).closest('tr').find('td:eq(6) input').val();
                    var no_reff = $(this).closest('tr').find('td:eq(7) input').val(); 
                    var create_user = '<?php echo $user; ?>';
        

                if (qty > 0 && price > 0) { 
                    $.ajax({
                    type:'POST',
                    url:'insert_req_dn.php',
                    data: {'unik_code':unik_code, 'no_po':no_po, 'item':item, 'qty':qty, 'price':price, 'attn':attn, 'seasons':seasons, 'no_reff':no_reff, 'create_user':create_user},
                    cache: 'false',
                    close: function(e){
                        e.preventDefault();
                    },
                    success: function(response){
                        console.log(response);
                        // alert(response);
                
                        window.location = 'request_debitnote.php';
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr);
                        alert(xhr);
                    }
                    });
                }
    
        });
                alert(response);
                // window.location = 'cash-in.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        } 
                         
       if($('select[name=nama_supp] option').filter(':selected').val() == '' || $('select[name=nama_supp] option').filter(':selected').val() == '-'){
        alert("Please Select Supplier");
        document.getElementById('nama_supp').focus();
        }else if(document.getElementById('total_value_h').value == ''){
        alert("Please Input Amount");
        }else if(document.getElementById('total_value_h').value <= '0'){
        alert("Amount can't be Minus");
        }else if(document.getElementById('total_value_h').value == '0.00'){
        alert("Total Amount can't be Zero");
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
    $("#form-simpan").on("click", "#batal", function(){
        $("input[type=checkbox]:checked").each(function () {
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

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
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
