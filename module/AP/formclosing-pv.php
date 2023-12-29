<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">CLOSING PAYMENT VOUCHER</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="formclosing-pv.php" method="post">
        <div class="form-row">
            <!-- <div class="col-md-6">
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
                </div>   -->

           
</div>                   
    </div>
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">
                <div class="col-md-12">
                <div class="container-1">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search no pv..">
                </div>
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="thead-dark">
                            <th style="width:5px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="width:100px;">No Payment Voucher</th>
                            <th style="width:70px;">PV Date</th>
                            <th style="width:70px;">Due Date</th>
                            <th style="width:50px;">Curreny</th>                                                                                
                            <th style="width:80px;">Amount</th>
                            <th style="width:80px;">Outstanding</th>
                            <th style="display: none;">-</th>
                            <th style="display: none;">-</th>
                            <th style="display: none;">-</th>
                            <th style="display: none;">-</th>
                            <th style="display: none;">-</th>
                            <th style="display: none;">-</th>
                            <th style="display: none;">-</th>
                            <th style="display: none;">-</th>                                                                                                                
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
           
            // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            
            // }

            // if(empty($nama_supp) or $nama_supp == 'ALL'){
            // $sql = mysqli_query($conn2,"select id, no_bpb, tgl_bpb, create_date, pono, supplier, top, curr, confirm1, tgl_po, SUM((qty * price) + ((qty * price) * (tax /100))) as total from bpb_new where confirm2 = '' and status != 'Cancel' group by no_bpb");                
            // }else {
            $sql = mysqli_query($conn2,"select a.no_pv,a.pv_date,max(b.due_date) as due_date,if(a.curr = '','-',a.curr) as curr ,a.total,a.outstanding,a.status,a.nama_supp as pay_to,a.pay_date,a.pay_meth,a.frm_akun, a.to_akun,if(a.no_cek = '','-',a.no_cek) as no_cek,a.cek_date,a.for_pay from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv where a.status = 'Approved' group by a.no_pv");
            // }
                                                                         
            while($row = mysqli_fetch_array($sql)){
            $duedate = $row['due_date'];
            $cekdate = $row['cek_date'];
            if ($duedate == '' || $duedate == '1970-01-01') { 
             $due_date = '-';
            }else{
                $due_date = date("d-M-Y",strtotime($row['due_date'])); 
            } 
            if ($cekdate == '' || $cekdate == '1970-01-01') { 
             $cek_date = '-';
            }else{
                $cek_date = date("d-M-Y",strtotime($row['cek_date'])); 
            }                                        
                    echo'<tr>
                            <td style="width:5px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px; text-align : center" value="'.$row['no_pv'].'">'.$row['no_pv'].'</td>
                            <td style="width:100px; text-align : center" value="'.date("d-M-Y",strtotime($row['pv_date'])).'">'.date("d-M-Y",strtotime($row['pv_date'])).'</td> 
                            <td style="width:100px; text-align : center" value="'.$due_date.'">'.$due_date.'</td>                                                                                             
                            <td style="width:50px; text-align : center" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px; text-align : right" value="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                            <td style="width:50px; text-align : right" value="'.$row['outstanding'].'">'.number_format($row['outstanding'],2).'</td> 
                            <td style="display: none; text-align : center" value="'.$row['pay_to'].'">'.$row['pay_to'].'</td>
                            <td style="display: none; text-align : center" value="'.date("d-M-Y",strtotime($row['pay_date'])).'">'.date("d-M-Y",strtotime($row['pay_date'])).'</td>
                            <td style="display: none; text-align : center" value="'.$row['pay_meth'].'">'.$row['pay_meth'].'</td>
                            <td style="display: none; text-align : center" value="'.$row['frm_akun'].'">'.$row['frm_akun'].'</td>
                            <td style="display: none; text-align : center" value="'.$row['to_akun'].'">'.$row['to_akun'].'</td>
                            <td style="display: none; text-align : center" value="'.$row['no_cek'].'">'.$row['no_cek'].'</td>
                            <td style="display: none; text-align : center" value="'.$cek_date.'">'.$cek_date.'</td>          
                            <td style="display: none; text-align : center" value="'.$row['for_pay'].'">'.$row['for_pay'].'</td>                 
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
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp1" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm1" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>          
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
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodal').modal('show');
    var no_pv = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_pv = $(this).closest('tr').find('td:eq(2)').text();
    var pay_to = $(this).closest('tr').find('td:eq(7)').attr('value');
    var pay_date = $(this).closest('tr').find('td:eq(8)').attr('value');
    var pay_meth = $(this).closest('tr').find('td:eq(9)').attr('value');
    var f_akun = $(this).closest('tr').find('td:eq(10)').attr('value');
    var t_akun = $(this).closest('tr').find('td:eq(11)').attr('value');
    var cek_no = $(this).closest('tr').find('td:eq(12)').attr('value');
    var cek_date = $(this).closest('tr').find('td:eq(13)').attr('value');
    var pay_for = $(this).closest('tr').find('td:eq(14)').attr('value');

    $.ajax({
    type : 'post',
    url : 'ajax_pv.php',
    data : {'no_pv': no_pv},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_pv);
    $('#txt_tglbpb').html('PV Date : ' + tgl_pv + '');
    $('#txt_top').html('Payment Method : ' + pay_meth + '');
    $('#txt_no_po').html('Payment To : ' + pay_to + '');
    $('#txt_supp1').html('Payment For : ' + pay_for + '');
    $('#txt_supp').html('Payment Date : ' + pay_date + '');
    $('#txt_curr').html('From Account : ' + f_akun + '');        
    $('#txt_confirm').html('To Account : ' + t_akun + '');
    $('#txt_confirm1').html('Cheque No : ' + cek_no + '');
    $('#txt_confirm2').html('Cheque Date : ' + cek_date + '');                
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
        var no_pv = $(this).closest('tr').find('td:eq(1)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvepv.php',
            data: {'no_pv':no_pv, 'approve_user':approve_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'approve-pv.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data Berhasil Di Approve");
        }else{
            alert("Silahkan Ceklist No PV ");
        }        
    });
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#cancel", function(){
        $("input[type=checkbox]:checked").each(function () {                     
        var no_pv = $(this).closest('tr').find('td:eq(1)').attr('value');
        var cancel_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancelpv.php',
            data: {'no_pv':no_pv, 'cancel_user':cancel_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'approve-pv.php';                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data Berhasil Di Cancel");
        }else{
            alert("Silahkan Ceklist No PV");
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
    document.getElementById('btnpv').onclick = function () {
    location.href = "approve-pv.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnib').onclick = function () {
    location.href = "approve-inbank.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnob').onclick = function () {
    location.href = "approve-outbank.php";
};
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
