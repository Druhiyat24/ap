<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">APPROVE LIST PAYMENT</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="formapprovelp.php" method="post">
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
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search no list payment..">
                </div>
                    <div class="card-body table-responsive p-0" style="height: 400px;">
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="thead-dark">
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="text-align: center;vertical-align: middle;width: 120px">No List Payment</th>
                            <th style="text-align: center;vertical-align: middle;width: 130px">List Payment Date</th>
                            <th style="text-align: center;vertical-align: middle;width: 250px">Supplier</th>
                            <th style="text-align: center;vertical-align: middle;width: 130px">Total Kontrabon</th>
                            <th style="text-align: center;vertical-align: middle;">Amount</th>
                            <th style="text-align: center;vertical-align: middle;">Outstanding</th>
                            <th style="text-align: center;vertical-align: middle;width: 60px">Currency</th>
                            <th style="text-align: center;vertical-align: middle; width: 70px">Create By</th>
                            <th style="text-align: center;vertical-align: middle; width: 60px">Status</th>
                            <th style="text-align: center;vertical-align: middle;display: none">TOP</th>             
                            <th style="text-align: center;vertical-align: middle;display: none">Tgl Jatuh Tempo</th> 
                            <th style="text-align: center;vertical-align: middle;display: none">TOP</th>             
                            <th style="text-align: center;vertical-align: middle;display: none">Tgl Jatuh Tempo</th> 
                            <th style="text-align: center;vertical-align: middle;display: none">TOP</th>             
                            <th style="text-align: center;vertical-align: middle;display: none">Tgl Jatuh Tempo</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Action</th>                       
                            <th style="text-align: center;vertical-align: middle;display: none;">Keterangan</th>  
                            <th style="text-align: center;vertical-align: middle;display: none;">Keterangan</th>                                                                                                                
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            
            }

            if(empty($nama_supp) or $nama_supp == 'ALL'){
            $sql = mysqli_query($conn2,"select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total_kbon, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where status = 'draft' group by no_payment
                union
 select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,'0' as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,due_date as tgl_tempo,'' as memo from saldo_awal where status = 'draft' and no_pay not like '%LP/NAG%' group by no_pay");                
            }else {
            $sql = mysqli_query($conn2,"select no_payment, tgl_payment, nama_supp, no_bpb, tgl_bpb, no_po, tgl_po,no_kbon, tgl_kbon, pph_value, SUM(total_kbon) as total_kbon, SUM(outstanding) as outstanding, SUM(amount) as amount, curr, create_user, status, top, tgl_tempo, memo from list_payment where nama_supp = '$nama_supp' and status = 'draft' group by no_payment
                union
 select no_pay as no_payment, tgl_pay as tgl_payment, supplier as nama_supp, '-' as no_bpb, '' as tgl_bpb, '-' as no_po, '' as tgl_po, '-' as no_kbon, '' as tgl_kbon, pph as pph_value, sum(total) as total,'0' as outstanding, sum(total) as amount,valuta as curr, '-' as create_user,`status`,'' as top,due_date as tgl_tempo,'' as memo from saldo_awal where status = 'draft' and no_pay not like '%LP/NAG%' and supplier = '$nama_supp' group by no_pay");
            }
                                                                         
            while($row = mysqli_fetch_array($sql)){ 
            $tgl_pay = $row['tgl_payment']; 
            if ($tgl_pay != '') {
                $tgl_payment = date("d-M-Y",strtotime($row['tgl_payment']));
                } else{
                  $tgl_payment = '-';  
                }                                    
                    echo'<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                           <td style="width: 100px;" value = "'.$row['no_payment'].'">'.$row['no_payment'].'</td>
                            <td style="width: 100px;" value = "'.$tgl_payment.'">'.$tgl_payment.'</td>
                            <td style="width: 100px;" value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style="text-align:right;width: 100px;" value = "'.$row['total_kbon'].'">'.number_format($row['total_kbon'],2).'</td>
                            <td style="text-align:right;width: 100px;" value = "'.$row['amount'].'">'.number_format($row['amount'],2).'</td>
                            <td style="text-align:right;width: 100px;" value = "'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td>
                            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
                            <td value = "'.$row['create_user'].'">'.$row['create_user'].'</td>
                            <td value = "'.$row['status'].'">'.$row['status'].'</td>
                            <td style="display: none;" value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
                            <td style="display: none;" value = "'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                            <td style="display: none;" value = "'.$row['no_po'].'">'.$row['no_po'].'</td>
                            <td style="display: none;" value = "'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>
                            <td style="display: none;" value = "'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="display: none;" value = "'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                            <td style="display: none;" value = "'.$row['pph_value'].'">'.number_format($row['pph_value'],2).'</td>
                            <td style="display: none;" value = "'.$row['memo'].'">'.$row['memo'].'</td>                         
                        </tr>';                
                   
                    } ?>
                    </tbody>
                    </table> 
                    </div>                    

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_bpb"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_list_payment" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_keterangan" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                                    
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
            <button style="border-radius: 10px" type="button" class="btn-outline-primary" name="approve" id="approve"><span class="fa fa-thumbs-up"></span> Approve</button>                
            <button style="border-radius: 10px" type="button" class="btn-outline-danger" name="cancel" id="cancel"><span class="fa fa-ban"></span> Cancel</button>           
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
        "bFilter": false,
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

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(-1)', function(){                
    $('#mymodal').modal('show');
    var no_payment = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_list_payment = $(this).closest('tr').find('td:eq(2)').text();
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(10)').text();
    var curr = $(this).closest('tr').find('td:eq(6)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(7)').attr('value');
    var status = $(this).closest('tr').find('td:eq(8)').attr('value');
    var top = $(this).closest('tr').find('td:eq(9)').attr('value');
    var keterangan = $(this).closest('tr').find('td:eq(16)').attr('value');               

    $.ajax({
    type : 'post',
    url : 'ajaxlistpayment.php',
    data : {'no_payment': no_payment},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_list_payment').html(no_payment);
    $('#txt_tgl_list_payment').html('Tgl List Payment : ' + tgl_list_payment + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_keterangan').html('Keterangan : ' + keterangan + '');                                        
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
       var no_payment = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_payment = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
        var amount = $(this).closest('tr').find('td:eq(6)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
        var no_kbon = $(this).closest('tr').find('td:eq(10)').attr('value');
        var tgl_kbon = $(this).closest('tr').find('td:eq(11)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(12)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(13)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(14)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(15)').attr('value');
        var pph = $(this).closest('tr').find('td:eq(16)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvelistpayment.php',
            data: {'no_payment':no_payment, 'approve_user':approve_user, 'tgl_payment':tgl_payment, 'supp':supp, 'amount':amount, 'curr':curr, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'no_po':no_po, 'tgl_po':tgl_po, 'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb, 'pph':pph},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'formapprovelp.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data Berhasil Di Approve");
        }else{
            alert("Silahkan Ceklist No BPB Dahulu");
        }        
    });
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#cancel", function(){
        $("input[type=checkbox]:checked").each(function () {                     
        var no_payment = $(this).closest('tr').find('td:eq(1)').attr('value');
        var cancel_user = '<?php echo $user ?>';
        $.ajax({
            type:'POST',
            url:'cancellistpayment.php',
            data: {'no_payment':no_payment, 'cancel_user':cancel_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'formapprovelp.php';                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data Berhasil Di Cancel");
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
