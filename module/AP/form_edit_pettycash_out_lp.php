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


</style>

<?php 
$doc_num = base64_decode($_GET['doc_num']); 

$sql = mysqli_query($conn2,"select a.*, CONCAT(a.coa_akun,' ',nama_coa) nama_akun from c_petty_cashout_h a INNER JOIN mastercoa_v2 b on b.no_coa = a.coa_akun where no_pco = '$doc_num'");
$row = mysqli_fetch_array($sql);                        ;
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <div class="card border-secondary mb-3">
    <div class="card-header" style="background: linear-gradient(90deg, #191970, #1e90ff);">
      <div class="d-flex align-items-center justify-content-start">
        <img src="../../images/note.png" alt="Bank Logo" 
        style="width:25px; height:auto; margin-right:10px;">
        <h5 class="mb-0 text-white">FORM EDIT PETTY CASH OUT</h5>
    </div>
</div>

<form id="form-data" method="post">
    <div class="card shadow-sm mb-4">
        <div class="card-body p-2">
            <div class="form-row">

                <div class="col-md-3 mb-3">            
                    <label for="pajak" style="width: 150px;"><b>Doc Number</b></label>
                    <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="no_pco" name="no_pco" value="<?= $doc_num; ?>">
                </div>

                <div class="col-md-2 mb-3">            
                    <label for="total" style="width: 150px;"><b>Date</b></label>
                    <input type="text" style="font-size: 13px;" name="tgl_pco" id="tgl_pco" class="form-control form-control-sm tanggal" 
                    value="<?php if(!empty($doc_num)) {
                        echo date("d-m-Y",strtotime($row['tgl_pco']));
                    }
                    else{
                        echo date("d-m-Y");
                    } ?>" autocomplete='off'>
                </div>

                <div class="col-md-3 mb-3">            
                    <label for="nama_supp" style="width: 150px;"><b>Reference</b></label>            
                    <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="reff_doc" name="reff_doc" value="<?php 
                    if(!empty($doc_num)) {
                        echo $row['reff'];
                    }
                    else{
                        echo '';
                    } 
                    ?>">
                </div> 
                <div class="col-md-4 mb-3"></div>

                <div class="col-md-3 mb-3">            
                    <label for="nama_supp"><b>Supplier</b></label>            
                    <select class="form-control selectpicker" name="nama_supp" id="nama_supp" data-dropup-auto="false" data-live-search="true">                                   
                        <?php
                        $customer = $row['nama_supp'];  
                        $isSelected = ' selected="selected"';                      
                        if(!empty($doc_num)) {
                            echo '<option value="'.$customer.'"'.$isSelected.'">'. $customer .'</option>'; 
                        }
                        else{
                            echo '<option value="Unrealize"  selected="true">Unrealize</option>'; 
                        }

                        // $sql_supp = mysqli_query($conn1,"select distinct(Supplier) from mastersupplier where tipe_sup = 'C' and Supplier != '$customer' order by Supplier ASC");
                        // while ($row_supp = mysqli_fetch_array($sql_supp)) {
                        //     $data = $row_supp['Supplier'];
                        //     if($row_supp['Supplier'] == $_POST['nama_supp']){
                        //         $isSelected = ' selected="selected"';
                        //     }else{
                        //         $isSelected = '';

                        //     }
                        //     echo '<option value="'.$data.'"'.$isSelected.'">'. $data .'</option>';   
                        // }
                        ?>
                    </select>  
                </div>

                <div class="col-md-2 mb-3">            
                   <label for="profit_center" style="width: 150px;"><b>Profit Center</b></label>            
                   <select class="form-control selectpicker" name="profit_center" id="profit_center" data-dropup-auto="false" data-live-search="true" onChange="UbahCostArc(this.value)">                                   
                    <?php
                    $profit_center = $row['profit_center'];  
                    $isSelected = ' selected="selected"';  
                    $sql_pctr = mysqli_query($conn2,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where kode_pc = '$profit_center'");             
                    $row_pctr = mysqli_fetch_array($sql_pctr); 

                    if(!empty($doc_num)) {
                        echo '<option value="'.$profit_center.'"'.$isSelected.'">'. $row_pctr['tampil'] .'</option>'; 
                    }
                    else{
                        echo '<option value="" disabled selected="true">Select Profit Center</option>'; 
                    }


                    $sql_pc = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active' and kode_pc != '$profit_center'");
                    while ($row_pc = mysqli_fetch_array($sql_pc)) {
                        $data = $row_pc['tampil'];
                        $code_combine = $row_pc['kode_pc'];
                        if($row_pc['kode_pc'] == $_POST['profit_center']){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';
                        }
                        echo '<option value="'.$code_combine.'"'.$isSelected.'">'. $data .'</option>';    
                    }
                    ?>
                </select>  
            </div>
            <div class="col-md-6 mb-3"> </div>

            <div class="col-md-3 mb-3">            
                <label for="nama_supp" style="width: 150px;"><b>Account</b></label>            
                <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="accountid" name="accountid" value="<?php 
                if(!empty($doc_num)) {
                    echo $row['nama_akun'];
                }
                else{
                    echo '';
                } 
                ?>">
                <input type="hidden" readonly style="font-size: 13px;" class="form-control form-control-sm" id="accountid_h" name="accountid_h" value="<?php 
                if(!empty($doc_num)) {
                    echo $row['coa_akun'];
                }
                else{
                    echo '';
                } 
                ?>">
            </div>

            <div class="col-md-2 mb-3">            
                <label for="nama_supp" style="width: 150px;"><b>Curr</b></label>            
                <input type="text" readonly style="font-size: 13px;" class="form-control form-control-sm" id="valuta" name="valuta" value="<?php 
                if(!empty($doc_num)) {
                    echo $row['curr'];
                }
                else{
                    echo '';
                } 
                ?>">
            </div>

            <div class="col-md-3 mb-3 custom-col">            
              <label for="amount" ><b>Amount</b></label>            
              <div class="input-group">
                <input type="text" class="form-control" id="amount" name="amount" style="font-size: 13px; text-align: right;" placeholder="0.00"
                value="<?php echo !empty($doc_num) ? number_format($row['amount'], 2) : ''; ?>">
                <input type="hidden" class="form-control" id="rate" name="rate" style="font-size: 13px; text-align: right;" placeholder="0.00"
                value="1" >
                <input type="hidden" style="font-size: 13px;text-align: right;" class="form-control" id="eqv_idr" name="eqv_idr" value="<?php echo !empty($doc_num) ? number_format($row['amount'], 2) : ''; ?>" placeholder="0.00" readonly>
            </div>
        </div>
        <div class="col-md-4 mb-3"></div>

        <div class="col-md-5 mb-3"> 
            <label for="nama_supp"><b>Descriptions</b></label>         
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
    <div class="card-body p-2">
        <div class="table-responsive">
            <table id="mytable" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                <thead>
                    <tr class="text-white" style="background-color: #1E3A8A;">
                        <th style="width:100px;">No LP</th>
                        <th style="width:100px;">LP Date</th>
                        <th style="width:100px;">Due Date</th>
                        <th style="width:100px;">DPP</th>
                        <th style="width:100px;">PPN</th>
                        <th style="width:100px;">PPH</th>
                        <th style="width:100px;">Total</th>
                        <th style="width:100px;">Total IDR</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $total_payment = 0;
                    $doc_num = base64_decode($_GET['doc_num']); 
                    $sql_datadet = mysql_query("select no_pco no_reff, reff_date, due_date, dpp, ppn, pph, total, eqv_idr from c_petty_cashout_det where no_pco = '$doc_num'",$conn1);

                    while($row_datadet = mysqli_fetch_array($sql_datadet)){
                        $total_payment += $row_datadet['eqv_idr'];
                        echo '<tr style="font-size:12px;text-align:center;">
                        <td style="width: 150px;" value = "'.$row_datadet['no_reff'].'">'.$row_datadet['no_reff'].'</td>
                        <td style="width: 100px;" value = "'.$row_datadet['reff_date'].'">'.date("d-M-Y",strtotime($row_datadet['reff_date'])).'</td>
                        <td style="width: 100px;" value = "'.$row_datadet['due_date'].'">'.date("d-M-Y",strtotime($row_datadet['due_date'])).'</td>
                        <td style="width:125px; text-align : right;" value="'.$row_datadet['dpp'].'">'.number_format($row_datadet['dpp'],2).'</td>
                        <td style="width:125px; text-align : right;" value="'.$row_datadet['ppn'].'">'.number_format($row_datadet['ppn'],2).'</td>
                        <td style="width:125px; text-align : right;" value="'.$row_datadet['pph'].'">'.number_format($row_datadet['pph'],2).'</td>
                        <td style="width:125px; text-align : right;" value="'.$row_datadet['total'].'">'.number_format($row_datadet['total'],2).'</td>
                        <td style="width:125px; text-align : right;" value="'.$row_datadet['eqv_idr'].'">'.number_format($row_datadet['eqv_idr'],2).'</td>
                        </tr>';
                    }
                    ?>
                </tbody>          
            </table>
        </div>
        <div class="form-row col mt-3">
            <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b>Total Debit</b></label>
            <div class="col-md-2 mb-3">                            
                <input type="text" class="form-control form-control-sm" name="tot_payment" id="tot_payment" value="<?= number_format($total_payment,2); ?>" placeholder="0.00" style="font-size: 14px;;text-align: right;" readonly>
                <input type="hidden" name="h_tot_payment" id="h_tot_payment" value="<?= $total_payment; ?>">

            </div>
        </div>

    </div>
</div>


<div class="card shadow-sm mb-4">
    <div class="card-body p-2">
        <div class="table-responsive">
            <table id="mytablenone" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                <thead>
                    <tr class="text-white" style="background-color: #2563EB;">
                        <th style="width:10px;">-</th>
                        <th style="width:100px;">Coa</th>
                        <th style="width:100px;">Profit Center</th> 
                        <th style="width:100px;">Cost Center</th>                                                           
                        <th style="width:100px;">Reff Document</th>
                        <th style="width:100px;">Reff Date</th>
                        <th style="width:100px;">Debit</th>                                                           
                        <th style="width:100px;">Credit</th>
                        <th style="width:100px;">Description</th>
                        <th style="width:8px;">cek</th>
                    </tr>
                </thead>

                <tbody id="tbody2">
                    <?php
                    $doc_num = base64_decode($_GET['doc_num']); 
                    $sql_none = mysql_query("select no_pco no_doc, a.id_coa, a.no_cc id_cost_center, reff_doc, reff_date, t_debit, t_credit, a.deskripsi keterangan, a.profit_center, concat(b.no_coa,' ', b.nama_coa) as nama_coa,CONCAT(a.no_cc,' - ',d.cc_name) cc_name, CONCAT(mp.id_pc,' - ',nama_pc) nama_pc from c_petty_cashout_adj_det a left join mastercoa_v2 b on b.no_coa = a.id_coa left join b_master_cc d on d.no_cc = a.no_cc LEFT JOIN master_pc mp on mp.kode_pc = a.profit_center where no_pco = '$doc_num'",$conn1);

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
                    <input style="text-align: left;font-size: 14px;" type="text" class="form-control" name="refferensi" placeholder="" value="'.$row['reff_doc'].'" autocomplete = "off">
                    </td>
                    <td>';
                    if ($row['reff_date'] == '1970-01-01') {
                        echo'<input style="text-align: left;font-size: 14px;" type="text" class="form-control tanggal_det" name="tgl_refferensi" placeholder="" value="" autocomplete = "off">';
                    }else{
                        echo'<input style="text-align: left;font-size: 14px;" type="text" class="form-control tanggal_det" name="tgl_refferensi" placeholder="" value="'.$row['reff_date'].'" autocomplete = "off">';
                    }
                    echo'</td>';
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
    <div class="form-row col mt-3">
        <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b>Total Debit</b></label>
        <div class="col-md-2 mb-3"> 
            <?php
            $sql = mysqli_query($conn2,"select amount eqv_idr from c_petty_cashout_h where no_pco = '$doc_num'");
            $row = mysqli_fetch_array($sql);                         
            $eqv_idr = $row['eqv_idr'];
            ?>                                
            <input type="text" class="form-control form-control-sm" name="tot_debit" id="tot_debit" value="<?= number_format($eqv_idr,2); ?>" placeholder="0.00" style="font-size: 14px;;text-align: right;" readonly>
            <input type="hidden" name="h_tot_debit" id="h_tot_debit" value="<?= $eqv_idr; ?>">

        </div>
    </div>

    <div class="form-row col mt-1">
        <label for="subtotal" class="col-form-label" style="width: 150px; font-size: 13px;;"><b>Total Credit</b></label>
        <div class="col-md-2 mb-3">                              
            <input type="text" class="form-control form-control-sm" name="tot_credit" id="tot_credit" value="<?= number_format($eqv_idr,2); ?>" placeholder="0.00" style="font-size: 14px;;text-align: right;" readonly>
            <input type="hidden" name="h_tot_credit" id="h_tot_credit" value="<?= $eqv_idr; ?>">

        </div>
    </div>

    <div class="form-row col mt-1">
        <div class="form-group">

            <button 
            type="button"
            name="edit_data"
            id="edit_data"
            class="btn btn-success align-self-start"
            style="line-height: 1; padding: 4px 12px; font-size: 0.875rem; border-radius: 6px; height: 32px; margin-left: 10px;">
            <i class="fas fa-save"></i> Save
        </button>

        <button type="button" style="border-radius: 6px" class="btn-danger btn-sm" name="batal" id="batal" onclick="location.href='petty-cashout.php'"><span class="fa fa-angle-double-left"></span> Back</button>
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
                            modal_input_cre();
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

function initializePlugins() {
    $(function () {
        $('.selectpicker').selectpicker();
        $('.tanggal').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $('.tanggal_det').datepicker({
            format: "yyyy-mm-dd",
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
        $('.tanggal_det').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
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
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="refferensi" placeholder="" autocomplete="off"></td>
<td><input style="font-size: 12px;width: 150px;" type="text" class="form-control tanggal_det" name="tgl_refferensi" placeholder="" autocomplete="off"></td>
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
                        <select class="form-control selectpicker cost_ctr" name="cost_ctr" id="cost_ctr" data-live-search="true" data-width="200px" data-size="5">
                        <option value="-"> - </option>
                        </select>
                        </td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="refferensi" placeholder="" autocomplete="off"></td>
                        <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control tanggal_det" name="tgl_refferensi" placeholder="" autocomplete="off"></td>
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
    let payment  = $("#h_tot_payment").val()|| 0;  
    let rate = $("#rate").val().replace(/,/g, '') || 1;

    var table = document.getElementById("tbody2");
    var tota = 0;

    for (var i = 0; i < table.rows.length; i++) {
        var $row = $(table.rows[i]);
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

    let totnya = parseFloat(tota) + parseFloat(payment);

    document.getElementsByName("tot_debit")[0].value = formatMoney(totnya.toFixed(2));
    document.getElementsByName("h_tot_debit")[0].value = totnya.toFixed(2);
}

</script>

<script type="text/javascript">
  function modal_input_cre() { 
    let deb  = $("#eqv_idr").val().replace(/,/g, '') || 0;   
    let rate = $("#rate").val().replace(/,/g, '') || 1;

    var table = document.getElementById("tbody2");
    var tota = 0;

    for (var i = 0; i < table.rows.length; i++) {
        var $row  = $(table.rows[i]);
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

    let tot_deb = parseFloat(tota) + parseFloat(deb);

    document.getElementsByName("tot_credit")[0].value = formatMoney(tot_deb.toFixed(2));
    document.getElementsByName("h_tot_credit")[0].value = tot_deb.toFixed(2);
}

</script>

<script type="text/javascript">
    $(document).on("click", "#edit_data", function () {
        // alert(1);
        let doc_num         = $("#no_pco").val();
        let date            = $("#tgl_pco").val();
        let ref_data        = $("#reff_doc").val();
        let customer        = $("#nama_supp").val();
        let profit_center   = $("#profit_center").val();
        let akun            = $("#accountid_h").val();
        let curr            = $("#valuta").val();
        let bank            = $("#nama_bank").val();
        let amount          = $("#amount").val().replace(/,/g, '');
        let rate            = $("#rate").val().replace(/,/g, '');
        let eqv_idr         = $("#eqv_idr").val().replace(/,/g, '');
        let deskripsi       = $("#pesan").val();
        let h_tot_debit     = $("#h_tot_debit").val();
        let h_tot_credit    = $("#h_tot_credit").val();
        var create_user     = '<?php echo $user; ?>';

        console.log ("doc_num : " + doc_num);
        console.log ("date : " + date);
        console.log ("ref_data : " + ref_data);
        console.log ("customer : " + customer);
        console.log ("profit_center : " + profit_center);
        console.log ("akun : " + akun);
        console.log ("curr : " + curr);
        console.log ("amount : " + amount);
        console.log ("rate : " + rate);
        console.log ("eqv_idr : " + eqv_idr);
        console.log ("deskripsi : " + deskripsi);
        console.log ("h_tot_debit : " + h_tot_debit);
        console.log ("h_tot_credit : " + h_tot_credit);
        var total_nak = 0;
        var total_nag = 0;


        if (h_tot_debit != h_tot_credit) {
            Swal.fire({
                icon: "warning",
                title: "Oops...",
                text: "Some required fields are missing. Please complete all fields before proceeding."
            });
            return;
        }

        let details = [];
        $("#tbody2 tr").each(function () {
            let coa             = $(this).find("select[name='nomor_coa']").val();
            let prof_ctr        = $(this).find("select[name='prof_ctr']").val();
            let cost_ctr        = $(this).find("select[name='cost_ctr']").val() || '-';
            let refferensi      = $(this).find("input[name='refferensi']").val();
            let tgl_refferensi  = $(this).find("input[name='tgl_refferensi']").val();
            let debit           = $(this).find("input[name='txt_amount']").val();
            let credit          = $(this).find("input[name='txt_credit']").val();
            let keterangan      = $(this).find("input[name='keterangan']").val();


            console.log ("det_coa : " + coa);
            console.log ("det_prof_ctr : " + prof_ctr);
            console.log ("det_cost_ctr : " + cost_ctr);
            console.log ("det_debit : " + debit);
            console.log ("det_credit : " + credit);
            console.log ("det_keterangan : " + keterangan);

        // hanya push kalau COA dan nominal terisi
        if (coa && (debit > 0 || credit > 0)) {
            details.push({
                coa: coa,
                prof_ctr: prof_ctr,
                cost_ctr: cost_ctr,
                refferensi: refferensi,
                tgl_refferensi: tgl_refferensi,
                debit: debit,
                credit: credit,
                keterangan: keterangan
            });
        }
    });

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
                    url: "update_pco_lp.php",
                    data: {
                        doc_num: doc_num,
                        date: date,
                        ref_data: ref_data,
                        customer: customer,
                        profit_center: profit_center,
                        akun: akun,
                        curr: curr,
                        amount: amount,
                        rate: rate,
                        eqv_idr: eqv_idr,
                        deskripsi: deskripsi,
                        h_tot_debit: h_tot_debit,
                        h_tot_credit: h_tot_credit,
                        create_user: create_user,
                        total_nak: total_nak,
                        total_nag: total_nag,
                        details: JSON.stringify(details)
                    },
                    success: function (res) {
                        if (res.trim() === "OK") {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: "Data has been successfully updated!"
                            }).then(() => {
                                window.location.href = "petty-cashout.php";
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
        $('.tanggal_det').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
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
        $('.tanggal_det').datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
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
