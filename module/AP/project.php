<?php
include '../header.php';

$current_user = isset($_SESSION['username']) ? $_SESSION['username'] : '';

if ($current_user !== 'indro') {
    echo '<div class="container-fluid mt-5 p-4 text-center">
            <i class="fa fa-lock" style="font-size:48px;color:#c0c4cc;"></i>
            <h4 class="mt-3" style="color:#5a6472;">Restricted Page</h4>
            <p style="color:#8a93a2;">You do not have access to this page.</p>
          </div>
          </body></html>';
    exit;
}

$full_name = $current_user;
$fn_res = mysqli_query($conn1, "SELECT FullName FROM userpassword WHERE username = '" . mysqli_real_escape_string($conn1, $current_user) . "'");
if ($fn_row = mysqli_fetch_assoc($fn_res)) {
    if (!empty($fn_row['FullName'])) $full_name = $fn_row['FullName'];
}

// ---------------------------------------------------------------------------
// HARI LIBUR — SATU sumber: tabel mgt_rep_hari_libur pada database yang sama
// (isinya sinkron dgn HRIS hris_nag.ref_hari_libur). TIDAK ada tanggal libur
// yang ditanam di kode: begitu SKB tahun berikutnya terbit dan diinput di sana,
// kalender halaman ini langsung ikut.
//   status_absen: LN = Libur Nasional, CT = Cuti Bersama, LP = Libur Produksi.
// LN & CT dihitung sebagai BUKAN hari kerja. LP hanya ditandai (pabrik libur,
// kantor tetap jalan) sehingga tidak mengurangi jumlah hari kerja.
// Satu tanggal kadang terisi dobel di tabel sumber -> diambil yang paling kuat
// (LN > CT > LP). Kalau tabelnya tidak ada, kalender tetap jalan tanpa libur.
// ---------------------------------------------------------------------------
$holidays = [];
$hq = @mysqli_query($conn2, "SELECT tanggal_libur, nama_hari_libur, status_absen
        FROM mgt_rep_hari_libur
        WHERE deleted_at IS NULL AND tanggal_libur >= '2024-01-01'
        ORDER BY tanggal_libur ASC");
if ($hq) {
    $rank = ['LN' => 3, 'CT' => 2, 'LP' => 1];
    while ($hrow = mysqli_fetch_assoc($hq)) {
        $d  = $hrow['tanggal_libur'];
        $st = strtoupper(trim((string) $hrow['status_absen']));
        $r  = isset($rank[$st]) ? $rank[$st] : 3;      // kode lain/kosong -> anggap libur nasional
        if (isset($holidays[$d]) && $holidays[$d]['r'] >= $r) continue;
        $holidays[$d] = [
            'n' => $hrow['nama_hari_libur'],
            't' => $st === 'CT' ? 'joint' : ($st === 'LP' ? 'production' : 'national'),
            'r' => $r,
        ];
    }
    foreach ($holidays as $d => $v) { unset($holidays[$d]['r']); }
}
?>

<style>
  /* =====================================================================
     Project Dashboard — "Executive Navy" skin.
     Everything is scoped to .proj-page / .modal-project / .detail-drawer so
     the app shell (navbar from header.php) is never restyled.
     ===================================================================== */
  @font-face { font-family: 'PJ Poppins'; src: url('../../fonts/poppins/Poppins-Regular.ttf') format('truetype');   font-weight: 400; font-style: normal; font-display: swap; }
  @font-face { font-family: 'PJ Poppins'; src: url('../../fonts/poppins/Poppins-Medium.ttf') format('truetype');    font-weight: 500; font-style: normal; font-display: swap; }
  @font-face { font-family: 'PJ Poppins'; src: url('../../fonts/poppins/Poppins-SemiBold.ttf') format('truetype');  font-weight: 600; font-style: normal; font-display: swap; }
  @font-face { font-family: 'PJ Poppins'; src: url('../../fonts/poppins/Poppins-Bold.ttf') format('truetype');      font-weight: 700; font-style: normal; font-display: swap; }
  @font-face { font-family: 'PJ Poppins'; src: url('../../fonts/poppins/Poppins-ExtraBold.ttf') format('truetype'); font-weight: 800; font-style: normal; font-display: swap; }

  /* ---------- Design tokens (page + drawer + modal) ---------- */
  .proj-page, .modal-project, .detail-drawer, .drawer-backdrop {
    --pj-font: 'PJ Poppins', 'Segoe UI', system-ui, -apple-system, Roboto, 'Helvetica Neue', Arial, sans-serif;
    --pj-canvas: #f2f5fa;
    --pj-surface: #ffffff;
    --pj-surface-2: #f8fafd;
    --pj-sunken: #edf1f8;
    --pj-line: #e1e7f0;
    --pj-line-soft: #edf1f7;
    --pj-ink: #0f172a;
    --pj-ink-2: #334155;
    --pj-muted: #64748b;
    --pj-faint: #94a3b8;
    --pj-navy: #191970;
    --pj-navy-2: #1e3a8a;
    --pj-blue: #1e90ff;
    --pj-accent: #2451d6;
    --pj-accent-soft: #eef2ff;
    --pj-gold: #a8760f;
    --pj-gold-2: #dcae47;
    --pj-gold-soft: #fbf3df;
    --pj-good: #047857;
    --pj-good-2: #10b981;
    --pj-good-soft: #e5f6ee;
    --pj-warn: #b45309;
    --pj-warn-2: #f59e0b;
    --pj-warn-soft: #fef3e0;
    --pj-bad: #c81e1e;
    --pj-bad-2: #ef4444;
    --pj-bad-soft: #fdecec;
    --pj-th: #1e3a8a;
    --pj-th-hover: #24469f;
    --pj-shadow-sm: 0 1px 2px rgba(15,23,42,.05);
    --pj-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 24px -14px rgba(15,23,42,.16);
    --pj-shadow-lg: 0 2px 6px rgba(15,23,42,.05), 0 30px 60px -28px rgba(15,35,95,.38);
    /* legacy names used by inline styles inside JS templates */
    --ink: var(--pj-ink); --ink-soft: var(--pj-muted); --line: var(--pj-line); --line-soft: var(--pj-line-soft);
    --surface: var(--pj-surface); --surface-2: var(--pj-surface-2); --accent: var(--pj-accent);
  }
  .proj-page[data-theme="dark"],
  .proj-page[data-theme="dark"] ~ .modal-project,
  .proj-page[data-theme="dark"] ~ .detail-drawer,
  .proj-page[data-theme="dark"] ~ .drawer-backdrop {
    --pj-canvas: #0a0f1e;
    --pj-surface: #111931;
    --pj-surface-2: #0f172d;
    --pj-sunken: #0c1328;
    --pj-line: #222e4f;
    --pj-line-soft: #1a2442;
    --pj-ink: #e9eef9;
    --pj-ink-2: #c3cce0;
    --pj-muted: #8f9bb8;
    --pj-faint: #66739a;
    --pj-accent: #82acff;
    --pj-accent-soft: rgba(96,145,255,.13);
    --pj-gold: #e9c46f;
    --pj-gold-2: #dcae47;
    --pj-gold-soft: rgba(220,174,71,.14);
    --pj-good: #4ade9b;
    --pj-good-soft: rgba(52,211,153,.13);
    --pj-warn: #fbbf4d;
    --pj-warn-soft: rgba(251,191,36,.13);
    --pj-bad: #fb8a8a;
    --pj-bad-soft: rgba(248,113,113,.14);
    --pj-th: #17224a;
    --pj-th-hover: #1d2b5c;
    --pj-shadow-sm: 0 1px 2px rgba(0,0,0,.35);
    --pj-shadow: 0 1px 2px rgba(0,0,0,.3), 0 10px 26px -14px rgba(0,0,0,.6);
    --pj-shadow-lg: 0 2px 6px rgba(0,0,0,.35), 0 30px 60px -26px rgba(0,0,0,.8);
  }

  .proj-page {
    font-family: var(--pj-font); color: var(--pj-ink); background: var(--pj-canvas);
    padding: 22px 24px 44px; min-height: calc(100vh - 56px);
    -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
    transition: background-color .25s ease;
  }
  .proj-page b, .detail-drawer b, .modal-project b { font-weight: 600; }

  @keyframes projFadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
  .view-fade-in { animation: projFadeUp .25s ease both; }
  .proj-card-enter { animation: projFadeUp .32s ease both; }

  /* ---------- Confetti (celebrates a project hitting Done) ---------- */
  .confetti-piece { position: fixed; top: -12px; width: 8px; height: 14px; z-index: 9999; pointer-events: none; animation-name: confettiFall; animation-timing-function: linear; animation-fill-mode: forwards; }
  @keyframes confettiFall { to { transform: translateY(105vh) rotate(var(--rot, 720deg)); opacity: .85; } }

  /* ---------- Hero ---------- */
  .proj-hero {
    position: relative; overflow: hidden; isolation: isolate; color: #fff;
    border-radius: 20px; padding: 26px 30px 92px;
    background:
      radial-gradient(560px 280px at 94% -18%, rgba(30,144,255,.50), rgba(30,144,255,0) 70%),
      radial-gradient(460px 240px at 48% 135%, rgba(79,70,229,.30), rgba(79,70,229,0) 70%),
      linear-gradient(112deg, #070b22 0%, #10185a 40%, #172e84 72%, #1b56c2 100%);
    box-shadow: 0 22px 44px -30px rgba(16,24,90,.85);
  }
  .proj-hero::before {
    content: ''; position: absolute; inset: 0; z-index: -1; pointer-events: none;
    background-image: linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
    background-size: 34px 34px;
    -webkit-mask-image: radial-gradient(ellipse 70% 90% at 78% 0%, #000 0%, transparent 72%);
            mask-image: radial-gradient(ellipse 70% 90% at 78% 0%, #000 0%, transparent 72%);
  }
  .hero-orbit {
    position: absolute; right: -90px; top: -150px; width: 420px; height: 420px; border-radius: 50%; z-index: -1; pointer-events: none;
    border: 1px solid rgba(255,255,255,.10);
    box-shadow: 0 0 0 70px rgba(255,255,255,.022), 0 0 0 140px rgba(255,255,255,.016);
  }
  .hero-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
  .hero-brand { display: flex; align-items: flex-start; gap: 18px; min-width: 0; }
  .hero-mark {
    width: 54px; height: 54px; border-radius: 16px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    font-size: 22px; color: #fff; margin-top: 2px;
    background: linear-gradient(145deg, rgba(255,255,255,.22), rgba(255,255,255,.05));
    border: 1px solid rgba(255,255,255,.22);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.25), 0 12px 26px -12px rgba(0,0,0,.7);
  }
  .proj-hero .eyebrow { display: flex; align-items: center; gap: 8px; font-size: 10.5px; font-weight: 600; letter-spacing: .16em; text-transform: uppercase; color: rgba(255,255,255,.72); }
  .eyebrow-mark { width: 18px; height: 2px; border-radius: 2px; background: #dcae47; display: inline-block; }
  .proj-hero .hero-title { margin: 4px 0 0; font-family: var(--pj-font); font-weight: 700; font-size: 30px; line-height: 1.15; letter-spacing: -.015em; color: #fff; }
  .proj-hero .hero-sub { margin: 8px 0 0; font-size: 13.5px; color: rgba(255,255,255,.78); max-width: 660px; line-height: 1.55; }
  .proj-hero .hero-sub b { color: #fff; font-weight: 600; }
  .hero-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; min-height: 28px; }
  .hero-chip {
    display: inline-flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 500; color: rgba(255,255,255,.86);
    padding: 5px 11px; border-radius: 999px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); white-space: nowrap;
  }
  .hero-chip i { font-size: 11px; color: rgba(255,255,255,.6); }
  .hero-chip b { color: #fff; font-weight: 600; }
  .hero-chip.gold { background: rgba(220,174,71,.14); border-color: rgba(220,174,71,.45); color: #f6dc9c; }
  .hero-chip.gold i { color: #f0c65f; }
  .hero-actions { display: flex; align-items: center; gap: 10px; padding-top: 4px; }
  .theme-toggle {
    width: 40px; height: 40px; border-radius: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18); color: #fff; font-size: 15px !important;
    transition: background .15s ease, border-color .15s ease;
  }
  .theme-toggle:hover { background: rgba(255,255,255,.16); border-color: rgba(255,255,255,.3); }
  .btn-hero-new {
    display: inline-flex; align-items: center; gap: 9px; height: 40px; padding: 0 18px 0 11px; border-radius: 12px; border: none; cursor: pointer;
    background: #fff; color: #10185a; font-weight: 600; font-size: 13px !important; letter-spacing: .005em;
    box-shadow: 0 10px 24px -10px rgba(0,0,0,.55), inset 0 -1px 0 rgba(16,24,90,.12);
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-hero-new i { width: 22px; height: 22px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #191970, #1e90ff); color: #fff; font-size: 10px; }
  .btn-hero-new:hover { transform: translateY(-1px); box-shadow: 0 14px 28px -10px rgba(0,0,0,.6); color: #0b1140; }
  .btn-hero-new:focus, .theme-toggle:focus { outline: 2px solid rgba(255,255,255,.7); outline-offset: 2px; }

  /* ---------- Delivery Performance band (overlaps the hero) ---------- */
  .perf-card {
    position: relative; z-index: 2; margin: -66px 18px 0;
    background: var(--pj-surface); border: 1px solid var(--pj-line); border-radius: 18px;
    box-shadow: var(--pj-shadow-lg);
  }
  .perf-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 13px 22px 12px; border-bottom: 1px solid var(--pj-line-soft); }
  .perf-title { display: flex; align-items: center; gap: 10px; font-size: 13.5px; font-weight: 600; color: var(--pj-ink); flex-wrap: wrap; }
  .perf-title .ico { width: 26px; height: 26px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; color: #fff; background: linear-gradient(135deg, #191970, #1e90ff); }
  .perf-scope { font-size: 11.5px; font-weight: 400; color: var(--pj-muted); padding-left: 11px; border-left: 1px solid var(--pj-line); }
  .perf-flags { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .perf-flag { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 999px; background: var(--pj-accent-soft); color: var(--pj-accent); white-space: nowrap; }
  .perf-flag.gold { background: var(--pj-gold-soft); color: var(--pj-gold); box-shadow: inset 0 0 0 1px rgba(220,174,71,.38); }
  .perf-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); }
  .kpi { position: relative; padding: 16px 20px 18px; min-width: 0; }
  .kpi + .kpi::before { content: ''; position: absolute; left: 0; top: 18px; bottom: 18px; width: 1px; background: var(--pj-line-soft); }
  .kpi-label { display: flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 500; color: var(--pj-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .kpi-label i { font-size: 11px; color: var(--pj-faint); width: 13px; text-align: center; }
  .kpi-value { margin-top: 6px; display: flex; align-items: baseline; gap: 6px; font-size: 30px; font-weight: 600; line-height: 1.05; letter-spacing: -.02em; color: var(--pj-ink); white-space: nowrap; }
  .kpi-unit { font-size: 13px; font-weight: 500; letter-spacing: 0; color: var(--pj-muted); }
  .kpi-foot { margin-top: 6px; font-size: 11.5px; color: var(--pj-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .kpi-foot b { color: var(--pj-ink-2); font-weight: 600; }
  .kpi-visual { margin-top: 12px; height: 22px; display: flex; align-items: flex-end; }
  .kpi-meter { width: 100%; height: 6px; border-radius: 999px; background: var(--pj-sunken); overflow: hidden; align-self: center; }
  .kpi-meter span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #191970, #1e90ff); }
  .kpi-meter.gold span { background: linear-gradient(90deg, #b8871b, #f0c65f); }
  .kpi.lead .kpi-value { color: var(--pj-navy-2); }
  .proj-page[data-theme="dark"] .kpi.lead .kpi-value { color: #fff; }
  .kpi.lead .kpi-label i { color: var(--pj-accent); }
  .kpi.gold .kpi-label i { color: var(--pj-gold-2); }
  .kpi-segbar { width: 100%; display: flex; gap: 2px; height: 6px; align-self: center; }
  .kpi-segbar span { height: 100%; border-radius: 2px; }
  .kpi-segbar span:first-child { border-radius: 999px 2px 2px 999px; }
  .kpi-segbar span:last-child { border-radius: 2px 999px 999px 2px; }
  .kpi-spark { display: flex; align-items: flex-end; gap: 4px; height: 22px; width: 100%; }
  .kpi-spark span { flex: 1; max-width: 18px; min-height: 2px; border-radius: 3px 3px 0 0; background: var(--pj-line); }
  .kpi-spark span.cur { background: linear-gradient(180deg, #1e90ff, #2451d6); }
  .kpi-spark span.prev { background: #9fb3e6; }
  .proj-page[data-theme="dark"] .kpi-spark span { background: #2a3760; }
  .proj-page[data-theme="dark"] .kpi-spark span.prev { background: #4a5f9c; }
  .proj-page[data-theme="dark"] .kpi-spark span.cur { background: linear-gradient(180deg, #4aa8ff, #2f6fe8); }
  .kpi-delta { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 2px 7px; border-radius: 999px; letter-spacing: 0; align-self: center; }
  .kpi-delta.up   { background: var(--pj-good-soft); color: var(--pj-good); }
  .kpi-delta.down, .kpi-delta.flat { background: var(--pj-sunken); color: var(--pj-muted); }
  .kpi-live { display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #1e90ff; box-shadow: 0 0 0 3px rgba(30,144,255,.2); align-self: center; }

  /* ---------- Section bar (selection scope + module shortcuts) ---------- */
  .section-bar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin: 26px 2px 12px; }
  .section-heading { display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; min-width: 0; }
  .section-kicker { font-size: 15px; font-weight: 600; color: var(--pj-ink); letter-spacing: -.005em; }
  .stat-scope { font-size: 12px; color: var(--pj-muted); font-weight: 400; }
  .stat-scope b { color: var(--pj-ink); font-weight: 600; }
  .module-summary { display: flex; gap: 8px; overflow-x: auto; padding: 3px; scrollbar-width: none; }
  .module-summary::-webkit-scrollbar { display: none; }
  .mod-sum-card {
    flex: 0 0 auto; display: flex; align-items: center; gap: 9px; padding: 5px 6px 5px 6px; border-radius: 999px; cursor: pointer; user-select: none;
    background: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: var(--pj-shadow-sm);
    transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
  }
  .mod-sum-card:hover { border-color: var(--mc); transform: translateY(-1px); box-shadow: 0 6px 16px -10px rgba(15,23,42,.35); }
  .mod-sum-card.active { border-color: var(--mc); box-shadow: 0 0 0 3px var(--mcs), var(--pj-shadow-sm); }
  .proj-page[data-theme="dark"] .mod-sum-card.active { box-shadow: 0 0 0 2px var(--mc); }
  .mod-sum-icon { width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #fff; background: var(--mc); flex-shrink: 0; }
  .mod-sum-name { font-size: 12px; font-weight: 600; color: var(--pj-ink); line-height: 1.15; white-space: nowrap; }
  .mod-sum-meta { font-size: 10.5px; color: var(--pj-muted); line-height: 1.2; white-space: nowrap; }
  .mod-sum-ring { --deg: 0deg; margin-left: 4px; width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: conic-gradient(var(--mc) var(--deg), var(--pj-line-soft) 0); }
  .mod-sum-ring span { width: 22px; height: 22px; border-radius: 50%; background: var(--pj-surface); display: flex; align-items: center; justify-content: center; font-size: 7.5px; font-weight: 700; color: var(--pj-ink-2); letter-spacing: -.03em; }

  /* ---------- Stat tiles (reflect current filters) ---------- */
  .stat-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
  .stat-tile {
    position: relative; overflow: hidden; height: 100%; padding: 15px 16px 14px; border-radius: 14px;
    background: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: var(--pj-shadow);
    transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease;
    animation: projFadeUp .4s ease both;
  }
  .stat-grid > .stat-tile:nth-child(2) { animation-delay: 40ms; }
  .stat-grid > .stat-tile:nth-child(3) { animation-delay: 80ms; }
  .stat-grid > .stat-tile:nth-child(4) { animation-delay: 120ms; }
  .stat-grid > .stat-tile:nth-child(5) { animation-delay: 160ms; }
  .stat-grid > .stat-tile:nth-child(6) { animation-delay: 200ms; }
  .stat-tile::before, .stat-tile::after { content: none; }
  .stat-tile:hover { transform: translateY(-2px); border-color: #cdd7e8; box-shadow: 0 2px 4px rgba(15,23,42,.04), 0 16px 30px -18px rgba(15,23,42,.35); }
  .proj-page[data-theme="dark"] .stat-tile:hover { border-color: #33416b; }
  .tile-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
  .stat-tile .lbl { font-size: 12px; font-weight: 500; color: var(--pj-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .stat-tile .icon-badge { width: 30px; height: 30px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; color: var(--tint); background: var(--tint-soft); }
  .stat-tile .num { margin-top: 6px; display: flex; align-items: center; font-size: 28px; font-weight: 600; line-height: 1.1; letter-spacing: -.02em; color: var(--pj-ink); }
  .tile-foot { margin-top: 10px; display: flex; align-items: center; gap: 10px; }
  .tile-meter { flex: 1; height: 4px; border-radius: 999px; background: var(--pj-sunken); overflow: hidden; }
  .tile-meter span { display: block; height: 100%; width: 0; border-radius: 999px; background: var(--tint-bar, var(--tint)); transition: width .45s ease; }
  .tile-note { font-size: 11px; color: var(--pj-muted); white-space: nowrap; }
  .tile-note .fa { color: var(--pj-good-2); margin-right: 3px; }
  .stat-tile.wm-total    { --tint: #1e3a8a; --tint-soft: #e8eefc; --tint-bar: linear-gradient(90deg,#191970,#1e90ff); }
  .stat-tile.wm-progress { --tint: #2451d6; --tint-soft: #e6eefe; }
  .stat-tile.wm-live     { --tint: #7c3aed; --tint-soft: #ede9fe; }
  .stat-tile.wm-done     { --tint: #059669; --tint-soft: #e2f5ec; }
  .stat-tile.wm-overdue  { --tint: #dc2626; --tint-soft: #fdeaea; }
  .proj-page[data-theme="dark"] .stat-tile.wm-total    { --tint: #8fb2ff; --tint-soft: rgba(96,145,255,.14); }
  .proj-page[data-theme="dark"] .stat-tile.wm-progress { --tint: #7fb0ff; --tint-soft: rgba(96,145,255,.14); }
  .proj-page[data-theme="dark"] .stat-tile.wm-live     { --tint: #c4b5fd; --tint-soft: rgba(167,139,250,.15); }
  .proj-page[data-theme="dark"] .stat-tile.wm-done     { --tint: #34d399; --tint-soft: rgba(52,211,153,.13); }
  .proj-page[data-theme="dark"] .stat-tile.wm-overdue  { --tint: #f87171; --tint-soft: rgba(248,113,113,.14); }
  .tile-rate {
    display: flex; align-items: center; justify-content: space-between; gap: 10px; color: #fff; border-color: transparent;
    background: radial-gradient(160px 120px at 100% 0%, rgba(30,144,255,.55), rgba(30,144,255,0) 70%), linear-gradient(135deg, #0d1450 0%, #1a2f86 60%, #1f52b8 100%);
    box-shadow: 0 16px 30px -18px rgba(25,25,112,.8);
  }
  .tile-rate:hover { border-color: transparent; }
  .tile-rate .sheen { position: absolute; inset: 0; pointer-events: none; background-image: linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px); background-size: 22px 22px; -webkit-mask-image: linear-gradient(120deg, transparent 30%, #000 100%); mask-image: linear-gradient(120deg, transparent 30%, #000 100%); }
  .tile-rate .lbl { color: rgba(255,255,255,.75); position: relative; }
  .tile-rate .num { color: #fff; position: relative; }
  .tile-rate .tile-note { color: rgba(255,255,255,.7); position: relative; display: block; margin-top: 12px; }
  .ring {
    --deg: 0deg; position: relative; width: 58px; height: 58px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    background: conic-gradient(#8ec5ff var(--deg), rgba(255,255,255,.16) 0);
  }
  .ring span { width: 46px; height: 46px; border-radius: 50%; background: #12206a; display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 600; color: #fff; }

  .live-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #1e90ff; margin-left: 10px; box-shadow: 0 0 0 3px rgba(30,144,255,.2); animation: livePulse 1.8s ease-in-out infinite; }
  @keyframes livePulse { 0%,100% { opacity: 1; } 50% { opacity: .35; } }
  #overdue-tile.overdue-alert { border-color: rgba(239,68,68,.55); box-shadow: 0 0 0 3px rgba(239,68,68,.12), var(--pj-shadow); }

  /* ---------- Toolbar ---------- */
  .proj-toolbar {
    position: sticky; top: 8px; z-index: 30; padding: 8px 10px; border-radius: 14px;
    background: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: var(--pj-shadow);
  }
  .toolbar-row { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; }
  .toolbar-filters { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
  .toolbar-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }
  .tb-divider { width: 1px; height: 24px; background: var(--pj-line); flex-shrink: 0; }
  .view-switch, .status-seg { display: inline-flex; align-items: center; gap: 2px; padding: 3px; border-radius: 11px; background: var(--pj-sunken); }
  .view-switch button {
    display: flex; align-items: center; gap: 7px; height: 30px; padding: 0 12px; border: none; border-radius: 8px; cursor: pointer; background: transparent;
    color: var(--pj-muted); font-size: 12.5px !important; font-weight: 500; white-space: nowrap; transition: color .15s ease, background .15s ease, box-shadow .15s ease;
  }
  .view-switch button i { font-size: 12px; opacity: .85; }
  .view-switch button:hover { color: var(--pj-ink); }
  .view-switch button:focus { outline: none; box-shadow: 0 0 0 2px rgba(36,81,214,.35); }
  .view-switch button.active { background: var(--pj-surface); color: var(--pj-navy-2); font-weight: 600; box-shadow: 0 1px 2px rgba(15,23,42,.08), 0 2px 8px -2px rgba(15,23,42,.12); }
  .view-switch button.active i { color: var(--pj-accent); opacity: 1; }
  .proj-page[data-theme="dark"] .view-switch button.active { background: #1c2749; color: #fff; }
  .proj-filter-pill {
    display: inline-flex; align-items: center; height: 30px; padding: 0 11px; border-radius: 8px; cursor: pointer; user-select: none; white-space: nowrap;
    font-size: 12.5px; font-weight: 500; color: var(--pj-muted); transition: color .15s ease, background .15s ease;
  }
  .proj-filter-pill:hover { color: var(--pj-ink); }
  .proj-filter-pill.active { background: linear-gradient(90deg, #191970, #1e56c8); color: #fff; font-weight: 600; box-shadow: 0 4px 10px -4px rgba(25,25,112,.55); }
  .select-wrap { position: relative; display: inline-flex; align-items: center; }
  .select-wrap > i { position: absolute; left: 11px; font-size: 11px; color: var(--pj-faint); pointer-events: none; z-index: 1; }
  .proj-toolbar select.form-control {
    height: 36px; padding: 0 30px 0 30px; border-radius: 10px; font-size: 12.5px; font-weight: 500; color: var(--pj-ink-2); cursor: pointer;
    background-color: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: none;
    -webkit-appearance: none; -moz-appearance: none; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%2394a3b8' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round' d='M1 1.5l5 5 5-5'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 11px center; background-size: 10px;
  }
  .proj-toolbar select.form-control:hover { border-color: #c9d4e6; }
  .proj-toolbar select.form-control:focus, .search-wrap input:focus { outline: none; border-color: #7aa2f7; box-shadow: 0 0 0 3px rgba(36,81,214,.14); }
  .proj-page[data-theme="dark"] .proj-toolbar select.form-control { background-color: var(--pj-surface-2); }
  .proj-page[data-theme="dark"] .proj-toolbar select.form-control:hover { border-color: #34436e; }
  .proj-page[data-theme="dark"] .proj-toolbar select option { background: #111931; color: #e9eef9; }
  .clear-filters-link { display: none; align-items: center; gap: 5px; height: 30px; padding: 0 8px; border-radius: 8px; font-size: 12px; font-weight: 500; color: var(--pj-muted); cursor: pointer; white-space: nowrap; }
  .clear-filters-link:hover { color: var(--pj-bad); background: var(--pj-bad-soft); }
  .clear-filters-link.show { display: inline-flex; }
  .search-wrap { position: relative; }
  .search-wrap .fa-search { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 11px; color: var(--pj-faint); pointer-events: none; }
  .search-wrap input {
    width: 210px; height: 36px; padding: 0 12px 0 32px; border-radius: 10px; font-size: 12.5px; color: var(--pj-ink);
    background: var(--pj-surface-2); border: 1px solid var(--pj-line); transition: border-color .15s ease, box-shadow .15s ease;
  }
  .search-wrap input::placeholder { color: var(--pj-faint); }
  .btn-export {
    display: inline-flex; align-items: center; gap: 8px; height: 36px; padding: 0 14px; border-radius: 10px; white-space: nowrap; cursor: pointer;
    background: var(--pj-surface); color: var(--pj-ink-2); border: 1px solid var(--pj-line); font-size: 12.5px !important; font-weight: 600;
    transition: border-color .15s ease, color .15s ease, box-shadow .15s ease;
  }
  .btn-export i { color: #169b62; font-size: 13px; }
  .btn-export:hover { border-color: #b9c7e0; color: var(--pj-ink); box-shadow: 0 6px 14px -10px rgba(15,23,42,.45); }
  .proj-page[data-theme="dark"] .btn-export { background: var(--pj-surface-2); }
  .proj-page[data-theme="dark"] .btn-export i { color: #34d399; }

  /* Kolom Description di Table: teks panjang dipotong 2 baris, lengkap di tooltip. */
  .proj-table td.desc-cell { white-space: normal; max-width: 340px; min-width: 200px; }
  .proj-table .desc-clamp {
    font-size: 11.5px; line-height: 1.5; color: var(--pj-muted);
    display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  }
  /* Pill status: bisa pilih lebih dari satu, yang aktif diberi centang. */
  .proj-filter-pill.active::before {
    content: '\f00c'; font-family: 'Font Awesome 5 Free'; font-weight: 900; font-size: 8px; margin-right: 6px; opacity: .9;
  }

  /* ---------- Loading & empty states ---------- */
  .view-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 90px 0; color: var(--pj-muted); font-size: 13px; }
  .view-loading .spinner { width: 32px; height: 32px; border-radius: 50%; border: 3px solid var(--pj-line); border-top-color: var(--pj-accent); animation: projSpin .7s linear infinite; margin-bottom: 12px; }
  @keyframes projSpin { to { transform: rotate(360deg); } }
  .empty-state { text-align: center; padding: 64px 16px; color: var(--pj-muted); background: var(--pj-surface); border: 1px dashed var(--pj-line); border-radius: 16px; }
  .empty-state .es-ico { width: 56px; height: 56px; margin: 0 auto 12px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--pj-accent); background: var(--pj-accent-soft); }
  .empty-state .es-title { font-size: 15px; font-weight: 600; color: var(--pj-ink); }
  .empty-state .es-sub { font-size: 12.5px; margin-top: 4px; }

  /* ---------- Badges ---------- */
  .proj-page .badge, .detail-drawer .badge, .modal-project .badge { font-weight: 600; font-size: 10.5px; line-height: 1.25; padding: 3px 8px; border-radius: 999px; letter-spacing: .01em; vertical-align: middle; }
  .badge-priority-High, .badge-priority-Medium, .badge-priority-Low { background: var(--pj-sunken); color: var(--pj-ink-2); display: inline-flex !important; align-items: center; gap: 5px; }
  .badge-priority-High::before, .badge-priority-Medium::before, .badge-priority-Low::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--pj-faint); }
  .badge-priority-High   { background: var(--pj-bad-soft); color: var(--pj-bad); }
  .badge-priority-High::before   { background: var(--pj-bad-2); }
  .badge-priority-Medium::before { background: var(--pj-warn-2); }
  .badge-status-Planned    { background: var(--pj-sunken); color: var(--pj-muted); }
  .badge-status-OnProgress { background: var(--pj-accent-soft); color: var(--pj-accent); }
  .badge-status-Live       { background: #ede9fe; color: #6d28d9; }
  .badge-status-Done       { background: var(--pj-good-soft); color: var(--pj-good); }
  .badge-ontime { background: var(--pj-good-soft); color: var(--pj-good); }
  .badge-late   { background: var(--pj-bad-soft); color: var(--pj-bad); }
  .badge-ontime .fa, .badge-late .fa { font-size: 9px; margin-right: 2px; }
  .badge-at-risk { background: var(--pj-warn-soft); color: var(--pj-warn); box-shadow: inset 0 0 0 1px rgba(245,158,11,.4); }
  .overdue-flag { color: var(--pj-bad); font-weight: 600; }

  /* ---------- Requester chip & small buttons ---------- */
  .pic-avatar { width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 600; color: #fff; flex-shrink: 0; letter-spacing: .02em; box-shadow: 0 0 0 2px var(--pj-surface); }
  .pic-chip { display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 500; color: var(--pj-ink-2); white-space: nowrap; }
  .pic-chip.unassigned { color: var(--pj-faint); font-style: italic; }
  .pic-chip-label { font-size: 10px; font-weight: 500; color: var(--pj-faint); }
  .pic-chip .pic-avatar { width: 20px; height: 20px; font-size: 8.5px; }
  .tl-row-label .pic-avatar { width: 18px; height: 18px; font-size: 7.5px; box-shadow: none; }
  .icon-btn { width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--pj-line); background: var(--pj-surface); color: var(--pj-muted); display: inline-flex; align-items: center; justify-content: center; font-size: 11px !important; cursor: pointer; transition: background .15s ease, color .15s ease, border-color .15s ease; }
  .icon-btn:hover { background: var(--pj-accent-soft); color: var(--pj-accent); border-color: transparent; }
  .icon-btn.danger:hover { background: var(--pj-bad-soft); color: var(--pj-bad); border-color: transparent; }

  /* ---------- Card (board) ---------- */
  .proj-card {
    position: relative; display: flex; flex-direction: column; padding: 13px 14px 11px; border-radius: 12px; cursor: pointer; flex-shrink: 0;
    background: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: var(--pj-shadow-sm);
    transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease;
  }
  .proj-card:hover { transform: translateY(-2px); border-color: #c5d2ea; box-shadow: 0 2px 4px rgba(15,23,42,.04), 0 14px 28px -16px rgba(15,23,42,.38); }
  .proj-page[data-theme="dark"] .proj-card:hover { border-color: #34436e; }
  .proj-card .pc-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
  .proj-card .pc-flags { display: inline-flex; align-items: center; gap: 5px; }
  .proj-card .cat, .detail-drawer .cat, .prev-card .cat {
    display: inline-flex; align-items: center; gap: 6px; font-size: 10.5px; font-weight: 600; letter-spacing: .03em; line-height: 1.3;
    padding: 2px 9px 2px 7px; border-radius: 999px; color: var(--pj-ink-2); background: var(--pj-sunken);
  }
  .prev-card .cat { padding: 2px 9px; }
  .cat-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--mod-color, var(--pj-faint)); flex-shrink: 0; }
  .proj-card .name { font-size: 13.5px; font-weight: 600; line-height: 1.35; color: var(--pj-ink); margin: 9px 0 3px; }
  .proj-card .desc { font-size: 11.5px; line-height: 1.5; color: var(--pj-muted); display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .pc-progress { display: flex; align-items: center; gap: 10px; margin-top: 11px; }
  .proj-card .progress, .prev-card .progress { flex: 1; height: 5px; border-radius: 999px; background: var(--pj-sunken); overflow: hidden; }
  .proj-card .progress-bar, .prev-card .progress-bar { border-radius: 999px; transition: width .4s ease; }
  .proj-card .pct { font-size: 11px; font-weight: 600; color: var(--pj-ink-2); min-width: 30px; text-align: right; }
  .proj-card .meta-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 9px; font-size: 11px; color: var(--pj-muted); white-space: nowrap; }
  .proj-card .meta-row .fa { color: var(--pj-faint); margin-right: 5px; }
  .proj-card .meta-row .delivered .fa { color: var(--pj-good-2); }
  .proj-card .footer-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-top: 10px; padding-top: 9px; border-top: 1px solid var(--pj-line-soft); }
  .proj-card .card-actions { display: inline-flex; gap: 4px; opacity: .55; transition: opacity .15s ease; }
  .proj-card:hover .card-actions { opacity: 1; }
  .proj-card .icon-btn { width: 25px; height: 25px; font-size: 10px !important; }
  .proj-card[draggable="true"] { cursor: grab; }
  .proj-card.dragging { opacity: .4; cursor: grabbing; }

  /* ---------- Board (kanban) ---------- */
  .board-wrap { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(270px, 1fr); gap: 14px; overflow-x: auto; padding-bottom: 6px; align-items: start; }
  .board-col { min-width: 0; padding: 12px 8px 8px; border-radius: 16px; background: var(--pj-sunken); box-shadow: inset 0 3px 0 var(--col, #94a3b8); }
  .proj-page[data-theme="dark"] .board-col { background: #0d1429; box-shadow: inset 0 3px 0 var(--col, #94a3b8), inset 0 0 0 1px var(--pj-line-soft); }
  .board-col-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 2px 6px 11px; }
  .board-col-head .title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--pj-ink); white-space: nowrap; }
  .board-col-head .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--col); }
  .board-col-head .count { min-width: 24px; height: 22px; padding: 0 8px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: var(--pj-ink-2); background: var(--pj-surface); box-shadow: var(--pj-shadow-sm); }
  .board-col-sub { font-size: 11px; color: var(--pj-muted); font-weight: 400; }
  .board-col-body {
    display: flex; flex-direction: column; gap: 9px; min-height: 90px; max-height: max(360px, calc(100vh - 340px)); overflow-y: auto;
    padding: 2px 4px 4px; border-radius: 12px; scrollbar-width: thin; scrollbar-color: var(--pj-line) transparent;
  }
  .board-col-body::-webkit-scrollbar { width: 6px; }
  .board-col-body::-webkit-scrollbar-thumb { background: var(--pj-line); border-radius: 999px; }
  .board-col-body.drop-target { background: var(--pj-accent-soft); outline: 2px dashed #7aa2f7; outline-offset: -2px; }
  .board-empty-hint {
    flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; text-align: center;
    min-height: 150px; padding: 18px 12px; border-radius: 12px; border: 1px dashed #cdd8ea; color: var(--pj-muted); font-size: 11.5px;
  }
  .proj-page[data-theme="dark"] .board-empty-hint { border-color: #26335a; }
  .board-empty-hint .ico { width: 36px; height: 36px; margin-bottom: 7px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--col); background: var(--pj-surface); box-shadow: var(--pj-shadow-sm); }
  .board-empty-hint b { font-size: 12px; font-weight: 600; color: var(--pj-ink-2); }

  /* ---------- Table ---------- */
  .proj-table-wrap {
    background: var(--pj-surface); border: 1px solid var(--pj-line); border-radius: 14px; overflow: auto; box-shadow: var(--pj-shadow);
    max-height: calc(100vh - 300px); min-height: 240px; scrollbar-width: thin; scrollbar-color: var(--pj-line) transparent;
  }
  .proj-table-wrap::-webkit-scrollbar { width: 7px; height: 7px; }
  .proj-table-wrap::-webkit-scrollbar-thumb { background: var(--pj-line); border-radius: 999px; }
  .proj-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 12.5px; }
  .proj-table thead th {
    position: sticky; top: 0; z-index: 5; padding: 12px 14px; text-align: left; white-space: nowrap; cursor: pointer; user-select: none;
    background: var(--pj-th); color: rgba(255,255,255,.82); font-size: 10.5px; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; border: 0;
  }
  .proj-table thead th:hover { background: var(--pj-th-hover); color: #fff; }
  .proj-table thead th.no-sort { cursor: default; }
  .proj-table thead th.no-sort:hover { background: var(--pj-th); color: rgba(255,255,255,.82); }
  .sort-arrow { font-size: 8px; margin-left: 5px; opacity: .35; }
  .sort-arrow.active { opacity: 1; color: #9cc3ff; }
  .proj-table tbody td { padding: 10px 14px; color: var(--pj-ink-2); vertical-align: middle; white-space: nowrap; border-bottom: 1px solid var(--pj-line-soft); background: var(--pj-surface); transition: background .12s ease; }
  .proj-table tbody tr:nth-child(even) td { background: var(--pj-surface-2); }
  .proj-table tbody tr:hover td { background: var(--pj-accent-soft); }
  .proj-table tbody tr:last-child td { border-bottom: 0; }
  .proj-table tbody td.wrap { white-space: normal; min-width: 200px; }
  .proj-table td.num-cell { color: var(--pj-faint); font-size: 11.5px; width: 44px; }
  .proj-table .proj-name { font-weight: 600; color: var(--pj-ink); }
  .proj-table .mod-tag { display: inline-flex; align-items: center; gap: 7px; font-size: 11.5px; font-weight: 600; color: var(--pj-ink-2); }
  .mod-tag .dot { width: 8px; height: 8px; border-radius: 3px; background: var(--mc, #94a3b8); flex-shrink: 0; }
  .list-progress { width: 72px; height: 5px; border-radius: 999px; background: var(--pj-sunken); display: inline-block; vertical-align: middle; overflow: hidden; }
  .list-progress .bar { display: block; height: 100%; border-radius: 999px; }
  .list-pct { font-size: 11px; font-weight: 600; color: var(--pj-ink-2); margin-left: 6px; }
  .proj-table .date-cell { font-variant-numeric: tabular-nums; }
  .proj-table .muted-dash { color: var(--pj-faint); }
  .status-select {
    height: 26px; border: none; border-radius: 999px; font-size: 11px; font-weight: 600; padding: 0 24px 0 10px; cursor: pointer;
    -webkit-appearance: none; -moz-appearance: none; appearance: none; font-family: inherit;
    background-repeat: no-repeat; background-position: right 9px center; background-size: 8px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='M1 1.5l5 5 5-5'/%3E%3C/svg%3E");
  }
  .status-select.ss-Planned    { background-color: var(--pj-sunken); color: var(--pj-muted); }
  .status-select.ss-OnProgress { background-color: var(--pj-accent-soft); color: var(--pj-accent); }
  .status-select.ss-Live       { background-color: #ede9fe; color: #6d28d9; }
  .status-select.ss-Done       { background-color: var(--pj-good-soft); color: var(--pj-good); }
  .status-select option { background: var(--pj-surface); color: var(--pj-ink); }
  .status-select:focus { outline: 2px solid #7aa2f7; outline-offset: 1px; }

  /* ---------- Calendar ---------- */
  .cal-card, .tl-card { background: var(--pj-surface); border: 1px solid var(--pj-line); border-radius: 16px; overflow: hidden; box-shadow: var(--pj-shadow); }
  .cal-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 14px 18px; border-bottom: 1px solid var(--pj-line-soft); }
  .cal-toolbar .title { font-size: 17px; font-weight: 600; color: var(--pj-ink); letter-spacing: -.01em; }
  .cal-toolbar .cal-sub { font-size: 12px; font-weight: 400; color: var(--pj-muted); margin-left: 10px; letter-spacing: 0; }
  .cal-nav { display: flex; align-items: center; gap: 6px; }
  .cal-nav button { height: 32px; border: 1px solid var(--pj-line); background: var(--pj-surface); color: var(--pj-ink-2); border-radius: 9px; font-weight: 500; cursor: pointer; transition: background .15s ease, border-color .15s ease; }
  .cal-nav button:hover { background: var(--pj-accent-soft); border-color: transparent; color: var(--pj-accent); }
  .cal-nav .nav-arrow { width: 32px; font-size: 11px !important; }
  .cal-nav .today-btn { padding: 0 14px; font-size: 12px !important; }
  .status-legend { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; padding: 9px 18px; border-bottom: 1px solid var(--pj-line-soft); background: var(--pj-surface-2); font-size: 11.5px; font-weight: 500; color: var(--pj-muted); }
  .status-legend .lg-item { display: flex; align-items: center; gap: 7px; }
  .status-legend .lg-bar { width: 18px; height: 10px; border-radius: 3px; border-left: 3px solid; background: var(--pj-sunken); display: inline-block; }
  .status-legend .lg-overdue { color: var(--pj-bad); }
  .status-legend .lg-overdue .lg-bar { border-left-color: #ef4444; box-shadow: 0 0 0 2px rgba(239,68,68,.35); }
  .cal-dow-row { display: grid; grid-template-columns: repeat(7, 1fr); border-bottom: 1px solid var(--pj-line); background: var(--pj-surface-2); }
  .cal-dow { text-align: left; padding: 9px 10px; font-size: 10.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--pj-muted); }
  .cal-week { position: relative; display: grid; grid-template-columns: repeat(7, 1fr); border-bottom: 1px solid var(--pj-line-soft); }
  .cal-week:last-child { border-bottom: none; }
  .cal-daycell { padding: 7px 10px 8px; border-right: 1px solid var(--pj-line-soft); }
  .cal-daycell:last-child { border-right: none; }
  .cal-daycell.outside { background: var(--pj-surface-2); }
  .cal-daynum { font-size: 11.5px; font-weight: 500; color: var(--pj-ink-2); text-align: left; }
  .cal-daycell.outside .cal-daynum { color: var(--pj-faint); }
  .cal-daynum .today-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; padding: 0 5px; margin: -3px 0 0 -5px; border-radius: 7px; background: linear-gradient(135deg, #191970, #1e90ff); color: #fff; font-weight: 600; box-shadow: 0 4px 10px -3px rgba(30,64,175,.6); }
  .cal-week-events { position: absolute; left: 0; right: 0; top: 30px; bottom: 4px; display: grid; grid-template-columns: repeat(7, 1fr); grid-auto-rows: 22px; row-gap: 3px; pointer-events: none; }
  .cal-bar {
    pointer-events: auto; align-self: start; display: flex; align-items: center; gap: 6px; height: 22px; margin: 0 4px; padding: 0 8px; border-radius: 6px;
    font-size: 11px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer;
    background-color: var(--mcs); color: var(--pj-ink);
    transition: transform .1s ease, box-shadow .1s ease;
  }
  .cal-bar:hover { transform: translateY(-1px); box-shadow: 0 4px 10px -4px rgba(15,23,42,.35); }
  .cal-bar.cont-left  { border-top-left-radius: 0; border-bottom-left-radius: 0; margin-left: 0; }
  .cal-bar.cont-right { border-top-right-radius: 0; border-bottom-right-radius: 0; margin-right: 0; }
  .cal-bar .bdot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; background: var(--mc); }
  .cal-bar .status-ico { font-size: 10px; flex-shrink: 0; }
  .cal-bar .bar-txt { overflow: hidden; text-overflow: ellipsis; }

  /* Status markers shared by Calendar + Timeline bars */
  .cal-bar, .tl-bar { border-left: 3px solid transparent; }
  .st-planned    { border-left-color: #94a3b8; }
  .st-onprogress { border-left-color: #2563eb; }
  .st-live       { border-left-color: #7c3aed; }
  .st-done       { border-left-color: #10b981; }
  .st-planned .status-ico    { color: #94a3b8; }
  .st-onprogress .status-ico { color: #2563eb; }
  .st-live .status-ico       { color: #7c3aed; }
  .st-done .status-ico       { color: #059669; }
  .cal-bar.st-done { opacity: .8; }
  .cal-bar.st-live, .tl-bar.st-live { opacity: .8; }
  .cal-bar.overdue-bar, .tl-bar.overdue-bar { box-shadow: 0 0 0 2px rgba(239,68,68,.55); }
  .proj-page[data-theme="dark"] .cal-bar, .proj-page[data-theme="dark"] .tl-bar { background-color: #1a2548; background-color: color-mix(in srgb, var(--mc) 26%, #111931); }
  .proj-page[data-theme="dark"] .st-done .status-ico { color: #34d399; }
  .proj-page[data-theme="dark"] .st-onprogress .status-ico { color: #7fb0ff; }

  /* ---------- Calendar: weekend (Sat & Sun off), month summary, compact legend ---------- */
  .proj-page { --pj-wknd: #e11d48; --pj-wknd-soft: #fff5f7; --pj-wknd-line: #fde0e7; }
  .proj-page[data-theme="dark"] { --pj-wknd: #fb7185; --pj-wknd-soft: rgba(244,63,94,.07); --pj-wknd-line: rgba(244,63,94,.24); }
  .cal-toolbar .cal-head { display: flex; align-items: center; flex-wrap: wrap; gap: 8px 16px; }
  .cal-chips { display: flex; flex-wrap: wrap; gap: 6px; }
  .cal-chip { display: inline-flex; align-items: center; gap: 6px; height: 26px; padding: 0 11px; border-radius: 999px; font-size: 11.5px; color: var(--pj-muted); background: var(--pj-sunken); white-space: nowrap; }
  .cal-chip b { color: var(--pj-ink); font-weight: 600; }
  .cal-chip .fa { font-size: 10.5px; color: var(--pj-faint); }
  .cal-chip.weekend { color: var(--pj-wknd); background: var(--pj-wknd-soft); box-shadow: inset 0 0 0 1px var(--pj-wknd-line); }
  .cal-chip.weekend b, .cal-chip.weekend .fa { color: var(--pj-wknd); }
  .cal-legend { display: flex; align-items: center; flex-wrap: wrap; gap: 6px 16px; padding: 8px 18px; border-bottom: 1px solid var(--pj-line-soft); font-size: 11px; color: var(--pj-muted); }
  .cal-legend .lg { display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
  .cal-legend .lg-dot { width: 8px; height: 8px; border-radius: 50%; }
  .cal-legend .lg-overdue { color: var(--pj-bad); }
  .cal-legend .lg-overdue .lg-dot { width: 8px; height: 8px; background: transparent; box-shadow: inset 0 0 0 2px #ef4444; }
  .cal-legend .lg-sep { width: 1px; height: 14px; background: var(--pj-line); }
  .cal-legend .lg-sw { width: 16px; height: 11px; border-radius: 3px; }
  .cal-legend .lg-sw.weekend { background: var(--pj-wknd-soft); box-shadow: inset 0 0 0 1px var(--pj-wknd-line); }
  .cal-legend .lg-sw.today { background: linear-gradient(135deg, #191970, #1e90ff); }
  .cal-dow { display: flex; align-items: center; }
  .cal-dow.weekend { color: var(--pj-wknd); background: var(--pj-wknd-soft); }
  .cal-dow .dow-off { margin-left: 7px; padding: 1px 6px; border-radius: 999px; font-size: 9px; font-weight: 600; letter-spacing: .06em; color: var(--pj-wknd); background: var(--pj-wknd-line); }
  .cal-daycell.weekend { background: var(--pj-wknd-soft); }
  .cal-daycell.weekend .cal-daynum { color: var(--pj-wknd); font-weight: 600; }
  .cal-daycell.outside, .cal-daycell.outside.weekend {
    background: repeating-linear-gradient(135deg, transparent 0 7px, var(--pj-line-soft) 7px 8px), var(--pj-surface-2);
  }
  .cal-daycell.is-today { box-shadow: inset 0 0 0 2px rgba(30,144,255,.28); }
  .cal-bar { background-color: var(--mcs); color: var(--mc); }
  .proj-page[data-theme="dark"] .cal-bar { color: var(--pj-ink); }
  .cal-filter-note { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; padding: 9px 18px; border-bottom: 1px solid var(--pj-line-soft); font-size: 12px; color: var(--pj-warn); background: var(--pj-warn-soft); }
  .cal-filter-note b { font-weight: 600; }
  .cal-filter-note button { margin-left: auto; height: 26px; padding: 0 12px; border-radius: 8px; border: 1px solid currentColor; background: transparent; color: inherit; font-size: 11.5px; font-weight: 600; cursor: pointer; }
  .cal-filter-note button:hover { background: var(--pj-surface); }

  /* Hari libur nasional / cuti bersama (merah) & libur produksi (amber). */
  .proj-page { --pj-hol: #be123c; --pj-hol-soft: #ffe9ee; --pj-hol-line: #fbc9d5; --pj-prod: #b45309; --pj-prod-soft: #fdf3e3; --pj-prod-line: #f5dfbc; }
  .proj-page[data-theme="dark"] { --pj-hol: #fda4af; --pj-hol-soft: rgba(225,29,72,.15); --pj-hol-line: rgba(225,29,72,.34); --pj-prod: #fbbf4d; --pj-prod-soft: rgba(245,158,11,.12); --pj-prod-line: rgba(245,158,11,.3); }
  .cal-daynum { display: flex; align-items: center; gap: 7px; }
  .cal-hol { flex: 1; min-width: 0; font-size: 9.5px; font-weight: 600; letter-spacing: .02em; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--pj-hol); }
  .cal-hol.prod { color: var(--pj-prod); }
  .cal-daycell.holiday { background: var(--pj-hol-soft); }
  .cal-daycell.holiday .cal-daynum { color: var(--pj-hol); font-weight: 600; }
  .cal-daycell.holiday-prod { background: var(--pj-prod-soft); }
  .cal-daycell.holiday-prod .cal-daynum { color: var(--pj-prod); font-weight: 600; }
  .cal-chip.holiday { color: var(--pj-hol); background: var(--pj-hol-soft); box-shadow: inset 0 0 0 1px var(--pj-hol-line); }
  .cal-chip.holiday b, .cal-chip.holiday .fa { color: var(--pj-hol); }
  .cal-legend .lg-sw.holiday { background: var(--pj-hol-soft); box-shadow: inset 0 0 0 1px var(--pj-hol-line); }
  .cal-legend .lg-sw.holiday-prod { background: var(--pj-prod-soft); box-shadow: inset 0 0 0 1px var(--pj-prod-line); }
  .tl-day-col.holiday { background: var(--pj-hol-soft); color: var(--pj-hol); }
  .tl-day-col.holiday-prod { background: var(--pj-prod-soft); color: var(--pj-prod); }

  /* ---------- Timeline ---------- */
  .tl-scroll { overflow-x: auto; max-height: calc(100vh - 360px); min-height: 220px; }
  .tl-grid { position: relative; width: 100%; }
  .tl-head-row { display: flex; position: sticky; top: 0; z-index: 4; background: var(--pj-surface-2); border-bottom: 1px solid var(--pj-line); }
  .tl-head-label { flex: 0 0 230px; position: sticky; left: 0; z-index: 6; padding: 10px 14px; background: var(--pj-surface-2); border-right: 1px solid var(--pj-line); font-size: 10.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--pj-muted); }
  .tl-head-days { display: flex; flex: 1 1 auto; min-width: 0; }
  .tl-day-col { flex: 1 1 0; min-width: 22px; padding: 10px 0; text-align: center; font-size: 10px; font-weight: 500; color: var(--pj-muted); border-right: 1px solid var(--pj-line-soft); }
  .tl-day-col.weekend { background: var(--pj-sunken); color: var(--pj-faint); }
  .tl-day-col.today { color: #fff; font-weight: 600; background: linear-gradient(180deg, #1e90ff, #2451d6); }
  .tl-row { display: flex; align-items: center; height: 40px; border-bottom: 1px solid var(--pj-line-soft); }
  .tl-row:last-child { border-bottom: 0; }
  .tl-row:hover { background: var(--pj-surface-2); }
  .tl-row-label { flex: 0 0 230px; position: sticky; left: 0; z-index: 3; height: 100%; display: flex; align-items: center; gap: 8px; padding: 0 14px; background-color: var(--pj-surface); border-right: 1px solid var(--pj-line); }
  .tl-row:hover .tl-row-label { background-color: var(--pj-surface-2); }
  .tl-row-name { font-size: 12px; font-weight: 500; color: var(--pj-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .tl-row-track { position: relative; flex: 1 1 auto; min-width: 0; height: 100%; }
  .tl-bar { position: absolute; top: 9px; height: 22px; border-radius: 6px; cursor: pointer; overflow: hidden; background-color: var(--mcs); transition: transform .1s ease, box-shadow .1s ease; }
  .tl-bar:hover { transform: translateY(-1px); box-shadow: 0 6px 12px -6px rgba(15,23,42,.45); z-index: 2; }
  .tl-bar.cont-left  { border-top-left-radius: 0; border-bottom-left-radius: 0; }
  .tl-bar.cont-right { border-top-right-radius: 0; border-bottom-right-radius: 0; }
  .tl-bar-fill { position: absolute; top: 0; bottom: 0; left: 0; background: var(--mc); opacity: .92; }
  .tl-bar-icon { position: relative; z-index: 1; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #fff; }
  .tl-today-line { position: absolute; top: 0; bottom: 0; width: 2px; background: #1e90ff; opacity: .55; z-index: 1; }

  /* ---------- Insights ---------- */
  .insights-grid { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 16px; align-items: stretch; }
  .chart-card { grid-column: span 4; min-width: 0; padding: 18px 20px; border-radius: 16px; background: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: var(--pj-shadow); display: flex; flex-direction: column; }
  .chart-card.span-8 { grid-column: span 8; }
  .chart-card.wide { grid-column: 1 / -1; }
  .chart-card.row-2 { grid-row: span 2; }
  .cc-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
  .chart-card h5 { margin: 0; font-size: 14px; font-weight: 600; color: var(--pj-ink); letter-spacing: -.005em; text-transform: none; }
  .cc-sub { margin-top: 2px; font-size: 11.5px; color: var(--pj-muted); }
  .cc-stats { display: flex; gap: 24px; flex-shrink: 0; }
  .cc-stat { text-align: right; }
  .cc-stat .v { font-size: 20px; font-weight: 600; line-height: 1.1; color: var(--pj-ink); letter-spacing: -.01em; white-space: nowrap; }
  .cc-stat .v small { font-size: 11px; font-weight: 500; color: var(--pj-muted); margin-left: 3px; letter-spacing: 0; }
  .cc-stat .k { font-size: 10.5px; color: var(--pj-muted); white-space: nowrap; }
  .cc-body { flex: 1; display: flex; flex-direction: column; justify-content: center; }
  .trend-plot { position: relative; height: 180px; margin: 18px 0 0 28px; }
  .trend-grid { position: absolute; left: 0; right: 0; height: 1px; background: var(--pj-line-soft); }
  .trend-grid span { position: absolute; left: -28px; top: -7px; width: 20px; text-align: right; font-size: 10px; color: var(--pj-faint); font-variant-numeric: tabular-nums; }
  .trend-chart { position: absolute; inset: 0; display: flex; align-items: stretch; gap: 10px; }
  .trend-col { flex: 1; display: flex; flex-direction: column; min-width: 0; }
  .trend-bar-wrap { position: relative; flex: 1; width: 100%; display: flex; align-items: flex-end; justify-content: center; }
  .trend-bar { position: relative; width: 44%; max-width: 46px; min-height: 2px; border-radius: 6px 6px 0 0; background: linear-gradient(180deg, #3558c8, #1e3a8a); transition: height .5s ease; }
  .trend-bar.cur { background: linear-gradient(180deg, #4aa8ff, #1e6fe0); }
  .proj-page[data-theme="dark"] .trend-bar { background: linear-gradient(180deg, #5f82e6, #2d4ea8); }
  .proj-page[data-theme="dark"] .trend-bar.cur { background: linear-gradient(180deg, #6cbcff, #2f7cf0); }
  .trend-bar.zero { background: var(--pj-line) !important; }
  .trend-val { position: absolute; left: 50%; transform: translateX(-50%); top: -20px; font-size: 11.5px; font-weight: 600; color: var(--pj-ink); white-space: nowrap; }
  .trend-bar.zero .trend-val { color: var(--pj-faint); font-weight: 500; }
  .trend-lbls { display: flex; gap: 10px; margin: 8px 0 0 28px; }
  .trend-lbl { flex: 1; text-align: center; font-size: 11px; font-weight: 500; color: var(--pj-muted); }
  .trend-lbl.cur { color: var(--pj-accent); font-weight: 600; }
  .donut-wrap { display: flex; align-items: center; gap: 22px; }
  .donut { position: relative; width: 128px; height: 128px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
  .donut span.donut-hole { width: 98px; height: 98px; border-radius: 50%; background: var(--pj-surface); display: flex; flex-direction: column; align-items: center; justify-content: center; }
  .donut-hole .num { font-size: 22px; font-weight: 600; line-height: 1.1; color: var(--pj-ink); letter-spacing: -.02em; }
  .donut-hole .lbl { font-size: 10px; color: var(--pj-muted); }
  .legend { flex: 1; display: flex; flex-direction: column; gap: 10px; min-width: 0; }
  .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--pj-ink-2); }
  .legend-item .sw { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
  .legend-item b { color: var(--pj-ink); font-weight: 600; font-variant-numeric: tabular-nums; }
  .legend-item .pc { color: var(--pj-faint); font-size: 11px; width: 38px; text-align: right; font-variant-numeric: tabular-nums; }
  .cc-note { margin-top: auto; padding-top: 12px; border-top: 1px solid var(--pj-line-soft); display: flex; align-items: center; gap: 8px; font-size: 11.5px; color: var(--pj-muted); }
  .cc-note-gap { height: 16px; flex-shrink: 0; }
  .cc-note.gold { color: var(--pj-gold); font-weight: 500; }
  .bar-chart-row { display: flex; align-items: center; gap: 12px; margin-bottom: 13px; }
  .bar-chart-row:last-child { margin-bottom: 0; }
  .bar-chart-label { width: 96px; flex-shrink: 0; display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 500; color: var(--pj-ink-2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .bar-chart-label .sw { width: 8px; height: 8px; border-radius: 2px; flex-shrink: 0; }
  .bar-chart-track { flex: 1; height: 8px; border-radius: 999px; background: var(--pj-sunken); overflow: hidden; }
  .bar-chart-fill { height: 100%; border-radius: 999px; transition: width .5s ease; }
  .bar-chart-value { width: 62px; flex-shrink: 0; text-align: right; font-size: 12px; font-weight: 600; color: var(--pj-ink); font-variant-numeric: tabular-nums; white-space: nowrap; }
  .bar-chart-value small { font-size: 10.5px; font-weight: 400; color: var(--pj-faint); margin-left: 4px; }
  .req-row { display: flex; align-items: center; gap: 12px; padding: 6px 0; }
  .req-row + .req-row { border-top: 1px solid var(--pj-line-soft); padding-top: 10px; }
  .req-row .pic-avatar { width: 36px; height: 36px; font-size: 12px; box-shadow: none; }
  .req-main { flex: 1; min-width: 0; }
  .req-name { display: flex; justify-content: space-between; align-items: baseline; gap: 8px; font-size: 12.5px; font-weight: 600; color: var(--pj-ink); }
  .req-name span { font-weight: 500; color: var(--pj-muted); font-size: 11.5px; white-space: nowrap; }
  .req-main .bar-chart-track { margin-top: 7px; height: 6px; }
  .activity-list { display: flex; flex-direction: column; max-height: 440px; overflow-y: auto; }
  .activity-item { display: flex; align-items: flex-start; gap: 11px; padding: 10px 8px; margin: 0 -8px; border-radius: 10px; cursor: pointer; }
  .activity-item + .activity-item { border-top: 1px solid var(--pj-line-soft); }
  .activity-item:hover { background: var(--pj-surface-2); }
  .activity-dot { width: 8px; height: 8px; margin-top: 6px; border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 0 3px var(--pj-line-soft); }
  .activity-body { flex: 1; min-width: 0; }
  .activity-text { font-size: 12.5px; line-height: 1.45; color: var(--pj-ink-2); }
  .activity-text b { color: var(--pj-ink); font-weight: 600; }
  .activity-time { margin-top: 2px; font-size: 11px; color: var(--pj-muted); }
  .activity-side { flex-shrink: 0; font-size: 11px; color: var(--pj-muted); white-space: nowrap; padding-top: 2px; }
  .ins-empty { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; padding: 14px 0; text-align: center; color: var(--pj-muted); font-size: 12px; }
  .ins-empty .ie-ico { width: 40px; height: 40px; margin-bottom: 6px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 15px; color: var(--pj-good); background: var(--pj-good-soft); }
  .ins-empty b { color: var(--pj-ink); font-size: 13px; font-weight: 600; }

  /* ---------- Detail drawer ---------- */
  .drawer-backdrop { position: fixed; inset: 0; z-index: 1040; background: rgba(7,11,34,.45); opacity: 0; pointer-events: none; transition: opacity .25s ease; }
  .drawer-backdrop.show { opacity: 1; pointer-events: auto; }
  .detail-drawer {
    position: fixed; top: 0; right: 0; bottom: 0; width: min(500px, 100vw); z-index: 1050; display: flex; flex-direction: column;
    font-family: var(--pj-font); color: var(--pj-ink); background: var(--pj-surface); box-shadow: -24px 0 60px -20px rgba(7,11,34,.45);
    transform: translateX(100%); transition: transform .3s cubic-bezier(.2,.8,.2,1); -webkit-font-smoothing: antialiased;
  }
  .detail-drawer.show { transform: translateX(0); }
  .drawer-head {
    position: relative; flex-shrink: 0; overflow: hidden; color: #fff; padding: 22px 24px 20px;
    background: radial-gradient(360px 200px at 100% 0%, rgba(30,144,255,.45), rgba(30,144,255,0) 70%), linear-gradient(120deg, #0a0f2e 0%, #141d62 55%, #1b3f9c 100%);
  }
  .drawer-close {
    position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer;
    color: #fff; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18); transition: background .15s ease;
  }
  .drawer-close:hover { background: rgba(255,255,255,.2); }
  .drawer-head .cat { color: #fff; background: rgba(255,255,255,.12); box-shadow: inset 0 0 0 1px rgba(255,255,255,.16); }
  .drawer-eyebrow { display: flex; align-items: center; gap: 8px; padding-right: 44px; }
  .drawer-title { margin: 10px 44px 12px 0; font-size: 19px; font-weight: 600; line-height: 1.3; letter-spacing: -.01em; color: #fff; }
  .drawer-badges { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
  .drawer-head .badge-status-Done { background: rgba(52,211,153,.18); color: #7ef0bf; }
  .drawer-head .badge-status-OnProgress { background: rgba(96,165,250,.2); color: #b3d4ff; }
  .drawer-head .badge-status-Live { background: rgba(167,139,250,.22); color: #d3c4ff; }
  .drawer-head .badge-status-Planned { background: rgba(255,255,255,.12); color: rgba(255,255,255,.85); }
  .drawer-head .badge-priority-High, .drawer-head .badge-priority-Medium, .drawer-head .badge-priority-Low { background: rgba(255,255,255,.1); color: rgba(255,255,255,.88); }
  .drawer-head .badge-ontime { background: rgba(52,211,153,.18); color: #7ef0bf; }
  .drawer-head .badge-late { background: rgba(248,113,113,.2); color: #fecaca; }
  .drawer-head .badge-at-risk { background: rgba(251,191,36,.18); color: #fcd57a; box-shadow: none; }
  .drawer-head .pic-chip { color: rgba(255,255,255,.88); }
  .drawer-head .pic-chip-label { color: rgba(255,255,255,.55); }
  .drawer-head .pic-avatar { box-shadow: 0 0 0 2px rgba(255,255,255,.25); }
  .drawer-head .overdue-flag { color: #fecaca; }
  .drawer-head .dh-when { color: rgba(255,255,255,.8); font-size: 11.5px; }
  .drawer-progress { display: flex; align-items: center; gap: 12px; margin-top: 16px; }
  .drawer-progress .track { flex: 1; height: 6px; border-radius: 999px; background: rgba(255,255,255,.14); overflow: hidden; }
  .drawer-progress .track span { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #6fb4ff, #ffffff); }
  .drawer-progress .val { font-size: 13px; font-weight: 600; min-width: 38px; text-align: right; }
  .drawer-meta-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1px; margin-top: 16px; border-radius: 12px; overflow: hidden; background: rgba(255,255,255,.12); }
  .drawer-meta-item { padding: 9px 11px; background: rgba(8,13,45,.45); min-width: 0; }
  .drawer-meta-item .k { display: block; margin-bottom: 2px; font-size: 10px; font-weight: 500; letter-spacing: .06em; text-transform: uppercase; color: rgba(255,255,255,.55); }
  .drawer-meta-item .v { display: block; font-size: 12px; font-weight: 600; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .drawer-body { flex: 1 1 auto; overflow-y: auto; padding: 20px 24px; }
  .drawer-section { margin-bottom: 22px; }
  .drawer-section h6 { display: flex; align-items: center; gap: 9px; margin-bottom: 10px; font-size: 12.5px; font-weight: 600; color: var(--pj-ink); }
  .drawer-section h6 > .fa { width: 24px; height: 24px; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; color: var(--pj-accent); background: var(--pj-accent-soft); margin-right: 0 !important; }
  .drawer-desc-box { font-size: 13px; line-height: 1.65; color: var(--pj-ink-2); padding: 12px 14px; border-radius: 12px; background: var(--pj-surface-2); border: 1px solid var(--pj-line-soft); }
  .drawer-foot { display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0; padding: 14px 24px; border-top: 1px solid var(--pj-line); background: var(--pj-surface-2); }
  .milestone-row { display: flex; align-items: center; gap: 10px; padding: 8px 2px; border-bottom: 1px solid var(--pj-line-soft); }
  .milestone-row:last-child { border-bottom: none; }
  .milestone-check { width: 19px; height: 19px; border-radius: 6px; border: 1.5px solid #c3cee0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer; color: #fff; font-size: 10px; transition: background .15s ease, border-color .15s ease; }
  .milestone-check:hover { border-color: var(--pj-accent); }
  .milestone-check.done { background: linear-gradient(135deg, #191970, #1e90ff); border-color: transparent; }
  .milestone-title { flex: 1; font-size: 13px; color: var(--pj-ink); cursor: pointer; }
  .milestone-title.done { text-decoration: line-through; color: var(--pj-muted); }
  .milestone-del { padding: 2px 4px; font-size: 11px; color: var(--pj-muted); cursor: pointer; opacity: 0; transition: opacity .15s ease, color .15s ease; }
  .milestone-row:hover .milestone-del { opacity: 1; }
  .milestone-del:hover { color: var(--pj-bad); }
  .milestone-add-row { display: flex; gap: 8px; margin-top: 10px; }
  .milestone-add-row input { flex: 1; height: 36px; padding: 0 12px; border-radius: 10px; font-size: 12.5px; color: var(--pj-ink); background: var(--pj-surface); border: 1px solid var(--pj-line); }
  .milestone-add-row input::placeholder { color: var(--pj-faint); }
  .milestone-add-row input:focus { outline: none; border-color: #7aa2f7; box-shadow: 0 0 0 3px rgba(36,81,214,.14); }
  .milestone-add-row .icon-btn { width: 36px; height: 36px; border-radius: 10px; color: #fff; border: none; background: linear-gradient(135deg, #191970, #1e90ff); }
  .milestone-add-row .icon-btn:hover { color: #fff; filter: brightness(1.1); }
  .milestone-progress-txt { margin-left: auto; font-size: 11px; font-weight: 500; color: var(--pj-muted); letter-spacing: 0; text-transform: none; }
  .drawer-empty { font-size: 12px; color: var(--pj-muted); padding: 6px 0; }
  .detail-drawer .activity-item { cursor: default; }
  .detail-drawer .activity-item:hover { background: transparent; }

  /* ---------- Modal ---------- */
  .modal-project .modal-dialog { max-width: min(1180px, 94vw); width: 100%; }
  .modal-project .modal-content { border: none; border-radius: 20px; overflow: hidden; font-family: var(--pj-font); color: var(--pj-ink); background: var(--pj-surface); box-shadow: 0 40px 80px -30px rgba(7,11,34,.6); -webkit-font-smoothing: antialiased; }
  .modal-project .modal-preview {
    position: relative; overflow: hidden; padding: 28px 26px; color: #fff; display: flex; flex-direction: column;
    background: radial-gradient(380px 260px at 100% 0%, rgba(30,144,255,.45), rgba(30,144,255,0) 70%), linear-gradient(160deg, #070b22 0%, #10185a 50%, #1a3d9a 100%);
  }
  .modal-project .modal-preview::before {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); background-size: 30px 30px;
    -webkit-mask-image: linear-gradient(160deg, #000, transparent 70%); mask-image: linear-gradient(160deg, #000, transparent 70%);
  }
  .modal-preview > * { position: relative; }
  .modal-preview .mp-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 26px; font-size: 12px; font-weight: 500; color: rgba(255,255,255,.75); }
  .modal-preview .mp-brand .hero-mark { width: 36px; height: 36px; border-radius: 11px; font-size: 14px; margin-top: 0; }
  .modal-preview .eyebrow { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; font-size: 10.5px; font-weight: 600; letter-spacing: .16em; text-transform: uppercase; color: rgba(255,255,255,.7); }
  .modal-preview .eyebrow::before { content: ''; width: 18px; height: 2px; border-radius: 2px; background: #dcae47; }
  .modal-preview .mp-title { font-size: 19px; font-weight: 600; letter-spacing: -.01em; margin-bottom: 18px; }
  .modal-preview .prev-card { border-radius: 14px; padding: 16px; color: #0f172a; background: #fff; box-shadow: 0 24px 48px -20px rgba(0,0,0,.6); }
  .prev-card .name { font-size: 15px; font-weight: 600; color: #0f172a; line-height: 1.35; margin: 10px 0 4px; }
  .prev-card .desc { font-size: 12px; color: #64748b; line-height: 1.5; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 12px; }
  .prev-card .meta-row { font-size: 11.5px; color: #64748b; margin-bottom: 10px; }
  .prev-card .pct { font-size: 12px; font-weight: 600; color: #0f172a; }
  .prev-card .progress { background: #edf1f8; flex: none; margin-top: 2px; }
  .prev-card .footer-row { display: flex; align-items: center; margin-top: 12px; padding-top: 10px; border-top: 1px solid #edf1f7; }
  .prev-card .pic-chip { color: #334155; }
  .prev-card .pic-chip-label { color: #94a3b8; }
  .prev-card .pic-avatar { box-shadow: 0 0 0 2px #fff; }
  .prev-card .badge-status-Planned { background: #edf1f8; color: #64748b; }
  .prev-card .badge-status-OnProgress { background: #eef2ff; color: #2451d6; }
  .prev-card .badge-status-Live { background: #ede9fe; color: #6d28d9; }
  .prev-card .badge-status-Done { background: #e5f6ee; color: #047857; }
  .prev-card .badge-priority-High, .prev-card .badge-priority-Medium, .prev-card .badge-priority-Low { background: #edf1f8; color: #334155; }
  .modal-preview .prev-tip { margin-top: auto; padding-top: 22px; font-size: 11.5px; line-height: 1.6; color: rgba(255,255,255,.7); }
  .modal-preview .prev-tip .fa { color: #dcae47; }
  .modal-form-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 22px; }
  .modal-form-head .t { font-size: 18px; font-weight: 600; color: var(--pj-ink); letter-spacing: -.01em; }
  .modal-form-head .s { font-size: 12px; color: var(--pj-muted); margin-top: 2px; }
  .modal-x { width: 32px; height: 32px; border-radius: 10px; border: 1px solid var(--pj-line); background: var(--pj-surface); color: var(--pj-muted); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px !important; flex-shrink: 0; }
  .modal-x:hover { color: var(--pj-ink); background: var(--pj-sunken); }
  .form-section { margin-bottom: 22px; padding-bottom: 20px; border-bottom: 1px solid var(--pj-line-soft); }
  .form-section.mb-0 { padding-bottom: 0; border-bottom: 0; }
  .form-section .fs-head { display: flex; align-items: center; gap: 9px; margin-bottom: 14px; }
  .form-section .fs-head .fs-icon { width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; color: var(--pj-accent); background: var(--pj-accent-soft); }
  .form-section .fs-head .fs-title { font-size: 13px; font-weight: 600; color: var(--pj-ink); }
  .field-label { display: block; margin-bottom: 6px; font-size: 11.5px; font-weight: 500; color: var(--pj-muted); }
  .form-control-modern { height: auto; padding: 9px 12px; border-radius: 10px; font-size: 13px; color: var(--pj-ink); background: var(--pj-surface); border: 1px solid var(--pj-line); transition: border-color .15s ease, box-shadow .15s ease; }
  .form-control-modern:focus { color: var(--pj-ink); background: var(--pj-surface); border-color: #7aa2f7; box-shadow: 0 0 0 3px rgba(36,81,214,.14); }
  .form-control-modern::placeholder { color: var(--pj-faint); }
  textarea.form-control-modern { resize: vertical; }
  .modal-project .text-muted { color: var(--pj-muted) !important; }
  .modal-project small.text-muted { font-size: 11px; }
  .pill-select { display: flex; flex-wrap: wrap; gap: 8px; }
  .pill-opt { height: 34px; padding: 0 15px; border-radius: 10px; cursor: pointer; font-size: 12.5px !important; font-weight: 500; color: var(--pj-ink-2); background: var(--pj-surface); border: 1px solid var(--pj-line); transition: border-color .15s ease, background .15s ease, color .15s ease; }
  .pill-opt:hover { border-color: #aebfdf; }
  .pill-opt.active { font-weight: 600; }
  .pill-opt.active[data-value="Planned"]     { background: var(--pj-sunken); border-color: #94a3b8; color: var(--pj-ink); }
  .pill-opt.active[data-value="On Progress"] { background: var(--pj-accent-soft); border-color: #2563eb; color: var(--pj-accent); }
  .pill-opt.active[data-value="Live"]        { background: #ede9fe; border-color: #7c3aed; color: #6d28d9; }
  .pill-opt.active[data-value="Done"]        { background: var(--pj-good-soft); border-color: #10b981; color: var(--pj-good); }
  .pill-opt.active[data-value="Low"]         { background: var(--pj-sunken); border-color: #94a3b8; color: var(--pj-ink); }
  .pill-opt.active[data-value="Medium"]      { background: var(--pj-warn-soft); border-color: #f59e0b; color: var(--pj-warn); }
  .pill-opt.active[data-value="High"]        { background: var(--pj-bad-soft); border-color: #ef4444; color: var(--pj-bad); }
  .progress-wrap { display: flex; align-items: center; gap: 16px; }
  .progress-ring-lg { --deg: 0deg; width: 56px; height: 56px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: conic-gradient(#2451d6 var(--deg), var(--pj-sunken) 0); transition: background .2s ease; }
  .progress-ring-lg span { width: 44px; height: 44px; border-radius: 50%; background: var(--pj-surface); display: flex; align-items: center; justify-content: center; font-size: 11.5px; font-weight: 600; color: var(--pj-ink); }
  #pj-progress-range { accent-color: #2451d6; }
  .swatch-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
  .modal-footer-modern { padding: 14px 28px; background: var(--pj-surface-2); border-top: 1px solid var(--pj-line); }
  .btn-modern-primary { display: inline-flex; align-items: center; gap: 6px; height: 38px; padding: 0 20px; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; color: #fff; background: linear-gradient(90deg, #191970, #1e6fe0); box-shadow: 0 10px 20px -10px rgba(25,25,112,.8); transition: box-shadow .15s ease, transform .15s ease; }
  .btn-modern-primary:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 14px 26px -12px rgba(25,25,112,.9); }
  .btn-modern-ghost { height: 38px; padding: 0 18px; border-radius: 10px; cursor: pointer; font-weight: 500; color: var(--pj-ink-2); background: var(--pj-surface); border: 1px solid var(--pj-line); transition: border-color .15s ease, color .15s ease, background .15s ease; }
  .btn-modern-ghost:hover { border-color: #aebfdf; color: var(--pj-ink); }
  .btn-modern-ghost.danger:hover { border-color: rgba(239,68,68,.5); color: var(--pj-bad); background: var(--pj-bad-soft); }

  /* ---------- Responsive ---------- */
  @media (max-width: 1540px) {
    .view-switch button i { display: none; }
    .search-wrap input { width: 180px; }
  }
  @media (max-width: 1400px) {
    .proj-page { padding: 18px 18px 40px; }
    .kpi { padding: 14px 16px 16px; }
    .kpi-value { font-size: 26px; }
    .stat-tile .num { font-size: 25px; }
    .stat-grid { gap: 12px; }
  }
  @media (max-width: 1199.98px) {
    .perf-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .kpi:nth-child(4)::before { display: none; }
    .kpi:nth-child(n+4) { border-top: 1px solid var(--pj-line-soft); }
    .stat-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .insights-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .chart-card { grid-column: span 1; }
    .chart-card.span-8 { grid-column: 1 / -1; }
    .chart-card.row-2 { grid-row: auto; }
    .toolbar-actions { margin-left: 0; flex: 1 1 100%; }
    .toolbar-actions .search-wrap { flex: 1; }
    .toolbar-actions .search-wrap input { width: 100%; }
  }
  @media (max-width: 767.98px) {
    .proj-page { padding: 12px 12px 32px; }
    .proj-hero { padding: 20px 18px 84px; border-radius: 16px; }
    .proj-hero .hero-title { font-size: 23px; }
    .hero-mark { display: none; }
    .perf-card { margin: -60px 8px 0; }
    .perf-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .kpi::before { display: none; }
    .kpi:nth-child(even)::before { display: block; }
    .kpi:nth-child(n+3) { border-top: 1px solid var(--pj-line-soft); }
    .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    .insights-grid { grid-template-columns: 1fr; }
    .chart-card, .chart-card.span-8 { grid-column: 1 / -1; }
    .cc-head { flex-wrap: wrap; }
    .tb-divider { display: none; }
    .view-switch, .status-seg { max-width: 100%; overflow-x: auto; }
    .board-wrap { grid-template-columns: none !important; grid-auto-columns: minmax(260px, 86vw); }
    .drawer-meta-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }
  @media (prefers-reduced-motion: reduce) {
    .view-fade-in, .proj-card-enter, .stat-tile, .live-dot, .view-loading .spinner { animation: none !important; }
    .proj-card, .stat-tile, .mod-sum-card, .btn-hero-new, .cal-bar, .tl-bar, .detail-drawer, .drawer-backdrop, .tile-meter span, .trend-bar, .bar-chart-fill, .btn-modern-primary { transition: none !important; }
    .proj-card:hover, .stat-tile:hover, .mod-sum-card:hover, .btn-hero-new:hover, .btn-modern-primary:hover { transform: none; }
  }
  /* ---------- Toolbar layout (2 intentional rows) ---------- */
  .proj-toolbar { padding: 10px 12px; }
  .proj-toolbar .toolbar-row { row-gap: 10px; }
  .proj-toolbar .view-switch { order: 1; }
  .proj-toolbar .toolbar-actions { order: 2; margin-left: auto; }
  .proj-toolbar .tb-divider { display: none; }
  .proj-toolbar .toolbar-filters { order: 3; flex: 1 1 100%; padding-top: 10px; border-top: 1px solid var(--pj-line-soft); }
  .proj-toolbar .toolbar-filters::before {
    content: 'Filters'; margin-right: 4px; font-size: 10.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--pj-faint);
  }
  @media (max-width: 767.98px) {
    .proj-toolbar .toolbar-actions { margin-left: 0; flex: 1 1 100%; }
    .proj-toolbar .toolbar-actions .search-wrap { flex: 1; }
    .proj-toolbar .toolbar-actions .search-wrap input { width: 100%; }
  }
</style>

<div class="proj-page" id="proj-page-root">
<script>
  // Applied synchronously, before the rest of this div paints, so there's no
  // flash of the wrong theme when a returning visitor has dark mode saved.
  (function () {
    if (localStorage.getItem('projTheme') === 'dark') {
      document.getElementById('proj-page-root').setAttribute('data-theme', 'dark');
    }
  })();
</script>

  <!-- Hero -->
  <header class="proj-hero">
    <span class="hero-orbit" aria-hidden="true"></span>
    <div class="hero-top">
      <div class="hero-brand">
        <span class="hero-mark" aria-hidden="true"><i class="fa fa-rocket"></i></span>
        <div>
          <div class="eyebrow"><span class="eyebrow-mark"></span>Finance Systems &middot; Delivery Portfolio</div>
          <h1 class="hero-title">Project Dashboard</h1>
          <p class="hero-sub">Welcome back, <b><?= htmlspecialchars($full_name) ?></b> &mdash; every feature built and shipped for the finance team, tracked in one executive view.</p>
          <div class="hero-meta" id="hero-meta"></div>
        </div>
      </div>
      <div class="hero-actions">
        <button type="button" class="theme-toggle" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle dark mode">
          <i class="fa fa-moon-o" id="theme-toggle-icon"></i>
        </button>
        <button type="button" class="btn-hero-new" onclick="openCreate()">
          <i class="fa fa-plus"></i> New Project
        </button>
      </div>
    </div>
  </header>

  <!-- Delivery performance (all-time, independent of filters) -->
  <section class="perf-card" id="perf-card" aria-label="Delivery performance">
    <div class="perf-head">
      <div class="perf-title">
        <span class="ico"><i class="fa fa-line-chart"></i></span>Delivery Performance
        <span class="perf-scope">All-time portfolio &middot; not affected by filters</span>
      </div>
      <div class="perf-flags" id="perf-flags"></div>
    </div>
    <div class="perf-grid" id="perf-grid">
      <div class="kpi"><div class="kpi-label">Loading&hellip;</div><div class="kpi-value">&mdash;</div></div>
    </div>
  </section>

  <!-- Current selection -->
  <div class="section-bar">
    <div class="section-heading">
      <span class="section-kicker">Current selection</span>
      <span class="stat-scope" id="stat-scope">Showing all projects</span>
    </div>
    <div class="module-summary" id="module-summary"></div>
  </div>

  <!-- Stat tiles -->
  <div class="stat-grid" id="stat-tiles">
    <div class="stat-tile wm-total">
      <div class="tile-top"><span class="lbl">Total projects</span><span class="icon-badge ic-total"><i class="fa fa-folder-open"></i></span></div>
      <div class="num" id="st-total" data-rawval="0">0</div>
      <div class="tile-foot"><span class="tile-meter"><span id="st-total-bar"></span></span><span class="tile-note" id="st-total-note">avg. progress</span></div>
    </div>
    <div class="stat-tile wm-progress">
      <div class="tile-top"><span class="lbl">On Progress</span><span class="icon-badge ic-progress"><i class="fa fa-spinner"></i></span></div>
      <div class="num" id="st-progress" data-rawval="0">0<span class="live-dot" id="progress-live-dot" style="display:none;"></span></div>
      <div class="tile-foot"><span class="tile-meter"><span id="st-progress-bar"></span></span><span class="tile-note" id="st-progress-note">0%</span></div>
    </div>
    <div class="stat-tile wm-live">
      <div class="tile-top"><span class="lbl">Live</span><span class="icon-badge ic-live"><i class="fa fa-rocket"></i></span></div>
      <div class="num" id="st-live" data-rawval="0">0</div>
      <div class="tile-foot"><span class="tile-meter"><span id="st-live-bar"></span></span><span class="tile-note" id="st-live-note">0%</span></div>
    </div>
    <div class="stat-tile wm-done">
      <div class="tile-top"><span class="lbl">Done + Live</span><span class="icon-badge ic-done"><i class="fa fa-check-circle"></i></span></div>
      <div class="num" id="st-done" data-rawval="0">0</div>
      <div class="tile-foot"><span class="tile-meter"><span id="st-done-bar"></span></span><span class="tile-note" id="st-done-note">0%</span></div>
    </div>
    <div class="stat-tile wm-overdue" id="overdue-tile">
      <div class="tile-top"><span class="lbl">Overdue</span><span class="icon-badge ic-overdue"><i class="fa fa-exclamation-triangle"></i></span></div>
      <div class="num" id="st-overdue" data-rawval="0">0</div>
      <div class="tile-foot"><span class="tile-meter"><span id="st-overdue-bar"></span></span><span class="tile-note" id="st-overdue-note">0%</span></div>
    </div>
    <div class="stat-tile tile-rate">
      <span class="sheen"></span>
      <div>
        <div class="lbl">Completion</div>
        <div class="num" id="st-rate" data-rawval="0">0%</div>
        <div class="tile-note" id="st-rate-note">of the selection</div>
      </div>
      <div class="ring" id="rate-ring"><span id="rate-ring-txt">0%</span></div>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="proj-toolbar mb-3">
    <div class="toolbar-row">
      <div class="view-switch" role="tablist" aria-label="Views">
        <button type="button" class="active" data-view="board" onclick="switchView('board')"><i class="fa fa-columns"></i> Board</button>
        <button type="button" data-view="list" onclick="switchView('list')"><i class="fa fa-table"></i> Table</button>
        <button type="button" data-view="calendar" onclick="switchView('calendar')"><i class="fa fa-calendar"></i> Calendar</button>
        <button type="button" data-view="timeline" onclick="switchView('timeline')"><i class="fa fa-tasks"></i> Timeline</button>
        <button type="button" data-view="insights" onclick="switchView('insights')"><i class="fa fa-pie-chart"></i> Insights</button>
      </div>
      <span class="tb-divider" aria-hidden="true"></span>
      <div class="toolbar-filters">
        <div class="status-seg" aria-label="Status filter">
          <span class="proj-filter-pill active" data-status="" onclick="setFilter('', this)">All</span>
          <span class="proj-filter-pill" data-status="On Progress" onclick="setFilter('On Progress', this)">On Progress</span>
          <span class="proj-filter-pill" data-status="Planned" onclick="setFilter('Planned', this)">Planned</span>
          <span class="proj-filter-pill" data-status="Done" onclick="setFilter('Done', this)">Done</span>
          <span class="proj-filter-pill" data-status="Live" onclick="setFilter('Live', this)">Live</span>
        </div>
        <div class="select-wrap">
          <i class="fa fa-cubes"></i>
          <select class="form-control form-control-sm" id="module-filter" style="width:auto;" onchange="renderActive()">
            <option value="">All Modules</option>
          </select>
        </div>
        <div class="select-wrap">
          <i class="fa fa-calendar-o"></i>
          <select class="form-control form-control-sm" id="month-filter" style="width:auto;" onchange="renderActive()">
            <option value="">All Months</option>
          </select>
        </div>
        <span class="clear-filters-link" id="clear-filters" onclick="clearFilters()"><i class="fa fa-times-circle"></i> Clear</span>
      </div>
      <div class="toolbar-actions">
        <div class="search-wrap">
          <i class="fa fa-search"></i>
          <input type="text" id="search-box" placeholder="Search project..." onkeyup="renderActive()">
        </div>
        <button type="button" class="btn-export" onclick="exportExcel()">
          <i class="fa fa-file-excel-o"></i> Export Excel
        </button>
      </div>
    </div>
  </div>

  <!-- Views -->
  <div id="view-board"><div class="view-loading"><div class="spinner"></div>Loading projects&hellip;</div></div>
  <div id="view-list" style="display:none;"></div>
  <div id="view-calendar" style="display:none;"></div>
  <div id="view-timeline" style="display:none;"></div>
  <div id="view-insights" style="display:none;"></div>

</div>

<!-- ===== MODAL CREATE / EDIT (premium) ===== -->
<div class="modal fade modal-project" tabindex="-1" role="dialog" aria-hidden="true" id="modalProject">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="row no-gutters">

        <!-- Live preview panel -->
        <div class="col-md-4 modal-preview">
          <div class="mp-brand"><span class="hero-mark"><i class="fa fa-rocket"></i></span>Delivery Portfolio</div>
          <div class="eyebrow" id="modal-proj-eyebrow">New Project</div>
          <div class="mp-title">Live card preview</div>
          <div class="prev-card">
            <span class="cat" id="pv-cat">General</span>
            <div class="name" id="pv-name" style="margin-top:10px;">Untitled project</div>
            <div class="desc" id="pv-desc" style="-webkit-line-clamp:3;line-clamp:3;">No description yet.</div>
            <div class="meta-row"><span id="pv-dates"><i class="fa fa-calendar mr-1"></i>? &rarr; ?</span></div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="pct" id="pv-pct">0%</span>
              <span class="badge badge-status-Planned" id="pv-status-badge">Planned</span>
            </div>
            <div class="progress"><div class="progress-bar" id="pv-progress-bar" style="width:0%;background:#94a3b8;"></div></div>
            <div class="footer-row" style="justify-content:space-between;">
              <span id="pv-pic"></span>
              <span class="badge badge-priority-Medium" id="pv-priority-badge">Medium</span>
            </div>
          </div>
          <div class="prev-tip"><i class="fa fa-magic mr-1"></i> This preview updates live as you fill the form &mdash; what you see is what lands on the board.</div>
        </div>

        <!-- Form -->
        <div class="col-md-8">
          <div class="modal-body p-4 p-lg-5" style="max-height:84vh; overflow-y:auto;">
            <input type="hidden" id="pj-id">

            <div class="modal-form-head">
              <div>
                <div class="t">Project details</div>
                <div class="s">Describe the feature, who asked for it and when it is due.</div>
              </div>
              <button type="button" class="modal-x" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>

            <div class="form-section">
              <div class="fs-head"><span class="fs-icon"><i class="fa fa-info"></i></span><span class="fs-title">Basic Information</span></div>
              <div class="form-group mb-2">
                <label class="field-label">Project Name *</label>
                <input type="text" class="form-control form-control-modern" id="pj-name" placeholder="e.g. Bank In Revamp" maxlength="255">
              </div>
              <div class="form-row">
                <div class="form-group col-md-6 mb-2">
                  <label class="field-label">Module</label>
                  <input type="text" class="form-control form-control-modern" id="pj-category" list="pj-category-list" placeholder="e.g. AP, NDS, AR, Signalbit" maxlength="100">
                  <datalist id="pj-category-list"></datalist>
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label class="field-label">Requested By</label>
                  <input type="text" class="form-control form-control-modern" id="pj-pic" list="pj-pic-list" maxlength="100">
                  <datalist id="pj-pic-list"></datalist>
                </div>
              </div>
              <div class="form-group mb-0">
                <label class="field-label">Description</label>
                <textarea class="form-control form-control-modern" id="pj-description" rows="3" placeholder="Short description of what this covers" maxlength="500"></textarea>
                <small class="text-muted" id="desc-counter">0 / 500</small>
              </div>
            </div>

            <div class="form-section">
              <div class="fs-head"><span class="fs-icon"><i class="fa fa-flag"></i></span><span class="fs-title">Status &amp; Priority</span></div>
              <div class="form-row">
                <div class="form-group col-lg-7 mb-2">
                  <label class="field-label">Status</label>
                  <div class="pill-select" id="pill-status">
                    <button type="button" class="pill-opt active" data-value="Planned">Planned</button>
                    <button type="button" class="pill-opt" data-value="On Progress">On Progress</button>
                    <button type="button" class="pill-opt" data-value="Done">Done</button>
                    <button type="button" class="pill-opt" data-value="Live">Live</button>
                  </div>
                  <input type="hidden" id="pj-status" value="Planned">
                </div>
                <div class="form-group col-lg-5 mb-0">
                  <label class="field-label">Priority</label>
                  <div class="pill-select" id="pill-priority">
                    <button type="button" class="pill-opt" data-value="Low">Low</button>
                    <button type="button" class="pill-opt active" data-value="Medium">Medium</button>
                    <button type="button" class="pill-opt" data-value="High">High</button>
                  </div>
                  <input type="hidden" id="pj-priority" value="Medium">
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="fs-head"><span class="fs-icon"><i class="fa fa-tasks"></i></span><span class="fs-title">Progress</span></div>
              <div class="progress-wrap">
                <div class="progress-ring-lg" id="progress-ring"><span id="progress-ring-txt">0%</span></div>
                <div class="flex-grow-1">
                  <input type="range" class="w-100" id="pj-progress-range" min="0" max="100" step="5" value="0" oninput="syncProgress(this.value)">
                  <input type="number" class="form-control form-control-sm form-control-modern mt-2" id="pj-progress" min="0" max="100" style="width:90px;" value="0" oninput="syncProgress(this.value)">
                </div>
              </div>
            </div>

            <div class="form-section mb-0">
              <div class="fs-head"><span class="fs-icon"><i class="fa fa-calendar"></i></span><span class="fs-title">Timeline</span></div>
              <div class="form-row">
                <div class="form-group col-md-6 mb-2">
                  <label class="field-label">Start Date</label>
                  <input type="date" class="form-control form-control-modern" id="pj-start">
                </div>
                <div class="form-group col-md-6 mb-2">
                  <label class="field-label">Target Date</label>
                  <input type="date" class="form-control form-control-modern" id="pj-target">
                </div>
              </div>
              <div class="form-group mb-0" id="pj-actual-row" style="display:none;">
                <label class="field-label">Done Date &mdash; work finished</label>
                <input type="date" class="form-control form-control-modern" id="pj-actual" style="max-width:220px;">
                <small class="text-muted d-block mt-1">Flags on-time vs late delivery, and this is the date used by Export Excel. Keep it as the date the work was finished &mdash; it must NOT be replaced by the go-live date.</small>
              </div>
              <div class="form-group mb-0 mt-3" id="pj-live-row" style="display:none;">
                <label class="field-label">Go-Live Date &mdash; running in production</label>
                <input type="date" class="form-control form-control-modern" id="pj-live" style="max-width:220px;">
                <small class="text-muted d-block mt-1">Kept separate from the Done Date on purpose, so moving a card to Live never overwrites when the work was actually finished.</small>
              </div>
            </div>
          </div>

          <div class="modal-footer-modern d-flex justify-content-end" style="gap:10px;">
            <button type="button" class="btn-modern-ghost" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn-modern-primary" onclick="saveProject()"><i class="fa fa-save mr-1"></i> Save Project</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- ===== PROJECT DETAIL DRAWER ===== -->
<div class="drawer-backdrop" id="drawer-backdrop" onclick="closeDrawer()"></div>
<div class="detail-drawer" id="detail-drawer">
  <div class="drawer-head" id="drawer-head"></div>
  <div class="drawer-body" id="drawer-body">
    <div class="drawer-section">
      <h6><i class="fa fa-info-circle mr-1"></i>Description</h6>
      <div id="drawer-desc" class="drawer-desc-box"></div>
    </div>
    <div class="drawer-section">
      <h6 class="d-flex align-items-center"><i class="fa fa-check-square-o mr-1"></i>Task List<span class="milestone-progress-txt" id="drawer-milestone-summary"></span></h6>
      <div id="drawer-milestones"></div>
      <div class="milestone-add-row">
        <input type="text" id="drawer-new-milestone" placeholder="Add a task..." maxlength="255" onkeydown="if(event.key==='Enter')addMilestone();">
        <button type="button" class="icon-btn" onclick="addMilestone()" title="Add"><i class="fa fa-plus"></i></button>
      </div>
    </div>
    <div class="drawer-section mb-0">
      <h6><i class="fa fa-history mr-1"></i>Activity</h6>
      <div class="activity-list" id="drawer-activity" style="max-height:280px;"></div>
    </div>
  </div>
  <div class="drawer-foot">
    <button type="button" class="btn-modern-ghost danger" onclick="doDelete(drawerProjectId, drawerProjectName); closeDrawer();"><i class="fa fa-trash mr-1"></i> Delete</button>
    <button type="button" class="btn-modern-primary" onclick="closeDrawer(); openEdit(drawerProjectId);"><i class="fa fa-pencil mr-1"></i> Edit Details</button>
  </div>
</div>

<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../css/4.1.1/sweetalert2@11.js"></script>

<script>
  // Sidebar collapse (shared shell behaviour)
  $('#body-row .collapse').collapse('hide');
  $('#collapse-icon').addClass('fa-angle-double-left');
  $('[data-toggle=sidebar-colapse]').click(function () { SidebarCollapse(); });
  function SidebarCollapse() {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');
    var sep = $('.sidebar-separator-title');
    sep.hasClass('d-flex') ? sep.removeClass('d-flex') : sep.addClass('d-flex');
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
  }

  // ===== Dark mode (scoped to the Project workspace card, not the whole app shell) =====
  function applyThemeIcon() {
    var isDark = $('#proj-page-root').attr('data-theme') === 'dark';
    $('#theme-toggle-icon').attr('class', isDark ? 'fa fa-sun-o' : 'fa fa-moon-o');
  }
  function toggleTheme() {
    var root = document.getElementById('proj-page-root');
    var isDark = root.getAttribute('data-theme') === 'dark';
    if (isDark) { root.removeAttribute('data-theme'); localStorage.removeItem('projTheme'); }
    else { root.setAttribute('data-theme', 'dark'); localStorage.setItem('projTheme', 'dark'); }
    applyThemeIcon();
  }
  applyThemeIcon();

  // Hari libur dari tabel mgt_rep_hari_libur (lihat blok PHP di atas).
  // t: national (LN) | joint (CT, cuti bersama) | production (LP, tidak mengurangi hari kerja).
  var HOLIDAYS = <?php echo json_encode($holidays, JSON_UNESCAPED_UNICODE); ?>;
  function holidayOn(dateKey) { return HOLIDAYS[dateKey] || null; }
  function isDayOff(h) { return !!h && h.t !== 'production'; }

  var allProjects  = [];
  var activeFilter = [];   // multi-pilih: kosong = semua status
  var currentView  = 'board';
  var calYear, calMonth; // 0-based month
  var MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

  // Kolom 'On Hold' dihapus (24 Sep 2026, permintaan user) dan 'Done' dipecah
  // dua: Done = pekerjaan selesai TAPI BELUM di-deploy, Live = sudah jalan di
  // produksi. Urutannya sengaja kiri->kanan mengikuti alur kerja nyata:
  // Planned -> On Progress -> Done -> Live.
  var STATUS_COLS = [
    { key: 'Planned',     dot: '#94a3b8' },
    { key: 'On Progress', dot: '#2563eb' },
    { key: 'Done',        dot: '#10b981' },
    { key: 'Live',        dot: '#7c3aed' }
  ];

  // Dua status di bawah ini sama-sama berarti 'pekerjaan sudah selesai'. Semua
  // perhitungan penyelesaian (on-time, lead time, completion rate, ekspor
  // Excel, sembunyikan hitungan mundur due date) memakai helper ini supaya
  // tidak ada satu pun tempat yang cuma mengecek 'Done' lalu melewatkan 'Live'.
  var DONE_STATUSES = ['Done', 'Live'];
  function isDoneStatus(s) { return DONE_STATUSES.indexOf(s) !== -1; }

  // Status marker language shared by Calendar bars + Timeline bars: a left border
  // stripe + small icon so status reads at a glance without losing the module color
  // that already tints the bar's background.
  var STATUS_META = {
    'Planned':     { cls: 'st-planned',    icon: 'fa-circle-o',     dot: '#94a3b8' },
    'On Progress': { cls: 'st-onprogress', icon: 'fa-play-circle',  dot: '#2563eb' },
    'Done':        { cls: 'st-done',       icon: 'fa-check-circle', dot: '#10b981' },
    'Live':        { cls: 'st-live',       icon: 'fa-rocket',       dot: '#7c3aed' }
  };
  function statusMeta(s) { return STATUS_META[s] || STATUS_META['Planned']; }
  function statusLegendHtml() {
    var items = STATUS_COLS.map(function (c) {
      var sm = statusMeta(c.key);
      return '<span class="lg-item"><span class="lg-bar" style="border-left-color:' + sm.dot + ';"></span>' + c.key + '</span>';
    }).join('');
    items += '<span class="lg-item lg-overdue"><span class="lg-bar"></span>Overdue</span>';
    return '<div class="status-legend">' + items + '</div>';
  }

  // ===== Module color palette (validated categorical set: works on light & dark surfaces) =====
  var MODULE_COLORS = {
    'AP':        { c: '#3b5bdb', s: '#e8edfc' },
    'AR':        { c: '#0d9488', s: '#e0f3f1' },
    'NDS':       { c: '#8b5cf6', s: '#efeafe' },
    'Signalbit': { c: '#e8590c', s: '#fdece2' }
  };
  var FALLBACK_PALETTE = [
    { c: '#0284c7', s: '#e0f0fa' }, { c: '#db2777', s: '#fce7f1' },
    { c: '#4d7c0f', s: '#ecf5dc' }, { c: '#b45309', s: '#fbefdc' },
    { c: '#6d28d9', s: '#ede7fb' }
  ];
  function moduleColor(cat) {
    if (!cat) return { c: '#94a3b8', s: '#eef1f6' };
    if (MODULE_COLORS[cat]) return MODULE_COLORS[cat];
    var hash = 0;
    for (var i = 0; i < cat.length; i++) hash = (hash * 31 + cat.charCodeAt(i)) >>> 0;
    return FALLBACK_PALETTE[hash % FALLBACK_PALETTE.length];
  }

  var MODULE_ICONS = {
    'NDS': 'fa-cubes', 'AP': 'fa-university', 'AR': 'fa-file-invoice-dollar', 'Signalbit': 'fa-bolt'
  };
  function moduleIcon(cat) {
    return MODULE_ICONS[cat] || 'fa-folder-o';
  }

  // ===== PIC / assignee avatars (brand navy family) =====
  var AVATAR_PALETTE = [
    { c: '#1e3a8a', s: '#e8eefc' }, { c: '#0369a1', s: '#e0f0fa' }, { c: '#4338ca', s: '#ebeafd' },
    { c: '#0f766e', s: '#e0f3f1' }, { c: '#7e22ce', s: '#f1e8fc' }
  ];
  function picInitials(name) {
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    return (parts[0][0] + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
  }
  function picColor(name) {
    if (!name) return { c: '#94a3b8', s: '#f1f5f9' };
    var hash = 0;
    for (var i = 0; i < name.length; i++) hash = (hash * 31 + name.charCodeAt(i)) >>> 0;
    return AVATAR_PALETTE[hash % AVATAR_PALETTE.length];
  }
  function picChipHtml(name) {
    if (!name) return '<span class="pic-chip unassigned"><i class="fa fa-question-circle-o"></i> No requester</span>';
    var pc = picColor(name);
    return '<span class="pic-chip" title="Requested by ' + escJs(name) + '">' +
      '<span class="pic-avatar" style="background:' + pc.c + ';">' + picInitials(name) + '</span>' +
      '<span class="pic-chip-label">Req.</span>' + escHtml(name) +
    '</span>';
  }

  function statusClass(s) { return 'badge-status-' + s.replace(/\s+/g, ''); }
  function progressColor(s) {
    if (s === 'Done') return '#10b981';
    if (s === 'Live') return '#7c3aed';
    if (s === 'On Progress') return '#2563eb';
    return '#94a3b8';
  }
  function fmtDate(d, opts) {
    if (!d) return null;
    var dt = new Date(d + 'T00:00:00');
    if (isNaN(dt)) return null;
    return dt.toLocaleDateString('en-US', opts || { day: '2-digit', month: 'short', year: 'numeric' });
  }
  function daysInfo(target, status) {
    if (!target || isDoneStatus(status)) return '';
    var today = new Date(); today.setHours(0,0,0,0);
    var t = new Date(target + 'T00:00:00');
    var diff = Math.round((t - today) / 86400000);
    if (diff < 0)  return '<span class="overdue-flag"><i class="fa fa-exclamation-triangle"></i> ' + Math.abs(diff) + 'd overdue</span>';
    if (diff === 0) return '<span class="overdue-flag">Due today</span>';
    return diff + 'd left';
  }
  function completionBadge(target, actual) {
    if (!actual) return '';
    if (!target) return '<span class="badge badge-ontime"><i class="fa fa-check"></i> ' + fmtDate(actual) + '</span>';
    var t = new Date(target + 'T00:00:00');
    var a = new Date(actual + 'T00:00:00');
    var diff = Math.round((a - t) / 86400000);
    if (diff <= 0) return '<span class="badge badge-ontime"><i class="fa fa-check"></i> On time</span>';
    return '<span class="badge badge-late"><i class="fa fa-clock-o"></i> Late ' + diff + 'd</span>';
  }
  function escHtml(s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); }
  function escJs(s) { return String(s).replace(/'/g, "\\'"); }

  // "At Risk": actively worked (On Progress), not already overdue, but progress%
  // is meaningfully behind the pace implied by elapsed time between start and
  // target date. Threshold of 15pp avoids flagging projects that are only
  // trivially behind schedule.
  var AT_RISK_THRESHOLD = 15;
  function isAtRisk(p) {
    if (p.status !== 'On Progress' || parseInt(p.is_overdue) === 1) return false;
    if (!p.start_date || !p.target_date) return false;
    var start = dOnly(p.start_date), target = dOnly(p.target_date), today = dOnly(new Date().toISOString().substring(0, 10));
    var totalDays = diffD(start, target);
    if (totalDays <= 0) return false;
    var elapsed = Math.max(0, Math.min(diffD(start, today), totalDays));
    var expectedPct = (elapsed / totalDays) * 100;
    return (parseInt(p.progress) || 0) < expectedPct - AT_RISK_THRESHOLD;
  }
  function atRiskBadgeHtml(p) {
    return isAtRisk(p) ? '<span class="badge badge-at-risk" title="Progress is behind the pace needed to hit the target date"><i class="fa fa-warning"></i> At Risk</span>' : '';
  }

  // ===== Data load =====
  function loadProjects() {
    $.ajax({
      url: 'ajax_project.php', method: 'POST', data: { action: 'list' }, dataType: 'json',
      success: function (r) {
        if (r.status !== 'success') { Swal.fire('Error', r.message || 'Failed to load data.', 'error'); return; }
        allProjects = r.data;
        renderPortfolio();
        updateCategoryList();
        updatePicList();
        updateMonthList();
        renderActive();
      },
      error: function () { Swal.fire('Error', 'Failed to contact the server.', 'error'); }
    });
  }

  // Animates a stat number from its previous value to a new one (eased count-up).
  function animateNumber(id, to, onFrame) {
    var el = document.getElementById(id);
    var from = parseInt(el.getAttribute('data-rawval')) || 0;
    el.setAttribute('data-rawval', to);
    if (from === to) { if (onFrame) onFrame(to); return; }
    var duration = 480, start = null;
    function step(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var val = Math.round(from + (to - from) * eased);
      if (onFrame) onFrame(val); else el.textContent = val;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  // Stat tiles reflect whatever is currently filtered (status/module/month/search),
  // not the all-time total — so "Total Projects" matches what's actually on screen.
  function updateStats(list) {
    var total = list.length;
    var c = { 'On Progress': 0, 'Done': 0, 'Live': 0 };
    var overdue = 0, progressSum = 0;
    list.forEach(function (p) {
      if (c[p.status] !== undefined) c[p.status]++;
      if (parseInt(p.is_overdue) === 1) overdue++;
      progressSum += parseInt(p.progress) || 0;
    });
    // Completion tidak menghitung item overdue (dikeluarkan dari basis).
    var base = total - overdue;
    // Done DAN Live sama-sama sudah selesai, jadi dua-duanya masuk hitungan
    // completion - kalau cuma 'Done', angka completion akan anjlok begitu
    // project dipindahkan ke kolom Live.
    var doneAll = c['Done'] + c['Live'];
    var rate = base > 0 ? Math.round((doneAll / base) * 100) : 0;

    animateNumber('st-total', total);
    animateNumber('st-progress', c['On Progress'], function (v) {
      document.getElementById('st-progress').firstChild.textContent = v;
    });
    animateNumber('st-live', c['Live']);
    animateNumber('st-done', doneAll);
    animateNumber('st-overdue', overdue);
    animateNumber('st-rate', rate, function (v) {
      document.getElementById('st-rate').textContent = v + '%';
      $('#rate-ring-txt').text(v + '%');
      $('#rate-ring').css('--deg', (v * 3.6) + 'deg');
    });

    $('#progress-live-dot').toggle(c['On Progress'] > 0);
    $('#overdue-tile').toggleClass('overdue-alert', overdue > 0);

    updateTileMeters(total, c, overdue, progressSum, base, doneAll);
    updateStatScope(total);
  }

  // Thin share-of-selection meters + one-line context under each stat tile.
  function updateTileMeters(total, c, overdue, progressSum, base, doneAll) {
    function share(n) { return total ? Math.round((n / total) * 100) : 0; }
    var avg = total ? Math.round(progressSum / total) : 0;
    $('#st-total-bar').css('width', avg + '%');
    $('#st-total-note').text(total ? avg + '% avg. progress' : 'No projects');
    [['progress', c['On Progress']], ['live', c['Live']], ['done', doneAll]].forEach(function (t) {
      $('#st-' + t[0] + '-bar').css('width', share(t[1]) + '%');
      $('#st-' + t[0] + '-note').text(share(t[1]) + '% of selection');
    });
    $('#st-overdue-bar').css('width', share(overdue) + '%');
    $('#st-overdue-note').html(overdue ? share(overdue) + '% need attention' : '<i class="fa fa-check"></i>All on schedule');
    $('#st-rate-note').text(doneAll + ' of ' + base + ' delivered');
  }

  function updateStatScope(total) {
    var parts = [];
    if ($('#month-filter').val()) parts.push($('#month-filter option:selected').text().replace(' (this month)', ''));
    if (activeFilter.length) parts.push(activeFilter.join(' + '));
    if ($('#module-filter').val()) parts.push($('#module-filter').val());
    if ($('#search-box').val()) parts.push('"' + $('#search-box').val() + '"');
    var scope = parts.length ? parts.join(' · ') : 'All time';
    $('#stat-scope').html('Showing <b>' + total + '</b> project' + (total !== 1 ? 's' : '') + ' &mdash; ' + scope);
  }

  // ===== Delivery Performance band =====
  // ALWAYS computed from allProjects (never from the filters) so the headline
  // numbers stay stable while the user slices the workspace below.
  // On-time rule mirrors Insights: Done + actual_date; no target or actual <= target = on time.
  function portfolioStats() {
    var s = {
      total: allProjects.length, delivered: 0, onTime: 0, late: 0, lead: [], active: [], planned: 0, live: 0,
      modules: {}, moduleOrder: [], byMonth: {}, first: '', lastTs: '', pics: {}, picOrder: []
    };
    allProjects.forEach(function (p) {
      var key = p.category || 'General';
      if (!s.modules[key]) { s.modules[key] = 0; s.moduleOrder.push(key); }
      s.modules[key]++;
      if (p.pic) { if (!s.pics[p.pic]) { s.pics[p.pic] = 0; s.picOrder.push(p.pic); } s.pics[p.pic]++; }
      var firstDate = p.start_date || p.target_date || '';
      if (firstDate && (!s.first || firstDate < s.first)) s.first = firstDate;
      var ts = p.updated_date || p.created_date || '';
      if (ts > s.lastTs) s.lastTs = ts;
      if (isDoneStatus(p.status)) {
        s.delivered++;
        if (p.status === 'Live') s.live++;
        if (p.actual_date) {
          var m = p.actual_date.substring(0, 7);
          s.byMonth[m] = (s.byMonth[m] || 0) + 1;
          if (!p.target_date || diffD(dOnly(p.target_date), dOnly(p.actual_date)) <= 0) s.onTime++; else s.late++;
          if (p.start_date) {
            var ld = diffD(dOnly(p.start_date), dOnly(p.actual_date));
            if (ld >= 0) s.lead.push(ld);
          }
        }
      } else if (p.status === 'On Progress') s.active.push(p);
      else s.planned++;
    });
    s.moduleOrder.sort(function (a, b) { return s.modules[b] - s.modules[a]; });
    s.picOrder.sort(function (a, b) { return s.pics[b] - s.pics[a]; });
    s.active.sort(function (a, b) { return (a.target_date || '9999').localeCompare(b.target_date || '9999'); });
    return s;
  }

  function monthKeyOffset(offset) {
    var d = new Date(); d = new Date(d.getFullYear(), d.getMonth() + offset, 1);
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
  }
  function monthShort(key) { return MONTH_NAMES[parseInt(key.split('-')[1], 10) - 1].substring(0, 3); }

  function renderPortfolio() {
    var s = portfolioStats();
    var measured = s.onTime + s.late;
    var onTimePct = measured ? Math.round((s.onTime / measured) * 100) : 0;
    var completionPct = s.total ? Math.round((s.delivered / s.total) * 100) : 0;
    var leadAvg = s.lead.length ? s.lead.reduce(function (a, b) { return a + b; }, 0) / s.lead.length : null;
    var sortedLead = s.lead.slice().sort(function (a, b) { return a - b; });
    var leadMedian = sortedLead.length ? (sortedLead.length % 2 ? sortedLead[(sortedLead.length - 1) / 2] : (sortedLead[sortedLead.length / 2 - 1] + sortedLead[sortedLead.length / 2]) / 2) : null;
    var sameDay = s.lead.filter(function (d) { return d === 0; }).length;
    var curKey = currentMonthKey, prevKey = monthKeyOffset(-1);
    var curCount = s.byMonth[curKey] || 0, prevCount = s.byMonth[prevKey] || 0;
    // Pembanding adil: bulan lalu s.d. tanggal yang sama (month-to-date vs month-to-date).
    var dom = new Date().getDate();
    var prevPace = allProjects.filter(function (p) {
      return p.status === 'Done' && p.actual_date && p.actual_date.substring(0, 7) === prevKey && parseInt(p.actual_date.substring(8, 10), 10) <= dom;
    }).length;
    var delta = curCount - prevPace;

    // --- hero meta chips ---
    var meta = '';
    if (s.first) meta += '<span class="hero-chip"><i class="fa fa-flag-o"></i>Tracking since <b>' + fmtDate(s.first, { month: 'short', year: 'numeric' }) + '</b></span>';
    if (s.picOrder.length) meta += '<span class="hero-chip"><i class="fa fa-user-o"></i>Key stakeholder <b>' + escHtml(s.picOrder[0]) + '</b></span>';
    if (s.lastTs) {
      var lastDay = s.lastTs.substring(0, 10), todayKeyStr = dKey(new Date());
      var when = (lastDay === todayKeyStr ? 'today' : fmtDate(lastDay, { day: '2-digit', month: 'short' })) + ', ' + s.lastTs.substring(11, 16);
      meta += '<span class="hero-chip"><i class="fa fa-clock-o"></i>Last update <b>' + when + '</b></span>';
    }
    $('#hero-meta').html(meta);

    // --- achievement flags ---
    var flags = '';
    if (measured > 0 && s.late === 0) flags += '<span class="perf-flag gold"><i class="fa fa-trophy"></i>Zero late deliveries</span>';
    else if (measured > 0 && onTimePct >= 90) flags += '<span class="perf-flag gold"><i class="fa fa-star"></i>' + onTimePct + '% on-time record</span>';
    if (s.delivered) flags += '<span class="perf-flag"><i class="fa fa-cubes"></i>' + s.delivered + ' features shipped</span>';
    $('#perf-flags').html(flags);

    // --- KPI cells ---
    function cell(cls, icon, label, value, unit, foot, visual) {
      return '<div class="kpi ' + cls + '">' +
        '<div class="kpi-label"><i class="fa ' + icon + '"></i>' + label + '</div>' +
        '<div class="kpi-value">' + value + (unit ? '<span class="kpi-unit">' + unit + '</span>' : '') + '</div>' +
        '<div class="kpi-foot">' + foot + '</div>' +
        '<div class="kpi-visual">' + visual + '</div>' +
      '</div>';
    }
    var html = '';
    html += cell('lead', 'fa-check-circle', 'Features delivered', s.delivered, 'of ' + s.total,
      '<b>' + completionPct + '%</b> of all projects completed',
      '<div class="kpi-meter"><span style="width:' + completionPct + '%;"></span></div>');
    html += cell('gold', 'fa-trophy', 'On-time delivery', measured ? onTimePct + '%' : '&mdash;', '',
      measured ? '<b>' + s.onTime + '</b> of ' + measured + ' on schedule &middot; ' + s.late + ' late' : 'No dated deliveries yet',
      '<div class="kpi-meter gold"><span style="width:' + onTimePct + '%;"></span></div>');

    var leadTxt = leadAvg === null ? '&mdash;' : (Math.round(leadAvg * 10) / 10).toFixed(1);
    var spark = '<div class="kpi-spark">';
    var leadBuckets = [0, 0, 0, 0];
    s.lead.forEach(function (d) { leadBuckets[d === 0 ? 0 : d === 1 ? 1 : d <= 3 ? 2 : 3]++; });
    var maxB = Math.max.apply(null, leadBuckets.concat([1]));
    var bucketNames = ['Same day', '1 day', '2-3 days', '4+ days'];
    leadBuckets.forEach(function (b, i) { spark += '<span class="' + (i < 2 ? 'cur' : '') + '" style="height:' + Math.max(8, Math.round(b / maxB * 100)) + '%;" title="' + bucketNames[i] + ': ' + b + '"></span>'; });
    spark += '</div>';
    html += cell('', 'fa-bolt', 'Avg. lead time', leadTxt, leadAvg === null ? '' : 'days',
      leadMedian === null ? 'Needs start &amp; delivery dates' : 'Median <b>' + leadMedian + 'd</b> &middot; <b>' + sameDay + '</b> same-day',
      spark);

    var act = s.active[0];
    var actFoot = act
      ? '<b>' + escHtml(act.project_name) + '</b> &middot; ' + (daysInfo(act.target_date, act.status).replace(/<[^>]+>/g, '') || 'no target')
      : s.planned ? s.planned + ' planned' : 'Nothing in flight';
    html += cell('', 'fa-play-circle', 'Active now', (s.active.length ? '<span class="kpi-live"></span>' : '') + s.active.length, 'in progress',
      actFoot,
      '<div class="kpi-meter"><span style="width:' + (s.total ? Math.round(((s.active.length + s.planned) / s.total) * 100) : 0) + '%;opacity:.55;"></span></div>');

    var seg = '<div class="kpi-segbar">' + s.moduleOrder.map(function (k) {
      var mc = moduleColor(k === 'General' ? null : k);
      return '<span style="flex:' + s.modules[k] + ';background:' + mc.c + ';" title="' + escJs(k) + ': ' + s.modules[k] + '"></span>';
    }).join('') + '</div>';
    html += cell('', 'fa-th-large', 'Modules covered', s.moduleOrder.length, 'modules',
      s.moduleOrder.map(function (k) { return escHtml(k) + ' <b>' + s.modules[k] + '</b>'; }).join(' &middot; '),
      seg);

    var deltaHtml = delta > 0 ? '<span class="kpi-delta up"><i class="fa fa-arrow-up"></i>' + delta + '</span>'
      : delta < 0 ? '<span class="kpi-delta down"><i class="fa fa-arrow-down"></i>' + Math.abs(delta) + '</span>'
      : '<span class="kpi-delta flat">=</span>';
    var months6 = [];
    for (var i = -5; i <= 0; i++) months6.push(monthKeyOffset(i));
    var max6 = Math.max.apply(null, months6.map(function (m) { return s.byMonth[m] || 0; }).concat([1]));
    var spark6 = '<div class="kpi-spark">' + months6.map(function (m) {
      var v = s.byMonth[m] || 0;
      return '<span class="' + (m === curKey ? 'cur' : m === prevKey ? 'prev' : '') + '" style="height:' + Math.max(8, Math.round(v / max6 * 100)) + '%;" title="' + monthShort(m) + ': ' + v + ' delivered"></span>';
    }).join('') + '</div>';
    html += cell('', 'fa-calendar-check-o', 'Delivered in ' + MONTH_NAMES[parseInt(curKey.split('-')[1], 10) - 1], curCount, deltaHtml,
      'vs <b>' + prevPace + '</b> by ' + monthShort(prevKey) + ' ' + dom + ' &middot; ' + prevCount + ' in ' + monthShort(prevKey),
      spark6);

    $('#perf-grid').html(html);
  }

  function updateCategoryList() {
    var cats = [];
    allProjects.forEach(function (p) { if (p.category && cats.indexOf(p.category) === -1) cats.push(p.category); });
    cats.sort();
    $('#pj-category-list').html(cats.map(function (c) { return '<option value="' + c + '">'; }).join(''));
    var current = $('#module-filter').val();
    var opts = '<option value="">All Modules</option>' + cats.map(function (c) { return '<option value="' + c + '">' + c + '</option>'; }).join('');
    $('#module-filter').html(opts).val(current);
  }

  function updatePicList() {
    var pics = [];
    allProjects.forEach(function (p) { if (p.pic && pics.indexOf(p.pic) === -1) pics.push(p.pic); });
    pics.sort();
    $('#pj-pic-list').html(pics.map(function (n) { return '<option value="' + n + '">'; }).join(''));
  }

  var currentMonthKey = (function () {
    var d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
  })();
  var monthFilterDefaulted = false;

  // A project occupies every month from its start_date to its target_date (inclusive),
  // not only the target month — so a Aug→Sep project shows under both August and September.
  function projectMonthSpan(p) {
    if (!p.target_date) return [];
    var endM = p.target_date.substring(0, 7);          // YYYY-MM
    var startM = p.start_date ? p.start_date.substring(0, 7) : endM;
    if (startM > endM) { var t = startM; startM = endM; endM = t; } // guard against inverted dates
    var out = [];
    var parts = startM.split('-');
    var y = parseInt(parts[0], 10), mo = parseInt(parts[1], 10), guard = 0;
    while (guard++ < 600) {
      var key = y + '-' + String(mo).padStart(2, '0');
      out.push(key);
      if (key === endM) break;
      mo++; if (mo > 12) { mo = 1; y++; }
    }
    return out;
  }

  function projectSpansMonth(p, month) {
    return projectMonthSpan(p).indexOf(month) !== -1;
  }

  function updateMonthList() {
    var months = [];
    allProjects.forEach(function (p) {
      projectMonthSpan(p).forEach(function (m) {
        if (months.indexOf(m) === -1) months.push(m);
      });
    });
    if (months.indexOf(currentMonthKey) === -1) months.push(currentMonthKey);
    months.sort().reverse(); // most recent first
    var current = $('#month-filter').val();
    var opts = '<option value="">All Months</option>' + months.map(function (m) {
      var parts = m.split('-');
      var label = MONTH_NAMES[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
      var isCurrent = m === currentMonthKey;
      return '<option value="' + m + '">' + label + (isCurrent ? ' (this month)' : '') + '</option>';
    }).join('');
    $('#month-filter').html(opts);
    if (!monthFilterDefaulted) {
      $('#month-filter').val(currentMonthKey);
      monthFilterDefaulted = true;
    } else {
      $('#month-filter').val(current);
    }
  }

  // Status boleh dipilih LEBIH DARI SATU: tiap pill nyala/mati sendiri, "All" mengosongkan.
  // Parameter el dibiarkan (dipakai pemanggil lama) tapi tidak lagi dipakai — tombol
  // yang menyala ditentukan dari isi activeFilter lewat syncFilterPills().
  function setFilter(status, el) {
    if (!status) {
      activeFilter = [];
    } else {
      var i = activeFilter.indexOf(status);
      if (i === -1) activeFilter.push(status); else activeFilter.splice(i, 1);
    }
    syncFilterPills();
    renderActive();
  }

  function syncFilterPills() {
    $('.proj-filter-pill').each(function () {
      var v = $(this).attr('data-status') || '';
      $(this).toggleClass('active', v ? activeFilter.indexOf(v) !== -1 : activeFilter.length === 0);
    });
  }

  function setModuleFilter(mod) {
    $('#module-filter').val(mod);
    renderActive();
  }

  function clearFilters() {
    activeFilter = [];
    syncFilterPills();
    $('#module-filter').val('');
    $('#month-filter').val('');
    $('#search-box').val('');
    renderActive();
  }

  function updateClearFiltersVisibility() {
    var anyActive = activeFilter.length > 0 || !!$('#module-filter').val() || !!$('#month-filter').val() || !!$('#search-box').val();
    $('#clear-filters').toggleClass('show', anyActive);
  }

  function getFiltered(opts) {
    opts = opts || {};
    var q = ($('#search-box').val() || '').toLowerCase();
    var mod = opts.ignoreModule ? '' : $('#module-filter').val();
    var month = $('#month-filter').val();
    return allProjects.filter(function (p) {
      if (activeFilter.length && activeFilter.indexOf(p.status) === -1) return false;
      if (mod && p.category !== mod) return false;
      if (month && !projectSpansMonth(p, month)) return false;
      if (q && p.project_name.toLowerCase().indexOf(q) === -1) return false;
      return true;
    });
  }

  // ===== Module summary strip (always reflects all data, doubles as a filter shortcut) =====
  function renderModuleSummary() {
    var groups = {};
    var order = [];
    // Respects status/month/search filters so counts match "Showing N projects"
    // above, but ignores the module filter itself so every module card stays
    // visible and clickable (switching between them, not just clearing).
    getFiltered({ ignoreModule: true }).forEach(function (p) {
      var key = p.category || 'General';
      if (!groups[key]) { groups[key] = { count: 0, progressSum: 0 }; order.push(key); }
      groups[key].count++;
      groups[key].progressSum += parseInt(p.progress) || 0;
    });
    order.sort(function (a, b) { return groups[b].count - groups[a].count; });

    var activeMod = $('#module-filter').val();
    var html = order.map(function (key) {
      var g = groups[key];
      var avg = g.count ? Math.round(g.progressSum / g.count) : 0;
      var mc = moduleColor(key === 'General' ? null : key);
      var isActive = activeMod === key;
      return '<div class="mod-sum-card' + (isActive ? ' active' : '') + '" style="--mc:' + mc.c + ';--mcs:' + mc.s + ';" onclick="setModuleFilter(\'' + (isActive ? '' : escJs(key)) + '\')" title="' + (isActive ? 'Clear module filter' : 'Filter by ' + escJs(key)) + '">' +
        '<span class="mod-sum-icon"><i class="fa ' + moduleIcon(key === 'General' ? null : key) + '"></i></span>' +
        '<div><div class="mod-sum-name">' + escHtml(key) + '</div><div class="mod-sum-meta">' + g.count + ' project' + (g.count !== 1 ? 's' : '') + '</div></div>' +
        '<div class="mod-sum-ring" style="--deg:' + (avg * 3.6) + 'deg;"><span>' + avg + '%</span></div>' +
      '</div>';
    }).join('');
    $('#module-summary').html(html);
  }

  // ===== Export to Excel (respects current filters) =====
  function exportExcel() {
    var params = {
      status: activeFilter.join(','),
      module: $('#module-filter').val(),
      month: $('#month-filter').val(),
      search: $('#search-box').val() || ''
    };
    var qs = Object.keys(params).map(function (k) {
      return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]);
    }).join('&');
    window.open('ekspor_project.php?' + qs, '_blank');
  }

  // ===== View switching =====
  function switchView(view) {
    currentView = view;
    $('.view-switch button').removeClass('active');
    $('.view-switch button[data-view="' + view + '"]').addClass('active');
    $('#view-board, #view-list, #view-calendar, #view-timeline, #view-insights').hide();
    $('#view-' + view).removeClass('view-fade-in').show();
    void document.getElementById('view-' + view).offsetWidth; // restart the animation
    $('#view-' + view).addClass('view-fade-in');
    if ((view === 'calendar' || view === 'timeline') && calYear === undefined) {
      var now = new Date(); calYear = now.getFullYear(); calMonth = now.getMonth();
    }
    renderActive();
  }
  function renderActive() {
    updateClearFiltersVisibility();
    updateStats(getFiltered());
    renderModuleSummary();
    if (currentView === 'board') renderBoard();
    else if (currentView === 'list') renderList();
    else if (currentView === 'calendar') renderCalendar();
    else if (currentView === 'timeline') renderTimeline();
    else renderInsights();
  }

  function emptyStateHtml(icon, title, sub) {
    return '<div class="empty-state"><div class="es-ico"><i class="fa ' + icon + '"></i></div>' +
      '<div class="es-title">' + title + '</div><div class="es-sub">' + sub + '</div></div>';
  }

  // ===== Shared card renderer =====
  function cardHtml(p, idx, compact) {
    var startTxt  = fmtDate(p.start_date);
    var targetTxt = fmtDate(p.target_date);
    var dateLine  = (startTxt || '?') + ' &rarr; ' + (targetTxt || '?');
    var mc = moduleColor(p.category);
    var secondaryInfo = isDoneStatus(p.status) ? completionBadge(p.target_date, p.actual_date) : daysInfo(p.target_date, p.status);
    var cardCls = 'proj-card proj-card-enter' + (compact ? ' compact' : '');
    var dragAttrs = compact ? ' draggable="true" data-id="' + p.id + '"' : '';
    var progress = parseInt(p.progress) || 0;
    return '' +
      '<div class="' + cardCls + '"' + dragAttrs + ' onclick="openDrawer(' + p.id + ')" style="--mod-color:' + mc.c + ';--mod-color-soft:' + mc.s + ';animation-delay:' + Math.min((idx||0) * 35, 350) + 'ms;">' +
        '<div class="pc-top">' +
          '<span class="cat"><span class="cat-dot"></span>' + (p.category ? escHtml(p.category) : 'General') + '</span>' +
          '<span class="pc-flags">' + atRiskBadgeHtml(p) + '<span class="badge badge-priority-' + p.priority + '">' + p.priority + '</span></span>' +
        '</div>' +
        '<div class="name">' + escHtml(p.project_name) + '</div>' +
        '<div class="desc">' + (p.description ? escHtml(p.description) : '<span class="text-muted">No description.</span>') + '</div>' +
        '<div class="pc-progress">' +
          '<div class="progress"><div class="progress-bar" style="width:' + progress + '%;background:' + progressColor(p.status) + ';"></div></div>' +
          '<span class="pct">' + progress + '%</span>' +
        '</div>' +
        '<div class="meta-row"><span><i class="fa fa-calendar"></i>' + dateLine + '</span><span class="' + (p.status === 'Done' ? 'delivered' : '') + '">' + secondaryInfo + '</span></div>' +
        (compact ? '' : '<div class="mt-2"><span class="badge ' + statusClass(p.status) + '">' + p.status + '</span></div>') +
        '<div class="footer-row">' +
          picChipHtml(p.pic) +
          '<span class="card-actions">' +
            '<button class="icon-btn" onclick="event.stopPropagation();openEdit(' + p.id + ')" title="Edit"><i class="fa fa-pencil"></i></button>' +
            '<button class="icon-btn danger" onclick="event.stopPropagation();doDelete(' + p.id + ',\'' + escJs(p.project_name) + '\')" title="Delete"><i class="fa fa-trash"></i></button>' +
          '</span>' +
        '</div>' +
      '</div>';
  }

  // ===== Board (kanban) =====
  var BOARD_EMPTY = {
    'Planned':     { icon: 'fa-lightbulb-o',  title: 'Nothing planned' },
    'On Progress': { icon: 'fa-play-circle',  title: 'Nothing in progress' },
    'Done':        { icon: 'fa-check-circle', title: 'Nothing waiting to go live' },
    'Live':        { icon: 'fa-rocket',       title: 'Nothing live yet' }
  };
  function renderBoard() {
    var list = getFiltered();
    var cols = activeFilter.length ? STATUS_COLS.filter(function (c) { return activeFilter.indexOf(c.key) !== -1; }) : STATUS_COLS;
    if (list.length === 0) {
      $('#view-board').html(emptyStateHtml('fa-folder-open-o', 'No projects found', 'Try another month, module or status filter.'));
      return;
    }
    var html = '<div class="board-wrap">';
    cols.forEach(function (col) {
      var items = list.filter(function (p) { return p.status === col.key; });
      var avg = items.length ? Math.round(items.reduce(function (a, p) { return a + (parseInt(p.progress) || 0); }, 0) / items.length) : 0;
      html += '<div class="board-col" style="--col:' + col.dot + ';">' +
        '<div class="board-col-head"><span class="title"><span class="dot"></span>' + col.key +
          (items.length && !isDoneStatus(col.key) ? '<span class="board-col-sub">&middot; avg ' + avg + '%</span>' : '') + '</span>' +
          '<span class="count">' + items.length + '</span></div>' +
        '<div class="board-col-body" data-status="' + col.key + '">';
      if (items.length === 0) {
        var em = BOARD_EMPTY[col.key] || BOARD_EMPTY['Planned'];
        html += '<div class="board-empty-hint"><span class="ico"><i class="fa ' + em.icon + '"></i></span><b>' + em.title + '</b>Drag a card here to move it</div>';
      } else {
        items.forEach(function (p, idx) { html += cardHtml(p, idx, true); });
      }
      html += '</div></div>';
    });
    html += '</div>';
    $('#view-board').html(html);
  }

  // ===== List (spreadsheet-style table) =====
  var listSortKey = 'target_date', listSortDir = 1;
  function sortArrowHtml(key) {
    if (listSortKey !== key) return '<span class="sort-arrow">&#9650;</span>';
    return '<span class="sort-arrow active">' + (listSortDir === 1 ? '&#9650;' : '&#9660;') + '</span>';
  }
  function renderList() {
    var list = getFiltered();
    list.sort(function (a, b) {
      var av = a[listSortKey] || '', bv = b[listSortKey] || '';
      if (listSortKey === 'progress') { av = parseInt(av); bv = parseInt(bv); }
      return (av > bv ? 1 : av < bv ? -1 : 0) * listSortDir;
    });
    if (list.length === 0) {
      $('#view-list').html(emptyStateHtml('fa-table', 'No projects found', 'Try another month, module or status filter.'));
      return;
    }
    var html = '<div class="proj-table-wrap"><table class="proj-table"><thead><tr>' +
      '<th class="no-sort">No</th>' +
      '<th onclick="listSort(\'category\')">Module' + sortArrowHtml('category') + '</th>' +
      '<th onclick="listSort(\'project_name\')">Project' + sortArrowHtml('project_name') + '</th>' +
      '<th class="no-sort">Description</th>' +
      '<th onclick="listSort(\'priority\')">Priority' + sortArrowHtml('priority') + '</th>' +
      '<th onclick="listSort(\'pic\')">Req. By' + sortArrowHtml('pic') + '</th>' +
      '<th onclick="listSort(\'progress\')">Progress' + sortArrowHtml('progress') + '</th>' +
      '<th onclick="listSort(\'status\')">Status' + sortArrowHtml('status') + '</th>' +
      '<th onclick="listSort(\'start_date\')">Start' + sortArrowHtml('start_date') + '</th>' +
      '<th onclick="listSort(\'target_date\')">Target' + sortArrowHtml('target_date') + '</th>' +
      '<th class="no-sort">Delivery</th>' +
      '<th class="no-sort text-right">Actions</th>' +
      '</tr></thead><tbody>';
    list.forEach(function (p, idx) {
      var mc = moduleColor(p.category);
      var secondary = (isDoneStatus(p.status) ? completionBadge(p.target_date, p.actual_date) : daysInfo(p.target_date, p.status)) +
        (isAtRisk(p) ? ' ' + atRiskBadgeHtml(p) : '');
      var dash = '<span class="muted-dash">&mdash;</span>';
      var statusOpts = ['Planned', 'On Progress', 'Done', 'Live'].map(function (s) {
        return '<option value="' + s + '"' + (s === p.status ? ' selected' : '') + '>' + s + '</option>';
      }).join('');
      html += '<tr style="cursor:pointer;" onclick="openDrawer(' + p.id + ')">' +
        '<td class="num-cell">' + (idx + 1) + '</td>' +
        '<td><span class="mod-tag" style="--mc:' + mc.c + ';"><span class="dot"></span>' + (p.category ? escHtml(p.category) : 'General') + '</span></td>' +
        '<td class="wrap"><span class="proj-name">' + escHtml(p.project_name) + '</span></td>' +
        '<td class="desc-cell" title="' + escJs(p.description || '') + '"><div class="desc-clamp">' + (p.description ? escHtml(p.description) : dash) + '</div></td>' +
        '<td><span class="badge badge-priority-' + p.priority + '">' + p.priority + '</span></td>' +
        '<td>' + picChipHtml(p.pic) + '</td>' +
        '<td><span class="list-progress"><span class="bar" style="width:' + p.progress + '%;background:' + progressColor(p.status) + ';"></span></span><span class="list-pct">' + p.progress + '%</span></td>' +
        '<td onclick="event.stopPropagation();"><select class="status-select ss-' + p.status.replace(/\s+/g, '') + '" onchange="quickStatusChange(' + p.id + ', this.value)">' + statusOpts + '</select></td>' +
        '<td class="date-cell">' + (fmtDate(p.start_date) || dash) + '</td>' +
        '<td class="date-cell">' + (fmtDate(p.target_date) || dash) + '</td>' +
        '<td>' + (secondary || dash) + '</td>' +
        '<td class="text-right" onclick="event.stopPropagation();"><button class="icon-btn mr-1" onclick="openEdit(' + p.id + ')" title="Edit"><i class="fa fa-pencil"></i></button>' +
        '<button class="icon-btn danger" onclick="doDelete(' + p.id + ',\'' + escJs(p.project_name) + '\')" title="Delete"><i class="fa fa-trash"></i></button></td>' +
        '</tr>';
    });
    html += '</tbody></table></div>';
    $('#view-list').html(html);
  }
  function listSort(key) {
    if (listSortKey === key) listSortDir *= -1; else { listSortKey = key; listSortDir = 1; }
    renderList();
  }

  // ===== Calendar (multi-day spanning event bars) =====
  function calNav(delta) {
    calMonth += delta;
    if (calMonth < 0) { calMonth = 11; calYear--; }
    if (calMonth > 11) { calMonth = 0; calYear++; }
    renderMonthView();
  }
  function calToday() {
    var now = new Date(); calYear = now.getFullYear(); calMonth = now.getMonth();
    renderMonthView();
  }

  // Tombol navigasi bulan dipakai bersama oleh Calendar dan Timeline, jadi yang
  // digambar ulang harus view yang sedang tampil (dulu selalu renderCalendar,
  // akibatnya di Timeline tombolnya terlihat tidak berfungsi).
  function renderMonthView() {
    if (currentView === "timeline") renderTimeline(); else renderCalendar();
  }

  function dOnly(str) { var d = new Date(str + 'T00:00:00'); d.setHours(0,0,0,0); return d; }
  function addD(date, n) { var d = new Date(date); d.setDate(d.getDate() + n); return d; }
  function diffD(a, b) { return Math.round((b - a) / 86400000); }
  function dKey(date) {
    return date.getFullYear() + '-' + String(date.getMonth()+1).padStart(2,'0') + '-' + String(date.getDate()).padStart(2,'0');
  }

  // Greedy interval-graph lane assignment so overlapping bars stack instead of colliding.
  function assignLanes(segments) {
    segments.sort(function (a, b) { return a.startCol - b.startCol || (b.endCol - b.startCol) - (a.endCol - a.startCol); });
    var laneEnds = []; // last occupied column per lane
    segments.forEach(function (seg) {
      var lane = laneEnds.findIndex(function (end) { return end < seg.startCol; });
      if (lane === -1) { lane = laneEnds.length; laneEnds.push(seg.endCol); }
      else { laneEnds[lane] = seg.endCol; }
      seg.lane = lane;
    });
    return laneEnds.length;
  }

  function renderCalendar() {
    if (calYear === undefined) { var n0 = new Date(); calYear = n0.getFullYear(); calMonth = n0.getMonth(); }
    var list = getFiltered().filter(function (p) { return !!p.target_date; });

    var firstOfMonth = new Date(calYear, calMonth, 1);
    var lastOfMonth  = new Date(calYear, calMonth + 1, 0);
    var gridStart = addD(firstOfMonth, -firstOfMonth.getDay());
    var gridEnd   = addD(lastOfMonth, 6 - lastOfMonth.getDay());
    var numWeeks  = Math.round((diffD(gridStart, gridEnd) + 1) / 7);
    var today = new Date(); today.setHours(0,0,0,0);
    var todayKey = dKey(today);

    // Sabtu & Minggu libur, ditambah hari libur nasional/cuti bersama dari tabel.
    // Libur yang jatuh di akhir pekan tidak dihitung dua kali.
    var daysInMonth = lastOfMonth.getDate(), weekendDays = 0, holidayDays = 0;
    for (var dd = 1; dd <= daysInMonth; dd++) {
      var cd = new Date(calYear, calMonth, dd), wd = cd.getDay();
      if (wd === 0 || wd === 6) { weekendDays++; continue; }
      if (isDayOff(holidayOn(dKey(cd)))) holidayDays++;
    }
    var workDays = daysInMonth - weekendDays - holidayDays;

    // Bar hanya digambar di dalam bulan aktif; tanggal bulan sebelum/sesudah dibiarkan kosong.
    var ranges = list.map(function (p) {
      var end = dOnly(p.target_date);
      var start = p.start_date ? dOnly(p.start_date) : end;
      if (start > end) start = end;
      return { p: p, start: start, end: end };
    }).filter(function (r) { return r.end >= firstOfMonth && r.start <= lastOfMonth; });
    var dueCount = ranges.filter(function (r) { return r.end <= lastOfMonth && r.end >= firstOfMonth; }).length;

    var chip = function (cls, icon, num, label) {
      return '<span class="cal-chip ' + cls + '"><i class="fa ' + icon + '"></i><b>' + num + '</b>' + label + '</span>';
    };
    var html = '<div class="cal-card">' +
      '<div class="cal-toolbar">' +
        '<div class="cal-head">' +
          '<div class="title">' + MONTH_NAMES[calMonth] + ' ' + calYear + '</div>' +
          '<div class="cal-chips">' +
            chip('', 'fa-folder-open-o', ranges.length, 'project' + (ranges.length !== 1 ? 's' : '')) +
            chip('', 'fa-flag-checkered', dueCount, 'due this month') +
            chip('', 'fa-briefcase', workDays, 'working days') +
            chip('weekend', 'fa-coffee', weekendDays, 'weekend days') +
            (holidayDays ? chip('holiday', 'fa-flag', holidayDays, 'holiday' + (holidayDays !== 1 ? 's' : '')) : '') +
          '</div>' +
        '</div>' +
        '<div class="cal-nav">' +
          '<button class="nav-arrow" onclick="calNav(-1)" title="Previous month"><i class="fa fa-chevron-left"></i></button>' +
          '<button class="today-btn" onclick="calToday()">Today</button>' +
          '<button class="nav-arrow" onclick="calNav(1)" title="Next month"><i class="fa fa-chevron-right"></i></button>' +
        '</div>' +
      '</div>';

    html += '<div class="cal-legend">' +
      STATUS_COLS.map(function (c) {
        return '<span class="lg"><span class="lg-dot" style="background:' + statusMeta(c.key).dot + ';"></span>' + c.key + '</span>';
      }).join('') +
      '<span class="lg lg-overdue"><span class="lg-dot"></span>Overdue</span>' +
      '<span class="lg-sep"></span>' +
      '<span class="lg"><span class="lg-sw weekend"></span>Weekend &middot; Sat &amp; Sun off</span>' +
      '<span class="lg"><span class="lg-sw holiday"></span>Public holiday &middot; off</span>' +
      '<span class="lg"><span class="lg-sw holiday-prod"></span>Production holiday</span>' +
      '<span class="lg"><span class="lg-sw today"></span>Today</span>' +
    '</div>';

    var monthFilter = $('#month-filter').val();
    var viewKey = calYear + '-' + String(calMonth + 1).padStart(2, '0');
    if (monthFilter && monthFilter !== viewKey) {
      html += '<div class="cal-filter-note"><i class="fa fa-filter"></i>Month filter is set to <b>' + $('#month-filter option:selected').text().replace(' (this month)', '') + '</b> &mdash; projects in ' + MONTH_NAMES[calMonth] + ' are hidden.' +
        '<button type="button" onclick="$(\'#month-filter\').val(\'\'); renderActive();">Show all months</button></div>';
    }

    html += '<div class="cal-dow-row">';
    ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(function (d, i) {
      var isWknd = i === 0 || i === 6;
      html += '<div class="cal-dow' + (isWknd ? ' weekend' : '') + '">' + d + (isWknd ? '<span class="dow-off">Off</span>' : '') + '</div>';
    });
    html += '</div>';

    for (var w = 0; w < numWeeks; w++) {
      var weekStart = addD(gridStart, w * 7);
      var weekEnd   = addD(weekStart, 6);
      var visStart  = weekStart < firstOfMonth ? firstOfMonth : weekStart;
      var visEnd    = weekEnd > lastOfMonth ? lastOfMonth : weekEnd;

      // Potong rentang tiap proyek ke minggu ini DAN ke bulan aktif.
      var segs = [];
      ranges.forEach(function (r) {
        var segStart = r.start > visStart ? r.start : visStart;
        var segEnd   = r.end < visEnd ? r.end : visEnd;
        if (segStart > segEnd) return;
        segs.push({
          p: r.p,
          startCol: diffD(weekStart, segStart),
          endCol: diffD(weekStart, segEnd),
          contLeft: r.start < segStart,
          contRight: r.end > segEnd
        });
      });
      var maxLanes = assignLanes(segs);
      var weekHeight = 26 + Math.max(maxLanes, 1) * 24 + 6;

      html += '<div class="cal-week" style="min-height:' + weekHeight + 'px;">';
      for (var c = 0; c < 7; c++) {
        var cellDate = addD(weekStart, c);
        var outside = cellDate.getMonth() !== calMonth;
        var isWeekend = c === 0 || c === 6;
        var isToday = dKey(cellDate) === todayKey;
        var hol = outside ? null : holidayOn(dKey(cellDate));
        var holCls = hol ? (hol.t === 'production' ? ' holiday-prod' : ' holiday') : '';
        var num = outside ? '' : (isToday ? '<span class="today-badge">' + cellDate.getDate() + '</span>' : cellDate.getDate());
        var holName = hol ? '<span class="cal-hol' + (hol.t === 'production' ? ' prod' : '') + '" title="' + escJs(hol.n) + '">' + escHtml(hol.n) + '</span>' : '';
        html += '<div class="cal-daycell' + (outside ? ' outside' : '') + (isWeekend ? ' weekend' : '') + holCls + (isToday ? ' is-today' : '') + '"><div class="cal-daynum">' + num + holName + '</div></div>';
      }
      html += '<div class="cal-week-events">';
      segs.forEach(function (seg) {
        var mc = moduleColor(seg.p.category);
        var sm = statusMeta(seg.p.status);
        var overdue = parseInt(seg.p.is_overdue) === 1;
        var cls = 'cal-bar ' + sm.cls + (overdue ? ' overdue-bar' : '') + (seg.contLeft ? ' cont-left' : '') + (seg.contRight ? ' cont-right' : '');
        html += '<span class="' + cls + '" style="grid-column:' + (seg.startCol + 1) + ' / ' + (seg.endCol + 2) + ';grid-row:' + (seg.lane + 1) + ';--mc:' + mc.c + ';--mcs:' + mc.s + ';" onclick="openEdit(' + seg.p.id + ')" title="' + escJs(seg.p.project_name + ' — ' + seg.p.status + (overdue ? ' (overdue)' : (isAtRisk(seg.p) ? ' (at risk)' : ''))) + '">' +
          '<i class="fa ' + sm.icon + ' status-ico"></i>' + escHtml(seg.p.project_name) +
          '</span>';
      });
      html += '</div></div>';
    }

    html += '</div>';
    $('#view-calendar').html(html);
  }

  // ===== Timeline (Gantt-lite, shares month navigation with Calendar) =====
  function getTimelineRows(list, year, month) {
    var monthStart = new Date(year, month, 1);
    var monthEnd = new Date(year, month + 1, 0);
    var rows = [];
    list.forEach(function (p) {
      if (!p.target_date) return;
      var realEnd = dOnly(p.target_date);
      var realStart = p.start_date ? dOnly(p.start_date) : realEnd;
      if (realStart > realEnd) realStart = realEnd;
      if (realEnd < monthStart || realStart > monthEnd) return;
      var clipStart = realStart > monthStart ? realStart : monthStart;
      var clipEnd = realEnd < monthEnd ? realEnd : monthEnd;
      rows.push({
        p: p,
        startCol: diffD(monthStart, clipStart),
        endCol: diffD(monthStart, clipEnd),
        contLeft: realStart < clipStart,
        contRight: realEnd > clipEnd
      });
    });
    rows.sort(function (a, b) { return a.startCol - b.startCol || a.p.project_name.localeCompare(b.p.project_name); });
    return { rows: rows, monthStart: monthStart, daysInMonth: diffD(monthStart, monthEnd) + 1 };
  }

  function renderTimeline() {
    if (calYear === undefined) { var n0 = new Date(); calYear = n0.getFullYear(); calMonth = n0.getMonth(); }
    var data = getTimelineRows(getFiltered(), calYear, calMonth);
    var rows = data.rows;
    var daysInMonth = data.daysInMonth;
    var minDayW = 22, labelW = 210;
    var today = new Date(); today.setHours(0, 0, 0, 0);
    var todayCol = diffD(data.monthStart, today);

    var toolbar = '<div class="cal-toolbar">' +
      '<div class="title">' + MONTH_NAMES[calMonth] + ' ' + calYear + '</div>' +
      '<div class="cal-nav">' +
        '<button class="nav-arrow" onclick="calNav(-1)"><i class="fa fa-chevron-left"></i></button>' +
        '<button class="today-btn" onclick="calToday()">Today</button>' +
        '<button class="nav-arrow" onclick="calNav(1)"><i class="fa fa-chevron-right"></i></button>' +
      '</div>' +
    '</div>' + statusLegendHtml();

    if (rows.length === 0) {
      $('#view-timeline').html('<div class="tl-card">' + toolbar +
        '<div class="empty-state"><i class="fa fa-tasks" style="font-size:36px;"></i><div class="mt-2">No dated projects in this month.</div></div></div>');
      return;
    }

    // min-width keeps day columns legible on narrow screens (scrolls below it);
    // on wide screens the grid's own width:100% + flex:1 day columns stretch to fill.
    var gridMinW = labelW + daysInMonth * minDayW;
    var html = '<div class="tl-card">' + toolbar + '<div class="tl-scroll"><div class="tl-grid" style="min-width:' + gridMinW + 'px;">';

    html += '<div class="tl-head-row"><div class="tl-head-label">Project</div><div class="tl-head-days">';
    for (var d = 1; d <= daysInMonth; d++) {
      var cellDate = addD(data.monthStart, d - 1);
      var dow = cellDate.getDay();
      var isWeekend = dow === 0 || dow === 6;
      var isToday = (d - 1) === todayCol;
      var tlHol = holidayOn(dKey(cellDate));
      var tlHolCls = tlHol ? (tlHol.t === 'production' ? ' holiday-prod' : ' holiday') : '';
      html += '<div class="tl-day-col' + (isWeekend ? ' weekend' : '') + tlHolCls + (isToday ? ' today' : '') + '"' + (tlHol ? ' title="' + escJs(tlHol.n) + '"' : '') + '>' + d + '</div>';
    }
    html += '</div></div>';

    rows.forEach(function (r) {
      var mc = moduleColor(r.p.category);
      var sm = statusMeta(r.p.status);
      var overdue = parseInt(r.p.is_overdue) === 1;
      var leftPct = (r.startCol / daysInMonth) * 100;
      var widthPct = ((r.endCol - r.startCol + 1) / daysInMonth) * 100;
      var todayLine = (todayCol >= 0 && todayCol < daysInMonth)
        ? '<div class="tl-today-line" style="left:calc(' + ((todayCol + 0.5) / daysInMonth * 100) + '% - 1px);"></div>' : '';
      var barCls = 'tl-bar ' + sm.cls + (overdue ? ' overdue-bar' : '') + (r.contLeft ? ' cont-left' : '') + (r.contRight ? ' cont-right' : '');
      html += '<div class="tl-row">' +
        '<div class="tl-row-label">' +
          '<span class="mod-tag" style="--mc:' + mc.c + ';"><span class="dot"></span></span>' +
          (r.p.pic ? '<span class="pic-avatar" style="background:' + picColor(r.p.pic).c + ';" title="' + escJs(r.p.pic) + '">' + picInitials(r.p.pic) + '</span>' : '') +
          '<span class="tl-row-name" title="' + escJs(r.p.project_name) + '">' + escHtml(r.p.project_name) + '</span>' +
        '</div>' +
        '<div class="tl-row-track">' + todayLine +
          '<div class="' + barCls +
            '" style="left:calc(' + leftPct + '% + 2px);width:calc(' + widthPct + '% - 4px);background:' + mc.s + ';" onclick="openEdit(' + r.p.id + ')" title="' + escJs(r.p.project_name + ' — ' + r.p.status + ' — ' + r.p.progress + '%' + (overdue ? ' (overdue)' : (isAtRisk(r.p) ? ' (at risk)' : ''))) + '">' +
            '<div class="tl-bar-fill" style="width:' + r.p.progress + '%;background:' + mc.c + ';"></div>' +
            '<i class="fa ' + sm.icon + ' tl-bar-icon"></i>' +
          '</div>' +
        '</div>' +
      '</div>';
    });

    html += '</div></div></div>';
    $('#view-timeline').html(html);
  }

  // ===== Insights (charts + recent activity) =====
  function buildDonutGradient(segments) {
    var acc = 0;
    var stops = segments.map(function (s) {
      var start = acc; acc += s.pct;
      return s.color + ' ' + start + '% ' + acc + '%';
    });
    return 'conic-gradient(' + stops.join(', ') + ')';
  }
  function statusBadgeInline(status) {
    return '<span class="badge ' + statusClass(status) + '" style="font-size:9.5px;padding:1px 6px;">' + status + '</span>';
  }
  function relativeTime(ts) {
    if (!ts) return '';
    var then = new Date(ts.replace(' ', 'T'));
    var diffSec = Math.round((new Date() - then) / 1000);
    if (diffSec < 60) return 'Just now';
    if (diffSec < 3600) return Math.floor(diffSec / 60) + ' min ago';
    if (diffSec < 86400) return Math.floor(diffSec / 3600) + ' hr ago';
    var days = Math.floor(diffSec / 86400);
    if (days === 1) return 'Yesterday';
    if (days < 30) return days + ' days ago';
    return fmtDate(ts.substring(0, 10));
  }

  function renderInsights() {
    var list = getFiltered();
    var html = '<div class="insights-grid">';
    var todayD = new Date(); todayD.setHours(0, 0, 0, 0);

    // ---- Delivery trend (last 6 months incl. current) ----
    var doneList = list.filter(function (p) { return isDoneStatus(p.status) && p.actual_date; });
    var monthCounts = {};
    doneList.forEach(function (p) {
      var m = p.actual_date.substring(0, 7);
      monthCounts[m] = (monthCounts[m] || 0) + 1;
    });
    var trendMonths = [];
    var now = new Date();
    for (var i = 5; i >= 0; i--) {
      var d = new Date(now.getFullYear(), now.getMonth() - i, 1);
      trendMonths.push(d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0'));
    }
    var trendVals = trendMonths.map(function (m) { return monthCounts[m] || 0; });
    var trendTotal = trendVals.reduce(function (a, b) { return a + b; }, 0);
    var activeMonths = trendVals.filter(function (v) { return v > 0; }).length;
    var maxCount = Math.max.apply(null, trendVals.concat([1]));
    var axisMax = Math.max(4, Math.ceil(maxCount / 4) * 4);
    html += '<div class="chart-card span-8">' +
      '<div class="cc-head"><div><h5>Delivery Trend</h5><div class="cc-sub">Projects completed per month &middot; last 6 months</div></div>' +
        '<div class="cc-stats">' +
          '<div class="cc-stat"><div class="v">' + trendTotal + '<small>shipped</small></div><div class="k">last 6 months</div></div>' +
          '<div class="cc-stat"><div class="v">' + (activeMonths ? (Math.round(trendTotal / activeMonths * 10) / 10) : 0) + '<small>/ month</small></div><div class="k">avg. in active months</div></div>' +
        '</div></div>' +
      '<div class="trend-plot">' +
        '<div class="trend-grid" style="top:0;"><span>' + axisMax + '</span></div>' +
        '<div class="trend-grid" style="top:50%;"><span>' + (axisMax / 2) + '</span></div>' +
        '<div class="trend-grid" style="top:100%;"><span>0</span></div>' +
        '<div class="trend-chart">';
    trendMonths.forEach(function (m, idx) {
      var c = trendVals[idx];
      var h = Math.round((c / axisMax) * 100);
      var isCur = idx === trendMonths.length - 1;
      html += '<div class="trend-col"><div class="trend-bar-wrap">' +
        '<div class="trend-bar' + (isCur ? ' cur' : '') + (c === 0 ? ' zero' : '') + '" style="height:' + (c === 0 ? 0 : Math.max(h, 3)) + '%;" title="' + c + ' completed">' +
          '<span class="trend-val">' + c + '</span>' +
        '</div></div></div>';
    });
    html += '</div></div><div class="trend-lbls">';
    trendMonths.forEach(function (m, idx) {
      var parts = m.split('-');
      html += '<span class="trend-lbl' + (idx === trendMonths.length - 1 ? ' cur' : '') + '">' + MONTH_NAMES[parseInt(parts[1], 10) - 1].substring(0, 3) + '</span>';
    });
    html += '</div></div>';

    // ---- On-time vs Late donut ----
    var onTime = 0, late = 0;
    doneList.forEach(function (p) {
      if (!p.target_date) { onTime++; return; }
      var diff = Math.round((dOnly(p.actual_date) - dOnly(p.target_date)) / 86400000);
      if (diff <= 0) onTime++; else late++;
    });
    var totalDone = onTime + late;
    var onTimePct = totalDone ? Math.round((onTime / totalDone) * 100) : 0;
    var otGrad = totalDone
      ? buildDonutGradient([{ pct: (onTime / totalDone) * 100, color: '#10b981' }, { pct: (late / totalDone) * 100, color: '#ef4444' }])
      : 'conic-gradient(var(--pj-sunken) 0 100%)';
    var otNote = !totalDone ? '<div class="cc-note"><i class="fa fa-info-circle"></i>No completed projects with a delivery date yet</div>'
      : late === 0 ? '<div class="cc-note gold"><i class="fa fa-trophy"></i>Every delivery landed on or before target</div>'
      : '<div class="cc-note"><i class="fa fa-clock-o"></i>' + late + ' delivered after target date</div>';
    html += '<div class="chart-card">' +
      '<div class="cc-head"><div><h5>On-time Delivery</h5><div class="cc-sub">Actual completion vs target date</div></div></div>' +
      '<div class="cc-body"><div class="donut-wrap">' +
        '<div class="donut" style="background:' + otGrad + ';"><span class="donut-hole"><span class="num">' + onTimePct + '%</span><span class="lbl">on time</span></span></div>' +
        '<div class="legend">' +
          '<div class="legend-item"><span class="sw" style="background:#10b981;"></span>On time<b class="ml-auto">' + onTime + '</b><span class="pc">' + onTimePct + '%</span></div>' +
          '<div class="legend-item"><span class="sw" style="background:#ef4444;"></span>Late<b class="ml-auto">' + late + '</b><span class="pc">' + (totalDone ? 100 - onTimePct : 0) + '%</span></div>' +
        '</div>' +
      '</div></div>' + otNote +
    '</div>';

    // ---- Workload by module ----
    var modGroups = {}, modOrder = [];
    list.forEach(function (p) {
      var key = p.category || 'General';
      if (!modGroups[key]) { modGroups[key] = 0; modOrder.push(key); }
      modGroups[key]++;
    });
    modOrder.sort(function (a, b) { return modGroups[b] - modGroups[a]; });
    var maxMod = Math.max.apply(null, modOrder.map(function (k) { return modGroups[k]; }).concat([1]));
    html += '<div class="chart-card"><div class="cc-head"><div><h5>Workload by Module</h5><div class="cc-sub">Share of projects in this selection</div></div></div><div class="cc-body">';
    if (modOrder.length === 0) html += '<div class="ins-empty">No data.</div>';
    modOrder.forEach(function (key) {
      var mc = moduleColor(key === 'General' ? null : key);
      var pct = Math.round((modGroups[key] / maxMod) * 100);
      var share = list.length ? Math.round((modGroups[key] / list.length) * 100) : 0;
      html += '<div class="bar-chart-row"><div class="bar-chart-label"><span class="sw" style="background:' + mc.c + ';"></span>' + escHtml(key) + '</div>' +
        '<div class="bar-chart-track"><div class="bar-chart-fill" style="width:' + pct + '%;background:' + mc.c + ';"></div></div>' +
        '<div class="bar-chart-value">' + modGroups[key] + '<small>' + share + '%</small></div></div>';
    });
    html += '</div></div>';

    // ---- Requests by Requester (who's asking for the most projects, all statuses) ----
    var picGroups = {}, picOrder = [];
    list.forEach(function (p) {
      var key = p.pic || 'No requester';
      if (!picGroups[key]) { picGroups[key] = 0; picOrder.push(key); }
      picGroups[key]++;
    });
    picOrder.sort(function (a, b) { return picGroups[b] - picGroups[a]; });
    var maxPic = Math.max.apply(null, picOrder.map(function (k) { return picGroups[k]; }).concat([1]));
    html += '<div class="chart-card"><div class="cc-head"><div><h5>Requests by Requester</h5><div class="cc-sub">Who the work is delivered for</div></div></div><div class="cc-body">';
    if (picOrder.length === 0) html += '<div class="ins-empty">No data.</div>';
    picOrder.forEach(function (key) {
      var pc = key === 'No requester' ? { c: '#94a3b8' } : picColor(key);
      var pct = Math.round((picGroups[key] / maxPic) * 100);
      var share = list.length ? Math.round((picGroups[key] / list.length) * 100) : 0;
      html += '<div class="req-row">' +
        '<span class="pic-avatar" style="background:' + pc.c + ';">' + (key === 'No requester' ? '?' : picInitials(key)) + '</span>' +
        '<div class="req-main"><div class="req-name">' + escHtml(key) + '<span>' + picGroups[key] + ' request' + (picGroups[key] !== 1 ? 's' : '') + ' &middot; ' + share + '%</span></div>' +
          '<div class="bar-chart-track"><div class="bar-chart-fill" style="width:' + pct + '%;background:' + pc.c + ';"></div></div></div>' +
      '</div>';
    });
    html += '</div></div>';

    // ---- Priority mix donut ----
    var prioCounts = { Low: 0, Medium: 0, High: 0 };
    list.forEach(function (p) { if (prioCounts[p.priority] !== undefined) prioCounts[p.priority]++; });
    var totalPrio = prioCounts.Low + prioCounts.Medium + prioCounts.High;
    var prioColors = { Low: '#94a3b8', Medium: '#f59e0b', High: '#ef4444' };
    var prioGrad = totalPrio
      ? buildDonutGradient([
          { pct: (prioCounts.Low / totalPrio) * 100, color: prioColors.Low },
          { pct: (prioCounts.Medium / totalPrio) * 100, color: prioColors.Medium },
          { pct: (prioCounts.High / totalPrio) * 100, color: prioColors.High }
        ])
      : 'conic-gradient(var(--pj-sunken) 0 100%)';
    function prioPct(n) { return totalPrio ? Math.round((n / totalPrio) * 100) : 0; }
    html += '<div class="chart-card"><div class="cc-head"><div><h5>Priority Mix</h5><div class="cc-sub">How urgent the requests are</div></div></div>' +
      '<div class="cc-body"><div class="donut-wrap">' +
        '<div class="donut" style="background:' + prioGrad + ';"><span class="donut-hole"><span class="num">' + totalPrio + '</span><span class="lbl">projects</span></span></div>' +
        '<div class="legend">' +
          ['Low', 'Medium', 'High'].map(function (k) {
            return '<div class="legend-item"><span class="sw" style="background:' + prioColors[k] + ';"></span>' + k + '<b class="ml-auto">' + prioCounts[k] + '</b><span class="pc">' + prioPct(prioCounts[k]) + '%</span></div>';
          }).join('') +
        '</div>' +
      '</div></div>' +
    '</div>';

    // ---- Upcoming deadlines (next 7 days, not yet Done) ----
    var horizon = addD(todayD, 7);
    var upcoming = list.filter(function (p) {
      if (isDoneStatus(p.status) || !p.target_date) return false;
      var t = dOnly(p.target_date);
      return t <= horizon;
    }).sort(function (a, b) { return a.target_date.localeCompare(b.target_date); });
    html += '<div class="chart-card"><div class="cc-head"><div><h5>Upcoming Deadlines</h5><div class="cc-sub">Due within the next 7 days</div></div></div>';
    if (upcoming.length === 0) {
      html += '<div class="ins-empty"><span class="ie-ico"><i class="fa fa-check"></i></span><b>All clear</b>Nothing due in the next 7 days.</div>';
    } else {
      html += '<div class="activity-list">';
      upcoming.forEach(function (p) {
        var mc = moduleColor(p.category);
        var t = dOnly(p.target_date);
        var diff = Math.round((t - todayD) / 86400000);
        var isOd = parseInt(p.is_overdue) === 1;   // hanya Planned & On Progress yang dihitung overdue
        var when = isOd ? '<span class="overdue-flag"><i class="fa fa-exclamation-triangle"></i> ' + Math.abs(diff) + 'd overdue</span>'
          : diff < 0 ? '<span class="text-muted">' + Math.abs(diff) + 'd past</span>'
          : diff === 0 ? '<span class="overdue-flag">Due today</span>'
          : diff + 'd left';
        html += '<div class="activity-item" onclick="openEdit(' + p.id + ')">' +
          '<span class="activity-dot" style="background:' + mc.c + ';"></span>' +
          '<div class="activity-body"><div class="activity-text"><b>' + escHtml(p.project_name) + '</b></div>' +
          '<div class="activity-time">' + fmtDate(p.target_date) + ' &middot; ' + picChipHtml(p.pic) + '</div></div>' +
          '<div class="activity-side">' + when + '</div>' +
        '</div>';
      });
      html += '</div>';
    }
    html += '</div>';

    // ---- Recent activity ----
    var recent = list.slice().sort(function (a, b) {
      var ad = a.updated_date || a.created_date || '';
      var bd = b.updated_date || b.created_date || '';
      return bd.localeCompare(ad);
    }).slice(0, 8);
    html += '<div class="chart-card span-8"><div class="cc-head"><div><h5>Recent Activity</h5><div class="cc-sub">Latest changes across the selection</div></div></div><div class="activity-list">';
    if (recent.length === 0) {
      html += '<div class="ins-empty">No activity yet.</div>';
    } else {
      recent.forEach(function (p) {
        var mc = moduleColor(p.category);
        var isNew = !p.updated_date;
        var ts = p.updated_date || p.created_date;
        html += '<div class="activity-item" onclick="openEdit(' + p.id + ')">' +
          '<span class="activity-dot" style="background:' + mc.c + ';"></span>' +
          '<div class="activity-body"><div class="activity-text">' + (isNew ? 'Created ' : 'Updated ') + '<b>' + escHtml(p.project_name) + '</b> &middot; ' + statusBadgeInline(p.status) + '</div>' +
          '<div class="activity-time">' + escHtml(p.category || 'General') + '</div></div>' +
          '<div class="activity-side">' + relativeTime(ts) + '</div>' +
        '</div>';
      });
    }
    html += '</div></div>';

    html += '</div>';
    $('#view-insights').html(html);
  }

  // ===== Live preview (modal) =====
  function updatePreview() {
    var cat = $('#pj-category').val().trim();
    var mc = moduleColor(cat);
    $('#pv-cat').text(cat || 'General').css({ color: mc.c, background: mc.s });
    $('.modal-preview .prev-card').css('--mod-color', mc.c);
    $('#pv-name').text($('#pj-name').val().trim() || 'Untitled project');
    $('#pv-desc').text($('#pj-description').val().trim() || 'No description yet.');

    var start = fmtDate($('#pj-start').val());
    var target = fmtDate($('#pj-target').val());
    $('#pv-dates').html('<i class="fa fa-calendar mr-1"></i>' + (start || '?') + ' &rarr; ' + (target || '?'));

    var status = $('#pj-status').val();
    var progress = parseInt($('#pj-progress').val()) || 0;
    $('#pv-pct').text(progress + '%');
    $('#pv-progress-bar').css({ width: progress + '%', background: progressColor(status) });
    $('#pv-status-badge').attr('class', 'badge ' + statusClass(status)).text(status);

    var priority = $('#pj-priority').val();
    $('#pv-priority-badge').attr('class', 'badge badge-priority-' + priority).text(priority);

    $('#pv-pic').html(picChipHtml($('#pj-pic').val().trim()));

    var descLen = $('#pj-description').val().length;
    $('#desc-counter').text(descLen + ' / 500');
  }

  function syncProgress(val) {
    val = Math.max(0, Math.min(100, parseInt(val) || 0));
    $('#pj-progress-range').val(val);
    $('#pj-progress').val(val);
    $('#progress-ring').css('--deg', (val * 3.6) + 'deg');
    $('#progress-ring-txt').text(val + '%');
    updatePreview();
  }

  function selectPill(groupId, hiddenId, value) {
    $('#' + groupId + ' .pill-opt').removeClass('active');
    $('#' + groupId + ' .pill-opt[data-value="' + value + '"]').addClass('active');
    $('#' + hiddenId).val(value);
  }
  $(document).on('click', '#pill-status .pill-opt', function () {
    selectPill('pill-status', 'pj-status', $(this).data('value'));
    onStatusChange(); updatePreview();
  });
  $(document).on('click', '#pill-priority .pill-opt', function () {
    selectPill('pill-priority', 'pj-priority', $(this).data('value'));
    updatePreview();
  });
  $(document).on('input change', '#pj-name, #pj-category, #pj-pic, #pj-description, #pj-start, #pj-target, #pj-actual, #pj-live', updatePreview);

  function onStatusChange() {
    var status = $('#pj-status').val();
    $('#pj-actual-row').toggle(isDoneStatus(status));
    $('#pj-live-row').toggle(status === 'Live');
    if (isDoneStatus(status)) syncProgress(100);
  }

  // ===== OPEN CREATE =====
  function openCreate() {
    $('#modal-proj-eyebrow').text('New Project');
    $('#pj-id').val('');
    $('#pj-name, #pj-category, #pj-pic, #pj-description, #pj-start, #pj-target, #pj-actual, #pj-live').val('');
    selectPill('pill-status', 'pj-status', 'Planned');
    selectPill('pill-priority', 'pj-priority', 'Medium');
    syncProgress(0);
    onStatusChange();
    updatePreview();
    $('#modalProject').modal('show');
  }

  // ===== OPEN EDIT =====
  function openEdit(id) {
    $.ajax({
      url: 'ajax_project.php', method: 'POST', data: { action: 'get_one', id: id }, dataType: 'json',
      success: function (r) {
        if (!r) { Swal.fire('Error', 'Data not found.', 'error'); return; }
        $('#modal-proj-eyebrow').text('Edit Project');
        $('#pj-id').val(r.id);
        $('#pj-name').val(r.project_name);
        $('#pj-category').val(r.category);
        $('#pj-pic').val(r.pic);
        $('#pj-description').val(r.description);
        selectPill('pill-status', 'pj-status', r.status || 'Planned');
        selectPill('pill-priority', 'pj-priority', r.priority || 'Medium');
        syncProgress(r.progress || 0);
        $('#pj-start').val(r.start_date);
        $('#pj-target').val(r.target_date);
        $('#pj-actual').val(r.actual_date);
        $('#pj-live').val(r.live_date);
        onStatusChange();
        updatePreview();
        $('#modalProject').modal('show');
      },
      error: function () { Swal.fire('Error', 'Failed to fetch data.', 'error'); }
    });
  }

  // ===== SAVE =====
  function saveProject() {
    var name = $('#pj-name').val().trim();
    if (!name) { Swal.fire('Notice', 'Project Name is required.', 'warning'); return; }

    $.ajax({
      url: 'save_project.php', method: 'POST', dataType: 'json',
      data: {
        id: $('#pj-id').val(),
        project_name: name,
        category: $('#pj-category').val().trim(),
        pic: $('#pj-pic').val().trim(),
        description: $('#pj-description').val().trim(),
        status: $('#pj-status').val(),
        priority: $('#pj-priority').val(),
        progress: $('#pj-progress').val(),
        start_date: $('#pj-start').val(),
        target_date: $('#pj-target').val(),
        actual_date: $('#pj-actual').val(),
        live_date: $('#pj-live').val()
      },
      success: function (r) {
        if (r.status === 'success') {
          $('#modalProject').modal('hide');
          Swal.fire({ icon: 'success', title: 'Success!', text: 'Project saved successfully.', timer: 1500, showConfirmButton: false });
          if (isDoneStatus($('#pj-status').val())) fireConfetti();
          loadProjects();
        } else {
          Swal.fire('Failed', r.message || 'An error occurred.', 'error');
        }
      },
      error: function () { Swal.fire('Error', 'Failed to contact the server.', 'error'); }
    });
  }

  // ===== Lightweight, dependency-free confetti burst (celebrates hitting Done) =====
  function fireConfetti() {
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var colors = ['#1e90ff', '#191970', '#dcae47', '#10b981', '#60a5fa', '#f59e0b'];
    for (var i = 0; i < 46; i++) {
      var el = document.createElement('div');
      el.className = 'confetti-piece';
      var duration = 1.6 + Math.random() * 1.1;
      var delay = Math.random() * 0.25;
      el.style.left = (Math.random() * 100) + 'vw';
      el.style.background = colors[i % colors.length];
      el.style.animationDuration = duration + 's';
      el.style.animationDelay = delay + 's';
      el.style.setProperty('--rot', (360 + Math.random() * 720) + 'deg');
      el.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
      document.body.appendChild(el);
      (function (node, ttl) { setTimeout(function () { node.remove(); }, ttl); })(el, (duration + delay) * 1000 + 400);
    }
  }

  // ===== DELETE =====
  function doDelete(id, name) {
    Swal.fire({
      title: 'Delete Project',
      html: 'Are you sure you want to delete <b>' + name + '</b>? This cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete it',
      cancelButtonText: 'Cancel'
    }).then(function (res) {
      if (!res.isConfirmed) return;
      $.ajax({
        url: 'delete_project.php', method: 'POST', data: { id: id }, dataType: 'json',
        success: function (r) {
          if (r.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1200, showConfirmButton: false });
            loadProjects();
          } else {
            Swal.fire('Failed', r.message || 'An error occurred.', 'error');
          }
        },
        error: function () { Swal.fire('Error', 'Failed to contact the server.', 'error'); }
      });
    });
  }

  // ===== Quick status change (inline, from the Table view) =====
  function quickStatusChange(id, newStatus) {
    $.ajax({
      url: 'quick_update_status.php', method: 'POST', dataType: 'json',
      data: { id: id, status: newStatus },
      success: function (r) {
        if (r.status === 'success') {
          Swal.fire({ icon: 'success', title: 'Status updated', timer: 1000, showConfirmButton: false });
          if (isDoneStatus(newStatus)) fireConfetti();
        } else {
          Swal.fire('Failed', r.message || 'An error occurred.', 'error');
        }
        loadProjects();
      },
      error: function () { Swal.fire('Error', 'Failed to contact the server.', 'error'); loadProjects(); }
    });
  }

  // ===== Board drag-and-drop (native HTML5 DnD, reuses quickStatusChange) =====
  var draggedProjectId = null;
  $(document).on('dragstart', '.proj-card[draggable="true"]', function (e) {
    draggedProjectId = $(this).data('id');
    $(this).addClass('dragging');
    if (e.originalEvent && e.originalEvent.dataTransfer) {
      e.originalEvent.dataTransfer.effectAllowed = 'move';
      e.originalEvent.dataTransfer.setData('text/plain', String(draggedProjectId));
    }
  });
  $(document).on('dragend', '.proj-card[draggable="true"]', function () {
    $(this).removeClass('dragging');
    $('.board-col-body').removeClass('drop-target');
  });
  $(document).on('dragover', '.board-col-body', function (e) {
    e.preventDefault();
    if (e.originalEvent && e.originalEvent.dataTransfer) e.originalEvent.dataTransfer.dropEffect = 'move';
    $(this).addClass('drop-target');
  });
  $(document).on('dragleave', '.board-col-body', function () {
    $(this).removeClass('drop-target');
  });
  $(document).on('drop', '.board-col-body', function (e) {
    e.preventDefault();
    $(this).removeClass('drop-target');
    var newStatus = $(this).data('status');
    var id = draggedProjectId;
    draggedProjectId = null;
    if (id && newStatus) quickStatusChange(id, newStatus);
  });

  // ===== Project detail drawer =====
  var drawerProjectId = null, drawerProjectName = '';

  function openDrawer(id) {
    var p = allProjects.filter(function (x) { return x.id == id; })[0];
    if (!p) return;
    drawerProjectId = p.id;
    drawerProjectName = p.project_name;

    var mc = moduleColor(p.category);
    var progress = parseInt(p.progress) || 0;
    var secondaryInfo = isDoneStatus(p.status) ? completionBadge(p.target_date, p.actual_date) : daysInfo(p.target_date, p.status);
    $('#drawer-head').html(
      '<span class="drawer-close" onclick="closeDrawer()"><i class="fa fa-times"></i></span>' +
      '<div class="drawer-eyebrow">' +
        '<span class="cat" style="--mod-color:' + mc.c + ';"><span class="cat-dot"></span>' + (p.category ? escHtml(p.category) : 'General') + '</span>' +
        atRiskBadgeHtml(p) +
      '</div>' +
      '<div class="drawer-title">' + escHtml(p.project_name) + '</div>' +
      '<div class="drawer-badges">' +
        '<span class="badge ' + statusClass(p.status) + '">' + p.status + '</span>' +
        '<span class="badge badge-priority-' + p.priority + '">' + p.priority + '</span>' +
        picChipHtml(p.pic) +
        (secondaryInfo ? '<span class="dh-when">' + secondaryInfo + '</span>' : '') +
      '</div>' +
      '<div class="drawer-progress"><span class="track"><span style="width:' + progress + '%;"></span></span><span class="val">' + progress + '%</span></div>' +
      '<div class="drawer-meta-grid">' +
        '<div class="drawer-meta-item"><span class="k">Start</span><span class="v">' + (fmtDate(p.start_date) || '&mdash;') + '</span></div>' +
        '<div class="drawer-meta-item"><span class="k">Target</span><span class="v">' + (fmtDate(p.target_date) || '&mdash;') + '</span></div>' +
        '<div class="drawer-meta-item"><span class="k">Actual</span><span class="v">' + (fmtDate(p.actual_date) || '&mdash;') + '</span></div>' +
        '<div class="drawer-meta-item"><span class="k">Progress</span><span class="v">' + progress + '%</span></div>' +
      '</div>'
    );
    $('#drawer-desc').html(p.description ? escHtml(p.description) : '<span class="text-muted">No description.</span>');

    loadDrawerMilestones();
    loadDrawerActivity();
    $('#drawer-backdrop, #detail-drawer').addClass('show');
  }

  function closeDrawer() {
    $('#drawer-backdrop, #detail-drawer').removeClass('show');
  }

  function closeDrawer() {
    $('#drawer-backdrop, #detail-drawer').removeClass('show');
  }

  function loadDrawerMilestones() {
    var pid = drawerProjectId;
    $.ajax({
      url: 'ajax_milestones.php', method: 'POST', data: { action: 'list', project_id: pid }, dataType: 'json',
      success: function (r) { if (pid === drawerProjectId) renderMilestones(r.data || []); }
    });
  }

  function renderMilestones(items) {
    if (items.length === 0) {
      $('#drawer-milestones').html('<div class="drawer-empty">No tasks yet — break this project into smaller steps below.</div>');
      $('#drawer-milestone-summary').text('');
      return;
    }
    var done = items.filter(function (m) { return parseInt(m.is_done) === 1; }).length;
    $('#drawer-milestone-summary').text(done + ' / ' + items.length + ' done');
    var html = items.map(function (m) {
      var isDone = parseInt(m.is_done) === 1;
      return '<div class="milestone-row">' +
        '<span class="milestone-check' + (isDone ? ' done' : '') + '" onclick="toggleMilestone(' + m.id + ')">' + (isDone ? '<i class="fa fa-check"></i>' : '') + '</span>' +
        '<span class="milestone-title' + (isDone ? ' done' : '') + '" onclick="toggleMilestone(' + m.id + ')">' + escHtml(m.title) + '</span>' +
        '<span class="milestone-del" onclick="deleteMilestone(' + m.id + ')" title="Remove"><i class="fa fa-trash-o"></i></span>' +
      '</div>';
    }).join('');
    $('#drawer-milestones').html(html);
  }

  function afterMilestoneChange(newProgress) {
    loadDrawerMilestones();
    loadDrawerActivity();
    if (newProgress !== null && newProgress !== undefined) {
      var p = allProjects.filter(function (x) { return x.id === drawerProjectId; })[0];
      if (p) {
        p.progress = newProgress;
        if (newProgress === 100 && !isDoneStatus(p.status)) { p.status = 'Done'; fireConfetti(); }
      }
    }
    loadProjects();
  }

  function addMilestone() {
    var title = $('#drawer-new-milestone').val().trim();
    if (!title) return;
    $.ajax({
      url: 'ajax_milestones.php', method: 'POST', dataType: 'json',
      data: { action: 'add', project_id: drawerProjectId, title: title },
      success: function (r) {
        $('#drawer-new-milestone').val('');
        afterMilestoneChange(r.progress);
      }
    });
  }

  function toggleMilestone(id) {
    $.ajax({
      url: 'ajax_milestones.php', method: 'POST', dataType: 'json',
      data: { action: 'toggle', project_id: drawerProjectId, id: id },
      success: function (r) { afterMilestoneChange(r.progress); }
    });
  }

  function deleteMilestone(id) {
    $.ajax({
      url: 'ajax_milestones.php', method: 'POST', dataType: 'json',
      data: { action: 'delete', project_id: drawerProjectId, id: id },
      success: function (r) { afterMilestoneChange(r.progress); }
    });
  }

  function loadDrawerActivity() {
    var pid = drawerProjectId;
    $.ajax({
      url: 'ajax_activity_log.php', method: 'POST', data: { project_id: pid }, dataType: 'json',
      success: function (r) { if (pid === drawerProjectId) renderDrawerActivity(r.data || []); }
    });
  }

  function activityText(a) {
    if (a.action === 'created') return 'Project <b>created</b>';
    if (a.action === 'milestone_added')   return 'Task added: <b>' + escHtml(a.note) + '</b>';
    if (a.action === 'milestone_done')    return 'Task completed: <b>' + escHtml(a.note) + '</b>';
    if (a.action === 'milestone_undone')  return 'Task reopened: <b>' + escHtml(a.note) + '</b>';
    if (a.action === 'milestone_deleted') return 'Task removed: <b>' + escHtml(a.note) + '</b>';
    if (a.action === 'updated' && a.field_name) {
      return '<b>' + escHtml(a.field_name) + '</b> changed from "' + escHtml(a.old_value || '&mdash;') + '" to "' + escHtml(a.new_value || '&mdash;') + '"';
    }
    return 'Updated';
  }

  function renderDrawerActivity(items) {
    if (items.length === 0) {
      $('#drawer-activity').html('<div class="drawer-empty">No activity recorded yet.</div>');
      return;
    }
    var html = items.map(function (a) {
      var dot = a.action.indexOf('milestone') === 0 ? '#2563eb' : (a.action === 'created' ? '#10b981' : '#94a3b8');
      return '<div class="activity-item">' +
        '<span class="activity-dot" style="background:' + dot + ';"></span>' +
        '<div class="activity-body"><div class="activity-text">' + activityText(a) + '</div>' +
        '<div class="activity-time">' + escHtml(a.changed_by) + ' &middot; ' + relativeTime(a.changed_date) + '</div></div>' +
      '</div>';
    }).join('');
    $('#drawer-activity').html(html);
  }

  $(function () { loadProjects(); });
</script>

</body>
</html>
