<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM MAINTAIN KONTRABON</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            
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
        <h4 class="modal-title" id="Heading">Select Data</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
            <label for="nama_supp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Choose Supplier</option>                
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

                <label><b>Kontrabon Date</b></label>
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
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>
                           <th style="text-align: center;vertical-align: middle;">No Kontra Bon</th>
                           <th style="text-align: center;vertical-align: middle; display: none;">No BPB</th>
                            <th style="text-align: center;vertical-align: middle;">Tanggal Kontra Bon</th>
                            <th style="text-align: center;vertical-align: middle;">Supplier</th>
                            <th style="text-align: center;vertical-align: middle;">SubTotal</th>
                            <th style="text-align: center;vertical-align: middle;">Tax (PPn)</th>
                            <th style="text-align: center;vertical-align: middle;">Tax (PPh)</th>            
                            <th style="text-align: center;vertical-align: middle;">potongan</th>
                            <th style="text-align: center;vertical-align: middle;">Total</th>
                            <th style="text-align: center;vertical-align: middle;">Currency</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Create By</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Status</th> 
                            <th style="text-align: center;vertical-align: middle;display: none;">Status</th>
                            <th style="text-align: center;vertical-align: middle;display: none;">Status</th> 
                            <th style="text-align: center;vertical-align: middle;display: none;">Status</th> 
                            <th style="text-align: center;vertical-align: middle;display: none;">Status</th>     
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

            $sql = mysqli_query($conn2,"select a.id, a.no_kbon, a.no_bpb, a.tgl_bpb, a.tgl_kbon, a.nama_supp, SUM(a.subtotal) as sub , SUM(a.tax) as tax, SUM(a.subtotal + a.tax - (a.pph_value + a.dp_value + b.jml_return)) as total , a.curr, a.create_user, a.status, a.status_int, a.tgl_tempo, a.no_faktur, a.supp_inv, a.tgl_inv, a.pph_code, SUM(a.pph_value) as pph_value, a.pph_code, b.jml_return - b.jml_potong as potongan from kontrabon a left JOIN potongan b on b.no_kbon = a.no_kbon  where a.tgl_kbon between '$start_date' and '$end_date' and a.nama_supp = '$nama_supp' and  a.status = 'Approved' group by no_kbon");


            while($row = mysqli_fetch_array($sql)){
            $tgl_kbon = $row['tgl_kbon'];
            $total = $row['total'];
            $potongan = $row['potongan'];
            $total_h = $total - $potongan;
            $no_kbon = $row['no_kbon'];
            $tgl_tempo = $row['tgl_tempo'];
            $diff = strtotime($tgl_tempo) - strtotime($tgl_kbon);
            $date_diff = round($diff / 86400); 
            $sql11 = mysqli_query($conn2,"select no_ro from bppb_new where no_kbon = '$no_kbon'"); 
            $rowz = mysqli_fetch_array($sql11);
            $no_ro = isset($rowz['no_ro']) ? $rowz['no_ro'] : null;                 
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>   
                            <td class="dt_out" style="width:100px;" dataout="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
                            <td style="display: none;" class="dt_out" style="width:100px;" dataout1="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td value = "'.date("d F Y",strtotime($row['tgl_kbon'])).'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                            <td value = "'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style="text-align:right;" value = "'.$row['sub'].'">'.number_format($row['sub'],2).'</td>
                            <td style="text-align:right;" value = "'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                            <td style="text-align:right;" value = "'.$row['pph_value'].'">- '.number_format($row['pph_value'],2).'</td>      
                            <td style="text-align:right;" value = "'.$row['potongan'].'">- '.number_format($row['potongan'],2).'</td>      
                            <td class="dt_out" style="width:100px;" dataout2="'.$total_h.'">'.number_format($total_h,2).'</td>
                            <td value = "'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="display: none;" value = "'.$row['create_user'].'">'.$row['create_user'].'</td>
                            <td style="display: none;" value = "'.$row['tgl_bpb'].'">'.$row['tgl_bpb'].'</td>
                            <td style="display: none;" value = "'.$row['no_faktur'].'">'.$row['no_faktur'].'</td>
                            <td style="display: none;" value = "'.$row['supp_inv'].'">'.$row['supp_inv'].'</td>
                            <td style="display: none;" value = "'.date("d-M-Y",strtotime($row['tgl_inv'])).'">'.date("d-M-Y",strtotime($row['tgl_inv'])).'</td>
                            <td style="display: none" value = "'.$no_ro.'">'.$no_ro.'</td>
                                                                  
                        </tr>';
                      }                  
                    ?>
            </tbody>                    
            </table>                    
<div class="box footer"> 


        <form id="form-simpan">


        </br>

            <div class="form-row col">
                <label for="subtotal" class="col-sm-2 col-form-label" style="width: 100px;">Applicant Name</label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="nama_pengaju" id="nama_pengaju" style="font-size: 14px; text-align: left;" value="<?php echo $user; ?>" readonly>
            </div>
            </div>

            <div class="form-row col">
                <label for="subtotal" class="col-sm-2 col-form-label" style="width: 100px;">Maintain Date</label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="tgl_pengajuan" id="tgl_pengajuan"  placeholder="-" style="font-size: 14px; text-align: left;" readonly
                 value="<?php             
            if(!empty($_POST['tgl'])) {
                echo $_POST['tgl'];
            }
            else{
                echo date("d F Y");
            } ?>">
            </div>
            </div>

            <div class="form-row col">
                <label for="subtotal" class="col-sm-2 col-form-label" style="width: 100px;">No Kontrabon</label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="nokbon" id="nokbon" value="" placeholder="-" style="font-size: 14px; text-align: left;" readonly>
            </div>
            </div>

            <div class="form-row col">
                <label for="subtotal" class="col-sm-2 col-form-label" style="width: 100px;">Kontrabon Date</label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="tglkbon" id="tglkbon" value="" placeholder="-" style="font-size: 14px; text-align: left;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="subtotal" class="col-sm-2 col-form-label" style="width: 100px;">Total Kontrabon</label>
            <div class="col-md-5 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px; text-align: left;" readonly>
            </div>
            </div>

            <div class="form-row col">
                <label for="subtotal" class="col-sm-2 col-form-label" style="width: 100px;">Message</label>
            <div class="col-md-5 mb-3">                              
                <textarea cols="30" rows="5" type="text" class="form-control " name="pesan" id="pesan" style="font-size: 14px; text-align: left;" value="" placeholder="type message here..." required></textarea>
            
            </div>
            </div>

        <!-- <form class="form-row col" method="POST" action="upload.php" enctype="multipart/form-data" style="margin-bottom: -15px;">
            <div class="form-row col">
            <label class="col-sm-2 col-form-label" style="width: 220px;"><b>File Bukti</b></label>
            <div class="col-md-4 mb-3" >  
            <input class="form-control" type="file" name="upload"/>
        </div>
             <div class="col-md-1 mb-1" >
            <button type="submit" class="btn-xs btn-success form-control" name="submit" style="margin-top: 5px;"><span class="glyphicon glyphicon-upload"></span> Upload</button>
</div>
    </div>
        </form> -->
            <!-- <div class="form-row col">
                <label for="pesan" class="col-sm-2 col-form-label"><b>File Bukti</b></label>
                <div class="col-md-5 mb-3">
                <input type="file" name="file" id="file" class="form-control border-input" required>
            
                </div>
            </div> -->
        </br>

           <div class="form-row col">
            <div class="col-md-5 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='pengajuankb.php'"><span class="fa fa-angle-double-left"></span> Back</button>         
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
<!--           <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>   -->                                         
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
    var k_bon = '';
    var t_bon = '';
    var total = 0;
    $("input[type=checkbox]:checked").each(function () {        
    var kb = $(this).closest('tr').find('td:eq(1)').attr('dataout') || '';
    var tgl = $(this).closest('tr').find('td:eq(3)').attr('value') || '';
    var ttl = parseFloat($(this).closest('tr').find('td:eq(9)').attr('dataout2'),10) || 0;
    
    k_bon += kb+", ";
    total += ttl;
    t_bon += tgl+", ";

    });
    $("#nokbon").val(k_bon);
    $("#tglkbon").val(t_bon); 
    $("#total").val(formatMoney(total));                  
});        
</script>

<script type="text/javascript">
    var k_bon = '';

    $("input[type=checkbox]:checked").each(function () {        
    var kb = $(this).closest('tr').find('td:eq(1)').attr('dataout');
    
    k_bon += kb;

    });
    $("#nokbon").val(k_bon);

</script>

<!-- <script type="text/javascript">
    $("#txt_dp").keyup(function(){
        var dp_value = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;
        dp_value = total_po * (dp / 100);
        $("#txt_dp_value").val(formatMoney(dp_value));
    });
</script> -->

<!-- <script type="text/javascript">
    $("#txt_dp_value").keyup(function(){
        var dp_code = 0;
        var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
        var dp_value = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        dp_code = (dp_value / total_po) * 100;
        $("#txt_dp").val(dp_code);
    });
</script> -->

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
        $("input[type=checkbox]:checked").each(function () {
        var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('dataout');
        var no_bpb = $(this).closest('tr').find('td:eq(2)').attr('dataout1');
        var tgl_kbon = $(this).closest('tr').find('td:eq(3)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(12)').attr('value');
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var tgl_pengajuan = document.getElementById('tgl_pengajuan').value;        
        var nama_pengaju = document.getElementById('nama_pengaju').value;
        var pesan = document.getElementById('pesan').value;
        var total = parseFloat($(this).closest('tr').find('td:eq(9)').attr('dataout2'),10) || 0;
        var no_ro = $(this).closest('tr').find('td:eq(16)').attr('value');  
        // var email = document.getElementById('email').value;
        // var file = document.getElementById('file').files[0].name;        
        // var curr = $(this).closest('tr').find('td:eq(8)').attr('value');                               
        // var tgl_kbon = $(this).closest('tr').find('td:eq(2)').attr('value');
        // var create_user = '<?php  $user; ?>';         
        // var total_kbon = parseFloat($(this).closest('tr').find('td:eq(3)').attr('data-total'),10) ||0;
        // var top = $(this).closest('tr').find('td:eq(4)').attr('value');
        // var outstanding = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) ||0;
        // var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) ||0;
        // var tgl_tempo = $(this).closest('tr').find('td:eq(7)').attr('value');
        // var balance = 0;
        // balance += outstanding - amount;   
        if(pesan != ''){   
        $.ajax({
            type:'POST',
            url:'insertpengajuan.php',
            data: {'no_kbon':no_kbon,'no_bpb':no_bpb, 'tgl_kbon':tgl_kbon, 'tgl_bpb':tgl_bpb, 'nama_supp':nama_supp, 'tgl_pengajuan':tgl_pengajuan, 'nama_pengaju':nama_pengaju, 'pesan':pesan ,'total':total, 'no_ro':no_ro},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert("data submitted successfully");
                window.location = 'pengajuankb.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        }else{
        document.getElementById('pesan').focus();
        alert("please enter your message");
    }
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please check the Kontrabon number");            
        }else{
            alert("data submitted successfully");
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
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('dataout');
    var tgl_kbon = $(this).closest('tr').find('td:eq(3)').text();
    var supp = $(this).closest('tr').find('td:eq(4)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(10)').text();
    var curr = $(this).closest('tr').find('td:eq(13)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(14)').attr('value');
    var status = $(this).closest('tr').find('td:eq(15)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(13)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(14)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(15)').text();                

    $.ajax({
    type : 'post',
    url : 'ajaxfm_kb.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_kbon);
    $('#txt_tgl_kbon').html('Kontrabon Date : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Currency : ' + tgl_tempo + '');
    $('#txt_curr').html('No Faktur : ' + curr + '');        
    $('#txt_create_user').html('No Supplier Invoice : ' + create_user + '');
    $('#txt_status').html('Supplier Invoice Date : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                               
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
