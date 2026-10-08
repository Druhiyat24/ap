<?php include '../header.php' ?>
<?php require_once __DIR__ . '/ubf_jenis.php';
/* Satu berkas dipakai semua jenis barang; yang membedakan cuma parameter
   `jenis` di tautan menu. Lihat ubf_jenis.php. */
$jenis = ubf_jenis();
$K     = ubf_konf($jenis);
?>
<script>
  /* Jenis yang sedang dibuka. Ditaruh paling atas supaya seluruh blok
     <script> di bawahnya bisa memakainya saat menyusun panggilan AJAX. */
  var UBF_JENIS = '<?php echo htmlspecialchars($jenis, ENT_QUOTES); ?>';
</script>


<!-- Tiap berkas CSS ditaut sendiri dgn penanda versi dari filemtime.
     Kosakata .ftl- dipakai bersama daftar FTR CBD/DP, Petty Cash Out, dan
     Approve BPB Knitting - bentuknya sama dgn List Memorial Journal. -->
<link rel="stylesheet" href="../css/app-skin-form.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-skin-form.css'); ?>">
<link rel="stylesheet" href="../css/app-loading.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-loading.css'); ?>">
<link rel="stylesheet" href="../css/app-ftr-list.css?v=<?php echo @filemtime(__DIR__ . '/../css/app-ftr-list.css'); ?>">

<!-- MAIN -->
<div class="container-fluid mt-3 p-3">
  <!-- Kartu filter. Kepala kartunya (kotak ikon + judul + jejak menu)
       mengikuti List Memorial Journal. -->
  <div class="ftl-card">
    <div class="ftl-head">
      <span class="ftl-head-icon"><i class="fa fa-cubes" aria-hidden="true"></i></span>
      <div>
        <h1>Update BPB <?php echo htmlspecialchars($K['label']); ?></h1>
        <span class="ftl-crumb">Cost Accounting &rsaquo; Update BPB &rsaquo; <?php echo htmlspecialchars($K['label']); ?> &rsaquo; List</span>
      </div>
    </div><!-- /.ftl-head -->

<div class="ftl-panel">
  <form id="form-data" action="update-bpb-fabric.php
" method="post">
    <div class="form-row">

    <!-- Start Date -->
    <div class="col-12 col-sm-6 col-xl-2 mb-2">
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
           echo date("d-m-Y");
       } ?>" placeholder="Start Date" autocomplete="off">
   </div>

   <!-- End Date -->
   <div class="col-12 col-sm-6 col-xl-2 mb-2">
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
       echo date("d-m-Y");
   } ?>"  placeholder="End Date" autocomplete="off">
</div>


<!-- Tombol -->
<div class="col-12 col-sm-12 col-xl-8 mb-2 ftl-actions">
  <button type="button" class="app-btn app-btn-primary app-btn-sm" onclick="dataTableReload()">
        <i class="fa fa-search"></i> Search
      </button>
    <button type="button" id="btnCreateNew" class="app-btn app-btn-success app-btn-sm" onclick="location.href='form_update_bpb_fabric.php?jenis=<?php echo urlencode($jenis); ?>'">
        <i class="fa fa-plus-circle" aria-hidden="true"></i> Create New
    </button>
    <a id="btnExportExcel" target="_blank">
    <button type="button" class="app-btn app-btn-excel app-btn-sm">
        <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </button>
</a>


</div>

</div>
</form>
</div><!-- /.ftl-panel -->
</div><!-- /.ftl-card: kartu filter -->

<div class="ftl-card mt-3">
    <div class="ftl-body">
      <!-- .app-loading-wrap: area yang ditutup overlay saat data ditarik. -->
      <div class="app-loading-wrap" id="ubfLoad">
        <div class="app-loading">
          <div class="app-loading-box">
            <div class="app-spinner"></div>
            <div class="app-loading-text">Loading data...</div>
          </div>
        </div>

      <!-- TANPA pembungkus yang bisa digulir ke samping: itu membuat modul
           Responsive menyangka ruangnya selalu cukup, jadi tanda "+" tidak
           pernah muncul. -->
      <div class="ftl-tblwrap">
          <table id="table-data" class="table ftl-tbl" role="grid" cellspacing="0" width="100%">
          <thead>
            <tr class="thead-dark">
                <!-- Kolom tanda "+": pembuka baris rincian di layar sempit. -->
                <th style="text-align: center;vertical-align: middle;"></th>
                <th style="text-align: center;vertical-align: middle;">No. Trans</th>
                <th style="text-align: center;vertical-align: middle;">Trans Date</th>
                <th style="text-align: center;vertical-align: middle;">Status</th>
                <th style="text-align: center;vertical-align: middle;">Description</th>
                <th style="text-align: center;vertical-align: middle;">Created By</th>
                <th style="text-align: center;vertical-align: middle;">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
      </div><!-- /.ftl-tblwrap -->
      </div><!-- /.app-loading-wrap -->
    </div><!-- /.ftl-body -->
</div><!-- /.ftl-card: kartu tabel -->
</div><!-- /.container-fluid -->



<!-- Modal Detail -->
<div class="modal fade ftl-modal is-titlefirst is-wide" id="mymodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: linear-gradient(90deg, #191970, #1e90ff);">
        <h5 class="modal-title" id="txt_bpb"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="row">
          <div id="txt_tglbpb" class="col-md-3 mb-2"></div>
          <div id="txt_supp" class="col-md-3 mb-2"></div>
          <div id="txt_status" class="col-md-3 mb-2"></div>
          <div id="txt_created_by" class="col-md-3 mb-2"></div>
          <div id="txt_deskripsi" class="col-12 mb-2"></div>
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

<script language="JavaScript" src="../css/4.1.1/xlsx.full.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/html2pdf.bundle.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/exceljs.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/FileSaver.min.js"></script>


<script language="JavaScript" src="../css/4.1.1/select2.min.js"></script>
<script language="JavaScript" src="../css/4.1.1/sweetalert2@11.js"></script>
<script language="JavaScript" src="../css/4.1.1/dataTables.fixedColumns.min"></script>

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

</script>

<script type="text/javascript">
      $(document).ready(function () {

    $.ajax({
        url: 'get_min_date.php',
        type: 'GET',
        dataType: 'json',
        success: function(res){
          console.log(res.tgl_awal);

            $('.tanggal').datepicker({
                format: "dd-mm-yyyy",
                startDate : res.tgl_awal, // dari database
                autoclose:true
            });

        }
    });

});
</script>


<script type="text/javascript">
  function toYmd(dmy) {
    if (!dmy) return '';
    let p = dmy.split('-'); // [dd, mm, yyyy]
    return `${p[2]}-${p[1]}-${p[0]}`;
  }

  let datatable = $("#table-data").DataTable({
    ordering: false,
    processing: true,
    serverSide: false,
    pageLength: 10,
    searching: true,
    info: true,
    autoWidth: false,

    /* Kotak Search, nomor halaman & jumlah baris diingat seumur tab. */
    stateSave: true,
    stateDuration: -1,

    /* RESPONSIVE: kolom yang tidak muat DIKOLAPS jadi baris rincian yang
       dibuka lewat tanda "+", bukan dipaksa digulir ke samping. */
    responsive: {
        details: { type: 'column', target: 0 }
    },

      ajax: {
        url: 'ajx_update-bpb-fabric.php?jenis=' + encodeURIComponent(UBF_JENIS),
        type: 'POST',
        data: function (d) {
          d.start_date      = $('#start_date').val();
          d.end_date        = $('#end_date').val();
        }
      },

      /* responsivePriority: makin KECIL angkanya, makin lama kolom itu
         dipertahankan saat layar menyempit. */
      columns: [
      { data: null, defaultContent: '', orderable: false, className: 'dtr-control', responsivePriority: 1 },
      { data: 'no_pengajuan',  responsivePriority: 1 },
      { data: 'tgl_pengajuan', responsivePriority: 5 },
      { data: 'status',        responsivePriority: 3 },
      { data: 'deskripsi',     responsivePriority: 6 },
      { data: 'created_by',    responsivePriority: 7 },
      { data: 'action', orderable: false, searchable: false, responsivePriority: 2 },
      ],

      /* Kelas "all" = kolom ini TIDAK PERNAH dikolaps, seberapa pun sempit
         layarnya. Nomor target bergeser +1 karena kolom "+" disisipkan. */
      columnDefs: [
          { targets: [1],    className: 'text-left ftl-doc all' },
          { targets: [3],    className: 'text-center all' },
          { targets: [6],    className: 'text-center ftl-act-cell all', width: '240px' },
          { targets: [2],    className: 'text-center' },
          { targets: [4, 5], className: 'text-left' }
            ],

      language: {
          emptyTable: 'No request found for this filter.',
          zeroRecords: 'No request matches your search.'
      }

});

/* Overlay mengikuti status processing DataTables. Kotak "Processing"
   bawaannya disembunyikan app-loading.css, jadi tidak muncul dua-duanya. */
datatable.on('processing.dt', function (e, settings, processing) {
    $('#ubfLoad').toggleClass('is-loading', processing);
});

$("[data-toggle=tooltip]").tooltip();

function dataTableReload() {
  datatable.ajax.reload(()=>{
    datatable.columns.adjust();
  });
}

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (ch) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch];
    });
}

function formatMoney(amount, decimalCount = 2) {
    const val = parseFloat(amount);
    if (isNaN(val)) return '0.00';
    return val.toLocaleString('en-US', {
        minimumFractionDigits: decimalCount,
        maximumFractionDigits: decimalCount
    });
}

// View detail of an edit request
$('#table-data').on('click', '.btn-view-pengajuan', function () {
    const noPengajuan = this.dataset.no;

    $('#txt_bpb').text('Edit Request - ' + noPengajuan);
    $('#txt_tglbpb, #txt_supp, #txt_status, #txt_created_by, #txt_deskripsi').html('');
    $('#details').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#mymodal').modal('show');

    $.ajax({
        url: 'get_detail_update_bpb_fabric.php?jenis=' + encodeURIComponent(UBF_JENIS),
        type: 'GET',
        data: { no_pengajuan: noPengajuan },
        dataType: 'json',
        success: function (res) {
            const h = res.header;
            if (h) {
                $('#txt_tglbpb').html('<b>Transaction Date:</b> ' + escapeHtml(h.tgl_pengajuan));
                /* Kotak ini sebelumnya tidak pernah diisi padahal datanya ada,
                   jadi selalu tampil sbg kartu putih kosong. */
                $('#txt_supp').html('<b>Supplier:</b> ' + escapeHtml(h.nama_supp || '-'));
                $('#txt_status').html('<b>Status:</b> ' + escapeHtml(h.status));
                const createdByText = h.created_by ? (h.created_by + ' (' + h.created_at + ')') : '-';
                $('#txt_created_by').html('<b>Created By:</b> ' + escapeHtml(createdByText));
                $('#txt_deskripsi').html('<b>Description:</b> ' + escapeHtml(h.deskripsi || '-'));
            }

            if (!res.items.length) {
                $('#details').html('<div class="text-center p-3 text-muted">No items found</div>');
                return;
            }

            /* Tabel rincian dijadikan DataTable: punya pencarian, jumlah baris,
               dan penomoran halaman. Diberi id supaya bisa diinisialisasi -
               sebelumnya tabel polos tanpa id. */
            let html = '<table id="table-detail-modal" class="table ftl-tbl" style="width:100%">';
            html += '<thead><tr class="thead-dark">'
                + '<th class="nw">No BPB</th><th class="nw">BPB Date</th><th class="text-left">Supplier</th>'
                + '<th class="nw">No WS</th><th class="text-left">Item</th>'
                + '<th class="text-right">Qty</th><th class="nw">Unit</th><th class="nw">Curr</th>'
                + '<th class="text-right">Price (Old)</th><th class="text-right">Price (New)</th>'
                + '<th class="text-right">PPN % (Old)</th><th class="text-right">PPN % (New)</th>'
                + '</tr></thead><tbody>';

            res.items.forEach(function (it) {
                /* Tiap sel diberi kelas perataannya sendiri. Sebelumnya semua
                   polos, jadi angka rata kiri dan kolom pendek saling menempel.
                   .ftl-doc / .ftl-amt kosakata yang sama dgn tabel daftar. */
                html += '<tr>'
                    + '<td class="ftl-doc">' + escapeHtml(it.no_bpb) + '</td>'
                    + '<td class="nw">' + escapeHtml(it.tgl_bpb) + '</td>'
                    + '<td class="text-left">' + escapeHtml(it.nama_supp || '-') + '</td>'
                    + '<td class="nw">' + escapeHtml(it.no_ws || '-') + '</td>'
                    + '<td class="text-left ub-item">' + escapeHtml(it.desc_item || it.id_item) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.qty) + '</td>'
                    + '<td class="nw">' + escapeHtml(it.unit || '-') + '</td>'
                    + '<td class="nw">' + escapeHtml(it.curr || '-') + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.price_old, 4) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.price_new, 4) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.ppn_old) + '</td>'
                    + '<td class="text-right ftl-amt">' + formatMoney(it.ppn_new) + '</td>'
                    + '</tr>';
            });

            html += '</tbody></table>';
            /* Instance lama dibuang dulu: modal ini dibuka berulang kali untuk
               dokumen berbeda, dan DataTables menolak diinisialisasi dua kali
               pada id yang sama. */
            if ($.fn.DataTable.isDataTable('#table-detail-modal')) {
                $('#table-detail-modal').DataTable().destroy();
            }
            $('#details').html(html);
            $('#table-detail-modal').DataTable({
                ordering: false,
                /* scrollX TIDAK dipakai: modalnya kini selebar 96vw (maks 1360px)
                   mengikuti modal Memorial Journal, jadi tabelnya muat. Kalau pun
                   kurang, .modal-body yang menggulir. */
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
                language: { emptyTable: 'No item in this request.' }
            });
        },
        error: function () {
            $('#details').html('<div class="text-center p-3 text-danger">Failed to load detail</div>');
        }
    });
});

// Cancel an edit request
$('#table-data').on('click', '.btn-cancel-pengajuan', function () {
    const noPengajuan = this.dataset.no;

    /* Label tombolnya sengaja tidak memakai kata "Cancel" sendirian: di kotak
       ini kata itu bisa berarti dua hal - membatalkan dokumen, atau menutup
       kotaknya. Jadi dieja penuh. */
    Swal.fire({
        icon: 'warning',
        title: 'Cancel this request?',
        html: '<div style="text-align:left;font-size:13px;line-height:1.9">'
            + '<div><b>Transaction No</b> : ' + noPengajuan + '</div>'
            + '<div style="margin-top:6px;color:#64748b">Its status will be changed to '
            + '<b style="color:#b3312c">Cancel</b>. This cannot be undone from this page.</div>'
            + '</div>',
        showCancelButton: true,
        confirmButtonColor: '#b3312c',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, cancel this request',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: 'cancel_update_bpb_fabric.php?jenis=' + encodeURIComponent(UBF_JENIS),
            type: 'POST',
            data: { no_pengajuan: noPengajuan },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    /* Tanpa timer & dgn tombol: nomornya perlu sempat dibaca. */
                    Swal.fire({
                        icon: 'success',
                        title: 'Request cancelled',
                        html: '<div style="font-size:13px;color:#475569">Transaction No</div>'
                            + '<div style="font-size:18px;font-weight:700;color:#1e3a8a;margin:4px 0 10px">'
                            + noPengajuan + '</div>'
                            + '<div style="font-size:13px;color:#475569">Its status is now '
                            + '<b style="color:#b3312c">Cancel</b>.</div>',
                        confirmButtonColor: '#1d4ed8',
                        confirmButtonText: 'OK'
                    });
                    datatable.ajax.reload(null, false);
                } else {
                    /* Pesan dari server dipakai apa adanya - di sinilah muncul
                       "already approved" / "already cancelled", yaitu keadaan yang
                       tidak terlihat kalau daftarnya sudah usang di layar. */
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot be cancelled',
                        text: res.message || 'An error occurred'
                    });
                    datatable.ajax.reload(null, false);
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to cancel the request' });
            }
        });
    });
});

function RepostJurnal() {

    let selected = [];

    $('.row-check:checked').each(function () {
        selected.push($(this).val());
    });

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Select at least 1 record!'
        });
        return;
    }

    console.log(selected);

    Swal.fire({
        title: 'Repost journal?',
        text: "The old journal will be moved to the cancel table!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Repost!',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Processing...',
                html: 'Reposting journal...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'proses_repost_bpb.php',
                type: 'POST',
                data: { bpb_list: selected },
                success: function (res) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res
                    });

                    datatable.ajax.reload();
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'A server error occurred'
                    });
                }
            });

        }

    });
}



document.getElementById('btnExportExcel').addEventListener('click', function(e) {
  let start_date = toYmd(document.getElementById('start_date').value);
  let end_date = toYmd(document.getElementById('end_date').value);

  this.href = `ekspor_update-bpb-fabric.php?jenis=${encodeURIComponent(UBF_JENIS)}&start_date=${start_date}&end_date=${end_date}`;
});

</script>

<script type="text/javascript">
    document.getElementById('btncreate').onclick = function () {
        location.href = "update-bpb-fabric.php";
    };
</script>

<script type="text/javascript">
    document.getElementById('reset').onclick = function () {
        location.href = "update-bpb-fabric.php";
    };
</script>
<script type="text/javascript">
  $("#select_all").click(function() {
    var c = this.checked;
    $(':checkbox').prop('checked', c);
  });  
</script>

<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->

</body>

</html>
