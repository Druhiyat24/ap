<?php
include '../../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$no_co = mysqli_real_escape_string($conn1, $_POST['no_co']);

$sql = mysqli_query($conn1, "select a.no_coa, IF(a.no_coa = '1.01.11','NAK','NAG') profit_center, IF(a.no_coa = '1.01.11','PCP002 - NIRWANA ALABARE KNITTING','PCP001 - NIRWANA ALABARE GARMENT') nama_pc, if(a.no_coa = '-','-',CONCAT(b.no_coa,' ',b.nama_coa)) as coa, a.no_costcenter, if(a.no_costcenter = '-' or a.no_costcenter is null,'-',c.cc_name) as cc_name, a.buyer, a.ws, round(sum(a.amount),0) as amount, if(a.deskrip = '' or a.deskrip is null,'-',a.deskrip) as deskripsi from c_cash_out a left join mastercoa_v2 b on b.no_coa = a.no_coa left join b_master_cc c on c.no_cc = a.no_costcenter where a.no_co = '$no_co' group by a.no_coa");

$table = '';

while ($row = mysqli_fetch_assoc($sql)) {

    $table .= '<tr>
    <td id="select"><input type="checkbox" id="select1" name="select1[]" value="" checked disabled></td>
    <td><select class="form-control selectpicker no_coa1" name="nomor_coa1[]" data-live-search="true" data-width="100%" data-size="5"> <option value="'.$row['no_coa'].'" >'.$row['coa'].'</option></select></td>
    <td><select class="form-control selectpicker prof_ctr1" name="prof_ctr1[]" data-live-search="true" data-width="100%" data-size="5"> <option value="'.$row['profit_center'].'" >'.$row['nama_pc'].'</option></select></td>
    <td><select class="form-control selectpicker cost_ctr1" name="cost_ctr1[]" data-live-search="true" data-width="100%" data-size="5"> <option value="'.$row['no_costcenter'].'" >'.$row['cc_name'].'</option></select></td>
    <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="buyer1[]" placeholder="" autocomplete="off" value="'.$row['buyer'].'"></td>
    <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="no_ws1[]" placeholder="" autocomplete="off" value="'.$row['ws'].'"></td>
    <td><select class="form-control selectpicker curr_det1" name="currenc1[]" data-live-search="true"><option value="IDR">IDR</option></select></td>
    <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_amount1[]" oninput="modal_input_amt1(this)" autocomplete="off" value="0" readonly></td>
    <td><input style="text-align:right;width:100%" type="number" min="1" class="form-control" name="txt_credit1[]" oninput="modal_input_cre1(this)" autocomplete="off" value="'.$row['amount'].'"></td>
    <td><input style="font-size:12px;width:100%" type="text" class="form-control" name="keterangan1[]" placeholder="" autocomplete="off" value="'.$row['deskripsi'].'"></td>
    <td><input name="chk_a1[]" type="checkbox" class="checkall_a1" value="" disabled></td>
    </tr>';
}

echo $table;

mysqli_close($conn1);
?>
