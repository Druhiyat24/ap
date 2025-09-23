<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$nama_supp  = $_POST['nama_supp'] ?? '';
$start_date = date("Y-m-d",strtotime($_POST['start_date'])) ?? '';
$end_date = date("Y-m-d",strtotime($_POST['end_date'])) ?? '';
$no_kbon = $_POST['no_kbon'] ?? '';
$profit_center = $_POST['profit_center'] ?? '';

$sub = '';
$tax = '';
$total = '';
$persen = '';     

$query_ftr = mysqli_query($conn2,"select no_ftr_cbd, tgl_ftr_cbd, supp, no_po, tgl_po, no_pi, curr, total, no_kbon, tgl_kbon, no_payment, tgl_payment, no_pv, no_bankout, bankout_date, coa from (select no_ftr_cbd, tgl_ftr_cbd, a.supp, a.no_po, a.tgl_po, a.no_pi, a.curr,  a.subtotal, a.tax, a.total, b.no_kbon, b.tgl_kbon, c.no_payment , c.tgl_payment from ftr_cbd a INNER JOIN kontrabon_cbd b on b.no_cbd = a.no_ftr_cbd inner join list_payment_cbd c on c.no_kbon = b.no_kbon where tgl_ftr_cbd >= '2025-07-01' and tgl_ftr_cbd between '$start_date' and '$end_date' and c.status != 'Cancel' and a.supp = '$nama_supp'
    UNION
    select no_ftr_dp, tgl_ftr_dp, a.supp, a.no_po, a.tgl_po, a.no_pi, a.curr,  a.dp_value, 0 tax, a.dp_value total, b.no_kbon, b.tgl_kbon, c.no_payment , c.tgl_payment from ftr_dp a INNER JOIN kontrabon_dp b on b.no_dp = a.no_ftr_dp inner join list_payment_dp c on c.no_kbon = b.no_kbon where tgl_ftr_dp >= '2025-07-01' and tgl_ftr_dp between '$start_date' and '$end_date' and c.status != 'Cancel' and a.supp = '$nama_supp') a
    INNER JOIN
    (select a.no_pv, coa, reff_doc, amount from tbl_pv a INNER JOIN tbl_pv_h b on b.no_pv = a.no_pv where (reff_doc like '%CBD%' OR reff_doc like '%DP%') and b.status != 'Cancel' GROUP BY reff_doc) b on b.reff_doc = a.no_payment
    INNER JOIN
    (select b.no_bankout, b.bankout_date, a.no_reff from b_bankout_det a INNER JOIN b_bankout_h b on b.no_bankout = a.no_bankout where a.no_reff like '%PV/%' and b.status != 'Cancel' GROUP BY a.no_reff) c on c.no_reff = b.no_pv order by tgl_ftr_cbd asc");


while($row_ftr = mysqli_fetch_array($query_ftr)){
    echo '<tr>
    <td style="width:10px;"><input type="checkbox" class="select3_edit"  name="select3_edit[]" value="" <?php if(in_array("1",$_POST[select])) echo "checked=checked";?></td>                       
    <td style="width:50px;" no-ftr="'.$row_ftr['no_ftr_cbd'].'">'.$row_ftr['no_ftr_cbd'].'</td>
    <td style="width:50px;" po-ftr="'.$row_ftr['no_po'].'">'.$row_ftr['no_po'].'</td>
    <td style="width:100px;" tglpo-ftr="'.$row_ftr['tgl_po'].'">'.date("d-M-Y",strtotime($row_ftr['tgl_po'])).'</td>                            
    <td style="width:50px;" pi-ftr="'.$row_ftr['no_pi'].'">'.$row_ftr['no_pi'].'</td>                            
    <td style="width:100px;text-align:right;" total-ftr="'.round($row_ftr['total'],2).'">'.number_format($row_ftr['total'],2).'</td>
    <td style="width:100px;">
    <input style="text-align: right;" type="number" min="0" style="font-size: 13px;;" class="form-control form-control-sm" id="amount_ftr_edit" name="amount_ftr_edit" value="'.round($row_ftr['total'],2).'" disabled>
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
    </tr>';
}
?>
