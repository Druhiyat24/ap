<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">FORM LIST PAYMENT</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="nopayment"><b>No List Payment</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(no_payment) from list_payment where id = (select max(id) from list_payment)");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_payment)'];
            $urutan = (int) substr($kodepay, 12, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "LP/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="nopayment" name="nopayment" value="'.$kodepay.'">'
            ?>
            </div>
            <div class="col-md-2 mb-3">            
            <label for="tanggal"><b>List Payment Date <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tanggal = date("Y-m-d",strtotime($_POST['tanggal']));
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_kbon) from kontrabon_h where nama_supp = '$nama_supp'and status = 'Approved' and balance >= 1 and tgl_kbon between '$start_date' and '$end_date' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_kbon)'];         
    
            // $top = 30;

            if(!empty($nama_supp)) {
                if($tanggal < $tgl){
                echo date("d-m-Y",strtotime($tgl));
                }else{
                  echo date("Y-m-d",strtotime($tanggal));  
                }
            }
            else{
                echo date("Y-m-d");
            }  ?>">

            <input type="hidden" style="font-size: 14px;" name="tgl_perhitungan" id="tgl_perhitungan" class="form-control">

            <input type="hidden" style="font-size: 14px;" name="tanggal1" id="tanggal1" class="form-control-plaintext" 
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_kbon) from kontrabon_h where nama_supp = '$nama_supp'and status = 'Approved' and balance >= 1 and tgl_kbon between '$start_date' and '$end_date' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_kbon)'];         
    
            // $top = 30;

            if(!empty($nama_supp)) {
                
                echo date("Y-m-d",strtotime($tgl));
            }
            else{
                echo date("2022-01-01");
            } 

                
        ?>">
            </div>

            <div class="col-md-2 mb-3">            
            <label for="tanggal"><b>Due Date <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="due_date" id="due_date" class="form-control due_date" 
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $due_date = date("Y-m-d",strtotime($_POST['due_date']));
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select max(b.tgl_tempo) as tgl_tempo from kontrabon b inner join kontrabon_h a on a.no_kbon = b.no_kbon where b.tgl_kbon between '$start_date' and '$end_date' and b.nama_supp = '$nama_supp' and b.status = 'Approved' and a.balance != '0' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['tgl_tempo'];         
    
            // $top = 30;

            if(!empty($nama_supp)) {
                echo date("Y-m-d",strtotime($tgl)); 
            }
            else{
                echo date("Y-m-d");
            } 
        ?>">

        <input type="hidden" style="font-size: 14px;" name="due_date1" id="due_date1" class="form-control-plaintext" 
            value="<?php             
            $start_date ='';
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select max(b.tgl_tempo) as tgl_tempo from kontrabon b inner join kontrabon_h a on a.no_kbon = b.no_kbon where b.tgl_kbon between '$start_date' and '$end_date' and b.nama_supp = '$nama_supp' and b.status = 'Approved' and a.balance != '0' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['tgl_tempo'];         
    
            // $top = 30;

            if(!empty($nama_supp)) {
                
                echo date("Y-m-d",strtotime($tgl));
            }
            else{
                echo date("2022-01-01");
            } 

                
        ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="jurnal"><b>Journal</b></label>            
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="jurnal" name="jurnal" value="0"
            placeholder="<?php echo "Payment" ?>">
            </div>            

            <div class="col-md-4 mb-3">            
            <label for="memo"><b>Descriptions</b></label>          
            <input type="text" style="font-size: 14px;" class="form-control" name="memo" id="memo" 
            value="<?php             
            if(!empty($_POST['memo'])) {
                echo $_POST['memo'];
            }
            else{
                echo '';
            } ?>">
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
        <h4 class="modal-title" id="Heading">Choose Supplier</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
            <label for="nama_supp"><b>Supplier</b></label>
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Select</option>                
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
                            <th style="width:50px;">No Kontrabon</th>
                            <th style="width:100px;">Kontrabon Date</th>                            
                            <th style="width:100px;">Total Kontrabon</th>                            
                            <th style="width:50px;">TOP</th>
                            <th style="width:100px;">Outstanding</th>
                            <th style="width:100px;">Amount</th>                                                        
                            <th style="width:100px;">Due Date</th>
                            <th style="width:100px;">Currency</th>                            
                            <th style="width:100px;display: none;">Supplier</th>
                            <th style="width:100px;display: none;">Status</th>
                            <th style="width:100px;display: none;">Create User</th>                            
                            <th style="width:100px;display: none;">No Faktur</th>                        
                            <th style="width:100px;display: none;">Tgl Invoice</th>
                            <th style="width:100px;display: none;">Tgl Invoice</th>
                              <th style="width:100px;display: none;">Status</th>
                            <th style="width:100px;display: none;">Create User</th>                            
                            <th style="width:100px;display: none;">No Faktur</th>                        
                            <th style="width:100px;display: none;">Tgl Invoice</th>
                            <th style="width:100px;display: none;">Tgl Invoice</th>
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

            $sql = mysqli_query($conn2,"select kontrabon.no_kbon as no_kbon, kontrabon.tgl_kbon as tgl_kbon, kontrabon.no_bpb as no_bpb, kontrabon.no_po as no_po, kontrabon.tgl_bpb as tgl_bpb, kontrabon.tgl_po as tgl_po, SUM(kontrabon.pph_value) as pph_value, kontrabon_h.total as total, kontrabon.nama_supp as supplier, kontrabon_h.balance as balance, kontrabon.tgl_tempo as tgl_tempo, kontrabon.curr as matauang, kontrabon.status as status, kontrabon.create_user as create_user, kontrabon.no_faktur as no_faktur, kontrabon.supp_inv as supp_inv, kontrabon.tgl_inv as tgl_inv,kontrabon_h.no_coa,kontrabon_h.nama_coa from kontrabon inner join kontrabon_h on kontrabon_h.no_kbon = kontrabon.no_kbon where kontrabon.tgl_kbon between '$start_date' and '$end_date' and kontrabon.nama_supp = '$nama_supp' and kontrabon.status = 'Approved' and kontrabon_h.balance != '0' group by no_kbon");


            while($row = mysqli_fetch_array($sql)){
            $tgl_kbon = $row['tgl_kbon'];
            $tgl_tempo = $row['tgl_tempo'];
            $diff = strtotime($tgl_tempo) - strtotime($tgl_kbon);
            $date_diff = round($diff / 86400);                     
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['no_kbon'].'">'.$row['no_kbon'].'</td>
                            <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_kbon'])).'" value="'.$row['tgl_kbon'].'">'.date("d-M-Y",strtotime($row['tgl_kbon'])).'</td>
                            <td style ="text-align: right;" class="dt_total" style="width:100px;" data-total="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                            <td value="'.$date_diff.'">'.$date_diff.' Day</td>
                            <td style ="text-align: right;" class="dt_out" style="width:100px;" data-out="'.$row['balance'].'">'.$row['balance'].'</td>                            
                            <td style="width:100px;">
                            <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount" value="" disabled>
                            </td>                            
                            <td style="width:100px;" value="'.$row['tgl_tempo'].'">'.date("d-M-Y",strtotime($row['tgl_tempo'])).'</td>                            
                            <td style="width:50px;" value="'.$row['matauang'].'">'.$row['matauang'].'</td>                            
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="display: none;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                                                        
                            <td style="display: none;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                            
                            <td style="display: none;" value="'.$row['pph_value'].'">'.$row['pph_value'].'</td>
                            <td style="display: none;" value="'.$row['supp_inv'].'">'.$row['supp_inv'].'</td>
                            <td style="display: none;" value="'.$row['create_user'].'">'.$row['create_user'].'</td>
                            <td style="display: none;" value="'.$row['status'].'">'.$row['status'].'</td>
                            <td style="display: none;" value="'.$row['no_faktur'].'">'.$row['no_faktur'].'</td>
                            <td style="display: none;" value="'.$row['tgl_inv'].'">'.date("d-M-Y",strtotime($row['tgl_inv'])).'</td>
                            <td style="display: none;" value="'.$row['no_coa'].'">'.$row['no_coa'].'</td>
                            <td style="display: none;" value="'.$row['nama_coa'].'">'.$row['nama_coa'].'</td>
                 
                        </tr>';
                      }                  
                    ?>
            </tbody>                    
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 100px;"><b>OutStanding</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 14px; text-align: right;" readonly>
            </div>
            </div>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Amount</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pajak" id="pajak" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>          
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 100px;"><b>Balance</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
            </div>
            </div>
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='payment.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
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
        format: "yyyy-mm-dd",
        autoclose:true,
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
    var sum_kb = 0;
    var sum_amount = 0;
    var ceklist = 0;
    var sum_total = 0;
    var dates= '';
    $(this).closest('tr').find('td:eq(6) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(6) input').val("");                  
    $("input[type=checkbox]:checked").each(function () { 
    var tanggal = document.getElementById('tgl_perhitungan').value || '01-01-1970'; 
    var tglkbon = $(this).closest('tr').find('td:eq(2)').attr('dates');       
    var kb = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
    var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;    
    var select_amount = $(this).closest('tr').find('td:eq(6) input');
    select_amount.prop('disabled', false);              
    sum_kb += kb;
    sum_amount += amount;
    sum_total = sum_kb - sum_amount;

    if(tglkbon > tanggal){
      dates = tglkbon;  
    }else{
        dates = tanggal;
    }     
    });
    $("#subtotal").val(formatMoney(sum_kb));
    $("#pajak").val(formatMoney(sum_amount));    
    $("#total").val(formatMoney(sum_total));
    $("#tgl_perhitungan").val(dates);
    // $("#select").val("1");                    
});        
</script>

<script type="text/javascript">
    $("input[name=txt_amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var kb = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
    var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) || 0;
    var select_amount = $(this).closest('tr').find('td:eq(6) input');                
    if(amount > balance){
        sum_kb += kb;
        select_amount.val(balance);
        sum_amount += balance;
        sum_total = sum_kb - sum_amount;
    }else{
    sum_kb += kb;
    sum_amount += amount;
    sum_total = sum_kb - sum_amount;        
    }   
    });
    $("#subtotal").val(formatMoney(sum_kb));
    $("#pajak").val(formatMoney(sum_amount));    
    $("#total").val(formatMoney(sum_total));
    });
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
        var no_payment = document.getElementById('nopayment').value;        
        var tgl_payment = document.getElementById('tanggal').value;
        var tgl_lp_p = document.getElementById('tgl_perhitungan').value;
        var keterangan = document.getElementById('memo').value;        
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var curr = $(this).closest('tr').find('td:eq(8)').attr('value');                               
        var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_kbon = $(this).closest('tr').find('td:eq(2)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(10)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(11)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(12)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(13)').attr('value');
        var pph_value = $(this).closest('tr').find('td:eq(14)').attr('value');
        var create_user = '<?php echo $user; ?>';         
        var total_kbon = parseFloat($(this).closest('tr').find('td:eq(3)').attr('data-total'),10) ||0;
        var top = $(this).closest('tr').find('td:eq(4)').attr('value');
        var outstanding = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-out'),10) ||0;
        var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) ||0;
        var tgl_tempo = $(this).closest('tr').find('td:eq(7)').attr('value');
        var duedate = document.getElementById('due_date').value;
        var no_coa = $(this).closest('tr').find('td:eq(20)').attr('value');
        var nama_coa = $(this).closest('tr').find('td:eq(21)').attr('value');
        var balance = 0;
        balance += outstanding - amount;
        if(amount >= 1 && tgl_payment >= tgl_lp_p){       
        $.ajax({
            type:'POST',
            url:'insertlistpayment.php',
            data: {'no_payment':no_payment, 'tgl_payment':tgl_payment, 'keterangan':keterangan, 'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'nama_supp':nama_supp, 'curr':curr, 'create_user':create_user, 'total_kbon':total_kbon, 'top':top, 'outstanding':outstanding, 'amount':amount, 'tgl_tempo':tgl_tempo, 'balance':balance, 'no_bpb':no_bpb, 'tgl_bpb':tgl_bpb, 'no_po':no_po, 'tgl_po':tgl_po, 'pph_value':pph_value, 'duedate':duedate, 'no_coa':no_coa, 'nama_coa':nama_coa},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                 // alert("Data saved successfully");
                 // alert(response);
                window.location = 'payment.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
    }
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            alert("Please check the Kontrabon number");            
        }else if (document.getElementById('tanggal').value < document.getElementById('tgl_perhitungan').value){
        alert("List Payment Date Can't be less than Kontrabon Date ");
        } else{
            alert("data saved successfully");
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
