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
    header("Content-Disposition: attachment; filename=fabric transaction out.xls");
    include '../../conn/conn.php';
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date']));

    ?>

    <h4> FABRIC OUT<br/> PERIODE: <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>

    <table style="width:100%;font-size:10px;" border="1" width="100%">
        <tr>
            <th rowspan="2" style="text-align: center;vertical-align: middle;">No</th>
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
        <tr>
            <th style="text-align: center;vertical-align: middle;">Non Return</th>
            <th style="text-align: center;vertical-align: middle;">Return</th>
            <th style="text-align: center;vertical-align: middle;">Non Return</th>
            <th style="text-align: center;vertical-align: middle;">Return</th>
            <th style="text-align: center;vertical-align: middle;">Non Return</th>
            <th style="text-align: center;vertical-align: middle;">Return</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
        </tr>
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


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
        $no = 0;
        while($row2 = mysqli_fetch_array($sql)){
            $no++;
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
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
            <td style="text-align : left;" value = "'.$row2['bppbno'].'">'.$row2['bppbno'].'</td>
            <td style="width: 100px;" value = "'.$row2['bppbdate'].'">'.date("d-M-Y",strtotime($row2['bppbdate'])).'</td>
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

            ?>
            <?php 

        }
        ?>
    </table>

</body>
</html>




