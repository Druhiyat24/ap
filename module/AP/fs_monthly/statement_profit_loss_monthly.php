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

/* Bungkus luar (border/radius/shadow) - overflow:hidden di sini yang
   "memotong" 2 kotak di dalamnya (header + body) supaya sudut membulatnya
   tetap rapi walau salah satu anaknya sendiri punya scrollbar. Class-class
   di bawah ini (laporan-outer, laporan-container, sfp-title-*, sfpm-*, dst)
   SENGAJA nama-nya sama persis dengan yang dipakai
   statement_financial_position_monthly.php - bukan salah copy, tapi
   supaya tampilan SPL identik dengan SFP tanpa perlu tulis ulang teori
   CSS-nya (lihat catatan lebar tabel & freeze scroll di situ untuk
   detail kenapa masing-masing aturan ada). ID elemen (bukan class) TETAP
   dibedakan (prefix spl- bukan sfp-) karena ID wajib unik satu halaman. */
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

/* ===== Subsections (kategori SPL - PENJUALAN KOTOR, BEBAN POKOK PENJUALAN, dkk) ===== */
.subsection-left {
  font-weight: bold;
  text-align: left;
  color: #1e3a8a;
  font-size: 13px;
  letter-spacing: .2px;
}

/* ===== Data Rows ===== */
.item-left {
  text-align: left;
  padding: 3px 0;
}

.item-right {
  text-align: right;
}

/* ===== Running total (per blok, kumulatif - NET SALES, GROSS PROFIT, dst) ===== */
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

/* ===== Spacers ===== */
.spacer {
  height: 15px;
}

.spacer-mid {
  height: 10px;
}

.spacer-small {
  height: 5px;
}

/* Freeze kolom paling kiri (deskripsi) - lihat catatan detail di
   statement_financial_position_monthly.php (kenapa pakai class eksplisit
   .sfpm-freeze-left, bukan :first-child). */
.sfpm-tbl .sfpm-freeze-left {
  position: sticky;
  left: 0;
  z-index: 2;
  background-color: #fafafa;
  box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.2);
}

#spl-header-table tr:last-child th {
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

#spl-monthly-table tr:has(td):hover td {
  background-color: rgba(37, 99, 235, 0.1);
}

#spl-monthly-table tr:has(td) > td {
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
$lastPeriod = end($periods);

$pcCols = ($profit_center === 'ALL') ? ['NAG', 'NAK', 'ALL'] : [$profit_center];
$pcColLabels = ['ALL' => 'Total'];

// Blok berjenjang persis fs_ytd/statement_profit_loss.php: tiap blok
// nambah 1-3 kategori ke running total sebelumnya. Semua kategori SPL pakai
// formula credit-normal (saldo + credit - debit). Label EN kategori & running
// total disalin persis dari fs_ytd/statement_profit_loss.php (section-right/
// grand-italic di file itu) supaya konsisten - kategori yang di YTD tidak
// punya judul terpisah (BEBAN LAINNYA/BEBAN BUNGA/BEBAN PAJAK) dikasih
// terjemahan wajar sendiri karena memang belum ada konvensi baku di sana.
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
        'runningLabel' => 'LABA RUGI KOTOR', 'runningLabelEn' => 'GROSS PROFIT / (LOSS)',
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

// Ambil data tiap kategori x tiap bulan sekali di awal.
$monthCategoryData = []; // [kategori][period_label] = rows
foreach ($periods as $p) {
    foreach ($blocks as $block) {
        foreach ($block['kategori'] as $catInfo) {
            $kategori = $catInfo[0];
            if (!isset($monthCategoryData[$kategori])) {
                $monthCategoryData[$kategori] = [];
            }
            $monthCategoryData[$kategori][$p['label']] = fsGetCategoryTotals(
                $conn2, [$kategori], $bulan_awal, $tahun_awal, $p['bulan'], $p['tahun'], $kata_filter, true
            );
        }
    }
}

// "PENJUALAN BERSIH" (Net Sales) - base 100% buat SEMUA kolom % di laporan
// ini, disalin dari fs_ytd/statement_profit_loss.php ($penjualan_bersih_*):
// base-nya SELALU Net Sales, tidak pernah berubah per section (baris
// BEBAN POKOK PENJUALAN dkk juga bagi ke Net Sales, bukan ke basis lain).
// Dihitung SEKALI di sini (bukan nunggu block pertama selesai di loop
// render di bawah) karena baris item block pertama sendiri (Penjualan
// Kotor dkk) JUGA butuh %-nya terhadap Net Sales.
$netSalesByPeriod = [];
foreach ($periods as $p) {
    foreach ($pcCols as $pcCol) {
        $netSalesByPeriod[$p['label']][$pcCol] = 0.0;
    }
    foreach (['PENJUALAN KOTOR', 'RETURN PENJUALAN', 'POTONGAN PENJUALAN'] as $kat) {
        foreach ($monthCategoryData[$kat][$p['label']] as $row) {
            foreach ($pcCols as $pcCol) {
                $netSalesByPeriod[$p['label']][$pcCol] += (float) ($row['total_' . strtolower($pcCol)] ?? 0);
            }
        }
    }
}

$pcColCount = count($pcCols);
// Tiap pcCol (NAG/NAK/Total) sekarang 2 kolom FISIK (Jumlah + %) - lihat
// fsGetCashflowIndirectCategoryTotals... eh maksudnya lihat catatan
// "Helper kolom persentase" di fs_monthly_functions.php. $pcColCount2 dipakai
// tiap kali butuh hitung offset/jumlah KOLOM FISIK (buat sfpmBlankCells,
// sfpmSpacerRow, sfpmColClass via sfpmRenderRowValuesWithPercent - semua
// sudah otomatis pakai groupSize x2 di dalamnya, tinggal $pcColCount2 yang
// dioper ke pemanggil generik yang belum tahu soal kolom %).
$pcColCount2 = $pcColCount * 2;
// +$pcColCount2 di akhir buat 1 grup kolom tambahan "YTD" di ujung kanan -
// lihat catatan lengkap di sfpmRenderRowValues()/sfpmColClass()
// (fs_monthly_functions.php) kenapa nilai bulan terakhir = YTD.
$valueColCount = count($periods) * $pcColCount2 + $pcColCount2;
$GLOBALS['sfpmYtdStartIdx'] = count($periods) * $pcColCount2;
?>

<div class="table-responsive mt-1">
  <div class="laporan-outer">
    <div class="sfp-title-block">
      <div class="sfp-title-text">
        <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
        <div class="sfp-title-report">LAPORAN LABA RUGI</div>
        <div class="sfp-title-report-en">Statements of Profit or Loss</div>
        <div class="sfp-title-period"><?= htmlspecialchars($periods[0]['label']); ?> - <?= htmlspecialchars($lastPeriod['label']); ?></div>
        <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
        <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
      </div>
      <button type="button" id="btnExcel-spl" class="sfpm-btn-export-excel">
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
      <table id="spl-header-table" class="laporan-table sfpm-tbl" border="0" cellspacing="0" style="width:<?= sfpmTableWidthWithPercent(count($periods), $pcColCount); ?>px">
        <?= sfpmColgroupWithPercent(count($periods), $pcColCount); ?>
        <tr>
          <th rowspan="2" class="periode-desc sfpm-freeze-left">Description</th>
          <?php $monthIdx = 0; foreach ($periods as $p) : ?>
            <th colspan="<?= $pcColCount2; ?>" class="judul-periode<?= $monthIdx++ % 2 === 0 ? ' sfpm-month-a' : ' sfpm-month-b'; ?> sfpm-month-start"><?= htmlspecialchars($p['label']); ?></th>
          <?php endforeach; ?>
          <th colspan="<?= $pcColCount2; ?>" class="judul-periode sfpm-month-start sfpm-ytd">YTD</th>
        </tr>
        <tr>
          <?php $idx = 0; foreach ($periods as $p) : ?>
            <?php foreach ($pcCols as $pcCol) : ?>
              <th colspan="2" class="periode<?= sfpmColClass($idx, $pcColCount2); ?>"><?= htmlspecialchars($pcColLabels[$pcCol] ?? $pcCol); ?></th>
              <?php $idx += 2; ?>
            <?php endforeach; ?>
          <?php endforeach; ?>
          <?php foreach ($pcCols as $pcCol) : ?>
              <th colspan="2" class="periode<?= sfpmColClass($idx, $pcColCount2); ?>"><?= htmlspecialchars($pcColLabels[$pcCol] ?? $pcCol); ?></th>
              <?php $idx += 2; ?>
          <?php endforeach; ?>
        </tr>
      </table>
    </div>
    <div class="laporan-container">
      <table id="spl-monthly-table" class="laporan-table sfpm-tbl" border="0" role="grid" cellspacing="0" style="width:<?= sfpmTableWidthWithPercent(count($periods), $pcColCount); ?>px">
        <?= sfpmColgroupWithPercent(count($periods), $pcColCount); ?>
        <?php
        $runningTotalPerMonth = []; // [period_label][pc] = running total sampai blok sebelumnya
        $blockCount = count($blocks);
        $blockIndex = 0;

        foreach ($blocks as $block) {
            $blockIndex++;
            $blockTotalPerMonth = [];

            foreach ($block['kategori'] as $catInfo) {
                [$kategori, $kategoriEn] = $catInfo;

                echo '<tr data-en="' . htmlspecialchars($kategoriEn) . '"><th class="subsection-left sfpm-freeze-left">' . htmlspecialchars($kategori) . '</th>'
                   . sfpmBlankCells($valueColCount, $pcColCount2)
                   . '</tr>';

                $subKategoriList = []; // sub_kategori => sub_kategori_eng
                foreach ($periods as $p) {
                    foreach ($monthCategoryData[$kategori][$p['label']] as $row) {
                        if (!isset($subKategoriList[$row['sub_kategori']])) {
                            $subKategoriList[$row['sub_kategori']] = $row['sub_kategori_eng'] ?? '';
                        }
                    }
                }

                foreach ($subKategoriList as $subKategori => $subKategoriEn) {
                    $itemValues = []; // [period_label][pcCol] = float
                    foreach ($periods as $p) {
                        $row = null;
                        foreach ($monthCategoryData[$kategori][$p['label']] as $r) {
                            if ($r['sub_kategori'] === $subKategori) {
                                $row = $r;
                                break;
                            }
                        }
                        $row = $row ?? ['total_nag' => 0, 'total_nak' => 0, 'total_all' => 0];
                        foreach ($pcCols as $pcCol) {
                            $v = (float) ($row['total_' . strtolower($pcCol)] ?? 0);
                            $itemValues[$p['label']][$pcCol] = $v;
                            $blockTotalPerMonth[$p['label']][$pcCol] = ($blockTotalPerMonth[$p['label']][$pcCol] ?? 0) + $v;
                        }
                    }
                    $itemPercent = [];
                    foreach ($periods as $p) {
                        foreach ($pcCols as $pcCol) {
                            $base = $netSalesByPeriod[$p['label']][$pcCol] ?? 0.0;
                            $itemPercent[$p['label']][$pcCol] = $base != 0.0 ? ($itemValues[$p['label']][$pcCol] / $base * 100) : 0.0;
                        }
                    }
                    echo '<tr data-en="' . htmlspecialchars($subKategoriEn) . '"><td class="item-left sfpm-freeze-left">' . htmlspecialchars($subKategori) . '</td>'
                       . sfpmRenderRowValuesWithPercent($itemValues, $itemPercent, $periods, $pcCols, 'td', 'item-right')
                       . '</tr>';
                }
            }

            // Running total kumulatif sampai blok ini (bukan cuma jumlah blok
            // ini sendiri - formula credit-normal di fsGetCategoryTotals()
            // sudah bikin tanda tiap kategori otomatis benar buat dijumlah
            // langsung, persis pola fs_ytd/statement_profit_loss.php).
            $runningRow = [];
            $runningPercent = [];
            foreach ($periods as $p) {
                foreach ($pcCols as $pcCol) {
                    $prev = $runningTotalPerMonth[$p['label']][$pcCol] ?? 0;
                    $add = $blockTotalPerMonth[$p['label']][$pcCol] ?? 0;
                    $runningTotalPerMonth[$p['label']][$pcCol] = $prev + $add;
                    $runningRow[$p['label']][$pcCol] = $runningTotalPerMonth[$p['label']][$pcCol];
                    $base = $netSalesByPeriod[$p['label']][$pcCol] ?? 0.0;
                    $runningPercent[$p['label']][$pcCol] = $base != 0.0 ? ($runningRow[$p['label']][$pcCol] / $base * 100) : 0.0;
                }
            }

            echo '<tr class="grand-total" data-en="' . htmlspecialchars($block['runningLabelEn']) . '"><th class="grand-left sfpm-freeze-left">' . htmlspecialchars($block['runningLabel']) . '</th>'
               . sfpmRenderRowValuesWithPercent($runningRow, $runningPercent, $periods, $pcCols, 'th', 'grand-right')
               . '</tr>';

            if ($blockIndex !== $blockCount) {
                sfpmSpacerRow($valueColCount, $pcColCount2);
            }
        }
        ?>
      </table>
    </div>
  </div>
</div>

<script>
  // Header (#spl-header-table) berdiri sendiri di luar area scroll - lihat
  // catatan lengkap di statement_financial_position_monthly.php (kenapa 2
  // tabel terpisah, kenapa overflow-x:scroll bukan hidden, kenapa width
  // eksplisit di <table>) - pola & alasannya identik, cuma ID elemen yang
  // beda (spl- bukan sfp-) karena tab ini hidup di halaman yang sama.
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
