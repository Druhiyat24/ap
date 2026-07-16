<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    .card-text {
        font-size: 14px;
        color: #2F4F4F;
    }
    .card {
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        background: linear-gradient(90deg, #ffffff, #e9e9e9);


    }

    .card-header {
        color: black;
        text-align: center;
        font-size: 15px;
        font-weight: bold;
        padding: 15px; /* Menambah jarak dalam header */
        white-space: nowrap; /* Agar teks tidak terpotong ke baris kedua */
        text-overflow: ellipsis; /* Jika teks sangat panjang, tampilkan "..." */
        overflow: hidden; /* Sembunyikan teks yang keluar dari batas */
        text-transform: uppercase;
    }

    .card-body {
        text-align: center;
        font-size: 14px;
        color: #2F4F4F;
    }

    .card-group .card {
        margin-bottom: 15px; /* Menambahkan margin bawah antara card */
    }

    .card-group {
        display: flex;
        justify-content: space-between; /* Agar kartu-kartu dalam grup terpisah rata */
        flex-wrap: wrap; /* Agar kartu-kartu berada di baris berikutnya pada ukuran layar kecil */
    }

    /* ===== Account Payable summary panel ===== */
    .ap-summary {
        --ap-surface: #ffffff;
        --ap-track:   #eef0f3;
        --ap-text:    #14181b;
        --ap-text-2:  #52514e;
        --ap-muted:   #8b8f96;
        --ap-border:  rgba(11,11,11,0.08);
        --ap-blue:    #2a78d6;
        --ap-red:     #d03b3b;
        --ap-green:   #1f8a4c;
    }
    .ap-hero.tone-green .ap-hero-label { background: var(--ap-green); }
    .ap-tile.tone-green .ap-tile-label { background: var(--ap-green); }
    .ap-hero {
        background: var(--ap-surface);
        border: 1px solid var(--ap-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        margin-bottom: 14px;
    }
    .ap-hero-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #ffffff;
        background: var(--ap-blue);
        padding: 13px 26px;
    }
    .ap-hero-label i { color: #ffffff; }
    .ap-hero-body { padding: 22px 26px 24px; }
    .ap-hero-value {
        font-size: 38px;
        font-weight: 700;
        color: var(--ap-text);
        line-height: 1.15;
    }
    .ap-hero-updated {
        font-size: 13px;
        color: var(--ap-muted);
        margin-top: 8px;
    }
    .ap-meter { margin-top: 20px; }
    .ap-meter-bar {
        display: flex;
        height: 12px;
        border-radius: 7px;
        overflow: hidden;
        background: var(--ap-track);
    }
    .ap-meter-seg { height: 100%; }
    .ap-meter-seg.not-due  { background: var(--ap-blue); }
    .ap-meter-seg.over-due { background: var(--ap-red); margin-left: 2px; }
    .ap-meter-seg.purchase { background: var(--ap-green); }
    .ap-meter-seg.return   { background: var(--ap-red); margin-left: 2px; }
    .ap-meter-legend {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-top: 14px;
    }
    .ap-meter-item {
        display: flex;
        flex-direction: column;
        flex: 1 1 0;
        min-width: 0;
    }
    .ap-meter-item.over-due,
    .ap-meter-item.return { text-align: right; }
    .ap-meter-item .lbl {
        font-size: 15px;
        color: var(--ap-text-2);
        line-height: 1.4;
    }
    .ap-meter-item .dot {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        vertical-align: middle;
        margin-right: 6px;
        margin-bottom: 2px;
    }
    .ap-meter-item.over-due .dot,
    .ap-meter-item.return .dot { margin-right: 0; margin-left: 6px; }
    .ap-meter-item.not-due .dot  { background: var(--ap-blue); }
    .ap-meter-item.over-due .dot { background: var(--ap-red); }
    .ap-meter-item.purchase .dot { background: var(--ap-green); }
    .ap-meter-item.return .dot   { background: var(--ap-red); }
    .ap-meter-item .pct {
        font-weight: 700;
        color: var(--ap-text);
        white-space: nowrap;
    }
    .ap-meter-item .val {
        font-size: 18px;
        font-weight: 700;
        color: var(--ap-text);
        margin-top: 6px;
    }
    .ap-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }
    .ap-tile {
        background: var(--ap-surface);
        border: 1px solid var(--ap-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        min-width: 0;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .ap-tile-label {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        font-size: 14.5px;
        font-weight: 700;
        line-height: 1.35;
        text-transform: uppercase;
        letter-spacing: .02em;
        color: #ffffff;
        background: var(--ap-blue);
        padding: 11px 16px;
    }
    .ap-tile-label i { color: #ffffff; font-size: 14px; margin-top: 2px; }
    .ap-tile-body {
        padding: 20px 16px 17px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .ap-tile-value {
        font-size: 17px;
        font-weight: 700;
        color: var(--ap-text);
    }
    .ap-tile-value small {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: var(--ap-text-2);
        margin-top: 4px;
    }

</style>

<div class="col p-4">
    <!-- <div class="box " style="background-color: #F0F8FF;"> -->
<!--         <p id="lastUpdate">
  <?php
    $res = mysqli_query($conn2, "select max(create_date) last_update from dsb_ap_bpb");
    $row = mysqli_fetch_assoc($res);
    echo "📅 Last update: " . date('d-m-Y H:i:s', strtotime($row['last_update']));
  ?>
  <button id="refreshDashboard" class="btn btn-sm btn-warning">🔁 Update</button>
<p id="refreshInfo" style="margin-top: 10px;"></p>
</p> -->
        <div class="row div-dashboard">
            <div class="col-md-8">
                <div class="row">
                    <?php
                    // Precomputed by get_purchase_dashboard_summary (event runs every 10 min).
                    // See module/AP/create_dsb_purchase_summary.sql.
                    $rs_pch_summary = mysqli_query($conn2, "SELECT * FROM dsb_purchase_summary WHERE id = 1");
                    $pch_row = $rs_pch_summary ? mysqli_fetch_assoc($rs_pch_summary) : [];

                    $total_npch_ytd  = $pch_row['ytd_net']      ?? 0;
                    $total_pch_ytd   = $pch_row['ytd_purchase'] ?? 0;
                    $total_retur_ytd = $pch_row['ytd_retur']    ?? 0;
                    $total_npch_cm   = $pch_row['cm_net']       ?? 0;
                    $total_pch_cm    = $pch_row['cm_purchase']  ?? 0;
                    $total_retur_cm  = $pch_row['cm_retur']     ?? 0;
                    $pch_updated_at  = $pch_row['updated_at']   ?? null;

                    $ytd_return_pct   = $total_pch_ytd > 0 ? round(($total_retur_ytd / $total_pch_ytd) * 100, 1) : 0;
                    $ytd_purchase_pct = $total_pch_ytd > 0 ? max(0, round(100 - $ytd_return_pct, 1)) : 0;
                    $cm_return_pct    = $total_pch_cm  > 0 ? round(($total_retur_cm  / $total_pch_cm)  * 100, 1) : 0;
                    $cm_purchase_pct  = $total_pch_cm  > 0 ? max(0, round(100 - $cm_return_pct, 1)) : 0;
                    ?>
                    <div class="col-md-6">
                        <div class="col p-2 ">
                            <div class="ap-summary">
                                <div class="ap-hero tone-green">
                                    <div class="ap-hero-label"><i class="fa fa-chart-line"></i> Net Purchase Year to Date</div>
                                    <div class="ap-hero-body">
                                        <div class="ap-hero-value">IDR <?= number_format($total_npch_ytd,2); ?></div>
                                        <?php if ($pch_updated_at): ?>
                                            <div class="ap-hero-updated"><i class="fa fa-clock-o"></i> Updated <?= date('d M Y, H:i', strtotime($pch_updated_at)); ?></div>
                                        <?php endif; ?>

                                        <div class="ap-meter">
                                            <div class="ap-meter-bar">
                                                <div class="ap-meter-seg purchase" style="width: <?= $ytd_purchase_pct; ?>%"></div>
                                                <div class="ap-meter-seg return" style="width: <?= $ytd_return_pct; ?>%"></div>
                                            </div>
                                            <div class="ap-meter-legend">
                                                <div class="ap-meter-item purchase">
                                                    <span class="lbl"><span class="dot"></span>Purchase Year to Date <b class="pct"><?= $ytd_purchase_pct; ?>%</b></span>
                                                    <span class="val">IDR <?= number_format($total_pch_ytd, 2); ?></span>
                                                </div>
                                                <div class="ap-meter-item return">
                                                    <span class="lbl">Purchase Return Year to Date <b class="pct"><?= $ytd_return_pct; ?>%</b><span class="dot"></span></span>
                                                    <span class="val">IDR (<?= number_format($total_retur_ytd, 2); ?>)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col p-2">
                            <div class="ap-summary">
                                <div class="ap-hero tone-green">
                                    <div class="ap-hero-label"><i class="fa fa-chart-line"></i> Net Purchase Current Month</div>
                                    <div class="ap-hero-body">
                                        <div class="ap-hero-value">IDR <?= number_format($total_npch_cm,2); ?></div>
                                        <?php if ($pch_updated_at): ?>
                                            <div class="ap-hero-updated"><i class="fa fa-clock-o"></i> Updated <?= date('d M Y, H:i', strtotime($pch_updated_at)); ?></div>
                                        <?php endif; ?>

                                        <div class="ap-meter">
                                            <div class="ap-meter-bar">
                                                <div class="ap-meter-seg purchase" style="width: <?= $cm_purchase_pct; ?>%"></div>
                                                <div class="ap-meter-seg return" style="width: <?= $cm_return_pct; ?>%"></div>
                                            </div>
                                            <div class="ap-meter-legend">
                                                <div class="ap-meter-item purchase">
                                                    <span class="lbl"><span class="dot"></span>Purchase Current Month <b class="pct"><?= $cm_purchase_pct; ?>%</b></span>
                                                    <span class="val">IDR <?= number_format($total_pch_cm, 2); ?></span>
                                                </div>
                                                <div class="ap-meter-item return">
                                                    <span class="lbl">Purchase Return Current Month <b class="pct"><?= $cm_return_pct; ?>%</b><span class="dot"></span></span>
                                                    <span class="val">IDR (<?= number_format($total_retur_cm, 2); ?>)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col p-2">
                    <div class="ap-summary">
                        <div class="ap-hero tone-green">
                            <div class="ap-hero-label"><i class="fa fa-trophy"></i> Top 10 Supplier by Amount (YTD)</div>
                            <div class="ap-hero-body" style="padding-top:8px;">
                                <div id="chart_supptop10"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="col-md-4">
                    <?php
                    // Precomputed by get_ap_dashboard_summary (event runs every 10 min),
                    // same combined BPB + Payment Voucher logic as payable_card_statement2.php's
                    // Summary tab. See module/AP/create_dsb_ap_summary.sql.
                    $rs_ap_summary = mysqli_query($conn2, "SELECT * FROM dsb_ap_summary WHERE id = 1");
                    $row = mysqli_fetch_assoc($rs_ap_summary);

                    $total         = $row['total'] ?? 0;
                    $not_due       = $row['not_due'] ?? 0;
                    $over_due      = $row['over_due'] ?? 0;
                    $ap_group      = $row['ap_group'] ?? 0;
                    $ap_nongroup   = $row['ap_nongroup'] ?? 0;
                    $ap_machine    = $row['ap_machine'] ?? 0;
                    $ap_nonmachine = $row['ap_nonmachine'] ?? 0;
                    $total_usd     = $row['total_usd'] ?? 0;
                    $total_eqvidr  = $row['total_usd_idr'] ?? 0;
                    $total_idr     = $row['total_idr'] ?? 0;
                    $updated_at    = $row['updated_at'] ?? null;

                    $not_due_pct  = $total > 0 ? round(($not_due / $total) * 100, 1) : 0;
                    $over_due_pct = $total > 0 ? max(0, round(100 - $not_due_pct, 1)) : 0;
                    ?>
                    <div class="ap-summary mt-2">

                        <div class="ap-hero">
                            <div class="ap-hero-label"><i class="fa fa-balance-scale"></i> Account payable Total Outstanding</div>
                            <div class="ap-hero-body">
                                <div class="ap-hero-value">IDR <?= number_format($total, 2); ?></div>
                                <?php if ($updated_at): ?>
                                    <div class="ap-hero-updated"><i class="fa fa-clock-o"></i> Updated <?= date('d M Y, H:i', strtotime($updated_at)); ?></div>
                                <?php endif; ?>

                                <div class="ap-meter">
                                    <div class="ap-meter-bar">
                                        <div class="ap-meter-seg not-due" style="width: <?= $not_due_pct; ?>%"></div>
                                        <div class="ap-meter-seg over-due" style="width: <?= $over_due_pct; ?>%"></div>
                                    </div>
                                    <div class="ap-meter-legend">
                                        <div class="ap-meter-item not-due">
                                            <span class="lbl"><span class="dot"></span>Account Payable Not Due <b class="pct"><?= $not_due_pct; ?>%</b></span>
                                            <span class="val">IDR <?= number_format($not_due, 2); ?></span>
                                        </div>
                                        <div class="ap-meter-item over-due">
                                            <span class="lbl">Account Payable Over Due <b class="pct"><?= $over_due_pct; ?>%</b><span class="dot"></span></span>
                                            <span class="val">IDR <?= number_format($over_due, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px;">
                        <div class="ap-grid">
                            <div class="ap-tile">
                                <div class="ap-tile-label"><i class="fa fa-building"></i> Account Payable Group</div>
                                <div class="ap-tile-body">
                                    <div class="ap-tile-value">IDR <?= number_format($ap_group, 2); ?></div>
                                </div>
                            </div>
                            <div class="ap-tile">
                                <div class="ap-tile-label"><i class="fa fa-sitemap"></i> Account Payable Non Group</div>
                                <div class="ap-tile-body">
                                    <div class="ap-tile-value">IDR <?= number_format($ap_nongroup, 2); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="ap-grid">
                            <div class="ap-tile">
                                <div class="ap-tile-label"><i class="fa fa-cogs"></i> Account Payable Machine</div>
                                <div class="ap-tile-body">
                                    <div class="ap-tile-value">IDR <?= number_format($ap_machine, 2); ?></div>
                                </div>
                            </div>
                            <div class="ap-tile">
                                <div class="ap-tile-label"><i class="fa fa-cube"></i> Account Payable Non Machine</div>
                                <div class="ap-tile-body">
                                    <div class="ap-tile-value">IDR <?= number_format($ap_nonmachine, 2); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="ap-grid">
                            <div class="ap-tile">
                                <div class="ap-tile-label"><i class="fa fa-dollar"></i> Account Payable USD</div>
                                <div class="ap-tile-body">
                                    <div class="ap-tile-value">USD <?= number_format($total_usd, 2); ?>
                                        <small>IDR <?= number_format($total_eqvidr, 2); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="ap-tile">
                                <div class="ap-tile-label"><i class="fa fa-money"></i> Account Payable IDR</div>
                                <div class="ap-tile-body">
                                    <div class="ap-tile-value">IDR <?= number_format($total_idr, 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>
</div>
</div>



<script type="text/javascript">
    function fmtCompactIDR(val) {
        var n = Math.abs(val);
        var sign = val < 0 ? '-' : '';
        if (n >= 1e9) return sign + 'IDR ' + (n / 1e9).toFixed(1) + 'B';
        if (n >= 1e6) return sign + 'IDR ' + (n / 1e6).toFixed(1) + 'M';
        if (n >= 1e3) return sign + 'IDR ' + (n / 1e3).toFixed(1) + 'K';
        return sign + 'IDR ' + n.toLocaleString('en-US');
    }

    var optionsSupp10 = {
        series: [{
            name: 'Amount',
            data: [<?php
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(total ORDER BY total DESC) total from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),'-01-01') and CURRENT_DATE() GROUP BY nama_supp
                    UNION
                    select nama_supp,-sum(dpp) total from dsb_ap_retur where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),'-01-01') and CURRENT_DATE() GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a");
                $row = mysqli_fetch_array($sql);
                $total = $row['total'];
                echo $total;
                ?>]
            }],
            chart: {
                height: 420,
                type: 'bar',
                toolbar: { show: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 500
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 0,
                    dataLabels: { position: 'top' }
                }
            },
            colors: ['#2a78d6'],
            dataLabels: {
                enabled: true,
                formatter: fmtCompactIDR,
                offsetY: -18,
                style: {
                    fontSize: '10px',
                    fontWeight: 600,
                    colors: ['#14181b']
                }
            },
            xaxis: {
                categories: [<?php
                    $sql = mysqli_query($conn2,'select GROUP_CONCAT(concat("""",nama_supp,"""") ORDER BY total DESC) nama_supp from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),\'-01-01\') and CURRENT_DATE() GROUP BY nama_supp
                        UNION
                        select nama_supp,-sum(dpp) total from dsb_ap_retur where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),\'-01-01\') and CURRENT_DATE() GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a');
                    $row = mysqli_fetch_array($sql);
                    $nama_supp = $row['nama_supp'];
                    echo $nama_supp;
                    ?>],
                labels: {
                    rotate: -45,
                    rotateAlways: true,
                    trim: true,
                    style: { fontSize: '11px', colors: '#52514e' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                tickAmount: 4,
                labels: {
                    formatter: fmtCompactIDR,
                    style: { fontSize: '10px', colors: '#8b8f96' }
                }
            },
            grid: {
                borderColor: '#e1e0d9',
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'IDR ' + Math.round(val).toLocaleString('en-US');
                    }
                }
            }
        };

        var chartSupp10 = new ApexCharts(document.querySelector("#chart_supptop10"), optionsSupp10);
        chartSupp10.render();
    </script>


    <script type="text/javascript">
        var options = {
          series: [{
              name: 'NET PURCHASE',
              data: [<?php 
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_purchase,0) - COALESCE(ttl_retur,0))/1000000,2)) net_purchase from (select bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a LEFT JOIN
                    (select sum(dpp)ttl_purchase,bln1 from (select dpp,MONTH(tgl_bpb) bln1 from dsb_ap_purchase ) a GROUP BY bln1) b on b.bln1 = a.bulan LEFT JOIN
                    (select sum(dpp)ttl_retur,bln2 from (select dpp,MONTH(tgl_bpb) bln2 from dsb_ap_retur ) a GROUP BY bln2) c on c.bln2 = a.bulan");
                $row = mysqli_fetch_array($sql);
                $total = $row['net_purchase'];
                echo $total;

                ?>]
            }, {
              name: 'PAYMENT',
              data: [<?php 
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_bpb,0) + COALESCE(ttl_kbon,0) + COALESCE(ttl_lp,0))/1000000,2)) payment from (select bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a LEFT JOIN
                    (select bln1,sum(end_balance_idr) ttl_bpb from (select MONTH(tgl_bpb) bln1,end_balance_idr from dsb_ap_bpb where end_balance_idr != 0 and tgl_bpb >= '2024-01-01') a GROUP BY bln1) b on b.bln1 = a.bulan LEFT JOIN
                    (select bln2,sum(end_balance_idr) ttl_kbon from (select MONTH(tgl_kbon) bln2,end_balance_idr from dsb_ap_kbon where end_balance_idr != 0 and tgl_kbon >= '2024-01-01') a GROUP BY bln2) c on c.bln2 = a.bulan LEFT JOIN
                    (select bln3,sum(end_balance_idr) ttl_lp from (select MONTH(tgl_payment) bln3,end_balance_idr from dsb_ap_lp where end_balance_idr != 0 and tgl_payment >= '2024-01-01') a GROUP BY bln3) d on d.bln3 = a.bulan");
                $row = mysqli_fetch_array($sql);
                $total = $row['payment'];
                echo $total;

                ?>]
            }],
            chart: {
              type: 'bar',
              height: 350
          },
          plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
          enabled: false
      },
      stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
      },
      xaxis: {
          categories: [<?php 
            $sql = mysqli_query($conn2,'select GROUP_CONCAT(concat("""",label,"""")) label from (select concat(nama_bulan_singkat," ",tahun) label from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a');
            $row = mysqli_fetch_array($sql);
            $label = $row['label'];
            echo $label;

            ?>],
        },
        yaxis: {
          title: {
            text: 'MIO'
        }
    },
    fill: {
      opacity: 1
  },
  tooltip: {
      y: {
        formatter: function (val) {
          return "IDR " + val + " MIO"
      }
  }
}
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();

</script>


