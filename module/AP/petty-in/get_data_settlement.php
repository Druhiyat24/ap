<?php
include '../../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pco = $_POST['no_pco'];

$sql = mysqli_query($conn1,"select a.no_coa, a.profit_center, CONCAT(mp.id_pc,' - ',nama_pc) nama_pc, CONCAT(a.no_coa,' ',nama_coa) coa, no_costcntr, cc_name, buyer,no_ws,curr,IF(debit > 0,round(debit,0),'') credit, IF(credit > 0,round(credit,0),'') debit, curr, upper(deskripsi) deskripsi from c_petty_cashout_none a inner join mastercoa_v2 b on b.no_coa = a.no_coa LEFT JOIN b_master_cc c on c.no_cc = a.no_costcntr LEFT JOIN master_pc mp on mp.kode_pc = a.profit_center where no_pco = '$no_pco' GROUP BY a.id");

$table = '';

while ($row = mysqli_fetch_assoc($sql)) {

    $table .= '<tr>       
    <td id="select"><input type="checkbox" id="select2" name="select2[]" value="" checked disabled></td>
    <td style="width: 50px"><select class="form-control selectpicker no_coa2" name="nomor_coa2[]" data-live-search="true" data-width="220px" data-size="5"> <option value="'.$row['no_coa'].'" >'.$row['coa'].'</option></select></td>
    <td ><select class="form-control selectpicker prof_ctr2" name="prof_ctr2[]" data-live-search="true" data-width="200px" data-size="5"> <option value="'.$row['profit_center'].'" >'.$row['nama_pc'].'</option></select></td>
    <td ><select class="form-control selectpicker cost_ctr2" name="cost_ctr2[]" data-live-search="true" data-width="200px" data-size="5"> <option value="'.$row['no_costcntr'].'" >'.$row['cc_name'].'</option></select></td>
    <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer2[]" placeholder="" autocomplete="off" value="'.$row['buyer'].'"></td>
    <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws2[]" placeholder="" autocomplete="off" value="'.$row['no_ws'].'"></td>
    <td><select class="form-control selectpicker curr_det2" name="currenc2[]" id="currenc" data-live-search="true"><option value="'.$row['curr'].'">'.$row['curr'].'</option></td></select>
    <td><input style="text-align:right;width:100%" type="number" min="1" style="font-size: 12px;width: 150px;" class="form-control" id="txt_amount" name="txt_amount2[]"  oninput="modal_input_amt2(value)" autocomplete = "off" value="'.$row['debit'].'"></td>
    <td><input style="text-align:right;width:100%" type="number" min="1" style="font-size: 12px;width: 150px;" class="form-control" id="txt_credit" name="txt_credit2[]" oninput="modal_input_cre2(value)" autocomplete = "off" value="'.$row['credit'].'"></td>
    <td><input style="font-size: 12px;width: 150px;" type="text" class="form-control" name="keterangan2[]" placeholder="" autocomplete="off" value="'.$row['deskripsi'].'"></td>
    <td><input name="chk_a2[]" type="checkbox" class="checkall_a2" value="" disabled></td>
    </tr>';
}




echo $table;

mysqli_close($conn2);
?>