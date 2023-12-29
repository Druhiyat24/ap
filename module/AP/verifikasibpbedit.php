<?php
  session_start();
  include '../../conn/conn.php'; 
  $user = $_SESSION['username'];    
?>

<!DOCTYPE html>
<html lang="en">

<head>
<style>
img {
  display: block;
  margin-left: auto;
  margin-right: auto;
  height: 30px;
}
.box {
  border-style: outset;
  box-sizing: border-box;
}
.form-control-plaintext {
  border: 1px solid grey;
}
.form-row {
  margin-right: 0;
  margin-left: -10px;
}
.filter-option {
    font-size: 12px;
}
.datatable_wrapper{
    font-size: 12px;
}

</style>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>VERIFIKASI BPB</title>

  <!-- Bootstrap core CSS -->
<link href="../css/4.1.1/main.css" rel="stylesheet">  
<link href="../css/4.1.1/bootstrap.min.css" rel="stylesheet">
<link href="../css/4.1.1/datatables.min.css" rel="stylesheet">
<link href="../css/4.1.1/datepicker3.css" rel="stylesheet">
<link href="../css/4.1.1/bootstrap-select.min.css" rel="stylesheet">
<link href="../fontawesome/css/font-awesome.min.css" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="../css/4.1.1/jquery.js"></script>

</head>
<body>
<!-- Bootstrap NavBar -->
<nav class="navbar navbar-expand-md navbar-dark bg-primary">
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">
        <img src="../img/NAG logo SIGN.png" alt="">
    </a>
    <a class="navbar-brand text-white"><b>PT.NIRWANA ALABARE GARMENT</b></a>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">Home <span class="sr-only">(current)</span></a>
            </li>
            <!--<li class="nav-item">
                <a class="nav-link" href="#top">Features</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#top">Pricing</a>
            </li>-->
            <!-- This menu is hidden in bigger devices with d-sm-none. 
           The sidebar isn't proper for smaller screens imo, so this dropdown menu can keep all the useful sidebar itens exclusively for smaller screens  -->
            <li class="nav-item dropdown d-sm-block d-md-none">
                <a class="nav-link dropdown-toggle" href="#" id="smallerscreenmenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Menu </a>
                <div class="dropdown-menu" aria-labelledby="smallerscreenmenu">
                    <a class="dropdown-item" href="#top">Pembelian</a>
                    <a class="dropdown-item" href="#top">Penjualan</a>
                    <a class="dropdown-item" href="#top">Tasks</a>
                    <a class="dropdown-item" href="#top">Etc ...</a>
                </div>
            </li><!-- Smaller devices menu END -->
        </ul>
    </div>
</nav><!-- NavBar END -->
<!-- Bootstrap row -->
<div class="row" id="body-row">
    <!-- Sidebar -->
    <div id="sidebar-container" class="sidebar-expanded d-none d-md-block">
        <!-- d-* hiddens the Sidebar in smaller devices. Its itens can be kept on the Navbar 'Menu' -->
        <!-- Bootstrap List Group -->
        <ul class="list-group">
            <!-- Separator with title -->
            <li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                <small>MAIN MENU</small>
            </li>
            <!-- /END Separator -->
            <!-- Menu with submenu -->
            <a href="#submenu1" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-money fa-fw mr-3"></span>
                    <span class="menu-collapsed">Pembelian</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id='submenu1' class="collapse sidebar-submenu">
                <a href="kontrabon.php" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">Kontra Bon</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">List Kontra Bon</span>
                </a>
                <a href="verifikasibpb.php" class="list-group-item list-group-item-action bg-dark text-white active">
                    <span class="menu-collapsed">Verifikasi BPB</span>
                </a>                
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">Maintain Kontra Bon</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">List Payment</span>
                </a>                
            </div>
            <a href="#submenu2" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-btc fa-fw mr-3"></span>
                    <span class="menu-collapsed">Penjualan</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id='submenu2' class="collapse sidebar-submenu">
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">Settings</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">Password</span>
                </a>
            </div>
            <!-- Separator with title -->
            <li class="list-group-item sidebar-separator-title text-muted d-flex align-items-center menu-collapsed">
                <small>LAIN-LAIN</small>
            </li>
            <!-- /END Separator -->
            <a href="#submenu3" data-toggle="collapse" aria-expanded="false" class="bg-dark list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-book fa-fw mr-3"></span>
                    <span class="menu-collapsed">Laporan</span>
                    <span class="submenu-icon ml-auto"></span>
                </div>
            </a>
            <!-- Submenu content -->
            <div id='submenu3' class="collapse sidebar-submenu">
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">Pembelian</span>
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <span class="menu-collapsed">Penjualan</span>
                </a>
            </div>
            <!--<a href="#" class="bg-dark list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-envelope-o fa-fw mr-3"></span>
                    <span class="menu-collapsed">Messages <span class="badge badge-pill badge-primary ml-2">5</span></span>
                </div>
            </a>-->
            <!-- Separator without title -->
            <li class="list-group-item sidebar-separator menu-collapsed"></li>
            <!-- /END Separator -->
            <a href="function/logout.php" class="bg-dark list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span class="fa fa-power-off fa-fw mr-3"></span>
                    <span class="menu-collapsed">Logout</span>
                </div>
            </a>
            <a href="#top" data-toggle="sidebar-colapse" class="bg-dark list-group-item list-group-item-action d-flex align-items-center">
                <div class="d-flex w-100 justify-content-start align-items-center">
                    <span id="collapse-icon" class="fa fa-2x mr-3"></span>
                    <span id="collapse-text" class="menu-collapsed">Collapse</span>
                </div>
            </a>
        </ul><!-- List Group END-->
    </div><!-- sidebar-container END -->
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
                <option value="" disabled selected="true">Pilih Supplier</option>                                 
                <?php                 
                $sql1 = mysql_query("select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC",$conn1);
                $no_bpb = $_GET['no_bpb'];                
                $sql2 = mysql_query("select supplier from bpb where no_bpb = '$no_bpb'",$conn2);
                $row2 = mysql_fetch_array($sql2);
                while ($row1 = mysql_fetch_array($sql1)) {
                    $data = $row1['Supplier'];
                    if($row1['Supplier'] == $row2['supplier']){
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
            $no_bpb = $_GET['no_bpb'];
            $sql = mysql_query("select start_date from bpb where no_bpb = '$no_bpb'",$conn2);
            $row = mysql_fetch_array($sql);
            $start_date = $row['start_date'];            

            if(!empty($start_date)) {
               echo date("d-m-Y",strtotime($start_date));
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
            $no_bpb = $_GET['no_bpb'];
            $sql = mysql_query("select end_date from bpb where no_bpb = '$no_bpb'",$conn2);
            $row = mysql_fetch_array($sql);                                    
            $end_date = $row['end_date'];

            if(!empty($end_date)) {
               echo date("d-m-Y",strtotime($end_date));
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
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;"><input type="checkbox" id="select_all"></th>                        
                            <th style="width:50px;">No BPB</th>
                            <th style="width:100px;">Tgl BPB</th>
                            <th style="width:100px;">No PO</th>                                                                                
                            <th style="width:100px;">Supplier</th>
                            <th style="width:100px;">TOP</th>
                            <th style="width:100px;display: none;">Currency</th>
                            <th style="width:100px;display: none;">Confirm</th>                                                                                    
                        </tr>
                    </thead>

            <tbody>
            <?php
            $no_bpb = $_GET['no_bpb'];
            $sql1 = mysql_query("select supplier, start_date, end_date, ceklist from bpb where no_bpb = '$no_bpb'");
            while($row1 = mysql_fetch_array($sql1)) {            
            $nama_supp = $row1['supplier'];
            $start_date = date("Y-m-d",strtotime($row1['start_date']));
            $end_date = date("Y-m-d",strtotime($row1['end_date']));
            $ceklist = $row1['ceklist'];
            }            

            if($nama_supp == 'ALL'){
            $query = mysql_query("select no_bpb from bpb",$conn2);

            $sql = mysql_query("select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, bpb.curr, bpb.confirm_by from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier where bpb.confirm='Y' and status_retur = 'N' and bpb.bpbno_int not in ('$query') and bpb.bpbdate between '$start_date' and '$end_date' group by bpb.bpbno_int",$conn1);                
            }else {

            $query = mysql_query("select no_bpb from bpb",$conn2);

            $sql = mysql_query("select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, bpb.curr, bpb.confirm_by from bpb INNER JOIN po_header on po_header.pono = bpb.pono INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier where bpb.confirm='Y' and status_retur = 'N' and Supplier='$nama_supp' and bpb.bpbno_int not in ('$query') and bpb.bpbdate between '$start_date' and '$end_date' group by bpb.bpbno_int",$conn1);
            }                
            if (!empty($nama_supp && $start_date && $end_date)) {                                              
                while($row = mysql_fetch_array($sql)){
                    $bpb = $row['bpbno_int'];
                    echo'<tr>';
                    if($bpb == $no_bpb){
                            echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" checked=checked</td>';
                        }else{
                            echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value=""</td>';
                        }                        
                            echo '<td style="width:50px;" value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
                            <td style="width:100px;" value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
                            <td style="width:100px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                                                                                                        
                            <td style="width:50px;" value="'.$row['Supplier'].'">'.$row['Supplier'].'</td>
                            <td style="width:50px;" value="'.$row['jml_pterms'].'">'.$row['jml_pterms'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['confirm_by'].'">'.$row['confirm_by'].'</td>
                        </tr>';
                    }
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
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>          
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
            <button type="button" class="btn-primary" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Simpan</button>                
            <button type="submit" class="btn-danger" name="batal" id="batal"><span class="fa fa-times"></span> Batal</button>           
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
    var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(3)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(4)').attr('value');
    var top = $(this).closest('tr').find('td:eq(5)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(6)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(7)').attr('value');

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
        var create_user = '<?php echo $user ?>';
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;

        $.ajax({
            type:'POST',
            url:'insertfmvbpb.php',
            data: {'no_bpb':no_bpb, 'ceklist':ceklist, 'create_user':create_user, 'start_date':start_date, 'end_date':end_date},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                console.log(response);
                alert(response);
                window.location = 'verifikasibpb.php';
                                               
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data Berhasil Di Simpan");
        }else{
            alert("Silahkan Ceklist No BPB Dahulu");
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
