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
    <h2 class="text-center">FABRIC TRANSACTION IN</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="ca_fabric_trx_in.php" method="post">        
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
   
        echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_ca_fabric_trx_in.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

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
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Kode barang</th>
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
                        <th rowspan="2" style="text-align: center;vertical-align: middle;;">PO</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Type</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Inv/SJ</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Jumlah</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Satuan</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Berat Bersih</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Keterangan</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Nama User</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;;">WS</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Style</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Curr</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Price</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Price Acct</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Status</th>
                        <th colspan="4" style="text-align: center;vertical-align: middle;">Nilai Barang / Unit Dalam Mata Uang Asal</th>
                        <th colspan="4" style="text-align: center;vertical-align: middle;">Nilai Barang Dalam Mata Uang Asal</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Rate</th>
                        <th colspan="4" style="text-align: center;vertical-align: middle;">Nilai Barang Ekuivalen IDR</th>
                    </tr>
                    <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;">Nilai Non Subcont</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang</th>
                        <th style="text-align: center;vertical-align: middle;">Jasa Subcont</th>
                        <th style="text-align: center;vertical-align: middle;">Total</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Non Subcont</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang</th>
                        <th style="text-align: center;vertical-align: middle;">Jasa Subcont</th>
                        <th style="text-align: center;vertical-align: middle;">Total</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Non Subcont</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang</th>
                        <th style="text-align: center;vertical-align: middle;">Jasa Subcont</th>
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

                $sql = mysqli_query($conn2,"select bpbno, bpbdate, id_item, goods_code, itemdesc, color, size, invno, jenis_dok , no_aju, tgl_aju, bcno , bcdate, supplier, pono, tipe_com, sjno, qty, qty_good, qty_reject, unit, berat_bersih, remark, username, confirm_by, ws, styleno, curr, price2 price, price_act, tipe_pembelian, price_non_subkon, nilai_barang, jasa_subkon, total_price, round(total_non_subkon, 2) total_non_subkon, total_nilai_barang, total_jasa_subkon, rate, ROUND(total_non_subkon * rate,2) total_non_subkon_idr, ROUND(total_nilai_barang * rate,2) total_nilai_barang_idr, ROUND(total_jasa_subkon * rate,2) total_jasa_subkon_idr from (select *, (price_non_subkon + nilai_barang + jasa_subkon) total_price, (qty * price_non_subkon) total_non_subkon, ROUND(qty * nilai_barang,2) total_nilai_barang, ROUND(qty * jasa_subkon,2) total_jasa_subkon from (select price2,bpbno,bpbdate,a.id_item,goods_code,itemdesc,color,size,invno,jenis_dok,no_aju,tgl_aju, bcno, bcdate,supplier,pono,tipe_com,invno sjno, qty, qty_good,qty_reject, unit,berat_bersih, remark,username,confirm_by,tmpjo.kpno ws,tmpjo.styleno,a.curr,price, if(tipe_com ='BUYER','0',price) price_act,a.id_jo, IF(bpbno like '%RI/%' and supplier like '%Production%' ,'Return From Production',type_pch) tipe_pembelian, IF(type_pch like '%Subkontraktor%', 0,price) price_non_subkon, if(a.nilai_barang is null,ri.nilai_barang,a.nilai_barang) nilai_barang, IF(type_pch like '%Subkontraktor%',price,0) jasa_subkon, if(rate is null, '1', rate) rate from (select s.id_gen,a.no_dok bpbno,a.tgl_dok bpbdate,type_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.supplier,a.no_po pono,z.tipe_com,no_invoice invno,b.id_item,goods_code,concat(itemdesc,' ',add_info) itemdesc,s.color,s.size, (b.qty_good + coalesce(b.qty_reject,0)) qty,b.qty_good as qty_good,coalesce(b.qty_reject,0) as qty_reject, b.unit,'' berat_bersih,a.deskripsi remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,b.curr,if(z.tipe_com ='FOC' OR z.tipe_com ='Buyer','0',if(b.price_update is null,b.price, b.price_update)) price, b.price price2, a.type_pch jenis_trans,'' reffno,b.id_jo,'' no_ws, a.type_pch,b.nilai_barang from whs_inmaterial_fabric a 
        inner join whs_inmaterial_fabric_det b on b.no_dok = a.no_dok
        inner join masteritem s on b.id_item=s.id_item 
        LEFT join po_header po on po.pono = a.no_po
        LEFT join po_header_draft z on z.id = po.id_draft
        where a.tgl_dok BETWEEN '$start_date' and '$end_date' and b.status != 'N' and a.status != 'cancel') a
        left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo
                LEFT JOIN (select no_dok, id_jo, id_item,ROUND(total / jml,2) nilai_barang from (select no_dok, id_jo, id_item, sum(qty) jml, sum(qty * nilai_barang) total from (select no_dok,id_jo, id_item,sum(qty_aktual) qty, nilai_barang from whs_lokasi_inmaterial where no_dok like '%RI%' and nilai_barang is not null GROUP BY no_dok,id_jo,id_item,nilai_barang) a GROUP BY no_dok, id_jo, id_item) a GROUP BY no_dok, id_jo, id_item) ri on ri.no_dok = a.bpbno and ri.id_item = a.id_item and ri.id_jo = a.id_jo
                left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bpbdate and cr.curr = a.curr) a) a");
            

            $total_qty =0;
            $total_neto =0;
            $total_nonsub =0;
            $total_nilaibrg =0;
            $total_sub =0;
            $sum_total_ocy =0;
            $total_nonsub_idr =0;
            $total_nilaibrg_idr =0;
            $total_sub_idr =0;
            $sum_total_idr =0;
            while($row2 = mysqli_fetch_array($sql)){
                $no_aju = $row2['no_aju'];
                $bcno = $row2['bcno'];
                $neto = isset($row2['berat_bersih']) ? $row2['berat_bersih'] : 0;
                if ($no_aju == '' || $no_aju == null) {
                    $tgl_aju = '-'; 
                }else{
                    $tgl_aju = date("Y-m-d",strtotime($row2['tgl_aju']));
                }
                if ($bcno == '' || $bcno == null) {
                    $bcdate = '-'; 
                }else{
                    $bcdate = date("Y-m-d",strtotime($row2['bcdate']));
                }
                $total_ocy = $row2['total_non_subkon'] + $row2['total_nilai_barang'] + $row2['total_jasa_subkon'];
                $total_idr = $row2['total_non_subkon_idr'] + $row2['total_nilai_barang_idr'] + $row2['total_jasa_subkon_idr'];

                $total_qty += $row2['qty'];
                // $total_neto += $neto;
                $total_nonsub += $row2['total_non_subkon'];
                $total_nilaibrg += $row2['total_nilai_barang'];
                $total_sub += $row2['total_jasa_subkon'];
                $sum_total_ocy += $total_ocy;
                $total_nonsub_idr += $row2['total_non_subkon_idr'];
                $total_nilaibrg_idr += $row2['total_nilai_barang_idr'];
                $total_sub_idr += $row2['total_jasa_subkon_idr'];
                $sum_total_idr += $total_idr;

                echo ' <tr style="font-size:12px;text-align:center;">
                <td style="text-align : left;" value = "'.$row2['bpbno'].'">'.$row2['bpbno'].'</td>
                <td style="width: 100px;" value = "'.$row2['bpbdate'].'">'.date("d-M-Y",strtotime($row2['bpbdate'])).'</td>
                <td style="text-align : left;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
                <td style="text-align : left;" value = "'.$row2['goods_code'].'">'.$row2['goods_code'].'</td>
                <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
                <td style="text-align : left;" value = "'.$row2['color'].'">'.$row2['color'].'</td>
                <td style="text-align : left;" value = "'.$row2['size'].'">'.$row2['size'].'</td>
                <td style="text-align : left;" value = "'.$row2['invno'].'">'.$row2['invno'].'</td>
                <td style="text-align : left;" value = "'.$row2['jenis_dok'].'">'.$row2['jenis_dok'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_aju'].'">'.$row2['no_aju'].'</td>
                <td style="width: 100px;" value = "'.$tgl_aju.'">'.$tgl_aju.'</td>
                <td style="text-align : left;" value = "'.$row2['bcno'].'">'.$row2['bcno'].'</td>
                <td style="width: 100px;" value = "'.$bcdate.'">'.$bcdate.'</td>
                <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
                <td style="text-align : left;" value = "'.$row2['pono'].'">'.$row2['pono'].'</td>
                <td style="text-align : left;" value = "'.$row2['tipe_com'].'">'.$row2['tipe_com'].'</td>
                <td style="text-align : left;" value = "'.$row2['sjno'].'">'.$row2['sjno'].'</td>
                <td style="text-align : right;" value = "'.$row2['qty'].'">'.number_format($row2['qty'],2).'</td>
                <td style="text-align : left;" value = "'.$row2['unit'].'">'.$row2['unit'].'</td>
                <td style="text-align : right;" value = "'.$row2['berat_bersih'].'">'.$neto.'</td>
                <td style="text-align : left;" value = "'.$row2['remark'].'">'.$row2['remark'].'</td>
                <td style="text-align : left;" value = "'.$row2['username'].'">'.$row2['username'].'</td>
                <td style="text-align : left;" value = "'.$row2['ws'].'">'.$row2['ws'].'</td>
                <td style="text-align : left;" value = "'.$row2['styleno'].'">'.$row2['styleno'].'</td>
                <td style="text-align : left;" value = "'.$row2['curr'].'">'.$row2['curr'].'</td>
                <td style="text-align : right;" value = "'.$row2['price'].'">'.number_format($row2['price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['price_act'].'">'.number_format($row2['price_act'],2).'</td>
                <td style="text-align : left;" value = "'.$row2['tipe_pembelian'].'">'.$row2['tipe_pembelian'].'</td>
                <td style="text-align : right;" value = "'.$row2['price_non_subkon'].'">'.number_format($row2['price_non_subkon'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['nilai_barang'].'">'.number_format($row2['nilai_barang'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['jasa_subkon'].'">'.number_format($row2['jasa_subkon'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_price'].'">'.number_format($row2['total_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_non_subkon'].'">'.number_format($row2['total_non_subkon'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_nilai_barang'].'">'.number_format($row2['total_nilai_barang'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_jasa_subkon'].'">'.number_format($row2['total_jasa_subkon'],2).'</td>
                <td style="text-align : right;" value = "'.$total_ocy.'">'.number_format($total_ocy,2).'</td>
                <td style="text-align : right;" value = "'.$row2['rate'].'">'.number_format($row2['rate'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_non_subkon_idr'].'">'.number_format($row2['total_non_subkon_idr'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_nilai_barang_idr'].'">'.number_format($row2['total_nilai_barang_idr'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['total_jasa_subkon_idr'].'">'.number_format($row2['total_jasa_subkon_idr'],2).'</td>
                <td style="text-align : right;" value = "'.$total_idr.'">'.number_format($total_idr,2).'</td>
                </tr>
                ';
            }

            echo ' <tr >
            <th colspan="17" style="text-align : center;" value = "Total">Total</th>
            <th style="text-align : right;" value = "'.$total_qty.'">'.number_format($total_qty,2).'</th>
            <th colspan="14" style="text-align : center;" value = ""></th>
            <th style="text-align : right;" value = "'.$total_nonsub.'">'.number_format($total_nonsub,2).'</th>
            <th style="text-align : right;" value = "'.$total_nilaibrg.'">'.number_format($total_nilaibrg,2).'</th>
            <th style="text-align : right;" value = "'.$total_sub.'">'.number_format($total_sub,2).'</th>
            <th style="text-align : right;" value = "'.$sum_total_ocy.'">'.number_format($sum_total_ocy,2).'</th>
            <th style="text-align : center;" value = ""></th>
            <th style="text-align : right;" value = "'.$total_nonsub_idr.'">'.number_format($total_nonsub_idr,2).'</th>
            <th style="text-align : right;" value = "'.$total_nilaibrg_idr.'">'.number_format($total_nilaibrg_idr,2).'</th>
            <th style="text-align : right;" value = "'.$total_sub_idr.'">'.number_format($total_sub_idr,2).'</th>
            <th style="text-align : right;" value = "'.$sum_total_idr.'">'.number_format($sum_total_idr,2).'</th>
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
