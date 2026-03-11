<?php
include "../../conn/conn.php";
$no_mj = $_POST['no_journal'];

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

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
<th>Remark</th>
<th>Action</th>
</tr>
</thead>

<tbody id="tbody2">

<?php foreach ($rows as $r): ?>

<?php
$date = ($r['reff_date']=='' || $r['reff_date']=='1970-01-01') ? '' : date('d-m-Y', strtotime($r['reff_date']));
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
<td colspan="13" class="text-center">

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

<td><textarea type="text" name="remark[]" class="form-control ket" ></textarea></td>

<td><input type="checkbox" class="remove"></td>

</tr>
</table>

<div class="row mt-3">

    <!-- BOX 1 -->
    <div class="col-md-4">
        <div class="total-box">
    <h6>Total PT. Nirwana Alabare Garment</h6>
    <hr>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Credit</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_credit_nag"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit),2) as credit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Debit</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_debit_nag"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit),2) as debit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Credit IDR</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_credit_nag_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit_idr),2) as credit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit_idr'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Debit IDR</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_debit_nag_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit_idr),2) as debit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAG'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit_idr'] : '';
            ?>" readonly>
        </div>
    </div>

</div>

    </div>


    <!-- BOX 2 -->
    <div class="col-md-4">
        <div class="total-box">
    <h6>Total PT. Nirwana Alabare Knitting</h6>
    <hr>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Credit</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_credit_nak"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit),2) as credit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Debit</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_debit_nak"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit),2) as debit from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Credit IDR</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_credit_nak_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit_idr),2) as credit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit_idr'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Debit IDR</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_debit_nak_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit_idr),2) as debit_idr from tbl_list_journal where no_journal='$no_mj' and profit_center = 'NAK'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit_idr'] : '';
            ?>" readonly>
        </div>
    </div>

</div>
    </div>


    <!-- BOX 3 -->
    <div class="col-md-4">
        <div class="total-box">
    <h6>Grand Total</h6>
    <hr>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Credit</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_credit_all"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit),2) as credit from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Debit</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_debit_all"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit),2) as debit from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Credit IDR</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_credit_all_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(credit_idr),2) as credit_idr from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['credit_idr'] : '';
            ?>" readonly>
        </div>
    </div>

    <div class="row mb-2 align-items-center">
        <div class="col-4">
            <label class="mb-0">Total Debit IDR</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control text-right" id="txt_debit_all_idr"
            value="<?php
                $sqldes = mysqli_query($conn2,"select format(sum(debit_idr),2) as debit_idr from tbl_list_journal where no_journal='$no_mj'");
                $row = mysqli_fetch_array($sqldes);
                echo !empty($no_mj) ? $row['debit_idr'] : '';
            ?>" readonly>
        </div>
    </div>

</div>

</div>

