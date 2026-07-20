<style type="text/css">
    label {
        font-size: 13px;;
    }

    input {
        font-size: 13px;;
    }
</style>

<div class="container-fluid mt-2 p-3" style="padding-left: 2rem; padding-right: 2rem;">
  <div class="card mb-3" style="border: none; background: transparent;">
  <form id="inst-form-data" method="post">
    <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #191970, #1e90ff); border: none; font-weight: 700; letter-spacing: 0.4px; color: #fff; cursor: pointer;" data-toggle="collapse" data-target="#inst_header_card_body" aria-expanded="true" aria-controls="inst_header_card_body">
                    <span><i class="fa fa-file-text-o mr-2"></i> FORM PAYMENT VOUCHER - INSTALLMENT</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="inst_header_card_body">
                <div class="card-body p-2">
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="inst_nokontrabon"><b>No Payment Voucher</b></label>
                            <?php
                            $sql = mysqli_query($conn2,"select CONCAT(
                                'PV-AP/INS/',
                                DATE_FORMAT(CURRENT_DATE(), '%Y'), '/',
                                DATE_FORMAT(CURRENT_DATE(), '%m'), '/',
                                LPAD(
                                COALESCE(MAX(CAST(RIGHT(no_kbon, 5) AS UNSIGNED)), 0) + 1,
                                5, '0'
                                )
                            ) nomor from kontrabon_h WHERE no_kbon LIKE 'PV-AP/INS/%' AND YEAR(tgl_kbon) = YEAR (CURRENT_DATE())");
                            $row = mysqli_fetch_array($sql);
                            $kodeBarang = $row['nomor'];

                            echo'<input type="text" readonly style="font-size: 13px;;" class="form-control form-control-sm" id="inst_nokontrabon" name="nokontrabon" value="'.$kodeBarang.'">'
                            ?>

                            <input type="hidden" style="font-size: 13px;;" name="unik_code" id="inst_unik_code" class="form-control form-control-sm" 
                            value="<?php 
                            $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
                            $shuffle  = substr(str_shuffle($karakter), 0, 16);
                            echo $shuffle; ?>" autocomplete='off' readonly>
                        </div>

                        <div class="col-md-2 mb-3">            
                            <label for="inst_tanggal"><b>Payment Voucher Date <i style="color: red;">*</i></b></label>
                            <input type="text" style="font-size: 13px;;" name="tanggal" id="inst_tanggal" class="form-control form-control-sm tanggal" onchange="ubahtanggalInst(this.value)"
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

                            if(!empty($tanggal) && $tanggal != '1970-01-01') {

                              echo date("Y-m-d",strtotime($tanggal));      
                          }
                          else{
                            echo date("Y-m-d");
                        } ?>">
                        <input type="hidden" style="font-size: 13px;;" name="tgl_perhitungan" id="inst_tgl_perhitungan" class="form-control form-control-sm">
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm" name="txt_top" id="inst_txt_top" 
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
                        <input type="hidden" style="font-size: 13px;;" name="tanggal3" id="inst_tanggal3" class="form-control form-control-sm"
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

                        <input type="hidden" style="font-size: 13px;;" name="tanggal4" id="inst_tanggal4" class="form-control form-control-sm"
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
                        <label for="inst_profit_center"><b>Profit Center <i style="color: red;">*</i></b></label>
                        <select class="form-control selectpicker" name="profit_center" id="inst_profit_center" data-dropup-auto="false" data-live-search="true" onchange="updateNoKontraBonInst()">
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

                        <input type="hidden" readonly style="font-size: 13px;;" class="form-control form-control-sm" id="inst_jurnal" name="jurnal" 
                        value="0" placeholder="<?php echo "KONTRA BON" ?>">
                    </div>

                   <!--  <div class="col-md-2 mb-3">
                        <label for="inst_matauang"><b>Currency</b></label> -->
                        <input type="hidden" readonly class="form-control form-control-sm" id="inst_matauang" name="matauang" value="">
                   <!--  </div>  -->

                    <!-- <div class="col-md-3 mb-3">
                        <label for="inst_txt_inv"><b>No Supplier Invoice <i style="color: red;">*</i></b></label>   -->
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm" id="inst_txt_inv" name="txt_inv"
                        value="<?php
                        $txt_inv = isset($_POST['txt_inv']) ? $_POST['txt_inv']: null;
                        echo $txt_inv;
                        ?>" required>
                    <!-- </div> -->

                    <!-- <div class="col-md-2 mb-3">
                        <label for="inst_txt_tglsi"><b>Supplier Invoice Date <i style="color: red;">*</i></b></label> -->
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm tanggal" name="txt_tglsi" id="inst_txt_tglsi"
                        value="<?php
                        if(!empty($_POST['txt_tglsi'])) {
                            echo date("Y-m-d",strtotime($_POST['txt_tglsi']));
                        }
                        else{
                            echo date("Y-m-d");
                        } ?>">
                   <!--  </div> -->

                   <!--  <div class="col-md-3 mb-3">
                        <label for="inst_no_faktur"><b>No Tax Invoice <i style="color: red;">*</i></b></label>           -->
                        <input type="hidden" style="font-size: 13px;;" class="form-control form-control-sm" id="inst_no_faktur" name="no_faktur"
                        value="<?php
                        $no_faktur = isset($_POST['no_faktur']) ? $_POST['no_faktur']: null;
                        echo $no_faktur;
                        ?>" required>
                    <!-- </div> -->

                    <div class="col-md-2 mb-3">            
                        <label for="inst_txt_tgltempo"><b>Due Date <i style="color: red;">*</i></b></label>   
                        <input type="text" style="font-size: 13px;;" class="form-control form-control-sm tanggal1" name="txt_tgltempo" id="inst_txt_tgltempo" 
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

                        if(!empty($nama_supp) && $top != 0 && $startdate != '1970-01-01') {
                            echo date("Y-m-d",strtotime($startdate . "+$top days"));
                        }
                        else{
                            echo date("Y-m-d");
                        } 


                        ?>">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="inst_jml_cicilan"><b>Jumlah Cicilan <i style="color: red;">*</i></b></label>
                        <input type="number" min="1" step="1" style="font-size: 13px;;" class="form-control form-control-sm" name="jml_cicilan" id="inst_jml_cicilan" value="<?php echo isset($_POST['jml_cicilan']) ? htmlspecialchars($_POST['jml_cicilan']) : ''; ?>" placeholder="cth: 3">
                        <input type="hidden" id="inst_top_cicilan" value="">
                    </div>
                    <?php
                    /* Load bank accounts dari master_supplier_bank berdasarkan supplier terpilih */
                    $supplier_banks = [];
                    if (!empty($nama_supp)) {
                        $q_sb = mysqli_query($conn2,
                            "SELECT msb.id, msb.bank_account, msb.bank_currency, msb.bank_name, msb.beneficiary_name
                             FROM master_supplier_bank msb
                             INNER JOIN mastersupplier ms ON ms.id_supplier = msb.id_supplier
                             WHERE ms.Supplier = '" . mysqli_real_escape_string($conn2, $nama_supp) . "'
                               AND msb.status = 'Active'
                             ORDER BY msb.bank_name ASC"
                        );
                        while ($r = mysqli_fetch_assoc($q_sb)) {
                            $supplier_banks[] = $r;
                        }
                    }
                    ?>

                    <!-- Bank Account selectpicker -->
                    <div class="col-md-3 mb-3">
                        <label for="inst_sel_bank_account"><b>Bank Account <i style="color: red;">*</i></b></label>
                        <select class="form-control form-control-sm selectpicker"
                                id="inst_sel_bank_account" name="sel_bank_account"
                                data-live-search="true" data-dropup-auto="false" data-size="8">
                            <option value="">-- Select Bank Account --</option>
                            <option value="-">- (Tidak ada rekening)</option>
                            <?php foreach ($supplier_banks as $b): ?>
                            <option value="<?= htmlspecialchars($b['id']) ?>"
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
                        <label for="inst_disp_bankname"><b>Bank Name</b></label>
                        <input type="text" readonly class="form-control form-control-sm bg-light"
                               id="inst_disp_bankname" name="disp_bankname" placeholder="-">
                    </div>

                    <!-- Beneficiary Name (readonly auto-fill) -->
                    <div class="col-md-3 mb-3">
                        <label for="inst_disp_beneficiary"><b>Beneficiary Name</b></label>
                        <input type="text" readonly class="form-control form-control-sm bg-light"
                               id="inst_disp_beneficiary" name="disp_beneficiary" placeholder="-">
                    </div>

                    <!-- Currency (readonly auto-fill) -->
                    <div class="col-md-2 mb-3">
                        <label for="inst_disp_currency"><b>Currency</b></label>
                        <input type="text" readonly class="form-control form-control-sm bg-light"
                               id="inst_disp_currency" name="disp_currency" placeholder="-">
                    </div>

                    <!-- Hidden backward-compat -->
                    <input type="hidden" id="inst_txt_bank_supp" name="txt_bank_supp">
                    <input type="hidden" id="inst_txt_akun_supp" name="txt_akun_supp">

                     <div class="col-md-3 mb-3">
                        <label for="inst_ir_number"><b>Invoice Received Number <i style="color: red;">*</i></b></label>
                        <select class="form-control selectpicker" name="ir_number" id="inst_ir_number" data-dropup-auto="false" data-size="5" data-live-search="true">
                            <option value="-" selected="true">-</option>                                                 
                            <?php
                            $ir_number = isset($_POST['ir_number']) ? $_POST['ir_number']: null; 
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;              
                            $sql = mysqli_query($conn1,"select doc_number, nama_supp, total_amount from ir_invoice_supp_h where status != 'Cancel' and nama_supp = '$nama_supp'");
                            while ($row = mysqli_fetch_array($sql)) {
                                $data = $row['doc_number'];
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
                        <select class="form-control form-control-sm selectpicker" id="inst_from_account" name="from_account" data-live-search="true">
                            <option value="">Select Account</option>

                            <?php
                            $sql = mysqli_query($conn1, "select bank_name as bank,curr,bank_account as account,RIGHT(bank_account,4) as kode, nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active'");
                            while ($row = mysqli_fetch_assoc($sql)) {
                                echo "<option value='" . $row['account'] . "' data-bank='" . $row['bank'] . "' data-currency='" . $row['curr'] . "' data-namapc='" . $row['nama_pc'] . "' data-kodepc='" . $row['kode_pc'] . "'  data-kodebank='" . $row['b_code'] . "'>" . $row['account'] . " </option>";
                            }
                            ?>

                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label><b>From Bank Name <i style="color: red;">*</i></b></label>
                        <input type="text" class="form-control form-control-sm bg-light" id="inst_from_bank" name="from_bank" readonly>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label><b>From Bank Currency <i style="color: red;">*</i></b></label>
                        <input type="text" class="form-control form-control-sm bg-light" id="inst_from_bank_curr" name="from_bank_curr" readonly>
                    </div>


                    <div class="col-md-6 mb-3">
                        <label for="inst_nama_supp"><b>Supplier</b></label>            
                        <div class="input-group">
                            <input type="text" readonly style="font-size: 13px;" class="form-control" name="txt_supp" id="inst_txt_supp" 
                            value="<?php 
                            $nama_supp = isset($_POST['nama_supp']) ? $_POST['nama_supp']: null;
                            echo $nama_supp; 
                            ?>">


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
                                id="inst_mysupp"
                                value="Select">
                                <input type="hidden" name="bpbvalue" id="inst_bpbvalue" value="">      
                            </div>

                        </div>
                    </div>


                </div>
            </div>
            </div>
        </div>
    </form>

    <form id="inst-form-simpan">
        <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #1E3A8A; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #1E3A8A; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#inst_bpb_card_body" aria-expanded="true" aria-controls="inst_bpb_card_body">
                    <span><i class="fa fa-list-alt"></i> Data BPB</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="inst_bpb_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="inst_mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
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

                                $sql = mysqli_query($conn2,"select a.no_bpb,a.curr, a.pono, a.tgl_bpb, a.tgl_po, SUM(a.qty * a.price) as sub, if(a.qty is null,SUM((a.qty * a.price) * (a.tax / 100)) ,SUM(((a.qty) * a.price) * (a.tax / 100))) as tax, if(a.qty is null,SUM((a.qty * a.price) + ((a.qty * a.price) * (a.tax / 100))) ,SUM((a.qty * a.price) + (((a.qty) * a.price) * (a.tax / 100)))) as total, a.top, a.confirm1, a.confirm2, a.supplier, a.tgl_po,a.id_item,a.id_supplier,b.mattype,if(b.matclass like '%ACCESORIES%','ACCESORIES',b.matclass) matclass,if(b.n_code_category is null,'-',b.n_code_category) n_code_category from bpb_new a INNER JOIN masteritem b on b.id_item = a.id_item  where a.supplier = '$nama_supp' and a.tgl_bpb between '$start_date' and '$end_date' and a.is_invoiced != 'Invoiced' and a.confirm2 != '' and status != 'Cancel' and a.profit_center = '$profit_center' group by a.no_bpb");


                                while($row = mysqli_fetch_array($sql)){
                                    $bpb = $row['no_bpb'];
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
                                        <td style="width:10px;"><input type="checkbox" class="chkA_inst"  id="inst_select" name="select_inst[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                                        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                                        <td style="width:100px;">                            
                                        <select name="combo_pph_inst" id="inst_combo_pph" disabled>
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
                                        <td style="width:10px;"><input type="checkbox" class="chkA_inst" id="inst_select" name="select_inst[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                                        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                                        <td style="width:100px;">                            
                                        <select name="combo_pph_inst" id="inst_combo_pph" disabled>
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
                                        <td style="width:10px;"><input type="checkbox" class="chkA_inst" id="inst_select" name="select_inst[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" value="'.$row['no_bpb'].'">'.$row['no_bpb'].'</td>
                                        <td style="width:50px;" value="'.$row['pono'].'">'.$row['pono'].'</td>                            
                                        <td style="width:100px;" dates="'.date("Y-m-d",strtotime($row['tgl_bpb'])).'" value="'.$row['tgl_bpb'].'">'.date("d-M-Y",strtotime($row['tgl_bpb'])).'</td>                            
                                        <td class="dt_price" style="width:100px;text-align:right;" data-link="1" data-subtotal="'.$sub.'">'.number_format($sub,2).'</td>
                                        <td class="dt_tax" style="width:100px;text-align:right;" data-tax="'.$tax.'">'.number_format($tax,2).'</td>
                                        <td style="width:100px;">                            
                                        <select name="combo_pph_inst" id="inst_combo_pph" disabled>
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
                        <label for="inst_subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total BPB</u></b></label>
                        <div class="col-md-2 mb-3">                              
                            <input type="text" class="form-control form-control-sm" name="subtotal" id="inst_subtotal" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                            <input type="hidden" name="subtotal_h" id="inst_subtotal_h" value="">
                            <input type="hidden" name="subtotal_h1" id="inst_subtotal_h1" value="">
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #2563EB; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #2563EB; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#inst_ro_card_body" aria-expanded="true" aria-controls="inst_ro_card_body">
                    <span><i class="fa fa-undo"></i> Data Return (RO)</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="inst_ro_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="inst_mytable1" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;;text-align:center;">
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

            // $querys = mysqli_query($conn2,"select curr,no_bppb, tgl_bppb, no_ro, no_bpb, sum((qty * price) + ((qty * price) * (tax /100))) as total,round(sum((qty * price) + ((qty * price) * (tax /100))),2) as total2 from bppb_new where supplier = '$nama_supp' and status != 'Cancel' GROUP BY no_bppb");

                                $querys = mysqli_query($conn2,"select * from (select curr,no_bppb, tgl_bppb, no_ro, no_bpb, sum((qty * price) + ((qty * price) * (tax /100))) as total,round(sum((qty * price) + ((qty * price) * (tax /100))),2) as total2 from bppb_new where supplier = '$nama_supp' and status != 'Cancel' and no_kbon is null and status = 'GMF-PCH' GROUP BY no_bppb) a where total2 > 0 order by no_bppb asc");


                                while($row1 = mysqli_fetch_array($querys)){
                                    $ro_no = isset($row1['no_ro']) ? $row1['no_ro'] : null ;
                                    $bpb_rtn = isset($row1['no_bppb']) ? $row1['no_bppb'] : null ;
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
                                        <td style="width:10px;"><input type="checkbox" class="chkB_inst" id="inst_select" name="select_inst[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                        <td style="width:50px;" data-ro="'.$row1['no_ro'].'">'.$row1['no_ro'].'</td>
                                        <td style="width:50px;" valuess="'.$row1['no_bppb'].'">'.$row1['no_bppb'].'</td>
                                        <td style="width:100px;" valuess="'.$row1['tgl_bppb'].'">'.date("d-M-Y",strtotime($row1['tgl_bppb'])).'</td>                            
                                        <td style="width:50px;" valuess="'.$row1['no_bpb'].'">'.$row1['no_bpb'].'</td>                            
                                        <td style="width:100px;text-align:right;" data-total-ro="'.round($sisaro,2).'">'.number_format($sisaro,2).'</td>
                                        <td style="width:100px;">
                                        <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="inst_txt_amount" name="txt_amount" value="'.round($sisaro,2).'" disabled>
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
                            <label for="inst_potongan" class="col-form-label" style="width: 150px;font-size: 13px;;"><b><u>Total Return</u></b></label>
                            <div class="col-md-2 mb-3">                              
                                <input type="text" class="form-control form-control-sm" name="potongan" id="inst_potongan" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                                <input type="hidden" name="potongan_h" id="inst_potongan_h" value="">
                                <input type="hidden" name="h_mattype" id="inst_h_mattype" value="">
                                <input type="hidden" name="h_matclass" id="inst_h_matclass" value="">
                                <input type="hidden" name="h_code_ctg" id="inst_h_code_ctg" value="">
                                <input type="hidden" name="h_cus_ctg" id="inst_h_cus_ctg" value="">
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #60A5FA; border-radius: 10px;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #2779bd; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#inst_ftr_card_body" aria-expanded="true" aria-controls="inst_ftr_card_body">
                    <span><i class="fa fa-file-text-o"></i> Data FTR (CBD/DP)</span>
                    <i class="fa fa-chevron-up"></i>
                </div>
                <div class="collapse show" id="inst_ftr_card_body">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="inst_mytable2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 13px;; text-align:center;">
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
                                    select no_cbd no_ftr_cbd, NULL tgl_ftr_cbd, h.nama_supp supp, d.no_po, d.tgl_po, NULL no_pi, h.curr, SUM(d.total) total, h.no_kbon, h.tgl_kbon, NULL no_payment, NULL tgl_payment, h.no_kbon no_pv, bh.no_bankout, bh.bankout_date, h.no_coa coa from kontrabon_h_cbd h INNER JOIN kontrabon_cbd d on d.no_kbon = h.no_kbon INNER JOIN b_bankout_det bd on bd.no_reff = h.no_kbon and bd.type_pv = 'CBD' INNER JOIN b_bankout_h bh on bh.no_bankout = bd.no_bankout where bh.status != 'Cancel' and h.nama_supp = '$nama_supp' GROUP BY d.no_kbon, d.no_cbd
                                    UNION ALL
                                    select no_dp no_ftr_cbd, NULL tgl_ftr_cbd, h.nama_supp supp, d.no_po, d.tgl_po, NULL no_pi, h.curr, SUM(d.total) total, h.no_kbon, h.tgl_kbon, NULL no_payment, NULL tgl_payment, h.no_kbon no_pv, bh.no_bankout, bh.bankout_date, h.no_coa coa from kontrabon_h_dp h INNER JOIN kontrabon_dp d on d.no_kbon = h.no_kbon INNER JOIN b_bankout_det bd on bd.no_reff = h.no_kbon and bd.type_pv = 'CBD' INNER JOIN b_bankout_h bh on bh.no_bankout = bd.no_bankout where bh.status != 'Cancel' and h.nama_supp = '$nama_supp' GROUP BY d.no_kbon, d.no_dp
                                    order by tgl_ftr_cbd asc) a LEFT JOIN (select no_ftr, a.no_po, no_bankout, total_ftr from kontrabon_ftr a INNER JOIN kontrabon_h b on b.no_kbon = a.no_kbon WHERE a.nama_supp = '$nama_supp' and b.status != 'Cancel') b on b.no_ftr = a.no_ftr_cbd and b.no_po = a.no_po and b.no_bankout = a.no_bankout where (a.total - COALESCE(b.total_ftr,0)) > 0");


                                while($row_ftr = mysqli_fetch_array($query_ftr)){
                                    echo '<tr>
                                    <td style="width:10px;"><input type="checkbox" class="chkC_inst" id="inst_select" name="select_inst[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                        
                                    <td style="width:50px;" no-ftr="'.$row_ftr['no_ftr_cbd'].'">'.$row_ftr['no_ftr_cbd'].'</td>
                                    <td style="width:50px;" po-ftr="'.$row_ftr['no_po'].'">'.$row_ftr['no_po'].'</td>
                                    <td style="width:100px;" tglpo-ftr="'.$row_ftr['tgl_po'].'">'.date("d-M-Y",strtotime($row_ftr['tgl_po'])).'</td>                            
                                    <td style="width:50px;" pi-ftr="'.$row_ftr['no_pi'].'">'.$row_ftr['no_pi'].'</td>                            
                                    <td style="width:100px;text-align:right;" total-ftr="'.round($row_ftr['total'],2).'">'.number_format($row_ftr['total'],2).'</td>
                                    <td style="width:100px;">
                                    <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="inst_amount_ftr" name="amount_ftr" value="'.round($row_ftr['total'],2).'" disabled>
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
                        <label for="inst_subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b><u>Total DP / CBD</u></b></label>
                        <div class="col-md-2 mb-3">                              
                            <input type="text" class="form-control form-control-sm" name="ttl_dp" id="inst_ttl_dp" value="" placeholder="0.00" style="font-size: 13px;;text-align: right;" readonly>
                            <input type="hidden" name="ttl_dp_h" id="inst_ttl_dp_h" value="">

                        </div>
                    </div>
                </div>
                </div>
            </div>

            <div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #7c3aed; border-radius: 10px;">
              <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #7c3aed; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#inst_potongan_card_body" aria-expanded="true" aria-controls="inst_potongan_card_body">
                <span><i class="fa fa-calculator"></i> Potongan</span>
                <i class="fa fa-chevron-up"></i>
              </div>
              <div class="collapse show" id="inst_potongan_card_body">
              <div class="card-body p-2">
                <div class="row">
                  <div class="col-md-7 border-end pe-4">

                    <div class="row mb-2 align-items-center">
                      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Laba Rugi Kurs</u></b></label>
                      <div class="col-3">
                        <input type="number" class="form-control form-control-sm text-right" name="labarugi" id="inst_labarugi" placeholder="0.00">
                    </div>
                    <div class="col-4">
                        <input type="text" class="form-control form-control-sm text-right" name="labarugi_h" id="inst_labarugi_h" placeholder="0.00" readonly>
                    </div>
                    <div class="col-2"></div>
                </div>

                <div class="row mb-2 align-items-center">
                  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Quantity</u></b></label>
                  <div class="col-3">
                    <input type="number" class="form-control form-control-sm text-right" name="selisihqty" id="inst_selisihqty" placeholder="0.00">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm text-right" name="selisihqty_h" id="inst_selisihqty_h" placeholder="0.00" readonly>
                </div>
                <div class="col-2"></div>
            </div>

            <div class="row mb-2 align-items-center">
              <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Selisih Harga</u></b></label>
              <div class="col-3">
                <input type="number" class="form-control form-control-sm text-right" name="selisihharga" id="inst_selisihharga" placeholder="0.00">
            </div>
            <div class="col-4">
                <input type="text" class="form-control form-control-sm text-right" name="selisihharga_h" id="inst_selisihharga_h" placeholder="0.00" readonly>
            </div>
            <div class="col-2"></div>
        </div>

        <div class="row mb-2 align-items-center">
          <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Materai</u></b></label>
          <div class="col-3">
            <input type="number" min="0" class="form-control form-control-sm text-right" name="materai" id="inst_materai" placeholder="0.00">
        </div>
        <div class="col-4">
            <input type="text" class="form-control form-control-sm text-right" name="materai_h" id="inst_materai_h" placeholder="0.00" readonly>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Potongan Pembelian</u></b></label>
      <div class="col-3">
        <input type="number" max="0" class="form-control form-control-sm text-right" name="potongbeli" id="inst_potongbeli" placeholder="0.00">
    </div>
    <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="potongbeli_h" id="inst_potongbeli_h" placeholder="0.00" readonly>
    </div>
    <div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya Expedisi</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="ekspedisi" id="inst_ekspedisi" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="ekspedisi_h" id="inst_ekspedisi_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Biaya MOQ</u></b></label>
  <div class="col-3">
    <input type="number" min="0" class="form-control form-control-sm text-right" name="moq" id="inst_moq" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="moq_h" id="inst_moq_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Koreksi PPN</u></b></label>
  <div class="col-3">
    <input type="number" class="form-control form-control-sm text-right" name="potongan_ppn" id="inst_potongan_ppn" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongan_ppn_h" id="inst_potongan_ppn_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Koreksi PPh</u></b></label>
  <div class="col-3">
    <input type="number" class="form-control form-control-sm text-right" name="potongan_pph" id="inst_potongan_pph" placeholder="0.00">
</div>
<div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="potongan_pph_h" id="inst_potongan_pph_h" placeholder="0.00" readonly>
</div>
<div class="col-2"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Jumlah Potongan</u></b></label>
  <div class="col-7">
    <input type="text" class="form-control form-control-sm text-right" name="jumlahpotong" id="inst_jumlahpotong" placeholder="0.00" readonly>
    <input type="hidden" name="jml_potong" id="inst_jml_potong">
</div>
<div class="col-2"></div>
</div>

<input type="hidden" name="ttl_sub" id="inst_ttl_sub">
<input type="hidden" name="ttl_sub1" id="inst_ttl_sub1">
<input type="hidden" name="ttl_sub2" id="inst_ttl_sub2">
<input type="hidden" name="ttl_sub3" id="inst_ttl_sub3">
<input type="hidden" name="ttl_sub4" id="inst_ttl_sub4">
<input type="hidden" name="ttl_sub5" id="inst_ttl_sub5">
<input type="hidden" name="ttl_sub6" id="inst_ttl_sub6">
<input type="hidden" name="ttl_sub7" id="inst_ttl_sub7">

</div>

<div class="col-md-5 ps-4">

    <div class="row mb-2 align-items-center">
      <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPn)</u></b></label>
      <div class="col-4">
        <input type="text" class="form-control form-control-sm text-right" name="pajak" id="inst_pajak" placeholder="0.00" readonly>
        <input type="hidden" name="pajak_h" id="inst_pajak_h">
    </div>
    <div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>PPN setelah Potongan</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pajak_setelah_potongan" id="inst_pajak_setelah_potongan" placeholder="0.00" readonly>
    <input type="hidden" name="pajak_setelah_potongan_h" id="inst_pajak_setelah_potongan_h">
</div>
<div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Tax (PPh)</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pph" id="inst_pph" placeholder="0.00" readonly>
    <input type="hidden" name="pph_h" id="inst_pph_h">
</div>
<div class="col-5"></div>
</div>

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>PPh setelah Potongan</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="pph_setelah_potongan" id="inst_pph_setelah_potongan" placeholder="0.00" readonly>
    <input type="hidden" name="pph_setelah_potongan_h" id="inst_pph_setelah_potongan_h">
</div>
<div class="col-5"></div>
</div>

<!-- <div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Total CBD / DP</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="ttl_dp" id="inst_ttl_dp" placeholder="0.00" readonly>
    <input type="hidden" name="ttl_dp_h" id="inst_ttl_dp_h">
</div>
<div class="col-5"></div>
</div> -->

<div class="row mb-2 align-items-center">
  <label class="col-3 col-form-label" style="font-size: 13px;;"><b><u>Total</u></b></label>
  <div class="col-4">
    <input type="text" class="form-control form-control-sm text-right" name="total" id="inst_total" placeholder="0.00" readonly>
    <input type="hidden" name="total_h" id="inst_total_h">
    <input type="hidden" name="po" id="inst_po">
    <input type="hidden" name="po1" id="inst_po1">
</div>
<div class="col-3">
    <button type="button" class="btn btn-warning btn-sm" id="inst_calculate">
      <span class="fa fa-calculator"></span> Calculate
  </button>
</div>
<div class="col-2"></div>
</div>

<div class="row mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
  <div class="col d-flex ">
    <button type="button" class="btn btn-primary btn-sm me-2 px-4 mr-2" id="inst_simpan">
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

<div class="card shadow mb-5" style="border: 1px solid #e2e8f0; border-left: 5px solid #0d9488; border-radius: 10px;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center" style="font-weight: bold; color: #0d9488; border-bottom: 1px solid #e2e8f0; cursor: pointer;" data-toggle="collapse" data-target="#inst_cicilan_card_body" aria-expanded="true" aria-controls="inst_cicilan_card_body">
        <span><i class="fa fa-calendar-check-o"></i> Installment Schedule</span>
        <i class="fa fa-chevron-up"></i>
    </div>
    <div class="collapse show" id="inst_cicilan_card_body">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table id="inst_cicilan_table" class="table table-sm table-bordered" style="font-size: 13px;text-align:center;">
                    <thead>
                        <tr style="background-color: #f1f5f9; color: #1e293b;">
                            <th>Installment No</th>
                            <th>Due Date</th>
                            <th>Subtotal (DPP)</th>
                            <th>Tax (PPN)</th>
                            <th>Tax (PPh)</th>
                            <th>RO</th>
                            <th>FTR (CBD/DP)</th>
                            <th>Potongan</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr style="background-color: #f1f5f9; color: #1e293b; font-weight: bold;">
                            <th colspan="2" class="text-right">Total</th>
                            <th class="text-right" id="inst_cicilan_total_dpp">0.00</th>
                            <th class="text-right" id="inst_cicilan_total_ppn">0.00</th>
                            <th class="text-right" id="inst_cicilan_total_pph">0.00</th>
                            <th class="text-right" id="inst_cicilan_total_ro">0.00</th>
                            <th class="text-right" id="inst_cicilan_total_ftr">0.00</th>
                            <th class="text-right" id="inst_cicilan_total_potongan">0.00</th>
                            <th class="text-right" id="inst_cicilan_total_total">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

</div>
</div>
</form>

<div class="modal fade" id="inst_mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- kasih modal-lg biar lega -->
    <div class="modal-content shadow-lg rounded-3">

      <!-- Header -->
      <div class="modal-header text-white" style="background-color: #191970;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title text-white" id="inst_Heading">Add Data</h4>
    </div>

    <!-- Body -->
    <div class="modal-body">
        <form id="inst-modal-form" method="post">

          <!-- Supplier -->
          <div class="form-group mb-3">
            <label for="inst_nama_supp"><b>Supplier</b></label>
            <select class="form-control selectpicker" name="nama_supp" id="inst_nama_supp" data-dropup-auto="false" data-live-search="true">
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

        <input type="hidden" style="font-size: 13px;" name="h_profit_center" id="inst_h_profit_center" class="form-control form-control-sm" value="">
        <input type="hidden" style="font-size: 13px;" name="tanggal_kbn" id="inst_tanggal_kbn" class="form-control form-control-sm" value="">

        <!-- BPB Date -->
        <div class="form-group">
            <label><b>BPB Date</b></label>
            <div class="d-flex align-items-center gap-2">
              <input type="text" class="form-control tanggal_fil mr-2" id="inst_start_date" name="start_date" 
              value="<?php
              if(!empty($_POST['start_date'])) {
                echo $_POST['start_date'];
                } else {
                    echo date("d-m-Y");
                } ?>" 
                placeholder="Tanggal Awal">

                <span class="mx-2">-</span>

                <input type="text" class="form-control tanggal_fil" id="inst_end_date" name="end_date" 
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
      <button type="submit" form="inst-modal-form" id="inst_send" name="send" class="btn btn-warning">
          <i class="fa fa-search" aria-hidden="true"></i> Search
      </button>
  </div>

</div>
</div>
</div>


<div class="modal fade" id="inst_mymodalbpb" data-target="#inst_mymodalbpb" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="inst_txt_bpb"></h4>
            </div>
            <div class="container">
                <div class="row">
                  <div id="inst_txt_tglbpb" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="inst_txt_no_po" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="inst_txt_supp2" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="inst_txt_top" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>         
                  <div id="inst_txt_curr" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="inst_txt_confirm" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="inst_txt_confirm2" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>
                  <div id="inst_txt_tgl_po" class="modal-body col-6" style="font-size: 13px;; padding: 0.5rem;"></div>                              
                  <div id="inst_details" class="modal-body col-12" style="font-size: 13px;; padding: 0.5rem;"></div>          
              </div>
          </div>
      </div>
      <!-- /.modal-content --> 
  </div>
  <!-- /.modal-dialog --> 
</div>         

<script>

    document.addEventListener("DOMContentLoaded", function() {
        let savedPc = localStorage.getItem("profit_center_inst");
        if (savedPc) {
            document.getElementById("inst_profit_center").value = savedPc;
            updateNoKontraBonInst();
        }
            let tanggal = document.getElementById('inst_tanggal').value;
            ubahtanggalInst(tanggal);
    });

    document.getElementById("inst_profit_center").addEventListener("change", function() {
        localStorage.setItem("profit_center_inst", this.value);
        document.querySelector("#inst_mytable tbody").innerHTML = "";
        document.querySelector("#inst_mytable1 tbody").innerHTML = "";
        document.querySelector("#inst_mytable2 tbody").innerHTML = "";
    });

    function updateNoKontraBonInst() {
        let pc = document.getElementById("inst_profit_center").value;  
        let input = document.getElementById("inst_nokontrabon");  
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

    $(document).ready(function() {
        $("#inst_mysupp").on("click", function() {
            let profit_center = document.getElementById('inst_profit_center').value;
            let tanggal = document.getElementById('inst_tanggal').value;

            if(profit_center == ""){
                Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
                $("#inst_profit_center").focus();
                return;
            }

            $('#inst_h_profit_center').val(profit_center);
            $('#inst_tanggal_kbn').val(tanggal);

            $("#inst_mymodal").modal("show");
        });
    });
</script>

<script>
// ===== Auto-fill Bank Info saat Bank Account dipilih =====
$(document).on('change', '#inst_sel_bank_account', function () {
    var opt = $(this).find('option:selected');
    var account     = opt.data('account')     || '';
    var currency    = opt.data('currency')    || '';
    var bankname    = opt.data('bankname')    || '';
    var beneficiary = opt.data('beneficiary') || '';

    $('#inst_disp_currency').val(currency);
    $('#inst_disp_bankname').val(bankname);
    $('#inst_disp_beneficiary').val(beneficiary);

    // update hidden fields (backward compat)
    $('#inst_txt_bank_supp').val(bankname);
    $('#inst_txt_akun_supp').val(account);
});

// ===== Auto-fill From Bank Name/Currency saat From Account dipilih =====
$(document).on('change', '#inst_from_account', function () {
    var opt = $(this).find('option:selected');
    $('#inst_from_bank').val(opt.data('bank') || '');
    $('#inst_from_bank_curr').val(opt.data('currency') || '');
});
</script>

<script>
    $(document).ready(function() {
        $("[data-toggle=tooltip]").tooltip();

    } );

    $(document).ready(function () {
        $('#inst_mytable').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $('#inst_mytable1').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
        });

        $('#inst_mytable2').DataTable({
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
        var tgl1 = document.getElementById('inst_tanggal3').value;
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
        // var tgl = document.getElementById('inst_tanggal').value;
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
    function ubahtanggalInst(value){  
        var tanggal = document.getElementById('inst_tanggal').value; 
        var txt_top = parseFloat(document.getElementById('inst_txt_top').value,10) || 0;
        var coba = new Date();
        var hasil = addDate(tanggal, txt_top);
        // alert(tanggal);
        $.ajax({
            type: 'POST', 
            url: 'getnomor_pv_installment.php',
            data: {'tanggal':tanggal},
            success: function(response) { 
                // console.log(response);
                $('#inst_nokontrabon').val(response); 
                updateNoKontraBonInst(); 
            }
        });
    // result.setDate(result.getDate() + txt_top);
    // tgl2    = DATEADD(day, txt_top, tanggal);
    $("#inst_tanggal").val(tanggal);
    $("#inst_txt_tgltempo").val(hasil);

};
</script>

<script type="text/javascript">
    function formatMoneyInst(amount, decimalCount = 2, decimal = ".", thousands = ",") {
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

function updateSetelahPotonganInst() {
    var pajak_h = parseFloat(document.getElementById('inst_pajak_h').value, 10) || 0;
    var pph_h = parseFloat(document.getElementById('inst_pph_h').value, 10) || 0;
    var potongan_ppn_h = parseFloat(document.getElementById('inst_potongan_ppn').value, 10) || 0;
    var potongan_pph_h = parseFloat(document.getElementById('inst_potongan_pph').value, 10) || 0;

    var pajak_setelah_potongan = pajak_h + potongan_ppn_h;
    var pph_setelah_potongan = pph_h + potongan_pph_h;

    $("#inst_pajak_setelah_potongan").val(formatMoneyInst(pajak_setelah_potongan));
    $("#inst_pajak_setelah_potongan_h").val(pajak_setelah_potongan);
    $("#inst_pph_setelah_potongan").val(formatMoneyInst(pph_setelah_potongan));
    $("#inst_pph_setelah_potongan_h").val(pph_setelah_potongan);
}

$("input[name=potongan_ppn]").keyup(function(){
    var potongan_ppn = parseFloat($(this).val(), 10) || 0;
    $("#inst_potongan_ppn_h").val(formatMoneyInst(potongan_ppn));
    updateSetelahPotonganInst();
});

$("input[name=potongan_pph]").keyup(function(){
    var potongan_pph = parseFloat($(this).val(), 10) || 0;
    $("#inst_potongan_pph_h").val(formatMoneyInst(potongan_pph));
    updateSetelahPotonganInst();
});
// $(".chkA_inst").change(function(){
    $("#inst-form-simpan input[id=inst_select]").change(function(){
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
        $("#inst-form-simpan .chkC_inst").prop("disabled", false);
    // $(this).closest('tr').find('td:eq(6) input').val(0);   
    $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph_inst]').prop('disabled', true);         
    $("#inst-form-simpan input[type=checkbox]:checked").each(function () { 
        var tgl4 = document.getElementById('inst_tanggal4').value; 
        var tanggal = document.getElementById('inst_tgl_perhitungan').value || '1970-01-01'; 
        var tglbpb = $(this).closest('tr').find('td:eq(3)').attr('value');  
        var tglbpb2 = $(this).closest('tr').find('td:eq(3)').attr('dates');
        var po = $(this).closest('tr').find('td:eq(2)').attr('value'); 
        var curr = $(this).closest('tr').find('td:eq(14)').attr('value') || $(this).closest('tr').find('td:eq(7)').attr('valuess') || document.getElementById('inst_matauang').value || 'IDR'; 
        var po1 = document.getElementById('inst_po').value;  
        var tax1 = parseFloat(document.getElementById('inst_pajak_h').value,10) || 0;
        var cbd1 = parseFloat(document.getElementById('inst_ttl_dp_h').value,10) || 0;
        var h_sub = parseFloat(document.getElementById('inst_subtotal_h').value,10) || 0;
        var h_pot = parseFloat($(this).closest('tr').find('td:eq(6) input').val(),10) || 0;
        var select_amount = $(this).closest('tr').find('td:eq(6) input');
        var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
        var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
        var price_ftr = parseFloat($(this).closest('tr').find('td:eq(5)').attr('total-ftr'),10) || 0;
        var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
        var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
        var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
        var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph_inst] option').filter(':selected').val(),10) ||0;
        var select_pph = $(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph_inst]');
        var mattype = $(this).closest('tr').find('td:eq(15)').attr('value'); 
        var matclass = $(this).closest('tr').find('td:eq(16)').attr('value'); 
        var n_code_category = $(this).closest('tr').find('td:eq(17)').attr('value'); 
        var cus_ctg = $(this).closest('tr').find('td:eq(18)').attr('value');
        var h_mattype = document.getElementById('inst_h_mattype').value;
        var h_matclass = document.getElementById('inst_h_matclass').value;
        var h_code_ctg = document.getElementById('inst_h_code_ctg').value;
        var h_cus_ctg = document.getElementById('inst_h_cus_ctg').value; 
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

    $("#inst_mytable input[type=checkbox]:checked").each(function () { 
        var po = $(this).closest('tr').find('td:eq(2)').attr('value'); 
        allowedPO.push(po);

    });

    $("#inst_subtotal").val(formatMoneyInst(sum_sub));
    $("#inst_subtotal_h").val(sum_sub.toFixed(2)); 
    $("#inst_subtotal_h1").val(data1.toFixed(2)); 
    $("#inst_potongan").val(formatMoneyInst(total));
    $("#inst_potongan_h").val(total.toFixed(4));            
    $("#inst_po").val(nopo1); 
    $("#inst_po1").val(nopo); 
    $("#sisapotongan").val(formatMoneyInst(sisa));
    $("#inst_ttl_sub").val(sisa);
    $("#inst_ttl_dp").val(formatMoneyInst(total_ftr));
    $("#inst_ttl_dp_h").val(total_ftr.toFixed(2));
    $("#inst_pajak").val(formatMoneyInst(ppn));
    $("#inst_pajak_h").val(ppn);
    $("#inst_pph").val(formatMoneyInst(pph11));
    $("#inst_pph_h").val(pph11.toFixed(2));
    updateSetelahPotonganInst();
    $("#inst_jumlahpotong").val(formatMoneyInst(pot));
    $("#inst_jml_potong").val(pot);
    $("#inst_matauang").val(curren);
    $("#inst_tanggal4").val(tanggals);
    $("#inst_tgl_perhitungan").val(dates);

    $("#inst_h_mattype").val(m_type);
    $("#inst_h_matclass").val(m_class);
    $("#inst_h_code_ctg").val(n_code);
    $("#inst_h_cus_ctg").val(c_ctg);
    $("#inst_select").val("1");   

    $("#inst-form-simpan .chkC_inst").prop("disabled", true); 
    if (allowedPO.length > 0) {
        $("#inst_mytable2 tbody tr").each(function () {
            var poC = $(this).find("td:eq(2)").attr("po-ftr");
            if (allowedPO.includes(poC)) {
                $(this).find(".chkC_inst").prop("disabled", false);
            }
        });
    }else{
        $("#inst_mytable2 tbody tr").each(function () {
            $("#inst-form-simpan .chkC_inst").prop("disabled", false).prop("checked", false);
            $(this).find('td:eq(6) input').prop('disabled', true);
            $("#inst_ttl_dp").val('');
            $("#inst_ttl_dp_h").val('');
        });
    }

});
</script>

<script type="text/javascript">
// Installment hanya boleh 1 PO per save (supaya "top" yang dipakai untuk
// jadwal due date tiap cicilan jelas/tidak ambigu). Begitu ada BPB yang
// tercentang, checkbox BPB dari PO lain langsung di-disable (readonly) -
// supaya tidak bisa diklik sama sekali. Sebelumnya pakai pola "reject
// setelah klik" (uncheck balik + trigger change lagi), tapi itu bikin total
// sempat ikut terjumlah duluan sebelum dibatalkan; dengan disable di awal,
// event change untuk PO yang tidak diizinkan tidak akan pernah terpicu.
$("#inst_mytable").on('change', 'input.chkA_inst', function () {
    var top = $(this).closest('tr').find('td:eq(13)').attr('value');

    var checkedPos = [];
    $("#inst_mytable input.chkA_inst:checked").each(function () {
        var rowPo = $(this).closest('tr').find('td:eq(2)').attr('value');
        if ($.inArray(rowPo, checkedPos) === -1) {
            checkedPos.push(rowPo);
        }
    });

    var lockedPo = checkedPos.length > 0 ? checkedPos[0] : '';

    $("#inst_mytable input.chkA_inst").each(function () {
        var rowPo = $(this).closest('tr').find('td:eq(2)').attr('value');
        var lockToOther = lockedPo !== '' && rowPo !== lockedPo;
        $(this).prop('disabled', lockToOther && !this.checked);
    });

    if (lockedPo !== '') {
        $("#inst_top_cicilan").val(parseFloat(top, 10) || 0);
    } else {
        $("#inst_top_cicilan").val('');
    }

    buildCicilanScheduleInst();
});
</script>

<script type="text/javascript">
    $("#inst-form-simpan select[name=combo_pph_inst]").on('change', function(){
        var sum_sub = 0;
        var total = 0;
        var sisa = 0;
        var nopo= '';
        var ppn = 0;
        var cbddp = 0;
        var sum_pph = 0;
        $("#inst-form-simpan input[type=checkbox]:checked").each(function () {  
            var po = $(this).closest('tr').find('td:eq(2)').attr('value');      
            var price = parseFloat($(this).closest('tr').find('td:eq(4)').attr('data-subtotal'),10) || 0;
            var price_ro = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-total-ro'),10) || 0;
            var a = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) || 0;
            var tax = parseFloat($(this).closest('tr').find('td:eq(5)').attr('data-tax'),10) ||0;
            var cbd = parseFloat($(this).closest('tr').find('td:eq(8)').attr('data'),10) ||0;
            var pph = parseFloat($(this).closest('tr').find('td:eq(6)').find('select[name=combo_pph_inst] option').filter(':selected').val(),10) ||0;            
            sum_pph += price * (pph / 100);
            sum_sub += price;
            total += price_ro;
            sisa = sum_sub - total - sum_pph; 
            nopo= po;
            ppn= tax;
            cbddp= cbd;  

        });  
        $("#inst_pph").val(formatMoneyInst(sum_pph));
        $("#inst_pph_h").val(sum_pph.toFixed(2));
        updateSetelahPotonganInst();
    // $("#inst_subtotal").val(formatMoneyInst(sum_sub));
    // $("#inst_subtotal_h").val(sum_sub);             
    // $("#inst_po").val(nopo); 
    // $("#inst_potongan").val(formatMoneyInst(total));
    // $("#inst_potongan_h").val(total);
    // $("#sisapotongan").val(formatMoneyInst(sisa));
    // $("#inst_ttl_sub").val(sisa);
    // $("#inst_total").val(formatMoneyInst(sisa));
    // $("#inst_total_h").val(sisa);
    // $("#inst_ttl_dp").val(formatMoneyInst(cbddp));
    // $("#inst_ttl_dp_h").val(cbddp);
    // $("#inst_pajak").val(formatMoneyInst(ppn));
    // $("#inst_pajak_h").val(ppn);
    // $("#inst_select").val("1");
});
</script>

<script type="text/javascript">
    $("#inst_mytable1 input[name=txt_amount]").keyup(function(){
        var sum_amount = 0;
        var sum_total = 0;
        var sum_balance = 0;        
        $("#inst-form-simpan input[type=checkbox]:checked").each(function () {        
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
        $("#inst_potongan").val(formatMoneyInst(sum_total));
        $("#inst_potongan_h").val(sum_total.toFixed(4));
    });


    $("#inst_mytable2 input[name=amount_ftr]").keyup(function(){
        var sum_amount = 0;
        var sum_total = 0;
        var sum_balance = 0;        
        $("#inst-form-simpan input[type=checkbox]:checked").each(function () {        
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
        $("#inst_ttl_dp").val(formatMoneyInst(sum_total));
        $("#inst_ttl_dp_h").val(sum_total.toFixed(4));
    });
</script>

<script type="text/javascript">
    $("input[name=labarugi]").keyup(function(){
        var laba = 0; 
        var jml1 = 0;
        var ttl_jml1 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('inst_labarugi').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('inst_ttl_sub').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('inst_selisihqty').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('inst_selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('inst_materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('inst_potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('inst_ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('inst_moq').value,10) || 0;               
            jml1 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;              
            laba = laba_h;
            ttl_jml1 = ttl_h + jml1; 
        });
        $("#inst_labarugi_h").val(formatMoneyInst(laba));
        $("#inst_jumlahpotong").val(formatMoneyInst(jml1));
        $("#inst_jml_potong").val(jml1);
        $("#sisapotongan").val(formatMoneyInst(ttl_jml1));
        $("#inst_ttl_sub2").val(ttl_jml1);

    });
</script>

<script type="text/javascript">
    $("input[name=selisihqty]").keyup(function(){
        var selisih = 0;
        var jml2 = 0; 
        var ttl_jml2 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('inst_labarugi').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('inst_ttl_sub').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('inst_selisihqty').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('inst_selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('inst_materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('inst_potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('inst_ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('inst_moq').value,10) || 0;               
            jml2 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                
            selisih = selisih_h;
            ttl_jml2 = ttl_h + jml2; 
        });
        $("#inst_selisihqty_h").val(formatMoneyInst(selisih));
        $("#inst_jumlahpotong").val(formatMoneyInst(jml2));
        $("#inst_jml_potong").val(jml2);
        $("#sisapotongan").val(formatMoneyInst(ttl_jml2));
        $("#inst_ttl_sub1").val(ttl_jml2);
    });
</script>

<script type="text/javascript">
    $("input[name=selisihharga]").keyup(function(){
        var selisihhrg = 0; 
        var jml3 = 0;
        var ttl_jml3 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('inst_labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('inst_selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('inst_ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('inst_selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('inst_materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('inst_potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('inst_ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('inst_moq').value,10) || 0;               
            jml3 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
            selisihhrg = selisihhrg_h;
            ttl_jml3 = ttl_h + jml3; 
        });
        $("#inst_selisihharga_h").val(formatMoneyInst(selisihhrg));
        $("#inst_jumlahpotong").val(formatMoneyInst(jml3));
        $("#inst_jml_potong").val(jml3);
        $("#sisapotongan").val(formatMoneyInst(ttl_jml3));
        $("#inst_ttl_sub3").val(ttl_jml3);
    });
</script>

<script type="text/javascript">
    $("input[name=materai]").keyup(function(){
        var mater = 0; 
        var jml4 = 0;
        var ttl_jml4 = 0;
        $("input[type=text]").each(function () {         
            var laba_h = parseFloat(document.getElementById('inst_labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('inst_selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('inst_ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('inst_selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('inst_materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('inst_potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('inst_ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('inst_moq').value,10) || 0;               
            jml4 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                
            mater = mater_h;
            ttl_jml4 = ttl_h + jml4; 
        });
        $("#inst_materai_h").val(formatMoneyInst(mater));
        $("#inst_jumlahpotong").val(formatMoneyInst(jml4));
        $("#inst_jml_potong").val(jml4);
        $("#sisapotongan").val(formatMoneyInst(ttl_jml4));
        $("#inst_ttl_sub4").val(ttl_jml4);
    });
</script>

<script type="text/javascript">
    $("input[name=potongbeli]").keyup(function(){
        var potongbeli = 0; 
        var jml5 = 0;
        var ttl_jml5 = 0;
        $("input[type=text]").each(function () {        
            var laba_h = parseFloat(document.getElementById('inst_labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('inst_selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('inst_ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('inst_selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('inst_materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('inst_potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('inst_ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('inst_moq').value,10) || 0;               
            jml5 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
            potongbeli = potongbeli_h;
            ttl_jml5 = ttl_h + jml5; 
        });
        $("#inst_potongbeli_h").val(formatMoneyInst(potongbeli));
        $("#inst_jumlahpotong").val(formatMoneyInst(jml5));
        $("#inst_jml_potong").val(jml5);
        $("#sisapotongan").val(formatMoneyInst(ttl_jml5));
        $("#inst_ttl_sub5").val(ttl_jml5);
    });
</script>

<script type="text/javascript">
    $("input[name=ekspedisi]").keyup(function(){
        var ekspedisi = 0; 
        var jml6 = 0;
        var ttl_jml6 = 0;
        $("input[type=text]").each(function () {        
            var laba_h = parseFloat(document.getElementById('inst_labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('inst_selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('inst_ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('inst_selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('inst_materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('inst_potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('inst_ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('inst_moq').value,10) || 0;               
            jml6 = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;                 
            ekspedisi = ekspedisi_h;
            ttl_jml6 = ttl_h + jml6; 
        });
        $("#inst_ekspedisi_h").val(formatMoneyInst(ekspedisi));
        $("#inst_jumlahpotong").val(formatMoneyInst(jml6));
        $("#inst_jml_potong").val(jml6);
        $("#sisapotongan").val(formatMoneyInst(ttl_jml6));
        $("#inst_ttl_sub6").val(ttl_jml6);
    });
</script>

<script type="text/javascript">
    $("input[name=moq]").keyup(function(){
        var moq = 0; 
        var jml = 0;
        var ttl_jml7 = 0;

        $("input[type=text]").each(function () {        
            var laba_h = parseFloat(document.getElementById('inst_labarugi').value,10) || 0;
            var selisih_h = parseFloat(document.getElementById('inst_selisihqty').value,10) || 0;
            var ttl_h = parseFloat(document.getElementById('inst_ttl_sub').value,10) || 0;
            var selisihhrg_h = parseFloat(document.getElementById('inst_selisihharga').value,10) || 0;
            var mater_h = parseFloat(document.getElementById('inst_materai').value,10) || 0;
            var potongbeli_h = parseFloat(document.getElementById('inst_potongbeli').value,10) || 0;
            var ekspedisi_h = parseFloat(document.getElementById('inst_ekspedisi').value,10) || 0;
            var moq_h = parseFloat(document.getElementById('inst_moq').value,10) || 0;

            jml = laba_h + selisih_h + selisihhrg_h + mater_h - potongbeli_h + ekspedisi_h + moq_h;
            moq = moq_h;
            ttl_jml7 = ttl_h + jml;

        });
        $("#inst_moq_h").val(formatMoneyInst(moq));
        $("#inst_jumlahpotong").val(formatMoneyInst(jml));
        $("#inst_jml_potong").val(jml);
        $("#sisapotongan").val(formatMoneyInst(ttl_jml7));
        $("#inst_ttl_sub7").val(ttl_jml7);
    });
</script>

<script type="text/javascript">
    $("#inst-form-simpan").on("click", "#inst_calculate", function(){
        var jumlah = 0; 
        var total = 0;
        var ttl_dp = 0;
        var pajak = 0;
        var pph = 0;
        $("input[type=button]").each(function () { 
            var subtotal_h = parseFloat(document.getElementById('inst_subtotal_h').value,10) || 0;
            var potongan_h = parseFloat(document.getElementById('inst_potongan_h').value,10) || 0;
            var jml_potong = parseFloat(document.getElementById('inst_jml_potong').value,10) || 0;
            var pajak_h = parseFloat(document.getElementById('inst_pajak_setelah_potongan_h').value,10) || 0;
            var pph_h = parseFloat(document.getElementById('inst_pph_setelah_potongan_h').value,10) || 0;
            var ttl_dp_h = parseFloat(document.getElementById('inst_ttl_dp_h').value,10) || 0;

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

        $("#inst_total").val(formatMoneyInst(total));
        $("#inst_total_h").val(total.toFixed(4));
        $("#inst_ttl_dp").val(formatMoneyInst(ttl_dp));
        $("#inst_ttl_dp_h").val(ttl_dp.toFixed(2));
        buildCicilanScheduleInst();
    });
</script>

<script type="text/javascript">
// Jadwal cicilan: DPP/PPN/PPh/RO/FTR/Potongan/Total dibagi rata ke N cicilan,
// sisa pembulatan masuk ke cicilan terakhir. Baris BPB/RO/FTR & jurnal
// akuntansi TIDAK dipecah - hanya breakdown nilai per cicilan ini yang disimpan.
function addDaysInst(dateStr, days) {
    var d = new Date(dateStr);
    d.setDate(d.getDate() + (parseInt(days, 10) || 0));
    return formatDate(d);
}

function splitAmountInst(amount, n) {
    var parts = [];
    var base = Math.round((amount / n) * 100) / 100;
    var runningTotal = 0;
    for (var i = 0; i < n - 1; i++) {
        parts.push(base);
        runningTotal += base;
    }
    parts.push(Math.round((amount - runningTotal) * 100) / 100);
    return parts;
}

function sumArrayInst(arr) {
    var s = 0;
    for (var i = 0; i < arr.length; i++) {
        s += arr[i];
    }
    return Math.round(s * 100) / 100;
}

function buildCicilanScheduleInst() {
    var n = parseInt(document.getElementById('inst_jml_cicilan').value, 10) || 0;
    var tbody = $("#inst_cicilan_table tbody");
    tbody.empty();

    if (n < 1) {
        $("#inst_cicilan_total_dpp, #inst_cicilan_total_ppn, #inst_cicilan_total_pph, #inst_cicilan_total_ro, #inst_cicilan_total_ftr, #inst_cicilan_total_potongan, #inst_cicilan_total_total").text(formatMoneyInst(0));
        return;
    }

    var dpp = parseFloat(document.getElementById('inst_subtotal_h').value, 10) || 0;
    var ppn = parseFloat(document.getElementById('inst_pajak_setelah_potongan_h').value, 10) || 0;
    var pph = parseFloat(document.getElementById('inst_pph_setelah_potongan_h').value, 10) || 0;
    var ro = parseFloat(document.getElementById('inst_potongan_h').value, 10) || 0;
    var ftr = parseFloat(document.getElementById('inst_ttl_dp_h').value, 10) || 0;
    var potongan = parseFloat(document.getElementById('inst_jml_potong').value, 10) || 0;
    var total = parseFloat(document.getElementById('inst_total_h').value, 10) || 0;
    var baseDueDate = document.getElementById('inst_txt_tgltempo').value;
    var top = parseFloat(document.getElementById('inst_top_cicilan').value, 10) || 0;

    var dppParts = splitAmountInst(dpp, n);
    var ppnParts = splitAmountInst(ppn, n);
    var pphParts = splitAmountInst(pph, n);
    var roParts = splitAmountInst(ro, n);
    var ftrParts = splitAmountInst(ftr, n);
    var potonganParts = splitAmountInst(potongan, n);
    var totalParts = splitAmountInst(total, n);

    for (var i = 1; i <= n; i++) {
        var dueDate = baseDueDate ? addDaysInst(baseDueDate, top * (i - 1)) : '';
        tbody.append(
            '<tr>' +
            '<td>' + i + '</td>' +
            '<td class="inst_cicilan_duedate" data-duedate="' + dueDate + '">' + dueDate + '</td>' +
            '<td class="text-right inst_cicilan_dpp" data-val="' + dppParts[i - 1] + '">' + formatMoneyInst(dppParts[i - 1]) + '</td>' +
            '<td class="text-right inst_cicilan_ppn" data-val="' + ppnParts[i - 1] + '">' + formatMoneyInst(ppnParts[i - 1]) + '</td>' +
            '<td class="text-right inst_cicilan_pph" data-val="' + pphParts[i - 1] + '">' + formatMoneyInst(pphParts[i - 1]) + '</td>' +
            '<td class="text-right inst_cicilan_ro" data-val="' + roParts[i - 1] + '">' + formatMoneyInst(roParts[i - 1]) + '</td>' +
            '<td class="text-right inst_cicilan_ftr" data-val="' + ftrParts[i - 1] + '">' + formatMoneyInst(ftrParts[i - 1]) + '</td>' +
            '<td class="text-right inst_cicilan_potongan" data-val="' + potonganParts[i - 1] + '">' + formatMoneyInst(potonganParts[i - 1]) + '</td>' +
            '<td class="text-right inst_cicilan_total" data-val="' + totalParts[i - 1] + '">' + formatMoneyInst(totalParts[i - 1]) + '</td>' +
            '</tr>'
        );
    }

    document.getElementById('inst_cicilan_total_dpp').textContent = formatMoneyInst(sumArrayInst(dppParts));
    document.getElementById('inst_cicilan_total_ppn').textContent = formatMoneyInst(sumArrayInst(ppnParts));
    document.getElementById('inst_cicilan_total_pph').textContent = formatMoneyInst(sumArrayInst(pphParts));
    document.getElementById('inst_cicilan_total_ro').textContent = formatMoneyInst(sumArrayInst(roParts));
    document.getElementById('inst_cicilan_total_ftr').textContent = formatMoneyInst(sumArrayInst(ftrParts));
    document.getElementById('inst_cicilan_total_potongan').textContent = formatMoneyInst(sumArrayInst(potonganParts));
    document.getElementById('inst_cicilan_total_total').textContent = formatMoneyInst(sumArrayInst(totalParts));
}

$("#inst_jml_cicilan").on('keyup change', function () {
    buildCicilanScheduleInst();
});
</script>

<script type="text/javascript">
    $("#inst-form-simpan").on("click", "#inst_simpan", function(){
        var no_kbon_h = document.getElementById('inst_nokontrabon').value;
        var unik_code = document.getElementById('inst_unik_code').value;
        var tgl_kbon_h = document.getElementById('inst_tanggal').value;
        var tgl_kbon_p = document.getElementById('inst_tgl_perhitungan').value;
        var tgl_kbon_s = document.getElementById('inst_tanggal4').value; 
        var nama_supp_h = document.getElementById('inst_txt_supp').value;
        var no_faktur_h = document.getElementById('inst_no_faktur').value;
        var no_po_h = document.getElementById('inst_po').value;
        var supp_inv_h = document.getElementById('inst_txt_inv').value;
        var tgl_inv_h = document.getElementById('inst_txt_tglsi').value;
        var tgl_tempo_h = document.getElementById('inst_txt_tgltempo').value;        
        var curr_h = document.getElementById('inst_matauang').value;
        var sub_h = document.getElementById('inst_subtotal_h').value;
        var tax_h = document.getElementById('inst_pajak_setelah_potongan_h').value;
        var dp_h = document.getElementById('inst_ttl_dp_h').value;
        var pph_h = document.getElementById('inst_pph_setelah_potongan_h').value;
        var total_h = document.getElementById('inst_total_h').value;
        var create_user_h = '<?php echo $user; ?>';
        var jml_return = document.getElementById('inst_potongan_h').value;
        var lr_kurs = document.getElementById('inst_labarugi').value;
        var s_qty = document.getElementById('inst_selisihqty').value;
        var s_harga = document.getElementById('inst_selisihharga').value;
        var materai = document.getElementById('inst_materai').value;
        var pot_beli = document.getElementById('inst_potongbeli').value;
        var ekspedisi = document.getElementById('inst_ekspedisi').value;
        var moq = document.getElementById('inst_moq').value;
        var jml_potong = document.getElementById('inst_jml_potong').value;
        var potongan_ppn = document.getElementById('inst_potongan_ppn').value;
        var potongan_pph = document.getElementById('inst_potongan_pph').value;
        var mattype = document.getElementById('inst_h_mattype').value;
        var matclass = document.getElementById('inst_h_matclass').value;
        var n_code_category = document.getElementById('inst_h_code_ctg').value;
        var cus_ctg = document.getElementById('inst_h_cus_ctg').value;
        var profit_center = document.getElementById('inst_profit_center').value;
        var ir_number = document.getElementById('inst_ir_number').value;
        var bank_account = document.getElementById('inst_sel_bank_account').value;
        var from_account = document.getElementById('inst_from_account').value;
        var from_bank = document.getElementById('inst_from_bank').value;
        var from_bank_curr = document.getElementById('inst_from_bank_curr').value;
        var jml_cicilan = parseInt(document.getElementById('inst_jml_cicilan').value, 10) || 0;
        var top_cicilan = document.getElementById('inst_top_cicilan').value;
        //&& tgl_kbon_h >= tgl_kbon_p
        if(total_h != '' && total_h >= 0 && ir_number != '' && bank_account != '' && profit_center != '' && jml_cicilan >= 1 && from_account != ''){
            $.ajax({
                type:'POST',
                url:'insertkbon_h_pv_installment.php',
                data: {'no_kbon_h':no_kbon_h, 'tgl_kbon_h':tgl_kbon_h,'nama_supp_h':nama_supp_h, 'no_faktur_h':no_faktur_h, 'supp_inv_h':supp_inv_h, 'tgl_inv_h':tgl_inv_h, 'tgl_tempo_h':tgl_tempo_h, 'curr_h':curr_h, 'create_user_h':create_user_h, 'sub_h':sub_h, 'tax_h':tax_h, 'dp_h':dp_h, 'pph_h':pph_h, 'total_h':total_h, 'jml_return':jml_return, 'lr_kurs':lr_kurs, 's_qty':s_qty, 's_harga':s_harga, 'materai':materai, 'pot_beli':pot_beli, 'ekspedisi':ekspedisi, 'moq':moq, 'jml_potong':jml_potong, 'potongan_ppn':potongan_ppn, 'potongan_pph':potongan_pph, 'no_po_h':no_po_h, 'tgl_kbon_s':tgl_kbon_s, 'unik_code':unik_code, 'mattype':mattype, 'matclass':matclass, 'n_code_category':n_code_category, 'cus_ctg':cus_ctg, 'profit_center':profit_center, 'ir_number':ir_number, 'bank_account':bank_account, 'from_account':from_account, 'from_bank':from_bank, 'from_bank_curr':from_bank_curr},
                cache: 'false',
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){
                    localStorage.removeItem("profit_center_inst");

                    var no_kbon_induk = response.split(':').pop().trim();
                    $("#inst_cicilan_table tbody tr").each(function (idx) {
                        var cicilanKe = idx + 1;
                        var noKbonDet = no_kbon_induk + '-' + String(cicilanKe).padStart(3, '0');
                        var row = $(this);
                        $.ajax({
                            type: 'POST',
                            url: 'insert_installment_detail.php',
                            data: {
                                no_kbon: no_kbon_induk,
                                no_kbon_det: noKbonDet,
                                cicilan_ke: cicilanKe,
                                total_cicilan: jml_cicilan,
                                top: top_cicilan,
                                tgl_tempo: row.find('.inst_cicilan_duedate').attr('data-duedate'),
                                dpp: row.find('.inst_cicilan_dpp').attr('data-val'),
                                ppn: row.find('.inst_cicilan_ppn').attr('data-val'),
                                pph: row.find('.inst_cicilan_pph').attr('data-val'),
                                ro: row.find('.inst_cicilan_ro').attr('data-val'),
                                ftr: row.find('.inst_cicilan_ftr').attr('data-val'),
                                potongan: row.find('.inst_cicilan_potongan').attr('data-val'),
                                total: row.find('.inst_cicilan_total').attr('data-val'),
                                create_user: '<?php echo $user; ?>'
                            },
                            cache: 'false',
                            error: function (xhr) {
                                console.log(xhr);
                                Swal.fire('Error', xhr.responseText, 'error');
                            }
                        });
                    });

                    $("#inst-form-simpan input[type=checkbox]:checked").each(function () {
                        //header
                        var no_kbon = document.getElementById('inst_nokontrabon').value; 
                        var unik_code = document.getElementById('inst_unik_code').value;       
                        var tgl_kbon = document.getElementById('inst_tanggal').value;
                        var tgl_kbon_p = document.getElementById('inst_tgl_perhitungan').value;
                        var jurnal = document.getElementById('inst_jurnal').value;
                        var nama_supp = document.getElementById('inst_txt_supp').value;
                        var profit_center = document.getElementById('inst_profit_center').value;
                        var no_faktur = document.getElementById('inst_no_faktur').value;
                        var supp_inv = document.getElementById('inst_txt_inv').value;
                        var tgl_inv = document.getElementById('inst_txt_tglsi').value;
                        var tgl_tempo = document.getElementById('inst_txt_tgltempo').value;        
                        var curr = document.getElementById('inst_matauang').value;
                        var ceklist = document.getElementById('inst_select').value;          
                        var total = document.getElementById('inst_total_h').value; 
                        var dp = document.getElementById('inst_ttl_dp_h').value;
                        var start_date = document.getElementById('inst_start_date').value;
                        var end_date = document.getElementById('inst_end_date').value;  
                        var create_user = '<?php echo $user; ?>';
                        //table bpb                               
                        var no_bpb = $(this).closest('#inst_mytable tr').find('td:eq(1)').attr('value');
                        var no_po = $(this).closest('#inst_mytable tr').find('td:eq(2)').attr('value');
                        var tgl_bpb = $(this).closest('#inst_mytable tr').find('td:eq(3)').attr('value');
                        var price = parseFloat($(this).closest('#inst_mytable tr').find('td:eq(4)').attr('data-subtotal'),10) ||0;
                        var tax = parseFloat($(this).closest('#inst_mytable tr').find('td:eq(5)').attr('data-tax'),10) ||0;
                        var cash = parseFloat($(this).closest('#inst_mytable tr').find('td:eq(8)').attr('data'),10) ||0;
                        var tgl_po = $(this).closest('#inst_mytable tr').find('td:eq(12)').attr('value');
                        var pph = parseFloat($(this).closest('#inst_mytable tr').find('td:eq(6)').find('select[name=combo_pph_inst] option').filter(':selected').val(),10) ||0;
                        var idtax = $(this).closest('#inst_mytable tr').find('td:eq(6)').find('select[name=combo_pph_inst] option').filter(':selected').attr('data-idtax');
                        //table ro
                        var no_ro = $(this).closest('#inst_mytable1 tr').find('td:eq(1)').attr('data-ro');
                        var no_bppb = $(this).closest('#inst_mytable1 tr').find('td:eq(2)').attr('valuess');
                        var ttl_ro = parseFloat($(this).closest('#inst_mytable1 tr').find('td:eq(6) input').val(),10) || 0;
                        var mattype = $(this).closest('#inst_mytable tr').find('td:eq(15)').attr('value') || $(this).closest('#inst_mytable1 tr').find('td:eq(8)').attr('valuess');
                        var matclass = $(this).closest('#inst_mytable tr').find('td:eq(16)').attr('value') || $(this).closest('#inst_mytable1 tr').find('td:eq(9)').attr('valuess');
                        var n_code_category = $(this).closest('#inst_mytable tr').find('td:eq(17)').attr('value') || $(this).closest('#inst_mytable1 tr').find('td:eq(10)').attr('valuess');
                        var cus_ctg = $(this).closest('#inst_mytable tr').find('td:eq(18)').attr('value') || $(this).closest('#inst_mytable1 tr').find('td:eq(11)').attr('valuess'); 

                        //table ftr 
                        var no_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(1)').attr('no-ftr');
                        var no_po_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(2)').attr('po-ftr');
                        var tgl_po_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(3)').attr('tglpo-ftr');
                        var no_pi_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(4)').attr('pi-ftr');
                        var ttl_ftr = parseFloat($(this).closest('#inst_mytable2 tr').find('td:eq(6) input').val(),10) || 0;
                        var curr_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(7)').attr('curr-ftr');
                        var kbon_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(8)').attr('kbon-ftr');
                        var tglkbon_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(9)').attr('tglkbon-ftr');
                        var lp_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(10)').attr('lp-ftr');
                        var tgllp_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(11)').attr('tgllp-ftr');
                        var pv_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(12)').attr('pv-ftr');
                        var bankout_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(13)').attr('bankout-ftr');
                        var bankoutdate_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(14)').attr('bankoutdate-ftr');
                        var coa_ftr = $(this).closest('#inst_mytable2 tr').find('td:eq(15)').attr('coa-ftr');

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
            $.ajax({
                type:'POST',
                url:'insertkbon.php',
                data: {'no_kbon':no_kbon, 'tgl_kbon':tgl_kbon, 'jurnal':jurnal, 'no_bpb':no_bpb, 'no_po':no_po,  'no_ro':no_ro,
                'nama_supp':nama_supp, 'tgl_bpb':tgl_bpb, 'no_faktur':no_faktur, 'supp_inv':supp_inv, 'tgl_inv':tgl_inv, 'tgl_tempo':tgl_tempo,
                'curr':curr, 'ceklist':ceklist, 'cash':cash, 'create_user':create_user, 'sum_sub':sum_sub, 'sum_tax':sum_tax, 'sum_dp':sum_dp, 'sum_pph':sum_pph, 'sum_total':sum_total, 'start_date':start_date, 'end_date':end_date, 'pph':pph, 'idtax':idtax, 'tgl_po':tgl_po, 'ttl_ro':ttl_ro, 'no_bppb':no_bppb, 'unik_code':unik_code, 'mattype':mattype, 'matclass':matclass, 'n_code_category':n_code_category, 'cus_ctg':cus_ctg, 'no_ftr':no_ftr, 'no_po_ftr':no_po_ftr, 'tgl_po_ftr':tgl_po_ftr, 'no_pi_ftr':no_pi_ftr, 'ttl_ftr':ttl_ftr, 'curr_ftr':curr_ftr, 'kbon_ftr':kbon_ftr, 'tglkbon_ftr':tglkbon_ftr, 'lp_ftr':lp_ftr, 'tgllp_ftr':tgllp_ftr, 'pv_ftr':pv_ftr, 'bankout_ftr':bankout_ftr, 'bankoutdate_ftr':bankoutdate_ftr, 'coa_ftr':coa_ftr, 'profit_center':profit_center},
                cache: 'false',
                close: function(e){
                    e.preventDefault();
                },
                success: function(response){
                    console.log(response);
                  // alert(response);
              },
              error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr);
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });
            // console.log(data);
        }
    });
console.log(response);
Swal.fire({icon: 'success', title: 'Data Saved Successfully', text: response, timer: 5000, showConfirmButton: true, confirmButtonText: 'OK'}).then(function(){
window.location = 'payment-voucher-ap.php';
});
},
error: function (xhr, ajaxOptions, thrownError) {
    console.log(xhr);
    Swal.fire('Error', xhr.responseText, 'error');
}
});
}

        // else if (document.getElementById('inst_tanggal').value < document.getElementById('inst_tgl_perhitungan').value){
        // alert("Contrabon Date Can't be less than BPB Date ");
        // }               

        if(document.querySelectorAll("input[name='select_inst[]']:checked").length == 0){
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please check the BPB number'});
        }else if (document.getElementById('inst_total_h').value == ''){
            document.getElementById('inst_calculate').focus();
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please do the calculation'});
        }else if (document.getElementById('inst_ir_number').value == ''){
            document.getElementById('inst_ir_number').focus();
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Invoice Received Number'});
        }else if (document.getElementById('inst_sel_bank_account').value == ''){
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select bank account supplier'});
        }else if (document.getElementById('inst_from_account').value == ''){
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please select From Account'});
        }else if (document.getElementById('inst_profit_center').value == ''){
            document.getElementById('inst_profit_center').focus();
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please Input Profit Center.'});
        }else if (parseInt(document.getElementById('inst_jml_cicilan').value, 10) < 1 || document.getElementById('inst_jml_cicilan').value == ''){
            document.getElementById('inst_jml_cicilan').focus();
            Swal.fire({icon: 'warning', title: 'Oops...', text: 'Please input Jumlah Cicilan (minimal 1).'});
        }else if (document.getElementById('inst_total_h').value < 0){
            Swal.fire({icon: 'warning', title: 'Oops...', text: "Contrabon can't be minus"});
        }else{

        } 
    });
</script>

<script type="text/javascript">     
    $('#inst_mytable tbody tr').on('click', 'td:eq(1)', function(){                
        $('#inst_mymodalbpb').modal('show');
        var no_bpb = $(this).closest('tr').find('td:eq(1)').attr('value');
        var tgl_bpb = $(this).closest('tr').find('td:eq(3)').text();
        var no_po = $(this).closest('tr').find('td:eq(2)').attr('value');
        var supp = $(this).closest('tr').find('td:eq(11)').attr('value');
        var top = $(this).closest('tr').find('td:eq(13)').attr('value');
        var curr = document.getElementById('inst_matauang').value;
        var confirm = $(this).closest('tr').find('td:eq(9)').attr('value');
        var confirm2 = $(this).closest('tr').find('td:eq(10)').attr('value');
        var tgl_po = $(this).closest('tr').find('td:eq(12)').text();  

        var targetUrl = no_po.includes("NAK") ? 'ajaxbpb_knitting.php' : 'ajaxbpb.php';  

        $.ajax({
            type : 'post',
            url : targetUrl,
            data : {'no_bpb': no_bpb},
            success : function(data){
    $('#inst_details').html(data); //menampilkan data ke dalam modal
}
});         
        //make your ajax call populate items or what even you need
        $('#inst_txt_bpb').html(no_bpb);
        $('#inst_txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
        $('#inst_txt_no_po').html('No PO : ' + no_po + '');
        $('#inst_txt_supp2').html('Supplier : ' + supp + '');
        $('#inst_txt_top').html('TOP : ' + top + ' Days');
        $('#inst_txt_curr').html('Currency : ' + curr + '');        
        $('#inst_txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
        $('#inst_txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
        $('#inst_txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
    });

</script>
