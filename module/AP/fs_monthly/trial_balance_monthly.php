<style>
  #myInput {
    border-radius: 6px;
    font-size: 13px;
    width: 200px;
  }

.dataTables_wrapper .dataTables_scrollHeadInner {
  width: auto !important;
}

.dataTables_wrapper .dataTables_scrollBody {
  width: auto !important;
  overflow-x: auto;
}

table.dataTable {
  width: 100%;
  min-width: max-content;
}

/* Kolom terasa dempet karena table-sm padding-nya kecil banget - dilebarin
   sedikit biar angka/teks ada jarak napas. */
#table_tbmonthly td, #table_tbmonthly th {
  padding: 8px 14px !important;
}

/* Freeze 6 kolom pertama (No COA s.d. Category 4) supaya tetap kelihatan
   waktu scroll ke samping. Lebar kolom DIBIARKAN NATURAL (tidak dipaksa px)
   supaya isi seperti "LAPORAN POSISI KEUANGAN" tidak kepotong - offset
   "left" tiap kolom makanya TIDAK di-hardcode di sini, tapi dihitung lewat
   JS (lihat script di financial_statement.php) dari lebar asli
   hasil render browser, jadi selalu presisi walau lebar kolom berubah-ubah.
   Header row KEDUA (baris NAG/NAK/ALL) sengaja TIDAK ikut disasar nth-child
   di sini karena rowspan bikin baris itu cuma punya <th> mulai dari kolom
   bulan pertama - kalau ikut disasar nth-child(1..6), yang kena malah sel
   NAG/NAK/ALL. */
#table_tbmonthly tbody td:nth-child(-n+6),
#table_tbmonthly thead tr:first-child th:nth-child(-n+6) {
  position: sticky;
  z-index: 2;
  background-color: #fff;
}
#table_tbmonthly thead tr:first-child th:nth-child(-n+6) {
  background: linear-gradient(180deg, #2f6fea, #2354c9);
  z-index: 3;
}
#table_tbmonthly thead th {
  font-weight: 600;
  letter-spacing: .2px;
}
#table_tbmonthly tbody td:nth-child(6),
#table_tbmonthly thead tr:first-child th:nth-child(6) {
  box-shadow: 2px 0 3px -1px rgba(0,0,0,0.25);
}

/* Hover baris - default bootstrap .table-hover ketutup sama background:#fff
   yang di-set khusus buat kolom freeze, jadi disamain manual di sini. */
#table_tbmonthly tbody tr:hover td:nth-child(-n+6) {
  background-color: #eaf2ff;
}
#table_tbmonthly tbody tr:hover td {
  background-color: rgba(37, 99, 235, 0.07);
}
#table_tbmonthly.table-striped tbody tr:nth-of-type(odd) td:nth-child(n+7) {
  background-color: #f8fafd;
}

/* Kolom YTD dikasih aksen keemasan - konsisten dgn kolom "Total"/"YTD" di
   tab SFP/SPL/CF Direct/CF Indirect. */
#table_tbmonthly .ytd-col {
  background-color: #fdf6e6 !important;
  border-left: 2px solid #c9962f;
  font-weight: 600;
}
#table_tbmonthly thead th.ytd-col,
#table_tbmonthly thead tr th[style*="1e3a8a"] {
  background: linear-gradient(180deg, #d9a53a, #b9822a) !important;
}

.tb-table-wrap {
  scrollbar-width: thin;
  scrollbar-color: #b7c3e0 #f1f4fa;
}
.tb-table-wrap::-webkit-scrollbar {
  height: 10px;
}
.tb-table-wrap::-webkit-scrollbar-track {
  background: #f1f4fa;
}
.tb-table-wrap::-webkit-scrollbar-thumb {
  background-color: #b7c3e0;
  border-radius: 8px;
  border: 2px solid #f1f4fa;
}

/* Title block + card wrapper - pola sama persis dgn tab SFP/SPL/CF Direct/
   CF Indirect (lihat catatan lengkap di fs1_monthly/trial_balance.php) -
   dulu halaman ini form polos tanpa card & tanpa judul laporan/periode. */
.tb-outer {
  border: 1px solid #dbe3f0;
  border-radius: 14px;
  background: #fafafa;
  box-shadow: 0 4px 18px rgba(30, 58, 138, 0.08);
  overflow: hidden;
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
  text-decoration: none;
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
  color: #fff;
}
.tb-table-wrap {
  padding: 0 20px 20px;
}
</style>

<!-- ====== TAB CONTENT: TRIAL BALANCE MONTHLY ====== -->
<?php
require_once __DIR__ . '/fs_monthly_functions.php';

$profit_center = isset($_POST['h_profit_center']) ? $_POST['h_profit_center'] : 'ALL';
$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');

// Beginning balance tiap kolom bulan selalu diambil dari saldo bulan "From"
// (dinamis ikut apa yang dipilih user, tidak dikunci ke Januari), persis
// seperti formula fs_ytd/trial_balance_ytd.php, cuma bulan_akhir yang berubah
// tiap kolom (jadi tiap kolom = angka kumulatif dari "From" sampai akhir
// bulan itu).
$bulan_awal = date('m', strtotime($start_date));
$tahun_awal = date('Y', strtotime($start_date));
$bulan_akhir = date('m', strtotime($end_date));
$tahun_akhir = date('Y', strtotime($end_date));
$kata_filter = date('M', strtotime($start_date)) . '_' . $tahun_awal;

$periods = fsMonthlyGetPeriods($bulan_awal, $tahun_awal, $bulan_akhir, $tahun_akhir);

// Kolom paling awal selalu 1 bulan persis sebelum "From" - dinamis ikut
// "From" yang dipilih (From = Feb 2026 -> kolom ini Jan 2026, bukan dikunci
// ke Desember), sama seperti kolom saldo awal di trial-balance-monthly.php
// (FS1 Monthly).
$prevPeriod = fsMonthlyGetPrevPeriod($bulan_awal, $tahun_awal);
$prevYearLabel = $prevPeriod['label'];

// Tiap bulan dipecah per profit center (NAG/NAK/ALL) tapi cuma tampilkan
// Ending Balance-nya saja - persis format trial-balance-monthly.php (FS1
// Monthly), bukan breakdown Beginning/Debit/Credit seperti tab Year To Date.
$pcCols = ($profit_center === 'ALL') ? ['NAG', 'NAK', 'ALL'] : [$profit_center];
$pcQueryList = ($profit_center === 'ALL') ? ['NAG', 'NAK'] : [$profit_center];
$pcLabel = [
    'NAG' => 'Nirwana Alabare Garment',
    'NAK' => 'Nirwana Alabare Knitting',
    'ALL' => 'Summary ALL',
];
?>

<form id="form-data-tbmonthly" method="post">
<div class="tb-outer mt-2">
  <div class="sfp-title-block">
    <div class="sfp-title-text">
      <div class="sfp-title-company">PT NIRWANA ALABARE GARMENT</div>
      <div class="sfp-title-report">NERACA SALDO</div>
      <div class="sfp-title-report-en">Trial Balance</div>
      <div class="sfp-title-period"><?= htmlspecialchars($start_date); ?> - <?= htmlspecialchars($end_date); ?></div>
      <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
      <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
    </div>
    <?php
      $exportStart = urlencode($start_date);
      $exportEnd = urlencode($end_date);
      echo '
        <a class="sfpm-btn-export-excel" target="_blank" href="ekspor_tb_monthly_fs2.php?start_date=' . $exportStart . '&end_date=' . $exportEnd . '&profit_center=' . urlencode($profit_center) . '">
          <svg class="sfpm-btn-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="2.5" width="16" height="15" rx="2" fill="#ffffff" fill-opacity=".15"/>
            <rect x="2" y="2.5" width="16" height="15" rx="2" stroke="#ffffff" stroke-width="1.1"/>
            <path d="M2 7.3h16M7.2 2.5v15" stroke="#ffffff" stroke-width="1.1"/>
            <path d="M4.3 10.1l2.1 3.2M6.4 10.1l-2.1 3.2" stroke="#ffffff" stroke-width="1.2" stroke-linecap="round"/>
          </svg>
          Export Excel
        </a>';
    ?>
  </div>

  <div class="tb-table-wrap">
    <table id="table_tbmonthly" class="table table-striped table-bordered table-hover table-sm nowrap">
      <?php $showPcSubHeader = count($pcCols) > 1; ?>
      <thead class="text-white" style="background-color: #2563EB;">
        <tr>
          <th rowspan="<?= $showPcSubHeader ? 2 : 1; ?>">No COA</th>
          <th rowspan="<?= $showPcSubHeader ? 2 : 1; ?>">COA Name</th>
          <th rowspan="<?= $showPcSubHeader ? 2 : 1; ?>">Category 1</th>
          <th rowspan="<?= $showPcSubHeader ? 2 : 1; ?>">Category 2</th>
          <th rowspan="<?= $showPcSubHeader ? 2 : 1; ?>">Category 3</th>
          <th rowspan="<?= $showPcSubHeader ? 2 : 1; ?>">Category 4</th>
          <th colspan="<?= count($pcCols); ?>"><?= htmlspecialchars($prevYearLabel); ?></th>
          <?php foreach ($periods as $p) : ?>
            <th colspan="<?= count($pcCols); ?>"><?= htmlspecialchars($p['label']); ?></th>
          <?php endforeach; ?>
          <th colspan="<?= count($pcCols); ?>" style="background-color:#1e3a8a;">YTD</th>
        </tr>
        <?php if ($showPcSubHeader) : ?>
        <tr>
          <?php foreach ($pcCols as $pcCol) : ?>
            <th><?= htmlspecialchars($pcCol); ?></th>
          <?php endforeach; ?>
          <?php foreach ($periods as $p) : ?>
            <?php foreach ($pcCols as $pcCol) : ?>
              <th><?= htmlspecialchars($pcCol); ?></th>
            <?php endforeach; ?>
          <?php endforeach; ?>
          <?php foreach ($pcCols as $pcCol) : ?>
            <th style="background-color:#1e3a8a;"><?= htmlspecialchars($pcCol); ?></th>
          <?php endforeach; ?>
        </tr>
        <?php endif; ?>
      </thead>
      <tbody>
        <?php
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

        // Ambil saldo akhir per COA untuk tiap kolom bulan - satu query per
        // bulan per profit center yang perlu ditampilkan (persis formula
        // saldo_akhir di fs_ytd/trial_balance_ytd.php, hanya bulan_akhir yang
        // berubah tiap iterasi).
        $monthData = []; // [period_label][pc][no_coa] = saldo_akhir

        // Kolom "Des <tahun-1>" - saldo awal tahun berjalan apa adanya, tanpa
        // pergerakan jurnal apapun (persis nilai fs_saldo_awal_tb['Jan_$tahun']).
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

        foreach ($coaList as $noCoa => $coa) {
            echo '<tr style="font-size:11px;text-align:center;">
                <td>' . htmlspecialchars($noCoa) . '</td>
                <td style="text-align:left;">' . htmlspecialchars($coa['nama_coa']) . '</td>
                <td style="text-align:left;">' . htmlspecialchars($coa['indname1']) . '</td>
                <td style="text-align:left;">' . htmlspecialchars($coa['indname2']) . '</td>
                <td style="text-align:left;">' . htmlspecialchars($coa['indname3']) . '</td>
                <td style="text-align:left;">' . htmlspecialchars($coa['indname4']) . '</td>';

            $columnLabels = array_merge([$prevYearLabel], array_column($periods, 'label'));
            foreach ($columnLabels as $colLabel) {
                if ($profit_center === 'ALL') {
                    $nagVal = $monthData[$colLabel]['NAG'][$noCoa] ?? 0;
                    $nakVal = $monthData[$colLabel]['NAK'][$noCoa] ?? 0;
                    echo '<td style="text-align:right;">' . fsMonthlyFormatNumber($nagVal) . '</td>';
                    echo '<td style="text-align:right;">' . fsMonthlyFormatNumber($nakVal) . '</td>';
                    echo '<td style="text-align:right;">' . fsMonthlyFormatNumber($nagVal + $nakVal) . '</td>';
                } else {
                    $val = $monthData[$colLabel][$profit_center][$noCoa] ?? 0;
                    echo '<td style="text-align:right;">' . fsMonthlyFormatNumber($val) . '</td>';
                }
            }

            // Kolom YTD - saldo akhir bulan TERAKHIR yang tampil (bukan
            // dijumlah), karena saldo tiap bulan sudah kumulatif sejak "From".
            $lastLabel = end($columnLabels);
            if ($profit_center === 'ALL') {
                $ytdNag = $monthData[$lastLabel]['NAG'][$noCoa] ?? 0;
                $ytdNak = $monthData[$lastLabel]['NAK'][$noCoa] ?? 0;
                echo '<td class="ytd-col" style="text-align:right;">' . fsMonthlyFormatNumber($ytdNag) . '</td>';
                echo '<td class="ytd-col" style="text-align:right;">' . fsMonthlyFormatNumber($ytdNak) . '</td>';
                echo '<td class="ytd-col" style="text-align:right;">' . fsMonthlyFormatNumber($ytdNag + $ytdNak) . '</td>';
            } else {
                $ytdVal = $monthData[$lastLabel][$profit_center][$noCoa] ?? 0;
                echo '<td class="ytd-col" style="text-align:right;">' . fsMonthlyFormatNumber($ytdVal) . '</td>';
            }

            echo '</tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
</form>
