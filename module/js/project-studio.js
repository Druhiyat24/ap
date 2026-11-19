/* ============================================================================
   project-studio.js - menu Project mode 3D:
     - kartu "Project Workflow" (Mockup -> ERD -> Coding per project aktif)
     - panel Mockup (versi .html, preview, Approve / Request changes)
     - editor ERD (tabel & relasi, Pull from database, Migration SQL)
     - isi laporan Lucca (SELALU bahasa Indonesia; teks UI lain Inggris)
   Ruangan 3D-nya (Design Studio & Data Room) ada di studio3d.js dan hanya
   dimuat saat ruangan dipilih. Data: ajax_project_studio.php.

   Coding TIDAK punya data sendiri: diambil dari status project (Planned =
   belum mulai, On Progress = berjalan, Done/Live = selesai). Tenggat yang
   dipakai Lucca = Target Date project.
   ============================================================================ */
(function () {
  'use strict';
  var AJAX = 'ajax_project_studio.php';
  var S = { ready: null, missing: [], mockups: {}, erds: {}, flags: {} };
  var opt = { getProjects: function () { return []; } };
  var listeners = [];

  /* ---------------- alat kecil ---------------- */
  const $ = (s, r) => (r || document).querySelector(s);
  const $$ = (s, r) => Array.prototype.slice.call((r || document).querySelectorAll(s));
  const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
  function post(action, data) {
    const fd = new FormData(); fd.append('action', action);
    Object.keys(data || {}).forEach(k => fd.append(k, data[k]));
    return fetch(AJAX, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(r => r.json())
      .then(j => { if (!j || j.status !== 'success') throw new Error(j && j.message ? j.message : 'Request failed.'); return j; });
  }
  function toast(msg, icon) { if (window.Swal) Swal.fire({ toast: true, position: 'top-end', icon: icon || 'success', title: msg, showConfirmButton: false, timer: 2600, timerProgressBar: true }); }
  function fail(e) { if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: e && e.message ? e.message : String(e) }); }
  const MON = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const MON_ID = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  function parseD(s) { const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(s || ''); return m ? new Date(+m[1], +m[2] - 1, +m[3]) : null; }
  function fmtD(s, withTime) {
    const d = parseD(s); if (!d) return '';
    let out = d.getDate() + ' ' + MON[d.getMonth()] + ' ' + d.getFullYear();
    const t = /(\d{2}:\d{2})/.exec((s || '').substring(10)); if (withTime && t) out += ' ' + t[1];
    return out;
  }

  /* ---------------- status ---------------- */
  const ST = { draft: 'Draft', review: 'In review', changes: 'Changes requested', approved: 'Approved', progress: 'In progress', done: 'Done', todo: 'Not started', waiting: 'Waiting', ready: 'Ready to start', skip: 'Not needed', submitted: 'Submitted' };
  const ST_COL = { draft: '#94a3b8', review: '#f59e0b', changes: '#ef4444', approved: '#10b981', progress: '#3b82f6', done: '#10b981', todo: '#94a3b8', waiting: '#94a3b8', ready: '#3b82f6', skip: '#94a3b8', submitted: '#f59e0b' };
  function chip(st, text) { return `<span class="ps-chip ${st}">${esc(text || ST[st] || st)}</span>`; }
  const isDoneSt = s => s === 'Done' || s === 'Live';
  const okSt = s => s === 'approved' || s === 'skip';

  // satu project = satu "view model" gabungan master_project + data studio
  function vm(p) {
    const id = String(p.id), mk = S.mockups[id], er = S.erds[id], fl = S.flags[id] || {};
    const mock = mk ? { st: mk.status, v: +mk.version, id: +mk.id, n: +mk.n, date: mk.created_date, note: mk.note || '' } : { st: +fl.mockup_skip ? 'skip' : 'todo', v: 0, n: 0 };
    const erd = er ? { st: er.status, v: +er.version, id: +er.id, n: +er.n, nNew: +er.n_new, nOld: +er.n_old, sqlRun: er.sql_run_date || '' } : { st: +fl.erd_skip ? 'skip' : 'todo', v: 0, n: 0, nNew: 0, nOld: 0 };
    const gate = okSt(mock.st) && okSt(erd.st);
    const code = isDoneSt(p.status) ? 'done' : p.status === 'On Progress' ? 'progress' : gate ? 'ready' : 'waiting';
    return { id: +p.id, name: p.project_name || '', module: p.category || 'General', status: p.status || '', target: p.target_date || '', overdue: +p.is_overdue === 1, mock, erd, code, gate, skipM: +fl.mockup_skip === 1, skipE: +fl.erd_skip === 1 };
  }
  // yang masih perlu diurus: project aktif + yang sudah selesai tapi mockup/ERD-nya masih menggantung
  function pending(v) { return ['review', 'changes'].includes(v.mock.st) || ['draft', 'review', 'changes'].includes(v.erd.st) || (v.erd.st === 'approved' && v.erd.nNew > 0 && !v.erd.sqlRun); }
  function list() {
    const ord = { 'On Progress': 0, 'Planned': 1 };
    return (opt.getProjects() || []).map(vm).filter(v => !isDoneSt(v.status) || pending(v)).sort((a, b) => {
      const oa = ord[a.status] != null ? ord[a.status] : 2, ob = ord[b.status] != null ? ord[b.status] : 2;
      if (oa !== ob) return oa - ob;
      return (a.target || '9999') < (b.target || '9999') ? -1 : (a.target || '9999') > (b.target || '9999') ? 1 : a.id - b.id;
    });
  }
  function find(pid) { const p = (opt.getProjects() || []).find(x => +x.id === +pid); return p ? vm(p) : null; }
  const VAL = { todo: 0, waiting: 0, ready: 0, draft: .35, changes: .45, review: .75, approved: 1, skip: 1, progress: .5, done: 1 };
  const pct = v => Math.round(VAL[v.mock.st] * 25 + VAL[v.erd.st] * 25 + VAL[v.code] * 50);
  function waitingCount() { return list().reduce((n, v) => n + (v.mock.st === 'review' ? 1 : 0) + (v.erd.st === 'review' ? 1 : 0), 0); }

  function notify() { renderCard(); listeners.forEach(fn => { try { fn(); } catch (e) { if (window.console) console.warn(e); } }); }
  function load() {
    return post('summary').then(j => {
      S.ready = !!j.ready; S.missing = j.missing || [];
      S.mockups = j.mockups || {}; S.erds = j.erds || {}; S.flags = j.flags || {};
      notify();
    }).catch(e => { S.ready = false; notify(); if (window.console) console.warn('Project studio:', e); });
  }

  /* ======================================================================
     LUCCA - laporan bahasa Indonesia
     ====================================================================== */
  function salam() { const h = new Date().getHours(); return h >= 4 && h < 11 ? 'Selamat pagi' : h < 15 && h >= 11 ? 'Selamat siang' : h >= 15 && h < 18 ? 'Selamat sore' : 'Selamat malam'; }
  function tgl(s) { const d = parseD(s); return d ? d.getDate() + ' ' + MON_ID[d.getMonth()] : ''; }
  function sisa(s) {
    const d = parseD(s); if (!d) return '';
    const t = new Date(); t.setHours(0, 0, 0, 0);
    const n = Math.round((d - t) / 86400000);
    return n < 0 ? `sudah lewat ${-n} hari` : n === 0 ? 'hari ini' : n === 1 ? 'besok' : `${n} hari lagi`;
  }
  function target(v) { return v.target ? ` Target project ${tgl(v.target)} (${sisa(v.target)}).` : ''; }
  function luccaItems(where) {
    const it = [], st = where !== 'data', dt = where !== 'studio';
    if (S.ready === false) {
      it.push({ lv: 'watch', ic: 'database', t: 'Tabel Design Studio belum ada di database produksi. Jalankan <b>db_migrate_LIVE_20261117_project_studio.sql</b> dulu supaya mockup &amp; ERD bisa disimpan.' });
      return it;
    }
    list().forEach(v => {
      const nm = `<b>${esc(v.name)}</b>`, late = v.overdue ? 'alert' : 'info';
      if (st) {
        if (v.mock.st === 'review') it.push({ lv: 'watch', ic: 'pencil-ruler', t: `Mockup ${nm} v${v.mock.v} menunggu persetujuan Anda.`, go: ['mockup', v.id], b: 'Periksa' });
        else if (v.mock.st === 'changes') it.push({ lv: late, ic: 'undo', t: `Mockup ${nm} sedang direvisi, versi barunya belum masuk.${target(v)}`, go: ['mockup', v.id], b: 'Lihat' });
        else if (v.mock.st === 'todo' && !isDoneSt(v.status)) it.push({ lv: late, ic: 'image', t: `${nm} belum punya mockup.${target(v)}`, go: ['mockup', v.id], b: 'Lihat' });
      }
      if (dt) {
        if (v.erd.st === 'review') it.push({ lv: 'watch', ic: 'project-diagram', t: `ERD ${nm} v${v.erd.v} menunggu persetujuan Anda.`, go: ['erd', v.id], b: 'Periksa' });
        else if (v.erd.st === 'draft') it.push({ lv: late, ic: 'project-diagram', t: `ERD ${nm} masih draf v${v.erd.v}, belum diajukan.${target(v)}`, go: ['erd', v.id], b: 'Lihat' });
        else if (v.erd.st === 'changes') it.push({ lv: late, ic: 'undo', t: `ERD ${nm} sedang direvisi.${target(v)}`, go: ['erd', v.id], b: 'Lihat' });
        else if (v.erd.st === 'todo' && !isDoneSt(v.status)) it.push({ lv: late, ic: 'database', t: `${nm} belum punya ERD.${target(v)}`, go: ['erd', v.id], b: 'Lihat' });
        else if (v.erd.st === 'approved' && v.erd.nNew > 0 && !v.erd.sqlRun) it.push({ lv: 'info', ic: 'file-code', t: `ERD ${nm} sudah disetujui. Ada ${v.erd.nNew} tabel baru — SQL migrasinya belum ditandai sudah dijalankan di produksi.`, go: ['sql', v.id], b: 'Lihat SQL' });
      }
      if (v.code === 'progress') it.push({ lv: late, ic: 'code', t: `Coding ${nm} sedang berjalan.${target(v)}` });
      else if (v.code === 'ready') it.push({ lv: 'info', ic: 'lock-open', t: `Mockup &amp; ERD ${nm} sudah beres — coding bisa dimulai.` });
    });
    const urut = { alert: 0, watch: 1, info: 2 };
    return it.sort((a, b) => urut[a.lv] - urut[b.lv]);
  }
  function luccaMood() { const a = luccaItems('all'); return a.some(i => i.lv === 'alert') ? 'alert' : a.some(i => i.lv === 'watch') ? 'watch' : 'calm'; }
  function luccaIntro(room, items) {
    const w = items.filter(i => i.lv === 'watch').length, a = items.filter(i => i.lv === 'alert').length;
    const tempat = room === 'studio' ? 'di studio' : 'di ruang data';
    let t = `${salam()}. `;
    if (S.ready === false) return t + 'Ada satu hal yang perlu disiapkan dulu.';
    if (a) t += `Ada <b>${a}</b> pekerjaan ${tempat} yang projectnya sudah lewat target. `;
    if (w) t += `<b>${w}</b> hal ${tempat} menunggu persetujuan Anda.`;
    else if (!a) t += items.length ? `Tidak ada yang menunggu persetujuan Anda ${tempat}.` : `Semua beres ${tempat}. Saya akan terus mengawasi.`;
    return t;
  }
  function go(g) {
    if (!g) return;
    if (g[0] === 'mockup') openMockup(g[1]);
    else if (g[0] === 'erd') openErd(g[1]);
    else if (g[0] === 'sql') openErd(g[1], null, true);
  }

  /* ======================================================================
     KARTU PROJECT WORKFLOW (di bawah Delivery Performance, mode 3D saja)
     ====================================================================== */
  const ICON = { mockup: 'pencil-ruler', erd: 'project-diagram', code: 'code' };
  // Kartu bisa dilipat (Show/Hide). Bawaan TERLIPAT (user 19 Nov 2026: "jangan langsung
  // terlihat"); pilihan diingat per browser. Terlipat: judul + ringkasan + jumlah yang menunggu.
  let flowOpen = false;
  try { flowOpen = localStorage.getItem('projFlowOpen') === '1'; } catch (e) { /* abaikan */ }
  function renderCard() {
    const box = document.getElementById('ps-flow-card'); if (!box) return;
    const rows = list(), w = waitingCount();
    const nMock = rows.filter(v => okSt(v.mock.st)).length, nErd = rows.filter(v => okSt(v.erd.st)).length;
    const nCode = rows.filter(v => v.code === 'done').length, nSql = rows.filter(v => v.erd.st === 'approved' && v.erd.nNew > 0 && !v.erd.sqlRun).length;
    const mini = `<span><b>${rows.length}</b> active project${rows.length === 1 ? '' : 's'}</span><span>Mockup <b>${nMock}/${rows.length}</b> approved</span>`
      + `<span>ERD <b>${nErd}/${rows.length}</b> approved</span><span>Coding <b>${nCode}</b> done</span>${nSql ? `<span><b>${nSql}</b> SQL not run yet</span>` : ''}`;
    box.classList.toggle('ps-closed', !flowOpen);
    const cell = (v, track) => {
      const t = track === 'mockup' ? v.mock : track === 'erd' ? v.erd : null;
      const st = t ? t.st : v.code;
      let label = ST[st] + (t && t.v ? ' · v' + t.v : '');
      if (track === 'code' && st === 'waiting') label = 'Waiting for ' + [okSt(v.mock.st) ? '' : 'Mockup', okSt(v.erd.st) ? '' : 'ERD'].filter(Boolean).join(' + ');
      const sub = track === 'erd' && v.erd.st === 'approved' && v.erd.nNew ? (v.erd.sqlRun ? `<span class="ps-due"><i class="fas fa-check"></i> SQL run on production</span>` : `<span class="ps-due">${v.erd.nNew} new table(s) · SQL not run yet</span>`)
        : track === 'mockup' && t && t.n > 1 ? `<span class="ps-due">${t.n} versions</span>` : '';
      return `<div class="ps-trk${track === 'code' ? ' lock' : ''}" data-open="${track}" data-pid="${v.id}"><span class="ps-tic" style="color:${ST_COL[st]}"><i class="fas fa-${ICON[track]}"></i></span><div class="ps-meta">${chip(st, label)}${sub}</div></div>`;
    };
    box.innerHTML = `<div class="ps-fc-h"><span class="ps-ic"><i class="fas fa-tasks"></i></span><div><b>Project Workflow</b><div class="ps-sub">Every active project goes through three steps — Mockup, ERD, Coding. Coding follows the project status.</div></div>
        <div class="ps-fc-mini">${mini}</div>
        <span class="ps-wait${w ? '' : ' ok'}"><i class="fas fa-${w ? 'hourglass-half' : 'check-circle'}"></i><b>${w}</b> waiting for your approval</span>
        <button type="button" class="ps-fc-toggle" aria-expanded="${flowOpen}" aria-controls="ps-fc-body" title="${flowOpen ? 'Hide' : 'Show'} the project workflow"><span>${flowOpen ? 'Hide' : 'Show'}</span></button></div>
      ${S.ready === false ? '<div class="ps-notready"><i class="fas fa-database"></i><span>The Design Studio tables are not on the production database yet. Run <b>db_migrate_LIVE_20261117_project_studio.sql</b> first — until then nothing can be saved.</span></div>' : ''}
      <div class="ps-fc-body" id="ps-fc-body"><div class="ps-tb-wrap"><table class="ps-tb"><thead><tr><th>Project</th><th><i class="fas fa-pencil-ruler"></i> Mockup</th><th><i class="fas fa-project-diagram"></i> ERD</th><th><i class="fas fa-code"></i> Coding</th><th>Progress</th></tr></thead><tbody>
      ${rows.length ? rows.map(v => `<tr><td><span class="ps-pn">${esc(v.name)}</span><span class="ps-mod">${esc(v.module)}</span><span class="ps-pst">${esc(v.status)}${v.target ? ' · target ' + fmtD(v.target) : ''}${v.overdue ? ' · <b style="color:#dc2626">overdue</b>' : ''}</span></td>
        <td data-l="Mockup">${cell(v, 'mockup')}</td><td data-l="ERD">${cell(v, 'erd')}</td><td data-l="Coding">${cell(v, 'code')}</td>
        <td data-l="Progress"><div class="ps-bar"><span style="width:${pct(v)}%"></span></div><div class="ps-pct">${pct(v)}%</div></td></tr>`).join('')
        : '<tr><td colspan="5" class="ps-empty-row">No active project. Planned and On Progress projects appear here.</td></tr>'}
      </tbody></table></div>
      <div class="ps-rule"><i class="fas fa-lock"></i><span>Coding is ready to start once the Mockup and the ERD are <b>Approved</b> (or marked <b>Not needed</b>). Click a Mockup or ERD cell to open it.</span></div></div>`;
  }
  document.addEventListener('click', e => {
    if (e.target.closest('#ps-flow-card .ps-fc-toggle')) {
      flowOpen = !flowOpen;
      try { localStorage.setItem('projFlowOpen', flowOpen ? '1' : '0'); } catch (er) { /* abaikan */ }
      renderCard();
      const b = document.querySelector('#ps-flow-card .ps-fc-toggle'); if (b) b.focus();   // fokus keyboard tetap di tombol
      return;
    }
    const t = e.target.closest('#ps-flow-card .ps-trk[data-open]');
    if (!t || t.classList.contains('lock')) return;
    if (t.dataset.open === 'mockup') openMockup(+t.dataset.pid); else openErd(+t.dataset.pid);
  });

  /* ======================================================================
     KERANGKA PANEL (dibuat sekali, ditempel ke body)
     ====================================================================== */
  function buildDom() {
    if (document.getElementById('ps-mk')) return;
    const wrap = document.createElement('div');
    wrap.innerHTML = `
<div class="ps-ov" id="ps-mk" role="dialog" aria-modal="true">
  <div class="ps-sheet">
    <div class="ps-sh-head"><span class="ps-ic"><i class="fas fa-pencil-ruler"></i></span><div><h2 id="ps-mk-title"></h2><div class="ps-sub" id="ps-mk-sub"></div></div><button class="ps-x" data-ps-close="ps-mk" title="Close"><i class="fas fa-times"></i></button></div>
    <div class="ps-flow" id="ps-mk-flow"></div>
    <div class="ps-body ps-mk-body">
      <div class="ps-pane">
        <div class="ps-pane-h">Versions</div>
        <div class="ps-up">
          <input type="text" id="ps-mk-note" maxlength="255" placeholder="Note for the new version (optional)">
          <div class="ps-row">
            <label class="ps-btn sm pri" title="Upload an .html mockup file"><i class="fas fa-upload"></i>Upload .html<input type="file" id="ps-mk-file" accept=".html,.htm" hidden></label>
            <button type="button" class="ps-btn sm" id="ps-mk-server" title="Pick one of the mockup_*.html files in module/AP"><i class="fas fa-folder-open"></i>From server</button>
          </div>
        </div>
        <div class="ps-vers" id="ps-mk-vers"></div>
        <div id="ps-mk-skip" style="padding:4px 14px 14px"></div>
      </div>
      <div class="ps-pane ps-pv">
        <div class="ps-pv-bar">
          <div class="ps-lbl" id="ps-mk-lbl"></div>
          <div class="ps-seg2" id="ps-mk-dev">
            <button type="button" data-w="100%" class="on"><i class="fas fa-desktop"></i>Desktop</button>
            <button type="button" data-w="820px"><i class="fas fa-tablet-alt"></i>Tablet</button>
            <button type="button" data-w="390px"><i class="fas fa-mobile-alt"></i>Phone</button>
          </div>
          <button type="button" class="ps-btn sm" id="ps-mk-full"><i class="fas fa-expand"></i>Full screen</button>
          <button type="button" class="ps-btn sm" id="ps-mk-dl"><i class="fas fa-download"></i>.html</button>
        </div>
        <div class="ps-pv-stage" id="ps-mk-stage"></div>
      </div>
      <div class="ps-pane ps-rvp">
        <div class="ps-pane-h">Review notes</div>
        <div id="ps-mk-rv"></div>
        <div class="ps-rv-form">
          <textarea id="ps-mk-rnote" placeholder="Write a review note for this version..."></textarea>
          <div class="ps-acts">
            <button type="button" class="ps-btn ok" id="ps-mk-ok"><i class="fas fa-check"></i>Approve</button>
            <button type="button" class="ps-btn no" id="ps-mk-no"><i class="fas fa-undo"></i>Request changes</button>
          </div>
          <div class="ps-who"><i class="fas fa-info-circle"></i> Every decision is kept per version with its date and time.</div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="ps-ov" id="ps-erd" role="dialog" aria-modal="true">
  <div class="ps-sheet">
    <div class="ps-sh-head"><span class="ps-ic"><i class="fas fa-project-diagram"></i></span><div><h2 id="ps-erd-title"></h2><div class="ps-sub" id="ps-erd-sub"></div></div><button class="ps-x" data-ps-close="ps-erd" title="Close"><i class="fas fa-times"></i></button></div>
    <div class="ps-flow" id="ps-erd-flow"></div>
    <div class="ps-body ps-erd-body">
      <div class="ps-pane">
        <div class="ps-pane-h">Tables <span id="ps-erd-count" style="text-transform:none;letter-spacing:0"></span></div>
        <div class="ps-search"><i class="fas fa-search"></i><input type="text" id="ps-erd-find" placeholder="Find table..."></div>
        <div class="ps-tl" id="ps-erd-list"></div>
      </div>
      <div class="ps-erd-main">
        <div class="ps-erd-tools">
          <select id="ps-erd-ver" class="ps-btn sm no-select2" title="Version"></select>
          <button type="button" class="ps-btn sm" id="ps-erd-pull"><i class="fas fa-download"></i>Pull from database</button>
          <button type="button" class="ps-btn sm" id="ps-erd-add"><i class="fas fa-plus"></i>New table</button>
          <button type="button" class="ps-btn sm" id="ps-erd-rel"><i class="fas fa-link"></i>Add relation</button>
          <span class="ps-erd-hint" id="ps-erd-hint"></span>
          <span class="ps-saved" id="ps-erd-saved"></span>
          <span class="ps-sp"></span>
          <button type="button" class="ps-btn sm" id="ps-erd-sql"><i class="fas fa-file-code"></i>Migration SQL</button>
          <button type="button" class="ps-btn sm no" id="ps-erd-no"><i class="fas fa-undo"></i>Request changes</button>
          <button type="button" class="ps-btn sm ok" id="ps-erd-ok"><i class="fas fa-check"></i>Approve</button>
          <button type="button" class="ps-btn sm pri" id="ps-erd-main"></button>
        </div>
        <div class="ps-erd-stage">
          <svg class="ps-erd-svg" id="ps-erd-svg" xmlns="http://www.w3.org/2000/svg"></svg>
          <div class="ps-erd-legend">
            <span><i class="ps-sq" style="background:#0b1d4a;outline:1.5px solid #e4c47c"></i>New table (needs migration)</span>
            <span><i class="ps-sq" style="background:#64748b"></i>Existing table</span>
            <span><i class="fas fa-key" style="color:#d4a017"></i>PK</span>
            <span><i class="fas fa-link" style="color:#2563eb"></i>FK</span>
          </div>
          <div class="ps-erd-zoom">
            <button type="button" id="ps-z-in" title="Zoom in"><i class="fas fa-plus"></i></button>
            <button type="button" id="ps-z-out" title="Zoom out"><i class="fas fa-minus"></i></button>
            <button type="button" id="ps-z-fit" title="Fit"><i class="fas fa-expand-arrows-alt"></i></button>
          </div>
          <div class="ps-erd-empty" id="ps-erd-empty" hidden></div>
        </div>
      </div>
      <div class="ps-pane ps-inspp">
        <div class="ps-pane-h" id="ps-erd-insp-h">Table detail</div>
        <div class="ps-insp" id="ps-erd-insp"></div>
      </div>
    </div>
  </div>
</div>
<div class="ps-dlg-ov" id="ps-dlg"><div class="ps-dlg"><div class="ps-dlg-h" id="ps-dlg-h"></div><div class="ps-dlg-b" id="ps-dlg-b"></div><div class="ps-dlg-f" id="ps-dlg-f"></div></div></div>`;
    while (wrap.firstElementChild) document.body.appendChild(wrap.firstElementChild);

    $$('[data-ps-close]').forEach(b => b.addEventListener('click', () => closeSheet(b.dataset.psClose)));
    document.addEventListener('keydown', e => {
      if (e.key !== 'Escape') return;
      if ($('#ps-dlg').classList.contains('on')) { $('#ps-dlg').classList.remove('on'); return; }
      if (relMode) { setRelMode(false); return; }
      if ($('#ps-erd').classList.contains('on')) closeSheet('ps-erd'); else if ($('#ps-mk').classList.contains('on')) closeSheet('ps-mk');
    });
    bindMockup(); bindErd();
  }
  function closeSheet(id) {
    if (id === 'ps-erd' && saveTimer) { clearTimeout(saveTimer); saveNow(); }
    $('#' + id).classList.remove('on');
  }
  function flowHtml(v, cur) {
    const s = (key, icon, title, st, extra) => `<span class="ps-stp${cur === key ? ' cur' : ''}"><i class="fas fa-${icon}"></i><b>${title}</b>${chip(st, ST[st] + (extra || ''))}</span>`;
    return s('mockup', 'pencil-ruler', 'Mockup', v.mock.st, v.mock.v ? ' · v' + v.mock.v : '') + '<span class="ps-arr"><i class="fas fa-chevron-right"></i></span>' +
      s('erd', 'project-diagram', 'ERD', v.erd.st, v.erd.v ? ' · v' + v.erd.v : '') + '<span class="ps-arr"><i class="fas fa-chevron-right"></i></span>' +
      s('code', 'code', 'Coding', v.code) +
      `<span class="ps-gate"><i class="fas fa-${v.gate ? 'lock-open' : 'lock'}"></i>${v.gate ? 'Mockup and ERD are done — coding can go ahead' : 'Coding is ready once Mockup and ERD are approved'}</span>`;
  }
  function dialog(head, body, foot) {
    $('#ps-dlg-h').innerHTML = head + '<button type="button" class="ps-x" id="ps-dlg-x"><i class="fas fa-times"></i></button>';
    $('#ps-dlg-b').innerHTML = body; $('#ps-dlg-f').innerHTML = foot;
    $('#ps-dlg').classList.add('on');
    $('#ps-dlg-x').onclick = () => $('#ps-dlg').classList.remove('on');
  }
  const closeDialog = () => $('#ps-dlg').classList.remove('on');

  /* ======================================================================
     PANEL MOCKUP
     ====================================================================== */
  const MK = { v: null, versions: [], reviews: [], sel: null };
  function openMockup(pid) {
    buildDom();
    const v = find(pid); if (!v) return;
    MK.v = v; MK.versions = []; MK.reviews = []; MK.sel = null;
    $('#ps-mk-title').textContent = v.name + ' — Mockup';
    $('#ps-mk').classList.add('on');
    renderMockup(true);
    if (S.ready === false) return;
    post('mockup_list', { project_id: v.id }).then(j => {
      MK.versions = j.versions || []; MK.reviews = j.reviews || []; MK.sel = MK.versions[0] || null;
      renderMockup();
    }).catch(fail);
  }
  function renderMockup(loading) {
    const v = MK.v = find(MK.v.id) || MK.v;
    $('#ps-mk-sub').textContent = `${v.module} · ${v.status}${v.target ? ' · target ' + fmtD(v.target) : ''} · ${MK.versions.length} version(s)`;
    $('#ps-mk-flow').innerHTML = flowHtml(v, 'mockup');
    const notReady = S.ready === false;
    $('#ps-mk-file').disabled = notReady; $('#ps-mk-server').disabled = notReady;
    $('#ps-mk-vers').innerHTML = loading ? '<div class="ps-empty" style="padding:20px"><i class="fas fa-circle-notch fa-spin"></i>Loading...</div>'
      : MK.versions.length ? MK.versions.map(x => `<div class="ps-ver${x === MK.sel ? ' on' : ''}" data-id="${x.id}"><div class="ps-top"><span class="ps-v">v${x.version}</span>${chip(x.status)}</div><div class="ps-by"><i class="far fa-clock"></i>${esc(fmtD(x.created_date, true))}${x.file_name ? ' · ' + esc(x.file_name) : ''}</div>${x.note ? `<div class="ps-nt">${esc(x.note)}</div>` : ''}</div>`).join('')
      : '<div class="ps-empty" style="padding:20px"><i class="fas fa-layer-group"></i><b>No versions yet</b>Upload the first .html mockup.</div>';
    $('#ps-mk-skip').innerHTML = !loading && !notReady && !MK.versions.length ? (v.skipM
      ? `<span style="font-size:12px;color:#64748b"><i class="fas fa-ban"></i> Marked as not needed. </span><button type="button" class="ps-link" data-skip="0">Undo</button>`
      : `<button type="button" class="ps-link" data-skip="1">This project does not need a mockup</button>`) : '';
    const stage = $('#ps-mk-stage'), sel = MK.sel;
    if (notReady) {
      $('#ps-mk-lbl').innerHTML = '<i class="fas fa-desktop"></i> Preview';
      stage.innerHTML = '<div class="ps-empty"><i class="fas fa-database"></i><b>Tables not created yet</b>Run db_migrate_LIVE_20261117_project_studio.sql on production first.</div>';
    } else if (!sel) {
      $('#ps-mk-lbl').innerHTML = '<i class="fas fa-desktop"></i> Preview';
      stage.innerHTML = loading ? '' : `<div class="ps-empty"><i class="fas fa-pencil-ruler"></i><b>No mockup yet</b>Upload an .html file, or pick one of the mockup_*.html files already on the server.</div>`;
    } else {
      $('#ps-mk-lbl').innerHTML = `<i class="fas fa-desktop"></i> v${sel.version} ${chip(sel.status)}`;
      const src = AJAX + '?action=mockup_raw&id=' + sel.id;
      const f = stage.querySelector('iframe');
      if (!f || f.dataset.id !== String(sel.id)) {
        stage.innerHTML = `<iframe sandbox="allow-scripts allow-same-origin allow-popups allow-modals" title="Mockup preview" data-id="${sel.id}" src="${src}"></iframe>`;
      }
      stage.querySelector('iframe').style.width = ($('#ps-mk-dev .on') || { dataset: { w: '100%' } }).dataset.w;
    }
    const rv = sel ? MK.reviews.filter(r => +r.ref_id === +sel.id) : [];
    $('#ps-mk-rv').innerHTML = rv.length ? rv.map(r => `<div class="ps-rv ${r.action}"><div class="ps-bub"><div class="ps-w">${chip(r.action)}<span>${esc(fmtD(r.created_date, true))}</span></div>${esc(r.note || '')}</div></div>`).join('')
      : '<div class="ps-empty" style="padding:16px 20px"><i class="far fa-comments"></i><b>No notes yet</b>Notes are kept per version, so every decision stays visible.</div>';
    const can = !!sel && MK.versions[0] === sel && sel.status !== 'approved';
    $('#ps-mk-ok').disabled = !can; $('#ps-mk-no').disabled = !can;
    $('#ps-mk-dl').disabled = !sel; $('#ps-mk-full').disabled = !sel;
  }
  function mockReload(selectFirst) {
    return Promise.all([post('mockup_list', { project_id: MK.v.id }), load()]).then(r => {
      const keep = MK.sel ? MK.sel.id : null;
      MK.versions = r[0].versions || []; MK.reviews = r[0].reviews || [];
      MK.sel = (!selectFirst && MK.versions.find(x => x.id === keep)) || MK.versions[0] || null;
      renderMockup();
    });
  }
  function upload(data) {
    data.project_id = MK.v.id; data.note = $('#ps-mk-note').value.trim();
    return post('mockup_upload', data).then(j => { $('#ps-mk-note').value = ''; toast(`v${j.version} uploaded and waiting for your review`); return mockReload(true); }).catch(fail);
  }
  function bindMockup() {
    $('#ps-mk-vers').addEventListener('click', e => { const c = e.target.closest('.ps-ver'); if (!c) return; MK.sel = MK.versions.find(x => +x.id === +c.dataset.id); renderMockup(); });
    $('#ps-mk-dev').addEventListener('click', e => { const b = e.target.closest('button'); if (!b) return; $('#ps-mk-dev .on').classList.remove('on'); b.classList.add('on'); const f = $('#ps-mk-stage iframe'); if (f) f.style.width = b.dataset.w; });
    $('#ps-mk-full').addEventListener('click', () => { const f = $('#ps-mk-stage iframe'); if (f && f.requestFullscreen) f.requestFullscreen(); });
    $('#ps-mk-dl').addEventListener('click', () => { if (MK.sel) window.location.href = AJAX + '?action=mockup_raw&download=1&id=' + MK.sel.id; });
    $('#ps-mk-file').addEventListener('change', e => { const f = e.target.files[0]; e.target.value = ''; if (f) upload({ file: f }); });
    $('#ps-mk-server').addEventListener('click', () => {
      post('mockup_files').then(j => {
        const files = j.files || [];
        dialog('<i class="fas fa-folder-open" style="color:#2563eb"></i> Mockup files on the server <small>· module/AP/mockup_*.html</small>',
          files.length ? files.map(f => `<label class="ps-pull-row"><input type="radio" name="ps-sf" value="${esc(f.name)}"><i class="far fa-file-code" style="color:#64748b"></i>${esc(f.name)}<small>${esc(f.mtime)} · ${Math.max(1, Math.round(f.size / 1024))} KB</small></label>`).join('') : '<div class="ps-empty"><b>No mockup files found</b></div>',
          '<button type="button" class="ps-btn" id="ps-sf-cancel">Cancel</button><button type="button" class="ps-btn pri" id="ps-sf-ok"><i class="fas fa-plus"></i>Add as new version</button>');
        $('#ps-sf-cancel').onclick = closeDialog;
        $('#ps-sf-ok').onclick = () => { const r = $('input[name=ps-sf]:checked'); if (!r) { toast('Pick a file first', 'info'); return; } closeDialog(); upload({ server_file: r.value }); };
      }).catch(fail);
    });
    $('#ps-mk-skip').addEventListener('click', e => {
      const b = e.target.closest('[data-skip]'); if (!b) return;
      post('skip', { project_id: MK.v.id, kind: 'mockup', on: b.dataset.skip }).then(() => { toast(b.dataset.skip === '1' ? 'Mockup marked as not needed' : 'Mockup is needed again'); return mockReload(); }).catch(fail);
    });
    const review = act => {
      const note = $('#ps-mk-rnote').value.trim();
      if (act === 'changes' && !note) { toast('Write what needs to change first', 'info'); $('#ps-mk-rnote').focus(); return; }
      post('mockup_review', { id: MK.sel.id, act, note: note || (act === 'approved' ? 'Approved.' : '') }).then(() => {
        $('#ps-mk-rnote').value = '';
        toast(act === 'approved' ? 'Mockup approved' : 'Changes requested');
        return mockReload();
      }).catch(fail);
    };
    $('#ps-mk-ok').addEventListener('click', () => review('approved'));
    $('#ps-mk-no').addEventListener('click', () => review('changes'));
  }

  /* ======================================================================
     EDITOR ERD (SVG). Hanya versi DRAFT yang bisa disunting; tersimpan
     otomatis. Versi lain hanya dilihat - "Edit as new version" membuat draft.
     ====================================================================== */
  const TW = 230, HH = 30, RH = 22;
  const TYPES = ['INT(11)', 'BIGINT(20)', 'VARCHAR(20)', 'VARCHAR(50)', 'VARCHAR(100)', 'VARCHAR(150)', 'VARCHAR(255)', 'DECIMAL(20,2)', 'DECIMAL(20,4)', 'DATE', 'DATETIME', 'TINYINT(1)', 'TEXT'];
  const ER = { v: null, cur: null, versions: [], reviews: [], E: null, editable: false };
  let svg = null, sel = null, view = { x: 0, y: 0, k: 1 }, relMode = false, relFrom = null, saveTimer = null, saving = null;
  const tH = t => HH + t.cols.length * RH + 6;
  const tById = id => ER.E.tables.find(t => t.id === id);

  function openErd(pid, focusTable, showSql) {
    buildDom();
    const v = find(pid); if (!v) return;
    ER.v = v; ER.cur = null; ER.versions = []; ER.reviews = []; ER.E = { tables: [], rels: [] }; ER.editable = false;
    sel = null; setRelMode(false);
    $('#ps-erd-title').textContent = v.name + ' — ERD';
    $('#ps-erd').classList.add('on');
    $('#ps-erd-saved').textContent = '';
    erdAll(true);
    if (S.ready === false) return;
    erdFetch(0).then(() => {
      if (focusTable && tById(focusTable)) sel = focusTable;
      erdAll(); requestAnimationFrame(erdFit);
      if (showSql) setTimeout(openSql, 120);
    }).catch(fail);
  }
  function erdFetch(id) {
    return post('erd_get', { project_id: ER.v.id, id: id || 0 }).then(j => {
      ER.versions = j.versions || []; ER.reviews = j.reviews || [];
      ER.cur = j.erd || null;
      const d = ER.cur && ER.cur.data ? ER.cur.data : { tables: [], rels: [] };
      ER.E = { tables: Array.isArray(d.tables) ? d.tables : [], rels: Array.isArray(d.rels) ? d.rels : [] };
      const latest = ER.versions[0];
      ER.editable = !!ER.cur && !!latest && +latest.id === +ER.cur.id && ER.cur.status === 'draft';
      if (!ER.E.tables.some(t => t.id === sel)) sel = (ER.E.tables.find(t => !t.old) || {}).id || null;
    });
  }
  function erdReload(id) { return Promise.all([erdFetch(id), load()]).then(() => { erdAll(); }); }
  function erdAll(loading) {
    const v = ER.v = find(ER.v.id) || ER.v, cur = ER.cur;
    $('#ps-erd-sub').textContent = `${v.module} · ${v.status}${v.target ? ' · target ' + fmtD(v.target) : ''}` + (cur ? ` · v${cur.version} ${ST[cur.status]}` : '');
    $('#ps-erd-flow').innerHTML = flowHtml(v, 'erd');
    $('#ps-erd-ver').innerHTML = ER.versions.map(x => `<option value="${x.id}"${cur && +x.id === +cur.id ? ' selected' : ''}>v${x.version} · ${ST[x.status]}</option>`).join('');
    $('#ps-erd-ver').style.display = ER.versions.length > 1 ? '' : 'none';
    const ed = ER.editable, latest = cur && ER.versions[0] && +ER.versions[0].id === +cur.id;
    ['#ps-erd-pull', '#ps-erd-add', '#ps-erd-rel'].forEach(s => { $(s).style.display = ed ? '' : 'none'; });
    $('#ps-erd-ok').style.display = $('#ps-erd-no').style.display = latest && cur.status === 'review' ? '' : 'none';
    const main = $('#ps-erd-main');
    if (!cur || !latest) main.style.display = 'none';
    else if (cur.status === 'draft') { main.style.display = ''; main.innerHTML = '<i class="fas fa-paper-plane"></i>Submit for review'; main.dataset.act = 'submit'; }
    else if (cur.status === 'review') main.style.display = 'none';
    else { main.style.display = ''; main.innerHTML = '<i class="fas fa-pen"></i>Edit as new version'; main.dataset.act = 'new'; }
    $('#ps-erd-sql').style.display = cur ? '' : 'none';
    svg.classList.toggle('ro', !ed);
    // keadaan kosong
    const empty = $('#ps-erd-empty');
    if (S.ready === false) { empty.hidden = false; empty.innerHTML = '<div class="ps-empty"><i class="fas fa-database"></i><b>Tables not created yet</b>Run db_migrate_LIVE_20261117_project_studio.sql on production first.</div>'; }
    else if (loading) { empty.hidden = false; empty.innerHTML = '<div class="ps-empty"><i class="fas fa-circle-notch fa-spin"></i>Loading...</div>'; }
    else if (!cur) {
      empty.hidden = false;
      empty.innerHTML = `<div class="ps-empty"><i class="fas fa-project-diagram"></i><b>No ERD yet</b>Start an ERD, then pull the tables this feature reads from the database and add the new tables it needs.<br><br>
        <button type="button" class="ps-btn pri" id="ps-erd-start"><i class="fas fa-plus"></i>Start ERD</button><br><br>
        ${v.skipE ? '<span style="font-size:12px"><i class="fas fa-ban"></i> Marked as not needed.</span> <button type="button" class="ps-link" data-eskip="0">Undo</button>' : '<button type="button" class="ps-link" data-eskip="1">This project does not change the database</button>'}</div>`;
    } else if (!ER.E.tables.length) {
      empty.hidden = false;
      empty.innerHTML = ed ? '<div class="ps-empty"><i class="fas fa-table"></i><b>Empty ERD</b>Pull existing tables from the database, or add a new table.<br><br><button type="button" class="ps-btn pri" data-pull="1"><i class="fas fa-download"></i>Pull from database</button></div>'
        : '<div class="ps-empty"><i class="fas fa-table"></i><b>This version has no tables</b></div>';
    } else empty.hidden = true;
    erdList(); erdDraw(); erdInsp();
  }
  function erdList() {
    const q = $('#ps-erd-find').value.trim().toLowerCase();
    const nNew = ER.E.tables.filter(t => !t.old).length;
    $('#ps-erd-count').textContent = ER.E.tables.length ? `${nNew} new · ${ER.E.tables.length - nNew} existing` : '';
    $('#ps-erd-list').innerHTML = ER.E.tables.filter(t => !q || t.id.toLowerCase().includes(q)).map(t => `<button type="button" data-t="${esc(t.id)}" class="${t.id === sel ? 'on' : ''}"><span class="ps-sq" style="background:${t.old ? '#64748b' : '#0b1d4a'};${t.old ? '' : 'outline:1.5px solid #e4c47c'}"></span>${esc(t.id)}<span class="ps-cnt">${t.cols.length}</span></button>`).join('');
  }
  function anchor(t, colName, side) {
    const i = Math.max(0, t.cols.findIndex(c => c[0] === colName));
    return { x: side > 0 ? t.x + TW : t.x, y: t.y + HH + i * RH + RH / 2 };
  }
  function relPath(r) {
    const a = tById(r[0]), b = tById(r[2]); if (!a || !b) return '';
    const hl = sel && (r[0] === sel || r[2] === sel) ? ' hl' : '';
    if (Math.abs(a.x - b.x) < TW * 0.6) {   // bertumpuk vertikal: keluar & masuk dari kiri
      const p1 = anchor(a, r[1], -1), p2 = anchor(b, r[3], -1), d = 46;
      return `<path class="relp${hl}" d="M${p1.x},${p1.y} C${p1.x - d},${p1.y} ${p2.x - d},${p2.y} ${p2.x},${p2.y} M${p1.x - 14},${p1.y} L${p1.x},${p1.y - 7} M${p1.x - 14},${p1.y} L${p1.x},${p1.y + 7} M${p2.x - 9},${p2.y - 7} V${p2.y + 7}"/>`;
    }
    const s = (b.x + TW / 2) >= (a.x + TW / 2) ? 1 : -1;
    const p1 = anchor(a, r[1], s), p2 = anchor(b, r[3], -s), dx = Math.max(40, Math.abs(p2.x - p1.x) / 2);
    return `<path class="relp${hl}" d="M${p1.x},${p1.y} C${p1.x + s * dx},${p1.y} ${p2.x - s * dx},${p2.y} ${p2.x},${p2.y} M${p1.x + s * 14},${p1.y} L${p1.x},${p1.y - 7} M${p1.x + s * 14},${p1.y} L${p1.x},${p1.y + 7} M${p2.x - s * 9},${p2.y - 7} V${p2.y + 7}"/>`;
  }
  function erdDraw() {
    const ICO = { pk: '', fk: '', uq: '' };
    const tables = ER.E.tables.map(t => `<g class="t ${t.old ? 'old' : 'new'}${t.id === sel ? ' sel' : ''}" data-id="${esc(t.id)}" transform="translate(${t.x},${t.y})">
        <rect class="card2" width="${TW}" height="${tH(t)}" rx="10" filter="url(#ps-sh)"/>
        <path class="hd" d="M0,${HH} V10 Q0,0 10,0 H${TW - 10} Q${TW},0 ${TW},10 V${HH} Z"/>
        <text class="hn" x="12" y="19.5">${esc(t.id)}</text>
        <text class="bdg" x="${TW - 10}" y="19" text-anchor="end">${t.old ? 'EXISTING' : 'NEW'}</text>
        ${t.cols.map((c, i) => `<g class="cl${relFrom && relFrom.t === t.id && relFrom.c === c[0] ? ' pick' : ''}" data-col="${esc(c[0])}" transform="translate(0,${HH + i * RH})"><rect class="rowbg" width="${TW}" height="${RH}"/>${c[2] ? `<text class="ico ${c[2]}" x="12" y="15">${ICO[c[2]] || ''}</text>` : ''}<text class="cn" x="28" y="15">${esc(c[0])}</text><text class="ct" x="${TW - 10}" y="15" text-anchor="end">${esc(c[1])}</text></g>`).join('')}
      </g>`).join('');
    svg.innerHTML = `<defs><filter id="ps-sh" x="-10%" y="-10%" width="120%" height="130%"><feDropShadow dx="0" dy="4" stdDeviation="5" flood-color="#0f172a" flood-opacity=".12"/></filter></defs>
      <g transform="translate(${view.x},${view.y}) scale(${view.k})"><g>${ER.E.rels.map(relPath).join('')}</g><g>${tables}</g></g>`;
  }
  function erdFit() {
    const r = svg.getBoundingClientRect();
    if (!ER.E.tables.length || !r.width) { view = { x: 40, y: 40, k: 1 }; erdDraw(); return; }
    let x0 = 1e9, y0 = 1e9, x1 = -1e9, y1 = -1e9;
    ER.E.tables.forEach(t => { x0 = Math.min(x0, t.x); y0 = Math.min(y0, t.y); x1 = Math.max(x1, t.x + TW); y1 = Math.max(y1, t.y + tH(t)); });
    const k = clamp(Math.min((r.width - 80) / (x1 - x0), (r.height - 90) / (y1 - y0)), .3, 1.25);
    view = { k, x: (r.width - (x1 - x0) * k) / 2 - x0 * k, y: (r.height - 40 - (y1 - y0) * k) / 2 - y0 * k };
    erdDraw();
  }
  function zoomAt(f, cx, cy) { const k = clamp(view.k * f, .25, 2.2); view.x = cx - (cx - view.x) * (k / view.k); view.y = cy - (cy - view.y) * (k / view.k); view.k = k; erdDraw(); }

  // simpan otomatis (hanya draft)
  function dirty() {
    if (!ER.editable) return;
    $('#ps-erd-saved').textContent = 'Unsaved changes...';
    clearTimeout(saveTimer); saveTimer = setTimeout(saveNow, 900);
  }
  function saveNow() {
    saveTimer = null;
    if (!ER.editable || !ER.cur) return Promise.resolve();
    $('#ps-erd-saved').textContent = 'Saving...';
    const id = ER.cur.id;
    saving = post('erd_save', { id, json: JSON.stringify(ER.E) }).then(() => {
      const t = new Date(); $('#ps-erd-saved').innerHTML = `<i class="fas fa-check"></i> Saved ${String(t.getHours()).padStart(2, '0')}:${String(t.getMinutes()).padStart(2, '0')}`;
      const n = ER.E.tables.filter(x => !x.old).length, e = S.erds[String(ER.v.id)];
      if (e && +e.id === +id) { e.n_new = n; e.n_old = ER.E.tables.length - n; notify(); }
    }).catch(e => { $('#ps-erd-saved').textContent = 'Not saved'; fail(e); });
    return saving;
  }
  function flush() { if (saveTimer) { clearTimeout(saveTimer); return saveNow(); } return saving || Promise.resolve(); }

  function setRelMode(on) {
    relMode = !!on && ER.editable; relFrom = null;
    if (!svg) return;
    $('#ps-erd-rel').classList.toggle('pri', relMode);
    const h = $('#ps-erd-hint'); h.classList.toggle('on', relMode);
    h.textContent = 'Click the FK column (the "many" side), then the column it points to. Esc to cancel.';
    erdDraw();
  }
  function pickRel(tid, cname) {
    if (!relFrom) { relFrom = { t: tid, c: cname }; $('#ps-erd-hint').textContent = `${tid}.${cname} → now click the column it points to`; erdDraw(); return; }
    if (relFrom.t === tid) { toast('Pick a column in another table', 'info'); return; }
    ER.E.rels.push([relFrom.t, relFrom.c, tid, cname]);
    const t = tById(relFrom.t), c = t && t.cols.find(x => x[0] === relFrom.c); if (c && !c[2] && !t.old) c[2] = 'fk';
    toast(`Relation added: ${relFrom.t}.${relFrom.c} → ${tid}.${cname}`, 'info');
    setRelMode(false); erdAll(); dirty();
  }
  function erdInsp() {
    const box = $('#ps-erd-insp'), t = sel && tById(sel), ed = ER.editable;
    if (!t) {
      $('#ps-erd-insp-h').textContent = 'ERD detail';
      const cur = ER.cur;
      if (!cur) { box.innerHTML = ''; return; }
      const nNew = ER.E.tables.filter(x => !x.old).length;
      const rv = ER.reviews.filter(r => +r.ref_id === +cur.id);
      box.innerHTML = `<label>Version</label><div>v${cur.version} ${chip(cur.status)}</div>
        <label>Tables</label><div style="font-size:12px">${nNew} new (needs migration) · ${ER.E.tables.length - nNew} existing · ${ER.E.rels.length} relation(s)</div>
        ${cur.status === 'approved' && nNew ? `<label>Migration SQL on production</label>${cur.sql_run_date ? `<div style="font-size:12px"><i class="fas fa-check" style="color:#16a34a"></i> Marked as run ${esc(fmtD(cur.sql_run_date, true))} <button type="button" class="ps-link" data-sqlrun="0">Undo</button></div>` : `<div style="font-size:12px;color:#92400e">Not marked as run yet. <button type="button" class="ps-link" data-sqlrun="1">Mark as run</button></div>`}` : ''}
        <div class="ps-ro"><i class="fas fa-mouse-pointer"></i><span>Click a table to see its columns${ed ? ' and edit them' : ''}.</span></div>
        <label>History</label>${rv.length ? rv.map(r => `<div class="ps-rv ${r.action}" style="margin:0 0 8px"><div class="ps-bub"><div class="ps-w">${chip(r.action)}<span>${esc(fmtD(r.created_date, true))}</span></div>${esc(r.note || '')}</div></div>`).join('') : '<div style="font-size:12px;color:#94a3b8">No decisions yet.</div>'}`;
      return;
    }
    $('#ps-erd-insp-h').textContent = 'Table detail';
    const rels = ER.E.rels.map((r, i) => ({ r, i })).filter(o => o.r[0] === t.id || o.r[2] === t.id);
    const relHtml = rels.length ? `<label>Relations</label>${rels.map(o => `<div class="ps-relro"><div>${esc(o.r[0])}.${esc(o.r[1])}<span>→ ${esc(o.r[2])}.${esc(o.r[3])}</span></div>${ed ? `<button type="button" class="ps-rm" data-rrm="${o.i}" title="Remove relation"><i class="fas fa-times"></i></button>` : ''}</div>`).join('')}` : '';
    if (t.old || !ed) {
      box.innerHTML = `<label>Table</label><div style="font-weight:600;font-size:13px;overflow-wrap:anywhere">${esc(t.id)}</div>
        ${t.old ? '<div class="ps-ro"><i class="fas fa-lock"></i><span>Existing table, pulled from the live database. Read only — the ERD only shows how the new tables connect to it.</span></div>' : '<div class="ps-ro"><i class="fas fa-lock"></i><span>This version is not a draft. Use <b>Edit as new version</b> to change it.</span></div>'}
        <label>Columns (${t.cols.length})</label>${t.cols.map(c => `<div class="ps-colro"><span>${c[2] ? `<b>${c[2].toUpperCase()}</b> ` : ''}${esc(c[0])}</span><span>${esc(c[1])}</span></div>`).join('')}${relHtml}
        ${ed ? '<div style="margin-top:12px"><button type="button" class="ps-btn sm no" id="ps-t-rm"><i class="fas fa-trash"></i>Remove from ERD</button></div>' : ''}`;
    } else {
      box.innerHTML = `<label>Table name</label><input type="text" id="ps-t-name" value="${esc(t.id)}">
        <label>Columns</label><div id="ps-t-cols">${t.cols.map((c, i) => `<div class="ps-colrow" data-i="${i}"><input type="text" value="${esc(c[0])}" data-f="0"><select data-f="1" class="no-select2">${[...new Set([c[1], ...TYPES])].map(x => `<option${x === c[1] ? ' selected' : ''}>${esc(x)}</option>`).join('')}</select><select data-f="2" class="no-select2"><option value="">—</option>${['pk', 'fk', 'uq'].map(x => `<option value="${x}"${x === c[2] ? ' selected' : ''}>${x.toUpperCase()}</option>`).join('')}</select><button type="button" class="ps-rm" title="Remove column"><i class="fas fa-times"></i></button></div>`).join('')}</div>
        <button type="button" class="ps-btn sm" id="ps-t-addcol" style="margin-top:4px"><i class="fas fa-plus"></i>Add column</button>${relHtml}
        <div style="margin-top:14px"><button type="button" class="ps-btn sm no" id="ps-t-rm"><i class="fas fa-trash"></i>Delete table</button></div>`;
      $('#ps-t-name').addEventListener('change', e => {
        const nv = e.target.value.trim().toLowerCase().replace(/[^a-z0-9_]/g, '_');
        if (!nv || (nv !== t.id && tById(nv))) { e.target.value = t.id; toast('That table name is already used', 'info'); return; }
        ER.E.rels.forEach(r => { if (r[0] === t.id) r[0] = nv; if (r[2] === t.id) r[2] = nv; });
        t.id = nv; sel = nv; erdAll(); dirty();
      });
      $('#ps-t-cols').addEventListener('change', e => {
        const row = e.target.closest('.ps-colrow'), c = t.cols[+row.dataset.i], f = +e.target.dataset.f, old = c[0];
        c[f] = f === 0 ? e.target.value.trim().replace(/[^A-Za-z0-9_]/g, '_') : e.target.value;
        if (f === 0) ER.E.rels.forEach(r => { if (r[0] === t.id && r[1] === old) r[1] = c[0]; if (r[2] === t.id && r[3] === old) r[3] = c[0]; });
        erdDraw(); dirty();
      });
      $('#ps-t-cols').addEventListener('click', e => {
        if (!e.target.closest('.ps-rm')) return;
        const i = +e.target.closest('.ps-colrow').dataset.i, nm = t.cols[i][0];
        t.cols.splice(i, 1);
        ER.E.rels = ER.E.rels.filter(r => !((r[0] === t.id && r[1] === nm) || (r[2] === t.id && r[3] === nm)));
        erdAll(); dirty();
      });
      $('#ps-t-addcol').addEventListener('click', () => { let n = 1; while (t.cols.some(c => c[0] === 'new_column' + (n > 1 ? '_' + n : ''))) n++; t.cols.push(['new_column' + (n > 1 ? '_' + n : ''), 'VARCHAR(100)', '']); erdAll(); dirty(); });
    }
    const rm = $('#ps-t-rm');
    if (rm) rm.addEventListener('click', () => { ER.E.tables = ER.E.tables.filter(x => x !== t); ER.E.rels = ER.E.rels.filter(r => r[0] !== t.id && r[2] !== t.id); sel = null; erdAll(); dirty(); });
  }
  function erdSQL() {
    const v = ER.v, cur = ER.cur || { version: 0 };
    const d = new Date(), ymd = d.getFullYear() + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0');
    const slug = v.name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
    const nw = ER.E.tables.filter(t => !t.old);
    let s = `/* ============================================================================\n   db_migrate_LIVE_${ymd}_${slug}.sql\n   Generated from ERD v${cur.version} (${v.name}) - NEW tables only.\n   SAFE TO RE-RUN: CREATE TABLE IF NOT EXISTS.\n   ============================================================================ */\n\n`;
    if (!nw.length) return s + '-- No new tables in this ERD.\n';
    nw.forEach(t => {
      const w = Math.max(...t.cols.map(c => c[0].length), 2) + 3;
      const lines = t.cols.map(c => '  ' + ('`' + c[0] + '`').padEnd(w) + ' ' + String(c[1]).padEnd(14) + (c[2] === 'pk' ? 'NOT NULL AUTO_INCREMENT' : 'DEFAULT NULL'));
      const pk = t.cols.find(c => c[2] === 'pk'); if (pk) lines.push('  PRIMARY KEY (`' + pk[0] + '`)');
      t.cols.filter(c => c[2] === 'uq').forEach(c => lines.push('  UNIQUE KEY `uq_' + c[0] + '` (`' + c[0] + '`)'));
      t.cols.filter(c => c[2] === 'fk').forEach(c => lines.push('  KEY `idx_' + c[0] + '` (`' + c[0] + '`)'));
      const refs = ER.E.rels.filter(r => r[0] === t.id).map(r => `   ${r[1]} -> ${r[2]}.${r[3]}`);
      s += (refs.length ? `/* relations (index only, no FOREIGN KEY constraint):\n${refs.join('\n')} */\n` : '') + 'CREATE TABLE IF NOT EXISTS `' + t.id + '` (\n' + lines.join(',\n') + '\n) ENGINE=InnoDB DEFAULT CHARSET=latin1;\n\n';
    });
    return s;
  }
  function openSql() {
    const fname = () => { const d = new Date(); return 'db_migrate_LIVE_' + d.getFullYear() + String(d.getMonth() + 1).padStart(2, '0') + String(d.getDate()).padStart(2, '0') + '_' + ER.v.name.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '') + '.sql'; };
    const cur = ER.cur, canMark = cur && cur.status === 'approved' && ER.E.tables.some(t => !t.old);
    dialog('<i class="fas fa-file-code" style="color:#16a34a"></i> Migration SQL <small>· new tables only, for the production checklist</small>', `<pre class="ps-sql">${esc(erdSQL())}</pre>`,
      (canMark ? (cur.sql_run_date ? `<span style="font-size:12px;color:#16a34a;margin-right:auto"><i class="fas fa-check"></i> Marked as run ${esc(fmtD(cur.sql_run_date, true))}</span>` : '<button type="button" class="ps-btn" id="ps-sql-mark" style="margin-right:auto"><i class="fas fa-check-double"></i>Mark as run on production</button>') : '') +
      '<button type="button" class="ps-btn" id="ps-sql-copy"><i class="far fa-copy"></i>Copy</button><button type="button" class="ps-btn pri" id="ps-sql-dl"><i class="fas fa-download"></i>Download .sql</button>');
    $('#ps-sql-copy').onclick = () => { if (navigator.clipboard) navigator.clipboard.writeText(erdSQL()).then(() => toast('SQL copied')); };
    $('#ps-sql-dl').onclick = () => { const a = document.createElement('a'); a.href = URL.createObjectURL(new Blob([erdSQL()], { type: 'text/plain' })); a.download = fname(); a.click(); };
    const mk = $('#ps-sql-mark');
    if (mk) mk.onclick = () => post('erd_sql_done', { id: cur.id, on: 1 }).then(() => { closeDialog(); toast('Marked as run on production'); return erdReload(cur.id); }).catch(fail);
  }
  function openPull() {
    dialog('<i class="fas fa-database" style="color:#2563eb"></i> Pull tables from database <small>· production, read only</small>',
      `<div class="ps-search" style="margin:0 0 8px"><i class="fas fa-search"></i><input type="text" id="ps-pull-q" placeholder="Search table name..."></div><div id="ps-pull-l"><div class="ps-empty" style="padding:14px"><i class="fas fa-circle-notch fa-spin"></i></div></div>
       <div class="ps-ro" style="margin-top:10px"><i class="fas fa-info-circle"></i><span>Columns are read from INFORMATION_SCHEMA, so the ERD matches the real table. Nothing is written to the database.</span></div>`,
      '<button type="button" class="ps-btn" id="ps-pull-cancel">Cancel</button><button type="button" class="ps-btn pri" id="ps-pull-ok"><i class="fas fa-plus"></i>Add selected</button>');
    const picked = new Set();
    let tq = null, seq = 0;
    const draw = () => { const my = ++seq; return post('db_tables', { q: $('#ps-pull-q').value.trim() }).then(j => {
      if (my !== seq || !$('#ps-pull-l')) return;   // jawaban lama datang terlambat: abaikan
      const rows = j.tables || [];
      $('#ps-pull-l').innerHTML = rows.length ? rows.map(r => { const has = !!tById(r.t); return `<label class="ps-pull-row${has ? ' dis' : ''}"><input type="checkbox" value="${esc(r.t)}" ${has ? 'disabled checked' : picked.has(r.t) ? 'checked' : ''}><i class="fas fa-table" style="color:#64748b"></i>${esc(r.t)}<small>${has ? 'already in ERD' : (r.r != null ? '±' + Number(r.r).toLocaleString('en-US') + ' rows' : '')}</small></label>`; }).join('')
        : '<div class="ps-empty" style="padding:14px"><b>No table found</b></div>';
    }).catch(fail); };
    draw();
    $('#ps-pull-q').addEventListener('input', () => { clearTimeout(tq); tq = setTimeout(draw, 300); });
    $('#ps-pull-l').addEventListener('change', e => { if (e.target.type === 'checkbox') { if (e.target.checked) picked.add(e.target.value); else picked.delete(e.target.value); } });
    $('#ps-pull-cancel').onclick = closeDialog;
    $('#ps-pull-ok').onclick = () => {
      const ids = [...picked].filter(id => !tById(id));
      if (!ids.length) { toast('Tick at least one table', 'info'); return; }
      post('db_columns', { tables: JSON.stringify(ids) }).then(j => {
        let y = ER.E.tables.length ? 40 + Math.max(...ER.E.tables.map(t => t.y + tH(t))) : 40;
        ids.forEach((id, i) => { const cols = (j.columns || {})[id] || []; ER.E.tables.push({ id, old: true, x: 40 + (i % 3) * 300, y: y + Math.floor(i / 3) * 240, cols }); });
        closeDialog(); sel = ids[0]; erdAll(); erdFit(); dirty();
        toast(ids.length + ' table(s) added from the database');
      }).catch(fail);
    };
  }
  function askNote(title) {
    return Swal.fire({ title, input: 'textarea', inputPlaceholder: 'Write what needs to change...', showCancelButton: true, confirmButtonText: 'Send', confirmButtonColor: '#b91c1c', inputValidator: v => (!v || !v.trim()) ? 'Write what needs to change first' : undefined })
      .then(r => r.isConfirmed ? r.value.trim() : null);
  }
  function bindErd() {
    svg = $('#ps-erd-svg');
    $('#ps-erd-find').addEventListener('input', erdList);
    $('#ps-erd-list').addEventListener('click', e => {
      const b = e.target.closest('button'); if (!b) return;
      sel = b.dataset.t; const t = tById(sel), r = svg.getBoundingClientRect();
      view.x = r.width / 2 - (t.x + TW / 2) * view.k; view.y = r.height / 2 - (t.y + tH(t) / 2) * view.k;
      erdList(); erdDraw(); erdInsp();
    });
    $('#ps-z-in').addEventListener('click', () => { const r = svg.getBoundingClientRect(); zoomAt(1.2, r.width / 2, r.height / 2); });
    $('#ps-z-out').addEventListener('click', () => { const r = svg.getBoundingClientRect(); zoomAt(1 / 1.2, r.width / 2, r.height / 2); });
    $('#ps-z-fit').addEventListener('click', erdFit);
    svg.addEventListener('wheel', e => { e.preventDefault(); const r = svg.getBoundingClientRect(); zoomAt(e.deltaY < 0 ? 1.1 : 1 / 1.1, e.clientX - r.left, e.clientY - r.top); }, { passive: false });
    let drag = null;
    svg.addEventListener('pointerdown', e => {
      const g = e.target.closest('.t'), col = e.target.closest('.cl');
      if (relMode && g && col) { pickRel(g.dataset.id, col.dataset.col); return; }
      try { svg.setPointerCapture(e.pointerId); } catch (x) { /* abaikan */ }
      if (g) {
        const t = tById(g.dataset.id); sel = t.id;
        drag = { t, sx: e.clientX, sy: e.clientY, ox: t.x, oy: t.y, moved: false };
        erdList(); erdInsp();
      } else { drag = { pan: true, sx: e.clientX, sy: e.clientY, ox: view.x, oy: view.y }; svg.classList.add('ps-panning'); }
    });
    svg.addEventListener('pointermove', e => {
      if (!drag) return;
      const dx = e.clientX - drag.sx, dy = e.clientY - drag.sy;
      if (drag.pan) { view.x = drag.ox + dx; view.y = drag.oy + dy; erdDraw(); return; }
      if (!ER.editable) return;   // versi bukan draft: tidak bisa digeser
      if (!drag.moved && Math.abs(dx) + Math.abs(dy) < 3) return;
      drag.t.x = Math.round((drag.ox + dx / view.k) / 10) * 10; drag.t.y = Math.round((drag.oy + dy / view.k) / 10) * 10; drag.moved = true;
      erdDraw();
    });
    svg.addEventListener('pointerup', () => { if (drag && !drag.pan) { erdDraw(); if (drag.moved) dirty(); } drag = null; svg.classList.remove('ps-panning'); });

    $('#ps-erd-rel').addEventListener('click', () => setRelMode(!relMode));
    $('#ps-erd-add').addEventListener('click', () => {
      let n = 1; while (tById('new_table_' + n)) n++;
      // diletakkan di kanan tabel paling kanan, sejajar tabel paling atas (tidak menumpuk)
      const xs = ER.E.tables.map(x => x.x + TW), ys = ER.E.tables.map(x => x.y);
      const t = { id: 'new_table_' + n, x: xs.length ? Math.max(...xs) + 70 : 40, y: ys.length ? Math.min(...ys) : 40, cols: [['id', 'INT(11)', 'pk'], ['create_by', 'VARCHAR(100)', ''], ['create_date', 'DATETIME', '']] };
      ER.E.tables.push(t); sel = t.id; erdAll(); erdFit(); dirty();
      setTimeout(() => { const i = $('#ps-t-name'); if (i) { i.focus(); i.select(); } }, 30);
    });
    $('#ps-erd-pull').addEventListener('click', openPull);
    $('#ps-erd-sql').addEventListener('click', openSql);
    $('#ps-erd-ver').addEventListener('change', e => { flush().then(() => erdFetch(+e.target.value)).then(() => { sel = null; erdAll(); erdFit(); }).catch(fail); });
    $('#ps-erd-empty').addEventListener('click', e => {
      if (e.target.closest('#ps-erd-start')) post('erd_new', { project_id: ER.v.id }).then(j => { toast('ERD v' + j.version + ' started'); return erdReload(j.id); }).then(() => requestAnimationFrame(erdFit)).catch(fail);
      const sk = e.target.closest('[data-eskip]');
      if (sk) post('skip', { project_id: ER.v.id, kind: 'erd', on: sk.dataset.eskip }).then(() => { toast(sk.dataset.eskip === '1' ? 'ERD marked as not needed' : 'ERD is needed again'); return load(); }).then(() => erdAll()).catch(fail);
      if (e.target.closest('[data-pull]')) openPull();
    });
    $('#ps-erd-insp').addEventListener('click', e => {
      const rr = e.target.closest('[data-rrm]');
      if (rr && ER.editable) { ER.E.rels.splice(+rr.dataset.rrm, 1); erdAll(); dirty(); return; }
      const sq = e.target.closest('[data-sqlrun]');
      if (sq) post('erd_sql_done', { id: ER.cur.id, on: sq.dataset.sqlrun }).then(() => { toast(sq.dataset.sqlrun === '1' ? 'Marked as run on production' : 'Mark removed'); return erdReload(ER.cur.id); }).catch(fail);
    });
    $('#ps-erd-main').addEventListener('click', () => {
      const act = $('#ps-erd-main').dataset.act, cur = ER.cur; if (!cur) return;
      if (act === 'submit') {
        if (!ER.E.tables.length) { toast('Add at least one table first', 'info'); return; }
        flush().then(() => post('erd_submit', { id: cur.id })).then(() => { toast('ERD submitted for review'); return erdReload(cur.id); }).catch(fail);
      } else if (act === 'new') {
        post('erd_new', { project_id: ER.v.id }).then(j => { toast('Editing v' + j.version + ' (draft)'); return erdReload(j.id); }).catch(fail);
      }
    });
    $('#ps-erd-ok').addEventListener('click', () => {
      const cur = ER.cur;
      post('erd_review', { id: cur.id, act: 'approved', note: 'Approved.' }).then(() => { toast('ERD approved'); return erdReload(cur.id); }).catch(fail);
    });
    $('#ps-erd-no').addEventListener('click', () => {
      const cur = ER.cur;
      askNote('What needs to change in this ERD?').then(note => { if (note) return post('erd_review', { id: cur.id, act: 'changes', note }).then(() => { toast('Changes requested'); return erdReload(cur.id); }); }).catch(fail);
    });
  }

  // data ERD untuk meja Data Room (3D): versi terakhir saja
  function erdData(pid) {
    if (S.ready === false || !S.erds[String(pid)]) return Promise.resolve(null);
    return post('erd_get', { project_id: pid }).then(j => j.erd && j.erd.data ? j.erd.data : null).catch(() => null);
  }

  window.ProjectStudio = {
    init: function (o) { opt = Object.assign(opt, o || {}); buildDom(); renderCard(); },
    load: load,
    projectsChanged: notify,
    onChange: function (fn) { listeners.push(fn); return function () { listeners = listeners.filter(x => x !== fn); }; },
    list: list,
    find: find,
    ready: function () { return S.ready; },
    counts: function () { const l = list(); return { studio: l.filter(v => v.mock.n > 0).length, data: l.filter(v => v.erd.n > 0).length }; },
    openMockup: openMockup,
    openErd: openErd,
    erdData: erdData,
    luccaItems: luccaItems,
    luccaMood: luccaMood,
    luccaIntro: luccaIntro,
    go: go,
    ST: ST, ST_COL: ST_COL
  };
})();
