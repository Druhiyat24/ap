<html>
<head>
    <title>Export Data General Ledger </title>
</head>
<body>
    <style type="text/css">
        body{
            font-family: sans-serif;
        }
        table{
            margin: 20px auto;
            border-collapse: collapse;
        }
        table th,
        table td{
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

        }
        a{
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=fabric transaction summary.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> FABRIC SUMMARY SUBCONTRACTOR<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Id Item</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Item Code</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Item Name</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Unit</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">WS</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">PO</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Beg Balance</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Sent to Subcontractors / Received from Warehouse</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Return from Subcontractors/ Sent to Warehouse</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <tr>
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
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"select id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, (qty_awal - qty_in_sblm) qty_awal, COALESCE(ROUND((total_awal - total_in_sblm) / (qty_awal - qty_in_sblm),2),0) price_awal, (total_awal - total_in_sblm) total_awal, qty_out, price_out, total_out, qty_in, price price_in, total_in, (qty_awal - qty_in_sblm + qty_out - qty_in) qty_akhir, COALESCE(ROUND((total_awal - total_in_sblm + total_out - total_in) / (qty_awal - qty_in_sblm + qty_out - qty_in),2),0) price_akhir, (total_awal - total_in_sblm + total_out - total_in) total_akhir from (select a.*,COALESCE(qty_in,0) qty_in, COALESCE(price,0) price, COALESCE(total_in,0) total_in, COALESCE(qty_in_sblm,0) qty_in_sblm, COALESCE(price_sblm,0) price_sblm, COALESCE(total_in_sblm,0) total_in_sblm from (select id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, round(sum(COALESCE(qty_awal,0)),2) qty_awal, COALESCE(ROUND(sum(COALESCE(qty_awal,0) * COALESCE(price_awal,0)) / sum(COALESCE(qty_awal,0)),2),0) price_awal, ROUND(sum(COALESCE(qty_awal,0) * COALESCE(price_awal,0)),2) total_awal , round(sum(COALESCE(qty,0)),2) qty_out, COALESCE(ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)) / sum(COALESCE(qty,0)),2),0) price_out, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)),2) total_out from (select 'saldo_awal' id, id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, round(sum(COALESCE(qty,0)),2) qty_awal, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)) / sum(COALESCE(qty,0)),2) price_awal, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)),2) total_awal , 0 qty, 0 price, 0 total from (select 'saldo_awal' id,id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, round(sum(COALESCE(qty,0)),2) qty, ROUND(COALESCE(price,0),2) price, ROUND(sum(COALESCE(qty,0) * COALESCE(price,0)),2) total from (select a.no_bpb,tgl_bpb, no_barcode, '' tujuan, no_po_subkon no_po, tmpjo.kpno no_ws, a.id_jo, a.id_item, mi.goods_code, mi.itemdesc, qty, IF(a.curr = 'IDR',price,(price * rate)) price, a.curr, unit  from ca_saldoawal_subkon a INNER JOIN masteritem mi on mi.id_item = a.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bpb and cr.curr = a.curr where a.status = 'Y') a GROUP BY id_item, unit, id_jo, no_po, price
            UNION
            select 'saldo_awal_out' id, id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, sum(COALESCE(qty_out,0)) qty, ROUND(COALESCE(price,0),2) price, ROUND(sum(COALESCE(qty_out,0) * COALESCE(price,0)),2) total from (select a.no_bppb,tgl_bppb, id_roll, tujuan, no_po_subkon no_po, kpno no_ws, b.id_jo, b.id_item, mi.goods_code, mi.itemdesc, qty_out, IF(b.curr = 'IDR',price_in,(price_in * rate)) price, b.curr, satuan unit  from whs_bppb_h a inner join (select b.*,tgl_dok from whs_bppb_det b INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll) b on b.no_bppb = a.no_bppb INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.tgl_dok and cr.curr = b.curr where tgl_bppb < '$start_date' and tgl_bppb >= '2024-12-01' and jenis_pengeluaran like '%Subkontraktor%' and no_po_subkon is not null and b.status = 'Y') a GROUP BY id_item, unit, id_jo, no_po, price) a GROUP BY id_item, unit, id_jo, no_po
            UNION
            select 'trx_out' id, id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, 0 qty_awal, 0 price_awal, 0 total_awal, sum(COALESCE(qty_out,0)) qty, ROUND(COALESCE(price,0),2) price, ROUND(sum(COALESCE(qty_out,0) * COALESCE(price,0)),2) total from (select a.no_bppb,tgl_bppb, id_roll, tujuan, no_po_subkon no_po, kpno no_ws, b.id_jo, b.id_item, mi.goods_code, mi.itemdesc, qty_out, IF(b.curr = 'IDR',price_in,(price_in * rate)) price, b.curr, satuan unit  from whs_bppb_h a inner join (select b.*,tgl_dok from whs_bppb_det b INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll) b on b.no_bppb = a.no_bppb INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.tgl_dok and cr.curr = b.curr where tgl_bppb BETWEEN '$start_date' and '$end_date' and jenis_pengeluaran like '%Subkontraktor%' and no_po_subkon is not null and b.status = 'Y') a GROUP BY id_item, unit, id_jo, no_po, price) a GROUP BY id_item, unit, id_jo, no_po) a 
            LEFT JOIN
            (select id_item, goods_code, itemdesc, unit, id_jo, no_ws, no_po, sum(COALESCE(qty_good,0)) qty_in, COALESCE(ROUND(sum(COALESCE(qty_good,0) * COALESCE(price,0)) / sum(COALESCE(qty_good,0)),2),0) price, ROUND(sum(COALESCE(qty_good,0) * COALESCE(price,0)),2) total_in, sum(COALESCE(qty_sblm,0)) qty_in_sblm, COALESCE(ROUND(sum(COALESCE(qty_sblm,0) * COALESCE(price_sblm,0)) / sum(COALESCE(qty_sblm,0)),2),0) price_sblm, ROUND(sum(COALESCE(qty_sblm,0) * COALESCE(price_sblm,0)),2) total_in_sblm from (
            select 'trx_in' id, a.no_dok, a.tgl_dok, a.supplier, no_po, kpno no_ws, b.id_jo, IF(id_item_out is null,b.id_item,id_item_out) id_item, mi.goods_code, mi.itemdesc, qty_good, COALESCE(IF(ot.curr = 'IDR',price_in,round((price_in * rate),2)),0) price, 0 qty_sblm, 0 price_sblm, b.curr, unit from whs_inmaterial_fabric a INNER JOIN whs_inmaterial_fabric_det b on b.no_dok = a.no_dok INNER JOIN (select pono, id_jo, id_item id_gen, id_item_out FROM po_header a INNER JOIN po_item b on b.id_po = a.id INNER JOIN masteritem mi on mi.id_gen = b.id_gen) po on po.pono = a.no_po and po.id_gen = b.id_item and po.id_jo = b.id_jo INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo LEFT JOIN (select b.curr, tgl_dok, tgl_bppb, no_po_subkon, b.id_jo, b.id_item, COALESCE(price_in,0) price_in from whs_bppb_h a inner join whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll where no_po_subkon != '' GROUP BY no_po_subkon, id_jo, id_item) ot on ot.id_jo = b.id_jo and ot.id_item = b.id_item and ot.no_po_subkon = a.no_po left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = ot.tgl_dok and cr.curr = ot.curr where a.tgl_dok BETWEEN '$start_date' and '$end_date' and b.status = 'Y' and a.status != 'cancel' and a.type_pch like '%Subkontraktor%'
            UNION
            select 'trx_in_sblm' id, a.no_dok, a.tgl_dok, a.supplier, no_po, kpno no_ws, b.id_jo, IF(id_item_out is null,b.id_item,id_item_out) id_item, mi.goods_code, mi.itemdesc, 0 qty_good, 0 price, qty_good qty_sblm, COALESCE(IF(ot.curr = 'IDR',price_in,round((price_in * rate),2)),0) price_sblm, b.curr, unit from whs_inmaterial_fabric a INNER JOIN whs_inmaterial_fabric_det b on b.no_dok = a.no_dok INNER JOIN (select pono, id_jo, id_item id_gen, id_item_out FROM po_header a INNER JOIN po_item b on b.id_po = a.id INNER JOIN masteritem mi on mi.id_gen = b.id_gen) po on po.pono = a.no_po and po.id_gen = b.id_item and po.id_jo = b.id_jo INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo LEFT JOIN (select b.curr, tgl_dok, tgl_bppb, no_po_subkon, b.id_jo, b.id_item, COALESCE(price_in,0) price_in from whs_bppb_h a inner join whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN (select no_barcode, a.no_dok, tgl_dok, id_jo,id_item from whs_lokasi_inmaterial a left join whs_inmaterial_fabric b on b.no_dok = a.no_dok GROUP BY no_barcode) bin on bin.no_barcode = b.id_roll where no_po_subkon != '' GROUP BY no_po_subkon, id_jo, id_item) ot on ot.id_jo = b.id_jo and ot.id_item = b.id_item and ot.no_po_subkon = a.no_po left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = ot.tgl_dok and cr.curr = ot.curr where a.tgl_dok < '$start_date' and a.tgl_dok >= '2024-12-01' and b.status = 'Y' and a.status != 'cancel' and a.type_pch like '%Subkontraktor%') a GROUP BY id_item, unit, id_jo,  no_po, unit) b on b.id_item  = a.id_item and b.id_jo = a.id_jo and a.unit = b.unit and b.no_po = a.no_po ) a");


$total_sal_qty =0;
$total_sal_nilai =0;
$total_sal_price =0;
$no = 0;
while($row2 = mysqli_fetch_array($sql)){
    $no++;
    $total_sal_qty =$row2['qty_awal'];
    $total_sal_price =$row2['price_awal'];
    $total_sal_nilai =$row2['total_awal'];
    $total_qty =$row2['qty_akhir'];
    $total_price =$row2['price_akhir'];
    $total_nilai =$row2['total_akhir'];
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
    <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
    <td style="text-align : left;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
    <td style="text-align : left;" value = "'.$row2['goods_code'].'">'.$row2['goods_code'].'</td>
    <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
    <td style="text-align : left;" value = "'.$row2['unit'].'">'.$row2['unit'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_ws'].'">'.$row2['no_ws'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_po'].'">'.$row2['no_po'].'</td>
    <td style="text-align : right;" value = "'.$total_sal_qty.'">'.number_format($total_sal_qty,2).'</td>
    <td style="text-align : right;" value = "'.$total_sal_price.'">'.number_format($total_sal_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_sal_nilai.'">'.number_format($total_sal_nilai,2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_out'].'">'.number_format($row2['qty_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_out'].'">'.number_format($row2['price_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_out'].'">'.number_format($row2['total_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_in'].'">'.number_format($row2['qty_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_in'].'">'.number_format($row2['price_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_in'].'">'.number_format($row2['total_in'],2).'</td>
    <td style="text-align : right;" value = "'.$total_qty.'">'.number_format($total_qty,2).'</td>
    <td style="text-align : right;" value = "'.$total_price.'">'.number_format($total_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_nilai.'">'.number_format($total_nilai,2).'</td>
    </tr>
    ';
}

?>
<?php 

}
?>
</table>

</body>
</html>




