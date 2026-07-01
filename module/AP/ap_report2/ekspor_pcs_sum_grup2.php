<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=AP Report - SUMMARY GROUP.xls");

include '../../../conn/conn.php';

include 'query_bpb2.php';
$sql_bpb = $sql;

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
    $m0 = (int)date('m', $base); $y0 = (int)date('Y', $base);
    for ($i = 0; $i < 6; $i++) {
        $mi = (($m0 - 1 + $i) % 12) + 1;
        $yi = $y0 + intdiv($m0 - 1 + $i, 12);
        $proj_months[] = $bulan_indo[$mi] . ' ' . $yi;
    }
}

// Combined UNION BPB + PV (no relasi filter — used for grand totals too)
$combined = "
SELECT supplier, item_type2, curr, relasi, saldo_akhir, saldo_akhir_idr,
    due_current, due_1_30, due_31_60, due_61_90, due_91_120,
    due_121_180, due_181_360, due_gt_360, total_due,
    pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe
FROM ($sql_bpb) bpb
UNION ALL
SELECT supplier, item_type2, curr, relasi, saldo_akhir, saldo_akhir_idr,
    due_current, due_1_30, due_31_60, due_61_90, due_91_120,
    due_121_180, due_181_360, due_gt_360, total_due,
    pro_due, pro_due0, pro_due1, pro_due2, pro_due3, pro_due4, pro_due5, tot_produe
FROM ($sql_pv) pv
";

$agg = "ROUND(SUM(IF(UPPER(curr)!='IDR',saldo_akhir,0)),2) saldo_akhir,
    ROUND(SUM(saldo_akhir_idr),2) saldo_akhir_idr,
    ROUND(SUM(due_current),2) due_current,
    ROUND(SUM(due_1_30),2) due_1_30,
    ROUND(SUM(due_31_60),2) due_31_60,
    ROUND(SUM(due_61_90),2) due_61_90,
    ROUND(SUM(due_91_120),2) due_91_120,
    ROUND(SUM(due_121_180),2) due_121_180,
    ROUND(SUM(due_181_360),2) due_181_360,
    ROUND(SUM(due_gt_360),2) due_gt_360,
    ROUND(SUM(total_due),2) total_due,
    ROUND(SUM(pro_due),2) pro_due,
    ROUND(SUM(pro_due0),2) pro_due0,
    ROUND(SUM(pro_due1),2) pro_due1,
    ROUND(SUM(pro_due2),2) pro_due2,
    ROUND(SUM(pro_due3),2) pro_due3,
    ROUND(SUM(pro_due4),2) pro_due4,
    ROUND(SUM(pro_due5),2) pro_due5,
    ROUND(SUM(tot_produe),2) tot_produe";

$foot_agg = "SUM(saldo_akhir) saldo_akhir, SUM(saldo_akhir_idr) saldo_akhir_idr,
    SUM(due_current) due_current, SUM(due_1_30) due_1_30,
    SUM(due_31_60) due_31_60, SUM(due_61_90) due_61_90,
    SUM(due_91_120) due_91_120, SUM(due_121_180) due_121_180,
    SUM(due_181_360) due_181_360, SUM(due_gt_360) due_gt_360,
    SUM(total_due) total_due,
    SUM(pro_due) pro_due, SUM(pro_due0) pro_due0, SUM(pro_due1) pro_due1,
    SUM(pro_due2) pro_due2, SUM(pro_due3) pro_due3, SUM(pro_due4) pro_due4,
    SUM(pro_due5) pro_due5, SUM(tot_produe) tot_produe";

// Grouped summary queries
$sql_grup     = "SELECT supplier grp_col, curr, $agg FROM ($combined) c WHERE relasi='GROUP' GROUP BY supplier, curr ORDER BY supplier, curr ASC";
$sql_non_grup = "SELECT item_type2 grp_col, curr, $agg FROM ($combined) c WHERE relasi='NON GROUP' GROUP BY item_type2, curr ORDER BY item_type2, curr ASC";
// For footer by curr (to filter IDR vs USD)
$sql_g_curr   = "SELECT curr, $agg FROM ($combined) c WHERE relasi='GROUP' GROUP BY curr";
$sql_ng_curr  = "SELECT curr, $agg FROM ($combined) c WHERE relasi='NON GROUP' GROUP BY curr";
$sql_all_curr = "SELECT curr, $agg FROM ($combined) c GROUP BY curr";

// Pre-fetch footer rows
$q_g_idr  = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_g_curr)  x WHERE curr = 'IDR'");
$q_g_usd  = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_g_curr)  x WHERE curr != 'IDR'");
$q_g_all  = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_g_curr)  x");
$q_ng_idr = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_ng_curr) x WHERE curr = 'IDR'");
$q_ng_usd = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_ng_curr) x WHERE curr != 'IDR'");
$q_ng_all = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_ng_curr) x");
$q_a_idr  = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_all_curr) x WHERE curr = 'IDR'");
$q_a_usd  = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_all_curr) x WHERE curr != 'IDR'");
$q_a_all  = mysqli_query($conn2, "SELECT $foot_agg FROM ($sql_all_curr) x");

$r_g_idr  = mysqli_fetch_assoc($q_g_idr);
$r_g_usd  = mysqli_fetch_assoc($q_g_usd);
$r_g_all  = mysqli_fetch_assoc($q_g_all);
$r_ng_idr = mysqli_fetch_assoc($q_ng_idr);
$r_ng_usd = mysqli_fetch_assoc($q_ng_usd);
$r_ng_all = mysqli_fetch_assoc($q_ng_all);
$r_a_idr  = mysqli_fetch_assoc($q_a_idr);
$r_a_usd  = mysqli_fetch_assoc($q_a_usd);
$r_a_all  = mysqli_fetch_assoc($q_a_all);

$q_grup_data     = mysqli_query($conn2, $sql_grup);
$q_non_grup_data = mysqli_query($conn2, $sql_non_grup);

$nf = fn($v) => number_format((float)($v ?? 0), 2, '.', ',');

// Helper: render aging + proj cells for a row
function aging_proj_cells($row, $nf, $proj_months) {
    $aging_keys = ['due_current','due_1_30','due_31_60','due_61_90','due_91_120','due_121_180','due_181_360','due_gt_360','total_due'];
    $proj_keys  = ['pro_due','pro_due0','pro_due1','pro_due2','pro_due3','pro_due4','pro_due5','tot_produe'];
    $h = '<td style="border:none;background:white;">&nbsp;</td>';
    foreach ($aging_keys as $k) $h .= '<td style="text-align:right;">' . $nf($row[$k] ?? 0) . '</td>';
    $h .= '<td style="border:none;background:white;">&nbsp;</td>';
    foreach ($proj_keys as $k) $h .= '<td style="text-align:right;">' . $nf($row[$k] ?? 0) . '</td>';
    return $h;
}

// Helper: render footer row (colspan 3 = label+saldo_akhir+saldo_akhir_idr visible; colspan 4 = only saldo_akhir_idr)
function footer_row($row, $label, $colspan, $nf, $proj_months) {
    if (!$row) return;
    $aging_keys = ['due_current','due_1_30','due_31_60','due_61_90','due_91_120','due_121_180','due_181_360','due_gt_360','total_due'];
    $proj_keys  = ['pro_due','pro_due0','pro_due1','pro_due2','pro_due3','pro_due4','pro_due5','tot_produe'];
    echo '<tr style="font-size:10px;">';
    if ($colspan == 3) {
        echo '<th colspan="3" style="text-align:center;vertical-align:middle;">' . htmlspecialchars($label) . '</th>';
        echo '<th style="text-align:right;">' . $nf($row['saldo_akhir']) . '</th>';
        echo '<th style="text-align:right;">' . $nf($row['saldo_akhir_idr']) . '</th>';
    } else {
        echo '<th colspan="4" style="text-align:center;vertical-align:middle;">' . htmlspecialchars($label) . '</th>';
        echo '<th style="text-align:right;">' . $nf($row['saldo_akhir_idr']) . '</th>';
    }
    echo '<td style="border:none;background:white;">&nbsp;</td>';
    foreach ($aging_keys as $k) echo '<th style="text-align:right;">' . $nf($row[$k] ?? 0) . '</th>';
    echo '<td style="border:none;background:white;">&nbsp;</td>';
    foreach ($proj_keys as $k) echo '<th style="text-align:right;">' . $nf($row[$k] ?? 0) . '</th>';
    echo '</tr>';
}
?>
<html>
<head><title>AP Report - Summary Group</title></head>
<body>
<style>
body { font-family: sans-serif; }
table { margin: 10px auto; border-collapse: collapse; }
table th, table td { border: 1px solid #3c3c3c; padding: 3px 6px; font-size: 10px; }
.h-default { background-color: #FFE4E1; text-align: center; vertical-align: middle; }
.h-aging   { background-color: #98FB98; text-align: center; vertical-align: middle; }
.h-proj    { background-color: #87CEFA; text-align: center; vertical-align: middle; }
.h-spacer  { border: none; background: white; width: 30px; }
</style>

<h4>Payable Card Statement - SUMMARY<br>PERIODE: <?= $label_start ?> - <?= $label_end ?></h4>

<table style="width:100%;font-size:10px;" border="1">
<thead>
<tr>
    <th rowspan="2" class="h-default">No</th>
    <th rowspan="2" class="h-default">Supplier Category &amp; Name</th>
    <th colspan="3" class="h-default">Amount</th>
    <th rowspan="2" class="h-spacer"></th>
    <th colspan="9" class="h-aging">Account Payable Aging Based on Due Date</th>
    <th rowspan="2" class="h-spacer"></th>
    <th colspan="8" class="h-proj">Account Payable Based on Due Date Projection</th>
</tr>
<tr>
    <th class="h-default">Currency</th>
    <th class="h-default">Foreign Currency</th>
    <th class="h-default">Equivalent IDR</th>
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
<!-- GROUP section label -->
<tr>
    <th colspan="5" style="background:white;text-align:left;color:#333;">GROUP</th>
    <th style="border:none;background:white;"></th>
    <th colspan="9" style="background:white;"></th>
    <th style="border:none;background:white;"></th>
    <th colspan="8" style="background:white;"></th>
</tr>
</thead>
<tbody>
<?php
$no = 1;
while ($row = mysqli_fetch_assoc($q_grup_data)):
?>
<tr style="font-size:10px;text-align:left;">
    <td style="text-align:center;"><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['grp_col']) ?></td>
    <td><?= htmlspecialchars($row['curr']) ?></td>
    <td style="text-align:right;"><?= $nf($row['saldo_akhir']) ?></td>
    <td style="text-align:right;"><?= $nf($row['saldo_akhir_idr']) ?></td>
    <?= aging_proj_cells($row, $nf, $proj_months) ?>
</tr>
<?php endwhile; ?>
</tbody>
<!-- GROUP footer -->
<?php
footer_row($r_g_idr, 'Total IDR',    3, $nf, $proj_months);
footer_row($r_g_usd, 'Total USD',    3, $nf, $proj_months);
footer_row($r_g_all, 'Summary Group',4, $nf, $proj_months);
?>

<!-- NON GROUP section label -->
<tr>
    <th colspan="5" style="background:white;text-align:left;color:#333;">NON GROUP</th>
    <th style="border:none;background:white;"></th>
    <th colspan="9" style="background:white;"></th>
    <th style="border:none;background:white;"></th>
    <th colspan="8" style="background:white;"></th>
</tr>
<?php
$no = 1;
while ($row = mysqli_fetch_assoc($q_non_grup_data)):
?>
<tr style="font-size:10px;text-align:left;">
    <td style="text-align:center;"><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['grp_col']) ?></td>
    <td><?= htmlspecialchars($row['curr']) ?></td>
    <td style="text-align:right;"><?= $nf($row['saldo_akhir']) ?></td>
    <td style="text-align:right;"><?= $nf($row['saldo_akhir_idr']) ?></td>
    <?= aging_proj_cells($row, $nf, $proj_months) ?>
</tr>
<?php endwhile; ?>
<!-- NON GROUP footer -->
<?php
footer_row($r_ng_idr, 'Total IDR',        3, $nf, $proj_months);
footer_row($r_ng_usd, 'Total USD',        3, $nf, $proj_months);
footer_row($r_ng_all, 'Summary Non Group',4, $nf, $proj_months);
?>

<!-- Grand Total footer -->
<?php
footer_row($r_a_idr, 'Summary Total IDR', 3, $nf, $proj_months);
footer_row($r_a_usd, 'Summary Total USD', 3, $nf, $proj_months);
footer_row($r_a_all, 'Summary Total',     4, $nf, $proj_months);
?>
</table>
</body>
</html>
