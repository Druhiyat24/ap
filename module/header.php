<?php
ini_set('memory_limit', '4096M');
set_time_limit(0);

session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php'; 
include_once __DIR__ . '/../tampilan.php';   // saklar tampilan baru/lama
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user == '') {
  // Sesi habis: bawa alamat halaman ini supaya setelah login kembali ke sini.
  $script = "<script>
  var p = location.pathname, i = p.lastIndexOf('/module/');
  var next = i >= 0 ? p.substring(i + 1) + location.search : '';
  window.location = '../function/logout.php' + (next ? '?next=' + encodeURIComponent(next) : '');</script>";
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


  /* Navbar & dropdown: lihat css/app-navbar.css */

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
<?php if (!TAMPILAN_BARU) { ?><link href="../css/app-navbar-lama.css" rel="stylesheet"><?php } ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">

<title><?php echo TAMPILAN_BARU ? 'NDS' : 'SB V2.0'; ?></title>
<?php if (TAMPILAN_BARU) { ?>
<!-- ikon tab, berlaku untuk semua halaman modul -->
<link rel="icon" type="image/png" sizes="32x32" href="../../images/nds/icon-32.png?v=2">
<link rel="icon" type="image/png" sizes="16x16" href="../../images/nds/icon-16.png?v=2">
<link rel="shortcut icon" href="../../images/nds/favicon.ico?v=2">
<link rel="apple-touch-icon" href="../../images/nds/icon-180.png?v=2">
<?php } ?>

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
<?php if (TAMPILAN_BARU) { ?>
<!-- Navbar & tema NDS -->
<link href="../css/app-navbar.css?v=<?php echo @filemtime(__DIR__ . '/css/app-navbar.css'); ?>" rel="stylesheet">
<link href="../css/app-tema.css?v=<?php echo @filemtime(__DIR__ . '/css/app-tema.css'); ?>" rel="stylesheet">
<?php } ?>
</head>

<!-- <body style="background-color: #F8F8FF;"> -->
  <body>


    <!-- Bootstrap NavBar -->


    <nav class="navbar navbar-expand-xl navbar-dark bg-primary">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
<?php if (TAMPILAN_BARU) { ?>
    <!-- Logo NDS (gaya di css/app-navbar.css) -->
    <a class="mn-brand" href="../index.php" title="Dashboard">
      <span class="mn-brand-ikon"><img src="../../images/nds/icon-64.png?v=2" alt=""></span>
      <span class="mn-brand-teks"><b>NDS</b><small>Nirwana Digital Solution</small></span>
    </a>
<?php } else { ?>
    <div class="text-center mr-2">
  <a href="#">
    <img src="../img/NAG logo SIGN.png" alt="" style="max-width:40px; height:auto; display:block; margin:0 auto; margin-bottom: 2px;">
  </a>
  <a class="text-white d-block" style="font-size: 7px; text-decoration:none;">
    <b>PT. NIRWANA ALABARE GARMENT</b>
  </a>
</div>
<?php } ?>


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
    var tautan = document.querySelectorAll('.navbar-nav.mr-auto a[href]');
    var menuIni = null;

    /* 1B. Menu utama yang memuat halaman ini ditandai. */
    if (berkas) {
        for (var i = 0; i < tautan.length; i++) {
            var h = (tautan[i].getAttribute('href') || '').split('?')[0].split('/').pop().toLowerCase();
            if (!h || h !== berkas) { continue; }
            menuIni = tautan[i];
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

    /* 4. Judul tab peramban = nama menu halaman ini. */
    function namaMenu(a) {
        var salin = a.cloneNode(true);
        var angka = salin.querySelectorAll('.badge, .notif-badge');   // angka notifikasi dibuang
        for (var k = 0; k < angka.length; k++) { angka[k].parentNode.removeChild(angka[k]); }
        return salin.textContent.replace(/\s+/g, ' ').trim();
    }
    /* nama submenu (atau menu utama) tempat tautan itu berada */
    function namaKelompok(a) {
        var li = a.parentNode.closest('.dropdown-submenu, .nav-item');
        var induk = li ? li.querySelector(':scope > a') : null;
        return (induk && induk !== a) ? namaMenu(induk) : '';
    }

    var judul = menuIni ? namaMenu(menuIni) : '';
    if (judul !== '') {
        /* Nama yang juga dipakai menu lain ("Payment Voucher" ada di Bank,
           Approval, Reverse) atau cuma satu kata ("Other", "Bank") diberi
           nama kelompoknya, mis. "Projection - Other" - kecuali nama
           kelompok itu sudah termuat ("Cash In", bukan "Cash - Cash In"). */
        var kembar = 0;
        for (var m = 0; m < tautan.length; m++) {
            var hm = (tautan[m].getAttribute('href') || '').split('?')[0].split('/').pop().toLowerCase();
            if (hm !== berkas && namaMenu(tautan[m]) === judul) { kembar++; }
        }
        var kelompok = namaKelompok(menuIni);
        var termuat = judul.toLowerCase().indexOf(kelompok.toLowerCase()) >= 0;
        if ((kembar > 0 || judul.indexOf(' ') < 0) && kelompok !== '' && !termuat) {
            judul = kelompok + ' - ' + judul;
        }
        document.title = judul;
    } else {
        /* Tidak ada di menu (Create/Edit/Detail): pakai judul besar halaman. */
        document.addEventListener('DOMContentLoaded', function () {
            var kepala = document.querySelectorAll('h1, h2, h3, h4, h5');
            for (var n = 0; n < kepala.length; n++) {
                if (kepala[n].closest('.modal, nav')) { continue; }
                var teks = kepala[n].textContent.replace(/\s+/g, ' ').trim();
                if (teks !== '') { document.title = teks; break; }
            }
        });
    }
})();
</script>
<?php if (TAMPILAN_BARU) { ?><script src="../js/app-navbar.js?v=<?php echo @filemtime(__DIR__ . '/js/app-navbar.js'); ?>"></script><?php } ?>
<?php if (TAMPILAN_BARU && TEMA_SELECT2) { ?><script src="../js/app-tema.js?v=<?php echo @filemtime(__DIR__ . '/js/app-tema.js'); ?>"></script><?php } ?>
<?php if (TAMPILAN_BARU && TEMA_INTERAKSI) { ?><script src="../js/app-interaksi.js?v=<?php echo @filemtime(__DIR__ . '/js/app-interaksi.js'); ?>"></script><?php } ?>
