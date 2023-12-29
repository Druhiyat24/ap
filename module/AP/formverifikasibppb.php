<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">VERIFIKASI BPPB</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="formverifikasibppb.php" method="post">
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

            <div class="col-md-3 mb-3"> 
            <label for="start_date"><b>From</b></label>          
            <input type="text" style="font-size: 12px;" class="form-control tanggal" id="start_date" name="start_date" 
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
            placeholder="Tanggal Awal" onchange="this.form.submit()">
            </div>

            <div class="col-md-3 mb-3"> 
            <label for="end_date"><b>To</b></label>          
            <input type="text" style="font-size: 12px;" class="form-control tanggal" id="end_date" name="end_date" 
            value="<?php
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
               $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }
            if(!empty($_POST['end_date'])) {
               echo $_POST['end_date'];
            }
            else{
               echo date("d-m-Y");
            } ?>" 
            placeholder="Tanggal Awal" onchange="this.form.submit()">
            </div>           
</div>                   
    </div>
</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">
                <div class="col-md-12">
                <div class="container-1">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search no bpb return..">
                </div>
                </div>
            </br>
        </br>
                <div class="tableFix" style="height: 500px;">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="width:100px;">No RO</th>                                                                                
                            <th style="width:50px;">No BPPB</th>
                            <th style="width:100px;">BPPB Date</th>
                            <th style="width:100px;">Supplier</th>
                            <th style="width:100px;">No BPB</th>
                            <th style="width:100px;display: none;">Currency</th>
                            <th style="width:100px;display: none;">Confirm</th>                                             
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
            $start_date ='';
            $end_date ='';            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));            
            }

            if($nama_supp == 'ALL'){
            $sql = mysql_query("select bppb.bppbno_int as bppbno_int, bppb.bppbdate as bppbdate, IF(bppb.bpbno_ro != '',bppb.bpbno_ro,bppb.bppbno_int) as bpbno_ro, sum(bppb.qty * bppb.price) as total, mastersupplier.Supplier as Supplier, bppb.curr as curr, bppb.confirm_by as confirm_by from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier where confirm = 'Y' and cancel != 'Y' and  bppb.bppbdate between '$start_date' and '$end_date' and bppb.ap_inv is null and tipe_sup = 'S' group by bppbno_int",$conn1);                
            }else {
            $sql = mysql_query("select bppb.bppbno_int as bppbno_int, bppb.bppbdate as bppbdate, IF(bppb.bpbno_ro != '',bppb.bpbno_ro,bppb.bppbno_int) as bpbno_ro, sum(bppb.qty * bppb.price) as total, mastersupplier.Supplier as Supplier, bppb.curr as curr, bppb.confirm_by as confirm_by from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier where confirm = 'Y' and cancel != 'Y' and Supplier='$nama_supp' and bppb.bppbdate between '$start_date' and '$end_date' and bppb.ap_inv is null and tipe_sup = 'S' group by bppbno_int",$conn1);
            }


            // if($nama_supp == 'ALL'){
            // $sql = mysql_query("select bppb.bppbno_int as bppbno_int, bppb.bppbdate as bppbdate, IF(bppb.bpbno_ro != '',bppb.bpbno_ro,bppb.bppbno_int) as bpbno_ro, sum(bppb.qty * bppb.price) as total, mastersupplier.Supplier as Supplier, bppb.curr as curr, bppb.confirm_by as confirm_by from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier where confirm = 'Y' and cancel != 'Y' and bppb.bpbno_ro != '' and bppb.bppbdate between '$start_date' and '$end_date' and bppb.ap_inv is null || confirm = 'Y' and cancel != 'Y' and bppb.id_bpb != '' and bppb.bppbdate between '$start_date' and '$end_date' and bppb.ap_inv is null group by bppbno_int",$conn1);                
            // }else {
            // $sql = mysql_query("select bppb.bppbno_int as bppbno_int, bppb.bppbdate as bppbdate, IF(bppb.bpbno_ro != '',bppb.bpbno_ro,bppb.bppbno_int) as bpbno_ro, sum(bppb.qty * bppb.price) as total, mastersupplier.Supplier as Supplier, bppb.curr as curr, bppb.confirm_by as confirm_by from bppb inner join mastersupplier on mastersupplier.Id_Supplier = bppb.id_supplier where confirm = 'Y' and cancel != 'Y' and bppb.bpbno_ro != '' and Supplier='$nama_supp' and bppb.bppbdate between '$start_date' and '$end_date' and bppb.ap_inv is null || confirm = 'Y' and cancel != 'Y' and bppb.id_bpb != '' and Supplier='$nama_supp' and bppb.bppbdate between '$start_date' and '$end_date' and bppb.ap_inv is null group by bppbno_int",$conn1);
            // }
                            
            if (!empty($nama_supp && $start_date && $end_date)) {                                              
                while($row = mysql_fetch_array($sql)){
                   $no_ro = $row['bpbno_ro'];
                   $no_bppb = $row['bppbno_int'];
                   if(strpos($no_bppb, 'WIP/OUT') !== false) {
                    $bpbno = '-';
                    }else{
                    $querys = mysql_query("select bpbno_int as bpb_no from bpb where bpbno = '$no_ro'",$conn1);
                    $rows = mysql_fetch_array($querys);  
                    $bpbno = isset($rows['bpb_no']) ? $rows['bpb_no'] : '-';
                    }
                                           
                    echo'<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:100px;" value="'.$row['bpbno_ro'].'">'.$row['bpbno_ro'].'</td>                                                                                                        
                            <td style="width:50px;" value="'.$row['bppbno_int'].'">'.$row['bppbno_int'].'</td>
                            <td style="width:100px;" value="'.$row['bppbdate'].'">'.date("d-M-Y",strtotime($row['bppbdate'])).'</td>
                            <td style="width:50px;" value="'.$row['Supplier'].'">'.$row['Supplier'].'</td>
                            <td style="width:50px;" value="'.$bpbno.'">'.$bpbno.'</td>
                            <td style="width:50px;display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['confirm_by'].'">'.$row['confirm_by'].'</td>                                                                                    
                        </tr>';
                    }
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
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                     
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
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='verifikasibppb.php'"><span class="fa fa-angle-double-left"></span> Back</button>               
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

<!-- <script>
    $(document).ready(function() {
    $('#mytable').dataTable({
        "bFilter": false,
    });
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>
 -->
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
    td = tr[i].getElementsByTagName("td")[2];
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
        startDate : "01-01-2021",
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
    var no_bpb = $(this).closest('tr').find('td:eq(2)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(3)').text();
    var no_ro = $(this).closest('tr').find('td:eq(2)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(4)').attr('value');
    var top = $(this).closest('tr').find('td:eq(5)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(6)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(7)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(8)').text();

    $.ajax({
    type : 'post',
    url : 'ajaxbppb.php',
    data : {'no_bpb': no_bpb, 'no_ro':no_ro},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_ro);
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('No BPB : ' + top + '');
    $('#txt_curr').html('Currency : ' + curr + '');                       
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
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {        
        var ceklist = document.getElementById('select').value;         
        var no_bppb = $(this).closest('tr').find('td:eq(2)').attr('value');
        var create_user = '<?php echo $user ?>';
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;
        var no_ro = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(5)').attr('value');       
        var confirm_by = $(this).closest('tr').find('td:eq(7)').attr('value');   

        $.ajax({
            type:'POST',
            url:'insertfmvbppb.php',
            data: {'no_bppb':no_bppb, 'ceklist':ceklist, 'create_user':create_user, 'start_date':start_date, 'end_date':end_date, 'no_ro':no_ro, 'no_bpb':no_bpb, 'confirm_by':confirm_by},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'verifikasibppb.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data saved successfully");
        }else{
            alert("Please check the BPB number");
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
