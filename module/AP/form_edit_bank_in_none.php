<?php include '../header.php' ?>

<style type="text/css">
    label {
        font-size: 13px;;
    }

    input {
        font-size: 13px;;
    }

    .card-header {
      display: flex !important;
      justify-content: flex-start !important;
      align-items: center !important;
  }

  .card-header h5 {
      margin-left: 0 !important;
  }

  .custom-col {
      flex: 0 0 12.5%;
      max-width: 12.5%;
  }

  .select2-container {
    width: 100% !important;
  }

  .select2-container .select2-selection--single {
    height: calc(1.5em + .5rem + 2px);
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: calc(1.5em + .5rem);
    font-size: 13px;
    padding-left: 8px;
  }

  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + .5rem + 2px);
  }

  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }

  #mytablenone .form-control {
    width: 100% !important;
  }

  #mytablenone .bootstrap-select {
    width: 100% !important;
  }

  .total-box{
    border:0;
    border-radius:10px;
    background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    overflow:hidden;
    height:100%;
    padding:0;
  }

  .total-box .total-box-header{
    padding:12px 16px;
    color:#fff;
    font-weight:700;
    font-size:14px;
  }

  .total-box.tone-nag .total-box-header{
    background:linear-gradient(90deg, #5b7ba8, #7fa0c9);
  }

  .total-box.tone-nak .total-box-header{
    background:linear-gradient(90deg, #4f8a6b, #74ad8f);
  }

  .total-box.tone-all .total-box-header{
    background:linear-gradient(90deg, #4a5578, #6b7699);
  }

  .total-box .total-box-body{
    padding:16px;
    display:flex;
    flex-direction:column;
    gap:14px;
  }

  .total-stat{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    padding-bottom:12px;
    border-bottom:1px dashed #e5e5e5;
  }

  .total-stat:last-child{
    border-bottom:0;
    padding-bottom:0;
  }

  .total-stat-label{
    font-size:12px;
    font-weight:600;
    color:#8a8a8a;
    text-transform:uppercase;
    letter-spacing:.03em;
  }

  .total-stat input.total-stat-value{
    border:0;
    background:transparent;
    padding:0;
    font-size:19px;
    font-weight:700;
    text-align:right;
    width:auto;
    max-width:100%;
    height:auto;
    color:#212529;
  }

</style>

<?php 
$doc_num = base64_decode($_GET['doc_num']); 

$sql = mysqli_query($conn2,"select * from tbl_bankin_arcollection where doc_num = '$doc_num'");
$row = mysqli_fetch_array($sql);                         ;

$sql_acc_now = mysqli_query($conn1,"select kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.bank_account = '".$row['akun']."'");
$row_acc_now = mysqli_fetch_assoc($sql_acc_now);
$kode_bank_now = $row_acc_now['b_code'] ?? '';
$pc_bank_now = $row_acc_now['kode_pc'] ?? '';
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <div class="d-flex align-items-center justify-content-start">
        <img src="../../images/note.png" alt="Bank Logo" 
        style="width:25px; height:auto; margin-right:10px;">
        <h5 class="mb-0 text-white">FORM EDIT BANK IN</h5>
    </div>
</div>

<form id="form-data" method="post">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-2">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Doc Number</b></label>
                    <input type="text" readonly class="form-control" id="no_bankin" name="no_bankin" value="<?= $doc_num; ?>">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Date</b></label>
                    <input type="text" name="tgl_bankin" id="tgl_bankin" class="form-control tanggal"
                    value="<?php if(!empty($doc_num)) {
                        echo date("d-m-Y",strtotime($row['date']));
                    }
                    else{
                        echo date("d-m-Y");
                    } ?>" autocomplete='off'>
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Source</b></label>
                    <select class="form-control select2" name="nama_supp" id="nama_supp" data-live-search="true">
                        <?php
                        $customer = $row['customer'];
                        $isSelected = ' selected="selected"';
                        if(!empty($doc_num)) {
                            echo '<option value="'.$customer.'"'.$isSelected.'">'. $customer .'</option>';
                        }
                        else{
                            echo '<option value="Unrealize"  selected="true">Unrealize</option>';
                        }

                        if ($customer != 'Unrealize') {
                            echo '<option value="Unrealize" >Unrealize</option>';
                        }

                        $sql_supp = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'C' and Supplier != '$customer' order by Supplier ASC");
                        while ($row_supp = mysqli_fetch_array($sql_supp)) {
                            $data = $row_supp['Supplier'];
                            if($row_supp['Supplier'] == $_POST['nama_supp']){
                                $isSelected = ' selected="selected"';
                            }else{
                                $isSelected = '';
                            }
                            echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';
                        }?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Reference</b></label>
                    <input type="text" readonly class="form-control" id="ref_data" name="ref_data" value="<?php
                    if(!empty($doc_num)) {
                        echo $row['ref_data'];
                    }
                    else{
                        echo '';
                    }
                ?>">
                </div>

                <?php $profit_center = $row['profit_center']; ?>
                <input type="hidden" name="profit_center" id="profit_center" value="<?= $profit_center; ?>">

                <div class="col-md-3 mb-2">
                    <label><b>Account</b></label>
                    <select class="form-control select2" id="accountid" name="accountid" data-live-search="true">
                        <?php
                        $akun_now = $row['akun'];
                        echo '<option value="'.$akun_now.'" selected="selected">'.$akun_now.'</option>';
                        $sql_acc = mysqli_query($conn1,"select bank_name as bank,curr,bank_account as account,nama_pc, kode_pc, b_code from b_masterbank a INNER JOIN master_pc b on b.kode_pc = a.profit_center_bank where a.status = 'Active' and a.bank_account != '$akun_now'");
                        while ($row_acc = mysqli_fetch_assoc($sql_acc)) {
                            echo '<option value="'.$row_acc['account'].'" data-bank="'.$row_acc['bank'].'" data-curr="'.$row_acc['curr'].'" data-kodepc="'.$row_acc['kode_pc'].'" data-kodebank="'.$row_acc['b_code'].'">'.$row_acc['account'].'</option>';
                        }
                        ?>
                    </select>
                    <input type="hidden" id="kode_bank_acc" name="kode_bank_acc" value="<?= $kode_bank_now; ?>">
                    <input type="hidden" id="pc_bank_acc" name="pc_bank_acc" value="<?= $pc_bank_now; ?>">
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Bank</b></label>
                    <input type="text" readonly class="form-control" id="nama_bank" name="nama_bank" value="<?php
                    if(!empty($doc_num)) {
                        echo $row['bank'];
                    }
                    else{
                        echo '';
                    }
                ?>">
                </div>

                <div class="col-md-3 mb-2">
                    <div class="d-flex">
                        <label style="flex:0 0 38%;"><b>Currency</b></label>
                        <label style="flex:1;"><b>Rate</b></label>
                    </div>
                    <div class="input-group">
                        <input type="text" readonly class="form-control" id="valuta" name="valuta" style="max-width:38%;" value="<?php
                        if(!empty($doc_num)) {
                            echo $row['curr'];
                        }
                        else{
                            echo '';
                        }
                    ?>">
                        <input type="text" class="form-control angka" id="rate" name="rate" style="text-align: right;" placeholder="0.00"
                        value="<?php echo !empty($doc_num) ? number_format($row['rate'], 2) : ''; ?>" <?php echo ($row['curr'] === 'IDR') ? 'readonly' : ''; ?>>
                    </div>
                </div>

            </div>

            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <label><b>Cash Flow Category</b></label>
                    <select class="form-control select2" name="cash_flow" id="cash_flow" data-live-search="true">
                        <option value="">Select Cash Flow Category</option>
                        <?php
                        $id_cash_flow = $row['id_cash_flow'] ?? '';
                        $sqlCf = mysqli_query($conn2, "select id, show_subcategory from master_cash_flow where type_cashflow = 'Cash In' and status = 'Y' order by nama_category asc, urutan asc");
                        while ($rowCf = mysqli_fetch_assoc($sqlCf)) {
                            $selectedCf = ($rowCf['id'] == $id_cash_flow) ? ' selected="selected"' : '';
                            echo '<option value="'.$rowCf['id'].'"'.$selectedCf.'>'.$rowCf['show_subcategory'].'</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label><b>Amount</b></label>
                    <input type="text" class="form-control angka" id="amount" name="amount" style="text-align: right;" placeholder="0.00"
                    value="<?php echo !empty($doc_num) ? number_format($row['amount'], 2) : ''; ?>">
                </div>

                <div class="col-md-3 mb-2">
                    <label><b>Equivalent IDR</b></label>
                    <input type="text" class="form-control" id="eqv_idr" name="eqv_idr" style="text-align: right;" value="<?php echo !empty($doc_num) ? number_format($row['eqv_idr'], 2) : ''; ?>" placeholder="0.00" readonly>
                </div>

            </div>

            <div class="form-row">

                <div class="col-md-8 mb-2">
                    <label><b>Description</b></label>
                    <div class="d-flex">
        <textarea 
        class="form-control me-2"
        style="font-size: 13px; text-align: left;" 
        cols="30" rows="3"
        name="pesan" id="pesan"
        placeholder="descriptions..." required><?php echo !empty($doc_num) ? $row['deskripsi'] : ''; ?></textarea>   
        
        <!-- <button 
        type="button"
        name="edit_data"
        id="edit_data"
        class="btn btn-success align-self-start"
        style="line-height: 1; padding: 4px 12px; font-size: 0.875rem; border-radius: 6px; height: 32px; margin-left: 10px;">
        <i class="fas fa-save"></i> Save
    </button>      -->
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
                        <table id="mytablenone" class="table table-striped table-bordered table-hover table-sm nowrap" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                            <thead class="table-gradient">
                                <tr>
                                    <th style="width:10px;">-</th>
                                    <th style="width:100px;">Coa</th>
                                    <th style="width:100px;">Profit Center</th> 
                                    <th style="width:100px;">Cost Center</th>                                                           
                                    <th style="width:100px;">Buyer</th>
                                    <th style="width:100px;">WS</th>
                                    <th style="width:50px;">Currency</th>
                                    <th style="width:100px;">Debit</th>                                                           
                                    <th style="width:100px;">Credit</th>
                                    <th style="width:100px;">Description</th>
                                    <th style="width:8px;">cek</th>
                                </tr>
                            </thead>

                            <tbody id="tbody2">
                                <?php
                                $doc_num = base64_decode($_GET['doc_num']); 
                                $sql_none = mysql_query("select no_doc, id_coa, id_cost_center, buyer, no_ws, curr, t_debit, t_credit, a.keterangan, a.profit_center, concat(b.no_coa,' ', b.nama_coa) as nama_coa,CONCAT(a.id_cost_center,' - ',d.cc_name) cc_name, CONCAT(mp.id_pc,' - ',nama_pc) nama_pc from tbl_bankin a left join mastercoa_v2 b on b.no_coa = a.id_coa left join b_master_cc d on d.no_cc = a.id_cost_center LEFT JOIN master_pc mp on mp.kode_pc = a.profit_center where no_doc = '$doc_num'",$conn1);

                                while($row = mysql_fetch_array($sql_none)){
                                    $id_coa = $row['id_coa'];
                                    $id_cost_center = $row['id_cost_center'];
                                    $t_debit = $row['t_debit'];
                                    $t_credit = $row['t_credit'];
                                    $profit_center = $row['profit_center'];

                                    echo '<tr">
                                    <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                                    <td>
                                    <select class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5"> 
                                    <option value="'.$row['id_coa'].'" >'.$row['nama_coa'].'</option>
                                    <option value="-" > - </option>'; 
                                    $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where no_coa != '$id_coa'"); 
                                    foreach ($sql as $cc) : 
                                     echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; 
                                 endforeach; ?>
                                 <?php
                                 echo '
                                 </select>
                                 </td>';
                                 echo '
                                 <td style="width: 200px;">
                                 <select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" style="width: 250px"> 
                                 <option value="'.$row['profit_center'].'" >'.$row['nama_pc'].'</option>';
                                 $sql3 = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active' and kode_pc != '$profit_center'"); 
                                 foreach ($sql3 as $fc) : 
                                    echo'<option value="'.$fc["kode_pc"].'"> '.$fc["tampil"].' </option>'; 
                                endforeach; 
                                echo'</select>
                                </td>';
                                echo '
                                <td style="width: 200px;">
                                <select class="form-control selectpicker cost_ctr" name="cost_ctr" id="cost_ctr" data-live-search="true" data-width="200px" data-size="5"> 
                                <option value="'.$row['id_cost_center'].'" >'.$row['cc_name'].'</option>';
                                if ($row['id_cost_center'] != '-') {
                                    echo '<option value="-" > - </option>';
                                }
                                $sql2 = mysqli_query($conn1,"select no_cc as code_combine,concat(no_cc,' - ',cc_name) as cost_name from b_master_cc where status = 'Active' and no_cc != '$id_cost_center'"); 
                                foreach ($sql2 as $ccs) : 
                                    echo'<option value="'.$ccs["code_combine"].'"> '.$ccs["cost_name"].' </option>'; 
                                endforeach; ?>
                                <?php
                                echo '
                                </select>
                                </td>';

                                echo '<td>
                                <input style="text-align: left;font-size: 14px;" type="text" class="form-control" name="buyer" placeholder="" value="'.$row['buyer'].'" autocomplete = "off">
                                </td>
                                <td>
                                <input style="text-align: left;font-size: 14px;" type="text" class="form-control" name="ws" placeholder="" value="'.$row['no_ws'].'" autocomplete = "off">
                                </td>
                                <td>
                                <select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true">
                                <option value="'.$row['curr'].'" >'.$row['curr'].'</option>';
                                if ($row['curr'] == 'IDR') {
                                    echo '<option value="USD">USD</option>';
                                }else{
                                    echo '<option value="IDR">IDR</option> ';
                                }
                                echo '</div>
                                </td>';
                                if ($t_debit == '0') {
                                    echo '<td>
                                    <input style="text-align: right;font-size: 14px;" type="number" min="1"class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off" readonly>
                                    </td>';
                                }else{
                                    echo '<td>
                                    <input style="text-align: right;font-size: 14px;" type="number" min="1" value="'.$t_debit.'"  class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
                                    </td>';
                                }

                                if ($t_credit == '0') {
                                    echo '<td>
                                    <input style="text-align: right;font-size: 14px;" type="number" min="1" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete = "off" readonly>
                                    </td>';
                                }else{
                                    echo '<td>
                                    <input style="text-align: right;font-size: 14px;" type="number" min="1" value="'.$t_credit.'" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete = "off">
                                    </td>';
                                }

                                echo'<td>
                                <input style="text-align: left;font-size: 14px;" type="text" class="form-control" name="keterangan" placeholder="" value="'.$row['keterangan'].'" autocomplete = "off">
                                </td>
                                <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
                                </tr>

                                ';
                            }
                            ?>
                        </tbody> 
                        <?php
                        echo '
                        <tfoot>
                        <tr>
                        <td colspan="11" align="center">
                        <button type="button" class="btn btn-primary" onclick=addRow("tbody2");>Add Row</button>
                        <button type="button" class="btn btn-warning" onclick=InsertRow("tbody2");>Interject Row</button>
                        <button type="button" class="btn btn-danger" onclick=deleteRow("tbody2");>Delete Row</button>
                        </td>
                        </tr>
                        </tfoot>';
                        ?>                   
                    </table>
                </div>
                <?php
                $sql = mysqli_query($conn2,"select eqv_idr from tbl_bankin_arcollection where doc_num = '$doc_num'");
                $row = mysqli_fetch_array($sql);
                $eqv_idr = $row['eqv_idr'];
                ?>
                <div class="row mt-1 p-3">

                    <!-- NAG -->
                    <div class="col-md-4">
                        <div class="total-box tone-nag">
                            <div class="total-box-header"><i class="fa fa-building"></i> Total PT. Nirwana Alabare Garment</div>
                            <div class="total-box-body">
                                <div class="total-stat is-debit">
                                    <span class="total-stat-label">Total Debit</span>
                                    <div class="total-stat-value-wrap">
                                        <input type="text" class="total-stat-value" id="tot_debit_nag" name="tot_debit_nag" readonly>
                                        <input type="hidden" id="h_tot_debit_nag" name="h_tot_debit_nag" readonly>
                                    </div>
                                </div>
                                <div class="total-stat is-credit">
                                    <span class="total-stat-label">Total Credit</span>
                                    <div class="total-stat-value-wrap">
                                        <input type="text" class="total-stat-value" id="tot_credit_nag" name="tot_credit_nag" readonly>
                                        <input type="hidden" id="h_tot_credit_nag" name="h_tot_credit_nag" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- NAK -->
                    <div class="col-md-4">
                        <div class="total-box tone-nak">
                            <div class="total-box-header"><i class="fa fa-industry"></i> Total PT. Nirwana Alabare Knitting</div>
                            <div class="total-box-body">
                                <div class="total-stat is-debit">
                                    <span class="total-stat-label">Total Debit</span>
                                    <div class="total-stat-value-wrap">
                                        <input type="text" class="total-stat-value" id="tot_debit_nak" name="tot_debit_nak" readonly>
                                        <input type="hidden" id="h_tot_debit_nak" name="h_tot_debit_nak" readonly>
                                    </div>
                                </div>
                                <div class="total-stat is-credit">
                                    <span class="total-stat-label">Total Credit</span>
                                    <div class="total-stat-value-wrap">
                                        <input type="text" class="total-stat-value" id="tot_credit_nak" name="tot_credit_nak" readonly>
                                        <input type="hidden" id="h_tot_credit_nak" name="h_tot_credit_nak" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="total-box tone-all">
                            <div class="total-box-header"><i class="fa fa-calculator"></i> Grand Total</div>
                            <div class="total-box-body">
                                <div class="total-stat is-debit">
                                    <span class="total-stat-label">Total Debit</span>
                                    <div class="total-stat-value-wrap">
                                        <input type="text" class="total-stat-value" id="tot_debit" name="tot_debit" value="<?= number_format($eqv_idr,2); ?>" readonly>
                                        <input type="hidden" id="h_tot_debit" name="h_tot_debit" value="<?= $eqv_idr; ?>" readonly>
                                    </div>
                                </div>
                                <div class="total-stat is-credit">
                                    <span class="total-stat-label">Total Credit</span>
                                    <div class="total-stat-value-wrap">
                                        <input type="text" class="total-stat-value" id="tot_credit" name="tot_credit" value="<?= number_format($eqv_idr,2); ?>" readonly>
                                        <input type="hidden" id="h_tot_credit" name="h_tot_credit" value="<?= $eqv_idr; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="form-row">
                    <div class="col-md-3 mt-3 mb-2">
                        <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="edit_data" id="edit_data"><span class="fa fa-floppy-o"></span> Save</button>
                        <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='bank-in.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                    </div>
                </div>
        </div>
    </div>
</form>
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
    function formatLiveIndo(value) {
      value = String(value).replace(/[^0-9,]/g, '');
      if(value === '') return '';
      let parts = value.split(',');
      let intPart = parts[0];
      let decPart = parts[1] || '';
                              decPart = decPart.substring(0,2); // batasi 2 desimal
                              intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                              return decPart ? intPart + ',' + decPart : intPart;
                          }

                          function formatLiveEn(value) {
                              value = String(value).replace(/[^0-9.]/g, '');
                              if (value === '') return '';

                              if (value.endsWith('.')) {
                                let intPart = value.slice(0, -1);
                                intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                return intPart + '.'; 
                            }

                            let parts = value.split('.');
                            let intPart = parts[0];
                            let decPart = parts[1] || '';

                            decPart = decPart.substring(0, 2);

                            intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                            return decPart ? intPart + '.' + decPart : intPart;
                        }


                        document.getElementById('amount').addEventListener('input', function(e) {
                            let amount = document.getElementById('amount');
                            amount.value = amount.value.replace(/,/g, '');
                            let rate = document.getElementById('rate');
                            rate.value = rate.value.replace(/,/g, '');
                            let ttl_idr = amount.value * rate.value;  
                            $("#eqv_idr").val(formatMoney(ttl_idr));
                            modal_input_amt();
                            const el = e.target;
                            const origLen = el.value.length;
                            const selStart = el.selectionStart;
                            const offsetFromEnd = origLen - selStart;

                            const newVal = formatLiveEn(el.value);
                            el.value = newVal;

                            const newLen = newVal.length;
                            let newPos = newLen - offsetFromEnd;
                            if (newPos < 0) newPos = 0;
                            try { el.setSelectionRange(newPos, newPos); } catch (err) {}

                        });



                        document.getElementById('rate').addEventListener('input', function(e) {
                            let amount = document.getElementById('amount');
                            amount.value = amount.value.replace(/,/g, '');
                            let rate = document.getElementById('rate');
                            rate.value = rate.value.replace(/,/g, '');
                            let ttl_idr = amount.value * rate.value;  
                            $("#eqv_idr").val(formatMoney(ttl_idr));

                            const el = e.target;
                            const origLen = el.value.length;
                            const selStart = el.selectionStart;
                            const offsetFromEnd = origLen - selStart;

                            const newVal = formatLiveEn(el.value);
                            el.value = newVal;

                            const newLen = newVal.length;
                            let newPos = newLen - offsetFromEnd;
                            if (newPos < 0) newPos = 0;
                            try { el.setSelectionRange(newPos, newPos); } catch (err) {}
                        });

// // saat form submit -> ubah ke angka mentah (tanpa format)
// document.querySelector('form').addEventListener('submit', function() {
//     let input = document.getElementById('amount');
//     input.value = input.value.replace(/,/g, '');
// });
</script>


<script type="text/javascript">

    function UbahCostArc(val) {
                                    var profit = val; // Ambil nilai parameter
    // alert(profit); // Debug: pastikan nilai 'profit' diterima

    // Pastikan selektor dropdown sesuai dengan nama elemen
    const costCtrDropdown = $('select[name="cost"]');
    const no_coa = $('select[name=coa] option').filter(':selected').val();
    console.log(profit + ' ' + no_coa)

    // Hapus opsi yang ada terlebih dahulu, kecuali opsi default
    costCtrDropdown.find('option').not(':first').remove();

    $.ajax({
                                        url: 'getCostCenter.php', // URL endpoint server Anda
                                        type: 'POST',
                                        data: { prof_ctr: profit, no_coa: no_coa }, // Kirim data prof_ctr ke server
                                        dataType: 'json',
                                        success: function (response) {
            // Periksa apakah respons valid
            if (response && response.length > 0) {
                $.each(response, function (index, costCtr) {
                    console.log(costCtr);
                    costCtrDropdown.append(`<option value="${costCtr.value}">${costCtr.text}</option>`);
                });

                costCtrDropdown.selectpicker('refresh');
            } else {
                console.error('Tidak ada data yang diterima dari server.');
                alert('Tidak ada data cost center yang tersedia.');
            }
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
        }
    });
}

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
                                                            const costCtrDropdown = $(row).find('.cost_ctr'); // dropdown cost center pada baris tsb

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

let coaWajibCC = [];
$.getJSON('get_coa_wajib_cc.php', function(data){
    coaWajibCC = data;
});

$('#accountid').on('change', function() {
    let opt = $(this).find(':selected');
    let bank = opt.data('bank');
    let curr = opt.data('curr');
    let kodebank = opt.data('kodebank');
    let kodepc = opt.data('kodepc');

    if (typeof bank !== 'undefined') {
        $('#nama_bank').val(bank);
        $('#valuta').val(curr);
        $('#kode_bank_acc').val(kodebank);
        $('#pc_bank_acc').val(kodepc);
        $('#profit_center').val(kodepc);

        if (curr === 'IDR') {
            $('#rate').val('1').prop('readonly', true);
        } else {
            $('#rate').prop('readonly', false);
        }

        let amount = parseFloat($('#amount').val().replace(/,/g, '')) || 0;
        let rate = parseFloat($('#rate').val().replace(/,/g, '')) || 0;
        $('#eqv_idr').val(formatMoney(amount * rate));
    }
});

function initializePlugins() {
    $(function () {
        $('.selectpicker').selectpicker();
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    });
}


   // JavaScript Document
   function addRow(tableID) {
    var tableID = "tbody2";
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;
    var row = table.insertRow(rowCount);

    $(function() {
        $('.selectpicker').selectpicker();
    });
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose:true
        });
    });
    $(function() {
      //Initialize Select2 Elements
      var selectcoba = rowCount;
      $('.rowCount').select2({
       theme: 'bootstrap4'
   })
      //Initialize Select2 Elements
      $('.select2add').select2({
        theme: 'bootstrap4'
    })
  });
    $coa = '';
    var element1 = `
    <tr>
    <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
    <td style="width: 50px">
    <select class="form-control selectpicker no_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
    <option value="-">-</option>
    <?php $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?>
    <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-live-search="true" data-width="250px" data-size="5">
<option value="-"> - </option>
<?php
$sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
foreach ($sql3 as $fc) : ?>
    <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
<?php endforeach; ?>
</select>
</td>
<td>
<select class="form-control selectpicker cost_ctr" name="cost_ctr[]" id="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
<option value="-"> - </option>
</select>
</td>
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="buyer" placeholder="" autocomplete="off"></td>
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="ws" placeholder="" autocomplete="off"></td>
<td>
<select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true">
<option value="IDR">IDR</option>
<option value="USD">USD</option>
</select>
</td>
<td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>
<td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete="off"></td>
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan" placeholder="" autocomplete="off"></td>
<td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
</tr>
`;



row.innerHTML = element1; 
initializePlugins();  

var headerPC = $('#profit_center').val();
if (headerPC) {
    $(row).find('.prof_ctr').val(headerPC);
    $(row).find('.prof_ctr').selectpicker('refresh');
                    // updateCostCenter(headerPC, row);
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
                        var chkbox = row.cells[10].childNodes[0];
                        if (null != chkbox && true == chkbox.checked)
                        {
                            if (rowCount <= 1)
                            {
                                alert("Tidak dapat menghapus semua baris.");
                                break;
                            }
                            table.deleteRow(i);
                            modal_input_amt();
                            modal_input_cre();
                            rowCount--;
                            i--;
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
                        var chkbox = row.cells[10].childNodes[0];
                        if (null != chkbox && true == chkbox.checked)
                        {
                            $(function() {
                                $('.selectpicker').selectpicker();

                            });

                            $(document).ready(function () {
                                $('.tanggal').datepicker({
                                    format: "dd-mm-yyyy",
                                    autoclose:true
                                });
                            });
                            var element2 = `
                            <tr>
                            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
                            <td style="width: 50px">
                            <select class="form-control selectpicker no_coa" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5">
                            <option value="-">-</option>
                            <?php $sql = mysqli_query($conn1, "select no_coa as id_coa, concat(no_coa, ' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : ?>
                            <option value="<?= $coa["id_coa"]; ?>"><?= $coa["coa"]; ?></option>
                        <?php endforeach; ?>
                        </select>
                        </td>
                        <td>
                        <select class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-live-search="true" data-width="250px" data-size="5">
                        <option value="-"> - </option>
                        <?php
                        $sql3 = mysqli_query($conn1, "select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                        foreach ($sql3 as $fc) : ?>
                            <option value="<?= $fc['kode_pc']; ?>"><?= $fc['tampil']; ?></option>
                        <?php endforeach; ?>
                        </select>
                        </td>
                        <td>
                        <select class="form-control selectpicker cost_ctr" name="cost_ctr[]" id="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
                        <option value="-"> - </option>
                        </select>
                        </td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="buyer" placeholder="" autocomplete="off"></td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="ws" placeholder="" autocomplete="off"></td>
                        <td>
                        <select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true">
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                        </select>
                        </td>
                        <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_amount" name="txt_amount" oninput="modal_input_amt(value)" autocomplete="off"></td>
                        <td><input style="text-align: right;width: 150px;" type="number" min="1" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete="off"></td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan" placeholder="" autocomplete="off"></td>
                        <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""></td>
                        </tr>
                        `;
                        var newRow = table.insertRow(i+1);
                        newRow.innerHTML = element2;
                        initializePlugins();

                        var headerPC = $('#profit_center').val();
                        if (headerPC) {
                            $(row).find('.prof_ctr').val(headerPC);
                            $(row).find('.prof_ctr').selectpicker('refresh');
                    // updateCostCenter(headerPC, row);
                }

            }

        }
    } catch(e)
    {
        alert(e);
    }
}
</script>


<script type="text/javascript">
  function modal_input_amt() { 
    let deb  = $("#eqv_idr").val().replace(/,/g, '') || 0;   
    let rate = $("#rate").val().replace(/,/g, '') || 1;

    var table = document.getElementById("tbody2");
    var tota = 0;

    for (var i = 0; i < table.rows.length; i++) {
        var $row  = $(table.rows[i]);
        var curr  = $row.find("select[name='currenc']").val();
        var price = parseFloat($row.find("input[name='txt_amount']").val()) || 0;
        var price2 = $row.find("input[name='txt_credit']");

        let harga = 0;
        if (price === 0) {
            price2.prop('readonly', false);
        } else {
            if (curr === 'USD') {
                harga = price * rate;
            } else {
                harga = price;
            }
            price2.prop('readonly', true);
        }

        tota += harga;
    }

    let tot_deb = parseFloat(tota) + parseFloat(deb);

    document.getElementsByName("tot_debit")[0].value = formatMoney(tot_deb.toFixed(2));
    document.getElementsByName("h_tot_debit")[0].value = tot_deb.toFixed(2);

    hitung_total_pc();
}

/* Ganti Currency atau Profit Center di baris detail harus ikut menghitung
   ulang total (konversi ke IDR pakai Rate header untuk baris USD). */
$(document).on('change', '#tbody2 select[name="currenc"], #tbody2 select[name="prof_ctr"]', function(){
    hitung_total_pc();
});

function hitung_total_pc() {

    let debit_nag = 0;
    let credit_nag = 0;
    let debit_nak = 0;
    let credit_nak = 0;

    let rate = parseFloat($("#rate").val().replace(/,/g, '')) || 1;

    $("#tbody2 tr").each(function () {
        let pc = $(this).find("select[name='prof_ctr']").val();
        let curr = $(this).find("select[name='currenc']").val();

        let debit = parseFloat($(this).find("input[name='txt_amount']").val()) || 0;
        let credit = parseFloat($(this).find("input[name='txt_credit']").val()) || 0;

        if (curr === 'USD') {
            debit = debit * rate;
            credit = credit * rate;
        }

        if (pc === 'NAG') {
            debit_nag += debit;
            credit_nag += credit;
        }

        if (pc === 'NAK') {
            debit_nak += debit;
            credit_nak += credit;
        }
    });

    let header_pc = $('#pc_bank_acc').val();
    let eqv = parseFloat($("#eqv_idr").val().replace(/,/g, '')) || 0;

    if (header_pc === 'NAG') {
        debit_nag += eqv;
    }

    if (header_pc === 'NAK') {
        debit_nak += eqv;
    }

    $('#tot_debit_nag').val(formatMoney(debit_nag));
    $('#tot_credit_nag').val(formatMoney(credit_nag));

    $('#tot_debit_nak').val(formatMoney(debit_nak));
    $('#tot_credit_nak').val(formatMoney(credit_nak));

    $('#h_tot_debit_nag').val(debit_nag);
    $('#h_tot_credit_nag').val(credit_nag);

    $('#h_tot_debit_nak').val(debit_nak);
    $('#h_tot_credit_nak').val(credit_nak);
}

</script>

<script type="text/javascript">
  function modal_input_cre() { 
    let rate = $("#rate").val().replace(/,/g, '') || 1;

    var table = document.getElementById("tbody2");
    var tota = 0;

    for (var i = 0; i < table.rows.length; i++) {
        var $row = $(table.rows[i]);
        var curr  = $row.find("select[name='currenc']").val();
        var price = parseFloat($row.find("input[name='txt_credit']").val()) || 0;
        var price2 = $row.find("input[name='txt_amount']");

        let harga = 0;
        if (price === 0) {
            price2.prop('readonly', false);
        } else {
            if (curr === 'USD') {
                harga = price * rate;
            } else {
                harga = price;
            }
            price2.prop('readonly', true);
        }

        tota += harga;
    }

    document.getElementsByName("tot_credit")[0].value = formatMoney(tota.toFixed(2));
    document.getElementsByName("h_tot_credit")[0].value = tota.toFixed(2);

    hitung_total_pc();
}

</script>

<script type="text/javascript">
    $(document).on("click", "#edit_data", function () {
        // alert(1);
        let doc_num         = $("#no_bankin").val();
        let date            = $("#tgl_bankin").val();
        let ref_data        = $("#ref_data").val();
        let customer        = $("#nama_supp").val();
        let profit_center   = $("#profit_center").val();
        let akun            = $("#accountid").val();
        let curr            = $("#valuta").val();
        let bank            = $("#nama_bank").val();
        let amount          = $("#amount").val().replace(/,/g, '');
        let rate            = $("#rate").val().replace(/,/g, '');
        let eqv_idr         = $("#eqv_idr").val().replace(/,/g, '');
        let deskripsi       = $("#pesan").val();
        let h_tot_debit     = $("#h_tot_debit").val();
        let h_tot_credit    = $("#h_tot_credit").val();
        let kode_bank_acc   = $("#kode_bank_acc").val();
        let pc_bank_acc     = $("#pc_bank_acc").val();
        var create_user     = '<?php echo $user; ?>';

        console.log ("doc_num : " + doc_num);
        console.log ("date : " + date);
        console.log ("ref_data : " + ref_data);
        console.log ("customer : " + customer);
        console.log ("profit_center : " + profit_center);
        console.log ("akun : " + akun);
        console.log ("curr : " + curr);
        console.log ("bank : " + bank);
        console.log ("amount : " + amount);
        console.log ("rate : " + rate);
        console.log ("eqv_idr : " + eqv_idr);
        console.log ("deskripsi : " + deskripsi);
        console.log ("h_tot_debit : " + h_tot_debit);
        console.log ("h_tot_credit : " + h_tot_credit);
        var total_nak = 0;
        var total_nag = 0;


        if (akun === '') {
            Swal.fire('Warning', 'Account tidak boleh kosong', 'warning');
            return;
        }

        if (deskripsi.trim() === '') {
            Swal.fire('Warning', 'Description tidak boleh kosong', 'warning');
            return;
        }

        if (curr !== 'IDR' && (rate === '' || parseFloat(rate) === 0 || parseFloat(rate) === 1)) {
            Swal.fire('Warning', 'Currency non IDR harus memiliki rate diisi dan tidak boleh 1', 'warning');
            return;
        }

        let cash_flow = $('#cash_flow').val();
        if (!cash_flow || cash_flow === '') {
            Swal.fire('Warning', 'Cash Flow Category tidak boleh kosong', 'warning');
            return;
        }

        if (h_tot_debit != h_tot_credit) {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Some required fields are missing. Please complete all fields before proceeding."
            });
            return;
        }

        let details = [];
        let validationError = false;

        $("#tbody2 tr").each(function (rowIndex) {
            let coa         = $(this).find("select[name='nomor_coa']").val();
            let prof_ctr    = $(this).find("select[name='prof_ctr']").val();
            let cost_ctr    = $(this).find("select[name='cost_ctr']").val() || '-';
            let buyer       = $(this).find("input[name='buyer']").val();
            let ws          = $(this).find("input[name='ws']").val();
            let currency    = $(this).find("select[name='currenc']").val();
            let debit       = parseFloat($(this).find("input[name='txt_amount']").val()) || 0;
            let credit      = parseFloat($(this).find("input[name='txt_credit']").val()) || 0;
            let keterangan  = $(this).find("input[name='keterangan']").val();

            console.log ("det_coa : " + coa);
            console.log ("det_prof_ctr : " + prof_ctr);
            console.log ("det_cost_ctr : " + cost_ctr);
            console.log ("det_buyer : " + buyer);
            console.log ("det_ws : " + ws);
            console.log ("det_currency : " + currency);
            console.log ("det_debit : " + debit);
            console.log ("det_credit : " + credit);
            console.log ("det_keterangan : " + keterangan);

            if (validationError) return;

            if (!coa || coa === '-') {
                Swal.fire('Warning', 'COA wajib diisi', 'warning');
                validationError = true;
                return false;
            }

            if (!prof_ctr || prof_ctr === '-') {
                Swal.fire('Warning', 'Profit Center wajib diisi', 'warning');
                validationError = true;
                return false;
            }

            if (coaWajibCC.includes(coa) && (cost_ctr === '-' || cost_ctr === '' || cost_ctr === null)) {
                Swal.fire('Warning', 'COA ' + coa + ' wajib isi Cost Center', 'warning');
                validationError = true;
                return false;
            }

            if (debit === 0 && credit === 0) {
                Swal.fire('Warning', 'Debit/Credit harus diisi', 'warning');
                validationError = true;
                return false;
            }

            if (prof_ctr === "NAK") {
                total_nak += (credit - debit);
            } else if (prof_ctr === "NAG") {
                total_nag += (credit - debit);
            }

            details.push({
                coa: coa,
                prof_ctr: prof_ctr,
                cost_ctr: cost_ctr,
                buyer: buyer,
                ws: ws,
                currency: currency,
                debit: debit,
                credit: credit,
                keterangan: keterangan
            });
    });

        if (validationError) return;

        if (details.length === 0) {
            Swal.fire('Warning', 'Detail transaksi tidak boleh kosong', 'warning');
            return;
        }

        console.log ("det_total_nak : " + total_nak);
        console.log ("det_total_nag : " + total_nag);

        Swal.fire({
            title: "Are you sure?",
            text: "The data will be updated.",
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
                    url: "update_bi_none.php",
                    data: {
                        doc_num: doc_num,
                        date: date,
                        ref_data: ref_data,
                        customer: customer,
                        profit_center: profit_center,
                        akun: akun,
                        curr: curr,
                        bank: bank,
                        amount: amount,
                        rate: rate,
                        eqv_idr: eqv_idr,
                        deskripsi: deskripsi,
                        h_tot_debit: h_tot_debit,
                        h_tot_credit: h_tot_credit,
                        kode_bank_acc: kode_bank_acc,
                        pc_bank_acc: pc_bank_acc,
                        cash_flow: cash_flow,
                        create_user: create_user,
                        total_nak: total_nak,
                        total_nag: total_nag,
                        details: JSON.stringify(details)
                    },
                    success: function (res) {
                        let resTrim = res.trim();
                        if (resTrim === "OK" || resTrim.startsWith("OK|")) {
                            let newDocNum = resTrim.startsWith("OK|") ? resTrim.split("|")[1] : null;
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: newDocNum
                                    ? "Data berhasil diupdate. Nomor dokumen berubah menjadi " + newDocNum
                                    : "Data has been successfully updated!"
                            }).then(() => {
                                window.location.href = "bank-in.php";
                                // window.location.reload();
                            });
                        } else {
                            Swal.fire("Error", res, "error");
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Failed to update : " + xhr.responseText
                        });
                    }
                });
            }
        });
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
    $(document).ready(function() {
        initializePlugins();
        hitung_total_pc();
        $('#mytablenone').DataTable({
            paging: false,          // Menambahkan paging
            searching: false,       // Menambahkan pencarian
            scrollCollapse: true,  // Mengatasi jika data tidak cukup
            fixedHeader: true,     // Menjaga header tetap terlihat
            language: {
            info: "", // Menghilangkan teks "Showing 1 to 1 of 1 entries"
            infoEmpty: "", // Untuk keadaan ketika tidak ada data
            infoFiltered: "" // Untuk keadaan filter
        }    // Menjaga header tetap terlihat
    });
        $("[data-toggle=tooltip]").tooltip();

    } );

    $(document).ready(function () {
        $('#mytable').DataTable({
            paging: false,
            searching: true,
            info: false,
            scrollY: "300px",
            scrollCollapse: true,
            scrollX: true
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
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            startDate : "01-01-2021",
            autoclose:true
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
    $("#select_all").click(function() {
      var c = this.checked;
      $(':checkbox').prop('checked', c);
  });  
</script>

</body>

</html>
