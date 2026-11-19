/**
 * Sudut DESIGN STUDIO di dalam perpustakaan 3D (menu Project, mode 3D).
 * User minta semuanya dalam SATU ruangan: sudut ini dipasang library3d.js di
 * ujung kanan deretan rak (dinding melengkungnya diperpanjang).
 *
 *   EASEL      1 easel = 1 project aktif; papan = mockup versi terakhir
 *              kosong = belum ada mockup, jepit kuning = menunggu persetujuan,
 *              catatan tempel = diminta revisi, stempel hijau = disetujui
 *   GULUNGAN   cetak biru di kaki easel = ERD project itu (pita = status)
 *   MEJA       meja cetak biru: ERD yang dipilih, tabel emas = baru, abu = lama
 *   LUCCA      laporannya digabung ke briefing Lucca perpustakaan (wing.lucca)
 *
 * createWing(api) -> objek "wing" untuk createLibrary(host, { wing }).
 * api: list(), ready(), luccaItems(where), go(g), openMockup(pid),
 *      openErd(pid, tableId), erdData(pid) -> Promise
 * Data & panelnya ada di project-studio.js (window.ProjectStudio).
 */
import * as THREE from '../lib/three-0.160.0/three.module.min.js';

const TW = 230, HH = 30, RH = 22;   // ukuran kartu tabel ERD (sama dgn editor 2D)
const MAX = 6, STEP = 1.75;          // easel paling banyak & jaraknya di sepanjang dinding
const KINDS = ['report', 'form', 'table', 'dash'];
const ST = { draft: 'Draft', review: 'In review', changes: 'Changes requested', approved: 'Approved', todo: 'Not started', skip: 'Not needed' };
const ST_COL = { draft: '#94a3b8', review: '#f59e0b', changes: '#ef4444', approved: '#10b981', todo: '#94a3b8', skip: '#94a3b8' };

const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
const esc = s => String(s == null ? '' : s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
const MON = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const fmtD = s => { const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(s || '')); return m ? +m[3] + ' ' + MON[+m[2] - 1] + ' ' + m[1] : String(s || ''); };
function rng(seed) { let s = (seed >>> 0) || 1; return () => (s = (Math.imul(s, 1664525) + 1013904223) >>> 0) / 4294967296; }
function rr(g, x, y, w, h, r) { g.beginPath(); g.moveTo(x + r, y); g.arcTo(x + w, y, x + w, y + h, r); g.arcTo(x + w, y + h, x, y + h, r); g.arcTo(x, y + h, x, y, r); g.arcTo(x, y, x + w, y, r); g.closePath(); }

/* gambar kecil "layar" di papan easel. Isi mockup aslinya (HTML) tidak bisa
   digambar ke kanvas, jadi papan memakai sketsa tata letak + judul project. */
function drawMock(g, W, H, v) {
  const r = rng(v.id * 97 + v.mock.v), kind = KINDS[v.id % KINDS.length];
  const box = (x, y, w, h, fill, o = {}) => { g.fillStyle = fill; rr(g, x, y, w, h, o.rad == null ? 5 : o.rad); g.fill(); if (o.stroke) { g.strokeStyle = o.stroke; g.lineWidth = 1.5; g.stroke(); } };
  const txt = (x, y, s, size, col, weight, maxW) => { g.font = `${weight || 500} ${size}px "PJ Poppins","Segoe UI",sans-serif`; g.fillStyle = col; g.textBaseline = 'middle'; g.textAlign = 'left'; g.fillText(s, x, y, maxW); };
  g.fillStyle = '#eef2f7'; g.fillRect(0, 0, W, H);
  box(0, 0, W, 34, '#0b1d4a', { rad: 0 }); g.fillStyle = '#e5a913'; g.fillRect(0, 32, W, 3);
  txt(16, 17, 'NDS', 15, '#fff', 700); ['Master', 'AP', 'Bank', 'Cash', 'Accounting'].forEach((m, i) => txt(80 + i * 74, 17, m, 12, 'rgba(255,255,255,.8)'));
  const P = 20; let y = 52;
  box(P, y, W - 2 * P, H - y - 18, '#fff', { rad: 10 });
  box(P, y, W - 2 * P, 38, '#1e3a8a', { rad: 10 });
  txt(P + 16, y + 19, v.name, 15, '#fff', 600, W - 2 * P - 32);
  y += 50;
  const fields = n => { for (let i = 0; i < n; i++) { const fx = P + 16 + i * ((W - 2 * P - 150) / n); box(fx, y, 60, 6, '#cbd5e1'); box(fx, y + 10, (W - 2 * P - 150) / n - 14, 26, '#fff', { stroke: '#cbd5e1' }); } box(W - P - 120, y + 10, 50, 26, '#2563eb'); box(W - P - 64, y + 10, 50, 26, '#16a34a'); y += 52; };
  const table = (cols, rows, status) => {
    const tx = P + 14, tw = W - 2 * P - 28, cw = tw / cols;
    box(tx, y, tw, 24, '#eef2f9', { rad: 3 }); y += 28;
    for (let i = 0; i < rows && y < H - 34; i++) {
      for (let c = 0; c < cols; c++) {
        if (status && c === cols - 1) { box(tx + 8 + c * cw, y + 4, 56, 15, i % 4 === 3 ? '#fee2e2' : '#d1fae5', { rad: 7 }); continue; }
        g.fillStyle = c === 0 ? '#475569' : '#94a3b8'; g.fillRect(tx + 8 + c * cw, y + 9, (c === 0 ? .55 : .4 + r() * .3) * (cw - 16), 6);
      }
      g.fillStyle = '#e3e9f2'; g.fillRect(tx, y + 22, tw, 1); y += 24;
    }
  };
  if (kind === 'report') { fields(5); table(6, 9); }
  else if (kind === 'table') { fields(3); table(5, 9, true); }
  else if (kind === 'form') {
    for (let i = 0; i < 4; i++) { const fx = P + 16 + (i % 2) * ((W - 2 * P) / 2), fy = y + Math.floor(i / 2) * 44; box(fx, fy - 4, 70, 6, '#cbd5e1'); box(fx, fy + 10, (W - 2 * P) / 2 - 34, 24, '#fff', { stroke: '#cbd5e1' }); }
    y += 96; table(5, 5); box(W - P - 150, H - 58, 62, 26, '#e2e8f0'); box(W - P - 80, H - 58, 62, 26, '#16a34a');
  } else {
    for (let i = 0; i < 4; i++) { const cw = (W - 2 * P - 40) / 4; box(P + 16 + i * (cw + 3), y, cw - 8, 56, '#fff', { stroke: '#e2e8f0' }); box(P + 26 + i * (cw + 3), y + 12, 50, 6, '#cbd5e1'); box(P + 26 + i * (cw + 3), y + 30, 70, 14, i > 1 ? '#fca5a5' : '#334155', { rad: 3 }); }
    [62, 48, 30, 22, 12, 8].forEach((hh, i) => box(P + 40 + i * 92, H - 40 - hh * 2.4, 56, hh * 2.4, '#2563eb', { rad: 4 }));
  }
  if (v.mock.st === 'approved') {   // stempel hijau
    g.save(); g.translate(W - 140, H - 76); g.rotate(-.18);
    g.strokeStyle = 'rgba(5,150,105,.85)'; g.lineWidth = 5; rr(g, -92, -28, 184, 56, 10); g.stroke();
    g.fillStyle = 'rgba(5,150,105,.85)'; g.font = '700 28px "PJ Poppins",sans-serif'; g.textAlign = 'center'; g.textBaseline = 'middle'; g.fillText('APPROVED', 0, 2);
    g.restore();
  }
}
function emptyBoard(g, W, H, v) {
  g.fillStyle = '#fbfaf4'; g.fillRect(0, 0, W, H);
  g.setLineDash([14, 10]); g.strokeStyle = '#cbd5e1'; g.lineWidth = 4; g.strokeRect(24, 24, W - 48, H - 48); g.setLineDash([]);
  g.textAlign = 'center'; g.fillStyle = '#94a3b8'; g.font = '600 26px "PJ Poppins",sans-serif';
  g.fillText(!v ? 'No active project' : v.mock.st === 'skip' ? 'Mockup not needed' : 'No mockup yet', W / 2, H / 2 - 10);
  g.font = '18px "PJ Poppins",sans-serif'; if (v) g.fillText(v.name, W / 2, H / 2 + 26, W - 80);
}

export function createWing(api) {
  let C = null, W = null;   // C = konteks dari library3d.js, W = isi sudut yang sedang dibangun
  const rows = () => api.list().slice(0, MAX);
  const count = () => Math.max(1, rows().length);
  const sig = () => rows().map(v => v.id).join(',');

  function canvasTex(w, h, draw) {
    const c = document.createElement('canvas'); c.width = w; c.height = h;
    draw(c.getContext('2d'), w, h);
    const t = new THREE.CanvasTexture(c); t.colorSpace = THREE.SRGBColorSpace; t.anisotropy = 8;
    return t;
  }
  function textPlate(lines, o = {}) {
    const w = o.w || 512, h = o.h || 128;
    return canvasTex(w, h, g => {
      if (o.bg) { g.fillStyle = o.bg; g.fillRect(0, 0, w, h); }
      if (o.border) { g.strokeStyle = o.border; g.lineWidth = 6; g.strokeRect(6, 6, w - 12, h - 12); }
      g.textAlign = 'center'; g.textBaseline = 'middle';
      lines.forEach(l => { let sz = parseFloat(l.font.match(/(\d+)px/)[1]); g.font = l.font; g.fillStyle = l.col; while (g.measureText(l.text).width > w - 30 && sz > 10) { sz--; g.font = l.font.replace(/\d+px/, sz + 'px'); } g.fillText(l.text, w / 2, l.y * h); });
    });
  }
  // semua geometri/bahan/tekstur didaftarkan ke library (dibuang saat rak dibangun ulang)
  const T = o => C.track(o);
  const mesh = (geo, mat, x = 0, y = 0, z = 0) => { const m = new THREE.Mesh(geo, mat); m.position.set(x, y, z); m.castShadow = true; m.receiveShadow = true; return m; };
  // buang dari daftar klik milik library - diubah di tempat (library memegang array yang sama)
  const unpick = pred => { for (let i = C.pickables.length - 1; i >= 0; i--) if (pred(C.pickables[i])) C.pickables.splice(i, 1); };

  /* ---------------- easel ---------------- */
  function paintEasel(b) {
    const v = b.v, st = v ? v.mock.st : 'todo', has = v && v.mock.v > 0;
    if (b.board) { const old = b.board; unpick(p => p === old); b.holder.remove(old); }
    while (b.extras.children.length) b.extras.remove(b.extras.children[0]);
    const tex = T(canvasTex(800, 500, (g, w, h) => { if (has) drawMock(g, w, h, v); else emptyBoard(g, w, h, v); }));
    const face = T(new THREE.MeshStandardMaterial({ map: tex, roughness: .82, emissive: '#ffffff', emissiveMap: tex, emissiveIntensity: .18 }));
    const board = new THREE.Mesh(b.boardGeo, [W.paper, W.paper, W.paper, W.paper, face, W.paper]);
    board.castShadow = true;
    if (v) { board.userData = { kind: 'wing', w: 'easel', key: 'e' + v.id, pid: v.id }; C.pickables.push(board); }
    b.holder.add(board); b.board = board; b.face = face;
    if (st === 'review') {   // jepit kuning = menunggu persetujuan
      b.extras.add(mesh(W.clipGeo, W.clipMat, 0, .48, .04));
      const ring = mesh(W.ringGeo, C.mats.brass, 0, .54, .04); b.extras.add(ring);
    }
    if (st === 'changes') {  // catatan tempel = diminta revisi
      const r = rng(v.id * 7);
      for (let i = 0; i < 3; i++) {
        const t = T(canvasTex(128, 128, g => { g.fillStyle = i % 2 ? '#fecaca' : '#fde68a'; g.fillRect(0, 0, 128, 128); g.strokeStyle = '#7c2d12'; g.lineWidth = 4; for (let k = 0; k < 4; k++) { g.beginPath(); g.moveTo(14, 30 + k * 22); g.lineTo(110 - r() * 40, 30 + k * 22); g.stroke(); } }));
        const n = new THREE.Mesh(W.noteGeo, T(new THREE.MeshStandardMaterial({ map: t, roughness: .9 })));
        n.position.set(-.5 + i * .4 + r() * .1, .22 - r() * .38, .025); n.rotation.z = (r() - .5) * .4; b.extras.add(n);
      }
    }
    if (!has && v && st !== 'skip') {   // pensil di langkan: menunggu dibuat
      const pen = mesh(W.penGeo, W.penMat, .35, -.49, .12); pen.rotation.z = Math.PI / 2 - .1; b.extras.add(pen);
    }
    b.plateMat.map = T(textPlate([{ text: v ? v.name : 'No active project', font: '700 40px "PJ Poppins",sans-serif', col: '#2a1d05', y: .4 },
      { text: !v ? '' : has ? `Mockup v${v.mock.v} · ${ST[st]}` : ST[st] || 'No mockup yet', font: '600 28px "PJ Poppins",sans-serif', col: st === 'changes' ? '#991b1b' : st === 'approved' ? '#065f46' : '#5b4412', y: .78 }], { w: 640, h: 98, bg: '#dcc184' }));
    b.plateMat.needsUpdate = true;
  }
  // gulungan cetak biru = ERD project ini; pitanya = status ERD
  function paintRoll(b) {
    const v = b.v;
    const old = b.roll.children.slice(); unpick(p => old.includes(p));
    while (b.roll.children.length) b.roll.remove(b.roll.children[0]);
    b.bandMat = null;
    if (!v || !v.erd.v) return;
    const tube = mesh(W.rollGeo, W.rollMat); tube.rotation.z = Math.PI / 2;
    const band = mesh(W.bandGeo, T(new THREE.MeshStandardMaterial({ color: ST_COL[v.erd.st] || '#94a3b8', roughness: .5, emissive: ST_COL[v.erd.st] || '#000', emissiveIntensity: .25 }))); band.rotation.z = Math.PI / 2;
    const ud = { kind: 'wing', w: 'roll', key: 'r' + v.id, pid: v.id };
    tube.userData = band.userData = ud;
    b.roll.add(tube, band);
    C.pickables.push(tube, band);
    b.bandMat = band.material;
  }

  /* ---------------- meja ERD ---------------- */
  function tileTexFor(t) {
    const w = 256, h = 40 + t.cols.length * 22;
    return canvasTex(w, h, g => {
      g.fillStyle = t.old ? '#cbd5e1' : '#fff4d6'; g.fillRect(0, 0, w, h);
      if (!t.old) { g.strokeStyle = '#c9971f'; g.lineWidth = 8; g.strokeRect(4, 4, w - 8, h - 8); }
      g.fillStyle = t.old ? '#475569' : '#0b1d4a'; g.fillRect(0, 0, w, 36);
      if (!t.old) { g.fillStyle = '#e5a913'; g.fillRect(0, 33, w, 4); }
      g.fillStyle = '#fff'; g.font = '600 16px "PJ Poppins",sans-serif'; g.textBaseline = 'middle'; g.fillText(t.id, 10, 18, w - 20);
      t.cols.forEach((c, i) => { const y = 40 + i * 22 + 8; g.fillStyle = c[2] === 'pk' ? '#b7791f' : c[2] === 'fk' ? '#2563eb' : '#94a3b8'; g.fillRect(10, y - 4, 7, 7); g.fillStyle = '#1e293b'; g.font = '13px "PJ Poppins",sans-serif'; g.fillText(c[0], 24, y, w - 34); });
    });
  }
  const tHt = t => HH + t.cols.length * RH + 6;
  function anchor(t, colName, side) { const i = Math.max(0, t.cols.findIndex(c => c[0] === colName)); return { x: side > 0 ? t.x + TW : t.x, y: t.y + HH + i * RH + RH / 2 }; }
  // sumber daya balok tabel dibuang sendiri tiap ERD di meja diganti
  function clearTiles() {
    const G = W.tilesG;
    while (G.children.length) { const m = G.children[0]; G.remove(m); }
    W.tileTrash.forEach(o => o.dispose()); W.tileTrash = [];
    unpick(p => p.userData && p.userData.w === 'tile');
    W.pulses = [];
  }
  function layTiles(pid, e) {
    clearTiles();
    const keep = o => { W.tileTrash.push(o); return o; };
    const tables = e && Array.isArray(e.tables) ? e.tables.filter(t => t && Array.isArray(t.cols)) : [];
    W.cur = pid; W.curTables = tables.length;
    paintTablePlate();
    Object.values(W.easels).forEach(b => { if (b.bandMat) b.bandMat.emissiveIntensity = b.v && b.v.id === pid ? .9 : .25; });
    if (!tables.length) return;
    let x0 = 1e9, y0 = 1e9, x1 = -1e9, y1 = -1e9;
    tables.forEach(t => { x0 = Math.min(x0, t.x); y0 = Math.min(y0, t.y); x1 = Math.max(x1, t.x + TW); y1 = Math.max(y1, t.y + tHt(t)); });
    const s = Math.min((W.tw - .3) / (x1 - x0), (W.td - .25) / (y1 - y0)), cx = (x0 + x1) / 2, cy = (y0 + y1) / 2;
    const P = (x, y) => new THREE.Vector3((x - cx) * s, 0, (y - cy) * s);
    const pos = {}, now = performance.now() / 1000;
    tables.forEach((t, i) => {
      const w = TW * s, d = tHt(t) * s, tex = keep(tileTexFor(t));
      const side = keep(new THREE.MeshStandardMaterial({ color: t.old ? '#475569' : '#e4b440', roughness: .35, metalness: t.old ? .2 : .75, emissive: t.old ? '#000' : '#8a6410', emissiveIntensity: .45 }));
      const topM = keep(new THREE.MeshStandardMaterial({ map: tex, roughness: .6, emissive: '#ffffff', emissiveMap: tex, emissiveIntensity: .14 }));
      const m = new THREE.Mesh(keep(new THREE.BoxGeometry(w, .06, d)), [side, side, topM, side, side, side]);
      m.position.copy(P(t.x + TW / 2, t.y + tHt(t) / 2)); m.position.y = -.2; m.castShadow = true;
      m.userData = { kind: 'wing', w: 'tile', key: 't' + pid + ':' + t.id, pid, tid: t.id, old: !!t.old, ncol: t.cols.length, t0: now + i * .1 };
      W.tilesG.add(m); C.pickables.push(m); pos[t.id] = t;
    });
    const cyan = keep(new THREE.MeshBasicMaterial({ color: '#7fe3ff', transparent: true, opacity: .9 })), white = keep(new THREE.MeshBasicMaterial({ color: '#ffffff' }));
    (Array.isArray(e.rels) ? e.rels : []).forEach((r, i) => {
      const a = pos[r[0]], b = pos[r[2]]; if (!a || !b) return;
      const sg = (b.x + TW / 2) >= (a.x + TW / 2) ? 1 : -1, pa = anchor(a, r[1], sg), pb = anchor(b, r[3], -sg);
      const A = P(pa.x, pa.y), B = P(pb.x, pb.y); A.y = B.y = .07;
      const mid = A.clone().lerp(B, .5); mid.y = .15 + A.distanceTo(B) * .12;
      const curve = new THREE.QuadraticBezierCurve3(A, mid, B);
      W.tilesG.add(new THREE.Mesh(keep(new THREE.TubeGeometry(curve, 28, .006, 6)), cyan));
      const pulse = new THREE.Mesh(W.dotGeo, white); W.tilesG.add(pulse);
      W.pulses.push({ curve, m: pulse, off: i * .37 });
    });
  }
  function loadTable(pid) {
    const seq = ++W.seq;
    api.erdData(pid).then(e => { if (W && seq === W.seq) layTiles(pid, e); });
  }
  function paintTablePlate() {
    const v = W.cur ? rows().find(x => x.id === W.cur) : null;
    W.tablePlateMat.map = T(textPlate([{ text: v ? 'ERD · ' + v.name : 'ERD table', font: '700 40px "PJ Poppins",sans-serif', col: '#2a1d05', y: .4 },
      { text: v ? `v${v.erd.v} · ${ST[v.erd.st] || ''} · ${v.erd.nNew} new + ${v.erd.nOld} existing` : 'Click a blueprint roll', font: '600 26px "PJ Poppins",sans-serif', col: '#5b4412', y: .78 }], { w: 640, h: 98, bg: '#dcc184' }));
    W.tablePlateMat.needsUpdate = true;
  }

  /* ---------------- bangun sudut (dipanggil library3d.js tiap rak dibangun) ---------------- */
  function build(c) {
    C = c;
    const R = c.R, a0 = c.aStart, list = rows(), n = Math.max(1, list.length);
    const G = new THREE.Group(); c.lib.add(G);
    const at = (rad, a) => new THREE.Vector3(rad * Math.sin(a), 0, R - rad * Math.cos(a));
    // easel & meja menghadap ke depan ruangan (arah kamera), bukan ke pusat lengkung -
    // kalau ke pusat lengkung, dari kamera hanya tampak dari samping
    // separuh ke pusat lengkung (sejajar rak) + separuh ke depan (arah kamera): tidak saling tumpuk, tetap terbaca
    const faceFront = (o, a) => { const fr = Math.atan2(o.position.x * .3 - o.position.x, R + 8 - o.position.z); o.rotation.y = a == null ? fr : .55 * -a + .45 * fr; };
    W = {
      G, sig: sig(), easels: {}, cur: null, curTables: 0, seq: 0, pulses: [], tileTrash: [], hot: null,
      paper: T(new THREE.MeshStandardMaterial({ color: '#f4f1e8', roughness: .9 })),
      boardGeo: T(new THREE.BoxGeometry(1.5, .94, .04)), clipGeo: T(new THREE.BoxGeometry(.2, .1, .06)), ringGeo: T(new THREE.TorusGeometry(.05, .008, 6, 16, Math.PI)),
      clipMat: T(new THREE.MeshStandardMaterial({ color: '#f59e0b', roughness: .4, metalness: .2 })), noteGeo: T(new THREE.PlaneGeometry(.2, .2)),
      penGeo: T(new THREE.CylinderGeometry(.012, .012, .32, 6)), penMat: T(new THREE.MeshStandardMaterial({ color: '#f2c94c', roughness: .6 })),
      rollGeo: T(new THREE.CylinderGeometry(.045, .045, .56, 16)), bandGeo: T(new THREE.CylinderGeometry(.048, .048, .09, 16)),
      rollMat: T(new THREE.MeshStandardMaterial({ color: '#cfe3ff', roughness: .55, emissive: '#3b82f6', emissiveIntensity: .2 })),
      dotGeo: T(new THREE.SphereGeometry(.022, 10, 8))
    };
    const legGeo = T(new THREE.BoxGeometry(.06, 1, .06)), wood = c.mats.wood, brass = c.mats.brass;
    const beam = (grp, a, b, t) => { const d = new THREE.Vector3().subVectors(b, a), len = d.length(); const m = mesh(legGeo, wood); m.scale.set(t / .06, len, t / .06); m.position.copy(a).addScaledVector(d, .5); m.quaternion.setFromUnitVectors(new THREE.Vector3(0, 1, 0), d.normalize()); grp.add(m); };
    const ledgeGeo = T(new THREE.BoxGeometry(1.52, .05, .16)), lipGeo = T(new THREE.BoxGeometry(1.52, .05, .05)), plateGeo = T(new THREE.BoxGeometry(.98, .15, .025));
    const corners = [];

    // easel di sepanjang dinding lanjutan, menghadap tengah ruangan
    (list.length ? list : [null]).forEach((v, k) => {
      const a = a0 + (.9 + STEP * (k + .5)) / R, rE = R - .9;
      const e = new THREE.Group(); e.position.copy(at(rE, a)); faceFront(e, a); G.add(e);
      beam(e, new THREE.Vector3(-.6, 0, .14), new THREE.Vector3(-.1, 2.4, -.02), .055);
      beam(e, new THREE.Vector3(.6, 0, .14), new THREE.Vector3(.1, 2.4, -.02), .055);
      beam(e, new THREE.Vector3(0, 0, -.75), new THREE.Vector3(0, 2.25, -.07), .05);
      e.add(mesh(ledgeGeo, wood, 0, .98, .1), mesh(lipGeo, wood, 0, 1.03, .17));
      const holder = new THREE.Group(); holder.position.set(0, 1.53, .08); holder.rotation.x = -.1; e.add(holder);
      const plateMat = T(new THREE.MeshStandardMaterial({ roughness: .35, metalness: .55 }));
      const plate = new THREE.Mesh(plateGeo, [brass, brass, brass, brass, plateMat, brass]); plate.position.set(0, .9, .19); e.add(plate);
      const roll = new THREE.Group(); roll.position.set(.42, 1.07, .13); e.add(roll);   // gulungan ERD berbaring di langkan
      const b = { v, e, holder, plateMat, boardGeo: W.boardGeo, extras: new THREE.Group(), roll };
      holder.add(b.extras);
      if (v) { plate.userData = { kind: 'wing', w: 'easel', key: 'e' + v.id, pid: v.id }; c.pickables.push(plate); W.easels[v.id] = b; }
      paintEasel(b); paintRoll(b);
      c.addObs(e, 0, -.25, .7);
      corners.push(e.localToWorld(new THREE.Vector3(-.9, 0, .8)), e.localToWorld(new THREE.Vector3(.9, 2.5, -.8)));
    });

    // meja cetak biru di depan deretan easel
    const aMid = a0 + (.9 + STEP * n / 2) / R;
    const tw = clamp(1.4 + n * .3, 2.0, 3.0), td = 1.45;
    const tb = new THREE.Group(); tb.position.copy(at(R - 3.4, aMid)); faceFront(tb); G.add(tb);
    const dark = c.mats.dark || wood;
    [[-1, -1], [1, -1], [-1, 1], [1, 1]].forEach(q => tb.add(mesh(T(new THREE.BoxGeometry(.12, .84, .12)), dark, q[0] * (tw / 2 - .12), .42, q[1] * (td / 2 - .12))));
    tb.add(mesh(T(new THREE.BoxGeometry(tw + .1, .08, td + .1)), dark, 0, .88, 0), mesh(T(new THREE.BoxGeometry(tw + .16, .03, td + .16)), brass, 0, .855, 0));
    const bp = T(canvasTex(1024, 640, (g, w, h) => {
      g.fillStyle = '#0d3b7d'; g.fillRect(0, 0, w, h);
      g.strokeStyle = 'rgba(160,210,255,.16)'; g.lineWidth = 1; for (let x = 0; x < w; x += 16) { g.beginPath(); g.moveTo(x, 0); g.lineTo(x, h); g.stroke(); } for (let y = 0; y < h; y += 16) { g.beginPath(); g.moveTo(0, y); g.lineTo(w, y); g.stroke(); }
      g.strokeStyle = 'rgba(160,210,255,.32)'; g.lineWidth = 1.5; for (let x = 0; x < w; x += 128) { g.beginPath(); g.moveTo(x, 0); g.lineTo(x, h); g.stroke(); } for (let y = 0; y < h; y += 128) { g.beginPath(); g.moveTo(0, y); g.lineTo(w, y); g.stroke(); }
      g.strokeStyle = 'rgba(200,230,255,.6)'; g.lineWidth = 4; g.strokeRect(10, 10, w - 20, h - 20);
    }));
    const top = new THREE.Mesh(T(new THREE.PlaneGeometry(tw - .12, td - .12)), T(new THREE.MeshStandardMaterial({ map: bp, emissive: '#ffffff', emissiveMap: bp, emissiveIntensity: .5, roughness: .6 })));
    top.rotation.x = -Math.PI / 2; top.position.y = .925; top.receiveShadow = true; tb.add(top);
    top.userData = { kind: 'wing', w: 'table', key: 'tb' }; c.pickables.push(top);
    W.tablePlateMat = T(new THREE.MeshStandardMaterial({ roughness: .35, metalness: .55 }));
    const tplate = new THREE.Mesh(T(new THREE.BoxGeometry(1.1, .17, .025)), [brass, brass, brass, brass, W.tablePlateMat, brass]);
    tplate.position.set(0, .8, td / 2 + .07); tplate.userData = top.userData; c.pickables.push(tplate); tb.add(tplate);
    W.tilesG = new THREE.Group(); W.tilesG.position.y = .93; tb.add(W.tilesG);
    W.tw = tw - .12; W.td = td - .12;
    W.tb = tb; W.twFull = tw + .16; W.tdFull = td + .16;   // ukuran luar meja (utk jalur programmer)
    c.addObs(tb, 0, 0, Math.max(tw, td) / 2 + .3);
    corners.push(tb.localToWorld(new THREE.Vector3(-tw / 2, 0, -td / 2)), tb.localToWorld(new THREE.Vector3(tw / 2, 1, td / 2)));
    const spot = new THREE.SpotLight('#ffe6bf', 16, 9, .75, .6, 1.4);
    spot.position.copy(tb.localToWorld(new THREE.Vector3(0, 3.6, .6))); spot.target = tb; G.add(spot);
    const glow = new THREE.PointLight('#5aa8ff', 2.2, 4, 2); glow.position.copy(tb.localToWorld(new THREE.Vector3(0, 1.6, 0))); G.add(glow);
    const warm = new THREE.PointLight('#ffcf8a', 6, 8, 2); warm.position.copy(at(R - 2.0, aMid)); warm.position.y = 3.2; G.add(warm);

    // plakat kuningan di dinding: nama sudut ini
    const sa = aMid, sign = new THREE.Mesh(T(new THREE.BoxGeometry(2.4, .42, .05)), [brass, brass, brass, brass, T(new THREE.MeshStandardMaterial({ map: T(textPlate([{ text: 'DESIGN STUDIO', font: '700 60px "PJ Poppins",sans-serif', col: '#2a1d05', y: .42 }, { text: 'Mockup · ERD', font: '600 30px "PJ Poppins",sans-serif', col: '#5b4412', y: .8 }], { w: 768, h: 134, bg: '#d9bc78', border: '#8a6a2a' })), roughness: .35, metalness: .55 })), brass]);
    sign.position.copy(at(c.wallR - .05, sa)); sign.position.y = Math.min(c.Ht + .3, 3.4); sign.rotation.y = -sa; G.add(sign);
    sign.userData = { kind: 'wing', w: 'sign', key: 'sg' }; c.pickables.push(sign);
    c.track({ dispose: clearTiles });

    // ERD pertama di meja: yang terakhir dipilih, atau project pertama yg punya ERD
    const keepCur = c.prevCur && list.some(v => v.id === c.prevCur && v.erd.v) ? c.prevCur : (list.find(v => v.erd.v) || {}).id;
    paintTablePlate();
    if (keepCur) loadTable(keepCur);
    // arah kamera saat "mendekat": tegak lurus deretan easel, sedikit dari depan
    const theta = .75 * -aMid + .25 * tb.rotation.y;
    return { corners, theta };
  }

  return {
    width() { return .9 + count() * STEP + .4; },
    build(c) { const prev = W ? W.cur : null; return build(Object.assign({}, c, { prevCur: prev })); },
    needsRebuild() { return !W || W.sig !== sig(); },
    // data mockup/ERD berubah tanpa ganti daftar project: cat ulang saja
    refresh() {
      if (!W) return;
      const byId = {}; rows().forEach(v => { byId[v.id] = v; });
      Object.values(W.easels).forEach(b => {
        if (!b.v || !byId[b.v.id]) return;
        b.v = byId[b.v.id];
        paintEasel(b); paintRoll(b);
      });
      if (W.cur) loadTable(W.cur); else { const f = rows().find(v => v.erd.v); if (f) loadTable(f.id); else paintTablePlate(); }
    },
    hover(u) {
      if (!W) return;
      const key = u ? u.key : null; if (key === W.hot) return;
      const glow = (k, on) => {
        if (!k) return;
        const pid = +k.slice(1);
        if (k[0] === 'e' && W.easels[pid]) W.easels[pid].face.emissiveIntensity = on ? .5 : .18;
        if (k[0] === 'r' && W.easels[pid] && W.easels[pid].bandMat) W.easels[pid].bandMat.emissiveIntensity = on ? 1.2 : (W.cur === pid ? .9 : .25);
      };
      glow(W.hot, false); W.hot = key; glow(key, true);
    },
    update() {
      if (!W) return;
      const now = performance.now() / 1000;
      W.tilesG.children.forEach(m => { const u = m.userData; if (u.w !== 'tile') return; const q = clamp((now - u.t0) / .55, 0, 1); m.position.y = -.2 + (1 - Math.pow(1 - q, 3)) * .23; });
      W.pulses.forEach(p => { p.m.position.copy(p.curve.getPoint((now * .35 + p.off) % 1)); });
    },
    tip(u) {
      const head = (col, mod, name) => `<div class="c3-tip-mod" style="--c:${col};">${mod}</div><div class="c3-tip-name">${name}</div>`;
      const v = u.pid ? api.list().find(x => x.id === u.pid) : null;
      const tgt = v && v.target ? `<div class="c3-tip-sub">Project target ${esc(fmtD(v.target))}${v.overdue ? ' · overdue' : ''}</div>` : '';
      if (u.w === 'easel' && v) return head(ST_COL[v.mock.st] || '#94a3b8', 'Mockup · ' + esc(v.module), esc(v.name)) + `<div class="c3-tip-meta">${v.mock.v ? 'v' + v.mock.v + ' · ' : ''}${ST[v.mock.st] || v.mock.st}</div>` + tgt + '<div class="c3-tip-go">Click to open the mockup</div>';
      if (u.w === 'roll' && v) return head(ST_COL[v.erd.st] || '#94a3b8', 'ERD · ' + esc(v.module), esc(v.name)) + `<div class="c3-tip-meta">v${v.erd.v} · ${ST[v.erd.st] || v.erd.st} · ${v.erd.nNew} new + ${v.erd.nOld} existing</div>` + `<div class="c3-tip-go">${W && W.cur === v.id ? 'On the table — click again to open the editor' : 'Click to lay it out on the table'}</div>`;
      if (u.w === 'table') { const c = W && W.cur ? api.list().find(x => x.id === W.cur) : null; return head('#3b82f6', 'ERD table', c ? esc(c.name) : 'Nothing on the table') + (c ? `<div class="c3-tip-meta">${W.curTables} table(s) · v${c.erd.v} ${ST[c.erd.st] || ''}</div><div class="c3-tip-go">Click to open the ERD editor</div>` : '<div class="c3-tip-sub">Click a blueprint roll at the foot of an easel</div>'); }
      if (u.w === 'tile') return head(u.old ? '#64748b' : '#e4c47c', u.old ? 'Existing table' : 'New table — needs migration', esc(u.tid)) + `<div class="c3-tip-meta">${u.ncol} columns</div><div class="c3-tip-go">Click to edit it in the ERD editor</div>`;
      if (u.w === 'sign') return head('#e4c47c', 'Design Studio', 'Mockup &amp; ERD') + '<div class="c3-tip-sub">Easel = mockup of an active project · blueprint roll = its ERD</div><div class="c3-tip-go">Click to walk closer (again to step back)</div>';
      return '';
    },
    click(u) {
      if (!u) return;
      if (u.w === 'easel') api.openMockup(u.pid);
      else if (u.w === 'roll') { if (W && W.cur === u.pid) api.openErd(u.pid); else loadTable(u.pid); }
      else if (u.w === 'table') { if (W && W.cur) api.openErd(W.cur); else { const f = rows()[0]; if (f) api.openErd(f.id); } }
      else if (u.w === 'tile') api.openErd(u.pid, u.tid);
      else if (u.w === 'sign') { if (C && C.focusStudio) C.focusStudio(); }
    },
    // utk programmer yang berkeliling (library3d): titik berdiri di depan tiap easel &
    // meja ERD (hot = menunggu persetujuan / sedang direvisi), plus bentuk persegi meja
    // supaya jalurnya tidak menembus meja. Koordinat dunia.
    nav() {
      if (!W) return { spots: [], rects: [] };
      const busy = s => s === 'review' || s === 'changes', spots = [];
      Object.values(W.easels).forEach(b => {
        const v = b.v; if (!v) return;
        spots.push({ w: 'easel', pid: v.id, name: v.name, hot: busy(v.mock.st) || busy(v.erd.st),
          stand: b.e.localToWorld(new THREE.Vector3(0, 0, 1.15)), look: b.e.localToWorld(new THREE.Vector3(0, 1.5, .1)) });
      });
      const tb = W.tb, rects = [];
      if (tb) {
        const cur = W.cur ? api.list().find(x => x.id === W.cur) : null, c = tb.localToWorld(new THREE.Vector3());
        spots.push({ w: 'table', pid: cur ? cur.id : null, name: cur ? cur.name : '', hot: !!cur && busy(cur.erd.st),
          stand: tb.localToWorld(new THREE.Vector3(0, 0, W.tdFull / 2 + .45)), look: tb.localToWorld(new THREE.Vector3(0, .93, 0)) });
        rects.push({ x: c.x, z: c.z, rot: tb.rotation.y, hw: W.twFull / 2, hd: W.tdFull / 2 });
      }
      return { spots, rects };
    },
    // ringkasan untuk briefing Lucca perpustakaan (bahasa Indonesia)
    lucca() {
      if (api.ready() === false) return { mood: 'watch', line: 'Tabel Design Studio belum ada di database produksi — jalankan <b>db_migrate_LIVE_20261117_project_studio.sql</b> dulu.', say: 'Tabel Design Studio belum ada di database produksi.', label: null, go: null };
      const items = api.luccaItems('all'), watch = items.filter(i => i.lv === 'watch'), first = watch[0];
      return {
        mood: items.some(i => i.lv === 'alert') ? 'alert' : watch.length ? 'watch' : 'calm',
        line: watch.length ? `Studio: <b>${watch.length}</b> mockup/ERD menunggu persetujuan Anda` : 'Studio: tidak ada mockup/ERD yang menunggu persetujuan',
        say: watch.length ? `Di studio, ${watch.length} mockup atau ERD menunggu persetujuan Anda.` : '',
        label: first ? 'Periksa studio' : null,
        go: first ? () => api.go(first.go) : null
      };
    }
  };
}
