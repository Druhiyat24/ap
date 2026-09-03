<?php
include '../header.php';
// ============================================================================
// edit_pv_regular_new.php — EDIT PV Regular dgn tampilan/alur PERSIS create
// (payment_voucher_ap/pv_regular.php), tapi PREFILL dari PV lama & SIMPAN sebagai
// REVISI (-REV_NN) lewat insertkbon_bulk_edit.php. File create SENGAJA tak diubah.
// Dibuka: edit_pv_regular_new.php?no_kbon=<urlencoded no_kbon lama>
// ============================================================================
$no_kbon_edit = isset($_GET['no_kbon']) ? urldecode($_GET['no_kbon']) : '';
$esc_edit     = mysqli_real_escape_string($conn2, $no_kbon_edit);
$H = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT * FROM kontrabon_h WHERE no_kbon = '$esc_edit' ORDER BY id DESC LIMIT 1")) ?: [];

// Nilai prefill header dari PV lama.
$EDIT_SUPP    = $H['nama_supp'] ?? '';
$pf_tgl_kbon  = !empty($H['tgl_kbon'])  ? date('Y-m-d', strtotime($H['tgl_kbon']))  : date('Y-m-d');
$pf_tgl_kbon2 = !empty($H['tgl_kbon2']) ? date('Y-m-d', strtotime($H['tgl_kbon2'])) : $pf_tgl_kbon;
$pf_tgl_inv   = !empty($H['tgl_inv'])   ? date('Y-m-d', strtotime($H['tgl_inv']))   : date('Y-m-d');
$pf_tgl_tempo = !empty($H['tgl_tempo']) ? date('Y-m-d', strtotime($H['tgl_tempo'])) : date('Y-m-d');
$pf_curr      = $H['curr'] ?? 'IDR';
$pf_supp_inv  = $H['supp_inv'] ?? '';
$pf_no_faktur = $H['no_faktur'] ?? '';
$pf_pc        = $H['profit_center'] ?? '';
$pf_ir        = $H['ir_number'] ?? '';
$pf_id_bank   = $H['id_bank_account'] ?? '';
$pf_from_acc  = $H['from_account'] ?? '';
$pf_from_bank = $H['from_bank'] ?? '';
$pf_from_curr = $H['from_bank_curr'] ?? '';

// Potongan header PV lama (labarugi, selisih qty/harga, materai, dst).
$POT = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT * FROM potongan WHERE no_kbon = '$esc_edit' ORDER BY id DESC LIMIT 1")) ?: [];
$pfp = function ($k) use ($POT) { $v = $POT[$k] ?? 0; return ((float)$v == 0) ? '' : $v; };

// Rekening bank milik supplier (dropdown Supplier Bank Account).
$__banks = [];
if ($EDIT_SUPP !== '') {
    $qb = mysqli_query($conn2,
        "SELECT msb.id, msb.bank_account, msb.bank_currency, msb.bank_name, msb.beneficiary_name
         FROM master_supplier_bank msb
         INNER JOIN mastersupplier ms ON ms.id_supplier = msb.id_supplier
         WHERE ms.Supplier = '" . mysqli_real_escape_string($conn2, $EDIT_SUPP) . "' AND msb.status = 'Active'
         ORDER BY msb.bank_name ASC");
    while ($rb = mysqli_fetch_assoc($qb)) { $__banks[] = $rb; }
}
$__sel_bankname = ''; $__sel_benef = ''; $__sel_bankcurr = '';
foreach ($__banks as $b) {
    if ((string) $b['id'] === (string) $pf_id_bank) {
        $__sel_bankname = $b['bank_name']; $__sel_benef = $b['beneficiary_name']; $__sel_bankcurr = $b['bank_currency'];
    }
}
?>
<!-- JS libs (header.php TIDAK memuat lib apa pun) — dimuat DULU spy inline script body jalan -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<style type="text/css">
    label {
        font-size: 13px;;
    }

    input {
        font-size: 13px;;
    }
    [data-toggle="collapse"] .fa-chevron-up { transition: transform 0.2s ease; }
    [data-toggle="collapse"].collapsed .fa-chevron-up { transform: rotate(180deg); }
</style>

<div class="container-fluid mt-2 p-3" style="padding-left: 2rem; padding-right: 2rem;">
  <div class="card mb-3" style="border: none; background: transparent;">
  <form id="form-data" method="post">
    <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #191970, #1e90ff); border: none; font-weight: 700; letter-spacing: 0.4px; color: #fff; cursor: pointer;" data-toggle="collapse" data-target="#header_card_body" aria-expanded="true" aria-controls="header_card_body">
                    <span><i class="fa fa-pencil mr-2"></i> EDIT PAYMENT VOUCHER (REVISI) &mdash; <?php echo htmlspecialchars($no_kbon_edit, ENT_QUOTES); ?></span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="header_card_body">
                <div class="card-body p-2">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">            
                            <label for="nokontrabon"><b>No Payment Voucher</b></label>
                            <?php
                            $sql = mysqli_query($conn2,"select CONCAT(
                                'PV-AP/REG/',
                                DATE_FORMAT(CURRENT_DATE(), '%Y'), '/',
                                DATE_FORMAT(CURRENT_DATE(), '%m'), '/',
                                LPAD(
                                COALESCE(MAX(CAST(RIGHT(no_kbon, 5) AS UNSIGNED)), 0) + 1,
                                5, '0'
                                )
                            ) nomor from kontrabon_h WHERE YEAR(tgl_kbon) = YEAR (CURRENT_DATE())");
                            $row = mysqli_fetch_array($sql);
                            $kodeBarang = $no_kbon_edit; // EDIT: nomor PV = nomor lama (readonly, tak berubah)

                            echo'<input type="text" readonly style="font-size: 13px;;" class="form-control form-control-sm" id="nokontrabon" name="nokontrabon" value="'.htmlspecialchars($kodeBarang, ENT_QUOTES).'">';
                            echo '<input type="hidden" id="edit_no_kbon" name="edit_no_kbon" value="'.htmlspecialchars($no_kbon_edit, ENT_QUOTES).'">';
                            ?>

                            <input type="hidden" style="font-size: 13px;;" name="unik_code" id="unik_code" class="form-control form-control-sm" 
                            value="<?php 
                            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
                            $shuffle  = substr(str_shuffle($karakter), 0, 16);
                            echo $shuffle; ?>" autocomplete='off' readonly>
                        </div>

                        <div class="col-md-2 mb-3">            
                            <label for="tanggal"><b>Payment Voucher Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;;" name="tanggal" id="tanggal" class="form-control form-control-sm tanggal" onchange="ubahtanggal(this.value)"
                            value="<?php             
                            $start_date ='';
                            $end_date ='';
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                $tanggal = date("Y-m-d",strtotime($_POST['tanggal_kbn']));
                                $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                                $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                            }

                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                            $sql = mysqli_query($conn2,"select distinct max(tgl_bpb) from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and status != 'Cancel' and tgl_bpb between '$start_date' and '$end_date' ");
                            $row = mysqli_fetch_array($sql);
                            $tgl = $row['max(tgl_bpb)'];         

                            echo $pf_tgl_kbon; // EDIT: prefill tanggal PV lama
                        ?>">
                        <input type="hidden" style="font-size: 13px;;" name="tgl_perhitungan" id="tgl_perhitungan" class="form-control form-control-sm">
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm" name="txt_top" id="txt_top" 
                        value="<?php
                        $start_date ='';
                        $end_date ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $startdate = date("Y-m-d",strtotime($_POST['tanggal_kbn']));
                            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                        }

                        $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                        $sql = mysqli_query($conn2,"select distinct max(tgl_bpb), top from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and tgl_bpb between '$start_date' and '$end_date' ");
                        $row = mysqli_fetch_array($sql);
                        $tgl = $row['max(tgl_bpb)'];
                        $top = isset($row['top']) ? $row['top'] : 0;            


                        if(!empty($nama_supp)) {
                            echo $top;
                        }
                        else{
                            echo $top;
                        } 

                        ?>">
                        <input type="hidden" style="font-size: 13px;;" name="tanggal3" id="tanggal3" class="form-control form-control-sm"
                        value="<?php             
                        $start_date ='';
                        $end_date ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                        }

                        $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                        $sql = mysqli_query($conn2,"select distinct max(tgl_bpb) from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and status != 'Cancel' and tgl_bpb between '$start_date' and '$end_date' ");
                        $row = mysqli_fetch_array($sql);
                        $tgl = $row['max(tgl_bpb)'];         


                        if(!empty($nama_supp)) {

                            echo date("Y-m-d",strtotime($tgl));
                        }
                        else{
                            echo date("Y-m-d");
                        }  ?>">

                        <input type="hidden" style="font-size: 13px;;" name="tanggal4" id="tanggal4" class="form-control form-control-sm"
                        value="<?php             
                        $start_date ='';
                        $end_date ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $tkbon = date("Y-m-d",strtotime($_POST['tanggal_kbn']));
                        }     

                        if(!empty($tkbon)) {

                            echo date("Y-m-d",strtotime($tkbon));
                        }
                        else{
                            echo date("Y-m-d");
                        }  ?>">
                    </div>

                    <div class="col-md-3 mb-3">            
                        <label for="profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>            
                        <select class="form-control selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true" onchange="updateNoKontraBon()">
                            <option value="" disabled selected="true">Select Profit Center</option>                                                 
                            <?php
                            $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center']: $pf_pc; // EDIT: prefill PC lama
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

                        <input type="hidden" readonly style="font-size: 13px;;" class="form-control form-control-sm" id="jurnal" name="jurnal" 
                        value="0" placeholder="<?php echo "KONTRA BON" ?>">
                    </div>

                   <!--  <div class="col-md-2 mb-3">            
                        <label for="matauang"><b>Currency</b></label> -->
                        <input type="hidden" readonly class="form-control form-control-sm" id="matauang" name="matauang" value="<?php echo htmlspecialchars($pf_curr, ENT_QUOTES); ?>">
                   <!--  </div>  -->                                        

                    <!-- <div class="col-md-3 mb-3">            
                        <label for="txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>   -->        
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm" id="txt_inv" name="txt_inv" 
                        value="<?php echo htmlspecialchars($pf_supp_inv, ENT_QUOTES); ?>" required>
                    <!-- </div> -->

                    <!-- <div class="col-md-2 mb-3">            
                        <label for="txt_tglsi"><b>Supplier Invoice Date <i style="color: red;">*</i></b></label> -->   
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm tanggal" name="txt_tglsi" id="txt_tglsi" 
                        value="<?php echo $pf_tgl_inv; ?>">
                   <!--  </div> -->

                   <!--  <div class="col-md-3 mb-3">            
                        <label for="no_faktur"><b>No Tax Invoice <i style="color: red;">*</i></b></label>           -->  
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm" id="no_faktur" name="no_faktur" 
                        value="<?php echo htmlspecialchars($pf_no_faktur, ENT_QUOTES); ?>" required>
                    <!-- </div> -->

                    <div class="col-md-2 mb-3">            
                        <label for="txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>   
                        <input type="text" style="font-size: 13px;;" class="form-control form-control-sm tanggal1" name="txt_tgltempo" id="txt_tgltempo" 
                        value="<?php
                        $start_date ='';
                        $end_date ='';
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $startdate = date("Y-m-d",strtotime($_POST['tanggal_kbn']));
                            $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                            $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                        }

                        $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                        $sql = mysqli_query($conn2,"select distinct max(tgl_bpb), top from bpb_new where supplier = '$nama_supp' and is_invoiced != 'Invoiced' and confirm2 != '' and tgl_bpb between '$start_date' and '$end_date' ");
                        $row = mysqli_fetch_array($sql);
                        $tgl = $row['max(tgl_bpb)'];
                        $top = $row['top'];            

            // $top = 30; && $tanggal != '1970-01-01'
            // echo $top;

                        echo $pf_tgl_tempo; // EDIT: prefill due date PV lama
                        ?>">
                    </div>
                    <?php
                    /* EDIT: bank accounts dari supplier PV lama (sudah dimuat di preamble = $__banks) */
                    $supplier_banks = $__banks;
                    ?>

                    <!-- Bank Account selectpicker -->
                    <div class="col-md-3 mb-3">
                        <label for="sel_bank_account"><b>Supplier Bank Account <i style="color: red;">*</i></b></label>
                        <select class="form-control form-control-sm selectpicker"
                                id="sel_bank_account" name="sel_bank_account"
                                data-live-search="true" data-dropup-auto="false" data-size="8">
                            <option value="">-- Select Bank Account --</option>
                            <option value="-"<?= ((string)$pf_id_bank === '-' ) ? ' selected' : '' ?>>- (Tidak ada rekening)</option>
                            <?php foreach ($supplier_banks as $b): ?>
                            <option value="<?= htmlspecialchars($b['id']) ?>"
                                    <?= ((string)$b['id'] === (string)$pf_id_bank) ? 'selected' : '' ?>
                                    data-account="<?= htmlspecialchars($b['bank_account']) ?>"
                                    data-currency="<?= htmlspecialchars($b['bank_currency']) ?>"
                                    data-bankname="<?= htmlspecialchars($b['bank_name']) ?>"
                                    data-beneficiary="<?= htmlspecialchars($b['beneficiary_name']) ?>">
                                <?= htmlspecialchars($b['bank_account']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Bank Name (readonly auto-fill) -->
                    <div class="col-md-2 mb-3">
                        <label for="disp_bankname"><b>Supplier Bank Name</b></label>
                        <input type="text" readonly class="form-control form-control-sm bg-light"
                               id="disp_bankname" name="disp_bankname" placeholder="-" value="<?php echo htmlspecialchars($__sel_bankname, ENT_QUOTES); ?>">
                    </div>

                    <!-- Beneficiary Name (readonly auto-fill) -->
                    <div class="col-md-3 mb-3">
                        <label for="disp_beneficiary"><b>Beneficiary Name</b></label>
                        <input type="text" readonly class="form-control form-control-sm bg-light"
                               id="disp_beneficiary" name="disp_beneficiary" placeholder="-" value="<?php echo htmlspecialchars($__sel_benef, ENT_QUOTES); ?>">
                    </div>

                    <!-- Currency (readonly auto-fill) -->
                    <div class="col-md-2 mb-3">
                        <label for="disp_currency"><b>Supplier Bank Currency</b></label>
                        <input type="text" readonly class="form-control form-control-sm bg-light"
                               id="disp_currency" name="disp_currency" placeholder="-" value="<?php echo htmlspecialchars($__sel_bankcurr, ENT_QUOTES); ?>">
                    </div>

                    <!-- Hidden backward-compat -->
                    <input type="hidden" id="txt_bank_supp" name="txt_bank_supp">
                    <input type="hidden" id="txt_akun_supp" name="txt_akun_supp">
                     <div class="col-md-3 mb-3">            
                        <label for="ir_number"><b>Invoice Received Number <i style="color: red;">*</i></b></label>            
                        <select class="form-control selectpicker" name="ir_number" id="ir_number" data-dropup-auto="false" data-size="5" data-live-search="true">
                            <option value="-"<?php echo ($pf_ir === '-' || $pf_ir === '') ? ' selected="true"' : ''; ?>>-</option>
                            <?php
                            // EDIT: default IR = IR PV lama; supplier = supplier PV lama.
                            $ir_number = isset($_POST['ir_number']) ? $_POST['ir_number']: $pf_ir;
                            $nama_supp = $EDIT_SUPP;
                            // IR PV lama TIDAK muncul di query (sudah dipakai kontrabon_h non-cancel),
                            // jadi inject manual sbg selected supaya tetap terpilih saat edit.
                            $ir_listed = [];
                            if ($pf_ir !== '' && $pf_ir !== '-') {
                                echo '<option value="'.htmlspecialchars($pf_ir, ENT_QUOTES).'" selected="selected">'.htmlspecialchars($pf_ir, ENT_QUOTES).'</option>';
                                $ir_listed[$pf_ir] = true;
                            }
                            // Hanya IR yang BELUM dipakai PV aktif (untuk MENAMBAH pilihan lain).
                            $sql = mysqli_query($conn1,"select doc_number, nama_supp, total_amount from ir_invoice_supp_h
                                where status != 'Cancel' and nama_supp = '".mysqli_real_escape_string($conn1, $nama_supp)."'
                                and doc_number NOT IN (select ir_number from kontrabon_h
                                    where status <> 'Cancel' and ir_number is not null and ir_number <> '' and ir_number <> '-')");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['doc_number'];
                                if (isset($ir_listed[$data])) { continue; }
                                if($row['doc_number'] == $ir_number ){
                                    $isSelected = ' selected="selected"';
                                }else{
                                    $isSelected = '';

                                }
                                echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                            }?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                    <label><b>From Account <i style="color: red;">*</i></b></label>
                    <select class="form-control form-control-sm selectpicker" id="from_account" name="from_account" data-live-search="true">
                        <option value="">Select Account</option>

                        <?php
                        // Akun Bank + akun Kas Kecil digabung jadi satu dropdown (union).
                        // Pembatasan per user: dessy cuma boleh lihat Bank, Yeni_acc cuma
                        // boleh lihat Kas, user lainnya lihat semuanya.
                        $where_akun = '';
                        if (strcasecmp($user, 'dessy') === 0) {
                            $where_akun = "WHERE tipe_akun = 'BANK'";
                        } elseif (strcasecmp($user, 'Yeni_acc') === 0) {
                            $where_akun = "WHERE tipe_akun = 'CASH'";
                        }

                        $sql = mysqli_query($conn1, "
                            select * from (
                                (select bank_account as account, bank_name as bank, curr, RIGHT(bank_account,4) as kode, nama_pc, kode_pc, b_code, 'BANK' as tipe_akun
                                 from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active')
                                UNION
                                (select no_coa as account, concat(no_coa,' ', nama_coa) as bank, 'IDR' as curr, kode_cash as kode,
                                 IF(no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') as nama_pc,
                                 IF(no_coa = '1.01.11','NAK','NAG') as kode_pc, kode_cash as b_code, 'CASH' as tipe_akun
                                 from mastercoa_v2 where no_coa like '%1.01%' and nama_coa like '%kas kecil%')
                            ) akun_gabungan
                            $where_akun
                            ORDER BY tipe_akun ASC, account ASC
                        ");
                        while ($row = mysqli_fetch_assoc($sql)) {
                            // Value yang dikirim ke database tetap cuma nomor akun/COA
                            // ($row['account']) - yang berubah cuma teks yang tampil di
                            // dropdown: akun Kas ditampilkan "no_coa NAMA_COA" (mis.
                            // "1.01.01 KAS KECIL PABRIK"), akun Bank tetap seperti semula.
                            $label = ($row['tipe_akun'] === 'CASH') ? $row['bank'] : $row['account'];
                            $selFrom = ((string)$row['account'] === (string)$pf_from_acc) ? ' selected="selected"' : ''; // EDIT: prefill From Account lama
                            echo "<option value='" . $row['account'] . "'" . $selFrom . " data-bank='" . $row['bank'] . "' data-currency='" . $row['curr'] . "' data-namapc='" . $row['nama_pc'] . "' data-kodepc='" . $row['kode_pc'] . "'  data-kodebank='" . $row['b_code'] . "'>" . $label . " </option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label><b>From Bank Name <i style="color: red;">*</i></b></label>
                    <input type="text" class="form-control form-control-sm bg-light" id="from_bank" name="from_bank" readonly value="<?php echo htmlspecialchars($pf_from_bank, ENT_QUOTES); ?>">
                </div>

                <div class="col-md-2 mb-3">
                    <label><b>From Bank Currency <i style="color: red;">*</i></b></label>
                    <input type="text" class="form-control form-control-sm bg-light" id="from_bank_curr" name="from_bank_curr" readonly value="<?php echo htmlspecialchars($pf_from_curr, ENT_QUOTES); ?>">
                </div>


                    <div class="col-md-6 mb-3">
                        <label for="nama_supp"><b>Supplier</b></label>            
                        <div class="input-group">
                            <input type="text" readonly style="font-size: 13px;" class="form-control" name="txt_supp" id="txt_supp"
                            value="<?php echo htmlspecialchars($EDIT_SUPP, ENT_QUOTES); ?>">


                            <div class="input-group-append col">
                                <input 
                                style="border: 0;
                                line-height: 1;
                                padding: 0 10px;
                                font-size: 1rem;
                                text-align: center;
                                color: #fff;
                                text-shadow: 1px 1px 1px #000;
                                border-radius: 6px;
                                background-color: rgb(95, 158, 160);"
                                type="button"
                                name="mysupp"
                                id="mysupp"
                                value="Select">
                                <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
                            </div>

                        </div>
                    </div>


                </div>
                </div>
            </div>
        </div>
    </form>

    <form id="form-simpan">
        <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #1E3A8A; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #1E3A8A; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#bpb_card_body" aria-expanded="true" aria-controls="bpb_card_body">
                    <span><i class="fa fa-list-alt"></i> Data BPB</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="bpb_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                            <thead>
                                <tr style="background-color: #f1f5f9; color: #1e293b;">
                                    <th style="width:6%;">Cek</th>
                                    <th style="width:15%;">NO BPB</th>
                                    <th style="width:15%;">NO PO</th>                            
                                    <th style="width:12%;">BPB Date</th>                            
                                    <th style="width:15%;">SubTotal</th>
                                    <th style="width:12%;">Tax (PPn)</th>
                                    <th style="width:10%;">Tax (PPh)</th>                            
                                    <th style="width:15%;">Total (BPB)</th>
                                    <th style="width:80px;display: none;">Total CBD/DP</th>
                                    <th style="width:100px;display: none;">TOP</th>
                                    <th style="width:100px;display: none;">Confirm1</th>
                                    <th style="width:100px;display: none;">Confirm2</th>
                                    <th style="width:100px;display: none;">Supplier</th>     
                                    <th style="width:100px;display: none;">Supplier</th>
                                    <th style="width:100px;display: none;">Supplier</th> 
                                    <th style="width:100px;display: none;">Confirm2</th>
                                    <th style="width:100px;display: none;">Supplier</th>     
                                    <th style="width:100px;display: none;">Supplier</th>
                                    <th style="width:100px;display: none;">Supplier</th>                                                       
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
                                $persen = '';            
                                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                    $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                                    $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                                    $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                                }
                                // echo $profit_center;
                                $querys = mysqli_query($conn2,"select distinct (no_bpb) from bpb_new");
                                $rows = mysqli_fetch_array($querys);
                                $no_bpb = isset($rows['no_bpb']) ?  $rows['no_bpb'] : null;


                                $q = mysqli_query($conn1,"select idtax, kriteria, percentage from mtax where category_tax = 'PPH'");
                                while($rs = mysqli_fetch_array($q)){
                                    $persen .= '<option data-idtax="'.$rs['idtax'].'" value="'.$rs['percentage'].'">'.$rs['kriteria'].'</option>';
                                }

                                // ============================================================
                                // EDIT: baris BPB EXISTING milik PV lama (checked by default),
                                // struktur kolom PERSIS create supaya JS ceklis/serialize sama.
                                // data-nofaktur/data-tglfaktur -> auto-isi kolom No/Tgl Faktur.
                                // ============================================================
                                $__existing_bpb = [];
                                $sqlEx = mysqli_query($conn2,"select a.no_bpb, a.no_po, a.tgl_bpb, a.subtotal, a.idtax, IFNULL(d.percentage,0) percentage, IFNULL(d.kriteria,'Non PPH') kriteria, a.tax, a.pph_code, a.pph_value, a.total, a.dp_value, a.tgl_po, a.tgl_tempo, a.curr, a.no_faktur no_faktur_bpb, b.confirm1, b.confirm2, b.upt_tgl_faktur tgl_faktur_bpb, IFNULL(c.mattype,'') mattype, IFNULL(c.matclass,'') matclass, IFNULL(c.n_code_category,'') n_code_category, IFNULL(c.cus_ctg,'') cus_ctg, fj.fp faktur_pajak_j, fj.tfp tgl_faktur_pajak_j
                                    from kontrabon a
                                    LEFT JOIN (select no_bpb, MAX(confirm1) confirm1, MAX(confirm2) confirm2, MAX(upt_tgl_faktur) upt_tgl_faktur from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.no_bpb
                                    LEFT JOIN (select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$esc_edit' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc) c on c.reff_doc = a.no_bpb
                                    LEFT JOIN mtax d on d.idtax = a.idtax
                                    LEFT JOIN (select reff_doc, MAX(faktur_pajak) fp, MAX(tgl_faktur_pajak) tfp from tbl_list_journal where no_journal = '$esc_edit' and type_journal = 'AP - Kontrabon' and faktur_pajak is not null and faktur_pajak <> '' GROUP BY reff_doc) fj on fj.reff_doc = a.no_bpb
                                    where a.no_kbon = '$esc_edit' and a.status <> 'Cancel'");
                                while ($rx = mysqli_fetch_array($sqlEx)) {
                                    $__existing_bpb[$rx['no_bpb']] = true;
                                    // Tgl/No Faktur diambil dari JURNAL PV (tbl_list_journal.tgl_faktur_pajak/faktur_pajak)
                                    // krn bpb_new.upt_tgl_faktur sering kosong; fallback ke upt_/kontrabon bila jurnal kosong.
                                    $__tgl_src_bpb = (!empty($rx['tgl_faktur_pajak_j']) && $rx['tgl_faktur_pajak_j'] != '0000-00-00') ? $rx['tgl_faktur_pajak_j'] : $rx['tgl_faktur_bpb'];
                                    $tgl_fk_bpb = (!empty($__tgl_src_bpb) && $__tgl_src_bpb != '0000-00-00') ? date('Y-m-d', strtotime($__tgl_src_bpb)) : '';
                                    $__no_fk_bpb = !empty($rx['no_faktur_bpb']) ? $rx['no_faktur_bpb'] : ($rx['faktur_pajak_j'] ?? '');
                                    $exDpp = (float)$rx['subtotal'] + (float)$rx['tax'];
                                    if ((int)$rx['pph_code'] == 0 && (float)$rx['percentage'] == 0) {
                                        $comboEx = '<option data-idtax="0" value="0" selected="selected">Non PPH</option>'.$persen;
                                    } else {
                                        $comboEx = '<option data-idtax="'.$rx['pph_code'].'" value="'.$rx['percentage'].'" selected="selected">'.$rx['kriteria'].'</option><option data-idtax="0" value="0">Non PPH</option>'.$persen;
                                    }
                                    echo '<tr data-nofaktur="'.htmlspecialchars($__no_fk_bpb, ENT_QUOTES).'" data-tglfaktur="'.htmlspecialchars($tgl_fk_bpb, ENT_QUOTES).'">
                                    <td style="width:10px;"><input type="checkbox" class="chkA" id="select" name="select[]" value="" checked></td>
                                    <td style="width:50px;" value="'.$rx['no_bpb'].'">'.$rx['no_bpb'].'</td>
                                    <td style="width:50px;" value="'.$rx['no_po'].'">'.$rx['no_po'].'</td>
                                    <td style="width:100px;" dates="'.date("Y-m-d",strtotime($rx['tgl_bpb'])).'" value="'.$rx['tgl_bpb'].'">'.date("d-M-Y",strtotime($rx['tgl_bpb'])).'</td>
                                    <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$rx['subtotal'].'">'.number_format($rx['subtotal'],2).'</td>
                                    <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$rx['tax'].'">'.number_format($rx['tax'],2).'</td>
                                    <td style="width:100px;"><select name="combo_pph" id="combo_pph" disabled>'.$comboEx.'</select></td>
                                    <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$rx['total'].'">'.number_format($rx['total'],2).'</td>
                                    <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$rx['dp_value'].'">'.number_format($rx['dp_value'],2).'</td>
                                    <td style="display: none;" value="'.$rx['confirm1'].'">'.$rx['confirm1'].'</td>
                                    <td style="display: none;" value="'.$rx['confirm2'].'">'.$rx['confirm2'].'</td>
                                    <td style="display: none;" value="'.htmlspecialchars($EDIT_SUPP, ENT_QUOTES).'">'.htmlspecialchars($EDIT_SUPP).'</td>
                                    <td style="display: none;" value="'.$rx['tgl_po'].'">'.date("d-M-Y",strtotime($rx['tgl_po'])).'</td>
                                    <td style="display: none;" value="'.$rx['tgl_tempo'].'">'.$rx['tgl_tempo'].'</td>
                                    <td style="display: none;" value="'.$rx['curr'].'">'.$rx['curr'].'</td>
                                    <td style="display: none;" value="'.$rx['mattype'].'">'.$rx['mattype'].'</td>
                                    <td style="display: none;" value="'.$rx['matclass'].'">'.$rx['matclass'].'</td>
                                    <td style="display: none;" value="'.$rx['n_code_category'].'">'.$rx['n_code_category'].'</td>
                                    <td style="display: none;" value="'.$rx['cus_ctg'].'">'.$rx['cus_ctg'].'</td>
                                    </tr>';
                                }

                                $sql = mysqli_query($conn2,"select a.no_bpb,a.curr, a.pono, a.tgl_bpb, a.tgl_po, SUM(a.qty * a.price) as sub, if(a.qty is null,SUM((a.qty * a.price) * (a.tax / 100)) ,SUM(((a.qty) * a.price) * (a.tax / 100))) as tax, if(a.qty is null,SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))) ,SUM((a.qty * a.price) + (((a.qty) * a.price) * (a.tax / 100)))) as total, a.top, a.confirm1, a.confirm2, a.supplier, a.tgl_po,a.id_item,a.id_supplier,b.mattype,if(b.matclass like '%ACCESORIES%','ACCESORIES',b.matclass) matclass,if(b.n_code_category is null,'-',b.n_code_category) n_code_category from bpb_new a INNER JOIN masteritem b on b.id_item = a.id_item  where a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' and a.is_invoiced != 'Invoiced' and a.confirm2 != '' and status != 'Cancel' and a.profit_center = '$profit_center' group by a.no_bpb");


                                while($row = mysqli_fetch_array($sql)){
                                    $bpb = $row['no_bpb'];
                                    if (isset($__existing_bpb[$bpb])) { continue; } // EDIT: jangan duplikat BPB existing
                                    $id_supplier = $row['id_supplier'];
                                    $pono = isset($row['pono']) ? $row['pono'] : null;

                                    $mattype = $row['mattype'];
                                    $matclass1 = $row['matclass'];
                                    if ($mattype == 'C') {
                                        if ($matclass1 == 'CMT' || $matclass1 == 'PRINTING' || $matclass1 == 'EMBRODEIRY' || $matclass1 == 'WASHING' || $matclass1 == 'PAINTING' || $matclass1 == 'HEATSEAL') {
                                            $matclass = $matclass1;
                                        }else{
                                            $matclass = 'OTHER';
                                        }
                                    }else{
                                        $matclass = $matclass1;
                                    }

                                    if ($id_supplier == '342' || $id_supplier == '20' || $id_supplier == '19' || $id_supplier == '692' || $id_supplier == '17' || $id_supplier == '18') {
                                        $cust_ctg = 'Related';
                                    }else{
                                        $cust_ctg = 'Third';
                                    }

                                    $n_code_category = $row['n_code_category'];

                                    $Aquer = mysqli_query($conn2,"select no_po, sum(amount) as amount from list_payment_dp where no_po = '$pono' and status_int >= '4'");
                                    $b = mysqli_fetch_array($Aquer);
                                    $po_no = isset($b['no_po']) ? $b['no_po'] : null ;
                                    $dp = isset($b['amount']) ? $b['amount'] : 0 ;

                                    $Aquerrff = mysqli_query($conn2,"select DISTINCT list_payment_dp.no_po as no_po, sum(DISTINCT kontrabon_h.dp_value) as amount from list_payment_dp inner join kontrabon_h on kontrabon_h.no_po = list_payment_dp.no_po where kontrabon_h.no_po = '$pono' and list_payment_dp.status_int = '5' and kontrabon_h.`status` != 'Cancel'");
                                    $f = mysqli_fetch_array($Aquerrff);
                                    $po_nno22 = isset($f['no_po']) ? $f['no_po'] : null ;
                                    $cbd22 = isset($f['amount']) ? $f['amount'] : 0 ;

                                    $tot_sisadp = $dp - $cbd22;

                                    $Aquerr = mysqli_query($conn2,"select b.no_po, sum(total) as amount_update from list_payment_cbd a inner join kontrabon_cbd b on b.no_kbon = a.no_kbon where b.no_po = '$pono' and a.status_int >= '4'");
                                    $c = mysqli_fetch_array($Aquerr);
                                    $po_nno = isset($c['no_po']) ? $c['no_po'] : null ;
                                    $cbd = isset($c['amount_update']) ? $c['amount_update'] : 0 ;

                                    $Aquerraa = mysqli_query($conn2,"select DISTINCT list_payment_cbd.no_po as no_po, sum(DISTINCT kontrabon_h.dp_value) as amount from list_payment_cbd inner join kontrabon_cbd a on a.no_kbon = list_payment_cbd.no_kbon  inner join kontrabon_h on kontrabon_h.no_po = a.no_po where kontrabon_h.no_po = '$pono' and list_payment_cbd.status_int = '5' and kontrabon_h.`status` != 'Cancel'");
                                    $d = mysqli_fetch_array($Aquerraa);
                                    $po_nno11 = isset($d['no_po']) ? $d['no_po'] : null ;
                                    $cbd11 = isset($d['amount']) ? $d['amount'] : 0 ;

            // $tot_sisa = $cbd - $cbd11;
                                    $tot_sisa = $cbd - $cbd11;

                                    $querys = mysqli_query($conn2,"select no_bpb, status from kontrabon where no_bpb = '$bpb' and status != 'Cancel'");
                                    $rows = mysqli_fetch_array($querys);
                                    $n_bpb = isset($rows['no_bpb']);
                                    $stat = isset($rows['status']);                            
                                    $sub = $row['sub'];
                                    $tax = $row['tax'];
                                    $total = $row['total'];
                                    $dpnol = 0;
                                    if($bpb == $n_bpb and $stat != 'Cancel'){
                                        echo '';
                                    }elseif($pono == $po_no){
                                        echo '<tr>
                                        <td style="width:10px;"><input type="checkbox" class="chkA"  id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                                        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                                        <td style="width:100px;">                            
                                        <select name="combo_pph" id="combo_pph" disabled>
                                        <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                                        '.$persen.'                                                                                     
                                        </select>                                                        
                                        </td>                           
                                        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$tot_sisadp.'">'.number_format($tot_sisadp,2).'</td>
                                        <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                                        <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                                        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                                        <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>    
                                        <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
                                        <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                                        <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
                                        <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
                                        <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
                                        <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td> 
                                        </tr>';
                                    }elseif($pono == $po_nno){
                                        echo '<tr>
                                        <td style="width:10px;"><input type="checkbox" class="chkA" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                                        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                                        <td style="width:100px;">                            
                                        <select name="combo_pph" id="combo_pph" disabled>
                                        <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                                        '.$persen.'                                                                                     
                                        </select>                                                        
                                        </td>                           
                                        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$tot_sisa.'">'.number_format($tot_sisa,2).'</td>
                                        <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                                        <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                                        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                                        <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td> 
                                        <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
                                        <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>  
                                        <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
                                        <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
                                        <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
                                        <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td>                                                                                                              
                                        </tr>';
                                    } else{
                                        echo '<tr>
                                        <td style="width:10px;"><input type="checkbox" class="chkA" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                                        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                                        <td style="width:100px;">                            
                                        <select name="combo_pph" id="combo_pph" disabled>
                                        <option data-idtax="0" value="0" selected="selected">Non PPH</option>
                                        '.$persen.'                                                                                     
                                        </select>                                                        
                                        </td>                           
                                        <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$total.'">'.number_format($total,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$dpnol.'">'.number_format($dpnol,2).'</td>
                                        <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                                        <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                                        <td style="display: none;" value="'.$row['supplier'].'">'.$row['supplier'].'</td>
                                        <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td> 
                                        <td style="display: none;" value="'.$row['top'].'">'.$row['top'].'</td> 
                                        <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>  
                                        <td style="display: none;" value="'.$mattype.'">'.$mattype.'</td> 
                                        <td style="display: none;" value="'.$matclass.'">'.$matclass.'</td> 
                                        <td style="display: none;" value="'.$n_code_category.'">'.$n_code_category.'</td>              
                                        <td style="display: none;" value="'.$cust_ctg.'">'.$cust_ctg.'</td>                                                                                                              
                                        </tr>';
                                    }            
                                }  

                                ?>
                            </tbody>                    
                        </table>   
                    </div>
                    <div class="form-row col mt-3">
                        <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total BPB</u></b></label>
                        <div class="col-md-2 mb-3">                              
                            <input type="text" class="form-control form-control-sm" name="subtotal" id="subtotal" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                            <input type="hidden" name="subtotal_h" id="subtotal_h" value="">
                            <input type="hidden" name="subtotal_h1" id="subtotal_h1" value="">
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #2563EB; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #2563EB; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#ro_card_body" aria-expanded="true" aria-controls="ro_card_body">
                    <span><i class="fa fa-undo"></i> Data Return (RO)</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="ro_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                            <thead>
                                <tr style="background-color: #f1f5f9; color: #1e293b;">
                                    <th style="width:10px;">Cek</th>
                                    <th style="width:50px;">No RO</th>
                                    <th style="width:50px;">NO BPPB</th>
                                    <th style="width:50px;">BPPB Date</th>                            
                                    <th style="width:50px;">NO BPB</th>                            
                                    <th style="width:100px;">Total Return</th>  
                                    <th style="width:100px;">Pembayaran</th> 
                                    <th style="display: none;">Pembayaran</th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>                                                    
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                // ============================================================
                                // EDIT: baris RO/RETUR EXISTING milik PV lama (checked default),
                                // struktur kolom PERSIS create. data-nofaktur/data-tglfaktur dari
                                // bppb_new.upt_*; mattype/matclass/dst dari COA jurnal GR/IR RO.
                                // ============================================================
                                $__existing_ro = [];
                                $sqlExRo = mysqli_query($conn2,"select a.no_ro, a.no_bpbrtn, b.no_bppb, b.tgl_bppb, b.no_bpb, a.total_ro, b.curr, IFNULL(c.mattype,'') mattype, IFNULL(c.matclass,'') matclass, IFNULL(c.n_code_category,'') n_code_category, IFNULL(c.cus_ctg,'') cus_ctg, b.upt_no_faktur, b.upt_tgl_faktur, fj.fp faktur_pajak_j, fj.tfp tgl_faktur_pajak_j
                                    from return_kb a
                                    LEFT JOIN (select no_bppb, no_ro, tgl_bppb, no_bpb, curr, MAX(upt_no_faktur) upt_no_faktur, MAX(upt_tgl_faktur) upt_tgl_faktur from bppb_new GROUP BY no_bppb) b on b.no_bppb = a.no_bpbrtn
                                    LEFT JOIN (select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$esc_edit' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc) c on c.reff_doc = a.no_bpbrtn
                                    LEFT JOIN (select reff_doc, MAX(faktur_pajak) fp, MAX(tgl_faktur_pajak) tfp from tbl_list_journal where no_journal = '$esc_edit' and type_journal = 'AP - Kontrabon' and faktur_pajak is not null and faktur_pajak <> '' GROUP BY reff_doc) fj on fj.reff_doc = a.no_bpbrtn
                                    where a.no_kbon = '$esc_edit' and a.status <> 'Cancel'");
                                while ($rr = mysqli_fetch_array($sqlExRo)) {
                                    $exBppb = $rr['no_bppb'] !== null ? $rr['no_bppb'] : $rr['no_bpbrtn'];
                                    $__existing_ro[$exBppb] = true;
                                    // Tgl/No Faktur RO diambil dari JURNAL PV (reff_doc = no_bpbrtn); fallback ke bppb_new.upt_*.
                                    $__tgl_src_ro = (!empty($rr['tgl_faktur_pajak_j']) && $rr['tgl_faktur_pajak_j'] != '0000-00-00') ? $rr['tgl_faktur_pajak_j'] : $rr['upt_tgl_faktur'];
                                    $tgl_fk_ro = (!empty($__tgl_src_ro) && $__tgl_src_ro != '0000-00-00') ? date('Y-m-d', strtotime($__tgl_src_ro)) : '';
                                    $__no_fk_ro = !empty($rr['upt_no_faktur']) ? $rr['upt_no_faktur'] : ($rr['faktur_pajak_j'] ?? '');
                                    $exTglBppb = !empty($rr['tgl_bppb']) ? date("d-M-Y",strtotime($rr['tgl_bppb'])) : '';
                                    echo '<tr data-nofaktur="'.htmlspecialchars($__no_fk_ro, ENT_QUOTES).'" data-tglfaktur="'.htmlspecialchars($tgl_fk_ro, ENT_QUOTES).'">
                                    <td style="width:10px;"><input type="checkbox" class="chkB" id="select" name="select[]" value="" checked></td>
                                    <td style="width:50px;" data-ro="'.$rr['no_ro'].'">'.$rr['no_ro'].'</td>
                                    <td style="width:50px;" valuess="'.$exBppb.'">'.$exBppb.'</td>
                                    <td style="width:100px;" valuess="'.$rr['tgl_bppb'].'">'.$exTglBppb.'</td>
                                    <td style="width:50px;" valuess="'.$rr['no_bpb'].'">'.$rr['no_bpb'].'</td>
                                    <td style="width:100px;text-align:right;" data-total-ro="'.round($rr['total_ro'],2).'">'.number_format($rr['total_ro'],2).'</td>
                                    <td style="width:100px;"><input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="txt_amount" name="txt_amount" value="'.round($rr['total_ro'],2).'" disabled></td>
                                    <td style="display: none;" valuess="'.$rr['curr'].'">'.$rr['curr'].'</td>
                                    <td style="display: none;" valuess="'.$rr['mattype'].'">'.$rr['mattype'].'</td>
                                    <td style="display: none;" valuess="'.$rr['matclass'].'">'.$rr['matclass'].'</td>
                                    <td style="display: none;" valuess="'.$rr['n_code_category'].'">'.$rr['n_code_category'].'</td>
                                    <td style="display: none;" valuess="'.$rr['cus_ctg'].'">'.$rr['cus_ctg'].'</td>
                                    </tr>';
                                }

            // $querys = mysqli_query($conn2,"select curr,no_bppb, tgl_bppb, no_ro, no_bpb, sum((qty * price) + ((qty * price) * (tax /100))) as total,round(sum((qty * price) + ((qty * price) * (tax /100))),2) as total2 from bppb_new where supplier = '$nama_supp' and status != 'Cancel' GROUP BY no_bppb");

                                $querys = mysqli_query($conn2,"select * from (select curr,no_bppb, tgl_bppb, no_ro, no_bpb, sum((qty * price) + ((qty * price) * (tax /100))) as total,round(sum((qty * price) + ((qty * price) * (tax /100))),2) as total2 from bppb_new where supplier = '$nama_supp' and status != 'Cancel' and no_kbon is null and status = 'GMF-PCH' GROUP BY no_bppb) a where total2 > 0 order by no_bppb asc");


                                while($row1 = mysqli_fetch_array($querys)){
                                    $ro_no = isset($row1['no_ro']) ? $row1['no_ro'] : null ;
                                    $bpb_rtn = isset($row1['no_bppb']) ? $row1['no_bppb'] : null ;
                                    if (isset($__existing_ro[$bpb_rtn])) { continue; } // EDIT: jangan duplikat RO existing
                                    $tot_ro = isset($row1['total']) ? $row1['total'] : 0;
                                    $tot_ro2 = isset($row1['total2']) ? $row1['total2'] : 0;

                // $Aquer123 = mysqli_query($conn2,"select DISTINCT no_ro, sum(DISTINCT total_ro) as amount from return_kb where no_ro = '$ro_no' and status != 'Cancel'");

                                    $Aquer123 = mysqli_query($conn2,"select DISTINCT no_bpbrtn, sum(DISTINCT total_ro) as amount from return_kb where no_bpbrtn = '$bpb_rtn' and status != 'Cancel'");
                                    $g = mysqli_fetch_array($Aquer123);
                                    $nobpbrtn = isset($g['no_bpbrtn']) ? $g['no_bpbrtn'] : null ;
                                    $amt_ro = isset($g['amount']) ? $g['amount'] : 0 ;
                                    $sisaro = $tot_ro2 - $amt_ro;

                                    $querybppb = mysqli_query($conn2,"select bppbno,bppbno_int,bppbdate,curr,id_supplier,supplier,mattype,n_code_category,matclass,curr,COALESCE(tax,0) tax,username,dateinput,(dpp + (dpp * (COALESCE(tax,0)/100))) total,dpp,(dpp * (COALESCE(tax,0)/100)) ppn from (select bppbno, bppbno_int, bppb.bppbdate, bppb.id_supplier, supplier, mattype, n_code_category, 
                                        if(matclass like '%ACCESORIES%','ACCESORIES',mi.matclass) matclass, bppb.curr,bppb.username, bppb.dateinput, 
                                        SUM(((qty) * price)) as dpp,bpbno_ro
                                        from bppb 
                                        inner join masteritem mi on bppb.id_item = mi.id_item
                                        inner join mastersupplier ms on bppb.id_supplier = ms.id_supplier
                                        where bppbno_int = '$bpb_rtn' group by bppbno) a left join

                                        (select bpbno,pono from bpb where bpbno = (select DISTINCT bpbno_ro from bppb where bppbno_int = '$bpb_rtn') GROUP BY bpbno) b on b.bpbno = a.bpbno_ro
                                        left JOIN
                                        (select pono,tax from po_header GROUP BY pono) c on c.pono = b.pono");


                                    $rowbppb = mysqli_fetch_array($querybppb);

                                    $id_supplier_bppb = isset($rowbppb['id_supplier']) ? $rowbppb['id_supplier'] : null;
            // echo $id_supplier_bppb;
                                    $mattype_bppb = isset($rowbppb['mattype']) ? $rowbppb['mattype'] : null;
                                    $matclass1_bppb = isset($rowbppb['matclass']) ? $rowbppb['matclass'] : null;
                                    if ($mattype_bppb == 'C') {
                                        if ($matclass1_bppb == 'CMT' || $matclass1_bppb == 'PRINTING' || $matclass1_bppb == 'EMBRODEIRY' || $matclass1_bppb == 'WASHING' || $matclass1_bppb == 'PAINTING' || $matclass1_bppb == 'HEATSEAL') {
                                            $matclass_bppb = $matclass1_bppb;
                                        }else{
                                            $matclass_bppb = 'OTHER';
                                        }
                                    }else{
                                        $matclass_bppb = $matclass1_bppb;
                                    }

                                    if ($id_supplier_bppb == '342' || $id_supplier_bppb == '20' || $id_supplier_bppb == '19' || $id_supplier_bppb == '692' || $id_supplier_bppb == '17' || $id_supplier_bppb == '18') {
                                        $cust_ctg_bppb = 'Related';
                                    }else{
                                        $cust_ctg_bppb = 'Third';
                                    }

                                    $n_code_category_bppb = isset($rowbppb['n_code_category']) ? $rowbppb['n_code_category'] : null;


                                    if($sisaro != 0 || $nobpbrtn == null){
                                        echo '<tr>
                                        <td style="width:10px;"><input type="checkbox" class="chkB" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" data-ro="'.$row1['no_ro'].'">'.$row1['no_ro'].'</td>
                                        <td style="width:50px;" valuess="'.$row1['no_bppb'].'">'.$row1['no_bppb'].'</td>
                                        <td style="width:100px;" valuess="'.$row1['tgl_bppb'].'">'.date("d-M-Y",strtotime($row1['tgl_bppb'])).'</td>                            
                                        <td style="width:50px;" valuess="'.$row1['no_bpb'].'">'.$row1['no_bpb'].'</td>                            
                                        <td style="width:100px;text-align:right;" data-total-ro="'.round($sisaro,2).'">'.number_format($sisaro,2).'</td>
                                        <td style="width:100px;">
                                        <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="txt_amount" name="txt_amount" value="'.round($sisaro,2).'" disabled>
                                        </td>
                                        <td style="display: none;" valuess="'.$row1['curr'].'">'.$row1['curr'].'</td>     
                                        <td style="display: none;" valuess="'.$mattype_bppb.'">'.$mattype_bppb.'</td> 
                                        <td style="display: none;" valuess="'.$matclass_bppb.'">'.$matclass_bppb.'</td> 
                                        <td style="display: none;" valuess="'.$n_code_category_bppb.'">'.$n_code_category_bppb.'</td>              
                                        <td style="display: none;" valuess="'.$cust_ctg_bppb.'">'.$cust_ctg_bppb.'</td>                                                                                            
                                        </tr>';
                                    }else{
                                        echo '';
                                    }}
                                    ?>
                                </tbody>                    
                            </table> 
                        </div>
                        <div class="form-row col mt-3">
                            <label for="potongan" class="col-form-label" style="width: 150px;font-size: 13px;;"><b><u>Total Return</u></b></label>
                            <div class="col-md-2 mb-3">                              
                                <input type="text" class="form-control form-control-sm" name="potongan" id="potongan" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                                <input type="hidden" name="potongan_h" id="potongan_h" value="">
                                <input type="hidden" name="h_mattype" id="h_mattype" value="">
                                <input type="hidden" name="h_matclass" id="h_matclass" value="">
                                <input type="hidden" name="h_code_ctg" id="h_code_ctg" value="">
                                <input type="hidden" name="h_cus_ctg" id="h_cus_ctg" value="">
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #60A5FA; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #2779bd; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#ftr_card_body" aria-expanded="true" aria-controls="ftr_card_body">
                    <span><i class="fa fa-file-text-o"></i> Data FTR (CBD/DP)</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="ftr_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
                            <thead>
                                <tr style="background-color: #f1f5f9; color: #1e293b;">
                                    <th style="width:6%;">Cek</th>
                                    <th style="width:18%;">No FTR</th>
                                    <th style="width:16%;">No PO</th>
                                    <th style="width:15%;">Tgl PO</th>
                                    <th style="width:16%;">No PI</th>
                                    <th style="width:15%;">Total</th>
                                    <th style="width:15%;">Pembayaran</th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                    <th style="display: none;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $query_ftr = mysqli_query($conn2,"select a.no_ftr_cbd, a.tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.curr, (a.total - COALESCE(b.total_ftr,0)) total, a.no_kbon, a.tgl_kbon, a.no_payment, a.tgl_payment, a.no_pv, a.no_bankout, a.bankout_date, a.coa from (select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, a.total, no_kbon, tgl_kbon, no_payment, tgl_payment, no_pv, no_bankout, bankout_date, coa from (select no_ftr_cbd, tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.curr,  a.subtotal, a.tax, a.total, b.no_kbon, b.tgl_kbon, c.no_payment , c.tgl_payment from ftr_cbd a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd inner join list_payment_cbd c on c.no_kbon = b.no_kbon where tgl_ftr_cbd >= '2025-06-01' and c.status != 'Cancel' and a.supp = '$nama_supp'
                                    UNION
                                    select no_ftr_dp, tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.curr,  a.dp_value, 0 tax, a.dp_value total, b.no_kbon, b.tgl_kbon, c.no_payment , c.tgl_payment from ftr_dp a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp inner join list_payment_dp c on c.no_kbon = b.no_kbon where tgl_ftr_dp >= '2025-06-01' and c.status != 'Cancel' and a.supp = '$nama_supp') a
                                    INNER JOIN
                                    (select a.no_pv, coa, reff_doc, amount from tbl_pv a INNER JOIN tbl_pv_h b on b.no_pv = a.no_pv where (reff_doc like '%CBD%' OR reff_doc like '%DP%') and b.status != 'Cancel' GROUP BY reff_doc,no_pv) b on b.reff_doc = a.no_payment
                                    INNER JOIN
                                    (select b.no_bankout, b.bankout_date, a.no_reff, a.total from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where a.no_reff like '%PV/%' and b.status != 'Cancel' GROUP BY a.no_reff) c on c.no_reff = b.no_pv
                                    UNION
                                    select no_cbd no_ftr_cbd, NULL tgl_ftr_cbd, h.nama_supp supp, d.no_po, d.tgl_po, NULL no_pi, h.curr, SUM(bd.total) total, h.no_kbon, h.tgl_kbon, NULL no_payment, NULL tgl_payment, h.no_kbon no_pv, bh.no_bankout, bh.bankout_date, h.no_coa coa from kontrabon_h_cbd h INNER JOIN kontrabon_cbd d on d.no_kbon = h.no_kbon INNER JOIN b_bankout_det bd on bd.no_reff = h.no_kbon and bd.type_pv = 'CBD' INNER JOIN b_bankout_h bh on bh.no_bankout = bd.no_bankout where bh.status != 'Cancel' and h.nama_supp = '$nama_supp' GROUP BY d.no_kbon, d.no_cbd
                                    UNION ALL
                                    select no_dp no_ftr_cbd, NULL tgl_ftr_cbd, h.nama_supp supp, d.no_po, d.tgl_po, NULL no_pi, h.curr, SUM(bd.total) total, h.no_kbon, h.tgl_kbon, NULL no_payment, NULL tgl_payment, h.no_kbon no_pv, bh.no_bankout, bh.bankout_date, h.no_coa coa from kontrabon_h_dp h INNER JOIN kontrabon_dp d on d.no_kbon = h.no_kbon INNER JOIN b_bankout_det bd on bd.no_reff = h.no_kbon and bd.type_pv = 'DP' INNER JOIN b_bankout_h bh on bh.no_bankout = bd.no_bankout where bh.status != 'Cancel' and h.nama_supp = '$nama_supp' GROUP BY d.no_kbon, d.no_dp
                                    order by tgl_ftr_cbd asc) a LEFT JOIN (select no_ftr, a.no_po, no_bankout, total_ftr from kontrabon_ftr a INNER JOIN kontrabon_h b on b.no_kbon = a.no_kbon WHERE a.nama_supp = '$nama_supp' and b.status != 'Cancel') b on b.no_ftr = a.no_ftr_cbd and b.no_po = a.no_po and b.no_bankout = a.no_bankout where (a.total - COALESCE(b.total_ftr,0)) > 0");


                                while($row_ftr = mysqli_fetch_array($query_ftr)){
                                    echo '<tr>
                                    <td style="width:10px;"><input type="checkbox" class="chkC" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                    <td style="width:50px;" no-ftr="'.$row_ftr['no_ftr_cbd'].'">'.$row_ftr['no_ftr_cbd'].'</td>
                                    <td style="width:50px;" po-ftr="'.$row_ftr['no_po'].'">'.$row_ftr['no_po'].'</td>
                                    <td style="width:100px;" tglpo-ftr="'.$row_ftr['tgl_po'].'">'.date("d-M-Y",strtotime($row_ftr['tgl_po'])).'</td>                            
                                    <td style="width:50px;" pi-ftr="'.$row_ftr['no_pi'].'">'.$row_ftr['no_pi'].'</td>                            
                                    <td style="width:100px;text-align:right;" total-ftr="'.round($row_ftr['total'],2).'">'.number_format($row_ftr['total'],2).'</td>
                                    <td style="width:100px;">
                                    <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="amount_ftr" name="amount_ftr" value="'.round($row_ftr['total'],2).'" disabled>
                                    </td>
                                    <td style="display: none;" curr-ftr="'.$row_ftr['curr'].'">'.$row_ftr['curr'].'</td>
                                    <td style="display: none;" kbon-ftr="'.$row_ftr['no_kbon'].'">'.$row_ftr['no_kbon'].'</td>
                                    <td style="display: none;" tglkbon-ftr="'.$row_ftr['tgl_kbon'].'">'.$row_ftr['tgl_kbon'].'</td>
                                    <td style="display: none;" lp-ftr="'.$row_ftr['no_payment'].'">'.$row_ftr['no_payment'].'</td>
                                    <td style="display: none;" tgllp-ftr="'.$row_ftr['tgl_payment'].'">'.$row_ftr['tgl_payment'].'</td>
                                    <td style="display: none;" pv-ftr="'.$row_ftr['no_pv'].'">'.$row_ftr['no_pv'].'</td>
                                    <td style="display: none;" bankout-ftr="'.$row_ftr['no_bankout'].'">'.$row_ftr['no_bankout'].'</td>
                                    <td style="display: none;" bankoutdate-ftr="'.$row_ftr['bankout_date'].'">'.$row_ftr['bankout_date'].'</td>
                                    <td style="display: none;" coa-ftr="'.$row_ftr['coa'].'">'.$row_ftr['coa'].'</td>
                                    </td>                                                                                            
                                    </tr>';
                                }
                                ?>
                            </tbody>                    
                        </table>
                    </div>
                    <div class="form-row col mt-3">
                        <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total DP / CBD</u></b></label>
                        <div class="col-md-2 mb-3">                              
                            <input type="text" class="form-control form-control-sm" name="ttl_dp" id="ttl_dp" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                            <input type="hidden" name="ttl_dp_h" id="ttl_dp_h" value="">

                        </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #7c3aed; border-radius: 10px;">
              <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #7c3aed; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#potongan_card_body" aria-expanded="true" aria-controls="potongan_card_body">
                <span><i class="fa fa-calculator"></i> Potongan</span>
                <i class="fa fa-chevron-up"></i>
              </div>
              <div class="collapse show" id="potongan_card_body">
              <div class="card-body p-2">
                <div class="row">
                  <div class="col-md-7 border-end pe-4">

                    <div class="row mb-2 align-items-center">
                      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Laba Rugi Kurs</u></b></label>
                      <div class="col-3">
                        <input type="number" class="form-control form-control-sm text-right" name="labarugi" id="labarugi" placeholder="0.00" value="<?php echo $pfp('lr_kurs'); ?>">
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm text-right" name="labarugi_h" id="labarugi_h" placeholder="0.00" readonly>
                    </div>
                    <div class="col-2"></div>
                </div>

                <div class="row mb-2 align-items-center">
                  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Quantity</u></b></label>
                  <div class="col-3">
                    <input type="number" class="form-control form-control-sm text-right" name="selisihqty" id="selisihqty" placeholder="0.00" value="<?php echo $pfp('s_qty'); ?>">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm text-right" name="selisihqty_h" id="selisihqty_h" placeholder="0.00" readonly>
                </div>
                <div class="col-2"></div>
            </div>

            <div class="row mb-2 align-items-center">
              <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Harga</u></b></label>
              <div class="col-3">
                <input type="number" class="form-control form-control-sm text-right" name="selisihharga" id="selisihharga" placeholder="0.00" value="<?php echo $pfp('s_harga'); ?>">
            </div>
            <div class="col-4">
                <input type="text" class="form-control form-control-sm text-right" name="selisihharga_h" id="selisihharga_h" placeholder="0.00" readonly>
            </div>
            <div class="col-2"></div>
        </div>

        <div class="row mb-2 align-items-center">
          <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Materai</u></b></label>
          <div class="col-3">
            <input type="number" min="0" class="form-control form-control-sm text-right" name="materai" id="materai" placeholder="0.00" value="<?php echo $pfp('materai'); ?>">
        </div>
        <div class="col-4">
            <input type="text" class="form-control form-control-sm text-right" name="materai_h" id="materai_h" placeholder="0.00" readonly>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Potongan Pembelian</u></b></label>
      <div class="col-3">
        <input type="number" max="0" class="form-control form-control-sm text-right" name="potongbeli" id="potongbeli" placeholder="0.00" value="<?php echo $pfp('pot_beli'); ?>">
    </div>
    <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="potongbeli_h" id="potongbeli_h" placeholder="0.00" readonly>
    </div>
    <div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Expedisi</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="ekspedisi" id="ekspedisi" placeholder="0.00" value="<?php echo $pfp('ekspedisi'); ?>">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="ekspedisi_h" id="ekspedisi_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya MOQ</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="moq" id="moq" placeholder="0.00" value="<?php echo $pfp('moq'); ?>">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="moq_h" id="moq_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Koreksi PPN</u></b></label>
  <div class="col-3">
    <input type="number" class="form-control form-control-sm text-right" name="potongan_ppn" id="potongan_ppn" placeholder="0.00" value="<?php echo $pfp('potongan_ppn'); ?>">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongan_ppn_h" id="potongan_ppn_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Koreksi PPh</u></b></label>
  <div class="col-3">
    <input type="number" class="form-control form-control-sm text-right" name="potongan_pph" id="potongan_pph" placeholder="0.00" value="<?php echo $pfp('potongan_pph'); ?>">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongan_pph_h" id="potongan_pph_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Jumlah Potongan</u></b></label>
  <div class="col-7">
    <input type="text" class="form-control form-control-sm text-right" name="jumlahpotong" id="jumlahpotong" placeholder="0.00" readonly>
    <input type="hidden" name="jml_potong" id="jml_potong">
</div>
<div class="col-2"></div>
</div>

<input type="hidden" name="ttl_sub" id="ttl_sub">
<input type="hidden" name="ttl_sub1" id="ttl_sub1">
<input type="hidden" name="ttl_sub2" id="ttl_sub2">
<input type="hidden" name="ttl_sub3" id="ttl_sub3">
<input type="hidden" name="ttl_sub4" id="ttl_sub4">
<input type="hidden" name="ttl_sub5" id="ttl_sub5">
<input type="hidden" name="ttl_sub6" id="ttl_sub6">
<input type="hidden" name="ttl_sub7" id="ttl_sub7">

</div>

<div class="col-md-5 ps-4">

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPn)</u></b></label>
      <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="pajak" id="pajak" placeholder="0.00" readonly>
        <input type="hidden" name="pajak_h" id="pajak_h">
    </div>
    <div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>PPN setelah Potongan</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pajak_setelah_potongan" id="pajak_setelah_potongan" placeholder="0.00" readonly>
    <input type="hidden" name="pajak_setelah_potongan_h" id="pajak_setelah_potongan_h">
</div>
<div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPh)</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pph" id="pph" placeholder="0.00" readonly>
    <input type="hidden" name="pph_h" id="pph_h">
</div>
<div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>PPh setelah Potongan</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pph_setelah_potongan" id="pph_setelah_potongan" placeholder="0.00" readonly>
    <input type="hidden" name="pph_setelah_potongan_h" id="pph_setelah_potongan_h">
</div>
<div class="col-5"></div>
</div>

<!-- <div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Total CBD / DP</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="ttl_dp" id="ttl_dp" placeholder="0.00" readonly>
    <input type="hidden" name="ttl_dp_h" id="ttl_dp_h">
</div>
<div class="col-5"></div>
</div> -->

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Total</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="total" id="total" placeholder="0.00" readonly>
    <input type="hidden" name="total_h" id="total_h">
    <input type="hidden" name="po" id="po">
    <input type="hidden" name="po1" id="po1">
</div>
<div class="col-3">
    <button type="button" class="btn btn-warning btn-sm" id="calculate">
      <span class="fa fa-calculator"></span> Calculate
  </button>
</div>
<div class="col-2"></div>
</div>

<div class="row mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
  <div class="col d-flex ">
    <button type="button" class="btn btn-primary btn-sm me-2 px-4 mr-2" id="simpan">
      <span class="fa fa-floppy-o"></span> Save
  </button>
  <button type="button" class="btn btn-danger btn-sm px-4" onclick="location.href='payment-voucher-ap.php'">
      <span class="fa fa-angle-double-left"></span> Back
  </button>
</div>
</div>

</div>
</div>
</div>
</div>
</div>



</div>
</div>
</form>

<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-lg rounded-3">

      <!-- Header -->
      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data</h4>
    </div>

    <!-- Body -->
    <div class="modal-body">
        <form id="modal-form" method="post">

          <!-- Supplier -->
          <div class="form-group mb-3">
            <label for="nama_supp"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                // EDIT: default supplier = supplier PV lama (fixed) supaya "Select"
                // memuat BPB tambahan dari supplier yang sama.
                $__supp_sel = isset($_POST['nama_supp']) && $_POST['nama_supp'] !== '' ? $_POST['nama_supp'] : $EDIT_SUPP;
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $__supp_sel){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                }?>
            </select>
        </div>

        <input type="hidden" style="font-size: 13px;" name="h_profit_center" id="h_profit_center" class="form-control form-control-sm" value="">
        <input type="hidden" style="font-size: 13px;" name="tanggal_kbn" id="tanggal_kbn" class="form-control form-control-sm" value="">

        <!-- BPB Date -->
        <div class="form-group">
            <label><b>BPB Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control tanggal_fil mr-2" id="start_date" name="start_date" 
              value="<?php
              if(!empty($_POST['start_date'])) {
                echo $_POST['start_date'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control tanggal_fil" id="end_date" name="end_date" 
                value="<?php
                if(!empty($_POST['end_date'])) {
                    echo $_POST['end_date'];
                    } else {
                        echo date("d-m-Y");
                    } ?>" 
                    placeholder="Tanggal Akhir">
                </div>
            </div>

        </form>
    </div>

    <!-- Footer -->
    <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
      </button>
      <button type="submit" form="modal-form" id="send" name="send" class="btn btn-warning">
          <i class="fa fa-search" aria-hidden="true"></i> Search
      </button>
  </div>

</div>
</div>
</div>


<div class="modal fade" id="mymodalbpb" data-target="#mymodalbpb" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_no_po" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_supp2" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_top" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>         
                  <div id="txt_curr" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_confirm" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_confirm2" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>                              
                  <div id="details" class="modal-body col-12" style="font-size: 13px;; padding: 0.5rem;"></div>          
              </div>
          </div>
      </div>
      <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>         

<script>

    // EDIT: nomor PV = nomor lama (readonly, TIDAK berubah). updateNoKontraBon
    // dijadikan no-op & tak ada restore PC dari localStorage / clear tabel (baris
    // existing harus tetap tampil). ubahtanggal tetap dipanggil utk hitung due date,
    // tapi versi edit TIDAK mengubah nomor (lihat script ubahtanggal di bawah).
    document.addEventListener("DOMContentLoaded", function() {
        // Prefill h_profit_center & tanggal_kbn utk modal "Select" (tambah BPB) supaya
        // reload memakai PC & tanggal PV ini.
        var pcEl = document.getElementById("profit_center");
        if (pcEl && document.getElementById('h_profit_center')) {
            document.getElementById('h_profit_center').value = pcEl.value || '';
        }
    });

    function updateNoKontraBon() { /* EDIT: no-op — nomor PV tidak boleh berubah */ }
</script>

<script>

    $(document).ready(function() {
        $("#mysupp").on("click", function() {
            let profit_center = $('select[name=profit_center] option').filter(':selected').val();
            let tanggal = document.getElementById('tanggal').value;

            if(profit_center == ""){
                Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
                $("#profit_center").focus();
                return;
            }

            $('#h_profit_center').val(profit_center);
            $('#tanggal_kbn').val(tanggal);

            $("#mymodal").modal("show");
        });
    });
</script>

<script>
// ===== Auto-fill Bank Info saat Bank Account dipilih =====
$(document).on('change', '#sel_bank_account', function () {
    var opt = $(this).find('option:selected');
    var account     = opt.data('account')     || '';
    var currency    = opt.data('currency')    || '';
    var bankname    = opt.data('bankname')    || '';
    var beneficiary = opt.data('beneficiary') || '';

    $('#disp_currency').val(currency);
    $('#disp_bankname').val(bankname);
    $('#disp_beneficiary').val(beneficiary);

    // update hidden fields (backward compat)
    $('#txt_bank_supp').val(bankname);
    $('#txt_akun_supp').val(account);
});

// ===== Auto-fill From Bank Name/Currency saat From Account dipilih =====
$(document).on('change', '#from_account', function () {
    var opt = $(this).find('option:selected');
    $('#from_bank').val(opt.data('bank') || '');
    $('#from_bank_curr').val(opt.data('currency') || '');
});
</script>

<script>
    $(document).ready(function() {
        $("[data-toggle=tooltip]").tooltip();

    } );

    $(document).ready(function () {
        // Tambah kolom input No Faktur & Tgl Faktur di UJUNG tabel BPB (index baru,
        // tidak menggeser td:eq(...) yang sudah dipakai kode lain). Inject SEBELUM
        // DataTable init supaya jumlah kolom thead & tbody konsisten.
        // EDIT: fungsi global supaya bisa dipanggil ulang saat menambah baris (Cari BPB).
        // Prefill No/Tgl Faktur dari data-nofaktur / data-tglfaktur baris (baris existing);
        // baris baru (tanpa atribut) -> kosong.
        window.pvAppendFakturCells = function ($rows) {
            $rows.each(function () {
                var $r = $(this);
                if ($r.find('td.fk-cell').length > 0) return;
                if ($r.find('td').length < 2) return; // lewati baris "No data"
                var nf = ($r.attr('data-nofaktur') || '').replace(/"/g, '&quot;');
                var tf = ($r.attr('data-tglfaktur') || '').replace(/"/g, '&quot;');
                $r.append(
                    '<td class="fk-cell"><input type="text" class="fk-no form-control form-control-sm" list="fkOptions" value="' + nf + '" placeholder="required" style="min-width:150px;font-size:12px;"></td>' +
                    '<td class="fk-cell"><input type="date" class="fk-tgl form-control form-control-sm" value="' + tf + '" style="min-width:150px;font-size:12px;"></td>'
                );
            });
        };
        (function () {
            // datalist bersama utk saran No Faktur (diisi dari faktur milik IR terpilih).
            if (!document.getElementById('fkOptions')) { $('body').append('<datalist id="fkOptions"></datalist>'); }
            var $head = $('#mytable thead tr');
            if ($head.find('th.fk-head').length === 0) {
                $head.append('<th class="fk-head" style="min-width:150px;">No Faktur</th><th class="fk-head" style="min-width:120px;">Tgl Faktur</th>');
            }
            window.pvAppendFakturCells($('#mytable tbody tr'));
            // Kolom No Faktur/Tgl Faktur juga di tabel RETUR (#mytable1) -> disimpan ke bppb_new.
            var $head1 = $('#mytable1 thead tr');
            if ($head1.find('th.fk-head').length === 0) {
                $head1.append('<th class="fk-head" style="min-width:150px;">No Faktur</th><th class="fk-head" style="min-width:120px;">Tgl Faktur</th>');
            }
            window.pvAppendFakturCells($('#mytable1 tbody tr'));
        })();

        $('#mytable').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $('#mytable1').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $('#mytable2').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });
    });

</script>

<script type="text/javascript">
    $(document).ready(function () {
        var tgl1 = document.getElementById('tanggal3').value;
        $('.tanggal').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal_fil').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        // var tgl = document.getElementById('tanggal').value;
        $('.tanggal1').datepicker({
            format: "yyyy-mm-dd",
            autoclose:true,
        // startDate: new Date(tgl)
    });
    });
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script type="text/javascript">
    function formatDate(date) {
        var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }
</script>

<script type="text/javascript">
    function addDate(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        return formatDate(result);
    }
</script>

<script type="text/javascript"> 
    var tgl = 0;
    var tgl2 = '';
    function ubahtanggal(value){
        // EDIT: JANGAN ambil/ubah nomor PV (nomor = nomor lama, readonly). Hanya update
        // due date bila user mengubah tanggal PV.
        var tanggal = document.getElementById('tanggal').value;
        var txt_top = parseFloat(document.getElementById('txt_top').value,10) || 0;
        var hasil = addDate(tanggal, txt_top);
        $("#tanggal").val(tanggal);
        // Due date dibiarkan (sudah prefill dari PV lama) kecuali user memang mengubah
        // tanggal & ada TOP; kalau TOP 0, biarkan due date apa adanya.
        if (txt_top > 0) { $("#txt_tgltempo").val(hasil); }
};
</script>

<script type="text/javascript">
    // JS's native toFixed() rounds half-DOWN for values like 0.365 (shows "0.36")
    // because 0.365 can't be represented exactly in binary floating point - it's
    // actually stored as ~0.36499999999999999. This nudges the value by a tiny
    // relative epsilon before rounding so it lands on the correct side of the
    // boundary (0.365 -> 0.37), matching how the amount ends up getting saved.
    // Use this everywhere INSTEAD of raw .toFixed() so what the user sees on
    // screen always matches what gets stored in the database.
    function roundHalfUp(num, decimals) {
        var factor = Math.pow(10, decimals || 0);
        var nudged = (Number(num) || 0) * factor * (1 + Number.EPSILON);
        return Math.round(nudged) / factor;
    }

    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
      try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        amount = roundHalfUp(Math.abs(Number(amount) || 0), decimalCount);
        let i = parseInt(amount.toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
};

function updateSetelahPotongan() {
    var pajak_h = parseFloat(document.getElementById('pajak_h').value, 10) || 0;
    var pph_h = parseFloat(document.getElementById('pph_h').value, 10) || 0;
    var potongan_ppn_h = parseFloat(document.getElementById('potongan_ppn').value, 10) || 0;
    var potongan_pph_h = parseFloat(document.getElementById('potongan_pph').value, 10) || 0;

    var pajak_setelah_potongan = pajak_h + potongan_ppn_h;
    var pph_setelah_potongan = pph_h + potongan_pph_h;

    $("#pajak_setelah_potongan").val(formatMoney(pajak_setelah_potongan));
    $("#pajak_setelah_potongan_h").val(pajak_setelah_potongan);
    $("#pph_setelah_potongan").val(formatMoney(pph_setelah_potongan));
    $("#pph_setelah_potongan_h").val(pph_setelah_potongan);
}

$("input[name=potongan_ppn]").keyup(function(){
    var potongan_ppn = parseFloat($(this).val(), 10) || 0;
    $("#potongan_ppn_h").val(formatMoney(potongan_ppn));
    updateSetelahPotongan();
});

$("input[name=potongan_pph]").keyup(function(){
    var potongan_pph = parseFloat($(this).val(), 10) || 0;
    $("#potongan_pph_h").val(formatMoney(potongan_pph));
    updateSetelahPotongan();
});
// $(".chkA").change(function(){
    $("#form-simpan input[id=select]").change(function(){
        var sum_sub = 0;
        var total = 0;
        var amt = '';
        var sisa = 0;
        var nopo= '';
        var nopo1= '';
        var curren= '';
        var tanggals= '';
        var dates= '';
        var m_type= '';
        var m_class= '';
        var n_code= '';
        var c_ctg= '';
        var ppn = 0;
        var cbddp = 0;
        var pph = 0;
        var pot = 0;
        var ppn1 = 0;
        var cbddp1 = 0;
        var pph11 = 0;
        var data = 0;
        var data1 = 0;
        var processedPO = [];
        var allowedPO = [];
        var cbddp_total = 0;
        var total_ftr = 0;
        $(this).closest('tr').find('td:eq(6) input').prop('disabled', true);
        $("#form-simpan .chkC").prop("disabled", false);
    // $(this).closest('tr').find('td:eq(6) input').val(0);   
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);         
    $("#form-simpan input[type=checkbox]:checked").each(function () { 
        var tgl4 = document.getElementById('tanggal4').value; 
        var tanggal = document.getElementById('tgl_perhitungan').value || '1970-01-01'; 
        var tglbpb = $(this).closest('tr').find('td:eq(3)').attr('value');  
        var tglbpb2 = $(this).closest('tr').find('td:eq(3)').attr('dates');
        var po = $(this).closest('tr').find('td:eq(2)').attr('value'); 
        var curr = $(this).closest('tr').find('td:eq(14)').attr('value') || $(this).closest('tr').find('td:eq(7)').attr('valuess') || document.getElementById('matauang').value || 'IDR'; 
        var po1 = document.getElementById('po').value;  
        var tax1 = parseFloat(document.getElementById('pajak_h').value,10) || 0;
        var cbd1 = parseFloat(document.getElementById('ttl_dp_h').value,10) || 0;
        var h_sub = parseFloat(document.getElementById('subtotal_h').value,10) || 0;
        var h_pot = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        var select_amount = $(this).closest('tr').find('td:eq(6) input');
        var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
        var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
        var price_ftr = parseFloat($(this).closest('tr').find('td:eq(5)').attr('total-ftr'),10) || 0;
        var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
        var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
        var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
        var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
        var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]');
        var mattype = $(this).closest('tr').find('td:eq(15)').attr('value'); 
        var matclass = $(this).closest('tr').find('td:eq(16)').attr('value'); 
        var n_code_category = $(this).closest('tr').find('td:eq(17)').attr('value'); 
        var cus_ctg = $(this).closest('tr').find('td:eq(18)').attr('value');
        var h_mattype = document.getElementById('h_mattype').value;
        var h_matclass = document.getElementById('h_matclass').value;
        var h_code_ctg = document.getElementById('h_code_ctg').value;
        var h_cus_ctg = document.getElementById('h_cus_ctg').value; 
        select_pph.prop('disabled', false);  
        select_amount.prop('disabled', false);  
        amt = select_amount.prop('disabled', false);  
    // alert(curr);
    
    sum_sub += price;
    total += price_ro;
    total_ftr += price_ftr;
    sisa = sum_sub - total; 
    nopo= po;
    ppn += tax;
    cbddp= cbd;
    curren= curr;
    pph11 += price * (pph / 100);
    pot = 0;
    // data1 = h_sub - h_pot;
    // // if(data1 > price_ro){
    //     data = price_ro;
    // // }else{
    // //     data = h_sub;
    // // }
    if(h_mattype == ''){
        m_type= mattype;  
    }else{
        m_type= h_mattype;
    }

    if(h_matclass == ''){
        m_class= matclass;  
    }else{
        m_class= h_matclass;
    }

    if(h_code_ctg == ''){
        n_code= n_code_category;  
    }else{
        n_code= h_code_ctg;
    }

    if(h_cus_ctg == ''){
        c_ctg= cus_ctg;  
    }else{
        c_ctg= h_cus_ctg;
    }

    if(po1 == ''){
      nopo1= po;  
  }else{
    nopo1= po1;
}

cbddp1 = cbd;  

if(tax1 == ''){
  ppn1= tax;
}else{
    ppn1= tax1;
}

if(tglbpb > tgl4){
  tanggals = tglbpb;  
}else{
    tanggals = tgl4;
}

if(tglbpb2 > tanggal){
  dates = tglbpb2;  
}else{
    dates = tanggal;
}

if (!processedPO.includes(po)) {
    cbddp_total += cbd;
        processedPO.push(po); // simpan PO ke array
    }

    // allowedPO.push(po);

});

    $("#mytable input[type=checkbox]:checked").each(function () { 
        var po = $(this).closest('tr').find('td:eq(2)').attr('value'); 
        allowedPO.push(po);

    });

    $("#subtotal").val(formatMoney(sum_sub));
    $("#subtotal_h").val(roundHalfUp(sum_sub, 2).toFixed(2));
    $("#subtotal_h1").val(roundHalfUp(data1, 2).toFixed(2));
    $("#potongan").val(formatMoney(total));
    $("#potongan_h").val(roundHalfUp(total, 4).toFixed(4));
    $("#po").val(nopo1); 
    $("#po1").val(nopo); 
    $("#sisapotongan").val(formatMoney(sisa));
    $("#ttl_sub").val(sisa);
    $("#ttl_dp").val(formatMoney(total_ftr));
    $("#ttl_dp_h").val(roundHalfUp(total_ftr, 2).toFixed(2));
    $("#pajak").val(formatMoney(ppn));
    $("#pajak_h").val(ppn);
    $("#pph").val(formatMoney(pph11));
    $("#pph_h").val(roundHalfUp(pph11, 2).toFixed(2));
    updateSetelahPotongan();
    $("#jumlahpotong").val(formatMoney(pot));
    $("#jml_potong").val(pot);
    $("#matauang").val(curren);
    $("#tanggal4").val(tanggals);
    $("#tgl_perhitungan").val(dates);

    $("#h_mattype").val(m_type);
    $("#h_matclass").val(m_class);
    $("#h_code_ctg").val(n_code);
    $("#h_cus_ctg").val(c_ctg);
    $("#select").val("1");   

    $("#form-simpan .chkC").prop("disabled", true); 
    if (allowedPO.length > 0) {
        $("#mytable2 tbody tr").each(function () {
            var poC = $(this).find("td:eq(2)").attr("po-ftr");
            if (allowedPO.includes(poC)) {
                $(this).find(".chkC").prop("disabled", false);
            }
        });
    }else{
        $("#mytable2 tbody tr").each(function () {
            $("#form-simpan .chkC").prop("disabled", false).prop("checked", false);
            $(this).find('td:eq(6) input').prop('disabled', true);
            $("#ttl_dp").val('');
            $("#ttl_dp_h").val('');
        });
    }

});        
</script>

<script type="text/javascript">
    $("#form-simpan select[name=combo_pph]").on('change', function(){
        var sum_sub = 0;
        var total = 0;
        var sisa = 0;
        var nopo= '';
        var ppn = 0;
        var cbddp = 0;
        var sum_pph = 0;
        $("#form-simpan input[type=checkbox]:checked").each(function () {  
            var po = $(this).closest('tr').find('td:eq(2)').attr('value');      
            var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
            var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
            var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
            var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
            var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
            var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;            
            sum_pph += price * (pph / 100);
            sum_sub += price;
            total += price_ro;
            sisa = sum_sub - total - sum_pph; 
            nopo= po;
            ppn= tax;
            cbddp= cbd;  

        });  
        $("#pph").val(formatMoney(sum_pph));
        $("#pph_h").val(roundHalfUp(sum_pph, 2).toFixed(2));
        updateSetelahPotongan();
    // $("#subtotal").val(formatMoney(sum_sub));
    // $("#subtotal_h").val(sum_sub);             
    // $("#po").val(nopo); 
    // $("#potongan").val(formatMoney(total));
    // $("#potongan_h").val(total);
    // $("#sisapotongan").val(formatMoney(sisa));
    // $("#ttl_sub").val(sisa);
    // $("#total").val(formatMoney(sisa));
    // $("#total_h").val(sisa);
    // $("#ttl_dp").val(formatMoney(cbddp));
    // $("#ttl_dp_h").val(cbddp);
    // $("#pajak").val(formatMoney(ppn));
    // $("#pajak_h").val(ppn);
    // $("#select").val("1");
});
</script>

<script type="text/javascript">
    $("#mytable1 input[name=txt_amount]").keyup(function(){
        var sum_amount = 0;
        var sum_total = 0;
        var sum_balance = 0;        
        $("#form-simpan input[type=checkbox]:checked").each(function () {        
            var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
            var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
            var select_amount = $(this).closest('tr').find('td:eq(6) input');                
            if(amount > balance){
                select_amount.val(balance);
                sum_amount += balance;
                sum_total = sum_amount;
            }else{
                sum_amount += amount;
                sum_total = sum_amount;        
            }   
        });
        $("#potongan").val(formatMoney(sum_total));
        $("#potongan_h").val(roundHalfUp(sum_total, 4).toFixed(4));
    });


    $("#mytable2 input[name=amount_ftr]").keyup(function(){
        var sum_amount = 0;
        var sum_total = 0;
        var sum_balance = 0;        
        $("#form-simpan input[type=checkbox]:checked").each(function () {        
            var amount = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
            var balance = parseFloat($(this).closest('tr').find('td:eq(5)').attr('total-ftr'),10) || 0;
            var select_amount = $(this).closest('tr').find('td:eq(6) input');                
            if(amount > balance){
                select_amount.val(balance);
                sum_amount += balance;
                sum_total = sum_amount;
            }else{
                sum_amount += amount;
                sum_total = sum_amount;        
            }   
        });
        $("#ttl_dp").val(formatMoney(sum_total));
        $("#ttl_dp_h").val(roundHalfUp(sum_total, 4).toFixed(4));
    });
</script>

<script type="text/javascript">
    $("input[name=labarugi]").keyup(function(){
        var laba = 0; 
        var jml1 = 0;
        var ttl_jml1 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
            jml1 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;              
            laba = laba_h;
            ttl_jml1 = ttl_h + jml1; 
        });
        $("#labarugi_h").val(formatMoney(laba));
        $("#jumlahpotong").val(formatMoney(jml1));
        $("#jml_potong").val(jml1);
        $("#sisapotongan").val(formatMoney(ttl_jml1));
        $("#ttl_sub2").val(ttl_jml1);

    });
</script>

<script type="text/javascript">
    $("input[name=selisihqty]").keyup(function(){
        var selisih = 0;
        var jml2 = 0; 
        var ttl_jml2 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
            jml2 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                
            selisih = selisih_h;
            ttl_jml2 = ttl_h + jml2; 
        });
        $("#selisihqty_h").val(formatMoney(selisih));
        $("#jumlahpotong").val(formatMoney(jml2));
        $("#jml_potong").val(jml2);
        $("#sisapotongan").val(formatMoney(ttl_jml2));
        $("#ttl_sub1").val(ttl_jml2);
    });
</script>

<script type="text/javascript">
    $("input[name=selisihharga]").keyup(function(){
        var selisihhrg = 0; 
        var jml3 = 0;
        var ttl_jml3 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
            jml3 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
            selisihhrg = selisihhrg_h;
            ttl_jml3 = ttl_h + jml3; 
        });
        $("#selisihharga_h").val(formatMoney(selisihhrg));
        $("#jumlahpotong").val(formatMoney(jml3));
        $("#jml_potong").val(jml3);
        $("#sisapotongan").val(formatMoney(ttl_jml3));
        $("#ttl_sub3").val(ttl_jml3);
    });
</script>

<script type="text/javascript">
    $("input[name=materai]").keyup(function(){
        var mater = 0; 
        var jml4 = 0;
        var ttl_jml4 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
            jml4 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                
            mater = mater_h;
            ttl_jml4 = ttl_h + jml4; 
        });
        $("#materai_h").val(formatMoney(mater));
        $("#jumlahpotong").val(formatMoney(jml4));
        $("#jml_potong").val(jml4);
        $("#sisapotongan").val(formatMoney(ttl_jml4));
        $("#ttl_sub4").val(ttl_jml4);
    });
</script>

<script type="text/javascript">
    $("input[name=potongbeli]").keyup(function(){
        var potongbeli = 0; 
        var jml5 = 0;
        var ttl_jml5 = 0;
        $("input[type=text]").each(function () {        
            var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
            jml5 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
            potongbeli = potongbeli_h;
            ttl_jml5 = ttl_h + jml5; 
        });
        $("#potongbeli_h").val(formatMoney(potongbeli));
        $("#jumlahpotong").val(formatMoney(jml5));
        $("#jml_potong").val(jml5);
        $("#sisapotongan").val(formatMoney(ttl_jml5));
        $("#ttl_sub5").val(ttl_jml5);
    });
</script>

<script type="text/javascript">
    $("input[name=ekspedisi]").keyup(function(){
        var ekspedisi = 0; 
        var jml6 = 0;
        var ttl_jml6 = 0;
        $("input[type=text]").each(function () {        
            var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;               
            jml6 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
            ekspedisi = ekspedisi_h;
            ttl_jml6 = ttl_h + jml6; 
        });
        $("#ekspedisi_h").val(formatMoney(ekspedisi));
        $("#jumlahpotong").val(formatMoney(jml6));
        $("#jml_potong").val(jml6);
        $("#sisapotongan").val(formatMoney(ttl_jml6));
        $("#ttl_sub6").val(ttl_jml6);
    });
</script>

<script type="text/javascript">
    $("input[name=moq]").keyup(function(){
        var moq = 0; 
        var jml = 0;
        var ttl_jml7 = 0;

        $("input[type=text]").each(function () {        
            var laba_h = parseFloat(document.getElementById('labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('moq').value,10) || 0;

            jml = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;
            moq = moq_h;
            ttl_jml7 = ttl_h + jml;

        });
        $("#moq_h").val(formatMoney(moq));
        $("#jumlahpotong").val(formatMoney(jml));
        $("#jml_potong").val(jml);
        $("#sisapotongan").val(formatMoney(ttl_jml7));
        $("#ttl_sub7").val(ttl_jml7);
    });
</script>

<script type="text/javascript">
    $("#form-simpan").on("click", "#calculate", function(){
        var jumlah = 0; 
        var total = 0;
        var ttl_dp = 0;
        var pajak = 0;
        var pph = 0;
        $("input[type=button]").each(function () { 
            var subtotal_h = parseFloat(document.getElementById('subtotal_h').value,10) || 0;
            var potongan_h = parseFloat(document.getElementById('potongan_h').value,10) || 0;
            var jml_potong = parseFloat(document.getElementById('jml_potong').value,10) || 0;
            var pajak_h = parseFloat(document.getElementById('pajak_setelah_potongan_h').value,10) || 0;
            var pph_h = parseFloat(document.getElementById('pph_setelah_potongan_h').value,10) || 0;
            var ttl_dp_h = parseFloat(document.getElementById('ttl_dp_h').value,10) || 0;

            pajak = pajak_h;
            pph = pph_h;
            jumlah = (subtotal_h - potongan_h + jml_potong) + pajak - pph;

            if (jumlah > '0') {
                if (ttl_dp_h > jumlah) {
                    ttl_dp = jumlah;
                    total = jumlah - ttl_dp;
                }else{
                    ttl_dp = ttl_dp_h;
                    total = jumlah - ttl_dp;
                }
            }
            else{
                ttl_dp = ttl_dp_h;
                total = jumlah - ttl_dp;
            }

        });

        $("#total").val(formatMoney(total));
        $("#total_h").val(roundHalfUp(total, 4).toFixed(4));
        $("#ttl_dp").val(formatMoney(ttl_dp));
        $("#ttl_dp_h").val(roundHalfUp(ttl_dp, 2).toFixed(2));
    });
</script>

<script type="text/javascript">
    // Kirim PV secara bulk (1 request, 1 transaksi di server). Dipisah jadi fungsi
    // supaya tombol "Try Again" di dialog gagal bisa memanggil ulang TANPA user harus
    // ceklis BPB lagi — kalau gagal, server rollback total & form tidak berubah.
    // #simpan dikunci selama request (cegah dobel-klik / dobel-PV).
    function submitBulkPV(headerData, items){
        $('#simpan').prop('disabled', true);
        // EDIT: POST ke endpoint EDIT (simpan sbg revisi -REV_NN), sertakan edit_no_kbon.
        var editNoKbon = (document.getElementById('edit_no_kbon') || {}).value || document.getElementById('nokontrabon').value;
        $.ajax({
            type:'POST',
            url:'insertkbon_bulk_edit.php',
            dataType:'json',
            data: { edit_no_kbon: editNoKbon, header: JSON.stringify(headerData), items: JSON.stringify(items) },
            cache: false,
            success: function(res){
                if (res && res.code === 'IR_ALREADY_USED') {
                    $('#simpan').prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Invoice Received already used',
                        html: 'This IR has already been used in another Payment Voucher' + (res.no_kbon ? ' (<b>' + res.no_kbon + '</b>)' : '') + '.<br>Cancel that PV first, or choose another IR.' });
                    return;
                }
                if (!res || !res.ok) {
                    $('#simpan').prop('disabled', false);
                    Swal.fire({ icon: 'error', title: 'Save failed — nothing was saved',
                        html: (res && res.msg ? res.msg : 'Save failed.') + (res && res.failed && res.failed.length ? '<br><small>Rows: ' + res.failed.join(', ') + '</small>' : '') + '<br><small>Your checked BPB are still here — click <b>Try Again</b> to resubmit (no need to re-check).</small>',
                        showCancelButton: true, confirmButtonText: 'Try Again', cancelButtonText: 'Close', confirmButtonColor: '#1E3A8A' })
                        .then(function(r){ if (r.isConfirmed) submitBulkPV(headerData, items); });
                    return;
                }
                localStorage.removeItem("profit_center");
                Swal.fire({icon:'success', title:'Data Saved Successfully', html: (res.no_kbon ? ('Payment Voucher: <b>' + res.no_kbon + '</b>') : ''), timer:5000, showConfirmButton:true, confirmButtonText:'OK'})
                    .then(function(){ window.location = 'payment-voucher-ap.php'; });
            },
            error: function (xhr) {
                $('#simpan').prop('disabled', false);
                console.log(xhr);
                Swal.fire({ icon: 'error', title: 'Server error — nothing was saved',
                    html: 'Nothing was saved (rolled back). Your checked BPB are still here — click <b>Try Again</b> to resubmit (no need to re-check).',
                    showCancelButton: true, confirmButtonText: 'Try Again', cancelButtonText: 'Close', confirmButtonColor: '#1E3A8A' })
                    .then(function(r){ if (r.isConfirmed) submitBulkPV(headerData, items); });
            }
        });
    }

    $("#form-simpan").on("click", "#simpan", function(){
        // WAJIB: tiap BPB yang diceklis harus punya No Faktur & Tgl Faktur (boleh "-").
        var fkMissing = [];
        $('#mytable tbody tr').each(function () {
            var $tr = $(this);
            if (!$tr.find('input.chkA[type=checkbox]').prop('checked')) return;
            var nob = ($tr.find('td:eq(1)').attr('value') || '').trim();
            var fn = $.trim($tr.find('.fk-no').val() || '');
            var ft = $.trim($tr.find('.fk-tgl').val() || '');
            var fnBad = (fn === '');
            var ftBad = (fn !== '' && fn !== '-' && ft === ''); // Tgl wajib hanya bila No Faktur berisi nomor nyata
            $tr.find('.fk-no').css('border-color', fnBad ? '#dc2626' : '');
            $tr.find('.fk-tgl').css('border-color', ftBad ? '#dc2626' : '');
            if (fnBad || ftBad) fkMissing.push(nob || '(BPB)');
        });
        if (fkMissing.length > 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'No Faktur / Tgl Faktur is required',
                    html: 'Please provide the <b>No Faktur</b> and <b>Tgl Faktur</b> for the following BPB (a dash "-" is allowed):<br><br>' + fkMissing.join('<br>') });
            } else {
                alert('No Faktur / Tgl Faktur is required for the following BPB: ' + fkMissing.join(', '));
            }
            return;
        }

        var no_kbon_h = document.getElementById('nokontrabon').value;
        var unik_code = document.getElementById('unik_code').value;
        var tgl_kbon_h = document.getElementById('tanggal').value;
        var tgl_kbon_p = document.getElementById('tgl_perhitungan').value;
        var tgl_kbon_s = document.getElementById('tanggal4').value; 
        var nama_supp_h = $('select[name=nama_supp] option').filter(':selected').val();
        var no_faktur_h = document.getElementById('no_faktur').value;
        var no_po_h = document.getElementById('po').value;
        var supp_inv_h = document.getElementById('txt_inv').value;
        var tgl_inv_h = document.getElementById('txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('txt_tgltempo').value;        
        var curr_h = document.getElementById('matauang').value;
        var sub_h = document.getElementById('subtotal_h').value;
        var tax_h = document.getElementById('pajak_setelah_potongan_h').value;
        var dp_h = document.getElementById('ttl_dp_h').value;
        var pph_h = document.getElementById('pph_setelah_potongan_h').value;
        var total_h = document.getElementById('total_h').value;
        var create_user_h = '<?php echo $user; ?>';
        var jml_return = document.getElementById('potongan_h').value;
        var lr_kurs = document.getElementById('labarugi').value;
        var s_qty = document.getElementById('selisihqty').value;
        var s_harga = document.getElementById('selisihharga').value;
        var materai = document.getElementById('materai').value;
        var pot_beli = document.getElementById('potongbeli').value;
        var ekspedisi = document.getElementById('ekspedisi').value;
        var moq = document.getElementById('moq').value;
        var jml_potong = document.getElementById('jml_potong').value;
        var potongan_ppn = document.getElementById('potongan_ppn').value;
        var potongan_pph = document.getElementById('potongan_pph').value;
        var mattype = document.getElementById('h_mattype').value;
        var matclass = document.getElementById('h_matclass').value;
        var n_code_category = document.getElementById('h_code_ctg').value;
        var cus_ctg = document.getElementById('h_cus_ctg').value;
        var profit_center = $('select[name=profit_center] option').filter(':selected').val();
        var ir_number = $('select[name=ir_number] option').filter(':selected').val();
        var bank_account = $('select[name=sel_bank_account] option').filter(':selected').val();
        var from_account = $('select[name=from_account] option').filter(':selected').val();
        var from_bank = document.getElementById('from_bank').value;
        var from_bank_curr = document.getElementById('from_bank_curr').value;
        //&& tgl_kbon_h >= tgl_kbon_p
        if(total_h != '' && total_h >= 0 && ir_number != '' && bank_account != '' && profit_center != '' && from_account != ''){
            var headerData = {'no_kbon_h':no_kbon_h, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'dp_h':dp_h, 'pph_h':pph_h, 'total_h':total_h, 'jml_return':jml_return, 'lr_kurs':lr_kurs, 's_qty':s_qty, 's_harga':s_harga, 'materai':materai, 'pot_beli':pot_beli, 'ekspedisi':ekspedisi, 'moq':moq, 'jml_potong':jml_potong, 'potongan_ppn':potongan_ppn, 'potongan_pph':potongan_pph, 'no_po_h':no_po_h, 'tgl_kbon_s':tgl_kbon_s, 'unik_code':unik_code, 'mattype':mattype, 'matclass':matclass, 'n_code_category':n_code_category, 'cus_ctg':cus_ctg, 'profit_center':profit_center, 'ir_number':ir_number, 'bank_account':bank_account, 'from_account':from_account, 'from_bank':from_bank, 'from_bank_curr':from_bank_curr};

            var items = [];
            $("#form-simpan input[type=checkbox]:checked").each(function () {
                        //header
                        var no_kbon = document.getElementById('nokontrabon').value; 
                        var unik_code = document.getElementById('unik_code').value;       
                        var tgl_kbon = document.getElementById('tanggal').value;
                        var tgl_kbon_p = document.getElementById('tgl_perhitungan').value;
                        var jurnal = document.getElementById('jurnal').value;
                        var nama_supp = $('select[name=nama_supp] option').filter(':selected').val();
                        var profit_center = $('select[name=profit_center] option').filter(':selected').val();
                        var no_faktur = document.getElementById('no_faktur').value;
                        var supp_inv = document.getElementById('txt_inv').value;
                        var tgl_inv = document.getElementById('txt_tglsi').value;
                        var tgl_tempo = document.getElementById('txt_tgltempo').value;        
                        var curr = document.getElementById('matauang').value;
                        var ceklist = document.getElementById('select').value;          
                        var total = document.getElementById('total_h').value; 
                        var dp = document.getElementById('ttl_dp_h').value;
                        var start_date = document.getElementById('start_date').value;
                        var end_date = document.getElementById('end_date').value;  
                        var create_user = '<?php echo $user; ?>';
                        //table bpb                               
                        var no_bpb = $(this).closest('#mytable tr').find('td:eq(1)').attr('value');
                        // No Faktur & Tgl Faktur dari input (auto-fill IR / manual). Baris BPB
                        // yang diceklis wajib minimal "-".
                        var no_faktur_in = '', tgl_faktur_in = '';
                        // Baris BPB (#mytable) atau baris RETUR (#mytable1) — ambil fk dari mana pun row-nya.
                        var $fkRow = $(this).closest('#mytable tr');
                        if ($fkRow.length === 0) $fkRow = $(this).closest('#mytable1 tr');
                        if ($fkRow.length) {
                            no_faktur_in = $.trim($fkRow.find('.fk-no').val() || '');
                            tgl_faktur_in = $.trim($fkRow.find('.fk-tgl').val() || '');
                        }
                        var no_po = $(this).closest('#mytable tr').find('td:eq(2)').attr('value');
                        var tgl_bpb = $(this).closest('#mytable tr').find('td:eq(3)').attr('value');
                        var price = parseFloat($(this).closest('#mytable tr').find('td:eq(4)').attr('data-subtotal'),10) ||0;
                        var tax = parseFloat($(this).closest('#mytable tr').find('td:eq(5)').attr('data-tax'),10) ||0;
                        var cash = parseFloat($(this).closest('#mytable tr').find('td:eq(8)').attr('data'),10) ||0;
                        var tgl_po = $(this).closest('#mytable tr').find('td:eq(12)').attr('value');
                        var pph = parseFloat($(this).closest('#mytable tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').val(),10) ||0;
                        var idtax = $(this).closest('#mytable tr').find('td:eq(6)').find('select[name=combo_pph] option').filter(':selected').attr('data-idtax');
                        //table ro
                        var no_ro = $(this).closest('#mytable1 tr').find('td:eq(1)').attr('data-ro');
                        var no_bppb = $(this).closest('#mytable1 tr').find('td:eq(2)').attr('valuess');
                        var ttl_ro = parseFloat($(this).closest('#mytable1 tr').find('td:eq(6) input').val(),10) || 0;
                        var mattype = $(this).closest('#mytable tr').find('td:eq(15)').attr('value') || $(this).closest('#mytable1 tr').find('td:eq(8)').attr('valuess');
                        var matclass = $(this).closest('#mytable tr').find('td:eq(16)').attr('value') || $(this).closest('#mytable1 tr').find('td:eq(9)').attr('valuess');
                        var n_code_category = $(this).closest('#mytable tr').find('td:eq(17)').attr('value') || $(this).closest('#mytable1 tr').find('td:eq(10)').attr('valuess');
                        var cus_ctg = $(this).closest('#mytable tr').find('td:eq(18)').attr('value') || $(this).closest('#mytable1 tr').find('td:eq(11)').attr('valuess'); 

                        //table ftr 
                        var no_ftr = $(this).closest('#mytable2 tr').find('td:eq(1)').attr('no-ftr');
                        var no_po_ftr = $(this).closest('#mytable2 tr').find('td:eq(2)').attr('po-ftr');
                        var tgl_po_ftr = $(this).closest('#mytable2 tr').find('td:eq(3)').attr('tglpo-ftr');
                        var no_pi_ftr = $(this).closest('#mytable2 tr').find('td:eq(4)').attr('pi-ftr');
                        var ttl_ftr = parseFloat($(this).closest('#mytable2 tr').find('td:eq(6) input').val(),10) || 0;
                        var curr_ftr = $(this).closest('#mytable2 tr').find('td:eq(7)').attr('curr-ftr');
                        var kbon_ftr = $(this).closest('#mytable2 tr').find('td:eq(8)').attr('kbon-ftr');
                        var tglkbon_ftr = $(this).closest('#mytable2 tr').find('td:eq(9)').attr('tglkbon-ftr');
                        var lp_ftr = $(this).closest('#mytable2 tr').find('td:eq(10)').attr('lp-ftr');
                        var tgllp_ftr = $(this).closest('#mytable2 tr').find('td:eq(11)').attr('tgllp-ftr');
                        var pv_ftr = $(this).closest('#mytable2 tr').find('td:eq(12)').attr('pv-ftr');
                        var bankout_ftr = $(this).closest('#mytable2 tr').find('td:eq(13)').attr('bankout-ftr');
                        var bankoutdate_ftr = $(this).closest('#mytable2 tr').find('td:eq(14)').attr('bankoutdate-ftr');
                        var coa_ftr = $(this).closest('#mytable2 tr').find('td:eq(15)').attr('coa-ftr');

                        var sum_pph = 0;
                        var sum_sub = 0;
                        var sum_tax = 0;
                        var sum_total = 0;
                        var sum_dp = 0;
                        sum_sub += price;
                        sum_tax += tax;
                        sum_dp += dp;  
                        sum_pph += sum_sub * (pph / 100);   
                        sum_total += total - sum_pph - sum_dp;
        // && tgl_kbon >= tgl_kbon_p
        if(total != '' && total >= 0){
            items.push({'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'jurnal':jurnal, 'no_bpb':no_bpb, 'no_po':no_po,  'no_ro':no_ro,
                'nama_supp':nama_supp, 'tgl_bpb':tgl_bpb, 'no_faktur':no_faktur, 'supp_inv':supp_inv, 'tgl_inv':tgl_inv, 'tgl_tempo':tgl_tempo,
                'curr':curr, 'ceklist':ceklist, 'cash':cash, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_tax':sum_tax, 'sum_dp':sum_dp, 'sum_pph':sum_pph, 'sum_total':sum_total, 'start_date':start_date, 'end_date':end_date, 'pph':pph, 'idtax':idtax, 'tgl_po':tgl_po, 'ttl_ro':ttl_ro, 'no_bppb':no_bppb, 'unik_code':unik_code, 'mattype':mattype, 'matclass':matclass, 'n_code_category':n_code_category, 'cus_ctg':cus_ctg, 'no_ftr':no_ftr, 'no_po_ftr':no_po_ftr, 'tgl_po_ftr':tgl_po_ftr, 'no_pi_ftr':no_pi_ftr, 'ttl_ftr':ttl_ftr, 'curr_ftr':curr_ftr, 'kbon_ftr':kbon_ftr, 'tglkbon_ftr':tglkbon_ftr, 'lp_ftr':lp_ftr, 'tgllp_ftr':tgllp_ftr, 'pv_ftr':pv_ftr, 'bankout_ftr':bankout_ftr, 'bankoutdate_ftr':bankoutdate_ftr, 'coa_ftr':coa_ftr, 'profit_center':profit_center, 'ir_number':ir_number, 'no_faktur_in':no_faktur_in, 'tgl_faktur_in':tgl_faktur_in});
            // console.log(data);
        }
    });

    // ===== KIRIM SEKALI: bulk 1-transaksi (all-or-nothing) =====
    // Header + SEMUA BPB/RO masuk 1 request. Server commit hanya kalau semua sukses;
    // kalau ada yang gagal / proses mati -> rollback total (tidak ada PV setengah jadi).
    // Kalau gagal, dialog menyediakan "Try Again" -> submit ulang tanpa ceklis lagi.
    submitBulkPV(headerData, items);
}

        // else if (document.getElementById('tanggal').value < document.getElementById('tgl_perhitungan').value){
        // alert("Contrabon Date Can't be less than BPB Date ");
        // }               

        if(document.querySelectorAll("input[name='select[]']:checked").length == 0){
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please check the BPB number'});
        }else if (document.getElementById('total_h').value == ''){
            document.getElementById('calculate').focus();
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please do the calculation'});
        }else if ($('select[name=ir_number] option').filter(':selected').val() == ''){
            document.getElementById('ir_number').focus();
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Invoice Received Number'});
        }else if ($('select[name=sel_bank_account] option').filter(':selected').val() == ''){
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select bank account supplier'});
        }else if ($('select[name=from_account] option').filter(':selected').val() == ''){
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select From Account'});
        }else if ($('select[name=profit_center] option').filter(':selected').val() == ''){
            document.getElementById('profit_center').focus();
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
        }else if (document.getElementById('total_h').value < 0){
            Swal.fire({icon: 'warning', title: 'Oops...', text: "Contrabon can't be minus"});
        }else{

        } 
    });
</script>

<script type="text/javascript">     
    $('#mytable tbody tr').on('click', 'td:eq(1)', function(){                
        $('#mymodalbpb').modal('show');
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(3)').text();
        var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(11)').attr('value');
        var top = $(this).closest('tr').find('td:eq(13)').attr('value');
        var curr = document.getElementById('matauang').value;
        var confirm = $(this).closest('tr').find('td:eq(9)').attr('value');
        var confirm2 = $(this).closest('tr').find('td:eq(10)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(12)').text();  

        var targetUrl = no_po.includes("NAK") ? 'ajaxbpb_knitting.php' : 'ajaxbpb.php';  

        $.ajax({
            type : 'post',
            url : targetUrl,
            data : {'no_bpb': no_bpb},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_bpb').html(no_bpb);
        $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
        $('#txt_no_po').html('No PO : ' + no_po + '');
        $('#txt_supp2').html('Supplier : ' + supp + '');
        $('#txt_top').html('TOP : ' + top + ' Days');
        $('#txt_curr').html('Currency : ' + curr + '');        
        $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
        $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
        $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');
    });

</script>

<script type="text/javascript">
// ============================================================================
// AUTO-CEKLIS BPB (+ RO/retur) saat Invoice Received Number dipilih.
// Daftar BPB milik IR diambil dari ir_kontrabon_bpb (via ir_kontrabon_h.doc_number).
//   #mytable  = tabel BPB   -> NO BPB di td:eq(1) attr "value",  checkbox .chkA
//   #mytable1 = tabel RO    -> NO BPB di td:eq(4) attr "valuess", checkbox .chkB
// Ceklis di-reset dulu supaya set-nya persis sesuai IR yang dipilih, lalu
// trigger 'change' (sama seperti klik manual) agar Total BPB terhitung ulang.
// ============================================================================
$(document).on('change', '#ir_number', function () {
    var ir = $(this).val();

    // Selalu re-enable + reset dulu (supaya ganti/clear IR bersih).
    $('#mytable  input.chkA[type=checkbox]').prop('disabled', false).prop('checked', false).removeAttr('title');
    $('#mytable1 input.chkB[type=checkbox]').prop('disabled', false).prop('checked', false).removeAttr('title');
    $('#mytable .fk-no, #mytable .fk-tgl, #mytable1 .fk-no, #mytable1 .fk-tgl').prop('disabled', false).val('').css('border-color', ''); // buka kunci + kosongkan (BPB + RO)
    $('#fkOptions').empty(); window.irFakturMap = {}; // reset saran No Faktur

    if (!ir || ir === '-' || ir === '') {
        $('#form-simpan input[id=select]').first().trigger('change'); // recalc -> 0, kembali bisa manual
        return;
    }

    $.post('ajx_pv_ir_bpb.php', { ir_number: ir }, function (res) {
        if (!res || !res.ok) { return; }
        var set = {};
        (res.bpb || []).forEach(function (b) { set[String(b).trim()] = true; });
        var totalIr = Object.keys(set).length;
        var found = 0;

        // Saran No Faktur (datalist) dari SEMUA faktur milik IR + map utk auto-isi Tgl
        // saat user memilih salah satu faktur di BPB tambahan (tetap boleh ketik lain).
        window.irFakturMap = {};
        var _opts = '';
        (res.faktur_list || []).forEach(function (f) {
            var n = String(f.no_faktur || '').trim();
            if (n) { _opts += '<option value="' + n.replace(/"/g, '&quot;') + '"></option>'; window.irFakturMap[n] = f.tgl_faktur || ''; }
        });
        $('#fkOptions').html(_opts);

        var fakMap = res.faktur || {};
        // Ceklis BPB milik IR ini + isi No Faktur / Tgl Faktur (tetap boleh diedit manual).
        $('#mytable tbody tr').each(function () {
            var $tr = $(this);
            var nob = ($tr.find('td:eq(1)').attr('value') || '').trim();
            if (set[nob]) {
                var fk = fakMap[nob] || {};
                // Nilai yang datang dari IR -> diisi & DIKUNCI (disabled, tak bisa diubah).
                // Kalau IR tak menyediakan -> biarkan kosong & tetap bisa diisi manual.
                if (fk.no_faktur) { $tr.find('.fk-no').val(fk.no_faktur).prop('disabled', true); }
                else { $tr.find('.fk-no').val('').prop('disabled', false); }
                if (fk.tgl_faktur) { $tr.find('.fk-tgl').val(fk.tgl_faktur).prop('disabled', true); }
                else { $tr.find('.fk-tgl').val('').prop('disabled', false); }
                var cb = $tr.find('input.chkA[type=checkbox]');
                if (cb.length && !cb.prop('checked')) { cb.prop('checked', true).trigger('change'); }
                found++;
            }
        });

        // Ceklis RO/retur + isi No/Tgl Faktur. RO SUDAH tersimpan di IR dengan NOMOR
        // RO-nya sendiri (No BPPB, td:eq(2)) — jadi map langsung by nomor RO itu.
        $('#mytable1 tbody tr').each(function () {
            var $tr = $(this);
            var nob = ($tr.find('td:eq(2)').attr('valuess') || '').trim();
            if (set[nob]) {
                var fk = fakMap[nob] || {};
                if (fk.no_faktur) { $tr.find('.fk-no').val(fk.no_faktur).prop('disabled', true); }
                else { $tr.find('.fk-no').val('').prop('disabled', false); }
                if (fk.tgl_faktur) { $tr.find('.fk-tgl').val(fk.tgl_faktur).prop('disabled', true); }
                else { $tr.find('.fk-tgl').val('').prop('disabled', false); }
                var cb = $tr.find('input.chkB[type=checkbox]');
                if (cb.length && !cb.prop('checked')) { cb.prop('checked', true).trigger('change'); }
            }
        });

        // Recalc final.
        $('#form-simpan input[id=select]').first().trigger('change');

        // KUNCI: selama IR terpilih, semua checkbox BPB & RO tidak bisa diubah
        // (tidak bisa di-uncheck, juga tidak bisa nambah baris lain). Set-nya
        // sepenuhnya ditentukan oleh IR. Clear IR (pilih '-') utk buka kunci.
        // Kunci HANYA baris yang ter-ceklis dari IR; yang tidak terceklis biar aktif
        // supaya user bisa menambah BPB lain manual.
        $('#mytable  input.chkA[type=checkbox]:checked').prop('disabled', true).attr('title', 'From selected IR — locked. Clear the IR to edit.');
        $('#mytable1 input.chkB[type=checkbox]:checked').prop('disabled', true).attr('title', 'From selected IR — locked. Clear the IR to edit.');

        // Info kalau ada BPB IR yang tidak tampil (mis. di luar filter tanggal/PC).
        if (totalIr > 0 && found < totalIr && typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true, position: 'top-end', icon: 'info',
                title: found + ' of ' + totalIr + ' BPB checked',
                text: 'Some BPB in this IR are not shown — check the Supplier / date filter.',
                showConfirmButton: false, timer: 4500, timerProgressBar: true
            });
        } else if (totalIr === 0 && typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true, position: 'top-end', icon: 'warning',
                title: 'No BPB linked to this IR',
                text: 'This IR has no BPB from the kontrabon flow.',
                showConfirmButton: false, timer: 4000, timerProgressBar: true
            });
        }
    }, 'json');
});

// Saat user memilih / mengetik No Faktur pada BPB tambahan dan nilainya COCOK dengan
// salah satu faktur milik IR -> auto-isi Tgl Faktur baris itu (hanya kalau editable).
$(document).on('input change', '#mytable input.fk-no', function () {
    var v = $.trim($(this).val() || '');
    if (window.irFakturMap && Object.prototype.hasOwnProperty.call(window.irFakturMap, v) && window.irFakturMap[v]) {
        var $tgl = $(this).closest('tr').find('.fk-tgl');
        if (!$tgl.prop('disabled')) { $tgl.val(window.irFakturMap[v]).css('border-color', ''); }
    }
});

// Catatan: BPB yang ditambah manual dibiarkan No Faktur/Tgl Faktur KOSONG dulu,
// tapi WAJIB diisi sebelum save (boleh "-"). Validasinya ada di handler #simpan.
</script>

<script type="text/javascript">
// ============================================================================
// EDIT: hitung ulang total otomatis saat halaman dibuka. Baris BPB & RO existing
// sudah ter-ceklis dari server, jadi cukup trigger recalc + Calculate agar
// Subtotal / PPn / Return / Total ikut terisi seperti setelah user klik manual.
// ============================================================================
$(window).on('load', function () {
    setTimeout(function () {
        var $sel = $('#form-simpan input[id=select]').first();
        if ($sel.length) { $sel.trigger('change'); }
        // Lipat nilai potongan (bila ada) ke Jumlah Potongan.
        ['labarugi','selisihqty','selisihharga','materai','potongbeli','ekspedisi','moq'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el && $.trim(el.value) !== '') { $(el).trigger('keyup'); }
        });
        var pp = document.getElementById('potongan_ppn'); if (pp && $.trim(pp.value) !== '') { $(pp).trigger('keyup'); }
        var ph = document.getElementById('potongan_pph'); if (ph && $.trim(ph.value) !== '') { $(ph).trigger('keyup'); }
        // Hitung Total akhir.
        $('#calculate').trigger('click');

        // EDIT: muat saran No Faktur (#fkOptions) dari IR terpilih TANPA me-reset /
        // re-check baris (beda dari handler '#ir_number change' yang mereset tabel).
        // Supaya saat user ceklis BPB tambahan lalu isi No Faktur, pilihan faktur
        // milik IR ini langsung tersedia + auto-isi Tgl Faktur bila cocok.
        var _irPre = $('select[name=ir_number]').val();
        if (_irPre && _irPre !== '-' && _irPre !== '') {
            $.post('ajx_pv_ir_bpb.php', { ir_number: _irPre }, function (res) {
                if (!res || !res.ok) { return; }
                window.irFakturMap = window.irFakturMap || {};
                if (!document.getElementById('fkOptions')) { $('body').append('<datalist id="fkOptions"></datalist>'); }
                var _o = '';
                (res.faktur_list || []).forEach(function (f) {
                    var n = String(f.no_faktur || '').trim();
                    if (n) { _o += '<option value="' + n.replace(/"/g, '&quot;') + '"></option>'; window.irFakturMap[n] = f.tgl_faktur || ''; }
                });
                $('#fkOptions').html(_o);
            }, 'json');
        }
    }, 350);
});
</script>

</div><!-- body-row END -->
</div>
</div>

<script>
  // Sidebar collapse (mirror create-payment-voucher-ap.php).
  $('#body-row .collapse').collapse('hide');
  $('#collapse-icon').addClass('fa-angle-double-left');
  $('[data-toggle=sidebar-colapse]').click(function() { SidebarCollapse(); });
  function SidebarCollapse () {
      $('.menu-collapsed').toggleClass('d-none');
      $('.sidebar-submenu').toggleClass('d-none');
      $('.submenu-icon').toggleClass('d-none');
      $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
      var SeparatorTitle = $('.sidebar-separator-title');
      if ( SeparatorTitle.hasClass('d-flex') ) { SeparatorTitle.removeClass('d-flex'); }
      else { SeparatorTitle.addClass('d-flex'); }
      $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }
</script>

</body>
</html>
