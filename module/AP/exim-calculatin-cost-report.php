<?php include '../header.php' ?>
<style >
    .modal {
      text-align: center;
      padding: 0!important;
  }

  .modal:before {
      content: '';
      display: inline-block;
      height: 100%;
      vertical-align: middle;
      margin-right: -4px;
  }

  .modal-dialog {
      display: inline-table;
      width: 700px;
      text-align: left;
      vertical-align: middle;
  }
</style>
<!-- MAIN -->
<div class="col p-4">
    <h2 class="text-center">EXIM CALCULATION COST REPORT</h2>
    <div class="box">
        <div class="box header">

            <form id="form-data" action="exim-calculatin-cost-report.php" method="post">        
                <div class="form-row">

                  <div class="col-md-4">
                    <label for="nama_type"><b>Buyer</b></label>            
                    <select style="background-color: gray;" class="form-control selectpicker" name="nama_buyer" id="nama_buyer" data-dropup-auto="false" data-live-search="true" required>
                     <option value="ALL"  selected="true">ALL</option> 
                     <?php
                     $nama_buyer ='';
                     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $nama_buyer = isset($_POST['nama_buyer']) ? $_POST['nama_buyer']: null;
                    }                 
                    $sql = mysql_query("select distinct(Supplier) buyer from mastersupplier where tipe_sup = 'C' order by Supplier ASC",$conn1);
                    while ($row = mysql_fetch_array($sql)) {
                        $data = $row['buyer'];
                        if($row['buyer'] == $_POST['nama_buyer']){
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
               placeholder="Tanggal Awal">
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
           placeholder="Tanggal Awal">
       </div>
       <div class="input-group-append col">                                   
        <button  type="submit" id="submit" value=" Search " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
        line-height: 1;
        padding: -2px 8px;
        font-size: 1rem;
        text-align: center;
        color: #fff;
        text-shadow: 1px 1px 1px #000;
        border-radius: 6px;
        background-color: rgb(46, 139, 87);"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
<!--             <button type="button" id="reset" value=" Reset " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button> -->

    <?php
        // $status = isset($_POST['status']) ? $_POST['status']: null;
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    $tanggal_awal = date("Y-m-d",strtotime($start_date));
    $tanggal_akhir = date("Y-m-d",strtotime($end_date)); 
    $tanggal1 = isset($tanggal_awal) ? $tanggal_awal : 0;
    $tanggal2 = isset($tanggal_akhir) ? $tanggal_akhir : 0;
    $kata_awal = date("M",strtotime($start_date));
    $tengah = '_';
    $kata_akhir = date("Y",strtotime($start_date));
    $kata_filter = $kata_awal . $tengah . $kata_akhir;
    $nama_buyer = isset($_POST['nama_buyer']) ? $_POST['nama_buyer']: null;

    if ($nama_buyer == 'ALL') {

        echo '<a style="padding-right: 10px;" target="_blank" href="ekspor-exim-calculatin-cost-all.php?start_date='.$start_date.' && end_date='.$end_date.'"><button type="button" class="btn btn-info " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

        ';
    }else{
        echo '<a style="padding-right: 10px;" target="_blank" href="ekspor-exim-calculatin-cost.php?start_date='.$start_date.' && end_date='.$end_date.' && nama_buyer='.$nama_buyer.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel</i></button></a>

        ';
    }
        //<a style="padding-right: 5px;" target="_blank" href="ekspor_sfp_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SFP</i></button></a>

        // <a style="padding-right: 5px;" target="_blank" href="ekspor_spl_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SPL</i></button></a>

        //     <a style="padding-left: 10px";><button type="button" class="btn btn-info " name="co_sal" id="co_sal" style= "margin-top: 30px;"><i class="fa fa-clipboard" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Copy Saldo</i></button></a>


    ?>  

</div>                                                            
</div>
<br/>
</div>
</form> 

<!-- <?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>
            <button id="btnupload" type="button" class="btn-success btn-xs" style="border-radius: 6%"><span class="fa fa-upload" aria-hidden="true"></span> Upload</button>';
        }else{
    echo '';
    }
?> -->
<div class="box body">
    <div class="row">       
        <div class="col-md-12">      
           <div class="tableFix" style="height: 450px;">        
            <table id="mytable2" class="table table-striped table-bordered text-nowrap" role="grid" cellspacing="0" width="100%">
                <thead>
                    <tr class="thead-dark">
                        <th style="text-align: center;vertical-align: middle;width: 10%;">No Memo</th>
                        <th style="text-align: center;vertical-align: middle;width: 10%;">Memo Date</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Supplier</th>
                        <th style="text-align: center;vertical-align: middle;width: 9%;">Buyer</th>
                        <th style="text-align: center;vertical-align: middle;width: 9%;">Season</th>
                        <th style="text-align: center;vertical-align: middle;width: 9%;">No Invoice</th>
                        <th style="text-align: center;vertical-align: middle;width: 9%;">Invoice Date</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Qty</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">PPJK/Customs Brokerage</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Storage</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Forwarder/Shipping line</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Insurance</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Disperindag</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">PPN</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Custom Bond</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Iuran APKB</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Courier</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">PDRI</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">VAT</th>
                        <th style="text-align: center;vertical-align: middle;width: 8%;">Total</th>

                    </tr>
                </thead>
            </tbody>

            <?php
            $nama_buyer ='';
            $start_date ='';
            $end_date =''; 
            $date_now = date("Y-m-d");  
            $tanggal_awal = date("Y-m-d",strtotime($date_now ));
            $tanggal_akhir = date("Y-m-d",strtotime($date_now ));
            $bulan_awal = date("m",strtotime($date_now));
            $bulan_akhir = date("m",strtotime($date_now));  
            $tahun_awal = date("Y",strtotime($date_now));
            $tahun_akhir = date("Y",strtotime($date_now));
            $kata_awal = date("M",strtotime($date_now));
            $tengah = '_';
            $kata_akhir = date("Y",strtotime($date_now));
            $kata_filter = $kata_awal . $tengah . $kata_akhir;
            $kata_filter2 = $kata_awal . $tengah . $kata_akhir;          
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_buyer = isset($_POST['nama_buyer']) ? $_POST['nama_buyer']: null;
                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                $end_date = date("Y-m-d",strtotime($_POST['end_date']));   
                $tanggal_awal = date("Y-m-d",strtotime($_POST['start_date']));
                $tanggal_akhir = date("Y-m-d",strtotime($_POST['end_date'])); 

                $bulan_awal = date("m",strtotime($_POST['start_date']));
                $bulan_akhir = date("m",strtotime($_POST['end_date']));  
                $tahun_awal = date("Y",strtotime($_POST['start_date']));
                $tahun_akhir = date("Y",strtotime($_POST['end_date']));

                $kata_awal = date("M",strtotime($_POST['start_date']));
                $tengah = '_';
                $kata_akhir = date("Y",strtotime($_POST['start_date']));
                $kata_filter = $kata_awal . $tengah . $kata_akhir;         
            }

            if ($nama_buyer == 'ALL') {
                $sql = mysqli_query($conn2,"select a.id_h, a.nm_memo, tgl_memo, nama_supp, buyer, season_desc, id_book, no_invoice, tgl_invoice, a.qty, round(ttl_ctg1 / b.qty,2) ttl_ctg1, round(ttl_ctg2 / b.qty,2) ttl_ctg2, round(ttl_ctg3 / b.qty,2) ttl_ctg3, round(ttl_ctg4 / b.qty,2) ttl_ctg4, round(ttl_ctg5 / b.qty,2) ttl_ctg5, round(ttl_ctg6 / b.qty,2) ttl_ctg6, round(ttl_ctg7 / b.qty,2) ttl_ctg7, round(ttl_ctg8 / b.qty,2) ttl_ctg8, round(ttl_ctg9 / b.qty,2) ttl_ctg9, round(ttl_ctg10 / b.qty,2) ttl_ctg10, round(ttl_vat / b.qty,2) ttl_vat from (select a.*, round(qty * ctg1,2) ttl_ctg1, round(qty * ctg2,2) ttl_ctg2, round(qty * ctg3,2) ttl_ctg3, round(qty * ctg4,2) ttl_ctg4, round(qty * ctg5,2) ttl_ctg5, round(qty * ctg6,2) ttl_ctg6, round(qty * ctg7,2) ttl_ctg7, round(qty * ctg8,2) ttl_ctg8, round(qty * ctg9,2) ttl_ctg9, round(qty * ctg10,2) ttl_ctg10, round(qty * vat,2) ttl_vat from (SELECT a.*, season_desc, qty, DATE(sj_date) tgl_invoice FROM (select a.id_h,a.nm_memo, a.tgl_memo, b.Supplier nama_supp, c.Supplier buyer, d.id_book, d.no_invoice from memo_h a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier INNER JOIN mastersupplier c on c.id_supplier = a.id_buyer INNER JOIN memo_inv d on d.id_h = a.id_h where jns_inv = 'INVOICE' and a.status != 'CANCEL') a left join (select a.*,if(sn.season_desc is null, '-', sn.season_desc) season_desc from (select b.no_invoice,a.id_book_invoice, a.sj_date, a.so_number,sum(qty) qty from tbl_invoice_detail a INNER JOIN tbl_book_invoice b on b.id = a.id_book_invoice where b.status != 'Cancel' GROUP BY a.id_book_invoice) a inner join so on so.so_no = a.so_number left join masterseason sn on sn.id_season = so.id_season GROUP BY a.id_book_invoice) b on b.id_book_invoice = a.id_book) a left join (select a.*, COALESCE(b.biaya,0) vat from (SELECT
                  id_h,
                  Coalesce(MAX(CASE WHEN id_ctg = '1' THEN biaya END),0) AS ctg1,
                  Coalesce(MAX(CASE WHEN id_ctg = '2' THEN biaya END),0) AS ctg2,
                  Coalesce(MAX(CASE WHEN id_ctg = '3' THEN biaya END),0) AS ctg3,
                  Coalesce(MAX(CASE WHEN id_ctg = '4' THEN biaya END),0) AS ctg4,
                  Coalesce(MAX(CASE WHEN id_ctg = '5' THEN biaya END),0) AS ctg5,
                  Coalesce(MAX(CASE WHEN id_ctg = '6' THEN biaya END),0) AS ctg6,
                  Coalesce(MAX(CASE WHEN id_ctg = '7' THEN biaya END),0) AS ctg7,
                  Coalesce(MAX(CASE WHEN id_ctg = '8' THEN biaya END),0) AS ctg8,
                  Coalesce(MAX(CASE WHEN id_ctg = '9' THEN biaya END),0) AS ctg9,
                  Coalesce(MAX(CASE WHEN id_ctg = '10' THEN biaya END),0) AS ctg10
                  FROM (select id_h, id_ctg, nm_ctg, round(SUM(biaya),2) biaya from memo_det where cancel = 'N' and nm_sub_ctg != 'VAT' GROUP BY id_h, id_ctg) a
                  GROUP BY id_h) a LEFT JOIN (select id_h, round(SUM(biaya),2) biaya from memo_det where cancel = 'N' and nm_sub_ctg = 'VAT' GROUP BY id_h) b on b.id_h = a.id_h) b on b.id_h = a.id_h) a INNER JOIN (SELECT id_h, nm_memo, SUM(qty) qty FROM (select a.id_h,a.nm_memo, a.tgl_memo, b.Supplier nama_supp, c.Supplier buyer, d.id_book from memo_h a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier INNER JOIN mastersupplier c on c.id_supplier = a.id_buyer INNER JOIN memo_inv d on d.id_h = a.id_h where jns_inv = 'INVOICE' and a.status != 'CANCEL') a left join (select a.*,if(sn.season_desc is null, '-', sn.season_desc) season_desc from (select b.no_invoice,a.id_book_invoice, a.so_number,sum(qty) qty from tbl_invoice_detail a INNER JOIN tbl_book_invoice b on b.id = a.id_book_invoice where b.status != 'Cancel' GROUP BY a.id_book_invoice) a inner join so on so.so_no = a.so_number left join masterseason sn on sn.id_season = so.id_season GROUP BY a.id_book_invoice) b on b.id_book_invoice = a.id_book GROUP BY id_h) b on b.nm_memo = a.nm_memo where tgl_memo BETWEEN '$start_date' and '$end_date'");
            }else{
                $sql = mysqli_query($conn2,"select a.id_h, a.nm_memo, tgl_memo, nama_supp, buyer, season_desc, id_book, no_invoice, tgl_invoice, a.qty, round(ttl_ctg1 / b.qty,2) ttl_ctg1, round(ttl_ctg2 / b.qty,2) ttl_ctg2, round(ttl_ctg3 / b.qty,2) ttl_ctg3, round(ttl_ctg4 / b.qty,2) ttl_ctg4, round(ttl_ctg5 / b.qty,2) ttl_ctg5, round(ttl_ctg6 / b.qty,2) ttl_ctg6, round(ttl_ctg7 / b.qty,2) ttl_ctg7, round(ttl_ctg8 / b.qty,2) ttl_ctg8, round(ttl_ctg9 / b.qty,2) ttl_ctg9, round(ttl_ctg10 / b.qty,2) ttl_ctg10, round(ttl_vat / b.qty,2) ttl_vat from (select a.*, round(qty * ctg1,2) ttl_ctg1, round(qty * ctg2,2) ttl_ctg2, round(qty * ctg3,2) ttl_ctg3, round(qty * ctg4,2) ttl_ctg4, round(qty * ctg5,2) ttl_ctg5, round(qty * ctg6,2) ttl_ctg6, round(qty * ctg7,2) ttl_ctg7, round(qty * ctg8,2) ttl_ctg8, round(qty * ctg9,2) ttl_ctg9, round(qty * ctg10,2) ttl_ctg10, round(qty * vat,2) ttl_vat from (SELECT a.*, season_desc, qty, DATE(sj_date) tgl_invoice FROM (select a.id_h,a.nm_memo, a.tgl_memo, b.Supplier nama_supp, c.Supplier buyer, d.id_book, d.no_invoice from memo_h a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier INNER JOIN mastersupplier c on c.id_supplier = a.id_buyer INNER JOIN memo_inv d on d.id_h = a.id_h where jns_inv = 'INVOICE' and a.status != 'CANCEL') a left join (select a.*,if(sn.season_desc is null, '-', sn.season_desc) season_desc from (select b.no_invoice,a.id_book_invoice, a.sj_date, a.so_number,sum(qty) qty from tbl_invoice_detail a INNER JOIN tbl_book_invoice b on b.id = a.id_book_invoice where b.status != 'Cancel' GROUP BY a.id_book_invoice) a inner join so on so.so_no = a.so_number left join masterseason sn on sn.id_season = so.id_season GROUP BY a.id_book_invoice) b on b.id_book_invoice = a.id_book) a left join (select a.*, COALESCE(b.biaya,0) vat from (SELECT
                  id_h,
                  Coalesce(MAX(CASE WHEN id_ctg = '1' THEN biaya END),0) AS ctg1,
                  Coalesce(MAX(CASE WHEN id_ctg = '2' THEN biaya END),0) AS ctg2,
                  Coalesce(MAX(CASE WHEN id_ctg = '3' THEN biaya END),0) AS ctg3,
                  Coalesce(MAX(CASE WHEN id_ctg = '4' THEN biaya END),0) AS ctg4,
                  Coalesce(MAX(CASE WHEN id_ctg = '5' THEN biaya END),0) AS ctg5,
                  Coalesce(MAX(CASE WHEN id_ctg = '6' THEN biaya END),0) AS ctg6,
                  Coalesce(MAX(CASE WHEN id_ctg = '7' THEN biaya END),0) AS ctg7,
                  Coalesce(MAX(CASE WHEN id_ctg = '8' THEN biaya END),0) AS ctg8,
                  Coalesce(MAX(CASE WHEN id_ctg = '9' THEN biaya END),0) AS ctg9,
                  Coalesce(MAX(CASE WHEN id_ctg = '10' THEN biaya END),0) AS ctg10
                  FROM (select id_h, id_ctg, nm_ctg, round(SUM(biaya),2) biaya from memo_det where cancel = 'N' and nm_sub_ctg != 'VAT' GROUP BY id_h, id_ctg) a
                  GROUP BY id_h) a LEFT JOIN (select id_h, round(SUM(biaya),2) biaya from memo_det where cancel = 'N' and nm_sub_ctg = 'VAT' GROUP BY id_h) b on b.id_h = a.id_h) b on b.id_h = a.id_h) a INNER JOIN (SELECT id_h, nm_memo, SUM(qty) qty FROM (select a.id_h,a.nm_memo, a.tgl_memo, b.Supplier nama_supp, c.Supplier buyer, d.id_book from memo_h a INNER JOIN mastersupplier b on b.id_supplier = a.id_supplier INNER JOIN mastersupplier c on c.id_supplier = a.id_buyer INNER JOIN memo_inv d on d.id_h = a.id_h where jns_inv = 'INVOICE' and a.status != 'CANCEL') a left join (select a.*,if(sn.season_desc is null, '-', sn.season_desc) season_desc from (select b.no_invoice,a.id_book_invoice, a.so_number,sum(qty) qty from tbl_invoice_detail a INNER JOIN tbl_book_invoice b on b.id = a.id_book_invoice where b.status != 'Cancel' GROUP BY a.id_book_invoice) a inner join so on so.so_no = a.so_number left join masterseason sn on sn.id_season = so.id_season GROUP BY a.id_book_invoice) b on b.id_book_invoice = a.id_book GROUP BY id_h) b on b.nm_memo = a.nm_memo where tgl_memo BETWEEN '$start_date' and '$end_date' and buyer = '$nama_buyer'");
            }

            $total_qty =0;
            $total_ctg1 =0;
            $total_ctg2 =0;
            $total_ctg3 =0;
            $total_ctg4 =0;
            $total_ctg5 =0;
            $total_ctg6 =0;
            $total_ctg7 =0;
            $total_ctg8 =0;
            $total_ctg9 =0;
            $total_ctg10 =0;
            $total_vat =0;
            $total_biaya_all =0;
            while($row2 = mysqli_fetch_array($sql)){
                $tgl_invoice = $row2['tgl_invoice'];
                if ($tgl_invoice == '0000-00-00' || $tgl_invoice == '1970-01-01' || $tgl_invoice == null) {
                    $tglinvoice = '-'; 
                }else{
                    $tglinvoice = date("Y-m-d",strtotime($tgl_invoice));
                }
                $biaya_total = $row2['ttl_ctg1'] + $row2['ttl_ctg2'] + $row2['ttl_ctg3'] + $row2['ttl_ctg4'] + $row2['ttl_ctg5'] + $row2['ttl_ctg6'] + $row2['ttl_ctg7'] + $row2['ttl_ctg8'] + $row2['ttl_ctg9'] + $row2['ttl_ctg10'] + $row2['ttl_vat'];

                $total_qty += $row2['qty'];
                $total_ctg1 += $row2['ttl_ctg1'];
                $total_ctg2 += $row2['ttl_ctg2'];
                $total_ctg3 += $row2['ttl_ctg3'];
                $total_ctg4 += $row2['ttl_ctg4'];
                $total_ctg5 += $row2['ttl_ctg5'];
                $total_ctg6 += $row2['ttl_ctg6'];
                $total_ctg7 += $row2['ttl_ctg7'];
                $total_ctg8 += $row2['ttl_ctg8'];
                $total_ctg9 += $row2['ttl_ctg9'];
                $total_ctg10 += $row2['ttl_ctg10'];
                $total_vat += $row2['ttl_vat'];
                $total_biaya_all += $biaya_total;

                echo ' <tr style="font-size:12px;text-align:center;">
                <td style="text-align : left;" value = "'.$row2['nm_memo'].'">'.$row2['nm_memo'].'</td>
                <td style="width: 100px;" value = "'.$row2['tgl_memo'].'">'.date("d-M-Y",strtotime($row2['tgl_memo'])).'</td>
                <td style="text-align : left;" value = "'.$row2['nama_supp'].'">'.$row2['nama_supp'].'</td>
                <td style="text-align : left;" value = "'.$row2['buyer'].'">'.$row2['buyer'].'</td>
                <td style="text-align : left;" value = "'.$row2['season_desc'].'">'.$row2['season_desc'].'</td>
                <td style="text-align : left;" value = "'.$row2['no_invoice'].'">'.$row2['no_invoice'].'</td>
                <td style="width: 100px;" value = "'.$row2['tgl_invoice'].'">'.$tglinvoice.'</td>
                <td style="text-align : right;" value = "'.$row2['qty'].'">'.number_format($row2['qty'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg1'].'">'.number_format($row2['ttl_ctg1'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg2'].'">'.number_format($row2['ttl_ctg2'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg3'].'">'.number_format($row2['ttl_ctg3'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg4'].'">'.number_format($row2['ttl_ctg4'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg5'].'">'.number_format($row2['ttl_ctg5'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg6'].'">'.number_format($row2['ttl_ctg6'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg7'].'">'.number_format($row2['ttl_ctg7'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg8'].'">'.number_format($row2['ttl_ctg8'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg9'].'">'.number_format($row2['ttl_ctg9'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_ctg10'].'">'.number_format($row2['ttl_ctg10'],2).'</td>
                <td style="text-align : right;" value = "'.$row2['ttl_vat'].'">'.number_format($row2['ttl_vat'],2).'</td>
                <td style="text-align : right;" value = "'.$biaya_total.'">'.number_format($biaya_total,2).'</td>
                </tr>
                ';
            }

            echo ' <tr >
            <th colspan="7" style="text-align : center;" value = "Total">Total</td>
            <th style="text-align : right;" value = "'.$total_qty.'">'.number_format($total_qty,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg1.'">'.number_format($total_ctg1,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg2.'">'.number_format($total_ctg2,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg3.'">'.number_format($total_ctg3,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg4.'">'.number_format($total_ctg4,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg5.'">'.number_format($total_ctg5,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg6.'">'.number_format($total_ctg6,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg7.'">'.number_format($total_ctg7,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg8.'">'.number_format($total_ctg8,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg9.'">'.number_format($total_ctg9,2).'</th>
            <th style="text-align : right;" value = "'.$total_ctg10.'">'.number_format($total_ctg10,2).'</th>
            <th style="text-align : right;" value = "'.$total_vat.'">'.number_format($total_vat,2).'</th>
            <th style="text-align : right;" value = "'.$total_biaya_all.'">'.number_format($total_biaya_all,2).'</th>
            </tr>
            ';


            ?>  
        </tbody>
    </table>
</div>                  
</div>

</div>
</div>
</div>
</div><!-- body-row END -->
</div>
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:450px;" class="modal-dialog modal-md">
            <div style="height: 225px" class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="Heading" style="text-align: center;"><b>UPLOAD</b></h4>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <form method="post" enctype="multipart/form-data" action="proses_upload.php">
                        Pilih File:
                        <input class="form-control" name="fileexcel" type="file" required="required">
                        <br>
                        <button class="btn btn-sm btn-info" type="submit">Submit</button>
                        <a target="_blank" href="format_upload_mj.xls"><button type="button" class="btn btn-warning "><i class="fa fa-file-excel-o" aria-hidden="true"> Format Upload</i></button></a>
                    </form>
                </div>
            </div>
        </div>
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
<!--           <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
  <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
  <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
  <!--         <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                    
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
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min.js"></script>
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

<script>
    $(document).ready(function() {
        $('#mytable').dataTable({
            'order': [1, 'asc'],
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false
        });

        $("[data-toggle=tooltip]").tooltip();

    } );

</script>

<script>
    function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("datatable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
    } else {
        tr[i].style.display = "none";
    }
}
}
}
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2023",
            autoclose:true
        });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    $("#form-data").on("click", "#co_sal", function(){ 
        var no_coa = $(this).closest('tr').find('td:eq(1)').attr('value');
        var beg_balance = $(this).closest('tr').find('td:eq(7)').attr('value');
        var debit = $(this).closest('tr').find('td:eq(8)').attr('value');
        var credit = $(this).closest('tr').find('td:eq(9)').attr('value');
        var end_balance = $(this).closest('tr').find('td:eq(10)').attr('value');
        var copy_user = '<?php echo $user ?>';
        var to_saldo = document.getElementById('to_saldo').value;

        $.ajax({
            type:'POST',
            url:'copy_saldo_tb.php',
            data: {'no_coa':no_coa, 'beg_balance':beg_balance,'debit':debit, 'credit':credit,'end_balance':end_balance, 'copy_user':copy_user,'to_saldo':to_saldo},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                // alert(response);            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
           }
       });
        alert("Copy Saldo successfully");     
    });
</script>


<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
    var date = $(this).closest('tr').find('td:eq(1)').text();
    var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
    var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
    var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
    var curr = "IDR";

    $.ajax({
    type : 'post',
    url : 'ajax_cashin.php',
    data : {'no_ib': no_ib},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_ib);
    $('#txt_tglbpb').html('Date : ' + date + '');
    $('#txt_no_po').html('Refference : ' + reff + '');
    $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "create-list-journal.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('btnupload').onclick = function () {
        location.href = "upload-list-journal.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "list-journal.php";
    };
</script>

<!-- <script type="text/javascript">     
    document.getElementById('btnupload').onclick = function (){ 
    // var txt_type = $(this).closest('tr').find('td:eq(4)').attr('value'); 
    // var txt_id = $(this).closest('tr').find('td:eq(0)').attr('value');           
    $('#mymodal2').modal('show');
    // $('#txt_type').val(txt_type);
    // $('#txt_id').val(txt_id);

};

</script> -->

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
