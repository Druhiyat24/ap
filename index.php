<?php
// ?next= diisi saat sesi habis: halaman yang dibuka terakhir (lihat login_next.php)
include 'login_next.php';
$next = login_next_aman(isset($_GET['next']) ? $_GET['next'] : '');

// saklar tampilan: lama -> pakai halaman login lama
include_once __DIR__ . '/tampilan.php';
if (!TAMPILAN_BARU) {
    include __DIR__ . '/login_lama.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>NDS | Log in</title>
<link rel="icon" type="image/png" sizes="32x32" href="images/nds/icon-32.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="images/nds/icon-16.png?v=2">
<link rel="shortcut icon" href="images/nds/favicon.ico?v=2">
<link rel="apple-touch-icon" href="images/nds/icon-180.png?v=2">
<link rel="stylesheet" href="module/fontawesome5/css/all.min.css">
<style>
/* Halaman login NDS (gaya 3D). Formulir dikirim ke auth.php (isian: user, pass). */
* { box-sizing: border-box; }

html, body { height: 100%; }
body {
  margin: 0;
  font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: #1e3a8a;
  background:
    radial-gradient(ellipse 55% 45% at 92% 0%, rgba(255, 221, 150, .40), transparent 70%),
    radial-gradient(ellipse 45% 40% at 0% 100%, rgba(253, 232, 180, .45), transparent 70%),
    linear-gradient(135deg, #f8faff 0%, #edf3fc 55%, #f5f7fc 100%);
  overflow-x: hidden;
}

/* ---------- hiasan latar (tidak bisa diklik) ----------
   closest-side: gradasinya habis TEPAT di tepi kotak, kalau tidak tepi kotaknya kelihatan */
.mn3-hias { position: fixed; pointer-events: none; z-index: 0; }
.mn3-awan1 {                                   /* awan biru kiri atas */
  width: 620px; height: 520px; left: -200px; top: -200px;
  background: radial-gradient(closest-side, rgba(186, 211, 250, .85), rgba(186, 211, 250, 0));
}
.mn3-awan2 {                                   /* awan emas di belakang ilustrasi */
  width: 560px; height: 460px; left: 4%; bottom: 6%;
  background: radial-gradient(closest-side, rgba(250, 226, 160, .55), rgba(250, 226, 160, 0));
}
.mn3-awan3 {                                   /* gelombang biru kanan bawah */
  width: 520px; height: 360px; right: -160px; bottom: -170px;
  background: radial-gradient(closest-side, rgba(147, 190, 245, .55), rgba(147, 190, 245, 0));
}
.mn3-garis-kiri  { left: 0; top: 0; width: 520px; height: 460px; }
.mn3-garis-kanan { right: 0; bottom: 0; width: 420px; height: 260px; }

/* ---------- tata letak: ilustrasi | formulir ---------- */
/* sengaja TANPA z-index: z-index membuat lapisan sendiri, dan logo yang
   memakai mix-blend-mode jadi tidak bisa melebur ke latar body */
.mn3-wrap {
  position: relative;
  min-height: 100vh;
  max-width: 1640px;
  margin: 0 auto;
  padding: 32px 4vw;
  display: grid;
  grid-template-columns: 1.15fr 1fr;
  align-items: center;
  gap: 3vw;
}

/* ---------- kiri: ilustrasi 3D ---------- */
.mn3-art { display: flex; justify-content: center; }
.mn3-art img {
  width: 100%;
  max-width: 780px;
  max-height: 80vh;
  object-fit: contain;
  animation: mn3-melayang 7s ease-in-out infinite;
  /* ujung bawah pantulan lantai dipudarkan supaya menyatu dgn latar */
  -webkit-mask-image: linear-gradient(to bottom, #000 88%, transparent 100%);
  mask-image: linear-gradient(to bottom, #000 88%, transparent 100%);
}
@keyframes mn3-melayang {
  0%, 100% { transform: translateY(0); }
  50%      { transform: translateY(-10px); }
}

/* ---------- kanan: logo + kartu kaca ---------- */
.mn3-panel { width: 100%; max-width: 540px; justify-self: center; }

.mn3-logo {
  display: block;
  width: 100%;
  max-width: 420px;
  height: auto;
  margin: 0 auto 12px;
  mix-blend-mode: multiply;      /* jaga-jaga kalau logo berlatar putih */
}

/* "AP . CASH . BANK . ACCOUNTING" diapit garis emas */
.mn3-modul {
  display: flex;
  align-items: center;
  gap: 14px;
  margin: 0 auto 30px;
  max-width: 500px;
}
.mn3-modul i { flex: 1; height: 3px; border-radius: 3px; background: linear-gradient(90deg, rgba(229, 169, 19, 0), #e5a913); }
.mn3-modul i:last-child { background: linear-gradient(90deg, #e5a913, rgba(229, 169, 19, 0)); }
.mn3-modul b { font-size: 15px; font-weight: 700; letter-spacing: .07em; white-space: nowrap; }
.mn3-modul b em { font-style: normal; color: #e5a913; margin: 0 8px; }

/* kartu kaca: tembus pandang + blur latar + bayangan berlapis */
.mn3-kartu {
  position: relative;
  padding: 32px 32px 28px;
  border-radius: 22px;
  background: linear-gradient(160deg, rgba(255, 255, 255, .82), rgba(255, 255, 255, .55));
  -webkit-backdrop-filter: blur(18px) saturate(150%);
  backdrop-filter: blur(18px) saturate(150%);
  border: 1px solid rgba(255, 255, 255, .95);
  box-shadow:
    0 34px 60px -28px rgba(30, 58, 138, .38),
    0 14px 26px -14px rgba(30, 58, 138, .20),
    inset 0 1px 0 rgba(255, 255, 255, 1);
}
.mn3-kartu::before {           /* pendar putih di sekeliling kartu */
  content: "";
  position: absolute;
  inset: -18px;
  z-index: -1;
  border-radius: 34px;
  background: radial-gradient(ellipse at 50% 50%, rgba(255, 255, 255, .9), rgba(255, 255, 255, 0) 70%);
  filter: blur(12px);
}

.mn3-isian { position: relative; margin-bottom: 16px; }
.mn3-isian > i {
  position: absolute;
  left: 19px; top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #475569;
  pointer-events: none;
}
.mn3-isian input {
  width: 100%;
  height: 56px;
  padding: 0 54px;
  font-size: 16px;
  color: #1e3a8a;
  background: rgba(255, 255, 255, .92);
  border: 1.5px solid #9db1d8;
  border-radius: 13px;
  outline: none;
  box-shadow: inset 0 1px 2px rgba(15, 23, 42, .06), 0 1px 0 rgba(255, 255, 255, .8);
  transition: border-color .15s ease, box-shadow .15s ease;
}
.mn3-isian input::placeholder { color: #64748b; }
.mn3-isian input:focus {
  border-color: #1e3a8a;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, .14), inset 0 1px 2px rgba(15, 23, 42, .06);
}
.mn3-mata {
  position: absolute;
  right: 8px; top: 50%;
  transform: translateY(-50%);
  width: 42px; height: 42px;
  display: flex; align-items: center; justify-content: center;
  border: 0; background: transparent; border-radius: 9px;
  color: #475569; font-size: 18px;
  cursor: pointer;
}
.mn3-mata:hover { background: rgba(30, 58, 138, .07); }

.mn3-ingat {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  margin: 2px 0 22px 4px;
  font-size: 14.5px;
  font-weight: 600;
  cursor: pointer;
  user-select: none;
}
.mn3-ingat input { width: 18px; height: 18px; accent-color: #1e3a8a; cursor: pointer; }

/* pemberitahuan sesi habis */
.mn3-info {
  display: flex; align-items: flex-start; gap: 10px;
  margin: 0 0 18px;
  padding: 11px 14px;
  font-size: 13.5px; font-weight: 600; line-height: 1.45;
  color: #8a5a00;
  background: #fff7e0;
  border: 1px solid #f5d98a;
  border-radius: 11px;
}
.mn3-info i { margin-top: 2px; }

/* tombol timbul: gradasi + kilap atas + bayangan biru */
.mn3-tombol {
  width: 100%;
  height: 60px;
  display: flex; align-items: center; justify-content: center; gap: 14px;
  font-size: 20px; font-weight: 700; letter-spacing: .02em;
  color: #fff;
  border: 0;
  border-radius: 14px;
  cursor: pointer;
  background: linear-gradient(180deg, #2f62dc 0%, #1e3a8a 100%);
  box-shadow:
    0 16px 30px -12px rgba(30, 64, 175, .75),
    inset 0 1px 0 rgba(255, 255, 255, .35),
    inset 0 -3px 0 rgba(0, 0, 0, .18);
  transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
}
.mn3-tombol i { transition: transform .15s ease; }
.mn3-tombol:hover { filter: brightness(1.06); transform: translateY(-1px);
  box-shadow: 0 20px 34px -12px rgba(30, 64, 175, .85), inset 0 1px 0 rgba(255, 255, 255, .35), inset 0 -3px 0 rgba(0, 0, 0, .18); }
.mn3-tombol:hover i { transform: translateX(4px); }
.mn3-tombol:active { transform: translateY(1px);
  box-shadow: 0 8px 18px -10px rgba(30, 64, 175, .8), inset 0 2px 4px rgba(0, 0, 0, .2); }

.mn3-kaki { display: flex; align-items: center; gap: 14px; margin-top: 28px; }
.mn3-kaki i { flex: 1; height: 2px; border-radius: 2px; background: linear-gradient(90deg, rgba(30, 58, 138, 0), rgba(30, 58, 138, .45)); }
.mn3-kaki i:last-child { background: linear-gradient(90deg, rgba(30, 58, 138, .45), rgba(30, 58, 138, 0)); }
.mn3-kaki span { font-size: 13.5px; font-weight: 600; white-space: nowrap; }

/* ---------- layar sedang & kecil ---------- */
@media (max-width: 1199.98px) {
  .mn3-wrap { grid-template-columns: 1fr 1fr; padding: 28px 3vw; }
  .mn3-logo { max-width: 360px; }
}
@media (max-width: 991.98px) {          /* tablet: ilustrasi di atas, formulir di bawah */
  .mn3-wrap { grid-template-columns: 1fr; gap: 8px; padding: 24px 24px 36px; align-content: center; }
  .mn3-art img { max-width: 440px; max-height: 38vh; }
  .mn3-logo { max-width: 320px; }
  .mn3-modul b { font-size: 13px; }
}
@media (max-width: 575.98px) {          /* HP: formulir saja */
  .mn3-art { display: none; }
  .mn3-wrap { padding: 20px 16px 32px; }
  .mn3-kartu { padding: 24px 18px 20px; }
  .mn3-isian input { height: 52px; }
  .mn3-tombol { height: 56px; font-size: 18px; }
  .mn3-garis-kiri { width: 300px; height: 260px; }
  .mn3-garis-kanan { width: 260px; height: 160px; }
}
@media (max-width: 440px) {
  .mn3-logo { max-width: 240px; }
  .mn3-modul { gap: 8px; margin-bottom: 22px; justify-content: center; }
  .mn3-modul i { display: none; }
  .mn3-modul b { font-size: 12px; white-space: normal; text-align: center; }
  .mn3-kaki { justify-content: center; }
  .mn3-kaki i { display: none; }
  .mn3-kaki span { font-size: 12px; white-space: normal; text-align: center; }
}
@media (prefers-reduced-motion: reduce) {
  .mn3-art img { animation: none; }
}


/* =====================================================================
   ANIMASI  -  BLOK TAMBAHAN (inilah yang nanti ditempel ke index.php)

   Lima kelompok, masing-masing dinyalakan oleh satu kelas di <body>
   supaya di mockup ini bisa dimatikan satu-satu lewat panel di pojok.
   Di halaman asli nanti kelima kelas itu dipasang tetap.

   ATURAN YANG DIPEGANG:
     1. Semua gerakan mati total kalau pemakai menyetel "kurangi gerakan"
        di sistemnya (prefers-reduced-motion) - ada di paling bawah.
     2. Animasi masuk memakai fill-mode BACKWARDS, bukan both. Kalau both,
        transform hasil animasi tetap menempel sesudah selesai dan
        MEMBATALKAN efek :hover tombol (animasi mengalahkan aturan biasa).
     3. Kartu kaca TIDAK boleh diberi transform/filter langsung: dia punya
        ::before ber-z-index -1 sbg pendar putih; begitu kartunya diberi
        transform, dia jadi lapisan sendiri dan pendarnya malah naik ke
        ATAS latar kartu - jadi tampak seperti noda putih. Karena itu
        animasinya dipasang di pembungkusnya (.mn3-kartu-bungkus).
   ===================================================================== */

/* --------------------------------------------------------- 1. MASUK HALAMAN
   Berurutan dari kiri ke kanan, jedanya kecil saja supaya tidak terasa lambat. */
@keyframes mn3-masuk      { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
@keyframes mn3-masuk-kiri { from { opacity: 0; transform: translateX(-44px) scale(.95); } to { opacity: 1; transform: none; } }
@keyframes mn3-masuk-redup{ from { opacity: 0; } to { opacity: 1; } }

body.anim-masuk .mn3-art    { animation: mn3-masuk-kiri .95s cubic-bezier(.22,.9,.26,1) backwards; }
body.anim-masuk .mn3-logo   { animation: mn3-masuk .70s cubic-bezier(.22,.9,.26,1) .10s backwards; }
body.anim-masuk .mn3-modul  { animation: mn3-masuk .70s cubic-bezier(.22,.9,.26,1) .18s backwards; }
body.anim-masuk .mn3-kartu-bungkus { animation: mn3-masuk .75s cubic-bezier(.22,.9,.26,1) .26s backwards; }
body.anim-masuk .mn3-kaki   { animation: mn3-masuk .70s cubic-bezier(.22,.9,.26,1) .62s backwards; }
body.anim-masuk .mn3-hias   { animation: mn3-masuk-redup 1.4s ease-out backwards; }

/* Isi kartu menyusul satu per satu sesudah kartunya muncul.
   Isian kedua ditunjuk dgn ".mn3-isian + .mn3-isian", BUKAN :nth-of-type -
   kalau sesi habis ada <div class="mn3-info"> ikut terpasang di atasnya,
   dan nomor urut :nth-of-type langsung bergeser satu. */
body.anim-masuk .mn3-info    { animation: mn3-masuk .55s ease-out .34s backwards; }
body.anim-masuk .mn3-isian   { animation: mn3-masuk .55s ease-out .38s backwards; }
body.anim-masuk .mn3-isian + .mn3-isian { animation-delay: .44s; }
body.anim-masuk .mn3-ingat   { animation: mn3-masuk .55s ease-out .50s backwards; }
body.anim-masuk .mn3-tombol  { animation: mn3-masuk .55s ease-out .56s backwards; }

/* ------------------------------------------------------------- 2. AWAN LATAR
   Tiga awan bergerak sangat pelan dgn arah & durasi berbeda supaya tidak
   pernah terlihat berbaris. Jaraknya kecil - yang dicari kesan "hidup",
   bukan gerakan yang menarik perhatian. */
@keyframes mn3-apung1 { 0%, 100% { transform: translate(0, 0) scale(1); }     50% { transform: translate(46px, 30px) scale(1.09); } }
@keyframes mn3-apung2 { 0%, 100% { transform: translate(0, 0) scale(1); }     50% { transform: translate(-34px, -26px) scale(1.07); } }
@keyframes mn3-apung3 { 0%, 100% { transform: translate(0, 0) scale(1); }     50% { transform: translate(-40px, -22px) scale(1.06); } }

body.anim-awan .mn3-awan1 { animation: mn3-apung1 26s ease-in-out infinite; }
body.anim-awan .mn3-awan2 { animation: mn3-apung2 22s ease-in-out infinite; }
body.anim-awan .mn3-awan3 { animation: mn3-apung3 30s ease-in-out infinite; }

/* ----------------------------------------------------- 3. GARIS EMAS DIGAMBAR
   Garis & lengkung di hiasan SVG seperti ditarik tangan saat halaman dibuka.
   700 dipilih karena lebih panjang dari garis terpanjang di gambar itu. */
@keyframes mn3-gambar { from { stroke-dashoffset: 700; } to { stroke-dashoffset: 0; } }
@keyframes mn3-denyut { 0%, 100% { transform: scale(1);   opacity: .80; }
                        50%      { transform: scale(1.35); opacity: .45; } }
@keyframes mn3-ombak  { 0%, 100% { transform: translateY(0); }
                        50%      { transform: translateY(-7px); } }

body.anim-garis .mn3-garis-kiri line,
body.anim-garis .mn3-garis-kiri path,
body.anim-garis .mn3-garis-kanan path[stroke] {
  stroke-dasharray: 700;
  animation: mn3-gambar 1.7s ease-out .25s backwards;
}
body.anim-garis .mn3-garis-kiri circle {
  transform-origin: 104px 30px;
  animation: mn3-denyut 3.4s ease-in-out 1.7s infinite;
}
/* dua gelombang biru kanan bawah naik-turun pelan, saling susul */
body.anim-garis .mn3-garis-kanan path[fill]:nth-of-type(1) { animation: mn3-ombak 9s  ease-in-out infinite; }
body.anim-garis .mn3-garis-kanan path[fill]:nth-of-type(2) { animation: mn3-ombak 11s ease-in-out .8s infinite; }

/* --------------------------------------------------------- 4. BUTIR EMAS
   Titik-titik kecil naik perlahan dari bawah layar lalu pudar. Jumlahnya
   sengaja sedikit (9) dan tembus pandang - kalau terlalu ramai, mata jadi
   tertarik ke sana, bukan ke kotak isian. */
.mn3-butir { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; display: none; }
body.anim-butir .mn3-butir { display: block; }
.mn3-butir span {
  position: absolute;
  bottom: -24px;
  width: 7px; height: 7px;
  border-radius: 50%;
  background: radial-gradient(circle at 35% 35%, #f6d98c, #e5a913);
  opacity: 0;
  animation: mn3-naik 17s linear infinite;
}
@keyframes mn3-naik {
  0%   { transform: translate3d(0, 0, 0) scale(.6);           opacity: 0; }
  12%  {                                                      opacity: .55; }
  82%  {                                                      opacity: .30; }
  100% { transform: translate3d(34px, -104vh, 0) scale(1.05); opacity: 0; }
}

/* ------------------------------------------------------- 5. KILAU TOMBOL
   Seberkas cahaya melintas tombol Login tiap beberapa detik - menarik mata
   ke satu-satunya tombol di halaman ini tanpa perlu warna tambahan. */
.mn3-tombol { position: relative; overflow: hidden; }
.mn3-tombol::after {
  content: "";
  position: absolute;
  top: -10%; bottom: -10%;
  left: -70%; width: 45%;
  background: linear-gradient(100deg, rgba(255,255,255,0), rgba(255,255,255,.42), rgba(255,255,255,0));
  transform: skewX(-18deg);
  opacity: 0;
}
body.anim-kilau .mn3-tombol::after { animation: mn3-kilau 6s ease-in-out 2.4s infinite; }
@keyframes mn3-kilau {
  0%            { left: -70%; opacity: 0; }
  6%            {             opacity: 1; }
  26%           { left: 125%; opacity: 1; }
  27%, 100%     { left: 125%; opacity: 0; }
}

/* --------------------------------------------- INTERAKSI (selalu menyala)
   Bukan hiasan - ini membantu pemakai tahu di mana dia sedang mengetik
   dan bahwa tombolnya sudah ditekan. */
.mn3-isian > i { transition: color .18s ease, transform .18s ease; }
.mn3-isian:focus-within > i { color: #1e3a8a; transform: translateY(-50%) scale(1.14); }
.mn3-isian input { transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease; }
.mn3-isian input:focus { transform: translateY(-1px); }
.mn3-mata { transition: background .15s ease, transform .15s ease; }
.mn3-mata:active { transform: translateY(-50%) scale(.9); }

/* tombol sedang memproses: tulisan diganti, panah jadi lingkaran berputar */
.mn3-tombol.sibuk { pointer-events: none; filter: saturate(.85) brightness(.96); }
.mn3-tombol.sibuk::after { display: none; }
@keyframes mn3-putar { to { transform: rotate(360deg); } }
.mn3-tombol.sibuk .mn3-putar {
  width: 22px; height: 22px;
  border: 3px solid rgba(255, 255, 255, .4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: mn3-putar .7s linear infinite;
}

/* ------------------------------------------------------ HORMATI SETELAN
   Satu pemakai bisa saja mual melihat gerakan (motion sickness) dan sudah
   menyetelnya di Windows/HP. Semua gerakan di atas dimatikan untuk dia -
   halamannya tetap lengkap, hanya diam. */
@media (prefers-reduced-motion: reduce) {
  body.anim-masuk .mn3-art, body.anim-masuk .mn3-logo, body.anim-masuk .mn3-modul,
  body.anim-masuk .mn3-kartu-bungkus, body.anim-masuk .mn3-kaki, body.anim-masuk .mn3-hias,
  body.anim-masuk .mn3-info, body.anim-masuk .mn3-isian, body.anim-masuk .mn3-ingat,
  body.anim-masuk .mn3-tombol,
  body.anim-awan .mn3-awan1, body.anim-awan .mn3-awan2, body.anim-awan .mn3-awan3,
  body.anim-garis .mn3-garis-kiri line, body.anim-garis .mn3-garis-kiri path,
  body.anim-garis .mn3-garis-kiri circle, body.anim-garis .mn3-garis-kanan path,
  body.anim-kilau .mn3-tombol::after {
    animation: none !important;
  }
  .mn3-butir { display: none !important; }
  .mn3-isian input:focus { transform: none; }
}
</style>
</head>
<!-- Lima kelas ini yang menyalakan animasi; dipisah begitu supaya satu
     kelompok bisa dimatikan tanpa menyentuh CSS-nya. -->
<body class="anim-masuk anim-awan anim-garis anim-butir anim-kilau">

<!-- hiasan latar -->
<div class="mn3-hias mn3-awan1"></div>
<div class="mn3-hias mn3-awan2"></div>
<div class="mn3-hias mn3-awan3"></div>
<svg class="mn3-hias mn3-garis-kiri" viewBox="0 0 520 460" fill="none" aria-hidden="true">
  <g stroke="#e5a913" stroke-width="1.6" stroke-linecap="round" opacity=".55">
    <line x1="34" y1="0" x2="34" y2="250"/>
    <line x1="52" y1="0" x2="52" y2="330"/>
    <line x1="70" y1="0" x2="70" y2="280"/>
    <line x1="88" y1="0" x2="88" y2="360"/>
  </g>
  <path d="M0 440 C 150 330, 290 150, 520 60" stroke="#e5a913" stroke-width="1.6" opacity=".45"/>
  <path d="M0 400 C 170 300, 300 120, 500 0" stroke="#e5a913" stroke-width="1.2" opacity=".30"/>
  <circle cx="104" cy="30" r="6" fill="#e5a913" opacity=".8"/>
</svg>
<svg class="mn3-hias mn3-garis-kanan" viewBox="0 0 420 260" fill="none" aria-hidden="true">
  <path d="M420 40 C 330 70, 250 150, 210 260 L420 260 Z" fill="#bcd5f8" opacity=".55"/>
  <path d="M420 110 C 360 130, 300 190, 280 260 L420 260 Z" fill="#9cc0f2" opacity=".45"/>
  <path d="M130 260 C 190 170, 300 90, 420 70" stroke="#e5a913" stroke-width="1.6" opacity=".55"/>
</svg>

<!-- Butir emas yang naik pelan. Sebarannya diatur lewat style sebaris supaya
     tidak perlu sembilan aturan CSS yang isinya cuma angka. -->
<div class="mn3-butir" aria-hidden="true">
  <span style="left: 6%;  animation-delay: 0s;    animation-duration: 18s;"></span>
  <span style="left: 17%; animation-delay: 3.5s;  animation-duration: 21s;"></span>
  <span style="left: 28%; animation-delay: 7s;    animation-duration: 16s;"></span>
  <span style="left: 39%; animation-delay: 1.5s;  animation-duration: 23s;"></span>
  <span style="left: 52%; animation-delay: 9.5s;  animation-duration: 19s;"></span>
  <span style="left: 63%; animation-delay: 5s;    animation-duration: 17s;"></span>
  <span style="left: 74%; animation-delay: 12s;   animation-duration: 22s;"></span>
  <span style="left: 85%; animation-delay: 2.5s;  animation-duration: 20s;"></span>
  <span style="left: 94%; animation-delay: 14s;   animation-duration: 18s;"></span>
</div>

<div class="mn3-wrap">

  <div class="mn3-art">
    <!-- di HP ilustrasi disembunyikan; <source> ini mencegah HP ikut mengunduh gambarnya -->
    <picture>
      <source media="(max-width: 575.98px)" srcset="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
      <img src="images/nds/login-art-3d.webp" alt="">
    </picture>
  </div>

  <div class="mn3-panel">

    <img class="mn3-logo" src="images/nds/logo.png" alt="NDS - Nirwana Digital Solution">

    <div class="mn3-modul">
      <i></i>
      <b>AP<em>&bull;</em>CASH<em>&bull;</em>BANK<em>&bull;</em>ACCOUNTING</b>
      <i></i>
    </div>

    <!-- Pembungkus ini HANYA untuk animasi masuk - lihat catatan di blok
         CSS ANIMASI soal kenapa kartunya sendiri tidak boleh diberi transform. -->
    <div class="mn3-kartu-bungkus">
    <form class="mn3-kartu" id="mn_form" method="post" action="auth.php">

      <?php if ($next !== '') { ?>
      <div class="mn3-info">
        <i class="far fa-clock" aria-hidden="true"></i>
        <span>Your session has expired. Log in again to return to the page you were on.</span>
      </div>
      <input type="hidden" name="next" value="<?php echo htmlspecialchars($next, ENT_QUOTES); ?>">
      <?php } ?>

      <div class="mn3-isian">
        <i class="far fa-user" aria-hidden="true"></i>
        <input type="text" name="user" id="mn_user" placeholder="Username" autocomplete="username" autofocus required>
      </div>

      <div class="mn3-isian">
        <i class="fas fa-lock" aria-hidden="true"></i>
        <input type="password" name="pass" id="mn_pass" placeholder="Password" autocomplete="current-password" required>
        <button type="button" class="mn3-mata" id="mn_lihat" title="Show password">
          <i class="far fa-eye" aria-hidden="true"></i>
        </button>
      </div>

      <label class="mn3-ingat">
        <input type="checkbox" id="mn_ingat"> Remember me
      </label>

      <button type="submit" class="mn3-tombol" id="mn_kirim">
        <span class="mn3-teks">Login</span> <i class="fas fa-arrow-right" aria-hidden="true"></i>
      </button>

    </form>
    </div><!-- /.mn3-kartu-bungkus -->

    <div class="mn3-kaki">
      <i></i>
      <span>PT Nirwana Alabare Garment</span>
      <i></i>
    </div>

  </div>
</div>

<script>
/* Tombol mata: perlihatkan / sembunyikan kata sandi. */
document.getElementById('mn_lihat').addEventListener('click', function () {
  var isi = document.getElementById('mn_pass');
  var mata = this.querySelector('i');
  var sembunyi = (isi.type === 'password');
  isi.type = sembunyi ? 'text' : 'password';
  mata.className = sembunyi ? 'far fa-eye-slash' : 'far fa-eye';
  this.title = sembunyi ? 'Hide password' : 'Show password';
  isi.focus();
});

/* "Remember me" = ingat NAMA PENGGUNA saja (di peramban ini), bukan kata sandi. */
(function () {
  var user  = document.getElementById('mn_user');
  var ingat = document.getElementById('mn_ingat');
  var simpan;
  try { simpan = window.localStorage; } catch (e) { simpan = null; }
  if (!simpan) { return; }

  var tersimpan = simpan.getItem('login_user');
  if (tersimpan) {
    user.value = tersimpan;
    ingat.checked = true;
    document.getElementById('mn_pass').focus();
  }

  user.form.addEventListener('submit', function () {
    try {
      if (ingat.checked) { simpan.setItem('login_user', user.value); }
      else { simpan.removeItem('login_user'); }
    } catch (e) { /* penyimpanan diblokir - abaikan */ }
  });
})();
</script>

<script>
/* ---------------------------------------------------------------------
   Tombol Login: begitu ditekan, tulisannya berganti dan panahnya jadi
   lingkaran berputar, lalu tombolnya dikunci supaya tidak tertekan dua kali.

   SENGAJA TANPA preventDefault - formulirnya memang harus terkirim ke
   auth.php. Kalau isiannya belum lengkap, peramban menahan pengirimannya
   dan peristiwa submit tidak pernah jalan, jadi tombolnya tidak akan
   tersangkut di "Signing in...".

   auth.php kalau gagal menjalankan history.go(-1), jadi pemakai KEMBALI ke
   halaman ini dari simpanan peramban - tombolnya akan tetap "Signing in..."
   kalau tidak disetel ulang. Itulah gunanya pageshow di bawah.
   --------------------------------------------------------------------- */
function tombolSibuk(sibuk) {
  var t = document.getElementById('mn_kirim');
  if (!t) { return; }
  t.classList.toggle('sibuk', sibuk);
  var teks = t.querySelector('.mn3-teks');
  if (teks) { teks.textContent = sibuk ? 'Signing in\u2026' : 'Login'; }
  var ikon = t.querySelector('i');
  if (ikon) { ikon.className = sibuk ? 'mn3-putar' : 'fas fa-arrow-right'; }
}
document.getElementById('mn_form').addEventListener('submit', function () {
  tombolSibuk(true);
});
window.addEventListener('pageshow', function () { tombolSibuk(false); });
</script>

</body>
</html>
