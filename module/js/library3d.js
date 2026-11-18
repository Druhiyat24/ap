/**
 * Project Library - tampilan 3D portofolio project (hanya untuk mode 3D).
 *
 * Seperti masuk perpustakaan: pintunya terbuka, kamera masuk, lalu tampak
 * deretan rak buku.
 *
 *   RAK    = satu modul (AP, AR, NDS, ...), papan nama kuningan di atasnya
 *   BARIS  = satu bulan; baris paling atas = bulan paling lama
 *   BUKU   = satu project; judulnya tertulis di punggung buku
 *
 * Stiker di bawah punggung buku = status (seperti label nomor panggil):
 *   Live         ungu, sampul sedikit mengilap
 *   Done         hijau, sampul agak pudar
 *   On Progress  biru berdenyut + pita pembatas biru
 *   Planned      buku "cetak biru" transparan
 *   Overdue      merah berdenyut + pita pembatas merah
 *
 * Bulan sebuah project: yang sudah selesai memakai actual_date (sama dgn
 * grafik Delivery Trend), yang belum memakai target_date.
 *
 * Bentuk buku ikut datanya: tebal = lama project, tinggi = prioritas, pita
 * pembatas = progres, berdebu (+ sarang laba-laba) = lama tidak disentuh.
 *
 * Di ujung kiri ada sudut pustakawan: dinding berjendela yang langitnya
 * mengikuti jam, papan pengumuman (angka bulan ini), lemari katalog (cari
 * buku), meja pustakawan (overdue & jatuh tempo 7 hari ke depan) dan troli
 * buku yang baru dikembalikan (selesai 7 hari terakhir). Suara (opsional)
 * dibuat langsung dgn Web Audio, tanpa berkas suara.
 *
 * Juga: geser waktu (baris rak mundur ke bulan-bulan lama), sorot satu PIC,
 * mode presentasi (tur otomatis layar penuh), buku kosong di meja (= project
 * baru), "Book of the month", rak "New this week", navigasi keyboard, simpan
 * gambar PNG, dan hiasan musiman (Agustus, Lebaran, akhir tahun).
 *
 * Ruangannya berisi: dinding berpanel dgn lukisan, meja kerja programmer
 * (layar = project yang sedang dikerjakan, lengkap dgn programmernya),
 * globe antik, sudut baca, tanaman, lampu gantung lilin & jam dinding yang
 * mengikuti jam asli.
 *
 * Hidup dari data: kalender dinding tenggat, perapian (apinya = project
 * selesai bulan ini), tanaman (layu kalau ada yang overdue), cuaca di
 * jendela (hujan/badai kalau ada yang overdue), kabar langsung dari rekan
 * (buku terbang ke rak), perayaan saat ada yang Done/Live (pita emas sehari).
 * Penghuni: programmer & kucing (pustakawan dihapus 17 Nov 2026). Bisa juga
 * jalan-jalan (WASD). Di ujung kanan ada sudut Design Studio (mockup & ERD,
 * ../js/studio3d.js) kalau project.php memberikannya lewat opts.wing.
 *
 * Dimuat dari index.php lewat import() dinamis begitu tombol 3D dipilih -
 * mode Normal tidak pernah mengunduh berkas ini maupun Three.js.
 */
import * as THREE from '../lib/three-0.160.0/three.module.min.js';

var CW = 2.6;               // lebar luar satu rak
var SIDE = 0.12;            // tebal papan samping
var IW = CW - SIDE * 2;     // lebar dalam (tempat buku)
var CD = 0.56;              // dalam rak
var PLINTH = 0.2;           // kaki rak
var SP = 0.68;              // jarak antar papan = tinggi satu baris (bulan)
var BT = 0.05;              // tebal papan
var CROWN = 0.5;            // mahkota rak (tempat papan nama)
var GAP = 0.3;              // celah antar rak
var ARC_R = 16;             // jari-jari lengkung deretan rak
var MAX_MONTHS = 6;         // baris bulan paling banyak; yang lebih lama tidak digambar
var SPINE_W = 64, SPINE_H = 256, ATLAS_COLS = 32;
var HOME = { theta: 0.5, phi: 1.4 };   // sudut kamera awal: serong dari kanan
var FOCUS_PHI = 1.47;
var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
var STATUS_ORDER = { 'Live': 0, 'Done': 1, 'On Progress': 2, 'Planned': 3 };
var ST_COLOR = { live: '#8b5cf6', done: '#10b981', prog: '#3b82f6', over: '#ef4444', plan: '#9cc6ff' };
var ST_TAG = { live: 'LIVE', done: 'DONE', prog: 'WIP', over: 'LATE' };
var GOLD = '#e4c47c', GOLD_DIM = '#5e4818';
var DESK = 999;             // "nomor rak" sudut meja depan (fokus kamera, buku di meja/troli)
var STUDIO = 998;           // "nomor rak" sudut Design Studio (fokus kamera; ada kalau opts.wing diberikan)
var WALL_W = 3.5;           // lebar dinding sudut pustakawan
// Warna Lucca (sistem perpustakaan) menurut kondisi: tenang / ada tenggat dekat / ada yang terlambat
var LUCCA_COL = { calm: 0x5ee7ff, watch: 0xffc35a, alert: 0xff5d73, gold: 0xffd479 };
var STALE_DAYS = 14;        // buku berdebu: belum selesai & tidak diubah selama ini
var WEB_DAYS = 30;          // ... dan bersarang laba-laba
var DAY = 86400000;
var DUST_COL = new THREE.Color('#8d8576');
// Suasana mengikuti jam: langit di jendela, berkas cahayanya, cahaya utama
// ruangan & seberapa terang lampu rak (malam hari lampunya lebih terang).
var TOD = {
  night:     { sky: ['#050a22', '#18265a'], shaft: '#9db8ff', shaftOp: 0.1,  key: '#a9b8ff', keyI: 1.25, hemi: 0.74, lamp: 1.35 },
  dawn:      { sky: ['#283670', '#f3a07a'], shaft: '#ffc8a8', shaftOp: 0.2,  key: '#ffcfb0', keyI: 1.85, hemi: 0.86, lamp: 1.1 },
  morning:   { sky: ['#4f9bf0', '#d4ebff'], shaft: '#fff3da', shaftOp: 0.27, key: '#fff0d6', keyI: 2.5,  hemi: 1.0,  lamp: 0.85 },
  midday:    { sky: ['#3a86ec', '#c4e2ff'], shaft: '#fffaf0', shaftOp: 0.3,  key: '#fff4e4', keyI: 2.6,  hemi: 1.05, lamp: 0.8 },
  afternoon: { sky: ['#4a7fd4', '#ffd9a0'], shaft: '#ffd99c', shaftOp: 0.28, key: '#ffdcb0', keyI: 2.3,  hemi: 0.95, lamp: 1.0 },
  sunset:    { sky: ['#34285f', '#ff8a4a'], shaft: '#ffa565', shaftOp: 0.26, key: '#ffb487', keyI: 1.95, hemi: 0.86, lamp: 1.15 }
};
function phaseOf(h) {
  return h < 5 ? 'night' : h < 6.5 ? 'dawn' : h < 10.5 ? 'morning' : h < 15 ? 'midday' : h < 17 ? 'afternoon' : h < 18.5 ? 'sunset' : 'night';
}
var TOUR_MS = 7500;
// benda yang punya keterangan / bisa diklik (selain buku & rak)
var THING_KINDS = { catalog: 1, bell: 1, newbook: 1, table: 1, globe: 1, clock: 1, screen: 1, dev: 1, duck: 1, pc: 1, calendar: 1, fire: 1, plant: 1, window: 1, cat: 1, lucca: 1 };         // lama tiap perhentian tur presentasi
// Idul Fitri (perkiraan 1 Syawal) - untuk hiasan ketupat
var IDUL_FITRI = ['2025-03-31', '2026-03-20', '2027-03-10', '2028-02-27', '2029-02-15', '2030-02-05', '2031-01-25', '2032-01-14'];
var SEASON_GREET = { merdeka: 'Dirgahayu Republik Indonesia!', lebaran: 'Selamat Hari Raya Idul Fitri', yearend: 'Happy holidays & a happy new year' };
// Hiasan musiman: sebulan menjelang s.d. seminggu sesudah Lebaran = ketupat,
// Agustus = umbul-umbul merah putih, 1 Des - 6 Jan = lampu kelap-kelip.
function seasonOf(d) {
  for (var i = 0; i < IDUL_FITRI.length; i++) {
    var diff = daysFrom(parseDay(IDUL_FITRI[i]), d);
    if (diff >= -30 && diff <= 7) return 'lebaran';
  }
  var m = d.getMonth() + 1;
  if (m === 8) return 'merdeka';
  if (m === 12 || (m === 1 && d.getDate() <= 6)) return 'yearend';
  return '';
}

function pad2(n) { return (n < 10 ? '0' : '') + n; }
function clamp(v, a, b) { return Math.max(a, Math.min(b, v)); }
function isDone(s) { return s === 'Done' || s === 'Live'; }
function esc(s) { return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
function easeOutCubic(t) { return 1 - Math.pow(1 - t, 3); }
function easeInOutCubic(t) { return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }

function monthOf(p) {
  var d = isDone(p.status) ? (p.actual_date || p.live_date || p.target_date) : (p.target_date || p.start_date);
  return d ? String(d).substring(0, 7) : '';
}
function dateOf(p) {
  return String((isDone(p.status) ? p.actual_date : p.target_date) || p.start_date || '');
}
function statusKey(p) {
  if (+p.is_overdue === 1) return 'over';
  return p.status === 'Live' ? 'live' : p.status === 'Done' ? 'done' : p.status === 'On Progress' ? 'prog' : 'plan';
}
function parseDay(v) {
  var m = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(v || ''));
  return m ? new Date(+m[1], +m[2] - 1, +m[3]) : null;
}
function today0() { var t = new Date(); t.setHours(0, 0, 0, 0); return t; }
function daysFrom(a, b) { return Math.round((b - a) / DAY); }
// Berapa hari project yang belum selesai tidak disentuh (0 = belum berdebu)
function staleDays(p) {
  if (isDone(p.status)) return 0;
  var d = parseDay(p.updated_date || p.created_date), n = d ? daysFrom(d, today0()) : 0;
  return n >= STALE_DAYS ? n : 0;
}
// Bentuk buku dari datanya: tebal = lama project (mulai -> target/selesai,
// skala log 1 minggu .. 8 bulan), tinggi = prioritas. Sedikit acak supaya
// raknya tetap tampak alami.
function bookShape(p, r) {
  var a = parseDay(p.start_date), b = parseDay(isDone(p.status) ? (p.actual_date || p.target_date) : p.target_date);
  var days = a && b ? daysFrom(a, b) : -1;
  var t = days >= 0 ? 0.05 + 0.085 * clamp(Math.log(Math.max(days, 7) / 7) / Math.log(240 / 7), 0, 1) : 0.07 + r() * 0.02;
  var pr = String(p.priority || '');
  var h = (pr === 'High' ? 0.575 : pr === 'Low' ? 0.43 : 0.5) + (r() - 0.5) * 0.024;
  return { t: t + (r() - 0.5) * 0.006, h: h, d: 0.34 + r() * 0.1 };
}

// Deret bulan dari yang paling awal sampai bulan ini (atau target terjauh).
// Rak menampilkan MAX_MONTHS di antaranya (bisa digeser mundur, lihat build).
function monthWindow(keys, current) {
  var all = keys.concat(current ? [current] : []).filter(Boolean).sort();
  if (!all.length) return [];
  var first = all[0], last = all[all.length - 1];
  var y = +first.substring(0, 4), m = +first.substring(5, 7), out = [];
  for (var guard = 0; guard < 240; guard++) {
    var k = y + '-' + pad2(m);
    out.push(k);
    if (k >= last) break;
    m++; if (m > 12) { m = 1; y++; }
  }
  return out;
}
function monthName(k) { return k ? MONTHS[+k.substring(5, 7) - 1] + ' ' + k.substring(0, 4) : ''; }
function rangeLabel(a, b) {
  if (!a) return '';
  if (a === b) return monthName(a);
  return (a.substring(0, 4) === b.substring(0, 4) ? MONTHS[+a.substring(5, 7) - 1] : monthName(a)) + ' \u2013 ' + monthName(b);
}
function prevMonthKey(k) {
  var y = +k.substring(0, 4), m = +k.substring(5, 7) - 1;
  if (m < 1) { m = 12; y--; }
  return y + '-' + pad2(m);
}

// Angka acak yang selalu sama untuk project yang sama - jadi tebal, tinggi
// & warna bukunya tidak berubah tiap kali rak dibangun ulang.
function hashStr(s) {
  var h = 2166136261;
  for (var i = 0; i < s.length; i++) { h ^= s.charCodeAt(i); h = Math.imul(h, 16777619); }
  return h >>> 0;
}
function rng(seed) {
  return function () {
    seed = (seed + 0x6D2B79F5) | 0;
    var t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
    t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
  };
}

// ---- tekstur buatan (canvas) ----------------------------------------------
function roundRect(g, x, y, w, h, r) {
  g.beginPath();
  g.moveTo(x + r, y);
  g.arcTo(x + w, y, x + w, y + h, r);
  g.arcTo(x + w, y + h, x, y + h, r);
  g.arcTo(x, y + h, x, y, r);
  g.arcTo(x, y, x + w, y, r);
  g.closePath();
}
function canvasTex(w, h, draw, linear) {
  var c = document.createElement('canvas');
  c.width = w; c.height = h;
  draw(c.getContext('2d'), w, h);
  var t = new THREE.CanvasTexture(c);
  if (!linear) t.colorSpace = THREE.SRGBColorSpace;
  return t;
}
function radialTexture(stops) {
  return canvasTex(128, 128, function (g) {
    var grd = g.createRadialGradient(64, 64, 0, 64, 64, 64);
    stops.forEach(function (s) { grd.addColorStop(s[0], s[1]); });
    g.fillStyle = grd;
    g.fillRect(0, 0, 128, 128);
  });
}
// Gradasi tegak: terang di atas, hilang di bawah (berkas cahaya lampu & baris yang disorot)
function fadeTexture() {
  return canvasTex(4, 128, function (g) {
    var grd = g.createLinearGradient(0, 0, 0, 128);
    grd.addColorStop(0, 'rgba(255,255,255,1)');
    grd.addColorStop(0.35, 'rgba(255,255,255,.45)');
    grd.addColorStop(1, 'rgba(255,255,255,0)');
    g.fillStyle = grd;
    g.fillRect(0, 0, 4, 128);
  });
}
function woodTexture() {
  var t = canvasTex(512, 512, function (g, w, h) {
    g.fillStyle = '#5c3720';
    g.fillRect(0, 0, w, h);
    var r = rng(7);
    for (var i = 0; i < 240; i++) {
      var y = r() * h, amp = 2 + r() * 6, f = 0.004 + r() * 0.012, ph = r() * 6;
      g.strokeStyle = (r() < 0.55 ? 'rgba(28,12,4,' : 'rgba(150,98,56,') + (0.05 + r() * 0.12) + ')';
      g.lineWidth = 0.6 + r() * 2.4;
      g.beginPath();
      for (var x = 0; x <= w; x += 16) {
        var yy = y + Math.sin(x * f + ph) * amp;
        if (x === 0) g.moveTo(x, yy); else g.lineTo(x, yy);
      }
      g.stroke();
    }
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
function plankTexture() {
  var t = canvasTex(1024, 1024, function (g, w, h) {
    var r = rng(5), ph = 128;
    for (var row = 0; row < h / ph; row++) {
      var x = -Math.floor(r() * 300);
      while (x < w) {
        var len = 320 + Math.floor(r() * 380);
        g.fillStyle = 'hsl(' + (20 + r() * 9) + ',' + (34 + r() * 14) + '%,' + (15 + r() * 8) + '%)';
        g.fillRect(x, row * ph, len, ph);
        for (var k = 0; k < 14; k++) {
          var yy = row * ph + r() * ph;
          g.strokeStyle = 'rgba(0,0,0,' + (0.05 + r() * 0.1) + ')';
          g.lineWidth = 1 + r() * 2;
          g.beginPath();
          g.moveTo(x, yy);
          g.bezierCurveTo(x + len * 0.3, yy + (r() - 0.5) * 10, x + len * 0.7, yy + (r() - 0.5) * 10, x + len, yy + (r() - 0.5) * 6);
          g.stroke();
        }
        g.fillStyle = 'rgba(0,0,0,.55)';
        g.fillRect(x, row * ph, 3, ph);
        x += len;
      }
      g.fillStyle = 'rgba(0,0,0,.6)';
      g.fillRect(0, row * ph, w, 3);
    }
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
function stoneTexture() {
  var t = canvasTex(512, 512, function (g, w, h) {
    g.fillStyle = '#0b1030';
    g.fillRect(0, 0, w, h);
    var r = rng(11), bh = 64;
    for (var row = 0; row < h / bh; row++) {
      var x = -(row % 2) * 64;
      while (x < w) {
        var bw = 96 + Math.floor(r() * 3) * 32;
        g.fillStyle = 'hsl(228,' + (26 + r() * 14) + '%,' + (17 + r() * 9) + '%)';
        g.fillRect(x + 3, row * bh + 3, bw - 6, bh - 6);
        g.fillStyle = 'rgba(255,255,255,.05)';
        g.fillRect(x + 3, row * bh + 3, bw - 6, 3);
        x += bw;
      }
    }
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
function rugTexture() {
  return canvasTex(1024, 640, function (g, w, h) {
    g.fillStyle = '#5a1a22';
    g.fillRect(0, 0, w, h);
    var r = rng(3), i;
    for (i = 0; i < 5000; i++) {
      g.fillStyle = r() < 0.5 ? 'rgba(0,0,0,.07)' : 'rgba(255,220,180,.035)';
      g.fillRect(r() * w, r() * h, 2, 2);
    }
    g.strokeStyle = '#c99a4a'; g.lineWidth = 10; g.strokeRect(30, 30, w - 60, h - 60);
    g.strokeStyle = '#2a0c14'; g.lineWidth = 34; g.strokeRect(66, 66, w - 132, h - 132);
    g.strokeStyle = '#b8873c'; g.lineWidth = 4; g.strokeRect(92, 92, w - 184, h - 184);
    g.fillStyle = '#c99a4a';
    for (i = 0; i < 26; i++) {
      var x = 80 + i * (w - 160) / 25;
      g.beginPath(); g.arc(x, 66, 5, 0, Math.PI * 2); g.arc(x, h - 66, 5, 0, Math.PI * 2); g.fill();
    }
    g.save();
    g.translate(w / 2, h / 2);
    for (i = 0; i < 3; i++) {
      g.beginPath();
      g.ellipse(0, 0, 230 - i * 62, 138 - i * 38, 0, 0, Math.PI * 2);
      g.strokeStyle = i % 2 ? '#2a0c14' : '#c99a4a';
      g.lineWidth = i ? 5 : 8;
      g.stroke();
    }
    g.beginPath(); g.moveTo(0, -98); g.lineTo(168, 0); g.lineTo(0, 98); g.lineTo(-168, 0); g.closePath();
    g.strokeStyle = 'rgba(201,154,74,.55)'; g.lineWidth = 3; g.stroke();
    g.restore();
  });
}
// Sarang laba-laba, berpangkal di pojok kiri atas tekstur
function cobwebTexture() {
  return canvasTex(256, 256, function (g, w) {
    var r = rng(21), n = 7, rays = [], i, k;
    g.lineCap = 'round';
    g.strokeStyle = 'rgba(255,255,255,.85)';
    g.lineWidth = 1.5;
    for (i = 0; i < n; i++) {
      var a = (i / (n - 1)) * Math.PI / 2 + (r() - 0.5) * 0.1, len = w * (0.85 + r() * 0.2);
      rays.push(a);
      g.beginPath(); g.moveTo(0, 0); g.lineTo(Math.cos(a) * len, Math.sin(a) * len); g.stroke();
    }
    g.lineWidth = 1.1;
    for (k = 1; k <= 7; k++) {
      var rad = k * w * 0.11 * (0.92 + r() * 0.16);
      g.strokeStyle = 'rgba(255,255,255,' + (0.8 - k * 0.07) + ')';
      g.beginPath();
      for (i = 0; i < n; i++) {
        var x = Math.cos(rays[i]) * rad, y = Math.sin(rays[i]) * rad;
        if (!i) { g.moveTo(x, y); continue; }
        var am = (rays[i] + rays[i - 1]) / 2, sag = rad * 0.84;
        g.quadraticCurveTo(Math.cos(am) * sag, Math.sin(am) * sag, x, y);
      }
      g.stroke();
    }
  });
}
function plasterTexture() {
  var t = canvasTex(256, 256, function (g, w, h) {
    g.fillStyle = '#6d6280';
    g.fillRect(0, 0, w, h);
    var r = rng(13);
    for (var i = 0; i < 2600; i++) {
      g.fillStyle = r() < 0.5 ? 'rgba(0,0,0,' + (r() * 0.08) + ')' : 'rgba(255,255,255,' + (r() * 0.05) + ')';
      var sz = 2 + r() * 10;
      g.fillRect(r() * w, r() * h, sz, sz);
    }
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
// Muka lemari katalog: laci-laci kecil dgn kartu label & pegangan kuningan
function catalogTexture() {
  return canvasTex(512, 768, function (g, w, h) {
    g.fillStyle = '#3d2414';
    g.fillRect(0, 0, w, h);
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.fillStyle = '#d9b062';
    g.font = '700 38px Georgia, "Times New Roman", serif';
    g.fillText('CARD CATALOG', w / 2, 46);
    var labels = ['A', 'B-C', 'D-E', 'F-G', 'H-J', 'K-L', 'M', 'N-O', 'P', 'Q-R', 'S', 'T', 'U-V', 'W-Z', '0-9'];
    var cols = 3, rows = 5, mx = 20, top = 88, gw = (w - mx * 2) / cols, gh = (h - top - 18) / rows;
    for (var ri = 0; ri < rows; ri++) {
      for (var cj = 0; cj < cols; cj++) {
        var x = mx + cj * gw + 6, y = top + ri * gh + 6, dw = gw - 12, dh = gh - 12;
        var grd = g.createLinearGradient(0, y, 0, y + dh);
        grd.addColorStop(0, '#8d5d36');
        grd.addColorStop(1, '#5c381e');
        g.fillStyle = grd;
        roundRect(g, x, y, dw, dh, 6); g.fill();
        g.strokeStyle = 'rgba(0,0,0,.5)'; g.lineWidth = 3;
        roundRect(g, x, y, dw, dh, 6); g.stroke();
        var lw = dw * 0.56, lh = dh * 0.28, lx = x + (dw - lw) / 2, ly = y + dh * 0.14;
        g.fillStyle = '#d6b265';
        g.fillRect(lx - 4, ly - 4, lw + 8, lh + 8);
        g.fillStyle = '#f3ead2';
        g.fillRect(lx, ly, lw, lh);
        g.fillStyle = '#3a2810';
        g.font = '600 ' + Math.round(lh * 0.6) + 'px Georgia, "Times New Roman", serif';
        g.fillText(labels[ri * cols + cj], lx + lw / 2, ly + lh / 2 + 1);
        g.fillStyle = '#e3c070';
        g.beginPath(); g.ellipse(x + dw / 2, y + dh * 0.72, dw * 0.13, dh * 0.085, 0, 0, Math.PI * 2); g.fill();
        g.fillStyle = 'rgba(60,40,10,.6)';
        g.beginPath(); g.ellipse(x + dw / 2, y + dh * 0.745, dw * 0.085, dh * 0.04, 0, 0, Math.PI * 2); g.fill();
      }
    }
  });
}
// Langit di jendela sesuai waktu: matahari & awan, atau bintang & bulan,
// dgn siluet atap kota di kejauhan.
function skyTexture(ph, weather) {
  var P = TOD[ph], wet = !!weather && weather !== 'clear', S = wet ? WET_SKY[ph] : P.sky;
  return canvasTex(256, 512, function (g, w, h) {
    var grd = g.createLinearGradient(0, 0, 0, h), r = rng(31), i;
    grd.addColorStop(0, S[0]);
    grd.addColorStop(1, S[1]);
    g.fillStyle = grd;
    g.fillRect(0, 0, w, h);
    if (wet) {
      // awan mendung tebal (hujan / badai)
      for (i = 0; i < 16; i++) {
        var wx0 = r() * w, wy0 = h * (0.05 + r() * 0.6), wr0 = 60 + r() * 90;
        var wc = g.createRadialGradient(wx0, wy0, 0, wx0, wy0, wr0);
        wc.addColorStop(0, ph === 'night' ? 'rgba(40,44,60,.8)' : 'rgba(70,76,88,.75)');
        wc.addColorStop(1, 'rgba(70,76,88,0)');
        g.save();
        g.translate(wx0, wy0); g.scale(1, 0.4); g.translate(-wx0, -wy0);
        g.fillStyle = wc;
        g.beginPath(); g.arc(wx0, wy0, wr0, 0, Math.PI * 2); g.fill();
        g.restore();
      }
    } else if (ph === 'night') {
      for (i = 0; i < 90; i++) {
        var sz = r() < 0.88 ? 1.5 : 2.6;
        g.fillStyle = 'rgba(255,255,255,' + (0.3 + r() * 0.7) + ')';
        g.fillRect(r() * w, r() * h * 0.78, sz, sz);
      }
      var mg = g.createRadialGradient(w * 0.66, h * 0.2, 0, w * 0.66, h * 0.2, 70);
      mg.addColorStop(0, 'rgba(230,236,255,.35)');
      mg.addColorStop(1, 'rgba(230,236,255,0)');
      g.fillStyle = mg;
      g.fillRect(0, 0, w, h);
      g.fillStyle = '#f4f0d8';
      g.beginPath(); g.arc(w * 0.66, h * 0.2, 26, 0, Math.PI * 2); g.fill();
      g.fillStyle = grd;
      g.beginPath(); g.arc(w * 0.66 + 12, h * 0.2 - 9, 24, 0, Math.PI * 2); g.fill();
    } else {
      var low = ph === 'dawn' || ph === 'sunset';
      var sx = ph === 'sunset' || ph === 'afternoon' ? w * 0.25 : w * 0.74, sy = low ? h * 0.78 : ph === 'midday' ? h * 0.06 : h * 0.22;
      var sg = g.createRadialGradient(sx, sy, 0, sx, sy, w * 0.75);
      sg.addColorStop(0, 'rgba(255,250,228,.95)');
      sg.addColorStop(0.12, 'rgba(255,236,190,.55)');
      sg.addColorStop(1, 'rgba(255,236,190,0)');
      g.fillStyle = sg;
      g.fillRect(0, 0, w, h);
      for (i = 0; i < 7; i++) {
        var cx = r() * w, cy = h * (0.22 + r() * 0.5), cw = 50 + r() * 90;
        var cg = g.createRadialGradient(cx, cy, 0, cx, cy, cw);
        cg.addColorStop(0, low ? 'rgba(255,190,160,.5)' : 'rgba(255,255,255,.55)');
        cg.addColorStop(1, 'rgba(255,255,255,0)');
        g.save();
        g.translate(cx, cy); g.scale(1, 0.3); g.translate(-cx, -cy);
        g.fillStyle = cg;
        g.beginPath(); g.arc(cx, cy, cw, 0, Math.PI * 2); g.fill();
        g.restore();
      }
    }
    var x = 0;
    while (x < w) {
      var bw = 14 + r() * 30, bh = 18 + r() * 58;
      g.fillStyle = ph === 'night' ? '#03061a' : 'rgba(28,36,78,.55)';
      g.fillRect(x, h - bh, bw, bh);
      if (ph === 'night') {
        g.fillStyle = 'rgba(255,214,140,.85)';
        for (i = 0; i < 3; i++) if (r() < 0.5) g.fillRect(x + 3 + r() * (bw - 8), h - bh + 5 + r() * (bh - 10), 3, 3);
      }
      x += bw;
    }
  });
}
// Bayangan jendela di lantai: lengkungnya di bawah (= paling jauh dari dinding)
function windowPatchTexture() {
  return canvasTex(256, 384, function (g, w, h) {
    var pad = 28, ww = w - pad * 2, r = ww / 2, rh = h - pad * 2 - r;
    g.filter = 'blur(7px)';
    g.fillStyle = '#fff';
    g.beginPath();
    g.moveTo(pad, pad); g.lineTo(pad + ww, pad); g.lineTo(pad + ww, pad + rh);
    g.arc(w / 2, pad + rh, r, 0, Math.PI, false);
    g.closePath();
    g.fill();
    g.globalCompositeOperation = 'destination-out';
    g.fillStyle = '#000';
    g.fillRect(w / 2 - 5, pad - 4, 10, h);
    [0.34, 0.67, 1].forEach(function (f) { g.fillRect(pad - 4, pad + rh * f - 4, ww + 8, 8); });
    g.globalCompositeOperation = 'source-over';
    g.filter = 'none';
  });
}
// Papan pengumuman dari gabus: kartu judul + empat catatan angka
function boardTexture(s) {
  return canvasTex(640, 700, function (g, w, h) {
    g.fillStyle = '#a8713f';
    g.fillRect(0, 0, w, h);
    var r = rng(41), i;
    for (i = 0; i < 5000; i++) {
      g.fillStyle = r() < 0.5 ? 'rgba(70,38,12,' + (r() * 0.3) + ')' : 'rgba(245,205,150,' + (r() * 0.2) + ')';
      g.fillRect(r() * w, r() * h, 1 + r() * 3, 1 + r() * 3);
    }
    var vg = g.createRadialGradient(w / 2, h / 2, w * 0.3, w / 2, h / 2, w * 0.8);
    vg.addColorStop(0, 'rgba(0,0,0,0)');
    vg.addColorStop(1, 'rgba(40,20,5,.45)');
    g.fillStyle = vg;
    g.fillRect(0, 0, w, h);
    function paper(x, y, pw, ph, rot, bg, draw, pin) {
      g.save();
      g.translate(x + pw / 2, y + ph / 2);
      g.rotate(rot);
      g.shadowColor = 'rgba(0,0,0,.5)'; g.shadowBlur = 16; g.shadowOffsetY = 7;
      g.fillStyle = bg;
      g.fillRect(-pw / 2, -ph / 2, pw, ph);
      g.shadowColor = 'rgba(0,0,0,0)'; g.shadowBlur = 0; g.shadowOffsetY = 0;
      g.textAlign = 'center';
      g.textBaseline = 'middle';
      draw(pw, ph);
      g.fillStyle = 'rgba(0,0,0,.35)';
      g.beginPath(); g.arc(3, -ph / 2 + 20, 11, 0, Math.PI * 2); g.fill();
      g.fillStyle = pin;
      g.beginPath(); g.arc(0, -ph / 2 + 16, 11, 0, Math.PI * 2); g.fill();
      g.fillStyle = 'rgba(255,255,255,.6)';
      g.beginPath(); g.arc(-3, -ph / 2 + 13, 4, 0, Math.PI * 2); g.fill();
      g.restore();
    }
    paper(140, 22, 360, 112, -0.015, '#f7f1e1', function () {
      g.fillStyle = '#7a1f1f';
      g.font = '700 32px Georgia, "Times New Roman", serif';
      g.fillText('LIBRARY NOTICES', 0, 2);
      g.fillStyle = '#6b5a40';
      g.font = 'italic 21px Georgia, "Times New Roman", serif';
      var line2 = s.date + (s.greeting ? ' \u00b7 ' + s.greeting : s.filtered ? ' \u00b7 filtered view' : '');
      fitFont(g, line2, 'italic', 21, 'Georgia, "Times New Roman", serif', 336);
      g.fillText(line2, 0, 34);
    }, '#c0392b');
    function stat(x, y, rot, bg, head, big, sub, pin) {
      paper(x, y, 262, 250, rot, bg, function (pw, ph) {
        g.fillStyle = 'rgba(45,30,12,.8)';
        g.font = '700 25px Arial, sans-serif';
        g.fillText(head, 0, -ph / 2 + 58);
        g.fillStyle = '#20160a';
        fitFont(g, big, '700', 96, 'Georgia, "Times New Roman", serif', pw - 40);
        g.fillText(big, 0, 16);
        g.fillStyle = 'rgba(45,30,12,.75)';
        fitFont(g, sub, 'italic', 22, 'Georgia, "Times New Roman", serif', pw - 28);
        g.fillText(sub, 0, ph / 2 - 34);
      }, pin);
    }
    stat(36, 160, -0.04, '#d9f2e3', 'ON TIME', s.onTime, s.onTimeSub, '#2e7d4f');
    stat(340, 168, 0.035, '#fde0dc', 'OVERDUE', s.overdue, s.overdueSub, '#c0392b');
    stat(44, 432, 0.03, '#fff3c4', 'DONE THIS MONTH', s.done, s.doneSub, '#b7791f');
    stat(334, 426, -0.03, '#dceafe', 'IN PROGRESS', s.prog, s.progSub, '#2b6cb0');
  });
}

// Sampul depan bergambar judul (buku yang dipajang menghadap depan)
function coverTexture(p, col, plan) {
  return canvasTex(256, 352, function (g, w, h) {
    if (plan) {
      g.fillStyle = '#173174';
      g.fillRect(0, 0, w, h);
      g.strokeStyle = 'rgba(160,200,255,.16)';
      g.lineWidth = 1;
      for (var gx = 0; gx < w; gx += 16) { g.beginPath(); g.moveTo(gx, 0); g.lineTo(gx, h); g.stroke(); }
      for (var gy = 0; gy < h; gy += 16) { g.beginPath(); g.moveTo(0, gy); g.lineTo(w, gy); g.stroke(); }
    } else {
      var c = new THREE.Color(col || '#5a3424'), grd = g.createLinearGradient(0, 0, w, h);
      grd.addColorStop(0, c.clone().lerp(new THREE.Color(1, 1, 1), 0.12).getStyle());
      grd.addColorStop(1, c.clone().multiplyScalar(0.55).getStyle());
      g.fillStyle = grd;
      g.fillRect(0, 0, w, h);
    }
    var ink = plan ? '#cfe1ff' : GOLD;
    g.fillStyle = 'rgba(0,0,0,.35)';
    g.fillRect(0, 0, 14, h);
    g.strokeStyle = ink;
    g.lineWidth = 3;
    g.strokeRect(24, 18, w - 42, h - 36);
    g.lineWidth = 1.2;
    g.strokeRect(31, 25, w - 56, h - 50);
    g.fillStyle = ink;
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.font = '700 15px Arial, sans-serif';
    g.fillText(String(p.category || 'General').toUpperCase(), w / 2 + 4, 62, w - 80);
    var words = String(p.project_name || '').split(/\s+/), lines = [], cur = '';
    g.font = '700 26px Georgia, "Times New Roman", serif';
    words.forEach(function (wd) {
      var t = cur ? cur + ' ' + wd : wd;
      if (g.measureText(t).width > w - 80 && cur) { lines.push(cur); cur = wd; } else cur = t;
    });
    if (cur) lines.push(cur);
    lines = lines.slice(0, 4);
    var y0 = h / 2 - (lines.length - 1) * 17;
    lines.forEach(function (ln, i) { g.fillText(ln, w / 2 + 4, y0 + i * 34, w - 70); });
    g.font = '600 12px Arial, sans-serif';
    g.fillText('PROJECT LIBRARY', w / 2 + 4, h - 54);
  });
}
// Buku catatan kosong di meja pustakawan (dua halaman bergaris)
function journalTexture() {
  return canvasTex(512, 340, function (g, w, h) {
    g.fillStyle = '#f3ead2';
    g.fillRect(0, 0, w, h);
    var gut = g.createLinearGradient(w / 2 - 30, 0, w / 2 + 30, 0);
    gut.addColorStop(0, 'rgba(120,90,40,0)');
    gut.addColorStop(0.5, 'rgba(120,90,40,.35)');
    gut.addColorStop(1, 'rgba(120,90,40,0)');
    g.fillStyle = gut;
    g.fillRect(w / 2 - 30, 0, 60, h);
    g.strokeStyle = 'rgba(90,130,190,.45)';
    g.lineWidth = 1.5;
    for (var y = 74; y < h - 20; y += 26) {
      g.beginPath(); g.moveTo(24, y); g.lineTo(w / 2 - 22, y); g.moveTo(w / 2 + 22, y); g.lineTo(w - 24, y); g.stroke();
    }
    g.fillStyle = '#8a5a1c';
    g.font = '600 15px Arial, sans-serif';
    g.textAlign = 'center';
    g.fillText('CHAPTER ONE', w / 4, 46);
    g.fillStyle = '#23324f';
    g.font = 'italic 30px "Segoe Script", "Brush Script MT", cursive';
    g.fillText('A new project\u2026', w * 0.75, 52);
  });
}
// Anyaman janur ketupat
function ketupatTexture() {
  return canvasTex(64, 64, function (g, w, h) {
    for (var y = 0; y < 8; y++) {
      for (var x = 0; x < 8; x++) {
        g.fillStyle = (x + y) % 2 ? '#3f8f3a' : '#c8d26a';
        g.fillRect(x * 8, y * 8, 8, 8);
      }
    }
    g.strokeStyle = 'rgba(30,60,20,.5)';
    g.lineWidth = 1;
    for (var i = 0; i <= 8; i++) {
      g.beginPath(); g.moveTo(i * 8, 0); g.lineTo(i * 8, h); g.stroke();
      g.beginPath(); g.moveTo(0, i * 8); g.lineTo(w, i * 8); g.stroke();
    }
  });
}

// ---- tekstur ruangan: dinding, lukisan, globe, daun, jam, buku terbuka ----
// Panel kayu dinding (satu ubin 1,2 m x 2,4 m, diulang)
function panelTexture() {
  var t = canvasTex(256, 512, function (g, w, h) {
    g.fillStyle = '#3b2416';
    g.fillRect(0, 0, w, h);
    var r = rng(17);
    for (var i = 0; i < 140; i++) {
      g.strokeStyle = 'rgba(' + (r() < 0.5 ? '20,10,4,' : '120,80,48,') + (0.05 + r() * 0.1) + ')';
      g.lineWidth = 0.6 + r() * 1.6;
      var x = r() * w;
      g.beginPath(); g.moveTo(x, 0); g.bezierCurveTo(x + (r() - 0.5) * 10, h * 0.3, x + (r() - 0.5) * 10, h * 0.7, x + (r() - 0.5) * 6, h); g.stroke();
    }
    [[20, 24, w - 40, h * 0.5 - 40], [20, h * 0.5 + 16, w - 40, h * 0.5 - 40]].forEach(function (pn) {
      g.fillStyle = 'rgba(0,0,0,.28)';
      g.fillRect(pn[0], pn[1], pn[2], pn[3]);
      g.fillStyle = 'rgba(150,100,60,.12)';
      g.fillRect(pn[0] + 10, pn[1] + 10, pn[2] - 20, pn[3] - 20);
      g.strokeStyle = 'rgba(255,220,170,.12)'; g.lineWidth = 2;
      g.strokeRect(pn[0] + 0.5, pn[1] + 0.5, pn[2], pn[3]);
    });
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
// Wallpaper damask hijau tua di atas rak
function damaskTexture() {
  var t = canvasTex(256, 256, function (g, w, h) {
    g.fillStyle = '#1d3326';
    g.fillRect(0, 0, w, h);
    g.fillStyle = 'rgba(214,190,120,.16)';
    function motif(cx, cy, s) {
      g.save(); g.translate(cx, cy); g.scale(s, s);
      g.beginPath();
      g.moveTo(0, -46); g.bezierCurveTo(26, -30, 30, -6, 0, 6); g.bezierCurveTo(-30, -6, -26, -30, 0, -46);
      g.moveTo(0, 6); g.bezierCurveTo(22, 14, 18, 40, 0, 46); g.bezierCurveTo(-18, 40, -22, 14, 0, 6);
      g.fill();
      g.beginPath(); g.arc(-24, 0, 6, 0, Math.PI * 2); g.arc(24, 0, 6, 0, Math.PI * 2); g.fill();
      g.restore();
    }
    motif(w / 2, h / 2, 1); motif(0, 0, 0.7); motif(w, 0, 0.7); motif(0, h, 0.7); motif(w, h, 0.7);
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
// Plafon kasetan kayu (satu ubin = 2,4 m): bingkai kayu, panel biru tua bergaris emas
function cofferTexture() {
  var t = canvasTex(256, 256, function (g, w, h) {
    var b = 24;
    g.fillStyle = '#4a2f1b';
    g.fillRect(0, 0, w, h);
    g.fillStyle = '#6e4628';
    g.fillRect(b, b, w - 2 * b, h - 2 * b);
    g.fillStyle = '#2a3d6a';
    g.fillRect(b + 14, b + 14, w - 2 * b - 28, h - 2 * b - 28);
    g.strokeStyle = 'rgba(214,180,100,.55)';
    g.lineWidth = 3;
    g.strokeRect(b + 21.5, b + 21.5, w - 2 * b - 43, h - 2 * b - 43);
    g.fillStyle = 'rgba(214,180,100,.6)';
    g.beginPath(); g.arc(w / 2, h / 2, 9, 0, Math.PI * 2); g.fill();
    g.fillStyle = 'rgba(0,0,0,.35)';
    g.fillRect(b, b, w - 2 * b, 6); g.fillRect(b, b, 6, h - 2 * b);
    g.fillStyle = 'rgba(255,220,170,.08)';
    g.fillRect(b, h - b - 6, w - 2 * b, 6); g.fillRect(w - b - 6, b, 6, h - 2 * b);
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
// Lukisan pemandangan (berbeda tiap rak)
function paintingTexture(seed) {
  return canvasTex(320, 200, function (g, w, h) {
    var r = rng(100 + seed * 7);
    var skies = [['#f6c27a', '#7a4a6e'], ['#9fc7e8', '#e9dcc0'], ['#e98a5a', '#3b2e5a'], ['#b8d0c8', '#f2e6c8'], ['#2a3a6a', '#d28a6a']];
    var sk = skies[seed % skies.length], grd = g.createLinearGradient(0, 0, 0, h);
    grd.addColorStop(0, sk[1]); grd.addColorStop(1, sk[0]);
    g.fillStyle = grd;
    g.fillRect(0, 0, w, h);
    g.fillStyle = 'rgba(255,240,200,.85)';
    g.beginPath(); g.arc(w * (0.2 + r() * 0.6), h * (0.25 + r() * 0.2), 10 + r() * 10, 0, Math.PI * 2); g.fill();
    var hills = ['#5c6b45', '#46553a', '#33402c', '#24301f'];
    hills.forEach(function (col, k) {
      g.fillStyle = col;
      g.beginPath();
      var base = h * (0.5 + k * 0.12);
      g.moveTo(0, h);
      for (var x = 0; x <= w; x += 16) g.lineTo(x, base - Math.sin(x * 0.012 + r() * 0.3 + k) * 14 - r() * 8);
      g.lineTo(w, h);
      g.fill();
    });
    g.strokeStyle = 'rgba(30,20,10,.5)'; g.lineWidth = 1.5;
    for (var b = 0; b < 3; b++) {
      var bx = w * (0.3 + r() * 0.5), by = h * (0.15 + r() * 0.2);
      g.beginPath(); g.moveTo(bx - 6, by); g.quadraticCurveTo(bx - 3, by - 4, bx, by); g.quadraticCurveTo(bx + 3, by - 4, bx + 6, by); g.stroke();
    }
    var vg = g.createRadialGradient(w / 2, h / 2, h * 0.3, w / 2, h / 2, w * 0.7);
    vg.addColorStop(0, 'rgba(0,0,0,0)'); vg.addColorStop(1, 'rgba(30,15,5,.45)');
    g.fillStyle = vg;
    g.fillRect(0, 0, w, h);
  });
}
// Globe antik: laut perkamen, benua cokelat (bergaya, bukan peta sungguhan)
function globeTexture() {
  return canvasTex(512, 256, function (g, w, h) {
    g.fillStyle = '#d8c497';
    g.fillRect(0, 0, w, h);
    var r = rng(77), i;
    for (i = 0; i < 9; i++) {
      var cx = r() * w, cy = h * (0.2 + r() * 0.6), sz = 30 + r() * 60;
      g.fillStyle = 'rgba(120,88,48,.85)';
      g.beginPath();
      for (var a = 0; a <= Math.PI * 2 + 0.01; a += 0.3) {
        var rr = sz * (0.6 + r() * 0.5);
        g.lineTo(cx + Math.cos(a) * rr * 1.4, cy + Math.sin(a) * rr * 0.8);
      }
      g.fill();
    }
    g.strokeStyle = 'rgba(90,60,30,.35)';
    g.lineWidth = 1;
    for (i = 1; i < 6; i++) { g.beginPath(); g.moveTo(0, h * i / 6); g.lineTo(w, h * i / 6); g.stroke(); }
    for (i = 0; i < 12; i++) { g.beginPath(); g.moveTo(w * i / 12, 0); g.lineTo(w * i / 12, h); g.stroke(); }
    var sh = g.createLinearGradient(0, 0, 0, h);
    sh.addColorStop(0, 'rgba(60,40,20,.25)'); sh.addColorStop(0.5, 'rgba(60,40,20,0)'); sh.addColorStop(1, 'rgba(60,40,20,.25)');
    g.fillStyle = sh;
    g.fillRect(0, 0, w, h);
  });
}
// Daun tanaman (tepi transparan, dipakai dgn alphaTest)
function leafTexture() {
  return canvasTex(64, 256, function (g, w, h) {
    g.clearRect(0, 0, w, h);
    var grd = g.createLinearGradient(0, 0, w, 0);
    grd.addColorStop(0, '#2f6b33'); grd.addColorStop(0.5, '#4f9a4a'); grd.addColorStop(1, '#2a5f2e');
    g.fillStyle = grd;
    g.beginPath();
    g.moveTo(w / 2, h);
    g.bezierCurveTo(w * 1.05, h * 0.65, w * 0.9, h * 0.2, w / 2, 0);
    g.bezierCurveTo(w * 0.1, h * 0.2, -w * 0.05, h * 0.65, w / 2, h);
    g.fill();
    g.strokeStyle = 'rgba(200,230,170,.6)'; g.lineWidth = 2;
    g.beginPath(); g.moveTo(w / 2, h); g.lineTo(w / 2, h * 0.05); g.stroke();
  });
}
// Tuts keyboard (dilihat dari atas)
function keysTexture() {
  return canvasTex(512, 146, function (g, w, h) {
    g.fillStyle = '#121318';
    g.fillRect(0, 0, w, h);
    var rows = 5, cols = 15, kw = (w - 16) / cols, kh = (h - 12) / rows;
    for (var r = 0; r < rows; r++) {
      for (var c = 0; c < cols; c++) {
        var x = 8 + c * kw, y = 6 + r * kh;
        if (r === 4 && c > 3 && c < 11) {
          if (c === 4) { g.fillStyle = '#2c2f38'; roundRect(g, x + 2, y + 2, kw * 7 - 4, kh - 4, 4); g.fill(); }
          continue;
        }
        g.fillStyle = r === 0 && c === 0 ? '#c0392b' : r === 2 && c === 14 ? '#3b82f6' : '#2c2f38';
        roundRect(g, x + 2, y + 2, kw - 4, kh - 4, 4);
        g.fill();
        g.fillStyle = 'rgba(255,255,255,.07)';
        g.fillRect(x + 4, y + 3, kw - 8, 3);
      }
    }
  });
}
// Muka jam dinding
function clockFaceTexture() {
  return canvasTex(256, 256, function (g, w) {
    var c = w / 2;
    g.fillStyle = '#f2e8cf';
    g.beginPath(); g.arc(c, c, c, 0, Math.PI * 2); g.fill();
    g.strokeStyle = '#3a2810';
    for (var i = 0; i < 60; i++) {
      var a = i / 60 * Math.PI * 2, big = i % 5 === 0, r0 = c - (big ? 22 : 12), r1 = c - 6;
      g.lineWidth = big ? 4 : 1.5;
      g.beginPath(); g.moveTo(c + Math.sin(a) * r0, c - Math.cos(a) * r0); g.lineTo(c + Math.sin(a) * r1, c - Math.cos(a) * r1); g.stroke();
    }
    g.fillStyle = '#3a2810';
    g.font = '700 30px Georgia, "Times New Roman", serif';
    g.textAlign = 'center'; g.textBaseline = 'middle';
    ['XII', 'III', 'VI', 'IX'].forEach(function (t, k) {
      var a = k / 4 * Math.PI * 2;
      g.fillText(t, c + Math.sin(a) * (c - 46), c - Math.cos(a) * (c - 46) + 2);
    });
  });
}
// Buku terbuka di meja baca: kiri = judul, kanan = progres & tenggat
function openBookTexture(p) {
  return canvasTex(512, 340, function (g, w, h) {
    g.fillStyle = '#f3ead2';
    g.fillRect(0, 0, w, h);
    var gut = g.createLinearGradient(w / 2 - 34, 0, w / 2 + 34, 0);
    gut.addColorStop(0, 'rgba(120,90,40,0)'); gut.addColorStop(0.5, 'rgba(120,90,40,.4)'); gut.addColorStop(1, 'rgba(120,90,40,0)');
    g.fillStyle = gut;
    g.fillRect(w / 2 - 34, 0, 68, h);
    g.textAlign = 'center'; g.textBaseline = 'middle';
    g.fillStyle = '#8a5a1c';
    g.font = '600 16px Arial, sans-serif';
    g.fillText(String(p.category || 'General').toUpperCase(), w / 4, 50, w / 2 - 40);
    g.fillStyle = '#21180d';
    var words = String(p.project_name || '').split(/\s+/), lines = [], cur = '';
    g.font = '700 30px Georgia, "Times New Roman", serif';
    words.forEach(function (wd) { var t = cur ? cur + ' ' + wd : wd; if (g.measureText(t).width > w / 2 - 50 && cur) { lines.push(cur); cur = wd; } else cur = t; });
    if (cur) lines.push(cur);
    lines.slice(0, 4).forEach(function (ln, i) { g.fillText(ln, w / 4, 110 + i * 38, w / 2 - 40); });
    var pr = clamp(parseInt(p.progress, 10) || 0, 0, 100);
    g.fillStyle = '#8d7b60';
    g.font = '600 15px Arial, sans-serif';
    g.fillText('PROGRESS', w * 0.75, 70);
    g.fillStyle = '#e3d7bb';
    g.fillRect(w / 2 + 40, 100, w / 2 - 80, 16);
    g.fillStyle = '#b07d2c';
    g.fillRect(w / 2 + 40, 100, (w / 2 - 80) * pr / 100, 16);
    g.fillStyle = '#21180d';
    g.font = '700 46px Georgia, "Times New Roman", serif';
    g.fillText(pr + '%', w * 0.75, 170);
    var d = parseDay(p.target_date);
    g.fillStyle = '#5b3fa0';
    g.font = '700 22px "Courier New", Courier, monospace';
    g.fillText(d ? 'DUE ' + pad2(d.getDate()) + ' ' + MONTHS[d.getMonth()].toUpperCase() : 'NO DUE DATE', w * 0.75, 236);
    if (p.pic) {
      g.fillStyle = '#23324f';
      g.font = 'italic 24px "Segoe Script", "Brush Script MT", cursive';
      g.fillText(String(p.pic), w * 0.75, 290, w / 2 - 50);
    }
  });
}

// ---- kalender dinding, api perapian, hujan --------------------------------
var FULL_MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
// tata letak kalender (piksel kanvas) - dipakai juga utk tahu tanggal yg ditunjuk
var CAL = { w: 352, h: 480, gx: 14, gy: 200, cw: 324 / 7, ch: 44 };
function calendarInfo(cm) {
  var y = +cm.substring(0, 4), m = +cm.substring(5, 7);
  return { y: y, m: m, off: (new Date(y, m - 1, 1).getDay() + 6) % 7, days: new Date(y, m, 0).getDate() };
}
// Kalender bulan ini: tenggat (belum selesai) dilingkari merah, yang selesai
// pada tanggal itu diberi latar hijau, hari ini dilingkari emas.
function calendarTexture(ci, due, done, today) {
  return canvasTex(CAL.w, CAL.h, function (g, w, h) {
    g.fillStyle = '#f7f1e1';
    g.fillRect(0, 0, w, h);
    var grd = g.createLinearGradient(0, 0, 0, 120), r = rng(ci.y * 12 + ci.m), i;
    grd.addColorStop(0, '#2c4a3a');
    grd.addColorStop(1, '#1d3326');
    g.fillStyle = grd;
    g.fillRect(0, 0, w, 120);
    var cols = ['#8a3b2e', '#b07d2c', '#2f5d7a', '#6b3f6e', '#3f6b45'];
    for (i = 0; i < 21; i++) {
      var bw = 9 + r() * 6, bh = 40 + r() * 40;
      g.fillStyle = cols[i % cols.length];
      g.fillRect(14 + i * 15.6, 112 - bh, bw, bh);
    }
    g.fillStyle = '#6b4a2a';
    g.fillRect(8, 112, w - 16, 8);
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.fillStyle = '#7a1f1f';
    fitFont(g, FULL_MONTHS[ci.m - 1].toUpperCase() + ' ' + ci.y, '700', 32, 'Georgia, "Times New Roman", serif', w - 30);
    g.fillText(FULL_MONTHS[ci.m - 1].toUpperCase() + ' ' + ci.y, w / 2, 152);
    g.font = '700 15px Arial, sans-serif';
    g.fillStyle = '#8d7b60';
    ['M', 'T', 'W', 'T', 'F', 'S', 'S'].forEach(function (d, k) { g.fillText(d, CAL.gx + CAL.cw * (k + 0.5), 184); });
    for (var dn = 1; dn <= ci.days; dn++) {
      var idx = dn - 1 + ci.off, col = idx % 7, row = Math.floor(idx / 7);
      var cx = CAL.gx + CAL.cw * (col + 0.5), cy = CAL.gy + CAL.ch * (row + 0.5);
      if (done[dn]) { g.fillStyle = 'rgba(16,185,129,.22)'; g.beginPath(); g.arc(cx, cy, 18, 0, Math.PI * 2); g.fill(); }
      if (dn === today) { g.fillStyle = '#e9c977'; g.beginPath(); g.arc(cx, cy, 17, 0, Math.PI * 2); g.fill(); }
      g.fillStyle = col > 4 ? '#a05050' : '#2c2216';
      g.font = (due[dn] ? '700 ' : '600 ') + '19px Georgia, "Times New Roman", serif';
      g.fillText(String(dn), cx, cy + 1);
      if (due[dn]) {
        g.strokeStyle = '#d0312d';
        g.lineWidth = 3;
        g.beginPath(); g.ellipse(cx, cy, 19, 16, -0.25, 0, Math.PI * 2); g.stroke();
        if (due[dn].length > 1) {
          g.fillStyle = '#d0312d';
          g.beginPath(); g.arc(cx + 15, cy - 13, 8, 0, Math.PI * 2); g.fill();
          g.fillStyle = '#fff';
          g.font = '700 11px Arial, sans-serif';
          g.fillText(String(due[dn].length), cx + 15, cy - 12);
        }
      }
    }
  });
}
// Lidah api (teardrop bercahaya) utk perapian
function flameTexture() {
  return canvasTex(64, 128, function (g, w, h) {
    var grd = g.createRadialGradient(w / 2, h * 0.8, 2, w / 2, h * 0.68, h * 0.64);
    grd.addColorStop(0, 'rgba(255,250,220,1)');
    grd.addColorStop(0.25, 'rgba(255,205,90,.95)');
    grd.addColorStop(0.6, 'rgba(255,115,30,.6)');
    grd.addColorStop(1, 'rgba(255,60,10,0)');
    g.fillStyle = grd;
    g.beginPath();
    g.moveTo(w / 2, 4);
    g.bezierCurveTo(w * 0.95, h * 0.45, w * 0.95, h * 0.95, w / 2, h - 2);
    g.bezierCurveTo(w * 0.05, h * 0.95, w * 0.05, h * 0.45, w / 2, 4);
    g.fill();
  });
}
// Garis-garis hujan (berulang, digeser tiap frame)
function rainTexture() {
  var t = canvasTex(128, 256, function (g, w, h) {
    g.clearRect(0, 0, w, h);
    var r = rng(55);
    g.lineCap = 'round';
    for (var i = 0; i < 70; i++) {
      var x = r() * w, y = r() * h, l = 10 + r() * 22;
      g.strokeStyle = 'rgba(220,232,255,' + (0.25 + r() * 0.4) + ')';
      g.lineWidth = 1 + r() * 0.8;
      g.beginPath(); g.moveTo(x, y); g.lineTo(x - l * 0.18, y + l); g.stroke();
    }
  });
  t.wrapS = t.wrapT = THREE.RepeatWrapping;
  return t;
}
// Langit mendung (hujan/badai) per waktu
var WET_SKY = {
  night: ['#06080f', '#1c2130'], dawn: ['#2a2e3e', '#6d6a72'], morning: ['#5a6372', '#9aa3ad'],
  midday: ['#5f6878', '#a3abb5'], afternoon: ['#55606f', '#968f8a'], sunset: ['#34303e', '#6f6266']
};

// Papan kuningan dgn tulisan terukir
function brassTexture(w, h, draw) {
  return canvasTex(w, h, function (g) {
    var grd = g.createLinearGradient(0, 0, 0, h);
    grd.addColorStop(0, '#f7e3a8');
    grd.addColorStop(0.45, '#d0aa58');
    grd.addColorStop(1, '#8f6528');
    g.fillStyle = grd;
    roundRect(g, 2, 2, w - 4, h - 4, h * 0.12);
    g.fill();
    g.strokeStyle = 'rgba(70,46,14,.85)';
    g.lineWidth = Math.max(2, h * 0.025);
    roundRect(g, h * 0.08, h * 0.08, w - h * 0.16, h - h * 0.16, h * 0.07);
    g.stroke();
    draw(g, w, h);
  });
}
function engrave(g, text, x, y) {
  g.fillStyle = 'rgba(255,244,210,.55)';
  g.fillText(text, x, y + 2);
  g.fillStyle = '#3a2810';
  g.fillText(text, x, y);
}
function fitFont(g, text, weight, size, family, maxW) {
  for (; size > 10; size -= 2) {
    g.font = weight + ' ' + size + 'px ' + family;
    if (g.measureText(text).width <= maxW) break;
  }
  return size;
}

// Lingkungan pantulan buatan: ruang kayu yang hangat + satu jendela biru.
// Tanpa ini kuningan & sampul mengilap terlihat datar.
function buildEnvScene() {
  var s = new THREE.Scene();
  var geo = new THREE.SphereGeometry(20, 32, 16);
  var top = new THREE.Color('#4a3322'), bottom = new THREE.Color('#0b0a14');
  var pos = geo.attributes.position, colors = [];
  for (var i = 0; i < pos.count; i++) {
    var t = (pos.getY(i) / 20 + 1) / 2;
    var c = bottom.clone().lerp(top, Math.pow(t, 1.4));
    colors.push(c.r, c.g, c.b);
  }
  geo.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));
  s.add(new THREE.Mesh(geo, new THREE.MeshBasicMaterial({ vertexColors: true, side: THREE.BackSide })));
  var soft = new THREE.Mesh(new THREE.PlaneGeometry(14, 6), new THREE.MeshBasicMaterial({ color: new THREE.Color(4.6, 3.6, 2.3), side: THREE.DoubleSide }));
  soft.position.set(3, 12, 8); soft.lookAt(0, 0, 0); s.add(soft);
  var win = new THREE.Mesh(new THREE.PlaneGeometry(7, 10), new THREE.MeshBasicMaterial({ color: new THREE.Color(1.1, 1.5, 3.2), side: THREE.DoubleSide }));
  win.position.set(-14, 6, -4); win.lookAt(0, 0, 0); s.add(win);
  return s;
}

// Beberapa balok jadi satu geometri (satu draw call per rak). UV dihitung
// dari ukuran sebenarnya supaya serat kayu tidak melar; part.v = serat tegak.
function boxesGeometry(parts) {
  var pos = [], nor = [], uv = [];
  parts.forEach(function (b) {
    var g = new THREE.BoxGeometry(b.w, b.h, b.d).toNonIndexed();
    var p = g.attributes.position, n = g.attributes.normal;
    for (var i = 0; i < p.count; i++) {
      var x = p.getX(i) + b.x, y = p.getY(i) + b.y, z = p.getZ(i) + b.z;
      var nx = n.getX(i), ny = n.getY(i), u, v;
      pos.push(x, y, z);
      nor.push(nx, ny, n.getZ(i));
      if (Math.abs(nx) > 0.5) { u = z; v = y; } else if (Math.abs(ny) > 0.5) { u = x; v = z; } else { u = x; v = y; }
      if (b.v) { var tmp = u; u = v; v = tmp; }
      uv.push(u * 0.35, v * 0.35);
    }
    g.dispose();
  });
  var geo = new THREE.BufferGeometry();
  geo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
  geo.setAttribute('normal', new THREE.Float32BufferAttribute(nor, 3));
  geo.setAttribute('uv', new THREE.Float32BufferAttribute(uv, 2));
  return geo;
}

// Balok 1x1x1 untuk buku, dgn tiga bahan: 0 = sampul (kiri/kanan),
// 1 = punggung (depan), 2 = halaman (atas/bawah/belakang).
function bookGeometry() {
  var g = new THREE.BoxGeometry(1, 1, 1);
  // urutan sisi BoxGeometry: +x, -x, +y, -y, +z, -z (6 indeks per sisi)
  var idx = g.index.array, side = [], pages = [], spine = [];
  for (var f = 0; f < 6; f++) {
    var dst = f < 2 ? side : f === 4 ? spine : pages;
    for (var k = 0; k < 6; k++) dst.push(idx[f * 6 + k]);
  }
  g.setIndex(side.concat(pages, spine));
  g.clearGroups();
  g.addGroup(0, 12, 0);
  g.addGroup(12, 18, 2);
  g.addGroup(30, 6, 1);
  return g;
}

// Warna kulit buku: keluarga warna modulnya, tiap buku sedikit berbeda.
function leather(base, r, key) {
  var hsl = {};
  new THREE.Color(base).getHSL(hsl, THREE.SRGBColorSpace);
  var h = (hsl.h + (r() - 0.5) * 0.1 + 1) % 1;
  var s = clamp(hsl.s * (0.42 + r() * 0.3), 0.16, 0.6);
  var l = 0.25 + r() * 0.15;
  if (key === 'done') { s *= 0.75; l += 0.06; }
  return new THREE.Color().setHSL(h, s, l, THREE.SRGBColorSpace);
}

function fitTitle(g, s, avail) {
  var font = '';
  for (var size = 24; size >= 15; size--) {
    font = '600 ' + size + 'px Georgia, "Times New Roman", serif';
    g.font = font;
    if (g.measureText(s).width <= avail) return { font: font, text: s };
  }
  while (s.length > 1 && g.measureText(s + '…').width > avail) s = s.slice(0, -1);
  return { font: font, text: s.replace(/\s+$/, '') + '…' };
}

// Punggung buku di atlas: g = warna, e = bagian yang berpendar (emas & stiker).
function drawSpine(g, e, x0, y0, b) {
  var W = SPINE_W, H = SPINE_H, r = rng(hashStr('s' + b.p.id));
  var ft = fitTitle(g, String(b.p.project_name || '').trim() || 'Untitled', H - 88);
  function title(ctx, col) {
    ctx.save();
    ctx.translate(x0 + W / 2 + 1, y0 + 36);
    ctx.rotate(Math.PI / 2);
    ctx.font = ft.font;
    ctx.textAlign = 'left';
    ctx.textBaseline = 'middle';
    ctx.fillStyle = col;
    ctx.fillText(ft.text, 0, 0);
    ctx.restore();
  }
  g.save(); g.beginPath(); g.rect(x0, y0, W, H); g.clip();
  e.save(); e.beginPath(); e.rect(x0, y0, W, H); e.clip();
  e.fillStyle = '#000';
  e.fillRect(x0, y0, W, H);
  if (b.key === 'plan') {
    g.fillStyle = 'rgba(110,160,255,.14)';
    g.fillRect(x0 + 4, y0 + 4, W - 8, H - 8);
    g.setLineDash([7, 5]);
    g.lineWidth = 2.5;
    g.strokeStyle = 'rgba(170,205,255,.9)';
    g.strokeRect(x0 + 5, y0 + 5, W - 10, H - 10);
    g.setLineDash([]);
    title(g, 'rgba(214,230,255,.95)');
    title(e, '#3d6fd0');
  } else {
    var c = b.color, white = new THREE.Color(1, 1, 1);
    var grd = g.createLinearGradient(x0, 0, x0 + W, 0);
    grd.addColorStop(0, c.clone().multiplyScalar(0.35).getStyle());
    grd.addColorStop(0.3, c.clone().lerp(white, 0.1).getStyle());
    grd.addColorStop(0.65, c.getStyle());
    grd.addColorStop(1, c.clone().multiplyScalar(0.3).getStyle());
    g.fillStyle = grd;
    g.fillRect(x0, y0, W, H);
    for (var i = 0; i < 70; i++) {
      g.fillStyle = r() < 0.5 ? 'rgba(0,0,0,.08)' : 'rgba(255,255,255,.035)';
      g.fillRect(x0 + r() * W, y0 + r() * H, 1 + r() * 2, 1 + r() * 3);
    }
    var gold = b.key === 'done' ? '#c9ae72' : GOLD;
    [[12, 3], [18, 2], [H - 50, 2], [H - 45, 3]].forEach(function (bd) {
      g.fillStyle = 'rgba(0,0,0,.35)';
      g.fillRect(x0 + 3, y0 + bd[0] + bd[1], W - 6, 1);
      g.fillStyle = gold;
      g.fillRect(x0 + 3, y0 + bd[0], W - 6, bd[1]);
      e.fillStyle = GOLD_DIM;
      e.fillRect(x0 + 3, y0 + bd[0], W - 6, bd[1]);
    });
    title(g, gold);
    title(e, GOLD_DIM);
    // stiker status, seperti label nomor panggil buku perpustakaan
    var sc = ST_COLOR[b.key], sy = y0 + H - 36, sh = 24;
    g.fillStyle = 'rgba(245,238,220,.95)';
    roundRect(g, x0 + 7, sy - 2, W - 14, sh + 4, 5); g.fill();
    g.fillStyle = sc;
    roundRect(g, x0 + 9, sy, W - 18, sh, 4); g.fill();
    g.font = '700 11px Arial, sans-serif';
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.fillStyle = '#fff';
    g.fillText(ST_TAG[b.key], x0 + W / 2, sy + sh / 2 + 1);
    e.fillStyle = sc;
    roundRect(e, x0 + 9, sy, W - 18, sh, 4); e.fill();
  }
  // Debu: project yang lama tidak disentuh - lapisan abu-abu, paling tebal di atas
  if (b.stale) {
    var dg = g.createLinearGradient(0, y0, 0, y0 + H);
    dg.addColorStop(0, 'rgba(224,214,192,.66)');
    dg.addColorStop(0.3, 'rgba(206,196,176,.32)');
    dg.addColorStop(1, 'rgba(190,180,160,.16)');
    g.fillStyle = dg;
    g.fillRect(x0, y0, W, H);
    for (var k = 0; k < 170; k++) {
      g.fillStyle = 'rgba(238,230,212,' + (0.12 + r() * 0.36) + ')';
      g.fillRect(x0 + r() * W, y0 + r() * r() * H, 1 + r() * 1.6, 1 + r() * 1.6);
    }
    e.fillStyle = 'rgba(0,0,0,.6)';
    e.fillRect(x0, y0, W, H);
  }
  g.restore();
  e.restore();
}

var ICONS = {
  pause: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M9 6v12M15 6v12"/></svg>',
  play: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"><path d="M8 5.5l11 6.5-11 6.5z"/></svg>',
  reset: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 12a8.5 8.5 0 1 0 2.6-6.1"/><path d="M3.5 4v5h5"/></svg>',
  back: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 6l-6 6 6 6"/></svg>',
  search: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></svg>',
  close: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>',
  soundOn: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9.5h3.5L12 5.5v13l-4.5-4H4z"/><path d="M15.5 9a4 4 0 0 1 0 6M18 6.5a7.5 7.5 0 0 1 0 11"/></svg>',
  soundOff: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9.5h3.5L12 5.5v13l-4.5-4H4z"/><path d="M16 9.5l5 5M21 9.5l-5 5"/></svg>',
  next: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 6l6 6-6 6"/></svg>',
  present: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12.5" rx="2"/><path d="M10.5 7.8l4 2.45-4 2.45z"/><path d="M8 20.5h8M12 16.5v4"/></svg>',
  more: '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="5.5" cy="12" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="18.5" cy="12" r="1.7"/></svg>',
  check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>',
  spot: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3.5h6l-1 6h-4z"/><path d="M10 9.5l-3.5 11h11L14 9.5"/></svg>',
  walk: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="13" cy="4.5" r="1.8"/><path d="M10 21l2-6 3 3v3M8 12l3-3.5 3 2.5 3 1M11 8.5L10 14"/></svg>',
  camera: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 8h3l1.6-2.5h6.8L17 8h3v11H4z"/><circle cx="12" cy="13.3" r="3.4"/></svg>'
};

// Suara perpustakaan, dibuat langsung dgn Web Audio (tanpa berkas): dengung
// ruangan yang pelan, pintu berderit, buku ditarik, sampul, halaman dibalik,
// centang & lonceng meja. Mati secara bawaan; pilihannya diingat per browser.
function createSound() {
  var AC = window.AudioContext || window.webkitAudioContext;
  var ctx = null, master = null, noise = null, room = null, on = false, ducked = false;
  try { on = localStorage.getItem('projSound') === '1'; } catch (e) { /* abaikan */ }
  // Browser baru mengizinkan suara sesudah ada klik/ketukan di halaman.
  function allowed() { return !navigator.userActivation || navigator.userActivation.hasBeenActive; }
  function init() {
    if (!AC || !allowed()) return false;
    if (!ctx) {
      try { ctx = new AC(); } catch (e) { AC = null; return false; }
      master = ctx.createGain();
      master.gain.value = 0;
      master.connect(ctx.destination);
      noise = ctx.createBuffer(1, ctx.sampleRate * 2, ctx.sampleRate);
      var nd = noise.getChannelData(0);
      for (var i = 0; i < nd.length; i++) nd[i] = Math.random() * 2 - 1;
    }
    if (ctx.state === 'suspended') ctx.resume();
    return true;
  }
  function startRoom() {
    var len = ctx.sampleRate * 4, buf = ctx.createBuffer(1, len, ctx.sampleRate), d = buf.getChannelData(0), last = 0;
    for (var i = 0; i < len; i++) { last = (last + 0.02 * (Math.random() * 2 - 1)) / 1.02; d[i] = last * 3.5; }
    var src = ctx.createBufferSource(), lp = ctx.createBiquadFilter(), g = ctx.createGain();
    src.buffer = buf;
    src.loop = true;
    lp.type = 'lowpass';
    lp.frequency.value = 360;
    g.gain.value = 0.06;
    src.connect(lp); lp.connect(g); g.connect(master);
    src.start();
    room = src;
  }
  function level() {
    if (!ctx) return;
    var v = on && !ducked ? 0.8 : 0, t = ctx.currentTime;
    master.gain.cancelScheduledValues(t);
    master.gain.setTargetAtTime(v, t, 0.12);
    if (v && !room) startRoom();
  }
  function burst(t, dur, f0, f1, q, gain, type) {
    var src = ctx.createBufferSource(), f = ctx.createBiquadFilter(), g = ctx.createGain();
    src.buffer = noise;
    f.type = type || 'bandpass';
    f.Q.value = q;
    f.frequency.setValueAtTime(f0, t);
    f.frequency.exponentialRampToValueAtTime(f1, t + dur);
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(gain, t + dur * 0.18);
    g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
    src.connect(f); f.connect(g); g.connect(master);
    src.start(t, Math.random() * 1.5, dur + 0.05);
  }
  function tone(t, freq, dur, gain, type, f1) {
    var o = ctx.createOscillator(), g = ctx.createGain();
    o.type = type || 'sine';
    o.frequency.setValueAtTime(freq, t);
    if (f1) o.frequency.exponentialRampToValueAtTime(f1, t + dur);
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(gain, t + 0.01);
    g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
    o.connect(g); g.connect(master);
    o.start(t);
    o.stop(t + dur + 0.05);
  }
  // Engsel pintu berderit: gergaji nada rendah yang "tersendat-sendat"
  function creak(t) {
    var dur = 1.5, n = 256, amp = new Float32Array(n), fr = new Float32Array(n), ph = 0;
    for (var i = 0; i < n; i++) {
      var x = i / (n - 1);
      ph += (14 + 26 * Math.sin(x * Math.PI)) * dur / n;
      var pulse = Math.pow(Math.max(0, Math.sin(ph * Math.PI * 2)), 5);
      var env = Math.sin(Math.min(1, x * 4) * Math.PI / 2) * (1 - x * x * x);
      amp[i] = 0.0001 + 0.3 * env * (0.3 + 0.7 * pulse);
      fr[i] = 62 + 36 * Math.sin(x * 4.2) + 10 * Math.random();
    }
    var o = ctx.createOscillator(), f = ctx.createBiquadFilter(), g = ctx.createGain();
    o.type = 'sawtooth';
    o.frequency.setValueCurveAtTime(fr, t, dur);
    f.type = 'bandpass';
    f.frequency.value = 820;
    f.Q.value = 3.5;
    g.gain.value = 0;
    g.gain.setValueCurveAtTime(amp, t, dur);
    o.connect(f); f.connect(g); g.connect(master);
    o.start(t);
    o.stop(t + dur + 0.05);
    tone(t + dur - 0.08, 72, 0.4, 0.3, 'sine', 42);
  }
  // dengkur kucing: gergaji sangat rendah, dimodulasi ±24 Hz
  function purr(t) {
    var dur = 1.6, o = ctx.createOscillator(), f = ctx.createBiquadFilter(), g = ctx.createGain(), lfo = ctx.createOscillator(), lg = ctx.createGain();
    o.type = 'sawtooth';
    o.frequency.value = 48;
    f.type = 'lowpass';
    f.frequency.value = 260;
    lfo.frequency.value = 24;
    lg.gain.value = 0.08;
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(0.12, t + 0.2);
    g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
    lfo.connect(lg); lg.connect(g.gain);
    o.connect(f); f.connect(g); g.connect(master);
    o.start(t); lfo.start(t);
    o.stop(t + dur + 0.05); lfo.stop(t + dur + 0.05);
  }
  var SFX = {
    creak: creak,
    flip: function (t) { burst(t, 0.24, 1600, 5200, 0.8, 0.22); burst(t + 0.15, 0.2, 3200, 1300, 0.7, 0.12); },
    slide: function (t) { burst(t, 0.34, 600, 1500, 0.6, 0.14); },
    open: function (t) { tone(t, 120, 0.2, 0.16, 'sine', 70); burst(t + 0.05, 0.45, 900, 3200, 0.6, 0.1); },
    close: function (t) { tone(t, 100, 0.26, 0.34, 'sine', 50); burst(t, 0.14, 700, 250, 0.8, 0.22, 'lowpass'); },
    tick: function (t) { tone(t, 1800, 0.06, 0.07, 'triangle'); burst(t, 0.07, 2600, 1800, 1.5, 0.08); },
    bell: function (t) { tone(t, 2093, 1.8, 0.16); tone(t, 5240, 0.8, 0.045); tone(t, 3517, 1.2, 0.06); tone(t, 1046, 0.25, 0.05, 'triangle'); },
    stamp: function (t) { tone(t, 70, 0.18, 0.4, 'sine', 40); burst(t, 0.09, 1200, 400, 0.9, 0.3, 'lowpass'); },
    shutter: function (t) { burst(t, 0.05, 3000, 2000, 1.2, 0.2); burst(t + 0.09, 0.06, 2600, 1800, 1.2, 0.16); },
    squeak: function (t) { tone(t, 900, 0.12, 0.12, 'triangle', 1500); tone(t + 0.13, 1100, 0.1, 0.09, 'triangle', 1700); },
    key: function (t) { burst(t, 0.03, 4200, 3000, 2.5, 0.035); burst(t + 0.07 + Math.random() * 0.05, 0.025, 3800, 2800, 2.5, 0.03); },
    fanfare: function (t) { [523, 659, 784, 1047].forEach(function (fq, i) { tone(t + i * 0.11, fq, 0.35, 0.1, 'triangle'); }); tone(t + 0.44, 1047, 0.7, 0.08); },
    purr: purr,
    thunder: function (t) { burst(t, 1.6, 300, 60, 0.7, 0.4, 'lowpass'); burst(t + 0.12, 1.2, 180, 50, 0.8, 0.3, 'lowpass'); },
    // Lucca: denting digital tiga nada
    lucca: function (t) { tone(t, 784, 0.3, 0.05); tone(t + 0.1, 1175, 0.45, 0.045); tone(t + 0.2, 1568, 0.6, 0.03); tone(t, 392, 0.5, 0.03, 'triangle'); }
  };
  function unarm() {
    window.removeEventListener('pointerdown', arm, true);
    window.removeEventListener('keydown', arm, true);
  }
  function arm() {
    unarm();
    if (on && init()) level();
  }
  window.addEventListener('pointerdown', arm, true);
  window.addEventListener('keydown', arm, true);
  if (on && init()) level();
  return {
    isOn: function () { return on; },
    setOn: function (v) {
      on = !!v;
      try { localStorage.setItem('projSound', on ? '1' : '0'); } catch (e) { /* abaikan */ }
      if (on) init();
      level();
    },
    duck: function (v) { ducked = !!v; level(); },
    play: function (name) {
      if (!on || ducked || !SFX[name] || !init()) return;
      try { SFX[name](ctx.currentTime + 0.02); } catch (e) { /* abaikan */ }
    },
    destroy: function () {
      unarm();
      if (room) { try { room.stop(); } catch (e) { /* abaikan */ } room = null; }
      if (ctx) { try { ctx.close(); } catch (e) { /* abaikan */ } ctx = null; }
    }
  };
}

/**
 * Memasang perpustakaan 3D di dalam `host`. Melempar error kalau WebGL tidak
 * ada - pemanggil yang memutuskan kembali ke tampilan normal.
 *
 * opts: onOpenBook(id, info) - buku diambil dari rak; info.rect = posisi buku
 *                          itu di layar (koordinat viewport) supaya pembaca
 *                          buku di halaman bisa "menyambung" animasinya.
 *                          Pembaca memanggil returnBook() saat ditutup.
 *       onPick(id)       - cadangan kalau onOpenBook tidak ada
 *       fmtDate(d, opts)  - format tanggal yang sama dgn halaman
 *       reducedMotion     - tanpa animasi & gerak kamera otomatis
 *       intro             - mainkan adegan pintu terbuka (saat tombol 3D dipilih)
 */
export function createLibrary(host, opts) {
  opts = opts || {};
  // Sudut tambahan di ujung kanan rak (Design Studio: easel mockup + meja ERD,
  // ../js/studio3d.js). Boleh kosong - perpustakaan tetap jalan tanpanya.
  // Kaitnya: wing.width() lebar dinding tambahan, wing.build(ctx) membangun
  // isinya, wing.tip/click/hover/update untuk kursor & animasi, wing.lucca()
  // ringkasan untuk briefing Lucca.
  var wing = opts.wing || null;
  var reduced = !!opts.reducedMotion;
  var coarse = window.matchMedia && matchMedia('(pointer: coarse)').matches;
  var fmt = function (d) { return opts.fmtDate ? opts.fmtDate(d, { day: '2-digit', month: 'short' }) : d; };

  var renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, coarse ? 1.5 : 2));
  renderer.setClearColor(0x000000, 0);
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.0;
  renderer.shadowMap.enabled = !coarse;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;
  var aniso = Math.min(8, renderer.capabilities.getMaxAnisotropy());

  var canvas = renderer.domElement;
  canvas.className = 'c3-canvas';
  canvas.setAttribute('aria-hidden', 'true');
  host.appendChild(canvas);

  var labelsEl = document.createElement('div');
  labelsEl.className = 'c3-labels';
  host.appendChild(labelsEl);

  var tipEl = document.createElement('div');
  tipEl.className = 'c3-tip';
  host.appendChild(tipEl);

  var legendEl = document.createElement('div');
  legendEl.className = 'c3-legend';
  legendEl.innerHTML =
    '<div class="c3-legend-title">1 book = 1 project &middot; a bookcase per module &middot; a shelf per month</div>' +
    '<span class="c3-key k-live">Live</span><span class="c3-key k-done">Done</span>' +
    '<span class="c3-key k-prog">On Progress</span><span class="c3-key k-plan">Planned</span>' +
    '<span class="c3-key k-over">Overdue</span><span class="c3-older"></span>' +
    '<div class="c3-legend-sub">Thicker = longer project &middot; taller = higher priority &middot; ribbon = progress &middot; dusty = untouched ' + STALE_DAYS + '+ days</div>';
  host.appendChild(legendEl);

  var ctrlEl = document.createElement('div');
  ctrlEl.className = 'c3-ctrl';
  ctrlEl.innerHTML =
    '<span class="c3-eco" hidden title="Shadows and resolution were lowered to keep the library smooth on this device">Power saver</span>' +
    '<span class="c3-ctrl2">' +
      '<span class="c3-spot" hidden>' + ICONS.spot + '<b></b><button type="button" data-act="spot-x" aria-label="Turn off the spotlight" title="Turn off the spotlight">' + ICONS.close + '</button></span>' +
      '<span class="c3-time" hidden><button type="button" data-act="older" aria-label="Show earlier months" title="Earlier months">' + ICONS.back + '</button>' +
        '<span class="c3-time-l"></span><button type="button" data-act="newer" aria-label="Show later months" title="Later months">' + ICONS.next + '</button></span>' +
    '</span>' +
    '<span class="c3-hint">Drag to look around &middot; Ctrl + scroll to zoom &middot; arrow keys to browse</span>' +
    '<button type="button" class="c3-back" data-act="back">' + ICONS.back + '<span>All shelves</span></button>' +
    '<button type="button" class="c3-btn" data-act="catalog" aria-label="Find a book" title="Find a book (card catalog)" aria-expanded="false">' + ICONS.search + '</button>' +
    '<button type="button" class="c3-btn" data-act="present" aria-label="Present: guided tour in full screen" title="Present (guided tour, full screen)">' + ICONS.present + '</button>' +
    '<span class="c3-more"><button type="button" class="c3-btn" data-act="more" aria-label="More options" title="More" aria-haspopup="true" aria-expanded="false">' + ICONS.more + '</button>' +
      '<span class="c3-menu" role="menu" hidden>' +
        '<button type="button" role="menuitemcheckbox" data-act="sound"><span class="c3-mi">' + ICONS.soundOff + '</span>Library sounds<span class="c3-chk">' + ICONS.check + '</span></button>' +
        '<button type="button" role="menuitemcheckbox" data-act="spin"><span class="c3-mi">' + ICONS.play + '</span>Camera drift<span class="c3-chk">' + ICONS.check + '</span></button>' +
        '<button type="button" role="menuitem" data-act="walk"><span class="c3-mi">' + ICONS.walk + '</span>Walk around</button>' +
        '<button type="button" role="menuitem" data-act="snap"><span class="c3-mi">' + ICONS.camera + '</span>Save picture (PNG)</button>' +
        '<button type="button" role="menuitem" data-act="reset"><span class="c3-mi">' + ICONS.reset + '</span>Reset view</button>' +
      '</span></span>';
  host.appendChild(ctrlEl);
  var ecoEl = ctrlEl.querySelector('.c3-eco'), menuEl = ctrlEl.querySelector('.c3-menu'), moreBtn = ctrlEl.querySelector('[data-act="more"]');
  var timeEl = ctrlEl.querySelector('.c3-time'), spotEl = ctrlEl.querySelector('.c3-spot');

  // Keterangan tur presentasi (bawah tengah) & teks untuk pembaca layar
  var tourEl = document.createElement('div');
  tourEl.className = 'c3-tour';
  tourEl.hidden = true;
  tourEl.innerHTML = '<div class="c3-tour-k"></div><div class="c3-tour-t"></div><div class="c3-tour-l"></div>' +
    '<div class="c3-tour-bar"><button type="button" data-tour="prev" aria-label="Previous stop" title="Previous">' + ICONS.back + '</button>' +
    '<button type="button" data-tour="pause"></button>' +
    '<button type="button" data-tour="next" aria-label="Next stop" title="Next">' + ICONS.next + '</button>' +
    '<button type="button" class="c3-tour-x" data-tour="exit">' + ICONS.close + '<span>Exit</span></button></div>' +
    '<div class="c3-tour-prog"><span></span></div>';
  host.appendChild(tourEl);
  var tourProg = tourEl.querySelector('.c3-tour-prog span');
  var srEl = document.createElement('div');
  srEl.className = 'c3-sr';
  srEl.setAttribute('aria-live', 'polite');
  host.appendChild(srEl);
  // kabar langsung (perubahan dari rekan) & mode jalan-jalan
  var newsEl = document.createElement('div');
  newsEl.className = 'c3-news';
  newsEl.hidden = true;
  newsEl.setAttribute('role', 'status');
  newsEl.innerHTML = '<div class="c3-news-h"><span class="c3-lucca-eye" aria-hidden="true"></span>Lucca &middot; baru saja di perpustakaan</div><div class="c3-news-list"></div>';
  host.appendChild(newsEl);
  // Lucca: sapaan & laporan singkat sistem perpustakaan.
  // Lucca asisten pemilik project -> SELALU bicara bahasa Indonesia (teks UI lain tetap Inggris).
  var luccaEl = document.createElement('div');
  luccaEl.className = 'c3-lucca';
  luccaEl.hidden = true;
  luccaEl.setAttribute('role', 'status');
  luccaEl.innerHTML = '<div class="c3-lucca-h"><span class="c3-lucca-eye" aria-hidden="true"></span><b>Lucca</b><small>Asisten perpustakaan</small>' +
    '<button type="button" class="c3-lucca-x" data-lu="close" aria-label="Tutup" title="Tutup">' + ICONS.close + '</button></div>' +
    '<div class="c3-lucca-t"></div><div class="c3-lucca-a"></div>';
  host.appendChild(luccaEl);
  var walkEl = document.createElement('div');
  walkEl.className = 'c3-walk';
  walkEl.hidden = true;
  walkEl.innerHTML = '<span class="c3-walk-hint"><b>Walking</b><span class="c3-walk-keys"> &middot; W A S D or arrows to move &middot; drag to look &middot; Shift to run</span>' +
    '<span class="c3-walk-touch"> &middot; joystick to move &middot; drag to look</span></span>' +
    '<button type="button" class="c3-walk-x" data-walk="exit">' + ICONS.close + '<span>Exit</span></button>';
  host.appendChild(walkEl);
  var joyEl = document.createElement('div');
  joyEl.className = 'c3-joy';
  joyEl.innerHTML = '<span></span>';
  host.appendChild(joyEl);
  host.tabIndex = 0;
  host.setAttribute('aria-label', 'Project library in 3D. Arrow keys move between bookcases and books, Enter opens the selected book, Escape steps back.');

  // Lemari katalog: cari buku menurut nama project, modul atau PIC
  var catEl = document.createElement('div');
  catEl.className = 'c3-cat';
  catEl.hidden = true;
  catEl.setAttribute('role', 'dialog');
  catEl.setAttribute('aria-label', 'Card catalog');
  catEl.innerHTML =
    '<div class="c3-cat-head">' + ICONS.search +
      '<input type="text" class="c3-cat-in" placeholder="Find a book: project, module or PIC" aria-label="Find a book" autocomplete="off" spellcheck="false">' +
      '<button type="button" class="c3-cat-x" aria-label="Close the catalog">' + ICONS.close + '</button></div>' +
    '<div class="c3-cat-list" role="listbox" aria-label="Books"></div>' +
    '<div class="c3-cat-foot"></div>';
  host.appendChild(catEl);
  var catIn = catEl.querySelector('.c3-cat-in'), catList = catEl.querySelector('.c3-cat-list'), catSel = 0, catRes = [];
  var sound = createSound();

  // ---- scene, camera, lights ------------------------------------------
  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(32, 1, 0.1, 400);
  var fitCam = new THREE.PerspectiveCamera(32, 1, 0.1, 400);

  var pmrem = new THREE.PMREMGenerator(renderer);
  var envScene = buildEnvScene();
  var envTex = pmrem.fromScene(envScene, 0.04).texture;
  scene.environment = envTex;
  envScene.traverse(function (o) { if (o.geometry) o.geometry.dispose(); if (o.material) o.material.dispose(); });

  var hemi = new THREE.HemisphereLight(0xffe3c0, 0x2a2040, 0.95);
  scene.add(hemi);
  var key = new THREE.DirectionalLight(0xffdcb0, 2.3);
  key.castShadow = renderer.shadowMap.enabled;
  key.shadow.mapSize.set(2048, 2048);
  key.shadow.bias = -0.0004;
  key.shadow.normalBias = 0.02;
  scene.add(key);
  scene.add(key.target);
  var fill = new THREE.DirectionalLight(0x7f9cff, 0.6);
  fill.position.set(-10, 6, 6);
  scene.add(fill);
  // Dua lampu di depan rak terdekat - dibuat sekali saja (menambah/mengurangi
  // jumlah lampu memaksa semua shader dikompilasi ulang).
  var lampLights = [new THREE.PointLight(0xffbf6e, 0, 8, 2), new THREE.PointLight(0xffbf6e, 0, 8, 2)];
  lampLights.forEach(function (l) { scene.add(l); });
  var deskLight = new THREE.PointLight(0xffc27a, 0, 3.6, 2);   // lampu baca di meja pustakawan
  scene.add(deskLight);

  // ---- bahan & geometri bersama -----------------------------------------
  var woodTex = woodTexture(); woodTex.anisotropy = aniso;
  var plankTex = plankTexture(); plankTex.repeat.set(7, 7); plankTex.anisotropy = aniso;
  var rugTex = rugTexture(); rugTex.anisotropy = aniso;
  var glowTex = radialTexture([[0, 'rgba(255,255,255,1)'], [0.25, 'rgba(255,255,255,.55)'], [1, 'rgba(255,255,255,0)']]);
  var dotTex = radialTexture([[0, 'rgba(255,255,255,1)'], [0.4, 'rgba(255,255,255,.35)'], [1, 'rgba(255,255,255,0)']]);
  var shadowTex = radialTexture([[0, 'rgba(0,0,0,1)'], [0.55, 'rgba(0,0,0,.6)'], [1, 'rgba(0,0,0,0)']]);
  var fadeTex = fadeTexture();

  var woodMat = new THREE.MeshStandardMaterial({ map: woodTex, color: 0xc29572, roughness: 0.6, metalness: 0, envMapIntensity: 0.6, emissive: 0x1a0c05 });
  // papan belakang diberi sedikit pendar hangat - tanpa itu bagian dalam rak hitam pekat
  var backMat = new THREE.MeshStandardMaterial({ map: woodTex, color: 0x6e4c38, roughness: 0.85, metalness: 0, envMapIntensity: 0.3, emissive: 0x2e1a0e });
  var brassMat = new THREE.MeshStandardMaterial({ color: 0xcaa055, roughness: 0.3, metalness: 1, envMapIntensity: 1.2 });
  var pagesMat = new THREE.MeshStandardMaterial({ color: 0xe9dfc4, roughness: 0.9, metalness: 0 });
  // lantai papan di dalam batas ruangan (polygonOffset: karpet & bayangan tipis di atasnya tidak berkedip)
  var floorMat = new THREE.MeshStandardMaterial({ map: plankTex, roughness: 0.5, metalness: 0, envMapIntensity: 0.5, polygonOffset: true, polygonOffsetFactor: 1, polygonOffsetUnits: 1 });
  var rugMat = new THREE.MeshStandardMaterial({ map: rugTex, roughness: 0.95, metalness: 0 });
  var contactMat = new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, opacity: 0.55, depthWrite: false });
  var stripMats = {
    cur: new THREE.MeshBasicMaterial({ color: new THREE.Color(2.2, 1.6, 0.7) }),
    hi: new THREE.MeshBasicMaterial({ color: new THREE.Color(0.8, 1.3, 2.6) })
  };
  var washMats = {
    cur: new THREE.MeshBasicMaterial({ map: fadeTex, color: 0xffc56b, transparent: true, opacity: 0.5, depthWrite: false, blending: THREE.AdditiveBlending }),
    hi: new THREE.MeshBasicMaterial({ map: fadeTex, color: 0x5d95ff, transparent: true, opacity: 0.5, depthWrite: false, blending: THREE.AdditiveBlending })
  };
  var ribbonMats = {
    prog: new THREE.MeshStandardMaterial({ color: 0x3b82f6, emissive: 0x3b82f6, emissiveIntensity: 0.6, roughness: 0.5, side: THREE.DoubleSide }),
    over: new THREE.MeshStandardMaterial({ color: 0xef4444, emissive: 0xef4444, emissiveIntensity: 0.6, roughness: 0.5, side: THREE.DoubleSide })
  };
  var ghostLineMat = new THREE.LineBasicMaterial({ color: 0xa8c8ff, transparent: true, opacity: 0.8 });
  var shadeMat = new THREE.MeshStandardMaterial({ color: 0x2d4a3a, roughness: 0.35, metalness: 0.6, side: THREE.DoubleSide, envMapIntensity: 1.2 });
  var bulbMat = new THREE.MeshBasicMaterial({ color: new THREE.Color(3, 2.4, 1.5) });
  var lightWashMat = new THREE.MeshBasicMaterial({ map: fadeTex, color: 0xffc98a, transparent: true, opacity: 0.16, depthWrite: false, blending: THREE.AdditiveBlending });

  var bookGeo = bookGeometry();
  var bookEdges = new THREE.EdgesGeometry(new THREE.BoxGeometry(1, 1, 1));
  var stripGeo = new THREE.BoxGeometry(IW - 0.06, 0.012, 0.014);
  var washGeo = new THREE.PlaneGeometry(IW, SP - BT);
  var plaqueGeo = new THREE.PlaneGeometry(CW * 0.66, 0.28);
  var planeGeo = new THREE.PlaneGeometry(1, 1);
  var lampShadeGeo = new THREE.BoxGeometry(CW * 0.5, 0.07, 0.13);
  var lampBulbGeo = new THREE.BoxGeometry(CW * 0.46, 0.014, 0.04);
  var lampArmGeo = boxesGeometry([
    { w: 0.025, h: 0.025, d: 0.24, x: -CW * 0.17, y: 0, z: 0 },
    { w: 0.025, h: 0.025, d: 0.24, x: CW * 0.17, y: 0, z: 0 }
  ]);
  var sharedGeo = [bookGeo, bookEdges, stripGeo, washGeo, plaqueGeo, planeGeo, lampShadeGeo, lampBulbGeo, lampArmGeo];
  var sharedMat = [woodMat, backMat, brassMat, pagesMat, floorMat, rugMat, contactMat, stripMats.cur, stripMats.hi, washMats.cur, washMats.hi,
    ribbonMats.prog, ribbonMats.over, ghostLineMat, shadeMat, bulbMat, lightWashMat];
  var sharedTex = [woodTex, plankTex, rugTex, glowTex, dotTex, shadowTex, fadeTex];

  // Sudut pustakawan & suasana
  var cobwebTex = cobwebTexture(), plasterTex = plasterTexture(), catalogTex = catalogTexture(), patchTex = windowPatchTexture();
  plasterTex.repeat.set(0.9, 0.9);
  catalogTex.anisotropy = aniso;
  var cobwebMat = new THREE.MeshBasicMaterial({ map: cobwebTex, color: 0xe9e4da, transparent: true, opacity: 0.55, depthWrite: false, side: THREE.DoubleSide });
  var dustyPagesMat = new THREE.MeshStandardMaterial({ color: 0xa69d8c, roughness: 1, metalness: 0 });
  var plasterMat = new THREE.MeshStandardMaterial({ map: plasterTex, color: 0x8d83a8, roughness: 0.95, metalness: 0, emissive: 0x150f22 });
  var catalogMat = new THREE.MeshStandardMaterial({ map: catalogTex, emissiveMap: catalogTex, emissive: 0xffffff, emissiveIntensity: 0.08, roughness: 0.55, metalness: 0.05 });
  var skyMat = new THREE.MeshBasicMaterial({ color: 0xffffff });
  var shaftMat = new THREE.MeshBasicMaterial({ map: fadeTex, transparent: true, opacity: 0.3, depthWrite: false, blending: THREE.AdditiveBlending, side: THREE.DoubleSide });
  var patchMat = new THREE.MeshBasicMaterial({ map: patchTex, transparent: true, opacity: 0.45, depthWrite: false, blending: THREE.AdditiveBlending });
  var glassMat = new THREE.MeshStandardMaterial({ color: 0x1e6e47, emissive: 0x0c3f27, roughness: 0.25, metalness: 0.2, side: THREE.DoubleSide, envMapIntensity: 1.2 });
  var poolMat = new THREE.MeshBasicMaterial({ map: glowTex, color: 0xffb860, transparent: true, opacity: 0.32, depthWrite: false, blending: THREE.AdditiveBlending });
  var wheelMat = new THREE.MeshStandardMaterial({ color: 0x1a1612, roughness: 0.6, metalness: 0.3 });
  sharedMat.push(cobwebMat, dustyPagesMat, plasterMat, catalogMat, skyMat, shaftMat, patchMat, glassMat, poolMat, wheelMat);
  sharedTex.push(cobwebTex, plasterTex, catalogTex, patchTex);

  // Isi ruangan (dinding, perabot, tanaman, lampu gantung, jam)
  var panelTex = panelTexture(), damaskTex = damaskTexture(), cofferTex = cofferTexture();
  var leafTex = leafTexture(), globeTex = globeTexture(), clockTex = clockFaceTexture();
  panelTex.anisotropy = damaskTex.anisotropy = globeTex.anisotropy = aniso;
  // Dinding ruangan (UV dalam ubin tekstur, lihat buildShell). Dinding belakang
  // dua muka; dinding samping & depan satu muka menghadap ke dalam - dari luar
  // tembus pandang seperti rumah boneka, jadi tidak pernah menutupi rak.
  var wallLowMat = new THREE.MeshStandardMaterial({ map: panelTex, side: THREE.DoubleSide, roughness: 0.7, metalness: 0, emissive: 0x140b06 });
  var wallUpMat = new THREE.MeshStandardMaterial({ map: damaskTex, side: THREE.DoubleSide, roughness: 0.9, metalness: 0, emissive: 0x07120c });
  var wallLowIn = wallLowMat.clone(), wallUpIn = wallUpMat.clone(), trimMat = woodMat.clone();
  wallLowIn.side = wallUpIn.side = THREE.FrontSide;
  trimMat.side = THREE.DoubleSide;
  var ceilMat = new THREE.MeshStandardMaterial({ map: cofferTex, emissiveMap: cofferTex, emissive: 0xffffff, emissiveIntensity: 0.16, roughness: 0.85, metalness: 0 });
  var slabMat = new THREE.MeshStandardMaterial({ color: 0x2a1910, roughness: 0.6, metalness: 0, envMapIntensity: 0.4 });
  var cutMat = new THREE.MeshStandardMaterial({ color: 0x4a3326, roughness: 0.9, metalness: 0, emissive: 0x120a06 });
  var doorMat = new THREE.MeshStandardMaterial({ map: woodTex, color: 0x9a6038, roughness: 0.55, metalness: 0, emissive: 0x2a160a });
  var pupilMat = new THREE.MeshBasicMaterial({ color: 0x040910 }), glintMat = new THREE.MeshBasicMaterial({ color: 0xffffff }), hitMat = new THREE.MeshBasicMaterial({ visible: false });
  var leatherMat = new THREE.MeshStandardMaterial({ color: 0x5b1f1a, roughness: 0.42, metalness: 0.05, envMapIntensity: 0.8 });
  var cushionMat = new THREE.MeshStandardMaterial({ color: 0x2f4a36, roughness: 0.6 });
  var potMat = new THREE.MeshStandardMaterial({ color: 0x9b5a3a, roughness: 0.8 });
  var soilMat = new THREE.MeshStandardMaterial({ color: 0x2a1c12, roughness: 1 });
  var leafMat = new THREE.MeshStandardMaterial({ map: leafTex, alphaTest: 0.5, side: THREE.DoubleSide, roughness: 0.6 });
  var globeMat = new THREE.MeshStandardMaterial({ map: globeTex, roughness: 0.5, metalness: 0.05, envMapIntensity: 0.8 });
  var clockMat = new THREE.MeshStandardMaterial({ map: clockTex, roughness: 0.6, emissiveMap: clockTex, emissive: 0xffffff, emissiveIntensity: 0.15 });
  var handMat = new THREE.MeshStandardMaterial({ color: 0x1c140a, roughness: 0.5 });
  var redMat = new THREE.MeshStandardMaterial({ color: 0xb3261e, roughness: 0.5 });
  var porcelainMat = new THREE.MeshStandardMaterial({ color: 0xf4f1ea, roughness: 0.3 });
  var shadeFabMat = new THREE.MeshStandardMaterial({ color: 0xf0dfb8, emissive: 0xffb860, emissiveIntensity: 0.55, roughness: 0.9, side: THREE.DoubleSide });
  var candleMat = new THREE.MeshStandardMaterial({ color: 0xf3ead2, emissive: 0x3a2a10, roughness: 0.7 });
  var flameMat = new THREE.MeshBasicMaterial({ color: new THREE.Color(3, 2.2, 1.1) });
  var flameGlow = new THREE.SpriteMaterial({ map: glowTex, color: 0xffb35c, transparent: true, opacity: 0.8, depthWrite: false, blending: THREE.AdditiveBlending });
  var unitBox = new THREE.BoxGeometry(1, 1, 1);
  var keysTex = keysTexture();
  keysTex.anisotropy = aniso;
  var feltMat = new THREE.MeshStandardMaterial({ color: 0x24272e, roughness: 0.95 });
  var darkMetalMat = new THREE.MeshStandardMaterial({ color: 0x16171c, roughness: 0.45, metalness: 0.5, envMapIntensity: 0.6 });
  var keysMat = new THREE.MeshStandardMaterial({ map: keysTex, roughness: 0.6, emissiveMap: keysTex, emissive: 0xffffff, emissiveIntensity: 0.15 });
  var stickyMat = new THREE.MeshStandardMaterial({ color: 0xffe066, roughness: 0.9, emissive: 0x3a3000, side: THREE.DoubleSide });
  var pcGlassMat = new THREE.MeshStandardMaterial({ color: 0x223355, roughness: 0.1, metalness: 0.3, transparent: true, opacity: 0.45 });
  var rgbMat = new THREE.MeshBasicMaterial({ color: 0xff3d7f, toneMapped: false });
  var coffeeMat = new THREE.MeshStandardMaterial({ color: 0x3b2314, roughness: 0.3 });
  var duckMat = new THREE.MeshStandardMaterial({ color: 0xffd23f, roughness: 0.35 });
  var beakMat = new THREE.MeshStandardMaterial({ color: 0xff8c1a, roughness: 0.5 });
  var eyeMat = new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.3 });
  var chairFabMat = new THREE.MeshStandardMaterial({ color: 0x1d1f26, roughness: 0.85 });
  var hoodieMat = new THREE.MeshStandardMaterial({ color: 0x2d3446, roughness: 0.9 });
  var jeansMat = new THREE.MeshStandardMaterial({ color: 0x26324a, roughness: 0.85 });
  var shoeMat = new THREE.MeshStandardMaterial({ color: 0xe9e6df, roughness: 0.7 });
  var skinMat = new THREE.MeshStandardMaterial({ color: 0xb9805a, roughness: 0.7 });
  var hairMat = new THREE.MeshStandardMaterial({ color: 0x1b1410, roughness: 0.8 });
  var phoneMat = new THREE.MeshStandardMaterial({ color: 0x141414, roughness: 0.4, metalness: 0.3 });
  var screenLight = new THREE.PointLight(0x7fb0ff, 0, 3.2, 2);   // cahaya kebiruan dari monitor
  scene.add(screenLight);
  sharedMat.push(feltMat, darkMetalMat, keysMat, stickyMat, pcGlassMat, rgbMat, coffeeMat, duckMat, beakMat, eyeMat, chairFabMat, hoodieMat, jeansMat, shoeMat, skinMat, hairMat, phoneMat);
  sharedTex.push(keysTex);
  // perapian, hujan, pita emas, bunga, kucing, pustakawan
  var flameTex = flameTexture(), rainTex = rainTexture();
  rainTex.repeat.set(2.2, 1.6);
  var marbleMat = new THREE.MeshStandardMaterial({ color: 0xd9cfbf, roughness: 0.35, metalness: 0, envMapIntensity: 0.6 });
  var sootMat = new THREE.MeshStandardMaterial({ color: 0x15110d, roughness: 1 });
  var logMat = new THREE.MeshStandardMaterial({ color: 0x3a2414, roughness: 0.9, emissive: 0x2a0d02 });
  var emberMat = new THREE.MeshBasicMaterial({ map: glowTex, color: 0xff6a1a, transparent: true, opacity: 0.85, depthWrite: false, blending: THREE.AdditiveBlending });
  var fireSprMat = new THREE.SpriteMaterial({ map: flameTex, color: 0xffffff, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending });
  var rainMat = new THREE.MeshBasicMaterial({ map: rainTex, transparent: true, opacity: 0.7, depthWrite: false });
  var sashMat = new THREE.MeshStandardMaterial({ color: 0xe9c252, emissive: 0x8a6510, emissiveIntensity: 0.9, metalness: 0.5, roughness: 0.35, side: THREE.DoubleSide });
  var flowerMat = new THREE.MeshStandardMaterial({ color: 0xff8fb1, emissive: 0x401020, roughness: 0.6 });
  var catMat = new THREE.MeshStandardMaterial({ color: 0xd98a3d, roughness: 0.85 });
  var catPinkMat = new THREE.MeshStandardMaterial({ color: 0xe88a9a, roughness: 0.6 });
  var cardiganMat = new THREE.MeshStandardMaterial({ color: 0x7a2e3a, roughness: 0.9 });
  var skirtMat = new THREE.MeshStandardMaterial({ color: 0x3a3a46, roughness: 0.85 });
  var skin2Mat = new THREE.MeshStandardMaterial({ color: 0xc89068, roughness: 0.7 });
  var hair2Mat = new THREE.MeshStandardMaterial({ color: 0x2a1d16, roughness: 0.8 });
  var smileMat = new THREE.MeshStandardMaterial({ color: 0x6a2a24, roughness: 0.6 });
  var fireLight = new THREE.PointLight(0xff8a3c, 0, 5.5, 2);   // cahaya perapian
  scene.add(fireLight);
  sharedMat.push(marbleMat, sootMat, logMat, emberMat, fireSprMat, rainMat, sashMat, flowerMat, catMat, catPinkMat, cardiganMat, skirtMat, skin2Mat, hair2Mat, smileMat);
  sharedTex.push(flameTex, rainTex);
  var chandLight = new THREE.PointLight(0xffc27a, 0, 9, 2);   // lampu gantung di atas meja baca
  scene.add(chandLight);
  var luccaLight = new THREE.PointLight(0x5ee7ff, 0, 4, 2);   // cahaya hologram Lucca
  scene.add(luccaLight);
  sharedMat.push(wallLowMat, wallUpMat, leatherMat, cushionMat, potMat, soilMat, leafMat, globeMat, clockMat, handMat, redMat, porcelainMat, shadeFabMat, candleMat, flameMat, flameGlow);
  sharedTex.push(panelTex, damaskTex, cofferTex, leafTex, globeTex, clockTex);
  sharedMat.push(wallLowIn, wallUpIn, trimMat, ceilMat, slabMat, cutMat, doorMat, pupilMat, glintMat, hitMat);
  sharedGeo.push(unitBox);

  // ---- debu yang melayang di depan rak -----------------------------------
  var DUST = coarse ? 70 : 160;
  var dustPos = new Float32Array(DUST * 3);
  for (var i = 0; i < DUST; i++) {
    dustPos[i * 3] = Math.random() - 0.5;
    dustPos[i * 3 + 1] = Math.random();
    dustPos[i * 3 + 2] = Math.random() - 0.5;
  }
  var dustGeo = new THREE.BufferGeometry();
  dustGeo.setAttribute('position', new THREE.BufferAttribute(dustPos, 3));
  var dustMat = new THREE.PointsMaterial({ map: dotTex, color: 0xffd9a0, size: 0.06, transparent: true, opacity: 0.7, depthWrite: false, blending: THREE.AdditiveBlending });
  var dust = new THREE.Points(dustGeo, dustMat);
  scene.add(dust);

  // ---- debu yang berkilau di berkas cahaya jendela ----------------------
  var SD = coarse ? 36 : 72, sdBase = new Float32Array(SD * 4);
  for (i = 0; i < SD; i++) {
    sdBase[i * 4] = Math.random();
    sdBase[i * 4 + 1] = Math.random() - 0.5;
    sdBase[i * 4 + 2] = (Math.random() - 0.5) * 0.3;
    sdBase[i * 4 + 3] = Math.random() * 6.283;
  }
  var sdGeo = new THREE.BufferGeometry();
  sdGeo.setAttribute('position', new THREE.BufferAttribute(new Float32Array(SD * 3), 3));
  var shaftDustMat = new THREE.PointsMaterial({ map: dotTex, color: 0xfff1d0, size: 0.04, transparent: true, opacity: 0.85, depthWrite: false, blending: THREE.AdditiveBlending });
  var shaftDust = new THREE.Points(sdGeo, shaftDustMat);
  shaftDust.frustumCulled = false;
  sharedMat.push(shaftDustMat);

  // ---- state ----------------------------------------------------------
  var lib = null, cases = [], books = [], pickables = [], labels = [], trash = [];
  // clock = waktu animasi (ms) yang maju per frame, maksimal 50 ms sekali:
  // frame pertama (kompilasi shader) bisa makan beberapa detik, dan adegan
  // pintu & buku yang meluncur tidak boleh ikut terlewati karenanya.
  var allCorners = [], lastKey = '', buildAt = 0, built = false, clock = 0;
  var target = new THREE.Vector3(), goal = new THREE.Vector3(), homeTarget = new THREE.Vector3();
  var view = { theta: HOME.theta, phi: HOME.phi, radius: 20, tTheta: HOME.theta, tPhi: HOME.phi, tRadius: 20, fit: 20 };
  var focus = -1, spin = !reduced, lastInteract = 0, driftHome = HOME.theta, driftPhase = 0;
  var size = { w: 0, h: 0 }, layout = 'right';
  var hovered = null, hoveredCase = -1, pendingHover = null, drag = null, pendingAct = null;
  var intro = null, door = null, taken = null;
  var tmpV = new THREE.Vector3(), tmpR = new THREE.Vector3(), tmpU = new THREE.Vector3();
  var corner = null, found = null, hoverHit = null, hoverThing = null, hoverKey = '', tipFor = null;
  var lastProjects = [], shelfById = {}, laneColors = {}, lampMul = 1, catGlow = 0;
  var todPhase = '', todHour = null, todCheck = 0, skyTex = null;
  var perf = { lvl: 0, n: 0, sum: 0, since: 0 }, perfLock = false;
  var monthShift = 0, monthList = [], monthWin = [], lastData = null, spotPic = '', tour = null, presenting = false, presentHome = null;
  var seasonOverride = null, decorMats = [], room = null;
  var weather = '', storm = { next: 0, t: -1e9 }, skyBase = 1.7, walk = null, walkObs = [], walkLim = { x0: -99, x1: 99, z1: 99 }, fireBase = 0;
  var luccaPos = null, luccaGreetAt = 0;
  try { perfLock = localStorage.getItem('lib3dQuality') === 'high'; } catch (e) { /* abaikan */ }
  // rak ke-i, atau sudut pustakawan (DESK)
  var studioStop = null;   // titik tuju kamera sudut Design Studio (diisi build)
  function stopAt(i) { return i === DESK ? corner : i === STUDIO ? studioStop : i >= 0 ? cases[i] || null : null; }
  applyTime(currentPhase());

  function track(o) { trash.push(o); return o; }

  function addLabel(kind, html, local, ci, cls) {
    var el = document.createElement('div');
    el.className = 'c3-label c3-' + kind + (cls ? ' ' + cls : '');
    el.innerHTML = '<div class="c3-in">' + html + '</div>';
    labelsEl.appendChild(el);
    labels.push({ el: el, kind: kind, local: local, ci: ci, w: 0 });
  }

  function disposeLib() {
    if (lib) scene.remove(lib);
    trash.forEach(function (o) { o.dispose(); });
    trash = []; cases = []; books = []; pickables = []; labels = []; allCorners = []; studioStop = null;
    labelsEl.innerHTML = '';
    lib = null;
    hovered = null; hoveredCase = -1; taken = null;
    corner = null; found = null; hoverHit = null; hoverThing = null; hoverKey = ''; tipFor = null; shelfById = {};
    decorMats = [];
    room = null;
    walkObs = [];
    chandLight.intensity = 0;
    luccaLight.intensity = 0;
    screenLight.intensity = 0;
    fireLight.intensity = 0;
    deskLight.intensity = 0;
    tipEl.classList.remove('show');
  }

  function signature(d) {
    return today0().getTime() + '|' + (d.lanes || []).map(function (l) { return l.name + l.color; }).join(',') + '|' + d.currentMonth + '|' + d.highlightMonth + '|' + (d.filtered ? 1 : 0) + '|' +
      (d.projects || []).map(function (p) {
        return [p.id, p.status, monthOf(p), p.is_overdue, p.category, p.project_name, p.live_date, p.actual_date, p.updated_date, p.progress, p.priority, p.start_date, p.target_date, p.pic].join(':');
      }).join(',');
  }

  // ---- papan nama & kartu "rak baru" ------------------------------------
  function plaqueTexture(name, sub, color) {
    var t = brassTexture(1024, 176, function (g, w, h) {
      g.beginPath();
      g.arc(62, h / 2, 17, 0, Math.PI * 2);
      g.fillStyle = color; g.fill();
      g.lineWidth = 3; g.strokeStyle = 'rgba(60,40,12,.9)'; g.stroke();
      g.textBaseline = 'middle';
      g.font = '600 30px Georgia, "Times New Roman", serif';
      var subW = g.measureText(sub).width;
      g.textAlign = 'right';
      engrave(g, sub, w - 40, h / 2 + 2);
      g.textAlign = 'left';
      fitFont(g, name, '700', 96, 'Georgia, "Times New Roman", serif', w - 104 - subW - 70);
      engrave(g, name, 98, h / 2 + 3);
    });
    t.anisotropy = aniso;
    return t;
  }
  function cardTexture(lines) {
    return canvasTex(320, 200, function (g, w, h) {
      g.fillStyle = '#efe6cf';
      roundRect(g, 4, 4, w - 8, h - 8, 10); g.fill();
      g.strokeStyle = '#b58a3c'; g.lineWidth = 3;
      roundRect(g, 14, 14, w - 28, h - 28, 6); g.stroke();
      g.textAlign = 'center'; g.textBaseline = 'middle';
      g.fillStyle = '#9a6b1f'; g.font = '700 20px Arial, sans-serif';
      g.fillText(lines[0], w / 2, 54);
      g.fillStyle = '#2b1d0e';
      fitFont(g, lines[1], '700', 40, 'Georgia, "Times New Roman", serif', w - 50);
      g.fillText(lines[1], w / 2, 100);
      g.fillStyle = '#6b5a40'; g.font = '400 19px Arial, sans-serif';
      g.fillText(lines[2], w / 2, 146);
    });
  }

  // ---- atlas punggung buku: satu kanvas untuk semua buku ----------------
  function buildAtlas(list) {
    var rows = Math.ceil(list.length / ATLAS_COLS);
    var cc = document.createElement('canvas'), ec = document.createElement('canvas');
    cc.width = ec.width = ATLAS_COLS * SPINE_W;
    cc.height = ec.height = rows * SPINE_H;
    var g = cc.getContext('2d'), e = ec.getContext('2d');
    list.forEach(function (b, k) {
      b.cell = k;
      drawSpine(g, e, (k % ATLAS_COLS) * SPINE_W, Math.floor(k / ATLAS_COLS) * SPINE_H, b);
    });
    var map = track(new THREE.CanvasTexture(cc)), glow = track(new THREE.CanvasTexture(ec));
    map.colorSpace = glow.colorSpace = THREE.SRGBColorSpace;
    map.anisotropy = glow.anisotropy = aniso;
    return { map: map, glow: glow, rows: rows };
  }
  // Salinan tekstur atlas yang menunjuk satu sel (gambarnya tetap satu di GPU).
  function cellTex(base, atlas, k) {
    var t = track(base.clone());
    t.repeat.set(1 / ATLAS_COLS, 1 / atlas.rows);
    t.offset.set((k % ATLAS_COLS) / ATLAS_COLS, 1 - (Math.floor(k / ATLAS_COLS) + 1) / atlas.rows);
    return t;
  }

  function build(d) {
    // Buku yang sedang dibaca tetap "dipinjam" walau raknya dibangun ulang
    // (mis. data dimuat ulang sesudah tugas dicentang di dalam buku).
    var keep = taken ? { id: String(taken.mesh.userData.p.id), where: taken.mesh.userData.where, handed: taken.handed } : null;
    disposeLib();
    lib = new THREE.Group();
    scene.add(lib);

    var projects = d.projects || [];
    lastProjects = projects;
    var allMonths = monthWindow(projects.map(monthOf).filter(Boolean), d.currentMonth);
    if (!allMonths.length) allMonths = [d.currentMonth];
    var maxShift = Math.max(0, allMonths.length - MAX_MONTHS);
    monthShift = clamp(monthShift, 0, maxShift);
    var months = allMonths.slice(Math.max(0, allMonths.length - MAX_MONTHS - monthShift), allMonths.length - monthShift);
    monthList = allMonths;
    monthWin = months;
    var season = seasonOverride != null ? seasonOverride : seasonOf(today0());
    var lanes = (d.lanes || []).slice();
    projects.forEach(function (p) {
      var nm = p.category || 'General';
      if (!lanes.some(function (l) { return l.name === nm; })) lanes.push({ name: nm, color: '#94a3b8' });
    });
    if (!lanes.length) lanes = [{ name: 'No module yet', color: '#94a3b8' }];
    if (!months.length) months = [d.currentMonth];
    // index.php mengurutkan modul dari yang terbanyak; dibalik supaya rak
    // terbanyak paling kanan = paling dekat ke kamera.
    lanes.reverse();
    laneColors = {};
    lanes.forEach(function (l) { laneColors[l.name] = l.color; });

    var M = months.length, Hc = PLINTH + M * SP + BT, Ht = Hc + CROWN, n = lanes.length;
    var counts = {}, byCell = {}, older = 0, newer = 0;
    projects.forEach(function (p) {
      var nm = p.category || 'General';
      counts[nm] = (counts[nm] || 0) + 1;
      var mk = monthOf(p), mi = months.indexOf(mk);
      if (mi < 0) { if (mk && mk > months[months.length - 1]) newer++; else older++; return; }
      (byCell[nm + '|' + mi] = byCell[nm + '|' + mi] || []).push(p);
    });
    legendEl.querySelector('.c3-older').textContent = older || newer
      ? [older ? '+' + older + ' earlier' : '', newer ? '+' + newer + ' later' : ''].filter(Boolean).join(' \u00b7 ') + ' not shown'
      : '';
    // Geser waktu: hanya muncul kalau bulannya lebih banyak dari baris rak
    timeEl.hidden = !maxShift;
    timeEl.querySelector('.c3-time-l').textContent = rangeLabel(months[0], months[months.length - 1]);
    timeEl.querySelector('[data-act="older"]').disabled = monthShift >= maxShift;
    timeEl.querySelector('[data-act="newer"]').disabled = monthShift <= 0;

    // Tata letak buku dulu (ukuran, posisi, warna), baru atlasnya digambar.
    var entries = [], ends = {}, byId = {};
    function makeEntry(p, ci, row, col) {
      var r = rng(hashStr(String(p.id) + '|' + p.project_name)), k = statusKey(p), sh = bookShape(p, r);
      var b = { p: p, key: k, t: sh.t, h: sh.h, d: sh.d, color: leather(col, r, k), ci: ci, row: row, stale: staleDays(p) };
      if (b.stale) b.color.lerp(DUST_COL, 0.3);
      return b;
    }
    lanes.forEach(function (lane, ci) {
      months.forEach(function (m, mi) {
        var list = (byCell[lane.name + '|' + mi] || []).sort(function (a, b) {
          return ((STATUS_ORDER[a.status] != null ? STATUS_ORDER[a.status] : 9) - (STATUS_ORDER[b.status] != null ? STATUS_ORDER[b.status] : 9)) || dateOf(a).localeCompare(dateOf(b));
        });
        if (!list.length) return;
        var row = list.map(function (p) { return makeEntry(p, ci, M - 1 - mi, lane.color); });
        var total = row.reduce(function (s, b) { return s + b.t; }, 0) + (row.length - 1) * 0.005;
        var f = Math.min(1, (IW - 0.12) / total), x = -IW / 2 + 0.03;
        row.forEach(function (b, k) {
          b.t *= f;
          b.x = x + b.t / 2;
          b.k = k;
          x += b.t + 0.005 * f;
          entries.push(b);
          byId[String(b.p.id)] = b;
        });
        ends[ci + '|' + mi] = x;
      });
    });

    // Sudut pustakawan: yang perlu perhatian (meja), yang baru selesai (troli)
    // & angka papan pengumuman - dari data yang sama dgn rak.
    var T0 = today0(), st = { overdue: 0, dueSoon: 0, doneMonth: 0, prog: 0, progSum: 0, onTime: 0, measured: 0 };
    var deskP = [], trolleyP = [];
    projects.forEach(function (p) {
      if (isDone(p.status)) {
        if (p.actual_date) {
          st.measured++;
          if (!p.target_date || String(p.actual_date).substring(0, 10) <= String(p.target_date).substring(0, 10)) st.onTime++;
        }
        if (monthOf(p) === d.currentMonth) st.doneMonth++;
        var rd = parseDay(p.actual_date || p.live_date), ago = rd ? daysFrom(rd, T0) : -1;
        if (ago >= 0 && ago < 7) trolleyP.push(p);
        return;
      }
      if (p.status === 'On Progress') { st.prog++; st.progSum += clamp(parseInt(p.progress, 10) || 0, 0, 100); }
      if (+p.is_overdue === 1) { st.overdue++; deskP.push(p); return; }
      var td = parseDay(p.target_date), left = td ? daysFrom(T0, td) : -1;
      if (left >= 0 && left <= 7) { st.dueSoon++; deskP.push(p); }
    });
    deskP.sort(function (a, b) { return (+b.is_overdue - +a.is_overdue) || String(a.target_date).localeCompare(String(b.target_date)); });
    st.attn = deskP;   // dipakai Lucca untuk laporannya
    trolleyP.sort(function (a, b) { return String(b.actual_date || b.live_date).localeCompare(String(a.actual_date || a.live_date)); });
    var extras = [];
    function cornerEntry(p) {
      var b = byId[String(p.id)];
      if (b) return b;
      b = makeEntry(p, DESK, 0, laneColors[p.category || 'General'] || '#94a3b8');
      extras.push(b);
      byId[String(p.id)] = b;
      return b;
    }
    var deskE = deskP.slice(0, 12).map(cornerEntry), trolleyE = trolleyP.slice(0, 12).map(cornerEntry);
    // Book of the month: selesai bulan ini (kalau belum ada: bulan lalu), tepat
    // waktu, paling cepat dari targetnya; seri -> yang pengerjaannya paling lama.
    var botm = null, botmMonth = '';
    [d.currentMonth, prevMonthKey(String(d.currentMonth || '2000-01'))].some(function (mk) {
      var best = null, bestScore = -1;
      projects.forEach(function (p) {
        if (!isDone(p.status) || !p.actual_date || monthOf(p) !== mk) return;
        var a = parseDay(p.actual_date), t = parseDay(p.target_date), st0 = parseDay(p.start_date);
        var early = a && t ? daysFrom(a, t) : 0;
        if (early < 0) return;
        var score = early * 1000 + (a && st0 ? Math.max(0, daysFrom(st0, a)) : 0);
        if (score > bestScore) { bestScore = score; best = p; }
      });
      if (best) { botm = best; botmMonth = mk; }
      return !!best;
    });
    // Baru masuk: dibuat 7 hari terakhir, paling baru dulu
    var freshP = projects.filter(function (p) {
      var c = parseDay(p.created_date), ago = c ? daysFrom(c, T0) : -1;
      return ago >= 0 && ago < 7;
    }).sort(function (a, b) { return String(b.created_date).localeCompare(String(a.created_date)); });
    var botmE = botm ? cornerEntry(botm) : null, freshE = freshP.slice(0, 2).map(cornerEntry);
    // Cuaca di jendela ikut data: cerah / hujan (1-2 overdue) / badai (3+)
    var wNow = st.overdue === 0 ? 'clear' : st.overdue <= 2 ? 'rain' : 'storm', wChanged = wNow !== weather;
    weather = wNow;
    var todayYmd = T0.getFullYear() + '-' + pad2(T0.getMonth() + 1) + '-' + pad2(T0.getDate());
    var atlas = entries.length + extras.length ? buildAtlas(entries.concat(extras)) : null;

    var step = (CW + GAP) / ARC_R;
    lanes.forEach(function (lane, ci) {
      var a = (ci - (n - 1) / 2) * step;
      var group = new THREE.Group();
      group.position.set(ARC_R * Math.sin(a), 0, ARC_R * (1 - Math.cos(a)));
      group.rotation.y = -a;
      lib.add(group);
      var c = { group: group, angle: a, theta: -a, lane: lane, count: counts[lane.name] || 0, hover: 0, hoverNow: 0, target: new THREE.Vector3(), fit: 10, corners: [] };

      // Rangka kayu (satu geometri) + papan belakang yang lebih gelap
      var parts = [
        { w: CW + 0.04, h: PLINTH, d: CD + 0.04, x: 0, y: PLINTH / 2, z: 0.02 },
        { w: SIDE, h: Hc, d: CD, x: -(CW - SIDE) / 2, y: Hc / 2, z: 0, v: 1 },
        { w: SIDE, h: Hc, d: CD, x: (CW - SIDE) / 2, y: Hc / 2, z: 0, v: 1 },
        { w: 0.17, h: Hc - PLINTH, d: 0.035, x: -(CW / 2 - 0.085), y: PLINTH + (Hc - PLINTH) / 2, z: CD / 2 + 0.017, v: 1 },
        { w: 0.17, h: Hc - PLINTH, d: 0.035, x: CW / 2 - 0.085, y: PLINTH + (Hc - PLINTH) / 2, z: CD / 2 + 0.017, v: 1 },
        { w: CW, h: CROWN - 0.12, d: CD, x: 0, y: Hc + (CROWN - 0.12) / 2, z: 0 },
        { w: CW + 0.12, h: 0.04, d: CD + 0.1, x: 0, y: Hc + CROWN - 0.1, z: 0.03 },
        { w: CW + 0.22, h: 0.08, d: CD + 0.18, x: 0, y: Ht - 0.04, z: 0.05 }
      ];
      for (var j = 0; j <= M; j++) {
        var by = PLINTH + j * SP;
        parts.push({ w: IW, h: BT, d: CD - 0.03, x: 0, y: by + BT / 2, z: 0.015 });
        if (j > 0) parts.push({ w: IW, h: BT + 0.03, d: 0.025, x: 0, y: by + BT / 2 - 0.015, z: CD / 2 + 0.005 });
      }
      var frame = new THREE.Mesh(track(boxesGeometry(parts)), woodMat);
      frame.castShadow = frame.receiveShadow = true;
      frame.userData = { kind: 'case', ci: ci };
      group.add(frame);
      var back = new THREE.Mesh(track(boxesGeometry([{ w: IW, h: Hc - PLINTH, d: 0.03, x: 0, y: PLINTH + (Hc - PLINTH) / 2, z: -CD / 2 + 0.015 }])), backMat);
      back.receiveShadow = true;
      back.userData = { kind: 'case', ci: ci };
      group.add(back);
      pickables.push(frame, back);

      // Kuningan: pegangan tangga di depan mahkota + penyangga buku tiap baris
      var brass = [
        { w: CW + 0.12, h: 0.03, d: 0.03, x: 0, y: Hc - 0.01, z: CD / 2 + 0.14 },
        { w: 0.03, h: 0.03, d: 0.13, x: -(CW / 2 - 0.2), y: Hc - 0.01, z: CD / 2 + 0.075 },
        { w: 0.03, h: 0.03, d: 0.13, x: CW / 2 - 0.2, y: Hc - 0.01, z: CD / 2 + 0.075 }
      ];
      months.forEach(function (m, mi) {
        var end = ends[ci + '|' + mi];
        if (end == null || end > IW / 2 - 0.08) return;
        var by = PLINTH + (M - 1 - mi) * SP + BT;
        brass.push({ w: 0.012, h: 0.28, d: 0.2, x: end + 0.012, y: by + 0.14, z: CD / 2 - 0.16 });
        brass.push({ w: 0.11, h: 0.008, d: 0.2, x: end + 0.06, y: by + 0.004, z: CD / 2 - 0.16 });
      });
      var brassMesh = new THREE.Mesh(track(boxesGeometry(brass)), brassMat);
      brassMesh.castShadow = true;
      group.add(brassMesh);

      // Papan nama modul
      var sub = c.count ? c.count + (c.count === 1 ? ' project' : ' projects') : (d.filtered ? 'no matches' : 'new shelf');
      var pTex = track(plaqueTexture(lane.name, sub, lane.color));
      c.plaqueMat = track(new THREE.MeshStandardMaterial({ map: pTex, emissiveMap: pTex, emissive: 0xffffff, emissiveIntensity: 0.22, metalness: 0.45, roughness: 0.38, envMapIntensity: 1.1 }));
      var plaque = new THREE.Mesh(plaqueGeo, c.plaqueMat);
      plaque.position.set(0, Hc + 0.2, CD / 2 + 0.004);
      plaque.userData = { kind: 'case', ci: ci };
      group.add(plaque);
      pickables.push(plaque);

      // Rak kosong: kartu "rak baru" di baris tengah
      if (!c.count) {
        var cTex = track(cardTexture(d.filtered ? ['NO MATCHES', lane.name, 'for the current filter'] : ['NEW SHELF', lane.name, 'awaiting its first project']));
        cTex.anisotropy = aniso;
        var card = new THREE.Mesh(planeGeo, track(new THREE.MeshStandardMaterial({ map: cTex, roughness: 0.85, emissiveMap: cTex, emissive: 0xffffff, emissiveIntensity: 0.12 })));
        var cr = Math.floor((M - 1) / 2);
        card.scale.set(0.84, 0.525, 1);
        card.position.set(0, PLINTH + cr * SP + BT + 0.27, -CD / 2 + 0.12);
        card.rotation.x = -0.1;
        group.add(card);
      }

      // Bulan ini (emas) & bulan yang difilter (biru): lampu strip + cahaya di papan belakang
      months.forEach(function (m, mi) {
        var isCur = m === d.currentMonth, isHi = !!d.highlightMonth && m === d.highlightMonth;
        if (!isCur && !isHi) return;
        var row = M - 1 - mi, y0 = PLINTH + row * SP + BT, y1 = PLINTH + (row + 1) * SP, k = isCur ? 'cur' : 'hi';
        var strip = new THREE.Mesh(stripGeo, stripMats[k]);
        strip.position.set(0, y1 - 0.008, CD / 2 - 0.05);
        group.add(strip);
        var wash = new THREE.Mesh(washGeo, washMats[k]);
        wash.position.set(0, (y0 + y1) / 2, -CD / 2 + 0.034);
        group.add(wash);
      });

      // Bayangan lembut di lantai (di HP tanpa shadow map, ini satu-satunya)
      var contact = new THREE.Mesh(planeGeo, contactMat);
      contact.rotation.x = -Math.PI / 2;
      contact.scale.set(CW + 1.1, CD + 1.1, 1);
      contact.position.set(0, 0.006, 0.05);
      group.add(contact);

      cases.push(c);
    });

    // Satu buku: sampul (kulit / cetak biru), punggung dari atlas, halaman
    // (abu-abu berdebu kalau lama tidak disentuh).
    function bookMesh(b, col, face) {
      var plan = b.key === 'plan' && !face;
      var spine = track(new THREE.MeshStandardMaterial({
        map: cellTex(atlas.map, atlas, b.cell), emissiveMap: cellTex(atlas.glow, atlas, b.cell), emissive: 0xffffff,
        roughness: b.key === 'live' ? 0.38 : 0.6, metalness: 0.05, transparent: plan, depthWrite: !plan
      }));
      var mats;
      if (plan) {
        var ghost = track(new THREE.MeshStandardMaterial({ color: 0x9cc6ff, emissive: new THREE.Color(col), emissiveIntensity: 0.25, transparent: true, opacity: 0.12, depthWrite: false, roughness: 0.2 }));
        mats = [ghost, spine, ghost];
      } else {
        var side;
        if (face) {
          // dipajang menghadap depan: sampulnya bergambar judul
          var cTex = track(coverTexture(b.p, b.key === 'plan' ? '' : b.color.getStyle(), b.key === 'plan'));
          cTex.anisotropy = aniso;
          side = track(new THREE.MeshStandardMaterial({ map: cTex, emissiveMap: cTex, emissive: 0xffffff, emissiveIntensity: 0.12, roughness: 0.55, metalness: 0 }));
        } else {
          side = track(new THREE.MeshStandardMaterial({ color: b.color.clone().multiplyScalar(0.8), roughness: b.key === 'live' ? 0.4 : 0.72, metalness: 0 }));
        }
        mats = [side, spine, b.stale ? dustyPagesMat : pagesMat];
      }
      var mesh = new THREE.Mesh(bookGeo, mats);
      mesh.scale.set(b.t, b.h, b.d);
      if (plan) mesh.add(new THREE.LineSegments(bookEdges, ghostLineMat));
      return mesh;
    }
    function addBook(b, mesh, g, where, ci, col, delay) {
      var pulse = b.key === 'prog' ? { amp: 0.8, speed: 0.004 } : b.key === 'over' ? { amp: 1.0, speed: 0.006 } : null;
      mesh.userData = {
        kind: 'book', where: where, p: b.p, ci: ci, color: col, g: g, baseX: g.position.x, baseZ: g.position.z, baseY: g.position.y, spine: mesh.material[1],
        key: b.key, cover: b.key === 'plan' ? '' : b.color.getStyle(), out: 0, outNow: 0, stale: b.stale,
        glow: (b.key === 'live' ? 0.8 : b.key === 'done' ? 0.45 : b.key === 'plan' ? 0.9 : 0.4) * (b.stale ? 0.6 : 1),
        amp: pulse ? pulse.amp : 0, speed: pulse ? pulse.speed : 0, phase: b.cell * 0.7,
        delay: delay, lift: 0, liftNow: 0
      };
      books.push(mesh);
      pickables.push(mesh);
    }

    // Buku di rak
    entries.forEach(function (b) {
      var c = cases[b.ci], g = new THREE.Group();
      var baseZ = CD / 2 - b.d / 2 - 0.035;
      g.position.set(b.x, PLINTH + b.row * SP + BT, baseZ);
      var mesh = bookMesh(b, c.lane.color);
      mesh.position.y = b.h / 2;
      g.add(mesh);
      // Pita pembatas = progres: makin panjang, makin dekat selesai
      var prog = clamp(parseInt(b.p.progress, 10) || 0, 0, 100);
      if (!isDone(b.p.status) && prog > 0) {
        var rw = Math.min(0.02, b.t * 0.26), len = 0.035 + (b.h - 0.07) * prog / 100;
        var rib = new THREE.Mesh(planeGeo, ribbonMats[b.key === 'over' ? 'over' : 'prog']);
        rib.scale.set(rw, len, 1);
        rib.position.set(b.t / 2 - rw / 2 - 0.003, b.h - 0.006 - len / 2, b.d / 2 + 0.004);
        g.add(rib);
      }
      // Selesai hari ini: pita emas melintang di punggungnya
      if (isDone(b.p.status) && (String(b.p.actual_date || '').substring(0, 10) === todayYmd || String(b.p.live_date || '').substring(0, 10) === todayYmd)) {
        var sash = new THREE.Mesh(planeGeo, sashMat);
        sash.scale.set(b.t + 0.006, 0.04, 1);
        sash.position.set(0, b.h * 0.64, b.d / 2 + 0.007);
        g.add(sash);
      }
      c.group.add(g);
      addBook(b, mesh, g, 'shelf', b.ci, c.lane.color, (built ? 0.5 : 1) * (b.ci * 110 + b.row * 75 + b.k * 26));
      shelfById[String(b.p.id)] = mesh;
      // Sarang laba-laba di pojok atas buku yang terbengkalai sebulan lebih
      if (b.stale >= WEB_DAYS) {
        var ws = 0.14 + (hashStr('w' + b.p.id) % 6) / 100, flip = b.x + b.t / 2 + ws > IW / 2 - 0.02;
        var web = new THREE.Mesh(planeGeo, cobwebMat);
        web.scale.set(flip ? -ws : ws, ws, 1);
        web.position.set(flip ? b.x - b.t / 2 - ws / 2 : b.x + b.t / 2 + ws / 2, PLINTH + (b.row + 1) * SP - ws / 2, baseZ + b.d / 2 + 0.008);
        c.group.add(web);
      }
    });

    // Tangga perpustakaan bersandar di ujung kanan rak terdekat
    var last = cases[cases.length - 1];
    var lx = CW / 2 - 0.42, lz0 = CD / 2 + 0.95, lz1 = CD / 2 + 0.15, ly1 = Hc - 0.01;
    var L = Math.sqrt((lz0 - lz1) * (lz0 - lz1) + ly1 * ly1), ladderParts = [
      { w: 0.05, h: L, d: 0.035, x: -0.2, y: L / 2, z: 0, v: 1 },
      { w: 0.05, h: L, d: 0.035, x: 0.2, y: L / 2, z: 0, v: 1 }
    ];
    for (var ry = 0.32; ry < L - 0.15; ry += 0.32) ladderParts.push({ w: 0.4, h: 0.028, d: 0.03, x: 0, y: ry, z: 0 });
    var ladder = new THREE.Mesh(track(boxesGeometry(ladderParts)), woodMat);
    ladder.position.set(lx, 0, lz0);
    ladder.rotation.x = -Math.atan2(lz0 - lz1, ly1);
    ladder.castShadow = true;
    last.group.add(ladder);

    // ---- Sudut pustakawan di ujung kiri ----------------------------------
    // Dinding berjendela (langit ikut jam), papan pengumuman, lemari katalog,
    // meja pustakawan dgn tumpukan buku yang perlu perhatian, dan troli buku
    // yang baru dikembalikan.
    var Hw = Math.max(Ht, 3.3), cornerLamp = null;
    (function () {
      var aw = cases[0].angle - (CW / 2 + GAP + WALL_W / 2 + 0.15) / ARC_R;
      var cg = new THREE.Group();
      cg.position.set(ARC_R * Math.sin(aw), 0, ARC_R * (1 - Math.cos(aw)));
      cg.rotation.y = -aw;
      lib.add(cg);
      var att = st.overdue + st.dueSoon;
      // phi: dilihat agak dari atas supaya isi meja & troli kelihatan
      var co = corner = { group: cg, angle: aw, theta: -aw, phi: 1.36, hover: 0, hoverNow: 0, target: new THREE.Vector3(), fit: 10, corners: [], st: st, returns: trolleyP.length, bellT: null, botm: botm, fresh: freshP.length };
      var zf = -CD / 2;   // muka dinding, sejajar papan belakang rak
      function own(m, part) { m.userData = { kind: 'corner', ci: DESK, part: part }; pickables.push(m); return m; }

      // Dinding: plester di atas, lambris kayu di bawah, pilaster & mahkota
      var plaster = new THREE.Mesh(track(boxesGeometry([{ w: WALL_W, h: Hw - 1.05, d: 0.2, x: 0, y: 1.05 + (Hw - 1.05) / 2, z: zf - 0.1 }])), plasterMat);
      plaster.receiveShadow = true;
      cg.add(own(plaster, 'wall'));
      var trim = new THREE.Mesh(track(boxesGeometry([
        { w: WALL_W, h: 1.05, d: 0.06, x: 0, y: 0.525, z: zf + 0.03 },
        { w: WALL_W + 0.04, h: 0.05, d: 0.11, x: 0, y: 1.06, z: zf + 0.05 },
        { w: WALL_W, h: 0.15, d: 0.1, x: 0, y: 0.075, z: zf + 0.05 },
        { w: 0.16, h: Hw, d: 0.32, x: -WALL_W / 2 - 0.02, y: Hw / 2, z: zf, v: 1 },
        { w: 0.16, h: Hw, d: 0.32, x: WALL_W / 2 + 0.02, y: Hw / 2, z: zf, v: 1 },
        { w: WALL_W + 0.3, h: 0.14, d: 0.36, x: 0, y: Hw - 0.07, z: zf + 0.02 },
        { w: WALL_W + 0.42, h: 0.07, d: 0.44, x: 0, y: Hw + 0.035, z: zf + 0.04 }
      ])), woodMat);
      trim.castShadow = trim.receiveShadow = true;
      cg.add(own(trim, 'wall'));

      // Jendela melengkung
      var sill = 1.3, top = Hw - 0.6, ww = Math.min(1.3, (top - sill) * 0.55), wr = ww / 2, hs = Math.max(0.4, top - sill - wr), wx = -0.45, wz = zf + 0.004, fw = 0.09;
      var arch = new THREE.Shape();
      arch.moveTo(-wr, 0); arch.lineTo(wr, 0); arch.lineTo(wr, hs);
      arch.absarc(0, hs, wr, 0, Math.PI, false);
      arch.lineTo(-wr, 0);
      var paneGeo = track(new THREE.ShapeGeometry(arch, 24)), pA = paneGeo.attributes.position, uA = paneGeo.attributes.uv;
      for (var pi = 0; pi < pA.count; pi++) uA.setXY(pi, (pA.getX(pi) + wr) / ww, pA.getY(pi) / (hs + wr));
      var pane = new THREE.Mesh(paneGeo, skyMat);
      pane.position.set(wx, sill, wz);
      pane.userData = { kind: 'window', ci: DESK };
      pickables.push(pane);
      cg.add(pane);
      var rainM = new THREE.Mesh(paneGeo, rainMat);   // tetesan hujan di kaca
      rainM.position.set(wx, sill, wz + 0.006);
      rainM.visible = weather !== 'clear';
      cg.add(rainM);
      var ring = new THREE.Shape();
      ring.moveTo(-wr - fw, 0); ring.lineTo(-wr - fw, hs);
      ring.absarc(0, hs, wr + fw, Math.PI, 0, true);
      ring.lineTo(wr + fw, 0); ring.lineTo(wr, 0); ring.lineTo(wr, hs);
      ring.absarc(0, hs, wr, 0, Math.PI, false);
      ring.lineTo(-wr, 0); ring.lineTo(-wr - fw, 0);
      var wframe = new THREE.Mesh(track(new THREE.ExtrudeGeometry(ring, { depth: 0.07, bevelEnabled: false, curveSegments: 24 })), woodMat);
      wframe.position.set(wx, sill, wz - 0.01);
      wframe.castShadow = true;
      cg.add(wframe);
      var bars = [{ w: 0.045, h: hs + wr, d: 0.05, x: 0, y: (hs + wr) / 2, z: 0.025 }];
      [0.34, 0.67, 1].forEach(function (f) { bars.push({ w: ww, h: 0.04, d: 0.05, x: 0, y: hs * f, z: 0.025 }); });
      bars.push({ w: ww + 0.36, h: 0.06, d: 0.24, x: 0, y: -0.03, z: 0.1 });   // ambang jendela
      var barMesh = new THREE.Mesh(track(boxesGeometry(bars)), woodMat);
      barMesh.position.set(wx, sill, wz);
      cg.add(barMesh);

      // Berkas cahaya dari jendela ke lantai (warna & kuatnya ikut jam)
      var W0 = new THREE.Vector3(wx, sill + (hs + wr) * 0.5, zf + 0.06), F0 = new THREE.Vector3(wx + 0.5, 0.02, 2.55);
      var ay = W0.clone().sub(F0), BL = ay.length();
      ay.normalize();
      var az = new THREE.Vector3(1, 0, 0).cross(ay).normalize(), ax = new THREE.Vector3().crossVectors(ay, az).normalize();
      var mid = W0.clone().add(F0).multiplyScalar(0.5);
      [[ww * 1.05, 0], [ww * 0.6, 0.07]].forEach(function (sb) {
        var beam = new THREE.Mesh(planeGeo, shaftMat);
        beam.matrixAutoUpdate = false;
        beam.matrix.makeBasis(ax, ay, az).scale(new THREE.Vector3(sb[0], BL, 1)).setPosition(mid.clone().addScaledVector(az, sb[1]));
        beam.matrixWorldNeedsUpdate = true;
        cg.add(beam);
      });
      var patch = new THREE.Mesh(planeGeo, patchMat);
      patch.rotation.set(-Math.PI / 2, 0, 0.2);
      patch.scale.set(ww * 1.35, (hs + wr) * 1.05, 1);
      patch.position.set(F0.x, 0.013, F0.z + 0.25);
      cg.add(patch);
      cg.add(shaftDust);
      co.beam = { W: W0, F: F0, X: ax, Z: az, w: ww };

      // Papan pengumuman (gabus) di kanan jendela
      var measured = st.measured, pct = measured ? Math.round(st.onTime / measured * 100) : null;
      var cm = String(d.currentMonth || ''), cmLabel = cm ? MONTHS[+cm.substring(5, 7) - 1] + ' ' + cm.substring(0, 4) : 'this month';
      var notes = {
        date: new Date().toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' }),
        filtered: !!d.filtered,
        greeting: SEASON_GREET[season] || '',
        onTime: pct == null ? '—' : pct + '%', onTimeSub: measured ? st.onTime + ' of ' + measured + ' on schedule' : 'nothing delivered yet',
        overdue: String(st.overdue), overdueSub: st.overdue ? (st.overdue === 1 ? 'book' : 'books') + ' past due' : 'all on schedule',
        done: String(st.doneMonth), doneSub: cmLabel,
        prog: String(st.prog), progSub: st.prog ? 'avg ' + Math.round(st.progSum / st.prog) + '% complete' : 'none right now'
      };
      co.notes = 'On time <b>' + notes.onTime + '</b> &middot; overdue <b>' + st.overdue + '</b> &middot; done this month <b>' + st.doneMonth + '</b> &middot; in progress <b>' + st.prog + '</b>';
      var bwid = Math.min(1.1, WALL_W / 2 - 0.6), bht = bwid * 700 / 640, bx = wx + wr + fw + 0.16 + bwid / 2;
      var by = Math.max(1.2 + bht / 2, sill + (top - sill) / 2);
      co.focusTop = Math.min(Hw, by + bht / 2 + 0.3);   // saat didekati: sampai atas papan saja
      co.boardTop = by + bht / 2;
      co.bx = bx;
      co.wallTop = Hw;
      var boardTex = track(boardTexture(notes));
      boardTex.anisotropy = aniso;
      var board = new THREE.Mesh(planeGeo, track(new THREE.MeshStandardMaterial({ map: boardTex, roughness: 0.9, metalness: 0, emissiveMap: boardTex, emissive: 0xffffff, emissiveIntensity: 0.14 })));
      board.scale.set(bwid, bht, 1);
      board.position.set(bx, by, zf + 0.035);
      cg.add(own(board, 'board'));
      var bframe = new THREE.Mesh(track(boxesGeometry([
        { w: bwid + 0.1, h: 0.05, d: 0.05, x: bx, y: by + bht / 2 + 0.025, z: zf + 0.03 },
        { w: bwid + 0.1, h: 0.05, d: 0.05, x: bx, y: by - bht / 2 - 0.025, z: zf + 0.03 },
        { w: 0.05, h: bht, d: 0.05, x: bx - bwid / 2 - 0.025, y: by, z: zf + 0.03, v: 1 },
        { w: 0.05, h: bht, d: 0.05, x: bx + bwid / 2 + 0.025, y: by, z: zf + 0.03, v: 1 }
      ])), woodMat);
      cg.add(own(bframe, 'board'));

      // Kalender dinding di kiri jendela: tenggat bulan ini dilingkari merah
      var cmk = String(d.currentMonth || '2000-01'), calInfo = calendarInfo(cmk), due = {}, doneD = {}, dueCount = 0;
      projects.forEach(function (p) {
        if (!isDone(p.status) && String(p.target_date || '').substring(0, 7) === cmk) {
          var dn = +String(p.target_date).substring(8, 10);
          (due[dn] = due[dn] || []).push(p);
          dueCount++;
        }
        if (isDone(p.status) && String(p.actual_date || '').substring(0, 7) === cmk) {
          var dn2 = +String(p.actual_date).substring(8, 10);
          (doneD[dn2] = doneD[dn2] || []).push(p);
        }
      });
      var todayD = T0.getFullYear() === calInfo.y && T0.getMonth() + 1 === calInfo.m ? T0.getDate() : 0;
      var calTx = track(calendarTexture(calInfo, due, doneD, todayD));
      calTx.anisotropy = aniso;
      var calW = 0.44, calH = calW * CAL.h / CAL.w, calX = (-WALL_W / 2 + 0.08 + (wx - wr - fw)) / 2, calY = Math.min(Hw - 0.3 - calH / 2, 1.95 + calH / 2);
      var calM = new THREE.Mesh(planeGeo, track(new THREE.MeshStandardMaterial({ map: calTx, emissiveMap: calTx, emissive: 0xffffff, emissiveIntensity: 0.16, roughness: 0.85 })));
      calM.scale.set(calW, calH, 1);
      calM.position.set(calX, calY, zf + 0.012);
      calM.userData = { kind: 'calendar', ci: DESK };
      pickables.push(calM);
      var nail = new THREE.Mesh(track(new THREE.SphereGeometry(0.012, 8, 6)), brassMat);
      nail.position.set(calX, calY + calH / 2 + 0.02, zf + 0.015);
      cg.add(calM, nail);
      calInfo.due = due;
      calInfo.done = doneD;
      calInfo.dueCount = dueCount;
      co.cal = calInfo;

      // Perapian di bawah papan: apinya sebesar jumlah project selesai bulan ini
      var fW = 1.1;
      var fpS = new THREE.Mesh(track(boxesGeometry([
        { w: 0.16, h: 0.98, d: 0.34, x: bx - (fW / 2 - 0.08), y: 0.49, z: zf + 0.17, v: 1 },
        { w: 0.16, h: 0.98, d: 0.34, x: bx + (fW / 2 - 0.08), y: 0.49, z: zf + 0.17, v: 1 },
        { w: fW, h: 0.22, d: 0.34, x: bx, y: 0.87, z: zf + 0.17 },
        { w: fW + 0.24, h: 0.07, d: 0.42, x: bx, y: 1.035, z: zf + 0.21 },
        { w: fW + 0.3, h: 0.05, d: 0.6, x: bx, y: 0.025, z: zf + 0.3 }
      ])), marbleMat);
      fpS.castShadow = fpS.receiveShadow = true;
      var fpIn = new THREE.Mesh(track(boxesGeometry([
        { w: fW - 0.32, h: 0.76, d: 0.04, x: bx, y: 0.38, z: zf + 0.03 },
        { w: 0.03, h: 0.76, d: 0.32, x: bx - (fW / 2 - 0.175), y: 0.38, z: zf + 0.17 },
        { w: 0.03, h: 0.76, d: 0.32, x: bx + (fW / 2 - 0.175), y: 0.38, z: zf + 0.17 },
        { w: fW - 0.32, h: 0.03, d: 0.32, x: bx, y: 0.745, z: zf + 0.17 }
      ])), sootMat);
      [fpS, fpIn].forEach(function (m) { m.userData = { kind: 'fire', ci: DESK }; pickables.push(m); cg.add(m); });
      var logG = track(new THREE.CylinderGeometry(0.045, 0.05, 0.52, 10));
      [[0, 0.1, 0.16, 0.1], [0.02, 0.1, 0.27, -0.12], [-0.01, 0.18, 0.21, 0.05]].forEach(function (l) {
        var lgm = new THREE.Mesh(logG, logMat);
        lgm.rotation.set(0, l[3], Math.PI / 2);
        lgm.position.set(bx + l[0], l[1], zf + l[2]);
        cg.add(lgm);
      });
      var ember = new THREE.Mesh(planeGeo, emberMat);
      ember.rotation.x = -Math.PI / 2;
      ember.scale.set(0.62, 0.3, 1);
      ember.position.set(bx, 0.06, zf + 0.2);
      cg.add(ember);
      var flames = [];
      for (var fi = 0; fi < 6; fi++) {
        var fsp = new THREE.Sprite(fireSprMat);
        fsp.position.set(bx + (fi - 2.5) * 0.075, 0.2, zf + 0.2 + (fi % 2) * 0.04);
        fsp.scale.set(0.12, 0.22, 1);
        fsp.visible = st.doneMonth > 0;
        cg.add(fsp);
        flames.push({ s: fsp, ph: fi * 1.3 });
      }
      co.fire = { flames: flames, n: st.doneMonth, level: clamp(st.doneMonth / 6, 0, 1), light: new THREE.Vector3(bx, 0.5, zf + 0.75) };

      // Book of the month: dipajang di atas rak perapian
      if (botmE) {
        var ly = 1.07, bcol = laneColors[botmE.p.category || 'General'] || '#94a3b8';
        var bmTex = track(brassTexture(768, 80, function (g2, w2, h2) {
          g2.textAlign = 'center';
          g2.textBaseline = 'middle';
          var tx2 = 'BOOK OF THE MONTH \u00b7 ' + MONTHS[+botmMonth.substring(5, 7) - 1].toUpperCase();
          fitFont(g2, tx2, '700', 40, 'Georgia, "Times New Roman", serif', w2 - 60);
          engrave(g2, tx2, w2 / 2, h2 / 2 + 2);
        }));
        bmTex.anisotropy = aniso;
        var bmPl = new THREE.Mesh(planeGeo, track(new THREE.MeshStandardMaterial({ map: bmTex, emissiveMap: bmTex, emissive: 0xffffff, emissiveIntensity: 0.25, metalness: 0.45, roughness: 0.38 })));
        bmPl.scale.set(0.64, 0.05, 1);
        bmPl.position.set(bx, ly - 0.035, zf + 0.422);
        cg.add(bmPl);
        var bmG = new THREE.Group(), bmMesh = bookMesh(botmE, bcol, true);
        bmG.position.set(bx, ly, zf + 0.17);
        bmG.rotation.x = -0.1;
        bmMesh.rotation.y = -Math.PI / 2;   // sampul (sisi +x) menghadap depan
        bmMesh.position.y = botmE.h / 2;
        bmG.add(bmMesh);
        var goldMat = track(new THREE.MeshStandardMaterial({ color: 0xd9a930, emissive: 0x6b4a08, metalness: 0.6, roughness: 0.35, side: THREE.DoubleSide }));
        var ros = new THREE.Mesh(track(new THREE.CircleGeometry(0.045, 20)), goldMat);
        ros.position.set(botmE.d / 2 - 0.075, botmE.h - 0.085, botmE.t / 2 + 0.006);
        var tl1 = new THREE.Mesh(planeGeo, goldMat), tl2 = new THREE.Mesh(planeGeo, goldMat);
        tl1.scale.set(0.022, 0.08, 1);
        tl2.scale.set(0.022, 0.07, 1);
        tl1.position.set(botmE.d / 2 - 0.09, botmE.h - 0.15, botmE.t / 2 + 0.005);
        tl2.position.set(botmE.d / 2 - 0.06, botmE.h - 0.145, botmE.t / 2 + 0.005);
        tl1.rotation.z = 0.25;
        tl2.rotation.z = -0.25;
        bmG.add(ros, tl1, tl2);
        cg.add(bmG);
        addBook(botmE, bmMesh, bmG, 'botm', DESK, bcol, (built ? 0.5 : 1) * 700);
      }

      // Lemari katalog (klik = cari buku)
      var cab = new THREE.Group();
      cab.position.set(-WALL_W / 2 + 0.45, 0, zf + 0.26);
      cg.add(cab);
      var cabBody = new THREE.Mesh(track(boxesGeometry([
        { w: 0.64, h: 1.08, d: 0.46, x: 0, y: 0.58, z: 0, v: 1 },
        { w: 0.7, h: 0.045, d: 0.5, x: 0, y: 1.14, z: 0.01 },
        { w: 0.6, h: 0.06, d: 0.42, x: 0, y: 0.03, z: 0 }
      ])), woodMat);
      cabBody.castShadow = cabBody.receiveShadow = true;
      var cabFront = new THREE.Mesh(planeGeo, catalogMat);
      cabFront.scale.set(0.6, 0.9, 1);
      cabFront.position.set(0, 0.6, 0.232);
      [cabBody, cabFront].forEach(function (m) { m.userData = { kind: 'catalog', ci: DESK }; pickables.push(m); cab.add(m); });

      // Baru masuk (dibuat 7 hari terakhir): dipajang menghadap depan di atas lemari katalog
      freshE.forEach(function (b, k) {
        var col = laneColors[b.p.category || 'General'] || '#94a3b8', mesh = bookMesh(b, col, true), g = new THREE.Group();
        g.position.set(k ? 0.15 : -0.12, 1.1625, k ? -0.03 : -0.11);
        g.rotation.set(-0.14, k ? -0.22 : 0.1, 0);
        mesh.rotation.y = -Math.PI / 2;
        mesh.position.y = b.h / 2;
        g.add(mesh);
        cab.add(g);
        addBook(b, mesh, g, 'new', DESK, col, (built ? 0.5 : 1) * (380 + k * 90));
      });
      if (freshP.length) {
        var nTex = track(cardTexture(['NEW', 'This week', freshP.length + (freshP.length === 1 ? ' book' : ' books')]));
        nTex.anisotropy = aniso;
        var nCard = new THREE.Mesh(planeGeo, track(new THREE.MeshStandardMaterial({ map: nTex, roughness: 0.85, emissiveMap: nTex, emissive: 0xffffff, emissiveIntensity: 0.14 })));
        nCard.scale.set(0.28, 0.175, 1);
        nCard.position.set(0.17, 1.25, 0.17);
        nCard.rotation.x = -0.3;
        cab.add(nCard);
      }

      // Meja pustakawan
      var DW = 1.9, DD = 0.72, DH = 0.78;
      var desk = new THREE.Group();
      desk.position.set(-0.3, 0, 1.15);
      desk.rotation.y = 0.05;
      cg.add(desk);
      var deskWood = new THREE.Mesh(track(boxesGeometry([
        { w: DW, h: 0.05, d: DD, x: 0, y: DH - 0.025, z: 0 },
        { w: 0.5, h: DH - 0.05, d: DD - 0.06, x: -(DW / 2 - 0.27), y: (DH - 0.05) / 2, z: 0, v: 1 },
        { w: 0.5, h: DH - 0.05, d: DD - 0.06, x: DW / 2 - 0.27, y: (DH - 0.05) / 2, z: 0, v: 1 },
        { w: DW - 0.06, h: 0.5, d: 0.035, x: 0, y: DH - 0.33, z: DD / 2 - 0.05 }
      ])), woodMat);
      deskWood.castShadow = deskWood.receiveShadow = true;
      desk.add(own(deskWood, 'desk'));
      desk.add(new THREE.Mesh(track(boxesGeometry([{ w: DW - 0.02, h: 0.03, d: 0.04, x: 0, y: 0.05, z: DD / 2 - 0.02 }])), brassMat));
      var plTex = track(brassTexture(1024, 200, function (g2, w2, h2) {
        g2.textAlign = 'center';
        g2.textBaseline = 'middle';
        fitFont(g2, 'NEEDS ATTENTION', '700', 66, 'Georgia, "Times New Roman", serif', w2 - 110);
        engrave(g2, 'NEEDS ATTENTION', w2 / 2, h2 / 2 - 24);
        var line = att ? st.overdue + ' overdue · ' + st.dueSoon + ' due within 7 days' : 'Nothing overdue or due this week';
        fitFont(g2, line, '600', 36, 'Georgia, "Times New Roman", serif', w2 - 110);
        engrave(g2, line, w2 / 2, h2 / 2 + 44);
      }));
      plTex.anisotropy = aniso;
      co.plaqueMat = track(new THREE.MeshStandardMaterial({ map: plTex, emissiveMap: plTex, emissive: 0xffffff, emissiveIntensity: 0.22, metalness: 0.45, roughness: 0.38, envMapIntensity: 1.1 }));
      var dpl = new THREE.Mesh(planeGeo, co.plaqueMat);
      dpl.scale.set(1.1, 0.215, 1);
      dpl.position.set(0, DH - 0.3, DD / 2 - 0.03);
      desk.add(own(dpl, 'desk'));

      // Lampu baca hijau (banker's lamp) + genangan cahaya di meja
      var lamp = new THREE.Group();
      lamp.position.set(-0.75, DH, -0.2);
      desk.add(lamp);
      cornerLamp = lamp;
      var lbase = new THREE.Mesh(track(new THREE.CylinderGeometry(0.075, 0.09, 0.03, 20)), brassMat);
      lbase.position.y = 0.015;
      var lstem = new THREE.Mesh(track(new THREE.CylinderGeometry(0.012, 0.012, 0.3, 8)), brassMat);
      lstem.position.y = 0.17;
      var lshade = new THREE.Mesh(track(new THREE.CylinderGeometry(0.08, 0.08, 0.36, 20, 1, true, 0, Math.PI)), glassMat);
      lshade.rotation.z = Math.PI / 2;
      lshade.position.set(0, 0.33, 0.05);
      var lbulb = new THREE.Mesh(track(new THREE.BoxGeometry(0.28, 0.012, 0.05)), bulbMat);
      lbulb.position.set(0, 0.312, 0.05);
      var pool = new THREE.Mesh(planeGeo, poolMat);
      pool.rotation.x = -Math.PI / 2;
      pool.scale.set(0.95, 0.75, 1);
      pool.position.set(0.1, 0.004, 0.16);
      lamp.add(lbase, lstem, lshade, lbulb, pool);

      // Lonceng meja (klik = berbunyi kalau suara dinyalakan)
      var bell = new THREE.Group();
      bell.position.set(0.84, DH, 0.24);
      desk.add(bell);
      var bellParts = [
        new THREE.Mesh(track(new THREE.CylinderGeometry(0.06, 0.066, 0.014, 20)), woodMat),
        new THREE.Mesh(track(new THREE.SphereGeometry(0.052, 20, 10, 0, Math.PI * 2, 0, Math.PI / 2)), brassMat),
        new THREE.Mesh(track(new THREE.CylinderGeometry(0.008, 0.008, 0.026, 8)), brassMat)
      ];
      bellParts[0].position.y = 0.007;
      bellParts[1].position.y = 0.014;
      bellParts[2].position.y = 0.075;
      bellParts.forEach(function (m) { m.userData = { kind: 'bell', ci: DESK }; pickables.push(m); bell.add(m); });
      co.bell = bell;

      // Buku catatan kosong + pena bulu & tinta: klik = tulis project baru
      var jr = new THREE.Group();
      jr.position.set(-0.64, DH, 0.13);
      jr.rotation.y = 0.2;
      desk.add(jr);
      var jTex = track(journalTexture());
      jTex.anisotropy = aniso;
      var jCover = new THREE.Mesh(track(new THREE.BoxGeometry(0.36, 0.012, 0.25)), track(new THREE.MeshStandardMaterial({ color: 0x5a2a1a, roughness: 0.6 })));
      jCover.position.y = 0.006;
      var jPage = new THREE.Mesh(planeGeo, track(new THREE.MeshStandardMaterial({ map: jTex, emissiveMap: jTex, emissive: 0xffffff, emissiveIntensity: 0.14, roughness: 0.9 })));
      jPage.rotation.x = -Math.PI / 2;
      jPage.scale.set(0.34, 0.23, 1);
      jPage.position.y = 0.0135;
      var quill = new THREE.Mesh(track(new THREE.ConeGeometry(0.018, 0.2, 10)), track(new THREE.MeshStandardMaterial({ color: 0xf2efe6, roughness: 0.8 })));
      quill.scale.set(1, 1, 0.3);
      quill.rotation.set(0, 0.6, -1.05);
      quill.position.set(0.1, 0.06, -0.02);
      var inkwell = new THREE.Mesh(track(new THREE.CylinderGeometry(0.028, 0.032, 0.045, 14)), track(new THREE.MeshStandardMaterial({ color: 0x14162a, roughness: 0.15, metalness: 0.2 })));
      inkwell.position.set(0.24, 0.0225, -0.09);
      [jCover, jPage, quill, inkwell].forEach(function (m) { m.userData = { kind: 'newbook', ci: DESK }; pickables.push(m); jr.add(m); });

      // Tumpukan buku yang perlu perhatian: overdue dulu, lalu yang paling dekat jatuh tempo
      var stacks = [{ x: -0.13, z: 0.04, y: 0 }, { x: 0.47, z: -0.03, y: 0 }];
      deskE.forEach(function (b, k) {
        var sk = stacks[k % 2], r = rng(hashStr('desk' + b.p.id)), g = new THREE.Group();
        g.position.set(sk.x + (r() - 0.5) * 0.04, DH + sk.y, sk.z + (r() - 0.5) * 0.04);
        g.rotation.y = (r() - 0.5) * 0.16;
        sk.y += b.t + 0.002;
        var col = laneColors[b.p.category || 'General'] || '#94a3b8', mesh = bookMesh(b, col);
        mesh.rotation.z = Math.PI / 2;   // berbaring, punggung menghadap pengunjung
        mesh.position.y = b.t / 2;
        g.add(mesh);
        desk.add(g);
        addBook(b, mesh, g, 'desk', DESK, col, (built ? 0.5 : 1) * (420 + k * 80));
      });

      // (17 Nov 2026) Sosok pustakawan di balik meja DIHAPUS atas permintaan
      // user. Semua kode yang memakainya sudah memeriksa corner.lib dulu, jadi
      // cukup tidak dibuat; perintah Lucca kini hanya ke programmer.

      // Troli buku yang baru dikembalikan (selesai 7 hari terakhir)
      var TW = 1.0, TD = 0.42;
      var tro = new THREE.Group();
      tro.position.set(1.85, 0, 2.05);   // bergeser ke kanan depan: di belakangnya ada perapian
      tro.rotation.y = -0.55;
      cg.add(tro);
      var troWood = new THREE.Mesh(track(boxesGeometry([
        { w: TW, h: 0.035, d: TD, x: 0, y: 0.3, z: 0 },
        { w: TW, h: 0.035, d: TD, x: 0, y: 0.72, z: 0 },
        { w: TW - 0.04, h: 0.04, d: TD - 0.04, x: 0, y: 0.12, z: 0 },
        { w: 0.035, h: 0.64, d: TD, x: -TW / 2, y: 0.46, z: 0, v: 1 },
        { w: 0.035, h: 0.64, d: TD, x: TW / 2, y: 0.46, z: 0, v: 1 },
        { w: TW, h: 0.03, d: 0.025, x: 0, y: 0.75, z: TD / 2 },
        { w: TW, h: 0.06, d: 0.025, x: 0, y: 0.34, z: TD / 2 }
      ])), woodMat);
      troWood.castShadow = troWood.receiveShadow = true;
      tro.add(own(troWood, 'trolley'));
      tro.add(new THREE.Mesh(track(boxesGeometry([
        { w: 0.025, h: 0.5, d: 0.025, x: TW / 2 + 0.03, y: 0.85, z: -TD / 2 + 0.05 },
        { w: 0.025, h: 0.5, d: 0.025, x: TW / 2 + 0.03, y: 0.85, z: TD / 2 - 0.05 },
        { w: 0.03, h: 0.03, d: TD - 0.06, x: TW / 2 + 0.03, y: 1.1, z: 0 }
      ])), brassMat));
      var wheelG = track(new THREE.CylinderGeometry(0.05, 0.05, 0.03, 16));
      [[-1, -1], [-1, 1], [1, -1], [1, 1]].forEach(function (q) {
        var wh = new THREE.Mesh(wheelG, wheelMat);
        wh.rotation.z = Math.PI / 2;
        wh.position.set(q[0] * (TW / 2 - 0.08), 0.05, q[1] * (TD / 2 - 0.06));
        tro.add(wh);
      });
      var nRet = trolleyP.length;
      var signTex = track(cardTexture(['RETURNED', 'This week', nRet ? nRet + (nRet === 1 ? ' book' : ' books') : 'none yet']));
      signTex.anisotropy = aniso;
      var sign = new THREE.Mesh(planeGeo, track(new THREE.MeshStandardMaterial({ map: signTex, roughness: 0.85, emissiveMap: signTex, emissive: 0xffffff, emissiveIntensity: 0.14 })));
      sign.scale.set(0.5, 0.3125, 1);
      sign.position.set(0, 0.52, TD / 2 + 0.02);
      tro.add(own(sign, 'trolley'));
      var tx = -TW / 2 + 0.05;
      trolleyE.forEach(function (b, k) {
        if (tx + b.t > TW / 2 - 0.05) return;
        var g = new THREE.Group(), col = laneColors[b.p.category || 'General'] || '#94a3b8', mesh = bookMesh(b, col);
        g.position.set(tx + b.t / 2, 0.7375, TD / 2 - b.d / 2 - 0.035);
        g.rotation.z = ((hashStr('t' + b.p.id) % 5) - 2) * 0.02;
        tx += b.t + 0.012;
        mesh.position.y = b.h / 2;
        g.add(mesh);
        tro.add(g);
        addBook(b, mesh, g, 'trolley', DESK, col, (built ? 0.5 : 1) * (560 + k * 70));
      });

      // Bayangan lembut di lantai
      co.obs = [[desk, 0, 0, 1.05], [tro, 0, 0, 0.62], [cab, 0, 0, 0.42], [cg, bx, 0.35, 0.7]];   // utk jalan-jalan
      [[desk, DW + 0.5, DD + 0.5], [tro, TW + 0.4, TD + 0.4], [cab, 0.95, 0.75]].forEach(function (sh) {
        var m = new THREE.Mesh(planeGeo, contactMat);
        m.rotation.x = -Math.PI / 2;
        m.scale.set(sh[1], sh[2], 1);
        m.position.y = 0.006;
        sh[0].add(m);
      });
    })();

    // Buku yang sedang dibaca: tetap tersembunyi di posisi "ditarik keluar"
    if (keep) {
      var km = books.filter(function (m) { return String(m.userData.p.id) === keep.id && m.userData.where === keep.where; })[0] ||
        books.filter(function (m) { return String(m.userData.p.id) === keep.id; })[0];
      if (km) {
        taken = { mesh: km, t0: clock - 1000, handed: keep.handed };
        km.userData.out = km.userData.outNow = 1;
        km.userData.delay = -1e6;
        if (keep.handed) km.userData.g.visible = false;
      }
    }

    lib.updateMatrixWorld(true);
    if (corner && corner.obs) corner.obs.forEach(function (ob) { addObs(ob[0], ob[1], ob[2], ob[3]); });

    // Sudut-sudut tiap rak & sudut pustakawan (untuk membingkai kamera)
    cases.forEach(function (c, ci) {
      var xs = [-CW / 2 - 0.12, CW / 2 + 0.12], ys = [0, Ht], zs = [-CD / 2, CD / 2 + 0.2];
      xs.forEach(function (x) { ys.forEach(function (y) { zs.forEach(function (z) { c.corners.push(c.group.localToWorld(new THREE.Vector3(x, y, z))); }); }); });
      if (ci === cases.length - 1) c.corners.push(c.group.localToWorld(new THREE.Vector3(lx + 0.25, 0, lz0)));
      allCorners = allCorners.concat(c.corners);
    });
    [-WALL_W / 2 - 0.25, WALL_W / 2 + 0.7].forEach(function (x) {
      [-CD / 2 - 0.25, 2.4].forEach(function (z) {
        corner.corners.push(corner.group.localToWorld(new THREE.Vector3(x, 0, z)), corner.group.localToWorld(new THREE.Vector3(x, corner.focusTop, z)));
        allCorners.push(corner.group.localToWorld(new THREE.Vector3(x, 0, z)), corner.group.localToWorld(new THREE.Vector3(x, Hw + 0.1, z)));
      });
    });
    var box = new THREE.Box3().setFromPoints(allCorners), center = box.getCenter(new THREE.Vector3()), span = box.max.x - box.min.x;

    // Karpet (lantai & dinding ruangan: buildShell)
    var rug = new THREE.Mesh(planeGeo, rugMat);
    rug.rotation.x = -Math.PI / 2;
    var rugW = clamp(span * 0.4, 3, 6.2);
    rug.scale.set(rugW, rugW * 0.58, 1);
    rug.position.set(center.x + span * 0.14, 0.004, box.max.z + rugW * 0.3 + 0.3);
    rug.receiveShadow = true;
    lib.add(rug);

    // Isi ruangan; buku terbuka di meja baca = project yang sedang dikerjakan
    var progP = projects.filter(function (p) { return p.status === 'On Progress'; }).sort(function (a, b) {
      return String(b.updated_date || b.created_date || '').localeCompare(String(a.updated_date || a.created_date || ''));
    });
    buildRoom({ Ht: Ht, rx: rug.position.x, rz: rug.position.z, rugW: rugW, progP: progP, st: st });

    // Sudut Design Studio di ujung kanan (dinding lanjutannya sudah dibuat buildShell).
    // Titik-titiknya ikut kamera pandangan umum, supaya sudut ini ikut terlihat.
    if (wing) {
      try {
        var wb = wing.build({
          lib: lib, pickables: pickables, track: track, addObs: addObs,
          R: ARC_R, aStart: cases[cases.length - 1].angle + (CW / 2 + GAP) / ARC_R, wallR: ARC_R + CD / 2 + 0.07, Ht: Ht,
          mats: { wood: woodMat, brass: brassMat, dark: wheelMat },
          focusStudio: function () { if (studioStop) setFocus(focus === STUDIO ? -1 : STUDIO); }
        });
        if (wb && wb.corners) {
          allCorners = allCorners.concat(wb.corners);
          // sudut ini jadi titik tuju kamera juga (klik plakatnya / panah kanan paling ujung)
          studioStop = { corners: wb.corners, target: new THREE.Vector3(), theta: wb.theta || 0, phi: FOCUS_PHI, fit: 10, hover: 0 };
        }
      } catch (e) { if (window.console) console.warn('Design Studio corner:', e); }
    }

    // Lampu baca kuningan di atas tiap rak (seperti di perpustakaan lama):
    // kap hijau, bilah cahaya di bawahnya, dan sapuan cahaya hangat di muka
    // rak. Dua lampu sungguhan di depan dua rak terdekat.
    lampLights.forEach(function (l) { l.intensity = 0; l.userData.on = false; });
    cases.forEach(function (c, ci) {
      var shade = new THREE.Mesh(lampShadeGeo, shadeMat);
      shade.position.set(0, Ht + 0.05, CD / 2 + 0.24);
      shade.rotation.x = 0.55;
      var bulb = new THREE.Mesh(lampBulbGeo, bulbMat);
      bulb.position.set(0, Ht + 0.0, CD / 2 + 0.26);
      var arms = new THREE.Mesh(lampArmGeo, brassMat);
      arms.position.set(0, Ht + 0.03, CD / 2 + 0.13);
      var wash = new THREE.Mesh(planeGeo, lightWashMat);
      wash.scale.set(IW + 0.2, Hc - PLINTH + 0.1, 1);
      wash.position.set(0, PLINTH + (Hc - PLINTH) / 2, CD / 2 + 0.03);
      c.group.add(shade, bulb, arms, wash);
      var k = cases.length - 1 - ci;
      if (k < lampLights.length) {
        lampLights[k].position.copy(c.group.localToWorld(new THREE.Vector3(0, Ht - 0.4, CD / 2 + 1.1)));
        lampLights[k].userData.on = true;
        lampLights[k].intensity = 7 * lampMul;
      }
    });
    deskLight.position.copy(cornerLamp.localToWorld(new THREE.Vector3(0, 0.3, 0.12)));
    if (corner && corner.fire) fireLight.position.copy(corner.group.localToWorld(corner.fire.light.clone()));
    deskLight.intensity = 2.6 * lampMul;
    applyLights();
    if (wChanged && todPhase) applyTime(todPhase);   // langit di jendela ikut cuaca baru
    if (season) buildDecor(season, Ht, Hw);

    // Label bulan di sisi kanan rak acuan (rak yang didekati / rak terdekat)
    months.forEach(function (m, mi) {
      var isCur = m === d.currentMonth, isHi = !!d.highlightMonth && m === d.highlightMonth;
      var mo = +m.substring(5, 7), yr = m.substring(0, 4), row = M - 1 - mi;
      var subTxt = isCur ? 'This month' : isHi ? 'Filtered' : yr;
      addLabel('shelf', '<b>' + MONTHS[mo - 1] + '</b><small>' + subTxt + '</small>', new THREE.Vector3(0, PLINTH + row * SP + BT + (SP - BT) / 2, CD / 2 + 0.02), -1, isCur ? 'cur' : isHi ? 'hi' : '');
    });
    // Nama modul di atas tiap rak - hanya di layar sempit, papan namanya terlalu kecil di sana
    cases.forEach(function (c, ci) {
      var subTxt = c.count ? String(c.count) : (d.filtered ? 'none' : 'new');
      addLabel('case', '<b>' + esc(c.lane.name) + '</b><small>' + subTxt + '</small>', new THREE.Vector3(0, Ht + 0.1, CD / 2), ci, c.count ? '' : 'empty');
    });
    var attn = st.overdue + st.dueSoon;
    addLabel('case', '<b>Front desk</b><small>' + (attn ? attn + ' need attention' : 'all calm') + '</small>', new THREE.Vector3(0, Hw + 0.14, 0), DESK, attn ? 'warn' : '');

    dust.scale.set(span + 2, Ht + 1.6, 5);
    dust.position.set(center.x, 0, box.max.z + 1.4);

    var s = Math.max(span, 8) * 0.75;
    var sc = key.shadow.camera;
    sc.left = -s; sc.right = s; sc.top = s; sc.bottom = -s; sc.near = 1; sc.far = 60;
    sc.updateProjectionMatrix();
    key.position.set(center.x + 7, 13, center.z + 11);
    key.target.position.copy(center);

    if (walk) walkCollide(walk.pos);   // batasnya (dinding) diatur buildShell

    var first = !built;
    if (first) luccaGreetAt = clock + (opts.intro && !reduced ? 4800 : 1800);   // sapaan Lucca (sekali sehari)
    if (focus !== DESK && focus !== STUDIO && focus >= cases.length) focus = -1;
    if (focus === STUDIO && !studioStop) focus = -1;
    refit();
    applyFocus(first);
    buildAt = clock + (first && opts.intro && !reduced ? 2300 : first ? 250 : 0);
    built = true;
    perf.since = performance.now();
    perf.n = perf.sum = 0;
    if (first && opts.intro && !reduced) startIntro();
    // Data dimuat ulang di tengah presentasi: daftar perhentiannya ikut diperbarui
    if (tour) {
      tour.steps = tourSteps();
      tour.lifted = [];
      tour.i = Math.min(tour.i, tour.steps.length - 1);
      tourLift(tour.steps[tour.i].lift || []);
    }
  }

  // ---- membingkai kamera ----------------------------------------------
  function placeCam(cam, tgt, r, theta, phi) {
    var sp = Math.sin(phi);
    cam.position.set(tgt.x + r * sp * Math.sin(theta), tgt.y + r * Math.cos(phi), tgt.z + r * sp * Math.cos(theta));
    cam.lookAt(tgt);
    cam.updateMatrixWorld();
  }
  // Jarak terdekat yang masih memuat semua titik di dalam bagian layar `share`.
  function fitDistance(points, tgt, theta, phi, share) {
    var lo = 0.5, hi = 400;
    for (var it = 0; it < 26; it++) {
      var mid = (lo + hi) / 2, ok = true;
      placeCam(fitCam, tgt, mid, theta, phi);
      for (var j = 0; j < points.length && ok; j++) {
        tmpV.copy(points[j]).project(fitCam);
        if (tmpV.z > 1 || tmpV.z < -1 || Math.abs(tmpV.x) > share.w || Math.abs(tmpV.y) > share.h) ok = false;
      }
      if (ok) hi = mid; else lo = mid;
    }
    return hi;
  }
  // Seperti fitDistance, tapi titik tujuannya juga digeser supaya isinya
  // benar-benar di tengah (dilihat serong, rak yang jauh tampak lebih kecil).
  function fitView(points, tgt, theta, phi, share) {
    var dist = fitDistance(points, tgt, theta, phi, share);
    for (var it = 0; it < 3; it++) {
      placeCam(fitCam, tgt, dist, theta, phi);
      var x0 = 9, x1 = -9, y0 = 9, y1 = -9;
      points.forEach(function (p) {
        tmpV.copy(p).project(fitCam);
        x0 = Math.min(x0, tmpV.x); x1 = Math.max(x1, tmpV.x); y0 = Math.min(y0, tmpV.y); y1 = Math.max(y1, tmpV.y);
      });
      var cx = (x0 + x1) / 2, cy = (y0 + y1) / 2;
      if (Math.abs(cx) < 0.01 && Math.abs(cy) < 0.01) break;
      var halfH = dist * Math.tan(THREE.MathUtils.degToRad(fitCam.fov) / 2), halfW = halfH * fitCam.aspect;
      tmpR.setFromMatrixColumn(fitCam.matrixWorld, 0);
      tmpU.setFromMatrixColumn(fitCam.matrixWorld, 1);
      tgt.addScaledVector(tmpR, cx * halfW).addScaledVector(tmpU, cy * halfH);
      dist = fitDistance(points, tgt, theta, phi, share);
    }
    return dist;
  }
  function shareFor(isFocus) {
    if (layout === 'full' || layout === 'walk') return isFocus ? { w: 0.78, h: 0.6 } : { w: 0.9, h: 0.64 };
    if (layout === 'right') return isFocus ? { w: 0.5, h: 0.58 } : { w: 0.56, h: 0.62 };
    return isFocus ? { w: 0.84, h: 0.6 } : { w: 0.94, h: 0.66 };
  }
  function refit() {
    if (!cases.length) return;
    fitCam.fov = camera.fov;
    fitCam.aspect = camera.aspect || 1;
    fitCam.updateProjectionMatrix();
    homeTarget.copy(new THREE.Box3().setFromPoints(allCorners).getCenter(tmpV));
    view.fit = fitView(allCorners, homeTarget, HOME.theta, HOME.phi, shareFor(false));
    cases.concat(corner ? [corner] : [], studioStop ? [studioStop] : []).forEach(function (c) {
      c.target.copy(new THREE.Box3().setFromPoints(c.corners).getCenter(tmpV));
      c.fit = fitView(c.corners, c.target, c.theta, c.phi || FOCUS_PHI, shareFor(true));
    });
  }
  function thetaRange() {
    var c = stopAt(focus);
    return c ? { lo: c.theta - 0.6, hi: c.theta + 0.6 } : { lo: -0.95, hi: 1.1 };
  }
  function applyFocus(snap) {
    var c = stopAt(focus);
    if (c) {
      goal.copy(c.target);
      view.tTheta = c.theta; view.tPhi = c.phi || FOCUS_PHI; view.tRadius = c.fit;
    } else {
      focus = -1;
      goal.copy(homeTarget);
      view.tTheta = HOME.theta; view.tPhi = HOME.phi; view.tRadius = view.fit;
      driftHome = HOME.theta; driftPhase = 0;
    }
    if (snap) {
      target.copy(goal);
      view.theta = view.tTheta; view.phi = view.tPhi; view.radius = view.tRadius;
      if (!reduced) { view.radius = view.tRadius * 1.5; view.phi = 1.2; view.theta = view.tTheta + 0.5; }
    }
    ctrlEl.classList.toggle('focused', focus >= 0);
    labels.forEach(function (l) { l.w = 0; });
  }
  function setFocus(i) {
    focus = i;
    setHover(null);
    clearFound();
    lastInteract = performance.now();
    applyFocus(false);
  }

  function applyOffset() {
    // Desktop: perpustakaannya digeser ke kanan (teks hero di kiri).
    // Layar sempit: sedikit ke atas, bawahnya untuk legenda.
    // Presentasi: layar penuh, sedikit naik (keterangan tur di bawah).
    if (layout === 'walk') camera.clearViewOffset();
    else if (layout === 'full') camera.setViewOffset(size.w, size.h, 0, size.h * 0.09, size.w, size.h);
    else if (layout === 'right') camera.setViewOffset(size.w, size.h, -size.w * 0.19, size.h * 0.05, size.w, size.h);
    else camera.setViewOffset(size.w, size.h, 0, size.h * 0.07, size.w, size.h);
    camera.updateProjectionMatrix();
  }

  function resize(force) {
    var w = host.clientWidth, h = host.clientHeight;
    if (!w || !h) return;
    var lay = walk ? 'walk' : presenting ? 'full' : w >= 992 ? 'right' : 'center';
    if (!force && w === size.w && h === size.h && lay === layout) return;
    size.w = w; size.h = h;
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    layout = lay;
    applyOffset();
    if (built) { refit(); if (!intro) applyFocus(false); }
  }

  // ---- adegan masuk: pintu perpustakaan terbuka -----------------------
  function buildDoorway(Ho, R) {
    var dt = [], g = new THREE.Group();
    function tr(o) { dt.push(o); return o; }
    var Wo = Ho * 0.56, Hs = Ho - Wo / 2, WW = R * 6, WH = R * 2.4, T = 0.5, gap = 0.035;
    var stone = tr(stoneTexture());
    stone.repeat.set(0.26, 0.26);
    stone.anisotropy = aniso;
    var stoneMat = tr(new THREE.MeshStandardMaterial({ map: stone, color: 0x8d96c8, roughness: 0.92, metalness: 0 }));

    // Dinding depan dgn lubang pintu melengkung
    var wall = new THREE.Shape();
    wall.moveTo(-WW / 2, 0);
    wall.lineTo(-Wo / 2, 0);
    wall.lineTo(-Wo / 2, Hs);
    wall.absarc(0, Hs, Wo / 2, Math.PI, 0, true);
    wall.lineTo(Wo / 2, 0);
    wall.lineTo(WW / 2, 0);
    wall.lineTo(WW / 2, WH);
    wall.lineTo(-WW / 2, WH);
    wall.lineTo(-WW / 2, 0);
    var wallMesh = new THREE.Mesh(tr(new THREE.ExtrudeGeometry(wall, { depth: T, bevelEnabled: false, curveSegments: 32 })), stoneMat);
    wallMesh.position.z = -T;
    g.add(wallMesh);
    // Bingkai kuningan tipis di tepi lengkungan
    var trim = new THREE.Shape();
    trim.moveTo(-Wo / 2 - 0.16, 0);
    trim.lineTo(-Wo / 2 - 0.16, Hs);
    trim.absarc(0, Hs, Wo / 2 + 0.16, Math.PI, 0, true);
    trim.lineTo(Wo / 2 + 0.16, 0);
    trim.lineTo(Wo / 2, 0);
    trim.lineTo(Wo / 2, Hs);
    trim.absarc(0, Hs, Wo / 2, 0, Math.PI, false);
    trim.lineTo(-Wo / 2, 0);
    var trimMesh = new THREE.Mesh(tr(new THREE.ExtrudeGeometry(trim, { depth: 0.06, bevelEnabled: false, curveSegments: 32 })), brassMat);
    g.add(trimMesh);

    // Dua daun pintu, engsel di tepi luar
    var leafMat = tr(new THREE.MeshStandardMaterial({ map: woodTex, color: 0x9a6a48, roughness: 0.55, metalness: 0, envMapIntensity: 0.7 }));
    function leaf(sgn) {
      var w = Wo / 2 - gap / 2, s = new THREE.Shape(), R0 = Wo / 2;
      if (sgn > 0) {
        s.moveTo(0, 0); s.lineTo(w, 0); s.lineTo(w, Hs + R0 * 0.999);
        s.absarc(R0, Hs, R0, Math.PI / 2, Math.PI, false);
        s.lineTo(0, 0);
      } else {
        s.moveTo(0, 0); s.lineTo(0, Hs);
        s.absarc(-R0, Hs, R0, 0, Math.PI / 2, false);
        s.lineTo(-w, Hs + R0 * 0.999); s.lineTo(-w, 0); s.lineTo(0, 0);
      }
      var pivot = new THREE.Group();
      pivot.position.set(-sgn * Wo / 2, 0, -0.16);
      var m = new THREE.Mesh(tr(new THREE.ExtrudeGeometry(s, { depth: 0.1, bevelEnabled: false, curveSegments: 24 })), leafMat);
      m.position.z = -0.05;
      pivot.add(m);
      // panel timbul & cincin pegangan
      [[0.12, Hs * 0.12, Hs * 0.38], [0.12, Hs * 0.56, Hs * 0.36]].forEach(function (pp) {
        var pw = w - 0.3, panel = new THREE.Mesh(tr(new THREE.BoxGeometry(pw, pp[2], 0.04)), leafMat);
        panel.position.set(sgn * (0.15 + pw / 2), pp[1] + pp[2] / 2, 0.06);
        pivot.add(panel);
      });
      var ring = new THREE.Mesh(tr(new THREE.TorusGeometry(0.1, 0.018, 8, 24)), brassMat);
      ring.position.set(sgn * (w - 0.2), Hs * 0.5, 0.1);
      pivot.add(ring);
      g.add(pivot);
      return pivot;
    }
    var left = leaf(1), right = leaf(-1);

    // Cahaya hangat dari dalam yang merembes lewat celah pintu
    var glowMat = tr(new THREE.MeshBasicMaterial({ color: new THREE.Color(2.4, 1.6, 0.7), transparent: true, opacity: 1, depthWrite: false }));
    var glowPlane = new THREE.Mesh(planeGeo, glowMat);
    glowPlane.scale.set(Wo, Ho, 1);
    glowPlane.position.set(0, Ho / 2, -0.45);
    g.add(glowPlane);

    // Papan nama di atas pintu
    var signTex = tr(brassTexture(1024, 200, function (sg, w, h) {
      sg.textAlign = 'center'; sg.textBaseline = 'middle';
      fitFont(sg, 'PROJECT LIBRARY', '700', 88, 'Georgia, "Times New Roman", serif', w - 120);
      engrave(sg, 'PROJECT LIBRARY', w / 2, h / 2 - 12);
      sg.font = '600 30px Georgia, "Times New Roman", serif';
      engrave(sg, 'Finance Systems · Delivery Portfolio', w / 2, h / 2 + 52);
    }));
    signTex.anisotropy = aniso;
    var sign = new THREE.Mesh(planeGeo, tr(new THREE.MeshStandardMaterial({ map: signTex, emissiveMap: signTex, emissive: 0xffffff, emissiveIntensity: 0.3, metalness: 0.5, roughness: 0.35 })));
    sign.scale.set(Wo * 1.5, Wo * 1.5 * 200 / 1024, 1);
    sign.position.set(0, Ho + 0.3 + Wo * 1.5 * 100 / 1024, 0.02);
    g.add(sign);

    // Lentera di kiri-kanan pintu
    var lanternMat = tr(new THREE.MeshStandardMaterial({ color: 0x1c1c22, roughness: 0.4, metalness: 0.8 }));
    var flameMat = tr(new THREE.MeshBasicMaterial({ color: new THREE.Color(3, 2.1, 1) }));
    var halo = tr(new THREE.SpriteMaterial({ map: glowTex, color: 0xffb35c, transparent: true, opacity: 0.85, depthWrite: false, blending: THREE.AdditiveBlending }));
    [-1, 1].forEach(function (sgn) {
      var x = sgn * (Wo / 2 + Wo * 0.32), y = Ho * 0.6;
      var cap = new THREE.Mesh(tr(new THREE.BoxGeometry(0.4, 0.07, 0.4)), lanternMat);
      cap.position.set(x, y + 0.24, 0.3);
      var base = new THREE.Mesh(tr(new THREE.BoxGeometry(0.32, 0.06, 0.32)), lanternMat);
      base.position.set(x, y - 0.23, 0.3);
      var glass = new THREE.Mesh(tr(new THREE.BoxGeometry(0.26, 0.4, 0.26)), flameMat);
      glass.position.set(x, y, 0.3);
      var arm = new THREE.Mesh(tr(new THREE.BoxGeometry(0.05, 0.05, 0.3)), lanternMat);
      arm.position.set(x, y + 0.3, 0.15);
      var h = new THREE.Sprite(halo);
      h.scale.set(2.4, 2.4, 1);
      h.position.set(x, y, 0.4);
      g.add(cap, base, glass, arm, h);
    });

    // Anak tangga & lantai batu di depan pintu
    var stepMesh = new THREE.Mesh(tr(new THREE.BoxGeometry(Wo * 1.7, 0.14, 1.1)), stoneMat);
    stepMesh.position.set(0, 0.07, 0.55);
    g.add(stepMesh);
    var ground = new THREE.Mesh(planeGeo, tr(new THREE.MeshStandardMaterial({ map: stone, color: 0x4a5180, roughness: 0.95 })));
    ground.rotation.x = -Math.PI / 2;
    ground.scale.set(WW, R * 2, 1);
    ground.position.set(0, 0, R);
    g.add(ground);

    return { group: g, left: left, right: right, glowMat: glowMat, trash: dt };
  }
  function removeDoor() {
    if (!door) return;
    scene.remove(door.group);
    door.trash.forEach(function (o) { o.dispose(); });
    door = null;
  }
  function startIntro() {
    var Rf = view.fit, Rs = Rf * 2.55, Dd = Rf * 1.4, theta = view.tTheta;
    var Ho = Math.max(Rf * 0.24, (target.y + 0.6) * 1.6);
    door = buildDoorway(Ho, Rf);
    door.group.position.set(target.x + Math.sin(theta) * Dd, 0, target.z + Math.cos(theta) * Dd);
    door.group.rotation.y = theta;
    scene.add(door.group);
    intro = { t0: clock, Rs: Rs, Rf: Rf, Dd: Dd, phi0: Math.PI / 2 - 0.015 };
    view.theta = theta; view.radius = view.tRadius = Rs; view.phi = view.tPhi = intro.phi0;
    host.classList.add('intro');
  }
  function updateIntro() {
    var t = (clock - intro.t0) / 1000;
    if (!intro.creaked && t > 0.45) { intro.creaked = true; sound.play('creak'); }
    var o = easeInOutCubic(clamp((t - 0.55) / 1.3, 0, 1));
    if (door) {
      door.left.rotation.y = 1.75 * o;
      door.right.rotation.y = -1.75 * o;
      door.glowMat.opacity = Math.max(0, 1 - o * 4);
    }
    var c = easeInOutCubic(clamp((t - 0.85) / 2.7, 0, 1));
    view.radius = view.tRadius = intro.Rs + (intro.Rf - intro.Rs) * c;
    view.phi = view.tPhi = intro.phi0 + (HOME.phi - intro.phi0) * easeInOutCubic(clamp((c - 0.5) / 0.5, 0, 1));
    if (door && view.radius < intro.Dd - 1.2) removeDoor();
    if (t > 3.6) endIntro();
  }
  function endIntro() {
    if (!intro) return;
    intro = null;
    removeDoor();
    host.classList.remove('intro');
    perf.since = performance.now();
    var fc = stopAt(focus);
    view.tRadius = fc ? fc.fit : view.fit;
    view.tPhi = fc ? fc.phi || FOCUS_PHI : HOME.phi;
    if (buildAt > clock) buildAt = clock;
  }

  // ---- interaction ----------------------------------------------------
  var raycaster = new THREE.Raycaster(), ndc = new THREE.Vector2();
  function pick(cx, cy) {
    var r = canvas.getBoundingClientRect();
    ndc.x = ((cx - r.left) / r.width) * 2 - 1;
    ndc.y = -((cy - r.top) / r.height) * 2 + 1;
    raycaster.setFromCamera(ndc, camera);
    var hit = raycaster.intersectObjects(pickables, false)[0];
    if (!hit) return null;
    var u = hit.object.userData;
    if (u.kind === 'book') return { book: hit.object };
    if (u.kind === 'wing') return { thing: 'wing', ci: -1, uv: null, wu: u };   // benda sudut Design Studio
    if (THING_KINDS[u.kind]) return { thing: u.kind, ci: u.ci != null ? u.ci : DESK, uv: hit.uv ? hit.uv.clone() : null };
    return { ci: u.ci, part: u.part || '' };
  }

  function tipHead(color, mod, name) {
    return '<div class="c3-tip-mod" style="--c:' + color + ';">' + mod + '</div><div class="c3-tip-name">' + name + '</div>';
  }
  function bookTip(u) {
    var p = u.p, od = +p.is_overdue === 1, st = p.status;
    var when = isDone(st)
      ? (st === 'Live' && p.live_date ? 'Live ' + fmt(p.live_date) : p.actual_date ? 'Delivered ' + fmt(p.actual_date) : '')
      : (p.target_date ? 'Due ' + fmt(p.target_date) : 'No target date');
    var cls = od ? 'over' : st === 'Live' ? 'live' : st === 'Done' ? 'done' : st === 'On Progress' ? 'prog' : 'plan';
    var sub = [];
    if (!isDone(st)) sub.push(clamp(parseInt(p.progress, 10) || 0, 0, 100) + '% done');
    if (p.priority === 'High' || p.priority === 'Low') sub.push(esc(p.priority) + ' priority');
    if (p.pic) sub.push(esc(p.pic));
    var where = { desk: ' &middot; on the desk', trolley: ' &middot; returns trolley', botm: ' &middot; book of the month', 'new': ' &middot; new this week', table: ' &middot; on the reading table' }[u.where] || '';
    return '<div class="c3-tip-mod" style="--c:' + u.color + ';">' + esc(p.category || 'General') + where + '</div>' +
      '<div class="c3-tip-name">' + esc(p.project_name) + '</div>' +
      '<div class="c3-tip-meta"><span class="c3-st st-' + cls + '">' + (od ? 'Overdue' : esc(st)) + '</span>' + esc(when || '') + '</div>' +
      (sub.length ? '<div class="c3-tip-sub">' + sub.join(' &middot; ') + '</div>' : '') +
      (u.stale ? '<div class="c3-tip-dust">Gathering dust &middot; untouched for ' + u.stale + ' days</div>' : '') +
      '<div class="c3-tip-go">Click to open</div>';
  }
  function caseTip(c) {
    return '<div class="c3-tip-mod" style="--c:' + c.lane.color + ';">Bookcase</div>' +
      '<div class="c3-tip-name">' + esc(c.lane.name) + '</div>' +
      '<div class="c3-tip-meta">' + (c.count ? c.count + (c.count === 1 ? ' project' : ' projects') : 'No projects yet') + '</div>' +
      '<div class="c3-tip-go">Click to walk closer</div>';
  }
  function cornerTip(part) {
    var s = corner.st, att = s.overdue + s.dueSoon, go = focus === DESK ? '' : '<div class="c3-tip-go">Click to walk closer</div>';
    if (part === 'board') return tipHead('#e4c47c', 'Notice board', 'Library notices') + '<div class="c3-tip-sub">' + corner.notes + '</div>' + go;
    if (part === 'trolley') {
      return tipHead('#10b981', 'Returns trolley', corner.returns ? corner.returns + (corner.returns === 1 ? ' project' : ' projects') + ' finished in the last 7 days' : 'Nothing finished in the last 7 days') + go;
    }
    return tipHead('#ef4444', 'Front desk', att ? att + (att === 1 ? ' book needs' : ' books need') + ' attention' : 'Nothing needs attention') +
      '<div class="c3-tip-meta">' + s.overdue + ' overdue &middot; ' + s.dueSoon + ' due within 7 days</div>' + go;
  }
  function thingTip(t, hit) {
    if (t === 'wing') return wing && hit && hit.wu ? wing.tip(hit.wu) : '';
    if (t === 'calendar') return calTip(hit && hit.uv);
    if (t === 'window') {
      var ovw = corner ? corner.st.overdue : 0;
      return tipHead('#9cc6ff', 'Window', weather === 'clear' ? 'Clear skies' : weather === 'rain' ? 'Light rain' : 'Thunderstorm') +
        '<div class="c3-tip-sub">The weather follows the projects: ' + (ovw ? ovw + (ovw === 1 ? ' project is' : ' projects are') + ' overdue.' : 'nothing is overdue.') + '</div>';
    }
    if (t === 'fire') {
      var fn = corner && corner.fire ? corner.fire.n : 0;
      return tipHead('#ff8a3c', 'Fireplace', fn ? fn + (fn === 1 ? ' project' : ' projects') + ' delivered this month' : 'Only embers so far this month') +
        '<div class="c3-tip-sub">It burns brighter with every delivery.</div>';
    }
    if (t === 'plant') {
      var hp = room ? room.health : 'thriving', no = room ? room.overdue : 0;
      return tipHead('#4f9a4a', 'Office plant', hp === 'thriving' ? 'Thriving' + (room && room.bloom ? ' and in bloom' : '') : hp === 'thirsty' ? 'A little thirsty' : 'Wilting') +
        '<div class="c3-tip-sub">' + (no ? no + (no === 1 ? ' project is' : ' projects are') + ' overdue \u2014 catch up and it will perk up.' : 'Nothing is overdue' + (room && room.bloom ? ' and 90%+ was delivered on time.' : '.')) + '</div>';
    }
    if (t === 'cat') return tipHead('#d98a3d', 'Library cat', 'Mochi is fast asleep') + '<div class="c3-tip-go">Click to pet</div>';
    if (t === 'lucca') {
      var lu = room && room.lucca, ls = lu ? lu.st : null, lc = '#' + ('00000' + LUCCA_COL[lu ? lu.mood : 'calm'].toString(16)).slice(-6);
      return tipHead(lc, 'Lucca · asisten perpustakaan', !ls ? 'Sedang mengawasi rak' : ls.overdue ? ls.overdue + ' terlambat — sedang ditangani' : ls.dueSoon ? ls.dueSoon + ' jatuh tempo dalam 7 hari' : 'Semua aman') +
        '<div class="c3-tip-sub">Mengawasi semua rak, memindai buku yang perlu perhatian, dan menjaga semuanya tetap sesuai jadwal.</div><div class="c3-tip-go">Klik untuk laporan Lucca</div>';
    }
    if (t === 'catalog') return tipHead('#e4c47c', 'Card catalog', 'Find any book') + '<div class="c3-tip-go">Click to search</div>';
    if (t === 'newbook') return tipHead('#e4c47c', 'Blank book', 'Write a new project') + '<div class="c3-tip-go">Click to start a new project</div>';
    if (t === 'table') {
      var n = room ? room.prog : 0, fp = featured();
      return tipHead('#3b82f6', 'Developer\u2019s desk', n ? n + (n === 1 ? ' project' : ' projects') + ' in progress' : 'Nothing in progress right now') +
        (fp ? '<div class="c3-tip-sub">On screen now: <b>' + esc(fp.project_name) + '</b></div>' : '');
    }
    if (t === 'screen') {
      var sp = featured();
      return sp ? tipHead('#3b82f6', 'Now coding', esc(sp.project_name)) + '<div class="c3-tip-sub">' + clamp(parseInt(sp.progress, 10) || 0, 0, 100) + '% done' +
        (sp.target_date ? ' &middot; due ' + esc(fmt(sp.target_date)) : '') + '</div><div class="c3-tip-go">Click to open the book</div>' : tipHead('#3b82f6', 'Monitors', 'All caught up');
    }
    if (t === 'dev') {
      var dp = featured();
      return tipHead('#3b82f6', 'Developer at work', dp ? 'Busy coding ' + esc(dp.project_name) : 'On a coffee break') + '<div class="c3-tip-go">Click to say hi</div>';
    }
    if (t === 'duck') return tipHead('#ffd23f', 'Rubber duck', 'Explain your code to it and the bug shows up') + '<div class="c3-tip-go">Click to squeak</div>';
    if (t === 'pc') return tipHead('#ff3d7f', 'Workstation', 'RGB makes it faster');
    if (t === 'globe') return tipHead('#e4c47c', 'Globe', 'An antique library globe') + '<div class="c3-tip-go">Click to give it a spin</div>';
    if (t === 'clock') {
      var nw = new Date();
      return tipHead('#e4c47c', 'Library clock', pad2(nw.getHours()) + ':' + pad2(nw.getMinutes()));
    }
    return tipHead('#e4c47c', 'Service bell', 'Ding!') + '<div class="c3-tip-go">' + (sound.isOn() ? 'Click to ring' : 'Turn on library sounds to hear it') + '</div>';
  }

  function setHover(hit, cx, cy) {
    var book = hit && hit.book ? hit.book : null;
    var thing = hit && hit.thing ? hit.thing : null;
    var ci = -1, part = '';
    // Rak yang sedang didekati tidak perlu keterangan lagi; di sudut
    // pustakawan papan pengumuman & troli tetap ada keterangannya.
    if (hit && !book && !thing && (hit.ci !== focus || (hit.ci === DESK && (hit.part === 'board' || hit.part === 'trolley')))) {
      ci = hit.ci;
      part = hit.part || '';
    }
    var k = book ? 'b' + book.id : thing ? 't' + thing + (thing === 'calendar' ? calDay(hit.uv) : thing === 'wing' ? hit.wu.key : '') : ci !== -1 ? 'c' + ci + part : '';
    if (k !== hoverKey) {
      if (wing && wing.hover) wing.hover(thing === 'wing' ? hit.wu : null);
      if (hovered) hovered.userData.lift = 0;
      var oc = stopAt(hoveredCase);
      if (oc) oc.hover = 0;
      hovered = book;
      hoveredCase = ci;
      hoverThing = thing;
      hoverKey = k;
      hoverHit = !k ? null : book ? { book: book } : thing ? { thing: thing, ci: hit.ci, uv: hit.uv, wu: hit.wu } : { ci: ci, part: part };
      if (book) { book.userData.lift = 1; tipEl.innerHTML = bookTip(book.userData); }
      else if (thing) tipEl.innerHTML = thingTip(thing, hit);
      else if (ci !== -1) { stopAt(ci).hover = 1; tipEl.innerHTML = ci === DESK ? cornerTip(part) : caseTip(cases[ci]); }
      tipFor = k ? 'hover' : null;
      tipEl.classList.toggle('show', !!k);
      canvas.style.cursor = book || (thing && !/^(table|clock|pc|plant|window|fire)$/.test(thing)) ? 'pointer' : ci !== -1 && ci !== focus ? 'zoom-in' : '';
    }
    if (k && cx != null) {
      var r = host.getBoundingClientRect();
      var x = cx - r.left + 16, y = cy - r.top + 16;
      var tw = tipEl.offsetWidth, th = tipEl.offsetHeight;
      if (x + tw > size.w - 8) x = cx - r.left - tw - 16;
      if (y + th > size.h - 8) y = cy - r.top - th - 16;
      tipEl.style.transform = 'translate(' + Math.round(Math.max(8, x)) + 'px,' + Math.round(Math.max(8, y)) + 'px)';
    }
  }

  function onDown(e) {
    if (e.pointerType === 'mouse' && e.button !== 0) return;
    if (intro) endIntro();
    clearFound();
    pendingAct = null;
    drag = { id: e.pointerId, x: e.clientX, y: e.clientY, sx: e.clientX, moved: 0, touch: e.pointerType !== 'mouse' };
    if (!drag.touch) { try { canvas.setPointerCapture(e.pointerId); } catch (err) { /* abaikan */ } }
  }
  function onMove(e) {
    if (drag && e.pointerId === drag.id) {
      var dx = e.clientX - drag.x, dy = e.clientY - drag.y;
      drag.x = e.clientX; drag.y = e.clientY;
      drag.moved += Math.abs(dx) + Math.abs(dy);
      if (drag.moved > 5) {
        if (walk) {
          // jalan-jalan: seret = menoleh / mendongak
          walk.yaw -= dx * 0.0045;
          walk.pitch = clamp(walk.pitch - dy * 0.0035, -0.85, 0.6);
          canvas.classList.add('dragging');
          setHover(null);
          return;
        }
        var lim = thetaRange();
        // Di layar sentuh saat mendekati satu rak, geser = pindah rak (lihat
        // onUp); kamera hanya ikut sedikit sebagai tanda.
        view.tTheta = clamp(view.tTheta - dx * (drag.touch && focus !== -1 ? 0.0015 : 0.0055), lim.lo, lim.hi);
        // Di layar sentuh geser vertikal dibiarkan untuk menggulir halaman.
        if (!drag.touch) view.tPhi = clamp(view.tPhi - dy * 0.004, 1.05, 1.56);
        driftHome = clamp(view.tTheta, -0.7, 0.7);
        driftPhase = 0;
        lastInteract = performance.now();
        canvas.classList.add('dragging');
        setHover(null);
      }
      return;
    }
    if (e.pointerType === 'mouse') pendingHover = { x: e.clientX, y: e.clientY };
  }
  function onUp(e) {
    if (!drag || e.pointerId !== drag.id) return;
    var click = drag.moved <= 5, touch = drag.touch, swipe = e.clientX - drag.sx;
    drag = null;
    canvas.classList.remove('dragging');
    if (walk) {
      if (!click) return;
      var wh = !touch && hoverHit ? hoverHit : pick(e.clientX, e.clientY);
      pendingAct = !wh ? null : wh.book ? { open: wh.book } : wh.thing ? { thing: wh.thing, uv: wh.uv, wu: wh.wu } : null;
      return;
    }
    if (!click) {
      // Sentuh, sedang mendekati satu rak: geser = pindah ke tetangganya
      // (urutan dari kiri: sudut pustakawan, lalu rak-rak).
      if (touch && focus !== -1 && Math.abs(swipe) > 50) {
        var order = [DESK].concat(cases.map(function (c, i) { return i; }));
        var at = Math.max(0, order.indexOf(focus));
        setFocus(order[clamp(at + (swipe < 0 ? 1 : -1), 0, order.length - 1)]);
      }
      return;
    }
    // Mouse: pakai persis yang sedang ditunjukkan tooltip.
    var hit = !touch && hoverHit ? hoverHit : pick(e.clientX, e.clientY);
    if (!hit) pendingAct = focus !== -1 ? { focus: -1 } : null;
    else if (hit.book) {
      var ci = hit.book.userData.ci;
      // Sentuh: ketukan pertama mendekat ke raknya dulu - dari jauh bukunya
      // terlalu kecil untuk diketuk tepat.
      pendingAct = touch && focus !== ci ? { focus: ci } : { open: hit.book };
    } else if (hit.thing === 'catalog') pendingAct = { thing: 'catalog' };
    else if (hit.thing) pendingAct = touch && hit.ci === DESK && focus !== DESK ? { focus: DESK } : { thing: hit.thing, uv: hit.uv, wu: hit.wu };
    else pendingAct = hit.ci !== focus ? { focus: hit.ci } : null;
  }
  // Aksi baru dijalankan pada event click, BUKAN pointerup: di layar sentuh
  // browser mengirim click sesudah jari diangkat ke elemen yang saat itu ada
  // di titik tsb - kalau panel detail sudah terbuka, itu latar gelapnya, dan
  // panelnya langsung tertutup lagi.
  function onClick() {
    var a = pendingAct;
    pendingAct = null;
    if (!a) return;
    if (a.open) takeBook(a.open);
    else if (a.thing === 'catalog') openCatalog();
    else if (a.thing === 'bell') ringBell();
    else if (a.thing === 'newbook') { if (opts.onNewBook) opts.onNewBook(); }
    else if (a.thing === 'globe') { if (room) room.globeBoost = 5; sound.play('slide'); }
    else if (a.thing === 'screen') {
      var fp = featured();
      if (fp && opts.onOpenBook) {
        sound.play('slide');
        opts.onOpenBook(fp.id, { rect: meshRect(room.dev.bezel), key: statusKey(fp), cover: coverOf(fp), color: laneColors[fp.category || 'General'] || '' });
      }
    }
    else if (a.thing === 'dev') { if (room && room.dev && room.dev.waveT == null) { room.dev.waveT = clock; sound.play('tick'); } }
    else if (a.thing === 'duck') { if (room && room.dev) room.dev.quackT = clock; sound.play('squeak'); }
    else if (a.thing === 'calendar') {
      var cd = calDay(a.uv), cl = cd && corner && corner.cal.due[cd];
      if (cl && cl.length) findBook(cl[0].id, false);
    }
    else if (a.thing === 'cat') { if (room && room.cat && room.cat.petT == null) { room.cat.petT = clock; sound.play('purr'); } }
    else if (a.thing === 'lucca') luccaBrief();
    else if (a.thing === 'wing') { if (wing) { sound.play('slide'); wing.click(a.wu); } }
    else setFocus(a.focus);
  }

  // Buku diambil: ditarik keluar dari rak, lalu (lihat frame) diserahkan ke
  // pembaca buku di halaman beserta posisinya di layar.
  function takeBook(mesh) {
    setHover(null);
    clearFound();
    if (!opts.onOpenBook) { if (opts.onPick) opts.onPick(mesh.userData.p.id); return; }
    if (taken) return;
    taken = { mesh: mesh, t0: clock, handed: false };
    mesh.userData.out = 1;
    sound.play('slide');
  }
  // Kotak layar (koordinat viewport) yang ditempati buku yang sedang diambil.
  function bookRect() { return taken ? meshRect(taken.mesh) : null; }
  // Kotak layar (koordinat viewport) sebuah mesh berbentuk balok satuan.
  function meshRect(m) {
    var r = canvas.getBoundingClientRect(), x0 = 1e9, x1 = -1e9, y0 = 1e9, y1 = -1e9;
    for (var k = 0; k < 8; k++) {
      tmpV.set(k & 1 ? 0.5 : -0.5, k & 2 ? 0.5 : -0.5, k & 4 ? 0.5 : -0.5).applyMatrix4(m.matrixWorld).project(camera);
      var sx = r.left + (tmpV.x + 1) / 2 * r.width, sy = r.top + (1 - tmpV.y) / 2 * r.height;
      x0 = Math.min(x0, sx); x1 = Math.max(x1, sx); y0 = Math.min(y0, sy); y1 = Math.max(y1, sy);
    }
    return { x: x0, y: y0, w: x1 - x0, h: y1 - y0 };
  }
  function returnBook() {
    if (!taken) return;
    var u = taken.mesh.userData;
    u.g.visible = true;
    u.out = 0;
    taken = null;
    sound.play('slide');
  }

  // Buku dicari (katalog / tautan langsung): kamera berjalan ke raknya, lalu
  // bukunya berdenyut terang & tertarik keluar. open = langsung diambil.
  function findBook(id, open) {
    var m = shelfById[String(id)];
    if (!m) m = books.filter(function (b) { return String(b.userData.p.id) === String(id); })[0];
    if (!m) return false;
    if (intro) endIntro();
    setFocus(m.userData.ci);
    found = { mesh: m, t0: clock, open: !!open };
    m.userData.lift = 1;
    return true;
  }
  function clearFound() {
    if (!found) return;
    var m = found.mesh;
    found = null;
    if (m !== hovered) m.userData.lift = 0;
    if (tipFor === m) { tipFor = null; tipEl.classList.remove('show'); }
  }
  function updateFound() {
    var fu = found.mesh.userData, age = clock - found.t0;
    fu.lift = 1;
    if (found.open) {
      if (age > (reduced ? 60 : 1150)) { var fm = found.mesh; clearFound(); takeBook(fm); }
      return;
    }
    if (!found.keep && age > 10000) { clearFound(); return; }
    if (hoverKey || taken || age < 500) return;
    if (tipFor !== found.mesh) { tipEl.innerHTML = bookTip(fu); tipFor = found.mesh; }
    tmpV.set(0, 0.5, 0.5).applyMatrix4(found.mesh.matrixWorld).project(camera);
    var tw = tipEl.offsetWidth, th = tipEl.offsetHeight;
    var x = (tmpV.x + 1) / 2 * size.w + 14, y = (1 - tmpV.y) / 2 * size.h - th - 12;
    // buku di baris atas: keterangannya di bawah buku (di atasnya ada tombol hero)
    if (y < size.h * 0.16) {
      tmpV.set(0, -0.5, 0.5).applyMatrix4(found.mesh.matrixWorld).project(camera);
      y = (1 - tmpV.y) / 2 * size.h + 12;
    }
    tipEl.style.transform = 'translate(' + Math.round(clamp(x, 8, size.w - tw - 8)) + 'px,' + Math.round(clamp(y, 8, size.h - th - 8)) + 'px)';
    tipEl.classList.add('show');
  }
  function ringBell() {
    if (!corner) return;
    corner.bellT = clock;
    sound.play('bell');
  }

  // ---- lemari katalog -----------------------------------------------------
  function coverOf(p) {
    var r = rng(hashStr(String(p.id) + '|' + p.project_name)), k = statusKey(p);
    bookShape(p, r);
    return k === 'plan' ? '' : leather(laneColors[p.category || 'General'] || '#94a3b8', r, k).getStyle();
  }
  function catScore(p, q) {
    var name = String(p.project_name || '').toLowerCase(), at = name.indexOf(q);
    if (at === 0) return 3;
    if (at > 0) return 2;
    return String(p.category || '').toLowerCase().indexOf(q) >= 0 || String(p.pic || '').toLowerCase().indexOf(q) >= 0 ? 1 : 0;
  }
  function catWhere(p) {
    var mk = monthOf(p), mo = mk ? MONTHS[+mk.substring(5, 7) - 1] + ' ' + mk.substring(0, 4) : 'no date';
    return esc(p.category || 'General') + ' &middot; ' + (mk ? mo + ' shelf' : 'no date');
  }
  function renderCatalog() {
    var q = catIn.value.trim().toLowerCase(), list, pics = {};
    lastProjects.forEach(function (p) { if (p.pic) pics[p.pic] = (pics[p.pic] || 0) + 1; });
    var picNames = Object.keys(pics).sort(function (a, b) { return pics[b] - pics[a] || a.localeCompare(b); });
    if (q) {
      list = lastProjects.map(function (p) { return { p: p, s: catScore(p, q) }; })
        .filter(function (x) { return x.s; })
        .sort(function (a, b) { return b.s - a.s || String(a.p.project_name).localeCompare(String(b.p.project_name)); })
        .slice(0, 8).map(function (x) { return x.p; });
      // nama PIC yang cocok = baris "sorot" di paling atas
      catRes = picNames.filter(function (nm) { return nm.toLowerCase().indexOf(q) >= 0; }).slice(0, 2)
        .map(function (nm) { return { pic: nm, n: pics[nm] }; })
        .concat(list.map(function (p) { return { p: p }; }));
    } else {
      list = lastProjects.slice().sort(function (a, b) {
        return String(b.updated_date || b.created_date || '').localeCompare(String(a.updated_date || a.created_date || ''));
      }).slice(0, 6);
      catRes = list.map(function (p) { return { p: p }; });
    }
    catSel = Math.min(catSel, Math.max(0, catRes.length - 1));
    var html = q || !catRes.length ? '' : '<div class="c3-cat-cap">Recently updated</div>';
    html += catRes.map(function (it, i) {
      var cls = 'c3-cat-it' + (it.pic ? ' c3-cat-pic' : '') + (i === catSel ? ' on' : ''), attrs = ' data-i="' + i + '" role="option" aria-selected="' + (i === catSel) + '"';
      if (it.pic) {
        return '<button type="button" class="' + cls + '"' + attrs + '><span class="c3-cat-ico">' + ICONS.spot + '</span>' +
          '<span class="c3-cat-t"><b>Spotlight ' + esc(it.pic) + '</b><small>Light up all ' + it.n + (it.n === 1 ? ' book' : ' books') + ' requested by ' + esc(it.pic) + '</small></span></button>';
      }
      return '<button type="button" class="' + cls + '"' + attrs + '><span class="c3-cat-dot" style="--k:' + ST_COLOR[statusKey(it.p)] + ';"></span>' +
        '<span class="c3-cat-t"><b>' + esc(it.p.project_name) + '</b><small>' + catWhere(it.p) + '</small></span></button>';
    }).join('');
    if (!catRes.length) html += '<div class="c3-cat-empty">' + (q ? 'No book matches &ldquo;' + esc(catIn.value.trim()) + '&rdquo;.' : 'The library is empty.') + '</div>';
    if (!q && picNames.length) {
      html += '<div class="c3-cat-cap">Spotlight a person</div><div class="c3-cat-chips">' + picNames.slice(0, 6).map(function (nm) {
        return '<button type="button" class="c3-cat-chip' + (nm === spotPic ? ' on' : '') + '" data-pic="' + esc(nm) + '">' + esc(nm) + ' <small>' + pics[nm] + '</small></button>';
      }).join('') + '</div>';
    }
    catList.innerHTML = html;
    catEl.querySelector('.c3-cat-foot').textContent = lastProjects.length + (lastProjects.length === 1 ? ' book' : ' books') + ' in the catalog \u00b7 \u2191\u2193 to choose \u00b7 Enter to find';
  }
  function openCatalog() {
    if (intro) endIntro();
    catEl.hidden = false;
    catBtn.setAttribute('aria-expanded', 'true');
    catIn.value = '';
    catSel = 0;
    renderCatalog();
    try { catIn.focus({ preventScroll: true }); } catch (e) { catIn.focus(); }
    sound.play('slide');
  }
  function closeCatalog(refocus) {
    if (catEl.hidden) return;
    catEl.hidden = true;
    catBtn.setAttribute('aria-expanded', 'false');
    if (refocus) catBtn.focus();
  }
  function catPick(i) {
    var it = catRes[i];
    if (!it) return;
    closeCatalog(false);
    if (it.pic) { setSpot(it.pic); return; }
    var p = it.p;
    if (findBook(p.id, false)) return;
    // Bukunya di bulan yang sedang tidak tampil: geser waktu ke sana dulu
    var idx = monthList.indexOf(monthOf(p));
    if (idx >= 0 && lastData && monthList.length > MAX_MONTHS) {
      var L = monthList.length, end = Math.max(MAX_MONTHS, Math.min(L, idx + 1 + Math.floor((MAX_MONTHS - 1) / 2)));
      monthShift = L - end;
      rebuild();
      if (findBook(p.id, false)) return;
    }
    // Tidak ada di rak sama sekali (mis. tanpa tanggal): langsung dibuka
    if (opts.onOpenBook) opts.onOpenBook(p.id, { rect: null, key: statusKey(p), cover: coverOf(p), color: '' });
  }
  catIn.addEventListener('input', function () { catSel = 0; renderCatalog(); });
  catIn.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      e.preventDefault();
      if (catRes.length) { catSel = (catSel + (e.key === 'ArrowDown' ? 1 : -1) + catRes.length) % catRes.length; renderCatalog(); }
    } else if (e.key === 'Enter') { e.preventDefault(); catPick(catSel); }
    else if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); closeCatalog(true); }
  });
  catList.addEventListener('click', function (e) {
    var c = e.target.closest('[data-pic]');
    if (c) {
      var nm = c.getAttribute('data-pic');
      closeCatalog(false);
      setSpot(spotPic === nm ? '' : nm);
      return;
    }
    var b = e.target.closest('.c3-cat-it');
    if (b) catPick(+b.getAttribute('data-i'));
  });
  catEl.querySelector('.c3-cat-x').addEventListener('click', function () { closeCatalog(true); });
  function onDocDown(e) {
    if (!catEl.hidden && !catEl.contains(e.target) && !catBtn.contains(e.target)) closeCatalog(false);
    if (!menuEl.hidden && !menuEl.parentNode.contains(e.target)) closeMenu(false);
  }
  document.addEventListener('pointerdown', onDocDown, true);
  function onCancel() { drag = null; canvas.classList.remove('dragging'); }
  function onLeave(e) { if (e.pointerType === 'mouse' && !drag) { pendingHover = null; setHover(null); } }
  // Roda mouse biasa tetap untuk menggulir halaman; zoom hanya dgn Ctrl
  // (cubit di touchpad juga dikirim sebagai Ctrl + roda).
  function onWheel(e) {
    if (!e.ctrlKey) return;
    e.preventDefault();
    if (intro) endIntro();
    var fc = stopAt(focus), base = fc ? fc.fit : view.fit;
    view.tRadius = clamp(view.tRadius * (1 + e.deltaY * 0.0015), base * 0.35, base * 1.5);
    lastInteract = performance.now();
  }
  canvas.addEventListener('pointerdown', onDown);
  canvas.addEventListener('pointermove', onMove);
  canvas.addEventListener('pointerup', onUp);
  canvas.addEventListener('pointercancel', onCancel);
  canvas.addEventListener('pointerleave', onLeave);
  canvas.addEventListener('click', onClick);
  canvas.addEventListener('wheel', onWheel, { passive: false });

  var spinBtn = ctrlEl.querySelector('[data-act="spin"]');
  function syncSpin() {
    spinBtn.setAttribute('aria-checked', spin ? 'true' : 'false');
  }
  syncSpin();
  var soundBtn = ctrlEl.querySelector('[data-act="sound"]'), catBtn = ctrlEl.querySelector('[data-act="catalog"]');
  function syncSound() {
    var on = sound.isOn();
    soundBtn.querySelector('.c3-mi').innerHTML = on ? ICONS.soundOn : ICONS.soundOff;
    soundBtn.setAttribute('aria-checked', on ? 'true' : 'false');
  }
  syncSound();
  function resetView() {
    if (intro) endIntro();
    focus = -1;
    setHover(null);
    clearFound();
    applyFocus(false);
  }
  ctrlEl.addEventListener('click', function (e) {
    var b = e.target.closest('button');
    if (!b) return;
    var act = b.getAttribute('data-act');
    if (act !== 'more') closeMenu(false);
    if (act === 'more') { if (menuEl.hidden) openMenu(); else closeMenu(true); }
    else if (act === 'spin') { spin = !spin; syncSpin(); lastInteract = 0; }
    else if (act === 'catalog') { if (catEl.hidden) openCatalog(); else closeCatalog(true); }
    else if (act === 'sound') { sound.setOn(!sound.isOn()); syncSound(); sound.play('flip'); }
    else if (act === 'snap') savePicture();
    else if (act === 'present') startTour();
    else if (act === 'walk') startWalk();
    else if (act === 'older' || act === 'newer') shiftMonths(act === 'older' ? 1 : -1);
    else if (act === 'spot-x') setSpot('');
    else resetView();
  });

  // ---- pengumuman untuk pembaca layar -------------------------------------
  function announce(text) { srEl.textContent = text; }
  function announceStop(i) {
    var c = stopAt(i);
    if (!c) { announce('All bookcases.'); return; }
    if (i === STUDIO) { announce('Design Studio. Mockups on the easels, ERDs as blueprint rolls and on the table.'); return; }
    if (i === DESK) {
      var s = c.st, att = s.overdue + s.dueSoon;
      announce('Front desk. ' + (att ? att + (att === 1 ? ' book needs' : ' books need') + ' attention.' : 'Nothing needs attention.') + ' Press down to browse the books here.');
      return;
    }
    announce('Bookcase ' + c.lane.name + ', ' + c.count + (c.count === 1 ? ' project' : ' projects') + '. Press down to browse the books.');
  }

  // ---- menu "lainnya" (suara, gerak kamera, simpan gambar, atur ulang) -----
  function openMenu() {
    menuEl.hidden = false;
    moreBtn.setAttribute('aria-expanded', 'true');
    var f = menuEl.querySelector('button');
    if (f) f.focus();
  }
  function closeMenu(refocus) {
    if (menuEl.hidden) return;
    menuEl.hidden = true;
    moreBtn.setAttribute('aria-expanded', 'false');
    if (refocus) moreBtn.focus();
  }
  menuEl.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { e.preventDefault(); e.stopPropagation(); closeMenu(true); }
    else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      e.preventDefault();
      var bs = [].slice.call(menuEl.querySelectorAll('button')), at = bs.indexOf(document.activeElement);
      bs[(at + (e.key === 'ArrowDown' ? 1 : -1) + bs.length) % bs.length].focus();
    }
  });

  // ---- kuat cahaya ruangan: ikut jam, meredup saat sorot PIC menyala -----
  function applyLights() {
    var P = TOD[todPhase] || TOD.afternoon, dim = spotPic ? 0.32 : 1;
    hemi.intensity = P.hemi * dim;
    key.intensity = P.keyI * dim;
    fill.intensity = 0.6 * dim;
    lampLights.forEach(function (l) { l.intensity = l.userData.on ? 7 * lampMul * dim : 0; });
    deskLight.intensity = corner ? 2.6 * lampMul * dim : 0;
    chandLight.intensity = room ? 5 * lampMul * dim : 0;
    screenLight.intensity = room && room.dev ? 1.6 * (spotPic ? 0.6 : 1) : 0;
    fireBase = corner && corner.fire ? (corner.fire.n ? 0.9 + 2.6 * corner.fire.level : 0.35) * (0.75 + 0.25 * lampMul) * dim : 0;
    fireLight.intensity = fireBase;
    bulbMat.color.setRGB(3 * lampMul, 2.4 * lampMul, 1.5 * lampMul);
    lightWashMat.opacity = 0.16 * lampMul * dim;
  }

  // ---- sorot satu PIC: lampu meredup, buku miliknya menyala & maju -------
  function setSpot(pic) {
    spotPic = pic || '';
    spotEl.hidden = !spotPic;
    spotEl.querySelector('b').textContent = spotPic;
    applyLights();
    if (spotPic) {
      var n = lastProjects.filter(function (p) { return String(p.pic || '') === spotPic; }).length;
      announce('Spotlight on ' + spotPic + ': ' + n + (n === 1 ? ' book' : ' books') + ' lit up.');
    } else announce('Spotlight off.');
  }

  // ---- geser waktu: baris rak mundur / maju sebulan ------------------------
  function rebuild() { if (lastData) build(lastData); }
  function shiftMonths(dir) {
    monthShift = Math.max(0, monthShift + dir);
    rebuild();
    announce('Showing ' + timeEl.querySelector('.c3-time-l').textContent + '.');
    sound.play('slide');
  }

  // ---- simpan gambar perpustakaan (PNG) -------------------------------------
  // Kanvas WebGL-nya transparan, jadi digambar di atas latar seperti hero,
  // ditambah label bulan/rak yang sedang tampil & keterangan tanggal.
  function savePicture() {
    renderer.render(scene, camera);
    var W = canvas.width, H = canvas.height, pr = W / (size.w || 1), out = document.createElement('canvas');
    out.width = W;
    out.height = H;
    var g = out.getContext('2d');
    var bg = g.createLinearGradient(0, 0, W, H * 0.45);
    bg.addColorStop(0, '#070b22');
    bg.addColorStop(0.4, '#10185a');
    bg.addColorStop(0.72, '#172e84');
    bg.addColorStop(1, '#1b56c2');
    g.fillStyle = bg;
    g.fillRect(0, 0, W, H);
    g.drawImage(canvas, 0, 0);
    g.textBaseline = 'middle';
    labels.forEach(function (l) {
      if (l.el.style.visibility === 'hidden' || getComputedStyle(l.el).display === 'none') return;
      var m = /translate\(([-\d.]+)px,\s*([-\d.]+)px\)/.exec(l.el.style.transform);
      if (!m) return;
      var b = l.el.querySelector('b'), sm = l.el.querySelector('small'), x = +m[1] * pr, y = +m[2] * pr;
      var cur = /\bcur\b/.test(l.el.className), col = cur ? '#f0c65f' : '#ffffff';
      if (l.kind === 'shelf') {
        var left = labelsEl.classList.contains('left');
        g.textAlign = left ? 'right' : 'left';
        x += (left ? -16 : 16) * pr;
        g.font = '600 ' + Math.round(11 * pr) + 'px Arial, sans-serif';
        g.fillStyle = col;
        g.fillText(b ? b.textContent.toUpperCase() : '', x, y - 6 * pr);
        g.font = Math.round(9.5 * pr) + 'px Arial, sans-serif';
        g.fillStyle = cur ? col : 'rgba(255,255,255,.55)';
        g.fillText(sm ? sm.textContent : '', x, y + 7 * pr);
      } else {
        g.textAlign = 'center';
        g.font = '600 ' + Math.round(10.5 * pr) + 'px Arial, sans-serif';
        g.fillStyle = '#ffffff';
        g.fillText(b ? b.textContent : '', x, y - 20 * pr);
        g.font = Math.round(9.5 * pr) + 'px Arial, sans-serif';
        g.fillStyle = 'rgba(255,255,255,.6)';
        g.fillText(sm ? sm.textContent : '', x, y - 7 * pr);
      }
    });
    var today = new Date();
    g.textAlign = 'left';
    g.font = '600 ' + Math.round(13 * pr) + 'px Arial, sans-serif';
    g.fillStyle = 'rgba(255,255,255,.78)';
    g.fillText('Project Library · ' + today.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }), 18 * pr, H - 20 * pr);
    var name = 'project-library-' + today.getFullYear() + '-' + pad2(today.getMonth() + 1) + '-' + pad2(today.getDate()) + '.png';
    function save(url) {
      var a = document.createElement('a');
      a.href = url;
      a.download = name;
      document.body.appendChild(a);
      a.click();
      a.parentNode.removeChild(a);
    }
    if (out.toBlob) {
      out.toBlob(function (blob) {
        if (!blob) return;
        var u = URL.createObjectURL(blob);
        save(u);
        setTimeout(function () { URL.revokeObjectURL(u); }, 5000);
      }, 'image/png');
    } else save(out.toDataURL('image/png'));
    host.classList.remove('flash');
    void host.offsetWidth;
    host.classList.add('flash');
    setTimeout(function () { host.classList.remove('flash'); }, 500);
    sound.play('shutter');
    announce('Picture saved as ' + name + '.');
  }

  // ---- mode presentasi: tur otomatis, layar penuh ---------------------------
  // Kamera berkeliling sendiri: pandangan umum, meja pustakawan, lalu tiap rak
  // (kiri ke kanan) dgn keterangan singkat; buku yang dibahas ditarik maju.
  function tourSteps() {
    var steps = [], cm = lastData ? lastData.currentMonth : '';
    var today = new Date().toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    var onShelf = books.filter(function (m) { return m.userData.where === 'shelf'; }).length;
    steps.push({ stop: -1, title: 'Project Library', lines: [lastProjects.length + ' projects · ' + onShelf + ' books on ' + cases.length + (cases.length === 1 ? ' bookcase' : ' bookcases') + ' (' + rangeLabel(months0(), months1()) + ')', today], lift: [] });
    if (corner) {
      var s = corner.st, att = s.overdue + s.dueSoon, pct = s.measured ? Math.round(s.onTime / s.measured * 100) + '%' : '—';
      var deskBooks = books.filter(function (m) { return m.userData.where === 'desk'; });
      var lines = [att ? att + (att === 1 ? ' book needs' : ' books need') + ' attention: ' + s.overdue + ' overdue, ' + s.dueSoon + ' due within 7 days' : 'Nothing is overdue or due this week',
        'On time ' + pct + ' · done this month ' + s.doneMonth + (corner.returns ? ' · ' + corner.returns + ' finished in the last 7 days' : '')];
      if (corner.botm) lines.push('Book of the month: ' + corner.botm.project_name);
      if (corner.cal) lines.push(corner.cal.dueCount + (corner.cal.dueCount === 1 ? ' deadline' : ' deadlines') + ' this month on the wall calendar');
      steps.push({ stop: DESK, title: 'Front desk', lines: lines, lift: deskBooks.concat(books.filter(function (m) { return m.userData.where === 'botm'; })) });
    }
    cases.forEach(function (c, ci) {
      if (!c.count) return;
      var mine = lastProjects.filter(function (p) { return (p.category || 'General') === c.lane.name; });
      var doneM = 0, prog = 0, over = [], plan = 0;
      mine.forEach(function (p) {
        if (isDone(p.status)) { if (monthOf(p) === cm) doneM++; }
        else if (+p.is_overdue === 1) over.push(p.project_name);
        else if (p.status === 'On Progress') prog++;
        else plan++;
      });
      var lift = books.filter(function (m) {
        var u = m.userData, p = u.p;
        return u.ci === ci && u.where === 'shelf' && (+p.is_overdue === 1 || p.status === 'On Progress' || (isDone(p.status) && monthOf(p) === cm));
      });
      steps.push({
        stop: ci, title: c.lane.name,
        lines: [c.count + (c.count === 1 ? ' project' : ' projects') + ' · ' + doneM + ' done this month · ' + prog + ' in progress' + (plan ? ' · ' + plan + ' planned' : ''),
          over.length ? over.length + ' overdue: ' + over.slice(0, 2).join(', ') + (over.length > 2 ? ' and ' + (over.length - 2) + ' more' : '') : 'Nothing overdue on this bookcase'],
        lift: lift
      });
    });
    steps.push({ stop: -1, title: 'That’s the library', lines: ['Click any book to read it', 'Esc or Exit leaves the presentation'], lift: [] });
    return steps;
  }
  function months0() { return monthWin[0] || ''; }
  function months1() { return monthWin[monthWin.length - 1] || ''; }
  function tourLift(list) {
    tour.lifted.forEach(function (m) { if (m !== hovered) m.userData.lift = 0; });
    tour.lifted = list;
    list.forEach(function (m) { m.userData.lift = 0.7; });
  }
  function syncTourBtns() {
    var pb = tourEl.querySelector('[data-tour="pause"]'), paused = !!(tour && tour.paused);
    pb.innerHTML = paused ? ICONS.play : ICONS.pause;
    pb.setAttribute('aria-label', paused ? 'Resume the tour' : 'Pause the tour');
    pb.title = paused ? 'Resume' : 'Pause';
    tourEl.querySelector('[data-tour="prev"]').disabled = !tour || tour.i <= 0;
  }
  function tourGo(dir) {
    if (!tour) return;
    var n = tour.steps.length, i = Math.max(0, tour.i + dir);
    if (i >= n) { stopTour(); return; }
    tour.i = i;
    tour.at = performance.now();
    var st = tour.steps[i];
    setFocus(st.stop);
    tourLift(st.lift || []);
    tourEl.querySelector('.c3-tour-k').textContent = 'Stop ' + (i + 1) + ' of ' + n;
    tourEl.querySelector('.c3-tour-t').textContent = st.title;
    tourEl.querySelector('.c3-tour-l').innerHTML = st.lines.map(function (l) { return '<div>' + esc(l) + '</div>'; }).join('');
    syncTourBtns();
    announce(st.title + '. ' + st.lines.join('. ') + '.');
    if (dir) sound.play('flip');
  }
  function tourPause() {
    if (!tour) return;
    tour.paused = !tour.paused;
    syncTourBtns();
  }
  function startTour() {
    if (tour || walk || !built) return;
    if (intro) endIntro();
    closeCatalog(false);
    closeMenu(false);
    clearFound();
    setHover(null);
    tour = { steps: tourSteps(), i: -1, paused: false, at: performance.now(), lifted: [] };
    tourEl.hidden = false;
    enterImmersive();
    tourGo(1);
  }
  function stopTour() {
    if (!tour) return;
    tourLift([]);
    tour = null;
    tourEl.hidden = true;
    exitImmersive();
    resetView();
    announce('Presentation ended.');
  }
  tourEl.addEventListener('click', function (e) {
    var b = e.target.closest('[data-tour]');
    if (!b || !tour) return;
    var a = b.getAttribute('data-tour');
    if (a === 'exit') stopTour();
    else if (a === 'pause') tourPause();
    else tourGo(a === 'next' ? 1 : -1);
  });
  // Keluar dari layar penuh (Esc bawaan browser) = presentasi selesai
  var fsOn = false;
  function onFsChange() {
    var on = !!document.fullscreenElement;
    if (fsOn && !on) { if (tour) stopTour(); else if (walk) stopWalk(); }
    fsOn = on;
  }
  document.addEventListener('fullscreenchange', onFsChange);
  function onWinKey(e) {
    if (e.key !== 'Escape' || e.defaultPrevented) return;
    if (tour) { e.preventDefault(); stopTour(); }
    else if (walk) { e.preventDefault(); stopWalk(); }
  }
  window.addEventListener('keydown', onWinKey);

  // ---- navigasi keyboard di panggung ----------------------------------------
  // Kiri/kanan = pindah rak (sudut pustakawan paling kiri), bawah/atas = pilih
  // buku di rak itu, Enter = buka, Esc = mundur selangkah, Home = semua rak.
  function stopOrder() { return [DESK].concat(cases.map(function (c, i) { return i; }), studioStop ? [STUDIO] : []); }
  function booksAt(ci) {
    return books.filter(function (m) { return m.userData.ci === ci && m.userData.g.visible; }).sort(function (a, b) {
      var ua = a.userData, ub = b.userData;
      return (ub.baseY - ua.baseY) || (ua.g.position.x - ub.g.position.x);
    });
  }
  function selectBook(m) {
    clearFound();
    found = { mesh: m, t0: clock - 600, open: false, keep: true };
    m.userData.lift = 1;
    var p = m.userData.p;
    announce(p.project_name + ', ' + (+p.is_overdue === 1 ? 'overdue' : p.status) + (p.target_date && !isDone(p.status) ? ', due ' + fmt(p.target_date) : '') + '. Press Enter to open.');
  }
  function onKey(e) {
    if (walk || e.target !== host || e.altKey || e.ctrlKey || e.metaKey) return;
    var k = e.key;
    if (tour) {
      if (k === 'ArrowRight' || k === 'PageDown') { e.preventDefault(); tourGo(1); }
      else if (k === 'ArrowLeft' || k === 'PageUp') { e.preventDefault(); tourGo(-1); }
      else if (k === ' ') { e.preventDefault(); tourPause(); }
      return;
    }
    if (intro) endIntro();
    if (k === 'ArrowRight' || k === 'ArrowLeft') {
      e.preventDefault();
      var order = stopOrder(), at = order.indexOf(focus), right = k === 'ArrowRight';
      var nx = at < 0 ? (right ? 0 : order.length - 1) : clamp(at + (right ? 1 : -1), 0, order.length - 1);
      setFocus(order[nx]);
      announceStop(order[nx]);
    } else if (k === 'ArrowDown' || k === 'ArrowUp') {
      e.preventDefault();
      if (focus === -1) { setFocus(cases.length - 1); announceStop(cases.length - 1); return; }
      var list = booksAt(focus);
      if (!list.length) { announce('There are no books here.'); return; }
      var cur = found ? list.indexOf(found.mesh) : -1, down = k === 'ArrowDown';
      selectBook(list[cur < 0 ? (down ? 0 : list.length - 1) : clamp(cur + (down ? 1 : -1), 0, list.length - 1)]);
    } else if (k === 'Enter' || k === ' ') {
      if (found) { e.preventDefault(); takeBook(found.mesh); }
      else if (focus !== -1) {
        e.preventDefault();
        var l2 = booksAt(focus);
        if (l2.length) selectBook(l2[0]);
      }
    } else if (k === 'Escape') {
      if (found) { e.preventDefault(); clearFound(); announce('Selection cleared.'); }
      else if (focus !== -1) { e.preventDefault(); resetView(); announce('All bookcases.'); }
    } else if (k === 'Home') { e.preventDefault(); resetView(); announce('All bookcases.'); }
  }
  host.addEventListener('keydown', onKey);

  // ---- hiasan musiman -------------------------------------------------------
  function buildDecor(season, Ht, Hw) {
    var spans = cases.map(function (c) { return { g: c.group, w: CW + 0.08, y: Ht + 0.03, z: CD / 2 + 0.17 }; });
    if (corner) spans.push({ g: corner.group, w: WALL_W, y: Hw - 0.2, z: -CD / 2 + 0.24, wall: true });
    function sag(sp, f, depth) { return sp.y - depth * (1 - Math.pow(2 * f - 1, 2)); }
    function stringLine(sp, depth, color) {
      var pts = [];
      for (var i = 0; i <= 20; i++) { var f = i / 20; pts.push(new THREE.Vector3((f - 0.5) * sp.w, sag(sp, f, depth), sp.z)); }
      sp.g.add(new THREE.Line(track(new THREE.BufferGeometry().setFromPoints(pts)), track(new THREE.LineBasicMaterial({ color: color }))));
    }
    if (season === 'merdeka') {
      // umbul-umbul segitiga merah putih di mahkota tiap rak
      var red = track(new THREE.MeshStandardMaterial({ color: 0xd8232a, emissive: 0x3a0606, roughness: 0.7, side: THREE.DoubleSide }));
      var white = track(new THREE.MeshStandardMaterial({ color: 0xf4f1ea, emissive: 0x262626, roughness: 0.7, side: THREE.DoubleSide }));
      var tri = track(new THREE.ShapeGeometry(new THREE.Shape([new THREE.Vector2(-0.065, 0), new THREE.Vector2(0.065, 0), new THREE.Vector2(0, -0.13)])));
      spans.forEach(function (sp) {
        stringLine(sp, 0.05, 0xeeeeee);
        var n = Math.max(6, Math.round(sp.w / 0.24));
        for (var i = 0; i < n; i++) {
          var f = (i + 0.5) / n, fl = new THREE.Mesh(tri, i % 2 ? white : red);
          fl.position.set((f - 0.5) * sp.w, sag(sp, f, 0.05), sp.z);
          fl.rotation.z = (f - 0.5) * 0.25;
          sp.g.add(fl);
        }
      });
    } else if (season === 'lebaran') {
      // ketupat bergantung di ujung-ujung mahkota rak & dinding
      var kTex = track(ketupatTexture());
      var kMat = track(new THREE.MeshStandardMaterial({ map: kTex, roughness: 0.75, emissive: 0x1c2a08 }));
      var kGeo = track(new THREE.OctahedronGeometry(0.1, 0));
      var tailMat = track(new THREE.MeshStandardMaterial({ color: 0xc9d36a, roughness: 0.8, side: THREE.DoubleSide }));
      var strMat = track(new THREE.LineBasicMaterial({ color: 0xd6c08a }));
      spans.forEach(function (sp, si) {
        var xs = sp.wall ? [-0.95, 0.25, 1.35] : [-(CW / 2 - 0.3), CW / 2 - 0.3];
        xs.forEach(function (x, k) {
          var drop = 0.22 + ((si + k) % 3) * 0.07, y = sp.y - drop;
          var line = new THREE.Line(track(new THREE.BufferGeometry().setFromPoints([new THREE.Vector3(x, sp.y, sp.z), new THREE.Vector3(x, y + 0.09, sp.z)])), strMat);
          var kt = new THREE.Mesh(kGeo, kMat);
          kt.scale.set(1, 1.2, 0.55);
          kt.rotation.y = 0.5;
          kt.position.set(x, y, sp.z);
          var t1 = new THREE.Mesh(planeGeo, tailMat), t2 = new THREE.Mesh(planeGeo, tailMat);
          t1.scale.set(0.014, 0.12, 1);
          t2.scale.set(0.014, 0.1, 1);
          t1.position.set(x - 0.012, y - 0.13, sp.z);
          t2.position.set(x + 0.014, y - 0.12, sp.z);
          t1.rotation.z = 0.12;
          t2.rotation.z = -0.15;
          sp.g.add(line, kt, t1, t2);
        });
      });
    } else if (season === 'yearend') {
      // lampu kelap-kelip warna-warni (dua kelompok bergantian menyala)
      var palette = ['#ff5a5a', '#ffd65a', '#6ad1ff', '#8dff8a', '#ff9de6'].map(function (c) { return new THREE.Color(c); });
      [0, 1].forEach(function (half) {
        var pos = [], colors = [];
        spans.forEach(function (sp) {
          sp.g.updateMatrixWorld(true);
          var n = Math.max(8, Math.round(sp.w / 0.16));
          for (var i = half; i < n; i += 2) {
            var f = (i + 0.5) / n, v = sp.g.localToWorld(new THREE.Vector3((f - 0.5) * sp.w, sag(sp, f, 0.07), sp.z + 0.01));
            pos.push(v.x, v.y, v.z);
            var c = palette[i % palette.length];
            colors.push(c.r, c.g, c.b);
          }
          if (!half) stringLine(sp, 0.07, 0x2c4a2a);
        });
        var geo = track(new THREE.BufferGeometry());
        geo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
        geo.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));
        var mat = track(new THREE.PointsMaterial({ map: dotTex, size: 0.26, vertexColors: true, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending }));
        var pts = new THREE.Points(geo, mat);
        pts.frustumCulled = false;
        lib.add(pts);
        decorMats.push(mat);
      });
    }
  }

  // ---- isi ruangan --------------------------------------------------------
  // Dinding belakang (panel kayu + wallpaper, melebur ke latar hero) dgn
  // lukisan di atas rak, meja kerja programmer (layar = project yang sedang
  // dikerjakan), globe antik, sudut baca, tanaman, lampu gantung lilin & jam
  // dinding yang mengikuti jam asli. Perabot lantai <= 1,4 m dan berdiri di
  // depan rak: dari tinggi kamera, tidak menutupi buku di rak.
  function buildRoom(o) {
    var Ht = o.Ht, rm = room = { flames: [], globe: null, hands: null, handT: 0, globeBoost: 0, prog: o.progP.length, center: new THREE.Vector3(o.rx, 0, o.rz) };
    // tanaman ikut "kesehatan" tim: segar & berbunga / haus / layu
    var ov = o.st.overdue, onPct = o.st.measured ? o.st.onTime / o.st.measured : 1;
    rm.overdue = ov;
    rm.health = ov === 0 ? 'thriving' : ov <= 2 ? 'thirsty' : 'wilting';
    rm.droop = ov === 0 ? 0 : ov <= 2 ? 0.28 : 0.6;
    rm.bloom = ov === 0 && onPct >= 0.9;
    if (ov === 0) leafMat.color.setRGB(1, 1, 1);
    else if (ov <= 2) leafMat.color.setRGB(1, 0.9, 0.62);
    else leafMat.color.setRGB(0.95, 0.72, 0.38);
    function mk(geo, mat, x, y, z) { var m = new THREE.Mesh(geo, mat); m.position.set(x || 0, y || 0, z || 0); return m; }

    // 1. Ruangan: dinding, lantai beralas, plafon (lihat buildShell)
    o.ceil = Math.max(Ht + 2.7, 7.2);
    buildShell(o);

    // lukisan pemandangan berbingkai di atas tiap rak
    cases.forEach(function (c, ci) {
      var pt = track(paintingTexture(ci));
      pt.anisotropy = aniso;
      var py = Ht + 0.8, pw = 1.0, ph = 0.62, pz = -CD / 2 - 0.035;
      var pm = mk(planeGeo, track(new THREE.MeshStandardMaterial({ map: pt, roughness: 0.7, emissiveMap: pt, emissive: 0xffffff, emissiveIntensity: 0.14 })), 0, py, pz + 0.012);
      pm.scale.set(pw, ph, 1);
      var fr = new THREE.Mesh(track(boxesGeometry([
        { w: pw + 0.12, h: 0.06, d: 0.04, x: 0, y: py + ph / 2 + 0.03, z: pz },
        { w: pw + 0.12, h: 0.06, d: 0.04, x: 0, y: py - ph / 2 - 0.03, z: pz },
        { w: 0.06, h: ph, d: 0.04, x: -pw / 2 - 0.03, y: py, z: pz, v: 1 },
        { w: 0.06, h: ph, d: 0.04, x: pw / 2 + 0.03, y: py, z: pz, v: 1 }
      ])), brassMat);
      c.group.add(pm, fr);
    });

    // 2. Meja kerja programmer di atas karpet (lihat buildDev)
    buildDev(o);

    // 3. Globe antik di samping karpet (klik = diputar)
    var gb = new THREE.Group();
    gb.position.set(o.rx - o.rugW / 2 - 0.55, 0, o.rz + 0.35);
    lib.add(gb);
    var ring = mk(track(new THREE.TorusGeometry(0.34, 0.016, 8, 48)), brassMat, 0, 0.98, 0);
    ring.rotation.x = Math.PI / 2;
    gb.add(mk(track(new THREE.CylinderGeometry(0.24, 0.27, 0.05, 24)), woodMat, 0, 0.025, 0),
      mk(track(new THREE.CylinderGeometry(0.045, 0.065, 0.62, 14)), woodMat, 0, 0.36, 0),
      mk(track(new THREE.CylinderGeometry(0.08, 0.06, 0.06, 14)), brassMat, 0, 0.69, 0), ring);
    var tilt = new THREE.Group();
    tilt.position.y = 0.98;
    tilt.rotation.z = 0.41;
    gb.add(tilt);
    tilt.add(mk(track(new THREE.TorusGeometry(0.31, 0.012, 8, 48)), brassMat));
    var spinG = new THREE.Group();
    tilt.add(spinG);
    var sphere = mk(track(new THREE.SphereGeometry(0.28, 32, 20)), globeMat);
    sphere.castShadow = true;
    sphere.userData = { kind: 'globe', ci: -1 };
    pickables.push(sphere);
    spinG.add(sphere);
    rm.globe = spinG;

    // 4. Sudut baca: kursi kulit, meja kecil dgn cangkir teh, lampu berdiri
    var nk = new THREE.Group();
    nk.position.set(o.rx - o.rugW / 2 - 2.0, 0, o.rz + 1.1);
    nk.rotation.y = 0.55;
    lib.add(nk);
    var arm = new THREE.Mesh(track(boxesGeometry([
      { w: 0.95, h: 0.3, d: 0.86, x: 0, y: 0.25, z: 0 },
      { w: 0.66, h: 0.13, d: 0.66, x: 0, y: 0.465, z: 0.06 },
      { w: 0.95, h: 0.62, d: 0.2, x: 0, y: 0.62, z: -0.33 },
      { w: 0.2, h: 0.34, d: 0.86, x: -0.375, y: 0.53, z: 0 },
      { w: 0.2, h: 0.34, d: 0.86, x: 0.375, y: 0.53, z: 0 }
    ])), leatherMat);
    arm.castShadow = arm.receiveShadow = true;
    nk.add(arm);
    var legs = [];
    [-1, 1].forEach(function (sx) { [-1, 1].forEach(function (sz) { legs.push({ w: 0.06, h: 0.1, d: 0.06, x: sx * 0.4, y: 0.05, z: sz * 0.36 }); }); });
    nk.add(new THREE.Mesh(track(boxesGeometry(legs)), wheelMat));
    var seatBook = mk(unitBox, track(new THREE.MeshStandardMaterial({ color: 0x2a3a5a, roughness: 0.6 })), 0.8, 0.633, 0.05);
    seatBook.scale.set(0.2, 0.03, 0.15);
    seatBook.rotation.y = 0.5;
    nk.add(seatBook);
    // kucing perpustakaan yang tidur di kursi (klik = dielus, mendengkur)
    var cat = new THREE.Group();
    cat.position.set(0.0, 0.535, 0.1);
    cat.rotation.y = 0.7;
    nk.add(cat);
    var cBody = mk(track(new THREE.SphereGeometry(0.1, 18, 12)), catMat, 0, 0.06, 0);
    cBody.scale.set(1.5, 0.7, 1);
    var cHead = mk(track(new THREE.SphereGeometry(0.068, 16, 12)), catMat, 0.15, 0.07, 0.03);
    var earG = track(new THREE.ConeGeometry(0.026, 0.055, 4)), eyeL = track(new THREE.BoxGeometry(0.004, 0.005, 0.022));
    cHead.add(mk(earG, catMat, 0.0, 0.062, -0.036), mk(earG, catMat, 0.0, 0.062, 0.036),
      mk(eyeL, eyeMat, 0.064, 0.012, -0.024), mk(eyeL, eyeMat, 0.064, 0.012, 0.024), mk(track(new THREE.SphereGeometry(0.008, 8, 6)), catPinkMat, 0.068, -0.006, 0));
    var tail = new THREE.Group();
    tail.position.set(-0.12, 0.02, 0);
    var tailM = mk(track(new THREE.TorusGeometry(0.1, 0.02, 6, 16, Math.PI * 1.1)), catMat, 0.1, 0, 0);
    tailM.rotation.x = Math.PI / 2;
    tail.add(tailM);
    cat.add(cBody, cHead, tail);
    [cBody, cHead].forEach(function (m) { m.userData = { kind: 'cat', ci: -1 }; pickables.push(m); });
    rm.cat = { body: cBody, head: cHead, tail: tail, petT: null };
    var st = new THREE.Group();
    st.position.set(0.85, 0, 0.12);
    nk.add(st);
    st.add(mk(track(new THREE.CylinderGeometry(0.24, 0.24, 0.03, 28)), woodMat, 0, 0.6, 0),
      mk(track(new THREE.CylinderGeometry(0.03, 0.03, 0.56, 10)), woodMat, 0, 0.3, 0),
      mk(track(new THREE.CylinderGeometry(0.16, 0.18, 0.03, 20)), woodMat, 0, 0.015, 0),
      mk(track(new THREE.CylinderGeometry(0.075, 0.075, 0.008, 20)), porcelainMat, 0.04, 0.619, 0.03),
      mk(track(new THREE.CylinderGeometry(0.045, 0.036, 0.055, 16)), porcelainMat, 0.04, 0.65, 0.03));
    var fl = new THREE.Group();
    fl.position.set(-0.78, 0, -0.25);
    nk.add(fl);
    var flPool = mk(planeGeo, poolMat, 0.3, 0.006, 0.3);
    flPool.rotation.x = -Math.PI / 2;
    flPool.scale.set(1.8, 1.8, 1);
    var flGlow = new THREE.Sprite(flameGlow);
    flGlow.position.y = 1.5;
    flGlow.scale.set(0.9, 0.9, 1);
    fl.add(mk(track(new THREE.CylinderGeometry(0.16, 0.18, 0.03, 20)), brassMat, 0, 0.015, 0),
      mk(track(new THREE.CylinderGeometry(0.015, 0.015, 1.5, 8)), brassMat, 0, 0.77, 0),
      mk(track(new THREE.CylinderGeometry(0.14, 0.24, 0.28, 24, 1, true)), shadeFabMat, 0, 1.55, 0), flGlow, flPool);

    addObs(gb, 0, 0, 0.42);
    addObs(nk, 0, 0, 0.75);
    addObs(nk, 0.85, 0.12, 0.3);
    addObs(nk, -0.78, -0.25, 0.25);

    // 5. Tanaman pot di ujung-ujung ruangan
    function plant(parent, x, z, s, seed) {
      var pg = new THREE.Group();
      pg.position.set(x, 0, z);
      pg.scale.setScalar(s);
      parent.add(pg);
      var pot = mk(track(new THREE.CylinderGeometry(0.2, 0.15, 0.36, 20)), potMat, 0, 0.18, 0);
      pot.castShadow = true;
      pg.add(pot, mk(track(new THREE.CylinderGeometry(0.215, 0.215, 0.04, 20)), potMat, 0, 0.36, 0), mk(track(new THREE.CylinderGeometry(0.19, 0.19, 0.01, 20)), soilMat, 0, 0.35, 0));
      var r = rng(seed), lg = track(new THREE.PlaneGeometry(0.16, 0.62));
      lg.translate(0, 0.31, 0);
      for (var i = 0; i < 16; i++) {
        var lf = mk(lg, leafMat, 0, 0.34, 0);
        lf.rotation.set(0.25 + r() * 0.85 + rm.droop, i / 16 * Math.PI * 2 + r() * 0.3, 0, 'YXZ');
        lf.scale.setScalar(0.7 + r() * 0.6);
        pg.add(lf);
      }
      if (rm.bloom) {
        var flG = track(new THREE.SphereGeometry(0.035, 10, 8));
        for (var fb = 0; fb < 6; fb++) {
          var fa2 = fb / 6 * Math.PI * 2 + r(), fr2 = 0.14 + r() * 0.12;
          pg.add(mk(flG, flowerMat, Math.cos(fa2) * fr2, 0.62 + r() * 0.25, Math.sin(fa2) * fr2));
        }
      }
      pot.userData = { kind: 'plant', ci: -1 };
      pickables.push(pot);
      addObs(pg, 0, 0, 0.32 * s);
    }
    var lastC = cases[cases.length - 1];
    plant(lastC.group, CW / 2 + 0.25, 1.8, 1.0, 5);   // di depan kaki tangga, tidak menutupi label bulan
    if (corner) plant(corner.group, -WALL_W / 2 - 0.55, 0.45, 1.05, 9);
    plant(nk, -0.55, -0.8, 0.85, 13);

    // 6. Dua lampu gantung lilin di atas meja baca
    [-1, 1].forEach(function (sx) {
      var cl = new THREE.Group();
      cl.position.set(o.rx + sx * 0.95, 6.2, o.rz - 0.1);
      lib.add(cl);
      var cr = mk(track(new THREE.TorusGeometry(0.42, 0.018, 8, 40)), brassMat, 0, -0.12, 0);
      cr.rotation.x = Math.PI / 2;
      var bowl = mk(track(new THREE.SphereGeometry(0.09, 16, 8, 0, Math.PI * 2, Math.PI / 2, Math.PI / 2)), brassMat, 0, -0.17, 0);
      var rodL = Math.max(0.3, o.ceil - 6.2);   // tergantung dari plafon
      cl.add(mk(track(new THREE.CylinderGeometry(0.012, 0.012, rodL, 6)), brassMat, 0, rodL / 2, 0), mk(track(new THREE.CylinderGeometry(0.05, 0.03, 0.34, 12)), brassMat), cr, bowl);
      for (var k = 0; k < 6; k++) {
        var a = k / 6 * Math.PI * 2, cx = Math.cos(a) * 0.42, cz = Math.sin(a) * 0.42;
        var sp = new THREE.Sprite(flameGlow);
        sp.position.set(cx, 0.04, cz);
        sp.scale.set(0.34, 0.34, 1);
        cl.add(mk(track(new THREE.CylinderGeometry(0.022, 0.022, 0.13, 10)), candleMat, cx, -0.055, cz), mk(track(new THREE.SphereGeometry(0.018, 8, 6)), flameMat, cx, 0.03, cz), sp);
        rm.flames.push(sp);
      }
    });
    chandLight.position.set(o.rx, 5.6, o.rz - 0.1);

    // 7. Jam dinding di atas papan pengumuman (ikut jam asli)
    if (corner && corner.boardTop + 0.72 < corner.wallTop - 0.12) {
      var ck = new THREE.Group();
      ck.position.set(corner.bx, corner.boardTop + 0.42, -CD / 2 + 0.03);
      corner.group.add(ck);
      var rim = mk(track(new THREE.CylinderGeometry(0.26, 0.26, 0.06, 40)), woodMat);
      rim.rotation.x = Math.PI / 2;
      var face = mk(track(new THREE.CircleGeometry(0.22, 40)), clockMat, 0, 0, 0.031);
      face.userData = { kind: 'clock', ci: DESK };
      pickables.push(face);
      var hand = function (w, l, mat, z) {
        var gg = track(new THREE.BoxGeometry(w, l, 0.008));
        gg.translate(0, l / 2 - 0.02, 0);
        return mk(gg, mat, 0, 0, z);
      };
      var hh = hand(0.022, 0.13, handMat, 0.036), mm = hand(0.014, 0.19, handMat, 0.041), ss = hand(0.006, 0.2, redMat, 0.046);
      ck.add(rim, face, hh, mm, ss);
      rm.hands = { h: hh, m: mm, s: ss };
    }

    // 8. Lucca, sistem perpustakaan
    buildLucca(o);
  }

  // ---- cangkang ruangan: dinding, lantai, plafon ----------------------------
  // Dilihat dari luar (ringkasan, presentasi) ruangannya seperti rumah boneka
  // yang dipotong: dinding belakang setinggi plafon, dinding samping terpotong
  // miring, lantainya alas kayu tebal bertepi kuningan. Dinding samping & depan
  // hanya punya satu muka (menghadap ke dalam) - dari luar tembus pandang, jadi
  // tidak pernah menutupi rak. Saat jalan-jalan ruangannya tertutup penuh:
  // dinding utuh, plafon kasetan, dinding depan berpintu & berjendela.
  function buildShell(o) {
    var Ht = o.Ht, CEIL = o.ceil, T = 0.2, rad = ARC_R + CD / 2 + 0.07, i;
    var aL = corner ? corner.angle - (WALL_W / 2 + 1.3) / ARC_R : cases[0].angle - (CW / 2 + 1.2) / ARC_R;
    var aR = cases[cases.length - 1].angle + (CW / 2 + 1.8 + (wing ? wing.width() : 0)) / ARC_R;   // + sudut Design Studio
    var xL = rad * Math.sin(aL), xR = rad * Math.sin(aR);
    var zF = Math.max(o.rz + o.rugW * 0.29, o.rz + 2.1) + 3.4;
    var hF = 1.15, s0 = 1.4;   // dinding samping: utuh 1,4 m dari belakang, lalu turun miring sampai 1,15 m di depan
    var walkOnly = room.walkOnly = new THREE.Group();
    walkOnly.visible = !!walk;
    lib.add(walkOnly);
    room.shell = { xL: xL, xR: xR, zF: zF, ceil: CEIL };
    room.sides = [];
    function mk(geo, mat, x, y, z) { var m = new THREE.Mesh(geo, mat); m.position.set(x || 0, y || 0, z || 0); return m; }

    // 1. Dinding belakang melengkung, setinggi plafon. Koordinat tekstur dalam
    //    ubin (panel kayu 1,2 x 2,4 m, damask 0,8 m) supaya potongan-potongannya
    //    menyambung rapi.
    function backSeg(b0, b1, y0, y1, mat, tu, tv) {
      if (b1 - b0 < 1e-4 || y1 - y0 < 1e-3) return;
      var h = y1 - y0, geo = track(new THREE.CylinderGeometry(rad, rad, h, Math.max(3, Math.ceil((b1 - b0) * 90)), 1, true, Math.PI - b1, b1 - b0)), uv = geo.attributes.uv;
      for (var k = 0; k < uv.count; k++) uv.setXY(k, rad * (b1 - uv.getX(k) * (b1 - b0)) / tu, (y0 + uv.getY(k) * h) / tv);
      var m = mk(geo, mat, 0, y0 + h / 2, ARC_R);
      m.receiveShadow = true;
      lib.add(m);
    }
    if (corner) {
      // di belakang sudut pustakawan dinding plesterannya sendiri; damask mulai di atasnya
      var cw = Math.atan2(WALL_W / 2 + 0.1, ARC_R + CD / 2), cL = corner.angle - cw, cR = corner.angle + cw;
      backSeg(aL, cL + 0.004, 0, Ht, wallLowMat, 1.2, 2.4);
      backSeg(aL, cL + 0.004, Ht, CEIL, wallUpMat, 0.8, 0.8);
      backSeg(cL, cR, corner.wallTop, CEIL, wallUpMat, 0.8, 0.8);
      backSeg(cR - 0.003, aR, 0, Ht, wallLowMat, 1.2, 2.4);
      backSeg(cR - 0.003, aR, Ht, CEIL, wallUpMat, 0.8, 0.8);
    } else {
      backSeg(aL, aR, 0, Ht, wallLowMat, 1.2, 2.4);
      backSeg(aL, aR, Ht, CEIL, wallUpMat, 0.8, 0.8);
    }
    // list mahkota & list kecil di bawahnya, lalu potongan tebal dinding di puncaknya
    lib.add(mk(track(new THREE.CylinderGeometry(rad - 0.24, rad - 0.02, 0.34, 120, 1, true, Math.PI - aR, aR - aL)), trimMat, 0, CEIL - 0.17, ARC_R),
      mk(track(new THREE.CylinderGeometry(rad - 0.05, rad - 0.05, 0.07, 120, 1, true, Math.PI - aR, aR - aL)), trimMat, 0, CEIL - 0.42, ARC_R));
    var capB = mk(track(new THREE.RingGeometry(rad - 0.24, rad + T, 120, 1, Math.PI / 2 - aR, aR - aL)), cutMat, 0, CEIL + 0.002, ARC_R);
    capB.rotation.x = -Math.PI / 2;
    lib.add(capB);

    // 2. Dinding samping & depan (datar): poligon u = sepanjang dinding, v = tinggi
    function clipY(poly, y, above) {
      var out = [];
      for (var k = 0; k < poly.length; k++) {
        var a = poly[k], b = poly[(k + 1) % poly.length], ina = above ? a[1] >= y : a[1] <= y, inb = above ? b[1] >= y : b[1] <= y;
        if (ina) out.push(a);
        if (ina !== inb) { var t = (y - a[1]) / (b[1] - a[1]); out.push([a[0] + (b[0] - a[0]) * t, y]); }
      }
      return out;
    }
    function flat(poly, mat, tu, tv, parent) {
      if (poly.length < 3) return;
      var s = new THREE.Shape();
      poly.forEach(function (p, k) { if (k) s.lineTo(p[0], p[1]); else s.moveTo(p[0], p[1]); });
      var geo = track(new THREE.ShapeGeometry(s)), uv = geo.attributes.uv;
      for (var k = 0; k < uv.count; k++) uv.setXY(k, uv.getX(k) / tu, uv.getY(k) / tv);
      var m = new THREE.Mesh(geo, mat);
      m.receiveShadow = true;
      parent.add(m);
    }
    // panel kayu sampai setinggi rak, damask di atasnya
    function layered(poly, parent) {
      flat(clipY(poly, Ht, false), wallLowIn, 1.2, 2.4, parent);
      flat(clipY(poly, Ht, true), wallUpIn, 0.8, 0.8, parent);
    }
    // list kayu menempel di dinding (sumbu z lokal = ke dalam ruangan)
    function trimX(parent, xa, xb, y, h, dz) {
      if (Math.abs(xb - xa) < 0.02) return;
      var m = mk(unitBox, woodMat, (xa + xb) / 2, y, dz / 2);
      m.scale.set(Math.abs(xb - xa), h, dz);
      m.receiveShadow = true;
      parent.add(m);
    }
    var scGeo = [track(new THREE.CylinderGeometry(0.075, 0.075, 0.025, 16)), track(new THREE.CylinderGeometry(0.05, 0.034, 0.05, 14)),
      track(new THREE.CylinderGeometry(0.02, 0.02, 0.14, 10)), track(new THREE.SphereGeometry(0.02, 8, 6))];
    scGeo[0].rotateX(Math.PI / 2);
    function sconce(parent, x, y) {
      var sc = new THREE.Group(), arm = mk(unitBox, brassMat, 0, -0.05, 0.1), glow = new THREE.Sprite(flameGlow);
      arm.scale.set(0.03, 0.03, 0.2);
      glow.position.set(0, 0.2, 0.2);
      glow.scale.set(0.34, 0.34, 1);
      sc.position.set(x, y, 0);
      sc.add(mk(scGeo[0], brassMat, 0, 0, 0.012), arm, mk(scGeo[1], brassMat, 0, -0.02, 0.2), mk(scGeo[2], candleMat, 0, 0.07, 0.2), mk(scGeo[3], flameMat, 0, 0.16, 0.2), glow);
      room.flames.push(glow);
      parent.add(sc);
    }
    function side(z0, x0, sgn) {   // sgn -1 = kiri (menghadap +x), 1 = kanan (menghadap -x)
      var L = zF - z0, d = sgn < 0 ? -1 : 1, gA = new THREE.Group(), gW = new THREE.Group(), capG = new THREE.Group();
      [gA, gW].forEach(function (g) { g.position.set(x0, 0, z0); g.rotation.y = sgn < 0 ? Math.PI / 2 : -Math.PI / 2; });
      lib.add(gA);
      gA.add(capG);
      walkOnly.add(gW);
      // dilihat dari luar dinding ini (kamera di sisi luar bidangnya) seluruhnya
      // disembunyikan - kalau tidak, list, lampu & potongannya melayang di depan isi ruangan
      room.sides.push({ g: gA, caps: capG, x: x0, inX: -sgn });
      layered([[0, 0], [d * L, 0], [d * L, hF], [d * s0, CEIL], [0, CEIL]], gA);
      layered([[d * s0, CEIL], [d * L, hF], [d * L, CEIL]], gW);
      // potongan tebal dinding di sisi atas yang miring & ujung depan
      function cap(len, x, y, rz, h) {
        var m = mk(unitBox, cutMat, x, y, -T / 2 + 0.01);
        m.scale.set(len, h || 0.05, T + 0.02);
        m.rotation.z = rz || 0;
        capG.add(m);   // potongan: hanya dari luar (saat jalan-jalan dindingnya utuh)
      }
      cap(s0, d * s0 / 2, CEIL - 0.025);
      cap(Math.hypot(L - s0, CEIL - hF) + 0.03, d * (s0 + L) / 2, (CEIL + hF) / 2 - 0.025, Math.atan2(hF - CEIL, d * (L - s0)));
      cap(0.05, d * L, hF / 2, 0, hF);
      // plint, list setinggi rak, list mahkota (yang terpotong ikut mode jalan-jalan)
      var sH = s0 + (L - s0) * (CEIL - Ht - 0.1) / (CEIL - hF);
      trimX(gA, 0, d * L, 0.08, 0.16, 0.05);
      trimX(gA, 0, d * sH, Ht + 0.03, 0.1, 0.07);
      trimX(gW, d * sH, d * L, Ht + 0.03, 0.1, 0.07);
      trimX(gA, 0, d * s0, CEIL - 0.17, 0.34, 0.2);
      trimX(gW, d * s0, d * L, CEIL - 0.17, 0.34, 0.2);
      // lampu dinding lilin (hanya yang masih di bawah potongan miring)
      [2.6, 5.4].forEach(function (s) {
        var top = s <= s0 ? CEIL : CEIL - (CEIL - hF) * (s - s0) / (L - s0);
        if (s < L - 0.8 && top > 3.1) sconce(gA, d * s, 2.5);
      });
    }
    side(ARC_R - rad * Math.cos(aL), xL, -1);
    side(ARC_R - rad * Math.cos(aR), xR, 1);

    // 3. Lantai papan dalam batas ruangan + alas kayu tebal bertepi kuningan
    function footprint(r, x0, x1, flipZ) {
      var s = new THREE.Shape(), a0 = Math.asin(clamp(x0 / r, -1, 1)), a1 = Math.asin(clamp(x1 / r, -1, 1)), n = 72, sz = flipZ ? -1 : 1;
      for (var k = 0; k <= n; k++) {
        var a = a0 + (a1 - a0) * k / n, x = r * Math.sin(a), z = ARC_R - r * Math.cos(a);
        if (k) s.lineTo(x, sz * z); else s.moveTo(x, sz * z);
      }
      s.lineTo(x1, sz * zF);
      s.lineTo(x0, sz * zF);
      return s;
    }
    var fGeo = track(new THREE.ShapeGeometry(footprint(rad, xL, xR, true))), fuv = fGeo.attributes.uv;
    for (i = 0; i < fuv.count; i++) fuv.setXY(i, fuv.getX(i) / 52 + 0.5, fuv.getY(i) / 52 + 0.5);
    fGeo.rotateX(-Math.PI / 2);
    var floor = new THREE.Mesh(fGeo, floorMat);
    floor.receiveShadow = true;
    lib.add(floor);
    var slabGeo = track(new THREE.ExtrudeGeometry(footprint(rad + T, xL - T, xR + T, true), { depth: 0.42, bevelEnabled: false }));
    slabGeo.rotateX(-Math.PI / 2);
    slabGeo.translate(0, -0.425, 0);
    lib.add(new THREE.Mesh(slabGeo, slabMat));
    [-0.012, -0.4].forEach(function (y) {
      var edge = mk(unitBox, brassMat, (xL + xR) / 2, y, zF + 0.012);
      edge.scale.set(xR - xL + 2 * T, y > -0.1 ? 0.035 : 0.025, 0.03);
      lib.add(edge);
    });

    // 4. Hanya saat jalan-jalan: plafon kasetan, roset lampu gantung, dinding depan
    var cGeo = track(new THREE.ShapeGeometry(footprint(rad, xL, xR, false))), cuv = cGeo.attributes.uv;
    for (i = 0; i < cuv.count; i++) cuv.setXY(i, cuv.getX(i) / 2.4, cuv.getY(i) / 2.4);
    cGeo.rotateX(Math.PI / 2);
    walkOnly.add(mk(cGeo, ceilMat, 0, CEIL, 0));
    var roseGeo = track(new THREE.CylinderGeometry(0.2, 0.28, 0.06, 24));
    [-1, 1].forEach(function (sx) { walkOnly.add(mk(roseGeo, brassMat, o.rx + sx * 0.95, CEIL - 0.03, o.rz - 0.1)); });

    var W = xR - xL, fw = new THREE.Group();
    fw.position.set(xL, 0, zF);
    fw.rotation.y = Math.PI;   // x lokal ke arah -x dunia, mukanya menghadap rak
    walkOnly.add(fw);
    layered([[0, 0], [-W, 0], [-W, CEIL], [0, CEIL]], fw);
    trimX(fw, 0, -W, 0.08, 0.16, 0.05);
    trimX(fw, 0, -W, Ht + 0.03, 0.1, 0.07);
    trimX(fw, 0, -W, CEIL - 0.17, 0.34, 0.2);
    function arch(w2, hs) {
      var s = new THREE.Shape();
      s.moveTo(-w2, 0); s.lineTo(w2, 0); s.lineTo(w2, hs);
      s.absarc(0, hs, w2, 0, Math.PI, false);
      s.lineTo(-w2, 0);
      return s;
    }
    function archRing(w2, hs, f) {
      var r = new THREE.Shape();
      r.moveTo(-w2 - f, 0); r.lineTo(-w2 - f, hs);
      r.absarc(0, hs, w2 + f, Math.PI, 0, true);
      r.lineTo(w2 + f, 0); r.lineTo(w2, 0); r.lineTo(w2, hs);
      r.absarc(0, hs, w2, 0, Math.PI, false);
      r.lineTo(-w2, 0); r.lineTo(-w2 - f, 0);
      return r;
    }
    // pintu ganda melengkung + papan nama kuningan di atasnya
    var dx = -W / 2, dw = 1.1, dh = 2.5;
    fw.add(mk(track(new THREE.ExtrudeGeometry(arch(dw, dh), { depth: 0.06, bevelEnabled: false, curveSegments: 24 })), doorMat, dx, 0, 0),
      mk(track(new THREE.ExtrudeGeometry(archRing(dw, dh, 0.15), { depth: 0.13, bevelEnabled: false, curveSegments: 24 })), woodMat, dx, 0, 0));
    var dParts = [{ w: 0.03, h: dh + dw - 0.04, d: 0.02, x: 0, y: (dh + dw) / 2, z: 0.07 }];
    [-1, 1].forEach(function (sx) {
      dParts.push({ w: dw - 0.32, h: 0.95, d: 0.03, x: sx * dw / 2, y: 0.75, z: 0.075 }, { w: dw - 0.32, h: 1.0, d: 0.03, x: sx * dw / 2, y: 1.95, z: 0.075 });
    });
    fw.add(mk(track(boxesGeometry(dParts)), doorMat, dx, 0, 0));
    var ringG = track(new THREE.TorusGeometry(0.07, 0.012, 8, 24));
    [-1, 1].forEach(function (sx) { fw.add(mk(ringG, brassMat, dx + sx * 0.16, 1.2, 0.1)); });
    var signTex = track(brassTexture(1024, 200, function (sg, w, h) {
      sg.textAlign = 'center'; sg.textBaseline = 'middle';
      fitFont(sg, 'PROJECT LIBRARY', '700', 88, 'Georgia, "Times New Roman", serif', w - 120);
      engrave(sg, 'PROJECT LIBRARY', w / 2, h / 2 - 12);
      sg.font = '600 30px Georgia, "Times New Roman", serif';
      engrave(sg, 'Finance Systems · Delivery Portfolio', w / 2, h / 2 + 52);
    }));
    signTex.anisotropy = aniso;
    var sign = mk(planeGeo, track(new THREE.MeshStandardMaterial({ map: signTex, emissiveMap: signTex, emissive: 0xffffff, emissiveIntensity: 0.25, metalness: 0.5, roughness: 0.35 })), dx, dh + dw + 0.5, 0.03);
    sign.scale.set(2.6, 2.6 * 200 / 1024, 1);
    fw.add(sign);
    sconce(fw, dx - 1.9, 2.3);
    sconce(fw, dx + 1.9, 2.3);
    // jendela melengkung (langitnya ikut jam & cuaca, sama dgn jendela pustakawan)
    var ww = 1.3, wr = ww / 2, whs = 2.0, wsill = 1.3;
    var paneGeo = track(new THREE.ShapeGeometry(arch(wr, whs), 24)), pA = paneGeo.attributes.position, uA = paneGeo.attributes.uv;
    for (i = 0; i < pA.count; i++) uA.setXY(i, (pA.getX(i) + wr) / ww, pA.getY(i) / (whs + wr));
    var wRing = track(new THREE.ExtrudeGeometry(archRing(wr, whs, 0.09), { depth: 0.08, bevelEnabled: false, curveSegments: 24 }));
    var wb = [{ w: 0.04, h: whs + wr, d: 0.05, x: 0, y: (whs + wr) / 2, z: 0.03 }];
    [0.33, 0.66, 1].forEach(function (f) { wb.push({ w: ww, h: 0.035, d: 0.05, x: 0, y: whs * f, z: 0.03 }); });
    wb.push({ w: ww + 0.3, h: 0.06, d: 0.2, x: 0, y: -0.03, z: 0.1 });
    var wBars = track(boxesGeometry(wb));
    (W > 9 ? [0.14, 0.32, 0.68, 0.86] : [0.22, 0.78]).forEach(function (f) {
      var x = -W * f;
      fw.add(mk(paneGeo, skyMat, x, wsill, 0.004), mk(wRing, woodMat, x, wsill, 0), mk(wBars, woodMat, x, wsill, 0));
    });

    // batas jalan-jalan: di dalam dinding (rak & perabot lewat walkObs)
    walkLim.x0 = xL + 0.45;
    walkLim.x1 = xR - 0.45;
    walkLim.z1 = zF - 0.5;
  }

  // ---- LUCCA: sistem perpustakaan ------------------------------------------
  // "Program" yang mengawasi seluruh perpustakaan: bola hologram yang melayang
  // di atas rak. Warnanya ikut kondisi (biru tenang / kuning ada tenggat dekat /
  // merah ada yang terlambat). Ia berpatroli dari rak ke rak & memindai buku
  // yang perlu perhatian, mengumumkan kabar dari rekan, memberi arahan ke
  // pustakawan & programmer (garis data + paket yang terbang ke mereka), dan
  // memberi laporan singkat saat diklik (dibacakan kalau suara perpustakaan
  // nyala). Menyapa sekali sehari.
  var DOWN = new THREE.Vector3(0, -1, 0), beamV = new THREE.Vector3();
  function placeBeam(m, a, b) {
    beamV.subVectors(b, a);
    var len = beamV.length() || 1e-3;
    m.position.copy(a);
    m.quaternion.setFromUnitVectors(DOWN, beamV.divideScalar(len));
    m.scale.set(1, len, 1);
  }
  function luccaHot(m) {
    var p = m.userData.p;
    if (isDone(p.status)) return false;
    if (+p.is_overdue === 1) return true;
    var td = parseDay(p.target_date), left = td ? daysFrom(today0(), td) : -1;
    return left >= 0 && left <= 7;
  }
  // warna Lucca: kondisi rak + sudut Design Studio (mockup/ERD yang menunggu persetujuan)
  function luccaMoodOf(st) {
    var sm = wing && wing.lucca ? wing.lucca().mood : 'calm';
    return st.overdue || sm === 'alert' ? 'alert' : st.dueSoon || sm === 'watch' ? 'watch' : 'calm';
  }
  function buildLucca(o) {
    var st = o.st, mood = luccaMoodOf(st), base = new THREE.Color(LUCCA_COL[mood]);
    function addMat(extra) {
      var p = { color: base.clone(), transparent: true, depthWrite: false, blending: THREE.AdditiveBlending };
      for (var k in extra) p[k] = extra[k];
      return track(new THREE.MeshBasicMaterial(p));
    }
    var coreMat = track(new THREE.MeshBasicMaterial({ color: base.clone().multiplyScalar(1.7) }));
    var ringMat = addMat({ opacity: 0.85, side: THREE.DoubleSide }), lineMat = addMat({ opacity: 0.32, wireframe: true });
    var glowMat = track(new THREE.SpriteMaterial({ map: glowTex, color: base.clone(), transparent: true, opacity: 0.8, depthWrite: false, blending: THREE.AdditiveBlending }));
    var dotMat = track(new THREE.SpriteMaterial({ map: glowTex, color: base.clone(), transparent: true, opacity: 1, depthWrite: false, blending: THREE.AdditiveBlending }));
    var moteMat = track(new THREE.PointsMaterial({ map: dotTex, color: base.clone(), size: 0.08, transparent: true, depthWrite: false, blending: THREE.AdditiveBlending }));
    var irisMat = track(new THREE.MeshBasicMaterial({ color: base.clone().multiplyScalar(0.35) }));
    var g = new THREE.Group();
    lib.add(g);
    var core = new THREE.Mesh(track(new THREE.SphereGeometry(0.15, 24, 16)), coreMat);
    var shell = new THREE.Mesh(track(new THREE.IcosahedronGeometry(0.31, 1)), lineMat);
    var r1 = new THREE.Mesh(track(new THREE.TorusGeometry(0.43, 0.009, 6, 80)), ringMat);
    var r2 = new THREE.Mesh(track(new THREE.TorusGeometry(0.53, 0.006, 6, 80)), ringMat);
    r1.rotation.x = 1.2;
    r2.rotation.y = 0.9;
    var glow = new THREE.Sprite(glowMat);
    glow.scale.set(1.4, 1.4, 1);
    // "mata" seperti lensa: pupil gelap, cincin iris, kilau kecil - selalu menoleh
    var eye = new THREE.Group(), pupil = new THREE.Mesh(track(new THREE.CircleGeometry(0.068, 28)), pupilMat);
    pupil.position.z = 0.152;
    var iris = new THREE.Mesh(track(new THREE.RingGeometry(0.066, 0.094, 32)), irisMat);
    iris.position.z = 0.1515;
    var glint = new THREE.Mesh(track(new THREE.CircleGeometry(0.016, 12)), glintMat);
    glint.position.set(0.024, 0.024, 0.153);
    eye.add(pupil, iris, glint);
    var mg = track(new THREE.BufferGeometry()), mp = new Float32Array(22 * 3);
    for (var i = 0; i < 22; i++) {
      var a = i / 22 * Math.PI * 2, rr = 0.64 + (i % 3) * 0.05;
      mp[i * 3] = Math.cos(a) * rr; mp[i * 3 + 1] = ((i * 7) % 5 - 2) * 0.045; mp[i * 3 + 2] = Math.sin(a) * rr;
    }
    mg.setAttribute('position', new THREE.BufferAttribute(mp, 3));
    var motes = new THREE.Points(mg, moteMat);
    var hit = new THREE.Mesh(track(new THREE.SphereGeometry(0.42, 12, 8)), hitMat);
    hit.userData = { kind: 'lucca', ci: -1 };
    pickables.push(hit);
    g.add(glow, core, shell, r1, r2, eye, motes, hit);

    // berkas pindai (kerucut) & cincin penanda di buku, garis perintah + paket data
    var beamGeo = track(new THREE.CylinderGeometry(0.02, 0.24, 1, 20, 1, true));
    beamGeo.translate(0, -0.5, 0);
    var beamMat = addMat({ opacity: 0, side: THREE.DoubleSide }), markMat = addMat({ opacity: 0, side: THREE.DoubleSide });
    var beam = new THREE.Mesh(beamGeo, beamMat), mark = new THREE.Mesh(track(new THREE.RingGeometry(0.2, 0.235, 40)), markMat);
    beam.visible = mark.visible = false;
    lib.add(beam, mark);
    var linkGeo = track(new THREE.CylinderGeometry(0.011, 0.011, 1, 6, 1, true));
    linkGeo.translate(0, -0.5, 0);
    var links = [0, 1].map(function () {
      var lm = addMat({ opacity: 0.5 }), mesh = new THREE.Mesh(linkGeo, lm), dot = new THREE.Sprite(dotMat);
      dot.scale.set(0.24, 0.24, 1);
      mesh.visible = dot.visible = false;
      lib.add(mesh, dot);
      return { mesh: mesh, mat: lm, dot: dot, to: new THREE.Vector3() };
    });

    // perhentian patroli: di atas tiap rak, di atas sudut pustakawan, & "rumah" di atas meja programmer
    var wps = [];
    cases.forEach(function (c, ci) {
      var bs = books.filter(function (m) { return m.userData.ci === ci; });
      wps.push({ p: c.group.localToWorld(new THREE.Vector3(0, o.Ht + 0.8, CD / 2 + 1.5)), pf: c.group.localToWorld(new THREE.Vector3(CW / 2 + 0.25, o.Ht - 0.1, CD / 2 + 1.5)),
        ci: ci, books: bs, hot: bs.filter(luccaHot) });
    });
    if (corner) {
      var db = books.filter(function (m) { return m.userData.ci === DESK && m.userData.where === 'desk'; });
      wps.push({ p: corner.group.localToWorld(new THREE.Vector3(0.5, Math.max(corner.wallTop, o.Ht) + 0.75, 1.6)), ci: DESK, books: db, hot: db.filter(luccaHot) });
    }
    var home = { p: new THREE.Vector3(o.rx - 1.4, o.Ht + 1.15, o.rz - 2.0), ci: -1, books: [], hot: [] };
    wps.push(home);
    var start = luccaPos && !reduced ? luccaPos.clone() : home.p.clone();
    g.position.copy(start);
    room.lucca = {
      g: g, core: core, shell: shell, r1: r1, r2: r2, eye: eye, glow: glow, motes: motes, beam: beam, mark: mark, links: links,
      mats: [coreMat, ringMat, lineMat, glowMat, dotMat, moteMat, beamMat, markMat, irisMat].concat(links.map(function (l) { return l.mat; })),
      coreMat: coreMat, beamMat: beamMat, markMat: markMat, irisMat: irisMat,
      base: base, colNow: base.clone(), gold: new THREE.Color(LUCCA_COL.gold), mood: mood, st: st,
      pos: start.clone(), from: start.clone(), to: start.clone(), t0: clock, dur: 0, at: home, pend: null, scan: null, scanPos: new THREE.Vector3(), cmd: null,
      wps: wps, home: home, next: clock + 2500 + Math.random() * 2500, spin: 0, goldT: null, talkUntil: 0, hov: 0, blinkAt: clock + 2000, blinkT: null
    };
  }
  // terbang ke perhentian (lalu memindai `book` kalau ada)
  function luccaGo(L, wp, book) {
    luccaEndScan(L);
    L.from.copy(L.pos);
    L.to.copy(wp.pf && focus === wp.ci ? wp.pf : wp.p);   // dari dekat: di pojok kanan atas rak, tidak tertutup tombol
    L.at = wp;
    L.t0 = clock;
    L.dur = reduced ? 0 : clamp(L.from.distanceTo(L.to) / 1.7, 0.6, 6) * 1000;
    L.pend = book || null;
    L.next = clock + L.dur + 6000 + Math.random() * 6000;
  }
  function luccaPatrol(L) {
    var others = L.wps.filter(function (w) { return w !== L.at; });
    var hot = others.filter(function (w) { return w.hot.length; });
    var wp = hot.length && Math.random() < 0.6 ? hot[Math.floor(Math.random() * hot.length)] : others[Math.floor(Math.random() * others.length)];
    if (!wp) return;
    var bk = wp.hot.length ? wp.hot[Math.floor(Math.random() * wp.hot.length)] : wp.books.length && Math.random() < 0.5 ? wp.books[Math.floor(Math.random() * wp.books.length)] : null;
    luccaGo(L, wp, bk);
  }
  function luccaEndScan(L) {
    if (!L.scan) return;
    var m = L.scan.m;
    if (m !== hovered && (!found || found.mesh !== m)) m.userData.lift = 0;
    L.scan = null;
    L.beam.visible = L.mark.visible = false;
  }
  // terbang ke rak sebuah buku & memindainya (kabar dari rekan, "Show me")
  function luccaFocus(id) {
    var L = room && room.lucca, m = shelfById[String(id)] || books.filter(function (b) { return String(b.userData.p.id) === String(id); })[0];
    if (!L || !m) return;
    var wp = L.wps.filter(function (w) { return w.ci === m.userData.ci; })[0];
    if (!wp) return;
    luccaGo(L, wp, m);
    L.spin = Math.max(L.spin, 2);
  }
  // arahan ke pustakawan & programmer: garis data + paket, lalu mereka bereaksi
  function luccaCommand(L, attn) {
    var tg = [];
    if (corner && corner.lib) {
      tg.push({ obj: corner.lib.head, react: function () {
        var lb = corner && corner.lib;
        if (!lb) return;
        if (attn) { lb.stampT = clock; sound.play('stamp'); } else lb.waveT = clock;
      } });
    }
    if (room.dev && room.dev.head) tg.push({ obj: room.dev.head, react: function () { if (room && room.dev && room.dev.waveT == null) room.dev.waveT = clock; } });
    L.cmd = { t0: clock, tg: tg };
  }
  function updateLucca(dt) {
    var L = room.lucca, g = L.g, ts = clock / 1000, k = L.dur > 0 ? clamp((clock - L.t0) / L.dur, 0, 1) : 1, e = easeInOutCubic(k);
    L.pos.lerpVectors(L.from, L.to, e);
    luccaPos = L.pos;
    g.position.copy(L.pos);
    if (!reduced) g.position.y += Math.sin(ts * 1.4) * 0.07 + Math.sin(e * Math.PI) * 0.22;
    if (k >= 1) {
      if (L.pend) { L.scan = { m: L.pend, t0: clock }; L.pend = null; }
      else if (!L.scan && !L.cmd && !reduced && !walk && !tour && clock > L.next) luccaPatrol(L);
    }
    // warna: ikut kondisi, keemasan sebentar saat ada yang selesai
    if (L.goldT != null && clock - L.goldT > 3500) L.goldT = null;
    L.colNow.lerp(L.goldT != null ? L.gold : L.base, Math.min(1, dt * 4));
    L.mats.forEach(function (m) { m.color.copy(L.colNow); });
    L.coreMat.color.multiplyScalar(1.7);
    L.irisMat.color.multiplyScalar(0.35);
    // putaran cincin & cangkang (lebih cepat saat sibuk / bersorak)
    L.spin = Math.max(0, L.spin - dt * 1.2);
    var sp = reduced ? 0 : dt * (1 + L.spin);
    L.shell.rotation.y += sp * 0.35; L.shell.rotation.x += sp * 0.12;
    L.r1.rotation.x += sp * 0.8; L.r1.rotation.y += sp * 0.3;
    L.r2.rotation.z += sp * 0.6; L.r2.rotation.x -= sp * 0.25;
    L.motes.rotation.y -= sp * 0.5;
    var talk = clock < L.talkUntil && !reduced ? 0.5 + 0.5 * Math.sin(ts * 17) : 0;
    L.hov += ((hoverThing === 'lucca' ? 1 : 0) - L.hov) * Math.min(1, dt * 8);
    L.core.scale.setScalar(1 + talk * 0.16);
    L.glow.scale.setScalar(1.4 + 0.3 * talk + 0.35 * L.hov + (reduced ? 0 : 0.08 * Math.sin(ts * 2.1)));
    luccaLight.position.copy(g.position);
    luccaLight.color.copy(L.colNow);
    luccaLight.intensity = (1.3 + 0.9 * talk + 0.6 * L.hov) * (spotPic ? 0.5 : 1);
    // mata: melihat buku yang dipindai, selain itu ke kamera; sesekali berkedip
    if (L.scan) L.scan.m.getWorldPosition(L.scanPos);
    L.eye.lookAt(L.scan ? L.scanPos : camera.position);
    if (!reduced && clock > L.blinkAt) { L.blinkT = clock; L.blinkAt = clock + 2500 + Math.random() * 4500; }
    var bk = L.blinkT != null ? (clock - L.blinkT) / 170 : 2;
    L.eye.scale.y = bk < 1 ? 0.12 + 0.88 * Math.abs(Math.cos(bk * Math.PI)) : 1;
    // memindai buku: kerucut cahaya, cincin berdenyut, bukunya sedikit tertarik keluar
    if (L.scan) {
      var sc = (clock - L.scan.t0) / 2800;
      if (sc >= 1) { luccaEndScan(L); L.next = Math.max(L.next, clock + 4000); }
      else {
        var fade = Math.min(1, sc * 5) * Math.min(1, (1 - sc) * 5), ph = (sc * 3) % 1;
        L.scan.m.userData.lift = Math.max(L.scan.m.userData.lift, 0.6);
        placeBeam(L.beam, g.position, L.scanPos);
        L.beamMat.opacity = 0.2 * fade;
        L.mark.position.copy(L.scanPos);
        L.mark.lookAt(camera.position);
        L.mark.scale.setScalar(0.7 + 0.6 * ph);
        L.markMat.opacity = fade * (1 - ph);
        L.beam.visible = L.mark.visible = !reduced;
      }
    }
    // arahan: garis data ke kepala pustakawan & programmer, paket terbang di atasnya
    if (L.cmd) {
      var ct = (clock - L.cmd.t0) / 1000, alive = false;
      L.cmd.tg.forEach(function (tg, j) {
        var lk = L.links[j], lt = ct - j * 0.45;
        if (!lk) return;
        if (lt < 1.6) alive = true;
        var on = lt > 0 && lt < 1.6 && !reduced;
        lk.mesh.visible = on;
        if (!on) {
          lk.dot.visible = false;
          if ((reduced || lt >= 0.8) && !tg.done) { tg.done = true; tg.react(); }
          return;
        }
        tg.obj.getWorldPosition(lk.to);
        lk.to.y += 0.1;
        placeBeam(lk.mesh, g.position, lk.to);
        lk.mat.opacity = 0.55 * Math.min(1, (1.6 - lt) * 3) * Math.min(1, lt * 6);
        var f = clamp(lt / 0.8, 0, 1);
        lk.dot.position.lerpVectors(g.position, lk.to, easeInOutCubic(f));
        lk.dot.visible = f < 1;
        if (f >= 1 && !tg.done) { tg.done = true; tg.react(); }
      });
      if (!alive) { L.cmd = null; L.links.forEach(function (lk) { lk.mesh.visible = lk.dot.visible = false; }); }
    }
  }

  // ---- kata-kata Lucca -------------------------------------------------------
  function luccaText(L) {
    var s = L.st, T0 = today0(), nm = String(opts.userName || '').trim().split(/\s+/)[0] || '';
    var hr = todHour != null ? todHour : new Date().getHours(), ov = s.overdue, ds = s.dueSoon;
    // bahasa Indonesia: tidak ada bentuk jamak, jadi angka langsung diikuti kata bendanya
    var greet = (hr >= 4 && hr < 11 ? 'Selamat pagi' : hr >= 11 && hr < 15 ? 'Selamat siang' : hr >= 15 && hr < 18 ? 'Selamat sore' : 'Selamat malam') + (nm ? ', ' + nm : '') + '.';
    var status = ov ? ov + ' buku terlambat' + (ds ? ' dan ' + ds + ' jatuh tempo dalam 7 hari.' : '.')
      : ds ? 'Tidak ada yang terlambat. ' + ds + ' buku jatuh tempo dalam 7 hari.'
      : s.prog > 1 ? s.prog + ' buku yang sedang dikerjakan semuanya masih sesuai jadwal.' : s.prog ? 'Satu buku yang sedang dikerjakan masih sesuai jadwal.' : 'Tidak ada yang sedang dikerjakan. Rak sedang tenang.';
    var top = s.attn && s.attn[0], topPlain = '', topHtml = '';
    if (top) {
      var td = parseDay(top.target_date), name = String(top.project_name || ''), pic = top.pic ? ' · ' + top.pic : '', when;
      if (+top.is_overdue === 1) {
        var late = td ? daysFrom(td, T0) : 0;
        when = late > 0 ? 'terlambat ' + late + ' hari' : 'lewat tenggat';
        topPlain = 'Paling mendesak: ' + name + ', ' + when + '.';
        topHtml = 'Paling mendesak: <b>' + esc(name) + '</b> &middot; ' + when + esc(pic);
      } else {
        var left = td ? daysFrom(T0, td) : 0;
        when = left <= 0 ? 'jatuh tempo hari ini' : left === 1 ? 'jatuh tempo besok' : 'jatuh tempo ' + left + ' hari lagi';
        topPlain = 'Tenggat berikutnya: ' + name + ', ' + when + '.';
        topHtml = 'Tenggat berikutnya: <b>' + esc(name) + '</b> &middot; ' + when + esc(pic);
      }
    }
    var pct = s.measured ? Math.round(s.onTime / s.measured * 100) : null;
    var deliv = s.doneMonth + ' project selesai bulan ini' + (pct != null ? ', ' + pct + '% tepat waktu secara keseluruhan.' : '.');
    var cmd = top ? (ov ? 'Saya sudah menandainya di meja depan dan memberi tahu programmer.' : 'Saya sudah mengingatkan programmer.') : 'Semua sesuai jadwal. Saya akan terus mengawasi.';
    var statusShort = ov ? '<b>' + ov + '</b> terlambat' + (ds ? ' &middot; <b>' + ds + '</b> jatuh tempo dalam 7 hari' : '') : ds ? 'Tidak ada yang terlambat &middot; <b>' + ds + '</b> jatuh tempo dalam 7 hari' : esc(status);
    var delivShort = '<b>' + s.doneMonth + '</b> selesai bulan ini' + (pct != null ? ' &middot; <b>' + pct + '%</b> tepat waktu' : '');
    var cmdShort = top ? (ov ? 'Sudah ditandai di meja depan &middot; programmer sudah diberi tahu' : 'Programmer sudah diingatkan') : 'Semua sesuai jadwal &middot; terus diawasi';
    var studio = wing && wing.lucca ? wing.lucca() : null;   // mockup & ERD di sudut Design Studio
    return { greet: greet, status: status, top: top, topPlain: topPlain, topHtml: topHtml, deliv: deliv, cmd: cmd, statusShort: statusShort, delivShort: delivShort, cmdShort: cmdShort, studio: studio };
  }
  var luccaTimer = 0;
  function showLucca(lines, acts, mood, ms, say) {
    clearTimeout(newsTimer);
    newsEl.hidden = true;
    luccaEl.className = 'c3-lucca ' + (mood || 'calm');
    luccaEl.querySelector('.c3-lucca-t').innerHTML = lines.map(function (l, k) { return '<p style="--i:' + k + '">' + l + '</p>'; }).join('');
    luccaEl.querySelector('.c3-lucca-a').innerHTML = acts.map(function (a) {
      return '<button type="button" data-lu="' + a[0] + '"' + (a[2] != null ? ' data-id="' + esc(a[2]) + '"' : '') + '>' + a[1] + '</button>';
    }).join('');
    luccaSay = say || '';
    luccaEl.hidden = false;
    host.classList.add('lucca-open');
    clearTimeout(luccaTimer);
    if (ms) luccaTimer = setTimeout(hideLucca, ms);
  }
  var luccaSay = '';
  function hideLucca(stop) {
    clearTimeout(luccaTimer);
    luccaEl.hidden = true;
    host.classList.remove('lucca-open');
    if (stop) stopSpeech();
  }
  function luccaBrief() {
    var L = room && room.lucca;
    if (!L) return;
    var r = luccaText(L), lines = [esc(r.greet), r.statusShort];
    if (r.topHtml) lines.push(r.topHtml);
    if (r.studio && r.studio.line) lines.push(r.studio.line);
    lines.push(r.delivShort, '<em>' + r.cmdShort + '</em>');
    var say = [r.greet, r.status, r.topPlain, r.studio ? r.studio.say : '', r.deliv, r.cmd].join(' ').replace(/\s+/g, ' ');
    var acts = [];
    if (r.top) acts.push(['show', 'Tunjukkan', r.top.id]);
    if (r.studio && r.studio.go) acts.push(['studio', r.studio.label]);
    if (window.speechSynthesis && window.SpeechSynthesisUtterance) acts.push(['speak', 'Bacakan']);
    showLucca(lines, acts, L.mood, 45000, say);
    L.talkUntil = clock + Math.min(9000, say.length * 55);
    L.spin = Math.max(L.spin, 1.5);
    sound.play('lucca');
    luccaCommand(L, !!r.top);
    if (sound.isOn()) speak(say);
    announce('Lucca: ' + say);
  }
  function luccaGreet() {
    var L = room && room.lucca;
    if (!L || walk || tour || taken || intro || !luccaEl.hidden) return;
    var day = String(today0().getTime());
    try {
      if (localStorage.getItem('lib3dLuccaDay') === day) return;
      localStorage.setItem('lib3dLuccaDay', day);
    } catch (e) { /* abaikan */ }
    var r = luccaText(L), acts = [['brief', 'Laporan lengkap']];
    if (r.top) acts.push(['show', 'Tunjukkan', r.top.id]);
    if (r.studio && r.studio.go) acts.push(['studio', r.studio.label]);
    var gl = [esc(r.greet) + ' Saya <em>Lucca</em>, asisten yang menjaga perpustakaan ini.', r.statusShort];
    if (r.studio && r.studio.mood !== 'calm') gl.push(r.studio.line);
    showLucca(gl, acts, L.mood, 14000, r.greet + ' ' + r.status + (r.studio && r.studio.say ? ' ' + r.studio.say : ''));
    L.talkUntil = clock + 2600;
    sound.play('lucca');
    announce('Lucca: ' + r.greet + ' ' + r.status);
  }
  // suara Lucca: suara sintetis bawaan browser (hanya sesudah diklik)
  function speak(text) {
    var ss = window.speechSynthesis;
    if (!ss || !window.SpeechSynthesisUtterance || !text) return;
    try {
      ss.cancel();
      // suara bahasa Indonesia (Windows: Andika/Gadis; Chrome: Google Bahasa Indonesia)
      var u = new SpeechSynthesisUtterance(text), vs = ss.getVoices().filter(function (v) { return /^id[-_]/i.test(v.lang); });
      u.lang = 'id-ID';
      u.rate = 1.0;
      u.pitch = 0.95;
      var pref = vs.filter(function (v) { return /gadis|andika|damayanti|google bahasa indonesia/i.test(v.name); })[0] || vs[0];
      if (pref) u.voice = pref;
      var L = room && room.lucca;
      if (L) L.talkUntil = clock + 60000;
      u.onend = u.onerror = function () { var L2 = room && room.lucca; if (L2) L2.talkUntil = 0; };
      ss.speak(u);
    } catch (e) { /* abaikan */ }
  }
  function stopSpeech() {
    try { if (window.speechSynthesis) window.speechSynthesis.cancel(); } catch (e) { /* abaikan */ }
  }
  luccaEl.addEventListener('click', function (e) {
    var b = e.target.closest('[data-lu]');
    if (!b) return;
    var act = b.getAttribute('data-lu');
    if (act === 'close') hideLucca(true);
    else if (act === 'brief') luccaBrief();
    else if (act === 'speak') speak(luccaSay);
    else if (act === 'studio') { hideLucca(); var sl = wing && wing.lucca ? wing.lucca() : null; if (sl && sl.go) sl.go(); }
    else if (act === 'show') {
      var id = b.getAttribute('data-id');
      hideLucca();
      if (findBook(id, false)) luccaFocus(id);
    }
  });
  luccaEl.addEventListener('mouseenter', function () { clearTimeout(luccaTimer); });
  luccaEl.addEventListener('mouseleave', function () { clearTimeout(luccaTimer); luccaTimer = setTimeout(hideLucca, 8000); });

  // ---- meja kerja programmer (di tengah, di atas karpet) --------------------
  // Dua monitor: kiri = editor kode yang sedang "diketik" untuk project yang
  // sedang dikerjakan (bergantian), kanan = papan daftar project In Progress.
  // PC ber-RGB, keyboard, mouse, kopi beruap, bebek karet & programmernya
  // sendiri (figur sederhana: hoodie, headphone, kacamata) yang mengetik.
  // Ia duduk agak ke kiri supaya dari sudut kamera layar tidak tertutup.
  function codeFor(p) {
    var K = '#c792ea', S = '#c3e88d', F = '#82aaff', C = '#676e95', N = '#f78c6c', T = '#d6deeb', V = '#f07178';
    if (!p) {
      return { file: 'library.js', lines: [[['// Project Library', C]], [['// all caught up — nothing in progress', C]], [['const', K], [' coffee ', T], ['= ', T], ["'refill'", S], [';', T]]] };
    }
    var slug = String(p.project_name || 'project').toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '') || 'project';
    var pr = clamp(parseInt(p.progress, 10) || 0, 0, 100);
    return {
      file: slug.slice(0, 24) + '.php',
      lines: [
        [['<?php', K]],
        [['// ' + String(p.project_name || ''), C]],
        [['$project', V], [' = ', T], ['Project', F], ['::', T], ['find', F], ['(', T], [String(p.id), N], [');', T]],
        [['$project', V], ['->progress', T], [' = ', T], [String(pr), N], [';', T]],
        [['if', K], [' (', T], ['$project', V], ['->', T], ['isOverdue', F], ['()) {', T]],
        [['    notify', F], ['(', T], ["'" + String(p.pic || 'team') + "'", S], [');', T]],
        [['}', T]],
        [['foreach', K], [' (', T], ['$project', V], ['->', T], ['tasks', F], ['() ', T], ['as', K], [' $task', V], [') {', T]],
        [['    $task', V], ['->', T], ['ship', F], ['();', T]],
        [['}', T]],
        [['return', K], [' view', F], ['(', T], ["'library'", S], [', ', T], ['compact', F], ['(', T], ["'project'", S], ['));', T]]
      ]
    };
  }
  function drawCode(dv, cursorOn) {
    var g = dv.codeCtx, w = 512, h = 300, src = dv.src, left = dv.typed, i, j;
    g.fillStyle = '#1e1f29';
    g.fillRect(0, 0, w, h);
    g.fillStyle = '#15161d';
    g.fillRect(0, 0, w, 30);
    g.fillStyle = '#272935';
    g.fillRect(8, 5, Math.min(250, 34 + src.file.length * 7.4), 25);
    g.textBaseline = 'middle';
    g.font = '13px Consolas, "Courier New", monospace';
    g.fillStyle = '#7fdbca';
    g.fillText('●', 16, 18);
    g.fillStyle = '#e6e6e6';
    g.fillText(src.file, 30, 18);
    var lh = 18, y = 46, last = null;
    for (i = 0; i < src.lines.length && left > 0; i++) {
      g.fillStyle = '#4b5263';
      g.fillText(String(i + 1), i + 1 < 10 ? 16 : 8, y);
      var x = 40;
      for (j = 0; j < src.lines[i].length && left > 0; j++) {
        var tk = src.lines[i][j], txt = tk[0].slice(0, left);
        left -= txt.length;
        g.fillStyle = tk[1];
        g.fillText(txt, x, y);
        x += g.measureText(txt).width;
      }
      last = { x: x, y: y };
      y += lh;
    }
    if (cursorOn && last) {
      g.fillStyle = '#ffcc00';
      g.fillRect(last.x + 1, last.y - 8, 7, 16);
    }
    g.fillStyle = '#3b82f6';
    g.fillRect(0, h - 22, w, 22);
    g.fillStyle = '#fff';
    g.font = '12px Arial, sans-serif';
    var p = dv.list[dv.idx];
    g.fillText('⎇ main   ·   ' + (p ? clamp(parseInt(p.progress, 10) || 0, 0, 100) + '% done   ·   ' + String(p.category || 'General') : 'idle'), 10, h - 11);
    dv.codeTex.needsUpdate = true;
  }
  function drawBoard(dv) {
    var g = dv.boardCtx, w = 512, h = 300, list = dv.list;
    g.fillStyle = '#0f172a';
    g.fillRect(0, 0, w, h);
    g.textBaseline = 'middle';
    g.fillStyle = '#e2e8f0';
    g.font = '700 16px Arial, sans-serif';
    g.fillText('IN PROGRESS', 16, 24);
    g.fillStyle = '#3b82f6';
    roundRect(g, 136, 13, 30, 22, 6);
    g.fill();
    g.fillStyle = '#fff';
    g.font = '700 13px Arial, sans-serif';
    g.textAlign = 'center';
    g.fillText(String(list.length), 151, 24);
    g.textAlign = 'left';
    if (!list.length) {
      g.fillStyle = '#94a3b8';
      g.font = 'italic 15px Arial, sans-serif';
      g.fillText('Nothing in progress right now', 16, 80);
    }
    list.slice(0, 6).forEach(function (p, k) {
      var y = 52 + k * 38, on = k === dv.idx, pr = clamp(parseInt(p.progress, 10) || 0, 0, 100), over = +p.is_overdue === 1;
      if (on) {
        g.fillStyle = '#1e293b';
        g.fillRect(8, y, w - 16, 34);
        g.fillStyle = '#3b82f6';
        g.fillRect(8, y, 4, 34);
      }
      g.fillStyle = laneColors[p.category || 'General'] || '#94a3b8';
      g.fillRect(20, y + 11, 8, 12);
      g.fillStyle = '#e2e8f0';
      g.font = (on ? '700 ' : '') + '13px Arial, sans-serif';
      var name = String(p.project_name || '');
      while (name.length > 4 && g.measureText(name).width > 250) name = name.slice(0, -2);
      if (name !== String(p.project_name || '')) name += '…';
      g.fillText(name, 36, y + 17);
      g.fillStyle = '#334155';
      g.fillRect(300, y + 13, 140, 8);
      g.fillStyle = over ? '#ef4444' : '#22c55e';
      g.fillRect(300, y + 13, 140 * pr / 100, 8);
      g.fillStyle = over ? '#fca5a5' : '#cbd5e1';
      g.font = '12px Arial, sans-serif';
      g.fillText(pr + '%', 450, y + 17);
    });
    dv.boardTex.needsUpdate = true;
  }
  function buildDev(o) {
    var dv = room.dev = { list: o.progP, idx: 0, typed: 0, hold: 0, tick: 0, blink: false, waveT: null, quackT: null, steam: [] };
    function mk(geo, mat, x, y, z) { var m = new THREE.Mesh(geo, mat); m.position.set(x || 0, y || 0, z || 0); return m; }
    function box(w, h, d, mat, x, y, z) { var m = mk(unitBox, mat, x, y, z); m.scale.set(w, h, d); return m; }
    var DL = 2.0, DW = 0.9, DH = 0.76;
    var ws = new THREE.Group();
    ws.position.set(o.rx, 0, o.rz - 0.1);
    lib.add(ws);

    // meja (kayu perpustakaan, model meja kerja modern)
    var dk = new THREE.Mesh(track(boxesGeometry([
      { w: DL, h: 0.05, d: DW, x: 0, y: DH - 0.025, z: 0 },
      { w: 0.05, h: DH - 0.05, d: DW - 0.06, x: -(DL / 2 - 0.04), y: (DH - 0.05) / 2, z: 0, v: 1 },
      { w: 0.05, h: DH - 0.05, d: DW - 0.06, x: DL / 2 - 0.04, y: (DH - 0.05) / 2, z: 0, v: 1 },
      { w: DL - 0.1, h: 0.4, d: 0.03, x: 0, y: 0.45, z: -(DW / 2 - 0.05) }
    ])), woodMat);
    dk.castShadow = dk.receiveShadow = true;
    dk.userData = { kind: 'table', ci: -1 };
    pickables.push(dk);
    ws.add(dk);
    var mat = mk(planeGeo, feltMat, -0.05, DH + 0.002, 0.15);
    mat.rotation.x = -Math.PI / 2;
    mat.scale.set(1.0, 0.38, 1);
    ws.add(mat);

    // dua monitor (layar = kanvas yang digambar ulang)
    function canvas512() { var c = document.createElement('canvas'); c.width = 512; c.height = 300; return c; }
    var cc = canvas512(), bc = canvas512();
    dv.codeCtx = cc.getContext('2d');
    dv.boardCtx = bc.getContext('2d');
    dv.codeTex = track(new THREE.CanvasTexture(cc));
    dv.boardTex = track(new THREE.CanvasTexture(bc));
    dv.codeTex.colorSpace = dv.boardTex.colorSpace = THREE.SRGBColorSpace;
    dv.codeTex.anisotropy = dv.boardTex.anisotropy = aniso;
    function monitor(x, ry, tex) {
      var mg = new THREE.Group();
      mg.position.set(x, DH, -0.18);
      mg.rotation.y = ry;
      ws.add(mg);
      var bez = box(0.64, 0.38, 0.025, darkMetalMat, 0, 0.34, -0.01);
      bez.castShadow = true;
      var scr = mk(planeGeo, track(new THREE.MeshBasicMaterial({ map: tex, toneMapped: false })), 0, 0.34, 0.0035);
      scr.scale.set(0.61, 0.345, 1);
      mg.add(box(0.22, 0.015, 0.16, darkMetalMat, 0, 0.0075, -0.04), box(0.04, 0.17, 0.03, darkMetalMat, 0, 0.09, -0.05), bez, scr);
      [bez, scr].forEach(function (m) { m.userData = { kind: 'screen', ci: -1 }; pickables.push(m); });
      return { group: mg, bezel: bez };
    }
    var monL = monitor(-0.37, 0.16, dv.codeTex);
    monitor(0.27, -0.16, dv.boardTex);
    dv.bezel = monL.bezel;
    // catatan tempel di bingkai monitor kiri
    [[0.25, 0.2, -0.08], [0.29, 0.21, 0.12]].forEach(function (s) {
      var n = mk(planeGeo, stickyMat, s[0], s[1], 0.0045);
      n.scale.set(0.05, 0.05, 1);
      n.rotation.z = s[2];
      monL.group.add(n);
    });

    // keyboard, mouse
    var kb = box(0.44, 0.022, 0.14, darkMetalMat, -0.05, DH + 0.011, 0.14);
    var keys = mk(planeGeo, keysMat, -0.05, DH + 0.0225, 0.14);
    keys.rotation.x = -Math.PI / 2;
    keys.scale.set(0.42, 0.12, 1);
    var mouse = mk(track(new THREE.SphereGeometry(1, 16, 10)), darkMetalMat, 0.32, DH + 0.012, 0.15);
    mouse.scale.set(0.032, 0.02, 0.055);
    ws.add(kb, keys, mouse);

    // PC dgn kipas RGB di ujung kanan meja
    var pc = new THREE.Group();
    pc.position.set(0.78, DH, -0.1);
    ws.add(pc);
    var pcBody = box(0.21, 0.45, 0.44, darkMetalMat, 0, 0.225, 0);
    pcBody.castShadow = true;
    pcBody.userData = { kind: 'pc', ci: -1 };
    pickables.push(pcBody);
    var glass = mk(planeGeo, pcGlassMat, 0.1065, 0.225, 0);
    glass.rotation.y = Math.PI / 2;
    glass.scale.set(0.4, 0.41, 1);
    var strip = mk(planeGeo, rgbMat, 0.1, 0.225, -0.15);
    strip.rotation.y = Math.PI / 2;
    strip.scale.set(0.02, 0.36, 1);
    pc.add(pcBody, glass, strip, mk(track(new THREE.SphereGeometry(0.006, 8, 6)), rgbMat, 0.06, 0.43, 0.221));
    var fanG = track(new THREE.TorusGeometry(0.055, 0.009, 6, 24));
    [0.1, 0.225, 0.35].forEach(function (y) { pc.add(mk(fanG, rgbMat, 0, y, 0.222)); });

    // kopi panas (beruap) & bebek karet di atas dua buku
    var mug = new THREE.Group();
    mug.position.set(-0.6, DH, 0.12);
    ws.add(mug);
    var handle = mk(track(new THREE.TorusGeometry(0.024, 0.007, 6, 14)), porcelainMat, 0.044, 0.05, 0);
    mug.add(mk(track(new THREE.CylinderGeometry(0.04, 0.036, 0.09, 18)), porcelainMat, 0, 0.045, 0), mk(track(new THREE.CircleGeometry(0.036, 18)), coffeeMat, 0, 0.083, 0), handle);
    mug.children[1].rotation.x = -Math.PI / 2;
    for (var sk = 0; sk < 3; sk++) {
      var sm = track(new THREE.SpriteMaterial({ map: glowTex, color: 0xffffff, transparent: true, opacity: 0, depthWrite: false }));
      var sp = new THREE.Sprite(sm);
      sp.position.set(0, 0.1, 0);
      mug.add(sp);
      dv.steam.push(sp);
    }
    var bk1 = box(0.24, 0.04, 0.17, track(new THREE.MeshStandardMaterial({ color: 0x1f3b6a, roughness: 0.6 })), -0.78, DH + 0.02, -0.12);
    var bk2 = box(0.22, 0.035, 0.16, track(new THREE.MeshStandardMaterial({ color: 0x7a2a22, roughness: 0.6 })), -0.78, DH + 0.0575, -0.12);
    bk1.rotation.y = 0.1;
    bk2.rotation.y = -0.15;
    ws.add(bk1, bk2);
    var duck = new THREE.Group();
    duck.position.set(-0.78, DH + 0.075, -0.12);
    duck.rotation.y = Math.PI + 0.5;
    ws.add(duck);
    var dBody = mk(track(new THREE.SphereGeometry(0.05, 16, 12)), duckMat, 0, 0.03, 0);
    dBody.scale.set(1.0, 0.75, 1.25);
    var dHead = mk(track(new THREE.SphereGeometry(0.032, 14, 10)), duckMat, 0, 0.085, -0.035);
    var beak = mk(track(new THREE.ConeGeometry(0.013, 0.032, 10)), beakMat, 0, 0.08, -0.072);
    beak.rotation.x = -Math.PI / 2;
    var eyeG = track(new THREE.SphereGeometry(0.005, 6, 4));
    duck.add(dBody, dHead, beak, mk(eyeG, eyeMat, 0.016, 0.095, -0.058), mk(eyeG, eyeMat, -0.016, 0.095, -0.058));
    [dBody, dHead].forEach(function (m) { m.userData = { kind: 'duck', ci: -1 }; pickables.push(m); });
    dv.duck = duck;

    // lampu baca hijau tetap ada di ujung kiri (sentuhan perpustakaan)
    var lp = new THREE.Group();
    lp.position.set(-0.86, DH, -0.3);
    ws.add(lp);
    var lsh = mk(track(new THREE.CylinderGeometry(0.08, 0.08, 0.36, 20, 1, true, 0, Math.PI)), glassMat, 0, 0.33, 0.05);
    lsh.rotation.z = Math.PI / 2;
    var lpool = mk(planeGeo, poolMat, 0.05, 0.004, 0.22);
    lpool.rotation.x = -Math.PI / 2;
    lpool.scale.set(0.9, 0.7, 1);
    lp.add(mk(track(new THREE.CylinderGeometry(0.075, 0.09, 0.03, 20)), brassMat, 0, 0.015, 0), mk(track(new THREE.CylinderGeometry(0.012, 0.012, 0.3, 8)), brassMat, 0, 0.17, 0), lsh,
      mk(track(new THREE.BoxGeometry(0.28, 0.012, 0.05)), bulbMat, 0, 0.312, 0.05), lpool);

    // kursi kantor
    var ch = new THREE.Group();
    ch.position.set(-0.1, 0, 0.62);
    ws.add(ch);
    for (var lg = 0; lg < 5; lg++) {
      var la = lg / 5 * Math.PI * 2, leg = box(0.05, 0.03, 0.3, darkMetalMat, Math.sin(la) * 0.15, 0.07, Math.cos(la) * 0.15);
      leg.rotation.y = la;
      ch.add(leg, mk(track(new THREE.SphereGeometry(0.026, 8, 6)), darkMetalMat, Math.sin(la) * 0.29, 0.03, Math.cos(la) * 0.29));
    }
    var seat = box(0.5, 0.08, 0.48, chairFabMat, 0, 0.46, 0);
    var back = box(0.46, 0.5, 0.06, chairFabMat, 0, 0.78, 0.24);
    back.rotation.x = 0.12;
    seat.castShadow = back.castShadow = true;
    ch.add(mk(track(new THREE.CylinderGeometry(0.025, 0.025, 0.32, 10)), darkMetalMat, 0, 0.26, 0), seat, back,
      box(0.05, 0.2, 0.05, darkMetalMat, -0.27, 0.58, 0.02), box(0.05, 0.2, 0.05, darkMetalMat, 0.27, 0.58, 0.02),
      box(0.07, 0.03, 0.28, chairFabMat, -0.27, 0.69, -0.02), box(0.07, 0.03, 0.28, chairFabMat, 0.27, 0.69, -0.02));

    // programmernya: menghadap monitor (punggung ke kamera)
    var pp = makePerson({ top: hoodieMat, bottom: jeansMat, shoes: shoeMat, skin: skinMat, hair: hairMat, hood: true, glasses: true, headphones: true });
    pp.root.position.set(-0.1, 0, 0.62);
    ws.add(pp.root);
    dv.body = pp.root;
    dv.torso = pp.torso;
    dv.head = pp.head;
    dv.shL = pp.shL; dv.shR = pp.shR; dv.elL = pp.elL; dv.elR = pp.elR;
    [pp.torso, pp.headMesh, pp.hair].forEach(function (m) { m.userData = { kind: 'dev', ci: -1 }; pickables.push(m); });
    addObs(ws, 0, 0, 1.15);
    addObs(ws, -0.1, 0.62, 0.45);

    // cahaya kebiruan dari layar
    ws.updateMatrixWorld(true);
    screenLight.position.copy(ws.localToWorld(new THREE.Vector3(-0.05, DH + 0.35, 0.25)));
    nextProject(dv, 0);
    drawBoard(dv);
  }
  // Project berikutnya di layar kiri (berganti sesudah selesai "diketik")
  function nextProject(dv, idx) {
    dv.idx = dv.list.length ? idx % Math.min(dv.list.length, 6) : 0;
    dv.src = codeFor(dv.list[dv.idx]);
    dv.total = dv.src.lines.reduce(function (s, l) { return s + l.reduce(function (t, k) { return t + k[0].length; }, 0); }, 0);
    dv.typed = reduced ? dv.total : 0;
    dv.hold = 0;
    drawCode(dv, true);
  }
  function featured() { var dv = room && room.dev; return dv ? dv.list[dv.idx] : null; }
  function updateDev(dv, dt, now) {
    var ts = clock / 1000;
    // mengetik / melambai
    if (dv.cheerT != null) {
      // bersorak: dua tangan ke atas, badan melompat kecil
      var c2 = (clock - dv.cheerT) / 1000, cu = Math.min(1, c2 * 4) * Math.min(1, Math.max(0, (2.6 - c2) * 3));
      dv.shR.rotation.set(0.6 * (1 - cu), 0.22 * (1 - cu), 2.6 * cu, 'YXZ');
      dv.shL.rotation.set(0.6 * (1 - cu), -0.22 * (1 - cu), -2.6 * cu, 'YXZ');
      dv.elR.rotation.x = dv.elL.rotation.x = 1.0 * (1 - cu) + cu * (0.25 + 0.2 * Math.sin(c2 * 14));
      dv.body.position.y = 0.05 * Math.abs(Math.sin(c2 * 9)) * cu;
      if (c2 > 2.6) {
        dv.cheerT = null;
        dv.shR.rotation.set(0.6, 0.22, 0, 'YXZ');
        dv.shL.rotation.set(0.6, -0.22, 0, 'YXZ');
        dv.body.position.y = 0;
      }
    } else if (dv.waveT != null) {
      var wt = (clock - dv.waveT) / 1000, up = Math.min(1, wt * 4) * Math.min(1, Math.max(0, (1.9 - wt) * 4));
      dv.shR.rotation.set(0.6 * (1 - up), 0.22 * (1 - up), 2.5 * up, 'YXZ');
      dv.elR.rotation.x = 1.0 * (1 - up) + up * (0.35 + 0.4 * Math.sin(wt * 12));
      dv.elL.rotation.x = 1.0;
      dv.head.rotation.y = -1.0 * up;
      if (wt > 1.9) { dv.waveT = null; dv.shR.rotation.set(0.6, 0.22, 0, 'YXZ'); }
    } else {
      dv.elL.rotation.x = 1.0 + 0.07 * Math.max(0, Math.sin(ts * 17));
      dv.elR.rotation.x = 1.0 + 0.07 * Math.max(0, Math.sin(ts * 17 + 2.1));
      dv.head.rotation.y = (Math.sin(ts * 0.13) > 0.55 ? 0.32 : 0) * Math.min(1, Math.abs(Math.sin(ts * 0.13)) * 2);
    }
    dv.head.rotation.x = 0.04 * Math.sin(ts * 0.8);
    dv.torso.scale.y = 1 + 0.012 * Math.sin(ts * 1.6);
    // uap kopi
    for (var k = 0; k < dv.steam.length; k++) {
      var f = (ts * 0.28 + k / 3) % 1, sp = dv.steam[k];
      sp.position.y = 0.1 + f * 0.2;
      sp.position.x = Math.sin(ts * 1.3 + k * 2) * 0.012;
      sp.scale.setScalar(0.04 + f * 0.09);
      sp.material.opacity = 0.32 * Math.sin(f * Math.PI);
    }
    // bebek "mencicit" (memantul)
    if (dv.quackT != null) {
      var qt = (clock - dv.quackT) / 260;
      dv.duck.scale.set(1 + 0.15 * Math.sin(Math.min(1, qt) * Math.PI), 1 - 0.22 * Math.sin(Math.min(1, qt) * Math.PI), 1);
      if (qt >= 1) { dv.quackT = null; dv.duck.scale.set(1, 1, 1); }
    }
    // kode diketik sedikit demi sedikit; kursor berkedip
    var step = perf.lvl ? 600 : 260;
    if (now - dv.tick > step) {
      dv.tick = now;
      dv.blink = !dv.blink;
      if (dv.typed < dv.total) {
        dv.typed = Math.min(dv.total, dv.typed + 3 + Math.floor(Math.random() * 6));
        if (dv.cheerT == null) sound.play('key');   // ketikan pelan (kalau suara nyala)
      }
      else if (!dv.hold) dv.hold = now;
      else if (now - dv.hold > 3500 && dv.list.length > 1) { nextProject(dv, dv.idx + 1); drawBoard(dv); return; }
      drawCode(dv, dv.blink);
    }
  }

  // ---- figur orang sederhana (duduk, menghadap -z) ---------------------------
  // Dipakai programmer & pustakawan. cfg: bahan (top/bottom/shoes/skin/hair)
  // & ciri (hood, bun, glasses, smile, headphones). Kembalian: bagian yang
  // dianimasikan (bahu/siku kiri-kanan, kepala, badan).
  function makePerson(cfg) {
    var P = {}, pe = new THREE.Group();
    function mk(geo, mat, x, y, z) { var m = new THREE.Mesh(geo, mat); m.position.set(x || 0, y || 0, z || 0); return m; }
    function box(w, h, d, mat, x, y, z) { var m = mk(unitBox, mat, x, y, z); m.scale.set(w, h, d); return m; }
    function cap(r, len) { return track(new THREE.CapsuleGeometry(r, len, 4, 12)); }
    P.root = pe;
    pe.add(box(0.34, 0.14, 0.3, cfg.bottom, 0, 0.57, 0.04));
    [-1, 1].forEach(function (sx) {
      var th = mk(cap(0.068, 0.26), cfg.bottom, sx * 0.095, 0.6, -0.14);
      th.rotation.x = Math.PI / 2;
      pe.add(th, mk(cap(0.055, 0.3), cfg.bottom, sx * 0.095, 0.34, -0.32), box(0.1, 0.07, 0.22, cfg.shoes, sx * 0.095, 0.035, -0.38));
    });
    var torso = mk(cap(0.15, 0.26), cfg.top, 0, 0.83, 0.07);
    torso.scale.set(1.25, 1, 0.8);
    torso.rotation.x = -0.1;
    torso.castShadow = true;
    var collar = mk(track(new THREE.TorusGeometry(cfg.hood ? 0.095 : 0.075, cfg.hood ? 0.035 : 0.022, 8, 18)), cfg.top, 0, 1.03, cfg.hood ? 0.1 : 0.05);
    collar.rotation.x = Math.PI / 2 - 0.3;
    pe.add(torso, collar);
    P.torso = torso;
    function arm(sx) {
      var sh = new THREE.Group();
      sh.position.set(sx * 0.19, 1.0, 0.05);
      sh.rotation.set(0.6, sx * 0.22, 0, 'YXZ');
      pe.add(sh);
      var ua = track(new THREE.CapsuleGeometry(0.048, 0.18, 4, 10));
      ua.translate(0, -0.138, 0);
      sh.add(mk(ua, cfg.top));
      var el = new THREE.Group();
      el.position.y = -0.26;
      el.rotation.x = 1.0;
      sh.add(el);
      var fa = track(new THREE.CapsuleGeometry(0.042, 0.18, 4, 10));
      fa.translate(0, -0.13, 0);
      el.add(mk(fa, cfg.top), box(0.075, 0.09, 0.032, cfg.skin, 0, -0.3, 0));
      return { sh: sh, el: el };
    }
    var aL = arm(-1), aR = arm(1);
    P.shL = aL.sh; P.shR = aR.sh; P.elL = aL.el; P.elR = aR.el;
    var hd = new THREE.Group();
    hd.position.set(0, 1.1, 0.03);
    pe.add(hd);
    var headM = mk(track(new THREE.SphereGeometry(0.1, 20, 16)), cfg.skin, 0, 0.1, -0.01);
    var hair = mk(track(new THREE.SphereGeometry(0.106, 20, 12, 0, Math.PI * 2, 0, Math.PI * 0.58)), cfg.hair, 0, 0.105, 0);
    hair.rotation.x = 0.35;
    var eye = track(new THREE.SphereGeometry(0.009, 8, 6));
    hd.add(mk(track(new THREE.CylinderGeometry(0.045, 0.05, 0.08, 12)), cfg.skin), headM, hair, mk(eye, eyeMat, -0.036, 0.11, -0.1), mk(eye, eyeMat, 0.036, 0.11, -0.1));
    if (cfg.bun) hd.add(mk(track(new THREE.SphereGeometry(0.052, 14, 10)), cfg.hair, 0, 0.18, 0.07));
    if (cfg.glasses) {
      var rimG = track(new THREE.TorusGeometry(0.026, 0.004, 6, 16));
      hd.add(mk(rimG, eyeMat, -0.038, 0.108, -0.104), mk(rimG, eyeMat, 0.038, 0.108, -0.104), box(0.03, 0.005, 0.005, eyeMat, 0, 0.11, -0.106));
    }
    if (cfg.smile) {
      var sm = mk(track(new THREE.TorusGeometry(0.03, 0.005, 6, 12, Math.PI)), smileMat, 0, 0.07, -0.094);
      sm.rotation.z = Math.PI;
      hd.add(sm);
    }
    if (cfg.headphones) {
      var cupG = track(new THREE.CylinderGeometry(0.045, 0.045, 0.035, 16));
      var cupL = mk(cupG, phoneMat, -0.107, 0.09, -0.01), cupR = mk(cupG, phoneMat, 0.107, 0.09, -0.01);
      cupL.rotation.z = cupR.rotation.z = Math.PI / 2;
      hd.add(mk(track(new THREE.TorusGeometry(0.113, 0.012, 6, 20, Math.PI)), phoneMat, 0, 0.1, -0.01), cupL, cupR);
    }
    P.head = hd;
    P.headMesh = headM;
    P.hair = hair;
    return P;
  }

  // Tanggal (1..31) yang ditunjuk di kalender dinding, dari koordinat uv
  function calDay(uv) {
    var ci = corner && corner.cal;
    if (!ci || !uv) return 0;
    var px = uv.x * CAL.w, py = (1 - uv.y) * CAL.h;
    var col = Math.floor((px - CAL.gx) / CAL.cw), row = Math.floor((py - CAL.gy) / CAL.ch);
    if (col < 0 || col > 6 || row < 0 || row > 5) return 0;
    var dn = row * 7 + col - ci.off + 1;
    return dn >= 1 && dn <= ci.days ? dn : 0;
  }
  function calTip(uv) {
    var ci = corner.cal, dn = calDay(uv), mo = MONTHS[ci.m - 1];
    if (dn && ci.due[dn]) {
      var list = ci.due[dn];
      return tipHead('#d0312d', dn + ' ' + mo + ' · due', list.length + (list.length === 1 ? ' project' : ' projects')) +
        '<div class="c3-tip-sub">' + list.slice(0, 4).map(function (p) { return esc(p.project_name); }).join('<br>') + (list.length > 4 ? '<br>&hellip;' : '') + '</div>' +
        '<div class="c3-tip-go">Click to find ' + (list.length === 1 ? 'it' : 'the first one') + '</div>';
    }
    if (dn && ci.done[dn]) {
      return tipHead('#10b981', dn + ' ' + mo + ' · delivered', ci.done[dn].map(function (p) { return esc(p.project_name); }).slice(0, 3).join(', '));
    }
    return tipHead('#d0312d', 'Wall calendar', FULL_MONTHS[ci.m - 1] + ' ' + ci.y) +
      '<div class="c3-tip-sub">' + ci.dueCount + (ci.dueCount === 1 ? ' deadline' : ' deadlines') + ' this month (red circles) &middot; green = delivered that day</div>';
  }

  // ---- kabar langsung: perubahan dari rekan (dipanggil index.php) -----------
  // Lonceng berbunyi, catatan singkat muncul, dan bukunya terbang ke raknya.
  var newsTimer = 0;
  function news(items) {
    if (!items || !items.length) return;
    newsEl.querySelector('.c3-news-list').innerHTML = items.slice(0, 3).map(function (it) {
      return '<button type="button" class="c3-news-it" data-id="' + esc(it.id) + '"><b>' + esc(it.title) + '</b><small>' + esc(it.text) + '</small></button>';
    }).join('');
    hideLucca(false);
    newsEl.className = 'c3-news ' + (room && room.lucca ? room.lucca.mood : 'calm');   // mata Lucca ikut warna kondisi
    newsEl.hidden = false;
    clearTimeout(newsTimer);
    newsTimer = setTimeout(function () { newsEl.hidden = true; }, 9000);
    ringBell();
    flyIn(items.map(function (it) { return it.id; }));
    luccaFocus(items[0].id);   // Lucca terbang ke raknya & memindai bukunya
    announce('Library update: ' + items.map(function (it) { return it.title + ', ' + it.text; }).join('. '));
  }
  newsEl.addEventListener('click', function (e) {
    var b = e.target.closest('[data-id]');
    if (b) { newsEl.hidden = true; findBook(b.getAttribute('data-id'), false); }
  });
  newsEl.addEventListener('mouseenter', function () { clearTimeout(newsTimer); });
  newsEl.addEventListener('mouseleave', function () { newsTimer = setTimeout(function () { newsEl.hidden = true; }, 3000); });
  function flyIn(ids) {
    if (reduced) return;
    var from = homeTarget.clone().add(new THREE.Vector3(Math.sin(view.theta) * 6, 2.6, Math.cos(view.theta) * 6)), k = 0;
    ids.forEach(function (id) {
      var m = shelfById[String(id)];
      if (!m) return;
      var u = m.userData;
      u.fly = { t0: clock + 500 + k * 350, from: u.g.parent.worldToLocal(from.clone()) };
      k++;
    });
  }

  // ---- perayaan: project jadi Done/Live ---------------------------------
  // Programmer bersorak, confetti berjatuhan di ruangan, fanfare (kalau suara
  // nyala). Buku yang selesai hari ini juga diberi pita emas (lihat build).
  var fxs = [];
  function celebrate() {
    if (room && room.dev) room.dev.cheerT = clock;
    if (room && room.lucca) { room.lucca.goldT = clock; room.lucca.spin = 6; }
    if (corner && corner.lib) corner.lib.clapT = clock;
    sound.play('fanfare');
    if (reduced) return;
    var N = coarse ? 110 : 180, pos = new Float32Array(N * 3), col = new Float32Array(N * 3), vel = [];
    var c0 = room ? room.center : homeTarget, pal = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#a855f7', '#e9c977'].map(function (c) { return new THREE.Color(c); });
    for (var i = 0; i < N; i++) {
      var a = Math.random() * Math.PI * 2, rr = Math.random() * 2.2;
      pos[i * 3] = c0.x + Math.cos(a) * rr;
      pos[i * 3 + 1] = 3.4 + Math.random() * 1.8;
      pos[i * 3 + 2] = c0.z + Math.sin(a) * rr * 0.7;
      var pc = pal[i % pal.length];
      col[i * 3] = pc.r; col[i * 3 + 1] = pc.g; col[i * 3 + 2] = pc.b;
      vel.push({ vy: -(0.7 + Math.random() * 0.9), ph: Math.random() * 6.28, sw: 0.2 + Math.random() * 0.4 });
    }
    var geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    geo.setAttribute('color', new THREE.BufferAttribute(col, 3));
    var mat = new THREE.PointsMaterial({ size: 0.075, vertexColors: true, transparent: true, depthWrite: false });
    var pts = new THREE.Points(geo, mat);
    pts.frustumCulled = false;
    scene.add(pts);
    fxs.push({ pts: pts, vel: vel, t0: clock });
  }
  function updateFx(dt) {
    for (var f = fxs.length - 1; f >= 0; f--) {
      var fx = fxs[f], age = (clock - fx.t0) / 1000, a = fx.pts.geometry.attributes.position.array;
      for (var i = 0; i < fx.vel.length; i++) {
        var v = fx.vel[i];
        if (a[i * 3 + 1] > 0.02) {
          a[i * 3 + 1] = Math.max(0.02, a[i * 3 + 1] + v.vy * dt);
          a[i * 3] += Math.sin(age * 3 + v.ph) * v.sw * dt;
        }
      }
      fx.pts.geometry.attributes.position.needsUpdate = true;
      fx.pts.material.opacity = Math.min(1, Math.max(0, (6 - age) / 1.5));
      if (age > 6) {
        scene.remove(fx.pts);
        fx.pts.geometry.dispose();
        fx.pts.material.dispose();
        fxs.splice(f, 1);
      }
    }
  }

  // ---- layar penuh (presentasi & jalan-jalan) -------------------------------
  // Panggung dipindah ke <body> selama layar penuh: di dalam hero ia berada
  // di bawah kartu & bilah atas halaman, walau posisinya fixed.
  function enterImmersive(cls) {
    presentHome = { parent: host.parentNode, next: host.nextSibling };
    document.body.appendChild(host);
    presenting = true;
    host.classList.add('presenting');
    if (cls) host.classList.add(cls);
    document.documentElement.classList.add('c3-presenting');
    var de = document.documentElement;
    if (de.requestFullscreen && !document.fullscreenElement) {
      try { var rq = de.requestFullscreen(); if (rq && rq.catch) rq.catch(function () { /* abaikan */ }); } catch (e) { /* abaikan */ }
    }
    resize(true);
    try { host.focus({ preventScroll: true }); } catch (e) { host.focus(); }
  }
  function exitImmersive() {
    presenting = false;
    host.classList.remove('presenting', 'walking');
    document.documentElement.classList.remove('c3-presenting');
    if (presentHome && presentHome.parent) {
      var nx = presentHome.next && presentHome.next.parentNode === presentHome.parent ? presentHome.next : null;
      presentHome.parent.insertBefore(host, nx);
    }
    presentHome = null;
    if (document.fullscreenElement && document.exitFullscreen) {
      try { var rq = document.exitFullscreen(); if (rq && rq.catch) rq.catch(function () { /* abaikan */ }); } catch (e) { /* abaikan */ }
    }
    resize(true);
  }

  // ---- mode jalan-jalan (orang pertama) -------------------------------------
  // WASD / panah atas-bawah = maju-mundur, A D = geser, panah kiri-kanan =
  // menoleh, Shift = lari, seret = melihat sekeliling. Di HP: joystick kiri
  // bawah + seret layar. Tidak bisa menembus rak & perabot (lingkaran kasar).
  function startWalk() {
    if (walk || tour || !built) return;
    if (intro) endIntro();
    closeCatalog(false);
    closeMenu(false);
    clearFound();
    setHover(null);
    var c = room ? room.center : homeTarget, p = new THREE.Vector3(c.x + 0.8, 1.6, c.z + 3.6);
    walk = { pos: p, yaw: Math.atan2(p.x - homeTarget.x, p.z - homeTarget.z), pitch: 0.04, keys: {}, joy: { x: 0, y: 0 }, bob: 0 };
    walkCollide(walk.pos);
    camera.fov = 62;
    camera.near = 0.05;
    camera.rotation.order = 'YXZ';
    canvas.style.touchAction = 'none';
    walkEl.hidden = false;
    if (room && room.walkOnly) room.walkOnly.visible = true;   // plafon, dinding depan & dinding samping utuh
    enterImmersive('walking');
    announce('Walking mode. W A S D or the arrow keys to move, drag to look around, Escape to leave.');
  }
  function stopWalk() {
    if (!walk) return;
    walk = null;
    walkEl.hidden = true;
    if (room && room.walkOnly) room.walkOnly.visible = false;
    joyKnob.style.transform = '';
    canvas.style.touchAction = '';
    camera.fov = 32;
    camera.near = 0.1;
    camera.updateProjectionMatrix();
    exitImmersive();
    resetView();
    announce('Walking mode ended.');
  }
  function walkCollide(p) {
    // tidak menembus rak (lengkung belakang) maupun dinding samping & depan
    var dx = p.x, dz = p.z - ARC_R, d = Math.hypot(dx, dz), a = Math.atan2(dx, -dz);
    d = Math.min(d, ARC_R - CD / 2 - 0.55);
    p.x = clamp(Math.sin(a) * d, walkLim.x0, walkLim.x1);
    p.z = Math.min(ARC_R - Math.cos(a) * d, walkLim.z1);
    for (var i = 0; i < walkObs.length; i++) {
      var o = walkObs[i], ox = p.x - o.x, oz = p.z - o.z, od = Math.hypot(ox, oz);
      if (od < o.r) {
        if (od < 1e-4) { ox = 1; oz = 0; od = 1; }
        p.x = o.x + ox / od * o.r;
        p.z = o.z + oz / od * o.r;
      }
    }
  }
  function updateWalk(dt) {
    var w = walk, k = w.keys;
    var fwd = (k.KeyW || k.ArrowUp ? 1 : 0) - (k.KeyS || k.ArrowDown ? 1 : 0) - w.joy.y;
    var str = (k.KeyD ? 1 : 0) - (k.KeyA ? 1 : 0) + w.joy.x;
    w.yaw += ((k.ArrowLeft ? 1 : 0) - (k.ArrowRight ? 1 : 0)) * dt * 1.8;
    var len = Math.hypot(fwd, str);
    if (len > 1) { fwd /= len; str /= len; }
    var sp = (k.ShiftLeft || k.ShiftRight ? 3.4 : 1.8) * dt, sy = Math.sin(w.yaw), cy = Math.cos(w.yaw);
    w.pos.x += (-sy * fwd + cy * str) * sp;
    w.pos.z += (-cy * fwd - sy * str) * sp;
    walkCollide(w.pos);
    if (len > 0.05 && !reduced) w.bob += dt * 9;
    camera.position.set(w.pos.x, 1.6 + Math.sin(w.bob) * 0.025, w.pos.z);
    camera.rotation.set(w.pitch, w.yaw, 0, 'YXZ');
    camera.updateMatrixWorld();
  }
  function onWalkKey(e) {
    if (!walk) return;
    var tg = e.target;
    if (tg && /^(INPUT|TEXTAREA|SELECT)$/.test(tg.tagName)) return;
    if (/^(Key[WASD]|Arrow(Up|Down|Left|Right)|Shift(Left|Right))$/.test(e.code)) {
      walk.keys[e.code] = e.type === 'keydown';
      e.preventDefault();
    }
  }
  function onWalkBlur() { if (walk) walk.keys = {}; }
  window.addEventListener('keydown', onWalkKey);
  window.addEventListener('keyup', onWalkKey);
  window.addEventListener('blur', onWalkBlur);
  walkEl.addEventListener('click', function (e) { if (e.target.closest('[data-walk="exit"]')) stopWalk(); });
  // joystick (layar sentuh)
  var joyKnob = joyEl.firstChild, joyId = null;
  function joyMove(e) {
    var r = joyEl.getBoundingClientRect(), R = r.width / 2, dx = (e.clientX - r.left - R) / R, dy = (e.clientY - r.top - R) / R, l = Math.hypot(dx, dy);
    if (l > 1) { dx /= l; dy /= l; }
    if (walk) walk.joy = { x: dx, y: dy };
    joyKnob.style.transform = 'translate(' + (dx * R * 0.6).toFixed(1) + 'px,' + (dy * R * 0.6).toFixed(1) + 'px)';
  }
  joyEl.addEventListener('pointerdown', function (e) {
    joyId = e.pointerId;
    try { joyEl.setPointerCapture(e.pointerId); } catch (err) { /* abaikan */ }
    joyMove(e);
    e.preventDefault();
    e.stopPropagation();
  });
  joyEl.addEventListener('pointermove', function (e) { if (e.pointerId === joyId) joyMove(e); });
  ['pointerup', 'pointercancel'].forEach(function (tp) {
    joyEl.addEventListener(tp, function (e) {
      if (e.pointerId !== joyId) return;
      joyId = null;
      if (walk) walk.joy = { x: 0, y: 0 };
      joyKnob.style.transform = '';
    });
  });

  // ---- penghuni: kucing & pustakawan ---------------------------------------
  function updateResidents(now) {
    var ts = clock / 1000;
    if (room && room.cat) {
      var ct = room.cat, pt = ct.petT != null ? (clock - ct.petT) / 1600 : 2, st = pt < 1 ? Math.sin(pt * Math.PI) : 0;
      ct.body.scale.set(1.5 * (1 + 0.12 * st), 0.7 * (1 + 0.04 * Math.sin(ts * 2.2)) * (1 - 0.1 * st), 1);
      ct.head.position.y = 0.07 + 0.04 * st;
      ct.tail.rotation.z = 0.15 * Math.sin(ts * 0.9) + 0.4 * st;
      if (pt >= 1) ct.petT = null;
    }
    var lb = corner && corner.lib;
    if (lb) {
      if (lb.stampT != null) {
        var s1 = (clock - lb.stampT) / 1000;
        var lift = s1 < 0.25 ? s1 / 0.25 : s1 < 0.34 ? 1 - (s1 - 0.25) / 0.09 : 0;
        lb.shR.rotation.set(0.6 - 1.3 * lift, 0.22, 0, 'YXZ');
        lb.elR.rotation.x = 1.0 - 0.5 * lift;
        if (s1 > 0.7) { lb.stampT = null; lb.shR.rotation.set(0.6, 0.22, 0, 'YXZ'); lb.elR.rotation.x = 1.0; }
      } else if (lb.waveT != null) {
        var w1 = (clock - lb.waveT) / 1000, up = Math.min(1, w1 * 4) * Math.min(1, Math.max(0, (1.9 - w1) * 4));
        lb.shL.rotation.set(0.6 * (1 - up), -0.22 * (1 - up), -2.5 * up, 'YXZ');
        lb.elL.rotation.x = 1.0 * (1 - up) + up * (0.35 + 0.4 * Math.sin(w1 * 12));
        if (w1 > 1.9) { lb.waveT = null; lb.shL.rotation.set(0.6, -0.22, 0, 'YXZ'); }
      } else if (lb.clapT != null) {
        var c1 = (clock - lb.clapT) / 1000, cl = Math.abs(Math.sin(c1 * 14)) * Math.min(1, Math.max(0, (2 - c1) * 2));
        lb.shL.rotation.set(0.9, -0.22 + 0.25 * cl, 0, 'YXZ');
        lb.shR.rotation.set(0.9, 0.22 - 0.25 * cl, 0, 'YXZ');
        if (c1 > 2) { lb.clapT = null; lb.shL.rotation.set(0.6, -0.22, 0, 'YXZ'); lb.shR.rotation.set(0.6, 0.22, 0, 'YXZ'); }
      } else {
        // menulis di buku besar
        lb.elR.rotation.x = 1.0 + 0.05 * Math.sin(ts * 6);
        lb.shR.rotation.y = 0.22 + 0.06 * Math.sin(ts * 3);
      }
      lb.head.rotation.x = 0.12 + 0.05 * Math.sin(ts * 0.5) - (Math.sin(ts * 0.17) > 0.7 ? 0.2 : 0);
    }
    var fr = corner && corner.fire;
    if (fr && fr.n) {
      for (var i = 0; i < fr.flames.length; i++) {
        var f = fr.flames[i], hgt = (0.16 + 0.36 * fr.level) * (0.8 + 0.2 * Math.sin(ts * 7 + f.ph) + 0.08 * Math.sin(ts * 13 + f.ph * 2));
        f.s.scale.set(hgt * 0.55, hgt, 1);
        f.s.position.y = 0.1 + hgt / 2;
      }
      fireLight.intensity = fireBase * (0.85 + 0.15 * Math.sin(ts * 9) * Math.sin(ts * 5.3));
    }
  }

  // rintangan berbentuk lingkaran (koordinat dunia) utk mode jalan-jalan
  function addObs(grp, lx, lz, r) {
    grp.updateMatrixWorld(true);
    var v = grp.localToWorld(new THREE.Vector3(lx, 0, lz));
    walkObs.push({ x: v.x, z: v.z, r: r });
  }

  // ---- loop -------------------------------------------------------------
  var raf = 0, running = false, visible = true, last = 0, casesLabelsOn = null, labelsLeft = null;
  var minFrame = coarse ? 1000 / 30 - 2 : 0;   // di HP cukup ±30 fps, hemat baterai

  // ---- suasana mengikuti jam -------------------------------------------
  function currentPhase() {
    var t = new Date();
    return phaseOf(todHour != null ? todHour : t.getHours() + t.getMinutes() / 60);
  }
  function applyTime(ph) {
    var P = TOD[ph];
    todPhase = ph;
    if (skyTex) skyTex.dispose();
    skyTex = skyTexture(ph, weather);
    skyMat.map = skyTex;
    skyMat.needsUpdate = true;
    skyBase = ph === 'night' ? 1.15 : weather && weather !== 'clear' ? 1.25 : 1.7;
    skyMat.color.setScalar(skyBase);
    // hujan/badai: berkas cahaya jendela jauh lebih redup
    var wetK = !weather || weather === 'clear' ? 1 : weather === 'rain' ? 0.35 : 0.2;
    shaftMat.color.set(P.shaft);
    shaftMat.opacity = P.shaftOp * wetK;
    patchMat.color.set(P.shaft);
    patchMat.opacity = Math.min(1, P.shaftOp * 1.8 * wetK);
    shaftDustMat.color.set(P.shaft);
    shaftDustMat.opacity = Math.min(1, 0.35 + P.shaftOp * 2);
    key.color.set(P.key);
    lampMul = P.lamp;
    applyLights();
    host.setAttribute('data-time', ph);
  }

  // Debu di berkas cahaya jendela: melayang pelan turun ke arah lantai
  function updateShaftDust(ts) {
    var b = corner.beam, a = sdGeo.attributes.position.array;
    for (var j = 0; j < SD; j++) {
      var o = j * 4, sv = (sdBase[o] + ts * (0.008 + (j % 5) * 0.003)) % 1;
      var u = (sdBase[o + 1] + Math.sin(ts * 0.3 + sdBase[o + 3]) * 0.06) * b.w * (0.8 + sv * 0.4);
      var nn = sdBase[o + 2] + Math.cos(ts * 0.23 + sdBase[o + 3]) * 0.05;
      a[j * 3] = b.W.x + (b.F.x - b.W.x) * sv + b.X.x * u + b.Z.x * nn;
      a[j * 3 + 1] = b.W.y + (b.F.y - b.W.y) * sv + b.X.y * u + b.Z.y * nn;
      a[j * 3 + 2] = b.W.z + (b.F.z - b.W.z) * sv + b.X.z * u + b.Z.z * nn;
    }
    sdGeo.attributes.position.needsUpdate = true;
  }

  // Mode hemat: kalau gambarnya patah-patah (rata-rata < ±24 fps), resolusi
  // & bayangan diturunkan - dua tingkat, sekali turun tidak naik lagi.
  function lowerQuality() {
    perf.lvl++;
    if (perf.lvl === 1) {
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1));
      key.castShadow = false;
      dust.visible = false;
    } else {
      renderer.setPixelRatio(0.75);
      shaftDust.visible = false;
      minFrame = Math.max(minFrame, 1000 / 30 - 2);
    }
    renderer.setSize(size.w, size.h, false);
    ecoEl.hidden = false;
    perf.since = performance.now();
  }

  function frame(now) {
    raf = requestAnimationFrame(frame);
    var dtMs = now - last;
    if (minFrame && dtMs < minFrame) return;
    last = now;
    var dt = Math.min(dtMs / 1000, 0.05), i;
    clock += dt * 1000;

    if (walk) updateWalk(dt);
    else {
      if (intro) updateIntro();
      else if (spin && !tour && focus < 0 && !drag && !hoverKey && !found && !taken && now - lastInteract > 2500) {
        // Kamera bergoyang pelan ke kiri-kanan (berhenti selama kursor
        // menunjuk sesuatu - kalau tidak, bukunya bergeser sebelum sempat diklik).
        driftPhase += dt * 0.16;
        view.tTheta = driftHome + 0.22 * Math.sin(driftPhase);
      }
      var k = 1 - Math.pow(0.004, dt);
      view.theta += (view.tTheta - view.theta) * k;
      view.phi += (view.tPhi - view.phi) * k;
      view.radius += (view.tRadius - view.radius) * k;
      target.lerp(goal, k);
      placeCam(camera, target, view.radius, view.theta, view.phi);
    }

    if (wing && wing.update) wing.update();   // sudut Design Studio: tabel ERD naik, denyut relasi

    // buku meluncur masuk ke rak (sekali per pembangunan ulang); yang
    // disorot ditarik sedikit keluar dan judulnya lebih terang; buku yang
    // dicari lewat katalog berdenyut terang
    var t0 = buildAt, fm = found ? found.mesh : null;
    for (i = 0; i < books.length; i++) {
      var m = books[i], u = m.userData, s = 1, slide = 0;
      if (!reduced) {
        var t = (clock - t0 - u.delay) / 650;
        if (t < 1) {
          slide = (1 - (t <= 0 ? 0 : easeOutCubic(t))) * 0.9;
          s = t <= 0 ? 0.001 : Math.min(1, 0.4 + t * 2);
        }
      }
      var isSpot = !!spotPic && String(u.p.pic || '') === spotPic, lt = isSpot ? Math.max(u.lift, 0.4) : u.lift;
      u.liftNow += (lt - u.liftNow) * Math.min(1, dt * 12);
      u.outNow += (u.out - u.outNow) * Math.min(1, dt * (reduced ? 60 : 9));
      u.g.position.z = u.baseZ + slide + u.liftNow * 0.16 + u.outNow * 0.55;
      u.g.position.y = u.baseY + u.outNow * 0.05;
      u.g.scale.setScalar(s);
      var gl = u.glow + u.liftNow * 0.7;
      if (u.amp) gl += u.amp * (reduced ? 0.5 : 0.5 + 0.5 * Math.sin(now * u.speed + u.phase));
      if (m === fm) gl += reduced ? 0.8 : 0.7 + 0.5 * Math.sin(now * 0.008);
      if (spotPic) gl = isSpot ? gl + 1.1 + (reduced ? 0 : 0.35 * Math.sin(now * 0.005 + u.phase)) : gl * 0.15;
      u.spine.emissiveIntensity = gl;
      // kabar langsung: buku terbang dari depan ruangan ke tempatnya di rak
      if (u.fly) {
        var ft = (clock - u.fly.t0) / 1300;
        if (ft < 0) u.g.scale.setScalar(0.001);
        else if (ft >= 1) { u.g.position.x = u.baseX; u.g.rotation.y = 0; u.fly = null; }
        else {
          var fe = easeInOutCubic(ft), ff = u.fly.from;
          u.g.position.set(ff.x + (u.baseX - ff.x) * fe, ff.y + (u.baseY - ff.y) * fe + Math.sin(fe * Math.PI) * 0.9, ff.z + (u.baseZ - ff.z) * fe);
          u.g.rotation.y = (1 - fe) * 6;
          u.g.scale.setScalar(1);
        }
      }
    }
    ribbonMats.prog.emissiveIntensity = 0.3 + 0.6 * (reduced ? 0.5 : 0.5 + 0.5 * Math.sin(now * 0.004));
    ribbonMats.over.emissiveIntensity = 0.3 + 0.7 * (reduced ? 0.5 : 0.5 + 0.5 * Math.sin(now * 0.006));
    for (i = 0; i < cases.length; i++) {
      var c = cases[i];
      c.hoverNow += (c.hover - c.hoverNow) * Math.min(1, dt * 10);
      c.plaqueMat.emissiveIntensity = 0.22 + c.hoverNow * 0.45;
    }
    if (corner) {
      corner.hoverNow += (corner.hover - corner.hoverNow) * Math.min(1, dt * 10);
      corner.plaqueMat.emissiveIntensity = 0.22 + corner.hoverNow * 0.45;
      catGlow += ((hoverThing === 'catalog' ? 1 : 0) - catGlow) * Math.min(1, dt * 10);
      catalogMat.emissiveIntensity = 0.08 + catGlow * 0.3;
      if (corner.bellT != null) {
        var bt = (clock - corner.bellT) / 180;
        corner.bell.scale.y = bt < 1 ? 1 - 0.3 * Math.sin(bt * Math.PI) : 1;
        if (bt >= 1) corner.bellT = null;
      }
      if (!reduced || !corner.dustSet) { updateShaftDust(clock / 1000); corner.dustSet = true; }
    }
    // ruangan: globe berputar, jam dinding, nyala lilin bergoyang
    if (room) {
      if (room.globe) {
        room.globeBoost = Math.max(0, room.globeBoost - dt * 0.9);
        if (!reduced) room.globe.rotation.y += dt * (0.12 + room.globeBoost);
      }
      if (room.hands && now - room.handT > 250) {
        room.handT = now;
        var tnow = new Date(), sec = tnow.getSeconds(), mnt = tnow.getMinutes() + sec / 60, hr = tnow.getHours() % 12 + mnt / 60;
        room.hands.s.rotation.z = -sec / 60 * Math.PI * 2;
        room.hands.m.rotation.z = -mnt / 60 * Math.PI * 2;
        room.hands.h.rotation.z = -hr / 12 * Math.PI * 2;
      }
      if (!reduced) for (i = 0; i < room.flames.length; i++) room.flames[i].scale.setScalar(0.34 * (0.9 + 0.1 * Math.sin(now * 0.017 + i * 1.7)));
      if (room.dev && !reduced) {
        updateDev(room.dev, dt, now);
        rgbMat.color.setHSL((clock * 0.00008) % 1, 0.9, 0.55);
      }
    }
    if (!reduced) updateResidents(now);
    updateFx(dt);
    if (room && room.lucca) updateLucca(dt);
    if (luccaGreetAt && clock > luccaGreetAt) { luccaGreetAt = 0; luccaGreet(); }
    // hujan di kaca & kilat saat badai
    if (weather && weather !== 'clear' && !reduced) {
      rainTex.offset.y += dt * (weather === 'storm' ? 2.4 : 1.5);
      if (weather === 'storm') {
        if (clock > storm.next) { storm.t = clock; storm.next = clock + 6000 + Math.random() * 7000; sound.play('thunder'); }
        var lfl = clock - storm.t;
        skyMat.color.setScalar(skyBase * (lfl < 90 || (lfl > 170 && lfl < 240) ? 2.6 : 1));
      }
    }
    if (!reduced) {
      var arr = dustGeo.attributes.position.array;
      for (i = 1; i < arr.length; i += 3) { arr[i] += dt * 0.02; if (arr[i] > 1) arr[i] = 0; }
      dustGeo.attributes.position.needsUpdate = true;
    }

    if (pendingHover && !drag) {
      setHover(intro ? null : pick(pendingHover.x, pendingHover.y), pendingHover.x, pendingHover.y);
      pendingHover = null;
    }

    if (room && room.sides) {
      for (i = 0; i < room.sides.length; i++) {
        var sd = room.sides[i];
        sd.g.visible = !!walk || (camera.position.x - sd.x) * sd.inX > 0.05;
        sd.caps.visible = !walk;
      }
    }
    renderer.render(scene, camera);

    // Buku yang diambil sudah cukup keluar dari rak: serahkan ke pembaca.
    if (taken && !taken.handed && (reduced || clock - taken.t0 > 340)) {
      taken.handed = true;
      var tu = taken.mesh.userData, rect = bookRect();
      tu.g.visible = false;
      opts.onOpenBook(tu.p.id, { rect: rect, cover: tu.cover, key: tu.key, color: tu.color });
    }
    if (found) updateFound();

    // Tur presentasi: maju sendiri tiap TOUR_MS (berhenti selama dijeda /
    // selama ada buku yang sedang dibaca)
    if (tour) {
      if (tour.paused || taken) tour.at += dtMs;
      var tp = (now - tour.at) / TOUR_MS;
      tourProg.style.width = Math.min(100, tp * 100).toFixed(1) + '%';
      if (tp >= 1) tourGo(1);
    }
    for (i = 0; i < decorMats.length; i++) decorMats[i].opacity = reduced ? 0.9 : 0.5 + 0.5 * Math.sin(now * 0.0035 + i * Math.PI);

    if (now - todCheck > 30000) {
      todCheck = now;
      var ph = currentPhase();
      if (ph !== todPhase) applyTime(ph);
    }
    // Diukur dgn waktu nyata (bukan `clock`, yang ikut melambat di perangkat
    // lambat): mulai 2,5 dtk sesudah rak dibangun, rata-rata ±3 dtk.
    if (!perfLock && perf.lvl < 2 && built && !intro && !taken && now - perf.since > 2500) {
      perf.n++;
      perf.sum += dtMs;
      if (perf.sum >= 3000 || perf.n >= 90) {
        if (perf.n >= 8 && perf.sum / perf.n > (coarse ? 52 : 42)) lowerQuality();
        perf.n = 0;
        perf.sum = 0;
      }
    }

    // Label HTML mengikuti titik 3D-nya, ditahan di dalam panggung.
    // Label bulan di kanan rak terdekat; saat mendekati satu rak, di sisi
    // kirinya (di kanannya ada rak tetangga). Di meja depan tidak ada.
    // Ada sudut Design Studio di ujung kanan: di tampilan keseluruhan label bulan
    // DISEMBUNYIKAN - di kanan rak terakhir menutupi easel, di kiri rak pertama menutupi
    // sudut meja (user 17 Nov: "label bulan agak mengganggu"). Muncul lagi saat mendekati rak.
    var homeOff = focus < 0 && !!studioStop;
    var showCases = (layout === 'center' || (layout === 'full' && size.w < 992)) && focus < 0, leftSide = focus >= 0 && focus !== DESK;
    if (showCases !== casesLabelsOn) { casesLabelsOn = showCases; labelsEl.classList.toggle('show-cases', showCases); }
    if (leftSide !== labelsLeft) { labelsLeft = leftSide; labelsEl.classList.toggle('left', leftSide); labels.forEach(function (l) { l.w = 0; }); }
    var ref = !cases.length || focus === DESK || focus === STUDIO || homeOff ? null : cases[focus >= 0 ? focus : cases.length - 1];
    for (i = 0; i < labels.length; i++) {
      var l = labels[i], cc = l.kind === 'shelf' ? ref : stopAt(l.ci);
      if (!cc) { l.el.style.visibility = 'hidden'; continue; }
      if (l.kind === 'case' && !showCases) continue;
      tmpV.copy(l.local);
      if (l.kind === 'shelf') tmpV.x = leftSide ? -CW / 2 - 0.06 : CW / 2 + 0.06;
      tmpV.applyMatrix4(cc.group.matrixWorld).project(camera);
      if (tmpV.z > 1) { l.el.style.visibility = 'hidden'; continue; }
      l.el.style.visibility = '';
      if (!l.w) l.w = l.el.firstElementChild.offsetWidth;
      var lx = (tmpV.x + 1) / 2 * size.w, ly = (1 - tmpV.y) / 2 * size.h;
      lx = l.kind === 'shelf' ? (leftSide ? clamp(lx, l.w + 18, size.w - 4) : clamp(lx, 4, size.w - l.w - 10)) : clamp(lx, l.w / 2 + 4, size.w - l.w / 2 - 4);
      l.el.style.transform = 'translate(' + lx.toFixed(1) + 'px,' + ly.toFixed(1) + 'px)';
    }
  }

  function kick() {
    var should = visible && !document.hidden;
    if (should && !running) { running = true; last = performance.now(); raf = requestAnimationFrame(frame); }
    else if (!should && running) { running = false; cancelAnimationFrame(raf); }
    sound.duck(!should);
  }
  var io = new IntersectionObserver(function (en) { visible = en[0].isIntersecting; kick(); });
  io.observe(host);
  document.addEventListener('visibilitychange', kick);
  var ro = new ResizeObserver(function () { resize(false); });
  ro.observe(host);
  resize();
  kick();

  return {
    update: function (d) {
      lastData = d;
      var sig = signature(d);
      if (sig === lastKey) return;
      lastKey = sig;
      build(d);
    },
    resetView: resetView,
    bookRect: bookRect,
    returnBook: returnBook,
    findBook: findBook,
    openCatalog: openCatalog,
    sfx: function (name) { sound.play(name); },
    // jam tiruan untuk mencoba suasana pagi/siang/malam (null = jam asli)
    setHour: function (h) { todHour = h == null ? null : +h; applyTime(currentPhase()); },
    quality: function () { return perf.lvl; },
    present: startTour,
    walk: startWalk,
    news: news,
    celebrate: celebrate,
    librarianStamp: function () { if (corner && corner.lib) corner.lib.stampT = clock; },
    lucca: luccaBrief,
    // data mockup/ERD berubah (approve, upload, ERD disimpan): sudut studio
    // dicat ulang (dibangun ulang kalau daftar projectnya berubah) & warna Lucca ikut
    studioChanged: function () {
      if (!wing) return;
      if (wing.needsRebuild && wing.needsRebuild()) rebuild(); else if (wing.refresh) wing.refresh();
      var L = room && room.lucca;
      if (L) { L.mood = luccaMoodOf(L.st); L.base.set(LUCCA_COL[L.mood]); }
    },
    setSpot: setSpot,
    // hiasan musiman tiruan untuk mencoba ('merdeka' | 'lebaran' | 'yearend' | '' = tanpa, null = ikut tanggal)
    setSeason: function (name) { seasonOverride = name === undefined ? null : name; rebuild(); },
    destroy: function () {
      stopTour();
      stopWalk();
      clearTimeout(newsTimer);
      hideLucca(true);
      window.removeEventListener('keydown', onWalkKey);
      window.removeEventListener('keyup', onWalkKey);
      window.removeEventListener('blur', onWalkBlur);
      fxs.forEach(function (f) { scene.remove(f.pts); f.pts.geometry.dispose(); f.pts.material.dispose(); });
      fxs = [];
      running = false;
      cancelAnimationFrame(raf);
      io.disconnect();
      ro.disconnect();
      document.removeEventListener('visibilitychange', kick);
      document.removeEventListener('pointerdown', onDocDown, true);
      document.removeEventListener('fullscreenchange', onFsChange);
      window.removeEventListener('keydown', onWinKey);
      host.removeEventListener('keydown', onKey);
      sound.destroy();
      removeDoor();
      disposeLib();
      if (skyTex) skyTex.dispose();
      sharedGeo.concat(sharedMat, sharedTex, [dustGeo, dustMat, sdGeo, envTex, pmrem]).forEach(function (o) { o.dispose(); });
      renderer.dispose();
      renderer.forceContextLoss();
      host.innerHTML = '';
      host.removeAttribute('data-time');
      host.removeAttribute('tabindex');
      host.removeAttribute('aria-label');
    }
  };
}
