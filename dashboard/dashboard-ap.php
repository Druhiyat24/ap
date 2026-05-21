<?php
/* ── Data queries ──────────────────────────────────────── */
// helper — safe fetch
function _qv($conn, $sql, $col) {
    $q = mysqli_query($conn, $sql);
    if (!$q) return 0;
    $r = mysqli_fetch_array($q);
    return $r[$col] ?? 0;
}

// Purchase YTD
$net_ytd = _qv($conn2,"select sum(total) total from (select sum(total_idr) total from dsb_ap_purchase where tgl_bpb BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') and CURDATE() UNION select -sum(total_idr) total from dsb_ap_retur where tgl_bpb BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') and CURDATE()) a",'total');
$pch_ytd = _qv($conn2,"select sum(total_idr) total from dsb_ap_purchase where tgl_bpb BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') and CURDATE()",'total');
$ret_ytd = _qv($conn2,"select sum(total_idr) total from dsb_ap_retur where tgl_bpb BETWEEN CONCAT(YEAR(CURDATE()),'-01-01') and CURDATE()",'total');

// Purchase Current Month
$net_cm = _qv($conn2,"select sum(total) total from (select sum(total_idr) total from dsb_ap_purchase where MONTH(tgl_bpb)=MONTH(CURDATE()) UNION select -sum(total_idr) total from dsb_ap_retur where MONTH(tgl_bpb)=MONTH(CURDATE())) a",'total');
$pch_cm = _qv($conn2,"select sum(total_idr) total from dsb_ap_purchase where MONTH(tgl_bpb)=MONTH(CURDATE())",'total');
$ret_cm = _qv($conn2,"select sum(total_idr) total from dsb_ap_retur where MONTH(tgl_bpb)=MONTH(CURDATE())",'total');

// AP Outstanding
$ap_sql = "select sum(total_type_idr) total, sum(due_0) not_due, sum(produe_0) over_due from (
    select 'bpb' id,sum(end_balance_idr) total_type_idr,sum(due_0) due_0,sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr!=0
    UNION select 'kbn',sum(end_balance_idr),sum(due_0),sum(produe_0) from dsb_ap_kbon where end_balance_idr!=0
    UNION select 'lp',sum(end_balance_idr),sum(due_0),sum(produe_0) from dsb_ap_lp where end_balance_idr!=0) a";
$q = mysqli_query($conn2, $ap_sql); $ra = $q ? mysqli_fetch_array($q) : [];
$ap_total   = $ra['total']    ?? 0;
$ap_notdue  = $ra['not_due']  ?? 0;
$ap_overdue = $ra['over_due'] ?? 0;

// AP Group / Non-Group
$ap_group    = _qv($conn2,"select sum(total_type_idr) total from (select sum(end_balance_idr) total_type_idr from dsb_ap_bpb where end_balance_idr!=0 and relasi='GROUP' UNION select sum(end_balance_idr) from dsb_ap_kbon where end_balance_idr!=0 and relasi='GROUP' UNION select sum(end_balance_idr) from dsb_ap_lp where end_balance_idr!=0 and relasi='GROUP') a",'total');
$ap_nongroup = _qv($conn2,"select sum(total_type_idr) total from (select sum(end_balance_idr) total_type_idr from dsb_ap_bpb where end_balance_idr!=0 and relasi='NON GROUP' UNION select sum(end_balance_idr) from dsb_ap_kbon where end_balance_idr!=0 and relasi='NON GROUP' UNION select sum(end_balance_idr) from dsb_ap_lp where end_balance_idr!=0 and relasi='NON GROUP') a",'total');

// AP USD / IDR
$q = mysqli_query($conn2,"select sum(end_balance) usd_val, sum(end_balance_idr) usd_idr from (select end_balance,end_balance_idr from dsb_ap_bpb where curr='USD' and end_balance_idr!=0 UNION select end_balance,end_balance_idr from dsb_ap_kbon where curr='USD' and end_balance_idr!=0 UNION select end_balance,end_balance_idr from dsb_ap_lp where curr='USD' and end_balance_idr!=0) a");
$ru = $q ? mysqli_fetch_array($q) : [];
$ap_usd_val = $ru['usd_val'] ?? 0;
$ap_usd_idr = $ru['usd_idr'] ?? 0;
$ap_idr_val = _qv($conn2,"select sum(end_balance_idr) idr_val from (select end_balance_idr from dsb_ap_bpb where curr='IDR' and end_balance_idr!=0 UNION select end_balance_idr from dsb_ap_kbon where curr='IDR' and end_balance_idr!=0 UNION select end_balance_idr from dsb_ap_lp where curr='IDR' and end_balance_idr!=0) a",'idr_val');

// Chart data — top 10 suppliers by net purchase amount
$sql_top10_val = mysqli_query($conn2,"select GROUP_CONCAT(total) total from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase GROUP BY nama_supp UNION select nama_supp,-sum(dpp) total from dsb_ap_retur GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a");
$r = mysqli_fetch_array($sql_top10_val); $chart_top10_val = $r['total'] ?? '';
$sql_top10_cat = mysqli_query($conn2,'select GROUP_CONCAT(concat(\'"\',nama_supp,\'"\')) nama_supp from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase GROUP BY nama_supp UNION select nama_supp,-sum(dpp) total from dsb_ap_retur GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a');
$r = mysqli_fetch_array($sql_top10_cat); $chart_top10_cat = $r['nama_supp'] ?? '';

$sql_monthly_net = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_purchase,0)-COALESCE(ttl_retur,0))/1000000,2)) v from (select bulan from dim_date where tahun=YEAR(CURDATE()) GROUP BY bulan ORDER BY bulan) a LEFT JOIN (select MONTH(tgl_bpb) bln,sum(dpp) ttl_purchase from dsb_ap_purchase GROUP BY bln) b on b.bln=a.bulan LEFT JOIN (select MONTH(tgl_bpb) bln,sum(dpp) ttl_retur from dsb_ap_retur GROUP BY bln) c on c.bln=a.bulan");
$r = mysqli_fetch_array($sql_monthly_net); $monthly_net = $r['v'] ?? '';
$sql_monthly_pay = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_bpb,0)+COALESCE(ttl_kbon,0)+COALESCE(ttl_lp,0))/1000000,2)) v from (select bulan from dim_date where tahun=YEAR(CURDATE()) GROUP BY bulan ORDER BY bulan) a LEFT JOIN (select MONTH(tgl_bpb) bln,sum(end_balance_idr) ttl_bpb from dsb_ap_bpb where end_balance_idr!=0 and tgl_bpb>='2024-01-01' GROUP BY bln) b on b.bln=a.bulan LEFT JOIN (select MONTH(tgl_kbon) bln,sum(end_balance_idr) ttl_kbon from dsb_ap_kbon where end_balance_idr!=0 and tgl_kbon>='2024-01-01' GROUP BY bln) c on c.bln=a.bulan LEFT JOIN (select MONTH(tgl_payment) bln,sum(end_balance_idr) ttl_lp from dsb_ap_lp where end_balance_idr!=0 and tgl_payment>='2024-01-01' GROUP BY bln) d on d.bln=a.bulan");
$r = mysqli_fetch_array($sql_monthly_pay); $monthly_pay = $r['v'] ?? '';
$sql_monthly_lbl = mysqli_query($conn2,'select GROUP_CONCAT(concat(\'"\',concat(nama_bulan_singkat," ",tahun),\'"\')) label from (select nama_bulan_singkat,tahun from dim_date where tahun=YEAR(CURDATE()) GROUP BY bulan ORDER BY bulan) a');
$r = mysqli_fetch_array($sql_monthly_lbl); $monthly_lbl = $r['label'] ?? '';

if (!function_exists('fmt')) {
    function fmt($v) { return 'IDR '.number_format((float)$v,0,',','.'); }
}
?>

<style>
/* ── AP Dashboard ──────────────────────────────────────── */
.dsb-info {
    display: flex; align-items: center; gap: 14px;
    background: #fff; border-radius: 14px; border: none;
    padding: 18px 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    transition: transform .2s, box-shadow .2s;
    margin-bottom: 18px;
    position: relative; overflow: hidden;
}
.dsb-info::before {
    content:''; position:absolute; right:-18px; bottom:-18px;
    width:80px; height:80px; border-radius:50%;
    background: rgba(255,255,255,.12);
}
.dsb-info:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.12); }
.dsb-info-icon {
    width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #fff;
}
.dsb-info-body { flex: 1; min-width: 0; }
.dsb-info-label { font-size: 11px; font-weight: 600; letter-spacing: .8px;
    text-transform: uppercase; color: #a0aec0; margin-bottom: 3px; }
.dsb-info-value { font-size: 15px; font-weight: 700; color: #2d3748;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.dsb-info-sub   { font-size: 11.5px; color: #718096; margin-top: 2px; }

.dsb-section {
    font-size: 11px; font-weight: 700; letter-spacing: 1.5px;
    text-transform: uppercase; color: #a0aec0;
    margin: 4px 0 14px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center; gap: 8px;
}
.dsb-section i { color: #cbd5e0; }

.dsb-chart-card {
    background: #fff; border-radius: 14px; border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    margin-bottom: 18px; overflow: hidden;
}
.dsb-chart-header {
    padding: 14px 20px; border-bottom: 1px solid #f0f4f8;
    font-size: 13px; font-weight: 600; color: #2d3748;
    display: flex; align-items: center; gap: 8px;
}
.dsb-chart-header i { color: #3949ab; }
.dsb-chart-body { padding: 12px 16px; }

/* Alert badges in header */
.dsb-badge {
    display: inline-block; padding: 2px 8px; border-radius: 999px;
    font-size: 10.5px; font-weight: 600; margin-left: auto;
}
.dsb-badge-up   { background: #dcfce7; color: #166534; }
.dsb-badge-down { background: #fee2e2; color: #991b1b; }
</style>

<!-- ── SECTION: Purchase Performance ───────────────────── -->
<div class="dsb-section"><i class="fas fa-shopping-cart"></i> Purchase Performance — <?= date('Y') ?></div>

<div class="row">
    <!-- Net Purchase YTD -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#1e88e5,#1565c0);">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Net Purchase YTD</div>
                <div class="dsb-info-value"><?= fmt($net_ytd) ?></div>
                <div class="dsb-info-sub">
                    <span style="color:#10b981;">+<?= fmt($pch_ytd) ?></span>
                    &nbsp;/&nbsp;
                    <span style="color:#ef4444;">(<?= fmt($ret_ytd) ?>)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase YTD -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#10b981,#047857);">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Purchase Year to Date</div>
                <div class="dsb-info-value"><?= fmt($pch_ytd) ?></div>
            </div>
        </div>
    </div>

    <!-- Return YTD -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Purchase Return YTD</div>
                <div class="dsb-info-value">(<?= fmt($ret_ytd) ?>)</div>
            </div>
        </div>
    </div>

    <!-- Net Purchase CM -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#3949ab,#283593);">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Net Purchase <?= date('M Y') ?></div>
                <div class="dsb-info-value"><?= fmt($net_cm) ?></div>
                <div class="dsb-info-sub">
                    <span style="color:#10b981;">+<?= fmt($pch_cm) ?></span>
                    &nbsp;/&nbsp;
                    <span style="color:#ef4444;">(<?= fmt($ret_cm) ?>)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase CM -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#06b6d4,#0e7490);">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Purchase <?= date('M Y') ?></div>
                <div class="dsb-info-value"><?= fmt($pch_cm) ?></div>
            </div>
        </div>
    </div>

    <!-- Return CM -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#f59e0b,#b45309);">
                <i class="fas fa-undo-alt"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Return <?= date('M Y') ?></div>
                <div class="dsb-info-value">(<?= fmt($ret_cm) ?>)</div>
            </div>
        </div>
    </div>
</div>

<!-- ── SECTION: AP Outstanding ──────────────────────────── -->
<div class="dsb-section"><i class="fas fa-balance-scale"></i> Account Payable Outstanding</div>

<div class="row">
    <!-- Total Outstanding -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">
                <i class="fas fa-university"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Total Outstanding</div>
                <div class="dsb-info-value"><?= fmt($ap_total) ?></div>
            </div>
        </div>
    </div>

    <!-- Not Due -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#10b981,#047857);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Not Due</div>
                <div class="dsb-info-value"><?= fmt($ap_notdue) ?></div>
            </div>
        </div>
    </div>

    <!-- Overdue -->
    <div class="col-md-4 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">Over Due</div>
                <div class="dsb-info-value"><?= fmt($ap_overdue) ?></div>
            </div>
        </div>
    </div>

    <!-- Group -->
    <div class="col-md-3 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#1e88e5,#1565c0);">
                <i class="fas fa-users"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">AP Group</div>
                <div class="dsb-info-value"><?= fmt($ap_group) ?></div>
            </div>
        </div>
    </div>

    <!-- Non-Group -->
    <div class="col-md-3 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#64748b,#334155);">
                <i class="fas fa-user"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">AP Non Group</div>
                <div class="dsb-info-value"><?= fmt($ap_nongroup) ?></div>
            </div>
        </div>
    </div>

    <!-- USD -->
    <div class="col-md-3 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#f59e0b,#b45309);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">AP in USD</div>
                <div class="dsb-info-value">USD <?= number_format($ap_usd_val,2) ?></div>
                <div class="dsb-info-sub"><?= fmt($ap_usd_idr) ?></div>
            </div>
        </div>
    </div>

    <!-- IDR -->
    <div class="col-md-3 col-sm-6">
        <div class="dsb-info">
            <div class="dsb-info-icon" style="background:linear-gradient(135deg,#06b6d4,#0e7490);">
                <i class="fas fa-coins"></i>
            </div>
            <div class="dsb-info-body">
                <div class="dsb-info-label">AP in IDR</div>
                <div class="dsb-info-value"><?= fmt($ap_idr_val) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- ── SECTION: Charts ──────────────────────────────────── -->
<div class="dsb-section"><i class="fas fa-chart-bar"></i> Analytics</div>

<div class="row">
    <div class="col-md-7">
        <div class="dsb-chart-card">
            <div class="dsb-chart-header">
                <i class="fas fa-trophy"></i> Top 10 Suppliers by Amount (YTD)
            </div>
            <div class="dsb-chart-body">
                <div id="chart_supptop10" style="min-height:300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="dsb-chart-card">
            <div class="dsb-chart-header">
                <i class="fas fa-chart-area"></i> Monthly Purchase vs Outstanding (Mio IDR)
            </div>
            <div class="dsb-chart-body">
                <div id="chart" style="min-height:300px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
/* ── Top 10 Suppliers chart ── */
new ApexCharts(document.querySelector("#chart_supptop10"), {
    series: [{ name: 'Amount (IDR)', data: [<?= $chart_top10_val ?>] }],
    chart: { type: 'bar', height: 300, toolbar: { show: false },
             animations: { enabled: true, speed: 700 } },
    plotOptions: { bar: { borderRadius: 4, columnWidth: '65%', distributed: true } },
    colors: ['#3949ab','#1e88e5','#00897b','#43a047','#fb8c00','#e53935','#8e24aa','#039be5','#00acc1','#7cb342'],
    legend: { show: false },
    dataLabels: { enabled: true, formatter: v => 'IDR ' + (v/1e6).toFixed(1) + 'M',
        offsetY: -18, style: { fontSize: '9px', colors: ['#2d3748'] } },
    xaxis: { categories: [<?= $chart_top10_cat ?>],
        labels: { style: { fontSize: '9px', colors: '#718096' }, rotate: -25 } },
    yaxis: { labels: { show: false } },
    grid: { borderColor: '#f0f4f8' },
    tooltip: { y: { formatter: v => 'IDR ' + Number(v).toLocaleString('id-ID') } }
}).render();

/* ── Monthly chart ── */
new ApexCharts(document.querySelector("#chart"), {
    series: [
        { name: 'Net Purchase', data: [<?= $monthly_net ?>] },
        { name: 'AP Outstanding', data: [<?= $monthly_pay ?>] }
    ],
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
    colors: ['#3949ab', '#ef4444'],
    dataLabels: { enabled: false },
    stroke: { show: true, width: 2, colors: ['transparent'] },
    xaxis: { categories: [<?= $monthly_lbl ?>],
        labels: { style: { fontSize: '9px', colors: '#718096' } } },
    yaxis: { title: { text: 'IDR (Mio)', style: { fontSize: '11px', color: '#a0aec0' } },
        labels: { style: { fontSize: '9px' } } },
    grid: { borderColor: '#f0f4f8' },
    legend: { position: 'top', fontSize: '11px' },
    tooltip: { y: { formatter: v => 'IDR ' + v + ' Mio' } }
}).render();
</script>
