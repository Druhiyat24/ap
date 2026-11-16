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

/* ---------------------------------------------------------------------------
   DATA PROJECT SELALU DIBACA DARI PRODUKSI.
   conn/conn.php sering ditukar lokal <-> produksi, sedangkan daftar project
   ini daftar resmi yang dipakai bersama - sama seperti aplikasi Project
   Tracker yang config-nya memang dipaku ke produksi. $conn_live di conn.php
   SELALU menunjuk 10.10.5.12/signalbit_erp, jadi halaman ini dan seluruh
   endpoint-nya memakai itu.
   MENU & HAK AKSES TIDAK IKUT: header.php sudah selesai dijalankan di atas,
   jadi menunya tetap dibaca dari basis data yang sedang dipakai aplikasi.
   --------------------------------------------------------------------------- */
$conn1 = $conn_live;
$conn2 = $conn_live;

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
    --pj-canvas: #f6f8fb;
    --pj-surface: #ffffff;
    --pj-surface-2: #fbfcfe;
    --pj-sunken: #f1f4f9;
    --pj-line: #e9eef6;
    --pj-line-soft: #f2f5fa;
    --pj-ink: #0b1220;
    --pj-ink-2: #2b3648;
    --pj-muted: #6b7688;
    --pj-faint: #9aa4b5;
    --pj-navy: #141a4a;
    --pj-navy-2: #1e2a6b;
    --pj-blue: #4a8ff5;
    --pj-accent: #2b5bd7;
    --pj-accent-soft: #eef3ff;
    --pj-gold: #a07c12;
    --pj-gold-2: #c9a227;
    --pj-gold-soft: #faf4e4;
    --pj-good: #0f7a52;
    --pj-good-2: #10b981;
    --pj-good-soft: #e8f5ef;
    --pj-warn: #9a6212;
    --pj-warn-2: #f59e0b;
    --pj-warn-soft: #fdf3e3;
    --pj-bad: #b3261e;
    --pj-bad-2: #ef4444;
    --pj-bad-soft: #fdeceb;
    --pj-th: #141a4a;
    --pj-th-hover: #1e2a6b;
    --pj-shadow-sm: 0 1px 2px rgba(11,18,32,.04);
    --pj-shadow: 0 1px 3px rgba(11,18,32,.05), 0 10px 28px -18px rgba(11,18,32,.28);
    --pj-shadow-lg: 0 2px 8px rgba(11,18,32,.06), 0 36px 70px -32px rgba(16,26,74,.42);
    /* legacy names used by inline styles inside JS templates */
    --ink: var(--pj-ink); --ink-soft: var(--pj-muted); --line: var(--pj-line); --line-soft: var(--pj-line-soft);
    --surface: var(--pj-surface); --surface-2: var(--pj-surface-2); --accent: var(--pj-accent);
  }
  .proj-page[data-theme="dark"],
  .proj-page[data-theme="dark"] ~ .modal-project,
  .proj-page[data-theme="dark"] ~ .detail-drawer,
  .proj-page[data-theme="dark"] ~ .drawer-backdrop {
    --pj-canvas: #080c18;
    --pj-surface: #121a30;
    --pj-surface-2: #0e1527;
    --pj-sunken: #0b1122;
    --pj-line: #243052;
    --pj-line-soft: #1b2443;
    --pj-ink: #eaeff9;
    --pj-ink-2: #c6cfe3;
    --pj-muted: #93a0bd;
    --pj-faint: #6b789f;
    --pj-accent: #8fb4ff;
    --pj-accent-soft: rgba(96,145,255,.13);
    --pj-gold: #e9c46f;
    --pj-gold-2: #d4b25a;
    --pj-gold-soft: rgba(220,174,71,.14);
    --pj-good: #4ade9b;
    --pj-good-soft: rgba(52,211,153,.13);
    --pj-warn: #fbbf4d;
    --pj-warn-soft: rgba(251,191,36,.13);
    --pj-bad: #fb8a8a;
    --pj-bad-soft: rgba(248,113,113,.14);
    --pj-th: #141c3c;
    --pj-th-hover: #1c2750;
    --pj-shadow-sm: 0 1px 2px rgba(0,0,0,.4);
    --pj-shadow: 0 1px 3px rgba(0,0,0,.35), 0 12px 30px -16px rgba(0,0,0,.7);
    --pj-shadow-lg: 0 2px 8px rgba(0,0,0,.4), 0 36px 70px -30px rgba(0,0,0,.85);
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

  /* Kolom Description di Table: 3 baris + tombol "more" di tempat.
     Riwayatnya: mula-mula dipotong 2 baris dan sisanya HANYA lewat tooltip
     (isi sebenarnya tersembunyi), lalu ditampilkan penuh - tapi deskripsi
     sampai ±350 karakter membuat satu baris setinggi ±150px dan cuma 4
     project muat selayar. Sekarang 3 baris, dan yang terpotong bisa dibuka
     langsung di sel itu tanpa tooltip. Tooltip di <td> tetap dibiarkan. */
  .proj-table td.desc-cell { white-space: normal; max-width: 460px; min-width: 240px; vertical-align: top; }
  .proj-table .desc-clamp {
    font-size: 11.5px; line-height: 1.5; color: var(--pj-muted);
    white-space: pre-line;      /* baris baru yang diketik user ikut terlihat */
    overflow-wrap: anywhere;    /* kata/URL sangat panjang tidak melebarkan tabel */
    display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; line-clamp: 3; overflow: hidden;
  }
  .proj-table .desc-cell.expanded .desc-clamp { display: block; -webkit-line-clamp: unset; line-clamp: unset; overflow: visible; }
  .proj-table .desc-more {
    display: none; margin-top: 3px; padding: 0; border: 0; background: none; cursor: pointer;
    font-size: 11px; font-weight: 600; color: var(--pj-accent);
  }
  .proj-table .desc-more:hover, .proj-table .desc-more:focus-visible { text-decoration: underline; }
  .proj-table .desc-more:focus { outline: none; }
  .proj-table .desc-cell.is-clamped .desc-more { display: inline-block; }
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

  /* Module picker - pengganti datalist bawaan browser (lihat JS: renderModuleCombo). */
  .mod-combo { position: relative; }
  .mod-combo .form-control-modern { padding-left: 46px; padding-right: 40px; }
  .mod-combo-ico {
    position: absolute; left: 8px; top: 50%; z-index: 3; transform: translateY(-50%); pointer-events: none;
    width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 12px;
    color: var(--pj-faint); background: var(--pj-sunken); transition: background .18s ease, color .18s ease;
  }
  .mod-combo.has-module .mod-combo-ico { color: #fff; background: var(--mc); box-shadow: 0 4px 10px -4px var(--mc); }
  .mod-combo-caret {
    position: absolute; right: 6px; top: 50%; z-index: 3; transform: translateY(-50%);
    width: 30px; height: 30px; padding: 0; border: 0; border-radius: 8px; cursor: pointer; background-color: transparent;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%2394a3b8' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round' d='M1 1.5l5 5 5-5'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: center; background-size: 10px;
    transition: transform .18s ease, background-color .15s ease;
  }
  .mod-combo-caret:hover { background-color: var(--pj-sunken); }
  .mod-combo-caret:focus { outline: none; }
  .mod-combo.open .mod-combo-caret { transform: translateY(-50%) rotate(180deg); }
  .mod-combo.open .form-control-modern { border-color: #7aa2f7; box-shadow: 0 0 0 3px rgba(36,81,214,.14); }
  .mod-combo-panel {
    position: absolute; left: 0; right: 0; top: calc(100% + 6px); z-index: 40; padding: 6px; border-radius: 14px;
    background: var(--pj-surface); border: 1px solid var(--pj-line);
    box-shadow: 0 2px 6px rgba(11,18,32,.06), 0 22px 44px -18px rgba(16,26,74,.38);
    opacity: 0; visibility: hidden; transform: translateY(-4px) scale(.985); transform-origin: top center;
    transition: opacity .14s ease, transform .14s ease, visibility 0s linear .14s;
  }
  .mod-combo.open .mod-combo-panel { opacity: 1; visibility: visible; transform: none; transition: opacity .14s ease, transform .14s ease; }
  .mod-combo-list { max-height: 286px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--pj-line) transparent; }
  .mc-head { padding: 6px 10px 5px; font-size: 10px; font-weight: 600; letter-spacing: .13em; text-transform: uppercase; color: var(--pj-faint); }
  .mc-opt { display: flex; align-items: center; gap: 11px; padding: 7px 10px; border-radius: 10px; cursor: pointer; transition: background .12s ease; }
  .mc-opt + .mc-opt { margin-top: 2px; }
  .mc-opt.active { background: var(--pj-sunken); }
  .mc-opt.selected { background: var(--mcs); }
  .mc-ico { width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--mc); background: var(--mcs); }
  .mc-opt.selected .mc-ico { color: #fff; background: var(--mc); }
  .mc-name { flex: 1; min-width: 0; font-size: 13px; font-weight: 600; line-height: 1.25; color: var(--pj-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .mc-name small { display: block; margin-top: 1px; font-size: 11px; font-weight: 400; color: var(--pj-muted); }
  .mc-check { font-size: 13px; color: var(--mc); opacity: 0; flex-shrink: 0; }
  .mc-opt.selected .mc-check { opacity: 1; }
  .mc-new { margin-top: 6px !important; padding-top: 9px; border-top: 1px dashed var(--pj-line); border-radius: 0 0 10px 10px; }
  .mc-head + .mc-new { margin-top: 0 !important; padding-top: 7px; border-top: 0; border-radius: 10px; }
  .mc-new .mc-ico { color: var(--pj-accent); background: var(--pj-accent-soft); }
  .proj-page[data-theme="dark"] ~ .modal-project .mc-opt.selected { background: color-mix(in srgb, var(--mc) 16%, transparent); }
  .proj-page[data-theme="dark"] ~ .modal-project .mc-ico { color: #fff; background: color-mix(in srgb, var(--mc) 30%, transparent); }
  .proj-page[data-theme="dark"] ~ .modal-project .mc-opt.selected .mc-ico { background: var(--mc); }
  .proj-page[data-theme="dark"] ~ .modal-project .mc-new .mc-ico { color: var(--pj-accent); background: var(--pj-accent-soft); }
  .proj-page[data-theme="dark"] ~ .modal-project .mod-combo-panel { box-shadow: 0 2px 8px rgba(0,0,0,.4), 0 24px 48px -20px rgba(0,0,0,.85); }
  .proj-page[data-theme="dark"] ~ .modal-project .mc-check { color: #fff; }
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

  /* ==========================================================================
     PEMOLESAN TAMPILAN (ditambahkan 27 Okt 2026, permintaan pemakai:
     "lebih elegan, lebih terlihat mahal").

     SENGAJA ditaruh paling bawah sebagai LAPISAN TERPISAH, bukan diedit ke
     dalam CSS di atasnya. Dua alasannya: yang di atas itu ±900 baris yang
     sudah saling bergantung, dan dengan terpisah begini seluruh perubahan
     tampilan bisa dibatalkan cukup dgn menghapus satu blok ini.
     Semua token (--pj-*) sudah ditala ulang di bagian paling atas berkas.
     ========================================================================== */

  /* ---- TIPOGRAFI -------------------------------------------------------
     Tiga hal yang paling menaikkan kesan "mahal": judul ber-tracking rapat,
     label mikro huruf besar ber-tracking lebar, dan angka tabular supaya
     digitnya sejajar rapi antar baris. */
  .proj-page { -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility; }

  .proj-page .hero-title { letter-spacing: -.025em; line-height: 1.16; }
  .proj-page .hero-sub   { line-height: 1.62; }
  .proj-page .eyebrow,
  .proj-page .section-kicker,
  .proj-page .kpi-label,
  .proj-page .stat-tile .lbl { letter-spacing: .13em; text-transform: uppercase; font-weight: 600; }

  /* Angka: tabular + tracking negatif supaya blok angkanya terasa padat. */
  .proj-page .kpi-value,
  .proj-page .stat-tile .num,
  .proj-page .proj-table td.num,
  .proj-page .pct {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum" 1, "cv01" 1;
    letter-spacing: -.028em;
  }

  /* ---- PERMUKAAN -------------------------------------------------------
     Sudut sedikit lebih besar + garis rambut, bukan garis tegas. */
  .proj-page .perf-card,
  .proj-page .stat-tile,
  .proj-page .proj-table-wrap,
  .proj-page .mod-sum-card { border-radius: 14px; }

  .proj-page .stat-tile { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
  .proj-page .stat-tile:hover { transform: translateY(-1px); box-shadow: var(--pj-shadow); }

  /* ---- TABEL -----------------------------------------------------------
     Baris lebih lapang, judul kolom jadi label mikro, garis antar baris
     dilembutkan jadi --pj-line-soft. */
  .proj-page .proj-table thead th {
    font-size: 10.5px; letter-spacing: .12em; text-transform: uppercase; font-weight: 600;
    padding-top: 11px; padding-bottom: 11px;
  }
  .proj-page .proj-table tbody td { padding-top: 13px; padding-bottom: 13px; border-bottom-color: var(--pj-line-soft); }
  .proj-page .proj-table tbody tr { transition: background .14s ease; }

  /* ---- PILL FILTER -----------------------------------------------------
     Yang aktif memakai navy pekat: satu aksen saja, tidak warna-warni. */
  .proj-page .proj-filter-pill { transition: background .16s ease, border-color .16s ease, color .16s ease; }
  .proj-page .proj-filter-pill.active,
  .proj-page .proj-filter-pill[aria-pressed="true"] {
    background: var(--pj-navy); border-color: var(--pj-navy); color: #fff;
    box-shadow: 0 5px 14px -7px rgba(20,26,74,.8);
  }

  /* ---- TOMBOL UTAMA ----------------------------------------------------
     Di atas hero yang gelap, putih justru kontras tertinggi - mata langsung
     ke sana, dan kesannya lebih mahal daripada tombol biru menyala. */
  .proj-page .btn-hero-new {
    background: linear-gradient(180deg, #ffffff, #eef2fb);
    color: var(--pj-navy); border: 0;
    box-shadow: 0 6px 18px -8px rgba(0,0,0,.5);
    transition: transform .16s ease, box-shadow .16s ease;
  }
  .proj-page .btn-hero-new:hover { transform: translateY(-1px); box-shadow: 0 10px 22px -9px rgba(0,0,0,.55); }
  .proj-page[data-theme="dark"] .btn-hero-new {
    background: linear-gradient(180deg, #e9eef9, #cfd8ee); color: #101634;
  }

  /* ---- FOKUS -----------------------------------------------------------
     Cincin fokus yang konsisten - detail kecil, tapi kelihatan kalau tidak ada. */
  .proj-page :is(button, a, input, select, textarea):focus-visible {
    outline: none; box-shadow: 0 0 0 3px var(--pj-accent-soft); border-color: var(--pj-accent);
  }

  /* ---- GERAK -----------------------------------------------------------
     Hormati pemakai yang mematikan animasi di setelan sistemnya. */
  @media (prefers-reduced-motion: reduce) {
    .proj-page *, .modal-project *, .detail-drawer * { transition: none !important; animation: none !important; }
    .proj-page .stat-tile:hover, .proj-page .btn-hero-new:hover { transform: none; }
  }


  /* ==========================================================================
     TAMPILAN HP (29 Okt 2026) - halaman ini mulai dibuka dari ponsel.

     Lapisan terpisah seperti blok pemolesan di atas, dan SELURUHNYA di dalam
     media query: tampilan desktop tidak tersentuh sama sekali.
     ========================================================================== */

  /* Tiga perbaikan yang ketemu saat uji di HP, tapi berlaku juga di desktop:
     1) bootstrap.min.css bawaan (salinan ap_dev) kehilangan margin negatif
        .form-row & padding kiri kolomnya - Module, Status, tanggal dst. jadi
        menjorok 15px dari Project Name. Dikembalikan ke nilai Bootstrap 4.
     2) Bayangan drawer yang tersembunyi di luar layar bocor ke tepi kanan.
     3) Centang di pill status aktif memakai font FontAwesome 5 yang tidak
        dimuat (tampil sebagai kotak) - diganti ikon garis lewat mask. */
  .modal-project .form-row { margin-left: -5px; margin-right: -5px; }
  .modal-project .form-row > [class*="col-"] { padding-left: 5px; padding-right: 5px; }
  .detail-drawer:not(.show) { box-shadow: none; }
  .proj-filter-pill.active::before {
    content: ''; display: inline-block; flex-shrink: 0; width: 10px; height: 10px; margin-right: 6px; background-color: currentColor;
    -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6L9 17l-5-5'/%3E%3C/svg%3E") center / contain no-repeat;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6L9 17l-5-5'/%3E%3C/svg%3E") center / contain no-repeat;
  }
  /* Kolom nama di Timeline harus tetap selebar yang ditentukan; tanpa ini
     nama project yang panjang melebarkan barisnya sendiri dan grid jadi miring. */
  .tl-head-label, .tl-row-label, .tl-row-name { min-width: 0; }

  /* Layar sentuh tidak punya hover: tombol yang tadinya baru muncul saat
     kursor lewat harus kelihatan dari awal. */
  @media (hover: none) {
    .proj-card .card-actions { opacity: 1; }
    .milestone-row .milestone-del { opacity: .6; }
  }

  @media (max-width: 767.98px) {
    .proj-page { overflow-x: clip; }

    /* Hero: tombol New Project jadi tombol lebar di bawah sapaan. */
    .proj-hero { padding: 18px 16px 80px; }
    .proj-hero .hero-title { font-size: 22px; }
    .proj-hero .hero-sub { font-size: 12.5px; line-height: 1.55; }
    .hero-meta { margin-top: 12px; gap: 6px; }
    .hero-chip { font-size: 11px; padding: 4px 10px; }
    .hero-actions { width: 100%; padding-top: 0; }
    .hero-actions .btn-hero-new { flex: 1; justify-content: center; height: 44px; }
    .hero-actions .theme-toggle { width: 44px; height: 44px; flex-shrink: 0; }

    /* Delivery Performance & kartu statistik: 2 kolom, angka diperkecil,
       label mikro dirapatkan supaya tidak terpotong di kolom sempit. */
    .perf-head { padding: 12px 14px; }
    .perf-scope { flex-basis: 100%; padding-left: 0; border-left: 0; }
    .kpi { padding: 12px 14px 14px; }
    .kpi-value { font-size: 22px; }
    .kpi-visual { margin-top: 10px; }
    .proj-page .kpi-label, .proj-page .stat-tile .lbl { font-size: 10.5px; letter-spacing: .06em; }
    .stat-tile { padding: 13px 14px 12px; }
    .stat-tile .num { font-size: 22px; }
    .stat-tile .icon-badge { width: 26px; height: 26px; border-radius: 8px; font-size: 11px; }
    .ring { width: 46px; height: 46px; }
    .ring span { width: 36px; height: 36px; font-size: 10.5px; }

    /* Pintasan modul: digeser ke samping sampai ke tepi layar. */
    .section-bar { margin: 20px 0 10px; }
    .module-summary { flex: 1 1 100%; margin: 0 -12px; padding: 3px 12px; }

    /* Toolbar: tidak lagi menempel di atas (di HP tingginya memakan layar).
       Urutannya: tampilan > cari > ekspor > filter. */
    .proj-toolbar { position: static; padding: 10px; }
    .proj-toolbar .view-switch { width: 100%; overflow-x: auto; scrollbar-width: none; }
    .proj-toolbar .view-switch::-webkit-scrollbar,
    .proj-toolbar .status-seg::-webkit-scrollbar { display: none; }
    .proj-toolbar .view-switch button { flex: 1 0 auto; justify-content: center; height: 34px; padding: 0 8px; font-size: 12px !important; }
    .proj-page .view-switch button i { display: none; }
    .proj-toolbar .toolbar-actions { flex-wrap: wrap; }
    .proj-toolbar .toolbar-actions .search-wrap { flex: 1 1 100%; }
    .proj-toolbar .btn-export { flex: 1 1 0; justify-content: center; height: 40px; }
    .proj-toolbar .toolbar-filters::before { flex: 1 1 100%; }
    .proj-toolbar .status-seg { width: 100%; overflow-x: auto; scrollbar-width: none; }
    .proj-toolbar .proj-filter-pill { flex: 1 0 auto; justify-content: center; height: 32px; }
    /* Satu baris penuh per pilihan: berdampingan, teks 16px-nya terpotong
       ("October 2026 (t..."). */
    .proj-toolbar .select-wrap { flex: 1 1 100%; }
    .proj-toolbar select.form-control { width: 100% !important; height: 40px; }
    .clear-filters-link.show { flex: 1 1 100%; justify-content: center; height: 34px; }

    /* Isian teks minimal 16px: di bawah itu Safari iPhone otomatis
       memperbesar halaman tiap kali isian disentuh. */
    .search-wrap input, .proj-toolbar select.form-control,
    .modal-project .form-control-modern, .milestone-add-row input { font-size: 16px; }
    .search-wrap input { height: 40px; }

    /* Board: kolom selebar layar, berhenti pas di tiap kolom saat digeser. */
    .board-wrap { margin: 0 -12px; padding: 0 12px 8px; scroll-snap-type: x mandatory; scroll-padding: 0 12px; }
    .board-col { scroll-snap-align: start; }

    /* Kalender: 7 kolom di layar 360px terlalu sempit untuk nama project -
       grid diberi lebar minimum dan digeser ke samping. */
    .cal-toolbar { padding: 12px 14px; }
    .cal-toolbar .title { font-size: 15px; }
    .cal-chip { height: 24px; padding: 0 9px; font-size: 11px; }
    .cal-legend, .status-legend { padding: 8px 14px; gap: 6px 12px; }
    .cal-scroll { overflow-x: auto; }
    .cal-scroll > .cal-dow-row, .cal-scroll > .cal-week { min-width: 620px; }

    /* Timeline: kolom nama dipersempit supaya batang waktunya kebagian layar. */
    .tl-head-label, .tl-row-label { flex-basis: 140px; }

    /* Insights */
    .chart-card { padding: 16px; }
    .cc-stats { gap: 16px; }
    .donut-wrap { gap: 16px; }
    .donut { width: 112px; height: 112px; }
    .donut span.donut-hole { width: 86px; height: 86px; }

    /* Drawer detail: selebar layar. */
    .drawer-head { padding: 18px 16px 16px; }
    .drawer-close { top: 12px; right: 12px; }
    .drawer-title { font-size: 17px; }
    .drawer-body { padding: 16px; }
    .drawer-foot { padding: 12px 16px; }
    .drawer-foot > button { flex: 1; justify-content: center; }
    .milestone-add-row input, .milestone-add-row .icon-btn { height: 42px; }
    .milestone-add-row .icon-btn { width: 42px; }

    /* Modal create/edit: layar penuh. Panel pratinjau disembunyikan - di HP
       ia mendorong form jauh ke bawah; tombol Simpan menempel di dasar layar. */
    .modal-project .modal-dialog { margin: 0; max-width: none; width: 100%; height: 100%; min-height: 100%; }
    .modal-project .modal-dialog-centered::before { display: none; }
    .modal-project .modal-content { height: 100%; border-radius: 0; }
    .modal-project .modal-content > .row { flex: 1 1 auto; min-height: 0; flex-wrap: nowrap; }
    .modal-project .modal-preview { display: none; }
    .modal-project .modal-content > .row > .col-md-8 { display: flex; flex-direction: column; min-height: 0; }
    .modal-project .modal-body { flex: 1 1 auto; min-height: 0; max-height: none !important; padding: 18px 16px 24px !important; }
    .modal-form-head { margin-bottom: 16px; }
    .form-section { margin-bottom: 18px; padding-bottom: 16px; }
    .modal-project .pill-select { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .modal-project #pill-priority { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .modal-project .pill-opt { height: 40px; padding: 0 8px; }
    .modal-footer-modern { padding: 12px 16px; }
    .modal-footer-modern > button { flex: 1; justify-content: center; height: 44px; }
    .mod-combo-list { max-height: 46vh; }
    .mc-opt { padding: 9px 10px; }
  }


  /* ==========================================================================
     PENATAAN ULANG DASHBOARD (29 Okt 2026) - board naik ke atas, info yang
     sama di semua kartu dibuang.

     Sebelumnya ada 12 angka (Delivery Performance + Current Selection) dan
     board baru mulai ±830px dari atas - di laptop 1366x768 harus scroll dulu.
     Lapisan terpisah lagi; hapus blok ini untuk kembali ke tampilan lama
     (markup & JS-nya tetap berfungsi tanpa blok ini).
     ========================================================================== */

  /* ---- Delivery Performance bisa dilipat -------------------------------
     Terlipat: tinggal satu baris ringkasan; hero ikut memendek supaya
     kartunya tetap menumpang separuh di tepi bawah hero. */
  .proj-page .perf-head { justify-content: flex-start; }
  .perf-flags { margin-left: auto; }
  .perf-mini { display: none; align-items: center; flex-wrap: wrap; gap: 4px 0; font-size: 12px; color: var(--pj-muted); }
  .perf-mini span { white-space: nowrap; }
  .perf-mini span + span::before { content: '\00b7'; margin: 0 9px; color: var(--pj-faint); }
  .perf-mini b { color: var(--pj-ink); font-weight: 600; font-variant-numeric: tabular-nums; }
  .perf-toggle {
    display: inline-flex; align-items: center; gap: 6px; height: 28px; padding: 0 9px 0 11px; flex-shrink: 0; cursor: pointer;
    border-radius: 8px; border: 1px solid var(--pj-line); background: var(--pj-surface); color: var(--pj-ink-2);
    font-size: 11.5px; font-weight: 600; transition: border-color .15s ease, background .15s ease;
  }
  .perf-toggle:hover { border-color: #c5d2ea; background: var(--pj-surface-2); }
  .perf-toggle::after {
    content: ''; width: 12px; height: 12px; background-color: currentColor; transform: rotate(180deg); transition: transform .2s ease;
    -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") center / contain no-repeat;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") center / contain no-repeat;
  }
  .proj-page.perf-collapsed .perf-toggle::after { transform: none; }
  .proj-page.perf-collapsed .perf-grid,
  .proj-page.perf-collapsed .perf-scope,
  .proj-page.perf-collapsed .perf-flags { display: none; }
  .proj-page.perf-collapsed .perf-head { border-bottom: 0; }
  .proj-page.perf-collapsed .perf-mini { display: flex; margin-right: auto; padding-left: 11px; border-left: 1px solid var(--pj-line); }
  .proj-page.perf-collapsed .proj-hero { padding-bottom: 58px; }
  .proj-page.perf-collapsed .perf-card { margin-top: -30px; }

  /* ---- Current selection: satu kartu tipis, bukan enam kartu tinggi ----
     Markup & id-nya tetap (updateStats tidak berubah); tile-top & tile-foot
     dibuat display:contents supaya isinya bisa diatur ulang lewat grid:
         [ikon] 20  65% avg. progress
                TOTAL PROJECTS                                         */
  .proj-page .stat-grid {
    gap: 0; margin-bottom: 14px; border-radius: 14px; overflow: hidden;
    background: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: var(--pj-shadow-sm);
  }
  .proj-page .stat-grid .stat-tile {
    display: grid; grid-template-columns: auto auto minmax(0, 1fr); grid-template-rows: auto auto; column-gap: 10px; row-gap: 2px; align-items: center;
    height: auto; padding: 12px 16px; border: 0; border-radius: 0; background: transparent; box-shadow: none; color: inherit;
  }
  .proj-page .stat-grid .stat-tile:hover { transform: none; box-shadow: none; }
  .proj-page .stat-grid .stat-tile + .stat-tile::before { content: ''; position: absolute; left: 0; top: 14px; bottom: 14px; width: 1px; background: var(--pj-line-soft); }
  .proj-page .stat-grid .tile-top,
  .proj-page .stat-grid .tile-foot,
  .proj-page .stat-grid .tile-rate > div:not(.ring) { display: contents; }
  .proj-page .stat-grid .tile-meter,
  .proj-page .stat-grid .tile-rate .sheen { display: none; }
  .proj-page .stat-grid .icon-badge,
  .proj-page .stat-grid .tile-rate .ring { grid-column: 1; grid-row: 1 / span 2; width: 34px; height: 34px; border-radius: 10px; }
  .proj-page .stat-grid .stat-tile .num { grid-column: 2; grid-row: 1; margin: 0; font-size: 20px; line-height: 1.15; }
  .proj-page .stat-grid .tile-note { grid-column: 3; grid-row: 1; margin: 0; min-width: 0; overflow: hidden; text-overflow: ellipsis; font-size: 11px; color: var(--pj-muted); }
  .proj-page .stat-grid .stat-tile .lbl { grid-column: 2 / span 2; grid-row: 2; font-size: 10px; color: var(--pj-muted); }
  /* Completion: cincin kecil di tempat ikon, angkanya sudah ada di sebelahnya. */
  .proj-page .stat-grid .tile-rate .ring { border-radius: 50%; background: conic-gradient(var(--pj-accent) var(--deg), var(--pj-sunken) 0); }
  .proj-page .stat-grid .tile-rate .ring span { width: 24px; height: 24px; font-size: 0; background: var(--pj-surface); }
  .proj-page .stat-grid .tile-rate .num { color: var(--pj-navy-2); }
  .proj-page[data-theme="dark"] .stat-grid .tile-rate .num { color: #fff; }
  .proj-page .stat-grid #overdue-tile.overdue-alert { border: 0; box-shadow: none; background: var(--pj-bad-soft); }
  .proj-page .stat-grid #overdue-tile.overdue-alert .num { color: var(--pj-bad); }
  @media (max-width: 1199.98px) {
    .proj-page .stat-grid .stat-tile:nth-child(3n+1)::before { display: none; }
    .proj-page .stat-grid .stat-tile:nth-child(n+4) { border-top: 1px solid var(--pj-line-soft); }
  }
  @media (max-width: 767.98px) {
    /* Dua kolom selebar ±190px: ikon dilepas supaya catatan di sebelah
       angka tidak terpotong ("65% avg. progre..."). */
    .proj-page .stat-grid .stat-tile { grid-template-columns: auto minmax(0, 1fr); padding: 11px 14px; column-gap: 8px; border-top: 0; }
    .proj-page .stat-grid .stat-tile::before { display: none; }
    .proj-page .stat-grid .stat-tile:nth-child(even)::before { display: block; }
    .proj-page .stat-grid .stat-tile:nth-child(n+3) { border-top: 1px solid var(--pj-line-soft); }
    .proj-page .stat-grid .icon-badge,
    .proj-page .stat-grid .tile-rate .ring { display: none; }
    .proj-page .stat-grid .stat-tile .num { grid-column: 1; font-size: 18px; }
    .proj-page .stat-grid .tile-note { grid-column: 2; }
    .proj-page .stat-grid .stat-tile .lbl { grid-column: 1 / span 2; }

    /* Delivery Performance terlipat: tombol Show di pojok kanan atas,
       ringkasan satu baris penuh di bawah judul (tanpa titik pemisah,
       yang jatuh di awal baris saat teksnya pindah baris). */
    .proj-page .perf-head { position: relative; padding-right: 86px; }
    .perf-toggle { position: absolute; top: 11px; right: 14px; }
    .proj-page.perf-collapsed .perf-mini { flex-basis: 100%; margin-right: 0; padding-left: 0; border-left: 0; gap: 2px 14px; }
    .perf-mini span + span::before { content: none; }
    .proj-page.perf-collapsed .proj-hero { padding-bottom: 52px; }
    .proj-page.perf-collapsed .perf-card { margin-top: -28px; }
  }

  /* ---- Kartu board ------------------------------------------------------ */
  .proj-card.is-shipped .pc-progress,
  .proj-card.no-progress .pc-progress { display: none; }
  .proj-card.is-shipped .desc { -webkit-line-clamp: 1; line-clamp: 1; }
  .proj-card .card-actions { margin-left: auto; }   /* tetap di kanan walau chip requester tidak ada */
  .prio-plain { font-size: 11.5px; color: var(--pj-faint); }

  /* ---- Modul tanpa project: ajakan membuat yang pertama ---------------- */
  .empty-state.es-module .es-ico { color: #fff; background: var(--mc); box-shadow: 0 10px 22px -12px var(--mc); }
  .es-btn {
    display: inline-flex; align-items: center; gap: 8px; height: 38px; margin-top: 16px; padding: 0 16px 0 13px; cursor: pointer;
    border: 0; border-radius: 10px; background: var(--pj-navy); color: #fff; font-size: 13px; font-weight: 600;
    box-shadow: 0 6px 16px -8px rgba(20,26,74,.7); transition: transform .15s ease, box-shadow .15s ease;
  }
  .es-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 20px -9px rgba(20,26,74,.75); }
  .es-btn .fa { font-size: 11px; }

  /* ---- Petunjuk pintasan "/" di kotak cari ----------------------------- */
  .search-wrap input { padding-right: 32px; }
  .kbd-hint {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%); pointer-events: none;
    min-width: 20px; height: 20px; padding: 0 5px; border-radius: 5px; display: inline-flex; align-items: center; justify-content: center;
    font: 600 11px/1 ui-monospace, Consolas, monospace; color: var(--pj-faint);
    background: var(--pj-surface); border: 1px solid var(--pj-line); box-shadow: inset 0 -1px 0 var(--pj-line);
    transition: opacity .15s ease;
  }
  .search-wrap input:focus + .kbd-hint,
  .search-wrap input:not(:placeholder-shown) + .kbd-hint { opacity: 0; }
  @media (hover: none) { .kbd-hint { display: none; } }


  /* ==========================================================================
     MODE 3D (6 Nov 2026) - tombol Normal | 3D di hero.

     Semua aturan di bawah (kecuali tombolnya sendiri) hanya berlaku di bawah
     .mode-3d, jadi mode Normal tetap persis seperti versi yang di-backup
     (tag git normal-sebelum-3d). Isinya "Project Library": rak buku per
     modul, baris per bulan, buku per project (../js/library3d.js); buku
     yang diklik dibuka seperti buku sungguhan (.bk-*, di bawah).
     ========================================================================== */

  /* ---- Tombol Normal | 3D (tampil di kedua mode) ----------------------
     Yang menyala ditentukan CSS dari kelas .mode-3d, bukan JS - jadi tidak
     berkedip saat halaman dibuka dalam mode 3D. */
  .mode-switch {
    display: inline-flex; align-items: center; gap: 2px; padding: 3px; flex-shrink: 0;
    border-radius: 12px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18);
  }
  .mode-switch button {
    display: inline-flex; align-items: center; gap: 6px; height: 32px; padding: 0 12px; border: 0; border-radius: 9px; cursor: pointer; white-space: nowrap;
    background: transparent; color: rgba(255,255,255,.78); font-size: 12.5px !important; font-weight: 600; transition: background .15s ease, color .15s ease;
  }
  .mode-switch button:hover { color: #fff; background: rgba(255,255,255,.08); }
  .mode-switch button:focus { outline: none; }   /* reboot Bootstrap memberi bingkai hitam juga saat diklik mouse */
  .mode-switch button:focus-visible { box-shadow: 0 0 0 2px rgba(255,255,255,.75); }
  .mode-switch button .fa { font-size: 12px; }
  .proj-page:not(.mode-3d) .mode-switch [data-mode="normal"],
  .proj-page.mode-3d .mode-switch [data-mode="3d"] { background: #fff; color: var(--pj-navy); box-shadow: 0 4px 12px -6px rgba(0,0,0,.6); }
  .proj-page .fa-cube {
    -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 2.8l8 4.6v9.2l-8 4.6-8-4.6V7.4z'/%3E%3Cpath d='M4 7.4l8 4.6 8-4.6M12 12v9.2'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 2.8l8 4.6v9.2l-8 4.6-8-4.6V7.4z'/%3E%3Cpath d='M4 7.4l8 4.6 8-4.6M12 12v9.2'/%3E%3C/svg%3E");
  }

  /* ---- Panggung perpustakaan 3D di hero --------------------------------
     Desktop: kanvas mengisi seluruh hero, teks di kiri di atasnya, raknya
     digeser ke kanan oleh kamera. Layar < 992px: panggung di bawah teks. */
  .scene3d { display: none; }
  .proj-page.mode-3d .scene3d { display: block; position: absolute; inset: 0; z-index: 0; }
  .proj-page.mode-3d .proj-hero { min-height: 600px; }
  .proj-page.mode-3d .proj-hero .hero-top { position: relative; z-index: 2; pointer-events: none; }
  .proj-page.mode-3d .proj-hero .hero-actions,
  .proj-page.mode-3d .proj-hero .hero-chip { pointer-events: auto; }
  .proj-page.mode-3d .proj-hero .hero-brand { max-width: 44%; }
  .c3-canvas { position: absolute; inset: 0; display: block; width: 100% !important; height: 100% !important; touch-action: pan-y; cursor: grab; outline: none; }
  .c3-canvas.dragging { cursor: grabbing; }
  .scene3d.loading::after {
    content: 'Opening the library\2026'; position: absolute; left: 66%; top: 46%; transform: translate(-50%, -50%);
    font-size: 13px; font-weight: 500; color: rgba(255,255,255,.65); animation: c3Pulse 1.2s ease-in-out infinite;
  }
  @keyframes c3Pulse { 50% { opacity: .35; } }

  /* Label bulan per baris rak & nama modul (HTML biasa, digeser mengikuti
     titik 3D-nya). Nama modul hanya di layar sempit - di desktop papan nama
     kuningan di rak sudah terbaca. */
  .c3-labels { position: absolute; inset: 0; z-index: 1; overflow: hidden; pointer-events: none; transition: opacity .5s ease; }
  .c3-label { position: absolute; left: 0; top: 0; white-space: nowrap; text-shadow: 0 1px 8px rgba(4,8,30,.85); }
  .c3-in { display: flex; flex-direction: column; line-height: 1.2; }
  .c3-shelf .c3-in { transform: translate(8px, -50%); padding-left: 8px; border-left: 2px solid rgba(255,255,255,.28); }
  .c3-shelf b { font-size: 11px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: rgba(255,255,255,.85); }
  .c3-shelf small { font-size: 9.5px; color: rgba(255,255,255,.5); }
  .c3-shelf.cur .c3-in { border-left-color: #f0c65f; }
  .c3-shelf.cur b, .c3-shelf.cur small { color: #f0c65f; }
  .c3-shelf.hi .c3-in { border-left-color: #9cc6ff; }
  .c3-shelf.hi b, .c3-shelf.hi small { color: #9cc6ff; }
  .c3-labels.left .c3-shelf .c3-in { align-items: flex-end; transform: translate(calc(-100% - 8px), -50%); padding: 0 8px 0 0; border-left: 0; border-right: 2px solid rgba(255,255,255,.28); }
  .c3-labels.left .c3-shelf.cur .c3-in { border-right-color: #f0c65f; }
  .c3-labels.left .c3-shelf.hi .c3-in { border-right-color: #9cc6ff; }
  /* Layar sempit, semua rak tampak: nama rak saja (label bulan terlalu rapat
     di sana - muncul begitu satu rak didekati). */
  .c3-case { display: none; }
  .c3-labels.show-cases .c3-case { display: block; }
  .c3-labels.show-cases .c3-shelf { display: none; }
  .c3-case .c3-in { align-items: center; transform: translate(-50%, -100%); }
  .c3-case b { font-size: 10.5px; font-weight: 600; color: #fff; }
  .c3-case small { font-size: 9.5px; color: rgba(255,255,255,.55); }
  .c3-case.empty small { color: #f0c65f; }

  /* Keterangan lantai yang disorot */
  .c3-tip {
    position: absolute; left: 0; top: 0; z-index: 4; width: 236px; padding: 11px 13px 10px; border-radius: 12px; pointer-events: none;
    opacity: 0; transition: opacity .14s ease; color: #fff;
    background: rgba(8,13,42,.88); border: 1px solid rgba(255,255,255,.14); box-shadow: 0 18px 40px -14px rgba(0,0,0,.75);
    -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px);
  }
  .c3-tip.show { opacity: 1; }
  .c3-tip-mod { display: flex; align-items: center; gap: 6px; font-size: 10.5px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: rgba(255,255,255,.65); }
  .c3-tip-mod::before { content: ''; width: 8px; height: 8px; border-radius: 2px; background: var(--c); }
  .c3-tip-name { margin-top: 4px; font-size: 13px; font-weight: 600; line-height: 1.35; }
  .c3-tip-meta { display: flex; align-items: center; gap: 8px; margin-top: 7px; font-size: 11.5px; color: rgba(255,255,255,.72); }
  .c3-st { padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 600; }
  .c3-st.st-live { background: rgba(124,58,237,.32); color: #d9cbff; }
  .c3-st.st-done { background: rgba(16,185,129,.24); color: #93f0cb; }
  .c3-st.st-prog { background: rgba(59,130,246,.3); color: #bcd6ff; }
  .c3-st.st-plan { background: rgba(255,255,255,.12); color: rgba(255,255,255,.82); }
  .c3-st.st-over { background: rgba(239,68,68,.3); color: #ffb8b8; }
  .c3-tip-go { margin-top: 8px; padding-top: 7px; border-top: 1px solid rgba(255,255,255,.1); font-size: 10.5px; color: rgba(255,255,255,.5); }
  .c3-tip-sub { margin-top: 6px; font-size: 11px; line-height: 1.45; color: rgba(255,255,255,.62); }
  .c3-tip-dust { display: flex; align-items: center; gap: 7px; margin-top: 7px; font-size: 11px; font-weight: 600; color: #e9cf8f; }
  .c3-tip-dust::before { content: ''; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; background: #b9ad94; box-shadow: 0 0 0 3px rgba(185,173,148,.25); }

  /* Legenda (kiri bawah) & tombol putar/atur ulang (kanan bawah), di atas
     bagian hero yang tertutup kartu Delivery Performance. */
  .c3-legend {
    position: absolute; left: 30px; bottom: 88px; z-index: 2; max-width: 44%; pointer-events: none;
    display: flex; flex-wrap: wrap; align-items: center; gap: 6px 14px; font-size: 11.5px; color: rgba(255,255,255,.78);
  }
  .c3-legend-title { flex-basis: 100%; margin-bottom: 2px; font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.5); }
  .c3-key { display: inline-flex; align-items: center; gap: 7px; }
  /* Kunci legenda berbentuk punggung buku: warna = stiker status di buku */
  .c3-key::before {
    content: ''; width: 7px; height: 14px; border-radius: 2px; flex-shrink: 0;
    background: linear-gradient(#e4c47c 0 2px, #4a2c1e 2px calc(100% - 6px), var(--k) calc(100% - 6px));
  }
  .c3-key.k-live { --k: #8b5cf6; }
  .c3-key.k-done { --k: #10b981; }
  .c3-key.k-prog { --k: #3b82f6; }
  .c3-key.k-over { --k: #ef4444; }
  .c3-key.k-prog::before, .c3-key.k-over::before { box-shadow: 0 0 9px var(--k); }
  .c3-key.k-plan::before { background: rgba(156,198,255,.12); border: 1px dashed rgba(170,205,255,.85); }
  .c3-older { font-size: 10.5px; color: rgba(255,255,255,.5); }
  .c3-older:empty { display: none; }
  .c3-legend-sub { flex-basis: 100%; margin-top: 1px; font-size: 10.5px; color: rgba(255,255,255,.5); }
  .c3-case.warn small { color: #ffaaa5; }
  .c3-ctrl { position: absolute; right: 30px; bottom: 88px; z-index: 2; display: flex; align-items: center; gap: 8px; }
  .c3-hint { margin-right: 4px; font-size: 11px; color: rgba(255,255,255,.55); }
  .c3-btn {
    width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; cursor: pointer;
    color: #fff; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.2); transition: background .15s ease;
  }
  .c3-btn:hover { background: rgba(255,255,255,.18); }
  .c3-btn svg { width: 15px; height: 15px; }
  .c3-btn:focus, .c3-back:focus { outline: none; }
  .c3-btn:focus-visible, .c3-back:focus-visible { box-shadow: 0 0 0 2px rgba(255,255,255,.75); }
  /* "All shelves" muncul saat sedang mendekati satu rak */
  .c3-back {
    display: none; align-items: center; gap: 4px; height: 34px; padding: 0 12px 0 8px; border-radius: 10px; cursor: pointer;
    font-size: 12px; font-weight: 600; color: #1b1407; background: #e9c977; border: 0; box-shadow: 0 6px 16px -8px rgba(0,0,0,.7);
  }
  .c3-back svg { width: 15px; height: 15px; }
  .c3-ctrl.focused .c3-back { display: inline-flex; }
  .c3-ctrl.focused .c3-hint { display: none; }
  /* Mode hemat otomatis (HP/PC yang berat) & tombol suara yang menyala */
  .c3-eco { margin-right: 2px; padding: 3px 9px; border-radius: 999px; font-size: 10.5px; font-weight: 600; white-space: nowrap; color: #bfe9cf; background: rgba(16,185,129,.16); border: 1px solid rgba(16,185,129,.35); }
  .c3-eco[hidden] { display: none; }
  .c3-btn[aria-expanded="true"] { background: rgba(255,255,255,.22); }

  /* Lemari katalog: kotak cari buku di atas tombol-tombol */
  .c3-cat {
    position: absolute; right: 30px; bottom: 134px; z-index: 5; width: 340px; max-width: calc(100% - 24px); overflow: hidden; border-radius: 14px;
    color: #2c2216; background: #f5eedc; font-family: Georgia, "Times New Roman", serif;
    box-shadow: 0 24px 60px -20px rgba(0,0,0,.85), 0 0 0 1px rgba(181,138,60,.6);
  }
  .c3-cat[hidden] { display: none; }
  .c3-cat-head { display: flex; align-items: center; gap: 8px; padding: 8px 8px 8px 14px; border-bottom: 1px solid #d8c9a6; background: linear-gradient(#efe4c8, #e8dbbb); }
  .c3-cat-head > svg { width: 17px; height: 17px; flex-shrink: 0; color: #8a5a1c; }
  .c3-cat-in { flex: 1; min-width: 0; height: 34px; padding: 0; border: 0; outline: none; background: transparent; color: #2c2216; font: 15px Georgia, "Times New Roman", serif; }
  .c3-cat-in::placeholder { color: #9a8566; font-style: italic; }
  /* cincin fokus global (.proj-page ... :focus-visible) diganti garis emas di kepala panel */
  .proj-page .c3-cat .c3-cat-in:focus-visible { box-shadow: none; }
  .c3-cat-head:focus-within { box-shadow: inset 0 -2px 0 #b58a3c; }
  .c3-cat-x { width: 30px; height: 30px; padding: 0; border: 0; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; color: #8a5a1c; background: transparent; }
  .c3-cat-x:hover { background: rgba(138,90,28,.12); }
  .c3-cat-x:focus { outline: none; }
  .c3-cat .c3-cat-x:focus-visible { box-shadow: 0 0 0 2px rgba(138,90,28,.6); }
  .c3-cat-x svg { width: 15px; height: 15px; }
  .c3-cat-list { max-height: 300px; overflow-y: auto; padding: 4px; }
  .c3-cat-it { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 10px; border: 0; border-radius: 9px; text-align: left; cursor: pointer; color: inherit; background: transparent; font: inherit; }
  .c3-cat-it.on, .c3-cat-it:hover { background: rgba(138,90,28,.12); }
  .c3-cat-it:focus { outline: none; }
  .c3-cat .c3-cat-it:focus-visible { box-shadow: inset 0 0 0 2px rgba(138,90,28,.6); }
  .c3-cat-dot { width: 7px; height: 22px; flex-shrink: 0; border-radius: 2px; background: linear-gradient(#e4c47c 0 3px, #4a2c1e 3px calc(100% - 7px), var(--k) calc(100% - 7px)); }
  .c3-cat-t { display: flex; flex-direction: column; min-width: 0; }
  .c3-cat-t b { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-size: 13.5px; font-weight: 700; }
  .c3-cat-t small { font: 11px Arial, sans-serif; color: #8d7b60; }
  .c3-cat-cap { padding: 8px 10px 4px; font: 600 10px Arial, sans-serif; letter-spacing: .14em; text-transform: uppercase; color: #a08a66; }
  .c3-cat-empty { padding: 14px 12px; font-size: 13px; font-style: italic; color: #8d7b60; }
  .c3-cat-foot { padding: 7px 14px; border-top: 1px solid #e0d3b5; font: 10.5px Arial, sans-serif; color: #a08a66; }
  /* katalog: baris "sorot PIC" & chip nama orang */
  .c3-cat-ico { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; flex-shrink: 0; border-radius: 6px; color: #7a5208; background: #ffe08a; }
  .c3-cat-ico svg { width: 14px; height: 14px; }
  .c3-cat-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 2px 10px 10px; }
  .c3-cat-chip { padding: 5px 10px; border: 1px solid #d8c9a6; border-radius: 999px; cursor: pointer; color: #2c2216; background: #fbf6ea; font: 600 12px Arial, sans-serif; }
  .c3-cat-chip small { font-weight: 600; color: #a08a66; }
  .c3-cat-chip:hover, .c3-cat-chip.on { background: #ffe08a; border-color: #e3b84e; }

  /* Geser waktu (baris rak mundur/maju per bulan) & sorot satu PIC */
  .c3-ctrl2 { display: inline-flex; align-items: center; gap: 8px; }
  .c3-time { display: inline-flex; align-items: center; gap: 2px; height: 34px; padding: 0 3px; border-radius: 10px; color: #fff; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.2); }
  .c3-time[hidden], .c3-spot[hidden] { display: none; }
  .c3-time button, .c3-spot button { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; padding: 0; border: 0; border-radius: 7px; cursor: pointer; color: inherit; background: transparent; }
  .c3-time button:hover:not(:disabled) { background: rgba(255,255,255,.14); }
  .c3-time button:disabled { opacity: .3; cursor: default; }
  .c3-time button svg { width: 15px; height: 15px; }
  .c3-time-l { min-width: 8.6em; padding: 0 2px; text-align: center; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
  .c3-spot { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 3px 0 10px; border-radius: 10px; font-size: 12px; color: #1b1407; background: #ffe08a; box-shadow: 0 0 22px rgba(255,214,110,.5); }
  .c3-spot > svg { width: 15px; height: 15px; flex-shrink: 0; }
  .c3-spot b { max-width: 10em; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
  .c3-spot button { width: 26px; height: 26px; background: rgba(0,0,0,.08); }
  .c3-spot button:hover { background: rgba(0,0,0,.16); }
  .c3-spot button svg { width: 13px; height: 13px; }
  .c3-ctrl:has(.c3-time:not([hidden])) .c3-hint, .c3-ctrl:has(.c3-spot:not([hidden])) .c3-hint { display: none; }

  /* Menu "lainnya": suara, gerak kamera, simpan gambar, atur ulang */
  .c3-more { position: relative; display: inline-flex; }
  .c3-menu {
    position: absolute; right: 0; bottom: calc(100% + 8px); z-index: 6; display: flex; flex-direction: column; min-width: 214px; padding: 6px;
    border-radius: 12px; background: rgba(10,14,40,.96); border: 1px solid rgba(255,255,255,.14); box-shadow: 0 20px 40px -14px rgba(0,0,0,.8);
  }
  .c3-menu[hidden] { display: none; }
  .c3-menu button { display: flex; align-items: center; gap: 10px; width: 100%; height: 36px; padding: 0 10px; border: 0; border-radius: 8px; cursor: pointer; text-align: left; white-space: nowrap; color: #fff; background: transparent; font-size: 12.5px; }
  .c3-menu button:hover { background: rgba(255,255,255,.1); }
  /* reboot Bootstrap memberi button:focus cincin "auto" walau diklik mouse */
  .c3-menu button:focus, .c3-time button:focus, .c3-spot button:focus, .c3-tour-bar button:focus, .c3-cat-chip:focus { outline: none; }
  .proj-page .c3-menu button:focus-visible, .c3-menu button:focus-visible { outline: none; box-shadow: none; background: rgba(255,255,255,.14); }
  .c3-time button:focus-visible, .c3-spot button:focus-visible, .c3-tour-bar button:focus-visible, .c3-cat-chip:focus-visible { box-shadow: 0 0 0 2px rgba(255,255,255,.75); }
  .c3-cat-chip:focus-visible { box-shadow: 0 0 0 2px rgba(138,90,28,.6); }
  .c3-mi, .c3-chk { display: inline-flex; }
  .c3-mi svg { width: 16px; height: 16px; }
  .c3-chk { margin-left: auto; color: #f3d68c; opacity: 0; }
  .c3-chk svg { width: 15px; height: 15px; }
  .c3-menu [aria-checked="true"] .c3-chk { opacity: 1; }

  /* Mode presentasi: panggung dipindah JS ke <body>, menutupi layar */
  html.c3-presenting { overflow: hidden; }
  .scene3d.presenting {
    display: block !important; position: fixed !important; inset: 0 !important; z-index: 1050 !important; height: auto !important; margin: 0 !important;
    background:
      radial-gradient(900px 420px at 92% -12%, rgba(30,144,255,.45), rgba(30,144,255,0) 70%),
      radial-gradient(760px 380px at 40% 120%, rgba(79,70,229,.28), rgba(79,70,229,0) 70%),
      linear-gradient(112deg, #070b22 0%, #10185a 40%, #172e84 72%, #1b56c2 100%);
  }
  .scene3d.presenting .c3-legend, .scene3d.presenting .c3-ctrl, .scene3d.presenting .c3-cat { display: none; }
  .c3-tour {
    position: absolute; left: 50%; bottom: 28px; z-index: 5; width: min(620px, calc(100% - 32px)); padding: 16px 20px 0; overflow: hidden; transform: translateX(-50%);
    border-radius: 16px; color: #fff; background: rgba(8,12,38,.86); border: 1px solid rgba(255,255,255,.14); box-shadow: 0 24px 60px -20px rgba(0,0,0,.85);
    -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px);
  }
  .c3-tour[hidden] { display: none; }
  .c3-tour-k { font-size: 10.5px; font-weight: 600; letter-spacing: .16em; text-transform: uppercase; color: #e9c977; }
  .c3-tour-t { margin-top: 4px; font-family: Georgia, "Times New Roman", serif; font-size: 25px; font-weight: 700; line-height: 1.2; }
  .c3-tour-l { margin-top: 6px; font-size: 13.5px; line-height: 1.55; color: rgba(255,255,255,.8); }
  .c3-tour-bar { display: flex; align-items: center; gap: 8px; margin: 12px 0; }
  .c3-tour-bar button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-width: 34px; height: 34px; padding: 0 10px; border: 0; border-radius: 9px; cursor: pointer; color: #fff; background: rgba(255,255,255,.1); font-size: 12.5px; font-weight: 600; }
  .c3-tour-bar button:hover:not(:disabled) { background: rgba(255,255,255,.2); }
  .c3-tour-bar button:disabled { opacity: .35; cursor: default; }
  .c3-tour-bar svg { width: 15px; height: 15px; }
  .c3-tour-bar .c3-tour-x { margin-left: auto; color: #1b1407; background: #e9c977; }
  .c3-tour-bar .c3-tour-x:hover { background: #f3d68c; }
  .c3-tour-prog { height: 3px; margin: 0 -20px; background: rgba(255,255,255,.1); }
  .c3-tour-prog span { display: block; width: 0; height: 100%; background: #e9c977; }

  /* Kabar langsung: perubahan dari rekan (klik = cari bukunya) */
  .c3-news {
    position: absolute; left: 30px; bottom: 164px; z-index: 4; width: min(340px, calc(100% - 24px)); padding: 8px; border-radius: 14px; color: #fff;
    background: rgba(8,12,38,.9); border: 1px solid rgba(233,201,119,.45); box-shadow: 0 18px 40px -14px rgba(0,0,0,.8);
    animation: c3NewsIn .45s cubic-bezier(.2,.8,.3,1);
  }
  .c3-news[hidden] { display: none; }
  @keyframes c3NewsIn { from { opacity: 0; transform: translateY(10px); } }
  .c3-news-h { display: flex; align-items: center; gap: 7px; padding: 2px 6px 6px; font-size: 10.5px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: #e9c977; }
  .c3-news-it { display: flex; flex-direction: column; width: 100%; padding: 7px 8px; border: 0; border-radius: 9px; cursor: pointer; text-align: left; color: inherit; background: transparent; }
  .c3-news-it:hover { background: rgba(255,255,255,.08); }
  .c3-news-it:focus { outline: none; }
  .c3-news-it:focus-visible { box-shadow: inset 0 0 0 2px rgba(255,255,255,.6); }
  .c3-news-it b { font-size: 13px; }
  .c3-news-it small { font-size: 11.5px; color: rgba(255,255,255,.68); }

  /* Lucca: sistem perpustakaan (sapaan & laporan singkat), gaya hologram */
  .c3-lucca {
    --lu: #5ee7ff; --lu-a: rgba(94,231,255,.45);
    position: absolute; left: 30px; bottom: 88px; z-index: 4; width: min(372px, calc(100% - 24px)); padding: 10px 12px 12px; border-radius: 14px; color: #e6f4ff;
    background: repeating-linear-gradient(0deg, rgba(255,255,255,.025) 0 1px, transparent 1px 3px), linear-gradient(160deg, rgba(6,20,36,.95), rgba(10,12,38,.93));
    border: 1px solid var(--lu-a); box-shadow: 0 18px 40px -14px rgba(0,0,0,.8), 0 0 26px -8px var(--lu-a);
    animation: c3NewsIn .45s cubic-bezier(.2,.8,.3,1);
  }
  .c3-lucca.watch, .c3-news.watch { --lu: #ffc35a; --lu-a: rgba(255,195,90,.5); }
  .c3-lucca.alert, .c3-news.alert { --lu: #ff6b80; --lu-a: rgba(255,93,115,.5); }
  .c3-lucca[hidden] { display: none; }
  .proj-page.perf-collapsed .c3-lucca { bottom: 50px; }
  .scene3d.lucca-open .c3-legend { opacity: 0; }   /* panel Lucca menempati tempat legenda */
  .c3-lucca-h { display: flex; align-items: center; gap: 8px; margin: 0 0 6px; font-size: 10.5px; letter-spacing: .16em; text-transform: uppercase; }
  .c3-lucca-h b { font-weight: 700; color: var(--lu); }
  .c3-lucca-h small { font-size: 10.5px; letter-spacing: .12em; color: rgba(230,244,255,.55); }
  .c3-lucca-eye {
    flex: none; width: 14px; height: 14px; border-radius: 50%;
    background: radial-gradient(circle, #04101a 0 30%, var(--lu, #5ee7ff) 34% 60%, transparent 64%); box-shadow: 0 0 10px var(--lu, #5ee7ff);
    animation: c3LuPulse 2.4s ease-in-out infinite;
  }
  .c3-lucca-x { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin: -4px -4px -4px auto; padding: 0; border: 0; border-radius: 7px; cursor: pointer; color: rgba(230,244,255,.7); background: transparent; }
  .c3-lucca-x:hover { color: #fff; background: rgba(255,255,255,.08); }
  .c3-lucca-x svg { width: 15px; height: 15px; }
  .c3-lucca-t p { margin: 0 0 3px; font-size: 12.5px; line-height: 1.45; color: rgba(230,244,255,.86); animation: c3LuIn .45s both; animation-delay: calc(var(--i) * .32s); }
  .c3-lucca-t p:first-child { font-size: 14px; font-weight: 600; }
  .c3-lucca-t b { color: #fff; }
  .c3-lucca-t em { font-style: normal; color: var(--lu); }
  .c3-lucca-a { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 9px; }
  .c3-lucca-a:empty { display: none; }
  .c3-lucca-a button { height: 30px; padding: 0 12px; border: 1px solid rgba(255,255,255,.18); border-radius: 8px; cursor: pointer; font-size: 12px; font-weight: 600; color: #e6f4ff; background: rgba(255,255,255,.06); }
  .c3-lucca-a button:first-child { border-color: transparent; color: #04101a; background: var(--lu); }
  .c3-lucca-a button:hover { filter: brightness(1.12); }
  .c3-lucca-x:focus, .c3-lucca-a button:focus { outline: none; }
  .c3-lucca-x:focus-visible, .c3-lucca-a button:focus-visible { box-shadow: 0 0 0 2px rgba(255,255,255,.8); }
  @keyframes c3LuIn { from { opacity: 0; transform: translateY(4px); filter: blur(2px); } }
  @keyframes c3LuPulse { 50% { box-shadow: 0 0 3px var(--lu, #5ee7ff); } }
  @media (prefers-reduced-motion: reduce) { .c3-lucca, .c3-lucca-t p, .c3-lucca-eye { animation: none; } }

  /* Mode jalan-jalan (layar penuh, orang pertama) */
  .c3-walk {
    position: absolute; left: 50%; bottom: 22px; z-index: 5; display: flex; align-items: center; gap: 12px; padding: 8px 8px 8px 16px; transform: translateX(-50%);
    white-space: nowrap; border-radius: 14px; font-size: 13px; color: #fff; background: rgba(8,12,38,.84); border: 1px solid rgba(255,255,255,.14);
  }
  .c3-walk[hidden] { display: none; }
  .c3-walk-touch { display: none; }
  .c3-walk-x { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 12px; border: 0; border-radius: 9px; cursor: pointer; font-size: 12.5px; font-weight: 600; color: #1b1407; background: #e9c977; }
  .c3-walk-x:focus { outline: none; }
  .c3-walk-x:focus-visible { box-shadow: 0 0 0 2px rgba(255,255,255,.8); }
  .c3-walk-x svg { width: 15px; height: 15px; }
  .c3-joy {
    display: none; position: absolute; left: 24px; bottom: 96px; z-index: 5; width: 120px; height: 120px; border-radius: 50%; touch-action: none;
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.28);
  }
  .c3-joy span { position: absolute; left: 50%; top: 50%; width: 52px; height: 52px; margin: -26px 0 0 -26px; border-radius: 50%; background: rgba(255,255,255,.35); }
  .scene3d.walking .c3-labels, .scene3d.walking .c3-tour { display: none; }
  @media (pointer: coarse) {
    .scene3d.walking .c3-joy { display: block; }
    .c3-walk-keys { display: none; }
    .c3-walk-touch { display: inline; }
  }

  /* Fokus keyboard di panggung, teks pembaca layar, kilat "jepret" */
  .scene3d:focus { outline: none; }
  .scene3d:focus-visible { box-shadow: inset 0 0 0 2px rgba(255,255,255,.5); }
  .c3-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
  .scene3d.flash::after { content: ''; position: absolute; inset: 0; z-index: 6; pointer-events: none; background: #fff; animation: c3Flash .45s ease-out forwards; }
  @keyframes c3Flash { from { opacity: .75; } to { opacity: 0; } }
  /* Selama adegan pintu terbuka, label & tombol disembunyikan dulu */
  .scene3d.intro .c3-labels, .scene3d.intro .c3-legend, .scene3d.intro .c3-ctrl { opacity: 0; pointer-events: none; }
  .scene3d.intro .c3-cat { display: none; }
  .c3-legend, .c3-ctrl { transition: opacity .5s ease; }
  .proj-page.perf-collapsed .c3-legend,
  .proj-page.perf-collapsed .c3-ctrl { bottom: 50px; }

  /* ---- Kartu board ikut miring mengikuti kursor -----------------------
     Animasi masuk kartu memakai fill-mode "both", yang terus menimpa
     transform sesudah selesai - di mode 3D diganti "backwards" supaya
     kemiringannya bisa jalan. */
  .proj-page.mode-3d .proj-card-enter { animation-fill-mode: backwards; }
  .proj-page.mode-3d .proj-card {
    transform: perspective(900px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) translateY(var(--lift, 0px));
    transition: transform .2s ease-out, box-shadow .25s ease, border-color .2s ease;
  }
  .proj-page.mode-3d .proj-card:hover { --lift: -4px; box-shadow: 0 2px 4px rgba(15,23,42,.05), 0 24px 44px -22px rgba(15,23,42,.5); }
  .proj-page.mode-3d .proj-card::after {
    content: ''; position: absolute; inset: 0; border-radius: inherit; pointer-events: none; opacity: 0; transition: opacity .25s ease;
    background: radial-gradient(240px circle at var(--mx, 50%) var(--my, 0%), rgba(255,255,255,.65), rgba(255,255,255,0) 62%);
    mix-blend-mode: soft-light;
  }
  .proj-page.mode-3d .proj-card:hover::after { opacity: 1; }
  .proj-page.mode-3d .proj-card.dragging { transform: none; }
  @media (prefers-reduced-motion: reduce) {
    .proj-page.mode-3d .proj-card { transform: none; }
  }

  /* ---- Layar < 992px: panggung di bawah teks hero --------------------- */
  @media (max-width: 991.98px) {
    .proj-page.mode-3d .proj-hero { min-height: 0; }
    .proj-page.mode-3d .proj-hero .hero-brand { max-width: none; }
    .proj-page.mode-3d .scene3d { position: relative; inset: auto; height: 420px; margin: 16px -30px 0; }
    .c3-legend, .proj-page.perf-collapsed .c3-legend { left: 16px; right: 16px; bottom: 12px; max-width: none; gap: 5px 12px; }
    .c3-ctrl, .proj-page.perf-collapsed .c3-ctrl { top: 10px; right: 12px; bottom: auto; }
    .c3-hint { display: none; }
    .scene3d.loading::after { left: 50%; }
    .c3-cat { left: 12px; right: 12px; top: 52px; bottom: auto; width: auto; max-width: none; }
    .c3-ctrl2 { position: absolute; top: 44px; right: 0; }   /* baris kedua di bawah tombol */
    .c3-news, .c3-lucca, .proj-page.perf-collapsed .c3-lucca { left: 12px; right: 12px; top: 96px; bottom: auto; width: auto; }
    .c3-menu { top: calc(100% + 8px); bottom: auto; }
    .c3-tour { bottom: 16px; padding: 12px 14px 0; }
    .c3-tour-t { font-size: 20px; }
    .c3-tour-l { font-size: 12.5px; }
    .c3-tour-prog { margin: 0 -14px; }
    .c3-cat-list { max-height: 228px; }
  }
  @media (max-width: 767.98px) {
    /* Tombol Normal | 3D mendapat baris sendiri - kalau sebaris, tombol
       New Project terpecah dua baris. (Berlaku juga di mode Normal, karena
       tombolnya memang tampil di kedua mode.) */
    .hero-actions { flex-wrap: wrap; }
    .hero-actions .mode-switch { flex: 1 1 100%; }
    .hero-actions .mode-switch button { flex: 1; justify-content: center; height: 36px; }
    .proj-page.mode-3d .scene3d { height: 400px; margin: 12px -16px 0; }
    .c3-legend { gap: 4px 11px; font-size: 10.5px; }
    .c3-legend-title { font-size: 9.5px; letter-spacing: .1em; }
    .c3-key::before { width: 6px; height: 12px; }
    .c3-legend-sub { display: none; }
    .c3-eco { display: none; }   /* baris tombol di atas panggung tidak muat */
  }

  /* ---- Buku project: dibuka dari rak perpustakaan (mode 3D) -------------
     Dibangun & diisi JS (openBook). Ukuran halaman diatur JS sesuai layar;
     isi halaman memakai em supaya ikut mengecil di layar kecil. Warna
     sampul (--cv) = warna buku di rak. */
  .bk-reader { position: fixed; inset: 0; z-index: 1065; display: none; perspective: 2400px; color: #2c2216; }
  .bk-reader.show { display: block; }
  .bk-backdrop {
    position: absolute; inset: 0; opacity: 0; transition: opacity .5s ease;
    background: radial-gradient(ellipse 70% 60% at 50% 45%, rgba(46,30,14,.6), rgba(5,7,22,.9));
    -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);
  }
  .bk-reader.lit .bk-backdrop { opacity: 1; }
  .bk-book { position: absolute; transform-style: preserve-3d; font-family: Georgia, "Times New Roman", serif; line-height: 1.45; }
  .bk-boards {
    position: absolute; inset: -10px -12px; border-radius: 10px; background: var(--cv); opacity: 0; transition: opacity .45s ease .2s;
    box-shadow: 0 46px 90px -34px rgba(0,0,0,.9), inset 0 0 0 1px rgba(255,255,255,.08), inset 0 0 40px rgba(0,0,0,.35);
    transform: translateZ(-1px);
  }
  .bk-reader.open .bk-boards { opacity: 1; }
  .bk-reader.single .bk-boards { inset: -8px -9px -8px -6px; }
  .bk-page, .bk-face { position: absolute; top: 0; height: 100%; overflow: hidden; background-color: #f5eedc; }
  .bk-page { cursor: pointer; }
  .pg-r {
    border-radius: 0 5px 5px 0;
    background-image: linear-gradient(90deg, rgba(70,45,12,.24), rgba(70,45,12,.06) 5%, rgba(70,45,12,0) 11%), radial-gradient(ellipse at 75% 25%, rgba(255,255,255,.4), rgba(255,255,255,0) 60%);
    box-shadow: inset -1px 0 0 #e0d3b5, inset -3px 0 0 #f3ead6, inset -4px 0 0 #d6c7a4;
  }
  .pg-l {
    border-radius: 5px 0 0 5px;
    background-image: linear-gradient(270deg, rgba(70,45,12,.24), rgba(70,45,12,.06) 5%, rgba(70,45,12,0) 11%), radial-gradient(ellipse at 25% 25%, rgba(255,255,255,.4), rgba(255,255,255,0) 60%);
    box-shadow: inset 1px 0 0 #e0d3b5, inset 3px 0 0 #f3ead6, inset 4px 0 0 #d6c7a4;
  }
  .bk-reader:not(.open) .bk-page, .bk-left.hold { visibility: hidden; }
  .bk-reader.single .bk-left { display: none; }
  .bk-cover { position: absolute; top: 0; height: 100%; transform-origin: 0 50%; transform-style: preserve-3d; }
  .bk-cover-front, .bk-cover-back { position: absolute; inset: 0; -webkit-backface-visibility: hidden; backface-visibility: hidden; }
  .bk-cover-front {
    border-radius: 3px 9px 9px 3px; color: #e8ca84;
    background:
      linear-gradient(90deg, rgba(0,0,0,.5), rgba(0,0,0,.12) 5%, rgba(255,255,255,.07) 8%, rgba(0,0,0,0) 13%),
      radial-gradient(ellipse at 30% 18%, rgba(255,255,255,.16), rgba(255,255,255,0) 55%),
      var(--cv);
    box-shadow: 0 34px 70px -26px rgba(0,0,0,.95), inset 0 0 0 1px rgba(255,255,255,.07);
  }
  .bk-cover-back { transform: rotateY(180deg); }
  .bk-reader.blueprint, .bk-pp-cover.blueprint { --cv: #173174; }
  .bk-reader.blueprint .bk-cover-front, .bk-pp-cover.blueprint .bk-cover-front {
    color: #cfe1ff;
    background:
      linear-gradient(rgba(160,200,255,.12) 1px, transparent 1px) 0 0 / 22px 22px,
      linear-gradient(90deg, rgba(160,200,255,.12) 1px, transparent 1px) 0 0 / 22px 22px,
      linear-gradient(90deg, rgba(0,0,0,.45), rgba(0,0,0,0) 12%),
      var(--cv);
  }
  .bk-cv-frame {
    position: absolute; inset: 7%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 8% 9%;
    border: 2px solid currentColor; outline: 1px solid currentColor; outline-offset: 5px; opacity: .95;
  }
  .bk-cv-mod { font: 600 .72em/1 Arial, sans-serif; letter-spacing: .3em; text-transform: uppercase; opacity: .85; }
  .bk-cv-title { font-size: 1.9em; font-weight: 700; line-height: 1.2; text-shadow: 0 1px 0 rgba(0,0,0,.55); overflow-wrap: anywhere; }
  .bk-cv-foot {
    position: absolute; left: 0; right: 0; bottom: 6%; display: flex; flex-direction: column; align-items: center; gap: .8em;
    font: 600 .6em Arial, sans-serif; letter-spacing: .26em; text-transform: uppercase; opacity: .85;
  }
  .bk-cv-tag { padding: .45em 1em; border-radius: 3px; background: var(--sc); color: #fff; letter-spacing: .16em; box-shadow: 0 0 0 2px rgba(245,238,220,.9); }
  .bk-leaf { position: absolute; top: 0; height: 100%; transform-style: preserve-3d; }
  .bk-face { left: 0; width: 100%; -webkit-backface-visibility: hidden; backface-visibility: hidden; }
  .bk-face.bk-back { transform: rotateY(180deg); }

  /* isi halaman */
  .bk-pg { position: absolute; inset: 0; display: flex; flex-direction: column; padding: 2.1em 2.3em 1.4em; }
  .bk-run { font: 600 .6em Arial, sans-serif; letter-spacing: .22em; text-transform: uppercase; color: #a08a66; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .bk-ch { margin-top: 1.4em; font: 600 .64em Arial, sans-serif; letter-spacing: .28em; text-transform: uppercase; color: #a27838; }
  .bk-h { margin: .15em 0 .7em; font-family: inherit; font-size: 1.55em; font-weight: 700; color: #21180d; }
  .bk-body { flex: 1 1 auto; min-height: 0; overflow: auto; scrollbar-width: thin; scrollbar-color: #cdbd9a transparent; }
  .bk-text { font-size: .92em; line-height: 1.62; color: #3b2f20; }
  .bk-text p { margin: 0 0 .7em; }
  .bk-text p:first-child:not(.bk-muted)::first-letter { float: left; font-size: 3.1em; line-height: .9; padding: .06em .08em 0 0; color: #8a5a1c; font-weight: 700; }
  .bk-muted { margin: 0; color: #8d7b60; font-style: italic; font-size: .9em; }
  .bk-progress { display: flex; align-items: center; gap: .7em; margin: .7em 0 .5em; font-size: .82em; }
  .bk-progress-k { font: 600 .8em Arial, sans-serif; letter-spacing: .14em; text-transform: uppercase; color: #8d7b60; }
  .bk-progress-bar { flex: 1; height: 6px; border-radius: 3px; background: #e3d7bb; overflow: hidden; }
  .bk-progress-bar span { display: block; height: 100%; background: linear-gradient(90deg, #8a5a1c, #c9993f); }
  .bk-dates { width: 100%; border-collapse: collapse; font-size: .8em; }
  .bk-dates th, .bk-dates td { padding: .38em 0; border-bottom: 1px dotted #cdbd9a; }
  .bk-dates th { text-align: left; font: 600 .82em Arial, sans-serif; letter-spacing: .12em; text-transform: uppercase; color: #8d7b60; }
  .bk-dates td { text-align: right; color: #2c2216; }
  .bk-note { margin-top: .55em; font-size: .8em; font-style: italic; color: #7a5a2a; text-align: right; }
  .bk-num { margin-top: auto; padding-top: .8em; text-align: center; font-size: .72em; color: #a08a66; }
  .bk-orn { position: relative; width: 46%; height: 9px; margin: 1em auto; color: inherit; background: linear-gradient(90deg, transparent, currentColor, transparent) center / 100% 1px no-repeat; opacity: .75; }
  .bk-orn::after { content: ''; position: absolute; left: 50%; top: 50%; width: 7px; height: 7px; background: currentColor; transform: translate(-50%, -50%) rotate(45deg); }
  .bk-titlepg { align-items: center; justify-content: center; text-align: center; padding: 3em 2.6em 1.4em; color: #b08a4a; }
  .bk-titlepg .bk-num { position: absolute; left: 0; right: 0; bottom: 1.4em; }
  .bk-mod { display: inline-flex; align-items: center; gap: .55em; font: 600 .7em Arial, sans-serif; letter-spacing: .28em; text-transform: uppercase; color: #6e5a3c; }
  .bk-mod::before { content: ''; width: .8em; height: .8em; border-radius: 2px; background: var(--mc); }
  .bk-title { margin: 0; font-family: inherit; font-size: 2em; line-height: 1.18; font-weight: 700; color: #1f160b; overflow-wrap: anywhere; }
  .bk-stamp {
    margin-top: .3em; padding: .3em .95em; border: 2px solid var(--sc); border-radius: 4px; color: var(--sc);
    font: 700 .72em Arial, sans-serif; letter-spacing: .2em; text-transform: uppercase; transform: rotate(-4deg);
  }
  .bk-byline { margin-top: 1.5em; font-size: .85em; font-style: italic; color: #6e5a3c; }
  .bk-byline b { font-style: normal; color: #2c2216; }
  .bk-endpaper {
    align-items: center; justify-content: center; background-color: var(--mc);
    /* lapisan gelap paling atas = latar, bukan bayangan: ikut tercetak */
    background-image:
      linear-gradient(rgba(30,18,8,.58), rgba(30,18,8,.58)),
      radial-gradient(circle at 50% 50%, rgba(255,240,210,.16) 0 1.5px, transparent 2px),
      linear-gradient(45deg, rgba(0,0,0,.09) 25%, transparent 25%, transparent 75%, rgba(0,0,0,.09) 75%),
      linear-gradient(45deg, rgba(0,0,0,.09) 25%, transparent 25%, transparent 75%, rgba(0,0,0,.09) 75%);
    background-size: auto, 14px 14px, 14px 14px, 14px 14px; background-position: 0 0, 0 0, 0 0, 7px 7px;
    box-shadow: inset 0 0 60px rgba(0,0,0,.45);
  }
  .bk-plate {
    min-width: 64%; padding: 1.3em 1.6em; text-align: center; background: #f5eedc; color: #2c2216;
    border: 1px solid #b58a3c; outline: 3px double #b58a3c; outline-offset: 4px; box-shadow: 0 8px 22px -8px rgba(0,0,0,.7);
  }
  .bk-plate-k { font-size: 1.1em; font-style: italic; font-weight: 700; color: #8a5a1c; letter-spacing: .06em; }
  .bk-plate-t { margin-top: .2em; font-size: 1.15em; font-weight: 700; }
  .bk-plate-s { margin-top: .35em; font: 600 .6em Arial, sans-serif; letter-spacing: .24em; text-transform: uppercase; color: #8d7b60; }
  .bk-plate-line { margin-top: 1.1em; padding-top: .7em; border-top: 1px solid #d8c9a6; font-size: .7em; font-style: italic; color: #8d7b60; }
  .bk-plate-n { margin-top: .1em; font-family: "Segoe Script", "Brush Script MT", "Lucida Handwriting", cursive; font-size: 1.05em; color: #23324f; }
  .bk-count { margin-bottom: .6em; font: 600 .64em Arial, sans-serif; letter-spacing: .14em; text-transform: uppercase; color: #8d7b60; }
  .bk-tasks { list-style: none; margin: 0; padding: 0; font-size: .9em; }
  .bk-tasks li { display: flex; align-items: flex-start; gap: .6em; padding: .42em 0; border-bottom: 1px dotted #cdbd9a; line-height: 1.4; }
  .bk-box { position: relative; flex-shrink: 0; width: .95em; height: .95em; margin-top: .2em; border: 1.5px solid #8a5a1c; border-radius: 2px; }
  .bk-tasks li.done .bk-box::after { content: ''; position: absolute; left: .22em; top: -.08em; width: .3em; height: .62em; border: solid #1f7a4d; border-width: 0 2px 2px 0; transform: rotate(40deg); }
  .bk-tasks li.done span + span { color: #8d7b60; text-decoration: line-through; text-decoration-color: rgba(138,90,28,.5); }
  .bk-log { list-style: none; margin: 0; padding: 0 0 0 1em; border-left: 1px solid #cdbd9a; font-size: .84em; }
  .bk-log li { position: relative; padding: 0 0 .8em .2em; line-height: 1.4; }
  .bk-log li::before { content: ''; position: absolute; left: -1.36em; top: .42em; width: .55em; height: .55em; border-radius: 50%; background: #c9993f; box-shadow: 0 0 0 2px #f5eedc; }
  .bk-log-t { display: block; color: #2c2216; }
  .bk-log-m { display: block; font-size: .85em; font-style: italic; color: #8d7b60; }
  .bk-dusty { color: #8d7b60; }
  /* tugas bisa dicentang langsung di buku */
  .bk-count span { font: italic .95em Georgia, "Times New Roman", serif; letter-spacing: 0; text-transform: none; color: #a27838; }
  .bk-tasks li[data-mid] { cursor: pointer; border-radius: 3px; transition: background .15s ease, opacity .15s ease; }
  .bk-tasks li[data-mid]:hover { background: rgba(138,90,28,.07); }
  .bk-tasks li[data-mid]:focus { outline: none; }
  .bk-tasks li[data-mid]:focus-visible { box-shadow: 0 0 0 2px rgba(138,90,28,.55); }
  .bk-tasks li.busy { opacity: .55; cursor: progress; }

  /* Kartu peminjaman di saku sampul belakang: tanggal = cap ungu, nama =
     tulisan tangan. "Date due" = target project. */
  .bk-cardpg { align-items: center; justify-content: center; padding: 2em 1.6em; }
  .bk-slip { position: relative; width: 86%; max-width: 22em; }
  .bk-card {
    position: relative; z-index: 1; padding: .9em 1.1em 3.6em; color: #2c2216; background: #f6eedb;
    box-shadow: 0 6px 16px -6px rgba(0,0,0,.65); transform: rotate(-1.2deg);
  }
  .bk-card-top { display: flex; justify-content: space-between; align-items: center; gap: .6em; font: 700 .56em Arial, sans-serif; letter-spacing: .2em; text-transform: uppercase; color: #8a5a1c; }
  .bk-card-due { min-height: 1.7em; margin: .25em 0 .2em; text-align: right; }
  .bk-card-due .bk-muted { font-size: .75em; }
  .bk-card-f { display: flex; align-items: baseline; gap: .6em; padding: .28em 0; border-bottom: 1px solid rgba(110,140,190,.5); font-size: .8em; }
  .bk-card-f > span { flex: 0 0 5.6em; font: 600 .7em Arial, sans-serif; letter-spacing: .14em; text-transform: uppercase; color: #8d7b60; }
  .bk-card-f > b { min-width: 0; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
  .bk-card-t { width: 100%; margin-top: .5em; border-collapse: collapse; font-size: .8em; }
  .bk-card-t th { padding: .2em .45em; text-align: left; font: 600 .7em Arial, sans-serif; letter-spacing: .14em; text-transform: uppercase; color: #8d7b60; border-bottom: 1px solid rgba(110,140,190,.65); }
  .bk-card-t td { height: 1.9em; padding: 0 .45em; border-bottom: 1px solid rgba(110,140,190,.45); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 0; }
  .bk-card-t th:first-child, .bk-card-t td:first-child { width: 46%; border-right: 1px solid rgba(200,80,80,.5); }
  .bk-ink { display: inline-block; font: 700 .92em "Courier New", Courier, monospace; letter-spacing: .05em; color: #5b3fa0; opacity: .85; transform: rotate(var(--r, -2deg)); }
  .bk-hand { font-family: "Segoe Script", "Brush Script MT", "Lucida Handwriting", cursive; font-weight: 400; color: #23324f; }
  .bk-pocket {
    position: relative; z-index: 2; margin-top: -3.1em; padding: .85em 1em .8em; text-align: center; color: #5b4320;
    border-radius: 0 0 4px 4px; background: #d9c08c linear-gradient(rgba(255,255,255,.1), rgba(0,0,0,.06));
    box-shadow: 0 8px 18px -8px rgba(0,0,0,.75), inset 0 1px 0 rgba(255,255,255,.4);
  }
  .bk-pocket b { display: block; font: 700 .6em Arial, sans-serif; letter-spacing: .26em; text-transform: uppercase; }
  .bk-pocket span { display: block; margin-top: .3em; font-size: .7em; font-style: italic; }

  /* tombol di bawah buku */
  .bk-bar {
    position: absolute; left: 50%; transform: translateX(-50%); display: flex; align-items: center; gap: 8px; padding: 6px; white-space: nowrap;
    border-radius: 14px; background: rgba(14,12,24,.74); border: 1px solid rgba(255,255,255,.12); -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
    opacity: 0; pointer-events: none; transition: opacity .35s ease;
  }
  .bk-reader.open:not(.closing) .bk-bar { opacity: 1; pointer-events: auto; }
  .bk-nav, .bk-act { height: 36px; border: 0; border-radius: 10px; cursor: pointer; color: #fff; background: rgba(255,255,255,.08); transition: background .15s ease; }
  .bk-nav { width: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
  .bk-nav svg { width: 17px; height: 17px; }
  .bk-nav:disabled { opacity: .3; cursor: default; }
  .bk-act { padding: 0 14px; font-size: 12.5px; font-weight: 600; }
  .bk-nav:hover:not(:disabled), .bk-act:hover { background: rgba(255,255,255,.18); }
  .bk-act.bk-close { color: #1b1407; background: #e9c977; }
  .bk-act.bk-close:hover { background: #f3d68c; }
  .bk-bar button:focus { outline: none; }
  .bk-bar button:focus-visible { box-shadow: 0 0 0 2px rgba(255,255,255,.8); }
  .bk-dots { display: inline-flex; gap: 6px; padding: 0 2px; }
  .bk-dots span { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,.3); }
  .bk-dots span.on { background: #e9c977; }
  .bk-toast {
    position: absolute; left: 50%; bottom: calc(100% + 10px); padding: 6px 12px; border-radius: 9px; white-space: nowrap; pointer-events: none;
    font-size: 12px; font-weight: 600; color: #1b1407; background: #e9c977; box-shadow: 0 8px 20px -10px rgba(0,0,0,.7);
    opacity: 0; transform: translate(-50%, 6px); transition: opacity .2s ease, transform .2s ease;
  }
  .bk-toast.show { opacity: 1; transform: translate(-50%, 0); }
  /* Stempel status: pilihan di atas bilah, cap karet jatuh ke halaman */
  .bk-stamps {
    position: absolute; left: 50%; bottom: calc(100% + 10px); z-index: 2; display: flex; flex-wrap: wrap; justify-content: center; gap: 6px;
    width: max-content; max-width: calc(100vw - 24px); padding: 6px; border-radius: 12px; transform: translateX(-50%);
    background: rgba(14,12,24,.92); border: 1px solid rgba(255,255,255,.14); box-shadow: 0 18px 40px -16px rgba(0,0,0,.8);
  }
  .bk-stamps[hidden] { display: none; }
  .bk-stamps button { display: inline-flex; align-items: center; gap: 8px; height: 34px; padding: 0 12px; border: 0; border-radius: 9px; cursor: pointer; white-space: nowrap; color: #fff; background: rgba(255,255,255,.08); font-size: 12.5px; font-weight: 600; }
  .bk-stamps button::before { content: ''; width: 10px; height: 10px; border-radius: 2px; background: var(--sc); }
  .bk-stamps button:hover:not(:disabled) { background: rgba(255,255,255,.18); }
  .bk-stamps button:disabled { opacity: .4; cursor: default; }
  .bk-rubber {
    position: absolute; left: 50%; top: 44%; z-index: 4; padding: .2em .65em; pointer-events: none; white-space: nowrap;
    color: var(--sc); border: .16em solid var(--sc); border-radius: .22em; box-shadow: inset 0 0 0 .07em var(--sc);
    font: 800 2.3em/1.1 Arial, sans-serif; letter-spacing: .14em; text-transform: uppercase;
    transform: translate(-50%, -50%) rotate(-9deg); opacity: .88; mix-blend-mode: multiply;
  }
  .bk-l-short { display: none; }
  @media (max-width: 759.98px) {
    .bk-bar { gap: 6px; }
    .bk-act { padding: 0 12px; font-size: 12px; }
    .bk-l-long { display: none; }
    .bk-l-short { display: inline; }
  }
  @media (max-width: 419.98px) {
    .bk-dots, .bk-nav[data-bk="print"] { display: none; }   /* bilah tetap muat di HP */
  }

  /* Cetak / simpan PDF dari buku: hanya halaman-halaman buku, satu A5 per
     halaman (dibangun JS di #bk-print, dibuang lagi sesudah dicetak). */
  .bk-print { display: none; }
  @page bkpage { size: 148mm 210mm; margin: 0; }
  @media print {
    html.bk-printing, html.bk-printing body { overflow: visible !important; height: auto !important; margin: 0 !important; padding: 0 !important; background: #fff !important; }
    html.bk-printing body > *:not(.bk-print) { display: none !important; }
    html.bk-printing .bk-print {
      display: block; color: #2c2216; font-family: Georgia, "Times New Roman", serif; font-size: 11pt; line-height: 1.45;
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .bk-pp { page: bkpage; position: relative; width: 148mm; height: 209.5mm; overflow: hidden; background: #f5eedc; page-break-after: always; break-after: page; }
    .bk-pp:last-child { page-break-after: auto; break-after: auto; }
    .bk-pp .bk-body { overflow: visible; }
    .bk-pp .bk-count span { display: none; }
    .bk-pp-cover .bk-cover-front { position: absolute; inset: 0; border-radius: 0; box-shadow: none; font-size: 15pt; }
  }


  /* ==========================================================================
     IKON GARIS (27 Okt 2026) - pengganti FontAwesome 4.

     Markup ikonnya TIDAK diubah sama sekali (tag i berkelas fa). Diganti
     lewat CSS mask, jadi EMPAT tempat yang merakit nama ikon secara dinamis
     di JavaScript (peta status, empty-state papan, timeline, moduleIcon())
     ikut berubah dengan sendirinya - tidak ada yang perlu dikejar satu-satu.
     Gambarnya data URI, tidak ada unduhan dari internet. Mau kembali ke ikon
     lama? hapus blok ini saja.
     ========================================================================== */
  .proj-page .fa, .modal-project .fa, .detail-drawer .fa {
    display: inline-block; width: 1em; height: 1em; vertical-align: -.125em;
    background-color: currentColor;
    -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat;
    -webkit-mask-position: center;  mask-position: center;
    -webkit-mask-size: contain;     mask-size: contain;
    font-style: normal;
  }
  .proj-page .fa::before, .modal-project .fa::before, .detail-drawer .fa::before { content: none; }

  .proj-page .fa-folder-o, .modal-project .fa-folder-o, .detail-drawer .fa-folder-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/%3E%3C/svg%3E"); }
  .proj-page .fa-folder-open, .modal-project .fa-folder-open, .detail-drawer .fa-folder-open { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/%3E%3C/svg%3E"); }
  .proj-page .fa-folder-open-o, .modal-project .fa-folder-open-o, .detail-drawer .fa-folder-open-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z'/%3E%3C/svg%3E"); }
  .proj-page .fa-rocket, .modal-project .fa-rocket, .detail-drawer .fa-rocket { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 13c-1.5 1.5-2 5-2 5s3.5-.5 5-2'/%3E%3Cpath d='M19 3s-5 0-9 4l-3 3 5 5 3-3c4-4 4-9 4-9z'/%3E%3Ccircle cx='14.4' cy='8.6' r='1.3'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 13c-1.5 1.5-2 5-2 5s3.5-.5 5-2'/%3E%3Cpath d='M19 3s-5 0-9 4l-3 3 5 5 3-3c4-4 4-9 4-9z'/%3E%3Ccircle cx='14.4' cy='8.6' r='1.3'/%3E%3C/svg%3E"); }
  .proj-page .fa-line-chart, .modal-project .fa-line-chart, .detail-drawer .fa-line-chart { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 19V5M4 19h16'/%3E%3Cpath d='M8 16l4-5 3 3 5-7'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 19V5M4 19h16'/%3E%3Cpath d='M8 16l4-5 3 3 5-7'/%3E%3C/svg%3E"); }
  .proj-page .fa-pie-chart, .modal-project .fa-pie-chart, .detail-drawer .fa-pie-chart { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3a9 9 0 1 0 9 9h-9z'/%3E%3Cpath d='M14 3.5A9 9 0 0 1 20.5 10H14z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3a9 9 0 1 0 9 9h-9z'/%3E%3Cpath d='M14 3.5A9 9 0 0 1 20.5 10H14z'/%3E%3C/svg%3E"); }
  .proj-page .fa-moon-o, .modal-project .fa-moon-o, .detail-drawer .fa-moon-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 14.5A8 8 0 0 1 9.5 4a8.3 8.3 0 1 0 10.5 10.5z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 14.5A8 8 0 0 1 9.5 4a8.3 8.3 0 1 0 10.5 10.5z'/%3E%3C/svg%3E"); }
  .proj-page .fa-sun-o, .modal-project .fa-sun-o, .detail-drawer .fa-sun-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='4'/%3E%3Cpath d='M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='4'/%3E%3Cpath d='M12 2v2M12 20v2M2 12h2M20 12h2M5 5l1.5 1.5M17.5 17.5L19 19M19 5l-1.5 1.5M6.5 17.5L5 19'/%3E%3C/svg%3E"); }
  .proj-page .fa-plus, .modal-project .fa-plus, .detail-drawer .fa-plus { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 5v14M5 12h14'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 5v14M5 12h14'/%3E%3C/svg%3E"); }
  .proj-page .fa-search, .modal-project .fa-search, .detail-drawer .fa-search { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='M20 20l-3.2-3.2'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='M20 20l-3.2-3.2'/%3E%3C/svg%3E"); }
  .proj-page .fa-pencil, .modal-project .fa-pencil, .detail-drawer .fa-pencil { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 20h9'/%3E%3Cpath d='M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 20h9'/%3E%3Cpath d='M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z'/%3E%3C/svg%3E"); }
  .proj-page .fa-trash, .modal-project .fa-trash, .detail-drawer .fa-trash { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 6h18'/%3E%3Cpath d='M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2'/%3E%3Cpath d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/%3E%3Cpath d='M10 11v6M14 11v6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 6h18'/%3E%3Cpath d='M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2'/%3E%3Cpath d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/%3E%3Cpath d='M10 11v6M14 11v6'/%3E%3C/svg%3E"); }
  .proj-page .fa-trash-o, .modal-project .fa-trash-o, .detail-drawer .fa-trash-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 6h18'/%3E%3Cpath d='M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2'/%3E%3Cpath d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/%3E%3Cpath d='M10 11v6M14 11v6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3 6h18'/%3E%3Cpath d='M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2'/%3E%3Cpath d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/%3E%3Cpath d='M10 11v6M14 11v6'/%3E%3C/svg%3E"); }
  .proj-page .fa-check, .modal-project .fa-check, .detail-drawer .fa-check { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6L9 17l-5-5'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 6L9 17l-5-5'/%3E%3C/svg%3E"); }
  .proj-page .fa-check-circle, .modal-project .fa-check-circle, .detail-drawer .fa-check-circle { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M8.5 12.2l2.5 2.5 4.5-5'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M8.5 12.2l2.5 2.5 4.5-5'/%3E%3C/svg%3E"); }
  .proj-page .fa-check-square-o, .modal-project .fa-check-square-o, .detail-drawer .fa-check-square-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3.5' y='3.5' width='17' height='17' rx='3'/%3E%3Cpath d='M8 12.2l2.5 2.5 5-5.5'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3.5' y='3.5' width='17' height='17' rx='3'/%3E%3Cpath d='M8 12.2l2.5 2.5 5-5.5'/%3E%3C/svg%3E"); }
  .proj-page .fa-circle-o, .modal-project .fa-circle-o, .detail-drawer .fa-circle-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='8'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='8'/%3E%3C/svg%3E"); }
  .proj-page .fa-times, .modal-project .fa-times, .detail-drawer .fa-times { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 6l12 12M18 6L6 18'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 6l12 12M18 6L6 18'/%3E%3C/svg%3E"); }
  .proj-page .fa-times-circle, .modal-project .fa-times-circle, .detail-drawer .fa-times-circle { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M9 9l6 6M15 9l-6 6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M9 9l6 6M15 9l-6 6'/%3E%3C/svg%3E"); }
  .proj-page .fa-clock-o, .modal-project .fa-clock-o, .detail-drawer .fa-clock-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M12 7v5.2l3.4 2'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M12 7v5.2l3.4 2'/%3E%3C/svg%3E"); }
  .proj-page .fa-history, .modal-project .fa-history, .detail-drawer .fa-history { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3.5 12a8.5 8.5 0 1 0 2.6-6.1'/%3E%3Cpath d='M3 4v4h4'/%3E%3Cpath d='M12 8v4.2l3 1.8'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3.5 12a8.5 8.5 0 1 0 2.6-6.1'/%3E%3Cpath d='M3 4v4h4'/%3E%3Cpath d='M12 8v4.2l3 1.8'/%3E%3C/svg%3E"); }
  .proj-page .fa-calendar, .modal-project .fa-calendar, .detail-drawer .fa-calendar { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='5' width='18' height='16' rx='2.5'/%3E%3Cpath d='M3 10h18M8 3v4M16 3v4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='5' width='18' height='16' rx='2.5'/%3E%3Cpath d='M3 10h18M8 3v4M16 3v4'/%3E%3C/svg%3E"); }
  .proj-page .fa-calendar-o, .modal-project .fa-calendar-o, .detail-drawer .fa-calendar-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='5' width='18' height='16' rx='2.5'/%3E%3Cpath d='M3 10h18M8 3v4M16 3v4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='5' width='18' height='16' rx='2.5'/%3E%3Cpath d='M3 10h18M8 3v4M16 3v4'/%3E%3C/svg%3E"); }
  .proj-page .fa-calendar-check-o, .modal-project .fa-calendar-check-o, .detail-drawer .fa-calendar-check-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='5' width='18' height='16' rx='2.5'/%3E%3Cpath d='M3 10h18M8 3v4M16 3v4'/%3E%3Cpath d='M9 15l2 2 4-4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='5' width='18' height='16' rx='2.5'/%3E%3Cpath d='M3 10h18M8 3v4M16 3v4'/%3E%3Cpath d='M9 15l2 2 4-4'/%3E%3C/svg%3E"); }
  .proj-page .fa-flag, .modal-project .fa-flag, .detail-drawer .fa-flag { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 21V4'/%3E%3Cpath d='M5 5h11l-1.6 3.6L16 12H5z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 21V4'/%3E%3Cpath d='M5 5h11l-1.6 3.6L16 12H5z'/%3E%3C/svg%3E"); }
  .proj-page .fa-flag-o, .modal-project .fa-flag-o, .detail-drawer .fa-flag-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 21V4'/%3E%3Cpath d='M5 5h11l-1.6 3.6L16 12H5z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 21V4'/%3E%3Cpath d='M5 5h11l-1.6 3.6L16 12H5z'/%3E%3C/svg%3E"); }
  .proj-page .fa-flag-checkered, .modal-project .fa-flag-checkered, .detail-drawer .fa-flag-checkered { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 21V4'/%3E%3Cpath d='M5 5h11l-1.6 3.6L16 12H5z'/%3E%3Cpath d='M5 5h3.7v3.5H5zM12.3 5H16l-1.6 3.6h-2.1zM8.7 8.5h3.6V12H8.7z' stroke='none' fill='%23000'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 21V4'/%3E%3Cpath d='M5 5h11l-1.6 3.6L16 12H5z'/%3E%3Cpath d='M5 5h3.7v3.5H5zM12.3 5H16l-1.6 3.6h-2.1zM8.7 8.5h3.6V12H8.7z' stroke='none' fill='%23000'/%3E%3C/svg%3E"); }
  .proj-page .fa-trophy, .modal-project .fa-trophy, .detail-drawer .fa-trophy { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M8 4h8v5.2a4 4 0 0 1-8 0z'/%3E%3Cpath d='M8 5.5H5V7a3 3 0 0 0 3 3M16 5.5h3V7a3 3 0 0 1-3 3'/%3E%3Cpath d='M10 17.5h4M9 21h6M12 13.2v4.3'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M8 4h8v5.2a4 4 0 0 1-8 0z'/%3E%3Cpath d='M8 5.5H5V7a3 3 0 0 0 3 3M16 5.5h3V7a3 3 0 0 1-3 3'/%3E%3Cpath d='M10 17.5h4M9 21h6M12 13.2v4.3'/%3E%3C/svg%3E"); }
  .proj-page .fa-star, .modal-project .fa-star, .detail-drawer .fa-star { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3.6l2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8L3.5 9.8l5.9-.9z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3.6l2.6 5.3 5.9.9-4.3 4.1 1 5.8-5.2-2.7-5.2 2.7 1-5.8L3.5 9.8l5.9-.9z'/%3E%3C/svg%3E"); }
  .proj-page .fa-cubes, .modal-project .fa-cubes, .detail-drawer .fa-cubes { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3l9 5-9 5-9-5z'/%3E%3Cpath d='M3 13l9 5 9-5'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3l9 5-9 5-9-5z'/%3E%3Cpath d='M3 13l9 5 9-5'/%3E%3C/svg%3E"); }
  .proj-page .fa-th-large, .modal-project .fa-th-large, .detail-drawer .fa-th-large { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3.5' y='3.5' width='7' height='7' rx='1.6'/%3E%3Crect x='13.5' y='3.5' width='7' height='7' rx='1.6'/%3E%3Crect x='3.5' y='13.5' width='7' height='7' rx='1.6'/%3E%3Crect x='13.5' y='13.5' width='7' height='7' rx='1.6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3.5' y='3.5' width='7' height='7' rx='1.6'/%3E%3Crect x='13.5' y='3.5' width='7' height='7' rx='1.6'/%3E%3Crect x='3.5' y='13.5' width='7' height='7' rx='1.6'/%3E%3Crect x='13.5' y='13.5' width='7' height='7' rx='1.6'/%3E%3C/svg%3E"); }
  .proj-page .fa-table, .modal-project .fa-table, .detail-drawer .fa-table { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4.5' width='18' height='15' rx='2.5'/%3E%3Cpath d='M3 9.5h18M9.5 9.5v10'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4.5' width='18' height='15' rx='2.5'/%3E%3Cpath d='M3 9.5h18M9.5 9.5v10'/%3E%3C/svg%3E"); }
  .proj-page .fa-columns, .modal-project .fa-columns, .detail-drawer .fa-columns { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4.5' width='18' height='15' rx='2.5'/%3E%3Cpath d='M12 4.5v15'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4.5' width='18' height='15' rx='2.5'/%3E%3Cpath d='M12 4.5v15'/%3E%3C/svg%3E"); }
  .proj-page .fa-tasks, .modal-project .fa-tasks, .detail-drawer .fa-tasks { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 6.5l1.6 1.6L9 4.7M4 16.5l1.6 1.6L9 14.7'/%3E%3Cpath d='M12 7h8M12 17h8'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 6.5l1.6 1.6L9 4.7M4 16.5l1.6 1.6L9 14.7'/%3E%3Cpath d='M12 7h8M12 17h8'/%3E%3C/svg%3E"); }
  .proj-page .fa-exclamation-triangle, .modal-project .fa-exclamation-triangle, .detail-drawer .fa-exclamation-triangle { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 4.2l8.6 15.3H3.4z'/%3E%3Cpath d='M12 10v4M12 17.3v.4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 4.2l8.6 15.3H3.4z'/%3E%3Cpath d='M12 10v4M12 17.3v.4'/%3E%3C/svg%3E"); }
  .proj-page .fa-warning, .modal-project .fa-warning, .detail-drawer .fa-warning { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 4.2l8.6 15.3H3.4z'/%3E%3Cpath d='M12 10v4M12 17.3v.4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 4.2l8.6 15.3H3.4z'/%3E%3Cpath d='M12 10v4M12 17.3v.4'/%3E%3C/svg%3E"); }
  .proj-page .fa-info, .modal-project .fa-info, .detail-drawer .fa-info { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M12 11v5M12 8.2v.4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M12 11v5M12 8.2v.4'/%3E%3C/svg%3E"); }
  .proj-page .fa-info-circle, .modal-project .fa-info-circle, .detail-drawer .fa-info-circle { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M12 11v5M12 8.2v.4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M12 11v5M12 8.2v.4'/%3E%3C/svg%3E"); }
  .proj-page .fa-question-circle-o, .modal-project .fa-question-circle-o, .detail-drawer .fa-question-circle-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M9.8 9.6a2.3 2.3 0 1 1 3 2.2v1.4M12 16.6v.4'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M9.8 9.6a2.3 2.3 0 1 1 3 2.2v1.4M12 16.6v.4'/%3E%3C/svg%3E"); }
  .proj-page .fa-bolt, .modal-project .fa-bolt, .detail-drawer .fa-bolt { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M13 3L5 14h6l-1 7 8-11h-6z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M13 3L5 14h6l-1 7 8-11h-6z'/%3E%3C/svg%3E"); }
  .proj-page .fa-yarn, .modal-project .fa-yarn, .detail-drawer .fa-yarn { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M3.5 8.3c4.5.2 8.3 4.2 8.9 10.6'/%3E%3Cpath d='M6.4 4.4c4.6 1.5 9 5.6 10.7 11.7'/%3E%3Cpath d='M11.7 3c3.4 1.6 6.4 4.6 7.3 8'/%3E%3Cpath d='M16.7 16.7c1.4 1.5 2.4 3.3 4.8 3.3'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M3.5 8.3c4.5.2 8.3 4.2 8.9 10.6'/%3E%3Cpath d='M6.4 4.4c4.6 1.5 9 5.6 10.7 11.7'/%3E%3Cpath d='M11.7 3c3.4 1.6 6.4 4.6 7.3 8'/%3E%3Cpath d='M16.7 16.7c1.4 1.5 2.4 3.3 4.8 3.3'/%3E%3C/svg%3E"); }
  .proj-page .fa-magic, .modal-project .fa-magic, .detail-drawer .fa-magic { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 20L15 9'/%3E%3Cpath d='M14 4l.9 2.1 2.1.9-2.1.9L14 10l-.9-2.1-2.1-.9 2.1-.9z'/%3E%3Cpath d='M19 12l.6 1.4 1.4.6-1.4.6-.6 1.4-.6-1.4-1.4-.6 1.4-.6z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 20L15 9'/%3E%3Cpath d='M14 4l.9 2.1 2.1.9-2.1.9L14 10l-.9-2.1-2.1-.9 2.1-.9z'/%3E%3Cpath d='M19 12l.6 1.4 1.4.6-1.4.6-.6 1.4-.6-1.4-1.4-.6 1.4-.6z'/%3E%3C/svg%3E"); }
  .proj-page .fa-user-o, .modal-project .fa-user-o, .detail-drawer .fa-user-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='8' r='4'/%3E%3Cpath d='M4 21c0-4 3.6-6 8-6s8 2 8 6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='8' r='4'/%3E%3Cpath d='M4 21c0-4 3.6-6 8-6s8 2 8 6'/%3E%3C/svg%3E"); }
  .proj-page .fa-briefcase, .modal-project .fa-briefcase, .detail-drawer .fa-briefcase { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='7.5' width='18' height='12.5' rx='2.5'/%3E%3Cpath d='M8.5 7.5V5.8A1.8 1.8 0 0 1 10.3 4h3.4a1.8 1.8 0 0 1 1.8 1.8v1.7M3 12.5h18'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='7.5' width='18' height='12.5' rx='2.5'/%3E%3Cpath d='M8.5 7.5V5.8A1.8 1.8 0 0 1 10.3 4h3.4a1.8 1.8 0 0 1 1.8 1.8v1.7M3 12.5h18'/%3E%3C/svg%3E"); }
  .proj-page .fa-university, .modal-project .fa-university, .detail-drawer .fa-university { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3.5 9.5L12 4l8.5 5.5z'/%3E%3Cpath d='M5.5 10v7M9.5 10v7M14.5 10v7M18.5 10v7M3 20h18'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3.5 9.5L12 4l8.5 5.5z'/%3E%3Cpath d='M5.5 10v7M9.5 10v7M14.5 10v7M18.5 10v7M3 20h18'/%3E%3C/svg%3E"); }
  .proj-page .fa-coffee, .modal-project .fa-coffee, .detail-drawer .fa-coffee { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 8h12v6a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z'/%3E%3Cpath d='M16 9.5h1.8a2.5 2.5 0 0 1 0 5H16'/%3E%3Cpath d='M6 3.5v1.8M10 3.5v1.8M14 3.5v1.8'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 8h12v6a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4z'/%3E%3Cpath d='M16 9.5h1.8a2.5 2.5 0 0 1 0 5H16'/%3E%3Cpath d='M6 3.5v1.8M10 3.5v1.8M14 3.5v1.8'/%3E%3C/svg%3E"); }
  .proj-page .fa-filter, .modal-project .fa-filter, .detail-drawer .fa-filter { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3.5 5.5h17l-6.5 7.6V20l-4-2v-4.9z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M3.5 5.5h17l-6.5 7.6V20l-4-2v-4.9z'/%3E%3C/svg%3E"); }
  .proj-page .fa-file-excel-o, .modal-project .fa-file-excel-o, .detail-drawer .fa-file-excel-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z'/%3E%3Cpath d='M14 3v5h5'/%3E%3Cpath d='M9.5 12l5 5M14.5 12l-5 5'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z'/%3E%3Cpath d='M14 3v5h5'/%3E%3Cpath d='M9.5 12l5 5M14.5 12l-5 5'/%3E%3C/svg%3E"); }
  .proj-page .fa-file-invoice-dollar, .modal-project .fa-file-invoice-dollar, .detail-drawer .fa-file-invoice-dollar { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 3h9l4 4v12.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 5 19.5v-15A1.5 1.5 0 0 1 6.5 3z'/%3E%3Cpath d='M15 3v4h4'/%3E%3Cpath d='M12 10.5v6M10.3 11.8h3M10.3 15.2h3'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 3h9l4 4v12.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 5 19.5v-15A1.5 1.5 0 0 1 6.5 3z'/%3E%3Cpath d='M15 3v4h4'/%3E%3Cpath d='M12 10.5v6M10.3 11.8h3M10.3 15.2h3'/%3E%3C/svg%3E"); }
  .proj-page .fa-save, .modal-project .fa-save, .detail-drawer .fa-save { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 3h11l3 3v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z'/%3E%3Cpath d='M8 3v5h6V3M8 21v-6h8v6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 3h11l3 3v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z'/%3E%3Cpath d='M8 3v5h6V3M8 21v-6h8v6'/%3E%3C/svg%3E"); }
  .proj-page .fa-spinner, .modal-project .fa-spinner, .detail-drawer .fa-spinner { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3v3.5M12 17.5V21M21 12h-3.5M6.5 12H3M18.4 5.6l-2.5 2.5M8.1 15.9l-2.5 2.5M18.4 18.4l-2.5-2.5M8.1 8.1L5.6 5.6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3v3.5M12 17.5V21M21 12h-3.5M6.5 12H3M18.4 5.6l-2.5 2.5M8.1 15.9l-2.5 2.5M18.4 18.4l-2.5-2.5M8.1 8.1L5.6 5.6'/%3E%3C/svg%3E"); }
  .proj-page .fa-arrow-up, .modal-project .fa-arrow-up, .detail-drawer .fa-arrow-up { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 19V5M6 11l6-6 6 6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 19V5M6 11l6-6 6 6'/%3E%3C/svg%3E"); }
  .proj-page .fa-arrow-down, .modal-project .fa-arrow-down, .detail-drawer .fa-arrow-down { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 5v14M6 13l6 6 6-6'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 5v14M6 13l6 6 6-6'/%3E%3C/svg%3E"); }
  .proj-page .fa-chevron-left, .modal-project .fa-chevron-left, .detail-drawer .fa-chevron-left { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 5l-7 7 7 7'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 5l-7 7 7 7'/%3E%3C/svg%3E"); }
  .proj-page .fa-chevron-right, .modal-project .fa-chevron-right, .detail-drawer .fa-chevron-right { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 5l7 7-7 7'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 5l7 7-7 7'/%3E%3C/svg%3E"); }
  .proj-page .fa-angle-double-left, .modal-project .fa-angle-double-left, .detail-drawer .fa-angle-double-left { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12.5 5l-7 7 7 7M19 5l-7 7 7 7'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12.5 5l-7 7 7 7M19 5l-7 7 7 7'/%3E%3C/svg%3E"); }
  .proj-page .fa-angle-double-right, .modal-project .fa-angle-double-right, .detail-drawer .fa-angle-double-right { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M11.5 5l7 7-7 7M5 5l7 7-7 7'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M11.5 5l7 7-7 7M5 5l7 7-7 7'/%3E%3C/svg%3E"); }
  .proj-page .fa-play-circle, .modal-project .fa-play-circle, .detail-drawer .fa-play-circle { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M10.2 8.9l5 3.1-5 3.1z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Cpath d='M10.2 8.9l5 3.1-5 3.1z'/%3E%3C/svg%3E"); }
  .proj-page .fa-lightbulb-o, .modal-project .fa-lightbulb-o, .detail-drawer .fa-lightbulb-o { -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9.2 17h5.6'/%3E%3Cpath d='M10 20.5h4'/%3E%3Cpath d='M12 3.5a5.5 5.5 0 0 1 3.2 10c-.5.4-.8 1-.8 1.6H9.6c0-.6-.3-1.2-.8-1.6A5.5 5.5 0 0 1 12 3.5z'/%3E%3C/svg%3E"); mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9.2 17h5.6'/%3E%3Cpath d='M10 20.5h4'/%3E%3Cpath d='M12 3.5a5.5 5.5 0 0 1 3.2 10c-.5.4-.8 1-.8 1.6H9.6c0-.6-.3-1.2-.8-1.6A5.5 5.5 0 0 1 12 3.5z'/%3E%3C/svg%3E"); }

  /* fa-spin tetap berputar walau ikonnya kini SVG */
  .proj-page .fa-spin { animation: pj-spin 1.1s linear infinite; }
  @keyframes pj-spin { to { transform: rotate(360deg); } }


</style>

<div class="proj-page" id="proj-page-root">
<script>
  // Applied synchronously, before the rest of this div paints, so there's no
  // flash of the wrong theme when a returning visitor has dark mode saved.
  (function () {
    if (localStorage.getItem('projTheme') === 'dark') {
      document.getElementById('proj-page-root').setAttribute('data-theme', 'dark');
    }
    // Delivery Performance terlipat atau tidak - sama alasannya: dipasang
    // sebelum tampil supaya tidak melompat. Belum pernah dipilih: di HP
    // terlipat (layar sempit), di desktop terbuka.
    var perf = null;
    try { perf = localStorage.getItem('projPerfCollapsed'); } catch (e) {}
    if (perf === '1' || (perf === null && window.innerWidth < 768)) {
      document.getElementById('proj-page-root').classList.add('perf-collapsed');
    }
    // Mode 3D juga dipasang sebelum tampil, supaya hero langsung setinggi
    // panggung 3D (perpustakaannya sendiri baru dimuat setelah halaman siap).
    var mode = null;
    try { mode = localStorage.getItem('projMode'); } catch (e) {}
    if (mode === '3d') document.getElementById('proj-page-root').classList.add('mode-3d');
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
        <div class="mode-switch" role="group" aria-label="View mode">
          <button type="button" data-mode="normal" onclick="setViewMode('normal')" aria-pressed="true"><i class="fa fa-th-large"></i>Normal</button>
          <button type="button" data-mode="3d" onclick="setViewMode('3d')" aria-pressed="false"><i class="fa fa-cube"></i>3D</button>
        </div>
        <button type="button" class="theme-toggle" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle dark mode">
          <i class="fa fa-moon-o" id="theme-toggle-icon"></i>
        </button>
        <button type="button" class="btn-hero-new" onclick="openCreate()" title="New project (N)">
          <i class="fa fa-plus"></i> New Project
        </button>
      </div>
    </div>
    <!-- Panggung mode 3D. Kosong & tersembunyi di mode Normal; isinya dipasang
         ../js/library3d.js begitu tombol 3D dipilih. -->
    <div class="scene3d" id="scene3d"></div>
  </header>

  <!-- Delivery performance (all-time, independent of filters) -->
  <section class="perf-card" id="perf-card" aria-label="Delivery performance">
    <div class="perf-head">
      <div class="perf-title">
        <span class="ico"><i class="fa fa-line-chart"></i></span>Delivery Performance
        <span class="perf-scope">All-time portfolio &middot; not affected by filters</span>
      </div>
      <div class="perf-mini" id="perf-mini"></div>
      <div class="perf-flags" id="perf-flags"></div>
      <button type="button" class="perf-toggle" id="perf-toggle" onclick="togglePerf()" aria-expanded="true" aria-controls="perf-grid"><span>Hide</span></button>
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
          <kbd class="kbd-hint" aria-hidden="true" title="Press / to search">/</kbd>
        </div>
        <!-- Dua ekspor yang BERBEDA, sengaja dipisah:
             - "Export List"  : isi tabel apa adanya, mengikuti filter & urutan
                                yang sedang tampil. Dibentuk di sisi browser.
             - "Export Excel" : laporan KPI "PROGRESS PROJECT IT" di
                                ekspor_project.php - hanya project Done/Live,
                                berformat matriks penilaian, dan dibatasi user
                                tertentu. Bukan pengganti yang satunya. -->
        <button type="button" class="btn-export" onclick="exportListExcel()">
          <i class="fa fa-table"></i> Export List
        </button>
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
                  <div class="mod-combo" id="mod-combo">
                    <span class="mod-combo-ico" aria-hidden="true"><i class="fa fa-folder-o"></i></span>
                    <input type="text" class="form-control form-control-modern" id="pj-category" placeholder="Choose or type a module" maxlength="100" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="mod-combo-list">
                    <button type="button" class="mod-combo-caret" tabindex="-1" aria-label="Show modules"></button>
                    <div class="mod-combo-panel" id="mod-combo-panel">
                      <div class="mod-combo-list" id="mod-combo-list" role="listbox"></div>
                    </div>
                  </div>
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
<!-- SheetJS: dipakai tombol "Export List". header.php tidak memuat pustaka JS
     apa pun, jadi tiap halaman memuat sendiri yang dibutuhkannya. -->
<script src="../css/4.1.1/xlsx.full.min.js"></script>

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
    'Signalbit': { c: '#e8590c', s: '#fdece2' },
    'Knitting':  { c: '#d6336c', s: '#fce8ef' }
  };
  // Modul baku: selalu ada di pilihan Module (form & filter) walau belum ada
  // satu project pun. Modul lain tetap boleh diketik bebas di form.
  var MODULE_PRESETS = ['AP', 'AR', 'NDS', 'Signalbit', 'Knitting'];
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
    'NDS': 'fa-cubes', 'AP': 'fa-university', 'AR': 'fa-file-invoice-dollar', 'Signalbit': 'fa-bolt', 'Knitting': 'fa-yarn'
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
  // Requester yang mengajukan sebagian besar project (>= 60%) sudah disebut di
  // hero sebagai Key stakeholder - di kartu namanya tidak diulang, jadi yang
  // tampil hanya requester lain. Diisi oleh renderPortfolio().
  var keyStakeholder = '';
  function requesterChipHtml(name) {
    return name && name === keyStakeholder ? '' : picChipHtml(name);
  }
  // Medium adalah prioritas bawaan; lencana hanya untuk yang menyimpang.
  function priorityBadgeHtml(p) {
    return p.priority && p.priority !== 'Medium' ? '<span class="badge badge-priority-' + p.priority + '">' + p.priority + '</span>' : '';
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
        applyProjects(r.data);
      },
      error: function () { Swal.fire('Error', 'Failed to contact the server.', 'error'); }
    });
  }
  function applyProjects(data) {
    allProjects = data;
    renderPortfolio();
    updateCategoryList();
    updatePicList();
    updateMonthList();
    renderActive();
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
    // isDoneStatus, bukan 'Done' saja: project yang sudah Live juga terkirim
    // (dulu bulan lalu terbaca 0 dan panahnya naik palsu).
    var prevPace = allProjects.filter(function (p) {
      return isDoneStatus(p.status) && p.actual_date && p.actual_date.substring(0, 7) === prevKey && parseInt(p.actual_date.substring(8, 10), 10) <= dom;
    }).length;
    var delta = curCount - prevPace;
    var topPic = s.picOrder[0];
    keyStakeholder = topPic && s.pics[topPic] / s.total >= 0.6 ? topPic : '';

    // --- hero meta chips ---
    var meta = '';
    if (s.first) meta += '<span class="hero-chip"><i class="fa fa-flag-o"></i>Tracking since <b>' + fmtDate(s.first, { month: 'short', year: 'numeric' }) + '</b></span>';
    if (s.picOrder.length) meta += '<span class="hero-chip"' + (keyStakeholder ? ' title="Requested ' + s.pics[topPic] + ' of ' + s.total + ' projects - cards only name other requesters"' : '') + '><i class="fa fa-user-o"></i>Key stakeholder <b>' + escHtml(s.picOrder[0]) + '</b></span>';
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

    // Ringkasan satu baris - hanya tampil saat panel ini dilipat.
    $('#perf-mini').html(
      '<span><b>' + s.delivered + '</b> delivered</span>' +
      (measured ? '<span><b>' + onTimePct + '%</b> on time</span>' : '') +
      (leadAvg !== null ? '<span><b>' + leadTxt + 'd</b> avg. lead</span>' : '') +
      '<span><b>' + curCount + '</b> in ' + monthShort(curKey) + '</span>'
    );
    syncPerfToggle();
  }

  // ===== Delivery Performance: lipat / buka (diingat per browser) =====
  function togglePerf() {
    var collapsed = document.getElementById('proj-page-root').classList.toggle('perf-collapsed');
    try { localStorage.setItem('projPerfCollapsed', collapsed ? '1' : '0'); } catch (e) {}
    syncPerfToggle();
  }
  function syncPerfToggle() {
    var collapsed = document.getElementById('proj-page-root').classList.contains('perf-collapsed');
    $('#perf-toggle').attr('aria-expanded', collapsed ? 'false' : 'true')
      .attr('title', collapsed ? 'Show delivery performance' : 'Hide delivery performance')
      .find('span').text(collapsed ? 'Show' : 'Hide');
  }

  // ===== Mode tampilan: Normal / 3D =====
  // Mode Normal = tampilan yang sudah ada, tidak berubah sama sekali.
  // Mode 3D menambah "Project Library" di hero (../js/library3d.js +
  // Three.js, dua-duanya baru diunduh saat tombol 3D dipilih): satu rak per
  // modul, satu baris per bulan, satu buku per project. Buku yang diambil
  // dari rak dibuka seperti buku sungguhan (lihat "Buku project" di bawah).
  // Kartu board juga ikut miring mengikuti kursor. Pilihannya diingat per
  // browser.
  var LIB3D_URL = '../js/library3d.js';
  var lib3d = null, lib3dLoading = false;
  // import() ditulis lewat Function: browser lama yang belum mengenal sintaks
  // itu tetap bisa membaca seluruh skrip ini (mode Normal tetap jalan) -
  // kegagalannya baru muncul saat tombol 3D dipilih, dan ditangani di bawah.
  var importModule = (function () {
    try { return new Function('u', 'return import(u)'); }
    catch (e) { return function () { return Promise.reject(e); }; }
  })();

  function isMode3d() { return document.getElementById('proj-page-root').classList.contains('mode-3d'); }
  function prefersReducedMotion() { return !!(window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches); }

  function setViewMode(mode) {
    var on = mode === '3d';
    document.getElementById('proj-page-root').classList.toggle('mode-3d', on);
    try { localStorage.setItem('projMode', on ? '3d' : 'normal'); } catch (e) {}
    syncModeSwitch();
    // Dari tombol: adegan pintu perpustakaan terbuka. Saat halaman dimuat
    // ulang dalam mode 3D, langsung masuk saja (lihat inisialisasi di bawah).
    if (on) start3d(true); else stop3d();
  }

  function syncModeSwitch() {
    var on = isMode3d();
    $('.mode-switch button').each(function () {
      var active = ($(this).attr('data-mode') === '3d') === on;
      $(this).toggleClass('active', active).attr('aria-pressed', active ? 'true' : 'false');
    });
  }

  function start3d(intro) {
    if (lib3d) { refresh3d(); return; }
    if (lib3dLoading) return;
    lib3dLoading = true;
    $('#scene3d').addClass('loading');
    importModule(new URL(LIB3D_URL, document.baseURI).href).then(function (mod) {
      lib3dLoading = false;
      $('#scene3d').removeClass('loading');
      if (!isMode3d()) return;           // sudah kembali ke Normal selagi memuat
      lib3d = mod.createLibrary(document.getElementById('scene3d'), {
        onOpenBook: openBook,
        onPick: openDrawer,
        onNewBook: newBookFromDesk,
        userName: <?= json_encode((string)$full_name, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>,   // sapaan Lucca
        fmtDate: fmtDate,
        reducedMotion: prefersReducedMotion(),
        intro: !!intro
      });
      refresh3d();
      liveStart();
    }).catch(function (err) {
      lib3dLoading = false;
      $('#scene3d').removeClass('loading').empty();
      if (window.console) console.warn('3D view unavailable:', err);
      document.getElementById('proj-page-root').classList.remove('mode-3d');
      try { localStorage.setItem('projMode', 'normal'); } catch (e) {}
      syncModeSwitch();
      Swal.fire({ icon: 'info', title: '3D view unavailable', text: "This browser or device can't show the 3D view, so the normal view is shown instead." });
    });
  }

  // ---- Kabar langsung (mode 3D): tiap ±1 menit cek perubahan dari rekan ----
  // Project yang berubah status / progres / baru dibuat: data dimuat ulang
  // diam-diam, lonceng perpustakaan berbunyi & bukunya terbang ke rak; yang
  // jadi Done/Live dirayakan. Perubahan oleh pengguna ini sendiri sudah dimuat
  // ulang saat itu juga, jadi tidak ikut diumumkan.
  var LIVE_MS = 60000, liveTimer = null;
  function liveStart() { if (!liveTimer) liveTimer = setInterval(livePoll, LIVE_MS); }
  function liveStop() { if (liveTimer) { clearInterval(liveTimer); liveTimer = null; } }
  function livePoll() {
    if (!lib3d || document.hidden || book || draggedProjectId || $('.modal.show').length || $('#detail-drawer').hasClass('show')) return;
    $.ajax({
      url: 'ajax_project.php', method: 'POST', data: { action: 'list' }, dataType: 'json',
      success: function (r) {
        if (!r || r.status !== 'success' || !lib3d || book) return;
        var old = {}, items = [], doneId = null;
        allProjects.forEach(function (p) { old[p.id] = p; });
        r.data.forEach(function (p) {
          var o = old[p.id], who = p.updated_by || p.created_by || '';
          if (!o) {
            items.push({ id: p.id, title: p.project_name, text: 'New book on the ' + (p.category || 'General') + ' shelf' + (who ? ' \u00b7 by ' + who : '') });
          } else if (o.status !== p.status) {
            items.push({ id: p.id, title: p.project_name, text: o.status + ' \u2192 ' + p.status + (who ? ' \u00b7 by ' + who : '') });
            if (doneId == null && isDoneStatus(p.status) && !isDoneStatus(o.status)) doneId = p.id;
          } else if (String(o.progress) !== String(p.progress) || (o.updated_date || '') !== (p.updated_date || '')) {
            var moved = String(o.progress) !== String(p.progress);
            items.push({ id: p.id, title: p.project_name, text: 'Updated' + (moved ? ' \u00b7 ' + (parseInt(p.progress) || 0) + '% done' : '') + (who ? ' \u00b7 by ' + who : '') });
          }
        });
        if (!items.length && r.data.length === allProjects.length) return;
        applyProjects(r.data);
        if (items.length && lib3d) {
          lib3d.news(items);
          if (doneId != null) lib3d.celebrate(doneId);
        }
      }
    });
  }

  function stop3d() {
    liveStop();
    closeBook(true);
    if (lib3d) { lib3d.destroy(); lib3d = null; }
  }

  // Data perpustakaan = filter yang sedang dipakai, KECUALI filter bulan:
  // baris rak memang deret bulan, jadi bulan yang dipilih cukup disorot.
  function refresh3d() {
    if (!lib3d) return;
    lib3d.update({
      projects: getFiltered({ ignoreMonth: true }),
      lanes: lanes3d(),
      currentMonth: currentMonthKey,
      highlightMonth: $('#month-filter').val() || '',
      filtered: activeFilter.length > 0 || !!$('#search-box').val()
    });
    consumeBookLink();
    consumeNewBook();
  }

  // Rak = semua modul (Knitting yang masih kosong ikut), terbanyak dulu.
  // Kalau filter modul dipakai, tinggal rak modul itu saja.
  function lanes3d() {
    var counts = {};
    allProjects.forEach(function (p) { var k = p.category || 'General'; counts[k] = (counts[k] || 0) + 1; });
    var names = moduleCombo.options.map(function (o) { return o.name; });
    if (counts['General'] && names.indexOf('General') === -1) names.push('General');
    var mod = $('#module-filter').val();
    if (mod) names = [mod];
    names.sort(function (a, b) { return (counts[b] || 0) - (counts[a] || 0) || a.localeCompare(b); });
    return names.map(function (n) { return { name: n, color: moduleColor(n === 'General' ? null : n).c }; });
  }

  // ===== Buku project (mode 3D) =====
  // Buku yang diambil dari rak dibuka di tengah layar seperti buku sungguhan:
  // sampul -> sampul dalam & halaman judul -> ringkasan & tugas (milestone)
  // -> riwayat. Hanya untuk dibaca; mengubah data tetap lewat panel detail
  // ("Open full details"). Animasinya menyambung dari posisi buku di rak
  // (info.rect dari library3d.js): buku berputar menunjukkan sampulnya
  // sambil membesar, lalu sampulnya terbuka. Layar < 760px: satu halaman
  // per tampilan.
  var book = null;
  var BK_STATUS = { live: ['Live', '#8b5cf6'], done: ['Done', '#10b981'], prog: ['On Progress', '#3b82f6'], plan: ['Planned', '#64748b'], over: ['Overdue', '#ef4444'] };
  var BK_EASE = 'cubic-bezier(.45,.05,.25,1)';
  var BK_ICON = {
    prev: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 6l-6 6 6 6"/></svg>',
    next: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 6l6 6-6 6"/></svg>',
    stamp: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 3.5h5a1.5 1.5 0 0 1 1.5 1.5c0 2-1.6 3.2-1.6 5.5v1.5H9.6v-1.5C9.6 8.2 8 7 8 5a1.5 1.5 0 0 1 1.5-1.5z"/><path d="M5 15h14v3H5zM6.5 20.5h11"/></svg>',
    link: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M10 14a4.5 4.5 0 0 0 6.4 0l3-3a4.5 4.5 0 0 0-6.4-6.4l-1 1"/><path d="M14 10a4.5 4.5 0 0 0-6.4 0l-3 3a4.5 4.5 0 0 0 6.4 6.4l1-1"/></svg>',
    print: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M7 9V3.5h10V9"/><rect x="3.5" y="9" width="17" height="8" rx="2"/><path d="M7 14h10v6.5H7z"/></svg>'
  };

  function ensureReader() {
    var el = document.getElementById('bk-reader');
    if (el) return el;
    el = document.createElement('div');
    el.id = 'bk-reader';
    el.className = 'bk-reader';
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'true');
    el.innerHTML =
      '<div class="bk-backdrop" data-bk="close"></div>' +
      '<div class="bk-book">' +
        '<div class="bk-boards"></div>' +
        '<div class="bk-page bk-left pg-l" data-bk="prev"></div>' +
        '<div class="bk-page bk-right pg-r" data-bk="next"></div>' +
        '<div class="bk-cover"><div class="bk-cover-front"></div><div class="bk-cover-back pg-l"></div></div>' +
      '</div>' +
      '<div class="bk-bar" role="toolbar" aria-label="Book">' +
        '<button type="button" class="bk-nav" data-bk="prev" aria-label="Previous page">' + BK_ICON.prev + '</button>' +
        '<span class="bk-dots" aria-hidden="true"></span>' +
        '<button type="button" class="bk-nav" data-bk="next" aria-label="Next page">' + BK_ICON.next + '</button>' +
        '<button type="button" class="bk-nav" data-bk="stamp" aria-label="Stamp a new status" title="Stamp a new status" aria-haspopup="true" aria-expanded="false">' + BK_ICON.stamp + '</button>' +
        '<button type="button" class="bk-nav" data-bk="share" aria-label="Copy a link to this book" title="Copy a link to this book">' + BK_ICON.link + '</button>' +
        '<button type="button" class="bk-nav" data-bk="print" aria-label="Print or save as PDF" title="Print or save as PDF">' + BK_ICON.print + '</button>' +
        '<button type="button" class="bk-act" data-bk="details"><span class="bk-l-long">Open full details</span><span class="bk-l-short">Details</span></button>' +
        '<button type="button" class="bk-act bk-close" data-bk="close">Put back</button>' +
        '<span class="bk-toast" role="status" aria-live="polite"></span>' +
        '<span class="bk-stamps" role="menu" aria-label="New status" hidden>' + ['Planned', 'On Progress', 'Done', 'Live'].map(function (st) {
          return '<button type="button" role="menuitem" data-stamp="' + st + '" style="--sc:' + BK_STATUS[bookKeyOfStatus(st)][1] + ';">' + st + '</button>';
        }).join('') + '</span>' +
      '</div>';
    document.body.appendChild(el);
    $(el).on('click', '[data-stamp]', function () { bookSetStatus(this.getAttribute('data-stamp')); });
    // Tugas di bab II dicentang langsung di buku (tanpa ikut membalik halaman)
    $(el).on('click', '.bk-tasks li[data-mid]', function (e) { e.stopPropagation(); bookToggleTask(this); });
    $(el).on('click', '[data-bk]', function () {
      var a = this.getAttribute('data-bk');
      if (a === 'close') closeBook();
      else if (a === 'prev') bookGo(-1);
      else if (a === 'next') bookGo(1);
      else if (a === 'stamp') bookStampMenu();
      else if (a === 'share') bookShare();
      else if (a === 'print') bookPrint();
      else if (a === 'details' && book) { var id = book.id; closeBook(true); openDrawer(id); }
    });
    // geser jari di atas buku = balik halaman
    var sx = null;
    el.querySelector('.bk-book').addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
    el.querySelector('.bk-book').addEventListener('touchend', function (e) {
      if (sx == null) return;
      var dx = e.changedTouches[0].clientX - sx;
      sx = null;
      if (Math.abs(dx) > 50) bookGo(dx < 0 ? 1 : -1);
    });
    // Selama buku terbuka: Esc menutup, panah membalik halaman, dan pintasan
    // halaman (/, N, Esc panel detail) tidak ikut jalan.
    window.addEventListener('keydown', function (e) {
      if (!book) return;
      var ae = document.activeElement, onTask = !!(ae && ae.getAttribute && ae.getAttribute('data-mid') && el.contains(ae));
      var sm = el.querySelector('.bk-stamps');
      if (e.key === 'Escape') { e.preventDefault(); if (sm && !sm.hidden) bookStampMenu(false); else closeBook(); }
      else if (onTask && (e.key === 'Enter' || e.key === ' ')) { e.preventDefault(); bookToggleTask(ae); }
      else if (e.key === 'ArrowRight' || e.key === 'PageDown') { e.preventDefault(); bookGo(1); }
      else if (e.key === 'ArrowLeft' || e.key === 'PageUp') { e.preventDefault(); bookGo(-1); }
      else if (e.key === 'Tab') {
        // fokus berputar di tugas & tombol buku saja, tidak lari ke halaman di belakang
        var btns = [].slice.call(el.querySelectorAll('.bk-page [data-mid], .bk-bar button')).filter(function (b) { return !b.disabled && b.offsetParent !== null; });
        if (btns.length) {
          e.preventDefault();
          var at = btns.indexOf(document.activeElement);
          if (at < 0) at = e.shiftKey ? 0 : -1;
          btns[(at + (e.shiftKey ? -1 : 1) + btns.length) % btns.length].focus();
        }
      }
      e.stopPropagation();
    }, true);
    window.addEventListener('resize', function () { if (book && !book.busy) bookLayout(true); });
    return el;
  }

  function bookGeo() {
    var vw = document.documentElement.clientWidth, vh = window.innerHeight, barH = 64;
    var single = vw < 760;
    var H = Math.min(vh - barH - (single ? 40 : 70), 620);
    var P = Math.round(H / 1.4);
    var maxP = single ? vw - 40 : Math.floor((vw - 96) / 2);
    if (P > maxP) { P = maxP; H = Math.round(P * 1.4); }
    var W = single ? P : P * 2;
    return { P: P, H: H, W: W, single: single, left: Math.round((vw - W) / 2), top: Math.max(16, Math.round((vh - barH - H) / 2)) };
  }

  // transform .bk-book: geser (dx,dy) + geser sampul ke tengah (sh) + skala + putar
  function bookT(dx, dy, sh, s, rot) {
    return 'translate(' + dx.toFixed(1) + 'px,' + dy.toFixed(1) + 'px) translateX(' + sh.toFixed(1) + 'px) scale(' + s.toFixed(4) + ') rotateY(' + rot + 'deg)';
  }
  // Posisi awal/akhir: sampul tepat di atas buku di rak, tampak dari samping.
  function bookFrom(rect) {
    var g = book.geo, sh = g.single ? 0 : -g.P / 2;
    if (!rect || !(rect.h > 2)) return bookT(0, 40, sh, 0.6, 30);
    var cx = g.left + (g.single ? g.P / 2 : g.P), cy = g.top + g.H / 2;
    return bookT(rect.x + rect.w / 2 - cx, rect.y + rect.h / 2 - cy, sh, Math.max(0.04, Math.min(1, rect.h / g.H)), 78);
  }

  function bookAnim(el, frames, ms, easing) {
    var endFrame = frames[frames.length - 1];
    function commit() { for (var k in endFrame) el.style[k] = endFrame[k]; }
    if (!ms || !el.animate) { commit(); return Promise.resolve(); }
    return new Promise(function (resolve) {
      var a = el.animate(frames, { duration: ms, easing: easing || BK_EASE, fill: 'forwards' });
      a.onfinish = function () { commit(); a.cancel(); resolve(); };
    });
  }

  function bookLayout(keep) {
    var el = book.el, g = book.geo = bookGeo(), bk = el.querySelector('.bk-book');
    bk.style.left = g.left + 'px';
    bk.style.top = g.top + 'px';
    bk.style.width = g.W + 'px';
    bk.style.height = g.H + 'px';
    bk.style.fontSize = Math.max(11, Math.min(16, g.P / 28)).toFixed(1) + 'px';
    bk.style.transformOrigin = (g.single ? g.P / 2 : g.P * 1.5) + 'px 50%';
    el.classList.toggle('single', g.single);
    ['.bk-left', '.bk-right', '.bk-cover'].forEach(function (s) { el.querySelector(s).style.width = g.P + 'px'; });
    el.querySelector('.bk-right').style.left = el.querySelector('.bk-cover').style.left = (g.single ? 0 : g.P) + 'px';
    el.querySelector('.bk-bar').style.top = (g.top + g.H + 20) + 'px';
    var spreads = g.single
      ? [['', 'title'], ['', 'summary'], ['', 'tasks'], ['', 'history'], ['', 'card']]
      : [['endpaper', 'title'], ['summary', 'tasks'], ['history', 'card']];
    if (keep && book.spreads && book.spreads.length !== spreads.length) { book.spreads = spreads; book.spread = 0; bookShow(0); }
    book.spreads = spreads;
    if (keep) bk.style.transform = bookT(0, 0, 0, 1, 0);
  }

  function bookRender(el, key) {
    el.innerHTML = key ? (book.pages[key] || '') : '';
    $(el).find('[data-bk-fill]').each(function () {
      var k = this.getAttribute('data-bk-fill');
      if (book.fill[k] != null) this.innerHTML = book.fill[k];
    });
  }
  function bookShow(n) {
    var el = book.el, s = book.spreads[n];
    bookRender(el.querySelector('.bk-left'), s[0]);
    bookRender(el.querySelector('.bk-right'), s[1]);
    el.querySelector('.bk-dots').innerHTML = book.spreads.map(function (x, i) { return '<span' + (i === n ? ' class="on"' : '') + '></span>'; }).join('');
    el.querySelector('.bk-nav[data-bk="prev"]').disabled = n === 0;
    el.querySelector('.bk-nav[data-bk="next"]').disabled = n === book.spreads.length - 1;
  }
  function bookFill(k, html) {
    // tugas yang sedang difokus (keyboard) tetap difokus sesudah daftarnya diganti
    var a = document.activeElement, mid = a && a.getAttribute ? a.getAttribute('data-mid') : null;
    book.fill[k] = html;
    $(book.el).find('[data-bk-fill="' + k + '"]').html(html);
    if (mid) { var nx = book.el.querySelector('.bk-page [data-mid="' + mid + '"]'); if (nx) nx.focus(); }
  }

  function bookTiming(p) {
    var day = 86400000;
    if (isDoneStatus(p.status)) {
      if (!p.actual_date) return '';
      if (!p.target_date) return 'Delivered ' + fmtDate(p.actual_date);
      var late = Math.round((new Date(p.actual_date + 'T00:00:00') - new Date(p.target_date + 'T00:00:00')) / day);
      return late > 0 ? 'Delivered ' + late + (late === 1 ? ' day' : ' days') + ' late' : late < 0 ? 'Delivered on time — ' + (-late) + (late === -1 ? ' day' : ' days') + ' early' : 'Delivered on time';
    }
    if (!p.target_date) return 'No target date yet';
    var today = new Date(); today.setHours(0, 0, 0, 0);
    var left = Math.round((new Date(p.target_date + 'T00:00:00') - today) / day);
    return left < 0 ? Math.abs(left) + (left === -1 ? ' day' : ' days') + ' overdue' : left === 0 ? 'Due today' : left + (left === 1 ? ' day' : ' days') + ' left';
  }

  function bookPages(p, key) {
    var mc = moduleColor(p.category).c, mod = escHtml(p.category || 'General'), st = BK_STATUS[key] || BK_STATUS.plan, stale = bookStale(p);
    var name = escHtml(p.project_name), pic = p.pic ? escHtml(p.pic) : '&mdash;';
    var progress = Math.max(0, Math.min(100, parseInt(p.progress) || 0));
    var run = '<div class="bk-run">' + name + '</div>';
    function num(n) { return '<div class="bk-num">' + n + '</div>'; }
    function row(k, v) { return '<tr><th>' + k + '</th><td>' + (v || '&mdash;') + '</td></tr>'; }
    var desc = p.description
      ? escHtml(p.description).split(/\n\s*\n/).map(function (t) { return '<p>' + t.replace(/\n/g, '<br>') + '</p>'; }).join('')
      : '<p class="bk-muted">No description has been written for this project yet.</p>';
    return {
      endpaper: '<div class="bk-pg bk-endpaper" style="--mc:' + mc + ';"><div class="bk-plate">' +
        '<div class="bk-plate-k">Ex Libris</div><div class="bk-plate-t">Project Library</div><div class="bk-plate-s">' + mod + ' shelf</div>' +
        '<div class="bk-plate-line">Requested by</div><div class="bk-plate-n">' + pic + '</div></div></div>',
      title: '<div class="bk-pg bk-titlepg">' +
        '<div class="bk-mod" style="--mc:' + mc + ';">' + mod + '</div><div class="bk-orn"></div>' +
        '<h2 class="bk-title">' + name + '</h2><div class="bk-orn"></div>' +
        '<div class="bk-stamp" style="--sc:' + st[1] + ';">' + st[0] + '</div>' +
        '<div class="bk-byline">Requested by <b>' + pic + '</b>' + (p.priority ? ' &middot; ' + escHtml(p.priority) + ' priority' : '') + '</div>' +
        num(1) + '</div>',
      summary: '<div class="bk-pg">' + run + '<div class="bk-ch">Chapter I</div><h3 class="bk-h">Summary</h3>' +
        '<div class="bk-body bk-text">' + desc + '</div>' +
        '<div class="bk-progress"><span class="bk-progress-k">Progress</span><span class="bk-progress-bar"><span style="width:' + progress + '%;"></span></span><b>' + progress + '%</b></div>' +
        '<table class="bk-dates">' + row('Started', fmtDate(p.start_date)) + row('Target', fmtDate(p.target_date)) +
        row('Delivered', fmtDate(p.actual_date)) + (p.live_date ? row('Live', fmtDate(p.live_date)) : '') + '</table>' +
        '<div class="bk-note">' + escHtml(bookTiming(p)) + '</div>' +
        (stale ? '<div class="bk-note bk-dusty">Gathering dust &mdash; untouched for ' + stale + ' days</div>' : '') + num(2) + '</div>',
      tasks: '<div class="bk-pg">' + run + '<div class="bk-ch">Chapter II</div><h3 class="bk-h">Tasks</h3>' +
        '<div class="bk-body" data-bk-fill="tasks"><p class="bk-muted">Turning to the task list&hellip;</p></div>' + num(3) + '</div>',
      history: '<div class="bk-pg">' + run + '<div class="bk-ch">Chapter III</div><h3 class="bk-h">History</h3>' +
        '<div class="bk-body" data-bk-fill="history"><p class="bk-muted">Reading the history&hellip;</p></div>' + num(4) + '</div>',
      // kartu peminjaman dalam saku di sampul belakang
      card: '<div class="bk-pg bk-endpaper bk-cardpg" style="--mc:' + mc + ';"><div class="bk-slip">' +
        '<div class="bk-card">' +
          '<div class="bk-card-top"><span>Project Library</span><span>Date due</span></div>' +
          '<div class="bk-card-due">' + (p.target_date ? '<span class="bk-ink" style="--r:-3deg;">' + bookStamp(p.target_date) + '</span>' : '<span class="bk-muted">no date yet</span>') + '</div>' +
          '<div class="bk-card-f"><span>Title</span><b>' + name + '</b></div>' +
          '<div class="bk-card-f"><span>Borrower</span><b class="bk-hand">' + pic + '</b></div>' +
          '<table class="bk-card-t"><thead><tr><th>Date</th><th>Handled by</th></tr></thead><tbody data-bk-fill="card">' + bookCardRows([]) + '</tbody></table>' +
        '</div>' +
        '<div class="bk-pocket"><b>Project Library</b><span>Please deliver this project on or before the date due.</span></div>' +
      '</div></div>'
    };
  }
  function bookCover(p, key) {
    var st = BK_STATUS[key] || BK_STATUS.plan;
    return '<div class="bk-cv-frame">' +
      '<div class="bk-cv-mod">' + escHtml(p.category || 'General') + '</div><div class="bk-orn"></div>' +
      '<div class="bk-cv-title">' + escHtml(p.project_name) + '</div><div class="bk-orn"></div>' +
      '<div class="bk-cv-foot"><span class="bk-cv-tag" style="--sc:' + st[1] + ';">' + st[0] + '</span><span>Project Library</span></div>' +
    '</div>';
  }
  function bookTasks(items) {
    if (!items.length) return '<p class="bk-muted">No tasks have been written down for this project yet.</p>';
    var done = items.filter(function (m) { return parseInt(m.is_done) === 1; }).length;
    return '<div class="bk-count">' + done + ' of ' + items.length + ' done <span>&middot; tick a task to update it</span></div><ol class="bk-tasks">' + items.map(function (m) {
      var on = parseInt(m.is_done) === 1;
      return '<li data-mid="' + (parseInt(m.id, 10) || 0) + '" role="checkbox" tabindex="0" aria-checked="' + on + '"' + (on ? ' class="done"' : '') + '>' +
        '<span class="bk-box"></span><span>' + escHtml(m.title) + '</span></li>';
    }).join('') + '</ol>';
  }
  function bookHistory(items) {
    if (!items.length) return '<p class="bk-muted">Nothing has been recorded yet.</p>';
    var shown = items.slice(0, 7);
    return '<ul class="bk-log">' + shown.map(function (a) {
      return '<li><span class="bk-log-t">' + activityText(a) + '</span><span class="bk-log-m">' + escHtml(a.changed_by || '') + ' &middot; ' + relativeTime(a.changed_date) + '</span></li>';
    }).join('') + '</ul>' +
      (items.length > shown.length ? '<p class="bk-muted">&hellip;and ' + (items.length - shown.length) + ' earlier entries in the full details.</p>' : '');
  }
  // Kartu peminjaman: 7 catatan terakhir, urut dari yang terlama (seperti
  // kartu sungguhan yang diisi dari atas); sisanya baris kosong.
  function bookCardRows(items) {
    var rows = items.slice(0, 7).reverse().map(function (a, i) {
      return '<tr><td><span class="bk-ink" style="--r:' + (((i * 37) % 7 - 3) * 0.7).toFixed(1) + 'deg;">' + bookStamp(a.changed_date) + '</span></td>' +
        '<td class="bk-hand">' + escHtml(a.changed_by || '') + '</td></tr>';
    });
    while (rows.length < 7) rows.push('<tr><td>&nbsp;</td><td></td></tr>');
    return rows.join('');
  }
  // "2026-11-03 10:20:00" -> "03 NOV 26" (cap tanggal perpustakaan)
  function bookStamp(d) {
    var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(d || ''));
    return m ? m[3] + ' ' + MONTH_NAMES[+m[2] - 1].substring(0, 3).toUpperCase() + ' ' + m[1].substring(2) : '';
  }
  // Hari sejak project yang belum selesai terakhir diubah (0 = belum 14 hari)
  function bookStale(p) {
    if (isDoneStatus(p.status)) return 0;
    var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(p.updated_date || p.created_date || ''));
    if (!m) return 0;
    var today = new Date(); today.setHours(0, 0, 0, 0);
    var n = Math.round((today - new Date(+m[1], +m[2] - 1, +m[3])) / 86400000);
    return n >= 14 ? n : 0;
  }
  function bookLoad(b) {
    $.ajax({
      url: 'ajax_milestones.php', method: 'POST', data: { action: 'list', project_id: b.id }, dataType: 'json',
      success: function (r) { if (book === b) bookFill('tasks', bookTasks(r.data || [])); },
      error: function () { if (book === b) bookFill('tasks', '<p class="bk-muted">The task list could not be loaded.</p>'); }
    });
    $.ajax({
      url: 'ajax_activity_log.php', method: 'POST', data: { project_id: b.id }, dataType: 'json',
      success: function (r) { if (book === b) { bookFill('history', bookHistory(r.data || [])); bookFill('card', bookCardRows(r.data || [])); } },
      error: function () { if (book === b) bookFill('history', '<p class="bk-muted">The history could not be loaded.</p>'); }
    });
  }

  function bookLock(on) {
    var d = document.documentElement;
    if (on) {
      var sw = window.innerWidth - d.clientWidth;
      d.style.overflow = 'hidden';
      if (sw > 0) document.body.style.paddingRight = sw + 'px';
    } else {
      d.style.overflow = '';
      document.body.style.paddingRight = '';
    }
  }

  function openBook(id, info) {
    var p = allProjects.filter(function (x) { return x.id == id; })[0];
    if (!p) { if (lib3d) lib3d.returnBook(); return; }
    if (book) closeBook(true);
    info = info || {};
    var el = ensureReader(), key = info.key || 'plan';
    var b = book = { id: p.id, p: p, key: key, el: el, spread: 0, busy: true, fill: {}, pages: bookPages(p, key), prevFocus: document.activeElement };
    bookLayout(false);
    var bk = el.querySelector('.bk-book'), cover = el.querySelector('.bk-cover'), left = el.querySelector('.bk-left');
    el.style.setProperty('--cv', info.cover || '#3a2418');
    el.classList.toggle('blueprint', key === 'plan');
    el.querySelector('.bk-cover-front').innerHTML = bookCover(p, key);
    el.querySelector('.bk-cover-back').innerHTML = b.pages.endpaper;
    el.setAttribute('aria-label', p.project_name);
    bookShow(0);
    left.classList.add('hold');
    cover.style.visibility = '';
    cover.style.opacity = '1';
    cover.style.transform = 'translateZ(1px) rotateY(0deg)';
    el.classList.remove('open', 'lit', 'closing');
    el.classList.add('show');
    bookLock(true);
    bookSetLink(p.id);
    void el.offsetWidth;
    el.classList.add('lit');
    bookLoad(b);

    var g = b.geo, ms = prefersReducedMotion() ? 0 : 1, sh = g.single ? 0 : -g.P / 2;
    bookAnim(bk, [{ transform: bookFrom(info.rect) }, { transform: bookT(0, 0, sh, 1, 0) }], 720 * ms, 'cubic-bezier(.2,.75,.25,1)').then(function () {
      if (book !== b) return;
      el.classList.add('open');
      sfx('open');
      return Promise.all([
        // Hanya transform: opacity yang ikut dianimasikan "meratakan" ruang
        // 3D sampul, sehingga muka depannya tampak terbalik di halaman kiri.
        bookAnim(cover, [{ transform: 'translateZ(1px) rotateY(0deg)' }, { transform: 'translateZ(1px) rotateY(-180deg)' }], 950 * ms),
        g.single ? null : bookAnim(bk, [{ transform: bookT(0, 0, sh, 1, 0) }, { transform: bookT(0, 0, 0, 1, 0) }], 950 * ms)
      ]);
    }).then(function () {
      if (book !== b) return;
      cover.style.visibility = 'hidden';
      left.classList.remove('hold');
      b.busy = false;
      el.querySelector('.bk-close').focus();
    });
  }

  // Balik halaman: satu lembar (dua muka) diputar di sumbu punggung buku;
  // halaman yang akan tampil sudah disiapkan di bawahnya.
  function bookGo(dir) {
    if (!book || book.busy) return;
    var n = book.spread + dir;
    if (n < 0 || n >= book.spreads.length) return;
    var b = book, el = b.el, g = b.geo, cur = b.spreads[b.spread], nxt = b.spreads[n];
    var leaf = document.createElement('div'), anim;
    leaf.className = 'bk-leaf';
    leaf.innerHTML = '<div class="bk-face bk-front"></div><div class="bk-face bk-back"></div>';
    leaf.style.width = g.P + 'px';
    var front = leaf.firstChild, back = leaf.lastChild;
    b.busy = true;
    if (g.single) {
      leaf.style.left = '0px';
      leaf.style.transformOrigin = '0 50%';
      front.classList.add('pg-r');
      if (dir > 0) {
        bookRender(front, cur[1]);
        bookRender(el.querySelector('.bk-right'), nxt[1]);
        anim = [{ transform: 'translateZ(3px) rotateY(0deg)' }, { transform: 'translateZ(3px) rotateY(-180deg)' }];
      } else {
        bookRender(front, nxt[1]);
        anim = [{ transform: 'translateZ(3px) rotateY(-180deg)' }, { transform: 'translateZ(3px) rotateY(0deg)' }];
      }
    } else if (dir > 0) {
      leaf.style.left = g.P + 'px';
      leaf.style.transformOrigin = '0 50%';
      front.classList.add('pg-r'); back.classList.add('pg-l');
      bookRender(front, cur[1]);
      bookRender(back, nxt[0]);
      bookRender(el.querySelector('.bk-right'), nxt[1]);
      anim = [{ transform: 'translateZ(3px) rotateY(0deg)' }, { transform: 'translateZ(3px) rotateY(-180deg)' }];
    } else {
      leaf.style.left = '0px';
      leaf.style.transformOrigin = '100% 50%';
      front.classList.add('pg-l'); back.classList.add('pg-r');
      bookRender(front, cur[0]);
      bookRender(back, nxt[1]);
      bookRender(el.querySelector('.bk-left'), nxt[0]);
      anim = [{ transform: 'translateZ(3px) rotateY(0deg)' }, { transform: 'translateZ(3px) rotateY(180deg)' }];
    }
    el.querySelector('.bk-book').appendChild(leaf);
    sfx('flip');
    bookAnim(leaf, anim, prefersReducedMotion() ? 0 : 780).then(function () {
      if (leaf.parentNode) leaf.parentNode.removeChild(leaf);
      if (book !== b) return;
      b.spread = n;
      bookShow(n);
      b.busy = false;
    });
  }

  // Menutup: sampul menutup, lalu buku terbang kembali ke tempatnya di rak
  // (library3d.js menyelipkannya lagi lewat returnBook).
  function closeBook(instant) {
    if (!book) return;
    var b = book, el = b.el, g = b.geo;
    if (instant || prefersReducedMotion()) { bookDone(b); return; }
    if (b.closing) return;
    b.closing = b.busy = true;
    $(el).find('.bk-leaf').remove();
    var bk = el.querySelector('.bk-book'), cover = el.querySelector('.bk-cover');
    if (bk.getAnimations) bk.getAnimations().concat(cover.getAnimations()).forEach(function (a) { a.cancel(); });
    b.spread = 0;
    bookShow(0);
    el.querySelector('.bk-left').classList.add('hold');
    cover.style.visibility = '';
    el.classList.add('closing');
    var sh = g.single ? 0 : -g.P / 2;
    Promise.all([
      bookAnim(cover, [{ transform: 'translateZ(1px) rotateY(-180deg)' }, { transform: 'translateZ(1px) rotateY(0deg)' }], 650),
      g.single ? null : bookAnim(bk, [{ transform: bookT(0, 0, 0, 1, 0) }, { transform: bookT(0, 0, sh, 1, 0) }], 650)
    ]).then(function () {
      if (book !== b) return;
      sfx('close');
      el.classList.remove('open', 'lit');
      var rect = lib3d ? lib3d.bookRect() : null;
      return bookAnim(bk, [{ transform: bookT(0, 0, sh, 1, 0), opacity: 1 }, { transform: bookFrom(rect), opacity: rect ? 1 : 0 }], 560, 'cubic-bezier(.55,0,.7,.25)');
    }).then(function () { bookDone(b); });
  }
  function bookDone(b) {
    if (book !== b) return;
    var el = b.el, bk = el.querySelector('.bk-book'), cover = el.querySelector('.bk-cover');
    if (bk.getAnimations) bk.getAnimations().concat(cover.getAnimations()).forEach(function (a) { a.cancel(); });
    $(el).find('.bk-leaf').remove();
    el.classList.remove('show', 'lit', 'open', 'closing');
    el.querySelector('.bk-stamps').hidden = true;
    bk.style.transform = bk.style.opacity = '';
    book = null;
    bookLock(false);
    bookSetLink(null, b.id);
    if (lib3d) lib3d.returnBook();
    if (b.prevFocus && b.prevFocus.focus && document.contains(b.prevFocus)) {
      try { b.prevFocus.focus({ preventScroll: true }); } catch (e) { /* abaikan */ }
    }
  }

  function sfx(name) { if (lib3d && lib3d.sfx) lib3d.sfx(name); }

  // Stempel status langsung dari buku (quick_update_status.php, sama dgn
  // ubah status cepat di tabel): cap karet jatuh ke halaman, status, progres
  // & tanggalnya ikut berubah, lalu raknya dimuat ulang (bukunya pindah baris).
  function bookKeyOfStatus(st) { return st === 'Live' ? 'live' : st === 'Done' ? 'done' : st === 'On Progress' ? 'prog' : 'plan'; }
  function ymdToday() { var d = new Date(); return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
  function bookStampMenu(open) {
    if (!book) return;
    var m = book.el.querySelector('.bk-stamps'), btn = book.el.querySelector('[data-bk="stamp"]');
    if (open == null) open = m.hidden;
    m.hidden = !open;
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (!open) return;
    var cur = book.p.status;
    $(m).find('[data-stamp]').each(function () {
      var same = this.getAttribute('data-stamp') === cur;
      this.disabled = same;
      this.title = same ? 'Current status' : 'Stamp "' + this.getAttribute('data-stamp') + '"';
    });
    var f = m.querySelector('[data-stamp]:not(:disabled)');
    if (f) f.focus();
  }
  function bookSetStatus(status) {
    var b = book;
    bookStampMenu(false);
    if (!b || b.stamping || b.p.status === status) return;
    b.stamping = true;
    var st = BK_STATUS[bookKeyOfStatus(status)], ink = document.createElement('div');
    ink.className = 'bk-rubber';
    ink.style.setProperty('--sc', st[1]);
    ink.textContent = st[0];
    b.el.querySelector('.bk-right').appendChild(ink);
    sfx('stamp');
    if (lib3d && lib3d.librarianStamp) lib3d.librarianStamp();   // pustakawan di 3D ikut mengecap
    bookAnim(ink, [
      { transform: 'translate(-50%, -50%) scale(2.4) rotate(-20deg)', opacity: 0 },
      { transform: 'translate(-50%, -50%) scale(.92) rotate(-9deg)', opacity: 0.9, offset: 0.75 },
      { transform: 'translate(-50%, -50%) scale(1) rotate(-9deg)', opacity: 0.88 }
    ], prefersReducedMotion() ? 0 : 320, 'cubic-bezier(.3,.7,.4,1)');
    function drop() { if (ink.parentNode) ink.parentNode.removeChild(ink); }
    function fail() {
      b.stamping = false;
      bookAnim(ink, [{ opacity: 0.88 }, { opacity: 0 }], 400).then(drop);
      bookToast('The status could not be changed');
    }
    $.ajax({
      url: 'quick_update_status.php', method: 'POST', dataType: 'json', data: { id: b.id, status: status },
      success: function (r) {
        if (!r || r.status !== 'success') { fail(); return; }
        b.stamping = false;
        var p = b.p, today = ymdToday();
        p.status = status;
        if (isDoneStatus(status)) {
          p.progress = 100;
          p.actual_date = p.actual_date || today;
          if (status === 'Live') p.live_date = p.live_date || today;
          p.is_overdue = 0;
          fireConfetti();
          if (lib3d && lib3d.celebrate) lib3d.celebrate(p.id);
        } else p.is_overdue = p.target_date && p.target_date < today ? 1 : 0;
        b.key = bookKeyOf(p);
        b.pages = bookPages(p, b.key);
        b.el.classList.toggle('blueprint', b.key === 'plan');
        b.el.querySelector('.bk-cover-front').innerHTML = bookCover(p, b.key);
        bookToast('Stamped: ' + st[0]);
        setTimeout(function () {
          if (book === b && !b.busy) bookShow(b.spread);
          drop();
        }, 1200);
        loadProjects();
      },
      error: fail
    });
  }

  // Buku kosong di meja pustakawan -> form New Project. Sesudah disimpan,
  // buku barunya langsung disorot di raknya.
  var newBook = null;
  function newBookFromDesk() {
    newBook = { ids: allProjects.map(function (p) { return String(p.id); }), saved: false };
    openCreate();
  }
  function consumeNewBook() {
    if (!newBook || !newBook.saved || !lib3d) return;
    var known = newBook.ids, fresh = allProjects.filter(function (p) { return known.indexOf(String(p.id)) === -1; })[0];
    newBook = null;
    if (fresh) setTimeout(function () { if (lib3d) lib3d.findBook(fresh.id, false); }, 400);
  }
  $(function () {
    $('#modalProject').on('hidden.bs.modal', function () { if (newBook && !newBook.saved) newBook = null; });
  });


  // Centang tugas langsung di buku - sama dgn centang di panel detail
  // (ajax_milestones.php toggle): progres & status project ikut berubah,
  // lalu data dimuat ulang (rak dibangun ulang, buku ini tetap terbuka).
  function bookToggleTask(li) {
    var b = book;
    if (!b || li.classList.contains('busy')) return;
    var was = li.classList.contains('done');
    function mark(on) { li.classList.toggle('done', on); li.setAttribute('aria-checked', on ? 'true' : 'false'); }
    function revert() { li.classList.remove('busy'); mark(was); bookToast('The task could not be updated'); }
    li.classList.add('busy');
    mark(!was);
    sfx('tick');
    $.ajax({
      url: 'ajax_milestones.php', method: 'POST', dataType: 'json',
      data: { action: 'toggle', project_id: b.id, id: li.getAttribute('data-mid') },
      success: function (r) {
        if (!r || r.status !== 'success') { revert(); return; }
        li.classList.remove('busy');
        var p = b.p;
        if (r.progress !== null && r.progress !== undefined) {
          p.progress = r.progress;
          if (r.progress === 100 && !isDoneStatus(p.status)) { p.status = 'Done'; fireConfetti(); if (lib3d && lib3d.celebrate) lib3d.celebrate(p.id); }
          else if (r.progress < 100 && p.status === 'Done') p.status = 'On Progress';
        }
        if (book === b) {
          var pg = Math.max(0, Math.min(100, parseInt(p.progress) || 0));
          b.pages = bookPages(p, b.key);
          $(b.el).find('.bk-progress-bar span').css('width', pg + '%');
          $(b.el).find('.bk-progress b').text(pg + '%');
          bookLoad(b);
        }
        loadProjects();
      },
      error: revert
    });
  }

  function bookToast(msg) {
    var t = book && book.el.querySelector('.bk-toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(bookToast.timer);
    bookToast.timer = setTimeout(function () { t.classList.remove('show'); }, 2400);
  }

  // Salin tautan langsung ke buku ini (index.php#book=ID). Di http biasa
  // (bukan https/localhost) navigator.clipboard tidak ada - pakai cara lama.
  function bookShare() {
    if (!book) return;
    var url = location.href.split('#')[0] + '#book=' + book.id, btn = book.el.querySelector('[data-bk="share"]');
    function copied() { bookToast('Link copied — paste it to a colleague'); }
    function fallback() {
      var ta = document.createElement('textarea'), ok = false;
      ta.value = url;
      ta.setAttribute('readonly', '');
      ta.style.cssText = 'position:fixed;top:0;left:0;width:1px;height:1px;opacity:0;';
      book.el.appendChild(ta);
      ta.select();
      try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
      ta.parentNode.removeChild(ta);
      if (btn) btn.focus();
      if (ok) copied(); else window.prompt('Copy this link to the book:', url);
    }
    if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(url).then(copied, fallback);
    else fallback();
  }

  // Cetak / simpan PDF: sampul + semua halaman buku, satu A5 per halaman.
  function bookPrint() {
    var b = book;
    if (!b) return;
    var old = document.getElementById('bk-print');
    if (old) old.parentNode.removeChild(old);
    var pr = document.createElement('div'), plan = b.key === 'plan', cv = b.el.style.getPropertyValue('--cv');
    pr.id = 'bk-print';
    pr.className = 'bk-print';
    pr.innerHTML = '<section class="bk-pp bk-pp-cover' + (plan ? ' blueprint' : '') + '"' + (plan ? '' : ' style="--cv:' + cv + ';"') + '>' +
      '<div class="bk-cover-front">' + bookCover(b.p, b.key) + '</div></section>' +
      ['title', 'summary', 'tasks', 'history', 'card'].map(function (k) { return '<section class="bk-pp">' + b.pages[k] + '</section>'; }).join('');
    $(pr).find('[data-bk-fill]').each(function () {
      var k = this.getAttribute('data-bk-fill');
      if (b.fill[k] != null) this.innerHTML = b.fill[k];
    });
    document.body.appendChild(pr);
    var root = document.documentElement;
    root.classList.add('bk-printing');
    function done() {
      window.removeEventListener('afterprint', done);
      root.classList.remove('bk-printing');
      if (pr.parentNode) pr.parentNode.removeChild(pr);
    }
    window.addEventListener('afterprint', done);
    setTimeout(function () { window.print(); }, 50);
  }

  // ---- Tautan langsung ke satu buku: index.php#book=ID ----
  // Membuka mode 3D (tanpa adegan pintu, pilihan mode yg tersimpan tidak
  // diubah), kamera berjalan ke raknya lalu bukunya diambil & dibuka. Buku
  // yang tidak ada di rak (lebih lama dari bulan yg tampil / tersaring)
  // langsung dibuka. Selama buku terbuka alamatnya ikut berisi #book=ID.
  var bookLink = null;
  function bookLinkId() { var m = /^#book=(\d+)$/.exec(location.hash || ''); return m ? m[1] : null; }
  function bookSetLink(id, clearId) {
    try {
      var base = location.pathname + location.search;
      if (id != null) history.replaceState(history.state, '', base + '#book=' + id);
      else if (bookLinkId() === String(clearId)) history.replaceState(history.state, '', base);
    } catch (e) { /* abaikan */ }
  }
  function bookKeyOf(p) {
    return +p.is_overdue === 1 ? 'over' : p.status === 'Live' ? 'live' : p.status === 'Done' ? 'done' : p.status === 'On Progress' ? 'prog' : 'plan';
  }
  function followBookLink() {
    var id = bookLinkId();
    if (!id || (book && String(book.id) === id)) return;
    bookLink = id;
    if (!isMode3d()) { document.getElementById('proj-page-root').classList.add('mode-3d'); syncModeSwitch(); }
    start3d(false);
    consumeBookLink();
  }
  function consumeBookLink() {
    if (!bookLink || !lib3d || !allProjects.length) return;
    var id = bookLink;
    bookLink = null;
    var p = allProjects.filter(function (x) { return String(x.id) === id; })[0];
    if (!p) {
      bookSetLink(null, id);
      Swal.fire({ icon: 'info', title: 'Book not found', text: 'That project is no longer in the library.' });
      return;
    }
    if (!lib3d.findBook(p.id, true)) openBook(p.id, { key: bookKeyOf(p) });
  }
  window.addEventListener('hashchange', followBookLink);

  // Kartu ikut miring mengikuti kursor - hanya mode 3D, hanya perangkat
  // ber-mouse, dan tidak untuk pemakai yang mematikan animasi.
  var cardTiltOk = !!(window.matchMedia && matchMedia('(hover: hover) and (prefers-reduced-motion: no-preference)').matches);
  $(document).on('mousemove', '.mode-3d .proj-card', function (e) {
    if (!cardTiltOk || draggedProjectId) return;
    var r = this.getBoundingClientRect();
    var px = (e.clientX - r.left) / r.width, py = (e.clientY - r.top) / r.height;
    this.style.setProperty('--rx', ((0.5 - py) * 7).toFixed(2) + 'deg');
    this.style.setProperty('--ry', ((px - 0.5) * 9).toFixed(2) + 'deg');
    this.style.setProperty('--mx', (px * 100).toFixed(1) + '%');
    this.style.setProperty('--my', (py * 100).toFixed(1) + '%');
  });
  $(document).on('mouseleave', '.mode-3d .proj-card', function () {
    this.style.removeProperty('--rx');
    this.style.removeProperty('--ry');
  });

  function updateCategoryList() {
    var counts = {};
    allProjects.forEach(function (p) { if (p.category) counts[p.category] = (counts[p.category] || 0) + 1; });
    var cats = Object.keys(counts);
    // Modul baku yang di data sudah ada dgn huruf besar/kecil berbeda tidak
    // ditambah lagi - ejaan di data yang dipakai, karena filter modul
    // membandingkan persis.
    MODULE_PRESETS.forEach(function (m) {
      if (!cats.some(function (c) { return c.toLowerCase() === m.toLowerCase(); })) cats.push(m);
    });
    cats.sort(function (a, b) { return a.toLowerCase().localeCompare(b.toLowerCase()); });
    moduleCombo.options = cats.map(function (c) { return { name: c, count: counts[c] || 0 }; });
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
    var month = opts.ignoreMonth ? '' : $('#month-filter').val();
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

  // ===== Export List: isi tabel apa adanya =====
  // Dibentuk di BROWSER, bukan di server: yang diekspor adalah baris yang
  // benar-benar sedang tampil, lengkap dgn filter status/module/bulan/pencarian
  // dan urutan kolom yang sedang dipakai. Versi server (ekspor_project.php)
  // tidak tahu-menahu soal urutan kolom di layar.
  function teksPolos(html) {
    var d = document.createElement('div');
    d.innerHTML = html || '';
    return (d.textContent || '').replace(/\s+/g, ' ').trim();
  }

  // Tanggal ditulis sebagai SERIAL Excel (angka), BUKAN objek Date.
  //
  // Memakai `new Date('2026-10-01T00:00:00')` membuat tengah malam WAKTU
  // SETEMPAT (WIB = UTC+7), lalu SheetJS mengubahnya ke serial lewat UTC -
  // hasilnya MUNDUR SATU HARI: 01 Okt jadi 30 Sep. Terbukti saat diuji, dan
  // kesalahan seperti ini tidak kelihatan sampai ada yang mencocokkan tanggal
  // di Excel dgn tanggal di layar.
  //
  // Serialnya dihitung sendiri dari angka tahun/bulan/tanggal lewat Date.UTC,
  // jadi zona waktu browser tidak ikut campur sama sekali. 25569 = selisih
  // hari antara titik nol Excel (1899-12-30) dan titik nol Unix (1970-01-01).
  function tglExcel(s) {
    if (!s || !/^\d{4}-\d{2}-\d{2}$/.test(s)) { return ''; }
    var p = s.split('-');
    var hari = Date.UTC(+p[0], +p[1] - 1, +p[2]) / 86400000;
    return isNaN(hari) ? '' : Math.round(hari) + 25569;
  }

  function exportListExcel() {
    if (typeof XLSX === 'undefined') {
      Swal.fire('Export unavailable', 'The spreadsheet library did not load.', 'error');
      return;
    }
    var list = getListTersortir();
    if (!list.length) {
      Swal.fire('Nothing to export', 'No project matches the current filters.', 'info');
      return;
    }

    var aoa = [[
      'No', 'Module', 'Project', 'Description', 'Priority', 'Req. By',
      'Progress (%)', 'Status', 'Start', 'Target', 'Delivery', 'Actual', 'Live'
    ]];

    list.forEach(function (p, i) {
      // Kolom Delivery di layar berisi lencana ber-HTML ("6d left", "On time",
      // ...). Diambil teksnya saja supaya yang masuk Excel sama dgn yang dibaca
      // user, bukan potongan markup.
      var delivery = isDoneStatus(p.status)
        ? completionBadge(p.target_date, p.actual_date)
        : daysInfo(p.target_date, p.status);

      aoa.push([
        i + 1,
        p.category || 'General',
        p.project_name || '',
        p.description || '',
        p.priority || '',
        p.pic || '',
        parseInt(p.progress) || 0,
        p.status || '',
        tglExcel(p.start_date),
        tglExcel(p.target_date),
        teksPolos(delivery),
        tglExcel(p.actual_date),
        tglExcel(p.live_date)
      ]);
    });

    var ws = XLSX.utils.aoa_to_sheet(aoa);

    // Kolom Start, Target, Actual & Live berisi angka serial - diberi format
    // tanggal supaya Excel MENAMPILKANNYA sebagai tanggal (dan bisa dipakai
    // untuk hitung selisih hari), bukan sebagai angka lima digit.
    [8, 9, 11, 12].forEach(function (kol) {
      for (var r = 1; r < aoa.length; r++) {
        var alamat = XLSX.utils.encode_cell({ r: r, c: kol });
        if (ws[alamat] && ws[alamat].t === 'n') { ws[alamat].z = 'yyyy-mm-dd'; }
      }
    });
    // Lebar kolom dipatok supaya Description tidak tergencet jadi satu huruf.
    ws['!cols'] = [
      { wch: 5 }, { wch: 12 }, { wch: 28 }, { wch: 70 }, { wch: 10 }, { wch: 16 },
      { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 12 }, { wch: 14 }, { wch: 12 }, { wch: 12 }
    ];
    ws['!autofilter'] = { ref: XLSX.utils.encode_range({ s: { r: 0, c: 0 }, e: { r: aoa.length - 1, c: 12 } }) };
    ws['!freeze'] = { xSplit: 0, ySplit: 1 };

    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Project List');

    var t = new Date();
    var cap = t.getFullYear() + ('0' + (t.getMonth() + 1)).slice(-2) + ('0' + t.getDate()).slice(-2)
            + '-' + ('0' + t.getHours()).slice(-2) + ('0' + t.getMinutes()).slice(-2);
    XLSX.writeFile(wb, 'project-list-' + cap + '.xlsx');
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
    refresh3d();
  }

  function emptyStateHtml(icon, title, sub) {
    return '<div class="empty-state"><div class="es-ico"><i class="fa ' + icon + '"></i></div>' +
      '<div class="es-title">' + title + '</div><div class="es-sub">' + sub + '</div></div>';
  }

  // Filter modul yang memang belum punya project sama sekali (mis. Knitting
  // yang baru ditambahkan) diberi ajakan membuat project pertamanya, bukan
  // "No projects found" yang terkesan filternya salah.
  function noResultsHtml(icon) {
    var mod = $('#module-filter').val();
    if (mod && !allProjects.some(function (p) { return p.category === mod; })) {
      var mc = moduleColor(mod);
      return '<div class="empty-state es-module" style="--mc:' + mc.c + ';--mcs:' + mc.s + ';">' +
        '<div class="es-ico"><i class="fa ' + moduleIcon(mod) + '"></i></div>' +
        '<div class="es-title">No ' + escHtml(mod) + ' projects yet</div>' +
        '<div class="es-sub">Add the first one &mdash; the module is filled in for you.</div>' +
        '<button type="button" class="es-btn" onclick="openCreateForModule(\'' + escJs(mod) + '\')"><i class="fa fa-plus"></i>New ' + escHtml(mod) + ' project</button>' +
      '</div>';
    }
    return emptyStateHtml(icon, 'No projects found', 'Try another month, module or status filter.');
  }

  // ===== Shared card renderer =====
  function cardHtml(p, idx, compact) {
    var startTxt  = fmtDate(p.start_date);
    var targetTxt = fmtDate(p.target_date);
    var dateLine  = (startTxt || '?') + ' &rarr; ' + (targetTxt || '?');
    var mc = moduleColor(p.category);
    var shipped = isDoneStatus(p.status);
    var secondaryInfo = shipped ? completionBadge(p.target_date, p.actual_date) : daysInfo(p.target_date, p.status);
    var progress = parseInt(p.progress) || 0;
    // Kartu yang sudah selesai dibuat ringkas: progress bar-nya selalu 100%
    // jadi disembunyikan, deskripsi satu baris, dan tanggal yang ditampilkan
    // tanggal kirim/live - bukan rentang rencana. Planned yang masih 0% juga
    // tanpa progress bar (isinya batang kosong).
    var shipDate = p.status === 'Live' ? (p.live_date || p.actual_date) : p.actual_date;
    if (shipped && shipDate) dateLine = (p.status === 'Live' ? 'Live ' : 'Delivered ') + fmtDate(shipDate);
    var cardCls = 'proj-card proj-card-enter' + (compact ? ' compact' : '') +
      (shipped ? ' is-shipped' : '') + (p.status === 'Planned' && !progress ? ' no-progress' : '');
    var dragAttrs = compact ? ' draggable="true" data-id="' + p.id + '"' : '';
    return '' +
      '<div class="' + cardCls + '"' + dragAttrs + ' onclick="openDrawer(' + p.id + ')" style="--mod-color:' + mc.c + ';--mod-color-soft:' + mc.s + ';animation-delay:' + Math.min((idx||0) * 35, 350) + 'ms;">' +
        '<div class="pc-top">' +
          '<span class="cat"><span class="cat-dot"></span>' + (p.category ? escHtml(p.category) : 'General') + '</span>' +
          '<span class="pc-flags">' + atRiskBadgeHtml(p) + priorityBadgeHtml(p) + '</span>' +
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
          requesterChipHtml(p.pic) +
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
      $('#view-board').html(noResultsHtml('fa-folder-open-o'));
      return;
    }
    var html = '<div class="board-wrap">';
    cols.forEach(function (col) {
      var items = list.filter(function (p) { return p.status === col.key; });
      // Kolom Done/Live terus bertambah panjang: yang paling baru di atas.
      if (isDoneStatus(col.key)) items.sort(function (a, b) {
        return (b.live_date || b.actual_date || '').localeCompare(a.live_date || a.actual_date || '');
      });
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
  // Dipakai BERSAMA oleh tabel di layar dan tombol Export List, supaya isi
  // berkas Excel-nya persis sama dgn yang sedang dilihat - termasuk urutan
  // kolom yang sedang diklik user.
  function getListTersortir() {
    var list = getFiltered().slice();
    list.sort(function (a, b) {
      var av = a[listSortKey] || '', bv = b[listSortKey] || '';
      if (listSortKey === 'progress') { av = parseInt(av); bv = parseInt(bv); }
      return (av > bv ? 1 : av < bv ? -1 : 0) * listSortDir;
    });
    return list;
  }

  function renderList() {
    var list = getListTersortir();
    if (list.length === 0) {
      $('#view-list').html(noResultsHtml('fa-table'));
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
        '<td class="desc-cell" title="' + escJs(p.description || '') + '"><div class="desc-clamp">' + (p.description ? escHtml(p.description) : dash) + '</div>' +
          '<button type="button" class="desc-more" onclick="event.stopPropagation();toggleDesc(this)">more</button></td>' +
        '<td>' + (priorityBadgeHtml(p) || '<span class="prio-plain">' + escHtml(p.priority || '') + '</span>') + '</td>' +
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
    // Tombol "more" hanya untuk deskripsi yang benar-benar terpotong.
    $('#view-list .desc-clamp').each(function () {
      if (this.scrollHeight > this.clientHeight + 1) $(this).parent().addClass('is-clamped');
    });
  }
  function toggleDesc(btn) {
    var cell = $(btn).closest('.desc-cell').toggleClass('expanded');
    $(btn).text(cell.hasClass('expanded') ? 'less' : 'more');
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

    // .cal-scroll: di layar HP grid-nya diberi lebar minimum & digeser ke samping.
    html += '<div class="cal-scroll"><div class="cal-dow-row">';
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

    html += '</div></div>';
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

  // Lead time per modul (hari dari start sampai selesai) - pengganti donat
  // prioritas selama semua project berprioritas sama.
  function leadByModuleCardHtml(doneList) {
    var g = {}, order = [], all = [];
    doneList.forEach(function (p) {
      if (!p.start_date) return;
      var d = diffD(dOnly(p.start_date), dOnly(p.actual_date));
      if (d < 0) return;
      var key = p.category || 'General';
      if (!g[key]) { g[key] = []; order.push(key); }
      g[key].push(d); all.push(d);
    });
    function avg(a) { return a.reduce(function (s, v) { return s + v; }, 0) / a.length; }
    function oneDec(n) { return Math.round(n * 10) / 10; }
    order.sort(function (a, b) { return avg(g[b]) - avg(g[a]); });
    var maxAvg = Math.max.apply(null, order.map(function (k) { return avg(g[k]); }).concat([1]));
    var html = '<div class="chart-card"><div class="cc-head"><div><h5>Lead Time by Module</h5><div class="cc-sub">Average days from start to delivery</div></div></div><div class="cc-body">';
    if (!order.length) html += '<div class="ins-empty">No delivered projects with start &amp; delivery dates.</div>';
    order.forEach(function (key) {
      var mc = moduleColor(key === 'General' ? null : key);
      var a = avg(g[key]), n = g[key].length;
      html += '<div class="bar-chart-row" title="' + n + ' delivered project' + (n !== 1 ? 's' : '') + '"><div class="bar-chart-label"><span class="sw" style="background:' + mc.c + ';"></span>' + escHtml(key) + '</div>' +
        '<div class="bar-chart-track"><div class="bar-chart-fill" style="width:' + Math.max(3, Math.round(a / maxAvg * 100)) + '%;background:' + mc.c + ';"></div></div>' +
        '<div class="bar-chart-value">' + oneDec(a) + '<small>days</small></div></div>';
    });
    html += '</div>';
    if (all.length) html += '<div class="cc-note"><i class="fa fa-bolt"></i>Overall ' + oneDec(avg(all)) + ' days avg &middot; ' + all.length + ' projects measured</div>';
    return html + '</div>';
  }

  function renderInsights() {
    var list = getFiltered();
    var html = '<div class="insights-grid">';
    var todayD = new Date(); todayD.setHours(0, 0, 0, 0);

    // ---- Delivery trend (last 6 months incl. current) ----
    // Grafik 6 bulan TIDAK ikut filter bulan: filter bawaannya bulan ini,
    // jadi dulu lima batang lainnya selalu 0 padahal datanya ada. Filter
    // modul, status & pencarian tetap berlaku.
    var doneList = list.filter(function (p) { return isDoneStatus(p.status) && p.actual_date; });
    var trendDone = getFiltered({ ignoreMonth: true }).filter(function (p) { return isDoneStatus(p.status) && p.actual_date; });
    var monthCounts = {};
    trendDone.forEach(function (p) {
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
      '<div class="cc-head"><div><h5>Delivery Trend</h5><div class="cc-sub">Projects completed per month &middot; last 6 months' + ($('#month-filter').val() ? ' &middot; month filter not applied' : '') + '</div></div>' +
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
    // Donat prioritas cuma berarti kalau prioritasnya beragam. Selama semua
    // project berprioritas sama (saat ini 59 dari 59 Medium), tempatnya
    // dipakai untuk lead time per modul.
    var prioKinds = ['Low', 'Medium', 'High'].filter(function (k) { return prioCounts[k] > 0; }).length;
    if (prioKinds <= 1) {
      html += leadByModuleCardHtml(doneList);
    } else {
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
    }

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
          '<div class="activity-time">' + fmtDate(p.target_date) + (requesterChipHtml(p.pic) ? ' &middot; ' + requesterChipHtml(p.pic) : '') + '</div></div>' +
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

  // ===== Module picker (modal) =====
  // Pengganti <datalist>: daftar bawaan browser itu sama sekali tidak bisa
  // diberi gaya (polos, tanpa warna/ikon modul, beda tiap browser). Isiannya
  // tetap <input id="pj-category"> biasa - modul baru masih bisa diketik
  // bebas, dan semua kode yang membaca .val()-nya tidak perlu diubah.
  // Handler dipasang LANGSUNG di elemennya, bukan didelegasikan ke document:
  // Esc harus dihentikan sebelum sampai ke modal, kalau tidak modalnya ikut tertutup.
  var moduleCombo = { options: [], items: [], active: -1, typed: false };

  function findModuleOption(name) {
    var n = (name || '').trim().toLowerCase();
    if (!n) return null;
    for (var i = 0; i < moduleCombo.options.length; i++) {
      if (moduleCombo.options[i].name.toLowerCase() === n) return moduleCombo.options[i];
    }
    return null;
  }

  function renderModuleCombo() {
    var raw = $('#pj-category').val().trim();
    var q = moduleCombo.typed ? raw.toLowerCase() : '';
    var items = moduleCombo.options.filter(function (o) { return !q || o.name.toLowerCase().indexOf(q) !== -1; });
    if (moduleCombo.typed && raw && !findModuleOption(raw)) items.push({ name: raw, isNew: true });
    moduleCombo.items = items;

    var html = '<div class="mc-head">Modules</div>' + items.map(function (o, i) {
      var act = i === moduleCombo.active ? ' active' : '';
      if (o.isNew) {
        return '<div class="mc-opt mc-new' + act + '" role="option" data-idx="' + i + '">' +
          '<span class="mc-ico"><i class="fa fa-plus"></i></span>' +
          '<span class="mc-name">Add &ldquo;' + escHtml(o.name) + '&rdquo;<small>as a new module</small></span>' +
        '</div>';
      }
      var mc = moduleColor(o.name);
      var sel = o.name.toLowerCase() === raw.toLowerCase();
      return '<div class="mc-opt' + (sel ? ' selected' : '') + act + '" role="option" aria-selected="' + sel + '" data-idx="' + i + '" style="--mc:' + mc.c + ';--mcs:' + mc.s + ';">' +
        '<span class="mc-ico"><i class="fa ' + moduleIcon(o.name) + '"></i></span>' +
        '<span class="mc-name">' + escHtml(o.name) + '<small>' + (o.count ? o.count + ' project' + (o.count !== 1 ? 's' : '') : 'No projects yet') + '</small></span>' +
        '<i class="fa fa-check mc-check"></i>' +
      '</div>';
    }).join('');
    $('#mod-combo-list').html(html);
  }

  function showModuleCombo() {
    renderModuleCombo();
    $('#mod-combo').addClass('open');
    $('#pj-category').attr('aria-expanded', 'true');
  }
  function closeModuleCombo() {
    moduleCombo.typed = false;
    moduleCombo.active = -1;
    $('#mod-combo').removeClass('open');
    $('#pj-category').attr('aria-expanded', 'false');
  }
  function setModuleActive(i) {
    moduleCombo.active = i;
    var el = $('#mod-combo-list .mc-opt').removeClass('active').eq(i).addClass('active')[0];
    if (el) el.scrollIntoView({ block: 'nearest' });
  }
  function pickModule(i) {
    var o = moduleCombo.items[i];
    if (!o) return;
    closeModuleCombo();
    $('#pj-category').val(o.name).trigger('change');
  }

  // Badge di kiri isian: warna + ikon modul yang terpilih. Hanya untuk modul
  // yang dikenal - selama mengetik nama baru badge-nya tetap netral, supaya
  // warnanya tidak berganti-ganti di tiap ketukan tombol.
  function syncModuleComboBadge(cat) {
    var known = findModuleOption(cat);
    var mc = known ? moduleColor(known.name) : null;
    $('#mod-combo').toggleClass('has-module', !!known)
      .css({ '--mc': mc ? mc.c : '', '--mcs': mc ? mc.s : '' });
    $('#mod-combo .mod-combo-ico i').attr('class', 'fa ' + (known ? moduleIcon(known.name) : 'fa-folder-o'));
  }

  $('#pj-category')
    .on('focus click', function () { if (!$('#mod-combo').hasClass('open')) showModuleCombo(); })
    .on('input', function () {
      moduleCombo.typed = true;
      moduleCombo.active = $(this).val().trim() ? 0 : -1;
      showModuleCombo();
    })
    .on('keydown', function (e) {
      var open = $('#mod-combo').hasClass('open');
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        if (!open) showModuleCombo();
        var n = moduleCombo.items.length;
        if (!n) return;
        var a = moduleCombo.active;
        setModuleActive(e.key === 'ArrowDown' ? (a + 1) % n : (a <= 0 ? n - 1 : a - 1));
      } else if (e.key === 'Enter') {
        if (open && moduleCombo.active >= 0) { e.preventDefault(); pickModule(moduleCombo.active); }
      } else if (e.key === 'Escape') {
        if (open) { e.preventDefault(); e.stopPropagation(); closeModuleCombo(); }
      } else if (e.key === 'Tab') {
        closeModuleCombo();
      }
    })
    .on('blur', function () {
      // 'knitting' yang diketik manual disamakan ejaannya dgn modul yang sudah ada.
      var known = findModuleOption($(this).val());
      if (known && known.name !== $(this).val()) $(this).val(known.name).trigger('change');
      closeModuleCombo();
    });
  // mousedown dicegah supaya isian tidak kehilangan fokus (= panel tertutup)
  // sebelum klik pada pilihan sempat diproses.
  $('#mod-combo-panel').on('mousedown', function (e) { e.preventDefault(); });
  $('#mod-combo-list')
    .on('click', '.mc-opt', function () { pickModule(parseInt($(this).attr('data-idx'), 10)); })
    .on('mousemove', '.mc-opt', function () {
      var i = parseInt($(this).attr('data-idx'), 10);
      if (i !== moduleCombo.active) setModuleActive(i);
    });
  $('#mod-combo .mod-combo-caret').on('mousedown', function (e) {
    e.preventDefault();
    if ($('#mod-combo').hasClass('open')) closeModuleCombo();
    else { $('#pj-category').trigger('focus'); showModuleCombo(); }
  });
  $('#modalProject').on('hide.bs.modal', closeModuleCombo);

  // ===== Live preview (modal) =====
  function updatePreview() {
    var cat = $('#pj-category').val().trim();
    var mc = moduleColor(cat);
    syncModuleComboBadge(cat);
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

  function openCreateForModule(mod) {
    openCreate();
    $('#pj-category').val(mod);
    updatePreview();
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
          if (newBook) newBook.saved = true;
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

  // ===== Pintasan keyboard =====
  //   /    langsung ke kotak pencarian
  //   N    New Project
  //   Esc  tutup panel detail
  // Tidak berlaku saat sedang mengetik, dan tidak menyentuh modal/SweetAlert
  // (Esc di modal sudah diurus Bootstrap; target event-nya ada di dalam modal).
  $(document).on('keydown', function (e) {
    if (e.ctrlKey || e.metaKey || e.altKey || e.isComposing) return;
    var t = e.target;
    if ($(t).closest('.modal, .swal2-container').length) return;
    if (e.key === 'Escape') {
      if ($('#detail-drawer').hasClass('show')) closeDrawer();
      return;
    }
    if (/^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName) || t.isContentEditable) return;
    if ($('.modal.show').length || $('#detail-drawer').hasClass('show')) return;
    if (e.key === '/') {
      e.preventDefault();
      $('#search-box').trigger('focus').select();
    } else if (e.key === 'n' || e.key === 'N') {
      e.preventDefault();
      openCreate();
    }
  });

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

  $(function () { syncPerfToggle(); syncModeSwitch(); if (isMode3d()) start3d(false); followBookLink(); loadProjects(); });
</script>

</body>
</html>
