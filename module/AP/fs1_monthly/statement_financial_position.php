<style>
#sfp .card-body {
  background: #f9fafb;
}
#sfp table {
  color: #333;
}
#sfp th, #sfp td {
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

/* Class-class di bawah ini SENGAJA sama persis dengan tab-tab FS1 lain
   (lihat catatan di cashflow_indirect.php) - FS1 & FS2 tidak pernah tampil
   dalam request yang sama. ID elemen dibedakan prefix "fs1sfp-". */
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
   perfect - sebelumnya rule ini KETINGGALAN ke-copy (harusnya disalin dari
   statement_financial_position_monthly.php punya FS2 sejak awal), bikin
   body table selalu geser 25px ke kanan dari header table. */
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

#fs1sfp-header-table tr:last-child th {
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

#fs1sfp-monthly-table tr:has(td):hover td {
  background-color: rgba(37, 99, 235, 0.1);
}

#fs1sfp-monthly-table tr:has(td) > td {
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

<!-- ====== TAB CONTENT: SFP MONTHLY (FS1) ====== -->
<?php
require_once __DIR__ . '/fs1_monthly_functions.php';

// Pola query DISALIN APA ADANYA dari ekspor_sfp_monthly.php (sudah
// berjalan & diverifikasi lewat Export Excel selama ini) - cuma dibungkus
// tampilan modern di sini, TIDAK ADA perubahan logic/angka. Beda mesin
// dari SPL/CF: SFP adalah laporan POSISI (saldo neraca akhir bulan, bukan
// arus), jadi kolom terakhir = nilai BULAN TERAKHIR ("YTD", lihat
// fs1mRenderRowLastMonth() di fs1_monthly_functions.php), BUKAN sum.
// Subtotal/grand-total DIJUMLAH DI PHP dari line-item yang sudah diambil
// (matematis identik dgn sum() SQL sumber asli - lihat catatan panjang di
// fs1mGetSfpLineTotal()/fs1mGetSfpLabaTahunBerjalan()).

$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');
$months = fs1mGetMonths($start_date, $end_date);
$monthCount = count($months);
$lastMonth = end($months);
$tahun_akhir = $lastMonth['tahun'];

// id_ctg4 -> [label id, label eng] - urutan & label PERSIS
// ekspor_sfp_monthly.php (lihat riset di atas).
$lineDefs = [
    '111' => ['Kas dan bank', 'Cash on hand and in banks'],
    '113' => ['Piutang usaha', 'Trade receivables'],
    '114' => ['Piutang lain-lain', 'Other receivables'],
    '115' => ['Persediaan', 'Inventories'],
    '116' => ['Uang muka pembelian', 'Advances'],
    '117' => ['Biaya dibayar dimuka', 'Prepaid expenses'],
    '118' => ['Pajak dibayar dimuka', 'Prepaid taxes'],
    '112' => ['Investasi', 'Investment'],
    '121' => ['Aset tetap', 'Fixed assets'],
    '122' => ['Aset takberwujud', 'Intangible assets'],
    '129' => ['Aset lain-lain', 'Other assets'],
    '212' => ['Utang bank jangka pendek', 'Short-term bank loans'],
    '211' => ['Utang usaha', 'Trade payables'],
    '215' => ['Utang pajak', 'Taxes payables'],
    '214' => ['Biaya akrual', 'Accrued expenses'],
    '219' => ['Utang lain-lain', 'Other payables'],
    '213' => ['Uang Muka Penjualan', 'Deferred Revenue'],
    '221' => ['Utang bank jangka panjang', 'Long-term bank loans'],
    '311' => ['Modal saham', 'Capital Stock'],
    '312' => ['Tambahan Modal Disetor', 'Paid in Capital'],
    '318' => ['Saldo laba di tahan', 'Retained earnings'],
];

$lineByMonth = []; // [id_ctg4][month_label] => float
foreach ($lineDefs as $idCtg4 => $def) {
    $row = fs1mGetSfpLineTotal($conn2, $idCtg4, $tahun_akhir);
    $lineByMonth[$idCtg4] = fs1mSfpRowToByMonth($row, $months);
}
$labaRow = fs1mGetSfpLabaTahunBerjalan($conn2, $tahun_akhir);
$labaByMonth = fs1mSfpRowToByMonth($labaRow, $months);

$zeroByMonth = [];
foreach ($months as $m) {
    $zeroByMonth[$m['label']] = 0.0;
}

function fs1sfpSumIds($lineByMonth, $ids, $zeroByMonth) {
    $out = $zeroByMonth;
    foreach ($ids as $id) {
        $out = fs1mSfpAddByMonth($out, $lineByMonth[$id]);
    }
    return $out;
}

$asetLancarIds = ['111', '113', '114', '115', '116', '117', '118'];
$asetTidakLancarIds = ['112', '121', '122', '129'];
$liabJPendekIds = ['212', '211', '215', '214', '219', '213'];
$liabJPanjangIds = ['221'];
$ekuitasIds = ['311', '312', '318'];

$totalAsetLancar = fs1sfpSumIds($lineByMonth, $asetLancarIds, $zeroByMonth);
$totalAsetTidakLancar = fs1sfpSumIds($lineByMonth, $asetTidakLancarIds, $zeroByMonth);
$jumlahAset = fs1mSfpAddByMonth($totalAsetLancar, $totalAsetTidakLancar);

$totalLiabJPendek = fs1sfpSumIds($lineByMonth, $liabJPendekIds, $zeroByMonth);
$totalLiabJPanjang = fs1sfpSumIds($lineByMonth, $liabJPanjangIds, $zeroByMonth);
$totalEkuitas = fs1mSfpAddByMonth(fs1sfpSumIds($lineByMonth, $ekuitasIds, $zeroByMonth), $labaByMonth);
$jumlahLiabDanEkuitas = fs1mSfpAddByMonth(fs1mSfpAddByMonth($totalLiabJPendek, $totalLiabJPanjang), $totalEkuitas);
?>

<div class="table-responsive mt-1">
  <div class="laporan-outer">
    <div class="sfp-title-block">
      <div class="sfp-title-text">
        <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
        <div class="sfp-title-report">LAPORAN POSISI KEUANGAN</div>
        <div class="sfp-title-report-en">Statements of Financial Position</div>
        <div class="sfp-title-period"><?= htmlspecialchars($months[0]['label']); ?> - <?= htmlspecialchars($lastMonth['label']); ?></div>
        <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
        <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
      </div>
      <button type="button" id="btnExcel-fs1sfp" class="sfpm-btn-export-excel">
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
      <table id="fs1sfp-header-table" class="laporan-table sfpm-tbl" border="0" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
        <?= fs1mColgroup($monthCount); ?>
        <tr>
          <th class="periode-desc sfpm-freeze-left">Description</th>
          <?php $idx = 0; foreach ($months as $m) : ?>
            <th class="periode<?= fs1mColClass($idx++, $monthCount); ?>"><?= htmlspecialchars($m['label']); ?></th>
          <?php endforeach; ?>
          <th class="periode<?= fs1mColClass($idx, $monthCount); ?>">YTD</th>
        </tr>
      </table>
    </div>
    <div class="laporan-container">
      <table id="fs1sfp-monthly-table" class="laporan-table sfpm-tbl" border="0" role="grid" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
        <?= fs1mColgroup($monthCount); ?>
        <?php
        function fs1sfpItemRow($label, $eng, $valuesByMonth, $months) {
            echo '<tr data-en="' . htmlspecialchars($eng) . '"><td class="item-left sfpm-freeze-left">' . htmlspecialchars($label) . '</td>'
               . fs1mRenderRowLastMonth($months, $valuesByMonth, 'td', 'item-right')
               . '</tr>';
        }
        function fs1sfpSectionHeader($label, $eng, $monthCount) {
            echo '<tr data-en="' . htmlspecialchars($eng) . '"><th class="subsection-left sfpm-freeze-left">' . htmlspecialchars($label) . '</th>'
               . fs1mBlankCells($monthCount)
               . '</tr>';
        }
        function fs1sfpTotalRow($label, $eng, $valuesByMonth, $months) {
            echo '<tr class="total-line" data-en="' . htmlspecialchars($eng) . '"><th class="total-left sfpm-freeze-left">' . htmlspecialchars($label) . '</th>'
               . fs1mRenderRowLastMonth($months, $valuesByMonth, 'td', 'total-right')
               . '</tr>';
        }
        function fs1sfpGrandRow($label, $eng, $valuesByMonth, $months) {
            echo '<tr class="grand-total" data-en="' . htmlspecialchars($eng) . '"><th class="grand-left sfpm-freeze-left">' . htmlspecialchars($label) . '</th>'
               . fs1mRenderRowLastMonth($months, $valuesByMonth, 'th', 'grand-right')
               . '</tr>';
        }

        echo '<tr data-en="ASSETS"><th class="subsection-left sfpm-freeze-left">ASET</th>' . fs1mBlankCells($monthCount) . '</tr>';
        fs1sfpSectionHeader('ASET LANCAR', 'CURRENT ASSETS', $monthCount);
        foreach ($asetLancarIds as $id) {
            fs1sfpItemRow($lineDefs[$id][0], $lineDefs[$id][1], $lineByMonth[$id], $months);
        }
        fs1sfpTotalRow('Jumlah Aset Lancar', 'Total Current Assets', $totalAsetLancar, $months);
        fs1mSpacerRow($monthCount, 'spacer-small');

        fs1sfpSectionHeader('ASET TIDAK LANCAR', 'NONCURRENT ASSETS', $monthCount);
        fs1sfpItemRow('Investasi', 'Investment', $lineByMonth['112'], $months);
        fs1sfpItemRow('Investasi pada entitas anak', 'Investment in subsidiary', $zeroByMonth, $months);
        fs1sfpItemRow('Aset tetap', 'Fixed assets', $lineByMonth['121'], $months);
        fs1sfpItemRow('Aset takberwujud', 'Intangible assets', $lineByMonth['122'], $months);
        fs1sfpItemRow('Aset pajak tangguhan', 'Deferred tax assets', $zeroByMonth, $months);
        fs1sfpItemRow('Aset lain-lain', 'Other assets', $lineByMonth['129'], $months);
        fs1sfpTotalRow('Jumlah Aset Tidak Lancar', 'Total Noncurrent Assets', $totalAsetTidakLancar, $months);
        fs1mSpacerRow($monthCount, 'spacer-small');

        fs1sfpGrandRow('JUMLAH ASET', 'TOTAL ASSETS', $jumlahAset, $months);
        fs1mSpacerRow($monthCount, 'spacer');

        echo '<tr data-en="LIABILITIES AND EQUITY"><th class="subsection-left sfpm-freeze-left">LIABILITAS DAN EKUITAS</th>' . fs1mBlankCells($monthCount) . '</tr>';
        fs1sfpSectionHeader('LIABILITAS JANGKA PENDEK', 'CURRENT LIABILITIES', $monthCount);
        fs1sfpItemRow('Utang bank jangka pendek', 'Short-term bank loans', $lineByMonth['212'], $months);
        fs1sfpItemRow('Utang usaha', 'Trade payables', $lineByMonth['211'], $months);
        fs1sfpItemRow('Utang pajak', 'Taxes payables', $lineByMonth['215'], $months);
        fs1sfpItemRow('Biaya akrual', 'Accrued expenses', $lineByMonth['214'], $months);
        fs1sfpItemRow('Utang PPN', 'VAT Payable', $zeroByMonth, $months);
        fs1sfpItemRow('Utang lain-lain', 'Other payables', $lineByMonth['219'], $months);
        fs1sfpItemRow('Uang Muka Penjualan', 'Deferred Revenue', $lineByMonth['213'], $months);
        fs1sfpTotalRow('Jumlah Liabilitas Jangka Pendek', 'Total Current Liabilities', $totalLiabJPendek, $months);
        fs1mSpacerRow($monthCount, 'spacer-small');

        fs1sfpSectionHeader('LIABILITAS JANGKA PANJANG', 'NONCURRENT LIABILITIES', $monthCount);
        fs1sfpItemRow('Utang bank jangka panjang', 'Long-term bank loans', $lineByMonth['221'], $months);
        fs1sfpItemRow('Liabilitas imbalan pasca kerja', 'Post-employment benefits obligation', $zeroByMonth, $months);
        fs1sfpTotalRow('Jumlah Liabilitas Jangka Panjang', 'Total Noncurrent Liabilities', $totalLiabJPanjang, $months);
        fs1mSpacerRow($monthCount, 'spacer-small');

        fs1sfpSectionHeader('EKUITAS', 'EQUITY', $monthCount);
        fs1sfpItemRow('Modal saham', 'Capital Stock', $lineByMonth['311'], $months);
        fs1sfpItemRow('Tambahan Modal Disetor', 'Paid in Capital', $lineByMonth['312'], $months);
        fs1sfpItemRow('Saldo laba di tahan', 'Retained earnings', $lineByMonth['318'], $months);
        fs1sfpItemRow('Pendapatan Komprehensif Lain-lain', 'Other Comprehensive Income', $zeroByMonth, $months);
        fs1sfpItemRow('Laba Tahun Berjalan', 'Profit of the year', $labaByMonth, $months);
        fs1sfpTotalRow('Jumlah Ekuitas', 'Total Equity', $totalEkuitas, $months);
        fs1mSpacerRow($monthCount, 'spacer-small');

        fs1sfpGrandRow('JUMLAH LIABILITAS DAN EKUITAS', 'TOTAL LIABILITIES AND EQUITY', $jumlahLiabDanEkuitas, $months);
        ?>
      </table>
    </div>
  </div>
</div>

<script>
  (function () {
    var bodyWrapper = document.querySelector('#sfp .laporan-container');
    var headerClip = document.querySelector('#sfp .sfp-header-clip');
    if (!bodyWrapper || !headerClip) return;

    function syncHeaderGutter() {
      var scrollbarWidth = bodyWrapper.offsetWidth - bodyWrapper.clientWidth;
      headerClip.style.paddingRight = (25 + scrollbarWidth) + 'px';
    }

    bodyWrapper.addEventListener('scroll', function () {
      headerClip.scrollLeft = bodyWrapper.scrollLeft;
    });

    window.addEventListener('resize', syncHeaderGutter);
    window.updateSfpHeaderOffset = syncHeaderGutter;
    syncHeaderGutter();
  })();
</script>
