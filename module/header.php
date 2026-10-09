<?php
ini_set('memory_limit', '4096M');
set_time_limit(0);

session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php'; 
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user == '') {
  $script = "<script>
  window.location = '../function/logout.php';</script>";
  echo $script;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <style>
    img {
      display: block;
      margin-left: auto;
      margin-right: auto;
      height: 30px;
  }
  .notif-badge {
      display: inline-block;
      background: linear-gradient(135deg, #ff5b5b, #d62828);
      color: #fff;
      font-size: 10px;
      font-weight: 700;
      line-height: 1;
      padding: 2px 7px;
      border-radius: 10px;
      margin-left: 6px;
      box-shadow: 0 0 0 2px rgba(255,255,255,0.08), 0 1px 3px rgba(0,0,0,0.4);
      vertical-align: middle;
      animation: notif-pulse 2s infinite;
  }
  @keyframes notif-pulse {
      0% {
          box-shadow: 0 0 0 2px rgba(255,255,255,0.08), 0 1px 3px rgba(0,0,0,0.4), 0 0 0 0 rgba(214,40,40,0.55);
      }
      70% {
          box-shadow: 0 0 0 2px rgba(255,255,255,0.08), 0 1px 3px rgba(0,0,0,0.4), 0 0 0 6px rgba(214,40,40,0);
      }
      100% {
          box-shadow: 0 0 0 2px rgba(255,255,255,0.08), 0 1px 3px rgba(0,0,0,0.4), 0 0 0 0 rgba(214,40,40,0);
      }
  }
  .box {
      border-style: outset;
      box-sizing: border-box;
  }
  .body {
      font-size: 12px;     
  }

/*  body {
      transform: scale(0.9);  
    transform-origin: 0 0;     
     width: 111.11%;
  }*/
  .box .header {
      font-size: 12px;
  }
  .form-control-plaintext {
      border: 1px solid grey;
  }
  .form-row {
      margin-right: 0;
      margin-left: -10px;
  }
  .filter-option {
      font-size: 12px;
  }
  .datatable_wrapper{
      font-size: 12px;
  }

  .container-1 input#myInput{
      width: 220px;
      height: 32px;
      position: relative;
      background: white;
      font-size: 10pt;
      float: right;
      color: #63717f;
      padding-left: 15px;
      -webkit-border-radius: 5px;
      -moz-border-radius: 5px;
      border-radius: 5px;
  }

  a{
      font-size: 14px;
  }
  button{
      font-size: 13px !important;
  }

  table{
      font-size: 12px;
  }

  table{
      font-size: 12px;
  }

  h2.text-center{
      font-size: 20px;
  }

  h3.text-center{
      font-size: 20px;
  }

  h4{
      font-size: 20px;
  }

  h5.text-white{
      font-size: 18px;
  }


  /* Chrome, Safari, Edge, Opera */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
  }

  .tableFix { /* Scrollable parent element */
      position: relative;
      overflow: auto;
      height: 100px;
      font-size: 12px;
  }

  .tableFix table{
      width: 100%;
      border-collapse: collapse;
  }

  .tableFix th,
  .tableFix td{
      padding: 8px;
      text-align: left;
  }

  .tableFix thead {
      position: sticky;  /* Edge, Chrome, FF */
      top: 0px;
      background: #F0F8FF;  /* Some background is needed */
  }


  .dropdown-submenu {
      position: relative;
  }

  .dropdown-submenu>.dropdown-menu {
      top: -6px;
      left: 100%;
      margin-left: 4px;
      border: none;
      padding: 6px;
      box-shadow: 0 10px 28px rgba(0,0,0,.4);
      -webkit-border-radius: 10px;
      -moz-border-radius: 10px;
      border-radius: 10px;
  }

  .dropdown-submenu:hover>.dropdown-menu {
      display: block;
  }

  /* Trigger submenu via click/tap too (not just hover) - hover alone is
     unreliable on touch/Android, needs a double-tap to actually open. */
  .dropdown-submenu.open-submenu>.dropdown-menu {
      display: block;
  }

  .dropdown-submenu>a {
      border-radius: 6px;
      transition: background-color .12s ease;
  }

  .dropdown-submenu:hover>a,
  .dropdown-submenu.open-submenu>a {
      background-color: rgba(255,255,255,.12);
  }

  .dropdown-submenu>a:after {
      content: "\f054";
      font-family: "Font Awesome 5 Free";
      font-weight: 900;
      font-size: 10px;
      color: rgba(255, 255, 255, .4);
      margin-left: auto;
      padding-left: 10px;
      transition: color .12s ease;
  }

  .dropdown-submenu:hover>a:after,
  .dropdown-submenu.open-submenu>a:after {
      color: #4dabf7;
  }

  .dropdown-submenu.pull-left {
      float: none;
  }

  .dropdown-submenu.pull-left>.dropdown-menu {
      left: -100%;
      margin-left: 10px;
      -webkit-border-radius: 6px 0 6px 6px;
      -moz-border-radius: 6px 0 6px 6px;
      border-radius: 6px 0 6px 6px;
  }


  /* Modify the background color */
  .skin-green .main-header .navbar {
      background-color: black;
  }

  /* Modern top navbar skin - cosmetic only, markup/permission logic untouched */
  nav.navbar.bg-primary {
      background: #14161a !important;
      box-shadow: 0 2px 10px rgba(0,0,0,.35);
      padding-top: .45rem;
      padding-bottom: .45rem;
  }

  .navbar-nav .nav-link {
      position: relative;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: .01em;
      padding: .6rem .9rem !important;
      border-radius: 6px;
      white-space: nowrap;
      color: rgba(255, 255, 255, .82) !important;
      transition: background-color .15s ease, color .15s ease;
  }

  /* Jarak merata antar menu utama (Master, AP, Bank, ...) */
  .navbar-nav.mr-auto > .nav-item {
      margin: 0 2px;
  }

  /* Ikon menu utama senada warna dengan teks (bukan warna beda sendiri) -
     aksen warna cuma dipakai buat state hover/aktif, bukan dekorasi statis */
  .navbar-nav.mr-auto > .nav-item > .nav-link > .fa,
  .navbar-nav.mr-auto > .nav-item > .nav-link > .fas,
  .navbar-nav.mr-auto > .nav-item > .nav-link > .far {
      color: inherit;
      opacity: .85;
      margin-right: 6px;
      font-size: 12.5px;
      transition: opacity .15s ease;
  }

  /* Garis aksen biru tipis di bawah menu, muncul melebar saat hover/aktif -
     ini yang jadi "warna" utamanya, bukan ikon */
  .navbar-nav.mr-auto > .nav-item > .nav-link::before {
      content: "";
      position: absolute;
      left: 10px;
      right: 10px;
      bottom: 2px;
      height: 2px;
      border-radius: 2px;
      background: #4dabf7;
      transform: scaleX(0);
      transition: transform .18s ease;
  }

  .navbar-nav.mr-auto > .nav-item > .nav-link:hover,
  .navbar-nav.mr-auto > .nav-item > .nav-link:focus {
      color: #fff !important;
  }

  .navbar-nav.mr-auto > .nav-item > .nav-link:hover::before,
  .navbar-nav.mr-auto > .nav-item > .nav-link:focus::before,
  .navbar-nav.mr-auto > .nav-item.show > .nav-link::before {
      transform: scaleX(1);
  }

  /* Caret dropdown default Bootstrap (segitiga kecil) diganti chevron
     yang lebih modern, konsisten dengan panah submenu */
  .navbar-nav .dropdown-toggle::after {
      display: inline-block;
      content: "\f078";
      font-family: "Font Awesome 5 Free";
      font-weight: 900;
      font-size: 8px;
      border: none;
      vertical-align: 1px;
      margin-left: 6px;
      opacity: .55;
      transition: opacity .15s ease, transform .15s ease;
  }

  .navbar-nav .nav-item.dropdown:hover > .nav-link.dropdown-toggle::after,
  .navbar-nav .nav-item.dropdown.show > .nav-link.dropdown-toggle::after {
      opacity: 1;
  }

  /* Grup kanan (Log-out / Home / nama user) dipisah pakai garis tipis dan
     dirapatkan supaya terasa satu kelompok, bukan tercecer sendiri-sendiri */
  .navbar-nav.ml-auto {
      align-items: center;
      padding-left: 14px;
      margin-left: 6px;
      border-left: 1px solid rgba(255, 255, 255, .14);
  }

  .navbar-nav.ml-auto > .nav-item {
      margin: 0 2px;
  }

  .navbar-nav.ml-auto .navbar-text { white-space: nowrap; }

  /* Di bawah breakpoint navbar-expand-xl, navbar-collapse jadi menu vertikal
     (hamburger) - style grup kanan di atas didesain untuk baris horizontal
     desktop, align-items:center bikin item malah ke-tengah & border-left
     jadi salah arah di layout vertikal. Ditimpa di sini biar tetap rapi
     rata kiri menyatu dengan menu lain, dengan garis PEMISAH horizontal
     (bukan vertikal) di atasnya. */
  @media (max-width: 1199.98px) {
      .navbar-nav.ml-auto {
          align-items: stretch;
          padding-left: 0;
          margin-left: 0;
          margin-top: 8px;
          padding-top: 10px;
          border-left: none;
          border-top: 1px solid rgba(255, 255, 255, .14);
      }

      .navbar-nav.ml-auto > .nav-item {
          margin: 2px 0;
      }

      .navbar-nav.ml-auto .nav-link,
      .navbar-nav.ml-auto .navbar-text {
          display: flex;
          justify-content: flex-start;
          border-radius: 6px;
          width: 100%;
      }
  }

  .navbar-nav.ml-auto .nav-item:hover > .nav-link,
  .navbar-nav.ml-auto .nav-link:hover,
  .navbar-nav.ml-auto .nav-link:focus {
      background-color: rgba(255,255,255,.14);
      color: #fff !important;
  }

  .navbar-nav .dropdown-menu {
      border: none;
      border-radius: 10px;
      box-shadow: 0 10px 28px rgba(0,0,0,.35);
      padding: 6px;
      margin-top: 6px;
  }

  .navbar-nav .dropdown-menu .dropdown-item {
      display: flex;
      align-items: center;
      border-radius: 6px;
      font-size: 12.5px;
      line-height: 1.25;
      padding: 6px 10px;
      transition: background-color .12s ease;
  }

  .navbar-nav .dropdown-menu .dropdown-item:hover,
  .navbar-nav .dropdown-menu .dropdown-item:focus {
      background-color: #1e90ff !important;
      color: #fff !important;
  }

  /* Icon "chip" seragam - dropdown-item lama pakai bermacam-macam fa-icon
     langsung inline, di sini dibungkus visual bulat berwarna supaya rapi &
     konsisten tanpa perlu ganti tiap ikon satu-satu di ~30 tempat. */
  .navbar-nav .dropdown-menu .dropdown-item > .fa,
  .navbar-nav .dropdown-menu .dropdown-item > .fas,
  .navbar-nav .dropdown-menu .dropdown-item > .far,
  .navbar-nav .dropdown-menu .dropdown-item > .fab {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      height: 22px;
      min-width: 22px;
      border-radius: 7px;
      background: rgba(77, 171, 247, .16);
      color: #4dabf7;
      font-size: 11px;
      margin-right: 9px;
      transition: background-color .12s ease, color .12s ease;
  }

  .navbar-nav .dropdown-menu .dropdown-item:hover > .fa,
  .navbar-nav .dropdown-menu .dropdown-item:hover > .fas,
  .navbar-nav .dropdown-menu .dropdown-item:hover > .far,
  .navbar-nav .dropdown-menu .dropdown-item:hover > .fab {
      background: rgba(255, 255, 255, .28);
      color: #fff;
  }

  .navbar-nav .dropdown-menu .dropdown-item .menu-collapsed {
      white-space: nowrap;
  }

  /* ---- Menu bertingkat: kedalaman dibedakan lewat warna panel ----
     Markup-nya memakai .bg-dark bawaan Bootstrap (#343a40 !important) di
     <ul> MAUPUN tiap <a>, jadi dulu ketiga tingkat berwarna sama persis dan
     kedalaman tidak terbaca. Butirnya dibuat transparan supaya mewarisi
     warna panelnya, lalu tiap tingkat diberi warna sendiri yang makin
     terang ke dalam. */
  .navbar-nav .dropdown-menu {
      background-color: #1b1f27 !important;
  }
  .navbar-nav .dropdown-submenu > .dropdown-menu {
      background-color: #222733 !important;
  }
  .navbar-nav .dropdown-submenu .dropdown-submenu > .dropdown-menu {
      background-color: #293040 !important;
  }
  .navbar-nav .dropdown-menu .dropdown-item {
      background-color: transparent !important;
      color: rgba(255, 255, 255, .86) !important;
      padding: 7px 11px;
      border-radius: 8px;
  }

  /* Butir AKHIR yang di-hover: biru pekat, seperti semula. */
  .navbar-nav .dropdown-menu .dropdown-item:hover,
  .navbar-nav .dropdown-menu .dropdown-item:focus {
      background-color: #2f6bdd !important;
      color: #fff !important;
  }

  /* Induk yang punya submenu: ditandai LEBIH LEMBUT + garis tegak di tepi
     kiri, supaya "sedang dibuka" tidak tertukar dgn "sedang disorot".
     Aturan ini lebih spesifik dari aturan hover di atas, jadi menang. */
  .navbar-nav .dropdown-submenu > a.dropdown-item {
      position: relative;
  }
  .navbar-nav .dropdown-submenu > a.dropdown-item::before {
      content: "";
      position: absolute;
      left: 3px;
      top: 7px;
      bottom: 7px;
      width: 3px;
      border-radius: 3px;
      background: #4dabf7;
      opacity: 0;
      transition: opacity .15s ease;
  }
  .navbar-nav .dropdown-submenu:hover > a.dropdown-item,
  .navbar-nav .dropdown-submenu.open-submenu > a.dropdown-item {
      background-color: rgba(77, 171, 247, .14) !important;
      color: #fff !important;
  }
  .navbar-nav .dropdown-submenu:hover > a.dropdown-item::before,
  .navbar-nav .dropdown-submenu.open-submenu > a.dropdown-item::before {
      opacity: 1;
  }

  /* Judul kecil di kepala panel tingkat ketiga - tanpa ini Request/Approval
     melayang tanpa konteks, karena nama jenisnya tertinggal di panel induk. */
  .navbar-nav .dropdown-menu.ubf-sub::before {
      content: attr(data-judul);
      display: block;
      padding: 4px 11px 7px;
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, .38);
  }

  /* ================= NAVBAR: lima pembenahan (8 Okt 2026) ================
     Penanda halaman aktif, navbar menempel saat digulir, dropdown panjang
     dibatasi tingginya, animasi buka + aksen Log-out, dan kerapatan
     otomatis di layar sempit. */

  /* 2. Navbar menempel saat digulir. Halaman di aplikasi ini tabelnya
     panjang (GL, Other Receivable ratusan baris) - tanpa ini, pindah menu
     harus menggulir balik ke paling atas. z-index 1030 = tingkat navbar
     bawaan Bootstrap, jadi modal (1050) tetap menutupinya. */
  nav.navbar.bg-primary {
      position: sticky;
      top: 0;
      z-index: 1030;
  }

  /* 1B. Halaman yang SEDANG dibuka. Sebelumnya garis biru cuma muncul saat
     hover/terbuka, jadi begitu dropdown ditutup tidak ada petunjuk sama
     sekali sedang berada di menu mana. Kelas .aktif dipasang oleh JS di
     bawah, dicocokkan dari nama berkas yang sedang dibuka. */
  .navbar-nav.mr-auto > .nav-item > .nav-link.aktif {
      background-color: rgba(77, 171, 247, .18);
      color: #fff !important;
  }
  .navbar-nav.mr-auto > .nav-item > .nav-link.aktif > .fa,
  .navbar-nav.mr-auto > .nav-item > .nav-link.aktif > .fas,
  .navbar-nav.mr-auto > .nav-item > .nav-link.aktif > .far {
      opacity: 1;
  }

  /* 3. Dropdown panjang (menu AP isinya belasan butir) dibatasi tingginya.
     Kelasnya dipasang JS HANYA pada panel yang tidak memuat submenu -
     overflow-y pada panel ber-submenu akan memotong panel anaknya. */
  .navbar-nav .dropdown-menu.ubf-gulir {
      max-height: calc(100vh - 96px);
      overflow-y: auto;
      overscroll-behavior: contain;
  }
  .navbar-nav .dropdown-menu.ubf-gulir::-webkit-scrollbar { width: 8px; }
  .navbar-nav .dropdown-menu.ubf-gulir::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, .18);
      border-radius: 8px;
  }
  .navbar-nav .dropdown-menu.ubf-gulir::-webkit-scrollbar-track { background: transparent; }

  /* 4. Panel muncul dgn fade + geser tipis, tidak lagi mendadak. */
  .navbar-nav .dropdown-menu {
      animation: ubfTurun .14s ease-out both;
  }
  @keyframes ubfTurun {
      from { opacity: 0; transform: translateY(-6px); }
      to   { opacity: 1; transform: none; }
  }

  /* 4b. Log-out diberi semburat merah saat disorot - sebelumnya warnanya
     sama persis dgn Home, padahal akibatnya jauh berbeda. */
  .navbar-nav.ml-auto .nav-link.ubf-logout:hover,
  .navbar-nav.ml-auto .nav-link.ubf-logout:focus {
      background-color: rgba(239, 68, 68, .20) !important;
      color: #ffb4b4 !important;
  }

  /* 5. Sepuluh menu utama + tiga tombol kanan terlalu padat di layar 14".
     Hanya dirapatkan di lebar sempit; di layar besar tetap lapang. */
  @media (min-width: 1200px) and (max-width: 1500px) {
      .navbar-nav.mr-auto > .nav-item { margin: 0 1px; }
      .navbar-nav .nav-link { font-size: 12.5px; padding: .55rem .62rem !important; }
      .navbar-nav.mr-auto > .nav-item > .nav-link > .fa,
      .navbar-nav.mr-auto > .nav-item > .nav-link > .fas,
      .navbar-nav.mr-auto > .nav-item > .nav-link > .far {
          margin-right: 5px;
          font-size: 12px;
      }
  }


  .navbar-nav.ml-auto .nav-link {
      font-size: 12px;
      padding: .5rem .85rem !important;
      border-radius: 20px;
  }

  .navbar-nav.ml-auto .nav-link:hover { background-color: rgba(255,255,255,.15); }
  .navbar-nav.ml-auto .fa { margin-right: 5px; }

  .navbar-nav.ml-auto .navbar-text {
      background: rgba(255,255,255,.14);
      padding: .35rem .85rem;
      border-radius: 20px;
      font-size: 12px;
  }

  .swal-wide{
      width:400px !important;
      height: 200px !important;
  }

  /* Chrome, Safari, Edge */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}


</style>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">

<title>SB V2.0</title>

<!-- Bootstrap core CSS -->
<link href="../css/4.1.1/main.css?v=<?php echo @filemtime(__DIR__ . '/css/4.1.1/main.css'); ?>" rel="stylesheet">
<link href="../css/4.1.1/bootstrap.min.css" rel="stylesheet">
<link href="../css/4.1.1/datatables.min.css" rel="stylesheet">
<link href="../css/4.1.1/bootstrap-select.min.css" rel="stylesheet">
<!-- <link href="../fontawesome/css/font-awesome.min.css" rel="stylesheet">
-->
<link href="../fontawesome5/css/all.min.css" rel="stylesheet">
<link href="../fontawesome5/css/v4-shims.min.css" rel="stylesheet">
<link href="../css/4.1.1/datepicker3.css" rel="stylesheet">

<link href="../css/4.1.1/bootstrap-multiselect.min.css" rel="stylesheet">
<link href="../css/4.1.1/select2.min.css" rel="stylesheet">
<link href="../css/4.1.1/select2-bootstrap4.min.css" rel="stylesheet">
<link href="../css/4.1.1/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="../css/4.1.1/sweetalert2.min" rel="stylesheet">
<link rel="stylesheet"
      href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
  <link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css" /> -->
</head>

<!-- <body style="background-color: #F8F8FF;"> -->
  <body>


    <!-- Bootstrap NavBar -->


    <nav class="navbar navbar-expand-xl navbar-dark bg-primary">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="text-center mr-2">
  <a href="#">
    <img src="../img/NAG logo SIGN.png" alt="" style="max-width:40px; height:auto; display:block; margin:0 auto; margin-bottom: 2px;">
  </a>
  <a class="text-white d-block" style="font-size: 7px; text-decoration:none;">
    <b>PT. NIRWANA ALABARE GARMENT</b>
  </a>
</div>


    <?php include __DIR__ . '/menu.php'; ?>
</nav>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('select[name="nama_supp"]');

    selects.forEach(function(select) {
      if (!select.value || select.value === '') {
        const optAll = select.querySelector('option[value="ALL"]');
        if (optAll) {
          optAll.selected = true;
      }
  }
});
});
</script>

<script>
  // Dropdown submenu (.dropdown-submenu) dulu cuma kebuka via CSS :hover -
  // di Android/touch itu ga reliable (butuh tap dua kali: tap pertama cuma
  // "hover", baru tap kedua benar-benar jalan). Di sini ditambah trigger
  // klik/tap murni vanilla JS (bukan jQuery) karena file ini di-include di
  // ATAS tiap halaman, sebelum jQuery/bootstrap.bundle.js sempat dimuat oleh
  // masing-masing halaman di bagian bawahnya.
  document.addEventListener('click', function (e) {
    var trigger = e.target.closest ? e.target.closest('.dropdown-submenu > a') : null;

    if (trigger) {
      e.preventDefault();
      // stopPropagation saja tidak cukup - listener auto-close dropdown dari
      // Bootstrap JUGA terpasang di document (level yang sama persis),
      // stopPropagation cuma menghentikan event naik ke ancestor, bukan
      // listener lain di elemen yang sama. Makanya submenu sempat kebuka
      // lalu langsung ketutup lagi oleh Bootstrap. stopImmediatePropagation
      // mencegah listener lain di document (termasuk punya Bootstrap) ikut
      // jalan untuk klik ini.
      e.stopImmediatePropagation();

      var li = trigger.parentElement;
      var wasOpen = li.classList.contains('open-submenu');
      var siblingList = li.parentElement ? li.parentElement.children : [];

      Array.prototype.forEach.call(siblingList, function (sibling) {
        if (sibling !== li) {
          sibling.classList.remove('open-submenu');
        }
      });

      li.classList.toggle('open-submenu', !wasOpen);
      return;
    }

    var openSubmenus = document.querySelectorAll('.dropdown-submenu.open-submenu');
    Array.prototype.forEach.call(openSubmenus, function (li) {
      if (!li.contains(e.target)) {
        li.classList.remove('open-submenu');
      }
    });
  });
</script>


    <!-- sidebar-container END -->
<script>
/* ---- Navbar: penanda halaman aktif & dropdown panjang ----
   Dikerjakan di sini, bukan di PHP, supaya tidak perlu menyentuh ~100
   cabang menu satu per satu - cukup mencocokkan nama berkas yang sedang
   dibuka dgn href tiap tautan menu. */
(function () {
    var berkas = (location.pathname.split('/').pop() || '').toLowerCase();

    /* 1B. Menu utama yang memuat halaman ini ditandai. */
    if (berkas) {
        var tautan = document.querySelectorAll('.navbar-nav.mr-auto a[href]');
        for (var i = 0; i < tautan.length; i++) {
            var h = (tautan[i].getAttribute('href') || '').split('?')[0].split('/').pop().toLowerCase();
            if (!h || h !== berkas) { continue; }
            /* naik sampai ketemu <li> yang anak langsung dari .mr-auto */
            var n = tautan[i];
            while (n && n.parentNode && !(n.parentNode.classList && n.parentNode.classList.contains('mr-auto'))) {
                n = n.parentNode;
            }
            if (n && n.classList && n.classList.contains('nav-item')) {
                var utama = n.querySelector('.nav-link');
                if (utama) { utama.classList.add('aktif'); }
            }
            break;
        }
    }

    /* 3. Batas tinggi HANYA untuk panel tingkat satu yang tidak memuat
       submenu - kalau dipasang pada panel ber-submenu, panel anaknya ikut
       terpotong (jebakan yang sama dgn .table-responsive). */
    var panel = document.querySelectorAll('.navbar-nav.mr-auto > .nav-item > .dropdown-menu');
    for (var j = 0; j < panel.length; j++) {
        if (!panel[j].querySelector('.dropdown-submenu')) {
            panel[j].classList.add('ubf-gulir');
        }
    }
})();
</script>
