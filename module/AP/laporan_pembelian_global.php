<?php include '../header.php' ?>

    <!-- MAIN -->
    <div class="col p-4">
        <h2 class="text-center">PURCHASE REPORT GLOBAL</h2>
<div class="box">
    <div class="box header">
        <div style="padding-left: 10px;padding-top: 5px;">
            <button style="-ms-transform: skew(7deg);-webkit-transform: skew(7deg);transform: skew(10deg);" id="rpt_det" type="button" class="btn-secondary btn-xs"><span></span> Report Detail</button>
            <button style="-ms-transform: skew(7deg);-webkit-transform: skew(7deg);transform: skew(10deg);" id="rpt_glo" type="button" class="btn-primary btn-xs"><span></span>Report Global</button>
        </div>
        <form id="form-data" action="laporan_pembelian_global.php" method="post"> 
        <input type='hidden' name='width' id='inp_width'/>
        <input type='hidden' name='divwidth' id='div_width'/>       
        <div class="form-row">
           <div class="col-md-2">
            <label for="nama_tipe"><b>Tipe</b></label>            
              <select class="form-control selectpicker" name="nama_tipe" id="nama_tipe" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $nama_tipe ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_tipe = isset($_POST['nama_tipe']) ? $_POST['nama_tipe']: null;
                }                 
                $sql = mysqli_query($conn1,"select nama_pilihan isi,if(nama_pilihan='Mesin','Barang Modal',nama_pilihan) tampil from masterpilihan where kode_pilihan='Type Mat' and nama_pilihan not in ('Barang Jadi','Scrap','Mesin') order by nama_pilihan");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['tampil'];
                    $data2 = $row['isi'];
                    if($row['isi'] == $_POST['nama_tipe']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data2.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>  

                <div class="col-md-4">
            <label for="nama_supp"><b>Supplier</b></label>            
              <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="ALL" selected="true">ALL</option>                                                
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                }?>
                </select>
                </div> 


            <div class="col-md-2 mb-3"> 
            <label for="start_date"><b>From</b></label>          
            <input type="text" style="font-size: 12px;" class="form-control tanggal" id="start_date" name="start_date" 
            value="<?php
            $start_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
               $start_date = date("Y-m-d",strtotime($_POST['start_date']));
            }
            if(!empty($_POST['start_date'])) {
               echo $_POST['start_date'];
            }
            else{
               echo date("d-m-Y");
            } ?>" 
            placeholder="Tanggal Awal" >
            </div>

            <div class="col-md-2 mb-3"> 
            <label for="end_date"><b>To</b></label>          
            <input type="text" style="font-size: 12px;" class="form-control tanggal" id="end_date" name="end_date" 
            value="<?php
            $end_date ='';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
               $end_date = date("Y-m-d",strtotime($_POST['end_date']));
            }
            if(!empty($_POST['end_date'])) {
               echo $_POST['end_date'];
            }
            else{
               echo date("d-m-Y");
            } ?>" 
            placeholder="Tanggal Awal" >
            </div>
            <div class="input-group-append col">                                   
            <button type="submit" id="submit"  value=" Search " style="margin-top: 25px; margin-bottom: 15px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 12px;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            <button type="button" id="ekspor" name="ekspor" value=" ekspor " style="margin-top: 25px; margin-bottom: 15px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 12px;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(0, 135, 131)"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel </button>

<!-- <?php
        $nama_tipe = isset($_POST['nama_tipe']) ? $_POST['nama_tipe']: null;

        if($nama_tipe == 'ALL'){
            echo '<a target="_blank" href="ekspor_lp_all.php?nama_supp='.$nama_supp.' && status='.$nama_tipe.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($nama_tipe == 'draft'){
            echo '<a target="_blank" href="ekspor_lp_draft.php?nama_supp='.$nama_supp.' && status='.$nama_tipe.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($nama_tipe == 'Approved'){
            echo '<a target="_blank" href="ekspor_lp_app.php?nama_supp='.$nama_supp.' && status='.$nama_tipe.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>';
        }elseif($nama_tipe == 'Cancel'){
            echo '<a target="_blank" href="ekspor_lp_cancel.php?nama_supp='.$nama_supp.' && status='.$nama_tipe.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }elseif($nama_tipe == 'Closed'){
            echo '<a target="_blank" href="ekspor_lp_closed.php?nama_supp='.$nama_supp.' && status='.$nama_tipe.' && start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>'; 
        }else{
            $filterr = ""; 
        }
        ?> -->
            </div>                                                            
    </div>
<br/>
</div>
</form> 

    </div>
        <?php
            $ttl_width_ =0;
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $width = isset($_POST['width']) ? $_POST['width']: null;
            $divwidth = isset($_POST['divwidth']) ? $_POST['divwidth']: null;
            $ttl_width = $width;
            $ttl_width_ = $ttl_width . "px";
            }   
                if ($ttl_width_ <= 0) {
                    $hidden = 'hidden';
                }else{
                    $hidden = '';
                }
        ?>
        <input type='hidden' name='fil_width' id='fil_width' value="<?= $ttl_width_;?>" />
    <div class="box body" <?= $hidden;?>>
        <div class="row">       
            <div class="col-md-12">
   <div class=" tableFix card-body table-responsive p-0" style="width: <?= $ttl_width_;?>;height: 400px;">
    <table id="datatable" class="table table-bordered table-head-fixed text-nowrap" width="100%">         
<!-- <table id="datatable" class="table table-striped table-bordered" role="grid" cellspacing="0" width="100%"> -->
    <thead>
        <tr class="thead-dark">
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
    </thead>
   
    <tbody>
    <?php
    $nama_tipe = '';
    $nama_supp = '';
    $start_date ='';
    $end_date ='';
    $date_now = date("Y-m-d");                
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_tipe = isset($_POST['nama_tipe']) ? $_POST['nama_tipe']: null; 
    $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null; 
    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
    $end_date = date("Y-m-d",strtotime($_POST['end_date']));               
    }

    if ($nama_tipe == 'Bahan Baku') {
        if ($nama_supp == 'ALL') {
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null group by a.bpbno_int order by bpbdate";
        }else{
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' group by a.bpbno_int order by bpbdate";
        }

    $sql = mysqli_query($conn1,"select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,concat(s.itemdesc,' ',add_info) itemdesc,s.color,s.size, sum(a.qty) qty,sum(a.qty-coalesce(a.qty_reject,0)) as qty_good,sum(coalesce(a.qty_reject,0)) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, SUM((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where");

    }elseif ($nama_tipe == 'Barang Dalam Proses') {
        if ($nama_supp == 'ALL') {
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null group by a.bpbno_int order by bpbdate";
        }else{
            $where = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' group by a.bpbno_int order by bpbdate";
        }

    $sql = mysqli_query($conn1,"select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,s.itemdesc itemdesc,s.color,s.size, sum(a.qty) qty,sum(a.qty-coalesce(a.qty_reject,0)) as qty_good,sum(coalesce(a.qty_reject,0)) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, SUM((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where");
    }elseif ($nama_tipe == 'Item General') {
        if ($nama_supp == 'ALL') {
            $where = "where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null group by a.bpbno_int order by bpbdate";
        }else{
            $where = " where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null and supplier = '$nama_supp' group by a.bpbno_int order by bpbdate";
        }

    $sql = mysqli_query($conn1,"select a.id, if(a.bpbno_int!='',a.bpbno_int,a.bpbno) bpbno,a.bpbdate,a.invno,a.jenis_dok,right(a.nomor_aju,6) no_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier,a.pono,z.tipe_com,a.id_item, CONCAT(s.itemdesc,' ',s.add_info) itemdesc,s.color,s.size, sum(a.qty) qty,sum(a.qty-coalesce(a.qty_reject,0)) as qty_good,sum(coalesce(a.qty_reject,0)) as qty_reject,a.unit, '' ws, '' styleno,a.curr,z.tax,a.price, SUM((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft) z on a.pono = z.pono LEFT join mastersupplier d on a.id_supplier=d.id_supplier LEFT OUTER JOIN mapping_category AS m ON s.n_code_category=m.n_id left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where");

    }elseif ($nama_tipe == 'ALL') {
        if ($nama_supp == 'ALL') {
            $where3 = "where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null group by a.bpbno_int order by bpbdate";
            $where2 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null group by a.bpbno_int order by bpbdate";
            $where1 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null group by a.bpbno_int order by bpbdate";
        }else{
            $where3 = " where bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbno_int LIKE '%GEN%' and d.tipe_sup = 'S' AND a.bpbdate between '$start_date' and '$end_date' and tipe_com is null and supplier = '$nama_supp' group by a.bpbno_int order by bpbdate";
            $where2 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('C') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' group by a.bpbno_int order by bpbdate";
            $where1 = "where bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com != 'FOC' and supplier = '$nama_supp' || bpbdate between '$start_date' and '$end_date' and d.tipe_sup = 'S' and left(bpbno,1) in ('A','F','B') and left(bpbno,2)!='FG' and tipe_com is null and supplier = '$nama_supp' group by a.bpbno_int order by bpbdate";
        }

        $sql1  = "(select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,concat(s.itemdesc,' ',add_info) itemdesc,s.color,s.size, sum(a.qty) qty,sum(a.qty-coalesce(a.qty_reject,0)) as qty_good,sum(coalesce(a.qty_reject,0)) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, SUM((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where1)";

        $sql2 = "(select a.id, if(bpbno_int!='',bpbno_int,bpbno) bpbno,bpbdate,invno,jenis_dok,right(nomor_aju,6) no_aju,tanggal_aju, lpad(bcno,6,'0') bcno,bcdate,d.supplier,a.pono,z.tipe_com,a.id_item,s.itemdesc itemdesc,s.color,s.size, sum(a.qty) qty,sum(a.qty-coalesce(a.qty_reject,0)) as qty_good,sum(coalesce(a.qty_reject,0)) as qty_reject, a.unit,tmpjo.kpno ws,tmpjo.styleno,a.curr,z.tax,a.price, SUM((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item left join (select brh.bpbno as nomorbpb, id_jo, id_item, id_rak_loc, group_concat(distinct kode_rak , ' ', nama_rak) rak from bpb_roll_h brh inner join bpb_roll br on brh.id = br.id_h inner join master_rak mr on br.id_rak_loc = mr.id group by brh.bpbno, id_jo, id_item) lr on a.bpbno = lr.nomorbpb and a.id_item = lr.id_item and a.id_jo = lr.id_jo inner join mastersupplier d on a.id_supplier=d.id_supplier LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft where po_header.app = 'A') z on a.pono = z.pono left join (select id_jo,kpno,styleno from act_costing ac inner join so on ac.id=so.id_cost inner join jo_det jod on so.id=jod.id_so group by id_jo) tmpjo on tmpjo.id_jo=a.id_jo left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where2)";

        $sql3 = "(select a.id, if(a.bpbno_int!='',a.bpbno_int,a.bpbno) bpbno,a.bpbdate,a.invno,a.jenis_dok,right(a.nomor_aju,6) no_aju,a.tanggal_aju, lpad(a.bcno,6,'0') bcno,a.bcdate,d.supplier,a.pono,z.tipe_com,a.id_item, CONCAT(s.itemdesc,' ',s.add_info) itemdesc,s.color,s.size, sum(a.qty) qty,sum(a.qty-coalesce(a.qty_reject,0)) as qty_good,sum(coalesce(a.qty_reject,0)) as qty_reject,a.unit, '' ws, '' styleno,a.curr,z.tax,a.price, SUM((a.qty-coalesce(a.qty_reject,0)) * a.price) dpp, SUM(((a.qty-coalesce(a.qty_reject,0)) * a.price) + (((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100)) total, (SUM((a.qty-coalesce(a.qty_reject,0)) * a.price) * z.tax /100) ppn, a.jenis_trans, bn.upt_no_inv,bn.upt_tgl_inv,bn.upt_no_faktur, bn.upt_tgl_faktur from bpb a inner join masteritem s on a.id_item=s.id_item LEFT join (select pono,tipe_com,po_header.tax from po_header_draft inner join po_header on po_header_draft.id = po_header.id_draft) z on a.pono = z.pono LEFT join mastersupplier d on a.id_supplier=d.id_supplier LEFT OUTER JOIN mapping_category AS m ON s.n_code_category=m.n_id left join (select no_bpb,upt_dok_inv,upt_dok_faktur,upt_no_inv,upt_tgl_inv,IF(upt_no_faktur is null OR upt_no_faktur = '',upt_no_faktur2,upt_no_faktur) upt_no_faktur, IF(upt_no_faktur is null OR upt_no_faktur = '',upt_tgl_faktur2,upt_tgl_faktur) upt_tgl_faktur FROM bpb_new where tgl_bpb between '$start_date' and '$end_date' GROUP BY no_bpb) bn on bn.no_bpb = a.bpbno_int $where3)";

        $sql = mysqli_query($conn1,"$sql1 UNION $sql2 UNION $sql3");


    }

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

        if ($price > 0) {
        echo '<tr style="font-size:12px;text-align:center;">
            <td style="width: 150px;" value = "'.$row['bpbno'].'">'.$row['bpbno'].'</td>
            <td style="width: 100px;" value = "'.$row['bpbdate'].'">'.date("d-M-Y",strtotime($row['bpbdate'])).'</td>
            <td style="width: 150px;" value = "'.$row['invno'].'">'.$row['invno'].'</td>
            <td style="width: 150px;" value = "'.$row['jenis_dok'].'">'.$row['jenis_dok'].'</td>
            <td style="width: 150px;" value = "'.$row['no_aju'].'">'.$row['no_aju'].'</td>
            <td style="width: 150px;" value = "'.$tgl_aju.'">'.$tgl_aju.'</td>
            <td style="width: 150px;" value = "'.$row['bcno'].'">'.$row['bcno'].'</td>
            <td style="width: 150px;" value = "'.$row['bcdate'].'">'.date("d-M-Y",strtotime($row['bcdate'])).'</td>
            <td style="width: 150px;" value = "'.$row['supplier'].'">'.$row['supplier'].'</td>
            <td style="width: 150px;" value = "'.$row['pono'].'">'.$row['pono'].'</td>
            <td style="width: 150px;" value = "'.$row['tipe_com'].'">'.$row['tipe_com'].'</td>
            <td style="width: 150px;" value = "'.$asal.'">'.$asal.'</td>
            <td style="width: 150px;" value = "'.$row['id_item'].'">'.$row['id_item'].'</td>
            <td style="width: 150px;" value = "'.$row['itemdesc'].'">'.$row['itemdesc'].'</td>
            <td style="width: 150px;" value = "'.$row['color'].'">'.$row['color'].'</td>
            <td style="width: 150px;" value = "'.$row['size'].'">'.$row['size'].'</td>
            <td style="width:50px; text-align : right;" value="'.$row['qty'].'">'.number_format($row['qty'],2).'</td>
            <td style="width:50px; text-align : right;" value="'.$row['qty_good'].'">'.number_format($row['qty_good'],2).'</td>
            <td style="width:50px; text-align : right;" value="'.$row['qty_reject'].'">'.number_format($row['qty_reject'],2).'</td>
            <td style="width: 150px;" value = "'.$row['unit'].'">'.$row['unit'].'</td>
            <td style="width: 150px;" value = "'.$row['ws'].'">'.$row['ws'].'</td>
            <td style="width: 150px;" value = "'.$row['styleno'].'">'.$row['styleno'].'</td>
            <td style="width: 150px;" value = "'.$row['curr'].'">'.$row['curr'].'</td>
            <td style="width:50px; text-align : right;" value="'.$row['price'].'">'.number_format($row['price'],2).'</td>
            <td style="width:50px; text-align : right;" value="'.$row['dpp'].'">'.number_format($row['dpp'],2).'</td>
            <td style="width:50px; text-align : right;" value="'.$rates.'">'.number_format($rates,2).'</td>
            <td style="width:50px; text-align : right;" value="'.$dpp_idr.'">'.number_format($dpp_idr,2).'</td>
            <td style="width:50px; text-align : right;" value="'.$ppn_idr.'">'.number_format($ppn_idr,2).'</td>
            <td style="width:50px; text-align : right;" value="'.$ttl_idr.'">'.number_format($ttl_idr,2).'</td>
            <td style="width: 150px;" value = "'.$row['jenis_trans'].'">'.$row['jenis_trans'].'</td>
            <td style="width: 150px;" value = "'.$no_inv.'">'.$no_inv.'</td>
            <td style="width: 100px;" value = "'.$tgl_inv.'">'.$tgl_inv.'</td>
            <td style="width: 150px;" value = "'.$no_faktur.'">'.$no_faktur.'</td>
            <td style="width: 100px;" value = "'.$tgl_faktur.'">'.$tgl_faktur.'</td>';
            echo '</tr>';
        }else{ echo ''; }
}?>

</tbody>                    
</table>
</div>
   
    </div>
    </div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_bpb"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                     
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content 
  </div>
      /.modal-dialog 
    </div> -->         
                                
</div><!-- body-row END -->
</div>
</div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
    <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
  <script>
  // Hide submenus
$('#body-row .collapse').collapse('hide'); 

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left'); 

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
    
    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }
    
    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>
<!-- <script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
    var divbody = document.getElementById("form-data").clientWidth;
    document.getElementById('inp_width').value=divbody;
    var divWidth = document.getElementById("sidebar-container").clientWidth; 
    document.getElementById('div_width').value=divWidth;
    var fil_width = document.getElementById('fil_width').value;
    if (fil_width <= 0) {
    document.getElementById('submit').click();
    }else{ 
    }
});
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<script type="text/javascript">
    document.getElementById('ekspor').onclick = function () {
        <?php
        $nama_tipe = $_POST['nama_tipe'];
        $nama_supp = $_POST['nama_supp'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        ?>
    location.href = "ekspor_lap_pembelian_global.php?status=<?= $nama_tipe;?>&nama_supp=<?= $nama_supp;?>&start_date=<?= $start_date;?>&end_date=<?= $end_date;?>";
};
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#active", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'activebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Active");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#deactive", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'deactivebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Deactive");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>


<script type="text/javascript">
    document.getElementById('rpt_det').onclick = function () {
    location.href = "laporan_pembelian.php";
};
</script>

<script type="text/javascript">
    document.getElementById('rpt_glo').onclick = function () {
    location.href = "laporan_pembelian_global.php";
};
</script>

<script>
function alert_cancel() {
  alert("Master Bank Deactive");
  location.reload();
}
function alert_approve() {
  alert("Master Bank Active");
  location.reload();
}
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>