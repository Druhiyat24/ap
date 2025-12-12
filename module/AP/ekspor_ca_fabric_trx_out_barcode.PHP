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

    <h4> FABRIC OUT<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th style="text-align: center;vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No Trans</th>
                        <th style="text-align: center;vertical-align: middle;">Tgl Trans</th>
                        <th style="text-align: center;vertical-align: middle;">No Barcode</th>
                        <th style="text-align: center;vertical-align: middle;">No Roll</th>
                        <th style="text-align: center;vertical-align: middle;">No Lot</th>
                        <th style="text-align: center;vertical-align: middle;">Lokasi</th>
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
                        <th style="text-align: center;vertical-align: middle;">Style</th>
                        <th style="text-align: center;vertical-align: middle;">Curr</th>
                        <th style="text-align: center;vertical-align: middle;">Price</th>
                        <th style="text-align: center;vertical-align: middle;">Status</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang / Unit Dalam Mata Uang Asal</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang Dalam Mata Uang Asal</th>
                        <th style="text-align: center;vertical-align: middle;">Rate</th>
                        <th style="text-align: center;vertical-align: middle;">Nilai Barang Ekuivalen IDR</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"select no_bppb, tgl_bppb, id_roll, no_lot, no_roll, CONCAT(no_rak,'  FABRIC WAREHOUSE RACK') no_rak, id_item, itemdesc, color, size, IFNULL(no_invoice,'-') no_invoice, dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, tujuan, qty_out, satuan, berat_bersih, IFNULL(catatan,'-') catatan, username, kpno, styleno, IFNULL(np_curr,'-') np_curr, IFNULL(np_price,0) np_price, jenis_pengeluaran, IFNULL(np_price,0) price_unit, (qty_out * IFNULL(np_price,0)) total, IFNULL(rate,1) rate, ((qty_out * IFNULL(np_price,0)) * IFNULL(rate,1)) total_idr from (select  a.no_bppb, a.tgl_bppb, id_roll, no_lot, no_roll, no_rak, b.id_item, c.itemdesc, c.color, c.size, a.no_invoice, a.dok_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, a.tujuan, b.qty_out, b.satuan, 0 berat_bersih, a.catatan, CONCAT(a.created_by,' (',a.created_at, ') ') username, kpno, styleno, IFNULL(b.np_curr_rev,b.np_curr) np_curr, np_tgl_in, IFNULL(b.np_price_rev,b.np_price) np_price, jenis_pengeluaran from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb INNER JOIN masteritem c on c.id_item = b.id_item left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=b.id_jo where a.tgl_bppb BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.status = 'Y') a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr ) cr on cr.tanggal = a.tgl_bppb and cr.curr = a.np_curr");


        $total_qty =0;
        $total_price_non_ro =0;
        $total_total_non_ro =0;
        $sum_total_ro_nonro_idr =0;
        $no = 0;
        while($row2 = mysqli_fetch_array($sql)){
            $no++;

            echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
            <td style="text-align : left;" value = "'.$row2['no_bppb'].'">'.$row2['no_bppb'].'</td>
                <td style="width: 100px;" value = "'.$row2['tgl_bppb'].'">'.date("d-M-Y",strtotime($row2['tgl_bppb'])).'</td>
                <td style="text-align : left;" value = "'.$row2['id_roll'].'">'.$row2['id_roll'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_roll'].'">'.$row2['no_roll'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_lot'].'">'.$row2['no_lot'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_rak'].'">'.$row2['no_rak'].'</td>
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

            ?>
            <?php 

        }
        ?>
    </table>

</body>
</html>




