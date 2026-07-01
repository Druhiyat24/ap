<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=AP Report - PAYMENT VOUCHER.xls");

include '../../../conn/conn.php';
include 'query_pv2.php'; // sets $sql, $start_date, $end_date

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
?>
<html>
<head><title>AP Report - Payment Voucher</title></head>
<body>
<style>
body { font-family: sans-serif; }
table { margin: 10px auto; border-collapse: collapse; }
table th, table td { border: 1px solid #3c3c3c; padding: 3px 6px; font-size: 10px; }
.h-default { background-color: #FFE4E1; text-align: center; vertical-align: middle; }
.h-item    { background-color: #FFDAB9; text-align: center; vertical-align: middle; }
.h-aging   { background-color: #98FB98; text-align: center; vertical-align: middle; }
.h-proj    { background-color: #87CEFA; text-align: center; vertical-align: middle; }
.h-spacer  { border: none; background: white; width: 30px; }
td.num     { text-align: right; }
</style>

<h4>Payable Card Statement - PAYMENT VOUCHER<br>PERIODE: <?= $label_start ?> - <?= $label_end ?></h4>

<table style="width:100%;font-size:10px;" border="1">
<thead>
<tr>
    <th rowspan="2" class="h-default">No</th>
    <th rowspan="2" class="h-default">Nama Supplier</th>
    <th rowspan="2" class="h-default">Payment Voucher Number</th>
    <th rowspan="2" class="h-default">PV Date</th>
    <th rowspan="2" class="h-default">Due Date</th>
    <th rowspan="2" class="h-default">Currency</th>
    <th rowspan="2" class="h-default">Beginning Balance</th>
    <th rowspan="2" class="h-default">Addition</th>
    <th rowspan="2" class="h-default">Deduction Advance</th>
    <th rowspan="2" class="h-default">Deduction Others</th>
    <th rowspan="2" class="h-default">Deduction Bank</th>
    <th rowspan="2" class="h-default">Deduction Cash</th>
    <th rowspan="2" class="h-default">Deduction Non Bank</th>
    <th rowspan="2" class="h-default">Deduction GM</th>
    <th rowspan="2" class="h-default">Reverse</th>
    <th rowspan="2" class="h-default">Ending Balance</th>
    <th rowspan="2" class="h-default">Rate</th>
    <th rowspan="2" class="h-default">Ending Balance IDR</th>
    <th rowspan="2" class="h-default">COA No</th>
    <th rowspan="2" class="h-default">COA Name</th>
    <th rowspan="2" class="h-item">Item Type 1</th>
    <th rowspan="2" class="h-item">Item Type 2</th>
    <th rowspan="2" class="h-item">Relationship</th>
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
$q  = mysqli_query($conn2, $sql);
$no = 1;
while ($row = mysqli_fetch_assoc($q)):
    $nf = fn($v) => number_format((float)$v, 2, '.', ',');
?>
<tr style="font-size:10px;">
    <td style="text-align:center;"><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['supplier']) ?></td>
    <td><?= htmlspecialchars($row['no_kbon']) ?></td>
    <td><?= date("d-M-Y", strtotime($row['tgl_kbon'])) ?></td>
    <td><?= date("d-M-Y", strtotime($row['duedate'])) ?></td>
    <td><?= htmlspecialchars($row['curr']) ?></td>
    <td class="num"><?= $nf($row['saldo_awal']) ?></td>
    <td class="num"><?= $nf($row['total_in']) ?></td>
    <td class="num"><?= $nf($row['uang_muka']) ?></td>
    <td class="num"><?= $nf($row['potongan']) ?></td>
    <td class="num"><?= $nf($row['ded_bank']) ?></td>
    <td class="num"><?= $nf($row['ded_cash']) ?></td>
    <td class="num"><?= $nf($row['ded_nonbank']) ?></td>
    <td class="num"><?= $nf($row['ded_gm']) ?></td>
    <td class="num"><?= $nf($row['reverse_kontrabon']) ?></td>
    <td class="num"><?= $nf($row['saldo_akhir']) ?></td>
    <td class="num"><?= $nf($row['rate']) ?></td>
    <td class="num"><?= $nf($row['saldo_akhir_idr']) ?></td>
    <td><?= htmlspecialchars($row['no_coa']) ?></td>
    <td><?= htmlspecialchars($row['nama_coa']) ?></td>
    <td><?= htmlspecialchars($row['item_type1']) ?></td>
    <td><?= htmlspecialchars($row['item_type2']) ?></td>
    <td><?= htmlspecialchars($row['relasi']) ?></td>
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
