<?php include '../header.php' ?>

    <!-- MAIN -->    
    <div class="col p-4">
        <h3 class="text-center">ACCEPT TRANSFER INVOICE FROM PURCHASING TO FINANCE</h3>
<div class="box">
    <div class="box header">
<form id="form-data" action="form_approve_fin.php" method="post">
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
        
                <!-- <div class="container-1 mr-4 mt-1" style="margin-bottom-">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search no kontrabon..">
                </div> -->
            <div class="card-body table-responsive">
                <!-- <div class="d-flex justify-content-between mr-2 mb-1">
                    <div class="ml-auto">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text"  id="myInput" name="myInput" required autocomplete="off" placeholder="Search No kontrabon.." onkeyup="myFunction()">
                </div> -->
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="thead-dark">                      
                            <th style="width:15%;">No Document</th>
                            <th style="width:10%;">Document Date</th> 
                            <th style="display: none;">No Kontrabon</th>
                            <th style="display: none;">Kontrabon Date</th>                            
                            <th style="display: none;">Supplier</th>
                            <th style="width:10%;">Total Kontrabon</th>
                            <th style="width:15%;">Transfer Date</th>
                            <th style="width:6%;">Action</th>

                        </tr>
                    </thead>

            <tbody>
            <?php
            $nama_supp ='';
           
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            
            }

            // if(empty($nama_supp) or $nama_supp == 'ALL'){
            // $sql = mysqli_query($conn2,"select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user,updated_at from ir_invoice_supp_h where status = 'Post Pch To Fin'");                
            // }else {
            // $sql = mysqli_query($conn2,"select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user,updated_at from ir_invoice_supp_h where status = 'Post Pch To Fin' and nama_supp = '$nama_supp'");
            // }

            if(empty($nama_supp) or $nama_supp == 'ALL'){
            $sql = mysqli_query($conn2,"select *,sum(total_amount) ttl_amount from (select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user,updated_at from ir_invoice_supp_h where status = 'Post Pch To Fin') a INNER JOIN (select nama_trans,no_trans,tgl_trans,doc_number doc_num from ir_trans_invoice_supp where status = 'post' and nama_trans = 'TPTF' GROUP BY doc_number) b on b.doc_num = a.doc_number group by no_trans");                
            }else {
            $sql = mysqli_query($conn2,"select *,sum(total_amount) ttl_amount from (select doc_number,tgl_penerimaan,nama_supp,total_amount, status, CONCAT(created_by,' (',created_date,')') create_user,updated_at from ir_invoice_supp_h where status = 'Post Pch To Fin' and nama_supp = '$nama_supp') a INNER JOIN (select nama_trans,no_trans,tgl_trans,doc_number doc_num from ir_trans_invoice_supp where status = 'post' and nama_trans = 'TPTF' GROUP BY doc_number and nama_supp = '$nama_supp') b on b.doc_num = a.doc_number group by no_trans");
            }
                                                                         
            while($row = mysqli_fetch_array($sql)){                               
                    echo'<tr>                       
                           <td style="width:50px;" value="'.$row['no_trans'].'">'.$row['no_trans'].'</td>
                            <td style="width:100px;" value="'.$row['tgl_trans'].'">'.date("d-M-Y",strtotime($row['tgl_trans'])).'</td>
                            <td style="display: none;" value="'.$row['doc_number'].'">'.$row['doc_number'].'</td>
                            <td style="display: none;" value="'.$row['tgl_penerimaan'].'">'.date("d-M-Y",strtotime($row['tgl_penerimaan'])).'</td>
                            <td style="display: none;" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                            <td style ="text-align: right;" class="dt_total" style="width:100px;" value="'.$row['ttl_amount'].'">'.number_format($row['ttl_amount'],2).'</td>
                            <td style="" value = "'.$row['updated_at'].'">'.$row['updated_at'].'</td>   
                            <td><a id="showapp" ><button style="border-radius: 6px" type="button" class="btn-xs btn-info"><i class="fa fa-thumbs-up"aria-hidden="true" style="padding-right: 10px; padding-left: 5px;"> Accept</i></button></a></td>                   
                        </tr>';                
                   
                    } ?>
                    </tbody>
                    </table> 
                    </div> 
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

    <div class="modal fade" id="modal-approve" data-target="#modal-approve" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-dark text-white">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h5 class="modal-title" id="txt_dp">ACCEPT TRANSFER INVOICE</h5>
        </div>
        <div class="container">
        <div class="row">
            <div id="txt_notrf" class="modal-body col-6" style="font-size: 14px;"></div>
          <div id="txt_tgl_trf" class="modal-body col-6" style="font-size: 14px;"></div>
          <div class="card-body table-responsive">
                <!-- <div class="d-flex justify-content-between mr-2 mb-1">
                    <div class="ml-auto">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text"  id="myInput" name="myInput" required autocomplete="off" placeholder="Search No kontrabon.." onkeyup="myFunction()">
                </div> -->
            <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr class="thead-dark">
                            <th style="width:5%"><input type="checkbox" id="select_all"></th>                        
                            <th style="display: none;">No Document</th>
                            <th style="display: none;">Document Date</th> 
                            <th style="width:15%;">No Kontrabon</th>
                            <th style="width:10%;">Kontrabon Date</th>                            
                            <th style="width:25%;">Supplier</th>
                            <th style="width:15%;">Total</th>
                            <th style="width:20%;">Transfer Date</th>

                        </tr>
                    </thead>

            <tbody id="data_invoice">
            
            </tbody>
            </table> 
            </div>      
        </div>
        </div>
        <div class="modal-footer">
            <form id="form-simpan">
        <button style="border-radius: 5px" type="button" class="btn-outline-primary btn-sm" name="approve" id="approve"><span class="fa fa-thumbs-up"></span> Accept</button>
    </form>
      </div>
        </div>       
                                
</div><!-- body-row END -->
</div>
</div>
                     
                    
<div class="box footer">   
        <form id="form-simpan">
           <div class="form-row col">
            <div class="col-md-3 mb-3">  
            </br>                            
            <button type="button" style="border-radius: 6px" class="btn-outline-warning" name="batal" id="batal" onclick="location.href='invoice_received.php'"><span class="fa fa-angle-double-left"></span> Back</button>         
            </div>
            </div>                                   
        </form>
        </div>        
                                
</div><!-- body-row END -->
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

<script type="text/javascript">
    $("table tbody tr").on("click", "#showapp", function(){                 
        var notrf = $(this).closest('tr').find('td:eq(0)').attr('value');
        var tgl_trf = $(this).closest('tr').find('td:eq(1)').attr('value');
        
        $.ajax({
            type:'POST',
            url:'cari_data_invoice.php',
            data: {'notrf':notrf},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#data_invoice').html(data);
                // $('#mytable2').dataTable({
                //     destroy: true,
                // });
                // alert(data);  
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 

        $('#txt_notrf').html('<b>No Document : </b>' + notrf + '');
        $('#txt_tgl_trf').html('<b>Document Date : </b>' + tgl_trf + ''); 
        $('#modal-approve').modal('show');
    });
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
        $("input[name='select[]']:checked").each(function () {                
        var no_dok = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_kbon = $(this).closest('tr').find('td:eq(3)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'approve_pchtofin.php',
            data: {'no_dok':no_dok, 'no_kbon':no_kbon, 'approve_user':approve_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'form_approve_fin.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        
            alert("Data Berhasil Di Approve");
               
    });
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#approve", function(){
        $("input[name='select[]']:not(:checked)").each(function () {                     
        var no_dok = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_kbon = $(this).closest('tr').find('td:eq(3)').attr('value');
        var approve_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'cancel_pchtofin.php',
            data: {'no_dok':no_dok, 'no_kbon':no_kbon, 'approve_user':approve_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                window.location = 'form_approve_fin.php';                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        // if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
        //     alert("Data Berhasil Di Cancel");
        // }else{
        //     alert("Silahkan Ceklist No Kontrabon");
        // }        
    });
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>
  
</body>

</html>
