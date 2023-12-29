<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM UPDATE BPB</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="noftrcbd"><b>No Dokumen</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(no_dok) from bpb_faktur_inv where jenis = 'FAK'");
            $row = mysqli_fetch_array($sql);
            $kodeftr = $row['max(no_dok)'];
            $urutan = (int) substr($kodeftr, 14, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "FAK/NAG/$bln$thn/";
            $kodeftr = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_dok" name="no_dok" value="'.$kodeftr.'">'
            ?>
            </div>
            <div class="col-md-2 mb-3">            
            <label for="tanggal"><b>Tanggal</b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
            }
            else{
                echo date("d-m-Y");
            } ?>" disabled>
            </div>

            <div class="col-md-7 mb-3">            
            </div>  

            <div class="col-md-3 mb-3">            
            <label for="no_faktur"><b>Nomor Faktur Pajak</b> <i style="color: red;">*</i></label>          
            <input type="text" style="font-size: 14px;" class="form-control" name="no_faktur" id="no_faktur" 
            value="<?php 
            $no_faktur = isset($_POST['no_faktur']) ? $_POST['no_faktur']: null;
            echo $no_faktur; 
            ?>" required autocomplete = 'off'>
            </div>
            <div class="col-md-2 mb-3">            
            <label for="tanggal_faktur"><b>Tanggal Faktur Pajak</b> <i style="color: red;">*</i></label>          
            <input type="text" style="font-size: 14px;" name="tanggal_faktur" id="tanggal_faktur" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal_faktur'])) {
                echo $_POST['tanggal_faktur'];
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-7 mb-3">            
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

            <div class="col-md-6 mb-3">
            <label for="from_hris" class="col-form-label"><b>Select Data:</b></label>
            <br>            
              <input style="border: 0;line-height: 1;padding: 10px 10px;font-size: 1rem;text-align: center;color: #fff;text-shadow: 1px 1px 1px #000;border-radius: 6px;background-color: rgb(95, 158, 160);" type="button" id="mysupp" name="v" data-target="#mymodal" data-toggle="modal" value="Select" class="btn btn-primary btn-lg" style="width: 100%;">    
</div>                   
    </div>
</form>
<div class="form-row">
    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose BPB</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label style="padding-left: 10px;" for="namasupp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="namasupp" id="namasupp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier),id_supplier from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data2 = $row['id_supplier'];
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['namasupp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                    </div>
              
                </div>

                <div class="col-md-12 mb-3">
                     <label><b>BPB Date</b></label>
                <div class="input-group-append">           
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="startdate_bpb" name="startdate_bpb" 
                value="<?php
                $startdate_bpb ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $startdate_bpb = date("Y-m-d",strtotime($_POST['startdate_bpb']));
                }
                if(!empty($_POST['startdate_bpb'])) {
                    echo $_POST['startdate_bpb'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <label class="col-md-1" for="end_date"><b>-</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="enddate_bpb" name="enddate_bpb" 
                value="<?php
                $enddate_bpb ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $enddate_bpb = date("Y-m-d",strtotime($_POST['enddate_bpb']));
                }
                if(!empty($_POST['enddate_bpb'])) {
                    echo $_POST['enddate_bpb'];
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Akhir">
                &nbsp;&nbsp;
                <input 
                style="border: 0;
                    line-height: 1;
                    padding: 10px 10px;
                    font-size: 1rem;
                    text-align: center;
                    color: #fff;
                    text-shadow: 1px 1px 1px #000;
                    border-radius: 6px;
                    background-color: rgb(95, 158, 160);"
                type="button" id="send2" name="send2" value="Search"> 
                </div>
                </div>  
                
                <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>
  
                <div class="modal-footer">
                    <div class="col-md-2 mb-3">
                    <input type="button" id="savebpb" name="savebpb" class="btn btn-warning btn-lg" style="width: 100%;" value="Save">
                </div>
                <div class="col-md-10 mb-3">
                </div>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>

      <!-- /.modal-dialog --> 
    </div>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:5%;"></th>                       
                            <th style="width:20%;">No BPB</th>
                            <th style="width:15%;">BPB Date</th>                            
                            <th style="width:25%;">Supplier</th>                          
                            <th style="width:15%;">Currency</th>                            
                            <th style="width:20%;">Total</th>
                        </tr>
                    </thead>

            <tbody id="tbl_bpb">
            
            </tbody>                    
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <br>
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='update_bpb.php'"><span class="fa fa-angle-double-left"></span> Back</button>               
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
    $("input[type=radio]").click(function(){
    var sum_sub = 0;
    var sum_tax = 0;
    var ceklist = 0;
    var sum_total = 0;
    $("input[type=radio]").prop('disabled', true);             
    $("input[type=radio]:checked").each(function () {        
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
    $("#modal-form2").on("click", "#send2", function(){
        var nama_supp = $('select[name=namasupp] option').filter(':selected').val(); 
        var start_date = document.getElementById('startdate_bpb').value;
        var end_date = document.getElementById('enddate_bpb').value;        
         
             
        $.ajax({
            type:'POST',
            url:'cari_bpb_verif2.php',
            data: {'nama_supp':nama_supp, 'start_date':start_date, 'end_date':end_date},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#details').html(data);
                // alert(data);  
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
 
    return false; 
    });


</script>

<script>
    $("#modal-form2").on("click", "#savebpb", function(){
        $("input[type=checkbox]:checked").each(function () {
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supplier = $(this).closest('tr').find('td:eq(3)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(4)').attr('value');
        var total = $(this).closest('tr').find('td:eq(5)').attr('value');
        var user = '<?php echo $user ?>';
             
        $.ajax({
            type:'POST',
            url:'insert_bpb_temp.php',
            data: {'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb,'supplier':supplier, 'curr':curr, 'total':total, 'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                // return false; 
            },
            success: function(response){
                // console.log(response);
                $('#mymodal').modal('toggle');
                $('#mymodal').modal('hide');
                 // alert(response);
            var user = '<?php echo $user ?>';
                $.ajax({
            type:'POST',
            url:'load_bpb_temp.php',
            data: { 'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#tbl_bpb').html(data);
                $('#table-bpb tbody tr').remove(); 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
            });
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


<script>
    $("#form-data").on("click", "#mysupp", function(){
        var user = '<?php echo $user ?>';
        $('#mytable tbody tr').remove(); 
             
        $.ajax({
            type:'POST',
            url:'delete_bpb_temp.php',
            data: {'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {
        var no_dok = document.getElementById('no_dok').value;        
        var tgl_dok = document.getElementById('tanggal').value;
        var no_inv = '';
        var tgl_inv = '';
        var no_faktur = document.getElementById('no_faktur').value;
        var tgl_faktur = document.getElementById('tanggal_faktur').value;        
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');                               
        var tgl_bpb = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supplier = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = '<?php echo $user; ?>';         
        if(no_faktur != '' && tgl_faktur != '' && no_bpb != ''){
        $.ajax({
            type:'POST',
            url:'insert_bpb_fakturinv2.php',
            data: {'no_dok':no_dok, 'tgl_dok':tgl_dok, 'no_inv':no_inv, 'tgl_inv':tgl_inv, 'no_faktur':no_faktur, 'tgl_faktur':tgl_faktur, 'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb, 'supplier':supplier, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert("Data saved successfully");
                window.location = 'update_bpb.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    } 
    });                 
        if(document.getElementById('no_faktur').value == ''){
            alert("Please Input No Faktur");
            document.getElementById('no_faktur').focus();
        }else if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please select data");
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
