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

/* Class-class di bawah ini SENGAJA sama persis dengan tab-tab FS2 Monthly
   (lihat catatan lengkap di statement_financial_position_monthly.php) -
   FS1 & FS2 tidak pernah tampil dalam request yang sama (dipilih exclusive
   lewat filter "Financial Statement"), jadi tidak ada risiko bentrok class/
   ID biarpun namanya sama. ID elemen tetap dibedakan prefix "fs1cfindirect-"
   demi kejelasan debug saja. */
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

#fs1cfindirect-header-table tr:last-child th {
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

#fs1cfindirect-monthly-table tr:has(td):hover td {
  background-color: rgba(37, 99, 235, 0.1);
}

#fs1cfindirect-monthly-table tr:has(td) > td {
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

<!-- ====== TAB CONTENT: CF INDIRECT MONTHLY (FS1) ====== -->
<?php
require_once __DIR__ . '/fs1_monthly_functions.php';

// Pola query DISALIN APA ADANYA dari ekspor_cf_indirect_monthly_fix.php
// (sudah berjalan & diverifikasi lewat Export Excel selama ini) - cuma
// dibungkus tampilan modern di sini, TIDAK ADA perubahan logic/angka.
// Termasuk override manual utk 'Jan_2023' (baris ~99-121 di file asal) -
// dipertahankan verbatim, bukan sesuatu yang perlu "diperbaiki".

function fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, $id_indirect) {
    $sql = mysqli_query($conn2, "select * from(select id_indirect,ind_name, ((sum(debit_idr)-sum(credit_idr)) * -1) total from (select * from (select no_coa coa_no, sum(ROUND(debit * rate,2)) debit_idr,sum(ROUND(credit * rate,2)) credit_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by id) a inner join
            (select no_coa,id_indirect from mastercoa_v2) b on b.no_coa = a.coa_no inner join
            (select id,ind_name from tbl_master_cashflow) c on c.id = b.id_indirect) a GROUP BY a.id_indirect) a where a.id_indirect = '$id_indirect'");
    $row = mysqli_fetch_array($sql);
    return isset($row['total']) ? $row['total'] : 0;
}

function fs1ComputeCfIndirectMonth($conn2, $bulan, $tahun, $kata_filter) {
    $bulan_awal = $bulan;
    $bulan_akhir = $bulan;
    $tahun_awal = $tahun;
    $tahun_akhir = $tahun;

    $sql7 = mysqli_query($conn2, "select indname1,sum(credit_idr - debit_idr) total from (select * from
(select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb order by no_coa asc) saldo
left join
(select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,id_ctg5 from mastercoa_v2 order by no_coa asc) coa
on coa.no_coa = saldo.nocoa
left join
(select a.id_ctg5 as id_ctg5A,a.ind_name as indname5,a.eng_name as engname5, b.ind_name as indname4,b.eng_name as engname4, c.ind_name as indname3,c.eng_name as engname3, d.ind_name as indname2,d.eng_name as engname2, e.ind_name as indname1,e.eng_name as engname1 from master_coa_ctg5 a INNER JOIN master_coa_ctg4 b on b.id_ctg4 = a.id_ctg4 INNER JOIN master_coa_ctg3 c on c.id_ctg3 = a.id_ctg3 INNER JOIN master_coa_ctg2 d on d.id_ctg2 = a.id_ctg2 INNER JOIN master_coa_ctg1 e on e.id_ctg1 = a.id_ctg1 GROUP BY a.id_ctg5) a on a.id_ctg5A =coa.id_ctg5
LEFT JOIN
(select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(ROUND(credit * rate,2)) credit_idr,sum(ROUND(debit * rate,2)) debit_idr,IF(sum(ROUND(debit * rate,2)) = sum(ROUND(credit * rate,2)),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a where a.indname1 = 'LAPORAN LABA RUGI'");

    $row7 = mysqli_fetch_array($sql7);
    $laba_rugi = isset($row7['total']) ? $row7['total'] : 0;

    $penyusutan = fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '19');
    $laba_ditahan = 0;

    $operating = [
        ['label' => 'Penurunan (Kenaikan) Piutang Dagang', 'eng' => 'Decrease / (Increase) Trade Receivable', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '20')],
        ['label' => 'Penurunan (Kenaikan) Piutang Lainnya', 'eng' => 'Decrease / (Increase) Other Receivable', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '21')],
        ['label' => 'Penurunan (Kenaikan) Persediaan', 'eng' => 'Decrease / (Increase) Inventory', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '22')],
        ['label' => 'Penurunan (Kenaikan) Biaya Dibayar Dimuka', 'eng' => 'Decrease / (Increase) Prepaid Expense', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '23')],
        ['label' => 'Penurunan (Kenaikan) Aset Lain-Lain', 'eng' => 'Decrease / (Increase) Other Asset', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '24')],
        ['label' => 'Kenaikan (Penurunan) Utang Dagang', 'eng' => 'Decrease / (Increase) Trade Payable', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '25')],
        ['label' => 'Kenaikan (Penurunan) Utang Lainnya', 'eng' => 'Decrease / (Increase) Other Payable', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '26')],
    ];
    $operating_total = 0;
    foreach ($operating as $row) {
        $operating_total += $row['total'];
    }

    $investing = [
        ['label' => 'Pembelian aset tetap', 'eng' => 'Acquisition of Fixed Asset', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '27')],
        ['label' => 'Aset Dalam Penyelesaian', 'eng' => 'Asset In Construction', 'total' => fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '28')],
    ];
    $investing_total = 0;
    foreach ($investing as $row) {
        $investing_total += $row['total'];
    }

    $total29_raw = fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '29');
    if ($kata_filter == 'Jan_2023') {
        $total29_display = 2910197113.18;
    } else {
        $total29_display = $total29_raw;
    }
    $total30 = fs1CfIndirectAmount($conn2, $bulan_awal, $bulan_akhir, $tahun_awal, $tahun_akhir, '30');

    $financing = [
        ['label' => 'Kenaikan (Penurunan) Utang Bank', 'eng' => 'Increase / (Decrease) Bank Loan', 'total' => $total29_display],
        ['label' => 'Kenaikan (Penurunan) Utang Direksi & Afiliasi', 'eng' => 'Increase / (Decrease) Shareholder & Affiliated Payable', 'total' => $total30],
    ];

    $totaljml6_raw = $total29_raw + $total30;
    if ($kata_filter == 'Jan_2023') {
        $financing_total = $totaljml6_raw - 49264896939.97;
    } else {
        $financing_total = $totaljml6_raw;
    }

    $net_change = $laba_rugi + $penyusutan + $laba_ditahan + $operating_total + $investing_total + $totaljml6_raw;
    if ($kata_filter == 'Jan_2023') {
        $net_change -= 49264896939.97;
    }

    $sql = mysqli_query($conn2, "select id_ctg2,id_ctg4,ind_categori4,saldo total,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4, sum(saldo) saldo, sum(debit_idr) debit, sum(credit_idr) credit,eng_categori4 from (select id_ctg2,id_ctg4,ind_categori4,eng_categori4,COALESCE(saldo,0) saldo,COALESCE(credit_idr,0) credit_idr,COALESCE(debit_idr,0) debit_idr from
            (select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa != '1.10.01' and no_coa != '1.10.02' UNION select no_coa nocoa,nama_coa namacoa,$kata_filter as saldo from saldo_awal_tb where no_coa = '1.10.01' and $kata_filter > 0 OR no_coa = '1.10.02' and $kata_filter > 0) saldo
            left join
            (select no_coa,nama_coa,'' beg_balance,ind_categori1,ind_categori2,ind_categori3,ind_categori4,eng_categori4,id_ctg4,id_ctg2 from mastercoa_v2 order by no_coa asc) coa
            on coa.no_coa = saldo.nocoa
            left join
            (select no_coa coa_no, sum(credit) credit,sum(debit) debit,IF(sum(debit) = sum(credit),'B','NB') balance,sum(credit_idr) credit_idr,sum(debit_idr) debit_idr,IF(sum(debit_idr) = sum(credit_idr),'B','NB') balance_idr from tbl_list_journal where tgl_journal BETWEEN (select tgl_awal from tbl_tgl_tb where bulan = '$bulan_awal' and tahun = '$tahun_awal') and (select tgl_akhir from tbl_tgl_tb where bulan = '$bulan_akhir' and tahun = '$tahun_akhir') group by no_coa)
            jnl on jnl.coa_no = coa.no_coa order by no_coa asc) a group by a.id_ctg4) a where a.id_ctg4 = '111'");

    $rowb = mysqli_fetch_array($sql);
    $beginning_cash = isset($rowb['total']) ? $rowb['total'] : 0;
    $ending_cash = $beginning_cash + $net_change;

    return [
        'laba_rugi' => $laba_rugi,
        'penyusutan' => $penyusutan,
        'laba_ditahan' => $laba_ditahan,
        'operating' => $operating,
        'operating_total' => $operating_total,
        'investing' => $investing,
        'investing_total' => $investing_total,
        'financing' => $financing,
        'financing_total' => $financing_total,
        'net_change' => $net_change,
        'beginning_cash' => $beginning_cash,
        'ending_cash' => $ending_cash,
    ];
}

$start_date = isset($_POST['start_date']) && $_POST['start_date'] !== '' ? $_POST['start_date'] : 'Jan ' . date('Y');
$end_date = isset($_POST['end_date']) && $_POST['end_date'] !== '' ? $_POST['end_date'] : date('M Y');
$months = fs1mGetMonths($start_date, $end_date);
$monthCount = count($months);
$lastMonth = end($months);

$monthlyData = [];
foreach ($months as $m) {
    $monthlyData[$m['label']] = fs1ComputeCfIndirectMonth($conn2, $m['bulan'], $m['tahun'], $m['kata_filter']);
}

// Susun ke bentuk [row_label][month_label] => float, biar bisa dirender
// lewat fs1mRenderRow() yang generik.
$labaRugiByMonth = [];
$penyusutanByMonth = [];
$labaDitahanByMonth = [];
$netChangeByMonth = [];
$beginningCashByMonth = [];
$endingCashByMonth = [];
$operatingByMonth = []; // [item_label][month_label] => float
$operatingTotalByMonth = [];
$investingByMonth = [];
$investingTotalByMonth = [];
$financingByMonth = [];
$financingTotalByMonth = [];

foreach ($months as $m) {
    $md = $monthlyData[$m['label']];
    $labaRugiByMonth[$m['label']] = $md['laba_rugi'];
    $penyusutanByMonth[$m['label']] = $md['penyusutan'];
    $labaDitahanByMonth[$m['label']] = $md['laba_ditahan'];
    $netChangeByMonth[$m['label']] = $md['net_change'];
    $beginningCashByMonth[$m['label']] = $md['beginning_cash'];
    $endingCashByMonth[$m['label']] = $md['ending_cash'];
    $operatingTotalByMonth[$m['label']] = $md['operating_total'];
    $investingTotalByMonth[$m['label']] = $md['investing_total'];
    $financingTotalByMonth[$m['label']] = $md['financing_total'];
    foreach ($md['operating'] as $row) {
        $operatingByMonth[$row['label']]['eng'] = $row['eng'];
        $operatingByMonth[$row['label']]['values'][$m['label']] = $row['total'];
    }
    foreach ($md['investing'] as $row) {
        $investingByMonth[$row['label']]['eng'] = $row['eng'];
        $investingByMonth[$row['label']]['values'][$m['label']] = $row['total'];
    }
    foreach ($md['financing'] as $row) {
        $financingByMonth[$row['label']]['eng'] = $row['eng'];
        $financingByMonth[$row['label']]['values'][$m['label']] = $row['total'];
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
        <div class="sfp-title-period"><?= htmlspecialchars($months[0]['label']); ?> - <?= htmlspecialchars($lastMonth['label']); ?></div>
        <div class="sfp-title-desc">(Dinyatakan dalam Rupiah, kecuali dinyatakan lain)</div>
        <div class="sfp-title-desc-en">Expressed in Rupiah, unless otherwise stated</div>
      </div>
      <button type="button" id="btnExcel-fs1cfindirect" class="sfpm-btn-export-excel">
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
      <table id="fs1cfindirect-header-table" class="laporan-table sfpm-tbl" border="0" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
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
      <table id="fs1cfindirect-monthly-table" class="laporan-table sfpm-tbl" border="0" role="grid" cellspacing="0" style="width:100%; min-width:<?= fs1mTableWidth($monthCount); ?>px">
        <?= fs1mColgroup($monthCount); ?>
        <?php
        echo '<tr data-en="Net Income (Loss)"><td class="item-left sfpm-freeze-left">Laba (Rugi) Bersih</td>'
           . fs1mRenderRow($months, $labaRugiByMonth, 'td', 'item-right')
           . '</tr>';
        echo '<tr data-en="Accumulated Depreciation Of Fixed Asset Adjustment"><td class="item-left sfpm-freeze-left">Penyesuaian Akumulasi Penyusutan Aset Tetap</td>'
           . fs1mRenderRow($months, $penyusutanByMonth, 'td', 'item-right')
           . '</tr>';
        echo '<tr data-en="Previous Year Retained Earning Adjustment"><td class="item-left sfpm-freeze-left">Penyesuaian Laba Ditahan Tahun Lalu</td>'
           . fs1mRenderRow($months, $labaDitahanByMonth, 'td', 'item-right')
           . '</tr>';

        fs1mSpacerRow($monthCount, 'spacer-small');

        $sections = [
            ['label' => 'ARUS KAS DARI AKTIVITAS OPERASI', 'labelEn' => 'CASH FLOW FROM OPERATING ACTIVITIES', 'items' => $operatingByMonth, 'totalByMonth' => $operatingTotalByMonth, 'totalLabel' => 'ARUS KAS YANG DIGUNAKAN UNTUK AKTIVITAS OPERASI', 'totalLabelEn' => 'CASH FLOW USED FROM OPERATING ACTIVITIES'],
            ['label' => 'ARUS KAS DARI AKTIVITAS INVESTASI', 'labelEn' => 'CASH FLOW FROM INVESTING ACTIVITIES', 'items' => $investingByMonth, 'totalByMonth' => $investingTotalByMonth, 'totalLabel' => 'ARUS KAS YANG DIPEROLEH DARI AKTIVITAS INVESTASI', 'totalLabelEn' => 'CASH FLOW GENERATED FROM INVESTING ACTIVITIES'],
            ['label' => 'ARUS KAS DARI AKTIVITAS PENDANAAN', 'labelEn' => 'CASH FLOW FROM FINANCING ACTIVITIES', 'items' => $financingByMonth, 'totalByMonth' => $financingTotalByMonth, 'totalLabel' => 'ARUS KAS YANG DIPEROLEH DARI AKTIVITAS PENDANAAN', 'totalLabelEn' => 'CASH FLOW GENERATED FROM FINANCING ACTIVITIES'],
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

        // Kas Awal/Akhir Periode - kolom "Total" di sini BUKAN jumlah (beda
        // dari baris lain), tapi nilai bulan PERTAMA (Kas Awal) / bulan
        // TERAKHIR (Kas Akhir) - persis ekspor_cf_indirect_monthly_fix.php
        // ($monthly_data[0]['beginning_cash'] / $monthly_data[count-1]['ending_cash']),
        // karena ini baris SALDO bukan baris ARUS/pergerakan.
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
