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
    <h2 class="text-center">FABRIC TRANSACTION SUMMARY SUBCONTRACTOR</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="ca_fabric_summary_sc.php" method="post">        
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

    echo '<a style="padding-right: 10px;" target="_blank" href="ekspor_ca_fabric_summary_sc.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

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
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Id Item</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Item Name</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Unit</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">WS</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">PO</th>
                        <th rowspan="2" style="text-align: center;vertical-align: middle;">Supplier</th>
                        <th colspan="3" style="text-align: center;vertical-align: middle;">Beg Balance</th>
                        <th colspan="3" style="text-align: center;vertical-align: middle;">Sent to Subcontractors / Received from Warehouse</th>
                        <th colspan="3" style="text-align: center;vertical-align: middle;">Return from Subcontractors/ Sent to Warehouse</th>
                        <th colspan="3" style="text-align: center;vertical-align: middle;">Adjustment</th>
                        <th colspan="3" style="text-align: center;vertical-align: middle;">Ending Balance</th>
                    </tr>
                    <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;">Qty</th>
                        <th style="text-align: center;vertical-align: middle;">Cost/Unit</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Qty</th>
                        <th style="text-align: center;vertical-align: middle;">Cost/Unit</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Qty</th>
                        <th style="text-align: center;vertical-align: middle;">Cost/Unit</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Qty</th>
                        <th style="text-align: center;vertical-align: middle;">Cost/Unit</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
                        <th style="text-align: center;vertical-align: middle;">Qty</th>
                        <th style="text-align: center;vertical-align: middle;">Cost/Unit</th>
                        <th style="text-align: center;vertical-align: middle;">Amount</th>
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

            // $sql = mysqli_query($conn2,"select id_item, goods_code, itemdesc, unit, ws, qty_awal, price_awal, total_awal, qty_out, price_out, total_out, qty_in, price_in, total_in, round(qty_awal + qty_out - qty_awal,2) total_qty, ROUND(total_awal + total_out - total_in,2) total_nilai, (ROUND(total_awal + total_out - total_in,2) / IF(round(qty_awal + qty_out - qty_awal,2) = 0,1,round(qty_awal + qty_out - qty_awal,2))) total_price from (select id_item,goods_code,itemdesc, unit, ws, SUM(COALESCE(qty_awal,0)) qty_awal, SUM(COALESCE(price_awal,0)) price_awal, SUM(COALESCE(total_awal,0)) total_awal, SUM(COALESCE(qty_out,0)) qty_out, SUM(COALESCE(price_out,0)) price_out, SUM(COALESCE(total_out,0)) total_out, SUM(COALESCE(qty_in,0)) qty_in, SUM(COALESCE(price_in,0)) price_in, SUM(COALESCE(total_in,0)) total_in from(select a.id_item, b.goods_code, b.itemdesc, a.unit, a.kpno ws, '' supplier, '' no_po_subkon, a.styleno, a.type_pch status, qty qty_awal, total total_awal, price price_awal, 0 qty_out, 0 total_out, 0 price_out, 0 qty_in, 0 total_in, 0 price_in  from ca_saldoawal_subkon a inner join masteritem b on b.id_item = a.id_item GROUP BY a.id_item, a.unit, a.kpno 
            //     UNION
            //     select id_item,goods_code, itemdesc, unit, ws, supplier,no_po_subkon, styleno, status, 0 qty_awal, 0 total_awal, 0 price_awal, qty_out, total_out, price_out, 0 qty_in, 0 total_in, 0 price_in from (select *, round(total_out/qty_out,2) price_out from (select id_item,goods_code, itemdesc, unit, ws, status, tujuan supplier, no_po_subkon, styleno, SUM(qty) qty_out, SUM(COALESCE(total_ro_idr,0) + COALESCE(total_non_ro_idr,0)) total_out  from (select bppbno, bppbdate, a.id_item, goods_code, itemdesc, color, size, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, tujuan, qty, unit, berat_bersih, remark, username, ws, styleno, curr, price, status,price_non_ro, price_ro, rate, round(qty * price_non_ro,2) total_non_ro, round(qty * price_ro,2) total_ro, IF(curr = 'IDR',round(qty * price_ro,2),round((qty * price_ro) * rate,2)) total_ro_idr, IF(curr = 'IDR',round(qty * price_non_ro,2),round((qty * price_non_ro) * rate,2)) total_non_ro_idr, no_po_subkon from (select bppbno, bppbdate, a.id_item, goods_code, itemdesc, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, supplier tujuan, color, size, qty, unit, berat_bersih, remark, username, ws, styleno, if(a.curr = '' OR a.curr is null,'IDR',a.curr) curr, if(bppbno like '%RO/%',b.price,a.price) price, jenis_pengeluaran status,price_non_ro, if(b.price is null,0,b.price) price_ro, IF(rate is null,1,rate) rate,no_po_subkon from (select a.no_bppb bppbno,a.no_req bppbno_req,a.tgl_bppb bppbdate,no_invoice invno,a.dok_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju tanggal_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.tujuan supplier,b.id_item,goods_code,concat(itemdesc,' ',add_info) itemdesc,s.color,s.size, sum(b.qty_out) qty,0 as qty_good,0 as qty_reject, b.satuan unit,'' berat_bersih,a.catatan remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,ac.kpno ws,ac.styleno,b.curr,b.price,br.idws_act,'' jenis_trans,IF(a.jenis_pengeluaran is null,'-',a.jenis_pengeluaran) jenis_pengeluaran, (coalesce(price_in,0) + COALESCE(nilai_barang,0)) price_non_ro, b.id_jo, a.no_po_subkon
            //     from whs_bppb_h a 
            //     inner join whs_bppb_det b on b.no_bppb = a.no_bppb
            //     inner join masteritem s on b.id_item=s.id_item 
            //     left join (select id_jo,id_so from jo_det group by id_jo ) tmpjod on tmpjod.id_jo=b.id_jo 
            //     left join (select bppbno as no_req,idws_act from bppb_req group by no_req) br on a.no_req = br.no_req 
            //     left join so on tmpjod.id_so=so.id 
            //     left join act_costing ac on so.id_cost=ac.id 
            //     where LEFT(a.no_bppb,2) = 'GK' and b.status != 'N' and a.status != 'cancel' and a.tgl_bppb BETWEEN  '$start_date' and '$end_date' and matclass= 'FABRIC' and jenis_pengeluaran like '%Subkontraktor%' GROUP BY b.id_jo,b.id_item,b.satuan,b.no_bppb,(coalesce(price_in,0) + COALESCE(nilai_barang,0)) order by a.no_bppb) a LEFT JOIN (select a.no_bppb, id_jo, id_item, price from whs_bppb_ro a INNER JOIN whs_bppb_h b on b.no_bppb = a.no_bppb where b.status != 'cancel' and b.tgl_bppb BETWEEN  '$start_date' and '$end_date') b on b.no_bppb = a.bppbno and b.id_item = a.id_item and b.id_jo = a.id_jo
            //     left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bppbdate and cr.curr = a.curr) a) a  GROUP BY status, id_item, unit, ws) a) a
            //     UNION
            //     select id_item,goods_code, itemdesc, unit, ws, supplier,pono, styleno, tipe_pembelian, 0 qty_awal, 0 total_awal, 0 price_awal , 0 qty_out, 0 total_out, 0 price_out, qty, total, price from (select *, round(total/qty,2) price from (select id_item,goods_code, itemdesc, unit, ws, tipe_pembelian,supplier, pono, styleno, SUM(qty_good) qty, SUM(COALESCE(total_non_subkon_idr,0) + COALESCE(total_nilai_barang_idr,0) + COALESCE(total_jasa_subkon_idr,0)) total  from (select bpbno, bpbdate, id_item, goods_code, itemdesc, color, size, invno, jenis_dok , no_aju, tgl_aju, bcno , bcdate, supplier, pono, tipe_com, sjno, qty, qty_good, qty_reject, unit, berat_bersih, remark, username, confirm_by, ws, styleno, curr, price, price_act, tipe_pembelian, price_non_subkon, nilai_barang, jasa_subkon, total_price, total_non_subkon, total_nilai_barang, total_jasa_subkon, rate, ROUND(total_non_subkon * rate,2) total_non_subkon_idr, ROUND(total_nilai_barang * rate,2) total_nilai_barang_idr, ROUND(total_jasa_subkon * rate,2) total_jasa_subkon_idr from (select *, (price_non_subkon + nilai_barang + jasa_subkon) total_price, ROUND(qty * price_non_subkon,2) total_non_subkon, ROUND(qty * nilai_barang,2) total_nilai_barang, ROUND(qty * jasa_subkon,2) total_jasa_subkon from (select bpbno,bpbdate,a.id_item,goods_code,itemdesc,color,size,invno,jenis_dok,no_aju,tgl_aju, bcno, bcdate,supplier,pono,tipe_com,invno sjno, qty, qty_good,qty_reject, unit,berat_bersih, remark,username,confirm_by,tmpjo.kpno ws,tmpjo.styleno,a.curr,price, if(tipe_com ='BUYER','0',price) price_act,a.id_jo, CASE WHEN bpbno like '%RI%' and supplier like '%cutting%' then 'Pengembalian dari Produksi' WHEN bpbno like '%RI%' and supplier like '%sample%' then 'Pengembalian dari Sample Room' else type_pch end tipe_pembelian, IF(type_pch like '%Subkontraktor%', 0,price) price_non_subkon, if(a.nilai_barang is null,ri.nilai_barang,a.nilai_barang) nilai_barang, IF(type_pch like '%Subkontraktor%',price,0) jasa_subkon, if(rate is null, '1', rate) rate from (select s.id_gen,a.no_dok bpbno,a.tgl_dok bpbdate,type_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.supplier,a.no_po pono,z.tipe_com,no_invoice invno,b.id_item,goods_code,concat(itemdesc,' ',add_info) itemdesc,s.color,s.size, (b.qty_good + coalesce(b.qty_reject,0)) qty,b.qty_good as qty_good,coalesce(b.qty_reject,0) as qty_reject, b.unit,'' berat_bersih,a.deskripsi remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,b.curr,if(z.tipe_com ='FOC','0',b.price)price, a.type_pch jenis_trans,'' reffno,b.id_jo,'' no_ws, a.type_pch,b.nilai_barang from whs_inmaterial_fabric a 
            //     inner join whs_inmaterial_fabric_det b on b.no_dok = a.no_dok
            //     inner join masteritem s on b.id_item=s.id_item 
            //     LEFT join po_header po on po.pono = a.no_po
            //     LEFT join po_header_draft z on z.id = po.id_draft
            //     where a.tgl_dok BETWEEN  '$start_date' and '$end_date' and b.status != 'N' and a.status != 'cancel') a
            //     left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo
            //     LEFT JOIN (select no_dok, id_jo, id_item,ROUND(total / jml,2) nilai_barang from (select no_dok, id_jo, id_item, sum(qty) jml, sum(qty * nilai_barang) total from (select no_dok,id_jo, id_item,sum(qty_aktual) qty, nilai_barang from whs_lokasi_inmaterial where no_dok like '%RI%' and nilai_barang is not null GROUP BY no_dok,id_jo,id_item,nilai_barang) a GROUP BY no_dok, id_jo, id_item) a GROUP BY no_dok, id_jo, id_item) ri on ri.no_dok = a.bpbno and ri.id_item = a.id_item and ri.id_jo = a.id_jo
            //     left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bpbdate and cr.curr = a.curr) a where tipe_pembelian like '%Subkontraktor%') a) a GROUP BY tipe_pembelian, id_item, unit, ws) a) a
            // ) a GROUP BY id_item, unit, ws) a");


            $sql = mysqli_query($conn2,"select id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, nama_supp, (qty_awal - qty_in_sblm) qty_awal, COALESCE(ROUND((total_awal - total_in_sblm) / (qty_awal - qty_in_sblm),2),0) price_awal, (total_awal - total_in_sblm) total_awal, qty_out, price_out, total_out, qty_in, price price_in, total_in, qty_adj, price_adj, total_adj, (qty_awal - qty_in_sblm + qty_out - qty_in - qty_adj) qty_akhir, COALESCE(ROUND((total_awal - total_in_sblm + total_out - total_in - total_adj) / (qty_awal - qty_in_sblm + qty_out - qty_in - qty_adj),2),0) price_akhir, (total_awal - total_in_sblm + total_out - total_in - total_adj) total_akhir from (select a.*,COALESCE(qty_in,0) qty_in, COALESCE(price,0) price, COALESCE(total_in,0) total_in, COALESCE(qty_in_sblm,0) qty_in_sblm, COALESCE(price_sblm,0) price_sblm, COALESCE(total_in_sblm,0) total_in_sblm ,COALESCE(qty_adj,0) qty_adj, COALESCE(price_adj,0) price_adj, COALESCE(total_adj,0) total_adj from (select id_item, goods_code, itemdesc, unit, id_jo, no_ws, nama_supp, GROUP_CONCAT(DISTINCT no_po) no_po, round(sum(COALESCE(qty_awal,0)),2) qty_awal, COALESCE(ROUND(sum(COALESCE(qty_awal,0) * COALESCE(price_awal,0)) / sum(COALESCE(qty_awal,0)),2),0) price_awal, ROUND(sum(COALESCE(qty_awal,0) * COALESCE(price_awal,0)),2) total_awal , round(sum(COALESCE(qty,0)),2) qty_out, COALESCE(ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)) / sum(COALESCE(qty,0)),2),0) price_out, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)),2) total_out from (select 'saldo_awal' id, nama_supp, id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, round(sum(COALESCE(qty,0)),2) qty_awal, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)) / sum(COALESCE(qty,0)),2) price_awal, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)),2) total_awal , 0 qty, 0 price, 0 total from (select 'saldo_awal' id, nama_supp,id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, round(sum(COALESCE(qty,0)),2) qty, ROUND(COALESCE(price,0),2) price, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)),2) total from (select nama_supp,a.no_bpb,tgl_bpb, no_barcode, '' tujuan, no_po_subkon no_po, tmpjo.kpno no_ws, a.id_jo, a.id_item, mi.goods_code, mi.itemdesc, qty, IF(a.curr = 'IDR',price,(price * rate)) price, a.curr, unit  from ca_saldoawal_subkon a INNER JOIN masteritem mi on mi.id_item = a.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bpb and cr.curr = a.curr where a.status = 'Y') a GROUP BY id_item, unit, id_jo, no_po, price
                UNION
                select 'saldo_awal_out' id, tujuan, id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, sum(COALESCE(qty_out,0)) qty, ROUND(COALESCE(price,0),2) price, ROUND(sum(COALESCE(qty_out,0) * COALESCE(price,0)),2) total from (select a.no_bppb,tgl_bppb, id_roll, tujuan, no_po_subkon no_po, kpno no_ws, b.id_jo, b.id_item, mi.goods_code, mi.itemdesc, qty_out, IF(b.curr = 'IDR',price_in,(price_in * rate)) price, b.curr, satuan unit  from whs_bppb_h a inner join (select b.*,tgl_dok from whs_bppb_det b INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll) b on b.no_bppb = a.no_bppb INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.tgl_dok and cr.curr = b.curr where tgl_bppb < '$start_date' and tgl_bppb >= '2024-12-01' and jenis_pengeluaran like '%Subkontraktor%' and b.status = 'Y') a GROUP BY id_item, unit, id_jo, no_po, price) a GROUP BY id_item, unit, id_jo, no_po
                UNION
                select 'trx_out' id, tujuan, id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, 0 qty_awal, 0 price_awal, 0 total_awal, sum(COALESCE(qty_out,0)) qty, ROUND(COALESCE(price,0),2) price, ROUND(sum(COALESCE(qty_out,0) * COALESCE(price,0)),2) total from (select a.no_bppb,tgl_bppb, id_roll, tujuan, no_po_subkon no_po, kpno no_ws, b.id_jo, b.id_item, mi.goods_code, mi.itemdesc, qty_out, IF(b.curr = 'IDR',price_in,(price_in * rate)) price, b.curr, satuan unit  from whs_bppb_h a inner join (select b.*,tgl_dok from whs_bppb_det b INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll) b on b.no_bppb = a.no_bppb INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.tgl_dok and cr.curr = b.curr where tgl_bppb BETWEEN '$start_date' and '$end_date' and jenis_pengeluaran like '%Subkontraktor%' and b.status = 'Y') a GROUP BY id_item, unit, id_jo, no_po, price) a GROUP BY id_item, id_jo) a 
                LEFT JOIN
                (select id_item, goods_code, itemdesc, unit, id_jo, no_ws, GROUP_CONCAT(DISTINCT no_po) no_po, sum(COALESCE(qty_good,0)) qty_in, COALESCE(ROUND(sum(COALESCE(qty_good,0) * COALESCE(price,0)) / sum(COALESCE(qty_good,0)),2),0) price, ROUND(sum(COALESCE(qty_good,0) * COALESCE(price,0)),2) total_in, sum(COALESCE(qty_sblm,0)) qty_in_sblm, COALESCE(ROUND(sum(COALESCE(qty_sblm,0) * COALESCE(price_sblm,0)) / sum(COALESCE(qty_sblm,0)),2),0) price_sblm, ROUND(sum(COALESCE(qty_sblm,0) * COALESCE(price_sblm,0)),2) total_in_sblm from (
                select 'trx_in' id, a.no_dok, a.tgl_dok, a.supplier, no_po, kpno no_ws, b.id_jo, IF(id_item_out is null,b.id_item,id_item_out) id_item, mi.goods_code, mi.itemdesc, qty_good, COALESCE(IF(ot.curr = 'IDR',b.nilai_barang,round((b.nilai_barang * if(rate is null,1,rate)),2)),0) price, 0 qty_sblm, 0 price_sblm, b.curr, unit from whs_inmaterial_fabric a INNER JOIN whs_inmaterial_fabric_det b on b.no_dok = a.no_dok INNER JOIN (select pono, id_jo, id_item id_gen, id_item_out FROM po_header a INNER JOIN po_item b on b.id_po = a.id INNER JOIN masteritem mi on mi.id_gen = b.id_gen) po on po.pono = a.no_po and po.id_gen = b.id_item and po.id_jo = b.id_jo INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo LEFT JOIN (select b.curr, tgl_dok, tgl_bppb, no_po_subkon, b.id_jo, b.id_item, COALESCE(price_in,0) price_in from whs_bppb_h a inner join whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll where no_po_subkon != '' GROUP BY no_po_subkon, id_jo, id_item) ot on ot.id_jo = b.id_jo and ot.id_item = b.id_item and ot.no_po_subkon = a.no_po left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = ot.tgl_dok and cr.curr = ot.curr where a.tgl_dok BETWEEN '$start_date' and '$end_date' and b.status = 'Y' and a.status != 'cancel' and a.type_pch like '%Subkontraktor%'
                                UNION
                                select 'adj' id,'' no_dok,'' tgl_dok,'' supplier, no_po, no_ws,id_jo, id_item, goods_code, itemdesc, sum(COALESCE(qty,0)) qty, ROUND(COALESCE(price,0),2) price, 0 qty_sblm, 0 price_sblm, 'IDR' curr, unit from ca_adjust_subcont where tgl_dok BETWEEN '$start_date' and '$end_date' GROUP BY id_item, unit, id_jo, no_po, price
                                UNION
                                select 'adj_sblm' id,'' no_dok,'' tgl_dok,'' supplier, no_po, no_ws,id_jo, id_item, goods_code, itemdesc, 0 qty_good, 0 price, sum(COALESCE(qty,0)) qty, ROUND(COALESCE(price,0),2) price, 'IDR' curr, unit from ca_adjust_subcont where tgl_dok < '$start_date' and tgl_dok >= '2024-12-01' GROUP BY id_item, unit, id_jo, no_po, price
                                                                UNION
                                select 'adj_inp_sblm' id,no_dok, tgl_periode tgl_dok,supplier, no_po, no_ws,id_jo, a.id_item, goods_code, itemdesc, 0 qty_good, 0 price, sum(COALESCE(qty,0)) qty, ROUND(COALESCE(price,0),2) price, 'IDR' curr, unit from ca_adjust_input a inner join masteritem b on b.id_item = a.id_item where tgl_periode < '$start_date' and tgl_periode >= '2024-12-01' and a.`status` = 'Y' GROUP BY a.id_item, unit, id_jo, no_po, price
                UNION
                select 'trx_in_sblm' id, a.no_dok, a.tgl_dok, a.supplier, no_po, kpno no_ws, b.id_jo, IF(id_item_out is null,b.id_item,id_item_out) id_item, mi.goods_code, mi.itemdesc, 0 qty_good, 0 price, qty_good qty_sblm, COALESCE(IF(ot.curr = 'IDR',b.nilai_barang,round((b.nilai_barang * if(rate is null,1,rate)),2)),0) price_sblm, b.curr, unit from whs_inmaterial_fabric a INNER JOIN whs_inmaterial_fabric_det b on b.no_dok = a.no_dok INNER JOIN (select pono, id_jo, id_item id_gen, id_item_out FROM po_header a INNER JOIN po_item b on b.id_po = a.id INNER JOIN masteritem mi on mi.id_gen = b.id_gen) po on po.pono = a.no_po and po.id_gen = b.id_item and po.id_jo = b.id_jo INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo LEFT JOIN (select b.curr, tgl_dok, tgl_bppb, no_po_subkon, b.id_jo, b.id_item, COALESCE(price_in,0) price_in from whs_bppb_h a inner join whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll where no_po_subkon != '' GROUP BY no_po_subkon, id_jo, id_item) ot on ot.id_jo = b.id_jo and ot.id_item = b.id_item and ot.no_po_subkon = a.no_po left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = ot.tgl_dok and cr.curr = ot.curr where a.tgl_dok < '$start_date' and a.tgl_dok >= '2024-12-01' and b.status = 'Y' and a.status != 'cancel' and a.type_pch like '%Subkontraktor%') a GROUP BY id_item, id_jo) b on b.id_item  = a.id_item and b.id_jo = a.id_jo LEFT JOIN
                                (select id_item, goods_code, itemdesc, unit, id_jo, no_ws, GROUP_CONCAT(DISTINCT no_po) no_po, sum(COALESCE(qty_good,0)) qty_adj, COALESCE(ROUND(sum(COALESCE(qty_good,0) * COALESCE(price,0)) / sum(COALESCE(qty_good,0)),2),0) price_adj, ROUND(sum(COALESCE(qty_good,0) * COALESCE(price,0)),2) total_adj from (
                select 'adj_inp' id,no_dok, tgl_periode tgl_dok,supplier, no_po, no_ws,id_jo, a.id_item, goods_code, itemdesc, sum(COALESCE(qty,0)) qty_good, ROUND(COALESCE(price,0),2) price, 0 qty_sblm, 0 price_sblm, 'IDR' curr, unit from ca_adjust_input a inner join masteritem b on b.id_item = a.id_item where tgl_periode BETWEEN '$start_date' and '$end_date' and a.`status` = 'Y' GROUP BY a.id_item, unit, id_jo, no_po, price) a GROUP BY id_item, id_jo) c on c.id_item  = a.id_item and c.id_jo = a.id_jo) a");


$total_qty =0;
$total_neto =0;
$total_nonsub =0;
$total_nilaibrg =0;
$total_sub =0;
$sum_total_ocy =0;
$total_nonsub_idr =0;

$total_sal_qty =0;
$total_sal_nilai =0;
$total_sal_price =0;
while($row2 = mysqli_fetch_array($sql)){
    $total_sal_qty =$row2['qty_awal'];
    $total_sal_price =$row2['price_awal'];
    $total_sal_nilai =$row2['total_awal'];
    $total_qty =$row2['qty_akhir'];
    $total_price =$row2['price_akhir'];
    $total_nilai =$row2['total_akhir'];

    // $total_qty =$row2['total_qty'];
    // $total_price =$row2['total_price'];
    // $total_nilai =$row2['total_nilai'];
    // $total_qty = $total_sal_qty + $row2['qty_out'] - $row2['qty_in'];
    // $total_nilai = $total_sal_nilai + $row2['total_out'] - $row2['total_in'];
    // if ($total_qty == 0) {
    //     $totalqty = 1;
    // }else{
    //     $totalqty = $total_qty; 
    // }
    // $total_price = $total_nilai / $totalqty;
    if ($total_sal_qty == 0 and $row2['qty_out'] == 0 and $row2['qty_in'] == 0 and $total_qty == 0) {
       '';
   }else{


    echo ' <tr style="font-size:12px;text-align:center;">
    <td style="text-align : left;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
    <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
    <td style="text-align : left;" value = "'.$row2['unit'].'">'.$row2['unit'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_ws'].'">'.$row2['no_ws'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_po'].'">'.$row2['no_po'].'</td>
    <td style="text-align : left;" value = "'.$row2['nama_supp'].'">'.$row2['nama_supp'].'</td>
    <td style="text-align : right;" value = "'.$total_sal_qty.'">'.number_format($total_sal_qty,2).'</td>
    <td style="text-align : right;" value = "'.$total_sal_price.'">'.number_format($total_sal_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_sal_nilai.'">'.number_format($total_sal_nilai,2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_out'].'">'.number_format($row2['qty_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_out'].'">'.number_format($row2['price_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_out'].'">'.number_format($row2['total_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_in'].'">'.number_format($row2['qty_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_in'].'">'.number_format($row2['price_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_in'].'">'.number_format($row2['total_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_adj'].'">'.number_format($row2['qty_adj'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_adj'].'">'.number_format($row2['price_adj'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_adj'].'">'.number_format($row2['total_adj'],2).'</td>
    <td style="text-align : right;" value = "'.$total_qty.'">'.number_format($total_qty,2).'</td>
    <td style="text-align : right;" value = "'.$total_price.'">'.number_format($total_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_nilai.'">'.number_format($total_nilai,2).'</td>
    </tr>
    ';
}
}

// echo ' <tr >
// <th colspan="17" style="text-align : center;" value = "Total">Total</th>
// <th style="text-align : right;" value = "'.$total_qty.'">'.number_format($total_qty,2).'</th>
// <th colspan="14" style="text-align : center;" value = ""></th>
// <th style="text-align : right;" value = "'.$total_nonsub.'">'.number_format($total_nonsub,2).'</th>
// <th style="text-align : right;" value = "'.$total_nilaibrg.'">'.number_format($total_nilaibrg,2).'</th>
// <th style="text-align : right;" value = "'.$total_sub.'">'.number_format($total_sub,2).'</th>
// <th style="text-align : right;" value = "'.$sum_total_ocy.'">'.number_format($sum_total_ocy,2).'</th>
// <th style="text-align : center;" value = ""></th>
// <th style="text-align : right;" value = "'.$total_nonsub_idr.'">'.number_format($total_nonsub_idr,2).'</th>
// <th style="text-align : right;" value = "'.$total_nilaibrg_idr.'">'.number_format($total_nilaibrg_idr,2).'</th>
// <th style="text-align : right;" value = "'.$total_sub_idr.'">'.number_format($total_sub_idr,2).'</th>
// <th style="text-align : right;" value = "'.$sum_total_idr.'">'.number_format($sum_total_idr,2).'</th>
// </tr>
// ';


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
            startDate : "01-12-2024",
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
