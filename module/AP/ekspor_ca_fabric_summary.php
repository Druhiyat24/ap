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

    <h4> FABRIC SUMMARY<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Id Item</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Item Code</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Item Name</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Unit</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">WS</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Beg Balance</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Local Purchase</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Import Purchase</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Return from Subcontractors - Fabric</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Return from Production </th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Return from Sample Room</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Total In</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Sent to Production</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Sent to Subcontractors - Fabric</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Return to Supplier - Local</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Return to Supplier - Import </th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Sent to Sample Room </th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Sales Non Group</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Sales Group</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Others</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Total Out</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Adjustment</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Ending Balance</th>
        </tr>
        <tr>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Qty</th>
            <th style="text-align: center;vertical-align: middle;width: 100px;">Cost/Unit</th>
            <th style="text-align: center;vertical-align: middle;width: 120px;">Amount</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"select a.*, coalesce(total_adj,0) total_adj, coalesce(price_adj,0) price_adj, coalesce(qty_adj,0) qty_adj, coalesce(total_adj_bfr,0) total_adj_bfr, coalesce(price_adj_bfr,0) price_adj_bfr, coalesce(qty_adj_bfr,0) qty_adj_bfr from (SELECT 
            id_item,
            goods_code,
            itemdesc,
            unit,
            ws,
            qty_awal,
            price_awal,
            total_awal,
            qty_a,
            price_a,
            total_a,
            qty_b,
            price_b,
            total_b,
            qty_c,
            price_c,
            total_c,
            qty_d,
            price_d,
            total_d,
            qty_e,
            price_e,
            total_e,
            total_in_qty,
            total_in_price,
            total_in_nilai,
            qty_oa,
            price_oa,
            total_oa,
            qty_ob,
            price_ob,
            total_ob,
            qty_oc,
            price_oc,
            total_oc,
            qty_od,
            price_od,
            total_od,
            qty_oe,
            price_oe,
            total_oe,
            qty_of,
            price_of,
            total_of,
            qty_og,
            price_og,
            total_og,
            qty_oh,
            price_oh,
            total_oh,
            total_out_qty,
            total_out_price,
            total_out_nilai,
            (qty_awal + total_in_qty - total_out_qty) AS total_end_qty,
            (total_awal + total_in_nilai - total_out_nilai) AS nilai_all,
            (qty_awal + total_in_qty - total_out_qty) AS qty_all,
            (
            (total_awal + total_in_nilai - total_out_nilai / 
            IF(
            (total_awal + total_in_qty - total_out_qty) = 0, 
            1, 
            (total_awal + total_in_qty - total_out_qty)
            )
            )) AS total_end_price,
            (
            (qty_awal + total_in_qty - total_out_qty) * 
            (
            (total_awal + total_in_nilai - total_out_nilai) / 
            IF(
            (qty_awal + total_in_qty - total_out_qty) = 0, 
            1, 
            (qty_awal + total_in_qty - total_out_qty)
            )
            )
            ) AS total_end_nilai
            FROM 
            (
            SELECT 
            id_item,
            goods_code,
            itemdesc,
            unit,
            ws,
            qty_awal,
            if(round(qty_awal,4) <= 0,0,price_awal) price_awal,
            if(round(qty_awal,4) <= 0,0,total_awal) total_awal,
            qty_a,
            price_a,
            total_a,
            qty_b,
            price_b,
            total_b,
            qty_c,
            price_c,
            total_c,
            qty_d,
            price_d,
            total_d,
            qty_e,
            price_e,
            total_e,
            (qty_a + qty_b + qty_c + qty_d + qty_e) AS total_in_qty,
            (
            (total_a + total_b + total_c + total_d + total_e) / 
            IF(
            (qty_a + qty_b + qty_c + qty_d + qty_e) = 0, 
            1, 
            (qty_a + qty_b + qty_c + qty_d + qty_e)
            )
            ) AS total_in_price,
            (total_a + total_b + total_c + total_d + total_e) AS total_in_nilai,
            qty_oa,
            price_oa,
            total_oa,
            qty_ob,
            price_ob,
            total_ob,
            qty_oc,
            price_oc,
            total_oc,
            qty_od,
            price_od,
            total_od,
            qty_oe,
            price_oe,
            total_oe,
            qty_of,
            price_of,
            total_of,
            qty_og,
            price_og,
            total_og,
            qty_oh,
            price_oh,
            total_oh,
            (
            qty_oa + qty_ob + qty_oc + qty_od + qty_oe + qty_of + qty_og + qty_oh
            ) AS total_out_qty,
            (
            (total_oa + total_ob + total_oc + total_od + total_oe + total_of + total_og + total_oh) / 
            IF(
            (qty_oa + qty_ob + qty_oc + qty_od + qty_oe + qty_of + qty_og + qty_oh) = 0, 
            1, 
            (qty_oa + qty_ob + qty_oc + qty_od + qty_oe + qty_of + qty_og + qty_oh)
            )
            ) AS total_out_price,
            (total_oa + total_ob + total_oc + total_od + total_oe + total_of + total_og + total_oh) AS total_out_nilai

            from (select id_item,goods_code,itemdesc, unit, ws, SUM(COALESCE(qty_awal,0)) qty_awal, round(SUM(COALESCE(total_awal,0)) / SUM(COALESCE(qty_awal,0)),2) price_awal, SUM(COALESCE(total_awal,0)) total_awal, SUM(COALESCE(qty_a,0)) qty_a, SUM(COALESCE(price_a,0)) price_a, SUM(COALESCE(total_a,0)) total_a,SUM(COALESCE(qty_b,0)) qty_b, SUM(COALESCE(price_b,0)) price_b, SUM(COALESCE(total_b,0)) total_b,SUM(COALESCE(qty_c,0)) qty_c, SUM(COALESCE(price_c,0)) price_c, SUM(COALESCE(total_c,0)) total_c,SUM(COALESCE(qty_d,0)) qty_d, SUM(COALESCE(price_d,0)) price_d, SUM(COALESCE(total_d,0)) total_d,SUM(COALESCE(qty_e,0)) qty_e, SUM(COALESCE(price_e,0)) price_e, SUM(COALESCE(total_e,0)) total_e, SUM(COALESCE(qty_oa,0)) qty_oa, SUM(COALESCE(price_oa,0)) price_oa, SUM(COALESCE(total_oa,0)) total_oa, SUM(COALESCE(qty_ob,0)) qty_ob, SUM(COALESCE(price_ob,0)) price_ob, SUM(COALESCE(total_ob,0)) total_ob, SUM(COALESCE(qty_oc,0)) qty_oc, SUM(COALESCE(price_oc,0)) price_oc, SUM(COALESCE(total_oc,0)) total_oc, SUM(COALESCE(qty_od,0)) qty_od, SUM(COALESCE(price_od,0)) price_od, SUM(COALESCE(total_od,0)) total_od, SUM(COALESCE(qty_oe,0)) qty_oe, SUM(COALESCE(price_oe,0)) price_oe, SUM(COALESCE(total_oe,0)) total_oe, SUM(COALESCE(qty_of,0)) qty_of, SUM(COALESCE(price_of,0)) price_of, SUM(COALESCE(total_of,0)) total_of, SUM(COALESCE(qty_og,0)) qty_og, SUM(COALESCE(price_og,0)) price_og, SUM(COALESCE(total_og,0)) total_og, SUM(COALESCE(qty_oh,0)) qty_oh, SUM(COALESCE(price_oh,0)) price_oh, SUM(COALESCE(total_oh,0)) total_oh from (
            select *,0 qty, 0 total, 0 price, 0 qty_a, 0 price_a, 0 total_a, 0 qty_b, 0 price_b, 0 total_b, 0 qty_c, 0 price_c, 0 total_c, 0 qty_d, 0 price_d, 0 total_d, 0 qty_e, 0 price_e, 0 total_e,0 qty_oa, 0 price_oa, 0 total_oa, 0 qty_ob, 0 price_ob, 0 total_ob, 0 qty_oc, 0 price_oc, 0 total_oc, 0 qty_od, 0 price_od, 0 total_od, 0 qty_oe, 0 price_oe, 0 total_oe, 0 qty_of, 0 price_of, 0 total_of, 0 qty_og, 0 price_og, 0 total_og, 0 qty_oh, 0 price_oh, 0 total_oh from (select id_item, goods_code, itemdesc, unit, ws, tipe_pembelian, sum(qty_awal) qty_awal, SUM(total_awal) total_awal, round(SUM(total_awal)/sum(qty_awal),2) price_awal from (select * from (select id_item,goods_code, itemdesc, unit, ws, tipe_pembelian, SUM(qty) qty_awal, SUM(COALESCE(total_non_subkon_idr,0) + COALESCE(total_nilai_barang_idr,0) + COALESCE(total_jasa_subkon_idr,0)) total_awal  from (select bpbno, bpbdate, id_item, goods_code, itemdesc, color, size, invno, jenis_dok , no_aju, tgl_aju, bcno , bcdate, supplier, pono, tipe_com, sjno, qty, qty_good, 0 qty_reject, unit, berat_bersih, remark, username, confirm_by, ws, styleno, curr, price, price_act, tipe_pembelian, price_non_subkon, nilai_barang, jasa_subkon, total_price, total_non_subkon, total_nilai_barang, total_jasa_subkon, rate, ROUND(total_non_subkon * rate,2) total_non_subkon_idr, ROUND(total_nilai_barang * rate,2) total_nilai_barang_idr, ROUND(total_jasa_subkon * rate,2) total_jasa_subkon_idr from (select *, (price_non_subkon + nilai_barang + jasa_subkon) total_price, ROUND(qty * price_non_subkon,2) total_non_subkon, ROUND(qty * nilai_barang,2) total_nilai_barang, ROUND(qty * jasa_subkon,2) total_jasa_subkon from (select no_bpb bpbno, tgl_bpb bpbdate, a.id_item, goods_code, itemdesc, color, size, '' invno, '' jenis_dok, '' no_aju, '' tgl_aju, '' bcno, '' bcdate, '' supplier, '' pono, '' tipe_com, '' sjno, sum(qty) qty, sum(qty) qty_good, unit, '' berat_bersih, '' remark, styleno, '' username, '' confirm_by, kpno ws, a.curr, price, price price_act, id_jo, type_pch tipe_pembelian, IF(type_pch like '%Subkontraktor%', 0,price) price_non_subkon, 0 nilai_barang, IF(type_pch like '%Subkontraktor%',price,0) jasa_subkon, if(rate is null, '1', rate) rate from ca_saldoawal_fabric a INNER JOIN masteritem b on b.id_item = a.id_item left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bpb and cr.curr = a.curr where a.status = 'Y' GROUP BY no_bpb, a.id_item, a.id_jo, unit) a) a) a GROUP BY tipe_pembelian, id_item, unit, ws) a

            UNION

            SELECT 
            id_item,
            goods_code,
            itemdesc,
            unit,
            ws,
            '' tipe_pembelian, 
            (total_in_qty - total_out_qty) qty,
            (total_in_nilai - total_out_nilai) nilai
            FROM 
            (
            SELECT 
            id_item,
            goods_code,
            itemdesc,
            unit,
            ws,
            qty_awal,
            price_awal,
            total_awal,
            qty_a,
            price_a,
            total_a,
            qty_b,
            price_b,
            total_b,
            qty_c,
            price_c,
            total_c,
            qty_d,
            price_d,
            total_d,
            qty_e,
            price_e,
            total_e,
            (qty_a + qty_b + qty_c + qty_d + qty_e) AS total_in_qty,
            (
            (total_a + total_b + total_c + total_d + total_e) / 
            IF(
            (qty_a + qty_b + qty_c + qty_d + qty_e) = 0, 
            1, 
            (qty_a + qty_b + qty_c + qty_d + qty_e)
            )
            ) AS total_in_price,
            (total_a + total_b + total_c + total_d + total_e) AS total_in_nilai,
            qty_oa,
            price_oa,
            total_oa,
            qty_ob,
            price_ob,
            total_ob,
            qty_oc,
            price_oc,
            total_oc,
            qty_od,
            price_od,
            total_od,
            qty_oe,
            price_oe,
            total_oe,
            qty_of,
            price_of,
            total_of,
            qty_og,
            price_og,
            total_og,
            qty_oh,
            price_oh,
            total_oh,
            (
            qty_oa + qty_ob + qty_oc + qty_od + qty_oe + qty_of + qty_og + qty_oh
            ) AS total_out_qty,
            (
            (total_oa + total_ob + total_oc + total_od + total_oe + total_of + total_og + total_oh) / 
            IF(
            (qty_oa + qty_ob + qty_oc + qty_od + qty_oe + qty_of + qty_og + qty_oh) = 0, 
            1, 
            (qty_oa + qty_ob + qty_oc + qty_od + qty_oe + qty_of + qty_og + qty_oh)
            )
            ) AS total_out_price,
            (total_oa + total_ob + total_oc + total_od + total_oe + total_of + total_og + total_oh) AS total_out_nilai

            from (select id_item,goods_code,itemdesc, unit, ws, SUM(COALESCE(qty_awal,0)) qty_awal, round(SUM(COALESCE(total_awal,0)) / SUM(COALESCE(qty_awal,0)),2) price_awal, SUM(COALESCE(total_awal,0)) total_awal, SUM(COALESCE(qty_a,0)) qty_a, SUM(COALESCE(price_a,0)) price_a, SUM(COALESCE(total_a,0)) total_a,SUM(COALESCE(qty_b,0)) qty_b, SUM(COALESCE(price_b,0)) price_b, SUM(COALESCE(total_b,0)) total_b,SUM(COALESCE(qty_c,0)) qty_c, SUM(COALESCE(price_c,0)) price_c, SUM(COALESCE(total_c,0)) total_c,SUM(COALESCE(qty_d,0)) qty_d, SUM(COALESCE(price_d,0)) price_d, SUM(COALESCE(total_d,0)) total_d,SUM(COALESCE(qty_e,0)) qty_e, SUM(COALESCE(price_e,0)) price_e, SUM(COALESCE(total_e,0)) total_e, SUM(COALESCE(qty_oa,0)) qty_oa, SUM(COALESCE(price_oa,0)) price_oa, SUM(COALESCE(total_oa,0)) total_oa, SUM(COALESCE(qty_ob,0)) qty_ob, SUM(COALESCE(price_ob,0)) price_ob, SUM(COALESCE(total_ob,0)) total_ob, SUM(COALESCE(qty_oc,0)) qty_oc, SUM(COALESCE(price_oc,0)) price_oc, SUM(COALESCE(total_oc,0)) total_oc, SUM(COALESCE(qty_od,0)) qty_od, SUM(COALESCE(price_od,0)) price_od, SUM(COALESCE(total_od,0)) total_od, SUM(COALESCE(qty_oe,0)) qty_oe, SUM(COALESCE(price_oe,0)) price_oe, SUM(COALESCE(total_oe,0)) total_oe, SUM(COALESCE(qty_of,0)) qty_of, SUM(COALESCE(price_of,0)) price_of, SUM(COALESCE(total_of,0)) total_of, SUM(COALESCE(qty_og,0)) qty_og, SUM(COALESCE(price_og,0)) price_og, SUM(COALESCE(total_og,0)) total_og, SUM(COALESCE(qty_oh,0)) qty_oh, SUM(COALESCE(price_oh,0)) price_oh, SUM(COALESCE(total_oh,0)) total_oh from (
            select *,0 qty, 0 total, 0 price, 0 qty_a, 0 price_a, 0 total_a, 0 qty_b, 0 price_b, 0 total_b, 0 qty_c, 0 price_c, 0 total_c, 0 qty_d, 0 price_d, 0 total_d, 0 qty_e, 0 price_e, 0 total_e,0 qty_oa, 0 price_oa, 0 total_oa, 0 qty_ob, 0 price_ob, 0 total_ob, 0 qty_oc, 0 price_oc, 0 total_oc, 0 qty_od, 0 price_od, 0 total_od, 0 qty_oe, 0 price_oe, 0 total_oe, 0 qty_of, 0 price_of, 0 total_of, 0 qty_og, 0 price_og, 0 total_og, 0 qty_oh, 0 price_oh, 0 total_oh from (select *, round(total_awal/qty_awal,2) price_awal from (select id_item,goods_code, itemdesc, unit, ws, tipe_pembelian, SUM(qty) qty_awal, SUM(COALESCE(total_non_subkon_idr,0) + COALESCE(total_nilai_barang_idr,0) + COALESCE(total_jasa_subkon_idr,0)) total_awal  from (select bpbno, bpbdate, id_item, goods_code, itemdesc, color, size, invno, jenis_dok , no_aju, tgl_aju, bcno , bcdate, supplier, pono, tipe_com, sjno, qty, qty_good, 0 qty_reject, unit, berat_bersih, remark, username, confirm_by, ws, styleno, curr, price, price_act, tipe_pembelian, price_non_subkon, nilai_barang, jasa_subkon, total_price, total_non_subkon, total_nilai_barang, total_jasa_subkon, rate, ROUND(total_non_subkon * rate,2) total_non_subkon_idr, ROUND(total_nilai_barang * rate,2) total_nilai_barang_idr, ROUND(total_jasa_subkon * rate,2) total_jasa_subkon_idr from (select *, (price_non_subkon + nilai_barang + jasa_subkon) total_price, ROUND(qty * price_non_subkon,2) total_non_subkon, ROUND(qty * nilai_barang,2) total_nilai_barang, ROUND(qty * jasa_subkon,2) total_jasa_subkon from (select no_bpb bpbno, tgl_bpb bpbdate, a.id_item, goods_code, itemdesc, color, size, '' invno, '' jenis_dok, '' no_aju, '' tgl_aju, '' bcno, '' bcdate, '' supplier, '' pono, '' tipe_com, '' sjno, sum(qty) qty, sum(qty) qty_good, unit, '' berat_bersih, '' remark, styleno, '' username, '' confirm_by, kpno ws, a.curr, price, price price_act, id_jo, type_pch tipe_pembelian, IF(type_pch like '%Subkontraktor%', 0,price) price_non_subkon, 0 nilai_barang, IF(type_pch like '%Subkontraktor%',price,0) jasa_subkon, if(rate is null, '1', rate) rate from ca_saldoawal_fabric a INNER JOIN masteritem b on b.id_item = a.id_item left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bpb and cr.curr = a.curr where a.status = 'Y' GROUP BY no_bpb, a.id_item, a.id_jo, unit) a) a) a GROUP BY tipe_pembelian, id_item, unit, ws) a) a
            UNION 
            select *,CASE WHEN tipe_pembelian = 'Pembelian Lokal' THEN qty END qty_a, CASE WHEN tipe_pembelian = 'Pembelian Lokal' THEN price END price_a, CASE WHEN tipe_pembelian = 'Pembelian Lokal' THEN total END total_a, CASE WHEN tipe_pembelian = 'Pembelian Impor' THEN qty END qty_b, CASE WHEN tipe_pembelian = 'Pembelian Impor' THEN price END price_b, CASE WHEN tipe_pembelian = 'Pembelian Impor' THEN total END total_b, CASE WHEN tipe_pembelian IN ('Pengembalian dari Subkontraktor CMT','Pengembalian dari Subkontraktor Jasa')
            THEN qty END qty_c, CASE WHEN tipe_pembelian IN ('Pengembalian dari Subkontraktor CMT','Pengembalian dari Subkontraktor Jasa')
            THEN price END price_c, CASE WHEN tipe_pembelian IN ('Pengembalian dari Subkontraktor CMT','Pengembalian dari Subkontraktor Jasa')
            THEN total END total_c, CASE WHEN tipe_pembelian = 'Pengembalian dari Produksi' THEN qty END qty_d, CASE WHEN tipe_pembelian = 'Pengembalian dari Produksi' THEN price END price_d, CASE WHEN tipe_pembelian = 'Pengembalian dari Produksi' THEN total END total_d, CASE WHEN tipe_pembelian = 'Pengembalian dari Sample Room' THEN qty END qty_e, CASE WHEN tipe_pembelian = 'Pengembalian dari Sample Room' THEN price END price_e, CASE WHEN tipe_pembelian = 'Pengembalian dari Sample Room' THEN total END total_e,0 qty_oa, 0 price_oa, 0 total_oa, 0 qty_ob, 0 price_ob, 0 total_ob, 0 qty_oc, 0 price_oc, 0 total_oc, 0 qty_od, 0 price_od, 0 total_od, 0 qty_oe, 0 price_oe, 0 total_oe, 0 qty_of, 0 price_of, 0 total_of, 0 qty_og, 0 price_og, 0 total_og, 0 qty_oh, 0 price_oh, 0 total_oh from (select *, round(total/qty,4) price from (select id_item,goods_code, itemdesc, unit, ws, tipe_pembelian,0 qty_awal, 0 total_awal, 0 price_awal, SUM(qty_good) qty, SUM(COALESCE(total_non_subkon_idr,0) + COALESCE(total_nilai_barang_idr,0) + COALESCE(total_jasa_subkon_idr,0)) total  from (select bpbno, bpbdate, id_item, goods_code, itemdesc, color, size, invno, jenis_dok , no_aju, tgl_aju, bcno , bcdate, supplier, pono, tipe_com, sjno, qty, qty_good, qty_reject, unit, berat_bersih, remark, username, confirm_by, ws, styleno, curr, price, price_act, tipe_pembelian, price_non_subkon, nilai_barang, jasa_subkon, total_price, total_non_subkon, total_nilai_barang, total_jasa_subkon, rate, ROUND(total_non_subkon * rate,4) total_non_subkon_idr, ROUND(total_nilai_barang * rate,4) total_nilai_barang_idr, ROUND(total_jasa_subkon * rate,4) total_jasa_subkon_idr from (select *, (price_non_subkon + nilai_barang + jasa_subkon) total_price, ROUND(qty * price_non_subkon,4) total_non_subkon, ROUND(qty * nilai_barang,4) total_nilai_barang, ROUND(qty * jasa_subkon,4) total_jasa_subkon from (select bpbno,bpbdate,a.id_item,goods_code,itemdesc,color,size,invno,jenis_dok,no_aju,tgl_aju, bcno, bcdate,supplier,pono,tipe_com,invno sjno, qty, qty_good,qty_reject, unit,berat_bersih, remark,username,confirm_by,tmpjo.kpno ws,tmpjo.styleno,a.curr,price, if(tipe_com ='BUYER','0',price) price_act,a.id_jo, CASE WHEN bpbno like '%RI%' and supplier like '%cutting%' then 'Pengembalian dari Produksi' WHEN bpbno like '%RI%' and supplier like '%sample%' then 'Pengembalian dari Sample Room' else type_pch end tipe_pembelian, IF(type_pch like '%Subkontraktor%', 0,price) price_non_subkon, if(a.nilai_barang is null,ri.nilai_barang,a.nilai_barang) nilai_barang, IF(type_pch like '%Subkontraktor%',price,0) jasa_subkon, if(rate is null, '1', rate) rate from (select s.id_gen,a.no_dok bpbno,a.tgl_dok bpbdate,type_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.supplier,a.no_po pono,z.tipe_com,no_invoice invno,b.id_item,goods_code, itemdesc,s.color,s.size, (b.qty_good + coalesce(b.qty_reject,0)) qty,b.qty_good as qty_good,coalesce(b.qty_reject,0) as qty_reject, b.unit,'' berat_bersih,a.deskripsi remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,b.curr,if(tipe_com ='FOC' OR tipe_com ='BUYER','0',if(price_update is null,price, price_update)) price, a.type_pch jenis_trans,'' reffno,b.id_jo,'' no_ws, a.type_pch,b.nilai_barang from whs_inmaterial_fabric a 
            inner join whs_inmaterial_fabric_det b on b.no_dok = a.no_dok
            inner join masteritem s on b.id_item=s.id_item 
            LEFT join po_header po on po.pono = a.no_po
            LEFT join po_header_draft z on z.id = po.id_draft
            where a.tgl_dok >= '2024-12-01' and a.tgl_dok < '$start_date' and b.status != 'N' and a.status != 'cancel') a
            left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo
            LEFT JOIN (select no_dok, id_jo, id_item,ROUND(total / jml,2) nilai_barang from (select no_dok, id_jo, id_item, sum(qty) jml, sum(qty * nilai_barang) total from (select no_dok,id_jo, id_item,sum(qty_aktual) qty, nilai_barang from whs_lokasi_inmaterial where no_dok like '%RI%' and nilai_barang is not null GROUP BY no_dok,id_jo,id_item,nilai_barang) a GROUP BY no_dok, id_jo, id_item) a GROUP BY no_dok, id_jo, id_item) ri on ri.no_dok = a.bpbno and ri.id_item = a.id_item and ri.id_jo = a.id_jo
            left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bpbdate and cr.curr = a.curr) a) a) a GROUP BY tipe_pembelian, id_item, unit, ws) a) a                       
            UNION                           
            select *,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
            CASE WHEN status = 'Pemakaian Produksi' THEN qty END AS qty_oa,
            CASE WHEN status = 'Pemakaian Produksi' THEN price END AS price_oa,
            CASE WHEN status = 'Pemakaian Produksi' THEN total END AS total_oa,

            CASE WHEN status IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN qty END AS qty_ob,
            CASE WHEN status IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN price END AS price_ob,
            CASE WHEN status IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN total END AS total_ob,

            CASE WHEN status = 'Retur Pembelian Lokal' THEN qty END AS qty_oc,
            CASE WHEN status = 'Retur Pembelian Lokal' THEN price END AS price_oc,
            CASE WHEN status = 'Retur Pembelian Lokal' THEN total END AS total_oc,

            CASE WHEN status = 'Retur Pembelian Impor' THEN qty END AS qty_od,
            CASE WHEN status = 'Retur Pembelian Impor' THEN price END AS price_od,
            CASE WHEN status = 'Retur Pembelian Impor' THEN total END AS total_od,

            CASE WHEN status = 'Pemakaian Sample Room' THEN qty END AS qty_oe,
            CASE WHEN status = 'Pemakaian Sample Room' THEN price END AS price_oe,
            CASE WHEN status = 'Pemakaian Sample Room' THEN total END AS total_oe,

            CASE WHEN status NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
            'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group')
            THEN qty END AS qty_of,
            CASE WHEN status NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
            'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group')
            THEN price END AS price_of,
            CASE WHEN status NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
            'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group')
            THEN total END AS total_of,
            CASE WHEN status = 'Sales Nongroup' THEN qty END AS qty_og,
            CASE WHEN status = 'Sales Nongroup' THEN price END AS price_og,
            CASE WHEN status = 'Sales Nongroup' THEN total END AS total_og,

            CASE WHEN status = 'Sales Group' THEN qty END AS qty_oh,
            CASE WHEN status = 'Sales Group' THEN price END AS price_oh,
            CASE WHEN status = 'Sales Group' THEN total END AS total_oh
            from (select *, round(total/qty,2) price from (select id_item,goods_code, itemdesc, unit, ws, status, 0 qty_awal, 0 total_awal, 0 price_awal, SUM(qty) qty, SUM(COALESCE(total_non_ro_idr,0)) total  from (select bppbno, bppbdate, a.id_item, goods_code, itemdesc, color, size, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, tujuan, qty, unit, berat_bersih, remark, username, ws, styleno, 'IDR' curr, price, status,price_non_ro, price_ro, '1' rate, round(qty * price_non_ro,2) total_non_ro, round(qty * price_ro,2) total_ro, IF(curr = 'IDR',round(qty * price_ro,2),round((qty * price_ro),2)) total_ro_idr, IF(curr = 'IDR',round(qty * price_non_ro,2),round((qty * price_non_ro),2)) total_non_ro_idr from (select bppbno, bppbdate, a.id_item, goods_code, itemdesc, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, supplier tujuan, color, size, qty, unit, berat_bersih, remark, username, ws, styleno, if(a.curr = '' OR a.curr is null,'IDR',a.curr) curr, if(bppbno like '%RO/%',b.price,a.price) price, jenis_pengeluaran status,price_non_ro, if(b.price is null,0,b.price) price_ro, IF(rate is null,1,rate) rate from (select a.no_bppb bppbno,a.no_req bppbno_req,a.tgl_bppb bppbdate,no_invoice invno,a.dok_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju tanggal_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.tujuan supplier,b.id_item,goods_code, itemdesc,s.color,s.size, sum(b.qty_out) qty,0 as qty_good,0 as qty_reject, b.satuan unit,'' berat_bersih,a.catatan remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,ac.kpno ws,ac.styleno,b.curr,b.price,br.idws_act,'' jenis_trans,CASE 
            WHEN a.jenis_pengeluaran IS NULL THEN '-'
            WHEN a.jenis_pengeluaran = 'penjualan' AND sg.supplier IS NULL THEN 'Sales Nongroup'
            WHEN a.jenis_pengeluaran = 'penjualan' AND sg.supplier IS NOT NULL THEN 'Sales Group'
            ELSE a.jenis_pengeluaran
            END AS jenis_pengeluaran, (coalesce(price_in,0) + COALESCE(nilai_barang,0)) price_non_ro, b.id_jo
            from whs_bppb_h a 
            inner join whs_bppb_det b on b.no_bppb = a.no_bppb
            inner join masteritem s on b.id_item=s.id_item 
            left join (select id_jo,id_so from jo_det group by id_jo ) tmpjod on tmpjod.id_jo=b.id_jo 
            left join (select bppbno as no_req,idws_act from bppb_req group by no_req) br on a.no_req = br.no_req 
            left join so on tmpjod.id_so=so.id 
            left join act_costing ac on so.id_cost=ac.id 
            left join (select id_supplier, supplier from ca_sales_group) sg on sg.supplier = a.tujuan
            where LEFT(a.no_bppb,2) = 'GK' and b.status != 'N' and a.status != 'cancel' and a.tgl_bppb >= '2024-12-01' and a.tgl_bppb < '$start_date' and matclass= 'FABRIC' GROUP BY b.id_jo,b.id_item,b.satuan,b.no_bppb,(coalesce(price_in,0) + COALESCE(nilai_barang,0)) order by a.no_bppb) a LEFT JOIN (select a.no_bppb, id_jo, id_item, price from whs_bppb_ro a INNER JOIN whs_bppb_h b on b.no_bppb = a.no_bppb where b.status != 'cancel' and b.tgl_bppb >= '2024-12-01' and b.tgl_bppb < '$start_date') b on b.no_bppb = a.bppbno and b.id_item = a.id_item and b.id_jo = a.id_jo
            left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bppbdate and cr.curr = a.curr) a) a  GROUP BY status, id_item, unit, ws) a) a) a GROUP BY id_item,goods_code,itemdesc, unit, ws) a) a) a GROUP BY tipe_pembelian, id_item, unit, ws) a
            UNION 
            select *,CASE WHEN tipe_pembelian = 'Pembelian Lokal' THEN qty END qty_a, CASE WHEN tipe_pembelian = 'Pembelian Lokal' THEN price END price_a, CASE WHEN tipe_pembelian = 'Pembelian Lokal' THEN total END total_a, CASE WHEN tipe_pembelian = 'Pembelian Impor' THEN qty END qty_b, CASE WHEN tipe_pembelian = 'Pembelian Impor' THEN price END price_b, CASE WHEN tipe_pembelian = 'Pembelian Impor' THEN total END total_b, CASE WHEN tipe_pembelian IN ('Pengembalian dari Subkontraktor CMT','Pengembalian dari Subkontraktor Jasa')
            THEN qty END qty_c, CASE WHEN tipe_pembelian IN ('Pengembalian dari Subkontraktor CMT','Pengembalian dari Subkontraktor Jasa')
            THEN price END price_c, CASE WHEN tipe_pembelian IN ('Pengembalian dari Subkontraktor CMT','Pengembalian dari Subkontraktor Jasa')
            THEN total END total_c, CASE WHEN tipe_pembelian = 'Pengembalian dari Produksi' THEN qty END qty_d, CASE WHEN tipe_pembelian = 'Pengembalian dari Produksi' THEN price END price_d, CASE WHEN tipe_pembelian = 'Pengembalian dari Produksi' THEN total END total_d, CASE WHEN tipe_pembelian = 'Pengembalian dari Sample Room' THEN qty END qty_e, CASE WHEN tipe_pembelian = 'Pengembalian dari Sample Room' THEN price END price_e, CASE WHEN tipe_pembelian = 'Pengembalian dari Sample Room' THEN total END total_e,0 qty_oa, 0 price_oa, 0 total_oa, 0 qty_ob, 0 price_ob, 0 total_ob, 0 qty_oc, 0 price_oc, 0 total_oc, 0 qty_od, 0 price_od, 0 total_od, 0 qty_oe, 0 price_oe, 0 total_oe, 0 qty_of, 0 price_of, 0 total_of, 0 qty_og, 0 price_og, 0 total_og, 0 qty_oh, 0 price_oh, 0 total_oh from (select *, round(total/qty,4) price from (select id_item,goods_code, itemdesc, unit, ws, tipe_pembelian,0 qty_awal, 0 total_awal, 0 price_awal, SUM(qty_good) qty, SUM(COALESCE(total_non_subkon_idr,0) + COALESCE(total_nilai_barang_idr,0) + COALESCE(total_jasa_subkon_idr,0)) total  from (select bpbno, bpbdate, id_item, goods_code, itemdesc, color, size, invno, jenis_dok , no_aju, tgl_aju, bcno , bcdate, supplier, pono, tipe_com, sjno, qty, qty_good, qty_reject, unit, berat_bersih, remark, username, confirm_by, ws, styleno, curr, price, price_act, tipe_pembelian, price_non_subkon, nilai_barang, jasa_subkon, total_price, total_non_subkon, total_nilai_barang, total_jasa_subkon, rate, ROUND(total_non_subkon * rate,4) total_non_subkon_idr, ROUND(total_nilai_barang * rate,4) total_nilai_barang_idr, ROUND(total_jasa_subkon * rate,4) total_jasa_subkon_idr from (select *, (price_non_subkon + nilai_barang + jasa_subkon) total_price, ROUND(qty * price_non_subkon,4) total_non_subkon, ROUND(qty * nilai_barang,4) total_nilai_barang, ROUND(qty * jasa_subkon,4) total_jasa_subkon from (select bpbno,bpbdate,a.id_item,goods_code,itemdesc,color,size,invno,jenis_dok,no_aju,tgl_aju, bcno, bcdate,supplier,pono,tipe_com,invno sjno, qty, qty_good,qty_reject, unit,berat_bersih, remark,username,confirm_by,tmpjo.kpno ws,tmpjo.styleno,a.curr,price, if(tipe_com ='BUYER','0',price) price_act,a.id_jo, CASE WHEN bpbno like '%RI%' and supplier like '%cutting%' then 'Pengembalian dari Produksi' WHEN bpbno like '%RI%' and supplier like '%sample%' then 'Pengembalian dari Sample Room' else type_pch end tipe_pembelian, IF(type_pch like '%Subkontraktor%', 0,price) price_non_subkon, if(a.nilai_barang is null,ri.nilai_barang,a.nilai_barang) nilai_barang, IF(type_pch like '%Subkontraktor%',price,0) jasa_subkon, if(rate is null, '1', rate) rate from (select s.id_gen,a.no_dok bpbno,a.tgl_dok bpbdate,type_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.supplier,a.no_po pono,z.tipe_com,no_invoice invno,b.id_item,goods_code, itemdesc,s.color,s.size, (b.qty_good + coalesce(b.qty_reject,0)) qty,b.qty_good as qty_good,coalesce(b.qty_reject,0) as qty_reject, b.unit,'' berat_bersih,a.deskripsi remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,b.curr,if(tipe_com ='FOC' OR tipe_com ='BUYER','0',if(price_update is null,price, price_update)) price, a.type_pch jenis_trans,'' reffno,b.id_jo,'' no_ws, a.type_pch,b.nilai_barang from whs_inmaterial_fabric a 
            inner join whs_inmaterial_fabric_det b on b.no_dok = a.no_dok
            inner join masteritem s on b.id_item=s.id_item 
            LEFT join po_header po on po.pono = a.no_po
            LEFT join po_header_draft z on z.id = po.id_draft
            where a.tgl_dok BETWEEN  '$start_date' and '$end_date' and b.status != 'N' and a.status != 'cancel') a
            left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo
            LEFT JOIN (select no_dok, id_jo, id_item,ROUND(total / jml,2) nilai_barang from (select no_dok, id_jo, id_item, sum(qty) jml, sum(qty * nilai_barang) total from (select no_dok,id_jo, id_item,sum(qty_aktual) qty, nilai_barang from whs_lokasi_inmaterial where no_dok like '%RI%' and nilai_barang is not null GROUP BY no_dok,id_jo,id_item,nilai_barang) a GROUP BY no_dok, id_jo, id_item) a GROUP BY no_dok, id_jo, id_item) ri on ri.no_dok = a.bpbno and ri.id_item = a.id_item and ri.id_jo = a.id_jo
            left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bpbdate and cr.curr = a.curr) a) a) a GROUP BY tipe_pembelian, id_item, unit, ws) a) a                       
            UNION                           
            select *,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
            CASE WHEN status = 'Pemakaian Produksi' THEN qty END AS qty_oa,
            CASE WHEN status = 'Pemakaian Produksi' THEN price END AS price_oa,
            CASE WHEN status = 'Pemakaian Produksi' THEN total END AS total_oa,

            CASE WHEN status IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN qty END AS qty_ob,
            CASE WHEN status IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN price END AS price_ob,
            CASE WHEN status IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN total END AS total_ob,

            CASE WHEN status = 'Retur Pembelian Lokal' THEN qty END AS qty_oc,
            CASE WHEN status = 'Retur Pembelian Lokal' THEN price END AS price_oc,
            CASE WHEN status = 'Retur Pembelian Lokal' THEN total END AS total_oc,

            CASE WHEN status = 'Retur Pembelian Impor' THEN qty END AS qty_od,
            CASE WHEN status = 'Retur Pembelian Impor' THEN price END AS price_od,
            CASE WHEN status = 'Retur Pembelian Impor' THEN total END AS total_od,

            CASE WHEN status = 'Pemakaian Sample Room' THEN qty END AS qty_oe,
            CASE WHEN status = 'Pemakaian Sample Room' THEN price END AS price_oe,
            CASE WHEN status = 'Pemakaian Sample Room' THEN total END AS total_oe,

            CASE WHEN status NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
            'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group')
            THEN qty END AS qty_of,
            CASE WHEN status NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
            'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group')
            THEN price END AS price_of,
            CASE WHEN status NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
            'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group')
            THEN total END AS total_of,
            CASE WHEN status = 'Sales Nongroup' THEN qty END AS qty_og,
            CASE WHEN status = 'Sales Nongroup' THEN price END AS price_og,
            CASE WHEN status = 'Sales Nongroup' THEN total END AS total_og,

            CASE WHEN status = 'Sales Group' THEN qty END AS qty_oh,
            CASE WHEN status = 'Sales Group' THEN price END AS price_oh,
            CASE WHEN status = 'Sales Group' THEN total END AS total_oh
            from (select *, round(total/qty,2) price from (select id_item,goods_code, itemdesc, unit, ws, status, 0 qty_awal, 0 total_awal, 0 price_awal, SUM(qty) qty, SUM(COALESCE(total_non_ro_idr,0)) total  from (select bppbno, bppbdate, a.id_item, goods_code, itemdesc, color, size, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, tujuan, qty, unit, berat_bersih, remark, username, ws, styleno, 'IDR' curr, price, status,price_non_ro, price_ro, '1' rate, round(qty * price_non_ro,2) total_non_ro, round(qty * price_ro,2) total_ro, IF(curr = 'IDR',round(qty * price_ro,2),round((qty * price_ro),2)) total_ro_idr, IF(curr = 'IDR',round(qty * price_non_ro,2),round((qty * price_non_ro),2)) total_non_ro_idr from (select bppbno, bppbdate, a.id_item, goods_code, itemdesc, invno, jenis_dok, no_aju, tanggal_aju, bcno, bcdate, supplier tujuan, color, size, qty, unit, berat_bersih, remark, username, ws, styleno, if(a.curr = '' OR a.curr is null,'IDR',a.curr) curr, if(bppbno like '%RO/%',b.price,a.price) price, jenis_pengeluaran status,price_non_ro, if(b.price is null,0,b.price) price_ro, IF(rate is null,1,rate) rate from (select a.no_bppb bppbno,a.no_req bppbno_req,a.tgl_bppb bppbdate,no_invoice invno,a.dok_bc jenis_dok,right(no_aju,6) no_aju,tgl_aju tanggal_aju, lpad(no_daftar,6,'0') bcno,tgl_daftar bcdate,a.tujuan supplier,b.id_item,goods_code, itemdesc,s.color,s.size, sum(b.qty_out) qty,0 as qty_good,0 as qty_reject, b.satuan unit,'' berat_bersih,a.catatan remark,CONCAT(a.created_by,' (',a.created_at, ') ') username,CONCAT(a.approved_by,' (',a.approved_date, ') ') confirm_by,ac.kpno ws,ac.styleno,b.curr,b.price,br.idws_act,'' jenis_trans,CASE 
            WHEN a.jenis_pengeluaran IS NULL THEN '-'
            WHEN a.jenis_pengeluaran = 'penjualan' AND sg.supplier IS NULL THEN 'Sales Nongroup'
            WHEN a.jenis_pengeluaran = 'penjualan' AND sg.supplier IS NOT NULL THEN 'Sales Group'
            ELSE a.jenis_pengeluaran
            END AS jenis_pengeluaran, (coalesce(price_in,0) + COALESCE(nilai_barang,0)) price_non_ro, b.id_jo
            from whs_bppb_h a 
            inner join whs_bppb_det b on b.no_bppb = a.no_bppb
            inner join masteritem s on b.id_item=s.id_item 
            left join (select id_jo,id_so from jo_det group by id_jo ) tmpjod on tmpjod.id_jo=b.id_jo 
            left join (select bppbno as no_req,idws_act from bppb_req group by no_req) br on a.no_req = br.no_req 
            left join so on tmpjod.id_so=so.id 
            left join act_costing ac on so.id_cost=ac.id 
            left join (select id_supplier, supplier from ca_sales_group) sg on sg.supplier = a.tujuan
            where LEFT(a.no_bppb,2) = 'GK' and b.status != 'N' and a.status != 'cancel' and a.tgl_bppb BETWEEN  '$start_date' and '$end_date' and matclass= 'FABRIC' GROUP BY b.id_jo,b.id_item,b.satuan,b.no_bppb,(coalesce(price_in,0) + COALESCE(nilai_barang,0)) order by a.no_bppb) a LEFT JOIN (select a.no_bppb, id_jo, id_item, price from whs_bppb_ro a INNER JOIN whs_bppb_h b on b.no_bppb = a.no_bppb where b.status != 'cancel' and b.tgl_bppb BETWEEN  '$start_date' and '$end_date') b on b.no_bppb = a.bppbno and b.id_item = a.id_item and b.id_jo = a.id_jo
            left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.bppbdate and cr.curr = a.curr) a) a  GROUP BY status, id_item, unit, ws) a) a) a GROUP BY id_item,goods_code,itemdesc, unit, ws) a) a) a left join (select id_item item_adj, unit unit_adj, no_ws ws_adj, qty qty_adj, price price_adj, total total_adj from tbl_adjust_fabric_sum where tgl_adjust BETWEEN  '$start_date' and '$end_date') b on b.item_adj = a.id_item and b.ws_adj = a.ws left join (select id_item item_adj_bfr, unit unit_adj_bfr, no_ws ws_adj_bfr, qty qty_adj_bfr, price price_adj_bfr, total total_adj_bfr from tbl_adjust_fabric_sum where tgl_adjust < '$start_date') c on c.item_adj_bfr = a.id_item and c.ws_adj_bfr = a.ws");


$total_sal_qty =0;
$total_sal_nilai =0;
$total_sal_price =0;
$no = 0;
while($row2 = mysqli_fetch_array($sql)){
    $no++;
    $total_sal_qty = round($row2['qty_awal'],2) + round($row2['qty_adj_bfr'],2);
    if ($total_sal_qty == 0) {
        $total_sal_qty = 0;
    }else{
        $total_sal_qty = $total_sal_qty; 
    }
    // $total_sal_nilai = round($row2['total_awal'],2);
    // $total_sal_price = round($total_sal_price / $total_sal_qty ,2);
    $total_sal_nilai = round($row2['total_awal'],2) + round($row2['total_adj_bfr'],2);
    // $total_sal_price = round($total_sal_nilai / $total_sal_qty ,2); 
    $total_sal_price = ($total_sal_qty != 0) ? round($total_sal_nilai / $total_sal_qty,2) : 0;
    $total_in_qty = $row2['qty_a'] + $row2['qty_b'] + $row2['qty_c'] + $row2['qty_d'] + $row2['qty_e'];
    $total_in_nilai = $row2['total_a'] + $row2['total_b'] + $row2['total_c'] + $row2['total_d'] + $row2['total_e'];
    $qty_adj = round($row2['qty_adj'],2);
    $total_adj = round($row2['total_adj'],2);
    if ($qty_adj == 0 && $total_adj == 0) {
        $qty_adj = 0;
    }else{
        $qty_adj = $qty_adj; 
    }
    $price_adj = ($qty_adj != 0) ? ($total_adj / $qty_adj) : 0;

    if ($total_in_qty == 0) {
        $total_inqty = 1;
    }else{
        $total_inqty = $total_in_qty; 
    }
    $total_in_price = $total_in_nilai / $total_inqty;

    $total_out_qty = $row2['qty_oa'] + $row2['qty_ob'] + $row2['qty_oc'] + $row2['qty_od'] + $row2['qty_oe'] + $row2['qty_of'] + $row2['qty_og'] + $row2['qty_oh'];
    $total_out_nilai = $row2['total_oa'] + $row2['total_ob'] + $row2['total_oc'] + $row2['total_od'] + $row2['total_oe'] + $row2['total_of'] + $row2['total_og'] + $row2['total_oh'];
    if ($total_out_qty == 0) {
        $total_outqty = 1;
    }else{
        $total_outqty = $total_out_qty; 
    }
    $total_out_price = $total_out_nilai / $total_outqty;

    $total_end_qty = $total_sal_qty + $total_in_qty - $total_out_qty + $qty_adj;
    $nilai_all = $total_sal_nilai + $total_in_nilai - $total_out_nilai + $total_adj;
    $qty_all = $total_sal_qty + $total_in_qty - $total_out_qty;
    if ($qty_all == 0) {
        $qtyall = 1;
    }else{
        $qtyall = $qty_all;
    }
    $total_end_price = $nilai_all / $qtyall;
    $total_end_nilai = $total_end_qty * $total_end_price;

    echo ' <tr style="font-size:12px;text-align:center;">
    <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
    <td style="text-align : left;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
    <td style="text-align : left;" value = "'.$row2['goods_code'].'">'.$row2['goods_code'].'</td>
    <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
    <td style="text-align : left;" value = "'.$row2['unit'].'">'.$row2['unit'].'</td>
    <td style="text-align : left;" value = "'.$row2['ws'].'">'.$row2['ws'].'</td>
    <td style="text-align : right;" value = "'.$total_sal_qty.'">'.number_format($total_sal_qty,2).'</td>
    <td style="text-align : right;" value = "'.$total_sal_price.'">'.number_format($total_sal_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_sal_nilai.'">'.number_format($total_sal_nilai,2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_a'].'">'.number_format($row2['qty_a'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_a'].'">'.number_format($row2['price_a'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_a'].'">'.number_format($row2['total_a'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_b'].'">'.number_format($row2['qty_b'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_b'].'">'.number_format($row2['price_b'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_b'].'">'.number_format($row2['total_b'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_c'].'">'.number_format($row2['qty_c'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_c'].'">'.number_format($row2['price_c'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_c'].'">'.number_format($row2['total_c'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_d'].'">'.number_format($row2['qty_d'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_d'].'">'.number_format($row2['price_d'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_d'].'">'.number_format($row2['total_d'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_e'].'">'.number_format($row2['qty_e'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_e'].'">'.number_format($row2['price_e'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_e'].'">'.number_format($row2['total_e'],2).'</td>
    <td style="text-align : right;" value = "'.$total_in_qty.'">'.number_format($total_in_qty,2).'</td>
    <td style="text-align : right;" value = "'.$total_in_price.'">'.number_format($total_in_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_in_nilai.'">'.number_format($total_in_nilai,2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_oa'].'">'.number_format($row2['qty_oa'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_oa'].'">'.number_format($row2['price_oa'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_oa'].'">'.number_format($row2['total_oa'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_ob'].'">'.number_format($row2['qty_ob'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_ob'].'">'.number_format($row2['price_ob'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_ob'].'">'.number_format($row2['total_ob'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_oc'].'">'.number_format($row2['qty_oc'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_oc'].'">'.number_format($row2['price_oc'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_oc'].'">'.number_format($row2['total_oc'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_od'].'">'.number_format($row2['qty_od'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_od'].'">'.number_format($row2['price_od'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_od'].'">'.number_format($row2['total_od'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_oe'].'">'.number_format($row2['qty_oe'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_oe'].'">'.number_format($row2['price_oe'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_oe'].'">'.number_format($row2['total_oe'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_of'].'">'.number_format($row2['qty_og'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_of'].'">'.number_format($row2['price_og'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_of'].'">'.number_format($row2['total_og'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_of'].'">'.number_format($row2['qty_oh'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_of'].'">'.number_format($row2['price_oh'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_of'].'">'.number_format($row2['total_oh'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['qty_of'].'">'.number_format($row2['qty_of'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['price_of'].'">'.number_format($row2['price_of'],2).'</td>
    <td style="text-align : right;" value = "'.$row2['total_of'].'">'.number_format($row2['total_of'],2).'</td>
    <td style="text-align : right;" value = "'.$total_out_qty.'">'.number_format($total_out_qty,2).'</td>
    <td style="text-align : right;" value = "'.$total_out_price.'">'.number_format($total_out_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_out_nilai.'">'.number_format($total_out_nilai,2).'</td>
    <td style="text-align : right;" value = "'.$qty_adj.'">'.number_format($qty_adj,2).'</td>
    <td style="text-align : right;" value = "'.$price_adj.'">'.number_format($price_adj,2).'</td>
    <td style="text-align : right;" value = "'.$total_adj.'">'.number_format($total_adj,2).'</td>
    <td style="text-align : right;" value = "'.$total_end_qty.'">'.number_format($total_end_qty,2).'</td>';

    // if ($total_end_qty == 0) {
    //     $total_end_price = 0;
    //     $total_end_nilai = 0;
    // }

    echo '<td style="text-align : right;" value = "'.$total_end_price.'">'.number_format($total_end_price,2).'</td>
    <td style="text-align : right;" value = "'.$total_end_nilai.'">'.number_format($total_end_nilai,2).'</td>

    </tr>
    ';

    ?>
    <?php 

}
?>
</table>

</body>
</html>




