<?php
include "../../conn/conn.php";
$no_mj = $_POST['no_journal'];

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Reference Date memang boleh kosong di database (NULL / '' / '0000-00-00').
// strtotime('0000-00-00') di PHP menghasilkan tanggal aneh ("-0001-11-30",
// akibat day/month 0 di-rollback), jadi harus dicek eksplisit sebelum
// diformat, bukan cuma cek '' dan '1970-01-01'.
function safeDate($val, $format = 'd-m-Y'){
    if (empty($val) || $val === '0000-00-00' || $val === '1970-01-01') {
        return '';
    }
    $ts = strtotime($val);
    return $ts !== false ? date($format, $ts) : '';
}

$sql = mysqli_query($conn1,"
SELECT 
    a.no_coa, 
    CONCAT(c.no_coa,' - ',c.nama_coa) AS coa,
    a.no_costcenter, 
    d.cc_name,
    a.reff_doc AS no_reff, 
    a.reff_date,
    a.buyer, 
    a.no_ws, 
    a.curr, 
    a.rate,
    a.debit, 
    a.credit,
    a.keterangan AS remark,
    a.profit_center,
    CONCAT(e.id_pc,' - ',e.nama_pc) AS nama_pc
FROM tbl_list_journal a
LEFT JOIN mastercoa_v2 c ON c.no_coa = a.no_coa
LEFT JOIN b_master_cc d ON d.no_cc = a.no_costcenter
LEFT JOIN master_pc e ON e.kode_pc = a.profit_center
WHERE a.no_journal = '$no_mj'
ORDER BY a.id ASC
");

$rows = [];
while ($r = mysqli_fetch_assoc($sql)) $rows[] = $r;
?>

<div class="form-row">
            <div class="col-md-3 mb-3">            
            <label for="pajak" class="col-form-label" style="width: 150px;"><b>No Journal</b></label>
            <?php
            echo'<input type="text" readonly style="font-size: 14px;" class="form-control-plaintext" id="no_doc" name="no_doc" value="'.$no_mj.'">'
            ?>
        </div>

            <div class="col-md-2 mb-3">            
            <label for="total" class="col-form-label" style="width: 150px;"><b>Date</b></label>
                <input type="text" style="font-size: 15px;" name="tgl_doc" id="tgl_doc" class="form-control tanggal" 
            value="<?php 
            $sql = mysqli_query($conn2,"select tgl_journal from tbl_list_journal where no_journal = '$no_mj'");
            $row = mysqli_fetch_array($sql);                         
            if(!empty($no_mj)) {
                echo date("d-m-Y",strtotime($row['tgl_journal']));
            }
            else{
                echo date("d-m-Y");
            }  ?>" autocomplete='off'>
            </div>

            
            <div class="col-md-3 mb-3" style="padding-top: 8px;">
            <label for="nama_supp"><b>Type</b></label>            
              <select class="form-control select2bs4" name="nama_type" id="nama_type" data-dropup-auto="false" data-live-search="true">
                <?php 
            $sql = mysqli_query($conn2,"select type_journal from tbl_list_journal where no_journal = '$no_mj' limit 1");
            $row = mysqli_fetch_array($sql);  
            $id_cmj = $row['type_journal'];
            $nama_cmj = strtoupper($row['type_journal']);  
            $isSelected = ' selected="selected"';                      
            if(!empty($no_mj)) {
                echo '<option value="'.$id_cmj.'"'.$isSelected.'">'. $nama_cmj .'</option>'; 
            }
            else{
                echo '<option value="-">-</option>'; 
            }  ?>
                </select>

            </div>
            </div>

<div class="table-scroll">

<table id="tableJournal" 
class="table table-striped table-bordered table-hover table-sm">

<thead>
<tr>
<th>-</th>
<th>COA</th>
<th>Profit Center</th>
<th>Cost Center</th>
<th>Reference</th>
<th>Ref Date</th>
<th>Buyer</th>
<th>Worksheet</th>
<th>Curr</th>
<th>Rate</th>
<th>Debit</th>
<th>Credit</th>
<th>Debit IDR</th>
<th>Credit IDR</th>
<th>Remark</th>
<th>Action</th>
</tr>
</thead>

<tbody id="tbody2">

<?php foreach ($rows as $r): ?>

<?php
$date = safeDate($r['reff_date']);
?>

<tr>

<td>
<input type="checkbox" class="checkrow" checked disabled>
<input type="hidden" name="checkrow[]" value="1">
</td>


<td>
<select class="form-control sel-coa" name="nomor_coa[]" data-selected="<?= h($r['no_coa']) ?>">
<option value="<?= h($r['no_coa']) ?>"><?= (empty($r['coa']) || $r['coa'] == '-') ? 'Nothing Selected' : h($r['coa']); ?>
</option>
</select>
</td>

<td>
<select class="form-control sel-pc" name="profit_center[]" data-selected="<?= h($r['profit_center']) ?>" onchange="updateRow(this)">
<option value="<?= h($r['profit_center']) ?>"><?= (empty($r['nama_pc']) || $r['nama_pc'] == '-') ? 'Nothing Selected' : h($r['nama_pc']); ?>
</option>
</select>
</td>

<td>
<select class="form-control sel-cc" name="nomor_cc[]" data-selected="<?= h($r['no_costcenter']) ?>">
<option value="<?= h($r['no_costcenter']) ?>"><?= (empty($r['cc_name']) || $r['cc_name'] == '-') ? 'Nothing Selected' : h($r['cc_name']); ?>
</option>
</select>
</td>

<td>
<input type="text" name="ref_no[]" class="form-control" value="<?= h($r['no_reff']) ?>">
</td>

<td>
<input type="text" name="tgl_active[]" class="form-control tanggal" value="<?= h($date) ?>">
</td>

<td>
<select class="form-control sel-buyer" name="buyer[]" data-selected="<?= h($r['buyer']) ?>">
<option value="<?= h($r['buyer']) ?>"><?= (empty($r['buyer']) || $r['buyer'] == '-') ? 'Nothing Selected' : h($r['buyer']); ?>
</option>
</select>
</td>

<td>
<select class="form-control sel-ws" name="no_ws[]" data-selected="<?= h($r['no_ws']) ?>">
<option value="<?= h($r['no_ws']) ?>"><?= (empty($r['no_ws']) || $r['no_ws'] == '-') ? 'Nothing Selected' : h($r['no_ws']); ?>
</option>
</select>
</td>

<td>
<select class="form-control sel-curr" name="currenc[]" onchange="updateRow(this)">
<option value="<?= h($r['curr']) ?>"><?= (empty($r['curr']) || $r['curr'] == '-') ? 'Nothing Selected' : h($r['curr']); ?>
</option>
</select>
</td>

<td>
<input type="number" step="0.0001" min="0" name="rate[]" class="form-control" value="<?= h($r['rate']) ?>" oninput="updateRow(this)">
</td>

<td>
<input type="text" name="debit[]" class="form-control debit" oninput="updateRow(this)"
value="<?= $r['debit']=="0"?"":h($r['debit']) ?>">
</td>

<td>
<input type="text" name="credit[]" class="form-control credit" oninput="updateRow(this)"
value="<?= $r['credit']=="0"?"":h($r['credit']) ?>">
</td>

<td>
<input type="text" name="debit_idr[]" class="form-control debit_idr" readonly
value="<?= ($r['debit']=="0"||$r['debit']=="")?"":h(number_format($r['debit']*$r['rate'],2)) ?>">
</td>

<td>
<input type="text" name="credit_idr[]" class="form-control credit_idr" readonly
value="<?= ($r['credit']=="0"||$r['credit']=="")?"":h(number_format($r['credit']*$r['rate'],2)) ?>">
</td>

<td>
<textarea type="text" name="remark[]" class="form-control ket" ><?= h($r['remark']) ?></textarea>
</td>

<td>
<input type="checkbox" class="remove">
</td>

</tr>

<?php endforeach; ?>

</tbody>

<tfoot>
<tr>
<td colspan="16" class="text-center">

<button type="button" class="btn btn-primary" id="btnAdd">
Add Row
</button>

<button type="button" class="btn btn-warning" id="btnInsert">
Interject Row
</button>

<button type="button" class="btn btn-danger" id="btnDelete">
Delete Row
</button>

</td>
</tr>
</tfoot>

</table>
</div>


<table style="display:none;">
<tr id="templateRow">

<td>
<input type="checkbox" class="checkrow" checked disabled>
<input type="hidden" name="checkrow[]" value="1">
</td>


<td><select class="form-control sel-coa" name="nomor_coa[]">
<option value="">-- Nothing Selected --</option>
</select></td>

<td><select class="form-control sel-pc" name="profit_center[]" onchange="updateRow(this)">
<option value="">-- Nothing Selected --</option>
</select></td>

<td><select class="form-control sel-cc" name="nomor_cc[]">
<option value="">-- Nothing Selected --</option>
</select></td>

<td><input type="text" name="ref_no[]" class="form-control"></td>

<td><input type="text" name="tgl_active[]" class="form-control tanggal"></td>

<td><select class="form-control sel-buyer" name="buyer[]">
<option value="">-- Nothing Selected --</option>
</select></td>

<td><select class="form-control sel-ws" name="no_ws[]">
<option value="">-- Nothing Selected --</option>
</select></td>

<td>
<select class="form-control sel-curr" name="currenc[]" onchange="updateRow(this)">
<option value="">-- Nothing Selected --</option>
</select>
</td>

<td>
<input type="number" step="0.0001" min="0" name="rate[]" class="form-control" value="1" oninput="updateRow(this)">
</td>

<td><input type="number" name="debit[]" class="form-control debit" oninput="updateRow(this)"></td>

<td><input type="number" name="credit[]" class="form-control credit" oninput="updateRow(this)"></td>

<td><input type="text" name="debit_idr[]" class="form-control debit_idr" readonly></td>

<td><input type="text" name="credit_idr[]" class="form-control credit_idr" readonly></td>

<td><textarea type="text" name="remark[]" class="form-control ket" ></textarea></td>

<td><input type="checkbox" class="remove"></td>

</tr>
</table>

<div class="row mt-3">

    <!-- BOX 1 -->
    <div class="col-md-4">
        <div class="total-box tone-nag">
    <div class="total-box-header"><i class="fa fa-building"></i> PT. Nirwana Alabare Garment</div>
    <div class="total-box-body">

    <div class="total-stat is-debit">
        <span class="total-stat-label">Debit</span>
        <div class="total-stat-value-wrap">
            <input type="text" class="total-stat-value" id="txt_debit_nag"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit),2) as debit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit'] : '';
            ?>" readonly>
            <div class="total-stat-sub">IDR <input type="text" class="total-stat-value-sm" id="txt_debit_nag_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit_idr),2) as debit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit_idr'] : '';
            ?>" readonly></div>
        </div>
    </div>

    <div class="total-stat is-credit">
        <span class="total-stat-label">Credit</span>
        <div class="total-stat-value-wrap">
            <input type="text" class="total-stat-value" id="txt_credit_nag"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit),2) as credit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit'] : '';
            ?>" readonly>
            <div class="total-stat-sub">IDR <input type="text" class="total-stat-value-sm" id="txt_credit_nag_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit_idr),2) as credit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit_idr'] : '';
            ?>" readonly></div>
        </div>
    </div>

</div>
</div>

    </div>


    <!-- BOX 2 -->
    <div class="col-md-4">
        <div class="total-box tone-nak">
    <div class="total-box-header"><i class="fa fa-industry"></i> PT. Nirwana Alabare Knitting</div>
    <div class="total-box-body">

    <div class="total-stat is-debit">
        <span class="total-stat-label">Debit</span>
        <div class="total-stat-value-wrap">
            <input type="text" class="total-stat-value" id="txt_debit_nak"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit),2) as debit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit'] : '';
            ?>" readonly>
            <div class="total-stat-sub">IDR <input type="text" class="total-stat-value-sm" id="txt_debit_nak_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit_idr),2) as debit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit_idr'] : '';
            ?>" readonly></div>
        </div>
    </div>

    <div class="total-stat is-credit">
        <span class="total-stat-label">Credit</span>
        <div class="total-stat-value-wrap">
            <input type="text" class="total-stat-value" id="txt_credit_nak"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit),2) as credit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit'] : '';
            ?>" readonly>
            <div class="total-stat-sub">IDR <input type="text" class="total-stat-value-sm" id="txt_credit_nak_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit_idr),2) as credit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit_idr'] : '';
            ?>" readonly></div>
        </div>
    </div>

</div>
</div>
    </div>


    <!-- BOX 3 -->
    <div class="col-md-4">
        <div class="total-box tone-all">
    <div class="total-box-header"><i class="fa fa-calculator"></i> Grand Total</div>
    <div class="total-box-body">

    <div class="total-stat is-debit">
        <span class="total-stat-label">Debit</span>
        <div class="total-stat-value-wrap">
            <input type="text" class="total-stat-value" id="txt_debit_all"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit),2) as debit from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit'] : '';
            ?>" readonly>
            <div class="total-stat-sub">IDR <input type="text" class="total-stat-value-sm" id="txt_debit_all_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit_idr),2) as debit_idr from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit_idr'] : '';
            ?>" readonly></div>
        </div>
    </div>

    <div class="total-stat is-credit">
        <span class="total-stat-label">Credit</span>
        <div class="total-stat-value-wrap">
            <input type="text" class="total-stat-value" id="txt_credit_all"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit),2) as credit from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit'] : '';
            ?>" readonly>
            <div class="total-stat-sub">IDR <input type="text" class="total-stat-value-sm" id="txt_credit_all_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit_idr),2) as credit_idr from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit_idr'] : '';
            ?>" readonly></div>
        </div>
    </div>

</div>
</div>

</div>

