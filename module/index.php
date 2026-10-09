<?php
ini_set('memory_limit', '4096M');
set_time_limit(0);

session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
include '../conn/conn.php';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user == '') {
    $script = "<script>
    window.location = 'function/logout.php';</script>";
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
  .div-dashboard{
    transform: scale(0.9);       /* skala 80% */
    transform-origin: 0 0;       /* titik awal zoom dari pojok kiri atas */
     width: 111.11%;
  }

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

  table{
      font-size: 12px;
  }

  table{
      font-size: 12px;
  }

  h2.text-center{
      font-size: 18px;
  }

  h3.text-center{
      font-size: 18px;
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



</style>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">

<title>SB V2.0</title>

<!-- Bootstrap core CSS -->
<link href="css/4.1.1/main.css" rel="stylesheet">  
<link href="css/4.1.1/bootstrap.min.css" rel="stylesheet">
<link href="css/4.1.1/datatables.min.css" rel="stylesheet">
<link href="css/4.1.1/bootstrap-select.min.css" rel="stylesheet">
<!-- <link href="fontawesome/css/font-awesome.min.css" rel="stylesheet">
--><link href="fontawesome5/css/all.min.css" rel="stylesheet">
<link href="fontawesome5/css/v4-shims.min.css" rel="stylesheet">
<link href="css/4.1.1/datepicker3.css" rel="stylesheet">

<link href="css/4.1.1/bootstrap-multiselect.min.css" rel="stylesheet">
<link href="css/4.1.1/select2.min.css" rel="stylesheet">
<link href="css/4.1.1/select2-bootstrap4.min.css" rel="stylesheet">
<link href="css/4.1.1/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="css/4.1.1/sweetalert2.min" rel="stylesheet">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
    <link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css" /> -->
</head>

<body>
    <!-- Bootstrap NavBar -->
    <nav class="navbar navbar-expand-xl navbar-dark bg-primary">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <!-- <a class="" href="#">
        <img src="img/NAG logo SIGN.png" alt="">
    </a>
    <a class=" text-white" style="font-size: 15px;"><b>PT.NIRWANA ALABARE GARMENT</b></a> -->

    <div class="text-center mr-2">
  <a href="#">
    <img src="img/NAG logo SIGN.png" alt="" style="max-width:40px; height:auto; display:block; margin:0 auto; margin-bottom: 2px;">
  </a>
  <a class="text-white d-block" style="font-size: 6px; text-decoration:none;">
    <b>PT. NIRWANA ALABARE GARMENT</b>
  </a>
</div>

    <?php $UBF_PRE = ''; include __DIR__ . '/menu.php'; ?>
</nav>

    <script>
      // Dropdown submenu (.dropdown-submenu) dulu cuma kebuka via CSS :hover -
      // di Android/touch itu ga reliable (butuh tap dua kali: tap pertama cuma
      // "hover", baru tap kedua benar-benar jalan). Di sini ditambah trigger
      // klik/tap murni vanilla JS (bukan jQuery) supaya jalan duluan sebelum
      // jQuery/bootstrap.bundle.js sempat dimuat di bagian bawah halaman.
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

    <!-- MAIN -->

    <div class="col p-3">
        <div class="col-md-2 pl-0 ">
          <select style="background-color: gray;" class="form-control selectpicker" name="pilih_dashboard" id="pilih_dashboard" onchange="ubahdashboard(this.value)" required>
             <option value="-" disabled selected="true">Select Dashboard</option> 
             <?php
             $pilih_dashboard ='';
             if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $pilih_dashboard = isset($_POST['pilih_dashboard']) ? $_POST['pilih_dashboard']: null;
            }                 
            $sql = mysql_query("select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and menurole.status = 'DSB'",$conn1);
            while ($row = mysql_fetch_array($sql)) {
                $data = isset($row['menu']) ? $row['menu'] : '-';
                $id = isset($row['id']) ? $row['id'] : '-';
                if($row['id'] == $_POST['pilih_dashboard']){
                    $isSelected = ' selected="selected"';
                }else{
                    $isSelected = '';
                }
                echo '<option value="'.$id.'"'.$isSelected.'">'. $data .'</option>';    
            }?>
        </select>
    </div>
    <div class="box " style="background-color: #F0F8FF;">

        <div id="isi_dashboard">
            <?php 
            $id_dsb = '';
            $querys = mysqli_query($conn1,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id, id_dsb from useraccess inner join menurole on menurole.menu = useraccess.menu left join menurole_dsb dsb on dsb.username = useraccess.username where useraccess.username = '$user' and menurole.status = 'DSB'");

            $rs = mysqli_fetch_array($querys);
            $id_dsb = isset($rs['id_dsb']) ? $rs['id_dsb'] : '';

            if ($id_dsb == '81') {
                include '../dashboard/dashboard-bank.php';    
            }elseif ($id_dsb == '80') {
                include '../dashboard/dashboard-ap.php';  
            }else{   
                include '../dashboard/welcome-page.php';  
            }
            ?>
        </div>
    </div>
</div>
<!-- Main Col END -->

</div><!-- body-row END -->

<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>  
<script type="text/javascript" src="css/4.1.1/fusionchart/fusioncharts.js"></script>
<script type="text/javascript" src="css/4.1.1/fusionchart/themes/fusioncharts.theme.fusion.js"></script>
<script type="text/javascript" src="css/4.1.1/apexchart/apexcharts.js"></script>
<script type="text/javascript" src="css/4.1.1/canvaschart/canvasjs.min.js"></script>
<script type="text/javascript" src="css/4.1.1/canvaschart/canvasjs.stock.min.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/index.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/xy.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/animated.js"></script>
<script type="text/javascript" src="css/4.1.1/amchart/radar.js"></script>
<script language="JavaScript" src="css/4.1.1/bootstrap-select.min.js"></script>
<script language="JavaScript" src="css/4.1.1/select2.full.min.js"></script>
<!--  <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script type="text/javascript" src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script> -->
    <script>
  // Hide submenus
  $('#body-row .collapse').collapse('hide'); 

// Collapse/Expand icon
$('#collapse-icon').addClass('fa-angle-double-left'); 

// Collapse click
$('[data-toggle=sidebar-colapse]').click(function() {
    SidebarCollapse();
});

function SidebarCollapse () {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

    // Treating d-flex/d-none on separators with title
    var SeparatorTitle = $('.sidebar-separator-title');
    if ( SeparatorTitle.hasClass('d-flex') ) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }

    // Collapse/Expand icon
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>

<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });

      //Initialize Select2 Elements
      $('.select2').select2({
        theme: 'bootstrap4',
    });

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4',
        width: '100%',
        containerCssClass: 'form-control-sm'
    })

</script>

<script type="text/javascript">
    function ubahdashboard(id){
        var id_dsb = id;
        $.ajax({
            type: 'POST', 
            url: 'ubah_dashboard.php', 
            data: {'id_dsb':id_dsb},
            success: function(response) { 
                $('#isi_dashboard').html(response); 
            }
        });
    }
</script>



<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
   
<script>
    var options = {
      series: [{
          name: 'Total Cash',
          data: [<?php
              // Gabungan CASH IN BANK + CASH ON HAND per bulan - sama persis
              // dengan query di dashboard/dashboard-bank.php.
              $sql1 = mysqli_query($conn1, "
                SELECT GROUP_CONCAT(
                    IF(t.bln <= MONTH(CURDATE()), t.total, 0)
                    ORDER BY t.bln SEPARATOR ','
                ) AS data
                FROM (
                    SELECT pa.bln, ROUND(SUM(GREATEST(pa.bal,0))/1000000,2) AS total
                    FROM (
                        SELECT m.bank_account AS acc, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(m.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=m.curr AND mr.v_codecurr='PAJAK'
                                      AND mr.tanggal <= COALESCE(
                                          (SELECT MAX(r2.transaksi_date) FROM b_reportbank r2
                                           WHERE r2.akun = m.bank_account AND r2.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r2.status != 'Cancel' AND r2.no_doc NOT LIKE 'FX/%'),
                                          LEAST(mo.month_end, CURDATE())
                                      )
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM b_masterbank m
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_bank s ON s.account = m.bank_account
                        LEFT JOIN b_reportbank r ON r.akun = m.bank_account AND r.no_doc NOT LIKE 'FX/%'
                        GROUP BY m.bank_account, mo.bln, mo.month_end, s.amount

                        UNION ALL

                        SELECT c.no_coa AS acc, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(s.curr IS NULL OR s.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=s.curr AND mr.v_codecurr='HARIAN' AND mr.tanggal <= LEAST(mo.month_end, CURDATE())
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM mastercoa_v2 c
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_pettycash s ON s.account = c.no_coa
                        LEFT JOIN c_report_pettycash r ON r.akun = c.no_coa
                        WHERE c.ind_categori5 = 'KAS'
                        GROUP BY c.no_coa, mo.bln, mo.month_end, s.amount, s.curr
                    ) pa
                    GROUP BY pa.bln
                ) t
              ");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_and_bank.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_cib').html(data);
                        $('#jdl_cib').html(title + ' <?= date("Y"); ?>');
                        $('#modaldetcib').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                       console.log(xhr);
                   }
               });         

            }
        }
    },
    colors: ['#008B8B'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"WITH RECURSIVE bln AS (
    SELECT 1 AS m
    UNION ALL
    SELECT m+1 FROM bln WHERE m < 12
)
SELECT GROUP_CONCAT(CONCAT('''', DATE_FORMAT(DATE(CONCAT(YEAR(CURDATE()), '-', m, '-01')), '%b %Y'), '''') ORDER BY m) AS nama
FROM bln");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#charttc"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Cash in Banks',
          data: [<?php
              // Ending balance riil per bulan (tahun berjalan) - sama persis dengan
              // query di dashboard/dashboard-bank.php (sumber card+chart CASH IN BANK).
              $sql1 = mysqli_query($conn1, "
                SELECT GROUP_CONCAT(
                    IF(t.bln <= MONTH(CURDATE()), t.total, 0)
                    ORDER BY t.bln SEPARATOR ','
                ) AS data
                FROM (
                    SELECT pa.bln,
                           ROUND(SUM(GREATEST(pa.bal,0))/1000000,2) AS total
                    FROM (
                        SELECT m.bank_account, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(m.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=m.curr AND mr.v_codecurr='PAJAK'
                                      AND mr.tanggal <= COALESCE(
                                          (SELECT MAX(r2.transaksi_date) FROM b_reportbank r2
                                           WHERE r2.akun = m.bank_account AND r2.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r2.status != 'Cancel' AND r2.no_doc NOT LIKE 'FX/%'),
                                          LEAST(mo.month_end, CURDATE())
                                      )
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM b_masterbank m
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_bank s ON s.account = m.bank_account
                        LEFT JOIN b_reportbank r ON r.akun = m.bank_account AND r.no_doc NOT LIKE 'FX/%'
                        GROUP BY m.bank_account, mo.bln, mo.month_end, s.amount
                    ) pa
                    GROUP BY pa.bln
                ) t
              ");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_in_bank.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_cib').html(data);
                        $('#jdl_cib').html(title + ' <?= date("Y"); ?>');
                        $('#modaldetcib').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                       console.log(xhr);
                   }
               });         

            }
        }
    },
    colors: ['#008B8B'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"WITH RECURSIVE bln AS (
    SELECT 1 AS m
    UNION ALL
    SELECT m+1 FROM bln WHERE m < 12
)
SELECT GROUP_CONCAT(CONCAT('''', DATE_FORMAT(DATE(CONCAT(YEAR(CURDATE()), '-', m, '-01')), '%b %Y'), '''') ORDER BY m) AS nama
FROM bln");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartcib"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Cash On Hand',
          data: [<?php
              // Ending balance riil per bulan (tahun berjalan) untuk akun kas -
              // sama persis dengan query di dashboard/dashboard-bank.php
              // (sumber card+chart CASH ON HAND).
              $sql1 = mysqli_query($conn1, "
                SELECT GROUP_CONCAT(
                    IF(t.bln <= MONTH(CURDATE()), t.total, 0)
                    ORDER BY t.bln SEPARATOR ','
                ) AS data
                FROM (
                    SELECT pa.bln,
                           ROUND(SUM(GREATEST(pa.bal,0))/1000000,2) AS total
                    FROM (
                        SELECT c.no_coa, mo.bln,
                               (COALESCE(s.amount,0) + COALESCE(SUM(
                                   CASE WHEN r.transaksi_date <= LEAST(mo.month_end, CURDATE()) AND r.status != 'Cancel'
                                        THEN r.debit - r.credit ELSE 0 END
                               ),0))
                               * IF(s.curr IS NULL OR s.curr='IDR',1,
                                   (SELECT mr.rate FROM masterrate mr
                                    WHERE mr.curr=s.curr AND mr.v_codecurr='HARIAN' AND mr.tanggal <= LEAST(mo.month_end, CURDATE())
                                    ORDER BY mr.tanggal DESC LIMIT 1)
                               ) AS bal
                        FROM mastercoa_v2 c
                        CROSS JOIN (
                            SELECT n AS bln, LAST_DAY(MAKEDATE(YEAR(CURDATE()),1) + INTERVAL (n-1) MONTH) AS month_end
                            FROM (SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6
                                  UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12) nn
                        ) mo
                        LEFT JOIN b_saldoawal_pettycash s ON s.account = c.no_coa
                        LEFT JOIN c_report_pettycash r ON r.akun = c.no_coa
                        WHERE c.ind_categori5 = 'KAS'
                        GROUP BY c.no_coa, mo.bln, mo.month_end, s.amount, s.curr
                    ) pa
                    GROUP BY pa.bln
                ) t
              ");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_on_hand.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_coh').html(data);
                        $('#jdl_coh').html(title + ' <?= date("Y"); ?>');
                        $('#modaldetcoh').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                       console.log(xhr);
                   }
               });         

            }
        }
    },
    colors: ['#008B8B'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
     $sql_bln = mysqli_query($conn2, "
WITH RECURSIVE bln AS (
    SELECT 1 AS m
    UNION ALL
    SELECT m+1 FROM bln WHERE m < 12
)
SELECT GROUP_CONCAT(CONCAT('''', DATE_FORMAT(DATE(CONCAT(YEAR(CURDATE()), '-', m, '-01')), '%b %Y'), '''') ORDER BY m) AS nama
FROM bln
");

      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartcoh"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M");
              $tahun = date("Y"); 

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.01','2.20.02')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.01','1.10.02')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloantotal"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.02')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.02')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloanusd"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.01')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.01')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
      ?>],
  position: 'bottom',
  axisBorder: {
    show: false
},
axisTicks: {
    show: false
},
crosshairs: {
    fill: {
      type: 'gradient',
      gradient: {
        colorFrom: '#D8E3F0',
        colorTo: '#BED1E6',
        stops: [0, 100],
        opacityFrom: 0.4,
        opacityTo: 0.5,
    }
}
},
tooltip: {
    enabled: true,
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloanidr"), options);
chart.render();
</script>

<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        $tahun = date("Y");
        // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.01')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.01')) a");
        // $row_bli = mysqli_fetch_array($sql_bli);
        // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

        $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
        $row1 = mysqli_fetch_array($sql1);
        $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

        $chart_bli = (abs($total_bli) / $limit_idr) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: <?= $chart_bli ?>,
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>

<script>
    var options = {
series: [{
    name: 'Bank Loan',
    data: [<?php

    $bulan_list = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $bulan_list[] = [
            'bulan' => date('M', $date),
            'tahun' => date('Y', $date)
        ];
    }

    $data = [];

    foreach ($bulan_list as $bln) {

        $tahun_tb = $bln['tahun'];
        $bulan_tb = $bln['bulan'];

        $sql = mysqli_query($conn2,"
            SELECT 
            ROUND(
                ABS(
                    SUM(IF(no_coa='2.20.01', saldo_$bulan_tb,0)) +
                    SUM(IF(no_coa='1.10.01' AND saldo_$bulan_tb < 0, saldo_$bulan_tb,0))
                ) / 1000000,2
            ) total
            FROM b_trial_balance_$tahun_tb
        ");

        $row = mysqli_fetch_assoc($sql);
        $data[] = $row['total'] ?? 0;
    }

    echo implode(",", $data);

    ?>]
}],
chart: {
    height: 350,
    type: 'bar'
},
colors: ['#008B8B'],
plotOptions: {
    bar: {
        borderRadius: 5,
        dataLabels: {
            position: 'top'
        }
    }
},
dataLabels: {
    enabled: true,
    formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},
xaxis: {
categories: [<?php

    $cat = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $cat[] = "'".date('M Y', $date)."'";
    }

    echo implode(",", $cat);

?>],
axisBorder: { show: false },
axisTicks: { show: false }
},
yaxis: {
labels: {
    formatter: function (val) {
        return val.toLocaleString('en-US');
    }
}
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv2"), options);
chart.render();

</script>


<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv3");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        // $sql_blu = mysqli_query($conn2,"select total,(total * rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.02')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
        // $row_blu = mysqli_fetch_array($sql_blu);
        // $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
        // $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

        $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
        $row1 = mysqli_fetch_array($sql1);
        $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
        $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

        if ($saldoakhir > 0) {
            $saldoakhirnya = 0;
        }else{
            $saldoakhirnya = $saldoakhir;
        }

        $chart_blu = (abs($saldoakhirnya * $rates3) / $limit_convert) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: '<?= $chart_blu ?>',
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>
<!-- select CONCAT('round(abs(sum(saldo2 /1000000)),2) saldo2') -->
<script>
   var options = {
series: [{
    name: 'Bank Loan',
    data: [<?php

    $bulan_list = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $bulan_list[] = [
            'bulan' => date('M', $date),
            'tahun' => date('Y', $date)
        ];
    }

    $data = [];

    foreach ($bulan_list as $bln) {

        $tahun_tb = $bln['tahun'];
        $bulan_tb = $bln['bulan'];

        $sql = mysqli_query($conn2,"
            SELECT 
            ROUND(
                ABS(
                    SUM(IF(no_coa='2.20.02', saldo_$bulan_tb,0)) +
                    SUM(IF(no_coa='1.10.02' AND saldo_$bulan_tb < 0, saldo_$bulan_tb,0))
                ) / 1000000,2
            ) total
            FROM b_trial_balance_$tahun_tb
        ");

        $row = mysqli_fetch_assoc($sql);
        $data[] = $row['total'] ?? 0;
    }

    echo implode(",", $data);

    ?>]
}],
chart: {
    height: 350,
    type: 'bar'
},
colors: ['#008B8B'],
plotOptions: {
    bar: {
        borderRadius: 5,
        dataLabels: {
            position: 'top'
        }
    }
},
dataLabels: {
    enabled: true,
    formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},
xaxis: {
categories: [<?php
    $cat = [];
    for ($i = 3; $i >= 1; $i--) {
        $date = strtotime("-$i month");
        $cat[] = "'".date('M Y', $date)."'";
    }
    echo implode(",", $cat);
?>],
axisBorder: { show: false },
axisTicks: { show: false }
},
yaxis: {
labels: {
    formatter: function (val) {
        return val.toLocaleString('en-US');
    }
}
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv4"), options);
chart.render();

</script>


<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv5");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        $sql_blu = mysqli_query($conn2,"select total,(total * rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.02')
            UNION
            select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
        $row_blu = mysqli_fetch_array($sql_blu);
        $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
        $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

        $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
        $row1 = mysqli_fetch_array($sql1);
        $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
        $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

        // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.01')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.01')) a");
        // $row_bli = mysqli_fetch_array($sql_bli);
        // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

        $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
        $row1 = mysqli_fetch_array($sql1);
        $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

        $chart_bl = (abs($total_bli) + abs($saldoakhir * $rates3)) / ($limit_idr + $limit_convert) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: <?= $chart_bl ?>,
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>


<script>

var options = {
series: [{
    name: 'Bank Loan',
    data: [<?php

        $bulan_list = [];
        for ($i = 3; $i >= 1; $i--) {
            $date = strtotime("-$i month");
            $bulan_list[] = [
                'bulan' => date('M', $date),
                'tahun' => date('Y', $date)
            ];
        }

        $data = [];

        foreach ($bulan_list as $bln) {

            $bulan_tb = $bln['bulan'];
            $tahun_tb = $bln['tahun'];

            $sql = mysqli_query($conn2,"
                SELECT 
                ROUND(
                    ABS(
                        SUM(IF(no_coa IN ('2.20.01','2.20.02'), saldo_$bulan_tb,0)) +
                        SUM(IF(no_coa IN ('1.10.01','1.10.02') AND saldo_$bulan_tb < 0, saldo_$bulan_tb,0))
                    ) / 1000000,2
                ) total
                FROM b_trial_balance_$tahun_tb
            ");

            $row = mysqli_fetch_assoc($sql);
            $data[] = $row['total'] ?? 0;
        }

        echo implode(",", $data);

    ?>]
}],
chart: {
    height: 350,
    type: 'bar'
},
colors: ['#008B8B'],
plotOptions: {
    bar: {
        borderRadius: 5,
        dataLabels: {
            position: 'top'
        }
    }
},
dataLabels: {
    enabled: true,
    formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},
xaxis: {
    categories: [<?php
        $cat = [];
        for ($i = 3; $i >= 1; $i--) {
            $date = strtotime("-$i month");
            $cat[] = "'".date('M Y', $date)."'";
        }
        echo implode(",", $cat);
    ?>],
    axisBorder: { show: false },
    axisTicks: { show: false }
},
yaxis: {
    labels: {
        show: false,
        formatter: function (val) {
            return val.toLocaleString('en-US');
        }
    }
},
tooltip: {
    enabled: true,
    y: {
        formatter: function(val) {
            return val.toLocaleString('en-US') + " Mio";
        }
    }
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv6"), options);
chart.render();

</script>

<!-- AP -->

<script type="text/javascript">
    function fmtCompactIDR(val) {
        var n = Math.abs(val);
        var sign = val < 0 ? '-' : '';
        if (n >= 1e9) return sign + 'IDR ' + (n / 1e9).toFixed(1) + 'B';
        if (n >= 1e6) return sign + 'IDR ' + (n / 1e6).toFixed(1) + 'M';
        if (n >= 1e3) return sign + 'IDR ' + (n / 1e3).toFixed(1) + 'K';
        return sign + 'IDR ' + n.toLocaleString('en-US');
    }

    var optionsSupp10 = {
        series: [{
            name: 'Amount',
            data: [<?php
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(total ORDER BY total DESC) total from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),'-01-01') and CURRENT_DATE() GROUP BY nama_supp
                    UNION
                    select nama_supp,-sum(dpp) total from dsb_ap_retur where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),'-01-01') and CURRENT_DATE() GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a");
                $row = mysqli_fetch_array($sql);
                $total = $row['total'];
                echo $total;
                ?>]
            }],
            chart: {
                height: 420,
                type: 'bar',
                toolbar: { show: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 500
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 0,
                    dataLabels: { position: 'top' }
                }
            },
            colors: ['#2a78d6'],
            dataLabels: {
                enabled: true,
                formatter: fmtCompactIDR,
                offsetY: -18,
                style: {
                    fontSize: '10px',
                    fontWeight: 600,
                    colors: ['#14181b']
                }
            },
            xaxis: {
                categories: [<?php
                    $sql = mysqli_query($conn2,'select GROUP_CONCAT(concat("""",nama_supp,"""") ORDER BY total DESC) nama_supp from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),\'-01-01\') and CURRENT_DATE() GROUP BY nama_supp
                        UNION
                        select nama_supp,-sum(dpp) total from dsb_ap_retur where tgl_bpb BETWEEN CONCAT(YEAR(CURRENT_DATE),\'-01-01\') and CURRENT_DATE() GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a');
                    $row = mysqli_fetch_array($sql);
                    $nama_supp = $row['nama_supp'];
                    echo $nama_supp;
                    ?>],
                labels: {
                    rotate: -45,
                    rotateAlways: true,
                    trim: true,
                    style: { fontSize: '11px', colors: '#52514e' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                tickAmount: 4,
                labels: {
                    formatter: fmtCompactIDR,
                    style: { fontSize: '10px', colors: '#8b8f96' }
                }
            },
            grid: {
                borderColor: '#e1e0d9',
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true } }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'IDR ' + Math.round(val).toLocaleString('en-US');
                    }
                }
            }
        };

        var chartSupp10 = new ApexCharts(document.querySelector("#chart_supptop10"), optionsSupp10);
        chartSupp10.render();
    </script>


    <script type="text/javascript">
        var options = {
          series: [{
              name: 'NET PURCHASE',
              data: [<?php 
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_purchase,0) - COALESCE(ttl_retur,0))/1000000,2)) net_purchase from (select bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a LEFT JOIN
                    (select sum(dpp)ttl_purchase,bln1 from (select dpp,MONTH(tgl_bpb) bln1 from dsb_ap_purchase ) a GROUP BY bln1) b on b.bln1 = a.bulan LEFT JOIN
                    (select sum(dpp)ttl_retur,bln2 from (select dpp,MONTH(tgl_bpb) bln2 from dsb_ap_retur ) a GROUP BY bln2) c on c.bln2 = a.bulan");
                $row = mysqli_fetch_array($sql);
                $total = $row['net_purchase'];
                echo $total;

                ?>]
            }, {
              name: 'PAYMENT',
              data: [<?php 
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_bpb,0) + COALESCE(ttl_kbon,0) + COALESCE(ttl_lp,0))/1000000,2)) payment from (select bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a LEFT JOIN
                    (select bln1,sum(end_balance_idr) ttl_bpb from (select MONTH(tgl_bpb) bln1,end_balance_idr from dsb_ap_bpb where end_balance_idr != 0 and tgl_bpb >= '2024-01-01') a GROUP BY bln1) b on b.bln1 = a.bulan LEFT JOIN
                    (select bln2,sum(end_balance_idr) ttl_kbon from (select MONTH(tgl_kbon) bln2,end_balance_idr from dsb_ap_kbon where end_balance_idr != 0 and tgl_kbon >= '2024-01-01') a GROUP BY bln2) c on c.bln2 = a.bulan LEFT JOIN
                    (select bln3,sum(end_balance_idr) ttl_lp from (select MONTH(tgl_payment) bln3,end_balance_idr from dsb_ap_lp where end_balance_idr != 0 and tgl_payment >= '2024-01-01') a GROUP BY bln3) d on d.bln3 = a.bulan");
                $row = mysqli_fetch_array($sql);
                $total = $row['payment'];
                echo $total;

                ?>]
            }],
            chart: {
              type: 'bar',
              height: 350
          },
          plotOptions: {
              bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
          enabled: false
      },
      stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
      },
      xaxis: {
          categories: [<?php 
            $sql = mysqli_query($conn2,'select GROUP_CONCAT(concat("""",label,"""")) label from (select concat(nama_bulan_singkat," ",tahun) label from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a');
            $row = mysqli_fetch_array($sql);
            $label = $row['label'];
            echo $label;
            
            ?>],
        },
        yaxis: {
          title: {
            text: 'MIO'
        }
    },
    fill: {
      opacity: 1
  },
  tooltip: {
      y: {
        formatter: function (val) {
          return "IDR " + val + " MIO"
      }
  }
}
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>

<script>
    document.getElementById('refreshDashboard').addEventListener('click', function () {
        const infoDiv = document.getElementById('refreshInfo');
        const lastUpdate = document.getElementById('lastUpdate');

        infoDiv.innerHTML = '⏳ Sedang memperbarui data...<br>';

        fetch('http://localhost/ap_dev/dashboard/run_all_summary.php')
        .then(response => {
            if (!response.body) throw new Error('Stream tidak tersedia');

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let allOutput = '';

            function read() {
                reader.read().then(({ done, value }) => {
                    if (done) {
                        // Jika selesai membaca semua dan tidak ada error, reload otomatis
                        if (!allOutput.includes('Not Found') && !allOutput.includes('❌')) {
                            infoDiv.innerHTML += '<br>✅ Selesai! Me-refresh halaman...';
                            lastUpdate.textContent = "📅 Terakhir update: baru saja";
                            setTimeout(() => location.reload(), 500);
                        } else {
                            infoDiv.innerHTML += '<br>⚠️ Proses selesai, tapi ada error. Cek konsol.';
                            console.error(allOutput);
                        }
                        return;
                    }

                    const chunk = decoder.decode(value, { stream: true });
                    allOutput += chunk;

                    const lines = chunk.split('\n');
                    lines.forEach(line => {
                        if (line.includes('▶️') || line.includes('⏳')) {
                            infoDiv.innerHTML += line + '<br>';
                        }
                    });

                    infoDiv.scrollTop = infoDiv.scrollHeight;
                    read();
                });
            }

            read();
        })
        .catch(err => {
            infoDiv.innerHTML = '❌ Gagal memperbarui data.<br>' + err;
        });
    });
</script>






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
</body>

</html>
