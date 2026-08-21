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
<link href="../css/4.1.1/main.css" rel="stylesheet">  
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
              <a href="../AP/master-cash-flow.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw"></span>
              <span class="menu-collapsed">Cash Flow - Accounting</span>

              </a>
              <a href="../AP/master-cash-flow-mapping.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-random fa-fw"></span>
              <span class="menu-collapsed">Cash Flow - Finance</span>
              </a>
              <a href="../AP/master-coa-category1.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw "></span>
              <span class="menu-collapsed">Category COA</span>
              </a>
              <a href="../AP/master-coa.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw "></span>
              <span class="menu-collapsed">Chart Of Account</span>
              </a>
              <a href="../AP/master-costcenter.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw "></span>
              <span class="menu-collapsed">Cost Center</span>
              </a>
              <a href="../AP/master-profit-center.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw "></span>
              <span class="menu-collapsed">Profit Center</span>
              </a>
              <a href="../AP/master-bank.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw "></span>
              <span class="menu-collapsed">Bank</span>
              </a>
              <a href="../AP/master-mapping-memo.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-paperclip fa-fw "></span>
              <span class="menu-collapsed">Mapping Memo</span>
              </a>
              <a href="../AP/master-supplier-bank.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-university fa-fw "></span>
              <span class="menu-collapsed">Master Bank Supplier</span>
              </a>
              <a href="../AP/master-pilihan-bank.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-university fa-fw "></span>
              <span class="menu-collapsed">Master Pilihan Bank</span>
              </a>
              <a href="../AP/master-rate.php" class="dropdown-item bg-dark text-white">
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

        $queryss = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%' and useraccess.menu != 'Transfer BPB' and useraccess.menu != 'Accept BPB Whs-Acc' and useraccess.menu != 'Maintain BPB' and useraccess.menu != 'Acct - Rekonsiliasi Jurnal-BPB' and useraccess.menu != 'Acct - Repost Jurnal BPB' and useraccess.menu not like '%create%' and profit_center != 'NAK' and  useraccess.menu != 'Update BPB Fabric' group by username");
        while($rss = mysqli_fetch_array($queryss)){
          $menu = isset($rss['ket']) ? $rss['ket'] :0;
          $id = isset($rss['id']) ? $rss['id'] :0;

          $sql = mysqli_query($conn2,"select count(distinct(no_bpb)) as no_bpb from bpb_new where status = 'GMF' and profit_center = 'NAG'");
          $row = mysqli_fetch_array($sql);
          $count = $row['no_bpb'];
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

    $queryss2 = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%update%' and  useraccess.menu != 'Update BPB Fabric' group by username");
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
         echo'<a href="../AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>';
     }elseif($id == '2'){ 
         echo'<a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>';
     }elseif($id == '19'){ 
         echo'<a href="../AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '20'){ 
         echo'<a href="../AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '1,2'){ 
         echo'<a href="../AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>';
     }elseif($id == '1,19'){ 
         echo'<a href="../AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="../AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '1,20'){ 
         echo'<a href="../AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="../AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '2,19'){ 
         echo'<a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="../AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '2,20'){ 
         echo'<a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="../AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '19,20'){ 
         echo'<a href="../AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>
         <a href="../AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '1,2,19'){ 
         echo'<a href="../AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="../AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>';
     }elseif($id == '1,2,20'){ 
         echo'<a href="../AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="../AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '2,19,20'){ 
         echo'<a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="../AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw mr-2"></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>
         <a href="../AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>';
     }elseif($id == '1,2,19,20'){ 
         echo'<a href="../AP/formapprovebpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB</span>
         '.$notif.'
         </a>
         <a href="../AP/verifikasibpb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB</span>
         </a>
         <a href="../AP/formapprovebppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa fa-thumbs-up fa-fw "></span>
         <span class="menu-collapsed">Approve BPB Return</span>
         '.$notif1.'
         </a>
         <a href="../AP/verifikasibppb.php" class="dropdown-item bg-dark text-white">
         <span  class="fa fa-share fa-fw "></span>
         <span class="menu-collapsed">Verifikasi BPB Return</span>
         </a>
         ';
     }else{
      echo '';
  }
  if($menu2 == 'Y'){
      echo '<a href="../AP/update_bpb.php" class="dropdown-item bg-dark text-white">
      <span  class="fa fa-pencil fa-fw "></span>
      <span class="menu-collapsed">Update BPB</span>
      </a>
      <a href="../AP/report-faktur-pajak.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-files-o fa-fw "></span>
      <span class="menu-collapsed">Report FP</span>
      </a>';
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

$queryss_nak = mysqli_query($conn2,"select 'Y' as ket,GROUP_CONCAT(useraccess.menu) as menu,useraccess.username as username, GROUP_CONCAT(menurole.id ORDER BY menurole.id asc) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu like '%BPB%' and useraccess.menu != 'Transfer BPB' and useraccess.menu != 'Accept BPB Whs-Acc' and useraccess.menu not like '%create%' and profit_center = 'NAK' group by username");
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
      echo'<a href="../AP/verifikasibpb_knitting.php" class="dropdown-item bg-dark text-white">
      <span  class="fa fa-share fa-fw "></span>
      <span class="menu-collapsed">Verifikasi BPB</span>
      </a>';
  }elseif($id_nak == '86'){
      echo'<a href="../AP/formapprovebpb_knitting.php" class="dropdown-item bg-dark text-white">
      <span  class="fa fa fa-thumbs-up fa-fw "></span>
      <span class="menu-collapsed">Approve BPB</span>
      '.$notif_nak.'
      </a>';
  }elseif($id_nak == '86,87'){ 
   echo'<a href="../AP/verifikasibpb_knitting.php" class="dropdown-item bg-dark text-white">
   <span  class="fa fa-share fa-fw "></span>
   <span class="menu-collapsed">Verifikasi BPB</span>
   </a>';
   echo'<a href="../AP/formapprovebpb_knitting.php" class="dropdown-item bg-dark text-white">
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
  <a href="../AP/ftrcbd.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-paper-plane-o fa-fw "></span>
  <span class="menu-collapsed">FTR CBD</span>
  </a>
  <a href="../AP/ftrdp.php" class="dropdown-item bg-dark text-white">
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
$querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Invoice Received'");
$rs = mysqli_fetch_array($querys);
$menu = isset($rs['menu']) ? $rs['menu'] :0;
$id = isset($rs['id']) ? $rs['id'] :0;

$querys2 = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, GROUP_CONCAT(menurole.id) as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu IN ('Transfer BPB') GROUP BY username");
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
  echo '<a href="../AP/bpb_received.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-share fa-fw "></span>
  <span class="menu-collapsed">BPB Transferred</span>
  </a>';
}


if(strpos($id, '66') !== false){    
  echo '<a href="../AP/invoice_received.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-share fa-fw "></span>
  <span class="menu-collapsed">Invoice Received</span>
  </a>
  <a href="../AP/report_invoice_received.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-tags fa-fw "></span>
  <span class="menu-collapsed">IR Report</span>
  </a>';
}

if(strpos($id3, '109') !== false){    
  echo '<a href="../AP/approve_transfer_memo.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-thumbs-o-up fa-fw "></span>
  <span class="menu-collapsed">Approve Transfer Memo</span>
  </a>

  <a href="../AP/transfer_memo.php" class="dropdown-item bg-dark text-white">
  <span class="fa fa-paper-plane fa-fw "></span>
  <span class="menu-collapsed">Transfer Memo</span>
  </a>';
}

echo'</ul>
</li>';
?>
            <!-- <a href="../AP/trf_inv_fintoacc.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-share fa-fw "></span>
                    <span class="menu-collapsed">Transfer Inv Fin - Acc</span>
                </a> -->


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
                    <a href="../AP/kontrabon.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-ticket fa-fw "></span>
                    <span class="menu-collapsed">Kontra Bon Reg</span>
                    </a>
                    <a href="../AP/kontrabonftrcbd.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-ticket fa-fw "></span>
                    <span class="menu-collapsed">Kontra Bon FTR CBD</span>
                    </a>
                    <a href="../AP/kontrabonftrdp.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-ticket fa-fw "></span>
                    <span class="menu-collapsed">Kontra Bon FTR DP</span>
                    </a>
                    </ul>
                    </li>';

                    //NEW PAYMENT VOUCHER

                    echo '
                    <li class="dropdown-submenu ">
                    <a class="dropdown-item bg-dark text-white" href="#">
                    <span s class="fa fa-credit-card fa-fw "></span>
                    <span class="menu-collapsed">Payment Voucher</span>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white" role="menu">

                    <a href="../AP/payment-voucher-ap.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-ticket fa-fw "></span>
                    <span class="menu-collapsed">Payment Voucher</span>
                    </a>

                    <a href="../AP/payment-voucher-list.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-list-alt fa-fw "></span>
                    <span class="menu-collapsed">Payment Voucher list</span>
                    </a>

                    </ul>
                    </li>';
                }else{
                    echo '';
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
                    <a href="../AP/payment.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">List Payment Reg</span>
                    </a>
                    <a href="../AP/listpaymentcbd.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">List Payment CBD</span>
                    </a>
                    <a href="../AP/listpaymentdp.php" class="dropdown-item bg-dark text-white">
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
                    <a href="../AP/pelunasanftr.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Payment Reg</span>
                    </a>
                    <a href="../AP/pelunasanftrcbd.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Payment CBD</span>
                    </a>
                    <a href="../AP/pelunasanftrdp.php" class="dropdown-item bg-dark text-white">
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
                    <a href="../AP/form-closing-payreg.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Close Payment Reg</span>
                    '.$notif123.'
                    </a>
                    <a href="../AP/formclosing-paycbd.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Close Payment CBD</span>
                    '.$notif456.'
                    </a>
                    <a href="../AP/formclosing-paydp.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed"> Close Payment DP</span>
                    '.$notif789.'
                    </a>
                    <a href="../AP/status_closing.php" class="dropdown-item bg-dark text-white">
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
                    <a href="../AP/status.php" class="dropdown-item bg-dark text-white">
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
                    echo '<a href="../AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report (Apr 2022 - Dec 2025)</span>
                    </a>
                    <a href="../AP/payable_card_statement.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report (Jan 2026 - Jun 2026)</span>
                    </a>
                    <a href="../AP/payable_card_statement2.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report</span>
                    </a>
                    <a href="../AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Rekap Pelunasan</span>
                    </a>';
                }elseif($id == '18' && $id3 == '0'){
                    echo '<a href="../AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report (Apr 2022 - Dec 2025)</span>
                    </a>
                    <a href="../AP/payable_card_statement.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report (Jan 2026 - Jun 2026)</span>
                    </a>
                    <a href="../AP/payable_card_statement2.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report</span>
                    </a>';
                }elseif($id == '0' && $id3 == '57'){
                    echo '<a href="../AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Rekap Pelunasan</span>
                    </a>';
                }elseif($id == '18' && $id3 == '57'){
                    echo '<a href="../AP/pcs_detail.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report (Apr 2022 - Dec 2025)</span>
                    </a>
                    <a href="../AP/payable_card_statement.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report (Jan 2026 - Jun 2026)</span>
                    </a>
                    <a href="../AP/payable_card_statement2.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">AP Report</span>
                    </a>
                    <a href="../AP/rekap-pelunasan.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Rekap Pelunasan</span>
                    </a>';
                }else{
                    echo '';
                }
                if($menu_pr == 'Y'){
                    echo '<a href="../AP/laporan_pembelian.php" class="dropdown-item bg-dark text-white">
                    <span class="fa fa-tags fa-fw "></span>
                    <span class="menu-collapsed">Purchase Report</span>
                    </a>
                    <a href="../AP/laporan_retur_pembelian.php" class="dropdown-item bg-dark text-white">
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
              echo '<a href="../AP/formapprovekb.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Kontrabon Reg</span>
              '.$notifkb.'
              </a>';
          }

          if(strpos($id, '33') !== false){
              echo '<a href="../AP/formapprovelp.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">List Payment Reg</span>
              '.$notiflp.'
              </a>';
          }

          if(strpos($id, '91') !== false){
              echo '<a href="../AP/form_approve_payment.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment Reg</span>
              '.$notifpay.'
              </a>';
          }

          if(strpos($id, '113') !== false){
              echo '<a href="../AP/approve-payment-voucher-ap-first.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment Voucher - First</span>
              '.$notifpv1.'
              </a>';
          }

          if(strpos($id, '114') !== false){
              echo '<a href="../AP/approve-payment-voucher-ap-second.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment Voucher - Second</span>
              '.$notifpv2.'
              </a>';
          }

          if(strpos($id, '119') !== false){
              echo '<a href="../AP/approve-payment-voucher-list.php" class="dropdown-item bg-dark text-white">
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
              <a href="../AP/request_debitnote.php" class="dropdown-item bg-dark text-white">
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
          <a href="../AP/bank-in.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-sign-in fa-fw mr-3"></span>
          <span class="menu-collapsed">Bank In</span>
          </a>
          <a href="../AP/bank-out.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-sign-out fa-fw mr-3"></span>
          <span class="menu-collapsed">Bank Out</span>
          </a>
          <a href="../AP/payment-voucher.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-money fa-fw mr-3"></span>
          <span class="menu-collapsed">Payment Voucher</span>
          </a>
          <li class="dropdown-submenu ">
          <a class="dropdown-item bg-dark text-white" href="#">
          <span class="fa fa-file-excel-o fa-fw mr-3"></span>
          <span class="menu-collapsed">Report</span>
          </a>
          <ul class="dropdown-menu bg-dark text-white" role="menu">
          <a href="../AP/bankreport.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-file-excel-o fa-fw mr-3"></span>
          <span class="menu-collapsed">Report Bank</span>
          </a>
          <a href="../AP/report-cashflow-realisation.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-line-chart fa-fw mr-3"></span>
          <span class="menu-collapsed">Report Cash Flow Realisation</span>
          </a>
          </ul>
          </li>';
      }

      if($id2 == '62'){
          echo '<a href="../AP/e_statement.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-money fa-fw mr-3"></span>
          <span class="menu-collapsed">E-Statement</span>
          </a>';
      }

      $querysPl = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Payment List'");
      $rsPl = mysqli_fetch_array($querysPl);
      $idPl = isset($rsPl['id']) ? $rsPl['id'] : 0;

      if($idPl == '115'){
          echo '<a href="../AP/payment-list.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-list-alt fa-fw mr-3"></span>
          <span class="menu-collapsed">Payment List</span>
          </a>';
      }

      $querysTl = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Transfer List'");
      $rsTl = mysqli_fetch_array($querysTl);
      $idTl = isset($rsTl['id']) ? $rsTl['id'] : 0;

      if($idTl == '130'){
          echo '<a href="../AP/transfer-list.php" class="dropdown-item bg-dark text-white">
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
          <a href="../AP/approve-pv.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Payment Voucher</span>
          </a> ';
      }elseif($id == '42'){
          echo '
          <a href="../AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Incoming Bank</span>
          </a> ';
      }elseif($id == '43'){
          echo '
          <a href="../AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Outgoing bank</span>
          </a> ';
      }elseif($id == '41,42'){
          echo '
          <a href="../AP/approve-pv.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Payment Voucher</span>
          </a>
          <a href="../AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Incoming Bank</span>
          </a> ';
      }elseif($id == '41,43'){
          echo '
          <a href="../AP/approve-pv.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Payment Voucher</span>
          </a>
          <a href="../AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Outgoing bank</span>
          </a> ';
      }elseif($id == '42,43'){
          echo '
          <a href="../AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Incoming Bank</span>
          </a>
          <a href="../AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Outgoing bank</span>
          </a>';
      }elseif($id == '41,42,43'){
          echo '
          <a href="../AP/approve-pv.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Payment Voucher</span>
          </a>
          <a href="../AP/approve-inbank.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Incoming Bank</span>
          </a>
          <a href="../AP/approve_bank_out.php" class="dropdown-item bg-dark text-white">
          <span class="fa fa-thumbs-up fa-fw "></span>
          <span class="menu-collapsed">Outgoing bank</span>
          </a> ';
      }else{
          echo '';
      }

          if(strpos($id_app, '117') !== false){
              echo '<a href="../AP/approve-payment-list-first.php" class="dropdown-item bg-dark text-white">
              <span class="fa fa-ticket fa-fw "></span>
              <span class="menu-collapsed">Payment List - First</span>
              '.$notifpl1.'
              </a>';
          }

          if(strpos($id_app, '118') !== false){
              echo '<a href="../AP/approve-payment-list-second.php" class="dropdown-item bg-dark text-white">
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
      <a href="../AP/cash-in.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-sign-in fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/cash-out.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-sign-out fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>
      <a href="../AP/petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-credit-card fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a>
      <a href="../AP/petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-credit-card fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a>
      <a href="../AP/cashreport.php" class="dropdown-item bg-dark text-white">
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
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a> ';
  }elseif($id == '45'){
      echo '
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a> ';
  }elseif($id == '46'){
      echo '
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a> ';
  }elseif($id == '47'){
      echo '
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a> ';
  }elseif($id == '44,45'){
      echo '
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>';
  }elseif($id == '44,46'){
      echo '
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a> ';
  }elseif($id == '44,47'){
      echo '
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a> ';
  }elseif($id == '45,46'){
      echo '
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a>';
  }elseif($id == '45,47'){
      echo '
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a> ';
  }elseif($id == '46,47'){
      echo '
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a>
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a> ';
  }elseif($id == '44,45,46'){
      echo '
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a> ';
  }elseif($id == '44,45,47'){
      echo '
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a> ';
  }elseif($id == '44,46,47'){
      echo '
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a>
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a> ';
  }elseif($id == '45,46,47'){
      echo '
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a>
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash Out</span>
      </a> ';
  }elseif($id == '44,45,46,47'){
      echo '
      <a href="../AP/approve-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash In</span>
      </a>
      <a href="../AP/approve-cashout.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Cash Out</span>
      </a>
      <a href="../AP/approve-petty-cashin.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-thumbs-up fa-fw"></span>
      <span class="menu-collapsed">Petty Cash In</span>
      </a>
      <a href="../AP/approve-petty-cashout.php" class="dropdown-item bg-dark text-white">
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
         echo'<a href="../AP/memorial-journal.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-bars fa-fw"></span>
         <span class="menu-collapsed">Memorial Journal</span>
         </a>';
     }if(strpos($id, '51') !== false){ 
         echo'<a href="../AP/list-journal.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-list-alt fa-fw"></span>
         <span class="menu-collapsed">List Journal</span>
         </a>';
     }if(strpos($id, '52') !== false){ 
         echo'<a href="../AP/general-ledger.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-print fa-list"></span>
         <span class="menu-collapsed">General Ledger</span>
         </a>';
         if($user == 'indro' || $user == 'willy' || $user == 'steven'){
            echo'<a href="../AP/general_ledger.php" class="dropdown-item bg-dark text-white">
         <span class="fa fa-print fa-list"></span>
         <span class="menu-collapsed">General Ledger New</span>
         </a>';
          }
     }if(strpos($id, '104') !== false){ 
         echo'<a href="../AP/trial_balance.php" class="dropdown-item bg-dark text-white">
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
      echo'<a href="../AP/other_receivable_report.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-fax fa-fw"></span>
      <span class="menu-collapsed">Other Receivable</span>
      </a>';
  }
  if(strpos($id, '65') !== false){
      echo'<a href="../AP/other_payable_report.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-fax fa-fw"></span>
      <span class="menu-collapsed">Other Payable</span>
      </a>';
  }
  if(strpos($id, '82') !== false){
      echo'<a href="../AP/purchase_advance_report.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-fax fa-fw"></span>
      <span class="menu-collapsed">Purchase Advance</span>
      </a>';
  }
  if(strpos($id, '105') !== false){
      echo'<a href="../AP/prepaid_tax_report.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-fax fa-fw"></span>
      <span class="menu-collapsed">Prepaid Tax</span>
      </a>';
  }

  echo'</ul>
  </li>';
  if(strpos($id, '53') !== false){ 
   echo'<li class="dropdown-submenu ">
   <a class="dropdown-item bg-dark text-white" href="#">
   <span class="fa fa-balance-scale fa-fw"></span>
   <span class="menu-collapsed">Financial Statement</span>
   </a>
   <ul class="dropdown-menu bg-dark text-white" role="menu">
   <a href="../AP/financial-statement-ytd.php" class="dropdown-item bg-dark text-white">
   <span class="fa fa-calendar fa-fw mr-3"></span>
   <span class="menu-collapsed">Year To Date</span>
   </a>
   <a href="../AP/trial-balance-monthly.php" class="dropdown-item bg-dark text-white">
   <span class="fa fa-calendar-o fa-fw mr-3"></span>
   <span class="menu-collapsed">Monthly</span>
   </a>
   </ul>
   </li>';
}

if($user == 'indro' || $user == 'willy' || $user == 'steven'){ 
   echo'<li class="dropdown-submenu ">
   <a class="dropdown-item bg-dark text-white" href="#">
   <span class="fa fa-balance-scale fa-fw"></span>
   <span class="menu-collapsed">Financial Statement 2</span>
   </a>
   <ul class="dropdown-menu bg-dark text-white" role="menu">
   <a href="../AP/financial_statement_ytd.php" class="dropdown-item bg-dark text-white">
   <span class="fa fa-calendar fa-fw mr-3"></span>
   <span class="menu-collapsed">Year To Date</span>
   </a>
   <a href="../AP/financial_statement_monthly.php" class="dropdown-item bg-dark text-white">
   <span class="fa fa-calendar-o fa-fw mr-3"></span>
   <span class="menu-collapsed">Monthly</span>
   </a>
   </ul>
   </li>';
}

}

if(strpos($id2, '88') !== false){ 
        // code...
 echo'<a href="../AP/closing-periode.php" class="dropdown-item bg-dark text-white">
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
  echo'<a href="../AP/repost-bank-out.php" class="dropdown-item bg-dark text-white">
  <span class="fas fa-landmark"></span>
  <span class="menu-collapsed">Bank Out</span>
  </a>';
}
if(strpos($id, '107') !== false){
  echo'<a href="../AP/repost-bpb.php" class="dropdown-item bg-dark text-white">
  <span class="fas fa-archive"></span>
  <span class="menu-collapsed">BPB</span>
  </a>';
}
echo'</ul>
</li>';
if(strpos($id, '106') !== false){ 
  echo'<a href="../AP/rekonsiliasi_jurnal_bpb.php" class="dropdown-item bg-dark text-white">
  <span class="fas fa-file-contract"></span>
  <span class="menu-collapsed">Rekonsiliasi Jurnal-BPB</span>
  </a>';
}

if(strpos($id, '108') !== false){ 
  echo'<a href="../AP/edit-journal.php" class="dropdown-item bg-dark text-white">
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


      <a href="../AP/ca_fabric_list_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-barcode fa-fw "></span>
      <span class="menu-collapsed">List Barcode</span>
      </a>
      <a href="../AP/ca_fabric_trx_in_new.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-cart-arrow-down fa-fw "></span>
      <span class="menu-collapsed">Trx Item In</span>
      </a>
      <a href="../AP/ca_fabric_trx_in_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-cart-arrow-down fa-fw "></span>
      <span class="menu-collapsed">Trx Barcode In</span>
      </a>
      
      <a href="../AP/ca_fabric_trx_out_item.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-paper-plane fa-fw "></span>
      <span class="menu-collapsed">Trx Item Out</span>
      </a>
      <a href="../AP/ca_fabric_trx_out_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-paper-plane fa-fw "></span>
      <span class="menu-collapsed">Trx Barcode Out</span>
      </a>
      
      <a href="../AP/ca_fabric_summary_item.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Item</span>
      </a>
      <a href="../AP/ca_fabric_summary_barcode.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Barcode</span>
      </a>
      <a href="../AP/ca_fabric_summary_sc.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Subcont</span>
      </a>
      <a href="../AP/ca_fabric_summary_subcont.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-calculator fa-fw "></span>
      <span class="menu-collapsed">Summary Subcont New</span>
      </a>
      <a href="../AP/update_bpb_fabric.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-pencil-square fa-fw "></span>
      <span class="menu-collapsed">Update Trx In</span>
      </a>
      <a href="../AP/update_np_revisi.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-pencil-square fa-fw "></span>
      <span class="menu-collapsed">Revisi Barcode</span>
      </a>
      <a href="../AP/adjust-subcont.php" class="dropdown-item bg-dark text-white">
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

      
      <a href="../AP/item_general_usage.php" class="dropdown-item bg-dark text-white">
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
        echo '<a href="../AP/update-bpb-fabric.php" class="dropdown-item bg-dark text-white">
        <span class="fas fa-warehouse fa-fw "></span>
        <span class="menu-collapsed">Fabric</span>
        </a>';

        $pendingApproveCount = 0;
        $cntApproveRs = mysqli_query($conn1, "SELECT COUNT(*) as cnt FROM update_bpb_fabric_h WHERE status NOT IN ('Approved','Cancel')");
        if ($cntApproveRs) {
            $cntApproveRow = mysqli_fetch_assoc($cntApproveRs);
            $pendingApproveCount = (int) ($cntApproveRow['cnt'] ?? 0);
        }

        echo '<a href="../AP/approve_update_bpb_fabric.php" class="dropdown-item bg-dark text-white d-flex justify-content-between align-items-center">
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
      echo '<a href="../AP/ca_fabric_closing_periode.php" class="dropdown-item bg-dark text-white">
      <span class="fas fa-warehouse fa-fw "></span>
      <span class="menu-collapsed">Fabric Warehouse</span>
      </a>';
  }

      echo '</ul>
      </li>';

  ?>

<!--   <a href="../AP/ca_fabric_trx_in.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-cart-arrow-down fa-fw "></span>
      <span class="menu-collapsed">Trx In</span>
      </a>
<a href="../AP/ca_fabric_trx_out.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-paper-plane fa-fw "></span>
      <span class="menu-collapsed">Trx Out</span>
      </a>
<a href="../AP/ca_fabric_summary.php" class="dropdown-item bg-dark text-white">
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
         echo'<a href="../AP/exim-calculatin-cost-report.php" class="dropdown-item bg-dark text-white">
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
        echo '<a href="../AP/maintain-bpb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw "></span>
        <span class="menu-collapsed">BPB</span>
        </a>';
    }
    if($id == '34'){
        echo '
        <a href="../AP/formreversebpb.php" class="dropdown-item bg-dark text-white">
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
        <a href="../AP/pengajuan_ftrcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">FTR CBD</span>
        </a>
        <a href="../AP/pengajuan_ftrdp.php" class="dropdown-item bg-dark text-white">
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
        <a href="../AP/pengajuankb.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed"> Kontrabon Reg</span>
        </a>
        <a href="../AP/pengajuankb_cbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">Kontrabon CBD</span>
        </a>
        <a href="../AP/pengajuankb_dp.php" class="dropdown-item bg-dark text-white">
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
        <a href="../AP/pengajuanpayment.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment Reg</span>
        </a>
        <a href="../AP/pengajuanpaymentcbd.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment CBD</span>
        </a>
        <a href="../AP/pengajuanpaymentdp.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-minus-square-o fa-fw"></span>
        <span class="menu-collapsed">List Payment DP</span>
        </a>
        </ul>
        </li>';
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
        echo '<a href="../AP/reverse_kontrabon.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-angle-right fa-fw"></span>
        <span class="menu-collapsed">Kontrabon</span>
        </a>';
    }
    if(strpos($id, '92') !== false){
        echo '<a href="../AP/reverse_payment.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-angle-right fa-fw"></span>
        <span class="menu-collapsed">Payment</span>
        </a>';
    }

    if(strpos($id, '98') !== false){
      echo '<a href="../AP/reverse_bank.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Bank</span>
      </a>';
  }

  if(strpos($id, '101') !== false){
      echo '<a href="../AP/reverse_petty_cash.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Petty Cash</span>
      </a>';
  }

  if(strpos($id, '120') !== false){
      echo '<a href="../AP/reverse_payment_voucher.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Payment Voucher</span>
      </a>';
  }

  if(strpos($id, '121') !== false){
      echo '<a href="../AP/reverse_payment_voucher_list.php" class="dropdown-item bg-dark text-white">
      <span class="fa fa-angle-right fa-fw"></span>
      <span class="menu-collapsed">Payment Voucher List</span>
      </a>';
  }

  if(strpos($id, '122') !== false){
      echo '<a href="../AP/reverse_payment_list.php" class="dropdown-item bg-dark text-white">
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

$sql_rvskbon = mysqli_query($conn2," select COUNT(id) jml from (select id from ap_reverse_h where rvs_number like '%SI/%' and status = 'DRAFT') a");
  $row_rvskbon = mysqli_fetch_array($sql_rvskbon);
  $count_rvskbon = $row_rvskbon['jml'];
  if($count_rvskbon != '0'){
    $notif_rvskbon = '<span class="notif-badge">'.$count_rvskbon.'</span>';
}else{
    $notif_rvskbon = '';
}

$sql_rvsbank = mysqli_query($conn2," select COUNT(id) jml from (select id from ap_reverse_h where rvs_number like '%BN/%' and status = 'DRAFT') a");
  $row_rvsbank = mysqli_fetch_array($sql_rvsbank);
  $count_rvsbank = $row_rvsbank['jml'];
  if($count_rvsbank != '0'){
    $notif_rvsbank = '<span class="notif-badge">'.$count_rvsbank.'</span>';
}else{
    $notif_rvsbank = '';
}

$sql_rvspc = mysqli_query($conn2," select COUNT(id) jml from (select id from ap_reverse_h where rvs_number like '%PC/%' and status = 'DRAFT') a");
  $row_rvspc = mysqli_fetch_array($sql_rvspc);
  $count_rvspc = $row_rvspc['jml'];
  if($count_rvspc != '0'){
    $notif_rvspc = '<span class="notif-badge">'.$count_rvspc.'</span>';
}else{
    $notif_rvspc = '';
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
    echo '<a href="../AP/form_approve_reverse_kontrabon.php" class="dropdown-item bg-dark text-white">
    <span class="fas fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Kontrabon</span>
    '.$notif_rvskbon.'
    </a>';
}

if(strpos($id, '93') !== false){
    echo '<a href="../AP/form_approve_reverse_payment.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Payment</span>
    '.$notif_rvspay.'
    </a>';
}

if(strpos($id, '100') !== false){
    echo '<a href="../AP/form_approve_reverse_bank.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Bank</span>
    '.$notif_rvsbank.'
    </a>';
}

if(strpos($id, '103') !== false){
    echo '<a href="../AP/form_approve_reverse_petty_cash.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Petty Cash</span>
    '.$notif_rvspc.'
    </a>';
}

if(strpos($id, '124') !== false){
    echo '<a href="../AP/form_approve_reverse_payment_list.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Payment List</span>
    '.$notif_rvspl.'
    </a>';
}

if(strpos($id, '127') !== false){
    echo '<a href="../AP/form_approve_reverse_payment_voucher_list.php" class="dropdown-item bg-dark text-white">
    <span class="fa fa-angle-right fa-fw "></span>
    <span class="menu-collapsed">Payment Voucher List</span>
    '.$notif_rvspvl.'
    </a>';
}

if(strpos($id, '129') !== false){
    echo '<a href="../AP/form_approve_reverse_payment_voucher.php" class="dropdown-item bg-dark text-white">
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
    <a href="" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cogs mr-1"></span> Setting<span class="caret"></span></a>
    <ul class="dropdown-menu bg-dark text-white" role="menu">
      <?php
      $querys = mysqli_query($conn1,"select Groupp from userpassword where username = '$user'");
      $rs = mysqli_fetch_array($querys);
      $group = isset($rs['Groupp']) ? $rs['Groupp'] : null;

      if($group != 'STAFF' && $group != null){                      
        echo '
        <a href="../AP/userrole.php" class="dropdown-item bg-dark text-white">
        <span class="fa fa-user-plus fa-fw"></span>
        <span class="menu-collapsed">Userrole</span>
        </a>';
    }else{
        echo '';
    }
    ?>
</ul>
</li>
<!-- END Menu Other Menu -->

<?php if ($user == 'indro'): ?>
<li class="nav-item active">
  <a href="../AP/project.php" class="nav-link">
    <span class="fa fa-rocket mr-1"></span> Project
  </a>
</li>
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
      <a class="nav-link" href="../function/logout.php" style="font-size:12px;"><span class="fa fa-power-off">Log-out</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="../index.php" style="font-size:12px;"><span class="fa fa-home">Home</a>
        </li>
        <li class="nav-item active">
          <span class="navbar-text text-white" style="font-size:12px;"><span class="fa fa-user"> <?php echo $user ?> </span></span>
      </li>
  </div>

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