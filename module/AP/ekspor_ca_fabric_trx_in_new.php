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
    header("Content-Disposition: attachment; filename=fabric transaction in.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> FABRIC IN<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No Trans</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Tgl Trans</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Id Item</th>
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
            <th colspan="3" style="text-align: center;vertical-align: middle;">Nilai Barang / Unit Dalam Mata Uang Asal</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Nilai Barang Dalam Mata Uang Asal</th>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">Rate</th>
            <th colspan="3" style="text-align: center;vertical-align: middle;">Nilai Barang Ekuivalen IDR</th>
        </tr>
        <tr >
            <th style="text-align: center;vertical-align: middle;">Nilai Barang</th>
            <th style="text-align: center;vertical-align: middle;">Jasa Subcont</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: center;vertical-align: middle;">Nilai Barang</th>
            <th style="text-align: center;vertical-align: middle;">Jasa Subcont</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: center;vertical-align: middle;">Nilai Barang</th>
            <th style="text-align: center;vertical-align: middle;">Jasa Subcont</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


        $sql = mysqli_query($conn2,"WITH
            out_h as (select a.no_dok, a.tgl_dok, b.id_jo, b.id_item, c.itemdesc, c.color, c.size, IFNULL(type_bc,'-') type_bc, IFNULL(no_invoice,'-') no_invoice, IFNULL(no_aju,'-') no_aju, tgl_aju, IFNULL(no_daftar,'-') no_daftar, tgl_daftar, a.supplier, IFNULL(a.no_po,'-') no_po, IFNULL(tipe_com,'-') tipe_com, IFNULL(no_invoice,'-') no_sj, IFNULL(a.deskripsi,'-') deskripsi, CONCAT(a.created_by,' (',a.created_at, ') ') username, kpno, styleno, a.type_pch, b.price from whs_inmaterial_fabric a INNER JOIN whs_inmaterial_fabric_det b on b.no_dok = a.no_dok INNER JOIN masteritem c on c.id_item = b.id_item INNER JOIN (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) d on d.id_jo=b.id_jo LEFT join po_header po on po.pono = a.no_po LEFT join po_header_draft pod on pod.id = po.id_draft where a.tgl_dok BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.status != 'N' GROUP BY b.id_item, b.id_jo, b.no_dok),

            out_det as (select no_dok, id_jo, id_item, no_barcode, no_roll, no_lot, kode_lok, sum(qty_aktual) qty_in, satuan, np_curr, np_tgl_in, IFNULL(np_price,0) np_price, IF(np_curr = 'IDR',1,IFNULL(rate,1)) rate from (select a.no_dok, b.id_jo, b.id_item, b.no_barcode, b.no_roll, b.no_lot, b.kode_lok, b.qty_aktual, satuan, IFNULL(np_curr_rev,np_curr) np_curr, np_tgl_in, IFNULL(np_price_rev,np_price) np_price from whs_inmaterial_fabric a INNER JOIN whs_lokasi_inmaterial b on b.no_dok = a.no_dok where a.tgl_dok BETWEEN '$start_date' and '$end_date' and a.status != 'Cancel' and b.status != 'N') a left join (select tanggal, curr, rate from masterrate where v_codecurr = 'PAJAK' and tanggal BETWEEN '$start_date' and '$end_date' GROUP BY tanggal, curr ) cr on cr.tanggal = a.np_tgl_in and cr.curr = np_curr GROUP BY a.id_item, a.id_jo, a.no_dok,np_curr)

            select a.no_dok, a.tgl_dok, a.id_item, itemdesc, color, size, no_invoice, type_bc, no_aju, tgl_aju, no_daftar, tgl_daftar, supplier, no_po, tipe_com, no_sj, b.qty_in, b.satuan, 0 berat_bersih, deskripsi, username, kpno, styleno, np_curr, price, np_price, type_pch, np_price price_unit, 0 jasa, np_price total_price, (np_price * qty_in) total_in, 0 jasa_in, (np_price * qty_in) jumlah_in, rate, ((np_price * qty_in) * rate) total_in_idr, 0 jasa_in_idr, ((np_price * qty_in) * rate) jumlah_in_idr from out_h a INNER JOIN out_det b on b.no_dok = a.no_dok and b.id_item = a.id_item and b.id_jo = a.id_jo");

        $no = 0;
        while($row2 = mysqli_fetch_array($sql)){
            $no++;
            echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
            <td style="text-align : left;" value = "'.$row2['no_dok'].'">'.$row2['no_dok'].'</td>
            <td style="width: 100px;" value = "'.$row2['tgl_dok'].'">'.date("d-M-Y",strtotime($row2['tgl_dok'])).'</td>
            <td style="text-align : left;" value = "'.$row2['id_item'].'">'.$row2['id_item'].'</td>
            <td style="text-align : left;" value = "'.$row2['itemdesc'].'">'.$row2['itemdesc'].'</td>
            <td style="text-align : left;" value = "'.$row2['color'].'">'.$row2['color'].'</td>
            <td style="text-align : left;" value = "'.$row2['size'].'">'.$row2['size'].'</td>
            <td style="text-align : left;" value = "'.$row2['no_invoice'].'">'.$row2['no_invoice'].'</td>
            <td style="text-align : left;" value = "'.$row2['type_bc'].'">'.$row2['type_bc'].'</td>
            <td style="text-align : left;" value = "'.$row2['no_aju'].'">'.$row2['no_aju'].'</td>
            <td style="width: 100px;" value = "'.$row2['tgl_aju'].'">'.date("Y-m-d",strtotime($row2['tgl_aju'])).'</td>
            <td style="text-align : left;" value = "'.$row2['no_daftar'].'">'.$row2['no_daftar'].'</td>
            <td style="width: 100px;" value = "'.$row2['tgl_daftar'].'">'.date("Y-m-d",strtotime($row2['tgl_daftar'])).'</td>
            <td style="text-align : left;" value = "'.$row2['supplier'].'">'.$row2['supplier'].'</td>
            <td style="text-align : left;" value = "'.$row2['no_po'].'">'.$row2['no_po'].'</td>
            <td style="text-align : left;" value = "'.$row2['tipe_com'].'">'.$row2['tipe_com'].'</td>
            <td style="text-align : left;" value = "'.$row2['no_sj'].'">'.$row2['no_sj'].'</td>
            <td style="text-align : right;" value = "'.$row2['qty_in'].'">'.number_format($row2['qty_in'],2).'</td>
            <td style="text-align : left;" value = "'.$row2['satuan'].'">'.$row2['satuan'].'</td>
            <td style="text-align : right;" value = "'.$row2['berat_bersih'].'">'.number_format($row2['berat_bersih'],2).'</td>
            <td style="text-align : left;" value = "'.$row2['deskripsi'].'">'.$row2['deskripsi'].'</td>
            <td style="text-align : left;" value = "'.$row2['username'].'">'.$row2['username'].'</td>
            <td style="text-align : left;" value = "'.$row2['kpno'].'">'.$row2['kpno'].'</td>
            <td style="text-align : left;" value = "'.$row2['styleno'].'">'.$row2['styleno'].'</td>
            <td style="text-align : left;" value = "'.$row2['np_curr'].'">'.$row2['np_curr'].'</td>
            <td style="text-align : right;" value = "'.$row2['price'].'">'.number_format($row2['price'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['np_price'].'">'.number_format($row2['np_price'],2).'</td>
            <td style="text-align : left;" value = "'.$row2['type_pch'].'">'.$row2['type_pch'].'</td>
            <td style="text-align : right;" value = "'.$row2['price_unit'].'">'.number_format($row2['price_unit'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['jasa'].'">'.number_format($row2['jasa'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['total_price'].'">'.number_format($row2['total_price'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['total_in'].'">'.number_format($row2['total_in'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['jasa_in'].'">'.number_format($row2['jasa_in'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['jumlah_in'].'">'.number_format($row2['jumlah_in'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['rate'].'">'.number_format($row2['rate'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['total_in_idr'].'">'.number_format($row2['total_in_idr'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['jasa_in_idr'].'">'.number_format($row2['jasa_in_idr'],2).'</td>
            <td style="text-align : right;" value = "'.$row2['jumlah_in_idr'].'">'.number_format($row2['jumlah_in_idr'],2).'</td>
            </tr>
            ';

            ?>
            <?php 

        }
        ?>
    </table>

</body>
</html>




