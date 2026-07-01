<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=AP Report - SUMMARY SUPPLIER.xls");

include '../../../conn/conn.php';

// Load BPB shared query → sets $sql, $start_date, $end_date
include 'query_bpb2.php';
$sql_bpb = $sql;

// Load PV shared query → overwrites $sql (same params from $_REQUEST)
include 'query_pv2.php';
$sql_pv = $sql;

$label_start = date("d F Y", strtotime($start_date));
$label_end   = date("d F Y", strtotime($end_date));

$q_bulan = mysqli_query($conn1,
    "SELECT CONCAT(UPPER(SUBSTR(nama_bulan_singkat,1,1)), LOWER(SUBSTR(nama_bulan_singkat,2)), ' ', tahun) bulan_tahun
     FROM dim_date
     WHERE kode_tanggal BETWEEN
           CONCAT(YEAR('$end_date'), LPAD(MONTH('$end_date'),2,'0'), '01')
       AND CONCAT(
           IF(MONTH('$end_date')+5 > 12, YEAR('$end_date')+1, YEAR('$end_date')),
           LPAD(IF(MONTH('$end_date')+5 > 12, MOD((MONTH('$end_date')+5),12), MONTH('$end_date')+5),2,'0'),
           '01')
     GROUP BY bulan, tahun
     ORDER BY MIN(kode_tanggal) ASC"
);
$proj_months = [];
if ($q_bulan) {
    while ($rb = mysqli_fetch_assoc($q_bulan)) $proj_months[] = $rb['bulan_tahun'];
}
if (empty($proj_months)) {
    $bulan_indo = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $base = $end_date ? strtotime($end_date) : time();
    $m0 = (int)date('m', $base);
    $y0 = (int)date('Y', $base);
    for ($i = 0; $i < 6; $i++) {
        $mi = (($m0 - 1 + $i) % 12) + 1;
        $yi = $y0 + intdiv($m0 - 1 + $i, 12);
        $proj_months[] = $bulan_indo[$mi] . ' ' . $yi;
    }
}

$sql_sum = "
SELECT
    supplier, a.curr,
    ROUND(SUM(saldo_awal),2)        saldo_awal,
    ROUND(SUM(addition),2)          addition,
    ROUND(SUM(reverse),2)           reverse,
    ROUND(SUM(deduction_advance),2) deduction_advance,
    ROUND(SUM(deduction_other),2)   deduction_other,
    ROUND(SUM(ded_bank),2)          ded_bank,
    ROUND(SUM(ded_cash),2)          ded_cash,
    ROUND(SUM(ded_nonbank),2)       ded_nonbank,
    ROUND(SUM(deduction_gm),2)      deduction_gm,
    0                               adjust,
    ROUND(SUM(saldo_akhir),2)       saldo_akhir,
    ROUND(SUM(saldo_akhir * IFNULL(b.rate,1)),2) saldo_akhir_idr,
    IFNULL(b.rate,1) rate,
    ROUND(SUM(due_current),2)       due_current,
    ROUND(SUM(due_1_30),2)          due_1_30,
    ROUND(SUM(due_31_60),2)         due_31_60,
    ROUND(SUM(due_61_90),2)         due_61_90,
    ROUND(SUM(due_91_120),2)        due_91_120,
    ROUND(SUM(due_121_180),2)       due_121_180,
    ROUND(SUM(due_181_360),2)       due_181_360,
    ROUND(SUM(due_gt_360),2)        due_gt_360,
    ROUND(SUM(total_due),2)         total_due,
    ROUND(SUM(pro_due),2)           pro_due,
    ROUND(SUM(pro_due0),2)          pro_due0,
    ROUND(SUM(pro_due1),2)          pro_due1,
    ROUND(SUM(pro_due2),2)          pro_due2,
    ROUND(SUM(pro_due3),2)          pro_due3,
    ROUND(SUM(pro_due4),2)          pro_due4,
    ROUND(SUM(pro_due5),2)          pro_due5,
    ROUND(SUM(tot_produe),2)        tot_produe
FROM (
    -- BPB rows
    SELECT supplier, curr, saldo_awal, in_bpb addition, reverse_bpb reverse, 0 deduction_advance, 0 deduction_other, 0 ded_bank, 0 ded_cash, 0 ded_nonbank, gm deduction_gm, saldo_akhir, rate, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe
    FROM ($sql_bpb) bpb
    UNION ALL
    -- Payment Voucher rows
    SELECT supplier, curr, saldo_awal, 0 addition, 0 reverse, uang_muka deduction_advance, potongan deduction_other, ded_bank ded_bank, ded_cash ded_cash, ded_nonbank ded_nonbank, ded_gm deduction_gm, saldo_akhir, rate, saldo_akhir_idr, due_current, due_1_30, due_31_60, due_61_90, due_91_120, due_121_180, due_181_360, due_gt_360, total_due, pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe
    FROM ($sql_pv) pv
) a LEFT JOIN (select * from ap_masterrate where tanggal = '$end_date' and v_codecurr = CASE  WHEN tanggal = LAST_DAY(tanggal) THEN 'HARIAN' ELSE 'PAJAK' END GROUP BY  curr, rate) b on b.curr = a.curr GROUP BY supplier,a.curr order by supplier,a.curr asc
";
?>
<html>
<head><title>AP Report - Summary Supplier</title></head>
<body>
<style>
body { font-family: sans-serif; }
table { margin: 10px auto; border-collapse: collapse; }
table th, table td { border: 1px solid #3c3c3c; padding: 3px 6px; font-size: 10px; }
.h-default { background-color: #FFE4E1; text-align: center; vertical-align: middle; }
.h-aging   { background-color: #98FB98; text-align: center; vertical-align: middle; }
.h-proj    { background-color: #87CEFA; text-align: center; vertical-align: middle; }
.h-spacer  { border: none; background: white; width: 30px; }
td.num     { text-align: right; }
</style>

<h4>Payable Card Statement - SUMMARY SUPPLIER<br>PERIODE: <?= $label_start ?> - <?= $label_end ?></h4>

<table style="width:100%;font-size:10px;" border="1">
<thead>
<tr>
    <th rowspan="2" class="h-default">No</th>
    <th rowspan="2" class="h-default">Nama Supplier</th>
    <th rowspan="2" class="h-default">Currency</th>
    <th rowspan="2" class="h-default">Begining Balance</th>
    <th rowspan="2" class="h-default">Addition</th>
    <th rowspan="2" class="h-default">Reverse</th>
    <th rowspan="2" class="h-default">Deduction Advance</th>
    <th rowspan="2" class="h-default">Deduction Other</th>
    <th rowspan="2" class="h-default">Deduction Bank</th>
    <th rowspan="2" class="h-default">Deduction Cash</th>
    <th rowspan="2" class="h-default">Deduction Non Bank</th>
    <th rowspan="2" class="h-default">Deduction GM</th>
    <th rowspan="2" class="h-default">Adjustment</th>
    <th rowspan="2" class="h-default">Ending Balance</th>
    <th rowspan="2" class="h-default">Rate</th>
    <th rowspan="2" class="h-default">Ending Balance IDR</th>
    <th rowspan="2" class="h-spacer"></th>
    <th colspan="9" class="h-aging">Account Payable Aging Based on Due Date</th>
    <th rowspan="2" class="h-spacer"></th>
    <th colspan="8" class="h-proj">Account Payable Based on Due Date Projection</th>
</tr>
<tr>
    <th class="h-aging">Current</th>
    <th class="h-aging">1-30</th>
    <th class="h-aging">31-60</th>
    <th class="h-aging">61-90</th>
    <th class="h-aging">91-120</th>
    <th class="h-aging">121-180</th>
    <th class="h-aging">181-360</th>
    <th class="h-aging">&gt;360</th>
    <th class="h-aging">Total</th>
    <th class="h-proj">Due</th>
    <?php foreach ($proj_months as $pm): ?>
    <th class="h-proj"><?= htmlspecialchars($pm) ?></th>
    <?php endforeach; ?>
    <th class="h-proj">Total</th>
</tr>
</thead>
<tbody>
<?php
$q  = mysqli_query($conn2, $sql_sum);
$no = 1;
while ($row = mysqli_fetch_assoc($q)):
    $nf = fn($v) => number_format((float)$v, 2, '.', ',');
?>
<tr style="font-size:10px;">
    <td style="text-align:center;"><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['supplier']) ?></td>
    <td><?= htmlspecialchars($row['curr']) ?></td>
    <td class="num"><?= $nf($row['saldo_awal']) ?></td>
    <td class="num"><?= $nf($row['addition']) ?></td>
    <td class="num"><?= $nf($row['reverse']) ?></td>
    <td class="num"><?= $nf($row['deduction_advance']) ?></td>
    <td class="num"><?= $nf($row['deduction_other']) ?></td>
    <td class="num"><?= $nf($row['ded_bank']) ?></td>
    <td class="num"><?= $nf($row['ded_cash']) ?></td>
    <td class="num"><?= $nf($row['ded_nonbank']) ?></td>
    <td class="num"><?= $nf($row['deduction_gm']) ?></td>
    <td class="num"><?= $nf($row['adjust']) ?></td>
    <td class="num"><?= $nf($row['saldo_akhir']) ?></td>
    <td class="num"><?= $nf($row['rate']) ?></td>
    <td class="num"><?= $nf($row['saldo_akhir_idr']) ?></td>
    <td class="h-spacer">&nbsp;</td>
    <td class="num"><?= $nf($row['due_current']) ?></td>
    <td class="num"><?= $nf($row['due_1_30']) ?></td>
    <td class="num"><?= $nf($row['due_31_60']) ?></td>
    <td class="num"><?= $nf($row['due_61_90']) ?></td>
    <td class="num"><?= $nf($row['due_91_120']) ?></td>
    <td class="num"><?= $nf($row['due_121_180']) ?></td>
    <td class="num"><?= $nf($row['due_181_360']) ?></td>
    <td class="num"><?= $nf($row['due_gt_360']) ?></td>
    <td class="num"><?= $nf($row['total_due']) ?></td>
    <td class="h-spacer">&nbsp;</td>
    <td class="num"><?= $nf($row['pro_due']) ?></td>
    <td class="num"><?= $nf($row['pro_due0']) ?></td>
    <td class="num"><?= $nf($row['pro_due1']) ?></td>
    <td class="num"><?= $nf($row['pro_due2']) ?></td>
    <td class="num"><?= $nf($row['pro_due3']) ?></td>
    <td class="num"><?= $nf($row['pro_due4']) ?></td>
    <td class="num"><?= $nf($row['pro_due5']) ?></td>
    <td class="num"><?= $nf($row['tot_produe']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</body>
</html>
