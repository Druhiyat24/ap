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
    header("Content-Disposition: attachment; filename=fabric transaction Barcode out.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> FABRIC MUTASI BARCODE<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Barcode</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Barcode Mapping</th>
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
saldo_awal as (select a.no_barcode, IFNULL(map.no_barcode,a.no_barcode) barcode_mapping, id_jo, a.id_item, b.goods_code, b.itemdesc, satuan, ws, price, rate, ROUND(sum(qty),4) saldo_awal_qty, ROUND(IF(qty > 0,(price * rate)/count(a.no_barcode),0),4) saldo_awal_price, (qty * (price * rate)) saldo_awal_total from whs_saldo_awal_nilai_persediaan a INNER JOIN masteritem b on b.id_item = a.id_item LEFT JOIN (select idbpb_det, no_barcode from whs_mut_lokasi a INNER JOIN whs_lokasi_inmaterial b on b.no_barcode_old = a.idbpb_det where a.status = 'Y' GROUP BY no_barcode) map on map.idbpb_det = a.no_barcode where tgl_periode = (SELECT MAX(tgl_periode) FROM whs_saldo_awal_nilai_persediaan WHERE tgl_periode <= '$start_date') GROUP BY a.no_barcode),

trx_in AS (select b.no_barcode, IFNULL(map.no_barcode,b.no_barcode) barcode_mapping, b.id_jo, b.id_item, mi.goods_code, mi.itemdesc, b.satuan, kpno no_ws, type_pch, qty_sj, COALESCE(IFNULL(np_curr_rev,np_curr),'-') curr, ROUND(COALESCE(IFNULL(np_price_rev,np_price),0),4) price, (qty_sj * (COALESCE(IFNULL(np_price_rev,np_price),0))) total_price, np_tgl_in, IFNULL(rate,1) rate from whs_inmaterial_fabric a INNER JOIN whs_lokasi_inmaterial b on b.no_dok = a.no_dok INNER JOIN masteritem mi on mi.id_item = b.id_item INNER JOIN (select id_jo, kpno, styleno from act_costing ac inner join so on ac.id = so.id_cost inner join jo_det jod on so.id = jod.id_so group by id_jo) tmpjo on tmpjo.id_jo = b.id_jo LEFT JOIN (select tanggal, curr curr_rate, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.np_tgl_in and cr.curr_rate = COALESCE(IFNULL(b.np_curr_rev,b.np_curr),'-') LEFT JOIN (select idbpb_det, no_barcode from whs_mut_lokasi a INNER JOIN whs_lokasi_inmaterial b on b.no_barcode_old = a.idbpb_det where a.status = 'Y' GROUP BY no_barcode) map on map.idbpb_det = b.no_barcode where a.tgl_dok BETWEEN '$start_date' and '$end_date' and b.status = 'Y'),

trx_out AS (select id_roll, id_jo, id_item, jenis_pengeluaran type_pch, sum(COALESCE(qty_out,0)) qty_sj, COALESCE(IFNULL(np_curr_rev,np_curr),'-') curr, ROUND(COALESCE(IFNULL(np_price_rev,np_price),0),4) price, (qty_out * (COALESCE(IFNULL(np_price_rev,np_price),0))) total_price, np_tgl_in, IFNULL(rate,1) rate from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb LEFT JOIN (select tanggal, curr curr_rate, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = b.np_tgl_in and cr.curr_rate = COALESCE(IFNULL(b.np_curr_rev,b.np_curr),'-') where tgl_bppb BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.status = 'Y' GROUP BY id_roll
),

trx_in_detail as (SELECT 
    no_barcode, barcode_mapping, id_jo, id_item, goods_code, itemdesc, satuan, no_ws,
    -- Pembelian Lokal
    SUM(CASE WHEN type_pch='Pembelian Lokal' THEN qty_sj ELSE 0 END) AS in_lokal_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Pembelian Lokal' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Pembelian Lokal' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Pembelian Lokal' THEN 1 END),4)
        ELSE 0
    END AS in_lokal_price,
    SUM(CASE WHEN type_pch='Pembelian Lokal' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_lokal_total,
        
        -- Pembelian Impor
    SUM(CASE WHEN type_pch='Pembelian Impor' THEN qty_sj ELSE 0 END) AS in_impor_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Pembelian Impor' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Pembelian Impor' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Pembelian Impor' THEN 1 END),4)
        ELSE 0
    END AS in_impor_price,
    SUM(CASE WHEN type_pch='Pembelian Impor' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_impor_total,
        
        -- Pengembalian dari Subkontraktor Jasa
    SUM(CASE WHEN type_pch='Pengembalian dari Subkontraktor Jasa' THEN qty_sj ELSE 0 END) AS in_subcont_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Pengembalian dari Subkontraktor Jasa' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Pengembalian dari Subkontraktor Jasa' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Pengembalian dari Subkontraktor Jasa' THEN 1 END),4)
        ELSE 0
    END AS in_subcont_price,
    SUM(CASE WHEN type_pch='Pengembalian dari Subkontraktor Jasa' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_subcont_total,
        
        -- Pengembalian dari Produksi
    SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN qty_sj ELSE 0 END) AS in_produksi_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Pengembalian dari Produksi' THEN 1 END),4)
        ELSE 0
    END AS in_produksi_price,
    SUM(CASE WHEN type_pch='Pengembalian dari Produksi' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_produksi_total,
        
        -- Pengembalian dari Sample Room
    SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN qty_sj ELSE 0 END) AS in_sample_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN 1 END),4)
        ELSE 0
    END AS in_sample_price,
    SUM(CASE WHEN type_pch='Pengembalian dari Sample Room' THEN ROUND(total_price * rate,4) ELSE 0 END) AS in_sample_total
FROM trx_in GROUP BY no_barcode),

trx_in_fix as (select *, (in_lokal_qty + in_impor_qty + in_subcont_qty + in_produksi_qty + in_sample_qty) jumlah_in_qty, ROUND((in_lokal_total + in_impor_total + in_subcont_total + in_produksi_total + in_sample_total) / (in_lokal_qty + in_impor_qty + in_subcont_qty + in_produksi_qty + in_sample_qty),4) jumlah_in_price, (in_lokal_total + in_impor_total + in_subcont_total + in_produksi_total + in_sample_total) jumlah_in_total from trx_in_detail),

trx_out_detail as (SELECT 
    id_roll, id_jo, id_item,
    -- Pemakaian produksi
    SUM(CASE WHEN type_pch='Pemakaian Produksi' THEN qty_sj ELSE 0 END) AS out_prod_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Pemakaian Produksi' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Pemakaian Produksi' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Pemakaian Produksi' THEN 1 END),4)
        ELSE 0
    END AS out_prod_price,
    SUM(CASE WHEN type_pch='Pembelian Lokal' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_prod_total,
        
        -- Jasa Subcont
    SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN qty_sj ELSE 0 END) AS out_subcont_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN 1 END),4)
        ELSE 0
    END AS out_subcont_price,
    SUM(CASE WHEN type_pch IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa') THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_subcont_total,
        
        -- Retur Pembelian Lokal
    SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN qty_sj ELSE 0 END) AS out_lokal_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN 1 END),4)
        ELSE 0
    END AS out_lokal_price,
    SUM(CASE WHEN type_pch = 'Retur Pembelian Lokal' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_lokal_total,
        
        -- Retur Pembelian Import
    SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN qty_sj ELSE 0 END) AS out_impor_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Retur Pembelian Impor' THEN 1 END),4)
        ELSE 0
    END AS out_impor_price,
    SUM(CASE WHEN type_pch='Retur Pembelian Impor' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_impor_total,
        
        -- Pemakaian Sample Room
    SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN qty_sj ELSE 0 END) AS out_sample_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Pemakaian Sample Room' THEN 1 END),4)
        ELSE 0
    END AS out_sample_price,
    SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_sample_total,
        -- Sales Nongroup
    SUM(CASE WHEN type_pch='Pemakaian Sample Room' THEN qty_sj ELSE 0 END) AS out_salnongroup_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Sales Nongroup' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Sales Nongroup' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Sales Nongroup' THEN 1 END),4)
        ELSE 0
    END AS out_salnongroup_price,
    SUM(CASE WHEN type_pch='Sales Nongroup' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_salnongroup_total,
        -- Sales Group
    SUM(CASE WHEN type_pch='Sales Group' THEN qty_sj ELSE 0 END) AS out_salgroup_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch='Sales Group' THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch='Sales Group' THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch='Sales Nongroup' THEN 1 END),4)
        ELSE 0
    END AS out_salgroup_price,
    SUM(CASE WHEN type_pch='Sales Nongroup' THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_salgroup_total,
        -- Other
    SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
                'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN qty_sj ELSE 0 END) AS out_other_qty,
    CASE 
        WHEN SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
                'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN qty_sj ELSE 0 END) > 0
        THEN ROUND(SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
                'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN (price * rate) ELSE 0 END)
             / COUNT(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
                'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN 1 END),4)
        ELSE 0
    END AS out_other_price,
    SUM(CASE WHEN type_pch NOT IN ('Pengiriman ke Subkontraktor CMT', 'Pengiriman ke Subkontraktor Jasa', 
                'Retur Pembelian Lokal', 'Pemakaian Produksi', 'Retur Pembelian Impor', 'Pemakaian Sample Room','Sales Nongroup', 'Sales Group') THEN ROUND(total_price * rate,4) ELSE 0 END) AS out_other_total
FROM trx_out GROUP BY id_roll),

trx_out_fix as (select *, (out_prod_qty + out_subcont_qty + out_lokal_qty + out_impor_qty + out_sample_qty + out_salnongroup_qty + out_salgroup_qty + out_other_qty) jumlah_out_qty, ROUND((out_prod_total + out_subcont_total + out_lokal_total + out_impor_total + out_sample_total + out_salnongroup_total + out_salgroup_total + out_other_total) / (out_prod_qty + out_subcont_qty + out_lokal_qty + out_impor_qty + out_sample_qty + out_salnongroup_qty + out_salgroup_qty + out_other_qty),4) jumlah_out_price, (out_prod_total + out_subcont_total + out_lokal_total + out_impor_total + out_sample_total + out_salnongroup_total + out_salgroup_total + out_other_total) jumlah_out_total from trx_out_detail),

pemasukan as (select a.no_barcode, a.barcode_mapping, a.id_jo, a.id_item, a.goods_code, a.itemdesc, a.satuan, a.ws no_ws, COALESCE(saldo_awal_qty,0) saldo_awal_qty, COALESCE(saldo_awal_price,0) saldo_awal_price, COALESCE(saldo_awal_total,0) saldo_awal_total, COALESCE(in_lokal_qty,0) in_lokal_qty, COALESCE(in_lokal_price,0) in_lokal_price, COALESCE(in_lokal_total,0) in_lokal_total, COALESCE(in_impor_qty,0) in_impor_qty, COALESCE(in_impor_price,0) in_impor_price, COALESCE(in_impor_total,0) in_impor_total, COALESCE(in_subcont_qty,0) in_subcont_qty, COALESCE(in_subcont_price,0) in_subcont_price, COALESCE(in_subcont_total,0) in_subcont_total, COALESCE(in_produksi_qty,0) in_produksi_qty, COALESCE(in_produksi_price,0) in_produksi_price, COALESCE(in_produksi_total,0) in_produksi_total, COALESCE(in_sample_qty,0) in_sample_qty, COALESCE(in_sample_price,0) in_sample_price, COALESCE(in_sample_total,0) in_sample_total, COALESCE(jumlah_in_qty,0) jumlah_in_qty, COALESCE(jumlah_in_price,0) jumlah_in_price, COALESCE(jumlah_in_total,0) jumlah_in_total from saldo_awal a left join trx_in_fix b on b.no_barcode = a.no_barcode
UNION
select a.no_barcode, a.barcode_mapping, a.id_jo, a.id_item, a.goods_code, a.itemdesc, a.satuan, a.no_ws, COALESCE(saldo_awal_qty,0) saldo_awal_qty, COALESCE(saldo_awal_price,0) saldo_awal_price, COALESCE(saldo_awal_total,0) saldo_awal_total, COALESCE(in_lokal_qty,0) in_lokal_qty, COALESCE(in_lokal_price,0) in_lokal_price, COALESCE(in_lokal_total,0) in_lokal_total, COALESCE(in_impor_qty,0) in_impor_qty, COALESCE(in_impor_price,0) in_impor_price, COALESCE(in_impor_total,0) in_impor_total, COALESCE(in_subcont_qty,0) in_subcont_qty, COALESCE(in_subcont_price,0) in_subcont_price, COALESCE(in_subcont_total,0) in_subcont_total, COALESCE(in_produksi_qty,0) in_produksi_qty, COALESCE(in_produksi_price,0) in_produksi_price, COALESCE(in_produksi_total,0) in_produksi_total, COALESCE(in_sample_qty,0) in_sample_qty, COALESCE(in_sample_price,0) in_sample_price, COALESCE(in_sample_total,0) in_sample_total, COALESCE(jumlah_in_qty,0) jumlah_in_qty, COALESCE(jumlah_in_price,0) jumlah_in_price, COALESCE(jumlah_in_total,0) jumlah_in_total from trx_in_fix a left join saldo_awal b on b.no_barcode = a.no_barcode where b.no_barcode IS NULL), 

Pemasukan_fix as (SELECT 
    no_barcode,
        barcode_mapping,
    id_jo,
    id_item,
    goods_code,
    itemdesc,
    satuan,
    no_ws,

    -- SALDO AWAL
    COALESCE(SUM(saldo_awal_qty),0) AS saldo_awal_qty,
    COALESCE(SUM(saldo_awal_total),0) AS saldo_awal_total,
    IF(SUM(saldo_awal_qty)=0, 0, SUM(saldo_awal_total)/SUM(saldo_awal_qty)) AS saldo_awal_price,

    -- IN LOKAL
    COALESCE(SUM(in_lokal_qty),0) AS in_lokal_qty,
    COALESCE(SUM(in_lokal_total),0) AS in_lokal_total,
    IF(SUM(in_lokal_qty)=0, 0, SUM(in_lokal_total)/SUM(in_lokal_qty)) AS in_lokal_price,

    -- IN IMPOR
    COALESCE(SUM(in_impor_qty),0) AS in_impor_qty,
    COALESCE(SUM(in_impor_total),0) AS in_impor_total,
    IF(SUM(in_impor_qty)=0, 0, SUM(in_impor_total)/SUM(in_impor_qty)) AS in_impor_price,

    -- IN SUBCONT
    COALESCE(SUM(in_subcont_qty),0) AS in_subcont_qty,
    COALESCE(SUM(in_subcont_total),0) AS in_subcont_total,
    IF(SUM(in_subcont_qty)=0, 0, SUM(in_subcont_total)/SUM(in_subcont_qty)) AS in_subcont_price,

    -- IN PRODUKSI
    COALESCE(SUM(in_produksi_qty),0) AS in_produksi_qty,
    COALESCE(SUM(in_produksi_total),0) AS in_produksi_total,
    IF(SUM(in_produksi_qty)=0, 0, SUM(in_produksi_total)/SUM(in_produksi_qty)) AS in_produksi_price,

    -- IN SAMPLE
    COALESCE(SUM(in_sample_qty),0) AS in_sample_qty,
    COALESCE(SUM(in_sample_total),0) AS in_sample_total,
    IF(SUM(in_sample_qty)=0, 0, SUM(in_sample_total)/SUM(in_sample_qty)) AS in_sample_price,

    -- TOTAL IN
    COALESCE(SUM(jumlah_in_qty),0) AS jumlah_in_qty,
    COALESCE(SUM(jumlah_in_total),0) AS jumlah_in_total,
    IF(SUM(jumlah_in_qty)=0, 0, SUM(jumlah_in_total)/SUM(jumlah_in_qty)) AS jumlah_in_price

FROM pemasukan
GROUP BY no_barcode),

pengeluaran_fix as (SELECT 
    id_roll,
    id_jo,
    id_item,

    -- OUT PRODUKSI
    COALESCE(SUM(out_prod_qty),0) AS out_prod_qty,
    COALESCE(SUM(out_prod_total),0) AS out_prod_total,
    IF(SUM(out_prod_qty)=0, 0, SUM(out_prod_total)/SUM(out_prod_qty)) AS out_prod_price,

    -- OUT SUBCONT
    COALESCE(SUM(out_subcont_qty),0) AS out_subcont_qty,
    COALESCE(SUM(out_subcont_total),0) AS out_subcont_total,
    IF(SUM(out_subcont_qty)=0, 0, SUM(out_subcont_total)/SUM(out_subcont_qty)) AS out_subcont_price,

    -- OUT LOKAL
    COALESCE(SUM(out_lokal_qty),0) AS out_lokal_qty,
    COALESCE(SUM(out_lokal_total),0) AS out_lokal_total,
    IF(SUM(out_lokal_qty)=0, 0, SUM(out_lokal_total)/SUM(out_lokal_qty)) AS out_lokal_price,

    -- OUT IMPOR
    COALESCE(SUM(out_impor_qty),0) AS out_impor_qty,
    COALESCE(SUM(out_impor_total),0) AS out_impor_total,
    IF(SUM(out_impor_qty)=0, 0, SUM(out_impor_total)/SUM(out_impor_qty)) AS out_impor_price,

    -- OUT SAMPLE
    COALESCE(SUM(out_sample_qty),0) AS out_sample_qty,
    COALESCE(SUM(out_sample_total),0) AS out_sample_total,
    IF(SUM(out_sample_qty)=0, 0, SUM(out_sample_total)/SUM(out_sample_qty)) AS out_sample_price,

    -- OUT SAL NON GROUP
    COALESCE(SUM(out_salnongroup_qty),0) AS out_salnongroup_qty,
    COALESCE(SUM(out_salnongroup_total),0) AS out_salnongroup_total,
    IF(SUM(out_salnongroup_qty)=0, 0, SUM(out_salnongroup_total)/SUM(out_salnongroup_qty)) AS out_salnongroup_price,

    -- OUT SAL GROUP
    COALESCE(SUM(out_salgroup_qty),0) AS out_salgroup_qty,
    COALESCE(SUM(out_salgroup_total),0) AS out_salgroup_total,
    IF(SUM(out_salgroup_qty)=0, 0, SUM(out_salgroup_total)/SUM(out_salgroup_qty)) AS out_salgroup_price,

    -- OUT OTHER
    COALESCE(SUM(out_other_qty),0) AS out_other_qty,
    COALESCE(SUM(out_other_total),0) AS out_other_total,
    IF(SUM(out_other_qty)=0, 0, SUM(out_other_total)/SUM(out_other_qty)) AS out_other_price,

    -- TOTAL OUT
    COALESCE(SUM(jumlah_out_qty),0) AS jumlah_out_qty,
    COALESCE(SUM(jumlah_out_total),0) AS jumlah_out_total,
    IF(SUM(jumlah_out_qty)=0, 0, SUM(jumlah_out_total)/SUM(jumlah_out_qty)) AS jumlah_out_price

FROM trx_out_fix
GROUP BY id_roll),

mutasi as (select a.*, COALESCE(out_prod_qty,0) out_prod_qty,   COALESCE(out_prod_total,0) out_prod_total,   COALESCE(out_prod_price,0) out_prod_price,   COALESCE(out_subcont_qty,0) out_subcont_qty,   COALESCE(out_subcont_total,0) out_subcont_total,   COALESCE(out_subcont_price,0) out_subcont_price,   COALESCE(out_lokal_qty,0) out_lokal_qty,   COALESCE(out_lokal_total,0) out_lokal_total,   COALESCE(out_lokal_price,0) out_lokal_price,   COALESCE(out_impor_qty,0) out_impor_qty,   COALESCE(out_impor_total,0) out_impor_total,   COALESCE(out_impor_price,0) out_impor_price,   COALESCE(out_sample_qty,0) out_sample_qty,   COALESCE(out_sample_total,0) out_sample_total,   COALESCE(out_sample_price,0) out_sample_price,   COALESCE(out_salnongroup_qty,0) out_salnongroup_qty,   COALESCE(out_salnongroup_total,0) out_salnongroup_total,   COALESCE(out_salnongroup_price,0) out_salnongroup_price,   COALESCE(out_salgroup_qty,0) out_salgroup_qty,   COALESCE(out_salgroup_total,0) out_salgroup_total,   COALESCE(out_salgroup_price,0) out_salgroup_price,   COALESCE(out_other_qty,0) out_other_qty,   COALESCE(out_other_total,0) out_other_total,   COALESCE(out_other_price,0) out_other_price,   COALESCE(jumlah_out_qty,0) jumlah_out_qty,   COALESCE(jumlah_out_total,0) jumlah_out_total,   COALESCE(jumlah_out_price,0) jumlah_out_price from pemasukan_fix a left join pengeluaran_fix b on b.id_roll = a.barcode_mapping)

select *, (saldo_awal_qty + jumlah_in_qty - jumlah_out_qty) saldo_akhir_qty, (saldo_awal_total + jumlah_in_total - jumlah_out_total) saldo_akhir_total, ((saldo_awal_total + jumlah_in_total - jumlah_out_total) / (saldo_awal_qty + jumlah_in_qty - jumlah_out_qty)) saldo_akhir_price from mutasi");


        $total_qty =0;
        $total_price_non_ro =0;
        $total_total_non_ro =0;
        $sum_total_ro_nonro_idr =0;
        $no = 0;
        while($row2 = mysqli_fetch_array($sql)){
            $no++;

            echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
            <td style="text-align : left;" value = "'.$row2['no_barcode'].'">'.$row2['no_barcode'].'</td>
            <td style="text-align : left;" value = "'.$row2['barcode_mapping'].'">'.$row2['barcode_mapping'].'</td>
                <td style="width: 100px;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
                <td style="text-align : left;" value = "'.$row2['goods_code'].'">'.$row2['goods_code'].'</td>
                <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
                <td style="text-align : left;" value = "'.$row2['satuan'].'">'.$row2['satuan'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_ws'].'">'.$row2['no_ws'].'</td>
                <td style="text-align : right;" value = "'.$row2['saldo_awal_qty'].'">'.number_format($row2['saldo_awal_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['saldo_awal_price'].'">'.number_format($row2['saldo_awal_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['saldo_awal_total'].'">'.number_format($row2['saldo_awal_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_lokal_qty'].'">'.number_format($row2['in_lokal_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_lokal_price'].'">'.number_format($row2['in_lokal_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_lokal_total'].'">'.number_format($row2['in_lokal_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_impor_qty'].'">'.number_format($row2['in_impor_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_impor_price'].'">'.number_format($row2['in_impor_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_impor_total'].'">'.number_format($row2['in_impor_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_subcont_qty'].'">'.number_format($row2['in_subcont_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_subcont_price'].'">'.number_format($row2['in_subcont_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_subcont_total'].'">'.number_format($row2['in_subcont_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_produksi_qty'].'">'.number_format($row2['in_produksi_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_produksi_price'].'">'.number_format($row2['in_produksi_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_produksi_total'].'">'.number_format($row2['in_produksi_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_sample_qty'].'">'.number_format($row2['in_sample_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_sample_price'].'">'.number_format($row2['in_sample_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['in_sample_total'].'">'.number_format($row2['in_sample_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['jumlah_in_qty'].'">'.number_format($row2['jumlah_in_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['jumlah_in_price'].'">'.number_format($row2['jumlah_in_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['jumlah_in_total'].'">'.number_format($row2['jumlah_in_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_prod_qty'].'">'.number_format($row2['out_prod_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_prod_price'].'">'.number_format($row2['out_prod_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_prod_total'].'">'.number_format($row2['out_prod_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_subcont_qty'].'">'.number_format($row2['out_subcont_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_subcont_price'].'">'.number_format($row2['out_subcont_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_subcont_total'].'">'.number_format($row2['out_subcont_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_lokal_qty'].'">'.number_format($row2['out_lokal_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_lokal_price'].'">'.number_format($row2['out_lokal_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_lokal_total'].'">'.number_format($row2['out_lokal_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_impor_qty'].'">'.number_format($row2['out_impor_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_impor_price'].'">'.number_format($row2['out_impor_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_impor_total'].'">'.number_format($row2['out_impor_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_sample_qty'].'">'.number_format($row2['out_sample_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_sample_price'].'">'.number_format($row2['out_sample_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_sample_total'].'">'.number_format($row2['out_sample_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_salnongroup_qty'].'">'.number_format($row2['out_salnongroup_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_salnongroup_price'].'">'.number_format($row2['out_salnongroup_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_salnongroup_total'].'">'.number_format($row2['out_salnongroup_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_salgroup_qty'].'">'.number_format($row2['out_salgroup_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_salgroup_price'].'">'.number_format($row2['out_salgroup_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_salgroup_total'].'">'.number_format($row2['out_salgroup_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_other_qty'].'">'.number_format($row2['out_other_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_other_price'].'">'.number_format($row2['out_other_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['out_other_total'].'">'.number_format($row2['out_other_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['jumlah_out_qty'].'">'.number_format($row2['jumlah_out_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['jumlah_out_price'].'">'.number_format($row2['jumlah_out_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['jumlah_out_total'].'">'.number_format($row2['jumlah_out_total'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['saldo_akhir_qty'].'">'.number_format($row2['saldo_akhir_qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['saldo_akhir_price'].'">'.number_format($row2['saldo_akhir_price'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['saldo_akhir_total'].'">'.number_format($row2['saldo_akhir_total'],2).'</td>

            </tr>
            ';

            ?>
            <?php 

        }
        ?>
    </table>

</body>
</html>




