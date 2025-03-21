 <?php
    include '../../conn/conn.php';
    ini_set('date.timezone', 'Asia/Jakarta');
    $insert_date = date("Y-m-d H:i:s");
    $nama_tipe = '';
    $nama_supp = '';
    $date_now = date("Y-m-d");                
    $start_date = '2024-01-01';
    $end_date = date("Y-m-d");               

            $where3 = "where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate BETWEEN '$start_date' and '$end_date' and tipe_com != 'FOC' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate BETWEEN '$start_date' and '$end_date' and tipe_com is null order by bpbno_int";
            $where2 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null order by bpbno_int";
            $where1 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null order by bpbno_int";

        $sql1  = "(select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,concat(s.itemdesc,' ',add_info) itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where1)";

        $sql2 = "(select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,s.itemdesc itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where2)";

        $sql3 = "(select a.id, if(a.bpbno_int!='',a.bpbno_int,a.bpbno) bpbno,a.bpbdate,a.invno,a.jenis_dok,right(a.nomor_aju,6) no_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier,a.pono,z.tipe_com,a.id_item, CONCAT(s.itemdesc,' ',s.add_info) itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject,a.unit, '' ws, '' styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft) z on a.pono = z.pono LEFT join mastersupplier d on a.id_supplier=d.id_supplier LEFT OUTER JOIN mapping_category AS m ON s.n_code_category=m.n_id left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where3)";

        $sql = mysqli_query($conn1,"$sql1 UNION $sql2 UNION $sql3");
        $sqlnya = "$sql1 UNION $sql2 UNION $sql3";

        echo $sqlnya;
    $asal = '';
    $rates = 0;
    $dpp_idr = 0;
    $ppn_idr = 0;
    $ttl_idr = 0;
    $d_bpbno = '';
    $d_bpbdate = '';
    $d_supplier = '';
    $d_tipe_com = '';
    $d_qty = 0;
    $d_qty_good = 0;
    $d_qty_reject = 0;
    $d_curr = '';
    $d_price = 0;
    $d_dpp = 0;
    $d_jenis_trans = '';

    $sqldel = "delete from dsb_ap_purchase;";
    $querydel = mysqli_query($conn1,$sqldel);

    $sqldel2 = "ALTER TABLE dsb_ap_purchase AUTO_INCREMENT = 1";
    $querydel2 = mysqli_query($conn1,$sqldel2);

    while($row = mysqli_fetch_array($sql)){
        $no_bpb = $row['bpbno']; 
        $nofaktur = $row['upt_no_faktur']; 
        $noinv = $row['upt_no_inv'];
        $price = $row['price'];
        $tgl_bpb = $row['bpbdate'];
        $tanggal_aju = $row['tanggal_aju'];
        $curr = $row['curr']; 
        $sql_rate = mysqli_query($conn1,"select rate from masterrate where v_codecurr = 'PAJAK' and tanggal = '$tgl_bpb'");
        $row_rate = mysqli_fetch_array($sql_rate);
        $rate = isset($row_rate['rate']) ? $row_rate['rate'] : 1;
        if ($curr == 'USD') {
            $rates = $rate;
        }else{
            $rates = '1';
        }
        $dpp_idr = $row['dpp'] * $rates;
        $ppn_idr = $row['ppn'] * $rates;
        $ttl_idr = $row['total'] * $rates;

        if ($tanggal_aju == '1970-01-01' || $tanggal_aju == '0000-00-00'|| $tanggal_aju == '') {
            $tgl_aju = '';
        } else{
            $tgl_aju = date("d-M-Y",strtotime($row['tanggal_aju']));
        }


        if ($noinv == '') {
            $no_inv = '-';
            $tgl_inv = '-';
        } else{
            $no_inv = $row['upt_no_inv']; 
            $tgl_inv = date("d-M-Y",strtotime($row['upt_tgl_inv']));
        }

        if ($nofaktur == '') {
            $no_faktur = '-';
            $tgl_faktur = '-';
        } else{
            $no_faktur = $row['upt_no_faktur']; 
            $tgl_faktur = date("d-M-Y",strtotime($row['upt_tgl_faktur']));
        }

        if(strpos($no_bpb, 'GACC') !== false) {
            $asal = 'AKSESORIS';
        }elseif (strpos($no_bpb, 'GK') !== false) {
            $asal = 'KAIN';
        }elseif (strpos($no_bpb, 'WIP') !== false) {
            $asal = 'BARANG DALAM PROSES';
        }elseif (strpos($no_bpb, 'GEN') !== false) {
            $asal = 'ITEM GENERAL';
        }else{
            $asal = ''; 
        }

        if ($price > 0 && $row['qty_good'] >= 0) {

        $d_bpbno = $row['bpbno'];
        $d_bpbdate = $row['bpbdate'];
        $d_supplier = $row['supplier'];
        $d_tipe_com = $row['tipe_com'];
        $d_qty = $row['qty'];
        $d_qty_good = $row['qty_good'];
        $d_qty_reject = $row['qty_reject'];
        $d_curr = $row['curr'];
        $d_price = $row['price'];
        $d_dpp = $row['dpp'];
        $d_jenis_trans = $row['jenis_trans'];
        // echo '<tr style="font-size:12px;text-align:center;">
        //     <td style="width: 150px;" value = "'.$row['bpbno'].'">'.$row['bpbno'].'</td>
        //     <td style="width: 100px;" value = "'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
        //     <td style="width: 150px;" value = "'.$row['invno'].'">'.$row['invno'].'</td>
        //     <td style="width: 150px;" value = "'.$row['jenis_dok'].'">'.$row['jenis_dok'].'</td>
        //     <td style="width: 150px;" value = "'.$row['no_aju'].'">'.$row['no_aju'].'</td>
        //     <td style="width: 150px;" value = "'.$tgl_aju.'">'.$tgl_aju.'</td>
        //     <td style="width: 150px;" value = "'.$row['bcno'].'">'.$row['bcno'].'</td>
        //     <td style="width: 150px;" value = "'.$row['bcdate'].'">'.date("d-M-Y",strtotime($row['bcdate'])).'</td>
        //     <td style="width: 150px;" value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
        //     <td style="width: 150px;" value = "'.$row['pono'].'">'.$row['pono'].'</td>
        //     <td style="width: 150px;" value = "'.$row['tipe_com'].'">'.$row['tipe_com'].'</td>
        //     <td style="width: 150px;" value = "'.$asal.'">'.$asal.'</td>
        //     <td style="width: 150px;" value = "'.$row['id_item'].'">'.$row['id_item'].'</td>
        //     <td style="width: 150px;" value = "'.$row['itemdesc'].'">'.$row['itemdesc'].'</td>
        //     <td style="width: 150px;" value = "'.$row['color'].'">'.$row['color'].'</td>
        //     <td style="width: 150px;" value = "'.$row['size'].'">'.$row['size'].'</td>
        //     <td style="width:50px; text-align : right;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>
        //     <td style="width:50px; text-align : right;" value="'.$row['qty_good'].'">'.number_format($row['qty_good'],2).'</td>
        //     <td style="width:50px; text-align : right;" value="'.$row['qty_reject'].'">'.number_format($row['qty_reject'],2).'</td>
        //     <td style="width: 150px;" value = "'.$row['unit'].'">'.$row['unit'].'</td>
        //     <td style="width: 150px;" value = "'.$row['ws'].'">'.$row['ws'].'</td>
        //     <td style="width: 150px;" value = "'.$row['styleno'].'">'.$row['styleno'].'</td>
        //     <td style="width: 150px;" value = "'.$row['curr'].'">'.$row['curr'].'</td>
        //     <td style="width:50px; text-align : right;" value="'.$row['price'].'">'.number_format($row['price'],2).'</td>
        //     <td style="width:50px; text-align : right;" value="'.$row['dpp'].'">'.number_format($row['dpp'],2).'</td>
        //     <td style="width:50px; text-align : right;" value="'.$rates.'">'.number_format($rates,2).'</td>
        //     <td style="width:50px; text-align : right;" value="'.$dpp_idr.'">'.number_format($dpp_idr,2).'</td>
        //     <td style="width:50px; text-align : right;" value="'.$ppn_idr.'">'.number_format($ppn_idr,2).'</td>
        //     <td style="width:50px; text-align : right;" value="'.$ttl_idr.'">'.number_format($ttl_idr,2).'</td>
        //     <td style="width: 150px;" value = "'.$row['jenis_trans'].'">'.$row['jenis_trans'].'</td>
        //     <td style="width: 150px;" value = "'.$no_inv.'">'.$no_inv.'</td>
        //     <td style="width: 100px;" value = "'.$tgl_inv.'">'.$tgl_inv.'</td>
        //     <td style="width: 150px;" value = "'.$no_faktur.'">'.$no_faktur.'</td>
        //     <td style="width: 100px;" value = "'.$tgl_faktur.'">'.$tgl_faktur.'</td>';
        //     echo '</tr>';

            $queryin = "INSERT INTO dsb_ap_purchase (no_bpb,tgl_bpb,nama_supp,tipe,tipe_item,jml_qty,qty_good,qty_reject,curr,price,total,rate,dpp,ppn,total_idr,jenis_transaksi,create_date) 
                        VALUES 
                    ('$d_bpbno', '$d_bpbdate', '$d_supplier', '$d_tipe_com', '$asal', '$d_qty', '$d_qty_good', '$d_qty_reject', '$d_curr', '$d_price', '$d_dpp', '$rates', '$dpp_idr', '$ppn_idr', '$ttl_idr', '$d_jenis_trans', '$insert_date')";
            $executein = mysqli_query($conn1,$queryin);

        }else{ echo ''; }
}?>