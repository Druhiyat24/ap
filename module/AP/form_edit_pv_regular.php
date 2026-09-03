<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 13px;;
    }

    input {
        font-size: 13px;;
    }

</style>

<?php
$no_kbon = base64_decode($_GET['no_kbon']);

$sql = mysqli_query($conn2,"select no_kbon from ap_edit_kontrabon_h where no_kbon = '$no_kbon'");
$row = mysqli_fetch_array($sql);
echo $nokbon['no_kbon'];

// Prefill untuk field baru (IR Number, Supplier Bank Account, From Account, Koreksi
// PPN/PPh) — dibaca dari baris LIVE yang masih ada (copy_data_kontrabon.php hanya
// mengubah status jadi 'Updating', tidak menghapus), jadi tetap terbaca meskipun
// snapshot ap_edit_* dibuat sebelum kolom-kolom ini ada di skema.
$esc_kbon = mysqli_real_escape_string($conn2, $no_kbon);
$hdr_live = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT ir_number, id_bank_account, from_account, from_bank, from_bank_curr, nama_supp FROM kontrabon_h WHERE no_kbon = '$esc_kbon' ORDER BY id DESC LIMIT 1")) ?: [];
$pot_live = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT potongan_ppn, potongan_pph FROM potongan WHERE no_kbon = '$esc_kbon' ORDER BY id DESC LIMIT 1")) ?: [];
$pf_ir_number = $hdr_live['ir_number']      ?? '';
$pf_id_bank   = $hdr_live['id_bank_account'] ?? '';
$pf_from_acc  = $hdr_live['from_account']   ?? '';
$pf_from_bank = $hdr_live['from_bank']      ?? '';
$pf_from_curr = $hdr_live['from_bank_curr'] ?? '';
$pf_supp      = $hdr_live['nama_supp']      ?? '';
$pf_pot_ppn   = $pot_live['potongan_ppn']   ?? 0;
$pf_pot_pph   = $pot_live['potongan_pph']   ?? 0;

// Rekening bank milik supplier (dropdown Supplier Bank Account) + nilai display terpilih.
$__banks = [];
if ($pf_supp !== '') {
    $qb = mysqli_query($conn2,
        "SELECT msb.id, msb.bank_account, msb.bank_currency, msb.bank_name, msb.beneficiary_name
         FROM master_supplier_bank msb
         INNER JOIN mastersupplier ms ON ms.id_supplier = msb.id_supplier
         WHERE ms.Supplier = '" . mysqli_real_escape_string($conn2, $pf_supp) . "' AND msb.status = 'Active'
         ORDER BY msb.bank_name ASC");
    while ($rb = mysqli_fetch_assoc($qb)) $__banks[] = $rb;
}
$__sel_bankname = ''; $__sel_benef = ''; $__sel_bankcurr = '';
foreach ($__banks as $b) {
    if ((string)$b['id'] === (string)$pf_id_bank) {
        $__sel_bankname = $b['bank_name']; $__sel_benef = $b['beneficiary_name']; $__sel_bankcurr = $b['bank_currency'];
    }
}

// Prefill field header (layout disamakan dengan create pv_regular.php).
$eh = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT tgl_kbon, curr, supp_inv, no_faktur, profit_center, tgl_kbon2 FROM ap_edit_kontrabon_h WHERE no_kbon = '$esc_kbon' LIMIT 1")) ?: [];
$ed = mysqli_fetch_assoc(mysqli_query($conn2, "SELECT tgl_inv, tgl_tempo FROM ap_edit_kontrabon WHERE no_kbon = '$esc_kbon' LIMIT 1")) ?: [];
$pf_tgl_kbon  = !empty($eh['tgl_kbon'])  ? date('Y-m-d', strtotime($eh['tgl_kbon']))  : date('Y-m-d');
$pf_curr      = $eh['curr'] ?? '';
$pf_supp_inv  = $eh['supp_inv'] ?? '';
$pf_no_faktur = $eh['no_faktur'] ?? '';
$pf_pc        = $eh['profit_center'] ?? '';
$pf_tgl_kbon2 = !empty($eh['tgl_kbon2']) ? date('Y-m-d', strtotime($eh['tgl_kbon2'])) : $pf_tgl_kbon;
$pf_tgl_inv   = !empty($ed['tgl_inv'])   ? date('Y-m-d', strtotime($ed['tgl_inv']))   : date('Y-m-d');
$pf_tgl_tempo = !empty($ed['tgl_tempo']) ? date('Y-m-d', strtotime($ed['tgl_tempo'])) : date('Y-m-d');
$pf_pc_nama = '';
if ($pf_pc !== '') {
    $rp = mysqli_fetch_assoc(mysqli_query($conn1, "SELECT nama_pc FROM master_pc WHERE kode_pc = '" . mysqli_real_escape_string($conn1, $pf_pc) . "' LIMIT 1"));
    $pf_pc_nama = $rp['nama_pc'] ?? $pf_pc;
}
?>

<!-- MAIN -->
<div class="container-fluid mt-2 p-3" style="padding-left: 2rem; padding-right: 2rem;">
  <div class="card mb-3" style="border: none; background: transparent;">
  <form id="form-data" method="post">
    <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #191970, #1e90ff); border: none; font-weight: 700; letter-spacing: 0.4px; color: #fff; cursor: pointer;" data-toggle="collapse" data-target="#header_card_body" aria-expanded="true" aria-controls="header_card_body">
                    <span><i class="fa fa-file-text-o mr-2"></i> FORM PAYMENT VOUCHER</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="header_card_body">
                <div class="card-body p-2">
                    <div class="form-row">

                        <div class="col-md-3 mb-3">
                            <label for="nokontrabon"><b>No Payment Voucher</b></label>
                            <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="nokontrabon" name="nokontrabon" value="<?= htmlspecialchars($no_kbon) ?>">
                            <input type="hidden" name="unik_code" id="unik_code" value="<?php $karakter='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789'; echo substr(str_shuffle($karakter),0,16); ?>" autocomplete="off" readonly>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="tanggal"><b>Payment Voucher Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" name="tanggal" id="tanggal" class="form-control form-control-sm tanggal" value="<?= htmlspecialchars($pf_tgl_kbon) ?>">
                            <input type="hidden" name="tgl_perhitungan" id="tgl_perhitungan" value="">
                            <input type="hidden" name="txt_top" id="txt_top" value="0">
                            <input type="hidden" name="tanggal3" id="tanggal3" value="<?= htmlspecialchars($pf_tgl_kbon) ?>">
                            <input type="hidden" name="tanggal4" id="tanggal4" value="<?= htmlspecialchars($pf_tgl_kbon2) ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>
                            <select class="form-control selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true">
                                <?php
                                if ($pf_pc !== '') { echo '<option value="'.htmlspecialchars($pf_pc).'" selected="selected">'.htmlspecialchars($pf_pc_nama).'</option>'; }
                                else { echo '<option value="" disabled selected="true">Select Profit Center</option>'; }
                                $qpc = mysqli_query($conn1, "select kode_pc, nama_pc from master_pc where status='Active' and kode_pc != '".mysqli_real_escape_string($conn1,$pf_pc)."'");
                                if ($qpc) while ($rpc = mysqli_fetch_assoc($qpc)) { echo '<option value="'.htmlspecialchars($rpc['kode_pc']).'">'.htmlspecialchars($rpc['nama_pc']).'</option>'; }
                                ?>
                            </select>
                            <input type="hidden" id="jurnal" name="jurnal" value="0">
                        </div>

                        <!-- Hidden mengikuti create: Currency, No Supplier Invoice, Supplier Invoice Date, No Tax Invoice -->
                        <input type="hidden" id="matauang" name="matauang" value="<?= htmlspecialchars($pf_curr) ?>">
                        <input type="hidden" id="txt_inv" name="txt_inv" value="<?= htmlspecialchars($pf_supp_inv) ?>">
                        <input type="hidden" id="txt_tglsi" name="txt_tglsi" value="<?= htmlspecialchars($pf_tgl_inv) ?>">
                        <input type="hidden" id="no_faktur" name="no_faktur" value="<?= htmlspecialchars($pf_no_faktur) ?>">

                        <div class="col-md-2 mb-3">
                            <label for="txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;" class="form-control form-control-sm tanggal1" name="txt_tgltempo" id="txt_tgltempo" value="<?= htmlspecialchars($pf_tgl_tempo) ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="sel_bank_account"><b>Supplier Bank Account <i style="color: red;">*</i></b></label>
                            <select class="form-control form-control-sm selectpicker" id="sel_bank_account" name="sel_bank_account" data-live-search="true" data-dropup-auto="false" data-size="8">
                                <option value="">-- Select Bank Account --</option>
                                <option value="-" <?= ($pf_id_bank === '-' ? 'selected="selected"' : '') ?>>- (Tidak ada rekening)</option>
                                <?php foreach ($__banks as $b): ?>
                                <option value="<?= htmlspecialchars($b['id']) ?>"
                                        data-account="<?= htmlspecialchars($b['bank_account']) ?>"
                                        data-currency="<?= htmlspecialchars($b['bank_currency']) ?>"
                                        data-bankname="<?= htmlspecialchars($b['bank_name']) ?>"
                                        data-beneficiary="<?= htmlspecialchars($b['beneficiary_name']) ?>"
                                        <?= ((string)$b['id'] === (string)$pf_id_bank ? 'selected="selected"' : '') ?>>
                                    <?= htmlspecialchars($b['bank_account']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="disp_bankname"><b>Supplier Bank Name</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="disp_bankname" name="disp_bankname" value="<?= htmlspecialchars($__sel_bankname) ?>" placeholder="-">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="disp_beneficiary"><b>Beneficiary Name</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="disp_beneficiary" name="disp_beneficiary" value="<?= htmlspecialchars($__sel_benef) ?>" placeholder="-">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="disp_currency"><b>Supplier Bank Currency</b></label>
                            <input type="text" readonly class="form-control form-control-sm bg-light" id="disp_currency" name="disp_currency" value="<?= htmlspecialchars($__sel_bankcurr) ?>" placeholder="-">
                        </div>

                        <input type="hidden" id="txt_bank_supp" name="txt_bank_supp">
                        <input type="hidden" id="txt_akun_supp" name="txt_akun_supp">

                        <div class="col-md-3 mb-3">
                            <label for="ir_number"><b>Invoice Received Number <i style="color: red;">*</i></b></label>
                            <select class="form-control selectpicker" name="ir_number" id="ir_number" data-dropup-auto="false" data-size="5" data-live-search="true">
                                <option value="-">-</option>
                                <?php
                                if ($pf_ir_number !== '' && $pf_ir_number !== '-') {
                                    echo '<option value="' . htmlspecialchars($pf_ir_number) . '" selected="selected">' . htmlspecialchars($pf_ir_number) . ' (current)</option>';
                                }
                                $qir = mysqli_query($conn1, "select doc_number from ir_invoice_supp_h
                                    where status != 'Cancel' and nama_supp = '" . mysqli_real_escape_string($conn1, $pf_supp) . "'
                                    and doc_number NOT IN (select ir_number from kontrabon_h
                                        where status <> 'Cancel' and ir_number is not null and ir_number <> '' and ir_number <> '-')");
                                if ($qir) while ($rir = mysqli_fetch_assoc($qir)) {
                                    if ($rir['doc_number'] == $pf_ir_number) continue;
                                    echo '<option value="' . htmlspecialchars($rir['doc_number']) . '">' . htmlspecialchars($rir['doc_number']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="from_account"><b>From Account <i style="color: red;">*</i></b></label>
                            <select class="form-control form-control-sm selectpicker" id="from_account" name="from_account" data-live-search="true">
                                <option value="">Select Account</option>
                                <?php
                                $where_akun = '';
                                if (strcasecmp($user, 'dessy') === 0) { $where_akun = "WHERE tipe_akun = 'BANK'"; }
                                elseif (strcasecmp($user, 'Yeni_acc') === 0) { $where_akun = "WHERE tipe_akun = 'CASH'"; }
                                $qacc = mysqli_query($conn1, "
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
                                if ($qacc) while ($ra = mysqli_fetch_assoc($qacc)) {
                                    $label = ($ra['tipe_akun'] === 'CASH') ? $ra['bank'] : $ra['account'];
                                    $selA = ((string)$ra['account'] === (string)$pf_from_acc) ? 'selected="selected"' : '';
                                    echo "<option value='" . htmlspecialchars($ra['account']) . "' data-bank='" . htmlspecialchars($ra['bank']) . "' data-currency='" . htmlspecialchars($ra['curr']) . "' data-namapc='" . htmlspecialchars($ra['nama_pc']) . "' data-kodepc='" . htmlspecialchars($ra['kode_pc']) . "' data-kodebank='" . htmlspecialchars($ra['b_code']) . "' $selA>" . htmlspecialchars($label) . " </option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="from_bank"><b>From Bank Name <i style="color: red;">*</i></b></label>
                            <input type="text" class="form-control form-control-sm bg-light" id="from_bank" name="from_bank" value="<?= htmlspecialchars($pf_from_bank) ?>" readonly>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="from_bank_curr"><b>From Bank Currency <i style="color: red;">*</i></b></label>
                            <input type="text" class="form-control form-control-sm bg-light" id="from_bank_curr" name="from_bank_curr" value="<?= htmlspecialchars($pf_from_curr) ?>" readonly>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="nama_supp"><b>Supplier</b></label>
                            <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" name="txt_supp" id="txt_supp" value="<?= htmlspecialchars($pf_supp) ?>">
                            <input type="hidden" name="bpbvalue" id="bpbvalue" value="">
                        </div>

                        <!-- Filter BPB Date untuk menambah BPB (mirror create): cari BPB
                             tersedia utk supplier ini pada rentang tanggal, lalu ceklis. -->
                        <div class="col-md-3 mb-3">
                            <label><b>BPB Date</b> <small class="text-muted">(tambah BPB)</small></label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control form-control-sm tanggal_fil" id="bpb_start" name="bpb_start" value="<?= date('d-m-Y', strtotime($pf_tgl_kbon)) ?>" placeholder="Awal" autocomplete="off">
                                <span class="mx-1">-</span>
                                <input type="text" class="form-control form-control-sm tanggal_fil" id="bpb_end" name="bpb_end" value="<?= date('d-m-Y') ?>" placeholder="Akhir" autocomplete="off">
                                <button type="button" class="btn btn-primary btn-sm ml-2" id="btnCariBpb" style="white-space:nowrap;"><i class="fas fa-search"></i> Cari BPB</button>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
            </div>
    </form>

<form id="form-simpan">
    <div class="card shadow-sm mb-4">
                <!-- <div class="card-header" style="background-color: #60A5FA; color: white; font-weight: bold;">
                    Data FTR
                </div> -->
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                            <thead>
                                <tr class="text-white" style="background-color: #1E3A8A;">
                                    <th style="width:6%;">Cek</th>
                                    <th style="width:18%;">NO BPB</th>
                                    <th style="width:15%;">NO PO</th>                            
                                    <th style="width:13%;">BPB Date</th>                            
                                    <th style="width:15%;">SubTotal</th>
                                    <th style="width:12%;">Tax (PPn)</th>
                                    <th style="width:12%;">Tax (PPh)</th>                            
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

                                $sql = mysqli_query($conn2,"select a.no_bpb, no_po, tgl_bpb, subtotal, a.idtax, IFNULL(percentage,0) percentage, IFNULL(kriteria,'Non PPH') kriteria, tax, pph_code, pph_value, total, dp_value, confirm1, confirm2, nama_supp, tgl_po, tgl_tempo, curr, mattype, matclass, n_code_category, cus_ctg, a.no_faktur no_faktur_bpb, b.upt_tgl_faktur tgl_faktur_bpb from ap_edit_kontrabon a INNER JOIN (select no_bpb, confirm1, confirm2, MAX(upt_tgl_faktur) upt_tgl_faktur from bpb_new where status != 'Cancel' GROUP BY no_bpb) b on b.no_bpb = a.no_bpb INNER JOIN (select * from (select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc
                                    UNION
                                    select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from ap_journal_temp a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc) a GROUP BY reff_doc) c on c.reff_doc = a.no_bpb left join mtax d on d.idtax = a.idtax where a.no_kbon = '$no_kbon' and a.status != 'Cancel'");


                                while($row = mysqli_fetch_array($sql)){
                                    // $bpb = $row['no_bpb'];
                                    // $id_supplier = $row['id_supplier'];
                                    // $pono = isset($row['pono']) ? $row['pono'] : null;

                                   $tgl_fk_bpb = (!empty($row['tgl_faktur_bpb']) && $row['tgl_faktur_bpb'] != '0000-00-00') ? date('Y-m-d', strtotime($row['tgl_faktur_bpb'])) : '';
                                   echo '<tr data-nofaktur="'.htmlspecialchars($row['no_faktur_bpb'], ENT_QUOTES).'" data-tglfaktur="'.htmlspecialchars($tgl_fk_bpb, ENT_QUOTES).'">
                                   <td style="width:10px;"><input type="checkbox" class="select_edit" name="select_edit[]" value="" checked></td>
                                   <td value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                   <td value="'.$row['no_po'].'">'.$row['no_po'].'</td>                            
                                   <td dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                   <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$row['subtotal'].'">'.number_format($row['subtotal'],2).'</td>
                                   <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$row['tax'].'">'.number_format($row['tax'],2).'</td>
                                   <td style="width:120px;">
                                   <select name="combo_pph_edit" class="combo_pph_edit form-control form-control-sm" style="width:100%;min-width:110px;">
                                   <option data-idtax="'.$row['pph_code'].'" value="'.$row['percentage'].'">'.$row['kriteria'].'</option>';
                                   if ($row['pph_code'] != 0) {
                                       echo '<option data-idtax="0" value="0">Non PPH</option>';
                                   }

                                   $sql_tax = mysqli_query(
                                    $conn2,
                                    "SELECT idtax, kriteria, percentage 
                                    FROM mtax 
                                    WHERE category_tax = 'PPH' 
                                    AND idtax != '".$row['idtax']."'"
                                );

                                   while ($tax = mysqli_fetch_assoc($sql_tax)) {
                                    echo '<option data-idtax="'.$tax['idtax'].'" value="'.$tax['percentage'].'">'.$tax['kriteria'].'</option>';
                                }

                                echo '  </select>
                                </td>                        
                                <td class="dt_total" style="width:100px;text-align:right;" data-total="'.$row['total'].'">'.number_format($row['total'],2).'</td>
                                <td class="dt_tax" style="width:100px;text-align:right;display: none;" data="'.$row['dp_value'].'">'.number_format($row['dp_value'],2).'</td>
                                <td style="display: none;" value="'.$row['confirm1'].'">'.$row['confirm1'].'</td> 
                                <td style="display: none;" value="'.$row['confirm2'].'">'.$row['confirm2'].'</td>
                                <td style="display: none;" value="'.$row['nama_supp'].'">'.$row['nama_supp'].'</td>
                                <td style="display: none;" value="'.$row['tgl_po'].'">'.date("d-M-Y",strtotime($row['tgl_po'])).'</td>    
                                <td style="display: none;" value="'.$row['tgl_tempo'].'">'.$row['tgl_tempo'].'</td> 
                                <td style="display: none;" value="'.$row['curr'].'">'.$row['curr'].'</td>
                                <td style="display: none;" value="'.$row['mattype'].'">'.$row['mattype'].'</td> 
                                <td style="display: none;" value="'.$row['matclass'].'">'.$row['matclass'].'</td> 
                                <td style="display: none;" value="'.$row['n_code_category'].'">'.$row['n_code_category'].'</td>              
                                <td style="display: none;" value="'.$row['cus_ctg'].'">'.$row['cus_ctg'].'</td> 
                                </tr>';
                            }  

                            ?>
                        </tbody>                    
                    </table>   
                </div>
                <div class="form-row col mt-3">
                    <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total BPB</u></b></label>
                    <div class="col-md-2 mb-3">

                        <?php
                        $sql = mysqli_query($conn2,"select sum(subtotal) subtotal from ap_edit_kontrabon where no_kbon = '$no_kbon'");
                        $row = mysqli_fetch_array($sql);                         
                        $subtotal = $row['subtotal'];
                        ?>                              

                        <input type="text" class="form-control form-control-sm" name="subtotal" id="subtotal" value="<?= number_format($subtotal,2); ?>" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                        <input type="hidden" name="subtotal_h" id="subtotal_h" value="<?= $subtotal; ?>">
                        <input type="hidden" name="subtotal_h1" id="subtotal_h1" value="<?= $subtotal; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mb-4">
                <!-- <div class="card-header" style="background-color: #60A5FA; color: white; font-weight: bold;">
                    Data FTR
                </div> -->
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
                            <thead>
                                <tr class="text-white" style="background-color: #2563EB;">
                                    <th style="display: none;">Cek</th>
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

                                $querys = mysqli_query($conn2,"select a.no_ro, no_bppb, tgl_bppb, no_bpb, total_ro, curr, mattype, matclass, n_code_category, cus_ctg, b.upt_no_faktur, b.upt_tgl_faktur from ap_edit_return_kb a INNER JOIN (select no_bppb, no_ro, tgl_bppb, no_bpb, curr, MAX(upt_no_faktur) upt_no_faktur, MAX(upt_tgl_faktur) upt_tgl_faktur from bppb_new GROUP BY no_bppb) b on b.no_bppb = a.no_bpbrtn LEFT JOIN (select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from tbl_list_journal a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc
                                 UNION
                                 select reff_doc, a.no_coa, mattype, matclass, n_code_category, cus_ctg from ap_journal_temp a INNER JOIN mastercoa_v2 b on b.no_coa = a.no_coa where no_journal = '$no_kbon' and a.nama_coa like '%GR/IR%' and type_journal = 'AP - Kontrabon' GROUP BY reff_doc) c on c.reff_doc = b.no_bppb where no_kbon = '$no_kbon'");

                                while($row1 = mysqli_fetch_array($querys)){
                                    // Prefill No/Tgl Faktur RO dari bppb_new.upt_* (strip-guard) saat edit.
                                    $tgl_fk_ro = (!empty($row1['upt_tgl_faktur']) && $row1['upt_tgl_faktur'] != '0000-00-00') ? date('Y-m-d', strtotime($row1['upt_tgl_faktur'])) : '';
                                    echo '<tr data-nofaktur="'.htmlspecialchars($row1['upt_no_faktur'] ?? '', ENT_QUOTES).'" data-tglfaktur="'.htmlspecialchars($tgl_fk_ro, ENT_QUOTES).'">
                                    <td style="width:10px;" hidden><input type="checkbox" class="chkB" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                        
                                    <td style="width:50px;" data-ro="'.$row1['no_ro'].'">'.$row1['no_ro'].'</td>
                                    <td style="width:50px;" valuess="'.$row1['no_bppb'].'">'.$row1['no_bppb'].'</td>
                                    <td style="width:100px;" valuess="'.$row1['tgl_bppb'].'">'.date("d-M-Y",strtotime($row1['tgl_bppb'])).'</td>                            
                                    <td style="width:50px;" valuess="'.$row1['no_bpb'].'">'.$row1['no_bpb'].'</td>                            
                                    <td style="width:100px;text-align:right;" data-total-ro="'.round($row1['total_ro'],2).'">'.number_format($row1['total_ro'],2).'</td>
                                    <td style="width:100px;">
                                    <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="txt_amount" name="txt_amount" value="'.round($row1['total_ro'],2).'" disabled>
                                    </td>
                                    <td style="display: none;" valuess="'.$row1['curr'].'">'.$row1['curr'].'</td>     
                                    <td style="display: none;" valuess="'.$row1['mattype'].'">'.$row1['mattype'].'</td> 
                                    <td style="display: none;" valuess="'.$row1['matclass'].'">'.$row1['matclass'].'</td> 
                                    <td style="display: none;" valuess="'.$row1['n_code_category'].'">'.$row1['n_code_category'].'</td>              
                                    <td style="display: none;" valuess="'.$row1['cus_ctg'].'">'.$row1['cus_ctg'].'</td>                                                                                           
                                    </tr>';
                                }
                                ?>
                            </tbody>                    
                        </table> 
                    </div>
                    <div class="form-row col mt-3">
                        <label for="potongan" class="col-form-label" style="width: 150px;font-size: 13px;;"><b><u>Total Return</u></b></label>
                        <div class="col-md-2 mb-3">    
                         <?php
                         $sql = mysqli_query($conn2,"select sum(total_ro) total_ro from ap_edit_return_kb where no_kbon = '$no_kbon'");
                         $row = mysqli_fetch_array($sql);                         
                         $total_ro = $row['total_ro'];
                         ?>                                  
                         <input type="text" class="form-control form-control-sm" name="potongan" id="potongan" value="<?= number_format($total_ro,2); ?>" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                         <input type="hidden" name="potongan_h" id="potongan_h" value="<?= $total_ro; ?>">
                         <input type="hidden" name="h_mattype" id="h_mattype" value="">
                         <input type="hidden" name="h_matclass" id="h_matclass" value="">
                         <input type="hidden" name="h_code_ctg" id="h_code_ctg" value="">
                         <input type="hidden" name="h_cus_ctg" id="h_cus_ctg" value="">
                     </div>
                </div>
            </div>
        </div>


    <div class="card shadow-sm mb-4" >
                <!-- <div class="card-header" style="background-color: #60A5FA; color: white; font-weight: bold;">
                    Data FTR
                </div> -->
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
                            <thead>
                                <tr style="background-color: #60A5FA; color: white;">
                                    <th style="display: none;">Cek</th>
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

                                $query_ftr = mysqli_query($conn2,"select no_ftr, no_po, tgl_po, no_pi, total_ftr, curr, '' no_kbon, '' tgl_kbon, '' no_payment, '' tgl_payment, no_pv, no_bankout, tgl_bankout, no_coa from ap_edit_kontrabon_ftr where no_kbon = '$no_kbon'");


                                while($row_ftr = mysqli_fetch_array($query_ftr)){
                                    echo '<tr>
                                    <td style="display: none;"><input type="checkbox" class="chkC" id="select" name="select[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";? checked></td>                        
                                    <td style="width:50px;" no-ftr="'.$row_ftr['no_ftr'].'">'.$row_ftr['no_ftr'].'</td>
                                    <td style="width:50px;" po-ftr="'.$row_ftr['no_po'].'">'.$row_ftr['no_po'].'</td>
                                    <td style="width:100px;" tglpo-ftr="'.$row_ftr['tgl_po'].'">'.date("d-M-Y",strtotime($row_ftr['tgl_po'])).'</td>                            
                                    <td style="width:50px;" pi-ftr="'.$row_ftr['no_pi'].'">'.$row_ftr['no_pi'].'</td>                            
                                    <td style="width:100px;text-align:right;" total-ftr="'.round($row_ftr['total_ftr'],2).'">'.number_format($row_ftr['total_ftr'],2).'</td>
                                    <td style="width:100px;">
                                    <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="amount_ftr" name="amount_ftr" value="'.round($row_ftr['total_ftr'],2).'" disabled>
                                    </td>
                                    <td style="display: none;" curr-ftr="'.$row_ftr['curr'].'">'.$row_ftr['curr'].'</td>
                                    <td style="display: none;" kbon-ftr="'.$row_ftr['no_kbon'].'">'.$row_ftr['no_kbon'].'</td>
                                    <td style="display: none;" tglkbon-ftr="'.$row_ftr['tgl_kbon'].'">'.$row_ftr['tgl_kbon'].'</td>
                                    <td style="display: none;" lp-ftr="'.$row_ftr['no_payment'].'">'.$row_ftr['no_payment'].'</td>
                                    <td style="display: none;" tgllp-ftr="'.$row_ftr['tgl_payment'].'">'.$row_ftr['tgl_payment'].'</td>
                                    <td style="display: none;" pv-ftr="'.$row_ftr['no_pv'].'">'.$row_ftr['no_pv'].'</td>
                                    <td style="display: none;" bankout-ftr="'.$row_ftr['no_bankout'].'">'.$row_ftr['no_bankout'].'</td>
                                    <td style="display: none;" bankoutdate-ftr="'.$row_ftr['tgl_bankout'].'">'.$row_ftr['tgl_bankout'].'</td>
                                    <td style="display: none;" coa-ftr="'.$row_ftr['no_coa'].'">'.$row_ftr['no_coa'].'</td>
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
                            <?php
                            $sql = mysqli_query($conn2,"select sum(total_ftr) total_ftr from ap_edit_kontrabon_ftr where no_kbon = '$no_kbon'");
                            $row = mysqli_fetch_array($sql);                         
                            $total_ftr = $row['total_ftr'];
                            ?>                                
                            <input type="text" class="form-control form-control-sm" name="ttl_dp" id="ttl_dp" value="<?= number_format($total_ftr,2); ?>" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                            <input type="hidden" name="ttl_dp_h" id="ttl_dp_h" value="<?= $total_ftr; ?>">

                        </div>
                </div>
            </div>

        <div class="card shadow-sm mb-4" style="margin-left:2rem;margin-right:2rem;">
          <div class="card-body p-2">
            <div class="row">
              <div class="col-md-7 border-end pe-4">

                <?php
                // FIX: query lama menulis "select select ..." (typo) -> syntax error,
                // sehingga breakdown potongan selalu ter-load 0 saat edit. Sekaligus
                // tambah potongan_ppn/potongan_pph (Koreksi PPN/PPh) agar sejajar create.
                $sql = mysqli_query($conn2,"select jml_return, lr_kurs, s_qty, s_harga, materai, pot_beli, ekspedisi, moq, jml_potong, potongan_ppn, potongan_pph from ap_edit_potongan where no_kbon = '$no_kbon'");
                $row = mysqli_fetch_array($sql);
                $jml_return = $row['jml_return'];
                $lr_kurs = $row['lr_kurs'];
                $s_qty = $row['s_qty'];
                $s_harga = $row['s_harga'];
                $materai = $row['materai'];
                $pot_beli = $row['pot_beli'];
                $ekspedisi = $row['ekspedisi'];
                $moq = $row['moq'];
                $jml_potong = $row['jml_potong'];
                // Fallback ke nilai LIVE bila snapshot lama belum punya kolom ini.
                $potongan_ppn = isset($row['potongan_ppn']) && $row['potongan_ppn'] !== null ? $row['potongan_ppn'] : $pf_pot_ppn;
                $potongan_pph = isset($row['potongan_pph']) && $row['potongan_pph'] !== null ? $row['potongan_pph'] : $pf_pot_pph;
                ?>

                <div class="row mb-2 align-items-center">
                  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Laba Rugi Kurs</u></b></label>
                  <div class="col-3">
                    <input type="number" class="form-control form-control-sm text-right" name="labarugi" id="labarugi" value="<?= $lr_kurs; ?>" placeholder="0.00">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm text-right" name="labarugi_h" id="labarugi_h" value="<?= number_format($lr_kurs,2); ?>" placeholder="0.00" readonly>
                </div>
                <div class="col-2"></div>
            </div>

            <div class="row mb-2 align-items-center">
              <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Quantity</u></b></label>
              <div class="col-3">
                <input type="number" class="form-control form-control-sm text-right" name="selisihqty" id="selisihqty" value="<?= $s_qty; ?>" placeholder="0.00">
            </div>
            <div class="col-4">
                <input type="text" class="form-control form-control-sm text-right" name="selisihqty_h" id="selisihqty_h" value="<?= number_format($s_qty,2); ?>" placeholder="0.00" readonly>
            </div>
            <div class="col-2"></div>
        </div>

        <div class="row mb-2 align-items-center">
          <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Harga</u></b></label>
          <div class="col-3">
            <input type="number" class="form-control form-control-sm text-right" name="selisihharga" id="selisihharga" value="<?= $s_harga; ?>" placeholder="0.00">
        </div>
        <div class="col-4">
            <input type="text" class="form-control form-control-sm text-right" name="selisihharga_h" id="selisihharga_h" value="<?= number_format($s_harga,2); ?>" placeholder="0.00" readonly>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Materai</u></b></label>
      <div class="col-3">
        <input type="number" min="0" class="form-control form-control-sm text-right" name="materai" id="materai" value="<?= $materai; ?>" placeholder="0.00">
    </div>
    <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="materai_h" id="materai_h" value="<?= number_format($materai,2); ?>" placeholder="0.00" readonly>
    </div>
    <div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Potongan Pembelian</u></b></label>
  <div class="col-3">
    <input type="number" max="0" class="form-control form-control-sm text-right" name="potongbeli" id="potongbeli" value="<?= $pot_beli; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongbeli_h" id="potongbeli_h" value="<?= number_format($pot_beli,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Expedisi</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="ekspedisi" id="ekspedisi" value="<?= $ekspedisi; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="ekspedisi_h" id="ekspedisi_h" value="<?= number_format($ekspedisi,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya MOQ</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="moq" id="moq" value="<?= $moq; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="moq_h" id="moq_h" value="<?= number_format($moq,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Koreksi PPN</u></b></label>
  <div class="col-3">
    <input type="number" class="form-control form-control-sm text-right" name="potongan_ppn" id="potongan_ppn" value="<?= $potongan_ppn; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongan_ppn_h" id="potongan_ppn_h" value="<?= number_format($potongan_ppn,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Koreksi PPh</u></b></label>
  <div class="col-3">
    <input type="number" class="form-control form-control-sm text-right" name="potongan_pph" id="potongan_pph" value="<?= $potongan_pph; ?>" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongan_pph_h" id="potongan_pph_h" value="<?= number_format($potongan_pph,2); ?>" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Jumlah Potongan</u></b></label>
  <div class="col-7">
    <input type="text" class="form-control form-control-sm text-right" name="jumlahpotong" id="jumlahpotong" value="<?= number_format($jml_potong,2); ?>" placeholder="0.00" readonly>
    <input type="hidden" name="jml_potong" id="jml_potong" value="<?= $jml_potong; ?>">
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

    <?php
    $sql = mysqli_query($conn2,"select sum(tax) tax_kbon, sum(pph_value) pph_kbon from ap_edit_kontrabon where no_kbon = '$no_kbon'");
    $row = mysqli_fetch_array($sql);                         
    $tax_kbon = $row['tax_kbon'];
    $pph_kbon = $row['pph_kbon'];
    ?>

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPn)</u></b></label>
      <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="pajak" id="pajak" value="<?= number_format($tax_kbon,2); ?>" placeholder="0.00" readonly>
        <input type="hidden" name="pajak_h" id="pajak_h" value="<?= $tax_kbon; ?>">
    </div>
    <div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPh)</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pph" id="pph" value="<?= number_format($pph_kbon,2); ?>" placeholder="0.00" readonly>
    <input type="hidden" name="pph_h" id="pph_h" value="<?= $pph_kbon; ?>">
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
    <button type="button" class="btn btn-dark btn-sm w-100" id="calculate">
      <span class="fa fa-calculator"></span> Calculate
  </button>
</div>
<div class="col-2"></div>
</div>

<div class="row mt-3">
  <div class="col">
    <button type="button" class="btn btn-primary btn-sm me-2" id="simpan">
      <span class="fa fa-floppy-o"></span> Save
  </button>
  <button type="button" class="btn btn-danger btn-sm" id="batal">
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



<!-- Modal Cari BPB -->
<div class="modal fade" id="modalCariBPB" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-xl rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data BPB</h4>
    </div>
    <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nama_supp_e1"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp_e1" id="nama_supp_e1" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_e1']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="profit_center_e1"><b>Profit Center</b></label>
            <input type="text" class="form-control form-control-sm mr-2" id="profitcenter_e1" name="profitcenter_e1" 
            value="" readonly>
            <input type="hidden" class="form-control form-control-sm mr-2" id="no_kbon_e1" name="no_kbon_e1" 
            value="" >
            <input type="hidden" class="form-control form-control-sm mr-2" id="profit_center_e1" name="profit_center_e1" 
            value="" >
        </div>

        <div class="col-md-6">
         <div class="form-group">
            <label><b>BPB Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm tanggal_fil mr-2" id="start_date_e1" name="start_date_e1" 
              value="<?php
              if(!empty($_POST['start_date_e1'])) {
                echo $_POST['start_date_e1'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control form-control-sm tanggal_fil" id="end_date_e1" name="end_date_e1" 
                value="<?php
                if(!empty($_POST['end_date_e1'])) {
                    echo $_POST['end_date_e1'];
                    } else {
                        echo date("d-m-Y");
                    } ?>" 
                    placeholder="Tanggal Akhir">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button class="btn btn-primary btn-xs w-100" id="btnSearchBPB">
              <i class="fas fa-search"></i> Search
          </button>
      </div>
  </div>

  <div class="table-responsive">
    <table id="mytable_edit" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
        <thead class="bg-secondary text-white">
          <tr>
            <th style="width:6%;">Cek</th>
            <th style="width:18%;">NO BPB</th>
            <th style="width:15%;">NO PO</th>                            
            <th style="width:13%;">BPB Date</th>                            
            <th style="width:15%;">SubTotal</th>
            <th style="width:12%;">Tax (PPn)</th>
            <th style="width:12%;">Tax (PPh)</th>                            
            <th style="width:15%;">Total (BPB)</th>
            <th style="display: none;"></th>
            <th style="display: none;"></th>
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
    </tbody>
</table>
</div>

<div class="form-row col mt-3">
    <label for="subtotal_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total BPB</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm" name="subtotal_edit" id="subtotal_edit" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="subtotal_h_edit" id="subtotal_h_edit" value="">
        <input type="hidden" name="subtotal_h1_edit" id="subtotal_h1_edit" value="">
    </div>
</div>

<div class="form-row col mt-0">
    <label for="pph_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total PPH</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm text-right" name="pph_edit" id="pph_edit" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="pph_h_edit" id="pph_h_edit">
    </div>
</div>

</div>
<div class="modal-footer">
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-success btn-sm" id="btnSaveBPB"><i class="fas fa-save"></i> Save</button>
</div>
</div>
</div>
</div>




<!-- Modal Cari BPB -->
<div class="modal fade" id="modalCariBPPB" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-xl rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data Return</h4>
    </div>
    <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nama_supp_e2"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp_e2" id="nama_supp_e2" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_e2']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="profit_center_e2"><b>Profit Center</b></label>
            <input type="text" class="form-control form-control-sm mr-2" id="profitcenter_e2" name="profitcenter_e2" 
            value="" readonly>
            <input type="hidden" class="form-control form-control-sm mr-2" id="no_kbon_e2" name="no_kbon_e2" 
            value="" >
            <input type="hidden" class="form-control form-control-sm mr-2" id="profit_center_e2" name="profit_center_e2" 
            value="" >
        </div>

        <div class="col-md-6">
         <div class="form-group">
            <label><b>BPPB Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm tanggal_fil mr-2" id="start_date_e2" name="start_date_e2" 
              value="<?php
              if(!empty($_POST['start_date_e2'])) {
                echo $_POST['start_date_e2'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control form-control-sm tanggal_fil" id="end_date_e2" name="end_date_e2" 
                value="<?php
                if(!empty($_POST['end_date_e2'])) {
                    echo $_POST['end_date_e2'];
                    } else {
                        echo date("d-m-Y");
                    } ?>" 
                    placeholder="Tanggal Akhir">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button class="btn btn-primary btn-xs w-100" id="btnSearchBPPB">
              <i class="fas fa-search"></i> Search
          </button>
      </div>
  </div>

  <div class="table-responsive">
    <table id="mytable1_edit" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
        <thead class="bg-secondary text-white">
          <tr>
            <th style="width:6%;">Cek</th>
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
    </tbody>
</table>
</div>

<div class="form-row col mt-3">
    <label for="potongan_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total Return</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm" name="potongan_edit" id="potongan_edit" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="potongan_h_edit" id="potongan_h_edit" value="">
    </div>
</div>

</div>
<div class="modal-footer">
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-success btn-sm" id="btnSaveBPPB"><i class="fas fa-save"></i> Save</button>
</div>
</div>
</div>
</div>



<div class="modal fade" id="modalCariFTR" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-xl rounded-3">

      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="Heading">Add Data FTR</h4>
    </div>
    <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nama_supp_e3"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp_e3" id="nama_supp_e3" data-dropup-auto="false" data-live-search="true">
                <option value="" disabled selected="true">select</option>                
                <?php 
                $sql = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'S' order by Supplier ASC");
                while ($row = mysqli_fetch_array($sql)) {
                    $data = $row['Supplier'];
                    if($row['Supplier'] == $_POST['nama_supp_e3']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="profit_center_e3"><b>Profit Center</b></label>
            <input type="text" class="form-control form-control-sm mr-2" id="profitcenter_e3" name="profitcenter_e3" 
            value="" readonly>
            <input type="hidden" class="form-control form-control-sm mr-2" id="no_kbon_e3" name="no_kbon_e3" 
            value="" >
            <input type="hidden" class="form-control form-control-sm mr-2" id="profit_center_e3" name="profit_center_e3" 
            value="" >
        </div>

        <div class="col-md-6">
         <div class="form-group">
            <label><b>FTR Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control form-control-sm tanggal_fil mr-2" id="start_date_e3" name="start_date_e3" 
              value="<?php
              if(!empty($_POST['start_date_e3'])) {
                echo $_POST['start_date_e3'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control form-control-sm tanggal_fil" id="end_date_e3" name="end_date_e3" 
                value="<?php
                if(!empty($_POST['end_date_e3'])) {
                    echo $_POST['end_date_e3'];
                    } else {
                        echo date("d-m-Y");
                    } ?>" 
                    placeholder="Tanggal Akhir">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button class="btn btn-primary btn-xs w-100" id="btnSearchFTR">
              <i class="fas fa-search"></i> Search
          </button>
      </div>
  </div>

  <div class="table-responsive">
    <table id="mytable2_edit" class="table table-striped table-bordered text-nowrap" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
        <thead class="bg-secondary text-white">
          <tr>
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
    </tbody>
</table>
</div>

<div class="form-row col mt-3">
    <label for="ttl_dp_edit" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total FTR</u></b></label>
    <div class="col-md-4 mb-3">                              
        <input type="text" class="form-control form-control-sm" name="ttl_dp_edit" id="ttl_dp_edit" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
        <input type="hidden" name="ttl_dp_h_edit" id="ttl_dp_h_edit" value="">
    </div>
</div>

</div>
<div class="modal-footer">
    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-success btn-sm" id="btnSaveFTR"><i class="fas fa-save"></i> Save</button>
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

</div><!-- body-row END -->
</div>
</div>

<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
// Auto-fill display fields (Bank Name/Beneficiary/Currency & From Bank) saat pilihan
// diubah — mirror create pv_regular.php. Ditaruh SETELAH jQuery dimuat (bukan di tengah
// body) supaya tidak "$ is not defined".
$(function () {
    $('#sel_bank_account').on('change', function () {
        var o = $(this).find('option:selected');
        $('#disp_bankname').val(o.data('bankname') || '');
        $('#disp_beneficiary').val(o.data('beneficiary') || '');
        $('#disp_currency').val(o.data('currency') || '');
    });
    $('#from_account').on('change', function () {
        var o = $(this).find('option:selected');
        $('#from_bank').val(o.data('bank') || '');
        $('#from_bank_curr').val(o.data('currency') || '');
    });
});
</script>



<script>

    // document.addEventListener("DOMContentLoaded", function() {
    //     let savedPc = localStorage.getItem("profit_center");
    //     if (savedPc) {
    //         document.getElementById("profit_center").value = savedPc;
    //         updateNoKontraBon();
    //     }
    //     let tanggal = document.getElementById('tanggal').value;
    //     ubahtanggal(tanggal);
    // });

    // document.getElementById("profit_center").addEventListener("change", function() {
    //     localStorage.setItem("profit_center", this.value);
    //     document.querySelector("#mytable tbody").innerHTML = "";
    //     document.querySelector("#mytable1 tbody").innerHTML = "";
    //     document.querySelector("#mytable2 tbody").innerHTML = "";
    // });

    function updateNoKontraBon() {
        let pc = document.getElementById("profit_center").value;  
        let input = document.getElementById("nokontrabon");  
        let current = input.value.trim();  

        let parts = current.split("/");

        console.log(parts[0] + ' ' + parts[1] + ' ' + parts[2] + ' ' + parts[3] + ' ' + parts[4] + ' ' + parts[5]);

    // kalau masih 5 bagian (belum ada PC)
    if (parts.length === 5) {
        // [0]=SI, [1]=APR, [2]=YYYY, [3]=MM, [4]=00025
        input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[2] + "/" + parts[3] + "/" + parts[4];
    } 
    // kalau sudah 6 bagian (sudah ada PC → replace)
    else if (parts.length === 6) {
        // [0]=SI, [1]=APR, [2]=PC lama, [3]=YYYY, [4]=MM, [5]=00025
        input.value = parts[0] + "/" + parts[1] + "/" + pc + "/" + parts[3] + "/" + parts[4] + "/" + parts[5];
    }

}
</script>
<script>

    $(document).on("click", "#edit_header", function () {
        let nokontrabon     = $("#nokontrabon").val();
        let tanggal         = $("#tanggal").val();
        let profit_center   = $("#profit_center").val();
        let txt_inv         = $("#txt_inv").val();
        let txt_tglsi       = $("#txt_tglsi").val();
        let no_faktur       = $("#no_faktur").val();
        let txt_tgltempo    = $("#txt_tgltempo").val();

        if (nokontrabon === "" || profit_center === "" || txt_tglsi === "" || txt_tgltempo === "" || tanggal === "" || txt_inv === "" || no_faktur === "") {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Some required fields are missing. Please complete all fields before proceeding."
            });
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "The header data will be updated.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, edit it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                url: "update_kontrabon_header.php", // ganti sesuai file PHP kamu
                data: {
                    nokontrabon: nokontrabon,
                    tanggal: tanggal,
                    profit_center: profit_center,
                    txt_inv: txt_inv,
                    txt_tglsi: txt_tglsi,
                    no_faktur: no_faktur,
                    txt_tgltempo: txt_tgltempo
                },
                success: function (res) {
                    if (res.trim() === "OK") {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Header has been successfully updated!"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire("Error", res, "error");
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Failed to update header: " + xhr.responseText
                    });
                }
            });
            }
        });
    });


    $(document).ready(function() {
        $("#mysupp").on("click", function() {
            let profit_center = $('select[name=profit_center] option').filter(':selected').val();
            let tanggal = document.getElementById('tanggal').value;

            if(profit_center == ""){
                alert("Please Input Profit Center.");
                $("#profit_center").focus();
                return;
            }

            $('#h_profit_center').val(profit_center);
            $('#tanggal_kbn').val(tanggal);

            $("#mymodal").modal("show");
        });
    });

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
        $("[data-toggle=tooltip]").tooltip();

    } );

    $(document).ready(function () {
        // #mytable dibiarkan tabel biasa (bukan DataTable) supaya alur ceklis/append/
        // serialize BPB sama seperti create (create pun #mytable-nya plain).
        // ===== Kolom No Faktur / Tgl Faktur per BPB (mirror create). Ditambah di UJUNG
        // baris supaya index td:eq(...) yang dipakai serialize tidak bergeser.
        // PENTING: kolom faktur pada #mytable1 HARUS di-append SEBELUM DataTable init-nya,
        // kalau tidak DataTables tak mengenali 2 kolom tambahan -> header jadi ngaco. =====
        function pvAppendFakturHead() {
            var $head = $('#mytable thead tr');
            if ($head.find('th.fk-head').length === 0) {
                $head.append('<th class="fk-head" style="min-width:150px;">No Faktur</th><th class="fk-head" style="min-width:120px;">Tgl Faktur</th>');
            }
        }
        window.pvAppendFakturCells = function($rows, defFaktur) {
            $rows.each(function(){
                var $r = $(this);
                if ($r.find('td.fk-cell').length > 0) return;   // sudah ada
                if ($r.find('td').length < 2) return;           // lewati baris "No data"
                // Prefill No Faktur per-BPB dari data-nofaktur baris (kalau ada), baru
                // fallback ke default (header). Baris baru dari Cari BPB: kosong.
                var nf = ($r.attr('data-nofaktur') || defFaktur || '').replace(/"/g, '&quot;');
                var tf = ($r.attr('data-tglfaktur') || '').replace(/"/g, '&quot;');  // dari bpb_new.upt_tgl_faktur
                $r.append(
                    '<td class="fk-cell"><input type="text" class="fk-no form-control form-control-sm" list="fkOptions" value="' + nf + '" placeholder="No Faktur" style="min-width:150px;font-size:12px;"></td>' +
                    '<td class="fk-cell"><input type="date" class="fk-tgl form-control form-control-sm" value="' + tf + '" style="min-width:150px;font-size:12px;"></td>'
                );
            });
        };
        pvAppendFakturHead();
        // Baris existing (BPB dokumen): prefill No Faktur dari no_faktur per-BPB (data-nofaktur),
        // fallback ke No Tax Invoice header.
        pvAppendFakturCells($('#mytable tbody tr'), $('#no_faktur').val() || '');

        // Kolom No Faktur/Tgl Faktur juga di tabel RETUR (#mytable1) -> disimpan ke bppb_new.
        (function () {
            var $h1 = $('#mytable1 thead tr');
            if ($h1.find('th.fk-head').length === 0) {
                $h1.append('<th class="fk-head" style="min-width:150px;">No Faktur</th><th class="fk-head" style="min-width:120px;">Tgl Faktur</th>');
            }
        })();
        pvAppendFakturCells($('#mytable1 tbody tr'), '');

        // DataTable init SETELAH kolom faktur ditambah (biar header tidak ngaco).
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

        // ===== Suggest & auto-adjust No Faktur (mirror create) =====
        // Datalist saran No Faktur diisi dari faktur milik IR terpilih (ajx_pv_ir_bpb.php).
        if (!document.getElementById('fkOptions')) { $('body').append('<datalist id="fkOptions"></datalist>'); }
        window.irFakturMap = window.irFakturMap || {};
        function pvLoadIrFaktur(ir) {
            ir = $.trim(ir || '');
            $('#fkOptions').empty(); window.irFakturMap = {};
            if (ir === '' || ir === '-') return;
            $.post('ajx_pv_ir_bpb.php', { ir_number: ir }, function (res) {
                if (!res || !res.ok) return;
                var _opts = '';
                (res.faktur_list || []).forEach(function (f) {
                    var n = String(f.no_faktur || '').trim();
                    if (n) { _opts += '<option value="' + n.replace(/"/g, '&quot;') + '"></option>'; window.irFakturMap[n] = f.tgl_faktur || ''; }
                });
                $('#fkOptions').html(_opts);
            }, 'json');
        }
        // Muat saran utk IR yang sedang terpilih + refresh saat IR diganti.
        pvLoadIrFaktur($('#ir_number').val());
        $('#ir_number').on('change', function () { pvLoadIrFaktur($(this).val()); });
        // Auto-isi Tgl Faktur saat No Faktur cocok salah satu faktur IR (tetap boleh diketik lain).
        $(document).on('input change', '#mytable input.fk-no, #mytable1 input.fk-no', function () {
            var v = $.trim($(this).val());
            if (window.irFakturMap && Object.prototype.hasOwnProperty.call(window.irFakturMap, v) && window.irFakturMap[v]) {
                var $tgl = $(this).closest('tr').find('.fk-tgl');
                if (!$tgl.prop('disabled')) { $tgl.val(window.irFakturMap[v]); }
            }
        });

        var tableBPB = $('#mytable_edit').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        var tableBPPB = $('#mytable1_edit').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        var tableFTR = $('#mytable2_edit').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });


        $('#modalCariBPB').on('shown.bs.modal', function () {
            tableBPB.columns.adjust().draw();
        });


        $('#modalCariBPPB').on('shown.bs.modal', function () {
            tableBPPB.columns.adjust().draw();
        });

        $('#modalCariFTR').on('shown.bs.modal', function () {
            tableFTR.columns.adjust().draw();
        });


        $(document).on('change', '.select_edit', function () {
            var sum_sub = 0;
            // Combo PPh dibiarkan selalu enabled supaya tampilan seragam (tidak greyed
            // saat un-check) — mengikuti tampilan create.
            $("input.select_edit[type=checkbox]:checked").each(function () {
                var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
                sum_sub += price;
            });

            $("#subtotal_edit").val(formatMoney(sum_sub));
            $("#subtotal_h_edit").val(sum_sub.toFixed(2));
            // $("#subtotal_h1_edit").val(data1.toFixed(2));
        });

        // Ceklis BPB di TABEL UTAMA (#mytable) -> langsung update Total BPB (#subtotal)
        // + Tax PPn (#pajak) dari baris yang terceklis (mirror create). Grand Total tetap
        // via tombol Calculate.
        $(document).on('change', '#mytable .select_edit', function () {
            var sub = 0, tax = 0;
            $('#mytable input.select_edit[type=checkbox]:checked').each(function () {
                var $tr = $(this).closest('tr');
                sub += parseFloat($tr.find('td:eq(4)').attr('data-subtotal'), 10) || 0;
                tax += parseFloat($tr.find('td:eq(5)').attr('data-tax'), 10) || 0;
            });
            $('#subtotal').val(formatMoney(sub));
            $('#subtotal_h').val(sub.toFixed(2));
            if ($('#pajak').length)   { $('#pajak').val(formatMoney(tax)); }
            if ($('#pajak_h').length) { $('#pajak_h').val(tax.toFixed(2)); }
        });


        $(document).on('change', '.select2_edit', function () {
            console.log('Checkbox berubah:', $(this).is(':checked'));
            var sum_sub = 0;

            $(this).closest('#mytable1_edit tr').find('td:eq(6) input').prop('disabled', true);       
            $("input[type=checkbox]:checked").each(function () {
                var select_amount = $(this).closest('#mytable1_edit tr').find('td:eq(6) input');
                select_amount.prop('disabled', false);
                var price = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro-edit'),10) || 0;
                sum_sub += price;
            });

            $("#potongan_edit").val(formatMoney(sum_sub));
            $("#potongan_h_edit").val(sum_sub.toFixed(2)); 
            // $("#subtotal_h1_edit").val(data1.toFixed(2)); 
        });


        $(document).on('change', '.select3_edit', function () {
            console.log('Checkbox berubah:', $(this).is(':checked'));
            var sum_sub = 0;

            $(this).closest('#mytable2_edit tr').find('td:eq(6) input').prop('disabled', true);       
            $("input[type=checkbox]:checked").each(function () {
                var select_amount = $(this).closest('#mytable2_edit tr').find('td:eq(6) input');
                select_amount.prop('disabled', false);
                var price = parseFloat($(this).closest('#mytable2_edit tr').find('td:eq(5)').attr('total-ftr'),10) || 0;
                sum_sub += price;
            });

            $("#ttl_dp_edit").val(formatMoney(sum_sub));
            $("#ttl_dp_h_edit").val(sum_sub.toFixed(2)); 
            // $("#subtotal_h1_edit").val(data1.toFixed(2)); 
        });


        $(document).on('input', '#mytable1_edit tbody input[name="txt_amount_edit"]', function () {
            var sum_amount = 0;
            var sum_total = 0;
            var sum_balance = 0;        
            $("input[type=checkbox]:checked").each(function () {        
                var amount = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(6) input').val(),10) || 0;
                var balance = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(5)').attr('data-total-ro-edit'),10) || 0;
                var select_amount = $(this).closest('#mytable1_edit tr').find('td:eq(6) input');                
                if(amount > balance){
                    select_amount.val(balance);
                    sum_amount += balance;
                    sum_total = sum_amount;
                }else{
                    sum_amount += amount;
                    sum_total = sum_amount;        
                }   
            });
            $("#potongan_edit").val(formatMoney(sum_total));
            $("#potongan_h_edit").val(sum_total.toFixed(4));
        });


        $(document).on('input', '#mytable2_edit tbody input[name="txt_amount_edit"]', function () {
            var sum_amount = 0;
            var sum_total = 0;
            var sum_balance = 0;        
            $("input[type=checkbox]:checked").each(function () {        
                var amount = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(6) input').val(),10) || 0;
                var balance = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(5)').attr('data-total-ro-edit'),10) || 0;
                var select_amount = $(this).closest('#mytable1_edit tr').find('td:eq(6) input');                
                if(amount > balance){
                    select_amount.val(balance);
                    sum_amount += balance;
                    sum_total = sum_amount;
                }else{
                    sum_amount += amount;
                    sum_total = sum_amount;        
                }   
            });
            $("#potongan_edit").val(formatMoney(sum_total));
            $("#potongan_h_edit").val(sum_total.toFixed(4));
        });


        $(document).on('change', '.combo_pph_edit', function() {
            var sum_pph = 0;
            $("input[type=checkbox]:checked").each(function () {        
                var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'), 10) || 0;
                var pph   = parseFloat($(this).closest('tr').find('td:eq(6) select.combo_pph_edit option:selected').val(), 10) || 0;            
                sum_pph  += price * (pph / 100);  
            });  

            $("#pph_edit").val(formatMoney(sum_pph));
            $("#pph_h_edit").val(sum_pph.toFixed(2));
        });

        $(document).on('click', '#edit_bpb', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit this data? Current data will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
            // buka modal cari BPB

            let no_kbon = $('#nokontrabon').val();
            let profitcenter = $('#profit_center option:selected').text();
            let profit_center = $('#profit_center').val();
            let nama_supp = $('#txt_supp').val();

            $('#no_kbon_e1').val(no_kbon);
            $('#profit_center_e1').val(profit_center);
            $('#profitcenter_e1').val(profitcenter);
            $('#nama_supp_e1').val(nama_supp);
            $('#nama_supp_e1').selectpicker('refresh');

            var modal = new bootstrap.Modal(document.getElementById('modalCariBPB'));
            modal.show();
        }
    })
        });


        $(document).on('click', '#edit_bppb', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit this data? Current data will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
            // buka modal cari BPB

            let no_kbon = $('#nokontrabon').val();
            let profitcenter = $('#profit_center option:selected').text();
            let profit_center = $('#profit_center').val();
            let nama_supp = $('#txt_supp').val();

            $('#no_kbon_e2').val(no_kbon);
            $('#profit_center_e2').val(profit_center);
            $('#profitcenter_e2').val(profitcenter);
            $('#nama_supp_e2').val(nama_supp);
            $('#nama_supp_e2').selectpicker('refresh');

            var modal = new bootstrap.Modal(document.getElementById('modalCariBPPB'));
            modal.show();
        }
    })
        });


        $(document).on('click', '#edit_ftr', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit this data? Current data will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit it!'
            }).then((result) => {
                if (result.isConfirmed) {
            // buka modal cari BPB

            let no_kbon = $('#nokontrabon').val();
            let profitcenter = $('#profit_center option:selected').text();
            let profit_center = $('#profit_center').val();
            let nama_supp = $('#txt_supp').val();

            $('#no_kbon_e3').val(no_kbon);
            $('#profit_center_e3').val(profit_center);
            $('#profitcenter_e3').val(profitcenter);
            $('#nama_supp_e3').val(nama_supp);
            $('#nama_supp_e3').selectpicker('refresh');

            var modal = new bootstrap.Modal(document.getElementById('modalCariFTR'));
            modal.show();
        }
    })
        });


        $('#btnSearchBPB').on('click', function() {
            let nama_supp = $('#nama_supp_e1').val();
            let start_date = $('#start_date_e1').val();
            let end_date = $('#end_date_e1').val();
            let no_kbon = $('#no_kbon_e1').val();
            let profit_center = $('#profit_center_e1').val();

            $.ajax({
                url: 'get_bpb_kontrabon.php',
                type: 'POST',
                data: {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center},
                success: function(res) {
                    console.log(res);
                    tableBPB.clear().rows.add($(res)).draw();
                }

            });
        });

        // ===== Cari BPB inline (mirror create): tambah BPB tersedia utk supplier ini pada
        // rentang tanggal ke tabel utama #mytable (un-checked). Dedup by no_bpb. =====
        $(document).on('click', '#btnCariBpb', function() {
            var nama_supp = $('#txt_supp').val();
            var start_date = $('#bpb_start').val();
            var end_date = $('#bpb_end').val();
            var no_kbon = $('#nokontrabon').val();
            var profit_center = $('#profit_center').val();
            if (!start_date || !end_date) { Swal.fire({icon:'warning', title:'Rentang tanggal BPB kosong'}); return; }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Mencari...');
            $.post('get_bpb_kontrabon.php', {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center}, function(res) {
                var existing = {};
                $('#mytable tbody tr').each(function(){ var nb = $(this).find('td:eq(1)').attr('value'); if (nb) existing[nb] = true; });
                var $rows = $('<table><tbody>' + res + '</tbody></table>').find('tbody > tr');
                var added = 0;
                $rows.each(function(){
                    var $r = $(this);
                    var nb = $r.find('td:eq(1)').attr('value');
                    if (!nb || existing[nb]) return;
                    $r.find('.select_edit').prop('checked', false);       // baris baru default belum diceklis
                    $r.find('td:eq(0)').css('display', '');                // pastikan kolom cek terlihat
                    $('#mytable tbody').append($r);
                    if (window.pvAppendFakturCells) window.pvAppendFakturCells($r, ''); // kolom faktur (kosong)
                    existing[nb] = true;
                    added++;
                });
                // ===== Muat RO/retur juga (agar "Cari BPB" memunculkan BPB DAN RO, seperti
                // create). RO ditambah ke #mytable1 (DataTable); disimpan saat Save utama
                // via insert_bppb_kontrabon_edit.php (full-replace ap_edit_return_kb +
                // jurnal RO ke ap_journal_temp). Baris RO baru default TER-CEKLIS. =====
                $.post('get_bppb_kontrabon.php', {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center}, function(resRo){
                    var dt1 = $('#mytable1').DataTable();
                    var exRo = {};
                    dt1.rows().every(function(){ var nb = $(this.node()).find('td:eq(2)').attr('valuess'); if (nb) exRo[nb] = true; });
                    var $roRows = $('<table><tbody>' + resRo + '</tbody></table>').find('tbody > tr');
                    var addedRo = 0;
                    $roRows.each(function(){
                        var $r = $(this);
                        var nb = $r.find('td:eq(2)').attr('valuess');
                        if (!nb || exRo[nb]) return;
                        // Samakan ke struktur #mytable1 (main): checkbox .chkB terlihat + checked,
                        // atribut data-total-ro, lalu tambah 2 kolom faktur (kosong).
                        $r.find('td:eq(0)').removeAttr('hidden').css('display', '').html('<input type="checkbox" class="chkB" name="select[]" checked>');
                        var $t5 = $r.find('td:eq(5)'); if (!$t5.attr('data-total-ro')) { $t5.attr('data-total-ro', $t5.attr('data-total-ro-edit') || ''); }
                        $r.append('<td class="fk-cell"><input type="text" class="fk-no form-control form-control-sm" list="fkOptions" placeholder="No Faktur" style="min-width:150px;font-size:12px;"></td>' +
                                  '<td class="fk-cell"><input type="date" class="fk-tgl form-control form-control-sm" style="min-width:120px;font-size:12px;"></td>');
                        dt1.row.add($r);
                        exRo[nb] = true; addedRo++;
                    });
                    if (addedRo) { dt1.draw(false); }
                    $btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cari BPB');
                    if (added === 0 && addedRo === 0) {
                        Swal.fire({icon:'info', title:'Tidak ada data baru', text:'Tidak ada BPB / RO baru (di luar yang sudah ada) untuk rentang ini.'});
                    }
                }).fail(function(){
                    $btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cari BPB');
                    if (added === 0) { Swal.fire({icon:'info', title:'Tidak ada BPB baru', text:'Tidak ada BPB baru untuk rentang ini.'}); }
                });
            }).fail(function(xhr){
                $btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cari BPB');
                Swal.fire('Error', xhr.responseText || 'Gagal mencari BPB', 'error');
            });
        });


        $('#btnSearchBPPB').on('click', function() {
            let nama_supp = $('#nama_supp_e2').val();
            let start_date = $('#start_date_e2').val();
            let end_date = $('#end_date_e2').val();
            let no_kbon = $('#no_kbon_e2').val();
            let profit_center = $('#profit_center_e2').val();

            $.ajax({
                url: 'get_bppb_kontrabon.php',
                type: 'POST',
                data: {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center},
                success: function(res) {
                    console.log(res);
                    tableBPPB.clear().rows.add($(res)).draw();
                }

            });
        });


        $('#btnSearchFTR').on('click', function() {
            let nama_supp = $('#nama_supp_e3').val();
            let start_date = $('#start_date_e3').val();
            let end_date = $('#end_date_e3').val();
            let no_kbon = $('#no_kbon_e3').val();
            let profit_center = $('#profit_center_e3').val();


            $.ajax({
                url: 'get_ftr_kontrabon.php',
                type: 'POST',
                data: {nama_supp: nama_supp, start_date: start_date, end_date: end_date, no_kbon: no_kbon, profit_center: profit_center},
                success: function(res) {
                    console.log(res);
                    tableFTR.clear().rows.add($(res)).draw();
                }

            });
        });


        // Save BPB
        $('#btnSaveBPB').on('click', function(e) {
            e.preventDefault();

            let dataArr = [];

            $('#mytable_edit tbody tr').each(function() {
                let checkbox = $(this).find('.select_edit');
                if (checkbox.is(':checked')) {
                    let no_kbon = $('#nokontrabon').val();
                    let tgl_kbon = $('#tanggal').val();
                    let pc_kbon = $('#profit_center').val();
                    let nama_supp = $('#nama_supp_e1').val();
                    let invoice = $('#txt_inv').val();
                    let faktur = $('#no_faktur').val();
                    let tglsi = $('#txt_tglsi').val();
                    let tgltempo = $('#txt_tgltempo').val();
                    var create_user = '<?php echo $user; ?>';
                    var no_bpb = $(this).closest('#mytable_edit tr').find('td:eq(1)').attr('value');
                    var no_po = $(this).closest('#mytable_edit tr').find('td:eq(2)').attr('value');
                    var tgl_bpb = $(this).closest('#mytable_edit tr').find('td:eq(3)').attr('value');
                    var price = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(4)').attr('data-subtotal'),10) ||0;
                    var tax = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(5)').attr('data-tax'),10) ||0;
                    var cash = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(8)').attr('data'),10) ||0;
                    var tgl_po = $(this).closest('#mytable_edit tr').find('td:eq(12)').attr('value');
                    var pph = parseFloat($(this).closest('#mytable_edit tr').find('td:eq(6)').find('select[name=combo_pph_edit] option').filter(':selected').val(),10) ||0;
                    var idtax = $(this).closest('#mytable_edit tr').find('td:eq(6)').find('select[name=combo_pph_edit] option').filter(':selected').attr('data-idtax');
                    var curr_bpb = $(this).closest('#mytable_edit tr').find('td:eq(14)').attr('value') || '';
                    var mattype = $(this).closest('#mytable_edit tr').find('td:eq(15)').attr('value') || '';
                    var matclass = $(this).closest('#mytable_edit tr').find('td:eq(16)').attr('value') || '';
                    var n_code_category = $(this).closest('#mytable_edit tr').find('td:eq(17)').attr('value') || '';
                    var cus_ctg = $(this).closest('#mytable_edit tr').find('td:eq(18)').attr('value') || ''; 
                    let start_date = $('#start_date_e1').val();
                    let end_date = $('#end_date_e1').val();

                    var sum_pph = 0;
                    var sum_sub = 0;
                    var sum_tax = 0;
                    var sum_total = 0;
                    var sum_dp = 0;
                    sum_sub += price;
                    sum_tax += tax;
                    sum_pph += sum_sub * (pph / 100);   
                    sum_total += (sum_sub + sum_tax) - sum_pph - sum_dp;


                    dataArr.push({
                        no_kbon: no_kbon,
                        tgl_kbon: tgl_kbon,
                        pc_kbon: pc_kbon,
                        nama_supp: nama_supp,
                        invoice: invoice,
                        faktur: faktur,
                        create_user: create_user,
                        no_bpb: no_bpb,
                        no_po: no_po,
                        tgl_bpb: tgl_bpb,
                        price: price,
                        tax: tax,
                        cash: cash,
                        tgl_po: tgl_po,
                        pph: pph,
                        idtax: idtax,
                        mattype: mattype,
                        matclass: matclass,
                        n_code_category: n_code_category,
                        cus_ctg: cus_ctg,
                        sum_sub: sum_sub,
                        sum_tax: sum_tax,
                        sum_pph: sum_pph,
                        sum_total: sum_total,
                        tglsi: tglsi,
                        tgltempo: tgltempo,
                        curr_bpb: curr_bpb,
                        start_date: start_date,
                        end_date: end_date
                    });
                }
            });

if (dataArr.length === 0) {
    Swal.fire({
        icon: 'warning',
        title: 'No BPB selected',
        text: 'Please check at least one BPB to save.'
    });
    return;
}

Swal.fire({
    title: 'Are you sure?',
    text: "The new data will replace the old data.",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, save it!',
    cancelButtonText: 'Cancel'
}).then((result) => {
    if (result.isConfirmed) {
        $.ajax({
            url: 'insert_bpb_kontrabon_edit.php',
            type: 'POST',
            data: {data: JSON.stringify(dataArr)},
            success: function(res) {
                    // alert(JSON.stringify(res));
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Your data has been successfully saved.'
                    }).then(() => {
                        // Refresh table or close modal
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong: ' + error
                    });
                }
            });
    }
});
});




$('#btnSaveBPPB').on('click', function(e) {
    e.preventDefault();

    let dataArr = [];

    $('#mytable1_edit tbody tr').each(function() {
        let checkbox = $(this).find('.select2_edit');
        if (checkbox.is(':checked')) {
            let no_kbon = $('#nokontrabon').val();
            let tgl_kbon = $('#tanggal').val();
            let pc_kbon = $('#profit_center').val();
            let nama_supp = $('#nama_supp_e2').val();
            let invoice = $('#txt_inv').val();
            let faktur = $('#no_faktur').val();
            let tglsi = $('#txt_tglsi').val();
            let tgltempo = $('#txt_tgltempo').val();
            var create_user = '<?php echo $user; ?>';

            var no_ro = $(this).closest('#mytable1_edit tr').find('td:eq(1)').attr('data-ro');
            var no_bppb = $(this).closest('#mytable1_edit tr').find('td:eq(2)').attr('valuess');
            var tgl_bppb = $(this).closest('#mytable1_edit tr').find('td:eq(3)').attr('valuess');
            var ttl_ro = parseFloat($(this).closest('#mytable1_edit tr').find('td:eq(6) input').val(),10) || 0;
            var curr = $(this).closest('#mytable1_edit tr').find('td:eq(7)').attr('valuess');
            var mattype = $(this).closest('#mytable1_edit tr').find('td:eq(8)').attr('valuess');
            var matclass = $(this).closest('#mytable1_edit tr').find('td:eq(9)').attr('valuess');
            var n_code_category = $(this).closest('#mytable1_edit tr').find('td:eq(10)').attr('valuess');
            var cus_ctg = $(this).closest('#mytable1_edit tr').find('td:eq(11)').attr('valuess');

            let start_date = $('#start_date_e2').val();
            let end_date = $('#end_date_e2').val();

            // var sum_pph = 0;
            // var sum_sub = 0;
            // var sum_tax = 0;
            // var sum_total = 0;
            // var sum_dp = 0;
            // sum_sub += price;
            // sum_tax += tax;
            // sum_pph += sum_sub * (pph / 100);   
            // sum_total += (sum_sub + sum_tax) - sum_pph - sum_dp;


            dataArr.push({
                no_kbon: no_kbon,
                tgl_kbon: tgl_kbon,
                pc_kbon: pc_kbon,
                nama_supp: nama_supp,
                invoice: invoice,
                faktur: faktur,
                tglsi: tglsi,
                tgltempo: tgltempo,
                create_user: create_user,

                no_ro: no_ro,
                no_bppb: no_bppb,
                tgl_bppb: tgl_bppb,
                ttl_ro: ttl_ro,
                curr: curr,
                mattype: mattype,
                matclass: matclass,
                n_code_category: n_code_category,
                cus_ctg: cus_ctg,

                start_date: start_date,
                end_date: end_date
                
            });
            console.log("Current dataArr:", dataArr);
        }
    });

    if (dataArr.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No BPPB selected',
            text: 'Please check at least one BPPB to save.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "The new data will replace the old data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'insert_bppb_kontrabon_edit.php',
                type: 'POST',
                data: {data: JSON.stringify(dataArr)},
                success: function(res) {
                    // alert(JSON.stringify(res));
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Your data has been successfully saved.'
                    }).then(() => {
                        // Refresh table or close modal
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong: ' + error
                    });
                }
            });
        }
    });
});


$('#btnSaveFTR').on('click', function(e) {
    e.preventDefault();

    let dataArr = [];

    $('#mytable2_edit tbody tr').each(function() {
        let checkbox = $(this).find('.select3_edit');
        if (checkbox.is(':checked')) {
            let no_kbon = $('#nokontrabon').val();
            let tgl_kbon = $('#tanggal').val();
            let pc_kbon = $('#profit_center').val();
            let nama_supp = $('#nama_supp_e2').val();
            let invoice = $('#txt_inv').val();
            let faktur = $('#no_faktur').val();
            let tglsi = $('#txt_tglsi').val();
            let tgltempo = $('#txt_tgltempo').val();
            var create_user = '<?php echo $user; ?>';

            var no_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(1)').attr('no-ftr');
            var no_po_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(2)').attr('po-ftr');
            var tgl_po_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(3)').attr('tglpo-ftr');
            var no_pi_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(4)').attr('pi-ftr');
            var ttl_ftr = parseFloat($(this).closest('#mytable2_edit tr').find('td:eq(6) input').val(),10) || 0;
            var curr_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(7)').attr('curr-ftr');
            var kbon_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(8)').attr('kbon-ftr');
            var tglkbon_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(9)').attr('tglkbon-ftr');
            var lp_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(10)').attr('lp-ftr');
            var tgllp_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(11)').attr('tgllp-ftr');
            var pv_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(12)').attr('pv-ftr');
            var bankout_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(13)').attr('bankout-ftr');
            var bankoutdate_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(14)').attr('bankoutdate-ftr');
            var coa_ftr = $(this).closest('#mytable2_edit tr').find('td:eq(15)').attr('coa-ftr');

            let start_date = $('#start_date_e3').val();
            let end_date = $('#end_date_e3').val();

            // var sum_pph = 0;
            // var sum_sub = 0;
            // var sum_tax = 0;
            // var sum_total = 0;
            // var sum_dp = 0;
            // sum_sub += price;
            // sum_tax += tax;
            // sum_pph += sum_sub * (pph / 100);   
            // sum_total += (sum_sub + sum_tax) - sum_pph - sum_dp;


            dataArr.push({
                no_kbon: no_kbon,
                tgl_kbon: tgl_kbon,
                pc_kbon: pc_kbon,
                nama_supp: nama_supp,
                invoice: invoice,
                faktur: faktur,
                tglsi: tglsi,
                tgltempo: tgltempo,
                create_user: create_user,

                no_ftr: no_ftr,
                no_po_ftr: no_po_ftr,
                tgl_po_ftr: tgl_po_ftr,
                no_pi_ftr: no_pi_ftr,
                ttl_ftr: ttl_ftr,
                curr_ftr: curr_ftr,
                kbon_ftr: kbon_ftr,
                tglkbon_ftr: tglkbon_ftr,
                lp_ftr: lp_ftr,
                tgllp_ftr: tgllp_ftr,
                pv_ftr: pv_ftr,
                bankout_ftr: bankout_ftr,
                bankoutdate_ftr: bankoutdate_ftr,
                coa_ftr: coa_ftr,

                start_date: start_date,
                end_date: end_date
                
            });
            console.log("Current dataArr:", dataArr);
        }
    });

    if (dataArr.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No BPPB selected',
            text: 'Please check at least one BPPB to save.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "The new data will replace the old data.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'insert_ftr_kontrabon_edit.php',
                type: 'POST',
                data: {data: JSON.stringify(dataArr)},
                success: function(res) {
                    // alert(JSON.stringify(res));
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Your data has been successfully saved.'
                    }).then(() => {
                        // Refresh table or close modal
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong: ' + error
                    });
                }
            });
        }
    });
});

});
</script>


<script>
    function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("mytable");
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

function myFunction2() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput1");
  filter = input.value.toUpperCase();
  table = document.getElementById("mytable1");
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
        var tanggal = document.getElementById('tanggal').value; 
        var txt_top = parseFloat(document.getElementById('txt_top').value,10) || 0;
        var coba = new Date();
        var hasil = addDate(tanggal, txt_top);
        // alert(tanggal);
        $.ajax({
            type: 'POST', 
            url: 'getnomor_kbon.php', 
            data: {'tanggal':tanggal},
            success: function(response) { 
                // console.log(response);
                $('#nokontrabon').val(response); 
                updateNoKontraBon(); 
            }
        });
    // result.setDate(result.getDate() + txt_top);
    // tgl2    = DATEADD(day, txt_top, tanggal);
    $("#tanggal").val(tanggal);
    $("#txt_tgltempo").val(hasil);

};
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


// $(".chkA").change(function(){
    $("input[id=select]").change(function(){
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
        $(".chkC").prop("disabled", false);
    // $(this).closest('tr').find('td:eq(6) input').val(0);   
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph]').prop('disabled', true);         
    $("input[type=checkbox]:checked").each(function () { 
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
        var pph1 = parseFloat(document.getElementById('pph_h').value,10) || 0;      
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
    pph = 0;
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

if(pph1 == ''){
  pph11= pph;  
}else{
    pph11= pph1;
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
    $("#subtotal_h").val(sum_sub.toFixed(2)); 
    $("#subtotal_h1").val(data1.toFixed(2)); 
    $("#potongan").val(formatMoney(total));
    $("#potongan_h").val(total.toFixed(4));            
    $("#po").val(nopo1); 
    $("#po1").val(nopo); 
    $("#sisapotongan").val(formatMoney(sisa));
    $("#ttl_sub").val(sisa);
    $("#ttl_dp").val(formatMoney(total_ftr));
    $("#ttl_dp_h").val(total_ftr.toFixed(2));
    $("#pajak").val(formatMoney(ppn));
    $("#pajak_h").val(ppn);
    $("#pph").val(formatMoney(pph11));
    $("#pph_h").val(pph11.toFixed(2));
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

    $(".chkC").prop("disabled", true); 
    if (allowedPO.length > 0) {
        $("#mytable2 tbody tr").each(function () {
            var poC = $(this).find("td:eq(2)").attr("po-ftr");
            if (allowedPO.includes(poC)) {
                $(this).find(".chkC").prop("disabled", false);
            }
        });
    }else{
        $("#mytable2 tbody tr").each(function () {
            $(".chkC").prop("disabled", false).prop("checked", false);
            $(this).find('td:eq(6) input').prop('disabled', true);
            $("#ttl_dp").val('');
            $("#ttl_dp_h").val('');
        });
    }

});        
</script>

<script type="text/javascript">
    $("select[name=combo_pph]").on('change', function(){
        var sum_sub = 0;
        var total = 0;
        var sisa = 0;
        var nopo= '';
        var ppn = 0;
        var cbddp = 0;
        var sum_pph = 0;
        $("input[type=checkbox]:checked").each(function () {  
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
        $("#pph_h").val(sum_pph.toFixed(2));        
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
        $("input[type=checkbox]:checked").each(function () {        
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
        $("#potongan_h").val(sum_total.toFixed(4));
    });


    $("#mytable2 input[name=amount_ftr]").keyup(function(){
        var sum_amount = 0;
        var sum_total = 0;
        var sum_balance = 0;        
        $("input[type=checkbox]:checked").each(function () {        
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
        $("#ttl_dp_h").val(sum_total.toFixed(4));
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
    // Delegasi dari document: form-simpan bisa ter-auto-close browser krn nesting
    // div bawaan form (net div -5). Pakai document agar klik #calculate tetap kena.
    $(document).on("click", "#calculate", function(){
        // Jumlahkan HANYA baris BPB yang ter-ceklis (mirror create). Subtotal/PPn/PPh
        // dihitung ulang dari tabel, bukan dari nilai prefilled — supaya uncheck/tambah
        // BPB langsung berpengaruh ke total.
        var sum_sub = 0, sum_tax = 0, sum_pph = 0;
        $('#mytable tbody tr').each(function(){
            if (!$(this).find('.select_edit').is(':checked')) return;
            var price = parseFloat($(this).find('td:eq(4)').attr('data-subtotal'),10) || 0;
            var tax   = parseFloat($(this).find('td:eq(5)').attr('data-tax'),10) || 0;
            var pphp  = parseFloat($(this).find('td:eq(6) select.combo_pph_edit option:selected').val(),10) || 0;
            sum_sub += price;
            sum_tax += tax;
            sum_pph += price * (pphp / 100);
        });

        $('#subtotal').val(formatMoney(sum_sub));
        $('#subtotal_h').val(sum_sub.toFixed(2));
        $('#pajak').val(formatMoney(sum_tax));
        $('#pajak_h').val(sum_tax.toFixed(2));
        $('#pph').val(formatMoney(sum_pph));
        $('#pph_h').val(sum_pph.toFixed(2));

        var potongan_h = parseFloat(document.getElementById('potongan_h').value,10) || 0; // Total Return
        var ttl_dp_h   = parseFloat(document.getElementById('ttl_dp_h').value,10) || 0;    // Total DP/CBD
        var jml_potong = parseFloat(document.getElementById('jml_potong').value,10) || 0;  // Potongan lain-lain

        var jumlah = (sum_sub - (potongan_h + ttl_dp_h) + jml_potong) + sum_tax - sum_pph;
        $("#total").val(formatMoney(jumlah));
        $("#total_h").val(jumlah.toFixed(4));
    });
</script>


<script type="text/javascript">
    $(document).on("click", "#simpan", function(){
        // WAJIB Calculate dulu (total kosong sampai Calculate dijalankan).
        var total_h = document.getElementById('total_h').value;
        if (total_h === '') { document.getElementById('calculate').focus(); alert("Please do the calculation "); return; }
        if (parseFloat(total_h) < 0) { alert("Contrabon can't be minus "); return; }

        var create_user_h = '<?php echo $user; ?>';

        // ---- Set BPB final = baris yang TER-CEKLIS (uncheck = dibuang, ceklis baru = ditambah) ----
        var dataArr = [];
        $('#mytable tbody tr').each(function(){
            var $tr = $(this);
            if (!$tr.find('.select_edit').is(':checked')) return;
            var no_bpb = $tr.find('td:eq(1)').attr('value');
            if (!no_bpb) return;
            var price = parseFloat($tr.find('td:eq(4)').attr('data-subtotal'),10) || 0;
            var tax   = parseFloat($tr.find('td:eq(5)').attr('data-tax'),10) || 0;
            var cash  = parseFloat($tr.find('td:eq(8)').attr('data'),10) || 0;
            var pph   = parseFloat($tr.find('td:eq(6) select.combo_pph_edit option:selected').val(),10) || 0;
            var idtax = $tr.find('td:eq(6) select.combo_pph_edit option:selected').attr('data-idtax');
            var sum_sub = price, sum_tax = tax, sum_pph = price * (pph/100);
            var sum_total = (sum_sub + sum_tax) - sum_pph;
            dataArr.push({
                no_kbon: $('#nokontrabon').val(), tgl_kbon: $('#tanggal').val(), pc_kbon: $('#profit_center').val(),
                nama_supp: $('#txt_supp').val(), invoice: $('#txt_inv').val(),
                faktur: ($.trim($tr.find('.fk-no').val() || '') || $('#no_faktur').val()),  // No Faktur per-BPB
                create_user: create_user_h,
                no_bpb: no_bpb, no_po: $tr.find('td:eq(2)').attr('value'), tgl_bpb: $tr.find('td:eq(3)').attr('value'),
                price: price, tax: tax, cash: cash, tgl_po: $tr.find('td:eq(12)').attr('value'),
                pph: pph, idtax: idtax,
                mattype: $tr.find('td:eq(15)').attr('value') || '', matclass: $tr.find('td:eq(16)').attr('value') || '',
                n_code_category: $tr.find('td:eq(17)').attr('value') || '', cus_ctg: $tr.find('td:eq(18)').attr('value') || '',
                sum_sub: sum_sub, sum_tax: sum_tax, sum_pph: sum_pph, sum_total: sum_total,
                tglsi: $('#txt_tglsi').val(), tgltempo: $('#txt_tgltempo').val(),
                curr_bpb: $tr.find('td:eq(14)').attr('value') || '',
                start_date: $('#bpb_start').val(), end_date: $('#bpb_end').val()
            });
        });
        if (dataArr.length === 0) { Swal.fire({icon:'warning', title:'Tidak ada BPB', text:'Ceklis minimal satu BPB.'}); return; }

        // Map faktur per-BPB (#mytable) & per-RETUR (#mytable1) -> dipakai server untuk
        // update bpb_new / bppb_new (upt_*) + faktur_pajak jurnal, dgn strip-guard.
        var bpbFakturMap = {};
        $('#mytable tbody tr').each(function () {
            var $tr = $(this);
            if (!$tr.find('.select_edit').is(':checked')) return;
            var noBpb = ($tr.find('td:eq(1)').attr('value') || '').trim();
            if (!noBpb) return;
            bpbFakturMap[noBpb] = { no_faktur: $.trim($tr.find('.fk-no').val() || ''), tgl_faktur: $.trim($tr.find('.fk-tgl').val() || '') };
        });
        var retFakturMap = {};
        $('#mytable1 tbody tr').each(function () {
            var $tr = $(this);
            var noBppb = ($tr.find('td:eq(2)').attr('valuess') || '').trim();
            if (!noBppb) return;
            retFakturMap[noBppb] = { no_faktur: $.trim($tr.find('.fk-no').val() || ''), tgl_faktur: $.trim($tr.find('.fk-tgl').val() || '') };
        });

        var payload = {
            no_kbon_h: document.getElementById('nokontrabon').value,
            unik_code: document.getElementById('unik_code').value,
            tgl_kbon_h: document.getElementById('tanggal').value,
            tgl_kbon_s: document.getElementById('tanggal4').value,
            nama_supp_h: document.getElementById('txt_supp').value,
            no_faktur_h: document.getElementById('no_faktur').value,
            no_po_h: document.getElementById('po').value,
            supp_inv_h: document.getElementById('txt_inv').value,
            tgl_inv_h: document.getElementById('txt_tglsi').value,
            tgl_tempo_h: document.getElementById('txt_tgltempo').value,
            curr_h: document.getElementById('matauang').value,
            sub_h: document.getElementById('subtotal_h').value,
            tax_h: document.getElementById('pajak_h').value,
            dp_h: document.getElementById('ttl_dp_h').value,
            pph_h: document.getElementById('pph_h').value,
            total_h: document.getElementById('total_h').value,
            create_user_h: create_user_h,
            jml_return: document.getElementById('potongan_h').value,
            lr_kurs: document.getElementById('labarugi').value,
            s_qty: document.getElementById('selisihqty').value,
            s_harga: document.getElementById('selisihharga').value,
            materai: document.getElementById('materai').value,
            pot_beli: document.getElementById('potongbeli').value,
            ekspedisi: document.getElementById('ekspedisi').value,
            moq: document.getElementById('moq').value,
            jml_potong: document.getElementById('jml_potong').value,
            potongan_ppn: document.getElementById('potongan_ppn') ? document.getElementById('potongan_ppn').value : 0,
            potongan_pph: document.getElementById('potongan_pph') ? document.getElementById('potongan_pph').value : 0,
            mattype: document.getElementById('h_mattype').value,
            matclass: document.getElementById('h_matclass').value,
            n_code_category: document.getElementById('h_code_ctg').value,
            cus_ctg: document.getElementById('h_cus_ctg').value,
            profit_center: $('select[name=profit_center] option').filter(':selected').val(),
            ir_number: $('select[name=ir_number] option').filter(':selected').val(),
            bank_account: $('select[name=sel_bank_account] option').filter(':selected').val(),
            from_account: $('select[name=from_account] option').filter(':selected').val(),
            from_bank: document.getElementById('from_bank') ? document.getElementById('from_bank').value : '',
            from_bank_curr: document.getElementById('from_bank_curr') ? document.getElementById('from_bank_curr').value : '',
            bpb_faktur_map: JSON.stringify(bpbFakturMap),
            ret_faktur_map: JSON.stringify(retFakturMap)
        };

        // RO/retur dari #mytable1 (ter-ceklis) -> full-replace ap_edit_return_kb + jurnal RO
        // ke ap_journal_temp (sama alur modal "Cari Return"), supaya RO yang ditambah lewat
        // "Cari BPB" ikut tersimpan & terjurnal saat rebuild.
        var roArr = [];
        $('#mytable1 tbody tr').each(function(){
            var $tr = $(this);
            var chk = $tr.find('.chkB');
            if (chk.length && !chk.is(':checked')) return;   // uncheck = dibuang
            var no_bppb = ($tr.find('td:eq(2)').attr('valuess') || '').trim();
            if (!no_bppb) return;
            roArr.push({
                no_kbon: $('#nokontrabon').val(), tgl_kbon: $('#tanggal').val(), pc_kbon: $('#profit_center').val(),
                nama_supp: $('#txt_supp').val(), invoice: $('#txt_inv').val(), faktur: $('#no_faktur').val(),
                tglsi: $('#txt_tglsi').val(), tgltempo: $('#txt_tgltempo').val(), create_user: create_user_h,
                no_ro: $tr.find('td:eq(1)').attr('data-ro'), no_bppb: no_bppb,
                tgl_bppb: $tr.find('td:eq(3)').attr('valuess'),
                ttl_ro: parseFloat($tr.find('td:eq(6) input').val(),10) || 0,
                curr: $tr.find('td:eq(7)').attr('valuess'), mattype: $tr.find('td:eq(8)').attr('valuess'),
                matclass: $tr.find('td:eq(9)').attr('valuess'), n_code_category: $tr.find('td:eq(10)').attr('valuess'),
                cus_ctg: $tr.find('td:eq(11)').attr('valuess'),
                start_date: $('#bpb_start').val(), end_date: $('#bpb_end').val()
            });
        });

        // (0) simpan RO (kalau ada) -> (1) rebuild set BPB temp -> (2) simpan revisi + jurnal balik.
        Swal.fire({ title:'Menyimpan...', allowOutsideClick:false, didOpen: function(){ Swal.showLoading(); } });
        function pvEditSaveBpbThenAll(){
            $.ajax({
                type:'POST', url:'insert_bpb_kontrabon_edit.php', data:{ data: JSON.stringify(dataArr) },
                success: function(){
                    $.ajax({
                        type:'POST', url:'insert_kontrabon_edit_all.php', data: payload,
                        success: function(response){
                            localStorage.removeItem("profit_center");
                            Swal.fire({ icon:'success', title:'Tersimpan', text: ('' + response).slice(0, 200) }).then(function(){
                                window.location = 'payment-voucher-ap.php';
                            });
                        },
                        error: function(xhr){ Swal.fire('Error', 'Gagal revisi: ' + (xhr.responseText || ''), 'error'); }
                    });
                },
                error: function(xhr){ Swal.fire('Error', 'Gagal simpan BPB: ' + (xhr.responseText || ''), 'error'); }
            });
        }
        if (roArr.length) {
            $.ajax({
                type:'POST', url:'insert_bppb_kontrabon_edit.php', data:{ data: JSON.stringify(roArr) },
                success: pvEditSaveBpbThenAll,
                error: function(xhr){ Swal.fire('Error', 'Gagal simpan RO: ' + (xhr.responseText || ''), 'error'); }
            });
        } else {
            pvEditSaveBpbThenAll();
        }
    });


    // Delegasi di document (paling robust) + redirect apapun hasilnya (.always), supaya
    // tombol Back selalu membawa user kembali ke daftar PV walau revert temp gagal.
    $(document).on("click", "#batal", function(){
        var no_kbon_h = document.getElementById('nokontrabon').value;
        $.post('batal_edit_kontrabon.php', { no_kbon_h: no_kbon_h }).always(function(){
            localStorage.removeItem("profit_center");
            window.location = 'payment-voucher-ap.php';
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
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
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
