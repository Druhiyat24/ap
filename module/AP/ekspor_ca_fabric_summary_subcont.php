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
    header("Content-Disposition: attachment; filename=fabric transaction Summary Subcont.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> FABRIC MUTASI SUBCONTRACTOR<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Id Item</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Item Name</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Unit</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">WS</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No PO</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Supplier</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Beg Balance</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Sent to Subcontractors / Received from Warehouse</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Return from Subcontractors/ Sent to Warehouse</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Adjustment</th>
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
            <th style="text-align: center;vertical-align: middle;">Qty</th>
            <th style="text-align: center;vertical-align: middle;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;">Amount</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"WITH
saldo_awal as (select id_item, itemdesc, unit, id_jo, no_ws, no_po, supplier, qty, price, total from whs_saldo_awal_subcont_fabric ),

out_before as (select id_item, itemdesc, satuan, id_jo, kpno, IFNULL(a.no_po_subkon,'-') no_po_subkon, tujuan, sum(qty_out) qty_out, np_curr, round(sum(total) / sum(qty_out), 4) np_price, round(sum(total) / sum(qty_out), 4) price_unit, sum(total_idr) total_idr from (select id_jo, no_bppb, tgl_bppb, id_roll, no_lot, no_roll, no_rak, id_item, itemdesc, color, size, IFNULL(no_invoice,'-') no_invoice, dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, tujuan, qty_out, satuan, berat_bersih, IFNULL(catatan,'-') catatan, username, kpno, no_ws_aktual, styleno, IFNULL(np_curr,'-') np_curr, IFNULL(np_price,0) np_price, jenis_pengeluaran, IFNULL(np_price,0) price_unit, (qty_out * IFNULL(np_price,0)) total, IFNULL(rate,1) rate, ((qty_out * IFNULL(np_price,0)) * IFNULL(rate,1)) total_idr, no_po_subkon from (select  b.id_jo, a.no_bppb, a.tgl_bppb, id_roll, no_lot, no_roll, no_rak, b.id_item, c.itemdesc, c.color, c.size, a.no_invoice, a.dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, a.tujuan, b.qty_out, b.satuan, 0 berat_bersih, a.catatan, CONCAT(a.created_by,' (',a.created_at, ') ') username, kpno, styleno, IFNULL(b.np_curr_rev,b.np_curr) np_curr, np_tgl_in, IFNULL(b.np_price_rev,b.np_price) np_price, jenis_pengeluaran, no_ws_aktual, no_po_subkon from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN masteritem c on c.id_item = b.id_item left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo where a.tgl_bppb > '2025-11-30' AND a.tgl_bppb < '$start_date' and jenis_pengeluaran like '%Subkontraktor%' and a.status != 'Cancel' and b.status = 'Y') a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bppb and cr.curr = a.np_curr) a GROUP BY id_item, satuan, id_jo, no_po_subkon, tujuan),

in_h_before as (select a.no_dok, a.tgl_dok, b.id_jo, b.id_item, c.itemdesc, c.color, c.size, IFNULL(type_bc,'-') type_bc, IFNULL(no_invoice,'-') no_invoice, IFNULL(no_aju,'-') no_aju, tgl_aju, IFNULL(no_daftar,'-') no_daftar, tgl_daftar, a.supplier, IFNULL(a.no_po,'-') no_po, IFNULL(no_invoice,'-') no_sj, IFNULL(a.deskripsi,'-') deskripsi, CONCAT(a.created_by,' (',a.created_at, ') ') username, kpno, styleno, a.type_pch, b.price from whs_inmaterial_fabric a INNER JOIN whs_inmaterial_fabric_det b on b.no_dok = a.no_dok INNER JOIN masteritem c on c.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) d on d.id_jo=b.id_jo where a.tgl_dok > '2025-11-30' and a.tgl_dok < '$start_date' and a.status != 'Cancel' and b.status != 'N' GROUP BY b.id_item, b.id_jo, b.no_dok),

    in_det_before as (select no_dok, id_jo, id_item, no_barcode, no_roll, no_lot, kode_lok, sum(qty_aktual) qty_in, satuan, np_curr, np_tgl_in, IFNULL(np_price,0) np_price, IF(np_curr = 'IDR',1,IFNULL(rate,1)) rate from (select a.no_dok, b.id_jo, b.id_item, b.no_barcode, b.no_roll, b.no_lot, b.kode_lok, b.qty_aktual, satuan, IFNULL(np_curr_rev,np_curr) np_curr, np_tgl_in, IFNULL(np_price_rev,np_price) np_price from whs_inmaterial_fabric a INNER JOIN whs_lokasi_inmaterial b on b.no_dok = a.no_dok where a.tgl_dok > '2025-11-30' and a.tgl_dok < '$start_date' and a.status != 'Cancel' and b.status != 'N') a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' and tanggal > '2025-11-30' and tanggal < '$start_date' GROUP BY tanggal, curr ) cr on cr.tanggal = a.np_tgl_in and cr.curr = np_curr GROUP BY a.id_item, a.id_jo, a.no_dok,np_curr),

in_before as (select id_item, itemdesc, satuan, id_jo, kpno, if(no_po = '','-', no_po) no_po, supplier, sum(qty) qty, (sum(jumlah_in_idr) / sum(qty)) price, sum(jumlah_in_idr) jumlah_in_idr from ((select a.id_item, itemdesc, b.satuan, a.id_jo, kpno, if(no_po = '','-', no_po) no_po, supplier, sum(b.qty_in) qty, (sum((np_price * qty_in) * rate) / sum(b.qty_in)) price, sum((np_price * qty_in) * rate) jumlah_in_idr from in_h_before a INNER JOIN in_det_before b on b.no_dok = a.no_dok and b.id_item = a.id_item and b.id_jo = a.id_jo where type_pch like '%Subkontraktor%' GROUP BY a.id_item, satuan, a.id_jo, no_po, supplier) UNION select id_item, deskripsi, unit, id_jo, no_ws, no_po, supplier, qty, price, (qty * price) total from ca_adjust_input where tgl_periode > '2025-11-30' and tgl_periode < '$start_date') a GROUP BY id_item, satuan, id_jo, no_po, supplier),

out_trx as (select id_item, itemdesc, satuan, id_jo, kpno, IFNULL(a.no_po_subkon,'-') no_po_subkon, tujuan, sum(qty_out) qty_out, np_curr, round(sum(total) / sum(qty_out), 4) np_price, round(sum(total) / sum(qty_out), 4) price_unit, sum(total_idr) total_idr from (select id_jo, no_bppb, tgl_bppb, id_roll, no_lot, no_roll, no_rak, id_item, itemdesc, color, size, IFNULL(no_invoice,'-') no_invoice, dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, tujuan, qty_out, satuan, berat_bersih, IFNULL(catatan,'-') catatan, username, kpno, no_ws_aktual, styleno, IFNULL(np_curr,'-') np_curr, IFNULL(np_price,0) np_price, jenis_pengeluaran, IFNULL(np_price,0) price_unit, (qty_out * IFNULL(np_price,0)) total, IFNULL(rate,1) rate, ((qty_out * IFNULL(np_price,0)) * IFNULL(rate,1)) total_idr, no_po_subkon from (select  b.id_jo, a.no_bppb, a.tgl_bppb, id_roll, no_lot, no_roll, no_rak, b.id_item, c.itemdesc, c.color, c.size, a.no_invoice, a.dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, a.tujuan, b.qty_out, b.satuan, 0 berat_bersih, a.catatan, CONCAT(a.created_by,' (',a.created_at, ') ') username, kpno, styleno, IFNULL(b.np_curr_rev,b.np_curr) np_curr, np_tgl_in, IFNULL(b.np_price_rev,b.np_price) np_price, jenis_pengeluaran, no_ws_aktual, no_po_subkon from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN masteritem c on c.id_item = b.id_item left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo where a.tgl_bppb > '2025-11-30' AND a.tgl_bppb BETWEEN '$start_date' and '$end_date' and jenis_pengeluaran like '%Subkontraktor%' and a.status != 'Cancel' and b.status = 'Y') a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bppb and cr.curr = a.np_curr) a GROUP BY id_item, satuan, id_jo, no_po_subkon, tujuan),

in_h as (select a.no_dok, a.tgl_dok, b.id_jo, b.id_item, c.itemdesc, c.color, c.size, IFNULL(type_bc,'-') type_bc, IFNULL(no_invoice,'-') no_invoice, IFNULL(no_aju,'-') no_aju, tgl_aju, IFNULL(no_daftar,'-') no_daftar, tgl_daftar, a.supplier, IFNULL(a.no_po,'-') no_po, IFNULL(no_invoice,'-') no_sj, IFNULL(a.deskripsi,'-') deskripsi, CONCAT(a.created_by,' (',a.created_at, ') ') username, kpno, styleno, a.type_pch, b.price from whs_inmaterial_fabric a INNER JOIN whs_inmaterial_fabric_det b on b.no_dok = a.no_dok INNER JOIN masteritem c on c.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) d on d.id_jo=b.id_jo  where a.tgl_dok > '2025-11-30' and a.tgl_dok BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.status != 'N' GROUP BY b.id_item, b.id_jo, b.no_dok),

    in_det as (select no_dok, id_jo, id_item, no_barcode, no_roll, no_lot, kode_lok, sum(qty_aktual) qty_in, satuan, np_curr, np_tgl_in, IFNULL(np_price,0) np_price, IF(np_curr = 'IDR',1,IFNULL(rate,1)) rate from (select a.no_dok, b.id_jo, b.id_item, b.no_barcode, b.no_roll, b.no_lot, b.kode_lok, b.qty_aktual, satuan, IFNULL(np_curr_rev,np_curr) np_curr, np_tgl_in, IFNULL(np_price_rev,np_price) np_price from whs_inmaterial_fabric a INNER JOIN whs_lokasi_inmaterial b on b.no_dok = a.no_dok where  a.tgl_dok > '2025-11-30' and a.tgl_dok BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.status != 'N') a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' and tanggal BETWEEN '$start_date' and '$end_date' GROUP BY tanggal, curr ) cr on cr.tanggal = a.np_tgl_in and cr.curr = np_curr GROUP BY a.id_item, a.id_jo, a.no_dok,np_curr),

in_trx as (select a.id_item, itemdesc, b.satuan, a.id_jo, kpno, if(no_po = '','-', no_po) no_po, supplier, sum(b.qty_in) qty, (sum((np_price * qty_in) * rate) / sum(b.qty_in)) price, sum((np_price * qty_in) * rate) jumlah_in_idr from in_h a INNER JOIN in_det b on b.no_dok = a.no_dok and b.id_item = a.id_item and b.id_jo = a.id_jo where type_pch like '%Subkontraktor%' GROUP BY a.id_item, satuan, a.id_jo, no_po, supplier),

in_adjust as (select id_item, deskripsi, unit, id_jo, no_ws, no_po, supplier, qty, price, (qty * price) total from ca_adjust_input where tgl_periode > '2025-11-30' and tgl_periode BETWEEN '$start_date' and '$end_date'),

saldo_out as (select id_item, itemdesc, unit, id_jo, no_ws, no_po, supplier, sum(Coalesce(qty,0)) qty, (sum(Coalesce(total,0)) / sum(Coalesce(qty,0))) price, sum(Coalesce(total,0)) total, sum(qty_out) qty_out, (sum(total_out) / sum(qty_out)) price_out, sum(total_out) total_out from (
select id_item, itemdesc, unit, id_jo, no_ws, no_po, supplier, qty, price, total, 0 qty_out, 0 price_out, 0 total_out from saldo_awal 
UNION ALL 
select id_item, itemdesc, satuan, id_jo, kpno, no_po_subkon, tujuan, qty_out, price_unit, total_idr, 0 qty_out, 0 price_out, 0 total_out from out_before
UNION ALL
select id_item, itemdesc, satuan, id_jo, kpno, no_po_subkon, tujuan, 0 qty, 0 price, 0 total,  qty_out, price_unit, total_idr from out_trx) a GROUP BY id_item, unit, id_jo, no_po, supplier)

select a.id_item, a.itemdesc, a.unit, a.id_jo, a.no_ws, a.no_po, a.supplier, (a.qty - COALESCE(b.qty,0)) qty_awal, COALESCE(round((a.total - COALESCE(b.jumlah_in_idr,0)) / (a.qty - COALESCE(b.qty,0)),2),0) price_awal, (a.total - COALESCE(b.jumlah_in_idr,0)) total_awal, a.qty_out, COALESCE(a.price_out,0) price_out, a.total_out, COALESCE(c.qty,0) qty_in, COALESCE(c.price,0) price_in, COALESCE(c.jumlah_in_idr,0) total_in, COALESCE(d.qty,0) qty_adj, COALESCE(d.price,0) price_adj, COALESCE(d.total,0) total_adj, ((a.qty - COALESCE(b.qty,0)) + a.qty_out - COALESCE(c.qty,0) - COALESCE(d.qty,0)) qty_akhir, COALESCE(round(((a.total - COALESCE(b.jumlah_in_idr,0)) + a.total_out - COALESCE(c.jumlah_in_idr,0) - COALESCE(d.total,0)) / ((a.qty - COALESCE(b.qty,0)) + a.qty_out - COALESCE(c.qty,0) - COALESCE(d.qty,0)),2),0) price_akhir, ((a.total - COALESCE(b.jumlah_in_idr,0)) + a.total_out - COALESCE(c.jumlah_in_idr,0) - COALESCE(d.total,0)) total_akhir from saldo_out a left join in_before b on b.id_item = a.id_item and b.satuan = a.unit and b.id_jo = a.id_jo and b.no_po = a.no_po and b.supplier = a.supplier left join in_trx c on c.id_item = a.id_item and c.satuan = a.unit and c.id_jo = a.id_jo and c.no_po = a.no_po and c.supplier = a.supplier left join in_adjust d on d.id_item = a.id_item and d.unit = a.unit and d.id_jo = a.id_jo and d.no_po = a.no_po and d.supplier = a.supplier
");


$total_qty =0;
$total_price_non_ro =0;
$total_total_non_ro =0;
$sum_total_ro_nonro_idr =0;
$no = 0;
while($row2 = mysqli_fetch_array($sql)){
    $no++;

    echo ' <tr style="font-size:12px;text-align:center;">
    <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
    <td style="width: 100px;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
    <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
    <td style="text-align : left;" value = "'.$row2['unit'].'">'.$row2['unit'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_ws'].'">'.$row2['no_ws'].'</td>
    <td style="text-align : left;" value = "'.$row2['no_po'].'">'.$row2['no_po'].'</td>
    <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
    <td style="text-align : right;" value = "'.$row2['qty_awal'].'">'.number_format($row2['qty_awal'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_awal'].'">'.number_format($row2['price_awal'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_awal'].'">'.number_format($row2['total_awal'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_out'].'">'.number_format($row2['qty_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_out'].'">'.number_format($row2['price_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_out'].'">'.number_format($row2['total_out'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_in'].'">'.number_format($row2['qty_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_in'].'">'.number_format($row2['price_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_in'].'">'.number_format($row2['total_in'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_adj'].'">'.number_format($row2['qty_adj'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_adj'].'">'.number_format($row2['price_adj'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_adj'].'">'.number_format($row2['total_adj'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_akhir'].'">'.number_format($row2['qty_akhir'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_akhir'].'">'.number_format($row2['price_akhir'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_akhir'].'">'.number_format($row2['total_akhir'],2).'</td>
    </tr>
    ';

    ?>
    <?php 

}
?>
</table>

</body>
</html>




