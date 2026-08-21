<?php include '../header.php' ?>

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


</style>

<!-- MAIN -->
<div class="container-fluid mt-4 p-4">
  <!-- Card Filter -->
  <div class="card shadow border-0">
    <div class="card-header text-white py-2 px-3" 
    style="background: linear-gradient(90deg, #191970, #1e90ff);">
    <h5 class="mb-0"><i class="fas fa-chart-line"></i> FINANCIAL STATEMENT MONTHLY</h5>
</div>

<div class="card-body p-3">
  <form id="form-data" action="financial_statement_monthly.php" method="post">
    <div class="row g-3">
      <!-- Supplier -->
      <div class="col-md-3 mb-3">
                <label for="h_profit_center"><b>Profit Center</b></label>            
                <select class="form-control selectpicker" name="h_profit_center" id="h_profit_center" data-dropup-auto="false" data-live-search="true">
                    <option value="ALL" selected="true">ALL</option>                                                
                    <?php
                    $nama_supp ='';
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $nama_supp = isset($_POST['h_profit_center']) ? $_POST['h_profit_center']: null;
                    }                 
                    $sql = mysqli_query($conn1,"select kode_pc, id_pc,nama_pc, CONCAT(id_pc,' - ',nama_pc) tampil from master_pc where status = 'Active'");
                    while ($row = mysqli_fetch_array($sql)) {
                        $data = $row['kode_pc'];
                        $data2 = $row['tampil'];
                        if($row['kode_pc'] == $_POST['h_profit_center']){
                            $isSelected = ' selected="selected"';
                        }else{
                            $isSelected = '';

                        }
                        echo '<option value="'.$data.'"'.$isSelected.'">'. $data2 .'</option>';    
                    }?>
                </select>
            </div>

    <!-- Start Date -->
    <div class="col-md-2">
        <label for="start_date" class="form-label"><b>From</b></label>
        <input type="text" class="form-control form-control-sm tanggal" id="start_date" name="start_date"
        value="<?php
        $start_date ='';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
           $start_date = date("Y-m-d",strtotime($_POST['start_date']));
       }
       if(!empty($_POST['start_date'])) {
           echo $_POST['start_date'];
       }
       else{
           // Default From selalu Januari tahun berjalan - semua kolom bulan
           // di tab-tab Monthly ini adalah angka YTD (kumulatif dari titik
           // ini), jadi "From" bukan tanggal bebas seperti di tab lain.
           echo 'Jan ' . date('Y');
       } ?>" placeholder="Month Year" autocomplete="off">
   </div>

   <!-- End Date -->
   <div class="col-md-2">
    <label for="end_date" class="form-label"><b>To</b></label>
    <input type="text" class="form-control form-control-sm tanggal" id="end_date" name="end_date"
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

<!-- Tombol -->
<div class="col-md-3 d-flex align-items-end mb-3">
  <button type="submit" class="btn btn-info btn-sm me-2">
    <i class="fa fa-search"></i> Search
</button>
<button type="button" id="reset" class="btn btn-danger btn-sm ml-2">
    <i class="fa fa-undo"></i> Reset
</button> 

</div>

</div>
</form>
</div>
</div>

<!-- Card Table -->
<div class="card shadow border-0 mt-4">
  <div class="card-body p-4">

   <div class="tab">
  <button class="tablinks" onclick="openTab(event, 'trial-balance')" id="defaultOpen">Trial Balance</button>
  <button class="tablinks" onclick="openTab(event, 'sfp')">SFP</button>
  <button class="tablinks" onclick="openTab(event, 'spl')">SPL</button>
  <button class="tablinks" onclick="openTab(event, 'cf-direct')">CF Direct</button>
  <button class="tablinks" onclick="openTab(event, 'cf-indirect')">CF Indirect</button>
</div>

<div id="trial-balance" class="tabcontent">
  <!-- <h2>Trial Balance</h2> -->
  <?php include 'fs_monthly/trial_balance_monthly.php'; ?>
</div>
<div id="sfp" class="tabcontent">
  <?php include 'fs_monthly/statement_financial_position_monthly.php'; ?>
</div>
<div id="spl" class="tabcontent">
  <?php include 'fs_monthly/statement_profit_loss_monthly.php'; ?>
</div>
<div id="cf-direct" class="tabcontent">
  <!-- <h6>Isi tab CF Direct</h6> -->
  <?php include 'fs_monthly/cashflow_direct_monthly.php'; ?>
</div>
<div id="cf-indirect" class="tabcontent">
  <!-- <h6>Isi tab CF Indirect</h6> -->
  <?php include 'fs_monthly/cashflow_indirect_monthly.php'; ?>
</div>

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

      $(document).ready(function() {
  // Hitung ulang posisi "left" 6 kolom freeze (No COA s.d. Category 4) dari
  // lebar ASLI hasil render browser - bukan angka px tebakan. Lebar kolom
  // dibiarkan natural (ikut isi), jadi offset-nya harus dihitung ulang tiap
  // kali tabel di-draw ulang (ganti halaman/search), bukan cuma sekali.
  function updateFrozenOffsets() {
    var table = document.getElementById('table_tbmonthly');
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

  var tbMonthlyTable = $('#table_tbmonthly').DataTable({
    // scrollX SENGAJA tidak dipakai - DataTables scrollX meng-clone header
    // ke elemen terpisah yang disinkron pakai transform (bukan scroll asli),
    // jadi CSS position:sticky untuk freeze kolom tidak berlaku di header-nya.
    // Scroll horizontal cukup diserahkan ke div .table-responsive (scroll
    // native) supaya sticky konsisten di header maupun body.
    //
    // "dom" custom di bawah ini yang bikin Search & Pagination dirender DI
    // LUAR div .table-responsive (cuma tabelnya - token 't' - yang dibungkus
    // .table-responsive) - kalau tidak, Search/Pagination ikut ke dalam
    // area yang sama dengan tabel dan ikut ke-scroll waktu geser ke samping.
    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
         "<'table-responsive't>" +
         "<'row'<'col-sm-5'i><'col-sm-7'p>>",
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
});

</script>


<script>
  // EXPORT EXCEL (SFP Monthly) - judul dibangun ulang di sini dalam format
  // klasik ID (kiri) / EN (kanan), persis konvensi tab SFP YTD
  // (fs_ytd/statement_financial_position.php) - blok judul di TAMPILAN
  // LAYAR SFP Monthly sendiri sudah dipindah jadi 1 blok terpusat di atas
  // tabel (lihat .sfp-title-block), jadi baris judul kiri/kanan di bawah
  // ini KHUSUS dibuat ulang untuk file Excel, dibaca dari teks yang sama
  // (.sfp-title-company/-report/-report-en/-period/-desc/-desc-en) supaya
  // datanya tetap 1 sumber, tidak di-hardcode dobel.
document.getElementById('btnExcel').addEventListener('click', function () {
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
  document.getElementById('btnPDF').addEventListener('click', function() {
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
$(document).on("click", "#co_sal", function(){ 
  // ambil data dari baris tabel
  var no_coa = $(this).closest('tr').find('td:eq(1)').attr('value');
  var beg_balance = $(this).closest('tr').find('td:eq(7)').attr('value');
  var debit = $(this).closest('tr').find('td:eq(8)').attr('value');
  var credit = $(this).closest('tr').find('td:eq(9)').attr('value');
  var end_balance = $(this).closest('tr').find('td:eq(10)').attr('value');
  var copy_user = '<?php echo $user ?>';
  var to_saldo_el = document.getElementById('to_saldo');
  var to_saldo = to_saldo_el ? to_saldo_el.value : null;

  // tampilkan semua di console
  console.log("=== DEBUG COPY SALDO ===");
  console.log("no_coa:", no_coa);
  console.log("beg_balance:", beg_balance);
  console.log("debit:", debit);
  console.log("credit:", credit);
  console.log("end_balance:", end_balance);
  console.log("copy_user:", copy_user);
  console.log("to_saldo:", to_saldo);
  console.log("========================");

  // jika elemen 'to_saldo' tidak ditemukan, tampilkan peringatan
  if (!to_saldo_el) {
    console.error("Elemen #to_saldo tidak ditemukan di halaman!");
    return;
  }

  // jalankan ajax
  $.ajax({
    type:'POST',
    url:'fs_ytd/copy_saldo_tb_new.php',
    data:{
      no_coa:no_coa,
      beg_balance:beg_balance,
      debit:debit,
      credit:credit,
      end_balance:end_balance,
      copy_user:copy_user,
      to_saldo:to_saldo
    },
    success: function(response){
      console.log("Response:", response);
      alert("Copy Saldo successfully");
    },
    error: function(xhr, ajaxOptions, thrownError) {
      console.error("AJAX Error:", xhr.responseText);
      alert("Error: " + xhr.responseText);
    }
  });
});
</script>


<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>

<script>
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

  // 🔧 Re-adjust DataTables saat tab aktif
  $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();

  // Tab yang baru ditampilkan lebar kolomnya baru bisa diukur browser SETELAH
  // display:block (elemen yang display:none selalu 0 lebar) - makanya offset
  // freeze-nya baru dihitung ulang di sini, bukan cuma sekali saat halaman
  // dimuat.
  if (tabName === 'trial-balance' && typeof updateFrozenOffsets === 'function') {
    updateFrozenOffsets();
  }
  if (tabName === 'sfp' && typeof updateSfpHeaderOffset === 'function') {
    updateSfpHeaderOffset();
  }
}

document.getElementById("defaultOpen").click();
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
        location.href = "financial_statement_monthly.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "financial_statement_monthly.php";
    };
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

<script type="text/javascript">
document.getElementById('btnPDF-spl').addEventListener('click', function () {
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
document.getElementById('btnExcel-spl').addEventListener('click', function () {
  // Ambil tabel utama
  const table = document.querySelector('.laporan-container-spl').outerHTML;

  // Tambahkan CSS ke dalam Excel
  const styles = `
    <style>
      body {
        font-family: Calibri, Arial, sans-serif;
        font-size: 11pt;
        color: #2c3e50;
      }

      table {
        border-collapse: collapse;
        width: 100%;
      }

      th, td {
        padding: 2px 4px;
        vertical-align: middle;
        border: none;
      }

      /* ==== ALIGNMENT KHUSUS EXCEL ==== */
      .item-left, .section-left, .total-left, .grand-left,
      .judul-left, .subjudul-left, .grand-left, .desc-left, .subsection-left {
        text-align: left !important;
        mso-justify: left;
      }

      .item-right, .section-right, .total-right, .grand-right,
      .judul-right, .subjudul-right, .item-italic, .grand-italic, .total-italic, .desc-right, .subsection-right {
        text-align: right !important;
        mso-justify: right;
      }

      .number {
    mso-number-format:"#\,##0.00_);(#\,##0.00)";
    text-align:right;
}

      /* ==== JUDUL DAN SUBJUDUL ==== */
      .judul-left, .judul-right {
        font-weight: bold;
        font-size: 11pt;
      }

      .subjudul-left, .subjudul-right {
        font-weight: bold;
        font-size: 10pt;
      }

      /* ==== PERIODE ==== */
      .periode, .isi-periode, .persentage, .isi-persentage {
        border-bottom: 2px solid #000;
        font-weight: bold;
        text-align: center;
      }

      /* ==== TOTAL ==== */
      .total-line td, .total-line th{
        border-top: 2px solid #000 !important;
        border-bottom: none !important;
        font-weight: bold;
        background: #ffffff !important;
      }

      /* ==== GRAND TOTAL ==== */
      .grand-total th {
        border-top: 3px double #000 !important;
        border-bottom: none !important;
        font-weight: bold;
        background: #f2f2f2 !important;
      }

      /* ==== SECTION ==== */
      .section-left, .section-right, .subsection-left, .subsection-right {
        font-weight: bold;
      }

      /* ==== SPASER ==== */
      .spacer { height: 10px; }
    </style>
  `;

  const html = `
    <html xmlns:x="urn:schemas-microsoft-com:office:excel">
      <head>
        <meta charset="UTF-8">
        ${styles}
      </head>
      <body>
        ${table}
      </body>
    </html>
  `;

  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "Statement_Profit_or_Loss_Monthly.xls";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});
</script>


<script type="text/javascript">
document.getElementById('btnPDF-cfdirect').addEventListener('click', function () {
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
document.getElementById('btnExcel-cfdirect').addEventListener('click', function () {
  // Ambil tabel utama
  const table = document.querySelector('.laporan-container-cfdirect').outerHTML;

  // Tambahkan CSS ke dalam Excel
  const styles = `
    <style>
      body {
        font-family: Calibri, Arial, sans-serif;
        font-size: 11pt;
        color: #2c3e50;
      }

      table {
        border-collapse: collapse;
        width: 100%;
      }

      th, td {
        padding: 2px 4px;
        vertical-align: middle;
        border: none;
      }

      /* ==== ALIGNMENT KHUSUS EXCEL ==== */
      .item-left, .section-left, .total-left, .grand-left,
      .judul-left, .subjudul-left, .grand-left, .desc-left, .subsection-left {
        text-align: left !important;
        mso-justify: left;
      }

      .item-right, .section-right, .total-right, .grand-right,
      .judul-right, .subjudul-right, .item-italic, .grand-italic, .total-italic, .desc-right, .subsection-right {
        text-align: right !important;
        mso-justify: right;
      }

      /* ==== JUDUL DAN SUBJUDUL ==== */
      .judul-left, .judul-right {
        font-weight: bold;
        font-size: 11pt;
      }

      .subjudul-left, .subjudul-right {
        font-weight: bold;
        font-size: 10pt;
      }

      .number {
    mso-number-format:"#\,##0.00_);(#\,##0.00)";
    text-align:right;
}

      /* ==== PERIODE ==== */
      .periode, .isi-periode, .persentage, .isi-persentage {
        border-bottom: 2px solid #000;
        font-weight: bold;
        text-align: center;
      }

      /* ==== TOTAL ==== */
      .total-line td, .total-line th{
        border-top: 2px solid #000 !important;
        border-bottom: none !important;
        font-weight: bold;
        background: #ffffff !important;
      }

      /* ==== GRAND TOTAL ==== */
      .grand-total th {
        border-top: 3px double #000 !important;
        border-bottom: none !important;
        font-weight: bold;
        background: #f2f2f2 !important;
      }

      /* ==== SECTION ==== */
      .section-left, .section-right, .subsection-left, .subsection-right {
        font-weight: bold;
      }

      /* ==== SPASER ==== */
      .spacer { height: 10px; }
    </style>
  `;

  const html = `
    <html xmlns:x="urn:schemas-microsoft-com:office:excel">
      <head>
        <meta charset="UTF-8">
        ${styles}
      </head>
      <body>
        ${table}
      </body>
    </html>
  `;

  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "CashFlow_Direct_Monthly.xls";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});
</script>


<script type="text/javascript">
document.getElementById('btnPDF-cfindirect').addEventListener('click', function () {
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
document.getElementById('btnExcel-cfindirect').addEventListener('click', function () {
  // Ambil tabel utama
  const table = document.querySelector('.laporan-container-cfindirect').outerHTML;

  // Tambahkan CSS ke dalam Excel
  const styles = `
    <style>
      body {
        font-family: Calibri, Arial, sans-serif;
        font-size: 11pt;
        color: #2c3e50;
      }

      table {
        border-collapse: collapse;
        width: 100%;
      }

      th, td {
        padding: 2px 4px;
        vertical-align: middle;
        border: none;
      }

      /* ==== ALIGNMENT KHUSUS EXCEL ==== */
      .item-left, .section-left, .total-left, .grand-left,
      .judul-left, .subjudul-left, .grand-left, .desc-left, .subsection-left {
        text-align: left !important;
        mso-justify: left;
      }

      .item-right, .section-right, .total-right, .grand-right,
      .judul-right, .subjudul-right, .item-italic, .grand-italic, .total-italic, .desc-right, .subsection-right {
        text-align: right !important;
        mso-justify: right;
      }

      /* ==== JUDUL DAN SUBJUDUL ==== */
      .judul-left, .judul-right {
        font-weight: bold;
        font-size: 11pt;
      }

      .number {
    mso-number-format:"#\,##0.00_);(#\,##0.00)";
    text-align:right;
}

      .subjudul-left, .subjudul-right {
        font-weight: bold;
        font-size: 10pt;
      }

      /* ==== PERIODE ==== */
      .periode, .isi-periode, .persentage, .isi-persentage {
        border-bottom: 2px solid #000;
        font-weight: bold;
        text-align: center;
      }

      /* ==== TOTAL ==== */
      .total-line td, .total-line th{
        border-top: 2px solid #000 !important;
        border-bottom: none !important;
        font-weight: bold;
        background: #ffffff !important;
      }

      /* ==== GRAND TOTAL ==== */
      .grand-total th {
        border-top: 3px double #000 !important;
        border-bottom: none !important;
        font-weight: bold;
        background: #f2f2f2 !important;
      }

      /* ==== SECTION ==== */
      .section-left, .section-right, .subsection-left, .subsection-right {
        font-weight: bold;
      }

      /* ==== SPASER ==== */
      .spacer { height: 10px; }
    </style>
  `;

  const html = `
    <html xmlns:x="urn:schemas-microsoft-com:office:excel">
      <head>
        <meta charset="UTF-8">
        ${styles}
      </head>
      <body>
        ${table}
      </body>
    </html>
  `;

  const blob = new Blob([html], { type: "application/vnd.ms-excel" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = "CashFlow_Indirect_Monthly.xls";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
});
</script>


</body>

</html>
