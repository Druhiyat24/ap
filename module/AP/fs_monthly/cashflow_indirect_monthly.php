<style>
#cf-indirect .card-body {
  background: #f9fafb;
}
#cf-indirect table {
  color: #333;
}
#cf-indirect th, #cf-indirect td {
  padding: 6px 10px;
}

/* ===== Tombol export ===== */
.sfpm-btn-export-excel {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  flex-shrink: 0;
  font-size: 12.5px;
  font-weight: 600;
  letter-spacing: .2px;
  color: #fff;
  background: linear-gradient(135deg, #1f7a4d, #14532d);
  border: none;
  border-radius: 999px;
  padding: 8px 18px 8px 14px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(20, 83, 45, 0.28);
  transition: box-shadow 0.15s ease, transform 0.15s ease, background 0.15s ease;
}
.sfpm-btn-export-excel .sfpm-btn-icon {
  display: inline-flex;
  width: 16px;
  height: 16px;
}
.sfpm-btn-export-excel:hover {
  background: linear-gradient(135deg, #23935d, #185c36);
  box-shadow: 0 4px 10px rgba(20, 83, 45, 0.35);
  transform: translateY(-1px);
}
.sfpm-btn-export-excel:active {
  transform: translateY(0);
  box-shadow: 0 1px 3px rgba(20, 83, 45, 0.3);
}

/* Class-class di bawah ini (laporan-outer, laporan-container, sfp-title-*,
   sfpm-*, dst) sama persis dengan statement_financial_position_monthly.php
   / statement_profit_loss_monthly.php - lihat catatan lengkap di file SFP
   untuk alasan tiap aturan (freeze scroll, lebar tabel eksplisit, dst). ID
   elemen dibedakan (prefix cfindirect- bukan sfp-/spl-) karena ID wajib
   unik satu halaman. */
.laporan-outer {
  border: 1px solid #dbe3f0;
  border-radius: 14px;
  background: #fafafa;
  box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
  overflow: hidden;
}

.laporan-container {
  max-height: 65vh;
  overflow-x: auto;
  overflow-y: auto;
  scrollbar-gutter: stable;
  background: #fafafa;
  padding: 0 25px 15px;
}

.laporan-table {
  font-size: 12.5px;
  margin: 0 auto;
  table-layout: fixed;
  border-collapse: separate;
  border-spacing: 0;
  color: #2c3e50;
}
.sfpm-tbl td, .sfpm-tbl th {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.sfp-title-block {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  text-align: left;
  padding: 18px 25px 14px;
  background: #fafafa;
  border-bottom: 1px solid #e5e9f2;
}

.sfp-title-company {
  font-weight: 700;
  font-size: 18px;
  color: #1e3a8a;
  letter-spacing: .3px;
}

.sfp-title-report {
  font-weight: 700;
  font-size: 16px;
  color: #2c3e50;
  margin-top: 4px;
}

.sfp-title-report-en {
  font-style: italic;
  font-size: 14.5px;
  color: #6b7280;
}

.sfp-title-period {
  font-weight: 600;
  font-size: 14.5px;
  color: #2c3e50;
  margin-top: 6px;
}

.sfp-title-desc {
  font-size: 13px;
  color: #777;
  margin-top: 3px;
}

.sfp-title-desc-en {
  font-style: italic;
  font-size: 12.5px;
  color: #999;
}

.periode {
  text-align: center;
  color: #1e3a8a;
  border-bottom: 2px solid #1e3a8a;
  font-weight: 600;
  padding-bottom: 6px;
}

.judul-periode {
  text-align: center;
  color: #1e3a8a;
  font-weight: 700;
  font-size: 13px;
  letter-spacing: .2px;
  padding-bottom: 4px;
}

.periode-desc {
  text-align: left;
  vertical-align: middle;
  color: #1e3a8a;
  font-weight: 700;
  font-size: 12.5px;
}

.subsection-left {
  font-weight: bold;
  text-align: left;
  color: #1e3a8a;
  font-size: 13px;
  letter-spacing: .2px;
}

.item-left {
  text-align: left;
  padding: 3px 0;
}

.item-right {
  text-align: right;
}

.total-line {
  line-height: 24px;
  font-weight: bold;
}
.total-line td,
.total-line th {
  border-top: 1px solid #94a3c4;
}

.total-left {
  text-align: left;
}

.total-right {
  text-align: right;
}

.grand-total {
  background: #e8edfa;
  font-weight: bold;
  line-height: 26px;
}
.grand-total td, .grand-total th {
  border-top: 2px solid #1e3a8a;
}

.grand-left {
  text-align: left;
}

.grand-right {
  text-align: right;
}

.spacer {
  height: 15px;
}

.spacer-mid {
  height: 10px;
}

.spacer-small {
  height: 5px;
}

.sfpm-tbl .sfpm-freeze-left {
  position: sticky;
  left: 0;
  z-index: 2;
  background-color: #fafafa;
  box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.2);
}

#cfindirect-header-table tr:last-child th {
  box-shadow: 0 2px 4px -2px rgba(0, 0, 0, 0.3);
}

.sfpm-tbl .sfpm-month-a {
  background-color: #f8fafd;
}
.sfpm-tbl .sfpm-month-b {
  background-color: #eef2fb;
}
.sfpm-tbl tr.grand-total .sfpm-month-a {
  background-color: #e7ecf9;
}
.sfpm-tbl tr.grand-total .sfpm-month-b {
  background-color: #dbe3f6;
}

.sfpm-tbl .sfpm-ytd {
  background-color: #fdf6e6;
  border-left: 2px solid #c9962f;
}
.sfpm-tbl tr.grand-total .sfpm-ytd {
  background-color: #f8e8c4;
}
.sfpm-tbl .judul-periode.sfpm-ytd,
.sfpm-tbl .periode.sfpm-ytd {
  color: #8a5a10;
}

#cfindirect-monthly-table tr:has(td):hover td {
  background-color: rgba(37, 99, 235, 0.1);
}

#cfindirect-monthly-table tr:has(td) > td {
  border-bottom: 1px solid #eef1f6;
}

.sfpm-tbl .sfpm-month-start {
  border-left: 1px solid #ccd6ee;
}

.laporan-container {
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
.laporan-container::-webkit-scrollbar {
  height: 10px;
  width: 10px;
}
.laporan-container::-webkit-scrollbar-track {
  background: #f1f4fa;
}
.laporan-container::-webkit-scrollbar-thumb {
  background-color: #b7c3e0;
  border-radius: 8px;
  border: 2px solid #f1f4fa;
}
.laporan-container::-webkit-scrollbar-thumb:hover {
  background-color: #93a4d1;
}
</style>

<!-- ====== TAB CONTENT: CF INDIRECT MONTHLY ====== -->
<?php
require_once __DIR__ . '/fs_monthly_functions.php';

$profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center'] : 'ALL';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');

$bulan_awal = date('m', strtotime($start_date));
$tahun_awal = date('Y', strtotime($start_date));
$bulan_akhir = date('m', strtotime($end_date));
$tahun_akhir = date('Y', strtotime($end_date));
$kata_filter = date('M', strtotime($start_date)) . '_' . $tahun_awal;

$periods = fsMonthlyGetPeriods($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir);
$lastPeriod = end($periods);

$pcCols = ($profit_center === 'ALL') ? ['NAG', 'NAK', 'ALL'] : [$profit_center];
$pcColLabels = ['ALL' => 'Total'];

$pcColCount = count($pcCols);
$valueColCount = count($periods) * $pcColCount + $pcColCount;
$GLOBALS['sfpmYtdStartIdx'] = count($periods) * $pcColCount;

// ===== Ambil semua data per periode =====

// "Laba (Rugi) Bersih" dihitung MANDIRI di sini (bukan nebeng SPL seperti
// versi YTD - lihat catatan di fsMonthlyNetIncomeByPeriod()).
$netIncomeByPeriod = fsMonthlyNetIncomeByPeriod($conn2, $periods, $pcCols, $bulan_awal, $tahun_awal, $kata_filter);

// "Penyesuaian Akumulasi Penyusutan Aset Tetap" (id_indirect=19, hardcode
// by ID persis versi YTD).
$depreciationValues = [];
foreach ($periods as $p) {
    $row = fsGetCashflowIndirectSingle($conn2, 19, $bulan_awal, $tahun_awal, $p['bulan'], $p['tahun']);
    foreach ($pcCols as $pcCol) {
        $depreciationValues[$p['label']][$pcCol] = $row['total_' . strtolower($pcCol)] ?? 0;
    }
}

// "Penyesuaian Laba Ditahan Tahun Lalu" - hardcode 0 persis versi YTD
// (tidak ada logic untuk baris ini sama sekali di sumbernya).
$retainedEarningsValues = [];
foreach ($periods as $p) {
    foreach ($pcCols as $pcCol) {
        $retainedEarningsValues[$p['label']][$pcCol] = 0.0;
    }
}

// 3 section utama - kategori & filter disalin persis dari
// fs_ytd/cashflow_indirect.php ($sql2/$sql3/$sql4).
$sections = [
    'operasi' => [
        'kategori' => 'Arus Kas dari Aktivitas Operasi_ind',
        'filter' => '',
        'label' => 'ARUS KAS DARI AKTIVITAS OPERASI', 'labelEn' => 'CASH FLOW FROM OPERATING ACTIVITIES',
        'totalLabel' => 'ARUS KAS YANG DIGUNAKAN UNTUK AKTIVITAS OPERASI', 'totalLabelEn' => 'CASH FLOW USED FROM OPERATING ACTIVITIES',
    ],
    'investasi' => [
        'kategori' => 'Arus Kas dari Aktivitas Investasi_ind',
        'filter' => "where status = 'Active' and id >= 4",
        'label' => 'ARUS KAS DARI AKTIVITAS INVESTASI', 'labelEn' => 'CASH FLOW FROM INVESTING ACTIVITIES',
        'totalLabel' => 'ARUS KAS YANG DIGUNAKAN UNTUK AKTIVITAS INVESTASI', 'totalLabelEn' => 'CASH FLOW USED FROM INVESTING ACTIVITIES',
    ],
    'pendanaan' => [
        'kategori' => 'Arus Kas dari Aktivitas Pendanaan_ind',
        'filter' => '',
        'label' => 'ARUS KAS DARI AKTIVITAS PENDANAAN', 'labelEn' => 'CASH FLOW FROM FINANCING ACTIVITIES',
        'totalLabel' => 'ARUS KAS YANG DIPEROLEH DARI AKTIVITAS PENDANAAN', 'totalLabelEn' => 'CASH FLOW GENERATED FROM FINANCING ACTIVITIES',
    ],
];

foreach ($sections as $key => &$sec) {
    $monthData = [];
    foreach ($periods as $p) {
        $monthData[$p['label']] = fsGetCashflowIndirectCategoryTotals(
            $conn2, [$sec['kategori']], $bulan_awal, $tahun_awal, $p['bulan'], $p['tahun'], $sec['filter']
        );
    }

    // Kumpulkan sub_kategori yang muncul di bulan manapun dalam rentang,
    // biar barisnya konsisten walau nilainya 0 di sebagian bulan.
    $subList = [];
    foreach ($periods as $p) {
        foreach ($monthData[$p['label']] as $row) {
            if (!isset($subList[$row['sub_kategori']])) {
                $subList[$row['sub_kategori']] = $row['sub_kategori_eng'] ?? '';
            }
        }
    }

    $itemValuesBySub = []; // sub_kategori => [period_label][pcCol] => float
    $sectionTotal = []; // [period_label][pcCol] => float
    foreach ($subList as $subKategori => $subKategoriEn) {
        foreach ($periods as $p) {
            $row = null;
            foreach ($monthData[$p['label']] as $r) {
                if ($r['sub_kategori'] === $subKategori) {
                    $row = $r;
                    break;
                }
            }
            $row = $row ?? ['total_nag' => 0, 'total_nak' => 0, 'total_all' => 0];
            foreach ($pcCols as $pcCol) {
                $v = (float) ($row['total_' . strtolower($pcCol)] ?? 0);
                $itemValuesBySub[$subKategori][$p['label']][$pcCol] = $v;
                $sectionTotal[$p['label']][$pcCol] = ($sectionTotal[$p['label']][$pcCol] ?? 0) + $v;
            }
        }
    }

    $sec['subList'] = $subList;
    $sec['itemValuesBySub'] = $itemValuesBySub;
    $sec['sectionTotal'] = $sectionTotal;
}
unset($sec);

// "Kas dan Setara Kas pada Awal Periode" - konstan di semua kolom bulan
// (lihat catatan di fsGetCashflowBeginningCash()), dihitung sekali saja.
$beginningCash = fsGetCashflowBeginningCash($conn2, $kata_filter);
$beginningCashValues = [];
foreach ($periods as $p) {
    foreach ($pcCols as $pcCol) {
        $beginningCashValues[$p['label']][$pcCol] = $beginningCash['total_' . strtolower($pcCol)] ?? 0;
    }
}

// "Kenaikan / (Penurunan) Bersih Kas dan Setara Kas" - murni PHP, jumlah
// semua komponen di atas (persis rumus di fs_ytd/cashflow_indirect.php).
$netChangeValues = [];
foreach ($periods as $p) {
    foreach ($pcCols as $pcCol) {
        $netChangeValues[$p['label']][$pcCol] =
            ($netIncomeByPeriod[$p['label']][$pcCol] ?? 0)
            + ($depreciationValues[$p['label']][$pcCol] ?? 0)
            + ($retainedEarningsValues[$p['label']][$pcCol] ?? 0)
            + ($sections['operasi']['sectionTotal'][$p['label']][$pcCol] ?? 0)
            + ($sections['investasi']['sectionTotal'][$p['label']][$pcCol] ?? 0)
            + ($sections['pendanaan']['sectionTotal'][$p['label']][$pcCol] ?? 0);
    }
}

// "Kas dan Setara Kas pada Akhir Periode" - murni PHP.
$endingCashValues = [];
foreach ($periods as $p) {
    foreach ($pcCols as $pcCol) {
        $endingCashValues[$p['label']][$pcCol] = ($netChangeValues[$p['label']][$pcCol] ?? 0) + ($beginningCashValues[$p['label']][$pcCol] ?? 0);
    }
}
?>

<div class="table-responsive mt-1">
  <div class="laporan-outer">
    <div class="sfp-title-block">
      <div class="sfp-title-text">
        <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
        <div class="sfp-title-report">LAPORAN ARUS KAS - METODE TIDAK LANGSUNG</div>
        <div class="sfp-title-report-en">Statements of Cash Flow - Indirect Method</div>
        <div class="sfp-title-period"><?= htmlspecialchars($periods[0]['label']); ?> - <?= htmlspecialchars($lastPeriod['label']); ?></div>
        <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
        <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
      </div>
      <button type="button" id="btnExcel-cfindirect" class="sfpm-btn-export-excel">
        <svg class="sfpm-btn-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
          <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
          <path d="M2 7.3h16M7.2 2.5v15" stroke="#ffffff" stroke-width="1.1"/>
          <path d="M4.3 10.1l2.1 3.2M6.4 10.1l-2.1 3.2" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
        Export Excel
      </button>
    </div>
    <div class="sfp-header-clip">
      <table id="cfindirect-header-table" class="laporan-table sfpm-tbl" border="0" cellspacing="0" style="width:<?= sfpmTableWidth($valueColCount); ?>px">
        <?= sfpmColgroup($valueColCount); ?>
        <tr>
          <th rowspan="2" class="periode-desc sfpm-freeze-left">Description</th>
          <?php $monthIdx = 0; foreach ($periods as $p) : ?>
            <th colspan="<?= $pcColCount; ?>" class="judul-periode<?= $monthIdx++ % 2 === 0 ? ' sfpm-month-a' : ' sfpm-month-b'; ?> sfpm-month-start"><?= htmlspecialchars($p['label']); ?></th>
          <?php endforeach; ?>
          <th colspan="<?= $pcColCount; ?>" class="judul-periode sfpm-month-start sfpm-ytd">YTD</th>
        </tr>
        <tr>
          <?php $idx = 0; foreach ($periods as $p) : ?>
            <?php foreach ($pcCols as $pcCol) : ?>
              <th class="periode<?= sfpmColClass($idx++, $pcColCount); ?>"><?= htmlspecialchars($pcColLabels[$pcCol] ?? $pcCol); ?></th>
            <?php endforeach; ?>
          <?php endforeach; ?>
          <?php foreach ($pcCols as $pcCol) : ?>
              <th class="periode<?= sfpmColClass($idx++, $pcColCount); ?>"><?= htmlspecialchars($pcColLabels[$pcCol] ?? $pcCol); ?></th>
          <?php endforeach; ?>
        </tr>
      </table>
    </div>
    <div class="laporan-container">
      <table id="cfindirect-monthly-table" class="laporan-table sfpm-tbl" border="0" role="grid" cellspacing="0" style="width:<?= sfpmTableWidth($valueColCount); ?>px">
        <?= sfpmColgroup($valueColCount); ?>
        <?php
        echo '<tr data-en="Net Income (Loss)"><td class="item-left sfpm-freeze-left">Laba (Rugi) Bersih</td>'
           . sfpmRenderRowValues($netIncomeByPeriod, $periods, $pcCols, 'td', 'item-right')
           . '</tr>';

        echo '<tr data-en="Accumulated Depreciation Of Fixed Asset Adjustment"><td class="item-left sfpm-freeze-left">Penyesuaian Akumulasi Penyusutan Aset Tetap</td>'
           . sfpmRenderRowValues($depreciationValues, $periods, $pcCols, 'td', 'item-right')
           . '</tr>';

        echo '<tr data-en="Previous Year Retained Earning Adjustment"><td class="item-left sfpm-freeze-left">Penyesuaian Laba Ditahan Tahun Lalu</td>'
           . sfpmRenderRowValues($retainedEarningsValues, $periods, $pcCols, 'td', 'item-right')
           . '</tr>';

        sfpmSpacerRow($valueColCount, $pcColCount, 'spacer-small');

        foreach ($sections as $sec) {
            echo '<tr data-en="' . htmlspecialchars($sec['labelEn']) . '"><th class="subsection-left sfpm-freeze-left">' . htmlspecialchars($sec['label']) . '</th>'
               . sfpmBlankCells($valueColCount, $pcColCount)
               . '</tr>';

            foreach ($sec['subList'] as $subKategori => $subKategoriEn) {
                echo '<tr data-en="' . htmlspecialchars($subKategoriEn) . '"><td class="item-left sfpm-freeze-left">' . htmlspecialchars($subKategori) . '</td>'
                   . sfpmRenderRowValues($sec['itemValuesBySub'][$subKategori], $periods, $pcCols, 'td', 'item-right')
                   . '</tr>';
            }

            echo '<tr class="total-line" data-en="' . htmlspecialchars($sec['totalLabelEn']) . '"><th class="total-left sfpm-freeze-left">' . htmlspecialchars($sec['totalLabel']) . '</th>'
               . sfpmRenderRowValues($sec['sectionTotal'], $periods, $pcCols, 'td', 'total-right')
               . '</tr>';

            sfpmSpacerRow($valueColCount, $pcColCount, 'spacer-small');
        }

        echo '<tr class="grand-total" data-en="Net Increase / (Decrease) in Cash and Cash Equivalent"><th class="grand-left sfpm-freeze-left">Kenaikan / (Penurunan) Bersih Kas dan Setara Kas</th>'
           . sfpmRenderRowValues($netChangeValues, $periods, $pcCols, 'th', 'grand-right')
           . '</tr>';

        sfpmSpacerRow($valueColCount, $pcColCount, 'spacer-small');

        echo '<tr class="total-line" data-en="Cash and Cash Equivalent at The Beginning of Period"><th class="total-left sfpm-freeze-left">KAS DAN SETARA KAS PADA AWAL PERIODE</th>'
           . sfpmRenderRowValues($beginningCashValues, $periods, $pcCols, 'td', 'total-right')
           . '</tr>';

        echo '<tr class="grand-total" data-en="Cash and Cash Equivalent at The End of Period"><th class="grand-left sfpm-freeze-left">KAS DAN SETARA KAS PADA AKHIR PERIODE</th>'
           . sfpmRenderRowValues($endingCashValues, $periods, $pcCols, 'th', 'grand-right')
           . '</tr>';
        ?>
      </table>
    </div>
  </div>
</div>

<script>
  // Pola & alasan identik dengan statement_financial_position_monthly.php /
  // statement_profit_loss_monthly.php - cuma ID elemen yang beda
  // (cfindirect- bukan sfp-/spl-).
  (function () {
    var bodyWrapper = document.querySelector('#cf-indirect .laporan-container');
    var headerClip = document.querySelector('#cf-indirect .sfp-header-clip');
    if (!bodyWrapper || !headerClip) return;

    function syncHeaderGutter() {
      var scrollbarWidth = bodyWrapper.offsetWidth - bodyWrapper.clientWidth;
      headerClip.style.paddingRight = (25 + scrollbarWidth) + 'px';
    }

    bodyWrapper.addEventListener('scroll', function () {
      headerClip.scrollLeft = bodyWrapper.scrollLeft;
    });

    window.addEventListener('resize', syncHeaderGutter);
    window.updateCfIndirectHeaderOffset = syncHeaderGutter;
    syncHeaderGutter();
  })();
</script>
