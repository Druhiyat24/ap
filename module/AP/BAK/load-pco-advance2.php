<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_pco = $_POST['no_pco'];

$sql = mysqli_query($conn1,"select a.no_coa, CONCAT(a.no_coa,' ',nama_coa) coa, no_costcntr, cc_name, buyer,no_ws,curr,IF(debit > 0,round(debit,0),'') debit, IF(credit > 0,round(credit,0),'') credit, curr, upper(deskripsi) deskripsi from c_petty_cashout_none a inner join mastercoa_v2 b on b.no_coa = a.no_coa INNER JOIN b_master_cc c on c.no_cc = a.no_costcntr where no_pco = '$no_pco' GROUP BY a.id");

$table = '';

while ($row = mysqli_fetch_assoc($sql)) {

    $table .= '<tr>       
    <td id="select"><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
    <td style="width: 50px"><select class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-live-search="true" data-width="220px" data-size="5"> <option value="'.$row['no_coa'].'" >'.$row['coa'].'</option></select></td>
    <td ><select class="form-control selectpicker" name="cost_ctr" id="cost_ctr" data-live-search="true" data-width="200px" data-size="5"> <option value="'.$row['no_costcntr'].'" >'.$row['cc_name'].'</option></select></td>
    <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" value="'.$row['buyer'].'"></td>
    <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" value="'.$row['no_ws'].'"></td>
    <td><select class="form-control selectpicker" name="currenc" id="currenc" data-live-search="true"><option value="'.$row['curr'].'">'.$row['curr'].'</option></td></select>
    <td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off" value="'.$row['debit'].'"></td>
    <td><input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_credit" name="txt_credit" oninput="modal_input_cre(value)" autocomplete = "off" value="'.$row['credit'].'"></td>
    <td><input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete="off" value="'.$row['deskripsi'].'"></td>
    <td><input name="chk_a[]" type="checkbox" class="checkall_a" value="" disabled></td>
    </tr>';
}




echo $table;

mysqli_close($conn2);
?>