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
?>

<style>
  :root {
    --jbt-ink: #161a24;
    --jbt-ink-soft: #667;
    --jbt-line: #e3e6ee;
    --jbt-line-soft: #eef0f6;
    --jbt-surface: #ffffff;
    --jbt-surface-2: #f6f7fb;
    --jbt-accent: #3454d1;
    --jbt-accent-soft: #eaeeff;
    --jbt-critical: #dc2626;
    --jbt-critical-soft: #fdecec;
    --jbt-warn: #b45309;
    --jbt-warn-soft: #fdf3e3;
    --jbt-violet: #7c3aed;
    --jbt-violet-soft: #f1eafd;
  }
  .jbt-page[data-theme="dark"] {
    --jbt-ink: #e7e9f2;
    --jbt-ink-soft: #97a0b8;
    --jbt-line: #2a3040;
    --jbt-line-soft: #212636;
    --jbt-surface: #171b28;
    --jbt-surface-2: #12151f;
    --jbt-accent: #7f97ff;
    --jbt-accent-soft: #1f2645;
    --jbt-critical: #f87171;
    --jbt-critical-soft: #3a1c1c;
    --jbt-warn: #f0a94a;
    --jbt-warn-soft: #3a2b12;
    --jbt-violet: #b794f6;
    --jbt-violet-soft: #2c2145;
  }

  .jbt-page {
    background:
      radial-gradient(900px circle at 8% -10%, rgba(52,84,209,.08) 0%, rgba(52,84,209,0) 45%),
      radial-gradient(800px circle at 100% 0%, rgba(124,58,237,.05) 0%, rgba(124,58,237,0) 40%),
      #f6f7fb;
    border-radius: 18px;
    padding: 20px;
    color: var(--jbt-ink);
    transition: background-color .25s ease;
    font-variant-numeric: tabular-nums;
  }
  .jbt-page[data-theme="dark"] {
    background:
      radial-gradient(900px circle at 8% -10%, rgba(127,151,255,.10) 0%, rgba(127,151,255,0) 45%),
      radial-gradient(800px circle at 100% 0%, rgba(183,148,246,.07) 0%, rgba(183,148,246,0) 40%),
      #0d0f16;
  }

  .jbt-hero {
    position: relative; overflow: hidden; border-radius: 16px; padding: 26px 28px;
    background: linear-gradient(135deg, #1b2350 0%, #3454d1 60%, #5c6fe0 100%);
    color: #fff; margin-bottom: 18px;
  }
  .jbt-hero::after {
    content: ''; position: absolute; right: -60px; top: -60px; width: 260px; height: 260px;
    border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,.14) 0%, rgba(255,255,255,0) 70%);
  }
  .jbt-hero-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; position:relative; z-index:1; }
  .jbt-hero h1 { font-size: 21px; font-weight: 800; margin: 0 0 6px; display:flex; align-items:center; gap:10px; }
  .jbt-hero p { font-size: 12.5px; color: rgba(255,255,255,.82); margin: 0; max-width: 640px; line-height:1.5; }
  .jbt-hero-actions { display:flex; gap:8px; align-items:center; }
  .jbt-theme-toggle {
    width: 38px; height: 38px; border-radius: 50%; border: none; cursor: pointer;
    background: rgba(255,255,255,.14); color: #fff; font-size: 14px; display:flex; align-items:center; justify-content:center;
    transition: background .15s ease, transform .15s ease;
  }
  .jbt-theme-toggle:hover { background: rgba(255,255,255,.24); transform: translateY(-1px); }
  .jbt-btn {
    border: none; border-radius: 10px; padding: 9px 16px; font-size: 12.5px; font-weight: 700; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px; transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
  }
  .jbt-btn-light { background: rgba(255,255,255,.16); color: #fff; }
  .jbt-btn-light:hover { background: rgba(255,255,255,.26); }
  .jbt-btn-solid { background: #fff; color: #2540b3; }
  .jbt-btn-solid:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,.18); }

  /* ---------- Stat cards ---------- */
  .jbt-stats { display:grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 18px; }
  @media (max-width: 992px) { .jbt-stats { grid-template-columns: repeat(2, 1fr); } }
  .jbt-stat {
    background: var(--jbt-surface); border: 1px solid var(--jbt-line); border-radius: 14px; padding: 16px 18px;
    display:flex; flex-direction:column; gap: 6px; box-shadow: 0 1px 2px rgba(20,25,45,.04);
  }
  .jbt-stat .lbl { font-size: 10.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; color: var(--jbt-ink-soft); display:flex; align-items:center; gap:6px; }
  .jbt-stat .val { font-size: 25px; font-weight: 800; color: var(--jbt-ink); line-height:1.1; }
  .jbt-stat .sub { font-size: 11px; color: var(--jbt-ink-soft); }
  .jbt-stat.critical .val { color: var(--jbt-critical); }
  .jbt-stat.warn .val { color: var(--jbt-warn); }

  /* ---------- Filter bar ---------- */
  .jbt-filters {
    background: var(--jbt-surface); border: 1px solid var(--jbt-line); border-radius: 14px; padding: 14px 16px;
    display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; margin-bottom: 16px;
  }
  .jbt-field { display:flex; flex-direction:column; gap:4px; }
  .jbt-field label { font-size: 10px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; color: var(--jbt-ink-soft); }
  .jbt-field input, .jbt-field select {
    border: 1px solid var(--jbt-line); border-radius: 9px; padding: 7px 10px; font-size: 12.5px;
    background: var(--jbt-surface-2); color: var(--jbt-ink); min-width: 150px;
  }
  .jbt-field input:focus, .jbt-field select:focus { outline: none; border-color: var(--jbt-accent); }
  .jbt-field.grow { flex: 1 1 220px; }
  .jbt-field.grow input { width: 100%; }

  /* ---------- Table ---------- */
  .jbt-table-wrap { background: var(--jbt-surface); border: 1px solid var(--jbt-line); border-radius: 14px; overflow: hidden; }
  .jbt-table-scroll { overflow-x: auto; }
  table.jbt-table { width: 100%; border-collapse: collapse; font-size: 12.5px; min-width: 1040px; }
  table.jbt-table thead th {
    background: var(--jbt-surface-2); color: var(--jbt-ink-soft); text-transform: uppercase; letter-spacing: .03em;
    font-size: 10.5px; font-weight: 800; padding: 11px 12px; text-align: left; border-bottom: 1px solid var(--jbt-line); white-space: nowrap;
  }
  table.jbt-table tbody td { padding: 10px 12px; border-bottom: 1px solid var(--jbt-line-soft); color: var(--jbt-ink); vertical-align: middle; }
  table.jbt-table tbody tr:hover { background: var(--jbt-surface-2); }
  table.jbt-table tbody tr:last-child td { border-bottom: none; }
  .num { text-align: right; font-variant-numeric: tabular-nums; }
  .mono { font-family: 'SFMono-Regular', Consolas, monospace; font-size: 11.5px; }

  .jbt-badge { display:inline-flex; align-items:center; gap:5px; padding: 4px 10px; border-radius: 999px; font-size: 10.5px; font-weight: 800; white-space:nowrap; }
  .jbt-badge.b-critical { background: var(--jbt-critical-soft); color: var(--jbt-critical); }
  .jbt-badge.b-warn { background: var(--jbt-warn-soft); color: var(--jbt-warn); }
  .jbt-badge.b-violet { background: var(--jbt-violet-soft); color: var(--jbt-violet); }
  .jbt-badge .dot { width:6px; height:6px; border-radius:50%; background: currentColor; }

  .jbt-detail-btn {
    border: 1px solid var(--jbt-line); background: var(--jbt-surface-2); color: var(--jbt-accent);
    border-radius: 8px; padding: 5px 11px; font-size: 11.5px; font-weight: 700; cursor: pointer;
  }
  .jbt-detail-btn:hover { background: var(--jbt-accent-soft); }

  .jbt-empty { padding: 60px 20px; text-align:center; color: var(--jbt-ink-soft); }
  .jbt-empty i { font-size: 42px; color: #22c55e; margin-bottom: 10px; display:block; }
  .jbt-loading { padding: 50px; text-align:center; color: var(--jbt-ink-soft); font-size: 13px; }
  .jbt-loading i { margin-right: 8px; }

  /* ---------- Modal detail ---------- */
  #jbtDetailModal .modal-dialog { max-width: 980px; }
  #jbtDetailModal .modal-content { border-radius: 14px; border: none; overflow:hidden; }
  #jbtDetailModal table { width:100%; font-size: 12px; border-collapse: collapse; }
  #jbtDetailModal th { background:#f6f7fb; text-transform:uppercase; font-size:10px; letter-spacing:.03em; color:#667; padding:8px 10px; text-align:left; white-space:nowrap; }
  #jbtDetailModal td { padding:8px 10px; border-bottom:1px solid #eef0f6; vertical-align:middle; }
  #jbtDetailModal tr.mismatch td { background: #fdecec; }
  #jbtDetailModal tr.mismatch td.mismatch-cell { color:#dc2626; font-weight:800; }
  .jbt-exp-actual { font-size: 10.5px; color:#a3abbd; display:block; }

  @keyframes jbtFadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
  .jbt-fade-in { animation: jbtFadeUp .25s ease both; }
</style>

<div class="jbt-page" id="jbtPage" data-theme="light">

  <div class="jbt-hero">
    <div class="jbt-hero-top">
      <div>
        <h1><i class="fa fa-balance-scale"></i> Journal Balance Tracker</h1>
        <p>Deteksi otomatis <b>no_journal</b> di <span class="mono" style="color:#fff;">tbl_list_journal</span> dimana total Debit IDR &ne; total Credit IDR, lengkap dengan diagnosis penyebab &mdash; <b>salah kurs</b> (konversi/rate) atau <b>jurnal tidak lengkap</b> (baris hilang).</p>
      </div>
      <div class="jbt-hero-actions">
        <button class="jbt-btn jbt-btn-light" id="jbtRefreshBtn" onclick="loadData()"><i class="fa fa-sync"></i> Refresh</button>
        <button class="jbt-btn jbt-btn-solid" onclick="exportExcel()"><i class="fa fa-file-excel"></i> Export Excel</button>
        <button class="jbt-theme-toggle" id="jbtThemeToggle" title="Toggle theme"><i class="fa fa-moon"></i></button>
      </div>
    </div>
  </div>

  <div class="jbt-stats" id="jbtStats">
    <div class="jbt-stat">
      <div class="lbl"><i class="fa fa-list-ol"></i> Total Jurnal Tidak Balance</div>
      <div class="val" id="statTotal">&mdash;</div>
      <div class="sub">status &ne; Cancel &middot; selisih &gt; Rp 1</div>
    </div>
    <div class="jbt-stat">
      <div class="lbl"><i class="fa fa-coins"></i> Total Selisih (abs)</div>
      <div class="val" id="statSelisih">&mdash;</div>
      <div class="sub">akumulasi seluruh selisih Debit/Credit IDR</div>
    </div>
    <div class="jbt-stat critical">
      <div class="lbl"><i class="fa fa-exchange-alt"></i> Salah Kurs (Konversi)</div>
      <div class="val" id="statRateConv">&mdash;</div>
      <div class="sub">baris dengan debit_idr/credit_idr &ne; jumlah &times; rate</div>
    </div>
    <div class="jbt-stat warn">
      <div class="lbl"><i class="fa fa-puzzle-piece"></i> Jurnal Tidak Lengkap</div>
      <div class="val" id="statIncomplete">&mdash;</div>
      <div class="sub">saldo native (mata uang asli) tidak balance</div>
    </div>
  </div>

  <div class="jbt-filters">
    <div class="jbt-field">
      <label>Dari Tanggal</label>
      <input type="date" id="fltStart">
    </div>
    <div class="jbt-field">
      <label>Sampai Tanggal</label>
      <input type="date" id="fltEnd">
    </div>
    <div class="jbt-field">
      <label>Diagnosis</label>
      <select id="fltDiagnosis">
        <option value="">Semua</option>
        <option value="salah_kurs_konversi">Salah Kurs (Konversi Baris)</option>
        <option value="tidak_lengkap">Jurnal Tidak Lengkap</option>
        <option value="salah_kurs_rate">Salah Kurs (Rate Tidak Konsisten)</option>
      </select>
    </div>
    <div class="jbt-field grow">
      <label>Cari No. Journal / Tipe</label>
      <input type="text" id="fltSearch" placeholder="Contoh: GACC/IN atau AP - BPB">
    </div>
    <button class="jbt-btn" style="background:var(--jbt-accent);color:#fff;" onclick="loadData()"><i class="fa fa-filter"></i> Terapkan</button>
    <button class="jbt-btn" style="background:var(--jbt-surface-2);color:var(--jbt-ink);border:1px solid var(--jbt-line);" onclick="resetFilters()"><i class="fa fa-times"></i> Reset</button>
  </div>

  <div class="jbt-table-wrap">
    <div class="jbt-table-scroll">
      <table class="jbt-table">
        <thead>
          <tr>
            <th>No. Journal</th>
            <th>Tanggal</th>
            <th>Tipe</th>
            <th>Status</th>
            <th>Curr</th>
            <th class="num">Baris</th>
            <th class="num">Debit IDR</th>
            <th class="num">Credit IDR</th>
            <th class="num">Selisih</th>
            <th>Diagnosis</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="jbtTbody">
          <tr><td colspan="11"><div class="jbt-loading"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Detail modal -->
<div class="modal fade" id="jbtDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#1b2350;color:#fff;">
        <h5 class="modal-title" id="jbtDetailTitle"><i class="fa fa-search-dollar"></i> Detail Journal</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:.8;"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body p-0" style="max-height:70vh;overflow-y:auto;">
        <div id="jbtDetailBody" class="p-4 text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>
      </div>
      <div class="modal-footer" id="jbtDetailFooter"></div>
    </div>
  </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
var jbtData = [];
var jbtSummary = {};

function fmtRp(n) {
  n = Number(n) || 0;
  var neg = n < 0;
  n = Math.abs(n);
  var s = n.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
  return (neg ? '-' : '') + 'Rp ' + s;
}
function fmtNum(n, dec) {
  return Number(n || 0).toLocaleString('id-ID', { minimumFractionDigits: dec || 0, maximumFractionDigits: dec || 2 });
}
function escHtml(s) {
  return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
  });
}
function diagBadge(d, label) {
  var cls = d === 'salah_kurs_konversi' ? 'b-critical' : (d === 'tidak_lengkap' ? 'b-warn' : 'b-violet');
  return '<span class="jbt-badge ' + cls + '"><span class="dot"></span>' + escHtml(label) + '</span>';
}

function loadData() {
  var tbody = document.getElementById('jbtTbody');
  tbody.innerHTML = '<tr><td colspan="11"><div class="jbt-loading"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div></td></tr>';

  $.post('ajax_journal_balance.php', {
    start_date: document.getElementById('fltStart').value,
    end_date: document.getElementById('fltEnd').value
  }, function (res) {
    if (res.status !== 'success') {
      tbody.innerHTML = '<tr><td colspan="11"><div class="jbt-empty"><i class="fa fa-exclamation-triangle" style="color:#dc2626;"></i>' + escHtml(res.message || 'Gagal memuat data') + '</div></td></tr>';
      return;
    }
    jbtData = res.data;
    jbtSummary = res.summary;
    renderStats();
    renderTable();
  }, 'json').fail(function () {
    tbody.innerHTML = '<tr><td colspan="11"><div class="jbt-empty"><i class="fa fa-exclamation-triangle" style="color:#dc2626;"></i>Gagal menghubungi server.</div></td></tr>';
  });
}

function renderStats() {
  document.getElementById('statTotal').textContent = fmtNum(jbtSummary.total);
  document.getElementById('statSelisih').textContent = fmtRp(jbtSummary.total_selisih_abs);
  document.getElementById('statRateConv').textContent = fmtNum(jbtSummary.rate_conversion);
  document.getElementById('statIncomplete').textContent = fmtNum(jbtSummary.incomplete + jbtSummary.rate_inconsistent);
}

function getFiltered() {
  var diag = document.getElementById('fltDiagnosis').value;
  var search = document.getElementById('fltSearch').value.trim().toLowerCase();
  return jbtData.filter(function (r) {
    if (diag && r.diagnosis !== diag) return false;
    if (search && r.no_journal.toLowerCase().indexOf(search) === -1 && (r.types || '').toLowerCase().indexOf(search) === -1) return false;
    return true;
  });
}

function renderTable() {
  var tbody = document.getElementById('jbtTbody');
  var list = getFiltered();

  if (list.length === 0) {
    tbody.innerHTML = '<tr><td colspan="11"><div class="jbt-empty"><i class="fa fa-check-circle"></i>' +
      (jbtData.length === 0 ? 'Tidak ada jurnal yang tidak balance pada rentang ini. Semua sudah balance.' : 'Tidak ada hasil yang cocok dengan filter.') +
      '</div></td></tr>';
    return;
  }

  var html = list.map(function (r, idx) {
    return '<tr class="jbt-fade-in" style="animation-delay:' + Math.min(idx * 12, 300) + 'ms;">' +
      '<td class="mono">' + escHtml(r.no_journal) + '</td>' +
      '<td>' + escHtml(r.tgl_journal) + '</td>' +
      '<td>' + escHtml(r.types) + '</td>' +
      '<td>' + escHtml(r.statuses) + '</td>' +
      '<td>' + escHtml(r.currs) + '</td>' +
      '<td class="num">' + escHtml(r.line_count) + '</td>' +
      '<td class="num">' + fmtRp(r.tot_debit_idr) + '</td>' +
      '<td class="num">' + fmtRp(r.tot_credit_idr) + '</td>' +
      '<td class="num" style="font-weight:800;color:' + (r.selisih < 0 ? '#dc2626' : '#b45309') + ';">' + fmtRp(r.selisih) + '</td>' +
      '<td>' + diagBadge(r.diagnosis, r.diagnosis_label) + '</td>' +
      '<td><button class="jbt-detail-btn" onclick="showDetail(\'' + encodeURIComponent(r.no_journal) + '\')"><i class="fa fa-search"></i> Detail</button></td>' +
    '</tr>';
  }).join('');

  tbody.innerHTML = html;
}

function resetFilters() {
  document.getElementById('fltStart').value = '';
  document.getElementById('fltEnd').value = '';
  document.getElementById('fltDiagnosis').value = '';
  document.getElementById('fltSearch').value = '';
  loadData();
}

document.getElementById('fltDiagnosis').addEventListener('change', renderTable);
document.getElementById('fltSearch').addEventListener('input', renderTable);

function showDetail(noJournalEnc) {
  var noJournal = decodeURIComponent(noJournalEnc);
  document.getElementById('jbtDetailTitle').innerHTML = '<i class="fa fa-search-dollar"></i> ' + escHtml(noJournal);
  document.getElementById('jbtDetailBody').innerHTML = '<div class="p-4 text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Memuat...</div>';
  document.getElementById('jbtDetailFooter').innerHTML = '';
  $('#jbtDetailModal').modal('show');

  $.post('ajax_journal_balance_detail.php', { no_journal: noJournal }, function (res) {
    if (res.status !== 'success') {
      document.getElementById('jbtDetailBody').innerHTML = '<div class="p-4 text-center text-danger">' + escHtml(res.message || 'Gagal memuat detail') + '</div>';
      return;
    }
    renderDetail(res);
  }, 'json').fail(function () {
    document.getElementById('jbtDetailBody').innerHTML = '<div class="p-4 text-center text-danger">Gagal menghubungi server.</div>';
  });
}

function renderDetail(res) {
  var rows = res.lines.map(function (l) {
    var mismatch = l.debit_idr_mismatch || l.credit_idr_mismatch;
    var expectedNote = '';
    if (l.debit_idr_mismatch) expectedNote = '<span class="jbt-exp-actual">expected: ' + fmtRp(l.expected_debit_idr) + '</span>';
    if (l.credit_idr_mismatch) expectedNote = '<span class="jbt-exp-actual">expected: ' + fmtRp(l.expected_credit_idr) + '</span>';
    return '<tr class="' + (mismatch ? 'mismatch' : '') + '">' +
      '<td>' + escHtml(l.no_coa) + '<br><span class="jbt-exp-actual">' + escHtml(l.nama_coa) + '</span></td>' +
      '<td>' + escHtml(l.curr) + '</td>' +
      '<td class="num">' + fmtNum(l.rate, 2) + '</td>' +
      '<td class="num">' + fmtNum(l.debit, 4) + '</td>' +
      '<td class="num">' + fmtNum(l.credit, 4) + '</td>' +
      '<td class="num ' + (l.debit_idr_mismatch ? 'mismatch-cell' : '') + '">' + fmtRp(l.debit_idr) + (l.debit_idr_mismatch ? expectedNote : '') + '</td>' +
      '<td class="num ' + (l.credit_idr_mismatch ? 'mismatch-cell' : '') + '">' + fmtRp(l.credit_idr) + (l.credit_idr_mismatch ? expectedNote : '') + '</td>' +
    '</tr>';
  }).join('');

  var html =
    '<div class="p-3 pb-0">' +
      '<table><thead><tr><th>COA</th><th>Curr</th><th class="num">Rate</th><th class="num">Debit</th><th class="num">Credit</th><th class="num">Debit IDR</th><th class="num">Credit IDR</th></tr></thead>' +
      '<tbody>' + rows + '</tbody>' +
      '<tfoot><tr style="font-weight:800;border-top:2px solid #d7dbea;">' +
        '<td colspan="3">TOTAL</td>' +
        '<td class="num">' + fmtNum(res.totals.debit, 4) + '</td>' +
        '<td class="num">' + fmtNum(res.totals.credit, 4) + '</td>' +
        '<td class="num">' + fmtRp(res.totals.debit_idr) + '</td>' +
        '<td class="num">' + fmtRp(res.totals.credit_idr) + '</td>' +
      '</tr></tfoot></table>' +
    '</div>' +
    '<div class="px-3 pb-3 pt-2" style="font-size:12px;color:#667;">' +
      'Selisih IDR: <b style="color:#dc2626;">' + fmtRp(res.totals.selisih_idr) + '</b> &middot; ' +
      'Selisih Native: <b>' + fmtNum(res.totals.selisih_nat, 4) + '</b>' +
      '<br><span style="color:#a3abbd;">Baris berwarna merah menunjukkan debit_idr/credit_idr yang tersimpan tidak sama dengan jumlah &times; rate pada baris tersebut (nilai "expected" adalah hasil perhitungan yang seharusnya).</span>' +
    '</div>';

  document.getElementById('jbtDetailBody').innerHTML = html;
  document.getElementById('jbtDetailFooter').innerHTML =
    '<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>';
}

function exportExcel() {
  var params = new URLSearchParams({
    start_date: document.getElementById('fltStart').value,
    end_date: document.getElementById('fltEnd').value,
    diagnosis: document.getElementById('fltDiagnosis').value,
    search: document.getElementById('fltSearch').value
  });
  window.location = 'ekspor_journal_balance.php?' + params.toString();
}

/* ---------- theme toggle (mirrors project.php's page-scoped toggle) ---------- */
(function () {
  var page = document.getElementById('jbtPage');
  var btn = document.getElementById('jbtThemeToggle');
  var saved = localStorage.getItem('jbt-theme');
  var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  applyTheme(theme);
  btn.addEventListener('click', function () {
    theme = theme === 'dark' ? 'light' : 'dark';
    localStorage.setItem('jbt-theme', theme);
    applyTheme(theme);
  });
  function applyTheme(t) {
    page.setAttribute('data-theme', t);
    btn.innerHTML = t === 'dark' ? '<i class="fa fa-sun"></i>' : '<i class="fa fa-moon"></i>';
  }
})();

loadData();
</script>

</body>
</html>
