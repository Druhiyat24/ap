<?php
include '../../conn/conn.php';
?>

<tr>
            <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
            <td >
                <select class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-live-search="true"> <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2"); foreach ($sql as $coa) : echo'<option value="'.$coa["id_coa"].'"> '.$coa["coa"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td >
                <select class="form-control selectpicker" name="nomor_cc" id="nomor_cc" data-live-search="true"> <option value="-" > - </option> <?php $sql = mysqli_query($conn1,"select no_cc as code_combine,cc_name as cost_name from b_master_cc"); foreach ($sql as $cc) : echo'<option value="'.$cc["code_combine"].'"> '.$cc["cost_name"].' </option>'; endforeach; ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'>
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'> 
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'> 
            </td>
            <td>
               <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off'>
            </td>
            <td>
                <select class="form-control " name="currenc" id="currenc" onchange="ubahrate(this.value)" data-live-search="true">
                    <option value="IDR">IDR</option>  
                    <option value="USD">USD</option>                       
                </select> 
            </td>
            <td>
                <input type="text" class="form-control" name="keterangan[]" placeholder="" autocomplete='off' readonly value="1"> 
            </td>
            <td>
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt(value)" autocomplete = "off">
            </td>
            <td>
                <input style="text-align: right;" type="number" min="1" style="font-size: 12px;" class="form-control" id="txt_amount" name="txt_amount"  oninput="modal_input_amt2(value)" autocomplete = "off">
            </td>

            <td><input name="chk_a[]" type="checkbox" class="checkall_a" value=""/></td>
        </tr>
