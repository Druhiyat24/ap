<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fa fa-sign-in" aria-hidden="true"></i> FABRIC TRANSACTION ITEM OUT</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="ca_fabric_trx_out_item.php" method="post">
    <div class="row g-3">

        <!-- Start Date -->
        <div class="col-md-2">
            <label for="start_date" class="form-label"><b>From</b></label>
            <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
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
           } ?>" placeholder="Start Date" autocomplete="off">
       </div>

       <!-- End Date -->
       <div class="col-md-2">
        <label for="end_date" class="form-label"><b>To</b></label>
        <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
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
       } ?>"  placeholder="End Date" autocomplete="off">
   </div>

   <!-- Tombol -->
   <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-info btn-sm me-2">
        <i class="fa fa-search"></i> Search
    </button>
    <button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
        <i class="fa fa-undo"></i> Reset
    </button>

    <?php
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_ca_fabric_trx_out_item.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success ml-2" style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';

    ?>     

</div>

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="mytable" 
          class="table table-striped table-bordered table-hover table-sm nowrap" 
          style="width:100%">
          <thead class="table-gradient text-white">
            <tr>
                        <th style="text-align: center;vertical-align: middle;">No Trans</th>
                        <th style="text-align: center;vertical-align: middle;">Tgl Trans</th>
                        <th style="text-align: center;vertical-align: middle;">Id Item</th>
                        <th style="text-align: center;vertical-align: middle;">Nama Barang</th>
                        <th style="text-align: center;vertical-align: middle;">Warna</th>
                        <th style="text-align: center;vertical-align: middle;">Ukuran</th>
                        <th style="text-align: center;vertical-align: middle;">No Inv</th>
                        <th style="text-align: center;vertical-align: middle;">Jenis Dok</th>
                        <th style="text-align: center;vertical-align: middle;">Nomor Aju</th>
                        <th style="text-align: center;vertical-align: middle;">Tgl Aju</th>
                        <th style="text-align: center;vertical-align: middle;">Nomor Daftar</th>
                        <th style="text-align: center;vertical-align: middle;">Tgl Daftar</th>
                        <th style="text-align: center;vertical-align: middle;">Supplier</th>
                        <th style="text-align: center;vertical-align: middle;">Jumlah</th>
                        <th style="text-align: center;vertical-align: middle;">Satuan</th>
                        <th style="text-align: center;vertical-align: middle;">Berat Bersih</th>
                        <th style="text-align: center;vertical-align: middle;">Keterangan</th>
                        <th style="text-align: center;vertical-align: middle;">Nama User</th>
                        <th style="text-align: center;vertical-align: middle;;">WS</th>
                        <th style="text-align: center;vertical-align: middle;;">WS Aktual</th>
                        <th style="text-align: center;vertical-align: middle;">Style</th>
                        <th style="text-align: center;vertical-align: middle;">Curr</th>
                        <th style="text-align: center;vertical-align: middle;">Price</th>
                        <th style="text-align: center;vertical-align: middle;">Status</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang / Unit Dalam Mata Uang Asal</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang Dalam Mata Uang Asal</th>
                        <th style="text-align: center;vertical-align: middle;">Rate</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang Ekuivalen IDR</th>
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
                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                $end_date = date("Y-m-d",strtotime($_POST['end_date']));                
            }

            $sql = mysqli_query($conn2,"select no_bppb, tgl_bppb, id_item, itemdesc, color, size, no_invoice, dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, tujuan, sum(qty_out) qty_out, satuan, berat_bersih, catatan, username, kpno, IFNULL(no_ws_aktual, kpno) no_ws_aktual, styleno, np_curr, round(sum(total) / sum(qty_out), 4) np_price, jenis_pengeluaran, round(sum(total) / sum(qty_out), 4) price_unit, sum(total) total, rate, sum(total_idr) total_idr from (select id_jo, no_bppb, tgl_bppb, id_roll, no_lot, no_roll, no_rak, id_item, itemdesc, color, size, IFNULL(no_invoice,'-') no_invoice, dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, tujuan, qty_out, satuan, berat_bersih, IFNULL(catatan,'-') catatan, username, kpno, no_ws_aktual, styleno, IFNULL(np_curr,'-') np_curr, IFNULL(np_price,0) np_price, jenis_pengeluaran, IFNULL(np_price,0) price_unit, (qty_out * IFNULL(np_price,0)) total, IFNULL(rate,1) rate, ((qty_out * IFNULL(np_price,0)) * IFNULL(rate,1)) total_idr from (select  b.id_jo, a.no_bppb, a.tgl_bppb, id_roll, no_lot, no_roll, no_rak, b.id_item, c.itemdesc, c.color, c.size, a.no_invoice, a.dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, a.tujuan, b.qty_out, b.satuan, 0 berat_bersih, a.catatan, CONCAT(a.created_by,' (',a.created_at, ') ') username, kpno, styleno, IFNULL(b.np_curr_rev,b.np_curr) np_curr, np_tgl_in, IFNULL(b.np_price_rev,b.np_price) np_price, jenis_pengeluaran, no_ws_aktual from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN masteritem c on c.id_item = b.id_item left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo where a.tgl_bppb BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.status = 'Y') a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bppb and cr.curr = a.np_curr) a GROUP BY no_bppb, id_jo, id_item, np_curr");


            function formatDateOrDash($date) {
                return (!empty($date) && $date != '0000-00-00')
                ? date("d-M-Y", strtotime($date))
                : '-';
            }

            function valueOrDash($value) {
                return !empty($value) ? $value : '-';
            }

            $total_qty =0;
            $total_price_non_ro =0;
            $total_total_non_ro =0;
            $sum_total_ro_nonro_idr =0;
            while($row2 = mysqli_fetch_array($sql)){
                $total_qty += $row2['qty_out'];
                $total_price_non_ro += $row2['price_unit'];
                $total_total_non_ro += $row2['total'];
                $sum_total_ro_nonro_idr += $row2['total_idr'];

                echo ' <tr style="font-size:12px;text-align:center;">
                <td style="text-align : left;" value = "'.$row2['no_bppb'].'">'.$row2['no_bppb'].'</td>
                <td style="width: 100px;" value = "'.$row2['tgl_bppb'].'">'.date("d-M-Y",strtotime($row2['tgl_bppb'])).'</td>
                <td style="text-align : left;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
                <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
                <td style="text-align : left;" value = "'.$row2['color'].'">'.$row2['color'].'</td>
                <td style="text-align : left;" value = "'.$row2['size'].'">'.$row2['size'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_invoice'].'">'.$row2['no_invoice'].'</td>
                <td style="text-align : left;" value = "'.$row2['dok_bc'].'">'.$row2['dok_bc'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_aju'].'">'.$row2['no_aju'].'</td>
                <td style="width: 100px;" value = "'.$row2['tgl_aju'].'">'.date("d-M-Y",strtotime($row2['tgl_aju'])).'</td>
                <td style="text-align : left;" value = "'.$row2['no_daftar'].'">'.$row2['no_daftar'].'</td>
                <td style="width: 100px;" value = "'.$row2['tgl_daftar'].'">'.date("d-M-Y",strtotime($row2['tgl_daftar'])).'</td>
                <td style="text-align : left;" value = "'.$row2['tujuan'].'">'.$row2['tujuan'].'</td>
                <td style="text-align : right;" value = "'.$row2['qty_out'].'">'.number_format($row2['qty_out'],2).'</td>
                <td style="text-align : left;" value = "'.$row2['satuan'].'">'.$row2['satuan'].'</td>
                <td style="text-align : right;" value = "'.$row2['berat_bersih'].'">'.$row2['berat_bersih'].'</td>
                <td style="text-align : left;" value = "'.$row2['catatan'].'">'.$row2['catatan'].'</td>
                <td style="text-align : left;" value = "'.$row2['username'].'">'.$row2['username'].'</td>
                <td style="text-align : left;" value = "'.$row2['kpno'].'">'.$row2['kpno'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_ws_aktual'].'">'.$row2['no_ws_aktual'].'</td>
                <td style="text-align : left;" value = "'.$row2['styleno'].'">'.$row2['styleno'].'</td>
                <td style="text-align : left;" value = "'.$row2['np_curr'].'">'.$row2['np_curr'].'</td>
                <td style="text-align : right;" value = "'.$row2['np_price'].'">'.number_format($row2['np_price'],2).'</td>
                <td style="text-align : left;" value = "'.$row2['jenis_pengeluaran'].'">'.$row2['jenis_pengeluaran'].'</td>
                <td style="text-align : right;" value = "'.$row2['price_unit'].'">'.number_format($row2['price_unit'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total'].'">'.number_format($row2['total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['rate'].'">'.number_format($row2['rate'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_idr'].'">'.number_format($row2['total_idr'],2).'</td>
                </tr>
                ';
            }
            ?>
        </tbody>
        <?php
            echo ' <tfoot> <tr >
            <th colspan="13" style="text-align : center;" value = "Total">Total</th>
            <th style="text-align : right;" value = "'.$total_qty.'">'.number_format($total_qty,2).'</th>
            <th colspan="10" style="text-align : center;" value = ""></th>
            <th style="text-align : right;" value = "'.$total_total_non_ro.'">'.number_format($total_total_non_ro,2).'</th>
            <th style="text-align : center;" value = ""></th>
            <th style="text-align : right;" value = "'.$sum_total_ro_nonro_idr.'">'.number_format($sum_total_ro_nonro_idr,2).'</th>
            </tr></tfoot>
            ';
            ?>
    </table>
</div>
</div>
</div>
</div>

<!-- CSS -->
<style>
  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
div.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 10px;
}
div.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 10px;
}


</style>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-6 mb-2"></div>
          <div id="txt_no_po" class="col-md-6 mb-2"></div>
          <div id="txt_supp" class="col-md-6 mb-2"></div>
          <div id="txt_top" class="col-md-6 mb-2"></div>
          <div id="txt_curr" class="col-md-6 mb-2"></div>
          <div id="txt_confirm" class="col-md-6 mb-2"></div>
          <div id="txt_confirm2" class="col-md-6 mb-2"></div>
          <div id="txt_tgl_po" class="col-md-6 mb-2"></div>
          <div id="details" class="col-12 mt-2"></div>
      </div>
  </div>
</div>
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
        $('#mytable').DataTable({
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: false 
        });

        $("[data-toggle=tooltip]").tooltip();
    });

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
        location.href = "ca_fabric_trx_out_item.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "ca_fabric_trx_out_item.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
