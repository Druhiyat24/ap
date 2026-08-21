<style>
table.dataTable {
  width: 100%;
  min-width: max-content;
}
.dataTables_wrapper .dataTables_scrollBody {
  overflow-x: auto;
}
</style>

<!-- ====== TAB CONTENT: SPL MONTHLY ====== -->
<?php
require_once __DIR__ . '/fs_monthly_functions.php';

$profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center'] : 'ALL';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');

// Beginning balance ikut bulan "From" yang dipilih user, tidak dikunci ke
// Januari (biar konsisten dengan trial_balance_monthly.php).
$bulan_awal = date('m', strtotime($start_date));
$tahun_awal = date('Y', strtotime($start_date));
$bulan_akhir = date('m', strtotime($end_date));
$tahun_akhir = date('Y', strtotime($end_date));
$kata_filter = date('M', strtotime($start_date)) . '_' . $tahun_awal;

$periods = fsMonthlyGetPeriods($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir);
$pcCols = ($profit_center === 'ALL') ? ['NAG', 'NAK', 'ALL'] : [$profit_center];

// Blok berjenjang persis fs_ytd/statement_profit_loss.php: tiap blok
// nambah 1 kategori ke running total sebelumnya. Semua kategori SPL pakai
// formula credit-normal (saldo + credit - debit) dan hasilnya tinggal
// dijumlah langsung (tandanya sudah otomatis benar dari formula itu, tidak
// perlu dikurangi manual) - persis pola yang dipakai di versi YTD-nya.
$blocks = [
    ['kategori' => ['PENJUALAN KOTOR', 'RETURN PENJUALAN', 'POTONGAN PENJUALAN'], 'runningLabel' => 'NET SALES'],
    ['kategori' => ['BEBAN POKOK PENJUALAN'], 'runningLabel' => 'GROSS PROFIT / (LOSS)'],
    ['kategori' => ['BEBAN LAINNYA'], 'runningLabel' => 'PROFIT / (LOSS) BEFORE INTEREST AND TAX'],
    ['kategori' => ['BEBAN BUNGA'], 'runningLabel' => 'PROFIT / (LOSS) BEFORE TAX'],
    ['kategori' => ['BEBAN PAJAK'], 'runningLabel' => 'NET INCOME / (LOSS)'],
];

// Ambil data tiap kategori x tiap bulan sekali di awal.
$monthCategoryData = []; // [kategori][period_label] = rows
foreach ($periods as $p) {
    foreach ($blocks as $block) {
        foreach ($block['kategori'] as $kategori) {
            if (!isset($monthCategoryData[$kategori])) {
                $monthCategoryData[$kategori] = [];
            }
            $monthCategoryData[$kategori][$p['label']] = fsGetCategoryTotals(
                $conn2, [$kategori], $bulan_awal, $tahun_awal, $p['bulan'], $p['tahun'], $kata_filter, true
            );
        }
    }
}

$colCount = 1 + (count($periods) * count($pcCols));
?>

<div class="table-responsive mt-2">
  <table id="table_splmonthly" class="table table-striped table-bordered table-hover table-sm nowrap">
    <thead class="text-white" style="background-color: #2563EB;">
      <tr>
        <th rowspan="2">Description</th>
        <?php foreach ($periods as $p) : ?>
          <th colspan="<?= count($pcCols); ?>"><?= htmlspecialchars($p['label']); ?></th>
        <?php endforeach; ?>
      </tr>
      <tr>
        <?php foreach ($periods as $p) : ?>
          <?php foreach ($pcCols as $pcCol) : ?>
            <th><?= htmlspecialchars($pcCol); ?></th>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php
      $runningTotalPerMonth = []; // [period_label][pc] = running total sampai blok sebelumnya

      foreach ($blocks as $block) {
          $blockTotalPerMonth = [];

          foreach ($block['kategori'] as $kategori) {
              echo '<tr class="table-primary"><th colspan="' . $colCount . '" style="text-align:left;">' . htmlspecialchars($kategori) . '</th></tr>';

              $subKategoriSet = [];
              foreach ($periods as $p) {
                  foreach ($monthCategoryData[$kategori][$p['label']] as $row) {
                      $subKategoriSet[$row['sub_kategori']] = true;
                  }
              }

              foreach (array_keys($subKategoriSet) as $subKategori) {
                  echo '<tr><td style="text-align:left;padding-left:20px;">' . htmlspecialchars($subKategori) . '</td>';
                  foreach ($periods as $p) {
                      $row = null;
                      foreach ($monthCategoryData[$kategori][$p['label']] as $r) {
                          if ($r['sub_kategori'] === $subKategori) {
                              $row = $r;
                              break;
                          }
                      }
                      $row = $row ?? ['total_nag' => 0, 'total_nak' => 0, 'total_all' => 0];
                      echo fsMonthlyRenderValueCells($row, $pcCols);

                      foreach (['NAG', 'NAK', 'ALL'] as $pc) {
                          $blockTotalPerMonth[$p['label']][$pc] = ($blockTotalPerMonth[$p['label']][$pc] ?? 0) + (float) ($row['total_' . strtolower($pc)] ?? 0);
                      }
                  }
                  echo '</tr>';
              }
          }

          // Running total kumulatif sampai blok ini.
          echo '<tr class="grand-total" style="border-top:3px double #000;"><th style="text-align:left;">' . htmlspecialchars($block['runningLabel']) . '</th>';
          foreach ($periods as $p) {
              $totalRow = ['total_nag' => 0, 'total_nak' => 0, 'total_all' => 0];
              foreach (['NAG', 'NAK', 'ALL'] as $pc) {
                  $prev = $runningTotalPerMonth[$p['label']][$pc] ?? 0;
                  $add = $blockTotalPerMonth[$p['label']][$pc] ?? 0;
                  $runningTotalPerMonth[$p['label']][$pc] = $prev + $add;
                  $totalRow['total_' . strtolower($pc)] = $runningTotalPerMonth[$p['label']][$pc];
              }
              echo str_replace('<td', '<th', str_replace('</td>', '</th>', fsMonthlyRenderValueCells($totalRow, $pcCols)));
          }
          echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>
