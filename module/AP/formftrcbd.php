<?php include '../header.php' ?>

<!-- MAIN -->
<!-- Skin kontrol form (selectpicker & input tanggal) - potongan dari
     app-skin.css. Ditaut SENDIRI dgn mtime-nya, BUKAN lewat app-skin.css:
     berkas induk itu meng-@import tanpa nomor versi, sehingga perubahan di
     berkas yang di-import tidak pernah sampai ke browser sampai mtime
     induknya ikut berubah. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">

<link rel="stylesheet" href="../css/app-ftr-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-form.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="ftr-card">
    <!-- Kepala kartu sama persis dgn menu Memorial Journal: kotak ikon kaca +
         judul + jejak menu. Judul lama berupa <h3> huruf besar di tengah
         halaman dilepas. Jumlah DIV yang DIBUKA di blok ini sengaja SAMA
         dgn sebelumnya (col p-4 / box / box header -> container / card /
         panel), supaya penutup-penutup di bawah tetap berpasangan. -->
    <div class="ftr-head">
      <span class="ftr-head-icon"><i class="fa fa-exchange" aria-hidden="true"></i></span>
      <div>
        <h1>Form Transfer Request (CBD)</h1>
        <span class="ftr-crumb">AP &rsaquo; FTR CBD &rsaquo; Create</span>
      </div>
    </div>

    <div class="ftr-panel">
            <form id="form-data" method="post">
                <div class="form-row">
                    <div class="col-12 col-sm-6 col-xl-3 mb-3">
                        <label for="noftrcbd"><b>No FTR CBD</b></label>
                        <?php
                        $sql = mysqli_query($conn2,"select max(no_ftr_cbd) from ftr_cbd where id = (select max(id) from ftr_cbd)");
                        $row = mysqli_fetch_array($sql);
                        $kodeftr = $row['max(no_ftr_cbd)'];
                        $urutan = (int) substr($kodeftr, 15, 5);
                        $urutan++;
                        $bln = date("m");
                        $thn = date("y");
                        $huruf = "FTR/C/NAG/$bln$thn/";
                        $kodeftr = $huruf . sprintf("%05s", $urutan);

                        echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="noftrcbd" name="noftrcbd" value="'.$kodeftr.'">'
                        ?>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2 mb-3">
                        <label for="tanggal"><b>FTR CBD Date <i style="color: red;">*</i></b></label>          
                        <input type="text" style="font-size: 14px;" name="tanggal" id="tanggal" class="form-control tanggal" 
                        value="<?php             
                        if(!empty($_POST['tanggal'])) {
                            echo $_POST['tanggal'];
                        }
                        else{
                            echo date("d-m-Y");
                        } ?>">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-3">
                        <label for="payment_date"><b>Payment Date<i style="color: red;">*</i></b></label>          
                        <input type="text" style="font-size: 13px;" name="payment_date" id="payment_date" class="form-control tanggal" 
                        value="<?php             
                        if(!empty($_POST['payment_date'])) {
                            echo $_POST['payment_date'];
                        }
                        else{
                            echo '-';
                        } ?>">
                    </div>

                    <!-- PAYMENT METHOD (baru, 01 Okt 2026 - Project #79).
                         Nilainya disimpan ke kolom BARU ftr_cbd.payment_method;
                         lihat db_migrate_LIVE_ftr_payment_method.sql - kolom itu
                         BELUM ada di produksi sebelum migrasi dijalankan.

                         Memakai selectpicker, BUKAN select2: halaman ini sudah
                         memuat bootstrap-select untuk Profit Center & Supplier dan
                         tidak memuat select2 sama sekali - menambah satu pustaka
                         lagi cuma untuk dua pilihan tidak sepadan. Keduanya sudah
                         diberi tampilan seragam oleh app-skin-form.css.

                         Bawaannya TRANSFER: itu cara bayar yang selama ini dipakai
                         (755 dokumen lama tidak punya nilai sama sekali), jadi Cash
                         selalu merupakan pilihan sadar, bukan kelalaian. -->
                    <div class="col-12 col-sm-6 col-xl-2 mb-3">
                        <label for="payment_method"><b>Payment Method <i style="color: red;">*</i></b></label>
                        <select class="form-control selectpicker" name="payment_method" id="payment_method" data-dropup-auto="false">
                            <?php
                            // TIDAK ada pilihan bawaan (dulu otomatis "Transfer").
                            // Permintaan user: cara bayar harus dipilih sadar, karena
                            // Cash menentukan perlakuan berikutnya - tanda tangan di
                            // cetakan dan kemungkinan tidak dibuatkan PV-AP CBD.
                            $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
                            echo '<option value="" disabled' . ($payment_method === '' ? ' selected' : '') . '>Select Payment Method</option>';
                            foreach (['Transfer', 'Cash'] as $pm) {
                                echo '<option value="' . $pm . '"' . ($pm === $payment_method ? ' selected' : '') . '>' . $pm . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- ITEM TYPE (baru, 01 Okt 2026).
                         Direkam DI SINI karena untuk FTR ber-Payment Method Cash ada
                         kemungkinan PV-AP CBD-nya tidak pernah dibuat - padahal jenis
                         item biasanya baru direkam di langkah PV itu, jadi tanpa ini
                         informasinya hilang sama sekali.

                         Pilihannya dibaca dari sumber yang SAMA dgn menu PV-AP CBD/DP
                         (pv_mapping_jurnal_dp, status = 'Y') - BUKAN daftar tetap yang
                         diketik ulang di sini, supaya tidak ada dua daftar yang bisa
                         saling melenceng kalau jenis item ditambah/dinonaktifkan. -->
                    

                </div>

                <div class="form-row">
                    <div class="col-12 col-sm-6 col-xl-3 mb-3">
                        <label for="profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>            
                        <select class="form-control selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true" onchange="updateKodeFTR()">
                            <option value="" disabled selected="true">Select Profit Center</option>                                                 
                            <?php
                            $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: null;               
                            $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['kode_pc'];
                                $data2 = $row['nama_pc'];
                                if($row['kode_pc'] == $profit_center ){
                                    $isSelected = ' selected="selected"';
                                }else{
                                    $isSelected = '';

                                }
                                echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                            }?>
                        </select>  
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2 mb-3">
                        <label for="item_type"><b>Item Type</b></label>
                        <select class="form-control selectpicker" name="item_type" id="item_type" data-dropup-auto="false" data-live-search="true">
                            <option value="" disabled selected>Select Item Type</option>
                            <?php
                            $item_type = isset($_POST['item_type']) ? $_POST['item_type'] : null;
                            $sqlIt = mysqli_query($conn1, "select item_type from pv_mapping_jurnal_dp where status = 'Y' group by item_type order by item_type");
                            while ($rowIt = mysqli_fetch_assoc($sqlIt)) {
                                $itv = $rowIt['item_type'];
                                echo '<option value="' . htmlspecialchars($itv) . '"' . ($itv === $item_type ? ' selected' : '') . '>' . htmlspecialchars($itv) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-12 col-xl-4 mb-3">
                        <label for="nama_supp"><b>Supplier</b></label>            
                        <div class="input-group">
                            <input type="text" readonly style="font-size: 14px;" class="form-control" name="txt_supp" id="txt_supp" 
                            value="<?php 
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                            echo $nama_supp; 
                        ?>">

                        <div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                                        <h4 class="modal-title" id="Heading">Choose Supplier</h4>
                                    </div>
                                    <div class="modal-body">
                                      <div class="form-group">
                                        <form id="modal-form" method="post">
                                            <label for="nama_supp"><b>Supplier</b></label>
                                            <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                                                <option value="" disabled selected="true">select</option>                
                                                <?php 
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

                                            <label><b>PO Date</b></label>
                                            <div class="input-group-append">           
                                                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="start_date" name="start_date" 
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

                                            <label class="col-md-1" for="end_date"><b>-</b></label>
                                            <input type="text" style="font-size: 14px;" class="form-control tanggal" id="end_date" name="end_date" 
                                            value="<?php
                                            $end_date ='';
                                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                              $end_date = date("Y-m-d",strtotime($_POST['start_date']));
                                          }
                                          if(!empty($_POST['end_date'])) {
                                            echo $_POST['end_date'];
                                        }
                                        else{
                                            echo date("d-m-Y");
                                        } ?>" 
                                        placeholder="Tanggal Akhir">
                                        <input type="hidden" style="font-size: 13px;" name="h_profit_center" id="h_profit_center" class="form-control form-control-sm" value="">
                                    </div>  
                                    <div class="modal-footer">
                                        <button type="submit" id="send" name="send" class="app-btn app-btn-primary app-btn-sm"><i class="fa fa-check"></i>
                                            Apply
                                        </button>
                                    </div>           
                                </form>
                            </div>
                        </div>


                    </div>
                    <!-- /.modal-content --> 
                </div>
                <!-- /.modal-dialog --> 
            </div>

                    <div class="input-group-append">
                <input type="button" name="mysupp" id="mysupp" value="Select">
                <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
            </div>
        </div>
    </div>  
                    <div class="col-12 col-xl-5 mb-3">
        <label for="memo"><b>Description</b></label>          
        <input type="text" style="font-size: 14px;" class="form-control" name="memo" id="memo" 
        value="<?php             
        if(!empty($_POST['memo'])) {
            echo $_POST['memo'];
        }
        else{
            echo '';
        } ?>">
    </div>

</div>
</form>
<div class="ftr-body">
    <div class="row">

        <div class="col-md-12">

            <table id="mytable" class="table table-striped ftr-tbl" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                <thead>
                    <tr>
                        <th style="width:10px;"></th>
                        <th style="width:50px;">NO PO</th>
                        <th style="width:100px;">NO PI</th>                            
                        <th style="width:50px;">PO Date</th>                            
                        <th style="width:100px;">SubTotal</th>
                        <th style="width:100px;">Tax (PPn)</th>                            
                        <th style="width:100px;">Total (PO)</th>
                        <th style="width:100px;">Currency</th>                            
                        <th style="width:100px;display: none;">Supplier</th>   
                        <th style="width:100px;">Amount</th>                                                       
                        <!--<th style="width:50px;">Delete</th>-->
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $start_date ='';
                    $end_date ='';
                    $sub = '';
                    $tax = '';
                    $total = ''; 
                    $profit_center = '';           
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                        $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                        $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                    }
                    $querys = mysqli_query($conn1,"select distinct pono from po_header");
                    $rows = mysqli_fetch_array($querys);
                    $pono = isset($rows['pono']) ? $rows['pono'] : null;

                    if ($profit_center == 'NAG') {
                    $sql = mysqli_query($conn1,"select a.no_po, podate, supplier, round(sub - COALESCE(subtotal,0),2) sub, round(a.tax - COALESCE(b.tax,0),2) tax, round(a.total - COALESCE(b.total,0),2) total, matauang, app, cancel, kode_pterms, tipe_com from (select po_header.pono as no_po, po_header.podate as podate, mastersupplier.Supplier as supplier, (SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) as sub, ((SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) * (po_header.tax / 100)) as tax, (SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) + ((SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) * (po_header.tax / 100)) as total, po_item.curr as matauang, po_header.app as app, po_item.cancel as cancel, masterpterms.kode_pterms, po_header_draft.tipe_com
                        from po_header 
                        inner join po_item on po_item.id_po = po_header.id
                        left join po_header_draft on po_header_draft.id = po_header.id_draft
                        left join (select id_po_draft, sum(IF(kategori = 'Plus',total,(total * -1))) total from po_add_biaya a INNER JOIN po_master_pilihan b on b.id = a.id_kategori GROUP BY id_po_draft) ad on ad.id_po_draft = po_header.id_draft
                        inner join mastersupplier on mastersupplier.Id_Supplier = po_header.id_supplier
                        inner join masterpterms on masterpterms.id = po_header.id_terms
                        where po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com IN ('','REGULAR') || po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com IS NULL || po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com = 'BUYER' group by no_po) a LEFT JOIN (select no_po, SUM(subtotal) subtotal, SUM(tax) tax, SUM(total) total from ftr_cbd where status != 'Cancel' GROUP BY no_po) b on b.no_po = a.no_po where (a.total - COALESCE(b.total,0)) != 0");
                    // var_dump("select a.no_po, podate, supplier, round(sub - COALESCE(subtotal,0),2) sub, round(a.tax - COALESCE(b.tax,0),2) tax, round(a.total - COALESCE(b.total,0),2) total, matauang, app, cancel, kode_pterms, tipe_com from (select po_header.pono as no_po, po_header.podate as podate, mastersupplier.Supplier as supplier, (SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) as sub, ((SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) * (po_header.tax / 100)) as tax, (SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) + ((SUM(po_item.qty * po_item.price) + coalesce(ad.total,0)) * (po_header.tax / 100)) as total, po_item.curr as matauang, po_header.app as app, po_item.cancel as cancel, masterpterms.kode_pterms, po_header_draft.tipe_com
                    //     from po_header 
                    //     inner join po_item on po_item.id_po = po_header.id
                    //     left join po_header_draft on po_header_draft.id = po_header.id_draft
                    //     left join (select id_po_draft, sum(IF(kategori = 'Plus',total,(total * -1))) total from po_add_biaya a INNER JOIN po_master_pilihan b on b.id = a.id_kategori GROUP BY id_po_draft) ad on ad.id_po_draft = po_header.id_draft
                    //     inner join mastersupplier on mastersupplier.Id_Supplier = po_header.id_supplier
                    //     inner join masterpterms on masterpterms.id = po_header.id_terms
                    //     where po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com IN ('','REGULAR') || po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com IS NULL || po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms = 'CBD' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com = 'BUYER' group by no_po) a LEFT JOIN (select no_po, SUM(subtotal) subtotal, SUM(tax) tax, SUM(total) total from ftr_cbd where status != 'Cancel' GROUP BY no_po) b on b.no_po = a.no_po where (a.total - COALESCE(b.total,0)) != 0");
                    while($row = mysqli_fetch_array($sql)){
                        $po = $row['no_po'];
                        $sub = $row['sub'];
                        $tax = $row['tax'];
                        $total = $row['total'];                  
                        echo '<tr>
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                        <td style="width:50px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                        <td style="width:100px;">
                        <input type="text" style="font-size: 14px;" class="form-control" id="txt_pi" name="txt_pi" value="" disabled>
                        </td>                            
                        <td style="width:100px;" value="'.$row['podate'].'">'.date("d-M-Y",strtotime($row['podate'])).'</td>                            
                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>                            
                        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                        <td style="width:50px;" value="'.$row['matauang'].'">'.$row['matauang'].'</td>                            
                        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>   
                        <td style="width:100px;">
                        <input type="text" style="font-size: 14px;" class="form-control" id="txt_amount" name="txt_amount" value="" disabled>
                        <input type="hidden" name="paid_subtotal[]" class="paid_subtotal" value="0">
                        <input type="hidden" name="paid_tax[]" class="paid_tax" value="0">
                        <input type="hidden" name="paid_total[]" class="paid_total" value="0">
                        </td>                                                                                                              
                        </tr>';
                    }  

                    }else{
                        $sql = pg_query($conn4,"select no_po, max(podate) podate, max(supplier) supplier, round(sum(sub),2) sub, round(sum(tax),2) tax, round(sum(sub + tax),2) total, max(matauang) matauang, max(app) app, max(cancel) cancel, max(kode_pterms) kode_pterms, max(tipe_com) tipe_com from (select no_po, tanggal podate, c.nama_supplier supplier, (qty * harga_per_unit) sub, ((qty * harga_per_unit) * ppn/100) tax, currency matauang, 'A' app, 'N' cancel, '-' kode_pterms, '-' tipe_com from purchase_orders a INNER JOIN purchase_order_details b on b.purchase_order_id = a.id INNER JOIN master_supplier c on c.id = a.id_supplier where status_po = 'approved') a where podate BETWEEN '$start_date' and '$end_date' and upper(supplier) = '$nama_supp' GROUP BY no_po ");

                        while($row = pg_fetch_assoc($sql)){
                        $po = $row['no_po'];
                        $sub = $row['sub'];
                        $tax = $row['tax'];
                        $total = $row['total'];                  
                        echo '<tr>
                        <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                        <td style="width:50px;" value="'.$row['no_po'].'">'.$row['no_po'].'</td>
                        <td style="width:100px;">
                        <input type="text" style="font-size: 14px;" class="form-control" id="txt_pi" name="txt_pi" value="" disabled>
                        </td>                            
                        <td style="width:100px;" value="'.$row['podate'].'">'.date("d-M-Y",strtotime($row['podate'])).'</td>                            
                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>                            
                        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                        <td style="width:50px;" value="'.$row['matauang'].'">'.$row['matauang'].'</td>                            
                        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>   
                        <td style="width:100px;">
                        <input type="text" style="font-size: 14px;" class="form-control" id="txt_amount" name="txt_amount" value="" disabled>
                        <input type="hidden" name="paid_subtotal[]" class="paid_subtotal" value="0">
                        <input type="hidden" name="paid_tax[]" class="paid_tax" value="0">
                        <input type="hidden" name="paid_total[]" class="paid_total" value="0">
                        </td>                                                                                                              
                        </tr>';
                    }  
                    }
              
                    ?>
                </tbody>                    
            </table>                    
            <div class="ftr-foot">
                <form id="form-simpan">
                    <!-- Ringkasan nilai. Dulu tiga baris isian, masing-masing
                         form-row col berisi col-md-3,
                         sehingga keterangan & angkanya menumpuk ke bawah dan
                         kotaknya selebar sepertiga halaman. Sekarang satu kotak
                         ringkas: keterangan di kiri, angka rata kanan.
                         Id #subtotal / #pajak / #total TIDAK diubah - JavaScript
                         mengisinya lewat id itu, $("#subtotal").val(...). -->
                    <div class="ftr-sum">
                        <div class="ftr-sum-row">
                            <span>Subtotal</span>
                            <input type="text" name="subtotal" id="subtotal" value="" placeholder="0.00" readonly>
                        </div>
                        <div class="ftr-sum-row">
                            <span>Tax (PPn)</span>
                            <input type="text" name="pajak" id="pajak" value="" placeholder="0.00" readonly>
                        </div>
                        <div class="ftr-sum-row is-total">
                            <span>Total</span>
                            <input type="text" name="total" id="total" value="" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="ftr-actions">
                        <button type="button" class="app-btn app-btn-primary" name="simpan" id="simpan"><i class="fa fa-floppy-o"></i> Save</button>
                        <button type="button" class="app-btn app-btn-danger" name="batal" id="batal" onclick="location.href='ftrcbd.php'"><i class="fa fa-angle-double-left"></i> Back</button>
                    </div>
                </form>
            </div>

            <div class="modal fade" id="mymodalpo" data-target="#mymodalpo" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                            <h4 class="modal-title" id="txt_po"></h4>
                        </div>
                        <div class="container">
                            <div class="row">
                              <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
                              <div id="txt_supp2" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>        
                              <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                            
                              <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
                          </div>
                      </div>
                  </div>
                  <!-- /.modal-content --> 
              </div>
              <!-- /.modal-dialog --> 
          </div>         

      </div><!-- body-row END -->
  </div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<!-- SweetAlert2: menggantikan seluruh alert() bawaan browser di halaman ini.
     header.php tidak memuat pustaka JS apa pun, jadi tiap halaman memuat
     sendiri yang dibutuhkannya. -->
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

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

<script>
    $(document).ready(function() {
        $('#mytable').dataTable();
        // Hanya KOTAK TABELNYA yang digulir mendatar, bukan seluruh
        // pembungkus - dgn begitu Show entries, Search & paging tetap
        // berada di lebar halaman dan tidak ikut bergeser.
        $('#mytable').parent().addClass('ftr-scroll');

        $("[data-toggle=tooltip]").tooltip();

    } );
</script>

<script type="text/javascript">
/* Dipakai dua blok <script>: pengatur batas kalender di bawah DAN
   pemeriksaan saat tombol Save ditekan. Karena itu ditaruh di luar
   $(document).ready() - di dalamnya, namanya tidak dikenal blok lain. */
function ftrAngkaTgl(s) {            // 'dd-mm-yyyy' -> 'yyyymmdd'
    var p = String(s || '').split('-');
    return (p.length === 3) ? (p[2] + p[1] + p[0]) : '';
}

    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });

        /* Payment Date tidak boleh mendahului FTR Date.
           Batas bawah kalendernya dikunci ke FTR Date, dan ikut bergeser
           kalau FTR Date-nya diubah. Kalau Payment Date yang sudah terisi
           ternyata lebih awal, isiannya DIKOSONGKAN - kalau dibiarkan,
           angka lama itu tetap terkirim walau kalendernya sudah dibatasi. */
        var $tglFtr   = $('#tanggal');
        var $tglBayar = $('#payment_date');

        function ftrSelaraskanBatas(kosongkanKalauLebihAwal) {
            var vFtr = $tglFtr.val();
            if (!vFtr || vFtr === '-') { return; }
            $tglBayar.datepicker('setStartDate', vFtr);

            var vBayar = $tglBayar.val();
            if (kosongkanKalauLebihAwal && vBayar && vBayar !== '-'
                && ftrAngkaTgl(vBayar) < ftrAngkaTgl(vFtr)) {
                $tglBayar.val('');
            }
        }

        ftrSelaraskanBatas(false);      // saat halaman dibuka: batasi saja
        $tglFtr.on('changeDate change', function () { ftrSelaraskanBatas(true); });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });

     document.addEventListener("DOMContentLoaded", function() {
        let savedPc = localStorage.getItem("profit_center");
        if (savedPc) {
            document.getElementById("profit_center").value = savedPc;
            updateKodeFTR();
        }
    });

    document.getElementById("profit_center").addEventListener("change", function() {
        localStorage.setItem("profit_center", this.value);
        document.querySelector("#mytable tbody").innerHTML = "";
    });


    $(document).ready(function() {
        $("#mysupp").on("click", function() {
            let profit_center = $('select[name=profit_center] option').filter(':selected').val();

            if(profit_center == ""){
                Swal.fire({ icon: 'warning', title: 'Profit Center is required',
                    text: 'Please select a Profit Center first.' })
                    .then(function () { $("#profit_center").focus(); });
                return;
            }

            $('#h_profit_center').val(profit_center);

            $("#mymodal").modal("show");
        });
    });

function updateKodeFTR() {
    const profitCenter = document.getElementById('profit_center').value;
    const input = document.getElementById('noftrcbd');
    let currentVal = input.value.trim();

    if (currentVal === '') return;

    // Pisahkan berdasarkan "/"
    let parts = currentVal.split('/');

    // Pastikan format minimal "FTR/C/{PC}/{TAHUN}/{NOMOR}"
    if (parts.length >= 5) {
        parts[2] = profitCenter; // ubah bagian profit center
        input.value = parts.join('/');
    }
}

</script>


<script type="text/javascript">
    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
      try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
};
/* Menghitung ulang nilai terbayar SATU baris dari isi kotak Amount-nya.
   Dipakai BERSAMA oleh pengetikan Amount dan pencentangan baris, supaya
   keduanya memakai rumus yang sama persis. */
function hitungBaris($row) {
    var max_po  = parseFloat($row.find('td:eq(6)').attr('data-total')) || 0;
    var $amount = $row.find('td:eq(9) input[name=txt_amount]');
    var nilai   = parseFloat($amount.val()) || 0;

    // Tidak boleh melebihi Total (PO) - aturan yang sudah ada sebelumnya.
    if (nilai > max_po) { nilai = max_po; $amount.val(max_po.toFixed(2)); }

    var price = parseFloat($row.find('td:eq(4)').attr('data-subtotal')) || 0;
    var tax   = parseFloat($row.find('td:eq(5)').attr('data-tax')) || 0;
    var total_po = price + tax;
    var persen = total_po > 0 ? (nilai / total_po) : 0;

    $row.find(".paid_subtotal").val(price * persen);
    $row.find(".paid_tax").val(tax * persen);
    $row.find(".paid_total").val((price * persen) + (tax * persen));
}

/* Menjumlahkan HANYA baris yang dicentang. */
function hitungSemua() {
    var sum_sub = 0, sum_tax = 0, sum_total = 0;
    $("input[name='select[]']:checked").each(function () {
        var $row = $(this).closest('tr');
        sum_sub   += parseFloat($row.find(".paid_subtotal").val()) || 0;
        sum_tax   += parseFloat($row.find(".paid_tax").val()) || 0;
        sum_total += parseFloat($row.find(".paid_total").val()) || 0;
    });
    $("#subtotal").val(formatMoney(sum_sub));
    $("#pajak").val(formatMoney(sum_tax));
    $("#total").val(formatMoney(sum_total));
}

$(document).on('change', "input[name='select[]']", function () {
    var $row    = $(this).closest('tr');
    var $pi     = $row.find('td:eq(2) input');
    var $amount = $row.find('td:eq(9) input');

    if (this.checked) {
        // AMOUNT LANGSUNG TERISI Total (PO) - permintaan user. Tetap bisa
        // diedit: itu nilai awal, bukan kunci. Pembayaran penuh yang paling
        // sering terjadi, jadi angka itu yang paling sering sudah benar.
        var total_po = parseFloat($row.find('td:eq(6)').attr('data-total')) || 0;
        $pi.prop('disabled', false);
        $amount.prop('disabled', false).val(total_po.toFixed(2));
    } else {
        // Centang dilepas -> isian dikosongkan DAN nilai tersembunyinya
        // dinolkan. Sebelumnya paid_* baris itu tertinggal, sehingga ringkasan
        // di bawah masih ikut menghitung baris yang sudah tidak dipilih.
        $pi.prop('disabled', true).val('');
        $amount.prop('disabled', true).val('');
        $row.find(".paid_subtotal, .paid_tax, .paid_total").val(0);
    }

    hitungBaris($row);
    hitungSemua();
});

$(document).on('keyup input', "input[name=txt_amount]", function () {
    hitungBaris($(this).closest('tr'));
    hitungSemua();
});




</script>

<!--<script type="text/javascript">
    $("#form-data").on("click", "#send", function(){
        var datas= $('select[name=nama_supp] option').filter(':selected').val();
        var start_date= $('#start_date').attr('value');
        var end_date= $('#start_date').attr('value');
        $.ajax({
            type:'POST',
            url:'cek.php',
            data: {'nama_supp':datas, 'start_date': start_date, 'end_date': end_date},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                alert(response);
            },
            error:  function (xhr, ajaxOptions, thrownError) {
                alert(xhr);
            }
        });
    });
</script>-->

<!-- <script type="text/javascript">
    $("input[type=radio]:checked").click(function(){        
    $(this).closest('tr').find('td:eq(2) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(2) input').val("");        
    })
</script> -->

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function () {
        var $dipilih       = $("input[name='select[]']:checked");
        var payment_method = document.getElementById('payment_method').value;
        var item_type      = document.getElementById('item_type').value;
        var tgl_bayar      = document.getElementById('payment_date').value;

        /* SELURUH pemeriksaan dijalankan SEBELUM satu baris pun dikirim.
           Versi sebelumnya memeriksa Payment Date & "PO sudah dicentang"
           SESUDAH $.each() - padahal di dalamnya tiap baris sudah terlanjur
           dikirim ke server, jadi peringatannya muncul setelah datanya masuk.
           Pesan "Data saved successfully" pun dulu selalu muncul, bahkan ketika
           tidak ada satu pun baris yang tersimpan. */
        if ($dipilih.length === 0) {
            Swal.fire({ icon: 'warning', title: 'No PO selected',
                text: 'Tick at least one PO row before saving.' });
            return;
        }
        if (tgl_bayar === '' || tgl_bayar === '-') {
            Swal.fire({ icon: 'warning', title: 'Payment Date is required',
                text: 'Please fill in the Payment Date.' })
                .then(function () { document.getElementById('payment_date').focus(); });
            return;
        }
        /* Diperiksa lagi di sini: isian tanggalnya masih bisa DIKETIK,
           jadi batas kalender saja tidak cukup. */
        var tgl_ftr_form = document.getElementById('tanggal').value;
        if (ftrAngkaTgl(tgl_bayar) < ftrAngkaTgl(tgl_ftr_form)) {
            Swal.fire({ icon: 'warning', title: 'Payment Date is too early',
                html: 'Payment Date <b>' + tgl_bayar + '</b> cannot be earlier than FTR CBD Date <b>' + tgl_ftr_form + '</b>.' })
                .then(function () { document.getElementById('payment_date').focus(); });
            return;
        }
        if (payment_method === '') {
            Swal.fire({ icon: 'warning', title: 'Payment Method is required',
                text: 'Please choose Transfer or Cash.' });
            return;
        }
        if (item_type === '') {
            Swal.fire({ icon: 'warning', title: 'Item Type is required',
                text: 'Please choose an Item Type.' });
            return;
        }

        /* No PI WAJIB di SETIAP baris yang dicentang. Kalau PI-nya memang belum
           ada, user mengisi tanda strip - yang penting kolomnya tidak kosong,
           karena nomor PI dipakai menelusuri dokumen ini di kemudian hari. */
        var $kosong = null;
        $dipilih.each(function () {
            if ($kosong) { return; }
            var $pi = $(this).closest('tr').find('td:eq(2) input');
            if ($.trim($pi.val()) === '') { $kosong = $pi; }
        });
        if ($kosong) {
            Swal.fire({ icon: 'warning', title: 'PI number is required',
                text: 'Fill in the PI number for every selected PO. Enter "-" if there is none.' })
                .then(function () { $kosong.focus(); });
            return;
        }

        var jumlah = $dipilih.length;
        var noFtr  = document.getElementById('noftrcbd').value;
        var totalTampil = $("#total").val() || '0.00';

        /* KONFIRMASI SEBELUM SIMPAN. Menu daftar FTR tidak punya tombol hapus -
           sekali tersimpan, pembetulannya harus lewat Cancel. Jadi isinya
           diperlihatkan dulu, supaya salah pilih cara bayar atau salah centang
           PO masih bisa dibatalkan di sini. */
        Swal.fire({
            icon: 'question',
            title: 'Save this FTR CBD?',
            html: '<div style="text-align:left;font-size:13px;line-height:1.95;color:#475569">'
                + '<div style="font-size:15px;font-weight:700;color:#1e3a8a;margin-bottom:6px">' + noFtr + '</div>'
                + 'Payment Method : <b>' + payment_method + '</b><br>'
                + 'Item Type : <b>' + item_type + '</b><br>'
                + 'PO rows : <b>' + jumlah + '</b><br>'
                + 'Total : <b>' + totalTampil + '</b>'
                + '</div>',
            showCancelButton: true,
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'Cancel'
        }).then(function (jawab) {
            if (!jawab.isConfirmed) { return; }
            kirimFtr($dipilih, jumlah, noFtr, payment_method, item_type, tgl_bayar);
        });
    });

    /* Pengiriman dipisah jadi fungsi sendiri. Kalau digabung ke dalam .then(),
       seluruh blok kirim ikut masuk satu tingkat lebih dalam dan susunannya
       jadi sulit dibaca. */
    function kirimFtr($dipilih, jumlah, noFtr, payment_method, item_type, tgl_bayar) {
        var selesai = 0, adaGagal = false, pesanGagal = '';
        Swal.fire({
            title: 'Saving...', text: 'Saving ' + jumlah + (jumlah === 1 ? ' row.' : ' rows.'),
            allowOutsideClick: false, allowEscapeKey: false,
            didOpen: function () { Swal.showLoading(); }
        });

        $dipilih.each(function () {
            var $row = $(this).closest('tr');
            $.ajax({
                type: 'POST',
                url: 'insertftrcbd.php',
                data: {
                    noftrcbd: document.getElementById('noftrcbd').value,
                    tglftrcbd: document.getElementById('tanggal').value,
                    tgl_bayar: tgl_bayar,
                    keterangan: document.getElementById('memo').value,
                    payment_method: payment_method,
                    item_type: item_type,
                    profit_center: document.getElementById('profit_center').value,
                    nama_supp: $('select[name=nama_supp] option').filter(':selected').val(),
                    curr: $row.find('td:eq(7)').attr('value'),
                    no_po: $row.find('td:eq(1)').attr('value'),
                    no_pi: $row.find('td:eq(2) input').val(),
                    tgl_po: $row.find('td:eq(3)').attr('value'),
                    create_user: '<?php echo $user; ?>',
                    sum_sub: parseFloat($row.find('.paid_subtotal').val()) || 0,
                    sum_tax: parseFloat($row.find('.paid_tax').val()) || 0,
                    sum_total: parseFloat($row.find('.paid_total').val()) || 0
                },
                error: function (xhr) {
                    adaGagal = true;
                    pesanGagal = (xhr.responseText || '').substring(0, 200) || ('Server responded ' + xhr.status + '.');
                },
                complete: function () {
                    selesai++;
                    if (selesai < jumlah) { return; }
                    if (adaGagal) {
                        Swal.fire({ icon: 'error', title: 'Failed to save', text: pesanGagal });
                    } else {
                        // Nomor FTR ditampilkan di sini: sesudah OK ditekan
                        // halaman kembali ke daftar, jadi inilah satu-satunya
                        // kesempatan user mencatat nomor yang baru terbit.
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved',
                            html: '<div style="font-size:12.5px;color:#64748b;letter-spacing:.04em">FTR CBD NUMBER</div>'
                                + '<div style="font-size:18px;font-weight:700;color:#1e3a8a;margin:5px 0 12px">' + noFtr + '</div>'
                                + '<div style="font-size:13px;color:#475569">' + jumlah
                                + (jumlah === 1 ? ' PO row saved.' : ' PO rows saved.') + '</div>',
                            confirmButtonText: 'OK'
                        })
                            .then(function () { window.location = 'ftrcbd.php'; });
                    }
                }
            });
        });
    }
</script>

<!--<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>-->

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
        $('#mymodalpo').modal('show');
        var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(3)').text();
        var supp = $(this).closest('tr').find('td:eq(8)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(7)').attr('value');   

        $.ajax({
            type : 'post',
            url : 'ajaxpocbd.php',
            data : {'no_po': no_po},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_po').html(no_po);
        /* Label & nilai ditulis sbg dua elemen terpisah supaya bisa
           disejajarkan lewat CSS - dulu satu kalimat utuh ("Supplier : X"),
           sehingga nilainya tidak pernah sejajar antar kolom. */
        function infoPo(sel, label, nilai) {
            $(sel).html('<span class="ftr-k">' + label + '</span>'
                      + '<span class="ftr-v">' + $('<i>').text(nilai == null || nilai === '' ? '-' : nilai).html() + '</span>');
        }
        infoPo('#txt_tgl_po', 'PO Date',  tgl_po);
        infoPo('#txt_supp2',  'Supplier', supp);
        infoPo('#txt_curr',   'Currency', curr);
    });

</script>

<!--<script>
    $(document).ready(){
        $('#mybpb').click(function){
            $('#mymodal').modal('show');
        }
    }
</script>-->
<!--<script>
$(document).ready(function() {   
    $("#send").click(function(e) {
        e.preventDefault();
        var datas= $(this).children("option:selected").val();
        $.ajax({
            type:"post",
            url:"cek.php",
            dataType: "json",
            data: {datas:datas},
            success: function(data){
                alert("Success: " + data);
            }
        });               
    });
</script>-->
<!--<script>
$(document).ready(function (){
    $("select.selectpicker").change(function(){
        var selectedbpb = $(this).children("option:selected").val();
        document.getElementById("bpbvalue").value = selectedbpb;             
    });
});
</script>-->
<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
