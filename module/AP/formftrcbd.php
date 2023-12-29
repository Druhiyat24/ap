<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM TRANSFER REQUEST (CBD)</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="noftrcbd"><b>No FTR CBD</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(no_ftr_cbd) from ftr_cbd where id = (select max(id) from ftr_cbd)");
            $row = mysqli_fetch_array($sql);
            $kodeftr = $row['max(no_ftr_cbd)'];
            $urutan = (int) substr($kodeftr, 15, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "FTR/C/NAG/$bln$thn/";
            $kodeftr = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="noftrcbd" name="noftrcbd" value="'.$kodeftr.'">'
            ?>
            </div>
            <div class="col-md-3 mb-3">            
            <label for="tanggal"><b>FTR CBD Date <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-4 mb-3">            
            <label for="memo"><b>Description</b></label>          
            <input type="text" style="font-size: 14px;" class="form-control" name="memo" id="memo" 
            value="<?php             
            if(!empty($_POST['memo'])) {
                echo $_POST['memo'];
            }
            else{
                echo '';
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
        <h4 class="modal-title" id="Heading">Choose Supplier</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
            <label for="nama_supp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
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

                <label><b>PO Date</b></label>
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

            <div class="input-group-append col">
            <input style="border: 0;
    line-height: 1;
    padding: 0 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Select">
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
                            <th style="width:10px;"></th>
                            <th style="width:50px;">NO PO</th>
                            <th style="width:100px;">NO PI</th>                            
                            <th style="width:50px;">PO Date</th>                            
                            <th style="width:100px;">SubTotal</th>
                            <th style="width:100px;">Tax (PPn)</th>                            
                            <th style="width:100px;">Total (PO)</th>
                            <th style="width:100px;">Currency</th>                            
                            <th style="width:100px;display: none;">Supplier</th>                                                         
                            <!--<th style="width:50px;">Delete</th>-->
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
            }
            $querys = mysqli_query($conn1,"select distinct pono from po_header");
            $rows = mysqli_fetch_array($querys);
            $pono = isset($rows['pono']) ? $rows['pono'] : null;


            $sql = mysqli_query($conn1,"select po_header.pono as no_po, po_header.podate as podate, mastersupplier.Supplier as supplier, SUM(po_item.qty * po_item.price) as sub, SUM((po_item.qty * po_item.price) * (po_header.tax / 100)) as tax, SUM((po_item.qty * po_item.price) + ((po_item.qty * po_item.price) * (po_header.tax / 100))) as total, po_item.curr as matauang, po_header.app as app, po_item.cancel as cancel, masterpterms.kode_pterms, po_header_draft.tipe_com
from po_header 
inner join po_item on po_item.id_po = po_header.id
left join po_header_draft on po_header_draft.id = po_header.id_draft
inner join mastersupplier on mastersupplier.Id_Supplier = po_header.id_supplier
inner join masterpterms on masterpterms.id = po_header.id_terms
where po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com = 'REGULAR' || po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com IS NULL || po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com = 'BUYER' group by no_po");



            while($row = mysqli_fetch_array($sql)){
            $po = $row['no_po'];
            $sub = $row['sub'];
            $tax = $row['tax'];
            $total = $row['total'];
            $querys = mysqli_query($conn2,"select no_po, status, cancel_date from ftr_cbd where no_po = '$po' and status != 'Cancel'");
            $rows = mysqli_fetch_array($querys);
            $n_po = isset($rows['no_po']) ? $rows['no_po'] : null;
            $stat = isset($rows['status']) ? $rows['status'] : null;
            // echo $n_po;
            $cancel_date = isset($rows['cancel_date']);                
            if($po == $n_po && $stat != 'Cancel'){
                echo '';
            }else{                    
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                            <td style="width:100px;">
                            <input type="text" style="font-size: 14px;" class="form-control" id="txt_pi" name="txt_pi" value="" disabled>
                            </td>                            
                            <td style="width:100px;" value="'.$row['podate'].'">'.date("d-M-Y",strtotime($row['podate'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>                            
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td style="width:50px;" value="'.$row['matauang'].'">'.$row['matauang'].'</td>                            
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>                                                                                                                
                        </tr>';
                        
                }
                }                
                    ?>
            </tbody>                    
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 100px;"><b>Subtotal</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 14px;text-align:right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Tax (PPn)</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pajak" id="pajak" value="" placeholder="0.00" style="font-size: 14px;text-align:right;" readonly>
            </div>
            </div>          
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 100px;"><b>Total</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px;text-align:right;" readonly>
            </div>
            </div>
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='ftrcbd.php'"><span class="fa fa-angle-double-left"></span> Back</button>               
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalpo" data-target="#mymodalpo" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_po"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                            
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
    $("input[type=checkbox]").click(function(){
    var sum_sub = 0;
    var sum_tax = 0;
    var ceklist = 0;
    var sum_total = 0;
    $(this).closest('tr').find('td:eq(2) input').prop('disabled', true);             
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) ||0;
    var select_pi = $(this).closest('tr').find('td:eq(2) input');
    select_pi.prop('disabled', false);                    
    sum_sub += price;
    sum_tax += tax;
    sum_total = sum_sub + sum_tax;     
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#pajak").val(formatMoney(sum_tax));    
    $("#total").val(formatMoney(sum_total));
    $("#select").val("1");                    
});        
</script>

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

<!-- <script type="text/javascript">
    $("input[type=radio]:checked").click(function(){        
    $(this).closest('tr').find('td:eq(2) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(2) input').val("");        
    })
</script> -->

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {
        var noftrcbd = document.getElementById('noftrcbd').value;        
        var tglftrcbd = document.getElementById('tanggal').value;
        var keterangan = document.getElementById('memo').value;        
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var curr = $(this).closest('tr').find('td:eq(7)').attr('value');                               
        var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_pi = $(this).closest('tr').find('td:eq(2) input').val();
        var tgl_po = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = '<?php echo $user; ?>';         
        var sub = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
        var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
        var total = parseFloat($(this).closest('tr').find('td:eq(6)').attr('data-total'),10) ||0;
        var sum_sub = 0;
        var sum_tax = 0;
        var sum_total = 0;       
        sum_sub += sub;
        sum_tax += tax;       
        sum_total += total;
        if(no_pi != ''){
        $.ajax({
            type:'POST',
            url:'insertftrcbd.php',
            data: {'noftrcbd':noftrcbd, 'tglftrcbd':tglftrcbd, 'keterangan':keterangan, 'no_po':no_po, 'no_pi':no_pi, 'tgl_po':tgl_po, 'nama_supp':nama_supp, 'curr':curr, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_tax':sum_tax, 'sum_total':sum_total},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert("Data saved successfully");
                window.location = 'ftrcbd.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }else{
        $(this).closest('tr').find('td:eq(2) input').focus();
        alert("PI number can't be empty");
    }
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please check the PO number");
        }else{
            alert("Data saved successfully"); 
        }
    });
</script>

<!--<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>-->

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalpo').modal('show');
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(3)').text();
    var supp = $(this).closest('tr').find('td:eq(8)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');   

    $.ajax({
    type : 'post',
    url : 'ajaxpocbd.php',
    data : {'no_po': no_po},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_po').html(no_po);
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');
    $('#txt_supp2').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');                               
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
