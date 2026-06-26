<?php
// Dipakai oleh create-paymentvoucher-exim.php (saat render halaman) dan
// get_memo_detail_exim.php (ajax refresh setelah pilih memo tanpa reload).
// Membutuhkan $conn1, $conn2, $user dari scope pemanggil.

$sql_jurnal = mysqli_query($conn2,"select no_journal from tbl_list_journal a inner join tbl_pv_memo_temp b on b.no_memo = a.no_journal where b.user = '$user' limit 1");
$hasil = mysqli_fetch_array($sql_jurnal);
$no_journal = isset($hasil['no_journal']) ?  $hasil['no_journal'] : null;

if ($no_journal != null) {
    $sqlpv = mysql_query("select a.id_h,a.nm_memo,a.tgl_memo,a.jns_trans,IF(a.ditagihkan != 'Y','TIDAK','YA') ditagihkan, '' nm_ctg,'' nm_sub_ctg,'' item_name,lj.no_coa, CONCAT( lj.no_coa, ' ', lj.nama_coa) nama_coa, lj.no_costcenter id_cc,lj.nama_costcenter cc_name,lj.keterangan,lj.credit biaya, lj.profit_center, CONCAT(pc.id_pc,' - ',pc.nama_pc) nama_pc  from memo_h a
  inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
    inner join tbl_list_journal lj on lj.no_journal = a.nm_memo
    left join master_pc pc on pc.kode_pc = a.profit_center
  where lj.credit != 0 and mtemp.user = '$user' and lj.keterangan not like '%DISCOUNT%' order by a.nm_memo asc",$conn1);
}else{
    $sqlpv = mysql_query("select id_h,nm_memo,tgl_memo,jns_trans, ditagihkan,nm_ctg,nm_sub_ctg,item_name,no_coa, nama_coa, id_cc,cc_name, keterangan,sum(biaya ) biaya, profit_center, nama_pc from (select a.id_h,a.nm_memo,a.tgl_memo,a.jns_trans,IF(a.ditagihkan != 'Y','TIDAK','YA') ditagihkan,mdet.nm_ctg,mdet.nm_sub_ctg,it.item_name,map.no_coa, CONCAT( map.no_coa, ' ', map.nama_coa) nama_coa, map.id_cc,map.cc_name, UPPER(CONCAT(mdet.nm_sub_ctg,' (',ms.supplier, '), BUYER ',mb.supplier, ', ',a.jns_trans, ', ',inv_vendor)) keterangan,mdet.biaya, a.profit_center, CONCAT(pc.id_pc,' - ',pc.nama_pc) nama_pc from memo_h a
           inner join mastersupplier ms on a.id_supplier = ms.id_supplier
           inner join mastersupplier mb on a.id_buyer = mb.id_supplier
           inner join memo_det mdet on mdet.id_h = a.id_h
           left join master_memo_item it on it.id = a.id_item
           left join master_pc pc on pc.kode_pc = a.profit_center
           inner join tbl_pv_memo_temp mtemp on mtemp.no_memo = a.nm_memo
           left join memo_mapping_v2 map on map.id_ctg = mdet.id_ctg and map.id_sub_ctg = mdet.id_sub_ctg and
           map.jns_trans = a.jns_trans and map.ditagihkan = a.ditagihkan or map.id_item = a.id_item
           where mdet.cancel = 'N' and map.status != 'Y' and mtemp.user = '$user'
           GROUP BY mdet.id order by mdet.id_h) a GROUP BY nm_memo,nm_ctg,nm_sub_ctg",$conn1);
}

    $id = 1;
 while($row = mysql_fetch_array($sqlpv)){
                $reff_date = isset($row['tgl_memo']) ? $row['tgl_memo'] : '';
                $amount = isset($row['biaya']) ? $row['biaya'] : 0;
                $coa_memo = isset($row['no_coa']) ? $row['no_coa'] : '';
                $cc_memo = isset($row['id_cc']) ? $row['id_cc'] : '';
                $pc_memo = isset($row['profit_center']) ? $row['profit_center'] : '';
                $ded_add = 0;
                if ($reff_date == '' || $reff_date == '1970-01-01') {
                    $reffdate = '';
                }else{
                    $reffdate = date("d-m-Y",strtotime($row['tgl_memo']));
                }

echo'<tr>
        <td><input type="checkbox" id="select" name="select[]" value="" checked disabled></td>
        <td >
            <input style="font-size: 12px;" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['nm_memo'].'" autocomplete="off" >
        </td>
        <td hidden>
            <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['jns_trans'].'" autocomplete="off" >
        </td>
        <td hidden>
            <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['ditagihkan'].'" autocomplete="off" >
        </td>
        <td hidden>
            <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['nm_ctg'].'" autocomplete="off" >
        </td>
        <td hidden>
            <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['nm_sub_ctg'].'" autocomplete="off" >
        </td>
        <td hidden>
            <input style="font-size: 12px" type="text" class="form-control" name="keterangan[]" placeholder="" value="'.$row['item_name'].'" autocomplete="off" >
        </td>
        <td>
            <select style="font-size: 12px;" class="form-control selectpicker" name="nomor_coa" id="nomor_coa" data-width="150px" data-live-search="true" data-size="5"> <option value="'.$row['no_coa'].'" >'.$row['nama_coa'].'</option><option value="-" > - </option>';  $sql = mysqli_query($conn1,"select no_coa as id_coa,concat(no_coa,' ', nama_coa) as coa from mastercoa_v2 where no_coa != '$coa_memo' order by no_coa asc"); foreach ($sql as $cc) : echo'<option value="'.$cc["id_coa"].'"> '.$cc["coa"].' </option>'; endforeach; ?>
            <?php
            echo '
            </select>
        </td>
        <td >
            <select style="font-size: 12px;" class="form-control selectpicker prof_ctr" name="prof_ctr" id="prof_ctr" data-width="150px" data-live-search="true" data-size="5"> <option value="'.$row['profit_center'].'" >'.$row['nama_pc'].'</option>';  $sql3 = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active' and kode_pc != '$pc_memo'"); foreach ($sql3 as $lpc) : echo'<option value="'.$lpc["kode_pc"].'"> '.$lpc["tampil"].' </option>'; endforeach; ?>
            <?php
            echo '
            </select>
        </td>
        <td >
            <select style="font-size: 12px;" class="form-control selectpicker nomor_cc" name="nomor_cc" id="nomor_cc" data-width="150px" data-live-search="true" data-size="5"> <option value="'.$row['id_cc'].'" >'.$row['cc_name'].'</option>';  $sql2 = mysqli_query($conn1,"select no_cc as code_combine,cc_name, CONCAT(no_cc,' ',cc_name) as cost_name from b_master_cc where status = 'Active' and no_cc != '$cc_memo'"); foreach ($sql2 as $ccs) : echo'<option value="'.$ccs["code_combine"].'"> '.$ccs["cost_name"].' </option>'; endforeach; ?>
            <?php
            echo '
            </select>
        </td>
        <td>
            <textarea style="font-size: 12px" type="text" class="form-control" name="keterangan[]" value="'.$row['keterangan'].'" placeholder="" autocomplete="off">'.$row['keterangan'].'</textarea>
        </td>';
        if ($amount == '0') {
            echo '<td>
            <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_amt(value)" autocomplete = "off" readonly>
        </td>';
        }else{
        echo '<td>
            <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="'.$row['biaya'].'" class="form-control"  oninput="modal_input_amt(value)" autocomplete = "off">
        </td>';
    }

    if ($ded_add == '0') {
            echo '<td>
            <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_dedadd(value)" autocomplete = "off" readonly>
        </td>';
        }else{
        echo '<td>
            <input  style="text-align: right;font-size: 12px;" type="number" min="1" value="" class="form-control"  oninput="modal_input_dedadd(value)" autocomplete = "off">
        </td>';
    }
        echo '
        <td>
            <input  type="text" style="font-size: 12px;" name="tgl_tempo" id="tgl_tempo" value="" class="form-control tanggal"
     autocomplete="off" placeholder="dd-mm-yyyy" >
        </td>
        <td>
            <select style = "font-size: 12px;" class="form-control" name="pphh" id="pphh"  onchange="input_pph()" data-width="120px" data-live-search="true" data-size="5"> <option data-idtax="0" value="0" > Non PPH </option>'; $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPH' GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
            <?php echo'</select>
        </td>

        <td >
            <select style = "font-size: 12px;" class="form-control" name="ppnn" id="ppnn'.$id.'"  onchange="input_ppn()" data-width="120px" data-live-search="true" data-size="5"> <option data-idtax="" value="" > Non PPN </option>'; $sql = mysqli_query($conn1,"select idtax, kriteria, percentage, GROUP_CONCAT(kriteria,' (',percentage,'%)') as kriteria2 from mtax where category_tax = 'PPN' GROUP BY idtax"); foreach ($sql as $cc) : echo'<option data-idtax="'.$cc['idtax'].'" value="'.$cc["percentage"].'"> '.$cc["kriteria2"].' </option>'; endforeach; ?>
            <?php echo'</select>
        </td>

        <td><input name="chk_a[]" type="checkbox" class="checkall_a" value="" disabled></td>
    </tr>';
$id++;
}
?>
<!-- id="pphh'.$id.'" -->
