<?php include '../header.php' ?>

<style>
    /* Tema admin (AdminLTE) ngasih border/box-shadow default ke .box/.box-header/
       .box-body yang bikin garis pemisah nongol di antara section - di-nol-in
       semua di sini biar bersih, ganti sama shadow+radius punya .pv-card sendiri. */
    .pv-card, .pv-card.box, .pv-card .box-header, .pv-card .box-body,
    .pv-card .box.header, .pv-card .box.body, .pv-card .box.footer {
        border: 0 !important;
        box-shadow: none !important;
    }
    /* AdminLTE ngasih .box border-top 3px + shadow tipis banget bawaan tema -
       rgba(0,0,0,.125) ternyata kelihatan nyaris polos di browser, jadi warna
       border dibikin lebih jelas/gelap supaya tepi card kelihatan tegas. */
    .pv-card, div.box.pv-card {
        border-radius: .25rem !important;
        background: #fff !important;
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.25) !important;
        overflow: hidden !important;
        margin-bottom: 20px !important;
        border: 1px solid #c9ccd1 !important;
        border-top: 1px solid #c9ccd1 !important;
    }
    .pv-card .box.header{ padding:24px 24px 8px; }
    .pv-card .box.body{ padding:8px 24px 24px; }
    /* Bug lama: div "box body" (tabel detail + total) ternyata tidak pernah
       ke-nest di dalam wrapper .pv-card (ada </div> ekstra di area modal
       Choose Memo yang menutup .pv-card terlalu awal), jadi dia jadi kotak
       terpisah sendiri dengan style default AdminLTE + jarak 20px dari
       margin-bottom punya .pv-card. Disatukan di sini secara visual tanpa
       harus bongkar nesting div yang sudah telanjur berantakan. */
    .box.body{
        border: 1px solid #c9ccd1 !important;
        border-top: 0 !important;
        border-radius: 0 0 .25rem .25rem !important;
        background: #fff !important;
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.25) !important;
        margin-top: -20px !important;
        padding: 8px 24px 24px;
    }
    .total-box{ border:0; border-radius:10px; background:#fff; box-shadow:0 2px 10px rgba(0,0,0,0.08); overflow:hidden; }
    .total-box .total-box-header{ padding:12px 16px; color:#fff; font-weight:700; font-size:14px; background:linear-gradient(90deg, #4a5578, #6b7699); }
    .total-box .total-box-body{ padding:16px; display:flex; flex-direction:column; gap:14px; }
    .total-stat{ display:flex; justify-content:space-between; align-items:flex-end; padding-bottom:12px; border-bottom:1px dashed #e5e5e5; }
    .total-stat:last-child{ border-bottom:0; padding-bottom:0; }
    .total-stat-label{ font-size:12px; font-weight:600; color:#8a8a8a; text-transform:uppercase; letter-spacing:.03em; }
    .total-stat input.total-stat-value{ border:0; background:transparent; padding:0; font-size:19px; font-weight:700; text-align:right; width:auto; max-width:100%; height:auto; color:#212529; }

    /* Catatan: JANGAN kasih width:100% !important ke .select2-container -
       select2 membuka dropdown-nya dengan container terpisah yang juga
       pakai class .select2-container, jadi aturan lebar itu ikut membesarkan
       panel dropdown-nya jadi selebar halaman. Lebar cukup diatur lewat
       inline style="width:100%" di masing-masing <select>. */
    .select2-selection--single{ height: calc(1.5em + .75rem + 2px) !important; padding:.375rem .75rem !important; display:flex !important; align-items:center; }
    .select2-selection--single .select2-selection__rendered{ line-height:1.5 !important; padding-left:0 !important; font-size:14px; }
    .select2-selection--single .select2-selection__arrow{ height: calc(1.5em + .75rem) !important; top:0 !important; }

    /* Input manual pengganti dropdown To Account (Supplier kantor pajak/bea
       cukai) - disamakan persis tinggi/padding-nya ke .select2-selection--single
       supaya sejajar rapi dengan dropdown-dropdown lain di baris yang sama. */
    .tocc-manual-input{ box-sizing:border-box; height: calc(1.5em + .75rem + 2px) !important; padding:.375rem .75rem !important; font-size:14px; line-height:1.5; }

    .table-gradient2 th{ background:#3B82F6; color:#fff; text-align:center; vertical-align:middle; white-space:nowrap; }

    /* Modal "Choose Memo" - modernisasi header/date-range/tabel hasil pencarian */
    #mymodal2 .modal-dialog{ max-width:60%; }
    #mymodal2 .modal-content{ border:0; border-radius:12px; overflow:hidden; box-shadow:0 1rem 3rem rgba(0,0,0,.2); }
    #mymodal2 .modal-header{ background:linear-gradient(90deg, #191970, #1e90ff); border-bottom:0; padding:16px 24px; }
    #mymodal2 .modal-header .modal-title{ color:#fff; font-weight:700; }
    #mymodal2 .modal-header .close{ color:#fff; opacity:.85; text-shadow:none; }
    #mymodal2 .modal-header .close:hover{ opacity:1; color:#fff; }
    #mymodal2 .modal-body{ padding:24px; }
    #mymodal2 .memo-date-row{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    #mymodal2 .memo-date-row .form-control{ max-width:160px; }
    #mymodal2 .memo-date-sep{ font-weight:700; color:#8a8a8a; }
    #mymodal2 #table-memo thead th{ background:#3B82F6; color:#fff; font-weight:600; border-color:#3B82F6; }
    #mymodal2 .modal-footer{ border-top:1px solid #eee; padding:16px 24px; }

    /* Tab pindah menu Payment Voucher / EXIM / FTR - ganti dari tombol skew
       jadul jadi pill-tab modern, tab aktif dikasih gradient sama kaya header. */
    .pv-type-tabs{ display:inline-flex; gap:4px; background:#eef0f4; padding:4px; border-radius:10px; }
    .pv-type-tabs .pv-type-tab{ border:0; background:transparent; color:#5a6270; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; cursor:pointer; transition:background .15s,color .15s; }
    .pv-type-tabs .pv-type-tab:hover{ background:rgba(0,0,0,.05); color:#212529; }
    .pv-type-tabs .pv-type-tab.active{ background:linear-gradient(90deg, #191970, #1e90ff); color:#fff; box-shadow:0 2px 6px rgba(30,144,255,.35); }
</style>

    <!-- MAIN -->
    <div class="col p-4">
<div class="box pv-card">
    <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff); margin:-1px -1px 0; border-radius:12px 12px 0 0;">
        <h5 class="mb-0"><i class="fa fa-file-text-o"></i> FORM PAYMENT VOUCHER MEMO EXIM</h5>
    </div>
    <div class="box header">
<form id="form-data" method="post">
        <div style="padding:5px 10px 10px;">
            <div class="pv-type-tabs">
            <button id="btnpv" type="button" class="pv-type-tab">Payment Voucher</button>
            <button id="btnpve" type="button" class="pv-type-tab active">Payment Voucher EXIM</button>
            <!-- Payment Voucher FTR sudah tidak dipakai, tab-nya di-hide (bukan dihapus) -->
            <!-- <button id="btnpvftr" type="button" class="pv-type-tab">Payment Voucher FTR</button> -->
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>No Payment Voucher</b></label>
                <?php
            $sql = mysqli_query($conn2,"select max(no_pv) from tbl_pv_h where YEAR(pv_date) = YEAR(CURRENT_DATE())");
            $row = mysqli_fetch_array($sql);
            $kodepay = $row['max(no_pv)'];
            $urutan = (int) substr($kodepay, 12, 5);
            $urutan++;
            $bln = date("m");
            $thn = date("y");
            $huruf = "PV/NAG/$bln$thn/";
            $kodepay = $huruf . sprintf("%05s", $urutan);

            echo'<input type="text" readonly style="font-size: 14px;" class="form-control" id="no_doc" name="no_doc" value="'.$kodepay.'">'
            ?>
        </div>

            <div class="col-md-2 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Payment Voucher Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class="form-control tanggal" 
            value="<?php 
            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;            
            if(!empty($_POST['nama_supp'])) {
                echo $_POST['tgl_active'];
            }
            else{
                echo date("d-m-Y");
            } ?>" autocomplete='off'>

            <input type="hidden" style="font-size: 15px;" name="unik_code" id="unik_code" class="form-control" 
            value="<?php 
            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
            $shuffle  = substr(str_shuffle($karakter), 0, 8);
            echo $shuffle; ?>" autocomplete='off' readonly>
            </div>

            
            <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Supplier</b></label>            
<!--               <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Supplier</option>                                                 
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
                </select> -->
                <input type="text" style="font-size: 14px;" class="form-control" id="nama_supp" name="nama_supp" readonly value="<?php 
            $sql = mysqli_query($conn2,"select DISTINCT ms.supplier supplier from memo_h a
          inner join mastersupplier ms on a.id_supplier = ms.id_supplier
          inner join mastersupplier mb on a.id_buyer = mb.id_supplier
                    inner join memo_det mdet on mdet.id_h = a.id_h
                    inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
                    where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and mtemp.user = '$user' GROUP BY nm_memo order by a.id_h desc limit 1");
            $row = mysqli_fetch_array($sql);
            $supplier = isset($row['supplier']) ? $row['supplier'] : null;           
            if(!empty($supplier)) {
                echo $supplier;
            }
            else{
                echo '';
            }?>">
                </div>

                <input type="hidden" style="font-size: 14px;text-align: right;" class="form-control" id="rat_pv" name="rat_pv" 
                value="<?php

                    $sqlx = mysqli_query($conn2,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
                    $rowx = mysqli_fetch_array($sqlx);
                    $maxid = $rowx['id'];

                    $sqly = mysqli_query($conn2,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxid' and v_codecurr = 'PAJAK'");
                    $rowy = mysqli_fetch_array($sqly);
                    $rate = $rowy['rate'];    
            // $top = 30;

                echo $rate;
          
        ?>">

        



                                        
    </div>
</br>

        <div class="form-row">

<div class="col-md-3 mb-3">
    <label class="col-form-label"><b>Supporting Document</b></label>

    <div class="input-group">
        <input type="text"
               readonly
               style="font-size:14px;"
               class="form-control"
               id="sup_doc"
               name="sup_doc"
               value="<?php
                    $sql = mysqli_query($conn2,"
                        SELECT GROUP_CONCAT(ket SEPARATOR ', ') AS sup_doc
                        FROM supp_doc_temp
                        WHERE ket != ''
                    ");
                    $row = mysqli_fetch_array($sql);
                    echo $row['sup_doc'];
               ?>">

        <div class="input-group-append">
            <button type="button"
                    class="btn btn-info"
                    id="btn5"
                    data-toggle="modal"
                    data-target="#mymodal5">
                Select
            </button>
        </div>
    </div>
</div>

            <div class="col-md-2 mb-3" style="padding-top: 8px;">
            <label for="forpay"><b>For Payment</b></label> 
            <input type="text" readonly style="font-size: 14px;" class="form-control" id="forpay" name="forpay" value="Export - Import">           
            </div>

            <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="ct_buyer"><b>Charge To Buyer</b></label> 
            <input type="text" readonly style="font-size: 14px;" class="form-control" id="ct_buyer" name="ct_buyer" value="<?php 
            $sql = mysqli_query($conn2,"select DISTINCT mb.supplier buyer from memo_h a
          inner join mastersupplier ms on a.id_supplier = ms.id_supplier
          inner join mastersupplier mb on a.id_buyer = mb.id_supplier
                    inner join memo_det mdet on mdet.id_h = a.id_h
                    inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
                    where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and a.ditagihkan = 'Y' and mtemp.user = '$user' GROUP BY nm_memo order by a.id_h desc limit 1");
            $row = mysqli_fetch_array($sql);
            $buyer = isset($row['buyer']) ? $row['buyer'] : null;           
            if(!empty($buyer)) {
                echo $buyer;
            }
            else{
                echo '-';
            }?>">           
            </div>

            

            <div class="col-md-3 mb-3">
            </div>
            
                <div class="col-md-2 mb-3"> 
                    <label for="carabayar" class="col-form-label" style="width: 150px;">Pay Methods </label>               
                <select class="form-control select2" name="carabayar" id="carabayar" style="width:100%">
                    <option value="" disabled selected="true">Choose pay method</option>
                    <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['forpay']) ? $_POST['forpay']: null;
                }                 
                $sql = mysqli_query($conn1,"select pay_method from tbl_paymethod ");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['pay_method'];
                    if($row['pay_method'] == $_POST['carabayar']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?> 
        
                </select> 
                </div>
                <div class="col-md-1 mb-3"> 
                    <label for="carabayar" class="col-form-label" style="width: 150px;">Currency </label>               
                <select class="form-control select2" name="curre" id="curre" style="width:100%">
                    <option value="" disabled selected="true">Curr</option>
                    <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['curre']) ? $_POST['curre']: null;
                }                 
                $sql = mysqli_query($conn1,"select DISTINCT curr from b_masterbank union select 'EUR' curr  ");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['curr'];
                    if($row['curr'] == $_POST['curre']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?> 
        
                </select>
                </div>

                <div class="col-md-2 mb-3">
            <label for="total" class="col-form-label" style="width: 150px;"><b>Payment Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_pay" id="tgl_pay" class="form-control tanggal" 
            value="<?php 
            if(!empty($_POST['tgl_pay'])) {
                echo $_POST['tgl_pay'];
            }
            else{
                echo date("d-m-Y");
            } ?>" autocomplete='off'>
            </div>

            <div class="col-md-1 mb-3">            
            <label for="total" class="col-form-label" style="width: 200px;"><b>Select No Memo</b></label>
                <input style="border: 0;
    line-height: 1;
    padding: 10px 10px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color: rgb(95, 158, 160);" type="button" name="btn2" id="btn2" data-target="#mymodal2" data-toggle="modal" value="Select Memo"> 
            </div>
            <div class="col-md-6 mb-3"></div>

                <?php
                // From Account/To Account selalu dirender (tidak lagi tergantung
                // $_POST['carabayar']), supaya ganti Pay Methods bisa toggle
                // tampilannya lewat JS tanpa submit/reload halaman. Lihat
                // handler $('#carabayar').on('change', ...) di bagian script.
                $cb = isset($_POST['carabayar']) ? $_POST['carabayar']: null;
                $frccToccHideStyle = ($cb == 'CASH') ? 'display:none;' : '';
                ?>
                <div class="col-md-2 mb-3" id="div_frcc" style="padding-top: 8px;<?php echo $frccToccHideStyle; ?>">
            <label for="nama_supp"><b>From Account</b></label>
              <select class="form-control select2" name="frcc" id="frcc" style="width:100%">
                <option value="" disabled selected="true">Select Account</option>
                <?php
                       $frcc ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $frcc = isset($_POST['frcc']) ? $_POST['frcc']: null;
                }
                $sql = mysqli_query($conn1,"select coa_name as bank,curr,bank_account as akun from b_masterbank where status = 'Active' group by id");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['bank'];
                    $indata = $row['akun'];
                    // Dibandingkan ke akun (value opsi), bukan nama bank -
                    // frcc yang tersimpan/dikirim itu nomor rekening, bukan
                    // nama bank, jadi bandingnya harus ke $indata.
                    if($indata == $frcc){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$indata.'"'.$isSelected.'">'. $data .'</option>';
                        }
                        ?>
              </select>

                <input type="hidden" style="font-size: 14px;" class="form-control" id="no_cek" name="no_cek" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="cek_date" name="cek_date" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="ke" name="ke" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="dari" name="dari" value="" autocomplete = "off">
                <input type="hidden" style="font-size: 14px;" class="form-control" id="pay_for" name="pay_for" value="" autocomplete = "off">

            </div>
            <div class="col-md-2 mb-3" id="div_tocc" style="padding-top: 8px;<?php echo $frccToccHideStyle; ?>">
            <label for="nama_supp"><b>To Account</b></label>
              <select class="form-control select2" name="tocc" id="tocc" style="width:100%">
                <option value="-" disabled selected="true">Select Account</option>
                <?php

                $sql = mysqli_query($conn2,"select DISTINCT ms.supplier supplier from memo_h a
          inner join mastersupplier ms on a.id_supplier = ms.id_supplier
          inner join mastersupplier mb on a.id_buyer = mb.id_supplier
                    inner join memo_det mdet on mdet.id_h = a.id_h
                    inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
                    where mdet.cancel = 'N' and mdet.nm_sub_ctg != 'VAT' and mtemp.user = '$user' GROUP BY nm_memo order by a.id_h desc limit 1");
            $row = mysqli_fetch_array($sql);
            $nama_supp_tocc = isset($row['supplier']) ? $row['supplier'] : null;

                       $tocc ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $tocc = isset($_POST['tocc']) ? $_POST['tocc']: null;
                }
                if ($nama_supp_tocc == null OR $nama_supp_tocc == '') {
                    $sql = mysqli_query($conn2,"SELECT ms.Supplier AS supplier, CONCAT(UPPER(TRIM(m.bank_name)),' ',UPPER(TRIM(m.bank_account))) AS bank, UPPER(TRIM(m.bank_account)) AS akun FROM master_supplier_bank m JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier WHERE m.status = 'Active' AND m.tipe_sup = 'S' ORDER BY ms.Supplier, m.bank_name");
                }else{
                    $sql = mysqli_query($conn2,"SELECT ms.Supplier AS supplier, CONCAT(UPPER(TRIM(m.bank_name)),' ',UPPER(TRIM(m.bank_account))) AS bank, UPPER(TRIM(m.bank_account)) AS akun FROM master_supplier_bank m JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier WHERE m.status = 'Active' AND m.tipe_sup = 'S' AND ms.Supplier = '$nama_supp_tocc' ORDER BY m.bank_name");
                }
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['bank'];
                    $indata = $row['akun'];
                    if($row['akun'] == $tocc){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$indata.'"'.$isSelected.'">'. $data .'</option>';
                        }
                        ?>
              </select>
              <!-- Untuk Supplier KANTOR PAJAK/KPPBC TMP A BANDUNG, To Account tidak
                   punya rekening tetap di master_supplier_bank - diganti isian bebas
                   lewat toggleToccInputMode() (lihat script di bawah). -->
              <input type="text" class="form-control tocc-manual-input" id="tocc_manual" name="tocc_manual" style="width:100%; display:none;" placeholder="Enter To Account" autocomplete="off">

            </div>

            <div class="col-md-2 mb-3">
                    <label for="pv_tax_type" class="col-form-label" style="width: 150px;">Payment Voucher Type</label>
                <select class="form-control select2" name="pv_tax_type" id="pv_tax_type" style="width:100%" required>
                    <option value="" disabled selected="selected">Select Type</option>
                    <option value="Tax">Tax</option>
                    <option value="Non Tax">Non Tax</option>
                </select>
                </div>

        
        </div>


<div class="form-row">


        <div class="col-md-6 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="2" type="text" class="form-control " name="pesan" id="pesan" value="<?php             
            if(!empty($_POST['pesan'])) {
                echo $_POST['pesan'];
            }
            else{
                echo '';
            } ?>" placeholder="Description..." ></textarea>
        </div>
                                
 </div>
</br>

    

    <div class="form-row">
    <div class="modal fade" id="mymodal3" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">For Payment</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form3" method="post">
                <div class="form-row">
                    <div class="col-6">

                    <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>
                            <th style="width:10px;">Check</th>
                            <th style="width:150px;">Supporting Doc</th>                                                    
                        </tr>
                    </thead>

            <tbody>
                    <?php

            $querys = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '1' ");


            while($row1 = mysqli_fetch_array($querys)){
                
                    echo '<tr>  
                    <td style="width:10px;"><input type="radio" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>  
                    <td style="width:10px;"><input type="radio" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" >
                            </td>                                                                                                 
                        </tr>';
                    ?> 
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <div class="col-md-12 mb-3">            
            <label for="tanggal"><b>Ke</b></label>          
            <input type="text" style="font-size: 16px;text-align: center;" name="ke_berapa" id="ke_berapa" class="form-control" 
            value="">
            </div>
            <div class="col-md-12 mb-3">            
            <label for="tanggal"><b>Dari</b></label>          
            <input type="text" style="font-size: 16px;text-align: center;" name="dari_berapa" id="dari_berapa" class="form-control" 
            value="">
            </div>

            <div class="col-md-12 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Dari Account</b></label>            
              <select class="form-control selectpicker" name="dari_akun" id="dari_akun" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>                                                 
                <?php
                $dari_akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $dari_akun = isset($_POST['dari_akun']) ? $_POST['dari_akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select name from tbl_akun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['name'];
                    if($row['name'] == $_POST['dari_akun']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>

                <div class="col-md-12 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Ke Account</b></label>            
              <select class="form-control selectpicker" name="ke_akun" id="ke_akun" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>                                                 
                <?php
                $ke_akun ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ke_akun = isset($_POST['ke_akun']) ? $_POST['ke_akun']: null;
                }                 
                $sql = mysqli_query($conn1,"select name from tbl_akun");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['name'];
                    if($row['name'] == $_POST['ke_akun']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>

                <div class="col-md-12 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Keterangan</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="3" type="text" class="form-control " name="keter" id="keter" value="<?php             
            if(!empty($_POST['keter'])) {
                echo $_POST['keter'];
            }
            else{
                echo '';
            } ?>" placeholder="..." required></textarea>
        </div>

                    
        </div>
                </div>  
            </div>
                <div class="modal-footer">
                    <button type="button" id="send3" name="send3" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div> 

 <div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="Heading"><i class="fa fa-file-text-o"></i> Choose Memo</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        </div>
          <div class="modal-body">
            <form id="modal-form2" method="post">
                <div class="form-row">
                    <div class="col-md-5 mb-3">
                        <label for="nama_supp_memo"><b>Supplier</b></label>
              <select class="form-control select2" name="nama_supp_memo" id="nama_supp_memo" style="width:100%">
                <option value="" disabled selected="true">Select Supplier</option>
                <?php
                $sql = mysqli_query($conn1,"select distinct(Supplier),id_supplier from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data2 = $row['id_supplier'];
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_memo']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data2.'"'.$isSelected.'">'. $data .'</option>';
                }?>
                </select>
                    </div>

                    <div class="col-md-7 mb-3">
                     <label><b>Memo Date</b></label>
                <div class="memo-date-row">
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="start_date_memo" name="start_date_memo"
                value="<?php
                $start_date_memo ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $start_date_memo = date("Y-m-d",strtotime($_POST['start_date_memo']));
                }
                if(!empty($_POST['start_date_memo'])) {
                    echo $_POST['start_date_memo'];
                }
                else{
                    echo date("d-m-Y");
                } ?>"
                placeholder="Tanggal Awal">

                <span class="memo-date-sep">-</span>
                <input type="text" style="font-size: 14px;" class="form-control tanggal" id="end_date_memo" name="end_date_memo"
                value="<?php
                $end_date_memo ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $end_date_memo = date("Y-m-d",strtotime($_POST['end_date_memo']));
                }
                if(!empty($_POST['end_date_memo'])) {
                    echo $_POST['end_date_memo'];
                }
                else{
                    echo date("d-m-Y");
                } ?>"
                placeholder="Tanggal Akhir">
                <button type="button" id="send2" name="send2" class="btn btn-info"><i class="fa fa-search"></i> Search</button>
                </div>
                    </div>
                </div>

                <div id="details" style="font-size: 12px;"></div>

                <div class="modal-footer">
                    <button type="button" id="savememo" name="savememo" class="btn btn-primary"><span class="fa fa-floppy-o" aria-hidden="true"></span>
                        Save
                    </button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div>


 <div class="form-row">
    <div class="modal fade" id="mymodal5" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Supporting Document</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form5" method="post">
                <div class="form-row">
                    <table id="doc_support" class="table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
        <tr><th class="text-center">Cek</th>
            <th class="text-center">Supporting Doc</th>
        </tr>
    </thead>
    <tbody>
        <?php

            $querys = mysqli_query($conn2,"select ref_doc from master_forpay where ket = '2' ");


            while($row1 = mysqli_fetch_array($querys)){
                $nodoc = $row1['ref_doc'];

                $sql22 = mysqli_query($conn2,"select ket from supp_doc_temp where ket = '$nodoc'");
                $row22 = mysqli_fetch_array($sql22);
                $ket = isset($row22['ket']) ? $row22['ket'] : null;

                $sql23 = mysqli_query($conn2,"select ket from supp_doc_temp where ket != 'Sales Order' and ket != 'Purchase Order' and ket != 'PEB' and ket != 'Invoice'");
                $row23 = mysqli_fetch_array($sql23);
                $ket2 = isset($row23['ket']) ? $row23['ket'] : null;
                
                    echo '<tr>'; 
                    if ($ket != '') {
                         echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>'; 
                     } else{
                    echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>'; 
                    }                        
                            echo '<td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>';
                   echo '<tr>'; 
                    if ($ket2 != '') {
                         echo'<td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                         <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$ket2.'" >
                            </td>                                                                                                 
                        </tr>'; 
                     } else{
                    echo'<td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
                    <td hidden><input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="" autocomplete="off" ></td>
            <td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="" >
                            </td>                                                                                                 
                        </tr>'; 
                    }  
                    
                    ?> 
    </tbody>                  
            </table> 
              
    </div>
  
                <div class="modal-footer">
                    <button type="button" id="send5" name="send5" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
                        Save
                    </button>
                </div>           
            </form>
        </div>
      </div>
    </div>
  </div>
 </div>
</div>       

</form>
    <div class="box body">
        <div class="row">
        
            <div class="col-md-12" style="overflow-x:auto; width:100%;">

            <table id="mytable" class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead class="table-gradient2">
        <tr><th class="text-center" style="width: 2%">-</th>
            <th class="text-center" style="width: 12%">No Memo</th>
            <th class="text-center" hidden>Jenis Transaksi</th>
            <th class="text-center" hidden>Ditagihkan</th>
            <th class="text-center" hidden>Kategori</th>
            <th class="text-center" hidden>Sub Kategori</th>
            <th class="text-center" hidden>Item</th>
            <th class="text-center" style="width: 8%">COA</th>
            <th class="text-center" style="width: 10%">Profit Center</th>
            <th class="text-center" style="width: 9%">Cost Center</th>
            <th class="text-center" style="width: 16%">Description</th>
            <th class="text-center" style="width: 8%">Amount</th>
            <th class="text-center" style="width: 8%">Deduction</th>
            <th class="text-center" style="width: 9%">Due date</th>
            <th class="text-center" style="width: 9%">PPH</th>
            <th class="text-center" style="width: 9%">PPN</th>
            <th class="text-center" style="width: 2%">Action</th>
        </tr>
    </thead>
    
    <tbody id="tbody2">

        <?php include 'inc_memo_detail_rows_exim.php'; ?>
    </tbody>
    <tfoot>
          <tr>
            <td colspan="16" align="center">
            <button type="button" class="btn btn-primary" onclick="addRow('tbody2')">Add Row</button>
            <button type="button" class="btn btn-warning" onclick="InsertRow('tbody2')">Interject Row</button>
            <button type="button" class="btn btn-danger" onclick="deleteRow('tbody2')">Delete Row</button>
            <!-- <input  style="margin-right: 15px;border: 0; line-height: 1; padding: 10px 20px; font-size: 1rem; text-align: center; color: #fff; text-shadow: 1px 1px 1px #000; border-radius: 6px; background-color: rgb(30, 144, 255);" id="add" type="button" value="(+) Add">  -->
            </td>
          </tr>
    </tfoot>                   
            </table>                    
<div style="padding:20px 0 0; border:0; box-shadow:none; background:transparent;">
        <form id="form-simpan">
            <div class="form-row col">
            <div class="col-md-3">
                </br>

        <div style = "display : none;">
                <select class="form-control selectpicker" name="pilih_pph" id="pilih_pph" data-dropup-auto="false" data-live-search="true">
                <option value="-" disabled selected="true">Select Account</option>
                </select>
                </div>
                <div class="mb-2">
            <label for="pilih_ppn"><b>PPN</b></label>
            <select class="form-control select2" name="pilih_ppn" id="pilih_ppn" style="width:100%" onchange='changeValueTax2(this.value)' required>
                <option value="" disabled selected="true">Select PPN</option>
                <?php
                        $sqlacc = mysqli_query($conn1,"select '0' idtax, 'Non PPN' kriteria, '0' percentage, 'Non PPN' kriteria2
                                                        union
                                                        select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN'  GROUP BY idtax");
                         $jsArray = "var prdName = new Array();\n";

                        while ($row = mysqli_fetch_array($sqlacc)) {
                            $idtax = $row['idtax'];
                            $data = $row['percentage'];
                            $data2 = $row['kriteria2'];
                            if($row['kriteria2'] == $_POST['pilih_ppn']){
                                $isSelected  = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option name="pilih_ppn" value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>'; 
                            $jsArray .= "prdName['" . $row['percentage'] . "'] = {idtax:'" . addslashes($row['idtax']) . "'};\n";   
                            
                        }
                        ?>
                </select>
                <input type="hidden" name="idtax" id="idtax" value="0">
            </div>
            <div class="mb-2">
                <label for="total_memo" class="col-form-label" style="width: 120px;"><b>Total Memo</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="total_memo" name="total_memo" placeholder="0.00" readonly value="<?php
            $sql = mysqli_query($conn2," select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user' ");
            $row = mysqli_fetch_array($sql);
            $biaya = $row['biaya'];
            if(!empty($biaya)) {
                echo number_format($biaya,2);
            }
            else{
                echo '';
            }?>">
                 <input type="hidden" name="total_memo_h" id="total_memo_h" value="<?php
            $sql = mysqli_query($conn2," select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user' ");
            $row = mysqli_fetch_array($sql);
            $biaya = $row['biaya'];
            if(!empty($biaya)) {
                echo $biaya;
            }
            else{
                echo '';
            }?>">
            </div>
            <div class="mb-2">
                <label for="balance_memo" class="col-form-label" style="width: 120px;"><b>Balance</b></label>
                <input type="text" style="font-size: 14px;text-align: right;" class="form-control" id="balance_memo" name="balance_memo" placeholder="0.00" readonly>
            </div>
        <!--     <div class="input-group" >
                <label for="nama_supp" class="col-form-label" style="width: 80px;"><b>Tax (11%)</b></label>
                <input type="checkbox" id="check_vat_baru" name="check_vat_baru" onclick="modal_input_vat_baru()">
            </div>
            </br> -->
             
            
            </div>
            <div class="col-md-5">

            </div>
            <div class="col-md-4">
                <?php
                $sql = mysqli_query($conn2," select sum(biaya) biaya from tbl_pv_memo_temp where user = '$user' ");
                $row = mysqli_fetch_array($sql);
                $biaya = $row['biaya'];
                $biayaFmt = !empty($biaya) ? number_format($biaya, 2) : '';
                $biayaRaw = !empty($biaya) ? $biaya : '';
                ?>
                <div class="total-box">
                    <div class="total-box-header"><i class="fa fa-calculator"></i> Total</div>
                    <div class="total-box-body">
                        <div class="total-stat">
                            <span class="total-stat-label">Total Without Tax</span>
                            <input type="text" class="total-stat-value" id="nomrate1" name="nomrate1" placeholder="0.00" readonly value="<?= $biayaFmt; ?>">
                            <input type="hidden" name="nomrate_h" id="nomrate_h" value="<?= $biayaRaw; ?>">
                        </div>
                        <div class="total-stat">
                            <span class="total-stat-label">Deduction</span>
                            <input type="hidden" id="ded_ad" name="ded_ad" placeholder="0.00">
                            <input type="text" class="total-stat-value" id="ded_ad_h" name="ded_ad_h" placeholder="0.00" readonly>
                        </div>
                        <div class="total-stat">
                            <span class="total-stat-label">Incoming Tax</span>
                            <input type="text" class="total-stat-value" id="pph" name="pph" placeholder="0.00" readonly>
                            <input type="hidden" name="pph_h" id="pph_h" value="">
                            <input type="hidden" name="pph_min" id="pph_min" value="">
                            <input type="hidden" name="pph_plus" id="pph_plus" value="">
                        </div>
                        <div class="total-stat">
                            <span class="total-stat-label">Value Added Tax</span>
                            <input type="text" class="total-stat-value" id="ppn" name="ppn" placeholder="0.00" readonly>
                            <input type="hidden" name="ppn_h" id="ppn_h" value="">
                        </div>
                        <div class="total-stat">
                            <span class="total-stat-label"><b>Total</b></span>
                            <input type="text" class="total-stat-value" id="total" name="total" placeholder="0.00" readonly value="<?= $biayaFmt; ?>">
                            <input type="hidden" name="total_h" id="total_h" value="<?= $biayaRaw; ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
            
           <div class="form-row col">
            <div class="col-md-3 mb-3">                              
            <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan" id="simpan"><span class="fa fa-floppy-o"></span> Save</button>                
            <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='payment-voucher.php'"><span class="fa fa-angle-double-left"></span> Back</button>           
            </div>
            </div>                                    
        </form>
        </div>

<div class="modal fade" id="mymodalkbon" data-target="#mymodalkbon" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_kbon"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tgl_kbon" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_nama_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_tempo" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_create_user" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_status" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_faktur" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_tgl_inv" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>                                           
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
  <script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/sweetalert2.all.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-multiselect.min.js"></script>
    <script language="JavaScript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.js"></script>

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
    $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
    });
  </script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        autoclose:true
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_pph').select2({
            dropdownAutoWidth : true
        });

        $('.select2_ppn').select2({
            dropdownAutoWidth : true
        });
    });
</script>


<!-- <?php
for ($x = 1; $x <= 50; $x++) {
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_coa<?= $x?>').select2({
            dropdownAutoWidth : true
        });
    });
</script>
<?php } ?> -->
<!-- <script type="text/javascript">
        $(document).ready(function(){
            $("#btn2").click(function(){
                Swal.fire({
                  type: 'error',
                  title: 'Oops...',
                  text: 'Something went wrong!',
                  footer: ''
                });
            });
            });
</script> -->

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_coa').select2({
            dropdownAutoWidth : true
        });
        $('.select2_coa2').select2({
            dropdownAutoWidth : true
        });
    });
</script>


<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<!--<script type="text/javascript"> 
    $("#mytable").on("click", "#delbutton", function() {
    var sub = $(this).closest('tr').find('td:eq(4)').attr('data-subtotal');
    var pajak = $(this).closest('tr').find('td:eq(5)').attr('data-tax');
    var total = $(this).closest('tr').find('td:eq(6)').attr('data-total');        
    var sub_val = document.getElementById("subtotal").value.replace(/[^0-9.]/g, '');
    var sub_tax = document.getElementById("pajak").value.replace(/[^0-9.]/g, '');
    var sub_total = document.getElementById("total").value.replace(/[^0-9.]/g, '');
    var min_sub = 0;
    var min_tax = 0;
    var min_total = 0;
    min_sub = sub_val - sub;
    min_tax = sub_tax - pajak;
    min_total = sub_total - total;
    $('#subtotal').val(formatMoney(min_sub));
    $('#pajak').val(formatMoney(min_tax));
    $('#total').val(formatMoney(min_total));                      
    $(this).closest("tr").remove();

});
</script>-->

<script>
    $(".select2").select2({
        theme: "bootstrap",
        placeholder: "Search"
} );
</script>

<script>
    $(document).ready(function() {
    $('#table-memo').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script>

<script type="text/javascript">

$(document).on('change', '.prof_ctr', function () {
        const selectedProfCtr = $(this).val();
        const row = $(this).closest('tr'); 
        const selectedCoa = row.find('select.no_coa').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
});

    $(document).on('change', '.no_coa', function () {
        const selectedCoa = $(this).val();
        const row = $(this).closest('tr'); 
        const selectedProfCtr = row.find('select.prof_ctr').val() || '-';
    // console.log("row:", row.html());
    // console.log("no_coa element:", row.find('.no_coa'));
    // console.log("selectedCoa:", selectedCoa);
    updateCostCenter(selectedProfCtr, selectedCoa, row);
});



// Fungsi reusable untuk isi dropdown Cost Center berdasarkan Profit Center
function updateCostCenter(profCtr, noCoa, row) {
    const costCtrDropdown = $(row).find('.nomor_cc'); // dropdown cost center pada baris tsb

    // Kosongkan dropdown cost_ctr sebelum diisi
    costCtrDropdown.selectpicker('destroy');  // Hancurkan selectpicker lama
    costCtrDropdown.empty();  // Kosongkan semua opsi yang ada
    costCtrDropdown.append('<option value="-"> - </option>');  // Tambahkan opsi default
    costCtrDropdown.selectpicker();  // Inisialisasi ulang selectpicker

    if (profCtr && profCtr !== '-') {
        // console.log(profCtr + ' ' + noCoa)
        // Lakukan AJAX ke server untuk mengambil data cost_ctr
        $.ajax({
            url: 'getCostCenter.php',  // Ganti dengan URL endpoint server Anda
            type: 'POST',
            data: { prof_ctr: profCtr , no_coa: noCoa },  // Kirim data prof_ctr ke server
            dataType: 'json',
            success: function (response) {
                if (response && response.length > 0) {
                    $.each(response, function (index, costCtr) {
                        costCtrDropdown.append(
                            `<option value="${costCtr.value}">${costCtr.text}</option>`
                            );
                    });

                    costCtrDropdown.selectpicker('refresh');
                } else {
                    console.warn('Tidak ada data cost center dari server.');
                    costCtrDropdown.selectpicker('refresh');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    } else {
        costCtrDropdown.selectpicker('refresh');
    }
}
    
    
   // JavaScript Document
function addRow(tableID) {
    var tableID = "tbody2";
 var table = document.getElementById(tableID);
 var rowCount = table.rows.length;
 var row = table.insertRow(rowCount);

 var element1 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td hidden><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><select class="form-control selectpicker no_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="100%" data-size="5"> <option value="-" > - </option><?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?> <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-live-search="true" data-width="100%" data-size="5"><option value="-"> - </option><?php $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'"); foreach ($sql3 as $fc) : ?> <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option> <?php endforeach; ?> </select> </td><td ><select class="form-control selectpicker nomor_cc" name="nomor_cc" id="nomor_cc" data-live-search="true" data-width="100%" data-size="5"> <option value="-" > - </option><?php $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name, CONCAT(no_cc,' ',cc_name) as cost_name from b_master_cc where status = 'Active'"); foreach ($sql2 as $cc) : ?> <option value="<?= $cc["code_combine"]; ?>"><?= $cc["cost_name"]; ?> </option><?php endforeach; ?></select></td><td><textarea style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></textarea></td><td><input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_dedadd(value)" autocomplete = "off"></td><td><input  type="text" style="font-size: 12px;" name="tgl_tempo" id="tgl_tempo" value="" class="form-control tanggal" autocomplete="off" placeholder="dd-mm-yyyy" ></td><td><select class="form-control select2add" name="pphh" id="pphh" style="width:100%" onchange="input_pph()"> <option data-idtax="0" value="0"> - </option><?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH' GROUP BY idtax"); foreach ($sql as $pph) : ?> <option data-idtax="<?= $pph["idtax"]; ?>" value="<?= $pph["percentage"]; ?>"><?= $pph["kriteria2"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control select2add ppnn-row" name="ppnn" id="ppnn" style="width:100%" onchange="input_ppn()"> <option data-idtax="0" value="0"> - </option><?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' GROUP BY idtax"); foreach ($sql as $ppn) : ?> <option data-idtax="<?= $ppn["idtax"]; ?>" value="<?= $ppn["percentage"]; ?>"><?= $ppn["kriteria2"]; ?> </option><?php endforeach; ?></select></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';

 row.innerHTML = element1;

 $('.selectpicker').selectpicker();
 $('.tanggal').datepicker({
    format: "dd-mm-yyyy",
    autoclose: true
 });
 $('.select2add').select2({
    theme: 'bootstrap4',
    dropdownAutoWidth: true,
    width: '100%'
 });

 // Kalau PPN header sudah dikunci (bukan Non PPN), baris baru ini juga
 // langsung ikut nilai itu & ikut terkunci.
 var headerPpnVal = $('#pilih_ppn').val();
 if (headerPpnVal && headerPpnVal !== '0') {
    var $newPpnRow = $(row).find('select[name=ppnn]');
    $newPpnRow.val(headerPpnVal).trigger('change');
    $newPpnRow.prop('disabled', true);
 }
    }
    
function deleteRow(tableID)
{
    try
         {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[16].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    if (rowCount <= 1)
                        {
                        Swal.fire('Error', 'Tidak dapat menghapus semua baris.', 'error');
                        break;
                        }
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                    recalcAllRows();
                    }
                }
            } catch(e)
    {
    Swal.fire('Error', String(e.message || e), 'error');
    }
 }
 
 function InsertRow(tableID)
{
    try{
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;
            for(var i=0; i<rowCount; i++)
                {
                var row = table.rows[i];
                var chkbox = row.cells[16].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    var newRow = table.insertRow(i+1);
                    var colCount = table.rows[0].cells.length;
                        for (h=0; h<colCount; h++){
                            var newCell = newRow.insertCell(h);
                            newCell.innerHTML = table.rows[0].cells[h].innerHTML;
                            var child = newCell.children;
                            for(var i2=0; i2<child.length; i2++) {
                                var test = newCell.children[i2].tagName;
                                switch(test) {
                                    case "INPUT":
                                        if(newCell.children[i2].type=='checkbox'){
                                            newCell.children[i2].value = "";
                                            newCell.children[i2].checked[16] = true;
                                        }else{
                                            newCell.children[i2].value = "";
                                        }
                                    break;
                                    case "SELECT":
                                        newCell.children[i2].value = "";
                                    break;
                                    default:
                                    break;
                                }
                            }
                        }
                    }
                    
                }
            } catch(e)
    {
    Swal.fire('Error', String(e.message || e), 'error');
    }
 }

 function hitungRow(){

}
</script>

<script type="text/javascript">
    // Satu-satunya sumber perhitungan Total - dipanggil dari SEMUA trigger
    // (input Amount, input Deduction, ganti PPH per baris, ganti PPN per
    // baris, ganti PPN header, delete row), sama seperti perbaikan di
    // create-paymentvoucher.php. Sebelumnya tiap trigger punya hitungan
    // sendiri-sendiri yang saling tumpang tindih/tidak sinkron - baris
    // Deduction pun tidak pernah ikut dihitung PPN/PPH-nya sama sekali.
    function recalcAllRows(){
        var table = document.getElementById("tbody2");
        var subtotal = 0;
        var dedTotal = 0;
        var pphTotal = 0;
        var ppnTotal = 0;

        for (var i = 0; i < table.rows.length; i++) {
            var amountEl = table.rows[i].cells[11].children[0];
            var dedEl    = table.rows[i].cells[12].children[0];
            var pphEl    = table.rows[i].cells[14].children[0];
            var ppnEl    = table.rows[i].cells[15].children[0];

            var amountVal = amountEl.value;
            var dedVal    = dedEl.value;
            var pphPct    = parseFloat(pphEl.value) || 0;
            var ppnPct    = parseFloat(ppnEl.value) || 0;

            var base;
            if (amountVal === '') {
                dedEl.readOnly = false;
                amountEl.readOnly = (dedVal !== '');
                var ded = parseFloat(dedVal) || 0;
                dedTotal += ded;
                base = -ded;
            } else {
                amountEl.readOnly = false;
                dedEl.readOnly = true;
                var amt = parseFloat(amountVal) || 0;
                subtotal += amt;
                base = amt;
            }

            pphTotal += base * (pphPct / 100);
            ppnTotal += base * (ppnPct / 100);
        }

        var dedAdValue = -dedTotal;
        var pphDisplay = -pphTotal;
        var totalH = subtotal + dedAdValue + ppnTotal - pphTotal;

        document.getElementsByName("nomrate_h")[0].value = subtotal.toFixed(2);
        document.getElementsByName("nomrate1")[0].value = formatMoney(subtotal.toFixed(2));
        document.getElementsByName("ded_ad")[0].value = dedAdValue.toFixed(2);
        document.getElementsByName("ded_ad_h")[0].value = formatMoney(dedAdValue.toFixed(2));
        document.getElementsByName("pph_h")[0].value = pphDisplay.toFixed(2);
        document.getElementsByName("pph")[0].value = formatMoney(pphDisplay.toFixed(2));
        document.getElementsByName("ppn_h")[0].value = ppnTotal.toFixed(2);
        document.getElementsByName("ppn")[0].value = formatMoney(ppnTotal.toFixed(2));
        document.getElementsByName("total_h")[0].value = totalH.toFixed(2);
        document.getElementsByName("total")[0].value = formatMoney(totalH.toFixed(2));
    }

    function input_pph(){
        recalcAllRows();
    }

    function input_ppn(){
        recalcAllRows();
    }


function getdate() {
    var pay_date = document.getElementById('tgl_pay').value;
    var table = document.getElementById("tbody2");
    for (var i = 0; i < (table.rows.length); i++) {

    var duedate = document.getElementById("tbody2").rows[i].cells[7].children[0];  
    duedate.value = pay_date;
}
}

// function getdate() {
//     var pay_date = document.getElementById('tgl_pay').value;
//     var table = document.getElementById("tbody2");
//     var rows = table.getElementsByTagName("tr");    
//     for (i = 0; i < rows.length; i++) {
//         var createClickHandler = function(row) {
//         return function() {
//       var currentRow = table.rows[i];

//     row.getElementsByTagName("td")[7];  = pay_date;
//     };
//       };
//       currentRow.onclick = createClickHandler(currentRow);
    
// }
// }
</script>

<script type="text/javascript">
      function modal_input_amt(){
        recalcAllRows();
      }

      function modal_input_dedadd(){
        recalcAllRows();
      }



function modal_input_vat_baru(){ 

    var vat = 0.11; 
    //
    if ($('[name="check_vat_baru"]').is(':checked')) {          
            var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
            var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
            var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            var twot = (total_pv).toFixed(2) * vat;
            var total_h = total_pv - pph_h + twot + ded_ad;
            document.getElementsByName("ppn_h")[0].value = (twot).toFixed(2);
            document.getElementsByName("ppn")[0].value = formatMoney(twot.toFixed(2));
            document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
            document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
  
    } else {        
            var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
            var pph_h = parseFloat(document.getElementById('pph_h').value,10) || 0;
            var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
            var total_h = total_pv - pph_h + ded_ad;

            document.getElementsByName("ppn")[0].value = "0.00";
            document.getElementsByName("ppn_h")[0].value = "0";
            document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
            document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
    }
}


function changeValueTax(id){
    var total_pv = parseFloat(document.getElementById('nomrate_h').value,10) || 0;
    var ppn_h = parseFloat(document.getElementById('ppn_h').value,10) || 0;
    var ded_ad = parseFloat(document.getElementById('ded_ad').value,10) || 0;
    var pph = id;
    var twot2 = (total_pv).toFixed(2) * (pph /100);
    var total_h = total_pv + ppn_h - twot2 + ded_ad;
    document.getElementsByName("pph_h")[0].value = (twot2).toFixed(2);
    document.getElementsByName("pph")[0].value = formatMoney(twot2.toFixed(2));
    document.getElementsByName("total_h")[0].value = (total_h).toFixed(2);
    document.getElementsByName("total")[0].value = formatMoney(total_h.toFixed(2));
}

function changeValueTax2(id){
    document.getElementById('idtax').value = prdName[id].idtax;

    var $rowPpn = $('#tbody2 select[name=ppnn]');
    if (id && id !== '0') {
        $rowPpn.val(id).trigger('change');
        $rowPpn.prop('disabled', true);
    } else {
        $rowPpn.prop('disabled', false);
    }
    recalcAllRows();
}
  </script>

    <!-- Handler keyup ded_ad lama (recalc total pakai formula terpisah dari
         recalcAllRows()) sudah dihapus - dulu bikin Total ketimpa nilai
         salah/0 setiap kali ada event keyup, jalan SETELAH recalcAllRows()
         yang benar (oninput lebih dulu daripada keyup untuk keystroke yang
         sama), jadi hasil yang benar langsung tertimpa lagi. -->

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
</script>

<script type="text/javascript">
    // Balance = Total Memo - Total. total_h dihitung ulang oleh banyak fungsi pajak/deduction
    // berbeda di file ini, jadi dipantau pakai interval (bukan dipanggil manual di tiap fungsi)
    // supaya tidak ada satupun jalur hitung yang terlewat. Save tetap diblok di handler #simpan
    // sebagai jaring pengaman terakhir kalau saja disabled attribute ini bisa terlewat.
    function updateBalanceMemo() {
        var totalMemoH = parseFloat(document.getElementById('total_memo_h').value) || 0;
        var totalH = parseFloat(document.getElementById('nomrate_h').value) || 0;
        var balance = totalMemoH - totalH;
        var balanceField = document.getElementById('balance_memo');

        balanceField.value = formatMoney(balance);

        // Tombol Save tetap bisa diklik - validasi balance dilakukan di handler klik #simpan
        // (munculkan Swal kalau belum 0), bukan dengan disable tombolnya.
        if (Math.abs(balance) > 0.01) {
            balanceField.style.color = '#c0392b';
            balanceField.style.fontWeight = 'bold';
        } else {
            balanceField.style.color = '';
            balanceField.style.fontWeight = '';
        }
    }

    $(document).ready(function () {
        updateBalanceMemo();
        setInterval(updateBalanceMemo, 300);
    });
</script>

<script type="text/javascript">
    // Supplier ini tidak punya rekening tetap di master_supplier_bank (kantor
    // pajak/bea cukai) - To Account diganti isian bebas, bukan dropdown.
    var TOCC_MANUAL_SUPPLIERS = ['KANTOR PAJAK', 'KPPBC TMP A BANDUNG'];

    function toggleToccInputMode() {
        var nama_supp = ($('#nama_supp').val() || '').toUpperCase();
        var isManual = TOCC_MANUAL_SUPPLIERS.indexOf(nama_supp) !== -1;
        var $toccSelect = $('#tocc');
        var $toccSelectWrap = $toccSelect.next('.select2-container');
        var $toccManual = $('#tocc_manual');

        if (isManual) {
            $toccSelect.val('-').trigger('change');
            $toccSelectWrap.hide();
            $toccManual.show();
        } else {
            $toccManual.val('').hide();
            $toccSelectWrap.show();
        }
    }

    // Isi ulang opsi #tocc sesuai Supplier yang dipilih, tanpa reload halaman
    // - sama seperti create-paymentvoucher.php. forpay EXIM selalu "Export -
    // Import" (bukan Pemindah Bukuan Bank/Cicilan Pinjaman Bank), jadi
    // get_pv_to_account.php otomatis selalu ambil rekening SUPPLIER, difilter
    // nama_supp.
    function refreshToAccount() {
        toggleToccInputMode();

        if ($('#tocc_manual').is(':visible')) {
            // Mode isian bebas aktif - tidak perlu ambil daftar rekening.
            return;
        }

        var forpay = $('#forpay').val() || '';
        var nama_supp = $('#nama_supp').val() || '';
        var $tocc = $('#tocc');
        var currentVal = $tocc.val();

        $.ajax({
            url: 'get_pv_to_account.php',
            type: 'POST',
            dataType: 'json',
            data: { forpay: forpay, nama_supp: nama_supp },
            success: function (response) {
                $tocc.empty().append('<option value="-" selected="selected">Select Account</option>');
                $.each(response || [], function (i, acc) {
                    $tocc.append('<option value="' + acc.value + '">' + acc.text + '</option>');
                });
                if (currentVal && $tocc.find('option[value="' + currentVal + '"]').length) {
                    $tocc.val(currentVal);
                }
                $tocc.trigger('change');
            }
        });
    }

    // Toggle tampilan From/To Account berdasarkan Pay Methods tanpa submit
    // form/reload halaman. Sebelumnya pakai onchange="this.form.submit()"
    // yang reload seluruh halaman tiap ganti Pay Methods.
    $('#carabayar').on('change', function () {
        var isCash = ($(this).val() === 'CASH');
        if (isCash) {
            $('#div_frcc, #div_tocc').hide();
        } else {
            $('#div_frcc, #div_tocc').show();
            refreshToAccount();
        }
    });

    $(document).on('change', '#nama_supp', function () {
        // Cek wrapper div-nya, bukan <select> aslinya - select2 selalu
        // menyembunyikan <select> asli (display:none) meski wrapper-nya
        // (#div_tocc) tampil, jadi $('#tocc').is(':visible') selalu false.
        if ($('#div_tocc').is(':visible')) {
            refreshToAccount();
        }
    });

    $(document).ready(function () {
        toggleToccInputMode();
    });
</script>


<!-- Handler keyup txt_amount lama sudah dihapus - formula-nya (BPB/bank
     reconciliation lama, baca td:eq(5)/td:eq(6) yang tidak sesuai struktur
     tabel detail PV ini) selalu menghasilkan 0, dan jalan SETELAH
     recalcAllRows() yang benar (keyup setelah oninput), jadi Total yang
     sudah benar selalu ketimpa jadi 0.00 lagi. Sama seperti bug ded_ad di
     atas. -->

<!-- -->

<script type="text/javascript">
    $("input[name=amount]").keyup(function(){
    var sum_kb = 0;
    var sum_amount = 0;
    var sum_total = 0;
    var sum_balance = 0;        
    $("input[type=checkbox]:checked").each(function () {        
    var amount = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;

    sum_amount += amount;
 
     
    });

    $("#nomrate1").val(formatMoney(sum_amount));    
    $("#nomrate2").val(formatMoney(sum_amount));    

    });
</script>


<script type="text/javascript"> 
<?php echo $jsArray; ?>
function changeValueACC(id){
    var select_rate = document.getElementById('rate');   
    document.getElementById('nama_bank').value = prdName[id].nama_bank;
    document.getElementById('valuta').value = prdName[id].valuta;
    document.getElementById('kode').value = prdName[id].kode;
    if (prdName[id].valuta == 'IDR') {
            select_rate.disabled = true;
        }else{
            select_rate.disabled = false;
        }
};
</script>

<script type="text/javascript">
    $("input[name=rate]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = rate;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#rate_h").val(formatMoney(rat));

    });
</script>

<script type="text/javascript">
    $("input[name=nominal_h]").keyup(function(){
    var ttl_jml = 0;
    var rat = 0;
    var valu = '';
    $("input[type=text]").each(function () {         
    var rate = parseFloat(document.getElementById('rate').value,10) || 1;
    var ttl_h = parseFloat(document.getElementById('nominal_h').value,10) || 0;
    var val = document.getElementById('valuta').value;
    valu = val;
    rat = ttl_h;
    if (valu == 'IDR') {
    ttl_jml = ttl_h / rate;  
    }else{
    ttl_jml = ttl_h * rate;    
    }
    });
   $("#nomrate").val(formatMoney(ttl_jml));
   $("#nomrate_h").val(ttl_jml);
   $("#nominal").val(formatMoney(rat));

    });
</script>

<script type="text/javascript">
    $("#modal-form3").on("click", "#send3", function(){
        var valu = '';
        $("input[type=radio]:checked").each(function () {
        var data = $(this).closest('tr').find('td:eq(1) input').val();
        valu = data;
        console.log(data);
         
             
                  
        });
        $("#txt_forpay").val(valu);
 
    });


</script>


<script type="text/javascript">
// get all number fields
var numInputs = document.querySelectorAll('input[type="number"]');

// Loop through the collection and call addListener on each element
Array.prototype.forEach.call(numInputs, addListener); 


function addListener(elm,index){
  elm.setAttribute('min', 1);  // set the min attribute on each field
  
  elm.addEventListener('keypress', function(e){  // add listener to each field 
     var key = !isNaN(e.charCode) ? e.charCode : e.keyCode;
     str = String.fromCharCode(key); 
    if (str.localeCompare('-') === 0){
       event.preventDefault();
    }
    
  });
  
}
</script>



<script type="text/javascript">
    $("#modal-form2").on("click", "#send2", function(){
        var id_supp = $('select[name=nama_supp_memo] option').filter(':selected').val(); 
        var start_date = document.getElementById('start_date_memo').value;
        var end_date = document.getElementById('end_date_memo').value;        
         
             
        $.ajax({
            type:'POST',
            url:'cari_memo.php',
            data: {'id_supp':id_supp, 'start_date':start_date, 'end_date':end_date},
            cache: 'false',
            close: function(e){
                e.preventDefault();
                return false; 
            },
            success: function(data){
                $('#details').html(data);  
                // console.log(response);
                // $('#modal-form2').modal('toggle');
                // // $('#modal-form2').modal('hide');
                //  // alert("Data saved successfully");
                // window.location.reload(false);
                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire('Error', xhr.responseText || String(xhr), 'error');
            }
        });             
 
                return false; 
    });


</script>

<script type="text/javascript">
    $("#modal-form5").on("click", "#send5", function(){
        // Kirim SATU request ke save_sup_doc_exim.php berisi semua item yang
        // dicentang. Endpoint itu membersihkan dulu baris milik sesi ini lalu
        // insert ulang sesuai checklist saat ini - jadi kalau sebelumnya
        // pilih PO lalu ganti ke SO, PO yang lama ikut terhapus (sebelumnya
        // pakai insertdoc.php per-checkbox yang tidak pernah menghapus
        // pilihan lama, jadi pilihan numpuk terus).
        var $btn = $(this);
        if ($btn.data('saving')) {
            return;
        }
        $btn.data('saving', true).prop('disabled', true);

        var doc_number = document.getElementById('no_doc').value;
        var unik_code = document.getElementById('unik_code').value;
        var items = [];

        $("#doc_support input[type=checkbox]:checked").each(function () {
            var data = $(this).closest('tr').find('td:eq(7) input').val();
            if (data) { items.push(data); }
        });

        $.ajax({
            type: 'POST',
            url: 'save_sup_doc_exim.php',
            dataType: 'json',
            data: { 'doc_number':doc_number, 'unik_code':unik_code, 'items': JSON.stringify(items) },
            cache: 'false',
            success: function (response) {
                $btn.data('saving', false).prop('disabled', false);
                if (response && response.success) {
                    $('#sup_doc').val(response.sup_doc);
                    $('#mymodal5').modal('hide');
                } else {
                    Swal.fire('Error', (response && response.error) || 'Gagal menyimpan Supporting Document.', 'error');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $btn.data('saving', false).prop('disabled', false);
                console.log(xhr);
                Swal.fire('Error', xhr.responseText || String(xhr), 'error');
            }
        });
    });


</script>

<script>
    $("#modal-form2").on("click", "#savememo", function(){
        // Cegah klik ganda - kalau request sebelumnya masih jalan, klik kedua
        // diabaikan total. Tanpa ini, modal yang lambat menutup (lihat bawah)
        // membuat user mudah klik Save lagi sebelum request pertama selesai,
        // sehingga memo yang sama ke-insert dua kali (double).
        var $btn = $(this);
        if ($btn.data('saving')) {
            return;
        }
        $btn.data('saving', true).prop('disabled', true);

        var memoPromises = [];

        $("#table-memo input[type=checkbox]:checked").each(function () {
        var no_memo = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_memo = $(this).closest('tr').find('td:eq(2)').attr('value');
        var jenis_transaksi = $(this).closest('tr').find('td:eq(3)').attr('value');
        var supplier = $(this).closest('tr').find('td:eq(4)').attr('value');
        var biaya = $(this).closest('tr').find('td:eq(5)').attr('value');
        var user = '<?php echo $user ?>';

        memoPromises.push($.ajax({
            type:'POST',
            url:'insert_memo_temp.php',
            data: {'no_memo':no_memo, 'tgl_memo':tgl_memo,'jenis_transaksi':jenis_transaksi, 'supplier':supplier, 'biaya':biaya, 'user':user},
            cache: 'false',
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire('Error', xhr.responseText || String(xhr), 'error');
            }
        }));
        });

        $.when.apply($, memoPromises).then(function(){
            // Tutup modal SEGERA setelah insert selesai - jangan tunggu
            // refresh tabel detail (request kedua, ada banyak query COA/PC/CC
            // di server). Sebelumnya modal baru ditutup setelah KEDUA request
            // selesai, jadi kelihatan macam modalnya tidak langsung close.
            $('#mymodal2').modal('hide');
            $btn.data('saving', false).prop('disabled', false);

            $.getJSON('get_memo_detail_exim.php', function(data){
                $('#tbody2').html(data.rows_html);
                $('#tbody2 .selectpicker').selectpicker();
                $('#tbody2 .tanggal').datepicker({ format: "dd-mm-yyyy", autoclose: true });
                // .trigger('change') wajib - .val() doang di select2 ga
                // mancing listener $(document).on('change', '#nama_supp', ...)
                // yang nyalain mode To Account manual buat supplier kantor
                // pajak/bea cukai (lihat TOCC_MANUAL_SUPPLIERS).
                $('#nama_supp').val(data.nama_supp).trigger('change');
                $('#ct_buyer').val(data.ct_buyer);
                $('#total_memo').val(data.biaya_formatted);
                $('#total_memo_h').val(data.biaya);
                $('#nomrate1').val(data.biaya_formatted);
                $('#nomrate_h').val(data.biaya);
                $('#total').val(data.biaya_formatted);
                $('#total_h').val(data.biaya);
                updateBalanceMemo();
            });
        }, function(){
            $btn.data('saving', false).prop('disabled', false);
        });
    });
</script>

<script type="text/javascript">
    $("#form-data").on("click", "#btn2", function(){
        var user = '<?php echo $user ?>';        
         
             
        $.ajax({
            type:'POST',
            url:'hapus_memo_temp.php',
            data: {'user':user},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                $('#mytable tbody tr').remove(); 
                $('#nomrate1').val("");
                $('#nomrate_h').val("");
                $('#total').val("");
                $('#total_h').val("");

                // $('#modal-form2').modal('toggle');

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire('Error', xhr.responseText || String(xhr), 'error');
            }
        });             
 
    });


</script> 

<script>
let coaWajibCC = [];

// Load sekali saat halaman dibuka - tanpa reload, cuma ambil daftar COA yang
// wajib isi Cost Center dari get_coa_wajib_cc.php (sama seperti di
// create-memorial-journal.php / create-out-bank.php dll).
$.getJSON('get_coa_wajib_cc.php', function(data){
    coaWajibCC = data;
    console.log("COA wajib CC:", coaWajibCC);
});
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        // Cegah klik ganda saat request masih jalan (lihat guard yang sama
        // di #savememo dan #send5 - klik dobel sebelum request pertama
        // selesai bisa membuat PV/baris detail tersimpan dua kali).
        var $btn = $(this);
        if ($btn.data('saving')) {
            return;
        }

        var totalMemoHGuard = parseFloat(document.getElementById('total_memo_h').value) || 0;
        var totalHGuard = parseFloat(document.getElementById('nomrate_h').value) || 0;
        if (Math.abs(totalMemoHGuard - totalHGuard) > 0.01) {
            Swal.fire('Error', 'Balance harus 0 sebelum bisa disimpan. Balance saat ini: ' + formatMoney(totalMemoHGuard - totalHGuard), 'error');
            return;
        }

        var no_pv = document.getElementById('no_doc').value;
        var rat_pv = document.getElementById('rat_pv').value;        
        var pv_date = document.getElementById('tgl_active').value;
        var nama_supp = document.getElementById('nama_supp').value;  
        var sup_doc = document.getElementById('sup_doc').value;        
        var ctb = document.getElementById('ct_buyer').value;    
        var pay_date = document.getElementById('tgl_pay').value;
        var pay_mth = $('select[name=carabayar] option').filter(':selected').val(); 
        var curr = document.getElementById('curre').value; 
        var forpay = document.getElementById('forpay').value;   
        var frcc = $('select[name=frcc] option').filter(':selected').val();
        // Supplier kantor pajak/bea cukai (lihat TOCC_MANUAL_SUPPLIERS) pakai
        // isian bebas #tocc_manual, bukan dropdown #tocc.
        var tocc = $('#tocc_manual').is(':visible') ? $('#tocc_manual').val() : $('select[name=tocc] option').filter(':selected').val();
        var no_cek = document.getElementById('no_cek').value;
        var cek_date = document.getElementById('cek_date').value;
        var ke = document.getElementById('ke').value; 
        var dari = document.getElementById('dari').value;        
        var pesan = document.getElementById('pesan').value;
        var subtotal = document.getElementById('nomrate_h').value || 0;
        var adjust = document.getElementById('ded_ad').value;
        var pph = document.getElementById('pph_h').value;
        var ppn = document.getElementById('ppn_h').value;
        var total = document.getElementById('total_h').value;
        var pilih_ppn = document.getElementById('pilih_ppn').value;
        var pilih_pph = document.getElementById('pilih_pph').value;
        var pv_tax_type = document.getElementById('pv_tax_type').value;
        var create_user = '<?php echo $user; ?>';

        // Validasi dulu SEBELUM kirim ajax apapun, supaya data invalid tidak
        // pernah mulai terkirim (sebelumnya validasi ini baru dicek setelah
        // semua ajax sudah ditembak).
        if(document.getElementById('nama_supp').value == '' || document.getElementById('nama_supp').value == '-'){
        Swal.fire('Error', 'Please select Supplier', 'error');
        document.getElementById('nama_supp').focus();
        return;
        }else if(document.getElementById('sup_doc').value == ''){
        Swal.fire('Error', 'Please Select Support Document', 'error');
        document.getElementById('sup_doc').focus();
        return;
        }else if(document.getElementById('ct_buyer').value == ''){
        Swal.fire('Error', 'Please select Charge to Buyer', 'error');
        document.getElementById('ct_buyer').focus();
        return;
        }else if($('select[name=carabayar] option').filter(':selected').val() == '' || $('select[name=carabayar] option').filter(':selected').val() == '-'){
        Swal.fire('Error', 'Please select payment method', 'error');
        document.getElementById('carabayar').focus();
        return;
        }else if(document.getElementById('curre').value == ''){
            Swal.fire('Error', 'Please select currency', 'error');
        document.getElementById('curre').focus();
        return;
        }else if(pv_tax_type == '' || pv_tax_type == null){
        Swal.fire('Error', 'Please select Payment Voucher Type', 'error');
        document.getElementById('pv_tax_type').focus();
        return;
        }else if(document.getElementById('forpay').value == '' || document.getElementById('forpay').value == '-'){
        Swal.fire('Error', 'Please select For payment', 'error');
        document.getElementById('forpay').focus();
        return;
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && $('select[name=frcc] option').filter(':selected').val() == ''){
        Swal.fire('Error', 'Please select From Account', 'error');
        document.getElementById('frcc').focus();
        return;
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && document.getElementById('forpay').value == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() == '-'){
        Swal.fire('Error', 'Please select From Account', 'error');
        document.getElementById('frcc').focus();
        return;
        }else if($('select[name=carabayar] option').filter(':selected').val() != 'CASH' && document.getElementById('forpay').value == 'Pemindah Bukuan Bank' && $('select[name=frcc] option').filter(':selected').val() != '-' && (tocc == '-' || tocc == '' || tocc == null)){
        Swal.fire('Error', 'Please select/isi To Account', 'error');
        (($('#tocc_manual').is(':visible')) ? document.getElementById('tocc_manual') : document.getElementById('tocc')).focus();
        return;
        }else if(document.getElementById('total_h').value == ''){
        Swal.fire('Error', 'Please Input Amount', 'error');
        return;
        }else if(document.getElementById('total_h').value <= '0'){
        Swal.fire('Error', "Amount can't be Minus", 'error');
        return;
        }else if(document.getElementById('total_h').value == '0.00'){
        Swal.fire('Error', "Total Amount can't be Zero", 'error');
        return;
        }

        // COA dan Profit Center di tiap baris detail wajib diisi, tidak boleh
        // kosong atau "-" (placeholder default selectpicker). Selain itu,
        // sebagian COA wajib disertai Cost Center (daftarnya dari
        // get_coa_wajib_cc.php / coaWajibCC). Semua dicek di sini, sebelum
        // ajax apapun dikirim, supaya baris yang belum lengkap tidak ikut
        // tersimpan ke database.
        var rowValidationError = null;
        $("#tbody2 input[type=checkbox]:checked").each(function (rowIdx) {
            var no_coa = $(this).closest('tr').find('td:eq(7)').find('select[name=nomor_coa] option').filter(':selected').val();
            var prof_ctr = $(this).closest('tr').find('td:eq(8)').find('select[id=prof_ctr] option').filter(':selected').val();
            var no_cc = $(this).closest('tr').find('td:eq(9)').find('select[name=nomor_cc] option').filter(':selected').val();

            if (!no_coa || no_coa == '-') {
                rowValidationError = 'COA pada baris detail ke-' + (rowIdx + 1) + ' tidak boleh kosong atau "-"';
                return false;
            }
            if (!prof_ctr || prof_ctr == '-') {
                rowValidationError = 'Profit Center pada baris detail ke-' + (rowIdx + 1) + ' tidak boleh kosong atau "-"';
                return false;
            }
            if (coaWajibCC.includes(no_coa) && (!no_cc || no_cc == '-')) {
                rowValidationError = 'COA ' + no_coa + ' pada baris detail ke-' + (rowIdx + 1) + ' wajib mengisi Cost Center';
                return false;
            }
        });

        if (rowValidationError !== null) {
            Swal.fire('Error', rowValidationError, 'error');
            return;
        }

        if (!(total >= '1' && curr !='' && pay_mth != '' && forpay != '' && pay_mth == 'CASH' && ctb != '' && nama_supp != '' || total >= '1' && curr !='' && forpay != '' && pay_mth != '' && pay_mth != 'CASH' && ctb != '' && nama_supp != '' && frcc != '')) {
            return;
        }

        // Kumpulkan semua baris detail jadi satu array, dikirim dalam SATU
        // request ke save_pv_exim.php yang membungkus header + semua detail
        // dalam satu transaction MySQL. Sebelumnya tiap baris dikirim lewat
        // ajax terpisah (insertpv.php dipanggil berkali-kali) - kalau salah
        // satu gagal di tengah jalan, baris lain yang sudah terkirim tetap
        // ke-commit sendiri-sendiri (tidak atomic, bisa nyangkut sebagian).
        var rows = [];

        $("#tbody2 input[type=checkbox]:checked").each(function () {
            var no_coa = $(this).closest('tr').find('td:eq(7)').find('select[name=nomor_coa] option').filter(':selected').val();
            var prof_ctr = $(this).closest('tr').find('td:eq(8)').find('select[id=prof_ctr] option').filter(':selected').val();
            var no_cc = $(this).closest('tr').find('td:eq(9)').find('select[name=nomor_cc] option').filter(':selected').val();
            var no_ref = $(this).closest('tr').find('td:eq(1) input').val();
            var deskripsi = $(this).closest('tr').find('td:eq(10) textarea').val();
            var amount = $(this).closest('tr').find('td:eq(11) input').val() || 0;
            var due_date = $(this).closest('tr').find('td:eq(13) input').val();
            var ded_add = $(this).closest('tr').find('td:eq(12) input').val() || 0;
            var pph_row = $(this).closest('tr').find('td:eq(14)').find('select[name=pphh] option').filter(':selected').val() || 0;
            var idtax = $(this).closest('tr').find('td:eq(14)').find('select[name=pphh] option').filter(':selected').attr('data-idtax');
            var ppn_row = $(this).closest('tr').find('td:eq(15)').find('select[name=ppnn] option').filter(':selected').val() || pilih_ppn;
            var id_ppn = $(this).closest('tr').find('td:eq(16)').find('select[name=ppnn] option').filter(':selected').attr('data-idtax') || document.getElementById('idtax').value;

            rows.push({
                no_coa: no_coa,
                prof_ctr: prof_ctr,
                no_cc: no_cc,
                no_ref: no_ref,
                deskripsi: deskripsi,
                amount: amount,
                due_date: due_date,
                ded_add: ded_add,
                pph: pph_row,
                idtax: idtax,
                ppn: ppn_row,
                id_ppn: id_ppn
            });
        });

        $btn.data('saving', true).prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: 'save_pv_exim.php',
            dataType: 'json',
            data: {
                'rat_pv':rat_pv, 'no_pv':no_pv, 'pv_date':pv_date, 'nama_supp':nama_supp, 'sup_doc':sup_doc,
                'ctb':ctb, 'pay_date':pay_date, 'pay_mth':pay_mth, 'curr':curr, 'forpay':forpay, 'frcc':frcc,
                'tocc':tocc, 'no_cek':no_cek, 'cek_date':cek_date, 'ke':ke, 'dari':dari, 'pesan':pesan,
                'subtotal':subtotal, 'adjust':adjust, 'pph':pph, 'ppn':ppn, 'total':total,
                'pilih_ppn':pilih_ppn, 'pilih_pph':pilih_pph, 'pv_tax_type':pv_tax_type, 'create_user':create_user,
                'rows': JSON.stringify(rows)
            },
            cache: 'false',
            success: function (response) {
                if (response && response.success) {
                    // Tahan 5 detik dan tetap tampilkan tombol OK supaya user
                    // sempat baca No PV yang terbentuk - sebelumnya langsung
                    // redirect tanpa menunggu Swal-nya, jadi kelihatan macam
                    // notifnya "langsung hilang".
                    Swal.fire({
                        icon: 'success',
                        title: 'Data berhasil disimpan',
                        html: 'No PV: <b>' + (response.no_pv || '-') + '</b>',
                        timer: 5000,
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    }).then(function () {
                        window.location = 'payment-voucher.php';
                    });
                } else {
                    $btn.data('saving', false).prop('disabled', false);
                    Swal.fire('Error', (response && response.error) || 'Gagal menyimpan Payment Voucher.', 'error');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $btn.data('saving', false).prop('disabled', false);
                console.log(xhr);
                var msg = 'Gagal menyimpan Payment Voucher.';
                try {
                    var parsed = JSON.parse(xhr.responseText);
                    if (parsed && parsed.error) { msg = parsed.error; }
                } catch (e) {}
                Swal.fire('Error', msg, 'error');
            }
        });
    });
</script>

<script type="text/javascript">
$("#select_all").click(function() {
  var c = this.checked;
  $(':checkbox').prop('checked', c);
});  
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#batal", function(){
        $("#doc_support input[type=checkbox]:checked").each(function () {
        var doc_number = document.getElementById('no_doc').value;
         
             
        $.ajax({
            type:'POST',
            url:'hapusdoc.php',
            data: {'doc_number':doc_number},
            cache: 'false',
            close: function(e){
                e.preventDefault();
            },
            success: function(response){
                console.log(response);
                // $('#modal-form2').modal('toggle');

                // return false; 
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire('Error', xhr.responseText || String(xhr), 'error');
            }
        });             
        });
 
    });


</script>

<script type="text/javascript">
    document.getElementById('btnpv').onclick = function () {
    location.href = "create-paymentvoucher.php";
};
</script>

<script type="text/javascript">
    document.getElementById('btnpve').onclick = function () {
    location.href = "create-paymentvoucher-exim.php";
};
</script>

<!-- Tab Payment Voucher FTR di-hide, handler klik-nya ikut dinonaktifkan
<script type="text/javascript">
    document.getElementById('btnpvftr').onclick = function () {
    location.href = "create-paymentvoucher-ftr.php";
};
</script>
-->

<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
    $('#mymodalkbon').modal('show');
    var no_kbon = $(this).closest('tr').find('td:eq(1)').attr('value');
    var tgl_kbon = $(this).closest('tr').find('td:eq(2)').text();
    var supp = $(this).closest('tr').find('td:eq(9)').attr('value');
    var tgl_tempo = $(this).closest('tr').find('td:eq(7)').text();
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var create_user = $(this).closest('tr').find('td:eq(16)').attr('value');
    var status = $(this).closest('tr').find('td:eq(17)').attr('value');
    var no_faktur = $(this).closest('tr').find('td:eq(18)').attr('value');
    var supp_inv = $(this).closest('tr').find('td:eq(15)').attr('value');
    var tgl_inv = $(this).closest('tr').find('td:eq(19)').text();                

    $.ajax({
    type : 'post',
    url : 'ajaxkbon.php',
    data : {'no_kbon': no_kbon},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_kbon').html(no_kbon);
    $('#txt_tgl_kbon').html('Tgl Kontrabon : ' + tgl_kbon + '');
    $('#txt_nama_supp').html('Supplier : ' + supp + '');
    $('#txt_tgl_tempo').html('Tgl Jatuh Tempo : ' + tgl_tempo + '');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_create_user').html('Create By : ' + create_user + '');
    $('#txt_status').html('Status : ' + status + '');
    $('#txt_no_faktur').html('No Faktur : ' + no_faktur + '');
    $('#txt_supp_inv').html('No Supplier Invoice : ' + supp_inv + '');
    $('#txt_tgl_inv').html('Tgl Supplier Invoice : ' + tgl_inv + '');                               
});

</script> -->

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
