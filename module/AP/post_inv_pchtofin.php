<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h3 class="text-center">TRANSFER INVOICE FROM PURCHASING TO FINANCE</h3>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-2 mb-1">            
            <label for="nopayment"><b>Document Number</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(substr(no_trans,15)) no_trans from ir_log_trans where kode_trans = 'TPTF'");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['no_trans'];
            $urutan = (int) substr($kodepay, 0, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "TPTF/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 12px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            ?>
            </div>
            <div class="col-md-2 mb-1">            
            <label for="tanggal"><b>Transfer Date </b></label>          
            <input type="text" style="font-size: 14px;" name="tgl_doc" id="tgl_doc" class="form-control tanggal2" 
            value="<?php 
            $tgl_doc = isset($_POST['tgl_doc']) ? $_POST['tgl_doc']: null;            
            if(!empty($_POST['tgl_doc'])) {
                echo $_POST['tgl_doc'];
            }
            else{
                echo date("Y-m-d");
            } ?>">
            <input type="hidden" id="tgl_fil_inv" name="tgl_fil_inv" value="">

            </div>                       

            <div class="col-md-4 mb-1">
            <label for="nama_supp"><b>Supplier</b></label>            
            <div class="input-group">
            <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                echo $nama_supp; 
            ?>">

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
    <div class="col-md-8" >
        <label for="nama_supp" class="col-form-label" style="width: 150px;"><b>Descriptions </b></label>         
        <textarea style="font-size: 14px; text-align: left;" cols="40" rows="3" type="text" class="form-control " name="pesan" id="pesan" value="" placeholder="descriptions..."></textarea>         
    </div> 

</div>

     <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Kontrabon</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
            <label for="nama_supp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                
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
                <input type="text" style="font-size: 14px;" class="form-control tanggal3" id="start_date" name="start_date" 
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
                <input type="text" style="font-size: 14px;" class="form-control tanggal3" id="end_date" name="end_date" 
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
                    <div class="col-md-9 mb-1"> 
                    </div> 
                    <div class="col-md-3 mb-1">  
                        <button type="submit" id="send" name="send" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-search"></span>Search</button>
                    </div>
                </div>
            </form>
        </div>
      </div>


        </div>
    <!-- /.modal-content --> 
  </div>
      <!-- /.modal-dialog --> 
    </div>
</form>
    <div class="box body mt-2">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th style="width:10%;"><input type="checkbox" id="select_all"></th>
                            <th style="width:15%;">No Document</th>
                            <th style="width:15%;">No Kontrabon</th>
                            <th style="width:15%;">Kontrabon Date</th>                            
                            <th style="width:25%;">Supplier</th>
                            <th style="width:20%;">Total Kontrabon</th>    
                            <th style="display: none;">Total Kontrabon</th>                        
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

            // if ($nama_supp == 'ALL') {
            //     $sql = mysqli_query($conn2,"select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user from ir_invoice_supp_h where status = 'Accepted Pch' and tgl_penerimaan between '$start_date' and '$end_date'");
            // }else{
            //     $sql = mysqli_query($conn2,"select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user from ir_invoice_supp_h where status = 'Accepted Pch' and nama_supp = '$nama_supp' and tgl_penerimaan between '$start_date' and '$end_date'");
            // }

            if ($nama_supp == 'ALL') {
                $sql = mysqli_query($conn2,"select *,IF(approved_date is null,tgl_penerimaan,approved_date) fill_date  from (select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user from ir_invoice_supp_h where status IN ('Accepted Pch') and tgl_penerimaan between '$start_date' and '$end_date') a LEFT JOIN (select nama_trans,no_trans,tgl_trans,DATE_FORMAT(approved_date,'%Y-%m-%d') approved_date,doc_number doc_num from ir_trans_invoice_supp where status = 'Approved' and nama_trans = 'TATP' GROUP BY doc_number) b on b.doc_num = a.doc_number order by fill_date asc");
            }else{
                $sql = mysqli_query($conn2,"select *,IF(approved_date is null,tgl_penerimaan,approved_date) fill_date  from (select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user from ir_invoice_supp_h where status IN ('Accepted Pch') and nama_supp = '$nama_supp' and tgl_penerimaan between '$start_date' and '$end_date') a LEFT JOIN (select nama_trans,no_trans,tgl_trans,DATE_FORMAT(approved_date,'%Y-%m-%d') approved_date,doc_number doc_num from ir_trans_invoice_supp where status = 'Approved' and nama_trans = 'TATP' and nama_supp = '$nama_supp' GROUP BY doc_number) b on b.doc_num = a.doc_number order by fill_date asc");
            }

            
            while($row = mysqli_fetch_array($sql)){   
            if ($row['no_trans'] == null || $row['no_trans'] == '') { $no_trans = '-'; }else{ $no_trans = $row['no_trans'];}                     
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$no_trans.'">'.$no_trans.'</td>
                            <td style="width:50px;" value="'.$row['doc_number'].'">'.$row['doc_number'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_penerimaan'].'">'.date("d-M-Y",strtotime($row['tgl_penerimaan'])).'</td>
                            <td style="" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style ="text-align: right;" class="dt_total" style="width:100px;" value="'.$row['total_amount'].'">'.number_format($row['total_amount'],2).'</td>
                            <td style="display:none" value="'.$row['fill_date'].'">'.$row['fill_date'].'</td>
                        </tr>';
                      }                  
                    ?>
            </tbody>                    
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
            <div class="col-md-4">
                </br>
            </div>
            <div class="col-md-4">

            </div>
            <div class="col-md-4">
                </br>
                <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 160px;"><b>Total Amount</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="total_value" name="total_value" placeholder="0.00" readonly>
                 <input type="hidden" name="total_value_h" id="total_value_h" value="">
                 <input type="hidden" style="font-size: 15px;" name="unik_code" id="unik_code" class="form-control" 
            value="<?php 
            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789!@#$%^&*()?';
            $shuffle  = substr(str_shuffle($karakter), 0, 25);
            echo $shuffle; ?>" autocomplete='off' readonly>
            </div>
            </br>
             
        </div>
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-paper-plane"></span> Post</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='invoice_received.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalkbon" data-target="#mymodalkbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
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
        // var tgl1 = document.getElementById('tanggal1').value;
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true,
        // startDate: new Date(tgl1)
    });

    $('.tanggal2').datepicker({
        format: "yyyy-mm-dd",
        autoclose:true,
        endDate: "+0d",
        datesDisabled: '+0d'
        // startDate: new Date(tgl1)
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var tgl = document.getElementById('due_date1').value;
    $('.due_date').datepicker({
        format: "yyyy-mm-dd",
        autoclose:true
        // startDate: new Date(tgl)
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal3').datepicker({
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
    var sum_amount = 0;  
    var tglinv = '';               
    var inv_tgl = document.getElementById('tgl_fil_inv').value;   
    $("input[type=checkbox]:checked").each(function () { 
    var amount = parseFloat($(this).closest('tr').find('td:eq(5)').attr('value')) || 0; 
    var tgl_inv = $(this).closest('tr').find('td:eq(6)').attr('value'); 

    sum_amount += amount;
    if (inv_tgl > tgl_inv) {
        tglinv = inv_tgl; 
    }else{
        tglinv = tgl_inv; 
    }
    
    });
    $("#total_value").val(formatMoney(sum_amount));
    $("#total_value_h").val(sum_amount);
    $("#tgl_fil_inv").val(tglinv);
    // $("#select").val("1");                    
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
        var doc_num = document.getElementById('no_doc').value;
        var ttl_amount = document.getElementById('total_value_h').value;
        var unik_code = document.getElementById('unik_code').value;
        var kode_trans = 'TPTF';  
        var create_user = '<?php echo $user; ?>';
        var tgl_doc = document.getElementById('tgl_doc').value;
        var inv_tgl = document.getElementById('tgl_fil_inv').value;

        if (ttl_amount >= '1' && tgl_doc >= inv_tgl) {
        $.ajax({
            type:'POST',
            url:'insert_log_trans.php',
            data: {'doc_num':doc_num, 'ttl_amount':ttl_amount, 'unik_code':unik_code, 'kode_trans':kode_trans, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                 $("input[type=checkbox]:checked").each(function () {
                    var no_doc = document.getElementById('no_doc').value;        
                    var tgl_doc = document.getElementById('tgl_doc').value;
                    var keterangan = document.getElementById('pesan').value;                               
                    var no_kbon = $(this).closest('tr').find('td:eq(2)').attr('value');
                    var tgl_kbon = $(this).closest('tr').find('td:eq(3)').attr('value');
                    var nama_supp = $(this).closest('tr').find('td:eq(4)').attr('value');
                    var amount = $(this).closest('tr').find('td:eq(5)').attr('value');
                    var create_user = '<?php echo $user; ?>';     
                    var unik_code = document.getElementById('unik_code').value;
                    var kode_trans = 'TPTF';    
    
                if(amount >= 1){       
                $.ajax({
                    type:'POST',
                    url:'insert_trf_fintoacc.php',
                    data: {'no_doc':no_doc, 'tgl_doc':tgl_doc, 'keterangan':keterangan, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'nama_supp':nama_supp, 'amount':amount, 'create_user':create_user, 'unik_code':unik_code, 'kode_trans':kode_trans},
                    cache: 'false',
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){
                    console.log(response);
                    window.location = 'invoice_received.php';
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
                         
       if(document.getElementById('total_value_h').value == ''){
        alert("Please check the Kontrabon");
        }else if(document.getElementById('total_value_h').value <= '0'){
        alert("Please check the Kontrabon");
        }else if(document.getElementById('total_value_h').value == '0.00'){
        alert("Please check the Kontrabon");
        }else if(document.getElementById('tgl_doc').value < document.getElementById('tgl_fil_inv').value){
        alert("Invalid Transfer Date, Minimum Input Date: "+document.getElementById('tgl_fil_inv').value);
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
    $('table tbody tr').on('click', 'td:eq(2)', function(){                
    $('#mymodalkbon').modal('show');
    var no_document = $(this).closest('tr').find('td:eq(2)').attr('value'); 
    var tgl_doc = $(this).closest('tr').find('td:eq(3)').attr('value'); 
    var supp = $(this).closest('tr').find('td:eq(4)').attr('value');                 

    $.ajax({
    type : 'post',
    url : 'ajax_suppinv.php',
    data : {'no_document': no_document},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_document);
    $('#txt_tgl_kbon').html('Invoice Date : ' + tgl_doc + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');                            
});

</script>

  
</body>

</html>
