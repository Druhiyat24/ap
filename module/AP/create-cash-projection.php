<?php include '../header.php' ?>
<?php
/* Cash > Projection > Other - halaman CREATE.
   Satu halaman, tiga tab - bentuknya mengikuti create-in-bank.php.
   Ketiga tabnya sudah jalan: Book Transfer, Other Inflow, PV Expense. */

include 'cash_projection/cfp_lib.php';

$akun     = cfp_daftar_akun($conn1);   // bank + kas, untuk Book Transfer
$bank     = cfp_daftar_bank($conn1);   // bank saja, untuk Other Inflow
$kategori     = cfp_daftar_kategori($conn1, 'Cash In');    // Other Inflow
$kategori_out = cfp_daftar_kategori($conn1, 'Cash Out');   // PV Expense
$supplier     = cfp_daftar_supplier($conn1);               // PV Expense
$customer     = cfp_daftar_customer($conn1);               // Other Inflow
?>
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-pco-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-pco-form.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">
<link rel="stylesheet" href="../css/app-cfp.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-cfp.css'); ?>">

<!-- Pustaka dimuat DI ATAS isi halaman: berkas tab yang di-include
     (cash_projection/bt_form.php) punya skrip sendiri yang memakai jQuery. -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/select2.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

<div class="container-fluid mt-3 p-3">
  <div class="pco-card">
    <div class="pco-head">
      <span class="pco-head-icon"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
      <div>
        <h1>Form Projection Other</h1>
        <span class="pco-crumb">Bank &rsaquo; Projection &rsaquo; Other &rsaquo; Create</span>
      </div>
    </div>

    <div class="pco-body">

      <div class="tab-container">
        <button type="button" class="tablinks active" data-tab="tab-bt">
          <i class="fa fa-exchange"></i> Book Transfer
        </button>
        <button type="button" class="tablinks" data-tab="tab-oi">
          <i class="fa fa-sign-in"></i> Other Inflow
        </button>
        <button type="button" class="tablinks" data-tab="tab-pv">
          <i class="fa fa-sign-out"></i> PV Expense
        </button>
      </div>

      <div id="tab-bt" class="tabcontent on">
        <?php include 'cash_projection/bt_form.php'; ?>
      </div>

      <div id="tab-oi" class="tabcontent">
        <?php include 'cash_projection/oi_form.php'; ?>
      </div>

      <div id="tab-pv" class="tabcontent">
        <?php include 'cash_projection/pv_form.php'; ?>
      </div>

    </div>

    <div class="pco-foot">
      <button type="button" class="app-btn app-btn-danger" id="btnback">
        <i class="fa fa-angle-double-left"></i> Back
      </button>
      <button type="button" class="app-btn app-btn-primary" id="btnsave">
        <i class="fa fa-save"></i> Save
      </button>
    </div>
  </div>
</div>


<script>
// Hide submenus
$('#body-row .collapse').collapse('hide');
$('#collapse-icon').addClass('fa-angle-double-left');
$('[data-toggle=sidebar-colapse]').click(function () { SidebarCollapse(); });

function SidebarCollapse() {
    $('.menu-collapsed').toggleClass('d-none');
    $('.sidebar-submenu').toggleClass('d-none');
    $('.submenu-icon').toggleClass('d-none');
    $('#sidebar-container').toggleClass('sidebar-expanded sidebar-collapsed');

    var SeparatorTitle = $('.sidebar-separator-title');
    if (SeparatorTitle.hasClass('d-flex')) {
        SeparatorTitle.removeClass('d-flex');
    } else {
        SeparatorTitle.addClass('d-flex');
    }
    $('#collapse-icon').toggleClass('fa-angle-double-left fa-angle-double-right');
}
</script>

<script>
$(function () {
  // dipasang di sini supaya tab mana pun yang dimuat tetap kebagian
  $('.app-select2').each(function () {
    $(this).select2({ width: '100%', dropdownParent: $('body') });
  });
  // tags:true -> boleh memilih dari daftar ATAU mengetik yang tidak ada di daftar
  $('.app-select2-tag').each(function () {
    $(this).select2({ width: '100%', dropdownParent: $('body'), tags: true });
  });
  $('.tanggal').datepicker({ format: 'dd M yyyy', autoclose: true });

  // pindah tab
  $('.tablinks').on('click', function () {
    if ($(this).prop('disabled')) { return; }
    $('.tablinks').removeClass('active');
    $(this).addClass('active');
    $('.tabcontent').removeClass('on');
    $('#' + $(this).data('tab')).addClass('on');
  });

  $('#btnback').on('click', function () {
    location.href = 'cash-projection.php';
  });
});
</script>

</body>

</html>
