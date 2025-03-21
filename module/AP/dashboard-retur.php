<?php
    include '../../conn/conn.php';
    ini_set('date.timezone', 'Asia/Jakarta');
    $insert_date = date("Y-m-d H:i:s");
    $nama_tipe = '';
    $nama_supp = '';
    $date_now = date("Y-m-d");                
    $start_date = '2024-01-01';
    $end_date = date("Y-m-d"); 

            $where1 = "where mid(a.bppbno,4,1) in ('A','F','B') and mid(a.bppbno,4,2)!='FG' and d.tipe_sup = 'S' and bppbdate between '$start_date' and '$end_date' order by bppbdate";
            $where2 = "where mid(bppbno,1,3) in ('WIP') and bppbdate between '$start_date' and '$end_date' order by bppbdate";
            $where3 = "where mid(a.bppbno,4,1) in ('N') and d.tipe_sup = 'S' and a.bppbdate between '$start_date' and '$end_date' order by a.bppbdate";
            $where4 = "where mid(bppbno,1,3) in ('Gen') and bppbdate between '$start_date' and '$end_date' order by bppbdate";

        $sql1  = "(select a.id,if(a.bppbno_int!='',a.bppbno_int,a.bppbno) bppbno,bppbdate,a.invno,a.jenis_dok,right(a.nomor_aju,6) no_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,supplier, ph.pono,ph.tipe_com,a.id_item,concat(itemdesc,' ',add_info) itemdesc,s.color,s.size, a.qty,a.qty as qty_good,0 as qty_reject, a.unit,ac.kpno ws,ac.styleno,a.curr,coalesce(ph.tax,0) tax, a.price,(a.qty * a.price) dpp, ((a.qty * a.price) + ((a.qty * a.price) * coalesce(ph.tax,0) /100)) total, ((a.qty * a.price) * coalesce(ph.tax,0) /100) ppn,a.jenis_trans, '' upt_no_inv,'' upt_tgl_inv,'' upt_no_faktur,'' upt_tgl_faktur from bppb a inner join masteritem s on a.id_item=s.id_item inner join mastersupplier d on a.id_supplier=d.id_supplier left join (select id_jo,id_so from jo_det group by id_jo ) tmpjod on tmpjod.id_jo=a.id_jo left join (select bppbno as no_req,idws_act from bppb_req group by no_req) br on a.bppbno_req = br.no_req left join so on tmpjod.id_so=so.id left join act_costing ac on so.id_cost=ac.id left join (select pono,bpbno from bpb GROUP BY bpbno_int) bpb on bpb.bpbno = a.bpbno_ro left JOIN (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A' GROUP BY pono) ph on ph.pono = bpb.pono $where1)";

        $sql2 = "(select id,bppbno,bppbdate,invno,jenis_dok,no_aju,tanggal_aju,bcno,bcdate,supplier,pono,tipe_com,id_item,itemdesc,color,size,qty,qty_good,qty_reject,unit,ws,styleno,curr,tax,price,dpp,total,ppn,jenis_trans,upt_no_inv,upt_tgl_inv,upt_no_faktur,upt_tgl_faktur from tbl_return_report $where2)";

        $sql3 = "(select a.id, if(a.bppbno_int!='',a.bppbno_int,a.bppbno) bppbno, a.bppbdate,a.invno,a.jenis_dok,right(a.nomor_aju,6) no_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier, ph.pono,ph.tipe_com,a.id_item, itemdesc itemdesc,s.color,s.size,a.qty, a.qty as qty_good,0 as qty_reject, a.unit,'' ws, '' styleno,a.curr,coalesce(ph.tax,0) tax,a.price, (a.qty * a.price) dpp, ((a.qty * a.price) + ((a.qty * a.price) * coalesce(ph.tax,0) /100)) total, ((a.qty * a.price) * coalesce(ph.tax,0) /100) ppn, a.jenis_trans, '' upt_no_inv,'' upt_tgl_inv,'' upt_no_faktur,'' upt_tgl_faktur from bppb a inner join masteritem s on a.id_item=s.id_item inner join mastersupplier d on a.id_supplier=d.id_supplier left join bpb on a.bpbno_ro = bpb.bpbno and a.id_item = bpb.id_item left JOIN (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A' GROUP BY pono) ph on ph.pono = bpb.pono $where3)";

        $sql4 = "(select id,bppbno,bppbdate,invno,jenis_dok,no_aju,tanggal_aju,bcno,bcdate,supplier,pono,tipe_com,id_item,itemdesc,color,size,qty,qty_good,qty_reject,unit,ws,styleno,curr,tax,price,dpp,total,ppn,jenis_trans,upt_no_inv,upt_tgl_inv,upt_no_faktur,upt_tgl_faktur from tbl_return_report $where4)";
        

        $sql = mysqli_query($conn1,"$sql1 UNION $sql2 UNION $sql3 UNION $sql4");


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

    $sqldel = "delete from dsb_ap_retur;";
    $querydel = mysqli_query($conn1,$sqldel);

    $sqldel2 = "ALTER TABLE dsb_ap_retur AUTO_INCREMENT = 1";
    $querydel2 = mysqli_query($conn1,$sqldel2);

    while($row = mysqli_fetch_array($sql)){
        $no_bpb = $row['bppbno']; 
        $nofaktur = $row['upt_no_inv']; 
        $noinv = $row['upt_no_faktur'];
        $price = $row['price'];
        $tgl_bpb = $row['bppbdate'];
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

        if ($price > 0) {
            $d_bpbno = $row['bppbno'];
        $d_bpbdate = $row['bppbdate'];
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
        //     <td style="width: 150px;" value = "'.$row['bppbno'].'">'.$row['bppbno'].'</td>
        //     <td style="width: 100px;" value = "'.$row['bppbdate'].'">'.date("d-M-Y",strtotime($row['bppbdate'])).'</td>
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
        $queryin = "INSERT INTO dsb_ap_retur (no_bpb,tgl_bpb,nama_supp,tipe,tipe_item,jml_qty,qty_good,qty_reject,curr,price,total,rate,dpp,ppn,total_idr,jenis_transaksi,create_date) 
                        VALUES 
                    ('$d_bpbno', '$d_bpbdate', '$d_supplier', '$d_tipe_com', '$asal', '$d_qty', '$d_qty_good', '$d_qty_reject', '$d_curr', '$d_price', '$d_dpp', '$rates', '$dpp_idr', '$ppn_idr', '$ttl_idr', '$d_jenis_trans', '$insert_date')";
            $executein = mysqli_query($conn1,$queryin);

        }else{ echo ''; }
}?>