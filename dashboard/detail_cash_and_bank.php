<?php
include '../conn/conn.php';

$total = 0;
$filter = isset($_POST['filter']) ? $_POST['filter'] : null;

function acc_format($angka){
    if ($angka < 0) {
        return '<span style="color:red;">(Rp. '.number_format(abs($angka),2).')</span>';
    } else {
        return 'Rp. '.number_format($angka,2);
    }
}

// Whitelist bulan - sama seperti detail_cash_in_bank.php / detail_cash_on_hand.php.
$monthMap = [
    'saldo_jan' => 1, 'saldo_feb' => 2, 'saldo_mar' => 3, 'saldo_apr' => 4,
    'saldo_may' => 5, 'saldo_jun' => 6, 'saldo_jul' => 7, 'saldo_aug' => 8,
    'saldo_sep' => 9, 'saldo_oct' => 10, 'saldo_nov' => 11, 'saldo_dec' => 12,
];
$bulanKe = $monthMap[$filter] ?? null;

if ($bulanKe === null) {
    echo '';
    exit;
}

// Gabungan CASH IN BANK + CASH ON HAND per akun untuk bulan yang dipilih -
// union dari b_masterbank (bank, rate PAJAK di tanggal transaksi terakhir)
// dan mastercoa_v2 kategori KAS (kas, selalu IDR). Dulu file ini masih baca
// snapshot lama b_trial_balance_$tahun, makanya angkanya beda dari chart-nya.
$sql = mysqli_query($conn1, "
    SELECT nama_coa, total FROM (
        SELECT CONCAT(m.sob, ' ', m.bank_account) AS nama_coa,
               ROUND(
                   (COALESCE(s.amount,0) + COALESCE(SUM(
                       CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                            THEN r.debit - r.credit ELSE 0 END
                   ),0))
                   * IF(m.curr='IDR',1,
                       (SELECT mr.rate FROM masterrate mr
                        WHERE mr.curr=m.curr AND mr.v_codecurr='PAJAK'
                          AND mr.tanggal <= COALESCE(
                              (SELECT MAX(r2.transaksi_date) FROM b_reportbank r2
                               WHERE r2.akun = m.bank_account AND r2.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r2.status != 'Cancel' AND r2.no_doc NOT LIKE 'FX/%'),
                              LEAST(mo.month_end, CURDATE())
                          )
                        ORDER BY mr.tanggal DESC LIMIT 1)
                   )
               ,2) AS total
        FROM b_masterbank m
        CROSS JOIN (SELECT LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL ($bulanKe-1) MONTH) AS month_end) mo
        LEFT JOIN b_saldoawal_bank s ON s.account = m.bank_account
        LEFT JOIN b_reportbank r ON r.akun = m.bank_account AND r.no_doc NOT LIKE 'FX/%'
        GROUP BY m.bank_account, m.sob, m.curr, mo.month_end, s.amount

        UNION ALL

        SELECT c.nama_coa,
               ROUND(
                   (COALESCE(s.amount,0) + COALESCE(SUM(
                       CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                            THEN r.debit - r.credit ELSE 0 END
                   ),0))
                   * IF(s.curr IS NULL OR s.curr='IDR',1,
                       (SELECT mr.rate FROM masterrate mr
                        WHERE mr.curr=s.curr AND mr.v_codecurr='HARIAN' AND mr.tanggal <= LEAST(mo.month_end, CURDATE())
                        ORDER BY mr.tanggal DESC LIMIT 1)
                   )
               ,2) AS total
        FROM mastercoa_v2 c
        CROSS JOIN (SELECT LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL ($bulanKe-1) MONTH) AS month_end) mo
        LEFT JOIN b_saldoawal_pettycash s ON s.account = c.no_coa
        LEFT JOIN c_report_pettycash r ON r.akun = c.no_coa
        WHERE c.ind_categori5 = 'KAS'
        GROUP BY c.no_coa, c.nama_coa, mo.month_end, s.amount, s.curr
    ) u
    WHERE total > 0
    ORDER BY nama_coa ASC
");

$table = '
<table id="mytdmodal" class="table table-striped table-bordered" width="100%" style="font-size:12px;">
<thead style="background-color:#1E90FF; color:white;">
<tr>
    <th width="5%">#</th>
    <th style="text-align:left;">Nama COA</th>
    <th style="text-align:right;">Amount</th>
</tr>
</thead>
<tbody>
';

while ($row = mysqli_fetch_assoc($sql)) {

    $total += $row['total'];

    $table .= '
    <tr>
        <td><i class="fa fa-dot-circle-o"></i></td>
        <td style="text-align:left;">'.$row['nama_coa'].'</td>
        <td style="text-align:right;">'.acc_format($row['total']).'</td>
    </tr>';
}

$table .= '
<tr style="background:#f2f2f2; font-weight:bold;">
    <td colspan="2" style="text-align:center">TOTAL</td>
    <td style="text-align:right;">'.acc_format($total).'</td>
</tr>
</tbody>
</table>
';

echo $table;
?>
