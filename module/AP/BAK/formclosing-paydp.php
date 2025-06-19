<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">CLOSING PAYMENT DP</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="formclosing-paydp.php" method="post">
        <div class="form-row">
            <div class="col-md-6">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $nama_supp = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                    if($nama_supp == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>                                 
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
    </div>
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">
                <div class="col-md-12">
                <div class="container-1">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search No List Payment..">
                </div>
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="width:100px;">No List Payment</th>
                            <th style="width:100px;">List Payment Date</th>
                            <th style="width:200px;">Supplier</th>
                            <th style="width:100px;display: none;">Curr</th>                                                       
                            <th style="width:150px;">Total List Payment</th>
                            <th style="width:50px;">No Payment</th>
                            <th style="width:100px;">Payment Date</th>
                            <th style="width:100px;">Pay Method</th>
                            <th style="width:100px;display: none;">Currency</th>
                            <th style="width:100px;">Nominal</th>
                            <th style="width:100px;display: none;">Currency</th>
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            
            }

            if(empty($nama_supp) or $nama_supp == 'ALL'){
            $sql = mysqli_query($conn2,"select a.no_payment, a.tgl_payment, a.nama_supp, a.curr, sum(a.amount) as amount, a.status, b.payment_ftr_id, b.tgl_pelunasan, b.cara_bayar, b.account, b.bank, b.valuta_bayar, b.nominal, b.nominal_fgn, b.ttl_bayar from list_payment_dp a left join payment_ftrdp b on b.list_payment_id = a.no_payment where a.`status` = 'Approved' group by a.no_payment");                
            }else {
            $sql = mysqli_query($conn2,"select a.no_payment, a.tgl_payment, a.nama_supp, a.curr, sum(a.amount) as amount, a.status, b.payment_ftr_id, b.tgl_pelunasan, b.cara_bayar, b.account, b.bank, b.valuta_bayar, b.nominal, b.nominal_fgn, b.ttl_bayar from list_payment_dp a left join payment_ftrdp b on b.list_payment_id = a.no_payment where a.`status` = 'Approved' and a.nama_supp = '$nama_supp' group by a.no_payment");
            }
                                                                         
            while($row = mysqli_fetch_array($sql)){ 
            $curr = isset($row['valuta_bayar']) ? $row['valuta_bayar'] :null;
            $no_paymt = isset($row['payment_ftr_id']) ? $row['payment_ftr_id'] :null;

            if ($no_paymt == '') {
                $nom = '-';
                $nom1 = '-';
                $method = '-';
                $tgl_lunas = '-';
                $no_lunas = '-';
            }else{
                $method = $row['cara_bayar'];
                $tgl_lunas = date("d-M-Y",strtotime($row['tgl_pelunasan']));
                $no_lunas = $row['payment_ftr_id'];
            if ($curr == 'IDR') {
                $nom = isset($row['nominal']) ? $row['nominal'] :0;  
                $nom1 = number_format($nom,2);  
                } elseif($curr == 'USD'){
                    $nom = isset($row['nominal_fgn']) ? $row['nominal_fgn'] :0;
                    $nom1 = number_format($nom,2);
                }else{
                    $nom = isset($row['ttl_bayar']) ? $row['ttl_bayar'] :0;
                    $nom1 = number_format($nom,2);
                }     
                }                                     
                    echo'<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="" value="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
                            <td style="" value="'.$row['tgl_payment'].'">'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
                            <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>                                                                  
                            <td style="display:none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="" value="'.$row['amount'].'">'.$row['curr'].' '.number_format($row['amount'],2).'</td>                                      
                            <td style="" value="'.$no_lunas.'">'.$no_lunas.'</td>
                            <td style=";" value="'.$tgl_lunas.'">'.$tgl_lunas.'</td>
                            <td style=";" value="'.$method.'">'.$method.'</td>
                            <td style="display: none;" value="'.$row['valuta_bayar'].'">'.$row['valuta_bayar'].'</td>                            
                            <td style=";" value="'.$nom.'">'.$row['valuta_bayar'].' '.$nom1.'</td>
                            <td style="display:none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                        </tr>';                
                   
                    } ?>
                    </tbody>
                    </table>                      

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_bpb"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>          
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>                            
                    
<div class="box footer">   
        <form id="form-simpan">
           <div class="form-row col">
            <div class="col-md-3 mb-3">  
            </br>                            
            <button style="border-radius: 6px" type="button" class="btn-outline-danger" name="approve" id="approve"><span class="fa fa-times-circle"></span> Close Payment</button>            
            </div>
            </div>                                   
        </form>
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
    $('#mytable').dataTable({
        "bFilter" : false,
    });
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("mytable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
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

<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodal').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(7)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(4)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(6)').attr('value');

    $.ajax({
    type : 'post',
    url : 'ajaxpelunasandp.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_kbon);
    $('#txt_tglbpb').html('Payment Date : ' + tgl_bpb + '');
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_no_po').html('Currency : ' + no_po + '');
    $('#txt_top').html('Pay Method : ' + top + '');
    $('#txt_curr').html('Account : ' + curr + '');        
    $('#txt_confirm').html('Bank : ' + confirm + '');                
});

</script> -->

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
    var sub = 0;
    var tax = 0;
    var total = 0;
    var ceklist = 0;         
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(5)').attr('value'),10) || 0;
    var qty = parseFloat($(this).closest('tr').find('td:eq(7)').attr('value'),10) ||0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(8)').attr('value'),10) ||0;               
    sub += price * qty;
    tax += tax;
    total = sub + tax;     
    });
    $("#subtotal").val(formatMoney(sub));
    $("#pajak").val(formatMoney(tax));
    $("#total").val(formatMoney(total));
    $("#select").val("1");                    
});        
</script>

<!--<script type="text/javascript">
$(document).ready(function(){
    $("#supp").on("change", function(){
        var supp= $('select[name=supp] option').filter(':selected').val();
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'supp':supp},
            close: function(e){
                e.preventDefault();
            },
            success: function(html){
                console.log(html);
                $("#no_po").html(html);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });            
        });
    });    
</script>-->

<script type="text/javascript">
    $("#form-simpan").on("click", "#approve", function(){
        $("input[type=checkbox]:checked").each(function () {                
        var no_pay = $(this).closest('tr').find('td:eq(1)').attr('value');
        var update_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'closeddp.php',
            data: {'no_pay':no_pay, 'update_user':update_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'formclosing-paydp.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Payment Closed");
        }else{
            alert("please check the payment number");
        }        
    });
</script>


<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
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
