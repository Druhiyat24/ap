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

    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav mr-auto">
        <!-- <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
      </div> -->

      <!-- navbar master -->
      <li class="nav-item dropdown active">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="fa fa-book mr-1"></span> Master
      </a>
      <div class="dropdown-menu bg-dark " aria-labelledby="navbarDropdown" style="width:200px;">
          <?php
          $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Master'");
          $rs = mysqli_fetch_array($querys);
          $menu = isset($rs['menu']) ? $rs['menu'] :0;
          $id = isset($rs['id']) ? $rs['id'] :0;

          if($id == '49'){                             
            echo '
            <a href="AP/master-cash-flow.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-paperclip fa-fw"></span>
            <span class="menu-collapsed">Cash Flow - Accounting</span>

            </a>
            <a href="AP/master-cash-flow-mapping.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-random fa-fw"></span>
            <span class="menu-collapsed">Cash Flow - Finance</span>
            </a>
            <a href="AP/master-coa-category1.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-paperclip fa-fw "></span>
            <span class="menu-collapsed">Category COA</span>
            </a>
            <a href="AP/master-coa.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-paperclip fa-fw "></span>
            <span class="menu-collapsed">Chart Of Account</span>
            </a>
            <a href="AP/master-costcenter.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-paperclip fa-fw "></span>
            <span class="menu-collapsed">Cost Center</span>
            </a>
            <a href="AP/master-profit-center.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-paperclip fa-fw "></span>
            <span class="menu-collapsed">Profit Center</span>
            </a>
            <a href="AP/master-bank.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-paperclip fa-fw "></span>
            <span class="menu-collapsed">Bank</span>
            </a>
            <a href="AP/master-mapping-memo.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-paperclip fa-fw "></span>
            <span class="menu-collapsed">Mapping Memo</span>
            </a>
            <a href="AP/master-supplier-bank.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-university fa-fw "></span>
              <span class="menu-collapsed">Master Bank Supplier</span>
              </a>
            <a href="AP/master-pilihan-bank.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-university fa-fw "></span>
              <span class="menu-collapsed">Master Pilihan Bank</span>
              </a>
            <a href="AP/master-rate.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw "></span>
              <span class="menu-collapsed">Rate</span>
              </a>
            ';
        }else{
            echo '';
        }
        ?>  
    </div>
</li>

<!-- navbar AP -->
<li class="nav-item dropdown active">
  <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-paypal mr-1"></span> AP<span class="caret"></span></a>
  <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
      $rs = mysqli_fetch_array($querys);
      $group = $rs['Groupp'];
      $pur = $rs['purchasing'];
      $app_po = $rs['approve_po'];

      $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%' and useraccess.menu != 'Document Handover - BPB & SJ' and useraccess.menu != 'Document Handover - Accept BPB Warehouse To Accounting' and useraccess.menu != 'Maintain BPB' and useraccess.menu != 'Acct - Rekonsiliasi Jurnal-BPB' and useraccess.menu != 'Acct - Repost Jurnal BPB' and useraccess.menu not like '%create%' and profit_center != 'NAK' and  useraccess.menu != 'Update BPB Fabric' group by username");
      while($rss = mysqli_fetch_array($queryss)){
        $menu = isset($rss['ket']) ? $rss['ket'] :0;
        $id = isset($rss['id']) ? $rss['id'] :0;

        $sql = mysqli_query($conn2,"select count(distinct(no_bpb)) as no_bpb from bpb_new where status = 'GMF' and profit_center = 'NAG'");
        $row = mysqli_fetch_array($sql);
        $count = isset($row['no_bpb']) ? $row['no_bpb'] : 0;
        if($count != '0'){
            $notif = '<span class="notif-badge">'.$count.'</span>';
        }else{
            $notif = '';
        } 

        $sql1 = mysqli_query($conn2,"select count(distinct(no_ro)) as no_ro from bppb_new where status = 'GMF'");
        $row1 = mysqli_fetch_array($sql1);
        $count1 = $row1['no_ro'];
        $countjml = $count + $count1;
        if($count1 != '0'){
            $notif1 = '<span class="notif-badge">'.$count1.'</span>';
        }else{
            $notif1 = '';
        }


    } 

    $queryss2 = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%update%'  and  useraccess.menu != 'Update BPB Fabric' group by username");
    $menu2 = '';
    while($rss2 = mysqli_fetch_array($queryss2)){
        $menu2 = isset($rss2['ket']) ? $rss2['ket'] :0;
    }              

    if($menu == 'Y' || $menu2 == 'Y'){  
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-envelope-o fa-fw "></span>
        <span class="menu-collapsed">BPB Garment</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">';
        if($id == '1'){ 
         echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>';
     }elseif($id == '2'){ 
         echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>';
     }elseif($id == '19'){ 
         echo'<a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '20'){ 
         echo'<a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '1,2'){ 
         echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>';
     }elseif($id == '1,19'){ 
         echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '1,20'){ 
         echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '2,19'){ 
         echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '2,20'){ 
         echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '19,20'){ 
         echo'<a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>
         <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '1,2,19'){ 
         echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '1,2,20'){ 
         echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '2,19,20'){ 
         echo'<a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>
         <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '1,2,19,20'){ 
         echo'<a href="AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>
         <a href="AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>
         ';
     }else{
        echo '';
    }
    if($menu2 == 'Y'){
        echo '<a href="AP/update_bpb.php" class="dropdown-item bg-dark text-white">
        <span  class="fa fa-pencil fa-fw "></span>
        <span class="menu-collapsed">Update BPB</span>
        </a>
        <a href="AP/report-faktur-pajak.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-files-o fa-fw "></span>
        <span class="menu-collapsed">Report FP</span>
        </a>';
    }else{
    }

    echo'</ul>
    </li>';
}
?>


<!-- BPB Knitting -->

<?php
$querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
$rs = mysqli_fetch_array($querys);
$group = $rs['Groupp'];
$pur = $rs['purchasing'];
$app_po = $rs['approve_po'];
$menu_nak = '';
$queryss_nak = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%' and useraccess.menu != 'Document Handover - BPB & SJ' and useraccess.menu != 'Document Handover - Accept BPB Warehouse To Accounting' and useraccess.menu != 'Maintain BPB' and useraccess.menu not like '%create%' and profit_center = 'NAK' group by username");
while($rss_nak = mysqli_fetch_array($queryss_nak)){
    $menu_nak = isset($rss_nak['ket']) ? $rss_nak['ket'] :0;
    $id_nak = isset($rss_nak['id']) ? $rss_nak['id'] :0;

    $sql_nak = mysqli_query($conn2,"select count(distinct(no_bpb)) as no_bpb from bpb_new where status = 'GMF' and profit_center = 'NAK'");
    $row_nak = mysqli_fetch_array($sql_nak);
    $count_nak = $row_nak['no_bpb'];
    if($count_nak != '0'){
        $notif_nak = '<span class="notif-badge">'.$count_nak.'</span>';
    }else{
        $notif_nak = '';
    } 


} 


if($menu_nak == 'Y'){  
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-envelope-o fa-fw "></span>
    <span class="menu-collapsed">BPB Knitting</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';
    if($id_nak == '87'){ 
        echo'<a href="AP/verifikasibpb_knitting.php" class="dropdown-item bg-dark text-white">
        <span  class="fa fa-share fa-fw "></span>
        <span class="menu-collapsed">Verifikasi BPB</span>
        </a>';
    }elseif($id_nak == '86'){
        echo'<a href="AP/formapprovebpb_knitting.php" class="dropdown-item bg-dark text-white">
        <span  class="fa fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Approve BPB</span>
        '.$notif.'
        </a>';
    }elseif($id_nak == '86,87'){ 
       echo'<a href="AP/verifikasibpb_knitting.php" class="dropdown-item bg-dark text-white">
       <span  class="fa fa-share fa-fw "></span>
       <span class="menu-collapsed">Verifikasi BPB</span>
       </a>';
       echo'<a href="AP/formapprovebpb_knitting.php" class="dropdown-item bg-dark text-white">
       <span  class="fa fa fa-thumbs-up fa-fw "></span>
       <span class="menu-collapsed">Approve BPB</span>
       '.$notif_nak.'
       </a>';
   }else{
    echo '';
}
echo'</ul>
</li>';
}
?>

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'FTR'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

if($id == '4'){  
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span s class="fa fa-money fa-fw "></span>
    <span class="menu-collapsed">FTR</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
    <a href="AP/ftrcbd.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-paper-plane-o fa-fw "></span>
    <span class="menu-collapsed">FTR CBD</span>
    </a>
    <a href="AP/ftrdp.php" class="dropdown-item bg-dark text-white">
    <span  class="fa fa-paper-plane-o fa-fw "></span>
    <span class="menu-collapsed">FTR DP</span>
    </a>
    </ul>
    </li>';
}else{
    echo '';
}
?>


<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Document Handover - Invoice'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

$querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, GROUP_CONCAT(menurole.id) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu IN ('Document Handover - BPB & SJ') GROUP BY username");
$rs2 = mysqli_fetch_array($querys2);
$menu2 = isset($rs2['menu']) ? $rs2['menu'] :0;
$id2 = isset($rs2['id']) ? $rs2['id'] :0;

$querys3 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, GROUP_CONCAT(menurole.id) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Approve Transfer Memo' GROUP BY username");
$rs3 = mysqli_fetch_array($querys3);
$menu3 = isset($rs3['menu']) ? $rs3['menu'] :0;
$id3 = isset($rs3['id']) ? $rs3['id'] :0;

echo '
<li class="dropdown-submenu ">
<a class="dropdown-item bg-dark text-white" href="#">
<span s class="fa fa-list-alt fa-fw "></span>
<span class="menu-collapsed">Document Tracking</span>
</a>
<ul class="dropdown-menu bg-dark text-white" role="menu">';
if(strpos($id2, '77') !== false){    
    echo '<a href="AP/bpb_received.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-share fa-fw "></span>
    <span class="menu-collapsed">BPB Transferred</span>
    </a>';
}

if(strpos($id, '66') !== false){    
    echo '<a href="AP/invoice_received.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-share fa-fw "></span>
    <span class="menu-collapsed">Invoice Received</span>
    </a>
    <a href="AP/report_invoice_received.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">IR Report</span>
    </a>';
}

if(strpos($id3, '109') !== false){    
  echo '<a href="AP/approve_transfer_memo.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-thumbs-o-up fa-fw "></span>
  <span class="menu-collapsed">Approve Transfer Memo</span>
  </a>

  <a href="AP/transfer_memo.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-paper-plane fa-fw "></span>
  <span class="menu-collapsed">Transfer Memo</span>
  </a>';
}


echo'</ul>
</li>';
?>


<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Kontrabon'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

if($id == '6'){
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span s class="fa fa-btc fa-fw "></span>
    <span class="menu-collapsed">Kontra Bon</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
    <a href="AP/kontrabon.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Kontra Bon Reg</span>
    </a>
    <a href="AP/kontrabonftrcbd.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Kontra Bon FTR CBD</span>
    </a>
    <a href="AP/kontrabonftrdp.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Kontra Bon FTR DP</span>
    </a>
    </ul>
    </li>';
}else{
    echo '';
}

// Invoice Received - menu sendiri (menggantikan submenu lama "Kontrabon New" yang
// dulu hanya utk dev/tester lewat hardcode username). Sekarang digating menurole
// 'Invoice Received' -> bisa diatur lewat Manage User Role (userrole.php).
// Anak menu tetap: Kontrabon (kontrabon_new.php) & Reference Number (kontrabon_new_ref.php).
$querysIR = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Invoice Received'");
$rsIR = mysqli_fetch_array($querysIR);
$idIR = isset($rsIR['id']) ? $rsIR['id'] :0;

if($idIR != 0){
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-inbox fa-fw "></span>
    <span class="menu-collapsed">Invoice Received</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
    <a href="AP/kontrabon_new.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-list-alt fa-fw "></span>
    <span class="menu-collapsed">Invoice Received</span>
    </a>
    <a href="AP/kontrabon_new_ref.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-hashtag fa-fw "></span>
    <span class="menu-collapsed">Reference Number</span>
    </a>
    </ul>
    </li>';
}

// Payment Voucher AP - userrole sendiri, terpisah dari Kontrabon.
$querysPvAp = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Payment Voucher AP'");
$rsPvAp = mysqli_fetch_array($querysPvAp);
$idPvAp = isset($rsPvAp['id']) ? $rsPvAp['id'] :0;

if($idPvAp == '135'){
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span s class="fa fa-credit-card fa-fw "></span>
    <span class="menu-collapsed">Payment Voucher</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">

    <a href="AP/payment-voucher-ap.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Payment Voucher</span>
    </a>

    <a href="AP/payment-voucher-list.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-list-alt fa-fw "></span>
    <span class="menu-collapsed">Payment Voucher list</span>
    </a>

    </ul>
    </li>';
}
?>


<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'List Payment'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

if($id == '8'){  
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-usd fa-fw"></span>
    <span class="menu-collapsed">List Payment</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
    <a href="AP/payment.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">List Payment Reg</span>
    </a>
    <a href="AP/listpaymentcbd.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">List Payment CBD</span>
    </a>
    <a href="AP/listpaymentdp.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">List Payment DP</span>
    </a>
    </ul>
    </li>';
}else{
    echo '';
}
?>


<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Payment'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

if($id == '10'){
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-usd fa-fw"></span>
    <span class="menu-collapsed">Payment</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
    <a href="AP/pelunasanftr.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Payment Reg</span>
    </a>
    <a href="AP/pelunasanftrcbd.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Payment CBD</span>
    </a>
    <a href="AP/pelunasanftrdp.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Payment DP</span>
    </a>
    </ul>
    </li>';
}else{
    echo '';
}
?>


<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Closing%' and useraccess.menu != 'Closing Periode' and useraccess.menu != 'Closing Fabric Warehouse'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

$sql123 = mysqli_query($conn2,"select count(distinct(no_payment)) as no_pay from list_payment where status = 'Approved'");
$row123 = mysqli_fetch_array($sql123);
$count123 = $row123['no_pay'];

$sqlsa = mysqli_query($conn2,"select count(distinct(no_pay)) as no_paysa from saldo_awal where status = 'Approved' and no_pay not like '%LP/NAG%'");
$rowsa = mysqli_fetch_array($sqlsa);
$countsa = $rowsa['no_paysa'];

$countlpsa12 = $count123 + $countsa;
if($countlpsa12 != '0'){
    $notif123 = '<span class="notif-badge">'.$countlpsa12.'</span>';
}else{
    $notif123 = '';
}

$sql456 = mysqli_query($conn2,"select count(distinct(no_payment)) as no_pay from list_payment_cbd where status = 'Approved'");
$row456 = mysqli_fetch_array($sql456);
$count456 = $row456['no_pay'];
if($count456 != '0'){
    $notif456 = '<span class="notif-badge">'.$count456.'</span>';
}else{
    $notif456 = '';
}

$sql789 = mysqli_query($conn2,"select count(distinct(no_payment)) as no_pay from list_payment_dp where status = 'Approved'");
$row789 = mysqli_fetch_array($sql789);
$count789 = $row789['no_pay'];
if($count789 != '0'){
    $notif789 = '<span class="notif-badge">'.$count789.'</span>';
}else{
    $notif789 = '';
}

if($id == '22'){
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-credit-card fa-fw"></span>
    <span class="menu-collapsed">Closing Payment</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
    <a href="AP/form-closing-payreg.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Close Payment Reg</span>
    '.$notif123.'
    </a>
    <a href="AP/formclosing-paycbd.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Close Payment CBD</span>
    '.$notif456.'
    </a>
    <a href="AP/formclosing-paydp.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed"> Close Payment DP</span>
    '.$notif789.'
    </a>
    <a href="AP/status_closing.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Closing Info</span>
    </a>
    </ul>
    </li>';
}else{
    echo '';
}
?>

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Status'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

if($id == '30'){       
    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-list-ul fa-fw"></span>
    <span class="menu-collapsed">Status</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
    <a href="AP/status.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-paperclip fa-fw"></span>
    <span class="menu-collapsed">Status</span>
    </a>
    </ul>
    </li>';
}else{
    echo '';
}
?>

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Report'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;


$querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Development'");
$rs2 = mysqli_fetch_array($querys2);
$menu2 = isset($rs2['menu']) ? $rs2['menu'] :0;
$id2 = isset($rs2['id']) ? $rs2['id'] :0;


$querys3 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Rekap Pelunasan%'");
$rs3 = mysqli_fetch_array($querys3);
$menu3 = isset($rs3['menu']) ? $rs3['menu'] :0;
$id3 = isset($rs3['id']) ? $rs3['id'] :0;

$sql_pr = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Purchase Report'  group by username");
$menu_pr = '';
while($rpr = mysqli_fetch_array($sql_pr)){
    $menu_pr = isset($rpr['ket']) ? $rpr['ket'] :0;
} 

echo '<li class="dropdown-submenu ">
<a class="dropdown-item bg-dark text-white" href="#">
<span class="fa fa-files-o fa-fw"></span>
<span class="menu-collapsed">Report</span>
</a>
<ul class="dropdown-menu bg-dark text-white" role="menu">';

if($id2 == '35'){
    echo '<a href="AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report (Apr 2022 - Dec 2025)</span>
    </a>
   <a href="AP/payable_card_statement.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report (Jan 2026 - Jun 2026)</span>
    </a>
    <a href="AP/payable_card_statement2.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report</span>
    </a>
    <a href="AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Rekap Pelunasan</span>
    </a>';
}elseif($id == '18' && $id3 == '0'){
    echo '<a href="AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report (Apr 2022 - Dec 2025)</span>
    </a>
    <a href="AP/payable_card_statement.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report (Jan 2026 - Jun 2026)</span>
    </a>
    <a href="AP/payable_card_statement2.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report</span>
    </a>';
}elseif($id == '0' && $id3 == '57'){
    echo '<a href="AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Rekap Pelunasan</span>
    </a>';
}elseif($id == '18' && $id3 == '57'){
    echo '<a href="AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report (Apr 2022 - Dec 2025)</span>
    </a>
    <a href="AP/payable_card_statement.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report (Jan 2026 - Jun 2026)</span>
    </a>
    <a href="AP/payable_card_statement2.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">AP Report</span>
    </a>
    <a href="AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Rekap Pelunasan</span>
    </a>';
}else{
    echo '';
}
if($menu_pr == 'Y'){
    echo '<a href="AP/laporan_pembelian.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Purchase Report</span>
    </a>
    <a href="AP/laporan_retur_pembelian.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-tags fa-fw "></span>
    <span class="menu-collapsed">Purchase Return Report</span>
    </a>';
}else{
}
if ($menu_pr == 'Y' || $id == '18' && $id3 == '57' || $id == '0' && $id3 == '57' || $id2 == '35' || 
    $id == '18' && $id3 == '0') {
    echo '';
}
echo '</ul>
</li>';
?>

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Approval%'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

$sqlkb = mysqli_query($conn2," select count(distinct(no_kbon)) as no_kbon from kontrabon_h where status = 'draft'");
$rowkb = mysqli_fetch_array($sqlkb);
$countkb = $rowkb['no_kbon'];
if($countkb != '0'){
    $notifkb = '<span class="notif-badge">'.$countkb.'</span>';
}else{
    $notifkb = '';
}

$sqllp = mysqli_query($conn2," select count(distinct(no_payment)) as no_pay from list_payment where status = 'draft'");
$rowlp = mysqli_fetch_array($sqllp);
$countlp = $rowlp['no_pay'];

$sqlsa = mysqli_query($conn2,"select count(distinct(no_pay)) as no_paysa from saldo_awal where status = 'draft' and no_pay not like '%LP/NAG%'");
$rowsa = mysqli_fetch_array($sqlsa);
$countsa = $rowsa['no_paysa'];

$countlpsa = $countlp + $countsa;

if($countlpsa != '0'){
    $notiflp = '<span class="notif-badge">'.$countlpsa.'</span>';
}else{
    $notiflp = '';
}


$sqlpay = mysqli_query($conn2," select COUNT(id) jml from (select a.id, payment_ftr_id, tgl_pelunasan, nama_pc, nama_supp, valuta_ftr curr, sum(ttl_bayar) total from payment_ftr a INNER JOIN master_pc b on b.kode_pc = a.profit_center where a.status = 'draft' GROUP BY payment_ftr_id) a");
$rowpay = mysqli_fetch_array($sqlpay);
$countpay = $rowpay['jml'];
if($countpay != '0'){
    $notifpay = '<span class="notif-badge">'.$countpay.'</span>';
}else{
    $notifpay = '';
}

$sqlpv1 = mysqli_query($conn2," select count(distinct(no_kbon)) as no_kbon from(select no_kbon from kontrabon_h where status = 'draft' and no_kbon not like '%INS%' and create_date >= '2026-07-01'
          UNION
          select no_kbon_det from kontrabon_h_installment_detail where status = 'draft'
          UNION
          select no_kbon from kontrabon_h_dp where status = 'draft'
          UNION
          select no_kbon from kontrabon_h_cbd where status = 'draft') a");
          $rowpv1 = mysqli_fetch_array($sqlpv1);
          $countpv1 = $rowpv1['no_kbon'];
          if($countpv1 != '0'){
              $notifpv1 = '<span class="notif-badge">'.$countpv1.'</span>';
          }else{
              $notifpv1 = '';
          }

          $sqlpv2 = mysqli_query($conn2," select count(distinct(no_kbon)) as no_kbon from(select no_kbon from kontrabon_h where status = 'FIRST APPROVED' and no_kbon not like '%INS%' and create_date >= '2026-07-01'
          UNION
          select no_kbon_det from kontrabon_h_installment_detail where status = 'FIRST APPROVED'
          UNION
          select no_kbon from kontrabon_h_dp where status = 'FIRST APPROVED'
          UNION
          select no_kbon from kontrabon_h_cbd where status = 'FIRST APPROVED') a");
          $rowpv2 = mysqli_fetch_array($sqlpv2);
          $countpv2 = $rowpv2['no_kbon'];
          if($countpv2 != '0'){
              $notifpv2 = '<span class="notif-badge">'.$countpv2.'</span>';
          }else{
              $notifpv2 = '';
          }

          $sqlpvl = mysqli_query($conn2," select count(distinct(pl_number)) pl_number from pv_payment_voucher_list_h where status = 'Draft'");
          $rowpvl = mysqli_fetch_array($sqlpvl);
          $countpvl = $rowpvl['pl_number'];
          if($countpvl != '0'){
              $notifpvl = '<span class="notif-badge">'.$countpvl.'</span>';
          }else{
              $notifpvl = '';
          }

echo '<li class="dropdown-submenu ">
<a class="dropdown-item bg-dark text-white" href="#">
<span class="fa fa-thumbs-o-up fa-fw"></span>
<span class="menu-collapsed">Approval</span>
</a>
<ul class="dropdown-menu bg-dark text-white" role="menu">';

if(strpos($id, '31') !== false){
    echo '<a href="AP/formapprovekb.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Kontrabon Reg</span>
    '.$notifkb.'
    </a>';
}

if(strpos($id, '33') !== false){
    echo '<a href="AP/formapprovelp.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">List Payment Reg</span>
    '.$notiflp.'
    </a>';
}

if(strpos($id, '91') !== false){
    echo '<a href="AP/form_approve_payment.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-ticket fa-fw "></span>
    <span class="menu-collapsed">Payment Reg</span>
    '.$notifpay.'
    </a>';
}

if(strpos($id, '113') !== false){
              echo '<a href="AP/approve-payment-voucher-ap-first.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment Voucher - First</span>
              '.$notifpv1.'
              </a>';
          }

          if(strpos($id, '114') !== false){
              echo '<a href="AP/approve-payment-voucher-ap-second.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment Voucher - Second</span>
              '.$notifpv2.'
              </a>';
          }

          if(strpos($id, '119') !== false){
              echo '<a href="AP/approve-payment-voucher-list.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment Voucher List</span>
              '.$notifpvl.'
              </a>';
          }

echo '</ul>
</li>';



?>

<?php
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Request Debitnote'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

if($id == '78'){    
    echo '
    <a href="AP/request_debitnote.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-registered fa-fw"></span>
    <span class="menu-collapsed">Request Debit Note</span>
    </a>';
}
?>


</ul>
</li>
<!-- END Menu AP -->
<!-- navbar Bank -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-university mr-1"></span> Bank<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Bank'");
      $rs = mysqli_fetch_array($querys);
      $menu = isset($rs['menu']) ? $rs['menu'] :0;
      $id = isset($rs['id']) ? $rs['id'] :0;

      $querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'E - Statement'");
      $rs2 = mysqli_fetch_array($querys2);
      $menu2 = isset($rs2['menu']) ? $rs2['menu'] :0;
      $id2 = isset($rs2['id']) ? $rs2['id'] :0;

      if($id == '36'){
        echo '
        <a href="AP/bank-in.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-in fa-fw mr-3"></span>
        <span class="menu-collapsed">Bank In</span>
        </a>
        <a href="AP/bank-out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-out fa-fw mr-3"></span>
        <span class="menu-collapsed">Bank Out</span>
        </a>
        <a href="AP/payment-voucher.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-money fa-fw mr-3"></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-file-excel-o fa-fw mr-3"></span>
        <span class="menu-collapsed">Report</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/bankreport.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-file-excel-o fa-fw mr-3"></span>
        <span class="menu-collapsed">Report Bank</span>
        </a>
        <a href="AP/report-cashflow-realisation.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-line-chart fa-fw mr-3"></span>
        <span class="menu-collapsed">Report Cash Flow Realisation</span>
        </a>
        </ul>
        </li>';
    }

    if($id2 == '62'){
        echo '<a href="AP/e_statement.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-money fa-fw mr-3"></span>
        <span class="menu-collapsed">E-Statement</span>
        </a>';
    }

    $querysPl = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Payment List'");
      $rsPl = mysqli_fetch_array($querysPl);
      $idPl = isset($rsPl['id']) ? $rsPl['id'] : 0;

      if($idPl == '115'){
          echo '<a href="AP/payment-list.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-list-alt fa-fw mr-3"></span>
          <span class="menu-collapsed">Payment List</span>
          </a>';
      }

      $querysTl = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Transfer List'");
      $rsTl = mysqli_fetch_array($querysTl);
      $idTl = isset($rsTl['id']) ? $rsTl['id'] : 0;

      if($idTl == '130'){
          echo '<a href="AP/transfer-list.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-exchange fa-fw mr-3"></span>
          <span class="menu-collapsed">Transfer List</span>
          </a>';
      }

    ?>
    <?php
    $querys = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and  useraccess.menu like '%Bank%' and useraccess.menu like '%Approval%' and useraccess.menu not like '%reverse%' group by username");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    $querys_app = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and  useraccess.menu like '%Approval Payment List%' group by username");
      $rs_app = mysqli_fetch_array($querys_app);
      $menu_app = isset($rs_app['menu']) ? $rs_app['menu'] :0;
      $id_app = isset($rs_app['id']) ? $rs_app['id'] :0;

      $sqlpl1 = mysqli_query($conn2," select count(distinct(pl_number)) pl_number from pv_payment_list_h where status = 'Draft'");
          $rowpl1 = mysqli_fetch_array($sqlpl1);
          $countpl1 = $rowpl1['pl_number'];
          if($countpl1 != '0'){
              $notifpl1 = '<span class="notif-badge">'.$countpl1.'</span>';
          }else{
              $notifpl1 = '';
          }

      $sqlpl2 = mysqli_query($conn2," select count(distinct(pl_number)) pl_number from pv_payment_list_h where status = 'FIRST APPROVED'");
          $rowpl2 = mysqli_fetch_array($sqlpl2);
          $countpl2 = $rowpl2['pl_number'];
          if($countpl2 != '0'){
              $notifpl2 = '<span class="notif-badge">'.$countpl2.'</span>';
          }else{
              $notifpl2 = '';
          }

    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-thumbs-up fa-fw"></span>
    <span class="menu-collapsed">Approval</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';

    if ($id == '41') {
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a> ';
    }elseif($id == '42'){
        echo '
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a> ';
    }elseif($id == '43'){
        echo '
        <a href="AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a> ';
    }elseif($id == '41,42'){
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a> ';
    }elseif($id == '41,43'){
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <a href="AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a> ';
    }elseif($id == '42,43'){
        echo '
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a>
        <a href="AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a>';
    }elseif($id == '41,42,43'){
        echo '
        <a href="AP/approve-pv.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Payment Voucher</span>
        </a>
        <a href="AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Incoming Bank</span>
        </a>
        <a href="AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw "></span>
        <span class="menu-collapsed">Outgoing bank</span>
        </a> ';
    }else{
        echo '';
    }

    if(strpos($id_app, '117') !== false){
              echo '<a href="AP/approve-payment-list-first.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment List - First</span>
              '.$notifpl1.'
              </a>';
          }

          if(strpos($id_app, '118') !== false){
              echo '<a href="AP/approve-payment-list-second.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment List - Second</span>
              '.$notifpl2.'
              </a>';
          }
    echo '</ul>
    </li>';
    ?>
</ul>
</li>
<!-- END Menu Bank -->

<!-- navbar Cash -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-money mr-1"></span> Cash<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Cash'");
      $rs = mysqli_fetch_array($querys);
      $menu = isset($rs['menu']) ? $rs['menu'] :0;
      $id = isset($rs['id']) ? $rs['id'] :0;

      if($id == '38'){                             
        echo '
        <a href="AP/cash-in.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-in fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/cash-out.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-sign-out fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-credit-card fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-credit-card fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a>
        <a href="AP/cashreport.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-file-excel-o fa-fw"></span>
        <span class="menu-collapsed">Report Cash</span>
        </a>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and  useraccess.menu like '%Cash%' and useraccess.menu like '%Approval%' and useraccess.menu not like '%reverse%' group by username");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    echo '
    <li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fa fa-thumbs-up fa-fw"></span>
    <span class="menu-collapsed">Approval</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';

    if ($id == '44') {
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a> ';
    }elseif($id == '45'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a> ';
    }elseif($id == '46'){
        echo '
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a> ';
    }elseif($id == '47'){
        echo '
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,45'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>';
    }elseif($id == '44,46'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a> ';
    }elseif($id == '44,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '45,46'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>';
    }elseif($id == '45,47'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '46,47'){
        echo '
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,45,46'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a> ';
    }elseif($id == '44,45,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,46,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '45,46,47'){
        echo '
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }elseif($id == '44,45,46,47'){
        echo '
        <a href="AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash In</span>
        </a>
        <a href="AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Cash Out</span>
        </a>
        <a href="AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash In</span>
        </a>
        <a href="AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-thumbs-up fa-fw"></span>
        <span class="menu-collapsed">Petty Cash Out</span>
        </a> ';
    }else{
        echo '';
    }
    echo '</ul>
    </li>';
    ?>
</ul>
</li>
<!-- END Menu Cash -->
<!-- navbar Accounting -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-bar-chart mr-1"></span> Accounting<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn1,"select Groupp, purchasing, approve_po from userpassword where username = '$user'");
      $rs = mysqli_fetch_array($querys);
      $group = $rs['Groupp'];
      $pur = $rs['purchasing'];
      $app_po = $rs['approve_po'];

      $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Acct%' and menurole.status = 'Menu' group by username");
      while($rss = mysqli_fetch_array($queryss)){
        $menu = isset($rss['ket']) ? $rss['ket'] :0;
        $id = isset($rss['id']) ? $rss['id'] :0;

    }  

    $queryss2 = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Closing Periode' and menurole.status = 'Menu' group by username");
    while($rss2 = mysqli_fetch_array($queryss2)){
        $menu2 = isset($rss2['ket']) ? $rss2['ket'] :0;
        $id2 = isset($rss2['id']) ? $rss2['id'] :0;

    }            

    if($menu == 'Y'){               
        echo '';
        if(strpos($id, '50') !== false){ 
         echo'<a href="AP/memorial-journal.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-bars fa-fw"></span>
         <span class="menu-collapsed">Memorial Journal</span>
         </a>';
     }if(strpos($id, '51') !== false){ 
         echo'<a href="AP/list-journal.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-list-alt fa-fw"></span>
         <span class="menu-collapsed">List Journal</span>
         </a>';
     }if(strpos($id, '52') !== false){ 
         echo'<a href="AP/general-ledger.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-print fa-list"></span>
         <span class="menu-collapsed">General Ledger</span>
         </a>';
          if($user == 'indro' || $user == 'willy' || $user == 'steven'){
            echo'<a href="AP/general_ledger.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-print fa-list"></span>
         <span class="menu-collapsed">General Ledger New</span>
         </a>';
          }
     }if(strpos($id, '104') !== false){ // SEMENTARA tampil lagi utk pembanding (tampilan lama)
         echo'<a href="AP/trial_balance.php" class="dropdown-item bg-dark text-white">
         <span class="fas fa-chart-line"></span>
         <span class="menu-collapsed">Trial Balance</span>
         </a>';
     }

     echo '<li class="dropdown-submenu ">
     <a class="dropdown-item bg-dark text-white" href="#">
     <span class="fa fa-list-ul fa-fw"></span>
     <span class="menu-collapsed">Sub Ledger</span>
     </a>
     <ul class="dropdown-menu bg-dark text-white" role="menu">';
     if(strpos($id, '64') !== false){
        echo'<a href="AP/other_receivable_report.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-fax fa-fw"></span>
        <span class="menu-collapsed">Other Receivable</span>
        </a>';
    }
    if(strpos($id, '65') !== false){
        echo'<a href="AP/other_payable_report.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-fax fa-fw"></span>
        <span class="menu-collapsed">Other Payable</span>
        </a>';
    }
    if(strpos($id, '82') !== false){
        echo'<a href="AP/purchase_advance_report.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-fax fa-fw"></span>
        <span class="menu-collapsed">Purchase Advance</span>
        </a>';
    }
    if(strpos($id, '105') !== false){
      echo'<a href="AP/prepaid_tax_report.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-fax fa-fw"></span>
      <span class="menu-collapsed">Prepaid Tax</span>
      </a>';
  }
    echo'</ul>
    </li>';
    if(strpos($id, '53') !== false){ // SEMENTARA tampil lagi utk pembanding (tampilan lama)
     echo'<li class="dropdown-submenu ">
     <a class="dropdown-item bg-dark text-white" href="#">
     <span class="fa fa-balance-scale fa-fw"></span>
     <span class="menu-collapsed">Financial Statement</span>
     </a>
     <ul class="dropdown-menu bg-dark text-white" role="menu">
     <a href="AP/financial-statement-ytd.php" class="dropdown-item bg-dark text-white">
     <span class="fa fa-calendar fa-fw mr-3"></span>
     <span class="menu-collapsed">Year To Date</span>
     </a>
     <a href="AP/trial-balance-monthly.php" class="dropdown-item bg-dark text-white">
     <span class="fa fa-calendar-o fa-fw mr-3"></span>
     <span class="menu-collapsed">Monthly</span>
     </a>
     </ul>
     </li>';
 }

 if($user == 'indro' || $user == 'willy' || $user == 'steven'){ // SEMENTARA tampil lagi utk pembanding (versi server: YTD saja)
   echo'<li class="dropdown-submenu ">
   <a class="dropdown-item bg-dark text-white" href="#">
   <span class="fa fa-balance-scale fa-fw"></span>
   <span class="menu-collapsed">Financial Statement 2</span>
   </a>
   <ul class="dropdown-menu bg-dark text-white" role="menu">
   <a href="AP/financial_statement_ytd.php" class="dropdown-item bg-dark text-white">
   <span class="fa fa-calendar fa-fw mr-3"></span>
   <span class="menu-collapsed">Year To Date</span>
   </a>
   </ul>
   </li>';
}

}

// Menu Financial Statement terpadu (menggantikan Trial Balance / FS1 / FS2 lama).
// Digate lewat nama userrole baru: 'FS - ALL' atau 'FS - Trial Balance Only'.
// Cek by-name (bukan strpos id) supaya id 132/133/134 tidak salah-cocok substring.
$user_esc_fs = mysqli_real_escape_string($conn2, $user);
$q_fsmenu = mysqli_query($conn2, "select count(*) as c from useraccess where username = '$user_esc_fs' and menu in ('FS - ALL','FS - Trial Balance Only')");
$has_fs_menu = ($q_fsmenu && ($rw_fs = mysqli_fetch_assoc($q_fsmenu)) && (int)$rw_fs['c'] > 0);
if($has_fs_menu){
 echo'<a href="AP/financial_statement.php" class="dropdown-item bg-dark text-white">
 <span class="fa fa-balance-scale fa-fw"></span>
 <span class="menu-collapsed">Financial Statement (New)</span>
 </a>';
}

if(strpos($id2, '88') !== false){
        // code...
 echo'<a href="AP/closing-periode.php" class="dropdown-item bg-dark text-white">
 <span class="fas fa-lock fa-fw"></span>
 <span class="menu-collapsed">Closing Periode</span>
 </a>';
}

echo '<li class="dropdown-submenu ">
<a class="dropdown-item bg-dark text-white" href="#">
<span class="fab fa-rev"></span>
<span class="menu-collapsed">Repost Journal</span>
</a>
<ul class="dropdown-menu bg-dark text-white" role="menu">';
if(strpos($id, '90') !== false){
    echo'<a href="AP/repost-bank-out.php" class="dropdown-item bg-dark text-white">
    <span class="fas fa-landmark"></span>
    <span class="menu-collapsed">Bank Out</span>
    </a>';
}
if(strpos($id, '107') !== false){
  echo'<a href="AP/repost-bpb.php" class="dropdown-item bg-dark text-white">
  <span class="fas fa-archive"></span>
  <span class="menu-collapsed">BPB</span>
  </a>';
}
echo'</ul>
</li>';

if(strpos($id, '106') !== false){ 
         echo'<a href="AP/rekonsiliasi_jurnal_bpb.php" class="dropdown-item bg-dark text-white">
         <span class="fas fa-file-contract"></span>
         <span class="menu-collapsed">Rekonsiliasi Jurnal-BPB</span>
         </a>';
     }

if(strpos($id, '108') !== false){ 
  echo'<a href="AP/edit-journal.php" class="dropdown-item bg-dark text-white">
  <span class="fas fa-pencil-alt"></span>
  <span class="menu-collapsed">Edit Jurnal</span>
  </a>';
}

?>
</ul>
</li>
<!-- END Menu Accounting -->

<!-- navbar Cost Accounting -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-industry mr-1"></span> Cost Accounting<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php

      $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Cost Accounting%' and menurole.status = 'Menu' group by username");
      while($rss = mysqli_fetch_array($queryss)){
        $menu = isset($rss['ket']) ? $rss['ket'] :0;
        $id = isset($rss['id']) ? $rss['id'] :0;

    }           

    if(strpos($id, '85') !== false){
        echo '           
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span s class="fa fa-tasks fa-fw "></span>
        <span class="menu-collapsed">Fabric</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">

      <a href="AP/ca_fabric_list_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-barcode fa-fw "></span>
      <span class="menu-collapsed">List Barcode</span>
      </a>
      <a href="AP/ca_fabric_trx_in_new.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-cart-arrow-down fa-fw "></span>
      <span class="menu-collapsed">Trx Item In</span>
      </a>
      <a href="AP/ca_fabric_trx_in_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-cart-arrow-down fa-fw "></span>
      <span class="menu-collapsed">Trx Barcode In</span>
      </a>
      
      <a href="AP/ca_fabric_trx_out_item.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-paper-plane fa-fw "></span>
      <span class="menu-collapsed">Trx Item Out</span>
      </a>
      <a href="AP/ca_fabric_trx_out_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-paper-plane fa-fw "></span>
      <span class="menu-collapsed">Trx Barcode Out</span>
      </a>
      
      <a href="AP/ca_fabric_summary_item.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Item</span>
      </a>
      <a href="AP/ca_fabric_summary_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Barcode</span>
      </a>
      <a href="AP/ca_fabric_summary_sc.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Subcont</span>
      </a>
      <a href="AP/ca_fabric_summary_subcont.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Subcont New</span>
      </a>
      <a href="AP/update_bpb_fabric.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-pencil-square fa-fw "></span>
      <span class="menu-collapsed">Update Trx In</span>
      </a>
      <a href="AP/adjust-subcont.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-pencil-square fa-fw "></span>
      <span class="menu-collapsed">Update Subcontractor</span>
      </a>

        </ul>
        </li>';
    }

    if(strpos($id, '110') !== false){
      echo '           
      <li class="dropdown-submenu ">
      <a class="dropdown-item bg-dark text-white" href="#">
      <span s class="fa fa-tasks fa-fw "></span>
      <span class="menu-collapsed">Item General</span>
      </a>
      <ul class="dropdown-menu bg-dark text-white" role="menu">

      
      <a href="AP/item_general_usage.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-cart-arrow-down fa-fw "></span>
      <span class="menu-collapsed">Item General Usage</span>
      </a>

      </ul>
      </li>';
  }

      $query_update = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Update BPB Fabric' and menurole.status = 'Menu' group by username");
      while($rs_update = mysqli_fetch_array($query_update)){
      $id_update = isset($rs_update['id']) ? $rs_update['id'] :0;
    }

      echo '
      <li class="dropdown-submenu ">
      <a class="dropdown-item bg-dark text-white" href="#">
      <span s class="far fa-edit fa-fw "></span>
      <span class="menu-collapsed">Update BPB</span>
      </a>
      <ul class="dropdown-menu bg-dark text-white" role="menu">';

      if(strpos($id_update, '112') !== false){
        echo '<a href="AP/update-bpb-fabric.php" class="dropdown-item bg-dark text-white">
        <span class="fas fa-warehouse fa-fw "></span>
        <span class="menu-collapsed">Fabric</span>
        </a>';

        $pendingApproveCount = 0;
        $cntApproveRs = mysqli_query($conn1, "SELECT COUNT(*) as cnt FROM update_bpb_fabric_h WHERE status NOT IN ('Approved','Cancel')");
        if ($cntApproveRs) {
            $cntApproveRow = mysqli_fetch_assoc($cntApproveRs);
            $pendingApproveCount = (int) ($cntApproveRow['cnt'] ?? 0);
        }

        echo '<a href="AP/approve_update_bpb_fabric.php" class="dropdown-item bg-dark text-white d-flex justify-content-between align-items-center">
        <span><span class="fas fa-check-circle fa-fw "></span>
        <span class="menu-collapsed">Approve Fabric</span></span>';
        if ($pendingApproveCount > 0) {
            echo '<span class="badge badge-danger ml-2">' . $pendingApproveCount . '</span>';
        }
        echo '</a>';
      }

      echo '
      </ul>
      </li>';

      $query_close = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Closing Fabric Warehouse' and menurole.status = 'Menu' group by username");
      while($rs_close = mysqli_fetch_array($query_close)){
      $id_close = isset($rs_close['id']) ? $rs_close['id'] :0;
    }

      echo '
      <li class="dropdown-submenu ">
      <a class="dropdown-item bg-dark text-white" href="#">
      <span class="fas fa-calendar-times fa-fw "></span>
      <span class="menu-collapsed">Closing Period</span>
      </a>
      <ul class="dropdown-menu bg-dark text-white" role="menu">';

  if(strpos($id_close, '111') !== false){
      echo '<a href="AP/ca_fabric_closing_periode.php" class="dropdown-item bg-dark text-white">
      <span class="fas fa-warehouse fa-fw "></span>
      <span class="menu-collapsed">Fabric Warehouse</span>
      </a>';
  }

      echo '</ul>
      </li>';

  ?>
  <!--    <a href="AP/ca_fabric_trx_in.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-cart-arrow-down fa-fw "></span>
      <span class="menu-collapsed">Trx In</span>
      </a>
<a href="AP/ca_fabric_trx_out.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-paper-plane fa-fw "></span>
      <span class="menu-collapsed">Trx Out</span>
      </a>
<a href="AP/ca_fabric_summary.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary</span>
      </a> -->
</ul>
</li>
<!-- END Menu Cost Accounting -->

<!-- navbar Exim -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cubes mr-1"></span> Exim<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php

      $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Exim%' and menurole.status = 'Menu' group by username");
      while($rss = mysqli_fetch_array($queryss)){
        $menu = isset($rss['ket']) ? $rss['ket'] :0;
        $id = isset($rss['id']) ? $rss['id'] :0;

    }           

    if($menu == 'Y'){               
        echo '';
        if(strpos($id, '83') !== false){ 
         echo'<a href="AP/exim-calculatin-cost-report.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-bars fa-fw"></span>
         <span class="menu-collapsed">Calculation Cost Report</span>
         </a>';
     }
     // if(strpos($id, '84') !== false){ 
     //     echo'<a href="#" class="dropdown-item bg-dark text-white">
     //     <span class="fa fa-list-alt fa-fw"></span>
     //     <span class="menu-collapsed">Checklist Cost Report</span>
     //     </a>';
     // }


 }

 ?>
</ul>
</li>
<!-- END Menu Exim -->

<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fas fa-retweet mr-1"></span> Reverse<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Reverse'");
      $rs = mysqli_fetch_array($querys);
      $menu = isset($rs['menu']) ? $rs['menu'] :0;
      $id = isset($rs['id']) ? $rs['id'] :0;

      $querymtn = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain BPB'");
      $rsl2 = mysqli_fetch_array($querymtn);
      $menu2 = isset($rsl2['menu']) ? $rsl2['menu'] :0;
      $mtn = isset($rsl2['id']) ? $rsl2['id'] :0;


      echo '
      <li class="dropdown-submenu ">
      <a class="dropdown-item bg-dark text-white" href="#">
      <span class="fa fa-envelope fa-fw"></span>
      <span class="menu-collapsed">BPB</span>
      </a>
      <ul class="dropdown-menu bg-dark text-white" role="menu">';
      if(strpos($mtn, '89') !== false){    
        echo '<a href="AP/maintain-bpb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw "></span>
        <span class="menu-collapsed">BPB</span>
        </a>';
    }
    if($id == '34'){
        echo '
        <a href="AP/formreversebpb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">Verifikasi BPB</span>                
        </a>';
    }
    echo '</ul>
    </li>';
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain FTR'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '12'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-money fa-fw"></span>
        <span class="menu-collapsed">FTR</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/pengajuan_ftrcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">FTR CBD</span>
        </a>
        <a href="AP/pengajuan_ftrdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">FTR DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain Kontrabon'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '14'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-btc fa-fw"></span>
        <span class="menu-collapsed">Kontra Bon</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/pengajuankb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed"> Kontrabon Reg</span>
        </a>
        <a href="AP/pengajuankb_cbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">Kontrabon CBD</span>
        </a>
        <a href="AP/pengajuankb_dp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">Kontrabon DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Maintain List Payment'");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    if($id == '16'){
        echo '
        <li class="dropdown-submenu ">
        <a class="dropdown-item bg-dark text-white" href="#">
        <span class="fa fa-usd fa-fw"></span>
        <span class="menu-collapsed">List Payment</span>
        </a>
        <ul class="dropdown-menu bg-dark text-white" role="menu">
        <a href="AP/pengajuanpayment.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment Reg</span>
        </a>
        <a href="AP/pengajuanpaymentcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment CBD</span>
        </a>
        <a href="AP/pengajuanpaymentdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment DP</span>
        </a>
        </ul>
        </li>';
    }else{
        echo '';
    }
    ?>

    <?php
    $querys = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Reverse%' group by username");
    $rs = mysqli_fetch_array($querys);
    $menu = isset($rs['menu']) ? $rs['menu'] :0;
    $id = isset($rs['id']) ? $rs['id'] :0;

    echo '<li class="dropdown-submenu ">
    <a class="dropdown-item bg-dark text-white" href="#">
    <span class="fas fa-history fa-fw"></span>
    <span class="menu-collapsed">Reverse</span>
    </a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">';

    if(strpos($id, '94') !== false){
        echo '<a href="AP/reverse_kontrabon.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-angle-right fa-fw"></span>
        <span class="menu-collapsed">Kontrabon</span>
        </a>';
    }
    if(strpos($id, '92') !== false){
        echo '<a href="AP/reverse_payment.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-angle-right fa-fw"></span>
        <span class="menu-collapsed">Payment</span>
        </a>';
    }

    if(strpos($id, '98') !== false){
      echo '<a href="AP/reverse_bank.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Bank</span>
      </a>';
  }

  if(strpos($id, '101') !== false){
      echo '<a href="AP/reverse_petty_cash.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Petty Cash</span>
      </a>';
  }

  if(strpos($id, '120') !== false){
      echo '<a href="AP/reverse_payment_voucher.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Payment Voucher</span>
      </a>';
  }

  if(strpos($id, '121') !== false){
      echo '<a href="AP/reverse_payment_voucher_list.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Payment Voucher List</span>
      </a>';
  }

  if(strpos($id, '122') !== false){
      echo '<a href="AP/reverse_payment_list.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Payment List</span>
      </a>';
  }

  echo '</ul>
  </li>';

  ?>

  <?php
  $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%Approval%'");
  $rs = mysqli_fetch_array($querys);
  $menu = isset($rs['menu']) ? $rs['menu'] :0;
  $id = isset($rs['id']) ? $rs['id'] :0;

  $sql_rvspay = mysqli_query($conn2," select COUNT(id) jml from (select id from ap_reverse_h where rvs_number like '%PAY%' and status = 'DRAFT') a");
  $row_rvspay = mysqli_fetch_array($sql_rvspay);
  $count_rvspay = $row_rvspay['jml'];
  if($count_rvspay != '0'){
    $notif_rvspay = '<span class="notif-badge">'.$count_rvspay.'</span>';
}else{
    $notif_rvspay = '';
}

$sql_rvspl = mysqli_query($conn2," SELECT COUNT(id) jml FROM ap_reverse_h WHERE rvs_number LIKE 'RVS/PL/%' AND status = 'DRAFT'");
$row_rvspl = mysqli_fetch_array($sql_rvspl);
$count_rvspl = $row_rvspl['jml'];
if($count_rvspl != '0'){
    $notif_rvspl = '<span class="notif-badge">'.$count_rvspl.'</span>';
}else{
    $notif_rvspl = '';
}

$sql_rvspvl = mysqli_query($conn2," SELECT COUNT(id) jml FROM ap_reverse_h WHERE rvs_number LIKE 'RVS/PVL/%' AND status = 'DRAFT'");
$row_rvspvl = mysqli_fetch_array($sql_rvspvl);
$count_rvspvl = $row_rvspvl['jml'];
if($count_rvspvl != '0'){
    $notif_rvspvl = '<span class="notif-badge">'.$count_rvspvl.'</span>';
}else{
    $notif_rvspvl = '';
}

$sql_rvspv = mysqli_query($conn2," SELECT COUNT(id) jml FROM ap_reverse_h WHERE rvs_number LIKE 'RVS/PV/%' AND rvs_number NOT LIKE 'RVS/PVL/%' AND status = 'DRAFT'");
$row_rvspv = mysqli_fetch_array($sql_rvspv);
$count_rvspv = $row_rvspv['jml'];
if($count_rvspv != '0'){
    $notif_rvspv = '<span class="notif-badge">'.$count_rvspv.'</span>';
}else{
    $notif_rvspv = '';
}

echo '<li class="dropdown-submenu ">
<a class="dropdown-item bg-dark text-white" href="#">
<span class="fa fa-thumbs-o-up fa-fw"></span>
<span class="menu-collapsed">Approval</span>
</a>
<ul class="dropdown-menu bg-dark text-white" role="menu">';


if(strpos($id, '95') !== false){
    echo '<a href="AP/form_approve_reverse_kontrabon.php" class="dropdown-item bg-dark text-white">
    <span class="fas fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Kontrabon</span>
    '.$notif_rvspay.'
    </a>';
}

if(strpos($id, '93') !== false){
    echo '<a href="AP/form_approve_reverse_payment.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Payment</span>
    '.$notif_rvskbon.'
    </a>';
}

if(strpos($id, '100') !== false){
    echo '<a href="AP/form_approve_reverse_bank.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Bank</span>
    '.$notif_rvsbank.'
    </a>';
}

if(strpos($id, '103') !== false){
    echo '<a href="AP/form_approve_reverse_petty_cash.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Petty Cash</span>
    '.$notif_rvspc.'
    </a>';
}

if(strpos($id, '124') !== false){
    echo '<a href="AP/form_approve_reverse_payment_list.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Payment List</span>
    '.$notif_rvspl.'
    </a>';
}

if(strpos($id, '127') !== false){
    echo '<a href="AP/form_approve_reverse_payment_voucher_list.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Payment Voucher List</span>
    '.$notif_rvspvl.'
    </a>';
}

if(strpos($id, '129') !== false){
    echo '<a href="AP/form_approve_reverse_payment_voucher.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Payment Voucher</span>
    '.$notif_rvspv.'
    </a>';
}


echo '</ul>
</li>';
?>

</ul>
</li>

<!-- navbar Other Menu -->
<li class="nav-item dropdown active">
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cogs mr-1"></span>Setting<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
        <?php
        $querys = mysqli_query($conn1,"select Groupp from userpassword where username = '$user'");
        $rs = mysqli_fetch_array($querys);
        $group = isset($rs['Groupp']) ? $rs['Groupp'] : null;

        if($group != 'STAFF' && $group != null){
            echo '
            <a href="AP/userrole.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-user-plus fa-fw"></span>
            <span class="menu-collapsed">Userrole</span>
            </a>';
        }else{
            echo '';
        }

        if($user === 'indro'){
            echo '
            <a href="AP/project-progress.php" class="dropdown-item bg-dark text-white">
            <span class="fa fa-tasks fa-fw"></span>
            <span class="menu-collapsed">Project Progress</span>
            </a>';
        }
        ?>
    </ul>
</li>
<!-- END Menu Other Menu -->

<?php if ($user == 'indro'): ?>
<li class="nav-item active">
  <a href="AP/project.php" class="nav-link">
    <span class="fa fa-rocket mr-1"></span> Project
  </a>
</li>
<?php /* Disembunyikan sementara - halaman tetap bisa diakses via link langsung
<li class="nav-item active">
  <a href="AP/menu-docs.php" class="nav-link">
    <span class="fa fa-book mr-1"></span> Menu Docs
  </a>
</li>
<li class="nav-item active">
  <a href="AP/journal-balance-tracker.php" class="nav-link">
    <span class="fa fa-balance-scale mr-1"></span> Journal Tracker
  </a>
</li>
*/ ?>
<?php endif; ?>
<!-- END Menu Project -->


      <!-- <li class="nav-item dropdown active">
          <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown">Proses<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">

            <li class="dropdown-submenu">
              <a class="dropdown-item" href="#">Another action</a>
              <ul class="dropdown-menu" role="menu">
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
              </ul>
            </li>

            <li class="dropdown-submenu">
              <a class="dropdown-item" href="#">Another action</a>
              <ul class="dropdown-menu" role="menu">
                <li><a class="dropdown-item" href="#">Another action</a></li>
                <li><a class="dropdown-item" href="#">Another action</a></li>
              </ul>
            </li>

          </ul>
        </li>
    -->
</ul>

<ul class="navbar-nav ml-auto">
    <li class="nav-item active">
        <a class="nav-link" href="function/logout.php" style="font-size:12px;"><span class="fa fa-power-off">Log-out</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="index.php" style="font-size:12px;"><span class="fa fa-home">Home</a>
            </li>
            <li class="nav-item active">
                <span class="navbar-text text-white" style="font-size:12px;"><span class="fa fa-user"> <?php echo $user ?> </span></span>
            </li>
        </div>

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





</body>

</html>
