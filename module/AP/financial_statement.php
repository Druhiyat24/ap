<?php include '../header.php' ?>
<?php
// Cek hak akses "Copy Saldo" SATU KALI di sini (dulu dicek terpisah di
// masing2 fs1_ytd/trial_balance.php & fs_ytd/trial_balance_ytd.php per-tab
// - sekarang tombolnya digabung jadi 1 di toolbar atas, jadi cukup dicek
// sekali). Query & menu key ('Acct - Copy saldo TB', id=56) disalin apa
// adanya dari kedua file itu, TIDAK diubah.
// ===== Hak akses menu FS terpadu (menurole baru) =====
// 3 role baru: 'FS - ALL' (akses semua tab), 'FS - Trial Balance Only' (hanya
// tab Trial Balance), 'FS - Copy Saldo' (izin tombol Copy Saldo). Menu FS
// terpadu ini menggantikan menu lama Trial Balance / Financial Statement (FS1)
// / Financial Statement 2 (FS2).
function fsUserHasRole($conn, $user, $menu) {
    $menu_esc = mysqli_real_escape_string($conn, $menu);
    $user_esc = mysqli_real_escape_string($conn, $user);
    $q = mysqli_query($conn, "SELECT 1 FROM useraccess WHERE username = '$user_esc' AND menu = '$menu_esc' LIMIT 1");
    return $q && mysqli_num_rows($q) > 0;
}
$hasFsAll    = fsUserHasRole($conn2, $user, 'FS - ALL');
$hasFsTbOnly = fsUserHasRole($conn2, $user, 'FS - Trial Balance Only');
// Copy Saldo: HANYA role baru 'FS - Copy Saldo'. Gate lama 'Acct - Copy saldo
// TB' sengaja TIDAK dipakai lagi supaya tombol tidak muncul sebelum role baru
// di-assign (permintaan: cutover bersih ke role baru).
$canCopySaldo = fsUserHasRole($conn2, $user, 'FS - Copy Saldo');
// Batasi ke tab Trial Balance SAJA kalau user cuma punya 'FS - Trial Balance
// Only' dan TIDAK punya 'FS - ALL'. User tanpa kedua role ini TIDAK dibatasi
// (fallback lihat semua) supaya tidak ada yang terkunci sebelum role di-assign.
$fsTbOnly = $hasFsTbOnly && !$hasFsAll;
?>

<style type="text/css">
    label {
        font-size: 14px;;
    }

    input {
        font-size: 14px;;
    }

.tab {
  display: flex;
  border-bottom: 2px solid #dee2e6; /* garis bawah tab bar */
  font-size: 13px;
}

.tab button {
  background: none;
  border: none;
  outline: none;
  padding: 8px 16px;
  cursor: pointer;
  color: #495057;
  transition: all 0.2s ease;
  font-weight: 500;
}

.tab button:hover {
  color: #0d6efd; /* biru saat hover */
}

.tab button.active {
  color: #0d6efd; /* teks aktif */
  border-bottom: 3px solid #0d6efd; /* garis bawah aktif */
  font-weight: 600;
}

.tabcontent {
  display: none;
  padding: 10px 0;
}

@media print {
  @page {
    margin: 0;
  }
  body {
    margin: 0;
    padding: 0;
  }
  .laporan-table {
    margin-top: 0 !important;
    padding-top: 0 !important;
  }
}

/* ===== Tombol filter (Search/Reset/Copy Saldo) - pill gradien konsisten
   dgn tombol Export Excel di seluruh tab report (bukan lagi kotak
   btn-info/btn-danger Bootstrap polos). Search & Copy Saldo pakai warna
   teal yang sama (aksi "maju"), Reset tetap dibedakan warna (aksi
   destruktif/reset) supaya tidak keliru diklik. */
.fs-filter-buttons {
  gap: 8px;
}

.fs-btn-pill {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 12.5px;
  font-weight: 600;
  letter-spacing: .2px;
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 9px 18px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transition: box-shadow 0.15s ease, transform 0.15s ease, background 0.15s ease;
}

.fs-btn-teal {
  background: linear-gradient(135deg, #17a2b8, #0f7c8f);
  box-shadow: 0 2px 6px rgba(15, 124, 143, 0.3);
}
.fs-btn-teal:hover {
  background: linear-gradient(135deg, #1cb8d1, #128a9f);
  box-shadow: 0 4px 10px rgba(15, 124, 143, 0.38);
  transform: translateY(-1px);
  color: #fff;
}

.fs-btn-coral {
  background: linear-gradient(135deg, #ef6a6a, #d9534f);
  box-shadow: 0 2px 6px rgba(217, 83, 79, 0.3);
}
.fs-btn-coral:hover {
  background: linear-gradient(135deg, #f37f7f, #e26460);
  box-shadow: 0 4px 10px rgba(217, 83, 79, 0.38);
  transform: translateY(-1px);
  color: #fff;
}

/* Hijau khusus tombol Copy Saldo supaya jelas beda dari Search (teal). */
.fs-btn-green {
  background: linear-gradient(135deg, #28c76f, #1f9d57);
  box-shadow: 0 2px 6px rgba(31, 157, 87, 0.3);
}
.fs-btn-green:hover {
  background: linear-gradient(135deg, #34d97e, #24ab60);
  box-shadow: 0 4px 10px rgba(31, 157, 87, 0.38);
  transform: translateY(-1px);
  color: #fff;
}

.fs-btn-pill:active {
  transform: translateY(0);
  box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
}

/* ===== Reskin bootstrap-select (dropdown Statement Format/Report Period/
   Profit Center) - library-nya SAMA PERSIS (bootstrap-select, sudah
   otomatis kasih tanda centang di item terpilih), cuma warnanya diselaraskan
   ke aksen teal & border lembut supaya senada dgn tombol & tab report. */
.filter-card .bootstrap-select > .dropdown-toggle {
  border: 1px solid #d7dce5 !important;
  border-radius: 8px !important;
  background: #fff !important;
  color: #2c3e50 !important;
  font-size: 13.5px;
  box-shadow: none !important;
}
.filter-card .bootstrap-select > .dropdown-toggle:focus {
  border-color: #17a2b8 !important;
  outline: none !important;
}
.filter-card .bootstrap-select .dropdown-menu {
  border: 1px solid #e5e9f2;
  border-radius: 10px;
  box-shadow: 0 8px 24px rgba(30, 58, 138, 0.12);
  padding: 6px;
  overflow: hidden;
}
.filter-card .bootstrap-select .dropdown-menu li a {
  border-radius: 6px;
  font-size: 13.5px;
  padding: 8px 12px;
}
.filter-card .bootstrap-select .dropdown-menu li.selected a,
.filter-card .bootstrap-select .dropdown-menu li a:hover {
  background-color: #e6f7fa;
  color: #0f7c8f;
}
.filter-card .bootstrap-select .dropdown-menu li.selected a .filter-option {
  font-weight: 600;
}
.filter-card .bootstrap-select .dropdown-menu .glyphicon-ok,
.filter-card .bootstrap-select .dropdown-menu .bs-ok-default {
  color: #17a2b8;
}

/* ===== Field bulan From/To - TETAP 2 field terpisah, hanya dibuat lebih
   ringkas (lebar tetap ~132px, tidak selebar kolom penuh) & rapi: ikon
   kalender di dalam, sudut membulat, aksen teal senada dropdown & tombol.
   Input tetap <input class="tanggal" id="start_date"/"end_date"> asli supaya
   datepicker (format "M yyyy") & backend TIDAK berubah. */
.fs-month-field { position: relative; width: 132px; max-width: 100%; }
.fs-month-field .fs-month-ico {
  position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
  color: #17a2b8; font-size: 12.5px; pointer-events: none;
}
.fs-month-field .fs-month-input {
  border: 1px solid #d7dce5 !important;
  border-radius: 8px !important;
  background: #fff !important;
  padding-left: 30px !important;
  font-weight: 600;
  color: #2c3e50;
  cursor: pointer;
}
.fs-month-field .fs-month-input:focus {
  border-color: #17a2b8 !important;
  box-shadow: 0 0 0 2px rgba(23,162,184,0.12) !important;
  outline: none !important;
}
.fs-month-field .fs-month-input::placeholder { font-weight: 500; color: #9aa5b5; }

</style>

<?php
// Tipe laporan (Monthly / Year to Date) - filter tambahan buat menyatukan
// financial_statement.php & financial_statement_ytd.php jadi 1
// tampilan (2 menu lama diarahkan kesini, lihat module/header.php). Kedua
// set file tab (fs_ytd/*.php & fs_monthly/*.php) SAMA SEKALI TIDAK diubah -
// halaman ini cuma milih file mana yang di-include per tab, dan JS
// Export Excel/PDF mana yang dirender, berdasarkan pilihan ini. Default
// 'monthly' (perilaku sebelum filter ini ada, kalau field belum pernah
// dikirim - mis. buka halaman pertama kali). $_GET juga dicek (bukan cuma
// $_POST) supaya link menu "Financial Statement" lama (module/header.php,
// masih dipertahankan dgn gate permission ASLI-nya, id 53 - beda dari gate
// "Financial Statement 2" yg hardcode username) bisa deep-link langsung ke
// kombinasi yang benar tanpa perlu submit form dulu.
// Default 'ytd' (tampilan awal menu terpadu = FS1 Year To Date).
$report_type = (($_POST['h_report_type'] ?? $_GET['h_report_type'] ?? '') === 'monthly') ? 'monthly' : 'ytd';

// Financial Statement 1 vs 2 - dulu 2 menu terpisah ("Financial Statement" &
// "Financial Statement 2"), sekarang disatukan lewat filter ini juga. Kedua
// entri menu di module/header.php TETAP DIPERTAHANKAN APA ADANYA (termasuk
// gate permission masing-masing yang berbeda - "Financial Statement" lama
// pakai cek id 53, "Financial Statement 2" hardcode username tertentu) -
// cuma target link-nya diarahkan kesini dengan query string h_fs_system.
// FS1 = sistem lama (financial-statement-ytd.php / trial-balance-monthly.php,
// TANPA breakdown profit center NAG/NAK, isinya sudah dipecah verbatim ke
// fs1_ytd/*.php & fs1_monthly/*.php - lihat catatan lengkap di
// fs1_ytd/trial_balance.php). FS1 Monthly baru punya Trial Balance saja
// (SFP/SPL/CF Direct/CF Indirect-nya menyusul, sementara ditampilkan
// placeholder "belum tersedia"). Default '1' (tampilan awal menu terpadu = FS1).
$fs_system = (($_POST['h_fs_system'] ?? $_GET['h_fs_system'] ?? '') === '2') ? '2' : '1';

// Tab yang lagi aktif (dipakai jauh di bawah utk milih tab mana yg
// di-render eager vs placeholder AJAX, dan di sini utk hidden field
// h_active_tab) - dihitung di ATAS, SEBELUM form/hidden field dirender,
// bukan di bawah dekat blok tabIncludes (kalau dihitung telat, hidden
// field akan render duluan dgn $activeTab masih undefined).
$tabIncludes = include 'fs_tab_includes.php';
$validTabs = array_keys($tabIncludes);
$activeTab = (isset($_POST['h_active_tab']) && in_array($_POST['h_active_tab'], $validTabs, true)) ? $_POST['h_active_tab'] : 'trial-balance';
// User 'FS - Trial Balance Only' dikunci ke tab Trial Balance (walau POST minta tab lain).
if ($fsTbOnly) { $activeTab = 'trial-balance'; }

// SAMA PERSIS dgn blok di fs_tab_fetch.php (lihat catatan lengkap di sana) -
// fs_ytd/statement_financial_position.php, statement_profit_loss.php,
// cashflow_direct.php, cashflow_indirect.php (FS2 YTD) TIDAK menghitung
// $bulan_awal/$bulan_akhir/$tahun_awal/$tahun_akhir sendiri, dan mengandalkan
// variabel itu SUDAH ada di scope PHP. Kemarin blok ini cuma ditambahkan di
// fs_tab_fetch.php (jalur AJAX lazy-load), padahal $activeTab di file INI
// SENDIRI juga bisa langsung meng-include salah satu dari 4 file itu (lihat
// include $tabMap[$fs_system][$report_type] di bawah) - itu sebabnya tab yg
// jadi $activeTab masih sempat kebobolan tampil "01 January 1970" walau tab
// lain (yg lazy-load lewat fs_tab_fetch.php) sudah benar.
$date_now = date('Y-m-d');
$bulan_awal = date('m', strtotime($date_now));
$bulan_akhir = date('m', strtotime($date_now));
$tahun_awal = date('Y', strtotime($date_now));
$tahun_akhir = date('Y', strtotime($date_now));
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $bulan_awal = date('m', strtotime($_POST['start_date']));
    $bulan_akhir = date('m', strtotime($_POST['end_date']));
    $tahun_awal = date('Y', strtotime($_POST['start_date']));
    $tahun_akhir = date('Y', strtotime($_POST['end_date']));
}
?>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0 filter-card">
    <div class="card-header text-white py-2 px-3"
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-chart-line"></i> FINANCIAL STATEMENT <?= $fs_system === '1' ? '1' : '2'; ?> - <?= $report_type === 'ytd' ? 'YEAR TO DATE' : 'MONTHLY'; ?></h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="financial_statement.php" method="post">
    <div class="row g-3">
      <!-- Financial Statement 1/2 -->
      <div class="col-md-2 mb-3">
        <label for="h_fs_system"><b>Statement Format</b></label>
        <select class="form-control selectpicker" name="h_fs_system" id="h_fs_system" data-dropup-auto="false">
          <option value="2"<?= $fs_system === '2' ? ' selected="selected"' : ''; ?>>FS2 &ndash; By Profit Center</option>
          <option value="1"<?= $fs_system === '1' ? ' selected="selected"' : ''; ?>>FS1 &ndash; Total</option>
        </select>
      </div>

      <!-- Tipe Laporan -->
      <div class="col-md-2 mb-3">
        <label for="h_report_type"><b>Report Period</b></label>
        <select class="form-control selectpicker" name="h_report_type" id="h_report_type" data-dropup-auto="false">
          <option value="monthly"<?= $report_type === 'monthly' ? ' selected="selected"' : ''; ?>>Monthly</option>
          <option value="ytd"<?= $report_type === 'ytd' ? ' selected="selected"' : ''; ?>>Year to Date</option>
        </select>
      </div>

      <!-- Supplier -->
      <div class="col-md-2 mb-3">
                <label for="h_profit_center"><b>Profit Center</b></label>
                <select class="form-control selectpicker" name="h_profit_center" id="h_profit_center" data-dropup-auto="false" data-live-search="true">
                    <option value="ALL" selected="true">All</option>
                    <?php
                    // Label tampilan dipendekkan (Garment/Knitting) - HANYA
                    // teks yang berubah, atribut value pada option TETAP
                    // kode asli (NAG/NAK dst.) supaya semua query yang sudah
                    // ada (filter WHERE profit_center = kode itu) tidak
                    // perlu diubah sama sekali.
                    $pcShortLabel = [
                        'NAG' => 'Garment',
                        'NAK' => 'Knitting',
                    ];
                    $nama_supp ='';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $nama_supp = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                    }
                    $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                    while ($row = mysqli_fetch_array($sql)) {
                        $data = $row['kode_pc'];
                        $data2 = $pcShortLabel[$data] ?? $row['tampil'];
                        if($row['kode_pc'] == $_POST['h_profit_center']){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';

                        }
                        echo '<option value="'.$data.'"'.$isSelected.'>'. $data2 .'</option>';
                    }?>
                </select>
            </div>

    <!-- Start Date (tetap field terpisah, hanya diringkas & dipercantik) -->
    <div class="col-auto mb-3">
        <label for="start_date" class="form-label"><b>From</b></label>
        <div class="fs-month-field">
          <i class="fa fa-calendar fs-month-ico"></i>
          <input type="text" class="form-control form-control-sm tanggal fs-month-input" id="start_date" name="start_date"
          value="<?php
          $start_date ='';
          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $start_date = date("Y-m-d",strtotime($_POST['start_date']));
         }
         if(!empty($_POST['start_date'])) {
             echo $_POST['start_date'];
         }
         else{
             // Default From = bulan berjalan (sama dgn To) - tampilan awal
             // fokus ke bulan berjalan saja.
             echo date("M Y");
         } ?>" placeholder="Month Year" autocomplete="off">
        </div>
   </div>

   <!-- End Date (tetap field terpisah) -->
   <div class="col-auto mb-3">
    <label for="end_date" class="form-label"><b>To</b></label>
    <div class="fs-month-field">
      <i class="fa fa-calendar fs-month-ico"></i>
      <input type="text" class="form-control form-control-sm tanggal fs-month-input" id="end_date" name="end_date"
      value="<?php
      $end_date ='';
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
         $end_date = date("Y-m-d",strtotime($_POST['end_date']));
     }
     if(!empty($_POST['end_date'])) {
         echo $_POST['end_date'];
     }
     else{
         echo date("M Y");
     } ?>"  placeholder="Month Year" autocomplete="off">
    </div>
</div>

<!-- Tombol -->
<div class="col-md-2 d-flex align-items-end mb-3 fs-filter-buttons">
  <button type="submit" class="btn fs-btn-pill fs-btn-teal">
    <i class="fa fa-search"></i> Search
</button>
<?php /* Tombol Reset di-hide sesuai permintaan (kode disimpan kalau perlu dikembalikan).
<button type="button" id="reset" class="btn fs-btn-pill fs-btn-coral">
    <i class="fa fa-undo"></i> Reset
</button>
*/ ?>
<?php if ($canCopySaldo): ?>
<button type="button" id="btn-copy-saldo-all" class="btn fs-btn-pill fs-btn-green">
    <i class="fa fa-clipboard"></i> Copy Saldo
</button>
<?php endif; ?>

<input type="hidden" name="h_active_tab" id="h_active_tab" value="<?= htmlspecialchars($activeTab); ?>">
</div>

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
  <div class="card-body p-4">

   <div class="tab">
  <button class="tablinks" onclick="openTab(event, 'trial-balance')" id="defaultOpen" data-tab="trial-balance">Trial Balance</button>
  <?php if (!$fsTbOnly): // sembunyikan tab non-Trial-Balance utk role 'FS - Trial Balance Only' ?>
  <button class="tablinks" onclick="openTab(event, 'sfp')" data-tab="sfp">SFP</button>
  <button class="tablinks" onclick="openTab(event, 'spl')" data-tab="spl">SPL</button>
  <button class="tablinks" onclick="openTab(event, 'cf-direct')" data-tab="cf-direct">CF Direct</button>
  <button class="tablinks" onclick="openTab(event, 'cf-indirect')" data-tab="cf-indirect">CF Indirect</button>
  <?php endif; ?>
</div>

<?php
// $tabIncludes & $activeTab sudah dihitung di atas (dekat $fs_system) -
// lihat catatan lengkap di sana. Cuma TAB YANG SEDANG AKTIF yang di-render
// server-side di sini (berat - bisa banyak query per tab); 4 tab lain
// dirender KOSONG (placeholder spinner) dan baru diambil via AJAX
// (fs_tab_fetch.php) SAAT diklik pertama kali oleh openTab() - lihat script
// di bawah. Ini yang bikin Search jadi jauh lebih cepat (dulu SELALU
// menghitung ke-5 tab tiap Search, padahal user cuma lihat 1 dalam satu
// waktu) & menghindari halaman kosong-putih lama sebelum semua bagian
// nongol.
?>
<?php foreach ($tabIncludes as $tabKey => $tabMap): ?>
<?php if ($fsTbOnly && $tabKey !== 'trial-balance') continue; // role 'FS - Trial Balance Only' ?>
<div id="<?= htmlspecialchars($tabKey); ?>" class="tabcontent">
  <?php if ($tabKey === $activeTab): ?>
    <?php include $tabMap[$fs_system][$report_type]; ?>
  <?php else: ?>
    <div class="text-center text-muted py-5" data-fs-tab-placeholder>
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <div class="mt-2">Memuat data...</div>
    </div>
  <?php endif; ?>
</div>
<?php endforeach; ?>

  </div>
</div>

<!-- CSS -->
<style>
  .table-gradient th {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
div.dataTables_wrapper .dataTables_paginate {
    float: right;
    margin-top: 10px;
}
div.dataTables_wrapper .dataTables_info {
    float: left;
    margin-top: 10px;
}


</style>

<!-- Modal Detail -->
<div class="modal fade" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-6 mb-2"></div>
          <div id="txt_no_po" class="col-md-6 mb-2"></div>
          <div id="txt_supp" class="col-md-6 mb-2"></div>
          <div id="txt_top" class="col-md-6 mb-2"></div>
          <div id="txt_curr" class="col-md-6 mb-2"></div>
          <div id="txt_confirm" class="col-md-6 mb-2"></div>
          <div id="txt_confirm2" class="col-md-6 mb-2"></div>
          <div id="txt_tgl_po" class="col-md-6 mb-2"></div>
          <div id="details" class="col-12 mt-2"></div>
      </div>
  </div>
</div>
</div>
</div>



<!-- Bootstrap core JavaScript -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>  
<script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js"></script> -->

<script language="JavaScript" src="../css/4.1.1/xlsx.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/html2pdf.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/exceljs.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/FileSaver.min.js"></script>


<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>

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
    $(document).ready(function() {
        $('#mytable').DataTable({
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: false 
        });

        $("[data-toggle=tooltip]").tooltip();
    });

    // Freeze 6 kolom pertama (No COA s.d. Category 4) - dipakai bareng oleh
    // #table_tbmonthly/#table_tbytd/#table_tbfs1monthly (FS1/FS2 x
    // Monthly/YTD, kecuali FS1 YTD yang tabelnya #datatable sederhana tanpa
    // freeze, lihat cabang lain di bawah). scrollX/fixedHeader DataTables
    // SENGAJA tidak dipakai - meng-clone header ke elemen terpisah yang
    // disinkron pakai transform, jadi CSS position:sticky tidak berlaku di
    // situ. Scroll horizontal diserahkan ke div .table-responsive (scroll
    // native), offset "left" tiap kolom freeze dihitung ULANG dari lebar
    // ASLI hasil render browser (bukan px tebakan) tiap kali tabel di-draw
    // ulang (ganti halaman/search).
    function initFreezeTrialBalance(tableId) {
      function updateFrozenOffsets() {
        var table = document.getElementById(tableId);
        if (!table) return;
        var headerRow = table.querySelector('thead tr:first-child');
        var bodyRows = table.querySelectorAll('tbody tr');
        var refRow = bodyRows.length ? bodyRows[0] : headerRow;
        if (!refRow || !refRow.children.length) return;

        var offset = 0;
        for (var i = 0; i < 6 && i < refRow.children.length; i++) {
          var width = refRow.children[i].getBoundingClientRect().width;
          if (headerRow && headerRow.children[i]) {
            headerRow.children[i].style.left = offset + 'px';
          }
          bodyRows.forEach(function (row) {
            if (row.children[i]) {
              row.children[i].style.left = offset + 'px';
            }
          });
          offset += width;
        }
      }

      $('#' + tableId).DataTable({
        // "dom" custom di bawah ini yang bikin Search & Pagination dirender
        // DI LUAR div .table-responsive (cuma tabelnya - token 't' - yang
        // dibungkus .table-responsive) - kalau tidak, Search/Pagination ikut
        // ke dalam area yang sama dengan tabel dan ikut ke-scroll waktu
        // geser ke samping.
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'table-responsive't>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        scrollCollapse: true,
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,
        ordering: false,
        drawCallback: function () {
          updateFrozenOffsets();
        }
      });

      $(window).on('resize', updateFrozenOffsets);
    }

    // Dispatcher DataTables utk tab Trial Balance - dipisah dari blok
    // $(document).ready() di bawah supaya BISA dipanggil ULANG dari
    // loadTabContent() (openTab() di script paling bawah halaman ini)
    // SETIAP KALI tab Trial Balance selesai di-lazy-load via AJAX
    // (fs_tab_fetch.php). Sebelum ada dispatcher ini, initFreezeTrialBalance()/
    // $('#datatable').dataTable() CUMA terpanggil SEKALI di $(document).ready()
    // awal - kalau Trial Balance BUKAN tab yang aktif saat page pertama kali
    // dimuat (mis. user Search dari tab SPL, baru klik ke Trial Balance),
    // tabelnya lazy-load via AJAX tapi DataTables tidak pernah diinisialisasi -
    // hasilnya tabel Trial Balance panjang tanpa paging/search sama sekali.
    function initTrialBalanceTable() {
      var fsSystem = $('#h_fs_system').val() === '1' ? '1' : '2';
      var reportType = $('#h_report_type').val() === 'ytd' ? 'ytd' : 'monthly';
      if (fsSystem === '2' && reportType === 'monthly') {
        initFreezeTrialBalance('table_tbmonthly');
      } else if (fsSystem === '2' && reportType === 'ytd') {
        initFreezeTrialBalance('table_tbytd');
      } else if (fsSystem === '1' && reportType === 'monthly') {
        initFreezeTrialBalance('table_tbfs1monthly');
      } else {
        // FS1 YTD: tabel #datatable, warisan financial-statement-ytd.php
        // (belum dimodernisasi/freeze - persis perilaku aslinya).
        $('#datatable').dataTable();
      }
    }

    $(document).ready(function() {
      initTrialBalanceTable();
    });

</script>

<?php if ($fs_system === '2'): ?>
<?php if ($report_type === 'monthly'): ?>
<script>
  // EXPORT EXCEL (SFP Monthly) - judul dibangun ulang di sini dalam format
  // klasik ID (kiri) / EN (kanan), persis konvensi tab SFP YTD
  // (fs_ytd/statement_financial_position.php) - blok judul di TAMPILAN
  // LAYAR SFP Monthly sendiri sudah dipindah jadi 1 blok terpusat di atas
  // tabel (lihat .sfp-title-block), jadi baris judul kiri/kanan di bawah
  // ini KHUSUS dibuat ulang untuk file Excel, dibaca dari teks yang sama
  // (.sfp-title-company/-report/-report-en/-period/-desc/-desc-en) supaya
  // datanya tetap 1 sumber, tidak di-hardcode dobel.
$(document).on('click', '#btnExcel', function () {
  var headerTable = document.getElementById('sfp-header-table');
  var bodyTable = document.getElementById('sfp-monthly-table');
  if (!headerTable || !bodyTable) return;

  // Jumlah kolom nilai (di luar kolom Description) dihitung dari <colgroup>
  // tabel header - bukan angka tebakan, otomatis ikut kalau jumlah
  // bulan/kolom YTD berubah.
  var valueColCount = headerTable.querySelectorAll('colgroup col').length - 1;

  function textOf(selector) {
    var el = document.querySelector(selector);
    return el ? el.textContent.trim() : '';
  }

  var companyName = textOf('#sfp .sfp-title-company');
  var reportId = textOf('#sfp .sfp-title-report');
  var reportEn = textOf('#sfp .sfp-title-report-en');
  var period = textOf('#sfp .sfp-title-period');
  var descId = textOf('#sfp .sfp-title-desc');
  var descEn = textOf('#sfp .sfp-title-desc-en');

  // Filler-nya SENGAJA berupa <th></th> satu-satu sejumlah valueColCount,
  // BUKAN 1 sel dengan colspan=valueColCount - HTML-to-XLS importer Excel
  // punya riwayat kurang reliable menangani colspan lebar (kadang bikin
  // kolom sesudahnya "kacau"/hilang di beberapa baris berikutnya). Konvensi
  // yang sama juga dipakai template SFP YTD (fs_ytd/statement_financial_position.php).
  var blankFiller = new Array(valueColCount + 1).join('<th></th>');
  var titleRows =
    '<tr><th class="judul-left">' + companyName + '</th>' + blankFiller + '<th class="judul-right">' + companyName + '</th></tr>' +
    '<tr><th class="judul-left">' + reportId + '</th>' + blankFiller + '<th class="judul-right">' + reportEn + '</th></tr>' +
    '<tr><th class="judul-left">' + period + '</th>' + blankFiller + '<th class="judul-right">' + period + '</th></tr>' +
    '<tr><th class="desc-left">' + descId + '</th>' + blankFiller + '<th class="desc-right">' + descEn + '</th></tr>';

  // Tampilan layar SFP Monthly sendiri cuma 1 kolom deskripsi (kiri) tanpa
  // terjemahan Inggris per baris (sudah disederhanakan sebelumnya) - tapi
  // untuk file Excel, user minta format klasik seperti tab YTD: tiap baris
  // (section/subsection/item/total/grand-total) punya kolom deskripsi
  // Inggris di ujung kanan juga. Teks Inggrisnya dititip di atribut
  // data-en pada tiap <tr> (lihat PHP) - tidak dirender ke layar sama
  // sekali, cuma dibaca di sini waktu export.
  function cloneBodyRowWithEn(tr) {
    var clone = tr.cloneNode(true);
    var firstCell = clone.cells[0];
    var isItemRow = firstCell && firstCell.tagName === 'TD';
    var enClass = 'item-italic';
    if (clone.classList.contains('grand-total')) {
      enClass = 'grand-italic';
    } else if (clone.classList.contains('total-line')) {
      enClass = 'total-italic';
    } else if (clone.querySelector('.section-left')) {
      enClass = 'section-right';
    } else if (clone.querySelector('.subsection-left')) {
      enClass = 'subsection-right';
    }
    var enCell = document.createElement(isItemRow ? 'td' : 'th');
    enCell.className = enClass;
    enCell.textContent = clone.getAttribute('data-en') || '';
    clone.appendChild(enCell);
    return clone.outerHTML;
  }

  function cloneHeaderRow(tr) {
    var clone = tr.cloneNode(true);
    // Teks "Description" di pojok kiri-atas cuma pengisi visual di layar
    // (biar sel itu tidak kosong) - sisi kanan tabel (kolom EN) TIDAK
    // pakai label apa pun, jadi di Excel kolom kiri ini juga dikosongkan
    // biar simetris, bukan cuma di layar doang.
    var descCell = clone.querySelector('.periode-desc');
    if (descCell) {
      descCell.textContent = '';
    }
    clone.appendChild(document.createElement('th'));
    return clone.outerHTML;
  }

  var headerRowsHtml = Array.prototype.map.call(headerTable.rows, cloneHeaderRow).join('');
  var bodyRowsHtml = Array.prototype.map.call(bodyTable.rows, cloneBodyRowWithEn).join('');

  // Excel (Open XML dari HTML) TIDAK reliable membaca text-align dari class
  // + <style> block seperti browser - kadang malah abai dan pakai
  // default-nya sendiri (rata kanan untuk sel yang "terdeteksi" numerik,
  // termasuk sel teks yang kebetulan ada di kolom sebelah kolom angka).
  // Satu-satunya cara yang konsisten dipatuhi Excel: style INLINE langsung
  // di tiap sel. Makanya di sini di-scan ulang - class-nya tetap
  // dipertahankan (masih dipakai buat border/bold/warna via <style> block,
  // yang Excel LEBIH bisa diandalkan untuk itu), cuma text-align-nya
  // dipaksa jadi inline juga sebagai jaring pengaman.
  var alignMap = {
    'judul-left': 'left', 'desc-left': 'left', 'section-left': 'left', 'subsection-left': 'left',
    'item-left': 'left', 'total-left': 'left', 'grand-left': 'left', 'periode-desc': 'left',
    'judul-right': 'right', 'desc-right': 'right', 'item-right': 'right', 'total-right': 'right',
    'grand-right': 'right', 'item-italic': 'right', 'section-right': 'right', 'subsection-right': 'right',
    'total-italic': 'right', 'grand-italic': 'right',
    'periode': 'center', 'judul-periode': 'center'
  };
  // Kiri & kanan (ID & EN) sama-sama italic - dipaksa inline juga sama
  // seperti text-align di atas, alasannya sama (Excel kurang reliable
  // baca font-style dari class).
  var italicClasses = [
    'judul-left', 'judul-right', 'desc-left', 'desc-right',
    'section-left', 'subsection-left', 'item-left', 'total-left', 'grand-left',
    'item-italic', 'section-right', 'subsection-right', 'total-italic', 'grand-italic'
  ];
  var wrapper = document.createElement('div');
  wrapper.innerHTML = '<table>' + titleRows + headerRowsHtml + bodyRowsHtml + '</table>';
  var tableEl = wrapper.firstElementChild;
  Array.prototype.forEach.call(tableEl.querySelectorAll('th, td'), function (cell) {
    for (var cls in alignMap) {
      if (cell.classList.contains(cls)) {
        cell.style.textAlign = alignMap[cls];
        break;
      }
    }
    for (var i = 0; i < italicClasses.length; i++) {
      if (cell.classList.contains(italicClasses[i])) {
        cell.style.fontStyle = 'italic';
        break;
      }
    }
  });

  var table = tableEl.outerHTML;

  // Style Excel disesuaikan dengan class yang SEKARANG dipakai tabel SFP
  // Monthly (item-right/total-right/grand-right dkk - bukan lagi
  // item-italic/section-right/grand-italic seperti versi lama yang punya
  // kolom deskripsi Inggris terpisah di ujung kanan tabel).
  var styles = `
  body {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    color: #000 !important;
  }
  table {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    border-collapse: collapse;
  }
  td, th {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    vertical-align: middle;
    padding: 3px 6px;
  }

  /* === Judul (format klasik ID kiri / EN kanan) - kiri & kanan sama-sama
     italic supaya konsisten === */
  .judul-left, .judul-right { font-weight: bold; font-size: 13pt !important; font-style: italic; }
  .judul-left { text-align: left !important; }
  .judul-right { text-align: right !important; }
  .desc-left, .desc-right { color: #555; font-style: italic; }
  .desc-left { text-align: left !important; }
  .desc-right { text-align: right !important; }

  /* === Header periode === */
  .judul-periode, .periode { text-align: center; font-weight: bold; }
  .periode-desc { text-align: left !important; font-weight: bold; }

  /* === Alignment isi (kiri & kanan sama-sama italic) === */
  .section-left, .subsection-left, .item-left, .total-left, .grand-left {
    text-align: left !important;
    font-style: italic;
  }
  .item-right, .total-right, .grand-right {
    text-align: right !important;
  }
  .section-left, .subsection-left, .total-left, .total-right, .grand-left, .grand-right {
    font-weight: bold;
  }

  /* === Kolom deskripsi Inggris di ujung kanan (khusus file Excel, lihat
     cloneBodyRowWithEn() - tidak ada di tampilan layar) === */
  .item-italic, .section-right, .subsection-right, .total-italic, .grand-italic {
    text-align: right !important;
    font-style: italic;
    color: #555;
  }
  .section-right, .total-italic, .grand-italic {
    font-weight: bold;
    color: #000;
  }

  /* === Kolom YTD - highlight beda, sama seperti di layar === */
  .sfpm-ytd { background: #fdf6e6 !important; }

  /* === Border garis total === */
  .total-line td, .total-line th {
    border-top: 1px solid #000 !important;
  }
  .grand-total td, .grand-total th {
    border-top: 3px double #000 !important;
    background: #f2f2f2 !important;
  }

  table, th, td {
    border: none;
    mso-border-alt: none;
  }
`;

  var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">' +
    '<head><meta charset="UTF-8"><style>' + styles + '</style>' +
    '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
    '<x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines>false</x:DisplayGridlines></x:WorksheetOptions>' +
    '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
    '</head><body>' + table + '</body></html>';

  var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
  var link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'Statement_Financial_Position_Monthly.xls';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
});

  // PRINT PDF
  $(document).on('click', '#btnPDF', function () {
    let element = document.querySelector('.laporan-table');
    let opt = {
      margin:       [0, 0, 0, 0], // ⬅️ Hapus semua margin PDF
      filename:     'Statement_Financial_Position_Monthly.pdf',
      image:        { type: 'jpeg', quality: 1 },
      html2canvas:  {
        scale: 2,
        scrollY: 0, // ⬅️ Pastikan tidak ikut offset scroll
      },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    // Hapus margin bawaan browser sebelum render
    document.body.style.margin = '0';
    document.body.style.padding = '0';
    element.style.marginTop = '5';
    element.style.paddingTop = '0';

    html2pdf().set(opt).from(element).save();
  });
</script>
<?php else: ?>
<script>
  // EXPORT EXCEL (SFP Year to Date) - disalin verbatim dari fs_ytd/statement_financial_position.php
  // punya sendiri (tombol Export di tab ini di-render oleh fs_ytd/statement_financial_position.php,
  // ID-nya sama seperti versi standalone: btnExcel/btnPDF).
$(document).on('click', '#btnExcel', function () {
  const table = document.querySelector('.laporan-container').outerHTML;
  const styles = `
  body {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    color: #000 !important;
  }

  .laporan-table {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    margin: auto;
    width: 95%;
    border-collapse: collapse;
    color: #000 !important;
  }

  td, th {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    vertical-align: middle;
    padding: 3px 6px;
  }

  /* === Alignment === */
  .judul-left, .subjudul-left, .tanggal-left,
  .desc-left, .section-left, .subsection-left,
  .item-left, .total-left, .grand-left {
    text-align: left !important;
  }

  .judul-right, .subjudul-right, .tanggal-right,
  .desc-right, .section-right, .subsection-right,
  .item-right, .total-right, .grand-right,
  .item-italic, .total-italic, .grand-italic {
    text-align: right !important;
  }

  /* === Border garis total === */
  .total-line td {
    border-bottom: 2px solid #000 !important;
  }

  .grand-total td {
    border-bottom: 3px double #000 !important;
    font-weight: bold;
    background: #f5f5f5;
  }

  /* === Bersihkan border Excel bawaan === */
  table, th, td {
    border: none !important;
    outline: none !important;
    mso-border-alt: none !important;
  }

  tr, td {
    mso-style-parent: "";
    mso-border-alt: none;
  }
`;

  const html = `
  <html xmlns:x="urn:schemas-microsoft-com:office:excel">
  <head>
    <meta charset="UTF-8">
    <style>${styles}</style>
    <!--[if gte mso 9]>
    <xml>
      <x:ExcelWorkbook>
        <x:ExcelWorksheets>
          <x:ExcelWorksheet>
            <x:Name>Laporan Keuangan</x:Name>
            <x:WorksheetOptions>
              <x:DisplayGridlines>false</x:DisplayGridlines>
            </x:WorksheetOptions>
          </x:ExcelWorksheet>
        </x:ExcelWorksheets>
      </x:ExcelWorkbook>
    </xml>
    <![endif]-->
  </head>
  <body>${table}</body>
  </html>`;

  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "Statement_Financial_Position_YTD.xls";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
});

  $(document).on('click', '#btnPDF', function () {
    let element = document.querySelector('.laporan-table');
    let opt = {
      margin:       [0, 0, 0, 0],
      filename:     'Statement_Financial_Position_YTD.pdf',
      image:        { type: 'jpeg', quality: 1 },
      html2canvas:  {
        scale: 2,
        scrollY: 0,
      },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    document.body.style.margin = '0';
    document.body.style.padding = '0';
    element.style.marginTop = '5';
    element.style.paddingTop = '0';

    html2pdf().set(opt).from(element).save();
  });
</script>
<?php endif; ?>
<?php endif; ?>


<script type="text/javascript">
    $(document).ready(function () {
        $('.tanggal').datepicker({
            format: "M yyyy",
            minViewMode: "months",
            autoclose:true
        });
    });
</script>

  <script type="text/javascript">
// Tombol "Copy Saldo" GABUNGAN (dulu 2 tombol terpisah, satu di tab Trial
// Balance FS1 & satu di tab Trial Balance FS2, masing2 manggil endpoint
// beda: copy_saldo_tb.php (FS1) / fs_ytd/copy_saldo_tb_new.php (FS2) - lihat
// catatan lengkap di fs1_ytd/trial_balance.php & fs_ytd/trial_balance_ytd.php).
// Sekarang 1 klik menjalankan KEDUANYA.
//
// PENTING: tabel staging tiap sistem (tbl_saldo_tb_temp utk FS1,
// fs_saldo_tb_temp utk FS2) cuma keisi ulang sbg efek samping SAAT halaman
// Trial Balance YTD sistem itu di-render (lihat blok PHP "insert into
// ..._temp" di kedua file itu) - dan FS1/FS2 SALING EKSKLUSIF, cuma 1 yg
// ke-render per request sesuai dropdown "Financial Statement". Makanya
// SEBELUM manggil endpoint copy, tiap sistem di-refresh dulu lewat
// background POST ke halaman ini sendiri (h_fs_system=1 lalu =2), supaya
// staging KEDUA sistem selalu fresh apapun tab yg sedang aktif di layar -
// bukan cuma yg sedang dibuka user.
$(document).on("click", "#btn-copy-saldo-all", function () {
  var $btn = $(this);
  var endDateVal = $('#end_date').val();
  var startDateVal = $('#start_date').val();
  var copyUser = '<?php echo $user ?>';
  var toSaldo = endDateVal ? endDateVal.replace(' ', '_') : '';

  if (!toSaldo) {
    Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Please fill in the "To" date first.' });
    return;
  }

  // ===== Konfirmasi SEBELUM (SweetAlert2, ganti confirm() native) =====
  Swal.fire({
    title: 'Copy Saldo?',
    html: 'Period <b>' + endDateVal + '</b> will be closed for <b>Financial Statement 1 &amp; 2</b> at once.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Copy Saldo',
    cancelButtonText: 'Cancel'
  }).then(function (result) {
    if (!result.isConfirmed) { return; }

    var originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

    // Loader selama proses berjalan (cegah double-click & tutup halaman).
    Swal.fire({
      title: 'Processing Copy Saldo...',
      html: 'Please wait, do not close this page.',
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: function () { Swal.showLoading(); }
    });

    function refreshStaging(fsSystem) {
      return $.ajax({
        type: 'POST',
        url: 'financial_statement.php',
        data: {
          h_fs_system: fsSystem,
          h_report_type: 'ytd',
          start_date: startDateVal,
          end_date: endDateVal
        }
      });
    }

    function copySaldo(url) {
      return $.ajax({
        type: 'POST',
        url: url,
        data: {
          copy_user: copyUser,
          to_saldo: toSaldo
        }
      });
    }

    var chainFs1 = refreshStaging('1').then(function () {
      return copySaldo('copy_saldo_tb.php');
    });
    var chainFs2 = refreshStaging('2').then(function () {
      return copySaldo('fs_ytd/copy_saldo_tb_new.php');
    });

    // ===== Hasil SESUDAH (SweetAlert2, ganti alert() native) =====
    $.when(chainFs1, chainFs2).done(function () {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Copy Saldo completed for Financial Statement 1 & 2.'
      });
    }).fail(function (xhr) {
      console.error('AJAX Error:', xhr ? xhr.responseText : '');
      Swal.fire({
        icon: 'error',
        title: 'Failed!',
        text: 'Copy Saldo failed. Check the console (F12) for details.'
      });
    }).always(function () {
      $btn.prop('disabled', false).html(originalHtml);
    });
  });
});
</script>


<script>
    $(function() {
        $('.selectpicker').selectpicker();

        // FS1 TIDAK PUNYA breakdown profit center sama sekali (beda mesin
        // dari FS2 - tabel saldo FS1 tidak ada kolom profit_center, lihat
        // catatan panjang di file-file fs1_*/*.php) - jadi kalau Statement
        // Format = FS1, Profit Center dikunci ke "All" & dropdown-nya
        // dinonaktifkan (bukan cuma dibiarkan tetap bisa dipilih tapi
        // diam-diam tidak berpengaruh apa-apa, yang bikin bingung).
        function syncProfitCenterAvailability() {
            var isFs1 = $('#h_fs_system').val() === '1';
            var $pc = $('#h_profit_center');
            if (isFs1) {
                $pc.val('ALL');
            }
            $pc.prop('disabled', isFs1).selectpicker('refresh');
        }

        syncProfitCenterAvailability();
        $('#h_fs_system').on('change', syncProfitCenterAvailability);
    });
</script>

<script>
// Tab aktif yg di-render server-side (lihat $activeTab di PHP) - HANYA ini
// yang sudah punya konten waktu page load; 4 tab lain masih placeholder
// spinner & diambil via fs_tab_fetch.php SAAT PERTAMA KALI diklik saja
// (loadedTabs melacak itu, di-reset otomatis tiap page load krn ini
// variabel JS biasa, bukan disimpan lintas request).
var fsActiveTab = '<?= htmlspecialchars($activeTab); ?>';
var loadedTabs = {};
loadedTabs[fsActiveTab] = true;

function fsTabOffsetSync(tabName) {
  if (tabName === 'trial-balance' && typeof updateFrozenOffsets === 'function') {
    updateFrozenOffsets();
  }
  if (tabName === 'sfp' && typeof updateSfpHeaderOffset === 'function') {
    updateSfpHeaderOffset();
  }
  if (tabName === 'spl' && typeof updateSplHeaderOffset === 'function') {
    updateSplHeaderOffset();
  }
  if (tabName === 'cf-indirect' && typeof updateCfIndirectHeaderOffset === 'function') {
    updateCfIndirectHeaderOffset();
  }
  if (tabName === 'cf-direct' && typeof updateCfDirectHeaderOffset === 'function') {
    updateCfDirectHeaderOffset();
  }
}

function loadTabContent(tabName) {
  var $target = $('#' + tabName);
  loadedTabs[tabName] = true;
  $.ajax({
    type: 'POST',
    url: 'fs_tab_fetch.php',
    data: {
      tab: tabName,
      h_fs_system: $('#h_fs_system').val(),
      h_report_type: $('#h_report_type').val(),
      h_profit_center: $('#h_profit_center').val(),
      start_date: $('#start_date').val(),
      end_date: $('#end_date').val()
    },
    success: function (response) {
      // .html() (bukan .innerHTML langsung) supaya <script> di dalam
      // fragment yang baru masuk (mis. IIFE sync-scroll tiap tab) ikut
      // dieksekusi - browser TIDAK otomatis menjalankan <script> yang
      // disisipkan lewat innerHTML biasa, tapi jQuery .html() menanganinya.
      $target.html(response);
      if (tabName === 'trial-balance' && typeof initTrialBalanceTable === 'function') {
        // Trial Balance BEDA dari tab lain: DataTables (paging/search/
        // freeze kolom) TIDAK didefinisikan di dalam fragment tab itu
        // sendiri, tapi lewat fungsi di halaman induk - kalau tab ini
        // baru pertama kali di-lazy-load (bukan tab aktif default saat
        // page load), init-nya perlu dipanggil manual di sini juga.
        initTrialBalanceTable();
      }
      $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
      fsTabOffsetSync(tabName);
    },
    error: function () {
      loadedTabs[tabName] = false;
      $target.html('<div class="alert alert-danger m-3">Gagal memuat data laporan. <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="loadTabContent(\'' + tabName + '\')"><i class="fa fa-redo"></i> Coba lagi</button></div>');
    }
  });
}

function openTab(evt, tabName) {
  // Sembunyikan semua tab
  var tabcontent = document.getElementsByClassName("tabcontent");
  for (var i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Hapus class active
  var tablinks = document.getElementsByClassName("tablinks");
  for (var i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Tampilkan tab yang diklik
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";

  // Simpan tab yg sedang aktif ke hidden field - ikut ke-submit bareng
  // Search, jadi reload berikutnya langsung merender tab yang SEDANG
  // dibuka user (bukan selalu balik ke Trial Balance) - lihat $activeTab
  // di PHP atas.
  var hiddenActiveTab = document.getElementById('h_active_tab');
  if (hiddenActiveTab) { hiddenActiveTab.value = tabName; }

  if (!loadedTabs[tabName]) {
    loadTabContent(tabName);
    return;
  }

  // 🔧 Re-adjust DataTables saat tab aktif
  $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();

  // Tab yang baru ditampilkan lebar kolomnya baru bisa diukur browser SETELAH
  // display:block (elemen yang display:none selalu 0 lebar) - makanya offset
  // freeze-nya baru dihitung ulang di sini, bukan cuma sekali saat halaman
  // dimuat.
  fsTabOffsetSync(tabName);
}

(document.querySelector('.tablinks[data-tab="' + fsActiveTab + '"]') || document.getElementById('defaultOpen')).click();
</script>



<!--
<script type="text/javascript">
    $('table tbody tr').on('click', 'td:eq(0)', function(){
    $('#mymodal').modal('show');
    var no_bpb = $(this).closest('tr').find('td:eq(0)').attr('value');
    var tgl_bpb = $(this).closest('tr').find('td:eq(2)').text();
    var no_po = $(this).closest('tr').find('td:eq(1)').attr('value');
    var supp = $(this).closest('tr').find('td:eq(3)').attr('value');
    var top = $(this).closest('tr').find('td:eq(10)').attr('value');
    var curr = $(this).closest('tr').find('td:eq(8)').attr('value');
    var confirm = $(this).closest('tr').find('td:eq(5)').attr('value');
    var confirm2 = $(this).closest('tr').find('td:eq(6)').attr('value');
    var tgl_po = $(this).closest('tr').find('td:eq(11)').text();        

    $.ajax({
    type : 'post',
    url : 'ajaxbpb.php',
    data : {'no_bpb': no_bpb},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_bpb);
    $('#txt_tglbpb').html('Tgl BPB : ' + tgl_bpb + '');
    $('#txt_no_po').html('No PO : ' + no_po + '');
    $('#txt_supp').html('Supplier : ' + supp + '');
    $('#txt_top').html('TOP : ' + top + ' Days');
    $('#txt_curr').html('Currency : ' + curr + '');        
    $('#txt_confirm').html('Confirm By (GMF) : ' + confirm + '');
    $('#txt_confirm2').html('Confirm By (PCH) : ' + confirm2 + '');
    $('#txt_tgl_po').html('Tgl PO : ' + tgl_po + '');                         
});

</script> -->

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "financial_statement.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "financial_statement.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

<?php if ($fs_system === '2'): ?>
<?php if ($report_type === 'monthly'): ?>
<script type="text/javascript">
$(document).on('click', '#btnPDF-spl', function () {
  const element = document.getElementById('laporan-spl-ytd');

  // 🔹 CSS sementara untuk PDF (perkecil font dan jarak baris)
  const style = document.createElement('style');
  style.innerHTML = `
    #laporan-spl-ytd, 
    #laporan-spl-ytd table, 
    #laporan-spl-ytd th, 
    #laporan-spl-ytd td {
      font-size: 11pt !important;
      line-height: 1.2 !important;
    }
    #laporan-spl-ytd {
      transform: scale(0.9);
      transform-origin: top left;
      width: 110%;
    }
  `;
  document.head.appendChild(style);

  // 🔹 Tampilkan Swal loading
  Swal.fire({
    title: 'Sedang membuat PDF...',
    text: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // 🔹 Opsi PDF — tingkatkan kualitas hasil render
  const opt = {
    margin: [5, 5, 5, 5],
    filename: 'Statement_Profit_or_Loss_Monthly.pdf',
    image: { type: 'jpeg', quality: 1 },
    html2canvas: {
      scale: 4,
      useCORS: true,
      letterRendering: true,
      scrollY: 0
    },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  };

  // 🔹 Proses PDF
  html2pdf()
    .set(opt)
    .from(element)
    .save()
    .then(() => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'PDF berhasil dibuat dan diunduh.',
        timer: 2000,
        showConfirmButton: false
      });
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Terjadi kesalahan saat membuat PDF.',
      });
    })
    .finally(() => {
      document.head.removeChild(style);
    });
});

</script>
<script>
  // EXPORT EXCEL (SPL Monthly) - pola & alasan identik dengan export SFP
  // Monthly di atas (judul klasik ID kiri/EN kanan dibangun ulang dari
  // .sfp-title-*, kolom EN per baris dari atribut data-en, alignment &
  // italic dipaksa inline karena Excel kurang reliable baca dari class) -
  // cuma ID elemen sumbernya beda (spl- bukan sfp-).
$(document).on('click', '#btnExcel-spl', function () {
  var headerTable = document.getElementById('spl-header-table');
  var bodyTable = document.getElementById('spl-monthly-table');
  if (!headerTable || !bodyTable) return;

  var valueColCount = headerTable.querySelectorAll('colgroup col').length - 1;

  function textOf(selector) {
    var el = document.querySelector(selector);
    return el ? el.textContent.trim() : '';
  }

  var companyName = textOf('#spl .sfp-title-company');
  var reportId = textOf('#spl .sfp-title-report');
  var reportEn = textOf('#spl .sfp-title-report-en');
  var period = textOf('#spl .sfp-title-period');
  var descId = textOf('#spl .sfp-title-desc');
  var descEn = textOf('#spl .sfp-title-desc-en');

  var blankFiller = new Array(valueColCount + 1).join('<th></th>');
  var titleRows =
    '<tr><th class="judul-left">' + companyName + '</th>' + blankFiller + '<th class="judul-right">' + companyName + '</th></tr>' +
    '<tr><th class="judul-left">' + reportId + '</th>' + blankFiller + '<th class="judul-right">' + reportEn + '</th></tr>' +
    '<tr><th class="judul-left">' + period + '</th>' + blankFiller + '<th class="judul-right">' + period + '</th></tr>' +
    '<tr><th class="desc-left">' + descId + '</th>' + blankFiller + '<th class="desc-right">' + descEn + '</th></tr>';

  function cloneBodyRowWithEn(tr) {
    var clone = tr.cloneNode(true);
    var firstCell = clone.cells[0];
    var isItemRow = firstCell && firstCell.tagName === 'TD';
    var enClass = 'item-italic';
    if (clone.classList.contains('grand-total')) {
      enClass = 'grand-italic';
    } else if (clone.classList.contains('total-line')) {
      enClass = 'total-italic';
    } else if (clone.querySelector('.section-left')) {
      enClass = 'section-right';
    } else if (clone.querySelector('.subsection-left')) {
      enClass = 'subsection-right';
    }
    var enCell = document.createElement(isItemRow ? 'td' : 'th');
    enCell.className = enClass;
    enCell.textContent = clone.getAttribute('data-en') || '';
    clone.appendChild(enCell);
    return clone.outerHTML;
  }

  function cloneHeaderRow(tr) {
    var clone = tr.cloneNode(true);
    // Teks "Description" di pojok kiri-atas cuma pengisi visual di layar
    // (biar sel itu tidak kosong) - sisi kanan tabel (kolom EN) TIDAK
    // pakai label apa pun, jadi di Excel kolom kiri ini juga dikosongkan
    // biar simetris, bukan cuma di layar doang.
    var descCell = clone.querySelector('.periode-desc');
    if (descCell) {
      descCell.textContent = '';
    }
    clone.appendChild(document.createElement('th'));
    return clone.outerHTML;
  }

  var headerRowsHtml = Array.prototype.map.call(headerTable.rows, cloneHeaderRow).join('');
  var bodyRowsHtml = Array.prototype.map.call(bodyTable.rows, cloneBodyRowWithEn).join('');

  var alignMap = {
    'judul-left': 'left', 'desc-left': 'left', 'section-left': 'left', 'subsection-left': 'left',
    'item-left': 'left', 'total-left': 'left', 'grand-left': 'left', 'periode-desc': 'left',
    'judul-right': 'right', 'desc-right': 'right', 'item-right': 'right', 'total-right': 'right',
    'grand-right': 'right', 'item-italic': 'right', 'section-right': 'right', 'subsection-right': 'right',
    'total-italic': 'right', 'grand-italic': 'right',
    'periode': 'center', 'judul-periode': 'center'
  };
  var italicClasses = [
    'judul-left', 'judul-right', 'desc-left', 'desc-right',
    'section-left', 'subsection-left', 'item-left', 'total-left', 'grand-left',
    'item-italic', 'section-right', 'subsection-right', 'total-italic', 'grand-italic'
  ];
  var wrapper = document.createElement('div');
  wrapper.innerHTML = '<table>' + titleRows + headerRowsHtml + bodyRowsHtml + '</table>';
  var tableEl = wrapper.firstElementChild;
  Array.prototype.forEach.call(tableEl.querySelectorAll('th, td'), function (cell) {
    for (var cls in alignMap) {
      if (cell.classList.contains(cls)) {
        cell.style.textAlign = alignMap[cls];
        break;
      }
    }
    for (var i = 0; i < italicClasses.length; i++) {
      if (cell.classList.contains(italicClasses[i])) {
        cell.style.fontStyle = 'italic';
        break;
      }
    }
  });

  var table = tableEl.outerHTML;

  var styles = `
  body {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    color: #000 !important;
  }
  table {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    border-collapse: collapse;
  }
  td, th {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    vertical-align: middle;
    padding: 3px 6px;
  }

  .judul-left, .judul-right { font-weight: bold; font-size: 13pt !important; }
  .desc-left, .desc-right { color: #555; }

  .judul-periode, .periode { text-align: center; font-weight: bold; }
  .periode-desc { text-align: left !important; font-weight: bold; }

  .section-left, .subsection-left, .item-left, .total-left, .grand-left {
    font-weight: bold;
  }

  .sfpm-ytd { background: #fdf6e6 !important; }

  .total-line td, .total-line th {
    border-top: 1px solid #000 !important;
  }
  .grand-total td, .grand-total th {
    border-top: 2px solid #000 !important;
    background: #f2f2f2 !important;
  }

  table, th, td {
    border: none;
    mso-border-alt: none;
  }
`;

  var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">' +
    '<head><meta charset="UTF-8"><style>' + styles + '</style>' +
    '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
    '<x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines>false</x:DisplayGridlines></x:WorksheetOptions>' +
    '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
    '</head><body>' + table + '</body></html>';

  var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
  var link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'Statement_Profit_or_Loss_Monthly.xls';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
});
</script>
<?php else: ?>
<script type="text/javascript">
$(document).on('click', '#btnPDF-spl', function () {
  const element = document.getElementById('laporan-spl-ytd');
  const style = document.createElement('style');
  style.innerHTML = `
    #laporan-spl-ytd,
    #laporan-spl-ytd table,
    #laporan-spl-ytd th,
    #laporan-spl-ytd td {
      font-size: 11pt !important;
      line-height: 1.2 !important;
    }
    #laporan-spl-ytd {
      transform: scale(0.9);
      transform-origin: top left;
      width: 110%;
    }
  `;
  document.head.appendChild(style);

  Swal.fire({
    title: 'Sedang membuat PDF...',
    text: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); }
  });

  const opt = {
    margin: [5, 5, 5, 5],
    filename: 'Statement_Profit_or_Loss_YTD.pdf',
    image: { type: 'jpeg', quality: 1 },
    html2canvas: { scale: 4, useCORS: true, letterRendering: true, scrollY: 0 },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  };

  html2pdf()
    .set(opt)
    .from(element)
    .save()
    .then(() => {
      Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'PDF berhasil dibuat dan diunduh.', timer: 2000, showConfirmButton: false });
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat membuat PDF.' });
    })
    .finally(() => {
      document.head.removeChild(style);
    });
});
</script>
<script>
$(document).on('click', '#btnExcel-spl', function () {
  const table = document.querySelector('.laporan-container-spl').outerHTML;
  const styles = `
    <style>
      body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #2c3e50; }
      table { border-collapse: collapse; width: 100%; }
      th, td { padding: 2px 4px; vertical-align: middle; border: none; }
      .item-left, .section-left, .total-left, .grand-left,
      .judul-left, .subjudul-left, .desc-left, .subsection-left {
        text-align: left !important; mso-justify: left;
      }
      .item-right, .section-right, .total-right, .grand-right,
      .judul-right, .subjudul-right, .item-italic, .grand-italic, .total-italic, .desc-right, .subsection-right {
        text-align: right !important; mso-justify: right;
      }
      .number { mso-number-format:"#\\,##0.00_);(#\\,##0.00)"; text-align:right; }
      .judul-left, .judul-right { font-weight: bold; font-size: 11pt; }
      .subjudul-left, .subjudul-right { font-weight: bold; font-size: 10pt; }
      .periode, .isi-periode, .persentage, .isi-persentage { border-bottom: 2px solid #000; font-weight: bold; text-align: center; }
      .total-line td, .total-line th{ border-top: 2px solid #000 !important; border-bottom: none !important; font-weight: bold; background: #ffffff !important; }
      .grand-total th { border-top: 3px double #000 !important; border-bottom: none !important; font-weight: bold; background: #f2f2f2 !important; }
      .section-left, .section-right, .subsection-left, .subsection-right { font-weight: bold; }
      .spacer { height: 10px; }
    </style>
  `;
  const html = `
    <html xmlns:x="urn:schemas-microsoft-com:office:excel">
      <head><meta charset="UTF-8">${styles}</head>
      <body>${table}</body>
    </html>
  `;
  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "Statement_Profit_or_Loss_YTD.xls";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});
</script>
<?php endif; ?>
<?php endif; ?>


<?php if ($fs_system === '2'): ?>
<?php if ($report_type === 'monthly'): ?>
<script type="text/javascript">
$(document).on('click', '#btnPDF-cfdirect', function () {
  const element = document.getElementById('laporan-cfdirect-ytd');

  // 🔹 CSS sementara untuk PDF (perkecil font dan jarak baris)
  const style = document.createElement('style');
  style.innerHTML = `
    #laporan-cfdirect-ytd, 
    #laporan-cfdirect-ytd table, 
    #laporan-cfdirect-ytd th, 
    #laporan-cfdirect-ytd td {
      font-size: 11pt !important;
      line-height: 1.2 !important;
    }
    #laporan-cfdirect-ytd {
      transform: scale(0.9);
      transform-origin: top left;
      width: 110%;
    }
  `;
  document.head.appendChild(style);

  // 🔹 Tampilkan Swal loading
  Swal.fire({
    title: 'Sedang membuat PDF...',
    text: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // 🔹 Opsi PDF — tingkatkan kualitas hasil render
  const opt = {
    margin: [5, 5, 5, 5],
    filename: 'CashFlow_Direct_Monthly.pdf',
    image: { type: 'jpeg', quality: 1 },
    html2canvas: {
      scale: 4,
      useCORS: true,
      letterRendering: true,
      scrollY: 0
    },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  };

  // 🔹 Proses PDF
  html2pdf()
    .set(opt)
    .from(element)
    .save()
    .then(() => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'PDF berhasil dibuat dan diunduh.',
        timer: 2000,
        showConfirmButton: false
      });
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Terjadi kesalahan saat membuat PDF.',
      });
    })
    .finally(() => {
      document.head.removeChild(style);
    });
});

</script>
<script>
  // EXPORT EXCEL (CF Direct Monthly) - pola & alasan identik dengan export
  // SFP/SPL/CF Indirect Monthly di atas.
$(document).on('click', '#btnExcel-cfdirect', function () {
  var headerTable = document.getElementById('cfdirect-header-table');
  var bodyTable = document.getElementById('cfdirect-monthly-table');
  if (!headerTable || !bodyTable) return;

  var valueColCount = headerTable.querySelectorAll('colgroup col').length - 1;

  function textOf(selector) {
    var el = document.querySelector(selector);
    return el ? el.textContent.trim() : '';
  }

  var companyName = textOf('#cf-direct .sfp-title-company');
  var reportId = textOf('#cf-direct .sfp-title-report');
  var reportEn = textOf('#cf-direct .sfp-title-report-en');
  var period = textOf('#cf-direct .sfp-title-period');
  var descId = textOf('#cf-direct .sfp-title-desc');
  var descEn = textOf('#cf-direct .sfp-title-desc-en');

  var blankFiller = new Array(valueColCount + 1).join('<th></th>');
  var titleRows =
    '<tr><th class="judul-left">' + companyName + '</th>' + blankFiller + '<th class="judul-right">' + companyName + '</th></tr>' +
    '<tr><th class="judul-left">' + reportId + '</th>' + blankFiller + '<th class="judul-right">' + reportEn + '</th></tr>' +
    '<tr><th class="judul-left">' + period + '</th>' + blankFiller + '<th class="judul-right">' + period + '</th></tr>' +
    '<tr><th class="desc-left">' + descId + '</th>' + blankFiller + '<th class="desc-right">' + descEn + '</th></tr>';

  function cloneBodyRowWithEn(tr) {
    var clone = tr.cloneNode(true);
    var firstCell = clone.cells[0];
    var isItemRow = firstCell && firstCell.tagName === 'TD';
    var enClass = 'item-italic';
    if (clone.classList.contains('grand-total')) {
      enClass = 'grand-italic';
    } else if (clone.classList.contains('total-line')) {
      enClass = 'total-italic';
    } else if (clone.querySelector('.section-left')) {
      enClass = 'section-right';
    } else if (clone.querySelector('.subsection-left')) {
      enClass = 'subsection-right';
    }
    var enCell = document.createElement(isItemRow ? 'td' : 'th');
    enCell.className = enClass;
    enCell.textContent = clone.getAttribute('data-en') || '';
    clone.appendChild(enCell);
    return clone.outerHTML;
  }

  function cloneHeaderRow(tr) {
    var clone = tr.cloneNode(true);
    var descCell = clone.querySelector('.periode-desc');
    if (descCell) {
      descCell.textContent = '';
    }
    clone.appendChild(document.createElement('th'));
    return clone.outerHTML;
  }

  var headerRowsHtml = Array.prototype.map.call(headerTable.rows, cloneHeaderRow).join('');
  var bodyRowsHtml = Array.prototype.map.call(bodyTable.rows, cloneBodyRowWithEn).join('');

  var alignMap = {
    'judul-left': 'left', 'desc-left': 'left', 'section-left': 'left', 'subsection-left': 'left',
    'item-left': 'left', 'total-left': 'left', 'grand-left': 'left', 'periode-desc': 'left',
    'judul-right': 'right', 'desc-right': 'right', 'item-right': 'right', 'total-right': 'right',
    'grand-right': 'right', 'item-italic': 'right', 'section-right': 'right', 'subsection-right': 'right',
    'total-italic': 'right', 'grand-italic': 'right',
    'periode': 'center', 'judul-periode': 'center'
  };
  var italicClasses = [
    'judul-left', 'judul-right', 'desc-left', 'desc-right',
    'section-left', 'subsection-left', 'item-left', 'total-left', 'grand-left',
    'item-italic', 'section-right', 'subsection-right', 'total-italic', 'grand-italic'
  ];
  var wrapper = document.createElement('div');
  wrapper.innerHTML = '<table>' + titleRows + headerRowsHtml + bodyRowsHtml + '</table>';
  var tableEl = wrapper.firstElementChild;
  Array.prototype.forEach.call(tableEl.querySelectorAll('th, td'), function (cell) {
    for (var cls in alignMap) {
      if (cell.classList.contains(cls)) {
        cell.style.textAlign = alignMap[cls];
        break;
      }
    }
    for (var i = 0; i < italicClasses.length; i++) {
      if (cell.classList.contains(italicClasses[i])) {
        cell.style.fontStyle = 'italic';
        break;
      }
    }
  });

  var table = tableEl.outerHTML;

  var styles = `
  body {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    color: #000 !important;
  }
  table {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    border-collapse: collapse;
  }
  td, th {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    vertical-align: middle;
    padding: 3px 6px;
  }

  .judul-left, .judul-right { font-weight: bold; font-size: 13pt !important; }
  .desc-left, .desc-right { color: #555; }

  .judul-periode, .periode { text-align: center; font-weight: bold; }
  .periode-desc { text-align: left !important; font-weight: bold; }

  .section-left, .subsection-left, .item-left, .total-left, .grand-left {
    font-weight: bold;
  }

  .sfpm-ytd { background: #fdf6e6 !important; }

  .total-line td, .total-line th {
    border-top: 1px solid #000 !important;
  }
  .grand-total td, .grand-total th {
    border-top: 2px solid #000 !important;
    background: #f2f2f2 !important;
  }

  table, th, td {
    border: none;
    mso-border-alt: none;
  }
`;

  var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">' +
    '<head><meta charset="UTF-8"><style>' + styles + '</style>' +
    '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
    '<x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines>false</x:DisplayGridlines></x:WorksheetOptions>' +
    '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
    '</head><body>' + table + '</body></html>';

  var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
  var link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'CashFlow_Direct_Monthly.xls';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
});
</script>
<?php else: ?>
<script type="text/javascript">
$(document).on('click', '#btnPDF-cfdirect', function () {
  const element = document.getElementById('laporan-cfdirect-ytd');
  const style = document.createElement('style');
  style.innerHTML = `
    #laporan-cfdirect-ytd,
    #laporan-cfdirect-ytd table,
    #laporan-cfdirect-ytd th,
    #laporan-cfdirect-ytd td {
      font-size: 11pt !important;
      line-height: 1.2 !important;
    }
    #laporan-cfdirect-ytd {
      transform: scale(0.9);
      transform-origin: top left;
      width: 110%;
    }
  `;
  document.head.appendChild(style);

  Swal.fire({
    title: 'Sedang membuat PDF...',
    text: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); }
  });

  const opt = {
    margin: [5, 5, 5, 5],
    filename: 'CashFlow_Direct_YTD.pdf',
    image: { type: 'jpeg', quality: 1 },
    html2canvas: { scale: 4, useCORS: true, letterRendering: true, scrollY: 0 },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  };

  html2pdf()
    .set(opt)
    .from(element)
    .save()
    .then(() => {
      Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'PDF berhasil dibuat dan diunduh.', timer: 2000, showConfirmButton: false });
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat membuat PDF.' });
    })
    .finally(() => {
      document.head.removeChild(style);
    });
});
</script>
<script>
$(document).on('click', '#btnExcel-cfdirect', function () {
  const table = document.querySelector('.laporan-container-cfdirect').outerHTML;
  const styles = `
    <style>
      body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #2c3e50; }
      table { border-collapse: collapse; width: 100%; }
      th, td { padding: 2px 4px; vertical-align: middle; border: none; }
      .item-left, .section-left, .total-left, .grand-left,
      .judul-left, .subjudul-left, .desc-left, .subsection-left {
        text-align: left !important; mso-justify: left;
      }
      .item-right, .section-right, .total-right, .grand-right,
      .judul-right, .subjudul-right, .item-italic, .grand-italic, .total-italic, .desc-right, .subsection-right {
        text-align: right !important; mso-justify: right;
      }
      .judul-left, .judul-right { font-weight: bold; font-size: 11pt; }
      .subjudul-left, .subjudul-right { font-weight: bold; font-size: 10pt; }
      .number { mso-number-format:"#\\,##0.00_);(#\\,##0.00)"; text-align:right; }
      .periode, .isi-periode, .persentage, .isi-persentage { border-bottom: 2px solid #000; font-weight: bold; text-align: center; }
      .total-line td, .total-line th{ border-top: 2px solid #000 !important; border-bottom: none !important; font-weight: bold; background: #ffffff !important; }
      .grand-total th { border-top: 3px double #000 !important; border-bottom: none !important; font-weight: bold; background: #f2f2f2 !important; }
      .section-left, .section-right, .subsection-left, .subsection-right { font-weight: bold; }
      .spacer { height: 10px; }
    </style>
  `;
  const html = `
    <html xmlns:x="urn:schemas-microsoft-com:office:excel">
      <head><meta charset="UTF-8">${styles}</head>
      <body>${table}</body>
    </html>
  `;
  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "CashFlow_Direct_YTD.xls";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});
</script>
<?php endif; ?>
<?php endif; ?>


<?php if ($fs_system === '2'): ?>
<?php if ($report_type === 'monthly'): ?>
<script type="text/javascript">
$(document).on('click', '#btnPDF-cfindirect', function () {
  const element = document.getElementById('laporan-cfindirect-ytd');

  // 🔹 CSS sementara untuk PDF (perkecil font dan jarak baris)
  const style = document.createElement('style');
  style.innerHTML = `
    #laporan-cfindirect-ytd, 
    #laporan-cfindirect-ytd table, 
    #laporan-cfindirect-ytd th, 
    #laporan-cfindirect-ytd td {
      font-size: 11pt !important;
      line-height: 1.2 !important;
    }
    #laporan-cfindirect-ytd {
      transform: scale(0.9);
      transform-origin: top left;
      width: 110%;
    }
  `;
  document.head.appendChild(style);

  // 🔹 Tampilkan Swal loading
  Swal.fire({
    title: 'Sedang membuat PDF...',
    text: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // 🔹 Opsi PDF — tingkatkan kualitas hasil render
  const opt = {
    margin: [5, 5, 5, 5],
    filename: 'CashFlow_Indirect_Monthly.pdf',
    image: { type: 'jpeg', quality: 1 },
    html2canvas: {
      scale: 4,
      useCORS: true,
      letterRendering: true,
      scrollY: 0
    },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  };

  // 🔹 Proses PDF
  html2pdf()
    .set(opt)
    .from(element)
    .save()
    .then(() => {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'PDF berhasil dibuat dan diunduh.',
        timer: 2000,
        showConfirmButton: false
      });
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: 'Terjadi kesalahan saat membuat PDF.',
      });
    })
    .finally(() => {
      document.head.removeChild(style);
    });
});

</script>
<script>
  // EXPORT EXCEL (CF Indirect Monthly) - pola & alasan identik dengan
  // export SFP/SPL Monthly di atas.
$(document).on('click', '#btnExcel-cfindirect', function () {
  var headerTable = document.getElementById('cfindirect-header-table');
  var bodyTable = document.getElementById('cfindirect-monthly-table');
  if (!headerTable || !bodyTable) return;

  var valueColCount = headerTable.querySelectorAll('colgroup col').length - 1;

  function textOf(selector) {
    var el = document.querySelector(selector);
    return el ? el.textContent.trim() : '';
  }

  var companyName = textOf('#cf-indirect .sfp-title-company');
  var reportId = textOf('#cf-indirect .sfp-title-report');
  var reportEn = textOf('#cf-indirect .sfp-title-report-en');
  var period = textOf('#cf-indirect .sfp-title-period');
  var descId = textOf('#cf-indirect .sfp-title-desc');
  var descEn = textOf('#cf-indirect .sfp-title-desc-en');

  var blankFiller = new Array(valueColCount + 1).join('<th></th>');
  var titleRows =
    '<tr><th class="judul-left">' + companyName + '</th>' + blankFiller + '<th class="judul-right">' + companyName + '</th></tr>' +
    '<tr><th class="judul-left">' + reportId + '</th>' + blankFiller + '<th class="judul-right">' + reportEn + '</th></tr>' +
    '<tr><th class="judul-left">' + period + '</th>' + blankFiller + '<th class="judul-right">' + period + '</th></tr>' +
    '<tr><th class="desc-left">' + descId + '</th>' + blankFiller + '<th class="desc-right">' + descEn + '</th></tr>';

  function cloneBodyRowWithEn(tr) {
    var clone = tr.cloneNode(true);
    var firstCell = clone.cells[0];
    var isItemRow = firstCell && firstCell.tagName === 'TD';
    var enClass = 'item-italic';
    if (clone.classList.contains('grand-total')) {
      enClass = 'grand-italic';
    } else if (clone.classList.contains('total-line')) {
      enClass = 'total-italic';
    } else if (clone.querySelector('.section-left')) {
      enClass = 'section-right';
    } else if (clone.querySelector('.subsection-left')) {
      enClass = 'subsection-right';
    }
    var enCell = document.createElement(isItemRow ? 'td' : 'th');
    enCell.className = enClass;
    enCell.textContent = clone.getAttribute('data-en') || '';
    clone.appendChild(enCell);
    return clone.outerHTML;
  }

  function cloneHeaderRow(tr) {
    var clone = tr.cloneNode(true);
    var descCell = clone.querySelector('.periode-desc');
    if (descCell) {
      descCell.textContent = '';
    }
    clone.appendChild(document.createElement('th'));
    return clone.outerHTML;
  }

  var headerRowsHtml = Array.prototype.map.call(headerTable.rows, cloneHeaderRow).join('');
  var bodyRowsHtml = Array.prototype.map.call(bodyTable.rows, cloneBodyRowWithEn).join('');

  var alignMap = {
    'judul-left': 'left', 'desc-left': 'left', 'section-left': 'left', 'subsection-left': 'left',
    'item-left': 'left', 'total-left': 'left', 'grand-left': 'left', 'periode-desc': 'left',
    'judul-right': 'right', 'desc-right': 'right', 'item-right': 'right', 'total-right': 'right',
    'grand-right': 'right', 'item-italic': 'right', 'section-right': 'right', 'subsection-right': 'right',
    'total-italic': 'right', 'grand-italic': 'right',
    'periode': 'center', 'judul-periode': 'center'
  };
  var italicClasses = [
    'judul-left', 'judul-right', 'desc-left', 'desc-right',
    'section-left', 'subsection-left', 'item-left', 'total-left', 'grand-left',
    'item-italic', 'section-right', 'subsection-right', 'total-italic', 'grand-italic'
  ];
  var wrapper = document.createElement('div');
  wrapper.innerHTML = '<table>' + titleRows + headerRowsHtml + bodyRowsHtml + '</table>';
  var tableEl = wrapper.firstElementChild;
  Array.prototype.forEach.call(tableEl.querySelectorAll('th, td'), function (cell) {
    for (var cls in alignMap) {
      if (cell.classList.contains(cls)) {
        cell.style.textAlign = alignMap[cls];
        break;
      }
    }
    for (var i = 0; i < italicClasses.length; i++) {
      if (cell.classList.contains(italicClasses[i])) {
        cell.style.fontStyle = 'italic';
        break;
      }
    }
  });

  var table = tableEl.outerHTML;

  var styles = `
  body {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    color: #000 !important;
  }
  table {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    border-collapse: collapse;
  }
  td, th {
    font-family: Calibri, sans-serif !important;
    font-size: 11pt !important;
    vertical-align: middle;
    padding: 3px 6px;
  }

  .judul-left, .judul-right { font-weight: bold; font-size: 13pt !important; }
  .desc-left, .desc-right { color: #555; }

  .judul-periode, .periode { text-align: center; font-weight: bold; }
  .periode-desc { text-align: left !important; font-weight: bold; }

  .section-left, .subsection-left, .item-left, .total-left, .grand-left {
    font-weight: bold;
  }

  .sfpm-ytd { background: #fdf6e6 !important; }

  .total-line td, .total-line th {
    border-top: 1px solid #000 !important;
  }
  .grand-total td, .grand-total th {
    border-top: 2px solid #000 !important;
    background: #f2f2f2 !important;
  }

  table, th, td {
    border: none;
    mso-border-alt: none;
  }
`;

  var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">' +
    '<head><meta charset="UTF-8"><style>' + styles + '</style>' +
    '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
    '<x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines>false</x:DisplayGridlines></x:WorksheetOptions>' +
    '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
    '</head><body>' + table + '</body></html>';

  var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
  var link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = 'CashFlow_Indirect_Monthly.xls';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
});
</script>
<?php else: ?>
<script type="text/javascript">
$(document).on('click', '#btnPDF-cfindirect', function () {
  const element = document.getElementById('laporan-cfindirect-ytd');
  const style = document.createElement('style');
  style.innerHTML = `
    #laporan-cfindirect-ytd,
    #laporan-cfindirect-ytd table,
    #laporan-cfindirect-ytd th,
    #laporan-cfindirect-ytd td {
      font-size: 11pt !important;
      line-height: 1.2 !important;
    }
    #laporan-cfindirect-ytd {
      transform: scale(0.9);
      transform-origin: top left;
      width: 110%;
    }
  `;
  document.head.appendChild(style);

  Swal.fire({
    title: 'Sedang membuat PDF...',
    text: 'Mohon tunggu sebentar',
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); }
  });

  const opt = {
    margin: [5, 5, 5, 5],
    filename: 'CashFlow_Indirect_YTD.pdf',
    image: { type: 'jpeg', quality: 1 },
    html2canvas: { scale: 4, useCORS: true, letterRendering: true, scrollY: 0 },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
  };

  html2pdf()
    .set(opt)
    .from(element)
    .save()
    .then(() => {
      Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'PDF berhasil dibuat dan diunduh.', timer: 2000, showConfirmButton: false });
    })
    .catch(() => {
      Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat membuat PDF.' });
    })
    .finally(() => {
      document.head.removeChild(style);
    });
});
</script>
<script>
$(document).on('click', '#btnExcel-cfindirect', function () {
  const table = document.querySelector('.laporan-container-cfindirect').outerHTML;
  const styles = `
    <style>
      body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #2c3e50; }
      table { border-collapse: collapse; width: 100%; }
      th, td { padding: 2px 4px; vertical-align: middle; border: none; }
      .item-left, .section-left, .total-left, .grand-left,
      .judul-left, .subjudul-left, .desc-left, .subsection-left {
        text-align: left !important; mso-justify: left;
      }
      .item-right, .section-right, .total-right, .grand-right,
      .judul-right, .subjudul-right, .item-italic, .grand-italic, .total-italic, .desc-right, .subsection-right {
        text-align: right !important; mso-justify: right;
      }
      .judul-left, .judul-right { font-weight: bold; font-size: 11pt; }
      .number { mso-number-format:"#\\,##0.00_);(#\\,##0.00)"; text-align:right; }
      .subjudul-left, .subjudul-right { font-weight: bold; font-size: 10pt; }
      .periode, .isi-periode, .persentage, .isi-persentage { border-bottom: 2px solid #000; font-weight: bold; text-align: center; }
      .total-line td, .total-line th{ border-top: 2px solid #000 !important; border-bottom: none !important; font-weight: bold; background: #ffffff !important; }
      .grand-total th { border-top: 3px double #000 !important; border-bottom: none !important; font-weight: bold; background: #f2f2f2 !important; }
      .section-left, .section-right, .subsection-left, .subsection-right { font-weight: bold; }
      .spacer { height: 10px; }
    </style>
  `;
  const html = `
    <html xmlns:x="urn:schemas-microsoft-com:office:excel">
      <head><meta charset="UTF-8">${styles}</head>
      <body>${table}</body>
    </html>
  `;
  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "CashFlow_Indirect_YTD.xls";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});
</script>
<?php endif; ?>
<?php endif; ?>

<?php if ($fs_system === '1'): ?>
<script>
  // EXPORT EXCEL (FS1 Monthly) - pola & alasan identik dengan export
  // FS2 Monthly di atas (judul dibaca dari .sfp-title-*, kolom EN dari
  // atribut data-en, alignment/italic dipaksa inline) - cuma FS1 tidak ada
  // grup NAG/NAK/YTD, jadi 1 kolom polos per bulan + 1 kolom Total di
  // ujung kanan (lihat fs1_monthly_functions.php).
  function fs1mExportExcel(headerTableId, bodyTableId, titleSelectorPrefix, filename) {
    var headerTable = document.getElementById(headerTableId);
    var bodyTable = document.getElementById(bodyTableId);
    if (!headerTable || !bodyTable) return;

    function textOf(selector) {
      var el = document.querySelector(selector);
      return el ? el.textContent.trim() : '';
    }

    var valueColCount = headerTable.querySelectorAll('colgroup col').length - 1;
    var companyName = textOf(titleSelectorPrefix + ' .sfp-title-company');
    var reportId = textOf(titleSelectorPrefix + ' .sfp-title-report');
    var reportEn = textOf(titleSelectorPrefix + ' .sfp-title-report-en');
    var period = textOf(titleSelectorPrefix + ' .sfp-title-period');
    var descId = textOf(titleSelectorPrefix + ' .sfp-title-desc');
    var descEn = textOf(titleSelectorPrefix + ' .sfp-title-desc-en');

    var blankFiller = new Array(valueColCount + 1).join('<th></th>');
    var titleRows =
      '<tr><th class="judul-left">' + companyName + '</th>' + blankFiller + '<th class="judul-right">' + companyName + '</th></tr>' +
      '<tr><th class="judul-left">' + reportId + '</th>' + blankFiller + '<th class="judul-right">' + reportEn + '</th></tr>' +
      '<tr><th class="judul-left">' + period + '</th>' + blankFiller + '<th class="judul-right">' + period + '</th></tr>' +
      '<tr><th class="desc-left">' + descId + '</th>' + blankFiller + '<th class="desc-right">' + descEn + '</th></tr>';

    function cloneBodyRowWithEn(tr) {
      var clone = tr.cloneNode(true);
      var firstCell = clone.cells[0];
      var isItemRow = firstCell && firstCell.tagName === 'TD';
      var enClass = 'item-italic';
      if (clone.classList.contains('grand-total')) {
        enClass = 'grand-italic';
      } else if (clone.classList.contains('total-line')) {
        enClass = 'total-italic';
      } else if (clone.querySelector('.subsection-left')) {
        enClass = 'subsection-right';
      }
      var enCell = document.createElement(isItemRow ? 'td' : 'th');
      enCell.className = enClass;
      enCell.textContent = clone.getAttribute('data-en') || '';
      clone.appendChild(enCell);
      return clone.outerHTML;
    }

    function cloneHeaderRow(tr) {
      var clone = tr.cloneNode(true);
      var descCell = clone.querySelector('.periode-desc');
      if (descCell) {
        descCell.textContent = '';
      }
      clone.appendChild(document.createElement('th'));
      return clone.outerHTML;
    }

    var headerRowsHtml = Array.prototype.map.call(headerTable.rows, cloneHeaderRow).join('');
    var bodyRowsHtml = Array.prototype.map.call(bodyTable.rows, cloneBodyRowWithEn).join('');

    var alignMap = {
      'judul-left': 'left', 'desc-left': 'left', 'subsection-left': 'left',
      'item-left': 'left', 'total-left': 'left', 'grand-left': 'left', 'periode-desc': 'left',
      'judul-right': 'right', 'desc-right': 'right', 'item-right': 'right', 'total-right': 'right',
      'grand-right': 'right', 'item-italic': 'right', 'subsection-right': 'right',
      'total-italic': 'right', 'grand-italic': 'right',
      'periode': 'center'
    };
    var italicClasses = [
      'judul-left', 'judul-right', 'desc-left', 'desc-right',
      'subsection-left', 'item-left', 'total-left', 'grand-left',
      'item-italic', 'subsection-right', 'total-italic', 'grand-italic'
    ];
    var wrapper = document.createElement('div');
    wrapper.innerHTML = '<table>' + titleRows + headerRowsHtml + bodyRowsHtml + '</table>';
    var tableEl = wrapper.firstElementChild;
    Array.prototype.forEach.call(tableEl.querySelectorAll('th, td'), function (cell) {
      for (var cls in alignMap) {
        if (cell.classList.contains(cls)) {
          cell.style.textAlign = alignMap[cls];
          break;
        }
      }
      for (var i = 0; i < italicClasses.length; i++) {
        if (cell.classList.contains(italicClasses[i])) {
          cell.style.fontStyle = 'italic';
          break;
        }
      }
    });

    var table = tableEl.outerHTML;

    var styles = `
    body { font-family: Calibri, sans-serif !important; font-size: 11pt !important; color: #000 !important; }
    table { font-family: Calibri, sans-serif !important; font-size: 11pt !important; border-collapse: collapse; }
    td, th { font-family: Calibri, sans-serif !important; font-size: 11pt !important; vertical-align: middle; padding: 3px 6px; }
    .judul-left, .judul-right { font-weight: bold; font-size: 13pt !important; }
    .desc-left, .desc-right { color: #555; }
    .periode { text-align: center; font-weight: bold; }
    .periode-desc { text-align: left !important; font-weight: bold; }
    .subsection-left, .item-left, .total-left, .grand-left { font-weight: bold; }
    .sfpm-ytd { background: #fdf6e6 !important; }
    .total-line td, .total-line th { border-top: 1px solid #000 !important; }
    .grand-total td, .grand-total th { border-top: 2px solid #000 !important; background: #f2f2f2 !important; }
    table, th, td { border: none; mso-border-alt: none; }
  `;

    var html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">' +
      '<head><meta charset="UTF-8"><style>' + styles + '</style>' +
      '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
      '<x:Name>Laporan Keuangan</x:Name><x:WorksheetOptions><x:DisplayGridlines>false</x:DisplayGridlines></x:WorksheetOptions>' +
      '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
      '</head><body>' + table + '</body></html>';

    var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  // Event delegation (bind ke document) - bukan getElementById langsung -
  // supaya tombol yg baru muncul lewat lazy-load AJAX (tab belum dibuka
  // user saat page load) tetap ke-handle tanpa perlu re-wiring manual.
  $(document).on('click', '#btnExcel-fs1cfindirect', function () {
    fs1mExportExcel('fs1cfindirect-header-table', 'fs1cfindirect-monthly-table', '#cf-indirect', 'CashFlow_Indirect_Monthly_FS1.xls');
  });

  $(document).on('click', '#btnExcel-fs1spl', function () {
    fs1mExportExcel('fs1spl-header-table', 'fs1spl-monthly-table', '#spl', 'Statement_Profit_or_Loss_Monthly_FS1.xls');
  });

  $(document).on('click', '#btnExcel-fs1cfdirect', function () {
    fs1mExportExcel('fs1cfdirect-header-table', 'fs1cfdirect-monthly-table', '#cf-direct', 'CashFlow_Direct_Monthly_FS1.xls');
  });

  $(document).on('click', '#btnExcel-fs1sfp', function () {
    fs1mExportExcel('fs1sfp-header-table', 'fs1sfp-monthly-table', '#sfp', 'Statement_Financial_Position_Monthly_FS1.xls');
  });
</script>
<?php endif; ?>


</body>

</html>
