<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">KONTRA BON FTR DP</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="nokontrabon"><b>No Kontra Bon</b></label>
            <?php
            $sql = mysqli_query($conn2,"select max(no_kbon) from kontrabon_dp");
            $row = mysqli_fetch_array($sql);
            $kodeBarang = $row['max(no_kbon)'];
            $urutan = (int) substr($kodeBarang, 14, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("Y");
            $huruf = "SI/DP/$thn/$bln/";                
            $kodeBarang = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="nokontrabon" name="nokontrabon" value="'.$kodeBarang.'">'
            ?>
            </div>
            <div class="col-md-3 mb-3">            
            <label for="tanggal"><b>Kontra Bon Date <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php             
            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="jurnal"><b>Journal</b></label>            
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="jurnal" name="jurnal" 
            value="0" placeholder="<?php echo "KONTRA BON" ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="matauang"><b>Currency</b></label>
            <?php
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select curr from ftr_dp where supp = '$nama_supp'");
            $row = mysqli_fetch_array($sql);
            $value = isset($row['curr']) ? $row['curr'] : null;
            if (!empty($nama_supp)) {
            echo '<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="matauang" name="matauang" value="'.$value.'">';  
            } else {
            echo '<input type="text" readonly class="form-control-plaintext" id="matauang" name="matauang" value="">';
            }
            ?>                        
            </div>                                         
    </div>

    <div class="form-row">
            <div class="col-md-6 mb-3">            
            <label for="no_faktur"><b>No Tax Invoice <i style="color: red;">*</i></b></label>            
            <input type="text" style="font-size: 14px;" class="form-control" id="no_faktur" name="no_faktur" 
            value="<?php 
            $no_faktur = isset($_POST['no_faktur']) ? $_POST['no_faktur']: null;
            echo $no_faktur; 
            ?>" required>
            </div>

            <div class="col-md-6 mb-3">            
            <label for="txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" class="form-control" id="txt_inv" name="txt_inv" 
            value="<?php
            $txt_inv = isset($_POST['txt_inv']) ? $_POST['txt_inv']: null;
                echo $txt_inv; 
            ?>" required>
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_tglsi"><b>Supplier Invoice Date <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 14px;" class="form-control tanggal" name="txt_tglsi" id="txt_tglsi" 
            value="<?php 
            if(!empty($_POST['txt_tglsi'])) {
                echo $_POST['txt_tglsi'];
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 14px;" class="form-control tanggal1" name="txt_tgltempo" id="txt_tgltempo" 
            value="<?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }            
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            $sql = mysqli_query($conn2,"select distinct max(tgl_ftr_dp) from ftr_dp where supp = '$nama_supp' ");
            $row = mysqli_fetch_array($sql);
            $tgl = $row['max(tgl_ftr_dp)'];

            if(!empty($nama_supp)) {
                echo date("d-m-Y",strtotime($tgl));
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

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
            <label for="nama_supp"><b>Supplier</b></label>            
            <div class="input-group">
            <input type="text" readonly style="font-size: 14px; width: 500px;" class="form-control" name="txt_supp" id="txt_supp" 
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
                <option value="" disabled selected="true">select</option>                
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
                <label><b>DP Date</b></label>
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
                            <th style="width:50px;">NO FTR</th>
                            <th style="width:50px;">NO PO</th>                            
                            <th style="width:50px;">PO Date</th>                            
                            <th style="width:100px;">Total PO</th>
                            <th style="width:100px;">DP Amount</th>
                            <th style="width:100px;display: none;">Tax (PPh)</th>                            
                            <th style="width:100px;">Balance</th>
                            <th style="width:100px;display: none;">Status</th>
                            <th style="width:100px;display: none;">Keterangan</th>   
                             <th style="width:100px;display: none;">Status</th>
                            <th style="width:100px;display: none;">Keterangan</th>                                                      
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
            $persen = '';            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }

            $querys = mysqli_query($conn2,"select distinct (no_ftr_dp) from ftr_dp");
            $rows = mysqli_fetch_array($querys);
            $no_dp = isset($rows['no_ftr_dp']) ? $rows['no_ftr_dp'] : null;                        

            $q = mysqli_query($conn1,"select idtax, kriteria, percentage from mtax where category_tax = 'PPH'");            
            while($rs = mysqli_fetch_array($q)){
            $persen .= '<option data-idtax="'.$rs['idtax'].'" value="'.$rs['percentage'].'">'.$rs['kriteria'].'</option>';
            }                       

            $sql = mysqli_query($conn2,"select no_ftr_dp, tgl_ftr_dp, no_po, tgl_po, SUM(total) as sub, SUM(dp_value) as dp, SUM(balance) as balance, supp as supplier, status, keterangan, create_user from ftr_dp where supp = '$nama_supp' and tgl_ftr_dp between '$start_date' and '$end_date' and status = 'Approved' and is_invoiced != 'Invoiced' group by no_ftr_dp");                                                     
            while($row = mysqli_fetch_array($sql)){
            $dp_no = $row['no_ftr_dp'];
            $querys = mysqli_query($conn2,"select no_dp, no_po, status from kontrabon_dp where no_dp = '$dp_no' and status != 'Cancel'");
            $rows = mysqli_fetch_array($querys);
            $n_dp = isset($rows['no_dp']);
            $stat = isset($rows['status']);                            
            $sub = $row['sub'];
            $dp = $row['dp'];
            $balance = $row['balance'];
            if($dp_no == $n_dp and $stat != 'Cancel'){
                echo '';
            }else{
                    echo '<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>
                            <td style="width:50px;" value="'.$row['no_ftr_dp'].'">'.$row['no_ftr_dp'].'</td>                                                    
                            <td style="width:50px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>                            
                            <td style="width:100px;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                            
                            <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td class="dt_tax" style="width:100px;text-align:right;" data-dp="'.$dp.'">'.number_format($dp,2).'</td>                           
                            <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$balance.'">'.number_format($balance,2).'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['create_user'].'">'.$row['create_user'].'</td>
                            <td style="display: none;" value="'.$row['tgl_ftr_dp'].'">'.date("d-M-Y",strtotime($row['tgl_ftr_dp'])).'</td> 
                            <td style="display: none;" value="'.$row['status'].'">'.$row['status'].'</td>
                            <td style="display: none;" value="'.$row['keterangan'].'">'.$row['keterangan'].'</td>                                                                                                               
                        </tr>';
                }        
                }                               
                    ?>
            </tbody>                    
            </table>                    
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 100px;"><b>Total PO</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="subtotal_h" id="subtotal_h" value="">
            </div>
            </div>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>DP Amount</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pajak" id="pajak" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pajak_h" id="pajak_h" value="">
            </div>
            </div>
           <!--  <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 100px;"><b>Tax (PPh)</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pph" id="pph" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pph_h" id="pph_h" value="">
            </div>
            </div> -->            
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 100px;"><b>Total Amount</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="total_h" id="total_h" value="">
            </div>
            </div>
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='kontrabonftrdp.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalbpb" data-target="#mymodalbpb" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_dp"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgldp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_keterangan" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                              
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

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal1').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true,
        startDate: new Date()
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
    var pajak = $(this).closest('tr').find('td:eq(5)').attr('data-dp');
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
    var sum_sub = 0;
    var sum_dp = 0;
    var ceklist = 0;
    var sum_pph = 0;
    var sum_total = 0;
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);         
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var dp = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-dp'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
    var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]');
    select_pph.prop('disabled', false);        
    sum_sub += price;
    sum_dp += dp;
    sum_pph += price * (pph / 100);
    sum_total = sum_dp - sum_pph;     
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub);       
    $("#pajak").val(formatMoney(sum_dp));
    $("#pajak_h").val(sum_dp);    
    // $("#pph").val(formatMoney(sum_pph));
    // $("#pph_h").val(sum_pph);        
    $("#total").val(formatMoney(sum_total));
    $("#total_h").val(sum_total);    
    $("#select").val("1");                      
});        
</script>

<!-- <script type="text/javascript">
    $("select[name=combo_pph]").on('change', function(){
    var sum_sub = 0;
    var sum_dp = 0;
    var ceklist = 0;
    var sum_pph = 0;
    var sum_total = 0;
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var dp = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-dp'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;            
    sum_sub += price;
    sum_dp += dp;
    sum_pph += price * (pph / 100);
    sum_total = sum_dp - sum_pph;     
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub);       
    $("#pajak").val(formatMoney(sum_dp));
    $("#pajak_h").val(sum_dp);    
    $("#pph").val(formatMoney(sum_pph));
    $("#pph_h").val(sum_pph);        
    $("#total").val(formatMoney(sum_total));
    $("#total_h").val(sum_total);
    $("#select").val("1");
    });
</script>
 -->
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
    $("#form-simpan").on("click", "#simpan", function(){
        var no_kbon_h = document.getElementById('nokontrabon').value;
        var tgl_kbon_h = document.getElementById('tanggal').value;
        var nama_supp_h = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur_h = document.getElementById('no_faktur').value;
        var supp_inv_h = document.getElementById('txt_inv').value;
        var tgl_inv_h = document.getElementById('txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('txt_tgltempo').value;        
        var curr_h = document.getElementById('matauang').value;
        var sub_h = document.getElementById('subtotal_h').value;
        var tax_h = document.getElementById('pajak_h').value;
        // var pph_h = document.getElementById('pph_h').value;
        var total_h = document.getElementById('total_h').value;
        var create_user_h = '<?php echo $user; ?>';        
        $.ajax({
            type:'POST',
            url:'insertkbon_h_dp.php',
            data: {'no_kbon_h':no_kbon_h, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'total_h':total_h},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert(response);
                window.location = 'kontrabonftrdp.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });                
        $("input[type=checkbox]:checked").each(function () {
        var no_kbon = document.getElementById('nokontrabon').value;        
        var tgl_kbon = document.getElementById('tanggal').value;
        var jurnal = document.getElementById('jurnal').value;
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur = document.getElementById('no_faktur').value;
        var supp_inv = document.getElementById('txt_inv').value;
        var tgl_inv = document.getElementById('txt_tglsi').value;
        var tgl_tempo = document.getElementById('txt_tgltempo').value;        
        var curr = document.getElementById('matauang').value;                               
        var no_dp = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = '<?php echo $user; ?>';
        var ceklist = document.getElementById('select').value;          
        var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
        var dp = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-dp'),10) ||0;
        var total = parseFloat($(this).closest('tr').find('td:eq(7)').attr('data-total'),10) ||0;
        // var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
        // var idtax = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').attr('data-idtax');
        var sum_sub = 0;
        var sum_dp = 0;
        var sum_total = 0;
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;        
        sum_sub += price;
        sum_dp += dp;     
        sum_total +=total;
        $.ajax({
            type:'POST',
            url:'insertkbon_dp.php',
            data: {'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'jurnal':jurnal, 'no_dp':no_dp, 'no_po':no_po,
            'nama_supp':nama_supp, 'tgl_po':tgl_po, 'no_faktur':no_faktur, 'supp_inv':supp_inv, 'tgl_inv':tgl_inv, 'tgl_tempo':tgl_tempo,
            'curr':curr, 'ceklist':ceklist, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_dp':sum_dp, 'sum_total':sum_total, 'start_date':start_date, 'end_date':end_date},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert(response);
                window.location = 'kontrabonftrdp.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        });                
        if(document.querySelectorAll("input[name='select[]']:checked").length >= 1){
            alert("Data saved successfully");
        }else{
            alert("Please check the FTR DP number");
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
    $('#mymodalbpb').modal('show');
    var noftrdp = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_dp = $(this).closest('tr').find('td:eq(9)').text();
    var supp = $(this).closest('tr').find('td:eq(7)').attr('value');
    var curr = document.getElementById('matauang').value;
    var create_user = $(this).closest('tr').find('td:eq(8)').attr('value');
    var status = $(this).closest('tr').find('td:eq(10)').attr('value');
    var keterangan = $(this).closest('tr').find('td:eq(11)').attr('value');    

    $.ajax({
    type : 'post',
    url : 'ajaxdp.php',
    data : {'noftrdp': noftrdp},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_dp').html(noftrdp);
    $('#txt_tgldp').html('Tgl FTR DP : ' + tgl_dp + '');
    $('#txt_supp2').html('Supplier : ' + supp + '');
    $('#txt_curr').html('Currency : ' + curr + '');
    $('#txt_create_user').html('Create By : ' + create_user + '');        
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_keterangan').html('Keterangan : ' + keterangan + '');                         
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
