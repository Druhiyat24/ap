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
   JS (lihat script di financial_statement_monthly.php) dari lebar asli
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
  background-color: #2563EB;
  z-index: 3;
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

/* Kolom YTD dikasih warna beda biar menonjol dari kolom bulanan biasa. */
#table_tbmonthly .ytd-col {
  background-color: #eef4ff;
  font-weight: 600;
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
  <div class="mb-2">
    <?php
      $exportStart = urlencode($start_date);
      $exportEnd = urlencode($end_date);
      echo '
        <a target="_blank" href="ekspor_tb_monthly_fs2.php?start_date=' . $exportStart . '&end_date=' . $exportEnd . '&profit_center=' . urlencode($profit_center) . '">
          <button type="button" class="btn btn-success">
            <i class="fa fa-file-excel-o me-2"></i> Export Excel
          </button>
        </a>';
    ?>
  </div>

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
</form>
