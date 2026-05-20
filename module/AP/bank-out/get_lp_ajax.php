<?php
include '../../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$tgl_awal  = date('Y-m-d', strtotime($_POST['tgl_awal']));
$tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));
$supplier  = $_POST['supplier'];

$sql = mysqli_query($conn2,"WITH
    total_lp as (       select a.profit_center, CONCAT(id_pc,' - ',nama_pc) nama_pc, a.nama_supp,a.no_payment,a.tgl_payment,max(a.tgl_tempo) as due_date,a.curr, (SUM(a.amount) - SUM(c.tax) + SUM(a.pph_value)) subtotal, SUM(c.tax) ppn, SUM(a.pph_value) pph, ((SUM(a.amount) - SUM(c.tax)+ SUM(a.pph_value)) + SUM(c.tax) - SUM(a.pph_value)) total, a.status from list_payment a INNER JOIN master_pc b on b.kode_pc = a.profit_center INNER JOIN kontrabon_h c on c.no_kbon = a.no_kbon where a.nama_supp = '$supplier' and a.tgl_payment BETWEEN '$tgl_awal' and '$tgl_akhir' and a.status = 'Closed'  group by a.no_payment, a.profit_center
),
    
    total_bk as (select a.profit_center, no_reff, sum(dpp) dpp_lp, sum(ppn) ppn_lp, sum(pph) pph_lp, sum(total) total_lp from b_bankout_det a inner join b_bankout_h b on b.no_bankout = a.no_bankout where b.status != 'Cancel' and no_reff like '%LP%' GROUP BY no_reff,a.profit_center)
    
    select a.profit_center, a.nama_pc, a.nama_supp, a.no_payment, a.tgl_payment,a.due_date, a.curr, (a.subtotal - COALESCE(b.dpp_lp,0)) subtotal, (a.ppn - COALESCE(b.ppn_lp,0)) ppn, (a.pph - COALESCE(b.pph_lp,0)) pph ,(a.total - COALESCE(b.total_lp,0)) total, a.status, IFNULL(c.rate,1) rate from total_lp a LEFT JOIN total_bk b on b.no_reff = a.no_payment and b.profit_center = a.profit_center LEFT JOIN (SELECT tanggal, curr, rate FROM ap_masterrate where v_codecurr = 'PAJAK' GROUP BY tanggal, curr) c on c.curr = a.curr and c.tanggal = a.tgl_payment where (a.total - COALESCE(b.total_lp,0)) > 0
    ");

while($row = mysqli_fetch_assoc($sql)){

    $total_idr = $row['total']; // sesuaikan kalau ada rate

    echo '
    <tr>
        <td>
            <input type="checkbox" class="chk_lp">
        </td>
        <td class="pc_lp" data-pclp="'.$row['profit_center'].'">'.$row['nama_pc'].'</td>
        <td class="no_lp" data-nolp="'.$row['no_payment'].'">'.$row['no_payment'].'</td>
        <td>'.date("d-M-Y",strtotime($row['tgl_payment'])).'</td>
        <td>'.date("d-M-Y",strtotime($row['due_date'])).'</td>
        <td>'.number_format($row['subtotal'],2).'</td>
        <td>'.number_format($row['ppn'],2).'</td>
        <td>'.number_format($row['pph'],2).'</td>
        <td class="total_lp" data-total="'.$row['total'].'">'.number_format($row['total'],2).'</td>
        <td class="rate_lp" data-ratelp="'.$row['rate'].'">'.number_format($row['rate'],2).'</td>
        <td style="width: 170px;">
            <input type="text" class="form-control txt_amount_lp" style="text-align:right" disabled>
        </td>
        <td style="width: 170px;">
            <input type="text" class="form-control txt_amount_lp_idr" style="text-align:right" disabled>
        </td>
    </tr>';
}
