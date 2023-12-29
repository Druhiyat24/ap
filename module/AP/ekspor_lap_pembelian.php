<html>
<head>
    <title>Export Data Ke Excel </title>
</head>
<body>
    <style type="text/css">
    body{
        font-family: sans-serif;
    }
    h4,p{
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
        font-family: sans-serif;
 
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
    $startdate = date("M Y",strtotime($_GET['start_date']));
    $enddate = date("M Y",strtotime($_GET['end_date']));
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=purchase report $startdate - $enddate.xls");
    $nama_supp =$_GET['nama_supp'];
    $status =$_GET['status'];
    $start_date = date("d F Y",strtotime($_GET['start_date']));
    $end_date = date("d F Y",strtotime($_GET['end_date'])); ?>

    <!-- <center> -->
        <h4>PURCHASE REPORT<br/> PERIODE <?php echo $start_date; ?> - <?php echo $end_date; ?></h4>
   <!--  </center> -->
    <p>TIPE: <?php echo $status; ?></p>
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No</th>
            <th style="text-align: center;vertical-align: middle;">No BPB</th>
            <th style="text-align: center;vertical-align: middle;">Tgl BPB</th>
            <th style="text-align: center;vertical-align: middle;">No SJ</th>
            <th style="text-align: center;vertical-align: middle;">Jenis Dok</th>
            <th style="text-align: center;vertical-align: middle;">No Aju</th>
            <th style="text-align: center;vertical-align: middle;">Tgl Aju</th>
            <th style="text-align: center;vertical-align: middle;">No Daftar</th>
            <th style="text-align: center;vertical-align: middle;">Tgl Daftar</th>
            <th style="text-align: center;vertical-align: middle;">Supplier</th>
            <th style="text-align: center;vertical-align: middle;">No PO</th>
            <th style="text-align: center;vertical-align: middle;">Tipe</th>
            <th style="text-align: center;vertical-align: middle;">Tipe Item</th>
            <th style="text-align: center;vertical-align: middle;">Id Item</th>
            <th style="text-align: center;vertical-align: middle;">Kode Barang</th>
            <th style="text-align: center;vertical-align: middle;">Nama Barang</th>
            <th style="text-align: center;vertical-align: middle;">Warna</th>
            <th style="text-align: center;vertical-align: middle;">Ukuran</th>
            <th style="text-align: center;vertical-align: middle;">Jumlah BPB</th>
            <th style="text-align: center;vertical-align: middle;">Qty Good</th>
            <th style="text-align: center;vertical-align: middle;">Qty Reject</th>
            <th style="text-align: center;vertical-align: middle;">Satuan</th>
            <th style="text-align: center;vertical-align: middle;">No WS</th>
            <th style="text-align: center;vertical-align: middle;">Style</th>
            <th style="text-align: center;vertical-align: middle;">Curr</th>
            <th style="text-align: center;vertical-align: middle;">Price</th>
            <th style="text-align: center;vertical-align: middle;">Total</th>
            <th style="text-align: center;vertical-align: middle;">Rate</th>
            <th style="text-align: center;vertical-align: middle;">DPP</th>
            <th style="text-align: center;vertical-align: middle;">PPN</th>
            <th style="text-align: center;vertical-align: middle;">Total IDR</th>
            <th style="text-align: center;vertical-align: middle;">Jenis Transaksi</th>
            <th style="text-align: center;vertical-align: middle;">No Invoice</th>
            <th style="text-align: center;vertical-align: middle;">Tgl Invoice</th>
            <th style="text-align: center;vertical-align: middle;">No Faktur</th>
            <th style="text-align: center;vertical-align: middle;">Tgl Faktur</th> 
        </tr>
        <?php 
        // koneksi database
        include '../../conn/conn.php';
        $nama_supp=$_GET['nama_supp'];
        $status =$_GET['status'];
        $start_date = date("Y-m-d",strtotime($_GET['start_date']));
        $end_date = date("Y-m-d",strtotime($_GET['end_date']));
        // menampilkan data pegawai
  

        if ($status == 'Bahan Baku') {
        if ($nama_supp == 'ALL') {
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null order by bpbdate";
        }else{
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' order by bpbdate";
        }

    $sql = mysqli_query($conn1,"select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,concat(s.itemdesc,' ',add_info) itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur,s.goods_code from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where");

    }elseif ($status == 'Barang Dalam Proses') {
        if ($nama_supp == 'ALL') {
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null order by bpbdate";
        }else{
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' order by bpbdate";
        }

    $sql = mysqli_query($conn1,"select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,s.itemdesc itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur,s.goods_code from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where");
    }elseif ($status == 'Item General') {
        if ($nama_supp == 'ALL') {
            $where = "where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null order by bpbdate";
        }else{
            $where = " where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null and supplier = '$nama_supp' order by bpbdate";
        }

    $sql = mysqli_query($conn1,"select a.id, if(a.bpbno_int!='',a.bpbno_int,a.bpbno) bpbno,a.bpbdate,a.invno,a.jenis_dok,right(a.nomor_aju,6) no_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier,a.pono,z.tipe_com,a.id_item, CONCAT(s.itemdesc,' ',s.add_info) itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject,a.unit, '' ws, '' styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur,s.goods_code from bpb a inner join masteritem s on a.id_item=s.id_item LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft) z on a.pono = z.pono LEFT join mastersupplier d on a.id_supplier=d.id_supplier LEFT OUTER JOIN mapping_category AS m ON s.n_code_category=m.n_id left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where");

    }elseif ($status == 'ALL') {
        if ($nama_supp == 'ALL') {
            $where3 = "where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null order by bpbdate";
            $where2 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null order by bpbdate";
            $where1 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null order by bpbdate";
        }else{
            $where3 = " where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null and supplier = '$nama_supp' order by bpbdate";
            $where2 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' order by bpbdate";
            $where1 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' order by bpbdate";
        }

        $sql1  = "(select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,concat(s.itemdesc,' ',add_info) itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur,s.goods_code from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where1)";

        $sql2 = "(select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,s.itemdesc itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur,s.goods_code from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where2)";

        $sql3 = "(select a.id, if(a.bpbno_int!='',a.bpbno_int,a.bpbno) bpbno,a.bpbdate,a.invno,a.jenis_dok,right(a.nomor_aju,6) no_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier,a.pono,z.tipe_com,a.id_item, CONCAT(s.itemdesc,' ',s.add_info) itemdesc,s.color,s.size, a.qty,(a.qty-coalesce(a.qty_reject,0)) as qty_good,coalesce(a.qty_reject,0) as qty_reject,a.unit, '' ws, '' styleno,a.curr,z.tax,a.price, ((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, (((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur,s.goods_code from bpb a inner join masteritem s on a.id_item=s.id_item LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft) z on a.pono = z.pono LEFT join mastersupplier d on a.id_supplier=d.id_supplier LEFT OUTER JOIN mapping_category AS m ON s.n_code_category=m.n_id left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where3)";

        $sql = mysqli_query($conn1,"$sql1 UNION $sql2 UNION $sql3");


    }

        $no = 1;

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
        echo '<tr style="font-size:12px;text-align:center;">
            <td >'.$no++.'</td>
            <td style="text-align : left;" value = "'.$row['bpbno'].'">'.$row['bpbno'].'</td>
            <td style="text-align : left;" value = "'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
            <td style="text-align : left;" value = "'.$row['invno'].'">'.$row['invno'].'</td>
            <td style="text-align : left;" value = "'.$row['jenis_dok'].'">'.$row['jenis_dok'].'</td>
            <td style="text-align : left;" value = "'.$row['no_aju'].'">'.$row['no_aju'].'</td>
            <td style="text-align : left;" value = "'.$tgl_aju.'">'.$tgl_aju.'</td>
            <td style="text-align : left;" value = "'.$row['bcno'].'">'.$row['bcno'].'</td>
            <td style="text-align : left;" value = "'.$row['bcdate'].'">'.date("d-M-Y",strtotime($row['bcdate'])).'</td>
            <td style="text-align : left;" value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
            <td style="text-align : left;" value = "'.$row['pono'].'">'.$row['pono'].'</td>
            <td style="text-align : left;" value = "'.$row['tipe_com'].'">'.$row['tipe_com'].'</td>
            <td style="text-align : left;" value = "'.$asal.'">'.$asal.'</td>
            <td style="text-align : left;" value = "'.$row['id_item'].'">'.$row['id_item'].'</td>
            <td style="text-align : left;" value = "'.$row['goods_code'].'">'.$row['goods_code'].'</td>
            <td style="text-align : left;" value = "'.$row['itemdesc'].'">'.$row['itemdesc'].'</td>
            <td style="text-align : left;" value = "'.$row['color'].'">'.$row['color'].'</td>
            <td style="text-align : left;" value = "'.$row['size'].'">'.$row['size'].'</td>
            <td style=" text-align : right;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>
            <td style=" text-align : right;" value="'.$row['qty_good'].'">'.number_format($row['qty_good'],2).'</td>
            <td style=" text-align : right;" value="'.$row['qty_reject'].'">'.number_format($row['qty_reject'],2).'</td>
            <td style="text-align : left;" value = "'.$row['unit'].'">'.$row['unit'].'</td>
            <td style="text-align : left;" value = "'.$row['ws'].'">'.$row['ws'].'</td>
            <td style="text-align : left;" value = "'.$row['styleno'].'">'.$row['styleno'].'</td>
            <td style="text-align : left;" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="text-align : right;" value="'.$row['price'].'">'.number_format($row['price'],2).'</td>
            <td style="text-align : right;" value="'.$row['dpp'].'">'.number_format($row['dpp'],2).'</td>
            <td style="text-align : right;" value="'.$rates.'">'.number_format($rates,2).'</td>
            <td style="text-align : right;" value="'.$dpp_idr.'">'.number_format($dpp_idr,2).'</td>
            <td style="text-align : right;" value="'.$ppn_idr.'">'.number_format($ppn_idr,2).'</td>
            <td style="text-align : right;" value="'.$ttl_idr.'">'.number_format($ttl_idr,2).'</td>
            <td style="text-align : left;" value = "'.$row['jenis_trans'].'">'.$row['jenis_trans'].'</td>
            <td style="text-align : left;" value = "'.$no_inv.'">'.$no_inv.'</td>
            <td style="text-align : left;" value = "'.$tgl_inv.'">'.$tgl_inv.'</td>
            <td style="text-align : left;" value = "'.$no_faktur.'">'.$no_faktur.'</td>
            <td style="text-align : left;" value = "'.$tgl_faktur.'">'.$tgl_faktur.'</td>
             </tr>';
        }else{ echo ''; }
         
        ?>
        <?php 
        
    }
        ?>
    </table>

</body>
</html>




