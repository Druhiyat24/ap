<?php
// Export Excel untuk tab Trial Balance Monthly di Financial Statement 2
// (module/AP/fs_monthly/trial_balance_monthly.php). Dipisah dari
// ekspor_tb_monthly.php (punya menu Financial Statement 1) karena itu pakai
// sumber data lama (saldo_awal_tb, kolom jan_$tahun/feb_$tahun/dst) yang
// berbeda dari engine FS2 (fs_saldo_awal_tb + kategori mastercoa_v2) yang
// dipakai tab ini.
include '../../conn/conn.php';
require_once __DIR__ . '/fs_monthly/fs_monthly_functions.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=trial-balance-monthly.xls");

$profit_center = isset($_GET['profit_center']) && $_GET['profit_center'] !== '' ? $_GET['profit_center'] : 'ALL';
$start_date = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : date('M Y');

$bulan_awal = date('m', strtotime($start_date));
$tahun_awal = date('Y', strtotime($start_date));
$bulan_akhir = date('m', strtotime($end_date));
$tahun_akhir = date('Y', strtotime($end_date));
$kata_filter = date('M', strtotime($start_date)) . '_' . $tahun_awal;

$periods = fsMonthlyGetPeriods($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir);

$pcCols = ($profit_center === 'ALL') ? ['NAG', 'NAK', 'ALL'] : [$profit_center];
$pcQueryList = ($profit_center === 'ALL') ? ['NAG', 'NAK'] : [$profit_center];

// Kolom paling awal selalu 1 bulan persis sebelum "From" - dinamis ikut
// "From" yang dipilih (From = Feb 2026 -> kolom ini Jan 2026, bukan dikunci
// ke Desember).
$prevPeriod = fsMonthlyGetPrevPeriod($bulan_awal, $tahun_awal);
$prevYearLabel = $prevPeriod['label'];
$lastPeriod = end($periods);
$lastPeriodLabel = $lastPeriod['label'];

$sqlCoa = mysqli_query($conn2, "
  select no_coa, nama_coa, indname1, indname2, indname3, indname4
  from mastercoa_v2 a
  left join (
    select a.id_ctg5 as id_ctg5A, b.ind_name as indname4, c.ind_name as indname3,
           d.ind_name as indname2, e.ind_name as indname1
    from master_coa_ctg5 a
    INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4
    INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3
    INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2
    INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1
    GROUP BY a.id_ctg5
  ) b on b.id_ctg5A = a.id_ctg5
  GROUP BY no_coa
  order by no_coa asc
");

$coaList = [];
while ($row = mysqli_fetch_assoc($sqlCoa)) {
    $coaList[$row['no_coa']] = $row;
}

$monthData = [];

$monthData[$prevYearLabel] = [];
foreach ($pcQueryList as $pc) {
    $pcEsc = mysqli_real_escape_string($conn2, $pc);
    $quePrev = "select no_coa, COALESCE($kata_filter,0) saldo_akhir from fs_saldo_awal_tb where profit_center = '$pcEsc'";
    $sqlPrev = mysqli_query($conn2, $quePrev);
    $monthData[$prevYearLabel][$pc] = [];
    while ($row = mysqli_fetch_assoc($sqlPrev)) {
        $monthData[$prevYearLabel][$pc][$row['no_coa']] = (float) $row['saldo_akhir'];
    }
}

foreach ($periods as $p) {
    $monthData[$p['label']] = [];
    foreach ($pcQueryList as $pc) {
        $pcEsc = mysqli_real_escape_string($conn2, $pc);
        $que = "
          select no_coa, (COALESCE(saldo,0) + COALESCE(debit_idr,0) - COALESCE(credit_idr,0)) saldo_akhir
          from (
            select a.no_coa, saldo, COALESCE(credit_idr,0) credit_idr, COALESCE(debit_idr,0) debit_idr
            from (select no_coa, $kata_filter as saldo from fs_saldo_awal_tb where profit_center = '$pcEsc') a
            LEFT JOIN (
              select no_coa, sum(ROUND(credit * rate,2)) credit_idr, sum(ROUND(debit * rate,2)) debit_idr
              from tbl_list_journal
              where tgl_journal BETWEEN
                (select tgl_awal from tbl_tgl_tb where bulan = '{$bulan_awal}' and tahun = '{$tahun_awal}')
                and (select tgl_akhir from tbl_tgl_tb where bulan = '{$p['bulan']}' and tahun = '{$p['tahun']}')
                and profit_center = '$pcEsc'
              group by no_coa
            ) e on e.no_coa = a.no_coa
          ) a
        ";
        $sqlMonth = mysqli_query($conn2, $que);
        $monthData[$p['label']][$pc] = [];
        while ($row = mysqli_fetch_assoc($sqlMonth)) {
            $monthData[$p['label']][$pc][$row['no_coa']] = (float) $row['saldo_akhir'];
        }
    }
}
?>
<html>
<head>
<title>Trial Balance Monthly</title>
<style>
  body, table, th, td {
    font-family: Calibri, sans-serif;
    font-size: 11pt;
  }
</style>
</head>
<body>
<?php $totalCols = 6 + ((count($periods) + 2) * count($pcCols)); ?>
<table border="1" cellspacing="0" cellpadding="6">
  <tr>
    <td colspan="<?= $totalCols; ?>" style="border:none;font-weight:bold;font-size:12pt;text-align:left;">TRIAL BALANCE MONTHLY</td>
  </tr>
  <tr>
    <td colspan="<?= $totalCols; ?>" style="border:none;font-weight:bold;font-size:12pt;text-align:left;">PERIODE <?= htmlspecialchars($prevYearLabel); ?> - <?= htmlspecialchars($lastPeriodLabel); ?></td>
  </tr>
  <tr>
    <td colspan="<?= $totalCols; ?>" style="border:none;font-weight:bold;font-size:12pt;text-align:left;">PROFIT CENTER: <?= htmlspecialchars($profit_center); ?></td>
  </tr>
  <tr>
    <td colspan="<?= $totalCols; ?>" style="border:none;">&nbsp;</td>
  </tr>
  <?php
    $showPcSubHeader = count($pcCols) > 1;
    $thBase = 'text-align:left;';
    $thNoBottom = $thBase . ($showPcSubHeader ? 'border-bottom:none;' : '');
  ?>
  <tr>
    <th style="<?= $thNoBottom; ?>">No COA</th>
    <th style="<?= $thNoBottom; ?>">COA Name</th>
    <th style="<?= $thNoBottom; ?>">Category 1</th>
    <th style="<?= $thNoBottom; ?>">Category 2</th>
    <th style="<?= $thNoBottom; ?>">Category 3</th>
    <th style="<?= $thNoBottom; ?>">Category 4</th>
    <th colspan="<?= count($pcCols); ?>" style="<?= $thBase; ?>"><?= htmlspecialchars($prevYearLabel); ?></th>
    <?php foreach ($periods as $p) : ?>
      <th colspan="<?= count($pcCols); ?>" style="<?= $thBase; ?>"><?= htmlspecialchars($p['label']); ?></th>
    <?php endforeach; ?>
    <th colspan="<?= count($pcCols); ?>" style="<?= $thBase; ?>">YTD</th>
  </tr>
  <?php if ($showPcSubHeader) : ?>
  <tr>
    <th style="<?= $thBase; ?>border-top:none;"></th>
    <th style="<?= $thBase; ?>border-top:none;"></th>
    <th style="<?= $thBase; ?>border-top:none;"></th>
    <th style="<?= $thBase; ?>border-top:none;"></th>
    <th style="<?= $thBase; ?>border-top:none;"></th>
    <th style="<?= $thBase; ?>border-top:none;"></th>
    <?php foreach ($pcCols as $pcCol) : ?>
      <th style="<?= $thBase; ?>"><?= htmlspecialchars($pcCol); ?></th>
    <?php endforeach; ?>
    <?php foreach ($periods as $p) : ?>
      <?php foreach ($pcCols as $pcCol) : ?>
        <th style="<?= $thBase; ?>"><?= htmlspecialchars($pcCol); ?></th>
      <?php endforeach; ?>
    <?php endforeach; ?>
    <?php foreach ($pcCols as $pcCol) : ?>
      <th style="<?= $thBase; ?>"><?= htmlspecialchars($pcCol); ?></th>
    <?php endforeach; ?>
  </tr>
  <?php endif; ?>
  <?php foreach ($coaList as $noCoa => $coa) : ?>
    <tr>
      <td><?= htmlspecialchars($noCoa); ?></td>
      <td><?= htmlspecialchars($coa['nama_coa']); ?></td>
      <td><?= htmlspecialchars($coa['indname1']); ?></td>
      <td><?= htmlspecialchars($coa['indname2']); ?></td>
      <td><?= htmlspecialchars($coa['indname3']); ?></td>
      <td><?= htmlspecialchars($coa['indname4']); ?></td>
      <?php
      $columnLabels = array_merge([$prevYearLabel], array_column($periods, 'label'));
      foreach ($columnLabels as $colLabel) {
          if ($profit_center === 'ALL') {
              $nagVal = $monthData[$colLabel]['NAG'][$noCoa] ?? 0;
              $nakVal = $monthData[$colLabel]['NAK'][$noCoa] ?? 0;
              echo '<td style="mso-number-format:\'#,##0.00\';text-align:right;">' . fsMonthlyFormatNumber($nagVal) . '</td>';
              echo '<td style="mso-number-format:\'#,##0.00\';text-align:right;">' . fsMonthlyFormatNumber($nakVal) . '</td>';
              echo '<td style="mso-number-format:\'#,##0.00\';text-align:right;">' . fsMonthlyFormatNumber($nagVal + $nakVal) . '</td>';
          } else {
              $val = $monthData[$colLabel][$profit_center][$noCoa] ?? 0;
              echo '<td style="mso-number-format:\'#,##0.00\';text-align:right;">' . fsMonthlyFormatNumber($val) . '</td>';
          }
      }

      // Kolom YTD - saldo akhir bulan TERAKHIR yang tampil (bukan dijumlah),
      // karena saldo tiap bulan sudah kumulatif sejak "From".
      $lastLabel = end($columnLabels);
      $ytdStyle = 'mso-number-format:\'#,##0.00\';text-align:right;font-weight:bold;';
      if ($profit_center === 'ALL') {
          $ytdNag = $monthData[$lastLabel]['NAG'][$noCoa] ?? 0;
          $ytdNak = $monthData[$lastLabel]['NAK'][$noCoa] ?? 0;
          echo '<td style="' . $ytdStyle . '">' . fsMonthlyFormatNumber($ytdNag) . '</td>';
          echo '<td style="' . $ytdStyle . '">' . fsMonthlyFormatNumber($ytdNak) . '</td>';
          echo '<td style="' . $ytdStyle . '">' . fsMonthlyFormatNumber($ytdNag + $ytdNak) . '</td>';
      } else {
          $ytdVal = $monthData[$lastLabel][$profit_center][$noCoa] ?? 0;
          echo '<td style="' . $ytdStyle . '">' . fsMonthlyFormatNumber($ytdVal) . '</td>';
      }
      ?>
    </tr>
  <?php endforeach; ?>
</table>
</body>
</html>
