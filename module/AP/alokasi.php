<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h2 class="text-center">ALOKASI</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="alokasi.php" method="post">
        <div class="form-row">
            <div class="col-md-6">
            <label for="nama_supp"><b>Customer</b></label>            
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
                $sql = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC",$conn1);
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

                <div class="col-md-3">
            <label for="nama_supp"><b>Bank</b></label>            
              <select class="form-control selectpicker" name="bank" id="bank" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $bank ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(nama_bank) from masterbank  order by nama_bank ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['nama_bank'];
                    if($row['nama_bank'] == $_POST['bank']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>

                <div class="col-md-3">
            <label for="nama_supp"><b>Account</b></label>            
              <select class="form-control selectpicker" name="akun" id="akun" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $bank = isset($_POST['bank']) ? $_POST['bank']: null;
                $akun = isset($_POST['akun']) ? $_POST['akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(no_rek) from masterbank where nama_bank = '$bank' order by no_rek ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['no_rek'];
                    if($row['no_rek'] == $_POST['akun']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>


                <div class="col-md-5">
            <label for="status"><b>Type</b></label>            
              <select class="form-control selectpicker" name="status" id="status" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="ALL" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'ALL'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >ALL</option>
                <!-- <option value="draft" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'draft'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Draft</option>
                <option value="Approved" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Approved'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Approved</option>
                <option value="Cancel" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Cancel'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Cancel</option>
                <option value="Closed" <?php
                $status = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $status = isset($_POST['status']) ? $_POST['status']: null;
                }                 
                    if($status == 'Closed'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>
                >Closed</option>         -->                                                                                                     
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
<?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>';
        }else{
    echo '';
    }
?>
    <div class="box body">
        <div class="row">       
            <div class="col-md-12">

            
<table id="mytable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;">Document Number</th>
            <th style="text-align: center;vertical-align: middle;">Date</th>
            <th style="text-align: center;vertical-align: middle;">Customer</th>
            <th style="text-align: center;vertical-align: middle;">Curreny</th>
            <th style="text-align: center;vertical-align: middle;">Value</th>
            <th style="text-align: center;vertical-align: middle;">Description</th>
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

            
            $sql = mysql_query("select doc_number,nama_supp, doc_date,curr,nominal,outstanding, status,referensi,akun,bank,create_by from ap_bankout_h where outstanding = '-1' ",$conn1);
                                                                      
                while($row = mysql_fetch_array($sql)){
                    
           echo'<tr>                       
                            <td style="width:50px; text-align : center" value="'.$row['doc_number'].'">'.$row['doc_number'].'</td>
                            <td style="width:100px; text-align : center" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style="width:100px; text-align : center" value="'.$row['doc_date'].'">'.date("d-M-Y",strtotime($row['doc_date'])).'</td>                                                                                             
                            <td style="width:50px; text-align : center" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px; text-align : center" value="'.$row['nominal'].'">'.number_format($row['nominal'],2).'</td>
                            <td style="width:50px; text-align : center" value="'.$row['status'].'">'.$row['status'].'</td>                                                                                  
                        </tr>';
                    
                    
                    } ?>
                    </tbody>
                    </table>  
                    </div> 
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
                    
<!-- <div class="box footer">   
        <form id="form-simpan">
           <div class="form-row col">
            <div class="col-md-4 mb-2">
                <h4><i>Total</i></h4>
                <table >
            <?php
            $nama_supp ='';
            $start_date ='';
            $end_date ='';            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));            
            }

            $jml = 0;
            $ost = 0;
            $sql = mysql_query("select sum(nominal) as nominal, sum(outstanding) as outstanding from ap_bankout_h ",$conn1);
                    
                    $row = mysql_fetch_array($sql);                                                  
                    $jml += $row['nominal'];
                    $ost += $row['outstanding'];

                    
           echo'<tr>   

            <th style="text-align: left;vertical-align: middle;">Amount</th>
            <th style="text-align: left;vertical-align: middle;"> </th>
            <th style="text-align: center;vertical-align: middle;"><input style="border: 3px solid #555; text-align: center; border-radius: 8px;" value = "'.number_format($jml,2).'" readonly></th>                                                                                                                                                                       
                        </tr>
                        <tr>
            <th style="text-align: left;vertical-align: middle;">Outstanding</th>
            <th style="text-align: left;vertical-align: middle;"> </th>
            <th style="text-align: center;vertical-align: middle;"><input style="border: 3px solid #555; text-align: center; border-radius: 8px;" value = "'.number_format($ost,2).'" readonly></th>                                                                                                                                                                                                                                
                        </tr>';
                    
                    
                     ?>
        </table>
            </div>
            </div>                                   
        </form>
        
        </div>   -->      
                                
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
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-alokasi.php";
};
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
