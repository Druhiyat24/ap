<?php
ini_set('memory_limit', '4096M');
set_time_limit(0);

session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
include '../../conn/conn.php';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : '';
if ($user == '') {
  echo "<script>window.location = '../function/logout.php';</script>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>SB AP v2.0</title>

  <style>
    .form-control-plaintext { border: 1px solid grey; }
    .form-row { margin-right: 0; margin-left: -10px; }
    .filter-option { font-size: 12px; }
    .datatable_wrapper { font-size: 12px; }
    table { font-size: 12px; }
    .swal-wide { width:400px !important; height:200px !important; }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
    input[type=number] { -moz-appearance:textfield; }
    .tableFix { position:relative; overflow:auto; height:100px; font-size:12px; }
    .tableFix table { width:100%; border-collapse:collapse; }
    .tableFix th, .tableFix td { padding:8px; text-align:left; }
    .tableFix thead { position:sticky; top:0; background:#F0F8FF; }
    .dropdown-submenu { position:relative; }
    .dropdown-submenu>.dropdown-menu { top:0; left:100%; margin-top:-6px; border-radius:0 6px 6px 6px; }
    .dropdown-submenu:hover>.dropdown-menu { display:block; }
    .dropdown-submenu>a:after { display:block; content:" "; float:right; width:0; height:0; border-color:transparent; border-style:solid; border-width:5px 0 5px 5px; border-left-color:#ccc; margin-top:5px; margin-right:-10px; }
    .dropdown-submenu:hover>a:after { border-left-color:#fff; }
  </style>

  <!-- Bootstrap 4 -->
  <link href="../css/4.1.1/bootstrap.min.css" rel="stylesheet">
  <!-- AdminLTE 3 -->
  <link href="../css/4.1.1/adminlte/adminlte.min.css" rel="stylesheet">
  <!-- Plugins -->
  <link href="../css/4.1.1/datatables.min.css" rel="stylesheet">
  <link href="../css/4.1.1/bootstrap-select.min.css" rel="stylesheet">
  <link href="../fontawesome5/css/all.min.css" rel="stylesheet">
  <link href="../fontawesome5/css/v4-shims.min.css" rel="stylesheet">
  <link href="../css/4.1.1/datepicker3.css" rel="stylesheet">
  <link href="../css/4.1.1/bootstrap-multiselect.min.css" rel="stylesheet">
  <link href="../css/4.1.1/select2.min.css" rel="stylesheet">
  <link href="../css/4.1.1/select2-bootstrap4.min.css" rel="stylesheet">
  <link href="../css/4.1.1/responsive.bootstrap4.min.css" rel="stylesheet">
  <link href="../css/4.1.1/sweetalert2.min.css" rel="stylesheet">
  <link href="../datatables_responsive/fixedColumns.css" rel="stylesheet">
  <!-- Modern Theme (must be last) -->
  <link href="../css/modern-theme.css" rel="stylesheet">

  <!-- jQuery + Bootstrap JS + AdminLTE JS (needed for sidebar treeview) -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../css/4.1.1/adminlte/adminlte.min.js"></script>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- ── Top navbar ──────────────────────────────────────── -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <span class="nav-link navbar-text">
        <i class="fa fa-user mr-1"></i><?php echo $user; ?>
      </span>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../index.php" title="Home">
        <i class="fas fa-home"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="../function/logout.php" title="Logout">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </li>
  </ul>
</nav>

<!-- ── Sidebar ──────────────────────────────────────────── -->
<aside class="main-sidebar elevation-4">
  <a href="../index.php" class="brand-link">
    <img src="../img/NAG logo SIGN.png" alt="NAG Logo" class="brand-image img-circle elevation-3">
    <span class="brand-text ml-2">
      <span class="brand-name">Nirwana Alabare</span>
      <span class="brand-sub">AP Management System</span>
    </span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <?php $AP = '../AP/'; include '../includes/sidebar_menu.php'; ?>
    </nav>
  </div>
</aside><!-- /.sidebar -->

<!-- ── Content wrapper (pages render inside here) ─────── -->
<div class="content-wrapper">
  <section class="content">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selects = document.querySelectorAll('select[name="nama_supp"]');
    selects.forEach(function(select) {
        if (!select.value || select.value === '') {
            const optAll = select.querySelector('option[value="ALL"]');
            if (optAll) optAll.selected = true;
        }
    });
});
</script>
