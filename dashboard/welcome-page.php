<?php
/* Quick stats for the welcome cards */
$stat_bpb   = (mysqli_fetch_array(mysqli_query($conn2,"SELECT COUNT(DISTINCT no_bpb) c FROM bpb_new WHERE status != 'CANCEL' AND DATE(tgl_bpb) = CURDATE()"))['c'] ?? 0);
$stat_kb    = (mysqli_fetch_array(mysqli_query($conn2,"SELECT COUNT(DISTINCT no_kbon) c FROM kontrabon_h WHERE status = 'draft'"))['c'] ?? 0);
$stat_lp    = (mysqli_fetch_array(mysqli_query($conn2,"SELECT COUNT(DISTINCT no_payment) c FROM list_payment WHERE status = 'draft'"))['c'] ?? 0);
$stat_pay   = (mysqli_fetch_array(mysqli_query($conn2,"SELECT COUNT(id) c FROM (SELECT a.id FROM payment_ftr a INNER JOIN master_pc b ON b.kode_pc = a.profit_center WHERE a.status = 'draft' GROUP BY payment_ftr_id) x"))['c'] ?? 0);
$hour       = (int)date('G');
$greeting   = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
?>

<style>
/* ── Welcome page ─────────────────────────────────────────── */
.wp-hero {
    background: linear-gradient(135deg, #283593 0%, #3949ab 50%, #1e88e5 100%);
    border-radius: 16px;
    padding: 36px 32px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    color: #fff;
}
.wp-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.07);
}
.wp-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; right: 80px;
    width: 140px; height: 140px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.wp-hero-greeting { font-size: 14px; opacity: .75; letter-spacing: .5px; margin-bottom: 4px; }
.wp-hero-name     { font-size: 28px; font-weight: 700; letter-spacing: -.3px; margin-bottom: 6px; }
.wp-hero-sub      { font-size: 13px; opacity: .65; }
.wp-hero-logo     {
    position: absolute; right: 32px; top: 50%;
    transform: translateY(-50%);
    opacity: .12; font-size: 96px;
}

/* Stat cards */
.wp-stat {
    border-radius: 14px;
    border: none;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    transition: transform .2s, box-shadow .2s;
    height: 100%;
    background: #fff;
}
.wp-stat:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.11); }
.wp-stat-icon {
    width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #fff;
}
.wp-stat-num  { font-size: 26px; font-weight: 700; line-height: 1; color: #2d3748; }
.wp-stat-lbl  { font-size: 12px; color: #718096; margin-top: 2px; }

/* Quick link cards */
.wp-link {
    border-radius: 14px; border: none;
    padding: 20px 18px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    transition: transform .2s, box-shadow .2s;
    height: 100%;
    background: #fff;
    text-decoration: none;
    color: inherit;
    display: block;
}
.wp-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,.11);
    text-decoration: none;
    color: inherit;
}
.wp-link-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff; margin-bottom: 12px;
}
.wp-link-name  { font-size: 14px; font-weight: 600; color: #2d3748; margin-bottom: 3px; }
.wp-link-desc  { font-size: 11.5px; color: #a0aec0; line-height: 1.5; }
.wp-section-title {
    font-size: 12px; font-weight: 700; letter-spacing: 1.4px;
    text-transform: uppercase; color: #a0aec0; margin: 0 0 14px;
}
</style>

<!-- Hero -->
<div class="wp-hero">
    <div class="wp-hero-greeting"><?= $greeting ?></div>
    <div class="wp-hero-name"><?= htmlspecialchars(strtoupper($user)) ?></div>
    <div class="wp-hero-sub">
        PT. Nirwana Alabare Garment &nbsp;·&nbsp; AP Management System &nbsp;·&nbsp;
        <?= date('l, d F Y') ?>
    </div>
    <div class="wp-hero-logo"><i class="fas fa-building-columns"></i></div>
</div>

<!-- Pending stats -->
<div class="wp-section-title">Pending Items</div>
<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3">
        <div class="wp-stat">
            <div class="wp-stat-icon" style="background:linear-gradient(135deg,#1e88e5,#1565c0);">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <div class="wp-stat-num"><?= $stat_bpb ?></div>
                <div class="wp-stat-lbl">BPB Today</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="wp-stat">
            <div class="wp-stat-icon" style="background:linear-gradient(135deg,#f59e0b,#b45309);">
                <i class="fas fa-coins"></i>
            </div>
            <div>
                <div class="wp-stat-num"><?= $stat_kb ?></div>
                <div class="wp-stat-lbl">Kontrabon Draft</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="wp-stat">
            <div class="wp-stat-icon" style="background:linear-gradient(135deg,#10b981,#047857);">
                <i class="fas fa-list-alt"></i>
            </div>
            <div>
                <div class="wp-stat-num"><?= $stat_lp ?></div>
                <div class="wp-stat-lbl">List Payment Draft</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="wp-stat">
            <div class="wp-stat-icon" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <div class="wp-stat-num"><?= $stat_pay ?></div>
                <div class="wp-stat-lbl">Payment Draft</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick links -->
<div class="wp-section-title">Quick Access</div>
<div class="row">
    <?php
    $links = [
        ['AP/kontrabon.php',        'Kontra Bon',      'Manage purchase reimbursements',    'fas fa-coins',             '#f59e0b','#b45309'],
        ['AP/payment.php',          'List Payment',    'View & manage payment lists',        'fas fa-list-alt',          '#10b981','#047857'],
        ['AP/pelunasanftr.php',     'Payment',         'Process supplier payments',          'fas fa-dollar-sign',       '#3949ab','#283593'],
        ['AP/bank-in.php',          'Bank In',         'Record incoming bank transactions',  'fas fa-arrow-circle-down', '#1e88e5','#1565c0'],
        ['AP/bank-out.php',         'Bank Out',        'Record outgoing bank transactions',  'fas fa-arrow-circle-up',   '#ef4444','#b91c1c'],
        ['AP/payment-voucher.php',  'Payment Voucher', 'Create & manage payment vouchers',   'fas fa-file-invoice',      '#8b5cf6','#6d28d9'],
        ['AP/transfer_memo.php',    'Transfer Memo',   'Manage transfer memos',              'fas fa-paper-plane',       '#06b6d4','#0e7490'],
        ['AP/status.php',           'AP Status',       'View payable status overview',       'fas fa-info-circle',       '#64748b','#334155'],
    ];
    foreach ($links as $l):
    ?>
    <div class="col-6 col-md-3 mb-3">
        <a href="<?= htmlspecialchars($l[0]) ?>" class="wp-link">
            <div class="wp-link-icon"
                 style="background:linear-gradient(135deg,<?= $l[4] ?>,<?= $l[5] ?>);">
                <i class="<?= $l[3] ?>"></i>
            </div>
            <div class="wp-link-name"><?= htmlspecialchars($l[1]) ?></div>
            <div class="wp-link-desc"><?= htmlspecialchars($l[2]) ?></div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
