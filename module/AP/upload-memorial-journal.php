<?php include '../header.php' ?>
<!-- <style >
    .modal {
  text-align: center;
  padding: 0!important;
}

.modal:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px;
}

.modal-dialog {
  display: inline-table;
  width: 700px;
  text-align: left;
  vertical-align: middle;
}
</style> -->
    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">UPLOAD MEMORIAL JOURNAL</h2>
<div class="box">
    <div class="box header">
        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $txt_periode = isset($_POST['tanggal']) ? $_POST['tanggal']: null;
            }else{
              $txt_periode =  date("Y-m-d");
            }

            $sql = mysqli_query($conn2,"select max(no_mj) from tbl_memorial_journal where MONTH(mj_date) = MONTH('$txt_periode') and YEAR(mj_date) = YEAR('$txt_periode')");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_mj)'];
            $urutan = (int) substr($kodepay, 13, 5);
            // $urutan++;
            $bln =  date("m",strtotime($txt_periode));
            $thn =  date("y",strtotime($txt_periode));
            $huruf = "GM/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);
            

            // echo '<label><i style="color: red;">Notes : Journal Number Start from '.$kodepay.'</i></label>'
             // echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            
echo '<form method="post" enctype="multipart/form-data" action="proses_upload_mj.php?kodepay='.$kodepay.' && periode='.$txt_periode.'">';
            ?>
    <div class="form-row">
        <div class="col-md-3 mb-3"> 
                <label>Choose File</label>
                <input style="height: 40px;" class="form-control" name="fileexcel" type="file" required="required">
        </div>
        <div class="col-md-2 mb-3">
            <label>Action</label>
        </br>
            <button style="padding-left: 100px;" class="btn btn-sm btn-info" type="submit">Submit</button>
            <a target="_blank" href="format-excel/format_upload.xls"><button type="button" class="btn btn-warning "><i class="fa fa-file-excel-o" aria-hidden="true"> Format Upload</i></button></a>
        </div>

    </div>
    <div class="col-md-12"> 
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $txt_periode = isset($_POST['tanggal']) ? $_POST['tanggal']: null;
            }else{
              $txt_periode =  date("Y-m-d");
            }

            $sql = mysqli_query($conn2,"select max(no_mj) from tbl_memorial_journal where MONTH(mj_date) = MONTH('$txt_periode') and YEAR(mj_date) = YEAR('$txt_periode')");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_mj)'];
            $urutan = (int) substr($kodepay, 13, 5);
            $urutan++;
            $bln =  date("m",strtotime($txt_periode));
            $thn =  date("y",strtotime($txt_periode));
            $huruf = "GM/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);
            $huruf = substr($kodepay,0,11);
            $angka = substr($kodepay,12,5) || 0;
            $angka2 = $angka + 12 ;
            $angka3 = sprintf("%05s", $angka2);

            echo '<label><i style="color: red;">Notes : Journal Number Start from '.$kodepay.'</i></label>'
             // echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            ;
            ?>
    </div>
</form>
<form id="form-data" method="post">
        <div class="form-row">
<div class="col-md-2 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Periode</b></label>            
             <!--  <select class="form-control selectpicker" name="txt_periode" id="txt_periode" data-dropup-auto="false" data-live-search="true" onchange="this.form.submit()">
                <option value="-" disabled selected="true">Select Periode</option>                                                 
                <?php
                $txt_periode ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $txt_periode = isset($_POST['txt_periode']) ? $_POST['txt_periode']: null;
                }                 
                $sql = mysqli_query($conn1,"SELECT no_bulan,nama_bulan FROM tbl_master_bulan");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['no_bulan'];
                    $data2 = $row['nama_bulan'];
                    if($row['no_bulan'] == $_POST['txt_periode']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                }?>
                </select>

                </div>
                <div class="col-md-2 mb-3" style="padding-top: 8px;">
                <label for="tanggal"><b>Kontra Bon Date <i style="color: red;">*</i></b></label>  -->         
            <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" onchange="this.form.submit()" value="<?php
            // $start_date ='';
            // $end_date ='';
            // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // $startdate = date("Y-m-d",strtotime($_POST['tanggal']));
            // $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            // $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            // }

            // $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
            // $sql = mysqli_query($conn2,"select distinct max(tgl_bpb), top from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and tgl_bpb between '$start_date' and '$end_date' ");
            // $row = mysqli_fetch_array($sql);
            // $tgl = $row['max(tgl_bpb)'];
            // $top = $row['top'];            
    
            // // $top = 30;

            // if(!empty($nama_supp)) {
            //     echo date("Y-m-d",strtotime($startdate . "+$top days"));
            // }
            // else{
            //     echo date("Y-m-d");
            // } 

            if(!empty($_POST['tanggal'])) {
                echo $_POST['tanggal'];
            }
            else{
                echo date("Y-m-d");
            }
        ?>">
            </div>
        </div>
</form>

    <div class="box body">
        <div class="row">
        
            <div class="col-md-12">
            <div class="table">
            <table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th class="text-center" style="display: none;">-</th>
            <th class="text-center" style="width: 11%">No Journal</th>
            <th class="text-center" style="width: 11%">Date</th>
            <th class="text-center" style="width: 10%">Type</th>
            <th class="text-center" style="width: 11%">COA</th>
            <th class="text-center" style="width: 11%">Cost Center</th>
            <th class="text-center" style="width: 10%">Reff</th>
            <th class="text-center" style="width: 9%">Reff Date</th>
            <th class="text-center" style="width: 10%">Buyer</th>
            <th class="text-center" style="width: 10%">WS</th>
            <th class="text-center" style="width: 6%">Curr</th>
            <th class="text-center" style="width: 9%">Debit</th>
            <th class="text-center" style="width: 9%">Credit</th>
            <th class="text-center" style="width: 10%">Remark</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $start_date ='';
            $end_date ='';
            $sub = '';
            $tax = '';
            $total = '';            

            $sql = mysqli_query($conn2,"select a.no_mj,a.mj_date,a.id_cmj,b.nama_cmj,concat(c.no_coa,' ', c.nama_coa) as coa , d.cc_name,a.no_coa,a.no_costcenter, a.no_reff, a.reff_date,a.buyer,a.no_ws,a.curr,a.rate,a.debit,a.credit,a.credit_idr,a.debit_idr,a.keterangan,a.status from tbl_memorial_journal_temp a left join master_category_mj b on b.id_cmj = a.id_cmj left join mastercoa_v2 c on c.no_coa = a.no_coa left join b_master_cc d on d.no_cc = a.no_costcenter where a.create_by = '$user'");


            while($row = mysqli_fetch_array($sql)){                    
                    echo '<tr>
                            <td style="width:10px;display: none"><input type="checkbox" id="select" name="select[]" value="" checked></td>                        
                            <td style="width:50px;" value="'.$row['no_mj'].'">'.$row['no_mj'].'</td>
                            <td style="width:100px;" value="'.$row['mj_date'].'">'.date("d-M-Y",strtotime($row['mj_date'])).'</td>
                            <td style ="style="width:100px;" data-out="'.$row['id_cmj'].'">'.$row['nama_cmj'].'</td>                                                     
                            <td style="width:50px;" value="'.$row['no_coa'].'">'.$row['coa'].'</td>                            
                            <td value="'.$row['no_costcenter'].'">'.$row['cc_name'].'</td>
                            <td value="'.$row['no_reff'].'">'.$row['no_reff'].'</td>
                            <td value="'.$row['reff_date'].'">'.date("d-M-Y",strtotime($row['reff_date'])).'</td>                                                        
                            <td value="'.$row['buyer'].'">'.$row['buyer'].'</td>
                            <td value="'.$row['no_ws'].'">'.$row['no_ws'].'</td>
                            <td value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td value="'.$row['debit'].'">'.$row['debit'].'</td>
                            <td value="'.$row['credit'].'">'.$row['credit'].'</td>
                            <td value="'.$row['keterangan'].'">'.$row['keterangan'].'</td>                 
                        </tr>';
                      }                  
                    ?>
    </tbody>
    
        
            </table>   
            </div>                 
<div class="box footer">   
        <form id="form-simpan">
            <div class="form-row col">
            <div class="col-md-4">
                </br>
      
            
            </br>
        <!--     <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 80px;"><b>Tax (11%)</b></label>
                <input type="checkbox" id="check_vat_baru" name="check_vat_baru" onclick="modal_input_vat_baru()">
            </div>
            </br> -->
             
            
            </div>
            <div class="col-md-4">

            </div>
           <div class="col-md-4">
                </br>
                <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total Credit</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="txt_credit" name="txt_credit" value="<?php             
            $sqldes = mysqli_query($conn2,"select format(sum(credit),2) as credit from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $credit = isset($row['credit']) ? $row['credit'] : 0;                  
           if($credit != '0') {
                echo $credit;
            }
            else{
                echo '';
            }?>" placeholder="0.00" readonly>
                 <input type="hidden" name="txt_credit_h" id="txt_credit_h" value="<?php             
            $sqldes = mysqli_query($conn2,"select sum(credit) as credit from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $credit = isset($row['credit']) ? $row['credit'] : 0;                  
            
                echo $credit;

             ?>">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total Debit</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="txt_debit" name="txt_debit" value="<?php             
            $sqldes = mysqli_query($conn2,"select format(sum(debit),2) as debit from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $debit = isset($row['debit']) ? $row['debit'] : 0;   
             if($debit != '0') {
                echo $debit;
            }
            else{
                echo '';
            }?>" placeholder="0.00" readonly>
                 <input type="hidden" name="txt_debit_h" id="txt_debit_h" value="<?php             
            $sqldes = mysqli_query($conn2,"select sum(debit) as debit from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $debit = isset($row['debit']) ? $row['debit'] : 0;                    
                echo $debit;
            ?>">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total Credit IDR</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="txt_credit_idr" name="txt_credit_idr" value="<?php             
            $sqldes = mysqli_query($conn2,"select format(sum(credit_idr),2) as credit_idr from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $credit_idr = isset($row['credit_idr']) ? $row['credit_idr'] : 0;                    
            if($credit_idr != '0') {
                echo $credit_idr;
            }
            else{
                echo '';
            } ?>" placeholder="0.00"  readonly>
                 <input type="hidden" name="txt_credit_idr_h" id="txt_credit_idr_h" value="<?php             
            $sqldes = mysqli_query($conn2,"select sum(credit_idr) as credit_idr from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $credit_idr = isset($row['credit_idr']) ? $row['credit_idr'] : 0;                                    
                echo $credit_idr;
             ?>">
            </div>
            </br>
            <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 180px;"><b>Total Debit IDR</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="txt_debit_idr" name="txt_debit_idr" value="<?php             
            $sqldes = mysqli_query($conn2,"select format(sum(debit_idr),2) as debit_idr from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $debit_idr = isset($row['debit_idr']) ? $row['debit_idr'] : 0;               
            if($debit_idr != '0') {
                echo $debit_idr;
            }
            else{
                echo '';
            } ?>" placeholder="0.00" readonly>
                 <input type="hidden" name="txt_debit_idr_h" id="txt_debit_idr_h" value="<?php             
            $sqldes = mysqli_query($conn2,"select sum(debit_idr) as debit_idr from tbl_memorial_journal_temp");
            $row = mysqli_fetch_array($sqldes);      
            $debit_idr = isset($row['debit_idr']) ? $row['debit_idr'] : 0;                 
                echo $debit_idr;
             ?>">
            </div>
            </br>
             
        </div>
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='memorial-journal.php'"><span class="fa fa-angle-double-left"></span> Back</button>  
            <button type="button" style="border-radius: 6px" class="btn-outline-warning btn-sm" name="reset" id="reset"><span class="fa fa-repeat"></span> Reset</button>         
            </div>
            </div>                                    
        </form>
        </div>

<div class="form-row">
    <div style="color: #2F4F4F;" class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:380px;" class="modal-dialog modal-md">
        <div style="height: 205px" class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading" style="text-align: center;"><b><i><u>Warning</u></i></b></h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <div class="col-md-1 mb-3"> 
                    </div>
                <div class="col-md-10 mb-3"> 
                <p for="nama_supp" style="text-align: center;font-size: 15px;"><b>Are you sure for Upload?</b></p> 
                <input type="hidden" name="txt_pass" id="txt_pass" value="Cobaaja123">
                <input type="hidden" name="txt_type" id="txt_type" value="">
                <input type="hidden" name="txt_id" id="txt_id" value="">
        </div>
    </div>
                <div class="form-row">
                    <div class="col-md-8">
                    </div>
                <div class="col-md-4">
                <div class="modal-footer">
                    <button type="submit" id="send2" name="send2" class="btn btn-success btn-sm" style="width: 100%;"><span class="fa fa-thumbs-up"></span>
                        Save
                    </button>
                    </div>
                    </div>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
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
  <script language="JavaScript" src="../css/4.1.1/dataTables.responsive.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/responsive.bootstrap4.min.js"></script>
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
        format: "yyyy-mm-dd",
        autoclose:true
    });
});
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
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

<script>
    $(".select2").select2({
        theme: "bootstrap",
        placeholder: "Search"
} );
</script>


<script type="text/javascript">
    
   // JavaScript Document
function addRow(tableID) {
    var tableID = "tbody2";
 var table = document.getElementById(tableID);
 var rowCount = table.rows.length;
 var row = table.insertRow(rowCount);

$(function() {
    $('.selectpicker').selectpicker();
});
$(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
$(function() {
      //Initialize Select2 Elements
      var selectcoba = rowCount;
      $('.rowCount').select2({
         theme: 'bootstrap4'
      })
      //Initialize Select2 Elements
      $('.select2add').select2({
        theme: 'bootstrap4'
      })
    });
 $coa = '';
 var element1 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td style="width: 50px"><select class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-live-search="true"> <option value="-" > - </option><?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?> <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?> </option><?php endforeach; ?></select></td><td ><select class="form-control selectpicker" name="nomor_cc" id="nomor_cc" data-live-search="true"> <option value="-" > - </option><?php $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc"); foreach ($sql2 as $cc) : ?> <option value="<?= $cc["code_combine"]; ?>"><?= $cc["cost_name"]; ?> </option><?php endforeach; ?></select></td><td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input type="text" class="form-control tanggal" name="keterangan[]" placeholder="" autocomplete="off"></td><td><select class="form-control selectpicker" name="buyer" id="buyer" data-live-search="true"> <option value="-" > - </option><?php $sql4 = mysqli_query($conn1,"select distinct(Supplier) as buyer from mastersupplier where tipe_sup = 'C' order by Supplier ASC"); foreach ($sql4 as $ms) : ?> <option value="<?= $ms["buyer"]; ?>"><?= $ms["buyer"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control selectpicker" name="no_ws" id="no_ws" data-live-search="true"> <option value="-" > - </option><?php $sql3 = mysqli_query($conn1,"select DISTINCT kpno no_ws from act_costing where cost_date >= '2022-01-01'"); foreach ($sql3 as $ws) : ?> <option value="<?= $ws["no_ws"]; ?>"><?= $ws["no_ws"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control selectpicker" name="currenc" id="currenc" onchange="ubahrate(this.value)" data-live-search="true"><option value="IDR">IDR</option><option value="USD">USD</option></select></td><td style="text-align: right; display: none"><input type="number" style="text-align: right;" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" readonly value="1"></td><td style="text-align: right;"><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt2(value)" autocomplete = "off"></td><td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';
 row.innerHTML = element1;    
    
    }
    
function deleteRow()
{
    try
         {
        var table = document.getElementById("tbody2");
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[12].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    if (rowCount <= 1)
                        {
                        alert("Tidak dapat menghapus semua baris.");
                        break;
                        }
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                    }
                }
            } catch(e)
    {
    alert(e);
    }
 }
 
 function InsertRow(tableID)
{
    try{
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[11].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
$(function() {
    $('.selectpicker').selectpicker();

});

$(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
        var element2 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td style="width: 50px"><select class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-live-search="true"> <option value="-" > - </option><?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?> <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?> </option><?php endforeach; ?></select></td><td ><select class="form-control selectpicker" name="nomor_cc" id="nomor_cc" data-live-search="true"> <option value="-" > - </option><?php $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc"); foreach ($sql2 as $cc) : ?> <option value="<?= $cc["code_combine"]; ?>"><?= $cc["cost_name"]; ?> </option><?php endforeach; ?></select></td><td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input type="text" class="form-control tanggal" name="keterangan[]" placeholder="" autocomplete="off"></td><td><select class="form-control selectpicker" name="buyer" id="buyer" data-live-search="true"> <option value="-" > - </option><?php $sql4 = mysqli_query($conn1,"select distinct(Supplier) as buyer from mastersupplier where tipe_sup = 'C' order by Supplier ASC"); foreach ($sql4 as $ms) : ?> <option value="<?= $ms["buyer"]; ?>"><?= $ms["buyer"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control selectpicker" name="no_ws" id="no_ws" data-live-search="true"> <option value="-" > - </option><?php $sql3 = mysqli_query($conn1,"select DISTINCT kpno no_ws from act_costing where cost_date >= '2022-01-01'"); foreach ($sql3 as $ws) : ?> <option value="<?= $ws["no_ws"]; ?>"><?= $ws["no_ws"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control selectpicker" name="currenc" id="currenc" onchange="ubahrate(this.value)" data-live-search="true"><option value="IDR">IDR</option><option value="USD">USD</option></select></td><td style="text-align: right; display: none"><input type="number" style="text-align: right;" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" readonly value="1"></td><td style="text-align: right;"><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt2(value)" autocomplete = "off"></td><td><input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';
        var newRow = table.insertRow(i+1);
        newRow.innerHTML = element2;
                    
                    }
                    
                }
            } catch(e)
    {
    alert(e);
    }
 }

 function hitungRow(){
    var table = document.getElementById("tbody2");
    var rowCount2 = table.rows.length;
    var tota = 0;
    var tot_price = 0;

          for(var i=0; i<rowCount2; i++){

    var price = parseFloat(document.getElementById("tbody2").rows[i].cells[6].children[0].value,10) || 0;

    tota += price;
    
    document.getElementsByName("total_value_h")[0].value = tota.toFixed(2);
    document.getElementsByName("total_value")[0].value = formatMoney(tota.toFixed(2));
}
     
}


async function hapusbaris(){
   await deleteRow()
   console.log("result");
   hitungRow();
}
</script>

 <script type="text/javascript">
      function ubahrate(curr){ 
    var rate = parseFloat(document.getElementById('rates').value,10) || 1; 
    var table = document.getElementById("tbody2");
    var val_amt = 0;
    var val_amt2 = 0;
    var total = 0;
    var total2 = 0;
    var total_h = 0;
    var total2_h = 0;
    var tota = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 0; i < (table.rows.length); i++) {

    var rates = document.getElementById("tbody2").rows[i].cells[8].children[0];
    var curren = document.getElementById("tbody2").rows[i].cells[7].children[0].value;
    var ratess = document.getElementById("tbody2").rows[i].cells[8].children[0].value || 1;
    var amt = document.getElementById("tbody2").rows[i].cells[9].children[0].value || 0;
    var amt2 = document.getElementById("tbody2").rows[i].cells[10].children[0].value || 0;
    if (curren == 'IDR') {
        rates.readOnly = true;
        rates.value = 1;
        val_amt = amt * 1;
        val_amt2 = amt2 * 1;
        total += parseFloat(val_amt);
        total2 += parseFloat(val_amt2);
        total_h += parseFloat(val_amt);
        total2_h += parseFloat(val_amt2);
    }else{
        rates.readOnly = false;
        rates.value = rate;
        val_amt = amt * rate;
        val_amt2 = amt2 * rate;
        total += parseFloat(amt);
        total2 += parseFloat(amt2);
        total_h += parseFloat(val_amt);
        total2_h += parseFloat(val_amt2);
    }
    document.getElementsByName("txt_debit")[0].value = formatMoney(total.toFixed(2));
    document.getElementsByName("txt_debit_h")[0].value = total.toFixed(2);
    document.getElementsByName("txt_debit_idr")[0].value = formatMoney(total_h.toFixed(2));
    document.getElementsByName("txt_debit_idr_h")[0].value = total_h.toFixed(2);
    document.getElementsByName("txt_credit")[0].value = formatMoney(total2.toFixed(2));
    document.getElementsByName("txt_credit_h")[0].value = total2.toFixed(2);
    document.getElementsByName("txt_credit_idr")[0].value = formatMoney(total2_h.toFixed(2));
    document.getElementsByName("txt_credit_idr_h")[0].value = total2_h.toFixed(2);
   
}
}
  </script>

<script type="text/javascript">
        function modal_input_amt(){ 
    // var val = document.getElementById('valuta').value;
    // var tot_pay = parseFloat(document.getElementById('total_cek_h').value,10) || 0; 
    // var tot_pay2 = parseFloat(document.getElementById('total_cek_idr_h').value,10) || 0;     
    var table = document.getElementById("tbody2");
    var total = 0;
    var total2 = 0;
    var val = 0;
    var val2 = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 0; i < (table.rows.length); i++) {

    var rate = document.getElementById("tbody2").rows[i].cells[8].children[0].value;
    var amt = document.getElementById("tbody2").rows[i].cells[9].children[0].value;
    var amt2 = document.getElementById("tbody2").rows[i].cells[10].children[0];
    if (amt == '') {
        val = 0;
        val2 = 0;
        amt2.readOnly = false;
    }else{
        val = amt;
        val2 = amt * rate;
        amt2.readOnly = true;
    }
    total += parseFloat(val);
    total2 += parseFloat(val2);

    // totall = tot_pay2 + tota;
    



    document.getElementsByName("txt_debit")[0].value = formatMoney(total.toFixed(2));
    document.getElementsByName("txt_debit_h")[0].value = total.toFixed(2);
    document.getElementsByName("txt_debit_idr")[0].value = formatMoney(total2.toFixed(2));
    document.getElementsByName("txt_debit_idr_h")[0].value = total2.toFixed(2);
}
}
  </script>

  <script type="text/javascript">
        function modal_input_amt2(){ 
    // var val = document.getElementById('valuta').value;
    // var tot_pay = parseFloat(document.getElementById('total_cek_h').value,10) || 0; 
    // var tot_pay2 = parseFloat(document.getElementById('total_cek_idr_h').value,10) || 0;     
    var table = document.getElementById("tbody2");
    var total = 0;
    var total2 = 0;
    var val = 0;
    var val2 = 0;
    var harga = 0;
    var totall = 0;
            for (var i = 0; i < (table.rows.length); i++) {

    var rate = document.getElementById("tbody2").rows[i].cells[8].children[0].value;
    var amt = document.getElementById("tbody2").rows[i].cells[9].children[0];
    var amt2 = document.getElementById("tbody2").rows[i].cells[10].children[0].value;
    if (amt2 == '') {
        val = 0;
        val2 = 0;
        amt.readOnly = false;
    }else{
        val = amt2;
        val2 = amt2 * rate;
        amt.readOnly = true;
    }
    total += parseFloat(val);
    total2 += parseFloat(val2);

    // totall = tot_pay2 + tota;
    



    document.getElementsByName("txt_credit")[0].value = formatMoney(total.toFixed(2));
    document.getElementsByName("txt_credit_h")[0].value = total.toFixed(2);
    document.getElementsByName("txt_credit_idr")[0].value = formatMoney(total2.toFixed(2));
    document.getElementsByName("txt_credit_idr_h")[0].value = total2.toFixed(2);
}
}
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
    

<!-- <script type="text/javascript">
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
</script> -->

<!-- -->

<script type="text/javascript">
    $("input[name=amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var amount = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;

    sum_amount += amount;
 
     
    });

    $("#nomrate1").val(formatMoney(sum_amount));    
    $("#nomrate2").val(formatMoney(sum_amount));    

    });
</script>


<!-- <script type="text/javascript"> 
<?php echo $jsArray; ?>
function changeValueACC(id){
    var select_rate = document.getElementById('rate');   
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('kode').value = prdName[id].kode;
    if (prdName[id].valuta == 'IDR') {
            select_rate.disabled = true;
        }else{
            select_rate.disabled = false;
        }
};
</script>
 -->
<!-- <script type="text/javascript">
    $("input[name=rate]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = rate;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#rate_h").val(formatMoney(rat));

    });
</script> -->

<script type="text/javascript">
    $("input[name=nominal_h]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = ttl_h;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#nominal").val(formatMoney(rat));

    });
</script>

<script type="text/javascript">
    $("#modal-form3").on("click", "#send3", function(){
        var valu = '';
        $("input[type=radio]:checked").each(function () {
        var data = $(this).closest('tr').find('td:eq(1) input').val();
        valu = data;
        console.log(data);
         
             
                  
        });
        $("#txt_forpay").val(valu);
 
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
    $("#simpan").on("click", function(){ 
    var txt_credit_h = document.getElementById('txt_credit_h').value; 
    var txt_debit_h = document.getElementById('txt_debit_h').value;
    if (txt_debit_h == txt_credit_h) {

    // $('#mymodal2').modal('show');
}else{
    alert("Debit and Credit can't balance")
}         

});

</script>

 <script type="text/javascript">
    $("#reset").on("click", function(){ 
        var create_user = '<?php echo $user; ?>';   
        $.ajax({
            type:'POST',
            url:'reset_upload_mj.php',
            data: {'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                window.location.reload(true);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 
    });

</script>

 <script type="text/javascript">
      $("#simpan").on("click", function(){ 
        // var pass = document.getElementById('pass').value;
        // var txt_pass = document.getElementById('txt_pass').value;
        // var status = document.getElementById('txt_type').value;
        // var id_cf = document.getElementById('txt_id').value;
        var txt_credit_h = document.getElementById('txt_credit_h').value; 
        var txt_debit_h = document.getElementById('txt_debit_h').value;
        var create_user = '<?php echo $user; ?>';   
    if (txt_debit_h == txt_credit_h) {
        $.ajax({
            type:'POST',
            url:'insert_upload_mj.php',
            data: {'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                // $('#tbl_cf').html(response);
                // console.log(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 alert("Data saved successfully");
                window.location = 'memorial-journal.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        }); 
    }
        
         
        // if(document.getElementById('pass').value == document.getElementById('txt_pass').value){
        //     alert("Data changed successfully");
        //     return false;   
        // }else{
        //     alert("Incorrect Password");
        // return false;           
        // }

 
    });


</script>

<!-- <script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;
        var unik_code = document.getElementById('unik_code').value;        
        var data = $(this).closest('tr').find('td:eq(1) input').val();
         
             
        $.ajax({
            type:'POST',
            url:'insertdoc.php',
            data: {'doc_number':doc_number, 'unik_code':unik_code, 'data':data},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');
                // $('#modal-form2').modal('hide');
                 // alert("Data saved successfully");
                window.location.reload(false);
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
                // return false; 
 
    });


</script> -->

<!-- <script type="text/javascript">
    $("#form-data").on("click", "#btn2", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
         
             
        $.ajax({
            type:'POST',
            url:'hapusdoc.php',
            data: {'doc_number':doc_number},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
    });


</script> -->


<!-- 
<script type="text/javascript">
    $('#currenc').change(function() {
        var angka = 0; 
        var curr = $(this).val(); 
        var rates = document.getElementById('rates').value;
        if (curr == 'IDR') {
            angka = 1;
        }else{
            angka = rates;
        }
        
        $('#txt_rate').val(angka);
        $('#txt_rate').prop('readonly', false);
    });
 
</script> -->

<!-- <script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        $("input[type=checkbox]:checked").each(function () {
        var no_mj= document.getElementById('no_doc').value;  
        var mj_date = document.getElementById('tgl_doc').value; 
        var id_cmj = $('select[name=nama_type] option').filter(':selected').val();       ;      
        var no_coa = $(this).closest('tr').find('td:eq(1)').find('select[name=nomor_coa] option').filter(':selected').val(); 
        var no_costcenter = $(this).closest('tr').find('td:eq(2)').find('select[name=nomor_cc] option').filter(':selected').val(); 
        var no_reff = $(this).closest('tr').find('td:eq(3) input').val();
        var reff_date = $(this).closest('tr').find('td:eq(4) input').val();                           
        var buyer = $(this).closest('tr').find('td:eq(5)').find('select[name=buyer] option').filter(':selected').val();           
        var no_ws = $(this).closest('tr').find('td:eq(6)').find('select[name=no_ws] option').filter(':selected').val();                            
        var curr = $(this).closest('tr').find('td:eq(7)').find('select[name=currenc] option').filter(':selected').val();      
        var rate =$(this).closest('tr').find('td:eq(8) input').val(); 
        var debit = $(this).closest('tr').find('td:eq(9) input').val() || 0;                               
        var credit = $(this).closest('tr').find('td:eq(10) input').val() || 0;
        var keterangan = $(this).closest('tr').find('td:eq(11) input').val();
        var create_user = '<?php echo $user; ?>';

        if (id_cmj != '') {
        $.ajax({
            type:'POST',
            url:'insert_memorial_journal.php',
            data: {'no_mj':no_mj, 'mj_date':mj_date, 'id_cmj':id_cmj, 'no_coa':no_coa, 'no_costcenter':no_costcenter, 'no_reff':no_reff,'reff_date':reff_date, 'buyer':buyer, 'no_ws':no_ws, 'curr':curr, 'rate':rate, 'debit':debit, 'credit':credit, 'keterangan':keterangan, 'create_user':create_user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // alert(response);
                window.location = 'memorial-journal.php';
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });
        } 
                         
    });
       if($('select[name=nama_type] option').filter(':selected').val() == '' ){
        alert("Please Select Type Journal");
        }else if(document.getElementById('txt_credit_h').value == '' && document.getElementById('txt_debit_h').value == '' || document.getElementById('txt_credit_h').value == '0' && document.getElementById('txt_debit_h').value == '0'){
        alert("Please Enter Amount");
        }else{               
       
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
    $("#form-simpan").on("click", "#batal", function(){
        $("input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;        
         
             
        $.ajax({
            type:'POST',
            url:'hapusdoc.php',
            data: {'doc_number':doc_number},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                alert(xhr);
            }
        });             
        });
 
    });


</script> -->

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
