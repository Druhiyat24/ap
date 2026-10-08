<?php include '../header.php' ?>
<?php
/* ============================================================================
   HALAMAN EDIT FTR DP.

   HANYA untuk dokumen berstatus draft. Dokumen yang sudah Approved dirujuk
   kontrabon_ftr / payment_ftrdp / pa_saldo_awal, jadi menambah atau membuang
   PO di sana akan membuat angka di dokumen hilir tidak cocok lagi.
   ============================================================================ */
$ftr_no = isset($_GET['no']) ? trim(base64_decode($_GET['no'], true)) : '';
if ($ftr_no === '' && isset($_POST['noftrdp'])) { $ftr_no = trim($_POST['noftrdp']); }

function ftrEditGagal($pesan) {
    echo '<div class="container-fluid mt-3 p-3"><div class="alert alert-warning" style="border-radius:12px">'
       . '<b>FTR DP cannot be edited.</b><br>' . htmlspecialchars($pesan, ENT_QUOTES)
       . '<div style="margin-top:10px"><a class="app-btn app-btn-light app-btn-sm" href="ftrdp.php">'
       . '<i class="fa fa-angle-double-left"></i> Back to list</a></div></div></div>';
    echo '<link rel="stylesheet" href="../css/app-skin-form.css">';
    exit;
}

if ($ftr_no === '') { ftrEditGagal('No document number was given.'); }

$ftr_no_esc = mysqli_real_escape_string($conn2, $ftr_no);
$q_head = mysqli_query($conn2, "select * from ftr_dp where no_ftr_dp = '$ftr_no_esc' order by id");
if (!$q_head) { ftrEditGagal('Database error: ' . mysqli_error($conn2)); }

$ftr_head  = null;
$ftr_baris = array();
while ($r = mysqli_fetch_assoc($q_head)) {
    if ($ftr_head === null) { $ftr_head = $r; }
    $ftr_baris[$r['no_po']] = $r;
}
if ($ftr_head === null)              { ftrEditGagal('FTR DP ' . $ftr_no . ' was not found.'); }
if ($ftr_head['status'] !== 'draft') { ftrEditGagal('FTR DP ' . $ftr_no . ' is ' . $ftr_head['status'] . '. Only a draft can be edited.'); }

$ftr_supp      = (string) $ftr_head['supp'];
$ftr_tanggal   = (!empty($ftr_head['tgl_ftr_dp']) && $ftr_head['tgl_ftr_dp'] > '1970-01-01') ? date('d-m-Y', strtotime($ftr_head['tgl_ftr_dp'])) : '';
$ftr_tgl_bayar = (!empty($ftr_head['tgl_bayar'])   && $ftr_head['tgl_bayar']   > '1970-01-01') ? date('d-m-Y', strtotime($ftr_head['tgl_bayar']))   : '';
$po_tampil     = array();

/* Satu baris tabel PO untuk FTR DP. $b = baris tersimpan di ftr_dp, atau null
   kalau PO ini belum ada di dokumen. $total_po = nilai PO penuh, $ost = sisa
   yang masih bisa di-DP-kan. */
function barisEditFtrDp($po, $podate, $total_po, $ost, $curr, $supplier, $b) {
    $pilih = ($b !== null);
    $pi    = $pilih ? (string) $b['no_pi'] : '';
    $dp    = $pilih ? (string) ($b['dp'] + 0) : '';
    $dpval = $pilih ? number_format((float) $b['dp_value'], 2, '.', ',') : '';
    $dpraw = $pilih ? (float) $b['dp_value'] : '';
    $mati  = $pilih ? '' : ' disabled';
    $tgl   = (!empty($podate) && $podate > '1970-01-01') ? date('d-M-Y', strtotime($podate)) : '-';
    $e     = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES); };

    return '<tr>'
        . '<td style="width:10px;"><input type="checkbox" name="select[]" value="1"' . ($pilih ? ' checked' : '') . '></td>'
        . '<td style="width:50px;" value="' . $e($po) . '">' . $e($po) . '</td>'
        . '<td style="width:100px;"><input type="text" style="font-size: 14px;" class="form-control" id="txt_pi" name="txt_pi" value="' . $e($pi) . '"' . $mati . '></td>'
        . '<td style="width:100px;" value="' . $e($podate) . '">' . $tgl . '</td>'
        . '<td class="dt_total" style="width:100px;text-align: right;" data-total="' . $e($total_po) . '">' . number_format((float) $total_po, 2) . '</td>'
        . '<td class="dt_total" style="width:100px;text-align: right;" data-total="' . $e($ost) . '">' . number_format((float) $ost, 2) . '</td>'
        . '<td style="width:100px;"><input type="number" style="font-size: 14px;text-align: right;" class="form-control" id="txt_dp" name="txt_dp" data-value="' . $e($dp) . '" value="' . $e($dp) . '"' . $mati . '></td>'
        . '<td style="width:100px;"><input type="text" style="font-size: 12px;text-align: right;" class="form-control" id="txt_dp_value" name="txt_dp_value" data-value="' . $e($dpraw) . '" value="' . $e($dpval) . '"' . $mati . '></td>'
        . '<td style="width:50px;" value="' . $e($curr) . '">' . $e($curr) . '</td>'
        . '<td style="display: none;" value="' . $e($supplier) . '">' . $e($supplier) . '</td>'
        . '</tr>';
}
?>

<!-- Skin kontrol form (selectpicker & input tanggal) - potongan dari
     app-skin.css. Ditaut SENDIRI dgn mtime-nya, BUKAN lewat app-skin.css:
     berkas induk itu meng-@import tanpa nomor versi, sehingga perubahan di
     berkas yang di-import tidak pernah sampai ke browser. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">

<!-- Kosakata .ftr- dipakai BERSAMA dgn form FTR CBD - satu sumber, jadi
     kedua form itu tidak bisa melenceng satu sama lain. -->
<link rel="stylesheet" href="../css/app-ftr-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-form.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <div class="ftr-card">
    <div class="ftr-head">
      <span class="ftr-head-icon"><i class="fa fa-exchange" aria-hidden="true"></i></span>
      <div>
        <h1>Form Transfer Request (DP)</h1>
        <span class="ftr-crumb">AP &rsaquo; FTR DP &rsaquo; Edit</span>
      </div>
    </div>

    <div class="ftr-panel">
            <form id="form-data" method="post">
                <div class="form-row">
                    <div class="col-12 col-sm-6 col-xl-3 mb-3">
                        <label for="noftrdp"><b>No FTR DP</b></label>
                        <?php echo '<input type="text" readonly style="font-size: 13px;" class="form-control-plaintext" id="noftrdp" name="noftrdp" value="' . htmlspecialchars($ftr_no, ENT_QUOTES) . '">'; ?>












                    </div>
                    <div class="col-12 col-sm-6 col-xl-2 mb-3">
                        <label for="tanggal"><b>FTR DP Date<i style="color: red;">*</i></b></label>          
                        <input type="text" style="font-size: 13px;" name="tanggal" id="tanggal" class="form-control tanggal" 
                        value="<?php             
                        if (!empty($_POST['tanggal'])) {
                            echo $_POST['tanggal'];
                        } elseif ($ftr_tanggal !== '') {
                            echo $ftr_tanggal;

                        }
                        else{
                            echo date("d-m-Y");
                        } ?>">
                    </div>

                    <div class="col-12 col-sm-6 col-xl-2 mb-3">
                        <label for="payment_date"><b>Payment Date<i style="color: red;">*</i></b></label>          
                        <input type="text" style="font-size: 13px;" name="payment_date" id="payment_date" class="form-control tanggal" 
                        value="<?php             
                        if (!empty($_POST['payment_date'])) {
                            echo $_POST['payment_date'];
                        } elseif ($ftr_tgl_bayar !== '') {
                            echo $ftr_tgl_bayar;

                        }
                        else{
                            echo '-';
                        } ?>">
                    </div>

                    <!-- PAYMENT METHOD (baru, 01 Okt 2026).
                         TIDAK ada pilihan bawaan: cara bayar harus dipilih sadar,
                         karena Cash menentukan perlakuan berikutnya - tanda tangan
                         Cashier & Received By di cetakan, dan kemungkinan tidak
                         dibuatkan PV-AP DP-nya sama sekali. -->
                    <div class="col-12 col-sm-6 col-xl-2 mb-3">
                        <label for="payment_method"><b>Payment Method <i style="color: red;">*</i></b></label>
                        <select class="form-control selectpicker" name="payment_method" id="payment_method" data-dropup-auto="false">
                            <?php
                            $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : (string) $ftr_head['payment_method'];
                            echo '<option value="" disabled' . ($payment_method === '' ? ' selected' : '') . '>Select Payment Method</option>';
                            foreach (array('Transfer', 'Cash') as $pm) {
                                echo '<option value="' . $pm . '"' . ($pm === $payment_method ? ' selected' : '') . '>' . $pm . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- ITEM TYPE (baru, 01 Okt 2026).
                         Direkam DI SINI karena untuk FTR ber-Payment Method Cash ada
                         kemungkinan PV-AP DP-nya tidak pernah dibuat - padahal jenis
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
                            $profit_center = isset($_POST['profit_center']) ? $_POST['profit_center'] : (string) $ftr_head['profit_center'];               
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
                        <label for="item_type"><b>Item Type <i style="color: red;">*</i></b></label>
                        <select class="form-control selectpicker" name="item_type" id="item_type" data-dropup-auto="false" data-live-search="true">
                            <option value="" disabled selected>Select Item Type</option>
                            <?php
                            $item_type = isset($_POST['item_type']) ? $_POST['item_type'] : $ftr_head['item_type'];
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
                            <input type="text" readonly style="font-size: 13px;" class="form-control" name="txt_supp" id="txt_supp" 
                            value="<?php 
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp'] : $ftr_supp;
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
                                                <option value="" disabled selected="true">Select</option>                
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
                                        <button type="submit" id="send" name="send" class="app-btn app-btn-primary app-btn-sm"><i class="fa fa-check"></i> Apply</button>
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
                <button type="button" class="app-btn app-btn-light" name="mysupp" id="mysupp" data-target="#mymodal" data-toggle="modal"><i class="fa fa-search"></i> Select</button>
                <input type="hidden" name="bpbvalue" id="bpbvalue" value="">      
            </div>
        </div>
    </div> 
                    <div class="col-12 col-xl-5 mb-3">
        <label for="memo"><b>Description</b></label>          
        <input type="text" style="font-size: 14px;" class="form-control" name="memo" id="memo" 
        value="<?php             
        if (isset($_POST['memo']) && $_POST['memo'] !== '') {
            echo htmlspecialchars($_POST['memo'], ENT_QUOTES);
        } else {
            echo htmlspecialchars((string) $ftr_head['keterangan'], ENT_QUOTES);




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
                        <th style="width:10px;">Cek</th>
                        <th style="width:50px;">NO PO</th>
                        <th style="width:100px;">NO PI</th>                            
                        <th style="width:50px;">PO Date</th>                            
                        <th style="width:100px;">Total PO</th>
                        <th style="width:100px;">Outstanding PO</th>
                        <th style="width:100px;">DP %</th>                            
                        <th style="width:100px;">DP Amount</th>
                        <th style="width:100px;">Currency</th>                            
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
                    $profit_center = '';           
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $start_date = date("Y-m-d",strtotime($_POST['start_date']));
                        $end_date = date("Y-m-d",strtotime($_POST['end_date']));
                        $profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                    }
                    $querys = mysqli_query($conn1,"select distinct pono from po_header where pono like '%PO/%'");
                    $rows = mysqli_fetch_array($querys);
                    $pono = $rows['pono'];

                    if ($profit_center == 'NAG') {

                    if(strpos($pono, 'PO/') !== false){
                        $sql = mysqli_query($conn1,"select id_po, a.no_po, podate, supplier, sub, tax, a.total total_po, (a.total - COALESCE(b.dp_value,0)) total, matauang, app, cancel, kode_pterms, tipe_com from (select po_header.id as id_po, po_header.pono as no_po, po_header.podate as podate, mastersupplier.Supplier as supplier, SUM(po_item.qty * po_item.price) as sub, SUM((po_item.qty * po_item.price) * (po_header.tax / 100)) as tax, SUM((po_item.qty * po_item.price) + ((po_item.qty * po_item.price) * (po_header.tax / 100))) as total, po_item.curr as matauang, po_header.app as app, po_item.cancel as cancel, masterpterms.kode_pterms, po_header_draft.tipe_com
                            from po_header 
                            inner join po_item on po_item.id_po = po_header.id
                            left JOIN po_header_draft on po_header_draft.id = po_header.id_draft
                            inner join mastersupplier on mastersupplier.Id_Supplier = po_header.id_supplier
                            inner join masterpterms on masterpterms.id = po_header.id_terms
                            where po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms like '%DP%' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com is null || po_header.app = 'A' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and supplier = '$nama_supp' and masterpterms.kode_pterms like '%DP%' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com = 'REGULAR' group by no_po) a left join (select no_po, SUM(dp_value) dp_value from ftr_dp where status != 'Cancel' and no_ftr_dp <> '$ftr_no_esc' GROUP BY no_po) b on b.no_po = a.no_po where (a.total - COALESCE(b.dp_value,0)) != 0");
                    }else{
                        $sql = mysqli_query($conn1,"select id_po, a.no_po, podate, supplier, sub, tax, a.total total_po, (a.total - COALESCE(b.dp_value,0)) total, matauang, app, cancel, kode_pterms, tipe_com from (select po_header.id as id_po, po_header.pono as no_po, jo.jo_no, po_header.podate as podate, mastersupplier.Supplier as supplier, masterpterms.kode_pterms, po_item.curr as matauang,
                            SUM(po_item.qty * po_item.price) as sub,  SUM((po_item.qty * po_item.price) * (po_header.tax / 100)) as tax, SUM((po_item.qty * po_item.price) + ((po_item.qty * po_item.price) * (po_header.tax / 100))) as total, po_header_draft.tipe_com
                            from po_header
                            inner join mastersupplier on mastersupplier.Id_Supplier = po_header.id_supplier
                            inner join masterpterms on masterpterms.id = po_header.id_terms
                            left join po_header_draft on po_header_draft.id = po_header.id_draft
                            inner join po_item on po_item.id_po = po_header.id
                            inner join jo on jo.id = po_item.id_jo
                            inner join mastergroup 
                            inner join mastersubgroup on mastersubgroup.id_group = mastergroup.id
                            inner join mastertype2 on mastertype2.id_sub_group = mastersubgroup.id
                            inner join mastercontents on mastercontents.id_type = mastertype2.id
                            inner join masterwidth on masterwidth.id_contents = mastercontents.id
                            inner join masterlength on masterlength.id_width = masterwidth.id
                            inner join masterweight on masterweight.id_length = masterlength.id
                            inner join mastercolor on mastercolor.id_weight = masterweight.id
                            inner join masterdesc on masterdesc.id_color = mastercolor.id
                            and po_item.id_gen = masterdesc.id
                            where po_header.app = 'A' and supplier = '$nama_supp' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and masterpterms.kode_pterms like '%DP%' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com is null || po_header.app = 'A' and supplier = '$nama_supp' and po_header.podate BETWEEN '$start_date' and '$end_date' and po_item.cancel = 'N' and masterpterms.kode_pterms like '%DP%' and masterpterms.aktif = 'Y' and po_header_draft.tipe_com = 'REGULAR' group by no_po) a left join (select no_po, SUM(dp_value) dp_value from ftr_dp where status != 'Cancel' and no_ftr_dp <> '$ftr_no_esc' GROUP BY no_po) b on b.no_po = a.no_po where (a.total - COALESCE(b.dp_value,0)) != 0");
                    }

                    while($row = mysqli_fetch_array($sql)){
                        $po = $row['no_po'];
                        $po_tampil[$po] = true;
                        $b = isset($ftr_baris[$po]) ? $ftr_baris[$po] : null;
                        echo barisEditFtrDp($po, $row['podate'], $row['total_po'], $row['total'], $row['matauang'], $row['supplier'], $b);
                    }






























                    }else{

                        $sql = pg_query($conn4,"select max(id) id_po, no_po, max(podate) podate, max(supplier) supplier, round(sum(sub),2) sub, round(sum(tax),2) tax, round(sum(sub + tax),2) total_po, round(sum(sub + tax),2) total, max(matauang) matauang, max(app) app, max(cancel) cancel, max(kode_pterms) kode_pterms, max(tipe_com) tipe_com from (select a.id, no_po, tanggal podate, c.nama_supplier supplier, (qty * harga_per_unit) sub, ((qty * harga_per_unit) * ppn/100) tax, currency matauang, 'A' app, 'N' cancel, '-' kode_pterms, '-' tipe_com from purchase_orders a INNER JOIN purchase_order_details b on b.purchase_order_id = a.id INNER JOIN master_supplier c on c.id = a.id_supplier where status_po = 'approved') a where podate BETWEEN '$start_date' and '$end_date' and upper(supplier) = '$nama_supp' GROUP BY no_po ");

                    while($row = pg_fetch_assoc($sql)){
                        $po = $row['no_po'];
                        $po_tampil[$po] = true;
                        $b = isset($ftr_baris[$po]) ? $ftr_baris[$po] : null;
                        echo barisEditFtrDp($po, $row['podate'], $row['total_po'], $row['total'], $row['matauang'], $row['supplier'], $b);
                    }






























                    }

                    /* Sisa baris milik dokumen ini yang tidak muncul di hasil
                       pencarian - ditampilkan apa adanya dari ftr_dp. */
                    foreach ($ftr_baris as $po_sisa => $b) {
                        if (isset($po_tampil[$po_sisa])) { continue; }
                        echo barisEditFtrDp($po_sisa, $b['tgl_po'], $b['total'], $b['total'], $b['curr'], $ftr_supp, $b);
                    }
                    ?>
                </tbody>                    
            </table>                    
            <div class="ftr-foot">
                <form id="form-simpan">
                    <div class="ftr-sum">
                        <div class="ftr-sum-row">
                            <span>Outstanding PO</span>
                            <input type="text" name="subtotal" id="subtotal" value="" placeholder="0.00" readonly>
                        </div>
                        <div class="ftr-sum-row">
                            <span>DP Amount</span>
                            <input type="text" name="pajak" id="pajak" value="" placeholder="0.00" readonly>
                        </div>
                        <div class="ftr-sum-row is-total">
                            <span>Balance</span>
                            <input type="text" name="total" id="total" value="" placeholder="0.00" readonly>
                        </div>
                    </div>

                    <div class="ftr-actions">
                        <button type="button" class="app-btn app-btn-primary" name="simpan" id="simpan"><i class="fa fa-floppy-o"></i> Save</button>
                        <button type="button" class="app-btn app-btn-danger" name="batal" id="batal" onclick="location.href='ftrdp.php'"><i class="fa fa-angle-double-left"></i> Back</button>
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

        /* Gulir mendatar dipasang di pembungkus TABELNYA saja - pembungkus
           bawaan DataTables juga memuat kotak pencarian & nomor halaman, yang
           tidak boleh ikut tergeser. */
        $('#mytable').parent().addClass('ftr-scroll');

        $("[data-toggle=tooltip]").tooltip();

    } );
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
                    text: 'Please choose a Profit Center first.' })
                    .then(function () { $("#profit_center").focus(); });
                return;
            }

            $('#h_profit_center').val(profit_center);

            $("#mymodal").modal("show");
        });
    });

function updateKodeFTR() {
    const profitCenter = document.getElementById('profit_center').value;
    const input = document.getElementById('noftrdp');
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
$("input[type=checkbox]").change(function(){
    // var sum_sub = 0;
    // var sum_dp = 0;
    // var ceklist = 0;
    // var sum_total = 0;
    $(this).closest('tr').find('td:eq(2) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(2) input').val("");       
    $(this).closest('tr').find('td:eq(6) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(6) input').val("");     
    $(this).closest('tr').find('td:eq(7) input').prop('disabled', true);
    $(this).closest('tr').find('td:eq(7) input').val("");                          
    $("input[type=checkbox]:checked").each(function () {        
    // var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
    // var dp = parseFloat($(this).closest('tr').find('td:eq(5) input').val(),10) || 0;    
    // var dp_value = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
    var select_pi = $(this).closest('tr').find('td:eq(2) input');
    var select_dp = $(this).closest('tr').find('td:eq(6) input');
    var select_dp_value = $(this).closest('tr').find('td:eq(7) input');        
    select_pi.prop('disabled', false);
    select_dp.prop('disabled', false);
    select_dp_value.prop('disabled', false);                                
    // sum_sub += price;
    // sum_dp += price * (dp /100);
    // sum_total = sum_sub - sum_dp;     
});
    // $("#subtotal").val(formatMoney(sum_sub));
    // $("#pajak").val(formatMoney(sum_dp));    
    // $("#total").val(formatMoney(sum_total));
    // $("#select").val("1");                    
});        
</script>

<script type="text/javascript">
    const formatCurrency = (str) => (""+str).replace(/[^\d.]/g, "").replace(/^(\d*\.)(.*)\.(.*)$/, '$1$2$3').replace(/\.(\d{2})\d+/, '.$1').replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    const parseCurrency = (str) => str.replace(/,/g,'');    
</script>

<script type="text/javascript">    
    $("input[name=txt_dp]").keyup(function(){
        var dp_value = 0;
        var sum_total_po = 0;
        var sum_total_po_show = 0;
        var sum_total_dp_value = 0;
        var total = 0;
        $("input[type=checkbox]:checked").each(function () {         
            var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
            var ost_po = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total'),10) || 0;
            var dp = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
            var select_dp = $(this).closest('tr').find('td:eq(6) input');
            if(dp >= 50){
                select_dp.val(50);
                sum_total_po += ost_po;
                sum_total_po_show += ost_po; 
                sum_total_dp_value = total_po/2;
                dp_value += sum_total_dp_value;
                total = sum_total_po - dp_value;            
            }else{
                sum_total_po += ost_po;
                sum_total_po_show += ost_po; 
                sum_total_dp_value = total_po * (dp / 100);
                dp_value += sum_total_dp_value;
                total = sum_total_po - dp_value;
            }
            parseFloat($(this).closest('tr').find('td:eq(7) input').val(formatMoney(sum_total_dp_value)),10);
            parseFloat($(this).closest('tr').find('td:eq(7) input').attr('data-value', sum_total_dp_value));         
        });               
        $("#subtotal").val(formatMoney(sum_total_po_show));        
        $("#pajak").val(formatMoney(dp_value));
        $("#total").val(formatMoney(total));                
    });    
</script>

<script type="text/javascript">
    $("input[name=txt_dp_value]").keyup(function(){
        var dp_code = 0;
        var sum_total_po = 0;
        var sum_total_po_show = 0;
        var sum_total_dp_value = 0;
        var total = 0;     
        $("input[type=checkbox]:checked").each(function () {                
            var total_po = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-total'),10) || 0;
            var ost_po = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total'),10) || 0;
            var dp_value = parseFloat($(this).closest('tr').find('td:eq(7) input').val(),10) || 0;
            var select_dp_value = $(this).closest('tr').find('td:eq(7) input');
            if(dp_value >= (total_po / 2)){
                select_dp_value.val(total_po/2);
                sum_total_po += ost_po;     
                sum_total_po_show += ost_po;   
                dp_code = 50;
                sum_total_dp_value += total_po/2;
                total = sum_total_po - sum_total_dp_value;
            }else{
                sum_total_po += ost_po;  
                sum_total_po_show += ost_po;      
                dp_code = (dp_value / total_po) * 50;
                sum_total_dp_value += dp_value;
                total = sum_total_po - sum_total_dp_value;
            }
            parseFloat($(this).closest('tr').find('td:eq(6) input').val(formatMoney(dp_code)),10);
            parseFloat($(this).closest('tr').find('td:eq(67) input').attr('data-value', sum_total_dp_value));        
        });        
        $("#subtotal").val(formatMoney(sum_total_po_show));
        $("#pajak").val(formatMoney(sum_total_dp_value));
        $("#total").val(formatMoney(total));                        
    });
</script>

<script type="text/javascript">
// get all number fields
var numInputs = document.querySelectorAll('input[type="number"]');
var text_value = document.querySelectorAll('input[name="txt_dp_value"]');
// Loop through the collection and call addListener on each element
Array.prototype.forEach.call(numInputs, addListener); 
Array.prototype.forEach.call(text_value, addListener);

function addListener(elm,index){
  elm.setAttribute('min', 0);  // set the min attribute on each field
  
  elm.addEventListener('keypress', function(e){  // add listener to each field 
   var key = !isNaN(e.charCode) ? e.charCode : e.keyCode;
   str = String.fromCharCode(key); 
   if (str.localeCompare('-') === 0){
     event.preventDefault();
 }

});

  
}
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
           Pesan "data saved successfully" pun dulu selalu muncul, bahkan
           ketika tidak ada satu pun baris yang tersimpan. */
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

        /* No PI & DP % wajib di SETIAP baris yang dicentang - diperiksa per
           baris, bukan lewat satu id #txt_pi / #txt_dp seperti dulu. Id itu
           dipakai berulang di tiap baris, jadi yang terbaca selalu baris
           PERTAMA saja: baris kedua yang kosong lolos begitu saja. */
        var $kosongPi = null, $kosongDp = null;
        $dipilih.each(function () {
            var $pi = $(this).closest('tr').find('td:eq(2) input');
            var $dp = $(this).closest('tr').find('td:eq(6) input');
            if (!$kosongPi && $.trim($pi.val()) === '') { $kosongPi = $pi; }
            if (!$kosongDp && $.trim($dp.val()) === '') { $kosongDp = $dp; }
        });
        if ($kosongPi) {
            Swal.fire({ icon: 'warning', title: 'PI number is required',
                text: 'Fill in the PI number for every selected PO. Enter "-" if there is none.' })
                .then(function () { $kosongPi.focus(); });
            return;
        }
        if ($kosongDp) {
            Swal.fire({ icon: 'warning', title: 'DP % is required',
                text: 'Fill in the DP percentage for every selected PO.' })
                .then(function () { $kosongDp.focus(); });
            return;
        }

        var jumlah = $dipilih.length;
        var noFtr  = document.getElementById('noftrdp').value;
        var totalTampil = $("#total").val() || '0.00';

        /* KONFIRMASI SEBELUM SIMPAN. Menu daftar FTR tidak punya tombol hapus -
           sekali tersimpan, pembetulannya harus lewat Cancel. Jadi isinya
           diperlihatkan dulu, supaya salah pilih cara bayar atau salah centang
           PO masih bisa dibatalkan di sini. */
        Swal.fire({
            icon: 'question',
            title: 'Save changes to this FTR DP?',
            html: '<div style="text-align:left;font-size:13px;line-height:1.95;color:#475569">'
                + '<div style="font-size:15px;font-weight:700;color:#1e3a8a;margin-bottom:6px">' + noFtr + '</div>'
                + 'Payment Method : <b>' + payment_method + '</b><br>'
                + 'Item Type : <b>' + item_type + '</b><br>'
                + 'PO rows : <b>' + jumlah + '</b><br>'
                + 'Balance : <b>' + totalTampil + '</b>'
                + '</div>',
            showCancelButton: true,
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'Cancel'
        }).then(function (jawab) {
            if (!jawab.isConfirmed) { return; }
            kirimFtrDp($dipilih, jumlah, noFtr, payment_method, item_type, tgl_bayar);
        });
    });

    /* Pengiriman dipisah jadi fungsi sendiri. Kalau digabung ke dalam .then(),
       seluruh blok kirim ikut masuk satu tingkat lebih dalam dan susunannya
       jadi sulit dibaca. */
    function kirimFtrDp($dipilih, jumlah, noFtr, payment_method, item_type, tgl_bayar) {
        Swal.fire({
            title: 'Saving...', text: 'Updating ' + jumlah + (jumlah === 1 ? ' row.' : ' rows.'),
            allowOutsideClick: false, allowEscapeKey: false,
            didOpen: function () { Swal.showLoading(); }
        });

        /* Seluruh baris dikemas jadi SATU muatan. Server menghapus baris lama
           dan menulis yang baru di dalam satu transaksi, jadi dokumen tidak
           pernah berada dalam keadaan setengah terganti. */
        var baris = [];
        $dipilih.each(function () {
            var $row     = $(this).closest('tr');
            var total    = parseFloat($row.find('td:eq(5)').attr('data-total')) || 0;
            var dp_value = parseFloat($row.find('td:eq(7) input').attr('data-value')) || 0;
            baris.push({
                no_po:    $row.find('td:eq(1)').attr('value'),
                no_pi:    $row.find('td:eq(2) input').val(),
                tgl_po:   $row.find('td:eq(3)').attr('value'),
                curr:     $row.find('td:eq(8)').attr('value'),
                total:    total,
                dp_code:  $row.find('td:eq(6) input').val(),
                dp_value: dp_value,
                balance:  total - dp_value
            });
        });

        $.ajax({
            type: 'POST',
            url: 'update_ftrdp.php',
            dataType: 'json',
            data: {
                noftrdp: noFtr,
                tglftrdp: document.getElementById('tanggal').value,
                tgl_bayar: tgl_bayar,
                keterangan: document.getElementById('memo').value,
                payment_method: payment_method,
                item_type: item_type,
                profit_center: document.getElementById('profit_center').value,
                nama_supp: document.getElementById('txt_supp').value,
                edit_user: '<?php echo $user; ?>',
                baris: JSON.stringify(baris)
            }
        }).done(function (jwb) {
            if (!jwb || jwb.ok !== true) {
                Swal.fire({ icon: 'error', title: 'Failed to save',
                    text: (jwb && jwb.message) ? jwb.message : 'The server did not confirm the change.' });
                return;
            }
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                html: '<div style="font-size:12.5px;color:#64748b;letter-spacing:.04em">FTR DP NUMBER</div>'
                    + '<div style="font-size:18px;font-weight:700;color:#1e3a8a;margin:5px 0 12px">' + noFtr + '</div>'
                    + '<div style="font-size:13px;color:#475569">' + jwb.rows
                    + (jwb.rows === 1 ? ' PO row saved.' : ' PO rows saved.') + '</div>',
                confirmButtonText: 'OK'
            }).then(function () { window.location = 'ftrdp.php'; });
        }).fail(function (xhr) {
            var pesan = '';
            try { pesan = (JSON.parse(xhr.responseText) || {}).message || ''; } catch (err) { pesan = ''; }
            Swal.fire({ icon: 'error', title: 'Failed to save',
                text: pesan || ('HTTP ' + xhr.status + ' - ' + (xhr.responseText || 'no response')) });
        });
    }





























































</script>

<script type="text/javascript">
    $("#select_all").click(function() {
      var c = this.checked;
      $(':checkbox').prop('checked', c);
  });  
</script>

<script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(1)', function(){                
        $('#mymodalpo').modal('show');
        var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
        var no_bpb = $(this).closest('tr').find('td:eq(8)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(3)').attr('value');
        var tgl_po2 = $(this).closest('tr').find('td:eq(3)').text();
        var supp = $(this).closest('tr').find('td:eq(8)').attr('value');
        var curr = $(this).closest('tr').find('td:eq(7)').attr('value');   

        $.ajax({
            type : 'post',
            url : 'ajaxpodp.php',
            data : {'no_po': no_po, 'no_bpb':no_bpb},
            success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#txt_po').html(no_po);
        $('#txt_tgl_po').html('Tgl PO : ' + tgl_po2 + '');
        $('#txt_supp2').html('Supplier : ' + supp + '');
        $('#txt_curr').html('Currency : ' + curr + '');                               
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
