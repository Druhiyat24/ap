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
      top: 0;
      left: 100%;
      margin-top: -6px;
      margin-left: -1px;
      -webkit-border-radius: 0 6px 6px 6px;
      -moz-border-radius: 0 6px 6px;
      border-radius: 0 6px 6px 6px;
  }

  .dropdown-submenu:hover>.dropdown-menu {
      display: block;
  }

  .dropdown-submenu>a:after {
      display: block;
      content: " ";
      float: right;
      width: 0;
      height: 0;
      border-color: transparent;
      border-style: solid;
      border-width: 5px 0 5px 5px;
      border-left-color: black;
      margin-top: 5px;
      margin-right: -10px;
  }

  .dropdown-submenu:hover>a:after {
      border-left-color: #fff;
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

<!-- Bootstrap 4 -->
<link href="css/4.1.1/bootstrap.min.css" rel="stylesheet">
<!-- AdminLTE 3 -->
<link href="css/4.1.1/adminlte/adminlte.min.css" rel="stylesheet">
<!-- Plugins -->
<link href="css/4.1.1/datatables.min.css" rel="stylesheet">
<link href="css/4.1.1/bootstrap-select.min.css" rel="stylesheet">
<link href="fontawesome5/css/all.min.css" rel="stylesheet">
<link href="fontawesome5/css/v4-shims.min.css" rel="stylesheet">
<link href="css/4.1.1/select2.min.css" rel="stylesheet">
<link href="css/4.1.1/select2-bootstrap4.min.css" rel="stylesheet">
<!-- Modern Theme (must be last) -->
<link href="css/modern-theme.css" rel="stylesheet">
<!-- jQuery + Bootstrap JS + AdminLTE JS (sidebar treeview) -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="css/4.1.1/adminlte/adminlte.min.js"></script>
<!-- Chart libraries — must be in <head> so dashboard include scripts can use them -->
<script src="css/4.1.1/apexchart/apexcharts.js"></script>
<script src="css/4.1.1/amchart/index.js"></script>
<script src="css/4.1.1/amchart/xy.js"></script>
<script src="css/4.1.1/amchart/animated.js"></script>
<script src="css/4.1.1/amchart/radar.js"></script>
<script src="css/4.1.1/fusionchart/fusioncharts.js"></script>
<script src="css/4.1.1/fusionchart/themes/fusioncharts.theme.fusion.js"></script>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- ── Top navbar ──────────────────────────────────────── -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <span class="nav-link navbar-text"><i class="fa fa-user mr-1"></i><?php echo $user; ?></span>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.php"><i class="fas fa-home"></i></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="function/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li>
  </ul>
</nav>

<!-- ── Sidebar ──────────────────────────────────────────── -->
<aside class="main-sidebar elevation-4">
  <a href="index.php" class="brand-link">
    <img src="img/NAG logo SIGN.png" alt="NAG Logo" class="brand-image img-circle elevation-3">
    <span class="brand-text ml-2">
      <span class="brand-name">Nirwana Alabare</span>
      <span class="brand-sub">AP Management System</span>
    </span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <?php $AP = 'AP/'; include 'includes/sidebar_menu.php'; ?>
    </nav>
  </div>
</aside>

<!-- ── Content wrapper ──────────────────────────────────── -->
<div class="content-wrapper">
<section class="content">

<!-- Content Header -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2 align-items-center">
      <div class="col-sm-6">
        <h1 class="m-0" style="font-size:1.3rem; font-weight:700; color:#2d3748;">
          <i class="fas fa-tachometer-alt mr-2" style="color:#3949ab;"></i>Dashboard
        </h1>
      </div>
      <div class="col-sm-6 text-right">
        <?php
        $sql = mysqli_query($conn1,"select useraccess.menu as menu, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and menurole.status = 'DSB'");
        $has_dsb = mysqli_num_rows($sql) > 0;
        if ($has_dsb):
        ?>
        <select class="form-control form-control-sm d-inline-block w-auto"
                name="pilih_dashboard" id="pilih_dashboard"
                onchange="ubahdashboard(this.value)">
          <option value="-">Select Dashboard</option>
          <?php
          mysqli_data_seek($sql, 0);
          while ($row = mysqli_fetch_array($sql)) {
              $sel = ($row['id'] == ($_POST['pilih_dashboard'] ?? '')) ? 'selected' : '';
              echo '<option value="'.htmlspecialchars($row['id']).'" '.$sel.'>'.htmlspecialchars($row['menu']).'</option>';
          }
          ?>
        </select>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div id="isi_dashboard">
      <?php
      $id_dsb = '';
      $querys = mysqli_query($conn1,"select menurole.id as id, id_dsb from useraccess inner join menurole on menurole.menu = useraccess.menu left join menurole_dsb dsb on dsb.username = useraccess.username where useraccess.username = '$user' and menurole.status = 'DSB'");
      $rs = mysqli_fetch_array($querys);
      $id_dsb = isset($rs['id_dsb']) ? $rs['id_dsb'] : '';
      if     ($id_dsb == '81') include '../dashboard/dashboard-bank.php';
      elseif ($id_dsb == '80') include '../dashboard/dashboard-ap.php';
      else                     include '../dashboard/welcome-page.php';
      ?>
    </div>
  </div>
</div>

<!-- Chart libraries loaded in <head> -->
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





</section><!-- /.content -->
</div><!-- /.content-wrapper -->
</div><!-- /.wrapper -->
</body>
</html>
