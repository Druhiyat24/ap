<?php include '../header.php' ?>

<!-- Skin kontrol form (dropdown & kalender tanggal) — potongan dari app-skin.css.
     Sengaja HANYA berkas ini, bukan app-skin.css utuh: skin penuh juga mengubah
     kartu/tabel/tombol/DataTables, sedangkan halaman ini sudah punya gaya
     kartu & tombolnya sendiri (diselaraskan dgn financial_statement.php). -->
<!-- ?v=filemtime: browser meng-cache CSS cukup agresif. Tanpa penanda versi,
     perubahan skin tidak kelihatan sampai user hard-refresh (Ctrl+F5). -->
<link rel="stylesheet" href="../css/app-skin.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin.css'); ?>">
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">

<style >
    .modal {
  text-align: center;
  padding: 0!important;
}

.modal:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px;
}

.modal-dialog {
  display: inline-table;
  width: 700px;
  text-align: left;
  vertical-align: middle;
}

/* ===== Skin filter & tombol - diselaraskan dgn financial_statement.php =====
   Tinggi input & tombol dikunci ke satu nilai (--fs-ctl-h) supaya SEJAJAR.
   Perlu dikunci karena app-skin-form.css membuat input.tanggal lebih tinggi
   (padding 8px 12px) dari tinggi bawaan tombol pill - kalau dibiarkan
   menghitung sendiri dari font, keduanya jadi beda tinggi. */
:root { --fs-ctl-h: 38px; }

.fs-btn-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 7px;
  height: var(--fs-ctl-h);
  font-size: 12.5px;
  font-weight: 600;
  letter-spacing: .2px;
  color: #fff;
  border: none;
  border-radius: 999px;
  padding: 0 20px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transition: box-shadow 0.15s ease, transform 0.15s ease, background 0.15s ease;
}
.fs-btn-teal { background: linear-gradient(135deg, #17a2b8, #0f7c8f); box-shadow: 0 2px 6px rgba(15,124,143,0.3); }
.fs-btn-teal:hover {
  background: linear-gradient(135deg, #1cb8d1, #128a9f);
  box-shadow: 0 4px 10px rgba(15,124,143,0.38);
  transform: translateY(-1px); color: #fff;
}
.fs-btn-green { background: linear-gradient(135deg, #28c76f, #1f9d57); box-shadow: 0 2px 6px rgba(31,157,87,0.3); }
.fs-btn-green:hover {
  background: linear-gradient(135deg, #34d97e, #24ab60);
  box-shadow: 0 4px 10px rgba(31,157,87,0.38);
  transform: translateY(-1px); color: #fff;
}
.fs-btn-pill:active { transform: translateY(0); box-shadow: inset 0 2px 4px rgba(0,0,0,0.15); }

/* Field tanggal dgn ikon kalender di dalam kotak */
.fs-month-field { position: relative; width: 240px; max-width: 100%; }
.fs-month-field .fs-month-ico {
  position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
  color: #17a2b8; font-size: 12.5px; pointer-events: none;
}
.fs-month-field .fs-month-input {
  border: 1px solid #d7dce5 !important;
  border-radius: 8px !important;
  background: #fff !important;
  height: var(--fs-ctl-h) !important;
  padding: 0 12px 0 30px !important;   /* kiri lebih lebar utk ikon kalender */
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

/* Header tabel senada dgn financial_statement.php */
.table-gradient th { background: #1E3A8A; color: #fff; text-align: center; vertical-align: middle; white-space: nowrap; }
div.dataTables_wrapper .dataTables_paginate { float: right; margin-top: 10px; }
div.dataTables_wrapper .dataTables_info { float: left; margin-top: 10px; }
</style>
    <!-- MAIN -->
    <div class="container-fluid mt-4 p-4">
    <!-- Card Filter -->
    <div class="card shadow border-0 filter-card">
      <div class="card-header text-white py-2 px-3" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="mb-0"><i class="fa fa-file-text-o"></i> REPORT FAKTUR PAJAK</h5>
      </div>
    <div class="card-body p-3">

        <form id="form-data" action="report-faktur-pajak.php" method="post">
        <div class="row g-3 align-items-end">

            <!-- <div class="col-md-4">
            <label for="nama_type"><b>No COA</b></label>            
              <select style="background-color: gray;" class="form-control selectpicker" name="coa_number" id="coa_number" data-dropup-auto="false" data-live-search="true" required>
                 <option value="-" disabled selected="true">Select coa</option> 
                <?php
                $coa_number ='';
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $coa_number = isset($_POST['coa_number']) ? $_POST['coa_number']: null;
                }                 
                $sql = mysql_query("select DISTINCT no_coa, nama_coa, CONCAT(no_coa,' - ',nama_coa) as coa from mastercoa_v2",$conn1);
                while ($row = mysql_fetch_array($sql)) {
                    $data = $row['coa'];
                    $id_ctg2 = $row['no_coa'];
                    if($row['no_coa'] == $_POST['coa_number']){
                        $isSelected = ' selected="selected"';
                    }else{
                        $isSelected = '';
                    }
                    echo '<option value="'.$id_ctg2.'"'.$isSelected.'">'. $data .'</option>';    
                }?>
                </select>
                </div>  -->

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
                 echo date("d-m-Y");
              } ?>"
              placeholder="Tanggal Awal" autocomplete="off">
            </div>
            </div>

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
                 echo date("d-m-Y");
              } ?>"
              placeholder="Tanggal Akhir" autocomplete="off">
            </div>
            </div>
            <div class="col-auto mb-3 d-flex align-items-end" style="gap:10px;">
            <button type="submit" id="submit" class="btn fs-btn-pill fs-btn-teal"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
<!--             <button type="button" id="reset" value=" Reset " style="height: 35px; margin-top: 30px; margin-bottom: 5px;margin-right: 15px;border: 0;
    line-height: 1;
    padding: -2px 8px;
    font-size: 1rem;
    text-align: center;
    color: #fff;
    text-shadow: 1px 1px 1px #000;
    border-radius: 6px;
    background-color:rgb(250, 69, 1)"><i class="fa fa-repeat" aria-hidden="true"></i> Reset </button> -->

<?php
        // $status = isset($_POST['status']) ? $_POST['status']: null;
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;
        $tanggal_awal = date("Y-m-d",strtotime($start_date));
        $tanggal_akhir = date("Y-m-d",strtotime($end_date)); 
        $tanggal1 = isset($tanggal_awal) ? $tanggal_awal : 0;
        $tanggal2 = isset($tanggal_akhir) ? $tanggal_akhir : 0;
        $kata_awal = date("M",strtotime($start_date));
        $tengah = '_';
        $kata_akhir = date("Y",strtotime($start_date));
        $kata_filter = $kata_awal . $tengah . $kata_akhir;


        // Tanggal TIDAK lagi ditempel dari PHP: sejak tabel pakai DataTables AJAX,
        // halaman tidak reload saat Search, jadi nilai PHP di sini bisa basi.
        // Link-nya dirakit di JS dari isi input saat tombol diklik (lihat #btnExport).
        echo '<button type="button" id="btnExport" class="btn fs-btn-pill fs-btn-green"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</button>';
        //<a style="padding-right: 5px;" target="_blank" href="ekspor_sfp_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SFP</i></button></a>

        // <a style="padding-right: 5px;" target="_blank" href="ekspor_spl_ytd.php?start_date='.$start_date.' && end_date='.$end_date.' && kata_filter='.$kata_filter.'"><button type="button" class="btn btn-success " style= "margin-top: 30px;"><i class="fa fa-file-excel-o" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Excel SPL</i></button></a>

        //     <a style="padding-left: 10px";><button type="button" class="btn btn-info " name="co_sal" id="co_sal" style= "margin-top: 30px;"><i class="fa fa-clipboard" aria-hidden="true" style="padding-right: 10px; padding-left: 5px;font-size: 1rem;color: #fff;text-shadow: 1px 1px 1px #000"> Copy Saldo</i></button></a>

        
        ?>

            </div>
        </div>
        </form>
    </div><!-- card-body -->
    </div><!-- card filter -->
    <br/>

<!-- <?php
        $querys = mysqli_query($conn2,"select useraccess.menu as menu,useraccess.username as username, useraccess.fullname as fullname, menurole.id as id from useraccess inner join menurole on menurole.menu = useraccess.menu where username = '$user' and useraccess.menu = 'Create List payment'");
        $rs = mysqli_fetch_array($querys);
        $id = isset($rs['id']) ? $rs['id'] : 0;

        if($id == '9'){
    echo '<button id="btncreate" type="button" class="btn-primary btn-xs" style="border-radius: 6%"><span class="fa fa-pencil-square-o"></span> Create</button>
            <button id="btnupload" type="button" class="btn-success btn-xs" style="border-radius: 6%"><span class="fa fa-upload" aria-hidden="true"></span> Upload</button>';
        }else{
    echo '';
    }
?> -->
    <!-- ===== Table card ===== -->
    <!-- Data TIDAK lagi dirender server-side; diisi lewat DataTables AJAX ke
         ajx_report_faktur_pajak.php (pola sama dgn ppn_masukan_report.php),
         supaya Search tidak me-reload halaman & ada overlay loading. -->
    <div class="card app-card border-0">
      <div class="card-body p-4">
        <!-- .app-loading-wrap: area yg ditutup overlay loading saat Search (skin: app-skin-form.css) -->
        <div class="app-loading-wrap" id="fpLoad">
          <div class="app-loading">
            <div class="app-loading-box">
              <div class="app-spinner"></div>
              <div class="app-loading-text">Loading data...</div>
            </div>
          </div>
          <table id="mytable" class="table table-hover app-dt" style="width:100%">
            <thead>
              <tr>
                <th>Nomor FP</th>
                <th>Tanggal FP</th>
                <th>Nomor BPB</th>
                <th>Tanggal BPB</th>
                <th>Referensi</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>DPP</th>
                <th>Diskon</th>
                <th>PPN</th>
                <th>Total</th>
                <th>Support Doc</th>
              </tr>
            </thead>
            <tbody><!-- diisi via DataTables AJAX (ajx_report_faktur_pajak.php) --></tbody>
            <!-- GRAND TOTAL: dihitung dari SELURUH baris hasil filter (lintas
                 halaman), lihat footerCallback. Urutan 13 sel = urutan kolom. -->
            <tfoot>
              <tr>
                <th class="ftot-label">GRAND TOTAL</th>
                <th class="ftot-sub" id="fpFootCount"></th>
                <th></th><th></th><th></th><th></th>
                <th class="num"></th><th class="num"></th><th class="num"></th>
                <th class="num"></th><th class="num"></th><th class="num"></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div><!-- app-loading-wrap -->
      </div><!-- card-body -->
    </div><!-- card tabel -->
</div><!-- container-fluid END -->
</div><!-- body-row END (wrapper dari header.php) -->
</div>

<div class="form-row">
    <div class="modal fade" id="mymodal2" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div style="width:450px;" class="modal-dialog modal-md">
        <div style="height: 225px" class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="Heading" style="text-align: center;"><b>UPLOAD</b></h4>
        </div>
          <div class="modal-body">
          <div class="form-group">
            <form method="post" enctype="multipart/form-data" action="proses_upload.php">
                                    Pilih File:
                                    <input class="form-control" name="fileexcel" type="file" required="required">
                                    <br>
                                    <button class="btn btn-sm btn-info" type="submit">Submit</button>
                                    <a target="_blank" href="format_upload_mj.xls"><button type="button" class="btn btn-warning "><i class="fa fa-file-excel-o" aria-hidden="true"> Format Upload</i></button></a>
                                </form>
        </div>
      </div>
    </div>
  </div>
 </div>

<div class="modal fade" id="mymodal" data-target="#mymodal" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
        <h4 class="modal-title" id="txt_bpb"></h4>
        </div>
        <div class="container">
        <div class="row">
          <div id="txt_tglbpb" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_no_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
          <div id="txt_supp" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
<!--           <div id="txt_top" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>         
          <div id="txt_curr" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div> -->
          <div id="txt_confirm" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>
  <!--         <div id="txt_tgl_po" class="modal-body col-6" style="font-size: 12px; padding: 0.5rem;"></div>  -->                    
          <div id="details" class="modal-body col-12" style="font-size: 12px; padding: 0.5rem;"></div>          
        </div>
        </div>
        </div>
    <!-- /.modal-content 
  </div>
      /.modal-dialog 
    </div> -->         
                                
</div><!-- body-row END -->
</div>
</div>

  <!-- Bootstrap core JavaScript -->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/datatables.min.js"></script>
    <script language="JavaScript" src="../css/4.1.1/bootstrap-datepicker.js"></script>
  <script language="JavaScript" src="../css/4.1.1/bootstrap-select.min.js"></script>
  <script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min.js"></script>
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
<!-- <script>
    $(document).ready(function() {
    $('#datatable').dataTable();
    
     $("[data-toggle=tooltip]").tooltip();
    
} );
</script> -->

<script>
var tFp;
$(document).ready(function() {

  // ===== DataTables AJAX =====
  // Endpoint (ajx_report_faktur_pajak.php) balikan JSON: { "data": [ [ ...13 kolom... ], ... ] }
  // (array per baris, urutan kolom sesuai header). Pola sama dgn ppn_masukan_report.php.
  var numCols = [6, 7, 8, 9, 10, 11];   // Harga, Qty, DPP, Diskon, PPN, Total

  tFp = $('#mytable').DataTable({
    ordering: false,
    processing: true,
    autoWidth: false,
    pageLength: 10,
    lengthMenu: [10, 25, 50, 100, 200],
    // Layout: length+search (atas) & info+pagination (bawah) DI LUAR area scroll;
    // hanya tabel yg dibungkus .app-dt-scroll yang scroll horizontal.
    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
         "<'app-dt-scroll't>" +
         "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
    ajax: {
      url: 'ajx_report_faktur_pajak.php',
      type: 'POST',
      data: function (d) {
        d.start_date = $('#start_date').val() || '';
        d.end_date   = $('#end_date').val() || '';
      },
      dataSrc: 'data'
    },
    columnDefs: [
      { targets: numCols, className: 'num' },
      { targets: [0, 2, 4, 5, 12], className: 'txtleft' }
    ],
    // GRAND TOTAL di <tfoot>: memakai rows({search:'applied'}) -> menjumlah SEMUA
    // baris hasil filter, TIDAK terpatok halaman aktif.
    footerCallback: function () {
      var api  = this.api();
      var rows = api.rows({ search: 'applied' }).data();
      var num  = function (v) {
        v = (v === null || v === undefined) ? '' : String(v).replace(/,/g, '').trim();
        return v === '' ? 0 : (parseFloat(v) || 0);
      };
      var fmt  = function (v) { return v.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
      var sumCols = [8, 10, 11];   // DPP, PPN, Total (Harga & Qty tidak dijumlah - tidak bermakna)
      var tot = {}, i, c;
      for (i = 0; i < sumCols.length; i++) { tot[sumCols[i]] = 0; }
      for (i = 0; i < rows.length; i++) {
        for (c = 0; c < sumCols.length; c++) { tot[sumCols[c]] += num(rows[i][sumCols[c]]); }
      }
      var $f = $('#mytable tfoot th');
      $f.each(function (idx) {
        if (sumCols.indexOf(idx) >= 0) { $(this).text(fmt(tot[idx])); }
        else if (idx > 1 && idx < 12) { $(this).text(''); }
      });
      $('#fpFootCount').text(rows.length + ' baris');
    },
    language: {
      processing:  '<i class="fa fa-spinner fa-spin"></i> Loading...',
      emptyTable:  '<div class="app-empty"><i class="fa fa-inbox"></i>Data not found</div>',
      zeroRecords: '<div class="app-empty"><i class="fa fa-search"></i>No matching records</div>',
      lengthMenu:  'Show _MENU_ entries',
      info:        'Showing _START_&ndash;_END_ of _TOTAL_ entries',
      infoEmpty:   'Showing 0 entries',
      paginate:    { previous: '&lsaquo; Prev', next: 'Next &rsaquo;' }
    }
  });

  // Overlay loading (skin .app-loading) mengikuti status processing DataTables.
  tFp.on('processing.dt', function (e, settings, processing) {
    $('#fpLoad').toggleClass('is-loading', processing);
  });

  // Search -> reload tabel (bukan reload halaman)
  $('#form-data').on('submit', function (e) { e.preventDefault(); tFp.ajax.reload(); });

  // Export Excel: rakit URL dari isi input SAAT diklik (bukan dari PHP), supaya
  // selalu ikut filter tanggal terakhir walau halaman tidak pernah reload.
  $('#btnExport').on('click', function () {
    var s = $('#start_date').val() || '';
    var e = $('#end_date').val() || '';
    window.open('ekspor-report-faktur-pajak.php?start_date=' + encodeURIComponent(s) +
                '&end_date=' + encodeURIComponent(e), '_blank');
  });

  $("[data-toggle=tooltip]").tooltip();
});
</script>

<script>
function myFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("datatable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>

<script type="text/javascript">
    $(document).ready(function () {
    $('.tanggal').datepicker({
        format: "dd-mm-yyyy",
        startDate : "01-01-2023",
        autoclose:true
    })
    // Samakan lebar kalender popup dengan lebar input pemicunya.
    .on('show', function () {
      var w = $(this).outerWidth();
      setTimeout(function () { $('.datepicker-dropdown:visible').outerWidth(w); }, 0);
    });
});
</script>

<script>
$(function() {
    $('.selectpicker').selectpicker();
});
</script>

<script type="text/javascript">
    $("#form-data").on("click", "#co_sal", function(){ 
        var no_coa = $(this).closest('tr').find('td:eq(1)').attr('value');
        var beg_balance = $(this).closest('tr').find('td:eq(7)').attr('value');
        var debit = $(this).closest('tr').find('td:eq(8)').attr('value');
        var credit = $(this).closest('tr').find('td:eq(9)').attr('value');
        var end_balance = $(this).closest('tr').find('td:eq(10)').attr('value');
        var copy_user = '<?php echo $user ?>';
        var to_saldo = document.getElementById('to_saldo').value;

        $.ajax({
            type:'POST',
            url:'copy_saldo_tb.php',
            data: {'no_coa':no_coa, 'beg_balance':beg_balance,'debit':debit, 'credit':credit,'end_balance':end_balance, 'copy_user':copy_user,'to_saldo':to_saldo},
            close: function(e){
                e.preventDefault();
            },
            success: function(response){                
                // alert(response);            
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        alert("Copy Saldo successfully");     
    });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#active", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'activebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Active");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>

<script type="text/javascript">
    $("table tbody tr").on("click", "#deactive", function(){                 
        var doc_number = $(this).closest('tr').find('td:eq(0)').attr('value');
        var active_user = '<?php echo $user ?>';

        $.ajax({
            type:'POST',
            url:'deactivebank.php',
            data: {'doc_number':doc_number, 'active_user':active_user},
            close: function(e){
                e.preventDefault();
            },
            success: function(data){                
                // console.log(data);
                window.location.reload();
                // alert("Deactive");                                              
            },
            error:  function (xhr, ajaxOptions, thrownError) {
               alert(xhr);
            }
        });
        });
</script>


<!-- <script type="text/javascript">     
    $('table tbody tr').on('click', 'td:eq(0)', function(){                
    $('#mymodal').modal('show');
    var no_ib = $(this).closest('tr').find('td:eq(0)').attr('value');
    var date = $(this).closest('tr').find('td:eq(1)').text();
    var reff = $(this).closest('tr').find('td:eq(2)').attr('value');
    var reff_doc = $(this).closest('tr').find('td:eq(3)').attr('value');
    var oth_doc = $(this).closest('tr').find('td:eq(4)').attr('value');
    var curr = "IDR";

    $.ajax({
    type : 'post',
    url : 'ajax_cashin.php',
    data : {'no_ib': no_ib},
    success : function(data){
    $('#details').html(data); //menampilkan data ke dalam modal
        }
    });         
        //make your ajax call populate items or what even you need
    $('#txt_bpb').html(no_ib);
    $('#txt_tglbpb').html('Date : ' + date + '');
    $('#txt_no_po').html('Refference : ' + reff + '');
    $('#txt_supp').html('Refference Document : ' + reff_doc + '');
    // $('#txt_top').html('Other Document : ' + oth_doc + '');
    // $('#txt_curr').html('Kas Account : ' + akun + '');        
    $('#txt_confirm').html('Currency : ' + curr + '');
    // $('#txt_tgl_po').html('Description : ' + desk + '');                    
});

</script> -->

<script type="text/javascript">
// Tombol Create/Upload/Reset markup-nya sudah dikomentari di halaman ini, jadi
// elemennya TIDAK ADA. Tanpa penjaga null, ketiga baris ini melempar
// "Cannot set property 'onclick' of null" di setiap muat halaman.
(function () {
  var bind = function (id, url) {
    var el = document.getElementById(id);
    if (el) { el.onclick = function () { location.href = url; }; }
  };
  bind('btncreate', 'create-list-journal.php');
  bind('btnupload', 'upload-list-journal.php');
  bind('reset',     'list-journal.php');
})();
</script>

<!-- <script type="text/javascript">     
    document.getElementById('btnupload').onclick = function (){ 
    // var txt_type = $(this).closest('tr').find('td:eq(4)').attr('value'); 
    // var txt_id = $(this).closest('tr').find('td:eq(0)').attr('value');           
    $('#mymodal2').modal('show');
    // $('#txt_type').val(txt_type);
    // $('#txt_id').val(txt_id);

};

</script> -->

<script>
function alert_cancel() {
  alert("Master Bank Deactive");
  location.reload();
}
function alert_approve() {
  alert("Master Bank Active");
  location.reload();
}
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
  
</body>

</html>
