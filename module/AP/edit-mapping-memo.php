<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">ADD MAPPING COA MEMO</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Category Name</b></label>
                <?php
            $id_sub = base64_decode($_GET['id_sub']);
            $sql = mysqli_query($conn2,"select a.id_ctg,nm_ctg,id_sub_ctg,upper(nm_sub_ctg) nm_sub_ctg from master_memo_ctg a inner join master_memo_subctg b on b.id_ctg = a.id_ctg where id_sub_ctg = '$id_sub' order by id_ctg asc");
            $row = mysqli_fetch_array($sql);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control form-control-sm" id="nm_ctg" name="nm_ctg" value="'.$row['nm_ctg'].'">
                <input type="hidden" readonly style="font-size: 14px;" class="form-control-plaintext" id="id_ctg" name="id_ctg" value="'.$row['id_ctg'].'">'
            ?>
            </div>

            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Sub Category Name</b></label>
                <?php
            $id_sub = base64_decode($_GET['id_sub']);
            $sql = mysqli_query($conn2,"select a.id_ctg,nm_ctg,id_sub_ctg,upper(nm_sub_ctg) nm_sub_ctg from master_memo_ctg a inner join master_memo_subctg b on b.id_ctg = a.id_ctg where id_sub_ctg = '$id_sub' order by id_ctg asc");
            $row = mysqli_fetch_array($sql);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control form-control-sm" id="nm_sub_ctg" name="nm_sub_ctg" value="'.$row['nm_sub_ctg'].'">
                <input type="hidden" readonly style="font-size: 14px;" class="form-control-plaintext" id="id_sub_ctg" name="id_sub_ctg" value="'.$row['id_sub_ctg'].'">'
            ?>
            </div>
            <div class="col-md-2 mb-3">  
                <label for="jns_trans" class="col-form-label"><b>Jenis Transaksi</b></label>            
              <select class="form-control selectpicker" name="jns_trans" id="jns_trans" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Pilih Jenis Transaksi</option> 
                <?php
                $jns_trans ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $jns_trans = isset($_POST['jns_trans']) ? $_POST['jns_trans']: null;
                }                 
                $sql = mysqli_query($conn1,"select DISTINCT jns_trans from memo_mapping_v2 where jns_trans != ''");
                while ($row = mysqli_fetch_array($sql)) {
                    $isi = $row['jns_trans'];
                    $tampil = $row['jns_trans'];
                    if($row['jns_trans'] == $_POST['jns_trans']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$isi.'"'.$isSelected.'">'. $tampil .'</option>';    
                }?>
                </select>
            </div>
            <div class="col-md-1 mb-3">  
                <label for="ditagihkan" class="col-form-label"><b>Ditagihkan</b></label>            
              <select class="form-control selectpicker" name="ditagihkan" id="ditagihkan" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Pilih ditagihkan</option> 
                <?php
                $ditagihkan ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ditagihkan = isset($_POST['ditagihkan']) ? $_POST['ditagihkan']: null;
                }                 
                $sql = mysqli_query($conn1,"select DISTINCT ditagihkan from memo_mapping_v2 where ditagihkan != ''");
                while ($row = mysqli_fetch_array($sql)) {
                    $isi = $row['ditagihkan'];
                    $tampil = $row['ditagihkan'];
                    if($row['ditagihkan'] == $_POST['ditagihkan']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$isi.'"'.$isSelected.'">'. $tampil .'</option>';    
                }?>
                </select>
            </div>
            <div class="col-md-2 mb-3">  
                <label for="id_item" class="col-form-label"><b>Item</b></label>            
              <select class="form-control selectpicker" name="id_item" id="id_item" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Pilih Item</option> 
                <?php
                $id_item ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $id_item = isset($_POST['id_item']) ? $_POST['id_item']: null;
                }                 
                $sql = mysqli_query($conn1,"select id id_item,item_name from master_memo_item where aktif = 'Y'");
                while ($row = mysqli_fetch_array($sql)) {
                    $isi = $row['id_item'];
                    $tampil = $row['item_name'];
                    if($row['id_item'] == $_POST['id_item']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$isi.'"'.$isSelected.'">'. $tampil .'</option>';    
                }?>
                </select>
            </div>

            <div class="col-md-3 mb-3">  
                <label for="id_item" class="col-form-label"><b>No COA</b></label>            
              <select class="form-control selectpicker" name="no_coa" id="no_coa" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Pilih Item</option> 
                <?php
                $no_coa ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $no_coa = isset($_POST['no_coa']) ? $_POST['no_coa']: null;
                }                 
                $sql = mysqli_query($conn1,"select no_coa,concat(no_coa,' ', nama_coa) as coa_name from mastercoa_v2");
                while ($row = mysqli_fetch_array($sql)) {
                    $isi = $row['no_coa'];
                    $tampil = $row['coa_name'];
                    if($row['no_coa'] == $_POST['no_coa']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$isi.'"'.$isSelected.'">'. $tampil .'</option>';    
                }?>
                </select>
            </div>

            <div class="col-md-3 mb-3">  
                <label for="id_item" class="col-form-label"><b>Cost Center</b></label>            
              <select class="form-control selectpicker" name="no_cc" id="no_cc" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Pilih Item</option> 
                <?php
                $no_cc ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $no_cc = isset($_POST['no_cc']) ? $_POST['no_cc']: null;
                }                 
                $sql = mysqli_query($conn1,"select no_cc,cc_name from b_master_cc where status = 'Active'");
                while ($row = mysqli_fetch_array($sql)) {
                    $isi = $row['no_cc'];
                    $tampil = $row['cc_name'];
                    if($row['no_cc'] == $_POST['no_cc']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$isi.'"'.$isSelected.'">'. $tampil .'</option>';    
                }?>
                </select>
            </div>

            <div class="col-md-2 mb-3" style="margin-top: 35px;">
                <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='master-mapping-memo.php'"><span class="fa fa-angle-double-left"></span> Back</button>
            </div>

    </div>
    </div>
    </br>
</br>
   
</form>

    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">

        <div class="table-responsive">
    <table id="mytable" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
        <thead>
            <tr class="bg-dark" style="color:white">                       
                <th>No</th>
                <th>Jenis Transaksi</th>  
                <th>Ditagihkan</th>  
                <th>Item Name</th>  
                <th>No Coa</th>  
                <th>Nama Coa</th>  
                <th>No Cost Center</th>     
                <th>Nama Cost Center</th>
                <th>Action</th>                                                     
            </tr>
        </thead>
        <tbody id="data_mapping">
    <?php
    $nama_ctg ='';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");  
    $id_sub = base64_decode($_GET['id_sub']);              
    
    $sql = mysqli_query($conn2,"select id,jns_trans,ditagihkan,id_ctg,nm_ctg,id_sub_ctg,nm_sub_ctg,id_item,item_name,no_coa,nama_coa,id_cc,cc_name from memo_mapping_v2 where id_sub_ctg = '$id_sub'");

        $no = 1;
    while($row = mysqli_fetch_array($sql)){

        echo '<tr style="font-size:12px;text-align:left;">
            <td value = "">'.$no++.'.</td>
            <td value = "'.$row['jns_trans'].'">'.$row['jns_trans'].'</td>
            <td value = "'.$row['ditagihkan'].'">'.$row['ditagihkan'].'</td>
            <td value = "'.$row['item_name'].'">'.$row['item_name'].'</td>
            <td value = "'.$row['no_coa'].'">'.$row['no_coa'].'</td>
            <td value = "'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
            <td value = "'.$row['id_cc'].'">'.$row['id_cc'].'</td>
            <td value = "'.$row['cc_name'].'">'.$row['cc_name'].'</td>';
            echo '<td style="font-size:12px;text-align:center;">';
                $no_coa = $row['no_coa'];
                $query2 = mysqli_query($conn2,"select id,no_coa,nama_coa from tbl_list_journal where no_journal like '%MEMO%' and no_coa != '2.18.02' and no_coa = '$no_coa' GROUP BY no_coa");
                $cek_data = mysqli_fetch_array($query2);
                $id_map = isset($cek_data['id']) ?  $cek_data['id'] : 0;

                if ($id_map != 0) {
                    echo '-';
                }else{
                    echo '<button style="border-radius: 6px" type="button" class="btn-xs btn-danger" onclick="deletedata('.$row['id'].')"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';   
                }                                              
            echo '</td>';
            echo '</tr>';
}?>
</tbody>                    
</table>
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
                                
</div><!-- body-row END -->
</div>
</div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-multiselect.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/sweetalert2.min"></script>
  <script language="JavaScript" src="../css/4.1.1/sweetalert2.all.min"></script>
    <script language="JavaScript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.js"></script>

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
    $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
    });
  </script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
</script>


<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_coa').select2({
            dropdownAutoWidth : true
        });

        $('.select2_costcenter').select2({
            dropdownAutoWidth : true
        });
    });
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<script>
    $(".select2").select2({
        theme: "bootstrap",
        placeholder: "Search"
} );
</script>

<script>
    $(document).ready(function() {
    $('#mytable').dataTable();

     $("[data-toggle=tooltip]").tooltip();

    
} );
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
function datamapping(){
    var id_sub_ctg = document.getElementById('id_sub_ctg').value;

    $.ajax({
    type : 'post',
    url : 'datamappingmemo.php',
    data : {'id_sub_ctg': id_sub_ctg},
    success : function(data){
    $('#data_mapping').html(data); //menampilkan data ke dalam modal
        }
    });    
}
</script>


<script type="text/javascript">
    $("#form-data").on("click", "#simpan", function(){
        var id_ctg = document.getElementById('id_ctg').value; 
        var nm_ctg = document.getElementById('nm_ctg').value;  
        var id_sub_ctg = document.getElementById('id_sub_ctg').value;
        var nm_sub_ctg = document.getElementById('nm_sub_ctg').value;
        var jns_trans = document.getElementById('jns_trans').value;
        var ditagihkan = document.getElementById('ditagihkan').value;
        var id_item = document.getElementById('id_item').value;
        var no_coa = document.getElementById('no_coa').value;
        var no_cc = document.getElementById('no_cc').value;       
        // var type_co = $('select[name=nama_type] option').filter(':selected').val();
        var create_user = '<?php echo $user; ?>';

        if (jns_trans != '' && ditagihkan != '' && no_coa != '') {
        $.ajax({
            type:'POST',
            url:'insert-mapping-coa.php',
            data: {'id_ctg':id_ctg, 'nm_ctg':nm_ctg, 'id_sub_ctg':id_sub_ctg, 'nm_sub_ctg':nm_sub_ctg,'jns_trans':jns_trans, 'ditagihkan':ditagihkan, 'id_item':id_item, 'no_coa':no_coa, 'no_cc':no_cc},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // window.location.reload();
                datamapping();
                // alert(response);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        } 
                         
       if(document.getElementById('jns_trans').value == ''){
        alert("Please Input Jenis Transaksi");
        }else if(document.getElementById('ditagihkan').value == ''){
        alert("Please Input Ditagihkan");
        }else if(document.getElementById('no_coa').value == ''){
        alert("Please Input COA");
        }else{               
            
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
    function deletedata(id_h){
        var id = id_h;        
             
        $.ajax({
            type:'POST',
            url:'hapus-mapping-coa.php',
            data: {'id':id},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // window.location.reload()
                datamapping();
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });     
    }
    
</script>

<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalkbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(2)').text();
    var supp = $(this).closest('tr').find('td:eq(9)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(7)').text();
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(16)').attr('value');
    var status = $(this).closest('tr').find('td:eq(17)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(18)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(15)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(19)').text();                

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
    $('#txt_tgl_kbon').html('Tgl Kontrabon : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Tgl Jatuh Tempo : ' + tgl_tempo + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                               
});

</script> -->

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
