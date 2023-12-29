<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">APPROVE KONTRA BON REGULAR</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="app_kbonreg.php" method="post">
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
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="thead-dark">
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="text-align: center;vertical-align: middle;">No Kontra Bon</th>
                            <th style="text-align: center;vertical-align: middle;">Kontra Bon Date</th>
                            <th style="text-align: center;vertical-align: middle;">Supplier</th>
                            <th style="text-align: center;vertical-align: middle;">SubTotal</th>
                            <th style="text-align: center;vertical-align: middle;">Tax (PPn)</th>
                            <th style="text-align: center;vertical-align: middle;">Tax (PPh)</th>            
                            <th style="text-align: center;vertical-align: middle;">Total CBD/DP</th>
                            <th style="text-align: center;vertical-align: middle;">Potongan</th>
                            <th style="text-align: center;vertical-align: middle;">Total KB</th>
                            <th style="text-align: center;vertical-align: middle;">Currency</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Create By</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Status</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Jatuh Tempo</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Tgl No Faktur</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">No Supplier Invocie</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Tgl Supplier Invocie</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">PPh Code</th>                                                                                                                
                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            
            }

            if(empty($nama_supp) or $nama_supp == 'ALL'){
            $sql = mysqli_query($conn3,"select a.no_kbon, a.tgl_kbon, a.no_bpb,a.no_po,a.tgl_bpb,a.tgl_po, a.nama_supp, SUM(a.subtotal), SUM(a.tax) as tax, SUM(a.total), a.curr, a.create_user, a.status, a.tgl_tempo, a.no_faktur, a.supp_inv, a.tgl_inv, a.pph_code, SUM(a.pph_value) as pph_value, a.dp_value, a.pph_code, b.jml_return, b.jml_potong from kontrabon a INNER JOIN potongan b on b.no_kbon = a.no_kbon where a.no_bpb != '' AND a.status = 'draft' group by a.no_kbon");                
            }else {
            $sql = mysqli_query($conn3,"select a.no_kbon, a.tgl_kbon, a.no_bpb,a.no_po,a.tgl_bpb,a.tgl_po, a.nama_supp, SUM(a.subtotal), SUM(a.tax) as tax, SUM(a.total), a.curr, a.create_user, a.status, a.tgl_tempo, a.no_faktur, a.supp_inv, a.tgl_inv, a.pph_code, SUM(a.pph_value) as pph_value, a.dp_value, a.pph_code, b.jml_return, b.jml_potong from kontrabon a INNER JOIN potongan b on b.no_kbon = a.no_kbon where a.no_bpb != '' AND a.status = 'draft' and a.nama_supp = '$nama_supp' group by a.no_kbon");
            }
                                                                         
            while($row = mysqli_fetch_array($sql)){ 
            $sub = $row['SUM(a.subtotal)'];
            $tax = $row['tax'];
            $pph = $row['pph_value'];
            $dp = $row['dp_value'];
            $return = $row['jml_return'];
            $potong = $row['jml_potong'];
            $total = $sub + $tax - ($pph + $dp + $return) + $potong ;
            $ttl_potong = $return - $potong;                                      
                    echo'<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                           <td value = "'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
                            <td value = "'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style="text-align:right;" value = "'.$row['SUM(a.subtotal)'].'">'.number_format($row['SUM(a.subtotal)'],2).'</td>
                            <td style="text-align:right;" value = "'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                            <td style="text-align:right;" value = "'.$row['pph_value'].'">- '.number_format($row['pph_value'],2).'</td>
                            <td style="text-align:right;" value = "'.$row['dp_value'].'">- '.number_format($row['dp_value'],2).'</td>            
                            <td style="text-align:right;" value = "'.$ttl_potong.'">'.number_format($ttl_potong,2).'</td>
                            <td style="text-align:right;" value = "'.$total.'">'.number_format($total,2).'</td>
                            <td value = "'.$row['curr'].'">'.$row['curr'].'</td> 
                            <td style="display: none;" value = "'.$row['create_user'].'">'.$row['create_user'].'</td>
                            <td style="display: none;" value = "'.$row['status'].'">'.$row['status'].'</td>
                            <td value = "'.$row['tgl_bpb'].'" style="display: none;">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>
                            <td value = "'.$row['no_po'].'" style="display: none;">'.$row['no_po'].'</td>
                            <td value = "'.$row['no_bpb'].'" style="display: none;">'.$row['no_bpb'].'</td>
                            <td value = "'.$row['tgl_po'].'" style="display: none;">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>
                            <td value = "'.$row['pph_code'].'" style="display: none;">'.$row['pph_code'].'</td> 
                            <td value = "'.$row['tgl_tempo'].'" style="display: none;">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>
                            <td value = "'.$row['no_faktur'].'" style="display: none;">'.$row['no_faktur'].'</td>
                            <td value = "'.$row['supp_inv'].'" style="display: none;">'.$row['supp_inv'].'</td>
                            <td value = "'.$row['tgl_inv'].'" style="display: none;">'.date("d-M-Y",strtotime($row['tgl_inv'])).'</td>
                            <td value = "'.$row['pph_code'].'" style="display: none;">'.$row['pph_code'].'</td>                          
                        </tr>';                
                   
                    } ?>
                    </tbody>
                    </table>                     

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
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
          <!-- <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
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

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodal').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(2)').text();
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(18)').text();
    var curr = $(this).closest('tr').find('td:eq(10)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(11)').attr('value');
    var status = $(this).closest('tr').find('td:eq(12)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(19)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(20)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(21)').text(); 
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
    $('#txt_tgl_kbon').html('Kontrabon Date : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Due Date : ' + tgl_tempo + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Supplier Invoice Date : ' + tgl_inv + '');                
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
        var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_kbon = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
        var pph = $(this).closest('tr').find('td:eq(6)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(10)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(13)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(14)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(15)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(16)').attr('value');
        var total = $(this).closest('tr').find('td:eq(9)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvekbon.php',
            data: {'no_kbon':no_kbon, 'approve_user':approve_user, 'tgl_kbon':tgl_kbon, 'supp':supp, 'pph':pph, 'curr':curr, 'tgl_bpb':tgl_bpb, 'no_po':no_po, 'no_bpb':no_bpb, 'tgl_po':tgl_po, 'total':total},

            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'app_kbonreg.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Approved Successfully");
        }else{
            alert("please check the kontrabon number");
        }        
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#approve", function(){                 
        var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_kbon = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
        var pph = $(this).closest('tr').find('td:eq(6)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(10)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(13)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(14)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(15)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(16)').attr('value');
        var total = $(this).closest('tr').find('td:eq(9)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approvekbon.php',
            data: {'no_kbon':no_kbon, 'approve_user':approve_user, 'tgl_kbon':tgl_kbon, 'supp':supp, 'pph':pph, 'curr':curr, 'tgl_bpb':tgl_bpb, 'no_po':no_po, 'no_bpb':no_bpb, 'tgl_po':tgl_po, 'total':total},
            // close: function(e){
            //     e.preventDefault();
            // },
            success: function(data){                
                console.log(data);
                window.location.reload();                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
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
