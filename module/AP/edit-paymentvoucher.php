<?php include '../header.php' ?>
<?php
// Halaman ini dipakai untuk EDIT payment voucher yang sudah ada (bukan buat
// baru). Datanya dimuat SEKALI di sini berdasarkan no_pv dari URL, lalu
// "dituangkan" ke $_POST dengan key yang SAMA PERSIS seperti yang dipakai
// field-field di bawah (semuanya sudah pola `if REQUEST_METHOD == POST`
// dari create-paymentvoucher.php) - supaya seluruh markup/JS validasi/
// perhitungan yang sudah teruji di form Create bisa dipakai apa adanya di
// sini tanpa mengubah setiap field satu-satu.
$no_pv = isset($_GET['no_pv']) ? base64_decode($_GET['no_pv']) : '';
$pvHeader = null;
$pvDetails = [];

if ($no_pv !== '' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $no_pv_esc = mysqli_real_escape_string($conn2, $no_pv);
    $sqlHeader = mysqli_query($conn2, "select * from tbl_pv_h where no_pv = '$no_pv_esc'");
    $pvHeader = mysqli_fetch_assoc($sqlHeader);

    if ($pvHeader) {
        $sqlDetails = mysqli_query($conn2, "select * from tbl_pv where no_pv = '$no_pv_esc' order by id");
        while ($d = mysqli_fetch_assoc($sqlDetails)) {
            $pvDetails[] = $d;
        }

        $cekDateVal = '';
        if (!empty($pvHeader['cek_date']) && $pvHeader['cek_date'] !== '0000-00-00' && strtotime($pvHeader['cek_date']) > 0 && date('Y-m-d', strtotime($pvHeader['cek_date'])) !== '1970-01-01') {
            $cekDateVal = date('d-m-Y', strtotime($pvHeader['cek_date']));
        }

        $_POST['nama_supp']  = $pvHeader['nama_supp'];
        $_POST['sup_doc']    = $pvHeader['supp_doc'];
        $_POST['ctb']        = $pvHeader['ctb'];
        $_POST['carabayar']  = $pvHeader['pay_meth'];
        $_POST['curre']      = $pvHeader['curr'];
        $_POST['forpay']     = $pvHeader['for_pay'];
        $_POST['pv_tax_type']= $pvHeader['pv_tax_type'];
        $_POST['frcc']       = $pvHeader['frm_akun'];
        $_POST['tocc']       = $pvHeader['to_akun'];
        $_POST['ke']         = $pvHeader['ke'];
        $_POST['dari']       = $pvHeader['dari'];
        $_POST['no_cek']     = $pvHeader['no_cek'];
        $_POST['cek_date']   = $cekDateVal;
        $_POST['pesan']      = $pvHeader['deskripsi'];
        $_POST['tgl_active'] = !empty($pvHeader['pv_date']) ? date('d-m-Y', strtotime($pvHeader['pv_date'])) : date('d-m-Y');
        $_POST['tgl_pay']    = !empty($pvHeader['pay_date']) ? date('d-m-Y', strtotime($pvHeader['pay_date'])) : date('d-m-Y');
        $_POST['pilih_ppn']  = $pvHeader['per_ppn'];
        $_POST['pilih_pph']  = $pvHeader['per_pph'];

        // Beberapa blok field di bawah expect REQUEST_METHOD == 'POST' biar
        // mereka mau baca dari $_POST (lihat pola aslinya di
        // create-paymentvoucher.php). Halaman ini sendiri tidak punya proses
        // simpan lewat POST biasa (Save dikirim lewat AJAX ke
        // update_pv_h.php), jadi aman untuk "pura-pura" POST di sini.
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }
}

if ($no_pv === '' || !$pvHeader) {
    echo '<div class="col p-4"><div class="alert alert-danger">Payment Voucher tidak ditemukan.</div></div></body></html>';
    exit;
}
?>

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

    .table-gradient2 th{ background:#3B82F6; color:#fff; text-align:center; vertical-align:middle; white-space:nowrap; }

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
        <h5 class="mb-0"><i class="fa fa-file-text-o"></i> FORM EDIT PAYMENT VOUCHER</h5>
    </div>
    <div class="box header">
<form id="form-data" method="post">
    <div style="padding:5px 10px 10px;">
            <!-- Mode edit - form_type mengikuti data yang sedang diedit, tidak
                 bisa ditukar Regular/EXIM di tengah proses edit (beda alur/
                 field), jadi tab pindah menu diganti badge status saja. -->
            <span class="badge badge-primary" style="font-size:13px;padding:6px 12px;">Editing: Payment Voucher (Regular)</span>
        </div>
        <div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>No Payment Voucher</b></label>
                <?php
            // Mode EDIT - no_pv sudah ada (dari record yang dimuat), TIDAK
            // regenerate/increment nomor baru seperti di form Create.
            echo '<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.htmlspecialchars($no_pv).'">';
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
              <select class="form-control select2" name="nama_supp" id="nama_supp" style="width:100%">
                <option value="-" disabled selected="true">Select Supplier</option>
                <?php
                $nama_supp ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $nama_supp){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

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
               value="<?= htmlspecialchars($pvHeader['supp_doc'] ?? ''); ?>">

        <div class="input-group-append">
            <button type="button"
                    class="btn btn-info"
                    id="btn2"
                    data-toggle="modal"
                    data-target="#mymodal2">
                Select
            </button>
        </div>
    </div>
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

            <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="ct_buyer"><b>Charge To Buyer</b></label>            
              <select class="form-control select2" name="ct_buyer" id="ct_buyer" style="width:100%">
                <option value="" disabled selected="true">Select Buyer</option> 
                <option value="-" <?php
                $ct_buyer = '';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ct_buyer = isset($_POST['ct_buyer']) ? $_POST['ct_buyer']: null;
                }                 
                    if($ct_buyer == '-'){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo $isSelected;
                ?>                
                >-</option>                                                 
                <?php
                $ct_buyer ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $ct_buyer = isset($_POST['ct_buyer']) ? $_POST['ct_buyer']: null;
                }                 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'C' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $ct_buyer){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>

                </div>
                <div class="col-md-4 mb-3">
                </div>
                <div class="col-md-3 mb-3"> 
                    <label for="carabayar" class="col-form-label" style="width: 150px;">Pay Methods </label>               
                <select class="form-control select2" name="carabayar" id="carabayar" style="width:100%">
                    <option value="" disabled selected="true">Choose pay method</option>
                    <?php
                $carabayar ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $carabayar = isset($_POST['carabayar']) ? $_POST['carabayar']: null;
                }                 
                $sql = mysqli_query($conn1,"select pay_method from tbl_paymethod ");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['pay_method'];
                    if($row['pay_method'] == $carabayar){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?> 
        
                </select>
                </div>
                <!--
                    From Account/To Account dipindah ke sini (baris Pay
                    Methods) atas permintaan user, ditukar posisi dengan
                    Payment Voucher Type/Currency yang sekarang pindah ke
                    baris For Payment di bawah. ID/nama tetap sama, cuma
                    posisi DOM-nya yang berubah - JS toggle & AJAX tetap
                    pilih elemen lewat ID jadi tidak terpengaruh.
                -->
                <div class="col-md-2 mb-3" id="div_frcc" style="padding-top: 8px;">
                    <label for="frcc"><b>From Account</b></label>
                    <?php $frccVal = ($_SERVER['REQUEST_METHOD'] == 'POST') ? ($_POST['frcc'] ?? '') : ''; ?>
                    <select class="form-control select2" name="frcc" id="frcc" style="width:100%">
                        <option value="-"<?= ($frccVal === '' || $frccVal === '-') ? ' selected="selected"' : ''; ?>>Select Account</option>
                        <?php
                        $sql = mysqli_query($conn1, "select coa_name as bank, bank_account as akun from b_masterbank where status = 'Active' group by id");
                        while ($row = mysqli_fetch_array($sql)) {
                            $sel = ($row['akun'] === $frccVal) ? ' selected' : '';
                            echo '<option value="' . $row['akun'] . '"'.$sel.'>' . $row['bank'] . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-3" id="div_tocc" style="padding-top: 8px;">
                    <label for="tocc"><b>To Account</b></label>
                    <?php $toccVal = ($_SERVER['REQUEST_METHOD'] == 'POST') ? ($_POST['tocc'] ?? '') : ''; ?>
                    <select class="form-control select2" name="tocc" id="tocc" style="width:100%">
                        <option value="-"<?= ($toccVal === '' || $toccVal === '-') ? ' selected="selected"' : ''; ?>>Select Account</option>
                        <?php
                        $sql = mysqli_query($conn2, "SELECT ms.Supplier AS supplier, CONCAT(UPPER(TRIM(m.bank_name)),' ',UPPER(TRIM(m.bank_account))) AS bank, UPPER(TRIM(m.bank_account)) AS akun FROM master_supplier_bank m JOIN mastersupplier ms ON ms.id_supplier = m.id_supplier WHERE m.status = 'Active' AND m.tipe_sup = 'S' ORDER BY ms.Supplier, m.bank_name");
                        while ($row = mysqli_fetch_array($sql)) {
                            $sel = ($row['akun'] === $toccVal) ? ' selected' : '';
                            echo '<option value="' . $row['akun'] . '"'.$sel.'>' . $row['bank'] . '</option>';
                        }
                        ?>
                    </select>
                    <!-- Untuk Supplier KANTOR PAJAK/KPPBC TMP A BANDUNG, To Account tidak
                         punya rekening tetap di master_supplier_bank - diganti isian bebas
                         lewat toggleToccInputMode() (lihat script di bawah). -->
                    <input type="text" class="form-control" id="tocc_manual" name="tocc_manual" style="width:100%; height: calc(1.5em + .75rem + 2px); display:none;" placeholder="Enter To Account" autocomplete="off" value="<?= htmlspecialchars($toccVal); ?>">
                </div>
        </div>
        </br>
<div class="form-row">
    <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>For Payment</b></label>
              <select class="form-control select2" name="forpay" id="forpay" style="width:100%">
                <option value="-" disabled selected="true">Select For Payment</option>
                <?php
                $forpay ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $forpay = isset($_POST['forpay']) ? $_POST['forpay']: null;
                }
                // Kalau for_pay yang tersimpan bukan salah satu opsi baku (di
                // create-mode ini berarti user pilih "Lainnya" lalu ketik teks
                // bebas, dan teks bebas ITU yang disimpan ke kolom for_pay,
                // bukan literal "Lainnya") - dropdown-nya diarahkan ke opsi
                // "Lainnya" dan teksnya dituangkan ke field "For payment Other".
                $forpayOptions = [];
                $sql = mysqli_query($conn1,"select ref_doc from master_forpay where ket = '1'");
                while ($row = mysqli_fetch_array($sql)) {
                    $forpayOptions[] = $row['ref_doc'];
                }
                $forpayIsCustom = ($forpay !== '' && $forpay !== null && !in_array($forpay, $forpayOptions, true));
                foreach ($forpayOptions as $data) {
                    $isSelected = ($forpayIsCustom && $data === 'Lainnya') ? ' selected="selected"' : (($data == $forpay) ? ' selected="selected"' : '');
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                }?>
                </select>


        </div>
        <div class="col-md-2 mb-3">
            <label for="pv_tax_type" class="col-form-label" style="width: 150px;">Payment Voucher Type</label>
            <select class="form-control select2" name="pv_tax_type" id="pv_tax_type" style="width:100%">
                <option value="" disabled selected="selected">Select Type</option>
                <?php
                $pvTaxTypeVal = ($_SERVER['REQUEST_METHOD'] == 'POST') ? ($_POST['pv_tax_type'] ?? '') : '';
                ?>
                <option value="Tax"<?= ($pvTaxTypeVal === 'Tax') ? ' selected' : ''; ?>>Tax</option>
                <option value="Non Tax"<?= ($pvTaxTypeVal === 'Non Tax') ? ' selected' : ''; ?>>Non Tax</option>
            </select>
        </div>
        <div class="col-md-1 mb-3">
            <label for="curre" class="col-form-label" style="width: 150px;">Currency </label>
            <select class="form-control select2" name="curre" id="curre" style="width:100%">
                <option value="" disabled selected="true">Curr</option>
                <?php
                $curre ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $curre = isset($_POST['curre']) ? $_POST['curre']: null;
                }
                $sql = mysqli_query($conn1,"select DISTINCT curr from b_masterbank union select 'EUR' curr ");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['curr'];
                    if($row['curr'] == $curre){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';

                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                }?>
            </select>
        </div>
        <!--
            Dulu blok ini di-render dua kali secara terpisah oleh PHP,
            dua blok kondisi berbeda berdasarkan $_POST forpay/carabayar
            saat halaman reload penuh, menghasilkan select#frcc/#tocc yang
            dobel/duplikat id di beberapa kombinasi. Sekarang cukup satu set
            field statis, ditampilkan/disembunyikan via JS (lihat fungsi
            updateForPaymentFields()) tanpa reload - field & nama tetap sama
            persis supaya insertpv_h.php tidak perlu diubah.
        -->
        <div class="col-md-1 mb-3" id="div_ke">
            <label for="ke" class="col-form-label" style="width: 150px;"><b>To</b></label>
            <input type="text" style="font-size: 14px;" class="form-control" id="ke" name="ke" value="<?= htmlspecialchars($pvHeader['ke'] ?? ''); ?>" autocomplete="off">
        </div>

        <div class="col-md-1 mb-3" id="div_dari">
            <label for="dari" class="col-form-label" style="width: 150px;"><b>From</b></label>
            <input type="text" style="font-size: 14px;" class="form-control" id="dari" name="dari" value="<?= htmlspecialchars($pvHeader['dari'] ?? ''); ?>" autocomplete="off">
        </div>

        <div class="col-md-2 mb-3" id="div_payfor">
            <label for="pay_for" class="col-form-label" style="width: 150px;"><b>For payment Other</b></label>
            <input type="text" style="font-size: 14px;" class="form-control" id="pay_for" name="pay_for" value="<?= $forpayIsCustom ? htmlspecialchars($forpay) : ''; ?>" autocomplete="off">
        </div>

        <input type="hidden" style="font-size: 14px;" class="form-control" id="no_cek" name="no_cek" value="<?= htmlspecialchars($pvHeader['no_cek'] ?? ''); ?>" autocomplete="off">
        <input type="hidden" style="font-size: 14px;" class="form-control" id="cek_date" name="cek_date" value="<?= htmlspecialchars($_POST['cek_date'] ?? ''); ?>" autocomplete="off">
    </div>


<div class="form-row">


        <div class="col-md-8 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>Description</b></label>
                <textarea style="font-size: 15px; text-align: left;" cols="30" rows="2" class="form-control " name="pesan" id="pesan" placeholder="Description..."><?= htmlspecialchars($_POST['pesan'] ?? ''); ?></textarea>
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
                    if($row['name'] == $dari_akun){
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
                    if($row['name'] == $ke_akun){
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
                    <button type="submit" id="send3" name="send3" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
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
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading">Choose Supporting Document</h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form id="modal-form2" method="post">
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
                            echo '<td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$row1['ref_doc'].'" disabled>
                            </td>                                                                                                 
                        </tr>';
                   }
                   echo '<tr>';
                   echo '<tr>'; 
                    if ($ket2 != '') {
                         echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                         
                            <td style="width:150px;">
                            <input style="text-align: left;"  style="font-size: 12px;" class="form-control" id="data-total-ro" name="data-total-ro"  value="'.$ket2.'" >
                            </td>                                                                                                 
                        </tr>'; 
                     } else{
                    echo'<td style="width:10px;"><input type="checkbox" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                         
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
                    <button type="button" id="send2" name="send2" class="btn btn-warning btn-lg" style="width: 100%;"><span class="fa fa-check"></span>
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
        
            <div class="col-md-12">

            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead class="table-gradient2">
        <tr><th class="text-center" style="width: 2%">-</th>
            <th class="text-center" style="width: 12%">COA</th>
            <th class="text-center" style="width: 10%">Profit Center</th>
            <th class="text-center" style="width: 10%">Cost Center</th>
            <th class="text-center" style="width: 9%">Reff Doc</th>
            <th class="text-center" style="width: 9%">Reff Date</th>
            <th class="text-center" style="width: 11%">Description</th>
            <th class="text-center" style="width: 9%">Amount</th>
            <th class="text-center" style="width: 9%">Deduction</th>
            <th class="text-center" style="width: 9%">Due date</th>
            <th class="text-center" style="width: 10%">PPH</th>
            <th class="text-center" style="width: 10%">PPN</th>
            <th class="text-center" style="width: 2%"> Action </th>
        </tr>
    </thead>
    
    <tbody id="tbody2">
        <tr style="display: none;">
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td >
                <select class="form-control" name="nomor_coa" id="nomor_coa" > <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td >
                <select class="form-control" name="prof_ctr" id="prof_ctr" > <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'"); foreach ($sql as $cc) : echo'<option value="'.$cc["kode_pc"].'"> '.$cc["tampil"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td >
                <select class="form-control select2abs4 nomor_cc" name="nomor_cc[]" id="nomor_cc" style="width: 250px"> <option value="-" > - </option>';
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'>
            </td>
            <td>
                <input type="text" style="font-size: 15px;" name="tgl_active" id="tgl_active" class='form-control tanggal' 
            value='' autocomplete='off' placeholder="dd-mm-yyyy">
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'> 
            </td>
            <td>
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
            </td>
            <td>
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_dedadd(value)" autocomplete = "off">
            </td>
            <td>
                <input type="text" style="font-size: 15px;" name="tgl_tempo" id="tgl_tempo" class="form-control tanggal" 
         autocomplete='off' placeholder="dd-mm-yyyy" value='<?= date("d-m-Y"); ?>'>
            </td>
            <td >
                <select class="form-control" name="pphh" id="pphh"  onchange="input_pph()"> <option data-idtax="0" value="0" > Non PPH </option> <?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH'  GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td >
                <select class="form-control" name="ppnn" id="ppnn"  onchange="input_ppn()"> <option data-idtax="" value="" > Non PPN </option> <?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN'  GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
                </select>
            </td>

            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
        </tr>
        <?php
        // Baris detail yang SUDAH TERSIMPAN untuk PV ini - dirender di sini
        // (bukan lewat addRow() JS) supaya langsung kelihatan begitu halaman
        // dibuka. Markup select/kolom sengaja disamakan persis dengan
        // addRow() (lihat cell index yang dipakai recalcAllRows()) supaya
        // Add Row/Delete Row/hitung ulang tetap konsisten dengan baris lama.
        foreach ($pvDetails as $d) {
            $dCoa = $d['coa'];
            $dProfCtr = $d['profit_center'];
            $dNoCc = $d['no_cc'];
            $dReffDoc = $d['reff_doc'];
            $dReffDate = (!empty($d['reff_date']) && $d['reff_date'] !== '0000-00-00' && date('Y-m-d', strtotime($d['reff_date'])) !== '1970-01-01') ? date('d-m-Y', strtotime($d['reff_date'])) : '';
            $dDeskripsi = $d['deskripsi'];
            $dAmount = ((float) $d['amount'] !== 0.0) ? $d['amount'] : '';
            $dDedAdd = ((float) $d['ded_add'] !== 0.0) ? $d['ded_add'] : '';
            $dDueDate = (!empty($d['due_date']) && $d['due_date'] !== '0000-00-00' && date('Y-m-d', strtotime($d['due_date'])) !== '1970-01-01') ? date('d-m-Y', strtotime($d['due_date'])) : date('d-m-Y');
            $dPph = $d['pph'];
            $dPpn = $d['ppn'];
            ?>
        <tr>
            <td><input type="checkbox" name="select[]" value="" checked></td>
            <td>
                <select class="form-control selectpicker no_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="100%" data-size="5">
                    <option value="-"<?= ($dCoa === '' || $dCoa === '-') ? ' selected' : ''; ?>> - </option>
                    <?php
                    $sqlCoa = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa,' ', nama_coa) as coa from mastercoa_v2");
                    foreach ($sqlCoa as $cc) {
                        $sel = ($cc['id_coa'] == $dCoa) ? ' selected' : '';
                        echo '<option value="'.$cc['id_coa'].'"'.$sel.'>'.$cc['coa'].'</option>';
                    }
                    ?>
                </select>
            </td>
            <td>
                <select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-live-search="true" data-width="100%" data-size="5">
                    <option value="-"<?= ($dProfCtr === '' || $dProfCtr === '-') ? ' selected' : ''; ?>> - </option>
                    <?php
                    $sqlPc = mysqli_query($conn1, "select kode_pc, id_pc, nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                    foreach ($sqlPc as $fc) {
                        $sel = ($fc['kode_pc'] == $dProfCtr) ? ' selected' : '';
                        echo '<option value="'.$fc['kode_pc'].'"'.$sel.'>'.$fc['tampil'].'</option>';
                    }
                    ?>
                </select>
            </td>
            <td>
                <select class="form-control selectpicker nomor_cc" name="nomor_cc[]" id="nomor_cc" data-live-search="true" data-width="100%" data-size="5">
                    <option value="-"<?= ($dNoCc === '' || $dNoCc === '-') ? ' selected' : ''; ?>> - </option>
                    <?php
                    // Sama seperti getCostCenter.php: CC difilter Profit Center +
                    // kategori COA (support_gen_adm/support_prod/prod/support_sell).
                    if ($dProfCtr !== '' && $dProfCtr !== '-' && $dCoa !== '' && $dCoa !== '-') {
                        $dProfCtrEsc = mysqli_real_escape_string($conn1, $dProfCtr);
                        $dCoaEsc = mysqli_real_escape_string($conn1, $dCoa);
                        $sqlGroup = mysqli_query($conn1, "
                            SELECT TRIM(BOTH ',' FROM CONCAT(
                                IF(support_gen_adm = 'Y','''SUPPORTING GENERAL & ADMINISTRATION'',',''),
                                IF(support_prod = 'Y','''SUPPORTING PRODUCTION'',',''),
                                IF(prod = 'Y','''PRODUCTION'',',''),
                                IF(support_sell = 'Y','''SUPPORTING SELLING'',','')
                            )) AS groups
                            FROM mastercoa_v2 WHERE no_coa = '$dCoaEsc'
                        ");
                        $rowGroup = mysqli_fetch_assoc($sqlGroup);
                        $groupFilter = $rowGroup['groups'] ?? '';
                        if ($groupFilter !== '') {
                            $sqlCc = mysqli_query($conn1, "select no_cc, CONCAT(no_cc,' - ',cc_name) tampil from b_master_cc where id_pc = '$dProfCtrEsc' and status = 'Active' and group2 IN ($groupFilter)");
                            foreach ($sqlCc as $cc2) {
                                $sel = ($cc2['no_cc'] == $dNoCc) ? ' selected' : '';
                                echo '<option value="'.$cc2['no_cc'].'"'.$sel.'>'.$cc2['tampil'].'</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </td>
            <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" value="<?= htmlspecialchars($dReffDoc); ?>" placeholder="" autocomplete="off"></td>
            <td><input type="text" style="font-size: 12px;" name="tgl_active" class="form-control tanggal" value="<?= htmlspecialchars($dReffDate); ?>" autocomplete="off" placeholder="dd-mm-yyyy"></td>
            <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" value="<?= htmlspecialchars($dDeskripsi); ?>" placeholder="" autocomplete="off"></td>
            <td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" name="txt_amount" value="<?= htmlspecialchars($dAmount); ?>" oninput="modal_input_amt(value)" autocomplete="off"></td>
            <td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" name="txt_credit" value="<?= htmlspecialchars($dDedAdd); ?>" oninput="modal_input_dedadd(value)" autocomplete="off"></td>
            <td><input type="text" style="font-size: 12px;" name="tgl_tempo" class="form-control tanggal" value="<?= htmlspecialchars($dDueDate); ?>" autocomplete="off" placeholder="dd-mm-yyyy"></td>
            <td>
                <select class="form-control select2add" name="pphh" style="width:100%" onchange="input_pph()">
                    <option data-idtax="0" value="0"> - </option>
                    <?php
                    $sqlPph = mysqli_query($conn1, "select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH' GROUP BY idtax");
                    foreach ($sqlPph as $pphOpt) {
                        $sel = ((string) $pphOpt['percentage'] === (string) $dPph) ? ' selected' : '';
                        echo '<option data-idtax="'.$pphOpt['idtax'].'" value="'.$pphOpt['percentage'].'"'.$sel.'>'.$pphOpt['kriteria2'].'</option>';
                    }
                    ?>
                </select>
            </td>
            <td>
                <select class="form-control select2add ppnn-row" name="ppnn" style="width:100%" onchange="input_ppn()">
                    <option data-idtax="0" value="0"> - </option>
                    <?php
                    $sqlPpn = mysqli_query($conn1, "select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' GROUP BY idtax");
                    foreach ($sqlPpn as $ppnOpt) {
                        $sel = ((string) $ppnOpt['percentage'] === (string) $dPpn) ? ' selected' : '';
                        echo '<option data-idtax="'.$ppnOpt['idtax'].'" value="'.$ppnOpt['percentage'].'"'.$sel.'>'.$ppnOpt['kriteria2'].'</option>';
                    }
                    ?>
                </select>
            </td>
            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
          <tr>
            <td colspan="12" align="center">
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
                <div style="display:none;">
                    <select class="form-control selectpicker" name="pilih_pph" id="pilih_pph" data-dropup-auto="false" data-live-search="true">
                        <option value="-" disabled selected="true">Select Account</option>
                    </select>
                </div>
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
            <div class="col-md-5">

            </div>
            <div class="col-md-4">
                <div class="total-box">
                    <div class="total-box-header"><i class="fa fa-calculator"></i> Total</div>
                    <div class="total-box-body">
                        <div class="total-stat">
                            <span class="total-stat-label">Total Without Tax</span>
                            <input type="text" class="total-stat-value" id="nomrate1" name="nomrate1" placeholder="0.00" readonly>
                            <input type="hidden" name="nomrate_h" id="nomrate_h" value="">
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
                            <input type="text" class="total-stat-value" id="total" name="total" placeholder="0.00" readonly>
                            <input type="hidden" name="total_h" id="total_h" value="">
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
  // COA yang wajib isi Cost Center (support_gen_adm/support_prod/prod/support_sell = 'Y')
  var coaWajibCC = [];
  $.getJSON('get_coa_wajib_cc.php', function(data){
      coaWajibCC = data;
  });
</script>

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
      $('.select2').select2({
        theme: 'bootstrap4',
        dropdownAutoWidth : true
      })
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

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<script>
    // Baris detail yang sudah ada (dirender server-side, bukan lewat addRow())
    // butuh init select2add manual di sini, lalu hitung ulang total begitu
    // halaman dibuka supaya kartu Total langsung menampilkan angka yang
    // sesuai dengan baris-baris yang sudah tersimpan.
    $(document).ready(function () {
        $('#tbody2 .select2add').select2({
            theme: 'bootstrap4',
            dropdownAutoWidth: true,
            width: '100%'
        });
        recalcAllRows();
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


<!-- <script >
    $('#add').click( function() {      
 var tableID = "tbody2";
 var table = document.getElementById(tableID);
 var rowCount = table.rows.length;
 var row = table.insertRow(rowCount);


 $coa = '';
 var element1 = '<tr> <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><select class="form-control select2" name="nomor_coa" id="nomor_coa" style="width: 250px"> <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select id_coa,concat(id_coa,' ', coa_name) as coa from tbl_coa_detail"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
                </select></td> </td> <td><input  type="text" class="form-control" id="due_date" name="due_date" value="<?php echo date("Y-m-d"); ?>" style="text-align:center; width: 150px;"  autocomplete="off"></td> <td><input  type="text" class="form-control" id="total" name="total" style="text-align:center; width: 180px;"  autocomplete="off"></td> <td><input  type="text" class="form-control" id="discount" name="discount" style="text-align:center; width: 180px;"  autocomplete="off"></td> <td><input  type="text" class="form-control" id="amt" name="amt" style="text-align:center; width: 180px;" onkeypress="javascript:return isNumber(event)" oninput="modal_input_amt(value)" autocomplete="off"></td> <td><input  type="text" class="form-control" id="discount" name="discount" style="text-align:center; width: 300px;"  autocomplete="off"></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td> </tr>';
 row.innerHTML = element1; 
}); 


  </script> -->

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

    // Ganti/isi ulang opsi #tocc sesuai For Payment yang dipilih (bank
    // perusahaan untuk Pemindah Bukuan Bank/Cicilan Pinjaman Bank, atau bank
    // supplier terpilih untuk Lainnya/default) tanpa reload halaman.
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

    // Tampilkan/sembunyikan From Account/To Account/Ke/Dari/For Payment
    // sesuai kombinasi Pay Methods + For Payment - dulu logic ini ada di
    // PHP dan trigger-nya reload seluruh halaman (onchange="this.form.submit()").
    function updateForPaymentFields() {
        var cb = $('#carabayar').val() || '';
        var ref = $('#forpay').val() || '';

        var isCash = (cb === 'CASH');
        var isPinjamanBank = (ref === 'Cicilan Pinjaman Bank');
        var isAktivaTetap = (ref === 'Cicilan Aktiva Tetap');
        var isLainnya = (ref === 'Lainnya');

        // Dulu (kode PHP lama) From Account/To Account tetap tampil untuk
        // SEMUA For Payment selama Pay Methods bukan CASH - termasuk Cicilan
        // Pinjaman Bank & Cicilan Aktiva Tetap (jadi Ke/Dari itu TAMBAHAN,
        // bukan pengganti Account). Sebelumnya field ini malah disembunyikan
        // untuk 2 For Payment itu, jadi hilang padahal sebelumnya ada.
        var showFrom = !isCash;
        var showTo = !isCash;
        var showKeDari = (isPinjamanBank || isAktivaTetap);
        var showPayFor = isLainnya;

        $('#div_frcc').toggle(showFrom);
        $('#div_tocc').toggle(showTo);
        $('#div_ke, #div_dari').toggle(showKeDari);
        $('#div_payfor').toggle(showPayFor);

        if (!showFrom) { $('#frcc').val('-').trigger('change'); }
        if (!showTo) { $('#tocc').val('-').trigger('change'); }
        if (!showKeDari) { $('#ke').val(''); $('#dari').val(''); }
        if (!showPayFor) { $('#pay_for').val(''); }

        if (showTo) {
            refreshToAccount();
        }
    }

    $(document).on('change', '#carabayar, #forpay', function () {
        updateForPaymentFields();
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
        updateForPaymentFields();
    });
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
    // Header (Supplier/Pay Methods/For Payment/dst) dulu WAJIB diisi dulu
    // sebelum bisa Add Row. Sekarang baris detail bisa ditambah kapan saja -
    // validasi header dipindah ke saat Save saja (lihat handler #simpan).
    var tableID = "tbody2";
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);

 var element1 = '<tr ><td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td><td><select class="form-control selectpicker no_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="100%" data-size="5"> <option value="-" > - </option><?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?> <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-live-search="true" data-width="100%" data-size="5"><option value="-"> - </option><?php $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'"); foreach ($sql3 as $fc) : ?> <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option> <?php endforeach; ?> </select> </td> <td> <select class="form-control selectpicker nomor_cc" name="nomor_cc[]" id="nomor_cc" data-live-search="true" data-width="100%" data-size="5"> <option value="-"> - </option> </select> </td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input type="text" style="font-size: 12px;" name="tgl_active" id="tgl_active" class="form-control tanggal" value="" autocomplete="off" placeholder="dd-mm-yyyy"></td><td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off"></td><td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off"></td><td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_dedadd(value)" autocomplete = "off"></td><td><input type="text" style="font-size: 12px;" name="tgl_tempo" id="tgl_tempo" class="form-control tanggal" autocomplete="off" placeholder="dd-mm-yyyy" value="<?= date("d-m-Y"); ?>"></td><td><select class="form-control select2add" name="pphh" style="width:100%" onchange="input_pph()"> <option data-idtax="0" value="0"> - </option><?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH' GROUP BY idtax"); foreach ($sql as $pph) : ?> <option data-idtax="<?= $pph["idtax"]; ?>" value="<?= $pph["percentage"]; ?>"><?= $pph["kriteria2"]; ?> </option><?php endforeach; ?></select></td><td><select class="form-control select2add ppnn-row" name="ppnn" style="width:100%" onchange="input_ppn()"> <option data-idtax="0" value="0"> - </option><?php $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' GROUP BY idtax"); foreach ($sql as $ppn) : ?> <option data-idtax="<?= $ppn["idtax"]; ?>" value="<?= $ppn["percentage"]; ?>"><?= $ppn["kriteria2"]; ?> </option><?php endforeach; ?></select></td><td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td></tr>';

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

    // Kalau PPN header sudah dikunci ke nilai tertentu (bukan Non PPN), baris
    // baru ini juga langsung ikut nilai itu & ikut terkunci - konsisten
    // dengan baris-baris lain yang sudah ada (lihat changeValueTax2()).
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
                var chkbox = row.cells[12].childNodes[0];
                if (null != chkbox && true == chkbox.checked)
                    {
                    if (rowCount <= 1)
                        {
                        alert("Tidak dapat menghapus semua baris.");
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
    alert(e);
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
                var chkbox = row.cells[11].childNodes[0];
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
                                            newCell.children[i2].checked[9] = true;
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
    alert(e);
    }
 }

 function hitungRow(){

}
</script>

<script type="text/javascript">
    // Satu-satunya sumber perhitungan Total - dipanggil dari SEMUA trigger
    // (input Amount, input Deduction, ganti PPH per baris, ganti PPN per
    // baris, ganti PPN header, delete row) supaya hasilnya selalu konsisten.
    // Sebelumnya tiap trigger punya hitungan sendiri-sendiri yang saling
    // tumpang tindih/tidak sinkron - baris Deduction pun tidak pernah ikut
    // dihitung PPN/PPH-nya sama sekali.
    function recalcAllRows(){
        var table = document.getElementById("tbody2");
        var subtotal = 0;
        var dedTotal = 0;
        var pphTotal = 0;
        var ppnTotal = 0;

        for (var i = 1; i < table.rows.length; i++) {
            var amountEl = table.rows[i].cells[7].children[0];
            var dedEl    = table.rows[i].cells[8].children[0];
            var pphEl    = table.rows[i].cells[10].children[0];
            var ppnEl    = table.rows[i].cells[11].children[0];

            var amountVal = amountEl.value;
            var dedVal    = dedEl.value;
            var pphPct    = parseFloat(pphEl.value) || 0;
            var ppnPct    = parseFloat(ppnEl.value) || 0;

            var base;
            if (amountVal === '') {
                // Baris Deduction - base negatif supaya PPH/PPN baris ini
                // ikut mengurangi total, sama seperti Deduction-nya sendiri.
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
    for (var i = 1; i < (table.rows.length); i++) {

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

    // PPN header dipilih (bukan Non PPN) -> semua PPN per baris ikut nilai
    // ini & dikunci (tidak bisa diubah manual per baris). Balik ke Non PPN
    // di header -> PPN per baris bisa dipilih bebas lagi. Total dihitung
    // ulang lewat recalcAllRows() supaya baris Deduction juga tetap ikut
    // terhitung PPN/PPH-nya, bukan cuma dianggap dari subtotal Amount saja.
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
        // Sama seperti create-paymentvoucher-exim.php: satu request berisi
        // semua item yang dicentang ke save_sup_doc.php, yang membersihkan
        // dulu baris milik sesi ini lalu insert ulang sesuai checklist saat
        // ini - jadi tidak numpuk seperti insertdoc.php (lama), dan tidak
        // perlu reload halaman karena hasilnya (sup_doc) langsung dikembalikan.
        var $btn = $(this);
        if ($btn.data('saving')) {
            return;
        }
        $btn.data('saving', true).prop('disabled', true);

        var doc_number = document.getElementById('no_doc').value;
        var unik_code = document.getElementById('unik_code').value;
        var items = [];

        $("#doc_support input[type=checkbox]:checked").each(function () {
            var data = $(this).closest('tr').find('td:eq(1) input').val();
            if (data) { items.push(data); }
        });

        $.ajax({
            type: 'POST',
            url: 'save_sup_doc.php',
            dataType: 'json',
            data: { 'doc_number': doc_number, 'unik_code': unik_code, 'items': JSON.stringify(items) },
            cache: 'false',
            success: function (response) {
                $btn.data('saving', false).prop('disabled', false);
                if (response && response.success) {
                    $('#sup_doc').val(response.sup_doc);
                    $('#mymodal2').modal('hide');
                } else {
                    Swal.fire('Error', (response && response.error) || 'Gagal menyimpan Supporting Document.', 'error');
                }
            },
            error: function (xhr) {
                $btn.data('saving', false).prop('disabled', false);
                console.log(xhr);
                Swal.fire('Error', xhr.responseText || String(xhr), 'error');
            }
        });
    });


</script>

<!-- <script type="text/javascript">
    $("#form-data").on("click", "#btn2", function(){
        $("input[type=checkbox]:checked").each(function () {
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
                alert(xhr);
            }
        });             
        });
 
    });


</script> -->

<script type="text/javascript">
    $("#form-simpan").on("click", "#simpan", function(){
        // Cegah klik dobel - tombol langsung dinonaktifkan begitu diklik,
        // cuma diaktifkan lagi kalau validasi gagal atau ada error dari
        // server (biar bisa dicoba lagi tanpa reload).
        if ($(this).prop('disabled')) {
            return;
        }
        var $saveBtn = $(this);

        var valid_detail = true;

$("input[type=checkbox]:checked").each(function () {

    // SKIP ROW TEMPLATE / HIDDEN
    if ($(this).closest('tr').is(':hidden')) {
        return true;
    }

    var prof_ctr = $(this).closest('tr')
        .find('td:eq(2)')
        .find('select[id=prof_ctr] option:selected')
        .val();

    var no_coa = $(this).closest('tr')
        .find('td:eq(1)')
        .find('select[id=nomor_coa] option:selected')
        .val();

    var cost_ctr = $(this).closest('tr')
        .find('td:eq(3)')
        .find('select[id=nomor_cc] option:selected')
        .val();

    if (no_coa === '' || no_coa === '-') {
        Swal.fire('Warning', 'Please select COA', 'warning');
        $(this).closest('tr').find('td:eq(1) select[id=nomor_coa]').focus();
        valid_detail = false;
        return false;
    }

    if (prof_ctr === '' || prof_ctr === '-') {
        Swal.fire('Warning', 'Please select Profit Center', 'warning');
        $(this).closest('tr').find('td:eq(2) select[id=prof_ctr]').focus();
        valid_detail = false;
        return false;
    }

    if (coaWajibCC.includes(no_coa) && (cost_ctr === '' || cost_ctr === '-' || cost_ctr == null)) {
        Swal.fire('Warning', 'COA ' + no_coa + ' wajib isi Cost Center', 'warning');
        $(this).closest('tr').find('td:eq(3) select[id=nomor_cc]').focus();
        valid_detail = false;
        return false;
    }
});

if (!valid_detail) {
    return false;
}

        var no_pv = document.getElementById('no_doc').value;  
        var rat_pv = document.getElementById('rat_pv').value;        
        var pv_date = document.getElementById('tgl_active').value;
        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();       
        var sup_doc = document.getElementById('sup_doc').value;        
        var ctb = $('select[name=ct_buyer] option').filter(':selected').val();    
        var pay_date = document.getElementById('tgl_pay').value;
        var pay_mth = $('select[name=carabayar] option').filter(':selected').val(); 
        var curr = document.getElementById('curre').value; 
        var for_pay = $('select[name=forpay] option').filter(':selected').val();
        if (for_pay == 'Lainnya') {
         var forpay = document.getElementById('pay_for').value;
        }else{
         var forpay = $('select[name=forpay] option').filter(':selected').val();   
        }
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
        var pv_tax_type = $('select[name=pv_tax_type] option').filter(':selected').val();
        var create_user = '<?php echo $user; ?>';

        // Dulu ada 2 pengecekan terpisah: satu gerbang sederhana yang
        // MENENTUKAN apakah insertpv_h.php benar-benar dipanggil, dan satu
        // lagi validasi detail terpisah yang cuma menentukan apakah alert
        // "data saved successfully" muncul - keduanya TIDAK saling terhubung.
        // Akibatnya alert sukses bisa muncul padahal gerbang pertama gagal
        // dan insertpv_h.php tidak pernah dipanggil sama sekali, sehingga
        // no_pv tidak pernah benar-benar tersimpan/bertambah. Sekarang cuma
        // ada SATU validasi, dijalankan SEBELUM ajax manapun dikirim.
        if ($('select[name=nama_supp] option').filter(':selected').val() == '' || $('select[name=nama_supp] option').filter(':selected').val() == '-') {
            Swal.fire('Warning', 'Please select Supplier', 'warning');
            document.getElementById('nama_supp').focus();
            return;
        }
        if (sup_doc == '') {
            Swal.fire('Warning', 'Please Select Support Document', 'warning');
            document.getElementById('sup_doc').focus();
            return;
        }
        if (ctb == '' || ctb == null) {
            Swal.fire('Warning', 'Please select Charge to Buyer', 'warning');
            document.getElementById('ct_buyer').focus();
            return;
        }
        if (pay_mth == '' || pay_mth == '-') {
            Swal.fire('Warning', 'Please select payment method', 'warning');
            document.getElementById('carabayar').focus();
            return;
        }
        if (pv_tax_type == '' || pv_tax_type == null) {
            Swal.fire('Warning', 'Please select Payment Voucher Type', 'warning');
            document.getElementById('pv_tax_type').focus();
            return;
        }
        if (curr == '') {
            Swal.fire('Warning', 'Please Enter Currency', 'warning');
            document.getElementById('curre').focus();
            return;
        }
        if (for_pay == '' || for_pay == '-') {
            Swal.fire('Warning', 'Please select For payment', 'warning');
            document.getElementById('forpay').focus();
            return;
        }
        if (for_pay == 'Lainnya' && document.getElementById('pay_for').value == '') {
            Swal.fire('Warning', 'Please Input For payment', 'warning');
            document.getElementById('pay_for').focus();
            return;
        }
        if (pay_mth != 'CASH' && frcc == '-') {
            Swal.fire('Warning', 'Please select From Account', 'warning');
            document.getElementById('frcc').focus();
            return;
        }
        if (pay_mth != 'CASH' && for_pay == 'Pemindah Bukuan Bank' && (tocc == '-' || tocc == '' || tocc == null)) {
            Swal.fire('Warning', 'Please select/isi To Account', 'warning');
            (($('#tocc_manual').is(':visible')) ? document.getElementById('tocc_manual') : document.getElementById('tocc')).focus();
            return;
        }
        if (total == '' || total == null) {
            Swal.fire('Warning', 'Please Input Amount', 'warning');
            return;
        }
        if (parseFloat(total) <= 0) {
            Swal.fire('Warning', "Amount can't be Minus or Zero", 'warning');
            return;
        }

        // Kumpulkan semua baris detail yang dicentang jadi satu array -
        // dikirim SEKALIGUS bareng header dalam satu request, supaya
        // server bisa insert semuanya (header + detail) dalam SATU
        // transaksi (all-or-nothing), bukan satu request AJAX per baris
        // seperti sebelumnya (kalau salah satu gagal di tengah, baris lain
        // yang sudah kadung tersimpan tidak ke-rollback).
        var details = [];
        $("input[type=checkbox]:checked").each(function () {
            var prof_ctr = $(this).closest('tr').find('td:eq(2)').find('select[id=prof_ctr] option').filter(':selected').val();
            var no_coa = $(this).closest('tr').find('td:eq(1)').find('select[id=nomor_coa] option').filter(':selected').val();
            var no_cc = $(this).closest('tr').find('td:eq(3)').find('select[id=nomor_cc] option').filter(':selected').val();
            var no_ref = $(this).closest('tr').find('td:eq(4) input').val();
            var ref_date = $(this).closest('tr').find('td:eq(5) input').val();
            var deskripsi = $(this).closest('tr').find('td:eq(6) input').val();
            var amount = $(this).closest('tr').find('td:eq(7) input').val() || 0;
            var due_date = $(this).closest('tr').find('td:eq(9) input').val();
            var ded_add = $(this).closest('tr').find('td:eq(8) input').val() || 0;
            var d_pph = $(this).closest('tr').find('td:eq(10)').find('select[name=pphh] option').filter(':selected').val() || 0;
            var idtax = $(this).closest('tr').find('td:eq(10)').find('select[name=pphh] option').filter(':selected').attr('data-idtax');
            var ppn_val = $(this).closest('tr').find('td:eq(11)').find('select[name=ppnn] option').filter(':selected').val() || document.getElementById('pilih_ppn').value;
            var d_ppn = (ppn_val === '0' || ppn_val === '' || ppn_val === null) ? document.getElementById('pilih_ppn').value : ppn_val;
            var idppn_val = $(this).closest('tr').find('td:eq(11)').find('select[name=ppnn] option').filter(':selected').attr('data-idtax') || document.getElementById('idtax').value;
            var id_ppn = (ppn_val === '0' || ppn_val === '' || ppn_val === null) ? document.getElementById('idtax').value : idppn_val;

            details.push({
                prof_ctr: prof_ctr, no_coa: no_coa, no_cc: no_cc, no_ref: no_ref,
                ref_date: ref_date, deskripsi: deskripsi, amount: amount,
                due_date: due_date, ded_add: ded_add, pph: d_pph, idtax: idtax,
                ppn: d_ppn, id_ppn: id_ppn
            });
        });

        $saveBtn.prop('disabled', true);

        $.ajax({
            type:'POST',
            url:'update_pv_h.php',
            data: {'no_pv':no_pv, 'rat_pv':rat_pv, 'pv_date':pv_date, 'nama_supp':nama_supp, 'sup_doc':sup_doc, 'ctb':ctb, 'pay_date':pay_date, 'pay_mth':pay_mth, 'curr':curr, 'forpay':forpay, 'pv_tax_type':pv_tax_type, 'frcc':frcc, 'tocc':tocc, 'no_cek':no_cek, 'cek_date':cek_date, 'ke':ke, 'dari':dari, 'pesan':pesan, 'subtotal':subtotal, 'adjust':adjust, 'pph':pph, 'ppn':ppn, 'total':total, 'pilih_ppn':pilih_ppn, 'pilih_pph':pilih_pph, 'create_user':create_user, 'details': JSON.stringify(details)},
            cache: 'false',
            success: function(response){
                console.log(response);

                // update_pv_h.php meng-update PV yang no_pv-nya SAMA (tidak
                // generate nomor baru seperti insertpv_h.php di form Create),
                // jadi response di sini normalnya = no_pv yang sama.
                var savedNoPv = (response || '').trim();
                if (savedNoPv === '' || savedNoPv.indexOf('Error') === 0) {
                    $saveBtn.prop('disabled', false);
                    Swal.fire('Error', 'Gagal menyimpan Payment Voucher: ' + savedNoPv, 'error');
                    return;
                }

                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Payment Voucher ' + savedNoPv + ' berhasil disimpan.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function () {
                    window.location = 'payment-voucher.php';
                });
                },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                $saveBtn.prop('disabled', false);
                Swal.fire('Error', 'Gagal menyimpan Payment Voucher: ' + (xhr.responseText || thrownError), 'error');
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
        $("input[type=checkbox]:checked").each(function () {
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
                alert(xhr);
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
