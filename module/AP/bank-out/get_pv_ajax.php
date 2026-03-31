<?php
include '../../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$tgl_awal  = date('Y-m-d', strtotime($_POST['tgl_awal']));
$tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
$supplier  = $_POST['supplier'];

$sql = mysqli_query($conn2,"WITH
    total_pv as (select b.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_pv,a.pv_date,max(b.due_date) as due_date,a.curr, SUM(b.amount - ded_add) subtotal, SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) ppn, SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100)) pph, (SUM(b.amount - ded_add) + SUM((b.amount * b.ppn/100) - (b.ded_add * b.ppn/100)) - SUM((b.amount * b.pph/100) - (b.ded_add * b.pph/100))) total, a.status, a.frm_akun, if(a.frm_akun = '-','-',c.bank_name) as bank_name, c.b_code from tbl_pv_h a inner join tbl_pv b on b.no_pv = a.no_pv left join b_masterbank c on c.bank_account = a.frm_akun INNER JOIN master_pc d on d.kode_pc = b.profit_center where a.nama_supp = '$supplier' and a.pv_date BETWEEN '$tgl_awal' and '$tgl_akhir' and a.status = 'Approved' and a.outstanding != '0' group by a.no_pv, b.profit_center),
    
    total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_pv, sum(ppn) ppn_pv, sum(pph) pph_pv, sum(total) total_pv from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and no_reff like '%PV%' GROUP BY no_reff,a.profit_center)
    
    select a.profit_center, a.nama_pc, a.nama_supp, a.no_pv, a.pv_date,a.due_date, a.curr, (a.subtotal - COALESCE(b.dpp_pv,0)) subtotal, (a.ppn - COALESCE(b.ppn_pv,0)) ppn, (a.pph - COALESCE(b.pph_pv,0)) pph ,(a.total - COALESCE(b.total_pv,0)) total, a.status, a.frm_akun, a.bank_name, a.b_code from total_pv a LEFT JOIN total_bk b on b.no_reff = a.no_pv and b.profit_center = a.profit_center where (a.total - COALESCE(b.total_pv,0)) > 0
");

while($row = mysqli_fetch_assoc($sql)){

    $total_idr = $row['total']; // sesuaikan kalau ada rate

    echo '
    <tr>
        <td>
            <input type="checkbox" class="chk_pv">
        </td>
        <td class="pc_pv" data-pcpv="'.$row['profit_center'].'">'.$row['nama_pc'].'</td>
        <td class="no_pv" data-nopv="'.$row['no_pv'].'">'.$row['no_pv'].'</td>
        <td>'.date("d-M-Y",strtotime($row['pv_date'])).'</td>
        <td>'.date("d-M-Y",strtotime($row['due_date'])).'</td>
        <td>'.number_format($row['subtotal'],2).'</td>
        <td>'.number_format($row['ppn'],2).'</td>
        <td>'.number_format($row['pph'],2).'</td>
        <td class="total_pv" data-total="'.$row['total'].'">'.number_format($row['total'],2).'</td>
        <td>
            <input type="text" class="form-control txt_amount_pv" style="text-align:right" disabled>
        </td>
    </tr>';
}
