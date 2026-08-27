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
?>

<style>
  :root {
    --ink: #181d1a;
    --ink-soft: #667066;
    --line: #e2e6e0;
    --line-soft: #eef1ec;
    --surface: #ffffff;
    --surface-2: #fbfbf9;
    --accent: #0e7a5f;
    --accent-2: #b45309;
  }
  /* Scoped to .proj-page so the surrounding app shell (sidebar/topbar from
     header.php) is untouched — this toggles the Project workspace only. */
  .proj-page[data-theme="dark"] {
    --ink: #e8ebe7;
    --ink-soft: #96a49a;
    --line: #2b332a;
    --line-soft: #212821;
    --surface: #1a201a;
    --surface-2: #161c16;
    --accent: #34d399;
    --accent-2: #f0a94a;
  }

  .proj-page {
    background:
      radial-gradient(900px circle at 8% -10%, rgba(14,122,95,.08) 0%, rgba(14,122,95,0) 45%),
      radial-gradient(800px circle at 100% 0%, rgba(180,83,9,.06) 0%, rgba(180,83,9,0) 40%),
      #f6f7f3;
    border-radius: 18px;
    padding: 20px;
    transition: background-color .25s ease;
  }
  .proj-page[data-theme="dark"] {
    background:
      radial-gradient(900px circle at 8% -10%, rgba(52,211,153,.10) 0%, rgba(52,211,153,0) 45%),
      radial-gradient(800px circle at 100% 0%, rgba(240,169,74,.07) 0%, rgba(240,169,74,0) 40%),
      #0f130f;
  }
  .theme-toggle {
    position: relative; width: 40px; height: 40px; border-radius: 50%; border: none; cursor: pointer;
    background: rgba(255,255,255,.14); color: #fff; font-size: 15px; display:flex; align-items:center; justify-content:center;
    transition: background .15s ease, transform .15s ease;
  }
  .theme-toggle:hover { background: rgba(255,255,255,.24); transform: translateY(-1px); }

  @keyframes projFadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes projPulse   { 0%,100% { opacity: 1; } 50% { opacity: .55; } }
  @media (prefers-reduced-motion: reduce) { .proj-card-enter, .view-fade-in { animation: none !important; } }
  .view-fade-in { animation: projFadeUp .25s ease both; }

  /* ---------- Confetti (celebrates a project hitting Done) ---------- */
  .confetti-piece { position: fixed; top: -12px; width: 8px; height: 14px; z-index: 9999; pointer-events: none; animation-name: confettiFall; animation-timing-function: linear; animation-fill-mode: forwards; }
  @keyframes confettiFall { to { transform: translateY(105vh) rotate(var(--rot, 720deg)); opacity: .85; } }

  /* ---------- Hero ---------- */
  .proj-hero {
    position: relative; overflow: hidden;
    background: linear-gradient(120deg, #0a1410 0%, #0d3327 42%, #0e7a5f 85%, #34d399 120%);
    border-radius: 16px; color: #fff; padding: 30px 32px;
    box-shadow: 0 14px 34px rgba(11,46,34,.28);
  }
  .proj-hero::before {
    content: ''; position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(rgba(255,255,255,.14) 1px, transparent 1px);
    background-size: 18px 18px; opacity: .5; mask-image: linear-gradient(120deg, #000 10%, transparent 70%);
  }
  .proj-hero::after {
    content: ''; position: absolute; right: -60px; top: -80px; width: 260px; height: 260px; border-radius: 50%; pointer-events: none;
    background: radial-gradient(circle, rgba(255,255,255,.18), rgba(255,255,255,0) 70%);
  }
  .proj-hero .eyebrow { position: relative; font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; opacity: .78; }
  .proj-hero h4 { position: relative; margin: 4px 0 0; font-weight: 800; font-size: 26px; letter-spacing: -.01em; }
  .proj-hero p  { position: relative; margin: 6px 0 0; opacity: .92; font-size: 13.5px; }
  .btn-hero-new {
    position: relative; background: #fff; color: #0b3d2e; font-weight: 800; border: none;
    padding: 10px 20px; border-radius: 999px; box-shadow: 0 8px 20px rgba(0,0,0,.18);
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .btn-hero-new:hover { transform: translateY(-2px); box-shadow: 0 12px 26px rgba(0,0,0,.22); color: #062e22; }

  /* ---------- Stat tiles ---------- */
  .stat-tile {
    position: relative; border-radius: 14px; padding: 16px 17px; height: 100%; overflow: hidden;
    background: var(--surface); border: 1px solid var(--line);
    box-shadow: 0 2px 10px rgba(23,27,46,.05);
    transition: box-shadow .2s ease, transform .2s ease;
    animation: projFadeUp .45s ease both;
  }
  #stat-tiles > div:nth-child(1) .stat-tile { animation-delay: 0ms; }
  #stat-tiles > div:nth-child(2) .stat-tile { animation-delay: 55ms; }
  #stat-tiles > div:nth-child(3) .stat-tile { animation-delay: 110ms; }
  #stat-tiles > div:nth-child(4) .stat-tile { animation-delay: 165ms; }
  #stat-tiles > div:nth-child(5) .stat-tile { animation-delay: 220ms; }
  #stat-tiles > div:nth-child(6) .stat-tile { animation-delay: 275ms; }
  @media (prefers-reduced-motion: reduce) { .stat-tile { animation: none !important; } }
  /* Faint oversized watermark icon + thin gradient accent bar per tile — tinted via --tint/--tint2. */
  .stat-tile::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--tint, #0e7a5f), var(--tint2, #34d399));
  }
  .stat-tile::after {
    font-family: FontAwesome; position: absolute; right: -10px; bottom: -18px; font-size: 68px;
    line-height: 1; opacity: .07; color: var(--tint, #0e7a5f); pointer-events: none;
  }
  .stat-tile:hover { box-shadow: 0 12px 28px -8px var(--tint-a, rgba(23,27,46,.18)), 0 3px 10px rgba(23,27,46,.06); transform: translateY(-3px); }
  .stat-tile .icon-badge {
    position: relative; width: 34px; height: 34px; border-radius: 10px; display:flex; align-items:center; justify-content:center;
    font-size: 14px; color:#fff; margin-bottom: 10px;
    box-shadow: 0 6px 14px -3px var(--tint-a, rgba(0,0,0,.3)), inset 0 1px 0 rgba(255,255,255,.3);
  }
  .stat-tile .num  { font-size: 25px; font-weight: 800; line-height: 1; letter-spacing: -.02em; color: var(--ink); font-variant-numeric: tabular-nums; display:flex; align-items:center; position: relative; }
  .stat-tile .lbl  { font-size: 11px; color: var(--ink-soft); margin-top: 5px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; position: relative; }
  .stat-tile.wm-total    { --tint: #242a26; --tint2: #454f47; --tint-a: rgba(36,42,38,.35); }
  .stat-tile.wm-progress { --tint: #0e7490; --tint2: #22b8cf; --tint-a: rgba(14,116,144,.4); }
  .stat-tile.wm-hold     { --tint: #c07a05; --tint2: #e6a83b; --tint-a: rgba(192,122,5,.4); }
  .stat-tile.wm-done     { --tint: #15803d; --tint2: #22c55e; --tint-a: rgba(21,128,61,.4); }
  .stat-tile.wm-overdue  { --tint: #b91c1c; --tint2: #ef4444; --tint-a: rgba(185,28,28,.4); }
  .stat-tile.wm-total::after    { content: "\f07c"; }
  .stat-tile.wm-progress::after { content: "\f110"; }
  .stat-tile.wm-hold::after     { content: "\f28b"; }
  .stat-tile.wm-done::after     { content: "\f058"; }
  .stat-tile.wm-overdue::after  { content: "\f071"; }
  .ic-total    { background: linear-gradient(135deg,#242a26,#454f47); }
  .ic-progress { background: linear-gradient(135deg,#0e7490,#22b8cf); }
  .ic-hold     { background: linear-gradient(135deg,#c07a05,#e6a83b); }
  .ic-done     { background: linear-gradient(135deg,#15803d,#22c55e); }
  .ic-overdue  { background: linear-gradient(135deg,#b91c1c,#ef4444); }
  .tile-rate {
    background: linear-gradient(135deg,#5b3208 0%,#8a4b0a 50%,#c17f2e 100%);
    color:#fff; display:flex; align-items:center; justify-content:space-between; position:relative;
  }
  .tile-rate::before { background: none; }
  .tile-rate::after  { content: none; }
  .tile-rate .sheen {
    position: absolute; inset: 0; pointer-events: none;
    background-image: radial-gradient(rgba(255,255,255,.18) 1px, transparent 1px);
    background-size: 14px 14px; opacity: .55; mask-image: linear-gradient(120deg, #000 10%, transparent 75%);
  }
  .tile-rate .num, .tile-rate .lbl { color: #fff; position: relative; }
  .ring {
    --deg: 0deg; width: 50px; height: 50px; border-radius: 50%; position: relative; flex-shrink:0;
    background: conic-gradient(#ffd98a var(--deg), rgba(255,255,255,.22) 0);
    display:flex; align-items:center; justify-content:center;
    box-shadow: 0 0 0 3px rgba(255,255,255,.15), 0 4px 14px rgba(0,0,0,.25);
  }
  .ring span {
    width: 40px; height: 40px; border-radius: 50%; background: rgba(43,24,4,.92);
    display:flex; align-items:center; justify-content:center; font-size: 11px; font-weight: 800; color:#ffd98a;
    box-shadow: inset 0 1px 3px rgba(0,0,0,.45);
  }

  .live-dot { display:inline-block; width:7px; height:7px; border-radius:50%; background:#22b8cf; margin-left:7px; animation: livePulse 1.6s ease-in-out infinite; }
  @keyframes livePulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.35; transform:scale(1.5); } }
  #overdue-tile.overdue-alert { animation: overduePulse 2.2s ease-in-out infinite; }
  @keyframes overduePulse { 0%,50%,100% { box-shadow: 0 2px 10px rgba(23,27,46,.05); } 25% { box-shadow: 0 2px 18px rgba(239,68,68,.4); } }
  @media (prefers-reduced-motion: reduce) { .live-dot, #overdue-tile.overdue-alert { animation: none !important; } }

  .stat-scope { font-size: 11.5px; color: var(--ink-soft); font-weight: 600; margin: -6px 0 14px 2px; }
  .stat-scope b { color: var(--ink); font-weight: 800; }

  /* ---------- Toolbar ---------- */
  .proj-toolbar {
    background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 10px 14px;
    box-shadow: 0 2px 8px rgba(23,27,46,.04); position: sticky; top: 8px; z-index: 30;
  }
  .toolbar-row { display:flex; align-items:center; flex-wrap: wrap; gap: 14px; }
  .toolbar-filters { display:flex; align-items:center; flex-wrap: wrap; gap: 8px; }
  .toolbar-actions { display:flex; align-items:center; gap: 10px; margin-left: auto; }
  .proj-filter-pill {
    border: 1px solid var(--line); background: #fff; color: var(--ink-soft);
    border-radius: 999px; padding: 5px 14px; font-size: 12.5px; font-weight: 700; cursor: pointer; user-select: none;
    transition: all .15s ease; white-space: nowrap;
  }
  .proj-filter-pill.active { background: linear-gradient(120deg,#0b3d2e,#0e7a5f); border-color: transparent; color: #fff; box-shadow: 0 4px 12px rgba(14,122,95,.35); }
  .proj-filter-pill:hover { border-color: var(--accent); }

  .search-wrap { position: relative; }
  .search-wrap .fa-search { position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:11px; color: var(--ink-soft); pointer-events:none; }
  .search-wrap input {
    width: 200px; border: 1.5px solid var(--line); border-radius: 999px; padding: 6px 12px 6px 30px; font-size: 12.5px;
    background: var(--surface-2); transition: all .15s ease;
  }
  .search-wrap input:focus { outline:none; border-color: var(--accent); background:#fff; box-shadow: 0 0 0 3px rgba(14,122,95,.12); width: 240px; }

  .btn-export {
    display:inline-flex; align-items:center; gap:7px; white-space: nowrap;
    border: 1.5px solid var(--accent); background: #fff; color: var(--accent);
    font-size: 12.5px; font-weight: 800; padding: 6px 16px; border-radius: 999px; transition: all .15s ease;
  }
  .btn-export:hover { background: linear-gradient(120deg,#0b3d2e,#0e7a5f); border-color: transparent; color: #fff; box-shadow: 0 6px 16px rgba(14,122,95,.35); transform: translateY(-1px); }

  .view-switch { display: inline-flex; background: var(--line-soft); border-radius: 999px; padding: 3px; gap: 2px; }
  .view-switch button {
    border: none; background: transparent; color: var(--ink-soft); font-size: 12.5px; font-weight: 700;
    padding: 6px 14px; border-radius: 999px; cursor: pointer; display:flex; align-items:center; gap:6px; transition: all .15s ease;
  }
  .view-switch button.active { background: #fff; color: var(--accent); box-shadow: 0 2px 8px rgba(23,27,46,.10); }

  .clear-filters-link {
    font-size: 11.5px; font-weight: 700; color: var(--ink-soft); cursor: pointer; white-space: nowrap;
    display: none; align-items: center; gap: 4px; padding: 5px 4px;
  }
  .clear-filters-link:hover { color: #c0261d; }
  .clear-filters-link.show { display: inline-flex; }

  /* ---------- Module summary strip ---------- */
  .module-summary { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 4px; margin-bottom: 14px; }
  .mod-sum-card {
    flex: 0 0 auto; min-width: 168px; background: var(--surface); border: 1px solid var(--line); border-left: 4px solid var(--mc, #0e7a5f);
    border-radius: 10px; padding: 10px 12px; display: flex; align-items: center; gap: 10px;
    box-shadow: 0 1px 4px rgba(23,27,46,.05); cursor: pointer; transition: box-shadow .15s ease, transform .15s ease;
  }
  .mod-sum-card:hover { box-shadow: 0 6px 14px rgba(23,27,46,.12); transform: translateY(-2px); }
  .mod-sum-card.active { outline: 2px solid var(--mc, #0e7a5f); outline-offset: 1px; }
  .mod-sum-icon { width: 32px; height: 32px; border-radius: 9px; background: var(--mcs, #eef1ec); color: var(--mc, #0e7a5f); display:flex; align-items:center; justify-content:center; font-size: 13px; flex-shrink: 0; }
  .mod-sum-name { font-size: 12px; font-weight: 800; color: var(--ink); line-height: 1.2; }
  .mod-sum-meta { font-size: 10.5px; color: var(--ink-soft); font-weight: 600; margin-top: 2px; }
  .mod-sum-ring { margin-left: auto; --deg: 0deg; width: 30px; height: 30px; border-radius: 50%; background: conic-gradient(var(--mc,#0e7a5f) var(--deg), var(--line-soft) 0); display:flex; align-items:center; justify-content:center; flex-shrink: 0; }
  .mod-sum-ring span { width: 22px; height: 22px; border-radius: 50%; background: #fff; font-size: 8.5px; font-weight: 800; color: var(--ink); display:flex; align-items:center; justify-content:center; }

  /* ---------- Loading state ---------- */
  .view-loading { display:flex; flex-direction:column; align-items:center; justify-content:center; padding: 90px 0; color: var(--ink-soft); }
  .view-loading .spinner { width: 34px; height: 34px; border-radius: 50%; border: 3px solid var(--line); border-top-color: var(--accent); animation: projSpin .7s linear infinite; margin-bottom: 12px; }
  @keyframes projSpin { to { transform: rotate(360deg); } }
  @media (prefers-reduced-motion: reduce) { .view-loading .spinner { animation: none; } }

  /* ---------- Card (shared by board/preview) ---------- */
  .proj-card {
    border: 1px solid var(--line); border-top: 3px solid var(--mod-color, #0e7a5f);
    border-radius: 14px; background: var(--surface); padding: 15px 16px; height: 100%;
    box-shadow: 0 2px 8px rgba(23,27,46,.05); transition: box-shadow .18s ease, transform .18s ease;
    display: flex; flex-direction: column;
  }
  .proj-card:hover { box-shadow: 0 14px 26px rgba(23,27,46,.14); transform: translateY(-3px); }
  .proj-card .cat  { font-size: 10px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; color: var(--mod-color,#0e7a5f); background: var(--mod-color-soft,#e6f5ef); padding: 2px 8px; border-radius: 6px; display: inline-block; }
  .proj-card .name { font-size: 15px; font-weight: 800; color: var(--ink); margin: 8px 0 5px; line-height: 1.3; }
  .proj-card .desc { font-size: 12.5px; color: var(--ink-soft); flex-grow: 1; margin-bottom: 10px; line-height: 1.5; display:-webkit-box; -webkit-line-clamp:2; line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .proj-card .meta-row { display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--ink-soft); margin-bottom: 8px; }
  .proj-card .progress { height: 6px; border-radius: 5px; background: var(--line-soft); }
  .proj-card .progress-bar { border-radius: 5px; background-image: linear-gradient(90deg, rgba(255,255,255,.35), rgba(255,255,255,0) 60%); background-blend-mode: overlay; transition: width .4s ease; }
  .proj-card .pct { font-size: 11px; font-weight: 800; color: var(--ink); }
  .card-progress-ring {
    --deg: 0deg; width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    background: conic-gradient(var(--ring-color, var(--accent)) var(--deg), var(--line-soft) 0);
    display:flex; align-items:center; justify-content:center; transition: background .4s ease;
  }
  .card-progress-ring span {
    width: 28px; height: 28px; border-radius: 50%; background: var(--surface);
    display:flex; align-items:center; justify-content:center; font-size: 9.5px; font-weight: 800; color: var(--ink);
    box-shadow: inset 0 0 0 1px var(--line);
  }
  .proj-card.compact .card-progress-ring { width: 30px; height: 30px; }
  .proj-card.compact .card-progress-ring span { width: 23px; height: 23px; font-size: 8.5px; }
  .badge-at-risk { background:#fff4e5; color:#b45309; font-weight:800; border:1px solid #f3c98a; font-size:10px; }
  .badge-priority-High     { background:#fdeaea; color:#c0261d; font-weight:700; }
  .badge-priority-Medium   { background:#fdf2df; color:#a2650a; font-weight:700; }
  .badge-priority-Low      { background:#eef0f5; color:#5a6472; font-weight:700; }
  .badge-status-Planned    { background:#eef0f5; color:#5a6472; font-weight:700; }
  .badge-status-OnProgress { background:#e0f4f7; color:#0e7490; font-weight:700; }
  .badge-status-OnHold     { background:#fdf2df; color:#a2650a; font-weight:700; }
  .badge-status-Done       { background:#e5f7ea; color:#178a41; font-weight:700; }
  .badge-ontime { background:#e5f7ea; color:#178a41; font-weight:700; }
  .badge-late   { background:#fdeaea; color:#c0261d; font-weight:700; }
  .proj-card .footer-row { display:flex; align-items:center; justify-content: space-between; margin-top: 9px; padding-top: 9px; border-top: 1px dashed var(--line); }
  .overdue-flag { color:#c0261d; font-weight:700; }
  .pic-avatar { width:22px; height:22px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:9.5px; font-weight:800; color:#fff; flex-shrink:0; box-shadow: 0 0 0 2px #fff; }
  .pic-chip { display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color: var(--ink-soft); }
  .pic-chip.unassigned { color:#c7cbd3; font-style:italic; font-weight:600; }
  .pic-chip-label { font-size:8.5px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; color: var(--ink-soft); opacity:.7; }
  .pic-chip .pic-avatar { width:20px; height:20px; font-size:9px; }
  .proj-table .pic-chip .pic-avatar { width:18px; height:18px; font-size:8.5px; }
  .tl-row-label .pic-avatar { width:16px; height:16px; font-size:7.5px; box-shadow:none; margin-right:2px; }
  .icon-btn { width:26px; height:26px; border-radius:8px; border:1px solid var(--line); background:#fff; color:var(--ink-soft); display:inline-flex; align-items:center; justify-content:center; font-size:11px; transition:.15s; }
  .icon-btn:hover { background: var(--line-soft); color: var(--ink); }
  .icon-btn.danger:hover { background:#fdeaea; color:#c0261d; border-color:#f3c5c5; }
  .proj-card-enter { animation: projFadeUp .35s ease both; }
  .empty-state { text-align:center; padding: 70px 0; color:#a3abbd; }

  /* Compact variant used inside board columns so long lists stay scannable */
  .proj-card.compact { padding: 11px 13px; border-radius: 11px; }
  .proj-card.compact .cat { font-size: 9.5px; padding: 1.5px 7px; }
  .proj-card.compact .name { font-size: 13.5px; margin: 6px 0 4px; line-height: 1.3; }
  .proj-card.compact .desc { font-size: 11.5px; margin-bottom: 8px; line-height: 1.4; -webkit-line-clamp: 2; line-clamp: 2; }
  .proj-card.compact .meta-row { font-size: 10px; margin-bottom: 6px; }
  .proj-card.compact .progress { height: 5px; }
  .proj-card.compact .pct { font-size: 10px; }
  .proj-card.compact .footer-row { margin-top: 7px; padding-top: 7px; }
  .proj-card.compact .icon-btn { width: 23px; height: 23px; font-size: 10px; }
  .proj-card.compact .pic-chip { font-size: 10px; }
  .proj-card.compact .pic-chip .pic-avatar { width: 18px; height: 18px; font-size: 8.5px; }
  .proj-card.compact:hover { transform: translateY(-2px); }

  /* ---------- Board (kanban) ---------- */
  .board-wrap { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(270px, 1fr); gap: 16px; overflow-x: auto; padding-bottom: 8px; align-items: start; }
  .board-col-head { display:flex; align-items:center; justify-content:space-between; padding: 4px 4px 12px; }
  .board-col-head .title { font-weight: 800; font-size: 13px; color: var(--ink); display:flex; align-items:center; gap:8px; }
  .board-col-head .dot { width:9px; height:9px; border-radius:50%; }
  .board-col-head .count { background: var(--line-soft); color: var(--ink-soft); font-size:11px; font-weight:800; padding:2px 9px; border-radius:999px; }
  .board-col-body {
    display:flex; flex-direction:column; gap: 10px;
    min-height: 120px; max-height: max(360px, calc(100vh - 340px)); overflow-y: auto;
    padding: 2px 6px 6px 2px; margin-right: -6px;
    scrollbar-width: thin; scrollbar-color: var(--line) transparent;
  }
  .board-col-body::-webkit-scrollbar { width: 6px; }
  .board-col-body::-webkit-scrollbar-track { background: transparent; }
  .board-col-body::-webkit-scrollbar-thumb { background: var(--line); border-radius: 999px; }
  .board-col-body::-webkit-scrollbar-thumb:hover { background: #c9cfc5; }
  .board-col-body.drop-target { background: #e6f5ef; outline: 2px dashed var(--accent); outline-offset: -4px; border-radius: 8px; }
  .board-empty-hint { font-size:12px; color:#a3abbd; text-align:center; padding:18px 0; border:1.5px dashed #e7e9f2; border-radius:10px; }
  .proj-card[draggable="true"] { cursor: grab; }
  .proj-card.dragging { opacity: .4; cursor: grabbing; }

  /* ---------- List view (spreadsheet-style grid) ---------- */
  .proj-table-wrap {
    background: var(--surface); border: 1px solid var(--line); border-radius: 12px; overflow: auto;
    max-height: calc(100vh - 300px); min-height: 240px;
    scrollbar-width: thin; scrollbar-color: var(--line) transparent;
  }
  .proj-table-wrap::-webkit-scrollbar { width: 7px; height: 7px; }
  .proj-table-wrap::-webkit-scrollbar-track { background: transparent; }
  .proj-table-wrap::-webkit-scrollbar-thumb { background: var(--line); border-radius: 999px; }
  .proj-table { width:100%; border-collapse: collapse; font-size: 12.5px; }
  .proj-table thead th {
    position: sticky; top: 0; z-index: 5;
    font-size:10px; text-transform:uppercase; letter-spacing:.05em; color: var(--ink-soft); font-weight:800;
    padding: 9px 12px; text-align:left; cursor:pointer; user-select:none; white-space: nowrap;
    background: var(--surface-2); border-bottom: 2px solid var(--line); border-right: 1px solid var(--line-soft);
  }
  .proj-table thead th:hover { color: var(--accent); background: var(--line-soft); }
  .proj-table thead th.no-sort { cursor: default; }
  .proj-table thead th.no-sort:hover { color: var(--ink-soft); background: var(--surface-2); }
  .sort-arrow { font-size: 9px; margin-left: 3px; opacity: .5; }
  .sort-arrow.active { opacity: 1; color: var(--accent); }
  .proj-table tbody td { padding: 7px 12px; color: var(--ink); vertical-align: middle; border-bottom:1px solid var(--line-soft); border-right: 1px solid var(--line-soft); white-space: nowrap; }
  .proj-table tbody tr:nth-child(even) { background: var(--surface-2); }
  .proj-table tbody tr:hover { background: var(--line-soft); }
  .proj-table tbody td.wrap { white-space: normal; }
  .proj-table .mod-tag { display:inline-flex; align-items:center; gap:5px; font-size: 10.5px; font-weight:800; color: var(--mc,#0e7a5f); }
  .proj-table .mod-tag .dot { width:7px; height:7px; border-radius:50%; background: var(--mc,#0e7a5f); flex-shrink:0; }
  .list-progress { width: 70px; height: 6px; border-radius:5px; background: var(--line-soft); display:inline-block; vertical-align:middle; }
  .list-progress .bar { height:100%; border-radius:5px; }
  .status-select {
    border: none; border-radius: 999px; font-size: 10.5px; font-weight: 700; padding: 3px 20px 3px 9px;
    cursor: pointer; appearance: none; -webkit-appearance: none;
    background-repeat: no-repeat; background-position: right 6px center; background-size: 8px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 10 6'%3E%3Cpath fill='%23667066' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
  }
  .status-select:focus { outline: 2px solid var(--accent); outline-offset: 1px; }

  /* ---------- Calendar (multi-day event bars) ---------- */
  .cal-card { background: var(--surface); border:1px solid var(--line); border-radius: 14px; overflow:hidden; box-shadow: 0 2px 8px rgba(23,27,46,.05); }
  .cal-toolbar { display:flex; align-items:center; justify-content:space-between; padding: 14px 18px; border-bottom:1px solid var(--line); }
  .cal-toolbar .title { font-weight:800; font-size:16px; color: var(--ink); }
  .cal-nav { display:flex; align-items:center; gap:6px; }
  .cal-nav button { border:1px solid var(--line); background:#fff; border-radius:8px; color:var(--ink-soft); font-weight:700; transition:.15s; }
  .cal-nav button:hover { background: var(--line-soft); color: var(--ink); }
  .cal-nav .nav-arrow { width:28px; height:28px; font-size:11px; }
  .cal-nav .today-btn { height:28px; padding:0 14px; font-size:11.5px; }

  .cal-dow-row { display:grid; grid-template-columns: repeat(7, 1fr); border-bottom:1px solid var(--line); background:var(--surface-2); }
  .cal-dow { text-align:center; font-size:10px; font-weight:800; color: var(--ink-soft); text-transform:uppercase; letter-spacing:.06em; padding:9px 0; }

  .cal-week { position:relative; display:grid; grid-template-columns: repeat(7, 1fr); border-bottom:1px solid var(--line); }
  .cal-week:last-child { border-bottom:none; }
  .cal-daycell { border-right:1px solid var(--line); padding:6px 8px 8px; }
  .cal-daycell:last-child { border-right:none; }
  .cal-daycell.outside { background:var(--surface-2); }
  .cal-daynum { font-size:11px; font-weight:700; color: var(--ink-soft); text-align:right; }
  .cal-daycell.outside .cal-daynum { color:#c7cbd3; }
  .cal-daynum .today-badge { display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; background: var(--accent); color:#fff; font-weight:800; box-shadow: 0 2px 6px rgba(14,122,95,.4); }

  .cal-week-events { position:absolute; left:0; right:0; top:24px; bottom:4px; display:grid; grid-template-columns: repeat(7, 1fr); grid-auto-rows: 21px; row-gap: 3px; pointer-events:none; }
  .cal-bar {
    pointer-events:auto; align-self:start; margin: 0 3px; padding: 2.5px 8px; border-radius: 6px;
    font-size:10.5px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    cursor:pointer; display:flex; align-items:center; gap:5px; box-shadow: 0 1px 2px rgba(23,27,46,.08);
    transition: transform .1s ease, box-shadow .1s ease;
  }
  .cal-bar:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(23,27,46,.16); }
  .cal-bar.cont-left  { border-top-left-radius:0; border-bottom-left-radius:0; margin-left:0; }
  .cal-bar.cont-right { border-top-right-radius:0; border-bottom-right-radius:0; margin-right:0; }
  .cal-bar .bdot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
  .cal-bar .status-ico { font-size:9px; flex-shrink:0; }

  /* ---------- Status markers (shared by Calendar bars + Timeline bars) ---------- */
  .cal-bar, .tl-bar { border-left: 3px solid transparent; }
  .st-planned    { border-left-color: #8a93a2; }
  .st-onprogress { border-left-color: #0e7490; }
  .st-onhold     { border-left-color: #e0a52c; }
  .st-done       { border-left-color: #22c55e; }
  .st-done { opacity: .62; }
  .st-onhold {
    background-image: repeating-linear-gradient(135deg, rgba(0,0,0,.06) 0 6px, transparent 6px 12px) !important;
  }
  .cal-bar.overdue-bar, .tl-bar.overdue-bar { animation: barOverduePulse 1.8s ease-in-out infinite; }
  @keyframes barOverduePulse {
    0%, 100% { box-shadow: 0 1px 2px rgba(23,27,46,.08); }
    50% { box-shadow: 0 0 0 3px rgba(239,68,68,.5); }
  }
  @media (prefers-reduced-motion: reduce) { .cal-bar.overdue-bar, .tl-bar.overdue-bar { animation:none; } }

  .status-legend { display:flex; align-items:center; gap:16px; flex-wrap:wrap; padding: 9px 18px; border-bottom:1px solid var(--line); background:var(--surface-2); font-size:11px; font-weight:700; color: var(--ink-soft); }
  .status-legend .lg-item { display:flex; align-items:center; gap:6px; }
  .status-legend .lg-bar { width:16px; height:9px; border-radius:2.5px; border-left:3px solid; background: var(--line-soft); display:inline-block; }
  .status-legend .lg-overdue { color:#c0261d; }
  .status-legend .lg-overdue .lg-bar { border-left-color:#ef4444; animation: barOverduePulse 1.8s ease-in-out infinite; }

  /* ---------- Timeline (Gantt-lite) — flex/% based so the grid always fills the card ---------- */
  .tl-card { background: var(--surface); border:1px solid var(--line); border-radius: 14px; overflow:hidden; box-shadow: 0 2px 8px rgba(23,27,46,.05); }
  .tl-scroll { overflow-x: auto; max-height: calc(100vh - 360px); min-height: 220px; }
  .tl-grid { position: relative; width: 100%; }
  .tl-head-row { display:flex; position: sticky; top:0; z-index: 4; background: var(--surface-2); border-bottom: 2px solid var(--line); }
  .tl-head-label {
    flex: 0 0 210px; position: sticky; left:0; z-index: 6; background:var(--surface-2);
    font-size:10.5px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; color: var(--ink-soft);
    padding: 9px 12px; border-right: 1px solid var(--line);
  }
  .tl-head-days { display:flex; flex: 1 1 auto; min-width: 0; }
  .tl-day-col { flex: 1 1 0; min-width: 22px; text-align:center; font-size:9.5px; color: var(--ink-soft); font-weight:700; padding: 9px 0; border-right: 1px solid var(--line-soft); }
  .tl-day-col.weekend { background: var(--line-soft); }
  .tl-day-col.today { color: #fff; background: var(--accent); border-radius: 4px 4px 0 0; }
  .tl-row { display:flex; align-items:center; height: 38px; border-bottom: 1px solid var(--line-soft); }
  .tl-row:hover { background: var(--surface-2); }
  .tl-row-label {
    flex: 0 0 210px; position: sticky; left:0; z-index: 3; background: inherit; background-color: var(--surface);
    padding: 0 12px; display:flex; align-items:center; gap:7px; border-right: 1px solid var(--line);
  }
  .tl-row:hover .tl-row-label { background-color: var(--surface-2); }
  .tl-row-name { font-size: 12px; font-weight: 700; color: var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .tl-row-track { position: relative; flex: 1 1 auto; min-width: 0; height: 100%; }
  .tl-bar {
    position:absolute; top:8px; height:22px; border-radius:6px; cursor:pointer; overflow:hidden;
    box-shadow: 0 1px 3px rgba(23,27,46,.15); transition: transform .1s ease, box-shadow .1s ease;
  }
  .tl-bar:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(23,27,46,.22); z-index: 2; }
  .tl-bar.cont-left  { border-top-left-radius:0; border-bottom-left-radius:0; }
  .tl-bar.cont-right { border-top-right-radius:0; border-bottom-right-radius:0; }
  .tl-bar-fill { position:absolute; top:0; bottom:0; left:0; border-radius: inherit; opacity: .9; }
  .tl-bar-icon { position:relative; z-index:1; height:100%; display:flex; align-items:center; justify-content:center; font-size:10px; color: var(--ink); opacity:.8; }
  .tl-today-line { position:absolute; top:0; bottom:0; width:2px; background: #c0261d; opacity: .55; z-index:1; }

  /* ---------- Insights ---------- */
  .insights-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
  .chart-card { background: var(--surface); border:1px solid var(--line); border-radius: 14px; padding: 16px 18px; box-shadow: 0 2px 8px rgba(23,27,46,.05); }
  .chart-card h5 { font-size: 12.5px; font-weight: 800; text-transform:uppercase; letter-spacing:.03em; color: var(--ink-soft); margin: 0 0 14px; }
  .chart-card.wide { grid-column: 1 / -1; }
  .bar-chart-row { display:flex; align-items:center; gap: 10px; margin-bottom: 10px; }
  .bar-chart-row:last-child { margin-bottom: 0; }
  .bar-chart-label { width: 96px; flex-shrink:0; font-size: 11.5px; font-weight:700; color: var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .bar-chart-track { flex:1; height: 14px; background: var(--line-soft); border-radius: 7px; overflow:hidden; position: relative; }
  .bar-chart-fill { height:100%; border-radius: 7px; transition: width .5s ease; }
  .bar-chart-value { width: 30px; flex-shrink:0; text-align:right; font-size: 11.5px; font-weight:800; color: var(--ink); font-variant-numeric: tabular-nums; }
  .trend-chart { display:flex; align-items:flex-end; gap: 10px; height: 140px; padding-top: 10px; }
  .trend-col { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%; gap:6px; }
  .trend-bar-wrap { flex:1; display:flex; align-items:flex-end; width: 100%; justify-content:center; }
  .trend-bar { width: 60%; max-width: 34px; border-radius: 5px 5px 2px 2px; background: linear-gradient(180deg, #22c55e, #0e7a5f); min-height: 3px; transition: height .5s ease; position: relative; }
  .trend-val { font-size: 10px; font-weight:800; color: var(--ink); }
  .trend-lbl { font-size: 9.5px; color: var(--ink-soft); font-weight:700; }
  .donut-wrap { display:flex; align-items:center; gap: 20px; }
  .donut { --seg: conic-gradient(var(--line-soft) 0 100%); width: 108px; height: 108px; border-radius: 50%; background: var(--seg); flex-shrink:0; display:flex; align-items:center; justify-content:center; }
  .donut span.donut-hole { width: 70px; height: 70px; border-radius:50%; background: var(--surface); display:flex; flex-direction:column; align-items:center; justify-content:center; }
  .donut-hole .num { font-size: 18px; font-weight:800; color: var(--ink); line-height:1; }
  .donut-hole .lbl { font-size: 8.5px; color: var(--ink-soft); font-weight:700; text-transform:uppercase; }
  .legend { display:flex; flex-direction:column; gap: 8px; }
  .legend-item { display:flex; align-items:center; gap: 8px; font-size: 12px; color: var(--ink); }
  .legend-item .sw { width: 10px; height: 10px; border-radius: 3px; flex-shrink:0; }
  .legend-item b { font-variant-numeric: tabular-nums; }
  .activity-list { display:flex; flex-direction:column; gap: 0; max-height: 320px; overflow-y: auto; }
  .activity-item { display:flex; gap: 10px; padding: 10px 4px; border-bottom: 1px dashed var(--line); cursor:pointer; }
  .activity-item:last-child { border-bottom: none; }
  .activity-item:hover { background: var(--line-soft); border-radius: 8px; }
  .activity-dot { width: 9px; height: 9px; border-radius: 50%; margin-top: 5px; flex-shrink:0; }
  .activity-text { font-size: 12.5px; color: var(--ink); line-height: 1.4; }
  .activity-text b { font-weight: 800; }
  .activity-time { font-size: 10.5px; color: var(--ink-soft); font-weight: 600; margin-top: 2px; }

  /* ---------- Detail drawer ---------- */
  .drawer-backdrop {
    position: fixed; inset: 0; background: rgba(10,15,10,.4); z-index: 1040;
    opacity: 0; pointer-events: none; transition: opacity .25s ease;
  }
  .drawer-backdrop.show { opacity: 1; pointer-events: auto; }
  .detail-drawer {
    position: fixed; top: 0; right: 0; bottom: 0; width: min(480px, 100vw);
    background: var(--surface); z-index: 1050; box-shadow: -14px 0 44px rgba(0,0,0,.2);
    transform: translateX(100%); transition: transform .3s cubic-bezier(.2,.8,.2,1);
    display: flex; flex-direction: column;
  }
  .detail-drawer.show { transform: translateX(0); }
  .drawer-head { padding: 22px 50px 20px 22px; border-bottom: 1px solid var(--line); position: relative; flex-shrink: 0; }
  .drawer-close {
    position: absolute; top: 18px; right: 18px; width: 30px; height: 30px; border-radius: 50%;
    border: 1px solid var(--line); background: var(--surface); color: var(--ink-soft);
    display:flex; align-items:center; justify-content:center; cursor:pointer; transition:.15s;
  }
  .drawer-close:hover { background: var(--line-soft); color: var(--ink); }
  .drawer-title { font-size: 17px; font-weight: 800; color: var(--ink); margin: 10px 0 12px; line-height:1.3; }
  .drawer-body { flex: 1 1 auto; overflow-y: auto; padding: 20px 22px; }
  .drawer-section { margin-bottom: 24px; }
  .drawer-section h6 { display:flex; align-items:center; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-soft); margin-bottom: 10px; }
  .drawer-foot { padding: 14px 22px; border-top: 1px solid var(--line); display:flex; gap:10px; justify-content:flex-end; flex-shrink:0; background: var(--line-soft); }
  .drawer-meta-grid { display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 4px; }
  .drawer-meta-item { font-size: 11.5px; }
  .drawer-meta-item .k { color: var(--ink-soft); font-weight:700; text-transform:uppercase; letter-spacing:.03em; font-size:10px; display:block; margin-bottom:3px; }
  .drawer-meta-item .v { color: var(--ink); font-weight:700; }

  .milestone-row { display:flex; align-items:center; gap:10px; padding:7px 0; border-bottom:1px dashed var(--line); }
  .milestone-row:last-child { border-bottom:none; }
  .milestone-check {
    width:19px; height:19px; border-radius:6px; border:1.5px solid var(--line); cursor:pointer;
    display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:.15s; color:#fff; font-size:10.5px;
  }
  .milestone-check.done { background: var(--accent); border-color: var(--accent); }
  .milestone-title { flex:1; font-size:13px; color:var(--ink); cursor:pointer; }
  .milestone-title.done { text-decoration: line-through; color: var(--ink-soft); }
  .milestone-del { color:var(--ink-soft); cursor:pointer; font-size:11px; opacity:0; transition:.15s; padding:2px 4px; }
  .milestone-row:hover .milestone-del { opacity:1; }
  .milestone-del:hover { color:#c0261d; }
  .milestone-add-row { display:flex; gap:8px; margin-top:10px; }
  .milestone-add-row input { flex:1; border:1.5px solid var(--line); border-radius:8px; padding:7px 10px; font-size:12.5px; background: var(--surface); color: var(--ink); }
  .milestone-progress-txt { font-size:10.5px; color:var(--ink-soft); font-weight:700; margin-left:auto; text-transform:none; letter-spacing:0; }
  .drawer-empty { font-size:12px; color:#a3abbd; padding: 6px 0; }

  /* ---------- Modal / Add data (premium) ---------- */
  .modal-project .modal-dialog { max-width: min(1360px, 94vw); width: 100%; }
  .modal-project .modal-content { border:none; border-radius: 18px; overflow:hidden; }
  .modal-project .modal-preview {
    background: linear-gradient(160deg, #0a1a14, #0d3327 55%, #0e7a5f);
    padding: 26px 22px; color:#fff; position:relative; overflow:hidden;
  }
  .modal-project .modal-preview::before {
    content:''; position:absolute; inset:0; background-image: radial-gradient(rgba(255,255,255,.12) 1px, transparent 1px); background-size:16px 16px;
    mask-image: linear-gradient(160deg, #000, transparent 75%);
  }
  .modal-preview .eyebrow { position:relative; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; opacity:.75; margin-bottom: 14px; }
  .modal-preview .prev-card { position:relative; background:#fff; border-radius:14px; padding:16px; color:#1a2233; box-shadow: 0 18px 40px rgba(0,0,0,.35); }
  .modal-preview .prev-tip { position:relative; margin-top:16px; font-size:11.5px; opacity:.85; line-height:1.6; }

  .form-section { margin-bottom: 20px; }
  .form-section .fs-head { display:flex; align-items:center; gap:8px; margin-bottom: 12px; }
  .form-section .fs-head .fs-icon { width:24px; height:24px; border-radius:7px; background: var(--line-soft); color: var(--accent); display:flex; align-items:center; justify-content:center; font-size:11px; }
  .form-section .fs-head .fs-title { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; color: var(--ink-soft); }
  .form-control-modern { border:1.5px solid var(--line); border-radius:10px; font-size:13.5px; padding:9px 12px; height:auto; transition: border-color .15s ease, box-shadow .15s ease; }
  .form-control-modern:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(14,122,95,.14); }
  textarea.form-control-modern { resize: vertical; }
  .field-label { font-size:11.5px; font-weight:800; color: var(--ink-soft); text-transform:uppercase; letter-spacing:.03em; margin-bottom:6px; display:block; }

  .pill-select { display:flex; flex-wrap:wrap; gap:8px; }
  .pill-opt { border:1.5px solid var(--line); background:#fff; color: var(--ink-soft); font-size:12.5px; font-weight:700; padding:7px 15px; border-radius:999px; cursor:pointer; transition: all .15s ease; }
  .pill-opt:hover { border-color: var(--accent); }
  .pill-opt.active[data-value="Planned"]     { background:#eef0f5; border-color:#c7cbd6; color:#3a4152; }
  .pill-opt.active[data-value="On Progress"] { background:#e0f4f7; border-color:#0e7490; color:#0e7490; }
  .pill-opt.active[data-value="On Hold"]     { background:#fdf2df; border-color:#e0a52c; color:#a2650a; }
  .pill-opt.active[data-value="Done"]        { background:#e5f7ea; border-color:#22c55e; color:#178a41; }
  .pill-opt.active[data-value="Low"]         { background:#eef0f5; border-color:#c7cbd6; color:#3a4152; }
  .pill-opt.active[data-value="Medium"]      { background:#fdf2df; border-color:#e0a52c; color:#a2650a; }
  .pill-opt.active[data-value="High"]        { background:#fdeaea; border-color:#ef4444; color:#c0261d; }

  .progress-wrap { display:flex; align-items:center; gap: 14px; }
  .progress-ring-lg { --deg: 0deg; width: 52px; height: 52px; border-radius:50%; background: conic-gradient(var(--accent) var(--deg), var(--line-soft) 0); display:flex; align-items:center; justify-content:center; flex-shrink:0; transition: background .2s ease; }
  .progress-ring-lg span { width:40px; height:40px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color: var(--ink); box-shadow: inset 0 0 0 1px var(--line); }

  .swatch-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px; }

  .modal-footer-modern { background: var(--line-soft); border-top:1px solid var(--line); padding: 14px 22px; }
  .btn-modern-primary { background: linear-gradient(120deg,#0b3d2e,#0e7a5f); border:none; color:#fff; font-weight:700; padding:9px 22px; border-radius:10px; box-shadow: 0 6px 16px rgba(14,122,95,.35); }
  .btn-modern-primary:hover { color:#fff; box-shadow: 0 8px 20px rgba(14,122,95,.45); }
  .btn-modern-ghost { background:#fff; border:1.5px solid var(--line); color: var(--ink-soft); font-weight:700; padding:9px 20px; border-radius:10px; }
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
  <div class="proj-hero d-flex align-items-center justify-content-between flex-wrap" style="gap:14px;">
    <div>
      <div class="eyebrow">Personal Workspace</div>
      <h4><i class="fa fa-rocket mr-2"></i>Project Dashboard</h4>
      <p>Welcome back, <b><?= htmlspecialchars($full_name) ?></b> &mdash; here's where your initiatives stand today.</p>
    </div>
    <div class="d-flex align-items-center" style="gap:10px;">
      <button type="button" class="theme-toggle" id="theme-toggle-btn" onclick="toggleTheme()" title="Toggle dark mode">
        <i class="fa fa-moon-o" id="theme-toggle-icon"></i>
      </button>
      <button type="button" class="btn-hero-new" onclick="openCreate()">
        <i class="fa fa-plus mr-1"></i> New Project
      </button>
    </div>
  </div>

  <!-- Stat tiles -->
  <div class="row mt-3" id="stat-tiles">
    <div class="col-6 col-lg mb-3">
      <div class="stat-tile wm-total">
        <div class="icon-badge ic-total"><i class="fa fa-folder-open"></i></div>
        <div class="num" id="st-total" data-rawval="0">0</div><div class="lbl">Total Projects</div>
      </div>
    </div>
    <div class="col-6 col-lg mb-3">
      <div class="stat-tile wm-progress">
        <div class="icon-badge ic-progress"><i class="fa fa-spinner"></i></div>
        <div class="num" id="st-progress" data-rawval="0">0<span class="live-dot" id="progress-live-dot" style="display:none;"></span></div><div class="lbl">On Progress</div>
      </div>
    </div>
    <div class="col-6 col-lg mb-3">
      <div class="stat-tile wm-hold">
        <div class="icon-badge ic-hold"><i class="fa fa-pause-circle"></i></div>
        <div class="num" id="st-hold" data-rawval="0">0</div><div class="lbl">On Hold</div>
      </div>
    </div>
    <div class="col-6 col-lg mb-3">
      <div class="stat-tile wm-done">
        <div class="icon-badge ic-done"><i class="fa fa-check-circle"></i></div>
        <div class="num" id="st-done" data-rawval="0">0</div><div class="lbl">Done</div>
      </div>
    </div>
    <div class="col-6 col-lg mb-3">
      <div class="stat-tile wm-overdue" id="overdue-tile">
        <div class="icon-badge ic-overdue"><i class="fa fa-exclamation-triangle"></i></div>
        <div class="num" id="st-overdue" data-rawval="0">0</div><div class="lbl">Overdue</div>
      </div>
    </div>
    <div class="col-6 col-lg mb-3">
      <div class="stat-tile tile-rate">
        <span class="sheen"></span>
        <div><div class="num" id="st-rate" data-rawval="0">0%</div><div class="lbl">Completion</div></div>
        <div class="ring" id="rate-ring"><span id="rate-ring-txt">0%</span></div>
      </div>
    </div>
  </div>
  <div class="stat-scope" id="stat-scope">Showing all projects</div>

  <!-- Module summary strip -->
  <div class="module-summary" id="module-summary"></div>

  <!-- Toolbar -->
  <div class="proj-toolbar mb-3">
    <div class="toolbar-row">
      <div class="view-switch">
        <button type="button" class="active" data-view="board" onclick="switchView('board')"><i class="fa fa-columns"></i> Board</button>
        <button type="button" data-view="list" onclick="switchView('list')"><i class="fa fa-table"></i> Table</button>
        <button type="button" data-view="calendar" onclick="switchView('calendar')"><i class="fa fa-calendar"></i> Calendar</button>
        <button type="button" data-view="timeline" onclick="switchView('timeline')"><i class="fa fa-tasks"></i> Timeline</button>
        <button type="button" data-view="insights" onclick="switchView('insights')"><i class="fa fa-pie-chart"></i> Insights</button>
      </div>
      <div class="toolbar-filters">
        <span class="proj-filter-pill active" data-status="" onclick="setFilter('', this)">All</span>
        <span class="proj-filter-pill" data-status="On Progress" onclick="setFilter('On Progress', this)">On Progress</span>
        <span class="proj-filter-pill" data-status="Planned" onclick="setFilter('Planned', this)">Planned</span>
        <span class="proj-filter-pill" data-status="On Hold" onclick="setFilter('On Hold', this)">On Hold</span>
        <span class="proj-filter-pill" data-status="Done" onclick="setFilter('Done', this)">Done</span>
        <select class="form-control form-control-sm" id="module-filter" style="width:auto;" onchange="renderActive()">
          <option value="">All Modules</option>
        </select>
        <select class="form-control form-control-sm" id="month-filter" style="width:auto;" onchange="renderActive()">
          <option value="">All Months</option>
        </select>
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
          <div class="eyebrow" id="modal-proj-eyebrow">New Project</div>
          <div class="prev-card">
            <span class="cat" id="pv-cat">General</span>
            <div class="name" id="pv-name" style="margin-top:8px;">Untitled project</div>
            <div class="desc" id="pv-desc" style="-webkit-line-clamp:3;line-clamp:3;">No description yet.</div>
            <div class="meta-row"><span id="pv-dates"><i class="fa fa-calendar mr-1"></i>? &rarr; ?</span></div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="pct" id="pv-pct">0%</span>
              <span class="badge badge-status-Planned" id="pv-status-badge">Planned</span>
            </div>
            <div class="progress"><div class="progress-bar" id="pv-progress-bar" style="width:0%;background:#8a93a2;"></div></div>
            <div class="footer-row" style="justify-content:space-between;">
              <span id="pv-pic"></span>
              <span class="badge badge-priority-Medium" id="pv-priority-badge">Medium</span>
            </div>
          </div>
          <div class="prev-tip"><i class="fa fa-magic mr-1"></i> This preview updates live as you fill the form — what you see is what lands on the board.</div>
        </div>

        <!-- Form -->
        <div class="col-md-8">
          <div class="modal-body p-4 p-lg-5" style="max-height:84vh; overflow-y:auto;">
            <input type="hidden" id="pj-id">

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
              <div class="form-group mb-2">
                <label class="field-label">Status</label>
                <div class="pill-select" id="pill-status">
                  <button type="button" class="pill-opt active" data-value="Planned">Planned</button>
                  <button type="button" class="pill-opt" data-value="On Progress">On Progress</button>
                  <button type="button" class="pill-opt" data-value="On Hold">On Hold</button>
                  <button type="button" class="pill-opt" data-value="Done">Done</button>
                </div>
                <input type="hidden" id="pj-status" value="Planned">
              </div>
              <div class="form-group mb-0">
                <label class="field-label">Priority</label>
                <div class="pill-select" id="pill-priority">
                  <button type="button" class="pill-opt" data-value="Low">Low</button>
                  <button type="button" class="pill-opt active" data-value="Medium">Medium</button>
                  <button type="button" class="pill-opt" data-value="High">High</button>
                </div>
                <input type="hidden" id="pj-priority" value="Medium">
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
                <label class="field-label">Actual Completion Date</label>
                <input type="date" class="form-control form-control-modern" id="pj-actual" style="max-width:220px;">
                <small class="text-muted d-block mt-1">Used to flag on-time vs late delivery.</small>
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
      <div id="drawer-desc" style="font-size:13px;color:var(--ink);line-height:1.6;"></div>
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
    <button type="button" class="btn-modern-ghost" onclick="doDelete(drawerProjectId, drawerProjectName); closeDrawer();">Delete</button>
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

  var allProjects  = [];
  var activeFilter = '';
  var currentView  = 'board';
  var calYear, calMonth; // 0-based month
  var MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

  var STATUS_COLS = [
    { key: 'Planned',     dot: '#8a93a2' },
    { key: 'On Progress', dot: '#0e7490' },
    { key: 'On Hold',     dot: '#e0a52c' },
    { key: 'Done',        dot: '#22c55e' }
  ];

  // Status marker language shared by Calendar bars + Timeline bars: a left border
  // stripe + small icon so status reads at a glance without losing the module color
  // that already tints the bar's background.
  var STATUS_META = {
    'Planned':     { cls: 'st-planned',    icon: 'fa-circle-o',     dot: '#8a93a2' },
    'On Progress': { cls: 'st-onprogress', icon: 'fa-play-circle',  dot: '#0e7490' },
    'On Hold':     { cls: 'st-onhold',     icon: 'fa-pause-circle', dot: '#e0a52c' },
    'Done':        { cls: 'st-done',       icon: 'fa-check-circle', dot: '#22c55e' }
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

  // ===== Module color palette =====
  var MODULE_COLORS = {
    'NDS':       { c: '#7c3aed', s: '#f1ebff' },
    'AP':        { c: '#be123c', s: '#fde8ec' },
    'AR':        { c: '#0d9488', s: '#e6f7f5' },
    'Signalbit': { c: '#b8860b', s: '#fbf3df' }
  };
  var FALLBACK_PALETTE = [
    { c: '#0e7a5f', s: '#e6f5ef' }, { c: '#0ea5e9', s: '#e6f6fd' },
    { c: '#f97316', s: '#fef0e6' }, { c: '#059669', s: '#e2f6ef' },
    { c: '#8b5cf6', s: '#f0ebfe' }
  ];
  function moduleColor(cat) {
    if (!cat) return { c: '#8a93a2', s: '#eef0f3' };
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

  // ===== PIC / assignee avatars =====
  function picInitials(name) {
    if (!name) return '?';
    var parts = name.trim().split(/\s+/);
    return (parts[0][0] + (parts.length > 1 ? parts[parts.length - 1][0] : '')).toUpperCase();
  }
  function picColor(name) {
    if (!name) return { c: '#c7cbd3', s: '#f3f4f6' };
    var hash = 0;
    for (var i = 0; i < name.length; i++) hash = (hash * 31 + name.charCodeAt(i)) >>> 0;
    return FALLBACK_PALETTE[hash % FALLBACK_PALETTE.length];
  }
  function picChipHtml(name) {
    if (!name) return '<span class="pic-chip unassigned"><i class="fa fa-question-circle-o"></i> No requester</span>';
    var pc = picColor(name);
    return '<span class="pic-chip" title="Requested by ' + escJs(name) + '">' +
      '<span class="pic-chip-label">Req:</span>' +
      '<span class="pic-avatar" style="background:' + pc.c + ';">' + picInitials(name) + '</span>' + escHtml(name) +
    '</span>';
  }

  function statusClass(s) { return 'badge-status-' + s.replace(/\s+/g, ''); }
  function progressColor(s) {
    if (s === 'Done') return '#22c55e';
    if (s === 'On Progress') return '#0e7490';
    if (s === 'On Hold') return '#e0a52c';
    return '#8a93a2';
  }
  function fmtDate(d, opts) {
    if (!d) return null;
    var dt = new Date(d + 'T00:00:00');
    if (isNaN(dt)) return null;
    return dt.toLocaleDateString('en-US', opts || { day: '2-digit', month: 'short', year: 'numeric' });
  }
  function daysInfo(target, status) {
    if (!target || status === 'Done') return '';
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
    var c = { 'On Progress': 0, 'On Hold': 0, 'Done': 0 };
    var overdue = 0;
    list.forEach(function (p) {
      if (c[p.status] !== undefined) c[p.status]++;
      if (parseInt(p.is_overdue) === 1) overdue++;
    });
    var rate = total > 0 ? Math.round((c['Done'] / total) * 100) : 0;

    animateNumber('st-total', total);
    animateNumber('st-progress', c['On Progress'], function (v) {
      document.getElementById('st-progress').firstChild.textContent = v;
    });
    animateNumber('st-hold', c['On Hold']);
    animateNumber('st-done', c['Done']);
    animateNumber('st-overdue', overdue);
    animateNumber('st-rate', rate, function (v) {
      document.getElementById('st-rate').textContent = v + '%';
      $('#rate-ring-txt').text(v + '%');
      $('#rate-ring').css('--deg', (v * 3.6) + 'deg');
    });

    $('#progress-live-dot').toggle(c['On Progress'] > 0);
    $('#overdue-tile').toggleClass('overdue-alert', overdue > 0);

    updateStatScope(total);
  }

  function updateStatScope(total) {
    var parts = [];
    if ($('#month-filter').val()) parts.push($('#month-filter option:selected').text().replace(' (this month)', ''));
    if (activeFilter) parts.push(activeFilter);
    if ($('#module-filter').val()) parts.push($('#module-filter').val());
    if ($('#search-box').val()) parts.push('"' + $('#search-box').val() + '"');
    var scope = parts.length ? parts.join(' · ') : 'All time';
    $('#stat-scope').html('Showing <b>' + total + '</b> project' + (total !== 1 ? 's' : '') + ' &mdash; ' + scope);
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

  function setFilter(status, el) {
    activeFilter = status;
    $('.proj-filter-pill').removeClass('active');
    $(el).addClass('active');
    renderActive();
  }

  function setModuleFilter(mod) {
    $('#module-filter').val(mod);
    renderActive();
  }

  function clearFilters() {
    activeFilter = '';
    $('.proj-filter-pill').removeClass('active');
    $('.proj-filter-pill[data-status=""]').addClass('active');
    $('#module-filter').val('');
    $('#month-filter').val('');
    $('#search-box').val('');
    renderActive();
  }

  function updateClearFiltersVisibility() {
    var anyActive = !!activeFilter || !!$('#module-filter').val() || !!$('#month-filter').val() || !!$('#search-box').val();
    $('#clear-filters').toggleClass('show', anyActive);
  }

  function getFiltered(opts) {
    opts = opts || {};
    var q = ($('#search-box').val() || '').toLowerCase();
    var mod = opts.ignoreModule ? '' : $('#module-filter').val();
    var month = $('#month-filter').val();
    return allProjects.filter(function (p) {
      if (activeFilter && p.status !== activeFilter) return false;
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
      return '<div class="mod-sum-card' + (isActive ? ' active' : '') + '" style="--mc:' + mc.c + ';--mcs:' + mc.s + ';" onclick="setModuleFilter(\'' + (isActive ? '' : escJs(key)) + '\')">' +
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
      status: activeFilter,
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

  // ===== Shared card renderer =====
  function cardHtml(p, idx, compact) {
    var startTxt  = fmtDate(p.start_date);
    var targetTxt = fmtDate(p.target_date);
    var dateLine  = (startTxt || '?') + ' &rarr; ' + (targetTxt || '?');
    var mc = moduleColor(p.category);
    var secondaryInfo = p.status === 'Done' ? completionBadge(p.target_date, p.actual_date) : daysInfo(p.target_date, p.status);
    var cardCls = 'proj-card proj-card-enter' + (compact ? ' compact' : '');
    var dragAttrs = compact ? ' draggable="true" data-id="' + p.id + '"' : '';
    return '' +
      '<div class="' + cardCls + '"' + dragAttrs + ' onclick="openDrawer(' + p.id + ')" style="--mod-color:' + mc.c + ';--mod-color-soft:' + mc.s + ';animation-delay:' + Math.min((idx||0) * 35, 350) + 'ms;">' +
        '<div class="d-flex justify-content-between align-items-start">' +
          '<span class="cat">' + (p.category ? escHtml(p.category) : 'General') + '</span>' +
          '<span>' + atRiskBadgeHtml(p) + ' <span class="badge badge-priority-' + p.priority + '">' + p.priority + '</span></span>' +
        '</div>' +
        '<div class="name">' + escHtml(p.project_name) + '</div>' +
        '<div class="desc">' + (p.description ? escHtml(p.description) : '<span class=\'text-muted\'>No description.</span>') + '</div>' +
        '<div class="meta-row"><span><i class="fa fa-calendar mr-1"></i>' + dateLine + '</span><span>' + secondaryInfo + '</span></div>' +
        '<div class="d-flex justify-content-between align-items-center">' +
          '<div class="card-progress-ring" style="--deg:' + (p.progress * 3.6) + 'deg;--ring-color:' + progressColor(p.status) + ';"><span>' + p.progress + '%</span></div>' +
          '<span class="badge ' + statusClass(p.status) + '">' + p.status + '</span>' +
        '</div>' +
        '<div class="footer-row">' +
          picChipHtml(p.pic) +
          '<span>' +
            '<button class="icon-btn mr-1" onclick="event.stopPropagation();openEdit(' + p.id + ')" title="Edit"><i class="fa fa-pencil"></i></button>' +
            '<button class="icon-btn danger" onclick="event.stopPropagation();doDelete(' + p.id + ',\'' + escJs(p.project_name) + '\')" title="Delete"><i class="fa fa-trash"></i></button>' +
          '</span>' +
        '</div>' +
      '</div>';
  }

  // ===== Board (kanban) =====
  function renderBoard() {
    var list = getFiltered();
    var cols = activeFilter ? STATUS_COLS.filter(function (c) { return c.key === activeFilter; }) : STATUS_COLS;
    if (list.length === 0) {
      $('#view-board').html('<div class="empty-state"><i class="fa fa-folder-open-o" style="font-size:36px;"></i><div class="mt-2">No projects found.</div></div>');
      return;
    }
    var html = '<div class="board-wrap">';
    cols.forEach(function (col) {
      var items = list.filter(function (p) { return p.status === col.key; });
      html += '<div><div class="board-col-head"><span class="title"><span class="dot" style="background:' + col.dot + ';"></span>' + col.key + '</span><span class="count">' + items.length + '</span></div><div class="board-col-body" data-status="' + col.key + '">';
      if (items.length === 0) {
        html += '<div class="board-empty-hint">Drop here</div>';
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
  var STATUS_BG = {
    'Planned':     { bg: '#eef0f5', fg: '#5a6472' },
    'On Progress': { bg: '#e0f4f7', fg: '#0e7490' },
    'On Hold':     { bg: '#fdf2df', fg: '#a2650a' },
    'Done':        { bg: '#e5f7ea', fg: '#178a41' }
  };
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
      $('#view-list').html('<div class="empty-state"><i class="fa fa-folder-open-o" style="font-size:36px;"></i><div class="mt-2">No projects found.</div></div>');
      return;
    }
    var html = '<div class="proj-table-wrap"><table class="proj-table"><thead><tr>' +
      '<th class="no-sort">No</th>' +
      '<th onclick="listSort(\'category\')">Module' + sortArrowHtml('category') + '</th>' +
      '<th onclick="listSort(\'project_name\')">Project' + sortArrowHtml('project_name') + '</th>' +
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
      var secondary = (p.status === 'Done' ? completionBadge(p.target_date, p.actual_date) : daysInfo(p.target_date, p.status)) +
        (isAtRisk(p) ? ' ' + atRiskBadgeHtml(p) : '');
      var sc = STATUS_BG[p.status] || STATUS_BG['Planned'];
      var statusOpts = ['Planned', 'On Progress', 'On Hold', 'Done'].map(function (s) {
        return '<option value="' + s + '"' + (s === p.status ? ' selected' : '') + '>' + s + '</option>';
      }).join('');
      html += '<tr style="cursor:pointer;" onclick="openDrawer(' + p.id + ')">' +
        '<td>' + (idx + 1) + '</td>' +
        '<td><span class="mod-tag" style="--mc:' + mc.c + ';"><span class="dot"></span>' + (p.category ? escHtml(p.category) : 'General') + '</span></td>' +
        '<td class="wrap" title="' + escJs(p.description || '') + '"><b>' + escHtml(p.project_name) + '</b></td>' +
        '<td><span class="badge badge-priority-' + p.priority + '">' + p.priority + '</span></td>' +
        '<td>' + picChipHtml(p.pic) + '</td>' +
        '<td><span class="list-progress"><span class="bar" style="width:' + p.progress + '%;background:' + progressColor(p.status) + ';display:block;"></span></span> <span style="font-size:11px;font-weight:700;">' + p.progress + '%</span></td>' +
        '<td onclick="event.stopPropagation();"><select class="status-select" style="background-color:' + sc.bg + ';color:' + sc.fg + ';" onchange="quickStatusChange(' + p.id + ', this.value)">' + statusOpts + '</select></td>' +
        '<td>' + (fmtDate(p.start_date) || '-') + '</td>' +
        '<td>' + (fmtDate(p.target_date) || '-') + '</td>' +
        '<td>' + (secondary || '<span class="text-muted">&mdash;</span>') + '</td>' +
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
    renderCalendar();
  }
  function calToday() {
    var now = new Date(); calYear = now.getFullYear(); calMonth = now.getMonth();
    renderCalendar();
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
    var numWeeks  = Math.round(diffD(gridStart, gridEnd) / 7) + 1;
    var today = new Date(); today.setHours(0,0,0,0);
    var todayKey = dKey(today);

    // Normalize each project into a clipped [start,end] range within the visible grid.
    var ranges = list.map(function (p) {
      var end = dOnly(p.target_date);
      var start = p.start_date ? dOnly(p.start_date) : end;
      if (start > end) start = end;
      return { p: p, start: start, end: end };
    }).filter(function (r) { return r.end >= gridStart && r.start <= gridEnd; });

    var html = '<div class="cal-card">' +
      '<div class="cal-toolbar">' +
        '<div class="title">' + MONTH_NAMES[calMonth] + ' ' + calYear + '</div>' +
        '<div class="cal-nav">' +
          '<button class="nav-arrow" onclick="calNav(-1)"><i class="fa fa-chevron-left"></i></button>' +
          '<button class="today-btn" onclick="calToday()">Today</button>' +
          '<button class="nav-arrow" onclick="calNav(1)"><i class="fa fa-chevron-right"></i></button>' +
        '</div>' +
      '</div>' +
      statusLegendHtml() +
      '<div class="cal-dow-row">';
    ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(function (d) { html += '<div class="cal-dow">' + d + '</div>'; });
    html += '</div>';

    for (var w = 0; w < numWeeks; w++) {
      var weekStart = addD(gridStart, w * 7);
      var weekEnd   = addD(weekStart, 6);

      // Build this week's bar segments (clip each project range to the week).
      var segs = [];
      ranges.forEach(function (r) {
        var segStart = r.start > weekStart ? r.start : weekStart;
        var segEnd   = r.end < weekEnd ? r.end : weekEnd;
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
        var isToday = dKey(cellDate) === todayKey;
        html += '<div class="cal-daycell' + (outside ? ' outside' : '') + '"><div class="cal-daynum">' +
          (isToday ? '<span class="today-badge">' + cellDate.getDate() + '</span>' : cellDate.getDate()) +
          '</div></div>';
      }
      html += '<div class="cal-week-events">';
      segs.forEach(function (seg) {
        var mc = moduleColor(seg.p.category);
        var sm = statusMeta(seg.p.status);
        var overdue = parseInt(seg.p.is_overdue) === 1;
        var cls = 'cal-bar ' + sm.cls + (overdue ? ' overdue-bar' : '') + (seg.contLeft ? ' cont-left' : '') + (seg.contRight ? ' cont-right' : '');
        html += '<span class="' + cls + '" style="grid-column:' + (seg.startCol + 1) + ' / ' + (seg.endCol + 2) + ';grid-row:' + (seg.lane + 1) + ';background:' + mc.s + ';color:' + mc.c + ';" onclick="openEdit(' + seg.p.id + ')" title="' + escJs(seg.p.project_name + ' — ' + seg.p.status + (overdue ? ' (overdue)' : (isAtRisk(seg.p) ? ' (at risk)' : ''))) + '">' +
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
      html += '<div class="tl-day-col' + (isWeekend ? ' weekend' : '') + (isToday ? ' today' : '') + '">' + d + '</div>';
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

    // ---- Completion trend (last 6 months incl. current) ----
    var doneList = list.filter(function (p) { return p.status === 'Done' && p.actual_date; });
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
    var maxCount = Math.max.apply(null, trendMonths.map(function (m) { return monthCounts[m] || 0; }).concat([1]));
    html += '<div class="chart-card wide"><h5>Completion Trend — Last 6 Months</h5><div class="trend-chart">';
    trendMonths.forEach(function (m) {
      var c = monthCounts[m] || 0;
      var h = Math.round((c / maxCount) * 100);
      var parts = m.split('-');
      html += '<div class="trend-col">' +
        '<div class="trend-val">' + c + '</div>' +
        '<div class="trend-bar-wrap"><div class="trend-bar" style="height:' + Math.max(h, 3) + '%;" title="' + c + ' completed"></div></div>' +
        '<div class="trend-lbl">' + MONTH_NAMES[parseInt(parts[1], 10) - 1].substring(0, 3) + '</div>' +
      '</div>';
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
      ? buildDonutGradient([{ pct: (onTime / totalDone) * 100, color: '#22c55e' }, { pct: (late / totalDone) * 100, color: '#ef4444' }])
      : 'conic-gradient(var(--line-soft) 0 100%)';
    html += '<div class="chart-card"><h5>On-time Delivery</h5><div class="donut-wrap">' +
      '<div class="donut" style="background:' + otGrad + ';"><span class="donut-hole"><span class="num">' + onTimePct + '%</span><span class="lbl">On time</span></span></div>' +
      '<div class="legend">' +
        '<div class="legend-item"><span class="sw" style="background:#22c55e;"></span>On time<b class="ml-auto">' + onTime + '</b></div>' +
        '<div class="legend-item"><span class="sw" style="background:#ef4444;"></span>Late<b class="ml-auto">' + late + '</b></div>' +
      '</div>' +
    '</div></div>';

    // ---- Workload by module ----
    var modGroups = {}, modOrder = [];
    list.forEach(function (p) {
      var key = p.category || 'General';
      if (!modGroups[key]) { modGroups[key] = 0; modOrder.push(key); }
      modGroups[key]++;
    });
    modOrder.sort(function (a, b) { return modGroups[b] - modGroups[a]; });
    var maxMod = Math.max.apply(null, modOrder.map(function (k) { return modGroups[k]; }).concat([1]));
    html += '<div class="chart-card"><h5>Workload by Module</h5>';
    if (modOrder.length === 0) html += '<div class="text-muted" style="font-size:12.5px;">No data.</div>';
    modOrder.forEach(function (key) {
      var mc = moduleColor(key === 'General' ? null : key);
      var pct = Math.round((modGroups[key] / maxMod) * 100);
      html += '<div class="bar-chart-row"><div class="bar-chart-label">' + escHtml(key) + '</div>' +
        '<div class="bar-chart-track"><div class="bar-chart-fill" style="width:' + pct + '%;background:' + mc.c + ';"></div></div>' +
        '<div class="bar-chart-value">' + modGroups[key] + '</div></div>';
    });
    html += '</div>';

    // ---- Requests by Requester (who's asking for the most projects, all statuses) ----
    var picGroups = {}, picOrder = [];
    list.forEach(function (p) {
      var key = p.pic || 'No requester';
      if (!picGroups[key]) { picGroups[key] = 0; picOrder.push(key); }
      picGroups[key]++;
    });
    picOrder.sort(function (a, b) { return picGroups[b] - picGroups[a]; });
    var maxPic = Math.max.apply(null, picOrder.map(function (k) { return picGroups[k]; }).concat([1]));
    html += '<div class="chart-card"><h5>Requests by Requester</h5>';
    if (picOrder.length === 0) html += '<div class="text-muted" style="font-size:12.5px;">No data.</div>';
    picOrder.forEach(function (key) {
      var pc = key === 'No requester' ? { c: '#c7cbd3' } : picColor(key);
      var pct = Math.round((picGroups[key] / maxPic) * 100);
      html += '<div class="bar-chart-row"><div class="bar-chart-label">' + escHtml(key) + '</div>' +
        '<div class="bar-chart-track"><div class="bar-chart-fill" style="width:' + pct + '%;background:' + pc.c + ';"></div></div>' +
        '<div class="bar-chart-value">' + picGroups[key] + '</div></div>';
    });
    html += '</div>';

    // ---- Upcoming deadlines (next 7 days, not yet Done) ----
    var todayD = new Date(); todayD.setHours(0, 0, 0, 0);
    var horizon = addD(todayD, 7);
    var upcoming = list.filter(function (p) {
      if (p.status === 'Done' || !p.target_date) return false;
      var t = dOnly(p.target_date);
      return t <= horizon;
    }).sort(function (a, b) { return a.target_date.localeCompare(b.target_date); });
    html += '<div class="chart-card wide"><h5>Upcoming Deadlines — Next 7 Days</h5><div class="activity-list">';
    if (upcoming.length === 0) {
      html += '<div class="text-muted" style="font-size:12.5px;">Nothing due in the next 7 days.</div>';
    } else {
      upcoming.forEach(function (p) {
        var mc = moduleColor(p.category);
        var t = dOnly(p.target_date);
        var diff = Math.round((t - todayD) / 86400000);
        var when = diff < 0 ? '<span class="overdue-flag">' + Math.abs(diff) + 'd overdue</span>'
          : diff === 0 ? '<span class="overdue-flag">Due today</span>'
          : diff + 'd left';
        html += '<div class="activity-item" onclick="openEdit(' + p.id + ')">' +
          '<span class="activity-dot" style="background:' + mc.c + ';"></span>' +
          '<div><div class="activity-text"><b>' + escHtml(p.project_name) + '</b> &middot; ' + fmtDate(p.target_date) + ' &middot; ' + when + '</div>' +
          '<div class="activity-time">' + picChipHtml(p.pic) + '</div></div>' +
        '</div>';
      });
    }
    html += '</div></div>';

    // ---- Priority mix donut ----
    var prioCounts = { Low: 0, Medium: 0, High: 0 };
    list.forEach(function (p) { if (prioCounts[p.priority] !== undefined) prioCounts[p.priority]++; });
    var totalPrio = prioCounts.Low + prioCounts.Medium + prioCounts.High;
    var prioColors = { Low: '#8a93a2', Medium: '#e0a52c', High: '#ef4444' };
    var prioGrad = totalPrio
      ? buildDonutGradient([
          { pct: (prioCounts.Low / totalPrio) * 100, color: prioColors.Low },
          { pct: (prioCounts.Medium / totalPrio) * 100, color: prioColors.Medium },
          { pct: (prioCounts.High / totalPrio) * 100, color: prioColors.High }
        ])
      : 'conic-gradient(var(--line-soft) 0 100%)';
    html += '<div class="chart-card"><h5>Priority Mix</h5><div class="donut-wrap">' +
      '<div class="donut" style="background:' + prioGrad + ';"><span class="donut-hole"><span class="num">' + totalPrio + '</span><span class="lbl">Total</span></span></div>' +
      '<div class="legend">' +
        '<div class="legend-item"><span class="sw" style="background:' + prioColors.Low + ';"></span>Low<b class="ml-auto">' + prioCounts.Low + '</b></div>' +
        '<div class="legend-item"><span class="sw" style="background:' + prioColors.Medium + ';"></span>Medium<b class="ml-auto">' + prioCounts.Medium + '</b></div>' +
        '<div class="legend-item"><span class="sw" style="background:' + prioColors.High + ';"></span>High<b class="ml-auto">' + prioCounts.High + '</b></div>' +
      '</div>' +
    '</div></div>';

    // ---- Recent activity ----
    var recent = list.slice().sort(function (a, b) {
      var ad = a.updated_date || a.created_date || '';
      var bd = b.updated_date || b.created_date || '';
      return bd.localeCompare(ad);
    }).slice(0, 8);
    html += '<div class="chart-card wide"><h5>Recent Activity</h5><div class="activity-list">';
    if (recent.length === 0) {
      html += '<div class="text-muted" style="font-size:12.5px;">No activity yet.</div>';
    } else {
      recent.forEach(function (p) {
        var mc = moduleColor(p.category);
        var isNew = !p.updated_date;
        var ts = p.updated_date || p.created_date;
        html += '<div class="activity-item" onclick="openEdit(' + p.id + ')">' +
          '<span class="activity-dot" style="background:' + mc.c + ';"></span>' +
          '<div><div class="activity-text">' + (isNew ? 'Created ' : 'Updated ') + '<b>' + escHtml(p.project_name) + '</b> &middot; ' + statusBadgeInline(p.status) + '</div>' +
          '<div class="activity-time">' + relativeTime(ts) + '</div></div>' +
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
  $(document).on('input change', '#pj-name, #pj-category, #pj-pic, #pj-description, #pj-start, #pj-target, #pj-actual', updatePreview);

  function onStatusChange() {
    var status = $('#pj-status').val();
    $('#pj-actual-row').toggle(status === 'Done');
    if (status === 'Done') syncProgress(100);
  }

  // ===== OPEN CREATE =====
  function openCreate() {
    $('#modal-proj-eyebrow').text('New Project');
    $('#pj-id').val('');
    $('#pj-name, #pj-category, #pj-pic, #pj-description, #pj-start, #pj-target, #pj-actual').val('');
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
        actual_date: $('#pj-actual').val()
      },
      success: function (r) {
        if (r.status === 'success') {
          $('#modalProject').modal('hide');
          Swal.fire({ icon: 'success', title: 'Success!', text: 'Project saved successfully.', timer: 1500, showConfirmButton: false });
          if ($('#pj-status').val() === 'Done') fireConfetti();
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
    var colors = ['#22c55e', '#0e7a5f', '#f0a94a', '#0ea5e9', '#eab308', '#ef4444'];
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
          if (newStatus === 'Done') fireConfetti();
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
    var secondaryInfo = p.status === 'Done' ? completionBadge(p.target_date, p.actual_date) : daysInfo(p.target_date, p.status);
    $('#drawer-head').html(
      '<span class="drawer-close" onclick="closeDrawer()"><i class="fa fa-times"></i></span>' +
      '<span class="cat" style="color:' + mc.c + ';background:' + mc.s + ';">' + (p.category ? escHtml(p.category) : 'General') + '</span> ' +
      atRiskBadgeHtml(p) +
      '<div class="drawer-title">' + escHtml(p.project_name) + '</div>' +
      '<div class="d-flex align-items-center flex-wrap" style="gap:8px;">' +
        '<span class="badge ' + statusClass(p.status) + '">' + p.status + '</span>' +
        '<span class="badge badge-priority-' + p.priority + '">' + p.priority + '</span>' +
        picChipHtml(p.pic) +
        (secondaryInfo ? '<span style="font-size:11px;color:var(--ink-soft);">' + secondaryInfo + '</span>' : '') +
      '</div>' +
      '<div class="drawer-meta-grid">' +
        '<div class="drawer-meta-item"><span class="k">Start</span><span class="v">' + (fmtDate(p.start_date) || '&mdash;') + '</span></div>' +
        '<div class="drawer-meta-item"><span class="k">Target</span><span class="v">' + (fmtDate(p.target_date) || '&mdash;') + '</span></div>' +
        '<div class="drawer-meta-item"><span class="k">Actual</span><span class="v">' + (fmtDate(p.actual_date) || '&mdash;') + '</span></div>' +
        '<div class="drawer-meta-item"><span class="k">Progress</span><span class="v">' + p.progress + '%</span></div>' +
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
        if (newProgress === 100 && p.status !== 'Done') { p.status = 'Done'; fireConfetti(); }
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
      return '<div class="activity-item" style="cursor:default;">' +
        '<span class="activity-dot" style="background:' + (a.action.indexOf('milestone') === 0 ? '#0e7490' : (a.action === 'created' ? '#22c55e' : '#8a93a2')) + ';"></span>' +
        '<div><div class="activity-text">' + activityText(a) + '</div>' +
        '<div class="activity-time">' + escHtml(a.changed_by) + ' &middot; ' + relativeTime(a.changed_date) + '</div></div>' +
      '</div>';
    }).join('');
    $('#drawer-activity').html(html);
  }

  $(function () { loadProjects(); });
</script>

</body>
</html>
