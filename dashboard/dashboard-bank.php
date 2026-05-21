<?php
/* ── Bank Dashboard – data queries ─────────────────────── */
$bulan = date("M");
$tahun = date("Y");

// Cash on Hand
$q = mysqli_query($conn2,"select round(sum(saldo_$bulan),0) total from b_trial_balance_$tahun a INNER JOIN mastercoa_v2 b on b.no_coa=a.no_coa where ind_categori5='KAS' and saldo_$bulan>0");
$r = mysqli_fetch_array($q);
$total_coh = $r['total'] ?? 0;

// Cash in Bank
$q = mysqli_query($conn2,"select round(sum(saldo_$bulan),0) total from b_trial_balance_$tahun a INNER JOIN mastercoa_v2 b on b.no_coa=a.no_coa where ind_categori5='BANK' and saldo_$bulan>0");
$r = mysqli_fetch_array($q);
$total_cib = $r['total'] ?? 0;

$total_cb = $total_coh + $total_cib;

// Bank Loan IDR – running balance account 008-997-1979
$q = mysqli_query($conn1,"select amount from b_saldoawal_bank where account='008-997-1979'");
$r = mysqli_fetch_array($q);
$salawal_ = $r['amount'] ?? 0;

$q = mysqli_query($conn1,"select saldo_akhir saldoawal from (SELECT (@runnum:=@runnum+1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit,(@runtot:=@runtot+q1.debit-q1.credit) AS saldo_akhir FROM (select transaksi_date as date,no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun='008-997-1979' and transaksi_date<CURRENT_DATE() and status!='Cancel') AS q1 JOIN (SELECT @runtot:=$salawal_,@runnum:=0) runtot) a ORDER BY nomor desc limit 1");
$r = mysqli_fetch_array($q);
$saldoswal2_ = $r['saldoawal'] ?? 0;

$q = mysqli_query($conn1,"select date,saldo_akhir from (SELECT (@runnum:=@runnum+1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit,(@runtot:=@runtot+q1.debit-q1.credit) AS saldo_akhir FROM (select transaksi_date as date,no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun='008-997-1979' and transaksi_date between CURRENT_DATE() and CURRENT_DATE() and status!='Cancel') AS q1 JOIN (SELECT @runtot:=$saldoswal2_,@runnum:=0) runtot) a ORDER BY nomor desc limit 1");
$r = mysqli_fetch_array($q);
$total_bli = $r['saldo_akhir'] ?? $saldoswal2_;

// IDR facility limit
$q = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr='IDR'");
$r = mysqli_fetch_array($q);
$limit_idr = $r['fac_limit'] ?? 0;

// Bank Loan USD – running balance account 008-998-1982
$q = mysqli_query($conn1,"select amount from b_saldoawal_bank where account='008-998-1982'");
$r = mysqli_fetch_array($q);
$salawal = $r['amount'] ?? 0;

$q = mysqli_query($conn1,"select saldo_akhir saldoawal from (SELECT (@runnum:=@runnum+1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit,(@runtot:=@runtot+q1.debit-q1.credit) AS saldo_akhir FROM (select transaksi_date as date,no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun='008-998-1982' and transaksi_date<CURRENT_DATE() and status!='Cancel') AS q1 JOIN (SELECT @runtot:=$salawal,@runnum:=0) runtot) a ORDER BY nomor desc limit 1");
$r = mysqli_fetch_array($q);
$saldoswal2 = $r['saldoawal'] ?? 0;

$q = mysqli_query($conn1,"select date,saldo_akhir from (SELECT (@runnum:=@runnum+1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit,(@runtot:=@runtot+q1.debit-q1.credit) AS saldo_akhir FROM (select transaksi_date as date,no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun='008-998-1982' and transaksi_date between CURRENT_DATE() and CURRENT_DATE() and status!='Cancel') AS q1 JOIN (SELECT @runtot:=$saldoswal2,@runnum:=0) runtot) a ORDER BY nomor desc limit 1");
$r = mysqli_fetch_array($q);
$usd_akhir = $r['saldo_akhir'] ?? $saldoswal2;
$dateakhir = $r['date'] ?? null;
$saldoakhir = $usd_akhir > 0 ? 0 : $usd_akhir;

// USD exchange rate
$q = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr='PAJAK' and tanggal='$dateakhir'");
$r = mysqli_fetch_array($q);
if ($r && ($r['id'] ?? null)) {
    $rates3 = $r['rate'];
} else {
    $q = mysqli_query($conn1,"select max(id) id FROM masterrate where v_codecurr='PAJAK'");
    $r = mysqli_fetch_array($q);
    $maxid = $r['id'] ?? null;
    $q = mysqli_query($conn1,"select ROUND(rate,2) rate FROM masterrate where id='$maxid' and v_codecurr='PAJAK'");
    $r = mysqli_fetch_array($q);
    $rates3 = $r['rate'] ?? 1;
}

// USD facility limit
$q = mysqli_query($conn2,"select fac_limit,(fac_limit*rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr='usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal=CURRENT_DATE() and v_codecurr='PAJAK') b");
$r = mysqli_fetch_array($q);
$fac_limit     = $r['fac_limit']     ?? 0;
$limit_convert = $r['limit_convert'] ?? 0;

// Totals
$total_bl    = abs($total_bli) + abs($saldoakhir * $rates3);
$total_limit = $limit_idr + $limit_convert;

// Gauge utilization %
$chart_bli = $limit_idr     > 0 ? round((abs($total_bli) / $limit_idr) * 100, 2)                    : 0;
$chart_blu = $limit_convert > 0 ? round((abs($saldoakhir * $rates3) / $limit_convert) * 100, 2)     : 0;
$chart_bl  = $total_limit   > 0 ? round($total_bl / $total_limit * 100, 2)                          : 0;

// Monthly labels for bar charts
$q = mysqli_query($conn2,"WITH RECURSIVE bln AS (SELECT 1 AS m UNION ALL SELECT m+1 FROM bln WHERE m<12) SELECT GROUP_CONCAT(CONCAT('''',DATE_FORMAT(DATE(CONCAT(YEAR(CURDATE()),'-',m,'-01')),'%b %Y'),'''') ORDER BY m) AS nama FROM bln");
$r = mysqli_fetch_array($q);
$monthly_lbl_bank = $r['nama'] ?? '';

// Monthly cash data (12 months)
function _cash_m($conn, $tahun, $filter) {
    $w = $filter === 'BOTH' ? "IN ('BANK','KAS')" : "='$filter'";
    $q = mysqli_query($conn,"SELECT CONCAT_WS(',',ROUND(SUM(IF(saldo_jan>0,saldo_jan,0))/1000000,2),ROUND(SUM(IF(saldo_feb>0,saldo_feb,0))/1000000,2),ROUND(SUM(IF(saldo_mar>0,saldo_mar,0))/1000000,2),ROUND(SUM(IF(saldo_apr>0,saldo_apr,0))/1000000,2),ROUND(SUM(IF(saldo_may>0,saldo_may,0))/1000000,2),ROUND(SUM(IF(saldo_jun>0,saldo_jun,0))/1000000,2),ROUND(SUM(IF(saldo_jul>0,saldo_jul,0))/1000000,2),ROUND(SUM(IF(saldo_aug>0,saldo_aug,0))/1000000,2),ROUND(SUM(IF(saldo_sep>0,saldo_sep,0))/1000000,2),ROUND(SUM(IF(saldo_oct>0,saldo_oct,0))/1000000,2),ROUND(SUM(IF(saldo_nov>0,saldo_nov,0))/1000000,2),ROUND(SUM(IF(saldo_dec>0,saldo_dec,0))/1000000,2)) AS data FROM b_trial_balance_$tahun a INNER JOIN mastercoa_v2 b ON b.no_coa=a.no_coa WHERE ind_categori5 $w");
    $r = mysqli_fetch_array($q);
    return $r['data'] ?? '0';
}
$data_coh_m   = _cash_m($conn2, $tahun, 'KAS');
$data_cib_m   = _cash_m($conn2, $tahun, 'BANK');
$data_total_m = _cash_m($conn2, $tahun, 'BOTH');

// Last 3 months loan data
function _loan_3m($conn, $coa_loan, $coa_od) {
    $li = is_array($coa_loan) ? "'".implode("','",$coa_loan)."'" : "'$coa_loan'";
    $oi = is_array($coa_od)   ? "'".implode("','",$coa_od)."'"   : "'$coa_od'";
    $data = [];
    for ($i = 3; $i >= 1; $i--) {
        $d = strtotime("-$i month");
        $bln = date('M', $d); $yr = date('Y', $d);
        $q = mysqli_query($conn,"SELECT ROUND(ABS(SUM(IF(no_coa IN ($li),saldo_$bln,0))+SUM(IF(no_coa IN ($oi) AND saldo_$bln<0,saldo_$bln,0)))/1000000,2) total FROM b_trial_balance_$yr");
        $r = mysqli_fetch_assoc($q);
        $data[] = $r['total'] ?? 0;
    }
    return implode(',', $data);
}
$loan_3m_idr   = _loan_3m($conn2, '2.20.01', '1.10.01');
$loan_3m_usd   = _loan_3m($conn2, '2.20.02', '1.10.02');
$loan_3m_total = _loan_3m($conn2, ['2.20.01','2.20.02'], ['1.10.01','1.10.02']);
$cat_3m = implode(',', ["'".date('M Y', strtotime('-3 month'))."'","'".date('M Y', strtotime('-2 month'))."'","'".date('M Y', strtotime('-1 month'))."'"]);

if (!function_exists('fmtb')) {
    function fmtb($v) { return 'IDR '.number_format((float)$v, 0, ',', '.'); }
}

// gauge color helper
function gc($pct) { return $pct > 75 ? '#ef4444' : ($pct > 50 ? '#f59e0b' : '#10b981'); }
?>

<style>
.bank-gauge { min-height: 200px; }
.bank-3mo   { min-height: 160px; }
</style>

<!-- ── Cash Position ──────────────────────────────────────── -->
<div class="dsb-section"><i class="fas fa-wallet"></i> Cash Position &mdash; <?= date('M Y') ?></div>
<div class="row mb-2">
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="dsb-info" style="cursor:pointer;" onclick="$('#modalcoh').modal('show')">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#10b981,#047857);">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Cash on Hand</div>
                <div class="dsb-info-value"><?= fmtb($total_coh) ?></div>
                <div class="dsb-info-sub"><i class="fas fa-chart-bar"></i> Click for monthly trend</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="dsb-info" style="cursor:pointer;" onclick="$('#modalcib').modal('show')">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#1e88e5,#1565c0);">
                <i class="fas fa-university"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Cash in Bank</div>
                <div class="dsb-info-value"><?= fmtb($total_cib) ?></div>
                <div class="dsb-info-sub"><i class="fas fa-chart-bar"></i> Click for monthly trend</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 mb-3">
        <div class="dsb-info" style="cursor:pointer;" onclick="$('#modaltc').modal('show')">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#3949ab,#283593);">
                <i class="fas fa-coins"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Total Cash &amp; Bank</div>
                <div class="dsb-info-value"><?= fmtb($total_cb) ?></div>
                <div class="dsb-info-sub"><i class="fas fa-chart-bar"></i> Click for monthly trend</div>
            </div>
        </div>
    </div>
</div>

<!-- ── Bank Loan Utilization ──────────────────────────────── -->
<div class="dsb-section"><i class="fas fa-landmark"></i> Bank Loan Utilization</div>
<div class="row">

    <!-- IDR Loan -->
    <div class="col-md-4 mb-3">
        <div class="dsb-info mb-3">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Bank Loan IDR</div>
                <div class="dsb-info-value"><?= fmtb(abs($total_bli)) ?></div>
                <div class="dsb-info-sub">Limit: <?= fmtb($limit_idr) ?></div>
            </div>
        </div>
        <div class="dsb-chart-card mb-3">
            <div class="dsb-chart-header">
                <i class="fas fa-tachometer-alt"></i> IDR Utilization
                <span class="dsb-badge <?= $chart_bli > 75 ? 'dsb-badge-down' : 'dsb-badge-up' ?>" style="margin-left:auto;"><?= round($chart_bli,1) ?>%</span>
            </div>
            <div class="dsb-chart-body">
                <div id="chartdiv" class="bank-gauge"></div>
            </div>
        </div>
        <div class="dsb-chart-card">
            <div class="dsb-chart-header">
                <i class="fas fa-chart-bar"></i> Last 3 Months (IDR Mio)
                <span style="margin-left:auto;cursor:pointer;color:#3949ab;font-size:11px;" onclick="$('#modalloanidr').modal('show')">
                    <i class="fas fa-expand-alt"></i>
                </span>
            </div>
            <div class="dsb-chart-body">
                <div id="chartdiv2" class="bank-3mo"></div>
            </div>
        </div>
    </div>

    <!-- USD Loan -->
    <div class="col-md-4 mb-3">
        <div class="dsb-info mb-3">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#f59e0b,#b45309);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Bank Loan USD</div>
                <div class="dsb-info-value">USD <?= number_format(abs($saldoakhir),2) ?></div>
                <div class="dsb-info-sub"><?= fmtb(abs($saldoakhir * $rates3)) ?> &nbsp;&middot;&nbsp; Limit: USD <?= number_format($fac_limit,0) ?></div>
            </div>
        </div>
        <div class="dsb-chart-card mb-3">
            <div class="dsb-chart-header">
                <i class="fas fa-tachometer-alt"></i> USD Utilization
                <span class="dsb-badge <?= $chart_blu > 75 ? 'dsb-badge-down' : 'dsb-badge-up' ?>" style="margin-left:auto;"><?= round($chart_blu,1) ?>%</span>
            </div>
            <div class="dsb-chart-body">
                <div id="chartdiv3" class="bank-gauge"></div>
            </div>
        </div>
        <div class="dsb-chart-card">
            <div class="dsb-chart-header">
                <i class="fas fa-chart-bar"></i> Last 3 Months (IDR Mio)
                <span style="margin-left:auto;cursor:pointer;color:#3949ab;font-size:11px;" onclick="$('#modalloanusd').modal('show')">
                    <i class="fas fa-expand-alt"></i>
                </span>
            </div>
            <div class="dsb-chart-body">
                <div id="chartdiv4" class="bank-3mo"></div>
            </div>
        </div>
    </div>

    <!-- Total Loan -->
    <div class="col-md-4 mb-3">
        <div class="dsb-info mb-3">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Bank Loan Total</div>
                <div class="dsb-info-value"><?= fmtb($total_bl) ?></div>
                <div class="dsb-info-sub">Total Limit: <?= fmtb($total_limit) ?></div>
            </div>
        </div>
        <div class="dsb-chart-card mb-3">
            <div class="dsb-chart-header">
                <i class="fas fa-tachometer-alt"></i> Total Utilization
                <span class="dsb-badge <?= $chart_bl > 75 ? 'dsb-badge-down' : 'dsb-badge-up' ?>" style="margin-left:auto;"><?= round($chart_bl,1) ?>%</span>
            </div>
            <div class="dsb-chart-body">
                <div id="chartdiv5" class="bank-gauge"></div>
            </div>
        </div>
        <div class="dsb-chart-card">
            <div class="dsb-chart-header">
                <i class="fas fa-chart-bar"></i> Last 3 Months (IDR Mio)
                <span style="margin-left:auto;cursor:pointer;color:#3949ab;font-size:11px;" onclick="$('#modalloantotal').modal('show')">
                    <i class="fas fa-expand-alt"></i>
                </span>
            </div>
            <div class="dsb-chart-body">
                <div id="chartdiv6" class="bank-3mo"></div>
            </div>
        </div>
    </div>
</div>

<!-- ── Modals: Loan History ──────────────────────────────── -->
<?php foreach ([
    ['modalloanidr',   'IDR Loan Balance — Month over Month',   'chartloanidr',   '#ef4444'],
    ['modalloanusd',   'USD Loan Balance — Month over Month',   'chartloanusd',   '#f59e0b'],
    ['modalloantotal', 'Total Loan Balance — Month over Month', 'chartloantotal', '#7c3aed'],
] as [$mid, $title, $cid, $col]): ?>
<div class="modal fade" id="<?= $mid ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#283593,#3949ab);color:#fff;padding:14px 20px;border:none;">
                <h5 class="modal-title" style="font-size:14px;font-weight:700;"><?= $title ?></h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div id="<?= $cid ?>" style="min-height:300px;"></div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ── Modals: Cash Monthly Trend ───────────────────────── -->
<?php foreach ([
    ['modalcoh', 'Cash on Hand — Month over Month (Mio IDR)', 'chartcoh', '#10b981'],
    ['modalcib', 'Cash in Bank — Month over Month (Mio IDR)', 'chartcib', '#1e88e5'],
    ['modaltc',  'Total Cash — Month over Month (Mio IDR)',   'charttc',  '#3949ab'],
] as [$mid, $title, $cid, $col]): ?>
<div class="modal fade" id="<?= $mid ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,<?= $col ?>,<?= $col ?>cc);color:#fff;padding:14px 20px;border:none;">
                <h5 class="modal-title" style="font-size:14px;font-weight:700;"><?= $title ?></h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.8;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div id="<?= $cid ?>" style="min-height:300px;"></div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- ── Modals: Detail Drill-down ────────────────────────── -->
<div class="modal fade" id="modaldetcoh" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
            <div class="modal-header" style="padding:12px 18px;border-bottom:1px solid #e2e8f0;">
                <h6 class="modal-title" id="jdl_coh" style="font-weight:700;"></h6>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="padding:12px;">
                <div id="detail_coh"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modaldetcib" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
            <div class="modal-header" style="padding:12px 18px;border-bottom:1px solid #e2e8f0;">
                <h6 class="modal-title" id="jdl_cib" style="font-weight:700;"></h6>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="padding:12px;">
                <div id="detail_cib"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modaldettc" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px;border:none;overflow:hidden;">
            <div class="modal-header" style="padding:12px 18px;border-bottom:1px solid #e2e8f0;">
                <h6 class="modal-title" id="jdl_tc" style="font-weight:700;"></h6>
                <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="padding:12px;">
                <div id="detail_tc"></div>
            </div>
        </div>
    </div>
</div>

<script>
/* ── JS modal openers (backward-compat) ── */
function openmodalloanidr()   { $('#modalloanidr').modal('show'); }
function openmodalloanusd()   { $('#modalloanusd').modal('show'); }
function openmodalloantotal() { $('#modalloantotal').modal('show'); }
function openmodalcoh()       { $('#modalcoh').modal('show'); }
function openmodalcib()       { $('#modalcib').modal('show'); }
function openmodaltc()        { $('#modaltc').modal('show'); }

/* ── Gauge chart helper (ApexCharts radialBar) ── */
function renderGauge(id, value, color) {
    new ApexCharts(document.querySelector('#' + id), {
        series: [value],
        chart: { type: 'radialBar', height: 200, toolbar: { show: false },
                 sparkline: { enabled: true } },
        plotOptions: {
            radialBar: {
                startAngle: -135, endAngle: 135,
                hollow: { size: '55%', background: 'transparent' },
                track: { background: '#e2e8f0', strokeWidth: '90%', margin: 4 },
                dataLabels: {
                    name: { show: false },
                    value: { fontSize: '22px', fontWeight: 700, offsetY: 8,
                             formatter: function(v) { return v + '%'; }, color: '#2d3748' }
                }
            }
        },
        fill: { type: 'gradient', gradient: {
            shade: 'dark', type: 'horizontal',
            gradientToColors: [color],
            stops: [0, 100]
        }},
        colors: [color],
        stroke: { lineCap: 'round' }
    }).render();
}

renderGauge('chartdiv',  <?= $chart_bli ?>, '<?= gc($chart_bli) ?>');
renderGauge('chartdiv3', <?= $chart_blu ?>, '<?= gc($chart_blu) ?>');
renderGauge('chartdiv5', <?= $chart_bl  ?>, '<?= gc($chart_bl)  ?>');

/* ── 3-month loan bar helper ── */
function renderBar3m(id, data, cats, color) {
    new ApexCharts(document.querySelector('#' + id), {
        series: [{ name: 'Loan (Mio IDR)', data: data }],
        chart: { type: 'bar', height: 160, toolbar: { show: false },
                 animations: { enabled: true, speed: 600 } },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%',
            dataLabels: { position: 'top' } } },
        colors: [color],
        dataLabels: { enabled: true, formatter: function(v) { return v.toLocaleString('id-ID'); },
            offsetY: -18, style: { fontSize: '10px', colors: ['#2d3748'] } },
        xaxis: { categories: cats, labels: { style: { fontSize: '10px' } } },
        yaxis: { labels: { show: false } },
        grid: { borderColor: '#f0f4f8' },
        tooltip: { y: { formatter: function(v) { return 'IDR ' + v + ' Mio'; } } }
    }).render();
}

renderBar3m('chartdiv2', [<?= $loan_3m_idr ?>],   [<?= $cat_3m ?>], '#ef4444');
renderBar3m('chartdiv4', [<?= $loan_3m_usd ?>],   [<?= $cat_3m ?>], '#f59e0b');
renderBar3m('chartdiv6', [<?= $loan_3m_total ?>],  [<?= $cat_3m ?>], '#7c3aed');

/* ── Loan modal charts (same data, taller) ── */
$('#modalloanidr').on('shown.bs.modal', function() {
    if (!$(this).data('rendered')) {
        new ApexCharts(document.querySelector('#chartloanidr'), {
            series: [{ name: 'IDR Loan (Mio)', data: [<?= $loan_3m_idr ?>] }],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 5, columnWidth: '50%' } },
            colors: ['#ef4444'],
            dataLabels: { enabled: true, formatter: function(v) { return v.toLocaleString('id-ID'); },
                offsetY: -18, style: { fontSize: '11px', colors: ['#2d3748'] } },
            xaxis: { categories: [<?= $cat_3m ?>] },
            yaxis: { labels: { show: false } },
            grid: { borderColor: '#f0f4f8' },
            tooltip: { y: { formatter: function(v) { return 'IDR ' + v + ' Mio'; } } }
        }).render();
        $(this).data('rendered', true);
    }
});
$('#modalloanusd').on('shown.bs.modal', function() {
    if (!$(this).data('rendered')) {
        new ApexCharts(document.querySelector('#chartloanusd'), {
            series: [{ name: 'USD Loan (Mio IDR)', data: [<?= $loan_3m_usd ?>] }],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 5, columnWidth: '50%' } },
            colors: ['#f59e0b'],
            dataLabels: { enabled: true, formatter: function(v) { return v.toLocaleString('id-ID'); },
                offsetY: -18, style: { fontSize: '11px', colors: ['#2d3748'] } },
            xaxis: { categories: [<?= $cat_3m ?>] },
            yaxis: { labels: { show: false } },
            grid: { borderColor: '#f0f4f8' },
            tooltip: { y: { formatter: function(v) { return 'IDR ' + v + ' Mio'; } } }
        }).render();
        $(this).data('rendered', true);
    }
});
$('#modalloantotal').on('shown.bs.modal', function() {
    if (!$(this).data('rendered')) {
        new ApexCharts(document.querySelector('#chartloantotal'), {
            series: [{ name: 'Total Loan (Mio IDR)', data: [<?= $loan_3m_total ?>] }],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: { bar: { borderRadius: 5, columnWidth: '50%' } },
            colors: ['#7c3aed'],
            dataLabels: { enabled: true, formatter: function(v) { return v.toLocaleString('id-ID'); },
                offsetY: -18, style: { fontSize: '11px', colors: ['#2d3748'] } },
            xaxis: { categories: [<?= $cat_3m ?>] },
            yaxis: { labels: { show: false } },
            grid: { borderColor: '#f0f4f8' },
            tooltip: { y: { formatter: function(v) { return 'IDR ' + v + ' Mio'; } } }
        }).render();
        $(this).data('rendered', true);
    }
});

/* ── Monthly Cash modal charts ── */
var monthlyChartOpts = function(seriesData, color, name) {
    return {
        series: [{ name: name, data: seriesData }],
        chart: { type: 'bar', height: 300, toolbar: { show: false },
            events: {
                click: function(event, ctx, config) {
                    if (config.dataPointIndex < 0) return;
                    var months = ['saldo_jan','saldo_feb','saldo_mar','saldo_apr','saldo_may','saldo_jun',
                                  'saldo_jul','saldo_aug','saldo_sep','saldo_oct','saldo_nov','saldo_dec'];
                    var titles = ['January','February','March','April','May','June',
                                  'July','August','September','October','November','December'];
                    var filter = months[config.dataPointIndex];
                    var title  = titles[config.dataPointIndex] + ' <?= date("Y") ?>';
                    return { filter: filter, title: title };
                }
            }
        },
        plotOptions: { bar: { borderRadius: 4, columnWidth: '60%', dataLabels: { position: 'top' } } },
        colors: [color],
        dataLabels: { enabled: true, formatter: function(v) { return v.toLocaleString('id-ID'); },
            offsetY: -18, style: { fontSize: '10px', colors: ['#2d3748'] } },
        xaxis: { categories: [<?= $monthly_lbl_bank ?>], labels: { style: { fontSize: '9px' }, rotate: -30 } },
        yaxis: { labels: { show: false } },
        grid: { borderColor: '#f0f4f8' },
        tooltip: { y: { formatter: function(v) { return v.toLocaleString('id-ID') + ' Mio'; } } }
    };
};

$('#modalcoh').on('shown.bs.modal', function() {
    if (!$(this).data('rendered')) {
        var opts = monthlyChartOpts([<?= $data_coh_m ?>], '#10b981', 'Cash on Hand (Mio)');
        opts.chart.events.click = function(event, ctx, config) {
            if (config.dataPointIndex < 0) return;
            var months = ['saldo_jan','saldo_feb','saldo_mar','saldo_apr','saldo_may','saldo_jun','saldo_jul','saldo_aug','saldo_sep','saldo_oct','saldo_nov','saldo_dec'];
            var titles = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $.ajax({ type:'post', url:'../dashboard/detail_cash_on_hand.php',
                data: { filter: months[config.dataPointIndex] },
                success: function(data) {
                    $('#detail_coh').html(data);
                    $('#jdl_coh').text(titles[config.dataPointIndex] + ' <?= date("Y") ?>');
                    $('#modaldetcoh').modal('show');
                }
            });
        };
        new ApexCharts(document.querySelector('#chartcoh'), opts).render();
        $(this).data('rendered', true);
    }
});

$('#modalcib').on('shown.bs.modal', function() {
    if (!$(this).data('rendered')) {
        var opts = monthlyChartOpts([<?= $data_cib_m ?>], '#1e88e5', 'Cash in Bank (Mio)');
        opts.chart.events.click = function(event, ctx, config) {
            if (config.dataPointIndex < 0) return;
            var months = ['saldo_jan','saldo_feb','saldo_mar','saldo_apr','saldo_may','saldo_jun','saldo_jul','saldo_aug','saldo_sep','saldo_oct','saldo_nov','saldo_dec'];
            var titles = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $.ajax({ type:'post', url:'../dashboard/detail_cash_in_bank.php',
                data: { filter: months[config.dataPointIndex] },
                success: function(data) {
                    $('#detail_cib').html(data);
                    $('#jdl_cib').text(titles[config.dataPointIndex] + ' <?= date("Y") ?>');
                    $('#modaldetcib').modal('show');
                }
            });
        };
        new ApexCharts(document.querySelector('#chartcib'), opts).render();
        $(this).data('rendered', true);
    }
});

$('#modaltc').on('shown.bs.modal', function() {
    if (!$(this).data('rendered')) {
        var opts = monthlyChartOpts([<?= $data_total_m ?>], '#3949ab', 'Total Cash (Mio)');
        opts.chart.events.click = function(event, ctx, config) {
            if (config.dataPointIndex < 0) return;
            var months = ['saldo_jan','saldo_feb','saldo_mar','saldo_apr','saldo_may','saldo_jun','saldo_jul','saldo_aug','saldo_sep','saldo_oct','saldo_nov','saldo_dec'];
            var titles = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $.ajax({ type:'post', url:'../dashboard/detail_cash_and_bank.php',
                data: { filter: months[config.dataPointIndex] },
                success: function(data) {
                    $('#detail_cib').html(data);
                    $('#jdl_cib').text(titles[config.dataPointIndex] + ' <?= date("Y") ?>');
                    $('#modaldetcib').modal('show');
                }
            });
        };
        new ApexCharts(document.querySelector('#charttc'), opts).render();
        $(this).data('rendered', true);
    }
});
</script>
