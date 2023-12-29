<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">BPB NOT CONFIRMED FROM SB1</h2>
<div class="box">
    <div class="box header">
<form id="form-data" action="status-confirm-bpb.php" method="post">        
        <div class="form-row">
            <div class="col-md-5">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
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

                 <div class="form-row">
            <div class="col-md-6"> 
            <label for="start_date"><b>From</b></label>
            <input type="text" class="form-control tanggal" id="start_date" name="start_date" 
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
            placeholder="Start Date" autocomplete='off' >
            </div>

            <div class="col-md-6 mb-1">
            <label for="end_date"><b>To</b></label>        
            <input type="text" class="form-control tanggal" id="end_date" name="end_date" 
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
            placeholder="Tanggal Akhir" >
            </div>
        </div>

            <div class="input-group-append col">                                   
           <button type="submit" id="submit" value=" Search " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="reset" value=" Reset " style="margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button> 

    <?php
        
            // echo '<a target="_blank" href="ekspor_status-confirm-bpb.php?nama_supp='.$nama_supp.' && filter='.$filterr.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        
        ?>          
            </div>                                                             
    </div>
</br>
</div>
</form>
</form>

            
<table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%">
    <thead>
        <tr class="thead-dark">
            <th style="text-align: center; vertical-align: middle;">No BPB</th>
            <th style="text-align: center; vertical-align: middle;">BPB Date</th>
            <th style="text-align: center; vertical-align: middle;">No PO</th>
            <th style="text-align: center; vertical-align: middle;">From BPB</th>
            <th style="text-align: center; vertical-align: middle;">Supplier</th>
            <th style="text-align: center; vertical-align: middle;">TOP</th>
            <th style="text-align: center; vertical-align: middle;">Payment Terms</th> 
            <th style="width:100px;display: none;">Currency</th>
            <th style="width:100px;display: none;">Confirm</th>
            <th style="width:100px;display: none;">Tgl PO</th>                                           
        </tr>
    </thead>
    
<tbody>
    <?php
    $nama_supp ='';
    $status = '';
    $filter = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $status = isset($_POST['status']) ? $_POST['status']: null;
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
    }


    if($nama_supp == 'ALL'){
            $sql = mysql_query("select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, masterpterms.kode_pterms, bpb.curr, bpb.confirm_by, po_header.podate, po_header_draft.tipe_com
                from bpb 
                INNER JOIN po_header on po_header.pono = bpb.pono 
                left JOIN po_header_draft on po_header_draft.id = po_header.id_draft
                INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier 
                inner join masterpterms on masterpterms.id = po_header.id_terms 
                where bpb.confirm !='Y' and bpb.is_cancel='N' and bpb.bpbdate between '$start_date' and '$end_date'",$conn1);                
            }else {
            $sql = mysql_query("select bpb.id ,bpb.bpbno_int, bpb.pono, bpb.bpbdate, mastersupplier.Supplier, po_header.jml_pterms, masterpterms.kode_pterms, bpb.curr, bpb.confirm_by, po_header.podate, po_header_draft.tipe_com
                from bpb 
                INNER JOIN po_header on po_header.pono = bpb.pono 
                left JOIN po_header_draft on po_header_draft.id = po_header.id_draft
                INNER JOIN mastersupplier on mastersupplier.Id_Supplier = bpb.id_supplier 
                inner join masterpterms on masterpterms.id = po_header.id_terms 
                where bpb.confirm !='Y' and bpb.is_cancel='N' and Supplier='$nama_supp' and bpb.bpbdate between '$start_date' and '$end_date'",$conn1);
            }


    if (!empty($nama_supp && $start_date && $end_date)) {                                              
                while($row = mysql_fetch_array($sql)){
                    $bpb = $row['bpbno_int'];
                    $querys = mysql_query("select distinct (no_bpb) from bpb_new where no_bpb = '$bpb' and status != 'Cancel'",$conn2);
                    $rows = mysql_fetch_array($querys);
                    $n_bpb = isset($rows['no_bpb']);

                    $querys12 = mysql_query("select DISTINCT bppbno from bpb where bpbno_int = '$bpb'",$conn1);
                    $rows12 = mysql_fetch_array($querys12);
                    $bppbno1 = isset($rows12['bppbno']) ? $rows12['bppbno'] : null;
                    
                    $querys23 = mysql_query("select DISTINCT bppbno_int, bpbno_ro from bppb where bppbno = '$bppbno1'",$conn1);
                    $rows23 = mysql_fetch_array($querys23);
                    $bpbno_ro2 = isset($rows23['bpbno_ro']) ? $rows23['bpbno_ro'] : null;

                    $querys34 = mysql_query("select DISTINCT bpbno_int from bpb where bpbno = '$bpbno_ro2'",$conn1);
                    $rows34 = mysql_fetch_array($querys34);
                    $bpbno_int3 = isset($rows34['bpbno_int']) ? $rows34['bpbno_int'] : null;
                    if ($bpbno_int3 != '') {
                        $bpbno_int4 = $bpbno_int3;
                    }else{
                        $bpbno_int4 = '-';
                    }
            if($bpb == $n_bpb) {
            echo '';
            }else{                              
                    echo'<tr>
                            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                            <td style="width:50px;" value="'.$row['bpbno_int'].'">'.$row['bpbno_int'].'</td>
                            <td style="width:100px;" value="'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
                            <td style="width:100px;" value="'.$row['pono'].'">'.$row['pono'].'</td>
                            <td style="width:100px;" value="'.$bpbno_int4.'">'.$bpbno_int4.'</td>                                                                                                          
                            <td style="width:50px;" value="'.$row['Supplier'].'">'.$row['Supplier'].'</td>
                            <td style="width:50px;" value="'.$row['jml_pterms'].'">'.$row['jml_pterms'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['confirm_by'].'">'.$row['confirm_by'].'</td>
                            <td style="width:50px;display: none;" value="'.$row['podate'].'">'.date("d-M-Y",strtotime($row['podate'])).'</td>
                            <td style="width:50px;" value="'.$row['kode_pterms'].'">'.$row['kode_pterms'].'</td>                                                                                    
                        </tr>';
                    }
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

    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>
  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
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
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        startDate : "01-01-2022",
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
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
</script>
<!-- 
<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(6)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(11)').text();        

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
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
    location.href = "status-confirm-bpb.php";
};
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
    location.href = "status-confirm-bpb.php";
};
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
