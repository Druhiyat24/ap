<style>
#cf-direct .card-body {
  background: #f9fafb;
}
#cf-direct table {
  color: #333;
}
#cf-direct th, #cf-direct td {
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

/* Class-class di bawah ini SENGAJA sama persis dengan tab-tab FS1/FS2
   Monthly lain (lihat catatan di cashflow_indirect.php) - FS1 & FS2 tidak
   pernah tampil dalam request yang sama, jadi tidak ada risiko bentrok. ID
   elemen dibedakan prefix "fs1cfdirect-" demi kejelasan debug saja. */
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

#fs1cfdirect-header-table tr:last-child th {
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

#fs1cfdirect-monthly-table tr:has(td):hover td {
  background-color: rgba(37, 99, 235, 0.1);
}

#fs1cfdirect-monthly-table tr:has(td) > td {
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

<!-- ====== TAB CONTENT: CF DIRECT MONTHLY (FS1) ====== -->
<?php
require_once __DIR__ . '/fs1_monthly_functions.php';

// Pola query DISALIN APA ADANYA dari ekspor_cf_direct_monthly_fix.php
// (sudah berjalan & diverifikasi lewat Export Excel selama ini) - cuma
// dibungkus tampilan modern di sini, TIDAK ADA perubahan logic/angka.
// Termasuk bug label profit_center NAK->'NAG' pada deteksi Pinjaman
// (lihat catatan panjang di fs1mCfDirectLoanAccountsCte() /
// fs1_monthly_functions.php) - user SUDAH ditanya & memilih REPLIKASI APA
// ADANYA, bukan diperbaiki.

$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');
$months = fs1mGetMonths($start_date, $end_date);
$monthCount = count($months);
$lastMonth = end($months);

$monthlyData = [];
foreach ($months as $m) {
    $monthlyData[$m['label']] = fs1ComputeCfDirectMonth($conn2, $m['bulan'], $m['tahun'], $m['kata_filter']);
}

// Susun ke bentuk [row_label][month_label] => float, biar bisa dirender
// lewat fs1mRenderRow() yang generik.
$netChangeByMonth = [];
$beginningCashByMonth = [];
$endingCashByMonth = [];
$operatingByMonth = []; // [item_label][month_label] => float
$operatingTotalByMonth = [];
$investingByMonth = [];
$investingTotalByMonth = [];
$pendanaanByMonth = [];
$pendanaanTotalByMonth = [];

foreach ($months as $m) {
    $md = $monthlyData[$m['label']];
    $netChangeByMonth[$m['label']] = $md['net_change'];
    $beginningCashByMonth[$m['label']] = $md['beginning_cash'];
    $endingCashByMonth[$m['label']] = $md['ending_cash'];
    $operatingTotalByMonth[$m['label']] = $md['operating_total'];
    $investingTotalByMonth[$m['label']] = $md['investing_total'];
    $pendanaanTotalByMonth[$m['label']] = $md['pendanaan_total'];

    foreach ($md['operating'] as $label => $row) {
        $operatingByMonth[$label]['eng'] = $row['eng'];
        $operatingByMonth[$label]['values'][$m['label']] = $row['total'];
    }
    foreach ($md['investing'] as $label => $row) {
        $investingByMonth[$label]['eng'] = $row['eng'];
        $investingByMonth[$label]['values'][$m['label']] = $row['total'];
    }

    $pendanaanByMonth['Penerimaan Pinjaman']['eng'] = 'Proceeds from Bank Loan';
    $pendanaanByMonth['Penerimaan Pinjaman']['values'][$m['label']] = $md['penerimaan_pinjaman'];
    $pendanaanByMonth['Pembayaran Pinjaman']['eng'] = 'Payment of Bank Loan';
    $pendanaanByMonth['Pembayaran Pinjaman']['values'][$m['label']] = $md['pembayaran_pinjaman'];
}
?>

<div class="table-responsive mt-1">
  <div class="laporan-outer">
    <div class="sfp-title-block">
      <div class="sfp-title-text">
        <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
        <div class="sfp-title-report">LAPORAN ARUS KAS - METODE LANGSUNG</div>
        <div class="sfp-title-report-en">Statements of Cash Flow - Direct Method</div>
        <div class="sfp-title-period"><?= htmlspecialchars($months[0]['label']); ?> - <?= htmlspecialchars($lastMonth['label']); ?></div>
        <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
        <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
      </div>
      <button type="button" id="btnExcel-fs1cfdirect" class="sfpm-btn-export-excel">
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
      <table id="fs1cfdirect-header-table" class="laporan-table sfpm-tbl" border="0" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
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
      <table id="fs1cfdirect-monthly-table" class="laporan-table sfpm-tbl" border="0" role="grid" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
        <?= fs1mColgroup($monthCount); ?>
        <?php
        $sections = [
            ['label' => 'ARUS KAS DARI AKTIVITAS OPERASI', 'labelEn' => 'CASH FLOW FROM OPERATING ACTIVITIES', 'items' => $operatingByMonth, 'totalByMonth' => $operatingTotalByMonth, 'totalLabel' => 'ARUS KAS BERSIH DARI AKTIVITAS OPERASI', 'totalLabelEn' => 'NET CASH FLOW FROM OPERATING ACTIVITIES'],
            ['label' => 'ARUS KAS DARI AKTIVITAS INVESTASI', 'labelEn' => 'CASH FLOW FROM INVESTING ACTIVITIES', 'items' => $investingByMonth, 'totalByMonth' => $investingTotalByMonth, 'totalLabel' => 'ARUS KAS BERSIH DARI AKTIVITAS INVESTASI', 'totalLabelEn' => 'NET CASH FLOW FROM INVESTING ACTIVITIES'],
            ['label' => 'ARUS KAS DARI AKTIVITAS PENDANAAN', 'labelEn' => 'CASH FLOW FROM FINANCING ACTIVITIES', 'items' => $pendanaanByMonth, 'totalByMonth' => $pendanaanTotalByMonth, 'totalLabel' => 'ARUS KAS BERSIH DARI AKTIVITAS PENDANAAN', 'totalLabelEn' => 'NET CASH FLOW FROM FINANCING ACTIVITIES'],
        ];

        foreach ($sections as $sec) {
            echo '<tr data-en="' . htmlspecialchars($sec['labelEn']) . '"><th class="subsection-left sfpm-freeze-left">' . htmlspecialchars($sec['label']) . '</th>'
               . fs1mBlankCells($monthCount)
               . '</tr>';

            foreach ($sec['items'] as $itemLabel => $item) {
                echo '<tr data-en="' . htmlspecialchars($item['eng']) . '"><td class="item-left sfpm-freeze-left">' . htmlspecialchars($itemLabel) . '</td>'
                   . fs1mRenderRow($months, $item['values'], 'td', 'item-right')
                   . '</tr>';
            }

            echo '<tr class="total-line" data-en="' . htmlspecialchars($sec['totalLabelEn']) . '"><th class="total-left sfpm-freeze-left">' . htmlspecialchars($sec['totalLabel']) . '</th>'
               . fs1mRenderRow($months, $sec['totalByMonth'], 'td', 'total-right')
               . '</tr>';

            fs1mSpacerRow($monthCount, 'spacer-small');
        }

        echo '<tr class="grand-total" data-en="Net Increase / (Decrease) in Cash and Cash Equivalent"><th class="grand-left sfpm-freeze-left">Kenaikan / (Penurunan) Bersih Kas dan Setara Kas</th>'
           . fs1mRenderRow($months, $netChangeByMonth, 'th', 'grand-right')
           . '</tr>';

        fs1mSpacerRow($monthCount, 'spacer-small');

        // Kas Awal/Akhir Periode - kolom "Total" BUKAN jumlah, tapi nilai
        // bulan PERTAMA (Kas Awal) / bulan TERAKHIR (Kas Akhir) - persis
        // pola cashflow_indirect.php krn ini baris SALDO bukan ARUS.
        echo '<tr class="total-line" data-en="Cash and Cash Equivalent at The Beginning of Period"><th class="total-left sfpm-freeze-left">KAS DAN SETARA KAS PADA AWAL PERIODE</th>';
        $idx = 0;
        foreach ($months as $m) {
            echo '<td class="total-right' . fs1mColClass($idx++, $monthCount) . '">' . fsMonthlyFormatNumber($beginningCashByMonth[$m['label']]) . '</td>';
        }
        echo '<td class="total-right' . fs1mColClass($idx, $monthCount) . '">' . fsMonthlyFormatNumber($beginningCashByMonth[$months[0]['label']]) . '</td>';
        echo '</tr>';

        echo '<tr class="grand-total" data-en="Cash and Cash Equivalent at The End of Period"><th class="grand-left sfpm-freeze-left">KAS DAN SETARA KAS PADA AKHIR PERIODE</th>';
        $idx = 0;
        foreach ($months as $m) {
            echo '<th class="grand-right' . fs1mColClass($idx++, $monthCount) . '">' . fsMonthlyFormatNumber($endingCashByMonth[$m['label']]) . '</th>';
        }
        echo '<th class="grand-right' . fs1mColClass($idx, $monthCount) . '">' . fsMonthlyFormatNumber($endingCashByMonth[$lastMonth['label']]) . '</th>';
        echo '</tr>';
        ?>
      </table>
    </div>
  </div>
</div>

<script>
  (function () {
    var bodyWrapper = document.querySelector('#cf-direct .laporan-container');
    var headerClip = document.querySelector('#cf-direct .sfp-header-clip');
    if (!bodyWrapper || !headerClip) return;

    function syncHeaderGutter() {
      var scrollbarWidth = bodyWrapper.offsetWidth - bodyWrapper.clientWidth;
      headerClip.style.paddingRight = (25 + scrollbarWidth) + 'px';
    }

    bodyWrapper.addEventListener('scroll', function () {
      headerClip.scrollLeft = bodyWrapper.scrollLeft;
    });

    window.addEventListener('resize', syncHeaderGutter);
    window.updateCfDirectHeaderOffset = syncHeaderGutter;
    syncHeaderGutter();
  })();
</script>
