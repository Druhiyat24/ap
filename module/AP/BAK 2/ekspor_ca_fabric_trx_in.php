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
        <tr>
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
        <?php 
        // koneksi database
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));


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
        $no = 0;
        while($row2 = mysqli_fetch_array($sql)){
            $no++;
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

            echo ' <tr style="font-size:12px;text-align:center;">
            <td style="text-align : left;" value = "'.$no.'">'.$no.'</td>
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

            ?>
            <?php 

        }
        ?>
    </table>

</body>
</html>




