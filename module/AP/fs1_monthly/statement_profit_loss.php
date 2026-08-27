<style>
#spl .card-body {
  background: #f9fafb;
}
#spl table {
  color: #333;
}
#spl th, #spl td {
  padding: 6px 10px;
}

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

.laporan-outer {
  border: 1px solid #dbe3f0;
  border-radius: 14px;
  background: #fafafa;
  box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
  overflow: hidden;
}

/* Header (baris judul kolom bulan) - overflow-x:scroll TAPI scrollbar-nya
   disembunyikan (digeser terprogram lewat JS, ikut .laporan-container di
   bawah). padding kiri/kanan 25px HARUS SAMA PERSIS dgn .laporan-container
   supaya kolom "Description" (freeze-left) di header & body align pixel-
   perfect - sebelumnya rule ini KETINGGALAN ke-copy (cuma ada di
   statement_financial_position_monthly.php punya FS2), bikin body table
   selalu geser 25px ke kanan dari header table. */
.sfp-header-clip {
  overflow-x: scroll;
  overflow-y: hidden;
  background: #fafafa;
  padding: 10px 25px 0;
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.sfp-header-clip::-webkit-scrollbar {
  display: none;
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

#fs1spl-header-table tr:last-child th {
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
.sfpm-tbl .periode.sfpm-ytd {
  color: #8a5a10;
}

#fs1spl-monthly-table tr:has(td):hover td {
  background-color: rgba(37, 99, 235, 0.1);
}

#fs1spl-monthly-table tr:has(td) > td {
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

<!-- ====== TAB CONTENT: SPL MONTHLY (FS1) ====== -->
<?php
require_once __DIR__ . '/fs1_monthly_functions.php';

$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');
$months = fs1mGetMonths($start_date, $end_date);
$monthCount = count($months);
$lastMonth = end($months);
$tahun_akhir = end($months)['tahun'];

// Blok berjenjang - kategori & label EN disalin persis dari
// ekspor_spl_monthly.php (5 blok, urutan & label sama persis dgn SPL
// Monthly punya FS2 - lihat catatan lengkap di fs1_monthly_functions.php
// bagian "BEBAN BUNGA" utk kategori yang query-nya beda sendiri).
$blocks = [
    [
        'kategori' => [
            ['PENJUALAN KOTOR', 'GROSS SALES'],
            ['RETURN PENJUALAN', 'SALES RETURN'],
            ['POTONGAN PENJUALAN', 'SALES DISCOUNT'],
        ],
        'runningLabel' => 'PENJUALAN BERSIH', 'runningLabelEn' => 'NET SALES',
    ],
    [
        'kategori' => [['BEBAN POKOK PENJUALAN', 'COST OF GOODS SOLD']],
        'runningLabel' => 'LABA / (RUGI) KOTOR', 'runningLabelEn' => 'GROSS PROFIT / (LOSS)',
    ],
    [
        'kategori' => [['BEBAN LAINNYA', 'OTHER EXPENSES']],
        'runningLabel' => 'LABA / (RUGI) SEBELUM BUNGA DAN PAJAK', 'runningLabelEn' => 'PROFIT / (LOSS) BEFORE INTEREST AND TAX',
    ],
    [
        'kategori' => [['BEBAN BUNGA', 'INTEREST EXPENSE']],
        'runningLabel' => 'LABA / (RUGI) SEBELUM PAJAK', 'runningLabelEn' => 'PROFIT / (LOSS) BEFORE TAX',
    ],
    [
        'kategori' => [['BEBAN PAJAK', 'TAX EXPENSE']],
        'runningLabel' => 'LABA / (RUGI) BERSIH', 'runningLabelEn' => 'NET INCOME / (LOSS)',
    ],
];

// Ambil semua data kategori sekali di awal - kategori "biasa" lewat
// fs1mGetCategoryTotals(), BEBAN BUNGA lewat fs1mGetBebanBunga() (query
// beda sendiri, lihat catatan di fs1_monthly_functions.php).
$categoryRows = []; // [kategori] => rows
foreach ($blocks as $block) {
    foreach ($block['kategori'] as $catInfo) {
        $kategori = $catInfo[0];
        $categoryRows[$kategori] = ($kategori === 'BEBAN BUNGA')
            ? fs1mGetBebanBunga($conn2, $tahun_akhir)
            : fs1mGetCategoryTotals($conn2, $kategori, $tahun_akhir);
    }
}
?>

<div class="table-responsive mt-1">
  <div class="laporan-outer">
    <div class="sfp-title-block">
      <div class="sfp-title-text">
        <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
        <div class="sfp-title-report">LAPORAN LABA ATAU RUGI DAN PENGHASILAN KOMPREHENSIF LAINNYA</div>
        <div class="sfp-title-report-en">Statements of Profit or Loss and Other Comprehensive Income</div>
        <div class="sfp-title-period"><?= htmlspecialchars($months[0]['label']); ?> - <?= htmlspecialchars($lastMonth['label']); ?></div>
        <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
        <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
      </div>
      <button type="button" id="btnExcel-fs1spl" class="sfpm-btn-export-excel">
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
      <table id="fs1spl-header-table" class="laporan-table sfpm-tbl" border="0" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
        <?= fs1mColgroup($monthCount); ?>
        <tr>
          <th class="periode-desc sfpm-freeze-left">Description</th>
          <?php $idx = 0; foreach ($months as $m) : ?>
            <th class="periode<?= fs1mColClass($idx++, $monthCount); ?>"><?= htmlspecialchars($m['label']); ?></th>
          <?php endforeach; ?>
          <th class="periode<?= fs1mColClass($idx, $monthCount); ?>">Total</th>
        </tr>
      </table>
    </div>
    <div class="laporan-container">
      <table id="fs1spl-monthly-table" class="laporan-table sfpm-tbl" border="0" role="grid" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
        <?= fs1mColgroup($monthCount); ?>
        <?php
        $runningByMonth = [];
        foreach ($months as $m) {
            $runningByMonth[$m['label']] = 0.0;
        }
        $blockCount = count($blocks);
        $blockIndex = 0;

        foreach ($blocks as $block) {
            $blockIndex++;
            $blockTotalByMonth = [];
            foreach ($months as $m) {
                $blockTotalByMonth[$m['label']] = 0.0;
            }

            foreach ($block['kategori'] as $catInfo) {
                [$kategori, $kategoriEn] = $catInfo;

                echo '<tr data-en="' . htmlspecialchars($kategoriEn) . '"><th class="subsection-left sfpm-freeze-left">' . htmlspecialchars($kategori) . '</th>'
                   . fs1mBlankCells($monthCount)
                   . '</tr>';

                $rows = $categoryRows[$kategori];
                foreach ($rows as $row) {
                    if (empty($row['sub_kategori'])) {
                        continue;
                    }
                    $itemByMonth = [];
                    foreach ($months as $m) {
                        $key = 'saldo_' . $m['bulan_key'];
                        $v = (float) ($row[$key] ?? 0);
                        $itemByMonth[$m['label']] = $v;
                        $blockTotalByMonth[$m['label']] += $v;
                    }
                    echo '<tr data-en="' . htmlspecialchars($row['sub_kategori_eng'] ?? '') . '"><td class="item-left sfpm-freeze-left">' . htmlspecialchars($row['sub_kategori']) . '</td>'
                       . fs1mRenderRow($months, $itemByMonth, 'td', 'item-right')
                       . '</tr>';
                }
            }

            foreach ($months as $m) {
                $runningByMonth[$m['label']] += $blockTotalByMonth[$m['label']];
            }

            echo '<tr class="grand-total" data-en="' . htmlspecialchars($block['runningLabelEn']) . '"><th class="grand-left sfpm-freeze-left">' . htmlspecialchars($block['runningLabel']) . '</th>'
               . fs1mRenderRow($months, $runningByMonth, 'th', 'grand-right')
               . '</tr>';

            if ($blockIndex !== $blockCount) {
                fs1mSpacerRow($monthCount);
            }
        }
        ?>
      </table>
    </div>
  </div>
</div>

<script>
  (function () {
    var bodyWrapper = document.querySelector('#spl .laporan-container');
    var headerClip = document.querySelector('#spl .sfp-header-clip');
    if (!bodyWrapper || !headerClip) return;

    function syncHeaderGutter() {
      var scrollbarWidth = bodyWrapper.offsetWidth - bodyWrapper.clientWidth;
      headerClip.style.paddingRight = (25 + scrollbarWidth) + 'px';
    }

    bodyWrapper.addEventListener('scroll', function () {
      headerClip.scrollLeft = bodyWrapper.scrollLeft;
    });

    window.addEventListener('resize', syncHeaderGutter);
    window.updateSplHeaderOffset = syncHeaderGutter;
    syncHeaderGutter();
  })();
</script>
