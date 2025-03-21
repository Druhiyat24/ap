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
    <h2 class="text-center">FABRIC TRANSACTION OUT</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="ca_fabric_trx_out.php" method="post">        
                <div class="form-row">

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

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_ca_fabric_trx_out.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

    ';

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
            <table id="mytable2" class="table table-striped table-bordered text-nowrap" role="grid" cellspacing="0" width="100%">
                <thead>
                    <tr class="thead-dark">
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">No Trans</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Tgl Trans</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Id Item</th>
                        <th hidden rowspan="2" style="text-align: center;vertical-align: middle;">Kode barang</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Nama Barang</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Warna</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Ukuran</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">No Inv</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Jenis Dok</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Nomor Aju</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Tgl Aju</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Nomor Daftar</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Tgl Daftar</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Supplier</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Jumlah</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Satuan</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Berat Bersih</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Keterangan</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Nama User</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;;">WS</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Style</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Curr</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Price</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Status</th>
                        <th colspan="2" style="text-align: center;vertical-align: middle;">Nilai Barang / Unit Dalam Mata Uang Asal</th>
                        <th colspan="2" style="text-align: center;vertical-align: middle;">Nilai Barang Dalam Mata Uang Asal</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Rate</th>
                        <th colspan="3" style="text-align: center;vertical-align: middle;">Nilai Barang Ekuivalen IDR</th>
                    </tr>
                    <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;">Non Return</th>
                        <th style="text-align: center;vertical-align: middle;">Return</th>
                        <th style="text-align: center;vertical-align: middle;">Non Return</th>
                        <th style="text-align: center;vertical-align: middle;">Return</th>
                        <th style="text-align: center;vertical-align: middle;">Non Return</th>
                        <th style="text-align: center;vertical-align: middle;">Return</th>
                        <th style="text-align: center;vertical-align: middle;">Total</th>
                    </tr>
                </thead>
            </tbody>

            <?php
            $nama_buyer ='';
            $start_date ='';
            $end_date =''; 
            $date_now = date("Y-m-d");  
            $tanggal_awal = date("Y-m-d",strtotime($date_now ));
            $tanggal_akhir = date("Y-m-d",strtotime($date_now ));      
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                $end_date = date("Y-m-d",strtotime($_POST['end_date']));   

            }

            $sql = mysqli_query($conn2,"select bppbno, bppbdate, a.id_item, goods_code, itemdesc, color, size, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, tujuan, qty, unit, berat_bersih, remark, username, ws, styleno, 'IDR' curr, price, status,price_non_ro, price_ro, '1' rate, round(qty * price_non_ro,2) total_non_ro, round(qty * price_ro,2) total_ro, IF(curr = 'IDR',round(qty * price_ro,2),round((qty * price_ro),2)) total_ro_idr, IF(curr = 'IDR',round(qty * price_non_ro,2),round((qty * price_non_ro),2)) total_non_ro_idr from (select bppbno, bppbdate, a.id_item, goods_code, itemdesc, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, supplier tujuan, color, size, qty, unit, berat_bersih, remark, username, ws, styleno, if(a.curr = '' OR a.curr is null,'IDR',a.curr) curr, if(bppbno like '%RO/%',b.price,a.price) price, jenis_pengeluaran status,price_non_ro, if(b.price is null,0,b.price) price_ro, IF(rate is null,1,rate) rate from (select a.no_bppb bppbno,a.no_req bppbno_req,a.tgl_bppb bppbdate,no_invoice invno,a.dok_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju tanggal_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.tujuan supplier,b.id_item,goods_code,concat(itemdesc,' ',add_info) itemdesc,s.color,s.size, sum(b.qty_out) qty,0 as qty_good,0 as qty_reject, b.satuan unit,'' berat_bersih,a.catatan remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,ac.kpno ws,ac.styleno,b.curr,b.price,br.idws_act,'' jenis_trans,IF(a.jenis_pengeluaran is null,'-',a.jenis_pengeluaran) jenis_pengeluaran, (coalesce(price_in,0) + COALESCE(nilai_barang,0)) price_non_ro, b.id_jo, b.id_roll
            from whs_bppb_h a 
            inner join whs_bppb_det b on b.no_bppb = a.no_bppb
            inner join masteritem s on b.id_item=s.id_item 
            left join (select id_jo,id_so from jo_det group by id_jo ) tmpjod on tmpjod.id_jo=b.id_jo 
            left join (select bppbno as no_req,idws_act from bppb_req group by no_req) br on a.no_req = br.no_req 
            left join so on tmpjod.id_so=so.id 
            left join act_costing ac on so.id_cost=ac.id 
            where LEFT(a.no_bppb,2) = 'GK' and b.status != 'N' and a.status != 'cancel' and a.tgl_bppb BETWEEN  '$start_date' and '$end_date' and matclass= 'FABRIC' GROUP BY b.id_jo,b.id_item,b.satuan,b.no_bppb,(coalesce(price_in,0) + COALESCE(nilai_barang,0)) order by a.no_bppb) a LEFT JOIN (select a.no_bppb, id_jo, id_item, price from whs_bppb_ro a INNER JOIN whs_bppb_h b on b.no_bppb = a.no_bppb where b.status != 'cancel' and b.tgl_bppb BETWEEN  '$start_date' and '$end_date') b on b.no_bppb = a.bppbno and b.id_item = a.id_item and b.id_jo = a.id_jo LEFT JOIN (select * from (select tgl_dok, no_barcode from whs_lokasi_inmaterial a inner join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode
            UNION
            select tgl_bpb, no_barcode from whs_sa_fabric GROUP BY no_barcode
                        UNION
                        select tgl_dok,b.no_barcode from whs_lokasi_inmaterial a INNER JOIN (select no_barcode, no_barcode_old from whs_lokasi_inmaterial where no_barcode_old is not null) b on b.no_barcode_old = a.no_barcode inner join whs_inmaterial_fabric c on c.no_dok = a.no_dok GROUP BY b.no_barcode) a GROUP BY no_barcode) c on c.no_barcode = a.id_roll
            left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bppbdate and cr.curr = a.curr) a");
            

            $total_qty =0;
            $total_price_non_ro =0;
            $total_price_ro =0;
            $total_total_non_ro =0;
            $total_total_ro =0;
            $total_total_non_ro_idr =0;
            $total_total_ro_idr =0;
            $total_total_ro_idr =0;
            $sum_total_ro_nonro_idr =0;
            while($row2 = mysqli_fetch_array($sql)){
                $no_aju = $row2['no_aju'];
                $bcno = $row2['bcno'];
                $neto = isset($row2['berat_bersih']) ? $row2['berat_bersih'] : 0;

                if ($no_aju == '' || $no_aju == null) {
                    $tgl_aju = '-'; 
                }else{
                    $tgl_aju = date("Y-m-d",strtotime($row2['tanggal_aju']));
                }
                if ($bcno == '' || $bcno == null) {
                    $bcdate = '-'; 
                }else{
                    $bcdate = date("Y-m-d",strtotime($row2['bcdate']));
                }
                
                $total_ro_nonro = $row2['total_non_ro_idr'] + $row2['total_ro_idr'];
                $sum_total_ro_nonro_idr += $total_ro_nonro;

                $total_qty += $row2['qty'];
                // $total_neto += $neto;
                $total_price_non_ro += $row2['price_non_ro'];
                $total_price_ro += $row2['price_ro'];
                $total_total_non_ro += $row2['total_non_ro'];
                $total_total_ro += $row2['total_ro'];
                $total_total_non_ro_idr += $row2['total_non_ro_idr'];
                $total_total_ro_idr += $row2['total_ro_idr'];

                echo ' <tr style="font-size:12px;text-align:center;">
                <td style="text-align : left;" value = "'.$row2['bppbno'].'">'.$row2['bppbno'].'</td>
                <td style="width: 100px;" value = "'.$row2['bppbdate'].'">'.date("d-M-Y",strtotime($row2['bppbdate'])).'</td>
                <td style="text-align : left;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
                <td style="text-align : left;display:none;" value = "'.$row2['goods_code'].'">'.$row2['goods_code'].'</td>
                <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
                <td style="text-align : left;" value = "'.$row2['color'].'">'.$row2['color'].'</td>
                <td style="text-align : left;" value = "'.$row2['size'].'">'.$row2['size'].'</td>
                <td style="text-align : left;" value = "'.$row2['invno'].'">'.$row2['invno'].'</td>
                <td style="text-align : left;" value = "'.$row2['jenis_dok'].'">'.$row2['jenis_dok'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_aju'].'">'.$row2['no_aju'].'</td>
                <td style="width: 100px;" value = "'.$tgl_aju.'">'.$tgl_aju.'</td>
                <td style="text-align : left;" value = "'.$row2['bcno'].'">'.$row2['bcno'].'</td>
                <td style="width: 100px;" value = "'.$bcdate.'">'.$bcdate.'</td>
                <td style="text-align : left;" value = "'.$row2['tujuan'].'">'.$row2['tujuan'].'</td>
                <td style="text-align : right;" value = "'.$row2['qty'].'">'.number_format($row2['qty'],2).'</td>
                <td style="text-align : left;" value = "'.$row2['unit'].'">'.$row2['unit'].'</td>
                <td style="text-align : right;" value = "'.$row2['berat_bersih'].'">'.$neto.'</td>
                <td style="text-align : left;" value = "'.$row2['remark'].'">'.$row2['remark'].'</td>
                <td style="text-align : left;" value = "'.$row2['username'].'">'.$row2['username'].'</td>
                <td style="text-align : left;" value = "'.$row2['ws'].'">'.$row2['ws'].'</td>
                <td style="text-align : left;" value = "'.$row2['styleno'].'">'.$row2['styleno'].'</td>
                <td style="text-align : left;" value = "'.$row2['curr'].'">'.$row2['curr'].'</td>
                <td style="text-align : right;" value = "'.$row2['price'].'">'.number_format($row2['price'],2).'</td>
                <td style="text-align : left;" value = "'.$row2['status'].'">'.$row2['status'].'</td>
                <td style="text-align : right;" value = "'.$row2['price_non_ro'].'">'.number_format($row2['price_non_ro'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['price_ro'].'">'.number_format($row2['price_ro'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_non_ro'].'">'.number_format($row2['total_non_ro'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_ro'].'">'.number_format($row2['total_ro'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['rate'].'">'.number_format($row2['rate'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_non_ro_idr'].'">'.number_format($row2['total_non_ro_idr'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_ro_idr'].'">'.number_format($row2['total_ro_idr'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_non_ro_idr'].'">'.number_format($row2['total_non_ro_idr'],2).'</td>
                </tr>
                ';
            }

            echo ' <tr >
            <th colspan="13" style="text-align : center;" value = "Total">Total</th>
            <th style="text-align : right;" value = "'.$total_qty.'">'.number_format($total_qty,2).'</th>
            <th colspan="9" style="text-align : center;" value = ""></th>
            <th style="text-align : right;" value = "'.$total_price_non_ro.'">'.number_format($total_price_non_ro,2).'</th>
            <th style="text-align : right;" value = "'.$total_price_ro.'">'.number_format($total_price_ro,2).'</th>
            <th style="text-align : right;" value = "'.$total_total_non_ro.'">'.number_format($total_total_non_ro,2).'</th>
            <th style="text-align : right;" value = "'.$total_total_ro.'">'.number_format($total_total_ro,2).'</th>
            <th style="text-align : center;" value = ""></th>
            <th style="text-align : right;" value = "'.$total_total_non_ro_idr.'">'.number_format($total_total_non_ro_idr,2).'</th>
            <th style="text-align : right;" value = "'.$total_total_ro_idr.'">'.number_format($total_total_ro_idr,2).'</th>
            <th style="text-align : right;" value = "'.$sum_total_ro_nonro_idr.'">'.number_format($sum_total_ro_nonro_idr,2).'</th>
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
