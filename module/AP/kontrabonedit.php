<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">KONTRA BON</h2>
<div class="box">
    <div class="box header">
<form id="form-data" method="post">
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="nokontrabon"><b>No Kontra Bon</b></label>
            <?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select no_kbon from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="nokontrabon" name="nokontrabon" value="'.$row['no_kbon'].'">'
            ?>
            </div>
            <div class="col-md-3 mb-3">            
            <label for="tanggal"><b>Tanggal Kontra Bon <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
            value="<?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select tgl_kbon from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);                         
            if(!empty($no_kbon)) {
                echo date("d-m-Y",strtotime($row['tgl_kbon']));
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="jurnal"><b>Jurnal</b></label>            
            <input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="jurnal" name="jurnal" 
            value="<?php echo "KONTRA BON" ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="matauang"><b>Currency</b></label>
            <?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select curr from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);            
            if (!empty($no_kbon)) {
            echo '<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="matauang" name="matauang" value="'.$row['curr'].'">';   
            } else {
            echo '<input type="text" readonly class="form-control-plaintext" id="" name="matauang" value="">';
            }
            ?>                       
            </div>                                         
    </div>

    <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="no_faktur"><b>No Faktur Pajak <i style="color: red;">*</i></b></label>            
            <input type="text" style="font-size: 14px;" class="form-control" id="no_faktur" name="no_faktur" 
            value="<?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select no_faktur from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);             
            echo $row['no_faktur']; 
            ?>" required>
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>          
            <input type="text" style="font-size: 14px;" class="form-control" id="txt_inv" name="txt_inv" 
            value="<?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select supp_inv from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);             
            echo $row['supp_inv'];
            ?>" required>
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_tglsi"><b>Tanggal Supplier Invoice <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 14px;" class="form-control tanggal" name="txt_tglsi" id="txt_tglsi" 
            value="<?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select tgl_inv from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);                          
            if(!empty($no_kbon)) {
                echo date("d-m-Y",strtotime($row['tgl_inv']));
            }
            else{
                echo date("d-m-Y");
            } ?>">
            </div>

            <div class="col-md-3 mb-3">            
            <label for="txt_tgltempo"><b>Tanggal Jatuh Tempo <i style="color: red;">*</i></b></label>   
            <input type="text" style="font-size: 14px;" class="form-control tanggal" name="txt_tgltempo" id="txt_tgltempo" 
            value="<?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select tgl_tempo from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);                          
            if(!empty($no_kbon)) {
                echo date("d-m-Y",strtotime($row['tgl_tempo']));
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

            <div class="col-md-9 mb-3">
            <label for="nama_supp"><b>Supplier</b></label>            
            <div class="input-group">
            <input type="text" readonly style="font-size: 14px; width: 300px;" class="form-control" name="txt_supp" id="txt_supp" 
            value="<?php
            $no_kbon = $_GET['nokontrabon'];
            $sql = mysqli_query($conn2,"select nama_supp from kontrabon where no_kbon='$no_kbon'");
            $row = mysqli_fetch_array($sql);             
            echo $row['nama_supp'];
            ?>">

    <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Pilih Data</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form" method="post">
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">Pilih Supplier</option>                
                <?php                 
                $sql1 = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                $no_kbon = $_GET['nokontrabon'];
                $sql2 = mysqli_query($conn2,"select nama_supp from kontrabon where no_kbon='$no_kbon'");
                $row2 = mysqli_fetch_array($sql2);                
                while ($row1 = mysqli_fetch_array($sql1)) {
                    $data = $row1['Supplier'];
                    if($row1['Supplier'] == $row2['nama_supp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                <label><b>Pilih Tanggal BPB</b></label>
                <div class="input-group-append">           
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="start_date" name="start_date" 
                value="<?php
                $no_kontrabon = $_GET['nokontrabon'];
                $sql = mysqli_query($conn2,"select start_date from kontrabon where no_kbon = '$no_kontrabon'");
                $row = mysqli_fetch_array($sql);
                $start_date = $row['start_date'];

                if(!empty($no_kontrabon)) {
                    echo date("d-m-Y",strtotime($start_date));
                }
                else{
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <label class="col-md-1" for="end_date"><b>-</b></label>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="end_date" name="end_date" 
                value="<?php
                $no_kontrabon = $_GET['nokontrabon'];
                $sql = mysqli_query($conn2,"select end_date from kontrabon where no_kbon = '$no_kontrabon'");
                $row = mysqli_fetch_array($sql);
                $end_date = $row['end_date'];

                if(!empty($no_kontrabon)) {
                    echo date("d-m-Y",strtotime($end_date)); 
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
            <input type="button" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal" value="Pilih Data">
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
                            <th style="width:50px;">NO BPB</th>
                            <th style="width:50px;">NO PO</th>                            
                            <th style="width:50px;">Tanggal BPB</th>                            
                            <th style="width:100px;">SubTotal</th>
                            <th style="width:100px;">Tax (PPn)</th>
                            <th style="width:100px;">Tax (PPh)</th>                            
                            <th style="width:100px;">Total (BPB)</th>
                            <th style="width:100px;display: none;">TOP</th>
                            <th style="width:100px;display: none;">Confirm1</th>
                            <th style="width:100px;display: none;">Confirm2</th>
                            <th style="width:100px;display: none;">Supplier</th>
                            <th style="width:100px;display: none;">Tgl PO</th>                                                         
                            <!--<th style="width:50px;">Delete</th>-->
                        </tr>
                    </thead>

            <tbody>
            <?php
            $no_kontrabon = $_GET['nokontrabon'];
            $query = mysqli_query($conn2,"select no_bpb as bp, nama_supp, start_date, end_date from kontrabon where no_kbon = '$no_kontrabon'");
            $rows = mysqli_fetch_array($query);           
            $supp = $rows['nama_supp'];
            $nomor_bpb = $rows['bp'];
            $tgl_awal = $rows['start_date'];
            $tgl_akhir = $rows['end_date'];                                                                      

            $sql = mysqli_query($conn2,"select no_bpb, pono, tgl_bpb, SUM(qty * price) as sub, SUM((qty * price) * (tax / 100)) as tax, SUM((qty * price) + ((qty * price) * (tax / 100))) as total, top, confirm1, confirm2, supplier, tgl_po, is_invoiced from bpb_new where supplier = '$supp' and tgl_bpb between '$start_date' and '$end_date' and confirm2 != '' group by no_bpb");                                                     
            while($row = mysqli_fetch_array($sql)){
            $bpb = $row['no_bpb'];            
            $invoice = $row['is_invoiced'];
            
            $querys = mysqli_query($conn2,"select distinct no_bpb, no_kbon, pph_code, status, idtax from kontrabon where status !='Cancel'");
            $rows = mysqli_fetch_array($querys);
            $n_bpb = $rows['no_bpb'];
            $n_kbon = $rows['no_kbon'];
            $pph = $rows['pph_code'];
            $status = $rows['status'];
            $sub = '';
            $tax = '';
            $total = '';
            $select = '';           
            $sub = $row['sub'];
            $tax = $row['tax'];
            $total = $row['total'];

            $q = mysqli_query($conn2,"select distinct kontrabon.no_bpb, mtax.idtax, mtax.kriteria, mtax.percentage from mtax left join (select no_kbon, no_bpb, idtax from kontrabon) kontrabon on kontrabon.idtax = mtax.idtax where category_tax = 'PPH'");
            while($rs = mysqli_fetch_array($q)){
            $bpb2 = $rs['no_bpb'];
            $idtax2 = $rs['idtax'];
            if($bpb == $bpb2){
            $isSelected = ' selected="selected"';
            }else{
            $isSelected = ''; 
            }
            $select .= '<option data-idtax="'.$rs['idtax'].'" value="'.$rs['percentage'].'" "'.$isSelected.'">'.$rs['kriteria'].'</option>';
            }

                 
                        echo'<tr>
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value=""<?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>
                        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                            <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                            <td style="width:100px;" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                            <td style="text-align: right;" class="dt_price" style="width:100px;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                            <td style="text-align: right;" class="dt_tax" style="width:100px;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                            <td style="width:100px;">
                            <select name="combo_pph" id="combo_pph">
                            <option value="0">Non PPH</option>                                                        
                            '.$select.'
                            </select>
                            </td>                            
                            <td style="text-align: right;" class="dt_total" style="width:100px;" data-total="'.$total.'">'.number_format($total,2).'</td>
                            <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td>
                            <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                            <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                            <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                            <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>                                                                                                                
                        </tr>';                                                                                  
                }        
                    ?>
            </tbody>                    
            </table>

<?php
$no_kontrabon = $_GET['nokontrabon'];
$sql = mysqli_query($conn2,"select SUM(subtotal), SUM(tax), SUM(pph_value), SUM(total) from kontrabon where no_kbon = '$no_kontrabon'");
while($row = mysqli_fetch_array($sql)){
$subtotal = $row['SUM(subtotal)'];
$tax = $row['SUM(tax)'];
$pph = $row['SUM(pph_value)'];
$total = $row['SUM(total)'];
}
?>                                
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
                <label for="subtotal" class="col-form-label" style="width: 100px;"><b>Subtotal</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="subtotal" id="subtotal" value="<?php echo number_format($subtotal,2);?>" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="subtotal_h" id="subtotal_h" value="<?php echo $subtotal;?>">                
            </div>
            </div>
            <div class="form-row col">
                <label for="pajak" class="col-form-label" style="width: 100px;"><b>Tax (PPn)</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pajak" id="pajak" value="<?php echo number_format($tax,2);?>" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pajak_h" id="pajak_h" value="<?php echo $tax;?>">                
            </div>
            </div>
            <div class="form-row col">
                <label for="pph" class="col-form-label" style="width: 100px;"><b>Tax (PPh)</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="pph" id="pph" value="<?php echo number_format($pph,2);?>" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="pph_h" id="pph_h" value="<?php echo $pph;?>">                
            </div>
            </div>            
           <div class="form-row col">
                <label for="total" class="col-form-label" style="width: 100px;"><b>Total</b></label>
            <div class="col-md-3 mb-3">                              
                <input type="text" class="form-control-plaintext" name="total" id="total" value="<?php echo number_format($total,2);?>" placeholder="0.00" style="font-size: 14px;text-align: right;" readonly>
                <input type="hidden" name="total_h" id="total_h" value="<?php echo $total;?>">                
            </div>
            </div>
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" class="btn-primary" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Simpan</button>                
            <button type="button" class="btn-danger" name="batal" id="batal" onclick="location.href='kontrabon.php'"><span class="fa fa-times"></span> Batal</button>           
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalbpb" data-target="#mymodalbpb" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
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
          <div id="txt_supp2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                              
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

<!-- <script type="text/javascript"> 
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
</script> -->

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
    var sum_tax = 0;
    var ceklist = 0;
    var sum_pph = 0;
    var sum_total = 0;
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);        
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
    var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]');
    select_pph.prop('disabled', false);        
    sum_sub += price;
    sum_tax += tax;
    sum_pph += price * (pph / 100);
    sum_total = sum_sub + sum_tax - sum_pph;     
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub);       
    $("#pajak").val(formatMoney(sum_tax));
    $("#pajak_h").val(sum_tax);    
    $("#pph").val(formatMoney(sum_pph));
    $("#pph_h").val(sum_pph);        
    $("#total").val(formatMoney(sum_total));
    $("#total_h").val(sum_total);
    $("#select").val("1");                     
});        
</script>

<script type="text/javascript">
    $("select[name=combo_pph]").on('change', function(){
    var sum_sub = 0;
    var sum_tax = 0;
    var ceklist = 0;
    var sum_pph = 0;
    var sum_total = 0;
    $("input[type=checkbox]:checked").each(function () {        
    var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
    var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
    var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;            
    sum_sub += price;
    sum_tax += tax;
    sum_pph += price * (pph / 100);
    sum_total = sum_sub + sum_tax - sum_pph;     
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(sum_sub);       
    $("#pajak").val(formatMoney(sum_tax));
    $("#pajak_h").val(sum_tax);    
    $("#pph").val(formatMoney(sum_pph));
    $("#pph_h").val(sum_pph);        
    $("#total").val(formatMoney(sum_total));
    $("#total_h").val(sum_total);
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
        var pph_h = document.getElementById('pph_h').value;
        var total_h = document.getElementById('total_h').value;
        var create_user_h = '<?php echo $user; ?>';        
        $.ajax({
            type:'POST',
            url:'updatekbon_h.php',
            data: {'no_kbon_h':no_kbon_h, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'pph_h':pph_h, 'total_h':total_h},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                window.location = 'kontrabon.php';
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
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(3)').attr('value');
        var create_user = '<?php echo $user; ?>';
        var ceklist = document.getElementById('select').value;          
        var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
        var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
        var total = parseFloat($(this).closest('tr').find('td:eq(7)').attr('data-total'),10) ||0;
        var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
        var idtax = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').attr('data-idtax');
        var sum_sub = 0;
        var sum_tax = 0;
        var sum_total = 0;
        var sum_pph = 0;
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;        
        sum_sub += price;
        sum_tax += tax;
        sum_pph += sum_sub * (pph / 100);        
        sum_total += total - sum_pph;
        $.ajax({
            type:'POST',
            url:'updatekbon.php',
            data: {'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'jurnal':jurnal, 'no_bpb':no_bpb, 'no_po':no_po,
            'nama_supp':nama_supp, 'tgl_bpb':tgl_bpb, 'no_faktur':no_faktur, 'supp_inv':supp_inv, 'tgl_inv':tgl_inv, 'tgl_tempo':tgl_tempo,
            'curr':curr, 'ceklist':ceklist, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_tax':sum_tax, 'sum_pph':sum_pph, 'sum_total':sum_total, 'start_date':start_date, 'end_date':end_date, 'pph':pph, 'idtax':idtax},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                window.location = 'kontrabon.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
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

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalbpb').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(3)').text();
    var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(10)').attr('value');
    var top = $(this).closest('tr').find('td:eq(8)').attr('value');
    var curr = document.getElementById('matauang').value;
    var confirm = $(this).closest('tr').find('td:eq(9)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(10)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(12)').text();    

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
    $('#txt_supp2').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script>

<script type="text/javascript">
    
</script>

<!-- <script type="text/javascript">
    $('input[type=checkbox]:checked').on('change', function(){                 
    var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');

    $.ajax({
        type:'POST',
        url:'deletekbon.php',
        data: {'no_bpb':no_bpb},
        success: function(data){                
            console.log(data);                                                                            
        },
        error:  function (xhr, ajaxOptions, thrownError) {
            alert(xhr);
        }
        });
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

