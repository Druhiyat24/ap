<?php include '../header.php' ?>
<style >
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
</style>
    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">OTHER PAYABLE REPORT</h2>
<div class="box">
    <div class="box header">

        <form id="form-data" action="other_payable_report.php" method="post">        
        <div class="form-row">

            <!-- <div class="col-md-4">
            <label for="nama_type"><b>No COA</b></label>            
              <select style="background-color: gray;" class="form-control selectpicker" name="coa_number" id="coa_number" data-dropup-auto="false" data-live-search="true" required>
                 <option value="-" disabled selected="true">Select coa</option> 
                <?php
                $coa_number ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                }                 
                $sql = mysql_query("select DISTINCT no_coa, nama_coa, CONCAT(no_coa,' - ',nama_coa) as coa from mastercoa_v2",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['coa'];
                    $id_ctg2 = $row['no_coa'];
                    if($row['no_coa'] == $_POST['coa_number']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_ctg2.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>  -->

            <div class="col-md-2 mb-3"> 
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
            placeholder="Tanggal Awal">
            </div>

            <div class="col-md-2 mb-3"> 
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
            placeholder="Tanggal Awal">
            </div>
            <div class="input-group-append col">                                   
            <button  type="submit" id="submit" value=" Search " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
<!--             <button type="button" id="reset" value=" Reset " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button> -->

<?php
        // $status = isset($_POST['status']) ? $_POST['status']: null;
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        $tanggal_awal = date("Y-m-d",strtotime($start_date));
        $tanggal_akhir = date("Y-m-d",strtotime($end_date)); 
        $tanggal1 = isset($tanggal_awal) ? $tanggal_awal : 0;
        $tanggal2 = isset($tanggal_akhir) ? $tanggal_akhir : 0;
        $kata_awal = date("M",strtotime($start_date));
        $tengah = '_';
        $kata_akhir = date("Y",strtotime($start_date));
        $kata_filter = $kata_awal . $tengah . $kata_akhir;


        echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_op_report.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

        ';
        //<a style="padding-right: 5px;" target="_blank" href="ekspor_sfp_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SFP</i></button></a>

        // <a style="padding-right: 5px;" target="_blank" href="ekspor_spl_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SPL</i></button></a>

        //     <a style="padding-left: 10px";><button type="button" class="btn btn-info " name="co_sal" id="co_sal" style= "margin-top: 30px;"><i class="fa fa-clipboard" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Copy Saldo</i></button></a>

        
        ?>  

            </div>                                                            
    </div>
<br/>
</div>
</form> 

    <div class="box body">
        <div class="row">       
            <div class="col-md-12">      
   <div class="tableFix" style="height: 450px;">        
<table id="mytable2" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
        <thead>
        <tr class="thead-dark">
            <th style="text-align: center;vertical-align: middle;width: 10%;">No Memo</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Memo Date</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">No Invoice Vendor</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Begining Balance</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Addition</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Deduction (BK)</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Deduction (GM)</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Inc Tax Art 23</th>
            <th style="text-align: center;vertical-align: middle;width: 10%;">Ending Balance</th>
        </tr>
    </thead>
        </tbody>

    <?php
    $coa_number ='';
    $start_date ='';
    $end_date =''; 
    $date_now = date("Y-m-d");  
    $tanggal_awal = date("Y-m-d",strtotime($date_now ));
    $tanggal_akhir = date("Y-m-d",strtotime($date_now ));
    $bulan_awal = date("m",strtotime($date_now));
    $bulan_akhir = date("m",strtotime($date_now));  
    $tahun_awal = date("Y",strtotime($date_now));
    $tahun_akhir = date("Y",strtotime($date_now));
    $kata_awal = date("M",strtotime($date_now));
    $tengah = '_';
    $kata_akhir = date("Y",strtotime($date_now));
    $kata_filter = $kata_awal . $tengah . $kata_akhir;
    $kata_filter2 = $kata_awal . $tengah . $kata_akhir;          
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));   
    $tanggal_awal = date("Y-m-d",strtotime($_POST['start_date']));
    $tanggal_akhir = date("Y-m-d",strtotime($_POST['end_date'])); 

    $bulan_awal = date("m",strtotime($_POST['start_date']));
    $bulan_akhir = date("m",strtotime($_POST['end_date']));  
    $tahun_awal = date("Y",strtotime($_POST['start_date']));
    $tahun_akhir = date("Y",strtotime($_POST['end_date']));

    $kata_awal = date("M",strtotime($_POST['start_date']));
    $tengah = '_';
    $kata_akhir = date("Y",strtotime($_POST['start_date']));
    $kata_filter = $kata_awal . $tengah . $kata_akhir;         
    }

 $sql = mysqli_query($conn2,"SELECT nm_memo,tgl_memo,no_invoice,supplier,buyer,IF(tgl_memo < '$start_date',total_memo,0) beg_balance, IF(tgl_memo >= '$start_date',total_memo,0) addition,total_bk deduction, total_bk2 deduction2 FROM (SELECT a.nm_memo,tgl_memo,no_invoice,supplier,buyer,total_memo,COALESCE(ttl_amount,0) total_bk, COALESCE(ttl_amount2,0) total_bk2 FROM (
SELECT nm_memo, tgl_memo,no_invoice,supplier,buyer,total_memo from (SELECT b.id,a.id_supplier,d.supplier,g.supplier buyer,a.nm_memo, a.tgl_memo,IF(b.inv_vendor is null,'-',b.inv_vendor) no_invoice,a.curr,sum(b.biaya) total_memo,'' id_book from memo_h a inner join mastersupplier d on d.id_supplier =a.id_supplier inner join mastersupplier g on g.id_supplier =a.id_buyer inner join memo_det b on b.id_h = a.id_h where  a.nm_memo >= 'MEMO/NAG/2310/01039' and a.tgl_memo BETWEEN '2023-01-01' and '$end_date' and b.cancel = 'N' and a.status NOT IN ('DRAFT','CANCEL') GROUP BY a.nm_memo order by a.nm_memo asc ) a) a left join

(select nm_memo,bankout_date,SUM(amount) amount,SUM(ttl_ppn) ttl_ppn, SUM(ttl_pph) ttl_pph, SUM(ttl_amount) ttl_amount, SUM(inc_tax) inc_tax from (select reff_doc nm_memo,bankout_date,amount,ttl_ppn,ttl_pph,(amount + ttl_ppn - ttl_pph)ttl_amount,(ttl_ppn - ttl_pph) inc_tax from (select a.reff_doc,d.bankout_date, a.amount,a.ppn,round( a.amount * a.ppn /100,2) ttl_ppn,a.pph, round( a.amount * a.pph /100,2) ttl_pph from tbl_pv a inner join b_bankout_det c on c.no_reff = a.no_pv inner join b_bankout_h d on d.no_bankout = c.no_bankout where d.status != 'CANCEL' and a.reff_doc like '%MEMO/%') a) a where bankout_date BETWEEN '$start_date' and '$end_date' GROUP BY nm_memo) b on b.nm_memo = a.nm_memo left join

(select nm_memo_bfr,bankout_date,amount2,ttl_ppn2,ttl_pph2,ttl_amount2,inc_tax2 from (select nm_memo nm_memo_bfr,bankout_date,SUM(amount) amount2,SUM(ttl_ppn) ttl_ppn2, SUM(ttl_pph) ttl_pph2, SUM(ttl_amount) ttl_amount2, SUM(inc_tax) inc_tax2 from (select reff_doc nm_memo,bankout_date,amount,ttl_ppn,ttl_pph,(amount + ttl_ppn - ttl_pph)ttl_amount,(ttl_ppn - ttl_pph) inc_tax from (select a.reff_doc,d.bankout_date, a.amount,a.ppn,round( a.amount * a.ppn /100,2) ttl_ppn,a.pph, round( a.amount * a.pph /100,2) ttl_pph from tbl_pv a inner join b_bankout_det c on c.no_reff = a.no_pv inner join b_bankout_h d on d.no_bankout = c.no_bankout where d.status != 'CANCEL' and a.reff_doc like '%MEMO/%') a) a where bankout_date < '$start_date' GROUP BY nm_memo union
select reff_doc,tgl_journal,sum((debit * rate) - (credit * rate)) total, '0','0',((debit * rate) - (credit * rate)) total2, '0' from tbl_list_journal where no_coa = '2.18.02' and no_journal like '%GM/%' and tgl_journal < '$start_date') a) c on c.nm_memo_bfr = a.nm_memo) a");

        $ttl_beg =0;
        $ttl_add =0;
        $ttl_ded =0;
        $ttl_for =0;
        $ttl_end =0;
        $ttl_mj =0;
    while($row2 = mysqli_fetch_array($sql)){
        $nm_memo = $row2['nm_memo'];
        $sqlmm = mysqli_query($conn1,"SELECT no_journal from tbl_list_journal where no_journal = '$nm_memo'");
        $rowmm = mysqli_fetch_array($sqlmm);
        $no_journal = isset($rowmm['no_journal']) ? $rowmm['no_journal'] : null;
        if ($no_journal != null) {
        $sql_mj_b = mysqli_query($conn2,"select reff_doc,tgl_journal,sum((debit * rate) - (credit * rate)) total, '0','0',((debit * rate) - (credit * rate)) total2, '0' from tbl_list_journal where no_coa = '2.18.02' and no_journal like '%GM/%' and tgl_journal < '$start_date' and reff_doc = '$nm_memo'");
        $row_mj_b = mysqli_fetch_array($sql_mj_b);
        $val_mj_b = isset($row_mj_b['total']) ? $row_mj_b['total'] : 0;
        // echo $val_mj_b;

        if ($val_mj_b > 0) {
             $beg_balance = $row2['beg_balance'] - $val_mj_b;
        }else{
             $beg_balance = $row2['beg_balance'];
        }

        // $beg_balance = $row2['beg_balance'];
        $addition = $row2['addition'];
        $deduction = $row2['deduction'];
        $deduction2 = $row2['deduction2'];

        $sql_mj = mysqli_query($conn2,"select reff_doc,tgl_journal,sum((debit * rate) - (credit * rate)) total, '0','0',((debit * rate) - (credit * rate)) total2, '0' from tbl_list_journal where no_coa = '2.18.02' and no_journal like '%GM/%' and tgl_journal BETWEEN '$start_date' and '$end_date' and reff_doc = '$nm_memo'");
        $row_mj = mysqli_fetch_array($sql_mj);
        $val_mj = isset($row_mj['total']) ? $row_mj['total'] : 0;
        
        if ($deduction > 0) {
        if ($beg_balance > 0) {
            $forex = $beg_balance - $deduction - $val_mj;
        }else{
            $forex = $addition - $deduction - $val_mj;
        }
        }else{
            $forex = 0;
        }
        $end_balance = $beg_balance + $addition - $deduction - $val_mj - $forex; 
        if ($deduction2 > 0) {
        }else{
        $ttl_beg +=$beg_balance;
        $ttl_add +=$addition;
        $ttl_ded +=$deduction;
        $ttl_for +=$forex;
        $ttl_end +=$end_balance;
        $ttl_mj  +=$val_mj;
        if ($beg_balance == 0 && $addition == 0) {
            // code...
        }else{

        echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$row2['nm_memo'].'">'.$row2['nm_memo'].'</td>
            <td style="width: 100px;" value = "'.$row2['tgl_memo'].'">'.date("d-M-Y",strtotime($row2['tgl_memo'])).'</td>
            <td style="text-align : left;" value = "'.$row2['no_invoice'].'">'.$row2['no_invoice'].'</td>
            <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
            <td style="text-align : right;" value = "'.$beg_balance.'">'.number_format($beg_balance,2).'</td>
            <td style="text-align : right;" value = "'.$row2['addition'].'">'.number_format($row2['addition'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['deduction'].'">'.number_format($row2['deduction'],2).'</td>
            <td style="text-align : right;" value = "'.$val_mj.'">'.number_format($val_mj,2).'</td>
            <td style="text-align : right;" value = "'.$forex.'">'.number_format($forex,2).'</td>
            <td style="text-align : right;" value = "'.$end_balance.'">'.number_format($end_balance,2).'</td>
            </tr>
            ';
        }
    }
        

        }else{
        }
}

        echo ' <tr >
            <th colspan="4" style="text-align : center;" value = "Total">Total</td>
            <th style="text-align : right;" value = "'.$ttl_beg.'">'.number_format($ttl_beg,2).'</th>
            <th style="text-align : right;" value = "'.$ttl_add.'">'.number_format($ttl_add,2).'</th>
            <th style="text-align : right;" value = "'.$ttl_ded.'">'.number_format($ttl_ded,2).'</th>
            <th style="text-align : right;" value = "'.$ttl_mj.'">'.number_format($ttl_mj,2).'</th>
            <th style="text-align : right;" value = "'.$ttl_for.'">'.number_format($ttl_for,2).'</th>
            <th style="text-align : right;" value = "'.$ttl_end.'">'.number_format($ttl_end,2).'</th>
            </tr>
            ';
?>  
</tbody>
</table>
</div>                  
</div>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:450px;" class="modal-dialog modal-md">
        <div style="height: 225px" class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading" style="text-align: center;"><b>UPLOAD</b></h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form method="post" enctype="multipart/form-data" action="proses_upload.php">
                                    Pilih File:
                                    <input class="form-control" name="fileexcel" type="file" required="required">
                                    <br>
                                    <button class="btn btn-sm btn-info" type="submit">Submit</button>
                                    <a target="_blank" href="format_upload_mj.xls"><button type="button" class="btn btn-warning "><i class="fa fa-file-excel-o" aria-hidden="true"> Format Upload</i></button></a>
                                </form>
        </div>
      </div>
    </div>
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
<!--           <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
  <!--         <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                    
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content 
  </div>
      /.modal-dialog 
    </div> -->         
                                
</div><!-- body-row END -->
</div>
</div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
    <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min.js"></script>
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
<!-- <script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

<script>
    $(document).ready(function() {
    $('#mytable').dataTable({
    'order': [1, 'asc'],
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": true,
    "bInfo": false,
    "bAutoWidth": false
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
  table = document.getElementById("datatable");
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
        startDate : "01-01-2023",
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
    $("#form-data").on("click", "#co_sal", function(){ 
        var no_coa = $(this).closest('tr').find('td:eq(1)').attr('value');
        var beg_balance = $(this).closest('tr').find('td:eq(7)').attr('value');
        var debit = $(this).closest('tr').find('td:eq(8)').attr('value');
        var credit = $(this).closest('tr').find('td:eq(9)').attr('value');
        var end_balance = $(this).closest('tr').find('td:eq(10)').attr('value');
        var copy_user = '<?php echo $user ?>';
        var to_saldo = document.getElementById('to_saldo').value;

        $.ajax({
            type:'POST',
            url:'copy_saldo_tb.php',
            data: {'no_coa':no_coa, 'beg_balance':beg_balance,'debit':debit, 'credit':credit,'end_balance':end_balance, 'copy_user':copy_user,'to_saldo':to_saldo},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                // alert(response);            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        alert("Copy Saldo successfully");     
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#active", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'activebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Active");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#deactive", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'deactivebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Deactive");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>


<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
    var date = $(this).closest('tr').find('td:eq(1)').text();
    var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
    var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
    var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
    var curr = "IDR";

    $.ajax({
    type : 'post',
    url : 'ajax_cashin.php',
    data : {'no_ib': no_ib},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_ib);
    $('#txt_tglbpb').html('Date : ' + date + '');
    $('#txt_no_po').html('Refference : ' + reff + '');
    $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "create-list-journal.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnupload').onclick = function () {
    location.href = "upload-list-journal.php";
};
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "list-journal.php";
};
</script>

<!-- <script type="text/javascript">     
    document.getElementById('btnupload').onclick = function (){ 
    // var txt_type = $(this).closest('tr').find('td:eq(4)').attr('value'); 
    // var txt_id = $(this).closest('tr').find('td:eq(0)').attr('value');           
    $('#mymodal2').modal('show');
    // $('#txt_type').val(txt_type);
    // $('#txt_id').val(txt_id);

};

</script> -->

<script>
function alert_cancel() {
  alert("Master Bank Deactive");
  location.reload();
}
function alert_approve() {
  alert("Master Bank Active");
  location.reload();
}
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>