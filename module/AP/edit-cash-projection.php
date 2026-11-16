<?php include '../header.php' ?>
<?php
/* Cash > Projection > Other - halaman EDIT.
   Formulirnya BERKAS YANG SAMA dgn Create (cash_projection/bt_form.php),
   cuma diberi isi awal - biar tidak ada dua versi yang lama-lama beda.
   Dokumen yang sudah Cancel tidak boleh dibuka di sini. */

include 'cash_projection/cfp_lib.php';

$no_doc = isset($_GET['no_doc']) ? trim($_GET['no_doc']) : '';
$doc    = mysqli_real_escape_string($conn1, $no_doc);

$baris = array();   // untuk tab Book Transfer
$oi    = array();   // untuk tab Other Inflow, dipakai juga oleh PV Expense
$jenis = '';
$batal = false;

$sql = mysqli_query($conn1, "select * from ap_cf_projection
                             where no_doc = '$doc' order by id");
while ($row = mysqli_fetch_assoc($sql)) {
    $jenis = $row['jenis'];
    if (strcasecmp($row['status'], 'Cancel') === 0) { $batal = true; }

    // Other Inflow: bentuknya ikut oi_form.php
    $oi[] = array(
        'nama'        => $row['nama_supplier'],   // kolomnya bernama nama_supplier; kunci kiriman ke formulir tetap nama
        'id_supplier' => $row['id_supplier'],   // terisi hanya di PV Expense
        'kategori'   => $row['kategori'],
        'bank'       => $row['akun_tujuan'],
        'tanggal'    => date('d M Y', strtotime($row['tanggal'])),
        'curr'       => $row['curr'],
        'amount'     => (float) $row['amount'],
        'rate'       => (float) $row['rate'],
        'amount_idr' => (float) $row['amount_idr'],
        'amount_usd' => (float) $row['amount_usd'],
        'deskripsi'  => $row['deskripsi'],
        'pc'         => $row['pc_tujuan'],
    );

    // Book Transfer: bentuknya ikut bt_form.php
    $baris[] = array(
        'source'      => $row['akun_source'],
        'tujuan'      => $row['akun_tujuan'],
        'tanggal'     => date('d M Y', strtotime($row['tanggal'])),
        'curr'        => $row['curr'],
        'amount'      => (float) $row['amount'],
        'rate'        => (float) $row['rate'],
        'amount_idr'  => (float) $row['amount_idr'],
        'amount_usd'  => (float) $row['amount_usd'],
        'deskripsi'   => $row['deskripsi'],
        'pc_source'   => $row['pc_source'],
        'pc_tujuan'   => $row['pc_tujuan'],
        'curr_tujuan' => $row['curr_tujuan'],
        'intra_inter' => $row['intra_inter'],
    );
}

$akun         = cfp_daftar_akun($conn1);
$bank         = cfp_daftar_bank($conn1);
$kategori     = cfp_daftar_kategori($conn1, 'Cash In');    // Other Inflow
$kategori_out = cfp_daftar_kategori($conn1, 'Cash Out');   // PV Expense
$supplier     = cfp_daftar_supplier($conn1);               // PV Expense
$customer     = cfp_daftar_customer($conn1);               // Other Inflow

/* siapkan untuk berkas tab */
$cfp_mode  = 'edit';
$cfp_doc   = $no_doc;
$cfp_baris = $baris;
$cfp_oi    = $oi;
/* PV Expense bentuk barisnya persis sama dgn Other Inflow - cuma
   tanggalnya dibaca sbg Due Date dan namanya nama supplier. */
$cfp_pv    = $oi;

/* tab mana yang dibuka - ikut jenis dokumennya */
$tab_bt = ($jenis == 'BOOK TRANSFER');
$tab_oi = ($jenis == 'OTHER INFLOW');
$tab_pv = ($jenis == 'PV EXPENSE');
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
      <span class="pco-head-icon"><i class="fa fa-pencil" aria-hidden="true"></i></span>
      <div>
        <h1>Edit Projection Other</h1>
        <span class="pco-crumb">Bank &rsaquo; Projection &rsaquo; Other &rsaquo; Edit &rsaquo;
          <b><?php echo htmlspecialchars($no_doc, ENT_QUOTES); ?></b></span>
      </div>
    </div>

    <div class="pco-body">

      <?php if (count($baris) == 0 || $batal) { ?>

        <div class="alert alert-warning mb-0">
          <?php echo ($batal)
              ? 'This document has been cancelled and can no longer be edited.'
              : 'Document not found.'; ?>
        </div>

      <?php } else { ?>

        <!-- Jenis dokumen tidak bisa diubah waktu edit, jadi tab lain dimatikan. -->
        <div class="tab-container">
          <button type="button" class="tablinks<?php echo $tab_bt ? ' active' : ''; ?>"
                  data-tab="tab-bt" <?php echo $tab_bt ? '' : 'disabled'; ?>>
            <i class="fa fa-exchange"></i> Book Transfer
          </button>
          <button type="button" class="tablinks<?php echo $tab_oi ? ' active' : ''; ?>"
                  data-tab="tab-oi" <?php echo $tab_oi ? '' : 'disabled'; ?>>
            <i class="fa fa-sign-in"></i> Other Inflow
          </button>
          <button type="button" class="tablinks<?php echo $tab_pv ? ' active' : ''; ?>"
                  data-tab="tab-pv" <?php echo $tab_pv ? '' : 'disabled'; ?>>
            <i class="fa fa-sign-out"></i> PV Expense
          </button>
        </div>

        <?php if ($tab_bt) { ?>
          <div id="tab-bt" class="tabcontent on">
            <?php include 'cash_projection/bt_form.php'; ?>
          </div>
        <?php } ?>

        <?php if ($tab_oi) { ?>
          <div id="tab-oi" class="tabcontent on">
            <?php include 'cash_projection/oi_form.php'; ?>
          </div>
        <?php } ?>

        <?php if ($tab_pv) { ?>
          <div id="tab-pv" class="tabcontent on">
            <?php include 'cash_projection/pv_form.php'; ?>
          </div>
        <?php } ?>

      <?php } ?>

    </div>

    <div class="pco-foot">
      <button type="button" class="app-btn app-btn-danger" id="btnback">
        <i class="fa fa-angle-double-left"></i> Back
      </button>
      <?php if (count($baris) > 0 && !$batal) { ?>
        <button type="button" class="app-btn app-btn-primary" id="btnsave">
          <i class="fa fa-save"></i> Save
        </button>
      <?php } ?>
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
