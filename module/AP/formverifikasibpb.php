<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">VERIFIKASI BPB</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="formverifikasibpb.php" method="post">
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
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search no bpb..">
                </div>
            </div>
            </br>
        </br>
           <div class="tableFix" style="height: 500px;">
            <table id="mytable" class="table table-head-fixed text-nowrap" cellspacing="0" width="100%" style="font-size: 13px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="width:50px;">No BPB</th>
                            <th style="width:100px;">Tgl BPB</th>
                            <th style="width:100px;">No PO</th>
                            <th style="width:100px;">From BPB</th>                                                                                
                            <th style="width:100px;">Supplier</th>
                            <th style="width:100px;">TOP</th>
                            <th style="width:100px;display: none;">Currency</th>
                            <th style="width:100px;display: none;">Confirm</th>
                            <th style="width:100px;display: none;">Tgl PO</th>
                            <th style="width:100px;">Payment Terms</th>                                                                                                                                                                        
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
            $sql = mysql_query("select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, masterpterms.kode_pterms, bpb.curr, bpb.confirm_by, po_header.podate, po_header_draft.tipe_com
                from bpb 
                INNER JOIN po_header on po_header.pono = bpb.pono 
                left JOIN po_header_draft on po_header_draft.id = po_header.id_draft
                INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier 
                inner join masterpterms on masterpterms.id = po_header.id_terms 
                where bpb.confirm='Y' and bpb.cancel='N' and bpb.bpbdate between '$start_date' and '$end_date' and po_header_draft.tipe_com is null || bpb.confirm='Y' and bpb.cancel='N' and bpb.bpbdate between '$start_date' and '$end_date' and po_header_draft.tipe_com = 'REGULAR' || bpb.confirm='Y' and bpb.cancel='N' and bpb.bpbdate between '$start_date' and '$end_date' and po_header_draft.tipe_com = 'BUYER' group by bpb.bpbno_int",$conn1);                
            }else {
            $sql = mysql_query("select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, masterpterms.kode_pterms, bpb.curr, bpb.confirm_by, po_header.podate, po_header_draft.tipe_com
                from bpb 
                INNER JOIN po_header on po_header.pono = bpb.pono 
                left JOIN po_header_draft on po_header_draft.id = po_header.id_draft
                INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier 
                inner join masterpterms on masterpterms.id = po_header.id_terms 
                where bpb.confirm='Y' and bpb.cancel='N' and Supplier='$nama_supp' and bpb.bpbdate between '$start_date' and '$end_date' and po_header_draft.tipe_com is null || bpb.confirm='Y' and bpb.cancel='N' and Supplier='$nama_supp' and bpb.bpbdate between '$start_date' and '$end_date' and po_header_draft.tipe_com = 'REGULAR' || bpb.confirm='Y' and bpb.cancel='N' and Supplier='$nama_supp' and bpb.bpbdate between '$start_date' and '$end_date' and po_header_draft.tipe_com = 'BUYER' group by bpb.bpbno_int",$conn1);
            }
                            
            if (!empty($nama_supp && $start_date && $end_date)) {                                              
                while($row = mysql_fetch_array($sql)){
                    $bpb = $row['bpbno_int'];
                    $querys = mysql_query("select distinct (no_bpb) from bpb_new where no_bpb = '$bpb' and status != 'Cancel'",$conn2);
                    $rows = mysql_fetch_array($querys);
                    $n_bpb = isset($rows['no_bpb']);

                    $querys12 = mysql_query("select DISTINCT bppbno from bpb where bpbno_int = '$bpb'",$conn1);
                    $rows12 = mysql_fetch_array($querys12);
                    $bppbno1 = isset($rows12['bppbno']) ? $rows12['bppbno'] : null;
                    
                    $querys23 = mysql_query("select DISTINCT bppbno_int, bpbno_ro from bppb where bppbno = '$bppbno1'",$conn1);
                    $rows23 = mysql_fetch_array($querys23);
                    $bpbno_ro2 = isset($rows23['bpbno_ro']) ? $rows23['bpbno_ro'] : null;

                    $querys34 = mysql_query("select DISTINCT bpbno_int from bpb where bpbno = '$bpbno_ro2'",$conn1);
                    $rows34 = mysql_fetch_array($querys34);
                    $bpbno_int3 = isset($rows34['bpbno_int']) ? $rows34['bpbno_int'] : null;
                    if ($bpbno_int3 != '') {
                        $bpbno_int4 = $bpbno_int3;
                    }else{
                        $bpbno_int4 = '-';
                    }
            if($bpb == $n_bpb) {
            echo '';
            }else{                              
                    echo'<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
                            <td style="width:100px;" value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
                            <td style="width:100px;" value="'.$row['pono'].'">'.$row['pono'].'</td>
                            <td style="width:100px;" value="'.$bpbno_int4.'">'.$bpbno_int4.'</td>                                                                                                          
                            <td style="width:50px;" value="'.$row['Supplier'].'">'.$row['Supplier'].'</td>
                            <td style="width:50px;" value="'.$row['jml_pterms'].'">'.$row['jml_pterms'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['confirm_by'].'">'.$row['confirm_by'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['podate'].'">'.date("d-M-Y",strtotime($row['podate'])).'</td>
                            <td style="width:50px;" value="'.$row['kode_pterms'].'">'.$row['kode_pterms'].'</td>                                                                                    
                        </tr>';
                    }
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
          <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
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
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='verifikasibpb.php'"><span class="fa fa-angle-double-left"></span> Back</button>               
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
<!-- 
<script>
    $(document).ready(function() {
    $('#mytable').dataTable({
        "bFilter": false,
    });
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

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
        startDate : "01-01-2019",
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
    var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(3)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(5)').attr('value');
    var top = $(this).closest('tr').find('td:eq(6)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(7)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(8)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(9)').text();

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
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By : ' + confirm + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                    
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
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_bpb_asal = $(this).closest('tr').find('td:eq(4)').attr('value');
        var create_user = '<?php echo $user ?>';
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;
        var tgl_po = $(this).closest('tr').find('td:eq(9)').attr('value');
        var pterms = $(this).closest('tr').find('td:eq(10)').attr('value');        

        $.ajax({
            type:'POST',
            url:'insertfmvbpb.php',
            data: {'no_bpb':no_bpb, 'no_bpb_asal':no_bpb_asal, 'ceklist':ceklist, 'create_user':create_user, 'start_date':start_date, 'end_date':end_date, 'tgl_po':tgl_po, 'pterms':pterms},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'verifikasibpb.php';
                                               
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
